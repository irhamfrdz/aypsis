<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_tanda_terima_2_fcl_shippers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanda_terima_id')->constrained('tanda_terimas')->cascadeOnDelete();
            $table->foreignId('shipper_jb_id')->constrained('shipper_consignees')->restrictOnDelete();
            $table->json('manifest_goods')->nullable();
            $table->json('goods')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tanda_terima_id', 'shipper_jb_id'], 'approval_tt2_fcl_shipper_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_tanda_terima_2_fcl_shippers');
    }
};
