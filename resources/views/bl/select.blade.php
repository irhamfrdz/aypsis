@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-file-contract mr-3 text-green-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Buat BL (Step 1)</h1>
                    <p class="text-gray-600">Pilih kapal dan nomor voyage terlebih dahulu</p>
                </div>
            </div>
            <div>
                <a href="{{ route('bl.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    Kembali ke Daftar BL
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div id="blSelectForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-2">Kapal <span class="text-red-500">*</span></label>
                    <select id="kapal_id" name="kapal_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        <option value="">--Pilih Kapal--</option>
                        @foreach($masterKapals->unique('nama_kapal')->sortBy('nama_kapal') as $kapal)
                            <option value="{{ $kapal->nama_kapal }}">{{ $kapal->nama_kapal }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="voyageContainer">
                    <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">No Voyage <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select id="no_voyage" name="no_voyage" class="flex-1 px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>
                        </select>
                        <button type="button" id="toggleManualVoyage" class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 text-sm">
                            <i class="fas fa-edit"></i> Baru
                        </button>
                    </div>
                    <input type="text" id="manual_voyage" class="hidden mt-2 w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Input nomor voyage baru...">
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                <button type="button" id="goToIndexFiltered" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200">
                    <i class="fas fa-list mr-2"></i>Ke Halaman Index BL
                </button>
                <button type="button" id="goToCreateBl" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md transition duration-200">
                    <i class="fas fa-plus mr-2"></i>Buat BL Manual
                </button>
                <button type="button" id="exportExcelBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-md transition duration-200">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kapalSelect = document.getElementById('kapal_id');
    const voyageSelect = document.getElementById('no_voyage');
    const manualVoyageInput = document.getElementById('manual_voyage');
    const toggleManualVoyageBtn = document.getElementById('toggleManualVoyage');
    const goToIndexFilteredBtn = document.getElementById('goToIndexFiltered');

    // Toggle manual voyage input
    toggleManualVoyageBtn.addEventListener('click', function() {
        if (manualVoyageInput.classList.contains('hidden')) {
            manualVoyageInput.classList.remove('hidden');
            voyageSelect.classList.add('hidden');
            voyageSelect.removeAttribute('required');
            manualVoyageInput.setAttribute('required', 'required');
            this.innerHTML = '<i class="fas fa-list"></i> List';
        } else {
            manualVoyageInput.classList.add('hidden');
            voyageSelect.classList.remove('hidden');
            manualVoyageInput.removeAttribute('required');
            voyageSelect.setAttribute('required', 'required');
            this.innerHTML = '<i class="fas fa-edit"></i> Baru';
        }
    });

    kapalSelect.addEventListener('change', function() {
        const namaKapal = this.value;
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;

        if (!namaKapal) {
            voyageSelect.innerHTML = '<option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>';
            voyageSelect.disabled = false;
            return;
        }

        console.log('Nama kapal dipilih:', namaKapal);

        fetch(`{{ route('bl.get-voyage-by-kapal', [], false) }}?nama_kapal=${encodeURIComponent(namaKapal)}`, {
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
            } else {
                voyageSelect.innerHTML = '<option value="">Belum ada voyage untuk kapal ini</option>';
                console.log('No voyages found');
            }
            voyageSelect.disabled = false;
        })
        .catch(err => {
            console.error('Fetch error:', err);
            voyageSelect.innerHTML = '<option value="">Error loading voyage</option>';
            voyageSelect.disabled = false;
        });
    });

    // Go to index with filter
    goToIndexFilteredBtn.addEventListener('click', function() {
        const namaKapal = kapalSelect.value;
        const voyage = manualVoyageInput.classList.contains('hidden') ? voyageSelect.value : manualVoyageInput.value;

        if (!namaKapal || !voyage) {
            alert('Silakan pilih kapal dan voyage terlebih dahulu');
            return;
        }
        
        // Redirect to BL index with filter parameters
        const url = new URL('{{ route("bl.index", [], false) }}', window.location.origin);
        url.searchParams.set('nama_kapal', namaKapal);
        url.searchParams.set('no_voyage', voyage);
        
        window.location.href = url.toString();
    });

    // Go to create BL manual
    const goToCreateBlBtn = document.getElementById('goToCreateBl');
    goToCreateBlBtn.addEventListener('click', function() {
        const namaKapal = kapalSelect.value;
        const voyage = manualVoyageInput.classList.contains('hidden') ? voyageSelect.value : manualVoyageInput.value;

        if (!namaKapal || !voyage) {
            alert('Silakan pilih kapal dan voyage terlebih dahulu');
            return;
        }
        
        // Redirect to BL create page with parameters
        const url = new URL('{{ route("bl.create", [], false) }}', window.location.origin);
        url.searchParams.set('nama_kapal', namaKapal);
        url.searchParams.set('no_voyage', voyage);
        
        window.location.href = url.toString();
    });

    // Export Excel with filter
    const exportExcelBtn = document.getElementById('exportExcelBtn');
    exportExcelBtn.addEventListener('click', function() {
        const namaKapal = kapalSelect.value;
        const voyage = manualVoyageInput.classList.contains('hidden') ? voyageSelect.value : manualVoyageInput.value;

        if (!namaKapal || !voyage) {
            alert('Silakan pilih kapal dan voyage terlebih dahulu');
            return;
        }
        
        // Redirect to BL export with filter parameters
        const url = new URL('{{ route("bl.export", [], false) }}', window.location.origin);
        url.searchParams.set('nama_kapal', namaKapal);
        url.searchParams.set('no_voyage', voyage);
        
        window.location.href = url.toString();
    });
});
</script>

@endsection
