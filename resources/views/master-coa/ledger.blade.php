@extends('layouts.app')

@section('title', 'Buku Besar - ' . $coa->nama_akun)
@section('page_title', 'Buku Besar - ' . $coa->nama_akun)

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Buku Besar (Ledger)</h1>
                    <div class="mt-2 space-y-1">
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Nomor Akun:</span> {{ $coa->nomor_akun }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Nama Akun:</span> {{ $coa->nama_akun }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Tipe Akun:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $coa->tipe_akun }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('master-coa-index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Saldo Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-500 uppercase">Saldo Awal</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">
                    Rp {{ number_format($saldoAwal, 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-500 uppercase">Total Debit</p>
                <p class="mt-2 text-2xl font-bold text-green-600">
                    Rp {{ number_format($totalDebit, 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-500 uppercase">Total Kredit</p>
                <p class="mt-2 text-2xl font-bold text-red-600">
                    Rp {{ number_format($totalKredit, 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-blue-200 p-4 bg-blue-50">
                <p class="text-xs font-medium text-blue-700 uppercase">Saldo Akhir</p>
                <p class="mt-2 text-2xl font-bold text-blue-900">
                    Rp {{ number_format($coa->saldo, 2, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('master-coa-ledger', $coa) }}" class="flex items-center space-x-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="border border-gray-300 rounded-md text-sm px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="border border-gray-300 rounded-md text-sm px-3 py-2">
                </div>
                <div class="pt-5">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                </div>
                @if(request('dari_tanggal') || request('sampai_tanggal'))
                    <div class="pt-5">
                        <a href="{{ route('master-coa-ledger', $coa) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Ledger Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Transaksi</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No. Referensi
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jenis Transaksi
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keterangan
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Debit
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kredit
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Saldo
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Row untuk saldo awal -->
                        <tr class="bg-yellow-50">
                            <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900">
                                <strong>Saldo Awal</strong>
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-900">-</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-900">-</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">
                                Rp {{ number_format($saldoAwal, 2, ',', '.') }}
                            </td>
                        </tr>

                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $transaction->tanggal_transaksi->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-indigo-600">
                                    {{ $transaction->nomor_referensi }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $transaction->jenis_transaksi }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $transaction->keterangan ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold text-green-600">
                                    @if($transaction->debit > 0)
                                        Rp {{ number_format($transaction->debit, 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold text-red-600">
                                    @if($transaction->kredit > 0)
                                        Rp {{ number_format($transaction->kredit, 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                    Rp {{ number_format($transaction->saldo, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                                    Tidak ada transaksi untuk periode ini
                                </td>
                            </tr>
                        @endforelse

                        <!-- Summary Row -->
                        @if($transactions->count() > 0)
                            <tr class="bg-gray-100 font-semibold">
                                <td colspan="4" class="px-4 py-3 text-sm text-gray-900">
                                    <strong>Total</strong>
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-green-700">
                                    Rp {{ number_format($totalDebit, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-red-700">
                                    Rp {{ number_format($totalKredit, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-blue-900">
                                    Rp {{ number_format($coa->saldo, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
