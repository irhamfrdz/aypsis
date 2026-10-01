<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PERMISSIONS = [
        'master-pricelist-lolo-batam-view' => 'Melihat Pricelist LOLO Batam',
        'master-pricelist-lolo-batam-create' => 'Menambah Pricelist LOLO Batam',
        'master-pricelist-lolo-batam-update' => 'Mengubah Pricelist LOLO Batam',
        'master-pricelist-lolo-batam-delete' => 'Menghapus Pricelist LOLO Batam',
    ];

    public function up(): void
    {
        Schema::create('master_pricelist_lolo_batams', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->string('nama_biaya');
            $table->string('size', 10);
            $table->decimal('tarif', 15, 2)->default(0);
            $table->string('status', 20)->default('aktif');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'size']);
            $table->index('vendor');
        });

        foreach (self::PERMISSIONS as $name => $description) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                ['description' => $description, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('name', array_keys(self::PERMISSIONS))->delete();
        Schema::dropIfExists('master_pricelist_lolo_batams');
    }
};
