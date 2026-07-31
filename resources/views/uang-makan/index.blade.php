@extends('layouts.app')

@section('title', 'Data Uang Makan Karyawan')
@section('page_title', 'Data Uang Makan Karyawan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Data Uang Makan</h1>
                    <p class="mt-1 text-sm text-gray-600">Daftar pencatatan uang makan harian karyawan</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @can('data-uang-makan-create')
                    <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import Excel
                    </button>
                    <a href="{{ route('uang-makan.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-sm">
                        Tambah Data
                    </a>
                    @endcan
                </div>
            </div>
        </div>

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

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('uang-makan.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
                <div class="w-full sm:w-1/3">
                    <label for="search" class="sr-only">Cari Karyawan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Cari nama atau NIK...">
                    </div>
                </div>
                <div class="w-full sm:w-1/3">
                    <label for="penempatan" class="sr-only">Filter Penempatan</label>
                    <select id="penempatan" name="penempatan" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" onchange="this.form.submit()">
                        <option value="">Semua Penempatan</option>
                        @foreach($penempatans as $pen)
                            <option value="{{ $pen }}" {{ request('penempatan') == $pen ? 'selected' : '' }}>{{ $pen }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cari
                    </button>
                </div>
                @if(request('penempatan') || request('search'))
                <div>
                    <a href="{{ route('uang-makan.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Reset Filter</a>
                </div>
                @endif
            </form>
        </div>

        <!-- Table Section -->
        <form action="{{ route('uang-makan.bulk-delete') }}" method="POST" id="bulk-delete-form">
            @csrf
            <div class="mb-4 flex justify-between items-center">
                <div></div>
                @can('data-uang-makan-delete')
                <button type="submit" id="btn-bulk-delete" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md text-sm hidden shadow-sm transition-colors duration-200" onclick="return confirm('Apakah Anda yakin ingin menghapus data yang dipilih?')">
                    Hapus Data Terpilih (<span id="selected-count">0</span>)
                </button>
                @endcan
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                                <input type="checkbox" id="check-all" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($uangMakans as $index => $uangMakan)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="ids[]" value="{{ $uangMakan->id }}" class="row-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $uangMakans->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $uangMakan->karyawan->nama_lengkap ?? '-' }}</div>
                                    <div class="text-sm text-gray-500">{{ $uangMakan->karyawan->nik ?? '-' }}</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        @if($uangMakan->karyawan && $uangMakan->karyawan->akun_bank)
                                            {{ $uangMakan->karyawan->nama_bank ?? 'Bank' }} - {{ $uangMakan->karyawan->akun_bank }}
                                        @else
                                            No Rek: -
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $uangMakan->tanggal->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($uangMakan->nominal, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 line-clamp-2">{{ $uangMakan->keterangan ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @can('data-uang-makan-edit')
                                    <a href="{{ route('uang-makan.edit', $uangMakan->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    @endcan
                                    @can('data-uang-makan-delete')
                                    <form action="{{ route('uang-makan.destroy', $uangMakan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data uang makan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($uangMakans->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $uangMakans->links() }}
                </div>
            @endif
        </div>
        </form>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('importModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('uang-makan.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Import Data Uang Makan
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    Unggah file Excel (xlsx/xls/csv) berisi data uang makan. Pastikan format kolom sesuai dengan template.
                                </p>
                                <a href="{{ route('uang-makan.template') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500 mb-4">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Download Template
                                </a>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">File Excel</label>
                                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Import
                    </button>
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const btnBulkDelete = document.getElementById('btn-bulk-delete');
        const selectedCount = document.getElementById('selected-count');

        function updateBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            if(selectedCount) selectedCount.textContent = checkedCount;
            
            if (btnBulkDelete) {
                if (checkedCount > 0) {
                    btnBulkDelete.classList.remove('hidden');
                } else {
                    btnBulkDelete.classList.add('hidden');
                }
            }

            if(checkAll) {
                checkAll.checked = (checkedCount === rowCheckboxes.length && rowCheckboxes.length > 0);
            }
        }

        if(checkAll) {
            checkAll.addEventListener('change', function() {
                rowCheckboxes.forEach(cb => {
                    cb.checked = checkAll.checked;
                });
                updateBulkDeleteButton();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkDeleteButton);
        });
    });
</script>
@endpush
