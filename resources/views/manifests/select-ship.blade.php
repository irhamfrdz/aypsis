@extends('layouts.app')

@section('title', 'Pilih Kapal - Manifest')
@section('page_title', 'Pilih Kapal - Manifest')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-indigo-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 border border-purple-100">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-8">
                <div class="flex items-center justify-center">
                    <div class="bg-white/20 backdrop-blur-sm rounded-full p-4 mr-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-white">
                        <h1 class="text-3xl font-bold">Manifest Pengiriman</h1>
                        <p class="text-purple-100 mt-2">Pilih kapal dan voyage untuk melihat data manifest</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <form method="GET" action="{{ route('report.manifests.index') }}" id="selectShipForm">
                <div class="space-y-6">
                    
                    <!-- Ship Selection -->
                    <div>
                        <label for="nama_kapal" class="block text-sm font-semibold text-gray-700 mb-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                                Nama Kapal <span class="text-red-500">*</span>
                            </div>
                        </label>
                        <select name="nama_kapal" id="nama_kapal" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($ships as $ship)
                                <option value="{{ $ship->nama_kapal }}" {{ request('nama_kapal') == $ship->nama_kapal ? 'selected' : '' }}>
                                    {{ $ship->nama_kapal }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-sm text-gray-500">Pilih kapal yang akan dilihat manifest-nya</p>
                    </div>

                    <!-- Voyage Input -->
                    <div>
                        <label for="no_voyage" class="block text-sm font-semibold text-gray-700 mb-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                                Nomor Voyage <span class="text-red-500">*</span>
                            </div>
                        </label>
                        <input type="text" name="no_voyage" id="no_voyage" required
                               value="{{ request('no_voyage') }}"
                               placeholder="Contoh: 001, 002, etc."
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                        <p class="mt-2 text-sm text-gray-500">Masukkan nomor voyage kapal</p>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-purple-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-purple-800">
                                <p class="font-semibold mb-1">Informasi:</p>
                                <ul class="list-disc list-inside space-y-1 text-purple-700">
                                    <li>Pilih kapal dan voyage untuk melihat daftar manifest</li>
                                    <li>Data manifest akan ditampilkan berdasarkan kapal dan voyage yang dipilih</li>
                                    <li>Anda dapat menambah, edit, atau hapus manifest setelah memilih kapal</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" 
                                class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold px-6 py-3 rounded-xl hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                Lanjutkan
                            </div>
                        </button>
                        <a href="{{ url()->previous() }}" 
                           class="flex-1 bg-gray-100 text-gray-700 font-semibold px-6 py-3 rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 text-center">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Kembali
                            </div>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Recent Ships (Optional) -->
        @if($ships->count() > 0)
        <div class="mt-8 bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Kapal yang Tersedia
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($ships->take(6) as $ship)
                <div class="bg-gray-50 hover:bg-purple-50 border border-gray-200 hover:border-purple-300 rounded-lg p-3 transition-all duration-200 cursor-pointer" 
                     onclick="document.getElementById('nama_kapal').value='{{ $ship->nama_kapal }}'; document.getElementById('nama_kapal').dispatchEvent(new Event('change'));">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">{{ $ship->nama_kapal }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Auto-focus on ship select
    document.addEventListener('DOMContentLoaded', function() {
        const namaKapal = document.getElementById('nama_kapal');
        if (namaKapal && !namaKapal.value) {
            namaKapal.focus();
        }
    });

    // Form validation
    document.getElementById('selectShipForm').addEventListener('submit', function(e) {
        const namaKapal = document.getElementById('nama_kapal').value;
        const noVoyage = document.getElementById('no_voyage').value;

        if (!namaKapal || !noVoyage) {
            e.preventDefault();
            alert('Mohon lengkapi nama kapal dan nomor voyage');
            return false;
        }
    });
</script>
@endpush
@endsection
