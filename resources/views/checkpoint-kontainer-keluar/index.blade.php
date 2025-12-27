@extends('layouts.app')

@section('title', 'Checkpoint Kontainer Keluar')
@section('page_title', 'Checkpoint Kontainer Keluar')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Checkpoint Kontainer Keluar</h1>
                    <p class="text-xs text-gray-600 mt-1">Catat waktu keluar kontainer dari lokasi</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-center">
                        <div class="text-lg font-semibold text-orange-600">{{ $stats['total_pending'] ?? 0 }}</div>
                        <div class="text-gray-500 text-xs">Menunggu Keluar</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-semibold text-green-600">{{ $stats['total_keluar_hari_ini'] ?? 0 }}</div>
                        <div class="text-gray-500 text-xs">Keluar Hari Ini</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-semibold text-blue-600">{{ $stats['total_keluar_bulan_ini'] ?? 0 }}</div>
                        <div class="text-gray-500 text-xs">Keluar Bulan Ini</div>
                    </div>
                    <a href="{{ route('checkpoint-kontainer-keluar.history') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Riwayat Keluar
                    </a>
                </div>
            </div>

            <!-- Alerts -->
            <div class="p-4">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium text-sm">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium text-sm">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Filter Form -->
                <form method="GET" action="{{ route('checkpoint-kontainer-keluar.index') }}" class="mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="No. SJ, Kontainer, Supir..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Keluar</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari
                            </button>
                            <a href="{{ route('checkpoint-kontainer-keluar.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Bulk Action Bar -->
                <div id="bulkActionBar" class="mb-4 p-4 bg-gradient-to-r from-orange-50 to-yellow-50 border-2 border-orange-300 rounded-lg shadow-sm" style="display: none;">
                    <form id="bulkKeluarForm" method="POST" action="{{ route('checkpoint-kontainer-keluar.bulk-keluar') }}">
                        @csrf
                        <div id="selectedIdsContainer"></div>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">
                                    <span id="selectedCount" class="font-bold text-orange-600">0</span> kontainer dipilih
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-2 items-center">
                                <input type="text" name="catatan_keluar" placeholder="Catatan (opsional)" 
                                       class="px-3 py-2 border border-gray-300 rounded-md text-sm w-48">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Proses Keluar Semua
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-orange-600">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Plat</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suratJalans as $sj)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4">
                                    <input type="checkbox" class="item-checkbox rounded border-gray-300 text-orange-600" value="{{ $sj->id }}">
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-medium text-blue-600">{{ $sj->no_surat_jalan }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $sj->tanggal_surat_jalan ? \Carbon\Carbon::parse($sj->tanggal_surat_jalan)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                        {{ $sj->no_kontainer ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $sj->supir ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $sj->no_plat ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $sj->tujuan_pengiriman ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($sj->status_checkpoint_keluar == 'sudah_keluar')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                            Sudah Keluar
                                        </span>
                                    @elseif($sj->status_checkpoint_keluar == 'pending')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                            Pending
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">
                                            Belum Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <button type="button" onclick="showKeluarModal({{ $sj->id }}, '{{ $sj->no_kontainer }}', '{{ $sj->no_surat_jalan }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs rounded-md hover:bg-green-700 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Proses Keluar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-sm">Tidak ada kontainer yang menunggu keluar</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($suratJalans->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $suratJalans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Proses Keluar -->
<div id="keluarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4 border-b pb-3">
                <h3 class="text-lg font-medium text-gray-900">Proses Checkpoint Keluar</h3>
                <button type="button" onclick="closeKeluarModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="keluarForm" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan</label>
                    <div id="modal_no_surat_jalan" class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm text-gray-700 font-medium"></div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Kontainer</label>
                    <div id="modal_no_kontainer" class="px-3 py-2 bg-blue-50 border border-blue-300 rounded-md text-sm text-blue-700 font-medium"></div>
                </div>

                <div class="mb-4">
                    <label for="catatan_keluar" class="block text-sm font-medium text-gray-700 mb-1">
                        Catatan (Opsional)
                    </label>
                    <textarea name="catatan_keluar" id="catatan_keluar" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-md p-3 mb-4">
                    <p class="text-xs text-green-800">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Kontainer akan dicatat keluar dengan waktu saat ini: <strong id="currentTime"></strong>
                    </p>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeKeluarModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Proses Keluar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const bulkActionBar = document.getElementById('bulkActionBar');
    const selectedCount = document.getElementById('selectedCount');
    const selectedIdsContainer = document.getElementById('selectedIdsContainer');

    // Select All functionality
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActionBar();
    });

    // Individual checkbox
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateSelectAllState();
            updateBulkActionBar();
        });
    });

    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            selectAll.indeterminate = false;
            selectAll.checked = false;
        } else if (checkedBoxes.length === checkboxes.length) {
            selectAll.indeterminate = false;
            selectAll.checked = true;
        } else {
            selectAll.indeterminate = true;
        }
    }

    function updateBulkActionBar() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        selectedCount.textContent = checkedBoxes.length;

        // Update hidden inputs
        selectedIdsContainer.innerHTML = '';
        checkedBoxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'surat_jalan_ids[]';
            input.value = cb.value;
            selectedIdsContainer.appendChild(input);
        });

        bulkActionBar.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
    }
});

function showKeluarModal(id, noKontainer, noSuratJalan) {
    document.getElementById('modal_no_surat_jalan').textContent = noSuratJalan;
    document.getElementById('modal_no_kontainer').textContent = noKontainer;
    document.getElementById('keluarForm').action = `/checkpoint-kontainer-keluar/${id}/keluar`;
    document.getElementById('currentTime').textContent = new Date().toLocaleString('id-ID');
    document.getElementById('keluarModal').classList.remove('hidden');
}

function closeKeluarModal() {
    document.getElementById('keluarModal').classList.add('hidden');
    document.getElementById('keluarForm').reset();
}

// Update current time every second
setInterval(function() {
    const timeElement = document.getElementById('currentTime');
    if (timeElement) {
        timeElement.textContent = new Date().toLocaleString('id-ID');
    }
}, 1000);

// Close modal on outside click
document.getElementById('keluarModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeKeluarModal();
    }
});
</script>
@endpush
@endsection
