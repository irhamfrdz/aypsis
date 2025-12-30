@extends('layouts.app')

@section('title', 'Checkpoint Kontainer Masuk - Pilih Cabang')
@section('page_title', 'Checkpoint Kontainer Masuk')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-50 py-6 sm:py-12 px-3 sm:px-4 lg:px-8">
    <div class="max-w-6xl mx-auto">
        
        <!-- Header Section -->
        <div class="text-center mb-8 sm:mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl shadow-lg mb-4 sm:mb-6">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2 sm:mb-3 px-4">Checkpoint Kontainer Masuk</h1>
            <p class="text-sm sm:text-base lg:text-lg text-gray-600 px-4">Pilih cabang untuk melakukan checkpoint kontainer masuk</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-8 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-8 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Branch Selection Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8 sm:mb-12">
            
            <!-- Jakarta Card -->
            <a href="{{ route('checkpoint-kontainer-masuk.checkpoint', 'jakarta') }}" 
               class="group relative bg-white rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl active:shadow-xl transition-all duration-300 overflow-hidden border-2 border-transparent hover:border-green-400 active:scale-95 sm:active:scale-100 sm:hover:-translate-y-1 touch-manipulation">
                <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-br from-green-100 to-green-50 rounded-bl-full opacity-50"></div>
                <div class="relative p-5 sm:p-8">
                    <div class="flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-xl shadow-lg mb-4 sm:mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1 sm:mb-2 group-hover:text-green-600 transition-colors">Jakarta</h3>
                    <p class="text-gray-600 text-xs sm:text-sm mb-4 sm:mb-0">Checkpoint kontainer masuk Jakarta</p>
                    <div class="mt-4 sm:mt-6 flex items-center text-green-600 font-medium text-sm sm:text-base">
                        <span>Pilih Cabang</span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Batam Card -->
            <a href="{{ route('checkpoint-kontainer-masuk.checkpoint', 'batam') }}" 
               class="group relative bg-white rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl active:shadow-xl transition-all duration-300 overflow-hidden border-2 border-transparent hover:border-teal-400 active:scale-95 sm:active:scale-100 sm:hover:-translate-y-1 touch-manipulation">
                <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-br from-teal-100 to-teal-50 rounded-bl-full opacity-50"></div>
                <div class="relative p-5 sm:p-8">
                    <div class="flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-teal-400 to-teal-600 rounded-xl shadow-lg mb-4 sm:mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1 sm:mb-2 group-hover:text-teal-600 transition-colors">Batam</h3>
                    <p class="text-gray-600 text-xs sm:text-sm mb-4 sm:mb-0">Checkpoint kontainer masuk Batam</p>
                    <div class="mt-4 sm:mt-6 flex items-center text-teal-600 font-medium text-sm sm:text-base">
                        <span>Pilih Cabang</span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Tanjung Pinang Card -->
            <a href="{{ route('checkpoint-kontainer-masuk.checkpoint', 'tanjung-pinang') }}" 
               class="group relative bg-white rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl active:shadow-xl transition-all duration-300 overflow-hidden border-2 border-transparent hover:border-emerald-400 active:scale-95 sm:active:scale-100 sm:hover:-translate-y-1 touch-manipulation sm:col-span-2 lg:col-span-1">
                <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-bl-full opacity-50"></div>
                <div class="relative p-5 sm:p-8">
                    <div class="flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl shadow-lg mb-4 sm:mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1 sm:mb-2 group-hover:text-emerald-600 transition-colors">Tanjung Pinang</h3>
                    <p class="text-gray-600 text-xs sm:text-sm mb-4 sm:mb-0">Checkpoint kontainer masuk Tanjung Pinang</p>
                    <div class="mt-4 sm:mt-6 flex items-center text-emerald-600 font-medium text-sm sm:text-base">
                        <span>Pilih Cabang</span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                </div>
            </a>

        </div>

        <!-- Quick Actions -->
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
            <a href="{{ route('checkpoint-kontainer-masuk.history') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-white border-2 border-green-200 text-green-700 text-sm font-medium rounded-xl hover:bg-green-50 hover:border-green-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-sm hover:shadow-md active:scale-95 touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Lihat History
            </a>
        </div>

    </div>
</div>
@endsection
