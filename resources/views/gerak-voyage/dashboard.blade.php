@extends('layouts.app')

@section('title', 'Dashboard Jadwal Kapal')
@section('page_title', 'Dashboard Jadwal Kapal')

@section('content')
@php
    $tanggalLabels = [
        'tanggal_muat' => 'Muat',
        'tanggal_mulai_berlayar' => 'Mulai Berlayar',
        'tanggal_berlabuh' => 'Berlabuh',
        'tanggal_sandar' => 'Sandar',
        'tanggal_mulai_bongkar' => 'Mulai Bongkar',
        'tanggal_selesai_bongkar' => 'Selesai Bongkar',
    ];
@endphp
<div class="py-6">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-800 p-6 text-white shadow-lg sm:p-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-200">Jadwal Kapal</p>
                    <h1 class="mt-2 text-2xl font-bold sm:text-3xl">Voyage Terbaru Setiap Kapal</h1>
                    <p class="mt-2 max-w-2xl text-sm text-blue-100">Pantau tanggal dan jam gerak dari voyage terbaru masing-masing kapal berdasarkan data manifest.</p>
                </div>
                <a href="{{ route('gerak-voyage.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-blue-800 hover:bg-blue-50">
                    <i class="fas fa-calendar-plus mr-2"></i> Atur Tanggal dan Jam
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Kapal dengan Voyage</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($totalShips, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <p class="text-sm font-medium text-emerald-700">Ada Tanggal Gerak</p>
                <p class="mt-2 text-3xl font-bold text-emerald-800">{{ number_format($shipsWithDates, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <p class="text-sm font-medium text-amber-700">Belum Ada Tanggal</p>
                <p class="mt-2 text-3xl font-bold text-amber-800">{{ number_format($shipsWithoutDates, 0, ',', '.') }}</p>
            </div>
        </div>

        <form method="GET" action="{{ route('gerak-voyage.dashboard') }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div>
                    <label for="nama_kapal" class="mb-1 block text-sm font-semibold text-slate-700">Kapal</label>
                    <select id="nama_kapal" name="nama_kapal" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua kapal</option>
                        @foreach($ships as $ship)
                            <option value="{{ $ship }}" @selected(($filters['nama_kapal'] ?? '') === $ship)>{{ $ship }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="no_voyage" class="mb-1 block text-sm font-semibold text-slate-700">Nomor Voyage Terbaru</label>
                    <input id="no_voyage" name="no_voyage" type="search" value="{{ $filters['no_voyage'] ?? '' }}" placeholder="Cari nomor voyage" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="status" class="mb-1 block text-sm font-semibold text-slate-700">Status Jadwal</label>
                    <select id="status" name="status" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua status</option>
                        <option value="terisi" @selected(($filters['status'] ?? '') === 'terisi')>Ada tanggal</option>
                        <option value="belum_terisi" @selected(($filters['status'] ?? '') === 'belum_terisi')>Belum ada tanggal</option>
                        <option value="jam_belum_lengkap" @selected(($filters['status'] ?? '') === 'jam_belum_lengkap')>Jam belum lengkap</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Tampilkan</button>
                    <a href="{{ route('gerak-voyage.dashboard') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                </div>
            </div>
        </form>

        <div class="flex flex-wrap items-end justify-between gap-2">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Jadwal per Kapal</h2>
                <p class="mt-1 text-sm text-slate-500">Setiap kapal menampilkan satu voyage terbaru. Urutan ditentukan dari tanggal berangkat, tanggal muat, atau tanggal pembuatan manifest.</p>
            </div>
            <span class="text-sm text-slate-500">{{ number_format($voyages->count(), 0, ',', '.') }} kapal ditampilkan</span>
        </div>

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
            @forelse($voyages as $voyage)
                @php
                    $filledDateCount = collect($tanggalFields)->filter(fn ($field) => ! empty($voyage->{$field}))->count();
                    $filledTimeCount = collect($tanggalFields)->filter(fn ($field) => ! empty($voyage->{str_replace('tanggal_', 'jam_', $field)}))->count();
                    $completeCount = collect($tanggalFields)->filter(fn ($field) => ! empty($voyage->{$field}) && ! empty($voyage->{str_replace('tanggal_', 'jam_', $field)}))->count();
                @endphp
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-5 sm:px-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-bold uppercase tracking-wide text-blue-600">Voyage {{ $voyage->no_voyage }}</p>
                                <h3 class="mt-1 break-words text-xl font-bold text-slate-900">{{ $voyage->nama_kapal }}</h3>
                                <p class="mt-1 text-xs text-slate-500">{{ number_format($voyage->jumlah_manifest, 0, ',', '.') }} manifest pada voyage ini</p>
                            </div>
                            @if($filledDateCount)
                                <span class="rounded-full {{ $filledDateCount === $completeCount ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }} px-3 py-1 text-xs font-semibold">{{ $filledDateCount }} tanggal / {{ $filledTimeCount }} jam terisi</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Jadwal belum diisi</span>
                            @endif
                        </div>
                        <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-valuenow="{{ $completeCount }}" aria-valuemin="0" aria-valuemax="6" aria-label="Tanggal dan jam gerak lengkap">
                            <div class="h-full rounded-full bg-blue-600" style="width: {{ $completeCount / 6 * 100 }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 p-5 sm:grid-cols-3 sm:p-6">
                        @foreach($tanggalLabels as $field => $label)
                            @php $jamField = str_replace('tanggal_', 'jam_', $field); @endphp
                            <div class="rounded-xl border {{ $voyage->{$field} ? 'border-blue-100 bg-blue-50' : 'border-slate-200 bg-slate-50' }} p-3">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $voyage->{$field} ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600' }} text-xs font-bold">{{ $loop->iteration }}</span>
                                    <span class="text-xs font-semibold text-slate-600">{{ $label }}</span>
                                </div>
                                <p class="mt-3 text-xs font-medium text-slate-500">Tanggal</p>
                                <p class="mt-0.5 text-sm font-bold {{ $voyage->{$field} ? 'text-slate-900' : 'text-slate-400' }}">
                                    {{ $voyage->{$field} ? \Carbon\Carbon::parse($voyage->{$field})->format('d M Y') : 'Belum diisi' }}
                                </p>
                                <p class="mt-2 text-xs font-medium text-slate-500">Jam</p>
                                <p class="mt-0.5 text-sm font-bold {{ $voyage->{$jamField} ? 'text-blue-700' : 'text-slate-400' }}">
                                    {{ $voyage->{$field} ? ($voyage->{$jamField} ? substr($voyage->{$jamField}, 0, 5).' WIB' : 'Belum diisi') : '-' }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 bg-slate-50 px-5 py-3 sm:px-6">
                        <span class="text-xs text-slate-500">Acuan voyage: {{ $voyage->tanggal_voyage ? \Carbon\Carbon::parse($voyage->tanggal_voyage)->format('d M Y') : '-' }}</span>
                        <a href="{{ route('gerak-voyage.create', ['nama_kapal' => $voyage->nama_kapal, 'no_voyage' => $voyage->no_voyage]) }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900 hover:underline">Atur Tanggal dan Jam <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500 xl:col-span-2">
                    <i class="fas fa-ship mb-3 text-3xl text-slate-300"></i>
                    <p class="font-semibold text-slate-700">Tidak ada jadwal kapal yang sesuai dengan filter.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
