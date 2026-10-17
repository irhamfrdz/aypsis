@extends('layouts.app')

@section('title', 'Master Penerima Bukti Potong PPh')
@section('page_title', 'Penerima Bukti Potong PPh')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Penerima Bukti Potong PPh</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola identitas penerima bukti potong PPh.</p>
        </div>
        @can('master-penerima-bukti-potong-pph-create')
            <a href="{{ route('master.penerima-bukti-potong-pph.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Tambah Penerima</a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('master.penerima-bukti-potong-pph.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-lg border border-gray-200 bg-white p-4">
        <div class="min-w-64 flex-1">
            <label for="search" class="mb-1 block text-sm font-medium text-gray-700">Cari nama, NPWP, atau NIK</label>
            <input id="search" name="search" type="search" value="{{ request('search') }}" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Ketik kata kunci">
        </div>
        <div>
            <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status" class="rounded-lg border-gray-300 text-sm">
                <option value="">Semua</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Cari</button>
    </form>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-gray-700">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">NPWP</th>
                    <th class="px-4 py-3">NIK</th>
                    <th class="px-4 py-3">Kontak</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($penerimas as $penerima)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $penerima->nama }}<div class="mt-1 max-w-xs truncate text-xs font-normal text-gray-500" title="{{ $penerima->alamat }}">{{ $penerima->alamat }}</div></td>
                        <td class="px-4 py-3 text-gray-700">{{ $penerima->npwp ?: '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $penerima->nik ?: '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $penerima->email ?: '-' }}<div class="text-xs text-gray-500">{{ $penerima->telepon }}</div></td>
                        <td class="px-4 py-3">{{ $penerima->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                @can('master-penerima-bukti-potong-pph-update')
                                    <a href="{{ route('master.penerima-bukti-potong-pph.edit', $penerima) }}" class="font-medium text-blue-700 hover:underline">Edit</a>
                                @endcan
                                @can('master-penerima-bukti-potong-pph-delete')
                                    <form method="POST" action="{{ route('master.penerima-bukti-potong-pph.destroy', $penerima) }}" onsubmit="return confirm('Hapus penerima ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-700 hover:underline">Hapus</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada penerima yang sesuai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $penerimas->links() }}</div>
</div>
@endsection
