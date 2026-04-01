@extends('layouts.app')

@section('title', 'Laporan Kas Truck')
@section('page_title', 'Laporan Kas Truck')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header with Back Button Option -->
        <div class="p-4 border-b border-gray-200">
            <a href="{{ route('report.kas-truck.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Pilih Periode
            </a>
        </div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Laporan Kas Truck</h1>
                <p class="text-xs text-gray-600 mt-1">Mutasi Transaksi Rekening: <span class="font-medium text-blue-700">{{ $accountName }}</span></p>
                @if($akunCoa)
                <button type="button" onclick="openTopupModal()" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm transition-colors duration-200">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Top Up Saldo
                </button>
                @endif
            </div>
            <div class="flex gap-4 text-sm">
                <div class="text-center">
                    <div class="text-lg font-semibold text-gray-600">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</div>
                    <div class="text-gray-500 text-xs">Saldo Awal</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-green-600">Rp {{ number_format($totalDebit, 0, ',', '.') }}</div>
                    <div class="text-gray-500 text-xs">Pemasukan (Debit)</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-red-600">Rp {{ number_format($totalKredit, 0, ',', '.') }}</div>
                    <div class="text-gray-500 text-xs">Pengeluaran (Kredit)</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-blue-600">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
                    <div class="text-gray-500 text-xs">Saldo Akhir</div>
                </div>
            </div>
        </div>

        <div class="p-4">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(!$akunCoa)
                <div class="mb-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span class="font-medium">Peringatan:</span> Aku COA "{{ $accountName }}" tidak ditemukan dalam sistem database referensi!
                    </div>
                </div>
            @else
            <!-- Search and Filter -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                <form method="GET" action="{{ route('report.kas-truck.view') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari keterangan / no referensi..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            Cari
                        </button>
                        <a href="{{ route('report.kas-truck.view', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No. Accurate
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keterangan
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pemasukan
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pengeluaran
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Saldo
                            </th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Initial Balance Row -->
                        <tr class="bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-600" colspan="5">Saldo Sebelum Periode (Mulai)</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>

                        @forelse($transactions as $trx)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    @if($trx->nomor_accurate)
                                        <span class="text-blue-600">{{ $trx->nomor_accurate }}</span>
                                    @elseif($trx->nomor_referensi && $trx->nomor_referensi !== '-')
                                        <span class="text-gray-400 text-xs italic">{{ $trx->nomor_referensi }}</span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $trx->keterangan ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-green-600">
                                    @if($trx->debit > 0)
                                        Rp {{ number_format($trx->debit, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-red-600">
                                    @if($trx->kredit > 0)
                                        Rp {{ number_format($trx->kredit, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">
                                    Rp {{ number_format($trx->running_balance, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <form action="{{ route('report.kas-truck.swap', $trx->id) }}" method="POST" onsubmit="return confirm('Tukar posisi Pemasukan/Pengeluaran transaksi ini?')">
                                        @csrf
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50 transition-colors duration-200" title="Tukar Pemasukan/Pengeluaran">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Tidak ada transaksi kas pada periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- TOPUP MODAL -->
<div id="topupModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeTopupModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form action="{{ route('report.kas-truck.topup') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Top Up Saldo Kas Truck
                            </h3>
                            <div class="mt-2 text-sm text-gray-500 mb-4">
                                Masukkan detail mutasi masuk (Pemasukan) untuk rekening Kas Trucking.
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Nominal Top Up (Rp) <span class="text-red-500">*</span></label>
                                    <input type="number" name="nominal" min="1" step="any" placeholder="Contoh: 5000000" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Referensi (Opsional)</label>
                                    <input type="text" name="no_referensi" placeholder="Ketik nomor referensi bank..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Laporan <span class="text-red-500">*</span></label>
                                    <textarea name="keterangan" rows="2" placeholder="Tuliskan keterangan top-up di sini..." required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                        Simpan Top Up
                    </button>
                    <button type="button" onclick="closeTopupModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openTopupModal() {
    document.getElementById('topupModal').classList.remove('hidden');
}

function closeTopupModal() {
    document.getElementById('topupModal').classList.add('hidden');
}
</script>
@endsection
