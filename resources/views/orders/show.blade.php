@extends('layouts.app')

@section('title', 'Detail Order')
@section('page_title', 'Detail Order')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Order</h1>
                    <p class="mt-1 text-sm text-gray-600">Nomor Order: {{ $order->nomor_order }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('orders.edit', $order) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="space-y-6">

                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Order</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->nomor_order }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Order</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->tanggal_order->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No Tiket/DO</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->no_tiket_do ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($order->status === 'draft') bg-gray-100 text-gray-800
                                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'completed') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Destination Information -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tujuan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tujuan Kirim</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->tujuan_kirim }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tujuan Ambil</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->tujuan_ambil }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Term</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->term->nama_status ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pengirim</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->pengirim->nama_pengirim ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Barang</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->jenisBarang->nama_barang ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Container Information -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kontainer</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Size Kontainer</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->size_kontainer }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit Kontainer</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->unit_kontainer }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipe Kontainer</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($order->tipe_kontainer) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Pickup</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->tanggal_pickup ? $order->tanggal_pickup->format('d/m/Y') : '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Document Types -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tipe Dokumen</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- FTZ03 Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">FTZ03</label>
                            <div class="text-sm text-gray-900">
                                @if($order->exclude_ftz03)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Exclude FTZ03
                                    </span>
                                @elseif($order->include_ftz03)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Include FTZ03
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Tidak ada
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- SPPB Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SPPB</label>
                            <div class="text-sm text-gray-900">
                                @if($order->exclude_sppb)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Exclude SPPB
                                    </span>
                                @elseif($order->include_sppb)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Include SPPB
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Tidak ada
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Buruh Bongkar Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buruh Bongkar</label>
                            <div class="text-sm text-gray-900">
                                @if($order->exclude_buruh_bongkar)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Exclude Buruh Bongkar
                                    </span>
                                @elseif($order->include_buruh_bongkar)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Include Buruh Bongkar
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Tidak ada
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($order->catatan)
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan</h3>
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $order->catatan }}</p>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
