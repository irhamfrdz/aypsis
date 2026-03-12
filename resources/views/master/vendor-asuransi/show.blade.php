@extends('layouts.app')

@section('title', 'Detail Vendor Asuransi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-white">Detail Vendor Asuransi</h1>
                    <p class="text-blue-100 text-sm">Informasi lengkap data asuransi</p>
                </div>
                <div class="flex space-x-2">
                    @can('master-vendor-asuransi-update')
                    <a href="{{ route('master.vendor-asuransi.edit', $vendorAsuransi) }}"
                       class="bg-white text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        Edit Data
                    </a>
                    @endcan
                    <a href="{{ route('master.vendor-asuransi.index') }}"
                       class="bg-blue-700 text-white hover:bg-blue-800 px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        Kembali
                    </a>
                </div>
            </div>

            <div class="p-6 space-y-8">
                <!-- Basic Info -->
                <div>
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Informasi Utama</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Kode Vendor</p>
                            <p class="font-mono text-gray-900">{{ $vendorAsuransi->kode ?: 'Tidak ada kode' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Nama Asuransi</p>
                            <p class="font-bold text-lg text-gray-900">{{ $vendorAsuransi->nama_asuransi }}</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div>
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Kontak & Alamat</h2>
                    <div class="space-y-4">
                        <div class="flex items-start bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                            <div class="bg-blue-100 p-2 rounded-lg mr-4">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Alamat Kantor</p>
                                <p class="text-gray-900 leading-relaxed">{{ $vendorAsuransi->alamat ?: 'Alamat tidak diisi' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                                <div class="bg-green-100 p-2 rounded-lg mr-4">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Nomor Telepon</p>
                                    <p class="text-gray-900 font-medium">{{ $vendorAsuransi->telepon ?: '-' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                                <div class="bg-purple-100 p-2 rounded-lg mr-4">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Email Address</p>
                                    <p class="text-gray-900 font-medium">{{ $vendorAsuransi->email ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Keterangan</h2>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 min-h-[100px]">
                            <p class="text-gray-700">{{ $vendorAsuransi->keterangan ?: 'Tidak ada keterangan' }}</p>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Catatan Internal</h2>
                        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 min-h-[100px]">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $vendorAsuransi->catatan ?: 'Tidak ada catatan' }}</p>
                        </div>
                    </div>
                </div>

                <!-- System Audit -->
                <div class="pt-6 border-t border-gray-100">
                    <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Dibuat: {{ $vendorAsuransi->created_at->format('d M Y H:i') }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Terakhir Update: {{ $vendorAsuransi->updated_at->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
