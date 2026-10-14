@extends('layouts.app')

@section('title', 'Master Tarif')
@section('page_title', 'Master Tarif')

@push('styles')
<style>
    .tarif-page { max-width: 1800px; margin: 0 auto; }
    .tarif-heading { margin-bottom: 18px; }
    .tarif-heading h1 { margin: 0; color: #111827; font-size: 1.5rem; font-weight: 700; line-height: 1.3; }
    .tarif-heading p { margin: 5px 0 0; color: #6b7280; font-size: 0.875rem; }
    .tarif-workspace { display: grid; grid-template-columns: 260px minmax(0, 1fr); gap: 16px; align-items: stretch; }
    .tarif-picker, .tarif-content { min-width: 0; overflow: hidden; border: 1px solid #e5e7eb; border-radius: 12px; background: #fff; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06); }
    .tarif-picker { display: flex; flex-direction: column; height: calc(100vh - 165px); min-height: 620px; }
    .tarif-picker-header { flex: none; padding: 16px 14px 12px; border-bottom: 1px solid #f1f5f9; }
    .tarif-picker-heading { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 12px; }
    .tarif-picker-heading h2 { margin: 0; color: #374151; font-size: 0.875rem; font-weight: 700; }
    .tarif-count { border-radius: 999px; padding: 2px 8px; background: #f3e8ff; color: #7e22ce; font-size: 0.75rem; font-weight: 700; }
    .tarif-search { display: block; width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 9px 11px; background: #fff; color: #111827; font-size: 0.875rem; }
    .tarif-search:focus { border-color: #9333ea; outline: 2px solid rgba(147, 51, 234, 0.16); }
    .tarif-list { flex: 1; min-height: 0; overflow-y: auto; padding: 8px; }
    .tarif-item { display: flex; align-items: center; width: 100%; margin: 2px 0; border: 0; border-radius: 8px; padding: 10px 11px; background: transparent; color: #374151; cursor: pointer; font-size: 0.875rem; line-height: 1.35; text-align: left; transition: background-color .15s, color .15s; }
    .tarif-item:hover, .tarif-item:focus-visible { background: #faf5ff; color: #7e22ce; }
    .tarif-item:focus-visible { outline: 2px solid #a855f7; outline-offset: -2px; }
    .tarif-item[aria-current="page"] { background: #f3e8ff; color: #6b21a8; font-weight: 700; }
    .tarif-item.hidden { display: none; }
    .tarif-empty { padding: 12px; color: #6b7280; font-size: 0.875rem; }
    .tarif-content { display: flex; flex-direction: column; }
    .tarif-content-header { flex: none; display: flex; align-items: center; min-height: 54px; padding: 12px 18px; border-bottom: 1px solid #e5e7eb; }
    .tarif-content-header h2 { margin: 0; color: #111827; font-size: 1rem; font-weight: 700; }
    .tarif-frame { display: block; width: 100%; height: calc(100vh - 220px); min-height: 565px; border: 0; background: #fff; }
    @media (max-width: 850px) {
        .tarif-workspace { grid-template-columns: minmax(0, 1fr); }
        .tarif-picker { height: auto; min-height: 0; }
        .tarif-list { max-height: 220px; }
        .tarif-frame { height: 70vh; min-height: 520px; }
    }
</style>
@endpush

@section('content')
<div class="tarif-page">
    <div class="tarif-heading">
        <h1 class="text-2xl font-bold text-gray-900">Master Tarif</h1>
        <p class="mt-1 text-sm text-gray-600">Pilih jenis tarif di sebelah kiri untuk mengelola datanya di halaman ini.</p>
    </div>

    <div class="tarif-workspace">
        <aside class="tarif-picker" aria-label="Jenis master tarif">
            <div class="tarif-picker-header">
                <div class="tarif-picker-heading">
                    <h2>Jenis tarif</h2>
                    <span class="tarif-count">{{ $items->count() }}</span>
                </div>
                <label for="tarif-search" class="sr-only">Cari jenis tarif</label>
                <input id="tarif-search" type="search" placeholder="Cari jenis tarif..." class="tarif-search">
            </div>
            <nav id="tarif-list" class="tarif-list" aria-label="Daftar jenis tarif">
                @foreach($items as $item)
                    <button type="button" data-tarif-url="{{ route($item['route']) }}" class="tarif-item" @if($loop->first) aria-current="page" @endif>
                        {{ $item['label'] }}
                    </button>
                @endforeach
            </nav>
            <p id="tarif-empty" class="tarif-empty" hidden>Jenis tarif tidak ditemukan.</p>
        </aside>

        <section class="tarif-content" aria-label="Pengelolaan master tarif">
            <div class="tarif-content-header">
                <h2 id="tarif-title" class="font-semibold text-gray-900">{{ $items->first()['label'] }}</h2>
            </div>
            <iframe id="tarif-frame" name="master-tarif-frame" src="{{ route($items->first()['route']) }}" title="{{ $items->first()['label'] }}" class="tarif-frame" loading="eager"></iframe>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const frame = document.getElementById('tarif-frame');
    const title = document.getElementById('tarif-title');
    const search = document.getElementById('tarif-search');
    const buttons = [...document.querySelectorAll('.tarif-item')];

    function select(button) {
        buttons.forEach(item => {
            if (item === button) item.setAttribute('aria-current', 'page');
            else item.removeAttribute('aria-current');
        });
        title.textContent = button.textContent.trim();
        frame.title = title.textContent;
        frame.src = button.dataset.tarifUrl;
    }

    buttons.forEach(button => button.addEventListener('click', () => select(button)));
    search.addEventListener('input', () => {
        const term = search.value.trim().toLocaleLowerCase();
        let visible = 0;
        buttons.forEach(button => {
            const match = button.textContent.toLocaleLowerCase().includes(term);
            button.classList.toggle('hidden', !match);
            if (match) visible++;
        });
        document.getElementById('tarif-empty').hidden = visible > 0;
    });

    frame.addEventListener('load', () => {
        try {
            const documentInFrame = frame.contentDocument;
            if (!documentInFrame) return;
            documentInFrame.querySelectorAll('a[target="_blank"]').forEach(link => {
                if (link.origin === window.location.origin && !link.hasAttribute('download') && !/export|print|template/i.test(link.pathname)) {
                    link.target = '_self';
                }
            });
        } catch (error) {}
    });
});
</script>
@endpush
