# Additional Permissions Seeder

Seeder ini digunakan untuk menambahkan permissions tambahan hingga mencapai target 300 permissions total.

## Fitur

- ✅ Otomatis mendeteksi jumlah permissions yang sudah ada
- ✅ Menambahkan permissions tambahan secara otomatis
- ✅ Target: Minimal 300 permissions total
- ✅ Permissions yang ditambahkan mencakup berbagai modul sistem

## Permissions yang Ditambahkan

Seeder ini akan menambahkan permissions untuk modul-modul berikut:

### 1. Sistem Notifikasi (5 permissions)
- `notification.view`, `notification.create`, `notification.update`, `notification.delete`, `notification.send`

### 2. Sistem Laporan (7 permissions)
- `report.view`, `report.create`, `report.update`, `report.delete`, `report.export`, `report.print`, `report.dashboard`

### 3. Sistem Audit Trail (3 permissions)
- `audit.view`, `audit.export`, `audit.delete`

### 4. Sistem Backup (5 permissions)
- `backup.create`, `backup.restore`, `backup.download`, `backup.delete`, `backup.schedule`

### 5. Sistem Maintenance (4 permissions)
- `maintenance.view`, `maintenance.schedule`, `maintenance.execute`, `maintenance.cancel`

### 6. Sistem API (5 permissions)
- `api.access`, `api.webhook.view`, `api.webhook.create`, `api.webhook.update`, `api.webhook.delete`

### 7. Sistem Integrasi (5 permissions)
- `integration.view`, `integration.create`, `integration.update`, `integration.delete`, `integration.test`

### 8. Sistem Workflow (5 permissions)
- `workflow.view`, `workflow.create`, `workflow.update`, `workflow.delete`, `workflow.execute`

### 9. Sistem Dokumentasi (5 permissions)
- `documentation.view`, `documentation.create`, `documentation.update`, `documentation.delete`, `documentation.publish`

### 10. Sistem Training (6 permissions)
- `training.view`, `training.create`, `training.update`, `training.delete`, `training.enroll`, `training.complete`

### 11. Sistem Feedback (5 permissions)
- `feedback.view`, `feedback.create`, `feedback.update`, `feedback.delete`, `feedback.respond`

### 12. Sistem Knowledge Base (5 permissions)
- `knowledge.view`, `knowledge.create`, `knowledge.update`, `knowledge.delete`, `knowledge.publish`

### 13. Sistem Analytics (5 permissions)
- `analytics.view`, `analytics.create`, `analytics.update`, `analytics.delete`, `analytics.export`

### 14. Sistem Monitoring (5 permissions)
- `monitoring.view`, `monitoring.alert.view`, `monitoring.alert.create`, `monitoring.alert.update`, `monitoring.alert.delete`

### 15. Sistem Security (6 permissions)
- `security.view`, `security.update`, `security.policy.view`, `security.policy.create`, `security.policy.update`, `security.policy.delete`

### 16. Sistem Compliance (5 permissions)
- `compliance.view`, `compliance.create`, `compliance.update`, `compliance.delete`, `compliance.audit`

### 17. Sistem Quality Assurance (5 permissions)
- `qa.view`, `qa.create`, `qa.update`, `qa.delete`, `qa.execute`

### 18. Sistem Helpdesk (6 permissions)
- `helpdesk.view`, `helpdesk.create`, `helpdesk.update`, `helpdesk.delete`, `helpdesk.assign`, `helpdesk.close`

### 19. Sistem Asset Management (6 permissions)
- `asset.view`, `asset.create`, `asset.update`, `asset.delete`, `asset.transfer`, `asset.maintenance`

### 20. Sistem Inventory (6 permissions)
- `inventory.view`, `inventory.create`, `inventory.update`, `inventory.delete`, `inventory.adjust`, `inventory.transfer`

### 21. Sistem Procurement (6 permissions)
- `procurement.view`, `procurement.create`, `procurement.update`, `procurement.delete`, `procurement.approve`, `procurement.reject`

### 22. Sistem Vendor Management (6 permissions)
- `vendor.view`, `vendor.create`, `vendor.update`, `vendor.delete`, `vendor.approve`, `vendor.blacklist`

### 23. Sistem Contract Management (6 permissions)
- `contract.view`, `contract.create`, `contract.update`, `contract.delete`, `contract.approve`, `contract.terminate`

## Cara Penggunaan

### 1. Menggunakan Script (Direkomendasikan)

#### Linux/Mac:
```bash
./run_additional_permissions.sh
```

#### Windows:
```batch
run_additional_permissions.bat
```

### 2. Manual menggunakan Artisan

```bash
php artisan db:seed --class=AdditionalPermissionsSeeder
```

### 3. Sebagai bagian dari Database Sync

Seeder ini juga akan dijalankan otomatis ketika menjalankan `DatabaseSyncSeeder`:

```bash
php artisan db:seed --class=DatabaseSyncSeeder
```

## Verifikasi

Setelah menjalankan seeder, Anda dapat memverifikasi hasilnya dengan:

```bash
php artisan tinker --execute="echo 'Total permissions: ' . DB::table('permissions')->count();"
```

Atau lihat di database:
```sql
SELECT COUNT(*) as total_permissions FROM permissions;
```

## Catatan

- Seeder ini akan otomatis menghitung jumlah permissions yang sudah ada
- Hanya akan menambahkan permissions jika jumlah saat ini kurang dari 300
- Permissions yang ditambahkan menggunakan ID mulai dari 382
- Semua permissions baru akan diberi timestamp created_at dan updated_at saat ini

## Troubleshooting

### Jika seeder tidak berjalan:
1. Pastikan database connection sudah benar
2. Pastikan user memiliki permission untuk insert ke tabel permissions
3. Cek log Laravel untuk error details

### Jika jumlah permissions tidak sesuai:
1. Jalankan seeder lagi - seeder ini aman untuk dijalankan berulang
2. Cek apakah ada permissions yang duplikat
3. Verifikasi dengan query manual ke database

## File Terkait

- `database/seeders/AdditionalPermissionsSeeder.php` - Seeder utama
- `database/seeders/DatabaseSyncSeeder.php` - Orchestrator yang memanggil seeder ini
- `run_additional_permissions.sh` - Script Linux/Mac
- `run_additional_permissions.bat` - Script Windows