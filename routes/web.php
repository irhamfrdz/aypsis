<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KontainerController;
use App\Http\Controllers\KontainerImportController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\PajakController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\PekerjaanController;
use App\Http\Controllers\MasterBankController;

use App\Http\Controllers\TujuanController;
use App\Http\Controllers\TujuanKegiatanUtamaController;
use App\Http\Controllers\PermohonanController;
use App\Http\Controllers\MasterKegiatanController;
use App\Http\Controllers\PranotaSupirController;
use App\Http\Controllers\PembayaranPranotaSupirController;
use App\Http\Controllers\SupirDashboardController;
use App\Http\Controllers\CheckpointController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PricelistSewaKontainerController;
use App\Http\Controllers\PricelistCatController;
use App\Http\Controllers\PranotaTagihanCatController;
use App\Http\Controllers\TagihanCatController;
use App\Http\Controllers\PranotaTagihanKontainerSewaController;
use App\Http\Controllers\PembayaranPranotaKontainerController;
use App\Http\Controllers\PembayaranPranotaCatController;
use App\Http\Controllers\PembayaranPranotaPerbaikanController;
use App\Http\Controllers\PembayaranPranotaPerbaikanKontainerController;
use App\Http\Controllers\PembayaranPranotaSuratJalanController;
use App\Http\Controllers\PembayaranPranotaUangJalanController;
use App\Http\Controllers\AktivitasLainnyaController;
use App\Http\Controllers\PembayaranAktivitasLainnyaController;
use App\Http\Controllers\PembayaranUangMukaController;
use App\Http\Controllers\PembayaranObController;
use App\Http\Controllers\RealisasiUangMukaController;
use App\Http\Controllers\VendorBengkelController;
use App\Http\Controllers\TipeAkunController;
use App\Http\Controllers\PengirimController;
use App\Http\Controllers\JenisBarangController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\MasterTujuanKirimController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OutstandingController;
// use App\Http\Controllers\PranotaSuratJalanController; // Disabled - replaced with pranota uang jalan
use App\Http\Controllers\PranotaUangKenekController;
use App\Http\Controllers\GateInController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProspekController;
use App\Http\Controllers\NaikKapalController;
use App\Http\Controllers\OrderDataManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\SuratJalanBongkaranController;
use App\Http\Controllers\MasterPricelistObController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*//*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda. Rute ini
| dimuat oleh RouteServiceProvider dan semua rute tersebut akan
| ditetapkan ke grup middleware "web".
|
*/

// Rute untuk login dan logout
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Public onboarding routes for karyawan (first-time self-registration / onboarding)
// Onboarding crew checklist khusus untuk karyawan baru (public, tanpa auth)
Route::get('karyawan/{karyawan}/onboarding-crew-checklist', [App\Http\Controllers\KaryawanController::class, 'onboardingCrewChecklist'])
    ->name('karyawan.onboarding-crew-checklist');
// Route edit khusus untuk onboarding (public, tanpa middleware permission)
Route::get('karyawan/{karyawan}/onboarding-edit', [App\Http\Controllers\KaryawanController::class, 'onboardingEdit'])
    ->name('karyawan.onboarding-edit');
// Route update khusus untuk onboarding (public, tanpa middleware permission)
Route::put('karyawan/{karyawan}/onboarding-update', [App\Http\Controllers\KaryawanController::class, 'onboardingUpdate'])
    ->name('karyawan.onboarding-update');
// Route update crew checklist khusus untuk onboarding (public, tanpa middleware permission)
Route::post('karyawan/{karyawan}/onboarding-crew-checklist', [App\Http\Controllers\KaryawanController::class, 'updateCrewChecklistOnboarding'])
    ->name('karyawan.onboarding-crew-checklist.update');
// Diletakkan di atas agar tidak tertimpa oleh group admin/master
Route::get('karyawan/create', [AuthController::class, 'showKaryawanRegisterForm'])->name('karyawan.create');
Route::post('karyawan', [AuthController::class, 'registerKaryawan'])->name('karyawan.store');

// Registration routes (admin only)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('register/karyawan', [AuthController::class, 'showKaryawanRegisterForm'])->name('register.karyawan');
    Route::post('register/karyawan', [AuthController::class, 'registerKaryawan'])->name('register.karyawan.store');
    Route::get('register/user', [AuthController::class, 'showUserRegisterForm'])->name('register.user');
    Route::post('register/user', [AuthController::class, 'registerUser'])->name('register.user.store');
});

// Test Edit Payment Functionality
// Route untuk copy permission (di luar middleware auth agar bisa digunakan di form create)
Route::get('master/user/{user}/permissions-for-copy', [UserController::class, 'getUserPermissionsForCopy'])
     ->name('master.user.permissions-for-copy');

// Test route untuk debugging menu tujuan kirim
Route::get('test-tujuan-kirim', function () {
    return view('test-tujuan-kirim');
})->name('test.tujuan-kirim')->middleware('auth');

// Debug sidebar route
Route::get('debug-sidebar', function () {
    return view('debug-sidebar');
})->name('debug.sidebar')->middleware('auth');

// Test route untuk verifikasi tujuan kirim
Route::get('test-tujuan-kirim-route', function () {
    return view('test-tujuan-kirim-route');
})->name('test.tujuan-kirim.route')->middleware('auth');

// Test route tanpa middleware untuk debug navigasi
Route::get('/test-no-middleware', function() {
    return '<h1>Test Tanpa Middleware</h1><p>Ini test tanpa middleware apapun. Timestamp: ' . now() . '</p><a href="/dashboard">Ke Dashboard</a>';
})->name('test.no.middleware');

// Rute yang dilindungi middleware auth (tambahkan pemeriksaan karyawan, persetujuan, dan checklist ABK)
Route::middleware([
    'auth',
    \App\Http\Middleware\EnsureKaryawanPresent::class,
    \App\Http\Middleware\EnsureUserApproved::class,
    \App\Http\Middleware\EnsureCrewChecklistComplete::class,
])->group(function () {
    // Onboarding routes for authenticated users who need to create their Karyawan record.
    // These are intentionally named without the 'master.' prefix and do NOT use the
    // 'can:master-karyawan' gate so that newly created users (pending) can submit
    // their karyawan data on first login.
    // Route create/store karyawan untuk master/admin hanya gunakan prefix master/karyawan agar tidak bentrok dengan onboarding


          // Tagihan Kontainer Sewa routes removed - controller/views refactored by request
          // The old routes and resource controller were deleted to allow a full rewrite.
          // If you want them restored later, use your backup or reintroduce new routes/controllers.

    /*
    |===========================================================================
    | 🏠 DASHBOARD & CORE SYSTEM ROUTES
    |===========================================================================
    */

    // Arahkan root URL ke dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard utama untuk admin/staff - controller handles permission logic
    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');

/*
    |===========================================================================
    | �👥 USER & PERMISSION MANAGEMENT - Granular Permission System
    |===========================================================================
    | User administration with matrix-based permission management
    */

    // ═══════════════════════════════════════════════════════════════════════
    // 👤 USER MANAGEMENT - Full CRUD + Permission Management
    // ═══════════════════════════════════════════════════════════════════════

    // ═══════════════════════════════════════════════════════════════════════
    // 👤 USER MANAGEMENT - Resource Routes with Granular Permissions
    // ═══════════════════════════════════════════════════════════════════════

    Route::prefix('master')->name('master.')->middleware(['auth'])->group(function() {
        // Core User CRUD operations - Resource routes with granular permissions
        Route::resource('user', UserController::class)->middleware([
            'index' => 'can:master-user-view',
            'show' => 'can:master-user-view',
            'create' => 'can:master-user-create',
            'store' => 'can:master-user-create',
            'edit' => 'can:master-user-update',
            'update' => 'can:master-user-update',
            'destroy' => 'can:master-user-delete'
        ]);

        // Additional user management routes with specific permissions
        Route::prefix('user')->name('user.')->group(function() {
            Route::post('bulk-assign-permissions', [UserController::class, 'bulkAssignPermissions'])
                ->name('bulk-assign-permissions')
                ->middleware('can:master-user-bulk-manage');
            Route::get('bulk-manage', [UserController::class, 'bulkManage'])
                ->name('bulk-manage')
                ->middleware('can:master-user-bulk-manage');
            Route::get('{user}/permissions', [UserController::class, 'getUserPermissions'])
                ->name('get-permissions')
                ->middleware('can:master-user-view');
            Route::post('{user}/assign-template', [UserController::class, 'assignTemplate'])
                ->name('assign-template')
                ->middleware('can:master-user-update');
        });
    });

    /*
    |===========================================================================
    | 📊 MASTER DATA MANAGEMENT ROUTES
    |===========================================================================
    | All master data CRUD operations organized alphabetically for easy navigation
    */

    Route::prefix('master')->name('master.')->group(function() {
     Route::post('pengirim-import', [App\Http\Controllers\PengirimController::class, 'import'])->name('pengirim.import.process')->middleware('can:master-pengirim-create');

        // ═══════════════════════════════════════════════════════════════════════
        // 👥 KARYAWAN (EMPLOYEE) MANAGEMENT
        // ═══════════════════════════════════════════════════════════════════════

        // Print all karyawan (print-friendly)
        Route::get('karyawan/print', [KaryawanController::class, 'print'])
             ->name('karyawan.print')
             ->middleware('can:master-karyawan-print');

        // Print single karyawan (form layout)
        Route::get('karyawan/{karyawan}/print', [KaryawanController::class, 'printSingle'])
             ->name('karyawan.print.single')
             ->middleware('can:master-karyawan-print');

        // Import karyawan from CSV (simple uploader)
        Route::get('karyawan/import', [KaryawanController::class, 'importForm'])
             ->name('karyawan.import')
             ->middleware(['auth', 'can:master-karyawan-create']);

        Route::post('karyawan/import', [KaryawanController::class, 'importStore'])
             ->name('karyawan.import.store')
             ->middleware(['auth', 'can:master-karyawan-create']);

        // Export/download CSV of all karyawan
        Route::get('karyawan/export', [KaryawanController::class, 'export'])
             ->name('karyawan.export')
             ->middleware('can:master-karyawan-export');

        // Export Excel-formatted CSV to prevent scientific notation
        Route::get('karyawan/export-excel', [KaryawanController::class, 'exportExcel'])
             ->name('karyawan.export-excel')
             ->middleware('can:master-karyawan-export');

        // Export untuk Excel Indonesia (comma delimiter dengan quotes untuk mengatasi koma dalam data)
        Route::get('karyawan/export-excel-indonesia', [KaryawanController::class, 'exportExcelIndonesia'])
             ->name('karyawan.export-excel-indonesia')
             ->middleware('can:master-karyawan-export');

        // Download CSV template for import (no special permission needed)
        Route::get('karyawan/template', [KaryawanController::class, 'downloadTemplate'])
             ->name('karyawan.template');

        // Download Excel template for import (no special permission needed)
        Route::get('karyawan/excel-template', [KaryawanController::class, 'downloadExcelTemplate'])
             ->name('karyawan.excel-template');

        // Download simple Excel template for import (headers only, no special permission needed)
        Route::get('karyawan/simple-excel-template', [KaryawanController::class, 'downloadSimpleExcelTemplate'])
             ->name('karyawan.simple-excel-template');

        // Crew checklist for ABK employees
        Route::get('karyawan/{karyawan}/crew-checklist', [KaryawanController::class, 'crewChecklist'])
            ->name('karyawan.crew-checklist')
            ->middleware('can:master-karyawan-view');

        // NEW: Simplified crew checklist page
        Route::get('karyawan/{karyawan}/crew-checklist-new', [KaryawanController::class, 'crewChecklistNew'])
            ->name('karyawan.crew-checklist-new')
            ->middleware('can:master-karyawan-view');

        Route::post('karyawan/{karyawan}/crew-checklist', [KaryawanController::class, 'updateCrewChecklist'])
            ->name('karyawan.crew-checklist.update')
            ->middleware('can:master-karyawan-update');

        Route::get('karyawan/{karyawan}/crew-checklist/print', [KaryawanController::class, 'printCrewChecklist'])
            ->name('karyawan.crew-checklist.print')
            ->middleware('can:master-karyawan-view');

        // Individual routes for karyawan with specific permissions (except index which is defined outside master group)
        Route::get('karyawan/create', [KaryawanController::class, 'create'])
             ->name('karyawan.create')
             ->middleware('can:master-karyawan-create');
        Route::post('karyawan', [KaryawanController::class, 'store'])
             ->name('karyawan.store')
             ->middleware('can:master-karyawan-create');
        Route::get('karyawan/get-next-nik', [KaryawanController::class, 'getNextNik'])
             ->name('karyawan.get-next-nik')
             ->middleware('can:master-karyawan-create');
         Route::get('karyawan/{karyawan}', [KaryawanController::class, 'show'])
              ->name('karyawan.show')
              ->middleware('can:master-karyawan-view');
         Route::get('karyawan/{karyawan}/edit', [KaryawanController::class, 'edit'])
              ->name('karyawan.edit')
              ->middleware('can:master-karyawan-update');
         Route::put('karyawan/{karyawan}', [KaryawanController::class, 'update'])
              ->name('karyawan.update')
              ->middleware('can:master-karyawan-update');
         Route::patch('karyawan/{karyawan}/update-catatan-pekerjaan', [KaryawanController::class, 'updateCatatanPekerjaan'])
              ->name('karyawan.update-catatan-pekerjaan')
              ->middleware('can:master-karyawan-update');
         Route::delete('karyawan/{karyawan}', [KaryawanController::class, 'destroy'])
              ->name('karyawan.destroy')
              ->middleware('can:master-karyawan-delete');

        // Master kontainer routes (with master prefix) - granular permissions
        Route::get('kontainer', [KontainerController::class, 'index'])
             ->name('kontainer.index')
             ->middleware('can:master-kontainer-view');
        Route::get('kontainer/create', [KontainerController::class, 'create'])
             ->name('kontainer.create')
             ->middleware('can:master-kontainer-create');

        // Master kontainer import/export routes (MUST BE BEFORE {kontainer} routes)
        Route::get('kontainer/download-template', [KontainerImportController::class, 'downloadTemplate'])
             ->name('kontainer.download-template');
        Route::get('kontainer/export', [KontainerImportController::class, 'export'])
             ->name('kontainer.export')
             ->middleware('can:master-kontainer-view');
        Route::post('kontainer/import', [KontainerImportController::class, 'import'])
             ->name('kontainer.import')
             ->middleware('can:master-kontainer-create');

        Route::post('kontainer', [KontainerController::class, 'store'])
             ->name('kontainer.store')
             ->middleware('can:master-kontainer-create');
        Route::get('kontainer/{kontainer}', [KontainerController::class, 'show'])
             ->name('kontainer.show')
             ->middleware('can:master-kontainer-view');
        Route::get('kontainer/{kontainer}/edit', [KontainerController::class, 'edit'])
             ->name('kontainer.edit')
             ->middleware('can:master-kontainer-update');
        Route::put('kontainer/{kontainer}', [KontainerController::class, 'update'])
             ->name('kontainer.update')
             ->middleware('can:master-kontainer-update');
        Route::delete('kontainer/{kontainer}', [KontainerController::class, 'destroy'])
             ->name('kontainer.destroy')
             ->middleware('can:master-kontainer-delete');

        // Master tujuan routes - CONVERTED TO RESOURCE (7 routes → 1 line) with permissions
        // Import/Export routes (must be BEFORE resource routes)
        Route::get('tujuan/template', [App\Http\Controllers\MasterTujuanImportController::class, 'downloadTemplate'])
             ->name('tujuan.template');
        Route::post('tujuan/import', [App\Http\Controllers\MasterTujuanImportController::class, 'import'])
             ->name('tujuan.import')
             ->middleware('can:master-tujuan-create');

        Route::resource('tujuan', TujuanController::class)->middleware([
            'index' => 'can:master-tujuan-view',
            'show' => 'can:master-tujuan-view'
        ]);

        // Master kegiatan routes (with master prefix) - granular permissions
        Route::get('kegiatan', [MasterKegiatanController::class, 'index'])
             ->name('kegiatan.index')
             ->middleware('can:master-kegiatan-view');
        Route::get('kegiatan/create', [MasterKegiatanController::class, 'create'])
             ->name('kegiatan.create')
             ->middleware('can:master-kegiatan-create');
        Route::post('kegiatan', [MasterKegiatanController::class, 'store'])
             ->name('kegiatan.store')
             ->middleware('can:master-kegiatan-create');
        Route::get('kegiatan/{kegiatan}', [MasterKegiatanController::class, 'show'])
             ->name('kegiatan.show')
             ->middleware('can:master-kegiatan-view');
        Route::get('kegiatan/{kegiatan}/edit', [MasterKegiatanController::class, 'edit'])
             ->name('kegiatan.edit')
             ->middleware('can:master-kegiatan-update');
        Route::put('kegiatan/{kegiatan}', [MasterKegiatanController::class, 'update'])
             ->name('kegiatan.update')
             ->middleware('can:master-kegiatan-update');
        Route::delete('kegiatan/{kegiatan}', [MasterKegiatanController::class, 'destroy'])
             ->name('kegiatan.destroy')
             ->middleware('can:master-kegiatan-delete');
        // CSV import/export helpers for Master Kegiatan
        Route::get('kegiatan/template/csv', [MasterKegiatanController::class, 'downloadTemplate'])
             ->name('kegiatan.template')
             ->middleware('can:master-kegiatan-view');

        Route::post('kegiatan/import/csv', [MasterKegiatanController::class, 'importCsv'])
             ->name('kegiatan.import')
             ->middleware('can:master-kegiatan-view');

        Route::get('kegiatan/export/csv', [MasterKegiatanController::class, 'exportCsv'])
             ->name('kegiatan.export')
             ->middleware('can:master-kegiatan-view');



        // Master tujuan kegiatan utama routes
        Route::get('tujuan-kegiatan-utama', [TujuanKegiatanUtamaController::class, 'index'])
             ->name('tujuan-kegiatan-utama.index')
             ->middleware('can:master-tujuan-kirim-view');
        Route::get('tujuan-kegiatan-utama/create', [TujuanKegiatanUtamaController::class, 'create'])
             ->name('tujuan-kegiatan-utama.create')
             ->middleware('can:master-tujuan-kirim-create');

        // Export/Print routes for Master Tujuan Kegiatan Utama (HARUS SEBELUM RESOURCE ROUTES)
        Route::get('tujuan-kegiatan-utama/export', [TujuanKegiatanUtamaController::class, 'export'])
             ->name('tujuan-kegiatan-utama.export')
             ->middleware('can:master-tujuan-kirim-view');
        Route::get('tujuan-kegiatan-utama/print', [TujuanKegiatanUtamaController::class, 'print'])
             ->name('tujuan-kegiatan-utama.print')
             ->middleware('can:master-tujuan-kirim-view');

        // Template dan Import routes for Master Tujuan Kegiatan Utama (HARUS SEBELUM RESOURCE ROUTES)
        Route::get('tujuan-kegiatan-utama/download-template', [TujuanKegiatanUtamaController::class, 'downloadTemplate'])
             ->name('tujuan-kegiatan-utama.download-template')
             ->middleware('can:master-tujuan-kirim-view');
        Route::get('tujuan-kegiatan-utama/import-form', [TujuanKegiatanUtamaController::class, 'showImportForm'])
             ->name('tujuan-kegiatan-utama.import-form')
             ->middleware('can:master-tujuan-kirim-create');
        Route::post('tujuan-kegiatan-utama/import', [TujuanKegiatanUtamaController::class, 'import'])
             ->name('tujuan-kegiatan-utama.import')
             ->middleware('can:master-tujuan-kirim-create');

        // Resource routes (HARUS SETELAH ROUTES SPESIFIK)
        Route::post('tujuan-kegiatan-utama', [TujuanKegiatanUtamaController::class, 'store'])
             ->name('tujuan-kegiatan-utama.store')
             ->middleware('can:master-tujuan-kirim-create');
        Route::get('tujuan-kegiatan-utama/{tujuan_kegiatan_utama}', [TujuanKegiatanUtamaController::class, 'show'])
             ->name('tujuan-kegiatan-utama.show')
             ->middleware('can:master-tujuan-kirim-view');
        Route::get('tujuan-kegiatan-utama/{tujuan_kegiatan_utama}/edit', [TujuanKegiatanUtamaController::class, 'edit'])
             ->name('tujuan-kegiatan-utama.edit')
             ->middleware('can:master-tujuan-kirim-update');
        Route::put('tujuan-kegiatan-utama/{tujuan_kegiatan_utama}', [TujuanKegiatanUtamaController::class, 'update'])
             ->name('tujuan-kegiatan-utama.update')
             ->middleware('can:master-tujuan-kirim-update');
        Route::delete('tujuan-kegiatan-utama/{tujuan_kegiatan_utama}', [TujuanKegiatanUtamaController::class, 'destroy'])
             ->name('tujuan-kegiatan-utama.destroy')
             ->middleware('can:master-tujuan-kirim-delete');

        // Master permission routes (with master prefix) - granular permissions
        Route::get('permission', [PermissionController::class, 'index'])
             ->name('permission.index')
             ->middleware('can:master-permission-view');
        Route::get('permission/create', [PermissionController::class, 'create'])
             ->name('permission.create')
             ->middleware('can:master-permission-create');
        Route::post('permission', [PermissionController::class, 'store'])
             ->name('permission.store')
             ->middleware('can:master-permission-create');
        Route::get('permission/{permission}', [PermissionController::class, 'show'])
             ->name('permission.show')
             ->middleware('can:master-permission-view');
        Route::get('permission/{permission}/edit', [PermissionController::class, 'edit'])
             ->name('permission.edit')
             ->middleware('can:master-permission-update');
        Route::put('permission/{permission}', [PermissionController::class, 'update'])
             ->name('permission.update')
             ->middleware('can:master-permission-update');
        Route::delete('permission/{permission}', [PermissionController::class, 'destroy'])
             ->name('permission.destroy')
             ->middleware('can:master-permission-delete');

        // Additional permission routes
        Route::post('permission/sync', [PermissionController::class, 'sync'])
             ->name('permission.sync')
             ->middleware('can:master-permission-view');

        Route::post('permission/bulk-delete', [PermissionController::class, 'bulkDelete'])
             ->name('permission.bulk-delete')
             ->middleware('can:master-permission-view');

        Route::post('permission/{permission}/assign-users', [PermissionController::class, 'assignUsers'])
             ->name('permission.assign-users')
             ->middleware('can:master-permission-view');

        Route::get('permission/{permission}/users', [PermissionController::class, 'getUsers'])
             ->name('permission.users')
             ->middleware('can:master-permission-view');

        // Master mobil routes - CONVERTED TO RESOURCE (7 routes → 1 line) with permissions
        // Specific routes MUST come before resource routes to avoid conflicts
        Route::get('mobil/template', [App\Http\Controllers\MasterMobilImportController::class, 'downloadTemplate'])
             ->name('mobil.template')
             ->middleware('can:master-mobil-view');
        Route::post('mobil/import', [App\Http\Controllers\MasterMobilImportController::class, 'import'])
             ->name('mobil.import')
             ->middleware('can:master-mobil-create');
        Route::get('mobil/export', [App\Http\Controllers\MasterMobilImportController::class, 'export'])
             ->name('mobil.export')
             ->middleware('can:master-mobil-view');

        Route::resource('mobil', MobilController::class)->middleware([
            'index' => 'can:master-mobil-view',
            'show' => 'can:master-mobil-view',
            'create' => 'can:master-mobil-create',
            'store' => 'can:master-mobil-create',
            'edit' => 'can:master-mobil-update',
            'update' => 'can:master-mobil-update',
            'destroy' => 'can:master-mobil-delete'
        ]);

        // Master pricelist sewa kontainer routes (with master prefix) - granular permissions
        Route::get('pricelist-sewa-kontainer', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'index'])
             ->name('master.pricelist-sewa-kontainer.index')
             ->middleware('can:master-pricelist-sewa-kontainer-view');
        Route::get('pricelist-sewa-kontainer/create', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'create'])
             ->name('master.pricelist-sewa-kontainer.create')
             ->middleware('can:master-pricelist-sewa-kontainer-create');
        Route::post('pricelist-sewa-kontainer', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'store'])
             ->name('master.pricelist-sewa-kontainer.store')
             ->middleware('can:master-pricelist-sewa-kontainer-create');

        // Import/Export routes for pricelist sewa kontainer (must come before parameterized routes)
        Route::get('pricelist-sewa-kontainer/export-template', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'exportTemplate'])
             ->name('master.pricelist-sewa-kontainer.export-template')
             ->middleware('can:master-pricelist-sewa-kontainer-view');
        Route::post('pricelist-sewa-kontainer/import', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'import'])
             ->name('master.pricelist-sewa-kontainer.import')
             ->middleware('can:master-pricelist-sewa-kontainer-create');

        // Parameterized routes (must come after specific routes)
        Route::get('pricelist-sewa-kontainer/{pricelist_sewa_kontainer}', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'show'])
             ->name('master.pricelist-sewa-kontainer.show')
             ->middleware('can:master-pricelist-sewa-kontainer-view');
        Route::get('pricelist-sewa-kontainer/{pricelist_sewa_kontainer}/edit', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'edit'])
             ->name('master.pricelist-sewa-kontainer.edit')
             ->middleware('can:master-pricelist-sewa-kontainer-update');
        Route::put('pricelist-sewa-kontainer/{pricelist_sewa_kontainer}', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'update'])
             ->name('master.pricelist-sewa-kontainer.update')
             ->middleware('can:master-pricelist-sewa-kontainer-update');
        Route::delete('pricelist-sewa-kontainer/{pricelist_sewa_kontainer}', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'destroy'])
             ->name('master.pricelist-sewa-kontainer.destroy')
             ->middleware('can:master-pricelist-sewa-kontainer-delete');

        // Master pricelist cat routes - CONVERTED TO RESOURCE (7 routes → 1 line) with permissions
        // Import/Export routes (must be BEFORE resource routes)
        Route::get('pricelist-cat/template', [App\Http\Controllers\MasterPricelistCatImportController::class, 'downloadTemplate'])
             ->name('pricelist-cat.template');
        Route::post('pricelist-cat/import', [App\Http\Controllers\MasterPricelistCatImportController::class, 'import'])
             ->name('pricelist-cat.import')
             ->middleware('can:master-pricelist-cat-create');

        Route::resource('pricelist-cat', PricelistCatController::class)->middleware([
            'index' => 'can:master-pricelist-cat-view',
            'show' => 'can:master-pricelist-cat-view',
            'create' => 'can:master-pricelist-cat-create',
            'store' => 'can:master-pricelist-cat-create',
            'edit' => 'can:master-pricelist-cat-update',
            'update' => 'can:master-pricelist-cat-update',
            'destroy' => 'can:master-pricelist-cat-delete'
        ]);

        // Master pricelist gate in routes - granular permissions
        // Import/Export routes (must be BEFORE resource routes)
        Route::get('pricelist-gate-in/import', [\App\Http\Controllers\PricelistGateInController::class, 'import'])
             ->name('pricelist-gate-in.import')
             ->middleware('can:master-pricelist-gate-in-create');
        Route::post('pricelist-gate-in/import/process', [\App\Http\Controllers\PricelistGateInController::class, 'importProcess'])
             ->name('pricelist-gate-in.import.process')
             ->middleware('can:master-pricelist-gate-in-create');
        Route::get('pricelist-gate-in/download-template', [\App\Http\Controllers\PricelistGateInController::class, 'downloadTemplate'])
             ->name('pricelist-gate-in.download-template')
             ->middleware('can:master-pricelist-gate-in-view');

        Route::resource('pricelist-gate-in', \App\Http\Controllers\PricelistGateInController::class)->middleware([
            'index' => 'can:master-pricelist-gate-in-view',
            'show' => 'can:master-pricelist-gate-in-view',
            'create' => 'can:master-pricelist-gate-in-create',
            'store' => 'can:master-pricelist-gate-in-create',
            'edit' => 'can:master-pricelist-gate-in-update',
            'update' => 'can:master-pricelist-gate-in-update',
            'destroy' => 'can:master-pricelist-gate-in-delete'
        ]);

        // Master pricelist OB routes - resource with permissions
        Route::resource('pricelist-ob', MasterPricelistObController::class)->middleware([
            'index' => 'can:master-pricelist-ob-view',
            'show' => 'can:master-pricelist-ob-view',
            'create' => 'can:master-pricelist-ob-create',
            'store' => 'can:master-pricelist-ob-create',
            'edit' => 'can:master-pricelist-ob-update',
            'update' => 'can:master-pricelist-ob-update',
            'destroy' => 'can:master-pricelist-ob-delete'
        ]);

        // Download template for divisi import
        Route::get('divisi/download-template', [DivisiController::class, 'downloadTemplate'])
             ->name('divisi.download-template')
             ->middleware('can:master-divisi-view');

        // Download template for pajak import
        Route::get('pajak/download-template', [PajakController::class, 'downloadTemplate'])
             ->name('pajak.download-template')
             ->middleware(['auth', 'can:master-pajak-view']);

        // Download template for bank import
        Route::get('bank/download-template', [MasterBankController::class, 'downloadTemplate'])
             ->name('bank.download-template')
             ->middleware('can:master-bank-view');

        // Download template for coa import
        Route::get('coa/download-template', [\App\Http\Controllers\MasterCoaController::class, 'downloadTemplate'])
             ->name('coa.download-template')
             ->middleware('can:master-coa-view');



    });

