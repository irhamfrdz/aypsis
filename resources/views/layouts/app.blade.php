<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','AYPSIS')</title>
    
    <!-- Vite Assets (Works Offline) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        /* Utility to ensure menu labels stay on a single line */
        .menu-text {
            white-space: nowrap;      /* keep on one line */
            overflow: hidden;         /* hide overflow */
            text-overflow: ellipsis;  /* add ellipsis if too long */
            display: block;
        }

        .dropdown-content {
            display: none;
            transition: all 0.2s ease-in-out;
        }

        .menu-item {
            transition: all 0.15s ease-in-out;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #dee2e6;
            border-radius: 2px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #ced4da;
        }

        /* Sidebar animations */
        .sidebar-item {
            transition: all 0.2s ease-in-out;
        }

        .sidebar-item:hover {
            transform: translateX(2px);
        }

        /* Active state animations */
        .sidebar-active {
            position: relative;
        }

        .sidebar-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: #3b82f6;
            border-radius: 0 2px 2px 0;
        }

        /* Search functionality styles */
        .sidebar-search-highlight {
            background-color: #fef3c7;
            font-weight: 600;
            padding: 2px 4px;
            border-radius: 3px;
            animation: highlight-pulse 0.3s ease-in-out;
        }

        @keyframes highlight-pulse {
            0% { background-color: #fef3c7; }
            50% { background-color: #fde68a; }
            100% { background-color: #fef3c7; }
        }

        .sidebar-hidden {
            display: none !important;
            opacity: 0;
            transform: scale(0.95);
            transition: all 0.2s ease-in-out;
        }

        .sidebar-visible {
            display: block !important;
            opacity: 1;
            transform: scale(1);
            transition: all 0.2s ease-in-out;
        }

        /* Search input focus effects */
        .sidebar-search-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
        }

        /* Clear button hover effect */
        .sidebar-clear-btn:hover {
            background-color: #f3f4f6;
            transform: scale(1.1);
        }
            padding: 12px 16px;
            border-bottom: 1px solid #dee2e6;
        }

        .clean-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f3f4;
            font-size: 0.875rem;
        }

        .clean-table tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 flex flex-col h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
        <div class="px-6 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="mobile-menu-button" class="lg:hidden mr-3 p-1 text-gray-600 hover:text-gray-900 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Notification Bell -->
                <div class="relative">
                    <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                        <i class="fas fa-bell text-xl"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                            {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </a>
                </div>
                
                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="profileDropdownButton" class="flex items-center space-x-2 text-sm text-gray-600 hover:text-gray-900 font-medium focus:outline-none">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <span>{{ Auth::user()->username }}</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                        <div class="py-2">
                            <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-3"></i>Profil Saya
                            </a>
                            <div class="border-t border-gray-200 my-2"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                    <i class="fas fa-sign-out-alt mr-3"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="flex flex-1 overflow-hidden">
    @php
        $user = Auth::user();
        $hasKaryawan = $user && $user->karyawan;
        $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
        $showSidebar = $hasKaryawan || $isAdmin || $user; // Show sidebar for logged in users, especially admins
    @endphp

    @if($showSidebar)
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden transition-opacity duration-300"></div>
    
    <!-- Sidebar -->
    <div id="sidebar" class="fixed top-0 bottom-0 left-0 z-50 w-80 lg:w-64 bg-gradient-to-b from-blue-50 via-white to-gray-100 shadow-2xl border-r border-gray-300 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col lg:top-16 rounded-r-2xl lg:rounded-r-2xl overflow-y-auto">
            <!-- Mobile Header with close button -->
            <div class="lg:hidden flex items-center justify-between p-4 bg-blue-600 shadow-md flex-shrink-0 sticky top-0 z-10">
                <div class="flex items-center text-white">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center mr-3 shadow-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-lg tracking-wide">AYPSIS</h2>
                        <p class="text-xs opacity-90 font-medium">Management System</p>
                    </div>
                </div>
                <button id="close-sidebar" class="text-white hover:bg-blue-700 p-2 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Header (Desktop Only) -->
            <div class="hidden lg:block p-6 border-b border-gray-300 flex-shrink-0 bg-white rounded-tr-2xl shadow-md">
                <div class="flex items-center text-gray-800 mb-4">
                    <div class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-lg text-blue-700 tracking-wide">AYPSIS</h2>
                        <p class="text-xs text-gray-400 font-semibold">Management System</p>
                    </div>
                </div>

                <!-- Sidebar Search -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           id="sidebar-search"
                           placeholder="Cari menu..."
                           class="sidebar-search-input w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 focus:bg-white transition-colors duration-200"
                           autocomplete="off">
                    <button type="button"
                            id="clear-search"
                            class="sidebar-clear-btn absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 opacity-0 pointer-events-none transition-opacity duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-2 pb-20">
                @php
                    $user = Auth::user();
                    $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
                    $isDashboard = Request::routeIs('dashboard');

                    // Check if user has any master data permissions
                    $hasMasterPermissions = $user && (
                        $user->can('master-permission-view') ||
                        $user->can('master-cabang-view') ||
                        $user->can('master-pengirim-view') ||
                        $user->can('master-penerima-view') ||
                        $user->can('master-jenis-barang-view') ||
                        $user->can('master-term-view') ||
                        $user->can('master-coa-view') ||
                        $user->can('master-kode-nomor-view') ||
                        $user->can('master-nomor-terakhir-view') ||
                        $user->can('master-tipe-akun-view') ||
                        $user->can('master-tujuan-view') ||
                        $user->can('master-tujuan-kirim-view') ||
                        $user->can('master-kegiatan-view') ||
                        $user->can('master-pelabuhan-view') ||
                        $user->can('master-pricelist-gate-in-view') ||
                        $user->can('master-user-view') ||
                        $user->can('master-karyawan-view') ||
                        $user->can('master-divisi-view') ||
                        $user->can('master-pekerjaan-view') ||
                        $user->can('master-pajak-view') ||
                        $user->can('master-bank-view') ||
                        $user->can('master-vendor-bengkel.view') ||
                        $user->can('vendor-kontainer-sewa-view')
                    );

                    // Show master section if user is admin OR has any master permissions
                    $showMasterSection = $isAdmin || $hasMasterPermissions;
                @endphp

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center py-2 px-5 rounded-xl mt-4 mb-4 transition-all duration-200 group shadow-sm text-xs {{ $isDashboard ? 'bg-blue-100 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-700' }}">
                    <span class="text-xs font-medium menu-text">Dashboard</span>
                </a>

                <!-- Master Data Section -->
                @php
                    $isMasterRoute = Request::routeIs('master-coa-*') || Request::routeIs('master.kode-nomor.*') || Request::routeIs('master.nomor-terakhir.*') || Request::routeIs('master.tipe-akun.*') || Request::routeIs('master.cabang.*') || Request::routeIs('master.kegiatan.*') || Request::routeIs('master-pelabuhan.*') || Request::routeIs('master.karyawan.*') || Request::routeIs('master.user.*') || Request::routeIs('master.divisi.*') || Request::routeIs('master.pekerjaan.*') || Request::routeIs('master.pajak.*') || Request::routeIs('admin.user-approval.*') || Request::routeIs('master-bank-*') || Request::routeIs('master.vendor-bengkel.*') || Request::routeIs('vendor-kontainer-sewa.*') || Request::routeIs('master.pricelist-gate-in.*');
                    $isPermohonanRoute = Request::routeIs('permohonan.*');
                    $isPenyelesaianRoute = Request::routeIs('approval.*');
                    $isPranotaRoute = Request::routeIs('pranota-supir.*') || Request::routeIs('pembayaran-pranota-supir.*');
                @endphp

                @if($showMasterSection)
                <div class="mt-4 mb-6">
                    <button id="master-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-sm font-medium {{ $isMasterRoute ? 'bg-blue-50 text-blue-700' : '' }}">
                        <span class="text-sm font-semibold">Master Data</span>
                        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isMasterRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="master-menu-content" class="dropdown-content ml-2 mt-3 space-y-2" @if($isMasterRoute) style="display: block;" @endif>


                        {{-- Master Umum Sub-Dropdown --}}
                        @php
                            $isUmumRoute = Request::routeIs('master.cabang.*') || Request::routeIs('master.kode-nomor.*') || Request::routeIs('master.nomor-terakhir.*') || Request::routeIs('master.kegiatan.*') || Request::routeIs('master-pelabuhan.*');
                            $hasUmumPermissions = $user && ($user->can('master-cabang-view') || $user->can('master-kode-nomor-view') || $user->can('master-nomor-terakhir-view') || $user->can('master-kegiatan-view') || $user->can('master-pelabuhan-view'));
                        @endphp

                        @if($hasUmumPermissions)
                        <div class="mx-2 mb-3">
                            <button id="master-umum-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 group {{ $isUmumRoute ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}"
                                <span class="text-xs font-medium">Master Umum</span>
                                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isUmumRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="master-umum-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isUmumRoute) style="display: block;" @endif>
                                @if($user && $user->can('master-cabang-view'))
                                    <a href="{{ route('master.cabang.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.cabang.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Cabang</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-kode-nomor-view'))
                                    <a href="{{ route('master.kode-nomor.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.kode-nomor.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Kode Penomoran</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-nomor-terakhir-view'))
                                    <a href="{{ route('master.nomor-terakhir.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.nomor-terakhir.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Nomor Terakhir</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-kegiatan-view'))
                                    <a href="{{ route('master.kegiatan.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.kegiatan.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Jenis Kegiatan</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-pelabuhan-view'))
                                    <a href="{{ route('master-pelabuhan.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master-pelabuhan.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Pelabuhan</span>
                                    </a>
                                @endif
                                </div>
                            </div>
                            @endif

                            {{-- Tipe Akun Sub-Dropdown --}}
                            @php
                                $isAkunRoute = Request::routeIs('master.tipe-akun.*') || Request::routeIs('master-coa-*');
                                $hasAkunPermissions = $user && ($user->can('master-tipe-akun-view') || $user->can('master-coa-view'));
                            @endphp

                        @if($hasAkunPermissions)
                        <div class="mx-2 mb-3">
                            <button id="master-akun-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 group {{ $isAkunRoute ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Tipe Akun</span>
                                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAkunRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="master-akun-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isAkunRoute) style="display: block;" @endif>
                                @if($user && $user->can('master-coa-view'))
                                    <a href="{{ route('master-coa-index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('master-coa-*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">COA</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-tipe-akun-view'))
                                    <a href="{{ route('master.tipe-akun.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('master.tipe-akun.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Tipe Akun</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                            {{-- Master Penjualan Sub-Dropdown --}}
                            @php
                                $isPenjualanRoute = Request::routeIs('pengirim.*') || Request::routeIs('penerima.*') || Request::routeIs('jenis-barang.*') || Request::routeIs('term.*') || Request::routeIs('master.tujuan-kegiatan-utama.*') || Request::routeIs('tujuan-kirim.*') || Request::routeIs('master.tujuan.*');
                                $hasPenjualanPermissions = $user && ($user->can('master-pengirim-view') || $user->can('master-penerima-view') || $user->can('master-jenis-barang-view') || $user->can('master-term-view') || $user->can('master-tujuan-kirim-view') || $user->can('master-tujuan-kirim-view'));
                            @endphp

                        @if($hasPenjualanPermissions)
                        <div class="mx-2 mb-3">
                            <button id="master-penjualan-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 group {{ $isPenjualanRoute ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Master Penjualan</span>
                                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isPenjualanRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="master-penjualan-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isPenjualanRoute) style="display: block;" @endif>
                                @if($user && $user->can('master-pengirim-view'))
                                    <a href="{{ route('pengirim.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('pengirim.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Pengirim</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-penerima-view'))
                                    <a href="{{ route('penerima.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('penerima.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Penerima</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-jenis-barang-view'))
                                    <a href="{{ route('jenis-barang.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('jenis-barang.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Jenis Barang</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-term-view'))
                                    <a href="{{ route('term.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('term.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Terms</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-tujuan-kirim-view'))
                                    <a href="{{ route('master.tujuan-kegiatan-utama.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('master.tujuan-kegiatan-utama.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Transportasi</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-tujuan-kirim-view'))
                                    <a href="{{ route('tujuan-kirim.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('tujuan-kirim.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Tujuan Kirim</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-tujuan-view'))
                                    <a href="{{ route('master.tujuan.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('master.tujuan.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Tujuan</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Master Karyawan Sub-Dropdown --}}
                            @php
                                $isUserRoute = Request::routeIs('master.user.*') || Request::routeIs('master.karyawan.*') || Request::routeIs('master.divisi.*') || Request::routeIs('master.pekerjaan.*') || Request::routeIs('master.pajak.*') || Request::routeIs('admin.user-approval.*') || Request::routeIs('master-bank-*') || Request::routeIs('master.permission.*');
                                $hasUserPermissions = $user && ($user->can('master-user-view') || $user->can('master-karyawan-view') || $user->can('master-divisi-view') || $user->can('master-pekerjaan-view') || $user->can('master-pajak-view') || $user->can('master-bank-view') || $user->can('master-permission-view'));
                                $hasUserApprovalAccess = $isAdmin ||
                                    auth()->user()->can('master-user') ||
                                    auth()->user()->can('user-approval') ||
                                    auth()->user()->can('user-approval.view') ||
                                    auth()->user()->can('user-approval.create') ||
                                    auth()->user()->can('user-approval.update') ||
                                    auth()->user()->can('user-approval.delete') ||
                                    auth()->user()->can('user-approval.approve') ||
                                    auth()->user()->can('user-approval.print') ||
                                    auth()->user()->can('user-approval.export');
                                $showUserSection = $hasUserPermissions || $hasUserApprovalAccess;
                            @endphp

                        @if($showUserSection)
                        <div class="mx-2 mb-3">
                            <button id="master-karyawan-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 group {{ $isUserRoute ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Master Karyawan</span>
                                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isUserRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="master-karyawan-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isUserRoute) style="display: block;" @endif>
                                @if($user && $user->can('master-user-view'))
                                    <a href="{{ route('master.user.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.user.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master User</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-karyawan-view'))
                                    <a href="{{ route('master.karyawan.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.karyawan.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Data Karyawan</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-divisi-view'))
                                    <a href="{{ route('master.divisi.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.divisi.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Divisi</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-pekerjaan-view'))
                                    <a href="{{ route('master.pekerjaan.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.pekerjaan.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Pekerjaan</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-pajak-view'))
                                    <a href="{{ route('master.pajak.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.pajak.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Pajak</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-bank-view'))
                                    <a href="{{ route('master-bank-index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master-bank-*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Bank</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-permission-view'))
                                    <a href="{{ route('master.permission.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.permission.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Izin (buat Programmer)</span>
                                    </a>
                                @endif

                                @if($hasUserApprovalAccess)
                                    @php
                                        $pendingUsersCount = \App\Models\User::where('status', 'pending')->count();
                                    @endphp
                                    <a href="{{ route('admin.user-approval.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('admin.user-approval.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Persetujuan User</span>
                                        @if($pendingUsersCount > 0)
                                            <span class="ml-auto bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $pendingUsersCount }}</span>
                                        @endif
                                    </a>
                                @endif


                            </div>
                        </div>
                        @endif

                        {{-- Master Tarif Sub-Dropdown --}}
        @php
            $isTarifRoute = Request::routeIs('master.master.pricelist-sewa-kontainer.*') || Request::routeIs('master.pricelist-cat.*') || Request::routeIs('uang-jalan-batam.*') || Request::routeIs('master.pricelist-gate-in.*') || Request::routeIs('master.pricelist-ob.*');
            $hasTarifPermissions = $user && ($user->can('master-pricelist-sewa-kontainer-view') || $user->can('master-pricelist-cat-view') || $user->can('uang-jalan-batam.view') || $user->can('master-pricelist-gate-in-view') || $user->can('master-pricelist-ob-view'));
        @endphp                        @if($hasTarifPermissions)
                        <div class="mx-2 mb-3">
                            <button id="master-tarif-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 group {{ $isTarifRoute ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Master Tarif</span>
                                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isTarifRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="master-tarif-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isTarifRoute) style="display: block;" @endif>
                                @if($user && $user->can('master-pricelist-sewa-kontainer-view'))
                                    <a href="{{ route('master.master.pricelist-sewa-kontainer.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('master.master.pricelist-sewa-kontainer.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Pricelist Kontainer Sewa</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-pricelist-cat-view'))
                                    <a href="{{ route('master.pricelist-cat.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('master.pricelist-cat.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Pricelist CAT Kontainer</span>
                                    </a>
                                @endif
                                @if($user && $user->can('uang-jalan-batam.view'))
                                    <a href="{{ route('uang-jalan-batam.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('uang-jalan-batam.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Uang Jalan Batam</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-pricelist-gate-in-view'))
                                    <a href="{{ route('master.pricelist-gate-in.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('master.pricelist-gate-in.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Pricelist Gate In</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-pricelist-ob-view'))
                                    <a href="{{ route('master.pricelist-ob.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('master.pricelist-ob.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Pricelist OB</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Master Pemasok Sub-Dropdown --}}
                        @php
                            $isPemasokRoute = Request::routeIs('master.vendor-bengkel.*') || Request::routeIs('vendor-kontainer-sewa.*');
                            $hasPemasokPermissions = $user && ($user->can('master-vendor-bengkel.view') || $user->can('vendor-kontainer-sewa-view'));
                        @endphp

                        @if($hasPemasokPermissions)
                        <div class="mx-2 mb-3">
                            <button id="master-pemasok-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 group {{ $isPemasokRoute ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Master Pemasok</span>
                                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isPemasokRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="master-pemasok-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isPemasokRoute) style="display: block;" @endif>
                                @if($user && $user->can('master-vendor-bengkel.view'))
                                    <a href="{{ route('master.vendor-bengkel.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('master.vendor-bengkel.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Vendor/Bengkel</span>
                                    </a>
                                @endif
                                @if($user && $user->can('vendor-kontainer-sewa-view'))
                                    <a href="{{ route('vendor-kontainer-sewa.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('vendor-kontainer-sewa.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Vendor Kontainer Sewa</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

{{-- User Dropdown has been completely moved to Master Data > Master Karyawan --}}

{{-- Aktiva Dropdown --}}
@php
    $isAktivaRoute = Request::routeIs('master.kontainer.*') || Request::routeIs('master.stock-kontainer.*') || Request::routeIs('master.mobil.*') || Request::routeIs('master-kapal.*');
    $hasAktivaPermissions = $user && ($user->can('master-kontainer-view') || $user->can('master-stock-kontainer-view') || $user->can('master-mobil-view') || $user->can('master-kapal.view'));
@endphp

@if($hasAktivaPermissions)
<div class="mt-4 mb-6">
    <button id="aktiva-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-sm font-medium {{ $isAktivaRoute ? 'bg-blue-50 text-blue-700' : '' }}">
        <span class="text-sm font-semibold">Aktiva</span>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isAktivaRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="aktiva-menu-content" class="dropdown-content ml-2 mt-3 space-y-2" @if($isAktivaRoute) style="display: block;" @endif>
        {{-- Kontainer Sub-Dropdown --}}
        @php
            $isKontainerRoute = Request::routeIs('master.kontainer.*') || Request::routeIs('master.stock-kontainer.*');
            $hasKontainerPermissions = $user && ($user->can('master-kontainer-view') || $user->can('master-stock-kontainer-view'));
        @endphp

        @if($hasKontainerPermissions)
        <div class="mx-2 mb-3">
            <button id="kontainer-menu-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 group {{ $isKontainerRoute ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Kontainer</span>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isKontainerRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="kontainer-menu-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isKontainerRoute) style="display: block;" @endif>
                @if($user && $user->can('master-kontainer-view'))
                    <a href="{{ route('master.kontainer.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('kontainer.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Master Kontainer Sewa</span>
                    </a>
                @endif

                @if($user && $user->can('master-stock-kontainer-view'))
                    <a href="{{ route('master.stock-kontainer.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.stock-kontainer.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Stock Kontainer</span>
                    </a>
                @endif

                @if($user && $user->can('pergerakan-kapal-view'))
                    <a href="{{ route('pergerakan-kapal.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('pergerakan-kapal.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pergerakan Kapal</span>
                    </a>
                @endif
                @if($user && $user->can('master-pelabuhan-view'))
                    <a href="{{ route('master-pelabuhan.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master-pelabuhan.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Master Pelabuhan</span>
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Master Kapal --}}
        @if($user && $user->can('master-kapal.view'))
        <div class="mx-2 mb-3">
            <a href="{{ route('master-kapal.index') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master-kapal.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Master Kapal</span>
            </a>
        </div>
        @endif

        {{-- Master Asset --}}
        @if($user && $user->can('master-mobil-view'))
        <div class="mx-2 mb-3">
            <a href="{{ route('master.mobil.index') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.mobil.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Master Asset</span>
            </a>
        </div>
        @endif
    </div>
