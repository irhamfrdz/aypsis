@php
    $payment = $pembayaran ?? null;
    $savedItems = $payment?->akunItems->map(fn ($item) => [
        'akun_coa_id' => $item->akun_coa_id,
        'nominal' => $item->nominal,
        'memo' => $item->memo,
    ])->all();
    $rows = old('akun_items', $savedItems ?: [[
        'akun_coa_id' => $payment?->akun_coa_id,
        'nominal' => $payment?->total_setelah_penyesuaian ?? ($totalPembayaran ?? null),
        'memo' => '',
    ]]);
@endphp
<div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
    <div class="mb-2 flex items-center justify-between gap-2">
        <div>
            <h4 class="text-sm font-semibold text-gray-800">Rincian Akun Biaya</h4>
            <p class="text-xs text-gray-500">Total nominal akun harus sama dengan total akhir pembayaran.</p>
        </div>
        <button type="button" id="add-cat-akun" class="rounded-md border border-indigo-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-50">+ Tambah Akun</button>
    </div>
    <div id="cat-akun-rows" class="space-y-2">
        @foreach($rows as $index => $row)
            <div class="cat-akun-row grid gap-2 rounded-md border border-gray-200 bg-white p-3 md:grid-cols-12">
                <div class="md:col-span-5">
                    <label class="{{ $labelClasses }}">Akun Biaya <span class="text-red-600">*</span></label>
                    <select name="akun_items[{{ $index }}][akun_coa_id]" class="cat-akun-select {{ $inputClasses }}" required>
                        <option value="">-- Pilih Akun COA --</option>
                        @foreach($akunBiaya as $akun)
                            <option value="{{ $akun->id }}" @selected((string) ($row['akun_coa_id'] ?? '') === (string) $akun->id)>{{ $akun->nomor_akun }} - {{ $akun->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="{{ $labelClasses }}">Nominal (Rp) <span class="text-red-600">*</span></label>
                    <input type="number" name="akun_items[{{ $index }}][nominal]" class="cat-akun-nominal {{ $inputClasses }}" min="0.01" step="0.01" value="{{ $row['nominal'] ?? '' }}" required>
                </div>
                <div class="md:col-span-4">
                    <label class="{{ $labelClasses }}">Memo</label>
                    <input type="text" name="akun_items[{{ $index }}][memo]" class="{{ $inputClasses }}" maxlength="500" value="{{ $row['memo'] ?? '' }}" placeholder="Memo akun (opsional)">
                </div>
                <div class="flex items-end md:col-span-1">
                    <button type="button" class="remove-cat-akun rounded-md border border-red-200 px-3 py-2 text-xs font-medium text-red-700 hover:bg-red-50">Hapus</button>
                </div>
            </div>
        @endforeach
    </div>
    <p class="mt-2 text-sm font-semibold text-gray-700">Total Akun: <span id="cat-akun-total">Rp 0</span></p>
    @error('akun_items')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    @error('akun_items.*.akun_coa_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    @error('akun_items.*.nominal')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    @error('akun_items.*.memo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

<template id="cat-akun-template">
    <div class="cat-akun-row grid gap-2 rounded-md border border-gray-200 bg-white p-3 md:grid-cols-12">
        <div class="md:col-span-5">
            <label class="{{ $labelClasses }}">Akun Biaya <span class="text-red-600">*</span></label>
            <select name="akun_items[__INDEX__][akun_coa_id]" class="cat-akun-select {{ $inputClasses }}" required>
                <option value="">-- Pilih Akun COA --</option>
                @foreach($akunBiaya as $akun)
                    <option value="{{ $akun->id }}">{{ $akun->nomor_akun }} - {{ $akun->nama_akun }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="{{ $labelClasses }}">Nominal (Rp) <span class="text-red-600">*</span></label>
            <input type="number" name="akun_items[__INDEX__][nominal]" class="cat-akun-nominal {{ $inputClasses }}" min="0.01" step="0.01" required>
        </div>
        <div class="md:col-span-4">
            <label class="{{ $labelClasses }}">Memo</label>
            <input type="text" name="akun_items[__INDEX__][memo]" class="{{ $inputClasses }}" maxlength="500" placeholder="Memo akun (opsional)">
        </div>
        <div class="flex items-end md:col-span-1">
            <button type="button" class="remove-cat-akun rounded-md border border-red-200 px-3 py-2 text-xs font-medium text-red-700 hover:bg-red-50">Hapus</button>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = document.getElementById('cat-akun-rows');
    const total = document.getElementById('cat-akun-total');
    const finalInput = document.getElementById('total_tagihan_setelah_penyesuaian') || document.getElementById('total_setelah_penyesuaian');
    let nextIndex = Math.max(-1, ...[...rows.querySelectorAll('.cat-akun-select')]
        .map(select => Number(select.name.match(/\[(\d+)\]/)?.[1] ?? -1))) + 1;
    const sum = () => [...rows.querySelectorAll('.cat-akun-nominal')]
        .reduce((value, input) => value + (Number(input.value) || 0), 0);
    const finalAmount = () => {
        const value = finalInput?.value || '';
        return finalInput?.id === 'total_tagihan_setelah_penyesuaian'
            ? Number(value.replace(/\./g, '').replace(',', '.')) || 0
            : Number(value) || 0;
    };
    const update = () => {
        total.textContent = 'Rp ' + sum().toLocaleString('id-ID');
        total.classList.toggle('text-red-600', Math.round(sum() * 100) !== Math.round(finalAmount() * 100));
    };
    const initializeAccountSearch = (scope = rows) => {
        if (!window.jQuery || !jQuery.fn.select2) return;
        jQuery(scope).find('.cat-akun-select').addBack('.cat-akun-select').each(function () {
            const select = jQuery(this);
            if (select.hasClass('select2-hidden-accessible')) return;
            select.select2({
                width: '100%',
                placeholder: '-- Pilih Akun COA --',
                allowClear: true,
            });
        });
    };
    initializeAccountSearch();
    window.validateCatAkunItems = () => {
        const items = [...rows.querySelectorAll('.cat-akun-row')];
        const ids = items.map(row => row.querySelector('.cat-akun-select').value);
        return items.length > 0 && ids.every(Boolean)
            && items.every(row => Number(row.querySelector('.cat-akun-nominal').value) > 0)
            && Math.round(sum() * 100) === Math.round(finalAmount() * 100);
    };
    document.getElementById('add-cat-akun').addEventListener('click', () => {
        rows.insertAdjacentHTML('beforeend', document.getElementById('cat-akun-template').innerHTML.replaceAll('__INDEX__', nextIndex++));
        initializeAccountSearch(rows.lastElementChild);
        update();
    });
    rows.addEventListener('click', event => {
        if (!event.target.closest('.remove-cat-akun') || rows.children.length === 1) return;
        event.target.closest('.cat-akun-row').remove();
        update();
    });
    rows.addEventListener('input', update);
    rows.addEventListener('change', update);
    finalInput?.addEventListener('input', update);
    update();
});
</script>
