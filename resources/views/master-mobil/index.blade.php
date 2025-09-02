@extends('layouts.app')

@section('title', 'Master Mobil')
@section('page_title', 'Master Mobil')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Daftar Mobil</h2>

    <!-- Tombol Tambah Mobil -->
    <div class="mb-4">
        <a href="{{ route('master.mobil.create') }}" class="inline-block bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors duration-200">
            + Tambah Mobil
        </a>
    </div>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Tabel Daftar Mobil -->
    <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktiva</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plat</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Rangka</th>
                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                @forelse ($mobils as $index => $mobil)
                    <tr class="hover:bg-gray-50">
                        <td class="py-4 px-6">{{ $mobils->firstItem() + $index }}</td>
                        <td class="py-4 px-6 font-mono text-sm">{{ $mobil->aktiva }}</td>
                        <td class="py-4 px-6">{{ $mobil->plat }}</td>
                        <td class="py-4 px-6">{{ $mobil->nomor_rangka }}</td>
                        <td class="py-4 px-6">{{ $mobil->ukuran }}</td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <!-- Tombol Edit -->
                                <a href="{{ route('master.mobil.edit', $mobil->id) }}" class="bg-yellow-500 text-white py-1 px-3 rounded-md hover:bg-yellow-600 transition-colors duration-200 text-sm">
                                    Edit
                                </a>
                                <!-- Tombol Hapus -->
                                <form action="{{ route('master.mobil.destroy', $mobil->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mobil ini?');">
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
                        <td colspan="6" class="py-4 px-6 text-center text-gray-500">
                            Tidak ada data mobil yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $mobils->links() }}
    </div>
</div>
@endsection
