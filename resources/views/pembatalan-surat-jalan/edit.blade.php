@extends('layouts.app')

@section('title', 'Edit Pembatalan Surat Jalan')
@section('page_title', 'Edit Pembatalan Surat Jalan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Transaksi Pembatalan</h1>
                <p class="mt-1 text-sm text-gray-600">Perbarui alasan pembatalan untuk surat jalan {{ $pembatalanSuratJalan->no_surat_jalan }}</p>
            </div>
            <a href="{{ route('pembatalan-surat-jalan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('pembatalan-surat-jalan.update', $pembatalanSuratJalan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">No. Surat Jalan</label>
                        <input type="text" value="{{ $pembatalanSuratJalan->no_surat_jalan }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-100 font-medium text-gray-800" disabled>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="alasan_batal" class="block text-sm font-semibold text-gray-700 mb-2">Alasan Batal <span class="text-red-500">*</span></label>
                    <textarea id="alasan_batal" name="alasan_batal" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Berikan alasan pembatalan" required>{{ $pembatalanSuratJalan->alasan_batal }}</textarea>
                    @error('alasan_batal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition">
                         Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
