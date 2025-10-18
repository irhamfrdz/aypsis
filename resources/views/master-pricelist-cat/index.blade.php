@extends('layouts.app')

@section('title', 'Master Pricelist CAT')
@section('page_title', 'Master Pricelist CAT')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Daftar Pricelist CAT</h2>

    <!-- Filters -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('master.pricelist-cat.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                <input type="text" name="vendor" value="{{ request('vendor') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Cari vendor...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status CAT</label>
                <select name="jenis_cat" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="cat_sebagian" {{ request('jenis_cat') == 'cat_sebagian' ? 'selected' : '' }}>Cat Sebagian</option>
                    <option value="cat_full" {{ request('jenis_cat') == 'cat_full' ? 'selected' : '' }}>Cat Full</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran Kontainer</label>
                <select name="ukuran_kontainer" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Ukuran</option>
                    <option value="20ft" {{ request('ukuran_kontainer') == '20ft' ? 'selected' : '' }}>20ft</option>
                    <option value="40ft" {{ request('ukuran_kontainer') == '40ft' ? 'selected' : '' }}>40ft</option>
                    <option value="40ft HC" {{ request('ukuran_kontainer') == '40ft HC' ? 'selected' : '' }}>40ft HC</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Cari...">
            </div>
            <div class="flex items-end space-x-2 md:col-span-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    Filter
                </button>
                <a href="{{ route('master.pricelist-cat.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Action Buttons -->
    <div class="mb-4 flex flex-wrap gap-2">
        <!-- Download Template Button -->
        <a href="{{ route('master.pricelist-cat.template') }}"
           class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Template CSV
        </a>

        <!-- Import Button -->
        <button type="button" onclick="document.getElementById('import-modal').style.display = 'block'"
                class="inline-flex items-center px-3 py-2 border border-green-600 text-sm font-medium rounded-md shadow-sm text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
            </svg>
            Import CSV
        </button>

        @can('master-pricelist-cat-create')
        <a href="{{ route('master.pricelist-cat.create') }}" class="inline-block bg-blue-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200">
            + Tambah Pricelist CAT
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <table class="min-w-full bg-white divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Vendor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Status CAT</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Ukuran</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Tarif</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pricelists as $index => $pricelist)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                        {{ $pricelists->firstItem() + $index }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $pricelist->vendor }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            {{ $pricelist->jenis_cat == 'cat_full' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $pricelist->jenis_cat == 'cat_full' ? 'Cat Full' : 'Cat Sebagian' }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                        {{ $pricelist->ukuran_kontainer }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                        {{ $pricelist->tarif ? 'Rp ' . number_format($pricelist->tarif, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            @can('master-pricelist-cat-update')
                            <a href="{{ route('master.pricelist-cat.edit', $pricelist) }}"
                               class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        @can('audit-log-view')
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    onclick="showAuditLog(get_class($index), {{ $index->id }})"
                                                    title="Lihat Riwayat">
                                                <i class="fas fa-history"></i> Riwayat
                                            </button>
                                        @endcan
                            @endcan
                            @can('master-pricelist-cat-delete')
                            <form method="POST" action="{{ route('master.pricelist-cat.destroy', $pricelist) }}"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pricelist CAT ini?')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                        Tidak ada data pricelist CAT ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($pricelists->hasPages())
    <div class="mt-6">
        {{ $pricelists->appends(request()->query())->links() }}
    </div>
    @endif
</div>

{{-- Import Modal --}}
<div id="import-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import Master Pricelist CAT</h3>
                <button type="button" onclick="document.getElementById('import-modal').style.display = 'none'"
                        class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('master.pricelist-cat.import') }}" method="POST" enctype="multipart/form-data">
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
                                <li>Vendor dan Jenis CAT wajib diisi</li>
                                <li>Jenis CAT: cat_sebagian atau cat_full</li>
                                <li>Ukuran: 20ft, 40ft, atau 40ft HC</li>
                                <li>Tarif: angka tanpa titik/koma</li>
                                <li>Hapus baris contoh data sebelum import</li>
                                <li>Data yang sudah ada akan diperbarui</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <button type="button"
                            onclick="document.getElementById('import-modal').style.display = 'none'"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
