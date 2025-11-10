@extends('layouts.app')

@section('title', 'Form OB Muat')
@section('page_title', 'Form OB Muat')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Form OB Muat</h1>
            <a href="{{ route('supir.ob-muat.index', ['kapal' => $selectedKapal, 'voyage' => $selectedVoyage]) }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-150 flex items-center">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Index
            </a>
        </div>
    </div>

    @if($existingTagihanOb)
        <!-- Alert jika sudah ada tagihan OB -->
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="font-medium">Tagihan OB untuk kontainer ini sudah ada!</span>
            </div>
            <div class="mt-2 text-sm">
                <p>Data: {{ $existingTagihanOb->kapal }} - {{ $existingTagihanOb->voyage }} - {{ $existingTagihanOb->nomor_kontainer }}</p>
                <p>Status: {{ $existingTagihanOb->status_kontainer_label }}, Biaya: {{ $existingTagihanOb->formatted_biaya }}</p>
                <p>Dibuat: {{ $existingTagihanOb->created_at->format('d/m/Y H:i') }} oleh {{ $existingTagihanOb->nama_supir }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">Terjadi kesalahan:</span>
            </div>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form OB Muat -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Form Tagihan OB Muat
                    </h2>
                </div>

                <div class="p-6">
                    <form action="{{ route('supir.ob-muat.store') }}" method="POST" id="obMuatForm">
                        @csrf

                        <!-- Hidden fields -->
                        <input type="hidden" name="kapal" value="{{ $selectedKapal }}">
                        <input type="hidden" name="voyage" value="{{ $selectedVoyage }}">
                        <input type="hidden" name="nomor_kontainer" value="{{ $nomorKontainer }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Info Kapal & Voyage -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kapal</label>
                                <div class="bg-gray-50 border border-gray-300 rounded-md px-3 py-2 text-gray-900">
                                    {{ $selectedKapal }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Voyage</label>
                                <div class="bg-gray-50 border border-gray-300 rounded-md px-3 py-2 text-gray-900">
                                    {{ $selectedVoyage }}
                                </div>
                            </div>

                            <!-- Nomor Kontainer -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Kontainer</label>
                                <div class="bg-gray-50 border border-gray-300 rounded-md px-3 py-2 text-gray-900 font-mono">
                                    {{ $nomorKontainer }}
                                </div>
                            </div>

                            <!-- Jenis Barang -->
                            <div class="md:col-span-2">
                                <label for="barang" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Barang <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="barang" 
                                       id="barang"
                                       value="{{ old('barang', $bl ? $bl->nama_barang : '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('barang') border-red-500 @enderror"
                                       placeholder="Masukkan jenis barang..."
                                       required>
                                @error('barang')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status Kontainer -->
                            <div>
                                <label for="status_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status Kontainer <span class="text-red-500">*</span>
                                </label>
                                <select name="status_kontainer" 
                                        id="status_kontainer"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status_kontainer') border-red-500 @enderror"
                                        required
                                        onchange="updateBiayaFromPricelist()">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="full" {{ old('status_kontainer', $defaultStatusKontainer) == 'full' ? 'selected' : '' }}>Full (Tarik Isi)</option>
                                    <option value="empty" {{ old('status_kontainer', $defaultStatusKontainer) == 'empty' ? 'selected' : '' }}>Empty (Tarik Kosong)</option>
                                </select>
                                @error('status_kontainer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @if($suratJalan && $suratJalan->aktifitas)
                                    <p class="mt-1 text-xs text-blue-600">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Berdasarkan aktifitas surat jalan: {{ $suratJalan->aktifitas }}
                                    </p>
                                @endif
                            </div>

                            <!-- Size Kontainer -->
                            <div>
                                <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-2">
                                    Size Kontainer <span class="text-red-500">*</span>
                                </label>
                                <select name="size_kontainer" 
                                        id="size_kontainer"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('size_kontainer') border-red-500 @enderror"
                                        required
                                        onchange="updateBiayaFromPricelist()">
                                    <option value="">-- Pilih Size --</option>
                                    <option value="20ft" {{ old('size_kontainer', $bl && $bl->size ? $bl->size . 'ft' : '') == '20ft' ? 'selected' : '' }}>20 ft</option>
                                    <option value="40ft" {{ old('size_kontainer', $bl && $bl->size ? $bl->size . 'ft' : '') == '40ft' ? 'selected' : '' }}>40 ft</option>
                                </select>
                                @error('size_kontainer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Biaya -->
                            <div class="md:col-span-2">
                                <label for="biaya" class="block text-sm font-medium text-gray-700 mb-2">
                                    Biaya <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" 
                                           name="biaya" 
                                           id="biaya"
                                           value="{{ old('biaya', 0) }}"
                                           step="0.01"
                                           min="0"
                                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('biaya') border-red-500 @enderror"
                                           placeholder="0.00"
                                           required>
                                </div>
                                @error('biaya')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <div id="price-suggestion" class="mt-1 text-xs text-blue-600 hidden">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span id="price-suggestion-text"></span>
                                    <button type="button" onclick="applyPriceSuggestion()" class="ml-2 text-blue-600 hover:text-blue-800 underline">Terapkan</button>
                                </div>
                            </div>

                            <!-- Keterangan -->
                            <div class="md:col-span-2">
                                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                                <textarea name="keterangan" 
                                          id="keterangan"
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-150 flex items-center"
                                    {{ $existingTagihanOb ? 'disabled' : '' }}>
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $existingTagihanOb ? 'Tagihan Sudah Ada' : 'Simpan Tagihan OB' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 p-4">
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Kontainer</h3>
                </div>
                <div class="p-4">
                    @if($bl)
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. BL</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $bl->no_bl }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Seal</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $bl->no_seal ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Barang</dt>
                                <dd class="text-sm text-gray-900">{{ $bl->nama_barang ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Size</dt>
                                <dd class="text-sm text-gray-900">{{ $bl->size ? $bl->size . ' ft' : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tgl. Gate In</dt>
                                <dd class="text-sm text-gray-900">{{ $bl->tgl_gate_in ? $bl->tgl_gate_in->format('d/m/Y') : '-' }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-500">Data BL tidak ditemukan.</p>
                    @endif

                    @if($suratJalan)
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Surat Jalan Terkait</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">No. Surat Jalan</dt>
                                    <dd class="text-xs text-gray-900 font-mono">{{ $suratJalan->no_surat_jalan }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">Aktifitas</dt>
                                    <dd class="text-xs text-gray-900">{{ $suratJalan->aktifitas ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">Supir</dt>
                                    <dd class="text-xs text-gray-900">{{ $suratJalan->supir ?: '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Master Pricelist OB -->
            <div class="mt-6 bg-blue-50 rounded-lg border border-blue-200">
                <div class="border-b border-blue-200 p-4">
                    <h3 class="text-sm font-semibold text-blue-800">Master Pricelist OB</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        @forelse($masterPricelistObs as $pricelist)
                            <div class="text-xs bg-white border border-blue-100 rounded p-2">
                                <div class="font-medium text-blue-900">{{ $pricelist->size_kontainer }} - {{ $pricelist->status_kontainer_label }}</div>
                                <div class="text-blue-700">{{ $pricelist->formatted_biaya }}</div>
                            </div>
                        @empty
                            <p class="text-xs text-blue-600">Belum ada pricelist OB.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for auto-calculate biaya -->
<script>
const masterPricelistObs = @json($masterPricelistObs);
let suggestedPrice = 0;

function updateBiayaFromPricelist() {
    const statusKontainer = document.getElementById('status_kontainer').value;
    const sizeKontainer = document.getElementById('size_kontainer').value;
    
    if (statusKontainer && sizeKontainer) {
        const pricelist = masterPricelistObs.find(p => 
            p.status_kontainer === statusKontainer && p.size_kontainer === sizeKontainer
        );
        
        if (pricelist) {
            suggestedPrice = parseFloat(pricelist.biaya);
            document.getElementById('price-suggestion-text').textContent = 
                `Saran dari pricelist: Rp ${new Intl.NumberFormat('id-ID').format(suggestedPrice)}`;
            document.getElementById('price-suggestion').classList.remove('hidden');
        } else {
            document.getElementById('price-suggestion').classList.add('hidden');
            suggestedPrice = 0;
        }
    } else {
        document.getElementById('price-suggestion').classList.add('hidden');
        suggestedPrice = 0;
    }
}

function applyPriceSuggestion() {
    if (suggestedPrice > 0) {
        document.getElementById('biaya').value = suggestedPrice;
        document.getElementById('price-suggestion').classList.add('hidden');
    }
}

// Auto-update when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateBiayaFromPricelist();
});
</script>
@endsection