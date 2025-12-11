<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>OB Bongkar - Daftar Kontainer - AYPSIS</title>
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
                    <a href="{{ route('supir.ob-bongkar') }}" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-xl font-bold text-gray-800">OB Bongkar - Daftar Kontainer</h1>
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
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4 shadow-md" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-bold mb-1">Berhasil!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-4 shadow-md" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-bold mb-1">Proses OB Bongkar Gagal</p>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-4 shadow-md" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-bold mb-1">Terjadi Kesalahan</p>
                            <ul class="list-disc list-inside text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Header Info Kapal & Voyage -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-orange-100 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">OB Bongkar (Step 2)</h2>
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
                                <p class="text-lg font-bold text-orange-600">{{ $bls->count() }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Sudah OB</p>
                                <p class="text-lg font-bold text-green-600">{{ $bls->where('sudah_ob', true)->count() }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Belum OB</p>
                                <p class="text-lg font-bold text-yellow-600">{{ $bls->where('sudah_ob', false)->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Kontainer -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex flex-col space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Daftar Nomor Kontainer</h3>
                                <p class="text-sm text-gray-600">Pilih kontainer untuk melakukan OB Bongkar</p>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           id="searchInput" 
                                           placeholder="Cari nomor kontainer, seal, barang..." 
                                           class="block w-full sm:w-80 pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                           oninput="searchTable()">
                                </div>
                            </div>
                        </div>
                        <!-- Filter Status -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600 font-medium">Filter:</span>
                            <button onclick="filterByStatus('all')" id="filter-all" class="filter-btn px-3 py-1 text-xs font-medium rounded-full border transition-colors bg-orange-600 text-white border-orange-600">
                                Semua ({{ $bls->count() }})
                            </button>
                            <button onclick="filterByStatus('sudah')" id="filter-sudah" class="filter-btn px-3 py-1 text-xs font-medium rounded-full border transition-colors bg-white text-gray-700 border-gray-300 hover:bg-gray-50">
                                Sudah OB ({{ $bls->where('sudah_ob', true)->count() }})
                            </button>
                            <button onclick="filterByStatus('belum')" id="filter-belum" class="filter-btn px-3 py-1 text-xs font-medium rounded-full border transition-colors bg-white text-gray-700 border-gray-300 hover:bg-gray-50">
                                Belum OB ({{ $bls->where('sudah_ob', false)->count() }})
                            </button>
                        </div>
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
                            <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
                                @foreach($bls as $bl)
                                    {{-- Skip rendering if container contains the word 'cargo' (case-insensitive) --}}
                                    @php
                                        $kontainerRaw = $bl->nomor_kontainer ?? '';
                                        // Detect 'cargo' as a word/sub-string using a case-insensitive regex
                                        $isCargo = preg_match('/\bcargo\b/i', $kontainerRaw);
                                    @endphp
                                    @if(!$isCargo)
                                    <tr class="hover:bg-gray-50 table-row" 
                                        data-kontainer="{{ strtolower($bl->nomor_kontainer ?? '') }}"
                                        data-seal="{{ strtolower($bl->no_seal ?? '') }}"
                                        data-barang="{{ strtolower($bl->nama_barang ?? '') }}"
                                        data-status="{{ ($bl->sudah_ob ?? false) ? 'sudah' : 'belum' }}">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 bg-orange-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                                <form action="{{ route('supir.ob-bongkar.process') }}" method="POST" class="inline ob-form" 
                                                      onsubmit="return handleSubmit(this, '{{ $bl->nomor_kontainer }}')">
                                                    @csrf
                                                    <input type="hidden" name="kapal" value="{{ $selectedKapal }}">
                                                    <input type="hidden" name="voyage" value="{{ $selectedVoyage }}">
                                                    <input type="hidden" name="bl_id" value="{{ $bl->id }}">
                                                    <button type="submit"
                                                            class="ob-submit-btn inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <svg class="w-3 h-3 mr-1 icon-default" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                                        </svg>
                                                        <svg class="w-3 h-3 mr-1 icon-loading hidden animate-spin" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                        <span class="btn-text">OB Bongkar</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- No Results Message -->
                    <div id="noResults" class="hidden p-8 text-center border-t border-gray-200">
                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ditemukan</h3>
                        <p class="text-gray-500">
                            Tidak ada kontainer yang sesuai dengan pencarian Anda.
                        </p>
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
                        <a href="{{ route('supir.ob-bongkar') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
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
        // Current filter state
        let currentFilter = 'all';

        // Filter by status
        function filterByStatus(status) {
            currentFilter = status;
            const tableRows = document.querySelectorAll('.table-row');
            const noResults = document.getElementById('noResults');
            let visibleCount = 0;

            // Update button styles
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-orange-600', 'text-white', 'border-orange-600');
                btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
            });
            
            const activeBtn = document.getElementById('filter-' + status);
            activeBtn.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
            activeBtn.classList.add('bg-orange-600', 'text-white', 'border-orange-600');

            // Filter rows
            tableRows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                let isVisible = false;

                if (status === 'all') {
                    isVisible = true;
                } else {
                    isVisible = rowStatus === status;
                }

                if (isVisible) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Apply search filter if there's a search term
            const searchTerm = document.getElementById('searchInput').value.trim();
            if (searchTerm) {
                searchTable();
            }

            // Show/hide no results message
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }

            console.log('Filter applied:', status, '| Visible rows:', visibleCount);
        }

        // Handle form submission with loading state
        function handleSubmit(form, kontainerNo) {
            if (!confirm('Yakin ingin memproses OB Bongkar untuk kontainer ' + kontainerNo + '?\n\nPastikan nomor kontainer sudah benar.')) {
                return false;
            }

            const btn = form.querySelector('.ob-submit-btn');
            const btnText = btn.querySelector('.btn-text');
            const iconDefault = btn.querySelector('.icon-default');
            const iconLoading = btn.querySelector('.icon-loading');

            // Disable button and show loading
            btn.disabled = true;
            btnText.textContent = 'Memproses...';
            iconDefault.classList.add('hidden');
            iconLoading.classList.remove('hidden');

            console.log('%c⏳ Memproses OB Bongkar...', 'color: orange; font-weight: bold;');
            console.log('Kontainer:', kontainerNo);
            console.log('Timestamp:', new Date().toLocaleString());

            return true;
        }

        // Search Function
        function searchTable() {
            const searchInput = document.getElementById('searchInput');
            const searchTerm = searchInput.value.toLowerCase().trim();
            const tableRows = document.querySelectorAll('.table-row');
            const noResults = document.getElementById('noResults');
            let visibleCount = 0;

            tableRows.forEach(row => {
                const kontainer = row.getAttribute('data-kontainer') || '';
                const seal = row.getAttribute('data-seal') || '';
                const barang = row.getAttribute('data-barang') || '';
                const rowStatus = row.getAttribute('data-status');

                // Check if matches search term
                const matchesSearch = !searchTerm || 
                                    kontainer.includes(searchTerm) || 
                                    seal.includes(searchTerm) || 
                                    barang.includes(searchTerm);

                // Check if matches status filter
                const matchesFilter = currentFilter === 'all' || rowStatus === currentFilter;

                // Show row only if matches both search and filter
                const isVisible = matchesSearch && matchesFilter;

                if (isVisible) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }

            // Update visible count in console
            console.log('Search term:', searchTerm, '| Filter:', currentFilter);
            console.log('Visible rows:', visibleCount, 'of', tableRows.length);
        }

        // Clear search on Escape key
        document.getElementById('searchInput').addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                searchTable();
            }
        });

        // Debug info
        console.log('=== OB Bongkar Index Debug Info ===');
        console.log('Selected Kapal:', '{{ $selectedKapal }}');
        console.log('Selected Voyage:', '{{ $selectedVoyage }}');
        console.log('Total Containers:', {{ $bls->count() }});
        console.log('Sudah OB:', {{ $bls->where('sudah_ob', true)->count() }});
        console.log('Belum OB:', {{ $bls->where('sudah_ob', false)->count() }});
        console.log('User:', '{{ Auth::user()->name }}');
        console.log('User ID:', {{ Auth::user()->id }});
        console.log('Page loaded at:', new Date().toLocaleString());
        
        @if(session('success'))
        console.log('%c✓ SUCCESS', 'color: green; font-weight: bold; font-size: 14px;');
        console.log('Message:', '{{ session('success') }}');
        @endif
        
        @if(session('error'))
        console.error('%c✗ ERROR', 'color: red; font-weight: bold; font-size: 14px;');
        console.error('Message:', '{{ session('error') }}');
        console.error('⚠️ Jika error ini terus muncul, screenshot dan laporkan ke administrator');
        @endif
        
        @if($errors->any())
        console.error('%c✗ VALIDATION ERRORS', 'color: red; font-weight: bold; font-size: 14px;');
        @foreach($errors->all() as $error)
        console.error('- {{ $error }}');
        @endforeach
        @endif
        
        console.log('===================================');
    </script>
</body>
</html>
