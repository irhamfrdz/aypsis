@extends('layouts.app')

@section('content')
<style>
/* Import page styles */
.drop-zone {
    border: 2px dashed #cbd5e0;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
    background: #f8fafc;
}

.drop-zone.dragover {
    border-color: #4f46e5;
    background: #eff6ff;
}

.drop-zone input[type="file"] {
    display: none;
}

.file-info {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 6px;
    padding: 12px;
    margin-top: 16px;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #10b981;
    border-radius: 4px;
    transition: width 0.3s ease;
    width: 0%;
}

.import-results {
    margin-top: 20px;
}

.result-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
}

.result-success {
    border-color: #10b981;
    background: #f0fdf4;
}

.result-error {
    border-color: #ef4444;
    background: #fef2f2;
}

.result-warning {
    border-color: #f59e0b;
    background: #fffbeb;
}

.validation-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 6px;
    padding: 12px;
    margin: 8px 0;
}

.validation-error ul {
    margin: 0;
    padding-left: 20px;
}

.sample-table {
    font-size: 12px;
}

.sample-table th,
.sample-table td {
    padding: 8px;
    border: 1px solid #e5e7eb;
    white-space: nowrap;
}

.sample-table th {
    background: #f3f4f6;
    font-weight: 600;
}
</style>

