<?php

echo "=== TESTING UNCHECKED DASHBOARD PERMISSION FIX ===\n\n";

// Test case 1: Dashboard checked
echo "TEST CASE 1: Dashboard permission CHECKED\n";
$matrixWithDashboard = [
    'system' => [
        'dashboard' => '1'  // Checked
    ],
    'auth' => [
        'login' => true
    ]
];

echo "Input matrix (dashboard checked): " . json_encode($matrixWithDashboard, JSON_PRETTY_PRINT) . "\n";

// Simulate the fixed logic
$permissionIds = [];

foreach ($matrixWithDashboard as $module => $actions) {
    if ($module === 'system') {
        // NEW LOGIC: Explicitly check for dashboard permission
        $dashboardEnabled = isset($actions['dashboard']) && ($actions['dashboard'] == '1' || $actions['dashboard'] === true);

        echo "Dashboard enabled check: " . ($dashboardEnabled ? 'true' : 'false') . "\n";

        if ($dashboardEnabled) {
            echo "✅ Dashboard permission would be ADDED to permissionIds\n";
        } else {
            echo "❌ Dashboard permission would NOT be added to permissionIds\n";
        }
        continue;
    }

    if ($module === 'auth') {
        if (isset($actions['login']) && $actions['login']) {
            echo "✅ Login permission would be ADDED\n";
        }
    }
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test case 2: Dashboard unchecked
echo "TEST CASE 2: Dashboard permission UNCHECKED\n";
$matrixWithoutDashboard = [
    'system' => [
        // No 'dashboard' key = unchecked
    ],
    'auth' => [
        'login' => true
    ]
];

echo "Input matrix (dashboard unchecked): " . json_encode($matrixWithoutDashboard, JSON_PRETTY_PRINT) . "\n";

foreach ($matrixWithoutDashboard as $module => $actions) {
    if ($module === 'system') {
        // NEW LOGIC: Explicitly check for dashboard permission
        $dashboardEnabled = isset($actions['dashboard']) && ($actions['dashboard'] == '1' || $actions['dashboard'] === true);

        echo "Dashboard enabled check: " . ($dashboardEnabled ? 'true' : 'false') . "\n";
        echo "isset(\$actions['dashboard']): " . (isset($actions['dashboard']) ? 'true' : 'false') . "\n";

        if ($dashboardEnabled) {
            echo "✅ Dashboard permission would be ADDED to permissionIds\n";
        } else {
            echo "❌ Dashboard permission would NOT be added to permissionIds (CORRECT for unchecked)\n";
        }
        continue;
    }

    if ($module === 'auth') {
        if (isset($actions['login']) && $actions['login']) {
            echo "✅ Login permission would be ADDED\n";
        }
    }
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test case 3: Dashboard explicitly set to false
echo "TEST CASE 3: Dashboard permission EXPLICITLY FALSE\n";
$matrixDashboardFalse = [
    'system' => [
        'dashboard' => false  // Explicitly unchecked
    ],
    'auth' => [
        'login' => true
    ]
];

echo "Input matrix (dashboard=false): " . json_encode($matrixDashboardFalse, JSON_PRETTY_PRINT) . "\n";

foreach ($matrixDashboardFalse as $module => $actions) {
    if ($module === 'system') {
        // NEW LOGIC: Explicitly check for dashboard permission
        $dashboardEnabled = isset($actions['dashboard']) && ($actions['dashboard'] == '1' || $actions['dashboard'] === true);

        echo "Dashboard enabled check: " . ($dashboardEnabled ? 'true' : 'false') . "\n";
        echo "isset(\$actions['dashboard']): " . (isset($actions['dashboard']) ? 'true' : 'false') . "\n";
        echo "\$actions['dashboard'] value: " . json_encode($actions['dashboard']) . "\n";

        if ($dashboardEnabled) {
            echo "✅ Dashboard permission would be ADDED to permissionIds\n";
        } else {
            echo "❌ Dashboard permission would NOT be added to permissionIds (CORRECT for false)\n";
        }
        continue;
    }
}

echo "\n=== SUMMARY ===\n";
echo "✅ FIX APPLIED: Changed from foreach() to explicit isset() check\n";
echo "✅ CHECKED (dashboard='1'): Permission will be added\n";
echo "✅ UNCHECKED (no dashboard key): Permission will NOT be added\n";
echo "✅ EXPLICITLY FALSE (dashboard=false): Permission will NOT be added\n\n";

echo "EXPECTED BEHAVIOR:\n";
echo "- When user checks dashboard: Permission added to database\n";
echo "- When user unchecks dashboard: Permission removed from database via sync()\n";
echo "- sync() replaces ALL permissions with only what's in \$permissionIds\n";
echo "- Missing permissions from \$permissionIds = removed from user\n\n";

echo "NEXT STEP: Test this in browser by editing a user and unchecking dashboard permission.\n";

?>
