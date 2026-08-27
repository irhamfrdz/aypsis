@extends('layouts.app')
@section('title', 'Report Gaji Supir Batam')

@section('content')
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Report Gaji Supir Batam</h1>
        <p class="text-sm text-gray-500 mt-1">Laporan data gaji supir cabang Batam</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('report-gaji-supir-batam.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Supir</label>
                <select name="karyawan_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm">
                    <option value="">-- Semua Supir --</option>
                    @foreach($supirList as $supir)
                        <option value="{{ $supir->id }}" {{ $karyawanId == $supir->id ? 'selected' : '' }}>
                            {{ $supir->nama_lengkap }} {{ $supir->plat ? '('.$supir->plat.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full md:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="{{ route('report-gaji-supir-batam.index') }}" class="w-full md:w-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if(session('error'))
        <div class="p-4 bg-red-50 text-red-600 text-sm border-b border-red-100">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white">
        <h3 class="text-sm font-semibold text-gray-800">Hasil Laporan</h3>
        <a href="{{ route('report-gaji-supir-batam.export', request()->all()) }}" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 text-xs font-medium rounded-lg transition-colors">
            <i class="fas fa-file-excel mr-1"></i> Export Excel
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                    <th class="p-3 font-medium">Periode</th>
                    <th class="p-3 font-medium">Supir</th>
                    <th class="p-3 font-medium">Gaji Pokok</th>
                    <th class="p-3 font-medium">Bensin</th>
                    <th class="p-3 font-medium">Uang Malam/Libur</th>
                    <th class="p-3 font-medium text-red-600">Potongan 5%</th>
                    <th class="p-3 font-medium">Total Gaji</th>
                    <th class="p-3 font-medium text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-50">
                @php 
                    $sumGajiPokok = 0;
                    $sumBensin = 0;
                    $sumMalamLibur = 0;
                    $sumPotongan = 0;
                    $sumTotal = 0;
                @endphp
                @forelse($gajiList as $gaji)
                    @php
                        $sumGajiPokok += $gaji->gaji_pokok;
                        $sumBensin += $gaji->biaya_bensin;
                        $sumMalamLibur += $gaji->uang_malam_libur;
                        $sumPotongan += $gaji->nominal_potongan_5_persen;
                        $sumTotal += $gaji->total_gaji;
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-3 text-gray-600">{{ $gaji->periode_text }}</td>
                        <td class="p-3 font-medium text-gray-800">
                            {{ $gaji->karyawan->nama_lengkap ?? '-' }}
                            @if($gaji->karyawan && $gaji->karyawan->plat)
                                <span class="text-xs text-gray-500 block">{{ $gaji->karyawan->plat }}</span>
                            @endif
                        </td>
                        <td class="p-3 text-gray-600">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
                        <td class="p-3 text-gray-600">Rp {{ number_format($gaji->biaya_bensin, 0, ',', '.') }}</td>
                        <td class="p-3 text-gray-600">Rp {{ number_format($gaji->uang_malam_libur, 0, ',', '.') }}</td>
                        <td class="p-3 text-red-500">Rp {{ number_format($gaji->nominal_potongan_5_persen, 0, ',', '.') }}</td>
                        <td class="p-3 font-semibold text-emerald-600">Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</td>
                        <td class="p-3 text-center">
                            <a href="{{ route('gaji-supir-batam.show', $gaji->id) }}" target="_blank" class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-md transition-colors text-xs font-medium">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300 block"></i>
                            Tidak ada data gaji supir untuk periode ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($gajiList) > 0)
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr class="font-bold text-gray-800 text-sm">
                        <td colspan="2" class="p-3 text-right">TOTAL (Halaman Ini):</td>
                        <td class="p-3 text-gray-800">Rp {{ number_format($sumGajiPokok, 0, ',', '.') }}</td>
                        <td class="p-3 text-gray-800">Rp {{ number_format($sumBensin, 0, ',', '.') }}</td>
                        <td class="p-3 text-gray-800">Rp {{ number_format($sumMalamLibur, 0, ',', '.') }}</td>
                        <td class="p-3 text-red-600">Rp {{ number_format($sumPotongan, 0, ',', '.') }}</td>
                        <td class="p-3 text-emerald-600">Rp {{ number_format($sumTotal, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
    
    @if($gajiList->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $gajiList->links() }}
        </div>
    @endif
</div>
@endsection
