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
        Schema::create('gps_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobil_id')->nullable()->constrained('mobils')->onDelete('cascade');
            $table->string('imei_gps')->index();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->integer('speed')->default(0);
            $table->string('status')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamp('recorded_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gps_histories');
    }
};
