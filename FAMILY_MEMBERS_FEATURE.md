# Family Members Feature Implementation

## Overview

Added comprehensive family member management functionality to the employee (karyawan) system. This allows adding, editing, and managing family member information for each employee.

## Database Changes

### New Table: `karyawan_family_members`

Created with migration: `2025_10_22_100720_create_karyawan_family_members_table.php`

**Fields:**

-   `id` - Primary key
-   `karyawan_id` - Foreign key to karyawans table
-   `hubungan` - Relationship (Suami, Istri, Anak, etc.)
-   `nama` - Name
-   `tanggal_lahir` - Birth date
-   `alamat` - Address
-   `no_telepon` - Phone number
-   `nik_ktp` - ID card number (16 digits)
-   `no_bpjs_kesehatan` - Health insurance number
-   `faskes` - Healthcare facility
-   `created_at`, `updated_at` - Timestamps

## Model Changes

### New Model: `KaryawanFamilyMember`

-   Path: `app/Models/KaryawanFamilyMember.php`
-   Fillable fields for all family member data
-   Relationship: `belongsTo(Karyawan::class)`

### Updated Model: `Karyawan`

-   Added relationship: `familyMembers()` - `hasMany(KaryawanFamilyMember::class)`

## Controller Updates

### KaryawanController

-   Added import for `KaryawanFamilyMember` model
-   **Store method**: Added validation and creation logic for family members
-   **Update method**: Added validation and update/delete logic for family members

**Validation Rules Added:**

```php
'family_members' => 'nullable|array',
'family_members.*.hubungan' => 'nullable|string|max:255',
'family_members.*.nama' => 'nullable|string|max:255',
'family_members.*.tanggal_lahir' => 'nullable|date',
'family_members.*.alamat' => 'nullable|string|max:500',
'family_members.*.no_telepon' => 'nullable|string|max:20',
'family_members.*.nik_ktp' => 'nullable|string|regex:/^[0-9]{16}$/',
'family_members.*.no_bpjs_kesehatan' => 'nullable|string|max:50',
'family_members.*.faskes' => 'nullable|string|max:255',
```

## View Updates

### Create Form: `resources/views/master-karyawan/create.blade.php`

-   Added "Susunan Keluarga" fieldset
-   Dynamic form with "Add Family Member" button
-   JavaScript for adding/removing family members dynamically
-   Proper form validation

### Edit Form: `resources/views/master-karyawan/edit.blade.php`

-   Added "Susunan Keluarga" fieldset
-   Pre-populated with existing family members
-   JavaScript for managing existing and new family members
-   Update/delete functionality

### Print Form: `resources/views/master-karyawan/print-single.blade.php`

-   Updated gender display to show "Laki-Laki" and "Perempuan" instead of "L" and "P"
-   Removed license plate number field
-   Updated field numbering

## JavaScript Functionality

### Dynamic Form Management

-   Add new family members with proper form fields
-   Remove family members with confirmation
-   Auto-numbering of family member entries
-   Proper form name indexing for array submission

### Form Fields for Each Family Member

-   **Hubungan** (Required): Dropdown with predefined relationships
-   **Nama** (Required): Text input
-   **Tanggal Lahir**: Date picker
-   **Alamat**: Text input
-   **No. Telepon**: Phone number input
-   **No. NIK/KTP**: 16-digit validation
-   **No. BPJS Kesehatan**: Text input
-   **Faskes**: Text input for healthcare facility

### Relationship Options

-   Suami (Husband)
-   Istri (Wife)
-   Anak (Child)
-   Ayah (Father)
-   Ibu (Mother)
-   Kakak (Older Sibling)
-   Adik (Younger Sibling)
-   Kakek (Grandfather)
-   Nenek (Grandmother)
-   Paman (Uncle)
-   Bibi (Aunt)
-   Lainnya (Others)

## Features Implemented

### Create Employee

1. Fill employee basic information
2. Add family members dynamically
3. Validate all fields including family member NIK (16 digits)
4. Save employee and associated family members

### Edit Employee

1. Load existing employee data
2. Display existing family members
3. Add new family members
4. Edit existing family members
5. Remove family members
6. Save changes with proper update/create/delete logic

### Data Validation

-   Required fields: Relationship and Name for family members
-   NIK validation: Must be exactly 16 digits
-   Phone number validation: Numbers only
-   Consistent data formatting (uppercase conversion)

## Testing

### Database Tests

-   ✅ Tables created successfully
-   ✅ Relationships working
-   ✅ CRUD operations functional

### Functionality Tests

-   ✅ Create family members
-   ✅ Read family members
-   ✅ Update family members
-   ✅ Delete family members
-   ✅ Form validation working

## Usage Instructions

### For Administrators

1. Navigate to Master Data > Karyawan
2. Create new employee or edit existing
3. Scroll to "Susunan Keluarga" section
4. Click "Tambah Anggota Keluarga" to add family members
5. Fill required fields (Hubungan, Nama)
6. Fill optional fields as needed
7. Use "Hapus" button to remove family members
8. Save the form

### For Developers

The family members data is accessible through the Karyawan model:

```php
$karyawan = Karyawan::find(1);
$familyMembers = $karyawan->familyMembers;

// Create new family member
$karyawan->familyMembers()->create([
    'hubungan' => 'ANAK',
    'nama' => 'John Doe',
    'tanggal_lahir' => '2010-01-01'
]);
```

## Migration Status

-   ✅ Migration created and executed successfully
-   ✅ Foreign key constraints working
-   ✅ Data integrity maintained

## Future Enhancements

-   Export family member data to Excel/PDF
-   Bulk import family members
-   Family member photo uploads
-   Emergency contact flagging
-   Family member age calculations
-   Dependent count automation for tax purposes
