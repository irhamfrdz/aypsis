@extends('layouts.app')

@section('title', 'Detail Rit Kenek Surat Jalan')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-900">Detail Rit Kenek</h1>
            <p class="mt-1 text-sm text-gray-500">Surat jalan {{ $suratJalan->no_surat_jalan ?? $suratJalan->nomor_surat_jalan }}</p>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5 px-6 py-6 text-sm">
            <div>
                <dt class="text-gray-500">Nomor Pranota</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $pranotaUangRitKenek->no_pranota }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Nomor Surat Jalan</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $suratJalan->no_surat_jalan ?? $suratJalan->nomor_surat_jalan }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Tanggal Surat Jalan</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $suratJalan->tanggal_surat_jalan ? \Carbon\Carbon::parse($suratJalan->tanggal_surat_jalan)->format('d/m/Y') : '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Kenek</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $kenekNama }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Kapal</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $kapal }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Voyage</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $voyage }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Nomor Kontainer</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $suratJalan->no_kontainer ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Biaya Rit Kenek Surat Jalan Ini</dt>
                <dd class="mt-1 text-lg font-bold text-indigo-700">Rp {{ number_format($biaya, 0, ',', '.') }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
