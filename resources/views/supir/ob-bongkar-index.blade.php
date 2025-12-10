<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
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
                                        data-barang="{{ strtolower($bl->nama_barang ?? '') }}">
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
                                                <form action="{{ route('supir.ob-bongkar.process') }}" method="POST" class="inline" 
                                                      onsubmit="return confirm('Yakin ingin memproses OB Bongkar untuk kontainer {{ $bl->nomor_kontainer }}?')">
                                                    @csrf
                                                    <input type="hidden" name="kapal" value="{{ $selectedKapal }}">
                                                    <input type="hidden" name="voyage" value="{{ $selectedVoyage }}">
                                                    <input type="hidden" name="bl_id" value="{{ $bl->id }}">
                                                    <button type="submit"
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                                        </svg>
                                                        OB Bongkar
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

                // Check if any field matches the search term
                const isVisible = kontainer.includes(searchTerm) || 
                                seal.includes(searchTerm) || 
                                barang.includes(searchTerm);

                if (isVisible) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (visibleCount === 0 && searchTerm !== '') {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }

            // Update visible count in console
            console.log('Search term:', searchTerm);
            console.log('Visible rows:', visibleCount, 'of', tableRows.length);
        }

        // Clear search on Escape key
        document.getElementById('searchInput').addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                searchTable();
            }
        });

        // Auto refresh setiap 30 detik untuk update data terbaru
        let autoRefreshTimer = setTimeout(function() {
            window.location.reload();
        }, 30000);

        // Pause auto-refresh when user is searching
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(autoRefreshTimer);
            // Restart timer setelah 30 detik dari input terakhir
            autoRefreshTimer = setTimeout(function() {
                window.location.reload();
            }, 30000);
        });

        // Debug info
        console.log('OB Bongkar Index loaded');
        console.log('Selected Kapal: {{ $selectedKapal }}');
        console.log('Selected Voyage: {{ $selectedVoyage }}');
        console.log('Total Containers: {{ $bls->count() }}');
        console.log('Sudah OB:', {{ $bls->where('sudah_ob', true)->count() }});
        console.log('Belum OB:', {{ $bls->where('sudah_ob', false)->count() }});
    </script>
</body>
</html>