// ⚡ Master Bank Routes - Clean implementation without middleware duplication
Route::middleware([
    'auth',
    \App\Http\Middleware\EnsureKaryawanPresent::class,
    \App\Http\Middleware\EnsureUserApproved::class,
    \App\Http\Middleware\EnsureCrewChecklistComplete::class,
])->group(function() {
    // Bank resource routes with exact names expected by views
    Route::get('master/bank', [MasterBankController::class, 'index'])
         ->name('master-bank-index')
         ->middleware('can:master-bank-view');
    Route::get('master/bank/create', [MasterBankController::class, 'create'])
         ->name('master-bank-create')
         ->middleware('can:master-bank-create');
    Route::post('master/bank', [MasterBankController::class, 'store'])
         ->name('master-bank-store')
         ->middleware('can:master-bank-create');
    Route::get('master/bank/{bank}', [MasterBankController::class, 'show'])
         ->name('master-bank-show')
         ->middleware('can:master-bank-view');
    Route::get('master/bank/{bank}/edit', [MasterBankController::class, 'edit'])
         ->name('master-bank-edit')
         ->middleware('can:master-bank-update');
    Route::put('master/bank/{bank}', [MasterBankController::class, 'update'])
         ->name('master-bank-update')
         ->middleware('can:master-bank-update');
    Route::delete('master/bank/{bank}', [MasterBankController::class, 'destroy'])
         ->name('master-bank-destroy')
         ->middleware('can:master-bank-destroy');
    Route::post('master/bank/import', [MasterBankController::class, 'import'])
         ->name('master-bank-import')
         ->middleware('can:master-bank-create');
});

// Master divisi routes with required middleware - HYBRID: Resource + additional routes with permissions
Route::middleware([
    'auth',
    \App\Http\Middleware\EnsureKaryawanPresent::class,
    \App\Http\Middleware\EnsureUserApproved::class,
    \App\Http\Middleware\EnsureCrewChecklistComplete::class,
])->group(function () {
    Route::resource('master/divisi', DivisiController::class)->names('master.divisi')->middleware([
        'index' => 'can:master-divisi-view',
        'show' => 'can:master-divisi-view',
        'create' => 'can:master-divisi-create',
        'store' => 'can:master-divisi-create',
        'edit' => 'can:master-divisi-update',
        'update' => 'can:master-divisi-update',
        'destroy' => 'can:master-divisi-delete'
    ]);
    Route::post('master/divisi/import', [DivisiController::class, 'import'])
         ->name('master.divisi.import')
         ->middleware('can:master-divisi-create');
});

