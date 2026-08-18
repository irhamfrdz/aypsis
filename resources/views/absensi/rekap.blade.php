@extends('layouts.app')

@section('title', 'Rekap Absensi Bulanan')
@section('page_title', 'Rekap Absensi Bulanan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Rekap Absensi Bulanan</h1>
                    <p class="mt-1 text-sm text-gray-600">Ringkasan kehadiran karyawan berdasarkan scan fingerprint per bulan</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="openIzinModal()" class="inline-flex items-center justify-center px-4 py-2 border border-transparent bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Izin Karyawan
                    </button>
                    <a href="{{ route('absensi.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Lihat Log Scan
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form action="{{ route('absensi.rekap') }}" method="GET" class="space-y-4" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search Karyawan -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold text-gray-700 mb-1">Cari Karyawan / NIK</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Nama atau NIK..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                    </div>


                    <!-- Group -->
                    <div class="md:col-span-1">
                        <label for="grup" class="block text-xs font-semibold text-gray-700 mb-1">Group</label>
                        <select name="grup" id="grup"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs"
                                onchange="handleGrupFilterChange()">
                            <option value="">Semua Group</option>
                            @foreach($grupsList as $g)
                                <option value="{{ $g }}" {{ request('grup') == $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sub Group -->
                    <div class="md:col-span-1">
                        <label for="sub_grup" class="block text-xs font-semibold text-gray-700 mb-1">Sub Group</label>
                        <select name="sub_grup" id="sub_grup"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            <option value="">Semua Sub Group</option>
                        </select>
                    </div>

                    <!-- Group BPJS -->
                    <div class="md:col-span-1">
                        <label for="grup_bpjs" class="block text-xs font-semibold text-gray-700 mb-1">Group BPJS</label>
                        <select name="grup_bpjs" id="grup_bpjs"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs"
                                onchange="handleGrupBpjsFilterChange()">
                            <option value="">Semua Group BPJS</option>
                            @foreach($grupsBpjsList as $g)
                                <option value="{{ $g }}" {{ request('grup_bpjs') == $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sub Group BPJS -->
                    <div class="md:col-span-1">
                        <label for="sub_grup_bpjs" class="block text-xs font-semibold text-gray-700 mb-1">Sub Group BPJS</label>
                        <select name="sub_grup_bpjs" id="sub_grup_bpjs"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            <option value="">Semua Sub Group BPJS</option>
                        </select>
                    </div>

                    <!-- Penempatan -->
                    <div class="md:col-span-1">
                        <label for="penempatan" class="block text-xs font-semibold text-gray-700 mb-1">Penempatan</label>
                        <select name="penempatan" id="penempatan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            <option value="">Semua Penempatan</option>
                            @foreach($penempatans as $penempatan)
                                <option value="{{ $penempatan }}" {{ request('penempatan') == $penempatan ? 'selected' : '' }}>{{ strtoupper($penempatan) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pekerjaan -->
                    <div class="md:col-span-1">
                        <label for="pekerjaan" class="block text-xs font-semibold text-gray-700 mb-1">Pekerjaan</label>
                        <select name="pekerjaan" id="pekerjaan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            <option value="">Semua Pekerjaan</option>
                            @foreach($pekerjaans as $pekerjaan)
                                <option value="{{ $pekerjaan }}" {{ request('pekerjaan') == $pekerjaan ? 'selected' : '' }}>{{ strtoupper($pekerjaan) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tingkat Kehadiran -->
                    <div class="md:col-span-1">
                        <label for="kehadiran" class="block text-xs font-semibold text-gray-700 mb-1">Filter Lupa Absen</label>
                        <select name="kehadiran" id="kehadiran"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            <option value="">Semua Karyawan</option>
                            <option value="tidak_absen_masuk" {{ request('kehadiran') == 'tidak_absen_masuk' ? 'selected' : '' }}>Tidak Absen Masuk</option>
                            <option value="tidak_absen_pulang" {{ request('kehadiran') == 'tidak_absen_pulang' ? 'selected' : '' }}>Tidak Absen Pulang</option>
                            <option value="tidak_absen_istirahat" {{ request('kehadiran') == 'tidak_absen_istirahat' ? 'selected' : '' }}>Tidak Absen Istirahat</option>
                        </select>
                    </div>

                    <!-- Dari Tanggal -->
                    <div class="md:col-span-1">
                        <label for="start_date" class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" required
                               value="{{ $startDateStr }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                    </div>

                    <!-- Sampai Tanggal -->
                    <div class="md:col-span-1">
                        <label for="end_date" class="block text-xs font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" required
                               value="{{ $endDateStr }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                    </div>

                    <!-- Action Buttons -->
                    <div class="md:col-span-5 flex items-end gap-2 justify-end mt-2">
                        @if(request()->anyFilled(['search', 'penempatan', 'grup', 'sub_grup', 'grup_bpjs', 'sub_grup_bpjs', 'kehadiran']))
                            <a href="{{ route('absensi.rekap') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-lg focus:outline-none transition-colors duration-200 h-[38px] shadow-sm">
                                Reset
                            </a>
                        @endif
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg focus:outline-none transition-colors duration-200 h-[38px] shadow-sm">
                            Filter Rekap
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">
                        Periode: {{ Carbon\Carbon::parse($startDateStr)->translatedFormat('d M Y') }} - {{ Carbon\Carbon::parse($endDateStr)->translatedFormat('d M Y') }}
                        <span class="ml-2 text-xs font-normal text-gray-500">({{ $normalWorkdays }} Hari Kerja)</span>
                    </h3>
                </div>
                <div>
                    <button type="submit" form="filterForm" name="export" value="pdf" class="inline-flex items-center justify-center px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 focus:outline-none transition-colors duration-200 shadow-sm cursor-pointer mr-2">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Ekspor PDF
                    </button>
                    <button type="button" onclick="document.getElementById('exportModal').classList.remove('hidden')" class="inline-flex items-center justify-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 focus:outline-none transition-colors duration-200 shadow-sm cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Ekspor Excel
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 font-semibold text-gray-500 uppercase tracking-wider text-[9px]">
                        <tr>
                            <th class="px-6 py-3 text-center w-10">
                                <input type="checkbox" id="selectAllKaryawan" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </th>
                            <th class="px-6 py-3 text-left">No</th>
                            <th class="px-6 py-3 text-left">NIK</th>
                            <th class="px-6 py-3 text-left">Nama Lengkap</th>
                            <th class="px-6 py-3 text-left">Penempatan</th>
                            <th class="px-6 py-3 text-center">Hadir</th>
                            <th class="px-6 py-3 text-center">Terlambat</th>
                            <th class="px-6 py-3 text-center">Pulang Cpt</th>
                            <th class="px-6 py-3 text-center">Lembur</th>
                            <th class="px-6 py-3 text-center">Tdk Masuk</th>
                            <th class="px-6 py-3 text-center">Tdk Pulang</th>
                            <th class="px-6 py-3 text-center">Tdk Istirahat</th>
                            <th class="px-6 py-3 text-center">Sakit</th>
                            <th class="px-6 py-3 text-center">Izin</th>
                            <th class="px-6 py-3 text-center">Cuti</th>
                            <th class="px-6 py-3 text-center">Alpha</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-[10px] text-gray-900">
                        @forelse($karyawans as $index => $karyawan)
                            @php
                                $stats = $rekapData[$karyawan->id] ?? [
                                    'total_masuk' => 0, 'sakit' => 0, 'izin' => 0, 'cuti' => 0, 'alpha' => 0,
                                    'terlambat_kali' => 0, 'terlambat_menit' => 0,
                                    'pulang_cepat_kali' => 0, 'pulang_cepat_menit' => 0,
                                    'lembur_jam' => 0, 'lembur_kali' => 0,
                                    'tidak_absen_masuk_kali' => 0, 'tidak_absen_pulang_kali' => 0, 'tidak_absen_istirahat_kali' => 0
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="checkbox" name="selected_karyawan[]" value="{{ $karyawan->id }}" form="filterForm" class="karyawan-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ is_array(request('selected_karyawan')) && in_array($karyawan->id, request('selected_karyawan')) ? 'checked' : '' }}>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-500">
                                    {{ $karyawans->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold font-mono text-indigo-600">
                                    {{ $karyawan->nik }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    {{ $karyawan->nama_lengkap }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ $karyawan->penempatan ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button type="button" data-nama="{{ $karyawan->nama_lengkap }}" data-dates="{{ json_encode($stats['detail_hadir'] ?? []) }}" class="btn-detail-hadir inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 {{ $stats['total_masuk'] > 0 ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                        {{ $stats['total_masuk'] }} Hari
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-[10px] font-medium">
                                    @if($stats['terlambat_kali'] > 0)
                                        <button type="button" data-nama="{{ $karyawan->nama_lengkap }}" data-dates="{{ json_encode($stats['detail_terlambat'] ?? []) }}" class="btn-detail-terlambat inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 bg-red-100 text-red-800 hover:bg-red-200">
                                            {{ $stats['terlambat_kali'] }}x ({{ $stats['terlambat_menit'] }}m)
                                        </button>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-[10px] font-medium">
                                    @if($stats['pulang_cepat_kali'] > 0)
                                        <button type="button" data-nama="{{ $karyawan->nama_lengkap }}" data-dates="{{ json_encode($stats['detail_pulang_cepat'] ?? []) }}" class="btn-detail-pulang-cepat inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1 bg-amber-100 text-amber-800 hover:bg-amber-200">
                                            {{ $stats['pulang_cepat_kali'] }}x ({{ $stats['pulang_cepat_menit'] }}m)
                                        </button>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($stats['lembur_kali'] > 0)
                                        <button type="button" data-nama="{{ $karyawan->nama_lengkap }}" data-dates="{{ json_encode($stats['detail_lembur'] ?? []) }}" class="btn-detail-lembur inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 bg-indigo-100 text-indigo-800 hover:bg-indigo-200">
                                            {{ $stats['lembur_kali'] }}x {{ $stats['lembur_jam'] > 0 ? '(' . $stats['lembur_jam'] . ' Jam)' : '' }}
                                        </button>
                                    @else
                                        <span class="text-[10px] font-medium text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-[10px] font-medium">
                                    @if($stats['tidak_absen_masuk_kali'] > 0)
                                        <button type="button" data-nama="{{ $karyawan->nama_lengkap }}" data-dates="{{ json_encode($stats['detail_tidak_absen_masuk'] ?? []) }}" class="btn-detail-tdk-masuk inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 bg-red-100 text-red-800 hover:bg-red-200" title="Tidak Absen Masuk">
                                            {{ $stats['tidak_absen_masuk_kali'] }}x
                                        </button>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-[10px] font-medium">
                                    @if($stats['tidak_absen_pulang_kali'] > 0)
                                        <button type="button" data-nama="{{ $karyawan->nama_lengkap }}" data-dates="{{ json_encode($stats['detail_tidak_absen_pulang'] ?? []) }}" class="btn-detail-tdk-pulang inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-1 bg-orange-100 text-orange-800 hover:bg-orange-200" title="Tidak Absen Pulang">
                                            {{ $stats['tidak_absen_pulang_kali'] }}x
                                        </button>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-[10px] font-medium">
                                    @if($stats['tidak_absen_istirahat_kali'] > 0)
                                        <button type="button" data-nama="{{ $karyawan->nama_lengkap }}" data-dates="{{ json_encode($stats['detail_tidak_absen_istirahat'] ?? []) }}" class="btn-detail-tdk-istirahat inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-1 bg-yellow-100 text-yellow-800 hover:bg-yellow-200" title="Tidak Absen Istirahat">
                                            {{ $stats['tidak_absen_istirahat_kali'] }}x
                                        </button>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $stats['sakit'] > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $stats['sakit'] }} Hari
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $stats['izin'] > 0 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $stats['izin'] }} Hari
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ ($stats['cuti'] ?? 0) > 0 ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $stats['cuti'] ?? 0 }} Hari
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-[10px] font-medium">
                                    @if($stats['alpha'] > 0)
                                        <button type="button" data-nama="{{ $karyawan->nama_lengkap }}" data-dates="{{ json_encode($stats['detail_alpha'] ?? []) }}" class="btn-detail-alpha inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 bg-red-100 text-red-800 hover:bg-red-200" title="Alpha">
                                            {{ $stats['alpha'] }}x
                                        </button>
                                    @else
                                        <span class="text-[10px] font-medium text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada data karyawan</h3>
                                        <p class="text-[10px] text-gray-500">Tidak ada karyawan yang terdaftar dengan filter ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($karyawans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $karyawans->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

<!-- Modal Export Excel -->
<div id="exportModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="document.getElementById('exportModal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-green-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                        Ekspor Data Absensi
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 mb-4">
                            Pilih rentang tanggal untuk mengekspor data absensi ke format Excel.
                        </p>
                        
                        <form action="{{ route('absensi.rekap') }}" method="GET" id="modalExportForm">
                            <input type="hidden" name="export" value="1">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="pekerjaan" value="{{ request('pekerjaan') }}">
                            <input type="hidden" name="grup" value="{{ request('grup') }}">
                            <input type="hidden" name="sub_grup" value="{{ request('sub_grup') }}">
                            <input type="hidden" name="grup_bpjs" value="{{ request('grup_bpjs') }}">
                            <input type="hidden" name="sub_grup_bpjs" value="{{ request('sub_grup_bpjs') }}">
                            <input type="hidden" name="kehadiran" value="{{ request('kehadiran') }}">

                            <div class="space-y-4">

                                <div>
                                    <label for="export_tempat" class="block text-xs font-semibold text-gray-700 mb-1">Filter Tempat</label>
                                    <select name="tempat" id="export_tempat"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                                        <option value="">Semua Tempat</option>
                                        @foreach($penempatans as $penempatan)
                                            <option value="{{ $penempatan }}" {{ request('tempat') == $penempatan ? 'selected' : '' }}>{{ strtoupper($penempatan) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="start_date" class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Awal</label>
                                    <input type="date" name="start_date" id="start_date_export" required
                                           value="{{ $startDateStr }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                                </div>
                                <div>
                                    <label for="end_date" class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Akhir</label>
                                    <input type="date" name="end_date" id="end_date_export" required
                                           value="{{ $endDateStr }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button type="submit" form="modalExportForm" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Ekspor Sekarang
                </button>
                <button type="button" onclick="document.getElementById('exportModal').classList.add('hidden')" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Kehadiran -->
<div id="detailHadirModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailHadir()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="detailHadirTitle">
                            Detail Kehadiran
                        </h3>
                        <div class="mt-4 max-h-[60vh] overflow-y-auto pr-2" id="detailHadirContent">
                            <!-- Content will be injected here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeDetailHadir()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Lembur -->
<div id="detailLemburModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailLembur()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="detailLemburTitle">
                            Detail Lembur
                        </h3>
                        <div class="mt-4 max-h-[60vh] overflow-y-auto pr-2" id="detailLemburContent">
                            <!-- Content will be injected here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeDetailLembur()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Terlambat -->
<div id="detailTerlambatModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailTerlambat()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="detailTerlambatTitle">
                            Detail Terlambat
                        </h3>
                        <div class="mt-4 max-h-[60vh] overflow-y-auto pr-2" id="detailTerlambatContent">
                            <!-- Content will be injected here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeDetailTerlambat()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pulang Cepat -->
<div id="detailPulangCepatModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailPulangCepat()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="detailPulangCepatTitle">
                            Detail Pulang Cepat
                        </h3>
                        <div class="mt-4 max-h-[60vh] overflow-y-auto pr-2" id="detailPulangCepatContent">
                            <!-- Content will be injected here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeDetailPulangCepat()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Lupa Absen (Generic) -->
<div id="detailLupaAbsenModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailLupaAbsen()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="detailLupaAbsenTitle">
                            Detail Lupa Absen
                        </h3>
                        <div class="mt-4 max-h-[60vh] overflow-y-auto pr-2" id="detailLupaAbsenContent">
                            <!-- Content will be injected here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeDetailLupaAbsen()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Izin -->
<div id="izinModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeIzinModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Tambah Izin Karyawan</h3>
                        <p class="text-sm text-gray-500">Buat data izin atau cuti karyawan (Otomatis Approved)</p>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('absensi.izin.store') }}" method="POST" id="izinForm">
                @csrf
                <div class="px-4 py-5 sm:p-6 space-y-4">
                    <!-- Karyawan -->
                    <div>
                        <label for="karyawan_id_izin" class="block text-sm font-medium text-gray-700 mb-1">Karyawan <span class="text-red-500">*</span></label>
                        <select name="karyawan_id" id="karyawan_id_izin" required
                                class="select2 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-sm bg-white">
                            <option value="">Pilih Karyawan</option>
                            @foreach($allKaryawans as $kar)
                                <option value="{{ $kar->id }}">{{ $kar->nama_lengkap }} ({{ $kar->nik }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jenis Izin -->
                    <div>
                        <label for="jenis_izin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Izin <span class="text-red-500">*</span></label>
                        <select name="jenis_izin" id="jenis_izin" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-sm bg-white">
                            <option value="">Pilih Jenis</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Izin">Izin (Full 1 Hari)</option>
                            <option value="Datang_Terlambat">Datang Terlambat</option>
                            <option value="Pulang_Cepat">Pulang Cepat</option>
                        </select>
                    </div>

                    <!-- Tanggal -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_mulai_izin" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai_izin" required value="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label for="tanggal_selesai_izin" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai_izin" required value="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label for="alasan_izin" class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Alasan <span class="text-red-500">*</span></label>
                        <textarea name="alasan" id="alasan_izin" rows="2" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Tulis alasan izin..."></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Izin
                    </button>
                    <button type="button" onclick="closeIzinModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const grupMap = @json($grupMap ?? []);
    const oldSubGrup = '{{ request('sub_grup') }}';
    const oldGrup = '{{ request('grup') }}';

    const grupBpjsMap = @json($grupBpjsMap ?? []);
    const oldSubGrupBpjs = '{{ request('sub_grup_bpjs') }}';
    const oldGrupBpjs = '{{ request('grup_bpjs') }}';

    function handleGrupFilterChange() {
        const grupSelect = document.getElementById('grup');
        const subGrupSelect = document.getElementById('sub_grup');
        const selectedGrup = grupSelect.value;
        
        // Reset Sub Group options
        subGrupSelect.innerHTML = '<option value="">Semua Sub Group</option>';
        
        if (selectedGrup && grupMap[selectedGrup]) {
            const subs = grupMap[selectedGrup];
            subs.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub;
                option.text = sub;
                subGrupSelect.appendChild(option);
            });
        } else {
            // Populate all sub groups if no group is selected
            let allSubs = new Set();
            for (const key in grupMap) {
                grupMap[key].forEach(sub => allSubs.add(sub));
            }
            // Sort
            allSubs = Array.from(allSubs).sort();
            allSubs.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub;
                option.text = sub;
                subGrupSelect.appendChild(option);
            });
        }
    }

    function handleGrupBpjsFilterChange() {
        const grupBpjsSelect = document.getElementById('grup_bpjs');
        const subGrupBpjsSelect = document.getElementById('sub_grup_bpjs');
        const selectedGrupBpjs = grupBpjsSelect.value;
        
        // Reset Sub Group options
        subGrupBpjsSelect.innerHTML = '<option value="">Semua Sub Group BPJS</option>';
        
        if (selectedGrupBpjs && grupBpjsMap[selectedGrupBpjs]) {
            const subs = grupBpjsMap[selectedGrupBpjs];
            subs.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub;
                option.text = sub;
                subGrupBpjsSelect.appendChild(option);
            });
        } else {
            // Populate all sub groups bpjs if no group is selected
            let allSubs = new Set();
            for (const key in grupBpjsMap) {
                grupBpjsMap[key].forEach(sub => allSubs.add(sub));
            }
            // Sort
            allSubs = Array.from(allSubs).sort();
            allSubs.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub;
                option.text = sub;
                subGrupBpjsSelect.appendChild(option);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Run once on load to populate sub_grup if a grup was selected
        handleGrupFilterChange();
        handleGrupBpjsFilterChange();
        
        // If there was an old sub_grup selected, re-select it
        if (oldSubGrup) {
            const subGrupSelect = document.getElementById('sub_grup');
            if (Array.from(subGrupSelect.options).some(opt => opt.value === oldSubGrup)) {
                subGrupSelect.value = oldSubGrup;
            }
        }
        
        // If there was an old sub_grup_bpjs selected, re-select it
        if (oldSubGrupBpjs) {
            const subGrupBpjsSelect = document.getElementById('sub_grup_bpjs');
            if (Array.from(subGrupBpjsSelect.options).some(opt => opt.value === oldSubGrupBpjs)) {
                subGrupBpjsSelect.value = oldSubGrupBpjs;
            }
        }

        // Initialize Select2 for the Izin Modal
        if ($('#karyawan_id_izin').length) {
            $('#karyawan_id_izin').select2({
                placeholder: "Cari berdasarkan nama atau NIK...",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#izinModal')
            });
        }

        // Select All Karyawan
        const selectAllCheckbox = document.getElementById('selectAllKaryawan');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.karyawan-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = selectAllCheckbox.checked;
                });
            });
        }

        // Event listener for detail hadir
        document.querySelectorAll('.btn-detail-hadir').forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-nama');
                let dates = [];
                try {
                    dates = JSON.parse(this.getAttribute('data-dates'));
                } catch (e) {
                    console.error("Gagal parse dates", e);
                }
                showDetailHadir(nama, dates);
            });
        });

        // Event listener for detail lembur
        document.querySelectorAll('.btn-detail-lembur').forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-nama');
                let dates = [];
                try {
                    dates = JSON.parse(this.getAttribute('data-dates'));
                } catch (e) {
                    console.error("Gagal parse dates", e);
                }
                showDetailLembur(nama, dates);
            });
        });

        // Event listener for detail terlambat
        document.querySelectorAll('.btn-detail-terlambat').forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-nama');
                let dates = [];
                try {
                    dates = JSON.parse(this.getAttribute('data-dates'));
                } catch (e) {
                    console.error("Gagal parse dates", e);
                }
                showDetailTerlambat(nama, dates);
            });
        });

        // Event listener for detail pulang cepat
        document.querySelectorAll('.btn-detail-pulang-cepat').forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-nama');
                let dates = [];
                try {
                    dates = JSON.parse(this.getAttribute('data-dates'));
                } catch (e) {
                    console.error("Gagal parse dates", e);
                }
                showDetailPulangCepat(nama, dates);
            });
        });

        // Event listener for Tdk Masuk
        document.querySelectorAll('.btn-detail-tdk-masuk').forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-nama');
                let dates = [];
                try { dates = JSON.parse(this.getAttribute('data-dates')); } catch (e) {}
                showDetailLupaAbsen(nama, dates, 'Tidak Absen Masuk');
            });
        });

        // Event listener for Tdk Pulang
        document.querySelectorAll('.btn-detail-tdk-pulang').forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-nama');
                let dates = [];
                try { dates = JSON.parse(this.getAttribute('data-dates')); } catch (e) {}
                showDetailLupaAbsen(nama, dates, 'Tidak Absen Pulang');
            });
        });

        // Event listener for Tdk Istirahat
        document.querySelectorAll('.btn-detail-tdk-istirahat').forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-nama');
                let dates = [];
                try { dates = JSON.parse(this.getAttribute('data-dates')); } catch (e) {}
                showDetailLupaAbsen(nama, dates, 'Tidak Absen Istirahat');
            });
        });

        // Event listener for Alpha
        document.querySelectorAll('.btn-detail-alpha').forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-nama');
                let dates = [];
                try { dates = JSON.parse(this.getAttribute('data-dates')); } catch (e) {}
                showDetailLupaAbsen(nama, dates, 'Alpha');
            });
        });

    });
    
    function openIzinModal() {
        document.getElementById('izinModal').classList.remove('hidden');
    }
    
    function closeIzinModal() {
        document.getElementById('izinModal').classList.add('hidden');
    }

    function showDetailHadir(nama, dates) {
        let html = '';
        if (!dates || dates.length === 0) {
            html = '<p class="text-sm text-gray-500 text-center py-4">Tidak ada data kehadiran.</p>';
        } else {
            html = '<ul class="space-y-2">';
            dates.forEach((date, index) => {
                html += `
                    <li class="flex items-center text-sm font-medium text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                        <span class="w-6 h-6 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs mr-3 shrink-0">${index + 1}</span>
                        ${date}
                    </li>
                `;
            });
            html += '</ul>';
        }
        
        document.getElementById('detailHadirTitle').innerText = 'Detail Hadir: ' + nama;
        document.getElementById('detailHadirContent').innerHTML = html;
        document.getElementById('detailHadirModal').classList.remove('hidden');
    }

    function closeDetailHadir() {
        document.getElementById('detailHadirModal').classList.add('hidden');
    }

    function showDetailLembur(nama, dates) {
        let html = '';
        if (!dates || dates.length === 0) {
            html = '<p class="text-sm text-gray-500 text-center py-4">Tidak ada data lembur.</p>';
        } else {
            html = '<ul class="space-y-2">';
            dates.forEach((date, index) => {
                html += `
                    <li class="flex items-center text-sm font-medium text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs mr-3 shrink-0">${index + 1}</span>
                        ${date}
                    </li>
                `;
            });
            html += '</ul>';
        }
        
        document.getElementById('detailLemburTitle').innerText = 'Detail Lembur: ' + nama;
        document.getElementById('detailLemburContent').innerHTML = html;
        document.getElementById('detailLemburModal').classList.remove('hidden');
    }

    function closeDetailLembur() {
        document.getElementById('detailLemburModal').classList.add('hidden');
    }

    function showDetailTerlambat(nama, dates) {
        let html = '';
        if (!dates || dates.length === 0) {
            html = '<p class="text-sm text-gray-500 text-center py-4">Tidak ada data terlambat.</p>';
        } else {
            html = '<ul class="space-y-2">';
            dates.forEach((date, index) => {
                html += `
                    <li class="flex items-center text-sm font-medium text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                        <span class="w-6 h-6 rounded-full bg-red-100 text-red-700 flex items-center justify-center text-xs mr-3 shrink-0">${index + 1}</span>
                        ${date}
                    </li>
                `;
            });
            html += '</ul>';
        }
        
        document.getElementById('detailTerlambatTitle').innerText = 'Detail Terlambat: ' + nama;
        document.getElementById('detailTerlambatContent').innerHTML = html;
        document.getElementById('detailTerlambatModal').classList.remove('hidden');
    }

    function closeDetailTerlambat() {
        document.getElementById('detailTerlambatModal').classList.add('hidden');
    }

    function showDetailPulangCepat(nama, dates) {
        let html = '';
        if (!dates || dates.length === 0) {
            html = '<p class="text-sm text-gray-500 text-center py-4">Tidak ada data pulang cepat.</p>';
        } else {
            html = '<ul class="space-y-2">';
            dates.forEach((date, index) => {
                html += `
                    <li class="flex items-center text-sm font-medium text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                        <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xs mr-3 shrink-0">${index + 1}</span>
                        ${date}
                    </li>
                `;
            });
            html += '</ul>';
        }
        
        document.getElementById('detailPulangCepatTitle').innerText = 'Detail Pulang Cepat: ' + nama;
        document.getElementById('detailPulangCepatContent').innerHTML = html;
        document.getElementById('detailPulangCepatModal').classList.remove('hidden');
    }

    function closeDetailPulangCepat() {
        document.getElementById('detailPulangCepatModal').classList.add('hidden');
    }

    function showDetailLupaAbsen(nama, dates, typeLabel) {
        let html = '';
        if (!dates || dates.length === 0) {
            html = `<p class="text-sm text-gray-500 text-center py-4">Tidak ada data ${typeLabel}.</p>`;
        } else {
            html = '<ul class="space-y-2">';
            dates.forEach((date, index) => {
                html += `
                    <li class="flex items-center text-sm font-medium text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                        <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center text-xs mr-3 shrink-0">${index + 1}</span>
                        ${date}
                    </li>
                `;
            });
            html += '</ul>';
        }
        
        document.getElementById('detailLupaAbsenTitle').innerText = `Detail ${typeLabel}: ${nama}`;
        document.getElementById('detailLupaAbsenContent').innerHTML = html;
        document.getElementById('detailLupaAbsenModal').classList.remove('hidden');
    }

    function closeDetailLupaAbsen() {
        document.getElementById('detailLupaAbsenModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
