<?php

namespace App\Helpers;

class TerbilangHelper
{
    public static function convert($angka)
    {
        $angka = abs($angka);
        $baca = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        $terbilang = "";
        
        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } else if ($angka < 20) {
            $terbilang = self::convert($angka - 10) . " belas";
        } else if ($angka < 100) {
            $terbilang = self::convert($angka / 10) . " puluh" . self::convert($angka % 10);
        } else if ($angka < 200) {
            $terbilang = " seratus" . self::convert($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = self::convert($angka / 100) . " ratus" . self::convert($angka % 100);
        } else if ($angka < 2000) {
            $terbilang = " seribu" . self::convert($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = self::convert($angka / 1000) . " ribu" . self::convert($angka % 1000);
        } else if ($angka < 1000000000) {
            $terbilang = self::convert($angka / 1000000) . " juta" . self::convert($angka % 1000000);
        } else if ($angka < 1000000000000) {
            $terbilang = self::convert($angka / 1000000000) . " milyar" . self::convert(fmod($angka, 1000000000));
        } else if ($angka < 1000000000000000) {
            $terbilang = self::convert($angka / 1000000000000) . " trilyun" . self::convert(fmod($angka, 1000000000000));
        }
        
        return $terbilang;
    }
}