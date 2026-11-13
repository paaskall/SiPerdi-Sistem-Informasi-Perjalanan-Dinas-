<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>UMK - {{ $perjalanan->nomor_pengajuan }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.4; }
        .judul { text-align: center; margin-bottom: 20px; }
        .judul h3 { margin: 0; text-decoration: underline; font-size: 14pt; }
        .judul p { margin: 2px 0; }
        .data { margin-bottom: 15px; }
        .data table { width: 100%; }
        .data td { padding: 2px 4px; vertical-align: top; }
        .tabel-biaya { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .tabel-biaya th, .tabel-biaya td { border: 1px solid #000; padding: 5px; font-size: 10pt; }
        .tabel-biaya th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .ttd { width: 100%; margin-top: 30px; }
        .ttd td { text-align: center; vertical-align: top; padding: 10px; }
        .ttd .nama { margin-top: 50px; text-decoration: underline; font-weight: bold; }
    </style>
</head>
<body>

    <div class="judul">
        <h3>UANG MUKA KERJA (UMK)</h3>
        <p>No. Pembukuan: {{ $perjalanan->no_pembukuan }}</p>
        <p>Akun: {{ $perjalanan->akun }}</p>
    </div>

    <div class="data">
        <table>
            <tr><td width="150"><strong>Nama</strong></td><td>: {{ $perjalanan->pegawai->name }}</td></tr>
            <tr><td><strong>NIP</strong></td><td>: {{ $perjalanan->pegawai->nip }}</td></tr>
            <tr><td><strong>Jabatan</strong></td><td>: {{ $perjalanan->pegawai->jabatan->nama ?? '-' }}</td></tr>
            <tr><td><strong>Pangkat/Golongan</strong></td><td>: {{ $perjalanan->pegawai->pangkat->nama ?? '-' }} ({{ $perjalanan->pegawai->golongan->nama ?? '-' }})</td></tr>
            <tr><td><strong>Tempat Berangkat</strong></td><td>: {{ $perjalanan->tempat_berangkat }}</td></tr>
            <tr><td><strong>Tujuan</strong></td><td>: {{ $perjalanan->kotaTujuan->nama }}</td></tr>
            <tr><td><strong>Alat Angkutan</strong></td><td>: {{ $perjalanan->alat_angkutan }}</td></tr>
            <tr><td><strong>Tingkat Biaya</strong></td><td>: {{ $perjalanan->tingkat_biaya }}</td></tr>
            <tr><td><strong>Tanggal Berangkat</strong></td><td>: {{ $perjalanan->tanggal_berangkat->format('d F Y') }}</td></tr>
            <tr><td><strong>Tanggal Pulang</strong></td><td>: {{ $perjalanan->tanggal_pulang->format('d F Y') }}</td></tr>
            <tr><td><strong>Lama Perjalanan</strong></td><td>: {{ $perjalanan->jumlah_hari }} Hari</td></tr>
            <tr><td><strong>Maksud Perjalanan</strong></td><td>: {{ $perjalanan->maksud_perjalanan }}</td></tr>
        </table>
    </div>

    <strong>Rincian Biaya:</strong>
    <table class="tabel-biaya">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Keterangan</th>
                <th width="40">Qty</th>
                <th width="50">Satuan</th>
                <th width="100">Harga Satuan</th>
                <th width="100">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($perjalanan->rincianBiaya as $index => $r)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $r->coa->nama }} - {{ $r->keterangan }}</td>
                    <td class="text-center">{{ $r->qty }}</td>
                    <td class="text-center">{{ $r->satuan }}</td>
                    <td class="text-right">{{ number_format($r->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($r->nominal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-right"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format($perjalanan->total_estimasi, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <table class="ttd">
        <tr>
            <td width="50%">
                <p>Bendahara Pengeluaran,</p>
                <p class="nama">{{ $perjalanan->nama_bendahara }}</p>
                <p>NIP. {{ $perjalanan->nip_bendahara }}</p>
            </td>
            <td width="50%">
                <p>Pejabat Pembuat Komitmen,</p>
                <p class="nama">{{ $perjalanan->nama_ppk }}</p>
                <p>NIP. {{ $perjalanan->nip_ppk }}</p>
            </td>
        </tr>
    </table>

</body>
</html>