</div>
@endif

{{-- Aktivitas Dropdown --}}
@php
    $isAktivitasRoute = Request::routeIs('permohonan.*') || Request::routeIs('pranota-supir.*') || Request::routeIs('pembayaran-pranota-supir.*') || Request::routeIs('orders.*') || Request::routeIs('pranota-uang-jalan.*') || Request::routeIs('uang-jalan.*') || Request::routeIs('pembayaran-pranota-uang-jalan.*') || Request::routeIs('pranota-rit.*') || Request::routeIs('pranota-uang-rit.*') || Request::routeIs('surat-jalan.*') || Request::routeIs('surat-jalan-bongkaran.*') || Request::routeIs('aktivitas-kontainer.*') || Request::routeIs('daftar-tagihan-kontainer-sewa.*') || Request::routeIs('pranota-kontainer-sewa.*') || Request::routeIs('pembayaran-pranota-kontainer.*') || Request::routeIs('pranota.*') || Request::routeIs('perbaikan-kontainer.*') || Request::routeIs('pranota-perbaikan-kontainer.*') || Request::routeIs('pembayaran-pranota-perbaikan-kontainer.*') || Request::routeIs('tagihan-cat.*') || Request::routeIs('pranota-cat.*') || Request::routeIs('pembayaran-pranota-cat.*') || Request::routeIs('tagihan-ob.*') || Request::routeIs('tanda-terima.*') || Request::routeIs('tanda-terima-tanpa-surat-jalan.*') || Request::routeIs('gate-in.*') || Request::routeIs('aktivitas-kapal.*') || Request::routeIs('pergerakan-kapal.*') || Request::routeIs('voyage.*') || Request::routeIs('jadwal-kapal.*') || Request::routeIs('status-kapal.*') || Request::routeIs('log-aktivitas-kapal.*') || Request::routeIs('monitoring-kapal.*') || Request::routeIs('naik-kapal.*') || Request::routeIs('bl.*') || Request::routeIs('approval.surat-jalan.*') || Request::routeIs('approval.*') || Request::routeIs('approval-ii.*') || Request::routeIs('pembayaran-aktivitas-lainnya.*');
    $hasAktivitasPermissions = $user && (
        $user->can('permohonan-memo-view') ||
        $user->can('pranota-supir-view') ||
        $user->can('pembayaran-pranota-supir-view') ||
        $user->can('order-view') ||
        $user->can('order-create') ||
        $user->can('order-update') ||
        $user->can('order-delete') ||
        $user->can('pranota-uang-jalan-view') ||
        $user->can('pranota-rit-view') ||
        $user->can('pranota-uang-rit-view') ||
        $user->can('surat-jalan-view') ||
        $user->can('surat-jalan-create') ||
        $user->can('surat-jalan-update') ||
        $user->can('surat-jalan-delete') ||
        $user->can('surat-jalan-bongkaran-view') ||
        $user->can('surat-jalan-bongkaran-create') ||
        $user->can('surat-jalan-bongkaran-update') ||
        $user->can('surat-jalan-bongkaran-delete') ||
        $user->can('aktivitas-kontainer-view') ||
        $user->can('tagihan-kontainer-sewa-index') ||
        $user->can('pranota-kontainer-sewa-view') ||
        $user->can('pembayaran-pranota-kontainer-view') ||
        $user->can('pranota.view') ||
        $user->can('perbaikan-kontainer-view') ||
        $user->can('pranota-perbaikan-kontainer-view') ||
        $user->can('pembayaran-pranota-perbaikan-kontainer-view') ||
        $user->can('tagihan-cat-view') ||
        $user->can('pranota-cat-view') ||
        $user->can('pembayaran-pranota-cat-view') ||
        $user->can('tagihan-ob-view') ||
        $user->can('tanda-terima-view') ||
        $user->can('tanda-terima-update') ||
        $user->can('tanda-terima-delete') ||
        $user->can('tanda-terima-tanpa-surat-jalan-view') ||
        $user->can('tanda-terima-tanpa-surat-jalan-create') ||
        $user->can('tanda-terima-tanpa-surat-jalan-update') ||
        $user->can('tanda-terima-tanpa-surat-jalan-delete') ||
        $user->can('gate-in-view') ||
        $user->can('gate-in-create') ||
        $user->can('gate-in-update') ||
        $user->can('gate-in-delete') ||
        $user->can('aktivitas-kapal-view') ||
        $user->can('pergerakan-kapal-view') ||
        $user->can('voyage-view') ||
        $user->can('jadwal-kapal-view') ||
        $user->can('status-kapal-view') ||
        $user->can('log-aktivitas-kapal-view') ||
        $user->can('monitoring-kapal-view') ||
        $user->can('prospek-edit') ||
        $user->can('bl-view') ||
        $user->can('approval-surat-jalan-view') ||
        $user->can('approval-view') ||
        $user->can('approval-approve') ||
        $user->can('approval-print') ||
        $user->can('approval-dashboard') ||
        $user->can('approval') ||
        $user->can('permohonan.approve') ||
        $user->can('pembayaran-aktivitas-lainnya-view') ||
        $user->can('pembayaran-aktivitas-lainnya-create') ||
        $user->can('pembayaran-aktivitas-lainnya-update') ||
        $user->can('pembayaran-aktivitas-lainnya-delete')
    );
    $showAktivitasSection = $isAdmin || $hasAktivitasPermissions;
