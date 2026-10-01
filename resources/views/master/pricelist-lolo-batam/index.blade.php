@extends('layouts.app')

@section('title', 'Pricelist LOLO Batam')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pricelist LOLO Batam</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola tarif lift on/lift off khusus operasional Batam.</p>
        </div>
        @can('master-pricelist-lolo-batam-create')
            <a href="{{ route('master.pricelist-lolo-batam.create') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700">
                <i class="fas fa-plus" aria-hidden="true"></i>Tambah Pricelist
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('master.pricelist-lolo-batam.index') }}" class="grid grid-cols-1 items-end gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm md:grid-cols-12">
        <div class="md:col-span-5">
            <label for="q" class="mb-1 block text-sm font-medium text-gray-700">Pencarian</label>
            <input type="search" id="q" name="q" value="{{ request('q') }}" placeholder="Keterangan" class="h-10 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div class="md:col-span-2">
            <label for="size" class="mb-1 block text-sm font-medium text-gray-700">Ukuran</label>
            <select id="size" name="size" class="h-10 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Semua</option>
                @foreach (['20', '40', '45'] as $size)
                    <option value="{{ $size }}" @selected(request('size') === $size)>{{ $size }} Feet</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status" class="h-10 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Semua</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="non-aktif" @selected(request('status') === 'non-aktif')>Tidak Aktif</option>
            </select>
        </div>
        <div class="flex gap-2 md:col-span-3 md:justify-end">
            <a href="{{ route('master.pricelist-lolo-batam.index') }}" class="inline-flex h-10 items-center rounded-lg border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
            <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700"><i class="fas fa-search" aria-hidden="true"></i>Filter</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3">No</th>
                        <th class="px-5 py-3 text-center">Ukuran</th>
                        <th class="px-5 py-3 text-right">Tarif</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3">Keterangan</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pricelists as $pricelist)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 text-gray-500">{{ $pricelists->firstItem() + $loop->index }}</td>
                            <td class="px-5 py-4 text-center text-gray-700">{{ $pricelist->size }} Feet</td>
                            <td class="px-5 py-4 text-right font-semibold text-indigo-700">{{ $pricelist->formatted_tarif }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $pricelist->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $pricelist->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}</span>
                            </td>
                            <td class="max-w-xs px-5 py-4 text-gray-600"><span class="line-clamp-2">{{ $pricelist->keterangan ?: '-' }}</span></td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                @can('master-pricelist-lolo-batam-update')
                                    <a href="{{ route('master.pricelist-lolo-batam.edit', $pricelist) }}" class="mr-3 font-medium text-indigo-600 hover:text-indigo-800">Edit</a>
                                @endcan
                                @can('master-pricelist-lolo-batam-delete')
                                    <form method="POST" action="{{ route('master.pricelist-lolo-batam.destroy', $pricelist) }}" class="inline" onsubmit="return confirm('Hapus pricelist LOLO Batam ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 hover:text-red-800">Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-gray-500"><i class="fas fa-inbox mb-3 block text-3xl text-gray-300" aria-hidden="true"></i>Belum ada data Pricelist LOLO Batam.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($pricelists->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">{{ $pricelists->links() }}</div>
        @endif
    </div>
</div>
@endsection
