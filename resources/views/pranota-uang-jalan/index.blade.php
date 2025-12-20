@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto px-3 py-2 overflow-hidden">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Pranota Uang Jalan</h1>
            <p class="text-xs text-gray-600 mt-0.5">Kelola pranota pembayaran uang jalan</p>
        </div>
        <a href="{{ route('pranota-uang-jalan.create') }}" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm whitespace-nowrap flex items-center">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Pranota
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Total Pranota</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['this_month'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Belum Bayar</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['unpaid'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Lunas</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $stats['paid'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
        <form method="GET" action="{{ route('pranota-uang-jalan.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari nomor pranota atau nomor uang jalan..."
                               class="w-full pl-10 pr-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                    <select name="status" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium flex items-center transition-colors">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filtera
                    </button>
                    <a href="{{ route('pranota-uang-jalan.index') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Reset
                    </a>
                          @can('pranota-uang-jalan-export')
                          <a href="{{ route('pranota-uang-jalan.export') }}?{{ http_build_query(request()->only(['search', 'status'])) }}" 
                              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium flex items-center transition-colors">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Excel
                    </a>
                    @endcan
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 resizable-table" id="pranotaUangJalanTable">
                <thead class="bg-gray-50">
                    <tr><th class="resizable-th px-2 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide" style="position: relative;">No<div class="resize-handle"></div></th><th class="resizable-th px-2 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide" style="position: relative;">Nomor Pranota<div class="resize-handle"></div></th><th class="resizable-th px-2 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide" style="position: relative;">Tanggal<div class="resize-handle"></div></th><th class="resizable-th px-2 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide" style="position: relative;">Qty<div class="resize-handle"></div></th><th class="resizable-th px-2 py-2 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide" style="position: relative;">Total<div class="resize-handle"></div></th><th class="resizable-th px-2 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide" style="position: relative;">Status<div class="resize-handle"></div></th><th class="px-2 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Aksi</th></tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pranotaUangJalans as $index => $pranota)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-2 py-2.5 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ $pranotaUangJalans->firstItem() + $index }}
                            </td>
                            <td class="px-2 py-2.5">
                                <div class="text-sm font-medium text-gray-900">{{ $pranota->nomor_pranota }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $pranota->periode_tagihan }}</div>
                            </td>
                            <td class="px-2 py-2.5 whitespace-nowrap text-sm text-gray-900">
                                {{ $pranota->tanggal_pranota->format('d/m/Y') }}
                            </td>
                            <td class="px-2 py-2.5 whitespace-nowrap text-sm text-gray-900 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $pranota->jumlah_uang_jalan }}
                                </span>
                            </td>
                            <td class="px-2 py-2.5 whitespace-nowrap text-right">
                                <div class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($pranota->total_amount, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-2 py-2.5 whitespace-nowrap text-center">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pranota->status_badge }}">
                                        {{ $pranota->status_text }}
                                    </span>
                                    @if($pranota->status_pembayaran == 'paid' && $pranota->pembayaranPranotaUangJalan)
                                        <div class="text-xs text-gray-600 font-medium">
                                            {{ $pranota->pembayaranPranotaUangJalan->nomor_pembayaran }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-2 py-2.5 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-1">
                                    <a href="{{ route('pranota-uang-jalan.show', $pranota) }}" 
                                       class="inline-flex items-center justify-center w-7 h-7 text-indigo-600 hover:text-white hover:bg-indigo-600 rounded-full transition-colors" title="Lihat Detail">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <a href="{{ route('pranota-uang-jalan.print', $pranota) }}" 
                                       class="inline-flex items-center justify-center w-7 h-7 text-green-600 hover:text-white hover:bg-green-600 rounded-full transition-colors" title="Cetak Pranota" target="_blank">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </a>

                                    @if($pranota->status_pembayaran == 'unpaid')
                                        <a href="{{ route('pranota-uang-jalan.edit', $pranota) }}" 
                                           class="inline-flex items-center justify-center w-7 h-7 text-yellow-600 hover:text-white hover:bg-yellow-600 rounded-full transition-colors" title="Edit">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    @endif

                                    <form action="{{ route('pranota-uang-jalan.destroy', $pranota) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus pranota ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-7 h-7 text-red-600 hover:text-white hover:bg-red-600 rounded-full transition-colors" title="Hapus">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-2 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-2 bg-gray-100 rounded-full mb-3">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-medium text-gray-900 mb-1">Belum Ada Data</h3>
                                    <p class="text-gray-500 text-sm mb-3">Belum ada pranota uang jalan yang tersedia</p>
                                    <a href="{{ route('pranota-uang-jalan.create') }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded transition-colors">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Buat Pranota Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pranotaUangJalans->hasPages())
            <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                {{ $pranotaUangJalans->links() }}
            </div>
        @endif
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="fixed top-4 right-4 z-50 max-w-sm bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg" id="success-alert">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="inline-flex bg-green-100 rounded-md p-1.5 text-green-500 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-100 focus:ring-green-600" onclick="document.getElementById('success-alert').remove()">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 z-50 max-w-sm bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg" id="error-alert">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="inline-flex bg-red-100 rounded-md p-1.5 text-red-500 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-100 focus:ring-red-600" onclick="document.getElementById('error-alert').remove()">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="fixed top-4 right-4 z-50 max-w-sm bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg" id="validation-errors">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium">Terdapat kesalahan:</h4>
                    <ul class="mt-1 text-sm list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="inline-flex bg-red-100 rounded-md p-1.5 text-red-500 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-100 focus:ring-red-600" onclick="document.getElementById('validation-errors').remove()">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
// Auto hide alerts after 5 seconds
setTimeout(function() {
    const successAlert = document.getElementById('success-alert');
    const errorAlert = document.getElementById('error-alert');
    const validationErrors = document.getElementById('validation-errors');
    
    if (successAlert) {
        successAlert.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
        successAlert.style.opacity = '0';
        successAlert.style.transform = 'translateX(100%)';
        setTimeout(() => successAlert.remove(), 500);
    }
    
    if (errorAlert) {
        errorAlert.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
        errorAlert.style.opacity = '0';
        errorAlert.style.transform = 'translateX(100%)';
        setTimeout(() => errorAlert.remove(), 500);
    }
    
    if (validationErrors) {
        validationErrors.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
        validationErrors.style.opacity = '0';
        validationErrors.style.transform = 'translateX(100%)';
        setTimeout(() => validationErrors.remove(), 500);
    }
}, 5000);

// Add responsive behavior for alerts on smaller screens
function adjustAlertPosition() {
    const alerts = document.querySelectorAll('[id$="-alert"], #validation-errors');
    alerts.forEach(alert => {
        if (window.innerWidth < 768) {
            alert.style.position = 'fixed';
            alert.style.top = '1rem';
            alert.style.left = '1rem';
            alert.style.right = '1rem';
            alert.style.maxWidth = 'calc(100vw - 2rem)';
            alert.style.zIndex = '9999';
        } else {
            alert.style.position = 'fixed';
            alert.style.top = '1rem';
            alert.style.right = '1rem';
            alert.style.left = 'auto';
            alert.style.maxWidth = '24rem';
            alert.style.zIndex = '50';
        }
    });
}

// Adjust on load and resize
window.addEventListener('load', adjustAlertPosition);
window.addEventListener('resize', adjustAlertPosition);
</script>
@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('pranotaUangJalanTable');
});
</script>
@endpush