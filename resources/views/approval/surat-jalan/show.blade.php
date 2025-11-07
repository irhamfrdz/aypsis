@extends('layouts.app')

@section('title', 'Detail Surat Jalan - Approval')
@section('page_title', 'Detail Surat Jalan - Approval')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Detail Surat Jalan</h1>
                    <p class="text-xs text-gray-600 mt-1">{{ $suratJalan->no_surat_jalan }} - Level {{ ucfirst(str_replace('-', ' ', $approvalLevel)) }}</p>
                </div>
                <div>
                    <a href="{{ route('approval.surat-jalan.index') }}"
                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Detail Surat Jalan -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Informasi Surat Jalan
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">No. Surat Jalan</label>
                                <p class="text-sm text-gray-900">{{ $suratJalan->no_surat_jalan }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal</label>
                                <p class="text-sm text-gray-900">{{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Barang</label>
                                <p class="text-sm text-gray-900 font-semibold flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    {{ $suratJalan->jenis_barang ?: '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Kegiatan</label>
                                @php
                                    $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $suratJalan->kegiatan)
                                                    ->value('nama_kegiatan') ?? $suratJalan->kegiatan;
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $kegiatanName }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Supir</label>
                                <p class="text-sm text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $suratJalan->supir ?: '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">No. Plat</label>
                                <p class="text-sm text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    {{ $suratJalan->no_plat ?: '-' }}
                                </p>
                            </div>
                            @if($suratJalan->tipe_kontainer !== 'cargo')
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Ukuran Kontainer</label>
                                <p class="text-sm text-gray-900">{{ $suratJalan->size ?: '-' }} {{ $suratJalan->size ? 'ft' : '' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Jumlah Kontainer</label>
                                <p class="text-sm text-gray-900">{{ $suratJalan->jumlah_kontainer }} unit</p>
                            </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Tipe Kontainer</label>
                                @if($suratJalan->tipe_kontainer === 'cargo')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Cargo
                                    </span>
                                @else
                                    <p class="text-sm text-gray-900">{{ $suratJalan->tipe_kontainer ?: 'Kontainer' }}</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Kontainer</label>
                                @if($suratJalan->tipe_kontainer === 'cargo')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Cargo tidak menggunakan nomor kontainer
                                    </span>
                                @else
                                    <div class="flex items-center space-x-2">
                                        <div id="kontainer-display">
                                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $suratJalan->no_kontainer ?: 'Belum diisi' }}</code>
                                        </div>
                                        @can('approval-surat-jalan-approve')
                                            <button type="button" onclick="editKontainer()" class="text-blue-600 hover:text-blue-800 text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                        @endcan
                                    </div>
                                    <div id="kontainer-edit" class="hidden mt-2">
                                        <div class="flex items-center space-x-2">
                                            <select id="kontainer-select" class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">Pilih dari stock atau ketik manual</option>
                                            </select>
                                            <input type="text" id="kontainer-manual" placeholder="Atau ketik manual" 
                                                   value="{{ $suratJalan->no_kontainer }}"
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div class="flex items-center space-x-2 mt-2">
                                            <button type="button" onclick="saveKontainer()" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                Simpan
                                            </button>
                                            <button type="button" onclick="cancelEditKontainer()" class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">No. Seal</label>
                                @if($suratJalan->tipe_kontainer === 'cargo')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Cargo tidak menggunakan seal
                                    </span>
                                @else
                                    <div class="flex items-center space-x-2">
                                        <div id="seal-display">
                                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $suratJalan->no_seal ?: 'Belum diisi' }}</code>
                                        </div>
                                        @can('approval-surat-jalan-approve')
                                            <button type="button" onclick="editSeal()" class="text-blue-600 hover:text-blue-800 text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                        @endcan
                                    </div>
                                    <div id="seal-edit" class="hidden mt-2">
                                        <div class="flex items-center space-x-2">
                                            <input type="text" id="seal-input" placeholder="Masukkan nomor seal" 
                                                   value="{{ $suratJalan->no_seal }}"
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <button type="button" onclick="saveSeal()" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                Simpan
                                            </button>
                                            <button type="button" onclick="cancelEditSeal()" class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Tujuan Pengambilan</label>
                                <p class="text-sm text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $suratJalan->tujuan_pengambilan ?? $suratJalan->order->tujuan_ambil ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Tujuan Pengiriman</label>
                                <p class="text-sm text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $suratJalan->tujuan_pengiriman ?? $suratJalan->order->tujuan_kirim ?? '-' }}
                                </p>
                            </div>
                            @if($suratJalan->pengirim)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Pengirim</label>
                                    <p class="text-sm text-gray-900">{{ $suratJalan->pengirim }}</p>
                                </div>
                            @endif
                            @if($suratJalan->alamat)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Alamat</label>
                                    <p class="text-sm text-gray-900">{{ $suratJalan->alamat }}</p>
                                </div>
                            @endif
                            @if($suratJalan->aktifitas)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Aktifitas</label>
                                    <p class="text-sm text-gray-900">{{ $suratJalan->aktifitas }}</p>
                                </div>
                            @endif
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Status Saat Ini</label>
                                <p class="text-sm">
                                    @switch($suratJalan->status)
                                        @case('sudah_checkpoint')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Sudah Checkpoint</span>
                                            @break
                                        @case('fully_approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Fully Approved</span>
                                            @break
                                        @case('rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $suratJalan->status }}</span>
                                    @endswitch
                                </p>
                            </div>
                        </div>

                        <!-- Gambar Checkpoint dari Supir -->
                        @if($suratJalan->gambar_checkpoint)
                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    <i class="fas fa-camera mr-1 text-indigo-600"></i>
                                    Gambar Checkpoint dari Supir
                                </label>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    @php
                                        $extension = pathinfo($suratJalan->gambar_checkpoint, PATHINFO_EXTENSION);
                                        $isPdf = strtolower($extension) === 'pdf';
                                    @endphp

                                    @if($isPdf)
                                        {{-- PDF Preview --}}
                                        <div class="flex items-center justify-between p-3 bg-white rounded border border-gray-300">
                                            <div class="flex items-center">
                                                <svg class="w-10 h-10 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">Dokumen PDF</p>
                                                    <p class="text-xs text-gray-500">Klik tombol untuk melihat dokumen</p>
                                                </div>
                                            </div>
                                            <a href="{{ asset('storage/' . $suratJalan->gambar_checkpoint) }}"
                                               target="_blank"
                                               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Lihat PDF
                                            </a>
                                        </div>
                                    @else
                                        {{-- Image Preview --}}
                                        <div class="space-y-3">
                                            <div class="relative group">
                                                <img src="{{ asset('storage/' . $suratJalan->gambar_checkpoint) }}"
                                                     alt="Gambar Checkpoint"
                                                     class="w-full h-64 object-contain bg-white rounded-lg border border-gray-300 cursor-pointer hover:shadow-lg transition-shadow"
                                                     onclick="window.open('{{ asset('storage/' . $suratJalan->gambar_checkpoint) }}', '_blank')">
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 rounded-lg transition-all flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex gap-2">
                                                <a href="{{ asset('storage/' . $suratJalan->gambar_checkpoint) }}"
                                                   target="_blank"
                                                   class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    Buka di Tab Baru
                                                </a>
                                                <a href="{{ asset('storage/' . $suratJalan->gambar_checkpoint) }}"
                                                   download
                                                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel Approval -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Status Approval
                        </h2>
                    </div>
                    <div class="p-4">
                        <!-- Status Semua Level Approval -->
                        <div class="space-y-3 mb-6">
                            @foreach($suratJalan->approvals as $approvalItem)
                                <div class="flex justify-between items-center p-3 rounded-lg {{ $approvalItem->status === 'approved' ? 'bg-green-50 border border-green-200' : ($approvalItem->status === 'rejected' ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200') }}">
                                    <span class="font-medium text-sm text-gray-800">{{ ucfirst(str_replace('-', ' ', $approvalItem->approval_level)) }}</span>
                                    @switch($approvalItem->status)
                                        @case('approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                                            @break
                                        @case('rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                    @endswitch
                                </div>
                                @if($approvalItem->approved_at && $approvalItem->approver)
                                    <div class="text-xs text-gray-500 ml-3 mb-3 space-y-1">
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $approvalItem->approver->name }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $approvalItem->approved_at->format('d/m/Y H:i') }}
                                        </div>
                                        @if($approvalItem->approval_notes)
                                            <div class="flex items-start">
                                                <svg class="w-3 h-3 mr-1 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                <span class="break-words">{{ $approvalItem->approval_notes }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Form Approval jika masih pending -->
                        @if($approval->status === 'pending')
                            <div class="border-t border-gray-200 pt-4">
                                <h3 class="text-base font-semibold text-gray-800 mb-4">Aksi Approval</h3>

                                <!-- Form Approve -->
                                <form action="{{ route('approval.surat-jalan.approve', $suratJalan) }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="approval_notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                  id="approval_notes" name="approval_notes" rows="3"
                                                  placeholder="Tambahkan catatan untuk approval..."></textarea>
                                    </div>
                                    <button type="submit" class="w-full mb-3 inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approve
                                    </button>
                                </form>

                                <!-- Button Reject -->
                                <button type="button" onclick="openRejectModal()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        @else
                            <div class="text-center py-6 border-t border-gray-200">
                                <p class="text-sm text-gray-500">Approval sudah diproses</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form action="{{ route('approval.surat-jalan.reject', $suratJalan) }}" method="POST">
            @csrf
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Reject Surat Jalan</h3>
                    <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <label for="reject_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Reject <span class="text-red-500">*</span>
                    </label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              id="reject_reason" name="approval_notes" rows="4"
                              placeholder="Jelaskan alasan mengapa surat jalan ini di-reject..." required></textarea>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Peringatan:</strong> Tindakan ini akan membuat surat jalan di-reject dan tidak bisa diproses lebih lanjut.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
// Load Select2 dynamically after jQuery is available
function loadSelect2() {
    return new Promise((resolve, reject) => {
        if (typeof window.$ !== 'undefined' && typeof window.$.fn.select2 !== 'undefined') {
            resolve(); // Select2 already loaded
            return;
        }
        
        // Wait for jQuery first
        function waitForjQuery() {
            if (typeof window.$ !== 'undefined') {
                // jQuery is available, now load Select2
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
                script.onload = () => resolve();
                script.onerror = () => reject('Failed to load Select2');
                document.head.appendChild(script);
            } else {
                setTimeout(waitForjQuery, 100);
            }
        }
        waitForjQuery();
    });
}

// Global variables
let isEditingKontainer = false;
let isEditingSeal = false;
let stockKontainers = [];

// Function to wait for jQuery and Select2 to be available
function waitForjQuery(callback) {
    loadSelect2().then(() => {
        callback();
    }).catch(error => {
        console.error('Error loading Select2:', error);
    });
}

function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('reject_reason').value = '';
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
    }
});

