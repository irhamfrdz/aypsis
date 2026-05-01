@extends('layouts.app')

@section('title', 'Detail Tanda Terima SJ Tarik Kosong Batam')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Tanda Terima</h1>
                <p class="text-gray-600">Informasi lengkap tanda terima penarikan kosong</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.print', $item->id) }}" target="_blank" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700">
                    <i class="fas fa-print mr-2"></i> Cetak
                </a>
                <a href="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.edit', $item->id) }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg font-medium hover:bg-amber-700">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column: Main Data -->
            <div class="md:col-span-2 space-y-6">
                <!-- TT Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-blue-600 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-white font-bold uppercase tracking-wider text-sm">Informasi Tanda Terima</h3>
                            <span class="bg-white text-blue-600 px-3 py-1 rounded-full text-[10px] font-black">{{ $item->no_tanda_terima }}</span>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Tanggal TT</p>
                            <p class="text-gray-900 font-medium">{{ $item->tanggal_tanda_terima->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Penerima</p>
                            <p class="text-gray-900 font-medium">{{ $item->penerima ?: '-' }}</p>
                        </div>
                        <div class="col-span-2 pt-4 border-t border-gray-50">
                            <p class="text-xs font-bold text-gray-400 uppercase mb-1">Catatan</p>
                            <p class="text-gray-600 text-sm leading-relaxed">{{ $item->catatan ?: 'Tidak ada catatan tambahan.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- SJ Reference Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gray-800 px-6 py-4">
                        <h3 class="text-white font-bold uppercase tracking-wider text-sm">Referensi Surat Jalan</h3>
                    </div>
                    <div class="p-6">
                        @if($item->suratJalan)
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">No. Surat Jalan</p>
                                <p class="text-gray-900 font-bold">{{ $item->no_surat_jalan }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Tanggal SJ</p>
                                <p class="text-gray-900">{{ $item->tanggal_surat_jalan ? $item->tanggal_surat_jalan->format('d/m/Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Supir</p>
                                <p class="text-gray-900">{{ $item->supir }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">No Plat</p>
                                <p class="text-gray-900">{{ $item->no_plat }}</p>
                            </div>
                            <div class="col-span-2 p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="flex items-center gap-4">
                                    <div class="bg-emerald-100 text-emerald-600 p-3 rounded-full">
                                        <i class="fas fa-box text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Detail Kontainer</p>
                                        <p class="text-lg font-black text-gray-900">{{ $item->no_kontainer }} <span class="text-gray-400 text-sm font-medium">/ {{ $item->size }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-6">
                            <p class="text-gray-400 italic">Data surat jalan asli tidak ditemukan atau sudah dihapus.</p>
                            <div class="mt-4 grid grid-cols-2 gap-4 text-left">
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase">No. SJ (Snapshot)</p>
                                    <p class="text-gray-900">{{ $item->no_surat_jalan }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase">Kontainer (Snapshot)</p>
                                    <p class="text-gray-900">{{ $item->no_kontainer }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Audit info -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4 border-b pb-2">Informasi Sistem</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Dibuat Oleh</p>
                            <p class="text-xs text-gray-700 font-medium">{{ $item->creator->name ?? 'System' }}</p>
                            <p class="text-[9px] text-gray-400">{{ $item->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($item->updated_by)
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Diupdate Oleh</p>
                            <p class="text-xs text-gray-700 font-medium">{{ $item->updater->name ?? '-' }}</p>
                            <p class="text-[9px] text-gray-400">{{ $item->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-blue-50 rounded-xl border border-blue-100 p-6">
                    <h3 class="text-xs font-bold text-blue-900 mb-2">Bantuan</h3>
                    <p class="text-[11px] text-blue-700 leading-relaxed">Tanda terima ini digunakan sebagai bukti penyerahan kontainer kosong di gudang/pelabuhan Batam.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
