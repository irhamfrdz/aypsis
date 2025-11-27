@extends('layouts.app')

@section('title', 'Detail Surat Jalan Bongkaran')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Detail Surat Jalan Bongkaran</h1>
                <p class="text-xs text-gray-600 mt-1">{{ $suratJalanBongkaran->nomor_surat_jalan }}</p>
            </div>
            <div class="flex gap-2">
                @can('surat-jalan-bongkaran-update')
                    <a href="{{ route('surat-jalan-bongkaran.edit', $suratJalanBongkaran) }}"
                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                @endcan
                <a href="{{ route('surat-jalan-bongkaran.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informasi Dasar -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nomor Surat Jalan</label>
                            <p class="text-gray-900 font-semibold">{{ $suratJalanBongkaran->nomor_surat_jalan }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tanggal Surat Jalan</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->tanggal_surat_jalan ? $suratJalanBongkaran->tanggal_surat_jalan->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Kapal</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->nama_kapal ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">No Voyage</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->no_voyage ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">No BL</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->no_bl ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Term</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->term ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Aktifitas</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->aktifitas ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <div class="mt-1">
                                @php
                                    $statusClasses = [
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'active' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'belum masuk checkpoint' => 'bg-yellow-100 text-yellow-800',
                                        'sudah masuk checkpoint' => 'bg-indigo-100 text-indigo-800',
                                        'gate in' => 'bg-purple-100 text-purple-800',
                                        'gate out' => 'bg-emerald-100 text-emerald-800',
                                    ];
                                    $statusClass = $statusClasses[$suratJalanBongkaran->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $suratJalanBongkaran->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pengiriman -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengiriman</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Pengirim</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->pengirim ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Jenis Barang</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->jenis_barang ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tujuan Alamat</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->tujuan_alamat ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tujuan Pengambilan</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->tujuan_pengambilan ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tujuan Pengiriman</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->tujuan_pengiriman ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Jenis Pengiriman</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->jenis_pengiriman ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tanggal Ambil Barang</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->tanggal_ambil_barang ? \Carbon\Carbon::parse($suratJalanBongkaran->tanggal_ambil_barang)->format('d/m/Y') : '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Personal -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Personal</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Supir</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->supir ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">No Plat</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->no_plat ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Kenek</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->kenek ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Krani</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->krani ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Container -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Container</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">No Kontainer</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->no_kontainer ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">No Seal</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->no_seal ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Size Kontainer</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->size ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tipe Kontainer</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->tipe_kontainer ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Jumlah Kontainer</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->jumlah_kontainer ?: '1' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Packaging -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Packaging</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <label class="text-sm font-medium text-gray-500">Karton</label>
                            <div class="mt-1">
                                @if($suratJalanBongkaran->karton == 'ya')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Ya</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Tidak</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-center">
                            <label class="text-sm font-medium text-gray-500">Plastik</label>
                            <div class="mt-1">
                                @if($suratJalanBongkaran->plastik == 'ya')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Ya</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Tidak</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-center">
                            <label class="text-sm font-medium text-gray-500">Terpal</label>
                            <div class="mt-1">
                                @if($suratJalanBongkaran->terpal == 'ya')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Ya</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Tidak</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Keuangan -->
                <div class="bg-gray-50 rounded-lg p-4 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Keuangan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">RIT</label>
                            <div class="mt-1">
                                @if($suratJalanBongkaran->rit)
                                    @if($suratJalanBongkaran->rit == 'menggunakan_rit')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Menggunakan RIT</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Tidak Menggunakan RIT</span>
                                    @endif
                                @else
                                    <span class="text-gray-900">-</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Uang Jalan Type</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->uang_jalan_type ? ucfirst($suratJalanBongkaran->uang_jalan_type) : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Uang Jalan Nominal</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->uang_jalan_nominal ? 'Rp ' . number_format($suratJalanBongkaran->uang_jalan_nominal, 0, ',', '.') : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Total Tarif</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->total_tarif ? 'Rp ' . number_format($suratJalanBongkaran->total_tarif, 0, ',', '.') : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Jumlah Terbayar</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->jumlah_terbayar ? 'Rp ' . number_format($suratJalanBongkaran->jumlah_terbayar, 0, ',', '.') : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status Pembayaran</label>
                            <div class="mt-1">
                                @if($suratJalanBongkaran->status_pembayaran)
                                    @php
                                        $statusClasses = [
                                            'belum_bayar' => 'bg-red-100 text-red-800',
                                            'sebagian' => 'bg-yellow-100 text-yellow-800',
                                            'lunas' => 'bg-green-100 text-green-800',
                                        ];
                                        $statusClass = $statusClasses[$suratJalanBongkaran->status_pembayaran] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $suratJalanBongkaran->status_pembayaran)) }}
                                    </span>
                                @else
                                    <span class="text-gray-900">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tagihan -->
                    @if($suratJalanBongkaran->tagihan_ayp || $suratJalanBongkaran->tagihan_atb || $suratJalanBongkaran->tagihan_pb)
                        <div class="mt-4">
                            <label class="text-sm font-medium text-gray-500">Tagihan</label>
                            <div class="mt-2 flex gap-2">
                                @if($suratJalanBongkaran->tagihan_ayp)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">AYP</span>
                                @endif
                                @if($suratJalanBongkaran->tagihan_atb)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">ATB</span>
                                @endif
                                @if($suratJalanBongkaran->tagihan_pb)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">PB</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Informasi Sistem -->
                <div class="bg-gray-50 rounded-lg p-4 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Sistem</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Dibuat Oleh</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->inputBy ? $suratJalanBongkaran->inputBy->name : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tanggal Dibuat</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->created_at ? $suratJalanBongkaran->created_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Terakhir Diupdate</label>
                            <p class="text-gray-900">{{ $suratJalanBongkaran->updated_at ? $suratJalanBongkaran->updated_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                @can('surat-jalan-bongkaran-delete')
                    <form action="{{ route('surat-jalan-bongkaran.destroy', $suratJalanBongkaran) }}" 
                          method="POST" 
                          class="inline" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat jalan bongkaran ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </form>
                @endcan
                @can('surat-jalan-bongkaran-update')
                    <a href="{{ route('surat-jalan-bongkaran.edit', $suratJalanBongkaran) }}"
                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                @endcan
                <a href="{{ route('surat-jalan-bongkaran.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection