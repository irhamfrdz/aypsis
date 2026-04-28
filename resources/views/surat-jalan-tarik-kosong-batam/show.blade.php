@extends('layouts.app')

@section('title', 'Detail Surat Jalan Tarik Kosong Batam')
@section('page_title', 'Detail Surat Jalan Tarik Kosong Batam')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-blue-700 px-8 py-6 flex justify-between items-center text-white">
            <div>
                <h2 class="text-2xl font-bold">Surat Jalan Tarik Kosong</h2>
                <p class="text-indigo-100 mt-1">No: {{ $item->no_surat_jalan }}</p>
            </div>
            <div class="text-right">
                <span class="px-4 py-1 bg-white bg-opacity-20 backdrop-blur-sm rounded-full text-sm font-semibold border border-white border-opacity-30">
                    {{ strtoupper($item->status) }}
                </span>
                <p class="mt-2 text-indigo-100 text-sm">Tanggal: {{ $item->tanggal_surat_jalan->format('d F Y') }}</p>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Left Column -->
                <div class="space-y-8">
                    <section>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-indigo-500"></i> RUTE PENGIRIMAN
                        </h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                                    <span class="text-[10px] font-bold text-indigo-400 uppercase block mb-1">Dari (Pengambilan)</span>
                                    <span class="text-sm font-semibold text-indigo-900">{{ $item->tujuan_pengambilan ?? '-' }}</span>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-xl border border-blue-100">
                                    <span class="text-[10px] font-bold text-blue-400 uppercase block mb-1">Ke (Pengiriman)</span>
                                    <span class="text-sm font-semibold text-blue-900">{{ $item->tujuan_pengiriman ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-truck mr-2 text-indigo-500"></i> ARMADA & PERSONIL
                        </h3>
                        <div class="grid grid-cols-2 gap-6 bg-gray-50 p-5 rounded-2xl">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">No. Plat</span>
                                <span class="text-sm font-bold text-gray-800">{{ $item->no_plat ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Supir</span>
                                <span class="text-sm font-bold text-gray-800">{{ $item->supir ?? '-' }}</span>
                            </div>
                            @if($item->supir2)
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Supir 2</span>
                                <span class="text-sm font-bold text-gray-800">{{ $item->supir2 }}</span>
                            </div>
                            @endif
                            @if($item->kenek)
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Kenek</span>
                                <span class="text-sm font-bold text-gray-800">{{ $item->kenek }}</span>
                            </div>
                            @endif
                        </div>
                    </section>
                </div>

                <!-- Right Column -->
                <div class="space-y-8">
                    <section>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-box mr-2 text-indigo-500"></i> DETAIL KONTAINER
                        </h3>
                        <div class="relative p-6 bg-white border-2 border-dashed border-gray-200 rounded-2xl overflow-hidden">
                            <div class="absolute -right-4 -bottom-4 text-gray-50 transform -rotate-12">
                                <i class="fas fa-shipping-fast text-8xl"></i>
                            </div>
                            <div class="relative z-10 space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Nomor</span>
                                    <span class="text-lg font-black text-indigo-600 tracking-tight">{{ $item->no_kontainer ?? 'BELUM ADA' }}</span>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="text-center bg-gray-50 p-2 rounded-lg">
                                        <span class="text-[10px] text-gray-400 uppercase block">Size</span>
                                        <span class="text-xs font-bold text-gray-800">{{ $item->size ?? '-' }} FT</span>
                                    </div>
                                    <div class="text-center bg-gray-50 p-2 rounded-lg">
                                        <span class="text-[10px] text-gray-400 uppercase block">Tipe</span>
                                        <span class="text-xs font-bold text-gray-800">{{ $item->tipe_kontainer ?? '-' }}</span>
                                    </div>
                                    <div class="text-center bg-gray-50 p-2 rounded-lg">
                                        <span class="text-[10px] text-gray-400 uppercase block">Status</span>
                                        <span class="text-xs font-bold text-gray-800">{{ $item->f_e == 'E' ? 'Empty' : 'Full' }}</span>
                                    </div>
                                </div>
                                <div class="pt-2">
                                    <span class="text-sm text-gray-500 block mb-1">No. Tiket / DO</span>
                                    <span class="text-sm font-semibold text-gray-800">{{ $item->no_tiket_do ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-money-bill-wave mr-2 text-indigo-500"></i> KEUANGAN
                        </h3>
                        <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl text-white">
                            <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Uang Jalan</span>
                            <span class="text-2xl font-bold">Rp {{ number_format($item->uang_jalan, 0, ',', '.') }}</span>
                        </div>
                    </section>

                    @if($item->catatan)
                    <section>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-sticky-note mr-2 text-indigo-500"></i> CATATAN
                        </h3>
                        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 italic text-sm text-yellow-800">
                            "{{ $item->catatan }}"
                        </div>
                    </section>
                    @endif
                </div>
            </div>
            
            <div class="mt-12 pt-8 border-t flex justify-between items-center text-xs text-gray-400">
                <div>
                    Input Oleh: <span class="font-semibold">{{ $item->creator->name ?? 'System' }}</span> pada {{ $item->input_date ? $item->input_date->format('d/m/Y H:i') : $item->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('surat-jalan-tarik-kosong-batam.index') }}" class="text-gray-500 hover:text-indigo-600 transition-colors">Kembali ke Daftar</a>
                    <a href="{{ route('surat-jalan-tarik-kosong-batam.edit', $item->id) }}" class="text-gray-500 hover:text-yellow-600 transition-colors">Edit Data</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="flex justify-center space-x-4">
        <a href="{{ route('surat-jalan-tarik-kosong-batam.print', $item->id) }}" target="_blank" class="flex items-center px-8 py-3 bg-white border-2 border-indigo-600 text-indigo-600 rounded-full font-bold hover:bg-indigo-50 transition-all shadow-lg">
            <i class="fas fa-print mr-2"></i> Cetak Surat Jalan
        </a>
        @can('surat-jalan-tarik-kosong-batam-update')
        <a href="{{ route('surat-jalan-tarik-kosong-batam.edit', $item->id) }}" class="flex items-center px-8 py-3 bg-indigo-600 text-white rounded-full font-bold hover:bg-indigo-700 transition-all shadow-lg">
            <i class="fas fa-edit mr-2"></i> Edit Surat Jalan
        </a>
        @endcan
    </div>
</div>
@endsection