// Additional Master Data Routes with required middleware
Route::middleware([
    'auth',
    \App\Http\Middleware\EnsureKaryawanPresent::class,
    \App\Http\Middleware\EnsureUserApproved::class,
    \App\Http\Middleware\EnsureCrewChecklistComplete::class,
])->group(function () {

    // Master pajak routes - HYBRID: Resource + additional routes with permissions
    Route::resource('master/pajak', PajakController::class)->names('master.pajak')->middleware([
        'index' => 'can:master-pajak-view',
        'show' => 'can:master-pajak-view',
        'create' => 'can:master-pajak-create',
        'store' => 'can:master-pajak-create',
        'edit' => 'can:master-pajak-update',
        'update' => 'can:master-pajak-update',
        'destroy' => 'can:master-pajak-destroy'
    ]);
    Route::post('master/pajak/import', [PajakController::class, 'import'])
         ->name('master.pajak.import')
         ->middleware('can:master-pajak-create');



    // ═══════════════════════════════════════════════════════════════════════
    // 🏗️ CORE MASTER DATA (SIMPLE RESOURCES) - Alphabetical Order
    // ═══════════════════════════════════════════════════════════════════════

    // 🏢 Cabang (Branch) Management with permissions
    Route::resource('master/cabang', CabangController::class)
         ->names('master.cabang')
         ->middleware([
             'index' => 'can:master-cabang-view',
             'show' => 'can:master-cabang-view'
         ]);

    // Master COA routes - HYBRID: Resource + constraints + additional routes with permissions
    Route::resource('master/coa', \App\Http\Controllers\MasterCoaController::class)
         ->names([
             'index' => 'master-coa-index',
             'create' => 'master-coa-create',
             'store' => 'master-coa-store',
             'show' => 'master-coa-show',
             'edit' => 'master-coa-edit',
             'update' => 'master-coa-update',
             'destroy' => 'master-coa-destroy'
         ])
         ->middleware([
             'index' => 'can:master-coa-view',
             'show' => 'can:master-coa-view'
         ])
         ->where(['coa' => '[0-9]+']);
    Route::post('master/coa/import', [\App\Http\Controllers\MasterCoaController::class, 'import'])
         ->name('master-coa-import')
         ->middleware('can:master-coa-create');
    Route::get('master/coa/export', [\App\Http\Controllers\MasterCoaController::class, 'export'])
         ->name('master-coa-export')
         ->middleware('can:master-coa-view');
    Route::get('master/coa/{coa}/ledger', [\App\Http\Controllers\MasterCoaController::class, 'ledger'])
         ->name('master-coa-ledger')
         ->middleware('can:master-coa-view')
         ->where(['coa' => '[0-9]+']);
    Route::get('master/coa/{coa}/ledger/print', [\App\Http\Controllers\MasterCoaController::class, 'ledgerPrint'])
         ->name('master-coa-ledger-print')
         ->middleware('can:master-coa-view')
         ->where(['coa' => '[0-9]+']);

    // Master pekerjaan routes - HYBRID: Resource + additional routes with permissions
    Route::resource('master/pekerjaan', PekerjaanController::class)->names('master.pekerjaan')->middleware([
        'index' => 'can:master-pekerjaan-view',
        'show' => 'can:master-pekerjaan-view',
        'create' => 'can:master-pekerjaan-create',
        'store' => 'can:master-pekerjaan-create',
        'edit' => 'can:master-pekerjaan-update',
        'update' => 'can:master-pekerjaan-update',
        'destroy' => 'can:master-pekerjaan-destroy'
    ]);
    Route::get('master/pekerjaan/export-template', [PekerjaanController::class, 'exportTemplate'])
         ->name('master.pekerjaan.export-template')
         ->middleware('can:master-pekerjaan-view');
    Route::post('master/pekerjaan/import', [PekerjaanController::class, 'import'])
         ->name('master.pekerjaan.import')
         ->middleware('can:master-pekerjaan-create');

    // 🔧 Master Vendor Bengkel (Workshop Vendor) - HYBRID: Resource + additional routes with permissions
    // Specific routes MUST come before resource routes to avoid conflicts
    Route::get('master/vendor-bengkel/export-template', [VendorBengkelController::class, 'exportTemplate'])
         ->name('master.vendor-bengkel.export-template')
         ->middleware('can:master-vendor-bengkel-view');
    Route::post('master/vendor-bengkel/import', [VendorBengkelController::class, 'import'])
         ->name('master.vendor-bengkel.import')
         ->middleware('can:master-vendor-bengkel-create');

    Route::resource('master/vendor-bengkel', VendorBengkelController::class)
         ->names('master.vendor-bengkel')
         ->middleware([
             'index' => 'can:master-vendor-bengkel-view',
             'show' => 'can:master-vendor-bengkel-view',
             'create' => 'can:master-vendor-bengkel-create',
             'store' => 'can:master-vendor-bengkel-create',
             'edit' => 'can:master-vendor-bengkel-update',
             'update' => 'can:master-vendor-bengkel-update',
             'destroy' => 'can:master-vendor-bengkel-delete'
         ]);

    // 🔢 Kode Nomor (Number Code) Management with permissions
    Route::resource('master/kode-nomor', \App\Http\Controllers\KodeNomorController::class)
         ->names('master.kode-nomor')
         ->middleware([
             'index' => 'can:master-kode-nomor-view',
             'show' => 'can:master-kode-nomor-view'
         ]);

    // Stock Kontainer Import/Export routes (must be BEFORE resource routes)
    Route::get('master/stock-kontainer/template', [App\Http\Controllers\StockKontainerImportController::class, 'downloadTemplate'])
         ->name('master.stock-kontainer.template');

    Route::post('master/stock-kontainer/import', [App\Http\Controllers\StockKontainerImportController::class, 'import'])
         ->name('master.stock-kontainer.import')
         ->middleware('can:master-stock-kontainer-create');

    // 📊 Stock Kontainer (Container Stock) Management with permissions
    Route::resource('master/stock-kontainer', \App\Http\Controllers\StockKontainerController::class)
         ->names('master.stock-kontainer')
         ->middleware([
             'index' => 'can:master-stock-kontainer-view',
             'show' => 'can:master-stock-kontainer-view',
             'create' => 'can:master-stock-kontainer-create',
             'store' => 'can:master-stock-kontainer-create',
             'edit' => 'can:master-stock-kontainer-update',
             'update' => 'can:master-stock-kontainer-update',
             'destroy' => 'can:master-stock-kontainer-delete'
         ]);

    // 🚢 Master Kapal - Download Template & Import (must be BEFORE resource routes)
    Route::get('master-kapal/download-template', [\App\Http\Controllers\MasterKapalController::class, 'downloadTemplate'])
         ->name('master-kapal.download-template')
         ->middleware('can:master-kapal.view');

    Route::get('master-kapal/import', [\App\Http\Controllers\MasterKapalController::class, 'importForm'])
         ->name('master-kapal.import-form')
         ->middleware('can:master-kapal.create');

    Route::post('master-kapal/import', [\App\Http\Controllers\MasterKapalController::class, 'import'])
         ->name('master-kapal.import')
         ->middleware('can:master-kapal.create');

    Route::get('master-kapal/export', [\App\Http\Controllers\MasterKapalController::class, 'export'])
         ->name('master-kapal.export')
         ->middleware('can:master-kapal.view');

    // 🚢 Master Kapal (Ship Master) Management with permissions
    Route::resource('master-kapal', \App\Http\Controllers\MasterKapalController::class)
         ->names('master-kapal')
         ->middleware([
             'index' => 'can:master-kapal.view',
             'show' => 'can:master-kapal.view',
             'create' => 'can:master-kapal.create',
             'store' => 'can:master-kapal.create',
             'edit' => 'can:master-kapal.edit',
             'update' => 'can:master-kapal.edit',
             'destroy' => 'can:master-kapal.delete'
         ]);

    // 💰 Uang Jalan Batam Management with permissions
    Route::resource('uang-jalan-batam', \App\Http\Controllers\UangJalanBatamController::class)
         ->names('uang-jalan-batam')
         ->middleware([
             'index' => 'can:uang-jalan-batam.view',
             'show' => 'can:uang-jalan-batam.view',
             'create' => 'can:uang-jalan-batam.create',
             'store' => 'can:uang-jalan-batam.create',
             'edit' => 'can:uang-jalan-batam.edit',
             'update' => 'can:uang-jalan-batam.edit',
             'destroy' => 'can:uang-jalan-batam.delete'
         ]);

    // 💰 Uang Jalan Batam Import/Export routes
    Route::prefix('uang-jalan-batam')->group(function () {
        Route::get('/download-template', [\App\Http\Controllers\UangJalanBatamController::class, 'downloadTemplate'])
             ->name('uang-jalan-batam.download-template')
             ->middleware('can:uang-jalan-batam.create');
        Route::get('/import', [\App\Http\Controllers\UangJalanBatamController::class, 'importForm'])
             ->name('uang-jalan-batam.import-form')
             ->middleware('can:uang-jalan-batam.create');
        Route::post('/import', [\App\Http\Controllers\UangJalanBatamController::class, 'import'])
             ->name('uang-jalan-batam.import')
             ->middleware('can:uang-jalan-batam.create');
    });

    // ⚓ Master Pelabuhan (Port Master) Management with permissions
    Route::resource('master-pelabuhan', \App\Http\Controllers\MasterPelabuhanController::class)
         ->names([
             'index' => 'master-pelabuhan.index',
             'create' => 'master-pelabuhan.create',
             'store' => 'master-pelabuhan.store',
             'show' => 'master-pelabuhan.show',
             'edit' => 'master-pelabuhan.edit',
             'update' => 'master-pelabuhan.update',
             'destroy' => 'master-pelabuhan.destroy'
         ])
         ->parameters(['master-pelabuhan' => 'masterPelabuhan'])
         ->middleware([
             'index' => 'can:master-pelabuhan-view',
             'create' => 'can:master-pelabuhan-create',
             'store' => 'can:master-pelabuhan-create',
             'show' => 'can:master-pelabuhan-view',
             'edit' => 'can:master-pelabuhan-edit',
             'update' => 'can:master-pelabuhan-edit',
             'destroy' => 'can:master-pelabuhan-delete'
         ]);

    // 🏦 Tipe Akun (Account Type) Management with permissions
    Route::resource('master/tipe-akun', TipeAkunController::class)->names('master.tipe-akun')->middleware([
        'index' => 'can:master-tipe-akun-view',
        'show' => 'can:master-tipe-akun-view'
    ]);

    // 📋 Nomor Terakhir (Last Number) Management with permissions
    Route::resource('master/nomor-terakhir', \App\Http\Controllers\NomorTerakhirController::class)
         ->names('master.nomor-terakhir')
         ->middleware([
             'index' => 'can:master-nomor-terakhir-view',
             'show' => 'can:master-nomor-terakhir-view'
         ]);

    // 📦 Pengirim (Sender) Management with permissions
    Route::resource('master/pengirim', PengirimController::class)
         ->names('pengirim')
         ->middleware([
             'index' => 'can:master-pengirim-view',
             'create' => 'can:master-pengirim-create',
             'store' => 'can:master-pengirim-create',
             'show' => 'can:master-pengirim-view',
             'edit' => 'can:master-pengirim-update',
             'update' => 'can:master-pengirim-update',
             'destroy' => 'can:master-pengirim-delete'
         ]);

    // 📥 Pengirim - Download Template & Import CSV
    Route::get('master/pengirim-download-template', [PengirimController::class, 'downloadTemplate'])
         ->name('pengirim.download-template')
         ->middleware('can:master-pengirim-view');

    Route::get('master/pengirim-import', [PengirimController::class, 'showImport'])
         ->name('pengirim.import')
         ->middleware('can:master-pengirim-create');

    Route::post('master/pengirim-import', [PengirimController::class, 'import'])
         ->name('pengirim.import.process')
         ->middleware('can:master-pengirim-create');

    // 📦 Jenis Barang (Item Type) Management with permissions
    Route::resource('master/jenis-barang', JenisBarangController::class)
         ->names('jenis-barang')
         ->middleware([
             'index' => 'can:master-jenis-barang-view',
             'create' => 'can:master-jenis-barang-create',
             'store' => 'can:master-jenis-barang-create',
             'show' => 'can:master-jenis-barang-view',
             'edit' => 'can:master-jenis-barang-update',
             'update' => 'can:master-jenis-barang-update',
             'destroy' => 'can:master-jenis-barang-delete'
         ]);

    // Jenis Barang import/export routes
    Route::get('master/jenis-barang-download-template', [JenisBarangController::class, 'downloadTemplate'])
         ->name('jenis-barang.download-template')
         ->middleware('can:master-jenis-barang-view');

    Route::get('master/jenis-barang-import', [JenisBarangController::class, 'showImportForm'])
         ->name('jenis-barang.import-form')
         ->middleware('can:master-jenis-barang-create');

    Route::post('master/jenis-barang-import', [JenisBarangController::class, 'import'])
         ->name('jenis-barang.import')
         ->middleware('can:master-jenis-barang-create');

    // 📦 Term Management with permissions
    Route::resource('master/term', TermController::class)
         ->names('term')
         ->middleware([
             'index' => 'can:master-term-view',
             'create' => 'can:master-term-create',
             'store' => 'can:master-term-create',
             'show' => 'can:master-term-view',
             'edit' => 'can:master-term-update',
             'update' => 'can:master-term-update',
             'destroy' => 'can:master-term-delete'
         ]);

    // 📥 Term - Download Template & Import CSV
    Route::get('master/term-download-template', [TermController::class, 'downloadTemplate'])
         ->name('term.download-template')
         ->middleware('can:master-term-view');

    Route::get('master/term-import', [TermController::class, 'showImport'])
         ->name('term.import')
         ->middleware('can:master-term-create');

    Route::post('master/term-import', [TermController::class, 'import'])
         ->name('term.import.process')
         ->middleware('can:master-term-create');

    // � Tujuan Kirim - Download Template & Import CSV (BEFORE resource routes)
    Route::get('master/tujuan-kirim-download-template', [MasterTujuanKirimController::class, 'downloadTemplate'])
         ->name('tujuan-kirim.download-template')
         ->middleware('can:master-tujuan-kirim-view');

    Route::get('master/tujuan-kirim-import', [MasterTujuanKirimController::class, 'showImport'])
         ->name('tujuan-kirim.import')
         ->middleware('can:master-tujuan-kirim-create');

    Route::post('master/tujuan-kirim-import', [MasterTujuanKirimController::class, 'import'])
         ->name('tujuan-kirim.import.process')
         ->middleware('can:master-tujuan-kirim-create');

    // 📤 Tujuan Kirim - Export CSV (BEFORE resource routes)
    Route::get('master/tujuan-kirim/export', [MasterTujuanKirimController::class, 'export'])
         ->name('tujuan-kirim.export')
         ->middleware('can:master-tujuan-kirim-view');

    // �📦 Tujuan Kirim (Shipping Destination) Management with permissions
    Route::resource('master/tujuan-kirim', \App\Http\Controllers\MasterTujuanKirimController::class)
         ->names([
             'index' => 'tujuan-kirim.index',
             'create' => 'tujuan-kirim.create',
             'store' => 'tujuan-kirim.store',
             'show' => 'tujuan-kirim.show',
             'edit' => 'tujuan-kirim.edit',
             'update' => 'tujuan-kirim.update',
             'destroy' => 'tujuan-kirim.destroy'
         ])
         ->parameters(['tujuan-kirim' => 'tujuanKirim'])
         ->middleware([
             'index' => 'can:master-tujuan-kirim-view',
             'create' => 'can:master-tujuan-kirim-create',
             'store' => 'can:master-tujuan-kirim-create',
             'show' => 'can:master-tujuan-kirim-view',
             'edit' => 'can:master-tujuan-kirim-update',
             'update' => 'can:master-tujuan-kirim-update',
             'destroy' => 'can:master-tujuan-kirim-delete'
         ]);

    // 🏢 Vendor Kontainer Sewa Management with permissions
    Route::resource('master/vendor-kontainer-sewa', \App\Http\Controllers\VendorKontainerSewaController::class)
         ->names([
             'index' => 'vendor-kontainer-sewa.index',
             'create' => 'vendor-kontainer-sewa.create',
             'store' => 'vendor-kontainer-sewa.store',
             'show' => 'vendor-kontainer-sewa.show',
             'edit' => 'vendor-kontainer-sewa.edit',
             'update' => 'vendor-kontainer-sewa.update',
             'destroy' => 'vendor-kontainer-sewa.destroy'
         ])
         ->parameters(['vendor-kontainer-sewa' => 'vendorKontainerSewa'])
         ->middleware([
             'index' => 'can:vendor-kontainer-sewa-view',
             'create' => 'can:vendor-kontainer-sewa-create',
             'store' => 'can:vendor-kontainer-sewa-create',
             'show' => 'can:vendor-kontainer-sewa-view',
             'edit' => 'can:vendor-kontainer-sewa-edit',
             'update' => 'can:vendor-kontainer-sewa-edit',
             'destroy' => 'can:vendor-kontainer-sewa-delete'
         ]);

    // 🚢 Pergerakan Kapal Management with permissions
    Route::resource('pergerakan-kapal', \App\Http\Controllers\PergerakanKapalController::class)
         ->names([
             'index' => 'pergerakan-kapal.index',
             'create' => 'pergerakan-kapal.create',
             'store' => 'pergerakan-kapal.store',
             'show' => 'pergerakan-kapal.show',
             'edit' => 'pergerakan-kapal.edit',
             'update' => 'pergerakan-kapal.update',
             'destroy' => 'pergerakan-kapal.destroy'
         ])
         ->parameters(['pergerakan-kapal' => 'pergerakanKapal'])
         ->middleware([
             'index' => 'can:pergerakan-kapal-view',
             'create' => 'can:pergerakan-kapal-create',
             'store' => 'can:pergerakan-kapal-create',
             'show' => 'can:pergerakan-kapal-view',
             'edit' => 'can:pergerakan-kapal-update',
             'update' => 'can:pergerakan-kapal-update',
             'destroy' => 'can:pergerakan-kapal-delete'
         ]);

    // API untuk generate voyage number
    Route::get('api/pergerakan-kapal/generate-voyage', [\App\Http\Controllers\PergerakanKapalController::class, 'generateVoyageNumber'])
         ->name('api.pergerakan-kapal.generate-voyage')
         ->middleware('can:pergerakan-kapal-create');

    // Additional routes for pergerakan kapal
    Route::patch('pergerakan-kapal/{pergerakanKapal}/approve', [\App\Http\Controllers\PergerakanKapalController::class, 'approve'])
         ->name('pergerakan-kapal.approve')
         ->middleware('can:pergerakan-kapal-approve');

    Route::get('pergerakan-kapal/print', [\App\Http\Controllers\PergerakanKapalController::class, 'print'])
         ->name('pergerakan-kapal.print')
         ->middleware('can:pergerakan-kapal-print');

    Route::get('pergerakan-kapal/export', [\App\Http\Controllers\PergerakanKapalController::class, 'export'])
         ->name('pergerakan-kapal.export')
         ->middleware('can:pergerakan-kapal-export');
});

