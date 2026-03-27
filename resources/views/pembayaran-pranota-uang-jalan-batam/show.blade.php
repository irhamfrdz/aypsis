@extends('layouts.app')

@section('title', 'Detail Pembayaran Pranota Uang Jalan Batam')
@section('page_title', 'Detail Pembayaran Pranota Uang Jalan Batam')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 max-w-4xl mx-auto">
        @php
            $labelClasses = "block text-sm font-medium text-gray-700 mb-1";
            $valueClasses = "text-sm text-gray-900 font-medium";
        @endphp

        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Detail Pembayaran Batam</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Nomor: <strong>{{ $pembayaranPranotaUangJalanBatam->nomor_pembayaran }}</strong></span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        @if($pembayaranPranotaUangJalanBatam->status_pembayaran == 'paid')
                            bg-green-100 text-green-800
                        @elseif($pembayaranPranotaUangJalanBatam->status_pembayaran == 'pending')
                            bg-yellow-100 text-yellow-800
                        @else
                            bg-red-100 text-red-800
                        @endif
                    ">
                        {{ ucfirst($pembayaranPranotaUangJalanBatam->status_pembayaran) }}
                    </span>
                </div>
            </div>
            <div class="flex space-x-2">
                @if($pembayaranPranotaUangJalanBatam->status_pembayaran == 'paid' || $pembayaranPranotaUangJalanBatam->status_pembayaran == 'pending')
                    <a href="{{ route('pembayaran-pranota-uang-jalan-batam.edit', $pembayaranPranotaUangJalanBatam->id) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                        Edit
                    </a>
                @endif
                <a href="{{ route('pembayaran-pranota-uang-jalan-batam.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pembayaran</h3>
                <div class="space-y-3 font-medium text-sm">
                    <div><label class="{{ $labelClasses }}">Nomor Pembayaran</label><div>{{ $pembayaranPranotaUangJalanBatam->nomor_pembayaran }}</div></div>
                    <div><label class="{{ $labelClasses }}">Tanggal</label><div>{{ $pembayaranPranotaUangJalanBatam->tanggal_pembayaran ? $pembayaranPranotaUangJalanBatam->tanggal_pembayaran->format('d/M/Y') : '-' }}</div></div>
                    <div><label class="{{ $labelClasses }}">Bank</label><div>{{ $pembayaranPranotaUangJalanBatam->bank ?? '-' }}</div></div>
                    <div><label class="{{ $labelClasses }}">Jenis</label><div>{{ $pembayaranPranotaUangJalanBatam->jenis_transaksi }}</div></div>
                    <div><label class="{{ $labelClasses }}">Nomor Accurate</label><div>{{ $pembayaranPranotaUangJalanBatam->nomor_accurate ?? '-' }}</div></div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Rincian Jumlah</h3>
                <div class="space-y-3">
                    <div><label class="{{ $labelClasses }}">Total Tagihan</label><div>Rp {{ number_format($pembayaranPranotaUangJalanBatam->total_pembayaran, 0, ',', '.') }}</div></div>
                    <div><label class="{{ $labelClasses }}">Penyesuaian</label><div>Rp {{ number_format($pembayaranPranotaUangJalanBatam->total_tagihan_penyesuaian, 0, ',', '.') }}</div></div>
                    <div class="border-t pt-3 font-bold text-lg text-gray-900">Total Akhir: Rp {{ number_format($pembayaranPranotaUangJalanBatam->total_tagihan_setelah_penyesuaian, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pranota Uang Jalan Batam Terkait</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-2 text-left">Nomor Pranota</th>
                            <th class="px-4 py-2 text-left">Tanggal</th>
                            <th class="px-4 py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pembayaranPranotaUangJalanBatam->pranotaUangJalanBatams as $pranota)
                        <tr>
                            <td class="px-4 py-2 font-medium text-blue-600">
                                <a href="{{ route('pranota-uang-jalan-batam.show', $pranota->id) }}" target="_blank">{{ $pranota->nomor_pranota }}</a>
                            </td>
                            <td class="px-4 py-2">{{ $pranota->tanggal_pranota->format('d/M/Y') }}</td>
                            <td class="px-4 py-2 text-right font-semibold">Rp {{ number_format($pranota->pivot->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Sistem</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                <div><label class="{{ $labelClasses }}">Dibuat oleh</label><div>{{ $pembayaranPranotaUangJalanBatam->createdBy->username ?? '-' }}</div></div>
                <div><label class="{{ $labelClasses }}">Dibuat pada</label><div>{{ $pembayaranPranotaUangJalanBatam->created_at->format('d/M/Y H:i') }}</div></div>
                <div><label class="{{ $labelClasses }}">Diperbarui oleh</label><div>{{ $pembayaranPranotaUangJalanBatam->updatedBy->username ?? '-' }}</div></div>
                <div><label class="{{ $labelClasses }}">Diperbarui pada</label><div>{{ $pembayaranPranotaUangJalanBatam->updated_at->format('d/M/Y H:i') }}</div></div>
            </div>
        </div>
    </div>
@endsection
