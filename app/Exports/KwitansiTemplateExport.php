<?php

namespace App\Exports;

use App\Models\PerjalananDinas;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Mengisi storage/app/templates/kwitansi_template.xlsx (placeholder {{nama}}).
 *
 * Kwitansi hanya punya 4 kategori biaya tetap (Ongkos Ticket, Uang Harian, Hotel, Lain-Lain);
 * semua baris rincian perjalanan dijumlahkan ke salah satu dari keempat kategori itu.
 *
 * Pemakaian di controller (WAJIB di-return):
 *   return (new KwitansiTemplateExport($perjalanan))->download('KWITANSI-001.xlsx');
 */
class KwitansiTemplateExport
{
    protected PerjalananDinas $perjalanan;
    protected string $templatePath;

    public function __construct(PerjalananDinas $perjalanan)
    {
        $this->perjalanan   = $perjalanan;
        $this->templatePath = storage_path('app/templates/kwitansi_template.xlsx');
    }

    public function download(string $filename = 'KWITANSI.xlsx'): StreamedResponse
    {
        $spreadsheet = $this->generate();

        return response()->streamDownload(function () use ($spreadsheet) {
            IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function saveTo(string $path): void
    {
        IOFactory::createWriter($this->generate(), 'Xlsx')->save($path);
    }

    public function generate(): Spreadsheet
    {
        $spreadsheet = IOFactory::load($this->templatePath);
        $sheet       = $spreadsheet->getSheetByName('KWITANSI') ?? $spreadsheet->getActiveSheet();

        [$text, $numeric] = $this->buildData();

        foreach ($sheet->getRowIterator() as $row) {
            $cells = $row->getCellIterator();
            $cells->setIterateOnlyExistingCells(true);

            foreach ($cells as $cell) {
                $val = $cell->getValue();
                if ($val instanceof RichText) {          // teks inline dibaca sebagai RichText
                    $val = $val->getPlainText();
                }
                if (! is_string($val) || ! str_contains($val, '{{')) {
                    continue;
                }

                // Sel berisi HANYA satu placeholder angka -> tulis sebagai angka (format ribuan template berlaku)
                if (preg_match('/^\{\{(\w+)\}\}$/', $val, $m) && array_key_exists($m[1], $numeric)) {
                    $cell->setValueExplicit($numeric[$m[1]], DataType::TYPE_NUMERIC);
                    continue;
                }

                // Teks WAJIB string eksplisit (NIP berupa angka tidak boleh jadi 1.99E+17)
                $cell->setValueExplicit(
                    preg_replace_callback('/\{\{(\w+)\}\}/', fn ($mm) => $text[$mm[1]] ?? '', $val),
                    DataType::TYPE_STRING
                );
            }
        }

        // Uang muka 0 -> kosongkan sel (seperti template asli)
        if (($numeric['uang_muka'] ?? 0) == 0) {
            $sheet->setCellValue('G36', null);
        }

        return $spreadsheet;
    }

    /**
     * @return array{0: array<string,string>, 1: array<string,float|int>}
     */
    private function buildData(): array
    {
        $p       = $this->perjalanan;
        $pegawai = $p->pegawai;
        $rincian = $p->rincianBiaya;

        // --- Kelompokkan rincian ke 4 kategori kwitansi ---
        $kategori = ['tiket' => 0.0, 'harian' => 0.0, 'hotel' => 0.0, 'lain' => 0.0];

        foreach ($rincian as $r) {
            $nominal = (float) ($r->nominal ?? ((float) ($r->qty ?? 1) * (float) ($r->harga_satuan ?? $r->harga ?? 0)));
            $kategori[$this->kategoriBiaya($r)] += $nominal;
        }

        $total    = array_sum($kategori);
        $uangMuka = (float) ($p->uang_muka ?? 0);
        $diterima = $total - $uangMuka;

        $dokumen = Carbon::parse($p->tanggal_dokumen ?? now())->locale('id');
        $spd     = Carbon::parse($p->tanggal_berangkat)->locale('id');

        $pangkat = $pegawai->pangkat ?? null;
        $pangkatJabatan = trim(
            ($pangkat ? $pangkat->nama . ' (' . $pangkat->golongan_ruang . ')' : '-')
            . ' / ' . ($pegawai->jabatan->nama ?? '-')
        );

        $text = [
            'no_kwitansi'         => (string) ($p->no_kwitansi ?? $p->nomor_pengajuan ?? ''),
            'akun'                => (string) ($p->akun ?? ($rincian->first()->coa->kode ?? '')),
            'terbilang'           => $this->terbilangRupiah($diterima),
            'tanggal_spd'         => $spd->translatedFormat('j F Y'),
            'no_spd'              => (string) ($p->no_spd ?? ''),
            'tempat_berangkat'    => (string) ($p->tempat_berangkat ?? 'Makassar'),
            'kota_tujuan'         => (string) ($p->kotaTujuan->nama ?? '-'),
            'bulan_tahun_dokumen' => $dokumen->translatedFormat('F Y'),

            'nama_pegawai'        => (string) ($pegawai->name ?? ''),
            'pangkat_jabatan'     => $pangkatJabatan,
            'tempat_kedudukan'    => (string) ($pegawai->tempat_kedudukan ?? $p->tempat_berangkat ?? 'Makassar'),

            'nama_ppk'            => (string) ($p->nama_ppk ?? ''),
            'nip_ppk'             => (string) ($p->nip_ppk ?? ''),
            'nama_bendahara'      => (string) ($p->nama_bendahara ?? ''),
            'nip_bendahara'       => (string) ($p->nip_bendahara ?? ''),
        ];

        $numeric = [
            'biaya_tiket'  => $kategori['tiket'],
            'biaya_harian' => $kategori['harian'],
            'biaya_hotel'  => $kategori['hotel'],
            'biaya_lain'   => $kategori['lain'],
            'total'        => $total,
            'uang_muka'    => $uangMuka,
            'diterima'     => $diterima,
            'hari_dokumen' => (int) $dokumen->day,
        ];

        return [$text, $numeric];
    }

    /** Tentukan satu baris rincian masuk kategori kwitansi yang mana. */
    private function kategoriBiaya($item): string
    {
        $s = strtolower(($item->coa->nama ?? '') . ' ' . ($item->keterangan ?? ''));

        foreach (['penginapan', 'hotel', 'akomodasi'] as $kata) {
            if (str_contains($s, $kata)) return 'hotel';
        }
        foreach (['uang harian', 'uang saku', 'harian'] as $kata) {
            if (str_contains($s, $kata)) return 'harian';
        }
        foreach (['tiket', 'ticket', 'pesawat', 'kereta'] as $kata) {
            if (str_contains($s, $kata)) return 'tiket';
        }

        return 'lain';
    }

    private function terbilangRupiah($angka): string
    {
        $angka = (int) round($angka);
        $hasil = $angka === 0 ? 'nol' : trim($this->terbilang($angka));

        return ucfirst(preg_replace('/\s+/', ' ', $hasil)) . ' rupiah';
    }

    private function terbilang(int $n): string
    {
        $huruf = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($n < 12)            return $huruf[$n];
        if ($n < 20)            return $this->terbilang($n - 10) . ' belas';
        if ($n < 100)           return $this->terbilang(intdiv($n, 10)) . ' puluh ' . $this->terbilang($n % 10);
        if ($n < 200)           return 'seratus ' . $this->terbilang($n - 100);
        if ($n < 1000)          return $this->terbilang(intdiv($n, 100)) . ' ratus ' . $this->terbilang($n % 100);
        if ($n < 2000)          return 'seribu ' . $this->terbilang($n - 1000);
        if ($n < 1000000)       return $this->terbilang(intdiv($n, 1000)) . ' ribu ' . $this->terbilang($n % 1000);
        if ($n < 1000000000)    return $this->terbilang(intdiv($n, 1000000)) . ' juta ' . $this->terbilang($n % 1000000);
        if ($n < 1000000000000) return $this->terbilang(intdiv($n, 1000000000)) . ' miliar ' . $this->terbilang($n % 1000000000);

        return (string) $n;
    }
}