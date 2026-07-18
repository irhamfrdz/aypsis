@extends('layouts.app')

@section('title', 'Data Absensi')
@section('page_title', 'Data Absensi')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Data Absensi</h1>
                    <p class="mt-1 text-sm text-gray-600">Daftar log absensi karyawan hasil sinkronisasi mesin fingerprint</p>
                </div>
                <div>
                    <a href="{{ route('absensi.rekap') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form action="{{ route('absensi.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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

                    <!-- Dari Tanggal -->
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                    </div>

                    <!-- Hingga Tanggal -->
                    <div>
                        <label for="end_date" class="block text-xs font-semibold text-gray-700 mb-1">Hingga Tanggal</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg focus:outline-none transition-colors duration-200 shadow-sm">
                        Filter Data
                    </button>
                    @if(request()->anyFilled(['search', 'pekerjaan', 'start_date', 'end_date']))
                        <a href="{{ route('absensi.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-lg focus:outline-none transition-colors duration-200 shadow-sm">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 font-semibold text-gray-500 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-6 py-3 text-left">No</th>
                            <th class="px-6 py-3 text-left">NIK</th>
                            <th class="px-6 py-3 text-left">Nama Lengkap</th>
                            <th class="px-6 py-3 text-left">Pekerjaan</th>
                            <th class="px-6 py-3 text-left">Tanggal</th>
                            <th class="px-6 py-3 text-center text-green-700 bg-green-50/50">Jam Masuk</th>
                            <th class="px-6 py-3 text-center text-orange-700 bg-orange-50/50">Istirahat Keluar</th>
                            <th class="px-6 py-3 text-center text-orange-700 bg-orange-50/50">Istirahat Masuk</th>
                            <th class="px-6 py-3 text-center text-red-700 bg-red-50/50">Jam Pulang</th>
                            <th class="px-6 py-3 text-left">Perangkat (IN / OUT)</th>
                            <th class="px-6 py-3 text-left">Detail Lokasi</th>
                            <th class="px-6 py-3 text-center">Foto</th>
                            <th class="px-6 py-3 text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-xs text-gray-900">
                        @forelse($absensis as $index => $absensi)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-500">
                                    {{ $absensis->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold font-mono text-indigo-600">
                                    {{ $absensi->nik }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    {{ $absensi->karyawan ? $absensi->karyawan->nama_lengkap : 'Karyawan Tidak Terdaftar' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ $absensi->karyawan ? $absensi->karyawan->pekerjaan : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-600">
                                    {{ Carbon\Carbon::parse($absensi->tanggal)->format('d-m-Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-mono font-bold text-green-600 bg-green-50/20 group relative" title="Diterima Server: {{ $absensi->server_received_masuk ? Carbon\Carbon::parse($absensi->server_received_masuk)->format('H:i:s') : '-' }}&#10;Diproses Server: {{ $absensi->server_processed_masuk ? Carbon\Carbon::parse($absensi->server_processed_masuk)->format('H:i:s') : '-' }}">
                                    {{ $absensi->waktu_masuk ? Carbon\Carbon::parse($absensi->waktu_masuk)->format('H:i:s') : '-' }}
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
                                <td class="px-6 py-4 whitespace-nowrap text-center font-mono font-bold text-orange-600 bg-orange-50/20">
                                    {{ $absensi->waktu_istirahat_keluar ? Carbon\Carbon::parse($absensi->waktu_istirahat_keluar)->format('H:i:s') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-mono font-bold text-orange-600 bg-orange-50/20">
                                    {{ $absensi->waktu_istirahat_masuk ? Carbon\Carbon::parse($absensi->waktu_istirahat_masuk)->format('H:i:s') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-mono font-bold text-red-600 bg-red-50/20 group relative" title="Diterima Server: {{ $absensi->server_received_pulang ? Carbon\Carbon::parse($absensi->server_received_pulang)->format('H:i:s') : '-' }}&#10;Diproses Server: {{ $absensi->server_processed_pulang ? Carbon\Carbon::parse($absensi->server_processed_pulang)->format('H:i:s') : '-' }}">
                                    {{ $absensi->waktu_pulang ? Carbon\Carbon::parse($absensi->waktu_pulang)->format('H:i:s') : '-' }}
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
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    <div class="space-y-1 text-[11px]">
                                        <div><span class="text-[9px] font-extrabold uppercase text-green-600 bg-green-100/80 px-1 py-0.5 rounded mr-1">IN</span>{{ $absensi->mesin_id_masuk && $mesins->get($absensi->mesin_id_masuk) ? $mesins->get($absensi->mesin_id_masuk)->nama_mesin : ($absensi->device_masuk ?: '-') }}</div>
                                        <div><span class="text-[9px] font-extrabold uppercase text-red-600 bg-red-100/80 px-1 py-0.5 rounded mr-1">OUT</span>{{ $absensi->mesin_id_pulang && $mesins->get($absensi->mesin_id_pulang) ? $mesins->get($absensi->mesin_id_pulang)->nama_mesin : ($absensi->device_pulang ?: '-') }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 max-w-xs text-[11px]">
                                    <div class="space-y-1">
                                        <div class="truncate" title="{{ $absensi->lokasi_masuk }}"><span class="text-[9px] font-extrabold uppercase text-green-600 bg-green-100/80 px-1 py-0.5 rounded mr-1">IN</span>{{ $absensi->lokasi_masuk ?: '-' }}</div>
                                        <div class="truncate" title="{{ $absensi->lokasi_pulang }}"><span class="text-[9px] font-extrabold uppercase text-red-600 bg-red-100/80 px-1 py-0.5 rounded mr-1">OUT</span>{{ $absensi->lokasi_pulang ?: '-' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($absensi->foto_masuk)
                                            <a href="{{ request()->getScheme() }}://{{ request()->getHost() }}:8085{{ $absensi->foto_masuk }}" target="_blank" class="relative inline-block group" title="Foto Masuk">
                                                <img src="{{ request()->getScheme() }}://{{ request()->getHost() }}:8085{{ $absensi->foto_masuk }}" class="w-8 h-8 object-cover rounded border border-gray-200 hover:scale-110 transition-transform duration-150">
                                                <span class="absolute -bottom-1 -right-1 text-[8px] bg-green-600 text-white font-bold px-0.5 rounded shadow">IN</span>
                                            </a>
                                        @endif
                                        @if($absensi->foto_pulang)
                                            <a href="{{ request()->getScheme() }}://{{ request()->getHost() }}:8085{{ $absensi->foto_pulang }}" target="_blank" class="relative inline-block group" title="Foto Pulang">
                                                <img src="{{ request()->getScheme() }}://{{ request()->getHost() }}:8085{{ $absensi->foto_pulang }}" class="w-8 h-8 object-cover rounded border border-gray-200 hover:scale-110 transition-transform duration-150">
                                                <span class="absolute -bottom-1 -right-1 text-[8px] bg-red-600 text-white font-bold px-0.5 rounded shadow">OUT</span>
                                            </a>
                                        @endif
                                        @if(!$absensi->foto_masuk && !$absensi->foto_pulang)
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500 max-w-xs text-[11px]">
                                    <div class="space-y-1">
                                        <div class="truncate" title="{{ $absensi->keterangan_masuk }}"><span class="text-[9px] font-extrabold text-green-600 mr-1">IN:</span>{{ $absensi->keterangan_masuk ?: '-' }}</div>
                                        <div class="truncate" title="{{ $absensi->keterangan_pulang }}"><span class="text-[9px] font-extrabold text-red-600 mr-1">OUT:</span>{{ $absensi->keterangan_pulang ?: '-' }}</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-10 text-center">
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
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $absensis->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
