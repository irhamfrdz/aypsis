@extends('layouts.app')

@section('title', 'Master Jadwal Kapal Berlabuh')
@section('page_title', 'Master Jadwal Kapal Berlabuh')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-7xl">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 bg-sky-100 text-sky-700 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </span>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Jadwal Kapal Berlabuh</h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola jadwal kedatangan, closing cargo, ETD & ETA kapal berlabuh</p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('master-jadwal-kapal-berlabuh-export')
                <a href="{{ route('master-jadwal-kapal-berlabuh.export', request()->query()) }}"
                   class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
            @endcan

            @can('master-jadwal-kapal-berlabuh-create')
                <a href="{{ route('master-jadwal-kapal-berlabuh.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Jadwal
                </a>
            @endcan
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jadwal</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-green-600 uppercase tracking-wider">Aktif / Berjalan</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['aktif'] }}</p>
            </div>
            <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['selesai'] }}</p>
            </div>
            <div class="p-3 bg-gray-50 text-gray-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-red-500 uppercase tracking-wider">Batal</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['batal'] }}</p>
            </div>
            <div class="p-3 bg-red-50 text-red-500 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('master-jadwal-kapal-berlabuh.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Pencarian</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kapal / voyage..."
                           class="w-full pl-8 pr-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <svg class="w-4 h-4 absolute left-2.5 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Filter Pelabuhan -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Pelabuhan / Rute</label>
                <select name="pelabuhan" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Semua Pelabuhan --</option>
                    @foreach($pelabuhanList as $p)
                        <option value="{{ $p }}" {{ request('pelabuhan') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kapal -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Kapal</label>
                <select name="nama_kapal" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Semua Kapal --</option>
                    @foreach($kapalList as $k)
                        <option value="{{ $k }}" {{ request('nama_kapal') == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Semua Status --</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 px-3 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs font-semibold transition-all duration-200">
                    Filter
                </button>
                <a href="{{ route('master-jadwal-kapal-berlabuh.index') }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition-all duration-200 text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Main Tables Grouped by Pelabuhan / Rute -->
    @php
        $groupedJadwals = $jadwals->getCollection()->groupBy('pelabuhan');
    @endphp

    @forelse($groupedJadwals as $pelabuhanName => $items)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
            <!-- Banner Pelabuhan / Top Header -->
            <div class="bg-sky-500 text-white font-bold py-2.5 px-4 text-sm sm:text-base tracking-wider flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-sky-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="uppercase font-black tracking-wider text-base sm:text-lg">{{ $pelabuhanName ?: 'TANPA PELABUHAN' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs bg-sky-700 bg-opacity-70 px-2.5 py-0.5 rounded-full font-medium text-white">
                        {{ $items->count() }} Jadwal Kapal
                    </span>
                    @can('master-jadwal-kapal-berlabuh-create')
                        <a href="{{ route('master-jadwal-kapal-berlabuh.create', ['pelabuhan' => $pelabuhanName]) }}"
                           class="inline-flex items-center px-2.5 py-1 bg-white hover:bg-sky-50 text-sky-700 text-xs font-bold rounded-md shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            + Tambah Jadwal
                        </a>
                    @endcan
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
                    <thead>
                        <tr class="text-gray-800 font-bold uppercase tracking-wider text-xs">
                            <th class="px-4 py-3 bg-gray-50 text-center border-b border-gray-200 w-14">No</th>
                            <th class="px-5 py-3 bg-gray-50 text-left border-b border-gray-200">Nama Kapal</th>
                            <th class="px-5 py-3 bg-sky-100 text-center text-sky-950 border-b border-sky-200 font-extrabold w-36">Close</th>
                            <th class="px-5 py-3 bg-yellow-300 text-center text-amber-950 border-b border-yellow-400 font-black w-36">ETD</th>
                            <th class="px-5 py-3 bg-red-600 text-center text-white border-b border-red-700 font-black w-36">ETA</th>
                            <th class="px-4 py-3 bg-gray-50 text-center border-b border-gray-200 w-28">Voyage</th>
                            <th class="px-4 py-3 bg-gray-50 text-center border-b border-gray-200 w-28">Status</th>
                            <th class="px-4 py-3 bg-gray-50 text-center border-b border-gray-200 w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $index => $item)
                            <tr class="hover:bg-sky-50/40 transition-colors">
                                <td class="px-4 py-3.5 text-center text-gray-600 font-bold whitespace-nowrap">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-5 py-3.5 font-bold text-gray-900 whitespace-nowrap">
                                    <a href="{{ route('master-jadwal-kapal-berlabuh.show', $item->id) }}" class="text-blue-700 hover:text-blue-900 hover:underline">
                                        {{ $item->nama_kapal }}
                                    </a>
                                    @if($item->keterangan)
                                        <p class="text-[11px] font-normal text-gray-400 truncate max-w-xs mt-0.5">{{ $item->keterangan }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center font-semibold text-sky-950 bg-sky-50/40 whitespace-nowrap">
                                    {{ $item->tanggal_closing ? \Carbon\Carbon::parse($item->tanggal_closing)->format('d-M-y') : '-' }}
                                </td>
                                <td class="px-5 py-3.5 text-center font-bold text-amber-950 bg-yellow-50 whitespace-nowrap">
                                    {{ $item->tanggal_etd ? \Carbon\Carbon::parse($item->tanggal_etd)->format('d-M-y') : '-' }}
                                </td>
                                <td class="px-5 py-3.5 text-center font-extrabold text-red-700 bg-red-50 whitespace-nowrap">
                                    {{ $item->tanggal_eta ? \Carbon\Carbon::parse($item->tanggal_eta)->format('d-M-y') : '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-center text-gray-600 whitespace-nowrap">
                                    {{ $item->no_voyage ?? '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    {!! $item->status_badge !!}
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('master.wa-broadcast.create', ['nama_kapal' => $item->nama_kapal, 'no_voyage' => $item->no_voyage, 'type' => 'jadwal']) }}"
                                           class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded transition" title="Broadcast WhatsApp Jadwal ke Shipper">
                                            <i class="fab fa-whatsapp text-sm"></i>
                                        </a>

                                        <a href="{{ route('master-jadwal-kapal-berlabuh.show', $item->id) }}"
                                           class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>

                                        @can('master-jadwal-kapal-berlabuh-update')
                                            <a href="{{ route('master-jadwal-kapal-berlabuh.edit', $item->id) }}"
                                               class="p-1.5 text-amber-600 hover:bg-amber-50 rounded transition" title="Edit Jadwal">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        @endcan

                                        @can('master-jadwal-kapal-berlabuh-delete')
                                            <form action="{{ route('master-jadwal-kapal-berlabuh.destroy', $item->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal {{ $item->nama_kapal }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition" title="Hapus Jadwal">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center text-gray-500 mb-6">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <p class="font-bold text-base text-gray-700">Belum ada data jadwal kapal berlabuh</p>
            <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Jadwal" untuk menambahkan jadwal baru.</p>
        </div>
    @endforelse

    <!-- Pagination -->
    @if($jadwals->hasPages())
        <div class="px-4 py-3 border border-gray-200 rounded-xl bg-white shadow-sm mb-6">
            {{ $jadwals->links() }}
        </div>
    @endif
</div>
@endsection
