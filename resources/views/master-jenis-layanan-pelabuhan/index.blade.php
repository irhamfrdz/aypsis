@extends('layouts.app')

@section('title', 'Jenis Layanan Pelabuhan')
@section('page_title', 'Jenis Layanan Pelabuhan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 flex items-center justify-between">
        <h1 class="text-lg font-semibold">Jenis Layanan Pelabuhan</h1>
        @can('master-jenis-layanan-pelabuhan-create')
            <a href="{{ route('master.jenis-layanan-pelabuhan.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">Tambah</a>
        @endcan
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('master.jenis-layanan-pelabuhan.index') }}" class="mb-4">
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari.." class="px-3 py-2 border border-gray-300 rounded-md w-full">
                <button class="px-3 py-2 bg-blue-600 text-white rounded-md">Cari</button>
            </div>
        </form>

        @if($items->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $item)
                        <tr>
                            <td class="px-4 py-2 text-sm">{{ $loop->iteration + ($items->firstItem() - 1) }}</td>
                            <td class="px-4 py-2 text-sm">{{ $item->nama }}</td>
                            <td class="px-4 py-2 text-sm">
                                @can('master-jenis-layanan-pelabuhan-edit')
                                    <a href="{{ route('master.jenis-layanan-pelabuhan.edit', $item) }}" class="text-blue-600">Edit</a>
                                @endcan
                                @can('master-jenis-layanan-pelabuhan-delete')
                                    <form method="POST" action="{{ route('master.jenis-layanan-pelabuhan.destroy', $item) }}" class="inline" onsubmit="return confirm('Hapus data?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 ml-3">Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @include('components.modern-pagination', ['paginator' => $items])
        @else
            <div class="text-center py-8 text-gray-500">Belum ada data Jenis Layanan Pelabuhan.</div>
        @endif
    </div>
</div>
@endsection
