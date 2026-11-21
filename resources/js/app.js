import { bootstrapNotifications } from './notifications';

bootstrapNotifications();

// Ekspos helper logout global (dipakai di layout super-admin & navigation)
window.confirmLogout = function (event) {
    if (event) event.preventDefault();

    if (typeof Swal === 'undefined') {
        if (confirm('Yakin ingin logout?')) {
            document.getElementById('logout-form').submit();
        }
        return;
    }

    Swal.fire({
        title: 'Keluar Aplikasi?',
        text: 'Anda akan keluar dari sistem. Yakin ingin melanjutkan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: { popup: 'font-sans rounded-2xl' },
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sedang logout...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading(),
            });
            document.getElementById('logout-form').submit();
        }
    });
};