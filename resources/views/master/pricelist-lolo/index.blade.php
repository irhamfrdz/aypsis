@extends('layouts.app')

@section('title', 'Pricelist Biaya LOLO')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pricelist Biaya LOLO</h1>
                <p class="text-gray-500 mt-2 text-lg">Kelola tarif Lift-On / Lift-Off terminal petikemas</p>
            </div>
            @can('master-pricelist-lolo-create')
            <div>
                <a href="{{ route('master.pricelist-lolo.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-indigo-200 shadow-lg transition-all transform hover:-translate-y-0.5 active:scale-95 duration-200">
                    <i class="fas fa-plus-circle mr-2 text-lg"></i> Tambah Tarif LOLO
                </a>
            </div>
            @endcan
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl p-6 mb-8 shadow-sm flex items-center animate-pulse">
        <div class="flex-shrink-0 bg-emerald-500 rounded-full p-2">
            <i class="fas fa-check text-white"></i>
        </div>
        <div class="ml-4">
            <p class="text-emerald-800 font-bold tracking-wide">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form method="GET" action="{{ route('master.pricelist-lolo.index') }}" class="mb-10">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="relative group">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 transition-colors group-focus-within:text-indigo-600">Terminal</label>
                        <div class="relative">
                            <i class="fas fa-ship absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            <input type="text" name="terminal" class="w-full pl-11 pr-4 py-3 bg-gray-50 border-0 ring-1 ring-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none" placeholder="Cari terminal..." value="{{ request('terminal') }}">
                        </div>
                    </div>
                    <div class="relative group">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Ukuran</label>
                        <select name="size" class="w-full px-4 py-3 bg-gray-50 border-0 ring-1 ring-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none cursor-pointer">
                            <option value="">Semua Ukuran</option>
                            <option value="20" {{ request('size') == '20' ? 'selected' : '' }}>20'</option>
                            <option value="40" {{ request('size') == '40' ? 'selected' : '' }}>40'</option>
                            <option value="45" {{ request('size') == '45' ? 'selected' : '' }}>45'</option>
                        </select>
                    </div>
                    <div class="relative group">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Kategori</label>
                        <select name="kategori" class="w-full px-4 py-3 bg-gray-50 border-0 ring-1 ring-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none cursor-pointer">
                            <option value="">Semua Kategori</option>
                            <option value="Full" {{ request('kategori') == 'Full' ? 'selected' : '' }}>Full</option>
                            <option value="Empty" {{ request('kategori') == 'Empty' ? 'selected' : '' }}>Empty</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-3 bg-gray-900 hover:bg-black text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-gray-200 active:scale-95 duration-200">
                            <i class="fas fa-search mr-2"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto rounded-xl ring-1 ring-gray-100">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">No</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">Terminal</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">Aktivitas</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">Ukuran</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">Kategori</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-black text-gray-500 uppercase tracking-widest">Tarif</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-widest">Status</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-widest">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($pricelists as $pricelist)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-400">
                                {{ ($pricelists->currentPage() - 1) * $pricelists->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-sm font-bold text-gray-900">{{ $pricelist->terminal }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1 text-xs font-black rounded-lg border {{ $pricelist->tipe_aktivitas === 'Lift On' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-purple-50 text-purple-700 border-purple-100' }}">
                                    {{ strtoupper($pricelist->tipe_aktivitas) }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600 font-bold">
                                {{ $pricelist->size }}'
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm">
                                <span class="text-gray-900 font-bold">{{ $pricelist->kategori }}</span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-black text-indigo-600">
                                {{ $pricelist->formatted_tarif }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                @if($pricelist->status === 'aktif')
                                <span class="px-3 py-1.5 text-[10px] font-black uppercase tracking-tighter bg-emerald-100 text-emerald-700 rounded-full border border-emerald-200">
                                    AKTIF
                                </span>
                                @else
                                <span class="px-3 py-1.5 text-[10px] font-black uppercase tracking-tighter bg-rose-100 text-rose-700 rounded-full border border-rose-200">
                                    TIDAK AKTIF
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-center text-sm">
                                <div class="flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-0 translate-x-4">
                                    @can('master-pricelist-lolo-update')
                                    <a href="{{ route('master.pricelist-lolo.edit', $pricelist->id) }}" class="p-2.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl transition-all" title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('master-pricelist-lolo-delete')
                                    <form action="{{ route('master.pricelist-lolo.destroy', $pricelist->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data pricelist ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl transition-all" title="Hapus Data">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="inline-flex items-center justify-center p-6 bg-gray-50 rounded-3xl mb-4">
                                    <i class="fas fa-folder-open text-5xl text-gray-200"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">Belum ada data</h3>
                                <p class="text-gray-500 mt-2 max-w-xs mx-auto text-sm">Silahkan tambahkan tarif baru dengan menekan tombol "Tambah Tarif LOLO" di atas.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 border-t border-gray-50 pt-8">
                {{ $pricelists->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
