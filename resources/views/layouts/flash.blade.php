{{-- resources/views/layouts/flash.blade.php --}}
{{-- Notifikasi SweetAlert dari session flash. Include SETELAH script SweetAlert dimuat. --}}
@php
    $flashStatus = [
        'profile-updated'        => 'Profil berhasil diperbarui.',
        'password-updated'       => 'Kata sandi berhasil diubah.',
        'verification-link-sent' => 'Tautan verifikasi baru telah dikirim ke email Anda.',
    ];
    $status = session('status');
    // Di halaman /profile sudah ada tulisan "Tersimpan." inline, jadi tidak perlu popup ganda.
    $skipStatus = request()->routeIs('profile.edit') && isset($flashStatus[$status]);
@endphp
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swal === 'undefined') return;

    @if (session('message'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: @json(session('message')), timer: 3000, showConfirmButton: false });
    @endif

    @if (session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: @json(session('error')), confirmButtonColor: '#1e3a8a' });
    @endif

    @if ($status && ! $skipStatus)
        @if (isset($flashStatus[$status]))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: @json($flashStatus[$status]), timer: 3000, showConfirmButton: false });
        @else
            Swal.fire({ icon: 'info', title: 'Informasi', text: @json($status), confirmButtonColor: '#1e3a8a', confirmButtonText: 'OK' });
        @endif
    @endif
});
</script>