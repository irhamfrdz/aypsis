<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetDashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KontainerController;
use App\Http\Controllers\KontainerImportController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\PajakController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\PekerjaanController;
use App\Http\Controllers\MasterBankController;
use App\Http\Controllers\Master\KlasifikasiBiayaController;
use App\Http\Controllers\TandaTerimaLclController;

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
use App\Http\Controllers\PembayaranPranotaObController;
use App\Http\Controllers\PembayaranPranotaPerbaikanController;
use App\Http\Controllers\PembayaranPranotaPerbaikanKontainerController;
use App\Http\Controllers\PembayaranPranotaSuratJalanController;
use App\Http\Controllers\PembayaranPranotaUangJalanController;
use App\Http\Controllers\AktivitasLainnyaController;
use App\Http\Controllers\PembayaranAktivitasLainController;
use App\Http\Controllers\PembayaranAktivitasLainnyaController;
use App\Http\Controllers\PembayaranUangMukaController;
use App\Http\Controllers\PembayaranObController;
use App\Http\Controllers\RealisasiUangMukaController;
use App\Http\Controllers\VendorBengkelController;
use App\Http\Controllers\TipeAkunController;
use App\Http\Controllers\PengirimController;
use App\Http\Controllers\MasterPengirimPenerimaController;
use App\Http\Controllers\JenisBarangController;
use App\Http\Controllers\TipeBarangController;
use App\Http\Controllers\MerkBanController;
use App\Http\Controllers\NamaStockBanController;
use App\Http\Controllers\TipeStockBanController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\MasterTujuanKirimController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OutstandingController;
// use App\Http\Controllers\PranotaSuratJalanController; // Disabled - replaced with pranota uang jalan
use App\Http\Controllers\PranotaUangKenekController;
use App\Http\Controllers\PranotaUangRitController;
// use App\Http\Controllers\PranotaRitKenekController; // Removed - not used
use App\Http\Controllers\GateInController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProspekController;
use App\Http\Controllers\NaikKapalController;
use App\Http\Controllers\OrderDataManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\SuratJalanBongkaranController;
use App\Http\Controllers\MasterPricelistObController;
use App\Http\Controllers\MasterPricelistAirTawarController;
use App\Http\Controllers\MasterPricelistKanisirBanController;
use App\Http\Controllers\MasterPelayananPelabuhanController;

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


