@extends('layouts.app')
@section('title', 'Denah Kontainer Gudang')
@section('page_title', 'Denah Kontainer Gudang')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-indigo-700 text-white rounded-xl p-6 mb-6">
        <h1 class="text-2xl font-bold">Denah Kontainer Gudang</h1>
        <p class="mt-2">Pilih gudang untuk melihat dan mengatur lokasi kontainer berdasarkan area, slot, baris, dan tingkat tumpukan.</p>
    </div>
    <div class="flex justify-between items-center gap-4 mb-6">
        <label class="flex-1 max-w-md">
            <span class="block text-sm font-medium mb-1">Cari gudang</span>
            <input id="warehouse-search" type="search" placeholder="Nama gudang atau lokasi" class="w-full border border-gray-300 rounded-lg px-4 py-2">
        </label>
        <a href="{{ route('master-gudang.index') }}" class="text-indigo-700 font-medium">Master Gudang &rarr;</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($gudangs as $gudang)
        <article data-warehouse-card data-search="{{ mb_strtolower($gudang->nama_gudang.' '.$gudang->lokasi) }}" class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <h2 class="text-lg font-bold text-gray-800">{{ $gudang->nama_gudang }}</h2>
                <span class="text-xs px-2 py-1 rounded bg-gray-100">{{ ucfirst($gudang->status) }}</span>
            </div>
            <p class="text-sm text-gray-500 mt-1">{{ $gudang->lokasi }}</p>
            <p class="text-sm mt-5 mb-5">{{ $gudang->denah_layout ? count($gudang->denah_layout['blocks']).' area sudah dikonfigurasi' : 'Layout belum dikonfigurasi' }}</p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('denah-gudang.show', $gudang) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm">Buka Denah</a>
                @can('master-gudang-edit')
                <a href="{{ route('master-gudang.layout', $gudang) }}" class="border border-gray-300 rounded-lg px-4 py-2 text-sm">Atur Layout</a>
                @endcan
            </div>
        </article>
        @empty
        <p class="text-gray-500">Belum ada gudang. Tambahkan gudang melalui Master Gudang terlebih dahulu.</p>
        @endforelse
    </div>
    <p id="warehouse-no-results" class="hidden mt-6 text-gray-500">Tidak ada gudang yang cocok dengan pencarian.</p>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('warehouse-search').addEventListener('input', function () {
    let count = 0;
    document.querySelectorAll('[data-warehouse-card]').forEach(card => {
        const match = card.dataset.search.includes(this.value.trim().toLowerCase());
        card.hidden = !match;
        if (match) count++;
    });
    document.getElementById('warehouse-no-results').classList.toggle('hidden', count !== 0);
});
</script>
@endpush
