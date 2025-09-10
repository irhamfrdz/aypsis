@extends('layouts.app')

@section('title', 'Edit Karyawan')
@section('page_title', 'Edit Data Karyawan')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('master.karyawan.update', $karyawan->id) }}" method="POST">
        @csrf
        @method('PUT')

        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
            $selectClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
        @endphp

        {{-- Bagian 1: Informasi Pribadi --}}
        <fieldset class="border p-4 rounded-md mb-6">
            <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Pribadi</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4">
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
                    <input type="text" name="nik" id="nik" value="{{ old('nik', $karyawan->nik) }}" class="{{ $inputClasses }}" required>
                    @error('nik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" class="{{ $inputClasses }}" required>
                    @error('nama_lengkap') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nama_panggilan" class="block text-sm font-medium text-gray-700 mb-1">Nama Panggilan</label>
                    <input type="text" name="nama_panggilan" id="nama_panggilan" value="{{ old('nama_panggilan', $karyawan->nama_panggilan) }}" class="{{ $inputClasses }}">
                    @error('nama_panggilan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $karyawan->email) }}" class="{{ $inputClasses }}">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $karyawan->no_hp) }}" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir', $karyawan->tempat_lahir) }}" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', optional($karyawan->tanggal_lahir)->format('Y-m-d')) }}" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="{{ $inputClasses }}">
                        <option value="">-- Pilih --</option>
                        <option value="L" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label for="status_perkawinan" class="block text-sm font-medium text-gray-700 mb-1">Status Perkawinan</label>
                    <input type="text" name="status_perkawinan" id="status_perkawinan" value="{{ old('status_perkawinan', $karyawan->status_perkawinan) }}" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="ktp" class="block text-sm font-medium text-gray-700 mb-1">No. KTP</label>
                    <input type="text" name="ktp" id="ktp" value="{{ old('ktp', $karyawan->ktp) }}" class="{{ $inputClasses }}">
                    @error('ktp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="kk" class="block text-sm font-medium text-gray-700 mb-1">No. KK</label>
                    <input type="text" name="kk" id="kk" value="{{ old('kk', $karyawan->kk) }}" class="{{ $inputClasses }}">
                    @error('kk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="agama" class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                    <input type="text" name="agama" id="agama" value="{{ old('agama', $karyawan->agama) }}" class="{{ $inputClasses }}">
                </div>
            </div>
        </fieldset>

        {{-- Bagian 2: Alamat --}}
        <fieldset class="border p-4 rounded-md mb-6">
            <legend class="text-lg font-semibold text-gray-800 px-2">Alamat</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4">
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat Singkat</label>
                    <input type="text" name="alamat" id="alamat" value="{{ old('alamat', $karyawan->alamat) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="rt_rw" class="block text-sm font-medium text-gray-700 mb-1">RT / RW</label>
                    <input type="text" name="rt_rw" id="rt_rw" value="{{ old('rt_rw', $karyawan->rt_rw) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="kelurahan" class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                    <input type="text" name="kelurahan" id="kelurahan" value="{{ old('kelurahan', $karyawan->kelurahan) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                    <input type="text" name="kecamatan" id="kecamatan" value="{{ old('kecamatan', $karyawan->kecamatan) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="kabupaten" class="block text-sm font-medium text-gray-700 mb-1">Kabupaten</label>
                    <input type="text" name="kabupaten" id="kabupaten" value="{{ old('kabupaten', $karyawan->kabupaten) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                    <input type="text" name="provinsi" id="provinsi" value="{{ old('provinsi', $karyawan->provinsi) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="kode_pos" class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                    <input type="text" name="kode_pos" id="kode_pos" value="{{ old('kode_pos', $karyawan->kode_pos) }}" class="{{ $inputClasses }}">
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label for="alamat_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <input type="text" name="alamat_lengkap" id="alamat_lengkap" value="{{ old('alamat_lengkap', $karyawan->alamat_lengkap) }}" class="{{ $inputClasses }}">
                </div>
            </div>
        </fieldset>

        {{-- Bagian 3: Pekerjaan & Riwayat --}}
        <fieldset class="border p-4 rounded-md mb-6">
            <legend class="text-lg font-semibold text-gray-800 px-2">Pekerjaan & Riwayat</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4">
                <div>
                    <label for="divisi" class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                    <input type="text" name="divisi" id="divisi" value="{{ old('divisi', $karyawan->divisi) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="pekerjaan" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                    <input type="text" name="pekerjaan" id="pekerjaan" value="{{ old('pekerjaan', $karyawan->pekerjaan) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="plat" class="block text-sm font-medium text-gray-700 mb-1">No. Plat</label>
                    <input type="text" name="plat" id="plat" value="{{ old('plat', $karyawan->plat) }}" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" value="{{ old('tanggal_masuk', optional($karyawan->tanggal_masuk)->format('Y-m-d')) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="tanggal_berhenti" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berhenti</label>
                    <input type="date" name="tanggal_berhenti" id="tanggal_berhenti" value="{{ old('tanggal_berhenti', optional($karyawan->tanggal_berhenti)->format('Y-m-d')) }}" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="tanggal_masuk_sebelumnya" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk (Sebelumnya)</label>
                    <input type="date" name="tanggal_masuk_sebelumnya" id="tanggal_masuk_sebelumnya" value="{{ old('tanggal_masuk_sebelumnya', optional($karyawan->tanggal_masuk_sebelumnya)->format('Y-m-d')) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="tanggal_berhenti_sebelumnya" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berhenti (Sebelumnya)</label>
                    <input type="date" name="tanggal_berhenti_sebelumnya" id="tanggal_berhenti_sebelumnya" value="{{ old('tanggal_berhenti_sebelumnya', optional($karyawan->tanggal_berhenti_sebelumnya)->format('Y-m-d')) }}" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="nik_supervisor" class="block text-sm font-medium text-gray-700 mb-1">NIK Supervisor</label>
                    <input type="text" name="nik_supervisor" id="nik_supervisor" value="{{ old('nik_supervisor', $karyawan->nik_supervisor) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="supervisor" class="block text-sm font-medium text-gray-700 mb-1">Supervisor</label>
                    <input type="text" name="supervisor" id="supervisor" value="{{ old('supervisor', $karyawan->supervisor) }}" class="{{ $inputClasses }}">
                </div>
            </div>
        </fieldset>

        {{-- Bagian 4: Bank & Lainnya --}}
        <fieldset class="border p-4 rounded-md mb-6">
            <legend class="text-lg font-semibold text-gray-800 px-2">Bank & Lainnya</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4">
                <div>
                    <label for="status_pajak" class="block text-sm font-medium text-gray-700 mb-1">Status Pajak</label>
                    <select name="status_pajak" id="status_pajak" class="{{ $selectClasses }}">
                        <option value="">-- Pilih Status Pajak --</option>
                        <option value="TK0" {{ old('status_pajak', $karyawan->status_pajak) == 'TK0' ? 'selected' : '' }}>TK0 - Tidak Kawin</option>
                        <option value="TK1" {{ old('status_pajak', $karyawan->status_pajak) == 'TK1' ? 'selected' : '' }}>TK1 - Tidak Kawin + 1 Tanggungan</option>
                        <option value="TK2" {{ old('status_pajak', $karyawan->status_pajak) == 'TK2' ? 'selected' : '' }}>TK2 - Tidak Kawin + 2 Tanggungan</option>
                        <option value="TK3" {{ old('status_pajak', $karyawan->status_pajak) == 'TK3' ? 'selected' : '' }}>TK3 - Tidak Kawin + 3 Tanggungan</option>
                        <option value="K0" {{ old('status_pajak', $karyawan->status_pajak) == 'K0' ? 'selected' : '' }}>K0 - Kawin</option>
                        <option value="K1" {{ old('status_pajak', $karyawan->status_pajak) == 'K1' ? 'selected' : '' }}>K1 - Kawin + 1 Tanggungan</option>
                        <option value="K2" {{ old('status_pajak', $karyawan->status_pajak) == 'K2' ? 'selected' : '' }}>K2 - Kawin + 2 Tanggungan</option>
                        <option value="K3" {{ old('status_pajak', $karyawan->status_pajak) == 'K3' ? 'selected' : '' }}>K3 - Kawin + 3 Tanggungan</option>
                        <option value="K/0" {{ old('status_pajak', $karyawan->status_pajak) == 'K/0' ? 'selected' : '' }}>K/0 - Kawin Penghasilan Istri Digabung</option>
                        <option value="K/1" {{ old('status_pajak', $karyawan->status_pajak) == 'K/1' ? 'selected' : '' }}>K/1 - Kawin Penghasilan Istri Digabung + 1 Tanggungan</option>
                        <option value="K/2" {{ old('status_pajak', $karyawan->status_pajak) == 'K/2' ? 'selected' : '' }}>K/2 - Kawin Penghasilan Istri Digabung + 2 Tanggungan</option>
                        <option value="K/3" {{ old('status_pajak', $karyawan->status_pajak) == 'K/3' ? 'selected' : '' }}>K/3 - Kawin Penghasilan Istri Digabung + 3 Tanggungan</option>
                        <option value="TK/" {{ old('status_pajak', $karyawan->status_pajak) == 'TK/' ? 'selected' : '' }}>TK/ - Tidak Kawin Penghasilan Suami Istri Digabung</option>
                        <option value="TK/0" {{ old('status_pajak', $karyawan->status_pajak) == 'TK/0' ? 'selected' : '' }}>TK/0 - Tidak Kawin Penghasilan Digabung</option>
                        <!-- Keep other values that might exist in data -->
                        @if(!in_array(old('status_pajak', $karyawan->status_pajak), ['', 'TK0', 'TK1', 'TK2', 'TK3', 'K0', 'K1', 'K2', 'K3', 'K/0', 'K/1', 'K/2', 'K/3', 'TK/', 'TK/0']) && old('status_pajak', $karyawan->status_pajak))
                            <option value="{{ old('status_pajak', $karyawan->status_pajak) }}" selected>{{ old('status_pajak', $karyawan->status_pajak) }}</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label for="nama_bank" class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                    <input type="text" name="nama_bank" id="nama_bank" value="{{ old('nama_bank', $karyawan->nama_bank) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="bank_cabang" class="block text-sm font-medium text-gray-700 mb-1">Cabang Bank</label>
                    <input type="text" name="bank_cabang" id="bank_cabang" value="{{ old('bank_cabang', $karyawan->bank_cabang) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="akun_bank" class="block text-sm font-medium text-gray-700 mb-1">Akun Bank</label>
                    <input type="text" name="akun_bank" id="akun_bank" value="{{ old('akun_bank', $karyawan->akun_bank) }}" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="atas_nama" class="block text-sm font-medium text-gray-700 mb-1">Atas Nama</label>
                    <input type="text" name="atas_nama" id="atas_nama" value="{{ old('atas_nama', $karyawan->atas_nama) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="jkn" class="block text-sm font-medium text-gray-700 mb-1">JKN</label>
                    <input type="text" name="jkn" id="jkn" value="{{ old('jkn', $karyawan->jkn) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="no_ketenagakerjaan" class="block text-sm font-medium text-gray-700 mb-1">BP Jamsostek</label>
                    <input type="text" name="no_ketenagakerjaan" id="no_ketenagakerjaan" value="{{ old('no_ketenagakerjaan', $karyawan->no_ketenagakerjaan) }}" class="{{ $inputClasses }}">
                </div>
                <div>
                    <label for="cabang" class="block text-sm font-medium text-gray-700 mb-1">Kantor Cabang AYP</label>
                    <input type="text" name="cabang" id="cabang" value="{{ old('cabang', $karyawan->cabang) }}" class="{{ $inputClasses }}">
                </div>

                <div class="md:col-span-2 lg:col-span-3">
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="4" class="{{ $inputClasses }}">{{ old('catatan', $karyawan->catatan) }}</textarea>
                </div>
            </div>
        </fieldset>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('master.karyawan.index') }}" class="inline-flex justify-center py-2 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Update Karyawan
            </button>
        </div>
    </form>
</div>
@endsection
