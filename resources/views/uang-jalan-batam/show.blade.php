@extends('layouts.app')

@section('title', 'Detail Uang Jalan Batam')
@section('page_title', 'Detail Uang Jalan Batam')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-xl font-bold text-gray-800">Detail Uang Jalan Batam</h2>
    <div class="flex space-x-2">
        <a href="{{ route('uang-jalan-batam.edit', $uangJalanBatam) }}" 
           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
        <a href="{{ route('uang-jalan-batam.index') }}" 
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Informasi Rute</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">Wilayah</label>
                <p class="mt-1 text-sm text-gray-900">{{ $uangJalanBatam->wilayah }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Rute</label>
                <p class="mt-1 text-sm text-gray-900">{{ $uangJalanBatam->rute }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Expedisi</label>
                <p class="mt-1 text-sm text-gray-900">{{ $uangJalanBatam->expedisi }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Ring</label>
                <p class="mt-1 text-sm text-gray-900">{{ $uangJalanBatam->ring }}</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Informasi Kontainer</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">FT</label>
                <p class="mt-1 text-sm text-gray-900">{{ $uangJalanBatam->ft }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">F/E</label>
                <p class="mt-1 text-sm text-gray-900">{{ $uangJalanBatam->f_e }}</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Informasi Tarif</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">Tarif</label>
                <p class="mt-1 text-lg font-semibold text-green-600">
                    Rp {{ number_format($uangJalanBatam->tarif, 0, ',', '.') }}
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Status</label>
                <div class="mt-1">
                    @if($uangJalanBatam->status)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($uangJalanBatam->status == 'aqua') bg-blue-100 text-blue-800
                            @elseif($uangJalanBatam->status == 'chasis PB') bg-green-100 text-green-800
                            @endif">
                            {{ $uangJalanBatam->status }}
                        </span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Tanggal Berlaku</label>
                <p class="mt-1 text-sm text-gray-900">{{ $uangJalanBatam->tanggal_berlaku->format('d F Y') }}</p>
                @if($uangJalanBatam->tanggal_berlaku->isPast())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                        Sudah Lewat
                    </span>
                @elseif($uangJalanBatam->tanggal_berlaku->isToday())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                        Hari Ini
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                        Aktif
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Informasi Sistem</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">Dibuat pada</label>
                <p class="mt-1 text-sm text-gray-900">{{ $uangJalanBatam->created_at->format('d F Y H:i:s') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Terakhir diubah</label>
                <p class="mt-1 text-sm text-gray-900">{{ $uangJalanBatam->updated_at->format('d F Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<div class="mt-6">
    <form method="POST" action="{{ route('uang-jalan-batam.destroy', $uangJalanBatam) }}" 
          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data uang jalan Batam ini?')" 
          class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-trash mr-2"></i>Hapus Data
        </button>
    </form>
</div>
@endsection