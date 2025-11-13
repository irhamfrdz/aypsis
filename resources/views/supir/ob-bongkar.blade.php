<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OB Bongkar - Pilih Kapal & Voyage - AYPSIS</title>
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
                    <a href="{{ route('supir.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-xl font-bold text-gray-800">OB Bongkar - Pilih Kapal & Voyage</h1>
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
        <div class="max-w-xl mx-auto">
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

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-4 text-sm" role="alert">
                    <div class="flex items-center mb-1">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">Terdapat kesalahan:</span>
                    </div>
                    <ul class="list-disc list-inside ml-6">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- Header dengan Icon -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-orange-100 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">OB Bongkar (Step 1)</h2>
                            <p class="text-sm text-gray-600">Pilih kapal dan nomor voyage</p>
                        </div>
                    </div>
                    <a href="{{ route('supir.dashboard') }}" class="px-3 py-1.5 text-sm bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Form Pilih Kapal dan Voyage -->
            <div class="bg-white rounded-lg shadow-sm p-4">
                <form action="{{ route('supir.ob-bongkar.store') }}" method="POST" id="obBongkarForm">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Dropdown Kapal -->
                        <div>
                            <label for="kapal" class="block text-sm font-medium text-gray-700 mb-1">
                                Kapal <span class="text-red-500">*</span>
                            </label>
                            <select id="kapal" name="kapal" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                    onchange="updateVoyageOptions()">
                                <option value="">--Pilih Kapal--</option>
                                @foreach($masterKapals as $kapal)
                                    <option value="{{ $kapal->nama_kapal }}">{{ $kapal->nama_kapal }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dropdown Voyage -->
                        <div>
                            <label for="voyage" class="block text-sm font-medium text-gray-700 mb-1">
                                No Voyage <span class="text-red-500">*</span>
                            </label>
                            <select id="voyage" name="voyage" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                    disabled>
                                <option value="">-PILIH KAPAL DAHULU-</option>
                            </select>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-md p-3">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-blue-700">
                                    <strong>Informasi:</strong> Pilihan voyage diambil dari data BL yang tersedia untuk proses bongkar kontainer.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-4">
                        <button type="button" onclick="proceedToObBongkar()" 
                                class="w-full px-4 py-2 bg-orange-600 text-white font-medium rounded-md hover:bg-orange-700 transition-colors flex items-center justify-center disabled:bg-gray-400 disabled:cursor-not-allowed text-sm"
                                id="proceedBtn" disabled>
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Ke Index OB Bongkar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Data voyage berdasarkan kapal dari BL
        const voyageData = {
            @php
                // Ambil data BL yang dikelompokkan berdasarkan kapal dan voyage
                $blsByKapal = \App\Models\Bl::select('nama_kapal', 'no_voyage', 'pelabuhan_muat', 'pelabuhan_bongkar')
                    ->whereNotNull('nama_kapal')
                    ->whereNotNull('no_voyage')
                    ->where('nama_kapal', '!=', '')
                    ->where('no_voyage', '!=', '')
                    ->orderBy('nama_kapal')
                    ->orderBy('no_voyage')
                    ->get()
                    ->groupBy('nama_kapal');
            @endphp
            @foreach($blsByKapal as $namaKapal => $bls)
            '{{ $namaKapal }}': [
                @foreach($bls->unique('no_voyage') as $bl)
                {
                    voyage: '{{ $bl->no_voyage }}',
                    pelabuhan_muat: '{{ $bl->pelabuhan_muat ?? "-" }}',
                    pelabuhan_bongkar: '{{ $bl->pelabuhan_bongkar ?? "-" }}'
                },
                @endforeach
            ],
            @endforeach
        };

        // Debug: Log voyage data to console
        console.log('Voyage Data (from BL):', voyageData);
        console.log('Total Kapal in voyageData:', Object.keys(voyageData).length);

        function updateVoyageOptions() {
            const kapalSelect = document.getElementById('kapal');
            const voyageSelect = document.getElementById('voyage');
            const proceedBtn = document.getElementById('proceedBtn');
            
            const selectedKapal = kapalSelect.value;
            console.log('Selected Kapal:', selectedKapal);
            
            // Reset voyage dropdown
            voyageSelect.innerHTML = '<option value="">-PILIH VOYAGE-</option>';
            voyageSelect.disabled = !selectedKapal;
            
            // Disable button
            proceedBtn.disabled = true;
            
            if (selectedKapal && voyageData[selectedKapal]) {
                console.log('Voyages for', selectedKapal, ':', voyageData[selectedKapal]);
                
                // Populate voyage options - hapus duplikat dan format dengan baik
                const uniqueVoyages = {};
                voyageData[selectedKapal].forEach(item => {
                    if (!uniqueVoyages[item.voyage]) {
                        uniqueVoyages[item.voyage] = item;
                    }
                });
                
                Object.values(uniqueVoyages).forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.voyage;
                    
                    // Format text dengan info yang ada
                    let displayText = item.voyage;
                    const additionalInfo = [];
                    
                    if (item.pelabuhan_muat && item.pelabuhan_muat !== '-') {
                        additionalInfo.push('Dari: ' + item.pelabuhan_muat);
                    }
                    if (item.pelabuhan_bongkar && item.pelabuhan_bongkar !== '-') {
                        additionalInfo.push('Ke: ' + item.pelabuhan_bongkar);
                    }
                    
                    if (additionalInfo.length > 0) {
                        displayText += ` (${additionalInfo.join(' - ')})`;
                    }
                    
                    option.textContent = displayText;
                    voyageSelect.appendChild(option);
                });
                
                // Enable voyage dropdown
                voyageSelect.disabled = false;
                console.log('Voyage dropdown enabled with', Object.keys(uniqueVoyages).length, 'unique options');
            } else if (selectedKapal) {
                console.log('No voyages found for kapal:', selectedKapal);
                console.log('Available kapals in voyageData:', Object.keys(voyageData));
                
                // Add message for no voyages
                const option = document.createElement('option');
                option.value = '';
                option.textContent = '-TIDAK ADA VOYAGE-';
                option.disabled = true;
                voyageSelect.appendChild(option);
            }
        }

        function updateKapalDetails() {
            const kapalSelect = document.getElementById('kapal');
            const voyageSelect = document.getElementById('voyage');
            const proceedBtn = document.getElementById('proceedBtn');
            
            const selectedKapal = kapalSelect.value;
            const selectedVoyage = voyageSelect.value;
            
            console.log('Updating for:', selectedKapal, selectedVoyage);
            
            if (selectedKapal && selectedVoyage && voyageData[selectedKapal]) {
                const voyageInfo = voyageData[selectedKapal].find(item => item.voyage === selectedVoyage);
                
                if (voyageInfo) {
                    console.log('Found voyage info:', voyageInfo);
                    
                    // Enable button
                    proceedBtn.disabled = false;
                } else {
                    console.log('Voyage info not found');
                    proceedBtn.disabled = true;
                }
            } else {
                proceedBtn.disabled = true;
            }
        }

        function proceedToObBongkar() {
            const kapal = document.getElementById('kapal').value;
            const voyage = document.getElementById('voyage').value;
            
            if (kapal && voyage) {
                // Redirect to OB Bongkar index page with selected kapal and voyage
                window.location.href = `/supir/ob-bongkar/index?kapal=${encodeURIComponent(kapal)}&voyage=${encodeURIComponent(voyage)}`;
            }
        }

        // Event listener untuk voyage selection
        document.getElementById('voyage').addEventListener('change', updateKapalDetails);

        // Debug: Log initial state
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded. Available kapal in voyageData:', Object.keys(voyageData));
        });
    </script>
</body>
</html>
