@extends('layouts.app')

@section('title', 'Rekap Pemakaian Barang')

@section('content')
<div class="p-6">
    <div class="mb-6"><h2 class="text-2xl font-bold text-gray-800"><i class="fas fa-boxes mr-2 text-indigo-600" aria-hidden="true"></i>Rekap Pemakaian Barang</h2><p class="mt-1 text-sm text-gray-500">Pilih barang dan periode, lalu pilih unit kendaraan untuk melihat pemakaiannya.</p></div>
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
        <h3 class="font-semibold text-gray-900">1. Pilih barang</h3>
        @if($errors->any())<div role="alert" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
        <form method="GET" action="{{ route('rekap-pemakaian-barang.index') }}" class="mt-4 space-y-5">
            <div>
                <label for="barang-search" class="mb-1 block text-sm font-semibold text-gray-700">Barang <span class="text-red-500">*</span></label>
                <input id="barang-search" type="search" placeholder="Cari barang..." class="w-full rounded-t-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <div id="barang-options" class="max-h-60 space-y-2 overflow-y-auto rounded-b-lg border border-t-0 border-gray-300 bg-white p-3">
                    @forelse($pilihanBarang as $barang)
                        <label class="barang-option flex cursor-pointer items-center gap-2 text-sm text-gray-700"><input type="checkbox" name="barang[]" value="{{ $barang }}" @checked(in_array($barang, $selectedBarang, true)) class="rounded border-gray-300 text-indigo-600"><span>{{ $barang }}</span></label>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada barang tersedia.</p>
                    @endforelse
                </div>
                <div class="mt-1 flex justify-between text-xs text-gray-500"><span>Pilih satu atau lebih barang</span><span><button type="button" id="barang-select-all" class="text-indigo-600 hover:underline">Pilih Semua</button> · <button type="button" id="barang-clear" class="text-red-600 hover:underline">Hapus Semua Pilihan</button></span></div>
            </div>
            <h3 class="font-semibold text-gray-900">2. Pilih rentang tanggal</h3>
            <div class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:w-52"><label for="start_date" class="mb-2 block text-sm font-medium text-gray-700">Dari Tanggal <span class="text-red-500">*</span></label><input id="start_date" type="date" name="start_date" value="{{ old('start_date', $startDate) }}" required class="h-11 w-full rounded-xl border border-gray-300 px-3 text-sm"></div>
            <div class="w-full sm:w-52"><label for="end_date" class="mb-2 block text-sm font-medium text-gray-700">Sampai Tanggal <span class="text-red-500">*</span></label><input id="end_date" type="date" name="end_date" value="{{ old('end_date', $endDate) }}" required class="h-11 w-full rounded-xl border border-gray-300 px-3 text-sm"></div>
            <button type="submit" class="h-11 rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700">Tampilkan Unit</button>
            <a href="{{ route('rekap-pemakaian-barang.index') }}" class="inline-flex h-11 items-center rounded-xl border border-gray-300 px-4 text-sm font-semibold text-gray-600 hover:bg-gray-50">Reset</a>
            </div>
        </form>
    </div>
    @if($startDate && $endDate)
        @php($units = $results->filter(fn ($row) => $row->unit && $row->unit !== '-')->groupBy('unit')->sortKeys())
        <section class="rounded-2xl border border-gray-200 bg-white shadow-sm" aria-labelledby="unit-title">
            <div class="border-b border-gray-100 p-5 sm:p-6"><h3 id="unit-title" class="font-semibold text-gray-900">3. Pilih unit kendaraan</h3><p class="mt-1 text-sm text-gray-500">Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p></div>
            <div class="p-5 sm:p-6">
                @if($units->isNotEmpty())
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4" id="unit-list">
                    @foreach($units as $name => $rows)
                    <button type="button" data-unit="{{ $name }}" aria-pressed="false" class="unit-card rounded-xl border border-gray-200 bg-white p-4 text-left transition hover:border-indigo-300 hover:bg-indigo-50"><span class="block font-bold text-gray-900">{{ $name }}</span><span class="mt-1 block text-xs text-gray-500">{{ $rows->count() }} catatan pemakaian</span></button>
                    @endforeach
                </div>
                <div id="unit-results" class="mt-6 hidden">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2"><h4 class="font-semibold text-gray-900">Pemakaian unit: <span id="unit-name" class="text-indigo-700"></span></h4><span id="unit-count" class="text-sm text-gray-500"></span></div>
                    <div class="overflow-x-auto rounded-xl border border-gray-200"><table class="min-w-full text-left text-sm"><thead class="bg-gray-50 text-xs uppercase text-gray-600"><tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Barang</th><th class="px-4 py-3">Penerima</th><th class="px-4 py-3 text-right">Qty</th><th class="px-4 py-3">Keterangan</th></tr></thead><tbody class="divide-y divide-gray-100">
                        @foreach($results as $row)
                        <tr class="result-row hidden" data-unit="{{ $row->unit }}"><td class="whitespace-nowrap px-4 py-3">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td><td class="px-4 py-3"><span class="font-medium text-gray-900">{{ $row->nama_barang }}</span><span class="mt-1 block text-xs text-gray-500">{{ $row->sumber }}</span></td><td class="px-4 py-3">{{ $row->penerima }}</td><td class="whitespace-nowrap px-4 py-3 text-right">{{ $row->qty }}</td><td class="px-4 py-3">{{ $row->keterangan ?: '-' }}</td></tr>
                        @endforeach
                    </tbody></table></div>
                </div>
                @else
                <p class="rounded-xl border border-dashed border-gray-200 p-8 text-center text-sm text-gray-500">Tidak ada pemakaian barang untuk unit kendaraan pada periode ini.</p>
                @endif
            </div>
        </section>
    @else
        <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">Pilih barang dan rentang tanggal untuk melihat unit kendaraan.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const barangOptions = [...document.querySelectorAll('.barang-option')];
    document.getElementById('barang-search')?.addEventListener('input', event => {
        const search = event.target.value.trim().toLocaleLowerCase('id-ID');
        barangOptions.forEach(option => option.classList.toggle('hidden', !option.textContent.toLocaleLowerCase('id-ID').includes(search)));
    });
    document.getElementById('barang-select-all')?.addEventListener('click', () => barangOptions.forEach(option => { if (!option.classList.contains('hidden')) option.querySelector('input').checked = true; }));
    document.getElementById('barang-clear')?.addEventListener('click', () => barangOptions.forEach(option => { option.querySelector('input').checked = false; }));
    document.querySelector('form[action="{{ route('rekap-pemakaian-barang.index') }}"]')?.addEventListener('submit', event => {
        if (!barangOptions.some(option => option.querySelector('input').checked)) {
            event.preventDefault();
            alert('Pilih minimal satu barang.');
        }
    });
    const cards = document.querySelectorAll('.unit-card');
    const rows = document.querySelectorAll('.result-row');
    cards.forEach(card => card.addEventListener('click', () => {
        cards.forEach(item => { item.setAttribute('aria-pressed', 'false'); item.classList.remove('border-indigo-500', 'bg-indigo-50', 'ring-2', 'ring-indigo-200'); });
        card.setAttribute('aria-pressed', 'true');
        card.classList.add('border-indigo-500', 'bg-indigo-50', 'ring-2', 'ring-indigo-200');
        let count = 0;
        rows.forEach(row => { const matches = row.dataset.unit === card.dataset.unit; row.classList.toggle('hidden', !matches); if (matches) count++; });
        document.getElementById('unit-name').textContent = card.dataset.unit;
        document.getElementById('unit-count').textContent = count + ' catatan';
        document.getElementById('unit-results').classList.remove('hidden');
    }));
});
</script>
@endpush
