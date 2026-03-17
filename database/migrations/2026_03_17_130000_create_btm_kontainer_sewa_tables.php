<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('btm_sewa_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('btm_sewa_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('btm_sewa_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('btm_sewa_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_number')->unique();
            $table->foreignId('vendor_id')->constrained('btm_sewa_vendors')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('type_id')->constrained('btm_sewa_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('size_id')->constrained('btm_sewa_sizes')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('btm_sewa_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('btm_sewa_vendors')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('type_id')->constrained('btm_sewa_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('size_id')->constrained('btm_sewa_sizes')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('monthly_rate', 15, 2)->default(0);
            $table->decimal('daily_rate', 15, 2)->default(0);
            $table->date('start_date');
            $table->timestamps();

            $table->index(['vendor_id', 'type_id', 'size_id', 'start_date'], 'btm_sewa_rates_lookup_idx');
        });

        Schema::create('btm_sewa_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('unit_number');
            $table->date('date_in');
            $table->date('date_out')->nullable();
            $table->enum('billing_mode', ['B', 'H'])->default('B');
            $table->timestamps();

            $table->index(['unit_number', 'date_in'], 'btm_sewa_trans_unit_in_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btm_sewa_transactions');
        Schema::dropIfExists('btm_sewa_rates');
        Schema::dropIfExists('btm_sewa_units');
        Schema::dropIfExists('btm_sewa_sizes');
        Schema::dropIfExists('btm_sewa_types');
        Schema::dropIfExists('btm_sewa_vendors');
    }
};
