@extends('layouts.app')

@section('title', 'Edit Chasis Batam - ' . $chasisBatam->kode)
@section('page_title', 'Edit Chasis Batam')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-200">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Edit Data Chasis</h2>
                <p class="text-xs text-gray-500 mt-0.5">Ubah data chasis: <strong class="text-indigo-600">{{ $chasisBatam->kode }}</strong>.</p>
            </div>
            <a href="{{ route('master.chasis-batam.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors duration-150">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 text-xs" role="alert">
                <div class="font-semibold mb-1">Ada beberapa kesalahan input:</div>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('master.chasis-batam.update', $chasisBatam) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Kode Chasis -->
            <div>
                <label for="kode" class="block text-xs font-semibold text-gray-700 mb-1">
                    Kode Chasis <span class="text-red-500">*</span>
                </label>
                <input type="text" name="kode" id="kode" value="{{ old('kode', $chasisBatam->kode) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tipe Chasis -->
                <div>
                    <label for="tipe" class="block text-xs font-semibold text-gray-700 mb-1">Tipe / Ukuran Chasis</label>
                    <select name="tipe" id="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Pilih Tipe</option>
                        <option value="20ft" {{ old('tipe', $chasisBatam->tipe) == '20ft' ? 'selected' : '' }}>20 feet</option>
                        <option value="40ft" {{ old('tipe', $chasisBatam->tipe) == '40ft' ? 'selected' : '' }}>40 feet</option>
                    </select>
                </div>

                <!-- Kondisi -->
                <div>
                    <label for="kondisi" class="block text-xs font-semibold text-gray-700 mb-1">
                        Kondisi <span class="text-red-500">*</span>
                    </label>
                    <select name="kondisi" id="kondisi" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="baik" {{ old('kondisi', $chasisBatam->kondisi) == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ old('kondisi', $chasisBatam->kondisi) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Lokasi -->
                <div>
                    <label for="lokasi" class="block text-xs font-semibold text-gray-700 mb-1">
                        Lokasi <span class="text-red-500">*</span>
                    </label>
                    <select name="lokasi" id="lokasi" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="sm" {{ old('lokasi', $chasisBatam->lokasi) == 'sm' ? 'selected' : '' }}>SM</option>
                        <option value="relasi" {{ old('lokasi', $chasisBatam->lokasi) == 'relasi' ? 'selected' : '' }}>Relasi</option>
                    </select>
                </div>

                <!-- Tanggal Terakhir Pakai -->
                <div>
                    <label for="tanggal_terakhir_pakai" class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Terakhir Pakai</label>
                    <input type="date" name="tanggal_terakhir_pakai" id="tanggal_terakhir_pakai" value="{{ old('tanggal_terakhir_pakai', $chasisBatam->tanggal_terakhir_pakai ? $chasisBatam->tanggal_terakhir_pakai->format('Y-m-d') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label for="catatan" class="block text-xs font-semibold text-gray-700 mb-1">Catatan Tambahan</label>
                <textarea name="catatan" id="catatan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('catatan', $chasisBatam->catatan) }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-gray-100 gap-2">
                <a href="{{ route('master.chasis-batam.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs font-semibold transition-colors duration-150">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition-colors duration-150 shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection
