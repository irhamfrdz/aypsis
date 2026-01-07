@extends('layouts.app')

@section('title', 'Manifest - ' . $namaKapal . ' - ' . $noVoyage)
@section('page_title', 'Manifest')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section with Ship Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Manifest</h1>
                    <p class="mt-1 text-sm text-gray-600">Kelola data manifest pengiriman kontainer</p>
                </div>
                @can('manifest-create')
                <div class="flex gap-2">
                    <button onclick="openImportModal()"
                       class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Import Excel
                    </button>
                    <a href="{{ route('report.manifests.create') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Manifest
                    </a>
                </div>
                @endcan
            </div>

            <!-- Ship & Voyage Info Banner -->
            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 rounded-lg p-4 text-white">
                <div class="flex flex-wrap items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            <div>
                                <div class="text-xs text-purple-100">Nama Kapal</div>
                                <div class="font-bold">{{ $namaKapal }}</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <div>
                                <div class="text-xs text-purple-100">No. Voyage</div>
                                <div class="font-bold">{{ $noVoyage }}</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div>
                                <div class="text-xs text-purple-100">Total Manifest</div>
                                <div class="font-bold">{{ $manifests->total() }} dokumen</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('report.manifests.select-ship') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Pilih Kapal Lain
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('report.manifests.index') }}">
                <!-- Hidden fields for ship and voyage -->
                <input type="hidden" name="nama_kapal" value="{{ $namaKapal }}">
                <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Pencarian -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Pencarian
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="No. BL, No. Kontainer, Nama Barang..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <!-- Tipe Kontainer -->
                    <div>
                        <label for="tipe_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer</label>
                        <select name="tipe_kontainer" id="tipe_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Semua Tipe</option>
                            <option value="Dry Container" {{ request('tipe_kontainer') == 'Dry Container' ? 'selected' : '' }}>Dry Container</option>
                            <option value="High Cube" {{ request('tipe_kontainer') == 'High Cube' ? 'selected' : '' }}>High Cube</option>
                            <option value="Reefer" {{ request('tipe_kontainer') == 'Reefer' ? 'selected' : '' }}>Reefer</option>
                        </select>
                    </div>

                    <!-- Size Kontainer -->
                    <div>
                        <label for="size_kontainer" class="block text-sm font-medium text-gray-700 mb-2">Size</label>
                        <select name="size_kontainer" id="size_kontainer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Semua Size</option>
                            <option value="20" {{ request('size_kontainer') == '20' ? 'selected' : '' }}>20'</option>
                            <option value="40" {{ request('size_kontainer') == '40' ? 'selected' : '' }}>40'</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('report.manifests.index') }}"
                       class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                        Reset
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. BL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe & Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($manifests as $index => $manifest)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ($manifests->currentPage() - 1) * $manifests->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @can('manifest-edit')
                                <div class="text-sm font-medium text-gray-900 editable-bl cursor-pointer hover:bg-yellow-50 px-2 py-1 rounded transition-colors" 
                                     contenteditable="true" 
                                     data-manifest-id="{{ $manifest->id }}"
                                     data-original-value="{{ $manifest->nomor_bl }}"
                                     title="Klik untuk edit">{{ $manifest->nomor_bl }}</div>
                                @else
                                <div class="text-sm font-medium text-gray-900">{{ $manifest->nomor_bl }}</div>
                                @endcan
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $manifest->nomor_kontainer }}</div>
                                <div class="text-xs text-gray-500">Seal: {{ $manifest->no_seal }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $manifest->tipe_kontainer }} - {{ $manifest->size_kontainer }}'
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $manifest->nama_barang }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $manifest->pengirim }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $manifest->penerima }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    @can('manifest-view')
                                    <a href="{{ route('report.manifests.show', $manifest->id) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                    @can('manifest-edit')
                                    <a href="{{ route('report.manifests.edit', $manifest->id) }}"
                                       class="text-purple-600 hover:text-purple-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                    @can('manifest-delete')
                                    <form action="{{ route('report.manifests.destroy', $manifest->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus manifest ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan manifest baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($manifests->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $manifests->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <h3 class="text-xl font-bold text-gray-900">Import Manifest</h3>
            </div>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form action="{{ route('report.manifests.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="nama_kapal" value="{{ $namaKapal }}">
            <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">

            <!-- Info Banner -->
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Format File:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>File Excel (.xlsx atau .xls)</li>
                            <li>Gunakan template yang disediakan</li>
                            <li>Maksimal 10MB</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Download Template -->
            <div class="mb-4">
                <a href="{{ route('report.manifests.download-template') }}" 
                   class="inline-flex items-center text-sm text-purple-600 hover:text-purple-800 font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Template
                </a>
            </div>

            <!-- File Upload -->
            <div class="mb-6">
                <label for="import_file" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih File Excel
                </label>
                <div class="relative">
                    <input type="file" 
                           name="file" 
                           id="import_file" 
                           accept=".xlsx,.xls"
                           required
                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <p class="mt-1 text-xs text-gray-500">File Excel dengan format .xlsx atau .xls</p>
            </div>

            <!-- Ship Info Display -->
            <div class="mb-6 p-3 bg-gray-50 rounded-lg">
                <div class="text-sm text-gray-600 space-y-1">
                    <div class="flex justify-between">
                        <span class="font-medium">Nama Kapal:</span>
                        <span class="text-gray-900">{{ $namaKapal }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">No. Voyage:</span>
                        <span class="text-gray-900">{{ $noVoyage }}</span>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2">
                <button type="button" 
                        onclick="closeImportModal()"
                        class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Import Modal Functions
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
    document.getElementById('importModal').classList.add('flex');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
    document.getElementById('importModal').classList.remove('flex');
    // Reset form
    document.getElementById('import_file').value = '';
}

