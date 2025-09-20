@extends('layouts.app')

@section('title', 'Tambah Karyawan')
@section('page_title','Tambah Karyawan')

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
            <h2 class="text-2xl font-bold text-gray-800">Formulir Karyawan Baru</h2>
            <p class="text-gray-600 mt-1">Lengkapi formulir di bawah untuk menambah karyawan baru</p>
        </div>

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

        <form action="{{ $formAction }}" method="POST">
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
                        <input type="text" name="nik" id="nik" class="{{ $inputClasses }}" required placeholder="Masukkan NIK (angka saja, tanpa huruf)" maxlength="25" pattern="[0-9]+">
                        <p class="text-xs text-gray-500 mt-1">NIK harus berupa angka saja, tidak boleh ada huruf</p>
                                                <div id="nikError" class="text-xs text-red-600 mt-1 hidden">NIK harus berupa angka saja, tidak boleh ada huruf</div>
                    </div>

                    <div>
                        <label for="nama_lengkap" class="{{ $labelClasses }}">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="{{ $inputClasses }}" required placeholder="Masukkan nama lengkap">
                    </div>

                    <div>
                        <label for="nama_panggilan" class="{{ $labelClasses }}">Nama Panggilan <span class="text-red-500">*</span></label>
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
                    <label for="ktp" class="{{ $labelClasses }}">Nomor KTP <span class="text-red-500">*</span></label>
                    <input type="text" name="ktp" id="ktp" class="{{ $inputClasses }}" placeholder="Masukkan nomor KTP (16 digit angka saja, tanpa huruf)" maxlength="16" pattern="[0-9]{16}" required>
                    <p class="text-xs text-gray-500 mt-1">Nomor KTP harus tepat 16 digit angka saja, tidak boleh ada huruf</p>
                    <div id="ktpError" class="text-xs text-red-600 mt-1 hidden">Nomor KTP harus tepat 16 digit angka saja, tidak boleh ada huruf</div>
                    <div id="ktpWarning" class="text-xs mt-1 hidden"></div>
                </div>

                <div>
                    <label for="kk" class="{{ $labelClasses }}">Nomor KK <span class="text-red-500">*</span></label>
                    <input type="text" name="kk" id="kk" class="{{ $inputClasses }}" placeholder="Masukkan nomor KK (16 digit angka saja, tanpa huruf)" maxlength="16" pattern="[0-9]{16}" required>
                    <p class="text-xs text-gray-500 mt-1">Nomor KK harus tepat 16 digit angka saja, tidak boleh ada huruf</p>
                    <div id="kkError" class="text-xs text-red-600 mt-1 hidden">Nomor KK harus tepat 16 digit angka saja, tidak boleh ada huruf</div>
                    <div id="kkWarning" class="text-xs mt-1 hidden"></div>
                </div>
            </div>
        </fieldset>

        {{-- Informasi Perusahaan --}}
        <fieldset class="border p-4 rounded-md mb-4">
            <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Perusahaan</legend>
            <div class="form-section pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        <fieldset class="border p-4 rounded-md mb-4">
            <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Alamat</legend>
            <div class="form-section pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        <fieldset class="border p-4 rounded-md mb-4">
            <legend class="text-lg font-semibold text-gray-800 px-2">Catatan</legend>
            <div class="form-section pt-4">
                <div>
                    <label for="catatan" class="{{ $labelClasses }}">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="4" class="{{ $inputClasses }}" placeholder="Tambahkan catatan khusus (opsional)">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </fieldset>

        {{-- Informasi Bank --}}
        <fieldset class="border p-4 rounded-md mb-4">
            <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Bank</legend>
            <div class="form-section pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_bank" class="{{ $labelClasses }}">Nama Bank</label>
                        <select name="nama_bank" id="nama_bank" class="{{ $selectClasses }}">
                            <option value="">-- Pilih Nama Bank --</option>
                            @foreach($banks as $bank)
                            <option value="{{ $bank->name }}" {{ old('nama_bank') == $bank->name ? 'selected' : '' }}>
                                {{ $bank->name }} @if($bank->code) ({{ $bank->code }}) @endif
                            </option>
                            @endforeach
                        </select>
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
                    <p class="text-xs text-blue-600 mt-1 font-medium">💡 <strong>Auto-fill:</strong> Field ini akan terisi otomatis saat Anda mengetik "Nama Lengkap" di atas. Jika nama rekening berbeda, Anda bisa mengubahnya manual.</p>
                </div>
            </div>
        </fieldset>

        {{-- Informasi Pajak & JKN --}}
        <fieldset class="border p-4 rounded-md mb-4">
            <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Pajak & JKN</legend>
            <div class="form-section pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            </div>
        </fieldset>

        <div class="flex justify-end mt-8">
            <a href="{{ route('master.karyawan.index') }}" class="inline-flex justify-center py-2 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Simpan Data Karyawan
            </button>
        </div>
        </form>
    </div>
