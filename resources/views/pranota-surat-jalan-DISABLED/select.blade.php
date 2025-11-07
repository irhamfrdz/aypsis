@extends('layouts.app')

@section('title', 'Pilih Surat Jalan untuk Pranota')

@section('content')
<div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-6">
    <div class="bg-white shadow rounded-lg">
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-medium text-gray-900">🚚 Pilih Surat Jalan untuk Pranota</h3>
                    <p class="mt-1 text-xs text-gray-600">Pilih 1 surat jalan yang akan dibuatkan pranota baru</p>
                </div>
                <a href="{{ route('pranota-surat-jalan.index') }}"
                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-4">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('pranota-surat-jalan.store') }}" method="POST" id="selectSuratJalanForm">
                @csrf

                <!-- Form Input Surat Jalan -->
                <div class="space-y-3">
                    <div>
                        <label for="surat_jalan_id" class="block text-xs font-medium text-gray-700 mb-1">
                            Nomor Surat Jalan
                        </label>
                        
                        <!-- Input dengan tombol untuk buka modal -->
                        <div class="relative">
                            <input type="text" id="selectedSuratJalan" 
                                   class="block w-full px-2.5 py-1.5 pr-8 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-xs"
                                   placeholder="Klik untuk memilih surat jalan..."
                                   readonly
                                   onclick="openSuratJalanModal()">
                            <input type="hidden" name="surat_jalan_id" id="surat_jalan_id" required>
                            <input type="hidden" name="uang_jalan" id="uang_jalan_hidden" value="0">
                            <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        
                        @error('surat_jalan_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Detail Surat Jalan yang dipilih -->
                    <div id="detailSuratJalan" class="bg-gray-50 rounded-md p-3 hidden">
                        <h4 class="text-xs font-medium text-gray-700 mb-2">Detail Surat Jalan</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                            <div>
                                <span class="text-gray-500">Supir:</span>
                                <span id="detailSupir" class="ml-1 font-medium">-</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Pengirim:</span>
                                <span id="detailPengirim" class="ml-1 font-medium">-</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Tujuan:</span>
                                <span id="detailTujuan" class="ml-1 font-medium">-</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Uang Jalan:</span>
                                <span id="detailUangJalan" class="ml-1 font-medium text-green-600">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Input Jumlah -->
                    <div id="formInputJumlah" class="hidden">
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-4 w-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-2">
                                    <h3 class="text-xs font-medium text-blue-800">Input Detail Jumlah Pranota</h3>
                                    <div class="mt-1 text-xs text-blue-700">
                                        <p>Silakan isi jumlah untuk setiap kategori berikut:</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <!-- Jumlah MEL -->
                            <div>
                                <label for="jumlah_mel" class="block text-xs font-medium text-gray-700 mb-1">
                                    Jumlah MEL
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-xs">Rp</span>
                                    </div>
                                    <input type="number" name="jumlah_mel" id="jumlah_mel" 
                                           class="block w-full pl-8 pr-2 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-xs"
                                           placeholder="0" min="0" step="1000" onchange="calculateTotal()" oninput="calculateTotal()">
                                </div>
                            </div>

                            <!-- Jumlah Kawalan -->
                            <div>
                                <label for="jumlah_kawalan" class="block text-xs font-medium text-gray-700 mb-1">
                                    Jumlah Kawalan
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-xs">Rp</span>
                                    </div>
                                    <input type="number" name="jumlah_kawalan" id="jumlah_kawalan" 
                                           class="block w-full pl-8 pr-2 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-xs"
                                           placeholder="0" min="0" step="1000" onchange="calculateTotal()" oninput="calculateTotal()">
                                </div>
                            </div>

                            <!-- Jumlah Pelancar -->
                            <div>
                                <label for="jumlah_pelancar" class="block text-xs font-medium text-gray-700 mb-1">
                                    Jumlah Pelancar
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-xs">Rp</span>
                                    </div>
                                    <input type="number" name="jumlah_pelancar" id="jumlah_pelancar" 
                                           class="block w-full pl-8 pr-2 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-xs"
                                           placeholder="0" min="0" step="1000" onchange="calculateTotal()" oninput="calculateTotal()">
                                </div>
                            </div>

                            <!-- Jumlah Parkir -->
                            <div>
                                <label for="jumlah_parkir" class="block text-xs font-medium text-gray-700 mb-1">
                                    Jumlah Parkir
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-xs">Rp</span>
                                    </div>
                                    <input type="number" name="jumlah_parkir" id="jumlah_parkir" 
                                           class="block w-full pl-8 pr-2 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-xs"
                                           placeholder="0" min="0" step="1000" onchange="calculateTotal()" oninput="calculateTotal()">
                                </div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="mt-4 bg-gray-50 rounded-lg p-3 border-2 border-dashed border-gray-300">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Total Keseluruhan:</span>
                                <span id="totalKeseluruhan" class="text-lg font-bold text-green-600">Rp 0</span>
                            </div>
                            <input type="hidden" name="jumlah_total" id="jumlah_total" value="0">
                        </div>

                        <!-- Catatan -->
                        <div class="mt-4">
                            <label for="keterangan" class="block text-xs font-medium text-gray-700 mb-1">
                                Catatan <span class="text-gray-400">(Optional)</span>
                            </label>
                            <textarea name="keterangan" id="keterangan" rows="2" 
                                      class="block w-full px-2.5 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-xs"
                                      placeholder="Masukkan catatan untuk pranota ini..."></textarea>
                            @error('keterangan')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 flex justify-end gap-2">
                    <a href="{{ route('pranota-surat-jalan.index') }}"
                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md shadow-sm text-xs font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            id="createPranotaBtn" disabled>
                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Buat Pranota
                    </button>
                </div>

                @if($approvedSuratJalans->count() == 0)
                    <div class="mt-4 text-center py-6">
                        <div class="text-gray-400">
                            <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <h3 class="text-xs font-medium text-gray-400 mb-1">Tidak ada surat jalan tersedia</h3>
                            <p class="text-xs text-gray-400">Tidak ada surat jalan yang belum memiliki pranota.</p>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- Modal Pilih Surat Jalan -->
<div id="suratJalanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Header Modal -->
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-medium text-gray-900">📄 DATA Surat Jalan</h3>
                <button onclick="closeSuratJalanModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Filter dan Search -->
            <div class="py-3 border-b">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Show</span>
                        <select id="entriesPerPage" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="text-sm text-gray-600">entries</span>
                    </div>
                    <div class="flex-1"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Search:</span>
                        <input type="text" id="searchModal" 
                               class="px-3 py-1 border border-gray-300 rounded text-sm w-48"
                               placeholder="Cari surat jalan...">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No SJ</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Supir</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No Plat</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pengirim</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tujuan Ambil</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Seal</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tujuan Kirim</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                        </tr>
                    </thead>
                    <tbody id="suratJalanTableBody" class="bg-white divide-y divide-gray-200">
                        @foreach($approvedSuratJalans as $suratJalan)
                            <tr class="surat-jalan-row hover:bg-blue-50 cursor-pointer transition-colors"
                                onclick="selectSuratJalan({{ $suratJalan->id }}, '{{ $suratJalan->no_surat_jalan ?? $suratJalan->nomor_surat_jalan ?? '-' }}', '{{ $suratJalan->supir ?? '' }}', '{{ $suratJalan->pengirim ?? '' }}', '{{ $suratJalan->tujuan_pengambilan ?? '' }}', {{ $suratJalan->uang_jalan ?? 0 }})"
                                data-nomor="{{ strtolower($suratJalan->no_surat_jalan ?? $suratJalan->nomor_surat_jalan ?? '') }}"
                                data-supir="{{ strtolower($suratJalan->supir ?? '') }}"
                                data-pengirim="{{ strtolower($suratJalan->pengirim ?? '') }}"
                                data-tujuan="{{ strtolower($suratJalan->tujuan_pengambilan ?? '') }}"
                                data-barang="{{ strtolower($suratJalan->jenis_barang ?? '') }}">
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <a href="#" class="text-blue-600 hover:text-blue-800">
                                        {{ $suratJalan->no_surat_jalan ?? $suratJalan->nomor_surat_jalan ?? '-' }}
                                    </a>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-900">{{ $suratJalan->supir ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-900">{{ $suratJalan->no_plat ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-900">{{ $suratJalan->pengirim ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-900">{{ $suratJalan->tujuan_pengambilan ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-900">{{ $suratJalan->seal ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-900">{{ $suratJalan->tujuan_kirim ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-900">{{ $suratJalan->jenis_barang ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info -->
            <div class="py-3 border-t">
                <div class="flex justify-between items-center text-sm text-gray-600">
                    <div id="paginationInfo">
                        Showing 1 to {{ min(10, $approvedSuratJalans->count()) }} of {{ $approvedSuratJalans->count() }} entries
                    </div>
                    <div id="paginationControls" class="flex gap-2">
                        <!-- Pagination controls will be added via JavaScript if needed -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openSuratJalanModal() {
    document.getElementById('suratJalanModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeSuratJalanModal() {
    document.getElementById('suratJalanModal').classList.add('hidden');
    document.body.style.overflow = 'auto'; // Restore scrolling
}

function selectSuratJalan(id, nomor, supir, pengirim, tujuan, uangJalan) {
    // Set values untuk input utama
    document.getElementById('surat_jalan_id').value = id;
    document.getElementById('selectedSuratJalan').value = nomor + ' - ' + supir;
    
    // Update detail section
    document.getElementById('detailSupir').textContent = supir || '-';
    document.getElementById('detailPengirim').textContent = pengirim || '-';
    document.getElementById('detailTujuan').textContent = tujuan || '-';
    document.getElementById('detailUangJalan').textContent = 'Rp ' + (uangJalan ? parseInt(uangJalan).toLocaleString('id-ID') : '0');
    
    // Store uang jalan value for calculation and form submission
    window.currentUangJalan = parseInt(uangJalan) || 0;
    document.getElementById('uang_jalan_hidden').value = window.currentUangJalan;
    
    // Show detail section
    document.getElementById('detailSuratJalan').classList.remove('hidden');
    
    // Show form input jumlah
    document.getElementById('formInputJumlah').classList.remove('hidden');
    
    // Calculate total with uang jalan
    calculateTotal();
    
    // Enable submit button
    const submitBtn = document.getElementById('createPranotaBtn');
    if (submitBtn) {
        submitBtn.disabled = false;
    }
    
    // Close modal
    closeSuratJalanModal();
}

// Calculate total function
function calculateTotal() {
    const mel = parseInt(document.getElementById('jumlah_mel').value) || 0;
    const kawalan = parseInt(document.getElementById('jumlah_kawalan').value) || 0;
    const pelancar = parseInt(document.getElementById('jumlah_pelancar').value) || 0;
    const parkir = parseInt(document.getElementById('jumlah_parkir').value) || 0;
    const uangJalan = window.currentUangJalan || 0;
    
    const total = mel + kawalan + pelancar + parkir + uangJalan;
    
    document.getElementById('totalKeseluruhan').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('jumlah_total').value = total;
}

// Search functionality
document.getElementById('searchModal').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.surat-jalan-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const nomor = row.dataset.nomor || '';
        const supir = row.dataset.supir || '';
        const pengirim = row.dataset.pengirim || '';
        const tujuan = row.dataset.tujuan || '';
        const barang = row.dataset.barang || '';
        
        const isVisible = nomor.includes(searchTerm) ||
                         supir.includes(searchTerm) ||
                         pengirim.includes(searchTerm) ||
                         tujuan.includes(searchTerm) ||
                         barang.includes(searchTerm);
        
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
    });
    
    // Update pagination info
    const totalRows = rows.length;
    document.getElementById('paginationInfo').textContent = 
        `Showing ${visibleCount} of ${totalRows} entries${searchTerm ? ' (filtered)' : ''}`;
});

// Entries per page functionality
document.getElementById('entriesPerPage').addEventListener('change', function() {
    // This would need more complex pagination logic for production
    console.log('Entries per page changed to:', this.value);
});

// Close modal when clicking outside
document.getElementById('suratJalanModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSuratJalanModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSuratJalanModal();
    }
});
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectSuratJalan = document.getElementById('surat_jalan_id');
    const createPranotaBtn = document.getElementById('createPranotaBtn');
    const detailSuratJalan = document.getElementById('detailSuratJalan');
    const detailSupir = document.getElementById('detailSupir');
    const detailPengirim = document.getElementById('detailPengirim');
    const detailTujuan = document.getElementById('detailTujuan');
    const detailUangJalan = document.getElementById('detailUangJalan');

    // Handle surat jalan selection
    selectSuratJalan.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            // Enable submit button
            createPranotaBtn.disabled = false;
            
            // Show and populate detail section
            detailSuratJalan.classList.remove('hidden');
            detailSupir.textContent = selectedOption.dataset.supir || '-';
            detailPengirim.textContent = selectedOption.dataset.pengirim || '-';
            detailTujuan.textContent = selectedOption.dataset.tujuan || '-';
            
            const uangJalan = parseInt(selectedOption.dataset.uang_jalan) || 0;
            detailUangJalan.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(uangJalan);
        } else {
            // Disable submit button
            createPranotaBtn.disabled = true;
            
            // Hide detail section
            detailSuratJalan.classList.add('hidden');
        }
    });

    // Initialize state
    if (selectSuratJalan.value) {
        selectSuratJalan.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush