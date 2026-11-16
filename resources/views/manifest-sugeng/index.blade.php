@extends('layouts.app')

@section('title', 'Manifest Sugeng')
@section('page_title', 'Manifest Sugeng')

@section('content')
<div class="space-y-5 px-4 py-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">Manifest Sugeng</h1><p class="text-sm text-gray-600">Klik nomor baris untuk memilih; Shift untuk rentang, Ctrl untuk beberapa baris. Ctrl+C menyalin, Delete mengosongkan baris, dan data Excel dapat ditempel mulai dari sel yang dipilih.</p></div>
        <div class="flex flex-wrap gap-2">
            @can('manifest-sugeng-manage')<button id="manifest-finish-open" type="button" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Selesaikan pekerjaan</button>@endcan
            @if(request('nama_kapal') && request('no_voyage'))<a href="{{ route('manifest-sugeng.export', ['nama_kapal' => request('nama_kapal'), 'no_voyage' => request('no_voyage')]) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Export Excel Manifest & Perincian</a>@endif
        </div>
    </div>
    @if(session('success'))<div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    @error('nomor_bl')<div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>@enderror
    @can('manifest-sugeng-manage')
    <dialog id="manifest-finish-dialog" data-voyages-url="{{ url('/api/manifests/voyages') }}" class="w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-gray-200 bg-white p-0 shadow-2xl backdrop:bg-slate-900/50">
        <form id="manifest-finish-form" method="POST" action="{{ route('manifest-sugeng.assign-voyage') }}">
            @csrf
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-bold text-gray-900">Selesaikan pekerjaan Manifest</h2>
                <p id="manifest-finish-count" class="mt-1 text-sm text-gray-600">Pilih kapal dan voyage untuk semua data pada halaman tabel ini.</p>
            </div>
            <div class="space-y-4 px-6 py-5">
                <label class="block text-sm font-semibold text-gray-700">Nama kapal
                    <select id="manifest-finish-ship" name="nama_kapal" required class="mt-1 block w-full rounded-xl border-gray-300 text-sm">
                        <option value="">-- Pilih kapal --</option>
                        @foreach($ships as $ship)<option value="{{ $ship }}">{{ $ship }}</option>@endforeach
                    </select>
                </label>
                <label class="block text-sm font-semibold text-gray-700">No. voyage
                    <select id="manifest-finish-voyage" name="no_voyage" required disabled class="mt-1 block w-full rounded-xl border-gray-300 text-sm disabled:bg-gray-100">
                        <option value="">-- Pilih kapal terlebih dahulu --</option>
                    </select>
                </label>
                <p id="manifest-finish-help" class="text-xs text-gray-500">Hanya data pada halaman tabel yang sedang dibuka yang akan ditugaskan.</p>
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
