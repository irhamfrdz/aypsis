@extends('layouts.app')

@section('title', 'Edit Tagihan LOLO Batam')
@section('page_title', 'Edit Tagihan LOLO Batam')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Tagihan LOLO Batam</h1>
            <p class="text-sm text-gray-500">Perbarui informasi dan rincian penagihan LOLO {{ $tagihanLoloBatam->nomor_tagihan }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tagihan-lolo-batam.show', $tagihanLoloBatam->id) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition-colors flex items-center">
                <i class="fas fa-eye mr-2"></i> Lihat Detail
            </a>
            <a href="{{ route('tagihan-lolo-batam.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-sm shadow-sm">
        <div class="flex">
            <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 mr-3"></i>
            <div>
                <p class="font-bold text-red-800">Terjadi kesalahan input:</p>
                <ul class="list-disc pl-5 mt-1 text-red-700 text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('tagihan-lolo-batam.update', $tagihanLoloBatam->id) }}" method="POST" id="tagihanForm">
        @csrf
        @method('PUT')

        {{-- Invoice Header Card --}}
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm mb-6">
            <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-100 pb-2 mb-4 flex items-center">
                <i class="fas fa-info-circle text-indigo-500 mr-2"></i> Informasi Utama Tagihan
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nomor Tagihan <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_tagihan" value="{{ old('nomor_tagihan', $tagihanLoloBatam->nomor_tagihan) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-semibold text-indigo-900 bg-gray-50/50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Tagihan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_tagihan" value="{{ old('tanggal_tagihan', $tagihanLoloBatam->tanggal_tagihan ? $tagihanLoloBatam->tanggal_tagihan->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Vendor / Depo Penerbit</label>
                    <input type="text" name="vendor" value="{{ old('vendor', $tagihanLoloBatam->vendor) }}" list="vendor_list" placeholder="Contoh: Meratus, Temas, Pelindo Batam..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <datalist id="vendor_list">
                        @foreach($vendors as $v)
                            <option value="{{ $v }}">
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Kapal (Opsional)</label>
                    <input type="text" name="kapal" value="{{ old('kapal', $tagihanLoloBatam->kapal) }}" list="kapal_list" placeholder="Nama Kapal..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <datalist id="kapal_list">
                        @foreach($kapals as $k)
                            <option value="{{ $k }}">
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. Voyage (Opsional)</label>
                    <input type="text" name="voyage" value="{{ old('voyage', $tagihanLoloBatam->voyage) }}" placeholder="Voyage..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status Pembayaran <span class="text-red-500">*</span></label>
                    <select name="status_pembayaran" id="status_pembayaran" onchange="toggleTanggalBayar()" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-white">
                        <option value="Belum Lunas" {{ old('status_pembayaran', $tagihanLoloBatam->status_pembayaran) == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="Lunas" {{ old('status_pembayaran', $tagihanLoloBatam->status_pembayaran) == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                <div id="tanggal_bayar_container" class="{{ old('status_pembayaran', $tagihanLoloBatam->status_pembayaran) == 'Lunas' ? '' : 'hidden' }}">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Bayar <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_bayar" id="tanggal_bayar" value="{{ old('tanggal_bayar', $tagihanLoloBatam->tanggal_bayar ? $tagihanLoloBatam->tanggal_bayar->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Catatan / Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan', $tagihanLoloBatam->keterangan) }}" placeholder="Catatan tagihan..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                {{-- Operator Selector --}}
                <div class="sm:col-span-2 md:col-span-4 bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 mt-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">
                        <i class="fas fa-user-cog text-indigo-500 mr-1"></i> Operator LOLO <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
                        <div class="flex items-center gap-4 bg-white p-2.5 rounded-lg border border-gray-200">
                            <label class="inline-flex items-center text-xs font-bold text-gray-700 cursor-pointer">
                                <input type="radio" name="tipe_operator" value="AYP" {{ old('tipe_operator', $tagihanLoloBatam->tipe_operator ?? 'AYP') === 'AYP' ? 'checked' : '' }} onchange="toggleTipeOperator(this.value)" class="text-indigo-600 focus:ring-indigo-500 mr-2">
                                Operator AYP
                            </label>
                            <label class="inline-flex items-center text-xs font-bold text-gray-700 cursor-pointer">
                                <input type="radio" name="tipe_operator" value="VENDOR" {{ old('tipe_operator', $tagihanLoloBatam->tipe_operator ?? 'AYP') === 'VENDOR' ? 'checked' : '' }} onchange="toggleTipeOperator(this.value)" class="text-indigo-600 focus:ring-indigo-500 mr-2">
                                Vendor
                            </label>
                        </div>

                        <div id="operator_ayp_box" class="md:col-span-2 {{ old('tipe_operator', $tagihanLoloBatam->tipe_operator ?? 'AYP') === 'AYP' ? '' : 'hidden' }}">
                            <select name="operator_karyawan_id" id="operator_karyawan_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-white">
                                <option value="">-- Pilih Nama Operator dari Data Karyawan --</option>
                                @foreach($karyawanOperators as $ko)
                                    <option value="{{ $ko->id }}" {{ old('operator_karyawan_id', $tagihanLoloBatam->operator_karyawan_id) == $ko->id ? 'selected' : '' }}>
                                        {{ $ko->nama_lengkap }}{{ $ko->pekerjaan ? ' ('.$ko->pekerjaan.')' : ($ko->divisi ? ' ('.$ko->divisi.')' : '') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="operator_vendor_box" class="md:col-span-2 {{ old('tipe_operator', $tagihanLoloBatam->tipe_operator ?? 'AYP') === 'VENDOR' ? '' : 'hidden' }}">
                            <input type="text" name="operator" id="operator_vendor_input" value="{{ old('operator', $tagihanLoloBatam->operator) }}" placeholder="Ketik nama vendor..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items Detail Card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="bg-gray-50/75 px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider flex items-center">
                        <i class="fas fa-boxes text-indigo-500 mr-2"></i> Rincian Kontainer LOLO
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Kelola kontainer dalam tagihan ini</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openImportModal()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors flex items-center">
                        <i class="fas fa-cloud-download-alt mr-1.5"></i> Tarik Data LOLO Batam
                    </button>
                    <button type="button" onclick="addRow()" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors flex items-center">
                        <i class="fas fa-plus mr-1.5"></i> Tambah Baris Manual
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="itemsTable">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase w-10">No</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase min-w-[150px]">No. Kontainer <span class="text-red-500">*</span></th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase w-20">Size</th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase w-28">Tipe</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase min-w-[200px]">Pilih Tarif LOLO Batam</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase min-w-[140px]">No. Surat Jalan</th>
                            <th class="px-3 py-3 text-right text-xs font-bold text-gray-500 uppercase w-32">Tarif (Rp) <span class="text-red-500">*</span></th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase w-16">Qty</th>
                            <th class="px-3 py-3 text-right text-xs font-bold text-gray-500 uppercase w-36">Subtotal (Rp)</th>
                            <th class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase w-12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white" id="itemsTableBody">
                        {{-- Rows will be populated via JS --}}
                    </tbody>
                </table>
            </div>

            {{-- Total Footer --}}
            <div class="bg-gray-50/75 px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-xs text-gray-500">
                    Total Item: <span id="totalItemsCount" class="font-bold text-gray-800">0</span> kontainer
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-gray-600 uppercase">Total Tagihan:</span>
                    <span id="grandTotalDisplay" class="text-xl font-bold text-emerald-700">Rp 0</span>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('tagihan-lolo-batam.show', $tagihanLoloBatam->id) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors flex items-center">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- MODAL TARIK DATA LOLO BATAM --}}
<div id="importModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeImportModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-indigo-600 px-6 py-4 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-base font-bold" id="modal-title">Tarik Data Kontainer Menggunakan LOLO</h3>
                    <p class="text-xs text-indigo-100 mt-0.5">Pilih kontainer dari Surat Jalan Bongkaran Batam atau Langsir Batam yang belum ditagihkan</p>
                </div>
                <button type="button" onclick="closeImportModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Sumber Dokumen</label>
                        <select id="modalSumberFilter" onchange="fetchPendingLolo()" class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="all">Semua (Bongkaran & Langsir)</option>
                            <option value="bongkaran">Surat Jalan Bongkaran Batam</option>
                            <option value="langsir">Langsir Kontainer Batam</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Cari No. Kontainer / No. SJ</label>
                        <div class="flex gap-2">
                            <input type="text" id="modalSearchInput" placeholder="Ketik nomor kontainer / surat jalan..." class="w-full rounded-lg border-gray-300 text-sm">
                            <button type="button" onclick="fetchPendingLolo()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>

                <div class="border rounded-xl overflow-hidden max-h-80 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-2.5 text-center w-12">
                                    <input type="checkbox" id="selectAllModal" onchange="toggleSelectAllModal(this)" class="rounded text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase">Sumber</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase">No. Kontainer</th>
                                <th class="px-4 py-2.5 text-center text-xs font-bold text-gray-500 uppercase">Size</th>
                                <th class="px-4 py-2.5 text-center text-xs font-bold text-gray-500 uppercase">Tipe</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase">No. SJ / Transaksi</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-500 uppercase">Keterangan / Kapal</th>
                            </tr>
                        </thead>
                        <tbody id="modalTableBody" class="divide-y divide-gray-200 bg-white">
                            <tr>
                                <td colspan="7" class="text-center py-8 text-gray-400">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data kontainer LOLO Batam...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t flex justify-between items-center">
                <span class="text-xs text-gray-500" id="selectedCountText">0 kontainer dipilih</span>
                <div class="flex gap-2">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="button" onclick="insertSelectedContainers()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 shadow-sm">
                        <i class="fas fa-check mr-1.5"></i> Masukkan ke Tagihan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const pricelists = @json($pricelists);
    const initialItems = @json($tagihanLoloBatam->items);
    let rowCount = 0;
    let pendingLoloData = [];

    function toggleTipeOperator(tipe) {
        const aypBox = document.getElementById('operator_ayp_box');
        const vendorBox = document.getElementById('operator_vendor_box');
        if (tipe === 'AYP') {
            aypBox.classList.remove('hidden');
            vendorBox.classList.add('hidden');
        } else {
            aypBox.classList.add('hidden');
            vendorBox.classList.remove('hidden');
        }
    }

    function toggleTanggalBayar() {
        const status = document.getElementById('status_pembayaran').value;
        const container = document.getElementById('tanggal_bayar_container');
        const input = document.getElementById('tanggal_bayar');

        if (status === 'Lunas') {
            container.classList.remove('hidden');
            input.setAttribute('required', 'required');
            if (!input.value) {
                input.value = new Date().toISOString().split('T')[0];
            }
        } else {
            container.classList.add('hidden');
            input.removeAttribute('required');
            input.value = '';
        }
    }

    function addRow(data = null) {
        rowCount++;
        const tbody = document.getElementById('itemsTableBody');

        const nomorKontainer = data ? data.nomor_kontainer : '';
        const size = data ? (data.size || '20') : '20';
        const tipe = data ? (data.tipe_kontainer || 'FULL') : 'FULL';
        const sumber = data ? (data.sumber_data || 'manual') : 'manual';
        const bongkaranId = data && data.sumber_data === 'bongkaran' ? (data.id || data.surat_jalan_bongkaran_id) : (data && data.surat_jalan_bongkaran_id ? data.surat_jalan_bongkaran_id : '');
        const langsirId = data && data.sumber_data === 'langsir' ? (data.id || data.langsir_batam_id) : (data && data.langsir_batam_id ? data.langsir_batam_id : '');
        const nomorSj = data ? (data.nomor_surat_jalan || '') : '';
        const kegiatan = data ? (data.kegiatan || 'LOLO Batam') : 'LOLO Batam';
        let tarif = data && data.tarif !== undefined ? data.tarif : 0;
        let pricelistId = data && data.master_pricelist_lolo_batam_id ? data.master_pricelist_lolo_batam_id : '';
        const jumlah = data && data.jumlah ? data.jumlah : 1;

        if (!tarif && pricelists.length > 0) {
            const match = pricelists.find(p => p.size === size && (p.tipe === tipe || p.tipe === 'ALL'));
            if (match) {
                tarif = match.tarif;
                pricelistId = match.id;
            }
        }

        const tr = document.createElement('tr');
        tr.id = `row_${rowCount}`;
        tr.className = 'hover:bg-gray-50/50 transition-colors';

        tr.innerHTML = `
            <td class="px-3 py-3 text-center text-xs font-semibold text-gray-500 row-number">${rowCount}</td>
            <td class="px-3 py-3">
                <input type="text" name="items[${rowCount}][nomor_kontainer]" value="${nomorKontainer}" required placeholder="Nomor Kontainer..." class="w-full rounded-md border-gray-300 text-xs font-bold uppercase focus:ring-indigo-500 focus:border-indigo-500">
                <input type="hidden" name="items[${rowCount}][sumber_data]" value="${sumber}">
                <input type="hidden" name="items[${rowCount}][surat_jalan_bongkaran_id]" value="${bongkaranId}">
                <input type="hidden" name="items[${rowCount}][langsir_batam_id]" value="${langsirId}">
                <input type="hidden" name="items[${rowCount}][kegiatan]" value="${kegiatan}">
            </td>
            <td class="px-3 py-3 text-center">
                <select name="items[${rowCount}][size]" onchange="onSizeOrTypeChange(${rowCount})" class="w-full rounded-md border-gray-300 text-xs text-center focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="20" ${size === '20' ? 'selected' : ''}>20'</option>
                    <option value="40" ${size === '40' ? 'selected' : ''}>40'</option>
                </select>
            </td>
            <td class="px-3 py-3 text-center">
                <select name="items[${rowCount}][tipe_kontainer]" onchange="onSizeOrTypeChange(${rowCount})" class="w-full rounded-md border-gray-300 text-xs text-center focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="FULL" ${tipe === 'FULL' ? 'selected' : ''}>FULL</option>
                    <option value="EMPTY" ${tipe === 'EMPTY' ? 'selected' : ''}>EMPTY</option>
                </select>
            </td>
            <td class="px-3 py-3">
                <select name="items[${rowCount}][master_pricelist_lolo_batam_id]" onchange="onPricelistChange(${rowCount}, this)" class="w-full rounded-md border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Tarif Master --</option>
                    ${pricelists.map(p => `
                        <option value="${p.id}" data-tarif="${p.tarif}" data-size="${p.size}" ${pricelistId == p.id ? 'selected' : ''}>
                            ${p.size ? p.size + 'ft' : ''} - Rp ${Number(p.tarif).toLocaleString('id-ID')}${p.keterangan ? ' (' + p.keterangan + ')' : ''}
                        </option>
                    `).join('')}
                </select>
            </td>
            <td class="px-3 py-3">
                <input type="text" name="items[${rowCount}][nomor_surat_jalan]" value="${nomorSj}" placeholder="No SJ..." class="w-full rounded-md border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
            </td>
            <td class="px-3 py-3">
                <input type="number" step="0.01" min="0" name="items[${rowCount}][tarif]" value="${tarif}" oninput="calcSubtotal(${rowCount})" required class="w-full rounded-md border-gray-300 text-xs text-right font-semibold text-emerald-700 focus:ring-indigo-500 focus:border-indigo-500">
            </td>
            <td class="px-3 py-3 text-center">
                <input type="number" min="1" name="items[${rowCount}][jumlah]" value="${jumlah}" oninput="calcSubtotal(${rowCount})" required class="w-full rounded-md border-gray-300 text-xs text-center focus:ring-indigo-500 focus:border-indigo-500">
            </td>
            <td class="px-3 py-3 text-right">
                <span id="subtotal_display_${rowCount}" class="text-xs font-bold text-gray-800">Rp ${Number(tarif * jumlah).toLocaleString('id-ID')}</span>
            </td>
            <td class="px-3 py-3 text-center">
                <button type="button" onclick="removeRow(${rowCount})" class="text-red-500 hover:text-red-700 p-1" title="Hapus Baris">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        reindexRows();
        calcSubtotal(rowCount);
    }

    function removeRow(id) {
        const row = document.getElementById(`row_${id}`);
        if (row) {
            row.remove();
            reindexRows();
            calcGrandTotal();
        }
    }

    function reindexRows() {
        const rows = document.querySelectorAll('#itemsTableBody tr');
        rows.forEach((row, index) => {
            const numCell = row.querySelector('.row-number');
            if (numCell) numCell.innerText = index + 1;
        });
        document.getElementById('totalItemsCount').innerText = rows.length;
    }

    function onPricelistChange(rowId, selectElem) {
        const selected = selectElem.options[selectElem.selectedIndex];
        if (selected && selected.dataset.tarif) {
            const row = document.getElementById(`row_${rowId}`);
            const tarifInput = row.querySelector(`input[name="items[${rowId}][tarif]"]`);
            tarifInput.value = selected.dataset.tarif;
            calcSubtotal(rowId);
        }
    }

    function onSizeOrTypeChange(rowId) {
        const row = document.getElementById(`row_${rowId}`);
        const size = row.querySelector(`select[name="items[${rowId}][size]"]`).value;
        const tipe = row.querySelector(`select[name="items[${rowId}][tipe_kontainer]"]`).value;
        const pricelistSelect = row.querySelector(`select[name="items[${rowId}][master_pricelist_lolo_batam_id]"]`);

        const match = pricelists.find(p => p.size === size && (p.tipe === tipe || p.tipe === 'ALL'));
        if (match) {
            pricelistSelect.value = match.id;
            const tarifInput = row.querySelector(`input[name="items[${rowId}][tarif]"]`);
            tarifInput.value = match.tarif;
            calcSubtotal(rowId);
        }
    }

    function calcSubtotal(rowId) {
        const row = document.getElementById(`row_${rowId}`);
        if (!row) return;

        const tarif = parseFloat(row.querySelector(`input[name="items[${rowId}][tarif]"]`).value) || 0;
        const jumlah = parseInt(row.querySelector(`input[name="items[${rowId}][jumlah]"]`).value) || 1;
        const subtotal = tarif * jumlah;

        const display = document.getElementById(`subtotal_display_${rowId}`);
        if (display) {
            display.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        }

        calcGrandTotal();
    }

    function calcGrandTotal() {
        let grandTotal = 0;
        const rows = document.querySelectorAll('#itemsTableBody tr');

        rows.forEach(row => {
            const tarifInput = row.querySelector('input[name*="[tarif]"]');
            const jumlahInput = row.querySelector('input[name*="[jumlah]"]');
            if (tarifInput && jumlahInput) {
                const tarif = parseFloat(tarifInput.value) || 0;
                const jumlah = parseInt(jumlahInput.value) || 1;
                grandTotal += (tarif * jumlah);
            }
        });

        document.getElementById('grandTotalDisplay').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        document.getElementById('totalItemsCount').innerText = rows.length;
    }

    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
        fetchPendingLolo();
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function fetchPendingLolo() {
        const type = document.getElementById('modalSumberFilter').value;
        const search = document.getElementById('modalSearchInput').value;
        const tbody = document.getElementById('modalTableBody');

        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-8 text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>`;

        fetch(`{{ route('tagihan-lolo-batam.api.pending-lolo') }}?type=${encodeURIComponent(type)}&search=${encodeURIComponent(search)}`)
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data.length > 0) {
                    pendingLoloData = res.data;
                    tbody.innerHTML = res.data.map((item, idx) => `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2.5 text-center">
                                <input type="checkbox" class="modal-checkbox rounded text-indigo-600 focus:ring-indigo-500" value="${idx}" onchange="updateSelectedCount()">
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold ${item.sumber_data === 'bongkaran' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'}">
                                    ${item.sumber_data === 'bongkaran' ? 'Bongkaran' : 'Langsir'}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 font-bold text-gray-900">${item.nomor_kontainer}</td>
                            <td class="px-4 py-2.5 text-center">${item.size}'</td>
                            <td class="px-4 py-2.5 text-center">${item.tipe_kontainer}</td>
                            <td class="px-4 py-2.5 text-gray-600">${item.nomor_surat_jalan || '-'}</td>
                            <td class="px-4 py-2.5 text-xs text-gray-500">${item.kegiatan}</td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-8 text-gray-400"><i class="fas fa-check-circle text-emerald-500 mr-2"></i> Tidak ada kontainer LOLO Batam yang belum ditagihkan.</td></tr>`;
                }
                updateSelectedCount();
            })
            .catch(err => {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i> Gagal memuat data: ${err.message}</td></tr>`;
            });
    }

    function toggleSelectAllModal(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.modal-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.modal-checkbox:checked').length;
        document.getElementById('selectedCountText').innerText = `${selected} kontainer dipilih`;
    }

    function insertSelectedContainers() {
        const selectedCheckboxes = document.querySelectorAll('.modal-checkbox:checked');
        if (selectedCheckboxes.length === 0) {
            alert('Silakan pilih minimal 1 kontainer terlebih dahulu.');
            return;
        }

        selectedCheckboxes.forEach(cb => {
            const idx = parseInt(cb.value);
            const item = pendingLoloData[idx];
            if (item) {
                addRow(item);
            }
        });

        closeImportModal();
    }

    // Populate existing items on edit page load
    document.addEventListener('DOMContentLoaded', () => {
        if (initialItems && initialItems.length > 0) {
            initialItems.forEach(item => {
                addRow(item);
            });
        } else {
            addRow();
        }
    });
</script>
@endpush
@endsection
