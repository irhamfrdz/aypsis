@extends('layouts.app')

@section('title', 'Outstanding Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0">Outstanding Orders</h1>
        <div class="flex space-x-2">
            <a href="{{ route('outstanding.export', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </a>
            <button id="refreshStats"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8" id="statsContainer">
        <div class="bg-white rounded-lg shadow border-l-4 border-yellow-500">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="text-xs font-bold text-yellow-600 uppercase tracking-wide mb-1">
                            Order Menunggu
                        </div>
                        <div class="text-2xl font-bold text-gray-900" id="pendingCount">
                            <svg class="animate-spin h-5 w-5 inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow border-l-4 border-blue-500">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-1">
                            Order Sedang Dikerjakan
                        </div>
                        <div class="text-2xl font-bold text-gray-900" id="partialCount">
                            <svg class="animate-spin h-5 w-5 inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow border-l-4 border-green-500">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="text-xs font-bold text-green-600 uppercase tracking-wide mb-1">
                            Order Selesai
                        </div>
                        <div class="text-2xl font-bold text-gray-900" id="completedCount">
                            <svg class="animate-spin h-5 w-5 inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow border-l-4 border-purple-500">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="text-xs font-bold text-purple-600 uppercase tracking-wide mb-1">
                            Total Outstanding
                        </div>
                        <div class="text-2xl font-bold text-gray-900" id="outstandingCount">
                            <svg class="animate-spin h-5 w-5 inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- Filter Card -->
    <div class="bg-white shadow-sm rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Filter Outstanding Orders</h3>
            <button class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    type="button" id="toggleFilter">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                </svg>
                Filters
            </button>
        </div>
        <div id="filterCollapse" class="p-6">
            <form id="filterForm" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="space-y-1">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label for="term_id" class="block text-sm font-medium text-gray-700">Term</label>
                        <select name="term_id" id="term_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Terms</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                    {{ $term->nama_status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label for="pengirim_id" class="block text-sm font-medium text-gray-700">Pengirim</label>
                        <select name="pengirim_id" id="pengirim_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Pengirim</option>
                            @foreach($pengirims as $pengirim)
                                <option value="{{ $pengirim->id }}" {{ request('pengirim_id') == $pengirim->id ? 'selected' : '' }}>
                                    {{ $pengirim->nama_pengirim }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label for="completion_percentage" class="block text-sm font-medium text-gray-700">Min. Completion %</label>
                        <input type="number" name="completion_percentage" id="completion_percentage"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               min="0" max="100" value="{{ request('completion_percentage') }}" placeholder="0-100">
                    </div>

                    <div class="space-y-1">
                        <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                        <input type="date" name="date_from" id="date_from"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               value="{{ request('date_from') }}">
                    </div>

                    <div class="space-y-1">
                        <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                        <input type="date" name="date_to" id="date_to"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               value="{{ request('date_to') }}">
                    </div>

                    <div class="space-y-1">
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               value="{{ request('search') }}" placeholder="Order number, container...">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">&nbsp;</label>
                        <div class="flex space-x-3">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Filter
                            </button>
                            <a href="{{ route('outstanding.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Outstanding Orders Data</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="outstandingTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr data-order-id="{{ $order->id }}" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->nomor_order }}</div>
                            @if($order->no_kontainer)
                                <div class="text-sm text-gray-500">{{ $order->no_kontainer }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->term->nama_status ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->pengirim->nama_pengirim ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->jenisBarang->nama_barang ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ number_format($order->units, 0) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ number_format($order->sisa, 0) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-300
                                    {{ ($order->completion_percentage ?? 0) >= 100 ? 'bg-green-600' :
                                       (($order->completion_percentage ?? 0) >= 50 ? 'bg-yellow-400' : 'bg-red-400') }}"
                                     style="width: {{ $order->completion_percentage ?? 0 }}%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ number_format($order->completion_percentage ?? 0, 1) }}%</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {!! $order->outstanding_status_badge !!}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="processUnits({{ $order->id }})"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <a href="{{ route('orders.show', $order->id) }}"
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center text-sm text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No outstanding orders found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter to find what you're looking for.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-6 py-3 border-t border-gray-200 flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
                @include('components.modern-pagination', ['paginator' => $orders])
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }}
                        of {{ $orders->total() }} results
                    </p>
                </div>
                <div>
                    @include('components.modern-pagination', ['paginator' => $orders])
                </div>
            </div>
        </div>
        @include('components.rows-per-page')
    </div>
</div>

