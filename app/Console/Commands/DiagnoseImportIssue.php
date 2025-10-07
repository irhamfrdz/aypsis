<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

class DiagnoseImportIssue extends Command
{
    protected $signature = 'diagnose:import-issue';
    protected $description = 'Diagnose import file issues';

    public function handle()
    {
        $this->info('🔍 Diagnosing Import File Issues...');

        try {
            // Check admin user for testing
            $user = User::where('username', 'admin')->first();
            if (!$user) {
                $this->error("❌ Admin user not found");
                return;
            }

            $this->info("✅ Testing with user: {$user->username}");

            // Check stock kontainer permissions
            $this->info("\n📋 Checking Stock Kontainer Permissions:");
            $stockPermissions = $user->permissions()->where('name', 'like', '%stock-kontainer%')->get();
            if ($stockPermissions->count() > 0) {
                foreach ($stockPermissions as $permission) {
                    $this->info("  ✅ {$permission->name}");
                }
            } else {
                $this->error("  ❌ No stock kontainer permissions found");
            }

            // Check specific import permission
            $importPermission = $user->permissions()->where('name', 'master-stock-kontainer-create')->first();
            if ($importPermission) {
                $this->info("✅ Import permission (master-stock-kontainer-create): GRANTED");
            } else {
                $this->error("❌ Import permission (master-stock-kontainer-create): DENIED");
            }

            // Check routes
            $this->info("\n🛣️ Checking Routes:");
            $importRoute = Route::getRoutes()->getByName('master.stock-kontainer.import');
            $templateRoute = Route::getRoutes()->getByName('master.stock-kontainer.template');

            if ($importRoute) {
                $this->info("✅ Import route exists: " . $importRoute->uri());
            } else {
                $this->error("❌ Import route not found");
            }

            if ($templateRoute) {
                $this->info("✅ Template route exists: " . $templateRoute->uri());
            } else {
                $this->error("❌ Template route not found");
            }

            // Check controller
            $this->info("\n🎛️ Checking Controller:");
            if (class_exists('App\Http\Controllers\StockKontainerImportController')) {
                $this->info("✅ StockKontainerImportController exists");

                $controller = new \App\Http\Controllers\StockKontainerImportController();
                if (method_exists($controller, 'import')) {
                    $this->info("✅ import() method exists");
                } else {
                    $this->error("❌ import() method not found");
                }
            } else {
                $this->error("❌ StockKontainerImportController not found");
            }

            // Check file upload settings
            $this->info("\n📁 Checking PHP File Upload Settings:");
            $this->info("  file_uploads: " . (ini_get('file_uploads') ? 'ON' : 'OFF'));
            $this->info("  upload_max_filesize: " . ini_get('upload_max_filesize'));
            $this->info("  post_max_size: " . ini_get('post_max_size'));
            $this->info("  max_execution_time: " . ini_get('max_execution_time'));
            $this->info("  memory_limit: " . ini_get('memory_limit'));

            // Test CSV format
            $this->info("\n📄 Expected CSV Format:");
            $this->info("  Header: Nomor Kontainer;Ukuran;Tipe Kontainer;Status;Tahun Pembuatan;Keterangan");
            $this->info("  Example: CONT001;20ft;Dry;available;2020;Kontainer test");

            $this->info("\n🎯 Common Issues & Solutions:");
            $this->info("  1. Permission denied → Contact admin to grant 'master-stock-kontainer-create'");
            $this->info("  2. Wrong CSV format → Download template first");
            $this->info("  3. File too large → Max 5MB, check file size");
            $this->info("  4. Wrong delimiter → Use semicolon (;) not comma");
            $this->info("  5. Invalid file type → Only .csv files allowed");

        } catch (\Exception $e) {
            $this->error("❌ Diagnosis failed: " . $e->getMessage());
        }
    }
}
