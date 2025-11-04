@extends('layouts.app')@extends('layouts.app')@extends('layouts.app')@extends('layouts.app')



@section('title', 'Detail Mobil')

@section('page_title', 'Detail Mobil')

@section('title', 'Detail Mobil')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6">

    <div class="flex justify-between items-center mb-6">

        <h2 class="text-xl font-bold text-gray-800">Detail Mobil: {{ $mobil->kode_no ?? $mobil->nomor_polisi }}</h2>@section('page_title', 'Detail Mobil')@section('title', 'Detail Mobil')@section('title', 'Detail Mobil')

        <div class="flex space-x-2">

            <a href="{{ route('master.mobil.edit', $mobil->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>@section('content')@section('page_title', 'Detail Mobil')@section('page_title', 'Detail Mobil')

                </svg>

                Edit<div class="bg-white shadow-md rounded-lg p-6">

            </a>

            <a href="{{ route('master.mobil.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">    <div class="flex justify-between items-center mb-6">

                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>        <h2 class="text-xl font-bold text-gray-800">Detail Mobil: {{ $mobil->kode_no ?? $mobil->nomor_polisi }}</h2>

                </svg>

                Kembali        <div class="flex space-x-2">@section('content')@section('content')

            </a>

        </div>            <a href="{{ route('master.mobil.edit', $mobil->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

    </div>

                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><div class="bg-white shadow-md rounded-lg p-6"><div class="bg-white shadow-md rounded-lg p-6">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Panel Kiri -->                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>

        <div class="space-y-6">

            <!-- Informasi Dasar -->                </svg>    <div class="flex justify-between items-center mb-6">    <div class="flex justify-between items-center mb-6">

            <div class="bg-gray-50 p-6 rounded-lg">

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Dasar</h3>                Edit

                <div class="space-y-3">

                    <div class="flex justify-between">            </a>        <h2 class="text-xl font-bold text-gray-800">Detail Mobil: {{ $mobil->kode_no ?? $mobil->nomor_polisi }}</h2>        <h2 class="text-xl font-bold text-gray-800">Detail Mobil: {{ $mobil->kode_no }}</h2>

                        <span class="font-medium text-gray-600">Kode No:</span>

                        <span class="text-gray-900">{{ $mobil->kode_no ?: '-' }}</span>            <a href="{{ route('master.mobil.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

                    </div>

                    <div class="flex justify-between">                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">        <div class="flex space-x-2">        <div class="flex space-x-2">

                        <span class="font-medium text-gray-600">Nomor Polisi:</span>

                        <span class="text-gray-900 font-semibold">{{ $mobil->nomor_polisi ?: '-' }}</span>                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>

                    </div>

                    <div class="flex justify-between">                </svg>            <a href="{{ route('master.mobil.edit', $mobil->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">            <a href="{{ route('master.mobil.edit', $mobil->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

                        <span class="font-medium text-gray-600">Lokasi:</span>

                        <span class="text-gray-900">{{ $mobil->lokasi ?: '-' }}</span>                Kembali

                    </div>

                    <div class="flex justify-between">            </a>                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <span class="font-medium text-gray-600">Merek:</span>

                        <span class="text-gray-900">{{ $mobil->merek ?: '-' }}</span>        </div>

                    </div>

                    <div class="flex justify-between">    </div>                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>

                        <span class="font-medium text-gray-600">Jenis:</span>

                        <span class="text-gray-900">{{ $mobil->jenis ?: '-' }}</span>

                    </div>

                    <div class="flex justify-between">    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">                </svg>                </svg>

                        <span class="font-medium text-gray-600">Tahun Pembuatan:</span>

                        <span class="text-gray-900">{{ $mobil->tahun_pembuatan ?: '-' }}</span>        <!-- Panel Kiri -->

                    </div>

                    <div class="flex justify-between">        <div class="space-y-6">                Edit                Edit

                        <span class="font-medium text-gray-600">Tipe:</span>

                        <span class="text-gray-900">{{ $mobil->tipe ?: '-' }}</span>            <!-- Informasi Dasar -->

                    </div>

                    <div class="flex justify-between">            <div class="bg-gray-50 p-6 rounded-lg">            </a>            </a>

                        <span class="font-medium text-gray-600">Model:</span>

                        <span class="text-gray-900">{{ $mobil->model ?: '-' }}</span>                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Dasar</h3>

                    </div>

                </div>                <div class="space-y-3">            <a href="{{ route('master.mobil.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">            <a href="{{ route('master.mobil.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

            </div>

                    <div class="flex justify-between">

            <!-- Informasi Teknis -->

            <div class="bg-gray-50 p-6 rounded-lg">                        <span class="font-medium text-gray-600">Kode No:</span>                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Teknis</h3>

                <div class="space-y-3">                        <span class="text-gray-900">{{ $mobil->kode_no ?: '-' }}</span>

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Nomor BPKB:</span>                    </div>                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>

                        <span class="text-gray-900">{{ $mobil->bpkb ?: '-' }}</span>

                    </div>                    <div class="flex justify-between">

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Nomor Mesin:</span>                        <span class="font-medium text-gray-600">Nomor Polisi:</span>                </svg>                </svg>

                        <span class="text-gray-900">{{ $mobil->no_mesin ?: '-' }}</span>

                    </div>                        <span class="text-gray-900 font-semibold">{{ $mobil->nomor_polisi ?: '-' }}</span>

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Nomor Rangka:</span>                    </div>                Kembali                Kembali

                        <span class="text-gray-900">{{ $mobil->nomor_rangka ?: '-' }}</span>

                    </div>                    <div class="flex justify-between">

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Atas Nama:</span>                        <span class="font-medium text-gray-600">Lokasi:</span>            </a>            </a>

                        <span class="text-gray-900">{{ $mobil->atas_nama ?: '-' }}</span>

                    </div>                        <span class="text-gray-900">{{ $mobil->lokasi ?: '-' }}</span>

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Silinder:</span>                    </div>        </div>        </div>

                        <span class="text-gray-900">{{ $mobil->silinder ?: '-' }}</span>

                    </div>                    <div class="flex justify-between">

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Bahan Bakar:</span>                        <span class="font-medium text-gray-600">Merek:</span>    </div>    </div>

                        <span class="text-gray-900">{{ $mobil->bahan_bakar ?: '-' }}</span>

                    </div>                        <span class="text-gray-900">{{ $mobil->merek ?: '-' }}</span>

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Warna:</span>                    </div>

                        <span class="text-gray-900">{{ $mobil->warna ?: '-' }}</span>

                    </div>                    <div class="flex justify-between">

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Warna TNKB:</span>                        <span class="font-medium text-gray-600">Jenis:</span>    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        <span class="text-gray-900">{{ $mobil->warna_tnkb ?: '-' }}</span>

                    </div>                        <span class="text-gray-900">{{ $mobil->jenis ?: '-' }}</span>

                </div>

            </div>                    </div>        <!-- Panel Kiri -->        <!-- Informasi Dasar -->



            <!-- Informasi Pembelian -->                    <div class="flex justify-between">

            <div class="bg-gray-50 p-6 rounded-lg">

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Pembelian</h3>                        <span class="font-medium text-gray-600">Tahun Pembuatan:</span>        <div class="space-y-6">        <div class="space-y-6">

                <div class="space-y-3">

                    <div class="flex justify-between">                        <span class="text-gray-900">{{ $mobil->tahun_pembuatan ?: '-' }}</span>

                        <span class="font-medium text-gray-600">Tanggal Beli:</span>

                        <span class="text-gray-900">                    </div>            <!-- Informasi Dasar -->            <div class="bg-gray-50 p-6 rounded-lg">

                            {{ $mobil->tanggal_beli ? \Carbon\Carbon::parse($mobil->tanggal_beli)->format('d/m/Y') : '-' }}

                        </span>                    <div class="flex justify-between">

                    </div>

                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Tipe:</span>            <div class="bg-gray-50 p-6 rounded-lg">                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Dasar</h3>

                        <span class="font-medium text-gray-600">Harga Beli:</span>

                        <span class="text-gray-900">                        <span class="text-gray-900">{{ $mobil->tipe ?: '-' }}</span>

                            {{ $mobil->harga_beli ? 'Rp ' . number_format($mobil->harga_beli, 0, ',', '.') : '-' }}

                        </span>                    </div>                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Dasar</h3>                <div class="space-y-3">

                    </div>

                    <div class="flex justify-between">                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Dealer:</span>

                        <span class="text-gray-900">{{ $mobil->dealer ?: '-' }}</span>                        <span class="font-medium text-gray-600">Model:</span>                <div class="space-y-3">                    <div class="flex justify-between">

                    </div>

                </div>                        <span class="text-gray-900">{{ $mobil->model ?: '-' }}</span>

            </div>

        </div>                    </div>                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Kode No:</span>



        <!-- Panel Kanan -->                </div>

        <div class="space-y-6">

            <!-- Informasi Pajak & Dokumen -->            </div>                        <span class="font-medium text-gray-600">Kode No:</span>                        <span class="text-gray-900">{{ $mobil->kode_no ?: '-' }}</span>

            <div class="bg-gray-50 p-6 rounded-lg">

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Pajak & Dokumen</h3>

                <div class="space-y-3">

                    <div class="flex justify-between">            <!-- Informasi Teknis -->                        <span class="text-gray-900">{{ $mobil->kode_no ?: '-' }}</span>                    </div>

                        <span class="font-medium text-gray-600">Pajak STNK:</span>

                        <span class="text-gray-900 {{ $mobil->pajak_stnk && \Carbon\Carbon::parse($mobil->pajak_stnk)->isPast() ? 'text-red-600 font-semibold' : '' }}">            <div class="bg-gray-50 p-6 rounded-lg">

                            {{ $mobil->pajak_stnk ? \Carbon\Carbon::parse($mobil->pajak_stnk)->format('d/m/Y') : '-' }}

                            @if($mobil->pajak_stnk && \Carbon\Carbon::parse($mobil->pajak_stnk)->isPast())                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Teknis</h3>                    </div>                    <div class="flex justify-between">

                                <span class="text-xs">(Expired)</span>

                            @endif                <div class="space-y-3">

                        </span>

                    </div>                    <div class="flex justify-between">                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Nomor Polisi:</span>

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Pajak Plat:</span>                        <span class="font-medium text-gray-600">Nomor BPKB:</span>

                        <span class="text-gray-900 {{ $mobil->pajak_plat && \Carbon\Carbon::parse($mobil->pajak_plat)->isPast() ? 'text-red-600 font-semibold' : '' }}">

                            {{ $mobil->pajak_plat ? \Carbon\Carbon::parse($mobil->pajak_plat)->format('d/m/Y') : '-' }}                        <span class="text-gray-900">{{ $mobil->bpkb ?: '-' }}</span>                        <span class="font-medium text-gray-600">Nomor Polisi:</span>                        <span class="text-gray-900 font-semibold">{{ $mobil->nomor_polisi ?: '-' }}</span>

                            @if($mobil->pajak_plat && \Carbon\Carbon::parse($mobil->pajak_plat)->isPast())

                                <span class="text-xs">(Expired)</span>                    </div>

                            @endif

                        </span>                    <div class="flex justify-between">                        <span class="text-gray-900 font-semibold">{{ $mobil->nomor_polisi ?: '-' }}</span>                    </div>

                    </div>

                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Nomor Mesin:</span>

                        <span class="font-medium text-gray-600">Nomor KIR:</span>

                        <span class="text-gray-900">{{ $mobil->no_kir ?: '-' }}</span>                        <span class="text-gray-900">{{ $mobil->no_mesin ?: '-' }}</span>                    </div>                    <div class="flex justify-between">

                    </div>

                    <div class="flex justify-between">                    </div>

                        <span class="font-medium text-gray-600">Pajak KIR:</span>

                        <span class="text-gray-900 {{ $mobil->pajak_kir && \Carbon\Carbon::parse($mobil->pajak_kir)->isPast() ? 'text-red-600 font-semibold' : '' }}">                    <div class="flex justify-between">                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Lokasi:</span>

                            {{ $mobil->pajak_kir ? \Carbon\Carbon::parse($mobil->pajak_kir)->format('d/m/Y') : '-' }}

                            @if($mobil->pajak_kir && \Carbon\Carbon::parse($mobil->pajak_kir)->isPast())                        <span class="font-medium text-gray-600">Nomor Rangka:</span>

                                <span class="text-xs">(Expired)</span>

                            @endif                        <span class="text-gray-900">{{ $mobil->nomor_rangka ?: '-' }}</span>                        <span class="font-medium text-gray-600">Lokasi:</span>                        <span class="text-gray-900">{{ $mobil->lokasi ?: '-' }}</span>

                        </span>

                    </div>                    </div>

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Berlaku Sampai:</span>                    <div class="flex justify-between">                        <span class="text-gray-900">{{ $mobil->lokasi ?: '-' }}</span>                    </div>

                        <span class="text-gray-900">

                            {{ $mobil->berlaku_sampai ? \Carbon\Carbon::parse($mobil->berlaku_sampai)->format('d/m/Y') : '-' }}                        <span class="font-medium text-gray-600">Atas Nama:</span>

                        </span>

                    </div>                        <span class="text-gray-900">{{ $mobil->atas_nama ?: '-' }}</span>                    </div>                    <div class="flex justify-between">

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Pajak Sampai:</span>                    </div>

                        <span class="text-gray-900">

                            {{ $mobil->pajak_sampai ? \Carbon\Carbon::parse($mobil->pajak_sampai)->format('d/m/Y') : '-' }}                    <div class="flex justify-between">                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Merek:</span>

                        </span>

                    </div>                        <span class="font-medium text-gray-600">Silinder:</span>

                </div>

            </div>                        <span class="text-gray-900">{{ $mobil->silinder ?: '-' }}</span>                        <span class="font-medium text-gray-600">Merek:</span>                        <span class="text-gray-900">{{ $mobil->merek ?: '-' }}</span>



            <!-- Informasi Asuransi & Lainnya -->                    </div>

            <div class="bg-gray-50 p-6 rounded-lg">

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Asuransi & Lainnya</h3>                    <div class="flex justify-between">                        <span class="text-gray-900">{{ $mobil->merek ?: '-' }}</span>                    </div>

                <div class="space-y-3">

                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Bahan Bakar:</span>

                        <span class="font-medium text-gray-600">Pemakai:</span>

                        <span class="text-gray-900">{{ $mobil->pemakai ?: '-' }}</span>                        <span class="text-gray-900">{{ $mobil->bahan_bakar ?: '-' }}</span>                    </div>                    <div class="flex justify-between">

                    </div>

                    <div class="flex justify-between">                    </div>

                        <span class="font-medium text-gray-600">Asuransi:</span>

                        <span class="text-gray-900">{{ $mobil->asuransi ?: '-' }}</span>                    <div class="flex justify-between">                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Jenis:</span>

                    </div>

                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Warna:</span>

                        <span class="font-medium text-gray-600">Jatuh Tempo Asuransi:</span>

                        <span class="text-gray-900 {{ $mobil->jatuh_tempo_asuransi && \Carbon\Carbon::parse($mobil->jatuh_tempo_asuransi)->isPast() ? 'text-red-600 font-semibold' : '' }}">                        <span class="text-gray-900">{{ $mobil->warna ?: '-' }}</span>                        <span class="font-medium text-gray-600">Jenis:</span>                        <span class="text-gray-900">{{ $mobil->jenis ?: '-' }}</span>

                            {{ $mobil->jatuh_tempo_asuransi ? \Carbon\Carbon::parse($mobil->jatuh_tempo_asuransi)->format('d/m/Y') : '-' }}

                            @if($mobil->jatuh_tempo_asuransi && \Carbon\Carbon::parse($mobil->jatuh_tempo_asuransi)->isPast())                    </div>

                                <span class="text-xs">(Expired)</span>

                            @endif                    <div class="flex justify-between">                        <span class="text-gray-900">{{ $mobil->jenis ?: '-' }}</span>                    </div>

                        </span>

                    </div>                        <span class="font-medium text-gray-600">Warna TNKB:</span>

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Warna Plat:</span>                        <span class="text-gray-900">{{ $mobil->warna_tnkb ?: '-' }}</span>                    </div>                    <div class="flex justify-between">

                        <span class="text-gray-900">

                            @if($mobil->warna_plat)                    </div>

                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 

                                    @if($mobil->warna_plat == 'Hitam') bg-black text-white                </div>                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Tahun Pembuatan:</span>

                                    @elseif($mobil->warna_plat == 'Kuning') bg-yellow-100 text-yellow-800

                                    @elseif($mobil->warna_plat == 'Merah') bg-red-100 text-red-800            </div>

                                    @elseif($mobil->warna_plat == 'Putih') bg-white text-black border border-gray-300

                                    @else bg-gray-100 text-gray-800 @endif">                        <span class="font-medium text-gray-600">Tahun Pembuatan:</span>                        <span class="text-gray-900">{{ $mobil->tahun_pembuatan ?: '-' }}</span>

                                    {{ $mobil->warna_plat }}

                                </span>            <!-- Informasi Pembelian -->

                            @else

                                -            <div class="bg-gray-50 p-6 rounded-lg">                        <span class="text-gray-900">{{ $mobil->tahun_pembuatan ?: '-' }}</span>                    </div>

                            @endif

                        </span>                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Pembelian</h3>

                    </div>

                    @if($mobil->catatan)                <div class="space-y-3">                    </div>                </div>

                        <div class="pt-3 border-t border-gray-200">

                            <span class="font-medium text-gray-600 block mb-2">Catatan:</span>                    <div class="flex justify-between">

                            <div class="bg-white p-3 rounded border text-gray-900 text-sm whitespace-pre-line">{{ $mobil->catatan }}</div>

                        </div>                        <span class="font-medium text-gray-600">Tanggal Beli:</span>                </div>            </div>

                    @endif

                </div>                        <span class="text-gray-900">

            </div>

                            {{ $mobil->tanggal_beli ? \Carbon\Carbon::parse($mobil->tanggal_beli)->format('d/m/Y') : '-' }}            </div>

            <!-- Penugasan Karyawan -->

            <div class="bg-gray-50 p-6 rounded-lg">                        </span>

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Penugasan Karyawan</h3>

                <div class="space-y-3">                    </div>            <!-- Informasi Teknis -->

                    @if($mobil->karyawan)

                        <div class="flex items-center space-x-3">                    <div class="flex justify-between">

                            <div class="flex-shrink-0">

                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">                        <span class="font-medium text-gray-600">Harga Beli:</span>            <!-- Informasi Teknis -->            <div class="bg-gray-50 p-6 rounded-lg">

                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>                        <span class="text-gray-900">

                                    </svg>

                                </div>                            {{ $mobil->harga_beli ? 'Rp ' . number_format($mobil->harga_beli, 0, ',', '.') : '-' }}            <div class="bg-gray-50 p-6 rounded-lg">                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Teknis</h3>

                            </div>

                            <div>                        </span>

                                <p class="text-sm font-medium text-gray-900">{{ $mobil->karyawan->nama_panggilan ?? $mobil->karyawan->nama_lengkap }}</p>

                                @if($mobil->karyawan->nik)                    </div>                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Teknis</h3>                <div class="space-y-3">

                                    <p class="text-sm text-gray-500">NIK: {{ $mobil->karyawan->nik }}</p>

                                @endif                    <div class="flex justify-between">

                                @if($mobil->karyawan->divisi)

                                    <p class="text-sm text-gray-500">{{ $mobil->karyawan->divisi }}</p>                        <span class="font-medium text-gray-600">Dealer:</span>                <div class="space-y-3">                    <div class="flex justify-between">

                                @endif

                            </div>                        <span class="text-gray-900">{{ $mobil->dealer ?: '-' }}</span>

                        </div>

                    @else                    </div>                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Nomor BPKB:</span>

                        <div class="text-center py-4">

                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">                </div>

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>

                            </svg>            </div>                        <span class="font-medium text-gray-600">Nomor BPKB:</span>                        <span class="text-gray-900">{{ $mobil->bpkb ?: '-' }}</span>

                            <p class="mt-2 text-sm text-gray-500">Belum ada karyawan yang ditugaskan</p>

                        </div>        </div>

                    @endif

                </div>                        <span class="text-gray-900">{{ $mobil->bpkb ?: '-' }}</span>                    </div>

            </div>

        </div>        <!-- Panel Kanan -->

    </div>

        <div class="space-y-6">                    </div>                    <div class="flex justify-between">

    <!-- Informasi Metadata -->

    <div class="mt-8 pt-6 border-t border-gray-200">            <!-- Informasi Pajak & Dokumen -->

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">

            <div>            <div class="bg-gray-50 p-6 rounded-lg">                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Nomor Mesin:</span>

                <span class="font-medium">Dibuat pada:</span> 

                {{ $mobil->created_at ? $mobil->created_at->format('d/m/Y H:i') : '-' }}                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Pajak & Dokumen</h3>

            </div>

            <div>                <div class="space-y-3">                        <span class="font-medium text-gray-600">Nomor Mesin:</span>                        <span class="text-gray-900">{{ $mobil->no_mesin ?: '-' }}</span>

                <span class="font-medium">Terakhir diubah:</span> 

                {{ $mobil->updated_at ? $mobil->updated_at->format('d/m/Y H:i') : '-' }}                    <div class="flex justify-between">

            </div>

        </div>                        <span class="font-medium text-gray-600">Pajak STNK:</span>                        <span class="text-gray-900">{{ $mobil->no_mesin ?: '-' }}</span>                    </div>

    </div>

                        <span class="text-gray-900 {{ $mobil->pajak_stnk && \Carbon\Carbon::parse($mobil->pajak_stnk)->isPast() ? 'text-red-600 font-semibold' : '' }}">

    <!-- Action Buttons -->

    <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">                            {{ $mobil->pajak_stnk ? \Carbon\Carbon::parse($mobil->pajak_stnk)->format('d/m/Y') : '-' }}                    </div>                    <div class="flex justify-between">

        <div class="flex space-x-3">

            @can('audit-log-view')                            @if($mobil->pajak_stnk && \Carbon\Carbon::parse($mobil->pajak_stnk)->isPast())

                <button type="button" 

                        class="audit-log-btn inline-flex items-center bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition-colors duration-200"                                <span class="text-xs">(Expired)</span>                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Nomor Rangka:</span>

                        data-model-type="{{ get_class($mobil) }}"

                        data-model-id="{{ $mobil->id }}"                            @endif

                        data-item-name="{{ $mobil->kode_no ?? $mobil->nomor_polisi }}"

                        title="Lihat Riwayat Perubahan">                        </span>                        <span class="font-medium text-gray-600">Nomor Rangka:</span>                        <span class="text-gray-900">{{ $mobil->nomor_rangka ?: '-' }}</span>

                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>                    </div>

                    </svg>

                    Riwayat Perubahan                    <div class="flex justify-between">                        <span class="text-gray-900">{{ $mobil->nomor_rangka ?: '-' }}</span>                    </div>

                </button>

            @endcan                        <span class="font-medium text-gray-600">Pajak Plat:</span>

        </div>

                                <span class="text-gray-900 {{ $mobil->pajak_plat && \Carbon\Carbon::parse($mobil->pajak_plat)->isPast() ? 'text-red-600 font-semibold' : '' }}">                    </div>                    <div class="flex justify-between">

        <div class="flex space-x-3">

            <a href="{{ route('master.mobil.edit', $mobil->id) }}"                             {{ $mobil->pajak_plat ? \Carbon\Carbon::parse($mobil->pajak_plat)->format('d/m/Y') : '-' }}

               class="inline-flex items-center bg-yellow-500 text-white py-2 px-4 rounded-md hover:bg-yellow-600 transition-colors duration-200">

                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">                            @if($mobil->pajak_plat && \Carbon\Carbon::parse($mobil->pajak_plat)->isPast())                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Atas Nama:</span>

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>

                </svg>                                <span class="text-xs">(Expired)</span>

                Edit Mobil

            </a>                            @endif                        <span class="font-medium text-gray-600">Atas Nama:</span>                        <span class="text-gray-900">{{ $mobil->atas_nama ?: '-' }}</span>

            

            <form action="{{ route('master.mobil.destroy', $mobil->id) }}"                         </span>

                  method="POST" 

                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus mobil {{ $mobil->nomor_polisi ?? $mobil->kode_no }}? Tindakan ini tidak dapat dibatalkan.');"                    </div>                        <span class="text-gray-900">{{ $mobil->atas_nama ?: '-' }}</span>                    </div>

                  class="inline">

                @csrf                    <div class="flex justify-between">

                @method('DELETE')

                <button type="submit"                         <span class="font-medium text-gray-600">Nomor KIR:</span>                    </div>                </div>

                        class="inline-flex items-center bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition-colors duration-200">

                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">                        <span class="text-gray-900">{{ $mobil->no_kir ?: '-' }}</span>

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>

                    </svg>                    </div>                    <div class="flex justify-between">            </div>

                    Hapus Mobil

                </button>                    <div class="flex justify-between">

            </form>

        </div>                        <span class="font-medium text-gray-600">Pajak KIR:</span>                        <span class="font-medium text-gray-600">Tipe:</span>        </div>

    </div>

