@extends('layouts.app')

@section('title', 'Perbaikan Kontainer')

@section('content')
<style>
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table-responsive table {
    min-width: 1200px;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }

    .table-responsive th,
    .table-responsive td {
        padding: 0.5rem;
        white-space: nowrap;
    }
}

/* Compact modal styles */
#catatanModal .modal-content {
    font-size: 0.875rem;
}

#catatanModal .modal-header {
    padding: 0.75rem 1rem;
}

#catatanModal .modal-body {
    padding: 1rem;
}

#catatanModal .modal-footer {
    padding: 0.75rem 1rem;
}

#catatanModal input,
#catatanModal select,
#catatanModal textarea {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
    height: auto;
}

#catatanModal textarea {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

#catatanModal label {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

#catatanModal .grid {
    gap: 0.75rem;
}

#catatanModal .bg-blue-50 {
    padding: 0.5rem;
    margin-bottom: 0.75rem;
}

#catatanModal .text-sm {
    font-size: 0.75rem;
}

#catatanModal .text-xs {
    font-size: 0.6875rem;
}

#catatanModal .px-4 {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

#catatanModal .py-2 {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}

#catatanModal .space-x-2 > * + * {
    margin-left: 0.5rem;
}

/* Make modal more compact */
#catatanModal h3 {
    font-size: 1rem;
    font-weight: 600;
}

#catatanModal h4 {
    font-size: 0.75rem;
    font-weight: 600;
}

#catatanModal .mb-4 {
    margin-bottom: 0.75rem;
}

#catatanModal .mb-1 {
    margin-bottom: 0.25rem;
}

#catatanModal .mt-4 {
    margin-top: 0.75rem;
}

