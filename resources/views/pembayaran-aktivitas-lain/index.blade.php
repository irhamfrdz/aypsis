@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-800">Pembayaran Aktivitas Lain</h1>
            @can('pembayaran-aktivitas-lain-create')
                <button onclick="openPaymentMethodModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Baru
                </button>
            @endcan
            @can('pembayaran-aktivitas-lain-print')
                <button onclick="openPrintWindow()" class="ml-3 inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18H4a2 2 0 01-2-2V9a2 2 0 012-2h16a2 2 0 012 2v7a2 2 0 01-2 2h-2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 14h12v7H6z"/>
                    </svg>
                    Print Tabel
                </button>
            @endcan
        </div>

        <!-- Filter Section -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('pembayaran-aktivitas-lain.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipe Pembayaran</label>
                    <select name="tipe_pembayaran" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="semua" {{ request('tipe_pembayaran', 'semua') == 'semua' ? 'selected' : '' }}>Semua</option>
                        <option value="langsung" {{ request('tipe_pembayaran') == 'langsung' ? 'selected' : '' }}>Bayar Langsung</option>
                        <option value="invoice" {{ request('tipe_pembayaran') == 'invoice' ? 'selected' : '' }}>Dari Invoice</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor, nomor accurate, jenis, keterangan..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition">
                        Filter
                    </button>
                    <a href="{{ route('pembayaran-aktivitas-lain.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider w-12">No</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider w-20">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nomor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nomor Accurate</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider w-24">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Jenis Aktivitas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Penerima</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pembayarans as $index => $item)
                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-900">{{ $pembayarans->firstItem() + $index }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($item->tipe_pembayaran === 'invoice')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-md bg-purple-100 text-purple-800">Invoice</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-md bg-blue-100 text-blue-800">Langsung</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->nomor }}</div>
                                @if($item->sub_jenis_kendaraan)
                                    <div class="text-xs text-gray-500">{{ $item->sub_jenis_kendaraan }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item->nomor_accurate ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-900">{{ $item->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900">{{ $item->jenis_aktivitas }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900">
                                    @php
                                        $penerimaList = explode(',', $item->penerima);
                                        $penerimaCount = count($penerimaList);
                                    @endphp
                                    @if($penerimaCount > 3)
                                        <span class="font-medium">{{ trim($penerimaList[0]) }}</span>
                                        <span class="text-xs text-gray-500"> +{{ $penerimaCount - 1 }} lainnya</span>
                                    @else
                                        {{ $item->penerima }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="flex justify-center gap-2">
                                    @can('pembayaran-aktivitas-lain-view')
                                        <a href="{{ route('pembayaran-aktivitas-lain.show', $item) }}" class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-1 rounded transition-colors" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('pembayaran-aktivitas-lain-view')
                                        <a href="{{ route('pembayaran-aktivitas-lain.print', $item) }}" class="text-green-600 hover:text-green-800 hover:bg-green-50 p-1 rounded transition-colors" title="Print" target="_blank">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('pembayaran-aktivitas-lain-update')
                                        <a href="{{ route('pembayaran-aktivitas-lain.edit', $item) }}" class="text-amber-600 hover:text-amber-800 hover:bg-amber-50 p-1 rounded transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('pembayaran-aktivitas-lain-delete')
                                        <button onclick="confirmDelete({{ $item->id }})" class="text-red-600 hover:text-red-800 hover:bg-red-50 p-1 rounded transition-colors" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-sm font-medium">Tidak ada data pembayaran</p>
                                    <p class="text-xs text-gray-400 mt-1">Gunakan filter atau tambahkan data baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pembayarans->links() }}
        </div>
    </div>
</div>

<!-- Payment Method Selection Modal -->
<div id="paymentMethodModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4 text-center">Pilih Metode Pembayaran</h3>
            <div class="mt-4 px-4 py-3 space-y-3">
                <button onclick="navigateToPaymentMethod('invoice')" class="w-full px-4 py-3 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Bayar Menggunakan Invoice
                </button>
                <button onclick="navigateToPaymentMethod('direct')" class="w-full px-4 py-3 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Bayar Langsung
                </button>
            </div>
            <div class="px-4 py-3 mt-2">
                <button onclick="closePaymentMethodModal()" class="w-full px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 transition">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Hapus Data</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus data ini?</p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-700">
                    Hapus
                </button>
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-24 hover:bg-gray-400">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let deleteId = null;

function openPaymentMethodModal() {
    document.getElementById('paymentMethodModal').classList.remove('hidden');
}

function closePaymentMethodModal() {
    document.getElementById('paymentMethodModal').classList.add('hidden');
}

function navigateToPaymentMethod(method) {
    if (method === 'invoice') {
        window.location.href = '{{ route('pembayaran-aktivitas-lain.create') }}?method=invoice';
    } else if (method === 'direct') {
        window.location.href = '{{ route('pembayaran-aktivitas-lain.create') }}?method=direct';
    }
}

function confirmDelete(id) {
    deleteId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    deleteId = null;
    document.getElementById('deleteModal').classList.add('hidden');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/pembayaran-aktivitas-lain/${deleteId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
});

function openPrintWindow() {
    const params = new URLSearchParams(window.location.search);
    window.open(`{{ route('pembayaran-aktivitas-lain.print.index') }}?${params.toString()}`, '_blank');
}
</script>
@endpush
@endsection
