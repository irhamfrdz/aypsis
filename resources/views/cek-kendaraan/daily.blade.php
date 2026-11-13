@extends('layouts.app')

@section('title', 'Dashboard Cek Kendaraan Harian')
@section('page_title', 'Dashboard Cek Kendaraan Harian')

@section('content')
<div class="daily-dashboard p-4 sm:p-6 max-w-[1600px] mx-auto">
    {{-- Header Section --}}
    <div class="daily-header mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Status Kendaraan <span class="text-blue-600">Harian</span></h1>
            <p class="text-gray-500 mt-2 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-400"></i>
                Memantau kelengkapan pengecekan harian oleh seluruh supir armada
            </p>
        </div>
        <div class="daily-actions flex items-center gap-3">
            <form action="{{ route('admin.cek-kendaraan.daily') }}" method="GET" class="daily-date-control relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <span class="daily-date-display" aria-hidden="true">{{ \Carbon\Carbon::parse($date)->format('d/M/Y') }}</span>
                <input aria-label="Tanggal monitoring" type="date" name="date" value="{{ $date }}"
                    class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-2xl shadow-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-sm font-bold text-gray-700 outline-none"
                    onchange="this.form.submit()">
                @if(isset($cabang))
                    <input type="hidden" name="cabang" value="{{ $cabang }}">
                @endif
            </form>
            <a href="{{ route('admin.cek-kendaraan.daily.export', ['date' => $date, 'cabang' => $cabang ?? null]) }}" class="px-4 py-2.5 bg-emerald-600 text-white rounded-2xl shadow-sm hover:bg-emerald-700 transition-all text-sm font-bold flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
            <button aria-label="Refresh data" onclick="window.location.reload()" class="p-2.5 bg-white border border-gray-200 rounded-2xl shadow-sm hover:bg-gray-50 text-gray-600 transition-all tooltip" title="Refresh Data">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    {{-- Cabang Tabs --}}
    <div class="daily-branches mb-8 flex items-center p-1.5 bg-gray-100/50 w-fit rounded-2xl border border-gray-200/50">
        <a href="{{ route('admin.cek-kendaraan.daily', ['date' => $date]) }}"
           aria-current="{{ !$cabang ? 'page' : 'false' }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ !$cabang ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Semua Cabang
        </a>
        <a href="{{ route('admin.cek-kendaraan.daily', ['date' => $date, 'cabang' => 'JAKARTA']) }}"
           aria-current="{{ $cabang === 'JAKARTA' ? 'page' : 'false' }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $cabang === 'JAKARTA' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            <i class="fas fa-city mr-2 {{ $cabang === 'JAKARTA' ? 'text-blue-500' : 'text-gray-400' }}"></i> Jakarta
        </a>
        <a href="{{ route('admin.cek-kendaraan.daily', ['date' => $date, 'cabang' => 'BATAM']) }}"
           aria-current="{{ $cabang === 'BATAM' ? 'page' : 'false' }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $cabang === 'BATAM' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            <i class="fas fa-ship mr-2 {{ $cabang === 'BATAM' ? 'text-blue-500' : 'text-gray-400' }}"></i> Batam
        </a>
    </div>

    @php
        $completedCount = $drivers->filter(function($driver) use ($checksForDate) {
            return $checksForDate->has($driver->id);
        })->count();
        $totalDrivers = $drivers->count();
        $pendingCount = $totalDrivers - $completedCount;
        $completionRate = $totalDrivers > 0 ? round(($completedCount / $totalDrivers) * 100) : 0;
    @endphp

    {{-- Stats Cards --}}
    <div class="daily-stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Supir --}}
        <div class="daily-stat bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
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
        <div class="daily-stat bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
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
        <div class="daily-stat bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
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

        {{-- Progress --}}
        <div class="daily-stat daily-progress bg-white p-6 rounded-3xl shadow-lg shadow-blue-200 group overflow-hidden relative">
            <div class="relative flex items-center justify-between h-full">
                <div>
                    <p class="text-xs font-bold text-blue-100 uppercase tracking-widest mb-1">Progress</p>
                    <h3 class="text-4xl font-black text-white leading-none">{{ $completionRate }}%</h3>
                </div>
                <progress class="daily-progress-bar" value="{{ $completionRate }}" max="100" aria-label="Progress pengecekan harian">{{ $completionRate }}%</progress>
            </div>
        </div>
    </div>

    {{-- Data Table Section --}}
    <div class="daily-monitoring bg-white rounded-[2rem] border border-gray-100 shadow-xl overflow-hidden">
        <div class="daily-toolbar p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </div>
                <input type="text" id="driverSearch" aria-label="Cari nama supir atau NIK" placeholder="Cari nama supir atau NIK..."
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
        <div class="daily-desktop hidden lg:block overflow-x-auto">
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
                                    <div class="w-12 h-12 rounded-2xl daily-avatar {{ $check ? 'from-blue-500 to-indigo-600 text-white' : 'from-gray-100 to-gray-200 text-gray-500' }} flex items-center justify-center font-black text-lg shadow-sm group-hover:scale-110 transition-transform">
                                        {{ substr($driver->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-gray-800 leading-tight">{{ $driver->nama_lengkap }}</p>
                                            @if($driver->cabang)
                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-tighter {{ $driver->cabang === 'JAKARTA' ? 'bg-blue-100 text-blue-700' : ($driver->cabang === 'BATAM' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                                    {{ $driver->cabang }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">NIK: {{ $driver->nik ?? 'N/A' }}</p>
                                        <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mt-0.5">{{ $driver->pekerjaan }}</p>
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
                                    <p class="text-gray-300 text-sm mt-1">Hanya menampilkan supir dengan pekerjaan 'SUPIR TRUCK' atau 'SUPIR TRAILER'.</p>
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
                            <div class="w-10 h-10 rounded-xl daily-avatar {{ $check ? 'from-blue-500 to-indigo-600' : 'from-gray-100 to-gray-200 text-gray-400' }} text-white flex items-center justify-center font-black text-sm shadow-sm">
                                @if($check)
                                    {{ substr($driver->nama_lengkap, 0, 1) }}
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                            </div>
                            <div class="ml-3">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-bold text-gray-800 leading-tight">{{ $driver->nama_lengkap }}</h4>
                                    @if($driver->cabang)
                                        <span class="px-1 py-0.5 rounded text-[7px] font-black uppercase tracking-tighter {{ $driver->cabang === 'JAKARTA' ? 'bg-blue-100 text-blue-700' : ($driver->cabang === 'BATAM' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                            {{ substr($driver->cabang, 0, 3) }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">NIK: {{ $driver->nik ?? 'N/A' }}</p>
                                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mt-0.5">{{ $driver->pekerjaan }}</p>
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
    body:has(.daily-dashboard) { font-family: Arial, sans-serif; font-size: 12px; }
    body:has(.daily-dashboard) #page-content { background: #F5F7FA; }
    body:has(.daily-dashboard) > header h1 { font-family: Arial, sans-serif; font-size: 20px; }
    body:has(.daily-dashboard) #sidebar :is(a, input, .menu-text),
    body:has(.daily-dashboard) > header :is(button, a, span:not([class*="fa-"])) { font-family: Arial, sans-serif; font-size: 12px; }
    .daily-dashboard {
        --daily-navy: #1E3A5F; --daily-bg: #F5F7FA; --daily-line: #e1e6ed;
        padding: 0; max-width: none; font: 12px/1.5 Arial, sans-serif; color: #344256; min-width: 0;
    }
    .daily-dashboard :is(p, span, a, button, input, select, label, table, th, td, h4) { font-family: Arial, sans-serif; font-size: 12px; }
    .daily-dashboard h1, .daily-dashboard h1 span { font: 600 20px/1.4 Arial, sans-serif; color: var(--daily-navy); letter-spacing: 0; }
    .daily-dashboard h3 { font: 700 20px/1.3 Arial, sans-serif; }
    .daily-dashboard .daily-header { align-items: center; margin-bottom: 18px; gap: 16px; }
    .daily-dashboard .daily-header p { color: #748091; margin-top: 4px; }
    .daily-dashboard .daily-header p i { color: #8a99ad; }
    .daily-dashboard .daily-actions { gap: 8px; flex-shrink: 0; }
    .daily-dashboard .daily-actions :is(input, a, button) { min-height: 34px; border-radius: 5px; font-weight: 500; box-shadow: none; transition: none; }
    .daily-dashboard .daily-actions > a { background: var(--daily-navy); border: 1px solid var(--daily-navy); padding: 7px 12px; }
    .daily-dashboard .daily-actions > a:hover { background: #2b4c75; }
    .daily-dashboard .daily-actions > button { width: 34px; padding: 7px; color: var(--daily-navy); border-color: #d5dde7; }
    .daily-dashboard .daily-date-control { width: 165px; }
    .daily-dashboard .daily-date-control input { width: 100%; background: #fff; border: 1px solid #d5dde7; padding: 7px 8px 7px 32px; color: transparent; }
    .daily-dashboard .daily-date-control input::-webkit-datetime-edit { opacity: 0; }
    .daily-dashboard .daily-date-display { position: absolute; left: 32px; top: 50%; transform: translateY(-50%); color: #344256; pointer-events: none; }
    .daily-dashboard .daily-date-control .absolute { padding-left: 10px; }
    .daily-dashboard :is(a, button, input):focus-visible { outline: 2px solid #6b86a8; outline-offset: 2px; box-shadow: none; }
    .daily-dashboard .daily-branches { gap: 3px; padding: 4px; background: #eef1f5; border: 1px solid var(--daily-line); border-radius: 6px; margin-bottom: 18px; max-width: 100%; }
    .daily-dashboard .daily-branches a { padding: 7px 14px; color: #6b7788; border-radius: 4px; font-weight: 500; transition: none; white-space: nowrap; }
    .daily-dashboard .daily-branches a[aria-current="page"] { color: var(--daily-navy); background: #fff; box-shadow: 0 1px 3px rgb(30 58 95 / 6%); font-weight: 600; }
    .daily-dashboard .daily-branches a:hover { color: var(--daily-navy); }
    .daily-dashboard .daily-branches i { color: #8798ae; margin-right: 6px; }
    .daily-dashboard .daily-stats { gap: 16px; margin-bottom: 20px; }
    .daily-dashboard :is(.daily-stat, .daily-monitoring) { background: #fff; border: 1px solid var(--daily-line); border-radius: 8px; box-shadow: 0 2px 5px rgb(30 58 95 / 4%); }
    .daily-dashboard .daily-stat { padding: 16px; display: flex; flex-direction: column; min-width: 0; transition: none; }
    .daily-dashboard .daily-stat > .relative { gap: 12px; }
    .daily-dashboard .daily-stat .uppercase { color: #6b7788; letter-spacing: .02em; font-weight: 500; }
    .daily-dashboard .daily-stat h3 { color: var(--daily-navy); font-variant-numeric: tabular-nums; }
    .daily-dashboard .daily-stat h3.text-emerald-600 { color: #34704f; }
    .daily-dashboard .daily-stat h3.text-rose-500 { color: #ad5252; }
    .daily-dashboard .daily-stat .w-12 { width: 30px; height: 30px; border-radius: 6px; background: #f2f5f8; }
    .daily-dashboard .daily-stat .w-12 i { font-size: 14px; }
    .daily-dashboard .daily-stat > .mt-4 { margin-top: 10px; color: #748091; font-weight: 400; }
    .daily-dashboard .daily-stat > .mt-4 span { background: #f1f4f8; border-radius: 4px; }
    .daily-dashboard .daily-progress > .relative { flex-direction: column; align-items: stretch; justify-content: space-between; height: 100%; gap: 12px; }
    .daily-dashboard .daily-progress-bar { display: block; width: 100%; height: 6px; border: 0; border-radius: 4px; background: #e8edf3; color: var(--daily-navy); appearance: none; overflow: hidden; }
    .daily-dashboard .daily-progress-bar::-webkit-progress-bar { background: #e8edf3; border-radius: 4px; }
    .daily-dashboard .daily-progress-bar::-webkit-progress-value { background: var(--daily-navy); border-radius: 4px; }
    .daily-dashboard .daily-progress-bar::-moz-progress-bar { background: var(--daily-navy); border-radius: 4px; }
    .daily-dashboard .daily-toolbar { padding: 14px 16px; border-color: var(--daily-line); align-items: center; gap: 12px; }
    .daily-dashboard #driverSearch { background: #f8fafc; border: 1px solid #dce3eb; border-radius: 5px; padding: 7px 10px 7px 32px; min-height: 34px; font-weight: 400; transition: none; }
    .daily-dashboard .daily-toolbar > .flex { gap: 4px; flex-wrap: wrap; }
    .daily-dashboard .daily-toolbar > .flex > span { font-weight: 500; color: #748091; text-transform: none; margin-right: 6px; }
    .daily-dashboard .status-btn { padding: 6px 11px; min-height: 32px; border-radius: 5px; border: 1px solid transparent; font-weight: 500; transition: none; }
    .daily-dashboard .status-btn.active { background: #edf2f8; border-color: #dae3ef; color: var(--daily-navy); }
    .daily-dashboard .status-btn:hover { background: #f3f6fa; }
    .daily-dashboard table { min-width: 800px; border-collapse: collapse; }
    .daily-dashboard th { background: var(--daily-bg); color: #647184; letter-spacing: .02em; font-weight: 600; padding: 11px 16px; border-bottom: 1px solid var(--daily-line); }
    .daily-dashboard td { padding: 12px 16px; border-color: var(--daily-line); }
    .daily-dashboard th:first-child { width: 36%; }
    .daily-dashboard .driver-row { transition: none; transform: none; }
    .daily-dashboard tr.driver-row:hover { background: #f8fafc; }
    .daily-dashboard .daily-avatar { width: 32px; height: 32px; border-radius: 6px; background: #f0f2f5; background-image: none; color: #8490a1; font: 600 12px Arial, sans-serif; box-shadow: none; transform: none; flex-shrink: 0; }
    .daily-dashboard .driver-row[data-status="selesai"] .daily-avatar { background: #edf2f8; color: var(--daily-navy); }
    .daily-dashboard td .ml-4 { margin-left: 10px; }
    .daily-dashboard .driver-row .tracking-widest { letter-spacing: 0; font-weight: 400; color: #7b8797; text-transform: none; }
    .daily-dashboard .driver-row .tracking-wider { letter-spacing: 0; font-weight: 500; }
    .daily-dashboard .driver-row .bg-blue-100 { background: #edf2f8; color: #456386; font-weight: 500; letter-spacing: 0; }
    .daily-dashboard .driver-row .bg-amber-100 { background: #faf3e7; color: #8a6b35; font-weight: 500; letter-spacing: 0; }
    .daily-dashboard .driver-row :is(.bg-emerald-100, .bg-rose-100) { border-radius: 4px; font-weight: 500; text-transform: none; letter-spacing: 0; padding: 3px 7px; }
    .daily-dashboard .driver-row .bg-emerald-100 { background: #edf6f1; color: #34704f; }
    .daily-dashboard .driver-row .bg-rose-100 { background: #fcf2e7; color: #946b2c; }
    .daily-dashboard .driver-row .text-gray-300 { color: #929cab; font-style: normal; }
    .daily-dashboard .driver-row .font-black { font-weight: 600; }
    .daily-dashboard td:last-child :is(a, button) { width: 30px; height: 30px; border: 1px solid #d4ddea; border-radius: 5px; box-shadow: none; background: #fff; color: var(--daily-navy); transition: none; }
    .daily-dashboard td:last-child a:hover { background: #edf2f8; }
    .daily-dashboard td:last-child button:disabled { background: #f6f8fa; border-color: #e9edf2; color: #b0bac8; }
    .daily-dashboard td[colspan] { padding: 30px 16px; }
    .daily-dashboard td[colspan] .w-20 { width: 36px; height: 36px; border: 0; box-shadow: none; margin-bottom: 12px; }
    .daily-dashboard td[colspan] i { font-size: 18px; }
    .daily-dashboard td[colspan] h3 { font-size: 16px; }
    .daily-dashboard .daily-monitoring > .border-t { padding: 12px 16px; border-color: var(--daily-line); background: #fafbfd; color: #7b8797; font-weight: 400; letter-spacing: 0; text-transform: none; }
    .daily-dashboard .overflow-x-auto::-webkit-scrollbar { height: 5px; }
    .daily-dashboard .overflow-x-auto::-webkit-scrollbar-thumb { background: #d5dde7; border-radius: 4px; }
    .daily-dashboard #mobileCards { background: var(--daily-bg); padding: 12px; gap: 12px; }
    .daily-dashboard #mobileCards .driver-row { border: 1px solid var(--daily-line); border-radius: 8px; padding: 14px; box-shadow: 0 1px 3px rgb(30 58 95 / 3%); }
    .daily-dashboard #mobileCards .driver-row > .flex { gap: 8px; margin-bottom: 12px; align-items: flex-start; }
    .daily-dashboard #mobileCards h4 { overflow-wrap: anywhere; }
    .daily-dashboard #mobileCards .ml-3 { min-width: 0; margin-left: 8px; }
    .daily-dashboard #mobileCards .ml-3 > .flex { flex-wrap: wrap; gap: 4px; }
    .daily-dashboard #mobileCards .grid { gap: 8px; margin-bottom: 12px; }
    .daily-dashboard #mobileCards .grid > div { border-color: #edf0f4; border-radius: 5px; background: #f8fafc; padding: 9px; }
    .daily-dashboard #mobileCards .grid .uppercase { color: #7b8797; font-weight: 400; letter-spacing: 0; }
    .daily-dashboard #mobileCards .driver-row > a { background: #fff; color: var(--daily-navy); border: 1px solid #cdd7e4; border-radius: 5px; padding: 7px 10px; box-shadow: none; transform: none; transition: none; }
    .daily-dashboard #mobileCards .driver-row > a:hover { background: #edf2f8; }
    .daily-dashboard #mobileCards .driver-row > button { border-radius: 5px; padding: 7px 10px; font-weight: 400; background: #f1f3f6; color: #8c98a8; }
    @media (max-width: 1200px) {
        .daily-dashboard .daily-header { flex-direction: column; align-items: flex-start; }
        .daily-dashboard .daily-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .daily-dashboard .daily-toolbar { align-items: stretch; }
        .daily-dashboard .daily-toolbar > .relative { max-width: none; }
    }
    @media (max-width: 540px) {
        .daily-dashboard .daily-actions { flex-wrap: wrap; gap: 6px; }
        .daily-dashboard .daily-branches { width: 100%; }
        .daily-dashboard .daily-branches a { flex: 1; padding: 7px 8px; text-align: center; }
        .daily-dashboard .daily-stats { gap: 10px; }
        .daily-dashboard .daily-stat { padding: 12px; }
        .daily-dashboard .daily-stat > .relative { gap: 8px; }
        .daily-dashboard #mobileCards .driver-row > .flex { flex-wrap: wrap; }
    }
    @media (max-width: 350px) {
        .daily-dashboard .daily-stats { grid-template-columns: 1fr; }
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
