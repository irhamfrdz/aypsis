@extends('layouts.app')

@section('title', 'Detail Tanda Terima - ' . $tandaTerimaTanpaSuratJalan->no_tanda_terima)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Tanda Terima</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $tandaTerimaTanpaSuratJalan->no_tanda_terima }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </a>
                    @can('tanda-terima-tanpa-surat-jalan-update')
                        <a href="{{ route('tanda-terima-tanpa-surat-jalan.edit', $tandaTerimaTanpaSuratJalan) }}"
                           class="inline-flex items-center px-4 py-2 border border-yellow-300 rounded-md text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Detail Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Dasar -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Dasar</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">No. Tanda Terima</label>
                                <p class="text-base font-semibold text-gray-900">{{ $tandaTerimaTanpaSuratJalan->no_tanda_terima }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Tanggal</label>
                                <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->tanggal_tanda_terima->format('d/m/Y') }}</p>
                            </div>
                            @if($tandaTerimaTanpaSuratJalan->nomor_surat_jalan_customer)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">No. Surat Jalan Customer</label>
                                    <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->nomor_surat_jalan_customer }}</p>
                                </div>
                            @endif
                            @if($tandaTerimaTanpaSuratJalan->estimasi_naik_kapal)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Estimasi Naik Kapal</label>
                                    <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->estimasi_naik_kapal }}</p>
                                </div>
                            @endif
                            @if($tandaTerimaTanpaSuratJalan->term && $tandaTerimaTanpaSuratJalan->term->nama_status)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Term</label>
                                    <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->term->nama_status }}</p>
                                </div>
                            @endif
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-2">Status</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Penerima dan Pengirim -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Penerima dan Pengirim</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Penerima</label>
                                <p class="text-base font-semibold text-gray-900">{{ $tandaTerimaTanpaSuratJalan->penerima }}</p>
                                @if($tandaTerimaTanpaSuratJalan->alamat_penerima)
                                    <p class="text-sm text-gray-600 mt-1">{{ $tandaTerimaTanpaSuratJalan->alamat_penerima }}</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Pengirim</label>
                                <p class="text-base font-semibold text-gray-900">{{ $tandaTerimaTanpaSuratJalan->pengirim }}</p>
                                @if($tandaTerimaTanpaSuratJalan->alamat_pengirim)
                                    <p class="text-sm text-gray-600 mt-1">{{ $tandaTerimaTanpaSuratJalan->alamat_pengirim }}</p>
                                @endif
                            </div>
                        </div>
                        @if($tandaTerimaTanpaSuratJalan->pic || $tandaTerimaTanpaSuratJalan->telepon)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                @if($tandaTerimaTanpaSuratJalan->pic)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">PIC (Person In Charge)</label>
                                        <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->pic }}</p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->telepon)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Telepon</label>
                                        <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->telepon }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Barang -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Barang</h2>
                    </div>
                    <div class="p-6">
                        <!-- Legacy single item info (for backward compatibility) -->
                        @if($tandaTerimaTanpaSuratJalan->jenis_barang && $tandaTerimaTanpaSuratJalan->dimensiItems->isEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Jenis Barang</label>
                                    <p class="text-base font-semibold text-gray-900">{{ $tandaTerimaTanpaSuratJalan->jenis_barang }}</p>
                                </div>
                                @if($tandaTerimaTanpaSuratJalan->nama_barang)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Nama Barang</label>
                                        <p class="text-base font-semibold text-gray-900">{{ $tandaTerimaTanpaSuratJalan->nama_barang }}</p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->aktifitas)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Aktifitas</label>
                                        <p class="text-base text-gray-900 capitalize">{{ $tandaTerimaTanpaSuratJalan->aktifitas }}</p>
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Jumlah</label>
                                    <p class="text-base text-gray-900">
                                        {{ number_format($tandaTerimaTanpaSuratJalan->jumlah_barang) }} 
                                        {{ $tandaTerimaTanpaSuratJalan->satuan_barang }}
                                    </p>
                                </div>
                                
                                @if($tandaTerimaTanpaSuratJalan->berat)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Berat</label>
                                        <p class="text-base text-gray-900">
                                            {{ number_format($tandaTerimaTanpaSuratJalan->berat, 2) }} 
                                            {{ $tandaTerimaTanpaSuratJalan->satuan_berat }}
                                        </p>
                                    </div>
                                @endif

                                @if($tandaTerimaTanpaSuratJalan->meter_kubik)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Volume</label>
                                        <p class="text-base text-gray-900">{{ number_format($tandaTerimaTanpaSuratJalan->meter_kubik, 3) }} m³</p>
                                    </div>
                                @endif

                                @if($tandaTerimaTanpaSuratJalan->tonase)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Tonase</label>
                                        <p class="text-base text-gray-900">{{ number_format($tandaTerimaTanpaSuratJalan->tonase, 2) }} Ton</p>
                                    </div>
                                @endif

                                @if($tandaTerimaTanpaSuratJalan->panjang || $tandaTerimaTanpaSuratJalan->lebar || $tandaTerimaTanpaSuratJalan->tinggi)
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Dimensi</label>
                                        <p class="text-base text-gray-900">
                                            @if($tandaTerimaTanpaSuratJalan->panjang)
                                                {{ number_format($tandaTerimaTanpaSuratJalan->panjang, 2) }} m
                                            @endif
                                            @if($tandaTerimaTanpaSuratJalan->lebar)
                                                × {{ number_format($tandaTerimaTanpaSuratJalan->lebar, 2) }} m
                                            @endif
                                            @if($tandaTerimaTanpaSuratJalan->tinggi)
                                                × {{ number_format($tandaTerimaTanpaSuratJalan->tinggi, 2) }} m
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                @if($tandaTerimaTanpaSuratJalan->keterangan_barang)
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Keterangan Barang</label>
                                        <p class="text-base text-gray-900 bg-gray-50 p-3 rounded-md">{{ $tandaTerimaTanpaSuratJalan->keterangan_barang }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Detailed items information -->
                        @if($tandaTerimaTanpaSuratJalan->dimensiItems->isNotEmpty())
                            <div class="mb-6">
                                <h3 class="text-base font-medium text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Detail Barang dan Dimensi
                                </h3>
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Panjang (m)</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lebar (m)</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tinggi (m)</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Volume (m³)</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tonase (Ton)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($tandaTerimaTanpaSuratJalan->dimensiItems as $index => $item)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">{{ $item->nama_barang ?: '-' }}</div>
                                                        @if($item->satuan)
                                                            <div class="text-xs text-gray-500">{{ $item->satuan }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                                        {{ $item->jumlah ? number_format($item->jumlah) : '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                                        {{ $item->panjang ? number_format($item->panjang, 3) : '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                                        {{ $item->lebar ? number_format($item->lebar, 3) : '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                                        {{ $item->tinggi ? number_format($item->tinggi, 3) : '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                                        @if($item->meter_kubik)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                {{ number_format($item->meter_kubik, 3) }} m³
                                                            </span>
                                                        @else
                                                            <span class="text-sm text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                                        @if($item->tonase)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                {{ number_format($item->tonase, 2) }} Ton
                                                            </span>
                                                        @else
                                                            <span class="text-sm text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td colspan="6" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Total:</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-200 text-blue-900">
                                                        {{ number_format($tandaTerimaTanpaSuratJalan->dimensiItems->sum('meter_kubik'), 3) }} m³
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-200 text-green-900">
                                                        {{ number_format($tandaTerimaTanpaSuratJalan->dimensiItems->sum('tonase'), 2) }} Ton
                                                    </span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Summary Information -->
                        @if($tandaTerimaTanpaSuratJalan->dimensiItems->isNotEmpty() || $tandaTerimaTanpaSuratJalan->jenis_barang)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">
                                        {{ $tandaTerimaTanpaSuratJalan->dimensiItems->isNotEmpty() ? 
                                           $tandaTerimaTanpaSuratJalan->dimensiItems->count() : 
                                           ($tandaTerimaTanpaSuratJalan->jumlah_barang ?: 1) }}
                                    </div>
                                    <div class="text-sm text-gray-600">Total Item</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-purple-600">
                                        {{ number_format($tandaTerimaTanpaSuratJalan->dimensiItems->isNotEmpty() ? 
                                           $tandaTerimaTanpaSuratJalan->dimensiItems->sum('meter_kubik') : 
                                           ($tandaTerimaTanpaSuratJalan->meter_kubik ?: 0), 3) }}
                                    </div>
                                    <div class="text-sm text-gray-600">Total Volume (m³)</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">
                                        {{ number_format($tandaTerimaTanpaSuratJalan->dimensiItems->isNotEmpty() ? 
                                           $tandaTerimaTanpaSuratJalan->dimensiItems->sum('tonase') : 
                                           ($tandaTerimaTanpaSuratJalan->tonase ?: 0), 2) }}
                                    </div>
                                    <div class="text-sm text-gray-600">Total Tonase (Ton)</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Kontainer -->
                @if($tandaTerimaTanpaSuratJalan->tipe_kontainer || $tandaTerimaTanpaSuratJalan->no_kontainer || $tandaTerimaTanpaSuratJalan->size_kontainer || $tandaTerimaTanpaSuratJalan->no_seal || $tandaTerimaTanpaSuratJalan->tanggal_seal)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="border-b border-gray-200 p-4">
                            <h2 class="text-lg font-semibold text-gray-800">Informasi Kontainer</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($tandaTerimaTanpaSuratJalan->tipe_kontainer)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Tipe Kontainer</label>
                                        <p class="text-base text-gray-900 uppercase">{{ $tandaTerimaTanpaSuratJalan->tipe_kontainer }}</p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->no_kontainer)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">No. Kontainer</label>
                                        <p class="text-base font-semibold text-gray-900">{{ $tandaTerimaTanpaSuratJalan->no_kontainer }}</p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->size_kontainer)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Size Kontainer</label>
                                        <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->size_kontainer }} ft</p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->no_seal)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">No. Seal</label>
                                        <code class="text-sm bg-gray-100 px-3 py-1 rounded">{{ $tandaTerimaTanpaSuratJalan->no_seal }}</code>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->tanggal_seal)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Tanggal Seal</label>
                                        <p class="text-base text-gray-900">{{ \Carbon\Carbon::parse($tandaTerimaTanpaSuratJalan->tanggal_seal)->format('d M Y') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Informasi Tujuan -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Tujuan</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Tujuan Pengambilan</label>
                                <p class="text-base text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $tandaTerimaTanpaSuratJalan->tujuan_pengambilan }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Tujuan Pengiriman</label>
                                <p class="text-base text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $tandaTerimaTanpaSuratJalan->tujuan_pengiriman }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Transportasi -->
                @if($tandaTerimaTanpaSuratJalan->supir || $tandaTerimaTanpaSuratJalan->kenek || $tandaTerimaTanpaSuratJalan->no_plat)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="border-b border-gray-200 p-4">
                            <h2 class="text-lg font-semibold text-gray-800">Informasi Transportasi</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($tandaTerimaTanpaSuratJalan->supir)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Supir</label>
                                        <p class="text-base text-gray-900 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $tandaTerimaTanpaSuratJalan->supir }}
                                        </p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->kenek)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Kenek</label>
                                        <p class="text-base text-gray-900 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $tandaTerimaTanpaSuratJalan->kenek }}
                                        </p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->no_plat)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">No. Plat</label>
                                        <code class="text-sm bg-gray-100 px-3 py-1 rounded">{{ $tandaTerimaTanpaSuratJalan->no_plat }}</code>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Catatan -->
                @if($tandaTerimaTanpaSuratJalan->catatan)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="border-b border-gray-200 p-4">
                            <h2 class="text-lg font-semibold text-gray-800">Catatan</h2>
                        </div>
                        <div class="p-6">
                            <p class="text-base text-gray-900 bg-gray-50 p-4 rounded-md">{{ $tandaTerimaTanpaSuratJalan->catatan }}</p>
                        </div>
                    </div>
                @endif

                <!-- Gambar Tanda Terima -->
                @php
                    $__gambarArray = $tandaTerimaTanpaSuratJalan->gambar_tanda_terima;
                    if (is_string($__gambarArray)) {
                        // Try to decode JSON string
                        $__decoded = json_decode($__gambarArray, true);
                        $__gambarArray = is_array($__decoded) ? $__decoded : [];
                    }
                    if (!is_array($__gambarArray)) {
                        $__gambarArray = [];
                    }
                @endphp
                @if(!empty($__gambarArray) && count($__gambarArray))
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="border-b border-gray-200 p-4">
                            <h2 class="text-lg font-semibold text-gray-800">Gambar Tanda Terima</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-sm text-gray-600">Terdapat {{ count($__gambarArray) }} gambar</div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach($__gambarArray as $index => $imagePath)
                                    @php
                                        // If stored path is a full URL, use it directly; otherwise prefix with storage asset path.
                                        if (is_string($imagePath) && preg_match('/^https?:\/\//', $imagePath)) {
                                            $imgUrl = $imagePath;
                                        } else {
                                            $imgUrl = asset('storage/' . ltrim($imagePath, '/'));
                                        }
                                    @endphp
                                    <div class="rounded-md overflow-hidden bg-gray-50 border border-gray-100 group">
                                            <button type="button" onclick="openImageModal(@json($imgUrl))" class="w-full h-32 sm:h-40 lg:h-36 flex items-center justify-center bg-gray-100">
                                            <img src="{{ $imgUrl }}" alt="Gambar Tanda Terima {{ $index + 1 }}" class="object-cover w-full h-full" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/image-placeholder.png') }}';"/>
                                        </button>
                                        <div class="p-2 flex items-center justify-between">
                                            <div class="text-xs text-gray-600">Gambar {{ $index + 1 }}</div>
                                            <div class="flex gap-2 items-center">
                                                <a href="{{ route('tanda-terima-tanpa-surat-jalan.download-image', [$tandaTerimaTanpaSuratJalan, $index]) }}" class="inline-flex items-center px-2 py-1 text-xs bg-white border rounded text-gray-700 hover:bg-gray-50">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l4-4m-4 4l-4-4M21 12v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8"></path></svg>
                                                    Unduh
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Sistem</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat Oleh</label>
                            <p class="text-sm text-gray-900">{{ $tandaTerimaTanpaSuratJalan->creator->name ?? 'System' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                            <p class="text-sm text-gray-900">{{ $tandaTerimaTanpaSuratJalan->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($tandaTerimaTanpaSuratJalan->updated_by)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diupdate</label>
                                <p class="text-sm text-gray-900">{{ $tandaTerimaTanpaSuratJalan->updater->name ?? 'System' }}</p>
                                <p class="text-xs text-gray-500">{{ $tandaTerimaTanpaSuratJalan->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="border-t border-gray-200 pt-4 space-y-3">
                            @can('tanda-terima-tanpa-surat-jalan-update')
                                <a href="{{ route('tanda-terima-tanpa-surat-jalan.edit', $tandaTerimaTanpaSuratJalan) }}"
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-yellow-300 rounded-md text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Tanda Terima
                                </a>
                            @endcan
                            
                            @can('tanda-terima-tanpa-surat-jalan-delete')
                                <form action="{{ route('tanda-terima-tanpa-surat-jalan.destroy', $tandaTerimaTanpaSuratJalan) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda terima ini? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus Tanda Terima
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Image Modal (hidden by default) -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-75 p-4">
        <button id="imageModalClose" aria-label="Tutup" onclick="closeImageModal()" class="absolute top-6 right-6 text-white bg-black bg-opacity-40 hover:bg-opacity-60 rounded-full p-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div id="imageModalContent" class="max-w-5xl max-h-full overflow-hidden rounded-md">
            <img id="imageModalImg" src="" alt="Gambar Tanda Terima" class="w-full h-auto max-h-[80vh] object-contain rounded-md" />
        </div>
    </div>

    <script>
        function openImageModal(src) {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('imageModalImg');
            img.src = src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('imageModalImg');
            img.src = '';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        // Close modal when clicking outside the image
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('imageModal');
            const content = document.getElementById('imageModalContent');
            if (!modal.classList.contains('hidden')) {
                if (e.target === modal) {
                    closeImageModal();
                }
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('imageModal');
                if (!modal.classList.contains('hidden')) {
                    closeImageModal();
                }
            }
        });
    </script>
</div>
@endsection