#catatanModal .pt-4 {
    padding-top: 0.75rem;
}
</style>
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Perbaikan Kontainer</h1>
                <p class="text-gray-600 mt-1">Kelola data perbaikan kontainer</p>
            </div>
            @can('perbaikan-kontainer-create')
            <a href="{{ route('perbaikan-kontainer.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Perbaikan
            </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('perbaikan-kontainer.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="belum_masuk_pranota" {{ request('status') == 'belum_masuk_pranota' ? 'selected' : '' }}>Belum Masuk Pranota</option>
                        <option value="sudah_masuk_pranota" {{ request('status') == 'sudah_masuk_pranota' ? 'selected' : '' }}>Sudah Masuk Pranota</option>
                        <option value="sudah_dibayar" {{ request('status') == 'sudah_dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Pencarian..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Filter
                    </button>
                    <a href="{{ route('perbaikan-kontainer.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div id="bulkActions" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-blue-800">
                        <span id="selectedCount">0</span> item dipilih
                    </span>
                    <div class="flex space-x-2">
                        <button type="button" id="btnBulkDelete"
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                            Hapus Terpilih
                        </button>
                        <button type="button" id="btnBulkStatus"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                            Update Status
                        </button>
                        <button type="button" id="btnBulkPranota"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                            Masukan Pranota
                        </button>
                    </div>
                </div>
                <button type="button" id="btnCancelSelection"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Batal
                </button>
            </div>
        </div>
        <!-- Table Container with Horizontal Scroll -->
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg table-responsive">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Nomor Tagihan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Kontainer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Vendor/Bengkel</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Estimasi Kerusakan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Estimasi Biaya</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Realisasi Biaya</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Tgl Masuk</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Tgl Selesai</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Tgl Cat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-[10px]">
                    @forelse($perbaikanKontainers as $index => $perbaikan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 whitespace-nowrap">
                            <input type="checkbox" name="selected_items[]" value="{{ $perbaikan->id }}"
                                   class="item-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                            {{ $loop->iteration + ($perbaikanKontainers->currentPage() - 1) * $perbaikanKontainers->perPage() }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $perbaikan->nomor_tagihan ?? '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $perbaikan->nomor_kontainer ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $perbaikan->vendorBengkel->nama_bengkel ?? $perbaikan->vendor_bengkel ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-2">
                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $perbaikan->estimasi_kerusakan_kontainer }}">
                                {{ Str::limit($perbaikan->estimasi_kerusakan_kontainer, 30) }}
                            </div>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                            {{ $perbaikan->estimasi_biaya_perbaikan ? 'Rp ' . number_format($perbaikan->estimasi_biaya_perbaikan, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                            {{ $perbaikan->realisasi_biaya_perbaikan ? 'Rp ' . number_format($perbaikan->realisasi_biaya_perbaikan, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $perbaikan->status_color }}">
                                {{ $perbaikan->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                            {{ $perbaikan->tanggal_perbaikan ? \Carbon\Carbon::parse($perbaikan->tanggal_perbaikan)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                            {{ $perbaikan->tanggal_selesai ? \Carbon\Carbon::parse($perbaikan->tanggal_selesai)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                            {{ $perbaikan->tanggal_cat ? \Carbon\Carbon::parse($perbaikan->tanggal_cat)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @can('perbaikan-kontainer-view')
                                <a href="{{ route('perbaikan-kontainer.show', $perbaikan) }}"
                                   class="text-blue-600 hover:text-blue-900">Lihat</a>
                                @endcan
                                @can('perbaikan-kontainer-update')
                                <a href="{{ route('perbaikan-kontainer.edit', $perbaikan) }}"
                                   class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endcan
                                @if(!$perbaikan->tanggal_cat)
                                @can('perbaikan-kontainer-update')
                                <button type="button" onclick="openCatatanModal({{ $perbaikan->id }}, '{{ $perbaikan->nomor_tagihan ?? 'N/A' }}', '{{ $perbaikan->estimasi_kerusakan_kontainer ?? '' }}', '{{ $perbaikan->nomor_kontainer ?? 'N/A' }}', '{{ $perbaikan->vendorBengkel->nama_bengkel ?? '' }}', '{{ $perbaikan->kontainer->ukuran ?? 'N/A' }}')"
                                        class="text-green-600 hover:text-green-900 focus:outline-none focus:ring-0"
                                        aria-label="Buka modal catatan perbaikan">Butuh Cat</button>
                                @endcan
                                @endif
                                @can('perbaikan-kontainer-delete')
                                <form method="POST" action="{{ route('perbaikan-kontainer.destroy', $perbaikan) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data perbaikan ini?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="px-4 py-4 text-center text-gray-500">
                            Tidak ada data perbaikan kontainer ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($perbaikanKontainers->hasPages())
        <div class="mt-6">
            {{ $perbaikanKontainers->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

<!-- Pranota Modal -->
<div id="pranotaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[60]">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-lg font-medium text-gray-900">Masukan ke Pranota Perbaikan Kontainer</h3>
                <button id="closePranotaModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4">
                <form id="pranotaForm" method="POST" action="{{ route('pranota-perbaikan-kontainer.store') }}">
                    @csrf

                    <!-- Selected Items Info -->
                    <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Item yang dipilih:</h4>
                        <div id="selectedItemsList" class="text-sm text-blue-700">
                            <!-- Selected items will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Pranota Form Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Pranota *</label>
                            <input type="text" name="nomor_pranota" id="nomor_pranota" readonly
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Nomor akan di-generate otomatis">
                            <p class="text-xs text-gray-500 mt-1">
                                Format: PMP (3 digit) + 1 (cetakan) + 25 (tahun) + 09 (bulan) + 000001 (running number)<br>
                                <span id="formatPreview" class="font-mono text-blue-600"></span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pranota *</label>
                            <input type="date" name="tanggal_pranota" id="tanggal_pranota" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ date('Y-m-d') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vendor/Bengkel *</label>
                            <input type="text" name="supplier" id="supplier" required readonly
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Vendor akan terisi otomatis berdasarkan item yang dipilih">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Biaya Total</label>
                            <input type="text" name="estimasi_biaya_total" id="estimasi_biaya_total" readonly
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Rp 0">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Realisasi Biaya Total</label>
                            <input type="text" name="realisasi_biaya_total" id="realisasi_biaya_total"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Rp 0">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="catatan" id="catatan" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Masukkan catatan tambahan..."></textarea>
                    </div>

                    <!-- Hidden field for selected IDs -->
                    <input type="hidden" name="perbaikan_ids" id="perbaikan_ids">

                    <!-- Hidden fields for numeric values -->
                    <input type="hidden" name="estimasi_biaya_total_numeric" id="estimasi_biaya_total_numeric">
                    <input type="hidden" name="realisasi_biaya_total_numeric" id="realisasi_biaya_total_numeric">
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-4 border-t">
                <button id="cancelPranotaBtn" type="button"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:ring-2 focus:ring-gray-500 mr-2">
                    Batal
                </button>
                <button id="submitPranotaBtn" type="button"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                    Simpan ke Pranota
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global functions for sidebar interaction
function disableSidebarInteraction() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.style.pointerEvents = 'none';
        sidebar.style.opacity = '0.5';
    }
}

function enableSidebarInteraction() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.style.pointerEvents = 'auto';
        sidebar.style.opacity = '1';
    }
}

// Global function for rupiah input handling
function handleRupiahInput(input) {
    input.addEventListener('input', function(e) {
        let value = this.value;
        let number = rupiahToNumber(value);
        if (number > 0) {
            this.value = formatRupiah(number);
        } else {
            this.value = '';
        }
    });

    input.addEventListener('focus', function(e) {
        if (this.value === 'Rp 0' || this.value === '') {
            this.value = '';
        }
    });

    input.addEventListener('blur', function(e) {
        if (this.value === '' || rupiahToNumber(this.value) === 0) {
            this.value = 'Rp 0';
        }
    });
}

// Global function to format rupiah
function formatRupiah(angka, prefix = 'Rp ') {
    console.log('formatRupiah called with:', angka, typeof angka);
    if (!angka) return '';
    let number_string = angka.toString().replace(/[^,\d]/g, ''),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    const result = prefix + rupiah;
    console.log('formatRupiah result:', result);
    return result;
}

// Global function to convert rupiah to number
function rupiahToNumber(rupiah) {
    const result = parseFloat(rupiah.replace(/[^\d]/g, '')) || 0;
    console.log('rupiahToNumber called with:', rupiah, 'result:', result);
    return result;
}

// Global function to get tarif from pricelist data
function getTarifFromPricelist(vendor, jenisCat, ukuranKontainer = null) {
    // Pricelist data passed from controller
    const pricelistData = @json($pricelistData);

    console.log('getTarifFromPricelist called with:', { vendor, jenisCat, ukuranKontainer });
    console.log('Pricelist data:', pricelistData);

    // Normalize ukuranKontainer to match pricelist format
    let normalizedUkuran = null;
    if (ukuranKontainer) {
        // Convert "20", "20 ft" to "20ft", "40" to "40ft", etc.
        const numMatch = ukuranKontainer.match(/(\d+)/);
        if (numMatch) {
            normalizedUkuran = numMatch[1] + 'ft';
        }
        console.log('Normalized ukuran from', ukuranKontainer, 'to', normalizedUkuran);
    }

    // Find matching pricelist entry
    const pricelistEntry = pricelistData.find(item =>
        item.vendor === vendor && item.jenis_cat === jenisCat && (!normalizedUkuran || item.ukuran_kontainer === normalizedUkuran)
    );

    console.log('Matching pricelist entry:', pricelistEntry);

    return pricelistEntry ? pricelistEntry.tarif : null;
}

// Global function to update estimasi biaya cat
function updateEstimasiBiayaCat() {
    const vendorSelect = document.getElementById('teknisi');
    const statusSelect = document.getElementById('status_perbaikan');
    const estimasiInput = document.getElementById('estimasi_biaya_cat');

    if (!vendorSelect || !statusSelect || !estimasiInput) return;

    const selectedVendor = vendorSelect.value;
    const selectedStatus = statusSelect.value;

    console.log('updateEstimasiBiayaCat called with:', { selectedVendor, selectedStatus });

    if (selectedVendor && selectedStatus) {
        const tarif = getTarifFromPricelist(selectedVendor, selectedStatus, window.currentUkuranKontainer);
        console.log('Tarif found:', tarif, typeof tarif);
        if (tarif !== null) {
            // Ensure tarif is treated as a number
            const numericTarif = parseFloat(tarif) || 0;
            console.log('Numeric tarif:', numericTarif);
            const formattedTarif = formatRupiah(numericTarif);
            console.log('Formatted tarif:', formattedTarif);
            estimasiInput.value = formattedTarif;
        } else {
            console.log('No tarif found, clearing input');
            estimasiInput.value = '';
        }
    } else {
        console.log('Vendor or status not selected, clearing input');
        estimasiInput.value = '';
    }
}

// Global function to preview tagihan cat format
function previewTagihanCatFormat() {
    const now = new Date();
    const year = now.getFullYear().toString().slice(-2);
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const kode = 'TC';
    const cetakan = '1';
    const runningNumber = '0000001';

    return `${kode}${cetakan}${year}${month}${runningNumber}`;
}

// Global function to generate tagihan cat number
function generateTagihanCatNumber() {
    const now = new Date();
    const year = now.getFullYear().toString().slice(-2); // 2 digit tahun (25)
    const month = (now.getMonth() + 1).toString().padStart(2, '0'); // 2 digit bulan (09)
    const kode = 'TC'; // 2 digit kode
    const cetakan = '1'; // 1 digit nomor cetakan

    // Get running number from localStorage or start from 1
    let runningNumber = parseInt(localStorage.getItem('tagihan_cat_running_number') || '0') + 1;

    // Reset counter if it's a new month
    const lastGenerated = localStorage.getItem('tagihan_cat_last_generated');
    const currentMonth = `${year}${month}`;

    if (lastGenerated !== currentMonth) {
        runningNumber = 1;
        localStorage.setItem('tagihan_cat_last_generated', currentMonth);
    }

    // Save new running number
    localStorage.setItem('tagihan_cat_running_number', runningNumber.toString());

    // Format running number to 7 digits
    const formattedRunningNumber = runningNumber.toString().padStart(7, '0');

    const nomorTagihanCat = `${kode}${cetakan}${year}${month}${formattedRunningNumber}`;
    return nomorTagihanCat;
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const btnBulkStatus = document.getElementById('btnBulkStatus');
    const btnBulkPranota = document.getElementById('btnBulkPranota');
    const btnCancelSelection = document.getElementById('btnCancelSelection');

    // Modal elements
    const pranotaModal = document.getElementById('pranotaModal');
    const closePranotaModal = document.getElementById('closePranotaModal');
    const cancelPranotaBtn = document.getElementById('cancelPranotaBtn');
    const submitPranotaBtn = document.getElementById('submitPranotaBtn');
    const pranotaForm = document.getElementById('pranotaForm');
    const selectedItemsList = document.getElementById('selectedItemsList');
    const perbaikanIdsInput = document.getElementById('perbaikan_ids');

    console.log('JavaScript loaded successfully');
    console.log('Elements found:', {
        selectAllCheckbox: !!selectAllCheckbox,
        itemCheckboxes: itemCheckboxes.length,
        bulkActions: !!bulkActions,
        selectedCount: !!selectedCount,
        btnBulkPranota: !!btnBulkPranota
    });

    // Initialize bulk actions on page load
    updateBulkActions();

    // Handle select all checkbox
    selectAllCheckbox.addEventListener('change', function() {
        console.log('Select all checkbox changed:', this.checked);
        const isChecked = this.checked;
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        updateBulkActions();
    });

    // Handle individual checkboxes
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            console.log('Individual checkbox changed:', this.checked, 'ID:', this.value);
            updateSelectAllState();
            updateBulkActions();
        });
    });

    // Update select all checkbox state
    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const totalBoxes = itemCheckboxes.length;

        if (checkedBoxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedBoxes.length === totalBoxes) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Update bulk actions visibility and count
    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const count = checkedBoxes.length;

        console.log('updateBulkActions called, checked boxes:', count);
        console.log('bulkActions element:', bulkActions);
        console.log('selectedCount element:', selectedCount);

        if (selectedCount) {
            selectedCount.textContent = count;
        }

        if (bulkActions) {
            if (count > 0) {
                console.log('Showing bulk actions - removing hidden class');
                bulkActions.classList.remove('hidden');
                bulkActions.style.display = 'block'; // Force show
            } else {
                console.log('Hiding bulk actions - adding hidden class');
                bulkActions.classList.add('hidden');
                bulkActions.style.display = 'none'; // Force hide
            }
        } else {
            console.error('bulkActions element not found!');
        }
    }

    // Cancel selection
    btnCancelSelection.addEventListener('click', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        updateBulkActions();
    });

    // Bulk delete handler
    btnBulkDelete.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal satu item untuk dihapus');
            return;
        }

        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        const message = `Apakah Anda yakin ingin menghapus ${checkedBoxes.length} item yang dipilih?`;

        if (confirm(message)) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("perbaikan-kontainer.bulk-delete") }}';

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Add method
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            // Add selected IDs
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    });

    // Bulk status update handler
    btnBulkStatus.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal satu item untuk update status');
            return;
        }

        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        const newStatus = prompt('Masukkan status baru:\n1. belum_masuk_pranota\n2. sudah_masuk_pranota\n3. sudah_dibayar');

        let statusValue = '';
        if (newStatus === '1' || newStatus === 'belum_masuk_pranota') {
            statusValue = 'belum_masuk_pranota';
        } else if (newStatus === '2' || newStatus === 'sudah_masuk_pranota') {
            statusValue = 'sudah_masuk_pranota';
        } else if (newStatus === '3' || newStatus === 'sudah_dibayar') {
            statusValue = 'sudah_dibayar';
        }

        if (statusValue) {
            const message = `Apakah Anda yakin ingin mengubah status ${checkedBoxes.length} item menjadi "${statusValue}"?`;
            if (confirm(message)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("perbaikan-kontainer.bulk-update-status") }}';

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Add status
                const statusField = document.createElement('input');
                statusField.type = 'hidden';
                statusField.name = 'status';
                statusField.value = statusValue;
                form.appendChild(statusField);

                // Add selected IDs
                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        } else if (newStatus) {
            alert('Status tidak valid. Pilih:\n1. belum_masuk_pranota\n2. sudah_masuk_pranota\n3. sudah_dibayar');
        }
    });

    // Bulk pranota handler - Open modal
    btnBulkPranota.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal satu item untuk dimasukkan ke pranota');
            return;
        }

        // Get selected items data and validate vendors
        const selectedItems = [];
        const ids = [];
        const vendors = new Set();

        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const nomorTagihan = row.querySelector('td:nth-child(3)').textContent.trim();
            const nomorKontainer = row.querySelector('td:nth-child(4) div:first-child').textContent.trim();
            const vendorName = row.querySelector('td:nth-child(5) div:first-child').textContent.trim();
            const id = checkbox.value;

            selectedItems.push({
                nomorTagihan: nomorTagihan || 'N/A',
                nomorKontainer: nomorKontainer,
                vendor: vendorName
            });
            ids.push(id);

            // Collect unique vendors
            if (vendorName && vendorName !== '-') {
                vendors.add(vendorName);
            }
        });

        // Validate vendor consistency
        if (vendors.size > 1) {
            const vendorList = Array.from(vendors).join(', ');
            alert(`Error: Item yang dipilih memiliki vendor/bengkel yang berbeda:\n${vendorList}\n\nSilakan pilih item dengan vendor yang sama untuk membuat pranota bersama.`);
            return;
        }

        // Get the vendor name (should be only one since we validated)
        const vendorName = vendors.size > 0 ? Array.from(vendors)[0] : '';

        // Populate modal with selected items
        selectedItemsList.innerHTML = selectedItems.map(item =>
            `<div>• Tagihan: ${item.nomorTagihan} - Kontainer: ${item.nomorKontainer} - Vendor: ${item.vendor}</div>`
        ).join('');

        // Set hidden input with selected IDs
        perbaikanIdsInput.value = JSON.stringify(ids);

        // Auto-populate vendor field
        document.getElementById('supplier').value = vendorName;

        // Calculate total estimasi biaya
        let totalEstimasi = 0;
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const estimasiBiaya = row.querySelector('td:nth-child(7)').textContent.trim();
            // Parse Rupiah format: "Rp 200.000" -> 200000
            const biaya = parseFloat(estimasiBiaya.replace(/[^\d]/g, '')) || 0;
            totalEstimasi += biaya;
        });

        // Set estimasi biaya total
        document.getElementById('estimasi_biaya_total').value = formatRupiah(totalEstimasi);

        // Calculate total realisasi biaya
        let totalRealisasi = 0;
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const realisasiBiaya = row.querySelector('td:nth-child(8)').textContent.trim();
            // Parse Rupiah format: "Rp 200.000" -> 200000
            const biaya = parseFloat(realisasiBiaya.replace(/[^\d]/g, '')) || 0;
            totalRealisasi += biaya;
        });

        // Set realisasi biaya total
        document.getElementById('realisasi_biaya_total').value = formatRupiah(totalRealisasi);

        // Initialize rupiah formatting for realisasi input
        const realisasiInput = document.getElementById('realisasi_biaya_total');
        handleRupiahInput(realisasiInput);

        // Auto-generate nomor pranota
        const nomorPranota = generatePranotaNumber();
        document.getElementById('nomor_pranota').value = nomorPranota;

        // Update format preview
        const previewFormat = previewPranotaFormat();
        document.getElementById('formatPreview').textContent = `Contoh: ${previewFormat}`;

        // Disable sidebar interaction
        disableSidebarInteraction();

        // Show modal
        pranotaModal.classList.remove('hidden');
    });

    // Modal handlers
    // Close modal
    closePranotaModal.addEventListener('click', function() {
        pranotaModal.classList.add('hidden');
        enableSidebarInteraction();
    });

    cancelPranotaBtn.addEventListener('click', function() {
        pranotaModal.classList.add('hidden');
        enableSidebarInteraction();
    });

    // Close modal when clicking outside
    pranotaModal.addEventListener('click', function(e) {
        if (e.target === pranotaModal) {
            pranotaModal.classList.add('hidden');
            enableSidebarInteraction();
        }
    });

    // Generate pranota number function
    function generatePranotaNumber() {
        const now = new Date();
        const year = now.getFullYear().toString().slice(-2); // 2 digit tahun (25)
        const month = (now.getMonth() + 1).toString().padStart(2, '0'); // 2 digit bulan (09)
        const kode = 'PMP'; // 3 digit kode
        const cetakan = '1'; // 1 digit nomor cetakan

        // Get running number from localStorage or start from 1
        let runningNumber = parseInt(localStorage.getItem('pranota_running_number') || '0') + 1;

        // Reset counter if it's a new month
        const lastGenerated = localStorage.getItem('pranota_last_generated');
        const currentMonth = `${year}${month}`;

        if (lastGenerated !== currentMonth) {
            runningNumber = 1;
            localStorage.setItem('pranota_last_generated', currentMonth);
        }

        // Save new running number
        localStorage.setItem('pranota_running_number', runningNumber.toString());

        // Format running number to 6 digits
        const formattedRunningNumber = runningNumber.toString().padStart(6, '0');

        const nomorPranota = `${kode}${cetakan}${year}${month}${formattedRunningNumber}`;
        return nomorPranota;
    }

    // Preview format function
    function previewPranotaFormat() {
        const now = new Date();
        const year = now.getFullYear().toString().slice(-2);
        const month = (now.getMonth() + 1).toString().padStart(2, '0');
        const kode = 'PMP';
        const cetakan = '1';
        const runningNumber = '000001';

        return `${kode}${cetakan}${year}${month}${runningNumber}`;
    }

    // Submit pranota form
    submitPranotaBtn.addEventListener('click', function() {
        const nomorPranota = document.getElementById('nomor_pranota').value.trim();
        const tanggalPranota = document.getElementById('tanggal_pranota').value;
        const supplier = document.getElementById('supplier').value.trim();

        if (!nomorPranota) {
            alert('Nomor pranota belum ter-generate!');
            document.getElementById('nomor_pranota').focus();
            return;
        }

        if (!tanggalPranota) {
            alert('Tanggal pranota harus diisi!');
            document.getElementById('tanggal_pranota').focus();
            return;
        }

        if (!supplier) {
            alert('Vendor/Bengkel belum terisi. Pastikan item yang dipilih memiliki vendor yang valid.');
            return;
        }

        const realisasiBiayaTotal = rupiahToNumber(document.getElementById('realisasi_biaya_total').value);
        if (realisasiBiayaTotal < 0) {
            alert('Realisasi biaya total tidak boleh negatif!');
            document.getElementById('realisasi_biaya_total').focus();
            return;
        }

        // Convert rupiah format to number for form submission
        const estimasiNumeric = rupiahToNumber(document.getElementById('estimasi_biaya_total').value);
        const realisasiNumeric = rupiahToNumber(document.getElementById('realisasi_biaya_total').value);

        document.getElementById('estimasi_biaya_total_numeric').value = estimasiNumeric;
        document.getElementById('realisasi_biaya_total_numeric').value = realisasiNumeric;

        // Submit form
        pranotaForm.submit();
    });
});
</script>

