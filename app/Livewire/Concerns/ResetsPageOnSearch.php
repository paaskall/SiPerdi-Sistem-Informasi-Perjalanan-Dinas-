<?php

namespace App\Livewire\Concerns;

/**
 * Otomatis reset pagination saat property $search berubah.
 * Menggantikan pattern berulang:
 *   public function updatingSearch() { $this->resetPage(); }
 */
trait ResetsPageOnSearch
{
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
}