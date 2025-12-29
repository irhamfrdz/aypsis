@extends('layouts.app')

@section('title', 'Pergerakan Kontainer')
@section('page_title', 'Pergerakan Kontainer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Pergerakan Kontainer</h1>
        <p class="text-gray-600 mt-2">Kelola dan pantau pergerakan kontainer dari berbagai gudang</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Kontainer</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($totalKontainers) }}</h3>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Stock Kontainer</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($totalStockKontainers) }}</h3>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Gudang</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($gudangs->count()) }}</h3>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Total Semua</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($totalAll) }}</h3>
                </div>
                <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('pergerakan-kontainer.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nomor kontainer..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                <select name="gudang" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Lokasi</option>
                    @foreach($gudangs as $gudang)
                        <option value="{{ $gudang->id }}" {{ request('gudang') == $gudang->id ? 'selected' : '' }}>
                            {{ $gudang->nama_gudang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('pergerakan-kontainer.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-boxes mr-2"></i>Semua Kontainer ({{ $allKontainers->total() }})
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($allKontainers as $index => $kontainer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $allKontainers->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $kontainer->nomor_seri_gabungan ?? '-' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $kontainer->awalan_kontainer }} {{ $kontainer->nomor_seri_kontainer }}-{{ $kontainer->akhiran_kontainer }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $kontainer->tipe_kontainer ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $kontainer->ukuran ?? '-' }} ft
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $gudangData = null;
                                        if ($kontainer->gudangs_id) {
                                            $gudangData = $gudangs->firstWhere('id', $kontainer->gudangs_id);
                                        }
                                    @endphp
                                    @if($gudangData)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-warehouse mr-1"></i>
                                            {{ $gudangData->nama_gudang }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($kontainer->source_table == 'kontainer')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-box mr-1"></i>Kontainer Aktif
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            <i class="fas fa-warehouse mr-1"></i>Stock
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $kontainer->created_at ? \Carbon\Carbon::parse($kontainer->created_at)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openMovementModal({{ json_encode($kontainer) }})" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition duration-200">
                                        <i class="fas fa-exchange-alt mr-1.5"></i>
                                        Pergerakan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p class="text-lg font-medium">Tidak ada data kontainer</p>
                                        <p class="text-sm">Belum ada data kontainer yang tersedia</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($allKontainers->hasPages())
                <div class="mt-6">
                    {{ $allKontainers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Movement Modal -->
<div id="movementModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-exchange-alt mr-2 text-indigo-600"></i>
                    Pergerakan Kontainer
                </h3>
                <button onclick="closeMovementModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4">
                <!-- Container Info -->
                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                    <div class="text-sm">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Nomor Kontainer:</span>
                            <span id="modal_nomor_kontainer" class="font-semibold text-gray-900"></span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Tipe:</span>
                            <span id="modal_tipe" class="font-semibold text-gray-900"></span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Ukuran:</span>
                            <span id="modal_ukuran" class="font-semibold text-gray-900"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lokasi Sekarang:</span>
                            <span id="modal_lokasi_sekarang" class="font-semibold text-gray-900"></span>
                        </div>
                    </div>
                </div>

                <!-- Movement Form -->
                <form id="movementForm" method="POST" action="{{ route('pergerakan-kontainer.store') }}">
                    @csrf
                    <input type="hidden" id="kontainer_id" name="kontainer_id">
                    <input type="hidden" id="source_table" name="source_table">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Lokasi Tujuan <span class="text-red-500">*</span>
                        </label>
                        <select name="gudang_tujuan_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Pilih Lokasi Tujuan</option>
                            @foreach($gudangs as $gudang)
                                <option value="{{ $gudang->id }}">{{ $gudang->nama_gudang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Pergerakan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_pergerakan" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan
                        </label>
                        <textarea name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Catatan pergerakan (opsional)"></textarea>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t">
                        <button type="button" onclick="closeMovementModal()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-200">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition duration-200">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Removed tab styles since we don't use tabs anymore */
</style>
@endpush

@push('scripts')
<script>
function openMovementModal(kontainer) {
    // Set container info
    document.getElementById('modal_nomor_kontainer').textContent = kontainer.nomor_seri_gabungan || '-';
    document.getElementById('modal_tipe').textContent = kontainer.tipe_kontainer || '-';
    document.getElementById('modal_ukuran').textContent = (kontainer.ukuran || '-') + ' ft';
    
    // Find and set current location
    const gudangs = @json($gudangs);
    const currentGudang = gudangs.find(g => g.id === kontainer.gudangs_id);
    document.getElementById('modal_lokasi_sekarang').textContent = currentGudang ? currentGudang.nama_gudang : 'Belum ditentukan';
    
    // Set hidden fields
    document.getElementById('kontainer_id').value = kontainer.id;
    document.getElementById('source_table').value = kontainer.source_table;
    
    // Show modal
    document.getElementById('movementModal').classList.remove('hidden');
}

function closeMovementModal() {
    document.getElementById('movementModal').classList.add('hidden');
    document.getElementById('movementForm').reset();
}

// Close modal when clicking outside
document.getElementById('movementModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMovementModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMovementModal();
    }
});
</script>
@endpush
@endsection
