@extends('layouts.app')

@section('title', 'Detail Mobil')
@section('page_title', 'Detail Mobil')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Detail Mobil: {{ $mobil->kode_no }}</h2>
        <div class="flex space-x-2">
            <a href="{{ route('master.mobil.edit', $mobil->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            <a href="{{ route('master.mobil.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Informasi Dasar -->
        <div class="space-y-6">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Dasar</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Kode No:</span>
                        <span class="text-gray-900">{{ $mobil->kode_no ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Nomor Polisi:</span>
                        <span class="text-gray-900 font-semibold">{{ $mobil->nomor_polisi ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Lokasi:</span>
                        <span class="text-gray-900">{{ $mobil->lokasi ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Merek:</span>
                        <span class="text-gray-900">{{ $mobil->merek ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Jenis:</span>
                        <span class="text-gray-900">{{ $mobil->jenis ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Tahun Pembuatan:</span>
                        <span class="text-gray-900">{{ $mobil->tahun_pembuatan ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Informasi Teknis -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Teknis</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Nomor BPKB:</span>
                        <span class="text-gray-900">{{ $mobil->bpkb ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Nomor Mesin:</span>
                        <span class="text-gray-900">{{ $mobil->no_mesin ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Nomor Rangka:</span>
                        <span class="text-gray-900">{{ $mobil->nomor_rangka ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Atas Nama:</span>
                        <span class="text-gray-900">{{ $mobil->atas_nama ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Pajak & Dokumen -->
        <div class="space-y-6">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Pajak & Dokumen</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Pajak STNK:</span>
                        <span class="text-gray-900 {{ $mobil->pajak_stnk && $mobil->pajak_stnk->isPast() ? 'text-red-600 font-semibold' : '' }}">
                            {{ $mobil->pajak_stnk ? $mobil->pajak_stnk->format('d/m/Y') : '-' }}
                            @if($mobil->pajak_stnk && $mobil->pajak_stnk->isPast())
                                <span class="text-xs">(Expired)</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Pajak Plat:</span>
                        <span class="text-gray-900 {{ $mobil->pajak_plat && $mobil->pajak_plat->isPast() ? 'text-red-600 font-semibold' : '' }}">
                            {{ $mobil->pajak_plat ? $mobil->pajak_plat->format('d/m/Y') : '-' }}
                            @if($mobil->pajak_plat && $mobil->pajak_plat->isPast())
                                <span class="text-xs">(Expired)</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Nomor KIR:</span>
                        <span class="text-gray-900">{{ $mobil->no_kir ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Pajak KIR:</span>
                        <span class="text-gray-900 {{ $mobil->pajak_kir && $mobil->pajak_kir->isPast() ? 'text-red-600 font-semibold' : '' }}">
                            {{ $mobil->pajak_kir ? $mobil->pajak_kir->format('d/m/Y') : '-' }}
                            @if($mobil->pajak_kir && $mobil->pajak_kir->isPast())
                                <span class="text-xs">(Expired)</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Informasi Asuransi & Lainnya -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Asuransi & Lainnya</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Pemakai:</span>
                        <span class="text-gray-900">{{ $mobil->pemakai ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Asuransi:</span>
                        <span class="text-gray-900">{{ $mobil->asuransi ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Jatuh Tempo Asuransi:</span>
                        <span class="text-gray-900 {{ $mobil->jatuh_tempo_asuransi && $mobil->jatuh_tempo_asuransi->isPast() ? 'text-red-600 font-semibold' : '' }}">
                            {{ $mobil->jatuh_tempo_asuransi ? $mobil->jatuh_tempo_asuransi->format('d/m/Y') : '-' }}
                            @if($mobil->jatuh_tempo_asuransi && $mobil->jatuh_tempo_asuransi->isPast())
                                <span class="text-xs">(Expired)</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Warna Plat:</span>
                        <span class="text-gray-900">
                            @if($mobil->warna_plat)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($mobil->warna_plat == 'Hitam') bg-gray-100 text-gray-800
                                    @elseif($mobil->warna_plat == 'Kuning') bg-yellow-100 text-yellow-800
                                    @elseif($mobil->warna_plat == 'Merah') bg-red-100 text-red-800
                                    @elseif($mobil->warna_plat == 'Putih') bg-gray-100 text-gray-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $mobil->warna_plat }}
                                </span>
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    @if($mobil->catatan)
                        <div class="pt-3 border-t border-gray-200">
                            <span class="font-medium text-gray-600 block mb-2">Catatan:</span>
                            <div class="bg-white p-3 rounded border text-gray-900 text-sm whitespace-pre-line">{{ $mobil->catatan }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Penugasan Karyawan -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Penugasan Karyawan</h3>
                <div class="space-y-3">
                    @if($mobil->karyawan)
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $mobil->karyawan->nama_lengkap }}</p>
                                @if($mobil->karyawan->nik)
                                    <p class="text-sm text-gray-500">NIK: {{ $mobil->karyawan->nik }}</p>
                                @endif
                                @if($mobil->karyawan->divisi)
                                    <p class="text-sm text-gray-500">{{ $mobil->karyawan->divisi }}</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Belum ada karyawan yang ditugaskan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Metadata -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
            <div>
                <span class="font-medium">Dibuat pada:</span> 
                {{ $mobil->created_at ? $mobil->created_at->format('d/m/Y H:i') : '-' }}
            </div>
            <div>
                <span class="font-medium">Terakhir diubah:</span> 
                {{ $mobil->updated_at ? $mobil->updated_at->format('d/m/Y H:i') : '-' }}
            </div>
        </div>
    </div>
</div>
@endsection