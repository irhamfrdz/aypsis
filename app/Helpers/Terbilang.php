<?php

namespace App\Helpers;

class Terbilang
{
    /**
     * Convert number to Indonesian words
     *
     * @param float|int $number
     * @return string
     */
    public static function make($number)
    {
        $number = abs($number);
        $words = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
            'sepuluh', 'sebelas'
        ];

        if ($number < 12) {
            return $words[$number];
        } elseif ($number < 20) {
            return self::make($number - 10) . ' belas';
        } elseif ($number < 100) {
            return self::make(floor($number / 10)) . ' puluh ' . self::make($number % 10);
        } elseif ($number < 200) {
            return 'seratus ' . self::make($number - 100);
        } elseif ($number < 1000) {
            return self::make(floor($number / 100)) . ' ratus ' . self::make($number % 100);
        } elseif ($number < 2000) {
            return 'seribu ' . self::make($number - 1000);
        } elseif ($number < 1000000) {
            return self::make(floor($number / 1000)) . ' ribu ' . self::make($number % 1000);
        } elseif ($number < 1000000000) {
            return self::make(floor($number / 1000000)) . ' juta ' . self::make($number % 1000000);
        } elseif ($number < 1000000000000) {
            return self::make(floor($number / 1000000000)) . ' miliar ' . self::make($number % 1000000000);
        } elseif ($number < 1000000000000000) {
            return self::make(floor($number / 1000000000000)) . ' triliun ' . self::make($number % 1000000000000);
        }

        return '';
    }
}
