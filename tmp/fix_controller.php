<?php
$content = file_get_contents('app/Http/Controllers/TandaTerimaTanpaSuratJalanController.php');

// Replacement sets
$content = str_replace('class TandaTerimaTanpaSuratJalanController', 'class TandaTerimaTanpaSuratJalanBatamController', $content);
// Prevent replacing TandaTerimaTanpaSuratJalanExport
$content = str_replace('TandaTerimaTanpaSuratJalanExport', 'TEMP_EXPORT_PLACEHOLDER', $content);
$content = str_replace('TandaTerimaTanpaSuratJalan', 'TandaTerimaTanpaSuratJalanBatam', $content);
$content = str_replace('TEMP_EXPORT_PLACEHOLDER', 'TandaTerimaTanpaSuratJalanExport', $content);
$content = str_replace('tanda-terima-tanpa-surat-jalan', 'tanda-terima-tanpa-surat-jalan-batam', $content);

file_put_contents('app/Http/Controllers/TandaTerimaTanpaSuratJalanBatamController.php', $content);
echo 'Controller cloned with all variables list!';
