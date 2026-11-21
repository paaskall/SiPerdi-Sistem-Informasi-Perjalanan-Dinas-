{{-- Helper JS halaman Riwayat (confirmSave). Listener swal / swal:success / swal:error sudah ada global di notifications.js. --}}
<!-- ========================================== -->
<!-- SWEETALERT (KONFIRMASI & NOTIFIKASI)       -->
<!-- ========================================== -->
<script>
    // Listener swal / swal:success / swal:error sudah didaftarkan global di resources/js/notifications.js,
    // jadi di sini cukup helper konfirmasi simpan.
    if (!window.__umkRiwayatBound) {
        window.__umkRiwayatBound = true;

        window.confirmSave = function (event) {
            if (event) event.preventDefault();
            const form = event && event.target ? event.target.closest('form') : null;

            Swal.fire({
                title: 'Simpan perubahan?',
                text: 'Pastikan data perjalanan dan rincian biaya sudah benar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1e3a8a',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, simpan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'font-sans rounded-2xl' }
            }).then((result) => {
                if (!result.isConfirmed) return;
                if (form) {
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            });
        };
    }
</script>