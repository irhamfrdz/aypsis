@extends('layouts.app')
@php $hideSidebar = true; @endphp

@section('title', 'Form Pelamar Karyawan')
@section('page_title', 'Recruitment')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto py-8">

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

    @if ($errors && is_object($errors) && $errors->any())
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
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Formulir Lamaran Kerja</h2>
                <p class="text-gray-600 mt-1">Lengkapi formulir di bawah untuk melamar</p>
            </div>
            <a href="{{ route('login') }}" class="inline-flex justify-center py-2 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-arrow-left mt-1 mr-2"></i> Kembali ke Login
            </a>
        </div>

        <form action="{{ route('recruitment.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @php
                $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-[10px] p-2.5";
                $selectClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-[10px] p-2.5";
                $labelClasses = "block text-xs font-medium text-gray-700";
            @endphp
            
            {{-- Informasi Pribadi --}}
            <fieldset class="border p-4 rounded-md mb-4">
                <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Pribadi</legend>
                <div class="form-section pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_lengkap" class="{{ $labelClasses }}">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="{{ $inputClasses }} @error('nama_lengkap') border-red-500 @enderror" required placeholder="Nama Sesuai KTP" value="{{ old('nama_lengkap') }}">
                            @error('nama_lengkap')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="no_nik" class="{{ $labelClasses }}">Nomor NIK (KTP) <span class="text-red-500">*</span></label>
                            <input type="text" name="no_nik" id="no_nik" class="{{ $inputClasses }} @error('no_nik') border-red-500 @enderror" required placeholder="16 Digit No. KTP" maxlength="16" pattern="[0-9]{16}" value="{{ old('no_nik') }}">
                            @error('no_nik')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="jenis_kelamin" class="{{ $labelClasses }}">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="{{ $selectClasses }} @error('jenis_kelamin') border-red-500 @enderror" required>
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="agama_select" class="{{ $labelClasses }}">Agama <span class="text-red-500">*</span></label>
                            <select id="agama_select" class="{{ $selectClasses }} @error('agama') border-red-500 @enderror" required onchange="handleAgamaChange(this)">
                                <option value="">-- Pilih Agama --</option>
                                <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Budha" {{ old('agama') == 'Budha' ? 'selected' : '' }}>Budha</option>
                                <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                <option value="Lainnya" {{ (old('agama') && !in_array(old('agama'), ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'])) ? 'selected' : '' }}>Lainnya (Isi Sendiri)</option>
                            </select>
                            <input type="hidden" name="agama" id="agama_hidden" value="{{ old('agama') }}">
                            <div id="agama_lainnya_container" class="mt-2 {{ (old('agama') && !in_array(old('agama'), ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'])) ? '' : 'hidden' }}">
                                <input type="text" id="agama_lainnya" class="{{ $inputClasses }}" placeholder="Masukkan Agama Anda..." value="{{ (old('agama') && !in_array(old('agama'), ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'])) ? old('agama') : '' }}" oninput="updateAgamaHidden(this.value)">
                            </div>
                            @error('agama')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="tanggal_lahir" class="{{ $labelClasses }}">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="{{ $inputClasses }} @error('tanggal_lahir') border-red-500 @enderror" required value="{{ old('tanggal_lahir') }}">
                            @error('tanggal_lahir')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="tempat_lahir" class="{{ $labelClasses }}">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" class="{{ $inputClasses }} @error('tempat_lahir') border-red-500 @enderror" required placeholder="Kota" value="{{ old('tempat_lahir') }}">
                            @error('tempat_lahir')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="no_handphone" class="{{ $labelClasses }}">Nomor Handphone <span class="text-red-500">*</span></label>
                            <input type="tel" name="no_handphone" id="no_handphone" class="{{ $inputClasses }} @error('no_handphone') border-red-500 @enderror" required placeholder="08xxxxxxxxxx" maxlength="20" value="{{ old('no_handphone') }}">
                            @error('no_handphone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="email" class="{{ $labelClasses }}">Email</label>
                            <input type="email" name="email" id="email" class="{{ $inputClasses }} @error('email') border-red-500 @enderror" placeholder="contoh@email.com" value="{{ old('email') }}">
                            @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- Identitas & Rekening --}}
            <fieldset class="border p-4 rounded-md mb-4">
                <legend class="text-lg font-semibold text-gray-800 px-2">Identitas & Rekening</legend>
                <div class="form-section pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="no_kartu_keluarga" class="{{ $labelClasses }}">No. Kartu Keluarga</label>
                            <input type="text" name="no_kartu_keluarga" id="no_kartu_keluarga" class="{{ $inputClasses }}" placeholder="Nomor KK" value="{{ old('no_kartu_keluarga') }}">
                        </div>

                        <div>
                            <label for="nomor_rekening" class="{{ $labelClasses }}">Nomor Rekening</label>
                            <input type="text" name="nomor_rekening" id="nomor_rekening" class="{{ $inputClasses }}" placeholder="Nomor Rekening Bank" value="{{ old('nomor_rekening') }}">
                        </div>
                        
                        <div>
                            <label for="npwp" class="{{ $labelClasses }}">NPWP</label>
                            <input type="text" name="npwp" id="npwp" class="{{ $inputClasses }}" placeholder="Nomor NPWP" value="{{ old('npwp') }}">
                        </div>
                        
                        <div>
                            <label for="no_bpjs_kesehatan" class="{{ $labelClasses }}">No. BPJS Kesehatan</label>
                            <input type="text" name="no_bpjs_kesehatan" id="no_bpjs_kesehatan" class="{{ $inputClasses }}" placeholder="Nomor BPJS Kesehatan" value="{{ old('no_bpjs_kesehatan') }}">
                        </div>
                        
                        <div>
                            <label for="no_ketenagakerjaan" class="{{ $labelClasses }}">No. Ketenagakerjaan</label>
                            <input type="text" name="no_ketenagakerjaan" id="no_ketenagakerjaan" class="{{ $inputClasses }}" placeholder="Nomor BPJS Ketenagakerjaan" value="{{ old('no_ketenagakerjaan') }}">
                        </div>

                        <div>
                            <label for="tanggungan_anak" class="{{ $labelClasses }}">Tanggungan (Jumlah Anak)</label>
                            <input type="number" name="tanggungan_anak" id="tanggungan_anak" class="{{ $inputClasses }}" value="{{ old('tanggungan_anak', 0) }}">
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- Ukuran Seragam & Perlengkapan --}}
            <fieldset class="border p-4 rounded-md mb-4">
                <legend class="text-lg font-semibold text-gray-800 px-2">Seragam & Perlengkapan</legend>
                <div class="form-section pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="wearpack_size" class="{{ $labelClasses }}">Ukuran Wearpack</label>
                            <select name="wearpack_size" id="wearpack_size" class="{{ $selectClasses }}">
                                <option value="">-- Pilih Ukuran --</option>
                                <option value="S" {{ old('wearpack_size') == 'S' ? 'selected' : '' }}>S</option>
                                <option value="M" {{ old('wearpack_size') == 'M' ? 'selected' : '' }}>M</option>
                                <option value="L" {{ old('wearpack_size') == 'L' ? 'selected' : '' }}>L</option>
                                <option value="XL" {{ old('wearpack_size') == 'XL' ? 'selected' : '' }}>XL</option>
                                <option value="XXL" {{ old('wearpack_size') == 'XXL' ? 'selected' : '' }}>XXL</option>
                                <option value="3XL" {{ old('wearpack_size') == '3XL' ? 'selected' : '' }}>3XL</option>
                            </select>
                        </div>

                        <div>
                            <label for="no_safety_shoes" class="{{ $labelClasses }}">Ukuran Sepatu Safety</label>
                            <input type="text" name="no_safety_shoes" id="no_safety_shoes" class="{{ $inputClasses }}" placeholder="Contoh: 42" value="{{ old('no_safety_shoes') }}">
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- Informasi Alamat & Kontak --}}
            <fieldset class="border p-4 rounded-md mb-4">
                <legend class="text-lg font-semibold text-gray-800 px-2">Alamat & Kontak Darurat</legend>
                <div class="form-section pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="kelurahan" class="{{ $labelClasses }}">Kelurahan</label>
                            <input type="text" name="kelurahan" id="kelurahan" class="{{ $inputClasses }}" placeholder="Nama Kelurahan" value="{{ old('kelurahan') }}">
                        </div>

                        <div>
                            <label for="kecamatan" class="{{ $labelClasses }}">Kecamatan</label>
                            <input type="text" name="kecamatan" id="kecamatan" class="{{ $inputClasses }}" placeholder="Nama Kecamatan" value="{{ old('kecamatan') }}">
                        </div>
                        
                        <div>
                            <label for="kota_kabupaten" class="{{ $labelClasses }}">Kota / Kabupaten</label>
                            <input type="text" name="kota_kabupaten" id="kota_kabupaten" class="{{ $inputClasses }}" placeholder="Nama Kota/Kabupaten" value="{{ old('kota_kabupaten') }}">
                        </div>
                        
                        <div>
                            <label for="provinsi" class="{{ $labelClasses }}">Provinsi</label>
                            <input type="text" name="provinsi" id="provinsi" class="{{ $inputClasses }}" placeholder="Nama Provinsi" value="{{ old('provinsi') }}">
                        </div>
                        
                        <div>
                            <label for="kode_pos" class="{{ $labelClasses }}">Kode Pos</label>
                            <input type="text" name="kode_pos" id="kode_pos" class="{{ $inputClasses }}" placeholder="12345" value="{{ old('kode_pos') }}">
                        </div>
                        
                        <div>
                            <label for="kontak_darurat" class="{{ $labelClasses }}">Kontak Darurat</label>
                            <input type="text" name="kontak_darurat" id="kontak_darurat" class="{{ $inputClasses }}" placeholder="Nama & No. HP (Cth: Jane - 0812xxxxx)" value="{{ old('kontak_darurat') }}">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="alamat_lengkap" class="{{ $labelClasses }}">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="alamat_lengkap" id="alamat_lengkap" rows="3" class="{{ $inputClasses }} @error('alamat_lengkap') border-red-500 @enderror" required placeholder="Jalan, No. Rumah, RT/RW">{{ old('alamat_lengkap') }}</textarea>
                            @error('alamat_lengkap')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- Upload CV / Resume --}}
            <fieldset class="border p-4 rounded-md mb-4">
                <legend class="text-lg font-semibold text-gray-800 px-2">Dokumen Pendukung</legend>
                <div class="form-section pt-4">
                    <div>
                        <label for="cv" class="{{ $labelClasses }}">Upload CV / Resume (PDF, DOC, DOCX - Maks 2MB)</label>
                        <input type="file" name="cv" id="cv" class="{{ $inputClasses }} @error('cv') border-red-500 @enderror">
                        @error('cv')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </fieldset>

            <div class="flex justify-end mt-8">
                <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Kirim Lamaran Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script>
    function handleAgamaChange(select) {
        const container = document.getElementById('agama_lainnya_container');
        const hiddenInput = document.getElementById('agama_hidden');
        const otherInput = document.getElementById('agama_lainnya');
        
        if (select.value === 'Lainnya') {
            container.classList.remove('hidden');
            otherInput.required = true;
            hiddenInput.value = otherInput.value;
        } else {
            container.classList.add('hidden');
            otherInput.required = false;
            hiddenInput.value = select.value;
        }
    }

    function updateAgamaHidden(value) {
        document.getElementById('agama_hidden').value = value;
    }
</script>
@endsection
