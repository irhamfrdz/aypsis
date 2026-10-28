<?php

namespace Tests\Feature;

use App\Http\Controllers\PermohonanAmprahanController;
use App\Http\Controllers\PurchaseOrderController;
use App\Models\PermohonanAmprahan;
use App\Models\PermohonanAmprahanItem;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $table->string('satuan')->nullable();
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

    public function test_reject_action_rejects_every_item_without_quantity_input(): void
    {
        $permohonan = PermohonanAmprahan::create(['status' => 'partially_approved']);
        $firstItem = PermohonanAmprahanItem::create([
            'permohonan_id' => $permohonan->id,
            'nama_barang' => 'Oli',
            'jumlah' => 5,
            'jumlah_disetujui' => 3,
            'status' => 'partially_approved',
        ]);
        $secondItem = PermohonanAmprahanItem::create([
            'permohonan_id' => $permohonan->id,
            'nama_barang' => 'Filter',
            'jumlah' => 2,
            'jumlah_disetujui' => 2,
            'status' => 'approved',
        ]);

        (new PermohonanAmprahanController)->reject($permohonan->id);

        foreach ([$firstItem, $secondItem] as $item) {
            $this->assertDatabaseHas('permohonan_amprahan_items', [
                'id' => $item->id,
                'jumlah_disetujui' => 0,
                'status' => 'rejected',
            ]);
        }
        $this->assertSame('rejected', $permohonan->fresh()->status);
    }

    public function test_saving_approval_opens_purchase_order_with_only_approved_quantities(): void
    {
        $permohonan = PermohonanAmprahan::create(['status' => 'pending']);
        $approvedItem = PermohonanAmprahanItem::create([
            'permohonan_id' => $permohonan->id,
            'nama_barang' => 'Oli',
            'jumlah' => 5,
            'satuan' => 'liter',
        ]);
        $rejectedItem = PermohonanAmprahanItem::create([
            'permohonan_id' => $permohonan->id,
            'nama_barang' => 'Filter',
            'jumlah' => 2,
            'satuan' => 'pcs',
        ]);

        Auth::shouldReceive('user')->andReturn(new class
        {
            public function can(string $permission): bool
            {
                return $permission === 'purchase-order-create';
            }
        });

        $request = Request::create('/approval-permohonan-amprahan/'.$permohonan->id.'/process', 'POST', [
            'items' => [$approvedItem->id => '3.50', $rejectedItem->id => '0'],
        ]);
        $this->app->instance('request', $request);
        $response = (new PermohonanAmprahanController)->process($request, $permohonan->id);

        $this->assertSame(
            route('purchase-orders.create', ['permohonan_amprahan' => $permohonan->id]),
            $response->getTargetUrl()
        );

        $createRequest = Request::create('/purchase-orders/create', 'GET', [
            'permohonan_amprahan' => $permohonan->id,
        ]);
        $view = (new PurchaseOrderController)->create($createRequest);
        $this->assertSame([
            ['deskripsi' => 'Oli', 'kuantitas' => '3.5', 'satuan' => 'liter', 'harga_satuan' => 0],
        ], $view->getData()['prefillItems']);

        $html = view('purchase-orders._form', [
            'submitLabel' => 'Simpan Purchase Order',
            'prefillItems' => $view->getData()['prefillItems'],
            'errors' => new \Illuminate\Support\ViewErrorBag,
        ])->render();
        $this->assertStringContainsString('value="Oli"', $html);
        $this->assertStringContainsString('value="3.5"', $html);
        $this->assertStringNotContainsString('value="Filter"', $html);
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
