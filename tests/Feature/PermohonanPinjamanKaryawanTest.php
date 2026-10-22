<?php

namespace Tests\Feature;

use App\Http\Controllers\UserController;
use App\Models\Karyawan;
use App\Models\Permission;
use App\Models\PermohonanPinjamanKaryawan;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PermohonanPinjamanKaryawanTest extends TestCase
{
    private User $user;

    private Karyawan $karyawan;

    protected function setUp(): void
    {
        parent::setUp();

        // Isolated schema: historical project migrations contain MySQL-only SQL.
        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => false,
            'cache.default' => 'array',
        ]);
        DB::purge('sqlite');

        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
            $table->string('divisi')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('status')->default('pending');
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
            $table->primary(['user_id', 'permission_id']);
            $table->timestamps();
        });
        Schema::create('saldo_utang_karyawans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id')->unique();
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('riwayat_utang_karyawans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->date('tanggal');
            $table->string('tipe');
            $table->decimal('nominal', 15, 2);
            $table->string('referensi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        (require database_path('migrations/2026_10_22_200000_create_permohonan_pinjaman_karyawans_table.php'))->up();

        $this->karyawan = Karyawan::withoutEvents(fn () => Karyawan::create([
            'nik' => 'TEST-001',
            'nama_lengkap' => 'Karyawan Pengujian',
            'nama_panggilan' => 'Tester',
            'divisi' => 'Keuangan',
            'pekerjaan' => 'Staf',
        ]));
        $this->user = User::withoutEvents(fn () => User::create([
            'username' => 'kiky',
            'password' => bcrypt('password'),
            'karyawan_id' => $this->karyawan->id,
            'status' => 'approved',
        ]));

        $this->actingAs($this->user);
    }

    public function test_request_can_be_created_and_approved_once(): void
    {
        $response = $this->post(route('permohonan-pinjaman-karyawan.store'), [
            'karyawan_id' => $this->karyawan->id,
            'tanggal_permohonan' => '2026-10-22',
            'tanggal_dibutuhkan' => '2026-10-24',
            'nominal' => 1_200_000,
            'tenor_bulan' => 6,
            'tujuan_pinjaman' => 'Biaya pendidikan keluarga',
            'catatan_pemohon' => 'Dibutuhkan pekan ini',
        ]);

        $permohonan = PermohonanPinjamanKaryawan::sole();

        $response->assertRedirect(route('permohonan-pinjaman-karyawan.show', $permohonan));
        $this->assertSame('pending', $permohonan->status);
        $this->assertSame('200000.00', $permohonan->rencana_cicilan);
        $this->assertSame($this->user->id, $permohonan->created_by);

        $approval = $this->post(route('permohonan-pinjaman-karyawan.approve', $permohonan), [
            'nominal_disetujui' => 900_000,
            'tenor_disetujui' => 5,
            'catatan_approval' => 'Disetujui sebagian',
        ]);

        $approval->assertRedirect(route('permohonan-pinjaman-karyawan.show', $permohonan));
        $this->assertDatabaseHas('permohonan_pinjaman_karyawans', [
            'id' => $permohonan->id,
            'status' => 'approved',
            'nominal_disetujui' => 900_000,
            'tenor_disetujui' => 5,
            'approved_by' => $this->user->id,
        ]);
        $this->assertDatabaseHas('saldo_utang_karyawans', [
            'karyawan_id' => $this->karyawan->id,
            'saldo' => 900_000,
        ]);
        $this->assertDatabaseHas('riwayat_utang_karyawans', [
            'karyawan_id' => $this->karyawan->id,
            'tipe' => 'penambahan',
            'nominal' => 900_000,
            'referensi' => $permohonan->nomor_permohonan,
        ]);

        $duplicateApproval = $this->post(route('permohonan-pinjaman-karyawan.approve', $permohonan), [
            'nominal_disetujui' => 900_000,
            'tenor_disetujui' => 5,
        ]);

        $duplicateApproval->assertSessionHasErrors('status');
        $this->assertDatabaseCount('riwayat_utang_karyawans', 1);
        $this->assertDatabaseHas('saldo_utang_karyawans', [
            'karyawan_id' => $this->karyawan->id,
            'saldo' => 900_000,
        ]);
    }

    public function test_rejected_request_does_not_change_employee_debt(): void
    {
        $permohonan = PermohonanPinjamanKaryawan::create([
            'nomor_permohonan' => 'PUK-261022-000001',
            'karyawan_id' => $this->karyawan->id,
            'tanggal_permohonan' => '2026-10-22',
            'nominal' => 500_000,
            'tenor_bulan' => 2,
            'rencana_cicilan' => 250_000,
            'tujuan_pinjaman' => 'Keperluan keluarga',
            'status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        $response = $this->post(route('permohonan-pinjaman-karyawan.reject', $permohonan), [
            'catatan_approval' => 'Dokumen belum lengkap',
        ]);

        $response->assertRedirect(route('permohonan-pinjaman-karyawan.show', $permohonan));
        $this->assertDatabaseHas('permohonan_pinjaman_karyawans', [
            'id' => $permohonan->id,
            'status' => 'rejected',
            'rejected_by' => $this->user->id,
        ]);
        $this->assertDatabaseCount('saldo_utang_karyawans', 0);
        $this->assertDatabaseCount('riwayat_utang_karyawans', 0);
    }

    public function test_permissions_round_trip_through_user_matrix(): void
    {
        $permissions = Permission::query()
            ->whereIn('name', [
                'permohonan-pinjaman-karyawan-view',
                'permohonan-pinjaman-karyawan-create',
                'permohonan-pinjaman-karyawan-approve',
            ])
            ->pluck('id', 'name');

        $this->assertCount(3, $permissions);

        $controller = app(UserController::class);
        $ids = $controller->testConvertMatrixPermissionsToIds([
            'permohonan-pinjaman-karyawan' => [
                'view' => '1',
                'create' => '1',
                'approve' => '1',
            ],
        ]);

        $this->assertEqualsCanonicalizing($permissions->values()->all(), $ids);

        $matrix = $controller->testConvertPermissionsToMatrix($permissions->keys()->all());
        $this->assertTrue($matrix['permohonan-pinjaman-karyawan']['view']);
        $this->assertTrue($matrix['permohonan-pinjaman-karyawan']['create']);
        $this->assertTrue($matrix['permohonan-pinjaman-karyawan']['approve']);
    }
}
