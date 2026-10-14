@extends('layouts.app')

@section('title', 'Tracking Transaksi')
@section('page_title', 'Tracking Transaksi')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8 space-y-6">
    <section class="rounded-2xl bg-gradient-to-r from-slate-900 to-blue-800 p-6 text-white shadow-sm sm:p-8">
        <div class="flex items-start gap-4">
            <div class="hidden sm:flex h-12 w-12 items-center justify-center rounded-xl bg-white/10 text-xl"><i class="fas fa-route"></i></div>
            <div>
                <h2 class="text-2xl font-bold">Tracking Transaksi</h2>
                <p class="mt-1 max-w-2xl text-sm text-blue-100">Cari nomor untuk menelusuri seluruh sumber dokumen transaksi.</p>
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-6">
        <form method="GET" action="{{ route('tracking-transaksi.index') }}" class="space-y-3">
            <label for="tracking-number" class="block text-sm font-semibold text-slate-800">Cari nomor dokumen</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i class="fas fa-search"></i></div>
                <input id="tracking-number" name="number" value="{{ $number }}" required autocomplete="off" role="combobox" aria-autocomplete="list" aria-controls="tracking-suggestions" aria-expanded="false" data-suggestions-url="{{ route('tracking-transaksi.suggestions') }}" class="block w-full appearance-none rounded-xl border border-slate-300 bg-white py-3 pl-10 pr-4 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100" placeholder="Ketik nomor surat jalan atau tanda terima">
                <ul id="tracking-suggestions" role="listbox" class="absolute z-30 mt-2 max-h-72 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white py-1 shadow-xl" hidden></ul>
            </div>
            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="fas fa-search"></i> Lacak Transaksi
            </button>
        </form>
    </section>

    @if($number !== '' && count($results) === 0)
        <div role="alert" class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">Tidak ada transaksi yang cocok dengan nomor <strong>{{ $number }}</strong>.</div>
    @endif

    @foreach($results as $result)
    <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">{{ $result['label'] }}</p>
                <h3 class="mt-1 text-xl font-bold text-slate-900">{{ $result['number'] }}</h3>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ collect($result['timeline'])->every(fn ($event) => $event['complete']) ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                {{ collect($result['timeline'])->where('complete', true)->count() }} dari {{ count($result['timeline']) }} tahap selesai
            </span>
        </div>
        @if(in_array($result['type'], ['surat-jalan', 'tanda-terima-lcl', 'tanda-terima-tanpa-surat-jalan'], true))
        <dl class="mt-4 grid gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nomor kontainer</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $result['details']['container'] ?: 'Belum tercatat' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Shipper</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $result['details']['shipper'] ?: 'Belum tercatat' }}</dd>
            </div>
        </dl>
        @endif
        <div class="mt-6">
            <h4 class="mb-4 text-sm font-semibold text-slate-800">Timeline transaksi</h4>
            @if(count($result['timeline']))
                <ol class="relative ml-2 border-l border-slate-200">
                    @foreach($result['timeline'] as $event)
                    <li class="relative pb-6 pl-6 last:pb-0">
                        <span class="absolute -left-[7px] top-1 h-3.5 w-3.5 rounded-full border-2 border-white {{ $event['complete'] ? 'bg-emerald-600 ring-emerald-200' : 'bg-slate-300 ring-slate-200' }} ring-1"></span>
                        <time class="text-xs font-medium text-slate-500">{{ $event['formatted_at'] }}</time>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $event['title'] }}</p>
                        @if($event['description'])<p class="mt-0.5 text-sm text-slate-600">{{ $event['description'] }}</p>@endif
                    </li>
                    @endforeach
                </ol>
            @else
                <p class="text-sm text-slate-500">Belum ada tanggal atau riwayat status yang tercatat untuk dokumen ini.</p>
            @endif
        </div>
    </section>
    @endforeach
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('tracking-number');
    const list = document.getElementById('tracking-suggestions');
    const form = input?.closest('form');
    if (!input || !list || !form) return;

    let timer;
    let activeRequest;

    const closeSuggestions = () => {
        list.hidden = true;
        input.setAttribute('aria-expanded', 'false');
    };

    const showSuggestions = (suggestions) => {
        list.replaceChildren();
        if (suggestions.length === 0) {
            const empty = document.createElement('li');
            empty.className = 'px-4 py-3 text-sm text-slate-500';
            empty.textContent = 'Tidak ada nomor yang cocok.';
            list.appendChild(empty);
        } else {
            suggestions.forEach((suggestion) => {
                const item = document.createElement('li');
                const button = document.createElement('button');
                button.type = 'button';
                button.role = 'option';
                button.className = 'flex w-full items-center justify-between gap-4 px-4 py-3 text-left hover:bg-blue-50 focus:bg-blue-50 focus:outline-none';

                const number = document.createElement('span');
                number.className = 'text-sm font-semibold text-slate-800';
                number.textContent = suggestion.number;
                const type = document.createElement('span');
                type.className = 'text-xs text-slate-500';
                type.textContent = suggestion.type;
                button.append(number, type);
                button.addEventListener('click', () => {
                    input.value = suggestion.number;
                    closeSuggestions();
                    form.requestSubmit();
                });
                item.appendChild(button);
                list.appendChild(item);
            });
        }
        list.hidden = false;
        input.setAttribute('aria-expanded', 'true');
    };

    const showMessage = (message) => {
        list.replaceChildren();
        const item = document.createElement('li');
        item.className = 'px-4 py-3 text-sm text-slate-500';
        item.textContent = message;
        list.appendChild(item);
        list.hidden = false;
        input.setAttribute('aria-expanded', 'true');
    };

    input.addEventListener('input', () => {
        clearTimeout(timer);
        activeRequest?.abort();
        const term = input.value.trim();
        if (term.length < 2) {
            closeSuggestions();
            return;
        }

        timer = setTimeout(async () => {
            activeRequest = new AbortController();
            try {
                const url = new URL(input.dataset.suggestionsUrl, window.location.origin);
                url.searchParams.set('q', term);
                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    signal: activeRequest.signal,
                });
                if (!response.ok) throw new Error('Gagal mengambil saran nomor.');
                const suggestions = await response.json();
                if (input.value.trim() === term) showSuggestions(suggestions);
            } catch (error) {
                if (error.name !== 'AbortError') {
                    showMessage('Saran nomor tidak dapat dimuat. Tekan Lacak Transaksi untuk mencari.');
                }
            }
        }, 250);
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeSuggestions();
        if (event.key === 'Enter' && !list.hidden && list.querySelector('button')) {
            event.preventDefault();
            list.querySelector('button').click();
        }
    });

    document.addEventListener('click', (event) => {
        if (!list.contains(event.target) && event.target !== input) closeSuggestions();
    });
});
</script>
@endsection
