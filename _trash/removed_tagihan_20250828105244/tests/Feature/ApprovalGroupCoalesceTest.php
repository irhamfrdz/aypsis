<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Permohonan;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ApprovalGroupCoalesceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure that two approvals with the same vendor and tanggal_harga_awal
     * are merged into a single tagihan (group) and that the pivot table
     * contains both container links. This test uses transactions so it
     * does not persist changes to your real database.
     */
    public function test_two_approvals_same_vendor_and_date_merge_into_one_tagihan()
    {
    // Arrange: create a user and act as them; disable CSRF middleware for test requests
    $user = User::factory()->create();
    $this->actingAs($user);
    // Disable all middleware to avoid CSRF/session related 419 errors in this test
    $this->withoutMiddleware();

        $supir = Karyawan::factory()->create();

        $date = now()->toDateString();
        $vendor = 'ZONA';

        // First permohonan with one kontainer
        $perm1 = Permohonan::factory()->create([
            'vendor_perusahaan' => $vendor,
            'supir_id' => $supir->id,
            'status' => 'draft',
        ]);
        $k1 = \App\Models\Kontainer::factory()->create(['ukuran' => '20']);
        $perm1->kontainers()->attach($k1->id);

        // Act: approve first
        // Call controller store() directly to avoid routing/middleware complexity in tests
        $request1 = \Illuminate\Http\Request::create('/approval/' . $perm1->id, 'POST', [
            'status_permohonan' => 'selesai',
            'tanggal_masuk_sewa' => $date,
        ]);
        $controller = new \App\Http\Controllers\PenyelesaianController();
        $response1 = $controller->store($request1, $perm1);
        $this->assertNotNull($response1);

        // Second permohonan with another kontainer, same vendor + date
        $perm2 = Permohonan::factory()->create([
            'vendor_perusahaan' => $vendor,
            'supir_id' => $supir->id,
            'status' => 'draft',
        ]);
        $k2 = \App\Models\Kontainer::factory()->create(['ukuran' => '40']);
        $perm2->kontainers()->attach($k2->id);

        // Act: approve second
        $request2 = \Illuminate\Http\Request::create('/approval/' . $perm2->id, 'POST', [
            'status_permohonan' => 'selesai',
            'tanggal_masuk_sewa' => $date,
        ]);
        $response2 = $controller->store($request2, $perm2);
        $this->assertNotNull($response2);
        // Assert: only one tagihan exists for that vendor+date
        $tagihanRows = DB::table('tagihan_kontainer_sewa')
            ->where('vendor', $vendor)
            ->whereDate('tanggal_harga_awal', $date)
            ->get();

        $this->assertCount(1, $tagihanRows, 'Expected a single tagihan row for same vendor+date');

        $tagihan = $tagihanRows->first();
        $this->assertNotNull($tagihan);

        // Assert: pivot table contains both kontainers linked to that tagihan
        $pivotCount = DB::table('tagihan_kontainer_sewa_kontainers')
            ->where('tagihan_id', $tagihan->id)
            ->count();

        $this->assertEquals(2, $pivotCount, 'Expected two pivot rows (one per kontainer) for the merged tagihan');
    }
}
