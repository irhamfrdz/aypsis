<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UpdateSuratJalanTarikKosongBatamKontainerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('surat_jalan_tarik_kosong_batams', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_jalan')->unique();
            $table->string('no_kontainer')->nullable();
            $table->timestamps();
        });
    }

    public function test_preview_does_not_update_and_apply_updates_by_exact_surat_jalan(): void
    {
        DB::table('surat_jalan_tarik_kosong_batams')->insert([
            'no_surat_jalan' => '0038591',
            'no_kontainer' => 'AYPU0000000',
        ]);
        $file = $this->inputFile("0038591;46277;FORU8134330;20;EMPTY;RIDWAN;BP9302DU;BUKIT SENYUM;SRIMAS;\n");

        try {
            $this->artisan('surat-jalan-tarik-kosong-batam:update-kontainer', ['file' => $file])
                ->assertExitCode(0);
            $this->assertDatabaseHas('surat_jalan_tarik_kosong_batams', [
                'no_surat_jalan' => '0038591', 'no_kontainer' => 'AYPU0000000',
            ]);

            $this->artisan('surat-jalan-tarik-kosong-batam:update-kontainer', [
                'file' => $file, '--apply' => true,
            ])->assertExitCode(0);
            $this->assertDatabaseHas('surat_jalan_tarik_kosong_batams', [
                'no_surat_jalan' => '0038591', 'no_kontainer' => 'FORU8134330',
            ]);
        } finally {
            unlink($file);
        }
    }

    public function test_apply_aborts_when_any_surat_jalan_is_missing(): void
    {
        DB::table('surat_jalan_tarik_kosong_batams')->insert([
            'no_surat_jalan' => '0038591',
            'no_kontainer' => 'AYPU0000000',
        ]);
        $file = $this->inputFile("0038591;FORU8134330\n0035602;AYPU2524182\n");

        try {
            $this->artisan('surat-jalan-tarik-kosong-batam:update-kontainer', [
                'file' => $file, '--apply' => true,
            ])->assertExitCode(1);
            $this->assertDatabaseHas('surat_jalan_tarik_kosong_batams', [
                'no_surat_jalan' => '0038591', 'no_kontainer' => 'AYPU0000000',
            ]);
        } finally {
            unlink($file);
        }
    }

    private function inputFile(string $content): string
    {
        $file = tempnam(storage_path('framework'), 'sj-batam-');
        file_put_contents($file, $content);

        return $file;
    }
}
