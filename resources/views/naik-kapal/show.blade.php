@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-eye mr-3 text-purple-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Detail Prospek</h1>
                    <p class="text-gray-600">Informasi lengkap kontainer prospek #{{ $naikKapal->id }}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('naik-kapal.edit', $naikKapal->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('naik-kapal.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700">Informasi Kontainer</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                        <div>
                            <p class="text-sm text-gray-500">Nomor Kontainer</p>
                            <p class="text-lg font-bold text-gray-900">{{ $naikKapal->nomor_kontainer }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nomor Seal</p>
                            <p class="text-lg font-semibold text-blue-600">{{ $naikKapal->no_seal ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tipe & Ukuran</p>
                            <p class="text-gray-900">{{ $naikKapal->tipe_kontainer }} / {{ $naikKapal->ukuran_kontainer }}</p>
                            @if($naikKapal->size_kontainer)
                                <p class="text-xs text-purple-600">Manual Size: {{ $naikKapal->size_kontainer }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status Naiknya</p>
                            @php
                                $statusColors = [
                                    'menunggu' => 'bg-yellow-100 text-yellow-800',
                                    'dimuat' => 'bg-blue-100 text-blue-800',
                                    'selesai' => 'bg-green-100 text-green-800',
                                    'Moved to BLS' => 'bg-purple-100 text-purple-800',
                                    'batal' => 'bg-red-100 text-red-800',
                                ];
                                $statusColor = $statusColors[$naikKapal->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                                {{ ucfirst($naikKapal->status) }}
                            </span>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500">Jenis Barang</p>
                            <p class="text-gray-900">{{ $naikKapal->jenis_barang ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700">Pelayaran & Logistik</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                        <div>
                            <p class="text-sm text-gray-500">Kapal</p>
                            <p class="text-gray-900 font-medium">{{ $naikKapal->nama_kapal }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Voyage</p>
                            <p class="text-gray-900 font-medium">{{ $naikKapal->no_voyage ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Rute</p>
                            <p class="text-gray-900">{{ $naikKapal->pelabuhan_asal ?: '?' }} → {{ $naikKapal->pelabuhan_tujuan ?: '?' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Waktu Muat</p>
                            <p class="text-gray-900">
                                {{ $naikKapal->tanggal_muat ? $naikKapal->tanggal_muat->format('d/m/Y') : '-' }}
                                @if($naikKapal->jam_muat)
                                    <span class="text-gray-500 text-sm ml-1">{{ \Carbon\Carbon::parse($naikKapal->jam_muat)->format('H:i') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($naikKapal->keterangan)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700">Keterangan</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 whitespace-pre-line">{{ $naikKapal->keterangan }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="bg-purple-600 px-6 py-4">
                    <h3 class="font-bold text-white">Data Prospek Link</h3>
                </div>
                <div class="p-6">
                    @if($naikKapal->prospek)
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Supir</p>
                                <p class="text-gray-900 font-medium">{{ $naikKapal->prospek->nama_supir }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Pengirim</p>
                                <p class="text-gray-900 font-medium">{{ $naikKapal->prospek->pt_pengirim }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Surat Jalan</p>
                                <p class="text-gray-900 font-medium">{{ $naikKapal->prospek->no_surat_jalan ?: '-' }}</p>
                            </div>
                            @if($naikKapal->prospek->tandaTerima)
                            <div class="pt-2">
                                <a href="{{ route('tanda-terima.show', $naikKapal->prospek->tandaTerima->id) }}" class="text-purple-600 hover:text-purple-800 text-sm font-bold">
                                    <i class="fas fa-external-link-alt mr-1"></i> Lihat Tanda Terima
                                </a>
                            </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 italic">Tidak ada link prospek</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700">Metadata</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Dibuat Oleh</p>
                        <p class="text-sm text-gray-900">{{ $naikKapal->createdBy->name ?? 'System' }}</p>
                        <p class="text-xs text-gray-400">{{ $naikKapal->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($naikKapal->updated_by)
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Terakhir Update</p>
                        <p class="text-sm text-gray-900">{{ $naikKapal->updatedBy->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-400">{{ $naikKapal->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
