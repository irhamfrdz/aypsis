@extends('layouts.app')

@section('title', 'Semua Surat Jalan Bongkaran')

@section('content')
<div class="flex-1 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Semua Surat Jalan Bongkaran</h1>
            <nav class="flex text-sm text-gray-600 mt-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-2">/</span>
                <span class="text-gray-500">Semua Surat Jalan Bongkaran</span>
            </nav>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
            <button type="button" class="ml-auto text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('error') }}</span>
            <button type="button" class="ml-auto text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Semua Surat Jalan Bongkaran</h2>
                    <p class="text-sm text-gray-600 mt-1">Menampilkan seluruh data surat jalan tanpa filter kapal/voyage</p>
                </div>
                <a href="{{ route('surat-jalan-bongkaran.select-ship') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Filter
                </a>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <!-- Search Form -->
            <form method="GET" action="{{ route('surat-jalan-bongkaran.all') }}" class="mb-6">
                <div class="flex gap-2">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nomor surat jalan, kontainer, seal, kapal, voyage..." 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('surat-jalan-bongkaran.all') }}" 
                           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Surat Jalan Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="suratJalanTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Surat Jalan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Voyage</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Plat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Barang</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suratJalans as $index => $sj)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-center">
                                    <div class="relative inline-block text-left">
                                        <button type="button" onclick="event.stopPropagation(); toggleDropdown('dropdown-sj-{{ $sj->id }}')"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>

                                        <div id="dropdown-sj-{{ $sj->id }}" class="hidden absolute left-0 z-50 mt-1 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                                            <div class="py-1">
                                                @can('surat-jalan-bongkaran-update')
                                                <a href="#" onclick="editSuratJalan({{ $sj->id }}); return false;" 
                                                   class="group flex items-center px-3 py-2 text-xs text-purple-700 hover:bg-purple-50 hover:text-purple-900">
                                                    <svg class="mr-2 h-4 w-4 text-purple-400 group-hover:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit Surat Jalan
                                                </a>
                                                @endcan
                                                <a href="#" onclick="printSJ({{ $sj->id }}); return false;" 
                                                   class="group flex items-center px-3 py-2 text-xs text-blue-700 hover:bg-blue-50 hover:text-blue-900">
                                                    <svg class="mr-2 h-4 w-4 text-blue-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                    </svg>
                                                    Print SJ
                                                </a>
                                                <a href="#" onclick="printBA({{ $sj->id }}); return false;" 
                                                   class="group flex items-center px-3 py-2 text-xs text-green-700 hover:bg-green-50 hover:text-green-900">
                                                    <svg class="mr-2 h-4 w-4 text-green-400 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    Print BA
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $suratJalans->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="font-semibold text-gray-900">{{ $sj->nomor_surat_jalan ?: '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->nama_kapal ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->no_voyage ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->term ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->supir ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->no_plat ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $sj->no_kontainer ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($sj->jenis_barang, 30) ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data Surat Jalan</h3>
                                        <p class="text-gray-500">Belum ada surat jalan bongkaran yang tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $suratJalans->links() }}
            </div>
        </div>
    </div>
</div>

<script>
// Toggle dropdown menu
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    allDropdowns.forEach(dropdown => {
        dropdown.classList.add('hidden');
    });
});

// Edit Surat Jalan
function editSuratJalan(id) {
    window.location.href = `/surat-jalan-bongkaran/${id}/edit`;
}

// Print SJ
function printSJ(id) {
    window.open(`/surat-jalan-bongkaran/${id}/print-sj`, '_blank');
}

// Print BA
function printBA(id) {
    window.open(`/surat-jalan-bongkaran/${id}/print-ba`, '_blank');
}
</script>
@endsection
