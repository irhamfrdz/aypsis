<?php

echo "=== DEBUG DASHBOARD PERMISSION UNCHECKING ISSUE ===\n\n";

// Simulate the problem scenario
echo "SCENARIO: User unchecks dashboard permission in form\n";
echo "Expected: dashboard permission removed from user\n";
echo "Actual: dashboard permission still exists after edit\n\n";

echo "1. ANALYZING THE ISSUE:\n";
echo "When checkbox is UNCHECKED in form:\n";
echo "- HTML form doesn't send the field at all\n";
echo "- Laravel receives: permissions['system']['dashboard'] = NOT SET\n";
echo "- convertMatrixPermissionsToIds() gets: \$actions['dashboard'] = undefined/null\n\n";

echo "2. CURRENT CODE BEHAVIOR:\n";
echo "In convertMatrixPermissionsToIds():\n";
echo "```php\n";
echo "if (\$module === 'system') {\n";
echo "    foreach (\$actions as \$action => \$enabled) {\n";
echo "        if (!\$enabled) continue; // PROBLEM: skips if false/0/null\n";
echo "        \n";
echo "        if (\$action === 'dashboard') {\n";
echo "            // Add permission ID\n";
echo "        }\n";
echo "    }\n";
echo "}\n";
echo "```\n\n";

echo "3. THE PROBLEM:\n";
echo "❌ When unchecked, \$actions['dashboard'] is NOT SET in array\n";
echo "❌ foreach() never iterates over non-existent key\n";
echo "❌ Permission is never processed for removal\n";
echo "❌ Old permissions remain in database\n\n";

echo "4. WHY THIS HAPPENS:\n";
echo "HTML checkboxes have this behavior:\n";
echo "- CHECKED: sends name='1' or name='on'\n";
echo "- UNCHECKED: sends NOTHING (key doesn't exist)\n";
echo "\n";
echo "So when dashboard is unchecked:\n";
echo "- Form data: ['system' => []] (no 'dashboard' key)\n";
echo "- convertMatrixPermissionsToIds processes empty array\n";
echo "- No dashboard permission is added to \$permissionIds\n";
echo "- But other existing permissions might still be there\n";
echo "- sync() only syncs what's in \$permissionIds\n\n";

echo "5. ROOT CAUSE:\n";
echo "The logic assumes that unchecked = \$enabled = false\n";
echo "But in reality: unchecked = key doesn't exist at all\n\n";

echo "6. SOLUTION APPROACHES:\n";
echo "A. FRONTEND FIX - Force unchecked values:\n";
echo "   Add hidden inputs or JavaScript to send dashboard=0 when unchecked\n\n";

echo "B. BACKEND FIX - Handle missing keys properly:\n";
echo "   Check if key exists, treat missing as unchecked\n\n";

echo "C. COMPREHENSIVE FIX - Both frontend and backend:\n";
echo "   1. Frontend ensures all possible permissions are sent\n";
echo "   2. Backend handles both present and missing keys\n\n";

echo "7. RECOMMENDED BACKEND FIX:\n";
echo "Modify convertMatrixPermissionsToIds() system module handling:\n";
echo "```php\n";
echo "if (\$module === 'system') {\n";
echo "    // Check for dashboard permission specifically\n";
echo "    \$dashboardEnabled = isset(\$actions['dashboard']) && (\$actions['dashboard'] == '1' || \$actions['dashboard'] === true);\n";
echo "    \n";
echo "    if (\$dashboardEnabled) {\n";
echo "        \$permission = Permission::where('name', 'dashboard')->first();\n";
echo "        if (\$permission) {\n";
echo "            \$permissionIds[] = \$permission->id;\n";
echo "        }\n";
echo "    }\n";
echo "    // Note: if not enabled, don't add to \$permissionIds (which means it will be removed by sync())\n";
echo "    continue;\n";
echo "}\n";
echo "```\n\n";

echo "8. WHY sync() DOESN'T REMOVE:\n";
echo "- sync(\$permissionIds) replaces ALL user permissions with only what's in \$permissionIds\n";
echo "- If dashboard permission ID is not in \$permissionIds, it SHOULD be removed\n";
echo "- But if there are other permissions being processed that re-add it, that could cause issues\n\n";

echo "=== ACTION REQUIRED ===\n";
echo "Need to fix the convertMatrixPermissionsToIds() method to properly handle unchecked checkboxes.\n";
echo "The current logic only processes checked permissions, but doesn't explicitly handle unchecked ones.\n";

?>
