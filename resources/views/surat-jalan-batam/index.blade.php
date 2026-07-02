@extends('layouts.app')

@section('title', 'Surat Jalan Batam')
@section('page_title', 'Surat Jalan Batam')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Surat Jalan Batam</h1>
                    <p class="mt-1 text-sm text-gray-600">Kelola data Surat Jalan Batam pengiriman barang</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('surat-jalan-batam.select-order') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Surat Jalan Batam
                    </a>
                    <a href="{{ route('surat-jalan-batam.create') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Surat Jalan Batam Tanpa Order
                    </a>
                    <button type="button" onclick="openBulkModal()"
                            class="inline-flex items-center justify-center px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Tambah Massal
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('surat-jalan-batam.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-4">
                    <!-- Pencarian -->
                    <div class="md:col-span-2 lg:col-span-2 xl:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Pencarian
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="No. Surat Jalan Batam, Pengirim, No. Kontainer, Tujuan..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Status Pembayaran -->
                    <div>
                        <label for="status_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                        <select name="status_pembayaran" id="status_pembayaran" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="all" {{ request('status_pembayaran') == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="belum_masuk_pranota" {{ request('status_pembayaran') == 'belum_masuk_pranota' ? 'selected' : '' }}>Belum Masuk Pranota</option>
                            <option value="belum_dibayar" {{ request('status_pembayaran') == 'belum_dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                            <option value="sudah_dibayar" {{ request('status_pembayaran') == 'sudah_dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                        </select>
                    </div>

                    <!-- Tipe Kontainer -->
                    <div>
                        <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer</label>
                        <select name="tipe_kontainer" id="tipe_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="" {{ request('tipe_kontainer') == '' ? 'selected' : '' }}>Semua Tipe</option>
                            <option value="Dry Container" {{ request('tipe_kontainer') == 'Dry Container' ? 'selected' : '' }}>Dry Container</option>
                            <option value="High Cube" {{ request('tipe_kontainer') == 'High Cube' ? 'selected' : '' }}>High Cube</option>
                            <option value="Reefer" {{ request('tipe_kontainer') == 'Reefer' ? 'selected' : '' }}>Reefer</option>
                            <option value="Open Top" {{ request('tipe_kontainer') == 'Open Top' ? 'selected' : '' }}>Open Top</option>
                            <option value="Flat Rack" {{ request('tipe_kontainer') == 'Flat Rack' ? 'selected' : '' }}>Flat Rack</option>
                            <option value="HC" {{ request('tipe_kontainer') == 'HC' ? 'selected' : '' }}>HC</option>
                            <option value="STD" {{ request('tipe_kontainer') == 'STD' ? 'selected' : '' }}>STD</option>
                            <option value="RF" {{ request('tipe_kontainer') == 'RF' ? 'selected' : '' }}>RF</option>
                            <option value="OT" {{ request('tipe_kontainer') == 'OT' ? 'selected' : '' }}>OT</option>
                            <option value="FR" {{ request('tipe_kontainer') == 'FR' ? 'selected' : '' }}>FR</option>
                            <option value="cargo" {{ request('tipe_kontainer') == 'cargo' ? 'selected' : '' }}>Cargo</option>
                        </select>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Tanggal Akhir -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filter
                    </button>
                    
                    @if(request()->hasAny(['search', 'status', 'status_pembayaran', 'tipe_kontainer', 'start_date', 'end_date']))
                        <a href="{{ route('surat-jalan-batam.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset
                        </a>
                    @endif

                    @can('surat-jalan-export')
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Excel
                    </a>
                    @endcan
                </div>
            </form>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Daftar Surat Jalan Batam</h3>
                <p class="mt-1 text-sm text-gray-600">Total: {{ $suratJalans->total() }} Surat Jalan Batam</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs resizable-table" id="suratJalanTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="resizable-th px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16 sticky left-0 bg-gray-50 z-10" style="position: sticky;">Actions<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20" style="position: relative;">Order<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24" style="position: relative;">No. SJ<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20" style="position: relative;">Tanggal<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24" style="position: relative;">Pengirim<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28" style="position: relative;">Tujuan Ambil<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28" style="position: relative;">Tujuan Kirim<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20" style="position: relative;">Barang<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20" style="position: relative;">Tipe<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24" style="position: relative;">Kontainer<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20" style="position: relative;">No. Plat<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20" style="position: relative;">Supir<div class="resize-handle"></div></th>      
                            
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20" style="position: relative;">Status<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28" style="position: relative;">Pembayaran<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28" style="position: relative;">Pranota Vendor<div class="resize-handle"></div></th>
                            <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suratJalans as $suratJalan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-2 whitespace-nowrap text-center sticky left-0 bg-white z-10">
                                <div class="relative inline-block text-left z-50">
                                    <button type="button" onclick="toggleDropdown(event, 'dropdown-{{ $suratJalan->id }}')"
                                            class="inline-flex items-center justify-center w-6 h-6 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-indigo-500 transition-colors duration-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                    
                                    <div id="dropdown-{{ $suratJalan->id }}" class="hidden absolute left-0 top-full z-50 mt-1 min-w-max max-w-xs rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                                        <div class="py-1">
                                            {{-- Tombol Edit - selalu tersedia --}}
                                                          <a href="{{ route('surat-jalan-batam.edit', $suratJalan->id) }}"
                                                              class="group flex items-center px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 hover:text-gray-900 whitespace-nowrap">
                                                                <svg class="mr-2 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </a>

                                            {{-- Tombol Cancel - untuk membatalkan Surat Jalan Batam --}}
                                            <button onclick="event.stopPropagation(); updateStatus('{{ $suratJalan->id }}', 'cancelled')"
                                                    class="group flex items-center w-full px-3 py-1.5 text-xs text-red-700 hover:bg-red-50 hover:text-red-900 whitespace-nowrap">
                                                <svg class="mr-2 h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Cancel
                                            </button>

                                            {{-- Tombol Print --}}
                                                          <a href="{{ route('surat-jalan-batam.print', $suratJalan->id) }}"
                                                              target="_blank"
                                                              class="group flex items-center px-3 py-1.5 text-xs text-blue-700 hover:bg-blue-50 hover:text-blue-900 whitespace-nowrap">
                                                <svg class="mr-2 h-4 w-4 text-blue-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Print SJ
                                            </a>

                                            {{-- Tombol Memo --}}
                                                          <a href="{{ route('surat-jalan-batam.print-memo', $suratJalan->id) }}"
                                                              target="_blank"
                                                              class="group flex items-center px-3 py-1.5 text-xs text-green-700 hover:bg-green-50 hover:text-green-900 whitespace-nowrap">
                                                <svg class="mr-2 h-4 w-4 text-green-400 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                Memo
                                            </a>

                                            {{-- Tombol Pre Printed --}}
                                                <button onclick="event.stopPropagation(); printPreprinted('{{ $suratJalan->id }}')"
                                                    class="group flex items-center w-full px-3 py-1.5 text-xs text-purple-700 hover:bg-purple-50 hover:text-purple-900 whitespace-nowrap">
                                                <svg class="mr-2 h-4 w-4 text-purple-400 group-hover:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                </svg>
                                                Pre Printed
                                            </button>

                                            {{-- Tombol Tagihan Vendor --}}
                                            @can('tagihan-supir-vendor-create')
                                                @if(!\App\Models\TagihanSupirVendor::where('surat_jalan_id', $suratJalan->id)->exists())
                                                    <form action="{{ route('tagihan-supir-vendor.create') }}" method="GET" class="inline w-full">
                                                        <input type="hidden" name="surat_jalan_id" value="{{ $suratJalan->id }}">
                                                        <button type="submit"
                                                            onclick="event.stopPropagation();"
                                                            class="group flex items-center w-full px-3 py-1.5 text-xs text-yellow-700 hover:bg-yellow-50 hover:text-yellow-900 whitespace-nowrap">
                                                            <svg class="mr-2 h-4 w-4 text-yellow-500 group-hover:text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            Buat Tagihan Vendor
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan

                                            {{-- Tombol Delete --}}
                                            <div class="border-t border-gray-100"></div>
                                            <form action="{{ route('surat-jalan-batam.destroy', $suratJalan->id) }}" method="POST" class="inline w-full">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="event.stopPropagation(); return confirm('Yakin ingin menghapus Surat Jalan Batam ini?')"
                                                    class="group flex items-center w-full px-3 py-1.5 text-xs text-red-700 hover:bg-red-50 hover:text-red-900 whitespace-nowrap">
                                                    <svg class="mr-2 h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-2 text-xs font-medium text-gray-900">
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->orderBatam ? $suratJalan->orderBatam->nomor_order : '-' }}">
                                    {{ $suratJalan->orderBatam ? $suratJalan->orderBatam->nomor_order : '-' }}
                                </div>
                            </td>
                            <td class="px-2 py-2 text-xs font-medium">
                                <a href="{{ route('surat-jalan-batam.print', $suratJalan->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 hover:underline font-medium overflow-hidden text-ellipsis block"
                                   title="{{ $suratJalan->no_surat_jalan }} - Klik untuk print"
                                   target="_blank">
                                    {{ $suratJalan->no_surat_jalan }}
                                </a>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                                <div title="{{ $suratJalan->formatted_tanggal_surat_jalan }}">
                                    {{ date('d/m/Y', strtotime($suratJalan->tanggal_surat_jalan)) }}
                                </div>
                            </td>
                            <td class="px-2 py-2 text-xs text-gray-900">
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->pengirim ?? '-' }}">
                                    {{ $suratJalan->pengirim ?? '-' }}
                                </div>
                            </td>
                            <td class="px-2 py-2 text-xs text-gray-900">
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->tujuan_pengambilan ?? '-' }}">
                                    {{ $suratJalan->tujuan_pengambilan ?? '-' }}
                                </div>
                            </td>
                            <td class="px-2 py-2 text-xs text-gray-900">
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->tujuan_pengiriman ?? '-' }}">
                                    {{ $suratJalan->tujuan_pengiriman ?? '-' }}
                                </div>
                            </td>
                            <td class="px-2 py-2 text-xs text-gray-900">
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->jenis_barang ?? '-' }}">
                                    {{ $suratJalan->jenis_barang ?? '-' }}
                                </div>
                            </td>
                            <td class="px-2 py-2 text-xs text-gray-900">
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->tipe_kontainer ?? '-' }}">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $suratJalan->tipe_kontainer ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-2 py-2 text-xs font-mono text-gray-900">
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->no_kontainer ?? '-' }}">
                                    {{ $suratJalan->no_kontainer ?? '-' }}
                                </div>
                            </td>
                            <td class="px-2 py-2 text-xs text-gray-900">
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->no_plat ?? '-' }}">
                                    {{ $suratJalan->no_plat ?? '-' }}
                                </div>
                            </td>
                            <td class="px-2 py-2 text-xs text-gray-900">
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->supir ?? '-' }}">
                                    {{ $suratJalan->supir ?? '-' }}
                                </div>
                            </td> 
                            <td class="px-2 py-2 whitespace-nowrap">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $suratJalan->status_badge }}">
                                    {{ ucfirst($suratJalan->status) }}
                                </span>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap">
                                @php
                                    $overallStatus = $suratJalan->overall_status_pembayaran;
                                @endphp
                                @if($overallStatus)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                        @if($overallStatus == 'sudah_dibayar')
                                            bg-green-100 text-green-800
                                        @elseif($overallStatus == 'belum_dibayar')
                                            bg-yellow-100 text-yellow-800
                                        @else
                                            bg-blue-100 text-blue-800
                                        @endif">
                                        @if($overallStatus == 'sudah_dibayar')
                                            Dibayar
                                        @elseif($overallStatus == 'belum_dibayar')
                                            Belum Bayar
                                        @else
                                            Belum Pranota
                                        @endif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        N/A
                                    </span>
                                @endif
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap">
                                @php
                                    $vendorStatus = $suratJalan->vendor_invoice_status;
                                @endphp
                                @if($vendorStatus == 'sudah_pranota')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800" title="No Invoice: {{ $suratJalan->tagihanSupirVendor->invoice->no_invoice ?? '-' }}">
                                        Sudah Pranota
                                    </span>
                                @elseif($vendorStatus == 'sudah_invoice')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800" title="No Invoice: {{ $suratJalan->tagihanSupirVendor->invoice->no_invoice ?? '-' }}">
                                        Sudah Invoice
                                    </span>
                                @elseif($vendorStatus == 'sudah_tagihan')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Sudah Tagihan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 text-center block w-full">
                                        -
                                    </span>
                                @endif
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- Tombol Detail --}}
                                                <a href="{{ route('surat-jalan-batam.show', $suratJalan->id) }}"
                                                    class="text-blue-600 hover:text-blue-900 transition-colors duration-200 whitespace-nowrap"
                                       title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    {{-- Tombol Edit --}}
                                                <a href="{{ route('surat-jalan-batam.edit', $suratJalan->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200 whitespace-nowrap"
                                       title="Edit Surat Jalan Batam">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    {{-- Tombol Print --}}
                                    @can('surat-jalan-batam-print')
                                    <a href="{{ route('surat-jalan-batam.print', $suratJalan->id) }}"
                                                     class="text-green-600 hover:text-green-900 transition-colors duration-200 whitespace-nowrap"
                                        title="Print Surat Jalan Batam"
                                        target="_blank">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </a>
                                    @endcan

                                    {{-- Tombol Audit (hanya untuk user dengan permission) --}}
                                    @can('audit-log-view')
                                        <button type="button"
                                            onclick="showAuditLog('{{ get_class($suratJalan) }}', '{{ $suratJalan->id }}', '{{ $suratJalan->no_surat_jalan }}')"
                                            class="text-purple-600 hover:text-purple-900 transition-colors duration-200 whitespace-nowrap"
                                            title="Lihat Audit Log">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    @endcan

                                    {{-- Tombol Delete --}}
                                    <form action="{{ route('surat-jalan-batam.destroy', $suratJalan->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus Surat Jalan Batam ini?')"
                                            class="text-red-600 hover:text-red-900 transition-colors duration-200 whitespace-nowrap"
                                                title="Hapus Surat Jalan Batam">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="16" class="px-6 py-8 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-500 text-base">Belum ada data Surat Jalan Batam</p>
                                    <p class="text-gray-400 text-sm mt-1">Tambah Surat Jalan Batam pertama untuk memulai</p>
                                    <a href="{{ route('surat-jalan-batam.select-order') }}" class="mt-3 text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                        Buat Surat Jalan Batam pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($suratJalans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                @include('components.modern-pagination', ['paginator' => $suratJalans])
                @include('components.rows-per-page')
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

