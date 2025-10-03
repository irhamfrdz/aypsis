use App\Models\User;
use Spatie\Permission\Models\Permission;

// Find admin user
$admin = User::where('name', 'admin')->first();
if (!$admin) {
    echo "Admin user not found!\n";
    exit;
}

// Check if permission exists
$permission = Permission::where('name', 'tagihan-kontainer-sewa-create')->first();
if (!$permission) {
    echo "Permission 'tagihan-kontainer-sewa-create' not found!\n";
    exit;
}

// Test permission
$hasPermission = $admin->hasPermissionTo('tagihan-kontainer-sewa-create');
echo "Admin has tagihan-kontainer-sewa-create permission: " . ($hasPermission ? 'YES' : 'NO') . "\n";

// Test can method
$canDo = $admin->can('tagihan-kontainer-sewa-create');
echo "Admin can() tagihan-kontainer-sewa-create: " . ($canDo ? 'YES' : 'NO') . "\n";
