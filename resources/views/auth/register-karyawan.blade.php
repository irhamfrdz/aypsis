<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Karyawan - AYPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-green-600 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
        <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
            <div class="max-w-md mx-auto">
                <div class="divide-y divide-gray-200">
                    <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                        <div class="text-center">
                            <h2 class="text-3xl font-extrabold text-gray-900">Registrasi Karyawan</h2>
                            <p class="mt-2 text-sm text-gray-600">Daftarkan diri sebagai karyawan AYPSIS</p>
                        </div>

                        <!-- Back to Login -->
                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-green-600 hover:text-green-800 text-sm">
                                ← Kembali ke Login
                            </a>
                        </div>

                        <!-- Error Messages -->
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

                        <form method="POST" action="{{ route('register.karyawan.store') }}" class="space-y-4">
                            @csrf

                            <!-- Nama Lengkap -->
                            <div>
                                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                            </div>

                            <!-- Nama Panggilan -->
                            <div>
                                <label for="nama_panggilan" class="block text-sm font-medium text-gray-700">Nama Panggilan *</label>
                                <input type="text" name="nama_panggilan" id="nama_panggilan" value="{{ old('nama_panggilan') }}" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                            </div>

                            <!-- NIK -->
                            <div>
                                <label for="nik" class="block text-sm font-medium text-gray-700">NIK *</label>
                                <input type="text" name="nik" id="nik" value="{{ old('nik') }}" required maxlength="20"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                                <p class="text-xs text-gray-500 mt-1">Nomor Induk Kependudukan (16 digit)</p>
                            </div>

                            <!-- No Ketenagakerjaan -->
                            <div>
                                <label for="no_ketenagakerjaan" class="block text-sm font-medium text-gray-700">No. Ketenagakerjaan</label>
                                <input type="text" name="no_ketenagakerjaan" id="no_ketenagakerjaan" value="{{ old('no_ketenagakerjaan') }}"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                                <p class="text-xs text-gray-500 mt-1">Opsional - jika sudah memiliki</p>
                            </div>

                            <!-- Alamat -->
                            <div>
                                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat *</label>
                                <textarea name="alamat" id="alamat" rows="3" required
                                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">{{ old('alamat') }}</textarea>
                            </div>

                            <!-- No Telepon -->
                            <div>
                                <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon *</label>
                                <input type="tel" name="no_telepon" id="no_telepon" value="{{ old('no_telepon') }}" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                            </div>

                            <!-- Pekerjaan -->
                            <div>
                                <label for="pekerjaan" class="block text-sm font-medium text-gray-700">Posisi yang Dilamar *</label>
                                <select name="pekerjaan" id="pekerjaan" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    <option value="">Pilih posisi...</option>
                                    <option value="Supir Truck" {{ old('pekerjaan') == 'Supir Truck' ? 'selected' : '' }}>Supir Truck</option>
                                    <option value="Supir Trailer" {{ old('pekerjaan') == 'Supir Trailer' ? 'selected' : '' }}>Supir Trailer</option>
                                    <option value="Krani" {{ old('pekerjaan') == 'Krani' ? 'selected' : '' }}>Krani</option>
                                    <option value="Admin" {{ old('pekerjaan') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="Staff Operasional" {{ old('pekerjaan') == 'Staff Operasional' ? 'selected' : '' }}>Staff Operasional</option>
                                    <option value="Lainnya" {{ old('pekerjaan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <!-- Alasan Pendaftaran -->
                            <div>
                                <label for="alasan_pendaftaran" class="block text-sm font-medium text-gray-700">Alasan Pendaftaran *</label>
                                <textarea name="alasan_pendaftaran" id="alasan_pendaftaran" rows="3" required maxlength="500"
                                          placeholder="Jelaskan mengapa Anda ingin bergabung dengan AYPSIS..."
                                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">{{ old('alasan_pendaftaran') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Maksimal 500 karakter</p>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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
</body>
</html>
