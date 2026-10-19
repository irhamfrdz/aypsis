<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label for="size_kontainer" class="mb-1 block text-sm font-medium text-gray-700">Ukuran Kontainer <span class="text-red-500">*</span></label>
        <select id="size_kontainer" name="size_kontainer" required class="w-full rounded-lg border-gray-300 text-sm">
            @foreach (['20', '40', '45'] as $size)
                <option value="{{ $size }}" @selected(old('size_kontainer', $pricelist->size_kontainer ?? '20') === $size)>{{ $size }}'</option>
            @endforeach
        </select>
        @error('size_kontainer')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="status_kontainer" class="mb-1 block text-sm font-medium text-gray-700">Status Kontainer <span class="text-red-500">*</span></label>
        <select id="status_kontainer" name="status_kontainer" required class="w-full rounded-lg border-gray-300 text-sm">
            <option value="full" @selected(old('status_kontainer', $pricelist->status_kontainer ?? 'full') === 'full')>Full</option>
            <option value="empty" @selected(old('status_kontainer', $pricelist->status_kontainer ?? 'full') === 'empty')>Empty</option>
        </select>
        @error('status_kontainer')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status Pricelist <span class="text-red-500">*</span></label>
        <select id="status" name="status" required class="w-full rounded-lg border-gray-300 text-sm">
            <option value="aktif" @selected(old('status', $pricelist->status ?? 'aktif') === 'aktif')>Aktif</option>
            <option value="non-aktif" @selected(old('status', $pricelist->status ?? 'aktif') === 'non-aktif')>Nonaktif</option>
        </select>
    </div>
    <div class="md:col-span-2 rounded-lg border border-gray-200 p-4">
        <div class="mb-3 flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Tarif Massa</h3>
                <p class="text-xs text-gray-500">Tambahkan satu atau lebih tingkat massa dan tarifnya.</p>
            </div>
            <button type="button" id="add-tarif-massa" class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700">Tambah Massa</button>
        </div>
        <div id="tarif-massa-rows" class="space-y-2">
            @php
                $tarifMassaRows = old('tarif_massa');
                if ($tarifMassaRows === null) {
                    $tarifMassaRows = isset($pricelist) ? $pricelist->tarifMassa->map(fn ($item) => ['massa' => $item->massa, 'tarif' => $item->tarif])->all() : [['massa' => 1, 'tarif' => 0]];
                }
            @endphp
            @foreach ($tarifMassaRows as $index => $tarifMassa)
                <div class="tarif-massa-row grid grid-cols-1 items-end gap-2 sm:grid-cols-[1fr_1fr_auto]">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Massa</label>
                        <input type="number" name="tarif_massa[{{ $index }}][massa]" value="{{ $tarifMassa['massa'] ?? '' }}" required min="0.01" step="0.01" placeholder="Contoh: 1" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Tarif (Rp)</label>
                        <input type="number" name="tarif_massa[{{ $index }}][tarif]" value="{{ $tarifMassa['tarif'] ?? '' }}" required min="0" step="0.01" placeholder="Tarif" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <button type="button" class="remove-tarif-massa rounded-lg border border-red-200 px-3 py-2 text-sm text-red-700 hover:bg-red-50">Hapus</button>
                </div>
            @endforeach
        </div>
        @error('tarif_massa')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
        @error('tarif_massa.*.massa')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
        @error('tarif_massa.*.tarif')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label for="keterangan" class="mb-1 block text-sm font-medium text-gray-700">Keterangan</label>
        <textarea id="keterangan" name="keterangan" rows="3" maxlength="2000" class="w-full rounded-lg border-gray-300 text-sm">{{ old('keterangan', $pricelist->keterangan ?? '') }}</textarea>
        @error('keterangan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<template id="tarif-massa-template">
    <div class="tarif-massa-row grid grid-cols-1 items-end gap-2 sm:grid-cols-[1fr_1fr_auto]">
        <div><label class="mb-1 block text-xs font-medium text-gray-600">Massa</label><input type="number" data-name="massa" required min="0.01" step="0.01" placeholder="Contoh: 1" class="w-full rounded-lg border-gray-300 text-sm"></div>
        <div><label class="mb-1 block text-xs font-medium text-gray-600">Tarif (Rp)</label><input type="number" data-name="tarif" required min="0" step="0.01" placeholder="Tarif" class="w-full rounded-lg border-gray-300 text-sm"></div>
        <button type="button" class="remove-tarif-massa rounded-lg border border-red-200 px-3 py-2 text-sm text-red-700 hover:bg-red-50">Hapus</button>
    </div>
</template>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = document.getElementById('tarif-massa-rows');
    const template = document.getElementById('tarif-massa-template');
    let nextIndex = rows.querySelectorAll('.tarif-massa-row').length;

    document.getElementById('add-tarif-massa').addEventListener('click', () => {
        const row = template.content.firstElementChild.cloneNode(true);
        row.querySelectorAll('[data-name]').forEach(input => {
            input.name = `tarif_massa[${nextIndex}][${input.dataset.name}]`;
        });
        nextIndex++;
        rows.appendChild(row);
    });

    rows.addEventListener('click', event => {
        if (event.target.closest('.remove-tarif-massa')) {
            if (rows.querySelectorAll('.tarif-massa-row').length > 1) {
                event.target.closest('.tarif-massa-row').remove();
            }
        }
    });
});
</script>