// ═══════════════════════════════════════════════════════════════════════
// � ORDER MANAGEMENT ROUTES
// ═══════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'verified'])->group(function () {
    // Order Data Management Routes (formerly approval)
    Route::prefix('orders/approval')->name('orders.approval.')->group(function () {
        Route::get('/', [OrderDataManagementController::class, 'index'])->name('index');
    });

    // Order Template Download Route
    Route::get('orders/download-template', [OrderController::class, 'downloadTemplate'])
         ->name('orders.download.template')
         ->middleware('can:order-view');

    // 📋 Order Management with permissions
    Route::resource('orders', OrderController::class)
         ->middleware([
             'index' => 'can:order-view',
             'create' => 'can:order-create',
             'store' => 'can:order-create',
             'show' => 'can:order-view',
             'edit' => 'can:order-update',
             'update' => 'can:order-update',
             'destroy' => 'can:order-delete'
         ]);

    // Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });

    // AJAX route for generating order number
    Route::post('/orders/generate-number', [OrderController::class, 'generateOrderNumber'])
         ->name('orders.generate-number')
         ->middleware('can:order-create');

    // 📊 Outstanding Orders Management with permissions
    Route::prefix('outstanding')->name('outstanding.')->middleware('can:order-view')->group(function () {
        Route::get('/', [OutstandingController::class, 'index'])->name('index');
        Route::get('/stats', [OutstandingController::class, 'getStats'])->name('stats');
        Route::get('/status/{status}', [OutstandingController::class, 'byStatus'])->name('by-status');
        Route::get('/{order}/details', [OutstandingController::class, 'getOrderDetails'])->name('details');
        Route::post('/{order}/process', [OutstandingController::class, 'processUnits'])
             ->name('process')
             ->middleware('can:order-update');
        Route::get('/export', [OutstandingController::class, 'export'])->name('export');
    });

    // ═══════════════════════════════════════════════════════════════════════
    // 📋 SURAT JALAN MANAGEMENT
    // ═══════════════════════════════════════════════════════════════════════

    // Surat Jalan Order Selection
    Route::get('/surat-jalan/select-order', [\App\Http\Controllers\SuratJalanController::class, 'selectOrder'])
         ->name('surat-jalan.select-order')
         ->middleware('can:surat-jalan-create');

    // Surat Jalan Without Order
    Route::get('/surat-jalan/create-without-order', [\App\Http\Controllers\SuratJalanController::class, 'createWithoutOrder'])
         ->name('surat-jalan.create-without-order')
         ->middleware('can:surat-jalan-create');

    // Store Surat Jalan Without Order
    Route::post('/surat-jalan/store-without-order', [\App\Http\Controllers\SuratJalanController::class, 'storeWithoutOrder'])
         ->name('surat-jalan.store-without-order')
         ->middleware('can:surat-jalan-create');

    // Surat Jalan Management with permissions
    Route::resource('surat-jalan', \App\Http\Controllers\SuratJalanController::class)
         ->middleware([
             'index' => 'can:surat-jalan-view',
             'create' => 'can:surat-jalan-create',
             'store' => 'can:surat-jalan-create',
             'show' => 'can:surat-jalan-view',
             'edit' => 'can:surat-jalan-update',
             'update' => 'can:surat-jalan-update',
             'destroy' => 'can:surat-jalan-delete'
         ]);

    // AJAX route for generating surat jalan number
    Route::get('/surat-jalan/generate-nomor', [\App\Http\Controllers\SuratJalanController::class, 'generateNomorSuratJalan'])
         ->name('surat-jalan.generate-nomor')
         ->middleware('can:surat-jalan-create');

    // Print surat jalan
    Route::get('/surat-jalan/{suratJalan}/print', [\App\Http\Controllers\SuratJalanController::class, 'print'])
         ->name('surat-jalan.print')
         ->middleware('can:surat-jalan-view');

    // Download PDF surat jalan
    Route::get('/surat-jalan/{suratJalan}/download', [\App\Http\Controllers\SuratJalanController::class, 'downloadPdf'])
         ->name('surat-jalan.download')
         ->middleware('can:surat-jalan-view');

    // Print memo for surat jalan
    Route::get('/surat-jalan/{suratJalan}/print-memo', [\App\Http\Controllers\SuratJalanController::class, 'printMemo'])
         ->name('surat-jalan.print-memo')
         ->middleware('can:surat-jalan-view');

    // Print preprinted surat jalan
    Route::get('/surat-jalan/{suratJalan}/print-preprinted', [\App\Http\Controllers\SuratJalanController::class, 'printPreprinted'])
         ->name('surat-jalan.print-preprinted')
         ->middleware('can:surat-jalan-view');

    // =============================
    // UANG JALAN MANAGEMENT ROUTES
    // =============================

    // Uang Jalan Management with permissions
    // Custom route untuk select surat jalan
    Route::get('uang-jalan/select-surat-jalan', [\App\Http\Controllers\UangJalanController::class, 'selectSuratJalan'])
         ->name('uang-jalan.select-surat-jalan')
         ->middleware('can:uang-jalan-create');
    
    Route::resource('uang-jalan', \App\Http\Controllers\UangJalanController::class)
         ->middleware([
             'index' => 'can:uang-jalan-view',
             'create' => 'can:uang-jalan-create', 
             'store' => 'can:uang-jalan-create',
             'show' => 'can:uang-jalan-view',
             'edit' => 'can:uang-jalan-update',
             'update' => 'can:uang-jalan-update',
             'destroy' => 'can:uang-jalan-delete'
         ]);

    // ====================================
    // PRANOTA UANG JALAN MANAGEMENT ROUTES
    // ====================================

    // Pranota Uang Jalan Management with permissions
    Route::resource('pranota-uang-jalan', \App\Http\Controllers\PranotaSuratJalanController::class)
         ->middleware([
             'index' => 'can:pranota-uang-jalan-view',
             'create' => 'can:pranota-uang-jalan-create', 
             'store' => 'can:pranota-uang-jalan-create',
             'show' => 'can:pranota-uang-jalan-view',
             'edit' => 'can:pranota-uang-jalan-update',
             'update' => 'can:pranota-uang-jalan-update',
             'destroy' => 'can:pranota-uang-jalan-delete'
         ]);

    // Print Pranota Uang Jalan
    Route::get('pranota-uang-jalan/{pranotaUangJalan}/print', [\App\Http\Controllers\PranotaSuratJalanController::class, 'print'])
         ->name('pranota-uang-jalan.print')
         ->middleware('can:pranota-uang-jalan-view');

    // ============= SURAT JALAN BONGKARAN ROUTES =============
    
    // Select kapal and voyage before creating
    Route::get('/surat-jalan-bongkaran/select-kapal', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'selectKapal'])
         ->name('surat-jalan-bongkaran.select-kapal')
         ->middleware('can:surat-jalan-bongkaran-create');
    
    // API endpoint for getting BL data
    Route::get('/surat-jalan-bongkaran/api/bl-data', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'getBlData'])
         ->name('surat-jalan-bongkaran.bl-data')
         ->middleware('can:surat-jalan-bongkaran-create');
    
    // Surat Jalan Bongkaran resource routes
    Route::resource('surat-jalan-bongkaran', \App\Http\Controllers\SuratJalanBongkaranController::class)
         ->middleware([
             'index' => 'can:surat-jalan-bongkaran-view',
             'create' => 'can:surat-jalan-bongkaran-create',
             'store' => 'can:surat-jalan-bongkaran-create',
             'show' => 'can:surat-jalan-bongkaran-view',
             'edit' => 'can:surat-jalan-bongkaran-update',
             'update' => 'can:surat-jalan-bongkaran-update',
             'destroy' => 'can:surat-jalan-bongkaran-delete'
         ]);

    // Print surat jalan bongkaran
    Route::get('/surat-jalan-bongkaran/{suratJalanBongkaran}/print', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'print'])
         ->name('surat-jalan-bongkaran.print')
         ->middleware('can:surat-jalan-bongkaran-view');

    // Download PDF surat jalan bongkaran
    Route::get('/surat-jalan-bongkaran/{suratJalanBongkaran}/download', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'downloadPdf'])
         ->name('surat-jalan-bongkaran.download')
         ->middleware('can:surat-jalan-bongkaran-view');

    // Update status surat jalan
    Route::post('/surat-jalan/{suratJalan}/update-status', [\App\Http\Controllers\SuratJalanController::class, 'updateStatus'])
         ->name('surat-jalan.update-status')
         ->middleware('can:surat-jalan-update');

    // AJAX route for getting uang jalan by tujuan
    Route::post('/api/get-uang-jalan-by-tujuan', [\App\Http\Controllers\SuratJalanController::class, 'getUangJalanByTujuan'])
         ->name('surat-jalan.get-uang-jalan')
         ->middleware('can:surat-jalan-create');

    // ═══════════════════════════════════════════════════════════════════════
    // � PRANOTA UANG RIT MANAGEMENT
    // ═══════════════════════════════════════════════════════════════════════

    // Pranota Uang Rit Management with permissions
    Route::resource('pranota-uang-rit', \App\Http\Controllers\PranotaUangRitController::class)
         ->middleware([
             'index' => 'can:pranota-uang-rit-view',
             'create' => 'can:pranota-uang-rit-create',
             'store' => 'can:pranota-uang-rit-create',
             'show' => 'can:pranota-uang-rit-view',
             'edit' => 'can:pranota-uang-rit-update',
             'update' => 'can:pranota-uang-rit-update',
             'destroy' => 'can:pranota-uang-rit-delete'
         ]);

    // Special route for selecting uang jalan
    Route::get('pranota-uang-rit/select-uang-jalan', [\App\Http\Controllers\PranotaUangRitController::class, 'selectUangJalan'])
         ->name('pranota-uang-rit.select-uang-jalan')
         ->middleware('can:pranota-uang-rit-create');

    Route::post('pranota-uang-rit/from-selection', [\App\Http\Controllers\PranotaUangRitController::class, 'createFromSelection'])
         ->name('pranota-uang-rit.from-selection')
         ->middleware('can:pranota-uang-rit-create');

    // Additional Pranota Uang Rit routes for workflow
    Route::post('pranota-uang-rit/{pranotaUangRit}/submit', [\App\Http\Controllers\PranotaUangRitController::class, 'submit'])
         ->name('pranota-uang-rit.submit')
         ->middleware('can:pranota-uang-rit-update');

    Route::post('pranota-uang-rit/{pranotaUangRit}/approve', [\App\Http\Controllers\PranotaUangRitController::class, 'approve'])
         ->name('pranota-uang-rit.approve')
         ->middleware('can:pranota-uang-rit-approve');

    Route::post('pranota-uang-rit/{pranotaUangRit}/mark-as-paid', [\App\Http\Controllers\PranotaUangRitController::class, 'markAsPaid'])
         ->name('pranota-uang-rit.mark-as-paid')
         ->middleware('can:pranota-uang-rit-mark-paid');

    Route::get('pranota-uang-rit/{pranotaUangRit}/print', [\App\Http\Controllers\PranotaUangRitController::class, 'print'])
         ->name('pranota-uang-rit.print')
         ->middleware('can:pranota-uang-rit-view');

    // ═══════════════════════════════════════════════════════════════════════
    // 🚚 PRANOTA UANG KENEK MANAGEMENT
    // ═══════════════════════════════════════════════════════════════════════

    // Pranota Uang Kenek Management with permissions
    Route::resource('pranota-uang-kenek', \App\Http\Controllers\PranotaUangKenekController::class)
         ->middleware([
             'index' => 'can:pranota-uang-kenek-view',
             'create' => 'can:pranota-uang-kenek-create',
             'store' => 'can:pranota-uang-kenek-create',
             'show' => 'can:pranota-uang-kenek-view',
             'edit' => 'can:pranota-uang-kenek-update',
             'update' => 'can:pranota-uang-kenek-update',
             'destroy' => 'can:pranota-uang-kenek-delete'
         ]);

    // Additional Pranota Uang Kenek routes for workflow
    Route::patch('pranota-uang-kenek/{pranotaUangKenek}/submit', [\App\Http\Controllers\PranotaUangKenekController::class, 'submit'])
         ->name('pranota-uang-kenek.submit')
         ->middleware('can:pranota-uang-kenek-update');

    Route::patch('pranota-uang-kenek/{pranotaUangKenek}/approve', [\App\Http\Controllers\PranotaUangKenekController::class, 'approve'])
         ->name('pranota-uang-kenek.approve')
         ->middleware('can:pranota-uang-kenek-approve');

    Route::patch('pranota-uang-kenek/{pranotaUangKenek}/mark-as-paid', [\App\Http\Controllers\PranotaUangKenekController::class, 'markAsPaid'])
         ->name('pranota-uang-kenek.mark-as-paid')
         ->middleware('can:pranota-uang-kenek-mark-paid');

    // Print pranota uang kenek
    Route::get('pranota-uang-kenek/{pranotaUangKenek}/print', [\App\Http\Controllers\PranotaUangKenekController::class, 'print'])
         ->name('pranota-uang-kenek.print')
         ->middleware('can:pranota-uang-kenek-view');

    // Debug route untuk surat jalan
    Route::get('/debug-surat-jalan', function() {
        $data = [];
        
        // Check total surat jalans
        $data['total_surat_jalans'] = \App\Models\SuratJalan::count();
        
        // Check approved
        $data['approved_count'] = \App\Models\SuratJalan::where('status', 'approved')->count();
        
        // Check dengan rit
        $data['menggunakan_rit_count'] = \App\Models\SuratJalan::where('rit', 'menggunakan_rit')->count();
        
        // Check both conditions
        $data['both_conditions'] = \App\Models\SuratJalan::where('status', 'approved')
            ->where('rit', 'menggunakan_rit')
            ->count();
        
        // Sample data
        $data['sample_data'] = \App\Models\SuratJalan::select('id', 'no_surat_jalan', 'status', 'rit', 'supir_nama', 'kenek_nama')
            ->limit(5)
            ->get();
        
        // Recent surat jalans
        $data['recent_data'] = \App\Models\SuratJalan::select('id', 'no_surat_jalan', 'status', 'rit', 'supir_nama', 'kenek_nama')
            ->latest()
            ->limit(3)
            ->get();
        
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    })->name('debug.surat-jalan');

    // ═══════════════════════════════════════════════════════════════════════
    // �📋 TANDA TERIMA MANAGEMENT
    // ═══════════════════════════════════════════════════════════════════════

    Route::resource('tanda-terima', \App\Http\Controllers\TandaTerimaController::class)
         ->except(['create', 'store'])
         ->middleware([
             'index' => 'can:tanda-terima-view',
             'show' => 'can:tanda-terima-view',
             'edit' => 'can:tanda-terima-edit',
             'update' => 'can:tanda-terima-edit',
             'destroy' => 'can:tanda-terima-delete'
         ]);

    // Route untuk menambahkan cargo ke prospek
    Route::post('tanda-terima/{tandaTerima}/add-to-prospek', [\App\Http\Controllers\TandaTerimaController::class, 'addToProspek'])
         ->name('tanda-terima.add-to-prospek')
         ->middleware('can:tanda-terima-edit');

    // ═══════════════════════════════════════════════════════════════════════
    // 📋 TANDA TERIMA TANPA SURAT JALAN MANAGEMENT
    // ═══════════════════════════════════════════════════════════════════════

    // Route untuk pilih tipe kontainer (harus sebelum resource route)
    Route::get('tanda-terima-tanpa-surat-jalan/pilih-tipe', [\App\Http\Controllers\TandaTerimaTanpaSuratJalanController::class, 'pilihTipe'])
         ->name('tanda-terima-tanpa-surat-jalan.pilih-tipe')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-create');

    // Routes untuk bulk actions LCL (harus sebelum resource route)
    Route::get('tanda-terima-lcl/export', [\App\Http\Controllers\TandaTerimaLclController::class, 'bulkExport'])
         ->name('tanda-terima-lcl.export')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-view');

    Route::get('tanda-terima-lcl/print', [\App\Http\Controllers\TandaTerimaLclController::class, 'bulkPrint'])
         ->name('tanda-terima-lcl.print')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-view');

    Route::delete('tanda-terima-lcl/bulk-delete', [\App\Http\Controllers\TandaTerimaLclController::class, 'bulkDelete'])
         ->name('tanda-terima-lcl.bulk-delete')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-delete');

    Route::post('tanda-terima-lcl/validate-containers', [\App\Http\Controllers\TandaTerimaLclController::class, 'validateContainers'])
         ->name('tanda-terima-lcl.validate-containers')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-view');

    Route::patch('tanda-terima-lcl/bulk-seal', [\App\Http\Controllers\TandaTerimaLclController::class, 'bulkSeal'])
         ->name('tanda-terima-lcl.bulk-seal')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-update');

    Route::post('tanda-terima-lcl/bulk-split', [\App\Http\Controllers\TandaTerimaLclController::class, 'bulkSplit'])
         ->name('tanda-terima-lcl.bulk-split')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-create');

    // Routes untuk LCL khusus menggunakan controller terpisah (setelah route spesifik)
    Route::resource('tanda-terima-lcl', \App\Http\Controllers\TandaTerimaLclController::class)
         ->middleware([
             'index' => 'can:tanda-terima-tanpa-surat-jalan-view',
             'create' => 'can:tanda-terima-tanpa-surat-jalan-create',
             'store' => 'can:tanda-terima-tanpa-surat-jalan-create',
             'show' => 'can:tanda-terima-tanpa-surat-jalan-view',
             'edit' => 'can:tanda-terima-tanpa-surat-jalan-update',
             'update' => 'can:tanda-terima-tanpa-surat-jalan-update',
             'destroy' => 'can:tanda-terima-tanpa-surat-jalan-delete'
         ]);

    // Route untuk create LCL khusus (backward compatibility)
    Route::get('tanda-terima-tanpa-surat-jalan/create-lcl', [\App\Http\Controllers\TandaTerimaLclController::class, 'create'])
         ->name('tanda-terima-tanpa-surat-jalan.create-lcl')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-create');

    Route::resource('tanda-terima-tanpa-surat-jalan', \App\Http\Controllers\TandaTerimaTanpaSuratJalanController::class)
         ->middleware([
             'index' => 'can:tanda-terima-tanpa-surat-jalan-view',
             'create' => 'can:tanda-terima-tanpa-surat-jalan-create',
             'store' => 'can:tanda-terima-tanpa-surat-jalan-create',
             'show' => 'can:tanda-terima-tanpa-surat-jalan-view',
             'edit' => 'can:tanda-terima-tanpa-surat-jalan-update',
             'update' => 'can:tanda-terima-tanpa-surat-jalan-update',
             'destroy' => 'can:tanda-terima-tanpa-surat-jalan-delete'
         ]);

    // Gate In AJAX Routes (must be defined BEFORE resource route to avoid conflicts)
    Route::get('gate-in/get-kontainers', [\App\Http\Controllers\GateInController::class, 'getKontainers'])
         ->name('gate-in.get-kontainers')
         ->middleware('can:gate-in-view');

    Route::get('gate-in/get-kontainers-surat-jalan', [\App\Http\Controllers\GateInController::class, 'getKontainersSuratJalan'])
         ->name('gate-in.get-kontainers-surat-jalan')
         ->middleware('can:gate-in-view');

    Route::get('gate-in/get-gudang-by-kegiatan', [\App\Http\Controllers\GateInController::class, 'getGudangByKegiatan'])
         ->name('gate-in.get-gudang-by-kegiatan')
         ->middleware('can:gate-in-view');

    Route::get('gate-in/get-kontainer-by-kegiatan', [\App\Http\Controllers\GateInController::class, 'getKontainerByKegiatan'])
         ->name('gate-in.get-kontainer-by-kegiatan')
         ->middleware('can:gate-in-view');

    Route::get('gate-in/get-muatan-by-kegiatan', [\App\Http\Controllers\GateInController::class, 'getMuatanByKegiatan'])
         ->name('gate-in.get-muatan-by-kegiatan')
         ->middleware('can:gate-in-view');

    Route::get('gate-in/calculate-total', [\App\Http\Controllers\GateInController::class, 'calculateTotal'])
         ->name('gate-in.calculate-total')
         ->middleware('can:gate-in-view');

    // Gate In Management Routes
    Route::resource('gate-in', \App\Http\Controllers\GateInController::class)
         ->middleware([
             'index' => 'can:gate-in-view',
             'create' => 'can:gate-in-create',
             'store' => 'can:gate-in-create',
             'show' => 'can:gate-in-view',
             'edit' => 'can:gate-in-update',
             'update' => 'can:gate-in-update',
             'destroy' => 'can:gate-in-delete'
         ]);

    Route::post('gate-in/{gateIn}/add-kontainer', [\App\Http\Controllers\GateInController::class, 'addKontainer'])
         ->name('gate-in.add-kontainer')
         ->middleware('can:gate-in-update');

    Route::post('gate-in/{gateIn}/remove-kontainer', [\App\Http\Controllers\GateInController::class, 'removeKontainer'])
         ->name('gate-in.remove-kontainer')
         ->middleware('can:gate-in-update');

    Route::patch('gate-in/{gateIn}/update-status', [\App\Http\Controllers\GateInController::class, 'updateStatus'])
         ->name('gate-in.update-status')
         ->middleware('can:gate-in-update');

});

// Test route for AJAX debugging
Route::get('/test-gate-in-ajax', function () {
    return view('test-gate-in-ajax');
})->middleware('auth');



// ═══════════════════════════════════════════════════════════════════════
// �🔗 SPECIAL ROUTES (Outside Master Group)
    // ═══════════════════════════════════════════════════════════════════════

    // Route master.karyawan.index di luar group master untuk konsistensi dengan view
    Route::get('master/karyawan', [KaryawanController::class, 'index'])
         ->name('master.karyawan.index')
         ->middleware('can:master-karyawan-view');

