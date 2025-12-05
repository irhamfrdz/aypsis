@extends('layouts.app')

@section('title', 'Surat Jalan Bongkaran')

@section('content')
<div class="flex-1 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Surat Jalan Bongkaran</h1>
            <nav class="flex text-sm text-gray-600 mt-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-2">/</span>
                <span class="text-gray-500">Surat Jalan Bongkaran</span>
            </nav>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
            <button type="button" class="ml-auto text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('error') }}</span>
            <button type="button" class="ml-auto text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Surat Jalan Bongkaran</h2>
                </div>
                <div>
                    <a href="{{ route('surat-jalan-bongkaran.select-kapal') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Surat Jalan
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <!-- Search Form -->
            <form method="GET" action="{{ route('surat-jalan-bongkaran.index') }}" class="mb-6">
                
                <div class="flex flex-col gap-4">
                    
                    <!-- Search and Filter Row -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   id="search" 
                                   name="search" 
                                   placeholder="Cari nomor surat jalan, container, seal, supir, plat..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Cari
                            </button>
                            <a href="{{ route('surat-jalan-bongkaran.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <!-- Surat Jalan Bongkaran Table -->
                <table class="min-w-full divide-y divide-gray-200" id="suratJalanTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Surat Jalan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Plat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Container</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Barang</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($suratJalans as $index => $sj)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-block text-left">
                                            <button type="button" onclick="toggleDropdown('dropdown-sj-{{ $sj->id }}')"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>

                                            <div id="dropdown-sj-{{ $sj->id }}" class="hidden absolute left-0 z-50 mt-1 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                                                <div class="py-1">
                                                    <a href="#" onclick="editSuratJalan({{ $sj->id }}); return false;" 
                                                       class="group flex items-center px-3 py-2 text-xs text-indigo-700 hover:bg-indigo-50 hover:text-indigo-900">
                                                        <svg class="mr-2 h-4 w-4 text-indigo-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Edit
                                                    </a>
                                                    <a href="#" onclick="printSJBongkaran({{ $sj->id }}); return false;" 
                                                       class="group flex items-center px-3 py-2 text-xs text-blue-700 hover:bg-blue-50 hover:text-blue-900">
                                                        <svg class="mr-2 h-4 w-4 text-blue-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                        </svg>
                                                        Print
                                                    </a>
                                                    <a href="#" onclick="deleteSuratJalan({{ $sj->id }}); return false;" 
                                                       class="group flex items-center px-3 py-2 text-xs text-red-700 hover:bg-red-50 hover:text-red-900">
                                                        <svg class="mr-2 h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        Hapus
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $suratJalans->firstItem() + $index }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-semibold text-gray-900">{{ $sj->nomor_surat_jalan ?: '-' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/m/Y') : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->supir ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->no_plat ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->no_kontainer ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($sj->jenis_barang, 30) ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data Surat Jalan</h3>
                                            <p class="text-gray-500">Belum ada surat jalan bongkaran yang tersedia untuk kapal dan voyage ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
            </div>

            <!-- Pagination -->
            @if(isset($suratJalans) && $suratJalans->hasPages())
                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Menampilkan {{ $suratJalans->firstItem() }} sampai {{ $suratJalans->lastItem() }} 
                        dari {{ $suratJalans->total() }} data
                    </div>
                    <div>
                        {{ $suratJalans->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
<script>
// Toggle dropdown menu for action buttons
function toggleDropdown(dropdownId) {
    // Close all other dropdowns first
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        if (dropdown.id !== dropdownId) {
            dropdown.classList.add('hidden');
        }
    });
    // Toggle the clicked dropdown
    const dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleDropdown"]') && !event.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

// Buat Surat Jalan function - Open modal and populate with BL data

// Setup auto-fill plat nomor when supir is selected in modal
function setupModalSupirAutoFill() {
    const supirSelect = document.getElementById('modal_supir');
    const noPlatInput = document.getElementById('modal_no_plat');
    
    if (supirSelect && noPlatInput) {
        // Remove existing listener if any
        supirSelect.removeEventListener('change', handleModalSupirChange);
        // Add new listener
        supirSelect.addEventListener('change', handleModalSupirChange);
    }
}

function handleModalSupirChange(e) {
    const selectedOption = e.target.options[e.target.selectedIndex];
    const platNumber = selectedOption.getAttribute('data-plat');
    const noPlatInput = document.getElementById('modal_no_plat');
    
    if (platNumber && platNumber.trim() !== '') {
        noPlatInput.value = platNumber;
    }
}

// Setup auto-calculate uang jalan based on tujuan pengambilan in modal
function setupModalUangJalanCalculation(containerSize) {
    const tujuanPengambilanSelect = document.getElementById('modal_tujuan_pengambilan');
    const uangJalanNominalInput = document.getElementById('modal_uang_jalan_nominal');
    const uangJalanTypeRadios = document.querySelectorAll('input[name="uang_jalan_type"]');
    
    function calculateModalUangJalan() {
        const selectedOption = tujuanPengambilanSelect.options[tujuanPengambilanSelect.selectedIndex];
        const uangJalan20 = parseFloat(selectedOption.getAttribute('data-uang-jalan-20')) || 0;
        const uangJalan40 = parseFloat(selectedOption.getAttribute('data-uang-jalan-40')) || 0;
        const uangJalanType = document.querySelector('input[name="uang_jalan_type"]:checked');
        
        let uangJalan = 0;
        
        // Determine uang jalan based on container size
        if (containerSize === '20' || containerSize === '20ft') {
            uangJalan = uangJalan20;
        } else if (containerSize === '40' || containerSize === '40ft' || containerSize === '40hc' || containerSize === '40 hc') {
            uangJalan = uangJalan40;
        } else {
            // Default to 20ft if size is not clear
            uangJalan = uangJalan20;
        }
        
        // Apply half calculation if "setengah" is selected
        if (uangJalanType && uangJalanType.value === 'setengah') {
            uangJalan = uangJalan / 2;
        }
        
        if (uangJalan > 0) {
            uangJalanNominalInput.value = Math.round(uangJalan);
        }
    }
    
    if (tujuanPengambilanSelect && uangJalanNominalInput) {
        // Remove existing listeners
        tujuanPengambilanSelect.removeEventListener('change', calculateModalUangJalan);
        
        // Add new listeners
        tujuanPengambilanSelect.addEventListener('change', calculateModalUangJalan);
        
        uangJalanTypeRadios.forEach(radio => {
            radio.removeEventListener('change', calculateModalUangJalan);
            radio.addEventListener('change', calculateModalUangJalan);
        });
    }
}

// Handle form submit with validation and loading state
function handleFormSubmit(event) {
    event.preventDefault();
    
    const form = document.getElementById('formBuatSuratJalan');
    const submitBtn = document.getElementById('btnSubmitModal');
    const submitText = document.getElementById('btnSubmitText');
    const submitLoading = document.getElementById('btnSubmitLoading');
    
    // Validate required fields
    const nomorSuratJalan = document.getElementById('modal_nomor_surat_jalan').value.trim();
    const tanggalSuratJalan = document.getElementById('modal_tanggal_surat_jalan').value.trim();
    
    if (!nomorSuratJalan) {
        showModalAlert('Field Wajib Diisi!', 'Nomor Surat Jalan harus diisi sebelum menyimpan.', 'error');
        document.getElementById('modal_nomor_surat_jalan').focus();
        return false;
    }
    
    if (!tanggalSuratJalan) {
        showModalAlert('Field Wajib Diisi!', 'Tanggal Surat Jalan harus diisi sebelum menyimpan.', 'error');
        document.getElementById('modal_tanggal_surat_jalan').focus();
        return false;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');
    
    // Submit form via AJAX
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw data;
            });
        }
        return response.json();
    })
    .then(data => {
        // Success - stay on index page and show success message
        closeModal();
        
        // Show success message
        showSuccessAlert('Berhasil!', 'Surat jalan bongkaran berhasil dibuat dan disimpan.');
        
        // Reload page to refresh data and show updated table
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Reset button state
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
        
        // Show error message
        let errorMessage = '';
        let errorTitle = 'Validasi Gagal!';
        
        if (error.errors && Object.keys(error.errors).length > 0) {
            // Laravel validation errors - format as list
            errorTitle = 'Validasi Gagal! Silakan periksa kembali data yang diinput:';
            const errorItems = [];
            
            for (const [field, messages] of Object.entries(error.errors)) {
                const fieldLabel = getFieldLabel(field);
                messages.forEach(msg => {
                    errorItems.push(`<li class="ml-4"><strong>${fieldLabel}:</strong> ${msg}</li>`);
                });
            }
            
            errorMessage = `<ul class="list-disc mt-2 text-sm">${errorItems.join('')}</ul>`;
        } else if (error.message) {
            errorMessage = error.message;
        } else {
            errorTitle = 'Terjadi Kesalahan!';
            errorMessage = 'Gagal menyimpan surat jalan. Silakan coba lagi atau hubungi administrator.';
        }
        
        showModalAlert(errorTitle, errorMessage, 'error');
    });
    
    return false;
}

