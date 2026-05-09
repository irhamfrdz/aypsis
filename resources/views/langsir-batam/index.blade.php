@extends('layouts.app')

@section('page_title', 'Langsir Kontainer Batam')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Langsir Kontainer Batam</h1>
                        <p class="text-gray-600 mt-1">Kelola data langsir kontainer di wilayah Batam</p>
                    </div>
                    @can('langsir-batam-create')
                    <a href="{{ route('langsir-batam.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Data Langsir
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Filter dan Search -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('langsir-batam.index') }}" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                            <div class="relative">
                                <input type="text" 
                                       id="search" 
                                       name="search" 
                                       value="{{ $search }}" 
                                       placeholder="Cari No. Transaksi, Kontainer, Supir..."
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="tanggal_dari" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dari</label>
                            <input type="date" 
                                   id="tanggal_dari" 
                                   name="tanggal_dari" 
                                   value="{{ $tanggal_dari }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>

                        <div>
                            <label for="tanggal_sampai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sampai</label>
                            <input type="date" 
                                   id="tanggal_sampai" 
                                   name="tanggal_sampai" 
                                   value="{{ $tanggal_sampai }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('langsir-batam.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Data Langsir -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            @if($langsirs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kontainer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dari - Ke</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Supir / Plat</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Biaya</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($langsirs as $index => $langsir)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-900">
                                        {{ $langsirs->firstItem() + $index }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap font-medium text-blue-600">
                                        {{ $langsir->no_transaksi }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                        {{ $langsir->tanggal->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-900">
                                        <div class="font-bold">{{ $langsir->no_kontainer }}</div>
                                        <div class="text-gray-500 text-[10px]">{{ $langsir->size }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                        <div class="flex items-center">
                                            <span class="font-medium">{{ $langsir->dari }}</span>
                                            <svg class="w-3 h-3 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                            <span class="font-medium">{{ $langsir->ke }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                        <div>{{ $langsir->supir ?? '-' }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $langsir->no_plat ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-gray-900">
                                        Rp {{ number_format($langsir->biaya, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <div class="flex justify-center space-x-2">
                                            @can('langsir-batam-view')
                                            <a href="{{ route('langsir-batam.show', $langsir->id) }}" 
                                               class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                               title="Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            @endcan

                                            @can('langsir-batam-update')
                                            <a href="{{ route('langsir-batam.edit', $langsir->id) }}" 
                                               class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                               title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            @endcan

                                            @can('langsir-batam-delete')
                                            <button type="button" 
                                                    onclick="confirmDelete('{{ $langsir->id }}', '{{ $langsir->no_transaksi }}')"
                                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($langsirs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $langsirs->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data langsir Batam</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                        Mulai menginput data langsir kontainer untuk wilayah Batam dengan menekan tombol di bawah.
                    </p>
                    @can('langsir-batam-create')
                    <a href="{{ route('langsir-batam.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Data Langsir
                    </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus</h3>
            <p class="text-sm text-gray-500 mb-1">
                Apakah Anda yakin ingin menghapus data langsir
            </p>
            <p class="text-sm font-semibold text-gray-900 mb-4">
                <span id="deleteItemName"></span>?
            </p>
            <div class="flex gap-3 justify-center">
                <button type="button" 
                        onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id, identifier) {
    document.getElementById('deleteItemName').textContent = identifier;
    document.getElementById('deleteForm').action = '{{ route('langsir-batam.destroy', ':id', false) }}'.replace(':id', id);
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endpush
