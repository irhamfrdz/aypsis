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
        Schema::table('users', function (Blueprint $table) {
            // Change default status from 'active' to 'pending'
            $table->string('status')->default('pending')->change();

            // Add approved_by and approved_at fields
            $table->unsignedBigInteger('approved_by')->nullable()->after('registration_reason');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Add foreign key for approved_by
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approved_by', 'approved_at']);
            $table->string('status')->default('active')->change();
        });
    }
};
