<?php

namespace App\Livewire\Concerns;

trait WithSwal
{
    /**
     * Dispatch notifikasi sukses.
     * Akan di-handle oleh resources/js/notifications.js
     */
    protected function swalSuccess(string $text, string $title = 'Berhasil!'): void
    {
        $this->dispatch('swal:success', title: $title, text: $text);
    }

    /**
     * Dispatch notifikasi error.
     */
    protected function swalError(string $text, string $title = 'Gagal!'): void
    {
        $this->dispatch('swal:error', title: $title, text: $text);
    }

    /**
     * Dispatch notifikasi warning.
     */
    protected function swalWarning(string $text, string $title = 'Peringatan'): void
    {
        $this->dispatch('swal:warning', title: $title, text: $text);
    }

    /**
     * Dispatch notifikasi info.
     */
    protected function swalInfo(string $text, string $title = 'Informasi'): void
    {
        $this->dispatch('swal:info', title: $title, text: $text);
    }

    /**
     * Dispatch konfirmasi (butuh interaksi user).
     */
    protected function swalConfirm(
        string $text,
        string $method,
        array $params = [],
        string $title = 'Konfirmasi',
        string $confirmText = 'Ya, Lanjutkan',
        string $cancelText = 'Batal',
    ): void {
        $this->dispatch('swal:confirm', [
            'title' => $title,
            'text' => $text,
            'icon' => 'warning',
            'confirmButtonText' => $confirmText,
            'cancelButtonText' => $cancelText,
            'method' => $method,
            'params' => $params,
        ]);
    }
}