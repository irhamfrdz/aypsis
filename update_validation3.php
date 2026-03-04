<?php
$file = 'app/Http/Controllers/BiayaKapalController.php';
$content = file_get_contents($file);

$search = <<<EOD
            'lolo_sections.*.subtotal' => 'nullable|numeric|min:0',
            'lolo_sections.*.pph' => 'nullable|numeric|min:0',
            'lolo_sections.*.total_biaya' => 'nullable|numeric|min:0',

            // Labuh tambat sections validation
EOD;

$replace = <<<EOD
            'lolo_sections.*.subtotal' => 'nullable|numeric|min:0',
            'lolo_sections.*.pph' => 'nullable|numeric|min:0',
            'lolo_sections.*.total_biaya' => 'nullable|numeric|min:0',

            // Storage sections validation
            'storage_sections' => 'nullable|array',
            'storage_sections.*.kapal' => 'nullable|string|max:255',
            'storage_sections.*.voyage' => 'nullable|string|max:255',
            'storage_sections.*.lokasi' => 'nullable|string|max:255',
            'storage_sections.*.vendor' => 'nullable|string|max:255',
            'storage_sections.*.kontainer' => 'nullable|array',
            'storage_sections.*.kontainer.*.bl_id' => 'nullable|numeric',
            'storage_sections.*.kontainer.*.hari' => 'nullable|numeric|min:1',
            'storage_sections.*.subtotal' => 'nullable|numeric|min:0',
            'storage_sections.*.pph' => 'nullable|numeric|min:0',
            'storage_sections.*.total_biaya' => 'nullable|numeric|min:0',

            // Labuh tambat sections validation
EOD;

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
?>
