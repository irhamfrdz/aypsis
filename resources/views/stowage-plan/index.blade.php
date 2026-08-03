@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Card -->
        <div class="rounded-2xl shadow-xl overflow-hidden mb-8" style="background: linear-gradient(to right, #7c3aed, #4f46e5);">
            <div class="px-8 py-6">
                <div class="flex items-center justify-between gap-6">
                    <div class="flex items-center flex-1 min-w-0">
                        <div style="background: rgba(255,255,255,0.2);" class="rounded-full p-3 mr-4 flex-shrink-0">
                            <svg class="w-8 h-8" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-2xl font-bold" style="color: white;">Stowage Plan Management</h1>
                            <p style="color: #c4b5fd;" class="text-sm mt-1">Pilih kapal dan voyage untuk mengatur tata letak kontainer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <form id="selectShipForm">
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
                                <option value="{{ $ship->nama_kapal }}">{{ $ship->nama_kapal }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-sm text-gray-500">Pilih kapal yang akan diatur stowage plan-nya</p>
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
                                    <li>Pilih kapal dan voyage untuk mengatur tata letak kontainer.</li>
                                    <li>Anda bisa mengatur koordinat Bay, Row, dan Tier untuk tiap kontainer.</li>
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
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const namaKapal = document.getElementById('nama_kapal');
        if (namaKapal && !namaKapal.value) {
            namaKapal.focus();
        }
    });

    document.getElementById('nama_kapal')?.addEventListener('change', function() {
        const namaKapal = this.value;
        loadVoyages(namaKapal);
    });

    function loadVoyages(namaKapal) {
        const voyageSelect = document.getElementById('no_voyage');
        const helpText = document.getElementById('voyage-help-text');
        
        if (!namaKapal) {
            voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option>';
            voyageSelect.disabled = true;
            helpText.textContent = 'Pilih nama kapal terlebih dahulu';
            return;
        }

        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;
        helpText.textContent = 'Memuat data voyage...';

        fetch(`/api/manifests/voyages/${encodeURIComponent(namaKapal)}`)
            .then(response => response.json())
            .then(data => {
                voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option>';
                
                if (data.voyages && data.voyages.length > 0) {
                    data.voyages.forEach(voyage => {
                        const option = document.createElement('option');
                        option.value = voyage;
                        option.textContent = voyage;
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

    document.getElementById('selectShipForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const namaKapal = document.getElementById('nama_kapal').value;
        const noVoyage = document.getElementById('no_voyage').value;

        if (!namaKapal || !noVoyage) {
            alert('Mohon lengkapi nama kapal dan nomor voyage');
            return;
        }

        const url = `{{ url('/stowage-plan') }}/${encodeURIComponent(namaKapal)}?voyage=${encodeURIComponent(noVoyage)}`;
        window.location.href = url;
    });
</script>
@endpush
@endsection
