@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-ship mr-3 text-orange-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">OB (Operasional Bongkaran)</h1>
                    <p class="text-gray-600">Pilih kapal dan nomor voyage untuk mulai operasional</p>
                </div>
            </div>
            <div>
                <a href="{{ route('tagihan-ob.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md mr-2">
                    Ke Tagihan OB
                </a>
                <a href="{{ route('pranota-ob.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Ke Pranota OB
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="obSelectForm" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="kegiatan" class="block text-sm font-medium text-gray-700 mb-2">Kegiatan <span class="text-red-500">*</span></label>
                    <select id="kegiatan" name="kegiatan" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        <option value="">--Pilih Kegiatan--</option>
                        <option value="bongkar" {{ request('kegiatan') == 'bongkar' ? 'selected' : '' }}>Bongkar</option>
                        <option value="muat" {{ request('kegiatan') == 'muat' ? 'selected' : '' }}>Muat</option>
                    </select>
                </div>

                <div>
                    <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">Kapal <span class="text-red-500">*</span></label>
                    <select id="nama_kapal" name="nama_kapal" class="w-full px-3 py-2 border border-gray-300 rounded-md" required disabled>
                        <option value="">--Pilih Kegiatan Terlebih Dahulu--</option>
                    </select>
                </div>

                <div>
                    <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">No Voyage <span class="text-red-500">*</span></label>
                    <select id="no_voyage" name="no_voyage" class="w-full px-3 py-2 border border-gray-300 rounded-md" required disabled>
                        <option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <button type="button" id="goToTagihanOB" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-md">
                    <i class="fas fa-file-invoice mr-2"></i>Ke Tagihan OB
                </button>
                <button type="button" id="goToOBIndex" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">
                    <i class="fas fa-arrow-right mr-2"></i>Lanjutkan ke OB
                </button>
            </div>
        </form>
    </div>

    <!-- Info Section -->
    <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 mt-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
            <div>
                <h3 class="text-sm font-medium text-blue-900">Informasi OB (Operasional Bongkaran)</h3>
                <p class="text-sm text-blue-700 mt-1">Setelah memilih kapal dan voyage, Anda dapat:</p>
                <ul class="text-sm text-blue-700 mt-2 space-y-1">
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
    const goToTagihanBtn = document.getElementById('goToTagihanOB');
    const goToOBIndexBtn = document.getElementById('goToOBIndex');

    // Handle kegiatan selection
    kegiatanSelect.addEventListener('change', function() {
        const kegiatan = this.value;
        
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
        const url = kegiatan === 'bongkar' 
            ? '{{ route("ob.get-kapal-bongkar") }}' 
            : '{{ route("ob.get-kapal-muat") }}';

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
        const url = kegiatan === 'bongkar'
            ? `{{ route('ob.get-voyage-bongkar') }}?nama_kapal=${encodeURIComponent(kapalName)}`
            : `{{ route('ob.get-voyage-muat') }}?nama_kapal=${encodeURIComponent(kapalName)}`;

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

    // Go to Tagihan OB with filter
    goToTagihanBtn.addEventListener('click', function() {
        const kegiatan = kegiatanSelect.value;
        const kapalName = kapalSelect.value;
        const voyage = voyageSelect.value;

        if (!kegiatan || !kapalName || !voyage) {
            alert('Silakan pilih kegiatan, kapal, dan voyage terlebih dahulu');
            return;
        }
        
        // Redirect to Tagihan OB with filter parameters
        const url = new URL('{{ route("tagihan-ob.index") }}', window.location.origin);
        url.searchParams.set('kegiatan', kegiatan);
        url.searchParams.set('nama_kapal', kapalName);
        url.searchParams.set('no_voyage', voyage);
        
        window.location.href = url.toString();
    });

    // Go to OB Index with filter
    goToOBIndexBtn.addEventListener('click', function() {
        const kegiatan = kegiatanSelect.value;
        const kapalName = kapalSelect.value;
        const voyage = voyageSelect.value;

        if (!kegiatan || !kapalName || !voyage) {
            alert('Silakan pilih kegiatan, kapal, dan voyage terlebih dahulu');
            return;
        }
        
        // Redirect to OB Index with filter parameters
        const url = new URL('{{ route("ob.index") }}', window.location.origin);
        url.searchParams.set('kegiatan', kegiatan);
        url.searchParams.set('nama_kapal', kapalName);
        url.searchParams.set('no_voyage', voyage);
        
        window.location.href = url.toString();
    });
});
</script>

@endsection
