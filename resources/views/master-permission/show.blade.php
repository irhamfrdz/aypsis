@extends('layouts.app')

@section('title', 'Detail Permission')
@section('page_title', 'Detail Permission')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center">
                <a href="{{ route('master.permission.index') }}" class="mr-4 p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">Detail Permission</h1>
                    <p class="mt-1 text-sm text-gray-600">Informasi lengkap permission {{ $permission->name }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('master.permission.edit', $permission) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('master.permission.destroy', $permission) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permission ini?');">
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
                        <p class="mt-1 text-sm text-gray-600">Detail informasi permission</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Permission</label>
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-indigo-700">
                                            {{ strtoupper(substr($permission->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <span class="text-lg font-semibold text-gray-900">{{ $permission->name }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Guard Name</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $permission->guard_name }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg p-4 {{ $permission->description ? '' : 'text-gray-500 italic' }}">
                                {{ $permission->description ?: 'Tidak ada deskripsi' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ID Permission</label>
                                <span class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">{{ $permission->id }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat</label>
                                <span class="text-sm text-gray-900">{{ $permission->created_at->format('d M Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users with this Permission -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Pengguna dengan Permission Ini</h3>
                        <p class="mt-1 text-sm text-gray-600">Daftar pengguna yang memiliki permission ini</p>
                    </div>
                    <div class="p-6">
                        @if($permission->users->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($permission->users as $user)
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                            <span class="text-xs font-medium text-indigo-700">
                                                {{ strtoupper(substr($user->username, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $user->username }}</p>
                                            @if($user->karyawan)
                                                <p class="text-xs text-gray-500">{{ $user->karyawan->nama_lengkap }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Belum ada pengguna</h3>
                                <p class="text-sm text-gray-500">Permission ini belum diassign ke pengguna manapun.</p>
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
                        <p class="mt-1 text-sm text-gray-600">Ringkasan data permission</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Pengguna</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $permission->users->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Guard</span>
                            <span class="text-sm font-medium text-blue-600">{{ $permission->guard_name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Dibuat</span>
                            <span class="text-sm text-gray-900">{{ $permission->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Diupdate</span>
                            <span class="text-sm text-gray-900">{{ $permission->updated_at->format('d M Y') }}</span>
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
                        <a href="{{ route('master.permission.edit', $permission) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Permission
                        </a>
                        <a href="{{ route('master.permission.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
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
