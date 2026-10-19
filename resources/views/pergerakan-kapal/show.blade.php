@extends('layouts.app')

@section('title', 'Detail Pergerakan Kapal')
@section('page_title', 'Detail Pergerakan Kapal')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mx-auto max-w-5xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Detail Pergerakan Kapal</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $pergerakanKapal->nama_kapal }} · Voyage {{ $pergerakanKapal->voyage ?: '-' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('pergerakan-kapal.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Kembali</a>
                @can('pergerakan-kapal-update')
                    <a href="{{ route('pergerakan-kapal.edit', $pergerakanKapal) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Edit</a>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 p-5 md:grid-cols-2">
            <section class="rounded-lg bg-gray-50 p-4">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Informasi Kapal</h2>
                <dl class="space-y-4">
                    <div><dt class="text-xs text-gray-500">Nama Kapal</dt><dd class="mt-1 font-medium text-gray-900">{{ $pergerakanKapal->nama_kapal }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Kapten</dt><dd class="mt-1 text-gray-900">{{ $pergerakanKapal->kapten ?: '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Voyage</dt><dd class="mt-1 text-gray-900">{{ $pergerakanKapal->voyage ?: '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Status</dt><dd class="mt-1"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $pergerakanKapal->status_badge }}">{{ $pergerakanKapal->status_label }}</span></dd></div>
                </dl>
            </section>

            <section class="rounded-lg bg-gray-50 p-4">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Rute</h2>
                <dl class="space-y-4">
                    <div><dt class="text-xs text-gray-500">Pelabuhan Asal</dt><dd class="mt-1 text-gray-900">{{ $pergerakanKapal->tujuan_asal ?: '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Pelabuhan Tujuan</dt><dd class="mt-1 text-gray-900">{{ $pergerakanKapal->tujuan_tujuan ?: '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Transit</dt><dd class="mt-1"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $pergerakanKapal->transit_badge }}">{{ $pergerakanKapal->transit_label }}</span></dd></div>
                    @if($pergerakanKapal->transit)
                        <div><dt class="text-xs text-gray-500">Pelabuhan Transit</dt><dd class="mt-1 text-gray-900">{{ $pergerakanKapal->tujuan_transit ?: '-' }}</dd></div>
                        <div><dt class="text-xs text-gray-500">Voyage Transit</dt><dd class="mt-1 text-gray-900">{{ $pergerakanKapal->voyage_transit ?: '-' }}</dd></div>
                    @endif
                </dl>
            </section>

            <section class="rounded-lg bg-gray-50 p-4">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Jadwal</h2>
                <dl class="space-y-4">
                    <div><dt class="text-xs text-gray-500">Tanggal Sandar</dt><dd class="mt-1 text-gray-900">{{ $pergerakanKapal->tanggal_sandar?->format('d/m/Y H:i') ?: '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Tanggal Labuh</dt><dd class="mt-1 text-gray-900">{{ $pergerakanKapal->tanggal_labuh?->format('d/m/Y H:i') ?: '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Tanggal Berangkat</dt><dd class="mt-1 text-gray-900">{{ $pergerakanKapal->tanggal_berangkat?->format('d/m/Y H:i') ?: '-' }}</dd></div>
                </dl>
            </section>

            <section class="rounded-lg bg-gray-50 p-4">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Keterangan</h2>
                <p class="whitespace-pre-line text-sm leading-6 text-gray-700">{{ $pergerakanKapal->keterangan ?: '-' }}</p>
            </section>
        </div>
    </div>
</div>
@endsection
