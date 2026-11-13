<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Perjalanan Dinas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; margin: 0; padding: 0; }
        
        /* Kotak luar utama */
        .container { border: 2px solid #000; padding: 15px; margin: 10px; position: relative; height: 90%; }
        
        /* Kop Surat */
        .header { text-align: center; margin-bottom: 10px; }
        .header h2 { font-size: 13px; margin: 0; font-weight: bold; }
        
        .title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 15px; text-decoration: underline; }
        
        /* Tabel tanpa border */
        .layout-table { width: 100%; border-collapse: collapse; }
        .layout-table td { padding: 3px 0; vertical-align: top; }
        
        /* Kotak Nominal */
        .amount-box { border: 1px solid #000; background: #f8f9fa; padding: 10px; margin: 15px 0; font-size: 14px; font-weight: bold; text-align: center; }
        
        /* TTD */
        .signature-table { width: 100%; margin-top: 15px; border-collapse: collapse; }
        .signature-table td { text-align: center; width: 50%; vertical-align: top; }
        .space-ttd { height: 50px; }
    </style>
</head>
<body>

    <div class="container">
        <!-- Kop Surat -->
        <div class="header">
            <h2>LEMBAGA ADMINISTRASI NEGARA PUSLATBANG KMP</h2>
        </div>

        <!-- Judul -->
        <div class="title">KUITANSI</div>

        <!-- Info Kiri -->
        <table class="layout-table">
            <tr>
                <td width="15%">No.</td>
                <td width="2%">:</td>
                <td width="43%">{{ $perjalanan->nomor_pengajuan }}</td>
                <td width="10%">M.A</td>
                <td width="2%">:</td>
                <td width="28%">{{ $perjalanan->akun }}</td>
            </tr>
            <tr>
                <td>Sudah terima dari</td>
                <td>:</td>
                <td>Kepala Puslatbang KMP</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td>Uang sejumlah</td>
                <td>:</td>
                <td colspan="4"><strong>{{ ucwords(\App\Helpers\TerbilangHelper::convert($perjalanan->total_estimasi)) }} Rupiah</strong></td>
            </tr>
            <tr>
                <td>Guna kelancaran uang muka/kekurangan ongkos Perjalanan Dinas menurut Surat Perintah dari :</td>
                <td>:</td>
                <td colspan="4">Kepala/Pejabat Pembuat Komitmen Puslatbang KMP</td>
            </tr>
        </table>

        <!-- Info Tengah -->
        <table class="layout-table" style="margin-top: 10px;">
            <tr>
                <td width="15%">Tanggal</td>
                <td width="2%">:</td>
                <td width="43%">{{ \Carbon\Carbon::parse($perjalanan->tanggal_berangkat)->translatedFormat('d F Y') }}</td>
                <td width="40%" class="text-right">Makassar, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Nomor</td>
                <td>:</td>
                <td>{{ $perjalanan->no_spd }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Untuk Perjalanan Dinas dari</td>
                <td>:</td>
                <td colspan="2">{{ $perjalanan->tempat_berangkat }} - {{ $perjalanan->kotaTujuan->nama ?? '-' }} PP</td>
            </tr>
        </table>

        <hr style="border: 1px solid #000; margin: 10px 0;">

        <!-- Perincian Kiri & Nama Kanan -->
        <table class="layout-table">
            <tr>
                <td width="50%" style="vertical-align: top;">
                    <p style="font-weight: bold; text-decoration: underline;">PERINCIAN</p>
                    <table width="100%">
                        @php
                            $kategori = ['tiket' => 0, 'harian' => 0, 'hotel' => 0, 'lain' => 0];
                            foreach($perjalanan->rincianBiaya as $item) {
                                $teks = strtolower(($item->coa->nama ?? '') . ' ' . $item->keterangan);
                                if(str_contains($teks, 'tiket') || str_contains($teks, 'pesawat')) $kategori['tiket'] += $item->nominal;
                                elseif(str_contains($teks, 'hotel') || str_contains($teks, 'penginapan')) $kategori['hotel'] += $item->nominal;
                                elseif(str_contains($teks, 'harian') || str_contains($teks, 'saku')) $kategori['harian'] += $item->nominal;
                                else $kategori['lain'] += $item->nominal;
                            }
                        @endphp
                        <tr><td>Ongkos Ticket</td><td class="text-right">Rp {{ number_format($kategori['tiket'], 0, ',', '.') }}</td></tr>
                        <tr><td>Uang Harian</td><td class="text-right">Rp {{ number_format($kategori['harian'], 0, ',', '.') }}</td></tr>
                        <tr><td>Hotel</td><td class="text-right">Rp {{ number_format($kategori['hotel'], 0, ',', '.') }}</td></tr>
                        <tr><td>Lain-Lain</td><td class="text-right">Rp {{ number_format($kategori['lain'], 0, ',', '.') }}</td></tr>
                        <tr><td colspan="2"><br></td></tr>
                        <tr><td>Jumlah ....................</td><td class="text-right">Rp {{ number_format($perjalanan->total_estimasi, 0, ',', '.') }}</td></tr>
                        <tr><td>Uang Muka</td><td class="text-right">Rp -</td></tr>
                        <tr><td style="font-weight:bold;">Jumlah yang diterima</td><td class="text-right" style="font-weight:bold;">Rp {{ number_format($perjalanan->total_estimasi, 0, ',', '.') }}</td></tr>
                    </table>
                </td>
                <td width="50%" style="vertical-align: top; padding-left: 20px;">
                    <table width="100%">
                        <tr><td width="30%">Nama</td><td width="5%">:</td><td>{{ $perjalanan->pegawai->name ?? '-' }}</td></tr>
                        <tr><td>Pangkat/Jabatan</td><td>:</td><td>{{ $perjalanan->pegawai->pangkat->nama ?? '-' }} / {{ $perjalanan->pegawai->jabatan->nama ?? '-' }}</td></tr>
                        <tr><td>Tempat Kedudukan</td><td>:</td><td>{{ $perjalanan->tempat_berangkat }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <hr style="border: 1px solid #000; margin: 10px 0;">

        <!-- Tanda Tangan Bawah -->
        <table class="signature-table">
            <tr>
                <td width="33%">
                    SETUJU DIBAYAR:<br><br>
                    LUNAS PADA TANGGAL<br><br><br><br>
                    Bendahara Pengeluaran,<br><br><br><br>
                    <strong><u>{{ $perjalanan->nama_bendahara }}</u></strong><br>
                    NIP. {{ $perjalanan->nip_bendahara }}
                </td>
                <td width="33%">
                    LEMBAGA ADMINISTRASI NEGARA PUSLATBANG KMP<br>
                    Kepala/Pejabat Pembuat Komitmen<br><br><br><br><br><br>
                    <strong><u>{{ $perjalanan->nama_ppk }}</u></strong><br>
                    NIP. {{ $perjalanan->nip_ppk }}
                </td>
                <td width="33%">
                    atasan Langsung Bendaharawan,<br><br><br><br><br><br><br>
                    <strong><u>............................................</u></strong><br>
                    NIP. ............................................
                </td>
            </tr>
        </table>
    </div>

</body>
</html>