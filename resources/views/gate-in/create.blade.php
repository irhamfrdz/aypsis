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
                    
                    <!-- Terminal -->
                    <div class="space-y-2">
                        <label for="terminal_id" class="block text-sm font-medium text-gray-700">
                            Terminal <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="terminal_id" id="terminal_id" class="block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out text-gray-900" required>
                                <option value="">Pilih Terminal</option>
                                @foreach($terminals as $terminal)
                                    <option value="{{ $terminal->id }}" {{ old('terminal_id') == $terminal->id ? 'selected' : '' }}>
                                        {{ $terminal->nama_terminal }}{{ $terminal->kode_terminal ? ' - ' . $terminal->kode_terminal : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        @error('terminal_id')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>                    <!-- Kapal -->
                    <div class="space-y-2">
                        <label for="kapal_id" class="block text-sm font-medium text-gray-700">
                            Kapal <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="kapal_id" id="kapal_id" class="block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out text-gray-900" required>
                                <option value="">Pilih Kapal</option>
                                @foreach($kapals as $kapal)
                                    <option value="{{ $kapal->id }}" {{ old('kapal_id') == $kapal->id ? 'selected' : '' }}>
                                        {{ $kapal->nama_kapal }}{{ $kapal->kode_kapal ? ' - ' . $kapal->kode_kapal : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        @error('kapal_id')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Service -->
                    <div class="space-y-2">
                        <label for="service_id" class="block text-sm font-medium text-gray-700">
                            Service <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="service_id" id="service_id" class="block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out text-gray-900" required>
                                <option value="">Pilih Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->nama_service }} - {{ $service->kode_service }}
                                        @if($service->tarif) ({{ $service->formatted_tarif }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        @error('service_id')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Kontainer -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Kontainer <span class="text-red-500">*</span>
                        </label>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <!-- Loading -->
                            <div id="kontainer-loading" class="hidden text-center py-8">
                                <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm text-gray-500">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memuat kontainer...
                                </div>
                            </div>
                            
                            <!-- Kontainer List -->
                            <div id="kontainer-list">
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <p class="text-sm">Pilih terminal, kapal, dan service terlebih dahulu</p>
                                </div>
                            </div>
                        </div>
                        @error('kontainer_ids')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="space-y-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">
                            Keterangan
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="4" 
                                  class="block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out resize-none"
                                  placeholder="Masukkan keterangan tambahan (opsional)...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
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
                        <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg transition duration-150 ease-in-out">
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
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load kontainers when all required fields are selected
    $('#terminal_id, #kapal_id, #service_id').change(function() {
        loadKontainers();
    });
    
    function loadKontainers() {
        const terminalId = $('#terminal_id').val();
        const kapalId = $('#kapal_id').val();
        const serviceId = $('#service_id').val();
        
        if (terminalId && kapalId && serviceId) {
            const loading = $('#kontainer-loading');
            const list = $('#kontainer-list');
            
            loading.removeClass('hidden');
            list.empty();
            
            // AJAX call to get kontainers
            $.ajax({
                url: '/gate-in/get-kontainers',
                method: 'GET',
                data: {
                    terminal_id: terminalId,
                    kapal_id: kapalId,
                    service_id: serviceId
                },
                success: function(data) {
                    loading.addClass('hidden');
                    
                    if (data.length === 0) {
                        list.html(`
                            <div class="text-center py-8">
                                <svg class="w-16 h-16 mx-auto mb-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.924-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada kontainer tersedia</h3>
                                <p class="text-sm text-gray-500">Belum ada kontainer yang sudah checkpoint supir untuk kombinasi yang dipilih.</p>
                            </div>
                        `);
                    } else {
                        let html = '<div class="space-y-3">';
                        html += '<div class="mb-4"><p class="text-sm font-medium text-gray-700">Pilih kontainer (bisa lebih dari 1):</p></div>';
                        
                        data.forEach(function(kontainer, index) {
                            html += `
                                <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                                    <div class="flex-shrink-0">
                                        <input type="checkbox" name="kontainer_ids[]" value="${kontainer.id}" id="kontainer_${index}" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    </div>
                                    <label for="kontainer_${index}" class="ml-3 flex-1 cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    ${kontainer.nomor_seri_gabungan || kontainer.nomor_kontainer}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    ${kontainer.ukuran || '-'} | ${kontainer.tipe_kontainer || '-'}
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Siap Gate In
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            `;
                        });
                        
                        html += '</div>';
                        list.html(html);
                    }
                },
                error: function() {
                    loading.addClass('hidden');
                    list.html(`
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto mb-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Terjadi kesalahan</h3>
                            <p class="text-sm text-gray-500">Gagal memuat data kontainer. Silakan coba lagi.</p>
                        </div>
                    `);
                }
            });
        }
    }
});

function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form? Semua pilihan akan dihapus.')) {
        document.getElementById('gate-in-form').reset();
        $('#kontainer-list').html(`
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-sm">Pilih terminal, kapal, dan service terlebih dahulu</p>
            </div>
        `);
    }
}
</script>
@endpush
