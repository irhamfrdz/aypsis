@extends('layouts.app')

@section('title', 'Master Kartu Bensin Batam')
@section('page_title', 'Master Kartu Bensin Batam')

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
        <!-- Total Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Total Kartu</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center text-white">
                    <i class="fas fa-credit-card"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-blue-900">{{ number_format($totalCards, 0, ',', '.') }}</div>
            <p class="text-xs text-blue-600 mt-1">Total kartu terdaftar</p>
        </div>

        <!-- Active Card -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-green-600 uppercase tracking-wider">Kartu Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center text-white">
                    <i class="fas fa-check"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-green-900">{{ number_format($activeCards, 0, ',', '.') }}</div>
            <p class="text-xs text-green-600 mt-1">Kartu berstatus aktif</p>
        </div>

        <!-- Inactive Card -->
        <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-red-600 uppercase tracking-wider">Tidak Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-red-500 flex items-center justify-center text-white">
                    <i class="fas fa-ban"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-red-900">{{ number_format($inactiveCards, 0, ',', '.') }}</div>
            <p class="text-xs text-red-600 mt-1">Kartu tidak aktif / diblokir</p>
        </div>
    </div>

    <!-- Filters & Actions Area -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <form method="GET" action="{{ route('master-kartu-bensin-batam.index') }}" class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <!-- Left Side: Search and Status Filter -->
            <div class="flex flex-1 flex-col sm:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Cari</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nomor kartu, nama, provider, supir, nopol..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    </div>
                </div>

                <!-- Status Select -->
                <div class="w-full sm:w-48">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <!-- Right Side: Filter Actions & Add Button -->
            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                    Filter
                </button>
                <a href="{{ route('master-kartu-bensin-batam.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                    Reset
                </a>
                @can('master-kartu-bensin-batam-create')
                <a href="{{ route('master-kartu-bensin-batam.create') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition flex items-center gap-2">
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
                        <th class="px-6 py-4">Nomor Kartu</th>
                        <th class="px-6 py-4">Nama Kartu</th>
                        <th class="px-6 py-4">Provider</th>
                        <th class="px-6 py-4">Kendaraan (No Polisi)</th>
                        <th class="px-6 py-4">Supir / Driver</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-blue-600">{{ $item->nomor_kartu }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $item->nama_kartu }}</td>
                        <td class="px-6 py-4">{{ $item->provider }}</td>
                        <td class="px-6 py-4">
                            @if($item->mobil)
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-800 rounded-md font-semibold text-xs border border-gray-200">
                                {{ $item->mobil->nomor_polisi }}
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($item->karyawan)
                            <span class="font-medium text-gray-800">
                                {{ $item->karyawan->nama_lengkap }}
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($item->status == 'aktif')
                            <span class="px-2.5 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                Aktif
                            </span>
                            @else
                            <span class="px-2.5 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                Tidak Aktif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="{{ $item->keterangan }}">{{ $item->keterangan ?? '-' }}</td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="flex justify-center items-center gap-3">
                                @can('master-kartu-bensin-batam-edit')
                                <a href="{{ route('master-kartu-bensin-batam.edit', $item->id) }}" class="text-blue-600 hover:text-blue-900 transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('master-kartu-bensin-batam-delete')
                                <form action="{{ route('master-kartu-bensin-batam.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kartu bensin ini?')" class="inline">
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
                        <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                            <i class="fas fa-folder-open text-3xl mb-3 block text-gray-300"></i>
                            Tidak ada data kartu bensin Batam ditemukan.
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
