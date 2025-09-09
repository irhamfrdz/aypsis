# User Registration Approval System Documentation

## Overview
Sistem persetujuan registrasi user telah diimplementasikan untuk memastikan semua akun user baru harus mendapat approval dari administrator sebelum dapat digunakan.

## Features Implemented

### 1. User Status Management
- **Pending**: Status default untuk registrasi baru, menunggu approval admin
- **Approved**: User dapat login dan menggunakan sistem
- **Rejected**: User ditolak, tidak bisa login

### 2. Database Changes
**Migration**: `2025_09_09_000001_add_approval_system_to_users`
- Added `status` field with default 'pending'
- Added `approved_by` field (foreign key to users.id)
- Added `approved_at` timestamp field

**Model Updates**:
- `User` model: Added approval-related fields and methods
- `Karyawan` model: Added missing `user()` relationship

### 3. Authentication Flow
**Registration Process**:
1. User fills registration form
2. Account created with status 'pending'
3. User sees success message: "Akun Anda menunggu persetujuan administrator"

**Login Process**:
1. User enters credentials
2. System checks account status:
   - `pending`: Login blocked with message
   - `rejected`: Login blocked with reason
   - `approved`: Login allowed normally

### 4. Admin Management Interface
**Route**: `/admin/user-approval`
**Controller**: `UserApprovalController`
**View**: `admin.user-approval`

**Features**:
- ✅ Tabbed interface (Pending, Approved, Rejected)
- ✅ Pending users count badge in sidebar
- ✅ Approve user with one click
- ✅ Reject user with optional reason
- ✅ View detailed user information
- ✅ Track who approved/rejected and when

### 5. Sidebar Integration
- New menu item "Persetujuan User" with FontAwesome icon
- Red badge showing pending approval count
- Only visible to users with 'manage-users' permission

## User Interface Components

### Registration Flow
1. **Registration Form**: `/register/user`
   - Links to karyawan records without user accounts
   - Requires reason for registration
   - Status automatically set to 'pending'

2. **Login Messages**:
   - Pending: "Akun Anda masih menunggu persetujuan administrator"
   - Rejected: "Akun Anda telah ditolak oleh administrator"

### Admin Interface
1. **Approval Dashboard**: Modern tabbed interface
   - Pending tab with approval/reject buttons
   - Approved tab showing approval history
   - Rejected tab showing rejected users

2. **Action Buttons**:
   - Green "Setujui" button for approval
   - Red "Tolak" button with modal for rejection reason
   - Blue "Detail" button for viewing full information

## Security Features

### Permission-Based Access
- User approval interface requires 'manage-users' permission
- Only authorized admins can approve/reject users

### Audit Trail
- Records who approved/rejected each user
- Timestamps for all approval actions
- Rejection reasons stored for reference

### Status Validation
- Login blocked for non-approved users
- Clear error messages for different status types
- Prevents unauthorized access attempts

## Backend Implementation

### Controllers
**AuthController Updates**:
```php
// Registration sets status to 'pending'
'status' => 'pending'

// Login checks status before allowing access
if ($user->status === 'pending') {
    Auth::logout();
    return back()->withErrors(['username' => 'Akun menunggu persetujuan...']);
}
```

**UserApprovalController**:
```php
// Approve user
$user->update([
    'status' => 'approved',
    'approved_by' => Auth::id(),
    'approved_at' => now(),
]);

// Reject user
$user->update([
    'status' => 'rejected',
    'approved_by' => Auth::id(),
    'approved_at' => now(),
]);
```

### Model Methods
**User Model**:
```php
public function isApproved(): bool
public function isPending(): bool  
public function isRejected(): bool
public function approvedBy(): BelongsTo
```

## Migration Strategy

### Backward Compatibility
- Existing users automatically set to 'approved' status
- No disruption to current user accounts
- Smooth transition to approval system

### Database Updates
```sql
-- New fields added
ALTER TABLE users ADD status VARCHAR(255) DEFAULT 'pending';
ALTER TABLE users ADD approved_by BIGINT UNSIGNED NULL;
ALTER TABLE users ADD approved_at TIMESTAMP NULL;
ALTER TABLE users ADD FOREIGN KEY (approved_by) REFERENCES users(id);
```

## Usage Examples

### Admin Workflow
1. Admin receives notification of pending registration
2. Reviews user information and reason for registration
3. Approves or rejects with optional reason
4. User receives appropriate access level

### User Experience
1. User registers with valid reason
2. Sees confirmation message about pending approval
3. Waits for admin approval notification
4. Can login once approved

## Testing

### Test Cases Covered
- ✅ Registration creates pending status
- ✅ Pending users cannot login
- ✅ Approved users can login normally
- ✅ Rejected users cannot login
- ✅ Admin can view all user statuses
- ✅ Approval/rejection updates status correctly
- ✅ Audit trail tracks approval actions

### Test Files
- `test_register_user.php`: Tests registration functionality
- `update_existing_users_status.php`: Migrates existing users

## Routes Added
```php
Route::prefix('admin/user-approval')->middleware(['auth', 'permission:manage-users'])->group(function () {
    Route::get('/', [UserApprovalController::class, 'index'])->name('admin.user-approval.index');
    Route::get('/{user}', [UserApprovalController::class, 'show'])->name('admin.user-approval.show');
    Route::post('/{user}/approve', [UserApprovalController::class, 'approve'])->name('admin.user-approval.approve');
    Route::post('/{user}/reject', [UserApprovalController::class, 'reject'])->name('admin.user-approval.reject');
});
```

## Files Modified/Created

### New Files
- `app/Http/Controllers/UserApprovalController.php`
- `resources/views/admin/user-approval.blade.php`
- `database/migrations/2025_09_09_000001_add_approval_system_to_users.php`

### Modified Files
- `app/Models/User.php` - Added approval relationships and methods
- `app/Models/Karyawan.php` - Added user() relationship
- `app/Http/Controllers/AuthController.php` - Updated login/registration logic
- `routes/web.php` - Added approval system routes
- `resources/views/layouts/app.blade.php` - Added approval menu with badge

## Future Enhancements
- Email notifications for approval/rejection
- Bulk approval actions
- Advanced filtering and search
- User approval statistics dashboard
- Integration with notification system

## Conclusion
The user registration approval system provides a complete solution for managing user access with proper security, audit trails, and user-friendly interfaces. All new registrations now require administrator approval while maintaining backward compatibility with existing users.
