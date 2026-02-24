@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Detail Tagihan Supir Vendor</h2>
            <p class="text-sm text-gray-500">Informasi lengkap rincian tagihan supir vendor</p>
        </div>
        <a href="{{ route('tagihan-supir-vendor.index') }}" class="flex items-center text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg px-4 py-2 hover:bg-gray-50 hover:text-gray-900 transition-colors">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                ID: #{{ $tagihanSupirVendor->id }}
            </h3>
            <div>
                @if($tagihanSupirVendor->status_pembayaran == 'lunas')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Lunas</span>
                @elseif($tagihanSupirVendor->status_pembayaran == 'sebagian')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Sebagian</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Belum Dibayar</span>
                @endif
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                <!-- Kolom Kiri -->
                <div class="space-y-4">
                    <div>
                        <div class="text-sm text-gray-500">No Surat Jalan</div>
                        <div class="font-medium text-gray-900 mt-1">
                            @if($tagihanSupirVendor->surat_jalan_id && $tagihanSupirVendor->suratJalan)
                                <a href="{{ route('surat-jalan.show', $tagihanSupirVendor->surat_jalan_id) }}" class="text-blue-600 hover:underline">
                                    {{ $tagihanSupirVendor->suratJalan->no_surat_jalan }}
                                </a>
                            @else
                                <span class="text-gray-500 italic">- Tidak Terhubung -</span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500">Nama Supir / Vendor</div>
                        <div class="font-medium text-gray-900 mt-1">
                            {{ $tagihanSupirVendor->nama_supir }} 
                            @if($tagihanSupirVendor->vendor)
                                <span class="text-gray-500 text-sm">({{ $tagihanSupirVendor->vendor->nama_vendor }})</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Rute Perjalanan</div>
                        <div class="flex items-center mt-1 text-gray-900 font-medium">
                            <span class="px-2.5 py-1 bg-gray-100 rounded-md">{{ $tagihanSupirVendor->dari }}</span>
                            <svg class="h-4 w-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            <span class="px-2.5 py-1 bg-gray-100 rounded-md">{{ $tagihanSupirVendor->ke }}</span>
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500">Jenis Kontainer</div>
                        <div class="font-medium text-gray-900 mt-1">{{ Str::upper($tagihanSupirVendor->jenis_kontainer ?? '-') }}</div>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-4">
                    <div>
                        <div class="text-sm text-gray-500">Nominal Tagihan</div>
                        <div class="font-medium text-gray-900 mt-1">Rp {{ number_format($tagihanSupirVendor->nominal, 0, ',', '.') }}</div>
                    </div>

                    @if($tagihanSupirVendor->adjustment != 0)
                    <div>
                        <div class="text-sm text-gray-500">Adjustment</div>
                        <div class="font-medium {{ $tagihanSupirVendor->adjustment > 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                            {{ $tagihanSupirVendor->adjustment > 0 ? '+' : '' }}Rp {{ number_format($tagihanSupirVendor->adjustment, 0, ',', '.') }}
                        </div>
                    </div>
                    @endif
                    
                    <div class="pt-2 border-t border-gray-100">
                        <div class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Total Akhir</div>
                        <div class="text-2xl font-bold text-blue-600 mt-1">Rp {{ number_format($tagihanSupirVendor->nominal + $tagihanSupirVendor->adjustment, 0, ',', '.') }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500">Dibuat Pada</div>
                        <div class="font-medium text-gray-900 mt-1">
                            {{ $tagihanSupirVendor->created_at->format('d F Y H:i:s') }}
                            <span class="text-xs text-gray-400 block mt-0.5">Oleh: {{ optional($tagihanSupirVendor->creator)->name ?? '-' }}</span>
                        </div>
                    </div>
                    
                    @if($tagihanSupirVendor->updated_at != $tagihanSupirVendor->created_at)
                    <div>
                        <div class="text-sm text-gray-500">Terakhir Diperbarui</div>
                        <div class="font-medium text-gray-900 mt-1">
                            {{ $tagihanSupirVendor->updated_at->format('d F Y H:i:s') }}
                            <span class="text-xs text-gray-400 block mt-0.5">Oleh: {{ optional($tagihanSupirVendor->updater)->name ?? '-' }}</span>
                        </div>
                    </div>
                    @endif
                    
                    @if($tagihanSupirVendor->keterangan)
                    <div>
                        <div class="text-sm text-gray-500">Keterangan Tambahan</div>
                        <div class="w-full mt-1 p-3 bg-gray-50 border border-gray-100 rounded-lg text-sm text-gray-700 leading-relaxed min-h-[80px]">
                            {{ $tagihanSupirVendor->keterangan }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            @if(auth()->user()->can('tagihan-supir-vendor-update'))
            <a href="{{ route('tagihan-supir-vendor.edit', $tagihanSupirVendor->id) }}" class="inline-flex justify-center items-center px-4 py-2 bg-amber-500 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-amber-600 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Tagihan
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
