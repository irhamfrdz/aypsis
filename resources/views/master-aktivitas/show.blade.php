@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Detail Aktivitas</h1>
                <p class="text-xs text-gray-600 mt-1">Informasi detail aktivitas</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('master-aktivitas.edit', $aktivitas) }}"
                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('master-aktivitas.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm whitespace-nowrap">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Basic Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kode</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-white px-3 py-2 rounded border">{{ $aktivitas->kode }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                    {{ $aktivitas->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $aktivitas->status === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>
                    </div>

                    <div class="mt-4">
                        <dt class="text-sm font-medium text-gray-500">Nama Aktivitas</dt>
                        <dd class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 rounded border">{{ $aktivitas->nama_aktivitas }}</dd>
                    </div>

                    <div class="mt-4">
                        <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                        <dd class="mt-1 text-sm text-gray-900 bg-white px-3 py-2 rounded border min-h-[60px]">
                            {{ $aktivitas->catatan ?: 'Tidak ada catatan' }}
                        </dd>
                    </div>
                </div>

                <!-- System Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi System</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $aktivitas->created_at ? $aktivitas->created_at->format('d M Y, H:i') : '-' }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Terakhir Diupdate</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $aktivitas->updated_at ? $aktivitas->updated_at->format('d M Y, H:i') : '-' }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <form action="{{ route('master-aktivitas.destroy', $aktivitas) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-150"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                </form>
                
                <a href="{{ route('master-aktivitas.edit', $aktivitas) }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors duration-150">
                    Edit Data
                </a>
            </div>
        </div>
    </div>
</div>
@endsection