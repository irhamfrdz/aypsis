@extends('layouts.app')

@php($editing = $suratJalan->exists)
@section('title', ($editing ? 'Ubah' : 'Tambah').' Surat Jalan Bongkaran Tanjung Pinang')

@section('content')
<div class="mx-auto max-w-5xl p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ $editing ? 'Ubah' : 'Tambah' }} Surat Jalan Bongkaran Tanjung Pinang</h1>
        <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.index') }}" class="text-sm text-teal-700 hover:underline">Kembali</a>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $editing ? route('surat-jalan-bongkaran-tanjung-pinang.update', $suratJalan->id) : route('surat-jalan-bongkaran-tanjung-pinang.store') }}" class="rounded border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @if($editing) @method('PUT') @endif
        <div class="grid gap-4 md:grid-cols-2">
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
                    <label for="{{ $field }}" class="mb-1 block text-sm font-medium text-gray-700">{{ $label }} @if(in_array($field, ['nomor_surat_jalan', 'tanggal_surat_jalan']))<span class="text-red-600">*</span>@endif</label>
                    <input id="{{ $field }}" name="{{ $field }}" type="{{ $field === 'tanggal_surat_jalan' ? 'date' : 'text' }}" value="{{ old($field, $field === 'tanggal_surat_jalan' ? $suratJalan->tanggal_surat_jalan?->format('Y-m-d') : $suratJalan->$field) }}" @if(in_array($field, ['nomor_surat_jalan', 'tanggal_surat_jalan'])) required @endif class="w-full rounded border-gray-300 text-sm @error($field) border-red-500 @enderror">
                </div>
            @endforeach
            <div class="md:col-span-2">
                <label for="aktifitas" class="mb-1 block text-sm font-medium text-gray-700">Aktivitas / Catatan</label>
                <textarea id="aktifitas" name="aktifitas" rows="3" class="w-full rounded border-gray-300 text-sm">{{ old('aktifitas', $suratJalan->aktifitas) }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <a href="{{ route('surat-jalan-bongkaran-tanjung-pinang.index') }}" class="rounded border px-4 py-2 text-sm">Batal</a>
            <button class="rounded bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
