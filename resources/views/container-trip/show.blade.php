@extends('layouts.app')

@section('title', 'Detail Perjalanan Kontainer')
@section('page_title', 'Detail Perjalanan Kontainer')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="{{ route('container-trip.index') }}" class="mr-4 text-white hover:text-indigo-100 transition duration-150 ease-in-out">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Detail Perjalanan</h1>
                            <p class="text-indigo-100 text-sm">Informasi lengkap kontainer {{ $containerTrip->no_kontainer }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('container-trip.edit', $containerTrip) }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informasi Kontainer
                </h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 uppercase tracking-wider">No. Kontainer</dt>
                        <dd class="mt-1 text-lg font-bold text-indigo-700">{{ $containerTrip->no_kontainer }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 uppercase tracking-wider">Ukuran</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $containerTrip->ukuran == '20' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $containerTrip->ukuran }}'
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 uppercase tracking-wider">Vendor Penyewa</dt>
                        <dd class="mt-1 text-base text-gray-900 font-semibold">{{ $containerTrip->vendor->nama_vendor }}</dd>
                    </div>



                    <div class="col-span-1 md:col-span-2 border-t border-gray-100 pt-6">
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Detail Waktu & Tarif</h4>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tanggal Ambil</dt>
                        <dd class="mt-1 text-base text-gray-900">{{ $containerTrip->tgl_ambil->format('d F Y') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tanggal Kembali</dt>
                        <dd class="mt-1 text-base text-gray-900">{{ $containerTrip->tgl_kembali ? $containerTrip->tgl_kembali->format('d F Y') : 'Masih Aktif' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Harga Sewa</dt>
                        <dd class="mt-1 text-xl font-bold text-green-600">Rp {{ number_format($containerTrip->harga_sewa, 2, ',', '.') }}</dd>
                    </div>

                    <div class="col-span-1 md:col-span-2 border-t border-gray-100 pt-6">
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Metadata System</h4>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
                        <dd class="mt-1 text-sm text-gray-600">{{ $containerTrip->created_at->format('d/m/Y H:i:s') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                        <dd class="mt-1 text-sm text-gray-600">{{ $containerTrip->updated_at->format('d/m/Y H:i:s') }}</dd>
                    </div>
                </dl>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <form action="{{ route('container-trip.destroy', $containerTrip) }}"
                      method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-800 text-sm font-medium transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Record
                    </button>
                </form>
                <a href="{{ route('container-trip.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
