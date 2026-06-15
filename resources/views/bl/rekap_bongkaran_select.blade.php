@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center mr-4">
                    <i class="fas fa-ship text-indigo-600 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Rekap Bongkar/Muat Barang</h1>
                    <p class="text-gray-500 text-sm">Pilih kapal dan voyage untuk melihat rekapan bongkar/muat barang</p>
                </div>
            </div>
            <div>
                <a href="{{ route('bl.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 transition duration-150 ease-in-out">
                    <i class="fas fa-list mr-2 text-gray-500"></i> Daftar BL
                </a>
            </div>
        </div>
    </div>

    <!-- Selection Form Card -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 max-w-3xl mx-auto">
        <form action="{{ route('bl.rekap-bongkaran') }}" method="GET" id="rekapForm">
            <div class="space-y-6">
                <!-- Ship Selection -->
                <div>
                    <label for="nama_kapal" class="block text-sm font-semibold text-gray-700 mb-2">Nama Kapal <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-anchor"></i>
                        </div>
                        <select id="nama_kapal" name="nama_kapal" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-800 bg-gray-50/50 hover:bg-gray-50/100 transition duration-150" required>
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($masterKapals->unique('nama_kapal')->sortBy('nama_kapal') as $kapal)
                                <option value="{{ $kapal->nama_kapal }}">{{ $kapal->nama_kapal }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Voyage Selection -->
                <div>
                    <label for="no_voyage" class="block text-sm font-semibold text-gray-700 mb-2">Voyage <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-compass"></i>
                        </div>
                        <select id="no_voyage" name="no_voyage" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-800 bg-gray-50/50 hover:bg-gray-50/100 transition duration-150" required disabled>
                            <option value="">- Pilih Kapal Terlebih Dahulu -</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-xl shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    <i class="fas fa-file-invoice mr-2"></i> Lihat Rekap Bongkar/Muat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kapalSelect = document.getElementById('nama_kapal');
    const voyageSelect = document.getElementById('no_voyage');

    kapalSelect.addEventListener('change', function() {
        const namaKapal = this.value;
        voyageSelect.innerHTML = '<option value="">Loading...</option>';
        voyageSelect.disabled = true;

        if (!namaKapal) {
            voyageSelect.innerHTML = '<option value="">- Pilih Kapal Terlebih Dahulu -</option>';
            return;
        }

        fetch(`{{ route('bl.get-voyage-by-kapal', [], false) }}?nama_kapal=${encodeURIComponent(namaKapal)}`, {
            method: 'GET',
            headers: { 
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            voyageSelect.innerHTML = '';
            if (data.success && data.voyages && data.voyages.length) {
                voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option>';
                data.voyages.forEach(v => {
                    voyageSelect.innerHTML += `<option value="${v}">${v}</option>`;
                });
                voyageSelect.disabled = false;
            } else {
                voyageSelect.innerHTML = '<option value="">Belum ada voyage untuk kapal ini</option>';
                voyageSelect.disabled = true;
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            voyageSelect.innerHTML = '<option value="">Error loading voyage</option>';
            voyageSelect.disabled = true;
        });
    });
});
</script>
@endsection
