@extends('layouts.app')

@section('title', 'Asuransi Tanda Terima')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Asuransi Tanda Terima</h1>
                <p class="text-gray-600 mt-1">Kelola data asuransi untuk tanda terima</p>
            </div>
            <div class="flex space-x-2">
                <form id="bulkExportForm" action="{{ route('asuransi-tanda-terima.export-request') }}" method="POST" target="_blank">
                    @csrf
                    <input type="hidden" name="selected_ids" id="selectedIdsInput">
                    <button type="button" onclick="showExportModal()"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Request Layout
                    </button>
                </form>

                <a href="{{ route('asuransi-tanda-terima.export', request()->query()) }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Excel
                </a>
                @can('asuransi-tanda-terima-create')
                <a href="{{ route('asuransi-tanda-terima.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Asuransi
                </a>
                @endcan
            </div>
        </div>
        <!-- Notification -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('asuransi-tanda-terima.index') }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nomor polis, vendor..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                    Cari
                </button>
                <a href="{{ route('asuransi-tanda-terima.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                    Reset
                </a>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left">
                            <input type="checkbox" id="selectAllCheckboxes" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tipe Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Nomor & Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">No. Kontainer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center whitespace-nowrap">Kuantitas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center whitespace-nowrap">Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Pengirim / Penerima</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status Asuransi</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($receipts as $item)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-3 py-4">
                                <input type="checkbox" name="receipt_ids[]" value="{{ $item->type }}_{{ $item->id }}" class="receipt-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->type == 'tt')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Tanda Terima</span>
                                @elseif($item->type == 'tttsj')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">TT Tanpa SJ</span>
                                @elseif($item->type == 'lcl')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800">Tanda Terima LCL</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="block text-sm font-medium text-gray-900">{{ $item->number }}</span>
                                <span class="block text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</span>
                                @if($item->insurance && ($item->insurance->nama_kapal || $item->insurance->nomor_voyage))
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @if($item->insurance->nama_kapal)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                🚢 {{ $item->insurance->nama_kapal }}
                                            </span>
                                        @endif
                                        @if($item->insurance->nomor_voyage)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                V: {{ $item->insurance->nomor_voyage }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900 font-mono">{{ $item->no_kontainer ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap max-w-md truncate" title="{{ is_array(json_decode($item->nama_barang, true)) ? implode(', ', json_decode($item->nama_barang, true)) : ($item->nama_barang ?: '') }}">
                                <span class="text-sm text-gray-900">
                                    @if($item->type == 'tt' && is_string($item->nama_barang) && (str_starts_with($item->nama_barang, '[') || str_starts_with($item->nama_barang, '{')))
                                        @php
                                            $decoded = json_decode($item->nama_barang, true);
                                            echo is_array($decoded) ? implode(', ', $decoded) : ($item->nama_barang ?: '-');
                                        @endphp
                                    @else
                                        {{ $item->nama_barang ?: '-' }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="text-sm text-gray-900 font-medium">{{ $item->kuantitas ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $item->satuan ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <span class="font-medium">Dari:</span> {{ $item->pengirim }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    <span class="font-medium">Ke:</span> {{ $item->penerima }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->insurance)
                                    <div class="flex flex-col">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit">
                                            Terproteksi
                                        </span>
                                        <span class="text-xs text-gray-600 mt-1 font-medium">{{ $item->insurance->nomor_polis }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $item->insurance->vendorAsuransi->nama_asuransi }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Belum Diasuransikan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center space-x-2">
                                    @if($item->insurance)
                                        <a href="{{ route('asuransi-tanda-terima.show', $item->insurance->id) }}" title="Lihat Polis" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-1.5 rounded-lg transition duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @can('asuransi-tanda-terima-update')
                                        <a href="{{ route('asuransi-tanda-terima.edit', $item->insurance->id) }}" title="Edit Polis" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-1.5 rounded-lg transition duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @endcan
                                    @else
                                        @can('asuransi-tanda-terima-create')
                                        <a href="{{ route('asuransi-tanda-terima.create', ['type' => $item->type, 'id' => $item->id]) }}" 
                                           class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition duration-200 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Input Asuransi
                                        </a>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-lg">Tidak ada data tanda terima ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $receipts->appends(request()->query())->links() }}
        </div>
    </div>
</div>
<!-- Export Configuration Modal -->
<div id="exportModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="hideExportModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Konfigurasi Export Layout Request</h3>
                        <p class="text-xs text-blue-600 mt-1">* Vendor dan Nama Kapal diambil otomatis dari data input asuransi.</p>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Tanggal Request</label>
                            <input type="date" id="export_request_date" value="{{ date('Y-m-d') }}" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="performBulkExport()" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-200">
                    Generate Export
                </button>
                <button type="button" onclick="hideExportModal()" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-200">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('selectAllCheckboxes').addEventListener('change', function() {
        document.querySelectorAll('.receipt-checkbox').forEach(cb => cb.checked = this.checked);
    });

    function showExportModal() {
        const selected = Array.from(document.querySelectorAll('.receipt-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Silakan pilih minimal satu data tanda terima.');
            return;
        }
        document.getElementById('selectedIdsInput').value = JSON.stringify(selected);
        document.getElementById('exportModal').classList.remove('hidden');
    }

    function hideExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    function performBulkExport() {
        const form = document.getElementById('bulkExportForm');
        const reqDate = document.getElementById('export_request_date').value;

        // Cleanup old dynamics if any
        form.querySelectorAll('.dynamic-input').forEach(el => el.remove());

        const dInput = document.createElement('input');
        dInput.type = 'hidden'; 
        dInput.name = 'request_date'; 
        dInput.value = reqDate; 
        dInput.className = 'dynamic-input';
        form.appendChild(dInput);

        form.submit();
        hideExportModal();
    }
</script>
@endsection
