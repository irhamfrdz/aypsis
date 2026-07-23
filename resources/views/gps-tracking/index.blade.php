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
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">{{ $mobils->count() }} Truk</span>
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
                            </div>
                            <div class="shrink-0 flex flex-col items-end">
                                <p class="text-sm leading-6 text-gray-900 font-medium truck-speed" id="speed-{{ $mobil->id }}">- km/h</p>
                                <p class="mt-1 text-xs leading-5 text-gray-500 truck-status flex items-center" id="status-{{ $mobil->id }}">
                                    <i class="fas fa-circle text-[8px] mr-1 text-gray-400"></i> Mencari sinyal...
                                </p>
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
                
                let statusHtml = '';
                if(loc.speed > 0) {
                    statusHtml = `<i class="fas fa-circle text-green-500 text-[8px] mr-1"></i> Berjalan`;
                } else {
                    statusHtml = `<i class="fas fa-circle text-red-500 text-[8px] mr-1"></i> Berhenti`;
                }
                $(`#status-${loc.mobil_id}`).html(statusHtml);
            }
        });
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
                    <div class="mt-3 pt-2 border-t border-gray-100 text-gray-400 flex items-center">
                        <i class="far fa-clock mr-1"></i> Update: ${loc.last_update}
                    </div>
                </div>
            </div>
        `;
    }

    function focusOnTruck(mobilId) {
        if(markers[mobilId]) {
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
</script>
@endpush
