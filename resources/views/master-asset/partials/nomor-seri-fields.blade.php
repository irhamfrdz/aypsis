@php
    $serialRows = old('nomor_seri_items', isset($asset) ? $asset->daftar_nomor_seri : [['nomor' => old('nomor_seri'), 'ukuran' => '']]);
    if (empty($serialRows)) $serialRows = [['nomor' => '', 'ukuran' => '']];
@endphp
<div class="md:col-span-3" id="asset-serial-fields">
    <div class="flex items-center justify-between gap-3 mb-2">
        <span class="block text-xs font-semibold text-gray-700">Nomor Seri / Nomor Mesin</span>
        <button type="button" id="add-asset-serial" class="px-3 py-2 text-xs font-semibold text-blue-700 bg-blue-50 rounded-xl">+ Tambah Nomor</button>
    </div>
    <p class="text-xs text-gray-500 mb-2">Tambahkan satu baris untuk setiap nomor. Isi ukuran bila berbeda, misalnya 5 HP atau 10 HP.</p>
    <div id="asset-serial-rows" class="space-y-2">
        @foreach($serialRows as $index => $serial)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end" data-serial-row>
                <label class="block text-xs text-gray-700">Nomor Seri / Nomor Mesin
                    <input type="text" name="nomor_seri_items[{{ $index }}][nomor]" value="{{ $serial['nomor'] ?? '' }}" maxlength="100" class="mt-1 w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </label>
                <label class="block text-xs text-gray-700">Ukuran (opsional)
                    <input type="text" name="nomor_seri_items[{{ $index }}][ukuran]" value="{{ $serial['ukuran'] ?? '' }}" maxlength="100" class="mt-1 w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </label>
                <button type="button" data-remove-serial class="justify-self-start px-3 py-2 text-xs text-red-700 bg-red-50 rounded-xl">Hapus</button>
            </div>
        @endforeach
    </div>
</div>
<template id="asset-serial-template">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end" data-serial-row>
        <label class="block text-xs text-gray-700">Nomor Seri / Nomor Mesin
            <input type="text" data-field="nomor" maxlength="100" class="mt-1 w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
        </label>
        <label class="block text-xs text-gray-700">Ukuran (opsional)
            <input type="text" data-field="ukuran" maxlength="100" class="mt-1 w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
        </label>
        <button type="button" data-remove-serial class="justify-self-start px-3 py-2 text-xs text-red-700 bg-red-50 rounded-xl">Hapus</button>
    </div>
</template>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rows = document.getElementById('asset-serial-rows');
    let nextIndex = Math.max(-1, ...Array.from(rows.querySelectorAll('input[name]'), input => Number(input.name.match(/\[(\d+)\]/)[1]))) + 1;
    document.getElementById('add-asset-serial').addEventListener('click', function () {
        const fragment = document.getElementById('asset-serial-template').content.cloneNode(true);
        fragment.querySelectorAll('input').forEach(input => {
            input.name = `nomor_seri_items[${nextIndex}][${input.dataset.field}]`;
        });
        nextIndex++;
        const firstInput = fragment.querySelector('input');
        rows.appendChild(fragment);
        firstInput.focus();
    });
    rows.addEventListener('click', function (event) {
        const button = event.target.closest('[data-remove-serial]');
        if (!button) return;
        const row = button.closest('[data-serial-row]');
        if (rows.children.length > 1) {
            row.remove();
        } else {
            row.querySelectorAll('input').forEach(input => { input.value = ''; });
            row.querySelector('input').focus();
        }
    });
});
</script>
@endpush
