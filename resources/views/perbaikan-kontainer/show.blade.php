@extends('layouts.app')

@section('title', 'Detail Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Detail Perbaikan Kontainer</h1>
                    <p class="text-gray-600 mt-1">Informasi lengkap perbaikan kontainer</p>
                </div>
                <div class="flex space-x-2">
                    @can('perbaikan-kontainer.update')
                    <a href="{{ route('perbaikan-kontainer.edit', $perbaikanKontainer) }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    @endcan
                    <a href="{{ route('perbaikan-kontainer.index') }}"
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
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $perbaikanKontainer->status_color }}">
                    {{ $perbaikanKontainer->status_label }}
                </span>
            </div>

            <!-- Detail Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kontainer Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kontainer</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nomor Kontainer</label>
                            <p class="text-sm text-gray-900">{{ $perbaikanKontainer->kontainer->nomor_kontainer ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Ukuran</label>
                            <p class="text-sm text-gray-900">{{ $perbaikanKontainer->kontainer->ukuran ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Perbaikan Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Perbaikan</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal Perbaikan</label>
                            <p class="text-sm text-gray-900">
                                {{ $perbaikanKontainer->tanggal_perbaikan ? \Carbon\Carbon::parse($perbaikanKontainer->tanggal_perbaikan)->format('d F Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal Selesai</label>
                            <p class="text-sm text-gray-900">
                                {{ $perbaikanKontainer->tanggal_selesai ? \Carbon\Carbon::parse($perbaikanKontainer->tanggal_selesai)->format('d F Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Status</label>
                            <p class="text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $perbaikanKontainer->status_color }}">
                                    {{ $perbaikanKontainer->status_label }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kerusakan Details -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Kerusakan</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Estimasi Kerusakan Kontainer</label>
                        <p class="text-sm text-gray-900 bg-white p-3 rounded border">{{ $perbaikanKontainer->estimasi_kerusakan_kontainer }}</p>
                    </div>
                    @if($perbaikanKontainer->deskripsi_perbaikan)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Deskripsi Kerusakan</label>
                        <p class="text-sm text-gray-900 bg-white p-3 rounded border">{{ $perbaikanKontainer->deskripsi_perbaikan }}</p>
                    </div>
                    @endif
                    @if($perbaikanKontainer->realisasi_kerusakan)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Realisasi Kerusakan Kontainer</label>
                        <p class="text-sm text-gray-900 bg-white p-3 rounded border">{{ $perbaikanKontainer->realisasi_kerusakan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Biaya Information -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Biaya</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Estimasi Biaya Perbaikan</label>
                        <p class="text-lg font-semibold text-green-600">
                            Rp {{ number_format($perbaikanKontainer->estimasi_biaya_perbaikan ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Realisasi Biaya Perbaikan</label>
                        <p class="text-lg font-semibold text-blue-600">
                            Rp {{ number_format($perbaikanKontainer->realisasi_biaya_perbaikan ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Catatan -->
            @if($perbaikanKontainer->catatan)
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Catatan</h3>
                <p class="text-sm text-gray-900 bg-white p-3 rounded border">{{ $perbaikanKontainer->catatan }}</p>
            </div>
            @endif

            <!-- Status Update Form (for quick status changes) -->
            @can('master-perbaikan-kontainer.update')
            <div class="mt-6 bg-blue-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Status</h3>
                <form action="{{ route('master.perbaikan-kontainer.update-status', $perbaikanKontainer) }}" method="POST" class="flex items-center space-x-4">
                    @csrf
                    @method('PATCH')
                    <div class="flex-1">
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="pending" {{ $perbaikanKontainer->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $perbaikanKontainer->status == 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                            <option value="completed" {{ $perbaikanKontainer->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $perbaikanKontainer->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Update Status
                    </button>
                </form>
            </div>
            @endcan

            <!-- Delete Button -->
            @can('perbaikan-kontainer.delete')
            <div class="mt-6 pt-6 border-t">
                <div class="flex justify-end">
                    <form method="POST" action="{{ route('perbaikan-kontainer.destroy', $perbaikanKontainer) }}"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data perbaikan ini? Tindakan ini tidak dapat dibatalkan.')"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Perbaikan
                        </button>
                    </form>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection
