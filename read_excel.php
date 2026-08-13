<?php
require 'vendor/autoload.php';
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('C:/folder_joki/aypsis/aypsis/ALAMAT BARANG (NON FAKTUR, AYP & NON AYP) TERBARU.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray(null, true, true, true);
print_r(array_slice($data, 0, 5));
