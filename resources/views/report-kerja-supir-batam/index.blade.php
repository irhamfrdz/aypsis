@extends('layouts.app')

@section('title', 'Report Kerja Supir Batam')
@section('page_title', 'Report Kerja Supir Batam')

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #print-area, #print-area * {
            visibility: visible;
        }
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6 no-print">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Filter Report</h3>
            <p class="text-xs text-gray-500 mt-1">Filter berdasarkan tanggal dan supir untuk melihat rincian pekerjaan.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('report-kerja-supir-batam.index') }}" class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div>
                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
            </div>
            <div>
                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai <span class="text-red-500">*</span></label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
            </div>
            <div>
                <label for="karyawan_id" class="block text-sm font-semibold text-gray-700 mb-2">Supir (Opsional)</label>
                <select name="karyawan_id" id="karyawan_id" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Semua Supir</option>
                    @foreach($supirList as $s)
                        <option value="{{ $s->id }}" {{ $karyawanId == $s->id ? 'selected' : '' }}>
                            {{ $s->nama_lengkap }} ({{ $s->nama_panggilan ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-filter mr-2"></i> Tampilkan
                </button>
                <a href="{{ route('report-kerja-supir-batam.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
        </div>
    </form>
</div>

@if($startDate && $endDate)
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" id="print-area">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Hasil Report Kerja Supir</h3>
            <p class="text-xs text-gray-500 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div class="no-print flex space-x-2">
            <a href="{{ route('report-kerja-supir-batam.export', request()->query()) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <i class="fas fa-print mr-1"></i> Cetak / PDF
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th scope="col" class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">NIK</th>
                    <th scope="col" class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Supir</th>
                    <th scope="col" class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Tipe</th>
                    <th scope="col" class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Tujuan</th>
                    <th scope="col" class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">No. Dokumen / No. Kontainer</th>
                    <th scope="col" class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider">Uang Jalan / Biaya</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($waybills as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $item['tanggal'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $item['nik'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 font-medium">{{ $item['supir'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $item['tipe'] == 'Langsir Batam' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $item['tipe'] == 'SJ Reguler' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $item['tipe'] == 'SJ Bongkaran' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $item['tipe'] == 'SJ Tarik Kosong' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ $item['tipe'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-800 text-sm">
                            {{ $item['tujuan'] }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <div><span class="font-medium text-gray-800">{{ $item['no_dokumen'] }}</span></div>
                            <div class="text-xs text-gray-400 mt-0.5">Kontainer: {{ $item['no_kontainer'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-gray-900">
                            Rp {{ number_format($item['uang_jalan'], 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-3 text-gray-300"></i>
                            <p>Tidak ada data pekerjaan ditemukan pada rentang tanggal tersebut.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 font-bold border-t border-gray-200">
                <tr>
                    <td colspan="7" class="px-6 py-4 text-right text-gray-800 uppercase tracking-wider text-xs">Total Pendapatan Supir:</td>
                    <td class="px-6 py-4 text-right text-indigo-700 text-base">Rp {{ number_format($totalRit, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@elseif($startDate || $endDate)
<div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Perhatian</h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>Silakan isi kolom Tanggal Mulai dan Tanggal Selesai untuk menampilkan laporan.</p>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
