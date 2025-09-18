@extends('layouts.app')

@section('title', isset($karyawan) ? 'Edit Data Karyawan - ' . $karyawan->nama_lengkap : 'Pendaftaran Karyawan Baru')
@section('page_title', isset($karyawan) ? 'Edit Data Karyawan' : 'Pendaftaran Karyawan Baru')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-6 lg:text-left">
            <h2 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
                {{ isset($karyawan) ? 'Formulir Edit Data Karyawan' : 'Formulir Pendaftaran Karyawan Baru' }}
            </h2>
            <p class="text-gray-600 text-sm lg:text-base">{{ isset($karyawan) ? 'Perbarui data karyawan Anda di bawah ini.' : 'Lengkapi formulir di bawah untuk mendaftarkan diri sebagai karyawan.' }}</p>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg mb-6 shadow-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Gagal menambahkan data karyawan:</span>
                </div>
                <div class="text-sm">{{ session('error') }}</div>
            </div>
        @endif
        @if (count($errors) > 0)
            <div class="bg-red-50 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 shadow-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Terdapat kesalahan dalam formulir:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach ((array) $errors as $error )
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <form action="{{ isset($karyawan) ? route('karyawan.onboarding-update', $karyawan->id) : route('karyawan.store') }}" method="POST" class="divide-y divide-gray-100">
            @csrf
            @if(isset($karyawan))
                @method('PUT')
            @endif
            @php
                $inputClasses = "mt-1 block w-full rounded-xl border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 text-base p-3 lg:p-4 transition-all duration-200 min-h-[48px]";
                $readonlyInputClasses = "mt-1 block w-full rounded-xl border-gray-300 bg-gray-100 shadow-sm text-base p-3 lg:p-4 min-h-[48px]";
                $selectClasses = "mt-1 block w-full rounded-xl border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 text-base p-3 lg:p-4 transition-all duration-200 min-h-[48px]";
                $labelClasses = "block text-sm font-semibold text-gray-700 mb-2";
                $fieldsetClasses = "p-6 lg:p-8 space-y-6";
                $legendClasses = "text-lg lg:text-xl font-bold text-gray-800 mb-6 flex items-center";
            @endphp
            {{-- Informasi Pribadi --}}
            <fieldset class="{{ $fieldsetClasses }}">
                <legend class="{{ $legendClasses }}">
                    <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Informasi Pribadi
                </legend>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                    <div>
                        <label for="nik" class="{{ $labelClasses }}">NIK<span class="text-red-500 ml-1">*</span></label>
                        <input type="text" name="nik" id="nik" class="{{ $inputClasses }}" required placeholder="Masukkan NIK" value="{{ old('nik', $karyawan->nik ?? '') }}">
                    </div>
                    <div>
                        <label for="nama_lengkap" class="{{ $labelClasses }}">Nama Lengkap <span class="text-red-500 ml-1">*</span></label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="{{ $inputClasses }}" required placeholder="Masukkan nama lengkap" value="{{ old('nama_lengkap', $karyawan->nama_lengkap ?? '') }}">
                    </div>
                    <div>
                        <label for="nama_panggilan" class="{{ $labelClasses }}">Nama Panggilan<span class="text-red-500 ml-1">*</span></label>
                        <input type="text" name="nama_panggilan" id="nama_panggilan" class="{{ $inputClasses }}" required placeholder="Masukkan nama panggilan" value="{{ old('nama_panggilan', $karyawan->nama_panggilan ?? '') }}">
                    </div>
                    <div>
                        <label for="email" class="{{ $labelClasses }}">Email</label>
                        <input type="email" name="email" id="email" class="{{ $inputClasses }}" placeholder="contoh@email.com" value="{{ old('email', $karyawan->email ?? '') }}">
                    </div>
                    <div>
                        <label for="tanggal_lahir" class="{{ $labelClasses }}">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="{{ $inputClasses }}" value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir ?? '') }}">
                    </div>
                    <div>
                        <label for="tempat_lahir" class="{{ $labelClasses }}">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="{{ $inputClasses }}" placeholder="Kota tempat lahir" value="{{ old('tempat_lahir', $karyawan->tempat_lahir ?? '') }}">
                    </div>
                    <div>
                        <label for="jenis_kelamin" class="{{ $labelClasses }}">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="{{ $selectClasses }}">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="agama" class="{{ $labelClasses }}">Agama</label>
                        <select name="agama" id="agama" class="{{ $selectClasses }}">
                            <option value="">-- Pilih Agama --</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Budha">Budha</option>
                            <option value="Konghucu">Konghucu</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="status_perkawinan" class="{{ $labelClasses }}">Status Pernikahan</label>
                        <select name="status_perkawinan" id="status_perkawinan" class="{{ $selectClasses }}">
                            <option value="">-- Pilih Status Perkawinan --</option>
                            <option value="Menikah">Menikah</option>
                            <option value="Belum Menikah">Belum Menikah</option>
                            <option value="Janda">Janda</option>
                            <option value="Duda">Duda</option>
                        </select>
                    </div>
                    <div>
                        <label for="no_hp" class="{{ $labelClasses }}">Nomor Handphone/Whatsapp</label>
                        <input type="tel" name="no_hp" id="no_hp" class="{{ $inputClasses }}" placeholder="08xxxxxxxxxx">
                    </div>
                    <div>
                        <label for="ktp" class="{{ $labelClasses }}">Nomor KTP</label>
                        <input type="text" name="ktp" id="ktp" class="{{ $inputClasses }}" placeholder="16 digit nomor KTP">
                    </div>
                    <div>
                        <label for="kk" class="{{ $labelClasses }}">Nomor KK</label>
                        <input type="text" name="kk" id="kk" class="{{ $inputClasses }}" placeholder="16 digit nomor KK">
                    </div>
                </div>
            </fieldset>

            {{-- Informasi Perusahaan --}}
            <fieldset class="{{ $fieldsetClasses }}">
                <legend class="{{ $legendClasses }}">
                    <svg class="w-6 h-6 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Informasi Perusahaan
                </legend>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                    <div>
                        <label for="divisi" class="{{ $labelClasses }}">Divisi</label>
                        <select name="divisi" id="divisi" class="{{ $selectClasses }}">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach($divisis as $divisi)
                            <option value="{{ $divisi->nama_divisi }}" {{ old('divisi', $karyawan->divisi ?? '') == $divisi->nama_divisi ? 'selected' : '' }}>{{ $divisi->nama_divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="pekerjaan" class="{{ $labelClasses }}">Pekerjaan</label>
                        <select name="pekerjaan" id="pekerjaan" class="{{ $selectClasses }}">
                            <option value="">-- Pilih Pekerjaan --</option>
                            @foreach($pekerjaans as $pekerjaan)
                            <option value="{{ $pekerjaan->nama_pekerjaan }}" {{ old('pekerjaan', $karyawan->pekerjaan ?? '') == $pekerjaan->nama_pekerjaan ? 'selected' : '' }}>{{ $pekerjaan->nama_pekerjaan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tanggal_masuk" class="{{ $labelClasses }}">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="{{ $inputClasses }}">
                    </div>
                    <div>
                        <label for="tanggal_berhenti" class="{{ $labelClasses }}">Tanggal Berhenti</label>
                        <input type="date" name="tanggal_berhenti" id="tanggal_berhenti" class="{{ $inputClasses }}">
                    </div>
                    <div>
                        <label for="tanggal_masuk_sebelumnya" class="{{ $labelClasses }}">Tanggal Masuk (Sebelumnya)</label>
                        <input type="date" name="tanggal_masuk_sebelumnya" id="tanggal_masuk_sebelumnya" class="{{ $inputClasses }}">
                    </div>
                    <div>
                        <label for="tanggal_berhenti_sebelumnya" class="{{ $labelClasses }}">Tanggal Berhenti (Sebelumnya)</label>
                        <input type="date" name="tanggal_berhenti_sebelumnya" id="tanggal_berhenti_sebelumnya" class="{{ $inputClasses }}">
                    </div>
                    <div>
                        <label for="nik_supervisor" class="{{ $labelClasses }}">NIK Supervisor</label>
                        <input type="text" name="nik_supervisor" id="nik_supervisor" class="{{ $inputClasses }}" placeholder="NIK supervisor">
                    </div>
                    <div>
                        <label for="supervisor" class="{{ $labelClasses }}">Nama Supervisor</label>
                        <input type="text" name="supervisor" id="supervisor" class="{{ $inputClasses }}" placeholder="Nama supervisor">
                    </div>
                    <div>
                        <label for="cabang" class="{{ $labelClasses }}">Kantor Cabang AYP</label>
                        <select name="cabang" id="cabang" class="{{ $selectClasses }}">
                            <option value="">-- Pilih Kantor Cabang AYP --</option>
                            @foreach($cabangs as $cabang)
                            <option value="{{ $cabang->nama_cabang }}" {{ old('cabang', $karyawan->cabang ?? '') == $cabang->nama_cabang ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="plat" class="{{ $labelClasses }}">Nomor Plat</label>
                        <input type="text" name="plat" id="plat" class="{{ $inputClasses }}" placeholder="Nomor plat kendaraan" value="{{ old('plat', $karyawan->plat ?? '') }}">
                    </div>
                </div>
            </fieldset>

            {{-- Informasi Alamat --}}
            <fieldset class="{{ $fieldsetClasses }}">
                <legend class="{{ $legendClasses }}">
                    <svg class="w-6 h-6 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Informasi Alamat
                </legend>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                    <div>
                        <label for="alamat" class="{{ $labelClasses }}">Alamat</label>
                        <input type="text" name="alamat" id="alamat" class="{{ $inputClasses }}" placeholder="Jalan, nomor rumah">
                    </div>
                    <div>
                        <label for="rt_rw" class="{{ $labelClasses }}">RT/RW</label>
                        <input type="text" name="rt_rw" id="rt_rw" class="{{ $inputClasses }}" placeholder="001/002">
                    </div>
                    <div>
                        <label for="kelurahan" class="{{ $labelClasses }}">Kelurahan</label>
                        <input type="text" name="kelurahan" id="kelurahan" class="{{ $inputClasses }}" placeholder="Nama kelurahan">
                    </div>
                    <div>
                        <label for="kecamatan" class="{{ $labelClasses }}">Kecamatan</label>
                        <input type="text" name="kecamatan" id="kecamatan" class="{{ $inputClasses }}" placeholder="Nama kecamatan">
                    </div>
                    <div>
                        <label for="kabupaten" class="{{ $labelClasses }}">Kabupaten</label>
                        <input type="text" name="kabupaten" id="kabupaten" class="{{ $inputClasses }}" placeholder="Nama kabupaten/kota">
                    </div>
                    <div>
                        <label for="provinsi" class="{{ $labelClasses }}">Provinsi</label>
                        <input type="text" name="provinsi" id="provinsi" class="{{ $inputClasses }}" placeholder="Nama provinsi">
                    </div>
                    <div>
                        <label for="kode_pos" class="{{ $labelClasses }}">Kode Pos</label>
                        <input type="text" name="kode_pos" id="kode_pos" class="{{ $inputClasses }}" placeholder="12345">
                    </div>
                    <div class="lg:col-span-2">
                        <label for="alamat_lengkap" class="{{ $labelClasses }}">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" id="alamat_lengkap" rows="3" class="{{ $readonlyInputClasses }}" readonly placeholder="Alamat lengkap akan muncul otomatis"></textarea>
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
                    <textarea name="catatan" id="catatan" rows="4" class="{{ $inputClasses }}" placeholder="Tambahkan catatan khusus (opsional)"></textarea>
                </div>
            </fieldset>

            {{-- Informasi Bank --}}
            <fieldset class="{{ $fieldsetClasses }}">
                <legend class="{{ $legendClasses }}">
                    <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Informasi Bank
                </legend>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                    <div>
                        <label for="nama_bank" class="{{ $labelClasses }}">Nama Bank</label>
                        <input type="text" name="nama_bank" id="nama_bank" class="{{ $inputClasses }}" placeholder="Contoh: Bank BCA">
                    </div>
                    <div>
                        <label for="bank_cabang" class="{{ $labelClasses }}">Cabang Bank</label>
                        <input type="text" name="bank_cabang" id="bank_cabang" class="{{ $inputClasses }}" placeholder="Contoh: Cabang Jakarta Pusat">
                    </div>
                    <div>
                        <label for="akun_bank" class="{{ $labelClasses }}">Nomor Rekening</label>
                        <input type="text" name="akun_bank" id="akun_bank" class="{{ $inputClasses }}" placeholder="Nomor rekening bank">
                    </div>
                    <div class="lg:col-span-2">
                        <label for="atas_nama" class="{{ $labelClasses }}">Atas Nama</label>
                        <input type="text" name="atas_nama" id="atas_nama" class="{{ $inputClasses }}" placeholder="Nama pemilik rekening">
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
                            @foreach($pajaks as $pajak)
                            <option value="{{ $pajak->nama_status }}" {{ old('status_pajak', $karyawan->status_pajak ?? '') == $pajak->nama_status ? 'selected' : '' }}>{{ $pajak->nama_status }} - {{ $pajak->keterangan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="jkn" class="{{ $labelClasses }}">JKN</label>
                        <input type="text" name="jkn" id="jkn" class="{{ $inputClasses }}" placeholder="Nomor JKN/BPJS" value="{{ old('jkn', $karyawan->jkn ?? '') }}">
                    </div>
                    <div>
                        <label for="no_ketenagakerjaan" class="{{ $labelClasses }}">BP Jamsostek</label>
                        <input type="text" name="no_ketenagakerjaan" id="no_ketenagakerjaan" class="{{ $inputClasses }}" placeholder="Nomor BP Jamsostek" value="{{ old('no_ketenagakerjaan', $karyawan->no_ketenagakerjaan ?? '') }}">
                    </div>
                </div>
            </fieldset>
            <div class="bg-gray-50 px-6 py-6 lg:px-8 lg:py-8">
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border-2 border-transparent bg-gradient-to-r from-blue-600 to-indigo-600 py-3 px-6 text-base font-semibold text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 min-h-[48px]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ isset($karyawan) ? 'Update Data Karyawan' : 'Simpan Data Karyawan' }}
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
    document.addEventListener('DOMContentLoaded', function() {
        const divisiSelect = document.getElementById('divisi');
        const pekerjaanSelect = document.getElementById('pekerjaan');
        // Data pekerjaan dari database
        const pekerjaanOptions = @json($pekerjaanByDivisi);
        function updatePekerjaanOptions() {
            pekerjaanSelect.innerHTML = '<option value="">-- Pilih Pekerjaan --</option>';
            const selectedDivisi = divisiSelect.value;
            if(selectedDivisi && pekerjaanOptions[selectedDivisi]){
                pekerjaanOptions[selectedDivisi].forEach(function(pekerjaan){
                    const option = document.createElement('option');
                    option.value = pekerjaan;
                    option.textContent = pekerjaan;
                    pekerjaanSelect.appendChild(option);
                });
            }
        }
        updatePekerjaanOptions();
        divisiSelect.addEventListener('change', updatePekerjaanOptions);
    });
</script>
@endpush
