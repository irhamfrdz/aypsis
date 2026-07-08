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
                <div>
                    <a href="{{ route('absensi.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Lihat Log Scan Absensi
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form action="{{ route('absensi.rekap') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Karyawan -->
                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-700 mb-1">Cari Karyawan / NIK</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Nama atau NIK..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                    </div>

                    <!-- Pekerjaan -->
                    <div>
                        <label for="pekerjaan" class="block text-xs font-semibold text-gray-700 mb-1">Pekerjaan</label>
                        <select name="pekerjaan" id="pekerjaan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            <option value="">Semua Pekerjaan</option>
                            @foreach($pekerjaans as $pekerjaan)
                                <option value="{{ $pekerjaan }}" {{ request('pekerjaan') == $pekerjaan ? 'selected' : '' }}>{{ $pekerjaan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bulan -->
                    <div>
                        <label for="month" class="block text-xs font-semibold text-gray-700 mb-1">Bulan</label>
                        <select name="month" id="month"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Tahun -->
                    <div>
                        <label for="year" class="block text-xs font-semibold text-gray-700 mb-1">Tahun</label>
                        <select name="year" id="year"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            @for($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg focus:outline-none transition-colors duration-200 h-[38px] shadow-sm">
                            Filter Rekap
                        </button>
                        @if(request()->anyFilled(['search', 'pekerjaan', 'month', 'year']))
                            <a href="{{ route('absensi.rekap') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-lg focus:outline-none transition-colors duration-200 h-[38px] shadow-sm">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">
                        Periode: {{ Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}
                    </h3>
                </div>
                <div>
                    <a href="{{ route('absensi.rekap.export', request()->all()) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 focus:outline-none transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Ekspor Excel
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 font-semibold text-gray-500 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-6 py-3 text-left">No</th>
                            <th class="px-6 py-3 text-left">NIK</th>
                            <th class="px-6 py-3 text-left">Nama Lengkap</th>
                             <th class="px-6 py-3 text-left">Pekerjaan</th>
                            <th class="px-6 py-3 text-center">Hadir</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-xs text-gray-900">
                        @forelse($karyawans as $index => $karyawan)
                            @php
                                $stats = $rekapData[$karyawan->id] ?? ['total_masuk' => 0, 'total_pulang' => 0, 'active_days' => 0];
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
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
                                    {{ $karyawan->pekerjaan ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                        {{ $stats['total_masuk'] }} Hari
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada data karyawan</h3>
                                        <p class="text-xs text-gray-500">Tidak ada karyawan yang terdaftar dengan filter ini.</p>
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
@endsection
