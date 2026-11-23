<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('surat_jalan_tarik_kosong_batams', 'tipe_operator_lolo')) {
            Schema::table('surat_jalan_tarik_kosong_batams', function (Blueprint $table): void {
                $table->string('tipe_operator_lolo')->nullable()->after('menggunakan_lolo');
            });
        }

        if (! Schema::hasColumn('surat_jalan_tarik_kosong_batams', 'operator_lolo_karyawan_id')) {
            Schema::table('surat_jalan_tarik_kosong_batams', function (Blueprint $table): void {
                $table->foreignId('operator_lolo_karyawan_id')->nullable()->after('tipe_operator_lolo');
            });
        }

        if (! Schema::hasColumn('surat_jalan_tarik_kosong_batams', 'operator_lolo_vendor')) {
            Schema::table('surat_jalan_tarik_kosong_batams', function (Blueprint $table): void {
                $table->string('operator_lolo_vendor')->nullable()->after('operator_lolo_karyawan_id');
            });
        }

        $foreignKeyExists = collect(Schema::getForeignKeys('surat_jalan_tarik_kosong_batams'))
            ->contains(fn (array $key): bool => in_array('operator_lolo_karyawan_id', $key['columns'], true));

        if (! $foreignKeyExists) {
            Schema::table('surat_jalan_tarik_kosong_batams', function (Blueprint $table): void {
                $table->foreign('operator_lolo_karyawan_id', 'sj_tarik_kosong_lolo_operator_fk')->references('id')->on('karyawans')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('surat_jalan_tarik_kosong_batams', function (Blueprint $table): void {
            $table->dropForeign('sj_tarik_kosong_lolo_operator_fk');
            $table->dropColumn(['tipe_operator_lolo', 'operator_lolo_karyawan_id', 'operator_lolo_vendor']);
        });
    }
};
