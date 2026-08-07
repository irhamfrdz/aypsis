@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto print-container">
    <!-- Header Section -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8 pb-4 border-b border-gray-100 no-print">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-extrabold tracking-tight">Detail PUML</h1>
            <p class="text-sm text-gray-500 mt-1">Nomor Dokumen: <span class="font-semibold text-indigo-600">{{ $puml->nomor_pranota }}</span></p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('pranota-puml.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <button onclick="window.print()" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-print mr-2"></i> Cetak PUML
            </button>
        </div>
    </div>

    <!-- Informasi PUML -->
    <div class="bg-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] rounded-xl border border-gray-100 p-6 mb-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 divide-x divide-gray-100">
            <div class="pl-2">
                <div class="flex items-center text-sm font-semibold text-gray-500 mb-2">
                    <i class="fas fa-calendar text-gray-400 mr-2"></i> Tanggal Pranota
                </div>
                <p class="text-lg font-bold text-gray-900">{{ $puml->tanggal_pranota->format('d F Y') }}</p>
            </div>
            <div class="pl-6">
                <div class="flex items-center text-sm font-semibold text-gray-500 mb-2">
                    <i class="fas fa-utensils text-emerald-400 mr-2"></i> Total Uang Makan
                </div>
                <p class="text-lg font-bold text-gray-900">Rp {{ number_format($puml->total_uang_makan, 0, ',', '.') }}</p>
            </div>
            <div class="pl-6">
                <div class="flex items-center text-sm font-semibold text-gray-500 mb-2">
                    <i class="fas fa-clock text-orange-400 mr-2"></i> Total Lembur
                </div>
                <p class="text-lg font-bold text-gray-900">Rp {{ number_format($puml->total_lembur, 0, ',', '.') }}</p>
            </div>
            <div class="pl-6 bg-gradient-to-r from-transparent to-indigo-50/30 rounded-r-xl">
                <div class="flex items-center text-sm font-semibold text-indigo-500 mb-2">
                    <i class="fas fa-wallet text-indigo-400 mr-2"></i> Grand Total
                </div>
                <p class="text-xl font-extrabold text-indigo-600">Rp {{ number_format($puml->grand_total, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Rekap per Karyawan -->
    <div class="bg-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] rounded-xl border border-gray-100 overflow-hidden mb-6">
        <header class="px-6 py-4 bg-gray-50/80 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">Rincian Penerimaan Karyawan</h2>
        </header>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">NIK</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Nama Karyawan</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Uang Makan</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Uang Lembur</th>
                        <th scope="col" class="px-6 py-4 font-bold text-right text-indigo-600">Total Terima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($karyawanRekap as $rekap)
                        <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-gray-600">{{ $rekap['karyawan']->nik ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $rekap['karyawan']->nama_lengkap ?? 'Unknown' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-gray-600 font-medium">Rp {{ number_format($rekap['total_uang_makan'], 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-gray-600 font-medium">Rp {{ number_format($rekap['total_lembur'], 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right bg-indigo-50/30">
                                <div class="font-extrabold text-indigo-700">Rp {{ number_format($rekap['total_uang_makan'] + $rekap['total_lembur'], 0, ',', '.') }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print-container, .print-container * {
            visibility: visible;
        }
        .print-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 15px;
        }
        .no-print {
            display: none !important;
        }
        .shadow-\[0_4px_20px_-4px_rgba\(0\,0\,0\,0\.05\)\] {
            box-shadow: none !important;
            border: 1px solid #e5e7eb !important;
        }
        .bg-indigo-50\/30 {
            background-color: transparent !important;
        }
    }
</style>
@endsection
