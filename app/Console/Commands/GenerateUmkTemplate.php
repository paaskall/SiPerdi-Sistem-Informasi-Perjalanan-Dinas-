<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GenerateUmkTemplate extends Command
{
    protected $signature = 'umk:generate-template';
    protected $description = 'Generate template Excel UMK dari nol (bersih)';

    public function handle()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('UMK');

        // Lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(5);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(8);
        $sheet->getColumnDimension('G')->setWidth(5);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(3);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(5);
        $sheet->getColumnDimension('L')->setWidth(18);
        $sheet->getColumnDimension('M')->setWidth(5);

        // Baris 1-2: Kop
        $sheet->setCellValue('A1', 'PUSAT PELATIHAN DAN PENGEMBANGAN DAN KAJIAN MANAJEMEN PEMERINTAHAN');
        $sheet->mergeCells('A1:M1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('A2', 'Jln. Baruga Raya No. 48 Antang, Makassar');
        $sheet->mergeCells('A2:M2');
        $sheet->getStyle('A2')->getFont()->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris 4: No Pembukuan & Akun
        $sheet->setCellValue('C4', 'No. Pembukuan');
        $sheet->setCellValue('F4', ':');
        $sheet->setCellValue('G4', '{{no_pembukuan}}');
        $sheet->setCellValue('K4', 'AKUN :');
        $sheet->setCellValue('N4', '{{akun}}');
        $sheet->mergeCells('N4:Q4');

        // Baris 6: Sudah terima dari
        $sheet->setCellValue('C6', 'Sudah terima dari');
        $sheet->setCellValue('F6', ':  KPA/Puslatbang KMP');

        // Baris 8: Uang sebanyak
        $sheet->setCellValue('C8', 'Uang sebanyak');
        $sheet->setCellValue('F8', '{{uang_sebanyak}}');
        $sheet->mergeCells('F8:M8');

        // Baris 10: Guna membayar
        $sheet->setCellValue('C10', 'Guna membayar :');
        $sheet->setCellValue('F10', '{{uraian_perjalanan}}');
        $sheet->mergeCells('F10:M10');

        // Baris 12: Header rincian
        $sheet->setCellValue('C12', '-');
        $sheet->setCellValue('D12', 'Keterangan');
        $sheet->setCellValue('E12', '');
        $sheet->setCellValue('H12', 'Qty');
        $sheet->setCellValue('I12', '');
        $sheet->setCellValue('J12', 'Satuan');
        $sheet->setCellValue('K12', 'x');
        $sheet->setCellValue('L12', 'Harga Satuan');
        $sheet->setCellValue('M12', '=');
        $sheet->setCellValue('N12', 'Jumlah');

        // Baris 13-18: Rincian biaya (6 baris kosong dengan placeholder)
        for ($i = 13; $i <= 18; $i++) {
            $sheet->setCellValue('C' . $i, '-');
            $sheet->setCellValue('D' . $i, '{{ket_' . ($i - 12) . '}}');
            $sheet->setCellValue('H' . $i, '{{qty_' . ($i - 12) . '}}');
            $sheet->setCellValue('J' . $i, '{{satuan_' . ($i - 12) . '}}');
            $sheet->setCellValue('L' . $i, '{{harga_' . ($i - 12) . '}}');
            $sheet->setCellValue('N' . $i, '{{jumlah_' . ($i - 12) . '}}');
        }

        // Baris 20: Total
        $sheet->setCellValue('M20', '{{total}}');
        $sheet->mergeCells('M20:N20');
        $sheet->getStyle('M20:N20')->getFont()->setBold(true);

        // Baris 22: Terbilang
        $sheet->setCellValue('C22', 'Terbilang  Rp.');
        $sheet->setCellValue('F22', '{{terbilang}}');
        $sheet->mergeCells('F22:L22');

        // Baris 25: Tanggal & TTD
        $sheet->setCellValue('E25', 'Lunas Tgl.');
        $sheet->setCellValue('H25', 'Setuju Dibayar,');
        $sheet->setCellValue('L25', 'Makassar, {{tanggal_cetak}}');

        // Baris 26: Label jabatan
        $sheet->setCellValue('E26', 'Bendahara Pengeluaran,');
        $sheet->setCellValue('H26', 'Pejabat Pembuat Komitmen');
        $sheet->setCellValue('L26', 'Diterima Oleh,');

        // Baris 30-31: Nama & NIP
        $sheet->setCellValue('E30', '{{nama_bendahara}}');
        $sheet->setCellValue('E31', 'NIP. {{nip_bendahara}}');
        $sheet->setCellValue('H30', '{{nama_ppk}}');
        $sheet->setCellValue('H31', 'NIP. {{nip_ppk}}');
        $sheet->setCellValue('L30', '{{nama_penerima}}');
        $sheet->setCellValue('L31', 'NIP. {{nip_penerima}}');

        // Simpan
        $path = storage_path('app/templates/umk_template.xlsx');
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        $this->info('Template UMK berhasil dibuat: ' . $path);
    }
}