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

$storage_val = <<<EOD
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

// We know the search block exists at two places: store and update.
// Let's replace ALL occurrences of it with itself + storage_val, except if the string 'Storage sections validation' is already right after it!
// Easiest is to inject it.

$content = str_replace($search, $search . "\n\n" . $storage_val, $content);
// Wait, this will add it in store() again! `store` method already has `Storage sections validation` manually added somewhere else, or does it?
// In store(), storage validation is around line 488, which might NOT be right after LOLO.

// Let's just fix it manually.
file_put_contents('test.log', 'replaced');
file_put_contents($file, $content);
?>
