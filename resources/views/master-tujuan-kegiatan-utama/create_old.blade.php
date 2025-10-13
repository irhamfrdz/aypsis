@extends('layouts.app')

@section('title','Tambah Data Transportasi')
@section('page_title', 'Tambah Data Transportasi')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Form Tambah Data Transportasi</h2>

    <form action="{{route('master.tujuan-kegiatan-utama.store')}}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Kode -->
            <div class="mb-4">
                <label for="kode" class="block text-sm font-medium text-gray-700">Kode</label>
                <input type="text" name="kode" id="kode" value="{{ old('kode') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Masukkan kode">
                @error('kode')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cabang -->
            <div class="mb-4">
                <label for="cabang" class="block text-sm font-medium text-gray-700">Cabang</label>
                <input type="text" name="cabang" id="cabang" value="{{ old('cabang') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Masukkan cabang">
                @error('cabang')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Wilayah -->
            <div class="mb-4">
                <label for="wilayah" class="block text-sm font-medium text-gray-700">Wilayah</label>
                <input type="text" name="wilayah" id="wilayah" value="{{ old('wilayah') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Masukkan wilayah">
                @error('wilayah')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dari -->
            <div class="mb-4">
                <label for="dari" class="block text-sm font-medium text-gray-700">Dari</label>
                <input type="text" name="dari" id="dari" value="{{ old('dari') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Lokasi asal">
                @error('dari')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ke -->
            <div class="mb-4">
                <label for="ke" class="block text-sm font-medium text-gray-700">Ke</label>
                <input type="text" name="ke" id="ke" value="{{ old('ke') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Lokasi tujuan">
                @error('ke')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Uang Jalan 20ft -->
            <div class="mb-4">
                <label for="uang_jalan_20ft" class="block text-sm font-medium text-gray-700">Uang Jalan 20ft</label>
                <input type="number" step="0.01" name="uang_jalan_20ft" id="uang_jalan_20ft" value="{{ old('uang_jalan_20ft') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                @error('uang_jalan_20ft')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Uang Jalan 40ft -->
            <div class="mb-4">
                <label for="uang_jalan_40ft" class="block text-sm font-medium text-gray-700">Uang Jalan 40ft</label>
                <input type="number" step="0.01" name="uang_jalan_40ft" id="uang_jalan_40ft" value="{{ old('uang_jalan_40ft') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                @error('uang_jalan_40ft')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Liter -->
            <div class="mb-4">
                <label for="liter" class="block text-sm font-medium text-gray-700">Liter</label>
                <input type="number" step="0.01" name="liter" id="liter" value="{{ old('liter') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                @error('liter')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jarak dari Penjaringan (km) -->
            <div class="mb-4">
                <label for="jarak_dari_penjaringan_km" class="block text-sm font-medium text-gray-700">Jarak dari Penjaringan (km)</label>
                <input type="number" step="0.01" name="jarak_dari_penjaringan_km" id="jarak_dari_penjaringan_km" value="{{ old('jarak_dari_penjaringan_km') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                @error('jarak_dari_penjaringan_km')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- MEL 20ft -->
            <div class="mb-4">
                <label for="mel_20ft" class="block text-sm font-medium text-gray-700">MEL 20ft</label>
                <input type="number" step="0.01" name="mel_20ft" id="mel_20ft" value="{{ old('mel_20ft') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                @error('mel_20ft')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- MEL 40ft -->
            <div class="mb-4">
                <label for="mel_40ft" class="block text-sm font-medium text-gray-700">MEL 40ft</label>
                <input type="number" step="0.01" name="mel_40ft" id="mel_40ft" value="{{ old('mel_40ft') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                @error('mel_40ft')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ongkos Truk 20ft -->
            <div class="mb-4">
                <label for="ongkos_truk_20ft" class="block text-sm font-medium text-gray-700">Ongkos Truk 20ft</label>
                <input type="number" step="0.01" name="ongkos_truk_20ft" id="ongkos_truk_20ft" value="{{ old('ongkos_truk_20ft') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                @error('ongkos_truk_20ft')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ongkos Truk 40ft -->
            <div class="mb-4">
                <label for="ongkos_truk_40ft" class="block text-sm font-medium text-gray-700">Ongkos Truk 40ft</label>
                <input type="number" step="0.01" name="ongkos_truk_40ft" id="ongkos_truk_40ft" value="{{ old('ongkos_truk_40ft') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                @error('ongkos_truk_40ft')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Antar Lokasi 20ft -->
            <div class="mb-4">
                <label for="antar_lokasi_20ft" class="block text-sm font-medium text-gray-700">Antar Lokasi 20ft</label>
                <input type="number" step="0.01" name="antar_lokasi_20ft" id="antar_lokasi_20ft" value="{{ old('antar_lokasi_20ft') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                @error('antar_lokasi_20ft')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Antar Lokasi 40ft -->
            <div class="mb-4">
                <label for="antar_lokasi_40ft" class="block text-sm font-medium text-gray-700">Antar Lokasi 40ft</label>
                <input type="number" step="0.01" name="antar_lokasi_40ft" id="antar_lokasi_40ft" value="{{ old('antar_lokasi_40ft') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                @error('antar_lokasi_40ft')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Keterangan - Full Width -->
        <div class="mb-4">
            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
            <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Masukkan keterangan">{{ old('keterangan') }}</textarea>
            @error('keterangan')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status -->
        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="aktif" value="1" {{ old('aktif', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Aktif</span>
            </label>
        </div>

        <div class="flex items-center justify-end">
            <a href="{{route('master.tujuan-kegiatan-utama.index')}}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection