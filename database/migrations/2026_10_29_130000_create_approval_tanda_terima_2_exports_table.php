<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_tanda_terima_2_exports', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 4);
            $table->unsignedBigInteger('source_id');
            $table->foreignId('exported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('exported_at');
            $table->timestamps();

            $table->unique(['source_type', 'source_id'], 'approval_tt2_exports_source_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_tanda_terima_2_exports');
    }
};
