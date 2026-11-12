@extends('layouts.app')

@section('title', 'Manifest Sugeng')
@section('page_title', 'Manifest Sugeng')

@section('content')
<div class="space-y-5 px-4 py-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">Manifest Sugeng</h1><p class="text-sm text-gray-600">Geser lembar ke kanan untuk melihat seluruh kolom. Klik nomor BL untuk membuka detail.</p></div>
        @if(request('nama_kapal') && request('no_voyage'))<a href="{{ route('manifest-sugeng.export', ['nama_kapal' => request('nama_kapal'), 'no_voyage' => request('no_voyage')]) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Export Excel Manifest & Perincian</a>@endif
    </div>
    @if(session('success'))<div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    @error('nomor_bl')<div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>@enderror
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
