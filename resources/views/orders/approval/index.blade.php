@extends('layouts.app')

@section('title', 'Kelola Data Order')
@section('page_title', 'Kelola Data Order')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Data Order</h1>
                    <p class="mt-1 text-sm text-gray-600">Lengkapi dan update data order yang belum lengkap</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center px-3 py-2 bg-orange-100 text-orange-800 text-sm font-medium rounded-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <span id="incompleteCount">{{ $orders->total() }}</span> Order Belum Lengkap
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
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

        @if (session('error'))
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

        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('orders.approval.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor order, kontainer, tujuan..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('orders.approval.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Order Belum Lengkap</h3>
                <p class="mt-1 text-sm text-gray-600">Total: {{ $orders->total() }} order memerlukan kelengkapan data</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->nomor_order }}</div>
                                    @if($order->no_kontainer)
                                        <div class="text-sm text-gray-500">{{ $order->no_kontainer }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->tanggal_order->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->pengirim->nama_pengirim ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs">
                                        <p class="font-medium">Ambil: {{ $order->tujuan_ambil ?? '-' }}</p>
                                        <p class="text-gray-600">Kirim: {{ $order->tujuan_kirim ?? '-' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($order->tipe_kontainer === 'cargo')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                            Cargo
                                        </span>
                                    @else
                                        {{ $order->size_kontainer }} ({{ $order->unit_kontainer }})
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $missingFields = collect();
                                        
                                        // Check basic order info
                                        if (!$order->nomor_order) {
                                            $missingFields->push('Nomor Order');
                                        }
                                        if (!$order->tanggal_order) {
                                            $missingFields->push('Tanggal Order');
                                        }
                                        if (!$order->no_tiket_do || trim($order->no_tiket_do) === '') {
                                            $missingFields->push('No Tiket/DO');
                                        }
                                        if (!$order->status || trim($order->status) === '') {
                                            $missingFields->push('Status');
                                        }
                                        
                                        // Check pengirim
                                        if (!$order->pengirim_id) {
                                            $missingFields->push('Pengirim');
                                        }
                                        
                                        // Check tujuan
                                        if (!$order->tujuan_ambil || trim($order->tujuan_ambil) === '') {
                                            $missingFields->push('Tujuan Ambil');
                                        }
                                        if (!$order->tujuan_kirim || trim($order->tujuan_kirim) === '') {
                                            $missingFields->push('Tujuan Kirim');
                                        }
                                        
                                        // Check master data
                                        if (!$order->term_id) {
                                            $missingFields->push('Term');
                                        }
                                        if (!$order->jenis_barang_id) {
                                            $missingFields->push('Jenis Barang');
                                        }
                                        
                                        // Check kontainer info
                                        if (!$order->tipe_kontainer || trim($order->tipe_kontainer) === '') {
                                            $missingFields->push('Tipe Kontainer');
                                        } else {
                                            if ($order->tipe_kontainer !== 'cargo') {
                                                if (!$order->size_kontainer) {
                                                    $missingFields->push('Size Kontainer');
                                                }
                                                if (!$order->unit_kontainer || (is_numeric($order->unit_kontainer) && $order->unit_kontainer <= 0)) {
                                                    $missingFields->push('Unit Kontainer');
                                                }
                                            }
                                        }
                                        
                                        // Check units (allow 0 as valid value, but not null or empty)
                                        if (is_null($order->units) || $order->units === '' || !is_numeric($order->units)) {
                                            $missingFields->push('Units');
                                        }
                                        
                                        $missingCount = $missingFields->count();
                                    @endphp
                                    @if($missingCount > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" title="{{ $missingFields->join(', ') }}">
                                            {{ $missingCount }} field belum lengkap
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Data lengkap
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('orders.edit', $order->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit Data">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('orders.show', $order) }}" class="text-gray-600 hover:text-gray-900" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-gray-500 text-base">Tidak ada order dengan data belum lengkap</p>
                                        <p class="text-gray-400 text-sm mt-1">Semua order sudah memiliki data yang lengkap</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($orders->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>



@endsection

@push('scripts')
<script>
// Simple functionality for the data management interface
document.addEventListener('DOMContentLoaded', function() {
    // Add any future JavaScript functionality here if needed
    console.log('Order data management interface loaded');
});
</script>
@endpush
