@extends('layouts.app')

@section('title', 'Edit Karyawan')
@section('page_title','Edit Karyawan')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
            <p class="font-bold">Sukses</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
            <p class="font-bold">Error</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
            <p class="font-bold">Terdapat kesalahan dalam formulir:</p>
            <ul class="list-disc list-inside mt-2">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Formulir Edit Karyawan</h2>
            <p class="text-gray-600 mt-1">Edit data karyawan di bawah ini</p>
        </div>

        @php
            // Choose appropriate update route
            $formAction = route('master.karyawan.update', $karyawan->id);
        @endphp

        <form action="{{ $formAction }}" method="POST">
            @csrf
            @method('PUT')

        @php
            // Definisikan kelas Tailwind yang sederhana dan konsisten
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-[10px] p-2.5";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-200 shadow-sm text-[10px] p-2.5";
            $selectClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-[10px] p-2.5";
            $labelClasses = "block text-xs font-medium text-gray-700";
            $fieldsetClasses = "border border-gray-200 p-6 rounded-lg mb-6 bg-gray-50/30";
            $legendClasses = "text-lg font-semibold text-gray-800 px-3 py-1 bg-white border border-gray-200 rounded-md shadow-sm flex items-center";
        @endphp
        {{-- Informasi Pribadi --}}
        <fieldset class="{{ $fieldsetClasses }}">
            <legend class="{{ $legendClasses }}">
                <svg class="w-6 h-6 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Informasi Pribadi
            </legend>
            <div class="form-section pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nik" class="{{ $labelClasses }}">NIK <span class="text-red-500">*</span></label>
                    <input type="text" name="nik" id="nik" class="{{ $inputClasses }}" required placeholder="Masukkan NIK (angka saja, tanpa huruf)" maxlength="25" pattern="[0-9]+" value="{{ old('nik', $karyawan->nik) }}">
                    <p class="text-xs text-gray-500 mt-1">NIK harus berupa angka saja, tidak boleh ada huruf</p>
                    <div id="nikError" class="text-xs text-red-600 mt-1 hidden">NIK harus berupa angka saja, tidak boleh ada huruf</div>
                </div>

                <div>
                    <label for="nama_lengkap" class="{{ $labelClasses }}">Nama Lengkap <span class="text-red-500 ml-1">*</span></label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" class="{{ $inputClasses }}" required placeholder="Masukkan nama lengkap" value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}">
                </div>

                <div>
                    <label for="nama_panggilan" class="{{ $labelClasses }}">Nama Panggilan<span class="text-red-500 ml-1">*</span></label>
                    <input type="text" name="nama_panggilan" id="nama_panggilan" class="{{ $inputClasses }}" required placeholder="Masukkan nama panggilan" value="{{ old('nama_panggilan', $karyawan->nama_panggilan) }}">
                </div>

                <div>
                    <label for="email" class="{{ $labelClasses }}">Email</label>
                    <input type="email" name="email" id="email" class="{{ $inputClasses }}" placeholder="contoh@email.com" value="{{ old('email', $karyawan->email) }}">
                </div>

                <div>
                    <label for="tanggal_lahir" class="{{ $labelClasses }}">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="{{ $inputClasses }}" value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir ? (\is_object($karyawan->tanggal_lahir) ? $karyawan->tanggal_lahir->format('Y-m-d') : $karyawan->tanggal_lahir) : '') }}">
                </div>

                <div>
                    <label for="tempat_lahir" class="{{ $labelClasses }}">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="{{ $inputClasses }}" placeholder="Kota tempat lahir" value="{{ old('tempat_lahir', $karyawan->tempat_lahir) }}">
                </div>

                <div>
                    <label for="jenis_kelamin" class="{{ $labelClasses }}">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="{{ $selectClasses }}">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label for="agama" class="{{ $labelClasses }}">Agama</label>
                    <select name="agama" id="agama" class="{{ $selectClasses }}">
                        <option value="">-- Pilih Agama --</option>
                        <option value="Islam" {{ old('agama', $karyawan->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Kristen" {{ old('agama', $karyawan->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                        <option value="Katolik" {{ old('agama', $karyawan->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                        <option value="Hindu" {{ old('agama', $karyawan->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Budha" {{ old('agama', $karyawan->agama) == 'Budha' ? 'selected' : '' }}>Budha</option>
                        <option value="Konghucu" {{ old('agama', $karyawan->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        <option value="Lainnya" {{ old('agama', $karyawan->agama) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div>
                    <label for="status_perkawinan" class="{{ $labelClasses }}">Status Pernikahan</label>
                    <select name="status_perkawinan" id="status_perkawinan" class="{{ $selectClasses }}">
                        <option value="">-- Pilih Status Perkawinan --</option>
                        <option value="Menikah" {{ old('status_perkawinan', $karyawan->status_perkawinan) == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                        <option value="Belum Menikah" {{ old('status_perkawinan', $karyawan->status_perkawinan) == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                        <option value="Janda" {{ old('status_perkawinan', $karyawan->status_perkawinan) == 'Janda' ? 'selected' : '' }}>Janda</option>
                        <option value="Duda" {{ old('status_perkawinan', $karyawan->status_perkawinan) == 'Duda' ? 'selected' : '' }}>Duda</option>
                    </select>
                </div>

                <div>
                    <label for="no_hp" class="{{ $labelClasses }}">Nomor Handphone/Whatsapp</label>
                    <input type="tel" name="no_hp" id="no_hp" class="{{ $inputClasses }}" placeholder="08xxxxxxxxxx" value="{{ old('no_hp', $karyawan->no_hp) }}">
                </div>

                <div>
                    <label for="ktp" class="{{ $labelClasses }}">Nomor KTP</label>
                    <input type="text" name="ktp" id="ktp" class="{{ $inputClasses }}" placeholder="Masukkan nomor KTP (16 digit angka saja, tanpa huruf)" maxlength="16" pattern="[0-9]{16}" value="{{ old('ktp', $karyawan->ktp) }}">
                    <p class="text-xs text-gray-500 mt-1">Nomor KTP harus tepat 16 digit angka saja, tidak boleh ada huruf</p>
                    <div id="ktpError" class="text-xs text-red-600 mt-1 hidden">Nomor KTP harus tepat 16 digit angka saja, tidak boleh ada huruf</div>
                    <div id="ktpWarning" class="text-xs mt-1 hidden"></div>
                </div>

                <div>
                    <label for="kk" class="{{ $labelClasses }}">Nomor KK</label>
                    <input type="text" name="kk" id="kk" class="{{ $inputClasses }}" placeholder="Masukkan nomor KK (16 digit angka saja, tanpa huruf)" maxlength="16" pattern="[0-9]{16}" value="{{ old('kk', $karyawan->kk) }}">
                    <p class="text-xs text-gray-500 mt-1">Nomor KK harus tepat 16 digit angka saja, tidak boleh ada huruf</p>
                    <div id="kkError" class="text-xs text-red-600 mt-1 hidden">Nomor KK harus tepat 16 digit angka saja, tidak boleh ada huruf</div>
                    <div id="kkWarning" class="text-xs mt-1 hidden"></div>
                </div>
            </div>
        </fieldset>

        {{-- Informasi Perusahaan --}}
        <fieldset class="{{ $fieldsetClasses }}">
            <legend class="{{ $legendClasses }}">
                <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Informasi Perusahaan
            </legend>
            <div class="form-section pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="divisi" class="{{ $labelClasses }}">Divisi</label>
                    <select name="divisi" id="divisi" class="{{ $selectClasses }}">
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($divisis as $divisi)
                        <option value="{{ $divisi->nama_divisi }}" {{ old('divisi', $karyawan->divisi) == $divisi->nama_divisi ? 'selected' : '' }}>{{ $divisi->nama_divisi }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="pekerjaan" class="{{ $labelClasses }}">Pekerjaan</label>
                    <select name="pekerjaan" id="pekerjaan" class="{{ $selectClasses }}">
                        <option value="">-- Pilih Pekerjaan --</option>
                    </select>
                </div>

                <div>
                    <label for="tanggal_masuk" class="{{ $labelClasses }}">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="{{ $inputClasses }}" value="{{ old('tanggal_masuk', $karyawan->tanggal_masuk ? (\is_object($karyawan->tanggal_masuk) ? $karyawan->tanggal_masuk->format('Y-m-d') : $karyawan->tanggal_masuk) : '') }}">
                </div>

                <div>
                    <label for="tanggal_berhenti" class="{{ $labelClasses }}">Tanggal Berhenti</label>
                    <input type="date" name="tanggal_berhenti" id="tanggal_berhenti" class="{{ $inputClasses }}" value="{{ old('tanggal_berhenti', $karyawan->tanggal_berhenti ? (\is_object($karyawan->tanggal_berhenti) ? $karyawan->tanggal_berhenti->format('Y-m-d') : $karyawan->tanggal_berhenti) : '') }}">
                </div>

                <div>
                    <label for="tanggal_masuk_sebelumnya" class="{{ $labelClasses }}">Tanggal Masuk (Sebelumnya)</label>
                    <input type="date" name="tanggal_masuk_sebelumnya" id="tanggal_masuk_sebelumnya" class="{{ $inputClasses }}" value="{{ old('tanggal_masuk_sebelumnya', $karyawan->tanggal_masuk_sebelumnya ? (\is_object($karyawan->tanggal_masuk_sebelumnya) ? $karyawan->tanggal_masuk_sebelumnya->format('Y-m-d') : $karyawan->tanggal_masuk_sebelumnya) : '') }}">
                </div>

                <div>
                    <label for="tanggal_berhenti_sebelumnya" class="{{ $labelClasses }}">Tanggal Berhenti (Sebelumnya)</label>
                    <input type="date" name="tanggal_berhenti_sebelumnya" id="tanggal_berhenti_sebelumnya" class="{{ $inputClasses }}" value="{{ old('tanggal_berhenti_sebelumnya', $karyawan->tanggal_berhenti_sebelumnya ? (\is_object($karyawan->tanggal_berhenti_sebelumnya) ? $karyawan->tanggal_berhenti_sebelumnya->format('Y-m-d') : $karyawan->tanggal_berhenti_sebelumnya) : '') }}">
                </div>

                <div>
                    <label for="nik_supervisor" class="{{ $labelClasses }}">NIK Supervisor</label>
                    <input type="text" name="nik_supervisor" id="nik_supervisor" class="{{ $inputClasses }}" placeholder="NIK supervisor" value="{{ old('nik_supervisor', $karyawan->nik_supervisor) }}">
                </div>

                <div>
                    <label for="supervisor" class="{{ $labelClasses }}">Nama Supervisor</label>
                    <input type="text" name="supervisor" id="supervisor" class="{{ $inputClasses }}" placeholder="Nama supervisor" value="{{ old('supervisor', $karyawan->supervisor) }}">
                </div>

                <div>
                    <label for="cabang" class="{{ $labelClasses }}">Kantor Cabang AYP</label>
                    <select name="cabang" id="cabang" class="{{ $selectClasses }}">
                        <option value="">-- Pilih Kantor Cabang AYP --</option>
                        <option value="Jakarta" {{ old('cabang', $karyawan->cabang) == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                        <option value="Batam" {{ old('cabang', $karyawan->cabang) == 'Batam' ? 'selected' : '' }}>Batam</option>
                        <option value="Pinang" {{ old('cabang', $karyawan->cabang) == 'Pinang' ? 'selected' : '' }}>Pinang</option>
                    </select>
                </div>

                <div>
                    <label for="plat" class="{{ $labelClasses }}">Nomor Plat</label>
                    <input type="text" name="plat" id="plat" class="{{ $inputClasses }}" placeholder="Nomor plat kendaraan" value="{{ old('plat', $karyawan->plat) }}">
                </div>
            </div>
        </fieldset>

        {{-- Informasi Alamat --}}
        <fieldset class="{{ $fieldsetClasses }}">
            <legend class="{{ $legendClasses }}">
                <svg class="w-6 h-6 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Informasi Alamat
            </legend>
            <div class="form-section pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="alamat" class="{{ $labelClasses }}">Alamat</label>
                    <input type="text" name="alamat" id="alamat" class="{{ $inputClasses }}" placeholder="Jalan, nomor rumah" value="{{ old('alamat', $karyawan->alamat) }}">
                </div>

                <div>
                    <label for="rt_rw" class="{{ $labelClasses }}">RT/RW</label>
                    <input type="text" name="rt_rw" id="rt_rw" class="{{ $inputClasses }}" placeholder="001/002" value="{{ old('rt_rw', $karyawan->rt_rw) }}">
                </div>

                <div>
                    <label for="kelurahan" class="{{ $labelClasses }}">Kelurahan</label>
                    <input type="text" name="kelurahan" id="kelurahan" class="{{ $inputClasses }}" placeholder="Nama kelurahan" value="{{ old('kelurahan', $karyawan->kelurahan) }}">
                </div>

                <div>
                    <label for="kecamatan" class="{{ $labelClasses }}">Kecamatan</label>
                    <input type="text" name="kecamatan" id="kecamatan" class="{{ $inputClasses }}" placeholder="Nama kecamatan" value="{{ old('kecamatan', $karyawan->kecamatan) }}">
                </div>

                <div>
                    <label for="kabupaten" class="{{ $labelClasses }}">Kabupaten</label>
                    <input type="text" name="kabupaten" id="kabupaten" class="{{ $inputClasses }}" placeholder="Nama kabupaten/kota" value="{{ old('kabupaten', $karyawan->kabupaten) }}">
                </div>

                <div>
                    <label for="provinsi" class="{{ $labelClasses }}">Provinsi</label>
                    <input type="text" name="provinsi" id="provinsi" class="{{ $inputClasses }}" placeholder="Nama provinsi" value="{{ old('provinsi', $karyawan->provinsi) }}">
                </div>

                <div>
                    <label for="kode_pos" class="{{ $labelClasses }}">Kode Pos</label>
                    <input type="text" name="kode_pos" id="kode_pos" class="{{ $inputClasses }}" placeholder="12345" value="{{ old('kode_pos', $karyawan->kode_pos) }}">
                </div>

                <div class="lg:col-span-2">
                    <label for="alamat_lengkap" class="{{ $labelClasses }}">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" id="alamat_lengkap" rows="3" class="{{ $readonlyInputClasses }}" readonly placeholder="Alamat lengkap akan muncul otomatis">{{ old('alamat_lengkap', $karyawan->alamat_lengkap) }}</textarea>
                </div>
            </div>
        </fieldset>

        {{-- Catatan --}}
        <fieldset class="{{ $fieldsetClasses }}">
            <legend class="{{ $legendClasses }}">
                <svg class="w-6 h-6 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Catatan
            </legend>
            <div>
                <label for="catatan" class="{{ $labelClasses }}">Catatan</label>
                <textarea name="catatan" id="catatan" rows="4" class="{{ $inputClasses }}" placeholder="Tambahkan catatan khusus (opsional)">{{ old('catatan', $karyawan->catatan) }}</textarea>
            </div>
        </fieldset>

        {{-- Informasi Bank --}}
        <fieldset class="{{ $fieldsetClasses }}">
            <legend class="{{ $legendClasses }}">
                <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Informasi Bank
            </legend>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                <div>
                    <label for="nama_bank" class="{{ $labelClasses }}">Nama Bank</label>
                    <select name="nama_bank" id="nama_bank" class="{{ $selectClasses }}">
                        <option value="">-- Pilih Bank --</option>
                        @foreach($banks as $bank)
                        <option value="{{ $bank->name }}" {{ old('nama_bank', $karyawan->nama_bank) == $bank->name ? 'selected' : '' }}>{{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="bank_cabang" class="{{ $labelClasses }}">Cabang Bank</label>
                    <input type="text" name="bank_cabang" id="bank_cabang" class="{{ $inputClasses }}" placeholder="Contoh: Cabang Jakarta Pusat" value="{{ old('bank_cabang', $karyawan->bank_cabang) }}">
                </div>

                <div>
                    <label for="akun_bank" class="{{ $labelClasses }}">Nomor Rekening</label>
                    <input type="text" name="akun_bank" id="akun_bank" class="{{ $inputClasses }}" placeholder="Nomor rekening bank" value="{{ old('akun_bank', $karyawan->akun_bank) }}">
                </div>

                <div class="lg:col-span-2">
                    <label for="atas_nama" class="{{ $labelClasses }}">Atas Nama</label>
                    <input type="text" name="atas_nama" id="atas_nama" class="{{ $inputClasses }}" placeholder="Nama pemilik rekening" value="{{ old('atas_nama', $karyawan->atas_nama) }}">
                    <p class="text-xs text-blue-600 mt-1 font-medium">💡 <strong>Auto-fill:</strong> Field ini akan terisi otomatis saat Anda mengetik "Nama Lengkap" di atas</p>
                </div>
            </div>
        </fieldset>

        {{-- Informasi Pajak & JKN --}}
        <fieldset class="{{ $fieldsetClasses }}">
            <legend class="{{ $legendClasses }}">
                <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Informasi Pajak & JKN
            </legend>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                <div>
                    <label for="status_pajak" class="{{ $labelClasses }}">Status Pajak</label>
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
                    </select>
                </div>

                <div>
                    <label for="jkn" class="{{ $labelClasses }}">JKN</label>
                    <input type="text" name="jkn" id="jkn" class="{{ $inputClasses }}" placeholder="Nomor JKN/BPJS" value="{{ old('jkn', $karyawan->jkn) }}">
                </div>

                <div>
                    <label for="no_ketenagakerjaan" class="{{ $labelClasses }}">BP Jamsostek</label>
                    <input type="text" name="no_ketenagakerjaan" id="no_ketenagakerjaan" class="{{ $inputClasses }}" placeholder="Nomor BP Jamsostek" value="{{ old('no_ketenagakerjaan', $karyawan->no_ketenagakerjaan) }}">
                </div>
            </div>
        </fieldset>

        <!-- Action Buttons -->
        <div class="bg-gray-50 px-6 py-6 lg:px-8 lg:py-8">
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                <a href="{{ route('master.karyawan.index') }}"
                   class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border-2 border-gray-300 bg-white py-3 px-6 text-base font-semibold text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 min-h-[48px]">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>

                <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border-2 border-transparent bg-gradient-to-r from-blue-600 to-indigo-600 py-3 px-6 text-base font-semibold text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 min-h-[48px]">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Data Karyawan
                </button>
            </div>
        </div>
        </form>
    </div>
</div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded',function(){
            const divisiSelect = document.getElementById('divisi');
            const pekerjaanSelect = document.getElementById('pekerjaan')
            const alamatFields = [
                document.getElementById('alamat'),
                document.getElementById('rt_rw'),
                document.getElementById('kelurahan'),
                document.getElementById('kecamatan'),
                document.getElementById('kabupaten'),
                document.getElementById('provinsi'),
                document.getElementById('kode_pos'),
            ]

            const alamatLengkapTextarea = document.getElementById('alamat_lengkap')

            // Data pekerjaan dari database
            const pekerjaanByDivisi = @json($pekerjaanByDivisi);

            // Fungsi untuk memperbarui opsi pekerjaan
            function updatePekerjaanOptions() {
                pekerjaanSelect.innerHTML = '<option value="">-- Pilih Pekerjaan --</option>';

                const selectedDivisi = divisiSelect.value;
                if (selectedDivisi && pekerjaanByDivisi[selectedDivisi]) {
                    pekerjaanByDivisi[selectedDivisi].forEach(function(pekerjaan) {
                        const option = document.createElement('option');
                        option.value = pekerjaan;
                        option.textContent = pekerjaan;
                        // Set selected if it matches current karyawan pekerjaan
                        if (pekerjaan === '{{ $karyawan->pekerjaan }}') {
                            option.selected = true;
                        }
                        pekerjaanSelect.appendChild(option);
                    });
                }
            }

            //Fungsi Untuk Memperbarui Kolam Alamat Lengkap
            function updateAlamatLengkap(){
                const alamatParts = alamatFields.map(field=>field.value.trim()).filter(part => part !== '')
                const combinedAddress = alamatParts.join(', ')
                alamatLengkapTextarea.value = combinedAddress
            }

            // Jalankan Fungsi Saat Halaman Dimuat
            updatePekerjaanOptions()
            updateAlamatLengkap()

            // Tambahkan Event Listener Untuk Perubahan Pada Halaman DropDown Divisi
            divisiSelect.addEventListener('change', updatePekerjaanOptions)

            // Tambahkan Event Listener Untuk Setiap Input Alamat
            alamatFields.forEach(field =>{
                field.addEventListener('input', updateAlamatLengkap)
            })

            // Auto-fill "Atas Nama" dari "Nama Lengkap"
            const namaLengkapField = document.getElementById('nama_lengkap');
            const atasNamaField = document.getElementById('atas_nama');

            if (namaLengkapField && atasNamaField) {
                // Auto-fill saat nama lengkap berubah
                namaLengkapField.addEventListener('input', function() {
                    const namaLengkap = this.value.trim();
                    const atasNama = atasNamaField.value.trim();

                    // Jika atas nama masih kosong atau sama dengan nilai sebelumnya, update otomatis
                    if (atasNama === '' || atasNama === namaLengkapField.dataset.previousValue) {
                        atasNamaField.value = namaLengkap;
                        atasNamaField.dataset.previousValue = namaLengkap;
                    }

                    // Update previous value untuk tracking
                    namaLengkapField.dataset.previousValue = namaLengkap;
                });

                // Set initial previous value
                namaLengkapField.dataset.previousValue = namaLengkapField.value;
                atasNamaField.dataset.previousValue = atasNamaField.value;

                // Tambahkan visual feedback untuk field atas nama
                atasNamaField.addEventListener('input', function() {
                    const namaLengkap = namaLengkapField.value.trim();
                    const atasNama = this.value.trim();

                    // Jika atas nama sama dengan nama lengkap, beri indikasi auto-filled
                    if (atasNama === namaLengkap && atasNama !== '') {
                        this.classList.add('bg-blue-50', 'border-blue-300');
                        this.classList.remove('bg-red-50', 'border-red-300');
                    } else if (atasNama !== namaLengkap && atasNama !== '') {
                        this.classList.add('bg-green-50', 'border-green-300');
                        this.classList.remove('bg-blue-50', 'border-blue-300', 'bg-red-50', 'border-red-300');
                    } else {
                        this.classList.remove('bg-blue-50', 'border-blue-300', 'bg-green-50', 'border-green-300');
                    }
                });

                // Trigger initial check
                atasNamaField.dispatchEvent(new Event('input'));
            }

            // Mobile-friendly enhancements
            const form = document.querySelector('form');
            const submitButton = form.querySelector('button[type="submit"]');

            // Add loading state on form submission
            form.addEventListener('submit', function() {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengupdate...
                `;
            });

            // Add focus effects for better UX
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    // Remove blue ring effect
                    // this.closest('div')?.classList.add('ring-2', 'ring-blue-200');
                });

                input.addEventListener('blur', function() {
                    // Remove blue ring effect
                    // this.closest('div')?.classList.remove('ring-2', 'ring-blue-200');
                });
            });

            // Smooth scroll to error fields if any
            const errorInputs = document.querySelectorAll('.border-red-500');
            if (errorInputs.length > 0) {
                errorInputs[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                errorInputs[0].focus();
            }

            // Auto-resize textarea
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
            });

            // Real-time validation for 16-digit fields
            const sixteenDigitFields = ['nik', 'ktp', 'kk'];
            sixteenDigitFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                const helperText = field.parentElement.querySelector('.text-xs');

                field.addEventListener('input', function() {
                    const value = this.value.replace(/\D/g, ''); // Remove non-digits
                    this.value = value; // Update field value

                    if (value.length === 0) {
                        this.classList.remove('border-red-500', 'border-green-500');
                        if (helperText) {
                            helperText.className = 'text-xs text-gray-500 mt-1';
                            helperText.textContent = `${fieldId.toUpperCase()} harus berupa 16 digit angka`;
                        }
                    } else if (value.length === 16) {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-green-500');
                        if (helperText) {
                            helperText.className = 'text-xs text-green-600 mt-1';
                            helperText.textContent = '✓ Format valid';
                        }
                    } else {
                        this.classList.remove('border-green-500');
                        this.classList.add('border-red-500');
                        if (helperText) {
                            helperText.className = 'text-xs text-red-600 mt-1';
                            helperText.textContent = `${fieldId.toUpperCase()} harus 16 digit (saat ini: ${value.length} digit)`;
                        }
                    }
                });

                field.addEventListener('blur', function() {
                    const value = this.value;
                    if (value.length > 0 && value.length !== 16) {
                        this.classList.add('border-red-500');
                        if (helperText) {
                            helperText.className = 'text-xs text-red-600 mt-1';
                            helperText.textContent = `${fieldId.toUpperCase()} harus tepat 16 digit angka`;
                        }
                    }
                });
            });
        })
    </script>

    <style>
        /* Custom mobile-friendly styles */
        @media (max-width: 768px) {
            /* Ensure inputs are touch-friendly on mobile */
            input, select, textarea {
                font-size: 16px !important; /* Prevents zoom on iOS */
                min-height: 48px !important;
            }

            /* Better spacing for mobile */
            .grid {
                gap: 1rem !important;
            }

            /* Improved button sizing */
            button, .btn {
                min-height: 48px !important;
                font-size: 16px !important;
            }
        }

        /* Smooth transitions */
        * {
            transition: all 0.2s ease;
        }

        /* Enhanced focus states */
        input:focus, select:focus, textarea:focus {
            /* Remove blue shadow effect */
            /* transform: translateY(-1px); */
            /* box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15); */
        }

        /* Loading spinner animation */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Custom gradient backgrounds */
        .bg-gradient-to-br {
            background: linear-gradient(135deg, var(--tw-gradient-from), var(--tw-gradient-via), var(--tw-gradient-to));
        }

        /* Enhanced shadow effects */
        .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Improved hover effects */
        button:hover, .btn:hover {
            transform: translateY(-2px);
        }

        /* Better fieldset styling */
        fieldset {
            position: relative;
        }

        fieldset::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
        }

        fieldset:last-of-type::after {
            display: none;
        }
    </style>
@endpush
