@extends('layouts.app')

@section('title', 'Detail Tanda Terima - ' . $tandaTerimaTanpaSuratJalan->no_tanda_terima)
@section('page_title', 'Detail Tanda Terima - ' . $tandaTerimaTanpaSuratJalan->no_tanda_terima)

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Detail Tanda Terima</h1>
                    <p class="text-xs text-gray-600 mt-1">{{ $tandaTerimaTanpaSuratJalan->no_tanda_terima }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.index') }}"
                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </a>
                    @can('tanda-terima-tanpa-surat-jalan-update')
                        <a href="{{ route('tanda-terima-tanpa-surat-jalan.edit', $tandaTerimaTanpaSuratJalan) }}"
                           class="inline-flex items-center px-3 py-2 border border-yellow-300 rounded-md text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Detail Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Informasi Tanda Terima
                        </h2>
                    </div>
                    <div class="p-4 space-y-6">
                        <!-- Informasi Dasar -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Dasar</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">No. Tanda Terima</label>
                                    <p class="text-sm text-gray-900 font-semibold">{{ $tandaTerimaTanpaSuratJalan->no_tanda_terima }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal</label>
                                    <p class="text-sm text-gray-900">{{ $tandaTerimaTanpaSuratJalan->tanggal_tanda_terima->format('d/m/Y') }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tandaTerimaTanpaSuratJalan->status_badge }}">
                                        {{ $tandaTerimaTanpaSuratJalan->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Penerima dan Pengirim -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Penerima dan Pengirim</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Penerima</label>
                                    <p class="text-sm text-gray-900 font-semibold">{{ $tandaTerimaTanpaSuratJalan->penerima }}</p>
                                    @if($tandaTerimaTanpaSuratJalan->alamat_penerima)
                                        <p class="text-sm text-gray-600 mt-1">{{ $tandaTerimaTanpaSuratJalan->alamat_penerima }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Pengirim</label>
                                    <p class="text-sm text-gray-900 font-semibold">{{ $tandaTerimaTanpaSuratJalan->pengirim }}</p>
                                    @if($tandaTerimaTanpaSuratJalan->alamat_pengirim)
                                        <p class="text-sm text-gray-600 mt-1">{{ $tandaTerimaTanpaSuratJalan->alamat_pengirim }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Barang -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Barang</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Barang</label>
                                    <p class="text-sm text-gray-900 font-semibold">{{ $tandaTerimaTanpaSuratJalan->jenis_barang }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Jumlah</label>
                                    <p class="text-sm text-gray-900">{{ number_format($tandaTerimaTanpaSuratJalan->jumlah_barang) }} {{ $tandaTerimaTanpaSuratJalan->satuan_barang }}</p>
                                </div>
                                @if($tandaTerimaTanpaSuratJalan->berat)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Berat</label>
                                        <p class="text-sm text-gray-900">{{ number_format($tandaTerimaTanpaSuratJalan->berat, 2) }} {{ $tandaTerimaTanpaSuratJalan->satuan_berat }}</p>
                                    </div>
                                @endif

                                <!-- Dimensi Information -->
                                @if($tandaTerimaTanpaSuratJalan->panjang || $tandaTerimaTanpaSuratJalan->lebar || $tandaTerimaTanpaSuratJalan->tinggi)
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Dimensi (cm)</label>
                                        <p class="text-sm text-gray-900">
                                            @if($tandaTerimaTanpaSuratJalan->panjang){{ number_format($tandaTerimaTanpaSuratJalan->panjang, 2) }} cm@endif
                                            @if($tandaTerimaTanpaSuratJalan->lebar) × {{ number_format($tandaTerimaTanpaSuratJalan->lebar, 2) }} cm@endif
                                            @if($tandaTerimaTanpaSuratJalan->tinggi) × {{ number_format($tandaTerimaTanpaSuratJalan->tinggi, 2) }} cm@endif
                                        </p>
                                    </div>
                                @endif

                                @if($tandaTerimaTanpaSuratJalan->meter_kubik)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Volume</label>
                                        <p class="text-sm text-gray-900">{{ number_format($tandaTerimaTanpaSuratJalan->meter_kubik, 6) }} m³</p>
                                    </div>
                                @endif

                                @if($tandaTerimaTanpaSuratJalan->tonase)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Tonase</label>
                                        <p class="text-sm text-gray-900">{{ number_format($tandaTerimaTanpaSuratJalan->tonase, 2) }} Ton</p>
                                    </div>
                                @endif

                                @if($tandaTerimaTanpaSuratJalan->keterangan_barang)
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Keterangan Barang</label>
                                        <p class="text-sm text-gray-900">{{ $tandaTerimaTanpaSuratJalan->keterangan_barang }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Informasi Tujuan -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Tujuan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Tujuan Pengambilan</label>
                                    <p class="text-sm text-gray-900 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $tandaTerimaTanpaSuratJalan->tujuan_pengambilan }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Tujuan Pengiriman</label>
                                    <p class="text-sm text-gray-900 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $tandaTerimaTanpaSuratJalan->tujuan_pengiriman }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Transportasi -->
                        @if($tandaTerimaTanpaSuratJalan->supir || $tandaTerimaTanpaSuratJalan->no_plat)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Transportasi</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if($tandaTerimaTanpaSuratJalan->supir)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 mb-1">Supir</label>
                                            <p class="text-sm text-gray-900 flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                {{ $tandaTerimaTanpaSuratJalan->supir }}
                                            </p>
                                        </div>
                                    @endif
                                    @if($tandaTerimaTanpaSuratJalan->no_plat)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 mb-1">No. Plat</label>
                                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $tandaTerimaTanpaSuratJalan->no_plat }}</code>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Catatan -->
                        @if($tandaTerimaTanpaSuratJalan->catatan)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Catatan</h3>
                                <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $tandaTerimaTanpaSuratJalan->catatan }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informasi Sistem
                        </h2>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat Oleh</label>
                            <p class="text-sm text-gray-900">{{ $tandaTerimaTanpaSuratJalan->created_by ?? 'System' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                            <p class="text-sm text-gray-900">{{ $tandaTerimaTanpaSuratJalan->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($tandaTerimaTanpaSuratJalan->updated_by)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diupdate Oleh</label>
                                <p class="text-sm text-gray-900">{{ $tandaTerimaTanpaSuratJalan->updated_by }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diupdate</label>
                            <p class="text-sm text-gray-900">{{ $tandaTerimaTanpaSuratJalan->updated_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="border-t border-gray-200 pt-4 space-y-2">
                            @can('tanda-terima-tanpa-surat-jalan-update')
                                <a href="{{ route('tanda-terima-tanpa-surat-jalan.edit', $tandaTerimaTanpaSuratJalan) }}"
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-yellow-300 rounded-md text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Tanda Terima
                                </a>
                            @endcan
                            @can('tanda-terima-tanpa-surat-jalan-delete')
                                <form action="{{ route('tanda-terima-tanpa-surat-jalan.destroy', $tandaTerimaTanpaSuratJalan) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda terima ini? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus Tanda Terima
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
