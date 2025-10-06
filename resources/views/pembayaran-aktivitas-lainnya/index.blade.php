@extends('layouts.app')

@section('title', 'Pembayaran Aktivitas Lain-lain')

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
    <div class="w-full">
        <div class="bg-white shadow-lg rounded-lg">
            <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-lg">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-money-bill-wave mr-3 text-blue-600"></i>
                    Pembayaran Aktivitas Lain-lain
                </h3>
                @can('pembayaran-aktivitas-lainnya-create')
                    <a href="{{ route('pembayaran-aktivitas-lainnya.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out">
                        <i class="fas fa-plus mr-2"></i> Tambah Pembayaran
                    </a>
                @endcan
            </div>

            <div class="p-6">
                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="filter_date_from" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dari:</label>
                        <input type="date" id="filter_date_from" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="filter_date_to" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Sampai:</label>
                        <input type="date" id="filter_date_to" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Search -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <div class="relative">
                            <input type="text" id="search" class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari berdasarkan nomor pembayaran atau keterangan...">
                            <button class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600" type="button" id="search_btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out" id="reset_filters">
                            <i class="fas fa-redo mr-2"></i> Reset Filter
                        </button>
                        @can('pembayaran-aktivitas-lainnya-export')
                            <button type="button" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out" id="export_excel">
                                <i class="fas fa-file-excel mr-2"></i> Export Excel
                            </button>
                        @endcan
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto shadow-sm border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="pembayaran_table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank/Kas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pembayaran as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pembayaran->firstItem() + $index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-semibold text-blue-600">{{ $item->nomor_pembayaran }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->tanggal_pembayaran->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                            {{ $item->bank->nama_akun ?? 'Bank tidak ditemukan' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-semibold text-green-600">
                                            Rp {{ number_format($item->total_pembayaran, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->is_dp)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Bayar DP
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-circle mr-1"></i>
                                                Normal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <div class="max-w-xs truncate" title="{{ $item->aktivitas_pembayaran }}">
                                            {{ Str::limit($item->aktivitas_pembayaran, 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->creator->username ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-1">
                                            @can('pembayaran-aktivitas-lainnya-view')
                                                <a href="{{ route('pembayaran-aktivitas-lainnya.show', $item) }}"
                                                   class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 transition duration-150 ease-in-out" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @can('pembayaran-aktivitas-lainnya-update')
                                                <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $item) }}"
                                                   class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-white bg-yellow-600 hover:bg-yellow-700 transition duration-150 ease-in-out" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('pembayaran-aktivitas-lainnya-print')
                                                <a href="{{ route('pembayaran-aktivitas-lainnya.print', $item) }}"
                                                   class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-white bg-gray-600 hover:bg-gray-700 transition duration-150 ease-in-out" target="_blank" title="Print">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            @endcan

                                            @can('pembayaran-aktivitas-lainnya-delete')
                                                <button type="button" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 transition duration-150 ease-in-out delete-btn"
                                                        data-id="{{ $item->id }}" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                                            <h5 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data pembayaran</h5>
                                            <p class="text-gray-500 mb-4">Belum ada pembayaran aktivitas lain-lain yang dibuat.</p>
                                            @can('pembayaran-aktivitas-lainnya-create')
                                                <a href="{{ route('pembayaran-aktivitas-lainnya.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out">
                                                    <i class="fas fa-plus mr-2"></i> Tambah Pembayaran Pertama
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-between items-center mt-6">
                    <div>
                        <span class="text-sm text-gray-500">
                            Menampilkan {{ $pembayaran->firstItem() ?? 0 }} - {{ $pembayaran->lastItem() ?? 0 }}
                            dari {{ $pembayaran->total() }} data
                        </span>
                    </div>
                    <div>
                        {{ $pembayaran->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Delete Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="deleteModal">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" id="deleteModalBackdrop"></div>
        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Konfirmasi Hapus</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" id="closeDeleteModal">
                    <span class="sr-only">Close</span>
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-2">Apakah Anda yakin ingin menghapus pembayaran ini?</p>
                <p class="text-sm text-red-600"><strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan.</p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="cancelDelete">
                    Batal
                </button>
                <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" id="confirmDelete">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom styles for smooth transitions and enhanced UX */
    .hover-scale:hover {
        transform: scale(1.02);
    }

    .table-row-hover:hover {
        background-color: #f9fafb;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let deleteId = null;

    // Delete button click
    $('.delete-btn').on('click', function() {
        deleteId = $(this).data('id');
        $('#deleteModal').removeClass('hidden');
    });

    // Modal close handlers for delete
    $('#closeDeleteModal, #cancelDelete, #deleteModalBackdrop').on('click', function() {
        $('#deleteModal').addClass('hidden');
    });

    // Confirm delete
    $('#confirmDelete').on('click', function() {
        if (deleteId) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: `/pembayaran-aktivitas-lainnya/${deleteId}`,
                method: 'DELETE',
                success: function(response) {
                    $('#deleteModal').addClass('hidden');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                }
            });
        }
    });

    // Search functionality
    $('#search_btn').on('click', function() {
        performSearch();
    });

    $('#search').on('keypress', function(e) {
        if (e.which === 13) {
            performSearch();
        }
    });

    function performSearch() {
        const search = $('#search').val();
        const dateFrom = $('#filter_date_from').val();
        const dateTo = $('#filter_date_to').val();

        let url = new URL(window.location.href);
        url.searchParams.set('search', search);
        url.searchParams.set('date_from', dateFrom);
        url.searchParams.set('date_to', dateTo);

        window.location.href = url.toString();
    }

    // Reset filters
    $('#reset_filters').on('click', function() {
        let url = new URL(window.location.href);
        url.search = '';
        window.location.href = url.toString();
    });

    // Export Excel
    $('#export_excel').on('click', function() {
        const search = $('#search').val();
        const dateFrom = $('#filter_date_from').val();
        const dateTo = $('#filter_date_to').val();

        let url = '/pembayaran-aktivitas-lainnya/export?';
        url += `search=${encodeURIComponent(search)}&`;
        url += `date_from=${encodeURIComponent(dateFrom)}&`;
        url += `date_to=${encodeURIComponent(dateTo)}`;

        window.open(url, '_blank');
    });

    // Set current filter values from URL
    const urlParams = new URLSearchParams(window.location.search);
    $('#search').val(urlParams.get('search') || '');
    $('#filter_date_from').val(urlParams.get('date_from') || '');
    $('#filter_date_to').val(urlParams.get('date_to') || '');
});
</script>
@endpush