<script>
// Toggle dropdown menu
        function toggleDropdown(event, dropdownId) {
            event.stopPropagation();
            // Close all other dropdowns first and move them back to their original parent
            document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                if (dropdown.id !== dropdownId) {
                    dropdown.classList.add('hidden');
                    dropdown.style.position = '';
                    dropdown.style.left = '';
                    dropdown.style.top = '';
                    dropdown.style.zIndex = '';
                    dropdown.style.minWidth = '';
                    if (dropdown.__origParent) {
                        dropdown.__origParent.appendChild(dropdown);
                        dropdown.__origParent = null;
                    }
                }
            });

            // Toggle the clicked dropdown
            const dropdown = document.getElementById(dropdownId);
            const button = dropdown.parentElement.querySelector('button');
            const isHidden = dropdown.classList.contains('hidden');

            if (isHidden) {
                // Move dropdown to body to avoid clipping by overflow/scrolling parents
                if (!dropdown.__origParent) {
                    dropdown.__origParent = dropdown.parentElement;
                    document.body.appendChild(dropdown);
                }

                // Show dropdown first so we can measure its natural size
                dropdown.classList.remove('hidden');

                // Reset inline styles used for previous positioning
                dropdown.style.position = 'fixed';
                dropdown.style.left = '0px';
                dropdown.style.top = '0px';
                dropdown.style.zIndex = '9999';
                dropdown.style.minWidth = '';

                // Force reflow to get correct measurements
                void dropdown.offsetWidth;

                // Measure natural width and height
                const naturalWidth = Math.max(dropdown.scrollWidth || 0, dropdown.offsetWidth || 0);
                const naturalHeight = dropdown.offsetHeight || 0;

                // Get button position
                const rect = button.getBoundingClientRect();
                let left = rect.left;
                // Prevent horizontal overflow (8px padding from edge)
                if (left + naturalWidth + 8 > window.innerWidth) {
                    left = Math.max(window.innerWidth - naturalWidth - 8, 8);
                }

                // Prefer showing below the button; if not enough space, show above
                let top = rect.bottom + 4; // 4px offset
                if (top + naturalHeight + 8 > window.innerHeight) {
                    top = Math.max(rect.top - naturalHeight - 4, 8);
                }

                dropdown.style.left = left + 'px';
                dropdown.style.top = top + 'px';
                dropdown.style.minWidth = Math.max(naturalWidth, rect.width) + 'px';
            } else {
                dropdown.classList.add('hidden');
                dropdown.style.position = '';
                dropdown.style.left = '';
                dropdown.style.top = '';
                dropdown.style.zIndex = '';
                dropdown.style.minWidth = '';
                if (dropdown.__origParent) {
                    dropdown.__origParent.appendChild(dropdown);
                    dropdown.__origParent = null;
                }
            }
        }