</div>                        <span class="text-gray-900 {{ $mobil->pajak_kir && \Carbon\Carbon::parse($mobil->pajak_kir)->isPast() ? 'text-red-600 font-semibold' : '' }}">



<!-- Include Audit Log Modal -->                            {{ $mobil->pajak_kir ? \Carbon\Carbon::parse($mobil->pajak_kir)->format('d/m/Y') : '-' }}                        <span class="text-gray-900">{{ $mobil->tipe ?: '-' }}</span>

@include('components.audit-log-modal')

                            @if($mobil->pajak_kir && \Carbon\Carbon::parse($mobil->pajak_kir)->isPast())

@endsection
                                <span class="text-xs">(Expired)</span>                    </div>        <!-- Informasi Pajak & Dokumen -->

                            @endif

                        </span>                    <div class="flex justify-between">        <div class="space-y-6">

                    </div>

                    <div class="flex justify-between">                        <span class="font-medium text-gray-600">Model:</span>            <div class="bg-gray-50 p-6 rounded-lg">

                        <span class="font-medium text-gray-600">Berlaku Sampai:</span>

                        <span class="text-gray-900">                        <span class="text-gray-900">{{ $mobil->model ?: '-' }}</span>                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Pajak & Dokumen</h3>

                            {{ $mobil->berlaku_sampai ? \Carbon\Carbon::parse($mobil->berlaku_sampai)->format('d/m/Y') : '-' }}

                        </span>                    </div>                <div class="space-y-3">

                    </div>

                    <div class="flex justify-between">                    <div class="flex justify-between">                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Pajak Sampai:</span>

                        <span class="text-gray-900">                        <span class="font-medium text-gray-600">Silinder:</span>                        <span class="font-medium text-gray-600">Pajak STNK:</span>

                            {{ $mobil->pajak_sampai ? \Carbon\Carbon::parse($mobil->pajak_sampai)->format('d/m/Y') : '-' }}

                        </span>                        <span class="text-gray-900">{{ $mobil->silinder ?: '-' }}</span>                        <span class="text-gray-900 {{ $mobil->pajak_stnk && $mobil->pajak_stnk->isPast() ? 'text-red-600 font-semibold' : '' }}">

                    </div>

                </div>                    </div>                            {{ $mobil->pajak_stnk ? $mobil->pajak_stnk->format('d/m/Y') : '-' }}

            </div>

                    <div class="flex justify-between">                            @if($mobil->pajak_stnk && $mobil->pajak_stnk->isPast())

            <!-- Informasi Asuransi & Lainnya -->

            <div class="bg-gray-50 p-6 rounded-lg">                        <span class="font-medium text-gray-600">Bahan Bakar:</span>                                <span class="text-xs">(Expired)</span>

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Asuransi & Lainnya</h3>

                <div class="space-y-3">                        <span class="text-gray-900">{{ $mobil->bahan_bakar ?: '-' }}</span>                            @endif

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Pemakai:</span>                    </div>                        </span>

                        <span class="text-gray-900">{{ $mobil->pemakai ?: '-' }}</span>

                    </div>                    <div class="flex justify-between">                    </div>

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Asuransi:</span>                        <span class="font-medium text-gray-600">Warna:</span>                    <div class="flex justify-between">

                        <span class="text-gray-900">{{ $mobil->asuransi ?: '-' }}</span>

                    </div>                        <span class="text-gray-900">{{ $mobil->warna ?: '-' }}</span>                        <span class="font-medium text-gray-600">Pajak Plat:</span>

                    <div class="flex justify-between">

                        <span class="font-medium text-gray-600">Jatuh Tempo Asuransi:</span>                    </div>                        <span class="text-gray-900 {{ $mobil->pajak_plat && $mobil->pajak_plat->isPast() ? 'text-red-600 font-semibold' : '' }}">

                        <span class="text-gray-900 {{ $mobil->jatuh_tempo_asuransi && \Carbon\Carbon::parse($mobil->jatuh_tempo_asuransi)->isPast() ? 'text-red-600 font-semibold' : '' }}">

                            {{ $mobil->jatuh_tempo_asuransi ? \Carbon\Carbon::parse($mobil->jatuh_tempo_asuransi)->format('d/m/Y') : '-' }}                    <div class="flex justify-between">                            {{ $mobil->pajak_plat ? $mobil->pajak_plat->format('d/m/Y') : '-' }}

                            @if($mobil->jatuh_tempo_asuransi && \Carbon\Carbon::parse($mobil->jatuh_tempo_asuransi)->isPast())

                                <span class="text-xs">(Expired)</span>                        <span class="font-medium text-gray-600">Warna TNKB:</span>                            @if($mobil->pajak_plat && $mobil->pajak_plat->isPast())

                            @endif

                        </span>                        <span class="text-gray-900">{{ $mobil->warna_tnkb ?: '-' }}</span>                                <span class="text-xs">(Expired)</span>

                    </div>

                    <div class="flex justify-between">                    </div>                            @endif

                        <span class="font-medium text-gray-600">Warna Plat:</span>

                        <span class="text-gray-900">                </div>                        </span>

                            @if($mobil->warna_plat)

                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium             </div>                    </div>

                                    @if($mobil->warna_plat == 'Hitam') bg-gray-100 text-gray-800

                                    @elseif($mobil->warna_plat == 'Kuning') bg-yellow-100 text-yellow-800        </div>                    <div class="flex justify-between">

                                    @elseif($mobil->warna_plat == 'Merah') bg-red-100 text-red-800

                                    @elseif($mobil->warna_plat == 'Putih') bg-gray-100 text-gray-800                        <span class="font-medium text-gray-600">Nomor KIR:</span>

                                    @else bg-gray-100 text-gray-800 @endif">

                                    {{ $mobil->warna_plat }}        <!-- Panel Kanan -->                        <span class="text-gray-900">{{ $mobil->no_kir ?: '-' }}</span>

                                </span>

                            @else        <div class="space-y-6">                    </div>

                                -

                            @endif            <!-- Informasi Pajak & Dokumen -->                    <div class="flex justify-between">

                        </span>

                    </div>            <div class="bg-gray-50 p-6 rounded-lg">                        <span class="font-medium text-gray-600">Pajak KIR:</span>

                    @if($mobil->catatan)

                        <div class="pt-3 border-t border-gray-200">                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Pajak & Dokumen</h3>                        <span class="text-gray-900 {{ $mobil->pajak_kir && $mobil->pajak_kir->isPast() ? 'text-red-600 font-semibold' : '' }}">

                            <span class="font-medium text-gray-600 block mb-2">Catatan:</span>

                            <div class="bg-white p-3 rounded border text-gray-900 text-sm whitespace-pre-line">{{ $mobil->catatan }}</div>                <div class="space-y-3">                            {{ $mobil->pajak_kir ? $mobil->pajak_kir->format('d/m/Y') : '-' }}

                        </div>

                    @endif                    <div class="flex justify-between">                            @if($mobil->pajak_kir && $mobil->pajak_kir->isPast())

                </div>

            </div>                        <span class="font-medium text-gray-600">Pajak STNK:</span>                                <span class="text-xs">(Expired)</span>



            <!-- Penugasan Karyawan -->                        <span class="text-gray-900">                            @endif

            <div class="bg-gray-50 p-6 rounded-lg">

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Penugasan Karyawan</h3>                            {{ $mobil->pajak_stnk ? \Carbon\Carbon::parse($mobil->pajak_stnk)->format('d/m/Y') : '-' }}                        </span>

                <div class="space-y-3">

                    @if($mobil->karyawan)                        </span>                    </div>

                        <div class="flex items-center space-x-3">

                            <div class="flex-shrink-0">                    </div>                </div>

                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">

                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">                    <div class="flex justify-between">            </div>

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>

                                    </svg>                        <span class="font-medium text-gray-600">Pajak Plat:</span>

                                </div>

                            </div>                        <span class="text-gray-900">            <!-- Informasi Asuransi & Lainnya -->

                            <div>

                                <p class="text-sm font-medium text-gray-900">{{ $mobil->karyawan->nama_panggilan ?? $mobil->karyawan->nama_lengkap }}</p>                            {{ $mobil->pajak_plat ? \Carbon\Carbon::parse($mobil->pajak_plat)->format('d/m/Y') : '-' }}            <div class="bg-gray-50 p-6 rounded-lg">

                                @if($mobil->karyawan->nik)

                                    <p class="text-sm text-gray-500">NIK: {{ $mobil->karyawan->nik }}</p>                        </span>                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Asuransi & Lainnya</h3>

                                @endif

                                @if($mobil->karyawan->divisi)                    </div>                <div class="space-y-3">

                                    <p class="text-sm text-gray-500">{{ $mobil->karyawan->divisi }}</p>

                                @endif                    <div class="flex justify-between">                    <div class="flex justify-between">

                            </div>

                        </div>                        <span class="font-medium text-gray-600">Nomor KIR:</span>                        <span class="font-medium text-gray-600">Pemakai:</span>

                    @else

                        <div class="text-center py-4">                        <span class="text-gray-900">{{ $mobil->no_kir ?: '-' }}</span>                        <span class="text-gray-900">{{ $mobil->pemakai ?: '-' }}</span>

                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>                    </div>                    </div>

                            </svg>

                            <p class="mt-2 text-sm text-gray-500">Belum ada karyawan yang ditugaskan</p>                    <div class="flex justify-between">                    <div class="flex justify-between">

                        </div>

                    @endif                        <span class="font-medium text-gray-600">Pajak KIR:</span>                        <span class="font-medium text-gray-600">Asuransi:</span>

                </div>

            </div>                        <span class="text-gray-900">                        <span class="text-gray-900">{{ $mobil->asuransi ?: '-' }}</span>

        </div>

    </div>                            {{ $mobil->pajak_kir ? \Carbon\Carbon::parse($mobil->pajak_kir)->format('d/m/Y') : '-' }}                    </div>



    <!-- Informasi Metadata -->                        </span>                    <div class="flex justify-between">

    <div class="mt-8 pt-6 border-t border-gray-200">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">                    </div>                        <span class="font-medium text-gray-600">Jatuh Tempo Asuransi:</span>

            <div>

                <span class="font-medium">Dibuat pada:</span>                     <div class="flex justify-between">                        <span class="text-gray-900 {{ $mobil->jatuh_tempo_asuransi && $mobil->jatuh_tempo_asuransi->isPast() ? 'text-red-600 font-semibold' : '' }}">

                {{ $mobil->created_at ? $mobil->created_at->format('d/m/Y H:i') : '-' }}

            </div>                        <span class="font-medium text-gray-600">Berlaku Sampai:</span>                            {{ $mobil->jatuh_tempo_asuransi ? $mobil->jatuh_tempo_asuransi->format('d/m/Y') : '-' }}

            <div>

                <span class="font-medium">Terakhir diubah:</span>                         <span class="text-gray-900">                            @if($mobil->jatuh_tempo_asuransi && $mobil->jatuh_tempo_asuransi->isPast())

                {{ $mobil->updated_at ? $mobil->updated_at->format('d/m/Y H:i') : '-' }}

            </div>                            {{ $mobil->berlaku_sampai ? \Carbon\Carbon::parse($mobil->berlaku_sampai)->format('d/m/Y') : '-' }}                                <span class="text-xs">(Expired)</span>

        </div>

    </div>                        </span>                            @endif



    <!-- Action Buttons -->                    </div>                        </span>

    <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">

        <div class="flex space-x-3">                    <div class="flex justify-between">                    </div>

            @can('audit-log-view')

                <button type="button"                         <span class="font-medium text-gray-600">Pajak Sampai:</span>                    <div class="flex justify-between">

                        class="audit-log-btn inline-flex items-center bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition-colors duration-200"

                        data-model-type="{{ get_class($mobil) }}"                        <span class="text-gray-900">                        <span class="font-medium text-gray-600">Warna Plat:</span>

                        data-model-id="{{ $mobil->id }}"

                        data-item-name="{{ $mobil->kode_no ?? $mobil->nomor_polisi }}"                            {{ $mobil->pajak_sampai ? \Carbon\Carbon::parse($mobil->pajak_sampai)->format('d/m/Y') : '-' }}                        <span class="text-gray-900">

                        title="Lihat Riwayat Perubahan">

                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">                        </span>                            @if($mobil->warna_plat)

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>

                    </svg>                    </div>                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 

                    Riwayat Perubahan

                </button>                </div>                                    @if($mobil->warna_plat == 'Hitam') bg-gray-100 text-gray-800

            @endcan

        </div>            </div>                                    @elseif($mobil->warna_plat == 'Kuning') bg-yellow-100 text-yellow-800

        

        <div class="flex space-x-3">                                    @elseif($mobil->warna_plat == 'Merah') bg-red-100 text-red-800

            <a href="{{ route('master.mobil.edit', $mobil->id) }}" 

               class="inline-flex items-center bg-yellow-500 text-white py-2 px-4 rounded-md hover:bg-yellow-600 transition-colors duration-200">            <!-- Informasi Pembelian -->                                    @elseif($mobil->warna_plat == 'Putih') bg-gray-100 text-gray-800

                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>            <div class="bg-gray-50 p-6 rounded-lg">                                    @else bg-gray-100 text-gray-800 @endif">

                </svg>

                Edit Mobil                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Pembelian</h3>                                    {{ $mobil->warna_plat }}

            </a>

                            <div class="space-y-3">                                </span>

            <form action="{{ route('master.mobil.destroy', $mobil->id) }}" 

                  method="POST"                     <div class="flex justify-between">                            @else

                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus mobil {{ $mobil->nomor_polisi ?? $mobil->kode_no }}? Tindakan ini tidak dapat dibatalkan.');"

                  class="inline">                        <span class="font-medium text-gray-600">Tanggal Beli:</span>                                -

                @csrf

                @method('DELETE')                        <span class="text-gray-900">                            @endif

                <button type="submit" 

                        class="inline-flex items-center bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition-colors duration-200">                            {{ $mobil->tanggal_beli ? \Carbon\Carbon::parse($mobil->tanggal_beli)->format('d/m/Y') : '-' }}                        </span>

                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>                        </span>                    </div>

                    </svg>

                    Hapus Mobil                    </div>                    @if($mobil->catatan)

                </button>

            </form>                    <div class="flex justify-between">                        <div class="pt-3 border-t border-gray-200">

        </div>

    </div>                        <span class="font-medium text-gray-600">Harga Beli:</span>                            <span class="font-medium text-gray-600 block mb-2">Catatan:</span>

