@extends('layouts.app')

@section('title', 'Detail Gudang Ban')
@section('page_title', 'Detail Gudang Ban')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-t-lg shadow-sm p-6 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Detail Gudang Ban</h1>
                    <p class="text-gray-600 text-sm mt-1">Informasi lengkap gudang ban</p>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('master-gudang-ban.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    @if(auth()->user()->can('master-gudang-ban-edit') || auth()->user()->can('stock-ban-update'))
                    <a href="{{ route('master-gudang-ban.edit', $masterGudangBan->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-md text-sm transition duration-200">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detail Content -->
        <div class="bg-white rounded-b-lg shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Gudang</h3>
                    <p class="text-gray-900 font-medium">{{ $masterGudangBan->nama_gudang }}</p>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Lokasi</h3>
                    <p class="text-gray-900 font-medium">{{ $masterGudangBan->lokasi ?: '-' }}</p>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Keterangan</h3>
                    <p class="text-gray-900">{{ $masterGudangBan->keterangan ?: '-' }}</p>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</h3>
                    @if($masterGudangBan->status == 'aktif')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>
                            Nonaktif
                        </span>
                    @endif
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Dibuat Pada</h3>
                    <p class="text-gray-900 text-sm">{{ $masterGudangBan->created_at ? $masterGudangBan->created_at->format('d M Y H:i') : '-' }}</p>
                    @if($masterGudangBan->createdBy)
                        <p class="text-xs text-gray-500">Oleh: {{ $masterGudangBan->createdBy->username }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
