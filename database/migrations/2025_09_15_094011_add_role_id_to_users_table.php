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
            $table->unsignedBigInteger('role_id')->nullable()->after('status');
            $table->index('role_id');

            // Add foreign key if roles table exists
            if (Schema::hasTable('roles')) {
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key if exists
            if (Schema::hasTable('roles')) {
                $table->dropForeign(['role_id']);
            }

            // Drop index and column
            $table->dropIndex(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
