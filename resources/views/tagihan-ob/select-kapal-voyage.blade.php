@extends('layouts.app')

@section('title', 'Pilih Kapal dan Voyage - Tagihan OB')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
            <h1 class="text-xl font-semibold flex items-center">
                <i class="fas fa-ship mr-3"></i>
                Pilih Kapal dan Voyage - Tagihan OB
            </h1>
            <p class="text-blue-100 mt-1">Silahkan pilih kapal dan voyage terlebih dahulu untuk melihat data tagihan OB</p>
        </div>

        <div class="p-6">
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6" role="alert">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('tagihan-ob.index') }}" method="GET" id="selectionForm">
                <div class="space-y-6">
                    <!-- Kapal Selection -->
                    <div>
                        <label for="kapal" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Kapal <span class="text-red-500">*</span>
                        </label>
                        <select name="kapal" id="kapal" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required onchange="updateVoyageOptions()">
                            <option value="">-- Pilih Kapal --</option>
                            @foreach ($voyageByKapal as $namaKapal => $voyages)
                                <option value="{{ $namaKapal }}">{{ $namaKapal }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Voyage Selection -->
                    <div>
                        <label for="voyage" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Voyage <span class="text-red-500">*</span>
                        </label>
                        <select name="voyage" id="voyage" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required disabled>
                            <option value="">-- Pilih Voyage --</option>
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Pilih kapal terlebih dahulu untuk melihat voyage yang tersedia</p>
                    </div>

                    <!-- Summary Info -->
                    <div id="summaryInfo" class="hidden bg-gray-50 border border-gray-200 rounded-md p-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Informasi Dipilih:</h4>
                        <div class="text-sm text-gray-600">
                            <p><strong>Kapal:</strong> <span id="selectedKapalText">-</span></p>
                            <p><strong>Voyage:</strong> <span id="selectedVoyageText">-</span></p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                                id="submitBtn" disabled>
                            <i class="fas fa-search mr-2"></i>
                            Lihat Data Tagihan OB
                        </button>
                    </div>
                </div>
            </form>

            @if ($voyageByKapal->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-ship text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Data Kapal Tidak Ditemukan</h3>
                    <p class="text-gray-500">Belum ada data kapal dan voyage yang tersedia dalam sistem.</p>
                    <p class="text-sm text-gray-400 mt-2">Silahkan hubungi administrator untuk menambahkan data kapal dan pergerakan kapal.</p>
                </div>
            @else
                <!-- Available Data Info -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-md p-4">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Data Tersedia
                    </h4>
                    <p class="text-sm text-blue-700">
                        Terdapat <strong>{{ $voyageByKapal->count() }}</strong> kapal dengan total 
                        <strong>{{ $voyageByKapal->sum(function($voyages) { return $voyages->count(); }) }}</strong> voyage
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Data voyage by kapal from backend
const voyageData = @json($voyageByKapal);

function updateVoyageOptions() {
    const kapalSelect = document.getElementById('kapal');
    const voyageSelect = document.getElementById('voyage');
    const summaryInfo = document.getElementById('summaryInfo');
    const selectedKapalText = document.getElementById('selectedKapalText');
    const selectedVoyageText = document.getElementById('selectedVoyageText');
    const submitBtn = document.getElementById('submitBtn');
    
    const selectedKapal = kapalSelect.value;
    
    // Clear voyage options
    voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option>';
    voyageSelect.disabled = true;
    selectedVoyageText.textContent = '-';
    
    if (selectedKapal && voyageData[selectedKapal]) {
        // Enable voyage select and populate options
        voyageSelect.disabled = false;
        
        voyageData[selectedKapal].forEach(function(voyage) {
            const option = document.createElement('option');
            option.value = voyage;
            option.textContent = voyage;
            voyageSelect.appendChild(option);
        });
        
        // Update summary
        selectedKapalText.textContent = selectedKapal;
        summaryInfo.classList.remove('hidden');
    } else {
        summaryInfo.classList.add('hidden');
        selectedKapalText.textContent = '-';
    }
    
    checkFormValidity();
}

function updateVoyageInfo() {
    const voyageSelect = document.getElementById('voyage');
    const selectedVoyageText = document.getElementById('selectedVoyageText');
    
    selectedVoyageText.textContent = voyageSelect.value || '-';
    checkFormValidity();
}

function checkFormValidity() {
    const kapalSelect = document.getElementById('kapal');
    const voyageSelect = document.getElementById('voyage');
    const submitBtn = document.getElementById('submitBtn');
    
    const isValid = kapalSelect.value && voyageSelect.value;
    submitBtn.disabled = !isValid;
}

// Add event listener for voyage selection
document.getElementById('voyage').addEventListener('change', updateVoyageInfo);

// Form submission handling
document.getElementById('selectionForm').addEventListener('submit', function(e) {
    const kapal = document.getElementById('kapal').value;
    const voyage = document.getElementById('voyage').value;
    
    if (!kapal || !voyage) {
        e.preventDefault();
        alert('Silahkan pilih kapal dan voyage terlebih dahulu.');
        return false;
    }
});
</script>
@endsection