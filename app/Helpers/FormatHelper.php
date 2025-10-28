<?php

if (!function_exists('format_volume')) {
    /**
     * Format volume dengan smart formatting
     * Menghilangkan trailing zero dan format desimal yang tidak perlu
     * 
     * @param float|null $value
     * @param int $decimals
     * @return string
     */
    function format_volume($value, $decimals = 3) {
        if ($value === null || $value === '') {
            return '-';
        }
        
        if ($value == 0) {
            return '0';
        }
        
        // Format dengan desimal yang diinginkan
        $formatted = number_format($value, $decimals, '.', '');
        
        // Hilangkan trailing zeros dan titik desimal jika tidak ada decimal
        return rtrim(rtrim($formatted, '0'), '.');
    }
}

if (!function_exists('format_weight')) {
    /**
     * Format berat dengan smart formatting
     * Menghilangkan trailing zero dan format desimal yang tidak perlu
     * 
     * @param float|null $value
     * @param int $decimals
     * @return string
     */
    function format_weight($value, $decimals = 2) {
        if ($value === null || $value === '') {
            return '-';
        }
        
        if ($value == 0) {
            return '0';
        }
        
        // Format dengan desimal yang diinginkan
        $formatted = number_format($value, $decimals, '.', '');
        
        // Hilangkan trailing zeros dan titik desimal jika tidak ada decimal
        return rtrim(rtrim($formatted, '0'), '.');
    }
}