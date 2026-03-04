<?php
$file = 'resources/views/biaya-kapal/edit.blade.php';
$content = file_get_contents($file);

// Replace any occurrence of clearing typical sections with clearing storage as well
$search = "clearAllLoloSections();";
$replace = "if (storageWrapper) storageWrapper.classList.add('hidden');\n            clearAllStorageSections();\n            " . $search;

// but wait, there are places where lolo is cleared but we also want to clear storage.
// A simpler way is to just inject clear storage anywhere clearAllKapalSections() or clearAllOperasionalSections() or clearAllLoloSections() is called, UNLESS it's inside the storage block itself.
// Let's just find `else if (` and inject it inside the body if not already there?
// We can just find all `else {` and `else if (` inside updateVisibility and if it's not the storage condition, add storage clear.

// Actually, in `edit.blade.php` around line 1762, at the beginning of `updateVisibility()`, we can just hide storage before the big `if/else`.
// Wait, updateVisibility() doesn't hide ALL sections at the top. It explicitly hides unneeded sections in each IF branch.
// Let's just do a regex replace to insert `if (storageWrapper) storageWrapper.classList.add('hidden'); clearAllStorageSections();` right before ANY `// Hide Nama Kapal and Nomor Voyage fields` inside the `updateVisibility` function.

$search = '// Hide Nama Kapal and Nomor Voyage fields';
$replace = "if (storageWrapper) storageWrapper.classList.add('hidden');\n            clearAllStorageSections();\n\n            " . $search;

$content = str_replace($search, $replace, $content);

// And also search for `// Show PPN/PPH fields for Biaya Penumpukan`
$search2 = '// Show PPN/PPH fields for Biaya Penumpukan';
$replace2 = "if (storageWrapper) storageWrapper.classList.add('hidden');\n            clearAllStorageSections();\n\n            " . $search2;
$content = str_replace($search2, $replace2, $content);

file_put_contents($file, $content);
echo "Cleanup Done";
?>
