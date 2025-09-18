<?php

namespace App\Exports;

use Illuminate\Http\Response;

class MasterCoaTemplateExport
{
    public function download()
    {
        $filename = 'template_master_coa_' . date('Y-m-d') . '.csv';

        // Create header only (no example data)
        $data = [
            ['nomor_akun', 'nama_akun', 'tipe_akun', 'saldo']
        ];

        $content = $this->createCsvContent($data);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    private function createCsvContent($data)
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

        return $content;
    }
}
