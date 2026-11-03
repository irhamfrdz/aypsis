@php
    $rows = old('akun_items', isset($pembayaran) && $pembayaran->akunItems->isNotEmpty()
        ? $pembayaran->akunItems->map(fn ($item) => ['akun_coa_id' => $item->akun_coa_id, 'nominal' => $item->nominal, 'memo' => $item->memo])->all()
        : [['akun_coa_id' => $pembayaran->akun_coa_id ?? '', 'nominal' => isset($pembayaran) ? (float) $pembayaran->total_setelah_penyesuaian + (float) $pembayaran->dp_amount : '', 'memo' => '']]);
    $dpNominal = isset($pembayaran) ? (float) $pembayaran->dp_amount : (float) $selectedDps->sum('dp_amount');
    $dpAccountDefault = isset($pembayaran) ? $pembayaran->defaultAkunDpId() : ($selectedDps->pluck('akun_coa_id')->filter()->unique()->count() === 1 ? $selectedDps->pluck('akun_coa_id')->filter()->first() : null);
@endphp
<div class="bg-white border border-gray-200 rounded-lg p-3 space-y-3" id="akun-items-section" data-dp="{{ $dpNominal }}">
    <div class="flex items-center justify-between">
        <div>
            <h4 class="text-sm font-semibold text-gray-800">Rincian Akun Biaya</h4>
            <p class="text-xs text-gray-500">Isi nominal per akun sebelum potongan DP. Jumlahnya harus sama dengan total biaya ditambah penyesuaian.</p>
        </div>
        <button type="button" id="add-akun-item" class="text-xs text-indigo-600 hover:text-indigo-800">+ Tambah Akun</button>
    </div>
    <div id="akun-items-list" class="space-y-2">
        @foreach($rows as $index => $row)
            <div class="akun-item-row space-y-2">
                <div class="flex flex-wrap gap-2 items-center">
                    <select name="akun_items[{{ $index }}][akun_coa_id]" class="akun-item-select {{ $inputClasses }} flex-1 min-w-48" required>
                        <option value="">-- Pilih Akun Biaya --</option>
                        @foreach($akunBiaya as $akun)
                            <option value="{{ $akun->id }}" {{ (string) ($row['akun_coa_id'] ?? '') === (string) $akun->id ? 'selected' : '' }}>{{ $akun->nomor_akun }} - {{ $akun->nama_akun }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="akun_items[{{ $index }}][nominal]" class="akun-item-nominal {{ $inputClasses }} w-40" min="0.01" step="0.01" placeholder="Nominal sebelum DP" value="{{ $row['nominal'] ?? '' }}" required>
                    <button type="button" class="remove-akun-item text-xs text-red-600">Hapus</button>
                </div>
                <textarea name="akun_items[{{ $index }}][memo]" class="akun-item-memo {{ $inputClasses }}" rows="2" maxlength="2000" placeholder="Memo/catatan untuk akun ini (opsional)">{{ $row['memo'] ?? '' }}</textarea>
            </div>
        @endforeach
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-xs border-t pt-2">
        <div>Total akun sebelum DP: <strong id="akun-total">Rp 0</strong></div>
        <div>Potongan DP: <strong>Rp {{ number_format($dpNominal, 0, ',', '.') }}</strong></div>
        <div>Yang dibayar dari Bank/Kas: <strong id="akun-kas">Rp 0</strong></div>
    </div>
    @if($dpNominal > 0)
        <div>
            <label for="akun_dp_id" class="{{ $labelClasses }}">Akun Potongan DP <span class="text-red-500">*</span></label>
            <select name="akun_dp_id" id="akun_dp_id" class="{{ $inputClasses }}" required>
                <option value="">-- Pilih Akun DP --</option>
                @foreach($akunBiaya as $akun)
                    <option value="{{ $akun->id }}" {{ (string) old('akun_dp_id', $dpAccountDefault) === (string) $akun->id ? 'selected' : '' }}>{{ $akun->nomor_akun }} - {{ $akun->nama_akun }}</option>
                @endforeach
            </select>
        </div>
    @endif
</div>
<style>
    .akun-item-row .select2-container {
        flex: 1 1 12rem;
        width: auto !important;
        min-width: 12rem;
    }
</style>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const section = document.getElementById('akun-items-section');
    const list = document.getElementById('akun-items-list');
    const rowTemplate = list.querySelector('.akun-item-row').cloneNode(true);
    const dp = Number(section.dataset.dp) || 0;
    const format = value => 'Rp ' + value.toLocaleString('id-ID');
    function initializeSearchableAccounts(root = section) {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') return;

        jQuery(root).find('.akun-item-select, #akun_dp_id').each(function () {
            if (jQuery(this).hasClass('select2-hidden-accessible')) return;
            jQuery(this).select2({
                width: '100%',
                placeholder: this.id === 'akun_dp_id' ? '-- Pilih Akun DP --' : '-- Pilih Akun Biaya --'
            });
        });
    }
    window.initializePranotaObAkunSelects = initializeSearchableAccounts;
    const expected = () => {
        const biayaPranota = document.getElementById('total-biaya');
        if (biayaPranota) {
            const biaya = Number(biayaPranota.textContent.replace(/[^0-9-]/g, '')) || 0;
            const penyesuaian = Number((document.getElementById('total_tagihan_penyesuaian')?.value || '0').replace(/\./g, '').replace(',', '.')) || 0;
            return biaya + penyesuaian;
        }
        const totalInput = document.getElementById('total_setelah_penyesuaian');
        return (Number((totalInput?.value || '0').replace(/\./g, '').replace(',', '.')) || 0) + dp;
    };
    let autoNominal = list.children.length === 1 && !list.querySelector('.akun-item-nominal').value;
    function update() {
        const rows = [...list.querySelectorAll('.akun-item-row')];
        if (autoNominal && rows.length === 1 && expected() > 0) {
            rows[0].querySelector('.akun-item-nominal').value = expected();
        }
        rows.forEach((row, index) => {
            row.querySelector('.akun-item-select').name = `akun_items[${index}][akun_coa_id]`;
            row.querySelector('.akun-item-nominal').name = `akun_items[${index}][nominal]`;
            row.querySelector('.akun-item-memo').name = `akun_items[${index}][memo]`;
        });
        const total = rows.reduce((sum, row) => sum + (Number(row.querySelector('.akun-item-nominal').value) || 0), 0);
        document.getElementById('akun-total').textContent = format(total);
        document.getElementById('akun-kas').textContent = format(total - dp);
        document.getElementById('akun-total').classList.toggle('text-red-600', Math.abs(total - expected()) > 0.01);
        window.updatePranotaObJournalPreview?.();
    }
    window.updatePranotaObAkunItems = update;
    document.getElementById('add-akun-item').addEventListener('click', () => {
        autoNominal = false;
        const row = rowTemplate.cloneNode(true);
        row.querySelector('.akun-item-select').value = '';
        row.querySelector('.akun-item-nominal').value = '';
        row.querySelector('.akun-item-memo').value = '';
        list.appendChild(row);
        initializeSearchableAccounts(row);
        update();
    });
    list.addEventListener('click', event => {
        if (event.target.closest('.remove-akun-item') && list.children.length > 1) {
            const row = event.target.closest('.akun-item-row');
            if (typeof jQuery !== 'undefined' && jQuery(row.querySelector('.akun-item-select')).hasClass('select2-hidden-accessible')) {
                jQuery(row.querySelector('.akun-item-select')).select2('destroy');
            }
            row.remove();
            update();
        }
    });
    list.addEventListener('input', event => {
        if (event.target.classList.contains('akun-item-nominal')) autoNominal = false;
        update();
    });
    list.addEventListener('change', update);
    document.getElementById('akun_dp_id')?.addEventListener('change', update);
    if (typeof jQuery !== 'undefined') {
        jQuery(list).on('change', '.akun-item-select', update);
        jQuery('#akun_dp_id').on('change', update);
    }
    document.getElementById('pembayaranForm').addEventListener('submit', event => {
        update();
        const total = [...list.querySelectorAll('.akun-item-nominal')].reduce((sum, input) => sum + (Number(input.value) || 0), 0);
        const ids = [...list.querySelectorAll('.akun-item-select')].map(select => select.value);
        if (Math.abs(total - expected()) > 0.01 || ids.some(id => !id) || new Set(ids).size !== ids.length) {
            event.preventDefault();
            alert('Periksa akun biaya dan nominalnya. Jumlah harus sama dengan total biaya sebelum DP, termasuk penyesuaian.');
        }
    });
    initializeSearchableAccounts();
    update();
});
</script>
@endpush
