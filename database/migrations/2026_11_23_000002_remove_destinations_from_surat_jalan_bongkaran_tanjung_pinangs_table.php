<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'surat_jalan_bongkaran_tanjung_pinangs';

    private const COLUMNS = ['tujuan_pengambilan', 'tujuan_pengiriman'];

    public function up(): void
    {
        $columns = array_values(array_filter(self::COLUMNS, fn (string $column) => Schema::hasColumn(self::TABLE, $column)));

        if ($columns !== []) {
            Schema::table(self::TABLE, function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }

    public function down(): void
    {
        foreach (self::COLUMNS as $column) {
            if (! Schema::hasColumn(self::TABLE, $column)) {
                Schema::table(self::TABLE, function (Blueprint $table) use ($column): void {
                    $table->string($column)->nullable();
                });
            }
        }
    }
};
