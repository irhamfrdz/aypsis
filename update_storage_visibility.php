<?php
$file = 'resources/views/biaya-kapal/edit.blade.php';
$content = file_get_contents($file);

// Add hiding logic before LOLO block
$search_lolo = '// Show LOLO wrapper if "Biaya Lolo" is selected';
$replace_lolo = "if (storageWrapper) storageWrapper.classList.add('hidden');\n            clearAllStorageSections();\n\n            " . $search_lolo;
$content = str_replace($search_lolo, $replace_lolo, $content);

// Now construct the Storage block and add it AFTER the LOLO block ends
$storage_block = <<<'JS'
        // Show Storage wrapper if "Storage" is selected
        else if (selectedText.toLowerCase().includes('storage')) {
            if (storageWrapper) storageWrapper.classList.remove('hidden');
            initializeStorageSections();
            
            // Hide standard fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide normal nominal
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(nominalInput) nominalInput.removeAttribute('required');
            
            // Hide other type-specific fields
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            biayaMateraiWrapper.classList.add('hidden');
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            
            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();
            
            // Hide Labuh Tambat wrapper
            if (document.getElementById('labuh_tambat_wrapper')) {
                document.getElementById('labuh_tambat_wrapper').classList.add('hidden');
                clearAllLabuhTambatSections();
            }

            // Hide Stuffing wrapper
            if (stuffingWrapper) stuffingWrapper.classList.add('hidden');
            clearAllStuffingSections();

            // Hide THC wrapper
            if (thcWrapper) thcWrapper.classList.add('hidden');
            clearAllTHCSections();
            
            // Hide LOLO wrapper
            if (loloWrapper) loloWrapper.classList.add('hidden');
            clearAllLoloSections();
        }
JS;

// To insert after LOLO block, let's find the buruh block
$search_buruh = '// Show barang wrapper if "Biaya Buruh" is selected';
$content = str_replace($search_buruh, $storage_block . "\n        " . $search_buruh, $content);

// For all other blocks (buruh, ktkbm, dll, labuh_tambat, trucking, dll), we need to hide storage.
// We can just add `.replace()` for each comment block of the branches.
$branches = [
    '// Show barang wrapper if "Biaya Buruh" is selected',
    '// Show TKBM wrapper if "Biaya KTKBM" is selected',
    '// Show Operasional wrapper if "Biaya Operasional" is selected',
    '// Show Labuh Tambat wrapper if "Penerimaan Labuh Tambat" is selected',
    '// Show Trucking wrapper if "Biaya Trucking" is selected',
    '// Show Stuffing wrapper if "Biaya Stuffing" is selected',
    '// Show dokumen fields',
    '// Show vendor wrapper if "Biaya Dokumen" is selected',
    '// Show OPP/OPT wrapper if "Penerimaan OPP/OPT" is selected',
    '// Show THC wrapper if "Biaya THC" is selected'
];

foreach ($branches as $branch) {
    if (strpos($content, $branch) !== false) {
        $r = "if (storageWrapper) storageWrapper.classList.add('hidden');\n            clearAllStorageSections();\n\n            " . $branch;
        $content = str_replace($branch, $r, $content);
    }
}

file_put_contents($file, $content);
echo "Done";
?>