// Route untuk memperbaiki data nama kapal KM SUMBER ABADI - DELETED
// Route::get('/fix-kapal-sumber-abadi', ...);

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

    // Dashboard asuransi asset
    Route::get('/dashboard/asset-insurance', [AssetDashboardController::class, 'index'])
         ->middleware(['auth'])
         ->name('dashboard.asset-insurance');

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
        // User Management - Separate routes to avoid middleware conflicts
        Route::get('user', [UserController::class, 'index'])
             ->name('user.index')
             ->middleware('can:master-user-view');
        
        Route::get('user/create', [UserController::class, 'create'])
             ->name('user.create')
             ->middleware('can:master-user-create');
        
        Route::post('user', [UserController::class, 'store'])
             ->name('user.store')
             ->middleware('can:master-user-create');
        
        Route::get('user/{user}', [UserController::class, 'show'])
             ->name('user.show')
             ->middleware('can:master-user-view');
        
        Route::get('user/{user}/edit', [UserController::class, 'edit'])
             ->name('user.edit')
             ->middleware('can:master-user-update');
        
        Route::put('user/{user}', [UserController::class, 'update'])
             ->name('user.update')
             ->middleware('can:master-user-update');
        
        Route::patch('user/{user}', [UserController::class, 'update'])
             ->name('user.update')
             ->middleware('can:master-user-update');
        
        Route::delete('user/{user}', [UserController::class, 'destroy'])
             ->name('user.destroy')
             ->middleware('can:master-user-delete');

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

        // Print multiple forms based on filters
        Route::get('karyawan/print-forms', [KaryawanController::class, 'printForms'])
             ->name('karyawan.print.forms')
             ->middleware('can:master-karyawan-print');

        // Print empty form
        Route::get('karyawan/print-empty', [KaryawanController::class, 'printEmpty'])
             ->name('karyawan.print.empty')
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

        // Export Empty Form Data (Form Karyawan Kosong)
        Route::get('karyawan/export-empty', [KaryawanController::class, 'exportEmpty'])
             ->name('karyawan.export-empty')
             ->middleware('can:master-karyawan-export');

        // Export Single Data Karyawan
        Route::get('karyawan/{karyawan}/export-single', [KaryawanController::class, 'exportSingle'])
              ->name('karyawan.export-single')
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
        Route::get('kontainer/download-template-nomor-gabungan', [KontainerImportController::class, 'downloadTemplateNomorGabungan'])
             ->name('kontainer.download-template-nomor-gabungan');
        Route::get('kontainer/download-template-gudang', [KontainerImportController::class, 'downloadTemplateGudang'])
             ->name('kontainer.download-template-gudang');
        Route::get('kontainer/download-template-tanggal-sewa', [KontainerImportController::class, 'downloadTemplateTanggalSewa'])
             ->name('kontainer.download-template-tanggal-sewa');
        Route::get('kontainer/export', [KontainerImportController::class, 'export'])
             ->name('kontainer.export')
             ->middleware('can:master-kontainer-view');
        Route::get('kontainer/export-tanpa-tanggal-sewa', [KontainerImportController::class, 'exportKontainerTanpaTanggalSewa'])
             ->name('kontainer.export-tanpa-tanggal-sewa')
             ->middleware('can:master-kontainer-view');
        Route::post('kontainer/import', [KontainerImportController::class, 'import'])
             ->name('kontainer.import')
             ->middleware('can:master-kontainer-create');
        Route::post('kontainer/import-nomor-gabungan', [KontainerImportController::class, 'importNomorGabungan'])
             ->name('kontainer.import-nomor-gabungan')
             ->middleware('can:master-kontainer-create');
        Route::post('kontainer/update-gudang', [KontainerImportController::class, 'updateGudang'])
             ->name('kontainer.update-gudang')
             ->middleware('can:master-kontainer-update');
        Route::post('kontainer/import-tanggal-sewa', [KontainerImportController::class, 'importTanggalSewa'])
             ->name('kontainer.import-tanggal-sewa')
             ->middleware('can:master-kontainer-update');

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
        Route::post('tujuan-kegiatan-utama/sync-ongkos', [TujuanKegiatanUtamaController::class, 'syncOngkos'])
             ->name('tujuan-kegiatan-utama.sync-ongkos')
             ->middleware('can:master-tujuan-kirim-update');

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
        Route::get('mobil/get-next-kode', [MobilController::class, 'getNextKode'])
             ->name('mobil.get-next-kode')
             ->middleware('can:master-mobil-create');
        Route::get('mobil/template', [App\Http\Controllers\MasterMobilImportController::class, 'downloadTemplate'])
             ->name('mobil.template')
             ->middleware('can:master-mobil-view');
        Route::post('mobil/import', [App\Http\Controllers\MasterMobilImportController::class, 'import'])
             ->name('mobil.import')
             ->middleware('can:master-mobil-create');
        Route::get('mobil/export', [App\Http\Controllers\MasterMobilImportController::class, 'export'])
             ->name('mobil.export')
             ->middleware('can:master-mobil-view');
        Route::get('mobil/template-asuransi', [MobilController::class, 'downloadTemplateAsuransi'])
             ->name('mobil.template-asuransi')
             ->middleware('can:master-mobil-view');
        Route::post('mobil/import-asuransi', [MobilController::class, 'importAsuransi'])
             ->name('mobil.import-asuransi')
             ->middleware('can:master-mobil-update');

        // Resource route with individual middleware per action
        Route::resource('mobil', MobilController::class)
             ->middleware('can:master-mobil-view')
             ->only(['index', 'show']);
        Route::resource('mobil', MobilController::class)
             ->middleware('can:master-mobil-create')
             ->only(['create', 'store']);
        Route::resource('mobil', MobilController::class)
             ->middleware('can:master-mobil-update')
             ->only(['edit', 'update']);
        Route::resource('mobil', MobilController::class)
             ->middleware('can:master-mobil-delete')
             ->only(['destroy']);

        // Ongkos Truck routes
        Route::get('ongkos-truck', [\App\Http\Controllers\OngkosTruckController::class, 'index'])
             ->name('ongkos-truck.index')
             ->middleware('can:ongkos-truck-view');
        Route::get('ongkos-truck/show-data', [\App\Http\Controllers\OngkosTruckController::class, 'showData'])
             ->name('ongkos-truck.show-data')
             ->middleware('can:ongkos-truck-view');
        Route::get('ongkos-truck/export-excel', [\App\Http\Controllers\OngkosTruckController::class, 'exportExcel'])
             ->name('ongkos-truck.export-excel')
             ->middleware('can:ongkos-truck-view');
        Route::get('ongkos-truck/{id}', [\App\Http\Controllers\OngkosTruckController::class, 'show'])
             ->name('ongkos-truck.show')
             ->middleware('can:ongkos-truck-view');
        Route::get('ongkos-truck/{id}/edit', [\App\Http\Controllers\OngkosTruckController::class, 'edit'])
             ->name('ongkos-truck.edit')
             ->middleware('can:ongkos-truck-update');
        Route::put('ongkos-truck/{id}', [\App\Http\Controllers\OngkosTruckController::class, 'update'])
             ->name('ongkos-truck.update')
             ->middleware('can:ongkos-truck-update');
        Route::delete('ongkos-truck/{id}', [\App\Http\Controllers\OngkosTruckController::class, 'destroy'])
             ->name('ongkos-truck.destroy')
             ->middleware('can:ongkos-truck-delete');

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

        // Pricelist CAT Management - Separate routes to avoid middleware conflicts
        Route::get('pricelist-cat', [PricelistCatController::class, 'index'])
             ->name('pricelist-cat.index')
             ->middleware('can:master-pricelist-cat-view');
        
        Route::get('pricelist-cat/create', [PricelistCatController::class, 'create'])
             ->name('pricelist-cat.create')
             ->middleware('can:master-pricelist-cat-create');
        
        Route::post('pricelist-cat', [PricelistCatController::class, 'store'])
             ->name('pricelist-cat.store')
             ->middleware('can:master-pricelist-cat-create');
        
        Route::get('pricelist-cat/{pricelistCat}', [PricelistCatController::class, 'show'])
             ->name('pricelist-cat.show')
             ->middleware('can:master-pricelist-cat-view');
        
        Route::get('pricelist-cat/{pricelistCat}/edit', [PricelistCatController::class, 'edit'])
             ->name('pricelist-cat.edit')
             ->middleware('can:master-pricelist-cat-update');
        
        Route::put('pricelist-cat/{pricelistCat}', [PricelistCatController::class, 'update'])
             ->name('pricelist-cat.update')
             ->middleware('can:master-pricelist-cat-update');
        
        Route::patch('pricelist-cat/{pricelistCat}', [PricelistCatController::class, 'update'])
             ->name('pricelist-cat.update')
             ->middleware('can:master-pricelist-cat-update');
        
        Route::delete('pricelist-cat/{pricelistCat}', [PricelistCatController::class, 'destroy'])
             ->name('pricelist-cat.destroy')
             ->middleware('can:master-pricelist-cat-delete');

        // Pricelist Rit Import/Export routes
        Route::get('pricelist-rit/template', [App\Http\Controllers\MasterPricelistRitImportController::class, 'downloadTemplate'])
             ->name('pricelist-rit.template');
        Route::post('pricelist-rit/import', [App\Http\Controllers\MasterPricelistRitImportController::class, 'import'])
             ->name('pricelist-rit.import')
             ->middleware('can:master-pricelist-rit-create');

        // Pricelist Rit Management - Separate routes to avoid middleware conflicts
        Route::get('pricelist-rit', [\App\Http\Controllers\PricelistRitController::class, 'index'])
             ->name('pricelist-rit.index')
             ->middleware('can:master-pricelist-rit-view');
        
        Route::get('pricelist-rit/create', [\App\Http\Controllers\PricelistRitController::class, 'create'])
             ->name('pricelist-rit.create')
             ->middleware('can:master-pricelist-rit-create');
        
        Route::post('pricelist-rit', [\App\Http\Controllers\PricelistRitController::class, 'store'])
             ->name('pricelist-rit.store')
             ->middleware('can:master-pricelist-rit-create');
        
        Route::get('pricelist-rit/{pricelistRit}', [\App\Http\Controllers\PricelistRitController::class, 'show'])
             ->name('pricelist-rit.show')
             ->middleware('can:master-pricelist-rit-view');
        
        Route::get('pricelist-rit/{pricelistRit}/edit', [\App\Http\Controllers\PricelistRitController::class, 'edit'])
             ->name('pricelist-rit.edit')
             ->middleware('can:master-pricelist-rit-update');
        
        Route::put('pricelist-rit/{pricelistRit}', [\App\Http\Controllers\PricelistRitController::class, 'update'])
             ->name('pricelist-rit.update')
             ->middleware('can:master-pricelist-rit-update');
        
        Route::patch('pricelist-rit/{pricelistRit}', [\App\Http\Controllers\PricelistRitController::class, 'update'])
             ->name('pricelist-rit.update')
             ->middleware('can:master-pricelist-rit-update');
        
        Route::delete('pricelist-rit/{pricelistRit}', [\App\Http\Controllers\PricelistRitController::class, 'destroy'])
             ->name('pricelist-rit.destroy')
             ->middleware('can:master-pricelist-rit-delete');

        // Pricelist Buruh Import/Export routes (must be BEFORE resource routes)
        Route::get('pricelist-buruh/export', [\App\Http\Controllers\Master\PricelistBuruhController::class, 'export'])
             ->name('pricelist-buruh.export')
             ->middleware('can:master-pricelist-buruh-view');
        
        Route::get('pricelist-buruh/template', [\App\Http\Controllers\Master\PricelistBuruhController::class, 'downloadTemplate'])
             ->name('pricelist-buruh.template');
        
        Route::post('pricelist-buruh/import', [\App\Http\Controllers\Master\PricelistBuruhController::class, 'import'])
             ->name('pricelist-buruh.import')
             ->middleware('can:master-pricelist-buruh-create');

        // Pricelist Buruh Management - Separate routes to avoid middleware conflicts
        Route::get('pricelist-buruh', [\App\Http\Controllers\Master\PricelistBuruhController::class, 'index'])
             ->name('pricelist-buruh.index')
             ->middleware('can:master-pricelist-buruh-view');
        
        Route::get('pricelist-buruh/create', [\App\Http\Controllers\Master\PricelistBuruhController::class, 'create'])
             ->name('pricelist-buruh.create')
             ->middleware('can:master-pricelist-buruh-create');
        
        Route::post('pricelist-buruh', [\App\Http\Controllers\Master\PricelistBuruhController::class, 'store'])
             ->name('pricelist-buruh.store')
             ->middleware('can:master-pricelist-buruh-create');
        
        Route::get('pricelist-buruh/{pricelistBuruh}', [\App\Http\Controllers\Master\PricelistBuruhController::class, 'show'])
             ->name('pricelist-buruh.show')
             ->middleware('can:master-pricelist-buruh-view');
        
        Route::get('pricelist-buruh/{pricelistBuruh}/edit', [\App\Http\Controllers\Master\PricelistBuruhController::class, 'edit'])
             ->name('pricelist-buruh.edit')
             ->middleware('can:master-pricelist-buruh-update');
        
        Route::put('pricelist-buruh/{pricelistBuruh}', [\App\Http\Controllers\Master\PricelistBuruhController::class, 'update'])
             ->name('pricelist-buruh.update')
             ->middleware('can:master-pricelist-buruh-update');
        
        Route::patch('pricelist-buruh/{pricelistBuruh}', [\App\Http\Controllers\Master\PricelistBuruhController::class, 'update'])
             ->name('pricelist-buruh.update')
             ->middleware('can:master-pricelist-buruh-update');
        
        Route::delete('pricelist-buruh/{pricelistBuruh}', [\App\Http\Controllers\Master\PricelistBuruhController::class, 'destroy'])
             ->name('pricelist-buruh.destroy')
             ->middleware('can:master-pricelist-buruh-delete');

        // Pricelist TKBM Routes
        Route::get('pricelist-tkbm', [\App\Http\Controllers\PricelistTkbmController::class, 'index'])->name('pricelist-tkbm.index');
        Route::get('pricelist-tkbm/create', [\App\Http\Controllers\PricelistTkbmController::class, 'create'])->name('pricelist-tkbm.create');
        Route::post('pricelist-tkbm', [\App\Http\Controllers\PricelistTkbmController::class, 'store'])->name('pricelist-tkbm.store');
        Route::get('pricelist-tkbm/{id}/edit', [\App\Http\Controllers\PricelistTkbmController::class, 'edit'])->name('pricelist-tkbm.edit');
        Route::put('pricelist-tkbm/{id}', [\App\Http\Controllers\PricelistTkbmController::class, 'update'])->name('pricelist-tkbm.update');
        Route::delete('pricelist-tkbm/{id}', [\App\Http\Controllers\PricelistTkbmController::class, 'destroy'])->name('pricelist-tkbm.destroy');

        // Master pricelist biaya dokumen routes - granular permissions
        Route::get('pricelist-biaya-dokumen', [\App\Http\Controllers\PricelistBiayaDokumenController::class, 'index'])
             ->name('pricelist-biaya-dokumen.index')
             ->middleware('can:master-pricelist-biaya-dokumen-view');
        
        Route::get('pricelist-biaya-dokumen/create', [\App\Http\Controllers\PricelistBiayaDokumenController::class, 'create'])
             ->name('pricelist-biaya-dokumen.create')
             ->middleware('can:master-pricelist-biaya-dokumen-create');
        
        Route::post('pricelist-biaya-dokumen', [\App\Http\Controllers\PricelistBiayaDokumenController::class, 'store'])
             ->name('pricelist-biaya-dokumen.store')
             ->middleware('can:master-pricelist-biaya-dokumen-create');
        
        Route::get('pricelist-biaya-dokumen/{pricelistBiayaDokumen}', [\App\Http\Controllers\PricelistBiayaDokumenController::class, 'show'])
             ->name('pricelist-biaya-dokumen.show')
             ->middleware('can:master-pricelist-biaya-dokumen-view');
        
        Route::get('pricelist-biaya-dokumen/{pricelistBiayaDokumen}/edit', [\App\Http\Controllers\PricelistBiayaDokumenController::class, 'edit'])
             ->name('pricelist-biaya-dokumen.edit')
             ->middleware('can:master-pricelist-biaya-dokumen-edit');
        
        Route::put('pricelist-biaya-dokumen/{pricelistBiayaDokumen}', [\App\Http\Controllers\PricelistBiayaDokumenController::class, 'update'])
             ->name('pricelist-biaya-dokumen.update')
             ->middleware('can:master-pricelist-biaya-dokumen-edit');
        
        Route::patch('pricelist-biaya-dokumen/{pricelistBiayaDokumen}', [\App\Http\Controllers\PricelistBiayaDokumenController::class, 'update'])
             ->name('pricelist-biaya-dokumen.update')
             ->middleware('can:master-pricelist-biaya-dokumen-edit');
        
        Route::delete('pricelist-biaya-dokumen/{pricelistBiayaDokumen}', [\App\Http\Controllers\PricelistBiayaDokumenController::class, 'destroy'])
             ->name('pricelist-biaya-dokumen.destroy')
             ->middleware('can:master-pricelist-biaya-dokumen-delete');

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

        // Pricelist Gate In Management - Separate routes to avoid middleware conflicts
        Route::get('pricelist-gate-in', [\App\Http\Controllers\PricelistGateInController::class, 'index'])
             ->name('pricelist-gate-in.index')
             ->middleware('can:master-pricelist-gate-in-view');
        
        Route::get('pricelist-gate-in/create', [\App\Http\Controllers\PricelistGateInController::class, 'create'])
             ->name('pricelist-gate-in.create')
             ->middleware('can:master-pricelist-gate-in-create');
        
        Route::post('pricelist-gate-in', [\App\Http\Controllers\PricelistGateInController::class, 'store'])
             ->name('pricelist-gate-in.store')
             ->middleware('can:master-pricelist-gate-in-create');
        
        Route::get('pricelist-gate-in/{pricelistGateIn}', [\App\Http\Controllers\PricelistGateInController::class, 'show'])
             ->name('pricelist-gate-in.show')
             ->middleware('can:master-pricelist-gate-in-view');
        
        Route::get('pricelist-gate-in/{pricelistGateIn}/edit', [\App\Http\Controllers\PricelistGateInController::class, 'edit'])
             ->name('pricelist-gate-in.edit')
             ->middleware('can:master-pricelist-gate-in-update');
        
        Route::put('pricelist-gate-in/{pricelistGateIn}', [\App\Http\Controllers\PricelistGateInController::class, 'update'])
             ->name('pricelist-gate-in.update')
             ->middleware('can:master-pricelist-gate-in-update');
        
        Route::patch('pricelist-gate-in/{pricelistGateIn}', [\App\Http\Controllers\PricelistGateInController::class, 'update'])
             ->name('pricelist-gate-in.update')
             ->middleware('can:master-pricelist-gate-in-update');
        
        Route::delete('pricelist-gate-in/{pricelistGateIn}', [\App\Http\Controllers\PricelistGateInController::class, 'destroy'])
             ->name('pricelist-gate-in.destroy')
             ->middleware('can:master-pricelist-gate-in-delete');

        // Pricelist OB Management - Separate routes to avoid middleware conflicts
        Route::get('pricelist-ob', [MasterPricelistObController::class, 'index'])
             ->name('pricelist-ob.index')
             ->middleware('can:master-pricelist-ob-view');
        
        Route::get('pricelist-ob/create', [MasterPricelistObController::class, 'create'])
             ->name('pricelist-ob.create')
             ->middleware('can:master-pricelist-ob-create');
        
        Route::post('pricelist-ob', [MasterPricelistObController::class, 'store'])
             ->name('pricelist-ob.store')
             ->middleware('can:master-pricelist-ob-create');
        
        // Import/Export routes for pricelist OB (must come before parameterized routes)
        Route::get('pricelist-ob/export-template', [MasterPricelistObController::class, 'exportTemplate'])
             ->name('pricelist-ob.export-template')
             ->middleware('can:master-pricelist-ob-view');
        Route::post('pricelist-ob/import', [MasterPricelistObController::class, 'import'])
             ->name('pricelist-ob.import')
             ->middleware('can:master-pricelist-ob-create');

        Route::get('pricelist-ob/{pricelistOb}', [MasterPricelistObController::class, 'show'])
             ->name('pricelist-ob.show')
             ->middleware('can:master-pricelist-ob-view');
        
        Route::get('pricelist-ob/{pricelistOb}/edit', [MasterPricelistObController::class, 'edit'])
             ->name('pricelist-ob.edit')
             ->middleware('can:master-pricelist-ob-update');
        
        Route::put('pricelist-ob/{pricelistOb}', [MasterPricelistObController::class, 'update'])
             ->name('pricelist-ob.update')
             ->middleware('can:master-pricelist-ob-update');
        
        Route::patch('pricelist-ob/{pricelistOb}', [MasterPricelistObController::class, 'update'])
             ->name('pricelist-ob.update')
             ->middleware('can:master-pricelist-ob-update');
        
        Route::delete('pricelist-ob/{pricelistOb}', [MasterPricelistObController::class, 'destroy'])
             ->name('pricelist-ob.destroy')
             ->middleware('can:master-pricelist-ob-delete');

        // Pricelist Air Tawar Management - Separate routes to avoid middleware conflicts
        Route::get('pricelist-air-tawar', [MasterPricelistAirTawarController::class, 'index'])
             ->name('pricelist-air-tawar.index')
             ->middleware('can:master-pricelist-air-tawar-view');
        
        Route::get('pricelist-air-tawar/create', [MasterPricelistAirTawarController::class, 'create'])
             ->name('pricelist-air-tawar.create')
             ->middleware('can:master-pricelist-air-tawar-create');
        
        Route::post('pricelist-air-tawar', [MasterPricelistAirTawarController::class, 'store'])
             ->name('pricelist-air-tawar.store')
             ->middleware('can:master-pricelist-air-tawar-create');
        
        // Import/Export routes for pricelist air tawar (must come before parameterized routes)
        Route::get('pricelist-air-tawar/export-template', [MasterPricelistAirTawarController::class, 'exportTemplate'])
             ->name('pricelist-air-tawar.export-template')
             ->middleware('can:master-pricelist-air-tawar-view');
        Route::post('pricelist-air-tawar/import', [MasterPricelistAirTawarController::class, 'import'])
             ->name('pricelist-air-tawar.import')
             ->middleware('can:master-pricelist-air-tawar-create');

        Route::get('pricelist-air-tawar/{pricelistAirTawar}', [MasterPricelistAirTawarController::class, 'show'])
             ->name('pricelist-air-tawar.show')
             ->middleware('can:master-pricelist-air-tawar-view');
        
        Route::get('pricelist-air-tawar/{pricelistAirTawar}/edit', [MasterPricelistAirTawarController::class, 'edit'])
             ->name('pricelist-air-tawar.edit')
             ->middleware('can:master-pricelist-air-tawar-update');
        
        Route::put('pricelist-air-tawar/{pricelistAirTawar}', [MasterPricelistAirTawarController::class, 'update'])
             ->name('pricelist-air-tawar.update')
             ->middleware('can:master-pricelist-air-tawar-update');
        
        Route::patch('pricelist-air-tawar/{pricelistAirTawar}', [MasterPricelistAirTawarController::class, 'update'])
             ->name('pricelist-air-tawar.update')
             ->middleware('can:master-pricelist-air-tawar-update');
        
        Route::delete('pricelist-air-tawar/{pricelistAirTawar}', [MasterPricelistAirTawarController::class, 'destroy'])
             ->name('pricelist-air-tawar.destroy')
             ->middleware('can:master-pricelist-air-tawar-delete');

        // Pricelist Kanisir Ban Management
        Route::get('pricelist-kanisir-ban', [MasterPricelistKanisirBanController::class, 'index'])
             ->name('pricelist-kanisir-ban.index')
             ->middleware('can:master-pricelist-kanisir-ban-view');
        
        Route::get('pricelist-kanisir-ban/create', [MasterPricelistKanisirBanController::class, 'create'])
             ->name('pricelist-kanisir-ban.create')
             ->middleware('can:master-pricelist-kanisir-ban-create');
        
        Route::post('pricelist-kanisir-ban', [MasterPricelistKanisirBanController::class, 'store'])
             ->name('pricelist-kanisir-ban.store')
             ->middleware('can:master-pricelist-kanisir-ban-create');
        
        Route::get('pricelist-kanisir-ban/{id}/edit', [MasterPricelistKanisirBanController::class, 'edit'])
             ->name('pricelist-kanisir-ban.edit')
             ->middleware('can:master-pricelist-kanisir-ban-update');
        
        Route::put('pricelist-kanisir-ban/{id}', [MasterPricelistKanisirBanController::class, 'update'])
             ->name('pricelist-kanisir-ban.update')
             ->middleware('can:master-pricelist-kanisir-ban-update');
        
        Route::delete('pricelist-kanisir-ban/{id}', [MasterPricelistKanisirBanController::class, 'destroy'])
             ->name('pricelist-kanisir-ban.destroy')
             ->middleware('can:master-pricelist-kanisir-ban-delete');

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

        // ═══════════════════════════════════════════════════════════════════════
        // 🏭 MASTER PELAYANAN PELABUHAN MANAGEMENT
        // ═══════════════════════════════════════════════════════════════════════
        Route::get('master-pelayanan-pelabuhan', [MasterPelayananPelabuhanController::class, 'index'])
             ->name('master-pelayanan-pelabuhan.index')
             ->middleware('can:master-pelayanan-pelabuhan-view');

        Route::get('master-pelayanan-pelabuhan/create', [MasterPelayananPelabuhanController::class, 'create'])
             ->name('master-pelayanan-pelabuhan.create')
             ->middleware('can:master-pelayanan-pelabuhan-create');

        Route::post('master-pelayanan-pelabuhan', [MasterPelayananPelabuhanController::class, 'store'])
             ->name('master-pelayanan-pelabuhan.store')
             ->middleware('can:master-pelayanan-pelabuhan-create');

        Route::get('master-pelayanan-pelabuhan/{masterPelayananPelabuhan}/edit', [MasterPelayananPelabuhanController::class, 'edit'])
             ->name('master-pelayanan-pelabuhan.edit')
             ->middleware('can:master-pelayanan-pelabuhan-edit');

        Route::put('master-pelayanan-pelabuhan/{masterPelayananPelabuhan}', [MasterPelayananPelabuhanController::class, 'update'])
             ->name('master-pelayanan-pelabuhan.update')
             ->middleware('can:master-pelayanan-pelabuhan-edit');

        Route::delete('master-pelayanan-pelabuhan/{masterPelayananPelabuhan}', [MasterPelayananPelabuhanController::class, 'destroy'])
             ->name('master-pelayanan-pelabuhan.destroy')
             ->middleware('can:master-pelayanan-pelabuhan-delete');

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
    // Master Divisi Management - Separate routes to avoid middleware conflicts
    Route::get('master/divisi', [DivisiController::class, 'index'])
         ->name('master.divisi.index')
         ->middleware('can:master-divisi-view');
    
    Route::get('master/divisi/create', [DivisiController::class, 'create'])
         ->name('master.divisi.create')
         ->middleware('can:master-divisi-create');
    
    Route::post('master/divisi', [DivisiController::class, 'store'])
         ->name('master.divisi.store')
         ->middleware('can:master-divisi-create');
    
    Route::get('master/divisi/{divisi}', [DivisiController::class, 'show'])
         ->name('master.divisi.show')
         ->middleware('can:master-divisi-view');
    
    Route::get('master/divisi/{divisi}/edit', [DivisiController::class, 'edit'])
         ->name('master.divisi.edit')
         ->middleware('can:master-divisi-update');
    
    Route::put('master/divisi/{divisi}', [DivisiController::class, 'update'])
         ->name('master.divisi.update')
         ->middleware('can:master-divisi-update');
    
    Route::patch('master/divisi/{divisi}', [DivisiController::class, 'update'])
         ->name('master.divisi.update')
         ->middleware('can:master-divisi-update');
    
    Route::delete('master/divisi/{divisi}', [DivisiController::class, 'destroy'])
         ->name('master.divisi.destroy')
         ->middleware('can:master-divisi-delete');
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

    // Master Pajak Management - Separate routes to avoid middleware conflicts
    Route::get('master/pajak', [PajakController::class, 'index'])
         ->name('master.pajak.index')
         ->middleware('can:master-pajak-view');
    
    Route::get('master/pajak/create', [PajakController::class, 'create'])
         ->name('master.pajak.create')
         ->middleware('can:master-pajak-create');
    
    Route::post('master/pajak', [PajakController::class, 'store'])
         ->name('master.pajak.store')
         ->middleware('can:master-pajak-create');
    
    Route::get('master/pajak/{pajak}', [PajakController::class, 'show'])
         ->name('master.pajak.show')
         ->middleware('can:master-pajak-view');
    
    Route::get('master/pajak/{pajak}/edit', [PajakController::class, 'edit'])
         ->name('master.pajak.edit')
         ->middleware('can:master-pajak-update');
    
    Route::put('master/pajak/{pajak}', [PajakController::class, 'update'])
         ->name('master.pajak.update')
         ->middleware('can:master-pajak-update');
    
    Route::patch('master/pajak/{pajak}', [PajakController::class, 'update'])
         ->name('master.pajak.update')
         ->middleware('can:master-pajak-update');
    
    Route::delete('master/pajak/{pajak}', [PajakController::class, 'destroy'])
         ->name('master.pajak.destroy')
         ->middleware('can:master-pajak-destroy');
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

    // Master Pekerjaan Management - Separate routes to avoid middleware conflicts
    Route::get('master/pekerjaan', [PekerjaanController::class, 'index'])
         ->name('master.pekerjaan.index')
         ->middleware('can:master-pekerjaan-view');
    
    Route::get('master/pekerjaan/create', [PekerjaanController::class, 'create'])
         ->name('master.pekerjaan.create')
         ->middleware('can:master-pekerjaan-create');
    
    Route::post('master/pekerjaan', [PekerjaanController::class, 'store'])
         ->name('master.pekerjaan.store')
         ->middleware('can:master-pekerjaan-create');
    
    Route::get('master/pekerjaan/{pekerjaan}', [PekerjaanController::class, 'show'])
         ->name('master.pekerjaan.show')
         ->middleware('can:master-pekerjaan-view');
    
    Route::get('master/pekerjaan/{pekerjaan}/edit', [PekerjaanController::class, 'edit'])
         ->name('master.pekerjaan.edit')
         ->middleware('can:master-pekerjaan-update');
    
    Route::put('master/pekerjaan/{pekerjaan}', [PekerjaanController::class, 'update'])
         ->name('master.pekerjaan.update')
         ->middleware('can:master-pekerjaan-update');
    
    Route::patch('master/pekerjaan/{pekerjaan}', [PekerjaanController::class, 'update'])
         ->name('master.pekerjaan.update')
         ->middleware('can:master-pekerjaan-update');
    
    Route::delete('master/pekerjaan/{pekerjaan}', [PekerjaanController::class, 'destroy'])
         ->name('master.pekerjaan.destroy')
         ->middleware('can:master-pekerjaan-destroy');
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

    // Stock Kontainer Update Gudang routes
    Route::get('master/stock-kontainer/template-gudang', [\App\Http\Controllers\StockKontainerController::class, 'downloadTemplateGudang'])
         ->name('master.stock-kontainer.template-gudang');

    Route::post('master/stock-kontainer/update-gudang', [\App\Http\Controllers\StockKontainerController::class, 'updateGudang'])
         ->name('master.stock-kontainer.update-gudang')
         ->middleware('can:master-stock-kontainer-update');

    // 📊 Stock Kontainer (Container Stock) Management with permissions
    Route::get('master/stock-kontainer', [\App\Http\Controllers\StockKontainerController::class, 'index'])
         ->name('master.stock-kontainer.index')
         ->middleware('can:master-stock-kontainer-view');
    Route::get('master/stock-kontainer/create', [\App\Http\Controllers\StockKontainerController::class, 'create'])
         ->name('master.stock-kontainer.create')
         ->middleware('can:master-stock-kontainer-create');
    Route::post('master/stock-kontainer', [\App\Http\Controllers\StockKontainerController::class, 'store'])
         ->name('master.stock-kontainer.store')
         ->middleware('can:master-stock-kontainer-create');
    Route::get('master/stock-kontainer/{stock_kontainer}', [\App\Http\Controllers\StockKontainerController::class, 'show'])
         ->name('master.stock-kontainer.show')
         ->middleware('can:master-stock-kontainer-view');
    Route::get('master/stock-kontainer/{stock_kontainer}/edit', [\App\Http\Controllers\StockKontainerController::class, 'edit'])
         ->name('master.stock-kontainer.edit')
         ->middleware('can:master-stock-kontainer-update');
    Route::put('master/stock-kontainer/{stock_kontainer}', [\App\Http\Controllers\StockKontainerController::class, 'update'])
         ->name('master.stock-kontainer.update')
         ->middleware('can:master-stock-kontainer-update');
    Route::delete('master/stock-kontainer/{stock_kontainer}', [\App\Http\Controllers\StockKontainerController::class, 'destroy'])
         ->name('master.stock-kontainer.destroy')
         ->middleware('can:master-stock-kontainer-delete');

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

    // 💰 Biaya Kapal (Ship Costs) Management with permissions
    Route::get('biaya-kapal/get-next-invoice-number', [\App\Http\Controllers\BiayaKapalController::class, 'getNextInvoiceNumber'])
         ->name('biaya-kapal.get-next-invoice-number')
         ->middleware('can:biaya-kapal-create');
    Route::get('biaya-kapal/get-voyages/{namaKapal}', [\App\Http\Controllers\BiayaKapalController::class, 'getVoyagesByShip'])
         ->name('biaya-kapal.get-voyages');
    Route::post('biaya-kapal/get-bls-by-voyages', [\App\Http\Controllers\BiayaKapalController::class, 'getBlsByVoyages'])
         ->name('biaya-kapal.get-bls-by-voyages');
    Route::post('biaya-kapal/get-container-counts', [\App\Http\Controllers\BiayaKapalController::class, 'getContainerCounts'])
         ->name('biaya-kapal.get-container-counts');
    Route::get('biaya-kapal/{biayaKapal}/print', [\App\Http\Controllers\BiayaKapalController::class, 'print'])
         ->name('biaya-kapal.print')
         ->middleware('can:biaya-kapal-view');
    Route::get('biaya-kapal/{biayaKapal}/print-dokumen', [\App\Http\Controllers\BiayaKapalController::class, 'printDokumen'])
         ->name('biaya-kapal.print-dokumen')
         ->middleware('can:biaya-kapal-view');
    Route::get('biaya-kapal/{biayaKapal}/print-trucking', [\App\Http\Controllers\BiayaKapalController::class, 'printTrucking'])
         ->name('biaya-kapal.print-trucking')
         ->middleware('can:biaya-kapal-view');
    Route::get('biaya-kapal', [\App\Http\Controllers\BiayaKapalController::class, 'index'])
         ->name('biaya-kapal.index')
         ->middleware('can:biaya-kapal-view');
    Route::get('biaya-kapal/create', [\App\Http\Controllers\BiayaKapalController::class, 'create'])
         ->name('biaya-kapal.create')
         ->middleware('can:biaya-kapal-create');
    Route::post('biaya-kapal', [\App\Http\Controllers\BiayaKapalController::class, 'store'])
         ->name('biaya-kapal.store')
         ->middleware('can:biaya-kapal-create');
    Route::get('biaya-kapal/{biayaKapal}', [\App\Http\Controllers\BiayaKapalController::class, 'show'])
         ->name('biaya-kapal.show')
         ->middleware('can:biaya-kapal-view');
    Route::get('biaya-kapal/{biayaKapal}/edit', [\App\Http\Controllers\BiayaKapalController::class, 'edit'])
         ->name('biaya-kapal.edit')
         ->middleware('can:biaya-kapal-update');
    Route::put('biaya-kapal/{biayaKapal}', [\App\Http\Controllers\BiayaKapalController::class, 'update'])
         ->name('biaya-kapal.update')
         ->middleware('can:biaya-kapal-update');
    Route::delete('biaya-kapal/{biayaKapal}', [\App\Http\Controllers\BiayaKapalController::class, 'destroy'])
         ->name('biaya-kapal.destroy')
         ->middleware('can:biaya-kapal-delete');

    // 🏢 Master Gudang (Warehouse Master) Management with permissions
    // Import & Template routes (must be before resource routes)
    Route::get('master-gudang/template/download', [\App\Http\Controllers\MasterGudangController::class, 'template'])
         ->name('master-gudang.template')
         ->middleware('can:master-gudang-view');
    
    Route::post('master-gudang/import', [\App\Http\Controllers\MasterGudangController::class, 'import'])
         ->name('master-gudang.import')
         ->middleware('can:master-gudang-create');
    
    Route::get('master-gudang', [\App\Http\Controllers\MasterGudangController::class, 'index'])
         ->name('master-gudang.index')
         ->middleware('can:master-gudang-view');
    Route::get('master-gudang/create', [\App\Http\Controllers\MasterGudangController::class, 'create'])
         ->name('master-gudang.create')
         ->middleware('can:master-gudang-create');
    Route::post('master-gudang', [\App\Http\Controllers\MasterGudangController::class, 'store'])
         ->name('master-gudang.store')
         ->middleware('can:master-gudang-create');
    Route::get('master-gudang/{master_gudang}', [\App\Http\Controllers\MasterGudangController::class, 'show'])
         ->name('master-gudang.show')
         ->middleware('can:master-gudang-view');
    Route::get('master-gudang/{master_gudang}/edit', [\App\Http\Controllers\MasterGudangController::class, 'edit'])
         ->name('master-gudang.edit')
         ->middleware('can:master-gudang-edit');
    Route::put('master-gudang/{master_gudang}', [\App\Http\Controllers\MasterGudangController::class, 'update'])
         ->name('master-gudang.update')
         ->middleware('can:master-gudang-edit');
    Route::delete('master-gudang/{master_gudang}', [\App\Http\Controllers\MasterGudangController::class, 'destroy'])
         ->name('master-gudang.destroy')
         ->middleware('can:master-gudang-delete');

    // � Stock Ban (Tire Stock) Management with permissions
    Route::get('stock-ban-dalam/{id}/use', [\App\Http\Controllers\StockBanController::class, 'useBanDalam'])->name('stock-ban-dalam.use')->middleware('can:stock-ban-update');
    Route::post('stock-ban-dalam/{id}/use', [\App\Http\Controllers\StockBanController::class, 'storeUsageBanDalam'])->name('stock-ban-dalam.store-usage')->middleware('can:stock-ban-update');
    Route::get('stock-ban-dalam/{id}', [\App\Http\Controllers\StockBanController::class, 'showBanDalam'])->name('stock-ban-dalam.show')->middleware('can:stock-ban-view');
    Route::get('stock-ban', [\App\Http\Controllers\StockBanController::class, 'index'])
         ->name('stock-ban.index')
         ->middleware('can:stock-ban-view');
    Route::get('stock-ban/create', [\App\Http\Controllers\StockBanController::class, 'create'])
         ->name('stock-ban.create')
         ->middleware('can:stock-ban-create');
    Route::post('stock-ban', [\App\Http\Controllers\StockBanController::class, 'store'])
         ->name('stock-ban.store')
         ->middleware('can:stock-ban-create');
    Route::post('stock-ban/{id}/use', [\App\Http\Controllers\StockBanController::class, 'storeUsage'])
         ->name('stock-ban.store-usage')
         ->middleware('can:stock-ban-update');
    Route::get('stock-ban/{stock_ban}', [\App\Http\Controllers\StockBanController::class, 'show'])
         ->name('stock-ban.show')
         ->middleware('can:stock-ban-view');
    Route::get('stock-ban/{stock_ban}/edit', [\App\Http\Controllers\StockBanController::class, 'edit'])
         ->name('stock-ban.edit')
         ->middleware('can:stock-ban-update');
    Route::put('stock-ban/{stock_ban}', [\App\Http\Controllers\StockBanController::class, 'update'])
         ->name('stock-ban.update')
         ->middleware('can:stock-ban-update');
    Route::put('stock-ban/{stock_ban}/masak', [\App\Http\Controllers\StockBanController::class, 'masak'])
         ->name('stock-ban.masak')
         ->middleware('can:stock-ban-update');
    Route::delete('stock-ban/{stock_ban}', [\App\Http\Controllers\StockBanController::class, 'destroy'])
         ->name('stock-ban.destroy')
         ->middleware('can:stock-ban-delete');

    // �💰 Pricelist Uang Jalan Batam Management with permissions
    // Download Template & Import (must be before resource routes)
    Route::get('pricelist-uang-jalan-batam/download-template', [\App\Http\Controllers\PricelistUangJalanBatamController::class, 'downloadTemplate'])
         ->name('pricelist-uang-jalan-batam.download-template')
         ->middleware('can:master-pricelist-uang-jalan-batam-view');
    
    Route::post('pricelist-uang-jalan-batam/import', [\App\Http\Controllers\PricelistUangJalanBatamController::class, 'import'])
         ->name('pricelist-uang-jalan-batam.import')
         ->middleware('can:master-pricelist-uang-jalan-batam-create');

    Route::resource('pricelist-uang-jalan-batam', \App\Http\Controllers\PricelistUangJalanBatamController::class)
         ->names('pricelist-uang-jalan-batam')
         ->middleware([
             'index' => 'can:master-pricelist-uang-jalan-batam-view',
             'show' => 'can:master-pricelist-uang-jalan-batam-view',
             'create' => 'can:master-pricelist-uang-jalan-batam-create',
             'store' => 'can:master-pricelist-uang-jalan-batam-create',
             'edit' => 'can:master-pricelist-uang-jalan-batam-edit',
             'update' => 'can:master-pricelist-uang-jalan-batam-edit',
             'destroy' => 'can:master-pricelist-uang-jalan-batam-delete'
         ]);

    // ⛽ Kelola BBM Management with permissions
    Route::resource('kelola-bbm', \App\Http\Controllers\KelolaBbmController::class)
         ->names('kelola-bbm')
         ->middleware([
             'index' => 'can:master-kelola-bbm-view',
             'show' => 'can:master-kelola-bbm-view',
             'create' => 'can:master-kelola-bbm-create',
             'store' => 'can:master-kelola-bbm-create',
             'edit' => 'can:master-kelola-bbm-edit',
             'update' => 'can:master-kelola-bbm-edit',
             'destroy' => 'can:master-kelola-bbm-delete'
         ]);

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

    // 📊 Pengirim - Export to Excel
    Route::get('master/pengirim-export-excel', [PengirimController::class, 'exportExcel'])
         ->name('pengirim.export-excel')
         ->middleware('can:master-pengirim-view');

    // Master Pengirim/Penerima - Download Template & Import (HARUS SEBELUM RESOURCE!)
    Route::get('master-pengirim-penerima/download-template', [MasterPengirimPenerimaController::class, 'downloadTemplate'])
         ->name('master-pengirim-penerima.download-template')
         ->middleware('can:master-pengirim-penerima-create');
    Route::post('master-pengirim-penerima/import', [MasterPengirimPenerimaController::class, 'import'])
         ->name('master-pengirim-penerima.import')
         ->middleware('can:master-pengirim-penerima-create');
    
    // Popup routes for adding penerima from tanda terima
    Route::get('tanda-terima/penerima/create', [MasterPengirimPenerimaController::class, 'createForTandaTerima'])
         ->name('tanda-terima.penerima.create');
    Route::post('tanda-terima/penerima/store', [MasterPengirimPenerimaController::class, 'storeForTandaTerima'])
         ->name('tanda-terima.penerima.store');
    
    // 📦 Master Pengirim/Penerima Management with permissions
    Route::resource('master-pengirim-penerima', MasterPengirimPenerimaController::class)
         ->middleware([
             'index' => 'can:master-pengirim-penerima-view',
             'create' => 'can:master-pengirim-penerima-create',
             'store' => 'can:master-pengirim-penerima-create',
             'show' => 'can:master-pengirim-penerima-view',
             'edit' => 'can:master-pengirim-penerima-update',
             'update' => 'can:master-pengirim-penerima-update',
             'destroy' => 'can:master-pengirim-penerima-delete'
         ]);

    // 📦 Pengirim - Special routes for Order form (no permission required)
    Route::get('order/pengirim/create', [PengirimController::class, 'createForOrder'])
         ->name('order.pengirim.create');
    Route::post('order/pengirim/store', [PengirimController::class, 'storeForOrder'])
         ->name('order.pengirim.store');

    // 📥 Penerima - Special routes for Order form (no permission required, using MasterPengirimPenerimaController)
    Route::get('order/penerima/create', [MasterPengirimPenerimaController::class, 'createForOrder'])
         ->name('order.penerima.create');
    Route::post('order/penerima/store', [MasterPengirimPenerimaController::class, 'storeForOrder'])
         ->name('order.penerima.store');

    // 🎯 Tujuan Kirim - Special routes for Order form (no permission required)
    Route::get('order/tujuan-kirim/create', [MasterTujuanKirimController::class, 'createForOrder'])
         ->name('order.tujuan-kirim.create');
    Route::post('order/tujuan-kirim/store', [MasterTujuanKirimController::class, 'storeForOrder'])
         ->name('order.tujuan-kirim.store');

    // Suggestion API for create-for-order popup
    Route::get('order/tujuan-kirim/suggest', [MasterTujuanKirimController::class, 'suggestForOrder'])
         ->name('order.tujuan-kirim.suggest');

    // 🎯 Tujuan Ambil - Special routes for Order form (no permission required)
    Route::get('order/tujuan-ambil/create', [TujuanKegiatanUtamaController::class, 'createForOrder'])
         ->name('order.tujuan-ambil.create');
    Route::post('order/tujuan-ambil/store', [TujuanKegiatanUtamaController::class, 'storeForOrder'])
         ->name('order.tujuan-ambil.store');

    // 📋 Term - Special routes for Order form (no permission required)
    Route::get('order/term/create', [TermController::class, 'createForOrder'])
         ->name('order.term.create');
    Route::post('order/term/store', [TermController::class, 'storeForOrder'])
         ->name('order.term.store');

    // 📦 Jenis Barang - Special routes for Order form (no permission required)
    Route::get('order/jenis-barang/create', [JenisBarangController::class, 'createForOrder'])
         ->name('order.jenis-barang.create');
    Route::post('order/jenis-barang/store', [JenisBarangController::class, 'storeForOrder'])
         ->name('order.jenis-barang.store');

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

    // Tipe Barang Management
    Route::resource('master/tipe-barang', TipeBarangController::class)
         ->names('master.tipe-barang')
         ->middleware('auth');

    // Merk Ban Management
    Route::resource('master/merk-ban', MerkBanController::class)
         ->names('master.merk-ban')
         ->middleware('auth');

    // Nama Stock Ban Management
    Route::resource('master/nama-stock-ban', NamaStockBanController::class)
         ->names('master.nama-stock-ban')
         ->middleware('auth');

    // Tipe Stock Ban Management
    Route::resource('master/tipe-stock-ban', TipeStockBanController::class)
         ->names('master.tipe-stock-ban')
         ->middleware('auth');

    // 📦 Klasifikasi Biaya (Master) Management with permissions
    Route::get('master/klasifikasi-biaya-download-template', [\App\Http\Controllers\Master\KlasifikasiBiayaController::class, 'downloadTemplate'])
         ->name('klasifikasi-biaya.download-template')
         ->middleware('can:master-klasifikasi-biaya-view');

    Route::get('master/klasifikasi-biaya-import', [\App\Http\Controllers\Master\KlasifikasiBiayaController::class, 'showImportForm'])
         ->name('klasifikasi-biaya.import-form')
         ->middleware('can:master-klasifikasi-biaya-create');

    Route::post('master/klasifikasi-biaya-import', [\App\Http\Controllers\Master\KlasifikasiBiayaController::class, 'import'])
         ->name('klasifikasi-biaya.import')
         ->middleware('can:master-klasifikasi-biaya-create');

    Route::get('master/klasifikasi-biaya-get-next-kode', [\App\Http\Controllers\Master\KlasifikasiBiayaController::class, 'getNextKode'])
         ->name('klasifikasi-biaya.get-next-kode')
         ->middleware('can:master-klasifikasi-biaya-create');

    Route::resource('master/klasifikasi-biaya', KlasifikasiBiayaController::class)
         ->names('klasifikasi-biaya')
         ->middleware([
             'index' => 'can:master-klasifikasi-biaya-view',
             'create' => 'can:master-klasifikasi-biaya-create',
             'store' => 'can:master-klasifikasi-biaya-create',
             'show' => 'can:master-klasifikasi-biaya-view',
             'edit' => 'can:master-klasifikasi-biaya-update',
             'update' => 'can:master-klasifikasi-biaya-update',
             'destroy' => 'can:master-klasifikasi-biaya-delete'
         ]);

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

    // 📦 Pergerakan Kontainer Management
    Route::get('pergerakan-kontainer', [\App\Http\Controllers\PergerakanKontainerController::class, 'index'])
         ->name('pergerakan-kontainer.index')
         ->middleware('can:pergerakan-kontainer-view');

    Route::post('pergerakan-kontainer', [\App\Http\Controllers\PergerakanKontainerController::class, 'store'])
         ->name('pergerakan-kontainer.store')
         ->middleware('can:pergerakan-kontainer-create');

    Route::get('pergerakan-kapal/export', [\App\Http\Controllers\PergerakanKapalController::class, 'export'])
         ->name('pergerakan-kapal.export')
         ->middleware('can:pergerakan-kapal-export');
});

