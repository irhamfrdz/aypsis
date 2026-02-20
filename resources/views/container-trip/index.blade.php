@extends('layouts.app')

@section('title', 'Data Master Sewa Kontainer')
@section('page_title', 'Data Master Sewa Kontainer')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Data Master Sewa Kontainer</h1>
                            <p class="text-indigo-100 text-sm">Kebenaran Tunggal - Pelacakan perjalanan kontainer sewa</p>
                        </div>
                    </div>
                    <a href="{{ route('container-trip.create') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Perjalanan
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h2>
            </div>
            <div class="p-6">
                <form action="{{ route('container-trip.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                                Pencarian
                            </label>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari no kontainer atau vendor..."
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                Status
                            </label>
                            <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Semua Status</option>
                                <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                                <option value="CLOSED" {{ request('status') == 'CLOSED' ? 'selected' : '' }}>CLOSED</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-end space-x-2">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Cari
                            </button>
                            <a href="{{ route('container-trip.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">List Perjalanan Kontainer</h2>
                    <span class="text-sm text-gray-500">Total: {{ $containerTrips->total() }} record</span>
                </div>
            </div>

            @if($containerTrips->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 resizable-table" id="containerTripTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Kontainer
                                    <div class="resize-handle"></div>
                                </th>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Vendor
                                    <div class="resize-handle"></div>
                                </th>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ukuran
                                    <div class="resize-handle"></div>
                                </th>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tgl Ambil
                                    <div class="resize-handle"></div>
                                </th>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tgl Kembali
                                    <div class="resize-handle"></div>
                                </th>
                                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Harga Sewa
                                    <div class="resize-handle"></div>
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($containerTrips as $trip)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-indigo-600">
                                            {{ $trip->no_kontainer }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $trip->vendor->nama_vendor }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $trip->ukuran == '20' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $trip->ukuran }}'
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $trip->tgl_ambil->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $trip->tgl_kembali ? $trip->tgl_kembali->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        Rp {{ number_format($trip->harga_sewa, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('container-trip.show', $trip) }}"
                                               class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded transition duration-150 ease-in-out">
                                                Lihat
                                            </a>
                                            <a href="{{ route('container-trip.edit', $trip) }}"
                                               class="inline-flex items-center px-3 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 text-xs font-medium rounded transition duration-150 ease-in-out">
                                                Edit
                                            </a>
                                            <form action="{{ route('container-trip.destroy', $trip) }}"
                                                  method="POST"
                                                  class="inline-block"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded transition duration-150 ease-in-out">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Rows Per Page Selection --}}
                @include('components.rows-per-page', [
                    'routeName' => 'container-trip.index',
                    'paginator' => $containerTrips,
                    'entityName' => 'trip',
                    'entityNamePlural' => 'trips'
                ])

                <!-- Pagination -->
                @include('components.modern-pagination', ['paginator' => $containerTrips, 'routeName' => 'container-trip.index'])
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data perjalanan kontainer</h3>
                    <p class="text-sm text-gray-500 mb-4">Mulai dengan menambahkan data pertama Anda</p>
                    <a href="{{ route('container-trip.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                        Tambah Perjalanan
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('containerTripTable');
});
</script>
@endpush
