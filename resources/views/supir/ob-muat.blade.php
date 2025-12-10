<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OB Muat - Pilih Kapal & Voyage - AYPSIS</title>
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
                    <h1 class="text-xl font-bold text-gray-800">OB Muat - Pilih Kapal & Voyage</h1>
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
                        <div class="bg-green-100 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414A1 1 0 0016 10v6a1 1 0 01-1 1z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">OB Muat (Step 1)</h2>
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
                <form action="{{ url('supir/ob-muat/store') }}" method="POST" id="obMuatForm">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Dropdown Kapal -->
                        <div>
                            <label for="kapal" class="block text-sm font-medium text-gray-700 mb-1">
                                Kapal <span class="text-red-500">*</span>
                            </label>
                            <select id="kapal" name="kapal" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                    onchange="updateVoyageOptions(); updateKapalDetails();">
                                <option value="">--Pilih Kapal--</option>
                                {{-- Use naik_kapals (naik kapal rows) to populate available ships so voyages are picked from the same data source --}}
                                @php $shipGroups = $naikKapals->groupBy('nama_kapal')->keys(); @endphp
                                @foreach($shipGroups as $kapalName)
                                    <option value="{{ $kapalName }}" {{ $kapalName == ($selectedKapal ?? '') ? 'selected' : '' }}>{{ $kapalName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dropdown Voyage -->
                        <div>
                            <label for="voyage" class="block text-sm font-medium text-gray-700 mb-1">
                                No Voyage <span class="text-red-500">*</span>
                            </label>
                            <select id="voyage" name="voyage" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                    disabled>
                                <option value="">-PILIH KAPAL DAHULU-</option>
                            </select>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-4">
                        <button type="button" onclick="proceedToObMuat()" 
                                class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center disabled:bg-gray-400 disabled:cursor-not-allowed text-sm"
                                id="proceedBtn" disabled>
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Ke Index OB
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Data voyage berdasarkan kapal dari naik_kapal (serialized safely via @json)
        const voyageData = @json($naikKapals->groupBy('nama_kapal')->map(function($voyages) {
            return $voyages->map(function($v) {
                return [
                    'voyage' => $v->no_voyage,
                    'tanggal_muat' => $v->tanggal_muat ? $v->tanggal_muat->format('d/m/Y') : '-',
                    'pelabuhan_tujuan' => $v->pelabuhan_tujuan ?? '-',
                    'jenis_barang' => $v->jenis_barang ?? '-',
                ];
            })->values();
        })->toArray());

        // Selected values passed from server (outside object literal)
        const initialSelectedKapal = @json($selectedKapal ?? '');
        const initialSelectedVoyage = @json($selectedVoyage ?? '');

        // Debug: Log voyage data to console
        const voyageDataKeys = (voyageData && typeof voyageData === 'object') ? Object.keys(voyageData) : [];
        console.log('Voyage Data (from Naik Kapal):', voyageData);
        console.log('Total Kapal in voyageData:', voyageDataKeys.length);

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
            
            if (selectedKapal && voyageData && voyageData[selectedKapal]) {
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
                    
                    if (item.tanggal_muat && item.tanggal_muat !== '-') {
                        additionalInfo.push(item.tanggal_muat);
                    }
                    if (item.pelabuhan_tujuan && item.pelabuhan_tujuan !== '-') {
                        additionalInfo.push(item.pelabuhan_tujuan);
                    }
                    
                    if (additionalInfo.length > 0) {
                        displayText += ` (${additionalInfo.join(' - ')})`;
                    }
                    
                    option.textContent = displayText;
                    // mark selected if the server passed an initial selected voyage
                    if (initialSelectedVoyage && item.voyage === initialSelectedVoyage) {
                        option.selected = true;
                    }
                    voyageSelect.appendChild(option);
                });
                
                // Enable voyage dropdown
                voyageSelect.disabled = false;
                // If a server-selected voyage exists, ensure the button state is updated
                if (initialSelectedVoyage) {
                    updateKapalDetails();
                }
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

        function proceedToObMuat() {
            const kapal = document.getElementById('kapal').value;
            const voyage = document.getElementById('voyage').value;
            
            if (kapal && voyage) {
                // Redirect to OB Muat index page with selected kapal and voyage
                window.location.href = `/supir/ob-muat/index?kapal=${encodeURIComponent(kapal)}&voyage=${encodeURIComponent(voyage)}`;
            }
        }

        // Event listener untuk voyage selection
        const voyageSelectEl = document.getElementById('voyage');
        if (voyageSelectEl) {
            voyageSelectEl.addEventListener('change', updateKapalDetails);
        }

        // Debug: Log initial state
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded. Available kapal in voyageData:', Object.keys(voyageData));

            @if($naikKapals->count() == 0)
            console.warn('No naik kapal data found. Using sample data.');
            @endif

            // Preselect kapal and voyage if provided by the server
            try {
                if (initialSelectedKapal) {
                    const kapalSelect = document.getElementById('kapal');
                    kapalSelect.value = initialSelectedKapal;
                    updateVoyageOptions();
                }

                if (initialSelectedVoyage) {
                    const voyageSelect = document.getElementById('voyage');
                    // If options already exist and one is selected by updateVoyageOptions, updateKapalDetails will be called.
                    // Otherwise, explicitly set and call updateKapalDetails
                    if (voyageSelect.querySelector(`option[value="${initialSelectedVoyage}"]`)) {
                        voyageSelect.value = initialSelectedVoyage;
                        updateKapalDetails();
                    }
                }
            } catch (e) {
                console.warn('Failed applying initial selections:', e);
            }
        });
    </script>
</body>
</html>