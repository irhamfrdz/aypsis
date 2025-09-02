<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','AYPSIS')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        /* Style for the dropdown content to be hidden by default */
        .dropdown-content {
            display: none;
        }

        /* Custom sidebar styles */
        .sidebar-menu {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #e5e7eb #f9fafb;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: #f9fafb;
            border-radius: 10px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: #d1d5db;
        }

        /* Smooth transitions for menu items */
        .menu-item {
            transform: translateX(0);
            transition: all 0.2s ease-in-out;
        }

        .menu-item:hover {
            transform: translateX(2px);
        }

        /* Badge style for menu items */
        .menu-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">@yield('page_title', 'Dashboard')</h1>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Halo, {{ Auth::user()->name }}!</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <div class="container mx-auto mt-8 px-6 flex-grow">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Sidebar Menu -->
            <div class="md:col-span-1 bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        Menu
                    </h3>
                </div>
                <nav class="p-4 space-y-1 sidebar-menu">
                    @php
                        $user = Auth::user();
                        $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
                    @endphp
                    <a href="{{ route('dashboard') }}" class="menu-item flex items-center py-3 px-4 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 group @if(Request::routeIs('dashboard')) bg-indigo-100 text-indigo-700 font-semibold shadow-sm @endif">
                        <svg class="w-5 h-5 mr-3 @if(Request::routeIs('dashboard')) text-indigo-600 @else text-gray-400 group-hover:text-indigo-500 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
                        </svg>
                        Dashboard
                    </a>

                    {{-- Dropdown untuk Master Data --}}
                    @php
                        $isMasterRoute = Request::routeIs('master.karyawan.*') || Request::routeIs('master.user.*') || Request::routeIs('master.kontainer.*') || Request::routeIs('master.tujuan.*') || Request::routeIs('master.kegiatan.*') || Request::routeIs('master.permission.*') || Request::routeIs('master.mobil.*');
                        $isPermohonanRoute = Request::routeIs('permohonan.*');
                        $isPenyelesaianRoute = Request::routeIs('penyelesaian.*');
                        $isPranotaRoute = Request::routeIs('pranota-supir.*');
                    @endphp

                    @if($isAdmin)
                    <div class="mb-2">
                        <button id="master-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 group {{ $isMasterRoute ? 'bg-indigo-100 text-indigo-700 font-semibold shadow-sm' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ $isMasterRoute ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <span>Master Data</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 {{ $isMasterRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="master-menu-content" class="dropdown-content pl-6 mt-2 space-y-1 border-l-2 border-indigo-100" @if($isMasterRoute) style="display: block;" @endif>
                            @can('master-karyawan')
                                <a href="{{ route('master.karyawan.index') }}" class="flex items-center py-2 px-3 rounded-md text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 @if(Request::routeIs('master.karyawan.*')) bg-indigo-50 text-indigo-600 font-medium @endif">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Master Karyawan
                                </a>
                            @endcan
                            @can('master-user')
                                <a href="{{ route('master.user.index') }}" class="flex items-center py-2 px-3 rounded-md text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 @if(Request::routeIs('master.user.*')) bg-indigo-50 text-indigo-600 font-medium @endif">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                    Master User
                                </a>
                            @endcan
                            @can('master-kontainer')
                                <a href="{{ route('master.kontainer.index') }}" class="flex items-center py-2 px-3 rounded-md text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 @if(Request::routeIs('master.kontainer.*')) bg-indigo-50 text-indigo-600 font-medium @endif">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Master Kontainer
                                </a>
                            @endcan
                            @can('master-pricelist-sewa-kontainer')
                                <a href="{{ route('master.pricelist-sewa-kontainer.index') }}" class="flex items-center py-2 px-3 rounded-md text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 @if(Request::routeIs('master.pricelist-sewa-kontainer.*')) bg-indigo-50 text-indigo-600 font-medium @endif">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Master Pricelist Sewa Kontainer
                                </a>
                            @endcan
                            @can('master-tujuan')
                                <a href="{{ route('master.tujuan.index') }}" class="flex items-center py-2 px-3 rounded-md text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 @if(Request::routeIs('master.tujuan.*')) bg-indigo-50 text-indigo-600 font-medium @endif">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Master Tujuan
                                </a>
                            @endcan
                            @can('master-kegiatan')
                                <a href="{{ route('master.kegiatan.index') }}" class="flex items-center py-2 px-3 rounded-md text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 @if(Request::routeIs('master.kegiatan.*')) bg-indigo-50 text-indigo-600 font-medium @endif">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Master Kegiatan
                                </a>
                            @endcan
                            @can('master-permission')
                                <a href="{{ route('master.permission.index') }}" class="flex items-center py-2 px-3 rounded-md text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 @if(Request::routeIs('master.permission.*')) bg-indigo-50 text-indigo-600 font-medium @endif">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Master Izin
                                </a>
                            @endcan
                            @can('master-mobil')
                                <a href="{{ route('master.mobil.index') }}" class="flex items-center py-2 px-3 rounded-md text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 @if(Request::routeIs('master.mobil.*')) bg-indigo-50 text-indigo-600 font-medium @endif">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12a2 2 0 100-4 2 2 0 000 4zm0 0v10m0-10a2 2 0 002-2V2"/>
                                    </svg>
                                    Master Mobil
                                </a>
                            @endcan
                        </div>
                    </div>
                    @else
                    @canany(['master-karyawan', 'master-user', 'master-kontainer', 'master-tujuan', 'master-kegiatan', 'master-permission', 'master-mobil', 'master-pricelist-sewa-kontainer'])
                    <div>
                        <button id="master-menu-toggle" class="w-full flex justify-between items-center py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 transition-colors duration-200 {{ $isMasterRoute ? 'bg-gray-200 font-semibold' : '' }}">
                            <span>Master Data</span>
                            <svg class="w-4 h-4 transition-transform {{ $isMasterRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="master-menu-content" class="dropdown-content pl-4 mt-2 space-y-2" @if($isMasterRoute) style="display: block;" @endif>
                            @can('master-karyawan')
                                <a href="{{ route('master.karyawan.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('master.karyawan.*')) bg-gray-200 font-semibold @endif">Master Karyawan</a>
                            @endcan
                            @can('master-user')
                                <a href="{{ route('master.user.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('master.user.*')) bg-gray-200 font-semibold @endif">Master User</a>
                            @endcan
                            @can('master-kontainer')
                                <a href="{{ route('master.kontainer.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('master.kontainer.*')) bg-gray-200 font-semibold @endif">Master Kontainer</a>
                            @endcan
                            @can('master-pricelist-sewa-kontainer')
                                <a href="{{ route('master.pricelist-sewa-kontainer.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('master.pricelist-sewa-kontainer.*')) bg-gray-200 font-semibold @endif">Master Pricelist Sewa Kontainer</a>
                            @endcan
                            @can('master-tujuan')
                                <a href="{{ route('master.tujuan.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('master.tujuan.*')) bg-gray-200 font-semibold @endif">Master Tujuan</a>
                            @endcan
                            @can('master-kegiatan')
                                <a href="{{ route('master.kegiatan.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('master.kegiatan.*')) bg-gray-200 font-semibold @endif">Master Kegiatan</a>
                            @endcan
                            @can('master-permission')
                                <a href="{{ route('master.permission.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('master.permission.*')) bg-gray-200 font-semibold @endif">Master Izin</a>
                            @endcan
                            @can('master-mobil')
                                <a href="{{ route('master.mobil.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('master.mobil.*')) bg-gray-200 font-semibold @endif">Master Mobil</a>
                            @endcan
                            {{-- Hapus menu dari master data --}}
                        </div>
                    </div>
                    @endcanany
                    @endif

                    {{-- Dropdown untuk Tagihan Kontainer Sewa (di luar master data) --}}
                    {{-- Tagihan Kontainer Sewa menu removed (refactored) --}}

                    {{-- Dropdown untuk Tagihan Kontainer Sewa (baru) --}}

                    @if($isAdmin || auth()->user()->can('master-pranota-tagihan-kontainer'))
                    @php
                        $isPranotaTagihanRoute = Request::routeIs('pembayaran-pranota-tagihan-kontainer.*') || Request::routeIs('pranota-tagihan-kontainer.*') || Request::routeIs('pembayaran-pranota-kontainer.*') || Request::routeIs('pranota.*') || Request::routeIs('daftar-tagihan-kontainer-sewa.*');
                    @endphp
                    <div class="mt-2">
                        <button id="pranota-tagihan-kontainer-menu-toggle" class="w-full flex justify-between items-center py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 transition-colors duration-200 {{ $isPranotaTagihanRoute ? 'bg-gray-200 font-semibold' : '' }}">
                            <span>Tagihan Kontainer Sewa</span>
                            <svg class="w-4 h-4 transition-transform {{ $isPranotaTagihanRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="pranota-tagihan-kontainer-menu-content" class="dropdown-content pl-4 mt-2 space-y-2" @if($isPranotaTagihanRoute) style="display: block;" @endif>
                            @if (Route::has('pembayaran-pranota-tagihan-kontainer.index'))
                                <a href="{{ route('pembayaran-pranota-tagihan-kontainer.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('pembayaran-pranota-tagihan-kontainer.*')) bg-gray-200 font-semibold @endif">Pembayaran Tagihan Kontainer Sewa</a>
                            @endif

                            {{-- Daftar Tagihan Kontainer (simple CRUD) --}}
                            @if (Route::has('daftar-tagihan-kontainer-sewa.index'))
                                <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('daftar-tagihan-kontainer-sewa.*')) bg-gray-200 font-semibold @endif">Daftar Tagihan Kontainer</a>
                            @endif

                            {{-- Daftar Pranota --}}
                            @if (Route::has('pranota.index'))
                                <a href="{{ route('pranota.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('pranota.*')) bg-gray-200 font-semibold @endif">Daftar Pranota</a>
                            @endif

                            {{-- Pembayaran Pranota Kontainer --}}
                            @if (Route::has('pembayaran-pranota-kontainer.index'))
                                <a href="{{ route('pembayaran-pranota-kontainer.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('pembayaran-pranota-kontainer.*')) bg-gray-200 font-semibold @endif">Pembayaran Pranota Kontainer</a>
                            @endif

                            @if (Route::has('pembayaran-pranota-tagihan-kontainer.create'))
                                <a href="{{ route('pembayaran-pranota-tagihan-kontainer.create') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('pembayaran-pranota-tagihan-kontainer.create')) bg-gray-200 font-semibold @endif">Buat Pembayaran</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Dropdown untuk Permohonan Memo --}}
                    @if($isAdmin || auth()->user()->can('master-permohonan'))
                    <div>
                        <button id="permohonan-menu-toggle" class="w-full flex justify-between items-center py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 transition-colors duration-200 {{ $isPermohonanRoute ? 'bg-gray-200 font-semibold' : '' }}">
                            <span>Permohonan Memo</span>
                            <svg class="w-4 h-4 transition-transform {{ $isPermohonanRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="permohonan-menu-content" class="dropdown-content pl-4 mt-2 space-y-2" @if($isPermohonanRoute) style="display: block;" @endif>
                            <a href="{{ route('permohonan.create') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('permohonan.create')) bg-gray-200 font-semibold @endif">Buat Permohonan</a>
                            <a href="{{ route('permohonan.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('permohonan.index')) bg-gray-200 font-semibold @endif">Daftar Permohonan</a>

                        </div>
                    </div>
                    @endif

                    {{-- Dropdown untuk Pranota --}}
                    @if($isAdmin || auth()->user()->can('master-pranota-supir'))
                    <div>
                        <button id="pranota-menu-toggle" class="w-full flex justify-between items-center py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 transition-colors duration-200 {{ $isPranotaRoute ? 'bg-gray-200 font-semibold' : '' }}">
                            <span>Pranota</span>
                            <svg class="w-4 h-4 transition-transform {{ $isPranotaRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="pranota-menu-content" class="dropdown-content pl-4 mt-2 space-y-2" @if($isPranotaRoute) style="display: block;" @endif>
                            <a href="{{ route('pranota-supir.create') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('pranota-supir.create')) bg-gray-200 font-semibold @endif">Buat Pranota Supir</a>
                            <a href="{{ route('pranota-supir.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('pranota-supir.index')) bg-gray-200 font-semibold @endif">Daftar Pranota Supir</a>
                        </div>
                    </div>
                    @endif

                    {{-- Dropdown untuk Pembayaran Pranota Supir --}}
                    @if($isAdmin || auth()->user()->can('master-pembayaran-pranota-supir'))
                    @php
                        $isPembayaranPranotaRoute = Request::routeIs('pembayaran-pranota-supir.*');
                    @endphp
                    <div>
                        <button id="pembayaran-pranota-menu-toggle" class="w-full flex justify-between items-center py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 transition-colors duration-200 {{ $isPembayaranPranotaRoute ? 'bg-gray-200 font-semibold' : '' }}">
                            <span>Pembayaran Pranota Supir</span>
                            <svg class="w-4 h-4 transition-transform {{ $isPembayaranPranotaRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="pembayaran-pranota-menu-content" class="dropdown-content pl-4 mt-2 space-y-2" @if($isPembayaranPranotaRoute) style="display: block;" @endif>
                            <a href="{{ route('pembayaran-pranota-supir.create') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('pembayaran-pranota-supir.create')) bg-gray-200 font-semibold @endif">Buat Pembayaran</a>
                            <a href="{{ route('pembayaran-pranota-supir.index') }}" class="block py-2 px-4 rounded-md text-gray-600 hover:bg-gray-100 @if(Request::routeIs('pembayaran-pranota-supir.index')) bg-gray-200 font-semibold @endif">Daftar Pembayaran</a>
                        </div>
                    </div>
                    @endif

                    {{-- Link untuk Penyelesaian Tugas --}}
                    @if($isAdmin || auth()->user()->can('master-permohonan'))
                    <a href="{{ route('approval.dashboard') }}" class="flex items-center py-3 px-4 rounded-lg text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 group @if($isPenyelesaianRoute) bg-orange-100 text-orange-700 font-semibold shadow-sm @endif">
                        <svg class="w-5 h-5 mr-3 @if($isPenyelesaianRoute) text-orange-600 @else text-gray-400 group-hover:text-orange-500 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Approval Tugas
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Page Content -->
            <div class="md:col-span-3">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Stack ini akan merender semua skrip yang di-push dari halaman lain (seperti create.blade.php) --}}
    {{-- DAN skrip yang di-push dari layout ini sendiri. --}}
    @stack('scripts')

    {{-- Skrip khusus untuk layout ini, seperti dropdown menu --}}
    <script>
        // Fungsi untuk menangani toggle dropdown
        function setupDropdown(buttonId, contentId) {
            const toggleButton = document.getElementById(buttonId);
            const content = document.getElementById(contentId);

            if (toggleButton && content) {
                const icon = toggleButton.querySelector('svg');
                toggleButton.addEventListener('click', function() {
                    const isVisible = content.style.display === 'block';
                    content.style.display = isVisible ? 'none' : 'block';
                    if(icon) icon.classList.toggle('rotate-180', !isVisible);
                });
            }
        }
    setupDropdown('master-menu-toggle', 'master-menu-content');
    setupDropdown('permohonan-menu-toggle', 'permohonan-menu-content');
    setupDropdown('pranota-menu-toggle', 'pranota-menu-content');
    setupDropdown('pembayaran-pranota-menu-toggle', 'pembayaran-pranota-menu-content');
    setupDropdown('pranota-tagihan-kontainer-menu-toggle', 'pranota-tagihan-kontainer-menu-content');
    </script>
</body>
</html>
