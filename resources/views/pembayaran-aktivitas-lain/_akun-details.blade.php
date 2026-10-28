@php
    $payment = $pembayaranAktivitasLain ?? null;
    $detailRows = old('akun_details', $payment?->akun_details);
    if (empty($detailRows)) {
        $detailRows = [['akun_coa_id' => $payment?->akun_coa_id ?? '', 'nominal' => $payment?->jumlah ?? '']];
    }
@endphp
<div id="akun_details_field" class="md:col-span-2 {{ old('jenis_aktivitas', $payment?->jenis_aktivitas ?? '') === 'Pembayaran Lain Lain' ? '' : 'hidden' }}">
    <label class="block text-sm font-medium text-gray-700 mb-2">Rincian Akun Biaya <span class="text-red-500">*</span></label>
    <div id="akun_details_rows" class="space-y-2">
        @foreach($detailRows as $index => $detail)
            <div class="akun-detail-row flex flex-wrap items-start gap-2">
                <div class="min-w-48 flex-1">
                    <select name="akun_details[{{ $index }}][akun_coa_id]" class="akun-detail-select w-full px-3 py-2 border border-gray-300 rounded-md text-sm" @disabled(old('jenis_aktivitas', $payment?->jenis_aktivitas ?? '') !== 'Pembayaran Lain Lain')>
                        <option value="">Pilih Akun COA</option>
                        @foreach($akunBiaya as $akun)
                            <option value="{{ $akun->id }}" @selected(($detail['akun_coa_id'] ?? '') == $akun->id)>{{ $akun->nomor_akun }}@if($akun->kode_nomor && $akun->kode_nomor !== $akun->nomor_akun) ({{ $akun->kode_nomor }})@endif - {{ $akun->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="number" name="akun_details[{{ $index }}][nominal]" value="{{ $detail['nominal'] ?? '' }}" min="1" step="1" placeholder="Jumlah (Rp)" aria-label="Jumlah rupiah untuk akun {{ $index + 1 }}" class="akun-detail-nominal w-48 px-3 py-2 border border-gray-300 rounded-md text-sm text-right" @disabled(old('jenis_aktivitas', $payment?->jenis_aktivitas ?? '') !== 'Pembayaran Lain Lain')>
                <button type="button" class="remove-akun-detail px-3 py-2 text-red-700 border border-red-200 rounded-md text-sm">Hapus</button>
                <input type="text" name="akun_details[{{ $index }}][memo]" value="{{ $detail['memo'] ?? '' }}" maxlength="500" placeholder="Memo untuk akun ini (opsional)" aria-label="Memo untuk akun {{ $index + 1 }}" class="akun-detail-memo w-full px-3 py-2 border border-gray-300 rounded-md text-sm" @disabled(old('jenis_aktivitas', $payment?->jenis_aktivitas ?? '') !== 'Pembayaran Lain Lain')>
            </div>
        @endforeach
    </div>
    <button type="button" id="add_akun_detail" class="mt-2 px-3 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-md text-sm">Tambah Akun</button>
    <p class="mt-2 text-sm font-semibold text-gray-700">Total: <span id="akun_details_total">Rp 0</span></p>
    <p class="text-xs text-gray-500">Jumlah pembayaran dihitung dari seluruh akun. Setiap akun akan memiliki baris jurnal tersendiri.</p>
    @error('akun_details')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    @error('akun_details.*.akun_coa_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    @error('akun_details.*.nominal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    @error('akun_details.*.memo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<template id="akun_detail_template">
    <div class="akun-detail-row flex flex-wrap items-start gap-2">
        <div class="min-w-48 flex-1">
            <select name="akun_details[__INDEX__][akun_coa_id]" class="akun-detail-select w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required>
                <option value="">Pilih Akun COA</option>
                @foreach($akunBiaya as $akun)
                    <option value="{{ $akun->id }}">{{ $akun->nomor_akun }}@if($akun->kode_nomor && $akun->kode_nomor !== $akun->nomor_akun) ({{ $akun->kode_nomor }})@endif - {{ $akun->nama_akun }}</option>
                @endforeach
            </select>
        </div>
        <input type="number" name="akun_details[__INDEX__][nominal]" min="1" step="1" placeholder="Jumlah (Rp)" aria-label="Jumlah rupiah per akun" class="akun-detail-nominal w-48 px-3 py-2 border border-gray-300 rounded-md text-sm text-right" required>
        <button type="button" class="remove-akun-detail px-3 py-2 text-red-700 border border-red-200 rounded-md text-sm">Hapus</button>
        <input type="text" name="akun_details[__INDEX__][memo]" maxlength="500" placeholder="Memo untuk akun ini (opsional)" aria-label="Memo per akun" class="akun-detail-memo w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
    </div>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const activity = document.getElementById('jenis_aktivitas');
    const field = document.getElementById('akun_details_field');
    const rows = document.getElementById('akun_details_rows');
    const display = document.getElementById('jumlah_input_display');
    const hidden = document.getElementById('jumlah_input_value');
    const single = document.getElementById('akun_coa_select');
    const bank = document.getElementById('akun_bank_select');
    let nextIndex = Math.max(-1, ...[...rows.querySelectorAll('.akun-detail-select')]
        .map(select => Number(select.name.match(/\[(\d+)\]/)?.[1] ?? -1))) + 1;

    const isMulti = () => activity.value === 'Pembayaran Lain Lain';
    const total = () => [...rows.querySelectorAll('.akun-detail-nominal')]
        .reduce((sum, input) => sum + (Number(input.value) || 0), 0);
    const update = () => {
        const active = isMulti();
        field.classList.toggle('hidden', !active);
        rows.querySelectorAll('select, input').forEach(input => {
            input.disabled = !active;
            input.required = active && !input.classList.contains('akun-detail-memo');
        });
        single.disabled = active || (activity.value === 'Pembayaran Kendaraan' && document.getElementById('lokasi_kendaraan_select').value === 'batam');
        single.required = !single.disabled;
        document.getElementById('akun_coa_field').classList.toggle('hidden', single.disabled);
        document.getElementById('akun_details_total').textContent = 'Rp ' + total().toLocaleString('id-ID');
        if (active) {
            const amount = total();
            hidden.value = amount || '';
            display.value = amount ? amount.toLocaleString('id-ID') : '';
            display.readOnly = true;
            display.classList.add('bg-gray-100');
            display.dataset.akunDetails = '1';
            display.dispatchEvent(new Event('input', { bubbles: true }));
            document.getElementById('journal_preview').classList.add('hidden');
        } else if (display.dataset.akunDetails) {
            display.readOnly = false;
            display.classList.remove('bg-gray-100');
            delete display.dataset.akunDetails;
        }
    };
    window.updateAkunDetails = update;
    window.initializeAkunDetailSelects = (container = rows) => {
        if (!window.jQuery?.fn?.select2) return;
        window.jQuery(container).find('.akun-detail-select').not('.select2-hidden-accessible').select2({
            placeholder: 'Cari akun COA',
            allowClear: true,
            minimumResultsForSearch: 0,
            width: '100%'
        });
    };

    window.validateAkunDetails = () => {
        if (!isMulti()) return true;
        const details = [...rows.querySelectorAll('.akun-detail-row')];
        const ids = details.map(row => row.querySelector('.akun-detail-select').value);
        return details.length > 0 && ids.every(Boolean) && new Set(ids).size === ids.length
            && !ids.includes(bank.value)
            && details.every(row => {
                const value = Number(row.querySelector('.akun-detail-nominal').value);
                return Number.isSafeInteger(value) && value > 0;
            });
    };

    rows.addEventListener('input', update);
    rows.addEventListener('change', update);
    rows.addEventListener('click', event => {
        if (!event.target.closest('.remove-akun-detail') || rows.children.length === 1) return;
        const row = event.target.closest('.akun-detail-row');
        const select = row.querySelector('.akun-detail-select');
        if (select.classList.contains('select2-hidden-accessible')) window.jQuery(select).select2('destroy');
        row.remove();
        update();
    });
    document.getElementById('add_akun_detail').addEventListener('click', () => {
        rows.insertAdjacentHTML('beforeend', document.getElementById('akun_detail_template').innerHTML.replaceAll('__INDEX__', nextIndex++));
        window.initializeAkunDetailSelects(rows.lastElementChild);
        update();
    });
    activity.addEventListener('change', () => setTimeout(update, 0));
    document.getElementById('pembayaran_form').addEventListener('submit', update, true);
    update();
});
</script>
@endpush
