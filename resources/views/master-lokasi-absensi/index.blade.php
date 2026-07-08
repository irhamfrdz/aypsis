@extends('layouts.app')

@section('title', 'Kelola Lokasi Absensi')
@section('page_title', 'Kelola Lokasi Absensi')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            height: 480px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            z-index: 1;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
    </style>
@endpush

@section('content')
<!-- Page Header Card -->
<div class="bg-white rounded-lg border border-gray-200 p-6 mb-6 shadow-sm">
    <h1 class="text-xl font-bold text-gray-900 leading-tight">Kelola Lokasi Absensi</h1>
    <p class="text-xs text-gray-500 mt-1">Atur koordinat GPS dan radius aman absensi karyawan</p>
</div>

<!-- Main Layout Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    
    <!-- Left Panel: Form & List (5 Cols) -->
    <div class="lg:col-span-5 flex flex-col gap-6">
        
        <!-- Form Card -->
        <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
            <h3 id="form-title" class="text-sm font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                <span class="text-blue-600"></span> Tambah Lokasi Absensi
            </h3>
            
            <form id="location-form" class="space-y-4">
                <input type="hidden" id="location-id" value="">
                
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nama Lokasi</label>
                    <input type="text" id="nama-lokasi" placeholder="Contoh: Kantor Pusat, Workshop" required
                        class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Latitude</label>
                        <input type="number" step="any" id="latitude" placeholder="-6.2000" required
                            class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Longitude</label>
                        <input type="number" step="any" id="longitude" placeholder="106.8166" required
                            class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Radius Jangkauan (Meter)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" id="radius" min="10" placeholder="100" value="100" required
                            class="flex-1 bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        <span class="text-xs text-gray-500 font-bold bg-gray-50 border border-gray-300 px-3 py-2.5 rounded-md">METER</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Keterangan / Catatan</label>
                    <textarea id="keterangan" rows="2" placeholder="Catatan atau alamat singkat..."
                        class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"></textarea>
                </div>

                <div class="flex items-center gap-2 py-1">
                    <input type="checkbox" id="is-active" checked value="1"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                    <label for="is-active" class="text-xs font-semibold text-gray-700 select-none cursor-pointer">Status Aktif (Digunakan untuk absensi)</label>
                </div>

                <div class="flex gap-2 pt-1">
                    <button type="button" id="cancel-edit-btn" class="hidden flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 rounded-md text-sm transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md text-sm transition shadow-sm">
                        Simpan Lokasi
                    </button>
                </div>
            </form>
        </div>

        <!-- List Card -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm flex flex-col min-h-[250px] max-h-[350px] overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Daftar Lokasi Terdaftar</h4>
                <span id="location-count" class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2.5 py-0.5 rounded-full">0 Lokasi</span>
            </div>
            
            <div id="locations-list" class="flex-1 overflow-y-auto custom-scrollbar divide-y divide-gray-150">
                <!-- Dinamis diisi js -->
                <div class="p-6 text-center text-gray-400 text-sm">
                    Memuat data lokasi...
                </div>
            </div>
        </div>

    </div>

    <!-- Right Panel: Map (7 Cols) -->
    <div class="lg:col-span-7 flex flex-col gap-4 bg-white rounded-lg border border-gray-200 p-5 shadow-sm h-fit">
        
        <!-- Search bar over map -->
        <div class="flex items-center gap-2 border border-gray-300 rounded-md p-1 bg-white max-w-md">
            <input type="text" id="map-search" placeholder="Cari nama jalan / tempat..." 
                class="flex-1 bg-transparent text-gray-800 px-3 py-1.5 text-sm focus:outline-none">
            <button id="search-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-1.5 rounded-md text-xs transition">
                Cari
            </button>
        </div>

        <!-- Map Container -->
        <div class="relative w-full">
            <div id="map"></div>
            <div class="absolute bottom-2 left-2 z-[500] bg-white/95 border border-gray-200 rounded px-2.5 py-1.5 text-[10px] text-gray-500 font-medium shadow-sm">
                * Klik di peta untuk memposisikan pin lokasi absensi
            </div>
        </div>
        
    </div>

</div>
@endsection

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        // API Base URL mapping to Node.js backend port
        const API_BASE_URL = "{{ request()->getScheme() }}://{{ request()->getHost() }}:5000";

        // Inisialisasi Map ke Indonesia (light mode / standard OpenStreetMap)
        const map = L.map('map', { zoomControl: false }).setView([-2.548926, 118.0148634], 5);
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Google Maps Tile Layer
        L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps',
            maxZoom: 20
        }).addTo(map);

        // State variables
        let currentMarker = null;
        let currentCircle = null;
        let mapMarkers = [];
        let isEditing = false;

        // Elements
        const form = document.getElementById('location-form');
        const formTitle = document.getElementById('form-title');
        const locationIdInput = document.getElementById('location-id');
        const namaInput = document.getElementById('nama-lokasi');
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');
        const radiusInput = document.getElementById('radius');
        const ketInput = document.getElementById('keterangan');
        const isActiveInput = document.getElementById('is-active');
        const cancelEditBtn = document.getElementById('cancel-edit-btn');
        const locationsList = document.getElementById('locations-list');
        const countBadge = document.getElementById('location-count');
        const searchInput = document.getElementById('map-search');
        const searchBtn = document.getElementById('search-btn');

        // Fetch lokasi dari Node.js backend
        async function loadLocations() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/lokasi-absensi`);
                const locations = await response.json();
                
                // Clear previous markers
                mapMarkers.forEach(m => map.removeLayer(m));
                mapMarkers = [];
                
                if (locations.length === 0) {
                    locationsList.innerHTML = `
                        <div class="p-6 text-center text-gray-400 text-sm">
                            Belum ada lokasi absensi yang disimpan. Silakan klik pada peta atau gunakan form di atas untuk menambahkan.
                        </div>
                    `;
                    countBadge.innerText = '0 Lokasi';
                    return;
                }

                countBadge.innerText = `${locations.length} Lokasi`;
                locationsList.innerHTML = locations.map(loc => {
                    const locJson = JSON.stringify(loc).replace(/"/g, '&quot;');
                    return `
                        <div class="p-4 hover:bg-slate-50 transition cursor-pointer flex justify-between items-start gap-3" onclick="focusLocation(${loc.latitude}, ${loc.longitude}, ${loc.radius})">
                            <div class="flex-1 min-w-0">
                                <h5 class="font-bold text-gray-800 text-xs truncate">${loc.nama_lokasi}</h5>
                                <p class="text-[10px] text-gray-500 truncate mt-0.5">${loc.keterangan || 'Tidak ada keterangan'}</p>
                                <div class="flex gap-2 items-center mt-2">
                                    <span class="text-[9px] bg-blue-50 text-blue-600 font-semibold px-2 py-0.5 rounded-full border border-blue-100">R: ${loc.radius}m</span>
                                    ${loc.is_active == 1 ? 
                                        `<span class="text-[9px] bg-emerald-50 text-emerald-600 font-semibold px-2 py-0.5 rounded-full border border-emerald-100">Aktif</span>` : 
                                        `<span class="text-[9px] bg-rose-50 text-rose-600 font-semibold px-2 py-0.5 rounded-full border border-rose-100">Nonaktif</span>`
                                    }
                                    <span class="text-[9px] text-gray-400 font-medium">${loc.latitude.toFixed(5)}, ${loc.longitude.toFixed(5)}</span>
                                </div>
                            </div>
                            <div class="flex gap-1 shrink-0" onclick="event.stopPropagation()">
                                <button onclick="editLocation(${locJson})" 
                                    class="bg-gray-100 hover:bg-blue-50 text-blue-600 p-1.5 rounded transition text-xs">
                                    ✏️
                                </button>
                                <button onclick="deleteLocation(${loc.id})" 
                                    class="bg-gray-100 hover:bg-red-50 text-red-600 p-1.5 rounded transition text-xs">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');

                // Draw markers and boundaries on map
                locations.forEach(loc => {
                    const marker = L.marker([loc.latitude, loc.longitude]).addTo(map);
                    marker.bindPopup(`
                        <div class="p-1">
                            <strong class="text-xs block font-bold text-gray-800">${loc.nama_lokasi}</strong>
                            <span class="text-[10px] text-gray-500 block">Radius: ${loc.radius}m</span>
                            <span class="text-[10px] font-semibold block ${loc.is_active == 1 ? 'text-emerald-600' : 'text-red-500'}">${loc.is_active == 1 ? 'Status: Aktif' : 'Status: Nonaktif'}</span>
                        </div>
                    `);
                    
                    const isActive = loc.is_active == 1;
                    const circleColor = isActive ? '#2563eb' : '#ef4444';
                    const circle = L.circle([loc.latitude, loc.longitude], {
                        color: circleColor,
                        fillColor: circleColor,
                        fillOpacity: isActive ? 0.1 : 0.05,
                        radius: loc.radius
                    }).addTo(map);

                    mapMarkers.push(marker);
                    mapMarkers.push(circle);
                });

            } catch (err) {
                console.error('Failed to load locations:', err);
                locationsList.innerHTML = `
                    <div class="p-6 text-center text-red-500 text-xs">
                        Gagal memuat data dari API.
                    </div>
                `;
            }
        }

        function focusLocation(lat, lon, radius) {
            map.setView([lat, lon], 17);
            if (!isEditing) {
                updateTempVisuals(lat, lon, radius);
            }
        }

        function updateTempVisuals(lat, lon, radius) {
            if (currentMarker) map.removeLayer(currentMarker);
            if (currentCircle) map.removeLayer(currentCircle);

            currentMarker = L.marker([lat, lon]).addTo(map);
            currentCircle = L.circle([lat, lon], {
                color: '#ea580c', // Orange 600
                fillColor: '#ea580c',
                fillOpacity: 0.15,
                radius: parseInt(radius) || 100
            }).addTo(map);
        }

        // Map Click Action
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lon = e.latlng.lng;
            const radius = radiusInput.value || 100;

            latInput.value = lat.toFixed(7);
            lonInput.value = lon.toFixed(7);

            updateTempVisuals(lat, lon, radius);
        });

        // Radius Input Dynamic Update
        radiusInput.addEventListener('input', function() {
            const lat = parseFloat(latInput.value);
            const lon = parseFloat(lonInput.value);
            const radius = this.value;

            if (!isNaN(lat) && !isNaN(lon)) {
                updateTempVisuals(lat, lon, radius);
            }
        });

        // Save (POST / PUT) Location
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const id = locationIdInput.value;
            const payload = {
                nama_lokasi: namaInput.value,
                latitude: parseFloat(latInput.value),
                longitude: parseFloat(lonInput.value),
                radius: parseInt(radiusInput.value) || 100,
                keterangan: ketInput.value,
                is_active: isActiveInput.checked ? 1 : 0
            };

            const url = id ? `${API_BASE_URL}/api/lokasi-absensi/${id}` : `${API_BASE_URL}/api/lokasi-absensi`;
            const method = id ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Terjadi kesalahan');

                alert(data.message || 'Lokasi berhasil disimpan!');
                resetForm();
                loadLocations();
            } catch (err) {
                alert('Gagal menyimpan: ' + err.message);
            }
        });

        function editLocation(loc) {
            isEditing = true;
            formTitle.innerHTML = `<span class="text-blue-600">✏️</span> Edit Lokasi: ${loc.nama_lokasi}`;
            locationIdInput.value = loc.id;
            namaInput.value = loc.nama_lokasi;
            latInput.value = loc.latitude;
            lonInput.value = loc.longitude;
            radiusInput.value = loc.radius;
            ketInput.value = loc.keterangan || '';
            isActiveInput.checked = loc.is_active == 1;
            
            cancelEditBtn.classList.remove('hidden');
            updateTempVisuals(loc.latitude, loc.longitude, loc.radius);
            map.setView([loc.latitude, loc.longitude], 17);
        }

        cancelEditBtn.addEventListener('click', resetForm);

        function resetForm() {
            isEditing = false;
            formTitle.innerHTML = `<span class="text-blue-600">📍</span> Tambah Lokasi Absensi`;
            locationIdInput.value = '';
            form.reset();
            isActiveInput.checked = true;
            
            cancelEditBtn.classList.add('hidden');
            if (currentMarker) map.removeLayer(currentMarker);
            if (currentCircle) map.removeLayer(currentCircle);
            currentMarker = null;
            currentCircle = null;
        }

        // Delete Location
        async function deleteLocation(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus lokasi absensi ini?')) return;

            try {
                const response = await fetch(`${API_BASE_URL}/api/lokasi-absensi/${id}`, { method: 'DELETE' });
                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Gagal menghapus');

                alert('Lokasi berhasil dihapus.');
                if (locationIdInput.value == id) {
                    resetForm();
                }
                loadLocations();
            } catch (err) {
                alert('Gagal menghapus: ' + err.message);
            }
        }

        // OSM Nominatim Search
        async function performSearch() {
            const query = searchInput.value.trim();
            if (!query) return;

            searchBtn.innerText = '...';
            searchBtn.disabled = true;

            try {
                let response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=id&q=${encodeURIComponent(query)}`);
                let results = await response.json();
                
                // Fallback: If not found, simplify the address automatically
                if (results.length === 0) {
                    const cleanedQuery = query
                        .replace(/rt\s*\.?\s*\d+\s*[\/\-]?\s*rw\s*\.?\s*\d+/gi, '') // Removes RT.7/RW.7, RT 01/RW 02
                        .replace(/blok\s+[a-z0-9]+/gi, '')                          // Removes Blok B
                        .replace(/no\s*\.?\s*\d+/gi, '')                             // Removes No.8, No 12
                        .replace(/\s+/g, ' ')
                        .trim();
                        
                    if (cleanedQuery && cleanedQuery !== query) {
                        response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=id&q=${encodeURIComponent(cleanedQuery)}`);
                        results = await response.json();
                    }
                }

                if (results.length === 0) {
                    alert('Lokasi tidak ditemukan. Coba masukkan nama jalan/daerah yang lebih sederhana.');
                    return;
                }

                const result = results[0];
                const lat = parseFloat(result.lat);
                const lon = parseFloat(result.lon);
                const radius = radiusInput.value || 100;

                latInput.value = lat.toFixed(7);
                lonInput.value = lon.toFixed(7);

                focusLocation(lat, lon, radius);
            } catch (err) {
                console.error('Search failed:', err);
                alert('Gagal melakukan pencarian.');
            } finally {
                searchBtn.innerText = 'Cari';
                searchBtn.disabled = false;
            }
        }

        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') performSearch();
        });

        // Load data on startup
        loadLocations();
    </script>
@endpush
