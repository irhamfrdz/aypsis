@extends('layouts.app')

@section('title', 'Dashboard Tanggal Gerak Voyage')
@section('page_title', 'Dashboard Tanggal Gerak Voyage')

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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="rounded-2xl bg-gradient-to-r from-blue-700 to-indigo-700 p-6 sm:p-8 text-white shadow-lg">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-blue-100">Monitoring Kapal</p>
                    <h1 class="mt-1 text-2xl sm:text-3xl font-bold">Dashboard Tanggal Gerak Voyage</h1>
                    <p class="mt-2 text-sm text-blue-100">Ringkasan tanggal perjalanan per kapal dan nomor voyage dari data manifest.</p>
                </div>
                <a href="{{ route('gerak-voyage.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-50">
                    <i class="fas fa-calendar-plus mr-2"></i> Atur Tanggal
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Total Voyage</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($totalVoyages, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <p class="text-sm text-emerald-700">Ada Tanggal Gerak</p>
                <p class="mt-2 text-3xl font-bold text-emerald-800">{{ number_format($voyagesWithDates, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <p class="text-sm text-amber-700">Belum Ada Tanggal</p>
                <p class="mt-2 text-3xl font-bold text-amber-800">{{ number_format($voyagesWithoutDates, 0, ',', '.') }}</p>
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
                    <label for="no_voyage" class="mb-1 block text-sm font-semibold text-slate-700">Nomor Voyage</label>
                    <input id="no_voyage" name="no_voyage" type="search" value="{{ $filters['no_voyage'] ?? '' }}" placeholder="Cari nomor voyage" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="status" class="mb-1 block text-sm font-semibold text-slate-700">Status Tanggal</label>
                    <select id="status" name="status" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua status</option>
                        <option value="terisi" @selected(($filters['status'] ?? '') === 'terisi')>Ada tanggal</option>
                        <option value="belum_terisi" @selected(($filters['status'] ?? '') === 'belum_terisi')>Belum ada tanggal</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Tampilkan</button>
                    <a href="{{ route('gerak-voyage.dashboard') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                </div>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-bold text-slate-900">Daftar Voyage</h2>
                <span class="text-sm text-slate-500">{{ number_format($voyages->total(), 0, ',', '.') }} hasil</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3">Kapal / Voyage</th>
                            <th class="whitespace-nowrap px-4 py-3 text-right">Manifest</th>
                            @foreach($tanggalLabels as $label)
                                <th class="whitespace-nowrap px-4 py-3">{{ $label }}</th>
                            @endforeach
                            <th class="whitespace-nowrap px-4 py-3">Status</th>
                            <th class="whitespace-nowrap px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($voyages as $voyage)
                            @php
                                $hasDate = collect($tanggalFields)->contains(fn ($field) => ! empty($voyage->{$field}));
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $voyage->nama_kapal }}</div>
                                    <div class="text-xs text-slate-500">Voyage {{ $voyage->no_voyage }}</div>
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-slate-700">{{ number_format($voyage->jumlah_manifest, 0, ',', '.') }}</td>
                                @foreach($tanggalLabels as $field => $label)
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-700">
                                        {{ $voyage->{$field} ? \Carbon\Carbon::parse($voyage->{$field})->format('d/m/Y') : '—' }}
                                    </td>
                                @endforeach
                                <td class="whitespace-nowrap px-4 py-3">
                                    @if($hasDate)
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Ada tanggal</span>
                                    @else
                                        <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Belum diisi</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <a href="{{ route('gerak-voyage.create', ['nama_kapal' => $voyage->nama_kapal, 'no_voyage' => $voyage->no_voyage]) }}" class="font-semibold text-blue-700 hover:text-blue-900 hover:underline">Atur Tanggal</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-10 text-center text-slate-500">Tidak ada voyage yang sesuai dengan filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($voyages->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">{{ $voyages->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
