@extends('layouts.app')

@section('title', 'Master Mobil')
@section('page_title', 'Master Mobil')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Daftar Mobil</h2>

    <!-- Search Form -->
    <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
        <form method="GET" action="{{ route('master.mobil.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari kode aktiva, nomor polisi, merek, jenis, atau nama karyawan..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       autocomplete="off">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('master.mobil.index') }}" class="inline-flex items-center bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6m0 0l6-6m-6 6l6 6"/>
                        </svg>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tombol Tambah Mobil -->
    <div class="mb-4 flex flex-wrap gap-3">
        <a href="{{ route('master.mobil.create') }}" class="inline-flex items-center bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Mobil
        </a>

        <!-- Download Template Button -->
        <a href="{{ route('master.mobil.template') }}"
           class="inline-flex items-center bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download Template
        </a>

        <!-- Import Button -->
        <button type="button" onclick="document.getElementById('import-modal').style.display = 'block'"
                class="inline-flex items-center bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
            </svg>
            Import CSV
        </button>
    </div>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Notifikasi Error -->
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Import Errors -->
    @if(session('import_errors'))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Error(s) pada Import Data:</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Import Warnings -->
    @if(session('import_warnings'))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Peringatan Import Data:</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach(session('import_warnings') as $warning)
                                <li>{{ $warning }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Search Result Info & Data Summary -->
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        @if(request('search'))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 flex-1">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-blue-800 text-sm">
                        Menampilkan <strong>{{ $mobils->total() }}</strong> hasil pencarian untuk "<strong>{{ request('search') }}</strong>"
                    </span>
                </div>
            </div>
        @else
            <div class="text-sm text-gray-600">
                Total <strong>{{ $mobils->total() }}</strong> mobil terdaftar
            </div>
        @endif
        
        <!-- Rows Per Page Quick Control -->
        <div class="flex items-center space-x-2">
            <label class="text-sm text-gray-600">Tampilkan:</label>
            <form method="GET" action="{{ route('master.mobil.index') }}" class="inline">
                @foreach(request()->query() as $key => $value)
                    @if($key !== 'per_page' && $key !== 'page')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <select name="per_page"
                        onchange="this.form.submit()"
                        class="px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
            <span class="text-sm text-gray-600">per halaman</span>
        </div>
    </div>

    <!-- Tabel Daftar Mobil -->
    <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode No</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Polisi</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merek</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                @forelse ($mobils as $index => $mobil)
                    <tr class="hover:bg-gray-50">
                        <td class="py-4 px-6">{{ $mobils->firstItem() + $index }}</td>
                        <td class="py-4 px-6 font-mono text-sm">{{ $mobil->kode_no }}</td>
                        <td class="py-4 px-6">{{ $mobil->nomor_polisi }}</td>
                        <td class="py-4 px-6">{{ $mobil->merek ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $mobil->jenis ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $mobil->tahun_pembuatan ?? '-' }}</td>
                        <td class="py-4 px-6">{{ $mobil->karyawan->nama_lengkap ?? '-' }}</td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <!-- Tombol Edit -->
                                <a href="{{ route('master.mobil.edit', $mobil->id) }}" class="bg-yellow-500 text-white py-1 px-3 rounded-md hover:bg-yellow-600 transition-colors duration-200 text-sm">
                                    Edit
                                </a>
                                <span class="text-gray-300">|</span>
                                @can('audit-log-view')
                                    <button type="button" class="audit-log-btn bg-purple-500 text-white py-1 px-3 rounded-md hover:bg-purple-600 transition-colors duration-200 text-sm"
                                            data-model-type="{{ get_class($mobil) }}"
                                            data-model-id="{{ $mobil->id }}"
                                            data-item-name="{{ $mobil->kode_no }}"
                                            title="Lihat Riwayat">
                                        Riwayat
                                    </button>
                                @endcan
                                <span class="text-gray-300">|</span>
                                <!-- Tombol Hapus -->
                                <form action="{{ route('master.mobil.destroy', $mobil->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mobil ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white py-1 px-3 rounded-md hover:bg-red-600 transition-colors duration-200 text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-4 px-6 text-center text-gray-500">
                            <div class="flex flex-col items-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                @if(request('search'))
                                    <p class="text-lg font-medium text-gray-600">Tidak ada mobil ditemukan</p>
                                    <p class="text-sm text-gray-500 mt-1">Tidak ada hasil untuk pencarian "<strong>{{ request('search') }}</strong>"</p>
                                    <a href="{{ route('master.mobil.index') }}" class="mt-3 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        Lihat semua mobil
                                    </a>
                                @else
                                    <p class="text-lg font-medium text-gray-600">Belum ada data mobil</p>
                                    <p class="text-sm text-gray-500 mt-1">Silakan tambah mobil baru atau import data CSV</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Rows Per Page Selection -->
    @include('components.rows-per-page', [
        'routeName' => 'master.mobil.index',
        'paginator' => $mobils,
        'entityName' => 'mobil',
        'entityNamePlural' => 'mobil'
    ])

    <!-- Modern Pagination -->
    <div class="mt-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        @include('components.modern-pagination', [
            'paginator' => $mobils,
            'routeName' => 'master.mobil.index'
        ])
    </div>
</div>

{{-- Import Modal --}}
<div id="import-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import Master Mobil</h3>
                <button type="button" onclick="document.getElementById('import-modal').style.display = 'none'"
                        class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('master.mobil.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                        File CSV <span class="text-red-500">*</span>
                    </label>
                    <input type="file"
                           id="csv_file"
                           name="csv_file"
                           accept=".csv"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">
                        Pilih file CSV dengan format yang sesuai template. Maksimal 5MB.
                    </p>
                </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-yellow-700">
                            <p class="font-medium">Panduan Import:</p>
                            <ul class="mt-1 list-disc list-inside text-xs">
                                <li>Download template CSV terlebih dahulu</li>
                                <li>Isi data sesuai format yang disediakan</li>
                                <li>Kode Aktiva atau Nomor Polisi minimal salah satu harus diisi</li>
                                <li>NIK karyawan opsional, jika ada akan otomatis terhubung ke data karyawan</li>
                                <li>Field tanggal gunakan format: "24 Sep 26" atau "24/09/2026"</li>
                                <li>Hapus baris contoh data sebelum import</li>
                                <li>Data yang sudah ada akan diperbarui berdasarkan Kode Aktiva</li>
                                <li>Semua field opsional kecuali identifier utama</li>
                            </ul>
                        </div>
                    </div>
                </div>                <div class="flex items-center justify-end space-x-3">
                    <button type="button"
                            onclick="document.getElementById('import-modal').style.display = 'none'"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Audit Log Modal -->
@include('components.audit-log-modal')

@endsection
