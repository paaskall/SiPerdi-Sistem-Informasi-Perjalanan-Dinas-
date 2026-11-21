<?php

namespace App\Livewire\Concerns;

use App\Support\LayoutResolver;

trait WithRoleLayout
{
    /**
     * Resolve nama layout berdasarkan role user yang login.
     * Menggantikan pattern berulang:
     *   ->layout(auth()->user()->hasRole('super-admin') ? 'layouts.super-admin' : 'layouts.app')
     */
    protected function resolveLayout(): string
    {
        return LayoutResolver::for(auth()->user());
    }
}