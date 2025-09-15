@extends('layouts.app')

@section('title', 'Akses Ditolak - Izin Tidak Cukup')
@section('page_title', 'Akses Ditolak')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl rounded-lg sm:px-10">
            <!-- Error Icon -->
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-6">
                    <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    Akses Ditolak
                </h2>

                <p class="text-gray-600 mb-6">
                    Anda tidak memiliki izin yang diperlukan untuk mengakses halaman ini.
                </p>
            </div>

            <!-- Permission Details -->
            @if(isset($requiredPermission))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293zM9 4a1 1 0 012 0v2H9V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-red-800">
                                Izin yang Diperlukan
                            </h3>
                            <div class="mt-1 text-sm text-red-700">
                                <code class="bg-red-100 px-2 py-1 rounded text-xs font-mono">
                                    {{ $requiredPermission }}
                                </code>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- User Info -->
            @if(auth()->check())
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-blue-800">
                                Informasi Akun
                            </h3>
                            <div class="mt-1 text-sm text-blue-700">
                                <p><strong>Username:</strong> {{ auth()->user()->username }}</p>
                                @if(auth()->user()->karyawan)
                                    <p><strong>Nama:</strong> {{ auth()->user()->karyawan->nama_lengkap }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Available Permissions (if any) -->
            @if(auth()->check() && isset($userPermissions) && count($userPermissions) > 0)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-green-800">
                                Izin yang Anda Miliki
                            </h3>
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach($userPermissions as $permission)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $permission }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="space-y-3">
                <!-- Go Back Button -->
                <button onclick="history.back()"
                        class="w-full flex justify-center items-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Halaman Sebelumnya
                </button>

                <!-- Go to Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="w-full flex justify-center items-center px-4 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                    </svg>
                    Kembali ke Dashboard
                </a>

                <!-- Contact Admin (if not admin) -->
                @if(auth()->check() && !auth()->user()->hasRole('admin'))
                    <div class="text-center pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-2">
                            Membutuhkan akses? Hubungi administrator sistem
                        </p>
                        <button onclick="alert('Silakan hubungi administrator untuk mendapatkan izin yang diperlukan.')"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Hubungi Admin
                        </button>
                    </div>
                @endif
            </div>

            <!-- Footer Info -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    Jika Anda merasa ini adalah kesalahan, silakan laporkan ke tim IT.
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    Error Code: 403 - Forbidden
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .permission-tag {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        margin: 0.125rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 0.25rem;
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .required-permission {
        background-color: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }
</style>
@endpush
