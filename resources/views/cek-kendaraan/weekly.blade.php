@extends('layouts.app')

@section('title', 'Dashboard Cek Kendaraan Mingguan')
@section('page_title', 'Dashboard Cek Kendaraan Mingguan')

@section('content')
<div class="weekly-dashboard p-4 sm:p-6 max-w-[1600px] mx-auto">
    {{-- Header Section --}}
    <div class="weekly-header mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Status Kendaraan <span class="text-indigo-600">Mingguan</span></h1>
            <p class="text-gray-500 mt-2 flex items-center">
                <i class="fas fa-info-circle mr-2 text-indigo-400"></i>
                Memantau kelengkapan pengecekan mingguan oleh seluruh supir armada
            </p>
            <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-wider">
                {{ $weekStart->format('d/M/Y') }} — {{ $weekEnd->format('d/M/Y') }}
            </p>
        </div>
        <div class="weekly-actions flex items-center gap-3">
            <div class="weekly-date-navigation flex items-center bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <a href="{{ route('admin.cek-kendaraan.weekly', ['week_start' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}"
                   aria-label="Minggu sebelumnya" class="p-2.5 hover:bg-gray-50 text-gray-600 border-r border-gray-100 transition-all">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <form action="{{ route('admin.cek-kendaraan.weekly') }}" method="GET" class="weekly-date-control relative group">
                    <span class="weekly-date-display" aria-hidden="true">{{ $weekStart->format('d/M/Y') }}</span>
                    <input aria-label="Tanggal awal minggu" type="date" name="week_start" value="{{ $weekStart->format('Y-m-d') }}"
                        class="pl-4 pr-4 py-2.5 bg-transparent border-none focus:ring-0 transition-all text-sm font-bold text-gray-700 outline-none"
                        onchange="this.form.submit()">
                    @if(isset($cabang))
                        <input type="hidden" name="cabang" value="{{ $cabang }}">
                    @endif
                </form>
                <a href="{{ route('admin.cek-kendaraan.weekly', ['week_start' => $weekStart->copy()->addWeek()->format('Y-m-d'), 'cabang' => $cabang ?? null]) }}"
                   aria-label="Minggu berikutnya" class="p-2.5 hover:bg-gray-50 text-gray-600 border-l border-gray-100 transition-all">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            <a href="{{ route('admin.cek-kendaraan.weekly.export', ['week_start' => $weekStart->format('Y-m-d'), 'cabang' => $cabang ?? null]) }}" class="px-4 py-2.5 bg-emerald-600 text-white rounded-2xl shadow-sm hover:bg-emerald-700 transition-all text-sm font-bold flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
            <button aria-label="Refresh data" onclick="window.location.reload()" class="p-2.5 bg-white border border-gray-200 rounded-2xl shadow-sm hover:bg-gray-50 text-gray-600 transition-all tooltip" title="Refresh Data">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    {{-- Cabang Tabs --}}
    <div class="weekly-branches mb-8 flex items-center p-1.5 bg-gray-100/50 w-fit rounded-2xl border border-gray-200/50">
        <a href="{{ route('admin.cek-kendaraan.weekly', ['week_start' => $weekStart->format('Y-m-d')]) }}"
           aria-current="{{ !$cabang ? 'page' : 'false' }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ !$cabang ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Semua Cabang
        </a>
        <a href="{{ route('admin.cek-kendaraan.weekly', ['week_start' => $weekStart->format('Y-m-d'), 'cabang' => 'JAKARTA']) }}"
           aria-current="{{ $cabang === 'JAKARTA' ? 'page' : 'false' }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $cabang === 'JAKARTA' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            <i class="fas fa-city mr-2 {{ $cabang === 'JAKARTA' ? 'text-indigo-500' : 'text-gray-400' }}"></i> Jakarta
        </a>
        <a href="{{ route('admin.cek-kendaraan.weekly', ['week_start' => $weekStart->format('Y-m-d'), 'cabang' => 'BATAM']) }}"
           aria-current="{{ $cabang === 'BATAM' ? 'page' : 'false' }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $cabang === 'BATAM' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            <i class="fas fa-ship mr-2 {{ $cabang === 'BATAM' ? 'text-indigo-500' : 'text-gray-400' }}"></i> Batam
        </a>
    </div>

    @php
        $totalDrivers = $drivers->count();
        $fullCompliance = 0;
        $partialCompliance = 0;
        $noCompliance = 0;

        foreach($drivers as $driver) {
            $driverChecks = $checksForWeek->get($driver->id);
            $checkCount = $driverChecks ? $driverChecks->count() : 0;

            if ($checkCount >= 7) $fullCompliance++;
            elseif ($checkCount > 0) $partialCompliance++;
            else $noCompliance++;
        }

        $overallRate = $totalDrivers > 0 ? round((($fullCompliance * 7 + $partialCompliance * 3) / ($totalDrivers * 7)) * 100) : 0;
    @endphp

    {{-- Stats Cards --}}
    <div class="weekly-stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Supir --}}
        <div class="weekly-stat bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
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

        {{-- Full Compliance --}}
        <div class="weekly-stat bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-1">Full (7/7)</p>
                    <h3 class="text-3xl font-black text-emerald-600">{{ $fullCompliance }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-emerald-600">
                Cek setiap hari dalam seminggu
            </div>
        </div>

        {{-- Partial --}}
        <div class="weekly-stat bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-1">Parsial (1-6)</p>
                    <h3 class="text-3xl font-black text-amber-500">{{ $partialCompliance }}</h3>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-500">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-amber-500">
                Melewatkan beberapa hari
            </div>
        </div>

        {{-- Belum Cek --}}
        <div class="weekly-stat bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-rose-400 uppercase tracking-widest mb-1">Belum (0/7)</p>
                    <h3 class="text-3xl font-black text-rose-500">{{ $noCompliance }}</h3>
                </div>
                <div class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center text-rose-500">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-medium text-rose-500">
                Sama sekali belum cek minggu ini
            </div>
        </div>
    </div>

    {{-- Data Table Section --}}
    <div class="weekly-monitoring bg-white rounded-[2rem] border border-gray-100 shadow-xl overflow-hidden">
        <div class="weekly-toolbar p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </div>
                <input type="text" id="driverSearch" aria-label="Cari nama supir atau NIK" placeholder="Cari nama supir atau NIK..."
                    class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-100 transition-all text-sm font-medium outline-none">
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-400 mr-2 uppercase">Filter:</span>
                <button onclick="filterStatus('all')" class="status-btn active px-4 py-2 rounded-xl text-xs font-bold transition-all bg-indigo-50 text-indigo-600">Semua</button>
                <button onclick="filterStatus('full')" class="status-btn px-4 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:bg-gray-50">Full</button>
                <button onclick="filterStatus('partial')" class="status-btn px-4 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:bg-gray-50">Parsial</button>
                <button onclick="filterStatus('none')" class="status-btn px-4 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:bg-gray-50">Belum</button>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden lg:block overflow-x-auto">
            <table id="driverTable" class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-400 text-[10px] uppercase font-black tracking-[0.2em]">
                        <th class="px-8 py-5 min-w-[250px]">Informasi Supir</th>
                        @foreach($weekDays as $day)
                            <th class="px-2 py-5 text-center">
                                <div>{{ $day->isoFormat('ddd') }}</div>
                                <div class="text-[8px] opacity-60">{{ $day->format('d/M/Y') }}</div>
                            </th>
                        @endforeach
                        <th class="px-8 py-5 text-center">Total</th>
                        <th class="px-8 py-5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($drivers as $driver)
                        @php
                            $driverChecks = $checksForWeek->get($driver->id);
                            $checkCount = $driverChecks ? $driverChecks->count() : 0;
                            $status = 'none';
                            if ($checkCount >= 7) $status = 'full';
                            elseif ($checkCount > 0) $status = 'partial';
                        @endphp
                        <tr class="driver-row hover:bg-indigo-50/30 transition-colors group" data-status="{{ $status }}" data-search="{{ strtolower($driver->nama_lengkap . ' ' . $driver->nik) }}">
                            <td class="px-8 py-5">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl weekly-avatar {{ $checkCount > 0 ? 'from-indigo-500 to-purple-600 text-white' : 'from-gray-100 to-gray-200 text-gray-500' }} flex items-center justify-center font-black text-sm shadow-sm group-hover:scale-110 transition-transform">
                                        {{ substr($driver->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-gray-800 leading-tight">{{ $driver->nama_lengkap }}</p>
                                            @if($driver->cabang)
                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-tighter {{ $driver->cabang === 'JAKARTA' ? 'bg-indigo-100 text-indigo-700' : ($driver->cabang === 'BATAM' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                                    {{ $driver->cabang }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">NIK: {{ $driver->nik ?? 'N/A' }}</p>
                                        <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mt-0.5">{{ $driver->pekerjaan }}</p>
                                    </div>
                                </div>
                            </td>

                            @foreach($weekDays as $day)
                                @php
                                    $dateKey = $day->format('Y-m-d');
                                    $check = $driverChecks ? $driverChecks->get($dateKey) : null;
                                    $dayCheck = $check ? $check->first() : null;
                                @endphp
                                <td class="px-2 py-5 text-center">
                                    @if($dayCheck)
                                        <a href="{{ route('admin.cek-kendaraan.show', $dayCheck->id) }}" target="_blank"
                                           class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all tooltip"
                                           title="Cek pukul {{ Carbon\Carbon::parse($dayCheck->jam)->format('H:i') }}">
                                            <i class="fas fa-check text-[10px]"></i>
                                        </a>
                                    @else
                                        <div class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-300">
                                            <i class="fas fa-times text-[10px]"></i>
                                        </div>
                                    @endif
                                </td>
                            @endforeach

                            <td class="px-8 py-5 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-sm font-black {{ $checkCount >= 7 ? 'text-emerald-600' : ($checkCount > 0 ? 'text-amber-500' : 'text-rose-500') }}">
                                        {{ $checkCount }}/7
                                    </span>
                                    <div class="weekly-row-progress w-16 h-1 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full {{ $checkCount >= 7 ? 'bg-emerald-500' : ($checkCount > 0 ? 'bg-amber-400' : 'bg-rose-400') }}"
                                             style="width: {{ ($checkCount / 7) * 100 }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    @if($checkCount > 0)
                                        <div class="weekly-row-actions relative group/actions">
                                            <button aria-label="Detail pengecekan mingguan" class="p-2 bg-gray-50 text-gray-400 hover:text-indigo-600 rounded-xl transition-all">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 hidden group-hover/actions:block z-50">
                                                <p class="px-4 py-1 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 mb-1">Detail Cek</p>
                                                @foreach($weekDays as $day)
                                                    @php
                                                        $dateKey = $day->format('Y-m-d');
                                                        $dayChecks = $driverChecks ? $driverChecks->get($dateKey) : null;
                                                        $dc = $dayChecks ? $dayChecks->first() : null;
                                                    @endphp
                                                    @if($dc)
                                                        <a href="{{ route('admin.cek-kendaraan.show', $dc->id) }}" target="_blank" class="flex items-center px-4 py-2 text-xs font-bold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span>
                                                            {{ $day->isoFormat('dddd') }}: {{ Carbon\Carbon::parse($dc->jam)->format('H:i') }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <button disabled class="p-2 bg-gray-50 text-gray-200 rounded-xl cursor-not-allowed">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center border-4 border-white shadow-inner mb-4">
                                        <i class="fas fa-users-slash text-3xl text-gray-200"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-400">Tidak ada data driver ditemukan</h3>
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
                    $driverChecks = $checksForWeek->get($driver->id);
                    $checkCount = $driverChecks ? $driverChecks->count() : 0;
                    $status = 'none';
                    if ($checkCount >= 7) $status = 'full';
                    elseif ($checkCount > 0) $status = 'partial';
                @endphp
                <div class="driver-row bg-white p-5 rounded-2xl border border-gray-100 shadow-sm transition-all active:scale-[0.98]" data-status="{{ $status }}" data-search="{{ strtolower($driver->nama_lengkap . ' ' . $driver->nik) }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-xl weekly-avatar {{ $checkCount > 0 ? 'from-indigo-500 to-purple-600' : 'from-gray-100 to-gray-200 text-gray-400' }} text-white flex items-center justify-center font-black text-sm shadow-sm">
                                {{ substr($driver->nama_lengkap, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-bold text-gray-800 leading-tight">{{ $driver->nama_lengkap }}</h4>
                                    @if($driver->cabang)
                                        <span class="px-1 py-0.5 rounded text-[7px] font-black uppercase tracking-tighter {{ $driver->cabang === 'JAKARTA' ? 'bg-indigo-100 text-indigo-700' : ($driver->cabang === 'BATAM' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                            {{ substr($driver->cabang, 0, 3) }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">NIK: {{ $driver->nik ?? 'N/A' }}</p>
                                <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mt-0.5">{{ $driver->pekerjaan }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs font-black {{ $checkCount >= 7 ? 'text-emerald-600' : ($checkCount > 0 ? 'text-amber-500' : 'text-rose-500') }}">
                                {{ $checkCount }}/7 Hari
                            </span>
                            <div class="weekly-row-progress w-16 h-1 bg-gray-100 rounded-full mt-1 overflow-hidden" aria-hidden="true">
                                <div class="h-full {{ $checkCount >= 7 ? 'bg-emerald-500' : ($checkCount > 0 ? 'bg-amber-400' : 'bg-rose-400') }}" style="width: {{ ($checkCount / 7) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl border border-gray-100">
                        @foreach($weekDays as $day)
                            @php
                                $dateKey = $day->format('Y-m-d');
                                $hasCheck = $driverChecks && $driverChecks->has($dateKey);
                            @endphp
                            <div class="flex flex-col items-center">
                                <span class="text-[8px] font-black text-gray-400 uppercase mb-1">{{ substr($day->isoFormat('ddd'), 0, 1) }}</span>
                                <span class="weekly-mobile-check {{ $hasCheck ? 'weekly-checked' : 'weekly-unchecked' }}" role="img" aria-label="{{ $day->format('d/M/Y') }}: {{ $hasCheck ? 'Selesai' : 'Belum cek' }}" title="{{ $day->format('d/M/Y') }}">
                                    <i class="fas {{ $hasCheck ? 'fa-check' : 'fa-times' }}" aria-hidden="true"></i>
                                </span>
                            </div>
                        @endforeach
                    </div>
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
            <span class="flex items-center"><i class="fas fa-sync-alt mr-2 text-xs text-indigo-400"></i> Terakhir Diperbarui: {{ now()->format('H:i') }} WIB</span>
        </div>
        @endif
    </div>
</div>

<style>
    body:has(.weekly-dashboard) { font-family: Arial, sans-serif; font-size: 12px; }
    body:has(.weekly-dashboard) #page-content { background: #F5F7FA; }
    body:has(.weekly-dashboard) > header h1 { font-family: Arial, sans-serif; font-size: 20px; }
    body:has(.weekly-dashboard) #sidebar :is(a, input, .menu-text),
    body:has(.weekly-dashboard) > header :is(button, a, span:not([class*="fa-"])) { font-family: Arial, sans-serif; font-size: 12px; }
    .weekly-dashboard {
        --weekly-navy: #1E3A5F; --weekly-bg: #F5F7FA; --weekly-line: #e1e6ed;
        padding: 0; max-width: none; font: 12px/1.5 Arial, sans-serif; color: #344256; min-width: 0;
    }
    .weekly-dashboard :is(p, span, a, button, input, select, label, table, th, td, h4) { font-family: Arial, sans-serif; font-size: 12px; }
    .weekly-dashboard h1, .weekly-dashboard h1 span { font: 600 20px/1.4 Arial, sans-serif; color: var(--weekly-navy); letter-spacing: 0; }
    .weekly-dashboard h3 { font: 700 20px/1.3 Arial, sans-serif; }
    .weekly-dashboard .weekly-header { align-items: center; margin-bottom: 18px; gap: 16px; }
    .weekly-dashboard .weekly-header p { color: #748091; margin-top: 4px; }
    .weekly-dashboard .weekly-header p i { color: #8a99ad; }
    .weekly-dashboard .weekly-actions { gap: 8px; flex-shrink: 0; }
    .weekly-dashboard .weekly-actions :is(input, a, button) { min-height: 34px; border-radius: 5px; font-weight: 500; box-shadow: none; transition: none; }
    .weekly-dashboard .weekly-actions > a { background: var(--weekly-navy); border: 1px solid var(--weekly-navy); padding: 7px 12px; }
    .weekly-dashboard .weekly-actions > a:hover { background: #2b4c75; }
    .weekly-dashboard .weekly-actions > button { width: 34px; padding: 7px; color: var(--weekly-navy); border-color: #d5dde7; }
    .weekly-dashboard .weekly-date-control { width: 165px; }
    .weekly-dashboard .weekly-date-control input { width: 100%; background: #fff; border: 1px solid #d5dde7; padding: 7px 8px 7px 32px; color: transparent; }
    .weekly-dashboard .weekly-date-control input::-webkit-datetime-edit { opacity: 0; }
    .weekly-dashboard .weekly-date-display { position: absolute; left: 32px; top: 50%; transform: translateY(-50%); color: #344256; pointer-events: none; }
    .weekly-dashboard :is(a, button, input):focus-visible { outline: 2px solid #6b86a8; outline-offset: 2px; box-shadow: none; }
    .weekly-dashboard .weekly-branches { gap: 3px; padding: 4px; background: #eef1f5; border: 1px solid var(--weekly-line); border-radius: 6px; margin-bottom: 18px; max-width: 100%; }
    .weekly-dashboard .weekly-branches a { padding: 7px 14px; color: #6b7788; border-radius: 4px; font-weight: 500; transition: none; white-space: nowrap; }
    .weekly-dashboard .weekly-branches a[aria-current="page"] { color: var(--weekly-navy); background: #fff; box-shadow: 0 1px 3px rgb(30 58 95 / 6%); font-weight: 600; }
    .weekly-dashboard .weekly-branches a:hover { color: var(--weekly-navy); }
    .weekly-dashboard .weekly-branches i { color: #8798ae; margin-right: 6px; }
    .weekly-dashboard .weekly-stats { gap: 16px; margin-bottom: 20px; }
    .weekly-dashboard :is(.weekly-stat, .weekly-monitoring) { background: #fff; border: 1px solid var(--weekly-line); border-radius: 8px; box-shadow: 0 2px 5px rgb(30 58 95 / 4%); }
    .weekly-dashboard .weekly-stat { padding: 16px; display: flex; flex-direction: column; min-width: 0; transition: none; }
    .weekly-dashboard .weekly-stat > .relative { gap: 12px; }
    .weekly-dashboard .weekly-stat .uppercase { color: #6b7788; letter-spacing: .02em; font-weight: 500; }
    .weekly-dashboard .weekly-stat h3 { color: var(--weekly-navy); font-variant-numeric: tabular-nums; }
    .weekly-dashboard .weekly-stat h3.text-emerald-600 { color: #34704f; }
    .weekly-dashboard .weekly-stat h3.text-rose-500 { color: #ad5252; }
    .weekly-dashboard .weekly-stat .w-12 { width: 30px; height: 30px; border-radius: 6px; background: #f2f5f8; }
    .weekly-dashboard .weekly-stat .w-12 i { font-size: 14px; }
    .weekly-dashboard .weekly-stat > .mt-4 { margin-top: 10px; color: #748091; font-weight: 400; }
    .weekly-dashboard .weekly-stat > .mt-4 span { background: #f1f4f8; border-radius: 4px; }
    .weekly-dashboard .weekly-toolbar { padding: 14px 16px; border-color: var(--weekly-line); align-items: center; gap: 12px; }
    .weekly-dashboard #driverSearch { background: #f8fafc; border: 1px solid #dce3eb; border-radius: 5px; padding: 7px 10px 7px 32px; min-height: 34px; font-weight: 400; transition: none; }
    .weekly-dashboard .weekly-toolbar > .flex { gap: 4px; flex-wrap: wrap; }
    .weekly-dashboard .weekly-toolbar > .flex > span { font-weight: 500; color: #748091; text-transform: none; margin-right: 6px; }
    .weekly-dashboard .status-btn { padding: 6px 11px; min-height: 32px; border-radius: 5px; border: 1px solid transparent; font-weight: 500; transition: none; }
    .weekly-dashboard .status-btn.active { background: #edf2f8; border-color: #dae3ef; color: var(--weekly-navy); }
    .weekly-dashboard .status-btn:hover { background: #f3f6fa; }
    .weekly-dashboard table { min-width: 800px; border-collapse: collapse; }
    .weekly-dashboard th { background: var(--weekly-bg); color: #647184; letter-spacing: .02em; font-weight: 600; padding: 11px 16px; border-bottom: 1px solid var(--weekly-line); }
    .weekly-dashboard td { padding: 12px 16px; border-color: var(--weekly-line); }
    .weekly-dashboard th:first-child { width: 36%; }
    .weekly-dashboard .driver-row { transition: none; transform: none; }
    .weekly-dashboard tr.driver-row:hover { background: #f8fafc; }
    .weekly-dashboard .weekly-avatar { width: 32px; height: 32px; border-radius: 6px; background: #f0f2f5; background-image: none; color: #8490a1; font: 600 12px Arial, sans-serif; box-shadow: none; transform: none; flex-shrink: 0; }
    .weekly-dashboard td .ml-4 { margin-left: 10px; }
    .weekly-dashboard .driver-row .tracking-widest { letter-spacing: 0; font-weight: 400; color: #7b8797; text-transform: none; }
    .weekly-dashboard .driver-row .tracking-wider { letter-spacing: 0; font-weight: 500; }
    .weekly-dashboard .driver-row .bg-amber-100 { background: #faf3e7; color: #8a6b35; font-weight: 500; letter-spacing: 0; }
    .weekly-dashboard .driver-row :is(.bg-emerald-100, .bg-rose-100) { border-radius: 4px; font-weight: 500; text-transform: none; letter-spacing: 0; padding: 3px 7px; }
    .weekly-dashboard .driver-row .bg-emerald-100 { background: #edf6f1; color: #34704f; }
    .weekly-dashboard .driver-row .bg-rose-100 { background: #fcf2e7; color: #946b2c; }
    .weekly-dashboard .driver-row .text-gray-300 { color: #929cab; font-style: normal; }
    .weekly-dashboard .driver-row .font-black { font-weight: 600; }
    .weekly-dashboard td:last-child :is(a, button) { width: 30px; height: 30px; border: 1px solid #d4ddea; border-radius: 5px; box-shadow: none; background: #fff; color: var(--weekly-navy); transition: none; }
    .weekly-dashboard td:last-child a:hover { background: #edf2f8; }
    .weekly-dashboard td:last-child button:disabled { background: #f6f8fa; border-color: #e9edf2; color: #b0bac8; }
    .weekly-dashboard td[colspan] { padding: 30px 16px; }
    .weekly-dashboard td[colspan] .w-20 { width: 36px; height: 36px; border: 0; box-shadow: none; margin-bottom: 12px; }
    .weekly-dashboard td[colspan] i { font-size: 18px; }
    .weekly-dashboard td[colspan] h3 { font-size: 16px; }
    .weekly-dashboard .weekly-monitoring > .border-t { padding: 12px 16px; border-color: var(--weekly-line); background: #fafbfd; color: #7b8797; font-weight: 400; letter-spacing: 0; text-transform: none; }
    .weekly-dashboard .overflow-x-auto::-webkit-scrollbar { height: 5px; }
    .weekly-dashboard .overflow-x-auto::-webkit-scrollbar-thumb { background: #d5dde7; border-radius: 4px; }
    .weekly-dashboard #mobileCards { background: var(--weekly-bg); padding: 12px; gap: 12px; }
    .weekly-dashboard #mobileCards .driver-row { border: 1px solid var(--weekly-line); border-radius: 8px; padding: 14px; box-shadow: 0 1px 3px rgb(30 58 95 / 3%); }
    .weekly-dashboard #mobileCards .driver-row > .flex { gap: 8px; margin-bottom: 12px; align-items: flex-start; }
    .weekly-dashboard #mobileCards h4 { overflow-wrap: anywhere; }
    .weekly-dashboard #mobileCards .ml-3 { min-width: 0; margin-left: 8px; }
    .weekly-dashboard #mobileCards .ml-3 > .flex { flex-wrap: wrap; gap: 4px; }
    @media (max-width: 1200px) {
        .weekly-dashboard .weekly-header { flex-direction: column; align-items: flex-start; }
        .weekly-dashboard .weekly-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .weekly-dashboard .weekly-toolbar { align-items: stretch; }
        .weekly-dashboard .weekly-toolbar > .relative { max-width: none; }
    }
    @media (max-width: 540px) {
        .weekly-dashboard .weekly-actions { flex-wrap: wrap; gap: 6px; }
        .weekly-dashboard .weekly-branches { width: 100%; }
        .weekly-dashboard .weekly-branches a { flex: 1; padding: 7px 8px; text-align: center; }
        .weekly-dashboard .weekly-stats { gap: 10px; }
        .weekly-dashboard .weekly-stat { padding: 12px; }
        .weekly-dashboard .weekly-stat > .relative { gap: 8px; }
        .weekly-dashboard #mobileCards .driver-row > .flex { flex-wrap: wrap; }
    }
    @media (max-width: 350px) {
        .weekly-dashboard .weekly-stats { grid-template-columns: 1fr; }
    }
    .weekly-dashboard .weekly-header > div:first-child > p:last-child { color: #6b7788; text-transform: none; letter-spacing: 0; font-weight: 400; }
    .weekly-dashboard .weekly-date-navigation { border-color: #d5dde7; border-radius: 5px; box-shadow: none; }
    .weekly-dashboard .weekly-date-navigation > a { display: inline-flex; align-items: center; justify-content: center; width: 30px; min-height: 34px; padding: 7px; color: var(--weekly-navy); border-color: #e3e8ef; transition: none; }
    .weekly-dashboard .weekly-date-control { width: 142px; }
    .weekly-dashboard .weekly-date-control input { padding: 7px 8px 7px 10px; border: 0; border-radius: 0; }
    .weekly-dashboard .weekly-date-display { left: 10px; }
    .weekly-dashboard .weekly-toolbar { flex-wrap: wrap; }
    .weekly-dashboard .weekly-toolbar > .relative { min-width: 200px; flex-basis: 260px; }
    .weekly-dashboard table { min-width: 1120px; }
    .weekly-dashboard th:first-child { width: 28%; min-width: 250px; }
    .weekly-dashboard th:not(:first-child):not(:nth-last-child(-n+2)) { min-width: 96px; padding: 10px 6px; }
    .weekly-dashboard th > div:last-child { font: 400 12px/1.5 Arial, sans-serif; color: #8490a1; text-transform: none; opacity: 1; white-space: nowrap; margin-top: 3px; }
    .weekly-dashboard td:not(:first-child):not(:nth-last-child(-n+2)) { padding: 10px 6px; }
    .weekly-dashboard .driver-row td .ml-4 > .flex { flex-wrap: wrap; gap: 4px 8px; }
    .weekly-dashboard .driver-row[data-status="full"] .weekly-avatar,
    .weekly-dashboard .driver-row[data-status="partial"] .weekly-avatar { background: #edf2f8; color: var(--weekly-navy); }
    .weekly-dashboard .driver-row .bg-indigo-100 { background: #edf2f8; color: #456386; font-weight: 500; letter-spacing: 0; }
    .weekly-dashboard .weekly-stat h3.text-amber-500 { color: #966712; }
    .weekly-dashboard .driver-row :is(.text-emerald-600, .text-emerald-700) { color: #34704f; }
    .weekly-dashboard .driver-row .text-amber-500 { color: #966712; }
    .weekly-dashboard .driver-row .text-rose-500 { color: #ad5252; }
    .weekly-dashboard td .w-7 { width: 25px; height: 25px; border-radius: 5px; transition: none; }
    .weekly-dashboard td .w-7 i { font-size: 12px; }
    .weekly-dashboard td .w-7.bg-emerald-100 { background: #edf6f1; color: #34704f; padding: 0; }
    .weekly-dashboard td a.w-7:hover { background: #d8ebe0; color: #275a3e; }
    .weekly-dashboard td .w-7.bg-rose-50 { background: #fbeded; color: #bd7474; }
    .weekly-dashboard .weekly-row-progress { height: 4px; width: 56px; background: #e9edf3; }
    .weekly-dashboard .weekly-row-progress .bg-emerald-500 { background: #5e9574; }
    .weekly-dashboard .weekly-row-progress .bg-amber-400 { background: #c5a566; }
    .weekly-dashboard .weekly-row-progress .bg-rose-400 { background: #c78b8b; }
    .weekly-dashboard .weekly-row-actions > button { border: 1px solid #d4ddea; background: #fff; color: var(--weekly-navy); width: 30px; height: 30px; padding: 5px; border-radius: 5px; }
    .weekly-dashboard .weekly-row-actions > .absolute { width: 230px; border-color: var(--weekly-line); border-radius: 6px; box-shadow: 0 4px 14px rgb(30 58 95 / 10%); padding: 6px 0; }
    .weekly-dashboard .weekly-row-actions > .absolute > p { color: #7b8797; letter-spacing: 0; font-weight: 400; }
    .weekly-dashboard .weekly-row-actions > .absolute a { height: auto; width: auto; border: 0; border-radius: 0; padding: 8px 12px; justify-content: flex-start; font-weight: 500; }
    .weekly-dashboard .weekly-row-actions:focus-within > .absolute { display: block; }
    .weekly-dashboard #mobileCards .driver-row > .flex:last-child { background: #f8fafc; border: 1px solid #edf0f4; border-radius: 5px; padding: 10px; }
    .weekly-dashboard #mobileCards .driver-row > .flex:last-child .uppercase { color: #7b8797; font-weight: 500; }

    .weekly-dashboard .weekly-mobile-check { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 5px; }
    .weekly-dashboard .weekly-mobile-check.weekly-checked { color: #34704f; background: #edf6f1; }
    .weekly-dashboard .weekly-mobile-check.weekly-unchecked { color: #bd7474; background: #fbeded; }
    .weekly-dashboard .weekly-mobile-check i { font-size: 12px; }
</style>

@push('scripts')
<script>
    function filterStatus(status) {
        // Update button styles
        document.querySelectorAll('.status-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-indigo-50', 'text-indigo-600');
            btn.classList.add('text-gray-500', 'hover:bg-gray-50');
        });

        const activeBtn = event.currentTarget;
        activeBtn.classList.add('active', 'bg-indigo-50', 'text-indigo-600');
        activeBtn.classList.remove('text-gray-500', 'hover:bg-gray-50');

        // Filter table rows and mobile cards
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