// ═══════════════════════════════════════════════════════════════════════
// � ORDER MANAGEMENT ROUTES
// ═══════════════════════════════════════════════════════════════════════

Route::middleware(['auth'])->group(function () {
    // Order Data Management Routes (formerly approval)
    Route::prefix('orders/approval')->name('orders.approval.')->group(function () {
        Route::get('/', [OrderDataManagementController::class, 'index'])->name('index');
    });

    // Order Template Download Route
    Route::get('orders/download-template', [OrderController::class, 'downloadTemplate'])
         ->name('orders.download.template')
         ->middleware('can:order-view');

    // 📋 Order Management with permissions - Separate routes to avoid middleware conflicts
    Route::get('orders', [OrderController::class, 'index'])
         ->name('orders.index')
         ->middleware('can:order-view');
    
    Route::get('orders/create', [OrderController::class, 'create'])
         ->name('orders.create')
         ->middleware('can:order-create');
    
    Route::post('orders', [OrderController::class, 'store'])
         ->name('orders.store')
         ->middleware('can:order-create');
    
    Route::get('orders/{order}', [OrderController::class, 'show'])
         ->name('orders.show')
         ->middleware('can:order-view');
    
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])
         ->name('orders.edit')
         ->middleware('can:order-update');
    
    Route::put('orders/{order}', [OrderController::class, 'update'])
         ->name('orders.update')
         ->middleware('can:order-update');
    
    Route::patch('orders/{order}', [OrderController::class, 'update'])
         ->name('orders.update')
         ->middleware('can:order-update');
    
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])
         ->name('orders.destroy')
         ->middleware('can:order-delete');

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
    // 🔍 APPROVAL ORDER ROUTES
    // ═══════════════════════════════════════════════════════════════════════

    Route::prefix('approval-order')->name('approval-order.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ApprovalOrderController::class, 'index'])
             ->name('index')
             ->middleware('can:approval-order-view');
        
        Route::get('/create', [\App\Http\Controllers\ApprovalOrderController::class, 'create'])
             ->name('create')
             ->middleware('can:approval-order-create');
        
        Route::post('/', [\App\Http\Controllers\ApprovalOrderController::class, 'store'])
             ->name('store')
             ->middleware('can:approval-order-create');
        
        Route::get('/{id}', [\App\Http\Controllers\ApprovalOrderController::class, 'show'])
             ->name('show')
             ->middleware('can:approval-order-view');
        
        Route::get('/{id}/edit', [\App\Http\Controllers\ApprovalOrderController::class, 'edit'])
             ->name('edit')
             ->middleware('can:approval-order-update');
        
        Route::put('/{id}', [\App\Http\Controllers\ApprovalOrderController::class, 'update'])
             ->name('update')
             ->middleware('can:approval-order-update');
        
        Route::delete('/{id}', [\App\Http\Controllers\ApprovalOrderController::class, 'destroy'])
             ->name('destroy')
             ->middleware('can:approval-order-delete');
        
        Route::post('/{id}/approve', [\App\Http\Controllers\ApprovalOrderController::class, 'approve'])
             ->name('approve')
             ->middleware('can:approval-order-approve');
        
        Route::post('/{id}/reject', [\App\Http\Controllers\ApprovalOrderController::class, 'reject'])
             ->name('reject')
             ->middleware('can:approval-order-reject');
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

    // Surat Jalan Management with permissions - Separate routes to avoid middleware conflicts
    // Export Surat Jalan (Excel)
    Route::get('surat-jalan/export', [\App\Http\Controllers\SuratJalanController::class, 'exportExcel'])
         ->name('surat-jalan.export')
         ->middleware('can:surat-jalan-export');

    Route::get('surat-jalan', [\App\Http\Controllers\SuratJalanController::class, 'index'])
         ->name('surat-jalan.index')
         ->middleware('can:surat-jalan-view');
    
    Route::get('surat-jalan/create', [\App\Http\Controllers\SuratJalanController::class, 'create'])
         ->name('surat-jalan.create')
         ->middleware('can:surat-jalan-create');
    
    Route::post('surat-jalan', [\App\Http\Controllers\SuratJalanController::class, 'store'])
         ->name('surat-jalan.store')
         ->middleware('can:surat-jalan-create');
    
    Route::get('surat-jalan/{suratJalan}', [\App\Http\Controllers\SuratJalanController::class, 'show'])
         ->name('surat-jalan.show')
         ->middleware('can:surat-jalan-view');
    
    Route::get('surat-jalan/{suratJalan}/edit', [\App\Http\Controllers\SuratJalanController::class, 'edit'])
         ->name('surat-jalan.edit')
         ->middleware('can:surat-jalan-update');
    
    Route::put('surat-jalan/{suratJalan}', [\App\Http\Controllers\SuratJalanController::class, 'update'])
         ->name('surat-jalan.update')
         ->middleware('can:surat-jalan-update');
    
    Route::patch('surat-jalan/{suratJalan}', [\App\Http\Controllers\SuratJalanController::class, 'update'])
         ->name('surat-jalan.update')
         ->middleware('can:surat-jalan-update');
    
    Route::delete('surat-jalan/{suratJalan}', [\App\Http\Controllers\SuratJalanController::class, 'destroy'])
         ->name('surat-jalan.destroy')
         ->middleware('can:surat-jalan-delete');

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

    // ═══════════════════════════════════════════════════════════════════════
    // APPROVAL SURAT JALAN ROUTES
    // ═══════════════════════════════════════════════════════════════════════

    Route::prefix('approval-surat-jalan')->name('approval-surat-jalan.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'index'])
             ->name('index')
             ->middleware('can:approval-surat-jalan-view');
        
        Route::get('/create', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'create'])
             ->name('create')
             ->middleware('can:approval-surat-jalan-create');
        
        Route::post('/', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'store'])
             ->name('store')
             ->middleware('can:approval-surat-jalan-create');
        
        Route::get('/{id}', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'show'])
             ->name('show')
             ->middleware('can:approval-surat-jalan-view');
        
        Route::get('/{id}/edit', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'edit'])
             ->name('edit')
             ->middleware('can:approval-surat-jalan-update');
        
        Route::put('/{id}', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'update'])
             ->name('update')
             ->middleware('can:approval-surat-jalan-update');
        
        Route::delete('/{id}', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'destroy'])
             ->name('destroy')
             ->middleware('can:approval-surat-jalan-delete');
        
        Route::post('/{id}/approve', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'approve'])
             ->name('approve')
             ->middleware('can:approval-surat-jalan-approve');
        
        Route::post('/{id}/reject', [\App\Http\Controllers\ApprovalSuratJalanController::class, 'reject'])
             ->name('reject')
             ->middleware('can:approval-surat-jalan-reject');
    });

    // =============================
    // UANG JALAN MANAGEMENT ROUTES
    // =============================

    // Uang Jalan Management with permissions
    // Custom route untuk select surat jalan
    Route::get('uang-jalan/select-surat-jalan', [\App\Http\Controllers\UangJalanController::class, 'selectSuratJalan'])
         ->name('uang-jalan.select-surat-jalan')
         ->middleware('can:uang-jalan-create');

    // Alias route untuk select surat jalan penyesuaian (penambahan/pengurangan UJ)
    Route::get('uang-jalan/select-surat-jalan-penyesuaian', [\App\Http\Controllers\UangJalanController::class, 'selectSuratJalan'])
         ->name('uang-jalan.select-surat-jalan-penyesuaian')
         ->middleware('can:uang-jalan-create');
    
    // Export Uang Jalan (Excel)
    Route::get('uang-jalan/export', [\App\Http\Controllers\UangJalanController::class, 'exportExcel'])
         ->name('uang-jalan.export')
         ->middleware('can:uang-jalan-export');

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

    // Uang Jalan Adjustment Routes (Penambahan/Pengurangan)
    Route::prefix('uang-jalan')->name('uang-jalan.')->group(function () {
        Route::get('adjustment/get-first-uang-jalan', [\App\Http\Controllers\UangJalanController::class, 'getFirstUangJalanForAdjustment'])
             ->name('adjustment.get-first-uang-jalan')
             ->middleware('can:uang-jalan-create');
        
        Route::get('adjustment/select-surat-jalan', [\App\Http\Controllers\UangJalanController::class, 'selectSuratJalanAdjustment'])
             ->name('adjustment.select-surat-jalan')
             ->middleware('can:uang-jalan-create');
        
        Route::get('adjustment/select-uang-jalan', [\App\Http\Controllers\UangJalanController::class, 'selectUangJalanAdjustment'])
             ->name('adjustment.select-uang-jalan')
             ->middleware('can:uang-jalan-create');
        
        Route::get('adjustment/create', [\App\Http\Controllers\UangJalanController::class, 'createAdjustment'])
             ->name('adjustment.create')
             ->middleware('can:uang-jalan-create');
        
        Route::post('adjustment/store', [\App\Http\Controllers\UangJalanController::class, 'storeAdjustment'])
             ->name('adjustment.store')
             ->middleware('can:uang-jalan-create');
    });

     // Uang Jalan Bongkaran Management
     Route::get('uang-jalan-bongkaran/select-surat-jalan-bongkaran', [\App\Http\Controllers\UangJalanBongkaranController::class, 'selectSuratJalanBongkaran'])
           ->name('uang-jalan-bongkaran.select-surat-jalan-bongkaran')
           ->middleware('can:uang-jalan-bongkaran-create');

     Route::resource('uang-jalan-bongkaran', \App\Http\Controllers\UangJalanBongkaranController::class)
           ->middleware([
                'index' => 'can:uang-jalan-bongkaran-view',
                'create' => 'can:uang-jalan-bongkaran-create',
                'store' => 'can:uang-jalan-bongkaran-create',
                'show' => 'can:uang-jalan-bongkaran-view',
                'edit' => 'can:uang-jalan-bongkaran-update',
                'update' => 'can:uang-jalan-bongkaran-update',
                'destroy' => 'can:uang-jalan-bongkaran-delete'
           ]);

    // ====================================
    // PRANOTA UANG JALAN MANAGEMENT ROUTES
    // ====================================

    // Pranota Uang Jalan Management with permissions
    // Export Pranota Uang Jalan
    Route::get('pranota-uang-jalan/export', [\App\Http\Controllers\PranotaSuratJalanController::class, 'exportExcel'])
         ->name('pranota-uang-jalan.export')
         ->middleware('can:pranota-uang-jalan-export');

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

     // Pranota Uang Jalan Bongkaran - list & basic management
          Route::get('pranota-uang-jalan-bongkaran', [\App\Http\Controllers\PranotaUangJalanBongkaranController::class, 'index'])
               ->name('pranota-uang-jalan-bongkaran.index')
               ->middleware('can:pranota-uang-jalan-bongkaran-view');

     // Pranota OB - list & basic management
          Route::get('pranota-ob', [\App\Http\Controllers\PranotaObController::class, 'index'])
               ->name('pranota-ob.index')
               ->middleware('can:pranota-ob-view');

     // Pranota OB Management
          Route::resource('pranota-ob', \App\Http\Controllers\PranotaObController::class)
               ->only(['index','show','destroy'])
               ->middleware([
                   'index' => 'can:pranota-ob-view',
                   'show' => 'can:pranota-ob-view',
                   'destroy' => 'can:pranota-ob-delete',
               ]);

     // Pranota OB Print route
          Route::get('pranota-ob/{pranota}/print', [\App\Http\Controllers\PranotaObController::class, 'print'])
               ->name('pranota-ob.print')
               ->middleware('can:pranota-ob-view');

     // Pranota OB Input DP route
          Route::get('pranota-ob/{pranota}/input-dp', [\App\Http\Controllers\PranotaObController::class, 'inputDp'])
               ->name('pranota-ob.input-dp')
               ->middleware('can:pranota-ob-view');

     // Pranota Uang Rit Management
          Route::resource('pranota-rit', \App\Http\Controllers\PranotaUangRitController::class)
               ->middleware([
                   'index' => 'can:pranota-rit-view',
                   'create' => 'can:pranota-rit-create',
                   'store' => 'can:pranota-rit-create',
                   'show' => 'can:pranota-rit-view',
                   'edit' => 'can:pranota-rit-update',
                   'update' => 'can:pranota-rit-update',
                   'destroy' => 'can:pranota-rit-delete'
               ]);

     // Pranota Rit Kenek route removed - functionality moved to pranota-uang-rit-kenek

     // Pranota Uang Jalan Bongkaran Management with permissions
      Route::resource('pranota-uang-jalan-bongkaran', \App\Http\Controllers\PranotaUangJalanBongkaranController::class)
             ->middleware([
                  'index' => 'can:pranota-uang-jalan-bongkaran-view',
                  'create' => 'can:pranota-uang-jalan-bongkaran-create',
                  'store' => 'can:pranota-uang-jalan-bongkaran-create',
                  'show' => 'can:pranota-uang-jalan-bongkaran-view',
                  'edit' => 'can:pranota-uang-jalan-bongkaran-update',
                  'update' => 'can:pranota-uang-jalan-bongkaran-update',
                  'destroy' => 'can:pranota-uang-jalan-bongkaran-delete'
             ]);

     // Print Pranota Uang Jalan Bongkaran
          Route::get('pranota-uang-jalan-bongkaran/{pranotaUangJalanBongkaran}/print', [\App\Http\Controllers\PranotaUangJalanBongkaranController::class, 'print'])
               ->name('pranota-uang-jalan-bongkaran.print')
               ->middleware('can:pranota-uang-jalan-bongkaran-view');
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
    
    // Surat Jalan Bongkaran Management with permissions - Separate routes to avoid middleware conflicts
    // API endpoint to fetch Manifest data (new primary method)
    Route::get('api/manifest/{id}', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'getManifestById'])
         ->name('api.manifest.show')
         ->middleware('can:surat-jalan-bongkaran-view');
    
    // API endpoint to fetch BL data (backward compatibility)
    Route::get('api/bl/{id}', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'getBlById'])
         ->name('api.bl.show')
         ->middleware('can:surat-jalan-bongkaran-view');
    
    // API endpoint to fetch Surat Jalan Bongkaran data
    Route::get('api/surat-jalan-bongkaran/{id}', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'getSuratJalanById'])
         ->name('api.surat-jalan-bongkaran.show')
         ->middleware('can:surat-jalan-bongkaran-view');
    
    Route::get('surat-jalan-bongkaran/select-ship', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'selectShip'])
         ->name('surat-jalan-bongkaran.select-ship')
         ->middleware('can:surat-jalan-bongkaran-view');
    
    Route::get('surat-jalan-bongkaran/get-voyages', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'getVoyages'])
         ->name('surat-jalan-bongkaran.get-voyages')
         ->middleware('can:surat-jalan-bongkaran-view');
    
    Route::get('surat-jalan-bongkaran', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'selectShip'])
         ->name('surat-jalan-bongkaran.index')
         ->middleware('can:surat-jalan-bongkaran-view');

    Route::get('surat-jalan-bongkaran/list', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'index'])
         ->name('surat-jalan-bongkaran.list')
         ->middleware('can:surat-jalan-bongkaran-view');

    Route::get('surat-jalan-bongkaran/export', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'export'])
         ->name('surat-jalan-bongkaran.export')
         ->middleware('can:surat-jalan-bongkaran-view');

    Route::get('surat-jalan-bongkaran/create', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'create'])
         ->name('surat-jalan-bongkaran.create')
         ->middleware('can:surat-jalan-bongkaran-create');
    
    Route::post('surat-jalan-bongkaran', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'store'])
         ->name('surat-jalan-bongkaran.store')
         ->middleware('can:surat-jalan-bongkaran-create');
    
    Route::get('surat-jalan-bongkaran/{suratJalanBongkaran}', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'show'])
         ->name('surat-jalan-bongkaran.show')
         ->middleware('can:surat-jalan-bongkaran-view');
    
    Route::get('surat-jalan-bongkaran/{suratJalanBongkaran}/edit', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'edit'])
         ->name('surat-jalan-bongkaran.edit')
         ->middleware('can:surat-jalan-bongkaran-update');
    
    Route::put('surat-jalan-bongkaran/{suratJalanBongkaran}', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'update'])
         ->name('surat-jalan-bongkaran.update')
         ->middleware('can:surat-jalan-bongkaran-update');
    
    Route::patch('surat-jalan-bongkaran/{suratJalanBongkaran}', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'update'])
         ->name('surat-jalan-bongkaran.update')
         ->middleware('can:surat-jalan-bongkaran-update');
    
    Route::delete('surat-jalan-bongkaran/{suratJalanBongkaran}', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'destroy'])
         ->name('surat-jalan-bongkaran.destroy')
         ->middleware('can:surat-jalan-bongkaran-delete');

    // Print surat jalan bongkaran
    Route::get('/surat-jalan-bongkaran/{suratJalanBongkaran}/print', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'print'])
         ->name('surat-jalan-bongkaran.print')
         ->middleware('can:surat-jalan-bongkaran-view');

    // Print SJ directly from BL (without creating surat jalan first)
    Route::get('/surat-jalan-bongkaran/print-from-bl/{bl}', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'printFromBl'])
         ->name('surat-jalan-bongkaran.print-from-bl')
         ->middleware('can:surat-jalan-bongkaran-view');

    // Print BA (Berita Acara) directly from BL
    Route::get('/surat-jalan-bongkaran/print-ba/{bl}', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'printBa'])
         ->name('surat-jalan-bongkaran.print-ba')
         ->middleware('can:surat-jalan-bongkaran-view');

    // Download PDF surat jalan bongkaran
    Route::get('/surat-jalan-bongkaran/{suratJalanBongkaran}/download', [\App\Http\Controllers\SuratJalanBongkaranController::class, 'downloadPdf'])
         ->name('surat-jalan-bongkaran.download')
         ->middleware('can:surat-jalan-bongkaran-view');

    // ============= TANDA TERIMA BONGKARAN ROUTES =============
    
    // Get next number for tanda terima bongkaran
    Route::get('/tanda-terima-bongkaran-next-number', [\App\Http\Controllers\TandaTerimaBongkaranController::class, 'getNextNumber'])
         ->name('tanda-terima-bongkaran.get-next-number')
         ->middleware('can:tanda-terima-bongkaran-create');
    
    // Tanda Terima Bongkaran Management with permissions
    Route::resource('tanda-terima-bongkaran', \App\Http\Controllers\TandaTerimaBongkaranController::class)
         ->middleware([
              'index' => 'can:tanda-terima-bongkaran-view',
              'create' => 'can:tanda-terima-bongkaran-create',
              'store' => 'can:tanda-terima-bongkaran-create',
              'show' => 'can:tanda-terima-bongkaran-view',
              'edit' => 'can:tanda-terima-bongkaran-update',
              'update' => 'can:tanda-terima-bongkaran-update',
              'destroy' => 'can:tanda-terima-bongkaran-delete'
         ]);

    // Print tanda terima bongkaran
    Route::get('/tanda-terima-bongkaran/{tandaTerimaBongkaran}/print', [\App\Http\Controllers\TandaTerimaBongkaranController::class, 'print'])
         ->name('tanda-terima-bongkaran.print')
         ->middleware('can:tanda-terima-bongkaran-print');

    // Export tanda terima bongkaran
    Route::get('/tanda-terima-bongkaran/export/excel', [\App\Http\Controllers\TandaTerimaBongkaranController::class, 'exportExcel'])
         ->name('tanda-terima-bongkaran.export')
         ->middleware('can:tanda-terima-bongkaran-export');

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

    // Special routes must come BEFORE resource routes to avoid conflicts
    Route::get('pranota-uang-rit/select-uang-jalan', [\App\Http\Controllers\PranotaUangRitController::class, 'selectUangJalan'])
         ->name('pranota-uang-rit.select-uang-jalan')
         ->middleware('can:pranota-uang-rit-create');

    // Page for selecting the date range before creating a pranota
    Route::get('pranota-uang-rit/select-date', [\App\Http\Controllers\PranotaUangRitController::class, 'selectDate'])
         ->name('pranota-uang-rit.select-date')
         ->middleware('can:pranota-uang-rit-create');

    Route::post('pranota-uang-rit/from-selection', [\App\Http\Controllers\PranotaUangRitController::class, 'createFromSelection'])
         ->name('pranota-uang-rit.from-selection')
         ->middleware('can:pranota-uang-rit-create');

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

    // Additional Pranota Uang Rit routes for workflow
    Route::post('pranota-uang-rit/{pranotaUangRit}/submit', [\App\Http\Controllers\PranotaUangRitController::class, 'submit'])
         ->name('pranota-uang-rit.submit')
         ->middleware('can:pranota-uang-rit-update');

    // Export Excel for selected surat jalan
    Route::post('pranota-uang-rit/export-excel', [\App\Http\Controllers\PranotaUangRitController::class, 'exportExcel'])
         ->name('pranota-uang-rit.export-excel')
         ->middleware('can:pranota-uang-rit-create');

    // Export single pranota to Excel
    Route::get('pranota-uang-rit/{pranotaUangRit}/export-single', [\App\Http\Controllers\PranotaUangRitController::class, 'exportSingle'])
         ->name('pranota-uang-rit.export-single')
         ->middleware('can:pranota-uang-rit-view');

    // Export all surat jalan in a pranota to Excel
    Route::get('pranota-uang-rit/{pranotaUangRit}/export-surat-jalan', [\App\Http\Controllers\PranotaUangRitController::class, 'exportSuratJalan'])
         ->name('pranota-uang-rit.export-surat-jalan')
         ->middleware('can:pranota-uang-rit-view');

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
    // � PRANOTA UANG RIT KENEK MANAGEMENT
    // ═══════════════════════════════════════════════════════════════════════

    // Special routes must come BEFORE resource routes to avoid conflicts
    Route::get('pranota-uang-rit-kenek/select-uang-jalan', [\App\Http\Controllers\PranotaUangRitKenekController::class, 'selectUangJalan'])
         ->name('pranota-uang-rit-kenek.select-uang-jalan')
         ->middleware('can:pranota-uang-rit-create');

    // Page for selecting the date range before creating a pranota
    Route::get('pranota-uang-rit-kenek/select-date', [\App\Http\Controllers\PranotaUangRitKenekController::class, 'selectDate'])
         ->name('pranota-uang-rit-kenek.select-date')
         ->middleware('can:pranota-uang-rit-create');

    Route::post('pranota-uang-rit-kenek/from-selection', [\App\Http\Controllers\PranotaUangRitKenekController::class, 'createFromSelection'])
         ->name('pranota-uang-rit-kenek.from-selection')
         ->middleware('can:pranota-uang-rit-create');

    // Pranota Uang Rit Kenek Management with permissions
    Route::resource('pranota-uang-rit-kenek', \App\Http\Controllers\PranotaUangRitKenekController::class)
         ->middleware([
             'index' => 'can:pranota-uang-rit-view',
             'create' => 'can:pranota-uang-rit-create',
             'store' => 'can:pranota-uang-rit-create',
             'show' => 'can:pranota-uang-rit-view',
             'edit' => 'can:pranota-uang-rit-update',
             'update' => 'can:pranota-uang-rit-update',
             'destroy' => 'can:pranota-uang-rit-delete'
         ]);

    // Additional Pranota Uang Rit Kenek routes for workflow
    Route::post('pranota-uang-rit-kenek/{pranotaUangRitKenek}/submit', [\App\Http\Controllers\PranotaUangRitKenekController::class, 'submit'])
         ->name('pranota-uang-rit-kenek.submit')
         ->middleware('can:pranota-uang-rit-update');

    Route::post('pranota-uang-rit-kenek/{pranotaUangRitKenek}/approve', [\App\Http\Controllers\PranotaUangRitKenekController::class, 'approve'])
         ->name('pranota-uang-rit-kenek.approve')
         ->middleware('can:pranota-uang-rit-approve');

    Route::post('pranota-uang-rit-kenek/{pranotaUangRitKenek}/mark-as-paid', [\App\Http\Controllers\PranotaUangRitKenekController::class, 'markAsPaid'])
         ->name('pranota-uang-rit-kenek.mark-as-paid')
         ->middleware('can:pranota-uang-rit-mark-paid');

    Route::get('pranota-uang-rit-kenek/{pranotaUangRitKenek}/print', [\App\Http\Controllers\PranotaUangRitKenekController::class, 'print'])
         ->name('pranota-uang-rit-kenek.print')
         ->middleware('can:pranota-uang-rit-view');

    Route::get('pranota-uang-rit-kenek/{pranotaUangRitKenek}/export-surat-jalan', [\App\Http\Controllers\PranotaUangRitKenekController::class, 'exportSuratJalan'])
         ->name('pranota-uang-rit-kenek.export-surat-jalan')
         ->middleware('can:pranota-uang-rit-view');

    Route::post('pranota-uang-rit-kenek/export-excel', [\App\Http\Controllers\PranotaUangRitKenekController::class, 'exportExcel'])
         ->name('pranota-uang-rit-kenek.export-excel')
         ->middleware('can:pranota-uang-rit-view');

    // ═══════════════════════════════════════════════════════════════════════
    // �🚚 PRANOTA UANG KENEK MANAGEMENT
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

    // Route untuk select surat jalan (halaman awal)
    Route::get('tanda-terima/select-surat-jalan', [\App\Http\Controllers\TandaTerimaController::class, 'selectSuratJalan'])
         ->name('tanda-terima.select-surat-jalan')
         ->middleware('can:tanda-terima-view');

    // Route untuk create tanda terima dari surat jalan
    Route::get('tanda-terima/from-surat-jalan/{suratJalan}', [\App\Http\Controllers\TandaTerimaController::class, 'createFromSuratJalan'])
         ->name('tanda-terima.from-surat-jalan')
         ->middleware('can:tanda-terima-create');

    // Resource routes untuk tanda terima dengan middleware yang benar
    Route::get('tanda-terima', [\App\Http\Controllers\TandaTerimaController::class, 'index'])
         ->name('tanda-terima.index')
         ->middleware('can:tanda-terima-view');
    
    Route::get('tanda-terima/create', [\App\Http\Controllers\TandaTerimaController::class, 'create'])
         ->name('tanda-terima.create')
         ->middleware('can:tanda-terima-create');
    
    Route::post('tanda-terima', [\App\Http\Controllers\TandaTerimaController::class, 'store'])
         ->name('tanda-terima.store')
         ->middleware('can:tanda-terima-create');
    
    Route::get('tanda-terima/{tanda_terima}', [\App\Http\Controllers\TandaTerimaController::class, 'show'])
         ->name('tanda-terima.show')
         ->middleware('can:tanda-terima-view');
    
    Route::get('tanda-terima/{tanda_terima}/edit', [\App\Http\Controllers\TandaTerimaController::class, 'edit'])
         ->name('tanda-terima.edit')
         ->middleware('can:tanda-terima-update');
    
    Route::put('tanda-terima/{tanda_terima}', [\App\Http\Controllers\TandaTerimaController::class, 'update'])
         ->name('tanda-terima.update')
         ->middleware('can:tanda-terima-update');
    
    Route::delete('tanda-terima/{tanda_terima}', [\App\Http\Controllers\TandaTerimaController::class, 'destroy'])
         ->name('tanda-terima.destroy')
         ->middleware('can:tanda-terima-delete');

    // Route untuk menambahkan cargo ke prospek
    Route::post('tanda-terima/{tandaTerima}/add-to-prospek', [\App\Http\Controllers\TandaTerimaController::class, 'addToProspek'])
         ->name('tanda-terima.add-to-prospek')
         ->middleware('can:tanda-terima-update');

    // Route untuk bulk add to prospek
    Route::post('tanda-terima/bulk-add-to-prospek', [\App\Http\Controllers\TandaTerimaController::class, 'bulkAddToProspek'])
         ->name('tanda-terima.bulk-add-to-prospek')
         ->middleware('can:tanda-terima-update');

    // Route untuk bulk delete tanda terima
    Route::delete('tanda-terima/bulk-delete', [\App\Http\Controllers\TandaTerimaController::class, 'bulkDelete'])
         ->name('tanda-terima.bulk-delete')
         ->middleware('can:tanda-terima-delete');

    // Route untuk export Excel tanda terima
    Route::post('tanda-terima/export-excel', [\App\Http\Controllers\TandaTerimaController::class, 'exportExcel'])
         ->name('tanda-terima.export-excel')
         ->middleware('can:tanda-terima-view');

    // Route untuk export Excel tanda terima berdasarkan filter (GET, filter-aware)
    Route::get('tanda-terima/export', [\App\Http\Controllers\TandaTerimaController::class, 'exportFiltered'])
         ->name('tanda-terima.export')
         ->middleware('can:tanda-terima-export');

    // Route untuk export Excel tanda terima berdasarkan filter (POST, for form submission)
    Route::post('tanda-terima/export', [\App\Http\Controllers\TandaTerimaController::class, 'exportFiltered'])
         ->name('tanda-terima.export.post')
         ->middleware('can:tanda-terima-export');

    // ═══════════════════════════════════════════════════════════════════════
    // 🚚 PERGERAKAN KONTAINER (CONTAINER MOVEMENT)
    // ═══════════════════════════════════════════════════════════════════════
    Route::get('pergerakan-kontainer', [\App\Http\Controllers\PergerakanKontainerController::class, 'index'])
         ->name('pergerakan-kontainer.index')
         ->middleware('can:pergerakan-kontainer-view');
    Route::post('pergerakan-kontainer', [\App\Http\Controllers\PergerakanKontainerController::class, 'store'])
         ->name('pergerakan-kontainer.store')
         ->middleware('can:pergerakan-kontainer-create');

    // 🕒 HISTORY KONTAINER (CONTAINER HISTORY)
    // ═══════════════════════════════════════════════════════════════════════
    Route::get('history-kontainer', [\App\Http\Controllers\HistoryKontainerController::class, 'index'])
         ->name('history-kontainer.index')
         ->middleware('can:master-kontainer-view');

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

    Route::get('tanda-terima-lcl/stuffing', [\App\Http\Controllers\TandaTerimaLclController::class, 'stuffing'])
         ->name('tanda-terima-lcl.stuffing')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-view');

    Route::post('tanda-terima-lcl/stuffing/process', [\App\Http\Controllers\TandaTerimaLclController::class, 'processStuffing'])
         ->name('tanda-terima-lcl.stuffing.process')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-update');

    Route::post('tanda-terima-lcl/seal', [\App\Http\Controllers\TandaTerimaLclController::class, 'sealKontainer'])
         ->name('tanda-terima-lcl.seal')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-update');

    Route::post('tanda-terima-lcl/unseal', [\App\Http\Controllers\TandaTerimaLclController::class, 'unsealKontainer'])
         ->name('tanda-terima-lcl.unseal')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-update');

    Route::post('tanda-terima-lcl/assign-container', [\App\Http\Controllers\TandaTerimaLclController::class, 'assignContainer'])
         ->name('tanda-terima-tanpa-surat-jalan.assign-container')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-update');

    Route::post('tanda-terima-lcl/bulk-split', [\App\Http\Controllers\TandaTerimaLclController::class, 'bulkSplit'])
         ->name('tanda-terima-lcl.bulk-split')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-create');
    
    Route::post('tanda-terima-lcl/get-barang-from-containers', [\App\Http\Controllers\TandaTerimaLclController::class, 'getBarangFromContainers'])
         ->name('tanda-terima-lcl.get-barang-from-containers')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-view');
    
    Route::post('tanda-terima-lcl/get-barang-from-containers-by-nomor', [\App\Http\Controllers\TandaTerimaLclController::class, 'getBarangFromContainersByNomor'])
         ->name('tanda-terima-lcl.get-barang-from-containers-by-nomor')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-view');
    
    Route::get('tanda-terima-lcl/show-container/{nomor_kontainer}', [\App\Http\Controllers\TandaTerimaLclController::class, 'showContainer'])
         ->name('tanda-terima-lcl.show-container')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-view');
    
    Route::post('tanda-terima-lcl/{id}/remove-from-container', [\App\Http\Controllers\TandaTerimaLclController::class, 'removeFromContainer'])
         ->name('tanda-terima-lcl.remove-from-container');

    // Download image route for LCL (must be before resource route)
    Route::get('tanda-terima-lcl/{tandaTerimaTanpaSuratJalan}/download-image/{imageIndex}', 
               [\App\Http\Controllers\TandaTerimaLclController::class, 'downloadImage'])
         ->name('tanda-terima-lcl.download-image')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-view');

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

    // Bulk export (selected) for standard tanda terima tanpa surat jalan
    Route::get('tanda-terima-tanpa-surat-jalan/export', [\App\Http\Controllers\TandaTerimaTanpaSuratJalanController::class, 'bulkExport'])
         ->name('tanda-terima-tanpa-surat-jalan.export')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-view');

    // Download image route (must be before resource route)
    Route::get('tanda-terima-tanpa-surat-jalan/{tandaTerimaTanpaSuratJalan}/download-image/{imageIndex}', 
               [\App\Http\Controllers\TandaTerimaTanpaSuratJalanController::class, 'downloadImage'])
         ->name('tanda-terima-tanpa-surat-jalan.download-image')
         ->middleware('can:tanda-terima-tanpa-surat-jalan-view');

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

    // ═══════════════════════════════════════════════════════════════════════
    // 🚚 CHECKPOINT KONTAINER KELUAR ROUTES
    // ═══════════════════════════════════════════════════════════════════════
    Route::get('checkpoint-kontainer-keluar', [\App\Http\Controllers\CheckpointKontainerKeluarController::class, 'index'])
         ->name('checkpoint-kontainer-keluar.index')
         ->middleware('can:checkpoint-kontainer-keluar-view');

    Route::get('checkpoint-kontainer-keluar/history', [\App\Http\Controllers\CheckpointKontainerKeluarController::class, 'history'])
         ->name('checkpoint-kontainer-keluar.history')
         ->middleware('can:checkpoint-kontainer-keluar-view');

    Route::get('checkpoint-kontainer-keluar/{cabangSlug}', [\App\Http\Controllers\CheckpointKontainerKeluarController::class, 'checkpoint'])
         ->name('checkpoint-kontainer-keluar.checkpoint')
         ->middleware('can:checkpoint-kontainer-keluar-view');

    Route::get('checkpoint-kontainer-keluar/{cabangSlug}/gudang/{gudangId}', [\App\Http\Controllers\CheckpointKontainerKeluarController::class, 'showSuratJalan'])
         ->name('checkpoint-kontainer-keluar.surat-jalan')
         ->middleware('can:checkpoint-kontainer-keluar-view');

    Route::post('checkpoint-kontainer-keluar/{suratJalan}/keluar', [\App\Http\Controllers\CheckpointKontainerKeluarController::class, 'processKeluar'])
         ->name('checkpoint-kontainer-keluar.keluar')
         ->middleware('can:checkpoint-kontainer-keluar-create');

    Route::post('checkpoint-kontainer-keluar/bulk-keluar', [\App\Http\Controllers\CheckpointKontainerKeluarController::class, 'bulkKeluar'])
         ->name('checkpoint-kontainer-keluar.bulk-keluar')
         ->middleware('can:checkpoint-kontainer-keluar-create');

    Route::post('checkpoint-kontainer-keluar/{suratJalan}/cancel', [\App\Http\Controllers\CheckpointKontainerKeluarController::class, 'cancelKeluar'])
         ->name('checkpoint-kontainer-keluar.cancel')
         ->middleware('can:checkpoint-kontainer-keluar-delete');

    Route::post('checkpoint-kontainer-keluar/kirim-kontainer', [\App\Http\Controllers\CheckpointKontainerKeluarController::class, 'kirimKontainer'])
         ->name('checkpoint-kontainer-keluar.kirim')
         ->middleware('can:checkpoint-kontainer-keluar-create');

    Route::post('pengembalian-kontainer', [\App\Http\Controllers\CheckpointKontainerKeluarController::class, 'storePengembalian'])
         ->name('pengembalian-kontainer.store')
         ->middleware('can:checkpoint-kontainer-keluar-create');

    // 📦 KONTAINER DALAM PERJALANAN ROUTES
    // ═══════════════════════════════════════════════════════════════════════
    Route::get('kontainer-perjalanan', [\App\Http\Controllers\KontainerPerjalananController::class, 'index'])
         ->name('kontainer-perjalanan.index')
         ->middleware('can:checkpoint-kontainer-keluar-view');

    // 📥 CHECKPOINT KONTAINER MASUK ROUTES
    // ═══════════════════════════════════════════════════════════════════════
    Route::get('checkpoint-kontainer-masuk', [\App\Http\Controllers\CheckpointKontainerMasukController::class, 'index'])
         ->name('checkpoint-kontainer-masuk.index')
         ->middleware('can:checkpoint-kontainer-masuk-view');

    Route::get('checkpoint-kontainer-masuk/history', [\App\Http\Controllers\CheckpointKontainerMasukController::class, 'history'])
         ->name('checkpoint-kontainer-masuk.history')
         ->middleware('can:checkpoint-kontainer-masuk-view');

    Route::get('checkpoint-kontainer-masuk/{cabangSlug}', [\App\Http\Controllers\CheckpointKontainerMasukController::class, 'checkpoint'])
         ->name('checkpoint-kontainer-masuk.checkpoint')
         ->middleware('can:checkpoint-kontainer-masuk-view');

    Route::get('checkpoint-kontainer-masuk/{cabangSlug}/gudang/{gudangId}', [\App\Http\Controllers\CheckpointKontainerMasukController::class, 'showKontainer'])
         ->name('checkpoint-kontainer-masuk.kontainer')
         ->middleware('can:checkpoint-kontainer-masuk-view');

    Route::post('checkpoint-kontainer-masuk/{cabangSlug}/gudang/{gudangId}/manual-masuk', [\App\Http\Controllers\CheckpointKontainerMasukController::class, 'manualMasuk'])
         ->name('checkpoint-kontainer-masuk.manual-masuk')
         ->middleware('can:checkpoint-kontainer-masuk-create');

    Route::post('checkpoint-kontainer-masuk/{kontainerPerjalananId}/masuk', [\App\Http\Controllers\CheckpointKontainerMasukController::class, 'processMasuk'])
         ->name('checkpoint-kontainer-masuk.masuk')
         ->middleware('can:checkpoint-kontainer-masuk-create');

    Route::post('checkpoint-kontainer-masuk/bulk-masuk', [\App\Http\Controllers\CheckpointKontainerMasukController::class, 'bulkMasuk'])
         ->name('checkpoint-kontainer-masuk.bulk-masuk')
         ->middleware('can:checkpoint-kontainer-masuk-create');

    Route::post('checkpoint-kontainer-masuk/{kontainerPerjalananId}/cancel', [\App\Http\Controllers\CheckpointKontainerMasukController::class, 'cancelMasuk'])
         ->name('checkpoint-kontainer-masuk.cancel')
         ->middleware('can:checkpoint-kontainer-masuk-delete');

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

    // Excel (XLSX) export for permohonan - filter-aware
    Route::get('permohonan/export-excel', [PermohonanController::class, 'exportExcel'])
         ->name('permohonan.export-excel')
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
            Route::get('/generate-nomor', [PembayaranPranotaUangJalanController::class, 'generateNomor'])
                ->name('generate-nomor')
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

     // 💰 PEMBAYARAN PRANOTA UANG JALAN BONGKARAN (Travel Allowance Payment Bongkaran)
     Route::prefix('pembayaran-pranota-uang-jalan-bongkaran')
          ->name('pembayaran-pranota-uang-jalan-bongkaran.')
          ->middleware(['auth'])
          ->group(function() {
               Route::get('/', [\App\Http\Controllers\PembayaranPranotaUangJalanBongkaranController::class, 'index'])
                    ->name('index')
                    ->middleware('can:pembayaran-pranota-uang-jalan-bongkaran-view');
               Route::get('/create', [\App\Http\Controllers\PembayaranPranotaUangJalanBongkaranController::class, 'create'])
                    ->name('create')
                    ->middleware('can:pembayaran-pranota-uang-jalan-bongkaran-create');
               Route::get('/generate-nomor', [\App\Http\Controllers\PembayaranPranotaUangJalanBongkaranController::class, 'generateNomor'])
                    ->name('generate-nomor')
                    ->middleware('can:pembayaran-pranota-uang-jalan-bongkaran-create');
               Route::post('/', [\App\Http\Controllers\PembayaranPranotaUangJalanBongkaranController::class, 'store'])
                    ->name('store')
                    ->middleware('can:pembayaran-pranota-uang-jalan-bongkaran-create');
               Route::get('/{pembayaran}', [\App\Http\Controllers\PembayaranPranotaUangJalanBongkaranController::class, 'show'])
                    ->name('show')
                    ->middleware('can:pembayaran-pranota-uang-jalan-bongkaran-view');
               Route::get('/{pembayaran}/edit', [\App\Http\Controllers\PembayaranPranotaUangJalanBongkaranController::class, 'edit'])
                    ->name('edit')
                    ->middleware('can:pembayaran-pranota-uang-jalan-bongkaran-edit');
               Route::put('/{pembayaran}', [\App\Http\Controllers\PembayaranPranotaUangJalanBongkaranController::class, 'update'])
                    ->name('update')
                    ->middleware('can:pembayaran-pranota-uang-jalan-bongkaran-edit');
               Route::delete('/{pembayaran}', [\App\Http\Controllers\PembayaranPranotaUangJalanBongkaranController::class, 'destroy'])
                    ->name('destroy')
                    ->middleware('can:pembayaran-pranota-uang-jalan-bongkaran-delete');
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

        // OB Bongkar (Operasi Bongkar) - Kapal & Voyage selection
        Route::get('/ob-bongkar', [SupirDashboardController::class, 'obBongkar'])
            ->name('ob-bongkar');
        Route::post('/ob-bongkar', [SupirDashboardController::class, 'obBongkarStore'])
            ->name('ob-bongkar.store');
        
        // OB Bongkar Index - Daftar kontainer berdasarkan kapal & voyage
        Route::get('/ob-bongkar/index', [SupirDashboardController::class, 'obBongkarIndex'])
            ->name('ob-bongkar.index');
        
        // OB Bongkar Process - Proses data OB Bongkar langsung
        Route::post('/ob-bongkar/process', [SupirDashboardController::class, 'obBongkarProcess'])
            ->name('ob-bongkar.process');

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

        // Checkpoint management for surat jalan bongkaran
        Route::get('/surat-jalan-bongkaran/{id}/checkpoint', [CheckpointController::class, 'createSuratJalanBongkaran'])
            ->name('checkpoint.create-surat-jalan-bongkaran');
        Route::post('/surat-jalan-bongkaran/{id}/checkpoint', [CheckpointController::class, 'storeSuratJalanBongkaran'])
            ->name('checkpoint.store-surat-jalan-bongkaran');
        
        // API for kontainer search
        Route::get('/api/kontainer/search', [\App\Http\Controllers\Api\KontainerSearchController::class, 'search'])
            ->name('api.kontainer.search');
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

               // Import CSV via modal (new AJAX endpoint)
               Route::post('daftar-tagihan-kontainer-sewa/import-csv', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'importCsvModal'])
                    ->name('daftar-tagihan-kontainer-sewa.import-csv')
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

               // AJAX endpoint: compute DPP from pricelist (for create/edit forms)
               Route::get('daftar-tagihan-kontainer-sewa/get-pricelist', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'getPricelistForDpp'])
                    ->name('daftar-tagihan-kontainer-sewa.get_pricelist')
                    ->middleware('can:tagihan-kontainer-sewa-create');

               // Generate invoice number (must be before resource routes to avoid conflict)
               Route::get('daftar-tagihan-kontainer-sewa/generate-invoice-number', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'generateInvoiceNumber'])
                    ->name('daftar-tagihan-kontainer-sewa.generate-invoice-number')
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

               Route::get('daftar-tagihan-kontainer-sewa/get-details-by-ids', [\App\Http\Controllers\DaftarTagihanKontainerSewaController::class, 'getDetailsByIds'])
                    ->name('daftar-tagihan-kontainer-sewa.get-details-by-ids')
                    ->middleware('can:tagihan-kontainer-view');

               // Invoice Kontainer Sewa routes - menggunakan permission tagihan-kontainer-sewa
               Route::resource('invoice-tagihan-sewa', \App\Http\Controllers\InvoiceKontainerSewaController::class)
                    ->middleware('can:tagihan-kontainer-sewa-index');
               Route::delete('invoice-tagihan-sewa-bulk-delete', [\App\Http\Controllers\InvoiceKontainerSewaController::class, 'bulkDelete'])
                    ->name('invoice-tagihan-sewa.bulk-delete')
                    ->middleware('can:tagihan-kontainer-sewa-destroy');
               Route::post('invoice-tagihan-sewa-details', [\App\Http\Controllers\InvoiceKontainerSewaController::class, 'details'])
                    ->name('invoice-tagihan-sewa.details')
                    ->middleware('can:tagihan-kontainer-sewa-index');
               Route::post('invoice-tagihan-sewa-store-pranota', [\App\Http\Controllers\InvoiceKontainerSewaController::class, 'storePranotaFromInvoice'])
                    ->name('invoice-tagihan-sewa.store-pranota')
                    ->middleware('can:tagihan-kontainer-sewa-create');

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

    // OB (Operasional Bongkaran) routes
    Route::get('ob', [\App\Http\Controllers\ObController::class, 'index'])
         ->name('ob.index')
         ->middleware('can:ob-view');
    Route::get('ob/print', [\App\Http\Controllers\ObController::class, 'print'])
         ->name('ob.print')
         ->middleware('can:ob-view');
    Route::get('ob/export', [\App\Http\Controllers\ObController::class, 'exportExcel'])
         ->name('ob.export')
         ->middleware('can:ob-view');
    Route::get('ob/get-voyage-by-kapal', [\App\Http\Controllers\ObController::class, 'getVoyageByKapal'])
         ->name('ob.get-voyage-by-kapal')
         ->middleware('can:ob-view');
    Route::get('ob/get-kapal-bongkar', [\App\Http\Controllers\ObController::class, 'getKapalBongkar'])
         ->name('ob.get-kapal-bongkar')
         ->middleware('can:ob-view');
    Route::get('ob/get-kapal-muat', [\App\Http\Controllers\ObController::class, 'getKapalMuat'])
         ->name('ob.get-kapal-muat')
         ->middleware('can:ob-view');
    Route::get('ob/get-voyage-bongkar', [\App\Http\Controllers\ObController::class, 'getVoyageBongkar'])
         ->name('ob.get-voyage-bongkar')
         ->middleware('can:ob-view');
    Route::get('ob/get-voyage-muat', [\App\Http\Controllers\ObController::class, 'getVoyageMuat'])
         ->name('ob.get-voyage-muat')
         ->middleware('can:ob-view');
    Route::post('ob/select', [\App\Http\Controllers\ObController::class, 'selectShipVoyage'])
         ->name('ob.select')
         ->middleware('can:ob-view');
    Route::post('ob/mark-as-ob', [\App\Http\Controllers\ObController::class, 'markAsOB'])
         ->name('ob.mark-as-ob')
         ->middleware('can:ob-view');
    Route::post('ob/mark-as-ob-bl', [\App\Http\Controllers\ObController::class, 'markAsOBBl'])
         ->name('ob.mark-as-ob-bl')
         ->middleware('can:ob-view');
    Route::post('ob/process-tl', [\App\Http\Controllers\ObController::class, 'processTL'])
         ->name('ob.process-tl')
         ->middleware('can:ob-view');
    Route::post('ob/process-tl-bongkar', [\App\Http\Controllers\ObController::class, 'processTLBongkar'])
         ->name('ob.process-tl-bongkar')
         ->middleware('can:ob-view');
    Route::post('ob/unmark-ob', [\App\Http\Controllers\ObController::class, 'unmarkOB'])
         ->name('ob.unmark-ob')
         ->middleware('can:ob-view');
    Route::post('ob/unmark-ob-bl', [\App\Http\Controllers\ObController::class, 'unmarkOBBl'])
         ->name('ob.unmark-ob-bl')
         ->middleware('can:ob-view');
    // Clear TL (Tanda Langsung) actions - allow manual removal of TL status
    Route::post('ob/clear-tl', [\App\Http\Controllers\ObController::class, 'clearTL'])
         ->name('ob.clear-tl')
         ->middleware('can:ob-view');
    Route::post('ob/clear-tl-bl', [\App\Http\Controllers\ObController::class, 'clearTLBl'])
         ->name('ob.clear-tl-bl')
         ->middleware('can:ob-view');
    Route::post('ob/masuk-pranota', [\App\Http\Controllers\ObController::class, 'masukPranota'])
         ->name('ob.masuk-pranota')
         ->middleware('can:ob-view');
    Route::get('ob/generate-nomor-pranota', [\App\Http\Controllers\ObController::class, 'generateNomorPranota'])
         ->name('ob.generate-nomor-pranota')
         ->middleware('can:ob-view');
    Route::post('ob/save-asal-ke', [\App\Http\Controllers\ObController::class, 'saveAsalKe'])
         ->name('ob.save-asal-ke')
         ->middleware('can:ob-view');
    Route::post('ob/save-asal-ke-bulk', [\App\Http\Controllers\ObController::class, 'saveAsalKeBulk'])
         ->name('ob.save-asal-ke-bulk')
         ->middleware('can:ob-view');

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


    // OB Main Module routes (Ship and Voyage Selection)
    Route::get('ob', [\App\Http\Controllers\ObController::class, 'index'])
         ->name('ob.index')
         ->middleware('can:ob-view');
    Route::get('ob/get-voyages', [\App\Http\Controllers\ObController::class, 'getVoyages'])
         ->name('ob.get-voyages')
         ->middleware('can:ob-view');
    Route::get('ob/dashboard', [\App\Http\Controllers\ObController::class, 'dashboard'])
         ->name('ob.dashboard')
         ->middleware('can:ob-view');


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
    Route::post('pranota-kontainer-sewa/update-grand-total', [\App\Http\Controllers\PranotaTagihanKontainerSewaController::class, 'updateGrandTotal'])
         ->name('pranota-kontainer-sewa.update-grand-total')
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

