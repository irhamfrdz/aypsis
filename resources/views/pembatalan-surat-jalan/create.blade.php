@extends('layouts.app')

@section('title', 'Tambah Pembatalan Surat Jalan')
@section('page_title', 'Tambah Pembatalan Surat Jalan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Transaksi Pembatalan</h1>
                <p class="mt-1 text-sm text-gray-600">Pilih Surat Jalan dan berikan alasan pembatalan</p>
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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Form Card -->
            <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form action="{{ route('pembatalan-surat-jalan.store') }}" method="POST">
                    @csrf

                    <div class="mb-5">
                        <label for="surat_jalan_id_select" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Surat Jalan <span class="text-red-500">*</span></label>
                        <select id="surat_jalan_id_select" name="surat_jalan_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 select2" required>
                            <option value="">-- Cari Surat Jalan --</option>
                            @foreach($suratJalans as $sj)
                                <option value="{{ $sj->id }}">{{ $sj->no_surat_jalan }} - {{ $sj->pengirim }}</option>
                            @endforeach
                        </select>
                        @error('surat_jalan_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="alasan_batal" class="block text-sm font-semibold text-gray-700 mb-2">Alasan Batal <span class="text-red-500">*</span></label>
                        <textarea id="alasan_batal" name="alasan_batal" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Berikan alasan pembatalan" required>{{ old('alasan_batal') }}</textarea>
                        @error('alasan_batal')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" onclick="return confirm('Yakin ingin memproses pembatalan ini? Surat jalan akan berganti status menjadi CANCELLED.')" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow-sm transition">
                             Simpan Transaksi Pembatalan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Card -->
            <div class="bg-amber-50 rounded-xl border border-amber-200 p-6 text-sm text-amber-900 h-fit">
                <h3 class="font-bold mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Informasi Side Effects
                </h3>
                <p>Saat Anda menyimpan transaksi ini:</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li>Status Surat Jalan otomatis menjadi <b class="text-red-700">Cancelled</b>.</li>
                    <li>Status detail di tabel prospek otomatis menjadi <b class="text-red-700">Batal</b>.</li>
                </ul>
            </div>
        </div>

    </div>
</div>

<!-- Select2 setup if typical -->
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Cari Surat Jalan...",
        allowClear: true,
        width: '100%'
    });
});
</script>
@endsection
