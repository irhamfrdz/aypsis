@extends('layouts.app')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 styling to match Tailwind */
    .select2-container--default .select2-selection--single {
        height: 42px;
        border-color: #d1d5db;
        border-radius: 0.375rem;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        color: #374151;
        font-size: 0.875rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px #3b82f6;
    }
</style>

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

<!-- Select2 JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#kapal_id').select2({
        placeholder: '--Pilih Kapal--',
        width: '100%'
    });
    
    $('#no_voyage').select2({
        placeholder: '-PILIH KAPAL TERLEBIH DAHULU-',
        width: '100%'
    });

    const manualVoyageInput = document.getElementById('manual_voyage');
    const toggleManualVoyageBtn = document.getElementById('toggleManualVoyage');
    const goToIndexFilteredBtn = document.getElementById('goToIndexFiltered');

    // Toggle manual voyage input
    toggleManualVoyageBtn.addEventListener('click', function() {
        if (manualVoyageInput.classList.contains('hidden')) {
            manualVoyageInput.classList.remove('hidden');
            $('#no_voyage').next('.select2-container').addClass('hidden');
            $('#no_voyage').removeAttr('required');
            manualVoyageInput.setAttribute('required', 'required');
            this.innerHTML = '<i class="fas fa-list"></i> List';
        } else {
            manualVoyageInput.classList.add('hidden');
            $('#no_voyage').next('.select2-container').removeClass('hidden');
            manualVoyageInput.removeAttribute('required');
            $('#no_voyage').attr('required', 'required');
            this.innerHTML = '<i class="fas fa-edit"></i> Baru';
        }
    });

    $('#kapal_id').on('change', function() {
        const namaKapal = this.value;
        const voyageSelect = document.getElementById('no_voyage');
        
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;
        $('#no_voyage').trigger('change.select2');

        if (!namaKapal) {
            voyageSelect.innerHTML = '<option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>';
            voyageSelect.disabled = false;
            $('#no_voyage').trigger('change.select2');
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
            $('#no_voyage').trigger('change.select2');
        })
        .catch(err => {
            console.error('Fetch error:', err);
            voyageSelect.innerHTML = '<option value="">Error loading voyage</option>';
            voyageSelect.disabled = false;
            $('#no_voyage').trigger('change.select2');
        });
    });

    // Go to index with filter
    goToIndexFilteredBtn.addEventListener('click', function() {
        const namaKapal = $('#kapal_id').val();
        const voyage = !manualVoyageInput.classList.contains('hidden') ? manualVoyageInput.value : $('#no_voyage').val();

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
        const namaKapal = $('#kapal_id').val();
        const voyage = !manualVoyageInput.classList.contains('hidden') ? manualVoyageInput.value : $('#no_voyage').val();

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
        const namaKapal = $('#kapal_id').val();
        const voyage = !manualVoyageInput.classList.contains('hidden') ? manualVoyageInput.value : $('#no_voyage').val();

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
