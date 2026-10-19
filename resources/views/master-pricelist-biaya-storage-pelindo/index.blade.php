@extends('layouts.app')

@section('title', 'Pricelist Biaya Storage Pelindo')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pricelist Biaya Storage Pelindo</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola tarif storage Pelindo berdasarkan ukuran dan status kontainer.</p>
        </div>
        @can('master-pricelist-biaya-storage-pelindo-create')
            <a href="{{ route('master-pricelist-biaya-storage-pelindo.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Tambah Tarif</a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('master-pricelist-biaya-storage-pelindo.index') }}" class="mb-5 grid grid-cols-1 gap-3 rounded-lg border bg-white p-4 md:grid-cols-4">
        <select name="size_kontainer" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua ukuran</option>
            @foreach (['20', '40', '45'] as $size)
                <option value="{{ $size }}" @selected(request('size_kontainer') === $size)>{{ $size }}'</option>
            @endforeach
        </select>
        <select name="status_kontainer" class="rounded-lg border-gray-300 text-sm">
            <option value="">Full &amp; Empty</option>
            <option value="full" @selected(request('status_kontainer') === 'full')>Full</option>
            <option value="empty" @selected(request('status_kontainer') === 'empty')>Empty</option>
        </select>
        <select name="status" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="non-aktif" @selected(request('status') === 'non-aktif')>Nonaktif</option>
        </select>
        <div class="flex gap-2">
            <button class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white">Filter</button>
            <a href="{{ route('master-pricelist-biaya-storage-pelindo.index') }}" class="rounded-lg border px-4 py-2 text-sm text-gray-700">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-3">Ukuran</th><th class="px-4 py-3">Status Kontainer</th>
                    <th class="px-4 py-3">Tarif Massa</th><th class="px-4 py-3">Status Pricelist</th><th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($pricelists as $pricelist)
                    <tr>
                        <td class="px-4 py-3">{{ $pricelist->size_kontainer }}'</td>
                        <td class="px-4 py-3">{{ strtoupper($pricelist->status_kontainer) }}</td>
                        <td class="px-4 py-3">
                            @forelse ($pricelist->tarifMassa as $tarifMassa)
                                <div class="whitespace-nowrap">Massa {{ rtrim(rtrim(number_format((float) $tarifMassa->massa, 2, '.', ''), '0'), '.') }}: Rp {{ number_format((float) $tarifMassa->tarif, 0, ',', '.') }}</div>
                            @empty
                                <span class="text-gray-400">-</span>
                            @endforelse
                        </td>
                        <td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs {{ $pricelist->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ strtoupper($pricelist->status) }}</span></td>
                        <td class="px-4 py-3 text-right">
                            @can('master-pricelist-biaya-storage-pelindo-update')
                                <a href="{{ route('master-pricelist-biaya-storage-pelindo.edit', $pricelist) }}" class="font-medium text-blue-700 hover:underline">Edit</a>
                            @endcan
                            @can('master-pricelist-biaya-storage-pelindo-delete')
                                <form action="{{ route('master-pricelist-biaya-storage-pelindo.destroy', $pricelist) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus tarif ini?')">
                                    @csrf @method('DELETE')
                                    <button class="font-medium text-red-700 hover:underline">Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada tarif storage Pelindo.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $pricelists->links() }}</div>
</div>
@endsection
