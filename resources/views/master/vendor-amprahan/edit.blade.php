@extends('layouts.app')

@section('title', 'Edit Vendor Amprahan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Vendor Amprahan</h1>
            <a href="{{ route('master.vendor-amprahan.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <form action="{{ route('master.vendor-amprahan.update', $vendorAmprahan) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="nama_toko" class="block text-sm font-medium text-gray-700 mb-1">Nama Toko <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_toko" id="nama_toko" value="{{ old('nama_toko', $vendorAmprahan->nama_toko) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_toko') border-red-500 @enderror">
                    @error('nama_toko')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="alamat_toko" class="block text-sm font-medium text-gray-700 mb-1">Alamat Toko</label>
                    <textarea name="alamat_toko" id="alamat_toko" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('alamat_toko') border-red-500 @enderror">{{ old('alamat_toko', $vendorAmprahan->alamat_toko) }}</textarea>
                    @error('alamat_toko')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                    Update Vendor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
