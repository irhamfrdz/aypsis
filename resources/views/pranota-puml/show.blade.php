@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail PUML: {{ $puml->nomor_pranota }}</h1>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <a href="{{ route('pranota-puml.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                <i class="fas fa-print mr-2"></i> Cetak PUML
            </button>
        </div>
    </div>

    <!-- Informasi PUML -->
    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Tanggal Pranota</p>
                <p class="text-sm font-medium text-gray-800">{{ $puml->tanggal_pranota->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Total Uang Makan</p>
                <p class="text-sm font-medium text-gray-800">Rp {{ number_format($puml->total_uang_makan, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Total Lembur</p>
                <p class="text-sm font-medium text-gray-800">Rp {{ number_format($puml->total_lembur, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Grand Total</p>
                <p class="text-sm font-bold text-green-600">Rp {{ number_format($puml->grand_total, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Rekap per Karyawan -->
    <div class="bg-white shadow-lg rounded-sm border border-gray-200 mb-6">
        <header class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Rincian Penerimaan Karyawan</h2>
        </header>
        <div class="p-3">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                        <tr>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">NIK</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Nama Karyawan</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-right">Uang Makan</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-right">Uang Lembur</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-right">Total Terima</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-200">
                        @foreach($karyawanRekap as $rekap)
                            <tr>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800">{{ $rekap['karyawan']->nik ?? '-' }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800">{{ $rekap['karyawan']->nama_lengkap ?? 'Unknown' }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-right">
                                    <div class="font-medium text-gray-800">Rp {{ number_format($rekap['total_uang_makan'], 0, ',', '.') }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-right">
                                    <div class="font-medium text-gray-800">Rp {{ number_format($rekap['total_lembur'], 0, ',', '.') }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-right bg-green-50">
                                    <div class="font-bold text-green-700">Rp {{ number_format($rekap['total_uang_makan'] + $rekap['total_lembur'], 0, ',', '.') }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body { visibility: hidden; }
        .max-w-9xl { visibility: visible; position: absolute; left: 0; top: 0; width: 100%; }
        .btn, nav, header { display: none !important; }
    }
</style>
@endsection
