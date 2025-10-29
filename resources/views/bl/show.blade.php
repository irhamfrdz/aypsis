@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-file-contract mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Detail Bill of Lading</h1>
                    <p class="text-gray-600">BL ID: #{{ $bl->id }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('bl.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Daftar BL
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Informasi Kontainer --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-box mr-2 text-blue-600"></i>
                Informasi Kontainer
            </h3>
            
            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Nomor BL:</span>
                    <span class="text-gray-900 font-semibold">{{ $bl->nomor_bl ?: '-' }}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Nomor Kontainer:</span>
                    <span class="text-gray-900">{{ $bl->nomor_kontainer ?: '-' }}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">No Seal:</span>
                    <span class="text-gray-900">{{ $bl->no_seal ?: '-' }}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Tipe Kontainer:</span>
                    <span class="text-gray-900">{{ $bl->tipe_kontainer ?: '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Informasi Kapal --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-ship mr-2 text-green-600"></i>
                Informasi Kapal
            </h3>
            
            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Nama Kapal:</span>
                    <span class="text-gray-900">{{ $bl->nama_kapal }}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">No Voyage:</span>
                    <span class="text-gray-900">{{ $bl->no_voyage }}</span>
                </div>
            </div>
        </div>

        {{-- Informasi Barang --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-boxes mr-2 text-orange-600"></i>
                Informasi Barang
            </h3>
            
            <div class="space-y-4">
                <div class="py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600 block mb-1">Nama Barang:</span>
                    <span class="text-gray-900">{{ $bl->nama_barang ?: '-' }}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Tonnage:</span>
                    <span class="text-gray-900">{{ $bl->tonnage ? number_format($bl->tonnage, 2) . ' Ton' : '-' }}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Kuantitas:</span>
                    <span class="text-gray-900">{{ $bl->kuantitas ?: '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Informasi Prospek Terkait --}}
        @if($bl->prospek)
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-link mr-2 text-purple-600"></i>
                Prospek Terkait
            </h3>
            
            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">ID Prospek:</span>
                    <span class="text-gray-900">#{{ $bl->prospek->id }}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Status:</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $bl->prospek->status === 'aktif' ? 'bg-green-100 text-green-800' : 
                           ($bl->prospek->status === 'sudah_muat' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst(str_replace('_', ' ', $bl->prospek->status)) }}
                    </span>
                </div>
                
                @if($bl->prospek->nama_supir)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Nama Supir:</span>
                    <span class="text-gray-900">{{ $bl->prospek->nama_supir }}</span>
                </div>
                @endif

                @if($bl->prospek->tujuan_pengiriman)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Tujuan:</span>
                    <span class="text-gray-900">{{ $bl->prospek->tujuan_pengiriman }}</span>
                </div>
                @endif
                
                <div class="pt-4">
                    <a href="{{ route('prospek.show', $bl->prospek) }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Lihat Detail Prospek
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- Informasi Timestamp --}}
        <div class="bg-white rounded-lg shadow-sm p-6 {{ $bl->prospek ? '' : 'lg:col-span-1' }}">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-clock mr-2 text-gray-600"></i>
                Informasi Waktu
            </h3>
            
            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Dibuat:</span>
                    <span class="text-gray-900">{{ $bl->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Diupdate:</span>
                    <span class="text-gray-900">{{ $bl->updated_at->format('d/m/Y H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection