@extends('layouts.app')

@section('title', 'Detail Pembayaran Pranota Lembur')
@section('page_title', 'Detail Pembayaran Pranota Lembur')

@section('content')
<div class="bg-white shadow-lg rounded-lg overflow-hidden max-w-4xl mx-auto">
    <div class="p-6 bg-gray-50 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-gray-800">{{ $pembayaranPranotaLembur->nomor_pembayaran }}</h3>
                <p class="text-sm text-gray-500">Tanggal: {{ $pembayaranPranotaLembur->tanggal_pembayaran->format('d F Y') }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                    {{ $pembayaranPranotaLembur->status_label }}
                </span>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Informasi Pembayaran</h4>
                <table class="min-w-full text-sm">
                    <tr>
                        <td class="py-1 text-gray-600 w-1/3">Bank</td>
                        <td class="py-1 font-medium text-gray-900">: {{ $pembayaranPranotaLembur->bank }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-gray-600">Jenis Transaksi</td>
                        <td class="py-1 font-medium text-gray-900">: {{ $pembayaranPranotaLembur->jenis_transaksi }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-gray-600">Nomor Accurate</td>
                        <td class="py-1 font-medium text-gray-900">: {{ $pembayaranPranotaLembur->nomor_accurate ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Metadata</h4>
                <table class="min-w-full text-sm">
                    <tr>
                        <td class="py-1 text-gray-600 w-1/3">Dibuat Oleh</td>
                        <td class="py-1 font-medium text-gray-900">: {{ $pembayaranPranotaLembur->createdBy->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-gray-600">Waktu Input</td>
                        <td class="py-1 font-medium text-gray-900">: {{ $pembayaranPranotaLembur->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Item Pranota Terbayar</h4>
        <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200 mb-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Pranota</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pembayaranPranotaLembur->pranotaLemburs as $pranota)
                        <tr>
                            <td class="px-4 py-2 text-sm text-indigo-600 font-medium">
                                <a href="{{ route('pranota-lembur.show', $pranota->id) }}" target="_blank">{{ $pranota->nomor_pranota }}</a>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-sm text-right font-semibold">Rp {{ number_format($pranota->pivot->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100">
                    <tr>
                        <td colspan="2" class="px-4 py-2 text-sm font-bold text-gray-900 text-right">TOTAL TAGIHAN</td>
                        <td class="px-4 py-2 text-sm font-bold text-gray-900 text-right text-lg">Rp {{ number_format($pembayaranPranotaLembur->total_pembayaran, 0, ',', '.') }}</td>
                    </tr>
                    @if($pembayaranPranotaLembur->total_tagihan_penyesuaian != 0)
                        <tr>
                            <td colspan="2" class="px-4 py-2 text-sm font-medium text-gray-600 text-right">Penyesuaian</td>
                            <td class="px-4 py-2 text-sm font-medium text-gray-600 text-right">Rp {{ number_format($pembayaranPranotaLembur->total_tagihan_penyesuaian, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="px-4 py-2 text-sm font-bold text-gray-900 text-right uppercase">Total Dibayar</td>
                            <td class="px-4 py-2 text-sm font-bold text-indigo-700 text-right text-xl">Rp {{ number_format($pembayaranPranotaLembur->total_tagihan_setelah_penyesuaian, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tfoot>
            </table>
        </div>

        @if($pembayaranPranotaLembur->keterangan)
            <div class="mb-6">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Keterangan</h4>
                <div class="p-3 bg-blue-50 border border-blue-100 rounded text-sm text-gray-700">
                    {{ $pembayaranPranotaLembur->keterangan }}
                </div>
            </div>
        @endif
    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between gap-2">
        <a href="{{ route('pembayaran-pranota-lembur.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
            Kembali
        </a>
        <div class="flex gap-2">
            @can('pembayaran-pranota-lembur-edit')
                <a href="{{ route('pembayaran-pranota-lembur.edit', $pembayaranPranotaLembur->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 transition ease-in-out duration-150">
                    Edit
                </a>
            @endcan
        </div>
    </div>
</div>
@endsection
