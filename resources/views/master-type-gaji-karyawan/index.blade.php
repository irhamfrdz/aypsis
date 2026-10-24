@extends('layouts.app')
@section('title', 'Master Type Gaji Karyawan')
@section('page_title', 'Master Type Gaji Karyawan')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b p-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Master Type Gaji Karyawan</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola daftar tipe gaji karyawan.</p>
            </div>
            @can('master-type-gaji-karyawan-create')
                <a href="{{ route('master.type-gaji-karyawan.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"><i class="fas fa-plus mr-2"></i>Tambah Type Gaji</a>
            @endcan
        </div>
        @if(session('success'))
            <div role="status" class="m-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        <form method="GET" action="{{ route('master.type-gaji-karyawan.index') }}" class="flex flex-wrap gap-3 border-b bg-gray-50 p-6">
            <label for="type-gaji-search" class="sr-only">Cari tipe gaji</label>
            <input id="type-gaji-search" type="search" name="search" value="{{ request('search') }}" placeholder="Cari ID, nama atau keterangan..." class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <label for="type-gaji-status" class="sr-only">Status</label>
            <select id="type-gaji-status" name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Semua status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>
            <button type="submit" class="rounded-lg bg-gray-700 px-4 py-2 text-sm text-white">Cari</button>
            <a href="{{ route('master.type-gaji-karyawan.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700">Reset</a>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500"><tr>
                    <th class="px-6 py-3">No</th><th class="px-6 py-3">Type Gaji ID</th><th class="px-6 py-3">Nama Type Gaji</th><th class="px-6 py-3">Keterangan</th><th class="px-6 py-3">Status</th><th class="px-6 py-3 text-right">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($types as $type)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500">{{ $types->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $type->formatted_type_gaji_id ?? '-' }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $type->nama }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $type->keterangan ?: '-' }}</td>
                            <td class="px-6 py-4"><span class="rounded-full px-2 py-1 text-xs font-medium {{ $type->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $type->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @can('master-type-gaji-karyawan-update')
                                    <a href="{{ route('master.type-gaji-karyawan.edit', $type) }}" class="mr-3 text-blue-600 hover:text-blue-800">Edit</a>
                                @endcan
                                @can('master-type-gaji-karyawan-delete')
                                    <form method="POST" action="{{ route('master.type-gaji-karyawan.destroy', $type) }}" class="inline" data-confirm="Hapus tipe gaji {{ $type->nama }}?" onsubmit="return confirm(this.dataset.confirm)">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">Tidak ada tipe gaji karyawan yang ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($types->hasPages())<div class="border-t p-6">{{ $types->links() }}</div>@endif
    </div>
</div>
@endsection
