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
        if (Schema::hasTable('tanda_terimas')) {
            Schema::table('tanda_terimas', function (Blueprint $table) {
                if (! Schema::hasColumn('tanda_terimas', 'notify_party')) {
                    $table->string('notify_party')->nullable()->after('alamat_penerima');
                }
                if (! Schema::hasColumn('tanda_terimas', 'alamat_notify_party')) {
                    $table->text('alamat_notify_party')->nullable()->after('notify_party');
                }
            });
        }

        if (Schema::hasTable('tanda_terimas_lcl')) {
            Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
                if (! Schema::hasColumn('tanda_terimas_lcl', 'notify_party')) {
                    $table->string('notify_party')->nullable()->after('alamat_penerima');
                }
                if (! Schema::hasColumn('tanda_terimas_lcl', 'alamat_notify_party')) {
                    $table->text('alamat_notify_party')->nullable()->after('notify_party');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tanda_terimas')) {
            Schema::table('tanda_terimas', function (Blueprint $table) {
                $table->dropColumn(['notify_party', 'alamat_notify_party']);
            });
        }

        if (Schema::hasTable('tanda_terimas_lcl')) {
            Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
                $table->dropColumn(['notify_party', 'alamat_notify_party']);
            });
        }
    }
};
