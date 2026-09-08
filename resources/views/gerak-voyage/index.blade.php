@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-ship mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Tanggal Gerak Voyage</h1>
                    <p class="text-gray-600">Manajemen data pergerakan kapal</p>
                </div>
            </div>
            <div>
                <a href="{{ route('gerak-voyage.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-plus mr-1"></i> Tambah Data
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal / Voyage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai Berlayar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berlabuh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sandar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai Bongkar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai Bongkar</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($gerakVoyages as $index => $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $item->nama_kapal }}</div>
                            <div class="text-sm text-gray-500">{{ $item->no_voyage }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tanggal_mulai_berlayar ? \Carbon\Carbon::parse($item->tanggal_mulai_berlayar)->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tanggal_berlabuh ? \Carbon\Carbon::parse($item->tanggal_berlabuh)->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tanggal_sandar ? \Carbon\Carbon::parse($item->tanggal_sandar)->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tanggal_mulai_bongkar ? \Carbon\Carbon::parse($item->tanggal_mulai_bongkar)->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tanggal_selesai_bongkar ? \Carbon\Carbon::parse($item->tanggal_selesai_bongkar)->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <a href="{{ route('gerak-voyage.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('gerak-voyage.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
