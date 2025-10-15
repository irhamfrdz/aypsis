@extends('layouts.app')

@section('title', 'Edit Order')
@section('page_title', 'Edit Order')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Order</h1>
                    <p class="mt-1 text-sm text-gray-600">Ubah informasi order</p>
                </div>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('orders.update', $order) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nomor Order -->
                        <div>
                            <label for="nomor_order" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Order <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_order" id="nomor_order" value="{{ old('nomor_order', $order->nomor_order) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @error('nomor_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Order -->
                        <div>
                            <label for="tanggal_order" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Order <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_order" id="tanggal_order" value="{{ old('tanggal_order', $order->tanggal_order->format('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- No Tiket/DO -->
                        <div>
                            <label for="no_tiket_do" class="block text-sm font-medium text-gray-700 mb-2">
                                No Tiket/DO
                            </label>
                            <input type="text" name="no_tiket_do" id="no_tiket_do" value="{{ old('no_tiket_do', $order->no_tiket_do) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Pilih Status</option>
                                <option value="draft" {{ old('status', $order->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="confirmed" {{ old('status', $order->status) === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing" {{ old('status', $order->status) === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ old('status', $order->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $order->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Destination Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tujuan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tujuan Kirim -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="tujuan_kirim_id" class="text-sm font-medium text-gray-700">
                                    Tujuan Kirim <span class="text-red-500">*</span>
                                </label>
                                <a href="{{ route('tujuan-kirim.create') }}" target="_blank"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container">
                                    <input type="text" id="search_tujuan_kirim" placeholder="Search..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="tujuan_kirim_id" id="tujuan_kirim_id" required
                                            class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 @error('tujuan_kirim_id') border-red-500 @enderror">
                                        <option value="">Select an option</option>
                                        @foreach($tujuanKirims as $tujuanKirim)
                                            @php
                                                $isSelected = false;
                                                if (old('tujuan_kirim_id')) {
                                                    $isSelected = old('tujuan_kirim_id') == $tujuanKirim->id;
                                                } else {
                                                    // Check if this tujuan kirim matches the current order's tujuan_kirim
                                                    $isSelected = $order->tujuan_kirim == $tujuanKirim->nama_tujuan;
                                                }
                                            @endphp
                                            <option value="{{ $tujuanKirim->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $tujuanKirim->nama_tujuan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                        <!-- Options will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                            @error('tujuan_kirim_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="tujuan_ambil_id" class="text-sm font-medium text-gray-700">
                                    Tujuan Ambil <span class="text-red-500">*</span>
                                </label>
                                <a href="{{ route('master.tujuan-kegiatan-utama.create') }}" target="_blank"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-ambil">
                                    <input type="text" id="search_tujuan_ambil" placeholder="Search..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="tujuan_ambil_id" id="tujuan_ambil_id" required
                                            class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                        <option value="">Select an option</option>
                                        @foreach($tujuanKegiatanUtamas as $tujuanKegiatanUtama)
                                            @php
                                                $isSelected = false;
                                                if (old('tujuan_ambil_id')) {
                                                    $isSelected = old('tujuan_ambil_id') == $tujuanKegiatanUtama->id;
                                                } else {
                                                    // Check if this tujuan ambil matches the current order's tujuan_ambil
                                                    $isSelected = $order->tujuan_ambil == $tujuanKegiatanUtama->ke;
                                                }
                                            @endphp
                                            <option value="{{ $tujuanKegiatanUtama->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $tujuanKegiatanUtama->ke }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options_ambil" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                        <!-- Options will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                            @error('tujuan_ambil_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Master Data Relations -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Data Master</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Term -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="term_id" class="text-sm font-medium text-gray-700">
                                    Term
                                </label>
                                <a href="{{ route('term.create') }}" target="_blank"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-term">
                                    <input type="text" id="search_term" placeholder="Search..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="term_id" id="term_id"
                                            class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                        <option value="">Select an option</option>
                                        @foreach($terms as $term)
                                            @php
                                                $isSelected = false;
                                                if (old('term_id')) {
                                                    $isSelected = old('term_id') == $term->id;
                                                } else {
                                                    $isSelected = $order->term_id == $term->id;
                                                }
                                            @endphp
                                            <option value="{{ $term->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $term->nama_status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options_term" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                        <!-- Options will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pengirim -->
                        <div>
                            <label for="pengirim_id" class="block text-sm font-medium text-gray-700 mb-2">Pengirim</label>
                            <select name="pengirim_id" id="pengirim_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Pilih Pengirim</option>
                                @foreach($pengirims as $pengirim)
                                    <option value="{{ $pengirim->id }}" {{ old('pengirim_id', $order->pengirim_id) == $pengirim->id ? 'selected' : '' }}>
                                        {{ $pengirim->kode }} - {{ $pengirim->nama_pengirim }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jenis Barang -->
                        <div>
                            <label for="jenis_barang_id" class="block text-sm font-medium text-gray-700 mb-2">Jenis Barang</label>
                            <select name="jenis_barang_id" id="jenis_barang_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Pilih Jenis Barang</option>
                                @foreach($jenisBarangs as $jenisBarang)
                                    <option value="{{ $jenisBarang->id }}" {{ old('jenis_barang_id', $order->jenis_barang_id) == $jenisBarang->id ? 'selected' : '' }}>
                                        {{ $jenisBarang->kode }} - {{ $jenisBarang->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Container Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kontainer</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Size Kontainer <span class="text-red-500">*</span></label>
                            <select name="size_kontainer" id="size_kontainer" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('size_kontainer') border-red-500 @enderror">
                                <option value="">Pilih Size Kontainer</option>
                                @foreach($ukuranKontainers as $ukuran)
                                    <option value="{{ $ukuran }}" {{ old('size_kontainer', $order->size_kontainer) === $ukuran ? 'selected' : '' }}>
                                        {{ $ukuran }}
                                    </option>
                                @endforeach
                            </select>
                            @error('size_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="unit_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Unit Kontainer <span class="text-red-500">*</span></label>
                            <input type="number" name="unit_kontainer" id="unit_kontainer" value="{{ old('unit_kontainer', $order->unit_kontainer) }}" required min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <small class="text-gray-500">Units untuk outstanding: {{ $order->units ?? 0 }} | Sisa: {{ $order->sisa ?? 0 }}</small>
                        </div>

                        <div>
                            <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer <span class="text-red-500">*</span></label>
                            <select name="tipe_kontainer" id="tipe_kontainer" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Pilih Tipe</option>
                                <option value="fcl" {{ old('tipe_kontainer', $order->tipe_kontainer) === 'fcl' ? 'selected' : '' }}>FCL</option>
                                <option value="lcl" {{ old('tipe_kontainer', $order->tipe_kontainer) === 'lcl' ? 'selected' : '' }}>LCL</option>
                                <option value="cargo" {{ old('tipe_kontainer', $order->tipe_kontainer) === 'cargo' ? 'selected' : '' }}>Cargo</option>
                                <option value="fcl_plus" {{ old('tipe_kontainer', $order->tipe_kontainer) === 'fcl_plus' ? 'selected' : '' }}>FCL Plus</option>
                            </select>
                        </div>

                        <div>
                            <label for="tanggal_pickup" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pickup</label>
                            <input type="date" name="tanggal_pickup" id="tanggal_pickup" value="{{ old('tanggal_pickup', $order->tanggal_pickup?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Document Types -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tipe Dokumen</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- FTZ03 Options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">FTZ03</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" name="ftz03_option" id="exclude_ftz03" value="exclude" {{ old('ftz03_option', ($order->exclude_ftz03 ? 'exclude' : ($order->include_ftz03 ? 'include' : 'none'))) === 'exclude' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="exclude_ftz03" class="ml-2 block text-sm text-gray-900">Exclude FTZ03</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="ftz03_option" id="include_ftz03" value="include" {{ old('ftz03_option', ($order->exclude_ftz03 ? 'exclude' : ($order->include_ftz03 ? 'include' : 'none'))) === 'include' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="include_ftz03" class="ml-2 block text-sm text-gray-900">Include FTZ03</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="ftz03_option" id="none_ftz03" value="none" {{ old('ftz03_option', ($order->exclude_ftz03 ? 'exclude' : ($order->include_ftz03 ? 'include' : 'none'))) === 'none' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="none_ftz03" class="ml-2 block text-sm text-gray-900">Tidak ada</label>
                                </div>
                            </div>
                        </div>

                        <!-- SPPB Options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">SPPB</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" name="sppb_option" id="exclude_sppb" value="exclude" {{ old('sppb_option', ($order->exclude_sppb ? 'exclude' : ($order->include_sppb ? 'include' : 'none'))) === 'exclude' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="exclude_sppb" class="ml-2 block text-sm text-gray-900">Exclude SPPB</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="sppb_option" id="include_sppb" value="include" {{ old('sppb_option', ($order->exclude_sppb ? 'exclude' : ($order->include_sppb ? 'include' : 'none'))) === 'include' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="include_sppb" class="ml-2 block text-sm text-gray-900">Include SPPB</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="sppb_option" id="none_sppb" value="none" {{ old('sppb_option', ($order->exclude_sppb ? 'exclude' : ($order->include_sppb ? 'include' : 'none'))) === 'none' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="none_sppb" class="ml-2 block text-sm text-gray-900">Tidak ada</label>
                                </div>
                            </div>
                        </div>

                        <!-- Buruh Bongkar Options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Buruh Bongkar</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" name="buruh_bongkar_option" id="exclude_buruh_bongkar" value="exclude" {{ old('buruh_bongkar_option', ($order->exclude_buruh_bongkar ? 'exclude' : ($order->include_buruh_bongkar ? 'include' : 'none'))) === 'exclude' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="exclude_buruh_bongkar" class="ml-2 block text-sm text-gray-900">Exclude Buruh Bongkar</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="buruh_bongkar_option" id="include_buruh_bongkar" value="include" {{ old('buruh_bongkar_option', ($order->exclude_buruh_bongkar ? 'exclude' : ($order->include_buruh_bongkar ? 'include' : 'none'))) === 'include' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="include_buruh_bongkar" class="ml-2 block text-sm text-gray-900">Include Buruh Bongkar</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="buruh_bongkar_option" id="none_buruh_bongkar" value="none" {{ old('buruh_bongkar_option', ($order->exclude_buruh_bongkar ? 'exclude' : ($order->include_buruh_bongkar ? 'include' : 'none'))) === 'none' ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <label for="none_buruh_bongkar" class="ml-2 block text-sm text-gray-900">Tidak ada</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Masukkan catatan tambahan (opsional)">{{ old('catatan', $order->catatan) }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to create searchable dropdown
    function createSearchableDropdown(config) {
        const selectElement = document.getElementById(config.selectId);
        const searchInput = document.getElementById(config.searchId);
        const dropdownOptions = document.getElementById(config.dropdownId);
        const originalOptions = Array.from(selectElement.options);

        // Get current selected value for editing
        const currentValue = selectElement.value;
        const currentText = selectElement.options[selectElement.selectedIndex]?.text || '';

        // Set initial search input value
        if (currentValue && currentText !== 'Select an option') {
            searchInput.value = currentText;
        }

        // Initially populate dropdown options
        populateDropdown(originalOptions);

        // Show dropdown when search input is focused or clicked
        searchInput.addEventListener('focus', function() {
            dropdownOptions.classList.remove('hidden');
        });

        searchInput.addEventListener('click', function() {
            dropdownOptions.classList.remove('hidden');
        });

        // Filter options based on search
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filteredOptions = originalOptions.filter(option => {
                if (option.value === '') return true;
                return option.text.toLowerCase().includes(searchTerm);
            });
            populateDropdown(filteredOptions);
            dropdownOptions.classList.remove('hidden');
        });

        // Populate dropdown with options
        function populateDropdown(options) {
            dropdownOptions.innerHTML = '';
            options.forEach(option => {
                const div = document.createElement('div');
                div.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                div.textContent = option.text;
                div.setAttribute('data-value', option.value);

                div.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    const text = this.textContent;

                    // Set the select value
                    selectElement.value = value;

                    // Update search input
                    if (value === '') {
                        searchInput.value = '';
                        searchInput.placeholder = 'Search...';
                    } else {
                        searchInput.value = text;
                    }

                    // Hide dropdown
                    dropdownOptions.classList.add('hidden');

                    // Trigger change event
                    selectElement.dispatchEvent(new Event('change'));
                });

                dropdownOptions.appendChild(div);
            });
        }

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.' + config.containerClass)) {
                dropdownOptions.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdownOptions.classList.add('hidden');
            }
        });
    }

    // Initialize Tujuan Kirim dropdown
    createSearchableDropdown({
        selectId: 'tujuan_kirim_id',
        searchId: 'search_tujuan_kirim',
        dropdownId: 'dropdown_options',
        containerClass: 'dropdown-container'
    });

    // Initialize Tujuan Ambil dropdown
    createSearchableDropdown({
        selectId: 'tujuan_ambil_id',
        searchId: 'search_tujuan_ambil',
        dropdownId: 'dropdown_options_ambil',
        containerClass: 'dropdown-container-ambil'
    });

    // Initialize Term dropdown
    createSearchableDropdown({
        selectId: 'term_id',
        searchId: 'search_term',
        dropdownId: 'dropdown_options_term',
        containerClass: 'dropdown-container-term'
    });

    // Auto-refresh when new data is added
    window.addEventListener('message', function(event) {
        if (event.data.type === 'tujuan-kirim-added' || event.data.type === 'tujuan-ambil-added' || event.data.type === 'term-added') {
            location.reload();
        }
    });
});
</script>
@endpush
