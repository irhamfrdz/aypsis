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
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('units')->default(1)->after('unit_kontainer')->comment('Total units ordered');
            $table->integer('sisa')->default(1)->after('units')->comment('Remaining units to be processed');
            $table->enum('outstanding_status', ['pending', 'partial', 'completed'])->default('pending')->after('sisa')->comment('Outstanding status tracking');
            $table->decimal('completion_percentage', 5, 2)->default(0)->after('outstanding_status')->comment('Completion percentage');
            $table->timestamp('completed_at')->nullable()->after('completion_percentage')->comment('Completion timestamp');
            $table->json('processing_history')->nullable()->after('completed_at')->comment('Processing history JSON');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'units',
                'sisa',
                'outstanding_status',
                'completion_percentage',
                'completed_at',
                'processing_history'
            ]);
        });
    }
};
