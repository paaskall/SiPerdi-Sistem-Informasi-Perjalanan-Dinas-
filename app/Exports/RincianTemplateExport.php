<?php

namespace App\Exports;

use App\Models\PerjalananDinas;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RincianTemplateExport
{
    private const TABEL = [
        ''      => 9,
        'real_' => 22,
    ];
    private const MAX_ROWS = 10;

    private const KOLOM_BARIS  = ['A', 'B', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
    private const KOLOM_HITUNG = ['E', 'F', 'G', 'H', 'I', 'J'];

    private const SINGKATAN = [
        'kali' => 'kl', 'hari' => 'hr', 'malam' => 'mlm', 'orang' => 'org',
        'paket' => 'pkt', 'lembar' => 'lbr', 'buah' => 'bh',
    ];

    protected PerjalananDinas $perjalanan;
    protected string $templatePath;

    public function __construct(PerjalananDinas $perjalanan)
    {
        $this->perjalanan   = $perjalanan;
        $this->templatePath = storage_path('app/templates/rincian_template.xlsx');
    }

    public function download(string $filename = 'RINCIAN.xlsx'): StreamedResponse
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
        $sheet       = $spreadsheet->getSheetByName('RINCIAN') ?? $spreadsheet->getActiveSheet();

        [$text, $numeric, $info] = $this->buildData();

        foreach ($sheet->getRowIterator() as $row) {
            $cells = $row->getCellIterator();
            $cells->setIterateOnlyExistingCells(true);

            foreach ($cells as $cell) {
                $val = $cell->getValue();
                if ($val instanceof RichText) { $val = $val->getPlainText(); }
                if (! is_string($val) || ! str_contains($val, '{{')) { continue; }

                if (preg_match('/^\{\{(\w+)\}\}$/', $val, $m) && array_key_exists($m[1], $numeric)) {
                    $cell->setValueExplicit($numeric[$m[1]], DataType::TYPE_NUMERIC);
                    continue;
                }

                $cell->setValueExplicit(
                    preg_replace_callback('/\{\{(\w+)\}\}/', fn ($mm) => $text[$mm[1]] ?? '', $val),
                    DataType::TYPE_STRING
                );
            }
        }

        foreach (self::TABEL as $prefix => $first) {
            $jumlahBaris = $info[$prefix]['count'];
            $ringkas     = $info[$prefix]['ringkas'];

            for ($i = 1; $i <= self::MAX_ROWS; $i++) {
                $r = $first + $i - 1;

                if ($i > $jumlahBaris) {
                    foreach (self::KOLOM_BARIS as $col) {
                        $sheet->setCellValue($col . $r, null);
                    }
                } elseif (in_array($i, $ringkas, true)) {
                    foreach (self::KOLOM_HITUNG as $col) {
                        $sheet->setCellValue($col . $r, null);
                    }
                }
            }
        }

        return $spreadsheet;
    }

    private function buildData(): array
    {
        $p       = $this->perjalanan;
        $pegawai = $p->pegawai;

        $text    = [];
        $numeric = [];
        $info    = [];

        $sumber = [
            ''      => $p->rincianBiaya,
            'real_' => $this->rincianRealisasi(),
        ];
        $total = [];

        foreach ($sumber as $prefix => $koleksi) {
            $rows = $koleksi->values();

            if ($rows->count() > self::MAX_ROWS) {
                throw new \RuntimeException(
                    'Template rincian hanya memuat ' . self::MAX_ROWS . ' baris per tabel, data berisi ' . $rows->count() . ' baris.'
                );
            }

            $total[$prefix]   = 0;
            $info[$prefix]    = ['count' => $rows->count(), 'ringkas' => []];

            foreach ($rows as $idx => $r) {
                $n      = $idx + 1;
                $qty    = (float) ($r->qty ?? 1);
                $harga  = (float) ($r->harga_satuan ?? $r->harga ?? $r->nominal ?? 0);
                $jumlah = (float) ($r->nominal ?? ($qty * $harga));
                $total[$prefix] += $jumlah;

                [$ket, $tiket] = $this->uraian($r);
                if ($tiket) {
                    $info[$prefix]['ringkas'][] = $n;
                }

                $text["{$prefix}ket_$n"]        = $ket;
                $text["{$prefix}satuan_$n"]     = $this->singkat((string) ($r->satuan ?? 'kali'));
                $numeric["{$prefix}qty_$n"]     = $qty;
                $numeric["{$prefix}harga_$n"]   = $harga;
                $numeric["{$prefix}jumlah_$n"]  = $jumlah;
            }
        }

        $totalBayar     = $total[''];
        $totalRealisasi = $total['real_'];

        $berangkat = Carbon::parse($p->tanggal_berangkat)->locale('id');
        $dokumen   = Carbon::parse($p->tanggal_dokumen ?? now())->locale('id');

        $text += [
            'no_spd'               => (string) ($p->no_spd ?? ''),
            'tanggal_spd'          => $berangkat->translatedFormat('j F Y'),
            'terbilang'            => $this->terbilangRupiah($totalRealisasi),
            'bulan_tahun_dokumen'  => $dokumen->translatedFormat('F Y'),

            'nama_bendahara'       => (string) ($p->nama_bendahara ?? ''),
            'nip_bendahara'        => (string) ($p->nip_bendahara ?? ''),
            'nama_penerima'        => (string) ($pegawai->name ?? ''),
            'nip_penerima'         => (string) ($pegawai->nip ?? ''),
            'nama_ppk'             => (string) ($p->nama_ppk ?? ''),
            'nip_ppk'              => (string) ($p->nip_ppk ?? ''),
        ];

        $numeric['total']           = $totalBayar;
        $numeric['total_realisasi'] = $totalRealisasi;
        $numeric['selisih']         = $totalRealisasi - $totalBayar;
        $numeric['hari_dokumen']    = (int) $dokumen->day;

        return [$text, $numeric, $info];
    }

    private function rincianRealisasi()
    {
        if (method_exists($this->perjalanan, 'rincianRealisasi')) {
            return $this->perjalanan->rincianRealisasi;
        }

        return $this->perjalanan->rincianBiaya;
    }

    /** @return array{0: string, 1: bool} [uraian, apakah baris tiket] */
    private function uraian($item): array
    {
        $ket   = (string) ($item->keterangan ?: ($item->coa->nama ?? '-'));
        $lower = strtolower($ket . ' ' . ($item->coa->nama ?? ''));
        $p     = $this->perjalanan;

        // 1. TIKET PESAWAT / KERETA
        if (str_contains($lower, 'tiket') || str_contains($lower, 'ticket') || str_contains($lower, 'pesawat') || str_contains($lower, 'kereta')) {
            $asal  = $p->tempat_berangkat ?? 'Asal';
            $prov  = $p->kotaTujuan->provinsi ?? '';
            $kota  = $p->kotaTujuan->nama ?? 'Tujuan';
            return ["Tiket {$asal} - " . ($prov ? "{$prov}, " : '') . $kota, true];
        }

        // 2. TAKSI / TRANSPORT LOKAL
        if (str_contains($lower, 'taksi') || str_contains($lower, 'taxi') || str_contains($lower, 'transport') || str_contains($lower, 'ojek') || str_contains($lower, 'angkutan darat')) {
            $asal  = $p->tempat_berangkat ?? 'Asal';
            $kota  = $p->kotaTujuan->nama ?? 'Tujuan';
            return ["Taksi {$asal} - {$kota} PP", false];
        }

        return [$ket, false];
    }

    private function singkat(string $satuan): string
    {
        return self::SINGKATAN[strtolower(trim($satuan))] ?? $satuan;
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