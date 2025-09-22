@extends('layouts.app')

@section('title', isset($karyawan) ? 'Edit Data Karyawan - ' . $karyawan->nama_lengkap : 'Pendaftaran Karyawan Baru')
@section('page_title', isset($karyawan) ? 'Edit Data Karyawan' : 'Pendaftaran Karyawan Baru')

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

    @if (count($errors) > 0)
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
            <p class="font-bold">Terdapat kesalahan dalam formulir:</p>
            <ul class="list-disc list-inside mt-2">
                @foreach ((array) $errors as $error )
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ isset($karyawan) ? 'Formulir Edit Data Karyawan' : 'Formulir Pendaftaran Karyawan Baru' }}</h2>
            <p class="text-gray-600 mt-1">{{ isset($karyawan) ? 'Perbarui data karyawan Anda di bawah ini.' : 'Lengkapi formulir di bawah untuk mendaftarkan diri sebagai karyawan.' }}</p>
        </div>
            <form action="{{ isset($karyawan) ? route('karyawan.onboarding-update', $karyawan->id) : route('karyawan.store') }}" method="POST" class="divide-y divide-gray-100">
            @csrf
            @if(isset($karyawan))
                @method('PUT')
            @endif
            @php
                // Definisikan kelas Tailwind yang sederhana dan konsisten
                $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-[10px] p-2.5";
                $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-200 shadow-sm text-[10px] p-2.5";
                $selectClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-[10px] p-2.5";
                $labelClasses = "block text-xs font-medium text-gray-700";
                $fieldsetClasses = "border p-4 rounded-md mb-4";
                $legendClasses = "text-lg font-semibold text-gray-800 px-2";
            @endphp
            {{-- Informasi Pribadi --}}
            <fieldset class="{{ $fieldsetClasses }}">
                <legend class="{{ $legendClasses }}">Informasi Pribadi</legend>
                <div class="form-section pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nik" class="{{ $labelClasses }}">NIK <span class="text-red-500">*</span></label>
                            <input type="text" name="nik" id="nik" class="{{ $inputClasses }}" required placeholder="Masukkan NIK (angka saja, tanpa huruf)" pattern="[0-9]+" value="{{ old('nik', $karyawan->nik ?? '') }}">
                            <p class="text-xs text-gray-500 mt-1">NIK harus berupa angka saja, tidak boleh ada huruf</p>
                            <div id="nikError" class="text-xs text-red-600 mt-1 hidden">NIK harus berupa angka saja, tidak boleh ada huruf</div>
                        </div>
                        <div>
                            <label for="nama_lengkap" class="{{ $labelClasses }}">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="{{ $inputClasses }}" required placeholder="Masukkan nama lengkap" value="{{ old('nama_lengkap', $karyawan->nama_lengkap ?? '') }}">
                        </div>
                        <div>
                            <label for="nama_panggilan" class="{{ $labelClasses }}">Nama Panggilan<span class="text-red-500">*</span></label>
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
                                <option value="L" {{ old('jenis_kelamin', $karyawan->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $karyawan->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="agama" class="{{ $labelClasses }}">Agama</label>
                            <select name="agama" id="agama" class="{{ $selectClasses }}">
                                <option value="">-- Pilih Agama --</option>
                                <option value="Islam" {{ old('agama', $karyawan->agama ?? '') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama', $karyawan->agama ?? '') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama', $karyawan->agama ?? '') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama', $karyawan->agama ?? '') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Budha" {{ old('agama', $karyawan->agama ?? '') == 'Budha' ? 'selected' : '' }}>Budha</option>
                                <option value="Konghucu" {{ old('agama', $karyawan->agama ?? '') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                <option value="Lainnya" {{ old('agama', $karyawan->agama ?? '') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label for="status_perkawinan" class="{{ $labelClasses }}">Status Pernikahan</label>
                            <select name="status_perkawinan" id="status_perkawinan" class="{{ $selectClasses }}">
                                <option value="">-- Pilih Status Perkawinan --</option>
                                <option value="Menikah" {{ old('status_perkawinan', $karyawan->status_perkawinan ?? '') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                <option value="Belum Menikah" {{ old('status_perkawinan', $karyawan->status_perkawinan ?? '') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                <option value="Janda" {{ old('status_perkawinan', $karyawan->status_perkawinan ?? '') == 'Janda' ? 'selected' : '' }}>Janda</option>
                                <option value="Duda" {{ old('status_perkawinan', $karyawan->status_perkawinan ?? '') == 'Duda' ? 'selected' : '' }}>Duda</option>
                            </select>
                        </div>
                        <div>
                            <label for="no_hp" class="{{ $labelClasses }}">Nomor Handphone/Whatsapp</label>
                            <input type="tel" name="no_hp" id="no_hp" class="{{ $inputClasses }}" placeholder="08xxxxxxxxxx" value="{{ old('no_hp', $karyawan->no_hp ?? '') }}">
                            <div id="noHpError" class="text-xs text-red-600 mt-1 hidden">Nomor handphone harus berupa angka saja, tidak boleh ada huruf</div>
                        </div>
                        <div>
                            <label for="ktp" class="{{ $labelClasses }}">Nomor KTP <span class="text-red-500">*</span></label>
                            <input type="text" name="ktp" id="ktp" class="{{ $inputClasses }}" placeholder="Masukkan nomor KTP (16 digit angka saja, tanpa huruf)" maxlength="16" pattern="[0-9]{16}" required value="{{ old('ktp', $karyawan->ktp ?? '') }}">
                            <p class="text-xs text-gray-500 mt-1">Nomor KTP harus tepat 16 digit angka saja, tidak boleh ada huruf</p>
                            <div id="ktpError" class="text-xs text-red-600 mt-1 hidden">Nomor KTP harus tepat 16 digit angka saja, tidak boleh ada huruf</div>
                            <div id="ktpWarning" class="text-xs mt-1 hidden"></div>
                        </div>
                        <div>
                            <label for="kk" class="{{ $labelClasses }}">Nomor KK <span class="text-red-500">*</span></label>
                            <input type="text" name="kk" id="kk" class="{{ $inputClasses }}" placeholder="Masukkan nomor KK (16 digit angka saja, tanpa huruf)" maxlength="16" pattern="[0-9]{16}" required value="{{ old('kk', $karyawan->kk ?? '') }}">
                            <p class="text-xs text-gray-500 mt-1">Nomor KK harus tepat 16 digit angka saja, tidak boleh ada huruf</p>
                            <div id="kkError" class="text-xs text-red-600 mt-1 hidden">Nomor KK harus tepat 16 digit angka saja, tidak boleh ada huruf</div>
                            <div id="kkWarning" class="text-xs mt-1 hidden"></div>
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- Informasi Perusahaan --}}
            <fieldset class="{{ $fieldsetClasses }}">
                <legend class="{{ $legendClasses }}">Informasi Perusahaan</legend>
                <div class="form-section pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                            <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="{{ $inputClasses }}" value="{{ old('tanggal_masuk', $karyawan->tanggal_masuk ?? '') }}">
                        </div>
                        <div>
                            <label for="tanggal_berhenti" class="{{ $labelClasses }}">Tanggal Berhenti</label>
                            <input type="date" name="tanggal_berhenti" id="tanggal_berhenti" class="{{ $inputClasses }}" value="{{ old('tanggal_berhenti', $karyawan->tanggal_berhenti ?? '') }}">
                        </div>
                        <div>
                            <label for="tanggal_masuk_sebelumnya" class="{{ $labelClasses }}">Tanggal Masuk (Sebelumnya)</label>
                            <input type="date" name="tanggal_masuk_sebelumnya" id="tanggal_masuk_sebelumnya" class="{{ $inputClasses }}" value="{{ old('tanggal_masuk_sebelumnya', $karyawan->tanggal_masuk_sebelumnya ?? '') }}">
                        </div>
                        <div>
                            <label for="tanggal_berhenti_sebelumnya" class="{{ $labelClasses }}">Tanggal Berhenti (Sebelumnya)</label>
                            <input type="date" name="tanggal_berhenti_sebelumnya" id="tanggal_berhenti_sebelumnya" class="{{ $inputClasses }}" value="{{ old('tanggal_berhenti_sebelumnya', $karyawan->tanggal_berhenti_sebelumnya ?? '') }}">
                        </div>
                        <div>
                            <label for="nik_supervisor" class="{{ $labelClasses }}">NIK Supervisor</label>
                            <input type="text" name="nik_supervisor" id="nik_supervisor" class="{{ $inputClasses }}" placeholder="NIK supervisor" value="{{ old('nik_supervisor', $karyawan->nik_supervisor ?? '') }}">
                        </div>
                        <div>
                            <label for="supervisor" class="{{ $labelClasses }}">Nama Supervisor</label>
                            <input type="text" name="supervisor" id="supervisor" class="{{ $inputClasses }}" placeholder="Nama supervisor" value="{{ old('supervisor', $karyawan->supervisor ?? '') }}">
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
                </div>
            </fieldset>

            {{-- Informasi Alamat --}}
            <fieldset class="{{ $fieldsetClasses }}">
                <legend class="{{ $legendClasses }}">Informasi Alamat</legend>
                <div class="form-section pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="alamat" class="{{ $labelClasses }}">Alamat</label>
                            <input type="text" name="alamat" id="alamat" class="{{ $inputClasses }}" placeholder="Jalan, nomor rumah" value="{{ old('alamat', $karyawan->alamat ?? '') }}">
                        </div>
                        <div>
                            <label for="rt_rw" class="{{ $labelClasses }}">RT/RW</label>
                            <input type="text" name="rt_rw" id="rt_rw" class="{{ $inputClasses }}" placeholder="001/002" value="{{ old('rt_rw', $karyawan->rt_rw ?? '') }}">
                        </div>
                        <div>
                            <label for="kelurahan" class="{{ $labelClasses }}">Kelurahan</label>
                            <input type="text" name="kelurahan" id="kelurahan" class="{{ $inputClasses }}" placeholder="Nama kelurahan" value="{{ old('kelurahan', $karyawan->kelurahan ?? '') }}">
                        </div>
                        <div>
                            <label for="kecamatan" class="{{ $labelClasses }}">Kecamatan</label>
                            <input type="text" name="kecamatan" id="kecamatan" class="{{ $inputClasses }}" placeholder="Nama kecamatan" value="{{ old('kecamatan', $karyawan->kecamatan ?? '') }}">
                        </div>
                        <div>
                            <label for="kabupaten" class="{{ $labelClasses }}">Kabupaten</label>
                            <input type="text" name="kabupaten" id="kabupaten" class="{{ $inputClasses }}" placeholder="Nama kabupaten/kota" value="{{ old('kabupaten', $karyawan->kabupaten ?? '') }}">
                        </div>
                        <div>
                            <label for="provinsi" class="{{ $labelClasses }}">Provinsi</label>
                            <input type="text" name="provinsi" id="provinsi" class="{{ $inputClasses }}" placeholder="Nama provinsi" value="{{ old('provinsi', $karyawan->provinsi ?? '') }}">
                        </div>
                        <div>
                            <label for="kode_pos" class="{{ $labelClasses }}">Kode Pos</label>
                            <input type="text" name="kode_pos" id="kode_pos" class="{{ $inputClasses }}" placeholder="12345" value="{{ old('kode_pos', $karyawan->kode_pos ?? '') }}">
                        </div>
                        <div class="lg:col-span-2">
                            <label for="alamat_lengkap" class="{{ $labelClasses }}">Alamat Lengkap</label>
                            <textarea name="alamat_lengkap" id="alamat_lengkap" rows="3" class="{{ $readonlyInputClasses }}" readonly placeholder="Alamat lengkap akan muncul otomatis">{{ old('alamat_lengkap', $karyawan->alamat_lengkap ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- Catatan --}}
            <fieldset class="{{ $fieldsetClasses }}">
                <legend class="{{ $legendClasses }}">Catatan</legend>
                <div class="form-section pt-4">
                    <div>
                        <label for="catatan" class="{{ $labelClasses }}">Catatan</label>
                        <textarea name="catatan" id="catatan" rows="4" class="{{ $inputClasses }}" placeholder="Tambahkan catatan khusus (opsional)">{{ old('catatan', $karyawan->catatan ?? '') }}</textarea>
                    </div>
                </div>
            </fieldset>

            {{-- Informasi Bank --}}
            <fieldset class="{{ $fieldsetClasses }}">
                <legend class="{{ $legendClasses }}">Informasi Bank</legend>
                <div class="form-section pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_bank" class="{{ $labelClasses }}">Nama Bank</label>
                            <select name="nama_bank" id="nama_bank" class="{{ $selectClasses }}">
                                <option value="">-- Pilih Nama Bank --</option>
                                @if(isset($banks))
                                    @foreach($banks as $bank)
                                    <option value="{{ $bank->name }}" {{ old('nama_bank', $karyawan->nama_bank ?? '') == $bank->name ? 'selected' : '' }}>{{ $bank->name }} @if($bank->code) ({{ $bank->code }}) @endif</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label for="bank_cabang" class="{{ $labelClasses }}">Cabang Bank</label>
                            <input type="text" name="bank_cabang" id="bank_cabang" class="{{ $inputClasses }}" placeholder="Contoh: Cabang Jakarta Pusat" value="{{ old('bank_cabang', $karyawan->bank_cabang ?? '') }}">
                        </div>
                        <div>
                            <label for="akun_bank" class="{{ $labelClasses }}">Nomor Rekening</label>
                            <input type="text" name="akun_bank" id="akun_bank" class="{{ $inputClasses }}" placeholder="Nomor rekening bank" value="{{ old('akun_bank', $karyawan->akun_bank ?? '') }}">
                        </div>
                        <div class="lg:col-span-2">
                            <label for="atas_nama" class="{{ $labelClasses }}">Atas Nama</label>
                            <input type="text" name="atas_nama" id="atas_nama" class="{{ $inputClasses }}" placeholder="Nama pemilik rekening" value="{{ old('atas_nama', $karyawan->atas_nama ?? '') }}">
                            <p class="text-xs text-blue-600 mt-1 font-medium">💡 <strong>Auto-fill:</strong> Field ini akan terisi otomatis saat Anda mengetik "Nama Lengkap" di atas. Jika nama rekening berbeda, Anda bisa mengubahnya manual.</p>
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- Informasi Pajak & JKN --}}
            <fieldset class="{{ $fieldsetClasses }}">
                <legend class="{{ $legendClasses }}">Informasi Pajak & JKN</legend>
                <div class="form-section pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                </div>
            </fieldset>

            <div class="flex justify-end mt-8">
                <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ isset($karyawan) ? 'Update Data Karyawan' : 'Simpan Data Karyawan' }}
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

            // Fungsi untuk membersihkan input identitas (hanya angka)
            function formatIdentityNumber(input) {
                const value = input.value.replace(/\D/g, '');
                input.value = value;
            }

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
            const noHpInput = document.getElementById('no_hp');
            const ktpError = document.getElementById('ktpError');
            const kkError = document.getElementById('kkError');
            const nikError = document.getElementById('nikError');
            const noHpError = document.getElementById('noHpError');
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

            // Fungsi validasi No HP - hanya angka
            function validateNoHp(input, errorElement) {
                const value = input.value.trim();
                const isValid = /^\d+$/.test(value) || value === ''; // Hanya angka atau kosong

                if (value === '') {
                    errorElement.classList.add('hidden');
                    input.classList.remove('border-red-500');
                    return true;
                }

                if (!isValid) {
                    errorElement.textContent = 'Nomor handphone harus berupa angka saja, tidak boleh ada huruf';
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

            // Event listener untuk No HP
            if (noHpInput) {
                noHpInput.addEventListener('input', function() {
                    formatIdentityNumber(this);
                    validateNoHp(this, noHpError);
                });

                noHpInput.addEventListener('blur', function() {
                    validateNoHp(this, noHpError);
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

                    // Validasi No HP
                    if (noHpInput && noHpInput.value.trim() !== '') {
                        if (!validateNoHp(noHpInput, noHpError)) {
                            isValid = false;
                            if (isValid) noHpInput.focus();
                        }
                    }

                    if (!isValid) {
                        e.preventDefault();
                        alert('Mohon perbaiki kesalahan pada form sebelum menyimpan.');
                        return false;
                    }
                });
            }

            // Add loading state on form submission
            const submitButton = form.querySelector('button[type="submit"]');

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

            // Auto-resize textarea
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
            });
        });
    </script>

    <style>
        /* Loading spinner animation */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Smooth transitions */
        * {
            transition: all 0.2s ease;
        }
    </style>
@endpush
