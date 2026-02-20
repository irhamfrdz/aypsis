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
        Schema::table('vendor_kontainer_sewas', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_kontainer_sewas', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('vendor_kontainer_sewas', 'npwp')) {
                $table->string('npwp')->nullable()->after('name');
            }
            if (!Schema::hasColumn('vendor_kontainer_sewas', 'tax_ppn_percent')) {
                $table->decimal('tax_ppn_percent', 5, 2)->default(11.00)->after('npwp');
            }
            if (!Schema::hasColumn('vendor_kontainer_sewas', 'tax_pph_percent')) {
                $table->decimal('tax_pph_percent', 5, 2)->default(2.00)->after('tax_ppn_percent');
            }

            // Drop old columns if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('vendor_kontainer_sewas', 'kode')) {
                $columnsToDrop[] = 'kode';
            }
            if (Schema::hasColumn('vendor_kontainer_sewas', 'nama_vendor')) {
                $columnsToDrop[] = 'nama_vendor';
            }
            if (Schema::hasColumn('vendor_kontainer_sewas', 'status')) {
                $columnsToDrop[] = 'status';
            }
            if (Schema::hasColumn('vendor_kontainer_sewas', 'catatan')) {
                $columnsToDrop[] = 'catatan';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_kontainer_sewas', function (Blueprint $table) {
            $table->string('kode')->nullable();
            $table->string('nama_vendor')->nullable();
            $table->string('status')->nullable();
            $table->text('catatan')->nullable();

            $table->dropColumn(['name', 'npwp', 'tax_ppn_percent', 'tax_pph_percent']);
        });
    }
};
