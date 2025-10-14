# MASTER TUJUAN KEGIATAN UTAMA - SEARCH & PAGINATION ENHANCEMENT

## Summary

Added comprehensive search functionality, rows per page selection, and modern pagination to the Master Tujuan Kegiatan Utama (Master Data Transportasi) index page.

## Changes Made

### 1. Controller Updates

**File: `app/Http/Controllers/TujuanKegiatanUtamaController.php`**

#### Enhanced index() Method

-   **Added Request parameter** to handle search and pagination parameters
-   **Implemented search functionality** across multiple fields:
    -   `kode` (transportation code)
    -   `cabang` (branch)
    -   `wilayah` (region)
    -   `dari` (from location)
    -   `ke` (to location)
    -   `keterangan` (description)
-   **Dynamic pagination** with configurable per_page parameter (default: 15)
-   **Query parameter preservation** using `appends($request->query())`

### 2. View Enhancements

**File: `resources/views/master-tujuan-kegiatan-utama/index.blade.php`**

#### Added Search Section

-   **Modern search form** with comprehensive styling
-   **Real-time search input** with placeholder guidance
-   **Search button** with icon and hover effects
-   **Clear filter button** (shown when search is active)
-   **Search result indicator** showing total matches and search term

#### Added Rows Per Page Selection

-   **Dynamic per-page selection** (15, 50, 100 records)
-   **Parameter preservation** across page changes
-   **Result count display** showing current range and total

#### Added Modern Pagination

-   **Advanced pagination component** with jump-to-page functionality
-   **Smart page range display** (shows relevant page numbers)
-   **Total records and current range indicators**
-   **Navigation controls** (previous/next with disabled states)

## Technical Implementation

### Search Query Logic

```php
if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function($q) use ($search) {
        $q->where('kode', 'like', "%{$search}%")
          ->orWhere('cabang', 'like', "%{$search}%")
          ->orWhere('wilayah', 'like', "%{$search}%")
          ->orWhere('dari', 'like', "%{$search}%")
          ->orWhere('ke', 'like', "%{$search}%")
          ->orWhere('keterangan', 'like', "%{$search}%");
    });
}
```

### Pagination Implementation

```php
$perPage = $request->get('per_page', 15);
$tujuanKegiatanUtamas = $query->paginate($perPage)->appends($request->query());
```

### Component Integration

```blade
{{-- Search Form --}}
<form method="GET" action="{{ route('master.tujuan-kegiatan-utama.index') }}">
    <!-- Search input and controls -->
</form>

{{-- Rows Per Page --}}
@include('components.rows-per-page', [
    'routeName' => 'master.tujuan-kegiatan-utama.index',
    'paginator' => $tujuanKegiatanUtamas,
    'entityName' => 'data transportasi',
    'entityNamePlural' => 'data transportasi'
])

{{-- Modern Pagination --}}
@include('components.modern-pagination', [
    'paginator' => $tujuanKegiatanUtamas,
    'routeName' => 'master.tujuan-kegiatan-utama.index'
])
```

## Features Added

### 🔍 Advanced Search

-   **Multi-field search** across transportation data
-   **Real-time search feedback** with result counters
-   **Search term preservation** during pagination
-   **Clear filter functionality** to reset search

### 📊 Flexible Pagination

-   **Configurable page sizes**: 15, 50, or 100 records per page
-   **Jump to specific page** functionality
-   **Smart pagination range** showing relevant page numbers
-   **Complete navigation controls** with proper disabled states

### 💫 Enhanced User Experience

-   **Modern UI design** with proper spacing and typography
-   **Responsive layout** works on mobile and desktop
-   **Loading states** and transition effects
-   **Intuitive controls** with clear labeling and icons

## Benefits

### 1. **Improved Data Discovery**

-   Users can quickly find specific transportation routes
-   Multi-field search covers all relevant data points
-   Real-time feedback on search results

### 2. **Better Performance**

-   Pagination reduces page load times for large datasets
-   Configurable page sizes allow users to control data volume
-   Efficient database queries with proper indexing support

### 3. **Enhanced Usability**

-   Modern, intuitive interface design
-   Consistent with other master data pages
-   Mobile-responsive for field use

### 4. **Scalability**

-   Handles large transportation datasets efficiently
-   Search functionality scales with data growth
-   Pagination prevents performance issues

## Usage Instructions

### For Users

1. **Search**: Enter terms in search box to find transportation data
2. **Filter**: Use "Hapus Filter" to clear search and show all data
3. **Page Size**: Select 15, 50, or 100 records per page from dropdown
4. **Navigation**: Use pagination controls to browse through pages
5. **Jump**: Enter specific page number in pagination controls

### For Developers

-   Search parameters are preserved across all actions
-   Components are reusable across other master data pages
-   Styling follows established design system patterns
-   Database queries are optimized for performance

## Future Enhancements

-   **Advanced filters** by status, region, or date ranges
-   **Sorting functionality** for table columns
-   **Export filtered results** to CSV/Excel
-   **Bulk operations** on selected records
-   **Search suggestions** and autocomplete
