@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Invoice Aktivitas Lain</h1>
                <p class="text-gray-600 mt-1">{{ $invoice->nomor_invoice }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('invoice-aktivitas-lain.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                @can('invoice-aktivitas-lain-update')
                <a href="{{ route('invoice-aktivitas-lain.edit', $invoice->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Invoice
                </a>
                @endcan
                @php
                    $isListrik = $invoice->klasifikasiBiayaUmum && 
                                str_contains(strtolower($invoice->klasifikasiBiayaUmum->nama ?? ''), 'listrik');
                @endphp

                @if($isListrik)
                    <a href="{{ route('invoice-aktivitas-lain.print-listrik', $invoice->id) }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Listrik
                    </a>
                @elseif($invoice->klasifikasiBiaya && (str_contains(strtolower($invoice->klasifikasiBiaya->nama), 'labuh tambat') || str_contains(strtolower($invoice->klasifikasiBiaya->nama), 'labuh tambah')))
                    <a href="{{ route('invoice-aktivitas-lain.print-labuh-tambat', $invoice->id) }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Labuh Tambat
                    </a>
                @else
                    <a href="{{ route('invoice-aktivitas-lain.print', $invoice->id) }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Invoice Info Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white">Informasi Invoice</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Invoice</label>
                            <p class="text-gray-900 font-semibold text-lg">{{ $invoice->nomor_invoice }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Invoice</label>
                            <p class="text-gray-900">{{ $invoice->tanggal_invoice->format('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Aktivitas</label>
                            <p class="text-gray-900">{{ $invoice->jenis_aktivitas ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sub Jenis Kendaraan</label>
                            <p class="text-gray-900">{{ $invoice->sub_jenis_kendaraan ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                            <p class="text-gray-900">{{ $invoice->penerima ?? '-' }}</p>
                        </div>
                        @if($invoice->vendor_labuh_tambat)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vendor Labuh Tambat</label>
                            <p class="text-gray-900 font-semibold">{{ $invoice->vendor_labuh_tambat }}</p>
                        </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'submitted' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-blue-100 text-blue-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'draft' => 'Draft',
                                    'submitted' => 'Submitted',
                                    'approved' => 'Approved',
                                    'paid' => 'Paid',
                                    'cancelled' => 'Cancelled',
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                            </span>
                        </div>
                    </div>

                    <!-- Akun Information -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Informasi Akun</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Akun COA</label>
                                <p class="text-gray-900">{{ $invoice->akun_coa->kode_nomor ?? '-' }} - {{ $invoice->akun_coa->nama_akun ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Akun Bank</label>
                                <p class="text-gray-900">{{ $invoice->akun_bank->kode_nomor ?? '-' }} - {{ $invoice->akun_bank->nama_akun ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Debit/Kredit</label>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $invoice->debit_kredit == 'debit' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($invoice->debit_kredit ?? '-') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($invoice->keterangan)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <p class="text-gray-900">{{ $invoice->keterangan }}</p>
                    </div>
                    @endif

                    <!-- BL Information -->
                    @php
                        $blDetails = $invoice->bl_details_array;
                    @endphp
                    @if(count($blDetails) > 0)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Informasi BL ({{ count($blDetails) }} BL)</h3>
                        <div class="space-y-4">
                            @foreach($blDetails as $index => $bl)
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">BL #{{ $index + 1 }} - Nomor BL</label>
                                        <p class="text-gray-900 font-semibold">{{ $bl['nomor_bl'] }}</p>
                                    </div>
                                    @if($bl['nomor_kontainer'])
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Nomor Kontainer</label>
                                        <p class="text-gray-900">{{ $bl['nomor_kontainer'] }}</p>
                                    </div>
                                    @endif
                                    @if($bl['no_voyage'])
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">No. Voyage</label>
                                        <p class="text-gray-900">{{ $bl['no_voyage'] }}</p>
                                    </div>
                                    @endif
                                    @if($bl['nama_kapal'])
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Kapal</label>
                                        <p class="text-gray-900">{{ $bl['nama_kapal'] }}</p>
                                    </div>
                                    @endif
                                    @if($bl['pengirim'])
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Pengirim</label>
                                        <p class="text-gray-900">{{ $bl['pengirim'] }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Klasifikasi Biaya & Barang Detail -->
                    @if($invoice->klasifikasiBiaya)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Klasifikasi Biaya</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Klasifikasi</label>
                            <p class="text-gray-900 font-semibold">{{ $invoice->klasifikasiBiaya->nama }}</p>
                        </div>

                        @php
                            $barangDetails = $invoice->barang_detail_array;
                        @endphp

                        @if(count($barangDetails) > 0)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Detail Barang</label>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tarif</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($barangDetails as $index => $barang)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $barang['nama_barang'] }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $barang['size'] ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $barang['tipe'] ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900 text-right">Rp {{ number_format($barang['tarif'], 0, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900 text-center">{{ $barang['jumlah'] }}</td>
                                            <td class="px-4 py-2 text-sm font-semibold text-gray-900 text-right">Rp {{ number_format($barang['subtotal'], 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                                        <tr>
                                            <td colspan="6" class="px-4 py-2 text-right text-sm font-semibold text-gray-900">Total Barang:</td>
                                            <td class="px-4 py-2 text-right text-sm font-bold text-gray-900">
                                                Rp {{ number_format(array_sum(array_column($barangDetails, 'subtotal')), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Labuh Tambat Detail -->
                    @if($invoice->klasifikasiBiaya && str_contains(strtolower($invoice->klasifikasiBiaya->nama), 'labuh tambat'))
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Detail Biaya Labuh Tambat</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Sub Total</label>
                                <p class="text-gray-900 font-bold">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</p>
                            </div>
                            <div class="p-4 bg-red-50 rounded-lg text-red-800">
                                <label class="block text-xs font-medium text-red-600 mb-1">PPH (2%)</label>
                                <p class="text-red-900 font-bold">- Rp {{ number_format($invoice->pph, 0, ',', '.') }}</p>
                            </div>
                            <div class="p-4 bg-green-50 rounded-lg text-green-800">
                                <label class="block text-xs font-medium text-green-600 mb-1">Total Akhir</label>
                                <p class="text-green-900 font-bold">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Detail Pembayaran -->
                    @php
                        $detailPembayaran = $invoice->detail_pembayaran_array;
                    @endphp
                    @if(!empty($detailPembayaran))
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Detail Pembayaran</h3>
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full align-middle">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Jenis Biaya</th>
                                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Biaya</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Keterangan</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal Kas</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No Bukti</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Penerima</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($detailPembayaran as $index => $detail)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $detail['jenis_biaya'] ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900 text-right font-medium">
                                                @if(isset($detail['biaya']) && $detail['biaya'])
                                                    Rp {{ number_format($detail['biaya'], 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $detail['keterangan'] ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">
                                                @if(isset($detail['tanggal_kas']) && $detail['tanggal_kas'])
                                                    {{ \Carbon\Carbon::parse($detail['tanggal_kas'])->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $detail['no_bukti'] ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $detail['penerima'] ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                                        <tr>
                                            <td colspan="2" class="px-4 py-2 text-right text-sm font-semibold text-gray-900">Total Detail Pembayaran:</td>
                                            <td class="px-4 py-2 text-right text-sm font-bold text-gray-900">
                                                Rp {{ number_format(array_sum(array_map(function($d) { return is_numeric($d['biaya'] ?? 0) ? $d['biaya'] : 0; }, $detailPembayaran)), 0, ',', '.') }}
                                            </td>
                                            <td colspan="4"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Deskripsi & Catatan -->
                    @if($invoice->deskripsi || $invoice->catatan)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Informasi Tambahan</h3>
                        @if($invoice->deskripsi)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <p class="text-gray-900 text-sm">{{ $invoice->deskripsi }}</p>
                        </div>
                        @endif
                        @if($invoice->catatan)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <p class="text-gray-900 text-sm">{{ $invoice->catatan }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Pembayaran Items -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white">Detail Pembayaran Aktivitas</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Aktivitas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($invoice->pembayarans as $index => $pembayaran)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $pembayaran->nomor ?? '-' }}</div>
                                        @if($pembayaran->nomor_accurate)
                                            <div class="text-xs text-gray-500">Accurate: {{ $pembayaran->nomor_accurate }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $pembayaran->tanggal->format('d/m/Y') ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $pembayaran->jenis_aktivitas ?? '-' }}</div>
                                        @if($pembayaran->sub_jenis_kendaraan)
                                            <div class="text-xs text-gray-500">{{ $pembayaran->sub_jenis_kendaraan }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $pembayaran->penerima ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                        Rp {{ number_format($pembayaran->pivot->jumlah_dibayar ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <p class="text-sm">Tidak ada pembayaran terkait dengan invoice ini</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                    Total:
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-base font-bold text-gray-900">
                                    Rp {{ number_format($invoice->total ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Status Pranota Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white">Status Pranota</h2>
                </div>
                <div class="p-6">
                    @if($invoice->pranota_id && $invoice->pranota)
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Pranota</label>
                                <p class="text-gray-900 font-mono font-semibold">{{ $invoice->pranota->no_invoice }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pranota</label>
                                <p class="text-gray-900">{{ $invoice->pranota->tanggal_pranota->format('d F Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                                @if($invoice->pranota->status == 'paid')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Lunas
                                    </span>
                                @elseif($invoice->pranota->status == 'approved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Disetujui
                                    </span>
                                @elseif($invoice->pranota->status == 'submitted')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Menunggu Pembayaran
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-orange-100 text-orange-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ ucfirst($invoice->pranota->status) }}
                                    </span>
                                @endif
                            </div>
                            <div class="pt-4">
                                <a href="{{ route('pranota-aktivitas-lain.show', $invoice->pranota->id) }}" 
                                   class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Lihat Detail Pranota
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Belum masuk pranota</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Metadata Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white">Informasi Sistem</h2>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dibuat Oleh</label>
                        <p class="text-gray-900">{{ $invoice->creator->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dibuat</label>
                        <p class="text-gray-900">{{ $invoice->created_at->format('d F Y H:i') }}</p>
                    </div>
                    @if($invoice->updated_at != $invoice->created_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Terakhir Diupdate</label>
                        <p class="text-gray-900">{{ $invoice->updated_at->format('d F Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
