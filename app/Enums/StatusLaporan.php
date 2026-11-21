<?php

namespace App\Enums;

enum StatusLaporan: string
{
    case SUBMITTED = 'SUBMITTED';
    case VERIFIED = 'VERIFIED';
    case REJECTED = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Menunggu Verifikasi',
            self::VERIFIED => 'Terverifikasi',
            self::REJECTED => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::SUBMITTED => 'bg-amber-50 text-amber-700 ring-amber-200',
            self::VERIFIED => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::REJECTED => 'bg-red-50 text-red-700 ring-red-200',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SUBMITTED => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            self::VERIFIED => 'M5 13l4 4L19 7',
            self::REJECTED => 'M6 18L18 6M6 6l12 12',
        };
    }

    public function isPending(): bool
    {
        return $this === self::SUBMITTED;
    }

    /** @return array<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}