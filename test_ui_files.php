<?php

echo "=== TESTING NEW PERMISSION MODULES UI FILES ===\n\n";

// Check if new permission modules exist in files
$newModules = [
    'pembayaran-uang-muka',
    'realisasi-uang-muka',
    'pembayaran-ob'
];

echo "1. Checking template files for new permission modules:\n";
echo str_repeat("-", 70) . "\n";

// Check create.blade.php
$createFile = 'resources/views/master-user/create.blade.php';
if (file_exists($createFile)) {
    $content = file_get_contents($createFile);

    echo "create.blade.php:\n";
    foreach ($newModules as $module) {
        if (strpos($content, "permissions[$module]") !== false) {
            echo "  ✅ $module module found\n";
        } else {
            echo "  ❌ $module module NOT found\n";
        }
    }

    // Check for pembayaran header checkbox
    if (strpos($content, 'pembayaran-header-checkbox') !== false) {
        echo "  ✅ pembayaran header checkbox found\n";
    } else {
        echo "  ❌ pembayaran header checkbox NOT found\n";
    }

    // Check for JavaScript functions
    if (strpos($content, 'initializeCheckAllPembayaran') !== false) {
        echo "  ✅ initializeCheckAllPembayaran function found\n";
    } else {
        echo "  ❌ initializeCheckAllPembayaran function NOT found\n";
    }

    // Check for module data-module="pembayaran"
    if (strpos($content, 'data-module="pembayaran"') !== false) {
        echo "  ✅ pembayaran module row found\n";
    } else {
        echo "  ❌ pembayaran module row NOT found\n";
    }

    // Check for data-parent="pembayaran"
    if (strpos($content, 'data-parent="pembayaran"') !== false) {
        echo "  ✅ pembayaran sub-modules found\n";
    } else {
        echo "  ❌ pembayaran sub-modules NOT found\n";
    }
} else {
    echo "  ❌ create.blade.php NOT found\n";
}

echo "\n";

// Check edit.blade.php
$editFile = 'resources/views/master-user/edit.blade.php';
if (file_exists($editFile)) {
    $content = file_get_contents($editFile);

    echo "edit.blade.php:\n";
    foreach ($newModules as $module) {
        if (strpos($content, "permissions[$module]") !== false) {
            echo "  ✅ $module module found\n";
        } else {
            echo "  ❌ $module module NOT found\n";
        }
    }

    // Check for pembayaran header checkbox
    if (strpos($content, 'pembayaran-header-checkbox') !== false) {
        echo "  ✅ pembayaran header checkbox found\n";
    } else {
        echo "  ❌ pembayaran header checkbox NOT found\n";
    }

    // Check for JavaScript functions
    if (strpos($content, 'initializeCheckAllPembayaran') !== false) {
        echo "  ✅ initializeCheckAllPembayaran function found\n";
    } else {
        echo "  ❌ initializeCheckAllPembayaran function NOT found\n";
    }

    // Check for module data-module="pembayaran"
    if (strpos($content, 'data-module="pembayaran"') !== false) {
        echo "  ✅ pembayaran module row found\n";
    } else {
        echo "  ❌ pembayaran module row NOT found\n";
    }

    // Check for data-parent="pembayaran"
    if (strpos($content, 'data-parent="pembayaran"') !== false) {
        echo "  ✅ pembayaran sub-modules found\n";
    } else {
        echo "  ❌ pembayaran sub-modules NOT found\n";
    }
} else {
    echo "  ❌ edit.blade.php NOT found\n";
}

echo "\n2. Detailed module check:\n";
echo str_repeat("-", 70) . "\n";

$files = ['create' => $createFile, 'edit' => $editFile];

foreach ($files as $type => $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        echo "$type.blade.php modules:\n";

        foreach ($newModules as $module) {
            $count = substr_count($content, "permissions[$module]");
            echo "  - $module: $count occurrences\n";
        }
        echo "\n";
    }
}

echo "3. JavaScript functions check:\n";
echo str_repeat("-", 70) . "\n";

foreach ($files as $type => $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        echo "$type.blade.php JavaScript:\n";

        // Check pembayaran functions
        if (strpos($content, 'initializeCheckAllPembayaran') !== false) {
            echo "  ✅ initializeCheckAllPembayaran function\n";
        } else {
            echo "  ❌ initializeCheckAllPembayaran function missing\n";
        }

        if (strpos($content, 'updatePembayaranHeaderCheckboxes') !== false) {
            echo "  ✅ updatePembayaranHeaderCheckboxes function\n";
        } else {
            echo "  ❌ updatePembayaranHeaderCheckboxes function missing\n";
        }

        if (strpos($content, 'pembayaran-header-checkbox') !== false) {
            echo "  ✅ pembayaran header checkbox class\n";
        } else {
            echo "  ❌ pembayaran header checkbox class missing\n";
        }
        echo "\n";
    }
}

echo "=== UI TEMPLATE TEST COMPLETED ===\n";
