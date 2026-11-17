@extends('layouts.app')

@php($editing = $suratJalan->exists)
@section('title', ($editing ? 'Ubah' : 'Tambah').' Surat Jalan Bongkaran Tanjung Pinang')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-5 md:px-6">
<div class="mx-auto max-w-5xl">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div><div class="text-xs font-semibold uppercase tracking-[.18em] text-teal-700">Operasional · Tanjung Pinang</div><h1 class="mt-1 text-2xl font-bold text-slate-900">{{ $editing ? 'Ubah' : 'Tambah' }} Surat Jalan Bongkaran</h1><p class="mt-1 text-sm text-slate-500">Lengkapi informasi kapal, muatan, kendaraan, dan tujuan.</p></div>
        <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Kembali ke daftar</a>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $editing ? route('surat-jalan-bongkaran-tanjung-pinang.update', $suratJalan->id) : route('surat-jalan-bongkaran-tanjung-pinang.store') }}" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        @csrf
        @if($editing) @method('PUT') @endif
        <div class="border-b border-slate-200 bg-slate-800 px-5 py-3 text-sm font-semibold text-white">Informasi surat jalan</div>
        <div class="grid gap-4 p-5 md:grid-cols-2">
            @foreach([
                'nomor_surat_jalan' => 'Nomor Surat Jalan', 'tanggal_surat_jalan' => 'Tanggal Surat Jalan',
                'nama_kapal' => 'Nama Kapal', 'no_voyage' => 'Nomor Voyage', 'no_bl' => 'Nomor BL',
                'pengirim' => 'Pengirim', 'penerima' => 'Penerima', 'jenis_barang' => 'Jenis Barang',
                'tujuan_pengambilan' => 'Tujuan Pengambilan', 'tujuan_pengiriman' => 'Tujuan Pengiriman',
                'supir' => 'Supir', 'no_plat' => 'Nomor Plat', 'kenek' => 'Kenek',
                'no_kontainer' => 'Nomor Kontainer', 'no_seal' => 'Nomor Seal', 'size' => 'Ukuran Kontainer',
                'tipe_kontainer' => 'Tipe Kontainer', 'term' => 'Term',
            ] as $field => $label)
                <div>
                    <label for="{{ $field }}" class="mb-1 block text-sm font-medium text-slate-700">{{ $label }} @if(in_array($field, ['nomor_surat_jalan', 'tanggal_surat_jalan']))<span class="text-red-600">*</span>@endif</label>
                    <input id="{{ $field }}" name="{{ $field }}" type="{{ $field === 'tanggal_surat_jalan' ? 'date' : 'text' }}" value="{{ old($field, $field === 'tanggal_surat_jalan' ? $suratJalan->tanggal_surat_jalan?->format('Y-m-d') : $suratJalan->$field) }}" @if(in_array($field, ['nomor_surat_jalan', 'tanggal_surat_jalan'])) required @endif class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-600 focus:ring-teal-600 @error($field) border-red-500 @enderror">
                </div>
            @endforeach
            <div class="md:col-span-2">
                <label for="aktifitas" class="mb-1 block text-sm font-medium text-slate-700">Aktivitas / Catatan</label>
                <textarea id="aktifitas" name="aktifitas" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-600 focus:ring-teal-600">{{ old('aktifitas', $suratJalan->aktifitas) }}</textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4">
            <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm">Batal</a>
            <button class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">Simpan Surat Jalan</button>
        </div>
    </form>
</div>
</div>
@endsection
