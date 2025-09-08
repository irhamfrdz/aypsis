<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi User - AYPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-600 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
        <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
            <div class="max-w-md mx-auto">
                <div class="divide-y divide-gray-200">
                    <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                        <div class="text-center">
                            <h2 class="text-3xl font-extrabold text-gray-900">Registrasi User</h2>
                            <p class="mt-2 text-sm text-gray-600">Buat akun user untuk karyawan terdaftar</p>
                        </div>

                        <!-- Back to Login -->
                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 text-sm">
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

                        @if($karyawans->isEmpty())
                            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                                <strong class="font-bold">Informasi!</strong>
                                <p class="text-sm mt-1">Saat ini tidak ada karyawan yang dapat didaftarkan sebagai user.</p>
                                <p class="text-xs mt-2">Hubungi administrator atau daftar sebagai karyawan terlebih dahulu.</p>
                            </div>
                        @else
                            <form method="POST" action="{{ route('register.user.store') }}" class="space-y-4">
                                @csrf

                                <!-- Nama -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Nama untuk akun user (bisa berbeda dari nama karyawan)</p>
                                </div>

                                <!-- Username -->
                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                                    <input type="text" name="username" id="username" value="{{ old('username') }}" required
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Username untuk login (harus unik)</p>
                                </div>

                                <!-- Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                                    <input type="password" name="password" id="password" required minlength="6"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password *</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Karyawan -->
                                <div>
                                    <label for="karyawan_id" class="block text-sm font-medium text-gray-700">Data Karyawan *</label>
                                    <select name="karyawan_id" id="karyawan_id" required
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Pilih data karyawan...</option>
                                        @foreach($karyawans as $karyawan)
                                            <option value="{{ $karyawan->id }}" {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                                {{ $karyawan->nama_lengkap }} ({{ $karyawan->pekerjaan }}) - NIK: {{ $karyawan->nik }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Pilih data karyawan yang akan dihubungkan dengan akun user ini</p>
                                </div>

                                <!-- Alasan Pendaftaran -->
                                <div>
                                    <label for="alasan_pendaftaran" class="block text-sm font-medium text-gray-700">Alasan Pendaftaran *</label>
                                    <textarea name="alasan_pendaftaran" id="alasan_pendaftaran" rows="3" required maxlength="500"
                                              placeholder="Jelaskan mengapa Anda membutuhkan akun user..."
                                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('alasan_pendaftaran') }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1">Maksimal 500 karakter</p>
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        🔐 Daftar Akun User
                                    </button>
                                </div>

                                <!-- Info -->
                                <div class="text-center text-xs text-gray-500 pt-4">
                                    <p>Akun user akan dalam status tidak aktif sampai disetujui administrator.</p>
                                    <p>Anda akan diberitahu jika akun sudah diaktifkan.</p>
                                </div>
                            </form>
                        @endif

                        <!-- Alternative -->
                        <div class="text-center pt-4">
                            <p class="text-sm text-gray-600">Belum terdaftar sebagai karyawan?</p>
                            <a href="{{ route('register.karyawan') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                Daftar sebagai Karyawan terlebih dahulu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