// Pembayaran Pranota OB routes
Route::prefix('pembayaran-pranota-ob')->name('pembayaran-pranota-ob.')->middleware(['auth'])->group(function () {
    Route::get('/', [PembayaranPranotaObController::class, 'index'])->name('index')
         ->middleware('can:pembayaran-pranota-ob-view');
    Route::get('/select-criteria', [PembayaranPranotaObController::class, 'selectCriteria'])->name('select-criteria')
         ->middleware('can:pembayaran-pranota-ob-create');
    Route::get('/create', [PembayaranPranotaObController::class, 'create'])->name('create')
         ->middleware('can:pembayaran-pranota-ob-create');
    Route::post('/', [PembayaranPranotaObController::class, 'store'])->name('store')
         ->middleware('can:pembayaran-pranota-ob-create');
    Route::get('/{id}', [PembayaranPranotaObController::class, 'show'])->name('show')
         ->middleware('can:pembayaran-pranota-ob-view');
    Route::get('/{id}/edit', [PembayaranPranotaObController::class, 'edit'])->name('edit')
         ->middleware('can:pembayaran-pranota-ob-update');
    Route::put('/{id}', [PembayaranPranotaObController::class, 'update'])->name('update')
         ->middleware('can:pembayaran-pranota-ob-update');
    Route::delete('/{id}', [PembayaranPranotaObController::class, 'destroy'])->name('destroy')
         ->middleware('can:pembayaran-pranota-ob-delete');
});

