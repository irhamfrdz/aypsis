@extends('layouts.app')

@section('title', 'History Pergerakan Kontainer')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">History Pergerakan Kontainer</h1>
        <p class="mt-2 text-sm text-gray-500">Pantau seluruh riwayat log pergerakan kontainer dalam satu dashboard.</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 transition-all hover:shadow-md">
        <form action="{{ route('history-kontainer.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Cari Nomor Kontainer</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-500">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                            class="block w-full pl-11 pr-4 py-2.5 bg-gray-50 border-gray-200 text-gray-900 text-sm rounded-xl border-0 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 transition-all" 
                            placeholder="Contoh: BEAU123456...">
                    </div>
                </div>

                <!-- Jenis Kegiatan -->
                <div>
                    <label for="jenis_kegiatan" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jenis Kegiatan</label>
                    <div class="relative">
                        <select name="jenis_kegiatan" id="jenis_kegiatan" 
                            class="block w-full pl-4 pr-10 py-2.5 bg-gray-50 border-0 ring-1 ring-inset ring-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-600 transition-all appearance-none cursor-pointer">
                            <option value="">Semua Kegiatan</option>
                            <option value="Masuk" {{ request('jenis_kegiatan') == 'Masuk' ? 'selected' : '' }}>Masuk</option>
                            <option value="Keluar" {{ request('jenis_kegiatan') == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <label for="start_date" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                        class="block w-full px-4 py-2.5 bg-gray-50 border-0 ring-1 ring-inset ring-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-600 transition-all cursor-pointer">
                </div>

                <!-- Tanggal Akhir -->
                <div>
                    <label for="end_date" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                        class="block w-full px-4 py-2.5 bg-gray-50 border-0 ring-1 ring-inset ring-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-600 transition-all cursor-pointer">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('history-kontainer.index') }}" 
                    class="inline-flex items-center px-6 py-2.5 border border-gray-200 text-sm font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all">
                    Reset
                </a>
                <button type="submit" 
                    class="inline-flex items-center px-8 py-2.5 border border-transparent text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data Kontainer</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Tipe</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Kegiatan</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Gudang / Lokasi</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detail Keterangan</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Oleh</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($histories as $history)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $history->tanggal_kegiatan->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400 font-medium">{{ $history->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-blue-600 group-hover:text-blue-700 tracking-wider">
                                    {{ $history->nomor_kontainer }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $tipeClass = match($history->tipe_kontainer) {
                                        'kontainer' => 'bg-blue-50 text-blue-600 border-blue-100',
                                        'stock' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'lcl' => 'bg-purple-50 text-purple-600 border-purple-100',
                                        default => 'bg-gray-50 text-gray-500 border-gray-100',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border {{ $tipeClass }}">
                                    {{ ucfirst($history->tipe_kontainer ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $kegiatanClass = match($history->jenis_kegiatan) {
                                        'Masuk' => 'bg-green-100 text-green-700',
                                        'Keluar' => 'bg-rose-100 text-rose-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold {{ $kegiatanClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $history->jenis_kegiatan == 'Masuk' ? 'bg-green-500' : 'bg-rose-500' }}"></span>
                                    {{ $history->jenis_kegiatan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 mr-3">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $history->gudang->nama_gudang ?? '-' }}</div>
                                        @if($history->gudang && $history->gudang->lokasi)
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">{{ $history->gudang->lokasi }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600 line-clamp-2 max-w-xs break-words italic">
                                    {{ $history->keterangan ?? '-' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-7 w-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-[10px] font-bold mr-2">
                                        {{ strtoupper(substr($history->creator->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-medium text-gray-500">{{ $history->creator->name ?? 'System' }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="h-12 w-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-gray-400 font-medium">Tidak ada data history pergerakan kontainer.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $histories->withQueryString()->links() }}
    </div>
</div>

<style>
    /* Custom table hover effect */
    .group:hover td {
        border-color: transparent !important;
    }
    
    /* Smooth transition for pagination */
    .pagination {
        display: flex;
        gap: 0.25rem;
    }
</style>
@endsection
