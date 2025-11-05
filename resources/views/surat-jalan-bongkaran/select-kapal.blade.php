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
                        <input type="text" name="no_voyage" id="no_voyage" required
                               value="{{ request('no_voyage') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="-PILIH-">
                    </div>
                </div>

                <!-- No BL -->
                <div>
                    <label for="no_bl" class="block text-sm font-medium text-gray-700 mb-2">No BL</label>
                    <input type="text" name="no_bl" id="no_bl"
                           value="{{ request('no_bl') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="-PILIH-">
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
    // Auto-fill placeholder suggestions
    const kapalSelect = document.getElementById('kapal_id');
    const voyageInput = document.getElementById('no_voyage');
    const blInput = document.getElementById('no_bl');
    
    kapalSelect.addEventListener('change', function() {
        if (this.value) {
            // Auto suggest voyage format based on current date
            const today = new Date();
            const year = today.getFullYear().toString().substr(-2);
            const month = String(today.getMonth() + 1).padStart(2, '0');
            
            if (!voyageInput.value || voyageInput.value === '-PILIH-') {
                voyageInput.value = `${year}${month}001`;
                voyageInput.placeholder = `Contoh: ${year}${month}001`;
            }
            
            if (!blInput.value || blInput.value === '-PILIH-') {
                blInput.placeholder = `Contoh: BL${year}${month}001`;
            }
        } else {
            voyageInput.placeholder = '-PILIH-';
            blInput.placeholder = '-PILIH-';
        }
    });
    
    // Format input suggestions
    voyageInput.addEventListener('focus', function() {
        if (this.value === '-PILIH-') {
            this.value = '';
        }
    });
    
    blInput.addEventListener('focus', function() {
        if (this.value === '-PILIH-') {
            this.value = '';
        }
    });
});
</script>
@endpush