<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KontainerController;
use App\Http\Controllers\PermissionController;

use App\Http\Controllers\TujuanController;
use App\Http\Controllers\PermohonanController;
use App\Http\Controllers\MasterKegiatanController;
use App\Http\Controllers\PranotaSupirController;
use App\Http\Controllers\PembayaranPranotaSupirController;
use App\Http\Controllers\SupirDashboardController;
use App\Http\Controllers\CheckpointController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\PricelistSewaKontainerController;
use App\Http\Controllers\PranotaController;
use App\Http\Controllers\PembayaranPranotaKontainerController;

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
    Route::get('master/karyawan/create', [\App\Http\Controllers\KaryawanController::class, 'create'])
        ->name('master.karyawan.create')
        ->middleware(['auth', 'permission:master-karyawan.create']);

    Route::post('master/karyawan', [\App\Http\Controllers\KaryawanController::class, 'store'])
        ->name('master.karyawan.store')
        ->middleware(['auth', 'permission:master-karyawan.create']);

          // Tagihan Kontainer Sewa routes removed - controller/views refactored by request
          // The old routes and resource controller were deleted to allow a full rewrite.
          // If you want them restored later, use your backup or reintroduce new routes/controllers.

    // Arahkan root URL ke dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard utama untuk admin/staff
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Grup Rute Master Data ---
    Route::prefix('master')->name('master.')->group(function() {
          // Print all karyawan (print-friendly)
          Route::get('karyawan/print', [KaryawanController::class, 'print'])
               ->name('karyawan.print')
               ->middleware('permission:master-karyawan.print');

          // Print single karyawan (form layout)
          Route::get('karyawan/{karyawan}/print', [KaryawanController::class, 'printSingle'])
               ->name('karyawan.print.single')
               ->middleware('permission:master-karyawan.print');

               // Import karyawan from CSV (simple uploader)
               Route::get('karyawan/import', [KaryawanController::class, 'importForm'])
                    ->name('karyawan.import')
                    ->middleware(['auth', 'permission:master-karyawan.create']);

               Route::post('karyawan/import', [KaryawanController::class, 'importStore'])
                    ->name('karyawan.import.store')
                    ->middleware(['auth', 'permission:master-karyawan.create']);

               // Export/download CSV of all karyawan
               Route::get('karyawan/export', [KaryawanController::class, 'export'])
                    ->name('karyawan.export')
                    ->middleware('permission:master-karyawan.export');

               // Export Excel-formatted CSV to prevent scientific notation
               Route::get('karyawan/export-excel', [KaryawanController::class, 'exportExcel'])
                    ->name('karyawan.export-excel')
                    ->middleware('permission:master-karyawan.export');

               // Download CSV template for import
               Route::get('karyawan/template', [KaryawanController::class, 'downloadTemplate'])
                    ->name('karyawan.template');

               // Download Excel template for import
               Route::get('karyawan/excel-template', [KaryawanController::class, 'downloadExcelTemplate'])
                    ->name('karyawan.excel-template');

               // Download simple Excel template for import (headers only)
               Route::get('karyawan/simple-excel-template', [KaryawanController::class, 'downloadSimpleExcelTemplate'])
                    ->name('karyawan.simple-excel-template');

               // Crew checklist for ABK employees
        Route::get('karyawan/{karyawan}/crew-checklist', [KaryawanController::class, 'crewChecklist'])
            ->name('karyawan.crew-checklist');

            // NEW: Simplified crew checklist page
            Route::get('karyawan/{karyawan}/crew-checklist-new', [KaryawanController::class, 'crewChecklistNew'])
                ->name('karyawan.crew-checklist-new');

            Route::post('karyawan/{karyawan}/crew-checklist', [KaryawanController::class, 'updateCrewChecklist'])
                ->name('karyawan.crew-checklist.update');

            Route::get('karyawan/{karyawan}/crew-checklist/print', [KaryawanController::class, 'printCrewChecklist'])
                ->name('karyawan.crew-checklist.print');

         // Individual routes for karyawan with specific permissions (except index which is defined outside master group)
         Route::get('karyawan/create', [KaryawanController::class, 'create'])
              ->name('karyawan.create')
              ->middleware('permission:master-karyawan.create');
         Route::post('karyawan', [KaryawanController::class, 'store'])
              ->name('karyawan.store')
              ->middleware('permission:master-karyawan.create');
         Route::get('karyawan/{karyawan}', [KaryawanController::class, 'show'])
              ->name('karyawan.show')
              ->middleware('permission:master-karyawan.view');
         Route::get('karyawan/{karyawan}/edit', [KaryawanController::class, 'edit'])
              ->name('karyawan.edit')
              ->middleware('permission:master-karyawan.update');
         Route::put('karyawan/{karyawan}', [KaryawanController::class, 'update'])
              ->name('karyawan.update')
              ->middleware('permission:master-karyawan.update');
         Route::delete('karyawan/{karyawan}', [KaryawanController::class, 'destroy'])
              ->name('karyawan.destroy')
              ->middleware('permission:master-karyawan.delete');

        // Master user routes (with master prefix) - granular permissions
        Route::get('user', [UserController::class, 'index'])
             ->name('user.index')
             ->middleware('can:master-user.view');
        Route::get('user/create', [UserController::class, 'create'])
             ->name('user.create')
             ->middleware('can:master-user.create');
        Route::post('user', [UserController::class, 'store'])
             ->name('user.store')
             ->middleware('can:master-user.create');
        Route::get('user/{user}', [UserController::class, 'show'])
             ->name('user.show')
             ->middleware('can:master-user.view');
        Route::get('user/{user}/edit', [UserController::class, 'edit'])
             ->name('user.edit')
             ->middleware('can:master-user.update');
        Route::put('user/{user}', [UserController::class, 'update'])
             ->name('user.update')
             ->middleware('can:master-user.update');
        Route::delete('user/{user}', [UserController::class, 'destroy'])
             ->name('user.destroy')
             ->middleware('can:master-user.delete');

        // Additional master user routes for permission management
        Route::get('user/bulk-manage', [UserController::class, 'bulkManage'])
             ->name('user.bulk-manage')
             ->middleware('can:master-user.view');
        Route::post('user/{user}/assign-template', [UserController::class, 'assignTemplate'])
             ->name('user.assign-template')
             ->middleware('can:master-user.view');
        Route::post('user/bulk-assign-permissions', [UserController::class, 'bulkAssignPermissions'])
             ->name('user.bulk-assign-permissions')
             ->middleware('can:master-user.view');
        Route::get('user/{user}/permissions', [UserController::class, 'getUserPermissions'])
             ->name('user.permissions')
             ->middleware('can:master-user.view');

        // Master kontainer routes (with master prefix) - granular permissions
        Route::get('kontainer', [KontainerController::class, 'index'])
             ->name('kontainer.index')
             ->middleware('can:master-kontainer.view');
        Route::get('kontainer/create', [KontainerController::class, 'create'])
             ->name('kontainer.create')
             ->middleware('can:master-kontainer.create');
        Route::post('kontainer', [KontainerController::class, 'store'])
             ->name('kontainer.store')
             ->middleware('can:master-kontainer.create');
        Route::get('kontainer/{kontainer}', [KontainerController::class, 'show'])
             ->name('kontainer.show')
             ->middleware('can:master-kontainer.view');
        Route::get('kontainer/{kontainer}/edit', [KontainerController::class, 'edit'])
             ->name('kontainer.edit')
             ->middleware('can:master-kontainer.update');
        Route::put('kontainer/{kontainer}', [KontainerController::class, 'update'])
             ->name('kontainer.update')
             ->middleware('can:master-kontainer.update');
        Route::delete('kontainer/{kontainer}', [KontainerController::class, 'destroy'])
             ->name('kontainer.destroy')
             ->middleware('can:master-kontainer.delete');

        // Master tujuan routes (with master prefix) - granular permissions
        Route::get('tujuan', [TujuanController::class, 'index'])
             ->name('tujuan.index')
             ->middleware('can:master-tujuan.view');
        Route::get('tujuan/create', [TujuanController::class, 'create'])
             ->name('tujuan.create')
             ->middleware('can:master-tujuan.create');
        Route::post('tujuan', [TujuanController::class, 'store'])
             ->name('tujuan.store')
             ->middleware('can:master-tujuan.create');
        Route::get('tujuan/{tujuan}', [TujuanController::class, 'show'])
             ->name('tujuan.show')
             ->middleware('can:master-tujuan.view');
        Route::get('tujuan/{tujuan}/edit', [TujuanController::class, 'edit'])
             ->name('tujuan.edit')
             ->middleware('can:master-tujuan.update');
        Route::put('tujuan/{tujuan}', [TujuanController::class, 'update'])
             ->name('tujuan.update')
             ->middleware('can:master-tujuan.update');
        Route::delete('tujuan/{tujuan}', [TujuanController::class, 'destroy'])
             ->name('tujuan.destroy')
             ->middleware('can:master-tujuan.delete');

        // Master kegiatan routes (with master prefix) - granular permissions
        Route::get('kegiatan', [MasterKegiatanController::class, 'index'])
             ->name('kegiatan.index')
             ->middleware('can:master-kegiatan.view');
        Route::get('kegiatan/create', [MasterKegiatanController::class, 'create'])
             ->name('kegiatan.create')
             ->middleware('can:master-kegiatan.create');
        Route::post('kegiatan', [MasterKegiatanController::class, 'store'])
             ->name('kegiatan.store')
             ->middleware('can:master-kegiatan.create');
        Route::get('kegiatan/{kegiatan}', [MasterKegiatanController::class, 'show'])
             ->name('kegiatan.show')
             ->middleware('can:master-kegiatan.view');
        Route::get('kegiatan/{kegiatan}/edit', [MasterKegiatanController::class, 'edit'])
             ->name('kegiatan.edit')
             ->middleware('can:master-kegiatan.update');
        Route::put('kegiatan/{kegiatan}', [MasterKegiatanController::class, 'update'])
             ->name('kegiatan.update')
             ->middleware('can:master-kegiatan.update');
        Route::delete('kegiatan/{kegiatan}', [MasterKegiatanController::class, 'destroy'])
             ->name('kegiatan.destroy')
             ->middleware('can:master-kegiatan.delete');
        // CSV import/export helpers for Master Kegiatan
        Route::get('kegiatan/template/csv', [MasterKegiatanController::class, 'downloadTemplate'])
             ->name('kegiatan.template')
             ->middleware('can:master-kegiatan.view');

        Route::post('kegiatan/import/csv', [MasterKegiatanController::class, 'importCsv'])
             ->name('kegiatan.import')
             ->middleware('can:master-kegiatan.view');

        // Master permission routes (with master prefix) - granular permissions
        Route::get('permission', [PermissionController::class, 'index'])
             ->name('permission.index')
             ->middleware('can:master-permission.view');
        Route::get('permission/create', [PermissionController::class, 'create'])
             ->name('permission.create')
             ->middleware('can:master-permission.create');
        Route::post('permission', [PermissionController::class, 'store'])
             ->name('permission.store')
             ->middleware('can:master-permission.create');
        Route::get('permission/{permission}', [PermissionController::class, 'show'])
             ->name('permission.show')
             ->middleware('can:master-permission.view');
        Route::get('permission/{permission}/edit', [PermissionController::class, 'edit'])
             ->name('permission.edit')
             ->middleware('can:master-permission.update');
        Route::put('permission/{permission}', [PermissionController::class, 'update'])
             ->name('permission.update')
             ->middleware('can:master-permission.update');
        Route::delete('permission/{permission}', [PermissionController::class, 'destroy'])
             ->name('permission.destroy')
             ->middleware('can:master-permission.delete');

        // Additional permission routes
        Route::post('permission/sync', [PermissionController::class, 'sync'])
             ->name('permission.sync')
             ->middleware('can:master-permission.view');

        Route::post('permission/bulk-delete', [PermissionController::class, 'bulkDelete'])
             ->name('permission.bulk-delete')
             ->middleware('can:master-permission.view');

        Route::post('permission/{permission}/assign-users', [PermissionController::class, 'assignUsers'])
             ->name('permission.assign-users')
             ->middleware('can:master-permission.view');

        Route::get('permission/{permission}/users', [PermissionController::class, 'getUsers'])
             ->name('permission.users')
             ->middleware('can:master-permission.view');

        // Master mobil routes (with master prefix) - granular permissions
        Route::get('mobil', [MobilController::class, 'index'])
             ->name('mobil.index')
             ->middleware('can:master-mobil.view');
        Route::get('mobil/create', [MobilController::class, 'create'])
             ->name('mobil.create')
             ->middleware('can:master-mobil.create');
        Route::post('mobil', [MobilController::class, 'store'])
             ->name('mobil.store')
             ->middleware('can:master-mobil.create');
        Route::get('mobil/{mobil}', [MobilController::class, 'show'])
             ->name('mobil.show')
             ->middleware('can:master-mobil.view');
        Route::get('mobil/{mobil}/edit', [MobilController::class, 'edit'])
             ->name('mobil.edit')
             ->middleware('can:master-mobil.update');
        Route::put('mobil/{mobil}', [MobilController::class, 'update'])
             ->name('mobil.update')
             ->middleware('can:master-mobil.update');
        Route::delete('mobil/{mobil}', [MobilController::class, 'destroy'])
             ->name('mobil.destroy')
             ->middleware('can:master-mobil.delete');

     // Route kontainer-sewa dipindahkan ke luar prefix master

        // Master pricelist sewa kontainer routes (with master prefix) - granular permissions
        Route::get('pricelist-sewa-kontainer', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'index'])
             ->name('pricelist-sewa-kontainer.index')
             ->middleware('can:master-pricelist-sewa-kontainer.view');
        Route::get('pricelist-sewa-kontainer/create', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'create'])
             ->name('pricelist-sewa-kontainer.create')
             ->middleware('can:master-pricelist-sewa-kontainer.create');
        Route::post('pricelist-sewa-kontainer', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'store'])
             ->name('pricelist-sewa-kontainer.store')
             ->middleware('can:master-pricelist-sewa-kontainer.create');
        Route::get('pricelist-sewa-kontainer/{pricelist_sewa_kontainer}', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'show'])
             ->name('pricelist-sewa-kontainer.show')
             ->middleware('can:master-pricelist-sewa-kontainer.view');
        Route::get('pricelist-sewa-kontainer/{pricelist_sewa_kontainer}/edit', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'edit'])
             ->name('pricelist-sewa-kontainer.edit')
             ->middleware('can:master-pricelist-sewa-kontainer.update');
        Route::put('pricelist-sewa-kontainer/{pricelist_sewa_kontainer}', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'update'])
             ->name('pricelist-sewa-kontainer.update')
             ->middleware('can:master-pricelist-sewa-kontainer.update');
        Route::delete('pricelist-sewa-kontainer/{pricelist_sewa_kontainer}', [\App\Http\Controllers\MasterPricelistSewaKontainerController::class, 'destroy'])
             ->name('pricelist-sewa-kontainer.destroy')
             ->middleware('can:master-pricelist-sewa-kontainer.delete');
    });

    // Route master.karyawan.index di luar group master untuk konsistensi dengan view
    Route::get('master/karyawan', [KaryawanController::class, 'index'])
         ->name('master.karyawan.index')
         ->middleware('permission:master-karyawan.view');

    // --- Rute Permohonan ---
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
         ->middleware('can:permohonan');

    // Bulk delete permohonan (declare before resource routes)
    Route::delete('permohonan/bulk-delete', [PermohonanController::class, 'bulkDelete'])
         ->name('permohonan.bulk-delete')
         ->middleware('can:permohonan');

    // Individual routes for permohonan with specific permissions
    Route::get('permohonan', [PermohonanController::class, 'index'])
         ->name('permohonan.index')
         ->middleware('can:permohonan.index');
    Route::get('permohonan/create', [PermohonanController::class, 'create'])
         ->name('permohonan.create')
         ->middleware('can:permohonan.create');
    Route::post('permohonan', [PermohonanController::class, 'store'])
         ->name('permohonan.store')
         ->middleware('can:permohonan.create');
    Route::get('permohonan/{permohonan}', [PermohonanController::class, 'show'])
         ->name('permohonan.show')
         ->middleware('can:permohonan.index');
    Route::get('permohonan/{permohonan}/edit', [PermohonanController::class, 'edit'])
         ->name('permohonan.edit')
         ->middleware('can:permohonan.edit');
    Route::put('permohonan/{permohonan}', [PermohonanController::class, 'update'])
         ->name('permohonan.update')
         ->middleware('can:permohonan.edit');
    Route::delete('permohonan/{permohonan}', [PermohonanController::class, 'destroy'])
         ->name('permohonan.destroy')
         ->middleware('can:permohonan.delete');

     // --- Rute Pranota Supir ---
    Route::get('/pranota-supir', [PranotaSupirController::class, 'index'])->name('pranota-supir.index')->middleware('permission:pranota-supir-view');
    Route::get('/pranota-supir/create', [PranotaSupirController::class, 'create'])->name('pranota-supir.create')->middleware('permission:pranota-supir-create');
     // Explicit per-pranota print route must be declared before the parameterized show route
     Route::get('/pranota-supir/{pranotaSupir}/print', [PranotaSupirController::class, 'print'])->name('pranota-supir.print')->middleware('permission:pranota-supir-print');

     Route::get('/pranota-supir/{pranotaSupir}', [PranotaSupirController::class, 'show'])->name('pranota-supir.show')->middleware('permission:pranota-supir-view');
    Route::post('/pranota-supir', [PranotaSupirController::class, 'store'])->name('pranota-supir.store')->middleware('permission:pranota-supir-create');

          // --- Rute Pranota & Pembayaran Pranota Tagihan Kontainer ---
                    // Tagihan Kontainer Sewa feature removed - routes deleted to allow clean rebuild

    // --- Rute Pembayaran Pranota Supir ---
    Route::prefix('pembayaran-pranota-supir')->name('pembayaran-pranota-supir.')->group(function() {
     Route::get('/', [PembayaranPranotaSupirController::class, 'index'])->name('index')->middleware('permission:pembayaran-pranota-supir-view');
     // Per-pembayaran print
     Route::get('/{pembayaran}/print', [PembayaranPranotaSupirController::class, 'print'])->name('print')->middleware('permission:pembayaran-pranota-supir-print');
     Route::get('/buat', [PembayaranPranotaSupirController::class, 'create'])->name('create')->middleware('permission:pembayaran-pranota-supir-create'); // Menampilkan form konfirmasi
     Route::post('/simpan', [PembayaranPranotaSupirController::class, 'store'])->name('store')->middleware('permission:pembayaran-pranota-supir-create'); // Menyimpan pembayaran
    });

    // --- Rute Khusus untuk Supir ---
    Route::prefix('supir')->name('supir.')->group(function () {
        Route::get('/dashboard', [SupirDashboardController::class, 'index'])->name('dashboard');
        Route::get('/permohonan/{permohonan}/checkpoint', [CheckpointController::class, 'create'])->name('checkpoint.create');
        Route::post('/permohonan/{permohonan}/checkpoint', [CheckpointController::class, 'store'])->name('checkpoint.store');
    });

    // --- Rute Penyelesaian Tugas ---
    // Menggunakan PenyelesaianController yang sudah kita kembangkan
     Route::prefix('approval')->name('approval.')->middleware('can:permohonan')->group(function () {
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

               // Minimal CRUD routes for new simplified Tagihan Kontainer Sewa (daftar)
               // Controller should be implemented as resourceful controller: index, create, store, show, edit, update, destroy
               // Download CSV template for daftar tagihan
               Route::get('daftar-tagihan-kontainer-sewa/template/csv', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'downloadTemplateCsv'])
                    ->name('daftar-tagihan-kontainer-sewa.template.csv')
                    ->middleware('can:tagihan-kontainer-view');

               // Import CSV upload endpoint
               Route::post('daftar-tagihan-kontainer-sewa/import', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'importCsv'])
                    ->name('daftar-tagihan-kontainer-sewa.import')
                    ->middleware('can:tagihan-kontainer-create');

               // Import CSV with automatic grouping
               Route::post('daftar-tagihan-kontainer-sewa/import-grouped', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'importWithGrouping'])
                    ->name('daftar-tagihan-kontainer-sewa.import.grouped')
                    ->middleware('can:tagihan-kontainer-create');

               // Update adjustment endpoint
               Route::patch('daftar-tagihan-kontainer-sewa/{id}/adjustment', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'updateAdjustment'])
                    ->name('daftar-tagihan-kontainer-sewa.adjustment.update')
                    ->middleware('can:tagihan-kontainer-update');

               // Individual routes with specific middleware instead of resource
               Route::get('daftar-tagihan-kontainer-sewa', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'index'])
                    ->name('daftar-tagihan-kontainer-sewa.index')
                    ->middleware('can:tagihan-kontainer-view');

               Route::get('daftar-tagihan-kontainer-sewa/create', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'create'])
                    ->name('daftar-tagihan-kontainer-sewa.create')
                    ->middleware('can:tagihan-kontainer-create');

               Route::post('daftar-tagihan-kontainer-sewa', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'store'])
                    ->name('daftar-tagihan-kontainer-sewa.store')
                    ->middleware('can:tagihan-kontainer-create');

               Route::get('daftar-tagihan-kontainer-sewa/{tagihan}', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'show'])
                    ->name('daftar-tagihan-kontainer-sewa.show')
                    ->middleware('can:tagihan-kontainer-view');

               Route::get('daftar-tagihan-kontainer-sewa/{tagihan}/edit', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'edit'])
                    ->name('daftar-tagihan-kontainer-sewa.edit')
                    ->middleware('can:tagihan-kontainer-update');

               Route::put('daftar-tagihan-kontainer-sewa/{tagihan}', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'update'])
                    ->name('daftar-tagihan-kontainer-sewa.update')
                    ->middleware('can:tagihan-kontainer-update');

               Route::delete('daftar-tagihan-kontainer-sewa/{tagihan}', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'destroy'])
                    ->name('daftar-tagihan-kontainer-sewa.destroy')
                    ->middleware('can:tagihan-kontainer-delete');

               // Pranota routes
               Route::prefix('pranota')->name('pranota.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\PranotaController::class, 'index'])->name('index');
                    // Print route must be declared before the parameterized show route
                    Route::get('/{id}/print', [\App\Http\Controllers\PranotaController::class, 'print'])->name('print')
                         ->middleware('permission:pranota.print');
                    Route::get('/{id}', [\App\Http\Controllers\PranotaController::class, 'show'])->name('show');
                    Route::post('/', [\App\Http\Controllers\PranotaController::class, 'store'])->name('store');
                    Route::post('/bulk', [\App\Http\Controllers\PranotaController::class, 'bulkStore'])->name('bulk.store');
                    Route::patch('/{id}/status', [\App\Http\Controllers\PranotaController::class, 'updateStatus'])->name('update.status');
                    Route::delete('/{id}', [\App\Http\Controllers\PranotaController::class, 'destroy'])->name('destroy');
               });

               // Pembayaran Pranota Kontainer routes
               Route::prefix('pembayaran-pranota-kontainer')->name('pembayaran-pranota-kontainer.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\PembayaranPranotaKontainerController::class, 'index'])->name('index')
                         ->middleware('permission:pembayaran-pranota-kontainer.view');
                    Route::get('/create', [\App\Http\Controllers\PembayaranPranotaKontainerController::class, 'create'])->name('create')
                         ->middleware('permission:pembayaran-pranota-kontainer.create');
                    Route::post('/payment-form', [\App\Http\Controllers\PembayaranPranotaKontainerController::class, 'showPaymentForm'])->name('payment-form')
                         ->middleware('permission:pembayaran-pranota-kontainer.view');
                    Route::post('/', [\App\Http\Controllers\PembayaranPranotaKontainerController::class, 'store'])->name('store')
                         ->middleware('permission:pembayaran-pranota-kontainer.create');
                    Route::get('/{id}', [\App\Http\Controllers\PembayaranPranotaKontainerController::class, 'show'])->name('show')
                         ->middleware('permission:pembayaran-pranota-kontainer.view');
                    Route::get('/{id}/edit', [\App\Http\Controllers\PembayaranPranotaKontainerController::class, 'edit'])->name('edit')
                         ->middleware('permission:pembayaran-pranota-kontainer.update');
                    Route::put('/{id}', [\App\Http\Controllers\PembayaranPranotaKontainerController::class, 'update'])->name('update')
                         ->middleware('permission:pembayaran-pranota-kontainer.update');
                    Route::delete('/{id}', [\App\Http\Controllers\PembayaranPranotaKontainerController::class, 'destroy'])->name('destroy')
                         ->middleware('permission:pembayaran-pranota-kontainer.delete');
                    Route::delete('/{pembayaranId}/pranota/{pranotaId}', [\App\Http\Controllers\PembayaranPranotaKontainerController::class, 'removePranota'])->name('remove-pranota')
                         ->middleware('permission:pembayaran-pranota-kontainer.update');
                    Route::get('/{id}/print', [\App\Http\Controllers\PembayaranPranotaKontainerController::class, 'print'])->name('print')
                         ->middleware('permission:pembayaran-pranota-kontainer.print');
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

// Profile Management Routes (for all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/account', [\App\Http\Controllers\ProfileController::class, 'updateAccount'])->name('profile.update.account');
        Route::put('/personal', [\App\Http\Controllers\ProfileController::class, 'updatePersonal'])->name('profile.update.personal');
        Route::post('/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.update.avatar');
        Route::delete('/delete', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

// Perbaikan Kontainer Routes (Independent from Master)
Route::middleware(['auth'])->group(function() {
    // Perbaikan Kontainer routes - granular permissions
    Route::get('perbaikan-kontainer', [\App\Http\Controllers\PerbaikanKontainerController::class, 'index'])
         ->name('perbaikan-kontainer.index')
         ->middleware('can:perbaikan-kontainer.view');
    Route::get('perbaikan-kontainer/create', [\App\Http\Controllers\PerbaikanKontainerController::class, 'create'])
         ->name('perbaikan-kontainer.create')
         ->middleware('can:perbaikan-kontainer.create');
    Route::post('perbaikan-kontainer', [\App\Http\Controllers\PerbaikanKontainerController::class, 'store'])
         ->name('perbaikan-kontainer.store')
         ->middleware('can:perbaikan-kontainer.create');
    Route::get('perbaikan-kontainer/{perbaikanKontainer}', [\App\Http\Controllers\PerbaikanKontainerController::class, 'show'])
         ->name('perbaikan-kontainer.show')
         ->middleware('can:perbaikan-kontainer.view');
    Route::get('perbaikan-kontainer/{perbaikanKontainer}/edit', [\App\Http\Controllers\PerbaikanKontainerController::class, 'edit'])
         ->name('perbaikan-kontainer.edit')
         ->middleware('can:perbaikan-kontainer.update');
    Route::put('perbaikan-kontainer/{perbaikanKontainer}', [\App\Http\Controllers\PerbaikanKontainerController::class, 'update'])
         ->name('perbaikan-kontainer.update')
         ->middleware('can:perbaikan-kontainer.update');
    Route::delete('perbaikan-kontainer/{perbaikanKontainer}', [\App\Http\Controllers\PerbaikanKontainerController::class, 'destroy'])
         ->name('perbaikan-kontainer.destroy')
         ->middleware('can:perbaikan-kontainer.delete');

    // Additional perbaikan kontainer routes
    Route::patch('perbaikan-kontainer/{perbaikanKontainer}/status', [\App\Http\Controllers\PerbaikanKontainerController::class, 'updateStatus'])
         ->name('perbaikan-kontainer.update-status')
         ->middleware('can:perbaikan-kontainer.update');

    // Pranota Perbaikan Kontainer routes
    Route::get('pranota-perbaikan-kontainer', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'index'])
         ->name('pranota-perbaikan-kontainer.index')
         ->middleware('can:pranota-perbaikan-kontainer.view');
    Route::get('pranota-perbaikan-kontainer/create', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'create'])
         ->name('pranota-perbaikan-kontainer.create')
         ->middleware('can:pranota-perbaikan-kontainer.create');
    Route::post('pranota-perbaikan-kontainer', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'store'])
         ->name('pranota-perbaikan-kontainer.store')
         ->middleware('can:pranota-perbaikan-kontainer.create');
    Route::get('pranota-perbaikan-kontainer/{pranotaPerbaikanKontainer}', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'show'])
         ->name('pranota-perbaikan-kontainer.show')
         ->middleware('can:pranota-perbaikan-kontainer.view');
    Route::get('pranota-perbaikan-kontainer/{pranotaPerbaikanKontainer}/edit', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'edit'])
         ->name('pranota-perbaikan-kontainer.edit')
         ->middleware('can:pranota-perbaikan-kontainer.update');
    Route::put('pranota-perbaikan-kontainer/{pranotaPerbaikanKontainer}', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'update'])
         ->name('pranota-perbaikan-kontainer.update')
         ->middleware('can:pranota-perbaikan-kontainer.update');
    Route::delete('pranota-perbaikan-kontainer/{pranotaPerbaikanKontainer}', [\App\Http\Controllers\PranotaPerbaikanKontainerController::class, 'destroy'])
         ->name('pranota-perbaikan-kontainer.destroy')
         ->middleware('can:pranota-perbaikan-kontainer.delete');

    // Pembayaran Pranota Perbaikan Kontainer routes
    Route::get('pembayaran-pranota-perbaikan-kontainer', [\App\Http\Controllers\PembayaranPranotaPerbaikanKontainerController::class, 'index'])
         ->name('pembayaran-pranota-perbaikan-kontainer.index')
         ->middleware('can:pembayaran-pranota-perbaikan-kontainer.view');
    Route::get('pembayaran-pranota-perbaikan-kontainer/create', [\App\Http\Controllers\PembayaranPranotaPerbaikanKontainerController::class, 'create'])
         ->name('pembayaran-pranota-perbaikan-kontainer.create')
         ->middleware('can:pembayaran-pranota-perbaikan-kontainer.create');
    Route::post('pembayaran-pranota-perbaikan-kontainer', [\App\Http\Controllers\PembayaranPranotaPerbaikanKontainerController::class, 'store'])
         ->name('pembayaran-pranota-perbaikan-kontainer.store')
         ->middleware('can:pembayaran-pranota-perbaikan-kontainer.create');
    Route::get('pembayaran-pranota-perbaikan-kontainer/{pembayaranPranotaPerbaikanKontainer}', [\App\Http\Controllers\PembayaranPranotaPerbaikanKontainerController::class, 'show'])
         ->name('pembayaran-pranota-perbaikan-kontainer.show')
         ->middleware('can:pembayaran-pranota-perbaikan-kontainer.view');
    Route::get('pembayaran-pranota-perbaikan-kontainer/{pembayaranPranotaPerbaikanKontainer}/edit', [\App\Http\Controllers\PembayaranPranotaPerbaikanKontainerController::class, 'edit'])
         ->name('pembayaran-pranota-perbaikan-kontainer.edit')
         ->middleware('can:pembayaran-pranota-perbaikan-kontainer.update');
    Route::put('pembayaran-pranota-perbaikan-kontainer/{pembayaranPranotaPerbaikanKontainer}', [\App\Http\Controllers\PembayaranPranotaPerbaikanKontainerController::class, 'update'])
         ->name('pembayaran-pranota-perbaikan-kontainer.update')
         ->middleware('can:pembayaran-pranota-perbaikan-kontainer.update');
    Route::delete('pembayaran-pranota-perbaikan-kontainer/{pembayaranPranotaPerbaikanKontainer}', [\App\Http\Controllers\PembayaranPranotaPerbaikanKontainerController::class, 'destroy'])
         ->name('pembayaran-pranota-perbaikan-kontainer.destroy')
         ->middleware('can:pembayaran-pranota-perbaikan-kontainer.delete');
});
