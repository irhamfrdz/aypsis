@extends('layouts.app')

@section('title', 'Tambah Vendor')
@section('page_title', 'Tambah Vendor')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Tambah Vendor</h1>
                            <p class="text-blue-100 text-sm">Buat data vendor baru</p>
                        </div>
                    </div>
                    <a href="{{ route('master.vendors.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6">
                <form action="{{ route('master.vendors.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Nama Vendor -->
                    <div class="space-y-2">
                        <label for="nama_vendor" class="block text-sm font-medium text-gray-700">Nama Vendor <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_vendor" id="nama_vendor" value="{{ old('nama_vendor') }}" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 @error('nama_vendor') border-red-300 @enderror">
                        @error('nama_vendor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipe Hitung -->
                    <div class="space-y-2">
                        <label for="tipe_hitung" class="block text-sm font-medium text-gray-700">Tipe Hitung <span class="text-red-500">*</span></label>
                        <select name="tipe_hitung" id="tipe_hitung" required class="block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500">
                            <option value="bulanan" {{ old('tipe_hitung') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                            <option value="harian" {{ old('tipe_hitung') == 'harian' ? 'selected' : '' }}>Harian</option>
                        </select>
                        @error('tipe_hitung')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('master.vendors.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Batal</a>
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg">Simpan Vendor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