// Additional route for pembayaran-pranota-ob print
Route::get('pembayaran-pranota-ob/{id}/print', [PembayaranPranotaObController::class, 'print'])
     ->name('pembayaran-pranota-ob.print')
     ->middleware(['auth', 'can:pembayaran-pranota-ob-view']);

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

// Pembayaran Aktivitas Lain routes
Route::prefix('pembayaran-aktivitas-lain')->name('pembayaran-aktivitas-lain.')->middleware(['auth'])->group(function () {
    Route::get('/', [PembayaranAktivitasLainController::class, 'index'])->name('index')
         ->middleware('can:pembayaran-aktivitas-lain-view');
    Route::get('/create', [PembayaranAktivitasLainController::class, 'create'])->name('create')
         ->middleware('can:pembayaran-aktivitas-lain-create');
    Route::post('/', [PembayaranAktivitasLainController::class, 'store'])->name('store')
         ->middleware('can:pembayaran-aktivitas-lain-create');
    // Invoice payment routes
    Route::post('/store-invoice', [PembayaranAktivitasLainController::class, 'storeInvoice'])->name('store-invoice')
         ->middleware('can:pembayaran-aktivitas-lain-create');
    Route::get('/{pembayaranAktivitasLain}', [PembayaranAktivitasLainController::class, 'show'])->name('show')
         ->middleware('can:pembayaran-aktivitas-lain-view');
    Route::get('/{pembayaranAktivitasLain}/edit', [PembayaranAktivitasLainController::class, 'edit'])->name('edit')
         ->middleware('can:pembayaran-aktivitas-lain-update');
    Route::put('/{pembayaranAktivitasLain}', [PembayaranAktivitasLainController::class, 'update'])->name('update')
         ->middleware('can:pembayaran-aktivitas-lain-update');
    Route::delete('/{pembayaranAktivitasLain}', [PembayaranAktivitasLainController::class, 'destroy'])->name('destroy')
         ->middleware('can:pembayaran-aktivitas-lain-delete');
    Route::post('/{pembayaranAktivitasLain}/approve', [PembayaranAktivitasLainController::class, 'approve'])->name('approve')
         ->middleware('can:pembayaran-aktivitas-lain-approve');
    Route::post('/{pembayaranAktivitasLain}/mark-as-paid', [PembayaranAktivitasLainController::class, 'markAsPaid'])->name('mark-as-paid')
         ->middleware('can:pembayaran-aktivitas-lain-approve');
    // Print routes
    Route::get('/print', [PembayaranAktivitasLainController::class, 'printIndex'])->name('print.index')
         ->middleware('can:pembayaran-aktivitas-lain-view');
    Route::get('/{pembayaranAktivitasLain}/print', [PembayaranAktivitasLainController::class, 'print'])->name('print')
         ->middleware('can:pembayaran-aktivitas-lain-view');
});