</div>

                        <span class="text-gray-900">                            <div class="bg-white p-3 rounded border text-gray-900 text-sm whitespace-pre-line">{{ $mobil->catatan }}</div>

<!-- Include Audit Log Modal -->

@include('components.audit-log-modal')                            {{ $mobil->harga_beli ? 'Rp ' . number_format($mobil->harga_beli, 0, ',', '.') : '-' }}                        </div>



@endsection                        </span>                    @endif

                    </div>                </div>

                    <div class="flex justify-between">            </div>

                        <span class="font-medium text-gray-600">Dealer:</span>

                        <span class="text-gray-900">{{ $mobil->dealer ?: '-' }}</span>            <!-- Penugasan Karyawan -->

                    </div>            <div class="bg-gray-50 p-6 rounded-lg">

                </div>                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Penugasan Karyawan</h3>

            </div>                <div class="space-y-3">

                    @if($mobil->karyawan)

            <!-- Informasi Asuransi & Lainnya -->                        <div class="flex items-center space-x-3">

            <div class="bg-gray-50 p-6 rounded-lg">                            <div class="flex-shrink-0">

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Informasi Asuransi & Lainnya</h3>                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">

                <div class="space-y-3">                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <div class="flex justify-between">                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>

                        <span class="font-medium text-gray-600">Pemakai:</span>                                    </svg>

                        <span class="text-gray-900">{{ $mobil->pemakai ?: '-' }}</span>                                </div>

                    </div>                            </div>

                    <div class="flex justify-between">                            <div>

                        <span class="font-medium text-gray-600">Asuransi:</span>                                <p class="text-sm font-medium text-gray-900">{{ $mobil->karyawan->nama_lengkap }}</p>

                        <span class="text-gray-900">{{ $mobil->asuransi ?: '-' }}</span>                                @if($mobil->karyawan->nik)

                    </div>                                    <p class="text-sm text-gray-500">NIK: {{ $mobil->karyawan->nik }}</p>

                    <div class="flex justify-between">                                @endif

                        <span class="font-medium text-gray-600">Jatuh Tempo Asuransi:</span>                                @if($mobil->karyawan->divisi)

                        <span class="text-gray-900">                                    <p class="text-sm text-gray-500">{{ $mobil->karyawan->divisi }}</p>

                            {{ $mobil->jatuh_tempo_asuransi ? \Carbon\Carbon::parse($mobil->jatuh_tempo_asuransi)->format('d/m/Y') : '-' }}                                @endif

                        </span>                            </div>

                    </div>                        </div>

                    <div class="flex justify-between">                    @else

                        <span class="font-medium text-gray-600">Warna Plat:</span>                        <div class="text-center py-4">

                        <span class="text-gray-900">{{ $mobil->warna_plat ?: '-' }}</span>                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    </div>                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>

                    @if($mobil->catatan)                            </svg>

                        <div class="pt-3 border-t border-gray-200">                            <p class="mt-2 text-sm text-gray-500">Belum ada karyawan yang ditugaskan</p>

                            <span class="font-medium text-gray-600 block mb-2">Catatan:</span>                        </div>

                            <div class="bg-white p-3 rounded border text-gray-900 text-sm whitespace-pre-line">{{ $mobil->catatan }}</div>                    @endif

                        </div>                </div>

                    @endif            </div>

                </div>        </div>

            </div>    </div>



            <!-- Penugasan Karyawan -->    <!-- Informasi Metadata -->

            <div class="bg-gray-50 p-6 rounded-lg">    <div class="mt-8 pt-6 border-t border-gray-200">

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Penugasan Karyawan</h3>        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">

                <div class="space-y-3">            <div>

                    @if($mobil->karyawan)                <span class="font-medium">Dibuat pada:</span> 

                        <div class="flex items-center space-x-3">                {{ $mobil->created_at ? $mobil->created_at->format('d/m/Y H:i') : '-' }}

                            <div class="flex-shrink-0">            </div>

                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">            <div>

                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">                <span class="font-medium">Terakhir diubah:</span> 

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>                {{ $mobil->updated_at ? $mobil->updated_at->format('d/m/Y H:i') : '-' }}

                                    </svg>            </div>

                                </div>        </div>

                            </div>    </div>

                            <div></div>

                                <p class="text-sm font-medium text-gray-900">{{ $mobil->karyawan->nama_panggilan ?? $mobil->karyawan->nama_lengkap }}</p>

                                @if($mobil->karyawan->nik)                            </div>@endsection

                                    <p class="text-sm text-gray-500">NIK: {{ $mobil->karyawan->nik }}</p>                            <div>

                                @endif                                <h4 class="font-semibold text-gray-800">{{ $mobil->karyawan->nama_panggilan ?? $mobil->karyawan->nama_lengkap }}</h4>

                                @if($mobil->karyawan->divisi)                                <p class="text-sm text-gray-600">NIK: {{ $mobil->karyawan->nik }}</p>

                                    <p class="text-sm text-gray-500">{{ $mobil->karyawan->divisi }}</p>                                <p class="text-sm text-gray-600">Divisi: {{ $mobil->karyawan->divisi ?? '-' }}</p>

                                @endif                                <p class="text-sm text-gray-600">Jabatan: {{ $mobil->karyawan->jabatan ?? '-' }}</p>

                            </div>                            </div>

                        </div>                        </div>

                    @else                    </div>

                        <div class="text-center py-4">                @else

                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">                    <div class="text-center py-4">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            </svg>                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>

                            <p class="mt-2 text-sm text-gray-500">Belum ada karyawan yang ditugaskan</p>                        </svg>

                        </div>                        <p class="text-gray-500">Belum ada karyawan yang ditugaskan</p>

                    @endif                    </div>

                </div>                @endif

            </div>            </div>

        </div>        </div>

    </div>

        <!-- Panel Kanan - Informasi Tambahan -->

    <!-- Informasi Metadata -->        <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">

    <div class="mt-8 pt-6 border-t border-gray-200">            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

            <div>                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>

                <span class="font-medium">Dibuat pada:</span>                 </svg>

                {{ $mobil->created_at ? $mobil->created_at->format('d/m/Y H:i') : '-' }}                Informasi Tambahan

            </div>            </h3>

            <div>            <div class="space-y-3">

                <span class="font-medium">Terakhir diubah:</span>                 <div class="flex justify-between">

                {{ $mobil->updated_at ? $mobil->updated_at->format('d/m/Y H:i') : '-' }}                    <span class="text-gray-600 font-medium">Status:</span>

            </div>                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">

        </div>                        Aktif

    </div>                    </span>

                </div>

    <!-- Action Buttons -->                <div class="flex justify-between">

    <div class="mt-6 flex justify-end space-x-3">                    <span class="text-gray-600 font-medium">Dibuat:</span>

        @can('audit-log-view')                    <span class="text-gray-800">{{ $mobil->created_at ? $mobil->created_at->format('d M Y H:i') : '-' }}</span>

            <button type="button"                 </div>

                    class="audit-log-btn inline-flex items-center bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition-colors duration-200"                <div class="flex justify-between">

                    data-model-type="{{ get_class($mobil) }}"                    <span class="text-gray-600 font-medium">Terakhir Update:</span>

                    data-model-id="{{ $mobil->id }}"                    <span class="text-gray-800">{{ $mobil->updated_at ? $mobil->updated_at->format('d M Y H:i') : '-' }}</span>

                    data-item-name="{{ $mobil->kode_no ?? $mobil->nomor_polisi }}"                </div>

                    title="Lihat Riwayat Perubahan">                @if($mobil->keterangan)

                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">                    <div class="pt-3 border-t border-yellow-200">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>                        <span class="text-gray-600 font-medium block mb-2">Keterangan:</span>

                </svg>                        <p class="text-gray-800 text-sm bg-white p-3 rounded-md border border-yellow-200">

                Riwayat                            {{ $mobil->keterangan }}

            </button>                        </p>

        @endcan                    </div>

                        @endif

        <form action="{{ route('master.mobil.destroy', $mobil->id) }}"             </div>

              method="POST"         </div>

              onsubmit="return confirm('Apakah Anda yakin ingin menghapus mobil {{ $mobil->nomor_polisi ?? $mobil->kode_no }}?');"    </div>

              class="inline">

            @csrf    <!-- Action Buttons -->

            @method('DELETE')    <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">

            <button type="submit"         <div class="flex space-x-3">

                    class="inline-flex items-center bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition-colors duration-200">            @can('audit-log-view')

                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">                <button type="button" 

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>                        class="audit-log-btn inline-flex items-center bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition-colors duration-200"

                </svg>                        data-model-type="{{ get_class($mobil) }}"

                Hapus                        data-model-id="{{ $mobil->id }}"

            </button>                        data-item-name="{{ $mobil->kode_no ?? $mobil->nomor_polisi }}"

        </form>                        title="Lihat Riwayat Perubahan">

    </div>                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

</div>                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>

                    </svg>

<!-- Include Audit Log Modal -->                    Riwayat Perubahan

@include('components.audit-log-modal')                </button>

            @endcan

@endsection        </div>
        
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