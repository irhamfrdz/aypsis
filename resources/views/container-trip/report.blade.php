@extends('layouts.app')

@section('title', 'Laporan Tagihan Kontainer')
@section('page_title', 'Laporan Tagihan & Prediksi')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Import Button -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Laporan Tagihan Kontainer</h1>
                    <p class="text-teal-100 text-sm">Prediksi tagihan, kontrol pajak, dan status pembayaran</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('container-trip.report.create') }}" class="bg-white text-teal-700 px-4 py-2 rounded shadow hover:bg-gray-100 font-bold">
                        + Input Kontainer Baru
                    </a>
                    <a href="{{ route('container-trip.report.summary') }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 font-bold">
                        Lihat Summary
                    </a>
                </div>
            </div>
            
            <!-- Optional Import Section Mockup -->
            <!-- 
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <form action="#" method="POST" enctype="multipart/form-data" class="flex items-center space-x-4">
                    @csrf
                    <label class="font-semibold text-gray-700">Impor Excel:</label>
                    <input type="file" name="file_excel" class="border rounded p-1">
                    <button type="button" class="bg-green-600 text-white px-3 py-1 rounded shadow" disabled title="Fitur belum aktif">Unggah</button>
                </form>
            </div>
            -->
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Main Report Table -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi DPP</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pajak (PPN+PPh)</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Netto (+Materai)</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status / Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($data_laporan as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row['vendor'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">{{ $row['no_kontainer'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row['periode'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row['hari'] }} hari</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">Rp {{ number_format($row['dpp']) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                    <div class="text-xs">PPN: {{ number_format($row['ppn']) }}</div>
                                    <div class="text-xs text-red-500">PPh: ({{ number_format($row['pph23']) }})</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900">
                                    Rp {{ number_format($row['total']) }}
                                    @if($row['materai'] > 0)
                                        <span class="block text-xs text-gray-400 font-normal">+Materai</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($row['status'] == 'LUNAS')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            LUNAS ({{ $row['no_invoice'] }})
                                        </span>
                                    @else
                                        <form action="{{ route('bayar.tagihan') }}" method="POST" class="flex flex-col space-y-2 items-center" onsubmit="return confirm('Tandai invoice ini sebagai LUNAS?')">
                                            @csrf
                                            <input type="hidden" name="container_trip_id" value="{{ $row['id_trip'] }}">
                                            <input type="hidden" name="periode_bulan" value="{{ $row['periode'] }}">
                                            
                                            <input type="text" name="no_invoice" placeholder="No Invoice" required class="text-xs border border-gray-300 rounded px-2 py-1 w-24 focus:ring-red-500 focus:border-red-500">
                                            
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition">
                                                Bayar
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                    Belum ada data perjalanan kontainer. 
                                    <a href="{{ route('container-trip.report.create') }}" class="text-indigo-600 hover:text-indigo-900">Input Baru</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
