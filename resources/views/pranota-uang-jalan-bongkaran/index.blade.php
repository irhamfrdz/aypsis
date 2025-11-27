@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto px-3 py-2 overflow-hidden">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Pranota Uang Jalan Bongkaran</h1>
            <p class="text-xs text-gray-600 mt-0.5">Kelola pranota pembayaran uang jalan bongkaran</p>
        </div>
        <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm whitespace-nowrap flex items-center">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Pranota Bongkaran
        </a>
    </div>

    <!-- Statistics Cards (reuse layout) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-600">Total Bongkaran</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
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
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['this_month'] ?? 0 }}</p>
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

    <!-- Filters & Table similar to pranota-uang-jalan -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
        <form method="GET" action="{{ route('pranota-uang-jalan-bongkaran.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor uang jalan bongkaran atau no kontainer..." class="w-full pl-10 pr-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                    <select name="status" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium flex items-center transition-colors">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('pranota-uang-jalan-bongkaran.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table of Uang Jalan Bongkaran entries -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 resizable-table" id="pranotaUangJalanBongkaranTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-2 text-xs font-semibold">No</th>
                        <th class="px-2 py-2 text-xs font-semibold">No Uang Jalan</th>
                        <th class="px-2 py-2 text-xs font-semibold">Tanggal</th>
                        <th class="px-2 py-2 text-xs font-semibold">Supir</th>
                        <th class="px-2 py-2 text-xs font-semibold text-right">Total</th>
                        <th class="px-2 py-2 text-xs font-semibold text-center">Status</th>
                        <th class="px-2 py-2 text-xs font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rows as $i => $row)
                        <tr>
                            <td class="px-2 py-2 text-sm">{{ $rows->firstItem() + $i }}</td>
                            <td class="px-2 py-2 text-sm">{{ $row->nomor_uang_jalan }}</td>
                            <td class="px-2 py-2 text-sm">{{ $row->tanggal_uang_jalan?->format('d/m/Y') }}</td>
                            <td class="px-2 py-2 text-sm">{{ $row->supir }}</td>
                            <td class="px-2 py-2 text-sm text-right">Rp {{ number_format($row->jumlah_total ?? 0, 0, ',', '.') }}</td>
                            <td class="px-2 py-2 text-sm text-center">{{ $row->status }}</td>
                            <td class="px-2 py-2 text-sm text-center">-</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-2 py-8 text-center">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rows->hasPages())
            <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                {{ $rows->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        initResizableTable('pranotaUangJalanBongkaranTable');
    });
</script>
@endpush

@endsection