/*
|===========================================================================
| 📄 BUSINESS PROCESS ROUTES (Permohonan, Pranota, Pembayaran)
|===========================================================================
| Core business workflows and document processing
*/

    // ═══════════════════════════════════════════════════════════════════════
    // 📝 PERMOHONAN (REQUEST) MANAGEMENT
    // ═══════════════════════════════════════════════════════════════════════

    // CSV export/import for permohonan (declare before resource to avoid routing conflict with parameterized routes)
    Route::get('permohonan/export', [PermohonanController::class, 'export'])
         ->name('permohonan.export')
         ->middleware('can:permohonan');

    Route::post('permohonan/import', [PermohonanController::class, 'import'])
         ->name('permohonan.import')
         ->middleware('can:permohonan');

    // Print single permohonan memo (declare before resource routes)
    Route::get('permohonan/{permohonan}/print', [PermohonanController::class, 'print'])
         ->name('permohonan.print')
         ->middleware('can:permohonan-memo-print');

    // Print permohonan by date range
    Route::get('permohonan/print/by-date', [PermohonanController::class, 'printByDate'])
         ->name('permohonan.print.by-date')
         ->middleware('can:permohonan-memo-print');

    // Bulk delete permohonan (declare before resource routes)
    Route::delete('permohonan/bulk-delete', [PermohonanController::class, 'bulkDelete'])
         ->name('permohonan.bulk-delete')
         ->middleware('can:permohonan-memo-delete');

    // Individual routes for permohonan with specific permissions
    Route::get('permohonan', [PermohonanController::class, 'index'])
         ->name('permohonan.index')
         ->middleware('can:permohonan-memo-view');
    Route::get('permohonan/create', [PermohonanController::class, 'create'])
         ->name('permohonan.create')
         ->middleware('can:permohonan-memo-create');
    Route::post('permohonan', [PermohonanController::class, 'store'])
         ->name('permohonan.store')
         ->middleware('can:permohonan-memo-create');
    Route::get('permohonan/{permohonan}', [PermohonanController::class, 'show'])
         ->name('permohonan.show')
         ->middleware('can:permohonan-memo-view');
    Route::get('permohonan/{permohonan}/edit', [PermohonanController::class, 'edit'])
         ->name('permohonan.edit')
         ->middleware('can:permohonan-memo-update');
    Route::put('permohonan/{permohonan}', [PermohonanController::class, 'update'])
         ->name('permohonan.update')
         ->middleware('can:permohonan-memo-update');
    Route::delete('permohonan/{permohonan}', [PermohonanController::class, 'destroy'])
         ->name('permohonan.destroy')
         ->middleware('can:permohonan-memo-delete');

    // ═══════════════════════════════════════════════════════════════════════
    // 📋 PRANOTA (INVOICE) MANAGEMENT - Granular Permissions
    // ═══════════════════════════════════════════════════════════════════════

    // 🚚 Pranota Supir (Driver Invoice) - HYBRID Resource with additional routes
    Route::prefix('pranota-supir')->name('pranota-supir.')->middleware(['auth'])->group(function () {
        Route::get('/', [PranotaSupirController::class, 'index'])
            ->name('index')
            ->middleware('can:pranota-supir-view');
        Route::get('/create', [PranotaSupirController::class, 'create'])
            ->name('create')
            ->middleware('can:pranota-supir-create');
        Route::post('/', [PranotaSupirController::class, 'store'])
            ->name('store')
            ->middleware('can:pranota-supir-create');
        Route::get('/{pranotaSupir}', [PranotaSupirController::class, 'show'])
            ->name('show')
            ->middleware('can:pranota-supir-view');
        Route::get('/{pranotaSupir}/edit', [PranotaSupirController::class, 'edit'])
            ->name('edit')
            ->middleware('can:pranota-supir-update');
        Route::put('/{pranotaSupir}', [PranotaSupirController::class, 'update'])
            ->name('update')
            ->middleware('can:pranota-supir-update');
        Route::delete('/{pranotaSupir}', [PranotaSupirController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:pranota-supir-delete');

        // Additional granular routes
        Route::get('/{pranotaSupir}/print', [PranotaSupirController::class, 'print'])
            ->name('print')
            ->middleware('can:pranota-supir-print');

        // Print pranota by date range
        Route::get('/print/by-date', [PranotaSupirController::class, 'printByDate'])
            ->name('print.by-date')
            ->middleware('can:pranota-supir-print');
    });

          // --- Rute Pranota & Pembayaran Pranota Tagihan Kontainer ---
                    // Tagihan Kontainer Sewa feature removed - routes deleted to allow clean rebuild

/*
|===========================================================================
| 💳 PAYMENT MANAGEMENT ROUTES - Granular Permission System
|===========================================================================
| All payment processing with detailed permission control
*/

    // ═══════════════════════════════════════════════════════════════════════
    // 🚚 PEMBAYARAN PRANOTA SUPIR (Driver Payment) - Full CRUD + Print
    // ═══════════════════════════════════════════════════════════════════════

    Route::prefix('pembayaran-pranota-supir')->name('pembayaran-pranota-supir.')->middleware(['auth'])->group(function() {
        Route::get('/', [PembayaranPranotaSupirController::class, 'index'])
            ->name('index')
            ->middleware('can:pembayaran-pranota-supir-view');
        Route::get('/create', [PembayaranPranotaSupirController::class, 'create'])
            ->name('create')
            ->middleware('can:pembayaran-pranota-supir-create');
        Route::get('/generate-nomor', [PembayaranPranotaSupirController::class, 'generateNomorPembayaran'])
            ->name('generate-nomor')
            ->middleware('can:pembayaran-pranota-supir-create');
        Route::post('/', [PembayaranPranotaSupirController::class, 'store'])
            ->name('store')
            ->middleware('can:pembayaran-pranota-supir-create');
        Route::get('/{pembayaran}', [PembayaranPranotaSupirController::class, 'show'])
            ->name('show')
            ->middleware('can:pembayaran-pranota-supir-view');
        Route::get('/{pembayaran}/edit', [PembayaranPranotaSupirController::class, 'edit'])
            ->name('edit')
            ->middleware('can:pembayaran-pranota-supir-update');
        Route::put('/{pembayaran}', [PembayaranPranotaSupirController::class, 'update'])
            ->name('update')
            ->middleware('can:pembayaran-pranota-supir-update');
        Route::delete('/{pembayaran}', [PembayaranPranotaSupirController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:pembayaran-pranota-supir-delete');

        // Additional granular routes
        Route::get('/{pembayaran}/print', [PembayaranPranotaSupirController::class, 'print'])
            ->name('print')
            ->middleware('can:pembayaran-pranota-supir-print');
    });

    // ═══════════════════════════════════════════════════════════════════════
    // 🔧 PEMBAYARAN PRANOTA PERBAIKAN KONTAINER (Container Repair Payment)
    // ═══════════════════════════════════════════════════════════════════════

    Route::prefix('pembayaran-pranota-perbaikan-kontainer')
        ->name('pembayaran-pranota-perbaikan-kontainer.')
        ->middleware(['auth'])
        ->group(function() {
            Route::get('/', [PembayaranPranotaPerbaikanKontainerController::class, 'index'])
                ->name('index')
                ->middleware('can:pembayaran-pranota-perbaikan-kontainer-view');
            Route::get('/create', [PembayaranPranotaPerbaikanKontainerController::class, 'create'])
                ->name('create')
                ->middleware('can:pembayaran-pranota-perbaikan-kontainer-create');
            Route::post('/', [PembayaranPranotaPerbaikanKontainerController::class, 'store'])
                ->name('store')
                ->middleware('can:pembayaran-pranota-perbaikan-kontainer-create');
            Route::get('/{pembayaran}', [PembayaranPranotaPerbaikanKontainerController::class, 'show'])
                ->name('show')
                ->middleware('can:pembayaran-pranota-perbaikan-kontainer-view');
            Route::get('/{pembayaran}/edit', [PembayaranPranotaPerbaikanKontainerController::class, 'edit'])
                ->name('edit')
                ->middleware('can:pembayaran-pranota-perbaikan-kontainer-update');
            Route::put('/{pembayaran}', [PembayaranPranotaPerbaikanKontainerController::class, 'update'])
                ->name('update')
                ->middleware('can:pembayaran-pranota-perbaikan-kontainer-update');
            Route::delete('/{pembayaran}', [PembayaranPranotaPerbaikanKontainerController::class, 'destroy'])
                ->name('destroy')
                ->middleware('can:pembayaran-pranota-perbaikan-kontainer-delete');

            // Additional granular routes
            Route::get('/{pembayaran}/print', [PembayaranPranotaPerbaikanKontainerController::class, 'print'])
                ->name('print')
                ->middleware('can:pembayaran-pranota-perbaikan-kontainer-print');
        });

    // 📄 Pranota Surat Jalan - DISABLED (Replaced with Pranota Uang Jalan)
    // Route::prefix('pranota-surat-jalan')->name('pranota-surat-jalan.')->middleware(['auth'])->group(function () {
    //     // Routes disabled - functionality moved to pranota-uang-jalan
    // });

    // ═══════════════════════════════════════════════════════════════════════
    // 💰 PEMBAYARAN PRANOTA SURAT JALAN (Delivery Note Payment)
    // ═══════════════════════════════════════════════════════════════════════

    Route::prefix('pembayaran-pranota-surat-jalan')
        ->name('pembayaran-pranota-surat-jalan.')
        ->middleware(['auth'])
        ->group(function() {
            Route::get('/', [PembayaranPranotaSuratJalanController::class, 'index'])
                ->name('index')
                ->middleware('can:pembayaran-pranota-surat-jalan-view');
            Route::get('/create', [PembayaranPranotaSuratJalanController::class, 'create'])
                ->name('create')
                ->middleware('can:pembayaran-pranota-surat-jalan-create');
            Route::post('/', [PembayaranPranotaSuratJalanController::class, 'store'])
                ->name('store')
                ->middleware('can:pembayaran-pranota-surat-jalan-create');
            Route::get('/{pembayaranPranotaSuratJalan}', [PembayaranPranotaSuratJalanController::class, 'show'])
                ->name('show')
                ->middleware('can:pembayaran-pranota-surat-jalan-view');
            Route::get('/{pembayaranPranotaSuratJalan}/edit', [PembayaranPranotaSuratJalanController::class, 'edit'])
                ->name('edit')
                ->middleware('can:pembayaran-pranota-surat-jalan-edit');
            Route::put('/{pembayaranPranotaSuratJalan}', [PembayaranPranotaSuratJalanController::class, 'update'])
                ->name('update')
                ->middleware('can:pembayaran-pranota-surat-jalan-edit');
            Route::delete('/{pembayaranPranotaSuratJalan}', [PembayaranPranotaSuratJalanController::class, 'destroy'])
                ->name('destroy')
                ->middleware('can:pembayaran-pranota-surat-jalan-delete');

            // Additional granular routes
            Route::get('/generate-nomor', [PembayaranPranotaSuratJalanController::class, 'generatePaymentNumber'])
                ->name('generate-nomor')
                ->middleware('can:pembayaran-pranota-surat-jalan-create');
        });

    // 💰 PEMBAYARAN PRANOTA UANG JALAN (Travel Allowance Payment)
    // ═══════════════════════════════════════════════════════════════════════

    Route::prefix('pembayaran-pranota-uang-jalan')
        ->name('pembayaran-pranota-uang-jalan.')
        ->middleware(['auth'])
        ->group(function() {
            Route::get('/', [PembayaranPranotaUangJalanController::class, 'index'])
                ->name('index')
                ->middleware('can:pembayaran-pranota-uang-jalan-view');
            Route::get('/create', [PembayaranPranotaUangJalanController::class, 'create'])
                ->name('create')
                ->middleware('can:pembayaran-pranota-uang-jalan-create');
            Route::post('/', [PembayaranPranotaUangJalanController::class, 'store'])
                ->name('store')
                ->middleware('can:pembayaran-pranota-uang-jalan-create');
            Route::get('/{pembayaranPranotaUangJalan}', [PembayaranPranotaUangJalanController::class, 'show'])
                ->name('show')
                ->middleware('can:pembayaran-pranota-uang-jalan-view');
            Route::get('/{pembayaranPranotaUangJalan}/edit', [PembayaranPranotaUangJalanController::class, 'edit'])
                ->name('edit')
                ->middleware('can:pembayaran-pranota-uang-jalan-edit');
            Route::put('/{pembayaranPranotaUangJalan}', [PembayaranPranotaUangJalanController::class, 'update'])
                ->name('update')
                ->middleware('can:pembayaran-pranota-uang-jalan-edit');
            Route::delete('/{pembayaranPranotaUangJalan}', [PembayaranPranotaUangJalanController::class, 'destroy'])
                ->name('destroy')
                ->middleware('can:pembayaran-pranota-uang-jalan-delete');
        });

/*
|===========================================================================
| 🚚 SUPIR (DRIVER) SPECIFIC ROUTES - Role-Based Access
|===========================================================================
| Special routes for driver role with checkpoint functionality
*/

    // ═══════════════════════════════════════════════════════════════════════
    // 🚚 SUPIR DASHBOARD & CHECKPOINT MANAGEMENT
    // ═══════════════════════════════════════════════════════════════════════

    Route::prefix('supir')->name('supir.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', [SupirDashboardController::class, 'index'])
            ->name('dashboard');

        // OB Muat (Operasi Bongkar Muat) - Kapal & Voyage selection
        Route::get('/ob-muat', [SupirDashboardController::class, 'obMuat'])
            ->name('ob-muat');
        Route::post('/ob-muat', [SupirDashboardController::class, 'obMuatStore'])
            ->name('ob-muat.store');
        
        // OB Muat Index - Daftar kontainer berdasarkan kapal & voyage
        Route::get('/ob-muat/index', [SupirDashboardController::class, 'obMuatIndex'])
            ->name('ob-muat.index');
        
        // OB Muat Process - Proses data OB Muat langsung
        Route::post('/ob-muat/process', [SupirDashboardController::class, 'obMuatProcess'])
            ->name('ob-muat.process');

        // Checkpoint management for drivers
        Route::get('/permohonan/{permohonan}/checkpoint', [CheckpointController::class, 'create'])
            ->name('checkpoint.create');
        Route::post('/permohonan/{permohonan}/checkpoint', [CheckpointController::class, 'store'])
            ->name('checkpoint.store');

        // Checkpoint management for surat jalan
        Route::get('/surat-jalan/{suratJalan}/checkpoint', [CheckpointController::class, 'createSuratJalan'])
            ->name('checkpoint.create-surat-jalan');
        Route::post('/surat-jalan/{suratJalan}/checkpoint', [CheckpointController::class, 'storeSuratJalan'])
            ->name('checkpoint.store-surat-jalan');
    });

     // === Approval Surat Jalan ===
     Route::prefix('approval/surat-jalan')->name('approval.surat-jalan.')->middleware('can:approval-surat-jalan-view')->group(function () {
         Route::get('/', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'index'])->name('index');
         Route::get('/{suratJalan}', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'show'])->name('show');
         Route::post('/{suratJalan}/approve', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'approve'])->name('approve')->middleware('can:approval-surat-jalan-approve');
         Route::post('/{suratJalan}/reject', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'reject'])->name('reject')->middleware('can:approval-surat-jalan-approve');
         
         // API untuk stock kontainer dan update
         Route::get('/api/stock-kontainers', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'getStockKontainers'])->name('api.stock-kontainers');
         Route::patch('/{suratJalan}/update-kontainer-seal', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'updateKontainerSeal'])->name('update-kontainer-seal')->middleware('can:approval-surat-jalan-approve');
     });

         // --- Rute Penyelesaian Tugas ---
        // Menggunakan PenyelesaianController yang sudah kita kembangkan
         Route::prefix('approval')->name('approval.')->middleware('can:approval-tugas-1.view')->group(function () {
          // Dashboard untuk melihat tugas yang perlu diselesaikan
          Route::get('/', [\App\Http\Controllers\PenyelesaianController::class, 'index'])->name('dashboard');
          // Riwayat approval yang sudah selesai
          Route::get('/riwayat', [\App\Http\Controllers\PenyelesaianController::class, 'riwayat'])->name('riwayat');
               // Proses masal permohonan (define before parameterized routes to avoid route-model binding conflicts)
               Route::post('/mass-process', [\App\Http\Controllers\PenyelesaianController::class, 'massProcess'])->name('mass_process');

               // Menampilkan form approval untuk permohonan tertentu
               Route::get('/{permohonan}', [\App\Http\Controllers\PenyelesaianController::class, 'create'])->name('create');
               // Menyimpan data dari form approval
               Route::post('/{permohonan}', [\App\Http\Controllers\PenyelesaianController::class, 'store'])->name('store');
     });

     // --- Rute Penyelesaian Tugas II (Duplicate dari Approval) ---
     // Menggunakan PenyelesaianIIController untuk sistem approval kedua
     Route::prefix('approval-ii')->name('approval-ii.')->middleware('can:approval-dashboard')->group(function () {
          // Dashboard untuk melihat tugas yang perlu diselesaikan
          Route::get('/', [\App\Http\Controllers\PenyelesaianIIController::class, 'index'])->name('dashboard');
          // Riwayat approval yang sudah selesai
          Route::get('/riwayat', [\App\Http\Controllers\PenyelesaianIIController::class, 'riwayat'])->name('riwayat');
               // Proses masal permohonan (define before parameterized routes to avoid route-model binding conflicts)
               Route::post('/mass-process', [\App\Http\Controllers\PenyelesaianIIController::class, 'massProcess'])->name('mass_process');
               // Menampilkan form approval untuk permohonan tertentu
               Route::get('/{permohonan}', [\App\Http\Controllers\PenyelesaianIIController::class, 'create'])->name('create');
               // Menyimpan data dari form approval
               Route::post('/{permohonan}', [\App\Http\Controllers\PenyelesaianIIController::class, 'store'])->name('store');
     });

               // Minimal CRUD routes for new simplified Tagihan Kontainer Sewa (daftar)
               // Controller should be implemented as resourceful controller: index, create, store, show, edit, update, destroy
               // Download CSV template for daftar tagihan
               Route::get('daftar-tagihan-kontainer-sewa/template/csv', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'downloadTemplateCsv'])
                    ->name('daftar-tagihan-kontainer-sewa.template.csv')
                    ->middleware('can:tagihan-kontainer-sewa-index');

               // Import page (UI for import) - GET route must come BEFORE POST route
               Route::get('daftar-tagihan-kontainer-sewa/import', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'importPage'])
                    ->name('daftar-tagihan-kontainer-sewa.import')
                    ->middleware('can:tagihan-kontainer-sewa-create');

               // Import CSV upload endpoint (legacy - for backward compatibility)
               Route::post('daftar-tagihan-kontainer-sewa/import', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'importCsv'])
                    ->name('daftar-tagihan-kontainer-sewa.import.legacy')
                    ->middleware('can:tagihan-kontainer-sewa-create');

               // Process import (handle file upload and processing)
               Route::post('daftar-tagihan-kontainer-sewa/import/process', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'processImport'])
                    ->name('daftar-tagihan-kontainer-sewa.import.process')
                    ->middleware('can:tagihan-kontainer-sewa-create');

               // Export data to CSV
               Route::get('daftar-tagihan-kontainer-sewa/export', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'export'])
                    ->name('daftar-tagihan-kontainer-sewa.export')
                    ->middleware('can:tagihan-kontainer-sewa-create');

               // Export template (Excel/CSV template download)
               Route::get('daftar-tagihan-kontainer-sewa/export-template', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'exportTemplate'])
                    ->name('daftar-tagihan-kontainer-sewa.export-template')
                    ->middleware('can:tagihan-kontainer-sewa-create');

               // Import CSV with automatic grouping (legacy route)
               Route::post('daftar-tagihan-kontainer-sewa/import-grouped', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'importWithGrouping'])
                    ->name('daftar-tagihan-kontainer-sewa.import.grouped')
                    ->middleware('can:tagihan-kontainer-sewa-create');

               // Test import page (temporary debug route)
               Route::get('test-import', function() {
                   return view('test-import');
               })->name('test-import')->middleware('can:tagihan-kontainer-sewa-create');

               // Debug import endpoint
               Route::any('debug-import', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'debugImport'])
                    ->name('debug-import');

               // Update adjustment endpoint
               Route::patch('daftar-tagihan-kontainer-sewa/{id}/adjustment', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'updateAdjustment'])
                    ->name('daftar-tagihan-kontainer-sewa.adjustment.update')
                    ->middleware('can:tagihan-kontainer-sewa-update');

               // Update adjustment note endpoint
               Route::patch('daftar-tagihan-kontainer-sewa/{id}/adjustment-note', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'updateAdjustmentNote'])
                    ->name('daftar-tagihan-kontainer-sewa.adjustment-note.update')
                    ->middleware('can:tagihan-kontainer-sewa-update');

               // Update vendor info endpoint
               Route::patch('daftar-tagihan-kontainer-sewa/{id}/vendor-info', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'updateVendorInfo'])
                    ->name('daftar-tagihan-kontainer-sewa.vendor-info.update')
                    ->middleware('can:tagihan-kontainer-sewa-update');

               // Update group info endpoint
               Route::patch('daftar-tagihan-kontainer-sewa/{id}/group-info', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'updateGroupInfo'])
                    ->name('daftar-tagihan-kontainer-sewa.group-info.update')
                    ->middleware('can:tagihan-kontainer-sewa-update');

               // Individual routes with specific middleware instead of resource
               Route::get('daftar-tagihan-kontainer-sewa', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'index'])
                    ->name('daftar-tagihan-kontainer-sewa.index')
                    ->middleware('can:tagihan-kontainer-sewa-index');

               Route::get('daftar-tagihan-kontainer-sewa/create', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'create'])
                    ->name('daftar-tagihan-kontainer-sewa.create')
                    ->middleware('can:tagihan-kontainer-sewa-create');

               Route::get('daftar-tagihan-kontainer-sewa/create-group', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'createGroup'])
                    ->name('daftar-tagihan-kontainer-sewa.create-group')
                    ->middleware('can:tagihan-kontainer-sewa-create');

               Route::post('daftar-tagihan-kontainer-sewa/store-group', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'storeGroup'])
                    ->name('daftar-tagihan-kontainer-sewa.store-group')
                    ->middleware('can:tagihan-kontainer-sewa-create');

               Route::post('daftar-tagihan-kontainer-sewa', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'store'])
                    ->name('daftar-tagihan-kontainer-sewa.store')
                    ->middleware('can:tagihan-kontainer-sewa-create');

               Route::get('daftar-tagihan-kontainer-sewa/{tagihan}', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'show'])
                    ->name('daftar-tagihan-kontainer-sewa.show')
                    ->middleware('can:tagihan-kontainer-sewa-index');

               Route::get('daftar-tagihan-kontainer-sewa/{tagihan}/edit', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'edit'])
                    ->name('daftar-tagihan-kontainer-sewa.edit')
                    ->middleware('can:tagihan-kontainer-sewa-update');

               Route::put('daftar-tagihan-kontainer-sewa/{tagihan}', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'update'])
                    ->name('daftar-tagihan-kontainer-sewa.update')
                    ->middleware('can:tagihan-kontainer-sewa-update');

               Route::delete('daftar-tagihan-kontainer-sewa/{tagihan}', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'destroy'])
                    ->name('daftar-tagihan-kontainer-sewa.destroy')
                    ->middleware('can:tagihan-kontainer-sewa-destroy');

               // Bulk operations for daftar tagihan kontainer sewa
               Route::delete('daftar-tagihan-kontainer-sewa/bulk-delete', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'bulkDelete'])
                    ->name('daftar-tagihan-kontainer-sewa.bulk-delete')
                    ->middleware('can:tagihan-kontainer-sewa-destroy');

               // Group management routes
               Route::get('daftar-tagihan-kontainer-sewa/groups', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'getGroups'])
                    ->name('daftar-tagihan-kontainer-sewa.groups')
                    ->middleware('can:tagihan-kontainer-sewa-destroy');

               Route::delete('daftar-tagihan-kontainer-sewa/delete-groups', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'deleteGroups'])
                    ->name('daftar-tagihan-kontainer-sewa.delete-groups')
                    ->middleware('can:tagihan-kontainer-delete');

               Route::patch('daftar-tagihan-kontainer-sewa/ungroup-containers', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'ungroupContainers'])
                    ->name('daftar-tagihan-kontainer-sewa.ungroup-containers')
                    ->middleware('can:tagihan-kontainer-delete');

               Route::post('daftar-tagihan-kontainer-sewa/masukan-ke-pranota', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'masukanKePranota'])
                    ->name('daftar-tagihan-kontainer-sewa.masukan-ke-pranota')
                    ->middleware('can:pranota-create');

               Route::post('daftar-tagihan-kontainer-sewa/bulk-update-status', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'bulkUpdateStatus'])
                    ->name('daftar-tagihan-kontainer-sewa.bulk-update-status')
                    ->middleware('can:tagihan-kontainer-update');

               // Pembayaran Pranota Kontainer routes
               Route::prefix('pembayaran-pranota-kontainer')->name('pembayaran-pranota-kontainer.')->group(function () {
                    Route::get('/', [PembayaranPranotaKontainerController::class, 'index'])->name('index')
                         ->middleware('can:pembayaran-pranota-kontainer-view');
                    Route::get('/create', [PembayaranPranotaKontainerController::class, 'create'])->name('create')
                         ->middleware('can:pembayaran-pranota-kontainer-create');
                    Route::get('/generate-nomor', [PembayaranPranotaKontainerController::class, 'generateNomorPembayaran'])->name('generate-nomor')
                         ->middleware('can:pembayaran-pranota-kontainer-create');
                    Route::get('/get-available-dp', [PembayaranPranotaKontainerController::class, 'getAvailableDP'])->name('get-available-dp')
                         ->middleware('can:pembayaran-pranota-kontainer-create');
                    Route::post('/payment-form', [PembayaranPranotaKontainerController::class, 'showPaymentForm'])->name('payment-form')
                         ->middleware('can:pembayaran-pranota-kontainer-view');
                    Route::post('/', [PembayaranPranotaKontainerController::class, 'store'])->name('store')
                         ->middleware('can:pembayaran-pranota-kontainer-create');
                    Route::get('/{id}', [PembayaranPranotaKontainerController::class, 'show'])->name('show')
                         ->middleware('can:pembayaran-pranota-kontainer-view');
                    Route::get('/{id}/edit', [PembayaranPranotaKontainerController::class, 'edit'])->name('edit')
                         ->middleware('can:pembayaran-pranota-kontainer-update');
                    Route::put('/{id}', [PembayaranPranotaKontainerController::class, 'update'])->name('update')
                         ->middleware('can:pembayaran-pranota-kontainer-update');
                    Route::delete('/{id}', [PembayaranPranotaKontainerController::class, 'destroy'])->name('destroy')
                         ->middleware('can:pembayaran-pranota-kontainer-delete');
                    Route::delete('/{pembayaranId}/pranota/{pranotaId}', [PembayaranPranotaKontainerController::class, 'removePranota'])->name('remove-pranota')
                         ->middleware('can:pembayaran-pranota-kontainer-update');
                    Route::get('/{id}/print', [PembayaranPranotaKontainerController::class, 'print'])->name('print')
                         ->middleware('can:pembayaran-pranota-kontainer-print');
               });

               // API route for transaction detail modal
               Route::get('/api/pembayaran-pranota-kontainer/detail/{nomorPembayaran}', [PembayaranPranotaKontainerController::class, 'getDetailByNomor'])
                    ->middleware(['auth', 'can:pembayaran-pranota-kontainer-view']);

});

