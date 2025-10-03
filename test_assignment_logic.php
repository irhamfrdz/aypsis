<?php
// Direct test assignment

// Create fake request data
$testData = [
    'permissions' => [
        'approval-tugas-1' => [
            'view' => '1',
            'approve' => '1'
        ],
        'approval-tugas-2' => [
            'view' => '1'
        ]
    ]
];

echo "=== Testing Direct Assignment ===\n";

// Create permission records directly
$permissions = [
    'approval-tugas-1.view',
    'approval-tugas-1.approve',
    'approval-tugas-2.view',
    'approval-tugas-2.approve'
];

// Show the test matrix data
echo "Test form data:\n";
print_r($testData);

echo "\nExpected permission names:\n";
foreach ($permissions as $perm) {
    echo "- $perm\n";
}

echo "\nExpected behavior:\n";
echo "1. Form sends: permissions[approval-tugas-1][view] = '1'\n";
echo "2. Controller convertMatrixPermissionsToIds() should look for 'approval-tugas-1.view'\n";
echo "3. Permission should be found in database and ID returned\n";
echo "4. sync() should save the permission to user\n";

echo "\n=== Test Complete ===\n";
