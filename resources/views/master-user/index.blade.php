@extends('layouts.app')

@section('title', 'Master User')
@section('page_title', 'Master User')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Master User</h1>
                    <p class="mt-1 text-sm text-gray-600">Kelola pengguna sistem dan hak akses mereka</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('master.user.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Pengguna
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('master.user.index') }}" id="searchForm">
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari Pengguna
                        </label>
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari berdasarkan username atau nama karyawan..."
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                                   onkeyup="handleSearchInput()">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 sm:items-end">
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('master.user.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Hapus Filter
                            </a>
                        @endif
                    </div>
                </div>
            </form>
            @if(request('search'))
                <div class="mt-4 p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-indigo-800">
                            Menampilkan <strong>{{ $users->total() }}</strong> hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
                        </p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Rows Per Page Selection --}}
        @include('components.rows-per-page', [
            'routeName' => 'master.user.index',
            'paginator' => $users,
            'entityName' => 'pengguna',
            'entityNamePlural' => 'pengguna'
        ])

    <!-- Tabel Daftar Pengguna -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Daftar Pengguna</h3>
            <p class="mt-1 text-sm text-gray-600">Kelola dan pantau semua pengguna sistem</p>
        </div>

        <div class="overflow-x-auto table-container">
            <table class="min-w-full divide-y divide-gray-200 resizable-table" id="masterUserTable">
                <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
                    <tr><th class="resizable-th px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">
                            <div class="flex items-center">
                                <span>No</span>
                            </div>
                        <div class="resize-handle"></div></th><th class="resizable-th px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Username
                            </div>
                        <div class="resize-handle"></div></th><th class="resizable-th px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="position: relative;">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Karyawan Terkait
                            </div>
                        <div class="resize-handle"></div></th><th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                                Aksi
                            </div>
                        </th></tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-[10px]">
                    @forelse ($users as $index => $user)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->username }}</div>
                            </td>
                            <td class="px-4 py-2">
                                @if($user->karyawan)
                                    <div class="text-sm">
                                        <div class="text-gray-900 flex items-center">
                                            {{ $user->karyawan->nama_lengkap }}
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm text-gray-500 italic">Tidak Terkait</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('master.user.edit', $user->id) }}"
                                       class="text-yellow-600 hover:text-yellow-900 transition-colors duration-150"
                                       title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <span class="text-gray-300">|</span>

                                    <!-- Audit Log Button -->
                                    @can('audit-log-view')
                                        <button type="button"
                                                class="audit-log-btn text-purple-600 hover:text-purple-800 hover:underline font-medium cursor-pointer"
                                                data-model-type="{{ get_class($user) }}"
                                                data-model-id="{{ $user->id }}"
                                                data-item-name="{{ $user->username }}"
                                                title="Lihat Riwayat Perubahan">
                                            Riwayat
                                        </button>
                                        <span class="text-gray-300">|</span>
                                    @endcan

                                    <form action="{{ route('master.user.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-150" title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-2 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada data pengguna</h3>
                                    <p class="text-sm text-gray-500">Belum ada pengguna yang terdaftar dalam sistem.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('master.user.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Tambah Pengguna Pertama
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @include('components.modern-pagination', ['paginator' => $users, 'routeName' => 'master.user.index'])

    </div>
</div>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

@endsection

@push('scripts')
<script>
let searchTimeout;

function handleSearchInput() {
    clearTimeout(searchTimeout);
    
    searchTimeout = setTimeout(() => {
        const searchInput = document.getElementById('search');
        const searchValue = searchInput.value.trim();
        
        // Auto submit form when typing (debounced)
        if (searchValue.length >= 3 || searchValue.length === 0) {
            document.getElementById('searchForm').submit();
        }
    }, 500); // Wait 500ms after user stops typing
}

document.addEventListener('DOMContentLoaded', function() {
    // Focus search input on page load if there's no search value
    const searchInput = document.getElementById('search');
    if (!searchInput.value) {
        searchInput.focus();
    }
    
    // Handle Enter key press
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('searchForm').submit();
        }
    });
    
    // Clear search when escape key is pressed
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchInput.value = '';
            if (new URLSearchParams(window.location.search).get('search')) {
                const currentPerPage = new URLSearchParams(window.location.search).get('per_page');
                const baseUrl = '{{ route("master.user.index") }}';
                window.location.href = currentPerPage ? `${baseUrl}?per_page=${currentPerPage}` : baseUrl;
            }
        }
    });
});
</script>
@endpush

<style>
/* Sticky Table Header Styles */
.sticky-table-header {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: rgb(249 250 251); /* bg-gray-50 */
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
}

/* Enhanced table container for better scrolling */
.table-container {
    max-height: calc(100vh - 300px); /* Adjust based on your layout */
    overflow-y: auto;
    border: 1px solid rgb(229 231 235); /* border-gray-200 */
    border-radius: 0.5rem;
}

/* Smooth scrolling for better UX */
.table-container {
    scroll-behavior: smooth;
}

/* Table header cells need specific background to avoid transparency issues */
.sticky-table-header th {
    background-color: rgb(249 250 251) !important;
    border-bottom: 1px solid rgb(229 231 235);
}

/* Optional: Add a subtle border when scrolling */
.table-container.scrolled .sticky-table-header {
    border-bottom: 2px solid rgb(59 130 246); /* blue-500 */
}

/* Ensure dropdown menus appear above sticky header */
.relative.group .absolute {
    z-index: 20;
}
</style>
