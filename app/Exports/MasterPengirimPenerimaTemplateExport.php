<?php

namespace App\Exports;

class MasterPengirimPenerimaTemplateExport
{
    public function download()
    {
        $filename = 'template_master_pengirim_penerima_' . date('Y-m-d') . '.csv';

        // Create header only (no example data)
        $data = [
            ['nama', 'alamat', 'npwp']
        ];

        return $this->createCsvFile($data, $filename);
    }

    private function createCsvFile($data, $filename)
    {
        // Add UTF-8 BOM to ensure Excel recognizes encoding properly
        $content = "\xEF\xBB\xBF";

        foreach ($data as $row) {
            $content .= implode(';', array_map(function($cell) {
                // Escape quotes and wrap in quotes if contains semicolon, quote, or newline
                if (strpos($cell, ';') !== false || strpos($cell, '"') !== false || strpos($cell, "\n") !== false) {
                    return '"' . str_replace('"', '""', $cell) . '"';
                }
                return $cell;
            }, $row)) . "\r\n";
        }

        return response($content)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
