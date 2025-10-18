@extends('layouts.app')

@section('title', 'Buat Gate In Baru')
@section('page_title', 'Buat Gate In Baru')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Create Gate In</h1>
                            <p class="text-blue-100 text-sm">Buat entri gate in baru untuk kontainer dari checkpoint supir</p>
                        </div>
                    </div>
                    <a href="{{ route('gate-in.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium">Terjadi Kesalahan!</h3>
                        <div class="mt-2 text-sm">
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium">Periksa Input Berikut:</h3>
                        <div class="mt-2 text-sm">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Gate In</h2>
                <p class="text-sm text-gray-600 mt-1">Lengkapi form berikut untuk membuat gate in baru</p>
            </div>

            <div class="p-6">
                <form action="{{ route('gate-in.store') }}" method="POST" id="gate-in-form" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nomor Gate In -->
                        <div class="space-y-2">
                            <label for="nomor_gate_in" class="block text-sm font-medium text-gray-700">
                                Nomor Gate In <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nomor_gate_in" id="nomor_gate_in"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Masukkan nomor gate in" value="{{ old('nomor_gate_in') }}" required maxlength="20">
                            @error('nomor_gate_in')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500">Maksimal 20 karakter</p>
                        </div>

                        <!-- Terminal -->
                        <div class="space-y-2">
                            <label for="terminal_id" class="block text-sm font-medium text-gray-700">
                                Terminal <span class="text-red-500">*</span>
                            </label>
                            <select name="terminal_id" id="terminal_id" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Terminal</option>
                                @foreach($terminals as $terminal)
                                    <option value="{{ $terminal->id }}" {{ old('terminal_id') == $terminal->id ? 'selected' : '' }}>
                                        {{ $terminal->nama_terminal }}
                                    </option>
                                @endforeach
                            </select>
                            @error('terminal_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Kapal -->
                    <div class="space-y-2">
                        <label for="kapal_id" class="block text-sm font-medium text-gray-700">
                            Kapal <span class="text-red-500">*</span>
                        </label>
                        <select name="kapal_id" id="kapal_id" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Pilih Kapal</option>
                            @foreach($kapals as $kapal)
                                <option value="{{ $kapal->id }}" {{ old('kapal_id') == $kapal->id ? 'selected' : '' }}>
                                    {{ $kapal->nama_kapal }}
                                </option>
                            @endforeach
                        </select>
                        @error('kapal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kontainer Section -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Kontainer dari Checkpoint Supir <span class="text-red-500">*</span>
                        </label>
                        <p class="text-sm text-gray-600">Pilih kontainer yang sudah melalui checkpoint supir (dapat memilih lebih dari satu)</p>

                        <!-- Loading State -->
                        <div id="loading-kontainer" class="border border-gray-300 rounded-lg p-6 bg-gray-50">
                            <div class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Memuat data kontainer...</span>
                            </div>
                        </div>

                        <!-- Kontainer Container -->
                        <div id="kontainer-container" class="hidden border border-gray-300 rounded-lg bg-gray-50 min-h-[100px]">
                            <!-- Data kontainer akan dimuat di sini -->
                        </div>

                        <!-- Error State -->
                        <div id="error-kontainer" class="hidden border border-red-300 rounded-lg p-6 bg-red-50">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="text-sm font-medium text-red-800 mb-2">Gagal Memuat Data</h3>
                                <p class="text-sm text-red-600 mb-4" id="error-message"></p>
                                <button type="button" onclick="loadKontainerData()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Coba Lagi
                                </button>
                            </div>
                        </div>

                        @error('kontainer_ids')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="space-y-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">
                            Keterangan
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                  placeholder="Masukkan keterangan tambahan (opsional)..." maxlength="500">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500">Maksimal 500 karakter</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <button type="button" onclick="resetForm()" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Form
                        </button>
                        <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed" id="submit-button">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span id="submit-text">Simpan Gate In</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// State management
let isLoading = false;
let kontainerData = [];

// DOM Ready
$(document).ready(function() {
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load kontainer data on page load
    loadKontainerData();

    // Form validation on submit
    $('#gate-in-form').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        const submitBtn = $('#submit-button');
        const submitText = $('#submit-text');

        submitBtn.prop('disabled', true);
        submitText.html('<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Menyimpan...');

        // Re-enable after timeout as fallback
        setTimeout(() => {
            submitBtn.prop('disabled', false);
            submitText.text('Simpan Gate In');
        }, 15000);
    });
});

