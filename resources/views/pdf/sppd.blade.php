<!DOCTYPE html>
<html>
<head>
    <title>SPPD - {{ $perjalanan->nomor_pengajuan }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 14px; line-height: 1.5; }
        .kop-surat { text-align: center; border-bottom: 3px double solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .kop-surat h1 { margin: 0; font-size: 22px; text-transform: uppercase; }
        .kop-surat p { margin: 0; font-size: 12px; }
        .judul { text-align: center; margin-bottom: 20px; }
        .judul h3 { margin: 0; text-decoration: underline; font-size: 16px; }
        .judul p { margin: 0; }
        .tabel-data { width: 100%; margin-bottom: 20px; }
        .tabel-data td { vertical-align: top; padding: 4px; }
        .tabel-biaya { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .tabel-biaya th, .tabel-biaya td { border: 1px solid black; padding: 8px; text-align: left; }
        .tabel-biaya th { background-color: #f2f2f2; }
        .ttd-container { width: 100%; margin-top: 40px; }
        .ttd-box { float: right; text-align: center; width: 250px; }
        .ttd-img { height: 80px; margin: 10px 0; }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <h1>PUSAT PEMBELAJARAN STRATEGI KEBIJAKAN MANAJEMEN PEMERINTAHAN</h1>
        <h2>LEMBAGA ADMINISTRASI NEGARA</h2>
        <p>Jl. Baruga No. 123, Telp. (021) 1234567, Email: pusjarskmp@gmail.go.id</p>
    </div>

    <!-- JUDUL SPPD -->
    <div class="judul">
        <h3>SURAT PERINTAH PERJALANAN DINAS (SPPD)</h3>
        <p>Nomor: {{ $perjalanan->nomor_pengajuan }}</p>
    </div>

    <!-- DATA PEGAWAI -->
    <table class="tabel-data">
        <tr>
            <td width="200"><strong>Nama Pegawai</strong></td>
            <td width="10">:</td>
            <td>{{ $perjalanan->pegawai->name }}</td>
        </tr>
        <tr>
            <td><strong>NIP</strong></td>
            <td>:</td>
            <td>{{ $perjalanan->pegawai->nip }}</td>
        </tr>
        <tr>
            <td><strong>Jabatan / Divisi</strong></td>
            <td>:</td>
            <td>{{ $perjalanan->pegawai->jabatan->nama ?? '-' }} / {{ $perjalanan->pegawai->divisi->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Golongan</strong></td>
            <td>:</td>
            <td>{{ $perjalanan->pegawai->golongan->nama ?? '-' }}</td>
        </tr>
    </table>

    <!-- DATA PERJALANAN -->
    <table class="tabel-data">
        <tr>
            <td width="200"><strong>Maksud Perjalanan</strong></td>
            <td width="10">:</td>
            <td>{{ $perjalanan->maksud_perjalanan }}</td>
        </tr>
        <tr>
            <td><strong>Tempat Tujuan</strong></td>
            <td>:</td>
            <td>{{ $perjalanan->kotaTujuan->nama }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Berangkat</strong></td>
            <td>:</td>
            <td>{{ $perjalanan->tanggal_berangkat->format('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Pulang</strong></td>
            <td>:</td>
            <td>{{ $perjalanan->tanggal_pulang->format('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Lama Perjalanan</strong></td>
            <td>:</td>
            <td>{{ $perjalanan->jumlah_hari }} Hari</td>
        </tr>
    </table>

    <!-- RINCIAN BIAYA -->
    <strong>Rincian Estimasi Biaya:</strong>
    <table class="tabel-biaya">
        <thead>
            <tr>
                <th width="50">No</th>
                <th>Keterangan</th>
                <th width="150">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($perjalanan->rincianBiaya as $index => $r)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $r->coa->nama }} - {{ $r->keterangan }}</td>
                    <td>{{ number_format($r->nominal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align: right;"><strong>Total Estimasi</strong></td>
                <td><strong>{{ number_format($perjalanan->total_estimasi, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="ttd-container">
        <div class="ttd-box">
            <p>Makassar, {{ \Carbon\Carbon::now()->format('d F Y') }}<br>Kepala Dinas,</p>
            
            <!-- TAMPILKAN GAMBAR TTD -->
            @if($pimpinan && $pimpinan->ttd_path)
                <img src="{{ public_path('storage/' . $pimpinan->ttd_path) }}" class="ttd-img" alt="TTD">
            @else
                <div style="height: 80px;"></div> <!-- Spasi jika TTD belum diupload -->
            @endif

            <p><strong><u>{{ $pimpinan->name ?? '..............................' }}</u></strong><br>
            NIP. {{ $pimpinan->nip ?? '..............................' }}</p>
        </div>
    </div>

</body>
</html>