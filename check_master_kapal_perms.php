<?php

echo "Available master-kapal permissions:\n";
$permissions = App\Models\Permission::where('name', 'like', 'master-kapal%')->pluck('name')->toArray();
foreach ($permissions as $permission) {
    echo "- $permission\n";
}

echo "\nTotal: " . count($permissions) . " permissions\n";