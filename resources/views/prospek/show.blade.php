@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-eye mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Detail Prospek</h1>
                    <p class="text-gray-600">Informasi detail data prospek pengiriman kontainer</p>
                </div>
            </div>
            <a href="{{ route('prospek.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- Detail Content --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Left Column - Main Information --}}
            <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Utama</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">ID Prospek</label>
                            <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded">{{ $prospek->id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Tanggal</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <i class="fas fa-calendar mr-2 text-blue-600"></i>
                                {{ $prospek->tanggal ? $prospek->tanggal->format('d F Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <p class="mt-1">
                                @php
                                    $statusColors = [
                                        'aktif' => 'bg-green-100 text-green-800',
                                        'sudah_muat' => 'bg-blue-100 text-blue-800',
                                        'batal' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusLabels = [
                                        'aktif' => 'Aktif',
                                        'sudah_muat' => 'Sudah Muat',
                                        'batal' => 'Batal'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$prospek->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$prospek->status] ?? $prospek->status }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pengiriman</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nama Supir</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <i class="fas fa-user mr-2 text-green-600"></i>
                                {{ $prospek->nama_supir ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">PT/Pengirim</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <i class="fas fa-building mr-2 text-purple-600"></i>
                                {{ $prospek->pt_pengirim ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Tujuan Pengiriman</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>
                                {{ $prospek->tujuan_pengiriman ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nama Kapal</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <i class="fas fa-ship mr-2 text-blue-600"></i>
                                {{ $prospek->nama_kapal ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Volume & Tonase Information --}}
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-cube mr-2 text-orange-600"></i>
                        Volume & Tonase
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-cube text-blue-600 mr-2"></i>
                                <h4 class="text-sm font-medium text-blue-800">Total Volume</h4>
                            </div>
                            <div class="text-xl font-bold text-blue-900">
                                @if($prospek->total_volume)
                                    {{ rtrim(rtrim(number_format($prospek->total_volume, 3, '.', ','), '0'), '.') }} m³
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-weight-hanging text-green-600 mr-2"></i>
                                <h4 class="text-sm font-medium text-green-800">Total Tonase</h4>
                            </div>
                            <div class="text-xl font-bold text-green-900">
                                @if($prospek->total_ton)
                                    {{ rtrim(rtrim(number_format($prospek->total_ton, 3, '.', ','), '0'), '.') }} Ton
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - Container Information --}}
            <div class="space-y-6">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kontainer</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Barang</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <i class="fas fa-box mr-2 text-orange-600"></i>
                                {{ $prospek->barang ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Ukuran Kontainer</label>
                            <p class="mt-1">
                                @if($prospek->ukuran)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                        {{ $prospek->ukuran == '20' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        <i class="fas fa-container mr-2"></i>
                                        {{ $prospek->ukuran }} Feet
                                    </span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nomor Kontainer</label>
                            <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded">
                                {{ $prospek->nomor_kontainer ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nomor Seal</label>
                            <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded">
                                {{ $prospek->no_seal ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Keterangan</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        @if($prospek->keterangan)
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $prospek->keterangan }}</p>
                        @else
                            <p class="text-sm text-gray-500 italic">Tidak ada keterangan tambahan</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Audit Information --}}
        <div class="mt-8 border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Audit</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                        <h4 class="text-sm font-medium text-blue-800">Dibuat</h4>
                    </div>
                    <div class="text-sm text-blue-700">
                        <p><strong>Oleh:</strong> {{ $prospek->createdBy->name ?? 'System' }}</p>
                        <p><strong>Tanggal:</strong> {{ $prospek->created_at ? $prospek->created_at->format('d F Y H:i') : '-' }}</p>
                    </div>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-edit text-green-600 mr-2"></i>
                        <h4 class="text-sm font-medium text-green-800">Terakhir Diperbarui</h4>
                    </div>
                    <div class="text-sm text-green-700">
                        <p><strong>Oleh:</strong> {{ $prospek->updatedBy->name ?? 'System' }}</p>
                        <p><strong>Tanggal:</strong> {{ $prospek->updated_at ? $prospek->updated_at->format('d F Y H:i') : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 flex justify-center">
            <a href="{{ route('prospek.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-md transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar Prospek
            </a>
        </div>
    </div>
</div>
@endsection
