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
 * Mengisi storage/app/templates/spd_template.xlsx (placeholder {{nama}}).
 *
 * Pemakaian di controller (WAJIB di-return):
 *   return (new SpdTemplateExport($perjalanan))->download('SPD-001.xlsx');
 */
class SpdTemplateExport
{
    /** Kode klasifikasi surat yang tercetak di sebelah nomor SPD. Ubah di sini jika berganti. */
    private const KODE_KLASIFIKASI = 'PLB.1.2.1/SDM.04.1';

    /** Template memuat maksimal 5 baris pengikut. */
    private const MAX_PENGIKUT = 5;

    /** Placeholder yang ditulis sebagai angka (bukan teks). */
    private const KUNCI_ANGKA = [
        'jumlah_hari',
        'hari_berangkat', 'tahun_berangkat',
        'hari_pulang', 'tahun_pulang',
    ];

    protected PerjalananDinas $perjalanan;
    protected string $templatePath;

    public function __construct(PerjalananDinas $perjalanan)
    {
        $this->perjalanan   = $perjalanan;
        $this->templatePath = storage_path('app/templates/spd_template.xlsx');
    }

    public function download(string $filename = 'SPD.xlsx'): StreamedResponse
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
        $sheet       = $spreadsheet->getSheetByName('SPD') ?? $spreadsheet->getActiveSheet();

        $data = $this->buildData();

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
                if (preg_match('/^\{\{(\w+)\}\}$/', $val, $m) && in_array($m[1], self::KUNCI_ANGKA, true)) {
                    $cell->setValueExplicit($data[$m[1]], DataType::TYPE_NUMERIC);
                    continue;
                }

                // Teks WAJIB string eksplisit (NIP berupa angka tidak boleh jadi 1.99E+17)
                $cell->setValueExplicit(
                    preg_replace_callback('/\{\{(\w+)\}\}/', fn ($mm) => (string) ($data[$mm[1]] ?? ''), $val),
                    DataType::TYPE_STRING
                );
            }
        }

        return $spreadsheet;
    }

    /** @return array<string,string|int> */
    private function buildData(): array
    {
        $p       = $this->perjalanan;
        $pegawai = $p->pegawai;
        $pangkat = $pegawai->pangkat ?? null;

        $berangkat = Carbon::parse($p->tanggal_berangkat)->locale('id');
        $pulang    = Carbon::parse($p->tanggal_pulang)->locale('id');
        $dokumen   = Carbon::parse($p->tanggal_dokumen ?? now())->locale('id');
        $hari      = (int) $p->jumlah_hari;

        $data = [
            // Kop kanan
            'lembar_ke'        => (string) ($p->lembar_ke ?? ''),
            'kode_no'          => (string) ($p->kode_no ?? ''),
            'no_spd'           => (string) ($p->no_spd ?? ''),
            'kode_klasifikasi' => (string) ($p->kode_klasifikasi ?? self::KODE_KLASIFIKASI),

            // Pejabat & pegawai
            'nama_ppk'         => (string) ($p->nama_ppk ?? ''),
            'nip_ppk'          => (string) ($p->nip_ppk ?? ''),
            'nama_pegawai'     => (string) ($pegawai->name ?? ''),
            'nip'              => (string) ($pegawai->nip ?? ''),
            'pangkat_golongan' => $pangkat ? trim($pangkat->nama . ', ' . $pangkat->golongan_ruang) : '-',
            'jabatan'          => (string) ($pegawai->jabatan->nama ?? '-'),
            'tingkat_biaya'    => (string) ($p->tingkat_biaya ?? '-'),

            // Perjalanan
            'maksud_perjalanan' => (string) $p->maksud_perjalanan,
            'alat_angkutan'     => (string) ($p->alat_angkutan ?? '-'),
            'tempat_berangkat'  => (string) ($p->tempat_berangkat ?? 'Makassar'),
            'kota_tujuan'       => (string) ($p->kotaTujuan->nama ?? '-'),

            // Lama & tanggal
            'jumlah_hari'           => $hari,
            'jumlah_hari_terbilang' => $hari === 0 ? 'nol' : trim(preg_replace('/\s+/', ' ', $this->terbilang($hari))),
            'hari_berangkat'        => (int) $berangkat->day,
            'bulan_berangkat'       => $berangkat->translatedFormat('F'),
            'tahun_berangkat'       => (int) $berangkat->year,
            'hari_pulang'           => (int) $pulang->day,
            'bulan_pulang'          => $pulang->translatedFormat('F'),
            'tahun_pulang'          => (int) $pulang->year,

            // Anggaran & penutup
            'akun'             => (string) ($p->akun ?? ($p->rincianBiaya->first()->coa->kode ?? '')),
            'keterangan_lain'  => (string) ($p->keterangan_lain ?? ''),
            'tanggal_dokumen'  => $dokumen->translatedFormat('j F Y'),
        ];

        // Pengikut (maks 5 baris)
        $pengikut = $this->daftarPengikut();
        for ($i = 1; $i <= self::MAX_PENGIKUT; $i++) {
            $data["pengikut_$i"]     = $pengikut[$i - 1]['nama'] ?? '';
            $data["ket_pengikut_$i"] = $pengikut[$i - 1]['keterangan'] ?? '';
        }

        return $data;
    }

    /**
     * Daftar pengikut. Dipakai bila model punya relasi/atribut `pengikut`
     * (koleksi atau array berisi objek/array dengan `nama` dan `keterangan`, atau string nama).
     * Jika belum ada, kolom pengikut dibiarkan kosong.
     *
     * @return array<int, array{nama: string, keterangan: string}>
     */
    private function daftarPengikut(): array
    {
        $p      = $this->perjalanan;
        $sumber = method_exists($p, 'pengikut') ? $p->pengikut : $p->getAttribute('pengikut');

        if (empty($sumber)) {
            return [];
        }

        $hasil = [];
        foreach ($sumber as $item) {
            $hasil[] = is_string($item)
                ? ['nama' => $item, 'keterangan' => '']
                : ['nama' => (string) data_get($item, 'nama', ''), 'keterangan' => (string) data_get($item, 'keterangan', '')];
        }

        return array_slice($hasil, 0, self::MAX_PENGIKUT);
    }

    private function terbilang(int $n): string
    {
        $huruf = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($n < 12)         return $huruf[$n];
        if ($n < 20)         return $this->terbilang($n - 10) . ' belas';
        if ($n < 100)        return $this->terbilang(intdiv($n, 10)) . ' puluh ' . $this->terbilang($n % 10);
        if ($n < 200)        return 'seratus ' . $this->terbilang($n - 100);
        if ($n < 1000)       return $this->terbilang(intdiv($n, 100)) . ' ratus ' . $this->terbilang($n % 100);
        if ($n < 2000)       return 'seribu ' . $this->terbilang($n - 1000);
        if ($n < 1000000)    return $this->terbilang(intdiv($n, 1000)) . ' ribu ' . $this->terbilang($n % 1000);

        return (string) $n;
    }
}