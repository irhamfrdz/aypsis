@extends('layouts.app')

@section('title', 'Tambah Vendor Amprahan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        @if(!isset($isPopup) || !$isPopup)
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Vendor Amprahan</h1>
            <a href="{{ route('master.vendor-amprahan.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
        @else
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Vendor Amprahan</h1>
        </div>
        @endif

        <form action="{{ route('master.vendor-amprahan.store') }}" method="POST">
            @csrf
            @if(isset($isPopup) && $isPopup)
                <input type="hidden" name="popup" value="1">
            @endif

            <div class="space-y-4">
                <div>
                    <label for="nama_toko" class="block text-sm font-medium text-gray-700 mb-1">Nama Toko <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_toko" id="nama_toko" value="{{ old('nama_toko', $search ?? '') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_toko') border-red-500 @enderror">
                    @error('nama_toko')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="alamat_toko" class="block text-sm font-medium text-gray-700 mb-1">Alamat Toko</label>
                    <textarea name="alamat_toko" id="alamat_toko" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('alamat_toko') border-red-500 @enderror">{{ old('alamat_toko') }}</textarea>
                    @error('alamat_toko')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                    Simpan Vendor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
