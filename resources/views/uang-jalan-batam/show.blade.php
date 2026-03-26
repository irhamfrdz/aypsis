@extends('layouts.app')

@section('page_title', 'Detail Uang Jalan Batam')

@section('content')
<div class="container mx-auto px-3 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Uang Jalan Batam</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap rincian pembayaran uang jalan</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('uang-jalan-batam.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors border border-gray-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                @can('uang-jalan-batam-update')
                <a href="{{ route('uang-jalan-batam.edit', $uangJalan->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Data
                </a>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Primary Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Details Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-blue-600 px-6 py-4">
                        <div class="flex justify-between items-center text-white">
                            <div>
                                <p class="text-blue-100 text-xs font-medium uppercase tracking-wider">Nomor Uang Jalan</p>
                                <h2 class="text-xl font-bold">{{ $uangJalan->nomor_uang_jalan }}</h2>
                            </div>
                            <div class="text-right">
                                <p class="text-blue-100 text-xs font-medium uppercase tracking-wider">Tanggal</p>
                                <h2 class="text-lg font-bold">{{ $uangJalan->tanggal_uang_jalan ? $uangJalan->tanggal_uang_jalan->format('d M Y') : '-' }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-y-6">
                            <div>
                                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Status Pembayaran</p>
                                @php
                                    $statusClasses = [
                                        'belum_dibayar' => 'bg-yellow-100 text-yellow-800',
                                        'belum_masuk_pranota' => 'bg-orange-100 text-orange-800',
                                        'sudah_masuk_pranota' => 'bg-blue-100 text-blue-800',
                                        'lunas' => 'bg-green-100 text-green-800',
                                        'dibatalkan' => 'bg-red-100 text-red-800'
                                    ];
                                    $label = \App\Models\UangJalanBatam::getStatusOptions()[$uangJalan->status] ?? $uangJalan->status;
                                @endphp
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $statusClasses[$uangJalan->status] ?? 'bg-gray-100' }}">
                                    {{ $label }}
                                </span>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Dibuat Oleh</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $uangJalan->createdBy->name ?? 'System' }}</p>
                            </div>
                            <div class="col-span-2 pt-4 border-t border-gray-100">
                                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">Informasi Surat Jalan</p>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-400 text-[10px] font-bold uppercase mb-0.5">No. Surat Jalan</p>
                                            <p class="font-bold text-gray-900">{{ $uangJalan->suratJalanBatam->no_surat_jalan }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-[10px] font-bold uppercase mb-0.5">Order ID</p>
                                            <p class="font-bold text-gray-900">{{ $uangJalan->suratJalanBatam->orderBatam->nomor_order ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-[10px] font-bold uppercase mb-0.5">Supir</p>
                                            <p class="font-bold text-gray-900">{{ $uangJalan->suratJalanBatam->supir ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-[10px] font-bold uppercase mb-0.5">No Plat</p>
                                            <p class="font-bold text-gray-900">{{ $uangJalan->suratJalanBatam->no_plat ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Costs Breakdown -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center bg-gray-50">
                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Rincian Komponen Biaya</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="flex justify-between items-center px-6 py-4">
                            <span class="text-sm text-gray-600 font-medium">Uang Jalan Pokok</span>
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($uangJalan->jumlah_uang_jalan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center px-6 py-4">
                            <span class="text-sm text-gray-600 font-medium">MEL</span>
                            <span class="text-sm font-medium text-gray-900">Rp {{ number_format($uangJalan->jumlah_mel, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center px-6 py-4">
                            <span class="text-sm text-gray-600 font-medium">Pelancar</span>
                            <span class="text-sm font-medium text-gray-900">Rp {{ number_format($uangJalan->jumlah_pelancar, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center px-6 py-4">
                            <span class="text-sm text-gray-600 font-medium">Kawalan</span>
                            <span class="text-sm font-medium text-gray-900">Rp {{ number_format($uangJalan->jumlah_kawalan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center px-6 py-4">
                            <span class="text-sm text-gray-600 font-medium">Parkir</span>
                            <span class="text-sm font-medium text-gray-900">Rp {{ number_format($uangJalan->jumlah_parkir, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center px-6 py-4 bg-gray-50 border-t-2 border-gray-100">
                            <span class="text-sm text-gray-800 font-bold uppercase tracking-wider">Subtotal</span>
                            <span class="text-sm font-extrabold text-blue-600">Rp {{ number_format($uangJalan->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Totals & Memo -->
            <div class="space-y-6">
                <!-- Final Total Card -->
                <div class="bg-blue-600 rounded-xl shadow-md p-6 text-white overflow-hidden relative">
                    <div class="relative z-10">
                        <p class="text-blue-200 text-xs font-bold uppercase tracking-widest mb-2">Total Bayar Akhir</p>
                        <h2 class="text-4xl font-black mb-1">Rp {{ number_format($uangJalan->jumlah_total, 0, ',', '.') }}</h2>
                        @if($uangJalan->jumlah_penyesuaian != 0)
                            <div class="mt-4 pt-4 border-t border-blue-500 flex justify-between items-start">
                                <div>
                                    <p class="text-blue-200 text-[10px] font-bold uppercase">Penyesuaian</p>
                                    <p class="text-sm font-bold {{ $uangJalan->jumlah_penyesuaian > 0 ? 'text-green-300' : 'text-red-300' }}">
                                        {{ $uangJalan->jumlah_penyesuaian > 0 ? '+' : '' }} Rp {{ number_format($uangJalan->jumlah_penyesuaian, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="text-right max-w-[60%]">
                                    <p class="text-blue-200 text-[10px] font-bold uppercase">Keterangan</p>
                                    <p class="text-xs italic">{{ $uangJalan->alasan_penyesuaian ?: 'Tanpa alasan' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <!-- Decoration icon -->
                    <div class="absolute -right-4 -bottom-4 opacity-10">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.82v-1.91c-1.56-.16-3.08-.82-4.14-1.87l1.34-1.34c.83.83 2.02 1.35 3.3 1.4v-4.13c-1.8-.46-3.41-1.35-4.52-2.43l1.34-1.34c.83.82 2.01 1.34 3.12 1.4V8c-1.56-.16-3.08-.82-4.14-1.87l1.34-1.34c.83.83 2.02 1.35 3.3 1.4V4h2.82v1.91c1.55.16 3.07.82 4.14 1.87l-1.34 1.34c-.83-.83-1.99-1.35-3.23-1.4v4.13c1.8.46 3.4 1.35 4.52 2.43l-1.34 1.34c-.83-.82-2.01-1.34-3.12-1.4v2.09c1.56.16 3.08.82 4.14 1.87l-1.34 1.34c-.83-.83-2.02-1.34-3.3-1.4z"/>
                        </svg>
                    </div>
                </div>

                <!-- Memo Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 h-5 flex items-center">
                        <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Memo / Catatan
                    </h3>
                    <div class="text-sm text-gray-700 bg-gray-50 rounded-lg p-4 italic border-l-4 border-gray-300">
                        {{ $uangJalan->memo ?: 'Tidak ada memo tambahan.' }}
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-xs text-gray-500 space-y-2">
                    <div class="flex justify-between">
                        <span>Waktu Input:</span>
                        <span class="font-medium text-gray-700">{{ $uangJalan->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Update Terakhir:</span>
                        <span class="font-medium text-gray-700">{{ $uangJalan->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
