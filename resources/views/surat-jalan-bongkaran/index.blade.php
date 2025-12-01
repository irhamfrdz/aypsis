@extends('layouts.app')

@section('title', 'Surat Jalan Bongkaran')

@section('content')
<div class="flex-1 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Surat Jalan Bongkaran</h1>
            <nav class="flex text-sm text-gray-600 mt-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-2">/</span>
                <span class="text-gray-500">Surat Jalan Bongkaran</span>
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
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Bill of Lading (BL)</h2>
                    <div class="flex items-center gap-4 mt-2">
                        <span class="text-sm text-gray-600">
                            <span class="font-medium">Kapal:</span> {{ $selectedKapal }}
                        </span>
                        <span class="text-sm text-gray-600">
                            <span class="font-medium">Voyage:</span> {{ $selectedVoyage }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('surat-jalan-bongkaran.select-ship') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Ganti Kapal/Voyage
                </a>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <!-- Search Form -->
            <form method="GET" action="{{ route('surat-jalan-bongkaran.index') }}" class="mb-6">
                <input type="hidden" name="nama_kapal" value="{{ $selectedKapal }}">
                <input type="hidden" name="no_voyage" value="{{ $selectedVoyage }}">
                
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               id="search" 
                               name="search" 
                               placeholder="Cari nomor BL, container, seal, kapal, voyage, barang..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Cari
                        </button>
                        <a href="{{ route('surat-jalan-bongkaran.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="blTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor BL</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Voyage</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Container</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Seal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Container</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tonnage</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Bongkar</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($bls as $index => $bl)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-center">
                                    <div class="relative inline-block text-left">
                                        <button type="button" onclick="toggleDropdown('dropdown-{{ $bl->id }}')"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>

                                        <div id="dropdown-{{ $bl->id }}" class="hidden absolute left-0 z-50 mt-1 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                                            <div class="py-1">
                                                <a href="#" onclick="buatSuratJalan({{ $bl->id }}); return false;" 
                                                   class="group flex items-center px-3 py-2 text-xs text-indigo-700 hover:bg-indigo-50 hover:text-indigo-900">
                                                    <svg class="mr-2 h-4 w-4 text-indigo-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    Buat Surat Jalan
                                                </a>
                                                <a href="#" onclick="printSJ({{ $bl->id }}); return false;" 
                                                   class="group flex items-center px-3 py-2 text-xs text-blue-700 hover:bg-blue-50 hover:text-blue-900">
                                                    <svg class="mr-2 h-4 w-4 text-blue-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                    </svg>
                                                    Print SJ
                                                </a>
                                                <a href="#" onclick="printBA({{ $bl->id }}); return false;" 
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
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $bls->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="font-semibold text-gray-900">{{ $bl->nomor_bl ?: '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $bl->nama_kapal ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $bl->no_voyage ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $bl->nomor_kontainer ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $bl->no_seal ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $bl->tipe_kontainer ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $bl->size_kontainer ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($bl->nama_barang, 30) ?: '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $bl->tonnage ? number_format($bl->tonnage, 2) . ' Ton' : '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $bl->volume ? number_format($bl->volume, 3) . ' m³' : '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($bl->status_bongkar)
                                        @php
                                            $badgeClass = match($bl->status_bongkar) {
                                                'Sudah Bongkar' => 'bg-green-100 text-green-800',
                                                'Belum Bongkar' => 'bg-yellow-100 text-yellow-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                            {{ $bl->status_bongkar }}
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data BL</h3>
                                        <p class="text-gray-500">Belum ada data Bill of Lading yang tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($bls->hasPages())
                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Menampilkan {{ $bls->firstItem() }} sampai {{ $bls->lastItem() }} 
                        dari {{ $bls->total() }} data
                    </div>
                    <div>
                        {{ $bls->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle dropdown menu for action buttons
function toggleDropdown(dropdownId) {
    // Close all other dropdowns first
    document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
        if (dropdown.id !== dropdownId) {
            dropdown.classList.add('hidden');
        }
    });
    // Toggle the clicked dropdown
    const dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleDropdown"]') && !event.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

// Buat Surat Jalan function
function buatSuratJalan(blId) {
    // TODO: Implement buat surat jalan functionality
    // For now, just show an alert
    alert('Buat Surat Jalan untuk BL ID: ' + blId);
    // You can redirect to create route like:
    // window.location.href = '/surat-jalan-bongkaran/create-from-bl/' + blId;
}

// Print SJ function
function printSJ(blId) {
    // TODO: Implement print SJ functionality
    // For now, just show an alert
    alert('Print SJ untuk BL ID: ' + blId);
    // You can redirect to print route like:
    // window.open('/surat-jalan-bongkaran/print-sj/' + blId, '_blank');
}

// Print BA function
function printBA(blId) {
    // TODO: Implement print BA functionality
    // For now, just show an alert
    alert('Print BA untuk BL ID: ' + blId);
    // You can redirect to print route like:
    // window.open('/surat-jalan-bongkaran/print-ba/' + blId, '_blank');
}
</script>
@endpush