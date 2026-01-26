@extends('layouts.app')

@section('title', 'Tambah Karyawan Tidak Tetap')
@section('page_title', 'Tambah Karyawan Tidak Tetap')

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
            <h2 class="text-2xl font-bold text-gray-800">Formulir Karyawan Tidak Tetap</h2>
            <p class="text-gray-600 mt-1">Lengkapi formulir di bawah untuk menambah karyawan tidak tetap baru</p>
        </div>

        <form action="{{ route('karyawan-tidak-tetap.store') }}" method="POST">
            @csrf

            @php
                // Definisikan kelas Tailwind yang sederhana dan konsisten
                $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-[10px] p-2.5";
                $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-200 shadow-sm text-[10px] p-2.5";
                $selectClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-[10px] p-2.5";
                $labelClasses = "block text-xs font-medium text-gray-700";
            @endphp

        {{-- Informasi Pribadi --}}
        <fieldset class="border p-4 rounded-md mb-4">
            <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Pribadi</legend>
            <div class="form-section pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nik" class="{{ $labelClasses }}">NIK <span class="text-red-500">*</span></label>
                        <input type="text" name="nik" id="nik" class="{{ $inputClasses }} bg-white" required value="{{ old('nik') }}" placeholder="Masukkan NIK">
                    </div>

                    <div>
                        <label for="nama_lengkap" class="{{ $labelClasses }}">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="{{ $inputClasses }}" required value="{{ old('nama_lengkap') }}" placeholder="Masukkan nama lengkap">
                    </div>

                    <div>
                        <label for="nama_panggilan" class="{{ $labelClasses }}">Nama Panggilan</label>
                        <input type="text" name="nama_panggilan" id="nama_panggilan" class="{{ $inputClasses }}" value="{{ old('nama_panggilan') }}" placeholder="Masukkan nama panggilan">
                    </div>

                    <div>
                        <label for="email" class="{{ $labelClasses }}">Email</label>
                        <input type="email" name="email" id="email" class="{{ $inputClasses }}" value="{{ old('email') }}" placeholder="contoh@email.com">
                    </div>

                    <div>
                        <label for="jenis_kelamin" class="{{ $labelClasses }}">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="{{ $selectClasses }}">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label for="agama" class="{{ $labelClasses }}">Agama</label>
                        <select name="agama" id="agama" class="{{ $selectClasses }}">
                            <option value="">-- Pilih Agama --</option>
                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>

                    <div>
                        <label for="nik_ktp" class="{{ $labelClasses }}">Nomor KTP</label>
                        <input type="text" name="nik_ktp" id="nik_ktp" class="{{ $inputClasses }}" value="{{ old('nik_ktp') }}" placeholder="Masukkan nomor KTP">
                    </div>
                </div>
            </div>
        </fieldset>

        {{-- Informasi Perusahaan --}}
        <fieldset class="border p-4 rounded-md mb-4">
            <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Pekerjaan</legend>
            <div class="form-section pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="divisi" class="{{ $labelClasses }}">Divisi</label>
                        <input type="text" name="divisi" id="divisi" class="{{ $readonlyInputClasses }}" value="NON KARYAWAN" readonly>
                    </div>

                    <div class="relative">
                        <label for="pekerjaan" class="{{ $labelClasses }}">Pekerjaan</label>
                        <div class="relative">
                            <input type="text" id="pekerjaan_search" class="{{ $inputClasses }}" placeholder="-- Pilih Pekerjaan --" autocomplete="off" value="{{ old('pekerjaan') }}">
                            <input type="hidden" name="pekerjaan" id="pekerjaan" value="{{ old('pekerjaan') }}">
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none top-4">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <ul id="pekerjaan_dropdown" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md mt-1 shadow-lg max-h-40 overflow-y-auto hidden text-[10px]">
                            @foreach($pekerjaans as $p)
                                <li class="px-3 py-2 cursor-pointer hover:bg-indigo-50 text-gray-700" data-value="{{ $p->nama_pekerjaan }}">{{ $p->nama_pekerjaan }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <label for="cabang" class="{{ $labelClasses }}">Kantor Cabang AYP</label>
                        <select name="cabang" id="cabang" class="{{ $selectClasses }}">
                            <option value="">-- Pilih Kantor Cabang AYP --</option>
                            <option value="JAKARTA" {{ old('cabang') == 'JAKARTA' ? 'selected' : '' }}>JAKARTA</option>
                            <option value="BATAM" {{ old('cabang') == 'BATAM' ? 'selected' : '' }}>BATAM</option>
                            <option value="TANJUNG PINANG" {{ old('cabang') == 'TANJUNG PINANG' ? 'selected' : '' }}>TANJUNG PINANG</option>
                        </select>
                    </div>

                    <div>
                        <label for="tanggal_masuk" class="{{ $labelClasses }}">Tanggal Masuk Kerja</label>
                        <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="{{ $inputClasses }}" value="{{ old('tanggal_masuk') }}">
                    </div>

                    <div>
                        <label for="status_pajak" class="{{ $labelClasses }}">Status Pajak</label>
                        <select name="status_pajak" id="status_pajak" class="{{ $selectClasses }}">
                            <option value="">-- Pilih Status Pajak --</option>
                            @foreach($pajaks as $pajak)
                                <option value="{{ $pajak->nama_status }}" {{ old('status_pajak') == $pajak->nama_status ? 'selected' : '' }}>{{ $pajak->nama_status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </fieldset>

        {{-- Informasi Alamat --}}
        <fieldset class="border p-4 rounded-md mb-4">
            <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Alamat Domisili</legend>
            <div class="form-section pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     <div class="lg:col-span-2">
                        <label for="alamat_lengkap" class="{{ $labelClasses }}">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" id="alamat_lengkap" rows="3" class="{{ $inputClasses }}" placeholder="Alamat lengkap">{{ old('alamat_lengkap') }}</textarea>
                    </div>

                    <div>
                        <label for="rt_rw" class="{{ $labelClasses }}">RT/RW</label>
                        <input type="text" name="rt_rw" id="rt_rw" class="{{ $inputClasses }}" value="{{ old('rt_rw') }}" placeholder="00/00">
                    </div>

                    <div>
                        <label for="kelurahan" class="{{ $labelClasses }}">Kelurahan</label>
                        <input type="text" name="kelurahan" id="kelurahan" class="{{ $inputClasses }}" value="{{ old('kelurahan') }}" placeholder="Nama kelurahan">
                    </div>

                    <div>
                        <label for="kecamatan" class="{{ $labelClasses }}">Kecamatan</label>
                        <input type="text" name="kecamatan" id="kecamatan" class="{{ $inputClasses }}" value="{{ old('kecamatan') }}" placeholder="Nama kecamatan">
                    </div>

                    <div>
                        <label for="kabupaten" class="{{ $labelClasses }}">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten" id="kabupaten" class="{{ $inputClasses }}" value="{{ old('kabupaten') }}" placeholder="Nama kabupaten/kota">
                    </div>

                    <div>
                        <label for="provinsi" class="{{ $labelClasses }}">Provinsi</label>
                        <input type="text" name="provinsi" id="provinsi" class="{{ $inputClasses }}" value="{{ old('provinsi') }}" placeholder="Nama provinsi">
                    </div>

                    <div>
                        <label for="kode_pos" class="{{ $labelClasses }}">Kode Pos</label>
                        <input type="text" name="kode_pos" id="kode_pos" class="{{ $inputClasses }}" value="{{ old('kode_pos') }}" placeholder="12345">
                    </div>
                </div>
            </div>
        </fieldset>

        <div class="flex justify-end mt-8">
            <a href="{{ route('karyawan-tidak-tetap.index') }}" class="inline-flex justify-center py-2 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Simpan Data
            </button>
        </div>
        </form>
    </div>
</div>
@endsection



@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('pekerjaan_search');
        const hiddenInput = document.getElementById('pekerjaan');
        const dropdown = document.getElementById('pekerjaan_dropdown');
        const options = dropdown.querySelectorAll('li');

        // Show dropdown on focus or click
        searchInput.addEventListener('focus', () => {
            dropdown.classList.remove('hidden');
        });
        
        searchInput.addEventListener('click', () => {
            dropdown.classList.remove('hidden');
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Filter options
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            let hasResults = false;
            
            options.forEach(option => {
                const text = option.innerText.toLowerCase();
                if (text.includes(filter)) {
                    option.classList.remove('hidden');
                    hasResults = true;
                } else {
                    option.classList.add('hidden');
                }
            });

            dropdown.classList.remove('hidden');
        });

        // Select option
        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.innerText;
                
                searchInput.value = text;
                hiddenInput.value = value;
                dropdown.classList.add('hidden');
            });
        });
        
        // Handle manual input (optional: clear if not in list, or allow custom)
        // Currently allows custom input if user types and clicks away without selecting
        searchInput.addEventListener('change', function() {
             hiddenInput.value = this.value; 
        });
    });
</script>
@endpush
