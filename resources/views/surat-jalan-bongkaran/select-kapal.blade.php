@extends('layouts.app')

@section('title', 'Pilih Kapal - Surat Jalan Bongkaran')

@section('content')
<div class="flex-1 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Surat Jalan Bongkaran</h1>
            <nav class="flex text-sm text-gray-600 mt-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Home</a>
                <span class="mx-2">/</span>
                <span class="text-gray-500">Surat_jalan_bongkaran</span>
            </nav>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 max-w-4xl mx-auto">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-blue-500">
            <h2 class="text-lg font-semibold text-white">Tambah Data</h2>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <form action="{{ route('surat-jalan-bongkaran.create') }}" method="GET" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kapal -->
                    <div>
                        <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-2">Kapal</label>
                        <select name="kapal_id" id="kapal_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-Pilih Kapal-</option>
                            @foreach($kapals as $kapal)
                                <option value="{{ $kapal->id }}" {{ request('kapal_id') == $kapal->id ? 'selected' : '' }}>
                                    {{ $kapal->nama_kapal }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- No Voyage -->
                    <div>
                        <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">No Voyage</label>
                        <select name="no_voyage" id="no_voyage" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-PILIH-</option>
                            <option value="" id="loading-option" style="display: none;">Loading...</option>
                        </select>
                    </div>
                </div>

                <!-- No BL -->
                <div>
                    <label for="no_bl" class="block text-sm font-medium text-gray-700 mb-2">No BL</label>
                    <select name="no_bl" id="no_bl"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-PILIH-</option>
                        <option value="" id="loading-bl-option" style="display: none;">Loading...</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-start space-x-4 pt-4">
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition-colors duration-200">
                        Submit
                    </button>
                    <button type="button" 
                            onclick="window.history.back()"
                            class="inline-flex items-center px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded transition-colors duration-200">
                        Cetak BA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kapalSelect = document.getElementById('kapal_id');
    const voyageSelect = document.getElementById('no_voyage');
    const blSelect = document.getElementById('no_bl');
    const loadingOption = document.getElementById('loading-option');
    const loadingBlOption = document.getElementById('loading-bl-option');
    
    let blData = {};
    
    kapalSelect.addEventListener('change', function() {
        const kapalId = this.value;
        
        // Reset voyage and BL dropdowns
        voyageSelect.innerHTML = '<option value="">-PILIH-</option>';
        blSelect.innerHTML = '<option value="">-PILIH-</option>';
        
        if (!kapalId) {
            return;
        }
        
        // Show loading
        loadingOption.style.display = 'block';
        voyageSelect.appendChild(loadingOption);
        
        // Fetch BL data via AJAX
        fetch(`{{ route('surat-jalan-bongkaran.bl-data') }}?kapal_id=${kapalId}`)
            .then(response => response.json())
            .then(data => {
                // Hide loading
                loadingOption.style.display = 'none';
                
                // Store BL data for later use
                blData = data.bls;
                
                // Populate voyage dropdown
                if (data.voyages && data.voyages.length > 0) {
                    data.voyages.forEach(voyage => {
                        const option = document.createElement('option');
                        option.value = voyage;
                        option.textContent = voyage;
                        // Maintain selected value if exists
                        if (voyage === "{{ request('no_voyage') }}") {
                            option.selected = true;
                        }
                        voyageSelect.appendChild(option);
                    });
                    
                    // If there's a selected voyage, populate BL dropdown
                    const selectedVoyage = voyageSelect.value;
                    if (selectedVoyage && blData[selectedVoyage]) {
                        populateBlDropdown(selectedVoyage);
                    }
                } else {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Tidak ada data voyage';
                    option.disabled = true;
                    voyageSelect.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Error fetching BL data:', error);
                loadingOption.style.display = 'none';
                
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Error loading data';
                option.disabled = true;
                voyageSelect.appendChild(option);
            });
    });
    
    voyageSelect.addEventListener('change', function() {
        const selectedVoyage = this.value;
        populateBlDropdown(selectedVoyage);
    });
    
    function populateBlDropdown(voyage) {
        // Reset BL dropdown
        blSelect.innerHTML = '<option value="">-PILIH-</option>';
        
        if (!voyage || !blData[voyage]) {
            return;
        }
        
        // Populate BL dropdown
        blData[voyage].forEach(bl => {
            const option = document.createElement('option');
            option.value = bl;
            option.textContent = bl;
            // Maintain selected value if exists
            if (bl === "{{ request('no_bl') }}") {
                option.selected = true;
            }
            blSelect.appendChild(option);
        });
        
        // Add option for no BL selected
        const noneOption = document.createElement('option');
        noneOption.value = '';
        noneOption.textContent = 'Tanpa BL';
        blSelect.appendChild(noneOption);
    }
    
    // Trigger kapal change if there's a pre-selected value
    if (kapalSelect.value) {
        kapalSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush