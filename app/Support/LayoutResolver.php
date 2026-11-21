<?php

namespace App\Support;

use App\Enums\Role;
use App\Models\User;

class LayoutResolver
{
    /**
     * Tentukan nama layout berdasarkan role user.
     * Super admin pakai layout khusus (sidebar), role lain pakai layout default (navbar).
     */
    public static function for(?User $user): string
    {
        if ($user && $user->hasRole(Role::SUPER_ADMIN->value)) {
            return 'layouts.super-admin';
        }

        return 'layouts.app';
    }
}