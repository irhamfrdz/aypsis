
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Message -->
    <div>
        <h2 class="text-2xl font-semibold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h2>
        <p class="text-gray-500">Berikut adalah ringkasan aktivitas sistem Anda.</p>
    </div>

    <!-- Summary Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card 1: Total Permohonan -->
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Permohonan</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalPermohonan }}</p>
            </div>
            <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
        </div>

        <!-- Card 2: Total Karyawan -->
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Karyawan</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalKaryawan }}</p>
            </div>
            <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <!-- Card 3: Kontainer Tersedia -->
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Kontainer Tersedia</p>
                <p class="text-3xl font-bold text-gray-800">{{ $kontainerTersedia }}</p>
            </div>
            <div class="bg-green-100 text-green-600 p-3 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7l8 4"></path></svg>
            </div>
        </div>
    </div>
</div>
@endsection
