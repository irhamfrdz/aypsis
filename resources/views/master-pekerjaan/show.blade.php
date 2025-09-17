@extends('layouts.app')

@section('title', 'Detail Pekerjaan')
@section('page_title', 'Detail Pekerjaan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center">
                <a href="{{ route('master.pekerjaan.index') }}" class="mr-4 p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">Detail Pekerjaan</h1>
                    <p class="mt-1 text-sm text-gray-600">Informasi lengkap pekerjaan {{ $pekerjaan->nama_pekerjaan }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('master.pekerjaan.edit', $pekerjaan) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('master.pekerjaan.destroy', $pekerjaan) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pekerjaan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Column - Basic Info -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Dasar</h3>
                        <p class="mt-1 text-sm text-gray-600">Detail informasi pekerjaan</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pekerjaan</label>
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-indigo-700">
                                            {{ strtoupper(substr($pekerjaan->nama_pekerjaan, 0, 1)) }}
                                        </span>
                                    </div>
                                    <span class="text-lg font-semibold text-gray-900">{{ $pekerjaan->nama_pekerjaan }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pekerjaan</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $pekerjaan->kode_pekerjaan }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    {{ $pekerjaan->divisi }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                @if($pekerjaan->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9a1 1 0 012 0v4a1 1 0 01-2 0V9zm0 6a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                                        </svg>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg p-4 {{ $pekerjaan->deskripsi ? '' : 'text-gray-500 italic' }}">
                                {{ $pekerjaan->deskripsi ?: 'Tidak ada deskripsi' }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ID Pekerjaan</label>
                            <span class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">{{ $pekerjaan->id }}</span>
                        </div>
                    </div>
                </div>

                <!-- Karyawan dengan Pekerjaan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Karyawan dengan Pekerjaan Ini</h3>
                        <p class="mt-1 text-sm text-gray-600">Daftar karyawan yang memiliki pekerjaan ini</p>
                    </div>
                    <div class="p-6">
                        @if($pekerjaan->karyawans->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($pekerjaan->karyawans as $karyawan)
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                            <span class="text-xs font-medium text-indigo-700">
                                                {{ strtoupper(substr($karyawan->nama_lengkap, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $karyawan->nama_lengkap }}</p>
                                            <p class="text-xs text-gray-500">{{ $karyawan->jabatan }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Belum ada karyawan</h3>
                                <p class="text-sm text-gray-500">Pekerjaan ini belum memiliki karyawan yang terdaftar.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Right Column - Statistics -->
            <div class="space-y-6">

                <!-- Statistics Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Statistik</h3>
                        <p class="mt-1 text-sm text-gray-600">Ringkasan data pekerjaan</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Karyawan</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $pekerjaan->karyawans->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Divisi</span>
                            <span class="text-sm font-medium text-purple-600">{{ $pekerjaan->divisi }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Status</span>
                            <span class="text-sm font-medium {{ $pekerjaan->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $pekerjaan->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Dibuat</span>
                            <span class="text-sm text-gray-900">{{ $pekerjaan->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Diupdate</span>
                            <span class="text-sm text-gray-900">{{ $pekerjaan->updated_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Aksi Cepat</h3>
                        <p class="mt-1 text-sm text-gray-600">Operasi umum</p>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('master.pekerjaan.edit', $pekerjaan) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Pekerjaan
                        </a>
                        <a href="{{ route('master.pekerjaan.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
