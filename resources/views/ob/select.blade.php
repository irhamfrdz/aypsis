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
                <a href="{{ route('pranota-ob.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Ke Pranota OB
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="obSelectForm" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">Kapal <span class="text-red-500">*</span></label>
                    <select id="nama_kapal" name="nama_kapal" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        <option value="">--Pilih Kapal--</option>
                        @foreach($ships as $ship)
                            <option value="{{ $ship->nama_kapal }}" {{ request('nama_kapal') == $ship->nama_kapal ? 'selected' : '' }}>{{ $ship->nama_kapal }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">No Voyage <span class="text-red-500">*</span></label>
                    <select id="no_voyage" name="no_voyage" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        <option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-4">
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
    const kapalSelect = document.getElementById('nama_kapal');
    const voyageSelect = document.getElementById('no_voyage');
    const goToOBIndexBtn = document.getElementById('goToOBIndex');

    kapalSelect.addEventListener('change', function() {
        const kapalId = this.value;
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;

        if (!kapalId) {
            voyageSelect.innerHTML = '<option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>';
            voyageSelect.disabled = false;
            return;
        }

        // Nama kapal (value) diambil dari option value (sudah berupa nama)
        const kapalName = kapalId;
        
        console.log('Nama kapal dipilih:', kapalName);

        fetch(`{{ route('ob.get-voyage-by-kapal') }}?nama_kapal=${encodeURIComponent(kapalName)}`, {
            method: 'GET',
            headers: { 
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(r => {
            console.log('Response status:', r.status);
            return r.json();
        })
        .then(data => {
            console.log('Response data:', data);
            voyageSelect.innerHTML = '';
            if (data.success && data.voyages && data.voyages.length) {
                voyageSelect.innerHTML = '<option value="">--Pilih Voyage--</option>';
                data.voyages.forEach(v => {
                    voyageSelect.innerHTML += `<option value="${v}">${v}</option>`;
                });
                console.log('Voyage loaded:', data.voyages.length);
                // If there is a preselected voyage in query params, select it (helps page reload)
                if (preselectedVoyage) {
                    voyageSelect.value = preselectedVoyage;
                }
            } else {
                voyageSelect.innerHTML = '<option value="">Belum ada voyage untuk kapal ini</option>';
                console.log('No voyages found');
                if (data.success === false && data.message) {
                    console.warn('getVoyageByKapal: ', data.message);
                    // Optionally show a toast or alert
                    // alert(data.message);
                }
            }
            voyageSelect.disabled = false;
        })
        .catch(err => {
            console.error('Fetch error:', err);
            voyageSelect.innerHTML = '<option value="">Error loading voyage</option>';
            voyageSelect.disabled = false;
        });
    });

    // If the page was loaded with nama_kapal and no_voyage in query params, trigger change and preselect
    const preselectedKapal = '{{ request('nama_kapal') }}';
    const preselectedVoyage = '{{ request('no_voyage') }}';
    if (preselectedKapal) {
        kapalSelect.value = preselectedKapal;
        // dispatch change to load voyages
        kapalSelect.dispatchEvent(new Event('change'));

        // after voyages are loaded, try to select the voyage (with a short timeout)
        if (preselectedVoyage) {
            setTimeout(() => {
                try {
                    voyageSelect.value = preselectedVoyage;
                } catch (e) {
                    console.warn('Preselect voyage failed', e);
                }
            }, 700);
        }
    }

    // (Removed) "Ke Tagihan OB" button behavior - the button was removed from the UI.

    // Go to OB Index with filter
    goToOBIndexBtn.addEventListener('click', function() {
        const kapalId = kapalSelect.value;
        const voyage = voyageSelect.value;

        if (!kapalId || !voyage) {
            alert('Silakan pilih kapal dan voyage terlebih dahulu');
            return;
        }

        const kapalName = kapalId;
        
        // Redirect to OB Index with filter parameters
        const url = new URL('{{ route("ob.index") }}', window.location.origin);
        url.searchParams.set('nama_kapal', kapalName);
        url.searchParams.set('no_voyage', voyage);
        
        window.location.href = url.toString();
    });
});
</script>

@endsection
