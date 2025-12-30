@extends('layouts.app')

@section('title', 'Edit Pengirim')
@section('page_title', 'Edit Pengirim')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Pengirim</h1>
                    <p class="mt-1 text-sm text-gray-600">Ubah informasi pengirim</p>
                </div>
                <a href="{{ route('pengirim.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('pengirim.update', $pengirim) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Kode Field -->
                <div>
                    <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode" id="kode" value="{{ old('kode', $pengirim->kode) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('kode') border-red-500 @enderror"
                           placeholder="Masukkan kode pengirim">
                    @error('kode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Pengirim Field -->
                <div>
                    <label for="nama_pengirim" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Pengirim <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_pengirim" id="nama_pengirim" value="{{ old('nama_pengirim', $pengirim->nama_pengirim) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('nama_pengirim') border-red-500 @enderror"
                           placeholder="Masukkan nama pengirim">
                    @error('nama_pengirim')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Field -->
                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea name="catatan" id="catatan" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('catatan') border-red-500 @enderror"
                              placeholder="Masukkan catatan (opsional)">{{ old('catatan', $pengirim->catatan) }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Krani 20ft Field -->
                <div>
                    <label for="harga_krani_20ft" class="block text-sm font-medium text-gray-700 mb-2">
                        Harga Krani 20ft
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                            Rp
                        </span>
                        <input type="number" name="harga_krani_20ft" id="harga_krani_20ft" value="{{ old('harga_krani_20ft', $pengirim->harga_krani_20ft ?? 0) }}" min="0" step="1000"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('harga_krani_20ft') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('harga_krani_20ft')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Krani 40ft Field -->
                <div>
                    <label for="harga_krani_40ft" class="block text-sm font-medium text-gray-700 mb-2">
                        Harga Krani 40ft
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                            Rp
                        </span>
                        <input type="number" name="harga_krani_40ft" id="harga_krani_40ft" value="{{ old('harga_krani_40ft', $pengirim->harga_krani_40ft ?? 0) }}" min="0" step="1000"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('harga_krani_40ft') border-red-500 @enderror"
                               placeholder="0">
                    </div>
                    @error('harga_krani_40ft')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Field -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="radio" name="status" id="status_active" value="active" {{ old('status', $pengirim->status) === 'active' ? 'checked' : '' }} required
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <label for="status_active" class="ml-2 block text-sm text-gray-900">
                                Aktif
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="status" id="status_inactive" value="inactive" {{ old('status', $pengirim->status) === 'inactive' ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <label for="status_inactive" class="ml-2 block text-sm text-gray-900">
                                Tidak Aktif
                            </label>
                        </div>
                    </div>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('pengirim.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Pengirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
