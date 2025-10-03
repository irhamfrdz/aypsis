@extends('layouts.app')

@section('title', 'Detail Stock Kontainer')
@section('page_title', 'Detail Stock Kontainer')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Detail Stock Kontainer</h2>
            <div class="flex space-x-3">
                <a href="{{ route('master.stock-kontainer.edit', $stockKontainer) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('master.stock-kontainer.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <!-- Header Info -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">{{ $stockKontainer->nomor_kontainer }}</h3>
                        <p class="text-blue-100 text-sm">{{ $stockKontainer->tipe_kontainer ?? 'Tipe belum ditentukan' }}</p>
                    </div>
                    <div class="flex space-x-2">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $stockKontainer->status_badge }}">
                            @switch($stockKontainer->status)
                                @case('available')
                                    Tersedia
                                    @break
                                @case('rented')
                                    Disewa
                                    @break
                                @case('maintenance')
                                    Perbaikan
                                    @break
                                @case('damaged')
                                    Rusak
                                    @break
                                @default
                                    {{ ucfirst(str_replace('_', ' ', $stockKontainer->status)) }}
                            @endswitch
                        </span>
                    </div>
                </div>
            </div>

            <!-- Detail Information -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informasi Dasar -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nomor Kontainer</dt>
                                <dd class="text-sm text-gray-900">{{ $stockKontainer->nomor_kontainer }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ukuran</dt>
                                <dd class="text-sm text-gray-900">{{ $stockKontainer->ukuran ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipe Kontainer</dt>
                                <dd class="text-sm text-gray-900">{{ $stockKontainer->tipe_kontainer ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nomor Seri</dt>
                                <dd class="text-sm text-gray-900">{{ $stockKontainer->nomor_seri ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tahun Pembuatan</dt>
                                <dd class="text-sm text-gray-900">{{ $stockKontainer->tahun_pembuatan ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Status & Informasi -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Status & Informasi</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $stockKontainer->status_badge }}">
                                        @switch($stockKontainer->status)
                                            @case('available')
                                                Tersedia
                                                @break
                                            @case('rented')
                                                Disewa
                                                @break
                                            @case('maintenance')
                                                Perbaikan
                                                @break
                                            @case('damaged')
                                                Rusak
                                                @break
                                            @default
                                                {{ ucfirst(str_replace('_', ' ', $stockKontainer->status)) }}
                                        @endswitch
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Masuk</dt>
                                <dd class="text-sm text-gray-900">{{ $stockKontainer->tanggal_masuk?->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Keluar</dt>
                                <dd class="text-sm text-gray-900">{{ $stockKontainer->tanggal_keluar?->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Informasi Sewa -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Sewa</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Harga Sewa per Hari</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ $stockKontainer->harga_sewa_per_hari ? 'Rp ' . number_format($stockKontainer->harga_sewa_per_hari, 0, ',', '.') : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Harga Sewa per Bulan</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ $stockKontainer->harga_sewa_per_bulan ? 'Rp ' . number_format($stockKontainer->harga_sewa_per_bulan, 0, ',', '.') : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pemilik</dt>
                                <dd class="text-sm text-gray-900">{{ $stockKontainer->pemilik ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Keterangan</h4>
                        <div class="text-sm text-gray-900">
                            {{ $stockKontainer->keterangan ?? 'Tidak ada keterangan' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Dibuat: {{ $stockKontainer->created_at->format('d/m/Y H:i') }} |
                    Diupdate: {{ $stockKontainer->updated_at->format('d/m/Y H:i') }}
                </div>
                <div class="flex space-x-3">
                    <form action="{{ route('master.stock-kontainer.destroy', $stockKontainer) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah anda yakin ingin menghapus stock kontainer ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
