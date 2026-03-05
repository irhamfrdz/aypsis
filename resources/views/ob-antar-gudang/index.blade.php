@extends('layouts.app')

@section('title', 'OB Antar Gudang - ' . $gudang->nama_gudang)
@section('page_title', 'OB Antar Gudang')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center">
                <i class="fas fa-warehouse mr-2 md:mr-3 text-teal-600 text-xl md:text-2xl"></i>
                <div>
                    <h1 class="text-lg md:text-2xl font-bold text-gray-800">OB Antar Gudang</h1>
                    <p class="text-xs md:text-sm text-gray-600">Gudang: <strong>{{ $gudang->nama_gudang }}</strong> {{ $gudang->lokasi ? '| Lokasi: ' . $gudang->lokasi : '' }}</p>
                    <p class="text-[10px] md:text-xs text-gray-500 mt-1">Last updated: {{ now()->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="window.location.reload()" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-md text-xs md:text-sm">
                    <i class="fas fa-sync-alt md:mr-2"></i><span class="hidden md:inline">Refresh Data</span>
                </button>
                <a href="{{ route('ob-antar-gudang.select') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-md text-xs md:text-sm">
                    <i class="fas fa-arrow-left md:mr-2"></i><span class="hidden md:inline">Pilih Gudang Lain</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">
        <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-boxes text-lg md:text-2xl text-blue-600"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Total Kontainer</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $totalAll }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-cubes text-lg md:text-2xl text-green-600"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Stock Kontainer</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $totalStockKontainers }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-box text-lg md:text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Kontainer Sewa</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $totalKontainers }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-full p-2 md:p-3">
                    <i class="fas fa-ruler text-lg md:text-2xl text-purple-600"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Ukuran</p>
                    <div class="text-[10px] md:text-xs text-gray-600">
                        @php
                            $allSizes = [];
                            foreach($stockSizes as $size => $count) {
                                $allSizes[$size] = ($allSizes[$size] ?? 0) + $count;
                            }
                            foreach($kontainerSizes as $size => $count) {
                                $allSizes[$size] = ($allSizes[$size] ?? 0) + $count;
                            }
                        @endphp
                        @forelse($allSizes as $size => $count)
                            <span class="inline-block bg-purple-100 text-purple-800 px-1.5 py-0.5 rounded text-[10px] font-medium mr-1 mb-0.5">{{ $size ?: 'N/A' }}: {{ $count }}</span>
                        @empty
                            <span>-</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 mb-4 md:mb-6">
        <form method="GET" action="{{ route('ob-antar-gudang.index') }}">
            <input type="hidden" name="gudang_id" value="{{ $gudang->id }}">
            
            <div class="space-y-3 md:space-y-0 md:grid md:grid-cols-2 lg:grid-cols-5 md:gap-4">
                {{-- Gudang Selector --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Gudang</label>
                    <select name="gudang_id" class="w-full px-2 md:px-3 py-1.5 md:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500" onchange="this.form.submit()">
                        @foreach($gudangs as $g)
                            <option value="{{ $g->id }}" {{ $gudang->id == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_gudang }} {{ $g->lokasi ? '- ' . $g->lokasi : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Search --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Pencarian</label>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="No kontainer, keterangan..."
                           class="w-full px-2 md:px-3 py-1.5 md:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Status</label>
                    <select name="status"
                            class="w-full px-2 md:px-3 py-1.5 md:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Semua Status</option>
                        <option value="available" {{ $filterStatus == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="active" {{ $filterStatus == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="rented" {{ $filterStatus == 'rented' ? 'selected' : '' }}>Rented</option>
                        <option value="maintenance" {{ $filterStatus == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="damaged" {{ $filterStatus == 'damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="inactive" {{ $filterStatus == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Ukuran Filter --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Ukuran</label>
                    <select name="ukuran"
                            class="w-full px-2 md:px-3 py-1.5 md:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Semua Ukuran</option>
                        <option value="20" {{ $filterUkuran == '20' ? 'selected' : '' }}>20 Feet</option>
                        <option value="40" {{ $filterUkuran == '40' ? 'selected' : '' }}>40 Feet</option>
                        <option value="45" {{ $filterUkuran == '45' ? 'selected' : '' }}>45 Feet</option>
                    </select>
                </div>

                {{-- Tipe Filter --}}
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Tipe Kontainer</label>
                    <select name="tipe_kontainer"
                            class="w-full px-2 md:px-3 py-1.5 md:py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Semua Tipe</option>
                        <option value="Dry" {{ $filterTipe == 'Dry' ? 'selected' : '' }}>Dry</option>
                        <option value="Reefer" {{ $filterTipe == 'Reefer' ? 'selected' : '' }}>Reefer</option>
                        <option value="Open Top" {{ $filterTipe == 'Open Top' ? 'selected' : '' }}>Open Top</option>
                        <option value="Flat Rack" {{ $filterTipe == 'Flat Rack' ? 'selected' : '' }}>Flat Rack</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mt-3 md:mt-4">
                <button type="submit" class="flex-1 md:flex-none bg-teal-600 hover:bg-teal-700 text-white px-3 md:px-4 py-2 rounded-md transition duration-200 inline-flex items-center justify-center text-sm">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
                <a href="{{ route('ob-antar-gudang.index', ['gudang_id' => $gudang->id]) }}" class="flex-1 md:flex-none bg-gray-500 hover:bg-gray-600 text-white px-3 md:px-4 py-2 rounded-md transition duration-200 inline-flex items-center justify-center text-sm">
                    <i class="fas fa-times mr-2"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Stock Kontainer Section --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4 md:mb-6">
        <div class="bg-green-600 px-4 py-3 flex items-center justify-between">
            <h2 class="text-sm md:text-base font-semibold text-white">
                <i class="fas fa-cubes mr-2"></i>Stock Kontainer ({{ $stockKontainers->total() }})
            </h2>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($stockKontainers as $key => $sk)
                <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <div class="font-mono text-sm font-bold text-gray-900">{{ $sk->nomor_kontainer ?: '-' }}</div>
                            <div class="text-xs text-gray-500">#{{ $stockKontainers->firstItem() + $key }}</div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $sk->status_badge }}">
                            {{ ucfirst($sk->status ?? 'N/A') }}
                        </span>
                    </div>
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ukuran:</span>
                            <span class="font-medium text-gray-900">{{ $sk->ukuran ? $sk->ukuran . ' ft' : '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tipe:</span>
                            <span class="font-medium text-gray-900">{{ $sk->tipe_kontainer ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Gudang:</span>
                            <span class="font-medium text-gray-900">{{ $sk->gudang->nama_gudang ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tgl Masuk:</span>
                            <span class="text-gray-900">{{ $sk->tanggal_masuk ? $sk->tanggal_masuk->format('d/m/Y') : '-' }}</span>
                        </div>
                        @if($sk->tahun_pembuatan)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tahun:</span>
                            <span class="text-gray-900">{{ $sk->tahun_pembuatan }}</span>
                        </div>
                        @endif
                        @if($sk->keterangan)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Keterangan:</span>
                            <span class="text-gray-900 text-right">{{ Str::limit($sk->keterangan, 40) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-6">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p class="text-sm">Tidak ada data stock kontainer di gudang ini</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full table-auto text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No. Kontainer</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Ukuran</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Tipe</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Status</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Gudang</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Tgl Masuk</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Tgl Keluar</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Tahun</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stockKontainers as $key => $sk)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $stockKontainers->firstItem() + $key }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 font-mono font-semibold">{{ $sk->nomor_kontainer ?: '-' }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                            @if($sk->ukuran)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $sk->ukuran == '20' ? 'bg-blue-100 text-blue-800' : ($sk->ukuran == '40' ? 'bg-orange-100 text-orange-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ $sk->ukuran }} ft
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $sk->tipe_kontainer ?: '-' }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $sk->status_badge }}">
                                {{ ucfirst($sk->status ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $sk->gudang->nama_gudang ?? '-' }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $sk->tanggal_masuk ? $sk->tanggal_masuk->format('d/m/Y') : '-' }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $sk->tanggal_keluar ? $sk->tanggal_keluar->format('d/m/Y') : '-' }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $sk->tahun_pembuatan ?: '-' }}</td>
                        <td class="px-2 py-2 text-xs text-gray-900 max-w-xs truncate" title="{{ $sk->keterangan }}">{{ Str::limit($sk->keterangan, 50) ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p>Tidak ada data stock kontainer di gudang {{ $gudang->nama_gudang }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($stockKontainers->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $stockKontainers->links() }}
        </div>
        @endif
    </div>

    {{-- Kontainer Sewa Section --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4 md:mb-6">
        <div class="bg-yellow-600 px-4 py-3 flex items-center justify-between">
            <h2 class="text-sm md:text-base font-semibold text-white">
                <i class="fas fa-box mr-2"></i>Kontainer Sewa ({{ $kontainers->total() }})
            </h2>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($kontainers as $key => $k)
                <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <div class="font-mono text-sm font-bold text-gray-900">{{ $k->nomor_kontainer ?: '-' }}</div>
                            <div class="text-xs text-gray-500">#{{ $kontainers->firstItem() + $key }}</div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $k->status == 'active' ? 'bg-green-100 text-green-800' : ($k->status == 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($k->status ?? 'N/A') }}
                        </span>
                    </div>
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ukuran:</span>
                            <span class="font-medium text-gray-900">{{ $k->ukuran ? $k->ukuran . ' ft' : '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tipe:</span>
                            <span class="font-medium text-gray-900">{{ $k->tipe_kontainer ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Vendor:</span>
                            <span class="font-medium text-gray-900">{{ $k->vendor ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Gudang:</span>
                            <span class="font-medium text-gray-900">{{ $k->gudang->nama_gudang ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Mulai Sewa:</span>
                            <span class="text-gray-900">{{ $k->tanggal_mulai_sewa ? $k->tanggal_mulai_sewa->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Selesai Sewa:</span>
                            <span class="text-gray-900">{{ $k->tanggal_selesai_sewa ? $k->tanggal_selesai_sewa->format('d/m/Y') : '-' }}</span>
                        </div>
                        @if($k->keterangan)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Keterangan:</span>
                            <span class="text-gray-900 text-right">{{ Str::limit($k->keterangan, 40) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-6">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p class="text-sm">Tidak ada data kontainer sewa di gudang ini</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full table-auto text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">No. Kontainer</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Ukuran</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Tipe</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Vendor</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Status</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Gudang</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Mulai Sewa</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Selesai Sewa</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kontainers as $key => $k)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $kontainers->firstItem() + $key }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 font-mono font-semibold">{{ $k->nomor_kontainer ?: '-' }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                            @if($k->ukuran)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $k->ukuran == '20' ? 'bg-blue-100 text-blue-800' : ($k->ukuran == '40' ? 'bg-orange-100 text-orange-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ $k->ukuran }} ft
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $k->tipe_kontainer ?: '-' }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $k->vendor ?: '-' }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $k->status == 'active' ? 'bg-green-100 text-green-800' : ($k->status == 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($k->status ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $k->gudang->nama_gudang ?? '-' }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $k->tanggal_mulai_sewa ? $k->tanggal_mulai_sewa->format('d/m/Y') : '-' }}</td>
                        <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">{{ $k->tanggal_selesai_sewa ? $k->tanggal_selesai_sewa->format('d/m/Y') : '-' }}</td>
                        <td class="px-2 py-2 text-xs text-gray-900 max-w-xs truncate" title="{{ $k->keterangan }}">{{ Str::limit($k->keterangan, 50) ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p>Tidak ada data kontainer sewa di gudang {{ $gudang->nama_gudang }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kontainers->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $kontainers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
