@extends('layouts.app')

@section('title', 'Live Tracking Armada')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-map-marked-alt text-primary mr-2"></i> Live Tracking Armada (GPS.id)
                </h2>
                <div>
                    <span id="last-update-time" class="text-muted mr-3">Menunggu pembaruan...</span>
                    <button class="btn btn-sm btn-outline-primary" onclick="fetchLatestLocations()">
                        <i class="fas fa-sync-alt"></i> Refresh Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(empty($googleMapsApiKey))
    <div class="alert alert-warning">
        <h5><i class="fas fa-exclamation-triangle"></i> Google Maps API Key Belum Dikonfigurasi</h5>
        <p>Silakan tambahkan <code>GOOGLE_MAPS_API_KEY=KODE_API_ANDA</code> pada file <strong>.env</strong> Anda untuk dapat memuat Peta Google Maps.</p>
    </div>
    @endif

    <div class="row">
        <!-- Sidebar Daftar Truk -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="mb-0">Daftar Armada Aktif</h5>
                    <p class="text-muted small">Truk dengan IMEI terdaftar</p>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" id="truck-list">
                        @forelse($mobils as $mobil)
                        <li class="list-group-item list-group-item-action truck-item" data-id="{{ $mobil->id }}" onclick="focusOnTruck({{ $mobil->id }})" style="cursor: pointer;">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-bold">{{ $mobil->nomor_polisi }}</h6>
                                <small class="text-muted truck-speed" id="speed-{{ $mobil->id }}">- km/h</small>
                            </div>
                            <p class="mb-1 small">{{ $mobil->merek }} - {{ $mobil->jenis }}</p>
                            <small class="truck-status text-secondary" id="status-{{ $mobil->id }}">
                                <i class="fas fa-circle" style="font-size: 8px;"></i> Mencari sinyal...
                            </small>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted">
                            <p class="mb-0 mt-3"><i class="fas fa-truck-slash fa-2x mb-2"></i></p>
                            <p>Belum ada armada yang didaftarkan IMEI GPS.</p>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Area Peta -->
        <div class="col-md-9 mb-4">
            <div class="card shadow-sm">
                <div class="card-body p-1">
                    <div id="map" style="width: 100%; height: 600px; border-radius: 4px;"></div>
                </div>
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
