# AYP SIS - Comprehensive Permissions Seeder

## 📋 Overview

This comprehensive permissions seeder creates a complete permission system for the AYP SIS (Ayodya Prima Sis) application. It includes all necessary permissions organized by categories, roles with appropriate permissions, and default users for immediate use.

## 🎯 Features

-   **440+ Permissions** organized by categories
-   **5 User Roles** with appropriate permission levels
-   **5 Default Users** ready for immediate login
-   **Database Backup** before seeding
-   **Comprehensive Logging** for troubleshooting
-   **Cross-platform Support** (Linux/Windows)

## 📁 File Structure

```
database/seeders/
├── ComprehensivePermissionsSeeder.php    # Main permissions seeder
└── RoleAndPermissionSeeder.php          # Roles and user assignments

Scripts:
├── run_permissions_seeder.sh            # Linux/Mac execution script
└── run_permissions_seeder.bat           # Windows execution script
```

## 🔐 Permission Categories

### 1. Dashboard Permissions

-   Dashboard access and management

### 2. Master Data Permissions

-   **Karyawan** (Employees)
-   **Kontainer** (Containers)
-   **Tujuan** (Destinations)
-   **Kegiatan** (Activities)
-   **Mobil** (Vehicles)
-   **Pricelist Sewa Kontainer** (Container Rental Pricing)
-   **Cabang** (Branches)
-   **Divisi** (Divisions)
-   **Pekerjaan** (Jobs)
-   **Pajak** (Taxes)
-   **Bank** (Banks)
-   **COA** (Chart of Accounts)

### 3. Pranota Permissions

-   **Pranota Memo** (Driver Notes)
-   **Pranota Tagihan Kontainer** (Container Billing Notes)

### 4. Pembayaran Permissions

-   **Pembayaran Pranota Memo** (Driver Note Payments)
-   **Pembayaran Pranota Kontainer** (Container Note Payments)
-   **Pembayaran Pranota Perbaikan Kontainer** (Container Repair Note Payments)

### 5. Tagihan Permissions

-   **Tagihan Kontainer Sewa** (Container Rental Billing)

### 6. Permohonan Permissions

-   **Permohonan** (Requests/Applications)

### 7. Perbaikan Kontainer Permissions

-   **Perbaikan Kontainer** (Container Repairs)
-   **Pranota Perbaikan Kontainer** (Container Repair Notes)

### 8. User & Approval Permissions

-   **User Approval** (User Approvals)
-   **Approval System** (General Approvals)

### 9. System Permissions

-   Login, logout, password management

## 👥 User Roles & Permissions

### 1. Admin (Full Access)

-   **Username:** `admin`
-   **Password:** `admin123`
-   **Permissions:** All permissions (440+)
-   **Description:** Complete system access

### 2. Manager (Operational Management)

-   **Username:** `manager`
-   **Password:** `manager123`
-   **Permissions:** Most permissions except user/permission management
-   **Description:** Operational management with limited admin access

### 3. Supervisor (Supervisory Access)

-   **Username:** `supervisor`
-   **Password:** `supervisor123`
-   **Permissions:** Operational + approval permissions
-   **Description:** Supervisory role with approval capabilities

### 4. Staff (Basic Access)

-   **Username:** `staff`
-   **Password:** `staff123`
-   **Permissions:** Basic view permissions only
-   **Description:** Read-only access for operational staff

### 5. Supir (Driver Access)

-   **Username:** `supir`
-   **Password:** `supir123`
-   **Permissions:** Limited to own pranota
-   **Description:** Driver-specific access

## 🚀 Installation & Usage

### Prerequisites

-   Laravel application installed
-   Database configured
-   PHP 8.0+
-   Composer dependencies installed

### Linux/Mac Execution

```bash
# Make script executable
chmod +x run_permissions_seeder.sh

# Run the seeder
./run_permissions_seeder.sh
```

### Windows Execution

```batch
# Run the batch file
run_permissions_seeder.bat
```

### Manual Execution

```bash
# Run individual seeders
php artisan db:seed --class=ComprehensivePermissionsSeeder --force
php artisan db:seed --class=RoleAndPermissionSeeder --force
```

## 📊 What Gets Created

### Permissions

-   440+ permissions across all modules
-   Organized by functional categories
-   CRUD operations for each module
-   Special permissions for approvals, printing, exporting

### Roles

-   5 predefined roles with appropriate permissions
-   Hierarchical permission structure
-   Role-based access control

### Users

-   5 default users with different access levels
-   Pre-configured passwords (change in production!)
-   Associated with appropriate roles

### Database Tables

-   `permissions` - All system permissions
-   `roles` - User roles
-   `role_permissions` - Role-permission relationships
-   `user_permissions` - Direct user permissions
-   `user_roles` - User-role relationships

## 🔧 Configuration

### Environment Variables

Ensure your `.env` file has correct database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Customizing Permissions

To modify permissions, edit:

-   `database/seeders/ComprehensivePermissionsSeeder.php` - Add/remove permissions
-   `database/seeders/RoleAndPermissionSeeder.php` - Modify role assignments

## 📋 Logging & Backup

### Automatic Features

-   **Database Backup:** Created before seeding
-   **Log Files:** Comprehensive execution logs
-   **Error Handling:** Detailed error reporting

### File Locations

```
backups/
└── backup_before_permissions_YYYYMMDD_HHMMSS.sql

logs/
└── seeder_YYYYMMDD_HHMMSS.log
```

## 🧪 Testing

### Verify Installation

```bash
# Check permissions count
php artisan tinker --execute="echo App\Models\Permission::count();"

# Check roles count
php artisan tinker --execute="echo App\Models\Role::count();"

# Check users count
php artisan tinker --execute="echo App\Models\User::count();"
```

### Test Login

Use the default credentials to test login functionality:

-   Admin: `admin` / `admin123`
-   Manager: `manager` / `manager123`
-   Staff: `staff` / `staff123`
-   Supervisor: `supervisor` / `supervisor123`
-   Supir: `supir` / `supir123`

## 🔒 Security Notes

### Production Deployment

1. **Change Default Passwords** immediately after installation
2. **Review User Permissions** based on your organizational needs
3. **Enable HTTPS** for secure authentication
4. **Configure Proper Session Management**

### Best Practices

-   Use strong passwords in production
-   Regularly audit user permissions
-   Implement password policies
-   Enable two-factor authentication if available

## 🐛 Troubleshooting

### Common Issues

#### Seeder Fails

```bash
# Clear cache and try again
php artisan config:clear
php artisan cache:clear
php artisan db:seed --class=ComprehensivePermissionsSeeder --force
```

#### Permission Denied

-   Check database user permissions
-   Verify `.env` database configuration
-   Ensure migration tables exist

#### Duplicate Permissions

-   The seeder checks for existing permissions
-   No duplicates will be created

### Log Analysis

Check log files in `logs/` directory for detailed error information.

## 📞 Support

For issues or questions:

1. Check log files for error details
2. Verify database connectivity
3. Ensure all prerequisites are met
4. Review Laravel logs for additional information

## 📝 Changelog

### v1.0.0

-   Initial release
-   440+ comprehensive permissions
-   5 user roles with appropriate permissions
-   5 default users
-   Cross-platform execution scripts
-   Automatic backup and logging

---

**Note:** This seeder is designed for the AYP SIS application. Modify permissions and roles according to your specific requirements.
