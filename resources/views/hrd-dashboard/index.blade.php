@extends('layouts.app')

@section('title', 'Dashboard HRD')
@section('page_title', 'Dashboard HRD')

@section('content')
<div class="space-y-6 pb-8">

    <style>
        .stat-card {
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            border: 1.5px solid #e2e8f0;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.08), 0 4px 8px -4px rgba(15, 23, 42, 0.03);
        }
        .stat-card[data-color="red"].active {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.22), 0 12px 24px -6px rgba(239, 68, 68, 0.12) !important;
            background-color: #fffbfb !important;
            transform: translateY(-2px);
        }
        .stat-card[data-color="yellow"].active {
            border-color: #eab308 !important;
            box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.22), 0 12px 24px -6px rgba(234, 179, 8, 0.12) !important;
            background-color: #fffef2 !important;
            transform: translateY(-2px);
        }
        .stat-card[data-color="orange"].active {
            border-color: #ea580c !important;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.22), 0 12px 24px -6px rgba(234, 88, 12, 0.12) !important;
            background-color: #fffaf5 !important;
            transform: translateY(-2px);
        }
        .stat-card[data-color="rose"].active {
            border-color: #e11d48 !important;
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.22), 0 12px 24px -6px rgba(225, 29, 72, 0.12) !important;
            background-color: #fff5f7 !important;
            transform: translateY(-2px);
        }
        #detail-panel {
            animation: panelSlideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes panelSlideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

    @php
        $totalKaryawan = $totalKaryawanAktif;
        $totalKaryawanMasuk = count(array_intersect($activeKaryawanIds, $absensiMasuk->pluck('karyawan_id')->filter()->unique()->all()));

        // 1. Hadir Normal (Tepat waktu)
        $cntHadirNormal = $karyawanHadirNormal->count();
        $pctHadirNormal = $totalKaryawan > 0 ? round(($cntHadirNormal / $totalKaryawan) * 100, 1) : 0;
        $pctHadirOfMasuk = $totalKaryawanMasuk > 0 ? round(($cntHadirNormal / $totalKaryawanMasuk) * 100, 1) : 0;

        // 2. Belum Absen Masuk
        $cntBelumMasuk = $karyawanBelumAbsen->count();
        $pctBelumMasuk = $totalKaryawan > 0 ? round(($cntBelumMasuk / $totalKaryawan) * 100, 1) : 0;

        // 3. Belum Absen Pulang (dari karyawan yang masuk)
        $cntBelumPulang = $karyawanBelumAbsenPulang->count();
        $pctBelumPulang = $totalKaryawanMasuk > 0 ? round(($cntBelumPulang / $totalKaryawanMasuk) * 100, 1) : 0;
        $cntSudahPulang = max(0, $totalKaryawanMasuk - $cntBelumPulang);

        // 4. Absen Terlambat (dari karyawan yang masuk)
        $cntTerlambat = $karyawanTerlambat->count();
        $pctTerlambat = $totalKaryawanMasuk > 0 ? round(($cntTerlambat / $totalKaryawanMasuk) * 100, 1) : 0;
        $cntTepatWaktu = $cntHadirNormal;

        // 5. Cuti & Izin Berjalan
        $cntCuti = $karyawanCuti->count();
        $pctCuti = $totalKaryawan > 0 ? round(($cntCuti / $totalKaryawan) * 100, 1) : 0;

        // 6. Absen Luar Radius
        $totalPresensi = $totalPresensiHariIni ?? ($absensiMasuk->count() + $absensiLuarRadius->count());
        $cntLuarRadius = $absensiLuarRadius->count();
        $pctLuarRadius = $totalPresensi > 0 ? round(($cntLuarRadius / max(1, $totalPresensi)) * 100, 1) : 0;
        $cntDalamRadius = max(0, $totalPresensi - $cntLuarRadius);

        // Overall rates
        $rateKehadiran = $totalKaryawan > 0 ? round(($totalKaryawanMasuk / $totalKaryawan) * 100, 1) : 0;
        $rateKepatuhanRadius = $totalPresensi > 0 ? round(($cntDalamRadius / max(1, $totalPresensi)) * 100, 1) : 100;
    @endphp

    <!-- Executive Command Header Banner -->
    <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs relative overflow-hidden border border-slate-200">
        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
            {{-- Title & Status --}}
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        HR Analytics Live Command
                    </span>
                    <span class="text-xs text-slate-500 font-medium">
                        <i class="far fa-calendar-alt mr-1 text-slate-400"></i>
                        @if($isSingleDay)
                            {{ $startDate->translatedFormat('l, d F Y') }}
                        @else
                            {{ $startDate->translatedFormat('d M Y') }} s/d {{ $endDate->translatedFormat('d M Y') }}
                        @endif
                    </span>
                    @if($isSingleDay)
                        @if($startDate->isToday())
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200">Hari Ini</span>
                        @elseif($startDate->isYesterday())
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">Kemarin</span>
                        @endif
                    @else
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">Rentang Tanggal</span>
                    @endif
                    @if(request('cabang'))
                        <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 flex items-center gap-1.5">
                            <i class="fas fa-building text-blue-500 text-[10px]"></i> Cabang: {{ request('cabang') }}
                        </span>
                    @endif
                    @if(request('grup'))
                        <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200 flex items-center gap-1.5">
                            <i class="fas fa-layer-group text-purple-500 text-[10px]"></i> Group: {{ request('grup') }}
                        </span>
                    @endif
                    @if(request('departemen'))
                        <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1.5">
                            <i class="fas fa-briefcase text-amber-500 text-[10px]"></i> Departemen: {{ request('departemen') }}
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 flex items-center gap-3">
                    Dashboard Kehadiran HRD
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Ringkasan performa kehadiran, kepatuhan tapping lokasi, kedisiplinan jam masuk, serta status izin kerja operasional.
                </p>
            </div>

            {{-- Filter & Actions Bar --}}
            <div class="flex flex-wrap items-center gap-2.5 bg-slate-50 p-2.5 rounded-xl border border-slate-200 shadow-xs">
                <form id="dashboard-filter-form" action="{{ route('hrd.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    @foreach(request()->except(['tanggal_dari', 'tanggal_sampai', 'tanggal_dashboard', 'cabang', 'grup', 'departemen', 'page']) as $key => $value)
                        @if(!is_array($value))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach

                    {{-- Quick Date Shortcuts --}}
                    <div class="inline-flex rounded-lg shadow-xs bg-white p-0.5 border border-slate-200">
                        <button type="button" onclick="setDashboardDate('{{ \Carbon\Carbon::today()->format('Y-m-d') }}')"
                                class="px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all {{ ($isSingleDay && $startDate->isToday()) ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            Hari Ini
                        </button>
                        <button type="button" onclick="setDashboardDate('{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}')"
                                class="px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all {{ ($isSingleDay && $startDate->isYesterday()) ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            Kemarin
                        </button>
                    </div>

                    {{-- Date Range Inputs (Dari - Sampai) --}}
                    <div class="flex items-center gap-1.5">
                        <div class="relative">
                            <input type="date" id="tanggal_dari" name="tanggal_dari" 
                                   value="{{ $startDate->format('Y-m-d') }}" 
                                   title="Tanggal Dari"
                                   class="rounded-lg bg-white border-slate-200 text-slate-800 text-xs py-1.5 px-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-xs cursor-pointer">
                        </div>
                        <span class="text-xs text-slate-400 font-semibold">s/d</span>
                        <div class="relative">
                            <input type="date" id="tanggal_sampai" name="tanggal_sampai" 
                                   value="{{ $endDate->format('Y-m-d') }}" 
                                   title="Tanggal Sampai"
                                   class="rounded-lg bg-white border-slate-200 text-slate-800 text-xs py-1.5 px-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-xs cursor-pointer">
                        </div>
                    </div>

                    {{-- Cabang Filter --}}
                    @if(count($allCabangs) > 0)
                    <div class="relative">
                        <select name="cabang" id="global_cabang_filter" onchange="this.form.submit()"
                                class="rounded-lg bg-white border-slate-200 text-slate-800 text-xs py-1.5 pr-8 pl-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-xs cursor-pointer">
                            <option value="">Semua Cabang</option>
                            @foreach($allCabangs as $cb)
                                <option value="{{ $cb }}" {{ request('cabang', $selectedCabang) == $cb ? 'selected' : '' }}>{{ $cb }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Department Filter --}}
                    @if(count($allDepartemens) > 0)
                    <div class="relative">
                        <select name="departemen" id="global_department_filter" onchange="this.form.submit()"
                                class="rounded-lg bg-white border-slate-200 text-slate-800 text-xs py-1.5 pr-8 pl-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-xs cursor-pointer">
                            <option value="">Semua Departemen</option>
                            @foreach($allDepartemens as $departemen)
                                <option value="{{ $departemen }}" {{ request('departemen', $selectedDepartemen) == $departemen ? 'selected' : '' }}>{{ $departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Group Filter --}}
                    @if(count($allGroups) > 0)
                    <div class="relative">
                        <select name="grup" id="global_group_filter" onchange="this.form.submit()"
                                class="rounded-lg bg-white border-slate-200 text-slate-800 text-xs py-1.5 pr-8 pl-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-xs cursor-pointer">
                            <option value="">Semua Group</option>
                            @foreach($allGroups as $grp)
                                <option value="{{ $grp }}" {{ request('grup', $selectedGroup) == $grp ? 'selected' : '' }}>{{ $grp }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <button type="submit" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-filter text-[10px]"></i>
                        <span>Filter</span>
                    </button>

                    @php
                        $isFilterActive = request('cabang') || request('grup') || request('departemen') || !($isSingleDay && $startDate->isToday());
                    @endphp
                    @if($isFilterActive)
                        <a href="{{ route('hrd.dashboard') }}" 
                           class="px-2.5 py-1.5 bg-white hover:bg-rose-50 text-slate-500 hover:text-rose-600 text-xs font-semibold rounded-lg border border-slate-200 hover:border-rose-200 transition-colors flex items-center gap-1 shadow-xs" 
                           title="Reset filter ke Hari Ini, Semua Cabang, Departemen & Group">
                            <i class="fas fa-undo-alt text-[10px]"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </form>

                <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                <button type="button" onclick="openExportModal()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-file-excel"></i>
                    <span>Export Rekap</span>
                </button>
            </div>
        </div>

        {{-- Executive Quick Insights Metrics --}}
        <div class="mt-6 pt-5 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="bg-emerald-50/50 rounded-xl p-3.5 border border-emerald-100/80 shadow-xs">
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-[11px] font-semibold uppercase tracking-wider">Pernah Absen Masuk di Periode</span>
                    <i class="fas fa-chart-line text-xs text-emerald-600"></i>
                </div>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-black text-emerald-600">{{ $rateKehadiran }}%</span>
                    <span class="text-xs text-slate-500 font-medium">({{ number_format($totalKaryawanMasuk) }} / {{ number_format($totalKaryawanAktif) }})</span>
                </div>
            </div>
            <div class="bg-blue-50/50 rounded-xl p-3.5 border border-blue-100/80 shadow-xs">
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-[11px] font-semibold uppercase tracking-wider">Ketepatan Masuk</span>
                    <i class="fas fa-stopwatch text-xs text-blue-600"></i>
                </div>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-black text-blue-600">{{ $pctHadirOfMasuk }}%</span>
                    <span class="text-xs text-slate-500 font-medium">({{ number_format($cntHadirNormal) }} tepat)</span>
                </div>
            </div>
            <div class="bg-purple-50/50 rounded-xl p-3.5 border border-purple-100/80 shadow-xs">
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-[11px] font-semibold uppercase tracking-wider">Kepatuhan Geofence</span>
                    <i class="fas fa-map-marked-alt text-xs text-purple-600"></i>
                </div>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-black text-purple-600">{{ $rateKepatuhanRadius }}%</span>
                    <span class="text-xs text-slate-500 font-medium">dalam radius</span>
                </div>
            </div>
            <div class="bg-amber-50/50 rounded-xl p-3.5 border border-amber-100/80 shadow-xs">
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-[11px] font-semibold uppercase tracking-wider">Cuti & Izin Aktif</span>
                    <i class="fas fa-calendar-check text-xs text-amber-600"></i>
                </div>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl font-black text-amber-600">{{ number_format($cntCuti) }}</span>
                    <span class="text-xs text-slate-500 font-medium">karyawan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Executive Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

        {{-- 1. Total Karyawan Aktif --}}
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Karyawan Aktif</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center ring-1 ring-blue-500/15">
                    <i class="fas fa-users text-lg"></i>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalKaryawanAktif) }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Basis headcount aktif</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>100% total aktif</span>
                <span class="font-bold text-blue-600">Total</span>
            </div>
        </div>

        {{-- 2. Belum Absen Masuk --}}
        <div class="stat-card bg-white rounded-2xl shadow-xs p-5 flex flex-col justify-between select-none group"
             id="card-belum-masuk"
             data-color="red"
             onclick="showDetailTable('belum-masuk')"
             title="Klik untuk melihat daftar karyawan yang belum absen masuk">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Belum Absen Masuk</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center ring-1 ring-rose-500/15 group-hover:scale-105 transition-transform">
                    <i class="fas fa-user-times text-lg"></i>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-rose-600 tracking-tight">{{ number_format($cntBelumMasuk) }}</p>
                <p class="text-xs text-rose-500 mt-0.5 font-medium">{{ $pctBelumMasuk }}% dari headcount</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-rose-600 font-semibold">
                <span class="flex items-center gap-1">
                    <i class="fas fa-hand-pointer text-[10px]"></i> Rincian data
                </span>
                <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
            </div>
        </div>

        {{-- 3. Belum Absen Pulang --}}
        <div class="stat-card bg-white rounded-2xl shadow-xs p-5 flex flex-col justify-between select-none group"
             id="card-belum-pulang"
             data-color="yellow"
             onclick="showDetailTable('belum-pulang')"
             title="Klik untuk melihat daftar karyawan yang belum absen pulang">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Belum Absen Pulang</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center ring-1 ring-amber-500/15 group-hover:scale-105 transition-transform">
                    <i class="fas fa-running text-lg"></i>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-amber-600 tracking-tight">{{ number_format($cntBelumPulang) }}</p>
                <p class="text-xs text-amber-600 mt-0.5 font-medium">{{ $pctBelumPulang }}% dari yang hadir</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-amber-600 font-semibold">
                <span class="flex items-center gap-1">
                    <i class="fas fa-hand-pointer text-[10px]"></i> Rincian data
                </span>
                <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
            </div>
        </div>

        {{-- 4. Absen Terlambat --}}
        <div class="stat-card bg-white rounded-2xl shadow-xs p-5 flex flex-col justify-between select-none group"
             id="card-terlambat"
             data-color="orange"
             onclick="showDetailTable('terlambat')"
             title="Klik untuk melihat daftar karyawan yang terlambat">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Terlambat</span>
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center ring-1 ring-orange-500/15 group-hover:scale-105 transition-transform">
                    <i class="fas fa-clock text-lg"></i>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-orange-600 tracking-tight">{{ number_format($cntTerlambat) }}</p>
                <p class="text-xs text-orange-500 mt-0.5 font-medium">> {{ sprintf('%02d:05', $jamBatas) }} WIB ({{ $pctTerlambat }}%)</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-orange-600 font-semibold">
                <span class="flex items-center gap-1">
                    <i class="fas fa-hand-pointer text-[10px]"></i> Rincian data
                </span>
                <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
            </div>
        </div>

        {{-- 5. Cuti & Izin Berjalan --}}
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Cuti & Izin</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center ring-1 ring-purple-500/15">
                    <i class="fas fa-calendar-alt text-lg"></i>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-purple-600 tracking-tight">{{ number_format($cntCuti) }}</p>
                <p class="text-xs text-purple-500 mt-0.5 font-medium">{{ $pctCuti }}% dari headcount</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-purple-600 font-semibold">
                <span>Status Approved</span>
                <i class="fas fa-check-double text-[10px]"></i>
            </div>
        </div>

        {{-- 6. Absen Luar Radius --}}
        <div class="stat-card bg-white rounded-2xl shadow-xs p-5 flex flex-col justify-between select-none group"
             id="card-luar-radius"
             data-color="rose"
             onclick="showDetailTable('luar-radius')"
             title="Klik untuk melihat daftar karyawan yang absen di luar radius">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Luar Radius</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center ring-1 ring-rose-500/15 group-hover:scale-105 transition-transform">
                    <i class="fas fa-map-marker-alt text-lg"></i>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-rose-600 tracking-tight">{{ number_format($cntLuarRadius) }}</p>
                <p class="text-xs text-rose-500 mt-0.5 font-medium">{{ $pctLuarRadius }}% dari total presensi</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-rose-600 font-semibold">
                <span class="flex items-center gap-1">
                    <i class="fas fa-hand-pointer text-[10px]"></i> Rincian data
                </span>
                <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
            </div>
        </div>

    </div>



    <!-- Detail Panel (muncul saat kartu diklik) -->
    <div id="detail-panel" class="hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">

            <!-- Panel Header -->
            <div id="detail-panel-header" class="px-6 py-4.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b transition-colors duration-200">
                <div class="flex items-center gap-3.5">
                    <div id="detail-icon-wrap" class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 shadow-xs">
                        <i id="detail-icon-el" class="fas text-xl"></i>
                    </div>
                    <div>
                        <h3 id="detail-title" class="font-black text-slate-900 text-base leading-tight tracking-tight"></h3>
                        <p id="detail-subtitle" class="text-xs text-slate-500 mt-0.5"></p>
                    </div>
                </div>

                {{-- Quick Table Search Filter & Close --}}
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-64">
                        <input type="text" id="detail_search_input" oninput="filterDetailTable(this.value)" 
                               placeholder="Cari nama atau NIK..." 
                               class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border-slate-200 bg-white shadow-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-400">
                        <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs pointer-events-none"></i>
                    </div>

                    @can('hrd-dashboard-export')
                    <a id="detail-export-button" href="{{ route('hrd.dashboard.export-detail') }}" onclick="return prepareDetailExport(this)"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold whitespace-nowrap shadow-xs"
                       title="Export tabel yang sedang ditampilkan ke Excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    @endcan

                    <span id="detail-badge" class="text-xs font-bold px-3 py-1.5 rounded-full whitespace-nowrap shadow-xs"></span>

                    <button onclick="closeDetailPanel()"
                            class="text-slate-400 hover:text-slate-700 transition-colors p-2 rounded-xl hover:bg-slate-100 flex-shrink-0"
                            title="Tutup panel">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Filter Active Info Bar -->
            @if(request('grup') || request('cabang') || request('departemen'))
            <div id="detail-filter-bar" class="px-6 py-2.5 bg-indigo-50/80 border-b border-indigo-100 flex flex-wrap items-center gap-2.5 text-xs text-indigo-800">
                <i class="fas fa-filter text-indigo-500"></i>
                <span>Filter aktif:</span>
                @if(request('cabang'))
                    <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded font-bold">Cabang: {{ request('cabang') }}</span>
                @endif
                @if(request('grup'))
                    <span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded font-bold">Group: {{ request('grup') }}</span>
                @endif
                @if(request('departemen'))
                    <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded font-bold">Departemen: {{ request('departemen') }}</span>
                @endif
                <a href="{{ route('hrd.dashboard', array_merge(request()->except(['grup', 'cabang', 'page']), ['tanggal_dari' => $startDate->format('Y-m-d'), 'tanggal_sampai' => $endDate->format('Y-m-d')])) }}" 
                   class="ml-auto text-indigo-600 hover:text-indigo-900 underline font-semibold">Hapus Filter Cabang & Group</a>
            </div>
            @endif

            <!-- Tables Container -->
            <div style="max-height: 540px; overflow-y: auto;" class="divide-y divide-slate-100">

                <!-- Table: Belum Absen Masuk -->
                <div id="table-belum-masuk" class="hidden">
                    @if($karyawanBelumAbsen->count() > 0)
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider w-12">#</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider w-36">NIK</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nama Karyawan</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Divisi / Cabang</th>
                                @if(! $isSingleDay)
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tanggal Tanpa Scan Masuk</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($karyawanBelumAbsen as $i => $k)
                            <tr class="hover:bg-rose-50/40 transition-colors" data-grup="{{ is_array($k->grup) ? implode(',', $k->grup) : ($k->grup ?? '') }}" data-search="{{ strtolower($k->nik . ' ' . $k->nama_lengkap . ' ' . ($k->divisi ?? '') . ' ' . ($k->cabang ?? '') . ' ' . implode(' ', $k->tanggal_belum_masuk ?? [])) }}">
                                <td class="px-6 py-3.5 text-slate-400 text-xs font-medium">{{ $i + 1 }}</td>
                                <td class="px-6 py-3.5">
                                    <span class="font-mono text-xs text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 font-semibold">{{ $k->nik }}</span>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-slate-800">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                            {{ strtoupper(mb_substr($k->nama_lengkap ?? 'K', 0, 2)) }}
                                        </div>
                                        <a href="{{ route('master.karyawan.show', $k->id) }}" target="_blank"
                                           class="hover:text-rose-600 transition-colors inline-flex items-center gap-1.5 group font-bold">
                                            <span>{{ $k->nama_lengkap }}</span>
                                            <i class="fas fa-external-link-alt text-[10px] opacity-0 group-hover:opacity-70 transition-opacity"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 uppercase">
                                            {{ $k->divisi ?: '-' }}
                                        </span>
                                        @if($k->cabang)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $k->cabang }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                @if(! $isSingleDay)
                                <td class="px-6 py-3.5 text-xs text-rose-700 font-semibold">
                                    @forelse($k->tanggal_belum_masuk ?? [] as $tanggal)
                                        <span class="inline-block mr-1 mb-1 px-2 py-0.5 rounded bg-rose-50 border border-rose-200">{{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</span>
                                    @empty
                                        <span class="text-slate-500">Tidak ada scan masuk dalam periode</span>
                                    @endforelse
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="py-20 text-center text-slate-500 flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3 text-2xl shadow-inner">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <p class="font-bold text-slate-800 text-base">Semua Karyawan Telah Absen Masuk</p>
                        <p class="text-xs text-slate-400 mt-0.5">Tidak ada karyawan yang belum tapping presensi masuk.</p>
                    </div>
                    @endif
                </div>{{-- end table-belum-masuk --}}

                <!-- Table: Hadir Normal -->
                <div id="table-hadir-normal" class="hidden">
                    @if($karyawanHadirNormal->count() > 0)
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-emerald-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider w-12">#</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider w-36">NIK</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nama Karyawan</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Divisi / Cabang</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Waktu Tapping Masuk</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Jarak / Radius</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($karyawanHadirNormal as $i => $absen)
                            <tr class="hover:bg-emerald-50/40 transition-colors" data-grup="{{ $absen->karyawan && is_array($absen->karyawan->grup) ? implode(',', $absen->karyawan->grup) : ($absen->karyawan->grup ?? '') }}" data-search="{{ strtolower(($absen->karyawan->nik ?? '') . ' ' . ($absen->karyawan->nama_lengkap ?? '') . ' ' . ($absen->karyawan->divisi ?? '') . ' ' . ($absen->karyawan->cabang ?? '')) }}">
                                <td class="px-6 py-3.5 text-slate-400 text-xs font-medium">{{ $i + 1 }}</td>
                                <td class="px-6 py-3.5"><span class="font-mono text-xs text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 font-semibold">{{ $absen->karyawan->nik ?? '-' }}</span></td>
                                <td class="px-6 py-3.5 font-medium text-slate-800">
                                    @if($absen->karyawan)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center flex-shrink-0">{{ strtoupper(mb_substr($absen->karyawan->nama_lengkap ?? 'K', 0, 2)) }}</div>
                                        <a href="{{ route('master.karyawan.show', $absen->karyawan->id) }}" target="_blank" class="hover:text-emerald-600 transition-colors inline-flex items-center gap-1.5 group font-bold"><span>{{ $absen->karyawan->nama_lengkap }}</span><i class="fas fa-external-link-alt text-[10px] opacity-0 group-hover:opacity-70 transition-opacity"></i></a>
                                    </div>
                                    @else <span class="text-slate-400 italic">Data karyawan tidak ditemukan</span> @endif
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 uppercase">{{ $absen->karyawan->divisi ?? '-' }}</span>
                                        @if($absen->karyawan && $absen->karyawan->cabang)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $absen->karyawan->cabang }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3.5"><span class="inline-flex items-center gap-1.5 font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full text-xs shadow-xs"><i class="fas fa-check text-[10px]"></i>{{ $isSingleDay ? \Carbon\Carbon::parse($absen->waktu)->format('H:i:s') : \Carbon\Carbon::parse($absen->waktu)->format('d/m/Y H:i:s') }} WIB</span></td>
                                <td class="px-6 py-3.5">
                                    @if($absen->jarak_absen_meter !== null)
                                        <span class="inline-flex items-center gap-1.5 font-semibold text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-md text-xs">
                                            <i class="fas fa-location-arrow text-[10px]"></i>
                                            {{ $absen->jarak_absen_meter < 1000 ? number_format($absen->jarak_absen_meter, 1, ',', '.') . ' m' : number_format($absen->jarak_absen_meter / 1000, 2, ',', '.') . ' km' }}
                                        </span>
                                        <span class="block text-[10px] text-slate-400 mt-1">Radius {{ number_format($absen->radius_absensi_meter) }} m{{ $absen->nama_lokasi_absensi ? ' · ' . $absen->nama_lokasi_absensi : '' }}</span>
                                    @else
                                        <span class="text-xs text-slate-400">GPS tidak tersedia</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="py-20 text-center text-slate-500"><i class="fas fa-user-check text-4xl text-slate-300 mb-3"></i><p class="font-bold text-slate-800 text-base">Belum ada karyawan hadir normal</p></div>
                    @endif
                </div>{{-- end table-hadir-normal --}}

                <!-- Table: Belum Absen Pulang -->
                <div id="table-belum-pulang" class="hidden">
                    @if($karyawanBelumAbsenPulang->count() > 0)
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider w-12">#</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider w-36">NIK</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nama Karyawan</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Divisi / Cabang</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tanggal Belum Absen Pulang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($karyawanBelumAbsenPulang as $i => $k)
                            <tr class="hover:bg-amber-50/40 transition-colors" data-grup="{{ is_array($k->grup) ? implode(',', $k->grup) : ($k->grup ?? '') }}" data-search="{{ strtolower($k->nik . ' ' . $k->nama_lengkap . ' ' . ($k->divisi ?? '') . ' ' . ($k->cabang ?? '')) }}">
                                <td class="px-6 py-3.5 text-slate-400 text-xs font-medium">{{ $i + 1 }}</td>
                                <td class="px-6 py-3.5">
                                    <span class="font-mono text-xs text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 font-semibold">{{ $k->nik }}</span>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-slate-800">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                            {{ strtoupper(mb_substr($k->nama_lengkap ?? 'K', 0, 2)) }}
                                        </div>
                                        <a href="{{ route('master.karyawan.show', $k->id) }}" target="_blank"
                                           class="hover:text-amber-600 transition-colors inline-flex items-center gap-1.5 group font-bold">
                                            <span>{{ $k->nama_lengkap }}</span>
                                            <i class="fas fa-external-link-alt text-[10px] opacity-0 group-hover:opacity-70 transition-opacity"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 uppercase">
                                            {{ $k->divisi ?: '-' }}
                                        </span>
                                        @if($k->cabang)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $k->cabang }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($k->tanggal_belum_pulang ?? [] as $tanggalBelumPulang)
                                            <span class="whitespace-nowrap rounded-md bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-800 border border-amber-200">{{ \Carbon\Carbon::parse($tanggalBelumPulang)->format('d-m-Y') }}</span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="py-20 text-center text-slate-500 flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3 text-2xl shadow-inner">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <p class="font-bold text-slate-800 text-base">Semua Karyawan Telah Absen Pulang</p>
                        <p class="text-xs text-slate-400 mt-0.5">Seluruh karyawan yang tapping masuk telah menyelesaikan absensi pulang.</p>
                    </div>
                    @endif
                </div>{{-- end table-belum-pulang --}}

                <!-- Table: Terlambat -->
                <div id="table-terlambat" class="hidden">
                    @if($karyawanTerlambat->count() > 0)
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider w-12">#</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nama Karyawan</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Divisi / Cabang</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Waktu Tapping Masuk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($karyawanTerlambat as $i => $absen)
                            <tr class="hover:bg-orange-50/40 transition-colors" data-grup="{{ $absen->karyawan && is_array($absen->karyawan->grup) ? implode(',', $absen->karyawan->grup) : ($absen->karyawan->grup ?? '') }}" data-search="{{ strtolower(($absen->karyawan->nik ?? '') . ' ' . ($absen->karyawan->nama_lengkap ?? '') . ' ' . ($absen->karyawan->divisi ?? '') . ' ' . ($absen->karyawan->cabang ?? '')) }}">
                                <td class="px-6 py-3.5 text-slate-400 text-xs font-medium">{{ $i + 1 }}</td>
                                <td class="px-6 py-3.5 font-medium text-slate-800">
                                    @if($absen->karyawan)
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                                {{ strtoupper(mb_substr($absen->karyawan->nama_lengkap ?? 'K', 0, 2)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('master.karyawan.show', $absen->karyawan->id) }}" target="_blank"
                                                   class="hover:text-orange-600 transition-colors inline-flex items-center gap-1.5 group font-bold">
                                                    <span>{{ $absen->karyawan->nama_lengkap }}</span>
                                                    <i class="fas fa-external-link-alt text-[10px] opacity-0 group-hover:opacity-70 transition-opacity"></i>
                                                </a>
                                                <span class="block font-mono text-[11px] text-slate-400">{{ $absen->karyawan->nik }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Data karyawan tidak ditemukan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 uppercase">
                                            {{ $absen->karyawan->divisi ?? '-' }}
                                        </span>
                                        @if($absen->karyawan && $absen->karyawan->cabang)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $absen->karyawan->cabang }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 font-bold text-orange-700 bg-orange-50 border border-orange-200 px-3 py-1 rounded-full text-xs shadow-xs">
                                        <i class="fas fa-clock text-[10px]"></i>
                                        {{ $isSingleDay ? \Carbon\Carbon::parse($absen->waktu)->format('H:i:s') : \Carbon\Carbon::parse($absen->waktu)->format('d/m/Y H:i:s') }} WIB
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="py-20 text-center text-slate-500 flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3 text-2xl shadow-inner">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <p class="font-bold text-slate-800 text-base">Nol Keterlambatan</p>
                        <p class="text-xs text-slate-400 mt-0.5">Semua karyawan yang hadir masuk tepat waktu sebelum jam batas.</p>
                    </div>
                    @endif
                </div>{{-- end table-terlambat --}}

                <!-- Table: Absen Luar Radius -->
                <div id="table-luar-radius" class="hidden">
                    @if($absensiLuarRadius->count() > 0)
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider w-12">#</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nama Karyawan</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Divisi / Cabang</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Detail Lokasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($absensiLuarRadius as $i => $absen)
                            <tr class="hover:bg-rose-50/40 transition-colors" data-grup="{{ $absen->karyawan && is_array($absen->karyawan->grup) ? implode(',', $absen->karyawan->grup) : ($absen->karyawan->grup ?? '') }}" data-search="{{ strtolower(($absen->karyawan->nik ?? '') . ' ' . ($absen->karyawan->nama_lengkap ?? '') . ' ' . ($absen->tipe ?? '') . ' ' . ($absen->detail_lokasi ?? '') . ' ' . ($absen->karyawan->cabang ?? '')) }}">
                                <td class="px-6 py-3.5 text-slate-400 text-xs font-medium">{{ $i + 1 }}</td>
                                <td class="px-6 py-3.5 font-medium text-slate-800">
                                    @if($absen->karyawan)
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                                {{ strtoupper(mb_substr($absen->karyawan->nama_lengkap ?? 'K', 0, 2)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('master.karyawan.show', $absen->karyawan->id) }}" target="_blank"
                                                   class="hover:text-rose-600 transition-colors inline-flex items-center gap-1.5 group font-bold">
                                                    <span>{{ $absen->karyawan->nama_lengkap }}</span>
                                                    <i class="fas fa-external-link-alt text-[10px] opacity-0 group-hover:opacity-70 transition-opacity"></i>
                                                </a>
                                                <span class="block font-mono text-[11px] text-slate-400">{{ $absen->karyawan->nik }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Data karyawan tidak ditemukan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 uppercase">
                                            {{ $absen->karyawan->divisi ?? '-' }}
                                        </span>
                                        @if($absen->karyawan && $absen->karyawan->cabang)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $absen->karyawan->cabang }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 font-bold text-rose-700 bg-rose-50 border border-rose-200 px-3 py-1 rounded-full text-xs shadow-xs">
                                        <i class="fas fa-clock text-[10px]"></i>
                                        {{ $isSingleDay ? \Carbon\Carbon::parse($absen->waktu)->format('H:i:s') : \Carbon\Carbon::parse($absen->waktu)->format('d/m/Y H:i:s') }} WIB
                                    </span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold {{ $absen->tipe == 'Masuk' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $absen->tipe }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-xs text-slate-600 font-medium">
                                    <div class="flex items-center gap-1.5 text-slate-700">
                                        <i class="fas fa-map-pin text-rose-500"></i>
                                        <span>{{ $absen->detail_lokasi ?: 'Koordinat di luar radius' }}</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="py-20 text-center text-slate-500 flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3 text-2xl shadow-inner">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <p class="font-bold text-slate-800 text-base">Kepatuhan Lokasi 100%</p>
                        <p class="text-xs text-slate-400 mt-0.5">Seluruh presensi tapping masuk & pulang berada di dalam radius kantor yang ditentukan.</p>
                    </div>
                    @endif
                </div>{{-- end table-luar-radius --}}

            </div>{{-- end Tables Container --}}

        </div>{{-- end detail-panel card --}}
    </div>{{-- end detail-panel --}}

</div>

<!-- Modal Export Rekap Absen -->
<div id="exportModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeExportModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('hrd.dashboard.export') }}" method="GET">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Export Rekap Absensi (Excel)
                    </h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Laporan ini berisi rekap absensi lengkap termasuk informasi kehadiran, keterlambatan, pulang cepat, dan ketidakhadiran karyawan.
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-download mr-2 mt-1"></i> Export Excel
                    </button>
                    <button type="button" onclick="closeExportModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $authUser = auth()->user();
    $karyawan = $authUser?->karyawan;
    $isAuthorizedApprover = false;
    
    if ($karyawan) {
        $pekerjaan = strtoupper($karyawan->pekerjaan ?? '');
        if (in_array($pekerjaan, ['HRD', 'IT'])) {
            $isAuthorizedApprover = true;
        } else {
            // Check if they are a supervisor (have subordinates)
            $subordinatesCount = \App\Models\Karyawan::where('nik_supervisor', $karyawan->nik)->count();
            if ($subordinatesCount > 0) {
                $isAuthorizedApprover = true;
            }
        }
    }
    
    // Also allow super-admin or specific users
    if ($authUser && ($authUser->hasRole('super-admin') || $authUser->username === 'kiky')) {
        $isAuthorizedApprover = true;
    }
@endphp

@if($isAuthorizedApprover)
    <!-- Modal Notifikasi Persetujuan Absensi -->
    <div id="approvalNotifModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="bg-blue-600 px-4 py-4 flex items-center justify-center relative">
                <div class="absolute -top-6 -right-6 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-2 shadow-inner">
                        <i class="fas fa-bell text-3xl text-blue-600 animate-pulse"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white text-center">Permintaan Persetujuan</h3>
                </div>
            </div>
            
            <div class="p-6 text-center">
                <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                    Terdapat <strong id="approvalNotifCount" class="text-blue-600 text-lg">0</strong> permohonan izin/absensi yang menunggu persetujuan Anda.
                </p>
                
                <div class="flex flex-col gap-2">
                    <a href="{{ route('master.persetujuan-absensi.index') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow transition-colors flex items-center justify-center">
                        <i class="fas fa-external-link-alt mr-2"></i> Tinjau Sekarang
                    </a>
                    <button onclick="closeApprovalNotifModal()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        Nanti Saja
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // Check session storage so we don't spam the user every time they go to dashboard
            if (sessionStorage.getItem('approval_notif_shown')) {
                return;
            }

            try {
                // Fetch attendance requests
                const resAtt = await fetch('{{ url("/master/api/admin/pending-attendance") }}');
                const dataAtt = await resAtt.json();
                
                // Fetch permission/leave requests
                const resPerm = await fetch('{{ url("/master/api/admin/pending-permissions") }}');
                const dataPerm = await resPerm.json();
                
                const totalPending = dataAtt.length + dataPerm.length;
                
                if (totalPending > 0) {
                    const modal = document.getElementById('approvalNotifModal');
                    document.getElementById('approvalNotifCount').innerText = totalPending;
                    
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    
                    // Trigger animation
                    setTimeout(() => {
                        modal.classList.remove('opacity-0');
                        modal.querySelector('.transform').classList.remove('scale-95');
                        modal.querySelector('.transform').classList.add('scale-100');
                    }, 50);
                    
                    // Mark as shown for this session
                    sessionStorage.setItem('approval_notif_shown', 'true');
                }
            } catch (err) {
                console.error('Error fetching pending approvals:', err);
            }
        });

        function closeApprovalNotifModal() {
            const modal = document.getElementById('approvalNotifModal');
            modal.classList.add('opacity-0');
            modal.querySelector('.transform').classList.remove('scale-100');
            modal.querySelector('.transform').classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
@endif

<script>
    var _activeTable = null;

    function prepareDetailExport(link) {
        if (!_activeTable) return false;

        var params = new URLSearchParams(window.location.search);
        params.set('kategori', _activeTable);
        params.delete('page');
        params.delete('q');
        var search = document.getElementById('detail_search_input').value.trim();
        if (search) params.set('q', search);

        link.href = @json(route('hrd.dashboard.export-detail')) + '?' + params.toString();
        return true;
    }

    // Konfigurasi tiap kategori kartu
    var DETAIL_CONFIG = {
        'belum-masuk': {
            title: 'Karyawan Belum Absen Masuk',
            subtitle: 'Daftar karyawan dengan tanggal tanpa scan masuk dalam periode terpilih',
            icon: 'fa-user-times',
            iconBg: '#fee2e2',
            iconColor: '#dc2626',
            headerBg: '#fff5f5',
            headerBorder: '#fecaca',
            badgeBg: '#fee2e2',
            badgeColor: '#991b1b',
            count: {{ $karyawanBelumAbsen->count() }},
        },
        'hadir-normal': {
            title: 'Karyawan Hadir Normal',
            subtitle: 'Daftar karyawan yang tapping masuk tepat waktu hari ini',
            icon: 'fa-user-check',
            iconBg: '#dcfce7',
            iconColor: '#16a34a',
            headerBg: '#f0fdf4',
            headerBorder: '#86efac',
            badgeBg: '#dcfce7',
            badgeColor: '#166534',
            count: {{ $karyawanHadirNormal->count() }},
        },
        'belum-pulang': {
            title: 'Karyawan Belum Absen Pulang',
            subtitle: 'Daftar karyawan yang sudah absen masuk tetapi belum absen pulang pada tanggal dalam periode yang dipilih',
            icon: 'fa-running',
            iconBg: '#fef9c3',
            iconColor: '#ca8a04',
            headerBg: '#fefce8',
            headerBorder: '#fde047',
            badgeBg: '#fef9c3',
            badgeColor: '#713f12',
            count: {{ $karyawanBelumAbsenPulang->count() }},
        },
        'terlambat': {
            title: 'Karyawan Absen Terlambat',
            subtitle: 'Daftar karyawan yang absen melebihi batas jam masuk ({{ sprintf('%02d:00', $jamBatas) }})',
            icon: 'fa-clock',
            iconBg: '#ffedd5',
            iconColor: '#ea580c',
            headerBg: '#fff7ed',
            headerBorder: '#fdba74',
            badgeBg: '#ffedd5',
            badgeColor: '#9a3412',
            count: {{ $karyawanTerlambat->count() }},
        },
        'luar-radius': {
            title: 'Karyawan Absen Luar Radius',
            subtitle: 'Daftar karyawan yang absen di luar radius yang telah ditentukan',
            icon: 'fa-map-marker-alt',
            iconBg: '#ffe4e6',
            iconColor: '#e11d48',
            headerBg: '#fff1f2',
            headerBorder: '#fda4af',
            badgeBg: '#ffe4e6',
            badgeColor: '#9f1239',
            count: {{ $absensiLuarRadius->count() }},
        },
    };

    /**
     * Tampilkan panel detail untuk kategori tertentu.
     * Toggle: klik kartu yang sama = tutup panel.
     */
    function showDetailTable(type) {
        var panel = document.getElementById('detail-panel');
        var isSame = (_activeTable === type);

        // Reset search input saat berganti tabel
        var searchInput = document.getElementById('detail_search_input');
        if (searchInput) searchInput.value = '';

        // Nonaktifkan semua kartu
        document.querySelectorAll('.stat-card').forEach(function(c) {
            c.classList.remove('active');
        });

        // Sembunyikan semua sub-tabel
        ['belum-masuk', 'hadir-normal', 'belum-pulang', 'terlambat', 'luar-radius'].forEach(function(t) {
            var el = document.getElementById('table-' + t);
            if (el) el.classList.add('hidden');
        });

        // Toggle: jika kartu yang sama diklik, tutup panel
        if (isSame) {
            _activeTable = null;
            panel.classList.add('hidden');
            return;
        }

        _activeTable = type;
        var cfg = DETAIL_CONFIG[type];
        if (!cfg) return;

        // Aktifkan kartu statistik yang diklik
        var card = document.getElementById('card-' + type);
        if (card) card.classList.add('active');

        // Update panel header style
        var header = document.getElementById('detail-panel-header');
        header.style.backgroundColor = cfg.headerBg;
        header.style.borderBottomColor = cfg.headerBorder;

        var iconWrap = document.getElementById('detail-icon-wrap');
        iconWrap.style.backgroundColor = cfg.iconBg;

        var iconEl = document.getElementById('detail-icon-el');
        iconEl.className = 'fas ' + cfg.icon + ' text-xl';
        iconEl.style.color = cfg.iconColor;

        document.getElementById('detail-title').textContent = cfg.title;
        document.getElementById('detail-subtitle').textContent = cfg.subtitle;

        var badge = document.getElementById('detail-badge');
        badge.textContent = cfg.count + ' Orang';
        badge.style.backgroundColor = cfg.badgeBg;
        badge.style.color = cfg.badgeColor;

        // Tampilkan sub-tabel yang relevan
        var activeTableEl = document.getElementById('table-' + type);
        if (activeTableEl) {
            activeTableEl.classList.remove('hidden');
            activeTableEl.querySelectorAll('tbody tr').forEach(function(r) {
                r.style.display = '';
            });
        }

        // Tampilkan panel
        panel.classList.remove('hidden');

        // Scroll ke panel dengan smooth
        setTimeout(function() {
            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 80);
    }

    /** Set tanggal cepat lalu submit form secara otomatis */
    function setDashboardDate(date) {
        var inputDari = document.getElementById('tanggal_dari');
        var inputSampai = document.getElementById('tanggal_sampai');
        if (inputDari) inputDari.value = date;
        if (inputSampai) inputSampai.value = date;
        var form = document.getElementById('dashboard-filter-form');
        if (form) {
            form.submit();
        }
    }

    /** Tutup panel detail dan reset semua state */
    function closeDetailPanel() {
        var panel = document.getElementById('detail-panel');
        panel.classList.add('hidden');
        document.querySelectorAll('.stat-card').forEach(function(c) {
            c.classList.remove('active');
        });
        var searchInput = document.getElementById('detail_search_input');
        if (searchInput) searchInput.value = '';
        if (_activeTable) {
            var activeTableEl = document.getElementById('table-' + _activeTable);
            if (activeTableEl) {
                activeTableEl.querySelectorAll('tbody tr').forEach(function(r) {
                    r.style.display = '';
                });
            }
        }
        _activeTable = null;
    }

    function openExportModal() {
        document.getElementById('exportModal').classList.remove('hidden');
    }
    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    /**
     * Live search pada panel detail (NIK / Nama / Divisi)
     */
    function filterDetailTable(query) {
        query = (query || '').toLowerCase().trim();
        if (!_activeTable) return;
        var activeTableEl = document.getElementById('table-' + _activeTable);
        if (!activeTableEl) return;

        var rows = activeTableEl.querySelectorAll('tbody tr[data-search]');
        var visibleCount = 0;

        rows.forEach(function(row) {
            var searchData = row.getAttribute('data-search') || '';
            var match = !query || searchData.includes(query);
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        var badge = document.getElementById('detail-badge');
        if (badge && DETAIL_CONFIG[_activeTable]) {
            if (!query) {
                badge.textContent = DETAIL_CONFIG[_activeTable].count + ' Orang';
            } else {
                badge.textContent = visibleCount + ' / ' + DETAIL_CONFIG[_activeTable].count + ' Orang';
            }
        }
    }
</script>
@endsection
