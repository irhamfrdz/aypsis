@extends('layouts.app')

@section('title', 'Tambah Pricelist Labuh Tambat')
@section('page_title', 'Tambah Pricelist Labuh Tambat')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('master.master-pricelist-labuh-tambat.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Tambah Pricelist Labuh Tambat</h1>
                    <p class="text-gray-600">Buat data tarif labuh tambat baru</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('master.master-pricelist-labuh-tambat.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Agen --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Agen <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="nama_agen" 
                           value="{{ old('nama_agen') }}" 
                           class="w-full px-3 py-2 border @error('nama_agen') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                    @error('nama_agen')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Kapal --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kapal <span class="text-red-500">*</span></label>
                    <div class="relative" id="kapal-combobox">
                        <input type="text" 
                               id="kapal-search" 
                               value="{{ old('nama_kapal') }}"
                               placeholder="Cari dan pilih kapal..." 
                               class="w-full px-3 py-2 border @error('nama_kapal') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               autocomplete="off">
                        
                        {{-- Hidden input for the actual value to be submitted --}}
                        <input type="hidden" name="nama_kapal" id="kapal-value" value="{{ old('nama_kapal') }}">
                        
                        {{-- Results List --}}
                        <div id="kapal-results" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            @foreach($kapals as $kapal)
                                <div class="kapal-item px-4 py-2 cursor-pointer hover:bg-blue-600 hover:text-white" 
                                     data-value="{{ $kapal->nama_kapal }}" 
                                     data-text="{{ $kapal->nama_kapal }} ({{ $kapal->nickname ?? '-' }})">
                                    {{ $kapal->nama_kapal }} ({{ $kapal->nickname ?? '-' }})
                                </div>
                            @endforeach
                            <div id="no-kapal-found" class="hidden px-4 py-2 text-gray-500">Tidak ada kapal ditemukan</div>
                        </div>
                    </div>
                    @error('nama_kapal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const searchInput = document.getElementById('kapal-search');
                        const hiddenInput = document.getElementById('kapal-value');
                        const resultsList = document.getElementById('kapal-results');
                        const items = resultsList.querySelectorAll('.kapal-item');
                        const noFound = document.getElementById('no-kapal-found');

                        // Show results when focused
                        searchInput.addEventListener('focus', function() {
                            resultsList.classList.remove('hidden');
                        });

                        // Filter results as user types
                        searchInput.addEventListener('input', function() {
                            const query = this.value.toLowerCase();
                            let count = 0;
                            
                            items.forEach(item => {
                                const text = item.getAttribute('data-text').toLowerCase();
                                if (text.includes(query)) {
                                    item.classList.remove('hidden');
                                    count++;
                                } else {
                                    item.classList.add('hidden');
                                }
                            });

                            if (count === 0) {
                                noFound.classList.remove('hidden');
                            } else {
                                noFound.classList.add('hidden');
                            }
                            
                            resultsList.classList.remove('hidden');
                            // Clear hidden value if they are typing (must re-select)
                            // hiddenInput.value = ''; 
                        });

                        // Select item
                        items.forEach(item => {
                            item.addEventListener('click', function() {
                                const val = this.getAttribute('data-value');
                                const text = this.getAttribute('data-text');
                                
                                searchInput.value = val;
                                hiddenInput.value = val;
                                resultsList.classList.add('hidden');
                            });
                        });

                        // Hide results when clicking outside
                        document.addEventListener('click', function(e) {
                            if (!document.getElementById('kapal-combobox').contains(e.target)) {
                                resultsList.classList.add('hidden');
                            }
                        });
                    });
                </script>

                {{-- Biaya --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                        <input type="number" 
                               step="0.01"
                               name="harga" 
                               value="{{ old('harga') }}" 
                               class="w-full pl-10 pr-3 py-2 border @error('harga') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>
                    @error('harga')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lokasi --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                    <select name="lokasi" 
                            class="w-full px-3 py-2 border @error('lokasi') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        <option value="" disabled {{ old('lokasi') ? '' : 'selected' }}>Pilih Lokasi</option>
                        <option value="Jakarta" {{ old('lokasi') == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                        <option value="Batam" {{ old('lokasi') == 'Batam' ? 'selected' : '' }}>Batam</option>
                        <option value="Pinang" {{ old('lokasi') == 'Pinang' ? 'selected' : '' }}>Pinang</option>
                    </select>
                    @error('lokasi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Aktif</span>
                        </label>
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="md:col-span-2 mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" 
                               rows="3" 
                               class="w-full px-3 py-2 border @error('keterangan') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <a href="{{ route('master.master-pricelist-labuh-tambat.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition duration-200">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