<!-- Process Units Modal -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="processUnitsModal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Process Units</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="processUnitsForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                    <input type="text" id="modalOrderNumber" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Units</label>
                        <input type="number" id="modalTotalUnits" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Remaining</label>
                        <input type="number" id="modalCurrentSisa" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Process Units <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="processedUnits" name="processed_units"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           min="1" required>
                    <p class="text-sm text-gray-500 mt-1">Enter number of units to process</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="processNotes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                              placeholder="Optional notes about this processing..."></textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Process Units
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Process Units Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="processUnitsModal">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="processUnitsForm">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Process Units</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                                    <input type="text" id="modalOrderNumber" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50" readonly>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Units</label>
                                        <input type="number" id="modalTotalUnits" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Remaining</label>
                                        <input type="number" id="modalCurrentSisa" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50" readonly>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Process Units <span class="text-red-500">*</span></label>
                                    <input type="number" id="processedUnits" name="processed_units"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                           min="1" required>
                                    <p class="mt-1 text-sm text-gray-500">Enter number of units to process</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea id="processNotes" name="notes" rows="3"
                                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="Optional notes about this processing..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Process Units
                    </button>
                    <button type="button" onclick="closeModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load statistics
    loadStatistics();

    // Auto-refresh stats every 30 seconds
    setInterval(loadStatistics, 30000);

    // Manual refresh button
    const refreshButton = document.getElementById('refreshStats');
    if (refreshButton) {
        refreshButton.addEventListener('click', function() {
            loadStatistics();
        });
    }

    // Process Units Form handler
    const processForm = document.getElementById('processUnitsForm');
    if (processForm) {
        processForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const orderId = this.getAttribute('data-order-id');
            const processedUnits = document.getElementById('processedUnits').value;
            const notes = document.getElementById('processNotes').value;

            if (!processedUnits || processedUnits <= 0) {
                showAlert('danger', 'Please enter a valid number of units to process');
                return;
            }

            // Disable form during processing
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            // Create form data
            const formData = new FormData();
            formData.append('processed_units', processedUnits);
            formData.append('notes', notes);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch(`{{ route('outstanding.process', '') }}/${orderId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the row in table
                    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                    if (row) {
                        const sisaElement = row.querySelector('td:nth-child(6) span');
                        if (sisaElement) {
                            sisaElement.textContent = new Intl.NumberFormat().format(data.order.sisa);
                        }
                        
                        const progressBar = row.querySelector('.h-2 div');
                        if (progressBar) {
                            progressBar.style.width = data.order.completion_percentage + '%';
                        }
                        
                        const progressText = row.querySelector('.text-xs.text-gray-500');
                        if (progressText) {
                            progressText.textContent = parseFloat(data.order.completion_percentage).toFixed(1) + '%';
                        }
                        
                        const statusElement = row.querySelector('td:nth-child(8)');
                        if (statusElement) {
                            statusElement.innerHTML = data.order.status_badge;
                        }
                    }

                    // Close modal and reset form
                    closeModal();

                    // Refresh statistics
                    loadStatistics();

                    // Show success message
                    showAlert('success', 'Units processed successfully!');
                } else {
                    showAlert('danger', data.message || 'Failed to process units');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Error processing units');
            })
            .finally(() => {
                submitButton.disabled = false;
            });
        });
    }
});

// Toggle filter collapse
function toggleFilter() {
    const filterCollapse = document.getElementById('filterCollapse');
    if (filterCollapse.style.display === 'none') {
        filterCollapse.style.display = 'block';
    } else {
        filterCollapse.style.display = 'none';
    }
}

function loadStatistics() {
    fetch('{{ route("outstanding.stats") }}')
        .then(response => response.json())
        .then(data => {
            const pendingCount = document.getElementById('pendingCount');
            const partialCount = document.getElementById('partialCount');
            const completedCount = document.getElementById('completedCount');
            const outstandingCount = document.getElementById('outstandingCount');
            
            if (pendingCount) pendingCount.textContent = data.pending;
            if (partialCount) partialCount.textContent = data.partial;
            if (completedCount) completedCount.textContent = data.completed;
            if (outstandingCount) outstandingCount.textContent = data.total_outstanding;
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
            // Show error state
            const errorIcon = '<svg class="w-5 h-5 inline text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
            document.querySelectorAll('.text-2xl').forEach(el => {
                el.innerHTML = errorIcon;
            });
        });
}

function processUnits(orderId) {
    // Get order details via fetch
    fetch(`{{ route('outstanding.details', '') }}/${orderId}`)
        .then(response => response.json())
        .then(data => {
            const modalOrderNumber = document.getElementById('modalOrderNumber');
            const modalTotalUnits = document.getElementById('modalTotalUnits');
            const modalCurrentSisa = document.getElementById('modalCurrentSisa');
            const processedUnits = document.getElementById('processedUnits');
            const processForm = document.getElementById('processUnitsForm');
            
            if (modalOrderNumber) modalOrderNumber.value = data.nomor_order;
            if (modalTotalUnits) modalTotalUnits.value = data.units;
            if (modalCurrentSisa) modalCurrentSisa.value = data.sisa;
            if (processedUnits) processedUnits.setAttribute('max', data.sisa);

            // Show modal
            const modal = document.getElementById('processUnitsModal');
            if (modal) {
                modal.classList.remove('hidden');
            }

            // Store order ID for processing
            if (processForm) {
                processForm.setAttribute('data-order-id', orderId);
            }
        })
        .catch(error => {
            console.error('Error loading order details:', error);
            showAlert('danger', 'Error loading order details');
        });
}

function closeModal() {
    const modal = document.getElementById('processUnitsModal');
    const form = document.getElementById('processUnitsForm');
    
    if (modal) {
        modal.classList.add('hidden');
    }
    
    if (form) {
        form.reset();
        form.removeAttribute('data-order-id');
    }
}

function showAlert(type, message) {
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
    const iconColor = type === 'success' ? 'text-green-400' : 'text-red-400';
    const icon = type === 'success' ?
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';

    const alertHtml = `
        <div id="alert" class="fixed top-4 right-4 max-w-md w-full ${bgColor} border rounded-lg shadow-lg z-50">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${icon}
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="closeAlert()" class="inline-flex ${iconColor} hover:text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing alerts
    const existingAlert = document.getElementById('alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    // Add new alert
    document.body.insertAdjacentHTML('beforeend', alertHtml);

    // Auto-remove after 5 seconds
    setTimeout(function() {
        closeAlert();
    }, 5000);
}

function closeAlert() {
    const alert = document.getElementById('alert');
    if (alert) {
        alert.style.transition = 'opacity 0.3s ease-in-out';
        alert.style.opacity = '0';
        setTimeout(() => {
            alert.remove();
        }, 300);
    }
}
</script>
@endpush