// Pembayaran Aktivitas Lainnya routes
Route::prefix('pembayaran-aktivitas-lainnya')->name('pembayaran-aktivitas-lainnya.')->middleware(['auth'])->group(function () {
    Route::get('/', [PembayaranAktivitasLainnyaController::class, 'index'])->name('index')
         ->middleware('can:pembayaran-aktivitas-lainnya-view');
    Route::get('/create', [PembayaranAktivitasLainnyaController::class, 'create'])->name('create')
         ->middleware('can:pembayaran-aktivitas-lainnya-create');
    Route::post('/', [PembayaranAktivitasLainnyaController::class, 'store'])->name('store')
         ->middleware('can:pembayaran-aktivitas-lainnya-create');
    Route::get('/{pembayaranAktivitasLainnya}', [PembayaranAktivitasLainnyaController::class, 'show'])->name('show')
         ->middleware('can:pembayaran-aktivitas-lainnya-view');
    Route::get('/{pembayaranAktivitasLainnya}/edit', [PembayaranAktivitasLainnyaController::class, 'edit'])->name('edit')
         ->middleware('can:pembayaran-aktivitas-lainnya-update');
    Route::put('/{pembayaranAktivitasLainnya}', [PembayaranAktivitasLainnyaController::class, 'update'])->name('update')
         ->middleware('can:pembayaran-aktivitas-lainnya-update');
    Route::delete('/{pembayaranAktivitasLainnya}', [PembayaranAktivitasLainnyaController::class, 'destroy'])->name('destroy')
         ->middleware('can:pembayaran-aktivitas-lainnya-delete');
    Route::post('/{pembayaranAktivitasLainnya}/approve', [PembayaranAktivitasLainnyaController::class, 'approve'])->name('approve')
         ->middleware('can:pembayaran-aktivitas-lainnya-approve');
    Route::post('/{pembayaranAktivitasLainnya}/mark-as-paid', [PembayaranAktivitasLainnyaController::class, 'markAsPaid'])->name('mark-as-paid')
         ->middleware('can:pembayaran-aktivitas-lainnya-approve');
});

