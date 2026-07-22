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
        Schema::table('absensis', function (Blueprint $table) {
            if (!Schema::hasColumn('absensis', 'attendance_uuid')) {
                $table->string('attendance_uuid')->nullable()->after('id');
            }
            if (!Schema::hasColumn('absensis', 'gps_accuracy')) {
                $table->string('gps_accuracy')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('absensis', 'payload_integrity')) {
                $table->string('payload_integrity')->nullable()->after('status');
            }
            if (!Schema::hasColumn('absensis', 'device_time')) {
                $table->timestamp('device_time')->nullable()->after('device');
            }
            if (!Schema::hasColumn('absensis', 'server_time')) {
                $table->timestamp('server_time')->nullable()->after('device_time');
            }
            if (!Schema::hasColumn('absensis', 'sync_time')) {
                $table->timestamp('sync_time')->nullable()->after('server_time');
            }
            if (!Schema::hasColumn('absensis', 'server_received_at')) {
                $table->timestamp('server_received_at')->nullable()->after('sync_time');
            }
            if (!Schema::hasColumn('absensis', 'server_processed_at')) {
                $table->timestamp('server_processed_at')->nullable()->after('server_received_at');
            }
            if (!Schema::hasColumn('absensis', 'timezone')) {
                $table->string('timezone')->nullable()->after('server_processed_at');
            }
            if (!Schema::hasColumn('absensis', 'device_id')) {
                $table->string('device_id')->nullable()->after('timezone');
            }
            if (!Schema::hasColumn('absensis', 'app_version')) {
                $table->string('app_version')->nullable()->after('device_id');
            }
            if (!Schema::hasColumn('absensis', 'os_version')) {
                $table->string('os_version')->nullable()->after('app_version');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn([
                'attendance_uuid', 'gps_accuracy', 'payload_integrity',
                'device_time', 'server_time', 'sync_time',
                'server_received_at', 'server_processed_at',
                'timezone', 'device_id', 'app_version', 'os_version'
            ]);
        });
    }
};
