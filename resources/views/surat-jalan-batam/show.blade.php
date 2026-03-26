@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Detail Surat Jalan Batam</h1>
                <p class="text-xs text-gray-600 mt-1">{{ $suratJalan->no_surat_jalan }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('surat-jalan-batam.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                    Kembali
                </a>
                <a href="{{ route('surat-jalan-batam.edit', $suratJalan->id) }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                    Edit
                </a>
                <a href="{{ route('surat-jalan-batam.print', $suratJalan->id) }}" target="_blank"
                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                    Cetak
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div>
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 border-b pb-2">Informasi Umum</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">No. Surat Jalan</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $suratJalan->no_surat_jalan }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Tanggal</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd class="text-sm font-medium">
                                <span class="px-2 py-1 rounded-full text-xs {{ $suratJalan->status_badge }}">
                                    {{ ucfirst($suratJalan->status) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Pengirim</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $suratJalan->pengirim ?: '-' }}</dd>
                        </div>
                    </dl>

                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mt-8 mb-4 border-b pb-2">Detail Pengiriman</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Tujuan Ambil</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $suratJalan->tujuan_pengambilan ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Tujuan Kirim</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $suratJalan->tujuan_pengiriman ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Jenis Barang</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $suratJalan->jenis_barang ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Right Column -->
                <div>
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 border-b pb-2">Transportasi & Unit</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">No. Plat</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $suratJalan->no_plat ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Supir</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $suratJalan->supir ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Kenek</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $suratJalan->kenek ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Tipe Kontainer</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $suratJalan->tipe_kontainer ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">No. Kontainer</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $suratJalan->no_kontainer ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">No. Seal</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $suratJalan->no_seal ?: '-' }}</dd>
                        </div>
                    </dl>

                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mt-8 mb-4 border-b pb-2">Order Referensi</h3>
                    @if($suratJalan->orderBatam)
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">No. Order Batam</dt>
                                <dd class="text-sm font-medium text-blue-600">
                                    <a href="{{ route('orders-batam.show', $suratJalan->orderBatam->id) }}">{{ $suratJalan->orderBatam->nomor_order }}</a>
                                </dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-500 italic">Tidak ada referensi order Batam</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
