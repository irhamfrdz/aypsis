@extends('layouts.app')

@section('title', 'Tambah Asuransi Tanda Terima')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-6">
                <a href="{{ route('asuransi-tanda-terima.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Tambah Asuransi Tanda Terima</h1>
            </div>

            <form action="{{ route('asuransi-tanda-terima.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vendor -->
                    <div class="md:col-span-2">
                        <label for="vendor_asuransi_id" class="block text-sm font-medium text-gray-700 mb-2">Vendor Asuransi <span class="text-red-500">*</span></label>
                        <select id="vendor_asuransi_id" name="vendor_asuransi_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent select2">
                            <option value="">-- Pilih Vendor --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" data-tarif="{{ $vendor->tarif }}" {{ old('vendor_asuransi_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->nama_asuransi }} (Tarif: {{ $vendor->tarif }}%)
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_asuransi_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Source Type -->
                    <div>
                        <label for="receipt_type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Tanda Terima <span class="text-red-500">*</span></label>
                        <select id="receipt_type" name="receipt_type" required onchange="toggleReceiptList()"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                {{ isset($selectedReceipt) ? 'disabled' : '' }}>
                            <option value="tt" {{ (old('receipt_type', $selectedType) == 'tt') ? 'selected' : '' }}>Tanda Terima (Regular)</option>
                            <option value="tttsj" {{ (old('receipt_type', $selectedType) == 'tttsj') ? 'selected' : '' }}>Tanda Terima Tanpa SJ</option>
                            <option value="lcl" {{ (old('receipt_type', $selectedType) == 'lcl') ? 'selected' : '' }}>Tanda Terima LCL</option>
                        </select>
                        @if(isset($selectedReceipt))
                            <input type="hidden" name="receipt_type" value="{{ $selectedType }}">
                        @endif
                        @error('receipt_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Receipt List -->
                    <div>
                        <label for="receipt_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Data <span class="text-red-500">*</span></label>
                        
                        <div id="wrapper_tt" class="receipt-select-wrapper {{ old('receipt_type', $selectedType) == 'tt' ? '' : 'hidden' }}">
                            <select name="receipt_id_tt" id="receipt_id_tt" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent select2" {{ isset($selectedReceipt) && $selectedType != 'tt' ? 'disabled' : '' }}>
                                <option value="">-- Pilih Tanda Terima --</option>
                                @if(isset($selectedReceipt) && $selectedType == 'tt')
                                    <option value="{{ $selectedReceipt->id }}" selected>[{{ $selectedReceipt->no_surat_jalan }}] - {{ $selectedReceipt->penerima }}</option>
                                @endif
                                @foreach($tandaTerimas as $tt)
                                    @if(!(isset($selectedReceipt) && $selectedType == 'tt' && $selectedReceipt->id == $tt->id))
                                        <option value="{{ $tt->id }}" {{ old('receipt_id_tt') == $tt->id ? 'selected' : '' }}>[{{ $tt->no_surat_jalan }}] - {{ $tt->penerima }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div id="wrapper_tttsj" class="receipt-select-wrapper {{ old('receipt_type', $selectedType) == 'tttsj' ? '' : 'hidden' }}">
                            <select name="receipt_id_tttsj" id="receipt_id_tttsj" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent select2" {{ isset($selectedReceipt) && $selectedType != 'tttsj' ? 'disabled' : '' }}>
                                <option value="">-- Pilih Tanda Terima Tanpa SJ --</option>
                                @if(isset($selectedReceipt) && $selectedType == 'tttsj')
                                    <option value="{{ $selectedReceipt->id }}" selected>[{{ $selectedReceipt->no_tanda_terima }}] - {{ $selectedReceipt->penerima }}</option>
                                @endif
                                @foreach($tandaTerimaTanpaSjs as $tt)
                                    @if(!(isset($selectedReceipt) && $selectedType == 'tttsj' && $selectedReceipt->id == $tt->id))
                                        <option value="{{ $tt->id }}" {{ old('receipt_id_tttsj') == $tt->id ? 'selected' : '' }}>[{{ $tt->no_tanda_terima }}] - {{ $tt->penerima }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div id="wrapper_lcl" class="receipt-select-wrapper {{ old('receipt_type', $selectedType) == 'lcl' ? '' : 'hidden' }}">
                            <select name="receipt_id_lcl" id="receipt_id_lcl" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent select2" {{ isset($selectedReceipt) && $selectedType != 'lcl' ? 'disabled' : '' }}>
                                <option value="">-- Pilih Tanda Terima LCL --</option>
                                @if(isset($selectedReceipt) && $selectedType == 'lcl')
                                    <option value="{{ $selectedReceipt->id }}" selected>[{{ $selectedReceipt->nomor_tanda_terima }}] - {{ $selectedReceipt->nama_penerima }}</option>
                                @endif
                                @foreach($tandaTerimaLcls as $tt)
                                    @if(!(isset($selectedReceipt) && $selectedType == 'lcl' && $selectedReceipt->id == $tt->id))
                                        <option value="{{ $tt->id }}" {{ old('receipt_id_lcl') == $tt->id ? 'selected' : '' }}>[{{ $tt->nomor_tanda_terima }}] - {{ $tt->nama_penerima }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" name="receipt_id" id="final_receipt_id" value="{{ old('receipt_id', $selectedId) }}">
                        @error('receipt_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Additional Info (Read-only display) -->
                    <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg p-4 grid grid-cols-2 md:grid-cols-5 gap-4" id="receipt_info_section">
                        <div>
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">No. Kontainer</p>
                            <p class="text-sm font-bold text-gray-800" id="info_no_kontainer">{{ $selectedReceipt ? ($selectedReceipt->no_kontainer ?? $selectedReceipt->nomor_kontainer ?? '-') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">No. Surat Jalan</p>
                            <p class="text-sm font-bold text-gray-800" id="info_no_surat_jalan">
                                @if($selectedReceipt)
                                    @if($selectedType == 'tt') {{ $selectedReceipt->no_surat_jalan ?? '-' }}
                                    @elseif($selectedType == 'tttsj') {{ $selectedReceipt->nomor_surat_jalan_customer ?? '-' }}
                                    @elseif($selectedType == 'lcl') {{ $selectedReceipt->no_surat_jalan_customer ?? '-' }}
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">Nama Barang</p>
                            <p class="text-sm font-bold text-gray-800 truncate" id="info_nama_barang" title="{{ $selectedReceipt ? (is_array($selectedReceipt->nama_barang) ? implode(', ', $selectedReceipt->nama_barang) : ($selectedReceipt->nama_barang ?? '-')) : '-' }}">
                                @if($selectedReceipt)
                                    @if($selectedType == 'tt') {{ is_array($selectedReceipt->nama_barang) ? implode(', ', $selectedReceipt->nama_barang) : ($selectedReceipt->nama_barang ?? '-') }}
                                    @elseif($selectedType == 'tttsj') {{ $selectedReceipt->nama_barang ?? '-' }}
                                    @elseif($selectedType == 'lcl') {{ $selectedReceipt->items->pluck('nama_barang')->filter()->unique()->implode(', ') ?: '-' }}
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">Jumlah</p>
                            <p class="text-sm font-bold text-gray-800" id="info_jumlah_barang">
                                @if($selectedReceipt)
                                    @if($selectedType == 'tt') {{ $selectedReceipt->jumlah ?? '-' }}
                                    @elseif($selectedType == 'tttsj') {{ $selectedReceipt->jumlah_barang ?? '-' }}
                                    @elseif($selectedType == 'lcl') {{ $selectedReceipt->items->sum('jumlah') ?: '-' }}
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">Satuan</p>
                            <p class="text-sm font-bold text-gray-800" id="info_satuan">
                                @if($selectedReceipt)
                                    @if($selectedType == 'tt') {{ $selectedReceipt->satuan ?? '-' }}
                                    @elseif($selectedType == 'tttsj') {{ $selectedReceipt->satuan_barang ?? '-' }}
                                    @elseif($selectedType == 'lcl') {{ $selectedReceipt->items->pluck('satuan')->filter()->unique()->implode(', ') ?: '-' }}
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                        </div>

                        <!-- View Button -->
                        <div class="md:col-span-5 flex justify-end mt-2 pt-2 border-t border-blue-100 {{ $selectedReceipt ? '' : 'hidden' }}" id="view_receipt_wrapper">
                             <a id="btn_view_receipt" href="{{ $selectedReceipt ? (
                                $selectedType == 'tt' ? route('tanda-terima.show', $selectedReceipt->id) : (
                                    $selectedType == 'tttsj' ? route('tanda-terima-tanpa-surat-jalan.show', $selectedReceipt->id) : (
                                        $selectedType == 'lcl' ? route('tanda-terima-lcl.show', $selectedReceipt->id) : '#'
                                    )
                                )
                             ) : '#' }}" target="_blank" class="inline-flex items-center text-blue-700 hover:text-blue-900 border border-blue-300 bg-white px-3 py-1 rounded-md text-xs font-bold shadow-sm transition duration-150">
                                <i class="fas fa-eye mr-1.5"></i> Lihat Detail Tanda Terima
                             </a>
                        </div>
                    </div>

                    <!-- Polis Number -->
                    <div>
                        <label for="nomor_polis" class="block text-sm font-medium text-gray-700 mb-2">Nomor Polis <span class="text-red-500">*</span></label>
                        <input type="text" id="nomor_polis" name="nomor_polis" value="{{ old('nomor_polis') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: POL12345678">
                        @error('nomor_polis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Polis Date -->
                    <div>
                        <label for="tanggal_polis" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Polis <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_polis" name="tanggal_polis" value="{{ old('tanggal_polis', date('Y-m-d')) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('tanggal_polis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Nilai Barang -->
                    <div>
                        <label for="nilai_barang" class="block text-sm font-medium text-gray-700 mb-2">Nilai Barang <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="number" id="nilai_barang" name="nilai_barang" value="{{ old('nilai_barang', 0) }}" required
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        @error('nilai_barang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Grand Total -->
                    <div>
                        <label for="grand_total" class="block text-sm font-medium text-gray-700 mb-2">Grand Total (Premi)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="text" id="grand_total" name="grand_total" value="0" readonly
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 bg-gray-50 focus:outline-none font-semibold text-gray-800">
                        </div>
                        <p class="mt-1 text-xs text-gray-500" id="tarif_info_text">Tarif: 0%</p>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label for="asuransi_file" class="block text-sm font-medium text-gray-700 mb-2">Unduh Dokumen Asuransi</label>
                        <input type="file" id="asuransi_file" name="asuransi_file"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Format: PDF, JPG, PNG (Maks 5MB)</p>
                        @error('asuransi_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan') }}</textarea>
                        @error('keterangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('asuransi-tanda-terima.index') }}"
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition duration-200">
                        Batal
                    </a>
                    <button type="submit" onclick="syncReceiptId()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleReceiptList() {
        const type = document.getElementById('receipt_type').value;
        const wrappers = document.querySelectorAll('.receipt-select-wrapper');
        
        wrappers.forEach(w => w.classList.add('hidden'));
        document.getElementById('wrapper_' + type).classList.remove('hidden');
    }

    function syncReceiptId() {
        const type = document.getElementById('receipt_type').value;
        const select = document.getElementById('receipt_id_' + type);
        if (select) {
            document.getElementById('final_receipt_id').value = select.value;
        }
    }

    function calculateGrandTotal() {
        const select = document.getElementById('vendor_asuransi_id');
        const nilaiInput = document.getElementById('nilai_barang');
        const grandTotalInput = document.getElementById('grand_total');
        const tarifInfo = document.getElementById('tarif_info_text');

        if (!select || !nilaiInput || !grandTotalInput) return;

        const selectedOption = select.options[select.selectedIndex];
        const tarif = selectedOption ? parseFloat(selectedOption.getAttribute('data-tarif')) || 0 : 0;
        const nilai = parseFloat(nilaiInput.value) || 0;

        const total = nilai * (tarif / 100);
        
        // Format to IDR without fraction if integer, or with 2 decimals if float
        const formatted = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).format(total);
        
        grandTotalInput.value = formatted;
        
        if (tarifInfo) {
            tarifInfo.textContent = 'Tarif: ' + tarif + '%';
        }
    }

    function updateReceiptInfo() {
        const type = document.getElementById('receipt_type').value;
        const id = document.getElementById('receipt_id_' + type).value;
        
        const infoNoKontainer = document.getElementById('info_no_kontainer');
        const infoNoSuratJalan = document.getElementById('info_no_surat_jalan');
        const infoNamaBarang = document.getElementById('info_nama_barang');
        const infoJumlahBarang = document.getElementById('info_jumlah_barang');
        const infoSatuan = document.getElementById('info_satuan');
        const viewWrapper = document.getElementById('view_receipt_wrapper');
        const viewBtn = document.getElementById('btn_view_receipt');

        if (!id) {
            infoNoKontainer.textContent = '-';
            infoNoSuratJalan.textContent = '-';
            infoNamaBarang.textContent = '-';
            infoJumlahBarang.textContent = '-';
            infoSatuan.textContent = '-';
            viewWrapper.classList.add('hidden');
            return;
        }

        // Show loading
        infoNoKontainer.textContent = '...';
        infoNoSuratJalan.textContent = '...';
        infoNamaBarang.textContent = '...';
        infoJumlahBarang.textContent = '...';
        infoSatuan.textContent = '...';

        fetch(`/asuransi-tanda-terima/get-receipt-details/${type}/${id}`)
            .then(response => response.json())
            .then(data => {
                infoNoKontainer.textContent = data.no_kontainer || '-';
                infoNoSuratJalan.textContent = data.no_surat_jalan || '-';
                infoNamaBarang.textContent = data.nama_barang || '-';
                infoNamaBarang.title = data.nama_barang || '-';
                infoJumlahBarang.textContent = data.jumlah_barang || '-';
                infoSatuan.textContent = data.satuan || '-';
                
                // Update Button
                let baseUrl = '';
                if (type === 'tt') baseUrl = '/tanda-terima/';
                else if (type === 'tttsj') baseUrl = '/tanda-terima-tanpa-surat-jalan/';
                else if (type === 'lcl') baseUrl = '/tanda-terima-lcl/';
                
                if (baseUrl) {
                    viewBtn.href = baseUrl + id;
                    viewWrapper.classList.remove('hidden');
                } else {
                    viewWrapper.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error fetching receipt details:', error);
                infoNoKontainer.textContent = 'Error';
                infoNoSuratJalan.textContent = 'Error';
                infoNamaBarang.textContent = 'Error';
                infoJumlahBarang.textContent = 'Error';
                infoSatuan.textContent = 'Error';
                viewWrapper.classList.add('hidden');
            });
    }

    // Initialize list toggle
    document.addEventListener('DOMContentLoaded', function() {
        toggleReceiptList();
        syncReceiptId();
        
        // Add event listeners for grand total
        const vendorSelect = document.getElementById('vendor_asuransi_id');
        const nilaiInput = document.getElementById('nilai_barang');
        const typeSelect = document.getElementById('receipt_type');
        const ttSelect = document.getElementById('receipt_id_tt');
        const tttsjSelect = document.getElementById('receipt_id_tttsj');
        const lclSelect = document.getElementById('receipt_id_lcl');

        if (vendorSelect) {
            vendorSelect.addEventListener('change', calculateGrandTotal);
            if (typeof $ !== 'undefined') { $(vendorSelect).on('change', calculateGrandTotal); }
        }
        if (nilaiInput) {
            nilaiInput.addEventListener('input', calculateGrandTotal);
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', () => {
                toggleReceiptList();
                updateReceiptInfo();
            });
        }

        [ttSelect, tttsjSelect, lclSelect].forEach(select => {
            if (select) {
                select.addEventListener('change', updateReceiptInfo);
                if (typeof $ !== 'undefined') { $(select).on('change', updateReceiptInfo); }
            }
        });

        // Initial calculation
        calculateGrandTotal();
        
        // If editing or pre-selected, update info
        if (document.getElementById('final_receipt_id').value) {
            updateReceiptInfo();
        }
    });
</script>
@endpush
@endsection
