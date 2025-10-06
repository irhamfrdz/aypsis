<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Creating aktivitas_lainnya table...\n";

    Schema::create('aktivitas_lainnya', function (Blueprint $table) {
        $table->id();
        $table->string('nomor_aktivitas')->unique();
        $table->date('tanggal_aktivitas');
        $table->text('deskripsi_aktivitas');
        $table->string('kategori')->default('lainnya');
        $table->foreignId('vendor_id')->nullable()->constrained('vendor_bengkel')->onDelete('set null');
        $table->decimal('nominal', 15, 2);
        $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'paid'])->default('draft');
        $table->text('keterangan')->nullable();
        $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
        $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
        $table->timestamp('approved_at')->nullable();
        $table->timestamps();

        $table->index(['status', 'kategori']);
        $table->index('tanggal_aktivitas');
    });

    echo "Creating pembayaran_aktivitas_lainnya table...\n";

    Schema::create('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
        $table->id();
        $table->string('nomor_pembayaran')->unique();
        $table->date('tanggal_pembayaran');
        $table->decimal('total_nominal', 15, 2);
        $table->enum('metode_pembayaran', ['cash', 'transfer', 'check', 'credit_card']);
        $table->string('referensi_pembayaran')->nullable();
        $table->text('keterangan')->nullable();
        $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
        $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
        $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
        $table->timestamp('approved_at')->nullable();
        $table->timestamps();

        $table->index(['status', 'tanggal_pembayaran']);
    });

    echo "Creating pembayaran_aktivitas_lainnya_items table...\n";

    Schema::create('pembayaran_aktivitas_lainnya_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pembayaran_id')->constrained('pembayaran_aktivitas_lainnya')->onDelete('cascade');
        $table->foreignId('aktivitas_id')->constrained('aktivitas_lainnya')->onDelete('cascade');
        $table->decimal('nominal', 15, 2);
        $table->text('keterangan')->nullable();
        $table->timestamps();

        $table->unique(['pembayaran_id', 'aktivitas_id'], 'pay_akt_lain_unique');
        $table->index('pembayaran_id');
        $table->index('aktivitas_id');
    });

    echo "All tables created successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
