@extends('layouts.app')

@section('title', 'Pilih Kapal & Voyage - Surat Jalan Bongkaran Tanjung Pinang')

@push('styles')
<style>
    .tp-select { font: 12px/1.5 Arial,sans-serif; color:#344256; }
    .tp-select h1,.tp-select h2 { color:#1E3A5F; }
    .tp-select .tp-card { border:1px solid #e1e6ed;border-radius:8px;background:#fff;box-shadow:0 2px 6px rgb(30 58 95 / 4%); }
    .tp-select select { min-height:38px;border-color:#d5dde7;border-radius:5px;font:12px Arial,sans-serif; }
    .tp-select button,.tp-select a { border-radius:5px;font:12px Arial,sans-serif; }
</style>
@endpush

@section('content')
<div class="tp-select flex-1 bg-[#F5F7FA] p-4 md:p-6">
    <div class="mx-auto max-w-6xl">
        <div class="mb-5">
            <h1 class="text-xl font-semibold">Surat Jalan Bongkaran Tanjung Pinang</h1>
            <nav class="mt-1 flex items-center gap-2 text-xs text-slate-500"><a href="{{ route('dashboard') }}" class="hover:text-blue-700">Dashboard</a><span>/</span><span>Pilih Kapal & Voyage</span></nav>
        </div>

        <div class="tp-card mx-auto max-w-2xl">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold">Pilih Kapal dan Voyage</h2>
                <p class="mt-1 text-xs text-slate-500">Pilih kapal dan voyage untuk menampilkan surat jalan bongkaran Tanjung Pinang.</p>
            </div>
            <div class="p-5">
                <form method="GET" action="{{ route('surat-jalan-bongkaran-tanjung-pinang.list') }}" id="tpSelectShipForm" class="space-y-5">
                    <div>
                        <label for="nama_kapal" class="mb-1.5 block text-xs font-semibold text-slate-600">Kapal <span class="text-red-600">*</span></label>
                        <select name="nama_kapal" id="nama_kapal" required class="w-full focus:border-blue-700 focus:ring-blue-700">
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($kapals as $kapal)<option value="{{ $kapal }}" @selected(request('nama_kapal') === $kapal)>{{ $kapal }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label for="no_voyage" class="mb-1.5 block text-xs font-semibold text-slate-600">Voyage <span class="text-red-600">*</span></label>
                        <select name="no_voyage" id="no_voyage" required disabled class="w-full focus:border-blue-700 focus:ring-blue-700">
                            <option value="">-- Pilih Voyage --</option>
                            @foreach($voyages as $voyage)<option value="{{ $voyage }}" @selected(request('no_voyage') === $voyage)>{{ $voyage }}</option>@endforeach
                        </select>
                        <p id="tpVoyageHint" class="mt-1 text-xs text-slate-500">Pilih kapal terlebih dahulu.</p>
                    </div>
                    <div class="flex flex-col-reverse justify-between gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-center text-slate-600 hover:bg-slate-50">Batal</a>
                        <button id="tpSubmitBtn" type="submit" disabled class="bg-[#1E3A5F] px-5 py-2.5 font-semibold text-white hover:bg-[#2b4c75] disabled:cursor-not-allowed disabled:opacity-50">Lanjutkan <i class="fas fa-arrow-right ml-1"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const kapal = document.getElementById('nama_kapal');
    const voyage = document.getElementById('no_voyage');
    const submit = document.getElementById('tpSubmitBtn');
    const hint = document.getElementById('tpVoyageHint');
    const initialVoyage = @json(request('no_voyage'));

    const updateSubmit = () => { submit.disabled = !(kapal.value && voyage.value); };
    const loadVoyages = async (selected = '') => {
        voyage.disabled = true;
        voyage.innerHTML = '<option value="">Memuat voyage...</option>';
        updateSubmit();
        if (!kapal.value) {
            voyage.innerHTML = '<option value="">-- Pilih Voyage --</option>';
            hint.textContent = 'Pilih kapal terlebih dahulu.';
            return;
        }
        try {
            const url = new URL(@json(route('surat-jalan-bongkaran-tanjung-pinang.get-voyages')), window.location.origin);
            url.searchParams.set('nama_kapal', kapal.value);
            const response = await fetch(url);
            const data = await response.json();
            voyage.innerHTML = '<option value="">-- Pilih Voyage --</option>';
            (data.voyages || []).forEach(value => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                option.selected = value === selected;
                voyage.appendChild(option);
            });
            voyage.disabled = !data.voyages?.length;
            hint.textContent = data.voyages?.length ? 'Pilih voyage untuk menampilkan datanya.' : 'Tidak ada voyage untuk kapal ini.';
        } catch (error) {
            voyage.innerHTML = '<option value="">Gagal memuat voyage</option>';
            hint.textContent = 'Voyage gagal dimuat. Silakan pilih ulang kapal.';
        }
        updateSubmit();
    };

    kapal.addEventListener('change', () => loadVoyages());
    voyage.addEventListener('change', updateSubmit);
    if (kapal.value) loadVoyages(initialVoyage); else updateSubmit();
});
</script>
@endpush
