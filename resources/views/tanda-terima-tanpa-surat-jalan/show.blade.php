@extends('layouts.app')

@section('title', 'Detail Tanda Terima - ' . $tandaTerimaTanpaSuratJalan->no_tanda_terima)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Tanda Terima</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $tandaTerimaTanpaSuratJalan->no_tanda_terima }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </a>
                    @can('tanda-terima-tanpa-surat-jalan-update')
                        <a href="{{ route('tanda-terima-tanpa-surat-jalan.edit', $tandaTerimaTanpaSuratJalan) }}"
                           class="inline-flex items-center px-4 py-2 border border-yellow-300 rounded-md text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Detail Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Dasar -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Dasar</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">No. Tanda Terima</label>
                                <p class="text-base font-semibold text-gray-900">{{ $tandaTerimaTanpaSuratJalan->no_tanda_terima }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Tanggal</label>
                                <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->tanggal_tanda_terima->format('d/m/Y') }}</p>
                            </div>
                            @if($tandaTerimaTanpaSuratJalan->nomor_surat_jalan_customer)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">No. Surat Jalan Customer</label>
                                    <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->nomor_surat_jalan_customer }}</p>
                                </div>
                            @endif
                            @if($tandaTerimaTanpaSuratJalan->estimasi_naik_kapal)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Estimasi Naik Kapal</label>
                                    <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->estimasi_naik_kapal }}</p>
                                </div>
                            @endif
                            @if($tandaTerimaTanpaSuratJalan->term && $tandaTerimaTanpaSuratJalan->term->nama_status)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Term</label>
                                    <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->term->nama_status }}</p>
                                </div>
                            @endif
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-2">Status</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Penerima dan Pengirim -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Penerima dan Pengirim</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Penerima</label>
                                <p class="text-base font-semibold text-gray-900">{{ $tandaTerimaTanpaSuratJalan->penerima }}</p>
                                @if($tandaTerimaTanpaSuratJalan->alamat_penerima)
                                    <p class="text-sm text-gray-600 mt-1">{{ $tandaTerimaTanpaSuratJalan->alamat_penerima }}</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Pengirim</label>
                                <p class="text-base font-semibold text-gray-900">{{ $tandaTerimaTanpaSuratJalan->pengirim }}</p>
                                @if($tandaTerimaTanpaSuratJalan->alamat_pengirim)
                                    <p class="text-sm text-gray-600 mt-1">{{ $tandaTerimaTanpaSuratJalan->alamat_pengirim }}</p>
                                @endif
                            </div>
                        </div>
                        @if($tandaTerimaTanpaSuratJalan->pic || $tandaTerimaTanpaSuratJalan->telepon)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                @if($tandaTerimaTanpaSuratJalan->pic)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">PIC (Person In Charge)</label>
                                        <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->pic }}</p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->telepon)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Telepon</label>
                                        <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->telepon }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Barang -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Barang</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Jenis Barang</label>
                                <p class="text-base font-semibold text-gray-900">{{ $tandaTerimaTanpaSuratJalan->jenis_barang }}</p>
                            </div>
                            @if($tandaTerimaTanpaSuratJalan->nama_barang)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Nama Barang</label>
                                    <p class="text-base font-semibold text-gray-900">{{ $tandaTerimaTanpaSuratJalan->nama_barang }}</p>
                                </div>
                            @endif
                            @if($tandaTerimaTanpaSuratJalan->aktifitas)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Aktifitas</label>
                                    <p class="text-base text-gray-900 capitalize">{{ $tandaTerimaTanpaSuratJalan->aktifitas }}</p>
                                </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Jumlah</label>
                                <p class="text-base text-gray-900">
                                    {{ number_format($tandaTerimaTanpaSuratJalan->jumlah_barang) }} 
                                    {{ $tandaTerimaTanpaSuratJalan->satuan_barang }}
                                </p>
                            </div>
                            
                            @if($tandaTerimaTanpaSuratJalan->berat)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Berat</label>
                                    <p class="text-base text-gray-900">
                                        {{ number_format($tandaTerimaTanpaSuratJalan->berat, 2) }} 
                                        {{ $tandaTerimaTanpaSuratJalan->satuan_berat }}
                                    </p>
                                </div>
                            @endif

                            @if($tandaTerimaTanpaSuratJalan->meter_kubik)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Volume</label>
                                    <p class="text-base text-gray-900">{{ number_format($tandaTerimaTanpaSuratJalan->meter_kubik, 6) }} m³</p>
                                </div>
                            @endif

                            @if($tandaTerimaTanpaSuratJalan->tonase)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Tonase</label>
                                    <p class="text-base text-gray-900">{{ number_format($tandaTerimaTanpaSuratJalan->tonase, 2) }} Ton</p>
                                </div>
                            @endif

                            @if($tandaTerimaTanpaSuratJalan->panjang || $tandaTerimaTanpaSuratJalan->lebar || $tandaTerimaTanpaSuratJalan->tinggi)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Dimensi</label>
                                    <p class="text-base text-gray-900">
                                        @if($tandaTerimaTanpaSuratJalan->panjang)
                                            {{ number_format($tandaTerimaTanpaSuratJalan->panjang, 2) }} cm
                                        @endif
                                        @if($tandaTerimaTanpaSuratJalan->lebar)
                                            × {{ number_format($tandaTerimaTanpaSuratJalan->lebar, 2) }} cm
                                        @endif
                                        @if($tandaTerimaTanpaSuratJalan->tinggi)
                                            × {{ number_format($tandaTerimaTanpaSuratJalan->tinggi, 2) }} cm
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if($tandaTerimaTanpaSuratJalan->keterangan_barang)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Keterangan Barang</label>
                                    <p class="text-base text-gray-900 bg-gray-50 p-3 rounded-md">{{ $tandaTerimaTanpaSuratJalan->keterangan_barang }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informasi Kontainer -->
                @if($tandaTerimaTanpaSuratJalan->tipe_kontainer || $tandaTerimaTanpaSuratJalan->no_kontainer || $tandaTerimaTanpaSuratJalan->size_kontainer || $tandaTerimaTanpaSuratJalan->no_seal || $tandaTerimaTanpaSuratJalan->tanggal_seal)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="border-b border-gray-200 p-4">
                            <h2 class="text-lg font-semibold text-gray-800">Informasi Kontainer</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($tandaTerimaTanpaSuratJalan->tipe_kontainer)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Tipe Kontainer</label>
                                        <p class="text-base text-gray-900 uppercase">{{ $tandaTerimaTanpaSuratJalan->tipe_kontainer }}</p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->no_kontainer)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">No. Kontainer</label>
                                        <p class="text-base font-semibold text-gray-900">{{ $tandaTerimaTanpaSuratJalan->no_kontainer }}</p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->size_kontainer)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Size Kontainer</label>
                                        <p class="text-base text-gray-900">{{ $tandaTerimaTanpaSuratJalan->size_kontainer }} ft</p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->no_seal)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">No. Seal</label>
                                        <code class="text-sm bg-gray-100 px-3 py-1 rounded">{{ $tandaTerimaTanpaSuratJalan->no_seal }}</code>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->tanggal_seal)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Tanggal Seal</label>
                                        <p class="text-base text-gray-900">{{ \Carbon\Carbon::parse($tandaTerimaTanpaSuratJalan->tanggal_seal)->format('d M Y') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Informasi Tujuan -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Tujuan</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Tujuan Pengambilan</label>
                                <p class="text-base text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $tandaTerimaTanpaSuratJalan->tujuan_pengambilan }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Tujuan Pengiriman</label>
                                <p class="text-base text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $tandaTerimaTanpaSuratJalan->tujuan_pengiriman }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Transportasi -->
                @if($tandaTerimaTanpaSuratJalan->supir || $tandaTerimaTanpaSuratJalan->kenek || $tandaTerimaTanpaSuratJalan->no_plat)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="border-b border-gray-200 p-4">
                            <h2 class="text-lg font-semibold text-gray-800">Informasi Transportasi</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($tandaTerimaTanpaSuratJalan->supir)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Supir</label>
                                        <p class="text-base text-gray-900 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $tandaTerimaTanpaSuratJalan->supir }}
                                        </p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->kenek)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">Kenek</label>
                                        <p class="text-base text-gray-900 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $tandaTerimaTanpaSuratJalan->kenek }}
                                        </p>
                                    </div>
                                @endif
                                @if($tandaTerimaTanpaSuratJalan->no_plat)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-2">No. Plat</label>
                                        <code class="text-sm bg-gray-100 px-3 py-1 rounded">{{ $tandaTerimaTanpaSuratJalan->no_plat }}</code>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Catatan -->
                @if($tandaTerimaTanpaSuratJalan->catatan)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="border-b border-gray-200 p-4">
                            <h2 class="text-lg font-semibold text-gray-800">Catatan</h2>
                        </div>
                        <div class="p-6">
                            <p class="text-base text-gray-900 bg-gray-50 p-4 rounded-md">{{ $tandaTerimaTanpaSuratJalan->catatan }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Sistem</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat Oleh</label>
                            <p class="text-sm text-gray-900">{{ $tandaTerimaTanpaSuratJalan->creator->name ?? 'System' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                            <p class="text-sm text-gray-900">{{ $tandaTerimaTanpaSuratJalan->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($tandaTerimaTanpaSuratJalan->updated_by)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diupdate</label>
                                <p class="text-sm text-gray-900">{{ $tandaTerimaTanpaSuratJalan->updater->name ?? 'System' }}</p>
                                <p class="text-xs text-gray-500">{{ $tandaTerimaTanpaSuratJalan->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="border-t border-gray-200 pt-4 space-y-3">
                            @can('tanda-terima-tanpa-surat-jalan-update')
                                <a href="{{ route('tanda-terima-tanpa-surat-jalan.edit', $tandaTerimaTanpaSuratJalan) }}"
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-yellow-300 rounded-md text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Tanda Terima
                                </a>
                            @endcan
                            
                            @can('tanda-terima-tanpa-surat-jalan-delete')
                                <form action="{{ route('tanda-terima-tanpa-surat-jalan.destroy', $tandaTerimaTanpaSuratJalan) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda terima ini? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
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
