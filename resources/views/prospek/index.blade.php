@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center">
            <i class="fas fa-shipping-fast mr-3 text-blue-600 text-2xl"></i>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Prospek</h1>
                <p class="text-gray-600">Daftar prospek pengiriman kontainer</p>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('prospek.index') }}">
            {{-- Preserve current per_page selection if present so filter submissions don't reset rows-per-page --}} 
            @if(request()->has('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nama supir, barang, pengirim..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="sudah_muat" {{ request('status') == 'sudah_muat' ? 'selected' : '' }}>Sudah Muat</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>

                {{-- Tipe Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontainer</label>
                    <select name="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tipe</option>
                        <option value="FCL" {{ request('tipe') == 'FCL' ? 'selected' : '' }}>FCL</option>
                        <option value="LCL" {{ request('tipe') == 'LCL' ? 'selected' : '' }}>LCL</option>
                        <option value="CARGO" {{ request('tipe') == 'CARGO' ? 'selected' : '' }}>CARGO</option>
                    </select>
                </div>

                {{-- Ukuran Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran</label>
                    <select name="ukuran" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Ukuran</option>
                        <option value="20" {{ request('ukuran') == '20' ? 'selected' : '' }}>20 Feet</option>
                        <option value="40" {{ request('ukuran') == '40' ? 'selected' : '' }}>40 Feet</option>
                    </select>
                </div>

                {{-- Tujuan Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan</label>
                    <input type="text"
                           name="tujuan"
                           value="{{ request('tujuan') }}"
                           placeholder="Tujuan pengiriman..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex justify-between items-center mt-4">
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('prospek.export-excel', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </a>
                    <a href="{{ route('prospek.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Reset
                    </a>
                </div>
                
                {{-- Tombol Naik Kapal untuk prospek aktif --}}
                <div class="flex gap-2">
                    <a href="{{ route('prospek.pilih-tujuan') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-ship mr-2"></i>
                        Naik Kapal
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
                <table class="min-w-full table-auto resizable-table" id="prospekTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">No<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">No. Surat Jalan<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tanggal<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tanggal Checkpoint<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Nama Supir<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Barang<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">PT/Pengirim<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tipe<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Ukuran<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">No. Kontainer<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">No. Seal<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">No. Seal<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tujuan<div class="resize-handle"></div></th>
                        <th class="resizable-th px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Status<div class="resize-handle"></div></th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($prospeks as $key => $prospek)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospeks->firstItem() + $key }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                {{ $prospek->no_surat_jalan ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->tanggal ? (is_string($prospek->tanggal) ? \Carbon\Carbon::parse($prospek->tanggal)->format('d/m/Y') : $prospek->tanggal->format('d/m/Y')) : '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($prospek->suratJalan && $prospek->suratJalan->tanggal_checkpoint)
                                    {{ is_string($prospek->suratJalan->tanggal_checkpoint) ? \Carbon\Carbon::parse($prospek->suratJalan->tanggal_checkpoint)->format('d/m/Y') : $prospek->suratJalan->tanggal_checkpoint->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->nama_supir ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->barang ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->pt_pengirim ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($prospek->tipe)
                                    @php
                                        $tipeUpper = strtoupper($prospek->tipe);
                                        $tipeConfig = [
                                            'FCL' => ['color' => 'bg-purple-100 text-purple-800', 'icon' => 'fa-shipping-fast'],
                                            'LCL' => ['color' => 'bg-orange-100 text-orange-800', 'icon' => 'fa-box'],
                                            'CARGO' => ['color' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-truck']
                                        ];
                                        $config = $tipeConfig[$tipeUpper] ?? ['color' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-shipping-fast'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['color'] }}">
                                        <i class="fas {{ $config['icon'] }} mr-1"></i>
                                        {{ $tipeUpper }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($prospek->ukuran)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $prospek->ukuran == '20' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $prospek->ukuran }} Feet
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                {{ $prospek->nomor_kontainer ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                {{ $prospek->no_seal ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                @can('prospek-edit')
                                    <div class="seal-edit-container" data-prospek-id="{{ $prospek->id }}">
                                        <span class="seal-display cursor-pointer hover:bg-gray-100 px-2 py-1 rounded border-2 border-transparent hover:border-blue-300 transition-all duration-200" 
                                              title="Klik untuk edit seal">
                                            {{ $prospek->no_seal ?? 'Klik untuk edit' }}
                                        </span>
                                        <input type="text" 
                                               class="seal-input hidden w-full px-2 py-1 border border-blue-500 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                               value="{{ $prospek->no_seal ?? '' }}"
                                               placeholder="Masukkan nomor seal">
                                        <div class="seal-buttons mt-1 gap-1" style="display: none;">
                                            <button class="save-seal bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs transition-colors">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="cancel-seal bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs transition-colors">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <span class="font-mono">{{ $prospek->no_seal ?? '-' }}</span>
                                @endcan
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prospek->tujuan_pengiriman ?? '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php
                                    $statusConfig = [
                                        'aktif' => [
                                            'color' => 'bg-green-100 text-green-800 border-green-200',
                                            'icon' => 'fa-check-circle',
                                            'label' => 'Aktif'
                                        ],
                                        'sudah_muat' => [
                                            'color' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'icon' => 'fa-ship',
                                            'label' => 'Sudah Muat'
                                        ],
                                        'batal' => [
                                            'color' => 'bg-red-100 text-red-800 border-red-200',
                                            'icon' => 'fa-times-circle',
                                            'label' => 'Batal'
                                        ]
                                    ];
                                    $config = $statusConfig[$prospek->status] ?? [
                                        'color' => 'bg-gray-100 text-gray-800 border-gray-200',
                                        'icon' => 'fa-question-circle',
                                        'label' => $prospek->status
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $config['color'] }}">
                                    <i class="fas {{ $config['icon'] }} mr-1"></i>
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('prospek.show', $prospek->id) }}"
                                       class="text-blue-600 hover:text-blue-900 transition duration-150"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-400"></i>
                                    <p class="text-lg font-medium">Tidak ada data prospek yang ditemukan</p>
                                    <p class="text-sm text-gray-400 mt-1">Silakan tambah data prospek baru atau ubah filter pencarian</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($prospeks->hasPages())
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                @include('components.modern-pagination', ['paginator' => $prospeks])
                @include('components.rows-per-page')
            </div>
        @endif
    </div>

    {{-- Summary Cards --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total Prospek (Belum Muat) --}}
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-hourglass-half text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Prospek</p>
                    <p class="text-xs text-gray-400">Belum dimuat ke kapal</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalBelumMuat }}</p>
                </div>
            </div>
        </div>

        {{-- Sudah Muat --}}
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                    <i class="fas fa-ship text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Sudah Muat</p>
                    <p class="text-xs text-gray-400">Dimuat ke kapal</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalSudahMuat }}</p>
                </div>
            </div>
        </div>

        {{-- Batal --}}
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Batal</p>
                    <p class="text-xs text-gray-400">Tidak jadi dimuat</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalBatal }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle seal inline editing
    const sealContainers = document.querySelectorAll('.seal-edit-container');
    
    sealContainers.forEach(container => {
        const prospekId = container.dataset.prospekId;
        const sealDisplay = container.querySelector('.seal-display');
        const sealInput = container.querySelector('.seal-input');
        const sealButtons = container.querySelector('.seal-buttons');
        const saveBtn = container.querySelector('.save-seal');
        const cancelBtn = container.querySelector('.cancel-seal');
        
        let originalValue = sealInput.value;
        
        // Enter edit mode
        sealDisplay.addEventListener('click', function() {
            sealDisplay.style.display = 'none';
            sealInput.classList.remove('hidden');
            sealButtons.style.display = 'flex';
            sealInput.focus();
            sealInput.select();
            originalValue = sealInput.value;
        });
        
        // Cancel edit
        cancelBtn.addEventListener('click', function() {
            exitEditMode();
            sealInput.value = originalValue;
        });
        
        // Save edit
        saveBtn.addEventListener('click', function() {
            saveSeal(prospekId, sealInput.value, container);
        });
        
        // Save on Enter key
        sealInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                saveSeal(prospekId, sealInput.value, container);
            } else if (e.key === 'Escape') {
                exitEditMode();
                sealInput.value = originalValue;
            }
        });
        
        // Exit edit mode on blur (with delay to allow button clicks)
        sealInput.addEventListener('blur', function() {
            setTimeout(() => {
                if (!container.querySelector('.save-seal:hover') && !container.querySelector('.cancel-seal:hover')) {
                    exitEditMode();
                    sealInput.value = originalValue;
                }
            }, 100);
        });
        
        function exitEditMode() {
            sealDisplay.style.display = 'inline';
            sealInput.classList.add('hidden');
            sealButtons.style.display = 'none';
        }
    });
    
    function saveSeal(prospekId, newValue, container) {
        const saveBtn = container.querySelector('.save-seal');
        const originalText = saveBtn.innerHTML;
        
        // Show loading
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        saveBtn.disabled = true;
        
        // Prepare form data
        const formData = new FormData();
        formData.append('no_seal', newValue);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'PATCH');
        
        fetch(`/prospek/${prospekId}/update-seal`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update display
                const sealDisplay = container.querySelector('.seal-display');
                sealDisplay.textContent = data.data.no_seal || 'Klik untuk edit';
                
                // Exit edit mode
                exitEditMode(container);
                
                // Show success message
                showToast('success', 'Nomor seal berhasil diperbarui');
            } else {
                throw new Error(data.error || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', error.message || 'Terjadi kesalahan saat mengupdate seal');
            
            // Reset input value
            const sealInput = container.querySelector('.seal-input');
            sealInput.value = sealInput.dataset.originalValue || '';
        })
        .finally(() => {
            // Reset button
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        });
    }
    
    function exitEditMode(container) {
        const sealDisplay = container.querySelector('.seal-display');
        const sealInput = container.querySelector('.seal-input');
        const sealButtons = container.querySelector('.seal-buttons');
        
        sealDisplay.style.display = 'inline';
        sealInput.classList.add('hidden');
        sealButtons.style.display = 'none';
    }
    
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'
        }`;
        
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-lg leading-none hover:opacity-75">&times;</button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }
        }, 3000);
    }
});
</script>

@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('prospekTable');
});
</script>
@endpush