// Profile Management Routes (for all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit')->middleware('can:profile-update');
        Route::put('/account', [\App\Http\Controllers\ProfileController::class, 'updateAccount'])->name('profile.update.account')->middleware('can:profile-update');
        Route::put('/personal', [\App\Http\Controllers\ProfileController::class, 'updatePersonal'])->name('profile.update.personal')->middleware('can:profile-update');
        Route::post('/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.update.avatar')->middleware('can:profile-update');
        Route::delete('/delete', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy')->middleware('can:profile-delete');
    });
});

// Perbaikan Kontainer Routes (Independent from Master)
Route::middleware(['auth'])->group(function() {
    // Perbaikan Kontainer routes - granular permissions
    Route::get('perbaikan-kontainer', [\App\Http\Controllers\PerbaikanKontainerController::class, 'index'])
         ->name('perbaikan-kontainer.index')
         ->middleware('can:tagihan-perbaikan-kontainer-view');
    Route::get('perbaikan-kontainer/create', [\App\Http\Controllers\PerbaikanKontainerController::class, 'create'])
         ->name('perbaikan-kontainer.create')
         ->middleware('can:tagihan-perbaikan-kontainer-create');
    Route::post('perbaikan-kontainer', [\App\Http\Controllers\PerbaikanKontainerController::class, 'store'])
         ->name('perbaikan-kontainer.store')
         ->middleware('can:tagihan-perbaikan-kontainer-create');
    Route::get('perbaikan-kontainer/{perbaikanKontainer}', [\App\Http\Controllers\PerbaikanKontainerController::class, 'show'])
         ->name('perbaikan-kontainer.show')
         ->middleware('can:tagihan-perbaikan-kontainer-view');
    Route::get('perbaikan-kontainer/{perbaikanKontainer}/print', [\App\Http\Controllers\PerbaikanKontainerController::class, 'print'])
         ->name('perbaikan-kontainer.print')
         ->middleware('can:tagihan-perbaikan-kontainer-print');
    Route::get('perbaikan-kontainer/{perbaikanKontainer}/edit', [\App\Http\Controllers\PerbaikanKontainerController::class, 'edit'])
         ->name('perbaikan-kontainer.edit')
         ->middleware('can:tagihan-perbaikan-kontainer-update');
    Route::put('perbaikan-kontainer/{perbaikanKontainer}', [\App\Http\Controllers\PerbaikanKontainerController::class, 'update'])
         ->name('perbaikan-kontainer.update')
         ->middleware('can:tagihan-perbaikan-kontainer-update');
    Route::delete('perbaikan-kontainer/{perbaikanKontainer}', [\App\Http\Controllers\PerbaikanKontainerController::class, 'destroy'])
         ->name('perbaikan-kontainer.destroy')
         ->middleware('can:tagihan-perbaikan-kontainer-delete');

    // Additional perbaikan kontainer routes
    Route::patch('perbaikan-kontainer/{perbaikanKontainer}/status', [\App\Http\Controllers\PerbaikanKontainerController::class, 'updateStatus'])
         ->name('perbaikan-kontainer.update-status')
         ->middleware('can:perbaikan-kontainer-update');
    Route::get('perbaikan-kontainer/print', [\App\Http\Controllers\PerbaikanKontainerController::class, 'printBulk'])
         ->name('perbaikan-kontainer.print-bulk')
         ->middleware('can:perbaikan-kontainer-view');

    // Bulk operations for perbaikan kontainer
    Route::delete('perbaikan-kontainer/bulk-delete', [\App\Http\Controllers\PerbaikanKontainerController::class, 'bulkDelete'])
         ->name('perbaikan-kontainer.bulk-delete')
         ->middleware('can:perbaikan-kontainer-delete');
    Route::patch('perbaikan-kontainer/bulk-update-status', [\App\Http\Controllers\PerbaikanKontainerController::class, 'bulkUpdateStatus'])
         ->name('perbaikan-kontainer.bulk-update-status')
         ->middleware('can:perbaikan-kontainer-update');
    Route::patch('perbaikan-kontainer/bulk-pranota', [\App\Http\Controllers\PerbaikanKontainerController::class, 'bulkPranota'])
         ->name('perbaikan-kontainer.bulk-pranota')
         ->middleware('can:perbaikan-kontainer-update');

    // Add catatan route
    Route::post('perbaikan-kontainer/add-catatan', [\App\Http\Controllers\PerbaikanKontainerController::class, 'addCatatan'])
         ->name('perbaikan-kontainer.add-catatan')
         ->middleware('can:perbaikan-kontainer-update');

    // Pranota Perbaikan Kontainer routes
    Route::get('pranota-perbaikan-kontainer', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'index'])
         ->name('pranota-perbaikan-kontainer.index')
         ->middleware('can:pranota-perbaikan-kontainer-view');
    Route::get('pranota-perbaikan-kontainer/create', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'create'])
         ->name('pranota-perbaikan-kontainer.create')
         ->middleware('can:pranota-perbaikan-kontainer-create');
    Route::post('pranota-perbaikan-kontainer', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'store'])
         ->name('pranota-perbaikan-kontainer.store')
         ->middleware('can:pranota-perbaikan-kontainer-create');
    Route::get('pranota-perbaikan-kontainer/{pranotaPerbaikanKontainer}', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'show'])
         ->name('pranota-perbaikan-kontainer.show')
         ->middleware('can:pranota-perbaikan-kontainer-view');
    Route::get('pranota-perbaikan-kontainer/{pranotaPerbaikanKontainer}/edit', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'edit'])
         ->name('pranota-perbaikan-kontainer.edit')
         ->middleware('can:pranota-perbaikan-kontainer-update');
    Route::put('pranota-perbaikan-kontainer/{pranotaPerbaikanKontainer}', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'update'])
         ->name('pranota-perbaikan-kontainer.update')
         ->middleware('can:pranota-perbaikan-kontainer-update');
    Route::delete('pranota-perbaikan-kontainer/{pranotaPerbaikanKontainer}', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'destroy'])
         ->name('pranota-perbaikan-kontainer.destroy')
         ->middleware('can:pranota-perbaikan-kontainer-delete');
    Route::get('pranota-perbaikan-kontainer/{pranotaPerbaikanKontainer}/print', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'print'])
         ->name('pranota-perbaikan-kontainer.print')
         ->middleware('can:pranota-perbaikan-kontainer-print');

    // Tagihan CAT routes
    Route::post('tagihan-cat/bulk-delete', [TagihanCatController::class, 'bulkDelete'])
         ->name('tagihan-cat.bulk-delete')
         ->middleware('can:tagihan-cat-delete');
    Route::post('tagihan-cat/bulk-update-status', [TagihanCatController::class, 'bulkUpdateStatus'])
         ->name('tagihan-cat.bulk-update-status')
         ->middleware('can:tagihan-cat-update');
    Route::get('tagihan-cat', [TagihanCatController::class, 'index'])
         ->name('tagihan-cat.index')
         ->middleware('can:tagihan-cat-view');
    Route::get('tagihan-cat/create', [TagihanCatController::class, 'create'])
         ->name('tagihan-cat.create')
         ->middleware('can:tagihan-cat-create');
    Route::post('tagihan-cat', [TagihanCatController::class, 'store'])
         ->name('tagihan-cat.store')
         ->middleware('can:tagihan-cat-create');
    Route::get('tagihan-cat/{tagihanCat}', [TagihanCatController::class, 'show'])
         ->name('tagihan-cat.show')
         ->middleware('can:tagihan-cat-view');
    Route::get('tagihan-cat/{tagihanCat}/edit', [TagihanCatController::class, 'edit'])
         ->name('tagihan-cat.edit')
         ->middleware('can:tagihan-cat-update');
    Route::put('tagihan-cat/{tagihanCat}', [TagihanCatController::class, 'update'])
         ->name('tagihan-cat.update')
         ->middleware('can:tagihan-cat-update');
    Route::delete('tagihan-cat/{tagihanCat}', [TagihanCatController::class, 'destroy'])
         ->name('tagihan-cat.destroy')
         ->middleware('can:tagihan-cat-delete');

    // Tagihan OB routes
    Route::get('tagihan-ob', [\App\Http\Controllers\TagihanObController::class, 'index'])
         ->name('tagihan-ob.index')
         ->middleware('can:tagihan-ob-view');
    Route::get('tagihan-ob/create', [\App\Http\Controllers\TagihanObController::class, 'create'])
         ->name('tagihan-ob.create')
         ->middleware('can:tagihan-ob-create');
    Route::post('tagihan-ob', [\App\Http\Controllers\TagihanObController::class, 'store'])
         ->name('tagihan-ob.store')
         ->middleware('can:tagihan-ob-create');
    Route::get('tagihan-ob/{tagihanOb}', [\App\Http\Controllers\TagihanObController::class, 'show'])
         ->name('tagihan-ob.show')
         ->middleware('can:tagihan-ob-view');
    Route::get('tagihan-ob/{tagihanOb}/edit', [\App\Http\Controllers\TagihanObController::class, 'edit'])
         ->name('tagihan-ob.edit')
         ->middleware('can:tagihan-ob-update');
    Route::put('tagihan-ob/{tagihanOb}', [\App\Http\Controllers\TagihanObController::class, 'update'])
         ->name('tagihan-ob.update')
         ->middleware('can:tagihan-ob-update');
    Route::delete('tagihan-ob/{tagihanOb}', [\App\Http\Controllers\TagihanObController::class, 'destroy'])
         ->name('tagihan-ob.destroy')
         ->middleware('can:tagihan-ob-delete');
    Route::post('tagihan-ob/{tagihanOb}/update-field', [\App\Http\Controllers\TagihanObController::class, 'updateField'])
         ->name('tagihan-ob.update-field')
         ->middleware('can:tagihan-ob-update');
    Route::post('tagihan-ob/create-from-ob-muat', [\App\Http\Controllers\TagihanObController::class, 'createFromObMuat'])
         ->name('tagihan-ob.create-from-ob-muat')
         ->middleware('can:tagihan-ob-create');

    // Pranota OB routes
    Route::get('pranota-ob', [\App\Http\Controllers\PranotaObController::class, 'index'])
         ->name('pranota-ob.index')
         ->middleware('can:pranota-ob-view');
    Route::get('pranota-ob/create', [\App\Http\Controllers\PranotaObController::class, 'create'])
         ->name('pranota-ob.create')
         ->middleware('can:pranota-ob-create');
    Route::post('pranota-ob', [\App\Http\Controllers\PranotaObController::class, 'store'])
         ->name('pranota-ob.store')
         ->middleware('can:pranota-ob-create');
    Route::get('pranota-ob/{pranotaOb}', [\App\Http\Controllers\PranotaObController::class, 'show'])
         ->name('pranota-ob.show')
         ->middleware('can:pranota-ob-view');
    Route::get('pranota-ob/{pranotaOb}/edit', [\App\Http\Controllers\PranotaObController::class, 'edit'])
         ->name('pranota-ob.edit')
         ->middleware('can:pranota-ob-update');
    Route::put('pranota-ob/{pranotaOb}', [\App\Http\Controllers\PranotaObController::class, 'update'])
         ->name('pranota-ob.update')
         ->middleware('can:pranota-ob-update');
    Route::delete('pranota-ob/{pranotaOb}', [\App\Http\Controllers\PranotaObController::class, 'destroy'])
         ->name('pranota-ob.destroy')
         ->middleware('can:pranota-ob-delete');
    Route::post('pranota-ob/{pranotaOb}/approve', [\App\Http\Controllers\PranotaObController::class, 'approve'])
         ->name('pranota-ob.approve')
         ->middleware('can:pranota-ob-approve');
    Route::post('pranota-ob/{pranotaOb}/submit', [\App\Http\Controllers\PranotaObController::class, 'submit'])
         ->name('pranota-ob.submit')
         ->middleware('can:pranota-ob-update');
    Route::post('pranota-ob/{pranotaOb}/cancel', [\App\Http\Controllers\PranotaObController::class, 'cancel'])
         ->name('pranota-ob.cancel')
         ->middleware('can:pranota-ob-update');
    Route::get('pranota-ob/{pranotaOb}/print', [\App\Http\Controllers\PranotaObController::class, 'print'])
         ->name('pranota-ob.print')
         ->middleware('can:pranota-ob-print');
    Route::get('api/available-tagihan-ob', [\App\Http\Controllers\PranotaObController::class, 'getAvailableTagihanOb'])
         ->name('api.available-tagihan-ob');

    // Pranota CAT routes
    Route::get('pranota-cat', [\App\Http\Controllers\PranotaTagihanCatController::class, 'index'])
         ->name('pranota-cat.index')
         ->middleware('can:pranota-cat-view');
    Route::get('pranota-cat/{id}', [\App\Http\Controllers\PranotaTagihanCatController::class, 'show'])
         ->name('pranota-cat.show')
         ->middleware('can:pranota-cat-view');
    Route::get('pranota-cat/{id}/print', [\App\Http\Controllers\PranotaTagihanCatController::class, 'print'])
         ->name('pranota-cat.print')
         ->middleware('can:pranota-cat-print');
    Route::post('pranota-cat', [\App\Http\Controllers\PranotaTagihanCatController::class, 'store'])
         ->name('pranota-cat.store')
         ->middleware('can:pranota-cat-create');
    Route::post('pranota-cat/bulk-create-from-tagihan-cat', [\App\Http\Controllers\PranotaTagihanCatController::class, 'bulkCreateFromTagihanCat'])
         ->name('pranota-cat.bulk-create-from-tagihan-cat')
         ->middleware('can:pranota-cat-create');
    Route::get('pranota-cat/generate-nomor', [\App\Http\Controllers\PranotaTagihanCatController::class, 'generateNomor'])
         ->name('pranota-cat.generate-nomor')
         ->middleware('can:pranota-cat-create');
    Route::post('pranota-cat/bulk-status-update', [\App\Http\Controllers\PranotaTagihanCatController::class, 'bulkStatusUpdate'])
         ->name('pranota-cat.bulk-status-update')
         ->middleware('can:pranota-cat-update');

    // Pranota Kontainer Sewa routes
    Route::get('pranota-kontainer-sewa', [PranotaTagihanKontainerSewaController::class, 'index'])
         ->name('pranota-kontainer-sewa.index')
         ->middleware('can:pranota-kontainer-sewa-view');
    Route::get('pranota-kontainer-sewa/import', [PranotaTagihanKontainerSewaController::class, 'importPage'])
         ->name('pranota-kontainer-sewa.import');
         // ->middleware('can:pranota-kontainer-sewa-create'); // Commented temporarily
    Route::post('pranota-kontainer-sewa/import', [PranotaTagihanKontainerSewaController::class, 'importCsv'])
         ->name('pranota-kontainer-sewa.import.process');
         // ->middleware('can:pranota-kontainer-sewa-create'); // Commented temporarily
    Route::get('pranota-kontainer-sewa/template/csv', [PranotaTagihanKontainerSewaController::class, 'downloadTemplateCsv'])
         ->name('pranota-kontainer-sewa.template.csv');
         // ->middleware('can:pranota-kontainer-sewa-create'); // Commented temporarily
    Route::get('pranota-kontainer-sewa/create', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'create'])
         ->name('pranota-kontainer-sewa.create')
         ->middleware('can:pranota-kontainer-sewa-create');
    Route::get('pranota-kontainer-sewa/{pranota}', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'show'])
         ->name('pranota-kontainer-sewa.show')
         ->middleware('can:pranota-kontainer-sewa-view');
    Route::get('pranota-kontainer-sewa/{pranota}/edit', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'edit'])
         ->name('pranota-kontainer-sewa.edit')
         ->middleware('can:pranota-kontainer-sewa-edit');
    Route::put('pranota-kontainer-sewa/{pranota}', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'update'])
         ->name('pranota-kontainer-sewa.update')
         ->middleware('can:pranota-kontainer-sewa-update');
    Route::get('pranota-kontainer-sewa/{pranota}/print', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'print'])
         ->name('pranota-kontainer-sewa.print')
         ->middleware('can:pranota-kontainer-sewa-print');
    Route::post('pranota-kontainer-sewa', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'store'])
         ->name('pranota-kontainer-sewa.store')
         ->middleware('can:pranota-kontainer-sewa-create');
    Route::post('pranota-kontainer-sewa/bulk-create-from-tagihan-kontainer-sewa', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'bulkCreateFromTagihanKontainerSewa'])
         ->name('pranota-kontainer-sewa.bulk-create-from-tagihan-kontainer-sewa')
         ->middleware('can:pranota-kontainer-sewa-create');
    Route::get('pranota-kontainer-sewa/next-number', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'getNextPranotaNumber'])
         ->name('pranota-kontainer-sewa.next-number')
         ->middleware('can:pranota-kontainer-sewa-view');
    Route::post('pranota-kontainer-sewa/bulk-status-update', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'bulkStatusUpdate'])
         ->name('pranota-kontainer-sewa.bulk-update-status')
         ->middleware('can:pranota-kontainer-sewa-update');
    Route::patch('pranota-kontainer-sewa/{pranota}/status', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'updateStatus'])
         ->name('pranota-kontainer-sewa.update.status')
         ->middleware('can:pranota-kontainer-sewa-update');
    Route::post('pranota-kontainer-sewa/{pranota}/lepas-kontainer', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'lepasKontainer'])
         ->name('pranota-kontainer-sewa.lepas-kontainer')
         ->middleware('can:pranota-kontainer-sewa-update');
    Route::delete('pranota-kontainer-sewa/{pranota}', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'destroy'])
         ->name('pranota-kontainer-sewa.destroy')
         ->middleware('can:pranota-kontainer-sewa-delete');
    Route::delete('pranota-kontainer-sewa-bulk-delete', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'bulkDelete'])
         ->name('pranota-kontainer-sewa.bulk-delete')
         ->middleware('can:pranota-kontainer-sewa-delete');

    // New routes for vendor invoice grouping
    Route::post('pranota-kontainer-sewa/create-by-vendor-invoice-group', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'createPranotaByVendorInvoiceGroup'])
         ->name('pranota-kontainer-sewa.create-by-vendor-invoice-group')
         ->middleware('can:pranota-kontainer-sewa-create');
    Route::post('pranota-kontainer-sewa/preview-vendor-invoice-grouping', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'previewVendorInvoiceGrouping'])
         ->name('pranota-kontainer-sewa.preview-vendor-invoice-grouping')
         ->middleware('can:pranota-kontainer-sewa-view');
    Route::post('pranota-kontainer-sewa/add-items-to-existing', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'addItemsToExisting'])
         ->name('pranota-kontainer-sewa.add-items-to-existing')
         ->middleware('can:pranota-kontainer-sewa-update');

               // Pranota Sewa routes
               Route::prefix('pranota')->name('pranota.')->group(function () {
                    Route::get('/', [PranotaTagihanKontainerSewaController::class, 'index'])->name('index')
                         ->middleware('can:pranota-view');
                    Route::get('/create', [PranotaTagihanKontainerSewaController::class, 'create'])->name('create')
                         ->middleware('can:pranota-create');
                    // Print route must be declared before the parameterized show route
                    Route::get('/{pranota}/print', [PranotaTagihanKontainerSewaController::class, 'print'])->name('print')
                         ->middleware('can:pranota-print');
                    Route::get('/{pranota}', [PranotaTagihanKontainerSewaController::class, 'show'])->name('show')
                         ->middleware('can:pranota-view');
                    Route::post('/', [PranotaTagihanKontainerSewaController::class, 'store'])->name('store')
                         ->middleware('can:pranota-create');
                    Route::post('/bulk', [PranotaTagihanKontainerSewaController::class, 'bulkStore'])->name('bulk.store')
                         ->middleware('can:pranota-create');
                    Route::patch('/{pranota}/status', [PranotaTagihanKontainerSewaController::class, 'updateStatus'])->name('update.status')
                         ->middleware('can:pranota-update');
                    Route::post('/{pranota}/lepas-kontainer', [PranotaTagihanKontainerSewaController::class, 'lepasKontainer'])->name('lepas-kontainer')
                         ->middleware('can:pranota-update');
                    Route::delete('/{pranota}', [PranotaTagihanKontainerSewaController::class, 'destroy'])->name('destroy')
                         ->middleware('can:pranota-delete');
               });

               // Pembayaran Pranota Kontainer routes
               Route::prefix('pembayaran-pranota-kontainer')->name('pembayaran-pranota-kontainer.')->group(function () {
                    Route::get('/', [PembayaranPranotaKontainerController::class, 'index'])->name('index')
                         ->middleware('can:pembayaran-pranota-kontainer-view');
                    Route::get('/create', [PembayaranPranotaKontainerController::class, 'create'])->name('create')
                         ->middleware('can:pembayaran-pranota-kontainer-create');
                    Route::post('/payment-form', [PembayaranPranotaKontainerController::class, 'showPaymentForm'])->name('payment-form')
                         ->middleware('can:pembayaran-pranota-kontainer-view');
                    Route::post('/', [PembayaranPranotaKontainerController::class, 'store'])->name('store')
                         ->middleware('can:pembayaran-pranota-kontainer-create');
                    Route::get('/{id}', [PembayaranPranotaKontainerController::class, 'show'])->name('show')
                         ->middleware('can:pembayaran-pranota-kontainer-view');
                    Route::get('/{id}/edit', [PembayaranPranotaKontainerController::class, 'edit'])->name('edit')
                         ->middleware('can:pembayaran-pranota-kontainer-update');
                    Route::put('/{id}', [PembayaranPranotaKontainerController::class, 'update'])->name('update')
                         ->middleware('can:pembayaran-pranota-kontainer-update');
                    Route::delete('/{id}', [PembayaranPranotaKontainerController::class, 'destroy'])->name('destroy')
                         ->middleware('can:pembayaran-pranota-kontainer-delete');
                    Route::delete('/{pembayaranId}/pranota/{pranotaId}', [PembayaranPranotaKontainerController::class, 'removePranota'])->name('remove-pranota')
                         ->middleware('can:pembayaran-pranota-kontainer-update');
                    Route::get('/{id}/print', [PembayaranPranotaKontainerController::class, 'print'])->name('print')
                         ->middleware('can:pembayaran-pranota-kontainer-print');
               });

     // Admin: daftar semua fitur (permissions + routes)
     Route::get('/admin/features', [\App\Http\Controllers\AdminController::class, 'features'])
          ->name('admin.features')
          ->middleware(['auth', 'role:admin']);
     Route::get('/admin/debug-perms', [\App\Http\Controllers\AdminController::class, 'debug'])
          ->name('admin.debug.perms')
          ->middleware(['auth', 'role:admin']);

     // User Approval System Routes
     Route::prefix('admin/user-approval')->middleware(['auth'])->group(function () {
         Route::get('/', [\App\Http\Controllers\UserApprovalController::class, 'index'])->name('admin.user-approval.index');
         Route::get('/{user}', [\App\Http\Controllers\UserApprovalController::class, 'show'])->name('admin.user-approval.show');
         Route::post('/{user}/approve', [\App\Http\Controllers\UserApprovalController::class, 'approve'])->name('admin.user-approval.approve');
         Route::post('/{user}/reject', [\App\Http\Controllers\UserApprovalController::class, 'reject'])->name('admin.user-approval.reject');
     });

});