// Close modal on outside click
document.getElementById('importModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeImportModal();
    }
});

// Editable BL Number functionality
document.addEventListener('DOMContentLoaded', function() {
    const editableCells = document.querySelectorAll('.editable-bl');
    
    editableCells.forEach(cell => {
        // Store original value on focus
        cell.addEventListener('focus', function() {
            this.dataset.originalValue = this.textContent.trim();
        });
        
        // Handle blur event (when user clicks away)
        cell.addEventListener('blur', function() {
            const newValue = this.textContent.trim();
            const originalValue = this.dataset.originalValue;
            const manifestId = this.dataset.manifestId;
            
            // Only update if value changed
            if (newValue !== originalValue && newValue !== '') {
                updateNomorBl(manifestId, newValue, this);
            } else if (newValue === '') {
                // Restore original if empty
                this.textContent = originalValue;
            }
        });
        
        // Handle Enter key
        cell.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.blur();
            }
            // Handle Escape key to cancel
            if (e.key === 'Escape') {
                e.preventDefault();
                this.textContent = this.dataset.originalValue;
                this.blur();
            }
        });
    });
    
    function updateNomorBl(manifestId, newValue, element) {
        // Show loading state
        element.classList.add('opacity-50');
        
        fetch(`/report/manifests/${manifestId}/update-nomor-bl`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nomor_bl: newValue
            })
        })
        .then(response => response.json())
        .then(data => {
            element.classList.remove('opacity-50');
            
            if (data.success) {
                // Update the value and original value
                element.textContent = data.nomor_bl;
                element.dataset.originalValue = data.nomor_bl;
                
                // Show success feedback
                element.classList.add('bg-green-100');
                setTimeout(() => {
                    element.classList.remove('bg-green-100');
                }, 1000);
                
                // Show toast notification
                showToast('success', data.message);
            } else {
                // Restore original value on error
                element.textContent = element.dataset.originalValue;
                showToast('error', 'Gagal memperbarui nomor BL');
            }
        })
        .catch(error => {
            element.classList.remove('opacity-50');
            element.textContent = element.dataset.originalValue;
            console.error('Error:', error);
            showToast('error', 'Terjadi kesalahan saat memperbarui');
        });
    }
    
    function showToast(type, message) {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.add('translate-x-0');
        }, 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
});
</script>
@endsection
