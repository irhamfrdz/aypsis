@extends('layouts.app')

@section('title', 'Detail Tagihan CAT')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Detail Tagihan CAT</h1>
                    <p class="text-gray-600 mt-1">Informasi lengkap tagihan Container Annual Test</p>
                </div>
                <div class="flex space-x-2">
                    @can('tagihan-cat-update')
                    <a href="{{ route('tagihan-cat.edit', $tagihanCat) }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    @endcan
                    <a href="{{ route('tagihan-cat.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="mb-6">
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $tagihanCat->status_color }}">
                    {{ $tagihanCat->status_label }}
                </span>
            </div>

            <!-- Detail Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kontainer Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kontainer</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nomor Tagihan CAT</label>
                            <p class="text-sm text-gray-900">{{ $tagihanCat->nomor_tagihan_cat ?? $tagihanCat->id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nomor Kontainer</label>
                            <p class="text-sm text-gray-900">{{ $tagihanCat->nomor_kontainer }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Vendor</label>
                            <p class="text-sm text-gray-900">{{ $tagihanCat->vendor ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tagihan Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Tagihan</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal CAT</label>
                            <p class="text-sm text-gray-900">
                                {{ $tagihanCat->tanggal_cat ? \Carbon\Carbon::parse($tagihanCat->tanggal_cat)->format('d F Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Status</label>
                            <p class="text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $tagihanCat->status_color }}">
                                    {{ $tagihanCat->status_label }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Estimasi Biaya</label>
                            <p class="text-lg font-semibold text-blue-600">
                                Rp {{ number_format($tagihanCat->estimasi_biaya ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Realisasi Biaya</label>
                            <p class="text-lg font-semibold text-green-600">
                                Rp {{ number_format($tagihanCat->realisasi_biaya ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keterangan -->
            @if($tagihanCat->keterangan)
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Keterangan</h3>
                <p class="text-sm text-gray-900 bg-white p-3 rounded border">{{ $tagihanCat->keterangan }}</p>
            </div>
            @endif

            <!-- Audit Information -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Audit</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Dibuat Oleh</label>
                        <p class="text-sm text-gray-900">{{ $tagihanCat->creator->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $tagihanCat->created_at ? \Carbon\Carbon::parse($tagihanCat->created_at)->format('d F Y H:i') : '-' }}
                        </p>
                    </div>
                    @if($tagihanCat->updater)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Terakhir Diupdate Oleh</label>
                        <p class="text-sm text-gray-900">{{ $tagihanCat->updater->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $tagihanCat->updated_at ? \Carbon\Carbon::parse($tagihanCat->updated_at)->format('d F Y H:i') : '-' }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status Update Form (for quick status changes) -->
            @can('tagihan-cat-update')
            <div class="mt-6 bg-blue-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Status</h3>
                <form action="{{ route('tagihan-cat.update', $tagihanCat) }}" method="POST" class="flex items-center space-x-4">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="nomor_kontainer" value="{{ $tagihanCat->nomor_kontainer }}">
                    <input type="hidden" name="vendor" value="{{ $tagihanCat->vendor }}">
                    <input type="hidden" name="tanggal_tagihan" value="{{ $tagihanCat->tanggal_tagihan->format('Y-m-d') }}">
                    <input type="hidden" name="jumlah_raw" value="{{ $tagihanCat->jumlah }}">
                    <input type="hidden" name="keterangan" value="{{ $tagihanCat->keterangan }}">
                    <input type="hidden" name="perbaikan_kontainer_id" value="{{ $tagihanCat->perbaikan_kontainer_id }}">
                    <div class="flex-1">
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="pending" {{ $tagihanCat->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $tagihanCat->status == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                            <option value="cancelled" {{ $tagihanCat->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Update Status
                    </button>
                </form>
            </div>
            @endcan

            <!-- Delete Button -->
            @can('tagihan-cat-delete')
            <div class="mt-6 pt-6 border-t">
                <div class="flex justify-end">
                    <form method="POST" action="{{ route('tagihan-cat.destroy', $tagihanCat) }}"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan CAT ini? Tindakan ini tidak dapat dibatalkan.')"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Tagihan
                        </button>
                    </form>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection
