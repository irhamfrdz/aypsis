
@extends('layouts.app')

@section('title', 'Master Permohonan')
@section('page_title', 'Daftar Permohonan')

@section('content')
<div class="space-y-8">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow" role="alert">
            <p class="font-bold">Sukses</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-md shadow" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 rounded-md shadow" role="alert">
            <p class="font-bold">Peringatan</p>
            <p>{{ session('warning') }}</p>
        </div>
    @endif

    <!-- Daftar Permohonan -->
    <div class="bg-gradient-to-br from-indigo-50 via-white to-indigo-100 shadow-lg rounded-xl p-8 border border-indigo-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-indigo-800">Daftar Permohonan</h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('permohonan.create') }}" class="inline-flex items-center px-5 py-2 border border-transparent text-base font-semibold rounded-lg shadow text-white bg-indigo-600 hover:bg-indigo-700 transition">
                    + Tambah Permohonan
                </a>

                <a href="{{ route('permohonan.export') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Download CSV</a>

                <form action="{{ route('permohonan.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="inline-flex items-center px-4 py-2 bg-white border rounded cursor-pointer text-sm">
                        <input type="file" name="csv_file" accept=".csv,text/csv" class="hidden" onchange="this.form.submit()">
                        Import CSV
                    </label>
                </form>
            </div>
        </div>

        {{-- Import errors/warnings (jika ada) --}}
        @if(session('import_errors'))
            <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <p class="font-bold text-yellow-800">Beberapa baris gagal diimpor</p>
                <p class="text-sm text-yellow-800">Periksa baris yang dilaporkan di bawah. Pastikan file CSV menggunakan delimiter <strong>;</strong> dan kolom-kolom sesuai format: <code>nomor_memo;kegiatan;supir;tujuan;jumlah_kontainer;total_harga_setelah_adj</code>.</p>
                <ul class="list-disc ml-5 mt-2 text-sm text-yellow-800">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <p class="mt-2 text-xs text-gray-600">Tip: Jika Anda ingin contoh file, beri tahu saya dan saya akan buatkan template CSV.</p>
            </div>
        @endif

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-indigo-200 bg-white rounded-lg">
                <thead class="bg-indigo-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Nomor Memo</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Kegiatan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Supir</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Tujuan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Total Biaya</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-indigo-100">
                    @forelse ($permohonans as $permohonan)
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-indigo-900 font-semibold">{{ $permohonan->nomor_memo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-indigo-800">{{ $kegiatanMap[$permohonan->kegiatan] ?? $permohonan->kegiatan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-indigo-800">{{ $permohonan->supir->nama_panggilan ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-indigo-800">{{ $permohonan->tujuan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-indigo-900 font-bold">Rp. {{ number_format($permohonan->total_harga_setelah_adj, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('permohonan.show', $permohonan) }}" class="inline-block px-3 py-1 rounded bg-indigo-500 text-white hover:bg-indigo-700 transition shadow">Lihat</a>
                                <a href="{{ route('permohonan.edit', $permohonan) }}" class="inline-block px-3 py-1 rounded bg-blue-500 text-white hover:bg-blue-700 transition shadow">Edit</a>
                                <form action="{{ route('permohonan.destroy', $permohonan) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-block px-3 py-1 rounded bg-red-500 text-white hover:bg-red-700 transition shadow">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada data permohonan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $permohonans->links() }}
        </div>
    </div>
</div>
@endsection
