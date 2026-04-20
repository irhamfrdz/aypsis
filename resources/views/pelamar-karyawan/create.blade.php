@extends('layouts.app')
@php $hideSidebar = true; @endphp

@section('title', 'Form Pelamar Karyawan')
@section('page_title', 'Recruitment')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 mb-4">
                Formulir Lamaran Kerja
            </h1>
            <p class="text-lg text-gray-600">Bergabunglah dengan tim kami dan bangun masa depan bersama.</p>
        </div>

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="p-8 sm:p-12">
                <form action="{{ route('recruitment.store') }}" method="POST">
                    @csrf

                    <!-- Personal Information Group -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-indigo-600"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">Informasi Pribadi</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm"
                                       placeholder="Nama Sesuai KTP">
                                @error('nama_lengkap') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="group">
                                <label for="no_nik" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Nomor NIK (KTP) <span class="text-red-500">*</span></label>
                                <input type="text" name="no_nik" id="no_nik" value="{{ old('no_nik') }}" required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm"
                                       placeholder="16 Digit No. KTP">
                                @error('no_nik') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="group">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm"
                                       placeholder="contoh@email.com">
                                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="group">
                                <label for="no_handphone" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">No. Handphone <span class="text-red-500">*</span></label>
                                <input type="text" name="no_handphone" id="no_handphone" value="{{ old('no_handphone') }}" required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm"
                                       placeholder="0812xxxxxxxx">
                                @error('no_handphone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="group">
                                <label for="jenis_kelamin" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="jenis_kelamin" id="jenis_kelamin" required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="group">
                                    <label for="tempat_lahir" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Tempat Lahir <span class="text-red-500">*</span></label>
                                    <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir') }}" required
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm"
                                           placeholder="Kota">
                                    @error('tempat_lahir') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div class="group">
                                    <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Tanggal Lahir <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                    @error('tanggal_lahir') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Identity & Account Group -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-id-card text-purple-600"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">Identitas & Rekening</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label for="no_kartu_keluarga" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">No. Kartu Keluarga</label>
                                <input type="text" name="no_kartu_keluarga" id="no_kartu_keluarga" value="{{ old('no_kartu_keluarga') }}"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                            </div>

                            <div class="group">
                                <label for="nomor_rekening" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Nomor Rekening</label>
                                <input type="text" name="nomor_rekening" id="nomor_rekening" value="{{ old('nomor_rekening') }}"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm"
                                       placeholder="Nomor Rekening Bank">
                            </div>

                            <div class="group">
                                <label for="npwp" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">NPWP</label>
                                <input type="text" name="npwp" id="npwp" value="{{ old('npwp') }}"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                            </div>

                            <div class="group">
                                <label for="no_bpjs_kesehatan" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">No. BPJS Kesehatan</label>
                                <input type="text" name="no_bpjs_kesehatan" id="no_bpjs_kesehatan" value="{{ old('no_bpjs_kesehatan') }}"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                            </div>

                            <div class="group">
                                <label for="no_ketenagakerjaan" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">No. Ketenagakerjaan</label>
                                <input type="text" name="no_ketenagakerjaan" id="no_ketenagakerjaan" value="{{ old('no_ketenagakerjaan') }}"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                            </div>

                            <div class="group">
                                <label for="tanggungan_anak" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Tanggungan (Jumlah Anak)</label>
                                <input type="number" name="tanggungan_anak" id="tanggungan_anak" value="{{ old('tanggungan_anak', 0) }}"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Equipment & Uniform Group -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-tshirt text-green-600"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">Ukuran Seragam & Perlengkapan</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label for="wearpack_size" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Ukuran Wearpack</label>
                                <select name="wearpack_size" id="wearpack_size"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                    <option value="">Pilih Ukuran</option>
                                    <option value="S" {{ old('wearpack_size') == 'S' ? 'selected' : '' }}>S</option>
                                    <option value="M" {{ old('wearpack_size') == 'M' ? 'selected' : '' }}>M</option>
                                    <option value="L" {{ old('wearpack_size') == 'L' ? 'selected' : '' }}>L</option>
                                    <option value="XL" {{ old('wearpack_size') == 'XL' ? 'selected' : '' }}>XL</option>
                                    <option value="XXL" {{ old('wearpack_size') == 'XXL' ? 'selected' : '' }}>XXL</option>
                                    <option value="3XL" {{ old('wearpack_size') == '3XL' ? 'selected' : '' }}>3XL</option>
                                </select>
                            </div>

                            <div class="group">
                                <label for="no_safety_shoes" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Ukuran Sepatu Safety</label>
                                <input type="text" name="no_safety_shoes" id="no_safety_shoes" value="{{ old('no_safety_shoes') }}"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm"
                                       placeholder="Contoh: 42">
                            </div>
                        </div>
                    </div>

                    <!-- Address & Contact Group -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-map-marker-alt text-orange-600"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">Alamat & Kontak Darurat</h2>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="group">
                                <label for="alamat_lengkap" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Alamat Lengkap <span class="text-red-500">*</span></label>
                                <textarea name="alamat_lengkap" id="alamat_lengkap" rows="3" required
                                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm"
                                          placeholder="Jalan, No. Rumah, RT/RW">{{ old('alamat_lengkap') }}</textarea>
                                @error('alamat_lengkap') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="group">
                                    <label for="kelurahan" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Kelurahan</label>
                                    <input type="text" name="kelurahan" id="kelurahan" value="{{ old('kelurahan') }}"
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                </div>
                                <div class="group">
                                    <label for="kecamatan" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Kecamatan</label>
                                    <input type="text" name="kecamatan" id="kecamatan" value="{{ old('kecamatan') }}"
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                </div>
                                <div class="group">
                                    <label for="kota_kabupaten" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Kota / Kabupaten</label>
                                    <input type="text" name="kota_kabupaten" id="kota_kabupaten" value="{{ old('kota_kabupaten') }}"
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                </div>
                                <div class="group">
                                    <label for="provinsi" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Provinsi</label>
                                    <input type="text" name="provinsi" id="provinsi" value="{{ old('provinsi') }}"
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                </div>
                                <div class="group">
                                    <label for="kode_pos" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Kode Pos</label>
                                    <input type="text" name="kode_pos" id="kode_pos" value="{{ old('kode_pos') }}"
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm">
                                </div>
                                <div class="group">
                                    <label for="kontak_darurat" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-indigo-600 transition-colors">Kontak Darurat</label>
                                    <input type="text" name="kontak_darurat" id="kontak_darurat" value="{{ old('kontak_darurat') }}"
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 shadow-sm"
                                           placeholder="Nama & No. HP (Contoh: Jane - 0812xxxxx)">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex flex-col sm:flex-row items-center justify-between gap-6">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                        </a>
                        <button type="submit" class="w-full sm:w-auto px-12 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-2xl hover:from-indigo-700 hover:to-purple-700 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 shadow-lg transform hover:-translate-y-1">
                            Kirim Lamaran Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12 text-sm text-gray-500">
            &copy; {{ date('Y') }} Aypsis Recruitment. Seluruh hak cipta dilindungi.
        </div>
    </div>
</div>
@endsection
