<?php
$files = [
    'resources/views/bl/rekap_bongkaran_kontainer_select.blade.php',
    'resources/views/bl/rekap_bongkaran_kontainer.blade.php',
    'resources/views/bl/rekap_bongkaran_kontainer_print.blade.php',
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Replace route names
    $content = str_replace('route(\'bl.rekap-bongkaran\')', 'route(\'bl.rekap-bongkaran-kontainer\')', $content);
    $content = str_replace('route(\'bl.rekap-bongkaran.select\')', 'route(\'bl.rekap-bongkaran-kontainer.select\')', $content);
    $content = str_replace('route(\'bl.rekap-bongkaran.print', 'route(\'bl.rekap-bongkaran-kontainer.print', $content);
    
    // Replace Titles
    $content = str_replace('Rekapan Bongkar/Muat Barang', 'Rekapan Bongkar/Muat Kontainer', $content);
    $content = str_replace('Pilih Kapal untuk Rekap Bongkaran', 'Pilih Kapal untuk Rekap Bongkaran Kontainer', $content);
    $content = str_replace('Pilih Kapal & Voyage', 'Pilih Kapal & Voyage Kontainer', $content);

    // Make sure we update form actions
    $content = preg_replace('/action="[^"]*?bl\/rekap-bongkaran"/', 'action="{{ route(\'bl.rekap-bongkaran-kontainer\') }}"', $content);
    $content = preg_replace('/action="{{ route\(\'bl\.rekap-bongkaran\'\) }}"/', 'action="{{ route(\'bl.rekap-bongkaran-kontainer\') }}"', $content);

    file_put_contents($file, $content);
}

echo "Replaced strings in views.";
