@extends('layouts.app')

@section('title', 'Tracking Transaksi')
@section('page_title', 'Tracking Transaksi')

@section('content')
@php
    $activeType = $selectedType && isset($types[$selectedType]) ? $selectedType : array_key_first($types);
@endphp
<div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8 space-y-6">
    <section class="rounded-2xl bg-gradient-to-r from-slate-900 to-blue-800 p-6 text-white shadow-sm sm:p-8">
        <div class="flex items-start gap-4">
            <div class="hidden sm:flex h-12 w-12 items-center justify-center rounded-xl bg-white/10 text-xl"><i class="fas fa-route"></i></div>
            <div>
                <h2 class="text-2xl font-bold">Tracking Transaksi</h2>
                <p class="mt-1 max-w-2xl text-sm text-blue-100">Pilih jenis dokumen dan masukkan nomornya untuk melihat riwayat status transaksi.</p>
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-6">
        <form method="GET" action="{{ route('tracking-transaksi.index') }}" class="space-y-5">
            <fieldset>
                <legend class="mb-3 text-sm font-semibold text-slate-800">1. Pilih jenis dokumen</legend>
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($types as $key => $config)
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="{{ $key }}" class="peer sr-only" @checked($activeType === $key)>
                        <span class="flex h-full items-center gap-3 rounded-xl border border-slate-200 p-4 text-sm text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-800 peer-checked:ring-1 peer-checked:ring-blue-600">
                            <i class="fas fa-file-alt text-blue-600"></i>
                            <span class="font-medium">{{ $config['label'] }}</span>
                        </span>
                    </label>
                    @endforeach
                </div>
            </fieldset>
            <div class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                <div>
                    <label for="tracking-number" class="mb-2 block text-sm font-semibold text-slate-800">2. Masukkan nomor dokumen</label>
                    <input id="tracking-number" name="number" value="{{ $number }}" required autocomplete="off" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ $activeType ? $types[$activeType]['number_label'] : 'Nomor dokumen' }}">
                </div>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-search"></i> Lacak Transaksi
                </button>
            </div>
        </form>
    </section>

    @if($notFound)
        <div role="alert" class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">Nomor <strong>{{ $number }}</strong> tidak ditemukan pada jenis dokumen yang dipilih.</div>
    @endif

    @if($record)
        <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">{{ $types[$selectedType]['label'] }}</p>
                    <h3 class="mt-1 text-xl font-bold text-slate-900">{{ $number }}</h3>
                </div>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800">{{ count($timeline) }} riwayat</span>
            </div>
            <div class="mt-6">
                <h4 class="mb-4 text-sm font-semibold text-slate-800">Timeline transaksi</h4>
                @if(count($timeline))
                    <ol class="relative ml-2 border-l border-slate-200">
                        @foreach($timeline as $event)
                        <li class="relative pb-6 pl-6 last:pb-0">
                            <span class="absolute -left-[7px] top-1 h-3.5 w-3.5 rounded-full border-2 border-white bg-blue-600 ring-1 ring-blue-200"></span>
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
    @endif
</div>
@endsection