// Invoice Aktivitas Lain routes
Route::prefix('invoice-aktivitas-lain')->name('invoice-aktivitas-lain.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\InvoiceAktivitasLainController::class, 'index'])->name('index')
         ->middleware('can:invoice-aktivitas-lain-view');
    Route::get('/get-next-number', [App\Http\Controllers\InvoiceAktivitasLainController::class, 'getNextInvoiceNumber'])->name('get-next-number')
         ->middleware('can:invoice-aktivitas-lain-create');
    Route::post('/bulk-delete', [App\Http\Controllers\InvoiceAktivitasLainController::class, 'bulkDelete'])->name('bulk-delete')
         ->middleware('can:invoice-aktivitas-lain-delete');
    Route::get('/create', [App\Http\Controllers\InvoiceAktivitasLainController::class, 'create'])->name('create')
         ->middleware('can:invoice-aktivitas-lain-create');
    Route::post('/', [App\Http\Controllers\InvoiceAktivitasLainController::class, 'store'])->name('store')
         ->middleware('can:invoice-aktivitas-lain-create');
    Route::get('/{invoiceAktivitasLain}', [App\Http\Controllers\InvoiceAktivitasLainController::class, 'show'])->name('show')
         ->middleware('can:invoice-aktivitas-lain-view');
    Route::get('/{invoiceAktivitasLain}/edit', [App\Http\Controllers\InvoiceAktivitasLainController::class, 'edit'])->name('edit')
         ->middleware('can:invoice-aktivitas-lain-update');
    Route::put('/{invoiceAktivitasLain}', [App\Http\Controllers\InvoiceAktivitasLainController::class, 'update'])->name('update')
         ->middleware('can:invoice-aktivitas-lain-update');
    Route::delete('/{invoiceAktivitasLain}', [App\Http\Controllers\InvoiceAktivitasLainController::class, 'destroy'])->name('destroy')
         ->middleware('can:invoice-aktivitas-lain-delete');
    Route::get('/{invoiceAktivitasLain}/print', [App\Http\Controllers\InvoiceAktivitasLainController::class, 'print'])->name('print')
         ->middleware('can:invoice-aktivitas-lain-view');
    Route::get('/{invoiceAktivitasLain}/print-listrik', [App\Http\Controllers\InvoiceAktivitasLainController::class, 'printListrik'])->name('print-listrik')
         ->middleware('can:invoice-aktivitas-lain-view');
    Route::get('/{invoiceAktivitasLain}/print-labuh-tambat', [App\Http\Controllers\InvoiceAktivitasLainController::class, 'printLabuhTambat'])->name('print-labuh-tambat')
         ->middleware('can:invoice-aktivitas-lain-view');
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
    Route::get('/export', [PembayaranObController::class, 'export'])->name('export')
         ->middleware('can:pembayaran-ob-export');
    Route::get('/create', [PembayaranObController::class, 'create'])->name('create')
         ->middleware('can:pembayaran-ob-create');
    Route::get('/generate-nomor', [PembayaranObController::class, 'generateNomorPembayaran'])->name('generate-nomor')
         ->middleware('can:pembayaran-ob-create');
    Route::get('/get-voyage-list', [PembayaranObController::class, 'getVoyageList'])->name('get-voyage-list')
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

    // API routes for Realisasi Uang Muka
    Route::get('/api/get-voyage-list', [RealisasiUangMukaController::class, 'getVoyageList']);
    Route::get('/api/get-supir-by-voyage', [RealisasiUangMukaController::class, 'getSupirByVoyage']);


});


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

    // Report Rit
    Route::get('/rit', [App\Http\Controllers\ReportRitController::class, 'index'])->name('rit.index');
    Route::get('/rit/view', [App\Http\Controllers\ReportRitController::class, 'view'])->name('rit.view');
    Route::get('/rit/print', [App\Http\Controllers\ReportRitController::class, 'print'])->name('rit.print');
    Route::get('/rit/export', [App\Http\Controllers\ReportRitController::class, 'export'])->name('rit.export');

    // Report Ongkos Truk
    Route::get('/ongkos-truk', [App\Http\Controllers\ReportOngkosTrukController::class, 'index'])->name('ongkos-truk.index');
    Route::get('/ongkos-truk/view', [App\Http\Controllers\ReportOngkosTrukController::class, 'view'])->name('ongkos-truk.view');
    Route::get('/ongkos-truk/print', [App\Http\Controllers\ReportOngkosTrukController::class, 'print'])->name('ongkos-truk.print');
    Route::get('/ongkos-truk/export', [App\Http\Controllers\ReportOngkosTrukController::class, 'export'])->name('ongkos-truk.export');

    // Report Surat Jalan
    Route::get('/surat-jalan', [App\Http\Controllers\ReportSuratJalanController::class, 'index'])->name('surat_jalan.index');
    Route::get('/surat-jalan/view', [App\Http\Controllers\ReportSuratJalanController::class, 'view'])->name('surat_jalan.view');

    // Report Pranota OB
    Route::get('/pranota-ob', [App\Http\Controllers\ReportPranotaObController::class, 'index'])->name('pranota-ob.index');
    Route::get('/pranota-ob/view', [App\Http\Controllers\ReportPranotaObController::class, 'view'])->name('pranota-ob.view');
    Route::get('/pranota-ob/print', [App\Http\Controllers\ReportPranotaObController::class, 'print'])->name('pranota-ob.print');
    Route::get('/pranota-ob/export', [App\Http\Controllers\ReportPranotaObController::class, 'export'])->name('pranota-ob.export');

    // Manifest
    Route::get('manifests/select-ship', [App\Http\Controllers\ManifestController::class, 'selectShip'])->name('manifests.select-ship');
    Route::get('manifests/download-template', [App\Http\Controllers\ManifestController::class, 'downloadTemplate'])->name('manifests.download-template');
    Route::get('manifests/download-bulk-template', [App\Http\Controllers\ManifestController::class, 'downloadBulkTemplate'])->name('manifests.download-bulk-template');
    Route::post('manifests/import', [App\Http\Controllers\ManifestController::class, 'import'])->name('manifests.import');
    Route::post('manifests/bulk-import', [App\Http\Controllers\ManifestController::class, 'bulkImport'])->name('manifests.bulk-import');
    Route::post('manifests/auto-update-nomor-urut', [App\Http\Controllers\ManifestController::class, 'autoUpdateNomorUrut'])->name('manifests.auto-update-nomor-urut');
    Route::post('manifests/{id}/update-nomor-bl', [App\Http\Controllers\ManifestController::class, 'updateNomorBl'])->name('manifests.update-nomor-bl');
    Route::post('manifests/{id}/update-nomor-urut', [App\Http\Controllers\ManifestController::class, 'updateNomorUrut'])->name('manifests.update-nomor-urut');
    Route::get('manifests/export', [App\Http\Controllers\ManifestController::class, 'export'])->name('manifests.export');
    Route::resource('manifests', App\Http\Controllers\ManifestController::class);
});

