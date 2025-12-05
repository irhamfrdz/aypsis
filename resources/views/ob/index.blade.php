@extends('layouts.app')

@section('title', 'OB - Pilih Kapal & Voyage')
@section('page_title', 'OB - Pilih Kapal & Voyage')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 max-w-6xl mx-auto">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900 mb-2">OB - Ocean Bunker</h1>
            <p class="text-sm text-gray-600">Pilih kapal dan voyage untuk melanjutkan ke modul OB</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Ship and Voyage Selection Form -->
        <form id="selectionForm" action="{{ route('ob.select') }}" method="POST">
            @csrf
            
            <!-- Ship Selection -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-200">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">1. Pilih Kapal</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label for="ship_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Kapal <span class="text-red-500">*</span>
                        </label>
                        <select name="ship_id" id="ship_id" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($ships as $ship)
                                <option value="{{ $ship->id }}" 
                                        data-nickname="{{ $ship->nickname }}"
                                        data-pelayaran="{{ $ship->pelayaran }}"
                                        {{ request('ship_id') == $ship->id ? 'selected' : '' }}>
                                    {{ $ship->nama_kapal }} @if($ship->nickname) ({{ $ship->nickname }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($selectedShip)
                        <div class="bg-white rounded-lg p-3 border border-blue-300">
                            <h4 class="font-medium text-blue-900 mb-2">Informasi Kapal</h4>
                            <div class="text-sm text-gray-700 space-y-1">
                                <p><span class="font-medium">Nama:</span> {{ $selectedShip->nama_kapal }}</p>
                                <p><span class="font-medium">Kode:</span> {{ $selectedShip->kode_kapal }}</p>
                                @if($selectedShip->nickname)
                                    <p><span class="font-medium">Nickname:</span> {{ $selectedShip->nickname }}</p>
                                @endif
                                @if($selectedShip->pelayaran)
                                    <p><span class="font-medium">Pelayaran:</span> {{ $selectedShip->pelayaran }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Voyage Selection -->
            <div class="bg-orange-50 rounded-lg p-4 mb-6 border border-orange-200">
                <h3 class="text-lg font-semibold text-orange-900 mb-4">2. Pilih Voyage</h3>
                
                <!-- Filters for voyage -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" 
                               value="{{ request('start_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" 
                               value="{{ request('end_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="filterVoyages()" 
                                class="w-full px-4 py-2 bg-orange-600 text-white font-medium rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            Filter Voyage
                        </button>
                    </div>
                </div>

                <!-- Voyage List -->
                <div id="voyageContainer">
                    @if($voyages->count() > 0)
                        <div class="space-y-3">
                            @foreach($voyages as $voyage)
                                <div class="bg-white rounded-lg p-4 border border-orange-200 hover:border-orange-400 transition-colors voyage-item cursor-pointer" 
                                     onclick="selectVoyage({{ $voyage->id }}, '{{ $voyage->voyage }}', '{{ $voyage->nama_kapal }}')">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-4">
                                                <div>
                                                    <input type="radio" name="voyage_id" value="{{ $voyage->id }}" 
                                                           id="voyage_{{ $voyage->id }}"
                                                           {{ request('voyage_id') == $voyage->id ? 'checked' : '' }}
                                                           class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="text-lg font-semibold text-gray-900">Voyage: {{ $voyage->voyage }}</h4>
                                                    <p class="text-sm text-gray-600">{{ $voyage->nama_kapal }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                                <div>
                                                    <span class="font-medium text-gray-700">Tanggal Sandar:</span>
                                                    <p class="text-gray-600">{{ $voyage->tanggal_sandar ? \Carbon\Carbon::parse($voyage->tanggal_sandar)->format('d/m/Y') : '-' }}</p>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-700">Tanggal Berangkat:</span>
                                                    <p class="text-gray-600">{{ $voyage->tanggal_berangkat ? \Carbon\Carbon::parse($voyage->tanggal_berangkat)->format('d/m/Y') : '-' }}</p>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-700">Tujuan Asal:</span>
                                                    <p class="text-gray-600">{{ $voyage->tujuan_asal ?: '-' }}</p>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-700">Tujuan:</span>
                                                    <p class="text-gray-600">{{ $voyage->tujuan_tujuan ?: '-' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $voyage->status === 'aktif' ? 'bg-green-100 text-green-800' : 
                                                   ($voyage->status === 'selesai' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($voyage->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $voyages->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada voyage ditemukan</h3>
                            <p class="mt-1 text-sm text-gray-500">Silakan pilih kapal terlebih dahulu atau sesuaikan filter tanggal.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <div>
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Info:</span> Setelah memilih kapal dan voyage, Anda akan diarahkan ke dashboard OB untuk kapal dan voyage tersebut.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="resetSelection()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Reset
                    </button>
                    <button type="submit" id="submitBtn" disabled
                            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        Lanjutkan ke Dashboard OB
                    </button>
                </div>
            </div>
        </form>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shipSelect = document.getElementById('ship_id');
    const submitBtn = document.getElementById('submitBtn');
    
    // Check initial state
    updateSubmitButton();
    
    // Ship selection change
    shipSelect.addEventListener('change', function() {
        if (this.value) {
            loadVoyagesForShip(this.value);
        } else {
            clearVoyages();
        }
        updateSubmitButton();
    });
    
    // Voyage selection change  
    document.addEventListener('change', function(e) {
        if (e.target.name === 'voyage_id') {
            updateSubmitButton();
        }
    });
});

function selectVoyage(voyageId, voyageName, shipName) {
    const radio = document.getElementById('voyage_' + voyageId);
    if (radio) {
        radio.checked = true;
        updateSubmitButton();
    }
}

function updateSubmitButton() {
    const shipSelect = document.getElementById('ship_id');
    const voyageSelected = document.querySelector('input[name="voyage_id"]:checked');
    const submitBtn = document.getElementById('submitBtn');
    
    if (shipSelect.value && voyageSelected) {
        submitBtn.disabled = false;
    } else {
        submitBtn.disabled = true;
    }
}

function loadVoyagesForShip(shipId) {
    fetch(`{{ route('ob.get-voyages') }}?ship_id=${shipId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateVoyageList(data.voyages);
            }
        })
        .catch(error => {
            console.error('Error loading voyages:', error);
        });
}

function updateVoyageList(voyages) {
    const container = document.getElementById('voyageContainer');
    
    if (voyages.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada voyage untuk kapal ini</h3>
                <p class="mt-1 text-sm text-gray-500">Silakan pilih kapal lain.</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="space-y-3">';
    
    voyages.forEach(voyage => {
        const statusClass = voyage.status === 'aktif' ? 'bg-green-100 text-green-800' : 
                           (voyage.status === 'selesai' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800');
        
        html += `
            <div class="bg-white rounded-lg p-4 border border-orange-200 hover:border-orange-400 transition-colors voyage-item cursor-pointer" 
                 onclick="selectVoyage(${voyage.id}, '${voyage.voyage}', '${voyage.nama_kapal || ''}')">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4">
                            <div>
                                <input type="radio" name="voyage_id" value="${voyage.id}" 
                                       id="voyage_${voyage.id}"
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">Voyage: ${voyage.voyage}</h4>
                            </div>
                        </div>
                        
                        <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Tanggal Sandar:</span>
                                <p class="text-gray-600">${voyage.tanggal_sandar || '-'}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Tanggal Berangkat:</span>
                                <p class="text-gray-600">${voyage.tanggal_berangkat || '-'}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Tujuan Asal:</span>
                                <p class="text-gray-600">${voyage.tujuan_asal || '-'}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Tujuan:</span>
                                <p class="text-gray-600">${voyage.tujuan_tujuan || '-'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                            ${voyage.status ? voyage.status.charAt(0).toUpperCase() + voyage.status.slice(1) : 'Unknown'}
                        </span>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function clearVoyages() {
    const container = document.getElementById('voyageContainer');
    container.innerHTML = `
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Silakan pilih kapal terlebih dahulu</h3>
            <p class="mt-1 text-sm text-gray-500">Voyage akan ditampilkan setelah Anda memilih kapal.</p>
        </div>
    `;
}

function filterVoyages() {
    const form = new FormData();
    form.append('ship_id', document.getElementById('ship_id').value);
    form.append('start_date', document.getElementById('start_date').value);
    form.append('end_date', document.getElementById('end_date').value);
    
    const params = new URLSearchParams();
    for (let [key, value] of form) {
        if (value) {
            params.append(key, value);
        }
    }
    
    window.location.href = `{{ route('ob.index') }}?${params.toString()}`;
}

function resetSelection() {
    window.location.href = '{{ route('ob.index') }}';
}
</script>

@endsection