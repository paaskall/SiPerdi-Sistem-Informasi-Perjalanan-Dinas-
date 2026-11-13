<?php

namespace App\Exports;

use App\Models\PerjalananDinas;
use App\Helpers\TerbilangHelper;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RincianExport implements FromArray, WithEvents
{
    protected $perjalanan;
    protected $rowCount = 0;

    public function __construct($id)
    {
        // Eager load relasi kotaTujuan dan pegawai agar tidak berat (N+1 query)
        $this->perjalanan = PerjalananDinas::with(['pegawai', 'kotaTujuan', 'rincianBiaya'])->findOrFail($id);
        $this->rowCount = $this->perjalanan->rincianBiaya->count();
    }

    public function array(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $p = $this->perjalanan;

                // ====== SETUP LEBAR KOLOM ======
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(45); // Lebarkan sedikit untuk teks tiket
                $sheet->getColumnDimension('C')->setWidth(3); // (
                $sheet->getColumnDimension('D')->setWidth(6); // qty
                $sheet->getColumnDimension('E')->setWidth(8); // satuan
                $sheet->getColumnDimension('F')->setWidth(3); // x
                $sheet->getColumnDimension('G')->setWidth(15); // harga
                $sheet->getColumnDimension('H')->setWidth(3); // )
                $sheet->getColumnDimension('I')->setWidth(18); // jumlah
                $sheet->getColumnDimension('J')->setWidth(20); // keterangan

                // ====== JUDUL ======
                $sheet->setCellValue('A1', 'RINCIAN BIAYA PERJALANAN DINAS');
                $sheet->mergeCells('A1:J1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // ====== HEADER SPD ======
                $sheet->setCellValue('A3', 'Lampiran SPD Nomor');
                $sheet->setCellValue('C3', ':');
                $sheet->setCellValue('D3', $p->no_spd);
                $sheet->mergeCells('D3:F3');

                $sheet->setCellValue('A4', 'Tanggal');
                $sheet->setCellValue('C4', ':');
                $sheet->setCellValue('D4', \Carbon\Carbon::parse($p->tanggal_berangkat)->format('d/m/Y'));

                // ====== HEADER TABEL ======
                $rowHeader = 6;
                $sheet->setCellValue('A'.$rowHeader, 'No');
                $sheet->setCellValue('B'.$rowHeader, 'PERINCIAN BIAYA');
                $sheet->mergeCells('C'.$rowHeader.':H'.$rowHeader); // Gabung C sampai H
                $sheet->setCellValue('C'.$rowHeader, 'RINCIAN (QTY x HARGA)'); // Opsional: Header untuk gabungan
                $sheet->setCellValue('I'.$rowHeader, 'JUMLAH');
                $sheet->setCellValue('J'.$rowHeader, 'KETERANGAN');

                $sheet->getStyle('A'.$rowHeader.':J'.$rowHeader)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                ]);
                $sheet->getRowDimension($rowHeader)->setRowHeight(25);

                // Sub Judul
                $sheet->setCellValue('B'.($rowHeader+1), 'Biaya Perjalanan Dinas yang telah dibayar semula terdiri dari :');
                $sheet->mergeCells('B'.($rowHeader+1).':J'.($rowHeader+1));
                $sheet->getStyle('B'.($rowHeader+1))->getFont()->setBold(true);

                // ====== DATA RINCIAN ======
                $startRow = 8;
                $currentRow = $startRow;

                foreach ($p->rincianBiaya as $i => $item) {
                    // ==========================================
                    // LOGIKA AUTO-TEXT TIKET PESAWAT / TRANSPORT
                    // ==========================================
                    $keterangan = $item->keterangan ?? '-';
                    $teksAsli = strtolower($item->keterangan ?? '');
                    
                    if (str_contains($teksAsli, 'tiket') || str_contains($teksAsli, 'pesawat') || str_contains($teksAsli, 'transport')) {
                        $kotaAsal = $p->tempat_berangkat ?? 'Asal'; 
                        $provinsiTujuan = $p->kotaTujuan->provinsi ?? '';
                        $kotaTujuan = $p->kotaTujuan->nama ?? 'Tujuan';
                        
                        // Format: Tiket Makassar - DKI Jakarta, Jakarta Pusat
                        $keterangan = "Tiket {$kotaAsal} - {$provinsiTujuan}, {$kotaTujuan}";
                    }
                    // ==========================================

                    $sheet->setCellValue('A'.$currentRow, $i + 1);
                    $sheet->setCellValue('B'.$currentRow, $keterangan);
                    
                    // Format ( Qty Satuan x Harga )
                    $sheet->setCellValue('C'.$currentRow, '(');
                    $sheet->setCellValue('D'.$currentRow, $item->qty);
                    $sheet->setCellValue('E'.$currentRow, $item->satuan);
                    $sheet->setCellValue('F'.$currentRow, 'x');
                    $sheet->setCellValue('G'.$currentRow, $item->harga_satuan);
                    $sheet->setCellValue('H'.$currentRow, ')');
                    
                    $sheet->setCellValue('I'.$currentRow, $item->nominal);
                    $sheet->setCellValue('J'.$currentRow, '');

                    // Style baris data
                    $sheet->getStyle('A'.$currentRow.':J'.$currentRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    
                    // Rata kanan untuk Harga dan Jumlah
                    $sheet->getStyle('G'.$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle('I'.$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
                    
                    // Rata tengah untuk Qty, Satuan, ( x )
                    $sheet->getStyle('C'.$currentRow.':H'.$currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    // Rata kiri untuk No dan Uraian
                    $sheet->getStyle('A'.$currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B'.$currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    $currentRow++;
                }

                // ====== TOTAL ======
                $sheet->setCellValue('I'.$currentRow, $p->total_estimasi);
                $sheet->getStyle('I'.$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('I'.$currentRow)->getFont()->setBold(true);
                $sheet->getStyle('A'.$currentRow.':J'.$currentRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                
                // ====== TERBILANG ======
                $currentRow += 2;
                $sheet->setCellValue('B'.$currentRow, 'JUMLAH :');
                $sheet->setCellValue('I'.$currentRow, $p->total_estimasi);
                $sheet->getStyle('I'.$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('B'.$currentRow)->getFont()->setBold(true);

                $currentRow++;
                $sheet->setCellValue('B'.$currentRow, 'Terbilang :');
                $sheet->setCellValue('C'.$currentRow, ucwords(TerbilangHelper::convert($p->total_estimasi)) . ' Rupiah');
                $sheet->mergeCells('C'.$currentRow.':J'.$currentRow);
                $sheet->getStyle('C'.$currentRow)->getFont()->setItalic(true);

                // ====== TANDA TANGAN ======
                $currentRow += 3;
                $sheet->setCellValue('F'.$currentRow, 'Makassar, ' . \Carbon\Carbon::now()->translatedFormat('d F Y'));
                
                $currentRow += 2;
                $sheet->setCellValue('B'.$currentRow, 'Telah dibayar sejumlah');
                $sheet->setCellValue('F'.$currentRow, 'Telah menerima jumlah uang sebesar');
                
                $currentRow++;
                $sheet->setCellValue('B'.$currentRow, 'Rp ' . number_format($p->total_estimasi, 0, ',', '.'));
                $sheet->setCellValue('F'.$currentRow, 'Rp ' . number_format($p->total_estimasi, 0, ',', '.'));

                $currentRow += 2;
                $sheet->setCellValue('B'.$currentRow, 'Bendahara Pengeluaran');
                $sheet->setCellValue('F'.$currentRow, 'Yang menerima');

                // Space untuk TTD
                $currentRow += 3;
                $sheet->setCellValue('B'.$currentRow, $p->nama_bendahara);
                $sheet->setCellValue('F'.$currentRow, $p->pegawai->name ?? '-');
                $sheet->getStyle('B'.$currentRow)->getFont()->setBold(true)->setUnderline(true);
                $sheet->getStyle('F'.$currentRow)->getFont()->setBold(true)->setUnderline(true);

                $currentRow++;
                $sheet->setCellValue('B'.$currentRow, 'NIP. ' . $p->nip_bendahara);
                $sheet->setCellValue('F'.$currentRow, 'NIP. ' . ($p->pegawai->nip ?? '-'));

                // ====== PERHITUNGAN SPD RAMPUNG ======
                $currentRow += 4;
                $sheet->setCellValue('A'.$currentRow, 'PERHITUNGAN SPD RAMPUNG');
                $sheet->getStyle('A'.$currentRow)->getFont()->setBold(true);

                $currentRow += 2;
                $sheet->setCellValue('B'.$currentRow, 'Ditetapkan sejumlah');
                $sheet->setCellValue('I'.$currentRow, $p->total_estimasi);
                $sheet->getStyle('I'.$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');

                $currentRow++;
                $sheet->setCellValue('B'.$currentRow, 'Yang telah dibayar semula');
                $sheet->setCellValue('I'.$currentRow, 0);
                $sheet->getStyle('I'.$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');

                $currentRow++;
                $sheet->setCellValue('B'.$currentRow, 'Sisa kurang/lebih');
                $sheet->setCellValue('I'.$currentRow, $p->total_estimasi);
                $sheet->getStyle('I'.$currentRow)->getNumberFormat()->setFormatCode('#,##0.00');

                $currentRow += 2;
                $sheet->setCellValue('F'.$currentRow, 'Pejabat Pembuat Komitmen');
                
                $currentRow += 3;
                $sheet->setCellValue('F'.$currentRow, $p->nama_ppk);
                $sheet->getStyle('F'.$currentRow)->getFont()->setBold(true)->setUnderline(true);
                
                $currentRow++;
                $sheet->setCellValue('F'.$currentRow, 'NIP. ' . $p->nip_ppk);

                // Align center untuk bagian tanda tangan
                $sheet->getStyle('B'.($currentRow-11).':J'.$currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B'.($currentRow-11).':B'.$currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('F'.($currentRow-11).':F'.$currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            },
        ];
    }
}