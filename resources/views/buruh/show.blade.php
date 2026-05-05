@extends('layouts.app')

@section('title', 'Detail Buruh')
@section('page_title', 'Detail Data Buruh')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('master.buruh.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    <svg class="mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar
                </a>
                <h1 class="mt-2 text-3xl font-extrabold text-gray-900 tracking-tight">Detail Buruh</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('master.buruh.edit', $buruh->id) }}" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                    Edit Data
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 p-8 text-white">
                <div class="flex items-center">
                    <div class="h-24 w-24 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-4xl font-bold border border-white/30">
                        {{ strtoupper(substr($buruh->nama, 0, 1)) }}
                    </div>
                    <div class="ml-6">
                        <h2 class="text-3xl font-bold">{{ $buruh->nama }}</h2>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $buruh->status == 'aktif' ? 'bg-green-400/20 text-green-100 border border-green-400/30' : 'bg-red-400/20 text-red-100 border border-red-400/30' }}">
                                {{ ucfirst($buruh->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Contact Info -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Informasi Pribadi</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">NIK (Nomor Induk Karyawan)</label>
                                <div class="mt-1 flex items-center text-gray-900 font-medium">
                                    <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    {{ $buruh->nik ?? 'Tidak ada data' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</label>
                                <div class="mt-1 flex items-start text-gray-900 font-medium">
                                    <svg class="h-5 w-5 text-indigo-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $buruh->alamat ?? 'Tidak ada data' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Info -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Informasi Sistem</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">ID Buruh</label>
                                <div class="mt-1 text-gray-900 font-medium">#{{ str_pad($buruh->id, 5, '0', STR_PAD_LEFT) }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Terdaftar Pada</label>
                                <div class="mt-1 text-gray-900 font-medium">{{ $buruh->created_at->format('d F Y, H:i') }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Terakhir Diperbarui</label>
                                <div class="mt-1 text-gray-900 font-medium">{{ $buruh->updated_at->format('d F Y, H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity/Notes Placeholder -->
                <div class="mt-12">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Catatan / Aktivitas</h3>
                    <div class="bg-gray-50 rounded-xl p-6 text-center border-2 border-dashed border-gray-200">
                        <p class="text-sm text-gray-500 italic">Belum ada catatan aktivitas untuk buruh ini.</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-8 py-4 flex justify-between items-center border-t border-gray-100">
                <span class="text-xs text-gray-400 font-medium italic italic">Data Buruh Aypsis System v1.0</span>
                <form action="{{ route('master.buruh.destroy', $buruh->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data buruh ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-bold uppercase tracking-widest">
                        Hapus Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
