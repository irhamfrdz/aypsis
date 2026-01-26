@extends('layouts.app')

@section('page_title', 'Gunakan Ban Dalam')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-wrench mr-2 text-blue-500"></i>Gunakan Ban Dalam
                </h2>
                <a href="{{ route('stock-ban.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-lg font-medium text-gray-600 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>

            {{-- Info Table --}}
            <div class="mb-6 bg-blue-50 rounded-lg p-4 border border-blue-100">
                <h3 class="font-semibold text-blue-800 mb-3">Informasi Stock</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Nama Barang</p>
                        <p class="font-medium text-gray-800">{{ $stockBanDalam->namaStockBan->nama }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Ukuran</p>
                        <p class="font-medium text-gray-800">{{ $stockBanDalam->ukuran ?? '-' }}</p>
                    </div>
                     <div>
                        <p class="text-gray-500">Lokasi</p>
                        <p class="font-medium text-gray-800">{{ $stockBanDalam->lokasi }}</p>
                    </div>
                     <div>
                        <p class="text-gray-500">Sisa Stock Saat Ini</p>
                        <p class="font-bold text-lg text-blue-600">{{ $stockBanDalam->qty }} {{ $stockBanDalam->type }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('stock-ban-dalam.store-usage', $stockBanDalam->id) }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Mobil --}}
                    <div>
                        <label for="mobil_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Pilih Kendaraan (Nomor Plat) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative" id="custom-select-container">
                            <input type="hidden" name="mobil_id" id="mobil_id_hidden" value="{{ old('mobil_id') }}" required>
                            
                            <div class="relative">
                                <input type="text" id="mobil_search" 
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition duration-150 ease-in-out cursor-pointer"
                                       placeholder="-- Pilih Kendaraan --"
                                       autocomplete="off"
                                       readonly
                                       onclick="this.removeAttribute('readonly');">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>

                            <!-- Dropdown List -->
                            <div id="mobil_dropdown" class="absolute z-10 w-full bg-white rounded-lg shadow-lg max-h-60 overflow-y-auto border border-gray-200 mt-1 hidden">
                                @foreach($mobils as $mobil)
                                    <div class="mobil-option px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm text-gray-700 transition-colors duration-150 border-b border-gray-50 last:border-0"
                                         data-id="{{ $mobil->id }}"
                                         data-text="{{ $mobil->nomor_polisi }} - {{ $mobil->merk ?? '' }}">
                                         <span class="font-bold text-gray-800">{{ $mobil->nomor_polisi }}</span>
                                         @if($mobil->merk)
                                            <span class="text-gray-500 text-xs ml-1">- {{ $mobil->merk }}</span>
                                         @endif
                                    </div>
                                @endforeach
                                <div id="no-results" class="px-4 py-3 text-sm text-gray-500 text-center hidden italic">
                                    Tidak ada data ditemukan
                                </div>
                            </div>
                        </div>
                        @error('mobil_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Qty --}}
                    <div>
                        <label for="qty" class="block text-sm font-medium text-gray-700 mb-1">
                            Jumlah Digunakan <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="qty" id="qty" 
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition duration-150 ease-in-out"
                               min="1" max="{{ $stockBanDalam->qty }}" value="{{ old('qty', 1) }}" required>
                        <p class="text-xs text-gray-500 mt-1 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i> Maksimal tersedia: <span class="font-bold ml-1">{{ $stockBanDalam->qty }}</span>
                        </p>
                        @error('qty')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                     {{-- Tanggal Keluar --}}
                    <div>
                        <label for="tanggal_keluar" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Diambil <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </div>
                            <input type="date" name="tanggal_keluar" id="tanggal_keluar" 
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 transition duration-150 ease-in-out"
                                   value="{{ old('tanggal_keluar', date('Y-m-d')) }}" required>
                        </div>
                        @error('tanggal_keluar')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                            Keterangan (Opsional)
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3" 
                                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition duration-150 ease-in-out"
                                  placeholder="Contoh: Digunakan untuk ban depan kanan">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button type="reset" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
                        Reset
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg shadow-blue-500/30">
                        <i class="fas fa-save mr-2"></i>Simpan Penggunaan
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
        const container = document.getElementById('custom-select-container');
        const searchInput = document.getElementById('mobil_search');
        const hiddenInput = document.getElementById('mobil_id_hidden');
        const dropdown = document.getElementById('mobil_dropdown');
        const options = document.querySelectorAll('.mobil-option');
        const noResults = document.getElementById('no-results');

        // Initial setup for old value
        const initialValue = hiddenInput.value;
        if (initialValue) {
            const selectedOption = document.querySelector(`.mobil-option[data-id="${initialValue}"]`);
            if (selectedOption) {
                searchInput.value = selectedOption.getAttribute('data-text');
            }
        }

        // Toggle dropdown on input focus/click
        searchInput.addEventListener('focus', showDropdown);
        searchInput.addEventListener('click', showDropdown);

        function showDropdown() {
            dropdown.classList.remove('hidden');
            // Ensure we can type immediately
            searchInput.removeAttribute('readonly');
        }

        function hideDropdown() {
            // Delay slightly to allow click event on option to register
            setTimeout(() => {
                dropdown.classList.add('hidden');
            }, 200);
        }

        // Filter options
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            let hasResults = false;

            options.forEach(option => {
                const text = option.getAttribute('data-text').toLowerCase();
                if (text.includes(searchTerm)) {
                    option.classList.remove('hidden');
                    hasResults = true;
                } else {
                    option.classList.add('hidden');
                }
            });

            if (hasResults) {
                noResults.classList.add('hidden');
            } else {
                noResults.classList.remove('hidden');
            }
            
            // If user clears input, clear hidden value
            if (searchTerm === '') {
                hiddenInput.value = '';
            }
        });

        // Handle option selection
        options.forEach(option => {
            option.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const text = this.getAttribute('data-text');

                hiddenInput.value = id;
                searchInput.value = text;
                dropdown.classList.add('hidden');
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                dropdown.classList.add('hidden');
                
                // If input has text but no valid ID selected (user typed something custom), clear it or reset to previous valid
                // Simple logic: if hidden input is empty but search is not, clear search (enforce selection)
                if (hiddenInput.value === '' && searchInput.value !== '') {
                    searchInput.value = '';
                }
            }
        });
    });
</script>
@endpush
