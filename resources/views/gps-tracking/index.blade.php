@extends('layouts.app')

@section('title', 'Live Tracking Armada')

@section('content')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }
    .leaflet-popup-content {
        margin: 10px;
    }
    #map {
        min-height: 600px; /* Ensure a minimum height */
    }
</style>
<div class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight flex items-center">
                <i class="fas fa-map-marked-alt text-indigo-600 mr-3"></i> 
                Live Tracking Armada (GPS.id)
            </h2>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0 items-center space-x-4">
            <span id="last-update-time" class="text-sm text-gray-500 font-medium">Menunggu pembaruan...</span>
            <button onclick="fetchLatestLocations()" type="button" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all">
                <i class="fas fa-sync-alt mr-2 text-gray-500"></i> Refresh Sekarang
            </button>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar Daftar Truk -->
        <div class="w-full lg:w-1/3 xl:w-1/4">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden flex flex-col h-[600px]">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Daftar Armada Aktif</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Truk dengan IMEI terdaftar</p>
                    </div>
                    <span id="truck-counter" class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">{{ $mobils->count() }} Truk</span>
                </div>
                <!-- Filter Buttons -->
                <div class="px-4 py-3 border-b border-gray-100 bg-white flex space-x-2">
                    <button type="button" onclick="setFilter('all')" id="filter-all" class="filter-btn flex-1 px-3 py-1.5 text-xs font-semibold rounded-md bg-indigo-600 text-white shadow-sm ring-1 ring-inset ring-indigo-600 transition-all">Semua</button>
                    <button type="button" onclick="setFilter('berjalan')" id="filter-berjalan" class="filter-btn flex-1 px-3 py-1.5 text-xs font-semibold rounded-md bg-white text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all">Berjalan</button>
                    <button type="button" onclick="setFilter('berhenti')" id="filter-berhenti" class="filter-btn flex-1 px-3 py-1.5 text-xs font-semibold rounded-md bg-white text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all">Berhenti</button>
                </div>
                <!-- Search Box -->
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="search-truck" onkeyup="applyFilters()" class="block w-full rounded-md border-0 py-1.5 pl-9 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all" placeholder="Cari nopol atau supir...">
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto">
                    <ul role="list" class="divide-y divide-gray-100" id="truck-list">
                        @forelse($mobils as $mobil)
                        <li class="relative flex justify-between gap-x-6 px-4 py-4 hover:bg-gray-50 transition-colors cursor-pointer truck-item group" data-id="{{ $mobil->id }}" onclick="focusOnTruck({{ $mobil->id }})">
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm font-semibold leading-6 text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $mobil->nomor_polisi }}</p>
                                <p class="mt-1 truncate text-xs leading-5 text-gray-500 font-medium">
                                    <i class="fas fa-user-tie text-gray-400 mr-1"></i> {{ $mobil->karyawan ? ($mobil->karyawan->nama_panggilan ?? $mobil->karyawan->nama_lengkap) : 'Tidak Ada Supir' }}
                                </p>
                                <p class="truncate text-[10px] leading-4 text-gray-400 mt-0.5">{{ $mobil->merek }} - {{ $mobil->jenis }}</p>
                                <!-- Tempat Info Surat Jalan / Aktifitas -->
                                <div id="sj-info-sidebar-{{ $mobil->id }}" class="mt-2 hidden flex-col gap-0.5">
                                </div>
                            </div>
                            <div class="shrink-0 flex flex-col items-end">
                                <p class="text-sm leading-6 text-gray-900 font-medium truck-speed" id="speed-{{ $mobil->id }}">- km/h</p>
                                <p class="mt-1 text-xs leading-5 text-gray-500 truck-status flex items-center" id="status-{{ $mobil->id }}">
                                    <i class="fas fa-circle text-[8px] mr-1 text-gray-400"></i> Mencari sinyal...
                                </p>
                                <div class="mt-2 flex gap-1 justify-end">
                                    <button type="button" onclick="event.stopPropagation(); loadHistory({{ $mobil->id }})" class="text-[10px] bg-indigo-50 border border-indigo-200 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-100 transition-colors">
                                        <i class="fas fa-history mr-1"></i> History
                                    </button>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="px-4 py-12 text-center">
                            <i class="fas fa-truck-slash text-4xl text-gray-300 mb-3"></i>
                            <p class="text-sm text-gray-500">Belum ada armada yang didaftarkan IMEI GPS.</p>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Area Peta -->
        <div class="w-full lg:w-2/3 xl:w-3/4">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden h-[600px] relative z-0">
                <div id="map" class="w-full h-full"></div>
                
                <!-- History Info Panel -->
                <div id="history-panel" class="hidden absolute top-4 left-1/2 transform -translate-x-1/2 z-[1000] bg-white rounded-lg shadow-lg border border-gray-200 p-4 min-w-[300px]">
                    <div class="flex justify-between items-start mb-2 border-b pb-2 cursor-move" id="history-header">
                        <h4 class="font-bold text-gray-800 select-none" id="history-title">Riwayat Perjalanan</h4>
                        <button type="button" onclick="clearHistory()" class="text-gray-400 hover:text-red-500 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="text-sm text-gray-600 space-y-2">
                        <div class="flex justify-between">
                            <span>Total Titik:</span>
                            <span id="history-count" class="font-semibold text-gray-900">0</span>
                        </div>
                        <div class="flex justify-between items-center bg-gray-50 p-2 rounded-md">
                            <span class="text-gray-700 font-medium">Status Pergerakan:</span>
                            <span id="history-idle" class="font-bold text-indigo-600 px-2 py-1 bg-indigo-50 rounded">Aktif</span>
                        </div>
                    </div>
                    <!-- List Titik Koordinat -->
                    <div class="mt-3 border-t pt-2 max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 pr-1" id="history-list">
                        <!-- List dinamis dimuat dari JS -->
                    </div>
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
    let map;
    let markers = {};
    let activeHistoryLayer = null; // Menyimpan layer polyline riwayat yang sedang aktif
    let historyMarkers = []; // Menyimpan marker titik riwayat
    let currentFilter = 'all'; // all, berjalan, berhenti
    const defaultCenter = [-6.2088, 106.8456]; // Jakarta Default Coordinate

    $(document).ready(function() {
        initMap();
    });

    function initMap() {
        // Initialize map
        map = L.map('map').setView(defaultCenter, 11);

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Initial fetch
        fetchLatestLocations();

        // Auto refresh setiap 30 detik
        setInterval(fetchLatestLocations, 30000);
    }

    // Custom Icon marker based on status
    function getIcon(color) {
        return L.icon({
            iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
    }

    function fetchLatestLocations() {
        $('#last-update-time').html('<i class="fas fa-spinner fa-spin mr-1"></i> Memperbarui...');

        $.ajax({
            url: '{{ route('gps-tracking.latest-locations') }}',
            method: 'GET',
            success: function(response) {
                if(response.success && response.data) {
                    updateMapMarkers(response.data);
                    
                    const now = new Date();
                    $('#last-update-time').html('<i class="fas fa-clock mr-1"></i> Diperbarui: ' + now.toLocaleTimeString());
                } else {
                    $('#last-update-time').html('<span class="text-red-500"><i class="fas fa-exclamation-circle mr-1"></i> Tidak ada data</span>');
                }
            },
            error: function() {
                $('#last-update-time').html('<span class="text-red-500"><i class="fas fa-exclamation-circle mr-1"></i> Gagal mengambil data</span>');
            }
        });
    }

    function updateMapMarkers(locations) {
        let bounds = [];
        let hasValidLocations = false;

        locations.forEach(function(loc) {
            if(loc.lat && loc.lng) {
                const position = [parseFloat(loc.lat), parseFloat(loc.lng)];
                bounds.push(position);
                hasValidLocations = true;

                // Tentukan warna ikon berdasarkan status/kecepatan
                let iconColor = 'blue';
                if(loc.status && loc.status.toLowerCase().includes('stop')) iconColor = 'red';
                else if (loc.speed > 0) iconColor = 'green';

                if(markers[loc.mobil_id]) {
                    // Update posisi marker yang sudah ada
                    markers[loc.mobil_id].setLatLng(position);
                    markers[loc.mobil_id].setIcon(getIcon(iconColor));
                } else {
                    // Buat marker baru
                    markers[loc.mobil_id] = L.marker(position, {icon: getIcon(iconColor)}).addTo(map);
                }

                // Update InfoWindow (Popup) content
                markers[loc.mobil_id].bindPopup(generateInfoWindowContent(loc));

                // Update UI Sidebar
                $(`#speed-${loc.mobil_id}`).text(`${loc.speed} km/h`);
                
                let isBerjalan = loc.speed > 0;
                let statusHtml = '';
                if(isBerjalan) {
                    statusHtml = `<i class="fas fa-circle text-green-500 text-[8px] mr-1"></i> Berjalan`;
                } else {
                    statusHtml = `<i class="fas fa-circle text-red-500 text-[8px] mr-1"></i> Berhenti`;
                }
                $(`#status-${loc.mobil_id}`).html(statusHtml);

                // Update Surat Jalan Info on Sidebar
                if (loc.info_sjs && loc.info_sjs.length > 0) {
                    const firstSj = loc.info_sjs[0];
                    const extraCount = loc.info_sjs.length - 1;
                    
                    let sjHtml = `
                        <div class="flex items-center justify-between mb-0.5">
                            <div class="text-[10px] text-gray-600 font-medium bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100 inline-block truncate flex-1 mr-1" title="${firstSj.no_surat_jalan}">
                                <i class="fas fa-file-invoice text-indigo-500 mr-1"></i> ${firstSj.no_surat_jalan}
                            </div>
                            ${extraCount > 0 ? `<div class="text-[9px] font-bold text-white bg-amber-500 px-1.5 py-0.5 rounded shrink-0" title="${extraCount} aktivitas lain berjalan">+${extraCount} SJ lain</div>` : ''}
                        </div>
                        <div class="text-[10px] text-gray-500 truncate" title="${firstSj.tujuan || '-'}"><i class="fas fa-map-marker-alt text-red-400 w-3 text-center mr-1"></i> ${firstSj.tujuan || '-'}</div>
                        <div class="text-[10px] text-gray-500 truncate" title="${firstSj.jenis_barang || '-'}"><i class="fas fa-box text-orange-400 w-3 text-center mr-1"></i> ${firstSj.jenis_barang || '-'}</div>
                    `;
                    $(`#sj-info-sidebar-${loc.mobil_id}`).html(sjHtml).removeClass('hidden').addClass('flex');
                } else {
                    $(`#sj-info-sidebar-${loc.mobil_id}`).addClass('hidden').removeClass('flex');
                }

                // Info popup is already updated, logic for filter moved to applyFilters()
            }
        });
        
        // Panggil applyFilters() sekali setelah semua marker terupdate
        applyFilters();
    }

    function generateInfoWindowContent(loc) {
        return `
            <div class="min-w-[200px]">
                <h6 class="font-bold text-sm border-b pb-2 mb-2 flex items-center">
                    <i class="fas fa-truck text-indigo-600 mr-2"></i> ${loc.nomor_polisi}
                </h6>
                <div class="text-xs space-y-1">
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 shrink-0">Armada:</span>
                        <span class="font-medium truncate" title="${loc.merek} - ${loc.jenis}">${loc.merek} - ${loc.jenis}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 shrink-0">Supir:</span>
                        <span class="font-medium truncate" title="${loc.supir}">${loc.supir}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status:</span>
                        <span class="font-medium ${loc.speed > 0 ? 'text-green-600' : 'text-red-600'}">${loc.status}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kecepatan:</span>
                        <span class="font-medium">${loc.speed} km/h</span>
                    </div>
                    ${loc.info_sjs && loc.info_sjs.length > 0 ? `
                    <div class="mt-2 pt-2 border-t border-gray-100 max-h-48 overflow-y-auto pr-1">
                        ${loc.info_sjs.map((sj, idx) => `
                            <div class="mb-3 last:mb-0 space-y-1 ${idx > 0 ? 'border-t border-gray-50 pt-2' : ''}">
                                <div class="font-semibold text-gray-700 mb-1 flex items-center justify-between">
                                    <div class="flex items-center"><i class="fas fa-file-invoice text-indigo-500 mr-1"></i> Aktifitas: ${sj.tipe}</div>
                                    ${loc.info_sjs.length > 1 ? `<span class="text-[9px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">#${loc.info_sjs.length - idx}</span>` : ''}
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="text-gray-500 shrink-0">No. SJ:</span>
                                    <span class="font-medium">${sj.no_surat_jalan || '-'}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="text-gray-500 shrink-0">Tujuan:</span>
                                    <span class="font-medium truncate" title="${sj.tujuan || '-'}">${sj.tujuan || '-'}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="text-gray-500 shrink-0">Kontainer:</span>
                                    <span class="font-medium">${sj.no_kontainer || '-'}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="text-gray-500 shrink-0">Barang:</span>
                                    <span class="font-medium truncate" title="${sj.jenis_barang || '-'}">${sj.jenis_barang || '-'}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    ` : `
                    <div class="mt-2 pt-2 border-t border-gray-100 text-gray-400 italic text-[10px] text-center">
                        Tidak ada aktivitas berjalan
                    </div>
                    `}
                    <div class="flex justify-between gap-2 mt-2 border-t border-gray-100 pt-2">
                        <span class="text-gray-500 shrink-0">Alamat:</span>
                        <span class="font-medium text-right text-[10px] sm:text-xs line-clamp-3" id="address-${loc.mobil_id}" title="${loc.alamat || ''}">
                            ${loc.alamat ? loc.alamat : '<button type="button" onclick="event.stopPropagation(); loadAddress(' + loc.mobil_id + ', ' + loc.lat + ', ' + loc.lng + ')" class="text-indigo-600 hover:underline">Tampilkan Alamat</button>'}
                        </span>
                    </div>
                    <div class="mt-3 pt-2 border-t border-gray-100 text-gray-400 flex items-center">
                        <i class="far fa-clock mr-1"></i> Update: ${loc.last_update}
                    </div>
                </div>
            </div>
        `;
    }

    async function loadAddress(id, lat, lng) {
        const el = document.getElementById('address-' + id);
        if(!el) return;
        el.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';
        try {
            const res = await fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1');
            const data = await res.json();
            if(data && data.display_name) {
                let address = data.address.road ? data.address.road + ', ' : '';
                address += data.address.city || data.address.town || data.address.village || data.address.county || '';
                if (!address || address.length < 5) address = data.display_name;
                el.innerText = address;
                el.title = data.display_name;
            } else {
                el.innerText = 'Alamat tidak ditemukan';
            }
        } catch(e) {
            el.innerHTML = '<button onclick="loadAddress(' + id + ', ' + lat + ', ' + lng + ')" class="text-red-500 hover:underline">Gagal, Coba Lagi</button>';
        }
    }

    function focusOnTruck(mobilId) {
        if(markers[mobilId]) {
            // Jika marker sedang disembunyikan oleh filter, ubah filter ke 'all' dulu
            if (!map.hasLayer(markers[mobilId])) {
                setFilter('all');
            }

            // Center map to marker
            map.setView(markers[mobilId].getLatLng(), 15);
            
            // Open popup
            markers[mobilId].openPopup();
            
            // Highlight list
            $('.truck-item').removeClass('bg-indigo-50 border-l-4 border-indigo-500');
            $(`[data-id="${mobilId}"]`).addClass('bg-indigo-50 border-l-4 border-indigo-500');
        } else {
            alert('Lokasi armada ini belum diketahui atau belum diperbarui.');
        }
    }

    function setFilter(filter) {
        currentFilter = filter;
        
        // Update styling tombol
        $('.filter-btn').removeClass('bg-indigo-600 text-white ring-indigo-600').addClass('bg-white text-gray-700 ring-gray-300');
        $(`#filter-${filter}`).removeClass('bg-white text-gray-700 ring-gray-300').addClass('bg-indigo-600 text-white ring-indigo-600');

        applyFilters();
    }

    function applyFilters() {
        const searchQuery = ($('#search-truck').val() || '').toLowerCase();

        // Terapkan filter gabungan ke DOM list dan Map
        $('.truck-item').each(function() {
            let id = $(this).data('id');
            let statusText = $(`#status-${id}`).text().toLowerCase();
            let nopolText = $(this).find('p.font-semibold').text().toLowerCase();
            let supirText = $(this).find('p.text-xs.font-medium').text().toLowerCase();
            
            let isBerjalan = statusText.includes('berjalan');
            let isBerhenti = statusText.includes('berhenti');
            
            // Check status filter
            let passStatus = false;
            if (currentFilter === 'all') passStatus = true;
            else if (currentFilter === 'berjalan' && isBerjalan) passStatus = true;
            else if (currentFilter === 'berhenti' && !isBerjalan) passStatus = true;

            // Check search filter
            let passSearch = true;
            if (searchQuery.trim() !== '') {
                passSearch = nopolText.includes(searchQuery) || supirText.includes(searchQuery);
            }

            let show = passStatus && passSearch;

            if (show) {
                $(this).show();
                if (markers[id] && !map.hasLayer(markers[id])) {
                    markers[id].addTo(map);
                }
            } else {
                $(this).hide();
                if (markers[id] && map.hasLayer(markers[id])) {
                    map.removeLayer(markers[id]);
                }
            }
        });

        // Update truck counter based on visible items
        let visibleCount = $('.truck-item:visible').length;
        $('#truck-counter').text(visibleCount + ' Truk');
    }

    function focusOnHistoryPoint(idx, lat, lng, element) {
        if (lat && lng) {
            map.setView([lat, lng], 17);
            
            // Reset all markers
            historyMarkers.forEach((marker) => {
                if (marker) {
                    marker.setStyle({ radius: 8, weight: 2, color: '#ffffff' });
                }
            });

            if (historyMarkers[idx]) {
                historyMarkers[idx].setStyle({ radius: 12, weight: 3, color: '#4f46e5' });
                historyMarkers[idx].bringToFront();
                historyMarkers[idx].openPopup();
            }
            
            // Highlight the clicked item
            $('#history-list li').removeClass('bg-indigo-50 border-indigo-100 shadow-sm');
            $(element).addClass('bg-indigo-50 border-indigo-100 shadow-sm');
        }
    }

    function clearHistory() {
        if (activeHistoryLayer) {
            map.removeLayer(activeHistoryLayer);
            activeHistoryLayer = null;
        }
        $('#history-panel').addClass('hidden');
    }

    async function loadHistory(mobilId) {
        // Tampilkan loading state
        $('#history-panel').removeClass('hidden');
        $('#history-title').html('<i class="fas fa-spinner fa-spin mr-2"></i>Memuat Riwayat...');
        $('#history-count').text('-');
        $('#history-idle').text('-');

        try {
            const response = await fetch(`{{ route('gps-tracking.index') }}/history/${mobilId}`);
            const result = await response.json();

            if (result.success && result.data) {
                const data = result.data;
                const history = data.history;

                $('#history-title').html(`<i class="fas fa-history mr-2 text-indigo-600"></i>${data.mobil.nomor_polisi}`);
                $('#history-count').text(history.length);

                let idleText = '';
                if (data.days_not_moving >= 14) {
                    idleText = `<span class="text-red-600">Tidak bergerak &ge; 14 hari</span>`;
                } else if (data.days_not_moving > 0) {
                    idleText = `<span class="text-orange-500">Tidak bergerak ${data.days_not_moving} hari</span>`;
                } else {
                    idleText = `<span class="text-green-600">Aktif Bergerak</span>`;
                }
                $('#history-idle').html(idleText);

                // Bersihkan history lama jika ada
                if (activeHistoryLayer) {
                    map.removeLayer(activeHistoryLayer);
                }
                historyMarkers = [];

                if (history.length > 0) {
                    let latlngs = [];
                    let layers = [];
                    let listHtml = '<ul class="space-y-3 relative border-l-2 border-gray-100 ml-2 mt-2">';
                    
                    // Kita reverse agar data terbaru di atas
                    let reversedHistory = [...history].reverse();
                    
                    reversedHistory.forEach((item, idx) => {
                        if (item.lat && item.lng) {
                            const pt = [parseFloat(item.lat), parseFloat(item.lng)];
                            
                            // Hanya tambahkan point untuk polyline (perlu urutan asli, bukan reversed, 
                            // jadi tidak kita masukkan ke latlngs di dalam loop reversed ini)
                            
                            let popupContent = `
                                <div class="text-xs min-w-[150px]">
                                    <div class="font-bold border-b pb-1 mb-1"><i class="far fa-clock mr-1 text-indigo-600"></i> ${item.recorded_at}</div>
                                    <div class="flex justify-between mt-1"><span>Kecepatan:</span> <span class="font-medium">${item.speed} km/h</span></div>
                                    <div class="flex justify-between"><span>Status:</span> <span class="font-medium ${item.speed > 0 ? 'text-green-600' : 'text-red-600'}">${item.status}</span></div>
                                </div>
                            `;
                            
                            let marker = L.circleMarker(pt, {
                                radius: 8,
                                fillColor: item.speed > 0 ? '#10b981' : '#ef4444',
                                color: '#ffffff',
                                weight: 2,
                                opacity: 1,
                                fillOpacity: 0.9
                            }).bindPopup(popupContent);
                            layers.push(marker);
                            historyMarkers[idx] = marker;
                            
                            let elId = 'hist-addr-' + Math.random().toString(36).substr(2, 9);
                            let locName = item.alamat ? item.alamat : `<span id="${elId}"><button type="button" onclick="event.stopPropagation(); loadHistoryAddress('${elId}', ${item.lat}, ${item.lng})" class="text-indigo-600 hover:underline text-[10px]"><i class="fas fa-map-marker-alt"></i> Tampilkan Alamat</button></span>`;
                            
                            listHtml += `
                                <li class="pl-4 relative cursor-pointer hover:bg-gray-50 transition-colors py-2 rounded -ml-2 border border-transparent" onclick="focusOnHistoryPoint(${idx}, ${item.lat}, ${item.lng}, this)">
                                    <div class="absolute w-2.5 h-2.5 ${idx === 0 ? 'bg-indigo-600' : 'bg-gray-400'} rounded-full left-[2px] top-3 border-2 border-white shadow-sm"></div>
                                    <div class="flex justify-between items-center mb-0.5 ml-2">
                                        <div class="text-[10px] font-semibold ${idx === 0 ? 'text-indigo-600' : 'text-gray-500'}"><i class="far fa-clock"></i> ${item.recorded_at}</div>
                                        <div class="text-[9px] font-medium px-1.5 py-0.5 rounded ${item.speed > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${item.speed} km/h</div>
                                    </div>
                                    <div class="text-xs font-medium text-gray-800 break-words ml-2" title="${item.alamat || ''}">${locName}</div>
                                </li>
                            `;
                        }
                    });
                    listHtml += '</ul>';
                    $('#history-list').html(listHtml);
                    
                    // Build polyline points in original order
                    history.forEach(item => {
                        if (item.lat && item.lng) {
                            latlngs.push([parseFloat(item.lat), parseFloat(item.lng)]);
                        }
                    });

                    // Gambar garis polyline
                    let polyline = L.polyline(latlngs, {
                        color: '#4f46e5', // indigo-600
                        weight: 4,
                        opacity: 0.5,
                        smoothFactor: 1
                    });
                    
                    layers.unshift(polyline);
                    activeHistoryLayer = L.layerGroup(layers).addTo(map);

                    // Sesuaikan zoom peta ke seluruh rentang garis
                    map.fitBounds(polyline.getBounds(), { padding: [50, 50] });
                } else {
                    alert('Belum ada data riwayat yang tersimpan untuk armada ini.');
                    clearHistory();
                }
            } else {
                alert('Gagal mengambil data riwayat.');
                clearHistory();
            }
        } catch (error) {
            console.error('Error fetching history:', error);
            alert('Terjadi kesalahan koneksi.');
            clearHistory();
        }
    }

    async function loadHistoryAddress(elId, lat, lng) {
        const el = document.getElementById(elId);
        if(!el) return;
        el.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';
        try {
            const res = await fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1');
            const data = await res.json();
            if(data && data.display_name) {
                let address = data.address.road ? data.address.road + ', ' : '';
                address += data.address.city || data.address.town || data.address.village || data.address.county || '';
                if (!address || address.length < 5) address = data.display_name;
                el.innerText = address;
                el.title = data.display_name;
            } else {
                el.innerText = 'Alamat tidak ditemukan';
            }
        } catch(e) {
            el.innerHTML = `<button onclick="loadHistoryAddress('${elId}', ${lat}, ${lng})" class="text-red-500 hover:underline text-[10px]">Gagal, Coba Lagi</button>`;
        }
    }
    // === DRAGGABLE HISTORY PANEL ===
    const dragPanel = document.getElementById('history-panel');
    const dragHeader = document.getElementById('history-header');

    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

    dragHeader.onmousedown = dragMouseDown;

    function dragMouseDown(e) {
        e = e || window.event;
        if (e.target.closest('button')) return;

        e.preventDefault();
        pos3 = e.clientX;
        pos4 = e.clientY;
        
        if (dragPanel.classList.contains('-translate-x-1/2')) {
            let rect = dragPanel.getBoundingClientRect();
            let parentRect = dragPanel.parentElement.getBoundingClientRect();
            
            dragPanel.classList.remove('left-1/2', 'transform', '-translate-x-1/2', 'top-4');
            
            dragPanel.style.left = (rect.left - parentRect.left) + "px";
            dragPanel.style.top = (rect.top - parentRect.top) + "px";
        }

        document.onmouseup = closeDragElement;
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        
        dragPanel.style.top = (dragPanel.offsetTop - pos2) + "px";
        dragPanel.style.left = (dragPanel.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
        document.onmouseup = null;
        document.onmousemove = null;
    }
</script>
@endpush