// Get field label in Indonesian
function getFieldLabel(fieldName) {
    const labels = {
        'nomor_surat_jalan': 'Nomor Surat Jalan',
        'tanggal_surat_jalan': 'Tanggal Surat Jalan',
        'term': 'Term',
        'aktifitas': 'Aktifitas',
        'pengirim': 'Pengirim',
        'jenis_barang': 'Jenis Barang',
        'tujuan_alamat': 'Tujuan Alamat',
        'tujuan_pengambilan': 'Tujuan Pengambilan',
        'tujuan_pengiriman': 'Tujuan Pengiriman',
        'jenis_pengiriman': 'Jenis Pengiriman',
        'tanggal_ambil_barang': 'Tanggal Ambil Barang',
        'supir': 'Supir',
        'no_plat': 'No Plat',
        'kenek': 'Kenek',
        'krani': 'Krani',
        'no_kontainer': 'No Kontainer',
        'no_seal': 'No Seal',
        'no_bl': 'Nomor BL',
        'size': 'Size Kontainer',
        'karton': 'Karton',
        'plastik': 'Plastik',
        'terpal': 'Terpal',
        'rit': 'RIT',
        'uang_jalan_type': 'Tipe Uang Jalan',
        'uang_jalan_nominal': 'Nominal Uang Jalan',
        'nama_kapal': 'Nama Kapal',
        'no_voyage': 'No Voyage',
    };
    
    return labels[fieldName] || fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

// Show alert inside modal
function showModalAlert(title, message, type = 'error') {
    // Remove existing alert if any
    const existingAlert = document.querySelector('.modal-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `modal-alert mb-4 px-4 py-3 rounded-lg ${
        type === 'error' 
            ? 'bg-red-50 border border-red-200 text-red-800' 
            : 'bg-green-50 border border-green-200 text-green-800'
    }`;
    
    alertDiv.innerHTML = `
        <div class="flex items-start w-full">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                ${type === 'error' 
                    ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                    : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                }
            </svg>
            <div class="flex-1">
                <div class="font-semibold mb-1">${title}</div>
                <div class="text-sm">${message}</div>
            </div>
            <button type="button" class="ml-3 flex-shrink-0 ${type === 'error' ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'}" onclick="this.parentElement.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    `;
    
    const modalBody = document.querySelector('#formBuatSuratJalan');
    modalBody.insertBefore(alertDiv, modalBody.firstChild);
    
    // Auto-scroll to top of modal to show alert
    const modalContent = document.querySelector('#modalBuatSuratJalan .max-h-\\[70vh\\]');
    if (modalContent) {
        modalContent.scrollTop = 0;
    }
}

// Show success alert on main page
function showSuccessAlert(title, message) {
    // Remove existing alerts if any
    const existingAlerts = document.querySelectorAll('.page-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = 'page-alert bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center';
    
    alertDiv.innerHTML = `
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span><strong>${title}</strong> ${message}</span>
        <button type="button" class="ml-auto text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    `;
    
    // Insert after page header
    const pageHeader = document.querySelector('.flex-1.p-6 > .flex.items-center.justify-between');
    if (pageHeader && pageHeader.parentNode) {
        pageHeader.parentNode.insertBefore(alertDiv, pageHeader.nextSibling);
    } else {
        // Fallback: insert at the beginning of the main content
        const mainContent = document.querySelector('.flex-1.p-6');
        if (mainContent) {
            mainContent.insertBefore(alertDiv, mainContent.firstChild);
        }
    }
    
    // Scroll to top to show the alert
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Close modal function
function closeModal() {
    document.getElementById('modalBuatSuratJalan').classList.add('hidden');
    const form = document.getElementById('formBuatSuratJalan');
    form.reset();
    
    // Reset select fields specifically
    document.getElementById('modal_term').value = '';
    document.getElementById('modal_aktifitas').value = '';
    document.getElementById('modal_tujuan_pengambilan').value = '';
    document.getElementById('modal_jenis_pengiriman').value = '';
    document.getElementById('modal_supir').value = '';
    document.getElementById('modal_kenek').value = '';
    document.getElementById('modal_krani').value = '';
    
    // Reset button state
    const submitBtn = document.getElementById('btnSubmitModal');
    const submitText = document.getElementById('btnSubmitText');
    const submitLoading = document.getElementById('btnSubmitLoading');
    
    submitBtn.disabled = false;
    submitText.classList.remove('hidden');
    submitLoading.classList.add('hidden');
    
    // Remove any alerts
    const existingAlert = document.querySelector('.modal-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('modalBuatSuratJalan');
    if (event.target === modal) {
        closeModal();
    }
});

// Print SJ function - Print surat jalan bongkaran
function printSJBongkaran(suratJalanId) {
    // Open print page in new window/tab
    const printUrl = `/surat-jalan-bongkaran/${suratJalanId}/print`;
    window.open(printUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
}

// Print BA function - Print Berita Acara directly from BL data

// Functions for Surat Jalan Bongkaran mode
function editSuratJalan(suratJalanId) {
    // Redirect to edit page or open edit modal
    window.location.href = '/surat-jalan-bongkaran/' + suratJalanId + '/edit';
}


function deleteSuratJalan(suratJalanId) {
    if (confirm('Apakah Anda yakin ingin menghapus surat jalan ini?')) {
        // Create a form to submit delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/surat-jalan-bongkaran/' + suratJalanId;
        form.style.display = 'none';

        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);

        // Add DELETE method
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}

</script>
@endpush
