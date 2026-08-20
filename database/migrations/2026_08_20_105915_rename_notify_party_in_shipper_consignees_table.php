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
        Schema::table('shipper_consignees', function (Blueprint $table) {
            $table->renameColumn('notify_party', 'notify_party_consignee');
            $table->renameColumn('npwp_notify_party', 'npwp_notify_party_consignee');
            $table->renameColumn('alamat_notify_party', 'alamat_notify_party_consignee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipper_consignees', function (Blueprint $table) {
            $table->renameColumn('notify_party_consignee', 'notify_party');
            $table->renameColumn('npwp_notify_party_consignee', 'npwp_notify_party');
            $table->renameColumn('alamat_notify_party_consignee', 'alamat_notify_party');
        });
    }
};
