@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Detail Surat Jalan</h1>
                <p class="text-xs text-gray-600 mt-1">{{ $suratJalan->no_surat_jalan }}</p>
            </div>
            <div class="flex gap-2">
                @can('surat-jalan-view')
                <a href="{{ route('surat-jalan.print', $suratJalan->id) }}" target="_blank"
                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </a>
                <a href="{{ route('surat-jalan.download', $suratJalan->id) }}"
                   class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </a>
                @endcan
                @can('surat-jalan-update')
                <a href="{{ route('surat-jalan.edit', $suratJalan->id) }}"
                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                @endcan
                <a href="{{ route('surat-jalan.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="space-y-3">
                        @if($suratJalan->order)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">No. Order</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $suratJalan->order->nomor_order }}</p>
                        </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-600">No. Surat Jalan</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $suratJalan->no_surat_jalan }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal Surat Jalan</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->formatted_tanggal_surat_jalan }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $suratJalan->status_badge }}">
                                {{ ucfirst($suratJalan->status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Input By</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->inputBy?->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Input Date</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->input_date?->format('d-m-Y H:i') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pengirim Information -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengirim</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Pengirim</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->pengirim ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Telepon</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->telp ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Alamat</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->alamat ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Barang Information -->
                <div class="bg-green-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Barang</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Jenis Barang</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->jenis_barang ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tujuan Pengambilan</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->tujuan_pengambilan ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tujuan Pengiriman</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->tujuan_pengiriman ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Retur Barang</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->retur_barang ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Jumlah Retur</label>
                            <p class="text-sm text-gray-900">{{ number_format($suratJalan->jumlah_retur) }} pcs</p>
                        </div>
                    </div>
                </div>

                <!-- Kontainer Information -->
                <div class="bg-purple-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kontainer</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tipe Kontainer</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->tipe_kontainer ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">No. Kontainer</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $suratJalan->no_kontainer ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">No. Seal</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $suratJalan->no_seal ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Size</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->size ? $suratJalan->size . ' ft' : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Packaging Information -->
                <div class="bg-yellow-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kemasan</h3>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Karton</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $suratJalan->karton == 'pakai' ? 'bg-green-100 text-green-800' : ($suratJalan->karton == 'tidak_pakai' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $suratJalan->karton ? ucwords(str_replace('_', ' ', $suratJalan->karton)) : 'Tidak diset' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Plastik</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $suratJalan->plastik == 'pakai' ? 'bg-green-100 text-green-800' : ($suratJalan->plastik == 'tidak_pakai' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $suratJalan->plastik ? ucwords(str_replace('_', ' ', $suratJalan->plastik)) : 'Tidak diset' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Terpal</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $suratJalan->terpal == 'pakai' ? 'bg-green-100 text-green-800' : ($suratJalan->terpal == 'tidak_pakai' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $suratJalan->terpal ? ucwords(str_replace('_', ' ', $suratJalan->terpal)) : 'Tidak diset' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Transport Information -->
                <div class="bg-orange-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Transport</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Karyawan</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->karyawan ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Supir</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->supir ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Supir 2</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->supir2 ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Kenek</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->kenek ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">No. Plat</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $suratJalan->no_plat ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Schedule Information -->
                <div class="bg-indigo-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Jadwal</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal Muat</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->formatted_tanggal_muat }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Jam Berangkat</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->jam_berangkat ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Waktu Berangkat</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->waktu_berangkat?->format('d-m-Y H:i') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-red-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tambahan</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Term</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->term ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Rit</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->rit }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Uang Jalan</label>
                            <p class="text-sm text-gray-900 font-semibold">{{ $suratJalan->formatted_uang_jalan }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal Uang Jalan</label>
                            <p class="text-sm text-gray-900">{{ $suratJalan->uangJalan?->tanggal_uang_jalan?->format('d-m-Y') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">No. Accurate (Pranota)</label>
                            <p class="text-sm text-gray-900 font-mono">
                                {{ $suratJalan->pembayaranPranotaUangJalan?->nomor_accurate ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">No. Pemesanan</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $suratJalan->no_pemesanan ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Aktivitas & Gambar -->
                <div class="md:col-span-2 space-y-4">
                    @if($suratJalan->aktifitas)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Aktivitas</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $suratJalan->aktifitas }}</p>
                    </div>
                    @endif

                    @if($suratJalan->gambar)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Gambar/Dokumen</h3>
                        <div class="max-w-md">
                            <img src="{{ asset('storage/' . $suratJalan->gambar) }}"
                                 alt="Surat Jalan Image"
                                 class="w-full h-auto rounded-lg shadow-sm border">
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                @can('surat-jalan-delete')
                <form action="{{ route('surat-jalan.destroy', $suratJalan->id) }}"
                      method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus surat jalan ini?')"
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-150">
                        Hapus
                    </button>
                </form>
                @endcan

                @can('surat-jalan-update')
                <a href="{{ route('surat-jalan.edit', $suratJalan->id) }}"
                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors duration-150">
                    Edit
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
