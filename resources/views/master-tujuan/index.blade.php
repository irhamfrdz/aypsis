@extends('layouts.app')

@section('title', 'Master Tujuan')
@section('page_title', 'Master Tujuan')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Daftar Tujuan</h2>
            <a href="{{ route('master.tujuan.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                Tambah Tujuan
            </a>
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
                            <th class="py-3 px-4">UJ 40ft</th>
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
                                <td class="py-3 px-4">Rp {{ number_format($tujuan->uang_jalan_40 ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">Rp {{ number_format($tujuan->antar_20 ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">Rp {{ number_format($tujuan->antar_40 ?? 0, 0, ',', '.') }}</td>

                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('master.tujuan.edit', $tujuan) }}" class="text-blue-500 hover:underline">Edit</a>
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
@endsection
