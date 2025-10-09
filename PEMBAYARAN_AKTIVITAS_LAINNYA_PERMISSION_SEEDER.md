# Pembayaran Aktivitas Lainnya Permission Seeder

## 📋 **Overview**

Seeder untuk membuat permissions yang diperlukan untuk modul Pembayaran Aktivitas Lainnya dengan sistem single-entry accounting.

## 🗂️ **File Location**

```
database/seeders/PembayaranAktivitasLainnyaPermissionSeeder.php
```

## 🔐 **Permissions Created**

### **Basic CRUD Operations:**

| Permission Name                       | Description                                 | Used For              |
| ------------------------------------- | ------------------------------------------- | --------------------- |
| `pembayaran-aktivitas-lainnya-view`   | Melihat daftar pembayaran aktivitas lainnya | Index, Show methods   |
| `pembayaran-aktivitas-lainnya-create` | Membuat pembayaran aktivitas lainnya baru   | Create, Store methods |
| `pembayaran-aktivitas-lainnya-update` | Mengedit pembayaran aktivitas lainnya       | Edit, Update methods  |
| `pembayaran-aktivitas-lainnya-delete` | Menghapus pembayaran aktivitas lainnya      | Destroy method        |

### **Additional Operations:**

| Permission Name                               | Description                                  | Used For               |
| --------------------------------------------- | -------------------------------------------- | ---------------------- |
| `pembayaran-aktivitas-lainnya-export`         | Mengekspor data pembayaran aktivitas lainnya | Export functionality   |
| `pembayaran-aktivitas-lainnya-print`          | Mencetak pembayaran aktivitas lainnya        | Print receipts/reports |
| `pembayaran-aktivitas-lainnya-approve`        | Menyetujui pembayaran aktivitas lainnya      | Approval workflow      |
| `pembayaran-aktivitas-lainnya-reject`         | Menolak pembayaran aktivitas lainnya         | Rejection workflow     |
| `pembayaran-aktivitas-lainnya-generate-nomor` | Generate nomor pembayaran                    | Auto number generation |
| `pembayaran-aktivitas-lainnya-payment-form`   | Akses form pembayaran                        | Payment form access    |

## 🚀 **How to Run**

### **On Development:**

```bash
# Don't run in development - manual deployment to server
# php artisan db:seed --class=PembayaranAktivitasLainnyaPermissionSeeder
```

### **On Server:**

```bash
# Upload seeder file to server first
# Then run:
php artisan db:seed --class=PembayaranAktivitasLainnyaPermissionSeeder
```

## 🎯 **Features**

### **Duplicate Prevention:**

-   ✅ Checks existing permissions before creating
-   ✅ Prevents duplicate entries
-   ✅ Shows appropriate messages for existing/new permissions

### **Error Handling:**

-   ✅ Safe to run multiple times
-   ✅ Informative console output
-   ✅ Transaction-safe operations

## 🔄 **Integration with UserController**

### **Matrix Permission Mapping:**

These permissions will be automatically mapped in the UserController's `convertMatrixPermissionsToIds()` method:

```php
// Matrix Format
'pembayaran-aktivitas-lainnya' => [
    'view' => true,    // → pembayaran-aktivitas-lainnya-view
    'create' => true,  // → pembayaran-aktivitas-lainnya-create
    'update' => true,  // → pembayaran-aktivitas-lainnya-update
    'delete' => true,  // → pembayaran-aktivitas-lainnya-delete
    'print' => true,   // → pembayaran-aktivitas-lainnya-print
    'export' => true,  // → pembayaran-aktivitas-lainnya-export
    'approve' => true, // → pembayaran-aktivitas-lainnya-approve
]
```

## 🛡️ **Security Considerations**

### **Permission Guards:**

Each controller method should use proper permission gates:

```php
// In Controller methods
public function index(Request $request)
{
    $this->authorize('pembayaran-aktivitas-lainnya-view');
    // ... method logic
}

public function store(Request $request)
{
    $this->authorize('pembayaran-aktivitas-lainnya-create');
    // ... method logic
}
```

### **Middleware Protection:**

Routes should be protected with appropriate middleware:

```php
// In routes file
Route::middleware(['auth', 'permission:pembayaran-aktivitas-lainnya-view'])
    ->get('/pembayaran-aktivitas-lainnya', [PembayaranAktivitasLainnyaController::class, 'index']);
```

## 📊 **Usage Examples**

### **Assign Permissions to User:**

```php
$user = User::find(1);

// Assign all pembayaran-aktivitas-lainnya permissions
$permissions = Permission::where('name', 'like', 'pembayaran-aktivitas-lainnya-%')->get();
$user->permissions()->sync($permissions->pluck('id'));
```

### **Check Permission in Blade:**

```blade
@can('pembayaran-aktivitas-lainnya-create')
    <a href="{{ route('pembayaran-aktivitas-lainnya.create') }}">
        Buat Pembayaran Baru
    </a>
@endcan
```

## 🔧 **Customization**

### **Adding New Permissions:**

To add new permissions, modify the `$permissions` array in the seeder:

```php
$permissions[] = [
    'name' => 'pembayaran-aktivitas-lainnya-custom-action',
    'description' => 'Custom action description'
];
```

### **Removing Permissions:**

To remove permissions, create a separate migration or modify the seeder to handle deletions.

## 🎯 **Production Deployment**

### **Steps:**

1. ✅ Upload seeder file to server
2. ✅ Run seeder on production
3. ✅ Assign permissions to appropriate users
4. ✅ Test functionality with different user roles
5. ✅ Update documentation

---

**Created:** October 9, 2025  
**System:** AYPSIS - Single Entry Accounting  
**Module:** Pembayaran Aktivitas Lainnya
