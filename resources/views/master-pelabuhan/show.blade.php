@extends('layouts.app')

@section('title', 'Detail Pelabuhan')
@section('page_title', 'Detail Pelabuhan')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Detail Pelabuhan</h1>
                <p class="text-xs text-gray-600 mt-1">Informasi lengkap data pelabuhan</p>
            </div>
            <div class="flex gap-2">
                @if(auth()->user()->permissions->contains('name', 'master-pelabuhan-edit'))
                    <a href="{{ route('master-pelabuhan.edit', $masterPelabuhan) }}"
                       class="inline-flex items-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                @endif
                <a href="{{ route('master-pelabuhan.index') }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Status Badge -->
            <div class="mb-6">
                {!! $masterPelabuhan->status_badge !!}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Pelabuhan -->
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-500">Nama Pelabuhan</label>
                    <p class="text-base font-semibold text-gray-900">{{ $masterPelabuhan->nama_pelabuhan }}</p>
                </div>

                <!-- Kota -->
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-500">Kota</label>
                    <p class="text-base text-gray-900">{{ $masterPelabuhan->kota }}</p>
                </div>

                <!-- Status -->
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-500">Status</label>
                    <p class="text-base text-gray-900 capitalize">{{ $masterPelabuhan->status }}</p>
                </div>

                <!-- Dibuat -->
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-500">Dibuat</label>
                    <p class="text-base text-gray-900">{{ $masterPelabuhan->created_at->format('d M Y H:i') }}</p>
                </div>

                <!-- Diperbarui -->
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-500">Terakhir Diperbarui</label>
                    <p class="text-base text-gray-900">{{ $masterPelabuhan->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <!-- Keterangan -->
            @if($masterPelabuhan->keterangan)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <label class="text-sm font-medium text-gray-500 block mb-2">Keterangan</label>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $masterPelabuhan->keterangan }}</p>
                    </div>
                </div>
            @endif

            <!-- Related Information -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-500 mb-3">Informasi Terkait</h3>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-900">Data Master Pelabuhan</p>
                            <p class="text-sm text-blue-800 mt-1">
                                Pelabuhan ini dapat digunakan sebagai referensi pada modul pergerakan kapal
                                untuk pelabuhan asal, pelabuhan tujuan, dan pelabuhan transit.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons at Bottom -->
        @if(auth()->user()->permissions->contains('name', 'master-pelabuhan-delete'))
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Hapus data pelabuhan ini jika sudah tidak diperlukan</p>
                    </div>
                    <form action="{{ route('master-pelabuhan.destroy', $masterPelabuhan) }}" method="POST" class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelabuhan {{ $masterPelabuhan->nama_pelabuhan }}? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-3 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
