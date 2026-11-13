<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form UMK - {{ $perjalanan->nomor_pengajuan }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; }
        .kop-surat { text-align: center; border-bottom: 3px double solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h2 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .kop-surat h1 { margin: 0; font-size: 20pt; text-transform: uppercase; }
        .kop-surat p { margin: 0; font-size: 10pt; }
        .judul { text-align: center; margin-bottom: 20px; }
        .judul h3 { margin: 0; text-decoration: underline; font-size: 14pt; }
        .judul p { margin: 0; font-size: 11pt; }
        .tabel-data { width: 100%; margin-bottom: 20px; }
        .tabel-data td { vertical-align: top; padding: 3px; }
        .tabel-biaya { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        .tabel-biaya th, .tabel-biaya td { border: 1px solid black; padding: 6px; text-align: left; }
        .tabel-biaya th { background-color: #f2f2f2; font-weight: bold; }
        .ttd-container { width: 100%; margin-top: 40px; }
        .ttd-box { float: left; text-align: center; width: 50%; }
        .ttd-box-kanan { float: right; text-align: center; width: 50%; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h1>PEMERINTAH KOTA CONTOH</h1>
        <h2>DINAS PERHUBUNGAN</h2>
        <p>Jl. Merdeka No. 123, Telp. (021) 1234567, Email: dinas@contoh.go.id</p>
    </div>

    <div class="judul">
        <h3>FORMULIR PERMOHONAN UANG MUKA KERJA (UMK)</h3>
        <p>Nomor: {{ $perjalanan->nomor_pengajuan }}</p>
    </div>

    <table class="tabel-data">
        <tr>
            <td width="200"><strong>Nama Lengkap</strong></td>
            <td width="10">:</td>
            <td>{{ $perjalanan->pegawai->name }}</td>
        </tr>
        <tr>
            <td><strong>NIP</strong></td>
            <td>:</td>
            <td>{{ $perjalanan->pegawai->nip }}</td>
        </tr>
        <tr>
            <td><strong>Jabatan</strong></td>
            <td>:</td>
            <td>{{ $perjalanan->pegawai->jabatan->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Divisi</strong></td>
            <td>:</td>
            <td>{{ $perjalanan->pegawai->divisi->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Golongan</strong></td>
            <td>:</td>
            <td>{{ $perjalanan->pegawai->golongan->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Pangkat</strong></td>
            <td>:</td>
            <td>{{ $perjalanan->pegawai->pangkat->nama ?? '-' }} {{ $perjalanan->pegawai->pangkat ? '(' . $perjalanan->pegawai->pangkat->golongan_ruang . ')' : '' }}</td>
        </tr>
    </table>

    <hr>

    <table class="tabel-data">
        <tr>
            <td width="200"><strong>Maksud Perjalanan</strong></td>
            <td width="10">:</td>
            <td>{{ $perjalanan->maksud_perjalanan }}</td>
        </tr>
        <tr>
            <td><strong>Kota Tujuan</strong></td>
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

    <div class="ttd-container">
        <div class="ttd-box">
            <p>Pemohon,</p>
            <div style="height: 80px;"></div>
            <p><strong><u>{{ $perjalanan->pegawai->name }}</u></strong><br>
            NIP. {{ $perjalanan->pegawai->nip }}</p>
        </div>
        <div class="ttd-box-kanan">
            <p>Kota Contoh, {{ \Carbon\Carbon::now()->format('d F Y') }}<br>Bagian Keuangan,</p>
            <div style="height: 80px;"></div>
            <p><strong><u>..............................</u></strong><br>
            NIP. ..............................</p>
        </div>
    </div>

</body>
</html>