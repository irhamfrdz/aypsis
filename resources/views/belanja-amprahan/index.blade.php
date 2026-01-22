@extends('layouts.app')

@section('title', 'Belanja Amprahan')
@section('page_title', 'Belanja Amprahan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Daftar Belanja Amprahan</h2>
            <a href="{{ route('belanja-amprahan.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Buat Baru</a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto clean-table">
                <thead>
                    <tr class="text-left text-xs text-gray-500">
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Nomor</th>
                        <th class="px-4 py-2">Tanggal</th>
                        <th class="px-4 py-2">Supplier</th>
                        <th class="px-4 py-2">Total</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="px-4 py-2">{{ $loop->iteration + ($items->currentPage()-1)*$items->perPage() }}</td>
                            <td class="px-4 py-2">{{ $item->nomor }}</td>
                            <td class="px-4 py-2">{{ $item->tanggal?->format('Y-m-d') }}</td>
                            <td class="px-4 py-2">{{ $item->supplier }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($item->total,2,',','.') }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('belanja-amprahan.edit', $item->id) }}" class="text-blue-600 mr-2">Edit</a>
                                <form action="{{ route('belanja-amprahan.destroy', $item->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Hapus data?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
