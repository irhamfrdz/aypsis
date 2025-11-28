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
            <form action="{{ route('surat-jalan-bongkaran.create') }}" method="GET" class="space-y-6" id="kapalForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kapal -->
                    <div>
                        <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">Kapal</label>
                        <select name="nama_kapal" id="nama_kapal" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-Pilih Kapal-</option>
                            @foreach($kapals as $kapal)
                                <option value="{{ $kapal->nama_kapal }}" {{ request('nama_kapal') == $kapal->nama_kapal ? 'selected' : '' }}>
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
                    <p class="mt-1 text-xs text-gray-500">Pilih kontainer dari voyage yang telah dipilih</p>
                    
                    <!-- Hidden fields for container details -->
                    <input type="hidden" name="container_seal" id="container_seal">
                    <input type="hidden" name="container_size" id="container_size">
                    <input type="hidden" name="jenis_barang" id="jenis_barang">
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
    const kapalSelect = document.getElementById('nama_kapal');
    const voyageSelect = document.getElementById('no_voyage');
    const blSelect = document.getElementById('no_bl');
    const loadingOption = document.getElementById('loading-option');
    const loadingBlOption = document.getElementById('loading-bl-option');
    
    let blData = {};
    
    kapalSelect.addEventListener('change', function() {
        const namaKapal = this.value;
        
        // Reset voyage and BL dropdowns
        voyageSelect.innerHTML = '<option value="">-PILIH-</option>';
        blSelect.innerHTML = '<option value="">-PILIH-</option>';
        
        if (!namaKapal) {
            return;
        }
        
        // Show loading
        loadingOption.style.display = 'block';
        voyageSelect.appendChild(loadingOption);
        
        // Fetch BL data via AJAX
        fetch(`{{ route('surat-jalan-bongkaran.bl-data') }}?nama_kapal=${encodeURIComponent(namaKapal)}`)
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
        
        // Populate BL dropdown with container data
        blData[voyage].forEach(container => {
            const option = document.createElement('option');
            // Use nomor_kontainer as value
            option.value = container.value;
            // Display with format
            option.textContent = container.text;
            // Store container details as data attributes
            if (container.id) {
                option.setAttribute('data-bl-id', container.id);
            }
            if (container.nomor_bl) {
                option.setAttribute('data-nomor-bl', container.nomor_bl);
            }
            if (container.no_seal) {
                option.setAttribute('data-no-seal', container.no_seal);
            }
            if (container.size) {
                option.setAttribute('data-size', container.size);
            }
            if (container.nama_barang) {
                option.setAttribute('data-nama-barang', container.nama_barang);
            }
            // Maintain selected value if exists
            // If either the BL number or container number matches request('no_bl'), select it
            if (container.value === "{{ request('no_bl') }}" || container.nomor_bl === "{{ request('no_bl') }}" || container.id == "{{ request('bl_id') }}") {
                option.selected = true;
                // Update hidden fields if this option is pre-selected
                updateContainerFields(container);
            }
            blSelect.appendChild(option);
        });
    }
    
    // Add event listener for BL selection change
    blSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            // Get container details from data attributes
            const containerData = {
                no_seal: selectedOption.getAttribute('data-no-seal') || '',
                size: selectedOption.getAttribute('data-size') || '',
                nomor_bl: selectedOption.getAttribute('data-nomor-bl') || '',
                nama_barang: selectedOption.getAttribute('data-nama-barang') || ''
            };
            updateContainerFields(containerData);
        } else {
            // Clear hidden fields if no container selected
            updateContainerFields({});
        }
    });
    
    function updateContainerFields(containerData) {
        const sealField = document.getElementById('container_seal');
        const sizeField = document.getElementById('container_size');
        const jenisBarangField = document.getElementById('jenis_barang');
        
        if (sealField) {
            sealField.value = containerData.no_seal || '';
        }
        if (sizeField) {
            sizeField.value = containerData.size || '';
        }
        if (jenisBarangField) {
            jenisBarangField.value = containerData.nama_barang || '';
        }
    }
    
    // Handle form submission to include container data as URL parameters
    const kapalForm = document.getElementById('kapalForm');
    kapalForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('Form submitted'); // Debug
        
        // Basic validation
        const namaKapal = document.getElementById('nama_kapal').value;
        const noVoyage = document.getElementById('no_voyage').value;
        
        if (!namaKapal || !noVoyage) {
            alert('Harap pilih kapal dan voyage terlebih dahulu!');
            return;
        }
        
        console.log('Validation passed'); // Debug
        
        const formData = new FormData(this);
        const params = new URLSearchParams();
        
        // Add form fields
        for (let [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
                console.log('Adding param:', key, '=', value); // Debug
            }
        }
        
        // Add container details if available
        const selectedBl = blSelect.options[blSelect.selectedIndex];
        if (selectedBl && selectedBl.value) {
            if (selectedBl.getAttribute('data-no-seal')) {
                params.append('container_seal', selectedBl.getAttribute('data-no-seal'));
            }
            if (selectedBl.getAttribute('data-size')) {
                params.append('container_size', selectedBl.getAttribute('data-size'));
            }
            if (selectedBl.getAttribute('data-nama-barang')) {
                params.append('jenis_barang', selectedBl.getAttribute('data-nama-barang'));
            }

            // Ensure both BL number and container number are sent.
            // Use data-nomor-bl (BL number) as the 'no_bl' parameter if available, otherwise fallback to the container number.
            const nomorBlValue = selectedBl.getAttribute('data-nomor-bl') || selectedBl.value;
            params.set('no_bl', nomorBlValue);
            // Also keep the container number as a separate param for clarity
            params.set('no_kontainer', selectedBl.value);
            // Also attach the BL id if available
            if (selectedBl.getAttribute('data-bl-id')) {
                params.set('bl_id', selectedBl.getAttribute('data-bl-id'));
            }
        }
        
        // Navigate to create page with parameters
        const finalUrl = this.action + '?' + params.toString();
        console.log('Navigating to:', finalUrl); // Debug
        window.location.href = finalUrl;
    });
    
    // Trigger kapal change if there's a pre-selected value
    if (kapalSelect.value) {
        kapalSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush