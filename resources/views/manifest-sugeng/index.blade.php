@extends('layouts.app')

@section('title', 'Manifest Sugeng')
@section('page_title', 'Manifest Sugeng')

@section('content')
<div class="space-y-5 px-4 py-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">Manifest Sugeng</h1><p class="text-sm text-gray-600">Klik nomor baris untuk memilih; Shift untuk rentang, Ctrl untuk beberapa baris. Ctrl+C menyalin, Delete mengeluarkan baris terpilih dari Manifest Sugeng tanpa menghapus tanda terima sumber, dan data Excel dapat ditempel mulai dari sel yang dipilih.</p></div>
        <div class="flex flex-wrap gap-2">
            @can('manifest-sugeng-manage')<button id="manifest-finish-open" type="button" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Selesaikan pekerjaan</button>@endcan
            @if(request('nama_kapal') && request('no_voyage'))<a href="{{ route('manifest-sugeng.export', ['nama_kapal' => request('nama_kapal'), 'no_voyage' => request('no_voyage')]) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Export Excel Manifest & Perincian</a>@endif
        </div>
    </div>
    @if(session('success'))<div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    @error('nomor_bl')<div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>@enderror
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
                <div><h2 class="text-lg font-bold text-gray-900">Selesaikan pekerjaan Manifest</h2>
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
                <p id="manifest-finish-help" class="rounded-lg bg-blue-50 px-3 py-2 text-xs text-blue-800">Hanya data yang tampil pada halaman tabel ini yang akan ditugaskan.</p>
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-100 px-6 py-4">
                <button id="manifest-finish-cancel" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Batal</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Simpan penugasan</button>
            </div>
        </form>
    </dialog>
    @endcan
    <form method="GET" class="flex flex-wrap gap-3 rounded-xl border border-gray-200 bg-white p-4">
        <input name="search" value="{{ request('search') }}" placeholder="Cari BL, tanda terima, kontainer, pengirim" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <input name="nama_kapal" list="manifest-sugeng-ships" value="{{ request('nama_kapal') }}" placeholder="Nama kapal" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <datalist id="manifest-sugeng-ships">@foreach($ships as $ship)<option value="{{ $ship }}">@endforeach</datalist>
        <input name="no_voyage" list="manifest-sugeng-voyages" value="{{ request('no_voyage') }}" placeholder="No. voyage" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <datalist id="manifest-sugeng-voyages">@foreach($voyages as $voyage)<option value="{{ $voyage }}">@endforeach</datalist>
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
