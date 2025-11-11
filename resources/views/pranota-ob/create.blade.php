@extends('layouts.app')

@section('title', 'Buat Pranota OB')

@push('styles')
<style>
.tagihan-card {
    transition: all 0.2s ease;
}

.tagihan-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.tagihan-card.selected {
    border-color: #059669;
    background-color: #f0fdf4;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
}

.selected-count {
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="bg-green-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h5 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Buat Pranota OB Baru
                    </h5>
                    <p class="text-green-100 text-sm mt-1">
                        Pilih tagihan OB yang akan dimasukkan ke dalam pranota
                    </p>
                </div>
                <div>
                    <a href="{{ route('pranota-ob.index') }}" 
                       class="bg-white text-green-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('pranota-ob.store') }}" method="POST" id="pranotaForm">
            @csrf
            <div class="p-6">
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
                        <div class="font-medium">Terjadi kesalahan:</div>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Fields -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="tanggal_pranota" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Pranota <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="tanggal_pranota" 
                               id="tanggal_pranota"
                               value="{{ old('tanggal_pranota', date('Y-m-d')) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                               required>
                    </div>

                    <div>
                        <label for="periode" class="block text-sm font-medium text-gray-700 mb-2">
                            Periode
                        </label>
                        <input type="text" 
                               name="periode" 
                               id="periode"
                               value="{{ old('periode', date('m/Y')) }}"
                               placeholder="Contoh: 11/2025"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Item Terpilih
                        </label>
                        <div class="bg-gray-50 px-3 py-2 border border-gray-300 rounded-md">
                            <span id="selectedCount" class="selected-count">0</span>
                            <span class="ml-2 text-sm text-gray-700">tagihan OB dipilih</span>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea name="keterangan" 
                              id="keterangan"
                              rows="3"
                              placeholder="Keterangan tambahan untuk pranota ini..."
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">{{ old('keterangan') }}</textarea>
                </div>

                <!-- Filter Tagihan -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h6 class="font-medium text-blue-900 mb-3">Filter Tagihan OB</h6>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <select id="filterKapal" class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Kapal</option>
                                @foreach($groupedTagihan->keys() as $kapal)
                                    <option value="{{ $kapal }}">{{ $kapal }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select id="filterVoyage" class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Voyage</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" id="selectAll" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-check-square mr-1"></i>Pilih Semua
                            </button>
                            <button type="button" id="deselectAll" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                <i class="fas fa-square mr-1"></i>Batal Pilih
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tagihan OB List -->
                <div class="mb-6">
                    <h6 class="font-medium text-gray-900 mb-4">
                        Pilih Tagihan OB 
                        <span class="text-sm font-normal text-gray-500">({{ $availableTagihanOb->count() }} tersedia)</span>
                    </h6>
                    
                    @if($availableTagihanOb->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="tagihanContainer">
                            @foreach($availableTagihanOb as $tagihan)
                                <div class="tagihan-card border border-gray-200 rounded-lg p-4 cursor-pointer" 
                                     data-kapal="{{ $tagihan->kapal }}" 
                                     data-voyage="{{ $tagihan->voyage }}"
                                     data-id="{{ $tagihan->id }}"
                                     onclick="toggleSelection(this)">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <h6 class="font-medium text-gray-900 text-sm">{{ $tagihan->nomor_kontainer }}</h6>
                                            <p class="text-xs text-gray-500">{{ $tagihan->kapal }} - {{ $tagihan->voyage }}</p>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   name="tagihan_ob_ids[]" 
                                                   value="{{ $tagihan->id }}"
                                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                                                   onchange="updateCount()">
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-1 text-xs">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Supir:</span>
                                            <span class="font-medium">{{ $tagihan->nama_supir }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Barang:</span>
                                            <span class="font-medium">{{ Str::limit($tagihan->barang, 20) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Status:</span>
                                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $tagihan->status_kontainer === 'full' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($tagihan->status_kontainer) }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                            <span class="text-gray-600">Biaya:</span>
                                            <span class="font-bold text-green-600">Rp {{ number_format($tagihan->biaya, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Summary -->
                        <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4" id="summary" style="display: none;">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h6 class="font-medium text-green-900">Total Terpilih</h6>
                                    <p class="text-sm text-green-700">
                                        <span id="totalItems">0</span> item - 
                                        <span class="font-bold">Rp <span id="totalAmount">0</span></span>
                                    </p>
                                </div>
                                <button type="submit" 
                                        class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                                        id="submitBtn" 
                                        disabled>
                                    <i class="fas fa-save mr-2"></i>
                                    Buat Pranota
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                            <h6 class="font-medium text-gray-900 mb-2">Tidak Ada Tagihan OB Tersedia</h6>
                            <p class="text-gray-500 mb-4">Semua tagihan OB sudah termasuk dalam pranota lain.</p>
                            <a href="{{ route('tagihan-ob.create') }}" class="text-green-600 hover:text-green-500">
                                <i class="fas fa-plus mr-1"></i>Buat Tagihan OB Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedTagihan = [];
const groupedData = @json($groupedTagihan);

// Filter functionality
document.getElementById('filterKapal').addEventListener('change', function() {
    const selectedKapal = this.value;
    const voyageSelect = document.getElementById('filterVoyage');
    
    // Clear voyage options
    voyageSelect.innerHTML = '<option value="">Semua Voyage</option>';
    
    if (selectedKapal && groupedData[selectedKapal]) {
        Object.keys(groupedData[selectedKapal]).forEach(voyage => {
            const option = document.createElement('option');
            option.value = voyage;
            option.textContent = voyage;
            voyageSelect.appendChild(option);
        });
    }
    
    filterTagihan();
});

document.getElementById('filterVoyage').addEventListener('change', filterTagihan);

function filterTagihan() {
    const selectedKapal = document.getElementById('filterKapal').value;
    const selectedVoyage = document.getElementById('filterVoyage').value;
    const cards = document.querySelectorAll('.tagihan-card');
    
    cards.forEach(card => {
        const kapal = card.dataset.kapal;
        const voyage = card.dataset.voyage;
        
        const kapalMatch = !selectedKapal || kapal === selectedKapal;
        const voyageMatch = !selectedVoyage || voyage === selectedVoyage;
        
        if (kapalMatch && voyageMatch) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
            // Uncheck if hidden
            const checkbox = card.querySelector('input[type="checkbox"]');
            if (checkbox.checked) {
                checkbox.checked = false;
                card.classList.remove('selected');
                updateCount();
            }
        }
    });
}

// Selection functionality
function toggleSelection(card) {
    const checkbox = card.querySelector('input[type="checkbox"]');
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        card.classList.add('selected');
    } else {
        card.classList.remove('selected');
    }
    
    updateCount();
}

// Select/Deselect all visible cards
document.getElementById('selectAll').addEventListener('click', function() {
    const visibleCards = document.querySelectorAll('.tagihan-card:not([style*="none"])');
    visibleCards.forEach(card => {
        const checkbox = card.querySelector('input[type="checkbox"]');
        if (!checkbox.checked) {
            checkbox.checked = true;
            card.classList.add('selected');
        }
    });
    updateCount();
});

document.getElementById('deselectAll').addEventListener('click', function() {
    const cards = document.querySelectorAll('.tagihan-card');
    cards.forEach(card => {
        const checkbox = card.querySelector('input[type="checkbox"]');
        checkbox.checked = false;
        card.classList.remove('selected');
    });
    updateCount();
});

// Update count and total
function updateCount() {
    const checkedBoxes = document.querySelectorAll('input[name="tagihan_ob_ids[]"]:checked');
    const count = checkedBoxes.length;
    
    document.getElementById('selectedCount').textContent = count;
    
    let total = 0;
    checkedBoxes.forEach(checkbox => {
        const card = checkbox.closest('.tagihan-card');
        const biayaText = card.querySelector('.font-bold.text-green-600').textContent;
        const biaya = parseInt(biayaText.replace(/[^\d]/g, ''));
        total += biaya;
    });
    
    document.getElementById('totalItems').textContent = count;
    document.getElementById('totalAmount').textContent = new Intl.NumberFormat('id-ID').format(total);
    
    // Show/hide summary and enable/disable submit button
    const summary = document.getElementById('summary');
    const submitBtn = document.getElementById('submitBtn');
    
    if (count > 0) {
        summary.style.display = 'block';
        submitBtn.disabled = false;
    } else {
        summary.style.display = 'none';
        submitBtn.disabled = true;
    }
}

// Form validation
document.getElementById('pranotaForm').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('input[name="tagihan_ob_ids[]"]:checked');
    
    if (checkedBoxes.length === 0) {
        e.preventDefault();
        alert('Pilih minimal satu tagihan OB');
        return false;
    }
});

// Initialize
updateCount();
</script>
@endpush