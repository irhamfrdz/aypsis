@extends('layouts.app')

@section('title', 'Dashboard HRD')
@section('page_title', 'Dashboard HRD')

@section('content')
<div class="space-y-8">
    <!-- Welcome Message & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Dashboard HRD</h2>
            <p class="text-gray-500">Ringkasan aktivitas kehadiran karyawan pada <strong>{{ $filterDate->translatedFormat('l, d F Y') }}</strong>.</p>
        </div>
        
        <div class="flex items-center gap-3">
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
            
            <button onclick="openExportModal()" class="px-4 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors flex items-center shadow-sm">
                <i class="fas fa-file-excel mr-2"></i> Rekap Absen
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        <!-- Total Karyawan Aktif -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-2xl text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Karyawan Aktif</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalKaryawanAktif) }}</p>
            </div>
        </div>

        <!-- Belum Absen -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-times text-2xl text-red-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Belum Absen Masuk</p>
                <p class="text-3xl font-bold text-red-600">{{ number_format($karyawanBelumAbsen->count()) }}</p>
            </div>
        </div>

        <!-- Belum Absen Pulang -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-running text-2xl text-yellow-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Belum Absen Pulang</p>
                <p class="text-3xl font-bold text-yellow-600">{{ number_format($karyawanBelumAbsenPulang->count()) }}</p>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-2xl text-orange-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Absen Terlambat</p>
                <p class="text-3xl font-bold text-orange-600">{{ number_format($karyawanTerlambat->count()) }}</p>
            </div>
        </div>

        <!-- Cuti / Izin -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-alt text-2xl text-purple-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Cuti & Izin Berjalan</p>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($karyawanCuti->count()) }}</p>
            </div>
        </div>

        <!-- Luar Radius -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-map-marker-alt text-2xl text-red-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Absen Luar Radius</p>
                <p class="text-3xl font-bold text-red-600">{{ number_format($absensiLuarRadius->count()) }}</p>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        
        <!-- Tabel Belum Absen -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[400px]">
            <div class="bg-red-50 border-b border-red-100 px-4 py-3 flex justify-between items-center">
                <h3 class="font-semibold text-red-800 flex items-center">
                    <i class="fas fa-user-times mr-2 text-red-600"></i> Karyawan Belum Absen
                </h3>
                <span class="bg-red-200 text-red-800 text-xs font-bold px-2 py-1 rounded-full">{{ $karyawanBelumAbsen->count() }} Orang</span>
            </div>
            <div class="p-0 flex-1 overflow-y-auto min-h-0">
                @if($karyawanBelumAbsen->count() > 0)
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">NIK</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Nama</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Divisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($karyawanBelumAbsen as $k)
                        <tr class="hover:bg-red-50/50">
                            <td class="px-4 py-2 text-gray-600">{{ $k->nik }}</td>
                            <td class="px-4 py-2 font-medium text-gray-800">
                                <a href="{{ route('master.karyawan.show', $k->id) }}" target="_blank" class="hover:text-indigo-600 transition-colors">{{ $k->nama_lengkap }}</a>
                            </td>
                            <td class="px-4 py-2 text-gray-500 text-xs uppercase">{{ $k->divisi ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-8 text-center text-gray-500 flex flex-col items-center justify-center h-full">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                    <p>Semua karyawan telah melakukan absensi.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Tabel Sudah Absen Masuk -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[400px]">
            <div class="bg-green-50 border-b border-green-100 px-4 py-3 flex justify-between items-center">
                <h3 class="font-semibold text-green-800 flex items-center">
                    <i class="fas fa-check-circle mr-2 text-green-600"></i> Sudah Absen Masuk
                </h3>
                <span class="bg-green-200 text-green-800 text-xs font-bold px-2 py-1 rounded-full">{{ $absensiMasuk->count() }} Orang</span>
            </div>
            <div class="p-0 flex-1 overflow-y-auto min-h-0">
                @if($absensiMasuk->count() > 0)
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">NIK</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Nama</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Jam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($absensiMasuk->sortByDesc('waktu') as $absen)
                        <tr class="hover:bg-green-50/50">
                            <td class="px-4 py-2 text-gray-600">{{ $absen->karyawan->nik ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <div class="font-medium text-gray-800">
                                    @if($absen->karyawan)
                                        <a href="{{ route('master.karyawan.show', $absen->karyawan->id) }}" target="_blank" class="hover:text-indigo-600 transition-colors">{{ $absen->karyawan->nama_lengkap }}</a>
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">{{ $absen->karyawan->divisi ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-2 font-semibold text-green-700">
                                {{ \Carbon\Carbon::parse($absen->waktu)->format('H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-8 text-center text-gray-500 flex flex-col items-center justify-center h-full">
                    <i class="fas fa-info-circle text-4xl text-gray-300 mb-2"></i>
                    <p>Belum ada karyawan yang absen masuk.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Tabel Belum Absen Pulang -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[500px]">
            <div class="bg-yellow-50 border-b border-yellow-100 px-4 py-3 flex justify-between items-center">
                <h3 class="font-semibold text-yellow-800 flex items-center">
                    <i class="fas fa-running mr-2 text-yellow-600"></i> Belum Absen Pulang
                </h3>
                <span class="bg-yellow-200 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full">{{ $karyawanBelumAbsenPulang->count() }} Orang</span>
            </div>
            <div class="p-0 flex-1 overflow-y-auto min-h-0">
                @if($karyawanBelumAbsenPulang->count() > 0)
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">NIK</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Nama</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($karyawanBelumAbsenPulang as $k)
                        <tr class="hover:bg-yellow-50/50">
                            <td class="px-4 py-2 text-gray-600">{{ $k->nik }}</td>
                            <td class="px-4 py-2">
                                <div class="font-medium text-gray-800">
                                    <a href="{{ route('master.karyawan.show', $k->id) }}" target="_blank" class="hover:text-indigo-600 transition-colors">{{ $k->nama_lengkap }}</a>
                                </div>
                                <div class="text-xs text-gray-500">{{ $k->divisi ?: '-' }}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-8 text-center text-gray-500 flex flex-col items-center justify-center h-full">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                    <p>Semua karyawan (yang absen masuk) telah absen pulang.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Container for Terlambat & Cuti (Stacked) -->
        <div class="flex flex-col gap-8 h-[500px]">
            
            <!-- Tabel Terlambat -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col flex-1 min-h-0">
                <div class="bg-orange-50 border-b border-orange-100 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-semibold text-orange-800 flex items-center">
                        <i class="fas fa-clock mr-2 text-orange-600"></i> Karyawan Terlambat ( {{ sprintf('%02d:00', $jamBatas) }})
                    </h3>
                    <span class="bg-orange-200 text-orange-800 text-xs font-bold px-2 py-1 rounded-full">{{ $karyawanTerlambat->count() }} Orang</span>
                </div>
                <div class="p-0 flex-1 overflow-y-auto min-h-0">
                    @if($karyawanTerlambat->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Nama</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Waktu Absen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($karyawanTerlambat as $absen)
                            <tr class="hover:bg-orange-50/50">
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-800">
                                        @if($absen->karyawan)
                                            <a href="{{ route('master.karyawan.show', $absen->karyawan->id) }}" target="_blank" class="hover:text-indigo-600 transition-colors">{{ $absen->karyawan->nama_lengkap }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $absen->karyawan->divisi ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-2 font-semibold text-orange-700">
                                    {{ \Carbon\Carbon::parse($absen->waktu)->format('H:i') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-8 text-center text-gray-500 flex flex-col items-center justify-center h-full">
                        <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                        <p>Tidak ada karyawan yang terlambat.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tabel Cuti / Izin -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col flex-1 min-h-0">
                <div class="bg-purple-50 border-b border-purple-100 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-semibold text-purple-800 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-purple-600"></i> Karyawan Cuti / Izin
                    </h3>
                    <span class="bg-purple-200 text-purple-800 text-xs font-bold px-2 py-1 rounded-full">{{ $karyawanCuti->count() }} Orang</span>
                </div>
                <div class="p-0 flex-1 overflow-y-auto min-h-0">
                    @if($karyawanCuti->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Nama</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($karyawanCuti as $cuti)
                            <tr class="hover:bg-purple-50/50">
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-800">
                                        <a href="{{ route('master.karyawan.show', $cuti->karyawan->id) }}" target="_blank" class="hover:text-indigo-600 transition-colors">{{ $cuti->karyawan->nama_lengkap }}</a>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $cuti->karyawan->divisi ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-2 text-gray-600">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $cuti->jenis_cuti }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-8 text-center text-gray-500 flex flex-col items-center justify-center h-full">
                        <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                        <p>Tidak ada karyawan yang sedang cuti atau izin.</p>
                    </div>
                    @endif
                </div>
            </div>
            
        </div>
        
        <!-- Tabel Absen Luar Radius -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[400px] col-span-1 xl:col-span-2">
            <div class="bg-red-50 border-b border-red-100 px-4 py-3 flex justify-between items-center">
                <h3 class="font-semibold text-red-800 flex items-center">
                    <i class="fas fa-map-marker-alt mr-2 text-red-600"></i> Karyawan Absen Luar Radius
                </h3>
                <span class="bg-red-200 text-red-800 text-xs font-bold px-2 py-1 rounded-full">{{ $absensiLuarRadius->count() }} Orang</span>
            </div>
            <div class="p-0 flex-1 overflow-y-auto min-h-0">
                @if($absensiLuarRadius->count() > 0)
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Nama</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Waktu</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Detail Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($absensiLuarRadius as $absen)
                        <tr class="hover:bg-red-50/50">
                            <td class="px-4 py-2">
                                <div class="font-medium text-gray-800">
                                    @if($absen->karyawan)
                                        <a href="{{ route('master.karyawan.show', $absen->karyawan->id) }}" target="_blank" class="hover:text-indigo-600 transition-colors">{{ $absen->karyawan->nama_lengkap }}</a>
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 font-semibold text-red-700">
                                {{ \Carbon\Carbon::parse($absen->waktu)->format('H:i') }}
                                <span class="text-xs font-normal text-gray-500 block">{{ $absen->tipe }}</span>
                            </td>
                            <td class="px-4 py-2 text-gray-600 text-xs">
                                {{ $absen->detail_lokasi }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-8 text-center text-gray-500 flex flex-col items-center justify-center h-full">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                    <p>Tidak ada karyawan yang absen di luar radius.</p>
                </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Attendance Trends Chart --></div>

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

<script>
    function openExportModal() {
        document.getElementById('exportModal').classList.remove('hidden');
    }
    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }
</script>
@endsection