@endphp

@if($showAktivitasSection)
<div class="mt-4 mb-6">
    <button id="aktivitas-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-sm font-medium {{ $isAktivitasRoute ? 'bg-indigo-50 text-indigo-700' : '' }}">
        <span class="text-sm font-semibold">Aktivitas</span>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isAktivitasRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="aktivitas-menu-content" class="dropdown-content ml-2 mt-3 space-y-2" @if($isAktivitasRoute) style="display: block;" @endif>

        {{-- Aktivitas Supir Sub-Dropdown --}}
        @php
            $isAktivitasSupirRoute = Request::routeIs('permohonan.*') || Request::routeIs('pranota-supir.*') || Request::routeIs('pembayaran-pranota-supir.*') || Request::routeIs('orders.*') || Request::routeIs('pranota-uang-jalan.*') || Request::routeIs('uang-jalan.*') || Request::routeIs('pembayaran-pranota-uang-jalan.*') || Request::routeIs('pranota-rit.*') || Request::routeIs('pranota-uang-rit.*') || Request::routeIs('surat-jalan.*') || Request::routeIs('surat-jalan-bongkaran.*');
            $hasAktivitasSupirPermissions = $user && ($user->can('permohonan-memo-view') || $user->can('pranota-supir-view') || $user->can('pembayaran-pranota-supir-view') || $user->can('order-view') || $user->can('order-create') || $user->can('order-update') || $user->can('order-delete') || $user->can('pranota-uang-jalan-view') || $user->can('pranota-rit-view') || $user->can('pranota-uang-rit-view') || $user->can('surat-jalan-view') || $user->can('surat-jalan-create') || $user->can('surat-jalan-update') || $user->can('surat-jalan-delete') || $user->can('surat-jalan-bongkaran-view') || $user->can('surat-jalan-bongkaran-create') || $user->can('surat-jalan-bongkaran-update') || $user->can('surat-jalan-bongkaran-delete'));
        @endphp

        @if($hasAktivitasSupirPermissions)
        <div class="mx-2 mb-3">
            <button id="aktivitas-supir-menu-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 group {{ $isAktivitasSupirRoute ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Aktivitas Supir</span>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAktivitasSupirRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="aktivitas-supir-menu-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isAktivitasSupirRoute) style="display: block;" @endif>
                
                {{-- Memo Sub-Dropdown --}}
                @php
                    $isMemoRoute = Request::routeIs('permohonan.*') || Request::routeIs('pranota-supir.*') || Request::routeIs('pembayaran-pranota-supir.*');
                    $hasMemoPermissions = $user && ($user->can('permohonan-memo-view') || $user->can('pranota-supir-view') || $user->can('pembayaran-pranota-supir-view'));
                @endphp

                @if($hasMemoPermissions)
                <div class="mx-1 mb-2">
                    <button id="memo-menu-toggle" class="w-full flex justify-between items-center py-1.5 px-2 rounded-md text-xs hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 group {{ $isMemoRoute ? 'bg-indigo-50 text-indigo-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-medium">Memo</span>
                        <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isMemoRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="memo-menu-content" class="dropdown-content ml-3 mt-1 space-y-1" @if($isMemoRoute) style="display: block;" @endif>
                        {{-- Permohonan Memo --}}
                        @if($user && $user->can('permohonan-memo-view'))
                            <a href="{{ route('permohonan.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 {{ Request::routeIs('permohonan.*') ? 'bg-indigo-50 text-indigo-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Permohonan Memo</span>
                            </a>
                        @endif

                        {{-- Pranota Supir --}}
                        @if($user && $user->can('pranota-supir-view'))
                            <a href="{{ route('pranota-supir.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 {{ Request::routeIs('pranota-supir.*') ? 'bg-indigo-50 text-indigo-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pranota Supir</span>
                            </a>
                        @endif

                        {{-- Bayar Pranota Supir --}}
                        @if(Route::has('pembayaran-pranota-supir.index') && $user && $user->can('pembayaran-pranota-supir-view'))
                            <a href="{{ route('pembayaran-pranota-supir.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-supir.*') ? 'bg-indigo-50 text-indigo-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Bayar Pranota Supir</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Uang Jalan Sub-Dropdown --}}
                @php
                    $isUangJalanRoute = Request::routeIs('uang-jalan.*') || Request::routeIs('pranota-uang-jalan.*') || Request::routeIs('pembayaran-pranota-uang-jalan.*');
                    $hasUangJalanPermissions = $user && (($user->can('uang-jalan-view') || $user->can('uang-jalan-create') || $user->can('uang-jalan-update') || $user->can('uang-jalan-delete')) || $user->can('pranota-uang-jalan-view') || $user->can('pembayaran-pranota-uang-jalan-view'));
                @endphp

                @if($hasUangJalanPermissions)
                <div class="mx-1 mb-2">
                    <button id="uang-jalan-menu-toggle" class="w-full flex justify-between items-center py-1.5 px-2 rounded-md text-xs hover:bg-yellow-50 hover:text-yellow-700 transition-all duration-200 group {{ $isUangJalanRoute ? 'bg-yellow-50 text-yellow-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-medium">Uang Jalan</span>
                        <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isUangJalanRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="uang-jalan-menu-content" class="dropdown-content ml-3 mt-1 space-y-1" @if($isUangJalanRoute) style="display: block;" @endif>
                        {{-- Uang Jalan --}}
                        @if($user && ($user->can('uang-jalan-view') || $user->can('uang-jalan-create') || $user->can('uang-jalan-update') || $user->can('uang-jalan-delete')))
                            <a href="{{ route('uang-jalan.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-yellow-50 hover:text-yellow-700 transition-all duration-200 {{ Request::routeIs('uang-jalan.*') ? 'bg-yellow-50 text-yellow-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Uang Jalan</span>
                            </a>
                        @endif

                        {{-- Pranota Uang Jalan --}}
                        @if($user && $user->can('pranota-uang-jalan-view'))
                            <a href="{{ route('pranota-uang-jalan.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-yellow-50 hover:text-yellow-700 transition-all duration-200 {{ Request::routeIs('pranota-uang-jalan.*') ? 'bg-yellow-50 text-yellow-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pranota Uang Jalan</span>
                            </a>
                        @endif

                        {{-- Bayar Pranota Uang Jalan --}}
                        @if(Route::has('pembayaran-pranota-uang-jalan.index') && $user && $user->can('pembayaran-pranota-uang-jalan-view'))
                            <a href="{{ route('pembayaran-pranota-uang-jalan.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-yellow-50 hover:text-yellow-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-uang-jalan.*') ? 'bg-yellow-50 text-yellow-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Bayar Pranota Uang Jalan</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Rit Sub-Dropdown --}}
                @php
                    $isRitRoute = Request::routeIs('pranota-rit.*') || Request::routeIs('pranota-uang-rit.*');
                    $hasRitPermissions = $user && ($user->can('pranota-rit-view') || $user->can('pranota-uang-rit-view'));
                @endphp

                @if($hasRitPermissions)
                <div class="mx-1 mb-2">
                    <button id="rit-menu-toggle" class="w-full flex justify-between items-center py-1.5 px-2 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 group {{ $isRitRoute ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-medium">Rit</span>
                        <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isRitRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="rit-menu-content" class="dropdown-content ml-3 mt-1 space-y-1" @if($isRitRoute) style="display: block;" @endif>
                        {{-- Pranota Rit --}}
                        @if($user && $user->can('pranota-rit-view'))
                            <a href="{{ route('pranota-rit.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('pranota-rit.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pranota Rit</span>
                            </a>
                        @endif

                        {{-- Pranota Uang Rit --}}
                        @if($user && $user->can('pranota-uang-rit-view'))
                            <a href="{{ route('pranota-uang-rit.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('pranota-uang-rit.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pranota Uang Rit</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Pranota Uang Kenek --}}
                @if($user && $user->can('pranota-uang-kenek-view'))
                    <a href="{{ route('pranota-uang-kenek.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('pranota-uang-kenek.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pranota Uang Kenek</span>
                    </a>
                @endif

                {{-- Surat Jalan Sub-Dropdown --}}
                @php
                    $isSuratJalanRoute = Request::routeIs('orders.*') || Request::routeIs('surat-jalan.*');
                    $hasSuratJalanPermissions = $user && (($user->can('order-view') || $user->can('order-create') || $user->can('order-update') || $user->can('order-delete')) || ($user->can('surat-jalan-view') || $user->can('surat-jalan-create') || $user->can('surat-jalan-update') || $user->can('surat-jalan-delete')));
                @endphp

                @if($hasSuratJalanPermissions)
                <div class="mx-1 mb-2">
                    <button id="surat-jalan-menu-toggle" class="w-full flex justify-between items-center py-1.5 px-2 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 group {{ $isSuratJalanRoute ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-medium">Surat Jalan</span>
                        <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isSuratJalanRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="surat-jalan-menu-content" class="dropdown-content ml-3 mt-1 space-y-1" @if($isSuratJalanRoute) style="display: block;" @endif>
                        {{-- Order Management --}}
                        @if($user && ($user->can('order-view') || $user->can('order-create') || $user->can('order-update') || $user->can('order-delete')))
                            <a href="{{ route('orders.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('orders.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs">Order Management</span>
                            </a>
                        @endif

                        {{-- Surat Jalan --}}
                        @if($user && ($user->can('surat-jalan-view') || $user->can('surat-jalan-create') || $user->can('surat-jalan-update') || $user->can('surat-jalan-delete')))
                            <a href="{{ route('surat-jalan.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('surat-jalan.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs">Surat Jalan</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Surat Jalan Bongkaran (tetap di luar) --}}
                @if($user && ($user->can('surat-jalan-bongkaran-view') || $user->can('surat-jalan-bongkaran-create') || $user->can('surat-jalan-bongkaran-update') || $user->can('surat-jalan-bongkaran-delete')))
                    <a href="{{ route('surat-jalan-bongkaran.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('surat-jalan-bongkaran.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        Surat Jalan Bongkaran
                    </a>
                @endif


            </div>
        </div>
        @endif

        {{-- Aktivitas Kontainer Sub-Dropdown --}}
        @php
            $isAktivitasKontainerRoute = Request::routeIs('daftar-tagihan-kontainer-sewa.*') || Request::routeIs('pranota-kontainer-sewa.*') || Request::routeIs('pembayaran-pranota-kontainer.*') || Request::routeIs('pranota.*') || Request::routeIs('perbaikan-kontainer.*') || Request::routeIs('pranota-perbaikan-kontainer.*') || Request::routeIs('pembayaran-pranota-perbaikan-kontainer.*') || Request::routeIs('tagihan-cat.*') || Request::routeIs('pranota-cat.*') || Request::routeIs('pembayaran-pranota-cat.*');
            $hasAktivitasKontainerPermissions = $user && ($user->can('tagihan-kontainer-sewa-index') || $user->can('pranota-kontainer-sewa-view') || $user->can('pembayaran-pranota-kontainer-view') || $user->can('pranota.view') || $user->can('perbaikan-kontainer-view') || $user->can('pranota-perbaikan-kontainer-view') || $user->can('pembayaran-pranota-perbaikan-kontainer-view') || $user->can('tagihan-cat-view') || $user->can('pranota-cat-view') || $user->can('pembayaran-pranota-cat-view'));
        @endphp

        @if($hasAktivitasKontainerPermissions)
        <div class="mx-2 mb-3">
            <button id="aktivitas-aktivitas-kontainer-menu-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 group {{ $isAktivitasKontainerRoute ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Aktivitas Kontainer</span>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAktivitasKontainerRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="aktivitas-aktivitas-kontainer-menu-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isAktivitasKontainerRoute) style="display: block;" @endif>
                {{-- Sewa Sub-Dropdown --}}
                @php
                    $isSewaRoute = Request::routeIs('daftar-tagihan-kontainer-sewa.*') || Request::routeIs('pranota-kontainer-sewa.*') || Request::routeIs('pembayaran-pranota-kontainer.*');
                    $hasSewaPermissions = $user && ($user->can('tagihan-kontainer-sewa-index') || $user->can('pranota-kontainer-sewa-view') || $user->can('pembayaran-pranota-kontainer-view'));
                @endphp

                @if($hasSewaPermissions)
                <div class="mb-2">
                    <button id="sewa-menu-toggle" class="w-full flex justify-between items-center py-1 px-3 rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isSewaRoute ? 'bg-cyan-50 text-cyan-700 font-medium' : '' }}">
                        <span class="text-xs font-medium">Sewa</span>
                        <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isSewaRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="sewa-menu-content" class="dropdown-content ml-4 mt-1 space-y-1" @if($isSewaRoute) style="display: block;" @endif>
                        {{-- Tagihan Kontainer Sewa --}}
                        @if($user && $user->can('tagihan-kontainer-sewa-index'))
                            <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-cyan-50 hover:text-cyan-700 transition-all duration-200 {{ Request::routeIs('daftar-tagihan-kontainer-sewa.*') ? 'bg-cyan-50 text-cyan-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Tagihan Kontainer Sewa</span>
                            </a>
                        @endif

                        {{-- Pranota Kontainer Sewa --}}
                        @if($user && $user->can('pranota-kontainer-sewa-view'))
                            <a href="{{ route('pranota-kontainer-sewa.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-cyan-50 hover:text-cyan-700 transition-all duration-200 {{ Request::routeIs('pranota-kontainer-sewa.*') ? 'bg-cyan-50 text-cyan-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pranota Kontainer Sewa</span>
                            </a>
                        @endif

                        {{-- Pembayaran Pranota Kontainer Sewa --}}
                        @if(Route::has('pembayaran-pranota-kontainer.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-kontainer-view')))
                            <a href="{{ route('pembayaran-pranota-kontainer.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-cyan-50 hover:text-cyan-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-kontainer.*') ? 'bg-cyan-50 text-cyan-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pembayaran Pranota Kontainer Sewa</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Perbaikan Sub-Dropdown --}}
                @php
                    $isPerbaikanRoute = Request::routeIs('perbaikan-kontainer.*') || Request::routeIs('pranota-perbaikan-kontainer.*') || Request::routeIs('pembayaran-pranota-perbaikan-kontainer.*');
                    $hasPerbaikanPermissions = $user && ($user->can('tagihan-perbaikan-kontainer-view') || $user->can('pranota-perbaikan-kontainer-view') || $user->can('pembayaran-pranota-perbaikan-kontainer-view'));
                @endphp

                @if($hasPerbaikanPermissions)
                <div class="mb-2">
                    <button id="perbaikan-menu-toggle" class="w-full flex justify-between items-center py-1 px-3 rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isPerbaikanRoute ? 'bg-amber-50 text-amber-700 font-medium' : '' }}">
                        <span class="text-xs font-medium">Perbaikan</span>
                        <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isPerbaikanRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="perbaikan-menu-content" class="dropdown-content ml-4 mt-1 space-y-1" @if($isPerbaikanRoute) style="display: block;" @endif>
                        {{-- Tagihan Perbaikan Kontainer --}}
                        @if($user && $user->can('tagihan-perbaikan-kontainer-view'))
                            <a href="{{ route('perbaikan-kontainer.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-amber-50 hover:text-amber-700 transition-all duration-200 {{ Request::routeIs('perbaikan-kontainer.*') ? 'bg-amber-50 text-amber-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Tagihan Perbaikan Kontainer</span>
                            </a>
                        @endif

                        {{-- Pranota Perbaikan Kontainer --}}
                        @if($user && $user->can('pranota-perbaikan-kontainer-view'))
                            <a href="{{ route('pranota-perbaikan-kontainer.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-amber-50 hover:text-amber-700 transition-all duration-200 {{ Request::routeIs('pranota-perbaikan-kontainer.*') ? 'bg-amber-50 text-amber-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pranota Perbaikan Kontainer</span>
                            </a>
                        @endif

                        {{-- Pembayaran Pranota Perbaikan Kontainer --}}
                        @if(Route::has('pembayaran-pranota-perbaikan-kontainer.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-perbaikan-kontainer-view')))
                            <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-amber-50 hover:text-amber-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-perbaikan-kontainer.*') ? 'bg-amber-50 text-amber-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pembayaran Pranota Perbaikan Kontainer</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- CAT Sub-Dropdown --}}
                @php
                    $isCatRoute = Request::routeIs('tagihan-cat.*') || Request::routeIs('pranota-cat.*') || Request::routeIs('pembayaran-pranota-cat.*');
                    $hasCatPermissions = $user && ($user->can('tagihan-cat-view') || $user->can('pranota-cat-view') || $user->can('pembayaran-pranota-cat-view'));
                @endphp

                @if($hasCatPermissions)
                <div class="mb-2">
                    <button id="cat-menu-toggle" class="w-full flex justify-between items-center py-1 px-3 rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isCatRoute ? 'bg-rose-50 text-rose-700 font-medium' : '' }}">
                        <span class="text-xs font-medium">CAT</span>
                        <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isCatRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="cat-menu-content" class="dropdown-content ml-4 mt-1 space-y-1" @if($isCatRoute) style="display: block;" @endif>
                        {{-- Daftar Tagihan CAT --}}
                        @if($user && $user->can('tagihan-cat-view'))
                            <a href="{{ route('tagihan-cat.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-rose-50 hover:text-rose-700 transition-all duration-200 {{ Request::routeIs('tagihan-cat.*') ? 'bg-rose-50 text-rose-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Daftar Tagihan CAT</span>
                            </a>
                        @endif

                        {{-- Pranota Tagihan CAT --}}
                        @if($user && $user->can('pranota-cat-view'))
                            <a href="{{ route('pranota-cat.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-rose-50 hover:text-rose-700 transition-all duration-200 {{ Request::routeIs('pranota-cat.*') ? 'bg-rose-50 text-rose-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pranota Tagihan CAT</span>
                            </a>
                        @endif

                        {{-- Pembayaran Pranota CAT Kontainer --}}
                        @if(Route::has('pembayaran-pranota-cat.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-cat-view')))
                            <a href="{{ route('pembayaran-pranota-cat.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-rose-50 hover:text-rose-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-cat.*') ? 'bg-rose-50 text-rose-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Bayar Pranota CAT Kontainer</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif


            </div>
        </div>
        @endif

        {{-- Aktivitas Kapal Sub-Dropdown --}}
        @php
            $isAktivitasKapalRoute = Request::routeIs('aktivitas-kapal.*') || Request::routeIs('pergerakan-kapal.*') || Request::routeIs('voyage.*') || Request::routeIs('jadwal-kapal.*') || Request::routeIs('status-kapal.*') || Request::routeIs('log-aktivitas-kapal.*') || Request::routeIs('monitoring-kapal.*') || Request::routeIs('naik-kapal.*') || Request::routeIs('bl.*') || Request::routeIs('prospek.*') || Request::routeIs('tagihan-ob.*');
            $hasAktivitasKapalPermissions = $user && ($user->can('aktivitas-kapal-view') || $user->can('pergerakan-kapal-view') || $user->can('voyage-view') || $user->can('jadwal-kapal-view') || $user->can('status-kapal-view') || $user->can('log-aktivitas-kapal-view') || $user->can('monitoring-kapal-view') || $user->can('prospek-edit') || $user->can('prospek-view') || $user->can('bl-view') || $user->can('tagihan-ob-view'));
        @endphp

        @if($hasAktivitasKapalPermissions)
        <div class="mx-2 mb-3">
            <button id="aktivitas-kapal-menu-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 group {{ $isAktivitasKapalRoute ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Aktivitas Kapal</span>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAktivitasKapalRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="aktivitas-kapal-menu-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isAktivitasKapalRoute) style="display: block;" @endif>
                {{-- Prospek --}}
                @if($user && $user->can('prospek-view'))
                    <a href="{{ route('prospek.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('prospek.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Prospek</span>
                    </a>
                @endif

                {{-- Naik Kapal --}}
                @if($user && $user->can('prospek-edit'))
                    <a href="{{ route('naik-kapal.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('naik-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Naik Kapal</span>
                    </a>
                @endif

                {{-- BL (Bill of Lading) --}}
                @if($user && $user->can('bl-view'))
                    <a href="{{ route('bl.select') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('bl.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">BL (Bill of Lading)</span>
                    </a>
                @endif

                {{-- Tagihan OB --}}
                @if($user && $user->can('tagihan-ob-view'))
                    <a href="{{ route('tagihan-ob.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('tagihan-ob.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Tagihan OB</span>
                    </a>
                @endif

                {{-- Pergerakan Kapal --}}
                @if($user && $user->can('pergerakan-kapal-view'))
                    <a href="{{ route('pergerakan-kapal.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('pergerakan-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pergerakan Kapal</span>
                    </a>
                @endif

                {{-- Daftar Voyage --}}
                @if($user && $user->can('voyage-view'))
                    <a href="{{ route('voyage.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('voyage.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Daftar Voyage</span>
                    </a>
                @endif

                {{-- Jadwal Kapal --}}
                @if($user && $user->can('jadwal-kapal-view'))
                    <a href="{{ route('jadwal-kapal.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('jadwal-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Jadwal Kapal</span>
                    </a>
                @endif

                {{-- Status Kapal --}}
                @if($user && $user->can('status-kapal-view'))
                    <a href="{{ route('status-kapal.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('status-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Status Kapal</span>
                    </a>
                @endif

                {{-- Log Aktivitas --}}
                @if($user && $user->can('log-aktivitas-kapal-view'))
                    <a href="{{ route('log-aktivitas-kapal.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('log-aktivitas-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Log Aktivitas</span>
                    </a>
                @endif

                {{-- Monitoring Kapal --}}
                @if($user && $user->can('monitoring-kapal-view'))
                    <a href="{{ route('monitoring-kapal.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('monitoring-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Monitoring Kapal</span>
                    </a>
                @endif

                {{-- Prospek --}}
                @if($user && $user->can('prospek-view'))
                    <a href="{{ route('prospek.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('prospek.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Prospek</span>
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Aktivitas Pelabuhan Sub-Dropdown --}}
        @php
            $isAktivitasPelabuhanRoute = Request::routeIs('approval.surat-jalan.*') || Request::routeIs('tanda-terima.*') || Request::routeIs('tanda-terima-tanpa-surat-jalan.*') || Request::routeIs('gate-in.*');
            $hasAktivitasPelabuhanPermissions = $user && ($user->can('approval-surat-jalan-view') || $user->can('tanda-terima-view') || $user->can('tanda-terima-update') || $user->can('tanda-terima-delete') || $user->can('tanda-terima-tanpa-surat-jalan-view') || $user->can('tanda-terima-tanpa-surat-jalan-create') || $user->can('tanda-terima-tanpa-surat-jalan-update') || $user->can('tanda-terima-tanpa-surat-jalan-delete') || $user->can('gate-in-view') || $user->can('gate-in-create') || $user->can('gate-in-update') || $user->can('gate-in-delete'));
        @endphp

        @if($hasAktivitasPelabuhanPermissions)
        <div class="mx-2 mb-3">
            <button id="aktivitas-pelabuhan-menu-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 group {{ $isAktivitasPelabuhanRoute ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Aktivitas Pelabuhan</span>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAktivitasPelabuhanRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="aktivitas-pelabuhan-menu-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isAktivitasPelabuhanRoute) style="display: block;" @endif>
                {{-- Approval Surat Jalan --}}
                @if($user && $user->can('approval-surat-jalan-view'))
                    <a href="{{ Route::has('approval.surat-jalan.index') ? route('approval.surat-jalan.index') : '#' }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('approval.surat-jalan.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Approval Surat Jalan</span>
                        @if(!Route::has('approval.surat-jalan.index'))
                            <span class="ml-auto text-xs text-gray-400 italic">(in dev)</span>
                        @endif
                    </a>
                @endif

                {{-- Tanda Terima --}}
                @if($user && ($user->can('tanda-terima-view') || $user->can('tanda-terima-update') || $user->can('tanda-terima-delete')))
                    <a href="{{ route('tanda-terima.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('tanda-terima.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Tanda Terima</span>
                    </a>
                @endif

                {{-- Tanda Terima Tanpa Surat Jalan --}}
                @if($user && ($user->can('tanda-terima-tanpa-surat-jalan-view') || $user->can('tanda-terima-tanpa-surat-jalan-create') || $user->can('tanda-terima-tanpa-surat-jalan-update') || $user->can('tanda-terima-tanpa-surat-jalan-delete')))
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('tanda-terima-tanpa-surat-jalan.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Tanda Terima Tanpa Surat Jalan</span>
                    </a>
                @endif

                {{-- Gate In --}}
                @if($user && ($user->can('gate-in-view') || $user->can('gate-in-create') || $user->can('gate-in-update') || $user->can('gate-in-delete')))
                    <a href="{{ route('gate-in.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('gate-in.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Gate In</span>
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Approval Tugas Sub-Dropdown --}}
        @php
            $isApprovalTugasRoute = (Request::routeIs('approval.*') && !Request::routeIs('approval.surat-jalan.*')) || Request::routeIs('approval-ii.*');
            $hasApprovalTugasPermissions = $user && ($user->can('approval-view') || $user->can('approval-approve') || $user->can('approval-print') || $user->can('approval-dashboard') || $user->can('approval') || $user->can('permohonan.approve'));
        @endphp

        @if($hasApprovalTugasPermissions)
        <div class="mx-2 mb-3">
            <button id="approval-tugas-menu-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 group {{ $isApprovalTugasRoute ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Approval Tugas</span>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isApprovalTugasRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="approval-tugas-menu-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isApprovalTugasRoute) style="display: block;" @endif>
                {{-- Approval Tugas --}}
                @if(Route::has('approval.dashboard'))
                    <a href="{{ route('approval.dashboard') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('approval.dashboard') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Approval Tugas
                    </a>
                @endif

                {{-- Approval Tugas II --}}
                @if(Route::has('approval-ii.dashboard'))
                    <a href="{{ route('approval-ii.dashboard') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('approval-ii.dashboard') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Approval Tugas II
                    </a>
                @endif


            </div>
        </div>
        @endif

        {{-- Aktivitas Lain-Lain Sub-Dropdown --}}
        @php
            $isAktivitasLainRoute = Request::routeIs('pembayaran-aktivitas-lainnya.*');
            $hasAktivitasLainPermissions = $isAdmin || ($user && ($user->can('pembayaran-aktivitas-lainnya-view') || $user->can('pembayaran-aktivitas-lainnya-create') || $user->can('pembayaran-aktivitas-lainnya-update') || $user->can('pembayaran-aktivitas-lainnya-delete')));
        @endphp

        @if($hasAktivitasLainPermissions)
        <div class="mx-2 mb-3">
            <button id="aktivitas-lain-menu-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-pink-50 hover:text-pink-700 transition-all duration-200 group {{ $isAktivitasLainRoute ? 'bg-pink-50 text-pink-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Aktivitas Lain-Lain</span>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAktivitasLainRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="aktivitas-lain-menu-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isAktivitasLainRoute) style="display: block;" @endif>
                {{-- Pembayaran Aktivitas Lain-Lain --}}
                @if($isAdmin || ($user && $user->can('pembayaran-aktivitas-lainnya-view')))
                    <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-pink-50 hover:text-pink-700 transition-all duration-200 {{ Request::routeIs('pembayaran-aktivitas-lainnya.*') ? 'bg-pink-50 text-pink-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pembayaran Aktivitas Lain-Lain</span>
                    </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endif





{{-- Report Dropdown --}}
@php
    $isReportRoute = Request::routeIs('report.tagihan.*') || Request::routeIs('report.pranota.*') || Request::routeIs('report.pembayaran.*');
    // Check if user has view permissions for tagihan, pranota, or pembayaran modules
    $hasReportPermission = $user && (
        $isAdmin ||
        $user->can('tagihan-kontainer-view') ||
        $user->can('pranota-tagihan-view') ||
        $user->can('pembayaran-pranota-kontainer-view') ||
        $user->can('pembayaran-pranota-perbaikan-kontainer-view') ||
        $user->can('pembayaran-pranota-cat-view')
    );
@endphp
@if($hasReportPermission)
<div class="mt-4 mb-4">
    <button id="report-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isReportRoute ? 'bg-purple-50 text-purple-700 font-medium' : '' }}">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isReportRoute ? 'bg-purple-100' : '' }}">
                <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isReportRoute ? 'text-purple-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium truncate w-full">Report</span>
        </div>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isReportRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="report-menu-content" class="dropdown-content ml-8 space-y-2 mt-2" @if($isReportRoute) style="display: block;" @endif>
        {{-- Report Tagihan --}}
        <a href="{{ route('report.tagihan.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('report.tagihan.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Report Tagihan
        </a>

        {{-- Report Pranota --}}
        <a href="{{ Route::has('report.pranota.index') ? route('report.pranota.index') : '#' }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('report.pranota.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Report Pranota
            @if(!Route::has('report.pranota.index'))
                <span class="ml-auto text-xs text-gray-400 italic">(soon)</span>
            @endif
        </a>

        {{-- Report Pembayaran --}}
        <a href="{{ Route::has('report.pembayaran.index') ? route('report.pembayaran.index') : '#' }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('report.pembayaran.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Report Pembayaran
            @if(!Route::has('report.pembayaran.index'))
                <span class="ml-auto text-xs text-gray-400 italic">(soon)</span>
            @endif
        </a>
    </div>
