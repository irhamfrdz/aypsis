@extends('layouts.app')

@section('title', 'Daftar Pranota OB')

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="bg-green-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h5 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        Daftar Pranota Tagihan OB
                    </h5>
                    <p class="text-green-100 text-sm mt-1">
                        Kelola pranota tagihan on board untuk multiple tagihan OB
                    </p>
                </div>
                <div class="flex space-x-2">
                    @can('pranota-ob-create')
                        <a href="{{ route('pranota-ob.create') }}" 
                           class="bg-white text-green-600 hover:bg-gray-50 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            <i class="fas fa-plus mr-1"></i>
                            Buat Pranota OB
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="p-6">
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4" role="alert">
                    <div class="flex justify-between items-center">
                        <span>{{ session('success') }}</span>
                        <button type="button" class="text-green-500 hover:text-green-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
                    <div class="flex justify-between items-center">
                        <span>{{ session('error') }}</span>
                        <button type="button" class="text-red-500 hover:text-red-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Filter & Search -->
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="md:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500" 
                               placeholder="Cari nomor pranota, keterangan...">
                    </div>
                </div>
                <div>
                    <select name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <option value="">Semua Status</option>
                        <option value="belum_realisasi" {{ request('status') === 'belum_realisasi' ? 'selected' : '' }}>Belum Realisasi</option>
                        <option value="sudah_realisasi" {{ request('status') === 'sudah_realisasi' ? 'selected' : '' }}>Sudah Realisasi</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <i class="fas fa-filter mr-1"></i>Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'periode']))
                        <a href="{{ route('pranota-ob.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <i class="fas fa-times mr-1"></i>Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 resizable-table" id="pranotaObTable">
                    <thead class="bg-gray-50">
                        <tr><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight" style="position: relative;">No<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight" style="position: relative;">Nomor Pranota<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight" style="position: relative;">Tanggal<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight" style="position: relative;">Jumlah Item<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight" style="position: relative;">Total Sebelum DP<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight" style="position: relative;">Total Sesudah DP<div class="resize-handle"></div></th><th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight" style="position: relative;">Status<div class="resize-handle"></div></th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-tight">Aksi</th></tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($pranotaObs as $index => $pranota)
                            <tr class="hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pranotaObs->firstItem() + $index }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $pranota->nomor_pranota }}</div>
                                    @if($pranota->keterangan)
                                        <div class="text-sm text-gray-500">{{ Str::limit($pranota->keterangan, 40) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $pranota->items_count ?? $pranota->items->count() }} item
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pranota->formatted_total }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">{{ $pranota->formatted_total_after_dp }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $pranota->status_badge }}">
                                        {{ $pranota->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @can('pranota-ob-view')
                                            <a href="{{ route('pranota-ob.show', $pranota) }}" 
                                               class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded" 
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        
                                        @can('pranota-ob-view')
                                            <a href="{{ route('pranota-ob.print', $pranota) }}" 
                                               target="_blank"
                                               class="text-purple-600 hover:text-purple-900 bg-purple-100 hover:bg-purple-200 px-2 py-1 rounded" 
                                               title="Print/Cetak">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        @endcan
                                        
                                        @can('pranota-ob-update')
                                            <a href="{{ route('pranota-ob.edit', $pranota) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 px-2 py-1 rounded" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        
                                        @can('pranota-ob-delete')
                                            <button type="button" 
                                                    class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-2 py-1 rounded" 
                                                    title="Hapus"
                                                    onclick="confirmDelete({{ $pranota->id }})">
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
                                        <i class="fas fa-file-invoice text-gray-400 text-4xl mb-4"></i>
                                        <p class="text-gray-500 text-lg">Belum ada pranota OB</p>
                                        <p class="text-gray-400 text-sm">Buat pranota OB pertama Anda</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $pranotaObs->firstItem() ?? 0 }} - {{ $pranotaObs->lastItem() ?? 0 }} 
                    dari {{ $pranotaObs->total() }} data
                </div>
                <div class="pagination-links">
                    {{ $pranotaObs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Konfirmasi Hapus
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Apakah Anda yakin ingin menghapus pranota OB ini?
                        </p>
                        <p class="text-sm text-red-600 mt-1">
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus
                    </button>
                </form>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm" onclick="closeDeleteModal()">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/pranota-ob/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endpush