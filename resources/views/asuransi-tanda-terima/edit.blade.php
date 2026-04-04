@extends('layouts.app')

@section('title', 'Edit Asuransi Tanda Terima')

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
                <h1 class="text-2xl font-bold text-gray-800">Edit Asuransi Tanda Terima</h1>
            </div>

            <form action="{{ route('asuransi-tanda-terima.update', $asuransiTandaTerima->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Source Info (Read-only for integrity) -->
                    <div class="md:col-span-2 bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <label class="block text-xs font-bold text-blue-700 uppercase mb-1">Terhubung ke (Kunci)</label>
                        <p class="text-gray-900 font-medium">{{ $asuransiTandaTerima->source_type_name }}: {{ $asuransiTandaTerima->source_number }}</p>
                    </div>

                    <!-- Vendor -->
                    <div class="md:col-span-2">
                        <label for="vendor_asuransi_id" class="block text-sm font-medium text-gray-700 mb-2">Vendor Asuransi <span class="text-red-500">*</span></label>
                        <select id="vendor_asuransi_id" name="vendor_asuransi_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent select2">
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" data-tarif="{{ $vendor->tarif }}" {{ old('vendor_asuransi_id', $asuransiTandaTerima->vendor_asuransi_id) == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->nama_asuransi }} (Tarif: {{ $vendor->tarif }}%)
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_asuransi_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Polis Number -->
                    <div>
                        <label for="nomor_polis" class="block text-sm font-medium text-gray-700 mb-2">Nomor Polis <span class="text-red-500">*</span></label>
                        <input type="text" id="nomor_polis" name="nomor_polis" value="{{ old('nomor_polis', $asuransiTandaTerima->nomor_polis) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('nomor_polis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Polis Date -->
                    <div>
                        <label for="tanggal_polis" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Polis <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_polis" name="tanggal_polis" value="{{ old('tanggal_polis', $asuransiTandaTerima->tanggal_polis ? $asuransiTandaTerima->tanggal_polis->format('Y-m-d') : '') }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('tanggal_polis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    @php
                        $currentRate = $asuransiTandaTerima->asuransi_rate;
                        if ($currentRate <= 0 && $asuransiTandaTerima->nilai_pertanggungan > 0) {
                            $currentRate = ($asuransiTandaTerima->premi / $asuransiTandaTerima->nilai_pertanggungan * 100);
                        }
                        if ($currentRate <= 0) {
                            $currentRate = $asuransiTandaTerima->vendorAsuransi->tarif ?? 0;
                        }
                    @endphp

                    <!-- Nilai Barang -->
                    <div>
                        <label for="nilai_barang" class="block text-sm font-medium text-gray-700 mb-2">Nilai Barang <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="number" id="nilai_barang" name="nilai_barang" value="{{ old('nilai_barang', $asuransiTandaTerima->nilai_pertanggungan) }}" required
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        @error('nilai_barang') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Rate Asuransi -->
                    <div>
                        <label for="asuransi_rate" class="block text-sm font-medium text-gray-700 mb-2">Rate Asuransi (%) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.0001" id="asuransi_rate" name="asuransi_rate" value="{{ old('asuransi_rate', number_format($currentRate, 4, '.', '')) }}" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: 0.2">
                        @error('asuransi_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Grand Total -->
                    <div>
                        <label for="grand_total" class="block text-sm font-medium text-gray-700 mb-2">Grand Total (Premi)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="text" id="grand_total" name="grand_total" value="0" readonly
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 bg-gray-50 focus:outline-none font-semibold text-gray-800">
                        </div>
                        <p class="mt-1 text-xs text-gray-500" id="tarif_info_text">Tarif default vendor: 0%</p>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label for="asuransi_file" class="block text-sm font-medium text-gray-700 mb-2">Update Dokumen Asuransi</label>
                        <input type="file" id="asuransi_file" name="asuransi_file"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @if($asuransiTandaTerima->asuransi_path)
                            <p class="mt-1 text-xs text-blue-600 italic flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A1 1 0 0111 2.293l4.707 4.707a1 1 0 01.293.707V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                Dokumen saat ini sudah ada. Unggah baru untuk mengganti.
                            </p>
                        @endif
                        @error('asuransi_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Ship & Voyage Info -->
                    <div>
                        <label for="nomor_urut" class="block text-sm font-medium text-gray-700 mb-2">Nomor Urut</label>
                        <input type="text" id="nomor_urut" name="nomor_urut" value="{{ old('nomor_urut', $asuransiTandaTerima->nomor_urut) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: 001">
                        @error('nomor_urut') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">Nama Kapal</label>
                        <input type="text" id="nama_kapal" name="nama_kapal" value="{{ old('nama_kapal', $asuransiTandaTerima->nama_kapal) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: MV. SEA STAR">
                        @error('nama_kapal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nomor_voyage" class="block text-sm font-medium text-gray-700 mb-2">Nomor Voyage</label>
                        <input type="text" id="nomor_voyage" name="nomor_voyage" value="{{ old('nomor_voyage', $asuransiTandaTerima->nomor_voyage) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: V.012">
                        @error('nomor_voyage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('keterangan', $asuransiTandaTerima->keterangan) }}</textarea>
                        @error('keterangan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('asuransi-tanda-terima.index') }}"
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition duration-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function calculateGrandTotal() {
        const select = document.getElementById('vendor_asuransi_id');
        const nilaiInput = document.getElementById('nilai_barang');
        const rateInput = document.getElementById('asuransi_rate');
        const grandTotalInput = document.getElementById('grand_total');
        const tarifInfo = document.getElementById('tarif_info_text');

        if (!nilaiInput || !rateInput || !grandTotalInput) return;

        const rate = parseFloat(rateInput.value) || 0;
        const nilai = parseFloat(nilaiInput.value) || 0;

        const total = nilai * (rate / 100);
        
        const formatted = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).format(total);
        
        grandTotalInput.value = formatted;
        
        if (select && tarifInfo) {
            const selectedOption = select.options[select.selectedIndex];
            const defaultRate = selectedOption ? parseFloat(selectedOption.getAttribute('data-tarif')) || 0 : 0;
            tarifInfo.textContent = 'Tarif default vendor: ' + defaultRate + '%';
        }
    }

    function onVendorChange() {
        const select = document.getElementById('vendor_asuransi_id');
        const rateInput = document.getElementById('asuransi_rate');
        if (!select || !rateInput) return;

        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const tarif = parseFloat(selectedOption.getAttribute('data-tarif')) || 0;
            rateInput.value = tarif;
        }
        calculateGrandTotal();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const vendorSelect = document.getElementById('vendor_asuransi_id');
        const nilaiInput = document.getElementById('nilai_barang');
        const rateInput = document.getElementById('asuransi_rate');

        if (vendorSelect) {
            vendorSelect.addEventListener('change', onVendorChange);
            if (typeof $ !== 'undefined') {
                $(vendorSelect).on('change', onVendorChange);
            }
        }
        if (nilaiInput) {
            nilaiInput.addEventListener('input', calculateGrandTotal);
        }
        if (rateInput) {
            rateInput.addEventListener('input', calculateGrandTotal);
        }

        calculateGrandTotal();
    });
</script>
@endpush
@endsection
