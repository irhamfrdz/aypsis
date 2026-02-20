@extends('layouts.app')

@section('title', 'Catat Invoice Vendor')
@section('page_title', 'Catat Invoice Vendor')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4">
                <div class="flex items-center">
                    <a href="{{ route('vendor-invoice.index') }}" class="mr-4 text-white hover:text-teal-100 transition duration-150">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Input Invoice Baru</h1>
                        <p class="text-teal-100 text-sm">Pastikan nomor invoice sesuai dengan fisik dokumen</p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <form action="{{ route('vendor-invoice.store') }}" method="POST" class="p-6 space-y-6" id="invoiceForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vendor -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="vendor_id" class="block text-sm font-bold text-gray-700 mb-1">Vendor <span class="text-red-500">*</span></label>
                        <select name="vendor_id" id="vendor_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 @error('vendor_id') border-red-500 @enderror" required>
                            <option value="">Pilih Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        @error('vendor_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- No Invoice -->
                    <div>
                        <label for="no_invoice" class="block text-sm font-bold text-gray-700 mb-1">No. Invoice <span class="text-red-500">*</span></label>
                        <input type="text" name="no_invoice" id="no_invoice" value="{{ old('no_invoice') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 @error('no_invoice') border-red-500 @enderror" placeholder="Contoh: INV/2024/001" required>
                        @error('no_invoice') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tgl Invoice -->
                    <div>
                        <label for="tgl_invoice" class="block text-sm font-bold text-gray-700 mb-1">Tanggal Invoice <span class="text-red-500">*</span></label>
                        <input type="date" name="tgl_invoice" id="tgl_invoice" value="{{ old('tgl_invoice', date('Y-m-d')) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 @error('tgl_invoice') border-red-500 @enderror" required>
                        @error('tgl_invoice') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-1 md:col-span-2 pt-4 border-t border-gray-100">
                        <h3 class="text-sm font-bold text-teal-700 uppercase tracking-wider mb-4">Rincian Nilai Invoice</h3>
                    </div>

                    <!-- DPP -->
                    <div>
                        <label for="total_dpp" class="block text-sm font-bold text-gray-700 mb-1">Total DPP <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 sm:text-sm">Rp</span>
                            <input type="number" step="0.01" name="total_dpp" id="total_dpp" value="{{ old('total_dpp', 0) }}" class="calc-field block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 @error('total_dpp') border-red-500 @enderror" required>
                        </div>
                    </div>

                    <!-- PPN -->
                    <div>
                        <label for="total_ppn" class="block text-sm font-bold text-gray-700 mb-1">Total PPN <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 sm:text-sm">Rp</span>
                            <input type="number" step="0.01" name="total_ppn" id="total_ppn" value="{{ old('total_ppn', 0) }}" class="calc-field block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 @error('total_ppn') border-red-500 @enderror" required>
                        </div>
                    </div>

                    <!-- PPh 23 -->
                    <div>
                        <label for="total_pph23" class="block text-sm font-bold text-gray-700 mb-1">Total PPh 23 <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 sm:text-sm">Rp</span>
                            <input type="number" step="0.01" name="total_pph23" id="total_pph23" value="{{ old('total_pph23', 0) }}" class="calc-field block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 @error('total_pph23') border-red-500 @enderror" required>
                        </div>
                        <p class="mt-1 text-[10px] text-gray-500 italic">* Nilai ini akan dikurangkan (Deduction)</p>
                    </div>

                    <!-- Materai -->
                    <div>
                        <label for="total_materai" class="block text-sm font-bold text-gray-700 mb-1">Total Materai</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 sm:text-sm">Rp</span>
                            <input type="number" step="0.01" name="total_materai" id="total_materai" value="{{ old('total_materai', 0) }}" class="calc-field block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 @error('total_materai') border-red-500 @enderror">
                        </div>
                    </div>

                    <!-- Netto -->
                    <div class="col-span-1 md:col-span-2 bg-teal-50 p-4 rounded-lg border border-teal-100 mt-2">
                        <label for="total_netto" class="block text-sm font-extrabold text-teal-900 mb-1 uppercase tracking-widest text-center">Total Netto yang Harus Dibayar</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-teal-700 font-bold text-xl">Rp</span>
                            <input type="number" step="0.01" name="total_netto" id="total_netto" value="{{ old('total_netto', 0) }}" class="block w-full pl-14 pr-4 py-3 border-2 border-teal-300 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-2xl font-black text-teal-800 text-right bg-white @error('total_netto') border-red-500 @enderror" required readonly>
                        </div>
                        <p class="mt-2 text-center text-xs text-teal-600 font-medium">Rumus: DPP + PPN - PPh23 + Materai</p>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-8 border-t border-gray-100">
                    <a href="{{ route('vendor-invoice.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition duration-150">Batal</a>
                    <button type="submit" class="px-10 py-2 bg-teal-600 text-white font-black rounded-lg hover:bg-teal-700 shadow-lg hover:shadow-xl transition duration-150">Simpan Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function calculateNetto() {
        let dpp = parseFloat($('#total_dpp').val()) || 0;
        let ppn = parseFloat($('#total_ppn').val()) || 0;
        let pph = parseFloat($('#total_pph23').val()) || 0;
        let materai = parseFloat($('#total_materai').val()) || 0;

        let netto = dpp + ppn - pph + materai;
        $('#total_netto').val(netto.toFixed(2));
    }

    $('.calc-field').on('input change', function() {
        calculateNetto();
    });

    // Initial calculation
    calculateNetto();
});
</script>
@endpush
