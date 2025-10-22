@extends('layouts.app')

@section('title', 'Buat Prospek Kapal')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('prospek-kapal.index') }}" class="hover:text-gray-700">Prospek Kapal</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span>Buat Baru</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Buat Prospek Kapal</h1>
            <p class="text-gray-600 mt-2">Pilih voyage dan jadwalkan loading kontainer</p>
        </div>

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('prospek-kapal.store') }}">
                @csrf

                <div class="space-y-6">
                    {{-- Voyage Selection --}}
                    <div>
                        <label for="pergerakan_kapal_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Voyage <span class="text-red-500">*</span>
                        </label>
                        <select name="pergerakan_kapal_id" id="pergerakan_kapal_id"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required onchange="updateVoyageDetails()">
                            <option value="">-- Pilih Voyage --</option>
                            @foreach($availableVoyages as $voyage)
                                <option value="{{ $voyage->id }}" {{ old('pergerakan_kapal_id') == $voyage->id ? 'selected' : '' }}
                                        data-voyage="{{ $voyage->voyage }}"
                                        data-kapal="{{ $voyage->nama_kapal }}"
                                        data-kapten="{{ $voyage->kapten }}"
                                        data-asal="{{ $voyage->pelabuhan_asal }}"
                                        data-tujuan="{{ $voyage->pelabuhan_tujuan }}"
                                        data-sandar="{{ $voyage->tanggal_sandar ? $voyage->tanggal_sandar->format('Y-m-d\TH:i') : '' }}"
                                        data-berangkat="{{ $voyage->tanggal_berangkat ? $voyage->tanggal_berangkat->format('Y-m-d\TH:i') : '' }}">
                                    {{ $voyage->voyage }} - {{ $voyage->nama_kapal }} ({{ $voyage->pelabuhan_asal }} → {{ $voyage->pelabuhan_tujuan }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Pilih voyage dari pergerakan kapal yang belum memiliki prospek</p>
                    </div>

                    {{-- Voyage Details --}}
                    <div id="voyage-details" class="hidden bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Detail Voyage</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-500">Voyage:</span>
                                <span id="detail-voyage" class="ml-2 text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Kapal:</span>
                                <span id="detail-kapal" class="ml-2 text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Kapten:</span>
                                <span id="detail-kapten" class="ml-2 text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Rute:</span>
                                <span id="detail-rute" class="ml-2 text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Tanggal Sandar:</span>
                                <span id="detail-sandar" class="ml-2 text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500">Tanggal Berangkat:</span>
                                <span id="detail-berangkat" class="ml-2 text-gray-900"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Loading Date --}}
                    <div>
                        <label for="tanggal_loading" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Loading <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="tanggal_loading" id="tanggal_loading"
                               value="{{ old('tanggal_loading') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        <p class="text-sm text-gray-500 mt-1">Waktu mulai loading kontainer ke kapal</p>
                    </div>

                    {{-- Estimated Departure --}}
                    <div>
                        <label for="estimasi_departure" class="block text-sm font-medium text-gray-700 mb-2">
                            Estimasi Keberangkatan
                        </label>
                        <input type="datetime-local" name="estimasi_departure" id="estimasi_departure"
                               value="{{ old('estimasi_departure') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Estimasi waktu kapal berangkat (opsional)</p>
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Catatan tambahan untuk prospek kapal ini...">{{ old('keterangan') }}</textarea>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-end space-x-3 pt-6 border-t">
                        <a href="{{ route('prospek-kapal.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            Buat Prospek Kapal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateVoyageDetails() {
    const select = document.getElementById('pergerakan_kapal_id');
    const detailsDiv = document.getElementById('voyage-details');

    if (select.value) {
        const option = select.options[select.selectedIndex];

        document.getElementById('detail-voyage').textContent = option.dataset.voyage;
        document.getElementById('detail-kapal').textContent = option.dataset.kapal;
        document.getElementById('detail-kapten').textContent = option.dataset.kapten;
        document.getElementById('detail-rute').textContent = option.dataset.asal + ' → ' + option.dataset.tujuan;

        // Format dates
        if (option.dataset.sandar) {
            const sandarDate = new Date(option.dataset.sandar);
            document.getElementById('detail-sandar').textContent = sandarDate.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } else {
            document.getElementById('detail-sandar').textContent = '-';
        }

        if (option.dataset.berangkat) {
            const berangkatDate = new Date(option.dataset.berangkat);
            document.getElementById('detail-berangkat').textContent = berangkatDate.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Auto-fill estimasi departure if available
            document.getElementById('estimasi_departure').value = option.dataset.berangkat;
        } else {
            document.getElementById('detail-berangkat').textContent = '-';
        }

        // Auto-fill loading date based on sandar date
        if (option.dataset.sandar) {
            const sandarDate = new Date(option.dataset.sandar);
            sandarDate.setHours(sandarDate.getHours() + 2); // 2 hours after sandar
            document.getElementById('tanggal_loading').value = sandarDate.toISOString().slice(0, 16);
        }

        detailsDiv.classList.remove('hidden');
    } else {
        detailsDiv.classList.add('hidden');
        document.getElementById('tanggal_loading').value = '';
        document.getElementById('estimasi_departure').value = '';
    }
}

// Initialize on page load if there's a selected value
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('pergerakan_kapal_id').value) {
        updateVoyageDetails();
    }
});
</script>
@endsection
