<?php

namespace App\Enums;

enum Role: string
{
    case PEGAWAI = 'pegawai';
    case KEUANGAN = 'keuangan';
    case PIMPINAN = 'pimpinan';
    case SUPER_ADMIN = 'super-admin';

    public function label(): string
    {
        return match ($this) {
            self::PEGAWAI => 'Pegawai',
            self::KEUANGAN => 'Keuangan',
            self::PIMPINAN => 'Pimpinan',
            self::SUPER_ADMIN => 'Super Admin',
        };
    }

    /** @return array<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}