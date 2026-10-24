<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AbsensiDeletionGuard;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AbsensiDetailDeletionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->dateTime('waktu');
            $table->string('tipe');
        });
        (require database_path('migrations/2026_10_15_110000_create_absensi_deletion_marks_table.php'))->up();

        $user = new User(['username' => 'attendance-test', 'karyawan_id' => 1, 'status' => 'approved']);
        $user->id = 1;
        $user->setRelation('karyawan', null);
        $this->actingAs($user);
        Gate::define('absensi-delete', fn () => true);

        DB::table('absensis')->insert([
            ['id' => 1, 'nik' => '1004', 'waktu' => '2026-10-19 19:00:00', 'tipe' => 'Lembur_Masuk'],
            ['id' => 2, 'nik' => '1004', 'waktu' => '2026-10-20 04:00:00', 'tipe' => 'Lembur_Pulang'],
            ['id' => 3, 'nik' => '1004', 'waktu' => '2026-10-20 06:30:00', 'tipe' => 'Lembur_Pulang'],
            ['id' => 4, 'nik' => '9999', 'waktu' => '2026-10-20 04:00:00', 'tipe' => 'Lembur_Pulang'],
        ]);
    }

    public function test_deleting_one_timeline_entry_preserves_other_times_and_blocks_reimport(): void
    {
        $detailUrl = route('absensi.show', ['nik' => '1004', 'tanggal' => '2026-10-19']);
        $this->from($detailUrl)->delete(route('absensi.destroy_log', 2))
            ->assertRedirect($detailUrl)
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('absensis', ['id' => 2]);
        $this->assertSame([1, 3, 4], DB::table('absensis')->orderBy('id')->pluck('id')->all());
        $this->assertSame(1, DB::table('absensi_deletion_marks')->count());
        $this->assertTrue(AbsensiDeletionGuard::isDeleted('1004', '2026-10-20 04:00:00', 'selesai lembur'));
        $this->assertFalse(AbsensiDeletionGuard::isDeleted('1004', '2026-10-20 06:30:00', 'Lembur_Pulang'));

        $this->delete(route('absensi.destroy_log', 2))->assertNotFound();
        $this->assertSame(1, DB::table('absensi_deletion_marks')->count());
    }

    public function test_delete_requires_attendance_delete_permission(): void
    {
        Gate::define('absensi-delete', fn () => false);
        $this->deleteJson(route('absensi.destroy_log', 2))->assertForbidden();
        $this->assertDatabaseHas('absensis', ['id' => 2]);
        $this->assertSame(0, DB::table('absensi_deletion_marks')->count());
    }
}
