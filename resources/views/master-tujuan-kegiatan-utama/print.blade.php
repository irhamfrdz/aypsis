@extends('layouts.app')

@section('title', 'Print Master Tujuan Kegiatan Utama')
@section('page_title', 'Print Master Tujuan Kegiatan Utama')

@section('content')
<style>
    @media print {
        .no-print { display: none; }
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background-color: #f0f0f0; }
    }
</style>

<div class="bg-white p-6">
    <div class="no-print mb-4">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Print Master Tujuan Kegiatan Utama</h2>
            <div class="space-x-2">
                <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <a href="{{ route('master.tujuan-kegiatan-utama.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="print-content">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold">MASTER TUJUAN KEGIATAN UTAMA</h1>
            <p class="text-gray-600">Dicetak pada: {{ date('d F Y, H:i') }}</p>
        </div>

        <table class="w-full border-collapse border border-gray-400">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-400 px-4 py-2 text-left">No</th>
                    <th class="border border-gray-400 px-4 py-2 text-left">Nama</th>
                    <th class="border border-gray-400 px-4 py-2 text-left">Deskripsi</th>
                    <th class="border border-gray-400 px-4 py-2 text-left">Status</th>
                    <th class="border border-gray-400 px-4 py-2 text-left">Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tujuanKegiatanUtamas as $index => $item)
                    <tr>
                        <td class="border border-gray-400 px-4 py-2">{{ $index + 1 }}</td>
                        <td class="border border-gray-400 px-4 py-2">{{ $item->nama }}</td>
                        <td class="border border-gray-400 px-4 py-2">{{ $item->deskripsi ?: '-' }}</td>
                        <td class="border border-gray-400 px-4 py-2">{{ $item->aktif ? 'Aktif' : 'Tidak Aktif' }}</td>
                        <td class="border border-gray-400 px-4 py-2">{{ $item->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border border-gray-400 px-4 py-2 text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-6 text-sm text-gray-600">
            <p>Total: {{ $tujuanKegiatanUtamas->count() }} data</p>
        </div>
    </div>
</div>
@endsection