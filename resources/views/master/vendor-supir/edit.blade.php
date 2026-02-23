@extends('layouts.app')

@section('title', 'Edit Vendor Supir')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Vendor Supir</h1>
            <p class="text-gray-600 mt-1">Perbarui data vendor supir</p>
        </div>

        <form action="{{ route('master.vendor-supir.update', $vendorSupir) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label for="nama_vendor" class="block text-sm font-medium text-gray-700 mb-1">Nama Vendor <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_vendor" id="nama_vendor" value="{{ old('nama_vendor', $vendorSupir->nama_vendor) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('nama_vendor') border-red-500 @enderror">
                    @error('nama_vendor')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $vendorSupir->no_hp) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">{{ old('alamat', $vendorSupir->alamat) }}</textarea>
                </div>

                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">{{ old('keterangan', $vendorSupir->keterangan) }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('master.vendor-supir.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
