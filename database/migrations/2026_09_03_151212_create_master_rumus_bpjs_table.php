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
        Schema::create('master_rumus_bpjs', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['jkn', 'jamsostek']);
            $table->string('group_name');
            $table->enum('tipe_rumus', ['nominal', 'persentase']);
            $table->decimal('nilai', 15, 2);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_rumus_bpjs');
    }
};
