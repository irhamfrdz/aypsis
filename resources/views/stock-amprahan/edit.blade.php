@extends('layouts.app')

@section('title', 'Edit Stock Amprahan')
@section('page_title', 'Edit Stock Amprahan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        {{-- Breadcrumb --}}
        <nav class="flex mb-6 text-sm text-gray-500">
            <a href="{{ route('stock-amprahan.index') }}" class="hover:text-indigo-600 transition-colors">Stock Amprahan</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 font-medium">Edit Data</span>
        </nav>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Ubah Informasi Stock</h2>
                
                <form action="{{ route('stock-amprahan.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        {{-- Nama Barang --}}
                        <div>
                            <label for="nama_barang" class="block text-sm font-semibold text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $item->nama_barang ?? ($item->masterNamaBarangAmprahan->nama_barang ?? '')) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" required>
                            @error('nama_barang')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Type Barang --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="master_nama_barang_amprahan_id" class="text-sm font-semibold text-gray-700">Type Barang <span class="text-red-500">*</span></label>
                                <a href="{{ route('master-nama-barang-amprahan.create') }}" id="add_type_barang_link"
                                   class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                   title="Tambah">
                                    Tambah
                                </a>
                            </div>
                            <div class="relative">
                                <div class="dropdown-container-type-barang">
                                    <input type="text" id="search_type_barang" placeholder="Search..." autocomplete="off"
                                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                    <select name="master_nama_barang_amprahan_id" id="master_nama_barang_amprahan_id" required
                                            class="hidden w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                        <option value="">Select an option</option>
                                        @foreach($masterItems as $master)
                                            <option value="{{ $master->id }}" {{ old('master_nama_barang_amprahan_id', $item->master_nama_barang_amprahan_id) == $master->id ? 'selected' : '' }}>
                                                {{ $master->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="dropdown_options_type_barang" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b max-h-60 overflow-y-auto hidden">
                                        {{-- Options will be populated by JavaScript --}}
                                    </div>
                                </div>
                            </div>
                            @error('master_nama_barang_amprahan_id')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                            {{-- Harga Satuan --}}
                            <div>
                                <label for="harga_satuan" class="block text-sm font-semibold text-gray-700 mb-1">Harga Satuan</label>
                                <input type="number" name="harga_satuan" id="harga_satuan" value="{{ old('harga_satuan', $item->harga_satuan ?? 0) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                @error('harga_satuan')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Jumlah --}}
                            <div>
                                <label for="jumlah" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="jumlah" id="jumlah" value="{{ old('jumlah', $item->jumlah) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" required>
                                @error('jumlah')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Satuan --}}
                            <div>
                                <label for="satuan" class="block text-sm font-semibold text-gray-700 mb-1">Satuan</label>
                                <input type="text" name="satuan" id="satuan" value="{{ old('satuan', $item->satuan) }}" placeholder="Contoh: rim, pack, pcs" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                                @error('satuan')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Lokasi --}}
                        <div>
                            <label for="lokasi" class="block text-sm font-semibold text-gray-700 mb-1">Lokasi Penyimpanan</label>
                            <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $item->lokasi) }}" placeholder="Contoh: Gudang A, Rak 2" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">
                            @error('lokasi')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3" placeholder="Catatan tambahan jika ada..." class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200">{{ old('keterangan', $item->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-end space-x-4">
                        <a href="{{ route('stock-amprahan.index') }}" class="px-6 py-2.5 text-sm font-semibold text-gray-700 hover:text-gray-900 transition-colors">Batal</a>
                        <button type="submit" class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md shadow-indigo-200 transition-all duration-200 focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                            Perbarui Stock
                        </button>
                    </div>
                </form>
            </div>
            
            {{-- Audit Info --}}
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 grid grid-cols-2 gap-4 text-xs text-gray-400">
                <div>
                    <span class="font-semibold block uppercase">Dibuat Oleh:</span>
                    {{ $item->createdBy->name ?? '-' }} ({{ $item->created_at->format('d/m/Y H:i') }})
                </div>
                <div>
                    <span class="font-semibold block uppercase">Diperbarui Oleh:</span>
                    {{ $item->updatedBy->name ?? '-' }} ({{ $item->updated_at->format('d/m/Y H:i') }})
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to create searchable dropdown
    function createSearchableDropdown(config) {
        const selectElement = document.getElementById(config.selectId);
        const searchInput = document.getElementById(config.searchId);
        const dropdownOptions = document.getElementById(config.dropdownId);
        let originalOptions = Array.from(selectElement.options);

        // Get selected value and set it in search input
        const selectedOption = Array.from(selectElement.options).find(opt => opt.selected);
        if (selectedOption && selectedOption.value !== '') {
            searchInput.value = selectedOption.text;
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

    // Initialize Type Barang dropdown
    createSearchableDropdown({
        selectId: 'master_nama_barang_amprahan_id',
        searchId: 'search_type_barang',
        dropdownId: 'dropdown_options_type_barang',
        containerClass: 'dropdown-container-type-barang'
    });

    // Handle Type Barang "Tambah" link to pass search parameter
    const addTypeBarangLink = document.getElementById('add_type_barang_link');
    const searchTypeBarangInput = document.getElementById('search_type_barang');
    if (addTypeBarangLink && searchTypeBarangInput) {
        addTypeBarangLink.addEventListener('click', function(e) {
            e.preventDefault();
            const searchValue = searchTypeBarangInput.value.trim();
            let url = "{{ route('master-nama-barang-amprahan.create') }}";

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
                'addTypeBarang',
                'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
            );

            // Focus on the popup window
            if (popup) {
                popup.focus();
            }
        });
    }
});
</script>
@endpush
@endsection
