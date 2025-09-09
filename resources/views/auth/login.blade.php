<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AYPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">AYPSIS Login</h2>

        <!-- Menampilkan pesan sukses jika ada -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Menampilkan pesan error login -->
        @error('login')
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Login Gagal!</p>
                        <p class="text-sm">{{ $message }}</p>
                        <p class="text-xs mt-2 text-red-600">
                            • Periksa kembali username dan password Anda<br>
                            • Pastikan menggunakan huruf besar/kecil yang tepat<br>
                            • Hubungi administrator jika masalah berlanjut
                        </p>
                    </div>
                </div>
            </div>
        @enderror

        <!-- Menampilkan error umum authentication -->
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Terjadi Kesalahan!</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Username -->
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                       class="shadow appearance-none border @error('username') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                <!-- Menampilkan error validasi untuk username -->
                @error('username')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input id="password" type="password" name="password" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    Login
                </button>
            </div>
        </form>

        <!-- Registration Buttons -->
        <div class="mt-6 space-y-3">
            <div class="text-center text-gray-600 text-sm font-medium">
                Belum memiliki akun?
            </div>

            <div class="flex flex-col space-y-2">
                <a href="{{ route('register.karyawan') }}" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center transition duration-200">
                    👥 Registrasi Karyawan
                </a>

                <a href="{{ route('register.user') }}" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center transition duration-200">
                    🔐 Registrasi User
                </a>
            </div>

            <div class="text-center text-xs text-gray-500 mt-2">
                Hubungi administrator untuk persetujuan akun
            </div>
        </div>

        <p class="text-center text-gray-500 text-xs mt-6">
            &copy;{{ date('Y') }} AYPSIS. All rights reserved.
        </p>
    </div>
</body>
</html>