// API route for next pranota number
Route::get('api/next-pranota-number', [PranotaTagihanKontainerSewaController::class, 'getNextPranotaNumber'])
     ->name('api.next-pranota-number')
     ->middleware('auth');

// Pembayaran Pranota CAT routes (FINAL VERSION - with all CRUD operations)
Route::prefix('pembayaran-pranota-cat')->name('pembayaran-pranota-cat.')->middleware(['auth'])->group(function () {
    Route::get('/', [PembayaranPranotaCatController::class, 'index'])->name('index')
         ->middleware('can:pranota-cat-view');
    Route::get('/create', [PembayaranPranotaCatController::class, 'create'])->name('create')
         ->middleware('can:pranota-cat-create');
    Route::post('/payment-form', [PembayaranPranotaCatController::class, 'showPaymentForm'])->name('payment-form')
         ->middleware('can:pranota-cat-view');
    Route::post('/', [PembayaranPranotaCatController::class, 'store'])->name('store')
         ->middleware('can:pranota-cat-create');
    Route::get('/{id}', [PembayaranPranotaCatController::class, 'show'])->name('show')
         ->middleware('can:pranota-cat-view');
    Route::get('/{id}/edit', [PembayaranPranotaCatController::class, 'edit'])->name('edit')
         ->middleware('can:pranota-cat-update');
    Route::put('/{id}', [PembayaranPranotaCatController::class, 'update'])->name('update')
         ->middleware('can:pranota-cat-update');
    Route::delete('/{id}', [PembayaranPranotaCatController::class, 'destroy'])->name('destroy')
         ->middleware('can:pranota-cat-delete');
});

// Additional route for pembayaran-pranota-cat print
Route::get('pembayaran-pranota-cat/{id}/print', [PembayaranPranotaCatController::class, 'print'])
     ->name('pembayaran-pranota-cat.print')
     ->middleware(['auth', 'can:pembayaran-pranota-cat-view']);

// Aktivitas Lain-lain routes
Route::prefix('aktivitas-lainnya')->name('aktivitas-lainnya.')->middleware(['auth'])->group(function () {
    Route::get('/', [AktivitasLainnyaController::class, 'index'])->name('index')
         ->middleware('can:aktivitas-lainnya-view');
    Route::get('/create', [AktivitasLainnyaController::class, 'create'])->name('create')
         ->middleware('can:aktivitas-lainnya-create');
    Route::post('/', [AktivitasLainnyaController::class, 'store'])->name('store')
         ->middleware('can:aktivitas-lainnya-create');
    Route::get('/{aktivitasLainnya}', [AktivitasLainnyaController::class, 'show'])->name('show')
         ->middleware('can:aktivitas-lainnya-view');
    Route::get('/{aktivitasLainnya}/edit', [AktivitasLainnyaController::class, 'edit'])->name('edit')
         ->middleware('can:aktivitas-lainnya-update');
    Route::put('/{aktivitasLainnya}', [AktivitasLainnyaController::class, 'update'])->name('update')
         ->middleware('can:aktivitas-lainnya-update');
    Route::delete('/{aktivitasLainnya}', [AktivitasLainnyaController::class, 'destroy'])->name('destroy')
         ->middleware('can:aktivitas-lainnya-delete');
    Route::post('/{aktivitasLainnya}/submit', [AktivitasLainnyaController::class, 'submitForApproval'])->name('submit')
         ->middleware('can:aktivitas-lainnya-update');
    Route::post('/{aktivitasLainnya}/approve', [AktivitasLainnyaController::class, 'approve'])->name('approve')
         ->middleware('can:aktivitas-lainnya-approve');
    Route::post('/{aktivitasLainnya}/reject', [AktivitasLainnyaController::class, 'reject'])->name('reject')
         ->middleware('can:aktivitas-lainnya-approve');
});

