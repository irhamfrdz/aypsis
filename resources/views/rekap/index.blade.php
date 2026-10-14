@extends('layouts.app')

@section('title', 'Rekap')
@section('page_title', 'Rekap')

@push('styles')
<style>
    #rekap-groups {
        align-items: start;
    }
    #rekap-groups > section {
        margin: 0;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        background: #fff;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    }
    #rekap-groups > section > button {
        width: 100%;
        min-height: 2.75rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    #rekap-groups > section > button span { font-size: inherit; }
    #rekap-groups > section > .dropdown-content {
        margin: 0.75rem 0 0;
        padding-top: 0.75rem;
        border-top: 1px solid #e2e8f0;
        border-bottom: 0;
    }
    #rekap-groups .dropdown-content a {
        min-height: 2rem;
        margin: 0.125rem 0;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
    }
    #rekap-groups .dropdown-content a:hover { background: #f1f5f9; }
</style>
@endpush

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

    <div id="rekap-groups" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($groups as $category => $items)
            <section class="rekap-group mx-2 mb-3" aria-label="{{ $category }}">
                <button type="button" class="rekap-toggle w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 hover:shadow-sm transition-all duration-200 group" aria-controls="rekap-content-{{ $loop->index }}" aria-expanded="false">
                    <span class="text-xs font-medium">{{ $category }}</span>
                    <svg class="rekap-arrow w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="rekap-content-{{ $loop->index }}" class="rekap-content dropdown-content ml-4 mt-2 space-y-1" style="display: none;">
                    @foreach($items as $item)
                        <a href="{{ route($item['route']) }}" target="_blank" rel="noopener noreferrer" class="rekap-link flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">{{ $item['label'] }}</span></a>
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
            const categoryMatches = group.querySelector('.rekap-toggle').textContent.toLocaleLowerCase().includes(term);
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
