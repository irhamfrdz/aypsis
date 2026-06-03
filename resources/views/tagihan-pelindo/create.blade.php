@extends('layouts.app')

@section('title', 'Tambah Tagihan Pelindo')
@section('page_title', 'Tambah Tagihan Pelindo')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Form Input Tagihan Pelindo</h1>
        <a href="{{ route('tagihan-pelindo.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition duration-150">
            Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-semibold text-red-800">Oops! Terjadi kesalahan:</p>
                <ul class="list-disc pl-5 mt-1 text-xs text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('tagihan-pelindo.store') }}" method="POST" id="invoiceForm">
        @csrf

        {{-- Invoice Header Card --}}
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm mb-6">
            <h2 class="text-sm font-bold text-gray-700 uppercase border-b pb-2 mb-4">Informasi Utama</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nomor Tagihan <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_tagihan" value="{{ old('nomor_tagihan', $nomorTagihan) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Tagihan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_tagihan" value="{{ old('tanggal_tagihan', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Kapal (dari BL)</label>
                    <select name="kapal" id="kapal_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white" onchange="onKapalChange()">
                        <option value="">-- Pilih Kapal --</option>
                        @foreach($bls->pluck('nama_kapal')->unique() as $kapal)
                            <option value="{{ $kapal }}" {{ old('kapal') == $kapal ? 'selected' : '' }}>{{ $kapal }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Voyage (dari BL)</label>
                    <select name="voyage" id="voyage_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                        <option value="">-- Pilih Voyage --</option>
                        @foreach($bls->unique('no_voyage') as $bl)
                            <option value="{{ $bl->no_voyage }}" data-kapal="{{ $bl->nama_kapal }}" {{ old('voyage') == $bl->no_voyage ? 'selected' : '' }}>{{ $bl->no_voyage }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status Pembayaran <span class="text-red-500">*</span></label>
                    <select name="status_pembayaran" id="status_pembayaran" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white" required>
                        <option value="Belum Lunas" {{ old('status_pembayaran') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="Lunas" {{ old('status_pembayaran') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
                <div id="tanggal_bayar_container" class="{{ old('status_pembayaran') == 'Lunas' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Bayar <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_bayar" id="tanggal_bayar" value="{{ old('tanggal_bayar') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Catatan tambahan..."></textarea>
            </div>
        </div>

        {{-- Invoice Items Table Card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-3 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-sm font-bold text-gray-700 uppercase">Item Detail Tagihan</h2>
                <button type="button" onclick="addRow()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-semibold shadow-sm transition-all duration-150">
                    <i class="fas fa-plus mr-1"></i> Tambah Baris
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="itemsTable">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase w-12">No</th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase min-w-[150px]">Nomor Kontainer</th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase min-w-[200px]">Kegiatan Pelindo</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase w-24">Ukuran</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase w-28">Full/Empty</th>
                            <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase w-32">Tarif</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase w-20">Qty</th>
                            <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase w-36">Total</th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase min-w-[150px]">Keterangan</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase w-12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="itemsTableBody">
                        {{-- Rows will be injected here dynamically --}}
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-gray-50 border-t border-gray-100 flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
                <div class="text-xs text-gray-500">
                    * Pastikan kegiatan Pelindo dipilih dari pricelist agar tarif dan ukuran terisi otomatis.
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-bold text-gray-700 text-sm">TOTAL PENAGIHAN:</span>
                    <span class="text-xl font-black text-indigo-700" id="grandTotalDisplay">Rp 0,00</span>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('tagihan-pelindo.index') }}" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-semibold transition duration-150">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition duration-150">
                Simpan Tagihan
            </button>
        </div>
    </form>
</div>

{{-- Pass pricelist data to JavaScript --}}
<script>
    const pricelistItems = @json($pricelists);
</script>

@push('scripts')
<script>
    let rowIndex = 0;

    // Toggle Tanggal Bayar container
    document.getElementById('status_pembayaran').addEventListener('change', function() {
        const container = document.getElementById('tanggal_bayar_container');
        const input = document.getElementById('tanggal_bayar');
        if (this.value === 'Lunas') {
            container.classList.remove('hidden');
            input.setAttribute('required', 'required');
            if(!input.value) {
                input.value = new Date().toISOString().split('T')[0];
            }
        } else {
            container.classList.add('hidden');
            input.removeAttribute('required');
            input.value = '';
        }
    });

    // Add first row on page load
    window.addEventListener('DOMContentLoaded', () => {
        addRow();
    });

    function addRow() {
        const tbody = document.getElementById('itemsTableBody');
        const tr = document.createElement('tr');
        tr.id = `row_${rowIndex}`;
        tr.className = 'hover:bg-gray-50/50 transition-colors';

        // Select options
        let selectOptions = '<option value="">-- Pilih Kegiatan --</option>';
        pricelistItems.forEach(item => {
            const statusSuffix = item.status_kontainer ? ` [${item.status_kontainer}]` : '';
            const label = `${item.kegiatan} (${item.ukuran || '-'}ft)${statusSuffix} - Rp ${Number(item.tarif).toLocaleString('id-ID')}`;
            selectOptions += `<option value="${item.id}" data-tarif="${item.tarif}" data-ukuran="${item.ukuran || ''}" data-kegiatan="${item.kegiatan}" data-status_kontainer="${item.status_kontainer || ''}">${label}</option>`;
        });

        tr.innerHTML = `
            <td class="px-4 py-2 text-center text-sm font-medium text-gray-400 row-number"></td>
            <td class="px-4 py-2">
                <input type="text" name="items[${rowIndex}][nomor_kontainer]" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Kontainer No.">
            </td>
            <td class="px-4 py-2">
                <select name="items[${rowIndex}][pricelist_pelindo_id]" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white" onchange="onKegiatanChange(this, ${rowIndex})">
                    ${selectOptions}
                </select>
                <input type="hidden" name="items[${rowIndex}][kegiatan]" id="kegiatan_${rowIndex}">
            </td>
            <td class="px-4 py-2 text-center">
                <input type="text" name="items[${rowIndex}][ukuran]" id="ukuran_${rowIndex}" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs text-center bg-gray-50" readonly placeholder="-">
            </td>
            <td class="px-4 py-2">
                <select name="items[${rowIndex}][status_kontainer]" id="status_kontainer_${rowIndex}" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">-</option>
                    <option value="Full">Full</option>
                    <option value="Empty">Empty</option>
                </select>
            </td>
            <td class="px-4 py-2">
                <input type="number" step="0.01" name="items[${rowIndex}][tarif]" id="tarif_${rowIndex}" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs text-right focus:ring-1 focus:ring-blue-500 focus:border-blue-500" value="0" oninput="calculateRowTotal(${rowIndex})">
            </td>
            <td class="px-4 py-2">
                <input type="number" name="items[${rowIndex}][jumlah]" id="jumlah_${rowIndex}" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs text-center focus:ring-1 focus:ring-blue-500 focus:border-blue-500" value="1" min="1" oninput="calculateRowTotal(${rowIndex})">
            </td>
            <td class="px-4 py-2">
                <input type="text" id="total_display_${rowIndex}" class="w-full px-2 py-1.5 border border-gray-200 rounded text-xs text-right bg-gray-50 font-semibold text-gray-700" value="Rp 0" readonly>
                <input type="hidden" id="total_val_${rowIndex}" class="row-total" value="0">
            </td>
            <td class="px-4 py-2">
                <input type="text" name="items[${rowIndex}][keterangan]" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Keterangan item...">
            </td>
            <td class="px-4 py-2 text-center">
                <button type="button" onclick="removeRow(${rowIndex})" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded transition duration-150">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        rowIndex++;
        updateRowNumbers();
    }

    function removeRow(idx) {
        const row = document.getElementById(`row_${idx}`);
        if (row) {
            row.remove();
            updateRowNumbers();
            calculateGrandTotal();
        }
    }

    function updateRowNumbers() {
        const rows = document.querySelectorAll('#itemsTableBody tr');
        rows.forEach((row, i) => {
            row.querySelector('.row-number').innerText = i + 1;
        });
    }

    function onKegiatanChange(selectElement, idx) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const inputKegiatan = document.getElementById(`kegiatan_${idx}`);
        const inputUkuran = document.getElementById(`ukuran_${idx}`);
        const selectStatus = document.getElementById(`status_kontainer_${idx}`);
        const inputTarif = document.getElementById(`tarif_${idx}`);

        if (selectedOption && selectedOption.value !== "") {
            const tarif = selectedOption.getAttribute('data-tarif');
            const ukuran = selectedOption.getAttribute('data-ukuran');
            const kegiatan = selectedOption.getAttribute('data-kegiatan');
            const status_kontainer = selectedOption.getAttribute('data-status_kontainer');

            inputKegiatan.value = kegiatan;
            inputUkuran.value = ukuran;
            if (selectStatus) {
                selectStatus.value = status_kontainer || '';
            }
            inputTarif.value = tarif;
        } else {
            inputKegiatan.value = '';
            inputUkuran.value = '';
            if (selectStatus) {
                selectStatus.value = '';
            }
            inputTarif.value = 0;
        }

        calculateRowTotal(idx);
    }

    function calculateRowTotal(idx) {
        const tarifInput = document.getElementById(`tarif_${idx}`);
        const jumlahInput = document.getElementById(`jumlah_${idx}`);
        const totalDisplay = document.getElementById(`total_display_${idx}`);
        const totalVal = document.getElementById(`total_val_${idx}`);

        const tarif = parseFloat(tarifInput.value) || 0;
        const jumlah = parseInt(jumlahInput.value) || 0;
        const total = tarif * jumlah;

        totalVal.value = total;
        totalDisplay.value = 'Rp ' + total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        const totalElements = document.querySelectorAll('.row-total');
        let grandTotal = 0;
        totalElements.forEach(el => {
            grandTotal += parseFloat(el.value) || 0;
        });

        document.getElementById('grandTotalDisplay').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function onKapalChange() {
        const kapalSelect = document.getElementById('kapal_select');
        const voyageSelect = document.getElementById('voyage_select');
        const selectedKapal = kapalSelect.value;
        const currentVoyage = voyageSelect.value;

        // Filter voyage options
        Array.from(voyageSelect.options).forEach(option => {
            if (option.value === '') return;
            const optionKapal = option.getAttribute('data-kapal');
            if (!selectedKapal || optionKapal === selectedKapal) {
                option.style.display = '';
                option.disabled = false;
            } else {
                option.style.display = 'none';
                option.disabled = true;
            }
        });

        // If the previously selected voyage is no longer valid, reset it
        const selectedOption = voyageSelect.options[voyageSelect.selectedIndex];
        if (selectedOption && selectedOption.disabled) {
            voyageSelect.value = '';
        }
    }

    // Run on load to apply initial filter if Kapal was old-selected
    window.addEventListener('DOMContentLoaded', () => {
        onKapalChange();
    });
</script>
@endpush
@endsection
