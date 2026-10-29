<?php

namespace App\Http\Controllers;

use App\Models\PerjalananDinas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SppdController extends Controller
{
    public function generate($id)
    {
        // Ambil data pengajuan beserta relasinya
        $perjalanan = PerjalananDinas::with(['pegawai.divisi', 'pegawai.golongan', 'kotaTujuan', 'rincianBiaya.coa'])->findOrFail($id);

        // Ambil data pimpinan (asumsi: ambil user pertama dengan role pimpinan)
        $pimpinan = \App\Models\User::role('pimpinan')->first();

        // Load view PDF dan masukkan datanya
        $pdf = Pdf::loadView('pdf.sppd', compact('perjalanan', 'pimpinan'));
        
        // Atur ukuran kertas (F4 / A4)
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('SPPD-' . str_replace('/', '-', $perjalanan->nomor_pengajuan) . '.pdf');
    }
}