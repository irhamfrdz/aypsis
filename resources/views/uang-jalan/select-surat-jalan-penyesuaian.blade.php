@extends('layouts.app')

@php
    $routePrefix = 'uang-jalan';
    $isBongkaran = false;
    $pageTitleText = 'Penyesuaian Uang Jalan';
    $selectRouteName = 'uang-jalan.select-surat-jalan-penyesuaian';
    $indexRouteName = $routePrefix . '.index';
    $createRouteName = $routePrefix . '.create-penyesuaian';
    $suratJalanQueryParam = 'surat_jalan_id';
    $statusOptions = $statusOptions ?? ['all' => 'Semua Status'];
    $status = $status ?? 'all';
    $suratJalans = $suratJalans ?? collect([]);
    $noField = 'no_surat_jalan';
@endphp

@section('page_title', 'Tambah Penyesuaian Uang Jalan')

@section('content')
<div class="min-h-screen bg-gray-50 py-4">
    <div class="max-w-5xl mx-auto px-3 sm:px-4">
        <!-- Breadcrumb / Header Navigation -->
        <nav class="flex mb-3" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-3 h-3 mr-2.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <a href="{{ route($indexRouteName) }}" class="text-sm font-medium text-gray-500 hover:text-blue-600">Uang Jalan</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-500">{{ $pageTitleText }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Main Form Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header with Green Background -->
            <div class="bg-green-500 px-4 py-3 rounded-t-lg">
                <h1 class="text-base font-semibold text-white">Tambah Penyesuaian Uang Jalan</h1>
            </div>

            <!-- Form Content -->
            <div class="p-4">
                <!-- Info -->
                <div class="mb-3 p-2 bg-green-50 border border-green-200 rounded text-xs">
                    <strong>Info:</strong> {{ $suratJalans->total() }} surat jalan tersedia untuk penyesuaian
                </div>

                <form id="selectSuratJalanForm" method="GET">
                    <div class="mb-4">
                        <label for="no_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1">
                            No Surat Jalan <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text"
                                   id="selected_surat_jalan_display"
                                   class="flex-1 px-3 py-1.5 text-sm border border-gray-300 rounded bg-gray-50 cursor-pointer focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                   placeholder="Klik untuk memilih surat jalan"
                                   readonly
                                   onclick="openSuratJalanModal()">
                            <button type="button"
                                    onclick="openSuratJalanModal()"
                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Pilih
                            </button>
                        </div>
                        <input type="hidden" id="selected_surat_jalan_id" name="surat_jalan_id" value="">
                        <p class="mt-0.5 text-xs text-gray-500">Klik "Pilih" untuk memilih surat jalan yang akan disesuaikan</p>
                    </div>

                    <!-- Preview Information (Hidden by default) -->
                    <div id="suratJalanPreview" class="hidden mb-4 p-3 bg-green-50 border border-green-200 rounded">
                        <h3 class="text-xs font-medium text-green-900 mb-2">Detail Surat Jalan & Uang Jalan</h3>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            <div>
                                <span class="text-gray-600">Tanggal SJ:</span>
                                <div id="preview-tanggal" class="font-medium text-gray-900">-</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Supir:</span>
                                <div id="preview-supir" class="font-medium text-gray-900">-</div>
                            </div>
                            <div>
                                <span class="text-gray-600">No Plat:</span>
                                <div id="preview-plat" class="font-medium text-gray-900">-</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Uang Jalan:</span>
                                <div id="preview-uang-jalan" class="font-medium text-green-600">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route($indexRouteName) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </a>
                        <button type="button"
                                id="lanjutkanBtn"
                                onclick="lanjutkanKeForm()"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Lanjutkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Surat Jalan -->
        <div id="suratJalanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
            <div class="relative top-4 mx-auto p-4 border w-11/12 max-w-6xl shadow-lg rounded-lg bg-white max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center mb-4 pb-2 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Pilih Surat Jalan</h3>
                    <button onclick="closeSuratJalanModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Search and Filter -->
                <div class="mb-4 flex flex-wrap gap-4">
                    <div class="flex-1 min-w-64">
                        <input type="text"
                               id="modalSearch"
                               placeholder="Cari no surat jalan, supir, plat..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               value="{{ $search }}">
                    </div>
                    <div class="min-w-48">
                        <select id="modalStatusFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button onclick="applyFilters()"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filter
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">Pilih</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No SJ</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Plat</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Jalan</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($suratJalans as $suratJalan)
                                <tr class="hover:bg-gray-50 cursor-pointer"
                                    onclick="selectSuratJalan('{{ $suratJalan->id }}', '{{ $suratJalan->$noField }}', '{{ $suratJalan->jenis_surat_jalan }}', this)"
                                    data-id="{{ $suratJalan->id }}"
                                    data-no-sj="{{ $suratJalan->$noField }}"
                                    data-tanggal="{{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}"
                                    data-supir="{{ $suratJalan->supir ?? '-' }}"
                                    data-plat="{{ $suratJalan->no_plat ?? '-' }}"
                                    data-uang-jalan="{{ $suratJalan->jenis_surat_jalan === 'biasa' ? ($suratJalan->uangJalan->jumlah_total ?? 0) : ($suratJalan->uangJalanBongkaran->jumlah_total ?? 0) }}"
                                    data-jenis="{{ $suratJalan->jenis_surat_jalan }}">
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <input type="radio" name="selected_surat_jalan" value="{{ $suratJalan->id }}"
                                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $suratJalan->$noField }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                        {{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                        {{ Str::limit($suratJalan->supir ?? '-', 20) }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                        {{ $suratJalan->no_plat ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-semibold text-green-600">
                                        Rp {{ number_format($suratJalan->jenis_surat_jalan === 'biasa' ? ($suratJalan->uangJalan->jumlah_total ?? 0) : ($suratJalan->uangJalanBongkaran->jumlah_total ?? 0), 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            @if($suratJalan->status === 'approved') bg-green-100 text-green-800
                                            @elseif($suratJalan->status === 'sudah_berangkat') bg-blue-100 text-blue-800
                                            @elseif($suratJalan->status === 'belum_masuk_checkpoint') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $suratJalan->status ?? 'unknown')) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-8 text-center text-sm text-gray-500">
                                        Tidak ada surat jalan yang tersedia untuk penyesuaian
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($suratJalans->hasPages())
                    <div class="mt-4 flex justify-center">
                        {{ $suratJalans->appends(request()->query())->links() }}
                    </div>
                @endif

                <!-- Modal Actions -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                    <button onclick="closeSuratJalanModal()"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded-lg transition-colors">
                        Batal
                    </button>
                    <button onclick="confirmSelection()"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Pilih Surat Jalan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedSuratJalan = null;

function openSuratJalanModal() {
    document.getElementById('suratJalanModal').classList.remove('hidden');
}

function closeSuratJalanModal() {
    document.getElementById('suratJalanModal').classList.add('hidden');
}

function selectSuratJalan(id, noSj, jenis, row) {
    // Remove previous selection
    document.querySelectorAll('input[name="selected_surat_jalan"]').forEach(radio => {
        radio.checked = false;
    });
    document.querySelectorAll('tbody tr').forEach(tr => {
        tr.classList.remove('bg-green-50');
    });

    // Set new selection
    const radio = row.querySelector('input[name="selected_surat_jalan"]');
    radio.checked = true;
    row.classList.add('bg-green-50');

    selectedSuratJalan = {
        id: id,
        noSj: noSj,
        jenis: jenis,
        tanggal: row.dataset.tanggal,
        supir: row.dataset.supir,
        plat: row.dataset.plat,
        uangJalan: row.dataset.uangJalan
    };
}

function confirmSelection() {
    if (!selectedSuratJalan) {
        alert('Silakan pilih surat jalan terlebih dahulu');
        return;
    }

    // Update form
    document.getElementById('selected_surat_jalan_display').value = selectedSuratJalan.noSj;
    document.getElementById('selected_surat_jalan_id').value = selectedSuratJalan.id;

    // Update preview
    document.getElementById('preview-tanggal').textContent = selectedSuratJalan.tanggal;
    document.getElementById('preview-supir').textContent = selectedSuratJalan.supir;
    document.getElementById('preview-plat').textContent = selectedSuratJalan.plat;
    document.getElementById('preview-uang-jalan').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(selectedSuratJalan.uangJalan);

    // Show preview
    document.getElementById('suratJalanPreview').classList.remove('hidden');

    // Enable lanjutkan button
    document.getElementById('lanjutkanBtn').disabled = false;

    closeSuratJalanModal();
}

function lanjutkanKeForm() {
    const suratJalanId = document.getElementById('selected_surat_jalan_id').value;
    if (!suratJalanId) {
        alert('Silakan pilih surat jalan terlebih dahulu');
        return;
    }

    // Redirect to create penyesuaian form with surat jalan id
    window.location.href = '{{ route($createRouteName) }}?' + new URLSearchParams({
        '{{ $suratJalanQueryParam }}': suratJalanId
    });
}

function applyFilters() {
    const search = document.getElementById('modalSearch').value;
    const status = document.getElementById('modalStatusFilter').value;

    const url = new URL(window.location);
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    if (status && status !== 'all') url.searchParams.set('status', status);
    else url.searchParams.delete('status');

    window.location.href = url.toString();
}

// Close modal when clicking outside
document.getElementById('suratJalanModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSuratJalanModal();
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's already a selected surat jalan from URL params
    const urlParams = new URLSearchParams(window.location.search);
    const suratJalanId = urlParams.get('{{ $suratJalanQueryParam }}');

    if (suratJalanId) {
        // Find and select the surat jalan in the table
        const row = document.querySelector(`tr[data-id="${suratJalanId}"]`);
        if (row) {
            selectSuratJalan(suratJalanId, row.dataset.noSj, row.dataset.jenis, row);
            confirmSelection();
        }
    }
});
</script>
@endpush