@extends('layouts.app')

@section('title', 'Detail Pembayaran Pranota Uang Jalan')
@section('page_title', 'Detail Pembayaran Pranota Uang Jalan')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 max-w-4xl mx-auto">
        @php
            $labelClasses = "block text-sm font-medium text-gray-700 mb-1";
            $valueClasses = "text-sm text-gray-900 font-medium";
        @endphp

        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Detail Pembayaran</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Nomor: <strong>{{ $pembayaranPranotaUangJalan->nomor_pembayaran }}</strong></span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        @if($pembayaranPranotaUangJalan->status_pembayaran == 'paid')
                            bg-green-100 text-green-800
                        @elseif($pembayaranPranotaUangJalan->status_pembayaran == 'pending')
                            bg-yellow-100 text-yellow-800
                        @else
                            bg-red-100 text-red-800
                        @endif
                    ">
                        {{ ucfirst($pembayaranPranotaUangJalan->status_pembayaran) }}
                    </span>
                </div>
            </div>
            <div class="flex space-x-2">
                @can('pembayaran-pranota-uang-jalan-update')
                    @if($pembayaranPranotaUangJalan->status_pembayaran == 'pending')
                        <a href="{{ route('pembayaran-pranota-uang-jalan.edit', $pembayaranPranotaUangJalan) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                    @endif
                @endcan
                <a href="{{ route('pembayaran-pranota-uang-jalan.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Alert Success -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Detail Pembayaran -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Informasi Pembayaran -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pembayaran</h3>
                <div class="space-y-3">
                    <div>
                        <label class="{{ $labelClasses }}">Nomor Pembayaran</label>
                        <div class="{{ $valueClasses }}">{{ $pembayaranPranotaUangJalan->nomor_pembayaran }}</div>
                    </div>
                    <div>
                        <label class="{{ $labelClasses }}">Tanggal Pembayaran</label>
                        <div class="{{ $valueClasses }}">{{ \Carbon\Carbon::parse($pembayaranPranotaUangJalan->tanggal_pembayaran)->format('d/M/Y') }}</div>
                    </div>
                    <div>
                        <label class="{{ $labelClasses }}">Bank</label>
                        <div class="{{ $valueClasses }}">{{ $pembayaranPranotaUangJalan->bank ?? '-' }}</div>
                    </div>
                    <div>
                        <label class="{{ $labelClasses }}">Jenis Transaksi</label>
                        <div class="{{ $valueClasses }}">{{ $pembayaranPranotaUangJalan->jenis_transaksi ?? '-' }}</div>
                    </div>
                    <div>
                        <label class="{{ $labelClasses }}">Nomor Accurate</label>
                        <div class="{{ $valueClasses }}">{{ $pembayaranPranotaUangJalan->nomor_accurate ?? '-' }}</div>
                    </div>
                    <div>
                        <label class="{{ $labelClasses }}">Status Pembayaran</label>
                        <div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($pembayaranPranotaUangJalan->status_pembayaran == 'paid')
                                    bg-green-100 text-green-800
                                @elseif($pembayaranPranotaUangJalan->status_pembayaran == 'pending')
                                    bg-yellow-100 text-yellow-800
                                @else
                                    bg-red-100 text-red-800
                                @endif
                            ">
                                {{ ucfirst($pembayaranPranotaUangJalan->status_pembayaran) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Jumlah -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Rincian Jumlah</h3>
                <div class="space-y-3">
                    <div>
                        <label class="{{ $labelClasses }}">Total Pembayaran</label>
                        <div class="{{ $valueClasses }}">Rp {{ number_format($pembayaranPranotaUangJalan->total_pembayaran, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <label class="{{ $labelClasses }}">Penyesuaian</label>
                        <div class="{{ $valueClasses }}">Rp {{ number_format($pembayaranPranotaUangJalan->total_tagihan_penyesuaian, 0, ',', '.') }}</div>
                    </div>
                    <div class="border-t pt-3">
                        <label class="{{ $labelClasses }}">Total Akhir</label>
                        <div class="text-lg font-bold text-gray-900">Rp {{ number_format($pembayaranPranotaUangJalan->total_tagihan_setelah_penyesuaian, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Pranota -->
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pranota Uang Jalan</h3>
            @if($pembayaranPranotaUangJalan->pranotaUangJalans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nomor Pranota</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pembayaranPranotaUangJalan->pranotaUangJalans as $pranota)
                            <tr>
                                <td class="px-4 py-2 text-sm">
                                    @can('pranota-uang-jalan-view')
                                        <a href="{{ route('pranota-uang-jalan.show', $pranota) }}" 
                                           class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                            {{ $pranota->nomor_pranota }}
                                        </a>
                                    @else
                                        {{ $pranota->nomor_pranota }}
                                    @endcan
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    {{ $pranota->tanggal_pranota ? \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/M/Y') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($pranota->status_pembayaran == 'paid') bg-green-100 text-green-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($pranota->status_pembayaran) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm text-right font-medium">
                                    Rp {{ number_format($pranota->pivot->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">Tidak ada pranota terkait.</p>
            @endif
        </div>

        <!-- Keterangan -->
        @if($pembayaranPranotaUangJalan->alasan_penyesuaian || $pembayaranPranotaUangJalan->keterangan)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Keterangan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($pembayaranPranotaUangJalan->alasan_penyesuaian)
                        <div>
                            <label class="{{ $labelClasses }}">Alasan Penyesuaian</label>
                            <div class="{{ $valueClasses }}">{{ $pembayaranPranotaUangJalan->alasan_penyesuaian }}</div>
                        </div>
                    @endif
                    @if($pembayaranPranotaUangJalan->keterangan)
                        <div>
                            <label class="{{ $labelClasses }}">Keterangan Tambahan</label>
                            <div class="{{ $valueClasses }}">{{ $pembayaranPranotaUangJalan->keterangan }}</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Informasi Sistem -->
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Sistem</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                <div>
                    <label class="{{ $labelClasses }}">Dibuat oleh</label>
                    <div class="{{ $valueClasses }}">{{ $pembayaranPranotaUangJalan->createdBy->username ?? 'System' }}</div>
                </div>
                <div>
                    <label class="{{ $labelClasses }}">Tanggal Dibuat</label>
                    <div class="{{ $valueClasses }}">{{ $pembayaranPranotaUangJalan->created_at ? $pembayaranPranotaUangJalan->created_at->format('d/M/Y H:i') : '-' }}</div>
                </div>
                <div>
                    <label class="{{ $labelClasses }}">Diperbarui oleh</label>
                    <div class="{{ $valueClasses }}">{{ $pembayaranPranotaUangJalan->updatedBy->username ?? 'System' }}</div>
                </div>
                <div>
                    <label class="{{ $labelClasses }}">Terakhir Diperbarui</label>
                    <div class="{{ $valueClasses }}">{{ $pembayaranPranotaUangJalan->updated_at ? $pembayaranPranotaUangJalan->updated_at->format('d/M/Y H:i') : '-' }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection