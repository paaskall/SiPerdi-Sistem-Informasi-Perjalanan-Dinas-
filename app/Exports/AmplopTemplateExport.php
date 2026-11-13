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
 * Amplop UMK: mengisi storage/app/templates/amplop_template.xlsx.
 *
 * Amplop memuat nama pegawai, keterangan UMK, dan rincian uang yang dimasukkan
 * ke dalam amplop (uang harian, penginapan, transport lokal, dst.) beserta totalnya.
 * Baris yang masuk ditentukan oleh method termasukAmplop() (semua kecuali tiket).
 *
 * Pemakaian di controller (WAJIB di-return):
 *   return (new AmplopTemplateExport($perjalanan))->download('AMPLOP-001.xlsx');
 */
class AmplopTemplateExport
{
    /** Template memuat 6 baris uraian (baris 6-11), setara kapasitas UMK. */
    private const MAX_ROWS   = 6;
    private const BARIS_AWAL = 6;

    /** Kolom yang berisi data/teks tetap pada tiap baris uraian (dikosongkan bila baris tak terpakai). */
    private const KOLOM_BARIS = ['A', 'B', 'F', 'G', 'H', 'I', 'J', 'L'];

    private const KOTA_ASAL_DEFAULT = 'Makassar';

    /**
     * >>> CEK DI MODEL PerjalananDinas: nama kolom di bawah ini adalah dugaan. <<<
     * Method atribut() memakai kolom pertama yang terisi; bila tidak ada satu pun yang
     * cocok, bagian itu dilewati (kalimat keterangan tetap terbentuk, hanya lebih pendek).
     */
    private const ATTR_KOTA_ASAL       = ['kota_asal', 'tempat_berangkat'];
    private const ATTR_TANGGAL_KEMBALI = ['tanggal_kembali', 'tanggal_pulang', 'tanggal_tiba'];
    private const ATTR_MAKSUD          = ['maksud_perjalanan', 'maksud', 'tujuan_perjalanan', 'keperluan', 'deskripsi'];

    protected PerjalananDinas $perjalanan;
    protected string $templatePath;

    public function __construct(PerjalananDinas $perjalanan)
    {
        $this->perjalanan   = $perjalanan;
        $this->templatePath = storage_path('app/templates/amplop_template.xlsx');
    }

    public function download(string $filename = 'AMPLOP.xlsx'): StreamedResponse
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
        $sheet       = $spreadsheet->getSheetByName('AMPLOP') ?? $spreadsheet->getActiveSheet();

        [$text, $numeric, $jumlahBaris] = $this->buildData();

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

                // Sel berisi HANYA satu placeholder angka -> tulis sebagai angka
                if (preg_match('/^\{\{(\w+)\}\}$/', $val, $m) && array_key_exists($m[1], $numeric)) {
                    $cell->setValueExplicit($numeric[$m[1]], DataType::TYPE_NUMERIC);
                    continue;
                }

