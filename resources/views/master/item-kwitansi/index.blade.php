@extends('layouts.app')

@section('title', 'Master Item Kwitansi')
@section('page_title', 'Master Item Kwitansi')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8 transition-all duration-300 hover:shadow-md">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:y-0">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Master Item Kwitansi</h1>
                    <p class="mt-2 text-sm text-gray-500 font-medium">Kelola daftar kode item, nama barang, dan pengelompokan untuk kwitansi</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    @can('master-item-kwitansi-create')
                    <button type="button" 
                            onclick="openCreateModal()"
                            class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200 shadow-lg shadow-blue-200 group">
                        <svg class="w-5 h-5 mr-2 transform group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Item Baru
                    </button>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="mb-8 animate-fade-in-down">
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-8 animate-fade-in-down">
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-rose-800">Terdapat kesalahan input:</h3>
                            <ul class="mt-1 text-sm text-rose-700 list-disc list-inside font-medium">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Search & Filter Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8 transition-all duration-300">
            <div class="relative max-w-2xl">
                <label for="search" class="block text-sm font-bold text-gray-700 mb-2 px-1">
                    <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Cari Item
                </label>
                <div class="group relative">
                    <input type="text"
                           id="searchInput"
                           placeholder="Cari berdasarkan nama item..."
                           class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition-all duration-300 placeholder-gray-400 font-medium">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-300 group-focus-within:text-blue-500 transition-colors duration-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Daftar Item Kwitansi</h3>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mt-1">Total: <span id="itemCount">{{ count($items) }}</span> Item</p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="window.location.reload()" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200" title="Refresh Data">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100" id="itemTable">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Nama Item</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Group</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Keterangan</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse ($items as $index => $item)
                            <tr class="item-row hover:bg-blue-50/30 transition-colors duration-150 group" 
                                data-name="{{ strtolower($item->nama_item) }}"
                                data-kode="{{ strtolower($item->kode) }}"
                                data-group="{{ strtolower($item->group) }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-400 group-hover:text-blue-500 transition-colors duration-200">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs font-extrabold px-2 py-1 bg-gray-100 text-gray-600 rounded-lg group-hover:bg-blue-100 group-hover:text-blue-700 transition-all duration-200 uppercase tracking-tighter">
                                        {{ $item->kode }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors duration-200">{{ $item->nama_item }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-500 bg-emerald-50 text-emerald-700 px-2 py-1 rounded-md border border-emerald-100 inline-block">
                                        <i class="fas fa-layer-group mr-1 opacity-50"></i>{{ $item->group }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 max-w-xs truncate font-medium italic" title="{{ $item->keterangan }}">
                                        {{ $item->keterangan ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        @can('master-item-kwitansi-update')
                                        <button type="button" 
                                                onclick="openEditModal({{ json_encode($item) }})"
                                                class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-100 rounded-lg transition-all duration-200"
                                                title="Edit Item">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @endcan
                                        
                                        @can('audit-log-view')
                                        <button type="button"
                                                class="audit-log-btn text-purple-600 hover:text-purple-800 p-2 hover:bg-purple-100 rounded-lg transition-all duration-200"
                                                data-model-type="App\Models\MasterItemKwitansi"
                                                data-model-id="{{ $item->id }}"
                                                data-item-name="{{ $item->nama_item }}"
                                                title="Riwayat Perubahan">
                                            <i class="fas fa-history"></i>
                                        </button>
                                        @endcan

                                        @can('master-item-kwitansi-delete')
                                        <form action="{{ route('master.item-kwitansi.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-rose-500 hover:text-rose-700 p-2 hover:bg-rose-50 rounded-lg transition-all duration-200"
                                                    title="Hapus Item">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-box-open text-gray-300 text-4xl"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada data item</h3>
                                        <p class="text-sm text-gray-500 font-medium max-w-sm">Data item kwitansi akan muncul di sini setelah Anda menambahkannya.</p>
                                        @can('master-item-kwitansi-create')
                                        <button type="button" 
                                                onclick="openCreateModal()"
                                                class="mt-6 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700">
                                            Tambah Item Pertama
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeCreateModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-modal-up">
            <form action="{{ route('master.item-kwitansi.store') }}" method="POST">
                @csrf
                <div class="bg-white px-8 pt-8 pb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-plus text-blue-600 text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-extrabold text-gray-900" id="modal-title">Tambah Item Baru</h3>
                        </div>
                        <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-1">
                                <label for="kode" class="block text-sm font-bold text-gray-700 mb-1">Kode <span class="text-rose-500">*</span></label>
                                <input type="text" name="kode" id="kode" required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 font-bold uppercase"
                                       placeholder="KODE01">
                            </div>
                            <div class="col-span-2">
                                <label for="nama_item" class="block text-sm font-bold text-gray-700 mb-1">Nama Item <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_item" id="nama_item" required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 font-medium"
                                       placeholder="Contoh: Biaya Handling Kontainer">
                            </div>
                        </div>

                        <div>
                            <label for="group" class="block text-sm font-bold text-gray-700 mb-1">Group <span class="text-rose-500">*</span></label>
                            <input type="text" name="group" id="group" required
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 font-medium"
                                   placeholder="Contoh: HANDLING">
                        </div>



                        <div>
                            <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3"
                                      class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 font-medium"
                                      placeholder="Tambahkan keterangan opsional..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-8 py-6 flex flex-row-reverse gap-3">
                    <button type="submit" class="inline-flex justify-center px-6 py-3 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all duration-200 shadow-lg shadow-blue-100">
                        Simpan Item
                    </button>
                    <button type="button" onclick="closeCreateModal()" class="inline-flex justify-center px-6 py-3 bg-white text-gray-700 text-sm font-bold rounded-xl border border-gray-200 hover:bg-gray-50 transition-all duration-200">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeEditModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-modal-up">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-8 pt-8 pb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-edit text-amber-600 text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-extrabold text-gray-900" id="modal-title">Edit Item Kwitansi</h3>
                        </div>
                        <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-1">
                                <label for="edit_kode" class="block text-sm font-bold text-gray-700 mb-1">Kode <span class="text-rose-500">*</span></label>
                                <input type="text" name="kode" id="edit_kode" required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-amber-100 focus:border-amber-500 transition-all duration-200 font-bold uppercase">
                            </div>
                            <div class="col-span-2">
                                <label for="edit_nama_item" class="block text-sm font-bold text-gray-700 mb-1">Nama Item <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_item" id="edit_nama_item" required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-amber-100 focus:border-amber-500 transition-all duration-200 font-medium">
                            </div>
                        </div>

                        <div>
                            <label for="edit_group" class="block text-sm font-bold text-gray-700 mb-1">Group <span class="text-rose-500">*</span></label>
                            <input type="text" name="group" id="edit_group" required
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-amber-100 focus:border-amber-500 transition-all duration-200 font-medium">
                        </div>



                        <div>
                            <label for="edit_keterangan" class="block text-sm font-bold text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" id="edit_keterangan" rows="3"
                                      class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-amber-100 focus:border-amber-500 transition-all duration-200 font-medium"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-8 py-6 flex flex-row-reverse gap-3">
                    <button type="submit" class="inline-flex justify-center px-6 py-3 bg-amber-600 text-white text-sm font-bold rounded-xl hover:bg-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-100 transition-all duration-200 shadow-lg shadow-amber-100">
                        Perbarui Item
                    </button>
                    <button type="button" onclick="closeEditModal()" class="inline-flex justify-center px-6 py-3 bg-white text-gray-700 text-sm font-bold rounded-xl border border-gray-200 hover:bg-gray-50 transition-all duration-200">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Audit Log Modal Component -->
@include('components.audit-log-modal')

@endsection

@push('scripts')
<script>
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('.item-row');
    const itemCountSpan = document.getElementById('itemCount');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;

        tableRows.forEach(row => {
            const name = row.getAttribute('data-name');
            const kode = row.getAttribute('data-kode');
            const group = row.getAttribute('data-group');
            
            if (name.includes(searchTerm) || kode.includes(searchTerm) || group.includes(searchTerm)) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        itemCountSpan.textContent = visibleCount;
    });

    // Modal Control Functions
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openEditModal(item) {
        const form = document.getElementById('editForm');
        form.action = `{{ url('master/item-kwitansi') }}/${item.id}`;
        
        document.getElementById('edit_kode').value = item.kode;
        document.getElementById('edit_nama_item').value = item.nama_item;
        document.getElementById('edit_group').value = item.group;

        document.getElementById('edit_keterangan').value = item.keterangan || '';
        
        document.getElementById('editModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modals on Escape key
    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endpush

@push('styles')
<style>
    @keyframes modal-up {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .animate-modal-up {
        animation: modal-up 0.3s ease-out forwards;
    }

    @keyframes fade-in-down {
        from { transform: translateY(-10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .animate-fade-in-down {
        animation: fade-in-down 0.4s ease-out forwards;
    }

    /* Custom Scrollbar for better UI */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #ccc;
    }

    /* HSL Tailored Colors for Premium Feel */
    :root {
        --primary-h: 221;
        --primary-s: 83%;
        --primary-l: 53%;
    }
</style>
@endpush
