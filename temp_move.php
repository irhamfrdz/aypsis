<?php
$content = file_get_contents(__DIR__ . '/zklibrary_ref.php');
$content = str_replace("<?php", "<?php\n\nnamespace App\Services;\n", $content);
file_put_contents(__DIR__ . '/app/Services/ZKLibrary.php', $content);
echo "Done";
