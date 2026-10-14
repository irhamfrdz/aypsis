@extends('layouts.app')

@section('title', 'Rekap')
@section('page_title', 'Rekap')

@section('content')
@php
    $user = Auth::user();
    $groups = collect([
        ['category' => 'Kontainer', 'label' => 'Rekap Perbaikan Kontainer', 'route' => 'rekap-perbaikan-kontainer.index', 'permission' => null],
        ['category' => 'Absensi', 'label' => 'Rekap Absensi', 'route' => 'absensi.rekap', 'permission' => 'absensi-rekap'],
        ['category' => 'Aktiva', 'label' => 'Rekap Biaya Kapal', 'route' => 'rekap-biaya-kapal.index', 'permission' => 'biaya-kapal-view'],
        ['category' => 'Aktiva', 'label' => 'Rekap Biaya Asset', 'route' => 'rekap-biaya-asset.index', 'permission' => 'rekap-biaya-asset-view'],
        ['category' => 'Aktiva', 'label' => 'Rekap Pemakaian Barang', 'route' => 'rekap-pemakaian-barang.index', 'permission' => 'rekap-pemakaian-barang-view'],
        ['category' => 'Bongkar/Muat', 'label' => 'Rekap Bongkar/Muat', 'route' => 'bl.rekap-bongkaran.select', 'permission' => 'bl-view'],
        ['category' => 'Bongkar/Muat', 'label' => 'Rekap Bongkar/Muat Kontainer', 'route' => 'bl.rekap-bongkaran-kontainer.select', 'permission' => 'bl-view'],
        ['category' => 'Bongkar/Muat', 'label' => 'Rekap Bongkar/Muat Perincian', 'route' => 'bl.rekap-bongkaran-perincian.select', 'permission' => 'bl-view'],
    ])->filter(fn ($item) => !$item['permission'] || $user->can($item['permission']))->groupBy('category');
@endphp
<div class="mx-auto max-w-6xl px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Rekap</h1>
        <p class="mt-1 text-sm text-slate-600">Pilih rekap yang ingin Anda buka.</p>
    </div>

    <label for="rekap-search" class="sr-only">Cari rekap</label>
    <input id="rekap-search" type="search" autocomplete="off" placeholder="Cari rekap..." class="mb-5 w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

    <div id="rekap-groups" class="grid grid-cols-1 items-start gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($groups as $category => $items)
            <section class="rekap-group rounded-xl border border-slate-200 bg-white p-4 shadow-sm" aria-label="{{ $category }}">
                <h2>
                    <button type="button" class="rekap-toggle flex w-full items-center justify-between border-b border-slate-200 px-3 pb-3 text-left text-sm font-semibold text-slate-800" aria-controls="rekap-content-{{ $loop->index }}" aria-expanded="false">
                        <span>{{ $category }}</span>
                        <svg class="rekap-arrow h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </h2>
                <div id="rekap-content-{{ $loop->index }}" class="rekap-content mt-3 space-y-1" style="display: none;">
                    @foreach($items as $item)
                        <a href="{{ route($item['route']) }}" target="_blank" rel="noopener noreferrer" class="rekap-link block rounded-md px-3 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700">{{ $item['label'] }}</a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
    <p id="rekap-empty" class="hidden py-8 text-center text-sm text-slate-500">Rekap tidak ditemukan.</p>
</div>
@endsection

@push('scripts')
<script>
    const rekapSearch = document.getElementById('rekap-search');
    const rekapGroups = [...document.querySelectorAll('.rekap-group')];
    const rekapEmpty = document.getElementById('rekap-empty');

    function setRekapExpanded(group, expanded) {
        const toggle = group.querySelector('.rekap-toggle');
        toggle.setAttribute('aria-expanded', String(expanded));
        toggle.querySelector('.rekap-arrow').classList.toggle('rotate-180', expanded);
        group.querySelector('.rekap-content').style.display = expanded ? 'block' : 'none';
    }

    rekapGroups.forEach(group => {
        group.dataset.manualExpanded = 'false';
        group.querySelector('.rekap-toggle').addEventListener('click', () => {
            const expanded = group.querySelector('.rekap-toggle').getAttribute('aria-expanded') !== 'true';
            group.dataset.manualExpanded = String(expanded);
            setRekapExpanded(group, expanded);
        });
    });

    rekapSearch.addEventListener('input', () => {
        const term = rekapSearch.value.trim().toLocaleLowerCase();
        let visibleGroups = 0;
        rekapGroups.forEach(group => {
            const categoryMatches = group.querySelector('h2').textContent.toLocaleLowerCase().includes(term);
            let visibleLinks = 0;
            group.querySelectorAll('.rekap-link').forEach(link => {
                const visible = categoryMatches || link.textContent.toLocaleLowerCase().includes(term);
                link.style.display = visible ? '' : 'none';
                if (visible) visibleLinks++;
            });
            group.style.display = visibleLinks ? '' : 'none';
            setRekapExpanded(group, term ? visibleLinks > 0 : group.dataset.manualExpanded === 'true');
            if (visibleLinks) visibleGroups++;
        });
        rekapEmpty.classList.toggle('hidden', visibleGroups > 0);
    });
</script>
@endpush
