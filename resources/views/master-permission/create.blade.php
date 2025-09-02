@extends('layouts.app')

@section('title', 'Tambah Izin Baru')
@section('page_title', 'Tambah Izin Baru')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Formulir Izin</h2>

    <form action="{{ route('master.permission.store') }}" method="POST">
        @csrf

        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Izin (Key) -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Izin (Key)</label>
                <input type="text" name="name" id="name" class="{{ $inputClasses }}" value="{{ old('name') }}" placeholder="contoh: master-data" required>
                <p class="mt-2 text-sm text-gray-500">Gunakan huruf kecil dan tanda hubung (-), tanpa spasi. Contoh: `master-karyawan`.</p>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <input type="text" name="description" id="description" class="{{ $inputClasses }}" value="{{ old('description') }}" placeholder="Contoh: Akses ke Master Karyawan">
                <p class="mt-2 text-sm text-gray-500">Penjelasan singkat mengenai fungsi izin ini.</p>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('master.permission.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Simpan Izin
            </button>
        </div>
    </form>
</div>
@endsection

