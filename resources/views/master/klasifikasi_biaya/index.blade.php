@extends('layouts.app')

@section('title','Master Klasifikasi Biaya')

@section('page_title','Master Klasifikasi Biaya')

@section('content')
<div class="p-6 bg-white rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-semibold">Daftar Klasifikasi Biaya</h3>
        <div class="flex items-center space-x-2">
            @can('master-klasifikasi-biaya-create')
            <a href="{{ route('klasifikasi-biaya.create') }}" class="px-3 py-2 bg-green-600 text-white rounded text-sm">Tambah</a>
            <a href="{{ route('klasifikasi-biaya.import-form') }}" class="px-3 py-2 bg-yellow-500 text-white rounded text-sm">Import</a>
            @endcan
            @can('master-klasifikasi-biaya-view')
            <a href="{{ route('klasifikasi-biaya.download-template') }}" class="px-3 py-2 bg-gray-200 rounded text-sm">Download Template</a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 text-green-700">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-4">
        <div class="flex space-x-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama" class="px-3 py-2 border rounded w-64">
            <button class="px-3 py-2 bg-blue-600 text-white rounded">Cari</button>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="text-xs text-gray-500">
                    <th class="px-2 py-2">Nama</th>
                    <th class="px-2 py-2">Deskripsi</th>
                    <th class="px-2 py-2">Status</th>
                    <th class="px-2 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="border-t">
                    <td class="px-2 py-2">{{ $item->nama }}</td>
                    <td class="px-2 py-2">{{ Str::limit($item->deskripsi, 80) }}</td>
                    <td class="px-2 py-2">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                    <td class="px-2 py-2">
                        @can('master-klasifikasi-biaya-update')
                        <a href="{{ route('klasifikasi-biaya.edit', $item) }}" class="text-blue-600 mr-2">Edit</a>
                        @endcan
                        @can('master-klasifikasi-biaya-delete')
                        <form action="{{ route('klasifikasi-biaya.destroy', $item) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Hapus</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-2 py-4 text-center text-gray-500">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection
