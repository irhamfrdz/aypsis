@extends('layouts.app')

@section('title', 'Master Tarif')
@section('page_title', 'Master Tarif')

@section('content')
<div class="mx-auto max-w-screen-2xl">
    <div class="mb-5">
        <h1 class="text-2xl font-bold text-gray-900">Master Tarif</h1>
        <p class="mt-1 text-sm text-gray-600">Pilih jenis tarif di sebelah kiri untuk mengelola datanya di halaman ini.</p>
    </div>

    <div class="grid gap-4 lg:grid-cols-[16rem_minmax(0,1fr)]">
        <aside class="rounded-lg border border-gray-200 bg-white p-3 shadow-sm lg:self-start" aria-label="Jenis master tarif">
            <label for="tarif-search" class="sr-only">Cari jenis tarif</label>
            <input id="tarif-search" type="search" placeholder="Cari jenis tarif..." class="mb-3 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-purple-500 focus:ring-purple-500">
            <nav id="tarif-list" class="max-h-72 space-y-1 overflow-y-auto lg:max-h-[calc(100vh-16rem)]" aria-label="Daftar jenis tarif">
                @foreach($items as $item)
                    <button type="button" data-tarif-url="{{ route($item['route']) }}" class="tarif-item flex w-full items-center rounded-md px-3 py-2 text-left text-sm text-gray-700 transition hover:bg-purple-50 hover:text-purple-700" @if($loop->first) aria-current="page" @endif>
                        {{ $item['label'] }}
                    </button>
                @endforeach
            </nav>
            <p id="tarif-empty" class="hidden px-3 py-2 text-sm text-gray-500">Jenis tarif tidak ditemukan.</p>
        </aside>

        <section class="min-w-0 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm" aria-label="Pengelolaan master tarif">
            <div class="border-b border-gray-200 px-4 py-3">
                <h2 id="tarif-title" class="font-semibold text-gray-900">{{ $items->first()['label'] }}</h2>
            </div>
            <iframe id="tarif-frame" name="master-tarif-frame" src="{{ route($items->first()['route']) }}" title="{{ $items->first()['label'] }}" class="h-[75vh] min-h-[36rem] w-full border-0" loading="eager"></iframe>
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
            item.classList.toggle('bg-purple-50', item === button);
            item.classList.toggle('text-purple-700', item === button);
            item.classList.toggle('font-semibold', item === button);
            if (item === button) item.setAttribute('aria-current', 'page');
            else item.removeAttribute('aria-current');
        });
        title.textContent = button.textContent.trim();
        frame.title = title.textContent;
        frame.src = button.dataset.tarifUrl;
    }

    buttons.forEach(button => button.addEventListener('click', () => select(button)));
    buttons[0]?.classList.add('bg-purple-50', 'text-purple-700', 'font-semibold');

    search.addEventListener('input', () => {
        const term = search.value.trim().toLocaleLowerCase();
        let visible = 0;
        buttons.forEach(button => {
            const match = button.textContent.toLocaleLowerCase().includes(term);
            button.classList.toggle('hidden', !match);
            if (match) visible++;
        });
        document.getElementById('tarif-empty').classList.toggle('hidden', visible > 0);
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
