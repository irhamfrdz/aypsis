@extends('layouts.app')

@section('title', 'Summary Tagihan Kontainer')
@section('page_title', 'Ringkasan Estimasi Hutang Sewa')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
            <div class="bg-gray-800 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-bold text-white">📊 Ringkasan Estimasi Hutang Sewa (Accrual)</h2>
                <a href="{{ route('container-trip.report.dashboard') }}" class="text-gray-300 hover:text-white text-sm font-medium">
                    &larr; Kembali ke Detail
                </a>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <p class="text-gray-600">Berikut adalah total dana yang harus disiapkan untuk tagihan yang BELUM DIBAYAR kepada vendor.</p>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Vendor</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Belum Tagih</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total DPP</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">PPN (11%)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">PPh 23 (2%)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-900 uppercase tracking-wider font-bold">Est. Netto (+Materai)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $grandTotal = 0; @endphp
                    @forelse($summary as $v)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $v['nama'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $v['jumlah_unit'] }} Unit</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ number_format($v['total_dpp']) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ number_format($v['total_ppn']) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-500">({{ number_format($v['total_pph']) }})</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900 bg-gray-50">Rp {{ number_format($v['total_netto']) }}</td>
                    </tr>
                    @php $grandTotal += $v['total_netto']; @endphp
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">Semua tagihan sudah lunas! Tidak ada estimasi hutang.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-100">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right text-base font-bold text-gray-900">TOTAL DANA YANG HARUS DISIAPKAN:</td>
                        <td class="px-6 py-4 text-right text-xl font-bold text-indigo-700">Rp {{ number_format($grandTotal) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
