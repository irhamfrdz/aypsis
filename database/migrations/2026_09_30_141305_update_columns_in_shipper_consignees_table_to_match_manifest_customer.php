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
            if (Schema::hasColumn('shipper_consignees', 'delivery_address') && ! Schema::hasColumn('shipper_consignees', 'delivery_address_contact_person')) {
                $table->renameColumn('delivery_address', 'delivery_address_contact_person');
            }
        });

        Schema::table('shipper_consignees', function (Blueprint $table) {
            $columnsToDrop = [
                'telepon',
                'hs_code',
                'commodity',
                'alamat_email',
                'nitku_shipper',
                'nitku_consignee',
                'ip_bp_kawasan',
                'npwp_consignee_16_digit',
                'contact_person',
            ];

            $existingColumnsToDrop = array_filter($columnsToDrop, function ($col) {
                return Schema::hasColumn('shipper_consignees', $col);
            });

            if (! empty($existingColumnsToDrop)) {
                $table->dropColumn(array_values($existingColumnsToDrop));
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipper_consignees', function (Blueprint $table) {
            if (Schema::hasColumn('shipper_consignees', 'delivery_address_contact_person') && ! Schema::hasColumn('shipper_consignees', 'delivery_address')) {
                $table->renameColumn('delivery_address_contact_person', 'delivery_address');
            }

            if (! Schema::hasColumn('shipper_consignees', 'telepon')) {
                $table->string('telepon')->nullable();
            }
            if (! Schema::hasColumn('shipper_consignees', 'hs_code')) {
                $table->string('hs_code')->nullable();
            }
            if (! Schema::hasColumn('shipper_consignees', 'commodity')) {
                $table->string('commodity')->nullable();
            }
            if (! Schema::hasColumn('shipper_consignees', 'alamat_email')) {
                $table->string('alamat_email')->nullable();
            }
            if (! Schema::hasColumn('shipper_consignees', 'nitku_shipper')) {
                $table->string('nitku_shipper')->nullable();
            }
            if (! Schema::hasColumn('shipper_consignees', 'nitku_consignee')) {
                $table->string('nitku_consignee')->nullable();
            }
            if (! Schema::hasColumn('shipper_consignees', 'ip_bp_kawasan')) {
                $table->string('ip_bp_kawasan')->nullable();
            }
            if (! Schema::hasColumn('shipper_consignees', 'npwp_consignee_16_digit')) {
                $table->string('npwp_consignee_16_digit')->nullable();
            }
            if (! Schema::hasColumn('shipper_consignees', 'contact_person')) {
                $table->string('contact_person')->nullable();
            }
        });
    }
};
