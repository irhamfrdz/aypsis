@extends('layouts.app')

@section('title', 'Detail Stock Amprahan')
@section('page_title', 'Detail Stock Amprahan')

@section('content')
<div class="container mx-auto px-4 py-8 text-white">
    <div class="max-w-4xl mx-auto">
        {{-- Breadcrumb --}}
        <nav class="flex mb-6 text-sm text-gray-500">
            <a href="{{ route('stock-amprahan.index') }}" class="hover:text-indigo-600 transition-colors">Stock Amprahan</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 font-medium tracking-tight">Detail Barang</span>
        </nav>

        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            {{-- Header Gradient --}}
            <div class="bg-gradient-to-br from-indigo-600 to-violet-700 px-8 py-10 text-white relative">
                <div class="relative z-10">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md mb-4 uppercase tracking-widest">Stock Information</span>
                    <h1 class="text-3xl font-extrabold tracking-tight mb-2 uppercase">{{ $item->masterNamaBarangAmprahan->nama_barang }}</h1>
                    <div class="flex items-center text-indigo-100 text-sm font-medium">
                        <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M13 7h.01M13 11h.01M13 15h.01M17 7h.01M17 11h.01M17 15h.01"/>
                        </svg>
                        ID: #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
                {{-- Decorative Circle --}}
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    {{-- Quantity & Unit --}}
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4 p-5 rounded-2xl bg-gray-50/80 border border-gray-100">
                            <div class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none mb-1 text-white">Stock Tersedia</p>
                                <p class="text-2xl font-black text-gray-900 leading-none">
                                    {{ number_format($item->jumlah, 0, ',', '.') }} 
                                    <span class="text-indigo-600 text-lg ml-1">{{ $item->satuan ?? 'Unit' }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4 p-5 rounded-2xl bg-gray-50/80 border border-gray-100">
                            <div class="p-3 bg-violet-100 text-violet-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Lokasi Penyimpanan</p>
                                <p class="text-lg font-bold text-gray-900 leading-none">
                                    {{ $item->lokasi ?? 'Tidak Ditentukan' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Info --}}
                    <div class="bg-gray-50/50 rounded-2xl p-6 border border-dashed border-gray-200">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Catatan / Keterangan</p>
                        <p class="text-sm text-gray-700 leading-relaxed italic">
                            {{ $item->keterangan ?? '"Tidak ada catatan khusus untuk barang ini."' }}
                        </p>
                    </div>
                </div>

                {{-- Audit Table Style --}}
                <div class="border-t border-gray-100 pt-8 mt-4">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6 flex items-center">
                        <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full mr-2.5"></span>
                        Activity Logs
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-blue-50/30 p-4 rounded-xl border border-blue-100/50">
                            <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mb-1 opacity-70">Created At</p>
                            <p class="text-sm font-bold text-gray-800">{{ $item->created_at->format('d M Y, H:i') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">by {{ $item->createdBy->name ?? 'System' }}</p>
                        </div>
                        <div class="bg-purple-50/30 p-4 rounded-xl border border-purple-100/50">
                            <p class="text-[10px] font-bold text-purple-500 uppercase tracking-widest mb-1 opacity-70">Last Modified</p>
                            <p class="text-sm font-bold text-gray-800">{{ $item->updated_at->format('d M Y, H:i') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">by {{ $item->updatedBy->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="mt-12 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-100 pt-8">
                    <a href="{{ route('stock-amprahan.index') }}" class="group flex items-center px-6 py-3 text-sm font-bold text-gray-500 hover:text-indigo-600 transition-all duration-300">
                        <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        KEMBALI KE DAFTAR
                    </a>
                    
                    <div class="flex space-x-3 w-full sm:w-auto">
                        @can('stock-amprahan-update')
                        <a href="{{ route('stock-amprahan.edit', $item->id) }}" class="flex-1 sm:flex-none inline-flex justify-center items-center px-8 py-3 bg-amber-500 hover:bg-amber-600 text-white text-sm font-black rounded-2xl shadow-lg shadow-amber-200 transition-all duration-300">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            EDIT DATA
                        </a>
                        @endcan

                        @can('stock-amprahan-delete')
                        <form action="{{ route('stock-amprahan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data ini secara permanen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center px-8 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-black rounded-2xl transition-all duration-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                HAPUS
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