// ===== KONTAINER EDIT FUNCTIONS =====

function editKontainer() {
    if (isEditingKontainer) return;
    
    isEditingKontainer = true;
    document.getElementById('kontainer-display').classList.add('hidden');
    document.getElementById('kontainer-edit').classList.remove('hidden');
    
    // Initialize Select2 for stock kontainer dropdown
    initKontainerSelect();
}

function cancelEditKontainer() {
    isEditingKontainer = false;
    document.getElementById('kontainer-display').classList.remove('hidden');
    document.getElementById('kontainer-edit').classList.add('hidden');
    
    // Reset values
    waitForjQuery(() => {
        $('#kontainer-select').val('').trigger('change');
    });
    document.getElementById('kontainer-manual').value = '{{ $suratJalan->no_kontainer }}';
}

function initKontainerSelect() {
    waitForjQuery(() => {
        $('#kontainer-select').select2({
        placeholder: 'Cari nomor kontainer dari stock...',
        allowClear: true,
        ajax: {
            url: '{{ route("approval.surat-jalan.api.stock-kontainers") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.nomor,
                            text: item.text,
                            data: item
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        width: '100%'
    });

    // When stock kontainer is selected, update the manual input
    $('#kontainer-select').on('select2:select', function (e) {
        const data = e.params.data;
        document.getElementById('kontainer-manual').value = data.id;
    });

    // When stock kontainer is cleared, clear manual input
    $('#kontainer-select').on('select2:clear', function (e) {
        document.getElementById('kontainer-manual').value = '';
    });
    });
}

function saveKontainer() {
    const kontainerValue = document.getElementById('kontainer-manual').value.trim();
    const stockKontainerId = $('#kontainer-select').val();
    
    // Show loading state
    const saveBtn = event.target;
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Menyimpan...';
    saveBtn.disabled = true;
    
    // Make AJAX request
    fetch('{{ route("approval.surat-jalan.update-kontainer-seal", $suratJalan) }}', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            no_kontainer: kontainerValue,
            stock_kontainer_id: stockKontainerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update display
            document.querySelector('#kontainer-display code').textContent = kontainerValue || 'Belum diisi';
            
            // Show success message
            showNotification('Nomor kontainer berhasil diperbarui', 'success');
            
            // Cancel edit mode
            cancelEditKontainer();
        } else {
            showNotification(data.message || 'Gagal memperbarui nomor kontainer', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat memperbarui nomor kontainer', 'error');
    })
    .finally(() => {
        // Restore button state
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
}

// ===== SEAL EDIT FUNCTIONS =====

function editSeal() {
    if (isEditingSeal) return;
    
    isEditingSeal = true;
    document.getElementById('seal-display').classList.add('hidden');
    document.getElementById('seal-edit').classList.remove('hidden');
    document.getElementById('seal-input').focus();
}

function cancelEditSeal() {
    isEditingSeal = false;
    document.getElementById('seal-display').classList.remove('hidden');
    document.getElementById('seal-edit').classList.add('hidden');
    
    // Reset value
    document.getElementById('seal-input').value = '{{ $suratJalan->no_seal }}';
}

function saveSeal() {
    const sealValue = document.getElementById('seal-input').value.trim();
    
    // Show loading state
    const saveBtn = event.target;
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Menyimpan...';
    saveBtn.disabled = true;
    
    // Make AJAX request
    fetch('{{ route("approval.surat-jalan.update-kontainer-seal", $suratJalan) }}', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            no_seal: sealValue
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update display
            document.querySelector('#seal-display code').textContent = sealValue || 'Belum diisi';
            
            // Show success message
            showNotification('Nomor seal berhasil diperbarui', 'success');
            
            // Cancel edit mode
            cancelEditSeal();
        } else {
            showNotification(data.message || 'Gagal memperbarui nomor seal', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat memperbarui nomor seal', 'error');
    })
    .finally(() => {
        // Restore button state
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
}

// ===== UTILITY FUNCTIONS =====

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' :
        type === 'error' ? 'bg-red-100 border-l-4 border-red-500 text-red-700' :
        'bg-blue-100 border-l-4 border-blue-500 text-blue-700'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' ? 
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                    type === 'error' ?
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' :
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                }
            </div>
            <div class="ml-3">
                <span class="font-medium text-sm">${message}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-current hover:opacity-75">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Handle Enter key for inputs
document.getElementById('seal-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        saveSeal();
    }
});

document.getElementById('kontainer-manual').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        saveKontainer();
    }
});

// Handle Escape key to cancel editing
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (isEditingKontainer) {
            cancelEditKontainer();
        }
        if (isEditingSeal) {
            cancelEditSeal();
        }
    }
});
</script>
@endpush
