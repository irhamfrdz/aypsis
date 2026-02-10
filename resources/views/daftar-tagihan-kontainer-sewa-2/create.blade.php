@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Tagihan Kontainer Sewa</h1>
                <p class="text-sm text-gray-600 mt-1">Isi form di bawah untuk menambah tagihan kontainer baru</p>
            </div>
            <a href="{{ route('daftar-tagihan-kontainer-sewa-2.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center">
                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                <div class="flex items-center mb-2">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-semibold">Terdapat kesalahan:</span>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('daftar-tagihan-kontainer-sewa-2.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200">
            @csrf

            <div class="p-6 space-y-6">
                <!-- Section: Informasi Kontainer -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kontainer</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Vendor -->
                        <div>
                            <label for="vendor" class="block text-sm font-medium text-gray-700 mb-2">
                                Vendor <span class="text-red-500">*</span>
                            </label>
                            <select name="vendor" 
                                    id="vendor" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('vendor') border-red-500 @enderror"
                                    required
                                    >
                                <option value="">-- Pilih Vendor --</option>
                                @if(isset($vendors) && $vendors->count() > 0)
                                    @foreach($vendors as $vendorItem)
                                        <option value="{{ $vendorItem }}" {{ old('vendor') == $vendorItem ? 'selected' : '' }}>
                                            {{ $vendorItem }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('vendor')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nomor Kontainer -->
                        <div>
                            <label for="nomor_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Kontainer <span class="text-red-500">*</span>
                            </label>
                            <select name="nomor_kontainer" 
                                    id="nomor_kontainer" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nomor_kontainer') border-red-500 @enderror">
                                <option value="">-- Pilih dari List atau Input Manual --</option>
                                @if(isset($containersData) && $containersData->count() > 0)
                                    @foreach($containersData as $container)
                                        <option value="{{ $container->nomor_seri_gabungan }}" 
                                                data-vendor="{{ $container->vendor }}"
                                                data-size="{{ $container->ukuran }}"
                                                {{ old('nomor_kontainer') == $container->nomor_seri_gabungan ? 'selected' : '' }}
                                                style="display: none;">
                                            {{ $container->nomor_seri_gabungan }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <input type="text" 
                                   name="nomor_kontainer_manual" 
                                   id="nomor_kontainer_manual" 
                                   value="{{ old('nomor_kontainer_manual') }}"
                                   class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Atau input nomor kontainer manual">
                            @error('nomor_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('nomor_kontainer_manual')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Size -->
                        <div>
                            <label for="size" class="block text-sm font-medium text-gray-700 mb-2">
                                Ukuran Kontainer
                            </label>
                            <select name="size" 
                                    id="size" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Pilih Ukuran --</option>
                                <option value="20" {{ old('size') == '20' ? 'selected' : '' }}>20 ft</option>
                                <option value="40" {{ old('size') == '40' ? 'selected' : '' }}>40 ft</option>
                            </select>
                            @error('size')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Group -->
                        <div>
                            <label for="group" class="block text-sm font-medium text-gray-700 mb-2">
                                Group
                            </label>
                            <input type="text" 
                                   name="group" 
                                   id="group" 
                                   value="{{ old('group') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Kosongkan jika tidak ada grup">
                            @error('group')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Invoice Vendor -->
                        <div>
                            <label for="invoice_vendor" class="block text-sm font-medium text-gray-700 mb-2">
                                Invoice Vendor
                            </label>
                            <input type="text" 
                                   name="invoice_vendor" 
                                   id="invoice_vendor" 
                                   value="{{ old('invoice_vendor') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('invoice_vendor') border-red-500 @enderror"
                                   placeholder="Nomor Invoice Vendor">
                            @error('invoice_vendor')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Invoice Vendor -->
                        <div>
                            <label for="tanggal_invoice_vendor" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Invoice Vendor
                            </label>
                            <input type="date" 
                                   name="tanggal_invoice_vendor" 
                                   id="tanggal_invoice_vendor" 
                                   value="{{ old('tanggal_invoice_vendor') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_invoice_vendor') border-red-500 @enderror">
                            @error('tanggal_invoice_vendor')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Periode & Masa -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Periode & Masa Sewa</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tanggal Awal -->
                        <div>
                            <label for="tanggal_awal" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Awal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="tanggal_awal" 
                                   id="tanggal_awal" 
                                   value="{{ old('tanggal_awal') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_awal') border-red-500 @enderror"
                                   required>
                            @error('tanggal_awal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Akhir -->
                        <div>
                            <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Akhir
                            </label>
                            <input type="date" 
                                   name="tanggal_akhir" 
                                   id="tanggal_akhir" 
                                   value="{{ old('tanggal_akhir') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('tanggal_akhir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Periode -->
                        <div>
                            <label for="periode" class="block text-sm font-medium text-gray-700 mb-2">
                                Periode <span class="text-xs text-gray-500">(Bulan ke-)</span>
                            </label>
                            <input type="number" 
                                   name="periode" 
                                   id="periode" 
                                   value="{{ old('periode', 1) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   min="1"
                                   placeholder="1">
                            <p class="mt-1 text-xs text-gray-500">Contoh: Periode 1 = bulan pertama sewa, Periode 2 = bulan kedua, dst.</p>
                            @error('periode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Masa -->
                        <div>
                            <label for="masa" class="block text-sm font-medium text-gray-700 mb-2">
                                Masa
                            </label>
                            <input type="text" 
                                   name="masa" 
                                   id="masa" 
                                   value="{{ old('masa') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Contoh: Januari 2024">
                            @error('masa')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Tarif & Biaya -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Tarif & Biaya</h2>
                    
                    <!-- Pricelist Preview -->
                    <div id="pricelist-preview" class="hidden mb-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-semibold text-blue-800">Harga Pricelist Terdeteksi:</span>
                            </div>
                            <div class="text-right">
                                <span id="pricelist-vendor" class="text-sm text-gray-600"></span>
                                <span id="pricelist-size" class="text-sm text-gray-600 ml-2"></span>
                                <span id="pricelist-tarif" class="text-sm text-gray-600 ml-2"></span>
                            </div>
                        </div>
                        <div class="mt-2 grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-500">Harga per unit:</span>
                                <span id="pricelist-harga" class="ml-2 font-bold text-green-600">-</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Berlaku sejak:</span>
                                <span id="pricelist-berlaku" class="ml-2 text-gray-700">-</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tarif -->
                        <div>
                            <label for="tarif" class="block text-sm font-medium text-gray-700 mb-2">
                                Tarif <span class="text-red-500">*</span>
                            </label>
                            <select name="tarif" 
                                    id="tarif" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                <option value="">-- Pilih Tarif --</option>
                                <option value="Bulanan" {{ old('tarif') == 'Bulanan' ? 'selected' : '' }}>📅 Bulanan</option>
                                <option value="Harian" {{ old('tarif') == 'Harian' ? 'selected' : '' }}>📆 Harian</option>
                            </select>
                            @error('tarif')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Adjustment -->
                        <div>
                            <label for="adjustment" class="block text-sm font-medium text-gray-700 mb-2">
                                Adjustment
                            </label>
                            <input type="number" 
                                   name="adjustment" 
                                   id="adjustment" 
                                   value="{{ old('adjustment', 0) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   step="0.01"
                                   placeholder="0">
                            @error('adjustment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- DPP -->
                        <div>
                            <label for="dpp" class="block text-sm font-medium text-gray-700 mb-2">
                                DPP
                            </label>
                            <input type="number" 
                                name="dpp" 
                                id="dpp" 
                                value="{{ old('dpp', 0) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50"
                                readonly
                                step="0.01"
                                placeholder="0">
                            @error('dpp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- DPP Nilai Lain -->
                        <div>
                            <label for="dpp_nilai_lain" class="block text-sm font-medium text-gray-700 mb-2">
                                DPP Nilai Lain (11/12)
                            </label>
                            <input type="number" 
                                   name="dpp_nilai_lain" 
                                   id="dpp_nilai_lain" 
                                   value="{{ old('dpp_nilai_lain', 0) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   step="0.01"
                                   placeholder="0">
                            @error('dpp_nilai_lain')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- PPN -->
                        <div>
                            <label for="ppn" class="block text-sm font-medium text-gray-700 mb-2">
                                PPN (12%)
                            </label>
                            <input type="number" 
                                   name="ppn" 
                                   id="ppn" 
                                   value="{{ old('ppn', 0) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   step="0.01"
                                   placeholder="0">
                            @error('ppn')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- PPH -->
                        <div>
                            <label for="pph" class="block text-sm font-medium text-gray-700 mb-2">
                                PPH (2%)
                            </label>
                            <input type="number" 
                                   name="pph" 
                                   id="pph" 
                                   value="{{ old('pph', 0) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   step="0.01"
                                   placeholder="0">
                            @error('pph')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Grand Total -->
                        <div class="md:col-span-2">
                            <label for="grand_total" class="block text-sm font-medium text-gray-700 mb-2">
                                Grand Total
                            </label>
                            <input type="number" 
                                   name="grand_total" 
                                   id="grand_total" 
                                   value="{{ old('grand_total', 0) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50"
                                   step="0.01"
                                   placeholder="0"
                                   readonly>
                            @error('grand_total')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Grand Total akan dihitung otomatis: DPP + PPN - PPH</p>
                        </div>
                    </div>
                </div>

                <!-- Section: Status -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Status</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status Kontainer
                            </label>
                            <select name="status" 
                                    id="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Pilih Status --</option>
                                <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex items-center justify-end space-x-3">
                <a href="{{ route('daftar-tagihan-kontainer-sewa-2.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-150">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-150 flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Tagihan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Filter containers based on selected vendor
function filterContainersByVendor() {
    const containerSelect = document.getElementById('nomor_kontainer');
    const vendorSelect = document.getElementById('vendor');
    const sizeSelect = document.getElementById('size');
    const tarifSelect = document.getElementById('tarif');
    const tanggalAwalInput = document.getElementById('tanggal_awal');
    const tanggalAkhirInput = document.getElementById('tanggal_akhir');
    const periodeInput = document.getElementById('periode');
    const selectedVendor = vendorSelect.value;
    
    // Reset container selection
    containerSelect.value = '';
    
    // Get all container options
    const options = containerSelect.querySelectorAll('option');
    
    // Show/hide options based on vendor
    options.forEach(option => {
        if (option.value === '') {
            // Update placeholder text
            if (selectedVendor) {
                option.textContent = '-- Pilih dari List --';
            } else {
                option.textContent = '-- Pilih Vendor Terlebih Dahulu --';
            }
            option.style.display = '';
        } else {
            const optionVendor = option.getAttribute('data-vendor');
            if (selectedVendor && optionVendor === selectedVendor) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        }
    });
}

// Auto-fill size when container is selected
function fillContainerSize() {
    const containerSelect = document.getElementById('nomor_kontainer');
    const vendorSelect = document.getElementById('vendor');
    const sizeSelect = document.getElementById('size');
    const selectedOption = containerSelect.options[containerSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const size = selectedOption.getAttribute('data-size');
        if (size) {
            sizeSelect.value = size;
            // Trigger change event to run fetchPricelistDpp via listener
            const event = new Event('change', { bubbles: true });
            sizeSelect.dispatchEvent(event);
        }
    }
}

// Format date to ddmmmyyyy
function formatDateToDDMMMYYYY(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    
    const day = String(date.getDate()).padStart(2, '0');
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const month = months[date.getMonth()];
    const year = date.getFullYear();
    
    return `${day}${month}${year}`;
}

// Convert ddmmmyyyy to yyyy-mm-dd for input value
function parseDDMMMYYYY(dateString) {
    if (!dateString) return '';
    
    // Match format like 21Nov2024
    const regex = /^(\d{2})([A-Za-z]{3})(\d{4})$/;
    const match = dateString.match(regex);
    
    if (!match) return '';
    
    const day = match[1];
    const monthStr = match[2];
    const year = match[3];
    
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const monthIndex = months.findIndex(m => m.toLowerCase() === monthStr.toLowerCase());
    
    if (monthIndex === -1) return '';
    
    const month = String(monthIndex + 1).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Setup date input formatting
function setupDateFormatting(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    // Store the actual date value
    input.dataset.actualValue = input.value;
    
    // Create a display element
    const displaySpan = document.createElement('span');
    displaySpan.className = 'absolute left-3 top-1/2 -translate-y-1/2 text-gray-700 pointer-events-none';
    displaySpan.style.display = 'none';
    
    // Wrap input in relative container
    const wrapper = document.createElement('div');
    wrapper.className = 'relative';
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);
    wrapper.appendChild(displaySpan);
    
    // Update display when date changes
    input.addEventListener('change', function() {
        const formatted = formatDateToDDMMMYYYY(this.value);
        if (formatted) {
            displaySpan.textContent = formatted;
            displaySpan.style.display = 'block';
            this.style.color = 'transparent';
        } else {
            displaySpan.style.display = 'none';
            this.style.color = '';
        }
        this.dataset.actualValue = this.value;
    });
    
    // Show formatted date on load if value exists
    if (input.value) {
        const formatted = formatDateToDDMMMYYYY(input.value);
        if (formatted) {
            displaySpan.textContent = formatted;
            displaySpan.style.display = 'block';
            input.style.color = 'transparent';
        }
    }
    
    // Reset color when focused
    input.addEventListener('focus', function() {
        this.style.color = '';
        displaySpan.style.display = 'none';
    });
    
    // Reapply formatting when blurred
    input.addEventListener('blur', function() {
        if (this.value) {
            const formatted = formatDateToDDMMMYYYY(this.value);
            if (formatted) {
                displaySpan.textContent = formatted;
                displaySpan.style.display = 'block';
                this.style.color = 'transparent';
            }
        }
    });
}

// Auto-calculate Grand Total
document.addEventListener('DOMContentLoaded', function() {
    const dppInput = document.getElementById('dpp');
    const ppnInput = document.getElementById('ppn');
    const pphInput = document.getElementById('pph');
    const grandTotalInput = document.getElementById('grand_total');
    const dppNilaiLainInput = document.getElementById('dpp_nilai_lain');
    const containerSelect = document.getElementById('nomor_kontainer');
    const vendorSelect = document.getElementById('vendor');
    const sizeSelect = document.getElementById('size');
    const tarifSelect = document.getElementById('tarif');
    const tanggalAwalInput = document.getElementById('tanggal_awal');
    const tanggalAkhirInput = document.getElementById('tanggal_akhir');
    const periodeInput = document.getElementById('periode');
    
    // Initialize container filtering on page load
    filterContainersByVendor();
    
    // Listen to container selection changes
    containerSelect.addEventListener('change', fillContainerSize);
    // Listen to vendor/size/tarif/date/period changes to fetch pricelist and auto-fill DPP
    if (vendorSelect) {
        vendorSelect.addEventListener('change', fetchPricelistDpp);
        vendorSelect.addEventListener('change', filterContainersByVendor);
    }
    if (sizeSelect) sizeSelect.addEventListener('change', fetchPricelistDpp);
    if (tarifSelect) tarifSelect.addEventListener('change', fetchPricelistDpp);
    if (tanggalAwalInput) tanggalAwalInput.addEventListener('change', fetchPricelistDpp);
    if (tanggalAkhirInput) tanggalAkhirInput.addEventListener('change', fetchPricelistDpp);
    if (periodeInput) periodeInput.addEventListener('input', fetchPricelistDpp);
    
    // Setup date formatting for all date inputs
    setupDateFormatting('tanggal_awal');
    setupDateFormatting('tanggal_akhir');
    setupDateFormatting('tanggal_invoice_vendor');

    // Auto-generate Masa from tanggal_awal and tanggal_akhir
    function generateMasa() {
        const masaInput = document.getElementById('masa');
        if (!tanggalAwalInput || !tanggalAkhirInput || !masaInput) return;
        
        const tanggalAwal = tanggalAwalInput.value;
        const tanggalAkhir = tanggalAkhirInput.value;
        
        if (!tanggalAwal) return;
        
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        const dateAwal = new Date(tanggalAwal);
        const dayAwal = dateAwal.getDate();
        const monthAwal = months[dateAwal.getMonth()];
        const yearAwal = dateAwal.getFullYear();
        
        if (tanggalAkhir) {
            const dateAkhir = new Date(tanggalAkhir);
            const dayAkhir = dateAkhir.getDate();
            const monthAkhir = months[dateAkhir.getMonth()];
            const yearAkhir = dateAkhir.getFullYear();
            
            masaInput.value = `${dayAwal} ${monthAwal} ${yearAwal} - ${dayAkhir} ${monthAkhir} ${yearAkhir}`;
        } else {
            masaInput.value = `${dayAwal} ${monthAwal} ${yearAwal}`;
        }
    }
    
    // Listen to tanggal changes for auto-generate masa
    if (tanggalAwalInput) tanggalAwalInput.addEventListener('change', generateMasa);
    if (tanggalAkhirInput) tanggalAkhirInput.addEventListener('change', generateMasa);
    
    // Update pricelist preview
    function updatePricelistPreview(data) {
        const previewEl = document.getElementById('pricelist-preview');
        if (!previewEl) return;
        
        if (data && data.success && data.harga_pricelist) {
            document.getElementById('pricelist-vendor').textContent = data.vendor || '';
            document.getElementById('pricelist-size').textContent = data.size ? data.size + ' ft' : '';
            document.getElementById('pricelist-tarif').textContent = data.tarif || '';
            document.getElementById('pricelist-harga').textContent = 'Rp ' + formatNumber(data.harga_pricelist);
            document.getElementById('pricelist-berlaku').textContent = data.tanggal_berlaku || '-';
            previewEl.classList.remove('hidden');
        } else {
            previewEl.classList.add('hidden');
        }
    }
    
    // Format number with thousand separators
    function formatNumber(num) {
        return parseFloat(num || 0).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
    
    // Fetch computed DPP & taxes from pricelist API
    function fetchPricelistDpp() {
        const vendor = (vendorSelect && vendorSelect.value) ? vendorSelect.value : '';
        const size = (sizeSelect && sizeSelect.value) ? sizeSelect.value : '';
        const tarif = (tarifSelect && tarifSelect.value) ? tarifSelect.value : '';
        const tanggal_awal = (tanggalAwalInput && tanggalAwalInput.value) ? tanggalAwalInput.value : '';
        const tanggal_akhir = (tanggalAkhirInput && tanggalAkhirInput.value) ? tanggalAkhirInput.value : '';
        const periode = (periodeInput && periodeInput.value) ? periodeInput.value : '';

        if (!vendor || !size) {
            updatePricelistPreview(null);
            return;
        }

        const params = new URLSearchParams({ vendor, size, tarif, tanggal_awal, tanggal_akhir, periode });
        const url = '{{ route("daftar-tagihan-kontainer-sewa-2.get_pricelist") }}' + '?' + params.toString();

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (!data || !data.success) {
                    updatePricelistPreview(null);
                    return;
                }
                
                // Update pricelist preview
                updatePricelistPreview(data);
                
                dppInput.value = parseFloat(data.dpp || 0).toFixed(2);
                dppNilaiLainInput.value = parseFloat(data.dpp_nilai_lain || 0).toFixed(2);
                ppnInput.value = parseFloat(data.ppn || 0).toFixed(2);
                pphInput.value = parseFloat(data.pph || 0).toFixed(2);
                grandTotalInput.value = parseFloat(data.grand_total || 0).toFixed(2);
                if (data.tarif && !tarifSelect.value) {
                    for (let i = 0; i < tarifSelect.options.length; i++) {
                        if (tarifSelect.options[i].value.toLowerCase() === data.tarif.toLowerCase()) {
                            tarifSelect.value = tarifSelect.options[i].value; 
                            break;
                        }
                    }
                }
                // Recompute derived values and ensure events are triggered
                calculateDppNilaiLain();
                calculatePph();
            })
            .catch(err => { 
                if (window.console) console.warn('fetchPricelistDpp failed', err);
                updatePricelistPreview(null);
            });
    }

    function parseNumber(str) {
        if (!str) return 0;
        let s = String(str).trim();
        // Normalize: handle both '1,234.56' and '1.234,56'
        if (s.indexOf(',') > -1 && s.indexOf('.') > -1) {
            if (s.lastIndexOf(',') > s.lastIndexOf('.')) {
                // comma likely decimal separator: remove dots and convert comma -> dot
                s = s.replace(/\./g, '');
                s = s.replace(',', '.');
            } else {
                // dot likely decimal separator: remove commas
                s = s.replace(/,/g, '');
            }
        } else {
            // Only commas or only dots or neither
            s = s.replace(/,/g, '');
        }
        const n = parseFloat(s);
        return isNaN(n) ? 0 : n;
    }

    function calculateGrandTotal() {
        const dpp = parseNumber(dppInput.value);
        const ppn = parseNumber(ppnInput.value);
        const pph = parseNumber(pphInput.value);

        const grandTotal = dpp + ppn - pph;
        grandTotalInput.value = grandTotal.toFixed(2);
        // Debug log
        if (window.console && window.console.debug) console.debug('calculateGrandTotal', { dpp, ppn, pph, grandTotal });
    }

    function calculateDppNilaiLain() {
        const dpp = parseNumber(dppInput.value) || 0;
        const dppNilaiLain = (dpp * 11 / 12).toFixed(2);
        dppNilaiLainInput.value = dppNilaiLain;
        
        // Auto-calculate PPN from DPP Nilai Lain
        calculatePpn();
    }

    function calculatePpn() {
        const dppNilaiLain = parseNumber(dppNilaiLainInput.value) || 0;
        const ppn = (dppNilaiLain * 0.12).toFixed(2);
        ppnInput.value = ppn;
        
        calculateGrandTotal();
    }

    function calculatePph() {
        const dpp = parseNumber(dppInput.value) || 0;
        const pph = (dpp * 0.02).toFixed(2);
        pphInput.value = pph;
        
        calculateGrandTotal();
    }

    // Listen to DPP changes (input/change/keyup)
    ['input', 'change', 'keyup'].forEach(evt => {
        dppInput.addEventListener(evt, function() {
            if (this.value === '' || this.value === null) {
                dppNilaiLainInput.value = '';
                pphInput.value = '';
                grandTotalInput.value = '';
                return;
            }
            calculateDppNilaiLain();
            calculatePph();
        });
    });

    // Listen to manual DPP Nilai Lain changes
    ['input', 'change', 'keyup'].forEach(evt => dppNilaiLainInput.addEventListener(evt, calculatePpn));

    // Listen to manual PPN changes
    ['input', 'change', 'keyup'].forEach(evt => ppnInput.addEventListener(evt, calculateGrandTotal));

    // Listen to manual PPH changes
    ['input', 'change', 'keyup'].forEach(evt => pphInput.addEventListener(evt, calculateGrandTotal));

    // Calculate on page load if values exist
    if (dppInput.value) {
        calculateDppNilaiLain();
        calculatePph();
    }
    // Attempt to fetch pricelist DPP on load if vendor/size available
    fetchPricelistDpp();
});
</script>
@endpush
@endsection
