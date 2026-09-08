@extends('layouts.app')

@section('title', 'Input Tanggal - Gerak Voyage')
@section('page_title', 'Input Tanggal - Gerak Voyage')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Card -->
        <div class="rounded-2xl shadow-xl overflow-hidden mb-8" style="background: linear-gradient(to right, #2563eb, #4f46e5);">
            <div class="px-8 py-6">
                <div class="flex items-center justify-between gap-6">
                    <div class="flex items-center flex-1 min-w-0">
                        <div style="background: rgba(255,255,255,0.2);" class="rounded-full p-3 mr-4 flex-shrink-0">
                            <svg class="w-8 h-8" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-2xl font-bold" style="color: white;">Input Tanggal Gerak Voyage</h1>
                            <p style="color: #c7d2fe;" class="text-sm mt-1">Kapal: <strong>{{ $namaKapal }}</strong> | Voyage: <strong>{{ $noVoyage }}</strong></p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('gerak-voyage.index') }}" 
                           class="bg-white text-blue-600 hover:bg-gray-50 px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-lg hover:shadow-xl text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-bold mb-1">Ada kesalahan input:</p>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <form action="{{ route('gerak-voyage.store') }}" method="POST">
                @csrf
                <input type="hidden" name="nama_kapal" value="{{ $namaKapal }}">
                <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="tanggal_mulai_berlayar" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai Berlayar</label>
                        <input type="date" id="tanggal_mulai_berlayar" name="tanggal_mulai_berlayar" 
                               value="{{ old('tanggal_mulai_berlayar', $manifest ? ($manifest->tanggal_mulai_berlayar ? \Carbon\Carbon::parse($manifest->tanggal_mulai_berlayar)->format('Y-m-d') : '') : '') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50 hover:bg-white">
                    </div>

                    <div>
                        <label for="tanggal_berlabuh" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Berlabuh</label>
                        <input type="date" id="tanggal_berlabuh" name="tanggal_berlabuh" 
                               value="{{ old('tanggal_berlabuh', $manifest ? ($manifest->tanggal_berlabuh ? \Carbon\Carbon::parse($manifest->tanggal_berlabuh)->format('Y-m-d') : '') : '') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50 hover:bg-white">
                    </div>

                    <div>
                        <label for="tanggal_sandar" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Sandar</label>
                        <input type="date" id="tanggal_sandar" name="tanggal_sandar" 
                               value="{{ old('tanggal_sandar', $manifest ? ($manifest->tanggal_sandar ? \Carbon\Carbon::parse($manifest->tanggal_sandar)->format('Y-m-d') : '') : '') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50 hover:bg-white">
                    </div>

                    <div>
                        <label for="tanggal_mulai_bongkar" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai Bongkar</label>
                        <input type="date" id="tanggal_mulai_bongkar" name="tanggal_mulai_bongkar" 
                               value="{{ old('tanggal_mulai_bongkar', $manifest ? ($manifest->tanggal_mulai_bongkar ? \Carbon\Carbon::parse($manifest->tanggal_mulai_bongkar)->format('Y-m-d') : '') : '') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50 hover:bg-white">
                    </div>

                    <div>
                        <label for="tanggal_selesai_bongkar" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai Bongkar</label>
                        <input type="date" id="tanggal_selesai_bongkar" name="tanggal_selesai_bongkar" 
                               value="{{ old('tanggal_selesai_bongkar', $manifest ? ($manifest->tanggal_selesai_bongkar ? \Carbon\Carbon::parse($manifest->tanggal_selesai_bongkar)->format('Y-m-d') : '') : '') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50 hover:bg-white">
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-100">
                    <button type="submit" 
                            class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Tanggal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
