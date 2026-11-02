@php
    $akunRows = old('akun_items', isset($pembayaran) && $pembayaran->akunItems->isNotEmpty()
        ? $pembayaran->akunItems->map(fn ($item) => ['akun_coa_id' => $item->akun_coa_id, 'nominal' => $item->nominal, 'memo' => $item->memo])->all()
        : [['akun_coa_id' => $pembayaran->akun_coa_id ?? '', 'nominal' => isset($pembayaran) ? $pembayaran->total_setelah_penyesuaian : '', 'memo' => '']]);
@endphp
<div class="border border-gray-200 rounded-lg p-4 mb-6" id="akun-items-section">
    <div class="flex justify-between items-start gap-3 mb-3">
        <div>
            <h4 class="text-sm font-semibold text-gray-800">Rincian Akun Biaya</h4>
            <p class="text-xs text-gray-500">Isi nominal dan memo/catatan untuk setiap akun. Jumlah nominal harus sama dengan grand total bayar.</p>
        </div>
        <button type="button" id="add-akun-item" class="text-xs font-semibold text-teal-700 hover:text-teal-900 whitespace-nowrap">+ Tambah Akun</button>
    </div>
    <div id="akun-items-list" class="space-y-3">
        @foreach($akunRows as $index => $row)
            <div class="akun-item-row grid grid-cols-1 md:grid-cols-12 gap-2 items-start rounded-lg border border-gray-200 bg-gray-50 p-3">
                <div class="md:col-span-5">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Akun Biaya</label>
                    <select name="akun_items[{{ $index }}][akun_coa_id]" class="akun-item-select block w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-white" required>
                        <option value="">-- Pilih Akun --</option>
                        @foreach($akunBiaya as $akun)
                            <option value="{{ $akun->id }}" {{ (string) ($row['akun_coa_id'] ?? '') === (string) $akun->id ? 'selected' : '' }}>{{ $akun->nomor_akun }} - {{ $akun->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal</label>
                    <input type="number" name="akun_items[{{ $index }}][nominal]" class="akun-item-nominal block w-full px-3 py-2 border border-gray-300 rounded-md text-sm" min="0.01" step="0.01" value="{{ $row['nominal'] ?? '' }}" required>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Memo / Catatan Akun</label>
                    <textarea name="akun_items[{{ $index }}][memo]" class="akun-item-memo block w-full px-3 py-2 border border-gray-300 rounded-md text-sm" rows="2" maxlength="2000" placeholder="Catatan khusus akun ini...">{{ $row['memo'] ?? '' }}</textarea>
                </div>
                <div class="md:col-span-1 md:pt-6 text-right">
                    <button type="button" class="remove-akun-item text-xs text-red-600 hover:text-red-800">Hapus</button>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-3 text-xs text-gray-700">Total rincian akun: <strong id="akun-items-total">Rp 0</strong></div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('akun-items-list');
    const grandTotal = document.getElementById('total_setelah_penyesuaian_display');
    const expected = () => Number((grandTotal.value || '0').replace(/[^0-9,.-]/g, '').replace(/\./g, '').replace(',', '.')) || 0;
    let autoNominal = list.children.length === 1 && !list.querySelector('.akun-item-nominal').value;

    function updateAkunItems() {
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
        const display = document.getElementById('akun-items-total');
        display.textContent = 'Rp ' + total.toLocaleString('id-ID');
        display.classList.toggle('text-red-600', Math.abs(total - expected()) > 0.01);
    }

    window.updateAntarGudangAkunItems = updateAkunItems;
    document.getElementById('add-akun-item').addEventListener('click', function () {
        autoNominal = false;
        const row = list.querySelector('.akun-item-row').cloneNode(true);
        row.querySelector('select').value = '';
        row.querySelector('input').value = '';
        row.querySelector('textarea').value = '';
        list.appendChild(row);
        updateAkunItems();
    });
    list.addEventListener('click', function (event) {
        if (event.target.closest('.remove-akun-item') && list.children.length > 1) {
            event.target.closest('.akun-item-row').remove();
            updateAkunItems();
        }
    });
    list.addEventListener('input', function (event) {
        if (event.target.classList.contains('akun-item-nominal')) autoNominal = false;
        updateAkunItems();
    });
    list.addEventListener('change', updateAkunItems);
    list.closest('form').addEventListener('submit', function (event) {
        const rows = [...list.querySelectorAll('.akun-item-row')];
        const total = rows.reduce((sum, row) => sum + (Number(row.querySelector('.akun-item-nominal').value) || 0), 0);
        const ids = rows.map(row => row.querySelector('.akun-item-select').value);
        if (Math.abs(total - expected()) > 0.01 || ids.some(id => !id) || new Set(ids).size !== ids.length) {
            event.preventDefault();
            alert('Periksa akun biaya dan nominalnya. Jumlah rincian akun harus sama dengan grand total bayar.');
        }
    });
    updateAkunItems();
});
</script>
@endpush
