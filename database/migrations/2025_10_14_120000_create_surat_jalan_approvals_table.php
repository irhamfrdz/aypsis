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
        // Check if table already exists
        if (!Schema::hasTable('surat_jalan_approvals')) {
            Schema::create('surat_jalan_approvals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('surat_jalan_id');
                $table->string('approval_level'); // 'tugas-1' atau 'tugas-2'
                $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->text('approval_notes')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();

                $table->foreign('surat_jalan_id')->references('id')->on('surat_jalans')->onDelete('cascade');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                
                // Unique constraint untuk mencegah duplikasi approval per level
                $table->unique(['surat_jalan_id', 'approval_level']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_approvals');
    }
};