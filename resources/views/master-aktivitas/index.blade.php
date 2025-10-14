@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Master Aktivitas</h1>
                <p class="text-xs text-gray-600 mt-1">Kelola data master aktivitas</p>
            </div>
            <a href="{{ route('master-aktivitas.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Aktivitas
            </a>
        </div>

        <!-- Search and Filter Section -->
        <div class="p-4 border-b border-gray-200">
            <form method="GET" action="{{ route('master-aktivitas.index') }}" class="flex flex-col sm:flex-row gap-3">
                <!-- Search Input -->
                <div class="flex-1">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari kode, nama aktivitas, atau catatan..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <!-- Status Filter -->
                <div class="sm:w-40">
                    <select name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="all">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Rows Per Page -->
                <div class="sm:w-32">
                    @include('components.rows-per-page')
                </div>

                <!-- Search Button -->
                <button type="submit"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-150 text-sm whitespace-nowrap">
                    <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Cari
                </button>

                <!-- Reset Button -->
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('master-aktivitas.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-150 text-sm whitespace-nowrap">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 mx-4 mt-4 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 mx-4 mt-4 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Aktivitas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($aktivitas as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item->kode }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $item->nama_aktivitas }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            @if($item->catatan)
                                {{ Str::limit($item->catatan, 50) }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $item->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $item->status === 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('master-aktivitas.show', $item) }}"
                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-150"
                                   title="Lihat Detail">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                <a href="{{ route('master-aktivitas.edit', $item) }}"
                                   class="text-yellow-600 hover:text-yellow-800 transition-colors duration-150"
                                   title="Edit">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>

                                <form action="{{ route('master-aktivitas.destroy', $item) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800 transition-colors duration-150"
                                            title="Hapus"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data aktivitas</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan aktivitas baru.</p>
                            <div class="mt-6">
                                <a href="{{ route('master-aktivitas.create') }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Aktivitas
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($aktivitas->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                @include('components.modern-pagination', ['paginator' => $aktivitas])
            </div>
        @endif
    </div>
</div>
@endsection