// API Routes for AJAX calls (no middleware needed for these specific routes)
Route::get('/api/manifests/voyages/{namaKapal}', [App\Http\Controllers\ManifestController::class, 'getVoyagesByShip']);

// ═══════════════════════════════════════════════════════════════════════
// 📊 AUDIT LOG ROUTES - Universal audit trail system
// ═══════════════════════════════════════════════════════════════════════

Route::middleware(['auth', \App\Http\Middleware\EnsureKaryawanPresent::class, \App\Http\Middleware\EnsureUserApproved::class])->group(function () {
    // Audit Log routes
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/{id}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    Route::post('audit-logs/model', [AuditLogController::class, 'getModelAuditLogs'])->name('audit-logs.model');
    Route::get('audit-logs/export/csv', [AuditLogController::class, 'export'])->name('audit-logs.export');



    // 🛒 Belanja Amprahan (simple CRUD)
    Route::resource('belanja-amprahan', \App\Http\Controllers\BelanjaAmprahanController::class)
          ->names('belanja-amprahan')
          ->middleware([
               'index' => 'can:belanja-amprahan-view',
               'show' => 'can:belanja-amprahan-view',
               'create' => 'can:belanja-amprahan-create',
               'store' => 'can:belanja-amprahan-create',
               'edit' => 'can:belanja-amprahan-update',
               'update' => 'can:belanja-amprahan-update',
               'destroy' => 'can:belanja-amprahan-delete'
          ]);


    // 📊 Prospek Management - Read Only
    Route::get('prospek', [ProspekController::class, 'index'])->name('prospek.index')
         ->middleware('can:prospek-view');

    Route::get('prospek/pilih-tujuan', [ProspekController::class, 'pilihTujuan'])->name('prospek.pilih-tujuan')
         ->middleware('can:prospek-edit');

    // Export prospek filtered list to Excel
    Route::get('prospek/export-excel', [ProspekController::class, 'exportExcel'])->name('prospek.export-excel')
         ->middleware('can:prospek-view');

    Route::get('prospek/proses-naik-kapal', [ProspekController::class, 'prosesNaikKapal'])->name('prospek.proses-naik-kapal')
         ->middleware('can:prospek-edit');

    Route::post('prospek/proses-naik-kapal', [ProspekController::class, 'prosesNaikKapal'])->name('prospek.proses-naik-kapal-batch')
         ->middleware('can:prospek-edit');

    Route::post('prospek/scan-surat-jalan', [ProspekController::class, 'scanSuratJalan'])->name('prospek.scan-surat-jalan')
         ->middleware('can:prospek-edit');

    Route::post('prospek/gabungkan-lcl', [ProspekController::class, 'gabungkanLCL'])->name('prospek.gabungkan-lcl')
         ->middleware('can:prospek-edit');

    Route::post('prospek/execute-naik-kapal', [ProspekController::class, 'executeNaikKapal'])->name('prospek.execute-naik-kapal')
         ->middleware('can:prospek-edit');

    Route::get('prospek/get-voyage-by-kapal', [ProspekController::class, 'getVoyageByKapal'])->name('prospek.get-voyage-by-kapal')
         ->middleware('can:prospek-view');

    // NOTE: Route dengan parameter harus di bawah route spesifik
    Route::get('prospek/{prospek}', [ProspekController::class, 'show'])->name('prospek.show')
                ->middleware('can:prospek-view');

    // Route untuk edit prospek
    Route::get('prospek/{prospek}/edit', [ProspekController::class, 'edit'])->name('prospek.edit')
         ->middleware('can:prospek-edit');

    // Route untuk update prospek
    Route::put('prospek/{prospek}', [ProspekController::class, 'update'])->name('prospek.update')
         ->middleware('can:prospek-edit');

    // Route untuk update seal (inline edit)
    Route::patch('prospek/{prospek}/update-seal', [ProspekController::class, 'updateSeal'])->name('prospek.update-seal')
         ->middleware('can:prospek-edit');

    // Route untuk update status
    Route::patch('prospek/{prospek}/update-status', [ProspekController::class, 'updateStatus'])->name('prospek.update-status')
         ->middleware('can:prospek-edit');

    // Route untuk sinkronisasi prospek dari surat jalan
    Route::post('prospek/{prospek}/sync-from-surat-jalan', [ProspekController::class, 'syncFromSuratJalan'])->name('prospek.sync-from-surat-jalan')
         ->middleware('can:prospek-edit');

    // Route untuk delete prospek
    Route::delete('prospek/{prospek}', [ProspekController::class, 'destroy'])->name('prospek.destroy')
         ->middleware('can:prospek-delete');

          // 🚢 Naik Kapal Management
          Route::get('naik-kapal/select', [NaikKapalController::class, 'select'])
               ->name('naik-kapal.select')
               ->middleware('can:prospek-view');
               
          Route::get('naik-kapal/get-voyages', [NaikKapalController::class, 'getVoyagesByKapal'])
               ->name('naik-kapal.get-voyages')
               ->middleware('can:prospek-view');
               
          Route::get('naik-kapal/print', [NaikKapalController::class, 'print'])
               ->name('naik-kapal.print')
               ->middleware('can:prospek-view');
               
          Route::get('naik-kapal/download-template', [NaikKapalController::class, 'downloadTemplate'])
               ->name('naik-kapal.download.template')
               ->middleware('can:prospek-view');
               
          Route::post('naik-kapal/bulk-action', [NaikKapalController::class, 'bulkAction'])
               ->name('naik-kapal.bulk-action')
               ->middleware('can:prospek-edit');
               
          Route::get('naik-kapal/export', [NaikKapalController::class, 'export'])
               ->name('naik-kapal.export')
               ->middleware('can:prospek-view');
               
          Route::patch('naik-kapal/{naikKapal}/update-size', [NaikKapalController::class, 'updateSize'])
               ->name('naik-kapal.update-size')
               ->middleware('can:prospek-edit');

          Route::resource('naik-kapal', NaikKapalController::class)
                     ->middleware('can:prospek-edit');

          // BL (Bill of Lading) Management
          Route::get('bl/get-voyage-by-kapal', [\App\Http\Controllers\BlController::class, 'getVoyageByKapal'])
               ->name('bl.get-voyage-by-kapal')
               ->middleware('can:bl-view');
               
          Route::get('bl/download-template', [\App\Http\Controllers\BlController::class, 'downloadTemplate'])
               ->name('bl.download.template')
               ->middleware('can:bl-view');
               
          Route::get('bl/export', [\App\Http\Controllers\BlController::class, 'export'])
               ->name('bl.export')
               ->middleware('can:bl-view');
               
          Route::get('bl/get-ships', [\App\Http\Controllers\BlController::class, 'getShips'])
               ->name('bl.get-ships')
               ->middleware('can:bl-view');
               
          Route::get('bl/get-voyages', [\App\Http\Controllers\BlController::class, 'getVoyages'])
               ->name('bl.get-voyages')
               ->middleware('can:bl-view');
               
          Route::post('bl/import', [\App\Http\Controllers\BlController::class, 'import'])
               ->name('bl.import')
               ->middleware('can:bl-create');
               
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
               
          Route::patch('bl/{bl}/status-bongkar', [\App\Http\Controllers\BlController::class, 'updateStatusBongkar'])->name('bl.update-status-bongkar')
               ->middleware('can:bl-edit');
               
          Route::patch('bl/{bl}/size-kontainer', [\App\Http\Controllers\BlController::class, 'updateSizeKontainer'])->name('bl.update-size-kontainer')
               ->middleware('can:bl-edit');
               
          // BL Bulk Operations
          Route::post('bl/validate-containers', [\App\Http\Controllers\BlController::class, 'validateContainers'])->name('bl.validate-containers')
               ->middleware('can:bl-edit');
               
          Route::post('bl/get-pt-pengirim', [\App\Http\Controllers\BlController::class, 'getPtPengirim'])->name('bl.get-pt-pengirim')
               ->middleware('can:bl-edit');
               
          Route::post('bl/bulk-split', [\App\Http\Controllers\BlController::class, 'bulkSplit'])->name('bl.bulk-split')
               ->middleware('can:bl-edit');
               
          Route::get('bl-api/by-kapal-voyage', [\App\Http\Controllers\BlController::class, 'getByKapalVoyage'])->name('bl.api.by-kapal-voyage')
               ->middleware('can:bl-view');
               
          Route::delete('bl/{bl}', [\App\Http\Controllers\BlController::class, 'destroy'])->name('bl.destroy')
               ->middleware('can:bl-delete');

          // ═══════════════════════════════════════════════════════════════════════
          // 🔧 STOCK BAN MANAGEMENT
          // ═══════════════════════════════════════════════════════════════════════
          Route::get('stock-ban', [\App\Http\Controllers\StockBanController::class, 'index'])
               ->name('stock-ban.index')
               ->middleware('can:stock-ban-view');
          Route::get('stock-ban/create', [\App\Http\Controllers\StockBanController::class, 'create'])
               ->name('stock-ban.create')
               ->middleware('can:stock-ban-create');
          Route::post('stock-ban', [\App\Http\Controllers\StockBanController::class, 'store'])
               ->name('stock-ban.store')
               ->middleware('can:stock-ban-create');
               
          // Custom Actions for StockBan
          Route::put('stock-ban/{stock_ban}/masak', [\App\Http\Controllers\StockBanController::class, 'masak'])
               ->name('stock-ban.masak')
               ->middleware('can:stock-ban-update');
          Route::post('stock-ban/bulk-masak', [\App\Http\Controllers\StockBanController::class, 'bulkMasak'])
               ->name('stock-ban.bulk-masak')
               ->middleware('can:stock-ban-update');
          Route::post('stock-ban/{stock_ban}/use', [\App\Http\Controllers\StockBanController::class, 'storeUsage'])
               ->name('stock-ban.use')
               ->middleware('can:stock-ban-update');


          // Ban Dalam Routes
          Route::get('stock-ban/ban-dalam/{id}/use', [\App\Http\Controllers\StockBanController::class, 'useBanDalam'])
               ->name('stock-ban.ban-dalam.use')
               ->middleware('can:stock-ban-update');
          Route::post('stock-ban/ban-dalam/{id}/use', [\App\Http\Controllers\StockBanController::class, 'storeUsageBanDalam'])
               ->name('stock-ban.ban-dalam.store-usage')
               ->middleware('can:stock-ban-update');
          Route::get('stock-ban/ban-dalam/{id}', [\App\Http\Controllers\StockBanController::class, 'showBanDalam'])
               ->name('stock-ban.ban-dalam.show')
               ->middleware('can:stock-ban-view');

          Route::get('stock-ban/{stock_ban}', [\App\Http\Controllers\StockBanController::class, 'show'])
               ->name('stock-ban.show')
               ->middleware('can:stock-ban-view');
          Route::get('stock-ban/{stock_ban}/edit', [\App\Http\Controllers\StockBanController::class, 'edit'])
               ->name('stock-ban.edit')
               ->middleware('can:stock-ban-update');
          Route::put('stock-ban/{stock_ban}', [\App\Http\Controllers\StockBanController::class, 'update'])
               ->name('stock-ban.update')
               ->middleware('can:stock-ban-update');
          Route::delete('stock-ban/{stock_ban}', [\App\Http\Controllers\StockBanController::class, 'destroy'])
               ->name('stock-ban.destroy')
               ->middleware('can:stock-ban-delete');

           // ═══════════════════════════════════════════════════════════════════════
           // 👥 KARYAWAN TIDAK TETAP MANAGEMENT
           // ═══════════════════════════════════════════════════════════════════════
           Route::get('karyawan-tidak-tetap/template', [\App\Http\Controllers\KaryawanTidakTetapController::class, 'downloadTemplate'])
                ->name('karyawan-tidak-tetap.template')
                ->middleware('can:karyawan-tidak-tetap-create');

           Route::get('karyawan-tidak-tetap/{karyawan_tidak_tetap}/print', [\App\Http\Controllers\KaryawanTidakTetapController::class, 'printSingle'])
                ->name('karyawan-tidak-tetap.print-single')
                ->middleware('can:karyawan-tidak-tetap-view');

           Route::post('karyawan-tidak-tetap/import', [\App\Http\Controllers\KaryawanTidakTetapController::class, 'import'])
                ->name('karyawan-tidak-tetap.import')
                ->middleware('can:karyawan-tidak-tetap-create');

           Route::get('karyawan-tidak-tetap', [\App\Http\Controllers\KaryawanTidakTetapController::class, 'index'])
                ->name('karyawan-tidak-tetap.index')
                ->middleware('can:karyawan-tidak-tetap-view');
           Route::get('karyawan-tidak-tetap/create', [\App\Http\Controllers\KaryawanTidakTetapController::class, 'create'])
                ->name('karyawan-tidak-tetap.create')
                ->middleware('can:karyawan-tidak-tetap-create');
           Route::post('karyawan-tidak-tetap', [\App\Http\Controllers\KaryawanTidakTetapController::class, 'store'])
                ->name('karyawan-tidak-tetap.store')
                ->middleware('can:karyawan-tidak-tetap-create');
           Route::get('karyawan-tidak-tetap/{karyawan_tidak_tetap}', [\App\Http\Controllers\KaryawanTidakTetapController::class, 'show'])
                ->name('karyawan-tidak-tetap.show')
                ->middleware('can:karyawan-tidak-tetap-view');
           Route::get('karyawan-tidak-tetap/{karyawan_tidak_tetap}/edit', [\App\Http\Controllers\KaryawanTidakTetapController::class, 'edit'])
                ->name('karyawan-tidak-tetap.edit')
                ->middleware('can:karyawan-tidak-tetap-update');
           Route::put('karyawan-tidak-tetap/{karyawan_tidak_tetap}', [\App\Http\Controllers\KaryawanTidakTetapController::class, 'update'])
                ->name('karyawan-tidak-tetap.update')
                ->middleware('can:karyawan-tidak-tetap-update');
           Route::delete('karyawan-tidak-tetap/{karyawan_tidak_tetap}', [\App\Http\Controllers\KaryawanTidakTetapController::class, 'destroy'])
                 ->name('karyawan-tidak-tetap.destroy')
                 ->middleware('can:karyawan-tidak-tetap-delete');
    Route::post('tanda-terima-lcl/sync-prospek', [TandaTerimaLclController::class, 'syncProspek'])->name('tanda-terima-lcl.sync-prospek')
         ->middleware('can:tanda-terima-lcl-edit');

           // ═══════════════════════════════════════════════════════════════════════
           // 📄 MASTER DOKUMEN PERIJINAN KAPAL
           // ═══════════════════════════════════════════════════════════════════════
           Route::get('master-dokumen-perijinan-kapal', [\App\Http\Controllers\DokumenPerijinanKapalController::class, 'index'])
                ->name('master-dokumen-perijinan-kapal.index')
                ->middleware('can:master-dokumen-perijinan-kapal-view');
           Route::get('master-dokumen-perijinan-kapal/create', [\App\Http\Controllers\DokumenPerijinanKapalController::class, 'create'])
                ->name('master-dokumen-perijinan-kapal.create')
                ->middleware('can:master-dokumen-perijinan-kapal-create');
           Route::post('master-dokumen-perijinan-kapal', [\App\Http\Controllers\DokumenPerijinanKapalController::class, 'store'])
                ->name('master-dokumen-perijinan-kapal.store')
                ->middleware('can:master-dokumen-perijinan-kapal-create');
           Route::get('master-dokumen-perijinan-kapal/{master_dokumen_perijinan_kapal}', [\App\Http\Controllers\DokumenPerijinanKapalController::class, 'show'])
                ->name('master-dokumen-perijinan-kapal.show')
                ->middleware('can:master-dokumen-perijinan-kapal-view');
           Route::get('master-dokumen-perijinan-kapal/{master_dokumen_perijinan_kapal}/edit', [\App\Http\Controllers\DokumenPerijinanKapalController::class, 'edit'])
                ->name('master-dokumen-perijinan-kapal.edit')
                ->middleware('can:master-dokumen-perijinan-kapal-update');
           Route::put('master-dokumen-perijinan-kapal/{master_dokumen_perijinan_kapal}', [\App\Http\Controllers\DokumenPerijinanKapalController::class, 'update'])
                ->name('master-dokumen-perijinan-kapal.update')
                ->middleware('can:master-dokumen-perijinan-kapal-update');
           Route::delete('master-dokumen-perijinan-kapal/{master_dokumen_perijinan_kapal}', [\App\Http\Controllers\DokumenPerijinanKapalController::class, 'destroy'])
                ->name('master-dokumen-perijinan-kapal.destroy')
                ->middleware('can:master-dokumen-perijinan-kapal-delete');

});

