@extends('layouts.app')

@section('title', 'Pembelian BBM Batam')
@section('page_title', 'Pembelian BBM Batam')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-4" role="alert">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2 text-xl"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Liter Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Volume</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center text-white">
                    <i class="fas fa-gas-pump"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-blue-900">{{ number_format($totalLiters, 2, ',', '.') }} L</div>
            <p class="text-xs text-blue-600 mt-1">Total BBM dibeli</p>
        </div>

        <!-- Total Biaya Card -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-green-600 uppercase tracking-wider">Total Pembelian</span>
                <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center text-white">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-green-900">Rp {{ number_format($totalCost, 0, ',', '.') }}</div>
            <p class="text-xs text-green-600 mt-1">Total pengeluaran</p>
        </div>

        <!-- Rata-rata Harga Card -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Rata-rata Harga</span>
                <div class="w-8 h-8 rounded-lg bg-purple-500 flex items-center justify-center text-white">
                    <i class="fas fa-tag"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-purple-900">Rp {{ number_format($averagePrice, 2, ',', '.') }}</div>
            <p class="text-xs text-purple-600 mt-1">Per Liter</p>
        </div>
    </div>

    <!-- Filters & Actions Area -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <form method="GET" action="{{ route('pembelian-bbm-batam.index') }}" class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <!-- Left Side: Search and Date Filter -->
            <div class="flex flex-1 flex-col sm:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Cari</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari bukti, nota, supplier, keterangan..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    </div>
                </div>

                <!-- Date Range -->
                <div class="flex gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                               class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Sampai</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                               class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
            </div>

            <!-- Right Side: Filter Actions & Add Button -->
            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                    Filter
                </button>
                <a href="{{ route('pembelian-bbm-batam.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                    Reset
                </a>
                @can('pembelian-bbm-batam-create')
                <a href="{{ route('pembelian-bbm-batam.create') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah
                </a>
                @endcan
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4">No Bukti</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Supplier</th>
                        <th class="px-6 py-4">No Nota</th>
                        <th class="px-6 py-4 text-right">Volume</th>
                        <th class="px-6 py-4 text-right">Harga/Liter</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-700">Total Harga</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-blue-600">{{ $item->nomor_bukti }}</td>
                        <td class="px-6 py-4">{{ $item->tanggal ? $item->tanggal->format('d-m-Y') : '-' }}</td>
                        <td class="px-6 py-4">{{ $item->supplier ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $item->nomor_nota ?? '-' }}</td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900">{{ number_format($item->jumlah_liter, 2, ',', '.') }} L</td>
                        <td class="px-6 py-4 text-right text-gray-500">Rp {{ number_format($item->harga_per_liter, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right font-bold text-gray-950">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="{{ $item->keterangan }}">{{ $item->keterangan ?? '-' }}</td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="flex justify-center items-center gap-3">
                                @can('pembelian-bbm-batam-edit')
                                <a href="{{ route('pembelian-bbm-batam.edit', $item->id) }}" class="text-blue-600 hover:text-blue-900 transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('pembelian-bbm-batam-delete')
                                <form action="{{ route('pembelian-bbm-batam.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pembelian ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-10 text-center text-gray-500">
                            <i class="fas fa-folder-open text-3xl mb-3 block text-gray-300"></i>
                            Tidak ada data pembelian BBM Batam ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