</div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const divisiSelect = document.getElementById('divisi');
            const pekerjaanSelect = document.getElementById('pekerjaan');
            const alamatFields = [
                document.getElementById('alamat'),
                document.getElementById('rt_rw'),
                document.getElementById('kelurahan'),
                document.getElementById('kecamatan'),
                document.getElementById('kabupaten'),
                document.getElementById('provinsi'),
                document.getElementById('kode_pos'),
            ];

            const alamatLengkapTextarea = document.getElementById('alamat_lengkap');

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
                        pekerjaanSelect.appendChild(option);
                    });
                }
            }

            // Fungsi untuk memperbarui alamat lengkap
            function updateAlamatLengkap() {
                const alamatParts = alamatFields.map(field => field.value.trim()).filter(part => part !== '');
                const combinedAddress = alamatParts.join(', ');
                alamatLengkapTextarea.value = combinedAddress;
            }

            // Jalankan fungsi saat halaman dimuat
            updatePekerjaanOptions();
            updateAlamatLengkap();

            // Event listener untuk perubahan dropdown divisi
            divisiSelect.addEventListener('change', updatePekerjaanOptions);

            // Event listener untuk setiap input alamat
            alamatFields.forEach(field => {
                field.addEventListener('input', updateAlamatLengkap);
            });

            // Auto-fill nama lengkap ke atas nama
            const namaLengkapInput = document.getElementById('nama_lengkap');
            const atasNamaInput = document.getElementById('atas_nama');

            if (namaLengkapInput && atasNamaInput) {
                namaLengkapInput.addEventListener('input', function() {
                    atasNamaInput.value = this.value.trim();
                });
            }

            // Validasi KTP dan KK
            const ktpInput = document.getElementById('ktp');
            const kkInput = document.getElementById('kk');
            const nikInput = document.getElementById('nik');
            const ktpError = document.getElementById('ktpError');
            const kkError = document.getElementById('kkError');
            const nikError = document.getElementById('nikError');
            const ktpWarning = document.getElementById('ktpWarning');
            const kkWarning = document.getElementById('kkWarning');
            const form = document.querySelector('form');

            // Fungsi validasi nomor identitas (KTP/KK) - harus tepat 16 digit
            function validateIdentityNumber(input, errorElement, fieldName) {
                const value = input.value.trim();
                const isValid = /^\d{16}$/.test(value);

                if (value === '') {
                    // Kosongkan error jika field kosong (karena mungkin tidak wajib)
                    errorElement.classList.add('hidden');
                    input.classList.remove('border-red-500');
                    return true;
                }

                if (!isValid) {
                    errorElement.textContent = `${fieldName} harus tepat 16 digit angka saja, tidak boleh ada huruf`;
                    errorElement.classList.remove('hidden');
                    input.classList.add('border-red-500');
                    return false;
                } else {
                    errorElement.classList.add('hidden');
                    input.classList.remove('border-red-500');
                    return true;
                }
            }

            // Fungsi validasi NIK - lebih fleksibel, hanya perlu angka
            function validateNIK(input, errorElement) {
                const value = input.value.trim();
                const isValid = /^\d+$/.test(value) && value.length > 0; // Hanya angka, minimal 1 digit

                if (value === '') {
                    // Kosongkan error jika field kosong (karena mungkin tidak wajib)
                    errorElement.classList.add('hidden');
                    input.classList.remove('border-red-500');
                    return true;
                }

                if (!isValid) {
                    errorElement.textContent = 'NIK harus berupa angka saja, tidak boleh ada huruf';
                    errorElement.classList.remove('hidden');
                    input.classList.add('border-red-500');
                    return false;
                } else {
                    errorElement.classList.add('hidden');
                    input.classList.remove('border-red-500');
                    return true;
                }
            }

            // Fungsi untuk menampilkan warning saat input KTP/KK
            function showIdentityWarning(input, warningElement, fieldName) {
                const value = input.value.trim();
                const length = value.length;

                if (length > 0 && length < 16) {
                    warningElement.textContent = `${fieldName} membutuhkan 16 digit angka saja. Saat ini: ${length} digit`;
                    warningElement.classList.remove('hidden');
                    warningElement.classList.add('text-yellow-600');
                    warningElement.classList.remove('text-red-600');
                } else if (length === 16) {
                    warningElement.textContent = `✅ ${fieldName} lengkap (16 digit angka saja)`;
                    warningElement.classList.remove('hidden');
                    warningElement.classList.add('text-green-600');
                    warningElement.classList.remove('text-yellow-600', 'text-red-600');
                } else if (length > 16) {
                    warningElement.textContent = `❌ ${fieldName} terlalu panjang. Maksimal 16 digit angka saja`;
                    warningElement.classList.remove('hidden');
                    warningElement.classList.add('text-red-600');
                    warningElement.classList.remove('text-yellow-600', 'text-green-600');
                } else {
                    warningElement.classList.add('hidden');
                }
            }

            // Event listener untuk NIK
            if (nikInput) {
                nikInput.addEventListener('input', function() {
                    formatIdentityNumber(this);
                    validateNIK(this, nikError);
                });

                nikInput.addEventListener('blur', function() {
                    validateNIK(this, nikError);
                });
            }

            // Event listener untuk KTP
            if (ktpInput) {
                ktpInput.addEventListener('input', function() {
                    formatIdentityNumber(this);
                    validateIdentityNumber(this, ktpError, 'Nomor KTP');
                    showIdentityWarning(this, ktpWarning, 'KTP');
                });

                ktpInput.addEventListener('blur', function() {
                    validateIdentityNumber(this, ktpError, 'Nomor KTP');
                    showIdentityWarning(this, ktpWarning, 'KTP');
                });
            }

            // Event listener untuk KK
            if (kkInput) {
                kkInput.addEventListener('input', function() {
                    formatIdentityNumber(this);
                    validateIdentityNumber(this, kkError, 'Nomor KK');
                    showIdentityWarning(this, kkWarning, 'KK');
                });

                kkInput.addEventListener('blur', function() {
                    validateIdentityNumber(this, kkError, 'Nomor KK');
                    showIdentityWarning(this, kkWarning, 'KK');
                });
            }

            // Validasi sebelum submit
            if (form) {
                form.addEventListener('submit', function(e) {
                    let isValid = true;

                    // Validasi NIK
                    if (nikInput && nikInput.value.trim() !== '') {
                        if (!validateNIK(nikInput, nikError)) {
                            isValid = false;
                            nikInput.focus();
                        }
                    }

                    // Validasi KTP
                    if (ktpInput && ktpInput.value.trim() !== '') {
                        if (!validateIdentityNumber(ktpInput, ktpError, 'Nomor KTP')) {
                            isValid = false;
                            if (isValid) ktpInput.focus();
                        }
                    }

                    // Validasi KK
                    if (kkInput && kkInput.value.trim() !== '') {
                        if (!validateIdentityNumber(kkInput, kkError, 'Nomor KK')) {
                            isValid = false;
                            if (isValid) kkInput.focus();
                        }
                    }

                    if (!isValid) {
                        e.preventDefault();
                        alert('Mohon perbaiki kesalahan pada form sebelum menyimpan.');
                        return false;
                    }
                });
            }
        });
    </script>
@endpush
