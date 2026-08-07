@extends('layouts.app')

@section('title', 'Detail Pranota Lembur Karyawan')
@section('page_title', 'Detail Pranota Lembur Karyawan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('pranota-lembur-karyawan.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900">Detail Pranota Lembur</h1>
                    </div>
                    <p class="mt-1 text-sm text-gray-600 ml-8">Melihat rincian karyawan pada pranota lembur tertentu.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200" onclick="window.print()">
                        <i class="fas fa-print mr-2"></i>
                        Cetak
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="text-sm font-medium text-gray-500 mb-1">Nomor Pranota</div>
                <div class="text-xl font-bold text-blue-600 font-mono">{{ $pranota->nomor_pranota }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="text-sm font-medium text-gray-500 mb-1">Tanggal Pranota</div>
                <div class="text-xl font-bold text-gray-900">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="text-sm font-medium text-gray-500 mb-1">Total Karyawan</div>
                <div class="text-xl font-bold text-gray-900">{{ $pranota->karyawans->count() }} Orang</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="text-sm font-medium text-gray-500 mb-1">Total Nominal Akhir</div>
                <div class="text-xl font-bold text-emerald-600">Rp {{ number_format($pranota->total_setelah_adjustment, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Rincian Karyawan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Karyawan</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Jam Lembur</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Nominal Awal</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Adjustment</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Akhir</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pranota->karyawans as $detail)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $detail->karyawan->nama_lengkap ?? 'Karyawan tidak ditemukan' }}</div>
                                <div class="text-xs text-gray-500">{{ $detail->karyawan->nik ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $detail->jam_lembur }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                Rp {{ number_format($detail->nominal_awal, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-orange-500">
                                Rp {{ number_format($detail->adjustment, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-emerald-600">
                                Rp {{ number_format($detail->total_akhir, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $detail->catatan ?: '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-900">TOTAL KESELURUHAN</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">Rp {{ number_format($pranota->total_biaya, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-orange-600">Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-emerald-700">Rp {{ number_format($pranota->total_setelah_adjustment, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
