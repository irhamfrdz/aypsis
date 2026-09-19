@extends('layouts.app')

@section('title', 'Kelola Lokasi Absensi')
@section('page_title', 'Kelola Lokasi Absensi')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            height: 530px;
            width: 100%;
            border-radius: 14px;
            z-index: 1;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        /* Leaflet popup styling override */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.05);
            border: 1px solid #e2e8f0;
            padding: 2px;
        }
        .leaflet-popup-content {
            margin: 10px 14px;
            font-family: inherit;
        }
        .location-item.active-item {
            background-color: #eff6ff !important;
            border-left: 3px solid #3b82f6 !important;
        }
    </style>
@endpush

@section('content')
<div class="space-y-6 pb-8">

    <!-- Header Banner Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                        <i class="fas fa-map-marked-alt text-[11px] text-blue-600"></i>
                        Master Data Geofence
                    </span>
                    <span class="text-xs text-slate-400 font-medium">• Presensi GPS Karyawan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 flex items-center gap-2.5">
                    Kelola Lokasi Absensi
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 max-w-2xl leading-relaxed">
                    Atur titik koordinat GPS dan radius jangkauan aman absensi masuk serta pulang karyawan secara presisi.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 flex items-center gap-3 shadow-2xs">
                    <div class="w-10 h-10 rounded-xl bg-blue-100/80 text-blue-600 flex items-center justify-center font-bold text-base shadow-2xs">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Terdaftar</span>
                        <span id="header-total-count" class="text-lg font-black text-slate-900 leading-none">0 Lokasi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Panel: Form & List (5 Cols) -->
        <div class="lg:col-span-5 flex flex-col gap-6">
            
            <!-- Form Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs flex flex-col gap-4">
                
                <!-- Form Header -->
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div id="form-icon-wrap" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold shadow-2xs transition-colors">
                            <i id="form-icon" class="fas fa-plus"></i>
                        </div>
                        <div>
                            <h3 id="form-title" class="text-sm font-bold text-slate-900 leading-tight">Tambah Lokasi Absensi</h3>
                            <p id="form-subtitle" class="text-[11px] text-slate-400 mt-0.5">Tentukan nama dan koordinat titik lokasi baru</p>
                        </div>
                    </div>
                    <span id="mode-badge" class="hidden text-[10px] font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1 shadow-2xs">
                        <i class="fas fa-pen text-[9px]"></i> Mode Edit
                    </span>
                </div>
                
                <form id="location-form" class="space-y-4">
                    <input type="hidden" id="location-id" value="">
                    
                    <!-- Nama Lokasi -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-building text-slate-400 text-[10px]"></i>
                            <span>Nama Lokasi</span>
                            <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama-lokasi" placeholder="Contoh: Kantor Utama, Gudang Logistik" required
                            class="w-full bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs placeholder-slate-400">
                    </div>
                    
                    <!-- Latitude & Longitude -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fas fa-map-pin text-slate-400 text-[10px]"></i>
                                <span>Latitude</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="any" id="latitude" placeholder="-6.2000000" required
                                class="w-full font-mono bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs placeholder-slate-400">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fas fa-map-pin text-slate-400 text-[10px]"></i>
                                <span>Longitude</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="any" id="longitude" placeholder="106.8166000" required
                                class="w-full font-mono bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs placeholder-slate-400">
                        </div>
                    </div>
                    
                    <!-- Radius Jangkauan -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-bullseye text-slate-400 text-[10px]"></i>
                            <span>Radius Jangkauan</span>
                            <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <input type="number" id="radius" min="10" placeholder="100" value="100" required
                                class="w-full bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl py-2.5 pl-3.5 pr-20 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs placeholder-slate-400">
                            <span class="absolute right-2.5 px-2.5 py-1 text-[10px] font-bold bg-slate-200/80 text-slate-600 rounded-lg select-none">
                                METER
                            </span>
                        </div>
                    </div>
                    
                    <!-- Keterangan / Catatan -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-align-left text-slate-400 text-[10px]"></i>
                            <span>Keterangan / Catatan</span>
                        </label>
                        <textarea id="keterangan" rows="2" placeholder="Catatan opsional atau alamat singkat lokasi..."
                            class="w-full bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition shadow-2xs placeholder-slate-400"></textarea>
                    </div>

                    <!-- Status Aktif Toggle Card -->
                    <label for="is-active" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer select-none">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-800">Status Aktif</span>
                                <span class="block text-[10px] text-slate-400">Gunakan lokasi ini untuk absensi karyawan</span>
                            </div>
                        </div>
                        <input type="checkbox" id="is-active" checked value="1"
                            class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer">
                    </label>

                    <!-- Submit & Cancel Buttons -->
                    <div class="flex gap-2.5 pt-1.5">
                        <button type="button" id="cancel-edit-btn" class="hidden flex-1 bg-slate-100 hover:bg-slate-200 active:scale-[0.98] text-slate-700 font-semibold py-2.5 rounded-xl text-xs transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-times text-[10px]"></i>
                            <span>Batal</span>
                        </button>
                        <button type="submit" id="submit-btn" class="flex-[2] bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-bold py-2.5 rounded-xl text-xs transition shadow-xs flex items-center justify-center gap-2">
                            <i class="fas fa-save text-[11px]"></i>
                            <span id="submit-btn-text">Simpan Lokasi</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- List Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs flex flex-col overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex justify-between items-center bg-slate-50/40">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-list-ul text-slate-400 text-xs"></i>
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Daftar Lokasi</h4>
                    </div>
                    <span id="location-count" class="text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-0.5 rounded-full shadow-2xs">0 Lokasi</span>
                </div>

                <!-- Live Search Box in List -->
                <div class="px-4 py-2.5 bg-white border-b border-slate-100">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                        <input type="text" id="filter-locations-input" oninput="filterLocationsList(this.value)" placeholder="Cari nama lokasi terdaftar..."
                            class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-800 transition">
                    </div>
                </div>
                
                <div id="locations-list" class="max-h-[360px] overflow-y-auto custom-scrollbar divide-y divide-slate-100">
                    <!-- Dinamis diisi js -->
                    <div class="p-8 text-center text-slate-400 text-xs flex flex-col items-center justify-center gap-2">
                        <i class="fas fa-circle-notch fa-spin text-lg text-blue-500"></i>
                        <span>Memuat data lokasi...</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Panel: Map (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs flex flex-col gap-4 h-fit">
            
            <!-- Map Search Bar & Controls -->
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="relative flex-1 max-w-md">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    <input type="text" id="map-search" placeholder="Cari nama jalan, gedung, atau daerah..." 
                        class="w-full pl-8 pr-20 py-2 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs placeholder-slate-400">
                    <button id="search-btn" class="absolute right-1 top-1 bottom-1 px-3 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-semibold rounded-lg text-xs transition flex items-center gap-1.5 shadow-xs">
                        <span>Cari</span>
                    </button>
                </div>

                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs">
                    <i class="fas fa-crosshairs text-blue-500"></i>
                    <span>Peta Interaktif GPS</span>
                </div>
            </div>

            <!-- Map Container -->
            <div class="relative w-full rounded-2xl overflow-hidden border border-slate-200 shadow-inner">
                <div id="map"></div>
                
                <!-- Floating Info Badge on Map -->
                <div class="absolute bottom-3 left-3 z-[500] bg-white/95 backdrop-blur-md border border-slate-200/80 rounded-xl px-3.5 py-2 text-[11px] text-slate-700 font-medium shadow-md flex items-center gap-2 select-none">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-ping"></span>
                    <i class="fas fa-arrows-alt text-orange-500 text-xs"></i>
                    <span>Geser pin (drag) atau klik peta untuk memindahkan titik lokasi</span>
                </div>
            </div>
            
        </div>

    </div>
</div>
@endsection

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        // API Base URL mapping to backend
        const API_BASE_URL = "{{ url('') }}";

        // Inisialisasi Map ke Indonesia
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
        let allLocationsData = [];
        let activeLocationId = null;

        // Elements
        const form = document.getElementById('location-form');
        const formTitle = document.getElementById('form-title');
        const formSubtitle = document.getElementById('form-subtitle');
        const formIcon = document.getElementById('form-icon');
        const formIconWrap = document.getElementById('form-icon-wrap');
        const modeBadge = document.getElementById('mode-badge');
        const submitBtnText = document.getElementById('submit-btn-text');
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
        const headerTotalCount = document.getElementById('header-total-count');
        const searchInput = document.getElementById('map-search');
        const searchBtn = document.getElementById('search-btn');

        // Fetch lokasi dari Node.js backend
        async function loadLocations() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/lokasi-absensi`);
                const locations = await response.json();
                allLocationsData = locations;
                
                // Clear previous markers
                mapMarkers.forEach(m => map.removeLayer(m));
                mapMarkers = [];
                
                renderLocationsList(locations);

                // Draw markers and boundaries on map
                locations.forEach(loc => {
                    const marker = L.marker([loc.latitude, loc.longitude]).addTo(map);
                    marker.locationId = loc.id;
                    const isActive = loc.is_active == 1;

                    marker.bindPopup(`
                        <div class="p-1 space-y-1">
                            <div class="font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full ${isActive ? 'bg-emerald-500' : 'bg-rose-500'}"></span>
                                <span>${loc.nama_lokasi}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 leading-tight">${loc.keterangan || 'Tidak ada catatan'}</p>
                            <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500 gap-2">
                                <span class="font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">R: ${loc.radius}m</span>
                                <span class="font-mono text-slate-400">${loc.latitude.toFixed(4)}, ${loc.longitude.toFixed(4)}</span>
                            </div>
                        </div>
                    `);
                    
                    const circleColor = isActive ? '#2563eb' : '#ef4444';
                    const circle = L.circle([loc.latitude, loc.longitude], {
                        color: circleColor,
                        fillColor: circleColor,
                        fillOpacity: isActive ? 0.08 : 0.04,
                        radius: loc.radius,
                        interactive: false // Clicks pass through to map
                    }).addTo(map);
                    circle.locationId = loc.id;

                    mapMarkers.push(marker);
                    mapMarkers.push(circle);
                });

            } catch (err) {
                console.error('Failed to load locations:', err);
                locationsList.innerHTML = `
                    <div class="p-8 text-center text-rose-500 text-xs flex flex-col items-center gap-1.5">
                        <i class="fas fa-exclamation-triangle text-base"></i>
                        <span>Gagal memuat data dari API.</span>
                    </div>
                `;
            }
        }

        // Render card list
        function renderLocationsList(locations) {
            if (locations.length === 0) {
                locationsList.innerHTML = `
                    <div class="p-8 text-center text-slate-400 text-xs flex flex-col items-center justify-center gap-2">
                        <i class="fas fa-map-marker-alt text-2xl text-slate-300"></i>
                        <p class="font-medium text-slate-600">Belum ada lokasi absensi</p>
                        <p class="text-[11px] text-slate-400">Klik pada peta atau isi formulir di atas untuk mendaftarkan titik lokasi baru.</p>
                    </div>
                `;
                countBadge.innerText = '0 Lokasi';
                if (headerTotalCount) headerTotalCount.innerText = '0 Lokasi';
                return;
            }

            countBadge.innerText = `${locations.length} Lokasi`;
            if (headerTotalCount) headerTotalCount.innerText = `${locations.length} Lokasi`;

            locationsList.innerHTML = locations.map(loc => {
                const locJson = JSON.stringify(loc).replace(/"/g, '&quot;');
                const isActive = loc.is_active == 1;
                const isSelected = (activeLocationId === loc.id);

                return `
                    <div id="loc-card-${loc.id}" 
                         class="location-item p-3.5 hover:bg-slate-50/80 transition cursor-pointer flex justify-between items-start gap-3 ${isSelected ? 'active-item' : ''}" 
                         onclick="focusLocation(${loc.latitude}, ${loc.longitude}, ${loc.radius}, ${loc.id})">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <div class="w-8 h-8 rounded-xl ${isActive ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-slate-100 text-slate-400'} flex items-center justify-center text-xs flex-shrink-0 mt-0.5 shadow-2xs">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h5 class="font-bold text-slate-800 text-xs truncate leading-snug">${loc.nama_lokasi}</h5>
                                <p class="text-[11px] text-slate-400 truncate mt-0.5">${loc.keterangan || 'Tidak ada catatan'}</p>
                                <div class="flex flex-wrap gap-1.5 items-center mt-2">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md border border-slate-200">
                                        <i class="fas fa-bullseye text-[8px] text-slate-400"></i> ${loc.radius} m
                                    </span>
                                    ${isActive ? 
                                        `<span class="inline-flex items-center gap-1 text-[10px] bg-emerald-50 text-emerald-700 font-semibold px-2 py-0.5 rounded-md border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>` : 
                                        `<span class="inline-flex items-center gap-1 text-[10px] bg-slate-100 text-slate-500 font-semibold px-2 py-0.5 rounded-md border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                        </span>`
                                    }
                                    <span class="font-mono text-[10px] text-slate-400 tracking-tight">${loc.latitude.toFixed(5)}, ${loc.longitude.toFixed(5)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 pt-0.5" onclick="event.stopPropagation()">
                            <button type="button" onclick="editLocation(${locJson})" title="Edit Lokasi"
                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 transition border border-slate-200 hover:border-blue-200 shadow-2xs">
                                <i class="fas fa-pen text-[10px]"></i>
                            </button>
                            <button type="button" onclick="deleteLocation(${loc.id})" title="Hapus Lokasi"
                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 transition border border-slate-200 hover:border-rose-200 shadow-2xs">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Filter list input handler
        function filterLocationsList(query) {
            query = (query || '').toLowerCase().trim();
            if (!query) {
                renderLocationsList(allLocationsData);
                return;
            }
            const filtered = allLocationsData.filter(loc => {
                const name = (loc.nama_lokasi || '').toLowerCase();
                const ket = (loc.keterangan || '').toLowerCase();
                return name.includes(query) || ket.includes(query);
            });
            renderLocationsList(filtered);
        }

        function focusLocation(lat, lon, radius, id) {
            activeLocationId = id;
            document.querySelectorAll('.location-item').forEach(el => el.classList.remove('active-item'));
            const card = document.getElementById('loc-card-' + id);
            if (card) card.classList.add('active-item');

            map.setView([lat, lon], 17);
            if (!isEditing) {
                updateTempVisuals(lat, lon, radius);
            }
        }

        function updateTempVisuals(lat, lon, radius) {
            if (currentMarker) map.removeLayer(currentMarker);
            if (currentCircle) map.removeLayer(currentCircle);

            const r = parseInt(radius) || 100;

            // Marker draggable
            currentMarker = L.marker([lat, lon], {
                draggable: true,
                autoPan: true
            }).addTo(map);

            currentCircle = L.circle([lat, lon], {
                color: '#ea580c', // Orange 600
                fillColor: '#ea580c',
                fillOpacity: 0.15,
                radius: r,
                interactive: false // Allows click to pass to map
            }).addTo(map);

            currentMarker.bindTooltip('Geser pin untuk memindahkan titik', {
                direction: 'top',
                offset: [0, -32]
            });

            // Dragging events
            currentMarker.on('drag', function(e) {
                const pos = e.target.getLatLng();
                if (currentCircle) {
                    currentCircle.setLatLng(pos);
                }
                latInput.value = pos.lat.toFixed(7);
                lonInput.value = pos.lng.toFixed(7);
            });

            currentMarker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                latInput.value = pos.lat.toFixed(7);
                lonInput.value = pos.lng.toFixed(7);
                if (currentCircle) {
                    currentCircle.setLatLng(pos);
                }
            });
        }

        // Map Click Action (memindahkan pin & lingkaran)
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

        // Manual Lat/Lon Input Dynamic Update
        function handleManualCoordChange() {
            const lat = parseFloat(latInput.value);
            const lon = parseFloat(lonInput.value);
            const radius = radiusInput.value || 100;

            if (!isNaN(lat) && !isNaN(lon)) {
                updateTempVisuals(lat, lon, radius);
                map.panTo([lat, lon]);
            }
        }
        latInput.addEventListener('change', handleManualCoordChange);
        lonInput.addEventListener('change', handleManualCoordChange);

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
            activeLocationId = loc.id;
            
            // Switch form to edit styling
            formTitle.textContent = 'Edit Lokasi: ' + loc.nama_lokasi;
            formSubtitle.textContent = 'Sesuaikan posisi pin di peta atau form koordinat';
            formIcon.className = 'fas fa-pen';
            formIconWrap.className = 'w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold shadow-2xs transition-colors';
            modeBadge.classList.remove('hidden');
            submitBtnText.textContent = 'Perbarui Lokasi';

            locationIdInput.value = loc.id;
            namaInput.value = loc.nama_lokasi;
            latInput.value = loc.latitude;
            lonInput.value = loc.longitude;
            radiusInput.value = loc.radius;
            ketInput.value = loc.keterangan || '';
            isActiveInput.checked = loc.is_active == 1;

            // Sembunyikan marker lama milik lokasi ini saat sedang diedit agar tidak bertumpuk
            mapMarkers.forEach(layer => {
                if (layer.locationId === loc.id) {
                    map.removeLayer(layer);
                } else if (!map.hasLayer(layer)) {
                    layer.addTo(map);
                }
            });
            
            cancelEditBtn.classList.remove('hidden');
            updateTempVisuals(loc.latitude, loc.longitude, loc.radius);
            map.setView([loc.latitude, loc.longitude], 17);

            // Buka tooltip sejenak sebagai panduan visual
            if (currentMarker) {
                currentMarker.openTooltip();
                setTimeout(() => {
                    if (currentMarker) currentMarker.closeTooltip();
                }, 2500);
            }

            // Highlight card in list
            document.querySelectorAll('.location-item').forEach(el => el.classList.remove('active-item'));
            const card = document.getElementById('loc-card-' + loc.id);
            if (card) {
                card.classList.add('active-item');
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        cancelEditBtn.addEventListener('click', resetForm);

        function resetForm() {
            isEditing = false;
            activeLocationId = null;
            
            // Switch form back to add styling
            formTitle.textContent = 'Tambah Lokasi Absensi';
            formSubtitle.textContent = 'Tentukan nama dan koordinat titik lokasi baru';
            formIcon.className = 'fas fa-plus';
            formIconWrap.className = 'w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold shadow-2xs transition-colors';
            modeBadge.classList.add('hidden');
            submitBtnText.textContent = 'Simpan Lokasi';

            locationIdInput.value = '';
            form.reset();
            isActiveInput.checked = true;

            // Kembalikan marker yang sempat disembunyikan saat mode edit
            mapMarkers.forEach(layer => {
                if (!map.hasLayer(layer)) {
                    layer.addTo(map);
                }
            });

            document.querySelectorAll('.location-item').forEach(el => el.classList.remove('active-item'));
            
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

            searchBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin text-xs"></i>';
            searchBtn.disabled = true;

            try {
                let response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=id&q=${encodeURIComponent(query)}`);
                let results = await response.json();
                
                // Fallback: If not found, simplify the address automatically
                if (results.length === 0) {
                    const cleanedQuery = query
                        .replace(/rt\s*\.?\s*\d+\s*[\/\-]?\s*rw\s*\.?\s*\d+/gi, '')
                        .replace(/blok\s+[a-z0-9]+/gi, '')
                        .replace(/no\s*\.?\s*\d+/gi, '')
                        .replace(/\s+/g, ' ')
                        .trim();
                        
                    if (cleanedQuery && cleanedQuery !== query) {
                        response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=id&q=${encodeURIComponent(cleanedQuery)}`);
                        results = await response.json();
                    }
                }

                if (results.length === 0) {
                    alert('Lokasi tidak ditemukan. Coba masukkan nama jalan atau daerah yang lebih umum.');
                    return;
                }

                const result = results[0];
                const lat = parseFloat(result.lat);
                const lon = parseFloat(result.lon);
                const radius = radiusInput.value || 100;

                latInput.value = lat.toFixed(7);
                lonInput.value = lon.toFixed(7);

                map.setView([lat, lon], 17);
                updateTempVisuals(lat, lon, radius);
            } catch (err) {
                console.error('Search failed:', err);
                alert('Gagal melakukan pencarian.');
            } finally {
                searchBtn.innerHTML = '<span>Cari</span>';
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