// Update status function
function updateStatus(suratJalanId, status) {
    if (confirm('Yakin ingin mengubah status Surat Jalan Batam ini?')) {
        fetch(`/surat-jalan-batam/${suratJalanId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal mengubah status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengubah status');
        });
    }
}

// Print preprinted function
function printPreprinted(suratJalanId) {
    window.open(`/surat-jalan-batam/${suratJalanId}/print-preprinted`, '_blank');
}

    <!-- Modal Buat Surat Jalan Batam Massal -->
    <div id="modalBuatSuratJalanMassal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-5 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-lg bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Buat Surat Jalan Batam Massal</h3>
                </div>
                <button type="button" onclick="closeBulkModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4 max-h-[80vh] overflow-y-auto px-1">
                <!-- Alert area for bulk modal -->
                <div id="bulkModalAlertArea"></div>

                <!-- Shared Fields -->
                <div class="bg-gray-50 p-4 rounded-xl mb-6 border border-gray-150">
                    <h4 class="text-sm font-bold text-gray-800 mb-3 uppercase tracking-wider">Shared Fields / Default Value</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Surat Jalan</label>
                            <input type="date" id="bulk_tanggal_surat_jalan" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Term</label>
                            <select id="bulk_term" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="">-- Pilih Term --</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->kode }}">{{ $term->kode }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Pengirim</label>
                            <input type="text" id="bulk_pengirim" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Penerima</label>
                            <input type="text" id="bulk_penerima" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat</label>
                            <input type="text" id="bulk_alamat" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tujuan Pengambilan</label>
                            <input type="text" id="bulk_tujuan_pengambilan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tujuan Pengiriman</label>
                            <select id="bulk_tujuan_pengiriman" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="">-- Pilih Tujuan Pengiriman --</option>
                                @foreach($pricelistRings as $pl)
                                    <option value="{{ $pl['value'] }}" data-rates="{{ json_encode($pl['rates']) }}" data-rates-prev="{{ json_encode($pl['rates_prev']) }}" data-ring="{{ $pl['ring'] }}">
                                        {{ $pl['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Guide -->
                <div class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                    <h4 class="text-sm font-semibold text-indigo-800 mb-2">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Panduan Format Data (Semicolon-separated / Dipisahkan Titik Koma)
                    </h4>
                    <p class="text-xs text-indigo-700 mb-1 font-semibold">Format baris data:</p>
                    <div class="bg-white rounded px-3 py-2 text-xs text-indigo-900 font-mono overflow-x-auto border border-indigo-100">
                        No SJ ; No Kontainer ; No Seal ; Size (20FT/40FT) ; Tipe ; F/E (Full/Empty) ; Supir ; No Plat ; Kenek ; Krani ; Jenis Barang ; Tujuan Pengiriman ; Tanggal
                    </div>
                    <p class="text-xs text-indigo-600 mt-1">
                        <strong>Contoh:</strong> SJB/2026/06/0001;CONT123456;SEAL123;20FT;Dry Container;Full;Supir Andi;BP 1234 XX;Kenek Budi;Krani Cici;Beras;Batu Ampar;2026-07-02
                    </p>
                </div>

                <!-- Textarea Input -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Data Surat Jalan <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-400 ml-2 font-normal">(setiap baris dipisahkan enter, kolom dipisahkan titik koma)</span>
                    </label>
                    <textarea id="bulkTextarea" rows="8"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Ketik atau tempel data di sini..."></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 mb-4">
                    <button type="button" onclick="parseBulkData()"
                            class="inline-flex items-center px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-800 text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Parse & Preview
                    </button>
                    <span id="bulkParseInfo" class="text-sm text-gray-500"></span>
                </div>

                <!-- Preview Table -->
                <div id="bulkPreviewContainer" class="hidden mb-4">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Preview Data</h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-xs" id="bulkPreviewTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">No SJ</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">No Kontainer</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">No Seal</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">F/E</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">No Plat</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-500 uppercase tracking-wider">Est. Uang Jalan</th>
                                </tr>
                            </thead>
                            <tbody id="bulkPreviewBody" class="bg-white divide-y divide-gray-100">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 mt-4 pt-3 border-t">
                <button type="button" onclick="closeBulkModal()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <button type="button" id="btnSubmitBulk" onclick="submitBulkSuratJalan()" disabled
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <span id="btnBulkSubmitText">Simpan Semua</span>
                    <span id="btnBulkSubmitLoading" class="hidden">
                        <svg class="animate-spin h-4 w-4 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>

<script>
// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative.inline-block') && !event.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
            dropdown.style.position = '';
            dropdown.style.left = '';
            dropdown.style.top = '';
            dropdown.style.zIndex = '';
            dropdown.style.minWidth = '';
            if (dropdown.__origParent) {
                dropdown.__origParent.appendChild(dropdown);
                dropdown.__origParent = null;
            }
        });
    }
});

// Bulk Creation functions
let bulkParsedRows = [];

function openBulkModal() {
    document.getElementById('modalBuatSuratJalanMassal').classList.remove('hidden');
    document.getElementById('bulkTextarea').value = '';
    document.getElementById('bulkPreviewContainer').classList.add('hidden');
    document.getElementById('bulkPreviewBody').innerHTML = '';
    document.getElementById('bulkParseInfo').textContent = '';
    document.getElementById('btnSubmitBulk').disabled = true;
    document.getElementById('bulkModalAlertArea').innerHTML = '';
    bulkParsedRows = [];
}

function closeBulkModal() {
    document.getElementById('modalBuatSuratJalanMassal').classList.add('hidden');
    bulkParsedRows = [];
}

function showBulkAlert(title, text, type) {
    const colorClasses = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-amber-50 border-amber-200 text-amber-800'
    };
    
    document.getElementById('bulkModalAlertArea').innerHTML = `
        <div class="mb-4 p-4 border rounded-lg ${colorClasses[type] || 'bg-gray-50 border-gray-200 text-gray-800'}">
            <strong class="font-bold">${title}</strong>
            <p class="text-sm mt-1">${text}</p>
        </div>
    `;
}

function parseBulkData() {
    const textarea = document.getElementById('bulkTextarea');
    const rawText = textarea.value.trim();

    if (!rawText) {
        showBulkAlert('Data Kosong', 'Silakan paste atau ketik data surat jalan terlebih dahulu.', 'error');
        return;
    }

    const lines = rawText.split('\n').filter(line => line.trim() !== '');
    const columnKeys = [
        'nomor_surat_jalan', 'no_kontainer', 'no_seal', 'size', 'tipe_kontainer', 
        'f_e', 'supir', 'no_plat', 'kenek', 'krani', 'jenis_barang', 'tujuan_pengiriman', 'tanggal_surat_jalan'
    ];

    bulkParsedRows = [];
    const tbody = document.getElementById('bulkPreviewBody');
    tbody.innerHTML = '';

    let warnings = [];
    const defaultTanggal = document.getElementById('bulk_tanggal_surat_jalan').value;
    const defaultTujuan = document.getElementById('bulk_tujuan_pengiriman').value;

    lines.forEach((line, index) => {
        const cols = line.split(';');
        const row = {};

        columnKeys.forEach((key, colIndex) => {
            row[key] = (cols[colIndex] || '').trim();
        });

        if (!row.nomor_surat_jalan) {
            warnings.push(`Baris ${index + 1}: Nomor Surat Jalan kosong, baris ini akan diabaikan.`);
            return;
        }

        bulkParsedRows.push(row);

        // Est Uang Jalan
        const resolvedTujuan = row.tujuan_pengiriman || defaultTujuan;
        const resolvedSize = row.size || '20FT';
        const resolvedFE = row.f_e || 'Full';
        
        let estUangJalan = '-';
        if (resolvedTujuan) {
            const destOption = Array.from(document.getElementById('bulk_tujuan_pengiriman').options)
                .find(opt => opt.value === resolvedTujuan);
            if (destOption) {
                try {
                    const rates = JSON.parse(destOption.getAttribute('data-rates') || '{}');
                    const sizeNum = resolvedSize.replace(/\D/g, '');
                    const key = `${sizeNum}_${resolvedFE}`;
                    const amount = rates[key];
                    if (amount !== undefined && amount !== null) {
                        estUangJalan = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
                    }
                } catch(e) {}
            }
        }

        // Build preview row
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50';

        const cellValues = [
            bulkParsedRows.length,
            row.nomor_surat_jalan,
            row.tanggal_surat_jalan || defaultTanggal,
            row.no_kontainer || '-',
            row.no_seal || '-',
            resolvedSize,
            row.tipe_kontainer || 'Dry Container',
            resolvedFE,
            row.supir || '-',
            row.no_plat || '-',
            resolvedTujuan || '-',
            estUangJalan
        ];

        cellValues.forEach((val, i) => {
            const td = document.createElement('td');
            td.className = 'px-3 py-2 whitespace-nowrap';
            if (i === 1) {
                td.className += ' font-semibold text-indigo-700';
            }
            if (i === 11) {
                td.className += ' text-right font-mono font-bold';
            }
            td.innerHTML = val;
            tr.appendChild(td);
        });

        tbody.appendChild(tr);
    });

    const previewContainer = document.getElementById('bulkPreviewContainer');
    const parseInfo = document.getElementById('bulkParseInfo');
    const submitBtn = document.getElementById('btnSubmitBulk');

    if (bulkParsedRows.length > 0) {
        previewContainer.classList.remove('hidden');
        parseInfo.innerHTML = `<span class="text-green-600 font-semibold">${bulkParsedRows.length} baris valid</span>` +
            (warnings.length > 0 ? ` | <span class="text-amber-600">${warnings.length} peringatan</span>` : '');
        submitBtn.disabled = false;

        if (warnings.length > 0) {
            showBulkAlert('Peringatan', warnings.join('<br>'), 'warning');
        } else {
            document.getElementById('bulkModalAlertArea').innerHTML = '';
        }
    } else {
        previewContainer.classList.add('hidden');
        parseInfo.innerHTML = '<span class="text-red-600 font-semibold">Tidak ada baris valid ditemukan</span>';
        submitBtn.disabled = true;
        showBulkAlert('Tidak Ada Data', 'Tidak ada baris dengan Nomor Surat Jalan yang valid.', 'error');
    }
}

function submitBulkSuratJalan() {
    if (bulkParsedRows.length === 0) {
        showBulkAlert('Error', 'Tidak ada data untuk disimpan.', 'error');
        return;
    }

    const submitBtn = document.getElementById('btnSubmitBulk');
    const submitText = document.getElementById('btnBulkSubmitText');
    const submitLoading = document.getElementById('btnBulkSubmitLoading');

    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');

    const payload = {
        tanggal_surat_jalan: document.getElementById('bulk_tanggal_surat_jalan').value,
        term: document.getElementById('bulk_term').value,
        pengirim: document.getElementById('bulk_pengirim').value,
        penerima: document.getElementById('bulk_penerima').value,
        alamat: document.getElementById('bulk_alamat').value,
        tujuan_pengambilan: document.getElementById('bulk_tujuan_pengambilan').value,
        tujuan_pengiriman: document.getElementById('bulk_tujuan_pengiriman').value,
        rows: bulkParsedRows
    };

    fetch('{{ route("surat-jalan-batam.store-bulk", [], false) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');

        if (data.success) {
            let msg = data.message;
            if (data.errors && data.errors.length > 0) {
                msg += '<br><br><strong>Detail error:</strong><br>' + data.errors.join('<br>');
                showBulkAlert('Sebagian Berhasil', msg, 'warning');
            } else {
                showBulkAlert('Berhasil!', msg, 'success');
            }

            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            let errorMsg = data.message || 'Gagal menyimpan data.';
            if (data.errors && data.errors.length > 0) {
                errorMsg += '<br><br><strong>Detail:</strong><br>' + data.errors.join('<br>');
            }
            showBulkAlert('Gagal', errorMsg, 'error');
        }
    })
    .catch(error => {
        console.error('Bulk submit error:', error);
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
        showBulkAlert('Error', 'Terjadi kesalahan saat menghubungi server.', 'error');
    });
}
</script>
@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('suratJalanTable');
});
</script>
@endpush