// Load kontainer data from checkpoint supir
function loadKontainerData() {
    if (isLoading) return;

    isLoading = true;

    // Show loading state
    $('#loading-kontainer').removeClass('hidden');
    $('#kontainer-container').addClass('hidden');
    $('#error-kontainer').addClass('hidden');

    console.log('Loading kontainer data from checkpoint supir...');

    $.ajax({
        url: '{{ route("gate-in.get-kontainers-surat-jalan") }}',
        method: 'GET',
        timeout: 30000,
        success: function(response) {
            console.log('Kontainer data loaded:', response.length, 'items');

            kontainerData = response;
            renderKontainerData(response);

            // Hide loading, show content
            $('#loading-kontainer').addClass('hidden');
            $('#kontainer-container').removeClass('hidden');

            isLoading = false;
        },
        error: function(xhr, status, error) {
            console.error('Error loading kontainer data:', xhr.status, error);

            let errorMessage = 'Terjadi kesalahan saat memuat data kontainer.';

            if (xhr.status === 0) {
                errorMessage = status === 'timeout' ?
                    'Request timeout. Server membutuhkan waktu terlalu lama.' :
                    'Koneksi terputus. Periksa koneksi internet Anda.';
            } else if (xhr.status === 404) {
                errorMessage = 'Endpoint tidak ditemukan (404).';
            } else if (xhr.status === 500) {
                errorMessage = 'Terjadi kesalahan server (500).';
            } else if (xhr.status === 403) {
                errorMessage = 'Akses ditolak. Anda tidak memiliki permission.';
            }

            // Show error state
            $('#loading-kontainer').addClass('hidden');
            $('#kontainer-container').addClass('hidden');
            $('#error-kontainer').removeClass('hidden');
            $('#error-message').text(errorMessage);

            showAlert('error', errorMessage);
            isLoading = false;
        }
    });
}

// Render kontainer data to UI
function renderKontainerData(data) {
    const container = $('#kontainer-container');

    if (data.length === 0) {
        container.html(`
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Data</h3>
                <p class="mt-1 text-sm text-gray-500">Belum ada kontainer dari checkpoint supir yang tersedia untuk gate in.</p>
                <div class="mt-4">
                    <button type="button" onclick="loadKontainerData()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh Data
                    </button>
                </div>
            </div>
        `);
        return;
    }

    let html = '<div class="p-4"><div class="space-y-3">';

    data.forEach((kontainer, index) => {
        html += `
            <div class="flex items-start p-4 border border-gray-200 rounded-lg bg-white hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="kontainer_ids[]" value="${kontainer.id}" id="kontainer_${index}"
                       class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                <label for="kontainer_${index}" class="ml-3 flex-1 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="font-medium text-gray-900">${kontainer.nomor_kontainer || 'N/A'}</div>
                        <div class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">${kontainer.size || '20'}ft</div>
                    </div>
                    <div class="mt-1 text-sm text-gray-600">
                        <div class="flex flex-wrap gap-4">
                            <span><strong>Surat Jalan:</strong> ${kontainer.no_surat_jalan}</span>
                            <span><strong>Supir:</strong> ${kontainer.supir_nama || 'N/A'}</span>
                            ${kontainer.no_plat ? `<span><strong>Plat:</strong> ${kontainer.no_plat}</span>` : ''}
                        </div>
                        ${kontainer.tujuan_pengiriman ? `<div class="mt-1"><strong>Tujuan:</strong> ${kontainer.tujuan_pengiriman}</div>` : ''}
                    </div>
                </label>
            </div>
        `;
    });

    html += '</div></div>';
    container.html(html);
}

// Form validation
function validateForm() {
    const errors = [];

    // Check nomor gate in
    const nomorGateIn = $('#nomor_gate_in').val().trim();
    if (!nomorGateIn) {
        errors.push('Nomor Gate In harus diisi');
    } else if (nomorGateIn.length > 20) {
        errors.push('Nomor Gate In maksimal 20 karakter');
    }

    // Check terminal
    if (!$('#terminal_id').val()) {
        errors.push('Terminal harus dipilih');
    }

    // Check kapal
    if (!$('#kapal_id').val()) {
        errors.push('Kapal harus dipilih');
    }

    // Check kontainer selection
    const selectedKontainers = $('input[name="kontainer_ids[]"]:checked').length;
    if (selectedKontainers === 0) {
        errors.push('Pilih minimal satu kontainer');
    }

    // Check keterangan length
    const keterangan = $('#keterangan').val();
    if (keterangan.length > 500) {
        errors.push('Keterangan maksimal 500 karakter');
    }

    if (errors.length > 0) {
        showAlert('error', errors.join(', '));
        return false;
    }

    return true;
}

// Reset form
function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form? Semua data yang sudah diisi akan hilang.')) {
        $('#gate-in-form')[0].reset();
        loadKontainerData();
        showAlert('info', 'Form telah direset');
    }
}

// Show alert messages
function showAlert(type, message) {
    // Remove existing alerts
    $('.custom-alert').remove();

    const alertClass = {
        'success': 'bg-green-100 border-green-500 text-green-700',
        'error': 'bg-red-100 border-red-500 text-red-700',
        'warning': 'bg-yellow-100 border-yellow-500 text-yellow-700',
        'info': 'bg-blue-100 border-blue-500 text-blue-700'
    };

    const iconSvg = {
        'success': '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
        'error': '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
        'warning': '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>',
        'info': '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>'
    };

    const alertDiv = $(`
        <div class="custom-alert fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg max-w-md border-l-4 ${alertClass[type] || alertClass.info}">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        ${iconSvg[type] || iconSvg.info}
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button type="button" class="inline-flex rounded-md p-1.5 hover:bg-opacity-20 focus:outline-none" onclick="$(this).closest('.custom-alert').remove()">
                        <span class="sr-only">Tutup</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `);

    $('body').append(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
        alertDiv.fadeOut(300, function() {
            $(this).remove();
        });
    }, 5000);
}
</script>
@endpush
