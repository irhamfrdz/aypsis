<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = ['supir', 'no_plat', 'kenek'];

    public function up(): void
    {
        $columns = array_values(array_filter($this->columns, fn ($column) => Schema::hasColumn('surat_jalan_bongkaran_tanjung_pinangs', $column)));

        if ($columns !== []) {
            Schema::table('surat_jalan_bongkaran_tanjung_pinangs', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    public function down(): void
    {
        $columns = array_values(array_filter($this->columns, fn ($column) => ! Schema::hasColumn('surat_jalan_bongkaran_tanjung_pinangs', $column)));

        if ($columns !== []) {
            Schema::table('surat_jalan_bongkaran_tanjung_pinangs', function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    $table->string($column)->nullable();
                }
            });
        }
    }
};
