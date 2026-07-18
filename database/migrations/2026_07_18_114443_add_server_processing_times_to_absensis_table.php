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
            $table->string('attendance_uuid')->nullable()->after('id');
            $table->string('gps_accuracy')->nullable()->after('longitude');
            $table->string('payload_integrity')->nullable()->after('status');
            $table->timestamp('device_time')->nullable()->after('device');
            $table->timestamp('server_time')->nullable()->after('device_time');
            $table->timestamp('sync_time')->nullable()->after('server_time');
            $table->timestamp('server_received_at')->nullable()->after('sync_time');
            $table->timestamp('server_processed_at')->nullable()->after('server_received_at');
            $table->string('timezone')->nullable()->after('server_processed_at');
            $table->string('device_id')->nullable()->after('timezone');
            $table->string('app_version')->nullable()->after('device_id');
            $table->string('os_version')->nullable()->after('app_version');
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
