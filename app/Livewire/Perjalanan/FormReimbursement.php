<?php

namespace App\Livewire\Perjalanan;

use App\Models\PerjalananDinas;
use App\Models\Reimbursement;
use App\Models\Coa;
use Livewire\Component;
use Livewire\WithFileUploads;

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

        // Jika ada data estimasi, tarik datanya
        if ($perjalanan->rincianBiaya->count() > 0) {
            foreach ($perjalanan->rincianBiaya as $item) {
                $this->rincian[] = [
                    'coa_id' => $item->coa_id,
                    'keterangan' => $item->keterangan,
                    'nominal' => $item->nominal,
                    'file_struk' => null, 
                ];
            }
        } else {
            // Jika kosong (karena data dummy), buat 1 baris kosong
            $this->tambahBaris();
        }
    }

    public function updated($property)
    {
        if (str_contains($property, 'rincian')) {
            $this->hitungTotal();
        }
    }

    public function tambahBaris()
    {
        $this->rincian[] = ['coa_id' => '', 'keterangan' => '', 'nominal' => 0, 'file_struk' => null];
    }

    public function hapusBaris($index)
    {
        unset($this->rincian[$index]);
        $this->rincian = array_values($this->rincian);
        $this->hitungTotal();
    }

    private function hitungTotal()
    {
        // Gunakan (float) agar string kosong dianggap 0
        $this->total_realisasi = collect($this->rincian)->sum(function($item) {
            return (float) ($item['nominal'] ?? 0);
        });
    }

    public function simpan()
    {
        $this->validate([
            'rincian.*.coa_id' => 'required|exists:coas,id',
            'rincian.*.nominal' => 'required|numeric|min:1',
            'rincian.*.file_struk' => 'required|image|max:2048', // WAJIB Upload Struk, max 2MB
        ]);

        $perjalanan = PerjalananDinas::findOrFail($this->perjalanan_id);
        $selisih = $this->total_realisasi - $perjalanan->total_estimasi;

        // Generate Nomor Reimb: 001/REIMB/2026
        $count = Reimbursement::whereYear('created_at', date('Y'))->count() + 1;
        $nomor = str_pad($count, 3, '0', STR_PAD_LEFT) . '/REIMB/' . date('Y');

        $reimb = Reimbursement::create([
            'perjalanan_id' => $this->perjalanan_id,
            'nomor_reimb' => $nomor,
            'total_realisasi' => $this->total_realisasi,
            'selisih' => $selisih,
            'status' => 'SUBMITTED',
        ]);

        foreach ($this->rincian as $r) {
            // Simpan file struk
            $path = $r['file_struk']->store('struk', 'public');
            
            $reimb->items()->create([
                'coa_id' => $r['coa_id'],
                'keterangan' => $r['keterangan'],
                'nominal' => $r['nominal'],
                'file_struk' => $path,
            ]);
        }

        // Ubah status perjalanan menjadi COMPLETED
        $perjalanan->update(['status' => 'COMPLETED']);

        session()->flash('message', 'Laporan Reimbursement berhasil dikirim!');
        return redirect()->route('perjalanan.riwayat');
    }

    public function render()
    {
        return view('livewire.perjalanan.form-reimbursement', [
            'coas' => Coa::where('aktif', true)->orderBy('kode')->get(),
            'perjalanan' => PerjalananDinas::find($this->perjalanan_id)
        ])->layout('layouts.app');
    }
}