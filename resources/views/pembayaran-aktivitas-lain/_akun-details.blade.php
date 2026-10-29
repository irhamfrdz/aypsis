@php
    $payment = $pembayaranAktivitasLain ?? null;
    $bankDirectionMode = $bankDirectionMode ?? false;
    $detailRows = old('akun_details', $payment?->akun_details);
    if (empty($detailRows)) {
        $detailRows = [['akun_coa_id' => $payment?->akun_coa_id ?? '', 'nominal' => $payment?->jumlah ?? '']];
    }
@endphp
<div id="akun_details_field" data-bank-direction-mode="{{ $bankDirectionMode ? 'true' : 'false' }}" class="md:col-span-2 {{ in_array(old('jenis_aktivitas', $payment?->jenis_aktivitas ?? ''), ['Pembayaran Lain Lain', 'Pembayaran Kendaraan']) ? '' : 'hidden' }}">
    <label class="block text-sm font-medium text-gray-700 mb-2">Rincian Akun Biaya <span class="text-red-500">*</span></label>
    <div id="akun_details_rows" class="space-y-2">
        @foreach($detailRows as $index => $detail)
            <div class="akun-detail-row flex flex-wrap items-start gap-2">
                <div class="min-w-48 flex-1">
                    <select name="akun_details[{{ $index }}][akun_coa_id]" class="akun-detail-select w-full px-3 py-2 border border-gray-300 rounded-md text-sm" @disabled(!in_array(old('jenis_aktivitas', $payment?->jenis_aktivitas ?? ''), ['Pembayaran Lain Lain', 'Pembayaran Kendaraan']))>
                        <option value="">Pilih Akun COA</option>
                        @foreach($akunBiaya as $akun)
                            <option value="{{ $akun->id }}" @selected(($detail['akun_coa_id'] ?? '') == $akun->id)>{{ $akun->nomor_akun }}@if($akun->kode_nomor && $akun->kode_nomor !== $akun->nomor_akun) ({{ $akun->kode_nomor }})@endif - {{ $akun->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
                @unless($bankDirectionMode)
                    <select name="akun_details[{{ $index }}][debit_kredit]" aria-label="Debit atau kredit untuk akun {{ $index + 1 }}" class="akun-detail-direction w-32 px-3 py-2 border border-gray-300 rounded-md text-sm" @disabled(!in_array(old('jenis_aktivitas', $payment?->jenis_aktivitas ?? ''), ['Pembayaran Lain Lain', 'Pembayaran Kendaraan']))>
                        @php $direction = $detail['debit_kredit'] ?? ($payment?->debit_kredit === 'debit' ? 'kredit' : 'debit'); @endphp
                        <option value="debit" @selected($direction === 'debit')>Debit</option>
                        <option value="kredit" @selected($direction === 'kredit')>Kredit</option>
                    </select>
                @endunless
                <input type="number" name="akun_details[{{ $index }}][nominal]" value="{{ $detail['nominal'] ?? '' }}" min="1" step="1" placeholder="Jumlah (Rp)" aria-label="Jumlah rupiah untuk akun {{ $index + 1 }}" class="akun-detail-nominal w-48 px-3 py-2 border border-gray-300 rounded-md text-sm text-right" @disabled(!in_array(old('jenis_aktivitas', $payment?->jenis_aktivitas ?? ''), ['Pembayaran Lain Lain', 'Pembayaran Kendaraan']))>
                <button type="button" class="remove-akun-detail px-3 py-2 text-red-700 border border-red-200 rounded-md text-sm">Hapus</button>
                <input type="text" name="akun_details[{{ $index }}][memo]" value="{{ $detail['memo'] ?? '' }}" maxlength="500" placeholder="Memo untuk akun ini (opsional)" aria-label="Memo untuk akun {{ $index + 1 }}" class="akun-detail-memo w-full px-3 py-2 border border-gray-300 rounded-md text-sm" @disabled(!in_array(old('jenis_aktivitas', $payment?->jenis_aktivitas ?? ''), ['Pembayaran Lain Lain', 'Pembayaran Kendaraan']))>
            </div>
        @endforeach
    </div>
    <button type="button" id="add_akun_detail" class="mt-2 px-3 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-md text-sm">Tambah Akun</button>
    <p class="mt-2 text-sm font-semibold text-gray-700"><span id="akun_details_total_label">Bank/Kas</span>: <span id="akun_details_total">Rp 0</span></p>
    <p id="akun_details_help" class="text-xs text-gray-500">{{ $bankDirectionMode ? 'Arah akun biaya mengikuti sisi berlawanan dari Bank/Kas.' : 'Bank/Kas menjadi lawan dari selisih debit dan kredit seluruh akun.' }}</p>
    @error('akun_details')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    @error('akun_details.*.akun_coa_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    @error('akun_details.*.debit_kredit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
        @unless($bankDirectionMode)
            <select name="akun_details[__INDEX__][debit_kredit]" aria-label="Debit atau kredit per akun" class="akun-detail-direction w-32 px-3 py-2 border border-gray-300 rounded-md text-sm" required>
                <option value="debit">Debit</option>
                <option value="kredit">Kredit</option>
            </select>
        @endunless
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
    const bankDirection = document.getElementById('debit_kredit');
    const bankDirectionMode = field.dataset.bankDirectionMode === 'true';
    let nextIndex = Math.max(-1, ...[...rows.querySelectorAll('.akun-detail-select')]
        .map(select => Number(select.name.match(/\[(\d+)\]/)?.[1] ?? -1))) + 1;

    const isVehicle = () => activity.value === 'Pembayaran Kendaraan';
    const isMulti = () => activity.value === 'Pembayaran Lain Lain' || isVehicle();
    const balance = () => [...rows.querySelectorAll('.akun-detail-row')]
        .reduce((sum, row) => sum + (bankDirectionMode
            ? (isVehicle() || bankDirection.value !== 'debit' ? 1 : -1)
            : (row.querySelector('.akun-detail-direction').value === 'debit' ? 1 : -1))
            * (Number(row.querySelector('.akun-detail-nominal').value) || 0), 0);
    const update = () => {
        const active = isMulti();
        field.classList.toggle('hidden', !active);
        document.getElementById('akun_details_total_label').textContent = isVehicle() ? 'Total biaya' : 'Bank/Kas';
        document.getElementById('akun_details_help').textContent = isVehicle()
            ? 'Jumlah pembayaran dihitung dari total biaya per akun. Untuk KIR, total ini harus sama dengan total nominal kendaraan.'
            : bankDirectionMode ? 'Arah akun biaya mengikuti sisi berlawanan dari Bank/Kas.'
                : 'Bank/Kas menjadi lawan dari selisih debit dan kredit seluruh akun.';
        rows.querySelectorAll('.akun-detail-direction').forEach(select => {
            if (isVehicle()) select.value = 'debit';
            select.classList.toggle('hidden', isVehicle());
        });
        rows.querySelectorAll('select, input').forEach(input => {
            input.disabled = !active;
            input.required = active && !input.classList.contains('akun-detail-memo');
        });
        if (active) window.initializeAkunDetailSelects?.();
        single.disabled = active || (activity.value === 'Pembayaran Kendaraan' && document.getElementById('lokasi_kendaraan_select').value === 'batam');
        single.required = !single.disabled;
        document.getElementById('akun_coa_field').classList.toggle('hidden', single.disabled);
        const net = balance();
        const bankLabel = bankDirectionMode && !isVehicle()
            ? (bankDirection.value ? bankDirection.value.toUpperCase() + ' ' : 'Pilih arah: ')
            : (isVehicle() ? '' : net > 0 ? 'Kredit ' : net < 0 ? 'Debit ' : '');
        document.getElementById('akun_details_total').textContent = bankLabel + 'Rp ' + Math.abs(net).toLocaleString('id-ID');
        if (active) {
            const amount = Math.abs(net);
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
        if (field.classList.contains('hidden') || !window.jQuery?.fn?.select2) return;
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
        return details.length > 0 && ids.every(Boolean) && balance() !== 0
            && (!bankDirectionMode || isVehicle() || ['debit', 'kredit'].includes(bankDirection.value))
            && (bank.disabled || !ids.includes(bank.value))
            && details.every(row => {
                const value = Number(row.querySelector('.akun-detail-nominal').value);
                return Number.isSafeInteger(value) && value > 0
                    && (bankDirectionMode ? true : isVehicle() ? row.querySelector('.akun-detail-direction').value === 'debit'
                        : ['debit', 'kredit'].includes(row.querySelector('.akun-detail-direction').value));
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
    activity.addEventListener('change', update);
    bankDirection.addEventListener('change', update);
    document.getElementById('pembayaran_form').addEventListener('submit', update, true);
    update();
});
</script>
@endpush
