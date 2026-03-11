@extends('layouts.app')

@section('title', 'Dashboard Cek Kendaraan Harian')
@section('page_title', 'Dashboard Cek Kendaraan Harian')

@section('content')
<div class="p-4 sm:p-6 max-w-[1600px] mx-auto">
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Status Kendaraan <span class="text-blue-600">Harian</span></h1>
            <p class="text-gray-500 mt-2 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-400"></i>
                Memantau kelengkapan pengecekan harian oleh seluruh supir armada
            </p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.cek-kendaraan.daily') }}" method="GET" class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <input type="date" name="date" value="{{ $date }}" 
                    class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-2xl shadow-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-sm font-bold text-gray-700 outline-none" 
                    onchange="this.form.submit()">
            </form>
            <button onclick="window.location.reload()" class="p-2.5 bg-white border border-gray-200 rounded-2xl shadow-sm hover:bg-gray-50 text-gray-600 transition-all tooltip" title="Refresh Data">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    @php
        $completedCount = $drivers->filter(fn($d) => $checksForDate->has($d->id))->count();
        $totalDrivers = $drivers->count();
        $pendingCount = $totalDrivers - $completedCount;
        $completionRate = $totalDrivers > 0 ? round(($completedCount / $totalDrivers) * 100) : 0;
    @endphp

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Supir --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Supir</p>
                    <h3 class="text-3xl font-black text-gray-800">{{ $totalDrivers }}</h3>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-500">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-gray-500">
                <span class="px-2 py-0.5 bg-gray-100 rounded-full mr-2">Driver Aktif</span>
            </div>
        </div>

        {{-- Selesai Cek --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-1">Selesai Cek</p>
                    <h3 class="text-3xl font-black text-emerald-600">{{ $completedCount }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-emerald-600">
                <i class="fas fa-arrow-up mr-1 text-[10px]"></i> {{ $completionRate }}% Kapasitas harian
            </div>
        </div>

        {{-- Belum Cek --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-rose-400 uppercase tracking-widest mb-1">Belum Cek</p>
                    <h3 class="text-3xl font-black text-rose-500">{{ $pendingCount }}</h3>
                </div>
                <div class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center text-rose-500">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-rose-500">
                Membutuhkan tindakan segera
            </div>
        </div>

        {{-- Progress Circle --}}
        <div class="bg-blue-600 p-6 rounded-3xl shadow-lg shadow-blue-200 group overflow-hidden relative">
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="relative flex items-center justify-between h-full">
                <div>
                    <p class="text-xs font-bold text-blue-100 uppercase tracking-widest mb-1">Progress</p>
                    <h3 class="text-4xl font-black text-white leading-none">{{ $completionRate }}%</h3>
                </div>
                <div class="relative w-16 h-16 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="32" cy="32" r="28" stroke="rgba(255,255,255,0.2)" stroke-width="6" fill="transparent" />
                        <circle cx="32" cy="32" r="28" stroke="white" stroke-width="6" fill="transparent"
                            stroke-dasharray="175.9" stroke-dashoffset="{{ 175.9 - (175.9 * $completionRate / 100) }}"
                            stroke-linecap="round" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table Section --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </div>
                <input type="text" id="driverSearch" placeholder="Cari nama supir atau NIK..." 
                    class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium outline-none">
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-400 mr-2 uppercase">Filter Status:</span>
                <button onclick="filterStatus('all')" class="status-btn active px-4 py-2 rounded-xl text-xs font-bold transition-all bg-blue-50 text-blue-600">Semua</button>
                <button onclick="filterStatus('selesai')" class="status-btn px-4 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:bg-gray-50">Selesai</button>
                <button onclick="filterStatus('pending')" class="status-btn px-4 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:bg-gray-50">Belum</button>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden lg:block overflow-x-auto">
            <table id="driverTable" class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-400 text-[10px] uppercase font-black tracking-[0.2em]">
                        <th class="px-8 py-5">Informasi Supir</th>
                        <th class="px-8 py-5">Kendaraan</th>
                        <th class="px-8 py-5">Odometer</th>
                        <th class="px-8 py-5">Status & Waktu</th>
                        <th class="px-8 py-5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($drivers as $driver)
                        @php
                            $driverChecks = $checksForDate->get($driver->id);
                            $check = $driverChecks ? $driverChecks->first() : null;
                            $status = $check ? 'selesai' : 'pending';
                        @endphp
                        <tr class="driver-row hover:bg-blue-50/30 transition-colors group" data-status="{{ $status }}" data-search="{{ strtolower($driver->nama_lengkap . ' ' . $driver->nik) }}">
                            <td class="px-8 py-5">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $check ? 'from-blue-500 to-indigo-600 text-white' : 'from-gray-100 to-gray-200 text-gray-500' }} flex items-center justify-center font-black text-lg shadow-sm group-hover:scale-110 transition-transform">
                                        {{ substr($driver->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-bold text-gray-800 leading-tight">{{ $driver->nama_lengkap }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">NIK: {{ $driver->nik ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                @if($check)
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-blue-500 mr-2"></div>
                                        <span class="text-sm font-black text-gray-700">{{ $check->mobil->nomor_polisi ?? '-' }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">{{ $check->mobil->merk ?? '' }} {{ $check->mobil->tipe ?? '' }}</p>
                                @else
                                    <span class="text-xs text-gray-300 font-medium italic">Data belum tersedia</span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                @if($check && $check->odometer)
                                    <span class="text-sm font-black text-gray-700">{{ number_format($check->odometer, 0, ',', '.') }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 ml-1">KM</span>
                                @else
                                    <span class="text-xs text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                @if($check)
                                    <div class="inline-flex flex-col">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-700 tracking-wider mb-1 flex items-center w-fit">
                                            <i class="fas fa-check-circle mr-1"></i> Selesai
                                        </span>
                                        <span class="text-xs font-bold text-gray-500">{{ \Carbon\Carbon::parse($check->jam)->format('H:i') }} WIB</span>
                                    </div>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-700 tracking-wider flex items-center w-fit">
                                        <i class="fas fa-clock mr-1"></i> Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                @if($check)
                                    <a href="{{ route('admin.cek-kendaraan.show', $check->id) }}" target="_blank" 
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @else
                                    <button disabled class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 text-gray-300 cursor-not-allowed">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center border-4 border-white shadow-inner mb-4">
                                        <i class="fas fa-users-slash text-3xl text-gray-200"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-400">Tidak ada data driver ditemukan</h3>
                                    <p class="text-gray-300 text-sm mt-1">Pastikan divisi/pekerjaan driver sudah diatur dengan benar.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="lg:hidden p-4 grid grid-cols-1 gap-4 bg-gray-50/50" id="mobileCards">
            @forelse($drivers as $driver)
                @php
                    $driverChecks = $checksForDate->get($driver->id);
                    $check = $driverChecks ? $driverChecks->first() : null;
                    $status = $check ? 'selesai' : 'pending';
                @endphp
                <div class="driver-row bg-white p-5 rounded-2xl border border-gray-100 shadow-sm transition-all active:scale-[0.98]" data-status="{{ $status }}" data-search="{{ strtolower($driver->nama_lengkap . ' ' . $driver->nik) }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $check ? 'from-blue-500 to-indigo-600' : 'from-gray-100 to-gray-200 text-gray-400' }} text-white flex items-center justify-center font-black text-sm shadow-sm">
                                @if($check)
                                    {{ substr($driver->nama_lengkap, 0, 1) }}
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-bold text-gray-800 leading-tight">{{ $driver->nama_lengkap }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">NIK: {{ $driver->nik ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($check)
                            <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase bg-emerald-100 text-emerald-700 tracking-wider">
                                <i class="fas fa-check-circle mr-1"></i> Selesai
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase bg-rose-100 text-rose-700 tracking-wider">
                                <i class="fas fa-clock mr-1"></i> Menunggu
                            </span>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.1em] mb-1">Kendaraan</p>
                            <p class="text-xs font-black text-gray-700">{{ $check->mobil->nomor_polisi ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.1em] mb-1">Waktu Cek</p>
                            <p class="text-xs font-black text-gray-700">{{ $check ? \Carbon\Carbon::parse($check->jam)->format('H:i') . ' WIB' : '-' }}</p>
                        </div>
                    </div>

                    @if($check)
                        <a href="{{ route('admin.cek-kendaraan.show', $check->id) }}" target="_blank" class="w-full flex items-center justify-center py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-100 transition-all hover:bg-blue-700 active:transform active:scale-95">
                            <i class="fas fa-eye mr-2"></i> Lihat Detail Pengecekan
                        </a>
                    @else
                        <button disabled class="w-full flex items-center justify-center py-2.5 bg-gray-100 text-gray-400 rounded-xl text-xs font-bold cursor-not-allowed">
                            <i class="fas fa-eye-slash mr-2"></i> Belum Melakukan Cek
                        </button>
                    @endif
                </div>
            @empty
                <div class="py-10 text-center col-span-full">
                    <p class="text-sm font-bold text-gray-400">Tidak ada data driver ditemukan</p>
                </div>
            @endforelse
        </div>

        @if($drivers->count() > 0)
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 text-[10px] font-black text-gray-400 flex flex-col sm:flex-row justify-between items-center uppercase tracking-[0.2em] gap-2">
            <span>{{ $drivers->count() }} Driver Armada Terdaftar</span>
            <span class="flex items-center"><i class="fas fa-sync-alt mr-2 text-xs text-blue-400"></i> Terakhir Diperbarui: {{ now()->format('H:i') }} WIB</span>
        </div>
        @endif
    </div>
</div>

<style>
    .sidebar-search-highlight {
        background-color: #fef08a;
        color: #854d0e;
        border-radius: 2px;
        padding: 0 1px;
    }
    
    .status-btn.active {
        background-color: rgba(59, 130, 246, 0.1);
        color: #2563eb;
    }

    /* Custom Scrollbar for overflow-x-auto */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>

@push('scripts')
<script>
    function filterStatus(status) {
        // Update button styles
        document.querySelectorAll('.status-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-50', 'text-blue-600');
            btn.classList.add('text-gray-500', 'hover:bg-gray-50');
        });
        
        const activeBtn = event.target;
        activeBtn.classList.add('active', 'bg-blue-50', 'text-blue-600');
        activeBtn.classList.remove('text-gray-500', 'hover:bg-gray-50');

        // Filter table rows
        const rows = document.querySelectorAll('.driver-row');
        rows.forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    document.getElementById('driverSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.driver-row');
        
        rows.forEach(row => {
            const searchText = row.dataset.search;
            if (searchText.includes(term)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection
