@extends('layouts.app')

@section('title', 'Detail Pranota Perbaikan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Detail Pranota Perbaikan Kontainer</h1>
                    <p class="text-gray-600 mt-1">Nomor Pranota: {{ $pranotaPerbaikanKontainer->nomor_pranota ?? 'Belum ada nomor' }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('pranota-perbaikan-kontainer.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    @can('pranota-perbaikan-kontainer.edit')
                    <a href="{{ route('pranota-perbaikan-kontainer.edit', $pranotaPerbaikanKontainer) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    @endcan
                    @can('pranota-perbaikan-kontainer-print')
                    <a href="{{ route('pranota-perbaikan-kontainer.print', $pranotaPerbaikanKontainer) }}" target="_blank"
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Pranota Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Informasi Pranota</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor Pranota</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $pranotaPerbaikanKontainer->nomor_pranota ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Pranota</label>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $pranotaPerbaikanKontainer->tanggal_pranota ? \Carbon\Carbon::parse($pranotaPerbaikanKontainer->tanggal_pranota)->format('d/m/Y') : '-' }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Teknisi/Vendor</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $pranotaPerbaikanKontainer->nama_teknisi ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Biaya</label>
                    <p class="mt-1 text-sm font-semibold text-green-600">
                        {{ $pranotaPerbaikanKontainer->total_biaya ? 'Rp ' . number_format($pranotaPerbaikanKontainer->total_biaya, 0, ',', '.') : '-' }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <p class="mt-1">
                        @if($pranotaPerbaikanKontainer->status == 'draft')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                Draft
                            </span>
                        @elseif($pranotaPerbaikanKontainer->status == 'approved')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Disetujui
                            </span>
                        @elseif($pranotaPerbaikanKontainer->status == 'in_progress')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Dalam Proses
                            </span>
                        @elseif($pranotaPerbaikanKontainer->status == 'completed')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                Selesai
                            </span>
                        @elseif($pranotaPerbaikanKontainer->status == 'cancelled')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Dibatalkan
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ ucfirst($pranotaPerbaikanKontainer->status ?? 'Unknown') }}
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dibuat Oleh</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $pranotaPerbaikanKontainer->creator->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">{{ $pranotaPerbaikanKontainer->created_at ? $pranotaPerbaikanKontainer->created_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
            </div>

            @if($pranotaPerbaikanKontainer->deskripsi_pekerjaan)
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700">Deskripsi Pekerjaan</label>
                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $pranotaPerbaikanKontainer->deskripsi_pekerjaan }}</p>
            </div>
            @endif

            @if($pranotaPerbaikanKontainer->catatan)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Catatan</label>
                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $pranotaPerbaikanKontainer->catatan }}</p>
            </div>
            @endif
        </div>

        <!-- Daftar Tagihan Perbaikan Kontainer -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Daftar Tagihan Perbaikan Kontainer</h2>
                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                    {{ $pranotaPerbaikanKontainer->perbaikanKontainers->count() }} item{{ $pranotaPerbaikanKontainer->perbaikanKontainers->count() != 1 ? 's' : '' }}
                </span>
            </div>

            @if($pranotaPerbaikanKontainer->perbaikanKontainers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Tagihan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Kontainer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Perbaikan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi Perbaikan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pranotaPerbaikanKontainer->perbaikanKontainers as $index => $perbaikan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $perbaikan->nomor_tagihan ?? '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $perbaikan->nomor_kontainer ?? '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $perbaikan->tanggal_perbaikan ? \Carbon\Carbon::parse($perbaikan->tanggal_perbaikan)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 max-w-xs">
                                    <div class="truncate" title="{{ $perbaikan->deskripsi_perbaikan }}">
                                        {{ $perbaikan->deskripsi_perbaikan ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                    {{ $perbaikan->realisasi_biaya_perbaikan ? 'Rp ' . number_format($perbaikan->realisasi_biaya_perbaikan, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                            @if($perbaikan->pivot->catatan_item)
                            <tr class="bg-gray-50">
                                <td colspan="6" class="px-4 py-2 text-sm text-gray-600">
                                    <strong>Catatan Item:</strong> {{ $perbaikan->pivot->catatan_item }}
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada tagihan perbaikan kontainer</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada tagihan perbaikan kontainer yang terkait dengan pranota ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
