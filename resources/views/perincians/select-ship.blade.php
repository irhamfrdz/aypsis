@extends('layouts.app')

@section('title', 'Pilih Kapal - Perincian')
@section('page_title', 'Pilih Kapal - Perincian')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-md">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-green-800">{{ session('success') }}</p>
                    @if(session('imported_count'))
                    <p class="text-sm text-green-700 mt-1">✓ {{ session('imported_count') }} data berhasil diimport</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Warning/Error Message -->
        @if(session('warning') || session('errors_list') || session('failed_count'))
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 shadow-md">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-yellow-800">
                        {{ session('warning') ?? 'Terdapat data yang gagal diimport' }}
                    </p>
                    @if(session('failed_count'))
                    <p class="text-sm text-yellow-700 mt-1">✗ {{ session('failed_count') }} data gagal diimport</p>
                    @endif
                    
                    @if(session('errors_list'))
                    <div class="mt-3 bg-white rounded-lg p-3 max-h-60 overflow-y-auto">
                        <p class="text-xs font-semibold text-gray-700 mb-2">Detail Error:</p>
                        <ul class="space-y-1">
                            @foreach(session('errors_list') as $error)
                            <li class="text-xs text-red-600 flex items-start">
                                <span class="mr-2">•</span>
                                <span>{{ $error }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- General Error Message -->
        @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-md">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Header Card -->
        <div class="rounded-2xl shadow-xl overflow-hidden mb-8" style="background: linear-gradient(to right, #7c3aed, #4f46e5);">
            <div class="px-8 py-6">
                <div class="flex items-center justify-between gap-6">
                    <!-- Left Side: Icon + Title -->
                    <div class="flex items-center flex-1 min-w-0">
                        <div style="background: rgba(255,255,255,0.2);" class="rounded-full p-3 mr-4 flex-shrink-0">
                            <svg class="w-8 h-8" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-2xl font-bold" style="color: white;">Perincian Pengiriman</h1>
                            <p style="color: #c4b5fd;" class="text-sm mt-1">Pilih kapal dan voyage untuk melihat data perincian</p>
                        </div>
                    </div>
                    
                    <!-- Right Side: Button -->
                    @can('perincian-create')
                    <div class="flex-shrink-0 flex gap-2">
                        <button onclick="autoUpdateNomorUrutAll(event)" 
                                style="background: #eab308; color: white;"
                                class="hover:bg-yellow-600 px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-lg hover:shadow-xl whitespace-nowrap text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Update No. Urut (Semua)
                        </button>
                        <button onclick="openBulkImportModal()" 
                                style="background: white; color: #7c3aed;"
                                class="hover:bg-gray-50 px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-lg hover:shadow-xl whitespace-nowrap text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Import Excel
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <form method="GET" action="{{ route('report.perincians.index') }}" id="selectShipForm">
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
                        <p class="mt-2 text-sm text-gray-500">Pilih kapal yang akan dilihat perincian-nya</p>
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
                        <select name="no_voyage" id="no_voyage" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                            <option value="">-- Pilih Voyage --</option>
                        </select>
                        <p class="mt-2 text-sm text-gray-500" id="voyage-help-text">Pilih nama kapal terlebih dahulu</p>
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
                                    <li>Pilih kapal dan voyage untuk melihat daftar perincian</li>
                                    <li>Data perincian akan ditampilkan berdasarkan kapal dan voyage yang dipilih</li>
                                    <li>Anda dapat menambah, edit, atau hapus perincian setelah memilih kapal</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" 
                                class="flex-1 bg-purple-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <div class="flex items-center justify-center">
                                <span>Lanjutkan</span>
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </button>
                        <a href="{{ url()->previous() }}" 
                           class="flex-1 bg-gray-100 text-gray-700 font-semibold px-6 py-3 rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 text-center">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                <span>Kembali</span>
                            </div>
                        </a>
                    </div>
                </div>
            </form>
        </div>

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

        // Load voyages if ship is pre-selected
        const selectedShip = "{{ request('nama_kapal') }}";
        const selectedVoyage = "{{ request('no_voyage') }}";
        if (selectedShip) {
            loadVoyages(selectedShip, selectedVoyage);
        }
    });

    // Load voyages when ship is selected
    document.getElementById('nama_kapal')?.addEventListener('change', function() {
        const namaKapal = this.value;
        loadVoyages(namaKapal);
    });

    function loadVoyages(namaKapal, selectedVoyage = '') {
        const voyageSelect = document.getElementById('no_voyage');
        const helpText = document.getElementById('voyage-help-text');
        
        if (!namaKapal) {
            voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option>';
            voyageSelect.disabled = true;
            helpText.textContent = 'Pilih nama kapal terlebih dahulu';
            return;
        }

        // Show loading state
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;
        helpText.textContent = 'Memuat data voyage...';

        // Fetch voyages from server
        fetch(`/api/perincians/voyages/${encodeURIComponent(namaKapal)}`)
            .then(response => response.json())
            .then(data => {
                voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option>';
                
                if (data.voyages && data.voyages.length > 0) {
                    data.voyages.forEach(voyage => {
                        const option = document.createElement('option');
                        option.value = voyage;
                        option.textContent = voyage;
                        if (selectedVoyage && voyage === selectedVoyage) {
                            option.selected = true;
                        }
                        voyageSelect.appendChild(option);
                    });
                    voyageSelect.disabled = false;
                    helpText.textContent = `${data.voyages.length} voyage tersedia`;
                } else {
                    voyageSelect.innerHTML = '<option value="">Tidak ada voyage tersedia</option>';
                    helpText.textContent = 'Tidak ada data voyage untuk kapal ini';
                }
            })
            .catch(error => {
                console.error('Error loading voyages:', error);
                voyageSelect.innerHTML = '<option value="">Error loading voyages</option>';
                helpText.textContent = 'Gagal memuat data voyage';
            });
    }

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
