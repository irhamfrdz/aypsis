<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RepairManifestSr20jp26Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (['manifests', 'naik_kapal', 'bls', 'prospek'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('prospek_id')->nullable();
                foreach (['nomor_kontainer', 'no_voyage', 'nama_kapal', 'tipe_kontainer', 'size_kontainer', 'nama_barang'] as $column) {
                    $table->string($column)->nullable();
                }
                $table->boolean('sudah_ob')->default(false);
            });
        }
        foreach (['kontainers', 'stock_kontainers'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('nomor_seri_gabungan');
                $table->string('ukuran');
            });
        }
        Schema::create('surat_jalan_bongkarans', fn (Blueprint $table) => $table->unsignedBigInteger('manifest_id'));
        for ($i = 0; $i < 107; $i++) {
            DB::table('manifests')->insert([
                'nama_kapal' => 'KM. SRIWIJAYA RAYA', 'no_voyage' => 'SR20JP26',
                'nomor_kontainer' => 'VALID'.$i, 'size_kontainer' => $i < 94 ? '20ft' : '40ft',
                'tipe_kontainer' => 'FCL', 'nama_barang' => 'Barang',
            ]);
        }
        $containers = [10237 => 'AYPU2063007', 10321 => 'GLDU5759330', 10326 => 'AYPU3250475',
            10375 => 'MAGU5226741', 10395 => 'AYPU3480644', 10413 => 'HJMU1531909',
            10414 => 'AYPU6524065', 10415 => 'AYPU6223171', 10433 => 'AYPU3231406'];
        foreach ($containers as $id => $number) {
            $target = ['no_voyage' => 'AS22JP26', 'nama_kapal' => 'KM ALKEN PESONA', 'nomor_kontainer' => $number];
            DB::table('prospek')->insert(['id' => $id] + $target);
            $data = ['prospek_id' => $id, 'size_kontainer' => in_array($id, [10237, 10321, 10395, 10433]) ? '20ft' : '40ft',
                'tipe_kontainer' => 'fcl', 'nama_barang' => 'Barang'];
            foreach (['manifests', 'naik_kapal', 'bls'] as $table) {
                DB::table($table)->insert($target + $data);
                DB::table($table)->insert(array_merge($target, $data, [
                    'no_voyage' => 'SR20JP26', 'nama_kapal' => 'KM. SRIWIJAYA RAYA',
                    'sudah_ob' => $table === 'bls',
                ]));
            }
        }
    }

    public function test_preview_verifies_94_and_13_without_changing_data(): void
    {
        $this->artisan('manifest:repair-sr20jp26')
            ->expectsOutputToContain('Full 20ft: 98 -> 94; full 40ft: 18 -> 13.')->assertSuccessful();
        $this->assertSame(116, DB::table('manifests')->where('no_voyage', 'SR20JP26')->count());
    }

    public function test_apply_backs_up_only_removes_stale_manifests_and_is_repeatable(): void
    {
        File::shouldReceive('ensureDirectoryExists')->once();
        File::shouldReceive('put')->once()->withArgs(function ($path, $json) {
            $backup = json_decode($json, true);

            return count($backup['remove']['manifests']) === 9 && count($backup['destination']['manifests']) === 9;
        })->andReturn(100);
        $this->artisan('manifest:repair-sr20jp26', ['--apply' => true])->assertSuccessful();
        $this->assertSame(107, DB::table('manifests')->where('no_voyage', 'SR20JP26')->count());
        $this->assertSame(9, DB::table('manifests')->where('no_voyage', 'AS22JP26')->count());
        $this->assertSame(18, DB::table('bls')->count());
        $this->assertSame(18, DB::table('naik_kapal')->count());
        $this->artisan('manifest:repair-sr20jp26', ['--apply' => true])->expectsOutputToContain('Data sudah benar')->assertSuccessful();
    }

    public function test_referenced_manifest_cannot_be_deleted(): void
    {
        DB::table('surat_jalan_bongkarans')->insert(['manifest_id' => DB::table('manifests')->where('prospek_id', 10237)->where('no_voyage', 'SR20JP26')->value('id')]);
        $this->artisan('manifest:repair-sr20jp26', ['--apply' => true])->expectsOutputToContain('Referensi')->assertFailed();
        $this->assertSame(125, DB::table('manifests')->count());
    }

    public function test_missing_destination_cancels_repair(): void
    {
        DB::table('manifests')->where('prospek_id', 10237)->where('no_voyage', 'AS22JP26')->delete();
        $this->artisan('manifest:repair-sr20jp26', ['--apply' => true])->expectsOutputToContain('tepat satu baris')->assertFailed();
        $this->assertSame(116, DB::table('manifests')->where('no_voyage', 'SR20JP26')->count());
    }

    public function test_unexpected_report_counts_cancel_repair(): void
    {
        DB::table('manifests')->where('nomor_kontainer', 'VALID0')->delete();
        $this->artisan('manifest:repair-sr20jp26', ['--apply' => true])->expectsOutputToContain('bukan 94/13')->assertFailed();
        $this->assertSame(9, DB::table('manifests')->where('no_voyage', 'SR20JP26')->whereNotNull('prospek_id')->count());
    }
}
