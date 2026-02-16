@extends('layouts.app')

@section('title', 'Detail Approval Order')
@section('page_title', 'Detail Approval Order')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Approval Order</h1>
                    <p class="mt-1 text-sm text-gray-600">Nomor Order: {{ $order->nomor_order }}</p>
                </div>
                <div class="flex space-x-3">
                    @can('approval-order-update')
                    <a href="{{ route('approval-order.edit', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Approval
                    </a>
                    @endcan
                    <a href="{{ route('approval-order.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Order Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-100">Informasi Order</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Order</label>
                            <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($order->tanggal_order)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Order</label>
                            <p class="mt-1">
                                <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold rounded-full
                                    @if($order->status === 'approved') bg-green-100 text-green-800
                                    @elseif($order->status === 'rejected') bg-red-100 text-red-800
                                    @elseif($order->status === 'active') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengirim</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $order->pengirim->nama_pengirim ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Tujuan Kirim</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->tujuan_kirim ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Barang</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->jenisBarang->nama_barang ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Tujuan Ambil</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->tujuan_ambil ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Recipient Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-100">Informasi Penerima</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Penerima</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $order->recipient->nama_penerima ?? $order->penerima ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Kontak</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->kontak_penerima ?: '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Alamat Penerima</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->alamat_penerima ?: '-' }}</p>
                        </div>

                        @if($order->notifyParty)
                        <div class="md:col-span-2 pt-4 border-t border-gray-100">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Notify Party</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $order->notifyParty->nama_penerima }}</p>
                            <p class="mt-1 text-xs text-gray-600">{{ $order->notifyParty->alamat }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Term & Options -->
            <div class="space-y-6">
                <!-- Term Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-100">Term Pembayaran</h3>
                    @if($order->term)
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <span class="text-blue-600 font-bold text-xl mr-2">{{ $order->term->kode }}</span>
                                <span class="text-blue-800 font-medium text-sm">{{ $order->term->nama_status }}</span>
                            </div>
                            <p class="text-xs text-blue-600">Terdaftar pada: {{ $order->term->created_at->format('d/m/Y') }}</p>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 italic">Belum ada term pembayaran</p>
                        </div>
                    @endif
                </div>

                <!-- Exclude/Include Options -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-100">Opsi Tambahan</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Biaya FTZ03</label>
                            <p class="mt-1 text-sm">
                                @if($order->exclude_ftz03) <span class="text-red-600 font-medium">Exclude</span>
                                @elseif($order->include_ftz03) <span class="text-green-600 font-medium">Include</span>
                                @else <span class="text-gray-400">Not Specified</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Biaya SPPB</label>
                            <p class="mt-1 text-sm">
                                @if($order->exclude_sppb) <span class="text-red-600 font-medium">Exclude</span>
                                @elseif($order->include_sppb) <span class="text-green-600 font-medium">Include</span>
                                @else <span class="text-gray-400">Not Specified</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Buruh Bongkar</label>
                            <p class="mt-1 text-sm">
                                @if($order->exclude_buruh_bongkar) <span class="text-red-600 font-medium">Exclude</span>
                                @elseif($order->include_buruh_bongkar) <span class="text-green-600 font-medium">Include</span>
                                @else <span class="text-gray-400">Not Specified</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Container Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-100">Kontainer</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $order->unit_kontainer }}</p>
                            <p class="text-xs text-gray-500 uppercase font-semibold">Unit</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-semibold text-gray-900">{{ $order->size_kontainer }}"</p>
                            <p class="text-xs text-gray-500 uppercase font-semibold">{{ ucfirst($order->tipe_kontainer) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Surat Jalan Section -->
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Surat Jalan Terkait</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Seal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($order->suratJalans as $sj)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $sj->no_surat_jalan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sj->no_kontainer ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sj->no_seal ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sj->supir->nama_lengkap ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data surat jalan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
