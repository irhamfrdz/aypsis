@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Message for Users Without Permissions -->
    <div class="text-center py-12">
        <div class="max-w-2xl mx-auto">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-8 text-white">
                <div class="flex justify-center mb-6">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold mb-4">Selamat Datang di AYP SISTEM</h1>
                <p class="text-xl opacity-90 mb-6">Sistem Manajemen Terpadu</p>
                <div class="bg-white bg-opacity-20 rounded-lg p-6">
                    <p class="text-lg">
                        Anda telah berhasil login ke dalam sistem. Saat ini akun Anda sedang dalam proses setup permission akses.
                    </p>
                    <p class="text-sm mt-4 opacity-80">
                        Silakan hubungi administrator untuk mengatur permission akses Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Akun</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Nama Lengkap</label>
                    <p class="text-gray-900">{{ Auth::user()->karyawan->nama_lengkap ?? Auth::user()->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Username</label>
                    <p class="text-gray-900">{{ Auth::user()->username }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Email</label>
                    <p class="text-gray-900">{{ Auth::user()->karyawan->email ?? Auth::user()->email ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">NIK</label>
                    <p class="text-gray-900">{{ Auth::user()->karyawan->nik ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Divisi</label>
                    <p class="text-gray-900">{{ Auth::user()->karyawan->divisi ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Pekerjaan</label>
                    <p class="text-gray-900">{{ Auth::user()->karyawan->pekerjaan ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">No. HP</label>
                    <p class="text-gray-900">{{ Auth::user()->karyawan->no_hp ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Menunggu Setup Permission
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Card -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-800">Butuh Bantuan?</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        Jika Anda mengalami kesulitan atau memiliki pertanyaan tentang sistem ini,
                        silakan hubungi administrator atau tim IT untuk mendapatkan bantuan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
