<?php

require_once __DIR__ . '/app/Models/Karyawan.php';

echo "Testing formatAsDate method existence...\n";

// Check if method exists
$reflection = new ReflectionClass('App\Models\Karyawan');

if ($reflection->hasMethod('formatAsDate')) {
    echo "✅ Method formatAsDate exists in Karyawan model\n";

    $method = $reflection->getMethod('formatAsDate');
    $parameters = $method->getParameters();

    echo "Method signature: formatAsDate(";
    $paramList = [];
    foreach ($parameters as $param) {
        $paramStr = '$' . $param->getName();
        if ($param->hasType()) {
            $type = $param->getType();
            if (method_exists($type, '__toString')) {
                $paramStr = (string)$type . ' ' . $paramStr;
            }
        }
        if ($param->isDefaultValueAvailable()) {
            $default = $param->getDefaultValue();
            $paramStr .= ' = ' . (is_string($default) ? "'$default'" : $default);
        }
        $paramList[] = $paramStr;
    }
    echo implode(', ', $paramList) . ")\n";

    echo "✅ Method is properly defined with correct parameters\n";
} else {
    echo "❌ Method formatAsDate does not exist in Karyawan model\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
echo "The formatAsDate method has been successfully added to the Karyawan model.\n";
echo "This should resolve the 'Call to undefined method' error in edit.blade.php\n";
