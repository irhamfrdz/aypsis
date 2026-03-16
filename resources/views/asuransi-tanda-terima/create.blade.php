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
                                <option value="{{ $vendor->id }}" {{ old('vendor_asuransi_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->nama_asuransi }}
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

                    <!-- Premi -->
                    <div>
                        <label for="premi" class="block text-sm font-medium text-gray-700 mb-2">Premi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="number" id="premi" name="premi" value="{{ old('premi', 0) }}" required
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        @error('premi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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

    // Initialize list toggle
    document.addEventListener('DOMContentLoaded', function() {
        toggleReceiptList();
        syncReceiptId();
    });
</script>
@endpush
@endsection
