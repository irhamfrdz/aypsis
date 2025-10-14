@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <!-- Alert Messages -->
    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                {{ session('info') }}
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Pilih Nomor Order</h1>
                <p class="text-xs text-gray-600 mt-1">Pilih order untuk membuat surat jalan</p>
            </div>
            <a href="{{ route('surat-jalan.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm font-medium whitespace-nowrap">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Filters -->
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('surat-jalan.select-order') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nomor Order, Pengirim, Tujuan..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <div class="flex gap-2 w-full">
                        <button type="submit"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                            Filter
                        </button>
                        <a href="{{ route('surat-jalan.select-order') }}"
                           class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 m-4 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 m-4 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Order</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Order</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan Kirim</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->nomor_order }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $order->tanggal_order ? $order->tanggal_order->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-900">{{ $order->pengirim->nama_pengirim ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-900">{{ $order->jenisBarang->nama_barang ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-900">{{ $order->tujuan_kirim ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $order->size_kontainer }} {{ $order->tipe_kontainer }}
                                @if($order->unit_kontainer)
                                    <span class="text-gray-500">({{ $order->unit_kontainer }} unit)</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->status == 'active') bg-green-100 text-green-800
                                @elseif($order->status == 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'completed') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                            @if(in_array($order->status, ['active', 'confirmed', 'processing']))
                                <a href="{{ route('surat-jalan.create', ['order_id' => $order->id]) }}"
                                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-150 inline-flex items-center">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Pilih Order
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">Tidak tersedia</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-sm">Tidak ada order yang tersedia</p>
                                <p class="text-xs text-gray-400 mt-1">Pastikan ada order dengan status active, confirmed, atau processing</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Info Box -->
<div class="max-w-7xl mx-auto mt-4">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="h-5 w-5 text-blue-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium">Informasi:</p>
                <p class="mt-1">Hanya order dengan status <span class="font-medium">Active</span>, <span class="font-medium">Confirmed</span>, atau <span class="font-medium">Processing</span> yang dapat digunakan untuk membuat surat jalan.</p>
            </div>
        </div>
    </div>
</div>
@endsection
