@extends('layouts.app')

@section('title', 'Checklist Kelengkapan Crew - ' . $karyawan->nama_lengkap)
@section('page_title', 'Checklist Kelengkapan Crew')

@section('content')
<!-- Header Section with Simple Design -->
<div class="bg-blue-600 shadow-lg rounded-lg p-6 mb-6 text-white">
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0">
        <div class="flex items-center space-x-4">
            <div class="bg-white/20 rounded-lg p-3">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold">Checklist Kelengkapan Crew</h1>
                <p class="text-blue-100">Verifikasi Dokumen dan Sertifikat ABK</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('master.karyawan.crew-checklist.print', $karyawan->id) }}" target="_blank"
               class="bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak</span>
            </a>
            <a href="{{ route('master.karyawan.index') }}"
               class="bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>
</div>

<!-- Employee Info Card with Simple Design -->
<div class="bg-white shadow-lg rounded-lg p-6 mb-6">
    <div class="flex items-center space-x-4 mb-6">
        <div class="bg-blue-100 rounded-lg p-3">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-xl font-bold text-gray-800">Informasi Karyawan</h3>
            <p class="text-gray-600">Data crew yang akan diverifikasi</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Nama Lengkap</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $karyawan->nama_lengkap }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center space-x-3">
                <div class="bg-green-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">NIK</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $karyawan->nik }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center space-x-3">
                <div class="bg-purple-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Divisi</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $karyawan->divisi }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center space-x-3">
                <div class="bg-orange-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V6m8 0h2a2 2 0 012 2v6a2 2 0 01-2 2h-2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Pekerjaan</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $karyawan->pekerjaan }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center space-x-3">
                <div class="bg-indigo-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">No. HP</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $karyawan->no_hp ?: '-' }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center space-x-3">
                <div class="bg-red-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Tanggal Verifikasi</p>
                    <p class="text-lg font-semibold text-gray-800">{{ now()->format('d/M/Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Messages with Simple Design -->
@if (session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    </div>
@endif

<!-- Main Form with Simple Design -->
<div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="bg-blue-100 rounded-lg p-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Formulir Checklist Kelengkapan</h3>
                <p class="text-sm text-gray-600">Lengkapi status dokumen dan sertifikat crew ABK</p>
            </div>
        </div>
    </div>

    <form action="{{ route('master.karyawan.crew-checklist.update', $karyawan->id) }}" method="POST" class="p-0">
        @csrf

        <!-- Simple Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Kelengkapan Dokumen</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Ada</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Tidak</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Nomor Sertifikat</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Tanggal Terbit</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Tanggal Expired</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($checklistItems as $index => $item)
                        <tr class="hover:bg-gray-50 {{ $item->is_expired ? 'bg-red-50' : ($item->is_expiring_soon ? 'bg-yellow-50' : '') }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="bg-blue-100 text-blue-800 text-sm font-bold rounded-full w-8 h-8 flex items-center justify-center mx-auto">
                                    {{ $index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    <div class="font-semibold text-gray-900">{{ $item->item_name }}</div>
                                    @if($item->item_name == 'BST (Basic Safety Training)')
                                        <div class="text-sm text-gray-600 bg-blue-50 p-3 rounded-lg border-l-4 border-blue-400">
                                            <strong>BST:</strong> Sertifikat dasar keselamatan pelaut untuk menghadapi bahaya di atas kapal, wajib dimiliki semua tingkat jabatan.
                                        </div>
                                    @elseif($item->item_name == 'SCRB (Survival Craft and Rescue Boat)')
                                        <div class="text-sm text-gray-600 bg-green-50 p-3 rounded-lg border-l-4 border-green-400">
                                            <strong>SCRB:</strong> Pelatihan penggunaan sekoci dan perahu penyelamat dalam keadaan darurat di laut.
                                        </div>
                                    @elseif($item->item_name == 'AFF (Advanced Fire Fighting)')
                                        <div class="text-sm text-gray-600 bg-red-50 p-3 rounded-lg border-l-4 border-red-400">
                                            <strong>AFF:</strong> Pelatihan pemadaman kebakaran tingkat lanjut.
                                        </div>
                                    @elseif($item->item_name == 'MFA (Medical First Aid)')
                                        <div class="text-sm text-gray-600 bg-purple-50 p-3 rounded-lg border-l-4 border-purple-400">
                                            <strong>MFA:</strong> Pelatihan pertolongan pertama medis di kapal.
                                        </div>
                                    @elseif($item->item_name == 'SAT (Security Awareness Training)')
                                        <div class="text-sm text-gray-600 bg-orange-50 p-3 rounded-lg border-l-4 border-orange-400">
                                            <strong>SAT:</strong> Pelatihan kesadaran keamanan kapal sesuai ISPS Code dan STCW Amandemen 2010.
                                        </div>
                                    @elseif($item->item_name == 'SDSD (Seafarer with Designated Security Duties)')
                                        <div class="text-sm text-gray-600 bg-indigo-50 p-3 rounded-lg border-l-4 border-indigo-400">
                                            <strong>SDSD:</strong> Pelatihan untuk pelaut yang ditunjuk menjalankan tugas keamanan kapal sesuai ISPS Code dan STCW Amandemen 2010.
                                        </div>
                                    @elseif($item->item_name == 'ERM (Engine Room Resource Management)')
                                        <div class="text-sm text-gray-600 bg-yellow-50 p-3 rounded-lg border-l-4 border-yellow-400">
                                            <strong>ERM:</strong> Manajemen sumber daya di ruang mesin.
                                        </div>
                                    @elseif($item->item_name == 'BRM (Bridge Resource Management)')
                                        <div class="text-sm text-gray-600 bg-teal-50 p-3 rounded-lg border-l-4 border-teal-400">
                                            <strong>BRM:</strong> Pelatihan manajemen sumber daya di anjungan kapal.
                                        </div>
                                    @elseif($item->item_name == 'MC (Medical Care)')
                                        <div class="text-sm text-gray-600 bg-pink-50 p-3 rounded-lg border-l-4 border-pink-400">
                                            <strong>MC:</strong> Pelatihan lanjutan untuk penanganan medis di kapal.
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <label class="inline-flex items-center justify-center w-12 h-12 rounded-full border-2 cursor-pointer transition-all duration-200 {{ $item->status == 'ada' ? 'bg-green-500 border-green-500 text-white' : 'bg-white border-gray-300 hover:border-green-400' }}">
                                    <input type="radio" name="checklist[{{ $item->id }}][status]" value="ada"
                                           {{ $item->status == 'ada' ? 'checked' : '' }}
                                           class="sr-only">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </label>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <label class="inline-flex items-center justify-center w-12 h-12 rounded-full border-2 cursor-pointer transition-all duration-200 {{ $item->status == 'tidak' ? 'bg-red-500 border-red-500 text-white' : 'bg-white border-gray-300 hover:border-red-400' }}">
                                    <input type="radio" name="checklist[{{ $item->id }}][status]" value="tidak"
                                           {{ $item->status == 'tidak' ? 'checked' : '' }}
                                           class="sr-only">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </label>
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="checklist[{{ $item->id }}][nomor_sertifikat]"
                                       value="{{ $item->nomor_sertifikat }}"
                                       placeholder="Masukkan nomor sertifikat"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </td>
                            <td class="px-6 py-4">
                                <input type="date" name="checklist[{{ $item->id }}][issued_date]"
                                       value="{{ $item->issued_date ? $item->issued_date->format('Y-m-d') : '' }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    <input type="date" name="checklist[{{ $item->id }}][expired_date]"
                                           value="{{ $item->expired_date ? $item->expired_date->format('Y-m-d') : '' }}"
                                           class="block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 text-sm {{ $item->is_expired ? 'border-red-400 bg-red-50 focus:ring-red-500 focus:border-red-500' : ($item->is_expiring_soon ? 'border-yellow-400 bg-yellow-50 focus:ring-yellow-500 focus:border-yellow-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500') }}">
                                    @if($item->is_expired)
                                        <div class="flex items-center space-x-2 text-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-xs font-semibold">Sudah Expired</span>
                                        </div>
                                    @elseif($item->is_expiring_soon)
                                        <div class="flex items-center space-x-2 text-yellow-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-xs font-semibold">Akan Expired dalam 30 hari</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <textarea name="checklist[{{ $item->id }}][catatan]"
                                          placeholder="Tambahkan catatan jika diperlukan"
                                          rows="2"
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm resize-none">{{ $item->catatan }}</textarea>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4 p-4">
            @foreach($checklistItems as $index => $item)
                <div class="bg-white border rounded-xl shadow-md overflow-hidden {{ $item->is_expired ? 'border-red-300 bg-red-50' : ($item->is_expiring_soon ? 'border-yellow-300 bg-yellow-50' : 'border-gray-200') }}">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 rounded-full w-8 h-8 flex items-center justify-center">
                                    <span class="text-sm font-bold">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-base">{{ $item->item_name }}</h4>
                                    @if($item->is_expired)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-500 text-white">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Expired
                                        </span>
                                    @elseif($item->is_expiring_soon)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-500 text-white">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Akan Expired
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="p-4 space-y-4">
                        <!-- Description -->
                        @if($item->item_name == 'BST (Basic Safety Training)')
                            <div class="text-sm text-gray-600 bg-blue-50 p-3 rounded-lg border-l-4 border-blue-400">
                                <strong>BST:</strong> Sertifikat dasar keselamatan pelaut untuk menghadapi bahaya di atas kapal, wajib dimiliki semua tingkat jabatan.
                            </div>
                        @elseif($item->item_name == 'SCRB (Survival Craft and Rescue Boat)')
                            <div class="text-sm text-gray-600 bg-green-50 p-3 rounded-lg border-l-4 border-green-400">
                                <strong>SCRB:</strong> Pelatihan penggunaan sekoci dan perahu penyelamat dalam keadaan darurat di laut.
                            </div>
                        @elseif($item->item_name == 'AFF (Advanced Fire Fighting)')
                            <div class="text-sm text-gray-600 bg-red-50 p-3 rounded-lg border-l-4 border-red-400">
                                <strong>AFF:</strong> Pelatihan pemadaman kebakaran tingkat lanjut.
                            </div>
                        @elseif($item->item_name == 'MFA (Medical First Aid)')
                            <div class="text-sm text-gray-600 bg-purple-50 p-3 rounded-lg border-l-4 border-purple-400">
                                <strong>MFA:</strong> Pelatihan pertolongan pertama medis di kapal.
                            </div>
                        @elseif($item->item_name == 'SAT (Security Awareness Training)')
                            <div class="text-sm text-gray-600 bg-orange-50 p-3 rounded-lg border-l-4 border-orange-400">
                                <strong>SAT:</strong> Pelatihan kesadaran keamanan kapal sesuai ISPS Code dan STCW Amandemen 2010.
                            </div>
                        @elseif($item->item_name == 'SDSD (Seafarer with Designated Security Duties)')
                            <div class="text-sm text-gray-600 bg-indigo-50 p-3 rounded-lg border-l-4 border-indigo-400">
                                <strong>SDSD:</strong> Pelatihan untuk pelaut yang ditunjuk menjalankan tugas keamanan kapal sesuai ISPS Code dan STCW Amandemen 2010.
                            </div>
                        @elseif($item->item_name == 'ERM (Engine Room Resource Management)')
                            <div class="text-sm text-gray-600 bg-yellow-50 p-3 rounded-lg border-l-4 border-yellow-400">
                                <strong>ERM:</strong> Manajemen sumber daya di ruang mesin.
                            </div>
                        @elseif($item->item_name == 'BRM (Bridge Resource Management)')
                            <div class="text-sm text-gray-600 bg-teal-50 p-3 rounded-lg border-l-4 border-teal-400">
                                <strong>BRM:</strong> Pelatihan manajemen sumber daya di anjungan kapal.
                            </div>
                        @elseif($item->item_name == 'MC (Medical Care)')
                            <div class="text-sm text-gray-600 bg-pink-50 p-3 rounded-lg border-l-4 border-pink-400">
                                <strong>MC:</strong> Pelatihan lanjutan untuk penanganan medis di kapal.
                            </div>
                        @endif

                        <!-- Status Selection -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">Status Dokumen</label>
                            <div class="flex space-x-4">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="checklist[{{ $item->id }}][status]" value="ada"
                                           {{ $item->status == 'ada' ? 'checked' : '' }}
                                           class="sr-only mobile-radio">
                                    <div class="flex items-center justify-center p-4 border-2 rounded-xl transition-all duration-200 mobile-status-ada {{ $item->status == 'ada' ? 'bg-green-500 border-green-500 text-white' : 'bg-white border-gray-300 hover:border-green-400' }}">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="font-semibold">ADA</span>
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="checklist[{{ $item->id }}][status]" value="tidak"
                                           {{ $item->status == 'tidak' ? 'checked' : '' }}
                                           class="sr-only mobile-radio">
                                    <div class="flex items-center justify-center p-4 border-2 rounded-xl transition-all duration-200 mobile-status-tidak {{ $item->status == 'tidak' ? 'bg-red-500 border-red-500 text-white' : 'bg-white border-gray-300 hover:border-red-400' }}">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span class="font-semibold">TIDAK</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Certificate Details -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Sertifikat</label>
                                <input type="text" name="checklist[{{ $item->id }}][nomor_sertifikat]"
                                       value="{{ $item->nomor_sertifikat }}"
                                       placeholder="Masukkan nomor sertifikat"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all duration-200">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Terbit</label>
                                    <input type="date" name="checklist[{{ $item->id }}][issued_date]"
                                           value="{{ $item->issued_date ? $item->issued_date->format('Y-m-d') : '' }}"
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Expired</label>
                                    <input type="date" name="checklist[{{ $item->id }}][expired_date]"
                                           value="{{ $item->expired_date ? $item->expired_date->format('Y-m-d') : '' }}"
                                           class="block w-full px-4 py-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 text-sm transition-all duration-200 {{ $item->is_expired ? 'border-red-400 bg-red-50 focus:ring-red-500 focus:border-red-500' : ($item->is_expiring_soon ? 'border-yellow-400 bg-yellow-50 focus:ring-yellow-500 focus:border-yellow-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500') }}">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                <textarea name="checklist[{{ $item->id }}][catatan]"
                                          placeholder="Tambahkan catatan jika diperlukan"
                                          rows="3"
                                          class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all duration-200 resize-none">{{ $item->catatan }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
                <div class="flex flex-wrap items-center justify-center lg:justify-start space-x-4 text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-red-100 border border-red-300 rounded"></div>
                        <span>Expired</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-yellow-100 border border-yellow-300 rounded"></div>
                        <span>Akan Expired</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-green-100 border border-green-300 rounded"></div>
                        <span>Valid</span>
                    </div>
                </div>
                <button type="submit" class="w-full lg:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Checklist</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced radio button interactions for both desktop and mobile
    const radioButtons = document.querySelectorAll('input[type="radio"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            const isMobile = this.classList.contains('mobile-radio');

            if (isMobile) {
                // Mobile card layout
                const card = this.closest('.bg-white.border.rounded-xl');
                const statusDivs = card.querySelectorAll('.mobile-status-ada, .mobile-status-tidak');

                // Reset all status divs in the card
                statusDivs.forEach(div => {
                    div.classList.remove('bg-green-500', 'border-green-500', 'text-white', 'bg-red-500', 'border-red-500');
                    div.classList.add('bg-white', 'border-gray-300');
                });

                // Style the selected div
                const selectedDiv = this.nextElementSibling;
                if (this.value === 'ada') {
                    selectedDiv.classList.remove('bg-white', 'border-gray-300');
                    selectedDiv.classList.add('bg-green-500', 'border-green-500', 'text-white');
                } else if (this.value === 'tidak') {
                    selectedDiv.classList.remove('bg-white', 'border-gray-300');
                    selectedDiv.classList.add('bg-red-500', 'border-red-500', 'text-white');

                    // Auto-clear certificate fields for mobile
                    const certificateField = card.querySelector('input[name*="[nomor_sertifikat]"]');
                    const issuedField = card.querySelector('input[name*="[issued_date]"]');
                    const expiredField = card.querySelector('input[name*="[expired_date]"]');

                    if (certificateField) certificateField.value = '';
                    if (issuedField) issuedField.value = '';
                    if (expiredField) expiredField.value = '';
                }
            } else {
                // Desktop table layout
                const label = this.closest('label');
                const row = this.closest('tr');
                const allLabelsInRow = row?.querySelectorAll('label');

                if (allLabelsInRow) {
                    // Reset all labels in the row
                    allLabelsInRow.forEach(l => {
                        l.classList.remove('bg-green-500', 'border-green-500', 'text-white', 'bg-red-500', 'border-red-500');
                        l.classList.add('bg-white', 'border-gray-300');
                    });

                    // Style the selected label
                    if (this.value === 'ada') {
                        label.classList.remove('bg-white', 'border-gray-300');
                        label.classList.add('bg-green-500', 'border-green-500', 'text-white');
                    } else if (this.value === 'tidak') {
                        label.classList.remove('bg-white', 'border-gray-300');
                        label.classList.add('bg-red-500', 'border-red-500', 'text-white');

                        // Auto-clear certificate fields for desktop
                        const certificateField = row.querySelector('input[name*="[nomor_sertifikat]"]');
                        const issuedField = row.querySelector('input[name*="[issued_date]"]');
                        const expiredField = row.querySelector('input[name*="[expired_date]"]');

                        if (certificateField) certificateField.value = '';
                        if (issuedField) issuedField.value = '';
                        if (expiredField) expiredField.value = '';
                    }
                }
            }
        });
    });

    // Initialize radio button states on page load for both layouts
    radioButtons.forEach(radio => {
        if (radio.checked) {
            radio.dispatchEvent(new Event('change'));
        }
    });

    // Add smooth scrolling for form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.innerHTML = `
            <svg class="animate-spin w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span class="text-lg">Menyimpan...</span>
        `;
        submitButton.disabled = true;
    });

    // Add focus effects to form inputs
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('td, div')?.classList.add('ring-2', 'ring-blue-200');
        });

        input.addEventListener('blur', function() {
            this.closest('td, div')?.classList.remove('ring-2', 'ring-blue-200');
        });
    });

    // Add touch-friendly interactions for mobile
    if ('ontouchstart' in window) {
        const cards = document.querySelectorAll('.lg\\:hidden .bg-white.border.rounded-xl');
        cards.forEach(card => {
            card.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });

            card.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 100);
            });
        });
    }
});
</script>

<style>
/* Simple and Clean Styling */
* {
    transition: all 0.2s ease;
}

/* Table hover effects */
tbody tr:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Radio button styling */
input[type="radio"]:checked + label {
    transform: scale(1.05);
}

/* Form input focus */
input:focus,
textarea:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Button hover effects */
button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

/* Mobile responsive */
@media (max-width: 1023px) {
    .mobile-status-ada,
    .mobile-status-tidak {
        min-height: 56px;
        touch-action: manipulation;
    }

    input[type="text"],
    input[type="date"],
    textarea {
        min-height: 44px;
        font-size: 16px;
    }

    button {
        min-height: 44px;
        touch-action: manipulation;
    }
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Focus styles for accessibility */
input:focus,
textarea:focus,
button:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}
</style>
@endsection
