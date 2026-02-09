@extends('layouts.app')

@section('title', 'Detail Alat Berat')
@section('page_title', 'Detail Alat Berat')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">Detail Alat Berat: {{ $alatBerat->nama }}</h2>
        <div class="flex space-x-2">
            <a href="{{ route('master.alat-berat.edit', $alatBerat->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            <a href="{{ route('master.alat-berat.index') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Informasi Utama</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Kode Alat</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono bg-white px-2 py-1 rounded border border-gray-200 inline-block">{{ $alatBerat->kode_alat }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($alatBerat->status == 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @elseif($alatBerat->status == 'maintenance')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Maintenance
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Nama Alat</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $alatBerat->nama }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Jenis</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $alatBerat->jenis ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $alatBerat->lokasi ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Spesifikasi Detail</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Merek</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $alatBerat->merk ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Tipe/Model</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $alatBerat->tipe ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Nomor Seri</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $alatBerat->nomor_seri ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Tahun Pembuatan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $alatBerat->tahun_pembuatan ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Keterangan</dt>
                        <dd class="mt-1 text-sm text-gray-900 bg-white p-2 rounded border border-gray-200 min-h-[60px]">{{ $alatBerat->keterangan ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-6 border-t border-gray-200 pt-4">
            <h3 class="text-md font-medium text-gray-900 mb-3">Informasi Sistem</h3>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-3">
                <div class="sm:col-span-1">
                    <dt class="text-xs font-medium text-gray-500">Dibuat Pada</dt>
                    <dd class="mt-1 text-xs text-gray-900">{{ $alatBerat->created_at->format('d M Y H:i') }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-xs font-medium text-gray-500">Terakhir Diupdate</dt>
                    <dd class="mt-1 text-xs text-gray-900">{{ $alatBerat->updated_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
