@extends('layouts.app')

@section('title', 'Edit Tipe Akun')
@section('page_title', 'Edit Tipe Akun')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Tipe Akun</h1>
                    <p class="mt-1 text-sm text-gray-600">Edit data tipe akun</p>
                </div>
                <a href="{{ route('master.tipe-akun.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('master.tipe-akun.update', $tipeAkun) }}">
                @csrf
                @method('PUT')

                <!-- Tipe Akun Field -->
                <div class="mb-6">
                    <label for="tipe_akun" class="block text-sm font-medium text-gray-700 mb-2">Tipe Akun <span class="text-red-500">*</span></label>
                    <input type="text" name="tipe_akun" id="tipe_akun" value="{{ old('tipe_akun', $tipeAkun->tipe_akun) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('tipe_akun') border-red-500 @enderror" placeholder="Masukkan tipe akun (contoh: Kas, Bank, Piutang)" required>
                    @error('tipe_akun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Field -->
                <div class="mb-6">
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('catatan') border-red-500 @enderror" placeholder="Masukkan catatan tipe akun (opsional)">{{ old('catatan', $tipeAkun->catatan) }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('master.tipe-akun.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
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
