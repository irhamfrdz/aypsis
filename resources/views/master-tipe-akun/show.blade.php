@extends('layouts.app')

@section('title', 'Detail Tipe Akun')
@section('page_title', 'Detail Tipe Akun')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Tipe Akun</h1>
                    <p class="mt-1 text-sm text-gray-600">Informasi lengkap tipe akun</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('master.tipe-akun.edit', $tipeAkun) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('master.tipe-akun.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Detail Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">

                <!-- Tipe Akun -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tipe Akun</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $tipeAkun->tipe_akun }}</dd>
                </div>

                <!-- Catatan -->
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $tipeAkun->catatan ?? '-' }}</dd>
                </div>

                <!-- Created At -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $tipeAkun->created_at->format('d M Y H:i') }}</dd>
                </div>

                <!-- Updated At -->
                <div>
                    <dt class="text-sm font-medium text-gray-500">Diupdate Pada</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $tipeAkun->updated_at->format('d M Y H:i') }}</dd>
                </div>

            </dl>
        </div>

        <!-- Delete Section -->
        <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6 mt-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Hapus Tipe Akun</h3>
                    <p class="mt-1 text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan. Data akan dihapus secara permanen.</p>
                </div>
                <form method="POST" action="{{ route('master.tipe-akun.destroy', $tipeAkun) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tipe akun ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus Tipe Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
