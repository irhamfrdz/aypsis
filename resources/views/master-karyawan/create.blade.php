@extends('layouts.app')

@section('title', 'Tambah Karyawan')
@section('page_title','Tambah Karyawan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-4 px-4 sm:px-6 lg:px-8">
    <!-- Mobile-optimized header -->
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-6 lg:text-left">
            <h2 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
                Formulir Karyawan Baru
            </h2>
            <p class="text-gray-600 text-sm lg:text-base">Lengkapi formulir di bawah untuk menambah karyawan baru</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 shadow-sm">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Terdapat kesalahan dalam formulir:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach ($errors->all() as $error )
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            @php
                // Choose appropriate store route:
                // - If the current user has no linked karyawan, prefer the onboarding route
                //   so the middleware doesn't block the POST (karyawan.store).
                // - Otherwise, if the user is admin and master store exists, use it.
                $formAction = null;
                try {
                    $user = auth()->user();
                    $hasKaryawan = $user && !empty($user->karyawan_id);

                    if (!$hasKaryawan && \Illuminate\Support\Facades\Route::has('karyawan.store')) {
                        $formAction = route('karyawan.store');
                    } elseif ($user && method_exists($user, 'hasRole') && $user->hasRole('admin') && \Illuminate\Support\Facades\Route::has('master.karyawan.store')) {
                        $formAction = route('master.karyawan.store');
                    } elseif (\Illuminate\Support\Facades\Route::has('karyawan.store')) {
                        $formAction = route('karyawan.store');
                    } elseif (\Illuminate\Support\Facades\Route::has('master.karyawan.store')) {
                        $formAction = route('master.karyawan.store');
                    } else {
                        $formAction = route('dashboard');
                    }
                } catch (\Exception $e) {
                    $formAction = route('dashboard');
                }
            @endphp

            <form action="{{ $formAction }}" method="POST" class="divide-y divide-gray-100">
            @csrf

        @php
            // Definisikan kelas Tailwind untuk input yang responsif dan mobile-friendly
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
                    <input type="text" name="nik" id="nik" class="{{ $inputClasses }}" required placeholder="Masukkan NIK">
                </div>

                <div>
                    <label for="nama_lengkap" class="{{ $labelClasses }}">Nama Lengkap <span class="text-red-500 ml-1">*</span></label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" class="{{ $inputClasses }}" required placeholder="Masukkan nama lengkap">
                </div>

                <div>
                    <label for="nama_panggilan" class="{{ $labelClasses }}">Nama Panggilan<span class="text-red-500 ml-1">*</span></label>
                    <input type="text" name="nama_panggilan" id="nama_panggilan" class="{{ $inputClasses }}" required placeholder="Masukkan nama panggilan">
                </div>

                <div>
                    <label for="email" class="{{ $labelClasses }}">Email</label>
                    <input type="email" name="email" id="email" class="{{ $inputClasses }}" placeholder="contoh@email.com">
                </div>

                <div>
                    <label for="tanggal_lahir" class="{{ $labelClasses }}">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="tempat_lahir" class="{{ $labelClasses }}">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="{{ $inputClasses }}" placeholder="Kota tempat lahir">
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
                        <option value="{{ $divisi->nama_divisi }}">{{ $divisi->nama_divisi }}</option>
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
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="tanggal_berhenti" class="{{ $labelClasses }}">Tanggal Berhenti</label>
                    <input type="date" name="tanggal_berhenti" id="tanggal_berhenti" class="{{ $inputClasses }}">
                </div>

                <div>
                    <label for="tanggal_masuk_sebelumnya" class="{{ $labelClasses }}">Tanggal Masuk (Sebelumnya)</label>
                    <input type="date" name="tanggal_masuk_sebelumnya" id="tanggal_masuk_sebelumnya" class="{{ $inputClasses }}" value="{{ old('tanggal_masuk_sebelumnya') }}">
                </div>

                <div>
                    <label for="tanggal_berhenti_sebelumnya" class="{{ $labelClasses }}">Tanggal Berhenti (Sebelumnya)</label>
                    <input type="date" name="tanggal_berhenti_sebelumnya" id="tanggal_berhenti_sebelumnya" class="{{ $inputClasses }}" value="{{ old('tanggal_berhenti_sebelumnya') }}">
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
                        <option value="{{ $cabang->nama_cabang }}">{{ $cabang->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="plat" class="{{ $labelClasses }}">Nomor Plat</label>
                    <input type="text" name="plat" id="plat" class="{{ $inputClasses }}" placeholder="Nomor plat kendaraan">
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
                <textarea name="catatan" id="catatan" rows="4" class="{{ $inputClasses }}" placeholder="Tambahkan catatan khusus (opsional)">{{ old('catatan') }}</textarea>
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
                        <option value="{{ $pajak->nama_status }}">{{ $pajak->nama_status }} - {{ $pajak->keterangan }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="jkn" class="{{ $labelClasses }}">JKN</label>
                    <input type="text" name="jkn" id="jkn" class="{{ $inputClasses }}" placeholder="Nomor JKN/BPJS">
                </div>

                <div>
                    <label for="no_ketenagakerjaan" class="{{ $labelClasses }}">BP Jamsostek</label>
                    <input type="text" name="no_ketenagakerjaan" id="no_ketenagakerjaan" class="{{ $inputClasses }}" placeholder="Nomor BP Jamsostek">
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
                    Simpan Data Karyawan
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

            // Fungsi Untuk Memperbarui Opsi Pekerjaan
            function updatePekerjaanOptions(){
                //Bersihkan Opsi Pekerjaan
                pekerjaanSelect.innerHTML = '<option value="">-- Pilih Pekerjaan --</option>'

                const selectedDivisi = divisiSelect.value
                if(selectedDivisi && pekerjaanByDivisi[selectedDivisi]){
                    pekerjaanByDivisi[selectedDivisi].forEach(function(pekerjaan){
                        const option = document.createElement('option')
                        option.value = pekerjaan
                        option.textContent = pekerjaan
                        pekerjaanSelect.appendChild(option)
                    })
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
                    Menyimpan...
                `;
            });

            // Add focus effects for better UX
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.closest('div')?.classList.add('ring-2', 'ring-blue-200');
                });

                input.addEventListener('blur', function() {
                    this.closest('div')?.classList.remove('ring-2', 'ring-blue-200');
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
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
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

        /* Improved shadow effects */
        .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Enhanced hover effects */
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
