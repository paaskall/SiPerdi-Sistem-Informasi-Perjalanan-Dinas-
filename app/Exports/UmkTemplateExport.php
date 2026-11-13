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
 * Mengisi storage/app/templates/umk_template.xlsx (placeholder {{nama}}) dengan data perjalanan dinas.
 *
 * Pemakaian di controller (WAJIB di-return):
 *   return (new UmkTemplateExport($perjalanan))->download('UMK-001.xlsx');
 */
class UmkTemplateExport
{
    /** Baris rincian di template: 13 s/d 22 (10 baris). */
    private const FIRST_ROW = 13;
    private const MAX_ROWS  = 10;

    protected PerjalananDinas $perjalanan;
    protected string $templatePath;

    public function __construct(PerjalananDinas $perjalanan)
    {
        $this->perjalanan   = $perjalanan;
        $this->templatePath = storage_path('app/templates/umk_template.xlsx');
    }

    /** Download sebagai file Excel. */
    public function download(string $filename = 'UMK.xlsx'): StreamedResponse
    {
        $spreadsheet = $this->generate();

        return response()->streamDownload(function () use ($spreadsheet) {
            IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /** Simpan hasil ke file .xlsx (dipakai untuk konversi ke PDF). */
    public function saveTo(string $path): void
    {
        IOFactory::createWriter($this->generate(), 'Xlsx')->save($path);
    }

    /** Isi template dengan data perjalanan, kembalikan Spreadsheet-nya. */
    public function generate(): Spreadsheet
    {
        $spreadsheet = IOFactory::load($this->templatePath);
        $sheet       = $spreadsheet->getSheetByName('UMK') ?? $spreadsheet->getActiveSheet();

        [$text, $numeric, $usedRows] = $this->buildData();

        // 1) Ganti semua placeholder
        foreach ($sheet->getRowIterator() as $row) {
            $cells = $row->getCellIterator();
            $cells->setIterateOnlyExistingCells(true);

            foreach ($cells as $cell) {
                $val = $cell->getValue();

                // Teks inline (t="inlineStr") dibaca PhpSpreadsheet sebagai RichText, bukan string.
                if ($val instanceof RichText) {
                    $val = $val->getPlainText();
                }

                if (! is_string($val) || ! str_contains($val, '{{')) {
                    continue;
                }

                // Sel yang isinya HANYA satu placeholder angka -> tulis sebagai angka
                // supaya format Rp/ribuan dari template tetap berlaku.
                if (preg_match('/^\{\{(\w+)\}\}$/', $val, $m) && array_key_exists($m[1], $numeric)) {
                    $cell->setValueExplicit($numeric[$m[1]], DataType::TYPE_NUMERIC);
                    continue;
                }

                $cell->setValue(preg_replace_callback(
                    '/\{\{(\w+)\}\}/',
                    fn ($mm) => $text[$mm[1]] ?? '',
                    $val
                ));
            }
        }

        // 2) Kosongkan baris rincian yang tidak terpakai (baris TIDAK dihapus,
        //    supaya merged cell & posisi total/tanda tangan tidak bergeser).
        for ($i = $usedRows + 1; $i <= self::MAX_ROWS; $i++) {
            $r = self::FIRST_ROW + $i - 1;
            foreach (['C', 'D', 'H', 'I', 'J', 'K', 'L', 'M'] as $col) {
                $sheet->setCellValue($col . $r, null);
            }
        }

        return $spreadsheet;
    }

    /**
     * @return array{0: array<string,string>, 1: array<string,float|int>, 2: int}
     */
    private function buildData(): array
    {
        $p       = $this->perjalanan;
        $pegawai = $p->pegawai;
        $rincian = $p->rincianBiaya->values();

        if ($rincian->count() > self::MAX_ROWS) {
            throw new \RuntimeException(
                'Template UMK hanya memuat ' . self::MAX_ROWS . ' baris rincian, sedangkan data berisi ' . $rincian->count() . ' baris.'
            );
        }

        $text    = [];
        $numeric = [];
        $total   = 0;

        // --- Rincian biaya ---
        foreach ($rincian as $idx => $r) {
            $n      = $idx + 1;
            $qty    = (float) ($r->qty ?? 1);
            $harga  = (float) ($r->harga ?? $r->nominal ?? 0);
            $jumlah = $qty * $harga;
            $total += $jumlah;

            $text["ket_$n"]    = (string) ($r->keterangan ?: ($r->coa->nama ?? '-'));
            $text["satuan_$n"] = (string) ($r->satuan ?? 'kali');
            $numeric["qty_$n"]    = $qty;
            $numeric["harga_$n"]  = $harga;
            $numeric["jumlah_$n"] = $jumlah;
        }

        // --- Header & data perjalanan ---
        $berangkat = Carbon::parse($p->tanggal_berangkat)->locale('id');
        $pulang    = Carbon::parse($p->tanggal_pulang)->locale('id');
        $dokumen   = Carbon::parse($p->tanggal_dokumen ?? now())->locale('id');

        $text += [
            'no_pembukuan'      => (string) ($p->no_pembukuan ?? ''),
            'akun'              => (string) ($p->akun ?? ($rincian->first()->coa->kode ?? '')),
            'no_spd'            => (string) ($p->no_spd ?? ''),
            'tempat_berangkat'  => (string) ($p->tempat_berangkat ?? 'Makassar'),
            'kota_tujuan'       => (string) ($p->kotaTujuan->nama ?? '-'),
            'alat_angkutan'     => (string) ($p->alat_angkutan ?? '-'),
            'tingkat_biaya'     => (string) ($p->tingkat_biaya ?? '-'),
            'maksud_perjalanan' => (string) $p->maksud_perjalanan,
            'jumlah_hari'       => (string) $p->jumlah_hari,
            'tanggal_berangkat' => $berangkat->translatedFormat('j F Y'),
            'tanggal_pulang'    => $pulang->translatedFormat('j F Y'),
            'tanggal_dokumen'   => $dokumen->translatedFormat('j F Y'),
            'terbilang'         => $this->terbilangRupiah($total),

            // Pegawai (penerima uang)
            'nama_pegawai'      => (string) ($pegawai->name ?? ''),
            'nip'               => (string) ($pegawai->nip ?? ''),
            'jabatan'           => (string) ($pegawai->jabatan->nama ?? '-'),
            'pangkat_golongan'  => trim(($pegawai->pangkat->nama ?? '-') . ', ' . ($pegawai->golongan->nama ?? '-')),
            'nama_penerima'     => (string) ($pegawai->name ?? ''),
            'nip_penerima'      => (string) ($pegawai->nip ?? ''),

            // Tanda tangan
            'nama_bendahara'    => (string) ($p->nama_bendahara ?? ''),
            'nip_bendahara'     => (string) ($p->nip_bendahara ?? ''),
            'nama_ppk'          => (string) ($p->nama_ppk ?? ''),
            'nip_ppk'           => (string) ($p->nip_ppk ?? ''),
        ];

        $numeric['total'] = $total;

        return [$text, $numeric, $rincian->count()];
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