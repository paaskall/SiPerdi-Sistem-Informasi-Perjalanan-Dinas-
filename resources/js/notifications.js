/**
 * Handler global untuk notifikasi SweetAlert dari Livewire.
 * Menggantikan duplikasi di app.blade.php, super-admin.blade.php, guest.blade.php, flash.blade.php
 */

const ensureSwal = () => {
    if (typeof Swal === 'undefined') {
        console.warn('[notifications] SweetAlert2 belum dimuat.');
        return false;
    }
    return true;
};

const normalizePayload = (payload) => {
    if (Array.isArray(payload)) return payload[0] ?? {};
    return payload ?? {};
};

const toast = (icon, fallbackTitle) => (payload) => {
    if (!ensureSwal()) return;
    const config = normalizePayload(payload);

    Swal.fire({
        toast: true,
        position: 'top',
        icon: config.icon || icon,
        title: config.title || fallbackTitle,
        text: config.text || '',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'font-sans rounded-xl' },
        ...config,
    });
};

const modal = (icon, fallbackTitle) => (payload) => {
    if (!ensureSwal()) return;
    const config = normalizePayload(payload);

    Swal.fire({
        icon: config.icon || icon,
        title: config.title || fallbackTitle,
        text: config.text || '',
        confirmButtonColor: '#1e3a8a',
        ...config,
    });
};

const confirm = (payload) => {
    if (!ensureSwal()) return;
    const config = normalizePayload(payload);

    Swal.fire({
        title: config.title || 'Konfirmasi',
        text: config.text || 'Yakin ingin melanjutkan?',
        icon: config.icon || 'warning',
        showCancelButton: true,
        confirmButtonColor: config.confirmButtonColor || '#1e3a8a',
        cancelButtonColor: '#64748b',
        confirmButtonText: config.confirmButtonText || 'Ya',
        cancelButtonText: config.cancelButtonText || 'Batal',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed && config.method) {
            Livewire.dispatch(config.method, config.params ?? {});
        }
    });
};

/**
 * Bootstrap semua listener.
 * Dipanggil sekali dari app.js setelah Livewire loaded.
 */
export function bootstrapNotifications() {
    document.addEventListener('livewire:init', () => {
        // Toast (auto-dismiss)
        Livewire.on('swal:success', toast('success', 'Berhasil!'));
        Livewire.on('swal:error', toast('error', 'Terjadi kesalahan.'));
        Livewire.on('swal:warning', toast('warning', 'Peringatan.'));
        Livewire.on('swal:info', toast('info', 'Informasi.'));

        // Modal (butuh klik OK)
        Livewire.on('swal', modal('info', 'Informasi'));

        // Konfirmasi (butuh interaksi user)
        Livewire.on('swal:confirm', confirm);
    });

    // Flash message dari Laravel session (setelah redirect)
    document.addEventListener('DOMContentLoaded', () => {
        if (!ensureSwal()) return;

        const flash = window.__flashMessages ?? {};

        if (flash.message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: flash.message,
                timer: 3000,
                showConfirmButton: false,
            });
        }

        if (flash.error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: flash.error,
                confirmButtonColor: '#1e3a8a',
            });
        }

        if (flash.status) {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: flash.status,
                confirmButtonColor: '#1e3a8a',
                confirmButtonText: 'OK',
            });
        }
    });
}