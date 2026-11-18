@extends('layouts.app')

@section('title', 'Manifest Sugeng')
@section('page_title', 'Manifest Sugeng')

@section('content')
<div class="space-y-5 px-4 py-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">Manifest Sugeng</h1><p class="text-sm text-gray-600">Klik nomor baris untuk memilih; Shift untuk rentang, Ctrl untuk beberapa baris. Ctrl+C menyalin, Delete mengeluarkan baris terpilih dari Manifest Sugeng tanpa menghapus tanda terima sumber, dan data Excel dapat ditempel mulai dari sel yang dipilih.</p></div>
        <div class="flex flex-wrap gap-2">
            <button id="manifest-view-open" type="button" class="rounded-lg border border-indigo-300 bg-white px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">Pilih kapal / voyage</button>
            @can('manifest-sugeng-manage')<button id="manifest-assign-open" type="button" hidden class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Masukan ke dalam kapal (<span id="manifest-assign-selected-count">0</span>)</button>@endcan
            @if($hasViewChoice && $items->total() > 0)<a href="{{ route('manifest-sugeng.export', request()->only(['nama_kapal', 'no_voyage', 'assignment', 'type', 'search'])) }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"><i class="fas fa-file-excel" aria-hidden="true"></i>Download Excel</a>@endif
        </div>
    </div>
    @if(session('success'))<div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    @error('nomor_bl')<div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>@enderror
    <style>#manifest-view-dialog .select2-container { width: 100% !important; } #manifest-view-dialog .select2-container--default .select2-selection--single { height: 42px; border: 1px solid #d1d5db; border-radius: .5rem; } #manifest-view-dialog .select2-container--default .select2-selection__rendered { line-height: 40px; } #manifest-view-dialog .select2-container--default .select2-selection__arrow { height: 40px; }</style>
    <dialog id="manifest-view-dialog" data-auto-open="{{ $hasViewChoice ? '0' : '1' }}" data-voyages-url="{{ url('/manifest-sugeng/voyages') }}" class="w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-gray-200 bg-white p-0 shadow-2xl backdrop:bg-slate-900/60">
        <div class="border-b border-gray-100 px-6 py-5"><h2 class="text-lg font-bold text-gray-900">Pilih data Manifest Sugeng</h2><p class="mt-1 text-sm text-gray-600">Pilih kapal dan voyage, atau lihat data yang belum ditugaskan.</p></div>
        <form method="GET" action="{{ route('manifest-sugeng.index') }}" class="space-y-4 px-6 py-5">
            <label class="block text-sm font-semibold text-gray-700">Nama kapal
                <select id="manifest-view-ship" name="nama_kapal" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><option value="">-- Pilih kapal --</option>@foreach($viewShips as $ship)<option value="{{ $ship }}" @selected(request('nama_kapal') === $ship)>{{ $ship }}</option>@endforeach</select>
            </label>
            <label class="block text-sm font-semibold text-gray-700">No. voyage
                <select id="manifest-view-voyage" name="no_voyage" required @disabled(!request('nama_kapal')) class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><option value="">-- Pilih voyage --</option>@foreach($voyages as $voyage)<option value="{{ $voyage }}" @selected(request('no_voyage') === $voyage)>{{ $voyage }}</option>@endforeach</select>
            </label>
            <div class="flex flex-wrap justify-between gap-3 pt-2"><a href="{{ route('manifest-sugeng.index', ['assignment' => 'unassigned']) }}" class="rounded-lg border border-blue-300 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50">Tanpa kapal dan voyage</a><div class="flex gap-2"><button id="manifest-view-cancel" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm">Batal</button><button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Tampilkan</button></div></div>
        </form>
    </dialog>
    @can('manifest-sugeng-manage')
    <style>
        #manifest-finish-dialog { width: min(34rem, calc(100vw - 2rem)); max-height: calc(100dvh - 2rem); margin: auto; overflow: visible; border-radius: 1rem; }
        #manifest-finish-dialog::backdrop { background: rgb(15 23 42 / 55%); backdrop-filter: blur(3px); }
        #manifest-finish-dialog .select2-container { width: 100% !important; }
        #manifest-finish-dialog .select2-container--default .select2-selection--single { height: 44px; border: 1px solid #d1d5db; border-radius: .75rem; }
        #manifest-finish-dialog .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 42px; padding-left: 12px; color: #111827; }
        #manifest-finish-dialog .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px; right: 8px; }
        #manifest-finish-dialog .select2-dropdown { border-color: #c7d2fe; border-radius: .75rem; overflow: hidden; }
        #manifest-finish-dialog .select2-search--dropdown { padding: 8px; }
        #manifest-finish-dialog .select2-search--dropdown .select2-search__field { border: 1px solid #d1d5db; border-radius: .5rem; padding: 7px 10px; outline-color: #6366f1; }
    </style>
    <dialog id="manifest-finish-dialog" data-voyages-url="{{ url('/api/manifests/voyages') }}" class="w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-gray-200 bg-white p-0 shadow-2xl">
        <form id="manifest-finish-form" method="POST" action="{{ route('manifest-sugeng.assign-voyage') }}">
            @csrf
            <div class="flex items-start gap-3 border-b border-gray-100 bg-slate-50 px-6 py-5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700"><i class="fas fa-ship" aria-hidden="true"></i></span>
                <div><h2 id="manifest-finish-title" class="text-lg font-bold text-gray-900">Masukan ke dalam kapal</h2>
                <p id="manifest-finish-count" class="mt-1 text-sm text-gray-600">Pilih kapal dan voyage tujuan untuk data pada halaman ini.</p></div>
                <button type="button" class="ml-auto rounded-lg p-2 text-gray-400 hover:bg-gray-200 hover:text-gray-700" aria-label="Tutup" onclick="document.getElementById('manifest-finish-dialog').close()"><i class="fas fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="space-y-5 px-6 py-6">
                <label class="block text-sm font-semibold text-gray-700">Nama kapal
                    <select id="manifest-finish-ship" name="nama_kapal" required data-placeholder="Cari atau pilih kapal" class="mt-2 block w-full text-sm">
                        <option value="">-- Pilih kapal --</option>
                        @foreach($ships as $ship)<option value="{{ $ship }}">{{ $ship }}</option>@endforeach
                    </select>
                </label>
                <label class="block text-sm font-semibold text-gray-700">No. voyage
                    <select id="manifest-finish-voyage" name="no_voyage" required disabled data-placeholder="Pilih kapal terlebih dahulu" class="mt-2 block w-full text-sm">
                        <option value="">-- Pilih kapal terlebih dahulu --</option>
                    </select>
                </label>
                <p id="manifest-finish-help" class="rounded-lg bg-blue-50 px-3 py-2 text-xs text-blue-800">Hanya data yang dicentang yang akan ditugaskan.</p>
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-100 px-6 py-4">
                <button id="manifest-finish-cancel" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Batal</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Simpan penugasan</button>
            </div>
        </form>
    </dialog>
    @endcan
    <form method="GET" class="flex flex-wrap gap-3 rounded-xl border border-gray-200 bg-white p-4">
        @if($showUnassigned)<input type="hidden" name="assignment" value="unassigned">@else<input type="hidden" name="nama_kapal" value="{{ request('nama_kapal') }}"><input type="hidden" name="no_voyage" value="{{ request('no_voyage') }}">@endif
        <input name="search" value="{{ request('search') }}" placeholder="Cari BL, tanda terima, kontainer, pengirim" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <span class="self-center text-sm font-medium text-gray-600">{{ $showUnassigned ? 'Tanpa kapal dan voyage' : (request('nama_kapal') && request('no_voyage') ? request('nama_kapal').' / '.request('no_voyage') : 'Pilih tampilan terlebih dahulu') }}</span>
        <select name="type" class="rounded-lg border border-gray-300 px-3 py-2 text-sm"><option value="">Semua jenis</option>@foreach(['fcl' => 'FCL', 'lcl' => 'LCL', 'ttsj' => 'TTSJ'] as $value => $label)<option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>@endforeach</select>
        <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Cari</button>
    </form>
    @include('manifest-sugeng._sheet')
    {{ $items->links() }}
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/manifest-sugeng.js') }}?v={{ substr(hash_file('sha256', public_path('js/manifest-sugeng.js')), 0, 12) }}" defer></script>
@endpush
