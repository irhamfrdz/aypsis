@extends('layouts.app')

@section('title', 'Surat Jalan')
@section('page_title', 'Surat Jalan')

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
                    <h1 class="text-3xl font-bold text-gray-900">Surat Jalan</h1>
                    <p class="mt-1 text-sm text-gray-600">Kelola data surat jalan pengiriman barang</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('surat-jalan.select-order') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Surat Jalan
                    </a>
                    <a href="{{ route('surat-jalan.create-without-order') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Surat Jalan Tanpa Order
                    </a>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('surat-jalan.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Pencarian
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="No. Surat Jalan, Pengirim, No. Kontainer, Tipe Kontainer, Tujuan..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:w-48">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="sm:w-48">
                    <label for="status_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                    <select name="status_pembayaran" id="status_pembayaran" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all" {{ request('status_pembayaran') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="belum_masuk_pranota" {{ request('status_pembayaran') == 'belum_masuk_pranota' ? 'selected' : '' }}>Belum Masuk Pranota</option>
                        <option value="belum_dibayar" {{ request('status_pembayaran') == 'belum_dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                        <option value="sudah_dibayar" {{ request('status_pembayaran') == 'sudah_dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                    </select>
                </div>
                <div class="sm:w-48">
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
                <div class="sm:w-40">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:w-40">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'status_pembayaran', 'tipe_kontainer', 'start_date', 'end_date']))
                        <a href="{{ route('surat-jalan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Reset
                        </a>
                    @endif
                    @can('surat-jalan-export')
                    <a href="{{ route('surat-jalan.export') }}?{{ http_build_query(request()->only(['search','status','status_pembayaran','tipe_kontainer','start_date','end_date'])) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 shadow-sm">
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
                <h3 class="text-lg font-medium text-gray-900">Daftar Surat Jalan</h3>
                <p class="mt-1 text-sm text-gray-600">Total: {{ $suratJalans->total() }} surat jalan</p>
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
                            <th class="resizable-th px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-12" style="position: relative;">Jumlah Rit<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20" style="position: relative;">Supir<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20" style="position: relative;">Status<div class="resize-handle"></div></th>
                            <th class="resizable-th px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28" style="position: relative;">Pembayaran<div class="resize-handle"></div></th>
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
                                                          <a href="{{ route('surat-jalan.edit', $suratJalan->id) }}"
                                                              class="group flex items-center px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 hover:text-gray-900 whitespace-nowrap">
                                                                <svg class="mr-2 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </a>

                                            {{-- Tombol Cancel - untuk membatalkan surat jalan --}}
                                            <button onclick="event.stopPropagation(); updateStatus('{{ $suratJalan->id }}', 'cancelled')"
                                                    class="group flex items-center w-full px-3 py-1.5 text-xs text-red-700 hover:bg-red-50 hover:text-red-900 whitespace-nowrap">
                                                <svg class="mr-2 h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Cancel
                                            </button>

                                            {{-- Tombol Print --}}
                                                          <a href="{{ route('surat-jalan.print', $suratJalan->id) }}"
                                                              target="_blank"
                                                              class="group flex items-center px-3 py-1.5 text-xs text-blue-700 hover:bg-blue-50 hover:text-blue-900 whitespace-nowrap">
                                                <svg class="mr-2 h-4 w-4 text-blue-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Print SJ
                                            </a>

                                            {{-- Tombol Memo --}}
                                                          <a href="{{ route('surat-jalan.print-memo', $suratJalan->id) }}"
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

                                            {{-- Tombol Delete --}}
                                            <div class="border-t border-gray-100"></div>
                                            <form action="{{ route('surat-jalan.destroy', $suratJalan->id) }}" method="POST" class="inline w-full">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="event.stopPropagation(); return confirm('Yakin ingin menghapus surat jalan ini?')"
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
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->order ? $suratJalan->order->nomor_order : '-' }}">
                                    {{ $suratJalan->order ? $suratJalan->order->nomor_order : '-' }}
                                </div>
                            </td>
                            <td class="px-2 py-2 text-xs font-medium">
                                <a href="{{ route('surat-jalan.print', $suratJalan->id) }}" 
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
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->tujuanPengambilanRelation->nama ?? $suratJalan->order->tujuan_ambil ?? '-' }}">
                                    {{ $suratJalan->tujuanPengambilanRelation->nama ?? $suratJalan->order->tujuan_ambil ?? '-' }}
                                </div>
                            </td>
                            <td class="px-2 py-2 text-xs text-gray-900">
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->tujuanPengirimanRelation->nama ?? $suratJalan->order->tujuan_kirim ?? '-' }}">
                                    {{ $suratJalan->tujuanPengirimanRelation->nama ?? $suratJalan->order->tujuan_kirim ?? '-' }}
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
                            <td class="px-2 py-2 text-center text-xs text-gray-900">
                                <div class="overflow-hidden text-ellipsis" title="{{ $suratJalan->pranota_uang_rit_count ?? 0 }}">
                                    {{ $suratJalan->pranota_uang_rit_count ?? 0 }}
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
                            <td class="px-2 py-2 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- Tombol Detail --}}
                                                <a href="{{ route('surat-jalan.show', $suratJalan->id) }}"
                                                    class="text-blue-600 hover:text-blue-900 transition-colors duration-200 whitespace-nowrap"
                                       title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    {{-- Tombol Edit --}}
                                                <a href="{{ route('surat-jalan.edit', $suratJalan->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200 whitespace-nowrap"
                                       title="Edit Surat Jalan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    {{-- Tombol Print --}}
                                                <a href="{{ route('surat-jalan.print', $suratJalan->id) }}"
                                                    class="text-green-600 hover:text-green-900 transition-colors duration-200 whitespace-nowrap"
                                       title="Print Surat Jalan"
                                       target="_blank">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </a>

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
                                    <form action="{{ route('surat-jalan.destroy', $suratJalan->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus surat jalan ini?')"
                                            class="text-red-600 hover:text-red-900 transition-colors duration-200 whitespace-nowrap"
                                                title="Hapus Surat Jalan">
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
                            <td colspan="15" class="px-6 py-8 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-500 text-base">Belum ada data surat jalan</p>
                                    <p class="text-gray-400 text-sm mt-1">Tambah surat jalan pertama untuk memulai</p>
                                    <a href="{{ route('surat-jalan.select-order') }}" class="mt-3 text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                        Buat surat jalan pertama
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
    if (confirm('Yakin ingin mengubah status surat jalan ini?')) {
        fetch(`/surat-jalan/${suratJalanId}/update-status`, {
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
    window.open(`/surat-jalan/${suratJalanId}/print-preprinted`, '_blank');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    // If click is not inside the button wrapper or inside any open dropdown, hide and restore dropdowns
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
