<?php

namespace App\Livewire\Perjalanan;

use App\Models\PerjalananDinas;
use App\Models\Reimbursement;
use App\Models\Coa;
use App\Services\GeminiService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class FormReimbursement extends Component
{
    use WithFileUploads;

    public $perjalanan_id;
    public $rincian = [];
    public $total_realisasi = 0;

    public function mount($id)
    {
        $perjalanan = PerjalananDinas::with('rincianBiaya')->findOrFail($id);
        
        if ($perjalanan->pegawai_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak mengakses ini.');
        }

        $this->perjalanan_id = $id;

        if ($perjalanan->rincianBiaya->count() > 0) {
            foreach ($perjalanan->rincianBiaya as $item) {
                $this->rincian[] = [
                    'coa_id' => $item->coa_id,
                    'keterangan' => $item->keterangan,
                    'nominal' => $item->nominal,
                    'file_struk' => null, 
                    'file_path' => null,
                ];
            }
        } else {
            $this->tambahBaris();
        }
    }

    public function updated($property)
    {
        if (str_contains($property, 'file_struk')) {
            $parts = explode('.', $property);
            $index = $parts[1];
            
            if (isset($this->rincian[$index]['file_struk']) && is_object($this->rincian[$index]['file_struk'])) {
                if (!empty($this->rincian[$index]['file_path'])) {
                    Storage::disk('public')->delete($this->rincian[$index]['file_path']);
                }
                $path = $this->rincian[$index]['file_struk']->store('struk', 'public');
                $this->rincian[$index]['file_path'] = $path;
            }
        }

        if (str_contains($property, 'rincian')) {
            $this->hitungTotal();
        }
    }

    public function tambahBaris()
    {
        $this->rincian[] = ['coa_id' => '', 'keterangan' => '', 'nominal' => 0, 'file_struk' => null, 'file_path' => null];
    }

    public function hapusBaris($index)
    {
        if (!empty($this->rincian[$index]['file_path'])) {
            Storage::disk('public')->delete($this->rincian[$index]['file_path']);
        }
        unset($this->rincian[$index]);
        $this->rincian = array_values($this->rincian);
        $this->hitungTotal();
    }

    private function hitungTotal()
    {
        $this->total_realisasi = collect($this->rincian)->sum(function($item) {
            return (float) ($item['nominal'] ?? 0);
        });
    }

    public function scanOcr($index)
    {
        if (empty($this->rincian[$index]['file_path'])) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal',
                'text' => 'Silakan upload file struk terlebih dahulu.'
            ]);
            return;
        }

        try {
            $gemini = new GeminiService();
            $fullPath = Storage::disk('public')->path($this->rincian[$index]['file_path']);
            $result = $gemini->scanStruk($fullPath, 'image/jpeg');

            $this->rincian[$index]['nominal'] = $result['nominal'];
            $this->rincian[$index]['keterangan'] = $result['merchant'];
            $this->hitungTotal();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => 'Nominal berhasil dideteksi: Rp ' . number_format($result['nominal'], 0, ',', '.')
            ]);

        } catch (Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal Membaca',
                'text' => $e->getMessage()
            ]);
        }
    }

    public function simpan()
    {
        $this->validate([
            'rincian.*.coa_id' => 'required|exists:coas,id',
            'rincian.*.nominal' => 'required|numeric|min:1',
            'rincian.*.file_path' => 'required', 
        ], [
            'rincian.*.file_path.required' => 'File struk wajib di-upload.',
        ]);

        $perjalanan = PerjalananDinas::findOrFail($this->perjalanan_id);
        
        if (Reimbursement::where('perjalanan_id', $this->perjalanan_id)->exists()) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal',
                'text' => 'Reimbursement untuk perjalanan ini sudah pernah dibuat.'
            ]);
            return;
        }

        $selisih = $this->total_realisasi - $perjalanan->total_estimasi;

        try {
            DB::beginTransaction();

            // Generate nomor dengan lock biar aman dari double submit
            $year = date('Y');
            $lastReimb = Reimbursement::whereYear('created_at', $year)
                            ->lockForUpdate()
                            ->orderBy('id', 'desc')
                            ->first();
            
            $nextNumber = $lastReimb ? ((int) substr($lastReimb->nomor_reimb, 0, 3)) + 1 : 1;
            $nomor = str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '/REIMB/' . $year;

            $reimb = Reimbursement::create([
                'perjalanan_id' => $this->perjalanan_id,
                'nomor_reimb' => $nomor,
                'total_realisasi' => $this->total_realisasi,
                'selisih' => $selisih,
                'status' => 'SUBMITTED',
            ]);

            foreach ($this->rincian as $r) {
                $reimb->items()->create([
                    'coa_id' => $r['coa_id'],
                    'keterangan' => $r['keterangan'],
                    'nominal' => $r['nominal'],
                    'file_struk' => $r['file_path'], 
                ]);
            }

            $perjalanan->update(['status' => 'COMPLETED']);

            DB::commit();

            // Kasih pesan flash buat SweetAlert di halaman riwayat
            session()->flash('message', 'Laporan Reimbursement berhasil dikirim!');
            return redirect()->route('perjalanan.riwayat');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal:error', [
                'title' => 'Gagal Menyimpan',
                'text' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.perjalanan.form-reimbursement', [
            'coas' => Coa::where('aktif', true)->orderBy('kode')->get(),
            'perjalanan' => PerjalananDinas::find($this->perjalanan_id)
        ])->layout('layouts.app');
    }
}