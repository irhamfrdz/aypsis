<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AYPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8 relative overflow-hidden">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">AYPSIS Login</h2>

        <!-- Success Animation Overlay -->
        <div id="success-overlay" class="hidden absolute inset-0 bg-indigo-600 z-50 flex-col items-center justify-center transition-opacity duration-300 rounded-lg">
            <div id="check-circle" class="transform scale-0 transition-transform duration-500 ease-out bg-white rounded-full p-4 mb-4 shadow-lg">
                <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-white text-2xl font-bold animate-pulse">Login Berhasil!</h2>
            <p class="text-indigo-200 mt-2 text-sm">Mengarahkan...</p>
        </div>

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
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input id="password" type="password" name="password" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- Remember Me -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="form-checkbox h-4 w-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500" {{ old('remember') ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Ingat saya (tetap login)</span>
                </label>
            </div>

            <div class="flex flex-col gap-4">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    Login
                </button>
                <div class="relative flex items-center py-2">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="flex-shrink mx-4 text-gray-400 text-xs uppercase font-semibold">Atau</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>
                <a href="{{ route('recruitment.create') }}" class="block text-center border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition duration-300">
                    Melamar (Pelamar Karyawan)
                </a>
            </div>
        </form>

        <!-- Registration removed: only admin can create accounts -->
        <div class="mt-6 text-center text-sm text-gray-700">
            Jika Anda belum memiliki akun, silakan hubungi administrator untuk pembuatan akun.
        </div>

        <p class="text-center text-gray-500 text-xs mt-6">
            &copy;{{ date('Y') }} AYPSIS. All rights reserved.
        </p>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form:not([action*="logout"])');
        const loginBtn = document.querySelector('button[type="submit"]');
        const overlay = document.getElementById('success-overlay');
        const checkCircle = document.getElementById('check-circle');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Disable button and show loading state
            const originalBtnText = loginBtn.innerHTML;
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
            
            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                // Fetch follows redirects automatically.
                // If login fails, Laravel redirects back to the login page (response.ok is true, but URL is the same)
                // If login succeeds, it redirects to dashboard (response.ok is true, URL is different)
                const isSamePage = response.url && new URL(response.url).pathname === window.location.pathname;

                if (response.ok && !isSamePage) {
                    // Success! Show animation overlay
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                    
                    // Trigger checkmark animation
                    setTimeout(() => {
                        checkCircle.classList.remove('scale-0');
                        checkCircle.classList.add('scale-100');
                    }, 50);

                    // Redirect after animation
                    setTimeout(() => {
                        window.location.href = response.url || '/';
                    }, 1500);
                } else {
                    // If login failed (either 422 JSON error or redirected back to login),
                    // submit normally to let Laravel show the validation errors.
                    form.submit();
                }
            } catch (error) {
                // If network error, submit normally
                form.submit();
            }
        });
    });
</script>
</html>

