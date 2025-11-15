@extends('layouts.app')

@section('title', 'Detail Mobil')
@section('page_title', 'Detail Mobil')

@section('content')
<div class="bg-white p-4 rounded-lg shadow-md">
    <!-- Header dengan tombol aksi -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-1">Detail Mobil</h2>
            <p class="text-sm text-gray-600">Informasi lengkap mobil {{ $mobil->kode_no ?? $mobil->nomor_polisi }}</p>
        </div>
        <div class="flex flex-wrap gap-2 mt-3 sm:mt-0">
            <a href="{{ route('master.mobil.index') }}" class="inline-flex items-center bg-gray-600 text-white py-1.5 px-3 rounded text-sm hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
            <a href="{{ route('master.mobil.edit', $mobil->id) }}" class="inline-flex items-center bg-yellow-500 text-white py-1.5 px-3 rounded text-sm hover:bg-yellow-600 transition-colors duration-200">
                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            @can('audit-log-view')
                <button type="button" class="audit-log-btn inline-flex items-center bg-purple-500 text-white py-1.5 px-3 rounded text-sm hover:bg-purple-600 transition-colors duration-200"
                        data-model-type="{{ get_class($mobil) }}"
                        data-model-id="{{ $mobil->id }}"
                        data-item-name="{{ $mobil->kode_no }}"
                        title="Lihat Riwayat">
                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Riwayat
                </button>
            @endcan
            <form action="{{ route('master.mobil.destroy', $mobil->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mobil ini?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center bg-red-500 text-white py-1.5 px-3 rounded text-sm hover:bg-red-600 transition-colors duration-200">
                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-4">
        @if($mobil->nomor_polisi && $mobil->no_kir)
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Dokumen Lengkap
            </span>
        @elseif($mobil->nomor_polisi || $mobil->no_kir)
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                Dokumen Parsial
            </span>
        @else
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Dokumen Tidak Lengkap
            </span>
        @endif
    </div>

    <!-- Grid Layout untuk Informasi -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        <!-- Informasi Identitas -->
        <div class="lg:col-span-2">
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                    Informasi Identitas
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kode Aktiva</label>
                        <p class="text-xs bg-white p-2 rounded border font-mono">{{ $mobil->kode_no ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nomor Polisi</label>
                        <p class="text-xs bg-white p-2 rounded border">
                            @if($mobil->nomor_polisi)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $mobil->nomor_polisi }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nomor KIR</label>
                        <p class="text-xs bg-white p-2 rounded border">
                            @if($mobil->no_kir)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $mobil->no_kir }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi</label>
                        <p class="text-xs bg-white p-2 rounded border">{{ $mobil->lokasi ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">BPKB</label>
                        <p class="text-xs bg-white p-2 rounded border font-mono">{{ $mobil->bpkb ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Atas Nama</label>
                        <p class="text-xs bg-white p-2 rounded border">{{ $mobil->atas_nama ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pemakai</label>
                        <p class="text-xs bg-white p-2 rounded border">{{ $mobil->pemakai ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Warna Plat</label>
                        <p class="text-xs bg-white p-2 rounded border">
                            @if($mobil->warna_plat)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium 
                                    @if($mobil->warna_plat == 'Hitam') bg-gray-100 text-gray-800
                                    @elseif($mobil->warna_plat == 'Kuning') bg-yellow-100 text-yellow-800
                                    @elseif($mobil->warna_plat == 'Merah') bg-red-100 text-red-800
                                    @elseif($mobil->warna_plat == 'Biru') bg-blue-100 text-blue-800
                                    @elseif($mobil->warna_plat == 'Putih') bg-gray-50 text-gray-800 border
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $mobil->warna_plat }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Spesifikasi Kendaraan -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Spesifikasi Kendaraan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Merek</label>
                        <p class="text-sm bg-white p-3 rounded border">{{ $mobil->merek ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Jenis</label>
                        <p class="text-sm bg-white p-3 rounded border">{{ $mobil->jenis ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tahun Pembuatan</label>
                        <p class="text-sm bg-white p-3 rounded border">{{ $mobil->tahun_pembuatan ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Warna</label>
                        <p class="text-sm bg-white p-3 rounded border">{{ $mobil->warna ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Rangka</label>
                        <p class="text-sm bg-white p-3 rounded border font-mono">{{ $mobil->nomor_rangka ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Mesin</label>
                        <p class="text-sm bg-white p-3 rounded border font-mono">{{ $mobil->no_mesin ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Pajak & Dokumen -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Informasi Pajak & Dokumen
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pajak STNK</label>
                        <p class="text-xs bg-white p-2 rounded border">
                            @if($mobil->pajak_stnk)
                                {{ \Carbon\Carbon::parse($mobil->pajak_stnk)->format('d M Y') }}
                                @php
                                    $expiry = \Carbon\Carbon::parse($mobil->pajak_stnk);
                                    $now = \Carbon\Carbon::now();
                                    $diff = $expiry->diffInDays($now, false);
                                @endphp
                                @if($diff > 0)
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Expired {{ $diff }}h
                                    </span>
                                @elseif($diff > -30)
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ abs($diff) }}h lagi
                                    </span>
                                @else
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Valid
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pajak Plat</label>
                        <p class="text-xs bg-white p-2 rounded border">
                            @if($mobil->pajak_plat)
                                {{ \Carbon\Carbon::parse($mobil->pajak_plat)->format('d M Y') }}
                                @php
                                    $expiry = \Carbon\Carbon::parse($mobil->pajak_plat);
                                    $now = \Carbon\Carbon::now();
                                    $diff = $expiry->diffInDays($now, false);
                                @endphp
                                @if($diff > 0)
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Expired {{ $diff }}h
                                    </span>
                                @elseif($diff > -30)
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ abs($diff) }}h lagi
                                    </span>
                                @else
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Valid
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pajak KIR</label>
                        <p class="text-xs bg-white p-2 rounded border">
                            @if($mobil->pajak_kir)
                                {{ \Carbon\Carbon::parse($mobil->pajak_kir)->format('d M Y') }}
                                @php
                                    $expiry = \Carbon\Carbon::parse($mobil->pajak_kir);
                                    $now = \Carbon\Carbon::now();
                                    $diff = $expiry->diffInDays($now, false);
                                @endphp
                                @if($diff > 0)
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Expired {{ $diff }}h
                                    </span>
                                @elseif($diff > -30)
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ abs($diff) }}h lagi
                                    </span>
                                @else
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Valid
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informasi Asuransi -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Informasi Asuransi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Perusahaan Asuransi</label>
                        <p class="text-sm bg-white p-3 rounded border">{{ $mobil->asuransi ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Jatuh Tempo Asuransi</label>
                        <p class="text-sm bg-white p-3 rounded border">
                            @if($mobil->tanggal_jatuh_tempo_asuransi)
                                {{ \Carbon\Carbon::parse($mobil->tanggal_jatuh_tempo_asuransi)->format('d F Y') }}
                                @php
                                    $expiry = \Carbon\Carbon::parse($mobil->tanggal_jatuh_tempo_asuransi);
                                    $now = \Carbon\Carbon::now();
                                    $diff = $expiry->diffInDays($now, false);
                                @endphp
                                @if($diff > 0)
                                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Expired {{ $diff }} hari
                                    </span>
                                @elseif($diff > -30)
                                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ abs($diff) }} hari lagi
                                    </span>
                                @else
                                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Valid
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Catatan & Keterangan
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Catatan</label>
                        <p class="text-sm bg-white p-3 rounded border min-h-[100px]">
                            {{ $mobil->catatan ?? 'Tidak ada catatan tambahan.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Informasi -->
        <div class="lg:col-span-1">
            
            <!-- Informasi Karyawan -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Karyawan Pengguna
                </h3>
                @if($mobil->karyawan)
                    <div class="space-y-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nama Lengkap</label>
                            <p class="text-xs bg-white p-2 rounded border">{{ $mobil->karyawan->nama_lengkap }}</p>
                        </div>
                        @if($mobil->karyawan->nik)
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">NIK</label>
                                <p class="text-xs bg-white p-2 rounded border font-mono">{{ $mobil->karyawan->nik }}</p>
                            </div>
                        @endif
                        @if($mobil->karyawan->jabatan)
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Jabatan</label>
                                <p class="text-xs bg-white p-2 rounded border">{{ $mobil->karyawan->jabatan }}</p>
                            </div>
                        @endif
                        <a href="{{ route('master.karyawan.show', $mobil->karyawan->id) }}" class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            Lihat Detail Karyawan
                        </a>
                    </div>
                @else
                    <div class="text-center py-3">
                        <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <p class="text-xs text-gray-500 mt-1">Belum ada karyawan yang ditugaskan</p>
                    </div>
                @endif
            </div>

            <!-- Tanggal & Timestamp -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Informasi Waktu
                </h3>
                <div class="space-y-2">
                    @if($mobil->tanggal_keluar)
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Keluar</label>
                            <p class="text-xs bg-white p-2 rounded border">
                                {{ \Carbon\Carbon::parse($mobil->tanggal_keluar)->format('d M Y') }}
                            </p>
                        </div>
                    @endif
                    @if($mobil->tanggal_masuk)
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Masuk</label>
                            <p class="text-xs bg-white p-2 rounded border">
                                {{ \Carbon\Carbon::parse($mobil->tanggal_masuk)->format('d M Y') }}
                            </p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Data Dibuat</label>
                        <p class="text-xs bg-white p-2 rounded border">
                            {{ $mobil->created_at->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                    @if($mobil->updated_at != $mobil->created_at)
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Terakhir Diupdate</label>
                            <p class="text-xs bg-white p-2 rounded border">
                                {{ $mobil->updated_at->format('d M Y, H:i') }} WIB
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Audit Log Modal -->
@include('components.audit-log-modal')

@endsection