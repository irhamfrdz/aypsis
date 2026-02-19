<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AYPSIS')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; }
        .glass-header { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(226, 232, 240, 0.8); }
    </style>
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen">
    <header class="glass-header sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i class="fas fa-truck text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 tracking-tight">AYPSIS <span class="text-indigo-600">SUPIR</span></h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden sm:flex flex-col items-end">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Supir</span>
                        <span class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="mt-4 pt-4 border-t border-gray-100 overflow-x-auto">
                <div class="flex space-x-1 min-w-max pb-1">
                    <a href="{{ route('supir.dashboard') }}" 
                       class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ Request::routeIs('supir.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                       <i class="fas fa-tasks mr-2"></i> Tugas Saya
                    </a>
                    
                    <a href="{{ route('supir.ob-muat') }}" 
                       class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ Request::routeIs('supir.ob-muat') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                       <i class="fas fa-box-open mr-2"></i> OB Muat
                    </a>
                    
                    <a href="{{ route('supir.ob-bongkar') }}" 
                       class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ Request::routeIs('supir.ob-bongkar') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                       <i class="fas fa-box mr-2"></i> OB Bongkar
                    </a>

                    <a href="{{ route('supir.cek-kendaraan.index') }}" 
                       class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ Request::routeIs('supir.cek-kendaraan.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                       <i class="fas fa-clipboard-check mr-2"></i> Cek Unit
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-100 py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm text-gray-400 font-medium">&copy; {{ date('Y') }} AYPSIS Management System. All rights reserved.</p>
        </div>
    </footer>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>
