<?php
$file = __DIR__ . '/resources/views/biaya-kapal/create.blade.php';
$content = file_get_contents($file);

// Extract the STUFFING SECTIONS MANAGEMENT block using regex up to </script>
$pattern = '/\/\/ ============= STUFFING SECTIONS MANAGEMENT =============.+?(?=<\/script>)/s';

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
        $content = str_replace($stuffingJsBlock, $stuffingJsBlock . "\n\n" . $thcJsBlock, $content);
        file_put_contents($file, $content);
        echo "Successfully added THC logic block.\n";
    } else {
        echo "THC logic block already exists.\n";
    }
} else {
    echo "Could not find STUFFING SECTIONS MANAGEMENT.\n";
}
