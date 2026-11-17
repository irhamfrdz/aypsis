@extends('layouts.app')

@section('title', 'Pilih Kapal & Voyage - Surat Jalan Bongkaran Tanjung Pinang')

@section('content')
<div class="flex-1 p-4 md:p-6">
    <div class="mb-4 flex flex-col justify-between md:mb-6 md:flex-row md:items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900 md:text-2xl">Pilih Kapal & Voyage</h1>
            <nav class="mt-1 flex flex-wrap items-center text-xs text-gray-600 md:text-sm">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-1 md:mx-2">/</span>
                <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.index') }}" class="hover:text-blue-600">Surat Jalan Bongkaran Tanjung Pinang</a>
                <span class="mx-1 md:mx-2">/</span>
                <span class="text-gray-500">Pilih Kapal & Voyage</span>
            </nav>
        </div>
    </div>

    <div class="mx-auto max-w-2xl rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-4 py-4 md:px-6">
            <h2 class="text-base font-semibold text-gray-900 md:text-lg">Pilih Kapal dan Voyage</h2>
            <p class="mt-1 text-xs text-gray-600 md:text-sm">Silakan pilih kapal dan voyage untuk melihat data manifest dan surat jalan.</p>
        </div>

        <div class="p-4 md:p-6">
            <form method="GET" action="{{ route('surat-jalan-bongkaran-tanjung-pinang.list') }}" id="selectShipForm">
                <div class="space-y-4 md:space-y-6">
                    <div>
                        <label for="nama_kapal" class="mb-1 block text-sm font-medium text-gray-700 md:mb-2">Kapal <span class="text-red-500">*</span></label>
                        <select name="nama_kapal" id="nama_kapal" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 md:px-4 md:py-3" required>
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($kapals as $kapal)
                                <option value="{{ $kapal }}" @selected(request('nama_kapal') == $kapal)>{{ $kapal }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="no_voyage" class="mb-1 block text-sm font-medium text-gray-700 md:mb-2">Voyage <span class="text-red-500">*</span></label>
                        <select name="no_voyage" id="no_voyage" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 md:px-4 md:py-3" required disabled>
                            <option value="">-- Pilih Voyage --</option>
                            @foreach($voyages as $voyage)
                                <option value="{{ $voyage }}" @selected(request('no_voyage') == $voyage)>{{ $voyage }}</option>
                            @endforeach
                        </select>
                        <p id="voyageHint" class="mt-1 text-xs text-gray-500 md:text-sm">Silakan pilih kapal terlebih dahulu</p>
                    </div>

                    <div class="flex flex-col gap-3 pt-4 sm:flex-row sm:items-center sm:justify-between sm:gap-0">
                        <a href="{{ route('dashboard') }}" class="flex w-full items-center justify-center rounded-lg bg-green-600 px-6 py-2.5 text-sm font-medium text-white transition-colors duration-200 hover:bg-green-700 sm:w-auto">
                            <svg class="mr-2 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            Dashboard
                        </a>
                        <div class="flex w-full flex-row items-center gap-3 sm:w-auto">
                            <a href="{{ route('dashboard') }}" class="flex-1 rounded-lg bg-gray-500 px-6 py-2.5 text-center text-sm font-medium text-white transition-colors duration-200 hover:bg-gray-600 sm:flex-none">Batal</a>
                            <button type="submit" id="submitBtn" disabled class="flex flex-1 items-center justify-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white transition-colors duration-200 hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50 sm:flex-none">
                                <svg class="mr-2 hidden h-4 w-4 sm:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                Lanjutkan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const kapalSelect = document.getElementById('nama_kapal');
    const voyageSelect = document.getElementById('no_voyage');
    const submitBtn = document.getElementById('submitBtn');
    const voyageHint = document.getElementById('voyageHint');
    const initialVoyage = @json(request('no_voyage'));

    function updateSubmitButton() {
        submitBtn.disabled = !(kapalSelect.value && voyageSelect.value);
    }

    async function loadVoyages(kapalName, selectedVoyage = '') {
        if (!kapalName) {
            voyageSelect.disabled = true;
            voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option>';
            voyageHint.textContent = 'Silakan pilih kapal terlebih dahulu';
            updateSubmitButton();
            return;
        }

        voyageSelect.disabled = true;
        voyageSelect.innerHTML = '<option value="">Memuat voyage...</option>';
        voyageHint.textContent = 'Memuat daftar voyage...';
        updateSubmitButton();

        try {
            const url = new URL(@json(route('surat-jalan-bongkaran-tanjung-pinang.get-voyages', [], false)), window.location.origin);
            url.searchParams.set('nama_kapal', kapalName);
            const response = await fetch(url);
            if (!response.ok) throw new Error('Gagal mengambil data voyage');
            const data = await response.json();
            voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option>';

            if (data.success && data.voyages && data.voyages.length) {
                data.voyages.forEach(function (voyage) {
                    const option = document.createElement('option');
                    option.value = voyage;
                    option.textContent = voyage;
                    option.selected = voyage === selectedVoyage;
                    voyageSelect.appendChild(option);
                });
                voyageSelect.disabled = false;
                voyageHint.textContent = 'Pilih voyage untuk menampilkan data.';
            } else {
                voyageSelect.innerHTML = '<option value="">Tidak ada voyage ditemukan</option>';
                voyageHint.textContent = 'Tidak ada voyage untuk kapal ini.';
            }
        } catch (error) {
            voyageSelect.innerHTML = '<option value="">Error memuat voyage</option>';
            voyageHint.textContent = 'Voyage gagal dimuat. Silakan pilih ulang kapal.';
        }
        updateSubmitButton();
    }

    kapalSelect.addEventListener('change', function () { loadVoyages(this.value); });
    voyageSelect.addEventListener('change', updateSubmitButton);
    if (kapalSelect.value) loadVoyages(kapalSelect.value, initialVoyage);
    else updateSubmitButton();
});
</script>
@endpush
