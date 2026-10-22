@extends('layouts.app')

@section('title', 'Data Absensi')
@section('page_title', 'Data Absensi')

@section('content')
<div class="min-h-screen bg-slate-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 sm:p-6 mb-6">
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
                <div class="flex items-start gap-4">
                    <span class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" /></svg>
                    </span>
                    <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Data Absensi</h1>
                    <p class="mt-1 text-sm text-slate-500">Daftar log kehadiran karyawan hasil sinkronisasi mesin fingerprint.</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2 w-full xl:max-w-4xl">
                    @can('absensi-create')
                    <button type="button" onclick="document.getElementById('createModal').classList.remove('hidden')" class="inline-flex min-h-11 items-center justify-center px-3 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Manual
                    </button>
                    @endcan
                    <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="inline-flex min-h-11 items-center justify-center px-3 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Import File Absen
                    </button>
                    <a href="{{ route('absensi.export_machine_users') }}" class="inline-flex min-h-11 items-center justify-center px-3 py-2 bg-teal-700 text-white text-sm font-semibold rounded-lg hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Data User
                    </a>
                    <a href="{{ route('absensi.export_excel', request()->query()) }}" class="inline-flex min-h-11 items-center justify-center px-3 py-2 bg-blue-700 text-white text-sm font-semibold rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Excel
                    </a>
                    <a href="{{ route('absensi.export_pdf', request()->query()) }}" class="inline-flex min-h-11 items-center justify-center px-3 py-2 bg-rose-600 text-white text-sm font-semibold rounded-lg hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download PDF
                    </a>
                    <button type="button" onclick="document.getElementById('exportDatModal').classList.remove('hidden')" class="inline-flex min-h-11 items-center justify-center px-3 py-2 bg-sky-700 text-white text-sm font-semibold rounded-lg hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download File .dat
                    </button>
                    <button type="button" onclick="document.getElementById('importKoreksiModal').classList.remove('hidden')" class="inline-flex min-h-11 items-center justify-center px-3 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Import Koreksi Excel
                    </button>
                    <a href="{{ route('absensi.rekap') }}" class="inline-flex min-h-11 items-center justify-center px-3 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Lihat Rekap Absensi
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Notifikasi Error -->
        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9a1 1 0 012 0v4a1 1 0 01-2 0V9zm0 6a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 sm:p-6 mb-6">
            <form action="{{ route('absensi.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                    <!-- Search Karyawan -->
                    <div>
                        <label for="search" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-1.5"><svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="m20 20-4-4"/></svg>Cari Karyawan / NIK</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Nama atau NIK..."
                               class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm placeholder:text-slate-400">
                    </div>

                    <!-- Pekerjaan -->
                    <div>
                        <label for="pekerjaan" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-1.5"><svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/></svg>Pekerjaan</label>
                        <select name="pekerjaan" id="pekerjaan"
                                class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                            <option value="">Semua Pekerjaan</option>
                            @foreach($pekerjaans as $pekerjaan)
                                <option value="{{ $pekerjaan }}" {{ request('pekerjaan') == $pekerjaan ? 'selected' : '' }}>{{ $pekerjaan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Departemen -->
                    <div>
                        <label for="departemen" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-1.5"><svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5" stroke-width="1.8"/></svg>Departemen</label>
                        <select name="departemen" id="departemen"
                                class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                            <option value="">Semua Departemen</option>
                            @foreach($departemens as $departemen)
                                <option value="{{ $departemen }}" {{ request('departemen') == $departemen ? 'selected' : '' }}>{{ strtoupper($departemen) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Cabang -->
                    <div>
                        <label for="cabang" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-1.5"><svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M10 21v-4h4v4"/></svg>Cabang</label>
                        <select name="cabang" id="cabang"
                                class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangs as $cabang)
                                <option value="{{ $cabang }}" {{ request('cabang') == $cabang ? 'selected' : '' }}>{{ strtoupper($cabang) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Rentang Tanggal -->
                    <div>
                        <label for="start_date" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-1.5"><svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M16 3v4M8 3v4M3 10h18"/></svg>Tanggal Dari</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                               onchange="document.getElementById('end_date').min = this.value"
                               class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                    </div>

                    <div>
                        <label for="end_date" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-1.5"><svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M16 3v4M8 3v4M3 10h18"/></svg>Tanggal Sampai</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" min="{{ $startDate }}"
                               class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 110-15 7.5 7.5 0 010 15z" /></svg>
                        Filter Data
                    </button>
                    @if(request()->anyFilled(['search', 'pekerjaan', 'departemen', 'cabang', 'start_date', 'end_date']))
                        <a href="{{ route('absensi.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-lg border border-slate-300 focus:outline-none transition shadow-sm">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-5 py-4 border-b border-slate-200">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Log Kehadiran</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Rincian waktu masuk, istirahat, pulang, dan lembur.</p>
                </div>
                <span class="inline-flex w-fit items-center gap-2 rounded-full bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600 border border-slate-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Data absensi
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="table-fixed min-w-[1100px] w-full divide-y divide-slate-200 xl:min-w-0">
                    <colgroup>
                        <col style="width: 3%">
                        <col style="width: 5%">
                        <col style="width: 10%">
                        <col style="width: 7%">
                        <col style="width: 8%">
                        <col style="width: 7%">
                        <col style="width: 7%">
                        <col style="width: 7%">
                        <col style="width: 7%">
                        <col style="width: 7%">
                        <col style="width: 7%">
                        <col style="width: 10%">
                        <col style="width: 10%">
                    </colgroup>
                    <thead class="bg-slate-50 font-semibold text-slate-500 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-2 py-3 text-left">No</th>
                            <th class="px-2 py-3 text-left">NIK</th>
                            <th class="px-2 py-3 text-left">Nama Lengkap</th>
                            <th class="px-2 py-3 text-left">Pekerjaan</th>
                            <th class="px-2 py-3 text-left">Tanggal</th>
                            <th class="px-2 py-3 text-center text-emerald-700 bg-emerald-50/70">Jam Masuk</th>
                            <th class="px-2 py-3 text-center text-amber-700 bg-amber-50/70">Istirahat Keluar</th>
                            <th class="px-2 py-3 text-center text-amber-700 bg-amber-50/70">Istirahat Masuk</th>
                            <th class="px-2 py-3 text-center text-rose-700 bg-rose-50/70">Jam Pulang</th>
                            <th class="px-2 py-3 text-center text-violet-700 bg-violet-50/70">Lembur Masuk</th>
                            <th class="px-2 py-3 text-center text-violet-700 bg-violet-50/70">Lembur Pulang</th>
                            <th class="px-2 py-3 text-center text-violet-700 bg-violet-50/70">Durasi Lembur</th>

                            <th class="px-2 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 text-sm text-slate-800">
                        @forelse($absensis as $index => $absensi)
                            <tr class="hover:bg-blue-50/40 transition-colors duration-150">
                                <td class="px-3 py-3 whitespace-nowrap font-medium text-slate-400">
                                    {{ $absensis->firstItem() + $index }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap font-semibold font-mono text-blue-700">
                                    {{ $absensi->nik }}
                                </td>
                                <td class="px-3 py-3 font-semibold text-slate-900">
                                    {{ $absensi->karyawan ? $absensi->karyawan->nama_lengkap : 'Karyawan Tidak Terdaftar' }}
                                </td>
                                <td class="px-3 py-3 text-slate-600">
                                    {{ $absensi->karyawan ? $absensi->karyawan->pekerjaan : '-' }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap font-mono text-slate-600">
                                    {{ Carbon\Carbon::parse($absensi->tanggal)->format('d-m-Y') }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-center font-mono font-bold text-emerald-700 bg-emerald-50/30 group relative">
                                    {{ $absensi->waktu_masuk ? Carbon\Carbon::parse($absensi->waktu_masuk)->format('H:i') : '-' }}
                                    @if($absensi->waktu_masuk)
                                    <form action="{{ route('absensi.delete_log') }}" method="POST" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Hapus jam masuk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="nik" value="{{ $absensi->nik }}">
                                        <input type="hidden" name="tanggal" value="{{ $absensi->tanggal }}">
                                        <input type="hidden" name="tipe" value="Masuk">
                                        <button type="submit" class="text-red-500 hover:text-red-700 bg-white rounded-full p-0.5 shadow-sm" title="Hapus Jam Masuk">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-center font-mono font-bold text-amber-700 bg-amber-50/30">
                                    {{ $absensi->waktu_istirahat_keluar ? Carbon\Carbon::parse($absensi->waktu_istirahat_keluar)->format('H:i') : '-' }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-center font-mono font-bold text-amber-700 bg-amber-50/30">
                                    {{ $absensi->waktu_istirahat_masuk ? Carbon\Carbon::parse($absensi->waktu_istirahat_masuk)->format('H:i') : '-' }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-center font-mono font-bold text-rose-700 bg-rose-50/30 group relative">
                                    {{ $absensi->waktu_pulang ? Carbon\Carbon::parse($absensi->waktu_pulang)->format('H:i') : '-' }}
                                    @if($absensi->waktu_pulang)
                                    <form action="{{ route('absensi.delete_log') }}" method="POST" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Hapus jam pulang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="nik" value="{{ $absensi->nik }}">
                                        <input type="hidden" name="tanggal" value="{{ $absensi->tanggal }}">
                                        <input type="hidden" name="tipe" value="Pulang">
                                        <button type="submit" class="text-red-500 hover:text-red-700 bg-white rounded-full p-0.5 shadow-sm" title="Hapus Jam Pulang">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-center font-mono font-bold text-violet-700 bg-violet-50/30 group relative">
                                    {{ $absensi->waktu_lembur_masuk ? Carbon\Carbon::parse($absensi->waktu_lembur_masuk)->format('H:i') : '-' }}
                                    @if($absensi->waktu_lembur_masuk)
                                    <form action="{{ route('absensi.delete_log') }}" method="POST" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Hapus jam lembur masuk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="nik" value="{{ $absensi->nik }}">
                                        <input type="hidden" name="tanggal" value="{{ $absensi->tanggal }}">
                                        <input type="hidden" name="tipe" value="lembur_masuk">
                                        <button type="submit" class="text-red-500 hover:text-red-700 bg-white rounded-full p-0.5 shadow-sm" title="Hapus Jam Lembur Masuk">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-center font-mono font-bold text-violet-700 bg-violet-50/30 group relative">
                                    {{ $absensi->waktu_lembur_pulang ? Carbon\Carbon::parse($absensi->waktu_lembur_pulang)->format('H:i') : '-' }}
                                    @if($absensi->waktu_lembur_pulang && Carbon\Carbon::parse($absensi->waktu_lembur_pulang)->toDateString() !== $absensi->tanggal)
                                        <span class="block text-xs font-normal" title="Tanggal aktual pulang lembur">{{ Carbon\Carbon::parse($absensi->waktu_lembur_pulang)->format('d/m/Y') }}</span>
                                    @endif
                                    @if($absensi->waktu_lembur_pulang)
                                    <form action="{{ route('absensi.delete_log') }}" method="POST" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Hapus jam lembur pulang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="nik" value="{{ $absensi->nik }}">
                                        <input type="hidden" name="tanggal" value="{{ $absensi->tanggal }}">
                                        <input type="hidden" name="tipe" value="lembur_pulang">
                                        <button type="submit" class="text-red-500 hover:text-red-700 bg-white rounded-full p-0.5 shadow-sm" title="Hapus Jam Lembur Pulang">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                </td>


                                <td class="px-3 py-3 whitespace-nowrap text-center font-mono font-bold text-violet-700 bg-violet-50/30">
                                    @php
                                        $durasiLemburMenit = null;
                                        if ($absensi->waktu_lembur_masuk && $absensi->waktu_lembur_pulang) {
                                            $mulaiLembur = Carbon\Carbon::parse($absensi->waktu_lembur_masuk);
                                            $selesaiLembur = Carbon\Carbon::parse($absensi->waktu_lembur_pulang);
                                            if ($selesaiLembur->gte($mulaiLembur)) {
                                                $durasiLemburMenit = (int) floor(($selesaiLembur->timestamp - $mulaiLembur->timestamp) / 60);
                                            }
                                        }
                                    @endphp
                                    @if($durasiLemburMenit !== null)
                                        {{ intdiv($durasiLemburMenit, 60) }} jam {{ $durasiLemburMenit % 60 }} menit
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center space-x-1">
                                        <a href="{{ route('absensi.show', ['nik' => $absensi->nik, 'tanggal' => $absensi->tanggal]) }}" 
                                           class="inline-flex h-9 w-9 items-center justify-center text-blue-700 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 rounded-lg transition" title="Lihat">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        @can('absensi-edit')
                                        <button type="button" 
                                            onclick="openEditModal('{{ $absensi->nik }}', '{{ $absensi->tanggal }}', '{{ $absensi->waktu_masuk ? \Carbon\Carbon::parse($absensi->waktu_masuk)->format('H:i') : '' }}', '{{ $absensi->waktu_istirahat_keluar ? \Carbon\Carbon::parse($absensi->waktu_istirahat_keluar)->format('H:i') : '' }}', '{{ $absensi->waktu_istirahat_masuk ? \Carbon\Carbon::parse($absensi->waktu_istirahat_masuk)->format('H:i') : '' }}', '{{ $absensi->waktu_pulang ? \Carbon\Carbon::parse($absensi->waktu_pulang)->format('H:i') : '' }}', '{{ $absensi->waktu_lembur_masuk ? \Carbon\Carbon::parse($absensi->waktu_lembur_masuk)->format('H:i') : '' }}', '{{ $absensi->waktu_lembur_pulang ? \Carbon\Carbon::parse($absensi->waktu_lembur_pulang)->format('H:i') : '' }}')"
                                            class="inline-flex h-9 w-9 items-center justify-center text-indigo-700 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        @endcan
                                        @can('absensi-delete')
                                        <form action="{{ route('absensi.destroy_day') }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus seluruh log absensi karyawan ini pada {{ $absensi->tanggal }} dari database? Log yang dihapus tidak akan diimpor ulang oleh sinkronisasi mesin.')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="nik" value="{{ $absensi->nik }}">
                                            <input type="hidden" name="tanggal" value="{{ $absensi->tanggal }}">
                                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center text-rose-700 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 rounded-lg transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada data absensi</h3>
                                        <p class="text-xs text-gray-500">Silakan lakukan sinkronisasi mesin fingerprint atau sesuaikan filter Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($absensis->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                {{ $absensis->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="document.getElementById('importModal').classList.add('hidden')"></div>

        <!-- Modal panel -->
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('absensi.import') }}" method="POST" enctype="multipart/form-data" onsubmit="document.getElementById('btnSubmitImport').disabled=true; document.getElementById('btnSubmitImport').innerHTML='Memproses...';">
                @csrf
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-green-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Import Data Absensi
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    Upload file absensi dari mesin ZKTeco. Mendukung format Excel (<b>.xls, .xlsx</b>) atau format log text (<b>.dat, .txt</b>).
                                </p>
                                <div class="w-full">
                                    <label class="block text-sm font-medium text-gray-700">File Absensi</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 hover:bg-gray-50 transition-colors cursor-pointer" onclick="document.getElementById('file_absensi').click()">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <label for="file_absensi" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Upload file</span>
                                                    <input id="file_absensi" name="file_absensi" type="file" class="sr-only" accept=".xls,.xlsx,.dat,.txt,.csv" required onchange="document.getElementById('file-name').textContent = this.files[0].name">
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500" id="file-name">
                                                Pilih file...
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" id="btnSubmitImport" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Import Data
                    </button>
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export DAT Modal -->
<div id="exportDatModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="document.getElementById('exportDatModal').classList.add('hidden')"></div>

        <!-- Modal panel -->
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('absensi.export_dat') }}" method="GET" onsubmit="setTimeout(() => document.getElementById('exportDatModal').classList.add('hidden'), 500);">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-blue-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Download File .dat
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                                    <input type="date" name="start_date" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->toDateString()) }}" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Hingga Tanggal</label>
                                    <input type="date" name="end_date" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ request('end_date', \Carbon\Carbon::now()->endOfMonth()->toDateString()) }}" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Sumber Mesin (Opsional)</label>
                                    <select name="mesin_id[]" multiple class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm select2" data-placeholder="Semua Mesin">
                                        @foreach($mesins as $mesin)
                                            <option value="{{ $mesin->id }}" {{ (is_array(request('mesin_id')) && in_array($mesin->id, request('mesin_id'))) ? 'selected' : '' }}>{{ $mesin->nama_mesin }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Download
                    </button>
                    <button type="button" onclick="document.getElementById('exportDatModal').classList.add('hidden')" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

<!-- Import Koreksi Excel Modal -->
<div id="importKoreksiModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="document.getElementById('importKoreksiModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('absensi.import_excel_koreksi') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-emerald-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Import Koreksi Absensi (Excel)</h3>
                            <div class="mt-4 space-y-4 text-left">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tipe Absen</label>
                                    <select name="tipe_absen" id="importTipeAbsen" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" onchange="updateTemplateLink()">
                                        <option value="Reguler">Absen Jam Kerja (Masuk & Pulang)</option>
                                        <option value="Lembur">Absen Lembur (Masuk & Pulang)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Absensi</label>
                                    <input type="date" name="tanggal_absensi" required value="{{ date('Y-m-d') }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div class="pt-2">
                                    <a href="{{ route('absensi.template_import', ['tipe' => 'Reguler']) }}" id="downloadTemplateBtn" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Download Template <span id="templateTypeName" class="ml-1 font-semibold underline">Absen Jam Kerja (Masuk & Pulang)</span>
                                    </a>
                                </div>
                                <div class="pt-2">
                                    <label class="block text-sm font-medium text-gray-700">Upload File Excel (.xlsx, .xls, .csv)</label>
                                    <input type="file" name="file_excel" accept=".xlsx,.xls,.csv" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-emerald-600 border border-transparent rounded-md shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Upload & Proses
                    </button>
                    <button type="button" onclick="document.getElementById('importKoreksiModal').classList.add('hidden')" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="resetCreateForm()"></div>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
            <form action="{{ route('absensi.store') }}" method="POST">
                @csrf
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modal-title">Tambah Absensi Manual</h3>
                    <div class="space-y-4">
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter Data</label>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="tipe_karyawan_filter" value="karyawan" checked class="form-radio text-indigo-600 focus:ring-indigo-500 h-4 w-4 border-gray-300" onchange="updateNikOptions('karyawan')">
                                    <span class="ml-2 text-sm text-gray-700">Karyawan</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="tipe_karyawan_filter" value="non_karyawan" class="form-radio text-indigo-600 focus:ring-indigo-500 h-4 w-4 border-gray-300" onchange="updateNikOptions('non_karyawan')">
                                    <span class="ml-2 text-sm text-gray-700">Non Karyawan</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilih Orang</label>
                            <select name="nik" id="create_nik" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm select2" required style="width: 100%">
                                <option value="">Pilih Karyawan</option>
                                @foreach($karyawanList as $karyawan)
                                    <option value="{{ $karyawan->nik }}">{{ $karyawan->nik }} - {{ $karyawan->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="entries_container" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                            <!-- Entry Block -->
                            <div class="absensi-entry bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
                                <h4 class="text-sm font-bold text-gray-700 mb-3 border-b pb-2 flex justify-between items-center">
                                    <span>Entri <span class="entry-number">1</span></span>
                                    <button type="button" class="text-red-500 hover:text-red-700 hidden remove-entry-btn" onclick="removeEntry(this)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </h4>
                                
                                <div class="mb-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal</label>
                                    <input type="text" name="tanggal[]" value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}" class="tanggal-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="dd/mm/yyyy" pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}" maxlength="10" required onchange="fetchDataForEntry(this)">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Jam Masuk</label>
                                        <input type="text" name="waktu_masuk[]" class="waktu-masuk-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="HH:mm" pattern="[0-2][0-9]:[0-5][0-9]" maxlength="5">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Jam Pulang</label>
                                        <input type="text" name="waktu_pulang[]" class="waktu-pulang-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="HH:mm" pattern="[0-2][0-9]:[0-5][0-9]" maxlength="5">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Istirahat Keluar</label>
                                        <input type="text" name="waktu_istirahat_keluar[]" class="waktu-istirahat-keluar-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="HH:mm" pattern="[0-2][0-9]:[0-5][0-9]" maxlength="5">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Istirahat Masuk</label>
                                        <input type="text" name="waktu_istirahat_masuk[]" class="waktu-istirahat-masuk-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="HH:mm" pattern="[0-2][0-9]:[0-5][0-9]" maxlength="5">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Lembur Masuk</label>
                                        <input type="text" name="waktu_lembur_masuk[]" class="waktu-lembur-masuk-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="HH:mm" pattern="[0-2][0-9]:[0-5][0-9]" maxlength="5">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Lembur Pulang</label>
                                        <input type="text" name="waktu_lembur_pulang[]" class="waktu-lembur-pulang-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="HH:mm" pattern="[0-2][0-9]:[0-5][0-9]" maxlength="5">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" onclick="addAbsensiEntry()" class="w-full mt-2 inline-flex justify-center items-center px-4 py-2 bg-indigo-50 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-100 border border-indigo-200 border-dashed transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Entri Hari Lain
                        </button>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan
                    </button>
                    <button type="button" onclick="resetCreateForm()" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="document.getElementById('editModal').classList.add('hidden')"></div>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
            <form action="{{ route('absensi.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modal-title">Edit Absensi</h3>
                    <div class="space-y-4">
                        <input type="hidden" name="nik" id="edit_nik">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                            <input type="date" name="tanggal" id="edit_tanggal" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm focus:outline-none sm:text-sm" readonly>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jam Masuk</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="time" name="waktu_masuk" id="edit_waktu_masuk" class="flex-1 block w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <button type="button" onclick="document.getElementById('edit_waktu_masuk').value = ''" class="inline-flex items-center px-3 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 rounded-r-md hover:bg-red-50 hover:text-red-500 transition-colors" title="Reset Jam Masuk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jam Pulang</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="time" name="waktu_pulang" id="edit_waktu_pulang" class="flex-1 block w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <button type="button" onclick="document.getElementById('edit_waktu_pulang').value = ''" class="inline-flex items-center px-3 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 rounded-r-md hover:bg-red-50 hover:text-red-500 transition-colors" title="Reset Jam Pulang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Istirahat Keluar</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="time" name="waktu_istirahat_keluar" id="edit_waktu_istirahat_keluar" class="flex-1 block w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <button type="button" onclick="document.getElementById('edit_waktu_istirahat_keluar').value = ''" class="inline-flex items-center px-3 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 rounded-r-md hover:bg-red-50 hover:text-red-500 transition-colors" title="Reset Istirahat Keluar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Istirahat Masuk</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="time" name="waktu_istirahat_masuk" id="edit_waktu_istirahat_masuk" class="flex-1 block w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <button type="button" onclick="document.getElementById('edit_waktu_istirahat_masuk').value = ''" class="inline-flex items-center px-3 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 rounded-r-md hover:bg-red-50 hover:text-red-500 transition-colors" title="Reset Istirahat Masuk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lembur Masuk</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="time" name="waktu_lembur_masuk" id="edit_waktu_lembur_masuk" class="flex-1 block w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <button type="button" onclick="document.getElementById('edit_waktu_lembur_masuk').value = ''" class="inline-flex items-center px-3 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 rounded-r-md hover:bg-red-50 hover:text-red-500 transition-colors" title="Reset Lembur Masuk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lembur Pulang</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="time" name="waktu_lembur_pulang" id="edit_waktu_lembur_pulang" class="flex-1 block w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <button type="button" onclick="document.getElementById('edit_waktu_lembur_pulang').value = ''" class="inline-flex items-center px-3 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 rounded-r-md hover:bg-red-50 hover:text-red-500 transition-colors" title="Reset Lembur Pulang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Gunakan input teks agar format tidak mengikuti regional setting browser/Windows.
    function dateIdToIso(value) {
        const match = String(value || '').trim().match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        return match ? `${match[3]}-${match[2]}-${match[1]}` : String(value || '').trim();
    }

    function dateIsoToId(value) {
        const match = String(value || '').trim().match(/^(\d{4})-(\d{2})-(\d{2})$/);
        return match ? `${match[3]}/${match[2]}/${match[1]}` : String(value || '').trim();
    }

    function timeTo24Hour(value) {
        const text = String(value || '').trim().toUpperCase();
        const match = text.match(/^(\d{1,2}):(\d{2})(?::\d{2})?\s*(AM|PM)?$/);
        if (!match) return text;
        let hour = parseInt(match[1], 10);
        const minute = match[2];
        if (match[3] === 'PM' && hour < 12) hour += 12;
        if (match[3] === 'AM' && hour === 12) hour = 0;
        return `${String(hour).padStart(2, '0')}:${minute}`;
    }

    function apply24HourTimeFormat() {
        document.querySelectorAll('.waktu-masuk-input, .waktu-pulang-input, .waktu-istirahat-keluar-input, .waktu-istirahat-masuk-input, .waktu-lembur-masuk-input, .waktu-lembur-pulang-input').forEach(input => {
            if (input.dataset.formatBound) return;
            input.dataset.formatBound = '1';
            input.addEventListener('blur', () => input.value = timeTo24Hour(input.value));
        });
    }

    document.addEventListener('DOMContentLoaded', apply24HourTimeFormat);

    new MutationObserver(apply24HourTimeFormat).observe(document.body, {
        childList: true,
        subtree: true,
    });

    document.querySelector('#createModal form')?.addEventListener('submit', function() {
        this.querySelectorAll('.tanggal-input').forEach(input => {
            input.value = dateIdToIso(input.value);
        });
        this.querySelectorAll('.waktu-masuk-input, .waktu-pulang-input, .waktu-istirahat-keluar-input, .waktu-istirahat-masuk-input, .waktu-lembur-masuk-input, .waktu-lembur-pulang-input').forEach(input => {
            input.value = timeTo24Hour(input.value);
        });
    });

    function toggleEntryInputsState(entry, disabled) {
        const inputs = entry.querySelectorAll('.waktu-masuk-input, .waktu-pulang-input, .waktu-istirahat-keluar-input, .waktu-istirahat-masuk-input, .waktu-lembur-masuk-input, .waktu-lembur-pulang-input');
        inputs.forEach(input => {
            input.disabled = disabled;
        });
    }

    function addAbsensiEntry() {
        const container = document.getElementById('entries_container');
        const entryCount = container.querySelectorAll('.absensi-entry').length + 1;
        
        const template = `
            <div class="absensi-entry bg-gray-50 p-4 rounded-lg border border-gray-200 relative mt-4">
                <h4 class="text-sm font-bold text-gray-700 mb-3 border-b pb-2 flex justify-between items-center">
                    <span>Entri <span class="entry-number">${entryCount}</span></span>
                    <button type="button" class="text-red-500 hover:text-red-700 remove-entry-btn" onclick="removeEntry(this)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </h4>
                
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="text" name="tanggal[]" value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}" class="tanggal-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="dd/mm/yyyy" pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}" maxlength="10" required onchange="fetchDataForEntry(this)">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jam Masuk</label>
                        <input type="text" name="waktu_masuk[]" class="waktu-masuk-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="HH:mm" pattern="[0-2][0-9]:[0-5][0-9]" maxlength="5">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jam Pulang</label>
                        <input type="text" name="waktu_pulang[]" class="waktu-pulang-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="HH:mm" pattern="[0-2][0-9]:[0-5][0-9]" maxlength="5">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Istirahat Keluar</label>
                        <input type="text" name="waktu_istirahat_keluar[]" class="waktu-istirahat-keluar-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="HH:mm" pattern="[0-2][0-9]:[0-5][0-9]" maxlength="5">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Istirahat Masuk</label>
                        <input type="text" name="waktu_istirahat_masuk[]" class="waktu-istirahat-masuk-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="HH:mm" pattern="[0-2][0-9]:[0-5][0-9]" maxlength="5">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Lembur Masuk</label>
                        <input type="text" name="waktu_lembur_masuk[]" class="waktu-lembur-masuk-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="HH:mm" pattern="[0-2][0-9]:[0-5][0-9]" maxlength="5">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Lembur Pulang</label>
                        <input type="text" name="waktu_lembur_pulang[]" class="waktu-lembur-pulang-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs" inputmode="numeric" placeholder="HH:mm" pattern="[0-2][0-9]:[0-5][0-9]" maxlength="5">
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', template);
        updateEntryNumbers();
        
        const lastEntry = container.lastElementChild;
        const dateInput = lastEntry.querySelector('.tanggal-input');
        fetchDataForEntry(dateInput);
    }

    function removeEntry(btn) {
        btn.closest('.absensi-entry').remove();
        updateEntryNumbers();
    }

    function updateEntryNumbers() {
        const entries = document.querySelectorAll('.absensi-entry');
        entries.forEach((entry, index) => {
            entry.querySelector('.entry-number').textContent = index + 1;
            const removeBtn = entry.querySelector('.remove-entry-btn');
            if (entries.length > 1) {
                removeBtn.classList.remove('hidden');
            } else {
                removeBtn.classList.add('hidden');
            }
        });
    }

    function fetchDataForEntry(dateInput) {
        const nik = document.getElementById('create_nik').value;
        const tanggal = dateIdToIso(dateInput.value);
        const entry = dateInput.closest('.absensi-entry');

        if (nik && tanggal) {
            toggleEntryInputsState(entry, true);
            fetch(`{{ route('absensi.get_data') }}?nik=${nik}&tanggal=${tanggal}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const setAndLock = (className, val) => {
                        const el = entry.querySelector('.' + className);
                        if(el) {
                            el.value = timeTo24Hour(val || '');
                            if (val) {
                                el.readOnly = true;
                                el.classList.add('bg-gray-200', 'cursor-not-allowed');
                            } else {
                                el.readOnly = false;
                                el.classList.remove('bg-gray-200', 'cursor-not-allowed');
                            }
                        }
                    };
                    
                    setAndLock('waktu-masuk-input', data.waktu_masuk);
                    setAndLock('waktu-pulang-input', data.waktu_pulang);
                    setAndLock('waktu-istirahat-keluar-input', data.waktu_istirahat_keluar);
                    setAndLock('waktu-istirahat-masuk-input', data.waktu_istirahat_masuk);
                    setAndLock('waktu-lembur-masuk-input', data.waktu_lembur_masuk);
                    setAndLock('waktu-lembur-pulang-input', data.waktu_lembur_pulang);
                    
                    toggleEntryInputsState(entry, false);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    clearEntryInputs(entry);
                    toggleEntryInputsState(entry, false);
                });
        } else {
            clearEntryInputs(entry);
        }
    }

    function fetchAllEntriesData() {
        const entries = document.querySelectorAll('.absensi-entry');
        entries.forEach(entry => {
            const dateInput = entry.querySelector('.tanggal-input');
            fetchDataForEntry(dateInput);
        });
    }

    function clearEntryInputs(entry) {
        const clearAndUnlock = (className) => {
            const el = entry.querySelector('.' + className);
            if(el) {
                el.value = '';
                el.readOnly = false;
                el.classList.remove('bg-gray-200', 'cursor-not-allowed');
            }
        };
        clearAndUnlock('waktu-masuk-input');
        clearAndUnlock('waktu-pulang-input');
        clearAndUnlock('waktu-istirahat-keluar-input');
        clearAndUnlock('waktu-istirahat-masuk-input');
        clearAndUnlock('waktu-lembur-masuk-input');
        clearAndUnlock('waktu-lembur-pulang-input');
    }

    function resetCreateForm() {
        document.getElementById('createModal').classList.add('hidden');
        $('#create_nik').val('').trigger('change.select2');
        
        const container = document.getElementById('entries_container');
        const entries = container.querySelectorAll('.absensi-entry');
        for(let i = 1; i < entries.length; i++) {
            entries[i].remove();
        }
        
        const firstEntry = container.querySelector('.absensi-entry');
        firstEntry.querySelector('.tanggal-input').value = '{{ \Carbon\Carbon::now()->format('d/m/Y') }}';
        clearEntryInputs(firstEntry);
        updateEntryNumbers();
    }
    const karyawanData = @json($karyawanList);
    const nonKaryawanData = @json($nonKaryawanList);

    function updateNikOptions(type) {
        const select = $('#create_nik');
        select.empty();
        
        let options = '<option value="">Pilih ' + (type === 'karyawan' ? 'Karyawan' : 'Non Karyawan') + '</option>';
        const data = type === 'karyawan' ? karyawanData : nonKaryawanData;
        
        data.forEach(item => {
            options += `<option value="${item.nik}">${item.nik} - ${item.nama_lengkap}</option>`;
        });
        
        select.html(options);
        select.trigger('change.select2');
        fetchAllEntriesData();
    }

    $(document).ready(function() {
        // Initialize Select2 for modal
        $('#create_nik').select2({
            dropdownParent: $('#createModal'),
            placeholder: "Pilih Karyawan",
            allowClear: true
        });

        $('#create_nik').on('change', function() {
            fetchAllEntriesData();
        });
    });

    function openEditModal(nik, tanggal, masuk, isOut, isIn, pulang, lemburIn, lemburOut) {
        document.getElementById('edit_nik').value = nik;
        document.getElementById('edit_tanggal').value = tanggal;
        
        const setAndLockEdit = (id, val) => {
            const el = document.getElementById(id);
            el.value = val || '';
            el.readOnly = false;
            el.classList.remove('bg-gray-200', 'cursor-not-allowed');
        };

        setAndLockEdit('edit_waktu_masuk', masuk);
        setAndLockEdit('edit_waktu_istirahat_keluar', isOut);
        setAndLockEdit('edit_waktu_istirahat_masuk', isIn);
        setAndLockEdit('edit_waktu_pulang', pulang);
        setAndLockEdit('edit_waktu_lembur_masuk', lemburIn);
        setAndLockEdit('edit_waktu_lembur_pulang', lemburOut);
        
        document.getElementById('editModal').classList.remove('hidden');
    }

    function resetEditJam() {
        document.getElementById('edit_waktu_masuk').value = '';
        document.getElementById('edit_waktu_istirahat_keluar').value = '';
        document.getElementById('edit_waktu_istirahat_masuk').value = '';
        document.getElementById('edit_waktu_pulang').value = '';
        document.getElementById('edit_waktu_lembur_masuk').value = '';
        document.getElementById('edit_waktu_lembur_pulang').value = '';
    }
</script>

<script>
function updateTemplateLink() {
    const sel = document.getElementById('importTipeAbsen');
    const type = sel.value;
    const text = sel.options[sel.selectedIndex].text;
    document.getElementById('downloadTemplateBtn').href = "{{ route('absensi.template_import') }}?tipe=" + type;
    document.getElementById('templateTypeName').textContent = text;
}
</script>
@endpush

@endsection
