@extends('layouts.app')

@section('title', 'Tambah Order')
@section('page_title', 'Tambah Order')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah Order Baru</h1>
                    <p class="mt-1 text-sm text-gray-600">Masukkan informasi order yang akan ditambahkan</p>
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
            <form action="{{ route('orders.store') }}" method="POST" class="space-y-6" autocomplete="off">
                @csrf

                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nomor Order -->
                        <div>
                            <label for="nomor_order" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Order <span class="text-red-500">*</span>
                            </label>
                            <div class="flex">
                                <input type="text" name="nomor_order" id="nomor_order" value="{{ old('nomor_order', $nextOrderNumber ?? '') }}" required readonly autocomplete="off"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 @error('nomor_order') border-red-500 @enderror"
                                       placeholder="Otomatis tergenerate">
                                <button type="button" id="generate_nomor_order"
                                        class="ml-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                    Generate
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Format: ODS + Bulan + Tahun + Running Number (contoh: ODS1025000001)</p>
                            @error('nomor_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Order -->
                        <div>
                            <label for="tanggal_order" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Order <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_order" id="tanggal_order" value="{{ old('tanggal_order') }}" required autocomplete="off"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_order') border-red-500 @enderror">
                            @error('tanggal_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No Tiket/DO -->
                        <div>
                            <label for="no_tiket_do" class="block text-sm font-medium text-gray-700 mb-2">
                                No Tiket/DO
                            </label>
                            <input type="text" name="no_tiket_do" id="no_tiket_do" value="{{ old('no_tiket_do') }}" autocomplete="off"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('no_tiket_do') border-red-500 @enderror"
                                   placeholder="Masukkan no tiket/DO">
                            @error('no_tiket_do')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Order -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status Order <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                                <option value="confirmed" {{ old('status', 'confirmed') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Order dengan status 'Confirmed' langsung aktif untuk diproses</p>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                <a href="{{ route('tujuan-kirim.create') }}" id="add_tujuan_kirim_link"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container">
                                    <input type="text" id="search_tujuan_kirim" placeholder="Search..." autocomplete="off"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="tujuan_kirim_id" id="tujuan_kirim_id" required
                                            class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 @error('tujuan_kirim_id') border-red-500 @enderror">
                                        <option value="">Select an option</option>
                                        @foreach($tujuanKirims as $tujuanKirim)
                                            <option value="{{ $tujuanKirim->id }}" {{ old('tujuan_kirim_id') == $tujuanKirim->id ? 'selected' : '' }}>
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

                        <!-- Tujuan Ambil -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="tujuan_ambil_id" class="text-sm font-medium text-gray-700">
                                    Tujuan Ambil <span class="text-red-500">*</span>
                                </label>
                                <a href="{{ route('master.tujuan-kegiatan-utama.create') }}" id="add_tujuan_ambil_link"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-ambil">
                                    <input type="text" id="search_tujuan_ambil" placeholder="Search..." autocomplete="off"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="tujuan_ambil_id" id="tujuan_ambil_id" required
                                            class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 @error('tujuan_ambil_id') border-red-500 @enderror">
                                        <option value="">Select an option</option>
                                        @foreach($tujuanKegiatanUtamas as $tujuanKegiatanUtama)
                                            <option value="{{ $tujuanKegiatanUtama->id }}" {{ old('tujuan_ambil_id') == $tujuanKegiatanUtama->id ? 'selected' : '' }}>
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
                                <a href="{{ route('term.create') }}" id="add_term_link"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-term">
                                    <input type="text" id="search_term" placeholder="Search..." autocomplete="off"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="term_id" id="term_id"
                                            class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 @error('term_id') border-red-500 @enderror">
                                        <option value="">Select an option</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                                {{ $term->nama_status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options_term" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                        <!-- Options will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                            @error('term_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pengirim -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="pengirim_id" class="text-sm font-medium text-gray-700">
                                    Pengirim
                                </label>
                                <a href="{{ route('pengirim.create') }}" id="add_pengirim_link"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-pengirim">
                                    <input type="text" id="search_pengirim" placeholder="Search..." autocomplete="off"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="pengirim_id" id="pengirim_id"
                                            class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 @error('pengirim_id') border-red-500 @enderror">
                                        <option value="">Select an option</option>
                                        @foreach($pengirims as $pengirim)
                                            <option value="{{ $pengirim->id }}" {{ old('pengirim_id') == $pengirim->id ? 'selected' : '' }}>
                                                {{ $pengirim->nama_pengirim }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options_pengirim" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                        <!-- Options will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                            @error('pengirim_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jenis Barang -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="jenis_barang_id" class="text-sm font-medium text-gray-700">
                                    Jenis Barang
                                </label>
                                <a href="{{ route('jenis-barang.create') }}" id="add_jenis_barang_link"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-jenis-barang">
                                    <input type="text" id="search_jenis_barang" placeholder="Search..." autocomplete="off"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                                    <select name="jenis_barang_id" id="jenis_barang_id"
                                            class="hidden w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 @error('jenis_barang_id') border-red-500 @enderror">
                                        <option value="">Select an option</option>
                                        @foreach($jenisBarangs as $jenisBarang)
                                            <option value="{{ $jenisBarang->id }}" {{ old('jenis_barang_id') == $jenisBarang->id ? 'selected' : '' }}>
                                                {{ $jenisBarang->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options_jenis_barang" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                        <!-- Options will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                            @error('jenis_barang_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Container Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kontainer</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Size Kontainer -->
                        <div id="size_kontainer_container">
                            <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                                Size Kontainer <span class="text-red-500">*</span>
                            </label>
                            <select name="size_kontainer" id="size_kontainer" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('size_kontainer') border-red-500 @enderror">
                                <option value="">Pilih Size Kontainer</option>
                                @foreach($ukuranKontainers as $ukuran)
                                    <option value="{{ $ukuran }}" {{ old('size_kontainer') === $ukuran ? 'selected' : '' }}>
                                        {{ $ukuran }}
                                    </option>
                                @endforeach
                            </select>
                            @error('size_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit Kontainer -->
                        <div id="unit_kontainer_container">
                            <label for="unit_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                                Unit Kontainer <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="unit_kontainer" id="unit_kontainer" value="{{ old('unit_kontainer') }}" required min="1" autocomplete="off"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('unit_kontainer') border-red-500 @enderror"
                                   placeholder="Jumlah unit kontainer">
                            <small class="text-gray-500">Akan digunakan untuk outstanding tracking</small>
                            @error('unit_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipe Kontainer -->
                        <div>
                            <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Kontainer <span class="text-red-500">*</span>
                            </label>
                            <select name="tipe_kontainer" id="tipe_kontainer" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('tipe_kontainer') border-red-500 @enderror">
                                <option value="">Pilih Tipe</option>
                                <option value="fcl" {{ old('tipe_kontainer') === 'fcl' ? 'selected' : '' }}>FCL</option>
                                <option value="lcl" {{ old('tipe_kontainer') === 'lcl' ? 'selected' : '' }}>LCL</option>
                                <option value="cargo" {{ old('tipe_kontainer') === 'cargo' ? 'selected' : '' }}>Cargo</option>
                                <option value="fcl_plus" {{ old('tipe_kontainer') === 'fcl_plus' ? 'selected' : '' }}>FCL Plus</option>
                            </select>
                            @error('tipe_kontainer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Pickup -->
                        <div>
                            <label for="tanggal_pickup" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Pickup
                            </label>
                            <input type="date" name="tanggal_pickup" id="tanggal_pickup" value="{{ old('tanggal_pickup') }}" autocomplete="off"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_pickup') border-red-500 @enderror">
                            @error('tanggal_pickup')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Satuan -->
                        <div>
                            <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">
                                Satuan
                            </label>
                            <select name="satuan" id="satuan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('satuan') border-red-500 @enderror">
                                <option value="">Pilih Satuan</option>
                                <option value="kg" {{ old('satuan') === 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                <option value="ton" {{ old('satuan') === 'ton' ? 'selected' : '' }}>Ton</option>
                                <option value="m3" {{ old('satuan') === 'm3' ? 'selected' : '' }}>Meter Kubik (m³)</option>
                                <option value="unit" {{ old('satuan') === 'unit' ? 'selected' : '' }}>Unit</option>
                                <option value="pcs" {{ old('satuan') === 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                <option value="dus" {{ old('satuan') === 'dus' ? 'selected' : '' }}>Dus</option>
                                <option value="karung" {{ old('satuan') === 'karung' ? 'selected' : '' }}>Karung</option>
                                <option value="kontainer" {{ old('satuan') === 'kontainer' ? 'selected' : '' }}>Kontainer</option>
                            </select>
                            <small class="text-gray-500">Satuan untuk pengukuran barang</small>
                            @error('satuan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea name="catatan" id="catatan" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('catatan') border-red-500 @enderror"
                              placeholder="Masukkan catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
                        Simpan Order
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
    // Handle Generate Order Number button
    const generateButton = document.getElementById('generate_nomor_order');
    const nomorOrderInput = document.getElementById('nomor_order');

    if (generateButton && nomorOrderInput) {
        generateButton.addEventListener('click', function() {
            // Disable button and show loading
            generateButton.disabled = true;
            generateButton.textContent = 'Loading...';

            // Make AJAX request to generate order number
            fetch('{{ route("orders.generate-number") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    nomorOrderInput.value = data.order_number;
                    showNotification('Nomor order berhasil digenerate: ' + data.order_number, 'success');
                } else {
                    showNotification('Gagal generate nomor order: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat generate nomor order', 'error');
            })
            .finally(() => {
                // Re-enable button
                generateButton.disabled = false;
                generateButton.textContent = 'Generate';
            });
        });
    }

    // Function to create searchable dropdown
    function createSearchableDropdown(config) {
        const selectElement = document.getElementById(config.selectId);
        const searchInput = document.getElementById(config.searchId);
        const dropdownOptions = document.getElementById(config.dropdownId);
        let originalOptions = Array.from(selectElement.options);

        // Function to refresh original options (when new items are added)
        function refreshOriginalOptions() {
            originalOptions = Array.from(selectElement.options);
        }

        // Make refreshOriginalOptions available globally for this dropdown
        if (config.selectId === 'term_id') {
            window.refreshTermOptions = refreshOriginalOptions;
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

    // Initialize Pengirim dropdown
    createSearchableDropdown({
        selectId: 'pengirim_id',
        searchId: 'search_pengirim',
        dropdownId: 'dropdown_options_pengirim',
        containerClass: 'dropdown-container-pengirim'
    });

    // Initialize Term dropdown
    createSearchableDropdown({
        selectId: 'term_id',
        searchId: 'search_term',
        dropdownId: 'dropdown_options_term',
        containerClass: 'dropdown-container-term'
    });

    // Initialize Jenis Barang dropdown
    createSearchableDropdown({
        selectId: 'jenis_barang_id',
        searchId: 'search_jenis_barang',
        dropdownId: 'dropdown_options_jenis_barang',
        containerClass: 'dropdown-container-jenis-barang'
    });

    // Handle Tipe Kontainer change - Show/Hide Size and Unit fields
    const tipeKontainerSelect = document.getElementById('tipe_kontainer');
    const sizeKontainerContainer = document.getElementById('size_kontainer_container');
    const unitKontainerContainer = document.getElementById('unit_kontainer_container');
    const sizeKontainerSelect = document.getElementById('size_kontainer');
    const unitKontainerInput = document.getElementById('unit_kontainer');

    // Handle Status Order change - Show information about status
    const statusSelect = document.getElementById('status');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            const selectedStatus = this.value;
            const helpText = this.parentElement.querySelector('.status-help-text');
            
            // Remove existing help text
            if (helpText) {
                helpText.remove();
            }
            
            // Add new help text based on selected status
            let message = '';
            let colorClass = '';
            
            switch(selectedStatus) {
                case 'confirmed':
                    message = '✅ Order akan langsung aktif dan siap diproses';
                    colorClass = 'text-green-600';
                    break;
                case 'pending':
                    message = '⏳ Order akan menunggu konfirmasi sebelum diproses';
                    colorClass = 'text-yellow-600';
                    break;
                case 'draft':
                    message = '📝 Order disimpan sebagai draft, tidak akan muncul di outstanding';
                    colorClass = 'text-gray-600';
                    break;
            }
            
            if (message) {
                const helpDiv = document.createElement('p');
                helpDiv.className = `mt-1 text-xs ${colorClass} status-help-text`;
                helpDiv.textContent = message;
                this.parentElement.appendChild(helpDiv);
            }
        });
        
        // Trigger change event on page load to show initial status
        statusSelect.dispatchEvent(new Event('change'));
    }

    function handleTipeKontainerChange() {
        const selectedTipe = tipeKontainerSelect.value;
        
        if (selectedTipe === 'cargo') {
            // Hide size and unit kontainer fields for cargo
            sizeKontainerContainer.style.display = 'none';
            unitKontainerContainer.style.display = 'none';
            
            // Remove required attributes and clear values
            sizeKontainerSelect.removeAttribute('required');
            unitKontainerInput.removeAttribute('required');
            sizeKontainerSelect.value = '';
            unitKontainerInput.value = '';
        } else {
            // Show size and unit kontainer fields for other types
            sizeKontainerContainer.style.display = 'block';
            unitKontainerContainer.style.display = 'block';
            
            // Add required attributes back
            sizeKontainerSelect.setAttribute('required', 'required');
            unitKontainerInput.setAttribute('required', 'required');
        }
    }

    // Initialize on page load
    if (tipeKontainerSelect) {
        handleTipeKontainerChange();
        tipeKontainerSelect.addEventListener('change', handleTipeKontainerChange);
    }

    // Handle Term "Tambah" link to pass search parameter
    const addTermLink = document.getElementById('add_term_link');
    const searchTermInput = document.getElementById('search_term');

    if (addTermLink && searchTermInput) {
        addTermLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchTermInput.value.trim();
            let url = "{{ route('term.create') }}";

            // Add popup parameter and nama_status if available
            const params = new URLSearchParams();
            params.append('popup', '1');

            if (searchValue) {
                params.append('search', searchValue);
            }

            url += '?' + params.toString();

            // Open as popup window with specific dimensions
            const popup = window.open(
                url,
                'addTerm',
                'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
            );

            // Focus on the popup window
            if (popup) {
                popup.focus();
            }
        });
    }

    // Handle Tujuan Kirim "Tambah" link to pass search parameter
    const addTujuanKirimLink = document.getElementById('add_tujuan_kirim_link');
    const searchTujuanKirimInput = document.getElementById('search_tujuan_kirim');
    if (addTujuanKirimLink && searchTujuanKirimInput) {
        addTujuanKirimLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchTujuanKirimInput.value.trim();
            let url = "{{ route('tujuan-kirim.create') }}";

            // Add popup parameter and nama_tujuan if available
            const params = new URLSearchParams();
            params.append('popup', '1');

            if (searchValue) {
                params.append('search', searchValue);
            }

            url += '?' + params.toString();

            // Open as popup window with specific dimensions
            const popup = window.open(
                url,
                'addTujuanKirim',
                'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
            );

            // Focus on the popup window
            if (popup) {
                popup.focus();
            }
        });
    }    // Handle Pengirim "Tambah" link
    const addPengirimLink = document.getElementById('add_pengirim_link');
    const searchPengirimInput = document.getElementById('search_pengirim');
    if (addPengirimLink && searchPengirimInput) {
        addPengirimLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchPengirimInput.value.trim();
            let url = "{{ route('pengirim.create') }}";

            // Add popup parameter and nama_pengirim if available
            const params = new URLSearchParams();
            params.append('popup', '1');

            if (searchValue) {
                params.append('search', searchValue);
            }

            url += '?' + params.toString();

            // Open as popup window with specific dimensions
            const popup = window.open(
                url,
                'addPengirim',
                'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
            );

            // Focus on the popup window
            if (popup) {
                popup.focus();
            }
        });
    }

    // Handle Jenis Barang "Tambah" link
    const addJenisBarangLink = document.getElementById('add_jenis_barang_link');
    const searchJenisBarangInput = document.getElementById('search_jenis_barang');
    if (addJenisBarangLink && searchJenisBarangInput) {
        addJenisBarangLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchJenisBarangInput.value.trim();
            let url = "{{ route('jenis-barang.create') }}";

            // Add popup parameter and nama_barang if available
            const params = new URLSearchParams();
            params.append('popup', '1');

            if (searchValue) {
                params.append('search', searchValue);
            }

            url += '?' + params.toString();

            // Open as popup window with specific dimensions
            const popup = window.open(
                url,
                'addJenisBarang',
                'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
            );

            // Focus on the popup window
            if (popup) {
                popup.focus();
            }
        });
    }    // Auto-refresh when new items are added
    window.addEventListener('message', function(event) {
        console.log('Message received:', event.data); // Debug log

        if (event.data.type === 'tujuan-kirim-added') {
            // Handle Tujuan Kirim added
            const tujuanKirimSelect = document.getElementById('tujuan_kirim_id');
            const searchTujuanKirimInput = document.getElementById('search_tujuan_kirim');
            const dropdownOptionsTujuanKirim = document.getElementById('dropdown_options');

            if (tujuanKirimSelect && event.data.data) {
                // Add new option to select
                const newOption = document.createElement('option');
                newOption.value = event.data.data.id;
                newOption.textContent = event.data.data.nama_tujuan;
                tujuanKirimSelect.appendChild(newOption);

                // Select the new option
                tujuanKirimSelect.value = event.data.data.id;

                // Update search input to show selected value
                if (searchTujuanKirimInput) {
                    searchTujuanKirimInput.value = event.data.data.nama_tujuan;
                }

                // Update the dropdown options in the searchable dropdown
                if (dropdownOptionsTujuanKirim) {
                    const newOptionDiv = document.createElement('div');
                    newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                    newOptionDiv.textContent = event.data.data.nama_tujuan;
                    newOptionDiv.setAttribute('data-value', event.data.data.id);

                    newOptionDiv.addEventListener('click', function() {
                        const value = this.getAttribute('data-value');
                        const text = this.textContent;
                        tujuanKirimSelect.value = value;
                        searchTujuanKirimInput.value = text;
                        dropdownOptionsTujuanKirim.classList.add('hidden');
                        tujuanKirimSelect.dispatchEvent(new Event('change'));
                    });

                    if (dropdownOptionsTujuanKirim.children.length > 1) {
                        dropdownOptionsTujuanKirim.insertBefore(newOptionDiv, dropdownOptionsTujuanKirim.children[1]);
                    } else {
                        dropdownOptionsTujuanKirim.appendChild(newOptionDiv);
                    }
                }

                // Hide dropdown
                if (dropdownOptionsTujuanKirim) {
                    dropdownOptionsTujuanKirim.classList.add('hidden');
                }

                // Trigger change event
                tujuanKirimSelect.dispatchEvent(new Event('change'));

                // Show success message
                showNotification('Tujuan Kirim "' + event.data.data.nama_tujuan + '" berhasil ditambahkan dan dipilih!', 'success');
            }
        } else if (event.data.type === 'pengirim-added') {
            // Handle Pengirim added
            const pengirimSelect = document.getElementById('pengirim_id');
            const searchPengirimInput = document.getElementById('search_pengirim');
            const dropdownOptionsPengirim = document.getElementById('dropdown_options_pengirim');

            if (pengirimSelect && event.data.data) {
                // Add new option to select
                const newOption = document.createElement('option');
                newOption.value = event.data.data.id;
                newOption.textContent = event.data.data.nama_pengirim;
                pengirimSelect.appendChild(newOption);

                // Select the new option
                pengirimSelect.value = event.data.data.id;

                // Update search input to show selected value
                if (searchPengirimInput) {
                    searchPengirimInput.value = event.data.data.nama_pengirim;
                }

                // Update the dropdown options in the searchable dropdown
                if (dropdownOptionsPengirim) {
                    const newOptionDiv = document.createElement('div');
                    newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                    newOptionDiv.textContent = event.data.data.nama_pengirim;
                    newOptionDiv.setAttribute('data-value', event.data.data.id);

                    newOptionDiv.addEventListener('click', function() {
                        const value = this.getAttribute('data-value');
                        const text = this.textContent;
                        pengirimSelect.value = value;
                        searchPengirimInput.value = text;
                        dropdownOptionsPengirim.classList.add('hidden');
                        pengirimSelect.dispatchEvent(new Event('change'));
                    });

                    if (dropdownOptionsPengirim.children.length > 1) {
                        dropdownOptionsPengirim.insertBefore(newOptionDiv, dropdownOptionsPengirim.children[1]);
                    } else {
                        dropdownOptionsPengirim.appendChild(newOptionDiv);
                    }
                }

                // Hide dropdown
                if (dropdownOptionsPengirim) {
                    dropdownOptionsPengirim.classList.add('hidden');
                }

                // Trigger change event
                pengirimSelect.dispatchEvent(new Event('change'));

                // Show success message
                showNotification('Pengirim "' + event.data.data.nama_pengirim + '" berhasil ditambahkan dan dipilih!', 'success');
            }
        } else if (event.data.type === 'jenis-barang-added') {
            // Handle Jenis Barang added
            const jenisBarangSelect = document.getElementById('jenis_barang_id');
            const searchJenisBarangInput = document.getElementById('search_jenis_barang');
            const dropdownOptionsJenisBarang = document.getElementById('dropdown_options_jenis_barang');

            if (jenisBarangSelect && event.data.data) {
                // Add new option to select
                const newOption = document.createElement('option');
                newOption.value = event.data.data.id;
                newOption.textContent = event.data.data.nama_barang;
                jenisBarangSelect.appendChild(newOption);

                // Select the new option
                jenisBarangSelect.value = event.data.data.id;

                // Update search input to show selected value
                if (searchJenisBarangInput) {
                    searchJenisBarangInput.value = event.data.data.nama_barang;
                }

                // Update the dropdown options in the searchable dropdown
                if (dropdownOptionsJenisBarang) {
                    const newOptionDiv = document.createElement('div');
                    newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                    newOptionDiv.textContent = event.data.data.nama_barang;
                    newOptionDiv.setAttribute('data-value', event.data.data.id);

                    newOptionDiv.addEventListener('click', function() {
                        const value = this.getAttribute('data-value');
                        const text = this.textContent;
                        jenisBarangSelect.value = value;
                        searchJenisBarangInput.value = text;
                        dropdownOptionsJenisBarang.classList.add('hidden');
                        jenisBarangSelect.dispatchEvent(new Event('change'));
                    });

                    if (dropdownOptionsJenisBarang.children.length > 1) {
                        dropdownOptionsJenisBarang.insertBefore(newOptionDiv, dropdownOptionsJenisBarang.children[1]);
                    } else {
                        dropdownOptionsJenisBarang.appendChild(newOptionDiv);
                    }
                }

                // Hide dropdown
                if (dropdownOptionsJenisBarang) {
                    dropdownOptionsJenisBarang.classList.add('hidden');
                }

                // Trigger change event
                jenisBarangSelect.dispatchEvent(new Event('change'));

                // Show success message
                showNotification('Jenis Barang "' + event.data.data.nama_barang + '" berhasil ditambahkan dan dipilih!', 'success');
            }
        } else if (event.data.type === 'term-added') {
            console.log('Processing term-added message...'); // Debug log
            // Handle Term added
            const termSelect = document.getElementById('term_id');
            const searchTermInput = document.getElementById('search_term');
            const dropdownOptionsElement = document.getElementById('dropdown_options_term');

            if (termSelect && event.data.data) {
                // Add new option to select
                const newOption = document.createElement('option');
                newOption.value = event.data.data.id;
                newOption.textContent = event.data.data.nama_status;
                termSelect.appendChild(newOption);

                // Select the new option
                termSelect.value = event.data.data.id;

                // Update search input to show selected value
                if (searchTermInput) {
                    searchTermInput.value = event.data.data.nama_status;
                }

                // Update the dropdown options in the searchable dropdown
                if (dropdownOptionsElement) {
                    // Create new option div for the dropdown
                    const newOptionDiv = document.createElement('div');
                    newOptionDiv.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                    newOptionDiv.textContent = event.data.data.nama_status;
                    newOptionDiv.setAttribute('data-value', event.data.data.id);

                    // Add click handler for the new option
                    newOptionDiv.addEventListener('click', function() {
                        const value = this.getAttribute('data-value');
                        const text = this.textContent;
                        termSelect.value = value;
                        searchTermInput.value = text;
                        dropdownOptionsElement.classList.add('hidden');
                        termSelect.dispatchEvent(new Event('change'));
                    });

                    // Insert the new option (skip the first empty option)
                    if (dropdownOptionsElement.children.length > 1) {
                        dropdownOptionsElement.insertBefore(newOptionDiv, dropdownOptionsElement.children[1]);
                    } else {
                        dropdownOptionsElement.appendChild(newOptionDiv);
                    }
                }

                // Refresh the original options for the searchable dropdown
                if (window.refreshTermOptions) {
                    window.refreshTermOptions();
                }

                // Hide dropdown
                if (dropdownOptionsElement) {
                    dropdownOptionsElement.classList.add('hidden');
                }

                // Trigger change event
                termSelect.dispatchEvent(new Event('change'));

                // Show success message
                showNotification('Term "' + event.data.data.nama_status + '" berhasil ditambahkan dan dipilih!', 'success');

                console.log('Term successfully added and selected'); // Debug log
            }
        }
    });

    // Function to show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Handle form submission - validate based on tipe kontainer
    const orderForm = document.querySelector('form[action*="orders.store"]');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            const tipeKontainer = document.getElementById('tipe_kontainer').value;
            
            if (tipeKontainer === 'cargo') {
                // For cargo, we don't need size_kontainer and unit_kontainer
                // Set them to empty or remove them before submission
                const sizeKontainerSelect = document.getElementById('size_kontainer');
                const unitKontainerInput = document.getElementById('unit_kontainer');
                
                if (sizeKontainerSelect) {
                    sizeKontainerSelect.value = '';
                    sizeKontainerSelect.removeAttribute('required');
                    sizeKontainerSelect.removeAttribute('name'); // Don't send this field
                }
                
                if (unitKontainerInput) {
                    unitKontainerInput.value = '';
                    unitKontainerInput.removeAttribute('required');
                    unitKontainerInput.removeAttribute('name'); // Don't send this field
                }
                
                console.log('Cargo type selected - size and unit fields removed from submission');
            } else {
                // For other types, ensure the fields have their names back
                const sizeKontainerSelect = document.getElementById('size_kontainer');
                const unitKontainerInput = document.getElementById('unit_kontainer');
                
                if (sizeKontainerSelect && !sizeKontainerSelect.getAttribute('name')) {
                    sizeKontainerSelect.setAttribute('name', 'size_kontainer');
                    sizeKontainerSelect.setAttribute('required', 'required');
                }
                
                if (unitKontainerInput && !unitKontainerInput.getAttribute('name')) {
                    unitKontainerInput.setAttribute('name', 'unit_kontainer');
                    unitKontainerInput.setAttribute('required', 'required');
                }
            }
        });
    }
});
</script>
@endpush
