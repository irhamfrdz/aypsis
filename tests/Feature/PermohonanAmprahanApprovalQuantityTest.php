<?php

namespace Tests\Feature;

use App\Http\Controllers\PermohonanAmprahanController;
use App\Models\PermohonanAmprahan;
use App\Models\PermohonanAmprahanItem;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PermohonanAmprahanApprovalQuantityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('permohonan_amprahans', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('permohonan_amprahan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id');
            $table->string('nama_barang');
            $table->decimal('jumlah', 10, 2);
            $table->decimal('jumlah_disetujui', 10, 2)->nullable();
            $table->string('status')->default('pending');
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('permohonan_amprahan_items');
        Schema::dropIfExists('permohonan_amprahans');

        parent::tearDown();
    }

    public function test_approving_three_of_five_sets_partial_status_and_saves_quantity(): void
    {
        $permohonan = PermohonanAmprahan::create(['status' => 'pending']);
        $item = PermohonanAmprahanItem::create([
            'permohonan_id' => $permohonan->id,
            'nama_barang' => 'Oli',
            'jumlah' => 5,
        ]);

        $this->process($permohonan->id, [$item->id => '3']);

        $this->assertDatabaseHas('permohonan_amprahan_items', [
            'id' => $item->id,
            'jumlah_disetujui' => 3,
            'status' => 'partially_approved',
        ]);
        $this->assertDatabaseHas('permohonan_amprahans', [
            'id' => $permohonan->id,
            'status' => 'partially_approved',
        ]);
    }

    public function test_quantity_above_requested_is_rejected_without_changing_status(): void
    {
        $permohonan = PermohonanAmprahan::create(['status' => 'pending']);
        $item = PermohonanAmprahanItem::create([
            'permohonan_id' => $permohonan->id,
            'nama_barang' => 'Oli',
            'jumlah' => 5,
        ]);

        try {
            $this->process($permohonan->id, [$item->id => '6']);
            $this->fail('Jumlah melebihi permintaan seharusnya ditolak.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey("items.{$item->id}", $exception->errors());
        }

        $this->assertDatabaseHas('permohonan_amprahan_items', [
            'id' => $item->id,
            'status' => 'pending',
            'jumlah_disetujui' => null,
        ]);
        $this->assertDatabaseHas('permohonan_amprahans', [
            'id' => $permohonan->id,
            'status' => 'pending',
        ]);
    }

    public function test_approved_request_can_be_corrected_to_rejected_and_reset_to_pending(): void
    {
        $permohonan = PermohonanAmprahan::create(['status' => 'pending']);
        $item = PermohonanAmprahanItem::create([
            'permohonan_id' => $permohonan->id,
            'nama_barang' => 'Oli',
            'jumlah' => 5,
        ]);

        $this->process($permohonan->id, [$item->id => '5']);
        $this->assertSame('approved', $permohonan->fresh()->status);

        $this->process($permohonan->id, [$item->id => '0']);
        $this->assertDatabaseHas('permohonan_amprahan_items', [
            'id' => $item->id,
            'status' => 'rejected',
            'jumlah_disetujui' => 0,
        ]);
        $this->assertSame('rejected', $permohonan->fresh()->status);

        (new PermohonanAmprahanController)->resetApproval($permohonan->id);
        $this->assertDatabaseHas('permohonan_amprahan_items', [
            'id' => $item->id,
            'status' => 'pending',
            'jumlah_disetujui' => null,
        ]);
        $this->assertSame('pending', $permohonan->fresh()->status);
    }

    private function process(int $permohonanId, array $items): void
    {
        $request = Request::create('/approval-permohonan-amprahan/'.$permohonanId.'/process', 'POST', [
            'items' => $items,
        ]);
        $this->app->instance('request', $request);

        (new PermohonanAmprahanController)->process($request, $permohonanId);
    }
}
