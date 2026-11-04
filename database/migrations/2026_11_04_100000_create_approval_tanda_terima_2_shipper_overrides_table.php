<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_tanda_terima_2_shipper_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 4);
            $table->unsignedBigInteger('source_id');
            $table->string('context', 40)->default('main');
            $table->foreignId('shipper_jb_id')->constrained('shipper_consignees')->restrictOnDelete();
            $table->json('fields');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['source_type', 'source_id', 'context'], 'approval_tt2_shipper_override_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_tanda_terima_2_shipper_overrides');
    }
};
