@extends('layouts.app')

@section('title', 'Tambah Stock Ban')
@section('page_title', 'Tambah Stock Ban')

@section('content')
<h2 class="text-xl font-bold text-gray-800 mb-4">Formulir Stock Ban Baru</h2>

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('stock-ban.store') }}" method="POST">
        @csrf

        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
        @endphp

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Stock Ban</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Kode Ban -->
                <div>
                    <label for="kode_ban" class="block text-sm font-medium text-gray-700">
                        Kode Ban <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="kode_ban" 
                           id="kode_ban" 
                           value="{{ old('kode_ban') }}" 
                           class="{{ $inputClasses }}" 
                           required 
                           maxlength="50" 
                           placeholder="Contoh: BAN-001">
                    @error('kode_ban')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ukuran Ban -->
                <div>
                    <label for="ukuran_ban" class="block text-sm font-medium text-gray-700">
                        Ukuran Ban <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="ukuran_ban" 
                           id="ukuran_ban" 
                           value="{{ old('ukuran_ban') }}" 
                           class="{{ $inputClasses }}" 
                           required 
                           maxlength="50" 
                           placeholder="Contoh: 11.00R20, 12.00R20">
                    @error('ukuran_ban')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Merek -->
                <div>
                    <label for="merek" class="block text-sm font-medium text-gray-700">
                        Merek <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="merek" 
                           id="merek" 
                           value="{{ old('merek') }}" 
                           class="{{ $inputClasses }}" 
                           required 
                           maxlength="100" 
                           placeholder="Contoh: Bridgestone, Dunlop, GT Radial">
                    @error('merek')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah -->
                <div>
                    <label for="jumlah" class="block text-sm font-medium text-gray-700">
                        Jumlah (pcs) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="jumlah" 
                           id="jumlah" 
                           value="{{ old('jumlah', 0) }}" 
                           class="{{ $inputClasses }}" 
                           required 
                           min="0" 
                           placeholder="Jumlah dalam pcs">
                    @error('jumlah')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kondisi -->
                <div>
                    <label for="kondisi" class="block text-sm font-medium text-gray-700">
                        Kondisi <span class="text-red-500">*</span>
                    </label>
                    <select name="kondisi" id="kondisi" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="Baru" {{ old('kondisi') == 'Baru' ? 'selected' : '' }}>Baru</option>
                        <option value="Bekas" {{ old('kondisi') == 'Bekas' ? 'selected' : '' }}>Bekas</option>
                        <option value="Vulkanisir" {{ old('kondisi') == 'Vulkanisir' ? 'selected' : '' }}>Vulkanisir</option>
                    </select>
                    @error('kondisi')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lokasi -->
                <div>
                    <label for="lokasi" class="block text-sm font-medium text-gray-700">
                        Lokasi <span class="text-red-500">*</span>
                    </label>
                    <select name="lokasi" id="lokasi" class="{{ $inputClasses }}" required>
                        <option value="">-- Pilih Lokasi --</option>
                        <option value="BTM" {{ old('lokasi') == 'BTM' ? 'selected' : '' }}>BTM</option>
                        <option value="JKT" {{ old('lokasi') == 'JKT' ? 'selected' : '' }}>JKT</option>
                        <option value="PNG" {{ old('lokasi') == 'PNG' ? 'selected' : '' }}>PNG</option>
                    </select>
                    @error('lokasi')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Satuan -->
                <div>
                    <label for="harga_satuan" class="block text-sm font-medium text-gray-700">
                        Harga Satuan <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" 
                               name="harga_satuan" 
                               id="harga_satuan" 
                               value="{{ old('harga_satuan', 0) }}" 
                               class="block w-full pl-12 rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5" 
                               required 
                               min="0" 
                               step="1000"
                               placeholder="0">
                    </div>
                    @error('harga_satuan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Masuk -->
                <div>
                    <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700">
                        Tanggal Masuk
                    </label>
                    <input type="date" 
                           name="tanggal_masuk" 
                           id="tanggal_masuk" 
                           value="{{ old('tanggal_masuk', date('Y-m-d')) }}" 
                           class="{{ $inputClasses }}">
                    @error('tanggal_masuk')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Supplier -->
                <div>
                    <label for="supplier" class="block text-sm font-medium text-gray-700">
                        Supplier
                    </label>
                    <input type="text" 
                           name="supplier" 
                           id="supplier" 
                           value="{{ old('supplier') }}" 
                           class="{{ $inputClasses }}" 
                           maxlength="200" 
                           placeholder="Nama supplier">
                    @error('supplier')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">
                        Keterangan
                    </label>
                    <textarea name="keterangan" 
                              id="keterangan" 
                              rows="3" 
                              class="{{ $inputClasses }}" 
                              placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <!-- Tombol Aksi -->
        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
            <a href="{{ route('stock-ban.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Batal
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
