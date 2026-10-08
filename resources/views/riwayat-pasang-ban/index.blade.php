@extends('layouts.app')

@section('title', 'Riwayat Pasang Ban')
@section('page_title', 'Riwayat Pasang Ban')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center p-2 bg-green-100 text-green-700 rounded-lg">
                        <i class="fas fa-history text-lg"></i>
                    </span>
                    <h1 class="text-2xl font-bold text-gray-800">Riwayat Pasang Ban</h1>
                </div>
                <p class="text-gray-600 mt-1 text-sm">Log riwayat aktivitas pemasangan, pelepasan, dan rotasi ban pada kendaraan</p>
            </div>
            <div class="flex items-center space-x-2">
                @can('stock-ban-view')
                <a href="{{ route('stock-ban.index') }}" class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                    <i class="fas fa-cubes mr-2 text-gray-500"></i>Lihat Stock Ban
                </a>
                @endcan
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-100">
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200/60">
                <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Riwayat</span>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalLogs) }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-3 border border-green-200/60">
                <span class="text-xs text-green-700 font-medium uppercase tracking-wider">Pasang Ban</span>
                <p class="text-2xl font-bold text-green-700 mt-1">{{ number_format($totalPasang) }}</p>
            </div>
            <div class="bg-red-50 rounded-lg p-3 border border-red-200/60">
                <span class="text-xs text-red-700 font-medium uppercase tracking-wider">Copot Ban</span>
                <p class="text-2xl font-bold text-red-700 mt-1">{{ number_format($totalCopot) }}</p>
            </div>
            <div class="bg-amber-50 rounded-lg p-3 border border-amber-200/60">
                <span class="text-xs text-amber-800 font-medium uppercase tracking-wider">Ban Pinjaman</span>
                <p class="text-2xl font-bold text-amber-700 mt-1">{{ number_format($totalPinjaman) }}</p>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('riwayat-pasang-ban.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pencarian</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="No Seri, Plat/KIR, Posisi Roda, Catatan..."
                               class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <!-- Unit / Mobil -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Unit Kendaraan</label>
                    <select name="mobil_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Semua Mobil</option>
                        @foreach($mobils as $m)
                            <option value="{{ $m->id }}" {{ request('mobil_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nomor_polisi ?: ($m->no_kir ? 'KIR: '.$m->no_kir : 'Mobil #'.$m->id) }}{{ $m->nomor_polisi && $m->no_kir ? ' (KIR: '.$m->no_kir.')' : '' }}{{ $m->jenis ? ' - '.$m->jenis : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Aksi -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Aksi</label>
                    <select name="action" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Semua Aksi</option>
                        <option value="pasang" {{ request('action') == 'pasang' ? 'selected' : '' }}>Pasang</option>
                        <option value="copot" {{ request('action') == 'copot' ? 'selected' : '' }}>Copot</option>
                        <option value="tukar" {{ request('action') == 'tukar' ? 'selected' : '' }}>Tukar</option>
                        <option value="kembalikan_pinjaman" {{ request('action') == 'kembalikan_pinjaman' ? 'selected' : '' }}>Kembalikan Pinjaman</option>
                        <option value="lepas_semua" {{ request('action') == 'lepas_semua' ? 'selected' : '' }}>Lepas Semua</option>
                    </select>
                </div>

                <!-- Status Pinjaman -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kepemilikan</label>
                    <select name="is_borrowed" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Semua Status</option>
                        <option value="0" {{ request('is_borrowed') === '0' ? 'selected' : '' }}>Milik Sendiri</option>
                        <option value="1" {{ request('is_borrowed') === '1' ? 'selected' : '' }}>Pinjaman</option>
                    </select>
                </div>

                <!-- Rentang Tanggal: Dari -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date"
                           name="start_date"
                           value="{{ request('start_date') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Rentang Tanggal: Sampai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date"
                           name="end_date"
                           value="{{ request('end_date') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('riwayat-pasang-ban.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                    <i class="fas fa-undo mr-1"></i>Reset
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-sm font-medium transition duration-200">
                    <i class="fas fa-filter mr-1"></i>Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3 text-left">Waktu & Tanggal</th>
                        <th class="px-4 py-3 text-left">Unit Kendaraan</th>
                        <th class="px-4 py-3 text-center">Posisi Roda</th>
                        <th class="px-4 py-3 text-left">No. Seri Ban</th>
                        <th class="px-4 py-3 text-left">Merk / Ukuran</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                        <th class="px-4 py-3 text-left">Kepemilikan</th>
                        <th class="px-4 py-3 text-left">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($logs as $index => $log)
                    <tr class="hover:bg-gray-50/80 transition duration-150">
                        <td class="px-4 py-3 text-center text-gray-500 text-xs">
                            {{ $logs->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                            <div class="font-medium text-xs">{{ $log->created_at ? $log->created_at->format('d M Y') : '-' }}</div>
                            <div class="text-[11px] text-gray-400">{{ $log->created_at ? $log->created_at->format('H:i:s') : '' }} WIB</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($log->category === 'alat_berat' || $log->alat_berat_id)
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-block p-1 bg-amber-100 text-amber-800 rounded text-xs"><i class="fas fa-truck-pickup"></i></span>
                                    <div>
                                        <div class="font-semibold text-gray-800 text-xs">
                                            {{ $log->alatBerat ? $log->alatBerat->nama : 'Alat Berat #'.$log->alat_berat_id }}
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[11px] text-gray-400">
                                            @if($log->alatBerat && $log->alatBerat->kode_alat)
                                                <span class="font-mono">{{ $log->alatBerat->kode_alat }}</span>
                                            @endif
                                            @if($log->alatBerat && $log->alatBerat->jenis)
                                                <span class="text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200/60 text-[10px]">{{ $log->alatBerat->jenis }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-block p-1 bg-blue-100 text-blue-800 rounded text-xs"><i class="fas fa-truck"></i></span>
                                    <div>
                                        @if($log->mobil)
                                            <div class="font-semibold text-gray-800 text-xs">
                                                {{ $log->mobil->nomor_polisi ?: ($log->mobil->no_kir ? 'KIR: '.$log->mobil->no_kir : 'Mobil #'.$log->mobil_id) }}
                                            </div>
                                            <div class="flex items-center gap-1.5 text-[11px] text-gray-500">
                                                @if($log->mobil->nomor_polisi && $log->mobil->no_kir)
                                                    <span class="font-mono">KIR: {{ $log->mobil->no_kir }}</span>
                                                @endif
                                                @if($log->mobil->jenis)
                                                    <span class="text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200/60 text-[10px]">{{ $log->mobil->jenis }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="font-semibold text-gray-800 text-xs">{{ $log->mobil_id ? 'Mobil #'.$log->mobil_id : '-' }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $log->posisi_roda }}
                            </span>
                            @if($log->wheel_code && $log->posisi_roda !== $log->wheel_code)
                                <span class="block text-[10px] text-gray-400 font-mono mt-0.5">{{ $log->wheel_code }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-mono text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200/60">
                                {{ $log->nomor_seri ?: '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-700">
                            @if($log->stockBan)
                                <div class="font-medium text-gray-900">
                                    {{ $log->stockBan->merk ?: ($log->stockBan->merkBan->nama ?? '-') }}
                                </div>
                                <div class="text-[11px] text-gray-500">
                                    {{ $log->stockBan->ukuran ?: ($log->stockBan->namaStockBan ? $log->stockBan->namaStockBan->nama : '') }}
                                </div>
                            @else
                                <span class="text-gray-400 italic text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @if($log->action === 'pasang')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-arrow-down mr-1 text-[10px]"></i> Pasang
                                </span>
                            @elseif($log->action === 'copot')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <i class="fas fa-arrow-up mr-1 text-[10px]"></i> Copot
                                </span>
                            @elseif($log->action === 'tukar')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    <i class="fas fa-exchange-alt mr-1 text-[10px]"></i> Tukar
                                </span>
                            @elseif($log->action === 'kembalikan_pinjaman')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    <i class="fas fa-undo-alt mr-1 text-[10px]"></i> Kembali Pinjaman
                                </span>
                            @elseif($log->action === 'lepas_semua')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                    <i class="fas fa-times-circle mr-1 text-[10px]"></i> Lepas Semua
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    {{ ucfirst($log->action ?: '-') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs">
                            @if($log->is_borrowed)
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200">
                                    <i class="fas fa-hand-holding text-[10px] text-amber-600"></i>
                                    <span>Pinjaman</span>
                                </div>
                                @if($log->donor_unit_name)
                                    <div class="text-[11px] text-amber-900 font-medium mt-0.5">
                                        Dari: <span class="underline">{{ $log->donor_unit_name }}</span>
                                    </div>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <i class="fas fa-check-circle text-[10px] text-emerald-600"></i>
                                    <span>Milik Sendiri</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 max-w-xs truncate" title="{{ $log->notes }}">
                            {{ $log->notes ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                <p class="text-base font-medium text-gray-600">Belum ada data riwayat pemasangan ban</p>
                                <p class="text-xs text-gray-400 mt-1">Data akan otomatis dicatat setiap kali ban dipasang atau dicopot dari unit kendaraan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
