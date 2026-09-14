@extends('layouts.app')

@section('title', 'Pricelist OB Antar Gudang')
@section('page_title', 'Pricelist OB Antar Gudang')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">{{ session('error') }}</div>@endif

        <div class="mb-6 flex items-center justify-between rounded-lg bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4 text-white shadow-lg">
            <div><h1 class="text-2xl font-bold">Pricelist OB Antar Gudang</h1><p class="text-sm text-teal-100">Kelola tarif perpindahan kontainer antar gudang.</p></div>
            @can('master-pricelist-ob-antar-gudang-create')
                <a href="{{ route('master.pricelist-ob-antar-gudang.create') }}" class="rounded bg-white px-4 py-2 text-sm font-semibold text-teal-700">Tambah Pricelist</a>
            @endcan
        </div>

        <div class="mb-6 rounded-lg bg-white p-5 shadow">
            <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <input name="search" value="{{ request('search') }}" placeholder="Cari..." class="rounded border-gray-300 text-sm">
                <select name="size_kontainer" class="rounded border-gray-300 text-sm"><option value="">Semua Size</option><option value="20ft" @selected(request('size_kontainer') === '20ft')>20 ft</option><option value="40ft" @selected(request('size_kontainer') === '40ft')>40 ft</option></select>
                <select name="status_kontainer" class="rounded border-gray-300 text-sm"><option value="">Semua Status Kontainer</option><option value="full" @selected(request('status_kontainer') === 'full')>Full</option><option value="empty" @selected(request('status_kontainer') === 'empty')>Empty</option></select>
                <select name="status_service" class="rounded border-gray-300 text-sm"><option value="">Semua Status Service</option><option value="service" @selected(request('status_service') === 'service')>Service</option><option value="non_service" @selected(request('status_service') === 'non_service')>Bukan Service</option></select>
                <div class="flex gap-2"><button class="flex-1 rounded bg-teal-600 px-4 py-2 text-sm font-medium text-white">Filter</button><a href="{{ route('master.pricelist-ob-antar-gudang.index') }}" class="rounded bg-gray-200 px-4 py-2 text-sm text-gray-700">Reset</a></div>
            </form>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr>
                <th class="px-5 py-3 text-left text-xs font-medium uppercase text-gray-500">Size</th><th class="px-5 py-3 text-left text-xs font-medium uppercase text-gray-500">Status Kontainer</th><th class="px-5 py-3 text-left text-xs font-medium uppercase text-gray-500">Service</th><th class="px-5 py-3 text-left text-xs font-medium uppercase text-gray-500">Biaya</th><th class="px-5 py-3 text-left text-xs font-medium uppercase text-gray-500">Keterangan</th><th class="px-5 py-3 text-right text-xs font-medium uppercase text-gray-500">Aksi</th>
            </tr></thead><tbody class="divide-y divide-gray-200 bg-white">
            @forelse($pricelists as $pricelist)<tr><td class="px-5 py-4 text-sm">{{ $pricelist->size_kontainer_label }}</td><td class="px-5 py-4 text-sm">{{ $pricelist->status_kontainer_label }}</td><td class="px-5 py-4 text-sm">{{ $pricelist->status_service_label }}</td><td class="px-5 py-4 text-sm font-medium">{{ $pricelist->formatted_biaya }}</td><td class="px-5 py-4 text-sm">{{ $pricelist->keterangan ?: '-' }}</td><td class="px-5 py-4 text-right text-sm">
                @can('master-pricelist-ob-antar-gudang-update')<a class="mr-3 text-indigo-600" href="{{ route('master.pricelist-ob-antar-gudang.edit', $pricelist) }}">Edit</a>@endcan
                @can('master-pricelist-ob-antar-gudang-delete')<form class="inline" method="POST" action="{{ route('master.pricelist-ob-antar-gudang.destroy', $pricelist) }}" onsubmit="return confirm('Hapus pricelist ini?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>@endcan
            </td></tr>@empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-500">Belum ada pricelist OB Antar Gudang.</td></tr>
            @endforelse
            </tbody></table></div>
            <div class="border-t px-5 py-3">{{ $pricelists->links() }}</div>
        </div>
    </div>
</div>
@endsection
