@extends('layouts.app')

@section('title', 'Detail Pembatalan Surat Jalan')
@section('page_title', 'Detail Pembatalan Surat Jalan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Transaksi Pembatalan</h1>
                <p class="mt-1 text-sm text-gray-600">ID Log Transaksi: SP-{{ $pembatalanSuratJalan->id }}</p>
            </div>
            <a href="{{ route('pembatalan-surat-jalan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Surat Jalan</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-sm">
                <div>
                    <span class="block text-gray-500">No. Surat Jalan</span>
                    <span class="font-bold text-gray-900">{{ $pembatalanSuratJalan->no_surat_jalan }}</span>
                </div>
                <div>
                    <span class="block text-gray-500">Waktu Pembatalan</span>
                    <span class="font-bold text-gray-900 text-green-700">{{ $pembatalanSuratJalan->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4 mb-4">
                <span class="block text-gray-500 text-sm">Alasan Batal</span>
                <p class="font-medium text-gray-800 mt-1 bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm">
                    {{ $pembatalanSuratJalan->alasan_batal }}
                </p>
            </div>

            @if($pembatalanSuratJalan->suratJalan)
            <div class="border-t border-gray-100 pt-4">
                <h3 class="text-base font-bold text-gray-800 mb-2">Detail Riwayat Pengiriman</h3>
                <div class="text-xs text-gray-600 grid grid-cols-2 gap-2">
                    <div>
                        <span class="block">Pengirim:</span>
                        <b class="text-gray-800">{{ $pembatalanSuratJalan->suratJalan->pengirim ?? '-' }}</b>
                    </div>
                    <div>
                        <span class="block">Tujuan:</span>
                        <b class="text-gray-800">{{ $pembatalanSuratJalan->suratJalan->tujuan_pengiriman ?? '-' }}</b>
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
