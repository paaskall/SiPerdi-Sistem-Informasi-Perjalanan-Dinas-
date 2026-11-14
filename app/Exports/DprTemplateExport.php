<?php

namespace App\Exports;

use App\Models\PerjalananDinas;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DprTemplateExport
{
    private const KODE_KLASIFIKASI = 'PLB.1.2.1/SDM.04.1';

    private const MAX_ROWS = 5;
    private const BARIS_AWAL = 21;

    protected PerjalananDinas $perjalanan;
    protected string $templatePath;

    public function __construct(PerjalananDinas $perjalanan)
    {
        $this->perjalanan   = $perjalanan;
        $this->templatePath = storage_path('app/templates/dpr_template.xlsx');
    }

    public function download(string $filename = 'DPR.xlsx'): StreamedResponse
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
        $sheet       = $spreadsheet->getSheetByName('DPR') ?? $spreadsheet->getActiveSheet();

        [$text, $numeric, $jumlahBaris] = $this->buildData();

        foreach ($sheet->getRowIterator() as $row) {
            $cells = $row->getCellIterator();
            $cells->setIterateOnlyExistingCells(true);

            foreach ($cells as $cell) {
                $val = $cell->getValue();
                if ($val instanceof RichText) {
                    $val = $val->getPlainText();
                }
                if (! is_string($val) || ! str_contains($val, '{{')) {
                    continue;
                }

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

        for ($i = $jumlahBaris + 1; $i <= self::MAX_ROWS; $i++) {
            $r = self::BARIS_AWAL + $i - 1;
            foreach (['B', 'C', 'I'] as $col) {
                $sheet->setCellValue($col . $r, null);
            }
        }

        return $spreadsheet;
    }

    private function buildData(): array
    {
        $p       = $this->perjalanan;
        $pegawai = $p->pegawai;

        // --- Baris uraian: hanya biaya tanpa bukti (penginapan & transport lokal) ---
        $baris = [];
        foreach ($p->rincianBiaya as $r) {
            if (! $this->termasukDpr($r)) {
                continue;
            }

            $ket = (string) ($r->keterangan ?: ($r->coa->nama ?? '-'));
            $lower = strtolower($ket . ' ' . ($r->coa->nama ?? ''));

            // Format teks khusus jika ini Taksi/Transport
            if (str_contains($lower, 'taksi') || str_contains($lower, 'taxi') || str_contains($lower, 'transport') || str_contains($lower, 'ojek')) {
                $asal  = $p->tempat_berangkat ?? 'Asal';
                $kota  = $p->kotaTujuan->nama ?? 'Tujuan';
                $ket = "Taksi {$asal} - {$kota} PP";
            }

            $baris[] = [
                'ket'    => $ket,
                'jumlah' => (float) ($r->nominal ?? ((float) ($r->qty ?? 1) * (float) ($r->harga_satuan ?? 0))),
            ];
        }

        if (count($baris) > self::MAX_ROWS) {
            $sisa  = array_slice($baris, self::MAX_ROWS - 1);
            $baris = array_slice($baris, 0, self::MAX_ROWS - 1);
            $baris[] = [
                'ket'    => 'Lain-lain (' . count($sisa) . ' item)',
                'jumlah' => array_sum(array_column($sisa, 'jumlah')),
            ];
        }

        $text    = [];
        $numeric = [];
        $total   = 0.0;

        foreach ($baris as $idx => $b) {
            $n = $idx + 1;
            $text["ket_$n"]      = $b['ket'];
            $numeric["jumlah_$n"] = $b['jumlah'];
            $total += $b['jumlah'];
        }

        $spd     = Carbon::parse($p->tanggal_berangkat)->locale('id');
        $dokumen = Carbon::parse($p->tanggal_dokumen ?? now())->locale('id');

        $text += [
            'nama_pegawai'        => (string) ($pegawai->name ?? ''),
            'nip'                 => (string) ($pegawai->nip ?? ''),
            'jabatan'             => (string) ($pegawai->jabatan->nama ?? '-'),
            'no_spd'              => (string) ($p->no_spd ?? ''),
            'kode_klasifikasi'    => (string) ($p->kode_klasifikasi ?? self::KODE_KLASIFIKASI),
            'tanggal_spd'         => $spd->translatedFormat('j F Y'),
            'bulan_tahun_dokumen' => $dokumen->translatedFormat('F Y'),
            'nama_ppk'            => (string) ($p->nama_ppk ?? ''),
            'nip_ppk'             => (string) ($p->nip_ppk ?? ''),
        ];

        $numeric['total']        = $total;
        $numeric['hari_dokumen'] = (int) $dokumen->day;

        return [$text, $numeric, count($baris)];
    }

    private function termasukDpr($item): bool
    {
        $s = strtolower(($item->coa->nama ?? '') . ' ' . ($item->keterangan ?? ''));

        foreach (['tiket', 'ticket', 'pesawat', 'kereta', 'uang harian', 'uang saku'] as $kata) {
            if (str_contains($s, $kata)) {
                return false;
            }
        }

        foreach (['penginapan', 'hotel', 'akomodasi', 'taksi', 'taxi', 'ojek', 'transport', 'angkutan darat'] as $kata) {
            if (str_contains($s, $kata)) {
                return true;
            }
        }

        return false;
    }
}