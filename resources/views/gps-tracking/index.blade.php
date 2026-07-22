@extends('layouts.app')

@section('title', 'Live Tracking Armada')

@section('content')
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

    @if(empty($googleMapsApiKey))
    <div class="rounded-md bg-yellow-50 p-4 mb-6 border border-yellow-200 shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Google Maps API Key Belum Dikonfigurasi</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Silakan tambahkan <code class="font-bold bg-yellow-100 px-1 py-0.5 rounded">GOOGLE_MAPS_API_KEY=KODE_API_ANDA</code> pada file <strong>.env</strong> Anda untuk dapat memuat Peta Google Maps.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar Daftar Truk -->
        <div class="w-full lg:w-1/3 xl:w-1/4">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden flex flex-col h-[600px]">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Daftar Armada Aktif</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Truk dengan IMEI terdaftar</p>
                </div>
                <div class="flex-1 overflow-y-auto">
                    <ul role="list" class="divide-y divide-gray-100" id="truck-list">
                        @forelse($mobils as $mobil)
                        <li class="relative flex justify-between gap-x-6 px-4 py-4 hover:bg-gray-50 transition-colors cursor-pointer truck-item group" data-id="{{ $mobil->id }}" onclick="focusOnTruck({{ $mobil->id }})">
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm font-semibold leading-6 text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $mobil->nomor_polisi }}</p>
                                <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ $mobil->merek }} - {{ $mobil->jenis }}</p>
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
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden h-[600px] relative">
                <div id="map" class="w-full h-full"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(!empty($googleMapsApiKey))
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap" async defer></script>
@endif

<script>
    let map;
    let markers = {};
    let infoWindows = {};
    const defaultCenter = { lat: 1.1301, lng: 104.0529 }; // Batam

    function initMap() {
        if (typeof google === 'undefined') {
            document.getElementById('map').innerHTML = '<div class="d-flex justify-content-center align-items-center h-100 bg-light"><p class="text-muted">Google Maps tidak dapat dimuat. Periksa API Key Anda.</p></div>';
            return;
        }

        map = new google.maps.Map(document.getElementById('map'), {
            center: defaultCenter,
            zoom: 11,
            mapTypeId: 'roadmap',
            streetViewControl: false,
        });

        // Initial fetch
        fetchLatestLocations();

        // Auto refresh setiap 30 detik
        setInterval(fetchLatestLocations, 30000);
    }

    function fetchLatestLocations() {
        if (typeof google === 'undefined') return;

        $('#last-update-time').html('<i class="fas fa-spinner fa-spin"></i> Memperbarui...');

        $.ajax({
            url: '{{ route('gps-tracking.latest-locations') }}',
            method: 'GET',
            success: function(response) {
                if(response.success && response.data) {
                    updateMapMarkers(response.data);
                    
                    const now = new Date();
                    $('#last-update-time').html('Terakhir diperbarui: ' + now.toLocaleTimeString());
                }
            },
            error: function() {
                $('#last-update-time').html('<span class="text-danger"><i class="fas fa-exclamation-circle"></i> Gagal mengambil data</span>');
            }
        });
    }

    function updateMapMarkers(locations) {
        let bounds = new google.maps.LatLngBounds();
        let hasValidLocations = false;

        locations.forEach(function(loc) {
            if(loc.lat && loc.lng) {
                const position = { lat: parseFloat(loc.lat), lng: parseFloat(loc.lng) };
                bounds.extend(position);
                hasValidLocations = true;

                // Tentukan warna ikon berdasarkan status
                let iconColor = 'blue';
                if(loc.status && loc.status.toLowerCase().includes('stop')) iconColor = 'red';
                else if (loc.speed > 0) iconColor = 'green';

                const iconUrl = `http://maps.google.com/mapfiles/ms/icons/${iconColor}-dot.png`;

                if(markers[loc.mobil_id]) {
                    // Update posisi marker yang sudah ada
                    markers[loc.mobil_id].setPosition(position);
                    markers[loc.mobil_id].setIcon(iconUrl);
                } else {
                    // Buat marker baru
                    markers[loc.mobil_id] = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: loc.nomor_polisi,
                        icon: iconUrl
                    });

                    // Buat InfoWindow
                    infoWindows[loc.mobil_id] = new google.maps.InfoWindow({
                        content: generateInfoWindowContent(loc)
                    });

                    // Event Listener klik marker
                    markers[loc.mobil_id].addListener('click', function() {
                        // Tutup semua infowindow
                        Object.values(infoWindows).forEach(iw => iw.close());
                        // Buka yang ini
                        infoWindows[loc.mobil_id].open(map, markers[loc.mobil_id]);
                    });
                }

                // Update InfoWindow content
                infoWindows[loc.mobil_id].setContent(generateInfoWindowContent(loc));

                // Update UI Sidebar
                $(`#speed-${loc.mobil_id}`).text(`${loc.speed} km/h`);
                
                let statusHtml = '';
                if(loc.speed > 0) {
                    statusHtml = `<i class="fas fa-circle text-success" style="font-size: 8px;"></i> Berjalan`;
                } else {
                    statusHtml = `<i class="fas fa-circle text-danger" style="font-size: 8px;"></i> Berhenti`;
                }
                $(`#status-${loc.mobil_id}`).html(statusHtml);
            }
        });

        // Sesuaikan zoom agar semua truk terlihat (hanya jika baru pertama kali atau ada request)
        // if(hasValidLocations && Object.keys(markers).length <= locations.length) {
        //     map.fitBounds(bounds);
        // }
    }

    function generateInfoWindowContent(loc) {
        return `
            <div style="min-width: 200px; padding: 5px;">
                <h6 style="margin-top: 0; margin-bottom: 5px; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                    <i class="fas fa-truck"></i> ${loc.nomor_polisi}
                </h6>
                <div style="font-size: 13px; line-height: 1.5;">
                    <div><strong>Armada:</strong> ${loc.merek} - ${loc.jenis}</div>
                    <div><strong>Status:</strong> ${loc.status}</div>
                    <div><strong>Kecepatan:</strong> ${loc.speed} km/h</div>
                    <div style="color: #777; font-size: 11px; margin-top: 5px;">
                        <i class="far fa-clock"></i> Update: ${loc.last_update}
                    </div>
                </div>
            </div>
        `;
    }

    function focusOnTruck(mobilId) {
        if(markers[mobilId] && typeof google !== 'undefined') {
            map.panTo(markers[mobilId].getPosition());
            map.setZoom(15);
            
            // Buka infowindow
            Object.values(infoWindows).forEach(iw => iw.close());
            infoWindows[mobilId].open(map, markers[mobilId]);
            
            // Highlight list
            $('.truck-item').removeClass('bg-light');
            $(`[data-id="${mobilId}"]`).addClass('bg-light');
        } else {
            alert('Lokasi armada ini belum diketahui atau belum diperbarui.');
        }
    }
</script>
@endpush
