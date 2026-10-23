<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('lokasi_asset', 3)->nullable();
        });

        $normalize = fn ($value) => match (strtoupper(trim($value ?? ''))) {
            'JAKARTA', 'JKT' => 'JKT',
            'BATAM', 'BTM' => 'BTM',
            'PNG' => 'PNG',
            default => null,
        };
        DB::table('assets')->orderBy('id')->chunkById(100, function ($assets) use ($normalize) {
            foreach ($assets as $asset) {
                $code = $normalize($asset->lokasi);
                if (! $code && $asset->source_table === 'mobils' && $asset->source_id) {
                    $code = $normalize(DB::table('mobils')->where('id', $asset->source_id)->value('lokasi'));
                }
                if (! $code && $asset->karyawan_id) {
                    $code = $normalize(DB::table('karyawans')->where('id', $asset->karyawan_id)->value('cabang'));
                }
                $values = ['lokasi_asset' => $code];
                if (in_array($asset->lokasi, ['JKT', 'BTM', 'PNG'], true)) {
                    $values['lokasi'] = $asset->kapal_id ? 'Di kapal' : ($asset->karyawan_id ? 'Di bawa oleh karyawan' : match ($asset->lokasi) {
                        'JKT' => 'Jakarta',
                        'BTM' => 'Batam',
                        default => null,
                    });
                }
                DB::table('assets')->where('id', $asset->id)->update($values);
            }
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('lokasi_asset');
        });
    }
};
