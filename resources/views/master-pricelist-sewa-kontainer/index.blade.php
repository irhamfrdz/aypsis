@extends('layouts.app')

@section('title', 'Master Pricelist Sewa Kontainer')
@section('page_title', 'Master Pricelist Sewa Kontainer')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Daftar Pricelist Sewa Kontainer</h2>
    <div class="mb-4">
    <a href="{{ route('master.pricelist-sewa-kontainer.create') }}" class="inline-block bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors duration-200">
            + Tambah Pricelist
        </a>
    </div>
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    <div class="overflow-x-auto shadow-md sm:rounded-lg">
    <table class="min-w-full bg-white divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-3 text-center font-semibold text-gray-600 w-10">No</th>
                    <th class="py-2 px-3 font-semibold text-gray-600 w-32">Vendor</th>
                    <th class="py-2 px-3 font-semibold text-gray-600 w-24">Tarif</th>
                    <th class="py-2 px-3 font-semibold text-gray-600 w-16 text-center">Ukuran</th>
                    <th class="py-2 px-3 font-semibold text-gray-600 w-32 text-right">Harga</th>
                    <th class="py-2 px-3 font-semibold text-gray-600 w-28 text-center">Tanggal Awal</th>
                    <th class="py-2 px-3 font-semibold text-gray-600 w-28 text-center">Tanggal Akhir</th>
                    <th class="py-2 px-3 font-semibold text-gray-600 w-40">Keterangan</th>
                    <th class="py-2 px-3 text-center font-semibold text-gray-600 w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                @forelse ($pricelists as $index => $pricelist)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3 text-center">{{ $pricelists->firstItem() + $index }}</td>
                        <td class="py-2 px-3">{{ $pricelist->vendor }}</td>
                        <td class="py-2 px-3">{{ $pricelist->tarif }}</td>
                        <td class="py-2 px-3 text-center">{{ $pricelist->ukuran_kontainer }} ft</td>
                        <td class="py-2 px-3 text-right">Rp {{ rtrim(rtrim(number_format($pricelist->harga, 2, ',', '.'), '0'), ',') }}</td>
                        <td class="py-2 px-3 text-center">{{ \Carbon\Carbon::parse($pricelist->tanggal_harga_awal)->format('d-m-Y') }}</td>
                        <td class="py-2 px-3 text-center">{{ $pricelist->tanggal_harga_akhir ? \Carbon\Carbon::parse($pricelist->tanggal_harga_akhir)->format('d-m-Y') : '-' }}</td>
                        <td class="py-2 px-3">{{ $pricelist->keterangan ?? '-' }}</td>
                        <td class="py-2 px-3 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('master.pricelist-sewa-kontainer.edit', $pricelist->id) }}" class="bg-yellow-500 text-white py-1 px-3 rounded-md hover:bg-yellow-600 transition-colors duration-200 text-xs">Edit</a>
                                <form action="{{ route('master.pricelist-sewa-kontainer.destroy', $pricelist->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white py-1 px-3 rounded-md hover:bg-red-600 transition-colors duration-200 text-xs">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-4 px-6 text-center text-gray-500">Tidak ada data pricelist yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $pricelists->links() }}
    </div>
</div>
@endsection
