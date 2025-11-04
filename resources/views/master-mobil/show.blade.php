@extends('layouts.app')@extends('layouts.app')



@section('title', 'Detail Mobil')@section('title', 'Detail Mobil')

@section('page_title', 'Detail Mobil')@section('page_title', 'Detail Mobil')



@section('content')@section('content')

<div class="bg-white p-6 rounded-lg shadow-md"><div class="bg-white shadow-md rounded-lg p-6">

    <!-- Header dengan Breadcrumb -->    <div class="flex justify-between items-center mb-6">

    <div class="mb-6 flex items-center justify-between">        <h2 class="text-xl font-bold text-gray-800">Detail Mobil: {{ $mobil->kode_no }}</h2>

        <div>        <div class="flex space-x-2">

            <nav class="text-sm text-gray-500 mb-2">            <a href="{{ route('master.mobil.edit', $mobil->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

                <a href="{{ route('master.mobil.index') }}" class="hover:text-gray-700">Master Mobil</a>                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                <span class="mx-2">/</span>                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>

                <span class="text-gray-700">Detail</span>                </svg>

            </nav>                Edit

            <h2 class="text-2xl font-bold text-gray-800">            </a>

                Detail Mobil: {{ $mobil->nomor_polisi ?? $mobil->kode_no }}            <a href="{{ route('master.mobil.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

            </h2>                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

        </div>                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>

        <div class="flex space-x-3">                </svg>

            <a href="{{ route('master.mobil.edit', $mobil->id) }}"                 Kembali

               class="inline-flex items-center bg-yellow-500 text-white py-2 px-4 rounded-md hover:bg-yellow-600 transition-colors duration-200">            </a>

                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">        </div>

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>    </div>

                </svg>

                Edit    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            </a>        <!-- Informasi Dasar -->

            <a href="{{ route('master.mobil.index') }}"         <div class="space-y-6">

               class="inline-flex items-center bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition-colors duration-200">            <div class="bg-gray-50 p-6 rounded-lg">

                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Dasar</h3>

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>                <div class="space-y-3">

                </svg>                    <div class="flex justify-between">

                Kembali                        <span class="font-medium text-gray-600">Kode No:</span>

            </a>                        <span class="text-gray-900">{{ $mobil->kode_no ?: '-' }}</span>

        </div>                    </div>

    </div>                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Nomor Polisi:</span>

    <!-- Informasi Utama Mobil -->                        <span class="text-gray-900 font-semibold">{{ $mobil->nomor_polisi ?: '-' }}</span>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">                    </div>

        <!-- Panel Kiri - Identitas Kendaraan -->                    <div class="flex justify-between">

        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">                        <span class="font-medium text-gray-600">Lokasi:</span>

            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">                        <span class="text-gray-900">{{ $mobil->lokasi ?: '-' }}</span>

                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">                    </div>

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>                    <div class="flex justify-between">

                </svg>                        <span class="font-medium text-gray-600">Merek:</span>

                Identitas Kendaraan                        <span class="text-gray-900">{{ $mobil->merek ?: '-' }}</span>

            </h3>                    </div>

            <div class="space-y-3">                    <div class="flex justify-between">

                <div class="flex justify-between">                        <span class="font-medium text-gray-600">Jenis:</span>

                    <span class="text-gray-600 font-medium">Kode Aktiva:</span>                        <span class="text-gray-900">{{ $mobil->jenis ?: '-' }}</span>

                    <span class="text-gray-800 font-mono">{{ $mobil->kode_no ?? '-' }}</span>                    </div>

                </div>                    <div class="flex justify-between">

                <div class="flex justify-between">                        <span class="font-medium text-gray-600">Tahun Pembuatan:</span>

                    <span class="text-gray-600 font-medium">Nomor Polisi:</span>                        <span class="text-gray-900">{{ $mobil->tahun_pembuatan ?: '-' }}</span>

                    <span class="text-gray-800 font-bold">{{ $mobil->nomor_polisi ?? '-' }}</span>                    </div>

                </div>                </div>

                <div class="flex justify-between">            </div>

                    <span class="text-gray-600 font-medium">Merek:</span>

                    <span class="text-gray-800">{{ $mobil->merek ?? '-' }}</span>            <!-- Informasi Teknis -->

                </div>            <div class="bg-gray-50 p-6 rounded-lg">

                <div class="flex justify-between">                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Teknis</h3>

                    <span class="text-gray-600 font-medium">Jenis:</span>                <div class="space-y-3">

                    <span class="text-gray-800">{{ $mobil->jenis ?? '-' }}</span>                    <div class="flex justify-between">

                </div>                        <span class="font-medium text-gray-600">Nomor BPKB:</span>

                <div class="flex justify-between">                        <span class="text-gray-900">{{ $mobil->bpkb ?: '-' }}</span>

                    <span class="text-gray-600 font-medium">Tipe:</span>                    </div>

                    <span class="text-gray-800">{{ $mobil->tipe ?? '-' }}</span>                    <div class="flex justify-between">

                </div>                        <span class="font-medium text-gray-600">Nomor Mesin:</span>

                <div class="flex justify-between">                        <span class="text-gray-900">{{ $mobil->no_mesin ?: '-' }}</span>

                    <span class="text-gray-600 font-medium">Model:</span>                    </div>

                    <span class="text-gray-800">{{ $mobil->model ?? '-' }}</span>                    <div class="flex justify-between">

                </div>                        <span class="font-medium text-gray-600">Nomor Rangka:</span>

                <div class="flex justify-between">                        <span class="text-gray-900">{{ $mobil->nomor_rangka ?: '-' }}</span>

                    <span class="text-gray-600 font-medium">Tahun Pembuatan:</span>                    </div>

                    <span class="text-gray-800">{{ $mobil->tahun_pembuatan ?? '-' }}</span>                    <div class="flex justify-between">

                </div>                        <span class="font-medium text-gray-600">Atas Nama:</span>

                <div class="flex justify-between">                        <span class="text-gray-900">{{ $mobil->atas_nama ?: '-' }}</span>

                    <span class="text-gray-600 font-medium">Silinder:</span>                    </div>

                    <span class="text-gray-800">{{ $mobil->silinder ?? '-' }}</span>                </div>

                </div>            </div>

            </div>        </div>

        </div>

        <!-- Informasi Pajak & Dokumen -->

        <!-- Panel Kanan - Spesifikasi Teknis -->        <div class="space-y-6">

        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">            <div class="bg-gray-50 p-6 rounded-lg">

            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Pajak & Dokumen</h3>

                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">                <div class="space-y-3">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>                    <div class="flex justify-between">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>                        <span class="font-medium text-gray-600">Pajak STNK:</span>

                </svg>                        <span class="text-gray-900 {{ $mobil->pajak_stnk && $mobil->pajak_stnk->isPast() ? 'text-red-600 font-semibold' : '' }}">

                Spesifikasi Teknis                            {{ $mobil->pajak_stnk ? $mobil->pajak_stnk->format('d/m/Y') : '-' }}

            </h3>                            @if($mobil->pajak_stnk && $mobil->pajak_stnk->isPast())

            <div class="space-y-3">                                <span class="text-xs">(Expired)</span>

                <div class="flex justify-between">                            @endif

                    <span class="text-gray-600 font-medium">Bahan Bakar:</span>                        </span>

                    <span class="text-gray-800">{{ $mobil->bahan_bakar ?? '-' }}</span>                    </div>

                </div>                    <div class="flex justify-between">

                <div class="flex justify-between">                        <span class="font-medium text-gray-600">Pajak Plat:</span>

                    <span class="text-gray-600 font-medium">Warna:</span>                        <span class="text-gray-900 {{ $mobil->pajak_plat && $mobil->pajak_plat->isPast() ? 'text-red-600 font-semibold' : '' }}">

                    <span class="text-gray-800">{{ $mobil->warna ?? '-' }}</span>                            {{ $mobil->pajak_plat ? $mobil->pajak_plat->format('d/m/Y') : '-' }}

                </div>                            @if($mobil->pajak_plat && $mobil->pajak_plat->isPast())

                <div class="flex justify-between">                                <span class="text-xs">(Expired)</span>

                    <span class="text-gray-600 font-medium">Warna TNKB:</span>                            @endif

                    <span class="text-gray-800">{{ $mobil->warna_tnkb ?? '-' }}</span>                        </span>

                </div>                    </div>

                <div class="flex justify-between">                    <div class="flex justify-between">

                    <span class="text-gray-600 font-medium">Nomor Rangka:</span>                        <span class="font-medium text-gray-600">Nomor KIR:</span>

                    <span class="text-gray-800 font-mono">{{ $mobil->nomor_rangka ?? '-' }}</span>                        <span class="text-gray-900">{{ $mobil->no_kir ?: '-' }}</span>

                </div>                    </div>

                <div class="flex justify-between">                    <div class="flex justify-between">

                    <span class="text-gray-600 font-medium">Nomor Mesin:</span>                        <span class="font-medium text-gray-600">Pajak KIR:</span>

                    <span class="text-gray-800 font-mono">{{ $mobil->nomor_mesin ?? '-' }}</span>                        <span class="text-gray-900 {{ $mobil->pajak_kir && $mobil->pajak_kir->isPast() ? 'text-red-600 font-semibold' : '' }}">

                </div>                            {{ $mobil->pajak_kir ? $mobil->pajak_kir->format('d/m/Y') : '-' }}

            </div>                            @if($mobil->pajak_kir && $mobil->pajak_kir->isPast())

        </div>                                <span class="text-xs">(Expired)</span>

    </div>                            @endif

                        </span>

    <!-- Informasi Dokumen dan Registrasi -->                    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">                </div>

        <!-- Panel Kiri - Dokumen -->            </div>

        <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">

            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">            <!-- Informasi Asuransi & Lainnya -->

                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">            <div class="bg-gray-50 p-6 rounded-lg">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Asuransi & Lainnya</h3>

                </svg>                <div class="space-y-3">

                Dokumen Kendaraan                    <div class="flex justify-between">

            </h3>                        <span class="font-medium text-gray-600">Pemakai:</span>

            <div class="space-y-3">                        <span class="text-gray-900">{{ $mobil->pemakai ?: '-' }}</span>

                <div class="flex justify-between">                    </div>

                    <span class="text-gray-600 font-medium">Nomor BPKB:</span>                    <div class="flex justify-between">

                    <span class="text-gray-800 font-mono">{{ $mobil->nomor_bpkb ?? '-' }}</span>                        <span class="font-medium text-gray-600">Asuransi:</span>

                </div>                        <span class="text-gray-900">{{ $mobil->asuransi ?: '-' }}</span>

                <div class="flex justify-between">                    </div>

                    <span class="text-gray-600 font-medium">Tanggal STNK:</span>                    <div class="flex justify-between">

                    <span class="text-gray-800">                        <span class="font-medium text-gray-600">Jatuh Tempo Asuransi:</span>

                        {{ $mobil->tanggal_stnk ? \Carbon\Carbon::parse($mobil->tanggal_stnk)->format('d M Y') : '-' }}                        <span class="text-gray-900 {{ $mobil->jatuh_tempo_asuransi && $mobil->jatuh_tempo_asuransi->isPast() ? 'text-red-600 font-semibold' : '' }}">

                    </span>                            {{ $mobil->jatuh_tempo_asuransi ? $mobil->jatuh_tempo_asuransi->format('d/m/Y') : '-' }}

                </div>                            @if($mobil->jatuh_tempo_asuransi && $mobil->jatuh_tempo_asuransi->isPast())

                <div class="flex justify-between">                                <span class="text-xs">(Expired)</span>

                    <span class="text-gray-600 font-medium">Berlaku Sampai:</span>                            @endif

                    <span class="text-gray-800">                        </span>

                        {{ $mobil->berlaku_sampai ? \Carbon\Carbon::parse($mobil->berlaku_sampai)->format('d M Y') : '-' }}                    </div>

                        @if($mobil->berlaku_sampai && \Carbon\Carbon::parse($mobil->berlaku_sampai)->isPast())                    <div class="flex justify-between">

                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">                        <span class="font-medium text-gray-600">Warna Plat:</span>

                                Kedaluwarsa                        <span class="text-gray-900">

                            </span>                            @if($mobil->warna_plat)

                        @elseif($mobil->berlaku_sampai && \Carbon\Carbon::parse($mobil->berlaku_sampai)->diffInDays() <= 30)                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 

                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">                                    @if($mobil->warna_plat == 'Hitam') bg-gray-100 text-gray-800

                                Segera Berakhir                                    @elseif($mobil->warna_plat == 'Kuning') bg-yellow-100 text-yellow-800

                            </span>                                    @elseif($mobil->warna_plat == 'Merah') bg-red-100 text-red-800

                        @endif                                    @elseif($mobil->warna_plat == 'Putih') bg-gray-100 text-gray-800

                    </span>                                    @else bg-gray-100 text-gray-800 @endif">

                </div>                                    {{ $mobil->warna_plat }}

                <div class="flex justify-between">                                </span>

                    <span class="text-gray-600 font-medium">Pajak Sampai:</span>                            @else

                    <span class="text-gray-800">                                -

                        {{ $mobil->pajak_sampai ? \Carbon\Carbon::parse($mobil->pajak_sampai)->format('d M Y') : '-' }}                            @endif

                        @if($mobil->pajak_sampai && \Carbon\Carbon::parse($mobil->pajak_sampai)->isPast())                        </span>

                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">                    </div>

                                Kedaluwarsa                    @if($mobil->catatan)

                            </span>                        <div class="pt-3 border-t border-gray-200">

                        @elseif($mobil->pajak_sampai && \Carbon\Carbon::parse($mobil->pajak_sampai)->diffInDays() <= 30)                            <span class="font-medium text-gray-600 block mb-2">Catatan:</span>

                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">                            <div class="bg-white p-3 rounded border text-gray-900 text-sm whitespace-pre-line">{{ $mobil->catatan }}</div>

                                Segera Berakhir                        </div>

                            </span>                    @endif

                        @endif                </div>

                    </span>            </div>

                </div>

            </div>            <!-- Penugasan Karyawan -->

        </div>            <div class="bg-gray-50 p-6 rounded-lg">

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Penugasan Karyawan</h3>

        <!-- Panel Kanan - Informasi Pembelian -->                <div class="space-y-3">

        <div class="bg-green-50 rounded-lg p-6 border border-green-200">                    @if($mobil->karyawan)

            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">                        <div class="flex items-center space-x-3">

                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">                            <div class="flex-shrink-0">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">

                </svg>                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                Informasi Pembelian                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>

            </h3>                                    </svg>

            <div class="space-y-3">                                </div>

                <div class="flex justify-between">                            </div>

                    <span class="text-gray-600 font-medium">Tanggal Beli:</span>                            <div>

                    <span class="text-gray-800">                                <p class="text-sm font-medium text-gray-900">{{ $mobil->karyawan->nama_lengkap }}</p>

                        {{ $mobil->tanggal_beli ? \Carbon\Carbon::parse($mobil->tanggal_beli)->format('d M Y') : '-' }}                                @if($mobil->karyawan->nik)

                    </span>                                    <p class="text-sm text-gray-500">NIK: {{ $mobil->karyawan->nik }}</p>

                </div>                                @endif

                <div class="flex justify-between">                                @if($mobil->karyawan->divisi)

                    <span class="text-gray-600 font-medium">Harga Beli:</span>                                    <p class="text-sm text-gray-500">{{ $mobil->karyawan->divisi }}</p>

                    <span class="text-gray-800 font-medium">                                @endif

                        {{ $mobil->harga_beli ? 'Rp ' . number_format($mobil->harga_beli, 0, ',', '.') : '-' }}                            </div>

                    </span>                        </div>

                </div>                    @else

                <div class="flex justify-between">                        <div class="text-center py-4">

                    <span class="text-gray-600 font-medium">Dealer:</span>                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <span class="text-gray-800">{{ $mobil->dealer ?? '-' }}</span>                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>

                </div>                            </svg>

            </div>                            <p class="mt-2 text-sm text-gray-500">Belum ada karyawan yang ditugaskan</p>

        </div>                        </div>

    </div>                    @endif

                </div>

    <!-- Informasi Penugasan dan Tambahan -->            </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">        </div>

        <!-- Panel Kiri - Penugasan -->    </div>

        <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">

            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">    <!-- Informasi Metadata -->

                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">    <div class="mt-8 pt-6 border-t border-gray-200">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">

                </svg>            <div>

                Penugasan Karyawan                <span class="font-medium">Dibuat pada:</span> 

            </h3>                {{ $mobil->created_at ? $mobil->created_at->format('d/m/Y H:i') : '-' }}

            <div class="space-y-3">            </div>

                @if($mobil->karyawan)            <div>

                    <div class="bg-white rounded-lg p-4 border border-purple-200">                <span class="font-medium">Terakhir diubah:</span> 

                        <div class="flex items-center">                {{ $mobil->updated_at ? $mobil->updated_at->format('d/m/Y H:i') : '-' }}

                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">            </div>

                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">        </div>

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>    </div>

                                </svg></div>

                            </div>@endsection
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $mobil->karyawan->nama_panggilan ?? $mobil->karyawan->nama_lengkap }}</h4>
                                <p class="text-sm text-gray-600">NIK: {{ $mobil->karyawan->nik }}</p>
                                <p class="text-sm text-gray-600">Divisi: {{ $mobil->karyawan->divisi ?? '-' }}</p>
                                <p class="text-sm text-gray-600">Jabatan: {{ $mobil->karyawan->jabatan ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <p class="text-gray-500">Belum ada karyawan yang ditugaskan</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Panel Kanan - Informasi Tambahan -->
        <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Informasi Tambahan
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 font-medium">Status:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Aktif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 font-medium">Dibuat:</span>
                    <span class="text-gray-800">{{ $mobil->created_at ? $mobil->created_at->format('d M Y H:i') : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 font-medium">Terakhir Update:</span>
                    <span class="text-gray-800">{{ $mobil->updated_at ? $mobil->updated_at->format('d M Y H:i') : '-' }}</span>
                </div>
                @if($mobil->keterangan)
                    <div class="pt-3 border-t border-yellow-200">
                        <span class="text-gray-600 font-medium block mb-2">Keterangan:</span>
                        <p class="text-gray-800 text-sm bg-white p-3 rounded-md border border-yellow-200">
                            {{ $mobil->keterangan }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">
        <div class="flex space-x-3">
            @can('audit-log-view')
                <button type="button" 
                        class="audit-log-btn inline-flex items-center bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition-colors duration-200"
                        data-model-type="{{ get_class($mobil) }}"
                        data-model-id="{{ $mobil->id }}"
                        data-item-name="{{ $mobil->kode_no ?? $mobil->nomor_polisi }}"
                        title="Lihat Riwayat Perubahan">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Riwayat Perubahan
                </button>
            @endcan
        </div>
        
        <div class="flex space-x-3">
            <a href="{{ route('master.mobil.edit', $mobil->id) }}" 
               class="inline-flex items-center bg-yellow-500 text-white py-2 px-4 rounded-md hover:bg-yellow-600 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Mobil
            </a>
            
            <form action="{{ route('master.mobil.destroy', $mobil->id) }}" 
                  method="POST" 
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus mobil {{ $mobil->nomor_polisi ?? $mobil->kode_no }}? Tindakan ini tidak dapat dibatalkan.');"
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Mobil
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Include Audit Log Modal -->
@include('components.audit-log-modal')

@endsection