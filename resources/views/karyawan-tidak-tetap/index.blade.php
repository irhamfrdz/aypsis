@extends('layouts.app')

@section('title', 'Data Karyawan Tidak Tetap')
@section('page_title', 'Data Karyawan Tidak Tetap')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"> <!-- Reduced width for this page -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="px-5 py-4 border-b bg-white flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-xl font-bold text-gray-900">Data Karyawan Tidak Tetap</h1>
            <div class="flex items-center gap-2">
                <form action="{{ route('karyawan-tidak-tetap.index') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="pl-8 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Cari Nama/NIK...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                </form>
                @can('karyawan-tidak-tetap-create')
                <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-file-excel mr-2"></i> Import
                </button>
                <a href="{{ route('karyawan-tidak-tetap.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i> Tambah
                </a>
                @endcan
            </div>
        </div>

        @if (session('success'))
            <div class="mx-5 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mx-5 mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if (session('import_errors'))
            <div class="mx-5 mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                <p class="font-bold">Beberapa baris gagal diimport:</p>
                <ul class="list-disc list-inside mt-2 text-sm max-h-40 overflow-y-auto">
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pekerjaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cabang</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($karyawans as $karyawan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ ($karyawans->currentPage() - 1) * $karyawans->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $karyawan->nama_lengkap }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $karyawan->nik ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $karyawan->pekerjaan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $karyawan->divisi ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $karyawan->cabang ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center space-x-3">
                                    <a href="{{ route('karyawan-tidak-tetap.print-single', $karyawan->id) }}" class="text-gray-600 hover:text-gray-900" title="Print" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @can('karyawan-tidak-tetap-update')
                                    <a href="{{ route('karyawan-tidak-tetap.edit', $karyawan->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('karyawan-tidak-tetap-delete')
                                    <form action="{{ route('karyawan-tidak-tetap.destroy', $karyawan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                Tidak ada data karyawan tidak tetap.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $karyawans->links() }}
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('importModal').classList.add('hidden')"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-file-import text-green-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Import Karyawan Tidak Tetap
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4">
                                Silahkan upload file Excel (.xlsx, .xls) atau CSV untuk mengimport data karyawan tidak tetap.
                            </p>
                            
                            <form action="{{ route('karyawan-tidak-tetap.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                                @csrf
                                <div class="mt-2 text-left">
                                    <label class="block text-sm font-medium text-gray-700">File Import</label>
                                    <input type="file" name="file" required accept=".xlsx, .xls, .csv" class="mt-1 block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-green-50 file:text-green-700
                                        hover:file:bg-green-100
                                    "/>
                                </div>
                                
                                <div class="mt-4 text-left">
                                    <a href="{{ route('karyawan-tidak-tetap.template') }}" class="text-indigo-600 hover:text-indigo-900 text-sm flex items-center">
                                        <i class="fas fa-download mr-1"></i> Download Template
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" form="importForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Import
                </button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('importModal').classList.add('hidden')">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
