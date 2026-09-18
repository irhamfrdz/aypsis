<?php

namespace Tests\Unit;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

class LclProspekReceiptBackfillTest extends TestCase
{
    public function test_receipts_are_backfilled_only_for_the_matching_lcl_container_and_seal(): void
    {
        $capsule = new Manager;
        $capsule->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        DB::swap($capsule->getDatabaseManager());

        try {
            $schema = $capsule->getConnection()->getSchemaBuilder();
            $schema->create('prospek', function (Blueprint $table) {
                $table->id();
                $table->string('no_surat_jalan')->nullable();
                $table->string('nomor_kontainer');
                $table->string('no_seal')->nullable();
                $table->string('keterangan');
            });
            $schema->create('tanda_terimas_lcl', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_tanda_terima');
            });
            $schema->create('tanda_terima_lcl_kontainer_pivot', function (Blueprint $table) {
                $table->id();
                $table->integer('tanda_terima_lcl_id');
                $table->string('nomor_kontainer');
                $table->string('nomor_seal');
            });

            foreach ([1 => 'TT-LCL-001', 2 => 'TT-LCL-002', 3 => 'TT-OLD'] as $id => $number) {
                DB::table('tanda_terimas_lcl')->insert(['id' => $id, 'nomor_tanda_terima' => $number]);
            }
            foreach ([1, 2, 2, 3] as $receiptId) {
                DB::table('tanda_terima_lcl_kontainer_pivot')->insert([
                    'tanda_terima_lcl_id' => $receiptId,
                    'nomor_kontainer' => 'CONTAINER-1',
                    'nomor_seal' => $receiptId === 3 ? 'OLD-SEAL' : 'NEW-SEAL',
                ]);
            }
            foreach ([
                [null, 'NEW-SEAL', 'Kontainer LCL dengan 2 tanda terima'],
                ['', 'NEW-SEAL', 'Synced from LCL Stuffing'],
                ['EXISTING', 'NEW-SEAL', 'Synced from LCL Stuffing'],
                [null, 'UNKNOWN-SEAL', 'Synced from LCL Stuffing'],
                [null, 'NEW-SEAL', 'Regular FCL'],
                [null, null, 'Synced from LCL Stuffing'],
            ] as [$number, $seal, $description]) {
                DB::table('prospek')->insert([
                    'no_surat_jalan' => $number, 'nomor_kontainer' => 'CONTAINER-1',
                    'no_seal' => $seal, 'keterangan' => $description,
                ]);
            }

            $migration = require __DIR__.'/../../database/migrations/2026_09_18_120000_fill_lcl_receipt_numbers_in_prospek.php';
            $migration->up();
            $expected = ['TT-LCL-001, TT-LCL-002', 'TT-LCL-001, TT-LCL-002', 'EXISTING', null, null, null];
            $this->assertSame($expected, DB::table('prospek')->orderBy('id')->pluck('no_surat_jalan')->all());
            $migration->up();
            $this->assertSame($expected, DB::table('prospek')->orderBy('id')->pluck('no_surat_jalan')->all());
        } finally {
            $capsule->getDatabaseManager()->disconnect();
            DB::clearResolvedInstance('db');
        }
    }
}
