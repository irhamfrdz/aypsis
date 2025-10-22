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
        Schema::create('karyawan_family_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->string('hubungan')->nullable(); // Relationship (Suami, Istri, Anak, etc.)
            $table->string('nama'); // Name
            $table->date('tanggal_lahir')->nullable(); // Birth date
            $table->text('alamat')->nullable(); // Address
            $table->string('no_telepon', 20)->nullable(); // Phone number
            $table->string('nik_ktp', 16)->nullable(); // NIK/KTP number
            $table->string('no_bpjs_kesehatan', 50)->nullable(); // BPJS Health number
            $table->string('faskes')->nullable(); // Healthcare facility
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('cascade');

            // Index for better performance
            $table->index('karyawan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan_family_members');
    }
};
