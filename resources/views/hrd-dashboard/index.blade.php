@extends('layouts.app')

@section('title', 'Dashboard HRD')
@section('page_title', 'Dashboard HRD')

@section('content')
<div class="space-y-6">

    <style>
        .stat-card {
            transition: all 0.2s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px 0 rgba(0,0,0,0.08);
        }
        .stat-card[data-color="red"].active {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239,68,68,0.13), 0 4px 16px 0 rgba(239,68,68,0.10);
            background-color: #fff5f5;
            transform: translateY(-2px);
        }
        .stat-card[data-color="yellow"].active {
            border-color: #eab308;
            box-shadow: 0 0 0 4px rgba(234,179,8,0.13), 0 4px 16px 0 rgba(234,179,8,0.10);
            background-color: #fefce8;
            transform: translateY(-2px);
        }
        .stat-card[data-color="orange"].active {
            border-color: #f97316;
            box-shadow: 0 0 0 4px rgba(249,115,22,0.13), 0 4px 16px 0 rgba(249,115,22,0.10);
            background-color: #fff7ed;
            transform: translateY(-2px);
        }
        .stat-card[data-color="rose"].active {
            border-color: #e11d48;
            box-shadow: 0 0 0 4px rgba(225,29,72,0.13), 0 4px 16px 0 rgba(225,29,72,0.10);
            background-color: #fff1f2;
            transform: translateY(-2px);
        }
        .diagram-stat-card {
            transition: all 0.2s ease;
            border: 1.5px solid #e5e7eb;
        }
        .diagram-stat-card[data-color="red"].active {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.18), 0 4px 16px 0 rgba(239,68,68,0.08);
            background-color: #fffbfb;
        }
        .diagram-stat-card[data-color="yellow"].active {
            border-color: #eab308 !important;
            box-shadow: 0 0 0 3px rgba(234,179,8,0.18), 0 4px 16px 0 rgba(234,179,8,0.08);
            background-color: #fffef2;
        }
        .diagram-stat-card[data-color="orange"].active {
            border-color: #ea580c !important;
            box-shadow: 0 0 0 3px rgba(234,88,12,0.18), 0 4px 16px 0 rgba(234,88,12,0.08);
            background-color: #fffaf5;
        }
        .diagram-stat-card[data-color="rose"].active {
            border-color: #e11d48 !important;
            box-shadow: 0 0 0 3px rgba(225,29,72,0.18), 0 4px 16px 0 rgba(225,29,72,0.08);
            background-color: #fff5f7;
        }
        #detail-panel {
            animation: panelSlideDown 0.28s cubic-bezier(0.16,1,0.3,1);
        }
        @keyframes panelSlideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

    <!-- Welcome Message & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Dashboard HRD</h2>
            <p class="text-gray-500">Ringkasan aktivitas kehadiran karyawan pada <strong>{{ $filterDate->translatedFormat('l, d F Y') }}</strong>.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('hrd.dashboard') }}" method="GET" class="flex items-center gap-2">
                @foreach(request()->except(['tanggal_dashboard', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label for="tanggal_dashboard" class="text-sm text-gray-600 font-medium whitespace-nowrap">Tanggal:</label>
                <input type="date" id="tanggal_dashboard" name="tanggal_dashboard" 
                       value="{{ request('tanggal_dashboard', $filterDate->format('Y-m-d')) }}" 
                       class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-1.5">
                <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @if(request('tanggal_dashboard'))
                    <a href="{{ route('hrd.dashboard', request()->except(['tanggal_dashboard', 'page'])) }}" class="px-3 py-1.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors" title="Reset Tanggal">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>

            {{-- Filter Group --}}
            @if(count($allGroups) > 0)
            <div class="flex items-center gap-2">
                <label for="global_group_filter" class="text-sm text-gray-600 font-medium whitespace-nowrap">
                    <i class="fas fa-layer-group mr-1 text-indigo-500"></i> Group:
                </label>
                <select id="global_group_filter" onchange="applyGroupFilter(this.value)"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-1.5 pr-8">
                    <option value="">Semua Group</option>
                    @foreach($allGroups as $grp)
                        <option value="{{ $grp }}">{{ $grp }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="resetGroupFilter()" id="reset_group_btn"
                        class="hidden px-2 py-1.5 bg-gray-100 text-gray-600 text-sm rounded-md hover:bg-gray-200 transition-colors" title="Reset Filter Group">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            <button onclick="openExportModal()" class="px-4 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors flex items-center shadow-sm">
                <i class="fas fa-file-excel mr-2"></i> Rekap Absen
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">

        {{-- Total Karyawan Aktif (non-clickable) --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-2xl text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Karyawan Aktif</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalKaryawanAktif) }}</p>
            </div>
        </div>

        {{-- Belum Absen Masuk (clickable) --}}
        <div class="stat-card bg-white rounded-lg shadow-sm p-6 flex items-center gap-4 select-none"
             id="card-belum-masuk"
             data-color="red"
             onclick="showDetailTable('belum-masuk')"
             title="Klik untuk melihat daftar karyawan yang belum absen masuk">
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-times text-2xl text-red-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Belum Absen Masuk</p>
                <p class="text-3xl font-bold text-red-600">{{ number_format($karyawanBelumAbsen->count()) }}</p>
                <p class="text-xs text-red-400 mt-0.5"><i class="fas fa-hand-pointer text-xs mr-1"></i>Klik untuk detail</p>
            </div>
        </div>

        {{-- Belum Absen Pulang (clickable) --}}
        <div class="stat-card bg-white rounded-lg shadow-sm p-6 flex items-center gap-4 select-none"
             id="card-belum-pulang"
             data-color="yellow"
             onclick="showDetailTable('belum-pulang')"
             title="Klik untuk melihat daftar karyawan yang belum absen pulang">
            <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-running text-2xl text-yellow-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Belum Absen Pulang</p>
                <p class="text-3xl font-bold text-yellow-600">{{ number_format($karyawanBelumAbsenPulang->count()) }}</p>
                <p class="text-xs text-yellow-500 mt-0.5"><i class="fas fa-hand-pointer text-xs mr-1"></i>Klik untuk detail</p>
            </div>
        </div>

        {{-- Absen Terlambat (clickable) --}}
        <div class="stat-card bg-white rounded-lg shadow-sm p-6 flex items-center gap-4 select-none"
             id="card-terlambat"
             data-color="orange"
             onclick="showDetailTable('terlambat')"
             title="Klik untuk melihat daftar karyawan yang terlambat">
            <div class="w-14 h-14 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-2xl text-orange-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Absen Terlambat</p>
                <p class="text-3xl font-bold text-orange-600">{{ number_format($karyawanTerlambat->count()) }}</p>
                <p class="text-xs text-orange-400 mt-0.5"><i class="fas fa-hand-pointer text-xs mr-1"></i>Klik untuk detail</p>
            </div>
        </div>

        {{-- Cuti / Izin Berjalan (non-clickable) --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-alt text-2xl text-purple-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Cuti & Izin Berjalan</p>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($karyawanCuti->count()) }}</p>
            </div>
        </div>

        {{-- Absen Luar Radius (clickable) --}}
        <div class="stat-card bg-white rounded-lg shadow-sm p-6 flex items-center gap-4 select-none"
             id="card-luar-radius"
             data-color="rose"
             onclick="showDetailTable('luar-radius')"
             title="Klik untuk melihat daftar karyawan yang absen di luar radius">
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-map-marker-alt text-2xl text-red-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Absen Luar Radius</p>
                <p class="text-3xl font-bold text-red-600">{{ number_format($absensiLuarRadius->count()) }}</p>
                <p class="text-xs text-red-400 mt-0.5"><i class="fas fa-hand-pointer text-xs mr-1"></i>Klik untuk detail</p>
            </div>
        </div>

    </div>{{-- end Summary Cards --}}

    <!-- Section: Diagram Analisis Variabel Kehadiran (Dipisah per Variabel) -->
    @php
        $totalKaryawan = $totalKaryawanAktif;
        $totalKaryawanMasuk = max(0, $totalKaryawan - $karyawanBelumAbsen->count());

        // 1. Hadir Normal (Tepat waktu)
        $cntHadirNormal = max(0, $totalKaryawanMasuk - $karyawanTerlambat->count());
        $pctHadirNormal = $totalKaryawan > 0 ? round(($cntHadirNormal / $totalKaryawan) * 100, 1) : 0;

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
        $cntTepatWaktu = max(0, $totalKaryawanMasuk - $cntTerlambat);

        // 5. Cuti & Izin Berjalan
        $cntCuti = $karyawanCuti->count();
        $pctCuti = $totalKaryawan > 0 ? round(($cntCuti / $totalKaryawan) * 100, 1) : 0;

        // 6. Absen Luar Radius
        $totalPresensi = $totalPresensiHariIni ?? ($absensiMasuk->count() + $absensiLuarRadius->count());
        $cntLuarRadius = $absensiLuarRadius->count();
        $pctLuarRadius = $totalPresensi > 0 ? round(($cntLuarRadius / max(1, $totalPresensi)) * 100, 1) : 0;
        $cntDalamRadius = max(0, $totalPresensi - $cntLuarRadius);
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-100 pb-4">
            <div>
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <i class="fas fa-chart-pie text-indigo-600"></i>
                    Diagram Analisis Variabel Kehadiran
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    Visualisasi diagram terpisah untuk setiap variabel indikator absensi karyawan — <strong>{{ $filterDate->translatedFormat('d F Y') }}</strong>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnTabPisah" onclick="switchDiagramTab('pisah')"
                        class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 text-white shadow-sm transition-all flex items-center gap-1.5">
                    <i class="fas fa-th-large"></i>
                    <span>Diagram Per Variabel</span>
                </button>
                <button type="button" id="btnTabRingkasan" onclick="switchDiagramTab('ringkasan')"
                        class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all flex items-center gap-1.5">
                    <i class="fas fa-chart-bar"></i>
                    <span>Ringkasan Komparasi</span>
                </button>
            </div>
        </div>

        <!-- TAB 1: Grid Diagram Terpisah Per Variabel (6 Diagram) -->
        <div id="container-tab-pisah" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            {{-- 1. Variabel: Hadir Normal --}}
            <div class="diagram-stat-card bg-white rounded-xl p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between"
                 data-color="green">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center text-green-600 flex-shrink-0">
                                <i class="fas fa-check-circle text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">Hadir Normal</h4>
                                <p class="text-[11px] text-gray-500">Tepat waktu tanpa terlambat</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-200">
                            {{ number_format($cntHadirNormal) }} Org
                        </span>
                    </div>

                    {{-- Diagram Donut Terpisah --}}
                    <div class="py-2">
                        <div class="relative w-36 h-36 mx-auto flex items-center justify-center">
                            <canvas id="chartVarHadirNormal"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-2xl font-extrabold text-green-600">{{ $pctHadirNormal }}%</span>
                                <span class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">dari aktif</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                            Hadir Tepat Waktu
                        </span>
                        <span class="font-bold text-gray-800">{{ number_format($cntHadirNormal) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                            Karyawan Lainnya
                        </span>
                        <span class="font-bold text-gray-800">{{ number_format(max(0, $totalKaryawan - $cntHadirNormal)) }}</span>
                    </div>
                    <div class="mt-2 text-[11px] text-gray-500 bg-gray-50 rounded-md px-2.5 py-1.5 flex items-center justify-between">
                        <span>Basis: {{ number_format($totalKaryawan) }} Karyawan Aktif</span>
                        <span class="text-green-600 font-semibold">{{ $totalKaryawanMasuk > 0 ? round(($cntHadirNormal / $totalKaryawanMasuk) * 100, 1) : 0 }}% hadir</span>
                    </div>
                </div>
            </div>

            {{-- 2. Variabel: Belum Absen Masuk --}}
            <div class="diagram-stat-card bg-white rounded-xl p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group hover:border-red-300 cursor-pointer"
                 id="card-diagram-belum-masuk"
                 data-color="red"
                 onclick="showDetailTable('belum-masuk')"
                 title="Klik untuk melihat detail karyawan belum absen masuk">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center text-red-600 flex-shrink-0 group-hover:scale-105 transition-transform">
                                <i class="fas fa-user-times text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm group-hover:text-red-600 transition-colors">Belum Absen Masuk</h4>
                                <p class="text-[11px] text-gray-500">Belum tapping masuk hari ini</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-red-50 text-red-700 border border-red-200">
                            {{ number_format($cntBelumMasuk) }} Org
                        </span>
                    </div>

                    {{-- Diagram Donut Terpisah --}}
                    <div class="py-2">
                        <div class="relative w-36 h-36 mx-auto flex items-center justify-center">
                            <canvas id="chartVarBelumMasuk"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-2xl font-extrabold text-red-600">{{ $pctBelumMasuk }}%</span>
                                <span class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">dari aktif</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            Belum Absen Masuk
                        </span>
                        <span class="font-bold text-gray-800">{{ number_format($cntBelumMasuk) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                            Sudah Absen Masuk
                        </span>
                        <span class="font-bold text-gray-800">{{ number_format($totalKaryawanMasuk) }}</span>
                    </div>
                    <div class="mt-2 text-[11px] text-red-600 bg-red-50/70 rounded-md px-2.5 py-1.5 flex items-center justify-between font-medium group-hover:bg-red-100/70 transition-colors">
                        <span><i class="fas fa-hand-pointer mr-1"></i> Klik untuk lihat daftar</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- 3. Variabel: Belum Absen Pulang --}}
            <div class="diagram-stat-card bg-white rounded-xl p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group hover:border-yellow-300 cursor-pointer"
                 id="card-diagram-belum-pulang"
                 data-color="yellow"
                 onclick="showDetailTable('belum-pulang')"
                 title="Klik untuk melihat detail karyawan belum absen pulang">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-lg bg-yellow-100 flex items-center justify-center text-yellow-600 flex-shrink-0 group-hover:scale-105 transition-transform">
                                <i class="fas fa-running text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm group-hover:text-yellow-600 transition-colors">Belum Absen Pulang</h4>
                                <p class="text-[11px] text-gray-500">Sudah masuk, belum tapping pulang</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">
                            {{ number_format($cntBelumPulang) }} Org
                        </span>
                    </div>

                    {{-- Diagram Donut Terpisah --}}
                    <div class="py-2">
                        <div class="relative w-36 h-36 mx-auto flex items-center justify-center">
                            <canvas id="chartVarBelumPulang"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-2xl font-extrabold text-yellow-600">{{ $pctBelumPulang }}%</span>
                                <span class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">dari hadir</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                            Belum Absen Pulang
                        </span>
                        <span class="font-bold text-gray-800">{{ number_format($cntBelumPulang) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                            Sudah Absen Pulang
                        </span>
                        <span class="font-bold text-gray-800">{{ number_format($cntSudahPulang) }}</span>
                    </div>
                    <div class="mt-2 text-[11px] text-yellow-700 bg-yellow-50/70 rounded-md px-2.5 py-1.5 flex items-center justify-between font-medium group-hover:bg-yellow-100/70 transition-colors">
                        <span><i class="fas fa-hand-pointer mr-1"></i> Klik untuk lihat daftar</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- 4. Variabel: Absen Terlambat --}}
            <div class="diagram-stat-card bg-white rounded-xl p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group hover:border-orange-300 cursor-pointer"
                 id="card-diagram-terlambat"
                 data-color="orange"
                 onclick="showDetailTable('terlambat')"
                 title="Klik untuk melihat detail karyawan terlambat">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 flex-shrink-0 group-hover:scale-105 transition-transform">
                                <i class="fas fa-clock text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm group-hover:text-orange-600 transition-colors">Absen Terlambat</h4>
                                <p class="text-[11px] text-gray-500">Masuk > {{ sprintf('%02d:05', $jamBatas) }} WIB</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-200">
                            {{ number_format($cntTerlambat) }} Org
                        </span>
                    </div>

                    {{-- Diagram Donut Terpisah --}}
                    <div class="py-2">
                        <div class="relative w-36 h-36 mx-auto flex items-center justify-center">
                            <canvas id="chartVarTerlambat"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-2xl font-extrabold text-orange-600">{{ $pctTerlambat }}%</span>
                                <span class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">dari hadir</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                            Absen Terlambat
                        </span>
                        <span class="font-bold text-gray-800">{{ number_format($cntTerlambat) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                            Tepat Waktu
                        </span>
                        <span class="font-bold text-gray-800">{{ number_format($cntTepatWaktu) }}</span>
                    </div>
                    <div class="mt-2 text-[11px] text-orange-600 bg-orange-50/70 rounded-md px-2.5 py-1.5 flex items-center justify-between font-medium group-hover:bg-orange-100/70 transition-colors">
                        <span><i class="fas fa-hand-pointer mr-1"></i> Klik untuk lihat daftar</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- 5. Variabel: Cuti & Izin Berjalan --}}
            <div class="diagram-stat-card bg-white rounded-xl p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between"
                 data-color="purple">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 flex-shrink-0">
                                <i class="fas fa-calendar-alt text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">Cuti & Izin Berjalan</h4>
                                <p class="text-[11px] text-gray-500">Status cuti/izin approved</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                            {{ number_format($cntCuti) }} Org
                        </span>
                    </div>

                    {{-- Diagram Donut Terpisah --}}
                    <div class="py-2">
                        <div class="relative w-36 h-36 mx-auto flex items-center justify-center">
                            <canvas id="chartVarCuti"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-2xl font-extrabold text-purple-600">{{ $pctCuti }}%</span>
                                <span class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">dari aktif</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                            Sedang Cuti / Izin
                        </span>
                        <span class="font-bold text-gray-800">{{ number_format($cntCuti) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                            Aktif Bekerja
                        </span>
                        <span class="font-bold text-gray-800">{{ number_format(max(0, $totalKaryawan - $cntCuti)) }}</span>
                    </div>
                    <div class="mt-2 text-[11px] text-purple-600 bg-purple-50/70 rounded-md px-2.5 py-1.5 flex items-center justify-between font-medium">
                        <span>Basis: {{ number_format($totalKaryawan) }} Karyawan Aktif</span>
                        <span>Approved</span>
                    </div>
                </div>
            </div>

            {{-- 6. Variabel: Absen Luar Radius --}}
            <div class="diagram-stat-card bg-white rounded-xl p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group hover:border-rose-300 cursor-pointer"
                 id="card-diagram-luar-radius"
                 data-color="rose"
                 onclick="showDetailTable('luar-radius')"
                 title="Klik untuk melihat detail presensi luar radius">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-lg bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0 group-hover:scale-105 transition-transform">
                                <i class="fas fa-map-marker-alt text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm group-hover:text-rose-600 transition-colors">Absen Luar Radius</h4>
                                <p class="text-[11px] text-gray-500">Di luar geofence kantor</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                            {{ number_format($cntLuarRadius) }} Presensi
                        </span>
                    </div>

                    {{-- Diagram Donut Terpisah --}}
                    <div class="py-2">
                        <div class="relative w-36 h-36 mx-auto flex items-center justify-center">
                            <canvas id="chartVarLuarRadius"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-2xl font-extrabold text-rose-600">{{ $pctLuarRadius }}%</span>
                                <span class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">presensi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            Di Luar Radius
                        </span>
                        <span class="font-bold text-gray-800">{{ number_format($cntLuarRadius) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                            Dalam Radius
                        </span>
                        <span class="font-bold text-gray-800">{{ number_format($cntDalamRadius) }}</span>
                    </div>
                    <div class="mt-2 text-[11px] text-rose-600 bg-rose-50/70 rounded-md px-2.5 py-1.5 flex items-center justify-between font-medium group-hover:bg-rose-100/70 transition-colors">
                        <span><i class="fas fa-hand-pointer mr-1"></i> Klik untuk lihat detail</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                </div>
            </div>

        </div>{{-- end TAB 1 --}}

        <!-- TAB 2: Ringkasan Komparasi Semua Variabel -->
        <div id="container-tab-ringkasan" class="hidden space-y-6">
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                <h4 class="font-bold text-gray-800 text-sm mb-1 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-indigo-500"></i>
                    Perbandingan Volume Semua Variabel
                </h4>
                <p class="text-xs text-gray-500 mb-4">Grafik komparasi kuantitas untuk masing-masing variabel kehadiran hari ini</p>
                <div class="relative w-full" style="height: 280px;">
                    <canvas id="comparisonBarChart"></canvas>
                </div>
            </div>

            <!-- Ringkasan Cepat Persentase -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="p-3 bg-green-50/70 rounded-lg border border-green-200/70">
                    <p class="text-xs text-gray-500 font-medium">Hadir Normal</p>
                    <p class="text-lg font-bold text-green-700 mt-0.5">{{ number_format($cntHadirNormal) }}</p>
                    <p class="text-[11px] text-green-600 font-semibold">{{ $pctHadirNormal }}% dari aktif</p>
                </div>
                <div class="p-3 bg-red-50/70 rounded-lg border border-red-200/70">
                    <p class="text-xs text-gray-500 font-medium">Belum Masuk</p>
                    <p class="text-lg font-bold text-red-700 mt-0.5">{{ number_format($cntBelumMasuk) }}</p>
                    <p class="text-[11px] text-red-600 font-semibold">{{ $pctBelumMasuk }}% dari aktif</p>
                </div>
                <div class="p-3 bg-yellow-50/70 rounded-lg border border-yellow-200/70">
                    <p class="text-xs text-gray-500 font-medium">Belum Pulang</p>
                    <p class="text-lg font-bold text-yellow-700 mt-0.5">{{ number_format($cntBelumPulang) }}</p>
                    <p class="text-[11px] text-yellow-600 font-semibold">{{ $pctBelumPulang }}% dari hadir</p>
                </div>
                <div class="p-3 bg-orange-50/70 rounded-lg border border-orange-200/70">
                    <p class="text-xs text-gray-500 font-medium">Terlambat</p>
                    <p class="text-lg font-bold text-orange-700 mt-0.5">{{ number_format($cntTerlambat) }}</p>
                    <p class="text-[11px] text-orange-600 font-semibold">{{ $pctTerlambat }}% dari hadir</p>
                </div>
                <div class="p-3 bg-purple-50/70 rounded-lg border border-purple-200/70">
                    <p class="text-xs text-gray-500 font-medium">Cuti & Izin</p>
                    <p class="text-lg font-bold text-purple-700 mt-0.5">{{ number_format($cntCuti) }}</p>
                    <p class="text-[11px] text-purple-600 font-semibold">{{ $pctCuti }}% dari aktif</p>
                </div>
                <div class="p-3 bg-rose-50/70 rounded-lg border border-rose-200/70">
                    <p class="text-xs text-gray-500 font-medium">Luar Radius</p>
                    <p class="text-lg font-bold text-rose-700 mt-0.5">{{ number_format($cntLuarRadius) }}</p>
                    <p class="text-[11px] text-rose-600 font-semibold">{{ $pctLuarRadius }}% presensi</p>
                </div>
            </div>
        </div>{{-- end TAB 2 --}}

    </div>{{-- end Section Diagram --}}

    <!-- Detail Panel (muncul saat kartu diklik) -->
    <div id="detail-panel" class="hidden">
        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">

            <!-- Panel Header -->
            <div id="detail-panel-header" class="px-5 py-4 flex items-center justify-between border-b border-gray-100 transition-colors duration-200">
                <div class="flex items-center gap-3">
                    <div id="detail-icon-wrap" class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                        <i id="detail-icon-el" class="fas text-xl"></i>
                    </div>
                    <div>
                        <h3 id="detail-title" class="font-semibold text-gray-800 text-base leading-tight"></h3>
                        <p id="detail-subtitle" class="text-xs text-gray-500 mt-0.5"></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span id="detail-badge" class="text-xs font-bold px-3 py-1.5 rounded-full whitespace-nowrap"></span>
                    <button onclick="closeDetailPanel()"
                            class="text-gray-400 hover:text-gray-700 transition-colors p-1.5 rounded-full hover:bg-gray-100 ml-1"
                            title="Tutup panel">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Filter Group Info Bar -->
            <div id="detail-filter-bar" class="hidden px-5 py-2 bg-indigo-50 border-b border-indigo-100 flex items-center gap-2 text-xs text-indigo-700">
                <i class="fas fa-filter"></i>
                <span>Difilter berdasarkan group: <strong id="active-group-label"></strong></span>
                <button onclick="resetGroupFilter()" class="ml-auto text-indigo-500 hover:text-indigo-800 underline font-medium">Reset Filter</button>
            </div>

            <!-- Tables Container -->
            <div style="max-height: 520px; overflow-y: auto;">

                <!-- Table: Belum Absen Masuk -->
                <div id="table-belum-masuk" class="hidden">
                    @if($karyawanBelumAbsen->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NIK</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Divisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($karyawanBelumAbsen as $i => $k)
                            <tr class="hover:bg-red-50/50 transition-colors" data-grup="{{ is_array($k->grup) ? implode(',', $k->grup) : ($k->grup ?? '') }}">
                                <td class="px-5 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                                <td class="px-5 py-3 text-gray-500 font-mono text-xs">{{ $k->nik }}</td>
                                <td class="px-5 py-3 font-medium text-gray-800">
                                    <a href="{{ route('master.karyawan.show', $k->id) }}" target="_blank"
                                       class="hover:text-red-600 transition-colors inline-flex items-center gap-1 group">
                                        {{ $k->nama_lengkap }}
                                        <i class="fas fa-external-link-alt text-xs opacity-0 group-hover:opacity-50 transition-opacity"></i>
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-xs uppercase">{{ $k->divisi ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="py-16 text-center text-gray-500 flex flex-col items-center">
                        <i class="fas fa-check-circle text-5xl text-green-300 mb-3"></i>
                        <p class="font-medium">Semua karyawan telah melakukan absensi masuk.</p>
                    </div>
                    @endif
                </div>{{-- end table-belum-masuk --}}

                <!-- Table: Belum Absen Pulang -->
                <div id="table-belum-pulang" class="hidden">
                    @if($karyawanBelumAbsenPulang->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NIK</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Divisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($karyawanBelumAbsenPulang as $i => $k)
                            <tr class="hover:bg-yellow-50/50 transition-colors" data-grup="{{ is_array($k->grup) ? implode(',', $k->grup) : ($k->grup ?? '') }}">
                                <td class="px-5 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                                <td class="px-5 py-3 text-gray-500 font-mono text-xs">{{ $k->nik }}</td>
                                <td class="px-5 py-3 font-medium text-gray-800">
                                    <a href="{{ route('master.karyawan.show', $k->id) }}" target="_blank"
                                       class="hover:text-yellow-600 transition-colors inline-flex items-center gap-1 group">
                                        {{ $k->nama_lengkap }}
                                        <i class="fas fa-external-link-alt text-xs opacity-0 group-hover:opacity-50 transition-opacity"></i>
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-xs uppercase">{{ $k->divisi ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="py-16 text-center text-gray-500 flex flex-col items-center">
                        <i class="fas fa-check-circle text-5xl text-green-300 mb-3"></i>
                        <p class="font-medium">Semua karyawan (yang absen masuk) telah absen pulang.</p>
                    </div>
                    @endif
                </div>{{-- end table-belum-pulang --}}

                <!-- Table: Terlambat -->
                <div id="table-terlambat" class="hidden">
                    @if($karyawanTerlambat->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Divisi</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Absen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($karyawanTerlambat as $i => $absen)
                            <tr class="hover:bg-orange-50/50 transition-colors" data-grup="{{ $absen->karyawan && is_array($absen->karyawan->grup) ? implode(',', $absen->karyawan->grup) : ($absen->karyawan->grup ?? '') }}">
                                <td class="px-5 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                                <td class="px-5 py-3 font-medium text-gray-800">
                                    @if($absen->karyawan)
                                        <a href="{{ route('master.karyawan.show', $absen->karyawan->id) }}" target="_blank"
                                           class="hover:text-orange-600 transition-colors inline-flex items-center gap-1 group">
                                            {{ $absen->karyawan->nama_lengkap }}
                                            <i class="fas fa-external-link-alt text-xs opacity-0 group-hover:opacity-50 transition-opacity"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-xs uppercase">{{ $absen->karyawan->divisi ?? '-' }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center gap-1 font-semibold text-orange-700 bg-orange-50 border border-orange-100 px-2.5 py-1 rounded-full text-xs">
                                        <i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($absen->waktu)->format('H:i') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="py-16 text-center text-gray-500 flex flex-col items-center">
                        <i class="fas fa-check-circle text-5xl text-green-300 mb-3"></i>
                        <p class="font-medium">Tidak ada karyawan yang terlambat.</p>
                    </div>
                    @endif
                </div>{{-- end table-terlambat --}}

                <!-- Table: Absen Luar Radius -->
                <div id="table-luar-radius" class="hidden">
                    @if($absensiLuarRadius->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Detail Lokasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($absensiLuarRadius as $i => $absen)
                            <tr class="hover:bg-red-50/50 transition-colors" data-grup="{{ $absen->karyawan && is_array($absen->karyawan->grup) ? implode(',', $absen->karyawan->grup) : ($absen->karyawan->grup ?? '') }}">
                                <td class="px-5 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                                <td class="px-5 py-3 font-medium text-gray-800">
                                    @if($absen->karyawan)
                                        <a href="{{ route('master.karyawan.show', $absen->karyawan->id) }}" target="_blank"
                                           class="hover:text-red-600 transition-colors inline-flex items-center gap-1 group">
                                            {{ $absen->karyawan->nama_lengkap }}
                                            <i class="fas fa-external-link-alt text-xs opacity-0 group-hover:opacity-50 transition-opacity"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center gap-1 font-semibold text-red-700 bg-red-50 border border-red-100 px-2.5 py-1 rounded-full text-xs">
                                        <i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($absen->waktu)->format('H:i') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-xs">{{ $absen->tipe }}</td>
                                <td class="px-5 py-3 text-gray-600 text-xs">{{ $absen->detail_lokasi }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="py-16 text-center text-gray-500 flex flex-col items-center">
                        <i class="fas fa-check-circle text-5xl text-green-300 mb-3"></i>
                        <p class="font-medium">Tidak ada karyawan yang absen di luar radius.</p>
                    </div>
                    @endif
                </div>{{-- end table-luar-radius --}}

            </div>{{-- end Tables Container --}}

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
                            <input type="date" name="start_date" id="start_date" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
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
    $karyawan = $authUser->karyawan;
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
    if ($authUser->hasRole('super-admin') || $authUser->username === 'kiky') {
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ── Diagram Analisis Variabel Kehadiran (Dipisah per Variabel) ─────────────
    document.addEventListener('DOMContentLoaded', function() {
        // Helper untuk membuat mini donut chart per variabel
        function createMiniDonut(canvasId, value, total, primaryColor, labelPrimary, labelSecondary) {
            var ctx = document.getElementById(canvasId);
            if (!ctx) return;

            var remainder = Math.max(0, total - value);
            // Jika kedua nilai 0 (misal presensi luar radius 0 dan total presensi 0)
            var dataValues = (value === 0 && remainder === 0) ? [0, 1] : [value, remainder];
            var bgColors   = (value === 0 && remainder === 0) ? ['#e2e8f0', '#f1f5f9'] : [primaryColor, '#e2e8f0'];

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [labelPrimary, labelSecondary],
                    datasets: [{
                        data: dataValues,
                        backgroundColor: bgColors,
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(c) {
                                    if (value === 0 && remainder === 0) return ' Tidak ada data';
                                    var val = c.raw;
                                    var pct = total > 0 ? ((val / total) * 100).toFixed(1) : '0.0';
                                    return ' ' + c.label + ': ' + val + ' (' + pct + '%)';
                                }
                            },
                            backgroundColor: 'rgba(17,24,39,0.85)',
                            padding: 8,
                            cornerRadius: 6,
                            titleFont: { size: 11 },
                            bodyFont: { size: 11 },
                        }
                    },
                    animation: { animateRotate: true, duration: 800 }
                }
            });
        }

        // 1. Hadir Normal
        createMiniDonut('chartVarHadirNormal', {{ $cntHadirNormal }}, {{ $totalKaryawan }}, '#22c55e', 'Hadir Tepat Waktu', 'Lainnya');
        // 2. Belum Absen Masuk
        createMiniDonut('chartVarBelumMasuk', {{ $cntBelumMasuk }}, {{ $totalKaryawan }}, '#ef4444', 'Belum Absen Masuk', 'Sudah Masuk');
        // 3. Belum Absen Pulang
        createMiniDonut('chartVarBelumPulang', {{ $cntBelumPulang }}, {{ max(1, $totalKaryawanMasuk) }}, '#eab308', 'Belum Absen Pulang', 'Sudah Absen Pulang');
        // 4. Absen Terlambat
        createMiniDonut('chartVarTerlambat', {{ $cntTerlambat }}, {{ max(1, $totalKaryawanMasuk) }}, '#ea580c', 'Absen Terlambat', 'Tepat Waktu');
        // 5. Cuti & Izin Berjalan
        createMiniDonut('chartVarCuti', {{ $cntCuti }}, {{ $totalKaryawan }}, '#9333ea', 'Cuti & Izin', 'Aktif Bekerja');
        // 6. Absen Luar Radius
        createMiniDonut('chartVarLuarRadius', {{ $cntLuarRadius }}, {{ max(1, $totalPresensi) }}, '#e11d48', 'Luar Radius', 'Dalam Radius');

        // Chart Bar Komparasi (TAB 2)
        var barCtx = document.getElementById('comparisonBarChart');
        if (barCtx) {
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: [
                        'Hadir Normal',
                        'Belum Masuk',
                        'Belum Pulang',
                        'Terlambat',
                        'Cuti & Izin',
                        'Luar Radius'
                    ],
                    datasets: [{
                        label: 'Jumlah Karyawan / Presensi',
                        data: [
                            {{ $cntHadirNormal }},
                            {{ $cntBelumMasuk }},
                            {{ $cntBelumPulang }},
                            {{ $cntTerlambat }},
                            {{ $cntCuti }},
                            {{ $cntLuarRadius }},
                        ],
                        backgroundColor: [
                            '#22c55e',
                            '#ef4444',
                            '#eab308',
                            '#ea580c',
                            '#9333ea',
                            '#e11d48',
                        ],
                        borderRadius: 6,
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(17,24,39,0.85)',
                            padding: 8,
                            cornerRadius: 6,
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 11 } }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { font: { size: 12, weight: '500' } }
                        }
                    }
                }
            });
        }
    });

    // Switch Tab Diagram
    function switchDiagramTab(tab) {
        var tabPisah = document.getElementById('container-tab-pisah');
        var tabRingkasan = document.getElementById('container-tab-ringkasan');
        var btnPisah = document.getElementById('btnTabPisah');
        var btnRingkasan = document.getElementById('btnTabRingkasan');

        if (tab === 'pisah') {
            tabPisah.classList.remove('hidden');
            tabRingkasan.classList.add('hidden');
            btnPisah.className = 'px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 text-white shadow-sm transition-all flex items-center gap-1.5';
            btnRingkasan.className = 'px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all flex items-center gap-1.5';
        } else {
            tabPisah.classList.add('hidden');
            tabRingkasan.classList.remove('hidden');
            btnPisah.className = 'px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all flex items-center gap-1.5';
            btnRingkasan.className = 'px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 text-white shadow-sm transition-all flex items-center gap-1.5';
        }
    }

    var _activeTable = null;

    // Konfigurasi tiap kategori kartu
    var DETAIL_CONFIG = {
        'belum-masuk': {
            title: 'Karyawan Belum Absen Masuk',
            subtitle: 'Daftar karyawan yang belum melakukan absensi masuk hari ini',
            icon: 'fa-user-times',
            iconBg: '#fee2e2',
            iconColor: '#dc2626',
            headerBg: '#fff5f5',
            headerBorder: '#fecaca',
            badgeBg: '#fee2e2',
            badgeColor: '#991b1b',
            count: {{ $karyawanBelumAbsen->count() }},
        },
        'belum-pulang': {
            title: 'Karyawan Belum Absen Pulang',
            subtitle: 'Daftar karyawan yang belum melakukan absensi pulang hari ini',
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

        // Nonaktifkan semua kartu
        document.querySelectorAll('.stat-card, .diagram-stat-card').forEach(function(c) {
            c.classList.remove('active');
        });

        // Sembunyikan semua sub-tabel
        ['belum-masuk', 'belum-pulang', 'terlambat', 'luar-radius'].forEach(function(t) {
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

        // Aktifkan kartu yang diklik (baik stat card maupun diagram card)
        var card = document.getElementById('card-' + type);
        if (card) card.classList.add('active');
        var cardDiag = document.getElementById('card-diagram-' + type);
        if (cardDiag) cardDiag.classList.add('active');

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
        if (activeTableEl) activeTableEl.classList.remove('hidden');

        // Tampilkan panel
        panel.classList.remove('hidden');

        // Re-apply group filter jika aktif
        var groupSelect = document.getElementById('global_group_filter');
        if (groupSelect && groupSelect.value) {
            applyGroupFilter(groupSelect.value);
        }

        // Scroll ke panel dengan smooth
        setTimeout(function() {
            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 80);
    }

    /** Tutup panel detail dan reset semua state */
    function closeDetailPanel() {
        var panel = document.getElementById('detail-panel');
        panel.classList.add('hidden');
        document.querySelectorAll('.stat-card, .diagram-stat-card').forEach(function(c) {
            c.classList.remove('active');
        });
        _activeTable = null;
    }

    function openExportModal() {
        document.getElementById('exportModal').classList.remove('hidden');
    }
    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    /**
     * Filter semua tabel berdasarkan grup karyawan.
     * Setiap <tr data-grup="..."> berisi daftar grup penuh (misal "LEMBUR:KANTOR JAKARTA,CUTI").
     * Filter mencocokkan berdasarkan prefix SEBELUM ':' saja.
     * @param {string} group - Nama group (tanpa sub-group) yang dipilih, atau string kosong untuk semua.
     */
    function applyGroupFilter(group) {
        var resetBtn = document.getElementById('reset_group_btn');
        var filterBar = document.getElementById('detail-filter-bar');
        var rows = document.querySelectorAll('tr[data-grup]');

        rows.forEach(function(row) {
            if (!group) {
                row.style.display = '';
            } else {
                var grupAttr = row.getAttribute('data-grup') || '';
                // Pecah berdasarkan koma, lalu ambil prefix sebelum ':' dari setiap item
                var grups = grupAttr.split(',').map(function(g) {
                    return g.trim().split(':')[0].trim();
                }).filter(function(g) { return g; });
                row.style.display = grups.includes(group) ? '' : 'none';
            }
        });

        // Tampilkan/sembunyikan tombol reset
        if (resetBtn) {
            resetBtn.classList.toggle('hidden', !group);
        }

        // Update info bar di panel detail
        if (filterBar) {
            if (group && _activeTable) {
                filterBar.classList.remove('hidden');
                var label = document.getElementById('active-group-label');
                if (label) label.textContent = group;
            } else {
                filterBar.classList.add('hidden');
            }
        }

        // Update badge jumlah di panel
        updatePanelBadge();
    }

    function resetGroupFilter() {
        var select = document.getElementById('global_group_filter');
        if (select) select.value = '';
        applyGroupFilter('');
    }

    /**
     * Perbarui badge jumlah orang di panel header berdasarkan baris yang tampil.
     */
    function updatePanelBadge() {
        if (!_activeTable) return;
        var activeTableEl = document.getElementById('table-' + _activeTable);
        if (!activeTableEl) return;
        var allRows     = activeTableEl.querySelectorAll('tbody tr[data-grup]');
        var visibleRows = activeTableEl.querySelectorAll('tbody tr[data-grup]:not([style*="display: none"])');
        var badge = document.getElementById('detail-badge');
        if (badge) {
            if (visibleRows.length === allRows.length) {
                badge.textContent = DETAIL_CONFIG[_activeTable].count + ' Orang';
            } else {
                badge.textContent = visibleRows.length + ' Orang';
            }
        }
    }
</script>
@endsection
