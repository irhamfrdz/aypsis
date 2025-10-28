@extends('layouts.app')

@section('title', 'Detail Tanda Terima LCL - ' . $tandaTerima->nomor_tanda_terima)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Tanda Terima LCL</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-sm text-gray-600">{{ $tandaTerima->nomor_tanda_terima }}</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            LCL
                        </span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.index', ['tipe' => 'lcl']) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </a>
                    @can('tanda-terima-tanpa-surat-jalan-update')
                        <a href="{{ route('tanda-terima-lcl.edit', $tandaTerima) }}"
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
                                <p class="text-base font-semibold text-gray-900">{{ $tandaTerima->nomor_tanda_terima }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Tanggal</label>
                                <p class="text-base text-gray-900">{{ $tandaTerima->tanggal_tanda_terima->format('d/m/Y') }}</p>
                            </div>
                            @if($tandaTerima->no_surat_jalan_customer)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">No. Surat Jalan Customer</label>
                                    <p class="text-base text-gray-900">{{ $tandaTerima->no_surat_jalan_customer }}</p>
                                </div>
                            @endif
                            @if($tandaTerima->term && $tandaTerima->term->nama_status)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Term</label>
                                    <p class="text-base text-gray-900">{{ $tandaTerima->term->nama_status }}</p>
                                </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Kuantitas</label>
                                <p class="text-base text-gray-900">{{ $tandaTerima->kuantitas ?? 0 }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Status</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ ucfirst($tandaTerima->status ?? 'Draft') }}
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
                            <!-- Penerima -->
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Penerima</label>
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <p class="text-base font-semibold text-gray-900">{{ $tandaTerima->nama_penerima }}</p>
                                    @if($tandaTerima->pic_penerima)
                                        <p class="text-sm text-gray-600 mt-1">PIC: {{ $tandaTerima->pic_penerima }}</p>
                                    @endif
                                    @if($tandaTerima->telepon_penerima)
                                        <p class="text-sm text-gray-600">Telepon: {{ $tandaTerima->telepon_penerima }}</p>
                                    @endif
                                    @if($tandaTerima->alamat_penerima)
                                        <p class="text-sm text-gray-600 mt-2">{{ $tandaTerima->alamat_penerima }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Pengirim -->
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Pengirim</label>
                                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                                    <p class="text-base font-semibold text-gray-900">{{ $tandaTerima->nama_pengirim }}</p>
                                    @if($tandaTerima->pic_pengirim)
                                        <p class="text-sm text-gray-600 mt-1">PIC: {{ $tandaTerima->pic_pengirim }}</p>
                                    @endif
                                    @if($tandaTerima->telepon_pengirim)
                                        <p class="text-sm text-gray-600">Telepon: {{ $tandaTerima->telepon_pengirim }}</p>
                                    @endif
                                    @if($tandaTerima->alamat_pengirim)
                                        <p class="text-sm text-gray-600 mt-2">{{ $tandaTerima->alamat_pengirim }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
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
                                <label class="block text-sm font-medium text-gray-500 mb-2">Nama Barang</label>
                                <p class="text-base font-semibold text-gray-900">{{ $tandaTerima->nama_barang }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-2">Jenis Barang</label>
                                <p class="text-base text-gray-900">{{ $tandaTerima->jenisBarang->nama_barang ?? 'Tidak ada' }}</p>
                            </div>
                            @if($tandaTerima->keterangan_barang)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500 mb-2">Keterangan Barang</label>
                                    <p class="text-base text-gray-900">{{ $tandaTerima->keterangan_barang }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Dimensi dan Volume -->
                @if($tandaTerima->items && $tandaTerima->items->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="border-b border-gray-200 p-4">
                            <h2 class="text-lg font-semibold text-gray-800">Dimensi dan Volume</h2>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-purple-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Item</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Panjang (m)</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Lebar (m)</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Tinggi (m)</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Volume (m³)</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Berat (Ton)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($tandaTerima->items as $item)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->item_number ?? $loop->iteration }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item->panjang ?? '-' }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item->lebar ?? '-' }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item->tinggi ?? '-' }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item->meter_kubik ? number_format($item->meter_kubik, 3) : '-' }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item->tonase ? number_format($item->tonase, 2) : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Total:</td>
                                            <td class="px-4 py-3 text-sm font-semibold text-purple-600">{{ number_format($tandaTerima->items->sum('meter_kubik'), 3) }} m³</td>
                                            <td class="px-4 py-3 text-sm font-semibold text-purple-600">{{ number_format($tandaTerima->items->sum('tonase'), 2) }} Ton</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Informasi Kontainer -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h3 class="text-lg font-semibold text-gray-800">Informasi Kontainer</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tipe Kontainer</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ strtoupper($tandaTerima->tipe_kontainer ?? 'LCL') }}
                            </span>
                        </div>
                        
                        @if($tandaTerima->nomor_kontainer)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Kontainer</label>
                                <p class="text-sm font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $tandaTerima->nomor_kontainer }}</p>
                            </div>
                        @endif
                        
                        @if($tandaTerima->size_kontainer)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Size Kontainer</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $tandaTerima->size_kontainer }}
                                </span>
                            </div>
                        @endif
                        
                        @if($tandaTerima->nomor_seal)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Seal</label>
                                <p class="text-sm font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $tandaTerima->nomor_seal }}</p>
                                @if($tandaTerima->tanggal_seal)
                                    <p class="text-xs text-gray-500 mt-1">Tanggal: {{ $tandaTerima->tanggal_seal->format('d/m/Y') }}</p>
                                @endif
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Status Seal</label>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Belum ada seal
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Pengiriman -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h3 class="text-lg font-semibold text-gray-800">Pengiriman</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Supir</label>
                            <p class="text-sm text-gray-900">{{ $tandaTerima->supir }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">No. Plat</label>
                            <p class="text-sm font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $tandaTerima->no_plat }}</p>
                        </div>
                        @if($tandaTerima->tujuanPengiriman)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Tujuan Pengiriman</label>
                                <p class="text-sm text-gray-900">{{ $tandaTerima->tujuanPengiriman->nama_tujuan }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Sistem -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-4">
                        <h3 class="text-lg font-semibold text-gray-800">Informasi Sistem</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat</label>
                            <p class="text-sm text-gray-900">{{ $tandaTerima->created_at->format('d/m/Y H:i') }}</p>
                            @if($tandaTerima->createdBy)
                                <p class="text-xs text-gray-500">oleh {{ $tandaTerima->createdBy->name }}</p>
                            @endif
                        </div>
                        
                        @if($tandaTerima->updated_at != $tandaTerima->created_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diubah</label>
                                <p class="text-sm text-gray-900">{{ $tandaTerima->updated_at->format('d/m/Y H:i') }}</p>
                                @if($tandaTerima->updatedBy)
                                    <p class="text-xs text-gray-500">oleh {{ $tandaTerima->updatedBy->name }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection