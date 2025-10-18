@extends('layouts.app')

@section('title', 'Buat Gate In Baru')
@section('page_title', 'Buat Gate In Baru')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Notifikasi Success -->
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

        <!-- Notifikasi Error -->
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

        <!-- Notifikasi Validation Errors -->
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
                            <p class="text-blue-100 text-sm">Buat entri gate in baru untuk kontainer</p>
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

        <!-- Form -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Gate In</h2>
                <p class="text-sm text-gray-600 mt-1">Lengkapi form berikut untuk membuat gate in baru</p>
            </div>

            <div class="p-6">
                <form action="{{ route('gate-in.store') }}" method="POST" id="gate-in-form" class="space-y-6">
                    @csrf

                    <!-- Nomor Gate In -->
                    <div class="space-y-2">
                        <label for="nomor_gate_in" class="block text-sm font-medium text-gray-700">
                            Nomor Gate In <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomor_gate_in" id="nomor_gate_in"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Masukkan nomor gate in" value="{{ old('nomor_gate_in') }}" required>
                        @error('nomor_gate_in')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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

                    <!-- Kontainer -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Kontainer <span class="text-red-500">*</span>
                        </label>
                        <p class="text-sm text-gray-600">Pilih nomor kontainer dari surat jalan yang sudah checkpoint supir (bisa pilih lebih dari 1)</p>
                        <div id="kontainer-container" class="border border-gray-300 rounded-lg p-4 bg-gray-50 min-h-[100px]">
                            <p class="text-sm text-gray-500 text-center py-4">Loading kontainer...</p>
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
                                  placeholder="Masukkan keterangan tambahan (opsional)...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <button type="button" onclick="resetForm()" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Form
                        </button>
                        <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed" onclick="return validateForm(event)" id="submit-button">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Gate In
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
function loadKontainerOptions() {
        const kontainerContainer = $('#kontainer-container');

        // Show loading
        kontainerContainer.html('<p class="text-sm text-gray-500 text-center py-4">Loading...</p>');

        console.log('Loading all available kontainers from surat jalan checkpoint...');

        $.ajax({
            url: '{{ route("gate-in.get-kontainers-surat-jalan") }}',
            method: 'GET',
            timeout: 30000, // 30 seconds timeout
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                console.log('Kontainers loaded:', data.length, 'items');
                console.log('Sample data:', data.slice(0, 2)); // Log sample data for debugging

                if (data.length === 0) {
                    kontainerContainer.html('<p class="text-sm text-gray-500 text-center py-4">Tidak ada kontainer dari surat jalan yang sudah checkpoint supir</p>');
                } else {
                    let html = '<div class="space-y-3">';

                    data.forEach(function(kontainer, index) {
                        html += `
                            <div class="flex items-center p-3 border border-gray-200 rounded-lg bg-white hover:bg-gray-50">
                                <input type="checkbox" name="kontainer_ids[]" value="${kontainer.id}" id="kontainer_${index}"
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mr-3">
                                <label for="kontainer_${index}" class="flex-1 cursor-pointer">
                                    <div class="font-medium text-gray-900">${kontainer.nomor_kontainer}</div>
                                    <div class="text-sm text-gray-600">
                                        Surat Jalan: ${kontainer.no_surat_jalan} |
                                        Supir: ${kontainer.supir_nama} |
                                        Size: ${kontainer.size}ft
                                    </div>
                                </label>
                            </div>
                        `;
                    });

                    html += '</div>';
                    kontainerContainer.html(html);
                    console.log('HTML generated and inserted into container');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error Details:');
                console.log('Status:', xhr.status);
                console.log('Status Text:', xhr.statusText);
                console.log('Response Text:', xhr.responseText);
                console.log('Error:', error);
                console.log('Ready State:', xhr.readyState);

                let errorMsg = '';
                let detailedError = '';

                if (xhr.status === 0) {
                    if (status === 'timeout') {
                        errorMsg = 'Request timeout. Server membutuhkan waktu terlalu lama untuk merespons.';
                    } else {
                        errorMsg = 'Koneksi terputus. Periksa koneksi internet Anda.';
                    }
                } else if (xhr.status === 404) {
                    errorMsg = 'Halaman tidak ditemukan (404).';
                } else if (xhr.status === 500) {
                    errorMsg = 'Terjadi kesalahan server (500).';
                } else if (xhr.status === 403) {
                    errorMsg = 'Akses ditolak. Anda tidak memiliki permission untuk aksi ini.';
                } else if (xhr.status === 401) {
                    errorMsg = 'Sesi Anda telah berakhir. Silakan login ulang.';
                } else {
                    errorMsg = `Error ${xhr.status}: ${xhr.statusText}`;
                }

                if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            detailedError = response.message;
                        } else if (response.error) {
                            detailedError = response.error;
                        }
                    } catch (e) {
                        // Response is not JSON, try to extract useful info
                        if (xhr.responseText.includes('SQLSTATE')) {
                            detailedError = 'Database error. Silakan hubungi administrator.';
                        } else if (xhr.responseText.includes('Unauthorized')) {
                            detailedError = 'Anda tidak memiliki akses untuk melihat data ini.';
                        }
                    }
                }

                const fullErrorMsg = detailedError ? `${errorMsg} ${detailedError}` : errorMsg;

                kontainerContainer.html(`
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Gagal Memuat Data</h3>
                        <p class="mt-1 text-sm text-red-600">${fullErrorMsg}</p>
                        <div class="mt-4">
                            <button type="button" onclick="loadKontainerOptions()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Coba Lagi
                            </button>
                        </div>
                    </div>
                `);

                // Show alert notification
                showAlert('error', `Gagal memuat data kontainer: ${errorMsg}`);
            }
        });
}

$(document).ready(function() {
    // Load all available kontainers immediately when page loads
    loadKontainerOptions();

    // Check for session messages and show alerts
    @if(session('success'))
        showAlert('success', '{{ session('success') }}');
    @endif

    @if(session('error'))
        showAlert('error', '{{ session('error') }}');
    @endif

    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle form submission with additional validation
    $('#gate-in-form').on('submit', function(e) {
        console.log('Form submitted');

        // Double check CSRF token
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (!csrfToken) {
            e.preventDefault();
            showAlert('error', 'Sesi telah berakhir. Silakan refresh halaman dan coba lagi.');
            return false;
        }

        // Check if any kontainer is selected
        const selectedKontainers = $('input[name="kontainer_ids[]"]:checked').length;
        if (selectedKontainers === 0) {
            e.preventDefault();
            showAlert('error', 'Pilih minimal satu kontainer sebelum menyimpan!');
            return false;
        }

        console.log(`Submitting form with ${selectedKontainers} kontainers selected`);
    });
});

function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form?')) {
        document.getElementById('gate-in-form').reset();
        // Reload kontainer options after reset
        loadKontainerOptions();
    }
}

function validateForm(event) {
    console.log('Validating form before submit...');

    // Disable submit button to prevent double submit
    const submitButton = event.target;
    const originalText = submitButton.innerHTML;

    // Check if required fields are filled
    const nomorGateIn = document.getElementById('nomor_gate_in').value.trim();
    if (!nomorGateIn) {
        showAlert('error', 'Nomor Gate In harus diisi!');
        event.preventDefault();
        return false;
    }

    // Check terminal selection
    const terminalId = document.getElementById('terminal_id').value;
    if (!terminalId) {
        showAlert('error', 'Terminal harus dipilih!');
        event.preventDefault();
        return false;
    }

    // Check kapal selection
    const kapalId = document.getElementById('kapal_id').value;
    if (!kapalId) {
        showAlert('error', 'Kapal harus dipilih!');
        event.preventDefault();
        return false;
    }

    // Check if at least one kontainer is selected
    const selectedKontainers = document.querySelectorAll('input[name="kontainer_ids[]"]:checked');
    console.log('Selected kontainers:', selectedKontainers.length);

    if (selectedKontainers.length === 0) {
        showAlert('error', 'Pilih minimal satu kontainer!');
        event.preventDefault();
        return false;
    }

    // Show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Menyimpan...
    `;

    // Re-enable button after 10 seconds as fallback
    setTimeout(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    }, 10000);

    console.log('Form validation passed. Submitting...');
    return true;
}

// Function to show alert messages
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `custom-alert fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg max-w-md ${
        type === 'error' ? 'bg-red-100 border-l-4 border-red-500 text-red-700' :
        type === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' :
        'bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700'
    }`;

    alertDiv.innerHTML = `
        <div class="flex">
            <div class="flex-shrink-0">
                ${type === 'error' ?
                    '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>' :
                    '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                }
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex rounded-md p-1.5 hover:bg-opacity-20 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                        <span class="sr-only">Tutup</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(alertDiv);

    // Auto remove alert after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush
