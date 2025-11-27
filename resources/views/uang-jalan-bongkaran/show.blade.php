@extends('layouts.app')

@section('page_title', 'Detail Uang Jalan Bongkaran')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Detail Uang Jalan Bongkaran</h1>
                        <p class="text-gray-600 mt-1">
                            {{ $uangJalanBongkaran->nomor_uang_jalan ?? 'No. Uang Jalan belum digenerate' }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        @can('uang-jalan-bongkaran-update')
                        @if(in_array($uangJalanBongkaran->status, ['belum_dibayar', 'belum_masuk_pranota']))
                        <a href="{{ route('uang-jalan-bongkaran.edit', $uangJalanBongkaran->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Uang Jalan Bongkaran
                        </a>
                        @endif
                        @endcan
                        <a href="{{ route('uang-jalan-bongkaran.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Surat Jalan -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Informasi Surat Jalan Bongkaran</h2>
                    </div>
                    <div class="px-6 py-4">
                        @if($uangJalanBongkaran->suratJalanBongkaran)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan</label>
                                <p class="text-sm text-gray-900 font-semibold">{{ $uangJalanBongkaran->suratJalanBongkaran->nomor_surat_jalan }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat Jalan</label>
                                <p class="text-sm text-gray-900">
                                    {{ $uangJalanBongkaran->suratJalanBongkaran->tanggal_surat_jalan ? $uangJalanBongkaran->suratJalanBongkaran->tanggal_surat_jalan->format('d/m/Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kegiatan</label>
                                <p class="text-sm text-gray-900">{{ $uangJalanBongkaran->suratJalanBongkaran->kegiatan ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                                <p class="text-sm text-gray-900">{{ $uangJalanBongkaran->suratJalanBongkaran->supir ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                                <p class="text-sm text-gray-900">{{ $uangJalanBongkaran->suratJalanBongkaran->kenek ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Plat</label>
                                <p class="text-sm text-gray-900">{{ $uangJalanBongkaran->suratJalanBongkaran->no_plat ?? '-' }}</p>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">Data surat jalan bongkaran tidak ditemukan</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Detail Uang Jalan Bongkaran -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Detail Pembayaran</h2>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Komponen Biaya -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-gray-900 border-b pb-2">Komponen Biaya</h3>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Uang Jalan</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($uangJalanBongkaran->jumlah_uang_jalan ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">MEL</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($uangJalanBongkaran->jumlah_mel ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Pelancar</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($uangJalanBongkaran->jumlah_pelancar ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Kawalan</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($uangJalanBongkaran->jumlah_kawalan ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Parkir</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($uangJalanBongkaran->jumlah_parkir ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 font-semibold text-gray-900 border-t border-gray-200">
                                    <span class="text-sm">Subtotal</span>
                                    <span class="text-sm">
                                        Rp {{ number_format($uangJalanBongkaran->subtotal ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Penyesuaian dan Total -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-gray-900 border-b pb-2">Penyesuaian</h3>
                                
                                @if($uangJalanBongkaran->alasan_penyesuaian)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penyesuaian</label>
                                    <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">
                                        {{ $uangJalanBongkaran->alasan_penyesuaian }}
                                    </p>
                                </div>
                                @endif
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Jumlah Penyesuaian</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($uangJalanBongkaran->jumlah_penyesuaian ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-blue-900">Total Uang Jalan</span>
                                        <span class="text-xl font-bold text-blue-900">
                                            Rp {{ number_format($uangJalanBongkaran->jumlah_total ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if($uangJalanBongkaran->memo)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Memo</label>
                                    <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">
                                        {{ $uangJalanBongkaran->memo }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Status</h2>
                    </div>
                    <div class="px-6 py-4">...
