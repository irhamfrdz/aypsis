@extends('layouts.app')

@section('title', 'Detail Vendor Kontainer Sewa')
@section('page_title', 'Detail Vendor Kontainer Sewa')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Detail Vendor Kontainer Sewa</h1>
                            <p class="text-blue-100 text-sm">Lihat informasi lengkap vendor kontainer sewa</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('vendor-kontainer-sewa.edit', $vendorKontainerSewa) }}"
                           class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        <a href="{{ route('vendor-kontainer-sewa.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Information -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Informasi Vendor</h2>
                    @if($vendorKontainerSewa->status === 'aktif')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Non-Aktif
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kode Vendor</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $vendorKontainerSewa->kode }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Vendor</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $vendorKontainerSewa->nama_vendor }}</dd>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($vendorKontainerSewa->status === 'aktif')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Non-Aktif
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID Vendor</dt>
                            <dd class="mt-1 text-sm text-gray-500">#{{ $vendorKontainerSewa->id }}</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Catatan -->
        @if($vendorKontainerSewa->catatan)
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Catatan</h3>
                </div>
                <div class="p-6">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($vendorKontainerSewa->catatan)) !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Metadata -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Sistem</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dibuat pada</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $vendorKontainerSewa->created_at->format('d F Y, H:i') }}
                            </div>
                            <span class="text-xs text-gray-500">
                                ({{ $vendorKontainerSewa->created_at->diffForHumans() }})
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Terakhir diupdate</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                {{ $vendorKontainerSewa->updated_at->format('d F Y, H:i') }}
                            </div>
                            <span class="text-xs text-gray-500">
                                ({{ $vendorKontainerSewa->updated_at->diffForHumans() }})
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex items-center justify-between">
            <a href="{{ route('vendor-kontainer-sewa.index') }}"
               class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                Kembali ke Daftar
            </a>

            <div class="flex space-x-3">
                <a href="{{ route('vendor-kontainer-sewa.edit', $vendorKontainerSewa) }}"
                   class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Vendor
                </a>

                <form action="{{ route('vendor-kontainer-sewa.destroy', $vendorKontainerSewa) }}"
                      method="POST"
                      class="inline-block"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus vendor ini? Tindakan ini tidak dapat dibatalkan!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Vendor
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
