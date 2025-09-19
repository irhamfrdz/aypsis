@extends('layouts.app')

@section('title', 'Edit Kode Nomor')
@section('page_title', 'Edit Kode Nomor')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Kode Nomor</h1>
                    <p class="mt-1 text-sm text-gray-600">Edit data kode nomor</p>
                </div>
                <a href="{{ route('master.kode-nomor.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('master.kode-nomor.update', $kodeNomor) }}">
                @csrf
                @method('PUT')

                <!-- Kode Field -->
                <div class="mb-6">
                    <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">Kode <span class="text-red-500">*</span></label>
                    <input type="text" name="kode" id="kode" value="{{ old('kode', $kodeNomor->kode) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('kode') border-red-500 @enderror" placeholder="Masukkan kode" required>
                    @error('kode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Akun Field -->
                <div class="mb-6">
                    <label for="nomor_akun" class="block text-sm font-medium text-gray-700 mb-2">Nomor Akun</label>
                    <input type="text" name="nomor_akun" id="nomor_akun" value="{{ old('nomor_akun', $kodeNomor->nomor_akun) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('nomor_akun') border-red-500 @enderror" placeholder="Masukkan nomor akun">
                    @error('nomor_akun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Akun Field -->
                <div class="mb-6">
                    <label for="nama_akun" class="block text-sm font-medium text-gray-700 mb-2">Nama Akun</label>
                    <input type="text" name="nama_akun" id="nama_akun" value="{{ old('nama_akun', $kodeNomor->nama_akun) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('nama_akun') border-red-500 @enderror" placeholder="Masukkan nama akun">
                    @error('nama_akun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Akun Field -->
                <div class="mb-6">
                    <label for="tipe_akun" class="block text-sm font-medium text-gray-700 mb-2">Tipe Akun</label>
                    <select name="tipe_akun" id="tipe_akun" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('tipe_akun') border-red-500 @enderror">
                        <option value="">Pilih tipe akun</option>
                        <option value="Aktiva" {{ old('tipe_akun', $kodeNomor->tipe_akun) == 'Aktiva' ? 'selected' : '' }}>Aktiva</option>
                        <option value="Pasiva" {{ old('tipe_akun', $kodeNomor->tipe_akun) == 'Pasiva' ? 'selected' : '' }}>Pasiva</option>
                        <option value="Ekuitas" {{ old('tipe_akun', $kodeNomor->tipe_akun) == 'Ekuitas' ? 'selected' : '' }}>Ekuitas</option>
                        <option value="Pendapatan" {{ old('tipe_akun', $kodeNomor->tipe_akun) == 'Pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                        <option value="Beban" {{ old('tipe_akun', $kodeNomor->tipe_akun) == 'Beban' ? 'selected' : '' }}>Beban</option>
                    </select>
                    @error('tipe_akun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Saldo Field -->
                <div class="mb-6">
                    <label for="saldo" class="block text-sm font-medium text-gray-700 mb-2">Saldo</label>
                    <input type="number" name="saldo" id="saldo" value="{{ old('saldo', $kodeNomor->saldo ?? 0) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('saldo') border-red-500 @enderror" placeholder="0.00">
                    @error('saldo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Field -->
                <div class="mb-6">
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('catatan') border-red-500 @enderror" placeholder="Masukkan catatan (opsional)">{{ old('catatan', $kodeNomor->catatan) }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('master.kode-nomor.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
