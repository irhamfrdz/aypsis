@extends('layouts.app')

@section('title', 'Master Pricelist Sewa Kontainer')
@section('page_title', 'Master Pricelist Sewa Kontainer')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Daftar Pricelist Sewa Kontainer</h2>
    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('master.master.pricelist-sewa-kontainer.create') }}" class="inline-block bg-indigo-600 text-white py-1 px-2 rounded text-xs font-medium hover:bg-indigo-700 transition-colors duration-200">
            + Tambah Pricelist
        </a>
        <a href="{{ route('master.master.pricelist-sewa-kontainer.export-template') }}" class="inline-block bg-green-600 text-white py-1 px-2 rounded text-xs font-medium hover:bg-green-700 transition-colors duration-200">
            📥 Download Template CSV
        </a>
        <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="inline-block bg-blue-600 text-white py-1 px-2 rounded text-xs font-medium hover:bg-blue-700 transition-colors duration-200">
            📤 Import CSV
        </button>
    </div>
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    <div class="overflow-x-auto shadow-md sm:rounded-lg">
    <table class="min-w-full bg-white divide-y divide-gray-200 text-xs table-fixed" style="table-layout: fixed;">
            <colgroup>
                <col style="width: 2.5rem;"> <!-- No -->
                <col style="width: 8rem;">  <!-- Vendor -->
                <col style="width: 6rem;">  <!-- Tarif -->
                <col style="width: 4rem;">  <!-- Ukuran -->
                <col style="width: 8rem;">  <!-- Harga -->
                <col style="width: 7rem;">  <!-- Tanggal Awal -->
                <col style="width: 7rem;">  <!-- Tanggal Akhir -->
                <col style="width: 10rem;"> <!-- Keterangan -->
                <col style="width: 6rem;">  <!-- Aksi -->
            </colgroup>
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-1 px-3 text-center font-semibold text-gray-600 w-10 align-top" style="height: 36px; vertical-align: top;">No</th>
                    <th class="py-1 px-3 font-semibold text-gray-600 w-32 align-top" style="height: 36px; vertical-align: top;">Vendor</th>
                    <th class="py-1 px-3 font-semibold text-gray-600 w-24 align-top" style="height: 36px; vertical-align: top;">Tarif</th>
                    <th class="py-1 px-3 font-semibold text-gray-600 w-16 text-center align-top" style="height: 36px; vertical-align: top;">Ukuran</th>
                    <th class="py-1 px-3 font-semibold text-gray-600 w-32 text-center align-top" style="height: 36px; vertical-align: top;">Harga</th>
                    <th class="py-1 px-3 font-semibold text-gray-600 w-28 text-center align-top" style="height: 36px; vertical-align: top;">Tanggal Awal</th>
                    <th class="py-1 px-3 font-semibold text-gray-600 w-28 text-center align-top" style="height: 36px; vertical-align: top;">Tanggal Akhir</th>
                    <th class="py-1 px-3 font-semibold text-gray-600 w-40 align-top" style="height: 36px; vertical-align: top;">Keterangan</th>
                    <th class="py-1 px-3 text-center font-semibold text-gray-600 w-24 align-top" style="height: 36px; vertical-align: top;">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100 text-gray-700 text-[10px]">
                @forelse ($pricelists as $index => $pricelist)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="py-1 px-3 text-center w-10 align-top" style="height: 36px; vertical-align: top;">{{ $pricelists->firstItem() + $index }}</td>
                        <td class="py-1 px-3 text-center w-32 align-top" style="height: 36px; vertical-align: top;">{{ $pricelist->vendor }}</td>
                        <td class="py-1 px-3 text-center w-24 align-top" style="height: 36px; vertical-align: top;">{{ $pricelist->tarif }}</td>
                        <td class="py-1 px-3 text-center w-16 align-top" style="height: 36px; vertical-align: top;">{{ $pricelist->ukuran_kontainer }} ft</td>
                        <td class="py-1 px-3 text-center w-32 align-top" style="height: 36px; vertical-align: top;">Rp {{ rtrim(rtrim(number_format($pricelist->harga, 2, ',', '.'), '0'), ',') }}</td>
                        <td class="py-1 px-3 text-center w-28 align-top" style="height: 36px; vertical-align: top;">{{ \Carbon\Carbon::parse($pricelist->tanggal_harga_awal)->format('d-m-Y') }}</td>
                        <td class="py-1 px-3 text-center w-28 align-top" style="height: 36px; vertical-align: top;">{{ $pricelist->tanggal_harga_akhir ? \Carbon\Carbon::parse($pricelist->tanggal_harga_akhir)->format('d-m-Y') : '-' }}</td>
                        <td class="py-1 px-3 text-center w-40 align-top" style="height: 36px; vertical-align: top; word-wrap: break-word;">{{ $pricelist->keterangan ?? '-' }}</td>
                        <td class="py-1 px-3 text-center w-24 align-top" style="height: 36px; vertical-align: top;">
                            <div class="flex items-center justify-center space-x-3 text-[10px]">
                                <a href="{{ route('master.master.pricelist-sewa-kontainer.edit', $pricelist->id) }}"
                                   class="text-blue-600 hover:text-blue-800 hover:underline font-medium"
                                   title="Edit Data">
                                    Edit
                                </a>
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('master.master.pricelist-sewa-kontainer.destroy', $pricelist->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 hover:underline font-medium cursor-pointer border-none bg-transparent p-0"
                                            title="Hapus Data">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-2 px-6 text-center text-gray-500 text-[10px] align-top" style="height: 36px; vertical-align: top;">Tidak ada data pricelist yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $pricelists->links() }}
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import Data CSV</h3>
                <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>

            <form action="{{ route('master.master.pricelist-sewa-kontainer.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Pilih File CSV</label>
                    <input type="file" name="file" id="file" accept=".csv,.txt" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-sm text-gray-500">Format file: CSV dengan header sesuai template</p>
                </div>

                <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Format CSV yang Diharapkan:</h4>
                    <p class="text-xs text-blue-700 mb-2">File CSV menggunakan pemisah titik koma (;) untuk menghindari masalah dengan koma dalam data. Template hanya berisi header kolom.</p>
                    <div class="text-xs text-blue-700 font-mono bg-white p-2 rounded border">
                        vendor;tarif;ukuran_kontainer;harga;tanggal_harga_awal;tanggal_harga_akhir;keterangan
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="px-3 py-1 bg-gray-300 text-gray-700 rounded text-sm hover:bg-gray-400 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors duration-200">
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
