<?php

namespace App\Services;

use App\Exports\UmkTemplateExport;
use App\Models\PerjalananDinas;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

/**
 * Membuat PDF UMK dengan layout PERSIS sama dengan template Excel:
 * template diisi -> disimpan sementara sebagai .xlsx -> dikonversi LibreOffice (headless) -> PDF.
 */
class UmkPdfConverter
{
    /**
     * @return string isi file PDF (binary)
     * @throws \RuntimeException jika LibreOffice tidak ada / konversi gagal
     */
    public function render(PerjalananDinas $perjalanan, string $exporter = UmkTemplateExport::class): string
    {
        $work = sys_get_temp_dir() . '/umk_' . Str::uuid();
        File::makeDirectory($work . '/profile', 0775, true);

        try {
            $xlsx = $work . '/umk.xlsx';
            (new $exporter($perjalanan))->saveTo($xlsx);

            $process = new Process(
                [
                    config('services.libreoffice.binary', 'soffice'),
                    '--headless', '--norestore', '--nologo',
                    // profil sementara per-request: tidak bentrok jika dua orang cetak bersamaan
                    '-env:UserInstallation=file://' . $work . '/profile',
                    '--convert-to', 'pdf',
                    '--outdir', $work,
                    $xlsx,
                ],
                $work,
                ['HOME' => $work],
                null,
                90
            );
            $process->run();

            $pdf = $work . '/umk.pdf';
            if (! is_file($pdf)) {
                throw new \RuntimeException(
                    'Konversi PDF gagal: ' . trim($process->getErrorOutput() . ' ' . $process->getOutput())
                );
            }

            return file_get_contents($pdf);
        } finally {
            File::deleteDirectory($work);
        }
    }
}