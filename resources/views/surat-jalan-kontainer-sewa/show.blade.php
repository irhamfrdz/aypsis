@extends('layouts.app')

@section('title', 'Detail Surat Jalan ' . $suratJalan->nomor_surat_jalan)

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('surat-jalan-kontainer-sewa.index') }}" class="hover:text-cyan-600 transition">SJ Kontainer Sewa</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 font-medium">{{ $suratJalan->nomor_surat_jalan }}</li>
        </ol>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4 text-sm">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                @if($suratJalan->tipe === 'pengambilan')
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                        <i class="fas fa-truck-loading text-emerald-600"></i>
                    </div>
                @else
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-undo-alt text-orange-600"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-xl font-bold text-gray-800">{{ $suratJalan->nomor_surat_jalan }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $suratJalan->tipe_badge }}">
                            {{ $suratJalan->tipe_label }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $suratJalan->status_badge }}">
                            {{ $suratJalan->status_label }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('surat-jalan-kontainer-sewa.print', $suratJalan->id) }}" target="_blank" class="px-4 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700 transition">
                    <i class="fas fa-print mr-1"></i> Cetak
                </a>
                @if($suratJalan->status === 'aktif')
                    <form method="POST" action="{{ route('surat-jalan-kontainer-sewa.update-status', $suratJalan->id) }}" onsubmit="return confirm('Tandai sebagai selesai?')">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="selesai">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition">
                            <i class="fas fa-check mr-1"></i> Selesai
                        </button>
                    </form>
                @endif
                @if($suratJalan->status !== 'selesai' && $suratJalan->status !== 'batal')
                    <form method="POST" action="{{ route('surat-jalan-kontainer-sewa.update-status', $suratJalan->id) }}" onsubmit="return confirm('Batalkan surat jalan ini?')">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="batal">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition">
                            <i class="fas fa-ban mr-1"></i> Batalkan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Detail Info --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b"><i class="fas fa-info-circle text-cyan-600 mr-1"></i> Informasi Utama</h2>
            <table class="w-full text-sm">
                <tr><td class="py-1.5 text-gray-500 w-[140px]">Tanggal Surat Jalan</td><td class="py-1.5 font-medium">{{ $suratJalan->tanggal->format('d/m/Y') }}</td></tr>
                <tr><td class="py-1.5 text-gray-500">Vendor</td><td class="py-1.5 font-medium">{{ $suratJalan->vendor ?? '-' }}</td></tr>
                <tr><td class="py-1.5 text-gray-500">Supir</td><td class="py-1.5 font-medium">{{ $suratJalan->supir ?? '-' }}</td></tr>
                <tr><td class="py-1.5 text-gray-500">No. Plat</td><td class="py-1.5 font-medium">{{ $suratJalan->no_plat ?? '-' }}</td></tr>
                <tr><td class="py-1.5 text-gray-500">Antar Lokasi</td><td class="py-1.5 font-medium">{{ $suratJalan->antar_lokasi ? 'Ya' : 'Tidak' }}</td></tr>
                <tr><td class="py-1.5 text-gray-500">Tujuan</td><td class="py-1.5 font-medium">{{ $suratJalan->tujuan ?? '-' }}</td></tr>
                <tr><td class="py-1.5 text-gray-500">Nominal Uang Jalan</td><td class="py-1.5 font-medium">Rp {{ number_format($suratJalan->nominal_uang_jalan, 0, ',', '.') }}</td></tr>
                <tr><td class="py-1.5 text-gray-500 border-t pt-2 mt-2">Nomor Kontainer</td><td class="py-1.5 font-bold text-cyan-700 border-t pt-2 mt-2">{{ $suratJalan->nomor_kontainer }}</td></tr>
                <tr><td class="py-1.5 text-gray-500">Ukuran / Tipe</td><td class="py-1.5 font-medium">{{ $suratJalan->ukuran ?? '-' }} / {{ $suratJalan->tipe_kontainer ?? '-' }}</td></tr>
                <tr><td class="py-1.5 text-gray-500">Kondisi</td><td class="py-1.5 font-medium">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $suratJalan->kondisi === 'baik' ? 'bg-green-100 text-green-700' : ($suratJalan->kondisi === 'rusak_ringan' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        {{ match($suratJalan->kondisi) { 'baik' => 'Baik', 'rusak_ringan' => 'Rusak Ringan', 'rusak_berat' => 'Rusak Berat', default => ucfirst($suratJalan->kondisi) } }}
                    </span>
                </td></tr>
                <tr><td class="py-1.5 text-gray-500">Catatan Kondisi</td><td class="py-1.5 font-medium">{{ $suratJalan->catatan_kondisi ?? '-' }}</td></tr>
            </table>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b"><i class="fas fa-map-marker-alt text-cyan-600 mr-1"></i> Lokasi & Keterangan</h2>
            <table class="w-full text-sm">
                <tr><td class="py-1.5 text-gray-500 w-[140px]">Lokasi Ambil</td><td class="py-1.5 font-medium">{{ $suratJalan->lokasi_pengambilan ?? '-' }}</td></tr>
                <tr><td class="py-1.5 text-gray-500">Lokasi Kembali</td><td class="py-1.5 font-medium">{{ $suratJalan->lokasi_pengembalian ?? '-' }}</td></tr>
                <tr><td class="py-1.5 text-gray-500">Keterangan</td><td class="py-1.5 font-medium">{{ $suratJalan->keterangan ?? '-' }}</td></tr>
                <tr><td class="py-1.5 text-gray-500">Vendor Item</td><td class="py-1.5 font-medium">{{ $suratJalan->vendor_item ?? '-' }}</td></tr>
                <tr><td class="py-1.5 text-gray-500">Dibuat oleh</td><td class="py-1.5 font-medium">{{ $suratJalan->createdByUser->name ?? '-' }}</td></tr>
            </table>
        </div>
    </div>
</div>
@endsection
