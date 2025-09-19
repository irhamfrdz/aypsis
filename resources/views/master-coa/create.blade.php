@extends('layouts.app')

@section('title', 'Tambah COA')
@section('page_title', 'Tambah COA')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tambah COA</h1>
                    <p class="mt-1 text-sm text-gray-600">Tambahkan Chart of Accounts (COA) baru ke dalam sistem</p>
                </div>
                <a href="{{ route('master-coa-index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('master-coa-store') }}" method="POST">
                @csrf

                <!-- Account Number Field -->
                <div class="mb-6">
                    <label for="nomor_akun" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        No. Akun <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nomor_akun"
                           id="nomor_akun"
                           value="{{ old('nomor_akun') }}"
                           class="w-full px-3 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 border-gray-300 @error('nomor_akun') border-red-300 @enderror"
                           placeholder="Masukkan nomor akun (contoh: 1001, 2001)"
                           maxlength="20"
                           required>
                    @error('nomor_akun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Nomor akun harus unik dan maksimal 20 karakter</p>
                </div>

                <!-- Kode Nomor Field -->
                <div class="mb-6">
                    <label for="kode_nomor" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Kode Nomor
                    </label>
                    <select name="kode_nomor"
                            id="kode_nomor"
                            class="w-full px-3 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 border-gray-300 @error('kode_nomor') border-red-300 @enderror">
                        <option value="">Pilih Kode Nomor (Opsional)</option>
                        @foreach($kodeNomors as $kodeNomor)
                            <option value="{{ $kodeNomor->kode }}" {{ old('kode_nomor') == $kodeNomor->kode ? 'selected' : '' }}>
                                {{ $kodeNomor->kode }}
                                @if($kodeNomor->catatan)
                                    - {{ $kodeNomor->catatan }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('kode_nomor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Pilih kode nomor dari daftar yang tersedia (opsional)</p>
                </div>

                <!-- Account Name Field -->
                <div class="mb-6">
                    <label for="nama_akun" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Nama Akun <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nama_akun"
                           id="nama_akun"
                           value="{{ old('nama_akun') }}"
                           class="w-full px-3 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 border-gray-300 @error('nama_akun') border-red-300 @enderror"
                           placeholder="Masukkan nama akun"
                           maxlength="255"
                           required>
                    @error('nama_akun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Type Field -->
                <div class="mb-6">
                    <label for="tipe_akun" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Tipe Akun <span class="text-red-500">*</span>
                    </label>
                    <select name="tipe_akun"
                            id="tipe_akun"
                            class="w-full px-3 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 border-gray-300 @error('tipe_akun') border-red-300 @enderror"
                            required>
                        <option value="">Pilih Tipe Akun</option>
                        @foreach($tipeAkuns as $tipeAkun)
                            <option value="{{ $tipeAkun->tipe_akun }}" {{ old('tipe_akun') == $tipeAkun->tipe_akun ? 'selected' : '' }}>
                                {{ $tipeAkun->tipe_akun }}
                                @if($tipeAkun->catatan)
                                    - {{ $tipeAkun->catatan }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('tipe_akun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Pilih tipe akun dari daftar yang tersedia</p>
                </div>

                <!-- Balance Field -->
                <div class="mb-6">
                    <label for="saldo" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Saldo Awal
                    </label>
                    <input type="number"
                           name="saldo"
                           id="saldo"
                           value="{{ old('saldo', 0) }}"
                           class="w-full px-3 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 border-gray-300 @error('saldo') border-red-300 @enderror"
                           placeholder="0.00"
                           step="0.01"
                           min="0">
                    @error('saldo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Saldo awal akun (opsional, default 0)</p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('master-coa-index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent text-sm font-medium rounded-lg text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Simpan COA
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
