@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-ship mr-3 text-purple-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Naik Kapal</h1>
                    <p class="text-gray-600">Daftar kontainer yang naik kapal</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('naik-kapal.select') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Info Panel --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-ship text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        @php
                            $kapal = \App\Models\MasterKapal::find(request('kapal_id'));
                        @endphp
                        {{ $kapal ? $kapal->nama_kapal : 'Kapal' }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        Voyage: <span class="font-medium text-purple-600">{{ request('no_voyage') }}</span>
                        @if(request('status_filter'))
                            | Filter: <span class="font-medium">{{ request('status_filter') === 'sudah_bl' ? 'Sudah BL' : 'Belum BL' }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('naik-kapal.select') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Ganti Kapal/Voyage
                </a>
                <button type="button" id="btnPrint" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>
                <button type="button" id="btnExportExcel" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Excel
                </button>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
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

    {{-- Bulk Actions --}}
    <div id="bulkActionsPanel" class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4 hidden">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-purple-600 mr-2"></i>
                <span id="selectedCountText" class="text-sm font-medium text-purple-800">0 item dipilih</span>
            </div>
            <div class="flex gap-3">
                <button type="button" id="btnMasukkanKeBls" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-file-alt mr-2"></i>
                    Masukkan ke BLS
                </button>
                <button type="button" id="btnTidakNaikKapal" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-times-circle mr-2"></i>
                    Tidak Naik Kapal
                </button>
                <button type="button" id="btnClearSelection" class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-2 rounded-md transition duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 resizable-table" id="naikKapalTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        </th>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Kontainer<div class="resize-handle"></div></th>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Barang<div class="resize-handle"></div></th>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tipe Kontainer<div class="resize-handle"></div></th>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Kapal & Voyage<div class="resize-handle"></div></th>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Volume & Tonase<div class="resize-handle"></div></th>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Tanggal Muat<div class="resize-handle"></div></th>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Status<div class="resize-handle"></div></th>
                        <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">Prospek<div class="resize-handle"></div></th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($naikKapals as $naikKapal)
                        <tr class="hover:bg-gray-50 {{ $naikKapal->status === 'Moved to BLS' ? 'bg-green-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="checkbox" name="selected_items[]" value="{{ $naikKapal->id }}" class="item-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $naikKapal->nomor_kontainer }}</div>
                                <div class="text-sm text-gray-500">{{ $naikKapal->ukuran_kontainer }}</div>
                                @if($naikKapal->no_seal)
                                    <div class="text-xs text-blue-600">Seal: {{ $naikKapal->no_seal }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $naikKapal->jenis_barang ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $naikKapal->tipe_kontainer }}</div>
                                @if($naikKapal->tipe_kontainer_detail)
                                    <div class="text-sm text-gray-500">{{ $naikKapal->tipe_kontainer_detail }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $naikKapal->nama_kapal }}</div>
                                @if($naikKapal->no_voyage)
                                    <div class="text-sm text-gray-500">Voyage: {{ $naikKapal->no_voyage }}</div>
                                @endif
                                @if($naikKapal->pelabuhan_tujuan)
                                    <div class="text-xs text-green-600">→ {{ $naikKapal->pelabuhan_tujuan }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-blue-600">
                                    <i class="fas fa-cube mr-1"></i>
                                    {{ $naikKapal->formatted_volume }} m³
                                </div>
                                <div class="text-sm text-green-600">
                                    <i class="fas fa-weight-hanging mr-1"></i>
                                    {{ $naikKapal->formatted_tonase }} Ton
                                </div>
                                @if($naikKapal->kuantitas)
                                    <div class="text-xs text-gray-500">Qty: {{ number_format($naikKapal->kuantitas) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($naikKapal->tanggal_muat)
                                    <div class="text-sm text-gray-900">{{ $naikKapal->tanggal_muat_formatted }}</div>
                                    @if($naikKapal->jam_muat)
                                        <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($naikKapal->jam_muat)->format('H:i') }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($naikKapal->status === 'Moved to BLS')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Sudah BL
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Belum BL
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($naikKapal->prospek)
                                    <div class="text-sm text-gray-900">{{ $naikKapal->prospek->nama_supir }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $naikKapal->prospek->id }}</div>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('naik-kapal.show', $naikKapal) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('naik-kapal.edit', $naikKapal) }}" class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('naik-kapal.destroy', $naikKapal) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus data naik kapal ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-ship text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg">Belum ada data naik kapal</p>
                                <p class="text-sm">Data naik kapal akan muncul ketika tersedia</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($naikKapals->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                @include('components.modern-pagination', ['paginator' => $naikKapals])
                @include('components.rows-per-page')
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Safety guard: if the page doesn't have the naik-kapal table, skip the script
    const naikKapalTable = document.getElementById('naikKapalTable');
    if (!naikKapalTable) return;

    // Checkbox functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');

    // Select/deselect all functionality
    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    // Individual checkbox change
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateSelectedCount();
        });
    });

    function updateSelectAllState() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        const totalCount = itemCheckboxes.length;
        
        if (checkedCount === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCount === totalCount) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        const bulkActionsPanel = document.getElementById('bulkActionsPanel');
        const selectedCountText = document.getElementById('selectedCountText');
        
        if (checkedCount > 0) {
            bulkActionsPanel.classList.remove('hidden');
            selectedCountText.textContent = `${checkedCount} item dipilih`;
        } else {
            bulkActionsPanel.classList.add('hidden');
        }
    }

    // Print functionality
    document.getElementById('btnPrint').addEventListener('click', function() {
        // Hide elements that shouldn't be printed
        const elementsToHide = [
            document.getElementById('bulkActionsPanel'),
            ...document.querySelectorAll('.flex.gap-3'), // Hide action buttons
            ...document.querySelectorAll('th:last-child'), // Hide Aksi column header
            ...document.querySelectorAll('td:last-child'), // Hide Aksi column data
            ...document.querySelectorAll('input[type="checkbox"]') // Hide checkboxes
        ];
        
        elementsToHide.forEach(el => {
            if (el) el.style.display = 'none';
        });
        
        // Add print-specific styles
        const printStyle = document.createElement('style');
        printStyle.id = 'print-style';
        printStyle.innerHTML = `
            @media print {
                body * {
                    visibility: hidden;
                }
                .container, .container * {
                    visibility: visible;
                }
                .container {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }
                table {
                    page-break-inside: auto;
                }
                tr {
                    page-break-inside: avoid;
                    page-break-after: auto;
                }
                .shadow-sm {
                    box-shadow: none !important;
                }
            }
        `;
        document.head.appendChild(printStyle);
        
        // Trigger print
        window.print();
        
        // Restore hidden elements after print
        setTimeout(() => {
            elementsToHide.forEach(el => {
                if (el) el.style.display = '';
            });
            document.getElementById('print-style').remove();
        }, 100);
    });

    // Export Excel functionality
    document.getElementById('btnExportExcel').addEventListener('click', function() {
        const kapalId = "{{ request('kapal_id') }}";
        const noVoyage = "{{ request('no_voyage') }}";
        const statusFilter = "{{ request('status_filter') }}";
        
        if (!kapalId || !noVoyage) {
            alert('Silakan pilih kapal dan voyage terlebih dahulu');
            return;
        }
        
        // Change button state
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengunduh...';
        this.disabled = true;
        
        // Build export URL with parameters
        let exportUrl = "{{ route('naik-kapal.export') }}?kapal_id=" + kapalId + "&no_voyage=" + noVoyage;
        if (statusFilter) {
            exportUrl += "&status_filter=" + statusFilter;
        }
        
        // Create temporary link and trigger download
        const link = document.createElement('a');
        link.href = exportUrl;
        link.download = 'naik-kapal-' + Date.now() + '.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Reset button state after delay
        setTimeout(() => {
            this.innerHTML = originalText;
            this.disabled = false;
        }, 2000);
    });

    // Bulk action button events
    document.getElementById('btnMasukkanKeBls').addEventListener('click', function() {
        const selectedIds = getSelectedIds();
        if (selectedIds.length === 0) return;
        
        if (confirm(`Yakin ingin memasukkan ${selectedIds.length} data ke BLS?`)) {
            processBulkAction('masukkan_ke_bls', selectedIds);
        }
    });

    document.getElementById('btnTidakNaikKapal').addEventListener('click', function() {
        const selectedIds = getSelectedIds();
        
        if (confirm(`Yakin ingin menandai ${selectedIds.length} data sebagai tidak naik kapal?`)) {
            processBulkAction('tidak_naik_kapal', selectedIds);
        }
    });

    document.getElementById('btnClearSelection').addEventListener('click', function() {
        selectAllCheckbox.checked = false;
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedCount();
    });

    function getSelectedIds() {
        const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
        return Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
    }

    function processBulkAction(action, selectedIds) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('action', action);
        formData.append('selected_ids', JSON.stringify(selectedIds));

        fetch('{{ route("naik-kapal.bulk-action") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast('success', data.message);
                // Reload page to reflect changes
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast('error', data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Terjadi kesalahan saat memproses data');
        });
    }

    function showToast(type, message) {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center`;
        toast.innerHTML = `
            <i class="fas ${icon} mr-2"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
});
</script>

@endsection