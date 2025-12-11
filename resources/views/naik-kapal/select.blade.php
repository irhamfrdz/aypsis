@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-ship mr-3 text-purple-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Naik Kapal</h1>
                    <p class="text-gray-600">Pilih kapal dan nomor voyage untuk melihat data naik kapal</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('naik-kapal.download.template') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-download mr-2"></i>Download Template
                </a>
                <a href="{{ url()->previous() }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Select Form --}}
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <i class="fas fa-ship text-6xl text-purple-600 mb-4"></i>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Pilih Kapal dan Voyage</h2>
                <p class="text-gray-600">Silakan pilih kapal dan nomor voyage untuk melihat data kontainer yang naik kapal</p>
            </div>

            <form method="GET" action="{{ route('naik-kapal.index') }}" id="naikKapalSelectForm">
                <div class="space-y-6">
                    <div>
                        <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Kapal <span class="text-red-500">*</span>
                        </label>
                        <select id="kapal_id" name="kapal_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                            <option value="">--Pilih Kapal--</option>
                            @php
                                $masterKapals = \App\Models\MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();
                            @endphp
                            @foreach($masterKapals as $kapal)
                                <option value="{{ $kapal->id }}">
                                    {{ $kapal->nama_kapal }} {{ $kapal->nickname ? '('.$kapal->nickname.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">
                            No Voyage <span class="text-red-500">*</span>
                        </label>
                        <select id="no_voyage" name="no_voyage" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required disabled>
                            <option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>
                        </select>
                    </div>

                    <div>
                        <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-2">
                            Status BL (Opsional)
                        </label>
                        <select id="status_filter" name="status_filter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">--Semua Status--</option>
                            <option value="sudah_bl">Sudah BL</option>
                            <option value="belum_bl">Belum BL</option>
                        </select>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition duration-200 font-medium text-lg">
                            <i class="fas fa-search mr-2"></i>
                            Lihat Data Naik Kapal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kapalSelect = document.getElementById('kapal_id');
    const voyageSelect = document.getElementById('no_voyage');

    kapalSelect.addEventListener('change', function() {
        const kapalId = this.value;
        loadVoyages(kapalId);
    });

    function loadVoyages(kapalId) {
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;

        if (!kapalId) {
            voyageSelect.innerHTML = '<option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>';
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
                voyageSelect.disabled = false;
            } else {
                voyageSelect.innerHTML = '<option value="">Belum ada voyage untuk kapal ini</option>';
            }
        })
        .catch(err => {
            voyageSelect.innerHTML = '<option value="">Error loading voyage</option>';
            console.error(err);
        });
    }
});
</script>
@endsection
