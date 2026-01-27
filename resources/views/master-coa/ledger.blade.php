@extends('layouts.app')

@section('title', 'Buku Besar - ' . $coa->nama_akun)
@section('page_title', 'Buku Besar - ' . $coa->nama_akun)

@push('styles')
<style>
    @media print {
        /* Hide elements that shouldn't be printed */
        .no-print,
        nav,
        .sidebar,
        aside,
        footer,
        button:not(.print-only),
        .pagination {
            display: none !important;
        }

        /* Reset page margins */
        @page {
            margin: 1cm;
            size: landscape;
        }

        body {
            margin: 0;
            padding: 0;
            background: white !important;
        }

        /* Make content full width */
        .container,
        .max-w-7xl {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Remove shadows and borders for cleaner print */
        .shadow,
        .shadow-sm,
        .shadow-md,
        .shadow-lg {
            box-shadow: none !important;
        }

        .rounded,
        .rounded-lg,
        .rounded-xl {
            border-radius: 0 !important;
        }

        /* Ensure tables fit on page */
        table {
            width: 100% !important;
            page-break-inside: auto;
            font-size: 10px !important;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        /* Adjust text sizes for print */
        .text-2xl {
            font-size: 18px !important;
        }

        .text-lg {
            font-size: 14px !important;
        }

        .text-sm {
            font-size: 10px !important;
        }

        .text-xs {
            font-size: 8px !important;
        }

        /* Print header styling - force display */
        .print-only {
            display: block !important;
            visibility: visible !important;
        }

        /* Summary cards adjustment */
        .grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 5px !important;
            margin-bottom: 10px !important;
        }

        .grid > div {
            padding: 8px !important;
            border: 1px solid #000 !important;
        }

        .grid p {
            margin: 0 !important;
        }

        /* Table styling for print */
        table th {
            background-color: #f3f4f6 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            border: 1px solid #000 !important;
            padding: 5px !important;
        }

        table td {
            border: 1px solid #ddd !important;
            padding: 4px !important;
        }

        /* Color preservation */
        .bg-yellow-50,
        .bg-gray-100,
        .bg-blue-50,
        .bg-green-100,
        .bg-red-100,
        .text-green-600,
        .text-red-600,
        .text-blue-900 {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Ensure modal doesn't print */
        #detailModal {
            display: none !important;
        }

        /* Remove interactive elements */
        button[onclick*="showTransactionDetail"] {
            color: inherit !important;
            text-decoration: none !important;
            pointer-events: none;
        }
    }

    /* Print button hover effect */
    button[onclick*="print"]:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Print Header (only visible when printing) -->
        <div style="display: none;" class="print-header-visible">
            <h1 style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 10px;">BUKU BESAR (LEDGER)</h1>
            <div style="text-align: center; font-size: 10px; margin-bottom: 5px;">
                <strong>Nomor Akun:</strong> {{ $coa->nomor_akun }} |
                <strong>Nama Akun:</strong> {{ $coa->nama_akun }} |
                <strong>Tipe:</strong> {{ $coa->tipe_akun }}
            </div>
            @if(request('dari_tanggal') || request('sampai_tanggal'))
                <div style="text-align: center; font-size: 9px; margin-bottom: 5px;">
                    Periode: {{ request('dari_tanggal') ? \Carbon\Carbon::parse(request('dari_tanggal'))->format('d/m/Y') : 'Awal' }}
                    s/d
                    {{ request('sampai_tanggal') ? \Carbon\Carbon::parse(request('sampai_tanggal'))->format('d/m/Y') : 'Sekarang' }}
                </div>
            @endif
            <div style="text-align: center; font-size: 8px; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px;">
                Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 no-print">
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
                    <a href="{{ route('master-coa-ledger-print', $coa->id) }}{{ request()->has('dari_tanggal') || request()->has('sampai_tanggal') ? '?' . http_build_query(request()->only(['dari_tanggal', 'sampai_tanggal'])) : '' }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </a>
                    <a href="{{ route('master-coa-index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 no-print">
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
                    Rp {{ number_format(abs($totalDebit), 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-500 uppercase">Total Kredit</p>
                <p class="mt-2 text-2xl font-bold text-red-600">
                    Rp {{ number_format(abs($totalKredit), 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 p-4">
                <p class="text-xs font-medium text-blue-700 uppercase">Saldo Akhir</p>
                <p class="mt-2 text-2xl font-bold text-blue-900">
                    Rp {{ number_format($coa->saldo, 2, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6 no-print">
            <form method="GET" action="{{ route('master-coa-ledger', ['coa' => $coa->id]) }}" class="flex items-center space-x-3">
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
                        <a href="{{ route('master-coa-ledger', $coa->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Ledger Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 no-print">
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
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <button type="button"
                                            onclick="showTransactionDetail('{{ $transaction->nomor_referensi }}')"
                                            class="text-indigo-600 hover:text-indigo-900 hover:underline cursor-pointer">
                                        {{ $transaction->nomor_referensi }}
                                    </button>
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
                                    @if($transaction->debit != 0)
                                        Rp {{ number_format(abs($transaction->debit), 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold text-red-600">
                                    @if($transaction->kredit != 0)
                                        Rp {{ number_format(abs($transaction->kredit), 2, ',', '.') }}
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
                                    Rp {{ number_format(abs($totalDebit), 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-red-700">
                                    Rp {{ number_format(abs($totalKredit), 2, ',', '.') }}
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
            <div class="px-6 py-4 border-t border-gray-200 no-print">
                @include('components.modern-pagination', ['paginator' => $transactions])
                @include('components.rows-per-page')
            </div>
        </div>

    </div>
</div>

<!-- Modal Detail Transaksi -->
<div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Header Modal -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Detail Transaksi</h3>
                <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Loading -->
            <div id="detailLoading" class="text-center py-8">
                <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-600">Memuat detail transaksi...</p>
            </div>

            <!-- Content -->
            <div id="detailContent" class="hidden">
                <!-- Info Pembayaran -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Nomor Pembayaran</p>
                            <p class="text-sm font-semibold text-gray-900" id="detailNomorPembayaran">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tanggal Pembayaran</p>
                            <p class="text-sm font-semibold text-gray-900" id="detailTanggal">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Bank</p>
                            <p class="text-sm font-semibold text-gray-900" id="detailBank">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Pembayaran</p>
                            <p class="text-sm font-semibold text-green-600" id="detailTotal">-</p>
                        </div>
                    </div>
                </div>

                <!-- Daftar Pranota -->
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Daftar Pranota</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Pranota</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah Tagihan</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody id="detailPranotaTable" class="bg-white divide-y divide-gray-200">
                                <!-- Data akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Daftar Tagihan -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Detail Tagihan Kontainer</h4>
                    <div class="overflow-x-auto max-h-96">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Kontainer</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ukuran</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Periode</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tarif</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Lama</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">DPP</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">PPN</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">PPH</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody id="detailTagihanTable" class="bg-white divide-y divide-gray-200">
                                <!-- Data akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Error -->
            <div id="detailError" class="hidden text-center py-8">
                <svg class="w-12 h-12 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-red-600" id="detailErrorMessage">Gagal memuat detail transaksi</p>
            </div>

            <!-- Footer -->
            <div class="mt-4 flex justify-end">
                <button type="button" onclick="closeDetailModal()" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showTransactionDetail(nomorReferensi) {
    // Show modal
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('detailLoading').classList.remove('hidden');
    document.getElementById('detailContent').classList.add('hidden');
    document.getElementById('detailError').classList.add('hidden');

    // Fetch data dari API endpoint
    fetch(`/api/pembayaran-pranota-kontainer/detail/${encodeURIComponent(nomorReferensi)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug
            document.getElementById('detailLoading').classList.add('hidden');

            if (data.success) {
                displayTransactionDetail(data.data);
                document.getElementById('detailContent').classList.remove('hidden');
            } else {
                showError(data.message || 'Gagal memuat detail transaksi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('detailLoading').classList.add('hidden');
            showError('Terjadi kesalahan saat mengambil detail pembayaran: ' + error.message);
        });
}

function displayTransactionDetail(data) {
    // Set info pembayaran
    document.getElementById('detailNomorPembayaran').textContent = data.nomor_pembayaran;
    document.getElementById('detailTanggal').textContent = data.tanggal_pembayaran;
    document.getElementById('detailBank').textContent = data.bank;
    document.getElementById('detailTotal').textContent = 'Rp ' + parseFloat(data.total_tagihan_setelah_penyesuaian).toLocaleString('id-ID');

    // Render pranota table
    const pranotaTable = document.getElementById('detailPranotaTable');
    pranotaTable.innerHTML = '';

    if (data.pranota_list && data.pranota_list.length > 0) {
        data.pranota_list.forEach(pranota => {
            const row = `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-sm text-gray-900">${pranota.no_invoice}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${pranota.tanggal_pranota || '-'}</td>
                    <td class="px-4 py-2 text-sm text-right text-gray-900">${pranota.jumlah_tagihan} item</td>
                    <td class="px-4 py-2 text-sm text-right font-semibold text-gray-900">Rp ${parseFloat(pranota.total_amount).toLocaleString('id-ID')}</td>
                </tr>
            `;
            pranotaTable.innerHTML += row;
        });
    } else {
        pranotaTable.innerHTML = '<tr><td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data pranota</td></tr>';
    }

    // Render tagihan table
    const tagihanTable = document.getElementById('detailTagihanTable');
    tagihanTable.innerHTML = '';

    if (data.tagihan_list && data.tagihan_list.length > 0) {
        data.tagihan_list.forEach(tagihan => {
            const row = `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-sm text-gray-900">${tagihan.nomor_kontainer}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${tagihan.ukuran_kontainer}</td>
                    <td class="px-4 py-2 text-sm text-center text-gray-500">${tagihan.periode}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${tagihan.tanggal_mulai} - ${tagihan.tanggal_akhir || 'Ongoing'}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${tagihan.tarif}</td>
                    <td class="px-4 py-2 text-sm text-right text-gray-900">${tagihan.lama_hari} hari</td>
                    <td class="px-4 py-2 text-sm text-right text-gray-900">Rp ${parseFloat(tagihan.dpp).toLocaleString('id-ID')}</td>
                    <td class="px-4 py-2 text-sm text-right text-gray-900">Rp ${parseFloat(tagihan.ppn).toLocaleString('id-ID')}</td>
                    <td class="px-4 py-2 text-sm text-right text-gray-900">Rp ${parseFloat(tagihan.pph).toLocaleString('id-ID')}</td>
                    <td class="px-4 py-2 text-sm text-right font-semibold text-gray-900">Rp ${parseFloat(tagihan.total_biaya).toLocaleString('id-ID')}</td>
                </tr>
            `;
            tagihanTable.innerHTML += row;
        });
    } else {
        tagihanTable.innerHTML = '<tr><td colspan="10" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data tagihan</td></tr>';
    }
}

function showError(message) {
    document.getElementById('detailError').classList.remove('hidden');
    document.getElementById('detailErrorMessage').textContent = message;
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('detailModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailModal();
    }
});
</script>
@endsection
