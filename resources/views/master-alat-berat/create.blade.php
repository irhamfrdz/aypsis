@extends('layouts.app')

@section('title', 'Tambah Alat Berat')
@section('page_title', 'Tambah Alat Berat')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-800">Tambah Alat Berat Baru</h2>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('master.alat-berat.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kode Alat -->
            <div>
                <label for="kode_alat" class="block text-sm font-medium text-gray-700">Kode Alat <span class="text-red-500">*</span></label>
                <input type="text" name="kode_alat" id="kode_alat" value="{{ old('kode_alat', $nextKode) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100" readonly>
            </div>

            <!-- Nama -->
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Alat <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Excavator PC200" required>
            </div>

            <!-- Jenis -->
            <div>
                <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis</label>
                <input type="text" name="jenis" id="jenis" value="{{ old('jenis') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Excavator, Forklift">
            </div>

            <!-- Merk -->
            <div>
                <label for="merk" class="block text-sm font-medium text-gray-700">Merek</label>
                <input type="text" name="merk" id="merk" value="{{ old('merk') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Komatsu, Caterpillar">
            </div>

            <!-- Tipe -->
            <div>
                <label for="tipe" class="block text-sm font-medium text-gray-700">Tipe/Model</label>
                <input type="text" name="tipe" id="tipe" value="{{ old('tipe') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: PC200-8">
            </div>

            <!-- Nomor Seri -->
            <div>
                <label for="nomor_seri" class="block text-sm font-medium text-gray-700">Nomor Seri / Rangka</label>
                <input type="text" name="nomor_seri" id="nomor_seri" value="{{ old('nomor_seri') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <!-- Tahun Pembuatan -->
            <div>
                <label for="tahun_pembuatan" class="block text-sm font-medium text-gray-700">Tahun Pembuatan</label>
                <input type="number" name="tahun_pembuatan" id="tahun_pembuatan" value="{{ old('tahun_pembuatan') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" min="1900" max="{{ date('Y')+1 }}">
            </div>

            <!-- Lokasi -->
            <div>
                <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi Saat Ini</label>
                <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Gudang A, Site B">
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('keterangan') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('master.alat-berat.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
