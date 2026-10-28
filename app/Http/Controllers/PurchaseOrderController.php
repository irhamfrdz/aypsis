<?php

namespace App\Http\Controllers;

use App\Models\PermohonanAmprahan;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderAssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['draft', 'issued', 'cancelled'])],
        ]);

        $orders = PurchaseOrder::query()
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('nomor_po', 'like', "%{$search}%")
                    ->orWhere('vendor', 'like', "%{$search}%");
            }))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('tanggal_po')->orderByDesc('id')->paginate(25)->withQueryString();

        return view('purchase-orders.index', compact('orders'));
    }

    public function create(Request $request): View
    {
        $prefillItems = null;
        $permohonanAmprahan = null;

        if ($request->filled('permohonan_amprahan')) {
            $request->validate(['permohonan_amprahan' => ['integer', 'exists:permohonan_amprahans,id']]);
            $permohonanAmprahan = PermohonanAmprahan::with('items')->findOrFail($request->integer('permohonan_amprahan'));
            abort_unless(in_array($permohonanAmprahan->status, ['pending', 'approved', 'partially_approved'], true), 404);
            abort_if($permohonanAmprahan->purchaseOrder()->exists(), 404);

            $prefillItems = $permohonanAmprahan->items
                ->filter(fn ($item) => (float) $item->jumlah_disetujui > 0)
                ->map(fn ($item) => [
                    'deskripsi' => $item->nama_barang,
                    'kuantitas' => rtrim(rtrim(number_format((float) $item->jumlah_disetujui, 3, '.', ''), '0'), '.'),
                    'satuan' => $item->satuan,
                    'harga_satuan' => 0,
                    'masuk_asset' => false,
                ])->values()->all();
            abort_if(empty($prefillItems), 404);
        }

        return view('purchase-orders.create', compact('prefillItems', 'permohonanAmprahan'));
    }

    public function store(Request $request): RedirectResponse
    {
        [$data, $items] = $this->validatedData($request);
        $this->validateAssetSelection($items, (bool) ($data['permohonan_amprahan_id'] ?? false));

        $order = DB::transaction(function () use ($data, $items) {
            $permohonanAmprahan = null;
            if ($data['permohonan_amprahan_id'] ?? null) {
                $permohonanAmprahan = PermohonanAmprahan::query()
                    ->lockForUpdate()->findOrFail($data['permohonan_amprahan_id']);
                $permohonanAmprahan->load('items');

                if (! in_array($permohonanAmprahan->status, ['pending', 'approved', 'partially_approved'], true)
                    || $permohonanAmprahan->purchaseOrder()->exists()) {
                    throw ValidationException::withMessages([
                        'permohonan_amprahan_id' => 'Permohonan ini sudah diproses menjadi Purchase Order.',
                    ]);
                }

                $this->assertMatchesAmprahan($permohonanAmprahan, $items);
            }

            $order = PurchaseOrder::create($data + [
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            if (! $order->nomor_po) {
                $order->update(['nomor_po' => sprintf('PO-%s-%06d', $order->tanggal_po->format('Ym'), $order->id)]);
            }
            $order->items()->createMany($items);

            if ($permohonanAmprahan && $permohonanAmprahan->status === 'pending') {
                $fullyApproved = true;
                foreach ($permohonanAmprahan->items as $item) {
                    if ((float) $item->jumlah_disetujui === 0.0) {
                        $item->status = 'rejected';
                        $fullyApproved = false;
                    } elseif ((float) $item->jumlah_disetujui === (float) $item->jumlah) {
                        $item->status = 'approved';
                    } else {
                        $item->status = 'partially_approved';
                        $fullyApproved = false;
                    }
                    $item->save();
                }
                $permohonanAmprahan->status = $fullyApproved ? 'approved' : 'partially_approved';
                $permohonanAmprahan->save();
            }

            return $order;
        });

        return redirect()->route('purchase-orders.show', $order)
            ->with('success', 'Purchase Order berhasil dibuat.');
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['items', 'creator']);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['items', 'permohonanAmprahan']);

        return view('purchase-orders.edit', [
            'purchaseOrder' => $purchaseOrder,
            'permohonanAmprahan' => $purchaseOrder->permohonanAmprahan,
        ]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        [$data, $items] = $this->validatedData($request, $purchaseOrder);
        $this->validateAssetSelection($items, (bool) $purchaseOrder->permohonan_amprahan_id);
        $data['nomor_po'] = ($data['nomor_po'] ?? null) ?: $purchaseOrder->nomor_po;

        DB::transaction(function () use ($purchaseOrder, $data, $items) {
            if ($purchaseOrder->permohonan_amprahan_id) {
                $this->assertMatchesAmprahan(
                    $purchaseOrder->permohonanAmprahan()->with('items')->firstOrFail(),
                    $items
                );
            }
            $purchaseOrder->update($data + ['updated_by' => auth()->id()]);
            if ($purchaseOrder->permohonan_amprahan_id) {
                $existingItems = $purchaseOrder->items()->get()->keyBy('urutan');
                $assetService = new PurchaseOrderAssetService;
                foreach ($items as $itemData) {
                    $item = $existingItems->pull($itemData['urutan']);
                    if ($item) {
                        $item->update($itemData);
                    } else {
                        $item = $purchaseOrder->items()->create($itemData);
                    }
                    if (! $item->masuk_asset) {
                        $assetService->remove($item);
                    }
                }
                foreach ($existingItems as $item) {
                    $assetService->remove($item);
                    $item->delete();
                }
            } else {
                $purchaseOrder->items()->delete();
                $purchaseOrder->items()->createMany($items);
            }
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order berhasil diperbarui.');
    }

    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        DB::transaction(function () use ($purchaseOrder) {
            $permohonanAmprahan = $purchaseOrder->permohonanAmprahan()->lockForUpdate()->first();
            $assetService = new PurchaseOrderAssetService;
            foreach ($purchaseOrder->items as $item) {
                $assetService->remove($item);
            }
            $purchaseOrder->delete();

            if ($permohonanAmprahan) {
                foreach ($permohonanAmprahan->items as $item) {
                    $item->status = 'pending';
                    $item->save();
                }
                $permohonanAmprahan->status = 'pending';
                $permohonanAmprahan->save();
            }
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dihapus.');
    }

    private function validatedData(Request $request, ?PurchaseOrder $purchaseOrder = null): array
    {
        $data = $request->validate([
            'nomor_po' => ['nullable', 'string', 'max:60', Rule::unique('purchase_orders', 'nomor_po')->ignore($purchaseOrder?->id)],
            'permohonan_amprahan_id' => [$purchaseOrder ? 'prohibited' : 'nullable', 'integer', 'exists:permohonan_amprahans,id'],
            'tanggal_po' => ['required', 'date'],
            'tanggal_dibutuhkan' => ['nullable', 'date', 'after_or_equal:tanggal_po'],
            'vendor' => ['required', 'string', 'max:255'],
            'alamat_vendor' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['draft', 'issued', 'cancelled'])],
            'catatan' => ['nullable', 'string', 'max:5000'],
            'diskon' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'pajak_persen' => ['nullable', 'numeric', 'between:0,100', 'decimal:0,2'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.deskripsi' => ['required', 'string', 'max:500'],
            'items.*.kuantitas' => ['required', 'numeric', 'min:0.001', 'max:999999999', 'decimal:0,3'],
            'items.*.satuan' => ['nullable', 'string', 'max:30'],
            'items.*.harga_satuan' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'items.*.masuk_asset' => ['sometimes', 'boolean'],
        ]);

        $items = [];
        $subtotalCents = 0;
        foreach (array_values($data['items']) as $index => $item) {
            $lineCents = (int) round((float) $item['kuantitas'] * (float) $item['harga_satuan'] * 100);
            $subtotalCents += $lineCents;
            $items[] = [
                'urutan' => $index + 1,
                'deskripsi' => $item['deskripsi'],
                'kuantitas' => $item['kuantitas'],
                'satuan' => $item['satuan'] ?? null,
                'harga_satuan' => $item['harga_satuan'],
                'jumlah' => $lineCents / 100,
                'masuk_asset' => (bool) ($item['masuk_asset'] ?? false),
            ];
        }
        unset($data['items']);

        $discountCents = (int) round((float) ($data['diskon'] ?? 0) * 100);
        if ($discountCents > $subtotalCents) {
            throw ValidationException::withMessages(['diskon' => 'Diskon tidak boleh melebihi subtotal.']);
        }
        $taxPercent = (float) ($data['pajak_persen'] ?? 0);
        $taxCents = (int) round(($subtotalCents - $discountCents) * $taxPercent / 100);

        $data['subtotal'] = $subtotalCents / 100;
        $data['diskon'] = $discountCents / 100;
        $data['pajak_persen'] = $taxPercent;
        $data['pajak'] = $taxCents / 100;
        $data['total'] = ($subtotalCents - $discountCents + $taxCents) / 100;

        return [$data, $items];
    }

    private function assertMatchesAmprahan(PermohonanAmprahan $permohonanAmprahan, array $items): void
    {
        $approvedItems = $permohonanAmprahan->items
            ->filter(fn ($item) => (float) $item->jumlah_disetujui > 0)->values();

        if ($approvedItems->isEmpty() || $approvedItems->count() !== count($items)) {
            throw ValidationException::withMessages([
                'items' => 'Barang PO harus sama dengan barang yang disetujui.',
            ]);
        }

        foreach ($approvedItems as $index => $approvedItem) {
            if ($items[$index]['deskripsi'] !== $approvedItem->nama_barang
                || abs((float) $items[$index]['kuantitas'] - (float) $approvedItem->jumlah_disetujui) > 0.0001
                || ($items[$index]['satuan'] ?? '') !== ($approvedItem->satuan ?? '')) {
                throw ValidationException::withMessages([
                    'items' => 'Barang PO harus sama dengan barang yang disetujui.',
                ]);
            }
        }
    }

    private function validateAssetSelection(array $items, bool $fromAmprahan): void
    {
        if (! $fromAmprahan && collect($items)->contains(fn ($item) => $item['masuk_asset'])) {
            throw ValidationException::withMessages([
                'items' => 'Pilihan masuk aset hanya tersedia untuk PO dari permohonan amprahan.',
            ]);
        }
    }
}