</div>
@endif

{{-- Audit Log Menu - Standalone --}}
@if($user && $user->can('audit-log-view'))
<div class="mt-4">
    <a href="{{ route('audit-logs.index') }}" class="w-full flex items-center py-2 px-5 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ Request::routeIs('audit-logs.*') ? 'bg-orange-50 text-orange-700 font-medium' : '' }}">
        <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ Request::routeIs('audit-logs.*') ? 'bg-orange-100' : '' }}">
            <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ Request::routeIs('audit-logs.*') ? 'text-orange-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
        </div>
        <span class="text-xs font-medium truncate w-full">Audit Log</span>
        <span class="ml-auto">
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </span>
    </a>
</div>
@endif

            </nav>
        </div>
    @endif

    <!-- Page Content -->
    <div class="flex-1 overflow-auto {{ $showSidebar ? 'lg:ml-64' : '' }}">
        <div class="p-6">
            @yield('content')
        </div>
    </div>        <!-- Mobile overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>
    </div>

    {{-- jQuery CDN - Required for many scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

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
                const icon = toggleButton.querySelector('.dropdown-arrow');
                toggleButton.addEventListener('click', function() {
                    const isVisible = content.style.display === 'block';
                    content.style.display = isVisible ? 'none' : 'block';
                    if(icon) icon.classList.toggle('rotate-180', !isVisible);
                });
            }
        }

        // Profile dropdown functionality
        const profileDropdownButton = document.getElementById('profileDropdownButton');
        const profileDropdown = document.getElementById('profileDropdown');

        profileDropdownButton.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdownButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });

        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeSidebar = document.getElementById('close-sidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const mainContent = document.querySelector('main') || document.querySelector('.flex.flex-1');

        function openSidebar() {
            if (sidebar) {
                sidebar.classList.remove('-translate-x-full');
            }
            if (overlay) {
                overlay.classList.remove('hidden');
            }
            // Only prevent main content scroll, not sidebar scroll
            if (mainContent) {
                mainContent.style.overflow = 'hidden';
            }
            // Prevent background scroll on mobile
            document.documentElement.style.overflow = 'hidden';
        }

        function closeSidebarMenu() {
            if (sidebar) {
                sidebar.classList.add('-translate-x-full');
            }
            if (overlay) {
                overlay.classList.add('hidden');
            }
            // Restore main content scroll
            if (mainContent) {
                mainContent.style.overflow = '';
            }
            // Restore background scroll
            document.documentElement.style.overflow = '';
        }

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', openSidebar);
        }

        if (closeSidebar) {
            closeSidebar.addEventListener('click', closeSidebarMenu);
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebarMenu);
        }

        // Close sidebar when clicking on a menu item (on mobile)
        if (sidebar && window.innerWidth < 1024) {
            const menuLinks = sidebar.querySelectorAll('a');
            menuLinks.forEach(link => {
                link.addEventListener('click', () => {
                    setTimeout(closeSidebarMenu, 150); // Small delay for better UX
                });
            });
        }

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                // Reset everything on desktop
                if (mainContent) mainContent.style.overflow = '';
                document.documentElement.style.overflow = '';
                if (overlay) overlay.classList.add('hidden');
            }
        });

        setupDropdown('master-menu-toggle', 'master-menu-content');
        setupDropdown('master-umum-toggle', 'master-umum-content');
        setupDropdown('master-akun-toggle', 'master-akun-content');
        setupDropdown('master-penjualan-toggle', 'master-penjualan-content');
        setupDropdown('master-karyawan-toggle', 'master-karyawan-content');
        setupDropdown('master-tarif-toggle', 'master-tarif-content');
        setupDropdown('master-pemasok-toggle', 'master-pemasok-content');
        setupDropdown('order-menu-toggle', 'order-menu-content');
        setupDropdown('input-menu-toggle', 'input-menu-content');
        setupDropdown('aktivitas-menu-toggle', 'aktivitas-menu-content');
        setupDropdown('aktivitas-supir-menu-toggle', 'aktivitas-supir-menu-content');
        setupDropdown('memo-menu-toggle', 'memo-menu-content');
        setupDropdown('surat-jalan-menu-toggle', 'surat-jalan-menu-content');
        setupDropdown('uang-jalan-menu-toggle', 'uang-jalan-menu-content');
        setupDropdown('rit-menu-toggle', 'rit-menu-content');
        setupDropdown('aktivitas-kontainer-menu-toggle', 'aktivitas-kontainer-menu-content');
        setupDropdown('aktivitas-aktivitas-kontainer-menu-toggle', 'aktivitas-aktivitas-kontainer-menu-content');
        setupDropdown('sewa-menu-toggle', 'sewa-menu-content');
        setupDropdown('perbaikan-menu-toggle', 'perbaikan-menu-content');
        setupDropdown('cat-menu-toggle', 'cat-menu-content');
        setupDropdown('aktivitas-kapal-menu-toggle', 'aktivitas-kapal-menu-content');
        setupDropdown('aktivitas-pelabuhan-menu-toggle', 'aktivitas-pelabuhan-menu-content');
        setupDropdown('approval-tugas-menu-toggle', 'approval-tugas-menu-content');
        setupDropdown('aktivitas-lain-menu-toggle', 'aktivitas-lain-menu-content');
        setupDropdown('approval-input-menu-toggle', 'approval-input-menu-content');
        setupDropdown('uang-jalan-supir-menu-toggle', 'uang-jalan-supir-menu-content');
        setupDropdown('permohonan-memo-menu-toggle', 'permohonan-memo-menu-content');
        setupDropdown('pranota-supir-menu-toggle', 'pranota-supir-menu-content');
        setupDropdown('aktiva-menu-toggle', 'aktiva-menu-content');
        setupDropdown('kontainer-menu-toggle', 'kontainer-menu-content');
        setupDropdown('tagihan-kontainer-menu-toggle', 'tagihan-kontainer-menu-content');
        setupDropdown('report-menu-toggle', 'report-menu-content');

        // Sidebar search functionality
        const sidebarSearch = document.getElementById('sidebar-search');
        const clearSearch = document.getElementById('clear-search');
        const nav = document.querySelector('nav');

        if (sidebarSearch && nav) {
            // Function to filter menu items
            function filterMenuItems(searchTerm) {
                const menuItems = nav.querySelectorAll('a, button');
                const dropdownContents = nav.querySelectorAll('.dropdown-content');
                let hasVisibleItems = false;

                // Reset highlights first
                menuItems.forEach(item => removeHighlight(item));
                dropdownContents.forEach(content => {
                    const subItems = content.querySelectorAll('a');
                    subItems.forEach(subItem => removeHighlight(subItem));
                });

                // If search is empty, restore original state
                if (searchTerm === '') {
                    restoreOriginalState();
                    return;
                }

                menuItems.forEach(item => {
                    const text = item.textContent.toLowerCase().trim();
                    const isVisible = text.includes(searchTerm.toLowerCase());

                    if (item.tagName === 'A' || item.tagName === 'BUTTON') {
                        // Main menu items (Dashboard, Master Data, etc.)
                        if (isVisible) {
                            item.style.display = '';
                            highlightText(item, searchTerm);
                            hasVisibleItems = true;

                            // If this is a dropdown toggle, show its content
                            const contentId = item.id?.replace('-toggle', '-content');
                            if (contentId) {
                                const content = document.getElementById(contentId);
                                if (content) {
                                    content.style.display = 'block';
                                    // Show all submenu items
                                    const subItems = content.querySelectorAll('a');
                                    subItems.forEach(subItem => {
                                        subItem.style.display = '';
                                        const subText = subItem.textContent.toLowerCase().trim();
                                        if (subText.includes(searchTerm.toLowerCase())) {
                                            highlightText(subItem, searchTerm);
                                        }
                                    });
                                }
                            }
                        } else {
                            item.style.display = 'none';

                            // Hide dropdown content if toggle is hidden
                            const contentId = item.id?.replace('-toggle', '-content');
                            if (contentId) {
                                const content = document.getElementById(contentId);
                                if (content) {
                                    content.style.display = 'none';
                                }
                            }
                        }
                    }
                });

                // Handle dropdown contents separately
                dropdownContents.forEach(content => {
                    const subItems = content.querySelectorAll('a');
                    let hasVisibleSubItems = false;

                    subItems.forEach(subItem => {
                        const text = subItem.textContent.toLowerCase().trim();
                        const isVisible = text.includes(searchTerm.toLowerCase());

                        if (isVisible) {
                            subItem.style.display = '';
                            highlightText(subItem, searchTerm);
                            hasVisibleSubItems = true;
                        } else {
                            subItem.style.display = 'none';
                        }
                    });

                    // Show/hide dropdown content based on visible subitems
                    if (hasVisibleSubItems) {
                        content.style.display = 'block';
                        // Also show the parent toggle
                        const toggleId = content.id?.replace('-content', '-toggle');
                        if (toggleId) {
                            const toggle = document.getElementById(toggleId);
                            if (toggle) {
                                toggle.style.display = '';
                                if (toggle.textContent.toLowerCase().trim().includes(searchTerm.toLowerCase())) {
                                    highlightText(toggle, searchTerm);
                                }
                            }
                        }
                    }
                });

                // Show clear button when there's search text
                if (clearSearch) {
                    if (searchTerm.length > 0) {
                        clearSearch.classList.remove('opacity-0', 'pointer-events-none');
                        clearSearch.classList.add('opacity-100', 'pointer-events-auto');
                    } else {
                        clearSearch.classList.add('opacity-0', 'pointer-events-none');
                        clearSearch.classList.remove('opacity-100', 'pointer-events-auto');
                    }
                }
            }

            function highlightText(element, searchTerm) {
                const text = element.textContent;
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                const highlightedText = text.replace(regex, '<span class="sidebar-search-highlight">$1</span>');
                element.innerHTML = highlightedText;
            }

            function removeHighlight(element) {
                // Remove highlight spans and restore original text
                const highlights = element.querySelectorAll('.sidebar-search-highlight');
                highlights.forEach(highlight => {
                    highlight.outerHTML = highlight.textContent;
                });
            }

            function restoreOriginalState() {
                // Show all menu items
                const allItems = nav.querySelectorAll('a, button');
                allItems.forEach(item => {
                    item.style.display = '';
                });

                // Reset all dropdown contents to their original state (closed by default)
                const dropdownContents = nav.querySelectorAll('.dropdown-content');
                dropdownContents.forEach(content => {
                    // Check if this dropdown should be open based on current route
                    const hasActiveClass = content.hasAttribute('style') && content.getAttribute('style').includes('display: block');
                    const hasActiveRoute = content.querySelector('a.bg-blue-50, a.bg-green-50, a.bg-purple-50, a.bg-orange-50, a.bg-indigo-50');
                    
                    if (hasActiveRoute) {
                        content.style.display = 'block';
                    } else {
                        content.style.display = '';
                        content.removeAttribute('style');
                    }
                });

                // Restore dropdown arrows rotation based on active state
                const dropdownToggles = nav.querySelectorAll('button[id$="-toggle"]');
                dropdownToggles.forEach(toggle => {
                    const arrow = toggle.querySelector('.dropdown-arrow');
                    const contentId = toggle.id.replace('-toggle', '-content');
                    const content = document.getElementById(contentId);
                    
                    if (arrow && content) {
                        const isActive = toggle.classList.contains('bg-blue-50') || 
                                        toggle.classList.contains('bg-green-50') || 
                                        toggle.classList.contains('bg-purple-50') || 
                                        toggle.classList.contains('bg-orange-50') ||
                                        content.querySelector('a.bg-blue-50, a.bg-green-50, a.bg-purple-50, a.bg-orange-50, a.bg-indigo-50');
                        
                        if (isActive) {
                            arrow.classList.add('rotate-180');
                            content.style.display = 'block';
                        } else {
                            arrow.classList.remove('rotate-180');
                            content.style.display = '';
                            content.removeAttribute('style');
                        }
                    }
                });
            }

            // Search input event listener
            sidebarSearch.addEventListener('input', function(e) {
                const searchTerm = e.target.value.trim();
                filterMenuItems(searchTerm);
            });

            // Clear search button
            if (clearSearch) {
                clearSearch.addEventListener('click', function() {
                    sidebarSearch.value = '';
                    sidebarSearch.focus();
                    filterMenuItems('');
                });
            }

            // Clear search on Escape key
            sidebarSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    sidebarSearch.value = '';
                    filterMenuItems('');
                }
            });
        }
    </script>
</body>
</html>
