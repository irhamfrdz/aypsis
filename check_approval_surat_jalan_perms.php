<?php

echo "Available approval-surat-jalan permissions:\n";
$permissions = App\Models\Permission::where('name', 'like', 'approval-surat-jalan%')->pluck('name')->toArray();
foreach ($permissions as $permission) {
    echo "- $permission\n";
}

echo "\nTotal: " . count($permissions) . " permissions\n";