@extends('layouts.app')

@section('title', 'Master Departemen')
@section('page_title', 'Master Departemen')

@section('content')
<div class="max-w-6xl mx-auto p-4 sm:p-6 space-y-5">
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h1 class="text-xl font-bold text-gray-900">Master Departemen</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola pilihan departemen untuk data karyawan.</p>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-800">{{ $errors->first() }}</div>
    @endif

    @can('master-divisi-create')
        <form action="{{ route('master.departemen.store') }}" method="POST" class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col sm:flex-row gap-3 sm:items-end">
            @csrf
            <div class="flex-1">
                <label for="nama_departemen" class="block text-sm font-medium text-gray-700 mb-1">Nama Departemen</label>
                <input id="nama_departemen" name="nama_departemen" value="{{ old('nama_departemen') }}" required maxlength="255" placeholder="Nama departemen baru" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">Tambah Departemen</button>
        </form>
    @endcan

    <form method="GET" action="{{ route('master.departemen.index') }}" class="flex gap-2">
        <input name="search" value="{{ request('search') }}" placeholder="Cari departemen..." class="flex-1 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
        <button class="px-4 py-2 rounded-lg bg-gray-800 text-white text-sm">Cari</button>
    </form>

    <div class="bg-white border border-gray-200 rounded-xl overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-gray-600"><tr>
                <th class="text-left px-4 py-3">Departemen</th>
                <th class="text-left px-4 py-3">Karyawan</th>
                <th class="text-left px-4 py-3">Status</th>
                <th class="text-right px-4 py-3">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($departemens as $departemen)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $departemen->nama_departemen }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $usage[$departemen->nama_departemen] ?? 0 }}</td>
                        <td class="px-4 py-3">{{ $departemen->is_active ? 'Aktif' : 'Tidak aktif' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @can('master-divisi-update')
                                <button type="button" onclick="document.getElementById('edit-{{ $departemen->id }}').classList.toggle('hidden')" class="text-blue-700 hover:underline">Edit</button>
                            @endcan
                            @can('master-divisi-delete')
                                <form action="{{ route('master.departemen.destroy', $departemen) }}" method="POST" class="inline ml-3" onsubmit="return confirm('Hapus departemen ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-700 hover:underline">Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @can('master-divisi-update')
                        <tr id="edit-{{ $departemen->id }}" class="hidden bg-blue-50">
                            <td colspan="4" class="p-4">
                                <form action="{{ route('master.departemen.update', $departemen) }}" method="POST" class="flex flex-col sm:flex-row gap-3 sm:items-end">
                                    @csrf @method('PUT')
                                    <div class="flex-1"><label class="block text-xs font-medium mb-1">Nama Departemen</label><input name="nama_departemen" value="{{ $departemen->nama_departemen }}" required maxlength="255" class="w-full rounded-lg border-gray-300 text-sm"></div>
                                    <div><label class="block text-xs font-medium mb-1">Status</label><select name="is_active" class="rounded-lg border-gray-300 text-sm"><option value="1" @selected($departemen->is_active)>Aktif</option><option value="0" @selected(!$departemen->is_active)>Tidak aktif</option></select></div>
                                    <button class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @endcan
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada departemen.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $departemens->links() }}
</div>
@endsection
