@extends('layouts.app')

@section('title', 'Edit Pricelist Meratus')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Pricelist Meratus</h1>
                    <p class="text-gray-600 mt-1">Perbarui data tarif Meratus</p>
                </div>
                <a href="{{ route('master.pricelist-meratus.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>

            <form action="{{ route('master.pricelist-meratus.update', $pricelistMeratus->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label for="jenis_biaya" class="block text-sm font-medium text-gray-700 mb-1">Jenis Biaya <span class="text-red-500">*</span></label>
                        <input type="text" name="jenis_biaya" id="jenis_biaya" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jenis_biaya') border-red-500 @enderror" value="{{ old('jenis_biaya', $pricelistMeratus->jenis_biaya) }}" required placeholder="Contoh: THC, Depo, dll">
                        @error('jenis_biaya')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <select name="lokasi" id="lokasi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('lokasi') border-red-500 @enderror">
                            <option value="">-- Pilih Lokasi --</option>
                            <option value="Jakarta" {{ old('lokasi', $pricelistMeratus->lokasi) == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                            <option value="Batam" {{ old('lokasi', $pricelistMeratus->lokasi) == 'Batam' ? 'selected' : '' }}>Batam</option>
                            <option value="Pinang" {{ old('lokasi', $pricelistMeratus->lokasi) == 'Pinang' ? 'selected' : '' }}>Pinang</option>
                        </select>
                        @error('lokasi')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="size" class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                        <input type="text" name="size" id="size" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('size') border-red-500 @enderror" value="{{ old('size', $pricelistMeratus->size) }}" placeholder="Contoh: 20ft, 40ft, dll">
                        @error('size')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="harga" class="block text-sm font-medium text-gray-700 mb-1">Harga <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="harga" id="harga" step="0.01" min="0" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('harga') border-red-500 @enderror" value="{{ old('harga', $pricelistMeratus->harga) }}" required>
                        </div>
                        @error('harga')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror" required>
                            <option value="Aktif" {{ old('status', $pricelistMeratus->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Non Aktif" {{ old('status', $pricelistMeratus->status) == 'Non Aktif' ? 'selected' : '' }}>Non Aktif</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm transition duration-200">
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
