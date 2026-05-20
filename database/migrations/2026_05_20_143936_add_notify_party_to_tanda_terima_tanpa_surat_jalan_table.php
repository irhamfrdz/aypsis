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
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->string('notify_party')->nullable()->after('alamat_penerima');
            $table->text('alamat_notify_party')->nullable()->after('notify_party');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->dropColumn(['notify_party', 'alamat_notify_party']);
        });
    }
};
