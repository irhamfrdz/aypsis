@extends('layouts.app')

@section('title', 'Detail Chasis Batam - ' . $chasisBatam->kode)
@section('page_title', 'Detail Chasis Batam')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-200">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Detail Chasis</h2>
                <p class="text-xs text-gray-500 mt-0.5">Informasi lengkap data chasis.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('master.chasis-batam.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors duration-150">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
                @can('master-chasis-batam-update')
                <a href="{{ route('master.chasis-batam.edit', $chasisBatam) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors duration-150 shadow-sm">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                @endcan
            </div>
        </div>

        <div class="space-y-6">
            <!-- Informasi Utama -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Kode Chasis</span>
                    <span class="text-sm font-bold text-indigo-700">{{ $chasisBatam->kode }}</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Plat Nomor / No. Polisi</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $chasisBatam->plat_nomor ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Tipe / Ukuran Chasis</span>
                    <span class="text-sm text-gray-800">
                        @if($chasisBatam->tipe)
                            <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold border border-blue-100">{{ $chasisBatam->tipe }}</span>
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Merek</span>
                    <span class="text-sm text-gray-800 font-medium">{{ $chasisBatam->merek ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Tahun Pembuatan</span>
                    <span class="text-sm text-gray-800">{{ $chasisBatam->tahun_pembuatan ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Status</span>
                    <span>
                        @if($chasisBatam->status === 'aktif')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                Non-Aktif
                            </span>
                        @endif
                    </span>
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <span class="text-[10px] uppercase font-bold text-gray-400 block mb-1">Catatan Tambahan</span>
                <div class="bg-white p-3 rounded-lg border border-gray-200 text-xs text-gray-700 whitespace-pre-line min-h-[60px]">
                    {{ $chasisBatam->catatan ?? 'Tidak ada catatan tambahan.' }}
                </div>
            </div>

            <!-- Audit Trail -->
            <div class="pt-4 border-t border-gray-100 text-[10px] text-gray-400 grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <span>Dibuat oleh: </span>
                    <strong class="text-gray-600">{{ $chasisBatam->creator ? $chasisBatam->creator->name : '-' }}</strong>
                    <span>pada {{ $chasisBatam->created_at ? $chasisBatam->created_at->format('d/m/Y H:i') : '-' }}</span>
                </div>
                @if($chasisBatam->updated_at != $chasisBatam->created_at)
                    <div class="md:text-right">
                        <span>Terakhir diubah oleh: </span>
                        <strong class="text-gray-600">{{ $chasisBatam->updater ? $chasisBatam->updater->name : '-' }}</strong>
                        <span>pada {{ $chasisBatam->updated_at ? $chasisBatam->updated_at->format('d/m/Y H:i') : '-' }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
