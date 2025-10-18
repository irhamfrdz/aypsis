@extends('layouts.app')

@section('title', 'Master Tujuan')
@section('page_title', 'Master Tujuan')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Daftar Tujuan</h2>
            <div class="flex space-x-2">
                <!-- Download Template Button -->
                <a href="{{ route('master.tujuan.template') }}"
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

                <!-- Add New Button -->
                <a href="{{ route('master.tujuan.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Tambah Tujuan
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($tujuans->isEmpty())
            <p class="text-gray-500">Belum ada data tujuan.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow-md">
                    <thead>
                        <tr class="bg-gray-100 text-left text-gray-600 text-[10px] font-semibold">
                            <th class="py-3 px-4">Nama Tujuan</th>
                            <th class="py-3 px-4">Cabang</th>
                            <th class="py-3 px-4">Wilayah</th>
                            <th class="py-3 px-4">Dari</th>
                            <th class="py-3 px-4">Ke</th>
                            <th class="py-3 px-4">UJ 20ft</th>
                            <th class="py-3 px-4">Ongkos Truk 20ft</th>
                            <th class="py-3 px-4">UJ 40ft</th>
                            <th class="py-3 px-4">Ongkos Truk 40ft</th>
                            <th class="py-3 px-4">Antarlokasi 20ft</th>
                            <th class="py-3 px-4">Antarlokasi 40ft</th>
                            <th class="py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-[10px]">
                        @foreach ($tujuans as $tujuan)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-3 px-4">{{ trim((($tujuan->dari ?? '') ? $tujuan->dari : '') . ' ' . (($tujuan->ke ?? '') ? '- '.$tujuan->ke : '')) }}</td>
                                <td class="py-3 px-4">{{ $tujuan->cabang }}</td>
                                <td class="py-3 px-4">{{ $tujuan->wilayah }}</td>
                                <td class="py-3 px-4">{{ $tujuan->dari }}</td>
                                <td class="py-3 px-4">{{ $tujuan->ke }}</td>
                                <td class="py-3 px-4">Rp {{ number_format($tujuan->uang_jalan_20 ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">Rp {{ number_format($tujuan->ongkos_truk_20 ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">Rp {{ number_format($tujuan->uang_jalan_40 ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">Rp {{ number_format($tujuan->ongkos_truk_40 ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">Rp {{ number_format($tujuan->antar_20 ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">Rp {{ number_format($tujuan->antar_40 ?? 0, 0, ',', '.') }}</td>

                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('master.tujuan.edit', $tujuan) }}" class="text-blue-500 hover:underline">Edit</a>
                                        @can('audit-log-view')
                                            <button type="button" class="btn btn-info btn-sm"
                                                    onclick="showAuditLog(get_class($tujuan), {{ $tujuan->id }})"
                                                    title="Lihat Riwayat">
                                                <i class="fas fa-history"></i> Riwayat
                                            </button>
                                        @endcan
                                    <form action="{{ route('master.tujuan.destroy', $tujuan) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tujuan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Import Modal --}}
    <div id="import-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Import Master Tujuan</h3>
                    <button type="button" onclick="document.getElementById('import-modal').style.display = 'none'"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('master.tujuan.import') }}" method="POST" enctype="multipart/form-data">
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
                                    <li>Cabang, Wilayah, Dari, Ke wajib diisi</li>
                                    <li>UJ, Ongkos Truk, Antar: angka tanpa titik/koma</li>
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
