@extends('layouts.app')

@section('title', 'Tambah Pengajuan Lupa Absen')
@section('page_title', 'Tambah Pengajuan Lupa Absen')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('master.persetujuan-absensi-lupa.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl mx-auto">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Form Pengajuan Lupa Absen</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('master.persetujuan-absensi-lupa.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="col-span-1 md:col-span-2">
                        <label for="karyawan_id" class="block text-sm font-medium text-gray-700 mb-1">Karyawan <span class="text-red-500">*</span></label>
                        <select name="karyawan_id" id="karyawan_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawans as $karyawan)
                                <option value="{{ $karyawan->id }}" {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                    {{ $karyawan->nik }} - {{ $karyawan->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                        @error('tanggal')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="waktu" class="block text-sm font-medium text-gray-700 mb-1">Waktu <span class="text-red-500">*</span></label>
                        <input type="time" name="waktu" id="waktu" value="{{ old('waktu', date('H:i')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                        @error('waktu')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label for="tipe_absen" class="block text-sm font-medium text-gray-700 mb-1">Tipe Absen <span class="text-red-500">*</span></label>
                        <select name="tipe_absen" id="tipe_absen" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            <option value="Masuk" {{ old('tipe_absen') == 'Masuk' ? 'selected' : '' }}>Masuk</option>
                            <option value="Pulang" {{ old('tipe_absen') == 'Pulang' ? 'selected' : '' }}>Pulang</option>
                        </select>
                        @error('tipe_absen')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label for="alasan" class="block text-sm font-medium text-gray-700 mb-1">Alasan Lupa Absen <span class="text-red-500">*</span></label>
                        <textarea name="alasan" id="alasan" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required placeholder="Jelaskan alasan mengapa lupa absen">{{ old('alasan') }}</textarea>
                        @error('alasan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition duration-150 ease-in-out">
                        Simpan Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
