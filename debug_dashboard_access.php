<?php

// Script untuk debug akses dashboard yang ditolak
echo "=== DEBUG DASHBOARD ACCESS DENIED ===\n\n";

// Simple connection untuk menghindari Laravel issues
$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "1. Mengecek user yang login (atau admin):\n";

    // Cari user admin atau yang punya dashboard permission
    $stmt = $pdo->query("
        SELECT u.id, u.username, u.karyawan_id,
               GROUP_CONCAT(p.name ORDER BY p.name SEPARATOR ', ') as permissions
        FROM users u
        LEFT JOIN user_permissions up ON u.id = up.user_id
        LEFT JOIN permissions p ON up.permission_id = p.id
        WHERE u.username = 'admin' OR u.id IN (
            SELECT DISTINCT up2.user_id
            FROM user_permissions up2
            JOIN permissions p2 ON up2.permission_id = p2.id
            WHERE p2.name = 'dashboard'
        )
        GROUP BY u.id, u.username, u.karyawan_id
        ORDER BY u.username
    ");

    $usersWithDashboard = $stmt->fetchAll(PDO::FETCH_OBJ);

    if (count($usersWithDashboard) > 0) {
        foreach ($usersWithDashboard as $user) {
            $hasDashboard = strpos($user->permissions, 'dashboard') !== false ? '✅ HAS DASHBOARD' : '❌ NO DASHBOARD';
            echo "   User: {$user->username} (ID: {$user->id})\n";
            echo "   Karyawan ID: " . ($user->karyawan_id ?: 'NULL') . "\n";
            echo "   Permissions: {$user->permissions}\n";
            echo "   Dashboard Access: $hasDashboard\n\n";
        }
    } else {
        echo "   ❌ Tidak ada user dengan permission dashboard!\n\n";
    }

    echo "2. Mengecek permission 'dashboard' di database:\n";
    $stmt = $pdo->prepare("SELECT id, name, description FROM permissions WHERE name = ?");
    $stmt->execute(['dashboard']);
    $dashboardPerm = $stmt->fetch(PDO::FETCH_OBJ);

    if ($dashboardPerm) {
        echo "   ✅ Permission 'dashboard' ditemukan (ID: {$dashboardPerm->id})\n";
        echo "   Deskripsi: {$dashboardPerm->description}\n\n";

        // Cek berapa user yang memiliki permission ini
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count
            FROM user_permissions up
            WHERE up.permission_id = ?
        ");
        $stmt->execute([$dashboardPerm->id]);
        $userCount = $stmt->fetch(PDO::FETCH_OBJ)->count;
        echo "   👥 Jumlah user dengan permission dashboard: $userCount\n\n";

    } else {
        echo "   ❌ Permission 'dashboard' TIDAK ditemukan di database!\n";
        echo "   Solusi: Jalankan seeder atau buat permission dashboard manual\n\n";
    }

    echo "3. Mengecek middleware yang mungkin memblokir:\n";
    echo "   Route dashboard menggunakan middleware:\n";
    echo "   - auth (harus login)\n";
    echo "   - EnsureKaryawanPresent (harus punya data karyawan)\n";
    echo "   - EnsureUserApproved (user harus disetujui)\n";
    echo "   - EnsureCrewChecklistComplete (checklist ABK harus selesai)\n";
    echo "   - can:dashboard (permission dashboard)\n\n";

    echo "4. Mengecek user yang mungkin bermasalah:\n";

    // Cek user yang tidak punya karyawan_id
    $stmt = $pdo->query("
        SELECT id, username, karyawan_id
        FROM users
        WHERE karyawan_id IS NULL
        ORDER BY username
    ");
    $usersNoKaryawan = $stmt->fetchAll(PDO::FETCH_OBJ);

    if (count($usersNoKaryawan) > 0) {
        echo "   ⚠️  User tanpa karyawan_id (akan diblokir EnsureKaryawanPresent):\n";
        foreach ($usersNoKaryawan as $user) {
            echo "     - {$user->username} (ID: {$user->id})\n";
        }
        echo "\n";
    }

    // Cek karyawan yang belum approved
    $stmt = $pdo->query("
        SELECT u.id, u.username, k.nama_lengkap, k.status_approval
        FROM users u
        LEFT JOIN karyawans k ON u.karyawan_id = k.id
        WHERE k.status_approval != 'approved' OR k.status_approval IS NULL
        ORDER BY u.username
    ");
    $usersNotApproved = $stmt->fetchAll(PDO::FETCH_OBJ);

    if (count($usersNotApproved) > 0) {
        echo "   ⚠️  User dengan karyawan belum approved (akan diblokir EnsureUserApproved):\n";
        foreach ($usersNotApproved as $user) {
            $status = $user->status_approval ?: 'NULL';
            echo "     - {$user->username} -> {$user->nama_lengkap} (Status: $status)\n";
        }
        echo "\n";
    }

    echo "5. Solusi yang bisa dicoba:\n";
    echo "   a. Pastikan user login memiliki permission 'dashboard'\n";
    echo "   b. Pastikan user memiliki karyawan_id yang valid\n";
    echo "   c. Pastikan karyawan memiliki status_approval = 'approved'\n";
    echo "   d. Untuk admin, bypass middleware dengan update route atau permission\n";
    echo "   e. Cek log Laravel untuk error detail: storage/logs/laravel.log\n\n";

    echo "6. Command untuk fix permission dashboard:\n";
    echo "   php artisan tinker\n";
    echo "   \$user = User::where('username', 'admin')->first();\n";
    echo "   \$perm = Permission::where('name', 'dashboard')->first();\n";
    echo "   \$user->permissions()->attach(\$perm->id);\n\n";

} catch (PDOException $e) {
    echo "❌ Error koneksi database: " . $e->getMessage() . "\n";
}

?>