// Pembayaran Aktivitas Lain-lain routes
Route::prefix('pembayaran-aktivitas-lainnya')->name('pembayaran-aktivitas-lainnya.')->middleware(['auth'])->group(function () {
    Route::get('/', [PembayaranAktivitasLainnyaController::class, 'index'])->name('index')
         ->middleware('can:pembayaran-aktivitas-lainnya-view');
    Route::get('/create', [PembayaranAktivitasLainnyaController::class, 'create'])->name('create')
         ->middleware('can:pembayaran-aktivitas-lainnya-create');
    Route::get('/export', [PembayaranAktivitasLainnyaController::class, 'export'])->name('export')
         ->middleware('can:pembayaran-aktivitas-lainnya-export');
    Route::post('/payment-form', [PembayaranAktivitasLainnyaController::class, 'showPaymentForm'])->name('payment-form')
         ->middleware('can:pembayaran-aktivitas-lainnya-view');
    Route::get('/generate-nomor-preview', [PembayaranAktivitasLainnyaController::class, 'generateNomorPreview'])->name('generate-nomor-preview')
         ->middleware('can:pembayaran-aktivitas-lainnya-create');
    Route::post('/', [PembayaranAktivitasLainnyaController::class, 'store'])->name('store')
         ->middleware('can:pembayaran-aktivitas-lainnya-create');
    Route::get('/{pembayaranAktivitasLainnya}', [PembayaranAktivitasLainnyaController::class, 'show'])->name('show')
         ->middleware('can:pembayaran-aktivitas-lainnya-view');
    Route::get('/{pembayaranAktivitasLainnya}/print', [PembayaranAktivitasLainnyaController::class, 'print'])->name('print')
         ->middleware('can:pembayaran-aktivitas-lainnya-print');
    Route::get('/{pembayaranAktivitasLainnya}/edit', [PembayaranAktivitasLainnyaController::class, 'edit'])->name('edit')
         ->middleware('can:pembayaran-aktivitas-lainnya-update');
    Route::put('/{pembayaranAktivitasLainnya}', [PembayaranAktivitasLainnyaController::class, 'update'])->name('update')
         ->middleware('can:pembayaran-aktivitas-lainnya-update');
    Route::delete('/{pembayaranAktivitasLainnya}', [PembayaranAktivitasLainnyaController::class, 'destroy'])->name('destroy')
         ->middleware('can:pembayaran-aktivitas-lainnya-delete');
    Route::post('/{pembayaranAktivitasLainnya}/approve', [PembayaranAktivitasLainnyaController::class, 'approve'])->name('approve')
         ->middleware('can:pembayaran-aktivitas-lainnya-approve');
    Route::post('/{pembayaranAktivitasLainnya}/reject', [PembayaranAktivitasLainnyaController::class, 'reject'])->name('reject')
         ->middleware('can:pembayaran-aktivitas-lainnya-approve');
});

// Pembayaran Uang Muka routes
Route::prefix('pembayaran-uang-muka')->name('pembayaran-uang-muka.')->middleware(['auth'])->group(function () {
    Route::get('/', [PembayaranUangMukaController::class, 'index'])->name('index')
         ->middleware('can:pembayaran-uang-muka-view');
    Route::get('/create', [PembayaranUangMukaController::class, 'create'])->name('create')
         ->middleware('can:pembayaran-uang-muka-create');
    Route::get('/generate-nomor', [PembayaranUangMukaController::class, 'generateNomor'])->name('generate-nomor')
         ->middleware('can:pembayaran-uang-muka-create');
    Route::post('/', [PembayaranUangMukaController::class, 'store'])->name('store')
         ->middleware('can:pembayaran-uang-muka-create');

    Route::get('/{id}', [PembayaranUangMukaController::class, 'show'])->name('show')
         ->middleware('can:pembayaran-uang-muka-view');
    Route::get('/{id}/edit', [PembayaranUangMukaController::class, 'edit'])->name('edit')
         ->middleware('can:pembayaran-uang-muka-edit');
    Route::put('/{id}', [PembayaranUangMukaController::class, 'update'])->name('update')
         ->middleware('can:pembayaran-uang-muka-edit');
    Route::delete('/{id}', [PembayaranUangMukaController::class, 'destroy'])->name('destroy')
         ->middleware('can:pembayaran-uang-muka-delete');
});

// Pembayaran OB routes
Route::prefix('pembayaran-ob')->name('pembayaran-ob.')->middleware(['auth'])->group(function () {
    Route::get('/', [PembayaranObController::class, 'index'])->name('index')
         ->middleware('can:pembayaran-ob-view');
    Route::get('/create', [PembayaranObController::class, 'create'])->name('create')
         ->middleware('can:pembayaran-ob-create');
    Route::get('/generate-nomor', [PembayaranObController::class, 'generateNomorPembayaran'])->name('generate-nomor')
         ->middleware('can:pembayaran-ob-create');
    Route::post('/', [PembayaranObController::class, 'store'])->name('store')
         ->middleware('can:pembayaran-ob-create');
    Route::get('/{id}', [PembayaranObController::class, 'show'])->name('show')
         ->middleware('can:pembayaran-ob-view');
    Route::get('/{id}/print', [PembayaranObController::class, 'print'])->name('print')
         ->middleware('can:pembayaran-ob-view');
    Route::get('/{id}/edit', [PembayaranObController::class, 'edit'])->name('edit')
         ->middleware('can:pembayaran-ob-edit');
    Route::put('/{id}', [PembayaranObController::class, 'update'])->name('update')
         ->middleware('can:pembayaran-ob-edit');
    Route::delete('/{id}', [PembayaranObController::class, 'destroy'])->name('destroy')
         ->middleware('can:pembayaran-ob-delete');
    Route::post('/{id}/approve', [PembayaranObController::class, 'approve'])->name('approve')
         ->middleware('can:pembayaran-ob-edit');
    Route::post('/{id}/reject', [PembayaranObController::class, 'reject'])->name('reject')
         ->middleware('can:pembayaran-ob-edit');
});

// Realisasi Uang Muka routes
Route::prefix('realisasi-uang-muka')->name('realisasi-uang-muka.')->middleware(['auth'])->group(function () {
    Route::get('/', [RealisasiUangMukaController::class, 'index'])->name('index')
         ->middleware('can:realisasi-uang-muka-view');
    Route::get('/create', [RealisasiUangMukaController::class, 'create'])->name('create')
         ->middleware('can:realisasi-uang-muka-create');
    Route::post('/generate-nomor', [RealisasiUangMukaController::class, 'generateNomor'])->name('generate-nomor')
         ->middleware('can:realisasi-uang-muka-create');
    Route::post('/force-generate-nomor', [RealisasiUangMukaController::class, 'forceGenerateNomor'])->name('force-generate-nomor')
         ->middleware('can:realisasi-uang-muka-create');
    Route::post('/', [RealisasiUangMukaController::class, 'store'])->name('store')
         ->middleware('can:realisasi-uang-muka-create');
    Route::get('/{id}', [RealisasiUangMukaController::class, 'show'])->name('show')
         ->middleware('can:realisasi-uang-muka-view');
    Route::get('/{id}/print', [RealisasiUangMukaController::class, 'print'])->name('print')
         ->middleware('can:realisasi-uang-muka-view');
    Route::get('/{id}/edit', [RealisasiUangMukaController::class, 'edit'])->name('edit')
         ->middleware('can:realisasi-uang-muka-edit');
    Route::put('/{id}', [RealisasiUangMukaController::class, 'update'])->name('update')
         ->middleware('can:realisasi-uang-muka-edit');
    Route::delete('/{id}', [RealisasiUangMukaController::class, 'destroy'])->name('destroy')
         ->middleware('can:realisasi-uang-muka-delete');
    Route::post('/{id}/approve', [RealisasiUangMukaController::class, 'approve'])->name('approve')
         ->middleware('can:realisasi-uang-muka-edit');
    Route::post('/{id}/reject', [RealisasiUangMukaController::class, 'reject'])->name('reject')
         ->middleware('can:realisasi-uang-muka-edit');

    // Debug route - no middleware to bypass permission issues
    Route::post('/debug-test', [RealisasiUangMukaController::class, 'store'])->name('debug-test');

});

// Debug routes (outside middleware)
Route::get('/realisasi-uang-muka/debug-simple', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'Debug route works!',
        'time' => now()->toDateTimeString()
    ]);
})->name('realisasi-uang-muka.debug-simple');

// Route to view debug logs
Route::get('/realisasi-uang-muka/debug-logs', function() {
    $logFile = storage_path('logs/laravel.log');
    if (!file_exists($logFile)) {
        return response()->json(['error' => 'Log file not found']);
    }

    // Get last 50 lines of log file
    $lines = [];
    $handle = fopen($logFile, 'r');
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            if (strpos($line, 'RealisasiUangMuka Debug') !== false) {
                $lines[] = $line;
            }
        }
        fclose($handle);
    }

    // Return last 5 debug entries
    $recentLogs = array_slice($lines, -5);

    return response('<pre>' . implode("\n", $recentLogs) . '</pre>');
})->name('realisasi-uang-muka.debug-logs');

Route::post('/realisasi-uang-muka/debug-submit', function(\Illuminate\Http\Request $request) {
    $input = $request->all();

    $penerimaFields = [];
    $jumlahKaryawanFields = [];
    $supirFields = [];
    $jumlahSupirFields = [];
    $mobilFields = [];
    $jumlahMobilFields = [];

    foreach ($input as $key => $value) {
        if (strpos($key, 'penerima') === 0) {
            $penerimaFields[$key] = $value;
        } elseif (strpos($key, 'jumlah_karyawan') === 0) {
            $jumlahKaryawanFields[$key] = $value;
        } elseif (strpos($key, 'supir') === 0) {
            $supirFields[$key] = $value;
        } elseif (strpos($key, 'jumlah') === 0 && strpos($key, 'karyawan') === false && strpos($key, 'mobil') === false) {
            $jumlahSupirFields[$key] = $value;
        } elseif (strpos($key, 'mobil') === 0) {
            $mobilFields[$key] = $value;
        } elseif (strpos($key, 'jumlah_mobil') === 0) {
            $jumlahMobilFields[$key] = $value;
        }
    }

    $kegiatan = null;
    if (isset($input['kegiatan'])) {
        $kegiatan = \App\Models\MasterKegiatan::find($input['kegiatan']);
    }

    $activityAnalysis = [];
    if ($kegiatan) {
        $kegiatanNama = strtolower($kegiatan->nama_kegiatan);
        $isMobil = (stripos($kegiatanNama, 'kir') !== false && stripos($kegiatanNama, 'stnk') !== false);
        $isSupir = (stripos($kegiatanNama, 'ob') !== false && (stripos($kegiatanNama, 'muat') !== false || stripos($kegiatanNama, 'bongkar') !== false));
        $isPenerima = !$isMobil && !$isSupir;

        $activityAnalysis = [
            'kegiatan_id' => $kegiatan->id,
            'kegiatan_nama' => $kegiatan->nama_kegiatan,
            'is_mobil_based' => $isMobil,
            'is_supir_based' => $isSupir,
            'is_penerima_based' => $isPenerima,
            'expected_fields' => $isPenerima ? 'penerima[] + jumlah_karyawan[]' : ($isSupir ? 'supir[] + jumlah[]' : 'mobil[] + jumlah_mobil[]')
        ];
    }

    return response()->json([
        'status' => 'debug_success',
        'message' => 'Form submission reached successfully - Debug Route',
        'method' => $request->method(),
        'has_debug_mode' => $request->has('debug_mode'),
        'input_count' => count($request->all()),
        'activity_analysis' => $activityAnalysis,
        'field_analysis' => [
            'penerima_fields' => $penerimaFields,
            'jumlah_karyawan_fields' => $jumlahKaryawanFields,
            'supir_fields' => $supirFields,
            'jumlah_supir_fields' => $jumlahSupirFields,
            'mobil_fields' => $mobilFields,
            'jumlah_mobil_fields' => $jumlahMobilFields
        ],
        'validation_prediction' => [
            'for_penerima' => count($penerimaFields) > 0 && count($jumlahKaryawanFields) > 0,
            'for_supir' => count($supirFields) > 0 && count($jumlahSupirFields) > 0,
            'for_mobil' => count($mobilFields) > 0 && count($jumlahMobilFields) > 0
        ],
        'basic_fields' => [
            'kegiatan' => isset($input['kegiatan']) ? $input['kegiatan'] : 'missing',
            'nomor_pembayaran' => isset($input['nomor_pembayaran']) ? $input['nomor_pembayaran'] : 'missing',
            'tanggal_pembayaran' => isset($input['tanggal_pembayaran']) ? $input['tanggal_pembayaran'] : 'missing',
            'kas_bank' => isset($input['kas_bank']) ? $input['kas_bank'] : 'missing',
            'jenis_transaksi' => isset($input['jenis_transaksi']) ? $input['jenis_transaksi'] : 'missing'
        ]
    ]);
})->name('realisasi-uang-muka.debug-submit');

// Report Routes
Route::middleware(['auth'])->prefix('report')->name('report.')->group(function () {
    // Report Tagihan
    Route::get('/tagihan', [App\Http\Controllers\ReportTagihanController::class, 'index'])->name('tagihan.index');
    Route::get('/tagihan/export', [App\Http\Controllers\ReportTagihanController::class, 'export'])->name('tagihan.export');

    // Report Pranota (to be implemented)
    // Route::get('/pranota', [App\Http\Controllers\ReportPranotaController::class, 'index'])->name('pranota.index');

    // Report Pembayaran
    Route::get('/pembayaran', [App\Http\Controllers\ReportPembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/export', [App\Http\Controllers\ReportPembayaranController::class, 'export'])->name('pembayaran.export');
    Route::get('/pembayaran/print', [App\Http\Controllers\ReportPembayaranController::class, 'print'])->name('pembayaran.print');
});

// ═══════════════════════════════════════════════════════════════════════
// 📊 AUDIT LOG ROUTES - Universal audit trail system
// ═══════════════════════════════════════════════════════════════════════

Route::middleware(['auth', \App\Http\Middleware\EnsureKaryawanPresent::class, \App\Http\Middleware\EnsureUserApproved::class])->group(function () {
    // Audit Log routes
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/{id}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    Route::post('audit-logs/model', [AuditLogController::class, 'getModelAuditLogs'])->name('audit-logs.model');
    Route::get('audit-logs/export/csv', [AuditLogController::class, 'export'])->name('audit-logs.export');

    // Test route
    Route::get('audit-logs-test', function () {
        return view('audit-logs.test');
    })->name('audit-logs.test');

    // Simple audit logs route for debugging
    Route::get('audit-logs-simple', function () {
        return view('audit-logs.index', [
            'auditLogs' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 25),
            'modules' => collect([]),
            'actions' => collect([]),
            'users' => collect([])
        ]);
    })->name('audit-logs.simple');

    // Test audit modal
    Route::get('audit-test-simple', function () {
        return view('audit-test-simple');
    })->name('audit.test.simple');

    // Debug audit modal route
    Route::get('debug-audit-modal', function () {
        return view('debug-audit-modal');
    })->name('debug.audit.modal');

    // Simple AJAX test route
    Route::get('simple-ajax-test', function () {
        return view('simple-ajax-test');
    })->name('simple.ajax.test');

    // Auth check route
    Route::get('auth-check', function () {
        return view('auth-check');
    })->name('auth.check');

    // Debug NIK route
    Route::get('debug-nik', function () {
        return view('debug-nik');
    })->name('debug.nik');

    // 📊 Prospek Management - Read Only
    Route::get('prospek', [ProspekController::class, 'index'])->name('prospek.index')
         ->middleware('can:prospek-view');

    Route::get('prospek/pilih-tujuan', [ProspekController::class, 'pilihTujuan'])->name('prospek.pilih-tujuan')
         ->middleware('can:prospek-edit');

    Route::get('prospek/proses-naik-kapal', [ProspekController::class, 'prosesNaikKapal'])->name('prospek.proses-naik-kapal')
         ->middleware('can:prospek-edit');

    Route::post('prospek/proses-naik-kapal', [ProspekController::class, 'prosesNaikKapal'])->name('prospek.proses-naik-kapal-batch')
         ->middleware('can:prospek-edit');

    Route::post('prospek/execute-naik-kapal', [ProspekController::class, 'executeNaikKapal'])->name('prospek.execute-naik-kapal')
         ->middleware('can:prospek-edit');

    Route::get('prospek/get-voyage-by-kapal', [ProspekController::class, 'getVoyageByKapal'])->name('prospek.get-voyage-by-kapal')
         ->middleware('can:prospek-view');

    // NOTE: Route dengan parameter harus di bawah route spesifik
    Route::get('prospek/{prospek}', [ProspekController::class, 'show'])->name('prospek.show')
                ->middleware('can:prospek-view');

    // Route untuk update seal (inline edit)
    Route::patch('prospek/{prospek}/update-seal', [ProspekController::class, 'updateSeal'])->name('prospek.update-seal')
         ->middleware('can:prospek-edit');

          // 🚢 Naik Kapal Management
          Route::get('naik-kapal/download-template', [NaikKapalController::class, 'downloadTemplate'])
               ->name('naik-kapal.download.template')
               ->middleware('can:prospek-view');
               
          Route::resource('naik-kapal', NaikKapalController::class)
                     ->middleware('can:prospek-edit');

          // BL (Bill of Lading) Management
          Route::get('bl/download-template', [\App\Http\Controllers\BlController::class, 'downloadTemplate'])
               ->name('bl.download.template')
               ->middleware('can:bl-view');
               
          Route::get('bl', [\App\Http\Controllers\BlController::class, 'select'])->name('bl.select')
               ->middleware('can:bl-view');
               
          Route::get('bl/index', [\App\Http\Controllers\BlController::class, 'index'])->name('bl.index')
               ->middleware('can:bl-view');

          Route::post('bl', [\App\Http\Controllers\BlController::class, 'store'])->name('bl.store')
               ->middleware('can:bl-create');
               
          Route::get('bl/{bl}', [\App\Http\Controllers\BlController::class, 'show'])->name('bl.show')
               ->middleware('can:bl-view');
               
          Route::patch('bl/{bl}/nomor-bl', [\App\Http\Controllers\BlController::class, 'updateNomorBl'])->name('bl.update-nomor-bl')
               ->middleware('can:bl-edit');
               
          // BL Bulk Operations
          Route::post('bl/validate-containers', [\App\Http\Controllers\BlController::class, 'validateContainers'])->name('bl.validate-containers')
               ->middleware('can:bl-edit');
               
          Route::post('bl/bulk-split', [\App\Http\Controllers\BlController::class, 'bulkSplit'])->name('bl.bulk-split')
               ->middleware('can:bl-edit');
               
          Route::get('bl-api/by-kapal-voyage', [\App\Http\Controllers\BlController::class, 'getByKapalVoyage'])->name('bl.api.by-kapal-voyage')
               ->middleware('can:bl-view');
});
