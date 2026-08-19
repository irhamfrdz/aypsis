@extends('layouts.app')

@section('title', 'Detail Absensi')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Absensi</h1>
            <p class="text-gray-500 mt-1">Informasi lengkap absensi harian karyawan</p>
        </div>
        <a href="{{ route('absensi.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <!-- Informasi Karyawan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-2xl">
                    {{ strtoupper(substr($karyawan->nama_lengkap, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $karyawan->nama_lengkap }}</h2>
                    <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                            NIK: {{ $karyawan->nik }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            {{ $karyawan->pekerjaan ?: '-' }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            {{ $karyawan->divisi ?: '-' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 font-semibold mb-1">Tanggal Absensi</p>
                <p class="text-lg font-bold text-gray-800 bg-gray-100 px-4 py-2 rounded-lg inline-block">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Timeline Absensi -->
    <h3 class="text-lg font-bold text-gray-800 mb-4">Log Absensi (Timeline)</h3>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        @if($absensis->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 font-semibold text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">Waktu</th>
                        <th class="px-6 py-4 text-left">Tipe</th>
                        <th class="px-6 py-4 text-left">Lokasi</th>
                        <th class="px-6 py-4 text-left">Perangkat / Mesin</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-center">Foto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($absensis as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($log->waktu)->format('H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @php
                                $tipeLower = strtolower($log->tipe);
                                $colorClass = 'bg-gray-100 text-gray-800';
                                if (in_array($tipeLower, ['masuk', 'check in'])) $colorClass = 'bg-green-100 text-green-800';
                                elseif (in_array($tipeLower, ['pulang', 'keluar', 'check out'])) $colorClass = 'bg-red-100 text-red-800';
                                elseif (str_contains($tipeLower, 'istirahat')) $colorClass = 'bg-orange-100 text-orange-800';
                                elseif (str_contains($tipeLower, 'lembur')) $colorClass = 'bg-purple-100 text-purple-800';
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-bold uppercase {{ $colorClass }}">
                                {{ $log->tipe }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $log->detail_lokasi ?: '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($log->mesin_id && $mesins->has($log->mesin_id))
                                <span class="font-medium text-gray-800">{{ $mesins->get($log->mesin_id)->nama_mesin }}</span>
                            @else
                                {{ $log->device ?: 'SISTEM PWA' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $log->status ?: '-' }}
                            @if($log->keterangan)
                                <div class="text-xs text-gray-400 mt-1">{{ $log->keterangan }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($log->foto)
                                <a href="{{ asset(ltrim($log->foto, '/')) }}" target="_blank" class="inline-block relative group">
                                    <img src="{{ asset(ltrim($log->foto, '/')) }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 hover:border-indigo-400 hover:scale-110 transition-all shadow-sm">
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-gray-500 font-medium">Tidak ada data absensi untuk tanggal ini.</p>
        </div>
        @endif
    </div>
</div>
@endsection
