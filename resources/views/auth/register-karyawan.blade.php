<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Karyawan - AYPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-2xl sm:mx-auto">
        <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-green-600 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-3 sm:rounded-3xl"></div>
        <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-12">
            <div class="max-w-xl mx-auto">
                <div class="divide-y divide-gray-200">
                    <div class="py-4 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                        <div class="text-center mb-6">
                            <h2 class="text-3xl font-extrabold text-gray-900">Registrasi Karyawan</h2>
                            <p class="mt-2 text-sm text-gray-600">Daftarkan diri Anda pada sistem AYPSIS</p>
                        </div>

                        <!-- Back to Login -->
                        <div class="text-center mb-4">
                            <a href="{{ route('login') }}" class="text-green-600 hover:text-green-800 text-sm">
                                ← Kembali ke Login
                            </a>
                        </div>

                        <!-- Flash Messages -->
                        @if (session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                <strong class="font-bold">Sukses!</strong>
                                <span class="block sm:inline"> {{ session('success') }}</span>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <strong class="font-bold">Gagal!</strong>
                                <span class="block sm:inline"> {{ session('error') }}</span>
                            </div>
                        @endif

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <strong class="font-bold">Error!</strong>
                                <ul class="list-disc list-inside text-sm mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('karyawan.store') }}" class="space-y-6 pt-4">
                            @csrf

                            <!-- Tipe Karyawan -->
                            <div>
                                <label for="tipe_karyawan" class="block text-sm font-semibold text-gray-700">Tipe Karyawan *</label>
                                <select name="tipe_karyawan" id="tipe_karyawan" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm bg-gray-50">
                                    <option value="tetap" {{ old('tipe_karyawan', 'tetap') === 'tetap' ? 'selected' : '' }}>Karyawan Tetap (ABK / Staff / Supir AYP)</option>
                                    <option value="tidak_tetap" {{ old('tipe_karyawan') === 'tidak_tetap' ? 'selected' : '' }}>Karyawan Tidak Tetap (Sopir / Pekerja Lepas)</option>
                                </select>
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <h3 class="text-md font-bold text-gray-800 mb-4">Informasi Pribadi</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Nama Lengkap -->
                                    <div>
                                        <label for="nama_lengkap" class="block text-xs font-medium text-gray-700">Nama Lengkap *</label>
                                        <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                    </div>

                                    <!-- Nama Panggilan -->
                                    <div>
                                        <label for="nama_panggilan" class="block text-xs font-medium text-gray-700">Nama Panggilan *</label>
                                        <input type="text" name="nama_panggilan" id="nama_panggilan" value="{{ old('nama_panggilan') }}" required
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                    </div>

                                    <!-- NIK (Only Karyawan Tetap) -->
                                    <div id="nik_container">
                                        <label for="nik" class="block text-xs font-medium text-gray-700">NIK *</label>
                                        <input type="text" name="nik" id="nik" value="{{ old('nik') }}" required maxlength="25" pattern="[0-9]+"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500" placeholder="Masukkan NIK (angka saja)">
                                        <p class="text-[10px] text-gray-500 mt-1">Hanya angka, tidak boleh ada huruf</p>
                                    </div>

                                    <!-- No Ketenagakerjaan (Only Karyawan Tetap) -->
                                    <div id="no_ketenagakerjaan_container">
                                        <label for="no_ketenagakerjaan" class="block text-xs font-medium text-gray-700">No. Ketenagakerjaan</label>
                                        <input type="text" name="no_ketenagakerjaan" id="no_ketenagakerjaan" value="{{ old('no_ketenagakerjaan') }}"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                        <p class="text-[10px] text-gray-500 mt-1">Opsional - jika sudah memiliki</p>
                                    </div>
                                    
                                    <!-- NIK KTP (Shared/Conditional) -->
                                    <div>
                                        <label for="nik_ktp" class="block text-xs font-medium text-gray-700">Nomor KTP (NIK KTP)</label>
                                        <input type="text" name="nik_ktp" id="nik_ktp" value="{{ old('nik_ktp') }}"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500" placeholder="Masukkan 16 digit No KTP">
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-xs font-medium text-gray-700">Email</label>
                                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500" placeholder="contoh@email.com">
                                    </div>

                                    <!-- Jenis Kelamin (Only Karyawan Tidak Tetap) -->
                                    <div class="karyawan-tidak-tetap-only">
                                        <label for="jenis_kelamin" class="block text-xs font-medium text-gray-700">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" id="jenis_kelamin"
                                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500 bg-white">
                                            <option value="">-- Pilih Jenis Kelamin --</option>
                                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>

                                    <!-- Agama (Only Karyawan Tidak Tetap) -->
                                    <div class="karyawan-tidak-tetap-only">
                                        <label for="agama" class="block text-xs font-medium text-gray-700">Agama</label>
                                        <select name="agama" id="agama"
                                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500 bg-white">
                                            <option value="">-- Pilih Agama --</option>
                                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                            <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                            <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Pekerjaan -->
                            <div class="border-t border-gray-200 pt-4">
                                <h3 class="text-md font-bold text-gray-800 mb-4">Informasi Pekerjaan & Cabang</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Pekerjaan (Dynamic) -->
                                    <div>
                                        <label for="pekerjaan" class="block text-xs font-medium text-gray-700">Posisi yang Dilamar *</label>
                                        <select name="pekerjaan" id="pekerjaan" required
                                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500 bg-white">
                                            <option value="">Pilih posisi...</option>
                                            @foreach($pekerjaans as $p)
                                                <option value="{{ $p->nama_pekerjaan }}" {{ old('pekerjaan') == $p->nama_pekerjaan ? 'selected' : '' }}>{{ $p->nama_pekerjaan }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Cabang (Only Karyawan Tidak Tetap) -->
                                    <div class="karyawan-tidak-tetap-only">
                                        <label for="cabang" class="block text-xs font-medium text-gray-700">Kantor Cabang AYP</label>
                                        <select name="cabang" id="cabang"
                                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500 bg-white">
                                            <option value="">-- Pilih Kantor Cabang AYP --</option>
                                            <option value="JAKARTA" {{ old('cabang') == 'JAKARTA' ? 'selected' : '' }}>JAKARTA</option>
                                            <option value="BATAM" {{ old('cabang') == 'BATAM' ? 'selected' : '' }}>BATAM</option>
                                            <option value="TANJUNG PINANG" {{ old('cabang') == 'TANJUNG PINANG' ? 'selected' : '' }}>TANJUNG PINANG</option>
                                        </select>
                                    </div>

                                    <!-- Tanggal Masuk (Only Karyawan Tidak Tetap) -->
                                    <div class="karyawan-tidak-tetap-only">
                                        <label for="tanggal_masuk" class="block text-xs font-medium text-gray-700">Tanggal Masuk Kerja</label>
                                        <input type="date" name="tanggal_masuk" id="tanggal_masuk" value="{{ old('tanggal_masuk') }}"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                    </div>

                                    <!-- Status Pajak (Only Karyawan Tidak Tetap) -->
                                    <div class="karyawan-tidak-tetap-only">
                                        <label for="status_pajak" class="block text-xs font-medium text-gray-700">Status Pajak</label>
                                        <select name="status_pajak" id="status_pajak"
                                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500 bg-white">
                                            <option value="">-- Pilih Status Pajak --</option>
                                            @foreach($pajaks as $pajak)
                                                <option value="{{ $pajak->nama_status }}" {{ old('status_pajak') == $pajak->nama_status ? 'selected' : '' }}>{{ $pajak->nama_status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Alamat -->
                            <div class="border-t border-gray-200 pt-4">
                                <h3 class="text-md font-bold text-gray-800 mb-4">Informasi Alamat & Domisili</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Alamat Lengkap -->
                                    <div class="md:col-span-2">
                                        <label for="alamat" class="block text-xs font-medium text-gray-700">Alamat Lengkap *</label>
                                        <textarea name="alamat" id="alamat" rows="3" required
                                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">{{ old('alamat') }}</textarea>
                                    </div>

                                    <!-- RT/RW (Only Karyawan Tidak Tetap) -->
                                    <div class="karyawan-tidak-tetap-only">
                                        <label for="rt_rw" class="block text-xs font-medium text-gray-700">RT/RW</label>
                                        <input type="text" name="rt_rw" id="rt_rw" value="{{ old('rt_rw') }}" placeholder="00/00"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                    </div>

                                    <!-- Kelurahan (Only Karyawan Tidak Tetap) -->
                                    <div class="karyawan-tidak-tetap-only">
                                        <label for="kelurahan" class="block text-xs font-medium text-gray-700">Kelurahan</label>
                                        <input type="text" name="kelurahan" id="kelurahan" value="{{ old('kelurahan') }}"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                    </div>

                                    <!-- Kecamatan (Only Karyawan Tidak Tetap) -->
                                    <div class="karyawan-tidak-tetap-only">
                                        <label for="kecamatan" class="block text-xs font-medium text-gray-700">Kecamatan</label>
                                        <input type="text" name="kecamatan" id="kecamatan" value="{{ old('kecamatan') }}"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                    </div>

                                    <!-- Kabupaten/Kota (Only Karyawan Tidak Tetap) -->
                                    <div class="karyawan-tidak-tetap-only">
                                        <label for="kabupaten" class="block text-xs font-medium text-gray-700">Kabupaten/Kota</label>
                                        <input type="text" name="kabupaten" id="kabupaten" value="{{ old('kabupaten') }}"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                    </div>

                                    <!-- Provinsi (Only Karyawan Tidak Tetap) -->
                                    <div class="karyawan-tidak-tetap-only">
                                        <label for="provinsi" class="block text-xs font-medium text-gray-700">Provinsi</label>
                                        <input type="text" name="provinsi" id="provinsi" value="{{ old('provinsi') }}"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                    </div>

                                    <!-- Kode Pos (Only Karyawan Tidak Tetap) -->
                                    <div class="karyawan-tidak-tetap-only">
                                        <label for="kode_pos" class="block text-xs font-medium text-gray-700">Kode Pos</label>
                                        <input type="text" name="kode_pos" id="kode_pos" value="{{ old('kode_pos') }}"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                    </div>

                                    <!-- No Telepon -->
                                    <div>
                                        <label for="no_telepon" class="block text-xs font-medium text-gray-700">No. Telepon / HP *</label>
                                        <input type="tel" name="no_telepon" id="no_telepon" value="{{ old('no_telepon') }}" required
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Alasan Pendaftaran -->
                            <div class="border-t border-gray-200 pt-4">
                                <div>
                                    <label for="alasan_pendaftaran" class="block text-xs font-medium text-gray-700">Alasan Pendaftaran *</label>
                                    <textarea name="alasan_pendaftaran" id="alasan_pendaftaran" rows="3" required maxlength="500"
                                              placeholder="Jelaskan mengapa Anda ingin bergabung dengan AYPSIS..."
                                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">{{ old('alasan_pendaftaran') }}</textarea>
                                    <p class="text-[10px] text-gray-500 mt-1">Maksimal 500 karakter</p>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button type="submit" id="submit_button" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    👥 Daftar Sebagai Karyawan
                                </button>
                            </div>

                            <!-- Info -->
                            <div class="text-center text-xs text-gray-500 pt-4">
                                <p>Data Anda akan direview oleh administrator.</p>
                                <p>Anda akan dihubungi jika pendaftaran disetujui.</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tipeKaryawan = document.getElementById('tipe_karyawan');
            const nikContainer = document.getElementById('nik_container');
            const nikInput = document.getElementById('nik');
            const noKetenagakerjaanContainer = document.getElementById('no_ketenagakerjaan_container');
            const submitButton = document.getElementById('submit_button');
            const tidakTetapElements = document.querySelectorAll('.karyawan-tidak-tetap-only');

            function toggleFields() {
                if (tipeKaryawan.value === 'tidak_tetap') {
                    // Hide Tetap elements
                    nikContainer.style.display = 'none';
                    nikInput.removeAttribute('required');
                    noKetenagakerjaanContainer.style.display = 'none';

                    // Show Tidak Tetap elements
                    tidakTetapElements.forEach(el => el.style.display = 'block');
                    submitButton.innerHTML = '👥 Daftar Sebagai Karyawan Tidak Tetap';
                } else {
                    // Show Tetap elements
                    nikContainer.style.display = 'block';
                    nikInput.setAttribute('required', 'required');
                    noKetenagakerjaanContainer.style.display = 'block';

                    // Hide Tidak Tetap elements
                    tidakTetapElements.forEach(el => el.style.display = 'none');
                    submitButton.innerHTML = '👥 Daftar Sebagai Karyawan';
                }
            }

            tipeKaryawan.addEventListener('change', toggleFields);
            toggleFields(); // Run on initial load to handle old input state
        });
    </script>
</body>
</html>
