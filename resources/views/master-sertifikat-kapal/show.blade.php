@extends('layouts.app')

@section('title', 'Detail Sertifikat Kapal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('master-sertifikat-kapal.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                    Master Sertifikat Kapal
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">Detail Sertifikat</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-900">Detail Sertifikat Kapal</h1>
                <div class="flex space-x-2">
                    @can('master-sertifikat-kapal-update')
                    <a href="{{ route('master-sertifikat-kapal.edit', $master_sertifikat_kapal->id) }}" class="px-3 py-1 text-xs font-semibold text-white bg-yellow-500 rounded hover:bg-yellow-600 transition duration-200">
                        Edit
                    </a>
                    @endcan
                    <a href="{{ route('master-sertifikat-kapal.index') }}" class="px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition duration-200">
                        Kembali
                    </a>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Sertifikat</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $master_sertifikat_kapal->nama_sertifikat }}</p>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status</h3>
                        @if($master_sertifikat_kapal->status == 'aktif')
                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800">Aktif</span>
                        @else
                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Keterangan</h3>
                        <div class="p-3 bg-gray-50 rounded border border-gray-100 min-h-[100px]">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $master_sertifikat_kapal->keterangan ?? 'Tidak ada keterangan.' }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 grid grid-cols-2 gap-4 text-center">
                    <div>
                        <h3 class="text-[10px] font-bold text-gray-400 uppercase">Dibuat Pada</h3>
                        <p class="text-xs text-gray-600 font-medium">{{ $master_sertifikat_kapal->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <h3 class="text-[10px] font-bold text-gray-400 uppercase">Terakhir Diupdate</h3>
                        <p class="text-xs text-gray-600 font-medium">{{ $master_sertifikat_kapal->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
