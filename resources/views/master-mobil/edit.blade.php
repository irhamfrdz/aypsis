@extends('layouts.app')

@section('title', 'Edit Mobil')
@section('page_title', 'Edit Mobil')

@section('content')
<h2 class="text-xl font-bold text-gray-800 mb-4">Formulir Edit Mobil</h2>

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('master-mobil.update', $mobil->id) }}" method="POST">
        @csrf
        @method('PUT')

        @php
            // Definisikan kelas Tailwind untuk input yang lebih besar dan jelas, sama seperti form permohonan
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
        @endphp

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kode No -->
                <div>
                    <label for="kode_no" class="block text-sm font-medium text-gray-700">Kode No <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_no" id="kode_no" value="{{ old('kode_no', $mobil->kode_no) }}" class="{{ $inputClasses }}" required maxlength="50" placeholder="Contoh: MOB001">
                    @error('kode_no')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Polisi -->
                <div>
                    <label for="nomor_polisi" class="block text-sm font-medium text-gray-700">Nomor Polisi <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_polisi" id="nomor_polisi" value="{{ old('nomor_polisi', $mobil->nomor_polisi) }}" class="{{ $inputClasses }}" required maxlength="20" placeholder="Contoh: B 1234 ABC">
                    @error('nomor_polisi')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lokasi -->
                <div>
                    <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                    <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $mobil->lokasi) }}" class="{{ $inputClasses }}" maxlength="100" placeholder="Contoh: Jakarta Utara">
                    @error('lokasi')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Merek -->
                <div>
                    <label for="merek" class="block text-sm font-medium text-gray-700">Merek</label>
                    <input type="text" name="merek" id="merek" value="{{ old('merek', $mobil->merek) }}" class="{{ $inputClasses }}" maxlength="50" placeholder="Contoh: Toyota, Isuzu">
                    @error('merek')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis -->
                <div>
                    <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis</label>
                    <input type="text" name="jenis" id="jenis" value="{{ old('jenis', $mobil->jenis) }}" class="{{ $inputClasses }}" maxlength="50" placeholder="Contoh: Truk, Pick Up, Mobil Box">
                    @error('jenis')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun Pembuatan -->
                <div>
                    <label for="tahun_pembuatan" class="block text-sm font-medium text-gray-700">Tahun Pembuatan</label>
                    <input type="number" name="tahun_pembuatan" id="tahun_pembuatan" value="{{ old('tahun_pembuatan', $mobil->tahun_pembuatan) }}" class="{{ $inputClasses }}" min="1990" max="{{ date('Y') + 1 }}" placeholder="Contoh: {{ date('Y') }}">
                    @error('tahun_pembuatan')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Teknis</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- BPKB -->
                <div>
                    <label for="bpkb" class="block text-sm font-medium text-gray-700">Nomor BPKB</label>
                    <input type="text" name="bpkb" id="bpkb" value="{{ old('bpkb', $mobil->bpkb) }}" class="{{ $inputClasses }}" maxlength="50">
                    @error('bpkb')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No Mesin -->
                <div>
                    <label for="no_mesin" class="block text-sm font-medium text-gray-700">Nomor Mesin</label>
                    <input type="text" name="no_mesin" id="no_mesin" value="{{ old('no_mesin', $mobil->no_mesin) }}" class="{{ $inputClasses }}" maxlength="50">
                    @error('no_mesin')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Rangka -->
                <div>
                    <label for="nomor_rangka" class="block text-sm font-medium text-gray-700">Nomor Rangka</label>
                    <input type="text" name="nomor_rangka" id="nomor_rangka" value="{{ old('nomor_rangka', $mobil->nomor_rangka) }}" class="{{ $inputClasses }}" maxlength="50">
                    @error('nomor_rangka')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Atas Nama -->
                <div>
                    <label for="atas_nama" class="block text-sm font-medium text-gray-700">Atas Nama</label>
                    <input type="text" name="atas_nama" id="atas_nama" value="{{ old('atas_nama', $mobil->atas_nama) }}" class="{{ $inputClasses }}" maxlength="100" placeholder="Nama pemilik kendaraan">
                    @error('atas_nama')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Informasi Pajak & Dokumen</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Pajak STNK -->
                <div>
                    <label for="pajak_stnk" class="block text-sm font-medium text-gray-700">Pajak STNK</label>
                    <input type="date" name="pajak_stnk" id="pajak_stnk" value="{{ old('pajak_stnk', $mobil->pajak_stnk ? $mobil->pajak_stnk->format('Y-m-d') : '') }}" class="{{ $inputClasses }}">
                    @error('pajak_stnk')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pajak Plat -->
                <div>
                    <label for="pajak_plat" class="block text-sm font-medium text-gray-700">Pajak Plat</label>
                    <input type="date" name="pajak_plat" id="pajak_plat" value="{{ old('pajak_plat', $mobil->pajak_plat ? $mobil->pajak_plat->format('Y-m-d') : '') }}" class="{{ $inputClasses }}">
                    @error('pajak_plat')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No KIR -->
                <div>
                    <label for="no_kir" class="block text-sm font-medium text-gray-700">Nomor KIR</label>
                    <input type="text" name="no_kir" id="no_kir" value="{{ old('no_kir', $mobil->no_kir) }}" class="{{ $inputClasses }}" maxlength="50">
                    @error('no_kir')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pajak KIR -->
                <div>
                    <label for="pajak_kir" class="block text-sm font-medium text-gray-700">Pajak KIR</label>
                    <input type="date" name="pajak_kir" id="pajak_kir" value="{{ old('pajak_kir', $mobil->pajak_kir ? $mobil->pajak_kir->format('Y-m-d') : '') }}" class="{{ $inputClasses }}">
                    @error('pajak_kir')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="mb-6">
            <legend class="text-lg font-semibold text-gray-800 mb-4">Penugasan Karyawan</legend>
            <div class="grid grid-cols-1 gap-4">
                <!-- Karyawan -->
                <div>
                    <label for="karyawan_id" class="block text-sm font-medium text-gray-700">Karyawan</label>
                    <select name="karyawan_id" id="karyawan_id" class="{{ $inputClasses }}">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}" {{ old('karyawan_id', $mobil->karyawan_id) == $karyawan->id ? 'selected' : '' }}>
                                {{ $karyawan->nama_lengkap }} @if($karyawan->nik)({{ $karyawan->nik }})@endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-blue-600">
                        <svg class="inline h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Nomor polisi akan otomatis diupdate ke data karyawan yang dipilih
                    </p>
                    @error('karyawan_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('master-mobil.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Perbarui
            </button>
        </div>
    </form>
</div>
@endsection
