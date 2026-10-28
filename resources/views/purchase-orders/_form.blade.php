@php
    $order = $purchaseOrder ?? null;
    $sourceLocked = isset($permohonanAmprahan) && $permohonanAmprahan !== null;
    $items = old('items', $order?->items->map(fn ($item) => [
        'deskripsi' => $item->deskripsi,
        'kuantitas' => $item->kuantitas,
        'satuan' => $item->satuan,
        'harga_satuan' => $item->harga_satuan,
    ])->all() ?? $prefillItems ?? [['deskripsi' => '', 'kuantitas' => 1, 'satuan' => '', 'harga_satuan' => 0]]);
    if (empty($items)) {
        $items = [['deskripsi' => '', 'kuantitas' => 1, 'satuan' => '', 'harga_satuan' => 0]];
    }
@endphp

<div class="space-y-6">
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">Periksa kembali data PO yang ditandai.</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="mb-5 text-base font-bold text-slate-900">Informasi Purchase Order</h2>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="nomor_po" class="mb-2 block text-sm font-semibold text-slate-700">Nomor PO</label>
                <input id="nomor_po" name="nomor_po" type="text" maxlength="60" value="{{ old('nomor_po', $order?->nomor_po) }}" placeholder="Kosongkan untuk nomor otomatis" class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('nomor_po')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">Status <span class="text-red-600">*</span></label>
                <select id="status" name="status" required class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="draft" @selected(old('status', $order?->status ?? 'draft') === 'draft')>Draft</option>
                    <option value="issued" @selected(old('status', $order?->status) === 'issued')>Diterbitkan</option>
                    <option value="cancelled" @selected(old('status', $order?->status) === 'cancelled')>Dibatalkan</option>
                </select>
                @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tanggal_po" class="mb-2 block text-sm font-semibold text-slate-700">Tanggal PO <span class="text-red-600">*</span></label>
                <input id="tanggal_po" name="tanggal_po" type="date" required value="{{ old('tanggal_po', $order?->tanggal_po?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('tanggal_po')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tanggal_dibutuhkan" class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Dibutuhkan</label>
                <input id="tanggal_dibutuhkan" name="tanggal_dibutuhkan" type="date" value="{{ old('tanggal_dibutuhkan', $order?->tanggal_dibutuhkan?->format('Y-m-d')) }}" class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('tanggal_dibutuhkan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="vendor" class="mb-2 block text-sm font-semibold text-slate-700">Vendor <span class="text-red-600">*</span></label>
                <input id="vendor" name="vendor" type="text" required maxlength="255" value="{{ old('vendor', $order?->vendor) }}" placeholder="Nama vendor" class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('vendor')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="alamat_vendor" class="mb-2 block text-sm font-semibold text-slate-700">Alamat Vendor</label>
                <textarea id="alamat_vendor" name="alamat_vendor" rows="2" maxlength="2000" placeholder="Opsional" class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('alamat_vendor', $order?->alamat_vendor) }}</textarea>
                @error('alamat_vendor')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">Rincian Barang / Jasa</h2>
                <p class="mt-1 text-xs text-slate-500">Jumlah setiap baris dihitung dari kuantitas × harga satuan.</p>
            </div>
            @unless($sourceLocked)
                <button type="button" id="add-po-item" class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100"><i class="fas fa-plus"></i>Tambah Baris</button>
            @endunless
        </div>
        @error('items')<p class="mb-3 text-sm text-red-600">{{ $message }}</p>@enderror
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-[850px] w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="w-12 px-3 py-3">No.</th>
                        <th class="px-3 py-3">Deskripsi</th>
                        <th class="w-28 px-3 py-3">Kuantitas</th>
                        <th class="w-28 px-3 py-3">Satuan</th>
                        <th class="w-36 px-3 py-3">Harga Satuan</th>
                        <th class="w-36 px-3 py-3 text-right">Jumlah</th>
                        <th class="w-16 px-3 py-3"></th>
                    </tr>
                </thead>
                <tbody id="po-items" class="divide-y divide-slate-100 bg-white">
                    @foreach($items as $index => $item)
                        <tr class="po-item-row">
                            <td class="px-3 py-3 align-top text-slate-500 po-item-number">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3 align-top">
                                <input name="items[{{ $index }}][deskripsi]" type="text" required maxlength="500" value="{{ $item['deskripsi'] ?? '' }}" placeholder="Nama barang atau jasa" @readonly($sourceLocked) class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("items.$index.deskripsi")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </td>
                            <td class="px-3 py-3 align-top">
                                <input name="items[{{ $index }}][kuantitas]" type="number" required min="0.001" step="0.001" value="{{ $item['kuantitas'] ?? 1 }}" @readonly($sourceLocked) class="po-qty w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("items.$index.kuantitas")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </td>
                            <td class="px-3 py-3 align-top">
                                <input name="items[{{ $index }}][satuan]" type="text" maxlength="30" value="{{ $item['satuan'] ?? '' }}" placeholder="pcs" @readonly($sourceLocked) class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("items.$index.satuan")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </td>
                            <td class="px-3 py-3 align-top">
                                <input name="items[{{ $index }}][harga_satuan]" type="number" required min="0" step="0.01" value="{{ $item['harga_satuan'] ?? 0 }}" class="po-price w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("items.$index.harga_satuan")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </td>
                            <td class="po-line-total whitespace-nowrap px-3 py-4 text-right align-top font-semibold text-slate-800">Rp 0</td>
                            <td class="px-3 py-3 text-right align-top">
                                @unless($sourceLocked)
                                    <button type="button" class="remove-po-item rounded-lg p-2 text-rose-600 hover:bg-rose-50" aria-label="Hapus baris"><i class="fas fa-trash"></i></button>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label for="catatan" class="mb-2 block text-sm font-semibold text-slate-700">Catatan</label>
                <textarea id="catatan" name="catatan" rows="5" maxlength="5000" placeholder="Syarat atau catatan untuk vendor" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('catatan', $order?->catatan) }}</textarea>
                @error('catatan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="rounded-xl bg-slate-50 p-5">
                <div class="flex justify-between text-sm text-slate-600"><span>Subtotal</span><strong id="po-subtotal" class="text-slate-900">Rp 0</strong></div>
                <div class="mt-4 flex items-center justify-between gap-3">
                    <label for="diskon" class="text-sm text-slate-600">Diskon (Rp)</label>
                    <input id="diskon" name="diskon" type="number" min="0" step="0.01" value="{{ old('diskon', $order?->diskon ?? 0) }}" class="w-40 rounded-lg border border-slate-300 px-3 py-2 text-right text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                @error('diskon')<p class="mt-1 text-right text-xs text-red-600">{{ $message }}</p>@enderror
                <div class="mt-3 flex items-center justify-between gap-3">
                    <label for="pajak_persen" class="text-sm text-slate-600">Pajak (%)</label>
                    <input id="pajak_persen" name="pajak_persen" type="number" min="0" max="100" step="0.01" value="{{ old('pajak_persen', $order?->pajak_persen ?? 0) }}" class="w-40 rounded-lg border border-slate-300 px-3 py-2 text-right text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                @error('pajak_persen')<p class="mt-1 text-right text-xs text-red-600">{{ $message }}</p>@enderror
                <div class="mt-3 flex justify-between text-sm text-slate-600"><span>Nilai Pajak</span><strong id="po-tax" class="text-slate-900">Rp 0</strong></div>
                <div class="mt-4 flex justify-between border-t border-slate-200 pt-4 text-base font-bold text-slate-900"><span>Total PO</span><span id="po-total">Rp 0</span></div>
            </div>
        </div>
    </section>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <a href="{{ $order ? route('purchase-orders.show', $order) : route('purchase-orders.index') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">{{ $submitLabel }}</button>
    </div>
</div>

<template id="po-item-template">
    <tr class="po-item-row">
        <td class="po-item-number px-3 py-3 align-top text-slate-500"></td>
        <td class="px-3 py-3 align-top"><input name="items[__INDEX__][deskripsi]" type="text" required maxlength="500" placeholder="Nama barang atau jasa" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"></td>
        <td class="px-3 py-3 align-top"><input name="items[__INDEX__][kuantitas]" type="number" required min="0.001" step="0.001" value="1" class="po-qty w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"></td>
        <td class="px-3 py-3 align-top"><input name="items[__INDEX__][satuan]" type="text" maxlength="30" placeholder="pcs" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"></td>
        <td class="px-3 py-3 align-top"><input name="items[__INDEX__][harga_satuan]" type="number" required min="0" step="0.01" value="0" class="po-price w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"></td>
        <td class="po-line-total whitespace-nowrap px-3 py-4 text-right align-top font-semibold text-slate-800">Rp 0</td>
        <td class="px-3 py-3 text-right align-top"><button type="button" class="remove-po-item rounded-lg p-2 text-rose-600 hover:bg-rose-50" aria-label="Hapus baris"><i class="fas fa-trash"></i></button></td>
    </tr>
</template>

<script>
    (() => {
        const list = document.getElementById('po-items');
        const money = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
        const recalculate = () => {
            let subtotalCents = 0;
            list.querySelectorAll('.po-item-row').forEach((row) => {
                const quantity = Number(row.querySelector('.po-qty').value) || 0;
                const price = Number(row.querySelector('.po-price').value) || 0;
                const lineCents = Math.round(quantity * price * 100);
                subtotalCents += lineCents;
                row.querySelector('.po-line-total').textContent = money(lineCents / 100);
            });
            const discountCents = Math.round((Number(document.getElementById('diskon').value) || 0) * 100);
            const taxableCents = Math.max(0, subtotalCents - discountCents);
            const taxCents = Math.round(taxableCents * (Number(document.getElementById('pajak_persen').value) || 0) / 100);
            document.getElementById('po-subtotal').textContent = money(subtotalCents / 100);
            document.getElementById('po-tax').textContent = money(taxCents / 100);
            document.getElementById('po-total').textContent = money((taxableCents + taxCents) / 100);
        };
        const renumber = () => list.querySelectorAll('.po-item-row').forEach((row, index) => {
            row.querySelector('.po-item-number').textContent = index + 1;
            row.querySelectorAll('[name]').forEach((input) => {
                input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
            });
        });
        document.getElementById('add-po-item')?.addEventListener('click', () => {
            if (list.querySelectorAll('.po-item-row').length >= 100) return;
            list.insertAdjacentHTML('beforeend', document.getElementById('po-item-template').innerHTML.replaceAll('__INDEX__', list.children.length));
            renumber();
            recalculate();
        });
        list.addEventListener('click', (event) => {
            const button = event.target.closest('.remove-po-item');
            if (!button || list.querySelectorAll('.po-item-row').length === 1) return;
            button.closest('.po-item-row').remove();
            renumber();
            recalculate();
        });
        list.addEventListener('input', recalculate);
        document.getElementById('diskon').addEventListener('input', recalculate);
        document.getElementById('pajak_persen').addEventListener('input', recalculate);
        renumber();
        recalculate();
    })();
</script>
