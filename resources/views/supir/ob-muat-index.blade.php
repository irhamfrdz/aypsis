<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>OB Muat - Daftar Kontainer - AYPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <a href="{{ url('supir/ob-muat') }}" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-xl font-bold text-gray-800">OB Muat - Daftar Kontainer</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="window.location.reload()" class="text-gray-600 hover:text-gray-900" title="Refresh halaman">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                    <span class="hidden sm:block text-sm text-gray-600">Halo, {{ Auth::user()->name }}!</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto mt-4 px-4 flex-grow">
        <div class="max-w-4xl mx-auto">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded mb-4 text-sm" role="alert">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-4 text-sm" role="alert">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Debug Info (Tambahan untuk troubleshooting) -->
            @if(request()->has('debug'))
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-3 py-2 rounded mb-4 text-xs" role="alert">
                <div class="font-bold mb-2">Debug Information:</div>
                <div class="space-y-1">
                    <p><strong>Total Containers:</strong> {{ $bls->count() }}</p>
                    <p><strong>Sudah OB Count:</strong> {{ $bls->where('sudah_ob', true)->count() }}</p>
                    <p><strong>Belum OB Count:</strong> {{ $bls->where('sudah_ob', false)->count() }}</p>
                    <div><strong>Container Details:</strong></div>
                    <ul class="ml-4 space-y-1">
                        @foreach($bls as $item)
                        <li>
                            ID: {{ $item->id }} | 
                            Container: {{ $item->nomor_kontainer }} | 
                            sudah_ob: {{ $item->sudah_ob ? 'TRUE' : 'FALSE' }} | 
                            supir_id: {{ $item->supir_id ?? 'NULL' }} |
                            tanggal_ob: {{ $item->tanggal_ob ?? 'NULL' }}
                        </li>
                        @endforeach
                    </ul>
                    <p class="mt-2"><strong>Current Time:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
            @endif

            <!-- Header Info Kapal & Voyage -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">OB Muat (Step 2)</h2>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Kapal:</span> {{ $selectedKapal }} | 
                                <span class="font-medium">Voyage:</span> {{ $selectedVoyage }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center space-x-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Total</p>
                                <p class="text-lg font-bold text-blue-600">{{ $bls->count() }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Sudah OB</p>
                                <p class="text-lg font-bold text-green-600">{{ $bls->where('sudah_ob', true)->count() }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Belum OB</p>
                                <p class="text-lg font-bold text-orange-600">{{ $bls->where('sudah_ob', false)->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Kontainer -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Daftar Nomor Kontainer</h3>
                            <p class="text-sm text-gray-600">Pilih kontainer untuk melakukan OB Muat</p>
                        </div>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="mb-3">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   id="searchInput" 
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                   placeholder="Cari nomor kontainer, seal, barang...">
                        </div>
                    </div>
                    
                    <!-- Filter Buttons -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-700">Filter:</span>
                        <button onclick="filterStatus('all')" 
                                class="filter-btn px-3 py-1 text-xs font-medium rounded-full border transition-colors bg-orange-500 text-white border-orange-500" 
                                data-filter="all">
                            Semua (<span id="count-all">{{ $bls->count() }}</span>)
                        </button>
                        <button onclick="filterStatus('sudah')" 
                                class="filter-btn px-3 py-1 text-xs font-medium rounded-full border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors" 
                                data-filter="sudah">
                            Sudah OB (<span id="count-sudah">{{ $bls->where('sudah_ob', true)->count() }}</span>)
                        </button>
                        <button onclick="filterStatus('belum')" 
                                class="filter-btn px-3 py-1 text-xs font-medium rounded-full border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors" 
                                data-filter="belum">
                            Belum OB (<span id="count-belum">{{ $bls->where('sudah_ob', false)->count() }}</span>)
                        </button>
                    </div>
                </div>

                @if($bls->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No Kontainer
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Seal
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Barang
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status OB
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bls as $bl)
                                    <tr class="hover:bg-gray-50 kontainer-row" 
                                        data-status="{{ $bl->sudah_ob ? 'sudah' : 'belum' }}"
                                        data-kontainer="{{ strtolower($bl->nomor_kontainer ?? '') }}"
                                        data-seal="{{ strtolower($bl->no_seal ?? '') }}"
                                        data-barang="{{ strtolower($bl->nama_barang ?? '') }}">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $bl->nomor_kontainer ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ $bl->no_seal ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="max-w-xs truncate" title="{{ $bl->nama_barang }}">
                                                {{ $bl->nama_barang ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                            @if($bl->sudah_ob ?? false)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Sudah OB
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Belum OB
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                                            @if($bl->sudah_ob ?? false)
                                                <button type="button" disabled
                                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-500 bg-gray-100 cursor-not-allowed">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Selesai
                                                </button>
                                            @else
                                                <form action="{{ route('supir.ob-muat.process') }}" method="POST" class="inline" 
                                                      onsubmit="return confirm('Yakin ingin memproses OB Muat untuk kontainer {{ $bl->nomor_kontainer }}?')">
                                                    @csrf
                                                    <input type="hidden" name="kapal" value="{{ $selectedKapal }}">
                                                    <input type="hidden" name="voyage" value="{{ $selectedVoyage }}">
                                                    <input type="hidden" name="naik_kapal_id" value="{{ $bl->id }}">
                                                    <button type="submit"
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        OB Muat
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr id="noResultsRow" style="display: none;">
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-sm font-medium">Tidak ada kontainer yang sesuai dengan pencarian</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Kontainer</h3>
                        <p class="text-gray-500 mb-4">
                            Tidak ditemukan kontainer untuk kapal <strong>{{ $selectedKapal }}</strong> 
                            dengan voyage <strong>{{ $selectedVoyage }}</strong>.
                        </p>
                        <a href="{{ url('supir/ob-muat') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Pilih Kapal & Voyage Lain
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <script>
        let currentFilter = 'all';
        
        // Filter by status
        function filterStatus(status) {
            currentFilter = status;
            
            // Update button styles
            document.querySelectorAll('.filter-btn').forEach(btn => {
                if (btn.dataset.filter === status) {
                    btn.className = 'filter-btn px-3 py-1 text-xs font-medium rounded-full border transition-colors bg-orange-500 text-white border-orange-500';
                } else {
                    btn.className = 'filter-btn px-3 py-1 text-xs font-medium rounded-full border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors';
                }
            });
            
            // Apply filter
            applyFilters();
        }
        
        // Search functionality
        document.getElementById('searchInput')?.addEventListener('input', function(e) {
            applyFilters();
        });
        
        function applyFilters() {
            const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
            const rows = document.querySelectorAll('.kontainer-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const status = row.dataset.status;
                const kontainer = row.dataset.kontainer;
                const seal = row.dataset.seal;
                const barang = row.dataset.barang;
                
                // Check status filter
                const statusMatch = currentFilter === 'all' || status === currentFilter;
                
                // Check search term
                const searchMatch = searchTerm === '' || 
                                  kontainer.includes(searchTerm) || 
                                  seal.includes(searchTerm) || 
                                  barang.includes(searchTerm);
                
                if (statusMatch && searchMatch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show "no results" message if needed
            const noResultsRow = document.getElementById('noResultsRow');
            if (noResultsRow) {
                noResultsRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        }
        
        // Auto refresh setiap 30 detik untuk update data terbaru
        setTimeout(function() {
            window.location.reload();
        }, 30000);

        // Debug info
        console.log('OB Muat Index loaded');
        console.log('Selected Kapal: {{ $selectedKapal }}');
        console.log('Selected Voyage: {{ $selectedVoyage }}');
        console.log('Total Containers: {{ $bls->count() }}');
        console.log('Sudah OB:', {{ $bls->where('sudah_ob', true)->count() }});
        console.log('Belum OB:', {{ $bls->where('sudah_ob', false)->count() }});
    </script>
</body>
</html>