<?php

namespace App\Http\Controllers;

use App\Exports\AmplopTemplateExport;
use App\Exports\DprTemplateExport;
use App\Exports\KwitansiTemplateExport;
use App\Exports\RincianTemplateExport;
use App\Exports\SpdTemplateExport;
use App\Exports\UmkTemplateExport;
use App\Models\PerjalananDinas;
use App\Services\UmkPdfConverter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class UmkController extends Controller
{
    private function muat($id): PerjalananDinas
    {
        return PerjalananDinas::with([
            'pegawai.divisi', 'pegawai.golongan', 'pegawai.jabatan', 'pegawai.pangkat',
            'kotaTujuan', 'rincianBiaya.coa',
        ])->findOrFail($id);
    }

    private function namaFile(PerjalananDinas $perjalanan, string $ext, string $prefix = 'UMK'): string
    {
        return $prefix . '-' . str_replace('/', '-', $perjalanan->nomor_pengajuan) . '.' . $ext;
    }

    /**
     * PDF dengan layout PERSIS sama dengan file Excel: template diisi lalu dikonversi LibreOffice.
     * Jika LibreOffice gagal, dipakai view Blade (DomPDF) sebagai cadangan bila view-nya ada.
     */
    private function pdfDariTemplate(
        PerjalananDinas $perjalanan,
        string $exporter,
        string $nama,
        ?string $viewCadangan = null,
        string $orientasi = 'portrait'
    ) {
        try {
            $isi = app(UmkPdfConverter::class)->render($perjalanan, $exporter);

            return response($isi, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $nama . '"',
            ]);
        } catch (\Throwable $e) {
            Log::warning("PDF {$nama} via LibreOffice gagal: " . $e->getMessage());
        }

        if ($viewCadangan && view()->exists($viewCadangan)) {
            $pdf = Pdf::loadView($viewCadangan, ['perjalanan' => $perjalanan]);
            $pdf->setPaper('a4', $orientasi);

            return $pdf->stream($nama);
        }

        abort(500, 'PDF gagal dibuat. Pastikan LibreOffice terpasang (lihat storage/logs/laravel.log).');
    }

    /* ===================== UMK ===================== */

    public function cetak($id)
    {
        $perjalanan = $this->muat($id);

        if ($perjalanan->status === 'DRAFT') {
            $perjalanan->update(['status' => 'PRINTED', 'printed_at' => now()]);
        }

        return $this->pdfDariTemplate(
            $perjalanan, UmkTemplateExport::class,
            $this->namaFile($perjalanan, 'pdf'), 'pdf.umk'
        );
    }

    public function downloadExcel($id)
    {
        $perjalanan = $this->muat($id);

        return (new UmkTemplateExport($perjalanan))
            ->download($this->namaFile($perjalanan, 'xlsx'));
    }

    /* ===================== RINCIAN ===================== */

    public function cetakRincian($id)
    {
        $perjalanan = $this->muat($id);

        return $this->pdfDariTemplate(
            $perjalanan, RincianTemplateExport::class,
            $this->namaFile($perjalanan, 'pdf', 'RINCIAN'), 'pdf.rincian'
        );
    }

    public function downloadRincianExcel($id)
    {
        $perjalanan = $this->muat($id);

        return (new RincianTemplateExport($perjalanan))
            ->download($this->namaFile($perjalanan, 'xlsx', 'RINCIAN'));
    }

    /* ===================== KWITANSI ===================== */

    public function cetakKwitansi($id)
    {
        $perjalanan = $this->muat($id);

        return $this->pdfDariTemplate(
            $perjalanan, KwitansiTemplateExport::class,
            $this->namaFile($perjalanan, 'pdf', 'KWITANSI'), 'pdf.kwitansi', 'landscape'
        );
    }

    public function downloadKwitansiExcel($id)
    {
        $perjalanan = $this->muat($id);

        return (new KwitansiTemplateExport($perjalanan))
            ->download($this->namaFile($perjalanan, 'xlsx', 'KWITANSI'));
    }

    /* ===================== SPD ===================== */

    public function cetakSpd($id)
    {
        $perjalanan = $this->muat($id);

        return $this->pdfDariTemplate(
            $perjalanan, SpdTemplateExport::class,
            $this->namaFile($perjalanan, 'pdf', 'SPD'), 'pdf.spd'
        );
    }

    public function downloadSpdExcel($id)
    {
        $perjalanan = $this->muat($id);

        return (new SpdTemplateExport($perjalanan))
            ->download($this->namaFile($perjalanan, 'xlsx', 'SPD'));
    }

    /* ===================== DPR (Daftar Pengeluaran Riil) ===================== */

    public function cetakDpr($id)
    {
        $perjalanan = $this->muat($id);

        return $this->pdfDariTemplate(
            $perjalanan, DprTemplateExport::class,
            $this->namaFile($perjalanan, 'pdf', 'DPR'), 'pdf.dpr'
        );
    }

    public function downloadDprExcel($id)
    {
        $perjalanan = $this->muat($id);

        return (new DprTemplateExport($perjalanan))
            ->download($this->namaFile($perjalanan, 'xlsx', 'DPR'));
    }

    /* ===================== AMPLOP ===================== */

    public function cetakAmplop($id)
    {
        $perjalanan = $this->muat($id);

        // Amplop tidak punya view Blade cadangan: PDF hanya lewat template + LibreOffice
        return $this->pdfDariTemplate(
            $perjalanan, AmplopTemplateExport::class,
            $this->namaFile($perjalanan, 'pdf', 'AMPLOP'), null, 'landscape'
        );
    }

    public function downloadAmplopExcel($id)
    {
        $perjalanan = $this->muat($id);

        return (new AmplopTemplateExport($perjalanan))
            ->download($this->namaFile($perjalanan, 'xlsx', 'AMPLOP'));
    }
}