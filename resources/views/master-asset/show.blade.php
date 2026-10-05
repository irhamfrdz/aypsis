@extends('layouts.app')

@section('title', 'Detail Asset - ' . $asset->kode_asset)
@section('page_title', 'Detail Asset')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('asset.index') }}" class="p-2.5 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 font-mono text-xs font-bold">{{ $asset->kode_asset }}</span>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $asset->status_badge_class }}">
                        {{ $asset->status }}
                    </span>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $asset->kondisi_badge_class }}">
                        {{ $asset->kondisi }}
                    </span>
                </div>
                <h1 class="text-xl font-bold text-gray-800 mt-1">{{ $asset->nama_asset }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if(Auth::user() && (Auth::user()->hasRole('admin') || Auth::user()->can('asset-update') || Auth::user()->can('master-asset-update')))
            <a href="{{ route('asset.edit', $asset->id) }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Asset
            </a>
            @endif
            <a href="{{ route('asset.index') }}" class="px-4 py-2 text-xs font-semibold rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                Kembali
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column: Photo & Quick Status -->
        <div class="space-y-6">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Foto Asset</h3>
                @if($asset->foto && file_exists(public_path($asset->foto)))
                    <div class="overflow-hidden rounded-xl border border-gray-100 shadow-xs aspect-square bg-gray-50 flex items-center justify-center">
                        <img src="{{ asset($asset->foto) }}" alt="{{ $asset->nama_asset }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="rounded-xl border-2 border-dashed border-gray-200 aspect-square flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs font-medium">Belum ada foto</span>
                    </div>
                @endif
            </div>

            <!-- Financial Summary Box -->
            <div class="bg-gradient-to-br from-blue-900 to-indigo-800 text-white p-5 rounded-2xl shadow-md space-y-4">
                <div>
                    <span class="text-xs text-blue-200 font-medium">Nilai Buku Saat Ini</span>
                    <h2 class="text-2xl font-bold mt-0.5">Rp {{ number_format($asset->nilai_buku, 0, ',', '.') }}</h2>
                </div>
                <div class="pt-3 border-t border-blue-700/60 grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-blue-300 block">Nilai Perolehan</span>
                        <span class="font-semibold text-white">Rp {{ number_format($asset->nilai_perolehan, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-blue-300 block">Masa Manfaat</span>
                        <span class="font-semibold text-white">{{ $asset->masa_manfaat_bulan ?: '-' }} Bulan</span>
                    </div>
                </div>
            </div>

            <!-- Lampiran Box -->
            @if($asset->lampiran && file_exists(public_path($asset->lampiran)))
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Dokumen Lampiran</h3>
                    <a href="{{ asset($asset->lampiran) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 border border-gray-200 transition-colors group">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="overflow-hidden">
                            <span class="text-xs font-semibold text-gray-800 block truncate">Unduh Dokumen Lampiran</span>
                            <span class="text-[10px] text-gray-400">Klik untuk melihat file</span>
                        </div>
                    </a>
                </div>
            @endif
        </div>

        <!-- Right Column: Detail Specifications -->
        <div class="md:col-span-2 space-y-6">
            <!-- Specifications Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider pb-2 border-b border-gray-100">Spesifikasi & Identifikasi</h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-gray-400 block font-medium">Kategori</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block">{{ $asset->kategori }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">Merk / Brand</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block">{{ $asset->merk ?: '-' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">Tipe / Model</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block">{{ $asset->tipe_model ?: '-' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">Nomor Seri</span>
                        <span class="font-mono font-semibold text-gray-800 mt-0.5 block">{{ $asset->nomor_seri ?: '-' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">Lokasi Asset</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block">{{ $asset->lokasi ?: '-' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">Penanggung Jawab (PIC)</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block">{{ $asset->pic_name }}</span>
                    </div>
                </div>
            </div>

            <!-- Financial Details Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider pb-2 border-b border-gray-100">Data Perolehan & Supplier</h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-gray-400 block font-medium">Tanggal Perolehan</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block">{{ $asset->tanggal_perolehan ? $asset->tanggal_perolehan->format('d M Y') : '-' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">Vendor / Supplier</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block">{{ $asset->vendor ?: '-' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">No. Faktur / Invoice</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block">{{ $asset->nomor_faktur ?: '-' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">Nilai Perolehan</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block">Rp {{ number_format($asset->nilai_perolehan, 0, ',', '.') }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">Nilai Residu</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block">Rp {{ number_format($asset->nilai_residu, 0, ',', '.') }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">Nilai Buku Saat Ini</span>
                        <span class="font-semibold text-emerald-700 mt-0.5 block">Rp {{ number_format($asset->nilai_buku, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes & Remarks -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-2">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Keterangan / Catatan</h3>
                <p class="text-xs text-gray-700 leading-relaxed whitespace-pre-line">{{ $asset->keterangan ?: 'Tidak ada keterangan tambahan.' }}</p>
            </div>

            <!-- Audit Trail Metadata -->
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-[11px] text-gray-500 flex flex-wrap justify-between gap-2">
                <div>
                    Dibuat pada: <span class="font-medium text-gray-700">{{ $asset->created_at ? $asset->created_at->format('d M Y H:i') : '-' }}</span>
                    @if($asset->creator)
                        oleh <span class="font-medium text-gray-700">{{ $asset->creator->name }}</span>
                    @endif
                </div>
                <div>
                    Terakhir diperbarui: <span class="font-medium text-gray-700">{{ $asset->updated_at ? $asset->updated_at->format('d M Y H:i') : '-' }}</span>
                    @if($asset->updater)
                        oleh <span class="font-medium text-gray-700">{{ $asset->updater->name }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
