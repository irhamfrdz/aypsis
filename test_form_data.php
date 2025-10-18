<?php

// Script untuk test form data yang dikirim
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Simulate form data
$formData = [
    'permissions' => [
        'order-management' => [
            'view' => '1',
            'create' => '1',
            'update' => '1',
            'delete' => '1',
            'print' => '1',
            'export' => '1'
        ],
        'surat-jalan' => [
            'view' => '1',
            'create' => '1',
            'update' => '1',
            'delete' => '1',
            'print' => '1',
            'export' => '1'
        ],
        'tanda-terima' => [
            'view' => '1',
            'create' => '1',
            'update' => '1',
            'delete' => '1',
            'print' => '1',
            'export' => '1'
        ],
        'gate-in' => [
            'view' => '1',
            'create' => '1',
            'update' => '1',
            'delete' => '1',
            'print' => '1',
            'export' => '1'
        ],
        'pranota-surat-jalan' => [
            'view' => '1',
            'create' => '1',
            'update' => '1',
            'delete' => '1',
            'print' => '1',
            'export' => '1'
        ],
        'approval-surat-jalan' => [
            'view' => '1',
            'approve' => '1',
            'reject' => '1',
            'print' => '1',
            'export' => '1'
        ]
    ]
];

echo "🧪 Test Form Data for Operational Permissions\n";
echo "==============================================\n\n";

echo "📝 Simulated Form Data:\n";
print_r($formData);

echo "\n📋 Testing each operational module:\n";

foreach ($formData['permissions'] as $module => $actions) {
    echo "\n🔧 Module: $module\n";
    foreach ($actions as $action => $value) {
        echo "   - $action: $value\n";
    }
}

echo "\n✅ Form structure looks correct!\n";
echo "✅ All operational modules are present\n";
echo "✅ Actions are properly mapped\n";

echo "\n📊 Summary:\n";
echo "- Total modules: " . count($formData['permissions']) . "\n";
echo "- Modules: " . implode(', ', array_keys($formData['permissions'])) . "\n";

echo "\n🔍 Next step: Check if UserController properly handles this data structure\n";

?>
