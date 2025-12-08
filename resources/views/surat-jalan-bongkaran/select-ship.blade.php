@extends('layouts.app')

@section('title', 'Pilih Kapal & Voyage - Surat Jalan Bongkaran')

@section('content')
<div class="flex-1 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pilih Kapal & Voyage</h1>
            <nav class="flex text-sm text-gray-600 mt-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-2">/</span>
                <a href="{{ route('surat-jalan-bongkaran.select-ship') }}" class="hover:text-blue-600">Surat Jalan Bongkaran</a>
                <span class="mx-2">/</span>
                <span class="text-gray-500">Pilih Kapal & Voyage</span>
            </nav>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 max-w-2xl mx-auto">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Pilih Kapal dan Voyage</h2>
            <p class="text-sm text-gray-600 mt-1">Silakan pilih kapal dan voyage untuk melihat data Bill of Lading</p>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <form method="GET" action="{{ route('surat-jalan-bongkaran.list') }}" id="selectShipForm">
                <div class="space-y-6">
                    <!-- Kapal Selection -->
                    <div>
                        <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">
                            Kapal <span class="text-red-500">*</span>
                        </label>
                        <select name="nama_kapal" 
                                id="nama_kapal" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($kapals as $kapal)
                                <option value="{{ $kapal }}" {{ request('nama_kapal') == $kapal ? 'selected' : '' }}>
                                    {{ $kapal }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Voyage Selection -->
                    <div>
                        <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">
                            Voyage <span class="text-red-500">*</span>
                        </label>
                        <select name="no_voyage" 
                                id="no_voyage" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required
                                {{ !request('nama_kapal') ? 'disabled' : '' }}>
                            <option value="">-- Pilih Voyage --</option>
                            @foreach($voyages as $voyage)
                                <option value="{{ $voyage }}" {{ request('no_voyage') == $voyage ? 'selected' : '' }}>
                                    {{ $voyage }}
                                </option>
                            @endforeach
                        </select>
                        @if(!request('nama_kapal'))
                            <p class="mt-1 text-sm text-gray-500">Silakan pilih kapal terlebih dahulu</p>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4">
                        <a href="{{ route('dashboard') }}" 
                           class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                id="submitBtn">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Lanjutkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kapalSelect = document.getElementById('nama_kapal');
    const voyageSelect = document.getElementById('no_voyage');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('selectShipForm');

    // Function to load voyages for selected kapal
    function loadVoyages(kapalName) {
        if (!kapalName) {
            voyageSelect.disabled = true;
            voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option>';
            return;
        }

        // Show loading state
        voyageSelect.disabled = true;
        voyageSelect.innerHTML = '<option value="">Memuat voyage...</option>';

        // Make AJAX request to get voyages
        fetch(`{{ route('surat-jalan-bongkaran.get-voyages') }}?nama_kapal=${encodeURIComponent(kapalName)}`)
            .then(response => response.json())
            .then(data => {
                voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option>';
                
                if (data.success && data.voyages && data.voyages.length > 0) {
                    data.voyages.forEach(voyage => {
                        const option = document.createElement('option');
                        option.value = voyage;
                        option.textContent = voyage;
                        voyageSelect.appendChild(option);
                    });
                    voyageSelect.disabled = false;
                } else {
                    voyageSelect.innerHTML = '<option value="">Tidak ada voyage ditemukan</option>';
                    voyageSelect.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error loading voyages:', error);
                voyageSelect.innerHTML = '<option value="">Error memuat voyage</option>';
                voyageSelect.disabled = true;
            });
    }

    // When kapal changes, load voyages via AJAX
    kapalSelect.addEventListener('change', function() {
        const kapal = this.value;
        loadVoyages(kapal);
        updateSubmitButton();
    });

    // Enable/disable submit button based on selections
    function updateSubmitButton() {
        const kapalValue = kapalSelect.value;
        const voyageValue = voyageSelect.value;
        
        if (kapalValue && voyageValue) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    voyageSelect.addEventListener('change', updateSubmitButton);
    
    // Load voyages if kapal is already selected (from URL parameter or page reload)
    const initialKapalValue = kapalSelect.value;
    if (initialKapalValue) {
        loadVoyages(initialKapalValue);
    }
    
    // Initial check
    updateSubmitButton();
});
</script>
@endpush
