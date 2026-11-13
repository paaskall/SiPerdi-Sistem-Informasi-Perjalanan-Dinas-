<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rincian Biaya Perjalanan Dinas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { font-size: 14px; margin: 0; font-weight: bold; text-decoration: underline; }
        
        .info-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        
        /* Tabel Utama */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 4px 6px; vertical-align: middle; }
        .data-table th { background-color: #e5e7eb; text-align: center; font-weight: bold; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .signature-table { width: 100%; margin-top: 20px; }
        .signature-table td { text-align: center; width: 50%; vertical-align: top; }
        .space-ttd { height: 60px; }
        
        .spd-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .spd-table td { padding: 3px 0; vertical-align: top; }
    </style>
</head>
<body>

    <div class="header">
        <h2>RINCIAN BIAYA PERJALANAN DINAS</h2>
    </div>

    <table class="info-table">
        <tr>
            <td width="130">Lampiran SPD Nomor</td>
            <td width="10">:</td>
            <td>{{ $perjalanan->no_spd }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($perjalanan->tanggal_berangkat)->format('d F Y') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">PERINCIAN BIAYA</th>
                <th width="25%">JUMLAH</th>
                <th width="25%">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4">Biaya Perjalanan Dinas yang telah dibayar semula terdiri dari :</td>
            </tr>
            @foreach($perjalanan->rincianBiaya as $index => $item)
                @php
                    $keterangan = $item->keterangan ?? '-';
                    $teksAsli = strtolower($item->keterangan ?? '');
                    if (str_contains($teksAsli, 'tiket') || str_contains($teksAsli, 'pesawat')) {
                        $keterangan = "Tiket " . ($perjalanan->tempat_berangkat ?? 'Asal') . " - " . ($perjalanan->kotaTujuan->provinsi ?? '') . ", " . ($perjalanan->kotaTujuan->nama ?? 'Tujuan');
                    } else {
                        $keterangan = $keterangan . " (" . $item->qty . " " . $item->satuan . " x Rp " . number_format($item->harga_satuan, 0, ',', '.') . ")";
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $keterangan }}</td>
                    <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" class="text-right" style="font-weight: bold;">Jumlah Total</td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($perjalanan->total_estimasi, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            
            <tr><td colspan="4" style="height: 20px;"></td></tr>

            <!-- Tabel Realisasi -->
            <tr>
                <td colspan="4">Ditetapkan sejumlah realisasi pengeluaran riil yaitu :</td>
            </tr>
            @foreach($perjalanan->rincianBiaya as $index => $item)
                @php
                    $keterangan = $item->keterangan ?? '-';
                    $teksAsli = strtolower($item->keterangan ?? '');
                    if (str_contains($teksAsli, 'tiket') || str_contains($teksAsli, 'pesawat')) {
                        $keterangan = "Tiket " . ($perjalanan->tempat_berangkat ?? 'Asal') . " - " . ($perjalanan->kotaTujuan->provinsi ?? '') . ", " . ($perjalanan->kotaTujuan->nama ?? 'Tujuan');
                    } else {
                        $keterangan = $keterangan . " (" . $item->qty . " " . $item->satuan . " x Rp " . number_format($item->harga_satuan, 0, ',', '.') . ")";
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $keterangan }}</td>
                    <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" class="text-right" style="font-weight: bold;">Jumlah Total</td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($perjalanan->total_estimasi, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="info-table" style="margin-bottom: 20px;">
        <tr>
            <td width="80">Terbilang :</td>
            <td style="font-style: italic;">{{ ucwords(\App\Helpers\TerbilangHelper::convert($perjalanan->total_estimasi)) }} Rupiah</td>
        </tr>
    </table>

    <!-- Tanda Tangan Utama -->
    <table class="signature-table">
        <tr>
            <td>
                Telah dibayar sejumlah<br>
                Rp {{ number_format($perjalanan->total_estimasi, 0, ',', '.') }}<br><br>
                Bendahara Pengeluaran
                <div class="space-ttd"></div>
                <strong><u>{{ $perjalanan->nama_bendahara }}</u></strong><br>
                NIP. {{ $perjalanan->nip_bendahara }}
            </td>
            <td>
                Makassar, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Telah menerima jumlah uang sebesar<br>
                Rp {{ number_format($perjalanan->total_estimasi, 0, ',', '.') }}<br><br>
                Yang menerima
                <div class="space-ttd"></div>
                <strong><u>{{ $perjalanan->pegawai->name ?? '-' }}</u></strong><br>
                NIP. {{ $perjalanan->pegawai->nip ?? '-' }}
            </td>
        </tr>
    </table>

    <!-- Perhitungan SPD Rampung -->
    <div style="page-break-inside: avoid; margin-top: 30px;">
        <p style="font-weight: bold; font-size: 11px; margin-bottom: 10px;">PERHITUNGAN SPD RAMPUNG</p>
        <table class="spd-table">
            <tr>
                <td width="30%">Ditetapkan sejumlah</td>
                <td width="5%">:</td>
                <td width="20%">Rp {{ number_format($perjalanan->total_estimasi, 0, ',', '.') }}</td>
                <td width="30%">Yang telah dibayar semula</td>
                <td width="5%">:</td>
                <td width="20%">Rp {{ number_format($perjalanan->total_estimasi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Sisa kurang/lebih</td>
                <td>:</td>
                <td>Rp -</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <table class="signature-table" style="margin-top: 30px;">
            <tr>
                <td width="50%"></td>
                <td width="50%">
                    Pejabat Pembuat Komitmen
                    <div class="space-ttd"></div>
                    <strong><u>{{ $perjalanan->nama_ppk }}</u></strong><br>
                    NIP. {{ $perjalanan->nip_ppk }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>