@extends('layouts.app')

@section('title', 'Master Pengirim/Penerima')

@section('content')
<div class="container mx-auto px-4 py-8">
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 font-medium">Master Pengirim/Penerima</li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Master Pengirim/Penerima</h1>
                <p class="text-gray-600 mt-1">Kelola data pengirim dan penerima</p>
            </div>
            @can('master-pengirim-penerima-create')
            <div class="flex items-center gap-2">
                <!-- Dropdown Import/Export -->
                <div class="relative group" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition duration-200">
                        <i class="fas fa-file-excel mr-2"></i> Excel <i class="fas fa-chevron-down ml-2 text-xs"></i>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 hidden"
                         :class="{'hidden': !open}">
                        <div class="py-1">
                            <!-- Download Data -->
                            <a href="{{ route('master-pengirim-penerima.export-data') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-download mr-2 text-green-600"></i> Export Data
                            </a>
                            <!-- Import Update -->
                            <button onclick="openImportUpdateModal(); open = false" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-file-upload mr-2 text-blue-600"></i> Import Update Data
                            </button>
                            <div class="border-t border-gray-100 my-1"></div>
                            <!-- Download Template (Lama) -->
                            <a href="{{ route('master-pengirim-penerima.download-template') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-file-csv mr-2 text-green-600"></i> Download Template Baru
                            </a>
                            <!-- Import Excel (Lama) -->
                            <button onclick="openImportModal(); open = false" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-upload mr-2 text-blue-600"></i> Import Data Baru
                            </button>
                        </div>
                    </div>
                </div>

                <a href="{{ route('master-pengirim-penerima.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">
                    <i class="fas fa-plus mr-2"></i> Tambah Data
                </a>
            </div>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
            <div class="flex">
                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('master-pengirim-penerima.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari nama, kode..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="{{ route('master-pengirim-penerima.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-200">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NPWP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $item->kode }}</code>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('master-pengirim-penerima.show', $item) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                {{ $item->nama }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $item->pic ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $item->telepon ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ Str::limit($item->alamat ?? '-', 50) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $item->npwp ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->status == 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-minus-circle mr-1"></i> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                @can('master-pengirim-penerima-view')
                                <a href="{{ route('master-pengirim-penerima.show', $item) }}"
                                   class="text-blue-600 hover:text-blue-800 transition"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('master-pengirim-penerima-update')
                                <a href="{{ route('master-pengirim-penerima.edit', $item) }}"
                                   class="text-yellow-600 hover:text-yellow-800 transition"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('master-pengirim-penerima-delete')
                                <form action="{{ route('master-pengirim-penerima.destroy', $item) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus {{ $item->nama }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800 transition"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-inbox text-6xl mb-4"></i>
                                <p class="text-lg font-medium">Tidak ada data</p>
                                <p class="text-sm">Belum ada data pengirim/penerima yang ditambahkan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($data->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $data->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import Data Pengirim/Penerima</h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('master-pengirim-penerima.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="import_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File Excel/CSV
                    </label>
                    <input type="file"
                           name="file"
                           id="import_file"
                           accept=".csv,.xlsx,.xls"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    <p class="mt-1 text-sm text-gray-500">
                        Format yang didukung: CSV, Excel (.xlsx, .xls)
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        Pastikan format file sesuai dengan template yang telah di-download
                    </p>
                    <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                        <p class="text-xs text-yellow-800">
                            <strong>Format CSV:</strong> nama;alamat;npwp<br>
                            <strong>Kode:</strong> Otomatis tergenerate (PP-XXXX)<br>
                            <strong>Status:</strong> Otomatis active
                        </p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Update -->
<div id="importUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-[32rem] shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import Update Data</h3>
                <button onclick="closeImportUpdateModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('master-pengirim-penerima.import-update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="import_update_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File Excel Hasil Export
                    </label>
                    <input type="file"
                           name="file"
                           id="import_update_file"
                           accept=".xlsx,.xls,.csv"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <h4 class="text-sm font-medium text-blue-800 mb-1">Cara Penggunaan:</h4>
                        <ol class="list-decimal ml-4 text-xs text-blue-700 space-y-1">
                            <li>Klik tombol <strong>Export Data</strong> untuk mengunduh data saat ini.</li>
                            <li>Buka file Excel, lalu isi field yang masih kosong (contoh: PIC, Telepon, Contact Person).</li>
                            <li>Upload kembali file Excel tersebut ke sini.</li>
                            <li>Sistem hanya akan memperbarui data jika field di database masih kosong (data lama yang sudah ada tidak akan ditimpa).</li>
                        </ol>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeImportUpdateModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Upload & Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Menambahkan AlpineJS jika belum ada di layout
if (typeof Alpine === 'undefined') {
    let script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
    script.defer = true;
    document.head.appendChild(script);
}

function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
    document.getElementById('import_file').value = '';
}

function openImportUpdateModal() {
    document.getElementById('importUpdateModal').classList.remove('hidden');
}

function closeImportUpdateModal() {
    document.getElementById('importUpdateModal').classList.add('hidden');
    document.getElementById('import_update_file').value = '';
}

// Close modals when clicking outside
window.addEventListener('click', function(e) {
    let importModal = document.getElementById('importModal');
    let importUpdateModal = document.getElementById('importUpdateModal');
    
    if (e.target === importModal) {
        closeImportModal();
    }
    if (e.target === importUpdateModal) {
        closeImportUpdateModal();
    }
});
</script>

@endsection
