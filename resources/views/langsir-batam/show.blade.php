@extends('layouts.app')

@section('page_title', 'Detail Langsir Kontainer Batam')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Detail Langsir Kontainer</h1>
                    <p class="text-xs text-gray-600 mt-1">Informasi lengkap transaksi langsir Batam</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('langsir-batam.index') }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    @can('langsir-batam-update')
                    <a href="{{ route('langsir-batam.edit', $langsir->id) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Data
                    </a>
                    @endcan
                </div>
            </div>

            <div class="p-6">
                <!-- Info Header Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <div class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-1">No. Transaksi</div>
                        <div class="text-lg font-black text-blue-900">{{ $langsir->no_transaksi }}</div>
                    </div>
                    <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                        <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">Tanggal</div>
                        <div class="text-lg font-black text-emerald-900">{{ $langsir->tanggal->format('d F Y') }}</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
                        <div class="text-[10px] font-bold text-purple-600 uppercase tracking-widest mb-1">Status</div>
                        <div class="flex items-center mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 uppercase">
                                {{ $langsir->status }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Detail Pengiriman -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center">
                            <span class="w-1.5 h-4 bg-blue-600 rounded-sm mr-2"></span>
                            Detail Kontainer & Rute
                        </h3>
                        <div class="bg-gray-50 rounded-xl p-4 space-y-4 border border-gray-100 shadow-inner">
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="text-xs text-gray-500 uppercase">No. Kontainer</span>
                                <span class="text-sm font-bold text-gray-900">{{ $langsir->no_kontainer }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="text-xs text-gray-500 uppercase">Size</span>
                                <span class="text-sm font-bold text-gray-900">{{ $langsir->size }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                                <span class="text-xs text-gray-500 uppercase">No. Seal</span>
                                <span class="text-sm font-bold text-gray-900">{{ $langsir->no_seal ?? '-' }}</span>
                            </div>
                            <div class="pt-2">
                                <div class="text-[10px] text-gray-500 uppercase mb-2">Rute Perjalanan</div>
                                <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                                    <div class="text-center flex-1">
                                        <div class="text-[10px] text-blue-600 font-bold uppercase mb-1">Dari</div>
                                        <div class="text-sm font-black text-gray-900">{{ $langsir->dari }}</div>
                                    </div>
                                    <div class="px-4">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </div>
                                    <div class="text-center flex-1">
                                        <div class="text-[10px] text-blue-600 font-bold uppercase mb-1">Ke</div>
                                        <div class="text-sm font-black text-gray-900">{{ $langsir->ke }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Transportasi & Biaya -->
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center">
                            <span class="w-1.5 h-4 bg-emerald-600 rounded-sm mr-2"></span>
                            Transportasi & Biaya
                        </h3>
                        <div class="bg-gray-50 rounded-xl p-4 space-y-4 border border-gray-100 shadow-inner">
                            <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200 shadow-sm">
                                <div class="p-2 bg-emerald-100 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[10px] text-gray-500 uppercase tracking-tighter">Nama Supir</div>
                                    <div class="text-sm font-bold text-gray-900">{{ $langsir->supir ?? '-' }}</div>
                                </div>
                                <div class="ml-auto text-right">
                                    <div class="text-[10px] text-gray-500 uppercase tracking-tighter">No. Plat</div>
                                    <div class="text-sm font-bold text-gray-900">{{ $langsir->no_plat ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="bg-blue-600 text-white rounded-xl p-5 shadow-lg transform hover:scale-[1.02] transition-transform">
                                <div class="text-xs font-medium uppercase tracking-widest opacity-80 mb-1">Biaya Langsir</div>
                                <div class="text-3xl font-black">Rp {{ number_format($langsir->biaya, 0, ',', '.') }}</div>
                            </div>

                            <div class="pt-2">
                                <div class="text-[10px] text-gray-500 uppercase mb-1">Keterangan</div>
                                <div class="bg-white p-3 rounded-lg border border-gray-200 min-h-[60px] text-sm text-gray-700 italic">
                                    {{ $langsir->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Info -->
                <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center text-[10px] text-gray-400 uppercase tracking-widest gap-4">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Diinput Oleh: <span class="text-gray-600 font-bold ml-1">{{ $langsir->inputUser->name ?? 'System' }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Terakhir Update: <span class="text-gray-600 font-bold ml-1">{{ $langsir->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
