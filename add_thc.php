<?php
$file = __DIR__ . '/resources/views/biaya-kapal/create.blade.php';
$content = file_get_contents($file);

// Replace to add toggle logic
$content = str_replace(
    "clearAllStuffingSections();",
    "clearAllStuffingSections();\n            if (thcWrapper) thcWrapper.classList.add('hidden');\n            clearAllThcSections();",
    $content
);

// Add the else if block for thc inside jenisBiayaSelect change listener
$stuffingBlock = <<<EOD
        // Show Stuffing fields if "Biaya Stuffing" is selected
        else if (selectedText.toLowerCase().includes('stuffing')) {
            // Show Stuffing multi kapal wrapper
            if (stuffingWrapper) stuffingWrapper.classList.remove('hidden');
            initializeStuffingSections();
            
            // Hide standard kapal/voyage/bl fields
            kapalWrapper.classList.add('hidden');
            voyageWrapper.classList.add('hidden');
            blWrapper.classList.add('hidden');
            clearKapalSelections();
            clearVoyageSelections();
            clearBlSelections();

            // Hide other standard fields
            if(nominalWrapper) nominalWrapper.classList.add('hidden');
            if(penerimaWrapper) penerimaWrapper.classList.add('hidden');
            if(namaVendorWrapper) namaVendorWrapper.classList.add('hidden');
            if(nomorRekeningWrapper) nomorRekeningWrapper.classList.add('hidden');
            
            // Remove required attributes
            if(nominalInput) nominalInput.removeAttribute('required');
            if(penerimaInput) penerimaInput.removeAttribute('required');
            
            // Hide other type-specific fields
            vendorWrapper.classList.add('hidden');
            if (vendorSelect) vendorSelect.value = '';
            barangWrapper.classList.add('hidden');
            clearAllKapalSections();
            if (airWrapper) airWrapper.classList.add('hidden');
            clearAllAirSections();
            ppnWrapper.classList.add('hidden');
            pphWrapper.classList.add('hidden');
            totalBiayaWrapper.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            sisaPembayaranWrapper.classList.add('hidden');
            biayaMateraiWrapper.classList.add('hidden');
            pphDokumenWrapper.classList.add('hidden');
            grandTotalDokumenWrapper.classList.add('hidden');
            
            // Reset values
            ppnInput.value = '0';
            pphInput.value = '0';
            totalBiayaInput.value = '';
            dpInput.value = '0';
            sisaPembayaranInput.value = '0';

            // Hide TKBM wrapper
            if (document.getElementById('tkbm_wrapper')) {
                document.getElementById('tkbm_wrapper').classList.add('hidden');
                clearAllTkbmSections();
            }
            
            // Hide Operasional wrapper
            if (operasionalWrapper) operasionalWrapper.classList.add('hidden');
            clearAllOperasionalSections();

            // Hide Trucking wrapper
            if (truckingWrapper) truckingWrapper.classList.add('hidden');
            clearAllTruckingSections();
        }
EOD;

$thcBlock = str_replace(
    ['Stuffing', 'stuffing'],
    ['THC', 'thc'],
    $stuffingBlock
);

if (strpos($content, "selectedText.toLowerCase().includes('thc')") === false) {
    $content = str_replace($stuffingBlock, $stuffingBlock . "\n" . $thcBlock, $content);
}


// Now duplicate the STUFFING SECTIONS MANAGEMENT block
// We need to extract the entire block using regex

$pattern = '/\/\/ ============= STUFFING SECTIONS MANAGEMENT =============.+?(?=\/\/ ============= PERLENGKAPAN SECTIONS MANAGEMENT =============)/s';

if (preg_match($pattern, $content, $matches)) {
    $stuffingJsBlock = $matches[0];
    
    // Create the thc js block by replacing stuffing with thc
    $thcJsBlock = str_replace(
        ['Stuffing', 'stuffing', 'STUFFING'],
        ['THC', 'thc', 'THC'],
        $stuffingJsBlock
    );
    
    // Fix color palette: rose -> teal
    $thcJsBlock = str_replace(
        ['rose'],
        ['teal'],
        $thcJsBlock
    );

    if (strpos($content, "THC SECTIONS MANAGEMENT") === false) {
        $content = str_replace($stuffingJsBlock, $stuffingJsBlock . "\n" . $thcJsBlock, $content);
    }
}

file_put_contents($file, $content);
echo "Done";
