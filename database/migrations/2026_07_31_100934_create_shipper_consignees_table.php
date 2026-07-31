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
        Schema::create('shipper_consignees', function (Blueprint $table) {
            $table->id();
            $table->string('telepon')->nullable();
            $table->string('hs_code')->nullable();
            $table->string('commodity')->nullable();
            $table->string('alamat_email')->nullable();
            $table->string('nitku_shipper')->nullable();
            $table->string('shipper')->nullable();
            $table->text('alamat_shipper')->nullable();
            $table->string('npwp_shipper')->nullable();
            $table->string('consignee')->nullable();
            $table->text('alamat_consignee')->nullable();
            $table->string('npwp_consignee')->nullable();
            $table->string('notify_party')->nullable();
            $table->text('alamat_notify_party')->nullable();
            $table->string('npwp_notify_party')->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('nitku_consignee')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipper_consignees');
    }
};
