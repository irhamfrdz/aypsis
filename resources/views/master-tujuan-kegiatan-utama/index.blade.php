@extends('layouts.app')

@section('title', 'Master Data Transportasi')
@section('page_title', 'Master Data Transportasi')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Daftar Data Transportasi</h2>
            <div class="flex space-x-2">
                <!-- Template Download Button -->
                <a href="{{ route('master.tujuan-kegiatan-utama.download-template') }}" class="inline-flex items-center px-3 py-2 border border-purple-600 text-sm font-medium rounded-md shadow-sm text-purple-600 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Template
                </a>

                <!-- Import Button -->
                <a href="{{ route('master.tujuan-kegiatan-utama.import-form') }}" class="inline-flex items-center px-3 py-2 border border-orange-600 text-sm font-medium rounded-md shadow-sm text-orange-600 bg-white hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    Import Data
                </a>

                <!-- Export Button -->
                <a href="{{ route('master.tujuan-kegiatan-utama.export') }}" class="inline-flex items-center px-3 py-2 border border-green-600 text-sm font-medium rounded-md shadow-sm text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </a>

                <!-- Print Button -->
                <a href="{{ route('master.tujuan-kegiatan-utama.print') }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-blue-600 text-sm font-medium rounded-md shadow-sm text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </a>

                <!-- Add New Button -->
                <a href="{{ route('master.tujuan-kegiatan-utama.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Data Transportasi
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($tujuanKegiatanUtamas->isEmpty())
            <p class="text-gray-500">Belum ada data tujuan kegiatan utama.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow-md">
                    <thead>
                        <tr class="bg-gray-100 text-left text-gray-600 text-[8px] font-semibold">
                            <th class="py-2 px-2">Kode</th>
                            <th class="py-2 px-2">Cabang</th>
                            <th class="py-2 px-2">Wilayah</th>
                            <th class="py-2 px-2">Dari</th>
                            <th class="py-2 px-2">Ke</th>
                            <th class="py-2 px-2">Uang Jalan 20ft</th>
                            <th class="py-2 px-2">Uang Jalan 40ft</th>
                            <th class="py-2 px-2">Keterangan</th>
                            <th class="py-2 px-2">Liter</th>
                            <th class="py-2 px-2">Jarak Penjaringan (km)</th>
                            <th class="py-2 px-2">MEL 20ft</th>
                            <th class="py-2 px-2">MEL 40ft</th>
                            <th class="py-2 px-2">Ongkos Truk 20ft</th>
                            <th class="py-2 px-2">Ongkos Truk 40ft</th>
                            <th class="py-2 px-2">Antar Lokasi 20ft</th>
                            <th class="py-2 px-2">Antar Lokasi 40ft</th>
                            <th class="py-2 px-2">Status</th>
                            <th class="py-2 px-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-[8px]">
                        @foreach ($tujuanKegiatanUtamas as $item)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-2 px-2">{{ $item->kode ?? '-' }}</td>
                                <td class="py-2 px-2">{{ $item->cabang ?? '-' }}</td>
                                <td class="py-2 px-2">{{ $item->wilayah ?? '-' }}</td>
                                <td class="py-2 px-2">{{ $item->dari ?? '-' }}</td>
                                <td class="py-2 px-2">{{ $item->ke ?? '-' }}</td>
                                <td class="py-2 px-2">{{ $item->uang_jalan_20ft ? 'Rp ' . number_format($item->uang_jalan_20ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->uang_jalan_40ft ? 'Rp ' . number_format($item->uang_jalan_40ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->keterangan ?? '-' }}</td>
                                <td class="py-2 px-2">{{ $item->liter ? number_format($item->liter, 2, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->jarak_dari_penjaringan_km ? number_format($item->jarak_dari_penjaringan_km, 2, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->mel_20ft ? 'Rp ' . number_format($item->mel_20ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->mel_40ft ? 'Rp ' . number_format($item->mel_40ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->ongkos_truk_20ft ? 'Rp ' . number_format($item->ongkos_truk_20ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->ongkos_truk_40ft ? 'Rp ' . number_format($item->ongkos_truk_40ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->antar_lokasi_20ft ? 'Rp ' . number_format($item->antar_lokasi_20ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">{{ $item->antar_lokasi_40ft ? 'Rp ' . number_format($item->antar_lokasi_40ft, 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-2">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $item->aktif ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="py-2 px-2 space-x-2">
                                    <a href="{{ route('master.tujuan-kegiatan-utama.edit', $item) }}" class="text-blue-500 hover:underline text-[8px]">Edit</a>
                                    <form action="{{ route('master.tujuan-kegiatan-utama.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline text-[8px]">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
