@extends('layouts.app')

@section('title', 'Pilih Kapal - Manifest')
@section('page_title', 'Pilih Kapal - Manifest')

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
                            <h1 class="text-2xl font-bold" style="color: white;">Manifest Pengiriman</h1>
                            <p style="color: #c4b5fd;" class="text-sm mt-1">Pilih kapal dan voyage untuk melihat data manifest</p>
                        </div>
                    </div>
                    
                    <!-- Right Side: Button -->
                    @can('manifest-create')
                    <div class="flex-shrink-0">
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

<!-- Bulk Import Modal -->
<div id="bulkImportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3 mr-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Import Manifest (Bulk)</h3>
                    <p class="text-sm text-gray-500">Upload file Excel dengan data lengkap</p>
                </div>
            </div>
            <button onclick="closeBulkImportModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form action="{{ route('report.manifests.bulk-import') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <!-- Info Banner -->
            <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-2">Format File Excel:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs text-blue-700">
                            <li>File harus berisi kolom: <strong>Nama Kapal</strong> dan <strong>No Voyage</strong></li>
                            <li>Data manifest akan dikelompokkan otomatis per kapal dan voyage</li>
                            <li>Format: .xlsx atau .xls (maksimal 10MB)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Download Template -->
            <div class="mb-6">
                <a href="{{ route('report.manifests.download-bulk-template') }}" 
                   class="inline-flex items-center text-sm text-purple-600 hover:text-purple-800 font-semibold transition-colors bg-purple-50 hover:bg-purple-100 px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Template Excel
                </a>
            </div>

            <!-- File Upload -->
            <div class="mb-6">
                <label for="bulk_import_file" class="block text-sm font-semibold text-gray-700 mb-3">
                    Pilih File Excel <span class="text-red-500">*</span>
                </label>
                <div class="relative border-2 border-dashed border-gray-300 rounded-xl hover:border-green-400 transition-colors">
                    <input type="file" 
                           name="file" 
                           id="bulk_import_file" 
                           accept=".xlsx,.xls"
                           required
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">
                            <span class="font-semibold text-green-600">Klik untuk upload</span>
                            atau drag & drop
                        </p>
                        <p class="mt-1 text-xs text-gray-500">Excel (.xlsx, .xls) maksimal 10MB</p>
                        <p id="fileName" class="mt-2 text-sm font-medium text-green-600"></p>
                    </div>
                </div>
            </div>

            <!-- Example Table -->
            <div class="mb-6 bg-gray-50 rounded-xl p-4">
                <p class="text-xs font-semibold text-gray-700 mb-2">Contoh format Excel (25 kolom):</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs border border-gray-300">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border border-gray-300 px-2 py-1">Nama Kapal</th>
                                <th class="border border-gray-300 px-2 py-1">No Voyage</th>
                                <th class="border border-gray-300 px-2 py-1">Tanggal Berangkat</th>
                                <th class="border border-gray-300 px-2 py-1">No BL</th>
                                <th class="border border-gray-300 px-2 py-1">No Manifest</th>
                                <th class="border border-gray-300 px-2 py-1">No Tanda Terima</th>
                                <th class="border border-gray-300 px-2 py-1">No Kontainer</th>
                                <th class="border border-gray-300 px-2 py-1">...</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white">
                                <td class="border border-gray-300 px-2 py-1">KM EXAMPLE</td>
                                <td class="border border-gray-300 px-2 py-1">001</td>
                                <td class="border border-gray-300 px-2 py-1">2026-01-15</td>
                                <td class="border border-gray-300 px-2 py-1">BL001</td>
                                <td class="border border-gray-300 px-2 py-1">MN001</td>
                                <td class="border border-gray-300 px-2 py-1">TT001</td>
                                <td class="border border-gray-300 px-2 py-1">CONT001</td>
                                <td class="border border-gray-300 px-2 py-1">...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-600 mt-2">
                    <strong>Total 25 kolom:</strong> Nama Kapal, No Voyage, Tanggal Berangkat, No BL, No Manifest, No Tanda Terima, 
                    No Kontainer, No Seal, Tipe Kontainer, Size Kontainer, Nama Barang, Pengirim, Alamat Pengirim, 
                    Penerima, Alamat Penerima, Contact Person, Term, Tonnage, Volume, Satuan, 
                    Kuantitas, Pelabuhan Muat, Pelabuhan Bongkar, Asal Kontainer, Ke
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button type="button" 
                        onclick="closeBulkImportModal()"
                        class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-all shadow-lg hover:shadow-xl">
                    <div class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <span>Upload & Import</span>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Bulk Import Modal Functions
window.openBulkImportModal = function() {
    document.getElementById('bulkImportModal').classList.remove('hidden');
    document.getElementById('bulkImportModal').classList.add('flex');
}

window.closeBulkImportModal = function() {
    document.getElementById('bulkImportModal').classList.add('hidden');
    document.getElementById('bulkImportModal').classList.remove('flex');
    document.getElementById('bulk_import_file').value = '';
    document.getElementById('fileName').textContent = '';
}

// Show selected filename
document.getElementById('bulk_import_file')?.addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || '';
    const fileNameDisplay = document.getElementById('fileName');
    if (fileName) {
        fileNameDisplay.textContent = '📄 ' + fileName;
    } else {
        fileNameDisplay.textContent = '';
    }
});

// Close modal on outside click
document.getElementById('bulkImportModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        window.closeBulkImportModal();
    }
});
</script>

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
        fetch(`/api/manifests/voyages/${encodeURIComponent(namaKapal)}`)
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
