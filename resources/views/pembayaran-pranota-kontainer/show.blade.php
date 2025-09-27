@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Detail Pembayaran</h1>
                <div class="flex space-x-3">
                    <a href="{{ route('pembayaran-pranota-kontainer.print', $pembayaran->id) }}"
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-print mr-2"></i>
                        Cetak
                    </a>
                    <a href="{{ route('pembayaran-pranota-kontainer.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="p-6">
            <!-- Payment Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Basic Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembayaran</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nomor Pembayaran</dt>
                            <dd class="text-sm text-gray-900 font-semibold">{{ $pembayaran->nomor_pembayaran }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nomor Cetakan</dt>
                            <dd class="text-sm text-gray-900">#{{ $pembayaran->nomor_cetakan }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Bank</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->bank }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jenis Transaksi</dt>
                            <dd class="text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $pembayaran->jenis_transaksi }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Pembayaran</dt>
                            <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Kas</dt>
                            <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status Pembayaran</dt>
                            <dd class="text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($pembayaran->status === 'approved') bg-green-100 text-green-800
                                    @elseif($pembayaran->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    @if($pembayaran->status === 'approved')
                                        <i class="fas fa-check-circle mr-1"></i>Sudah Dibayar
                                    @elseif($pembayaran->status === 'rejected')
                                        <i class="fas fa-times-circle mr-1"></i>Ditolak
                                    @else
                                        <i class="fas fa-clock mr-1"></i>Belum Dibayar
                                    @endif
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Amount -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Jumlah Pembayaran</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Pranota</dt>
                            <dd class="text-sm text-gray-900 font-semibold">Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</dd>
                        </div>
                        @if($pembayaran->total_tagihan_penyesuaian != 0)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Penyesuaian</dt>
                                <dd class="text-sm font-semibold {{ $pembayaran->total_tagihan_penyesuaian >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $pembayaran->total_tagihan_penyesuaian >= 0 ? '+' : '' }}Rp {{ number_format($pembayaran->total_tagihan_penyesuaian, 0, ',', '.') }}
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Pembayaran</dt>
                            <dd class="text-lg text-gray-900 font-bold">Rp {{ number_format($pembayaran->total_tagihan_setelah_penyesuaian ?? $pembayaran->total_pembayaran, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->pembuatPembayaran->name ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Additional Information -->
            @if($pembayaran->alasan_penyesuaian || $pembayaran->keterangan)
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tambahan</h3>
                    @if($pembayaran->alasan_penyesuaian)
                        <div class="mb-3">
                            <dt class="text-sm font-medium text-gray-500 mb-1">Alasan Penyesuaian</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->alasan_penyesuaian }}</dd>
                        </div>
                    @endif
                    @if($pembayaran->keterangan)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">Keterangan</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaran->keterangan }}</dd>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Pranota List -->
            <div class="bg-white border border-gray-200 rounded-lg">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Pranota Kontainer ({{ $pembayaran->items->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Pranota
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Pranota
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah Tagihan
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Amount
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status Pembayaran
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Pembayaran
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($pembayaran->items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-blue-600">
                                            {{ $item->pranota->no_invoice ?? 'N/A' }}
                                        </div>
                                        @if($item->pranota && $item->pranota->no_external)
                                            <div class="text-xs text-gray-500">Ext: {{ $item->pranota->no_external }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->pranota ? \Carbon\Carbon::parse($item->pranota->tanggal_invoice)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->pranota->jumlah_kontainer ?? 1 }}item
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                        Rp {{ number_format($item->amount ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Sudah Dibayar
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                        @if($item->pranota)
                                            <a href="{{ route('pranota.show', $item->pranota->id) }}"
                                               class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium rounded-full transition-colors duration-200">
                                                <i class="fas fa-eye mr-1"></i>
                                                Lihat
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Data Hilang
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="2" class="px-6 py-3"></td>
                                <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                    <strong>Total:</strong>
                                </td>
                                <td class="px-6 py-3"></td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">
                                    <strong>Rp</strong><br>
                                    <strong>{{ number_format($pembayaran->items->sum('amount'), 0, ',', '.') }}</strong>
                                </td>
                                <td colspan="3" class="px-6 py-3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
