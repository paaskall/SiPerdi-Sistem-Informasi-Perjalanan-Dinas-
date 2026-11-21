<?php

namespace App\Support;

class Rupiah
{
    /**
     * Format angka menjadi string Rupiah.
     * Contoh: 1500000 -> "Rp 1.500.000"
     */
    public static function format(float|int|null $value, bool $withPrefix = true): string
    {
        $formatted = number_format((float) ($value ?? 0), 0, ',', '.');

        return $withPrefix ? "Rp {$formatted}" : $formatted;
    }
}