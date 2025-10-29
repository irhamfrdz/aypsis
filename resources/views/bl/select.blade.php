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
                        @foreach($masterKapals as $kapal)
                            <option value="{{ $kapal->id }}">{{ $kapal->nama_kapal }} {{ $kapal->nickname ? '('.$kapal->nickname.')' : '' }}</option>
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

            <div class="mt-6">
                <button type="button" id="goToIndexFiltered" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                    <i class="fas fa-list mr-2"></i>Ke Halaman Index BL
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kapalSelect = document.getElementById('kapal_id');
    const voyageSelect = document.getElementById('no_voyage');
    const goToIndexFilteredBtn = document.getElementById('goToIndexFiltered');

    kapalSelect.addEventListener('change', function() {
        const kapalId = this.value;
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;

        if (!kapalId) {
            voyageSelect.innerHTML = '<option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>';
            voyageSelect.disabled = false;
            return;
        }

        fetch(`{{ route('prospek.get-voyage-by-kapal') }}?kapal_id=${kapalId}`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            voyageSelect.innerHTML = '';
            if (data.success && data.voyages && data.voyages.length) {
                voyageSelect.innerHTML = '<option value="">--Pilih Voyage--</option>';
                data.voyages.forEach(v => {
                    voyageSelect.innerHTML += `<option value="${v}">${v}</option>`;
                });
            } else {
                voyageSelect.innerHTML = '<option value="">Belum ada voyage untuk kapal ini</option>';
            }
            voyageSelect.disabled = false;
        })
        .catch(err => {
            voyageSelect.innerHTML = '<option value="">Error loading voyage</option>';
            voyageSelect.disabled = false;
            console.error(err);
        });
    });

    // Go to index with filter
    goToIndexFilteredBtn.addEventListener('click', function() {
        const kapalId = kapalSelect.value;
        const voyage = voyageSelect.value;

        if (!kapalId || !voyage) {
            alert('Silakan pilih kapal dan voyage terlebih dahulu');
            return;
        }

        const kapalName = kapalSelect.options[kapalSelect.selectedIndex].text.split(' (')[0];
        
        // Redirect to BL index with filter parameters
        const url = new URL('{{ route("bl.index") }}', window.location.origin);
        url.searchParams.set('nama_kapal', kapalName);
        url.searchParams.set('no_voyage', voyage);
        
        window.location.href = url.toString();
    });
});
</script>

@endsection
