@extends('layouts.app')

@section('title', 'Welcome')
@section('page_title', 'Welcome')

@section('content')
<div class="space-y-8">
    <div class="text-center py-12">
        <div class="max-w-2xl mx-auto">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-8 text-white">
                <div class="flex justify-center mb-6">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold mb-4">Selamat Datang di AYPSIS</h1>
                <p class="text-xl opacity-90 mb-6">Sistem Manajemen Terpadu</p>
                <div class="bg-white bg-opacity-20 rounded-lg p-6">
                    <p class="text-lg">
                        Anda telah berhasil login ke dalam sistem.
                    </p>
                    <p class="text-sm mt-4 opacity-80">
                        Silakan pilih menu di sebelah kiri untuk mulai menggunakan sistem sesuai dengan hak akses Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
