<?php

namespace App\Exports;

use App\Models\PerjalananDinas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UmkExport implements FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $perjalanan;

    public function __construct(PerjalananDinas $perjalanan)
    {
        $this->perjalanan = $perjalanan;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        return collect($this->perjalanan->rincianBiaya);
    }

    public function headings(): array
    {
        return [
            ['UMK ' . $this->perjalanan->nomor_pengajuan],
            ['No. Pembukuan: ' . $this->perjalanan->no_pembukuan],
            ['Akun: ' . $this->perjalanan->akun],
            [],
            ['Pegawai', ':', $this->perjalanan->pegawai->name],
            ['NIP', ':', $this->perjalanan->pegawai->nip],
            ['Jabatan', ':', $this->perjalanan->pegawai->jabatan->nama ?? '-'],
            ['Pangkat', ':', $this->perjalanan->pegawai->pangkat->nama ?? '-'],
            ['Tujuan', ':', $this->perjalanan->kotaTujuan->nama],
            ['Tempat Berangkat', ':', $this->perjalanan->tempat_berangkat],
            ['Alat Angkutan', ':', $this->perjalanan->alat_angkutan],
            ['Tingkat Biaya', ':', $this->perjalanan->tingkat_biaya],
            ['Tanggal Berangkat', ':', $this->perjalanan->tanggal_berangkat->format('d/m/Y')],
            ['Tanggal Pulang', ':', $this->perjalanan->tanggal_pulang->format('d/m/Y')],
            ['Lama Perjalanan', ':', $this->perjalanan->jumlah_hari . ' Hari'],
            ['Maksud Perjalanan', ':', $this->perjalanan->maksud_perjalanan],
            [],
            ['No', 'Keterangan', 'Qty', 'Satuan', 'Harga Satuan', 'Jumlah'],
        ];
    }

    public function map($rincian): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            ($rincian->coa->nama ?? '') . ' - ' . $rincian->keterangan,
            $rincian->qty,
            $rincian->satuan,
            number_format($rincian->harga_satuan, 0, ',', '.'),
            number_format($rincian->nominal, 0, ',', '.'),
        ];
    }

    // TAMBAHKAN : ?array DI SINI
    public function styles(Worksheet $sheet): ?array
    {
        $rowCount = $this->collection()->count() + 19;

        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Total
        $sheet->setCellValue('E' . ($rowCount + 1), 'Total');
        $sheet->setCellValue('F' . ($rowCount + 1), number_format($this->perjalanan->total_estimasi, 0, ',', '.'));
        $sheet->getStyle('E' . ($rowCount + 1) . ':F' . ($rowCount + 1))->getFont()->setBold(true);

        // Tanda tangan
        $ttdRow = $rowCount + 4;
        $sheet->setCellValue('A' . $ttdRow, 'Bendahara Pengeluaran');
        $sheet->setCellValue('D' . $ttdRow, 'Pejabat Pembuat Komitmen');
        $sheet->setCellValue('A' . ($ttdRow + 4), $this->perjalanan->nama_bendahara);
        $sheet->setCellValue('A' . ($ttdRow + 5), 'NIP. ' . $this->perjalanan->nip_bendahara);
        $sheet->setCellValue('D' . ($ttdRow + 4), $this->perjalanan->nama_ppk);
        $sheet->setCellValue('D' . ($ttdRow + 5), 'NIP. ' . $this->perjalanan->nip_ppk);

        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}