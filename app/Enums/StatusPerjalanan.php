<?php

namespace App\Enums;

enum StatusPerjalanan: string
{
    case DRAFT = 'DRAFT';
    case PRINTED = 'PRINTED';
    case RECEIVED_BY_FINANCE = 'RECEIVED_BY_FINANCE';
    case PIMPINAN_SIGNED = 'PIMPINAN_SIGNED';
    case REJECTED = 'REJECTED';
    case COMPLETED = 'COMPLETED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PRINTED => 'Sudah Dicetak',
            self::RECEIVED_BY_FINANCE => 'Diterima Keuangan',
            self::PIMPINAN_SIGNED => 'Disetujui Pimpinan',
            self::REJECTED => 'Ditolak',
            self::COMPLETED => 'Selesai',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-slate-100 text-slate-700 ring-slate-200',
            self::PRINTED => 'bg-amber-50 text-amber-700 ring-amber-200',
            self::RECEIVED_BY_FINANCE => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::PIMPINAN_SIGNED => 'bg-blue-50 text-blue-700 ring-blue-200',
            self::REJECTED => 'bg-red-50 text-red-700 ring-red-200',
            self::COMPLETED => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            self::PRINTED => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z',
            self::RECEIVED_BY_FINANCE, self::PIMPINAN_SIGNED, self::COMPLETED => 'M5 13l4 4L19 7',
            self::REJECTED => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }

    public function canBeEdited(): bool
    {
        return $this === self::DRAFT;
    }

    public function canBeReported(): bool
    {
        return in_array($this, [
            self::PRINTED,
            self::RECEIVED_BY_FINANCE,
            self::PIMPINAN_SIGNED,
            self::COMPLETED,
        ], true);
    }

    public function canBeVerifiedByFinance(): bool
    {
        return $this === self::PRINTED;
    }

    /** @return array<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** @return array<string> Status yang boleh dibuatkan laporan */
    public static function reportableValues(): array
    {
        return array_values(array_map(
            fn (self $s) => $s->value,
            array_filter(self::cases(), fn (self $s) => $s->canBeReported())
        ));
    }
}