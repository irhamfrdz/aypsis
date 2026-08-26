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
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
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
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-8">
        
        <!-- Tabel Belum Absen -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
            <div class="bg-red-50 border-b border-red-100 px-4 py-3 flex justify-between items-center">
                <h3 class="font-semibold text-red-800 flex items-center">
                    <i class="fas fa-user-times mr-2 text-red-600"></i> Karyawan Belum Absen
                </h3>
                <span class="bg-red-200 text-red-800 text-xs font-bold px-2 py-1 rounded-full">{{ $karyawanBelumAbsen->count() }} Orang</span>
            </div>
            <div class="p-0 flex-1 max-h-96 overflow-y-auto">
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
            <div class="bg-green-50 border-b border-green-100 px-4 py-3 flex justify-between items-center">
                <h3 class="font-semibold text-green-800 flex items-center">
                    <i class="fas fa-check-circle mr-2 text-green-600"></i> Sudah Absen Masuk
                </h3>
                <span class="bg-green-200 text-green-800 text-xs font-bold px-2 py-1 rounded-full">{{ $absensiMasuk->count() }} Orang</span>
            </div>
            <div class="p-0 flex-1 max-h-96 overflow-y-auto">
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
            <div class="bg-yellow-50 border-b border-yellow-100 px-4 py-3 flex justify-between items-center">
                <h3 class="font-semibold text-yellow-800 flex items-center">
                    <i class="fas fa-running mr-2 text-yellow-600"></i> Belum Absen Pulang
                </h3>
                <span class="bg-yellow-200 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full">{{ $karyawanBelumAbsenPulang->count() }} Orang</span>
            </div>
            <div class="p-0 flex-1 max-h-96 overflow-y-auto">
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
        <div class="flex flex-col gap-8 h-full">
            
            <!-- Tabel Terlambat -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col flex-1 max-h-96">
                <div class="bg-orange-50 border-b border-orange-100 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-semibold text-orange-800 flex items-center">
                        <i class="fas fa-clock mr-2 text-orange-600"></i> Karyawan Terlambat (> {{ sprintf('%02d:00', $jamBatas) }})
                    </h3>
                    <span class="bg-orange-200 text-orange-800 text-xs font-bold px-2 py-1 rounded-full">{{ $karyawanTerlambat->count() }} Orang</span>
                </div>
                <div class="p-0 flex-1 overflow-y-auto">
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
                                <td class="px-4 py-2">
                                    <span class="text-orange-700 font-bold bg-orange-100 px-2 py-1 rounded">
                                        {{ \Carbon\Carbon::parse($absen->waktu)->format('H:i:s') }}
                                    </span>
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
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col flex-1 max-h-96">
                <div class="bg-purple-50 border-b border-purple-100 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-semibold text-purple-800 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-purple-600"></i> Karyawan Cuti & Izin
                    </h3>
                    <span class="bg-purple-200 text-purple-800 text-xs font-bold px-2 py-1 rounded-full">{{ $karyawanCuti->count() }} Permohonan</span>
                </div>
                <div class="p-0 flex-1 overflow-y-auto">
                    @if($karyawanCuti->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Nama</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Jenis</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($karyawanCuti as $cuti)
                            <tr class="hover:bg-purple-50/50">
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-800">
                                        @if($cuti->karyawan)
                                            <a href="{{ route('master.karyawan.show', $cuti->karyawan->id) }}" target="_blank" class="hover:text-indigo-600 transition-colors">{{ $cuti->karyawan->nama_lengkap }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-purple-700 font-semibold bg-purple-100 px-2 py-1 rounded text-xs">
                                        {{ $cuti->jenis_cuti ?: 'Cuti/Izin' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-600 text-xs italic">
                                    {{ Str::limit($cuti->keterangan ?? '-', 30) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-8 text-center text-gray-500 flex flex-col items-center justify-center h-full">
                        <i class="fas fa-info-circle text-4xl text-gray-300 mb-2"></i>
                        <p>Tidak ada karyawan yang cuti atau izin hari ini.</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
