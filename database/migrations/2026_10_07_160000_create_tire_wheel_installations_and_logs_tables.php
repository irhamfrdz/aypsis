<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('tire_wheel_installations')) {
            Schema::create('tire_wheel_installations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('mobil_id')->nullable();
                $table->unsignedBigInteger('alat_berat_id')->nullable();
                $table->string('category', 50)->nullable();
                $table->string('wheel_id', 50);
                $table->string('wheel_code', 50)->nullable();
                $table->string('wheel_name', 150)->nullable();
                $table->unsignedBigInteger('stock_ban_id');
                $table->string('nomor_seri', 100)->nullable();
                $table->string('merk', 100)->nullable();
                $table->string('ukuran', 100)->nullable();
                $table->string('kondisi', 50)->nullable();
                $table->boolean('is_borrowed')->default(0);
                $table->unsignedBigInteger('donor_unit_id')->nullable();
                $table->string('donor_unit_name', 150)->nullable();
                $table->string('donor_category', 50)->nullable();
                $table->dateTime('borrowed_at')->nullable();
                $table->dateTime('installed_at')->useCurrent();
                $table->timestamp('created_at')->useCurrent()->nullable();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->nullable();

                $table->index('mobil_id', 'idx_twi_mobil');
                $table->index('alat_berat_id', 'idx_twi_alat');
                $table->index('stock_ban_id', 'idx_twi_stock_ban');
                $table->index('wheel_id', 'idx_twi_wheel');
                $table->unique(['mobil_id', 'wheel_id'], 'uq_twi_mobil_wheel');
                $table->unique(['alat_berat_id', 'wheel_id'], 'uq_twi_alat_wheel');
            });
        }

        if (! Schema::hasTable('tire_installation_logs')) {
            Schema::create('tire_installation_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('mobil_id')->nullable();
                $table->unsignedBigInteger('alat_berat_id')->nullable();
                $table->string('category', 50)->nullable();
                $table->string('wheel_id', 50);
                $table->string('wheel_code', 50)->nullable();
                $table->unsignedBigInteger('stock_ban_id');
                $table->string('nomor_seri', 100)->nullable();
                $table->enum('action', ['pasang', 'copot', 'tukar', 'kembalikan_pinjaman', 'lepas_semua']);
                $table->boolean('is_borrowed')->default(0);
                $table->unsignedBigInteger('donor_unit_id')->nullable();
                $table->string('donor_unit_name', 150)->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->useCurrent()->nullable();

                $table->index('mobil_id', 'idx_til_mobil');
                $table->index('alat_berat_id', 'idx_til_alat');
                $table->index('stock_ban_id', 'idx_til_stock_ban');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tire_installation_logs');
        Schema::dropIfExists('tire_wheel_installations');
    }
};
