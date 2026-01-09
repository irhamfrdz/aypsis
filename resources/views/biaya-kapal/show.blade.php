@extends('layouts.app')

@section('title', 'Detail Biaya Kapal')
@section('page_title', 'Detail Biaya Kapal')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Detail Biaya Kapal</h2>
        <div class="flex space-x-2">
            @can('biaya-kapal-update')
            <a href="{{ route('biaya-kapal.edit', $biayaKapal->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            @endcan
            <a href="{{ route('biaya-kapal.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal</label>
                <p class="text-lg font-semibold text-gray-900">{{ $biayaKapal->tanggal->format('d/m/Y') }}</p>
            </div>

            @if($biayaKapal->nomor_referensi)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Referensi</label>
                <p class="text-lg font-semibold text-gray-900">{{ $biayaKapal->nomor_referensi }}</p>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Invoice</label>
                <p class="text-lg font-semibold text-gray-900">{{ $biayaKapal->nomor_invoice }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Kapal</label>
                @php
                    $namaKapals = is_array($biayaKapal->nama_kapal) ? $biayaKapal->nama_kapal : [$biayaKapal->nama_kapal];
                @endphp
                <div class="flex flex-wrap gap-2">
                    @foreach($namaKapals as $kapal)
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">{{ $kapal }}</span>
                    @endforeach
                </div>
            </div>

            @if($biayaKapal->no_voyage && count($biayaKapal->no_voyage) > 0)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Voyage</label>
                @php
                    $noVoyages = is_array($biayaKapal->no_voyage) ? $biayaKapal->no_voyage : [$biayaKapal->no_voyage];
                @endphp
                <div class="flex flex-wrap gap-2">
                    @foreach($noVoyages as $voyage)
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">{{ $voyage }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Biaya</label>
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                    {{ $biayaKapal->jenis_biaya_label }}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nominal</label>
                <p class="text-2xl font-bold text-green-600">{{ $biayaKapal->formatted_nominal }}</p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Keterangan</label>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-900">{{ $biayaKapal->keterangan ?: '-' }}</p>
                </div>
            </div>

            @if($biayaKapal->bukti)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-2">Bukti</label>
                @if($biayaKapal->bukti_foto)
                    <a href="{{ $biayaKapal->bukti_foto }}" target="_blank" class="block">
                        <img src="{{ $biayaKapal->bukti_foto }}" alt="Bukti" class="max-w-full h-auto rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    </a>
                @elseif($biayaKapal->bukti_pdf)
                    <a href="{{ $biayaKapal->bukti_pdf }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Lihat PDF
                    </a>
                @else
                    <a href="{{ asset('storage/' . $biayaKapal->bukti) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download File
                    </a>
                @endif
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat</label>
                <p class="text-sm text-gray-700">{{ $biayaKapal->created_at->format('d/m/Y H:i') }}</p>
            </div>

            @if($biayaKapal->updated_at != $biayaKapal->created_at)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diubah</label>
                <p class="text-sm text-gray-700">{{ $biayaKapal->updated_at->format('d/m/Y H:i') }}</p>
            </div>
            @endif
        </div>
    </div>

    @can('biaya-kapal-delete')
    <div class="mt-8 pt-6 border-t border-gray-200">
        <form action="{{ route('biaya-kapal.destroy', $biayaKapal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Hapus Data
            </button>
        </form>
    </div>
    @endcan
</div>
@endsection
