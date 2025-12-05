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

            <div class="mt-6 flex gap-4">
                <button type="button" id="goToTagihanOB" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-md">
                    <i class="fas fa-file-invoice mr-2"></i>Ke Tagihan OB
                </button>
                <button type="button" id="goToPranotaOB" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                    <i class="fas fa-clipboard-list mr-2"></i>Ke Pranota OB
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
    const kapalSelect = document.getElementById('kapal_id');
    const voyageSelect = document.getElementById('no_voyage');
    const goToTagihanBtn = document.getElementById('goToTagihanOB');
    const goToPranotaBtn = document.getElementById('goToPranotaOB');

    kapalSelect.addEventListener('change', function() {
        const kapalId = this.value;
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;

        if (!kapalId) {
            voyageSelect.innerHTML = '<option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>';
            voyageSelect.disabled = false;
            return;
        }

        // Ambil nama kapal dari option yang dipilih
        const kapalName = kapalSelect.options[kapalSelect.selectedIndex].text.split(' (')[0];
        
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

    // Go to Tagihan OB with filter
    goToTagihanBtn.addEventListener('click', function() {
        const kapalId = kapalSelect.value;
        const voyage = voyageSelect.value;

        if (!kapalId || !voyage) {
            alert('Silakan pilih kapal dan voyage terlebih dahulu');
            return;
        }

        const kapalName = kapalSelect.options[kapalSelect.selectedIndex].text.split(' (')[0];
        
        // Redirect to Tagihan OB with filter parameters
        const url = new URL('{{ route("tagihan-ob.index") }}', window.location.origin);
        url.searchParams.set('nama_kapal', kapalName);
        url.searchParams.set('no_voyage', voyage);
        
        window.location.href = url.toString();
    });

    // Go to Pranota OB with filter
    goToPranotaBtn.addEventListener('click', function() {
        const kapalId = kapalSelect.value;
        const voyage = voyageSelect.value;

        if (!kapalId || !voyage) {
            alert('Silakan pilih kapal dan voyage terlebih dahulu');
            return;
        }

        const kapalName = kapalSelect.options[kapalSelect.selectedIndex].text.split(' (')[0];
        
        // Redirect to Pranota OB with filter parameters
        const url = new URL('{{ route("pranota-ob.index") }}', window.location.origin);
        url.searchParams.set('nama_kapal', kapalName);
        url.searchParams.set('no_voyage', voyage);
        
        window.location.href = url.toString();
    });
});
</script>

@endsection
