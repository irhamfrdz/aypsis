@extends('layouts.app')

@section('title', 'Detail Perbaikan Kontainer')
@section('page_title', 'Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-4 overflow-y-auto h-full pb-24">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-150">
            <div>
                <h1 class="text-lg font-bold text-gray-900">Detail Perbaikan Kontainer</h1>
                <p class="text-xs text-gray-500 mt-1">Informasi lengkap perbaikan untuk nomor: <strong class="text-blue-600 font-semibold">{{ $perbaikanKontainer->no_perbaikan }}</strong></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('perbaikan-kontainer.index') }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-chevron-left mr-1.5"></i> Kembali
                </a>
                @can('perbaikan-kontainer-update')
                <a href="{{ route('perbaikan-kontainer.edit', $perbaikanKontainer->id) }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                    <i class="fas fa-edit mr-1.5"></i> Edit Data
                </a>
                @endcan
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Status Alert Summary -->
            @php
                $statusColor = match($perbaikanKontainer->status) {
                    'pending' => 'bg-gray-50 text-gray-800 border-gray-200',
                    'proses' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                    'selesai' => 'bg-green-50 text-green-800 border-green-200',
                    'batal' => 'bg-red-50 text-red-800 border-red-200',
                    default => 'bg-gray-50 text-gray-800 border-gray-200'
                };
                $statusLabel = match($perbaikanKontainer->status) {
                    'pending' => 'Menunggu Antrean (Pending)',
                    'proses' => 'Sedang Dalam Proses Perbaikan',
                    'selesai' => 'Selesai Diperbaiki',
                    'batal' => 'Perbaikan Dibatalkan',
                    default => ucfirst($perbaikanKontainer->status)
                };
                $statusIcon = match($perbaikanKontainer->status) {
                    'pending' => 'fa-clock text-gray-500',
                    'proses' => 'fa-spinner fa-spin text-yellow-500',
                    'selesai' => 'fa-check-circle text-green-500',
                    'batal' => 'fa-times-circle text-red-500',
                    default => 'fa-info-circle text-gray-500'
                };
            @endphp
            <div class="flex items-center gap-3 p-4 rounded-xl border {{ $statusColor }}">
                <i class="fas {{ $statusIcon }} text-xl"></i>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider opacity-75">Status Pengerjaan</p>
                    <p class="text-sm font-bold">{{ $statusLabel }}</p>
                </div>
            </div>

            <!-- Core Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Panel: Container Info -->
                <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3 pb-1.5 border-b border-gray-200">
                        <i class="fas fa-box text-blue-500 mr-1.5"></i> Informasi Kontainer
                    </h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Nomor Kontainer:</dt>
                            <dd class="font-bold text-gray-900">{{ $perbaikanKontainer->no_kontainer }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Ukuran:</dt>
                            <dd class="text-gray-900 font-semibold">
                                {{ $perbaikanKontainer->ukuran ? $perbaikanKontainer->ukuran . ' Feet' : '-' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Tipe Kontainer:</dt>
                            <dd class="text-gray-900 font-semibold">{{ $perbaikanKontainer->tipe_kontainer ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Middle Panel: Vendor & Shop Info -->
                <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3 pb-1.5 border-b border-gray-200">
                        <i class="fas fa-store-alt text-indigo-500 mr-1.5"></i> Informasi Bengkel
                    </h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Nama Bengkel:</dt>
                            <dd class="font-bold text-gray-900">{{ $perbaikanKontainer->bengkel->nama_bengkel ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Kode Bengkel:</dt>
                            <dd class="text-gray-900 font-semibold">{{ $perbaikanKontainer->bengkel->kode ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Catatan Vendor:</dt>
                            <dd class="text-gray-900 italic text-right max-w-xs truncate">{{ $perbaikanKontainer->bengkel->keterangan ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Right Panel: Paint Info -->
                <div class="border border-gray-200 rounded-xl p-4 bg-blue-50/30">
                    <h3 class="text-sm font-bold text-blue-900 uppercase tracking-wider mb-3 pb-1.5 border-b border-blue-200">
                        <i class="fas fa-paint-roller text-blue-500 mr-1.5"></i> Informasi Pengecatan
                    </h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Menggunakan Cat:</dt>
                            <dd class="font-bold {{ $perbaikanKontainer->is_cat ? 'text-green-600' : 'text-red-500' }}">
                                {{ $perbaikanKontainer->is_cat ? 'Ya' : 'Tidak' }}
                            </dd>
                        </div>
                        @if($perbaikanKontainer->is_cat)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Vendor Cat:</dt>
                            <dd class="text-gray-900 font-semibold">{{ $perbaikanKontainer->vendor_cat ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Biaya Cat:</dt>
                            <dd class="text-gray-900 font-bold">Rp {{ number_format($perbaikanKontainer->biaya_cat, 0, ',', '.') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Cost & Date Info Table -->
            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 font-bold text-sm text-gray-700">
                    <i class="fas fa-file-invoice-dollar text-green-600 mr-1.5"></i> Rincian Biaya & Jadwal Pengerjaan
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                    <!-- Dates Column -->
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Tanggal Masuk:</span>
                            <span class="font-semibold text-gray-900">{{ $perbaikanKontainer->tanggal_masuk ? $perbaikanKontainer->tanggal_masuk->format('d F Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Tanggal Selesai / Keluar:</span>
                            <span class="font-semibold text-gray-900">{{ $perbaikanKontainer->tanggal_keluar ? $perbaikanKontainer->tanggal_keluar->format('d F Y') : '-' }}</span>
                        </div>
                    </div>
                    
                    <!-- Cost Column -->
                    <div class="p-4 space-y-3 bg-gray-50/50">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Estimasi Biaya:</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($perbaikanKontainer->estimasi_biaya, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Biaya Riil (Aktual):</span>
                            <span class="font-extrabold text-green-700 text-base">
                                @if($perbaikanKontainer->status === 'selesai')
                                    Rp {{ number_format($perbaikanKontainer->biaya_riil, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Description -->
            <div class="border border-gray-200 rounded-xl p-4">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 pb-1 border-b border-gray-150"><i class="fas fa-exclamation-circle text-red-500 mr-1.5"></i>Keterangan Kerusakan</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line bg-gray-50 p-3 rounded-lg border border-gray-100">{{ $perbaikanKontainer->keterangan_kerusakan }}</p>
            </div>

            <!-- Repair Resolution (only if completed or canceled) -->
            @if($perbaikanKontainer->keterangan_perbaikan)
            <div class="border border-green-200 rounded-xl p-4 bg-green-50/20">
                <h3 class="text-sm font-bold text-green-800 uppercase tracking-wider mb-2 pb-1 border-b border-green-150"><i class="fas fa-check-circle text-green-500 mr-1.5"></i>Keterangan Tindakan Perbaikan</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line bg-white p-3 rounded-lg border border-green-100">{{ $perbaikanKontainer->keterangan_perbaikan }}</p>
            </div>
            @endif

            <!-- Audit Trail / Metadata Info -->
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-150 text-xs text-gray-500 space-y-2">
                <h4 class="font-semibold uppercase tracking-wider text-gray-400 mb-2"><i class="fas fa-info mr-1"></i>Informasi Transaksi</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <span class="font-medium text-gray-400">Dibuat Oleh:</span> 
                        {{ $perbaikanKontainer->creator->username ?? '-' }} 
                        ({{ $perbaikanKontainer->created_at ? $perbaikanKontainer->created_at->format('d/m/Y H:i') : '-' }})
                    </div>
                    <div>
                        <span class="font-medium text-gray-400">Terakhir Diperbarui:</span> 
                        {{ $perbaikanKontainer->updater->username ?? '-' }} 
                        ({{ $perbaikanKontainer->updated_at ? $perbaikanKontainer->updated_at->format('d/m/Y H:i') : '-' }})
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
