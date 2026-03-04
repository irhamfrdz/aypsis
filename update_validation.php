<?php
$file = 'app/Http/Controllers/BiayaKapalController.php';
$content = file_get_contents($file);

$search = <<<EOD
            // LOLO sections validation
            'lolo_sections' => 'nullable|array',
            'lolo_sections.*.kapal' => 'nullable|string|max:255',
            'lolo_sections.*.voyage' => 'nullable|string|max:255',
            'lolo_sections.*.lokasi' => 'nullable|string|max:255',
            'lolo_sections.*.vendor' => 'nullable|string|max:255',
            'lolo_sections.*.kontainer' => 'nullable|array',
            'lolo_sections.*.kontainer.*.bl_id' => 'nullable|numeric',
            'lolo_sections.*.subtotal' => 'nullable|numeric|min:0',
            'lolo_sections.*.pph' => 'nullable|numeric|min:0',
            'lolo_sections.*.total_biaya' => 'nullable|numeric|min:0',
EOD;

$replace = $search . <<<EOD


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
EOD;

if (strpos($content, "'storage_sections' => 'nullable|array',") === false) {
    $content = str_replace($search, $replace, $content);
    file_put_contents($file, $content);
    echo "Validation added";
} else {
    echo "Already exists";
}

?>
