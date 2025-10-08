<?php<?php

try {

    $pdo = new PDO('mysql:host=localhost;dbname=aypsis', 'root', '');require_once __DIR__ . '/vendor/autoload.php';



    // Check permissions$app = require_once __DIR__ . '/bootstrap/app.php';

    $stmt = $pdo->prepare('$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        SELECT p.name

        FROM permissions p$user = \App\Models\User::where('username', 'admin')->first();

        JOIN role_has_permissions rhp ON p.id = rhp.permission_idif ($user) {

        JOIN model_has_roles mhr ON rhp.role_id = mhr.role_id    echo "Admin user permissions:\n";

        WHERE mhr.model_id = 1 AND mhr.model_type = "App\\\\Models\\\\User"    $permissions = $user->permissions()->pluck('name')->toArray();

    ');    foreach ($permissions as $perm) {

    $stmt->execute();        echo "- $perm\n";

    $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);    }



    echo "User ID 1 permissions:" . PHP_EOL;    echo "\nDirect permissions check:\n";

    foreach ($permissions as $permission) {    echo "Has pranota-cat-create: " . ($user->hasPermissionTo('pranota-cat-create') ? 'YES' : 'NO') . "\n";

        echo "- " . $permission . PHP_EOL;    echo "Has pranota-cat-view: " . ($user->hasPermissionTo('pranota-cat-view') ? 'YES' : 'NO') . "\n";

        if (strpos($permission, 'approval') !== false) {

            echo "  ↳ APPROVAL RELATED ✅" . PHP_EOL;    echo "\nVia can() method:\n";

        }    echo "Can pranota-cat-create: " . ($user->can('pranota-cat-create') ? 'YES' : 'NO') . "\n";

    }    echo "Can pranota-cat-view: " . ($user->can('pranota-cat-view') ? 'YES' : 'NO') . "\n";



    // Check specifically for approval-dashboard    // Check roles

    if (in_array('approval-dashboard', $permissions)) {    echo "\nUser roles:\n";

        echo PHP_EOL . "✅ User HAS approval-dashboard permission" . PHP_EOL;    $roles = $user->roles()->pluck('name')->toArray();

    } else {    foreach ($roles as $role) {

        echo PHP_EOL . "❌ User DOES NOT have approval-dashboard permission" . PHP_EOL;        echo "- $role\n";

        echo "Available approval permissions:" . PHP_EOL;    }

        foreach ($permissions as $perm) {} else {

            if (strpos($perm, 'approval') !== false) {    echo "Admin user not found\n";

                echo "- " . $perm . PHP_EOL;}

            }
        }
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