<!-- Catatan Modal -->
<div id="catatanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[60]" role="dialog" aria-modal="true" aria-labelledby="catatanModalTitle">
    <div class="relative top-5 mx-auto p-4 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 id="catatanModalTitle" class="text-lg font-medium text-gray-900">Input Informasi Perbaikan</h3>
                <button id="closeCatatanModal" class="text-gray-400 hover:text-gray-600 p-1" aria-label="Tutup modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4">
                <form id="catatanForm" method="POST" action="{{ route('perbaikan-kontainer.add-catatan') }}">
                    @csrf

                    <!-- Item Info -->
                    <div class="mb-3 p-2 bg-blue-50 rounded-lg">
                        <h4 class="text-xs font-medium text-blue-800 mb-1">Data Perbaikan Kontainer:</h4>
                        <div id="catatanItemInfo" class="text-xs text-blue-700">
                            <!-- Item info will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Catatan Form Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Tagihan Cat *</label>
                            <input type="text" name="nomor_tagihan_cat" id="nomor_tagihan_cat" readonly
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Nomor akan di-generate otomatis">
                            <p class="text-xs text-gray-500 mt-1">
                                Format: TC (2 digit) + 1 (cetakan) + 25 (tahun) + 09 (bulan) + 0000001 (running number)<br>
                                <span id="tagihanCatFormatPreview" class="font-mono text-blue-600"></span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Cat</label>
                            <select name="status_perbaikan" id="status_perbaikan"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Pilih Status --</option>
                                @foreach($pricelistJenisCat as $jenis)
                                <option value="{{ $jenis }}">{{ $jenis == 'cat_full' ? 'Cat Full' : 'Cat Sebagian' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vendor/Bengkel *</label>
                            <select name="teknisi" id="teknisi" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Pilih Vendor/Bengkel --</option>
                                @foreach($pricelistVendors as $vendor)
                                <option value="{{ $vendor }}">{{ $vendor }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kontainer</label>
                            <input type="text" name="nomor_kontainer" id="nomor_kontainer" readonly
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Nomor kontainer">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan *</label>
                            <textarea name="catatan" id="catatan_text" rows="3" required
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Masukkan detail catatan perbaikan..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Cat</label>
                            <input type="date" name="tanggal_cat" id="tanggal_catatan"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ date('Y-m-d') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Biaya Cat</label>
                            <input type="text" name="estimasi_biaya_cat" id="estimasi_biaya_cat"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Rp 0">
                        </div>
                    </div>

                    <!-- Hidden field for perbaikan ID -->
                    <input type="hidden" name="perbaikan_id" id="catatan_perbaikan_id">

                    <!-- Hidden field for numeric estimasi biaya cat -->
                    <input type="hidden" name="estimasi_biaya_cat_numeric" id="estimasi_biaya_cat_numeric">
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-4 border-t">
                <button id="cancelCatatanBtn" type="button"
                        class="px-3 py-1.5 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:ring-2 focus:ring-gray-500 mr-2 text-sm">
                    Batal
                </button>
                <button id="submitCatatanBtn" type="button"
                        class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 text-sm">
                    <svg class="w-3 h-3 mr-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Simpan Informasi Perbaikan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Catatan Modal Functions
let lastFocusedElement = null;

function openCatatanModal(perbaikanId, nomorTagihan, estimasiKerusakan, nomorKontainer, vendorName = '', ukuranKontainer = '') {
    try {
        console.log('openCatatanModal called with:', { perbaikanId, nomorTagihan, estimasiKerusakan, nomorKontainer, vendorName, ukuranKontainer });

        // Store the currently focused element
        lastFocusedElement = document.activeElement;

        // Populate item info
        const itemInfoElement = document.getElementById('catatanItemInfo');
        if (itemInfoElement) {
            itemInfoElement.innerHTML = `
                <div>• <strong>Nomor Tagihan:</strong> ${nomorTagihan}</div>
                <div>• <strong>Nomor Kontainer:</strong> ${nomorKontainer}</div>
                <div>• <strong>Ukuran Kontainer:</strong> ${ukuranKontainer}</div>
                <div>• <strong>Estimasi Kerusakan:</strong> ${estimasiKerusakan || 'Tidak ada informasi'}</div>
                <div>• <strong>Tanggal Input:</strong> ${new Date().toLocaleDateString('id-ID')}</div>
            `;
        }

        // Set perbaikan ID
        const perbaikanIdElement = document.getElementById('catatan_perbaikan_id');
        if (perbaikanIdElement) {
            perbaikanIdElement.value = perbaikanId;
        }

        // Reset form
        const catatanForm = document.getElementById('catatanForm');
        if (catatanForm) {
            catatanForm.reset();
        }
        const tanggalCatatanElement = document.getElementById('tanggal_catatan');
        if (tanggalCatatanElement) {
            tanggalCatatanElement.value = new Date().toISOString().split('T')[0];
        }

        // Initialize rupiah formatting for estimasi biaya cat
        const estimasiBiayaCatInput = document.getElementById('estimasi_biaya_cat');
        if (estimasiBiayaCatInput) {
            handleRupiahInput(estimasiBiayaCatInput);
        }

        // Auto-generate nomor tagihan cat
        const nomorTagihanCat = generateTagihanCatNumber();
        const nomorTagihanCatElement = document.getElementById('nomor_tagihan_cat');
        if (nomorTagihanCatElement) {
            nomorTagihanCatElement.value = nomorTagihanCat;
        }

        // Update format preview
        const previewFormat = previewTagihanCatFormat();
        const previewElement = document.getElementById('tagihanCatFormatPreview');
        if (previewElement) {
            previewElement.textContent = `Contoh: ${previewFormat}`;
        }

        // Set nomor kontainer
        const nomorKontainerElement = document.getElementById('nomor_kontainer');
        if (nomorKontainerElement) {
            nomorKontainerElement.value = nomorKontainer;
        }

        // Store ukuran kontainer for tarif calculation
        window.currentUkuranKontainer = ukuranKontainer;
        console.log('Setting currentUkuranKontainer to:', ukuranKontainer);

        // Set vendor if available
        if (vendorName) {
            const vendorSelect = document.getElementById('teknisi');
            if (vendorSelect) {
                // Try to find and select the matching vendor option
                for (let i = 0; i < vendorSelect.options.length; i++) {
                    if (vendorSelect.options[i].value === vendorName) {
                        vendorSelect.selectedIndex = i;
                        break;
                    }
                }
            }
        }

        // Add event listeners for auto-fill functionality (remove existing first)
        const vendorSelect = document.getElementById('teknisi');
        const statusSelect = document.getElementById('status_perbaikan');

        if (vendorSelect && statusSelect) {
            // Remove existing listeners to prevent duplicates
            vendorSelect.removeEventListener('change', updateEstimasiBiayaCat);
            statusSelect.removeEventListener('change', updateEstimasiBiayaCat);

            // Add new listeners
            vendorSelect.addEventListener('change', updateEstimasiBiayaCat);
            statusSelect.addEventListener('change', updateEstimasiBiayaCat);

            // Trigger initial update if both are selected
            updateEstimasiBiayaCat();
        }

        // Disable sidebar interaction
        disableSidebarInteraction();

        // Show modal
        const modal = document.getElementById('catatanModal');
        if (modal) {
            modal.classList.remove('hidden');
            console.log('Modal opened successfully');
        } else {
            console.error('Modal element not found');
        }

        // Focus on the first input in the modal
        setTimeout(() => {
            const firstInput = document.getElementById('catatan_text');
            if (firstInput) {
                firstInput.focus();
            }
        }, 100);

    } catch (error) {
        console.error('Error in openCatatanModal:', error);
        alert('Terjadi kesalahan saat membuka modal: ' + error.message);
    }
}

// Catatan Modal Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    const catatanModal = document.getElementById('catatanModal');
    const closeCatatanModal = document.getElementById('closeCatatanModal');
    const cancelCatatanBtn = document.getElementById('cancelCatatanBtn');
    const submitCatatanBtn = document.getElementById('submitCatatanBtn');
    const catatanForm = document.getElementById('catatanForm');

    function closeModal() {
        catatanModal.classList.add('hidden');
        // Restore focus to the last focused element
        if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
        // Enable sidebar interaction
        enableSidebarInteraction();
    }

    // Close modal
    closeCatatanModal.addEventListener('click', closeModal);

    cancelCatatanBtn.addEventListener('click', closeModal);

    // Close modal when clicking outside
    catatanModal.addEventListener('click', function(e) {
        if (e.target === catatanModal) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !catatanModal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Submit catatan form
    submitCatatanBtn.addEventListener('click', function() {
        const nomorTagihanCat = document.getElementById('nomor_tagihan_cat').value.trim();
        const teknisi = document.getElementById('teknisi').value.trim();
        const catatan = document.getElementById('catatan_text').value.trim();

        if (!nomorTagihanCat) {
            alert('Nomor tagihan cat belum ter-generate!');
            return;
        }

        if (!teknisi) {
            alert('Vendor/Bengkel harus dipilih!');
            document.getElementById('teknisi').focus();
            return;
        }

        if (!catatan) {
            alert('Catatan harus diisi!');
            document.getElementById('catatan_text').focus();
            return;
        }

        // Convert rupiah format to number for form submission
        const estimasiBiayaCatNumeric = rupiahToNumber(document.getElementById('estimasi_biaya_cat').value);
        document.getElementById('estimasi_biaya_cat_numeric').value = estimasiBiayaCatNumeric;

        // Submit form
        catatanForm.submit();
    });
});
</script>
