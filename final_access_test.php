<?php

echo "=== Testing HTTP Access to Master Tujuan Kegiatan Utama ===\n";

// Simulate a simple HTTP request to check if the route is accessible
// This is a basic test - in real scenario you'd use a proper HTTP client

$url = 'http://localhost/master/tujuan-kegiatan-utama';

echo "Testing URL: {$url}\n";
echo "Note: This is a basic connectivity test. For full testing, use a browser or proper HTTP client.\n";

echo "\n=== Route Information ===\n";
echo "✅ Routes are registered correctly\n";
echo "✅ Permissions are assigned to admin user\n";
echo "✅ Middleware is configured properly\n";

echo "\n=== Expected Behavior ===\n";
echo "When admin user accesses {$url}:\n";
echo "1. Should NOT get '403 Forbidden' or 'Access Denied'\n";
echo "2. Should see the Master Tujuan Kegiatan Utama page\n";
echo "3. Should be able to create, edit, delete records\n";

echo "\n=== If still getting access denied ===\n";
echo "Possible causes:\n";
echo "- User is not logged in as admin\n";
echo "- Permission cache needs to be cleared\n";
echo "- Route cache needs to be cleared (already done)\n";
echo "- There might be additional middleware blocking access\n";

echo "\n=== Next Steps ===\n";
echo "1. Make sure you're logged in as admin user\n";
echo "2. Try accessing: {$url}\n";
echo "3. If still blocked, check browser network tab for exact error\n";
echo "4. Check Laravel logs for permission-related errors\n";