                // Teks WAJIB string eksplisit
                $cell->setValueExplicit(
                    preg_replace_callback('/\{\{(\w+)\}\}/', fn ($mm) => $text[$mm[1]] ?? '', $val),
                    DataType::TYPE_STRING
                );
            }
        }

        // Baris uraian yang tak terpakai dikosongkan seluruhnya (termasuk tanda "-", "x", "=")
        for ($i = $jumlahBaris + 1; $i <= self::MAX_ROWS; $i++) {
            $r = self::BARIS_AWAL + $i - 1;
            foreach (self::KOLOM_BARIS as $col) {
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

        // --- Baris uraian ---
        $baris = [];
        foreach ($p->rincianBiaya as $r) {
            if (! $this->termasukAmplop($r)) {
                continue;
            }

            $ket     = (string) ($r->keterangan ?: ($r->coa->nama ?? '-'));
            $qty     = (float) ($r->qty ?? 1);
            $harga   = (float) ($r->harga_satuan ?? 0);
            $nominal = (float) ($r->nominal ?? ($qty * $harga));

            // Rumus tampilan "qty x harga = jumlah" harus selalu cocok. Bila data tidak
            // konsisten (mis. hanya ada nominal), tampilkan sebagai 1 x nominal.
            if ($qty <= 0 || abs($qty * $harga - $nominal) > 0.5) {
                $qty   = 1.0;
                $harga = $nominal;
            }

            $baris[] = [
                'ket'    => $ket,
                'qty'    => $qty,
                'sat'    => $this->satuan($r, $ket),
                'harga'  => $harga,
                'jumlah' => $nominal,
            ];
        }

        // Lebih dari 6 baris: baris ke-6 menampung sisanya, supaya total tetap benar
        if (count($baris) > self::MAX_ROWS) {
            $sisa    = array_slice($baris, self::MAX_ROWS - 1);
            $baris   = array_slice($baris, 0, self::MAX_ROWS - 1);
            $lain    = array_sum(array_column($sisa, 'jumlah'));
            $baris[] = [
                'ket'    => 'Lain-lain (' . count($sisa) . ' item)',
                'qty'    => 1.0,
                'sat'    => 'kl',
                'harga'  => $lain,
                'jumlah' => $lain,
            ];
        }

        $text    = [];
        $numeric = [];
        $total   = 0.0;

        foreach ($baris as $idx => $b) {
            $n = $idx + 1;
            $text["ket_$n"]       = $b['ket'];
            $text["sat_$n"]       = $b['sat'];
            $numeric["qty_$n"]    = $b['qty'];
            $numeric["harga_$n"]  = $b['harga'];
            $numeric["jumlah_$n"] = $b['jumlah'];
            $total += $b['jumlah'];
        }

        $text['nama_pegawai']    = (string) ($pegawai->name ?? '');
        $text['guna_membayar']   = $this->kalimatKeterangan();
        $numeric['total']        = $total;

        return [$text, $numeric, count($baris)];
    }

    /**
     * "UMK Makassar - Kota Tujuan PP 21 September 2021 s/d 25 September 2021 <maksud>,
     *  dengan rincian sebagai berikut :"
     */
    private function kalimatKeterangan(): string
    {
        $p = $this->perjalanan;

        $asal   = (string) $this->atribut(self::ATTR_KOTA_ASAL, self::KOTA_ASAL_DEFAULT);
        $kota   = $p->kotaTujuan;
        $tujuan = (string) ($kota->nama ?? $kota->nama_kota ?? '');

        $berangkat = Carbon::parse($p->tanggal_berangkat)->locale('id')->translatedFormat('j F Y');
        $kembaliRaw = $this->atribut(self::ATTR_TANGGAL_KEMBALI);
        $rentang   = $berangkat;
        if ($kembaliRaw) {
            $rentang .= ' s/d ' . Carbon::parse($kembaliRaw)->locale('id')->translatedFormat('j F Y');
        }

        $maksud = trim((string) $this->atribut(self::ATTR_MAKSUD, ''));
        if (mb_strlen($maksud) > 250) {                    // kotak keterangan hanya muat ±4 baris
            $maksud = rtrim(mb_substr($maksud, 0, 247)) . '...';
        }

        $rute  = $tujuan !== '' ? "$asal - $tujuan" : $asal;
        $kalimat = "UMK $rute PP $rentang";
        if ($maksud !== '') {
            $kalimat .= " $maksud";
        }

        return $kalimat . ', dengan rincian sebagai berikut :';
    }

    /** Nilai kolom pertama yang terisi di model perjalanan, atau $default. */
    private function atribut(array $kandidat, $default = null)
    {
        foreach ($kandidat as $kolom) {
            try {
                $v = $this->perjalanan->getAttribute($kolom);
            } catch (\Throwable $e) {                      // mis. preventAccessingMissingAttributes aktif
                continue;
            }
            if ($v !== null && $v !== '') {
                return $v;
            }
        }

        return $default;
    }

    /** Satuan: hari untuk uang harian & penginapan, selain itu kali. */
    private function satuan($item, string $ket): string
    {
        $s = strtolower(($item->coa->nama ?? '') . ' ' . $ket);
        foreach (['uang harian', 'uang saku', 'penginapan', 'hotel', 'akomodasi'] as $kata) {
            if (str_contains($s, $kata)) {
                return 'hr';
            }
        }

        return 'kl';
    }

    /**
     * Apakah baris rincian ini dimasukkan ke amplop?
     * Ya: semua uang tunai (uang harian, penginapan, transport lokal, dll).
     * Tidak: tiket pesawat/kereta (dibelikan kantor, tidak ada uang tunainya).
     * >>> Samakan dengan filter baris di UmkTemplateExport agar total amplop = total UMK. <<<
     */
    private function termasukAmplop($item): bool
    {
        $s = strtolower(($item->coa->nama ?? '') . ' ' . ($item->keterangan ?? ''));

        foreach (['tiket', 'ticket', 'pesawat', 'kereta'] as $kata) {
            if (str_contains($s, $kata)) {
                return false;
            }
        }

        return true;
    }
}