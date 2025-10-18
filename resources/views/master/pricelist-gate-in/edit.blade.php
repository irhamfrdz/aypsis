@extends('layouts.app')

@section('title', 'Edit Pricelist Gate In')
@section('page_title', 'Edit Pricelist Gate In')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Edit Pricelist Gate In</h1>
                        <p class="text-blue-100 text-sm">Edit pricelist gate in: {{ $pricelistGateIn->kode }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <form action="{{ route('master.pricelist-gate-in.update', $pricelistGateIn) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kode -->
                    <div>
                        <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kode" id="kode" value="{{ old('kode', $pricelistGateIn->kode) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kode') border-red-500 @enderror"
                               placeholder="Masukkan kode pricelist" required>
                        @error('kode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                            <option value="">Pilih Status</option>
                            <option value="aktif" {{ old('status', $pricelistGateIn->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $pricelistGateIn->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tarif -->
                    <div>
                        <label for="tarif" class="block text-sm font-medium text-gray-700 mb-2">
                            Tarif <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="tarif" id="tarif" value="{{ old('tarif', $pricelistGateIn->tarif) }}" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tarif') border-red-500 @enderror"
                               placeholder="0.00" required>
                        @error('tarif')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="mt-6">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea name="keterangan" id="keterangan" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-500 @enderror"
                              placeholder="Masukkan keterangan pricelist">{{ old('keterangan', $pricelistGateIn->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan -->
                <div class="mt-6">
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea name="catatan" id="catatan" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('catatan') border-red-500 @enderror"
                              placeholder="Masukkan catatan tambahan">{{ old('catatan', $pricelistGateIn->catatan) }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('master.pricelist-gate-in.show', $pricelistGateIn) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
