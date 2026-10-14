@extends('layouts.app')

@section('title', 'Master Data')
@section('page_title', 'Master Data')

@push('styles')
<style>
    #master-data-directory {
        align-items: start;
    }
    #master-data-directory > div {
        margin: 0;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        background: #fff;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    }
    #master-data-directory > div > button,
    #master-data-directory > div > a {
        width: 100%;
        min-height: 2.75rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    #master-data-directory > div > button span,
    #master-data-directory > div > a span { font-size: inherit; }
    #master-data-directory > div > .dropdown-content {
        margin: 0.75rem 0 0;
        padding-top: 0.75rem;
        border-top: 1px solid #e2e8f0;
        border-bottom: 0;
    }
    #master-data-directory .dropdown-content a {
        min-height: 2rem;
        margin: 0.125rem 0;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
    }
    #master-data-directory .dropdown-content a:hover { background: #f1f5f9; }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
@endphp
<div class="mx-auto max-w-6xl px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Master Data</h1>
        <p class="mt-1 text-sm text-slate-600">Pilih kategori untuk membuka data yang Anda perlukan.</p>
    </div>

    <label for="master-data-search" class="sr-only">Cari master data</label>
    <input id="master-data-search" type="search" autocomplete="off" placeholder="Cari master data..." class="mb-5 w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

    <div id="master-data-directory" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @include('master.data._directory')
    </div>
    <p id="master-data-empty" class="hidden py-8 text-center text-sm text-slate-500">Master data tidak ditemukan.</p>
</div>
@endsection

@push('scripts')
<script>
    const masterDirectory = document.getElementById('master-data-directory');
    const masterSearch = document.getElementById('master-data-search');
    const masterEmpty = document.getElementById('master-data-empty');
    const masterCards = [...masterDirectory.children];

    masterCards.forEach(card => {
        const toggle = card.querySelector('button[id$="-toggle"]');
        if (!toggle) return;
        const content = document.getElementById(toggle.id.replace(/-toggle$/, '-content'));
        if (!content) return;
        toggle.type = 'button';
        toggle.setAttribute('aria-controls', content.id);
        toggle.setAttribute('aria-expanded', 'false');
        content.style.display = 'none';
        toggle.addEventListener('click', () => {
            const expanded = toggle.getAttribute('aria-expanded') !== 'true';
            toggle.setAttribute('aria-expanded', String(expanded));
            toggle.querySelector('.dropdown-arrow')?.classList.toggle('rotate-180', expanded);
            content.style.display = expanded ? 'block' : 'none';
        });
    });

    masterSearch.addEventListener('input', () => {
        const term = masterSearch.value.trim().toLocaleLowerCase();
        let visibleCards = 0;
        masterCards.forEach(card => {
            const toggle = card.querySelector('button[id$="-toggle"]');
            const content = toggle && document.getElementById(toggle.id.replace(/-toggle$/, '-content'));
            const links = [...card.querySelectorAll('a')];
            const categoryMatches = toggle?.textContent.toLocaleLowerCase().includes(term);
            let visibleLinks = 0;
            links.forEach(link => {
                const visible = !term || categoryMatches || link.textContent.toLocaleLowerCase().includes(term);
                link.hidden = !visible;
                if (visible) visibleLinks++;
            });
            const visible = !term || categoryMatches || visibleLinks > 0;
            card.hidden = !visible;
            if (visible) visibleCards++;
            if (content && term) {
                content.style.display = 'block';
                toggle.setAttribute('aria-expanded', 'true');
                toggle.querySelector('.dropdown-arrow')?.classList.add('rotate-180');
            }
        });
        masterEmpty.classList.toggle('hidden', visibleCards > 0);
    });
</script>
@endpush
