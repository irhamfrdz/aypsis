@extends('layouts.app')

@section('title', 'Detail Manifest')
@section('page_title', 'Detail Manifest')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('report.manifests.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Manifest
            </a>
            <div class="mt-2 flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Detail Manifest</h1>
                <div class="flex gap-2">
                    @can('manifest-edit')
                    <a href="{{ route('report.manifests.edit', $manifest->id) }}"
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    @endcan
                    @can('manifest-delete')
                    <form action="{{ route('report.manifests.destroy', $manifest->id) }}" method="POST" class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus manifest ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Informasi BL & Kontainer -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b">Informasi BL & Kontainer</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">No. BL</label>
                    <p class="text-base text-gray-900">{{ $manifest->nomor_bl ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Prospek</label>
                    <p class="text-base text-gray-900">{{ $manifest->prospek->nama_perusahaan ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">No. Kontainer</label>
                    <p class="text-base text-gray-900">{{ $manifest->nomor_kontainer ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">No. Seal</label>
                    <p class="text-base text-gray-900">{{ $manifest->no_seal ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Tipe Kontainer</label>
                    <p class="text-base text-gray-900">{{ $manifest->tipe_kontainer ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Size Kontainer</label>
                    <p class="text-base text-gray-900">{{ $manifest->size_kontainer ? $manifest->size_kontainer . "'" : '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Informasi Kapal & Pelabuhan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b">Informasi Kapal & Pelabuhan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Nama Kapal</label>
                    <p class="text-base text-gray-900">{{ $manifest->nama_kapal ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">No. Voyage</label>
                    <p class="text-base text-gray-900">{{ $manifest->no_voyage ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Pelabuhan Asal</label>
                    <p class="text-base text-gray-900">{{ $manifest->pelabuhan_asal ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Pelabuhan Tujuan</label>
                    <p class="text-base text-gray-900">{{ $manifest->pelabuhan_tujuan ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Berangkat</label>
                    <p class="text-base text-gray-900">{{ $manifest->tanggal_berangkat ? $manifest->tanggal_berangkat->format('d M Y') : '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Penerimaan</label>
                    <p class="text-base text-gray-900">{{ $manifest->penerimaan ? $manifest->penerimaan->format('d M Y') : '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Informasi Barang -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b">Informasi Barang</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Nama Barang</label>
                    <p class="text-base text-gray-900 whitespace-pre-line">{{ $manifest->nama_barang ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Tonnage</label>
                    <p class="text-base text-gray-900">{{ $manifest->tonnage ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Volume</label>
                    <p class="text-base text-gray-900">{{ $manifest->volume ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Satuan</label>
                    <p class="text-base text-gray-900">{{ $manifest->satuan ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Kuantitas</label>
                    <p class="text-base text-gray-900">{{ $manifest->kuantitas ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Term</label>
                    <p class="text-base text-gray-900">{{ $manifest->term ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Informasi Pengirim & Penerima -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b">Informasi Pengirim & Penerima</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">SHIPPER</label>
                    <p class="text-base text-gray-900">{{ $manifest->pengirim ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Penerima</label>
                    <p class="text-base text-gray-900">{{ $manifest->penerima ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Asal Kontainer</label>
                    <p class="text-base text-gray-900">{{ $manifest->asal_kontainer ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Ke</label>
                    <p class="text-base text-gray-900">{{ $manifest->ke ?? '-' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Alamat Pengiriman</label>
                    <p class="text-base text-gray-900 whitespace-pre-line">{{ $manifest->alamat_pengiriman ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Contact Person</label>
                    <p class="text-base text-gray-900">{{ $manifest->contact_person ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Informasi Sistem -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b">Informasi Sistem</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat Oleh</label>
                    <p class="text-base text-gray-900">{{ $manifest->createdBy->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                    <p class="text-base text-gray-900">{{ $manifest->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Diperbarui Oleh</label>
                    <p class="text-base text-gray-900">{{ $manifest->updatedBy->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Diperbarui</label>
                    <p class="text-base text-gray-900">{{ $manifest->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
