@extends('layouts.app')

@section('title', 'View Report Pranota Uang Jalan')
@section('page_title', 'View Report Pranota Uang Jalan')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header Section --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border-l-4 border-blue-600">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-50 rounded-lg mr-4">
                    <i class="fas fa-file-invoice-dollar text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Report Pranota Uang Jalan</h1>
                    <p class="text-sm text-gray-500 font-medium">
                        Periode: <span class="text-blue-600">{{ $startDate->format('d M Y') }}</span> s/d <span class="text-blue-600">{{ $endDate->format('d M Y') }}</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('report.pranota-uang-jalan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow hover:shadow-md transition duration-200">
                    <i class="fas fa-print mr-2"></i> Cetak Report
                </button>
                <a href="{{ route('report.pranota-uang-jalan.export', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'search' => $search]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg shadow hover:shadow-md transition duration-200">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            </div>
        </div>
    </div>

    {{-- Filter & Search Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <form method="GET" action="{{ route('report.pranota-uang-jalan.view') }}" class="flex flex-wrap items-end gap-4">
            <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
            <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
            
            <div class="flex-1 min-w-[250px]">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pencarian Cepat</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" 
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition duration-200" 
                           placeholder="Cari Nomor Pranota, Periode, atau Catatan...">
                </div>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition duration-200">
                Filter Data
            </button>
            @if($search)
                <a href="{{ route('report.pranota-uang-jalan.view', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
                   class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-lg transition duration-200">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">No</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Tanggal / No Pranota</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Periode Tagihan</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest text-blue-600 font-bold">Uang Jalan</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Penyesuaian</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest font-bold text-blue-600">Total Akhir</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Dibuat Oleh</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($pranotas as $index => $pranota)
                        <tr class="hover:bg-blue-50/30 transition duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">{{ $index + 1 }}</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-800">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-400 font-medium">{{ $pranota->nomor_pranota }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $pranota->periode_tagihan }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">
                                {{ number_format($pranota->jumlah_uang_jalan, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="font-medium {{ $pranota->penyesuaian < 0 ? 'text-red-500' : ($pranota->penyesuaian > 0 ? 'text-green-500' : '') }}">
                                    {{ number_format($pranota->penyesuaian, 0, ',', '.') }}
                                </div>
                                @if($pranota->keterangan_penyesuaian)
                                    <div class="text-[10px] italic">{{ $pranota->keterangan_penyesuaian }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-bold text-blue-600">
                                {{ number_format($pranota->total_with_penyesuaian, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-bold rounded-full {{ $pranota->status_badge }}">
                                    {{ $pranota->status_text }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-500">{{ $pranota->creator->name ?? '-' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="p-4 bg-gray-50 rounded-full mb-3">
                                        <i class="fas fa-folder-open text-gray-300 text-4xl"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-400">Tidak ada data ditemukan</h3>
                                    <p class="text-sm text-gray-300">Coba ubah filter atau rentang tanggal Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($pranotas->count() > 0)
                <tfoot class="bg-gray-50 border-t-2 border-gray-100">
                    <tr>
                        <th colspan="3" class="px-4 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Summary Total</th>
                        <th class="px-4 py-4 text-left text-sm font-bold text-gray-700">
                            {{ number_format($pranotas->sum('jumlah_uang_jalan'), 0, ',', '.') }}
                        </th>
                        <th class="px-4 py-4 text-left text-sm font-bold text-gray-700">
                            {{ number_format($pranotas->sum('penyesuaian'), 0, ',', '.') }}
                        </th>
                        <th class="px-4 py-4 text-right text-sm font-bold text-blue-600 bg-blue-50">
                            {{ number_format($pranotas->sum('total_with_penyesuaian'), 0, ',', '.') }}
                        </th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        body { background-color: white !important; }
        .flex.flex-1 { margin-left: 0 !important; }
        aside, nav, header, .no-print, button, a[href="{{ route('report.pranota-uang-jalan.index') }}"], form { display: none !important; }
        .container { width: 100% !important; max-width: none !important; padding: 0 !important; }
        .bg-white { box-shadow: none !important; border: none !important; }
        .rounded-xl { border-radius: 0 !important; }
        table { border-collapse: collapse !important; width: 100% !important; }
        th, td { border: 1px solid #e5e7eb !important; padding: 8px !important; }
        .bg-gray-50 { background-color: #f9fafb !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
