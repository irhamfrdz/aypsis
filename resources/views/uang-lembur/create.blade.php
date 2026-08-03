@extends('layouts.app')

@section('title', 'Tambah Data Uang Lembur')
@section('page_title', 'Tambah Data Uang Lembur')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Tambah Data Uang Lembur</h2>
                <a href="{{ route('uang-lembur.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    &larr; Kembali
                </a>
            </div>
            
            <div class="p-6">
                <form action="{{ route('uang-lembur.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label for="karyawan_id" class="block text-sm font-medium text-gray-700">Karyawan <span class="text-red-500">*</span></label>
                            <select id="karyawan_id" name="karyawan_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                                <option value="">Pilih Karyawan</option>
                                @foreach($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id }}">{{ $karyawan->nama }} ({{ $karyawan->nik }}) - {{ $karyawan->penempatan }}</option>
                                @endforeach
                            </select>
                            @error('karyawan_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal Lembur <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" id="tanggal" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>
                        
                        <div>
                            <label for="tipe_hari" class="block text-sm font-medium text-gray-700">Tipe Hari <span class="text-red-500">*</span></label>
                            <select id="tipe_hari" name="tipe_hari" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                                <option value="Hari Kerja">Hari Kerja</option>
                                <option value="Hari Libur">Hari Libur</option>
                            </select>
                        </div>

                        <div>
                            <label for="jam_mulai" class="block text-sm font-medium text-gray-700">Jam Mulai <span class="text-red-500">*</span></label>
                            <input type="time" name="jam_mulai" id="jam_mulai" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>

                        <div>
                            <label for="jam_selesai" class="block text-sm font-medium text-gray-700">Jam Selesai <span class="text-red-500">*</span></label>
                            <input type="time" name="jam_selesai" id="jam_selesai" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <p class="mt-1 text-xs text-gray-500">Nominal lembur akan otomatis dihitung berdasarkan rumus saat disimpan.</p>
                        </div>

                        <div class="col-span-2">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                        </div>

                        <div class="col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                            <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
