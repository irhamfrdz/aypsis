@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-3 md:p-6 mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center">
                <i class="fas fa-ship mr-2 md:mr-3 text-orange-600 text-xl md:text-2xl"></i>
                <div>
                    <h1 class="text-lg md:text-2xl font-bold text-gray-800">OB (Operasional Bongkaran)</h1>
                    <p class="text-xs md:text-base text-gray-600">Pilih kapal dan nomor voyage untuk mulai operasional</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('tagihan-ob.index') }}" class="flex-1 md:flex-none bg-gray-500 hover:bg-gray-600 text-white px-3 md:px-4 py-2 rounded-md text-xs md:text-sm text-center">
                    <i class="fas fa-file-invoice md:mr-2"></i><span class="md:inline">Ke Tagihan OB</span>
                </a>
                <a href="{{ route('pranota-ob.index') }}" class="flex-1 md:flex-none bg-blue-500 hover:bg-blue-600 text-white px-3 md:px-4 py-2 rounded-md text-xs md:text-sm text-center">
                    <i class="fas fa-file-alt md:mr-2"></i><span class="md:inline">Ke Pranota OB</span>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-3 md:p-6">
        <form id="obSelectForm" method="GET">
            <div class="space-y-4 md:space-y-0 md:grid md:grid-cols-1 md:grid-cols-3 md:gap-6">
                <div>
                    <label for="kegiatan" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Kegiatan <span class="text-red-500">*</span></label>
                    <select id="kegiatan" name="kegiatan" class="w-full px-3 py-2.5 md:py-2 text-sm md:text-base border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required>
                        <option value="">--Pilih Kegiatan--</option>
                        <option value="bongkar" {{ request('kegiatan') == 'bongkar' ? 'selected' : '' }}>Bongkar</option>
                        <option value="muat" {{ request('kegiatan') == 'muat' ? 'selected' : '' }}>Muat</option>
                        <option value="antar_gudang" {{ request('kegiatan') == 'antar_gudang' ? 'selected' : '' }}>Antar Gudang</option>
                    </select>
                </div>

                <div id="kapal_container">
                    <label for="nama_kapal" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Kapal <span class="text-red-500">*</span></label>
                    <select id="nama_kapal" name="nama_kapal" class="w-full px-3 py-2.5 md:py-2 text-sm md:text-base border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required disabled>
                        <option value="">--Pilih Kegiatan Terlebih Dahulu--</option>
                    </select>
                </div>

                <div id="voyage_container">
                    <label for="no_voyage" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">No Voyage <span class="text-red-500">*</span></label>
                    <select id="no_voyage" name="no_voyage" class="w-full px-3 py-2.5 md:py-2 text-sm md:text-base border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required disabled>
                        <option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>
                    </select>
                </div>

                <div id="gudang_container" class="hidden">
                    <label for="gudang_id" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Pilih Gudang <span class="text-red-500">*</span></label>
                    <select id="gudang_id" name="gudang_id" class="w-full px-3 py-2.5 md:py-2 text-sm md:text-base border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">--Pilih Gudang--</option>
                        @foreach($gudangs as $gudang)
                            <option value="{{ $gudang->id }}">{{ $gudang->nama_gudang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 md:mt-6">
                <button type="button" id="goToOBIndex" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white px-6 py-3 md:py-2 rounded-md text-sm md:text-base font-medium shadow-sm hover:shadow-md transition-all">
                    <i class="fas fa-arrow-right mr-2"></i>Lanjutkan ke OB
                </button>
            </div>
        </form>
    </div>

    <!-- Info Section -->
    <div class="bg-blue-50 rounded-lg border border-blue-200 p-3 md:p-4 mt-4 md:mt-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 mr-2 md:mr-3 mt-0.5 md:mt-1 text-sm md:text-base"></i>
            <div>
                <h3 class="text-xs md:text-sm font-medium text-blue-900">Informasi OB (Operasional Bongkaran)</h3>
                <p class="text-xs md:text-sm text-blue-700 mt-1">Setelah memilih kapal dan voyage, Anda dapat:</p>
                <ul class="text-xs md:text-sm text-blue-700 mt-2 space-y-1">
                    <li>• <strong>Tagihan OB:</strong> Mengelola tagihan operasional bongkaran</li>
                    <li>• <strong>Pranota OB:</strong> Membuat dan mengelola pranota untuk tagihan OB</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kegiatanSelect = document.getElementById('kegiatan');
    const kapalSelect = document.getElementById('nama_kapal');
    const voyageSelect = document.getElementById('no_voyage');
    const gudangSelect = document.getElementById('gudang_id');
    const kapalContainer = document.getElementById('kapal_container');
    const voyageContainer = document.getElementById('voyage_container');
    const gudangContainer = document.getElementById('gudang_container');
    const goToOBIndexBtn = document.getElementById('goToOBIndex');

    // Handle kegiatan selection
    kegiatanSelect.addEventListener('change', function() {
        const kegiatan = this.value;
        
        // Handle UI toggling for Antar Gudang
        if (kegiatan === 'antar_gudang') {
            kapalContainer.classList.add('hidden');
            voyageContainer.classList.add('hidden');
            gudangContainer.classList.remove('hidden');
            
            kapalSelect.required = false;
            voyageSelect.required = false;
            gudangSelect.required = true;
            
            return; // No need to fetch kapal for antar_gudang
        } else {
            kapalContainer.classList.remove('hidden');
            voyageContainer.classList.remove('hidden');
            gudangContainer.classList.add('hidden');
            
            kapalSelect.required = true;
            voyageSelect.required = true;
            gudangSelect.required = false;
        }

        // Reset kapal and voyage
        kapalSelect.innerHTML = '<option value="">Loading...</option>';
        kapalSelect.disabled = true;
        voyageSelect.innerHTML = '<option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>';
        voyageSelect.disabled = true;

        if (!kegiatan) {
            kapalSelect.innerHTML = '<option value="">--Pilih Kegiatan Terlebih Dahulu--</option>';
            kapalSelect.disabled = true;
            return;
        }

        console.log('Kegiatan dipilih:', kegiatan);

        // Fetch kapal berdasarkan kegiatan
        let url = '';
        if (kegiatan === 'bongkar') {
            url = '{{ route("ob.get-kapal-bongkar", [], false) }}';
        } else if (kegiatan === 'muat') {
            url = '{{ route("ob.get-kapal-muat", [], false) }}';
        } else if (kegiatan === 'antar_gudang') {
            url = '{{ route("ob.get-kapal-antar-gudang", [], false) }}';
        }

        fetch(url, {
            method: 'GET',
            headers: { 
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            console.log('Kapal data:', data);
            kapalSelect.innerHTML = '';
            if (data.success && data.kapals && data.kapals.length) {
                kapalSelect.innerHTML = '<option value="">--Pilih Kapal--</option>';
                data.kapals.forEach(k => {
                    kapalSelect.innerHTML += `<option value="${k}">${k}</option>`;
                });
                console.log('Kapal loaded:', data.kapals.length);
            } else {
                kapalSelect.innerHTML = '<option value="">Tidak ada kapal tersedia</option>';
            }
            kapalSelect.disabled = false;
        })
        .catch(err => {
            console.error('Fetch error:', err);
            kapalSelect.innerHTML = '<option value="">Error loading kapal</option>';
            kapalSelect.disabled = false;
        });
    });

    // Handle kapal selection
    kapalSelect.addEventListener('change', function() {
        const kapalName = this.value;
        const kegiatan = kegiatanSelect.value;
        
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;

        if (!kapalName || !kegiatan) {
            voyageSelect.innerHTML = '<option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>';
            voyageSelect.disabled = false;
            return;
        }

        console.log('Nama kapal dipilih:', kapalName);

        // Fetch voyage berdasarkan kegiatan dan kapal
        let url = '';
        if (kegiatanSelect.value === 'bongkar') {
            url = `{{ route('ob.get-voyage-bongkar', [], false) }}?nama_kapal=${encodeURIComponent(kapalName)}`;
        } else if (kegiatanSelect.value === 'muat') {
            url = `{{ route('ob.get-voyage-muat', [], false) }}?nama_kapal=${encodeURIComponent(kapalName)}`;
        } else if (kegiatanSelect.value === 'antar_gudang') {
            url = `{{ route('ob.get-voyage-antar-gudang', [], false) }}?nama_kapal=${encodeURIComponent(kapalName)}`;
        }

        fetch(url, {
            method: 'GET',
            headers: { 
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            console.log('Voyage data:', data);
            voyageSelect.innerHTML = '';
            if (data.success && data.voyages && data.voyages.length) {
                voyageSelect.innerHTML = '<option value="">--Pilih Voyage--</option>';
                data.voyages.forEach(v => {
                    voyageSelect.innerHTML += `<option value="${v}">${v}</option>`;
                });
                console.log('Voyage loaded:', data.voyages.length);
                // If there is a preselected voyage in query params, select it
                if (preselectedVoyage) {
                    voyageSelect.value = preselectedVoyage;
                }
            } else {
                voyageSelect.innerHTML = '<option value="">Belum ada voyage untuk kapal ini</option>';
            }
            voyageSelect.disabled = false;
        })
        .catch(err => {
            console.error('Fetch error:', err);
            voyageSelect.innerHTML = '<option value="">Error loading voyage</option>';
            voyageSelect.disabled = false;
        });
    });

    // Preselect values from query params
    const preselectedKegiatan = '{{ request('kegiatan') }}';
    const preselectedKapal = '{{ request('nama_kapal') }}';
    const preselectedVoyage = '{{ request('no_voyage') }}';
    
    if (preselectedKegiatan) {
        kegiatanSelect.value = preselectedKegiatan;
        kegiatanSelect.dispatchEvent(new Event('change'));

        if (preselectedKapal) {
            setTimeout(() => {
                kapalSelect.value = preselectedKapal;
                kapalSelect.dispatchEvent(new Event('change'));
            }, 700);
        }
    }

    // Go to OB Index with filter
    goToOBIndexBtn.addEventListener('click', function() {
        const kegiatan = kegiatanSelect.value;
        const kapalName = kapalSelect.value;
        const voyage = voyageSelect.value;
        const gudangId = gudangSelect.value;

        if (!kegiatan) {
            alert('Silakan pilih kegiatan terlebih dahulu');
            return;
        }

        if (kegiatan === 'antar_gudang') {
            if (!gudangId) {
                alert('Silakan pilih gudang terlebih dahulu');
                return;
            }
        } else {
            if (!kapalName || !voyage) {
                alert('Silakan pilih kapal dan voyage terlebih dahulu');
                return;
            }
        }
        
        // Redirect to OB Index with filter parameters
        const url = new URL('{{ route("ob.index", [], false) }}', window.location.origin);
        url.searchParams.set('kegiatan', kegiatan);
        
        if (kegiatan === 'antar_gudang') {
            url.searchParams.set('gudang_id', gudangId);
        } else {
            url.searchParams.set('nama_kapal', kapalName);
            url.searchParams.set('no_voyage', voyage);
        }
        
        window.location.href = url.toString();
    });
});
</script>

@endsection
