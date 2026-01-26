@extends('layouts.app')

@section('title', 'Detail Karyawan Tidak Tetap')
@section('page_title', 'Detail Karyawan Tidak Tetap')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900">Detail Karyawan: {{ $karyawanTidakTetap->nama }}</h2>
            <div class="flex space-x-2">
                <a href="{{ route('karyawan-tidak-tetap.edit', $karyawanTidakTetap->id) }}" class="inline-flex items-center px-3 py-1.5 border border-indigo-600 text-indigo-600 rounded-md text-sm font-medium hover:bg-indigo-50">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('karyawan-tidak-tetap.index') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Nama Lengkap</h3>
                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $karyawanTidakTetap->nama }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">NIK</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $karyawanTidakTetap->nik ?? '-' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Posisi / Pekerjaan</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $karyawanTidakTetap->posisi ?? '-' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">No HP</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $karyawanTidakTetap->no_hp ?? '-' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <p class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $karyawanTidakTetap->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($karyawanTidakTetap->status) }}
                        </span>
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Tanggal Masuk</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $karyawanTidakTetap->tanggal_masuk ? $karyawanTidakTetap->tanggal_masuk->format('d F Y') : '-' }}</p>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Alamat</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $karyawanTidakTetap->alamat ?? '-' }}</p>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Keterangan</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $karyawanTidakTetap->keterangan ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
