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
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->unsignedBigInteger('penerima_id')->nullable()->after('order_id');
            $table->unsignedBigInteger('notify_party_id')->nullable()->after('penerima_id');
            $table->text('alamat_penerima')->nullable()->after('notify_party_id');
            
            $table->foreign('penerima_id')->references('id')->on('penerimas')->onDelete('set null');
            $table->foreign('notify_party_id')->references('id')->on('penerimas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropForeign(['penerima_id']);
            $table->dropForeign(['notify_party_id']);
            $table->dropColumn(['penerima_id', 'notify_party_id', 'alamat_penerima']);
        });
    }
};
