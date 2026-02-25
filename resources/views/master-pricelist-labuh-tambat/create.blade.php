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
                {{-- Nama Tarif --}}
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
