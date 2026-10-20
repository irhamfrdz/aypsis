@extends('layouts.app')
@section('title', 'Dashboard Armada')
@section('page_title', 'Dashboard Armada')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Armada</h1>
            <p class="mt-1 text-sm text-gray-500">Pemanfaatan armada GPS berdasarkan hari menghasilkan rit, ketersediaan, dan aktivitas perjalanan.</p>
        </div>
        <a href="{{ route('master.armada.index') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Kelola Master Armada</a>
    </div>
    @if(session('success'))<div role="status" class="rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">{{ session('success') }}</div>@endif
    @if($errors->any())
        <div role="alert" class="rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="GET" action="{{ route('dashboard-armada.index') }}" class="bg-white rounded-xl border border-gray-200 p-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-3">
        <input type="hidden" name="mode" value="{{ $rankingMode }}">
        <div>
            <label for="tanggal_mulai" class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
            <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ old('tanggal_mulai', $tanggalMulai->toDateString()) }}" max="{{ today()->toDateString() }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label for="tanggal_selesai" class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
            <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="{{ old('tanggal_selesai', $tanggalRanking->toDateString()) }}" max="{{ today()->toDateString() }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label for="search" class="block text-xs font-semibold text-gray-600 mb-1">Cari Armada</label>
            <input type="search" id="search" name="search" value="{{ $search }}" placeholder="Plat, kode, nama, atau pengemudi" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label for="lokasi" class="block text-xs font-semibold text-gray-600 mb-1">Lokasi</label>
            <select id="lokasi" name="lokasi" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Semua Lokasi</option>
                @foreach($lokasis as $item)<option value="{{ $item }}" @selected($lokasi === $item)>{{ $item }}</option>@endforeach
            </select>
        </div>
        <div>
            <label for="jenis" class="block text-xs font-semibold text-gray-600 mb-1">Jenis Kendaraan</label>
            <select id="jenis" name="jenis" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Semua Jenis</option>
                @foreach($jenises as $item)<option value="{{ $item }}" @selected($jenis === $item)>{{ $item }}</option>@endforeach
            </select>
        </div>
        <div class="flex items-end gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Terapkan</button>
            <a href="{{ route('dashboard-armada.index') }}" class="py-2 text-sm text-gray-500 hover:text-indigo-600">Reset</a>
        </div>
    </form>
    <p class="text-sm text-gray-500">Periode {{ $tanggalMulai->format('d M Y') }}–{{ $tanggalRanking->format('d M Y') }} ({{ $periode }} hari kalender, termasuk tanggal awal dan akhir). Pilih rentang tanggal lalu klik Terapkan. Default 7 hari sampai kemarin.
        @if($tanggalRanking->isToday())<span class="text-amber-700">Periode ini mencakup hari ini; data masih dapat bertambah.</span>@endif
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach([
            ['Armada GPS', $totalArmada],
            ['Total Rit pada Hari Tersedia', $rankingSummary['rit']],
            ['Armada Mendapat Ranking', $rankingSummary['ranked_vehicles']],
            ['Perlu Pemeriksaan Data', $rankingSummary['incomplete_vehicles']],
        ] as [$label, $value])
            <div class="bg-white rounded-xl border border-gray-200 p-5"><p class="text-sm text-gray-500">{{ $label }}</p><p class="mt-2 text-3xl font-bold text-indigo-700">{{ number_format($value, 0, ',', '.') }}</p></div>
        @endforeach
    </div>

    @include('dashboard-armada._ranking')
    @include('dashboard-armada._availability')
</div>
@endsection
