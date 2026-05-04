@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Detail Kwitansi: {{ $kwitansi->kwt_no }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('kwitansi.edit', $kwitansi->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('kwitansi.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Header Section --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Pelanggan</p>
                    <p class="font-medium">{{ $kwitansi->pelanggan_kode }} - {{ $kwitansi->pelanggan_nama ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Terima Dari</p>
                    <p class="font-medium">{{ $kwitansi->terima_dari ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Kirim Ke</p>
                    <p class="font-medium">{{ $kwitansi->kirim_ke ?: '-' }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Tgl. Inv.</p>
                    <p class="font-medium">{{ $kwitansi->tgl_inv ? \Carbon\Carbon::parse($kwitansi->tgl_inv)->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">No. PO</p>
                    <p class="font-medium">{{ $kwitansi->no_po ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tgl. Kirim</p>
                    <p class="font-medium">{{ $kwitansi->tgl_kirim ? \Carbon\Carbon::parse($kwitansi->tgl_kirim)->format('d/m/Y') : '-' }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">FOB</p>
                    <p class="font-medium">{{ $kwitansi->fob ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Syarat Pembayaran</p>
                    <p class="font-medium">{{ $kwitansi->syarat_pembayaran ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pengirim / Penjual</p>
                    <p class="font-medium">{{ $kwitansi->pengirim ?: '-' }} / {{ $kwitansi->penjual ?: '-' }}</p>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">Keterangan</p>
            <p class="font-medium">{{ $kwitansi->keterangan ?: '-' }}</p>
        </div>
    </div>

    {{-- Detail Item Section --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Item Detail</h3>
        
        <div class="overflow-x-auto border border-gray-200 rounded-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item (Kode & Deskripsi)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Tambahan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($kwitansi->details as $detail)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="block text-xs font-semibold text-gray-700">{{ $detail->item_kode }}</span>
                                <span class="block text-sm">{{ $detail->item_description }}</span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm">{{ $detail->qty }}</td>
                            <td class="px-4 py-3 text-right text-sm">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-medium">Rp {{ number_format($detail->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @if($detail->no_bl) <span class="block">B/L: {{ $detail->no_bl }}</span> @endif
                                @if($detail->no_sj) <span class="block">S/J: {{ $detail->no_sj }}</span> @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer/Summary Section --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <p class="text-sm text-gray-500">Akun Piutang</p>
                <p class="font-medium text-lg">{{ $kwitansi->akun_piutang ?: '-' }}</p>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between border-b border-gray-100 pb-2">
                    <span class="text-gray-600">Sub Total</span>
                    <span class="font-medium">Rp {{ number_format($kwitansi->sub_total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-2 text-red-600">
                    <span>Discount ({{ $kwitansi->discount_persen }}%)</span>
                    <span>- Rp {{ number_format($kwitansi->discount_nominal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-2">
                    <span class="text-gray-600">Biaya Kirim</span>
                    <span class="font-medium">Rp {{ number_format($kwitansi->biaya_kirim, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="font-bold text-lg text-gray-800">Total Invoice</span>
                    <span class="font-bold text-xl text-indigo-700">Rp {{ number_format($kwitansi->total_invoice, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