<div class="container mx-auto p-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Import Data Tagihan Kontainer Sewa</h1>
                    <p class="mt-2 text-gray-600">Upload file Excel atau CSV untuk mengimport data tagihan kontainer sewa secara bulk</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Dropdown for template formats -->
                    <div class="relative inline-block">
                        <button id="templateDropdown"
                                class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Template
                            <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="templateDropdownMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                            <a href="{{ route('daftar-tagihan-kontainer-sewa.export-template', ['format' => 'standard']) }}"
                               class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg border-b border-gray-100">
                                <div class="font-medium">📄 Template Standard</div>
                                <div class="text-xs text-gray-500">Format sederhana untuk input manual</div>
                            </a>
                            <a href="{{ route('daftar-tagihan-kontainer-sewa.export-template', ['format' => 'dpe']) }}"
                               class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg">
                                <div class="font-medium">📊 Template DPE</div>
                                <div class="text-xs text-gray-500">Format lengkap dengan data finansial</div>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Import Instructions -->
        <div class="mb-4 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Petunjuk Import</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <h3 class="font-medium text-gray-900 mb-2">Format CSV:</h3>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-blue-600">📄</span>
                            <span><strong>Standard:</strong> Delimiter koma (,)</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-green-600">📊</span>
                            <span><strong>DPE:</strong> Delimiter titik koma (;)</span>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-600">
                        <strong>Kolom:</strong> vendor, nomor_kontainer, size, group, tanggal_awal, tanggal_akhir, periode, tarif, status
                    </div>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900 mb-2">Requirements:</h3>
                    <ul class="text-gray-600 space-y-1">
                        <li>• CSV/TXT, delimiter: , atau ;</li>
                        <li>• Max 10MB, 1000 baris</li>
                        <li>• Encoding: UTF-8</li>
                        <li>• <strong>group</strong> boleh kosong (otomatis)</li>
                        <li>• Format tanggal: YYYY-MM-DD</li>
                    </ul>
                </div>
            </div>

            <!-- Collapsible Sample Data -->
            <div class="mt-3">
                <button type="button" onclick="toggleSampleTable()" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                    <span id="sampleToggleText">Lihat contoh format data</span>
                    <svg id="sampleToggleIcon" class="h-4 w-4 ml-1 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="sampleTable" class="hidden mt-2 overflow-x-auto">
                    <table class="sample-table w-full border-collapse border border-gray-300 text-xs">
                        <thead>
                            <tr>
                                <th>vendor</th>
                                <th>nomor_kontainer</th>
                                <th>size</th>
                                <th>group</th>
                                <th>tanggal_awal</th>
                                <th>tanggal_akhir</th>
                                <th>periode</th>
                                <th>tarif</th>
                                <th>status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>DPE</td>
                                <td>CCLU3836629</td>
                                <td>20</td>
                                <td></td>
                                <td>2025-01-21</td>
                                <td>2025-02-20</td>
                                <td>1</td>
                                <td>Bulanan</td>
                                <td>Tersedia</td>
                            </tr>
                            <tr>
                                <td>ZONA</td>
                                <td>ZONA001234</td>
                                <td>20</td>
                                <td>GROUP001</td>
                                <td>2024-01-01</td>
                                <td>2024-01-31</td>
                                <td>1</td>
                                <td>Bulanan</td>
                                <td>ongoing</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Import Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Upload File Import</h2>

            <form id="importForm" action="{{ route('daftar-tagihan-kontainer-sewa.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- File Upload Zone -->
                <div class="drop-zone" id="dropZone">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="text-lg font-medium text-gray-900 mb-2">Drag & drop file di sini</p>
                        <p class="text-sm text-gray-500 mb-4">atau klik untuk memilih file</p>
                        <button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-150">
                            Pilih File
                        </button>
                    </div>
                    <input type="file" id="fileInput" name="import_file" accept=".csv,.txt" required>
                </div>

                <!-- File Info (akan muncul setelah file dipilih) -->
                <div id="fileInfo" class="file-info hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-green-800" id="fileName"></p>
                                <p class="text-sm text-green-600" id="fileSize"></p>
                            </div>
                        </div>
                        <button type="button" id="removeFile" class="text-red-500 hover:text-red-700">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Import Options -->
                <div class="mt-6 space-y-4">
                    <!-- Validate Only Warning -->
                    <div id="validateOnlyWarning" class="hidden bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Peringatan:</strong> Mode validasi aktif. Data <strong>TIDAK AKAN TERSIMPAN</strong> ke database. Uncheck opsi ini untuk menyimpan data.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="validate_only" id="validateOnly" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">
                                <span class="font-medium">Hanya validasi</span>
                                <span class="text-red-600">(⚠️ tidak menyimpan data)</span>
                            </span>
                        </label>
                        <p class="ml-6 mt-1 text-xs text-gray-500">Gunakan untuk mengecek format CSV tanpa mengimport data</p>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="skip_duplicates" id="skipDuplicates" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" checked>
                            <span class="ml-2 text-sm text-gray-700">Skip data yang sudah ada (berdasarkan nomor kontainer dan periode)</span>
                        </label>
                        <p class="ml-6 mt-1 text-xs text-gray-500">Data duplikat akan diabaikan dan tidak diimport</p>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="update_existing" id="updateExisting" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Update data yang sudah ada</span>
                        </label>
                        <p class="ml-6 mt-1 text-xs text-gray-500">Data yang sudah ada akan diupdate dengan nilai baru dari CSV</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex items-center justify-end space-x-3">
                    <button type="button" id="cancelBtn" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors duration-150">
                        Batal
                    </button>
                    <button type="submit" id="importBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors duration-150" disabled>
                        <span id="importBtnText">Import Data</span>
                        <svg id="importSpinner" class="hidden ml-2 h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Progress Bar -->
                <div id="progressContainer" class="hidden mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Progress Import</span>
                        <span class="text-sm text-gray-500" id="progressText">0%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Import Results (akan dimunculkan via JavaScript) -->
        <div id="importResults" class="import-results hidden">
            <!-- Results akan di-populate via JavaScript -->
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleSampleTable() {
    const table = document.getElementById('sampleTable');
    const toggleText = document.getElementById('sampleToggleText');
    const toggleIcon = document.getElementById('sampleToggleIcon');

    if (table.classList.contains('hidden')) {
        table.classList.remove('hidden');
        toggleText.textContent = 'Sembunyikan contoh format data';
        toggleIcon.classList.add('rotate-180');
    } else {
        table.classList.add('hidden');
        toggleText.textContent = 'Lihat contoh format data';
        toggleIcon.classList.remove('rotate-180');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeFile = document.getElementById('removeFile');
    const importBtn = document.getElementById('importBtn');
    const importForm = document.getElementById('importForm');
    const progressContainer = document.getElementById('progressContainer');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    const importBtnText = document.getElementById('importBtnText');
    const importSpinner = document.getElementById('importSpinner');
    const validateOnly = document.getElementById('validateOnly');
    const skipDuplicates = document.getElementById('skipDuplicates');
    const updateExisting = document.getElementById('updateExisting');
    const validateOnlyWarning = document.getElementById('validateOnlyWarning');

    // Show/hide warning when validate_only checkbox changes
    validateOnly.addEventListener('change', function() {
        if (this.checked) {
            validateOnlyWarning.classList.remove('hidden');
            // Update import button text
            if (importBtn.querySelector('#importBtnText')) {
                importBtn.querySelector('#importBtnText').textContent = 'Validasi Data';
            }
        } else {
            validateOnlyWarning.classList.add('hidden');
            // Reset import button text
            if (importBtn.querySelector('#importBtnText')) {
                importBtn.querySelector('#importBtnText').textContent = 'Import Data';
            }
        }
    });

    // Drag and drop functionality
    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelect(files[0]);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    removeFile.addEventListener('click', () => {
        fileInput.value = '';
        fileInfo.classList.add('hidden');
        importBtn.disabled = true;
        dropZone.style.display = 'block';
    });

    // Handle mutual exclusivity of skip_duplicates and update_existing
    skipDuplicates.addEventListener('change', function() {
        if (this.checked) {
            updateExisting.checked = false;
        }
    });

    updateExisting.addEventListener('change', function() {
        if (this.checked) {
            skipDuplicates.checked = false;
        }
    });

    function handleFileSelect(file) {
        // Validate file type
        const allowedTypes = ['text/csv', 'text/plain'];
        const allowedExtensions = ['.csv', '.txt'];

        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
            alert('Format file tidak didukung. Silakan pilih file CSV (.csv) atau Text (.txt)');
            return;
        }

        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 10MB.');
            return;
        }

        // Show file info
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileInfo.classList.remove('hidden');
        dropZone.style.display = 'none';
        importBtn.disabled = false;

        // Set the file to input (for form submission)
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form submission with progress
    importForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!fileInput.files.length) {
            alert('Silakan pilih file terlebih dahulu');
            return;
        }

        // Show loading state
        importBtn.disabled = true;
        importBtnText.textContent = validateOnly.checked ? 'Memvalidasi...' : 'Mengimport...';
        importSpinner.classList.remove('hidden');
        progressContainer.classList.remove('hidden');

        // Create FormData
        const formData = new FormData(this);

        // Create XMLHttpRequest for progress tracking
        const xhr = new XMLHttpRequest();

        // Progress event
        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressFill.style.width = percentComplete + '%';
                progressText.textContent = Math.round(percentComplete) + '%';
            }
        });

        // Success event
        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    handleImportResponse(response);
                } catch (e) {
                    // If not JSON, assume it's a redirect or HTML response
                    window.location.reload();
                }
            } else {
                handleImportError('Server error: ' + xhr.status);
            }
        });

        // Error event
        xhr.addEventListener('error', function() {
            handleImportError('Network error occurred');
        });

        // Send request
        xhr.open('POST', this.action);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        xhr.send(formData);
    });

    function handleImportResponse(response) {
        // Reset form state
        importBtn.disabled = false;
        importBtnText.textContent = 'Import Data';
        importSpinner.classList.add('hidden');
        progressContainer.classList.add('hidden');

        // Show results
        displayImportResults(response);

        // Scroll to results
        document.getElementById('importResults').scrollIntoView({ behavior: 'smooth' });
    }

    function handleImportError(message) {
        // Reset form state
        importBtn.disabled = false;
        importBtnText.textContent = 'Import Data';
        importSpinner.classList.add('hidden');
        progressContainer.classList.add('hidden');

        alert('Error: ' + message);
    }

    function displayImportResults(response) {
        const resultsContainer = document.getElementById('importResults');

        let html = '<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">';
        html += '<h2 class="text-lg font-semibold text-gray-900 mb-4">Hasil Import</h2>';

        if (response.success) {
            html += '<div class="result-card result-success">';
            html += '<div class="flex items-center mb-2">';
            html += '<svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">';
            html += '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>';
            html += '</svg>';
            html += '<h3 class="font-medium text-green-800">Import Berhasil</h3>';
            html += '</div>';

            if (response.validate_only) {
                html += '<p class="text-green-700 mb-2">Validasi selesai. Data valid dan siap untuk diimport.</p>';
            } else {
                html += '<p class="text-green-700 mb-2">Data berhasil diimport.</p>';
            }

            html += '<div class="text-sm text-green-600">';
            if (response.imported_count !== undefined) {
                html += '<p>• Data berhasil diimport: ' + response.imported_count + ' baris</p>';
            }
            if (response.updated_count !== undefined) {
                html += '<p>• Data berhasil diupdate: ' + response.updated_count + ' baris</p>';
            }
            if (response.skipped_count !== undefined) {
                html += '<p>• Data diskip (duplikat): ' + response.skipped_count + ' baris</p>';
            }
            if (response.total_processed !== undefined) {
                html += '<p>• Total data diproses: ' + response.total_processed + ' baris</p>';
            }
            html += '</div>';
            html += '</div>';
        }

        if (response.errors && response.errors.length > 0) {
            html += '<div class="result-card result-error">';
            html += '<div class="flex items-center mb-2">';
            html += '<svg class="h-5 w-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">';
            html += '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>';
            html += '</svg>';
            html += '<h3 class="font-medium text-red-800">Errors</h3>';
            html += '</div>';
            html += '<div class="text-sm text-red-700">';
            response.errors.forEach(error => {
                html += '<div class="validation-error">';
                html += '<p class="font-medium">Baris ' + (error.row || 'Unknown') + ':</p>';
                if (Array.isArray(error.errors)) {
                    html += '<ul>';
                    error.errors.forEach(err => {
                        html += '<li>' + err + '</li>';
                    });
                    html += '</ul>';
                } else {
                    html += '<p>' + error.message + '</p>';
                }
                html += '</div>';
            });
            html += '</div>';
            html += '</div>';
        }

        if (response.warnings && response.warnings.length > 0) {
            html += '<div class="result-card result-warning">';
            html += '<div class="flex items-center mb-2">';
            html += '<svg class="h-5 w-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">';
            html += '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>';
            html += '</svg>';
            html += '<h3 class="font-medium text-yellow-800">Warnings</h3>';
            html += '</div>';
            html += '<div class="text-sm text-yellow-700">';
            response.warnings.forEach(warning => {
                html += '<p>• ' + warning + '</p>';
            });
            html += '</div>';
            html += '</div>';
        }

        // Add action buttons
        html += '<div class="mt-6 flex items-center justify-end space-x-3">';
        if (!response.validate_only && response.success) {
            html += '<a href="{{ route("daftar-tagihan-kontainer-sewa.index") }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-150">Lihat Data</a>';
        }
        html += '<button type="button" onclick="location.reload()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-150">Import Lagi</button>';
        html += '</div>';

        html += '</div>';

        resultsContainer.innerHTML = html;
        resultsContainer.classList.remove('hidden');
    }

    // Template dropdown functionality
    document.getElementById('templateDropdown').addEventListener('click', function(e) {
        e.preventDefault();
        const menu = document.getElementById('templateDropdownMenu');
        menu.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('templateDropdown');
        const menu = document.getElementById('templateDropdownMenu');

        if (!dropdown.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });
});
</script>
@endpush
@endsection
