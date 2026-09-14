@extends('layouts.app')

@section('title', 'Master Type Bon Amprahan')
@section('page_title', 'Master Type Bon Amprahan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div><h2 class="text-2xl font-bold text-gray-800">Master Type Bon Amprahan</h2><p class="text-sm text-gray-600 mt-1">Kelola jenis bon untuk amprahan.</p></div>
            <a href="{{ route('master.type-bon-amprahan.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"><i class="fas fa-plus mr-2"></i>Tambah Type</a>
        </div>
        <div class="p-6 border-b bg-gray-50">
            <form method="GET" class="flex gap-2">
                <input name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama..." class="flex-1 px-4 py-2 border rounded-lg">
                <button class="px-5 py-2 bg-gray-700 text-white rounded-lg">Cari</button>
                @if(request('search'))<a href="{{ route('master.type-bon-amprahan.index') }}" class="px-5 py-2 bg-gray-400 text-white rounded-lg">Reset</a>@endif
            </form>
        </div>
        @if(session('success'))<div class="m-6 p-4 bg-green-50 text-green-800 border border-green-200 rounded-lg">{{ session('success') }}</div>@endif
        <div class="overflow-x-auto"><table class="w-full"><thead class="bg-gray-50"><tr>
            <th class="px-6 py-3 text-left text-xs uppercase">#</th><th class="px-6 py-3 text-left text-xs uppercase">Kode</th><th class="px-6 py-3 text-left text-xs uppercase">Nama</th><th class="px-6 py-3 text-left text-xs uppercase">Keterangan</th><th class="px-6 py-3 text-left text-xs uppercase">Status</th><th class="px-6 py-3 text-center text-xs uppercase">Aksi</th>
        </tr></thead><tbody class="divide-y">
        @forelse($types as $index => $type)
            <tr><td class="px-6 py-4 text-sm">{{ $types->firstItem() + $index }}</td><td class="px-6 py-4 font-semibold">{{ $type->kode }}</td><td class="px-6 py-4">{{ $type->nama }}</td><td class="px-6 py-4 text-sm">{{ $type->keterangan ?: '-' }}</td><td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full {{ $type->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $type->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span></td><td class="px-6 py-4 text-center"><a class="text-blue-600 mr-3" href="{{ route('master.type-bon-amprahan.edit', $type) }}" title="Edit"><i class="fas fa-edit"></i></a><form class="inline" method="POST" action="{{ route('master.type-bon-amprahan.destroy', $type) }}" onsubmit="return confirm('Hapus type bon ini?')">@csrf @method('DELETE')<button class="text-red-600" title="Hapus"><i class="fas fa-trash"></i></button></form></td></tr>
        @empty
            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada type bon amprahan.</td></tr>
        @endforelse
        </tbody></table></div>
        @if($types->hasPages())<div class="p-6 border-t">{{ $types->links() }}</div>@endif
    </div>
</div>
@endsection
