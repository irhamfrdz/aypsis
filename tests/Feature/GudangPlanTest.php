<?php

namespace Tests\Feature;

use App\Http\Controllers\GudangPlanController;
use App\Http\Middleware\EnsureCrewChecklistComplete;
use App\Http\Middleware\EnsureKaryawanPresent;
use App\Http\Middleware\EnsureUserApproved;
use App\Models\Gudang;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GudangPlanTest extends TestCase
{
    private Gudang $gudang;

    private bool $mayView = true;

    private bool $mayEdit = true;

    protected function setUp(): void
    {
        parent::setUp();
        // Isolated feature schema: do not execute the project's unrelated historical migrations.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.foreign_key_constraints' => true, 'cache.default' => 'array']);
        DB::purge('sqlite');
        (require database_path('migrations/2025_12_12_111750_create_gudangs_table.php'))->up();
        (require database_path('migrations/2026_09_16_120000_create_gudang_positions_table.php'))->up();
        foreach (['kontainers', 'stock_kontainers'] as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('gudangs_id')->nullable();
                $table->string('nomor_seri_gabungan');
                $table->string('ukuran');
                $table->string('tipe_kontainer')->default('Dry');
                $table->string('status')->default('available');
            });
        }
        $this->withoutMiddleware([EnsureKaryawanPresent::class, EnsureUserApproved::class, EnsureCrewChecklistComplete::class]);
        $user = new User;
        $user->setRawAttributes(['id' => 1, 'username' => 'planner-test']);
        $user->setRelation('roles', collect());
        $user->setRelation('karyawan', null);
        $user->setRelation('unreadNotifications', collect());
        $user->setRelation('permissions', collect([
            new Permission(['name' => 'master-gudang-view']),
            new Permission(['name' => 'master-gudang-edit']),
        ]));
        $this->actingAs($user);
        Gate::define('master-gudang-view', fn () => $this->mayView);
        Gate::define('master-gudang-edit', fn () => $this->mayEdit);
        $this->gudang = Gudang::create(['nama_gudang' => 'Gudang Uji', 'lokasi' => 'Jakarta', 'status' => 'aktif']);
    }

    private function layout(array $overrides = []): array
    {
        return ['blocks' => [array_merge(['code' => 'A', 'bays' => 4, 'rows' => 2, 'tiers' => 3, 'disabled' => []], $overrides)]];
    }

    private function saveLayout(?array $layout = null, ?int $version = null)
    {
        return $this->putJson(route('master-gudang.layout.update', $this->gudang), [
            'layout' => $layout ?? $this->layout(), 'version' => $version ?? $this->gudang->fresh()->denah_version,
        ]);
    }

    private function container(string $size = '20', string $source = 'stock', ?int $gudangId = null): int
    {
        $table = $source === 'stock' ? 'stock_kontainers' : 'kontainers';

        return DB::table($table)->insertGetId([
            'gudangs_id' => $gudangId ?? $this->gudang->id,
            'nomor_seri_gabungan' => strtoupper($source).(DB::table($table)->count() + 1000000),
            'ukuran' => $size,
        ]);
    }

    private function place(int $id, array $overrides = [])
    {
        return $this->putJson(route('denah-gudang.positions.store', $this->gudang), array_merge([
            'version' => $this->gudang->fresh()->denah_version,
            'source' => 'stock', 'container_id' => $id, 'block' => 'A', 'bay' => 1, 'row' => 1, 'tier' => 1,
        ], $overrides));
    }

    public function test_layout_and_both_container_sources_persist_and_can_be_moved_and_removed(): void
    {
        $this->saveLayout()->assertOk()->assertJsonPath('version', 1);
        $stock = $this->container('40');
        $sewa = $this->container('20', 'sewa');
        $this->place($stock)->assertOk()->assertJsonPath('positions.0.span', 2);
        $this->place($sewa, ['source' => 'sewa', 'bay' => 3])->assertOk();
        $this->place($stock, ['row' => 2])->assertOk()->assertJsonCount(2, 'positions')->assertJsonPath('positions.0.row', 2);
        $position = $this->gudang->positions()->where('source', 'stock')->first();
        $this->deleteJson(route('denah-gudang.positions.destroy', [$this->gudang, $position]), ['version' => 4])
            ->assertOk()->assertJsonCount(1, 'positions')->assertJsonCount(2, 'containers');
        $this->assertDatabaseCount('stock_kontainers', 1);
        $this->assertDatabaseHas('gudangs', ['id' => $this->gudang->id, 'denah_version' => 5]);
    }

    public function test_40_foot_footprint_rejects_overlap_blocked_cells_and_last_bay(): void
    {
        $this->saveLayout($this->layout(['disabled' => [['bay' => 2, 'row' => 2]]]))->assertOk();
        $forty = $this->container('40');
        $twenty = $this->container();
        $this->place($forty, ['bay' => 4])->assertUnprocessable();
        $this->place($forty, ['row' => 2])->assertUnprocessable();
        $this->place($forty)->assertOk();
        $this->place($twenty, ['bay' => 2])->assertUnprocessable()
            ->assertJsonPath('errors.denah.0', 'Area A, Slot 02, Baris 01, Tingkat 01 sudah ditempati kontainer lain.');
        $this->assertDatabaseCount('gudang_positions', 1);
        $this->assertSame(2, $this->gudang->fresh()->denah_version);
    }

    public function test_layout_changes_cannot_remove_occupied_space(): void
    {
        $this->saveLayout()->assertOk();
        $this->place($this->container('40'))->assertOk();
        foreach ([['bays' => 1], ['code' => 'B'], ['disabled' => [['bay' => 2, 'row' => 1]]]] as $changes) {
            $this->saveLayout($this->layout($changes))->assertUnprocessable();
        }
        $this->assertSame($this->layout(), $this->gudang->fresh()->denah_layout);
    }

    public function test_tiers_need_support_and_supporting_container_cannot_be_removed_or_moved(): void
    {
        $this->saveLayout()->assertOk();
        $lower = $this->container('40');
        $upper = $this->container('40');
        $this->place($upper, ['tier' => 2])->assertUnprocessable();
        $this->place($lower)->assertOk();
        $this->place($upper, ['tier' => 2])->assertOk();
        $this->place($lower, ['row' => 2])->assertUnprocessable();
        $position = $this->gudang->positions()->where('container_id', $lower)->first();
        $this->deleteJson(route('denah-gudang.positions.destroy', [$this->gudang, $position]), ['version' => 3])->assertUnprocessable();
        $this->assertDatabaseCount('gudang_positions', 2);
    }

    public function test_stale_versions_cannot_overwrite_layout_or_positions(): void
    {
        $this->saveLayout()->assertOk();
        $this->saveLayout($this->layout(['bays' => 10]), 0)->assertConflict();
        $container = $this->container();
        $this->place($container, ['version' => 0])->assertConflict();
        $this->place($container)->assertOk();
        $position = $this->gudang->positions()->first();
        $this->deleteJson(route('denah-gudang.positions.destroy', [$this->gudang, $position]), ['version' => 1])->assertConflict();
        $this->assertDatabaseCount('gudang_positions', 1);
    }

    public function test_only_current_warehouse_containers_with_supported_sizes_can_be_placed(): void
    {
        $this->saveLayout()->assertOk();
        $this->place($this->container('20', 'stock', $this->gudang->id + 99))->assertUnprocessable();
        $inactive = $this->container();
        DB::table('stock_kontainers')->where('id', $inactive)->update(['status' => 'inactive']);
        $this->place($inactive)->assertUnprocessable();
        $this->place($this->container('45'))->assertUnprocessable();
        $this->place($this->container(), ['source' => 'malicious'])->assertUnprocessable();
        $this->gudang->update(['status' => 'nonaktif']);
        $this->place($this->container())->assertUnprocessable();
        $this->assertDatabaseCount('gudang_positions', 0);
    }

    public function test_duplicate_physical_container_from_different_sources_is_rejected(): void
    {
        $this->saveLayout()->assertOk();
        $stock = $this->container();
        $sewa = $this->container('20', 'sewa');
        DB::table('kontainers')->where('id', $sewa)->update(['nomor_seri_gabungan' => 'stock 1000000']);
        $this->place($stock)->assertOk();
        $this->place($sewa, ['source' => 'sewa', 'bay' => 2])->assertUnprocessable();
    }

    public function test_invalid_layout_and_missing_layout_are_rejected(): void
    {
        $this->place($this->container())->assertUnprocessable();
        $this->saveLayout(['blocks' => []])->assertUnprocessable();
        $this->saveLayout(['blocks' => [$this->layout()['blocks'][0], $this->layout()['blocks'][0]]])->assertUnprocessable();
        $this->saveLayout($this->layout(['disabled' => [['bay' => 99, 'row' => 1]]]))->assertUnprocessable();
        $this->saveLayout($this->layout(['code' => '<script>']))->assertUnprocessable();
        $this->saveLayout($this->layout(['tiers' => 7]))->assertUnprocessable();
        $this->assertNull($this->gudang->fresh()->denah_layout);
    }

    public function test_view_and_edit_permissions_are_enforced(): void
    {
        $this->mayView = false;
        $this->getJson(route('denah-gudang.index'))->assertForbidden();
        $this->getJson(route('denah-gudang.show', $this->gudang))->assertForbidden();
        $this->getJson(route('master-gudang.layout', $this->gudang))->assertForbidden();
        $this->saveLayout()->assertForbidden();
        $this->mayView = true;
        $this->mayEdit = false;
        $this->saveLayout()->assertForbidden();
        $this->place($this->container())->assertForbidden();
        $this->deleteJson(route('denah-gudang.positions.destroy', [$this->gudang, 1]), ['version' => 0])->assertForbidden();
    }

    public function test_stale_positions_are_flagged_and_delete_is_scoped_to_the_warehouse(): void
    {
        $this->saveLayout()->assertOk();
        $id = $this->container();
        $this->place($id)->assertOk()->assertJsonPath('positions.0.stale', false);
        DB::table('stock_kontainers')->where('id', $id)->update(['gudangs_id' => null]);
        $view = app(GudangPlanController::class)->show($this->gudang->fresh());
        $this->assertTrue($view->getData()['state']['positions'][0]['stale']);
        $position = $this->gudang->positions()->first();
        $other = Gudang::create(['nama_gudang' => 'Lain', 'lokasi' => 'Batam', 'status' => 'aktif']);
        $this->deleteJson(route('denah-gudang.positions.destroy', [$other, $position]), ['version' => 0])->assertNotFound();
        $this->assertDatabaseCount('gudang_positions', 1);
    }

    public function test_planner_markup_renders_with_correct_routes_and_escaped_warehouse_name(): void
    {
        $this->gudang->update(['nama_gudang' => '<script>alert(1)</script>']);
        $controller = app(GudangPlanController::class);
        foreach (['layout', 'positions'] as $mode) {
            $view = $mode === 'layout' ? $controller->layout($this->gudang) : $controller->show($this->gudang);
            $html = view('denah-gudang.planner', [...$view->getData(), 'mode' => $mode])->render();
            $this->assertStringContainsString('id="gp-grid"', $html);
            $this->assertStringContainsString('A-S03-B02-T01', $html);
            $this->assertStringNotContainsString('<label>Bay', $html);
            $this->assertStringNotContainsString('<label>Tier', $html);
            $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
            $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        }
    }

    public function test_pages_render_for_warehouse_viewer(): void
    {
        $this->withoutVite();
        $this->get(route('denah-gudang.index'))->assertOk()->assertSee('Gudang Uji');
        $this->get(route('denah-gudang.show', $this->gudang))->assertOk()->assertSee('Input Posisi Kontainer');
        $this->get(route('master-gudang.layout', $this->gudang))->assertOk()->assertSee('Simpan Layout Gudang');
        $this->mayEdit = false;
        $this->get(route('denah-gudang.show', $this->gudang))->assertOk()->assertDontSee('id="gp-save-position"', false);
    }
}
