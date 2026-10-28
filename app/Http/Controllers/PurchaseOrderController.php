<?php

namespace App\Http\Controllers;

use App\Models\PermohonanAmprahan;
use App\Models\PurchaseOrder;
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
            abort_unless(in_array($permohonanAmprahan->status, ['approved', 'partially_approved'], true), 404);

            $prefillItems = $permohonanAmprahan->items
                ->filter(fn ($item) => (float) $item->jumlah_disetujui > 0)
                ->map(fn ($item) => [
                    'deskripsi' => $item->nama_barang,
                    'kuantitas' => rtrim(rtrim(number_format((float) $item->jumlah_disetujui, 3, '.', ''), '0'), '.'),
                    'satuan' => $item->satuan,
                    'harga_satuan' => 0,
                ])->values()->all();
            abort_if(empty($prefillItems), 404);
        }

        return view('purchase-orders.create', compact('prefillItems', 'permohonanAmprahan'));
    }

    public function store(Request $request): RedirectResponse
    {
        [$data, $items] = $this->validatedData($request);

        $order = DB::transaction(function () use ($data, $items) {
            $order = PurchaseOrder::create($data + [
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            if (! $order->nomor_po) {
                $order->update(['nomor_po' => sprintf('PO-%s-%06d', $order->tanggal_po->format('Ym'), $order->id)]);
            }
            $order->items()->createMany($items);

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
        $purchaseOrder->load('items');

        return view('purchase-orders.edit', compact('purchaseOrder'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        [$data, $items] = $this->validatedData($request, $purchaseOrder);
        $data['nomor_po'] = $data['nomor_po'] ?: $purchaseOrder->nomor_po;

        DB::transaction(function () use ($purchaseOrder, $data, $items) {
            $purchaseOrder->update($data + ['updated_by' => auth()->id()]);
            $purchaseOrder->items()->delete();
            $purchaseOrder->items()->createMany($items);
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order berhasil diperbarui.');
    }

    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dihapus.');
    }

    private function validatedData(Request $request, ?PurchaseOrder $purchaseOrder = null): array
    {
        $data = $request->validate([
            'nomor_po' => ['nullable', 'string', 'max:60', Rule::unique('purchase_orders', 'nomor_po')->ignore($purchaseOrder?->id)],
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
}
