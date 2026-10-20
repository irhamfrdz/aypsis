@php
    $accountRows = old('akun_items');
    if ($accountRows === null) {
        $accountRows = isset($item) && $item->akunItems->isNotEmpty()
            ? $item->akunItems->map(fn ($line) => [
                'akun_coa_id' => $line->akun_coa_id,
                'nominal' => $line->nominal,
                'keterangan' => $line->keterangan,
            ])->all()
            : [[
                'akun_coa_id' => $item->akun_coa_id ?? '',
                'nominal' => $item->total_tagihan_setelah_penyesuaian ?? '',
                'keterangan' => '',
            ]];
    }
    $accountRows = $accountRows ?: [['akun_coa_id' => '', 'nominal' => '', 'keterangan' => '']];
@endphp

<div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
        <div>
            <h4 class="text-sm font-semibold">Rincian Akun Biaya</h4>
            <p class="text-xs text-gray-500">Tambahkan satu baris untuk setiap akun seperti di Accurate. Total nominal akun harus sama dengan total akhir.</p>
        </div>
        <button type="button" id="addAkunItem" class="px-3 py-2 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700">+ Tambah Akun</button>
    </div>
    @error('akun_items')<p class="mb-2 text-xs text-red-600">{{ $message }}</p>@enderror
    <div id="akunItems" class="space-y-2">
        @foreach($accountRows as $index => $line)
        <div class="akun-item grid grid-cols-1 md:grid-cols-12 gap-2 items-end p-2 bg-white border rounded">
            <div class="md:col-span-4">
                <label class="block text-xs font-medium text-gray-700 mb-1">Akun Biaya *</label>
                <select name="akun_items[{{ $index }}][akun_coa_id]" class="akun-select w-full rounded border-gray-300 text-sm p-2" required>
                    <option value="">-- Pilih Akun --</option>
                    @foreach($akunBiaya as $akun)
                        <option value="{{ $akun->id }}" @selected(($line['akun_coa_id'] ?? '') == $akun->id)>{{ $akun->nomor_akun ? $akun->nomor_akun.' - ' : '' }}{{ $akun->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-medium text-gray-700 mb-1">Nominal *</label>
                <input type="number" name="akun_items[{{ $index }}][nominal]" class="akun-nominal w-full rounded border-gray-300 text-sm p-2 text-right" min="0.01" step="0.01" value="{{ $line['nominal'] ?? '' }}" required>
            </div>
            <div class="md:col-span-4">
                <label class="block text-xs font-medium text-gray-700 mb-1">Catatan / Notes</label>
                <input type="text" name="akun_items[{{ $index }}][keterangan]" class="w-full rounded border-gray-300 text-sm p-2" maxlength="255" value="{{ $line['keterangan'] ?? '' }}" placeholder="Contoh: Parkir Motor B.4192 BNP">
            </div>
            <div class="md:col-span-1">
                <button type="button" class="remove-akun-item w-full px-2 py-2 bg-red-50 text-red-700 rounded text-xs hover:bg-red-100">Hapus</button>
            </div>
        </div>
        @endforeach
    </div>
    <div class="flex flex-wrap justify-end gap-6 mt-3 text-sm">
        <span>Total akun: <strong id="totalAkunItems">Rp 0</strong></span>
        <span id="selisihAkunItems" class="font-semibold text-red-600"></span>
    </div>
</div>

<template id="akunItemTemplate">
    <div class="akun-item grid grid-cols-1 md:grid-cols-12 gap-2 items-end p-2 bg-white border rounded">
        <div class="md:col-span-4">
            <label class="block text-xs font-medium text-gray-700 mb-1">Akun Biaya *</label>
            <select name="akun_items[__INDEX__][akun_coa_id]" class="akun-select w-full rounded border-gray-300 text-sm p-2" required>
                <option value="">-- Pilih Akun --</option>
                @foreach($akunBiaya as $akun)
                    <option value="{{ $akun->id }}">{{ $akun->nomor_akun ? $akun->nomor_akun.' - ' : '' }}{{ $akun->nama_akun }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-3">
            <label class="block text-xs font-medium text-gray-700 mb-1">Nominal *</label>
            <input type="number" name="akun_items[__INDEX__][nominal]" class="akun-nominal w-full rounded border-gray-300 text-sm p-2 text-right" min="0.01" step="0.01" required>
        </div>
        <div class="md:col-span-4">
            <label class="block text-xs font-medium text-gray-700 mb-1">Catatan / Notes</label>
            <input type="text" name="akun_items[__INDEX__][keterangan]" class="w-full rounded border-gray-300 text-sm p-2" maxlength="255" placeholder="Contoh: Parkir Motor B.4192 BNP">
        </div>
        <div class="md:col-span-1">
            <button type="button" class="remove-akun-item w-full px-2 py-2 bg-red-50 text-red-700 rounded text-xs hover:bg-red-100">Hapus</button>
        </div>
    </div>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('akunItems');
    let nextIndex = {{ count($accountRows) ? max(array_keys($accountRows)) + 1 : 0 }};
    const rupiah = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 });

    window.updateAkunTotal = function () {
        const total = [...container.querySelectorAll('.akun-nominal')]
            .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
        const final = parseFloat(document.getElementById('total_akhir').value) || 0;
        document.getElementById('totalAkunItems').textContent = 'Rp ' + rupiah.format(total);
        const difference = final - total;
        const label = document.getElementById('selisihAkunItems');
        label.textContent = Math.abs(difference) > 0.01 ? 'Selisih: Rp ' + rupiah.format(difference) : 'Sesuai total akhir';
        label.className = Math.abs(difference) > 0.01 ? 'font-semibold text-red-600' : 'font-semibold text-green-600';
    };

    document.getElementById('addAkunItem').addEventListener('click', function () {
        container.insertAdjacentHTML('beforeend', document.getElementById('akunItemTemplate').innerHTML.replaceAll('__INDEX__', nextIndex++));
        window.updateAkunTotal();
    });
    container.addEventListener('click', function (event) {
        if (event.target.closest('.remove-akun-item') && container.querySelectorAll('.akun-item').length > 1) {
            event.target.closest('.akun-item').remove();
            window.updateAkunTotal();
        }
    });
    container.addEventListener('input', window.updateAkunTotal);
    window.updateAkunTotal();
});
</script>
@endpush
