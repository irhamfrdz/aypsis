@extends('layouts.app')

@section('title', 'Master Gudang Amprahan')
@section('page_title', 'Master Gudang Amprahan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Master Gudang Amprahan</h2>
                    <p class="text-sm text-gray-600 mt-1">Kelola data gudang amprahan</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('master.gudang-amprahan.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 shadow-sm">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Gudang
                    </a>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <form action="{{ route('master.gudang-amprahan.index') }}" method="GET" class="flex gap-2">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari gudang..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <button type="submit" 
                        class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Cari
                </button>
                @if(request('search'))
                <a href="{{ route('master.gudang-amprahan.index') }}" 
                   class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition duration-200">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="m-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama Gudang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($gudangAmprahans as $index => $gudang)
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $gudangAmprahans->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $gudang->nama_gudang }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $gudang->lokasi ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ Str::limit($gudang->keterangan, 50) ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($gudang->status == 'active')
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                <i class="fas fa-check-circle mr-1"></i>Aktif
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                <i class="fas fa-times-circle mr-1"></i>Nonaktif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('master.gudang-amprahan.edit', $gudang->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition duration-200"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('master.gudang-amprahan.destroy', $gudang->id) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus gudang ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 transition duration-200"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p class="text-sm">Tidak ada data gudang amprahan.</p>
                            @if(request('search'))
                            <p class="text-sm mt-2">
                                Tidak ada hasil untuk pencarian "<strong>{{ request('search') }}</strong>"
                            </p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($gudangAmprahans->hasPages())
        <div class="p-6 border-t border-gray-200">
            {{ $gudangAmprahans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
