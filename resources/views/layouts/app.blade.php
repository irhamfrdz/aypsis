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
                        $user->can('uang-jalan-batam.view') ||
                        $user->can('master-user-view') ||
                        $user->can('master-karyawan-view') ||
                        $user->can('master-divisi-view') ||
                        $user->can('master-pekerjaan-view') ||
                        $user->can('master-pajak-view') ||
                        $user->can('master-bank-view')
                    );

                    // Show master section if user is admin OR has any master permissions
                    $showMasterSection = $isAdmin || $hasMasterPermissions;
                @endphp

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center py-2 px-5 rounded-xl mt-4 mb-4 transition-all duration-200 group shadow-sm text-xs {{ $isDashboard ? 'bg-blue-100 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-700' }}">
                    <span class="text-xs font-medium menu-text">Dashboard</span>
                </a>

                <!-- Prospek Menu -->
                @php
                    $isProspekRoute = Request::routeIs('prospek.*');
                @endphp

                @if($user && $user->can('prospek-view'))
                <a href="{{ route('prospek.index') }}" class="flex items-center py-2 px-5 rounded-xl mt-4 mb-4 transition-all duration-200 group shadow-sm text-xs {{ $isProspekRoute ? 'bg-green-100 text-green-700 font-bold' : 'text-gray-700 hover:bg-green-100 hover:text-green-700' }}">
                    <span class="text-xs font-medium menu-text">Prospek</span>
                </a>
                @endif

                <!-- Master Data Section -->
                @php
                    $isMasterRoute = Request::routeIs('master-coa-*') || Request::routeIs('master.kode-nomor.*') || Request::routeIs('master.nomor-terakhir.*') || Request::routeIs('master.tipe-akun.*') || Request::routeIs('master.cabang.*') || Request::routeIs('master.kegiatan.*') || Request::routeIs('master-pelabuhan.*') || Request::routeIs('master.karyawan.*') || Request::routeIs('master.user.*') || Request::routeIs('master.divisi.*') || Request::routeIs('master.pekerjaan.*') || Request::routeIs('master.pajak.*') || Request::routeIs('admin.user-approval.*') || Request::routeIs('master-bank-*');
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
                                $isPenjualanRoute = Request::routeIs('pengirim.*') || Request::routeIs('penerima.*') || Request::routeIs('jenis-barang.*') || Request::routeIs('term.*') || Request::routeIs('transportasi.*') || Request::routeIs('tujuan-kirim.*') || Request::routeIs('master.tujuan.*');
                                $hasPenjualanPermissions = $user && ($user->can('master-pengirim-view') || $user->can('master-penerima-view') || $user->can('master-jenis-barang-view') || $user->can('master-term-view') || $user->can('master-transportasi-view') || $user->can('master-tujuan-kirim-view') || $user->can('master-tujuan-view'));
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
                                @if($user && $user->can('master-transportasi-view'))
                                    <a href="{{ route('transportasi.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('transportasi.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
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
                    </div>
                </div>
                @endif

{{-- User Dropdown has been completely moved to Master Data > Master Karyawan --}}

{{-- Order Management Section --}}
@php
    $hasOrderPermissions = $user && ($user->can('order-view') || $user->can('order-create') || $user->can('order-update') || $user->can('order-delete'));
@endphp

@if($hasOrderPermissions)
<a href="{{ route('orders.index') }}" class="flex items-center py-2 px-5 rounded-xl mt-4 mb-4 transition-all duration-200 group shadow-sm text-xs {{ Request::routeIs('orders.*') ? 'bg-orange-100 text-orange-700 font-bold' : 'text-gray-700 hover:bg-orange-100 hover:text-orange-700' }}">
    <div class="flex items-center justify-center w-8 h-8 rounded-xl mr-3 {{ Request::routeIs('orders.*') ? 'bg-orange-200' : 'bg-orange-50 group-hover:bg-orange-200' }}">
        <svg class="w-4 h-4 {{ Request::routeIs('orders.*') ? 'text-orange-700' : 'text-orange-600 group-hover:text-orange-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
    </div>
    <span class="text-xs font-medium menu-text">Order Management</span>
</a>
@endif

{{-- Aktiva Dropdown --}}
@php
    $isAktivaRoute = Request::routeIs('master.kontainer.*') || Request::routeIs('master.master.pricelist-sewa-kontainer.*') || Request::routeIs('master.stock-kontainer.*') || Request::routeIs('master.pricelist-cat.*') || Request::routeIs('master.mobil.*') || Request::routeIs('uang-jalan-batam.*') || Request::routeIs('master-kapal.*');
    $hasAktivaPermissions = $user && ($user->can('master-kontainer-view') || $user->can('master-pricelist-sewa-kontainer-view') || $user->can('master-stock-kontainer-view') || $user->can('master-pricelist-cat-view') || $user->can('master-mobil-view') || $user->can('uang-jalan-batam.view') || $user->can('master-kapal.view'));
@endphp

@if($hasAktivaPermissions)
<div class="mt-4 mb-4">
    <button id="aktiva-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-xl text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200 group shadow-sm text-xs {{ $isAktivaRoute ? 'bg-green-100 text-green-700 font-bold' : '' }}">
        <div class="flex items-center justify-center w-8 h-8 rounded-xl mr-3 {{ $isAktivaRoute ? 'bg-green-200' : 'bg-green-50 group-hover:bg-green-200' }}">
            <svg class="w-4 h-4 {{ $isAktivaRoute ? 'text-green-700' : 'text-green-600 group-hover:text-green-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <span class="text-xs font-medium menu-text flex-1 text-left">Aktiva</span>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isAktivaRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="aktiva-menu-content" class="dropdown-content ml-8 space-y-2 mt-2" @if($isAktivaRoute) style="display: block;" @endif>
        {{-- Kontainer Sub-Dropdown --}}
        @php
            $isKontainerRoute = Request::routeIs('master.kontainer.*') || Request::routeIs('master.master.pricelist-sewa-kontainer.*') || Request::routeIs('master.stock-kontainer.*') || Request::routeIs('master.pricelist-cat.*') || Request::routeIs('master.vendor-bengkel.*') || Request::routeIs('vendor-kontainer-sewa.*') || Request::routeIs('uang-jalan-batam.*');
            $hasKontainerPermissions = $user && ($user->can('master-kontainer-view') || $user->can('master-pricelist-sewa-kontainer-view') || $user->can('master-stock-kontainer-view') || $user->can('master-pricelist-cat-view') || $user->can('master-vendor-bengkel.view') || $user->can('vendor-kontainer-sewa-view') || $user->can('uang-jalan-batam.view'));
        @endphp

        @if($hasKontainerPermissions)
        <div class="mb-3">
            <button id="kontainer-menu-toggle" class="w-full flex justify-between items-center py-1.5 px-4 rounded-lg text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200 group text-xs {{ $isKontainerRoute ? 'bg-green-50 text-green-700 font-medium' : '' }}">
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
                @if($user && $user->can('master-pricelist-sewa-kontainer-view'))
                    <a href="{{ route('master.master.pricelist-sewa-kontainer.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.master.pricelist-sewa-kontainer.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Master Pricelist Kontainer Sewa</span>
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
                @if($user && $user->can('master-pricelist-cat-view'))
                    <a href="{{ route('master.pricelist-cat.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.pricelist-cat.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pricelist CAT</span>
                    </a>
                @endif
                @if($user && $user->can('master-pricelist-gate-in-view'))
                    <a href="{{ route('master.pricelist-gate-in.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.pricelist-gate-in.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pricelist Gate In</span>
                    </a>
                @endif
                @if($user && $user->can('uang-jalan-batam.view'))
                    <a href="{{ route('uang-jalan-batam.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('uang-jalan-batam.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Uang Jalan Batam</span>
                    </a>
                @endif
                @if($user && $user->can('master-vendor-bengkel.view'))
                    <a href="{{ route('master.vendor-bengkel.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.vendor-bengkel.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Vendor/Bengkel</span>
                    </a>
                @endif
                @if($user && $user->can('vendor-kontainer-sewa-view'))
                    <a href="{{ route('vendor-kontainer-sewa.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('vendor-kontainer-sewa.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Vendor Kontainer Sewa</span>
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Master Kapal --}}
        @if($user && $user->can('master-kapal.view'))
            <a href="{{ route('master-kapal.index') }}" class="flex items-center py-1.5 px-4 mx-1 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master-kapal.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                <span class="text-xs">Master Kapal</span>
            </a>
        @endif

        {{-- Master Mobil --}}
        @if($user && $user->can('master-mobil-view'))
            <a href="{{ route('master.mobil.index') }}" class="flex items-center py-1.5 px-4 mx-1 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.mobil.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                <span class="text-xs">Master Mobil</span>
            </a>
        @endif
    </div>
</div>
@endif

{{-- Aktivitas Dropdown --}}
@php
    $isAktivitasRoute = Request::routeIs('permohonan.*') || Request::routeIs('pranota-supir.*') || Request::routeIs('pranota-uang-jalan.*') || Request::routeIs('pranota-uang-rit.*') || Request::routeIs('surat-jalan.*') || Request::routeIs('surat-jalan-bongkaran.*') || Request::routeIs('aktivitas-kontainer.*') || Request::routeIs('daftar-tagihan-kontainer-sewa.*') || Request::routeIs('pranota.*') || Request::routeIs('perbaikan-kontainer.*') || Request::routeIs('pranota-perbaikan-kontainer.*') || Request::routeIs('tagihan-cat.*') || Request::routeIs('pranota-cat.*') || Request::routeIs('tanda-terima.*') || Request::routeIs('tanda-terima-tanpa-surat-jalan.*') || Request::routeIs('gate-in.*') || Request::routeIs('aktivitas-kapal.*') || Request::routeIs('pergerakan-kapal.*') || Request::routeIs('voyage.*') || Request::routeIs('jadwal-kapal.*') || Request::routeIs('status-kapal.*') || Request::routeIs('log-aktivitas-kapal.*') || Request::routeIs('monitoring-kapal.*') || Request::routeIs('naik-kapal.*') || Request::routeIs('bl.*') || Request::routeIs('approval.*') || Request::routeIs('approval-ii.*');
    $hasAktivitasPermissions = $user && (
        $user->can('permohonan-memo-view') ||
        $user->can('pranota-supir-view') ||
        $user->can('pranota-uang-jalan-view') ||
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
        $user->can('pranota.view') ||
        $user->can('perbaikan-kontainer-view') ||
        $user->can('pranota-perbaikan-kontainer-view') ||
        $user->can('tagihan-cat-view') ||
        $user->can('pranota-cat-view') ||
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
        $user->can('approval-view') ||
        $user->can('approval-approve') ||
        $user->can('approval-print') ||
        $user->can('approval-dashboard') ||
        $user->can('approval') ||
        $user->can('permohonan.approve')
    );
    $showAktivitasSection = $isAdmin || $hasAktivitasPermissions;
@endphp

@if($showAktivitasSection)
<div class="mt-4 mb-4">
    <button id="aktivitas-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg mt-4 mb-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isAktivitasRoute ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isAktivitasRoute ? 'bg-indigo-100' : '' }}">
                <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isAktivitasRoute ? 'text-indigo-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="text-xs font-medium truncate w-full">Aktivitas</span>
        </div>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isAktivitasRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="aktivitas-menu-content" class="dropdown-content ml-8 space-y-4 mt-4 mb-4" @if($isAktivitasRoute) style="display: block;" @endif>

        {{-- Aktivitas Supir Sub-Dropdown --}}
        @php
            $isAktivitasSupirRoute = Request::routeIs('permohonan.*') || Request::routeIs('pranota-supir.*') || Request::routeIs('pranota-uang-jalan.*') || Request::routeIs('pranota-uang-rit.*') || Request::routeIs('surat-jalan.*') || Request::routeIs('surat-jalan-bongkaran.*');
            $hasAktivitasSupirPermissions = $user && ($user->can('permohonan-memo-view') || $user->can('pranota-supir-view') || $user->can('pranota-uang-jalan-view') || $user->can('pranota-uang-rit-view') || $user->can('surat-jalan-view') || $user->can('surat-jalan-create') || $user->can('surat-jalan-update') || $user->can('surat-jalan-delete') || $user->can('surat-jalan-bongkaran-view') || $user->can('surat-jalan-bongkaran-create') || $user->can('surat-jalan-bongkaran-update') || $user->can('surat-jalan-bongkaran-delete'));
        @endphp

        @if($hasAktivitasSupirPermissions)
        <div class="mt-2 mb-2">
            <button id="aktivitas-supir-menu-toggle" class="w-full flex justify-between items-center py-1 px-3 rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isAktivitasSupirRoute ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-4 h-4 rounded bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isAktivitasSupirRoute ? 'bg-blue-100' : '' }}">
                        <svg class="w-3 h-3 text-gray-600 group-hover:text-gray-700 {{ $isAktivitasSupirRoute ? 'text-blue-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12a2 2 0 100-4 2 2 0 000 4zm0 0v10m0-10a2 2 0 002-2V2"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium truncate w-full">Aktivitas Supir</span>
                </div>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAktivitasSupirRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="aktivitas-supir-menu-content" class="dropdown-content ml-6 space-y-2 mt-2" @if($isAktivitasSupirRoute) style="display: block;" @endif>
                {{-- Permohonan Memo --}}
                @if($user && $user->can('permohonan-memo-view'))
                    <a href="{{ route('permohonan.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('permohonan.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Permohonan Memo
                    </a>
                @endif

                {{-- Pranota Supir --}}
                @if($user && $user->can('pranota-supir-view'))
                    <a href="{{ route('pranota-supir.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pranota-supir.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Pranota Supir
                    </a>
                @endif

                {{-- Pranota Uang Jalan --}}
                @if($user && $user->can('pranota-uang-jalan-view'))
                    <a href="{{ route('pranota-uang-jalan.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pranota-uang-jalan.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                        Pranota Uang Jalan
                    </a>
                @endif

                {{-- Pranota Uang Rit --}}
                @if($user && $user->can('pranota-uang-rit-view'))
                    <a href="{{ route('pranota-uang-rit.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pranota-uang-rit.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pranota Uang Rit
                    </a>
                @endif

                {{-- Pranota Uang Kenek --}}
                @if($user && $user->can('pranota-uang-kenek-view'))
                    <a href="{{ route('pranota-uang-kenek.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pranota-uang-kenek.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Pranota Uang Kenek
                    </a>
                @endif

                {{-- Surat Jalan --}}
                @if($user && ($user->can('surat-jalan-view') || $user->can('surat-jalan-create') || $user->can('surat-jalan-update') || $user->can('surat-jalan-delete')))
                    <a href="{{ route('surat-jalan.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('surat-jalan.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Surat Jalan
                    </a>
                @endif

                {{-- Surat Jalan Bongkaran --}}
                @if($user && ($user->can('surat-jalan-bongkaran-view') || $user->can('surat-jalan-bongkaran-create') || $user->can('surat-jalan-bongkaran-update') || $user->can('surat-jalan-bongkaran-delete')))
                    <a href="{{ route('surat-jalan-bongkaran.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('surat-jalan-bongkaran.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        Surat Jalan Bongkaran
                    </a>
                @endif

                {{-- Uang Jalan --}}
                @if($user && ($user->can('uang-jalan-view') || $user->can('uang-jalan-create') || $user->can('uang-jalan-update') || $user->can('uang-jalan-delete')))
                    <a href="{{ route('uang-jalan.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('uang-jalan.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Uang Jalan
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Aktivitas Kontainer Sub-Dropdown --}}
        @php
            $isAktivitasKontainerRoute = Request::routeIs('daftar-tagihan-kontainer-sewa.*') || Request::routeIs('pranota.*') || Request::routeIs('perbaikan-kontainer.*') || Request::routeIs('pranota-perbaikan-kontainer.*') || Request::routeIs('tagihan-cat.*') || Request::routeIs('pranota-cat.*') || Request::routeIs('tanda-terima.*') || Request::routeIs('tanda-terima-tanpa-surat-jalan.*') || Request::routeIs('gate-in.*');
            $hasAktivitasKontainerPermissions = $user && ($user->can('tagihan-kontainer-sewa-index') || $user->can('pranota.view') || $user->can('perbaikan-kontainer-view') || $user->can('pranota-perbaikan-kontainer-view') || $user->can('tagihan-cat-view') || $user->can('pranota-cat-view') || $user->can('tanda-terima-view') || $user->can('tanda-terima-update') || $user->can('tanda-terima-delete') || $user->can('tanda-terima-tanpa-surat-jalan-view') || $user->can('tanda-terima-tanpa-surat-jalan-create') || $user->can('tanda-terima-tanpa-surat-jalan-update') || $user->can('tanda-terima-tanpa-surat-jalan-delete') || $user->can('gate-in-view') || $user->can('gate-in-create') || $user->can('gate-in-update') || $user->can('gate-in-delete'));
        @endphp

        @if($hasAktivitasKontainerPermissions)
        <div class="mt-2 mb-2">
            <button id="aktivitas-aktivitas-kontainer-menu-toggle" class="w-full flex justify-between items-center py-1 px-3 rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isAktivitasKontainerRoute ? 'bg-green-50 text-green-700 font-medium' : '' }}">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-4 h-4 rounded bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isAktivitasKontainerRoute ? 'bg-green-100' : '' }}">
                        <svg class="w-3 h-3 text-gray-600 group-hover:text-gray-700 {{ $isAktivitasKontainerRoute ? 'text-green-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium truncate w-full">Aktivitas Kontainer</span>
                </div>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAktivitasKontainerRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="aktivitas-aktivitas-kontainer-menu-content" class="dropdown-content ml-6 space-y-2 mt-2" @if($isAktivitasKontainerRoute) style="display: block;" @endif>
                {{-- Tagihan Kontainer Sewa --}}
                @if($user && $user->can('tagihan-kontainer-sewa-index'))
                    <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('daftar-tagihan-kontainer-sewa.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Tagihan Kontainer Sewa
                    </a>
                @endif

                {{-- Daftar Pranota Kontainer Sewa --}}
                @if($user && $user->can('pranota-kontainer-sewa-view'))
                    <a href="{{ route('pranota-kontainer-sewa.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pranota-kontainer-sewa.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Daftar Pranota Kontainer Sewa
                    </a>
                @endif

                {{-- Tagihan Perbaikan Kontainer --}}
                @if($user && $user->can('tagihan-perbaikan-kontainer-view'))
                    <a href="{{ route('perbaikan-kontainer.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('perbaikan-kontainer.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Tagihan Perbaikan Kontainer
                    </a>
                @endif

                {{-- Pranota Perbaikan Kontainer --}}
                @if($user && $user->can('pranota-perbaikan-kontainer-view'))
                    <a href="{{ route('pranota-perbaikan-kontainer.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pranota-perbaikan-kontainer.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Pranota Perbaikan Kontainer
                    </a>
                @endif

                {{-- Tagihan CAT --}}
                @if($user && $user->can('tagihan-cat-view'))
                    <a href="{{ route('tagihan-cat.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('tagihan-cat.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Tagihan CAT
                    </a>
                @endif

                {{-- Daftar Pranota Tagihan CAT --}}
                @if($user && $user->can('pranota-cat-view'))
                    <a href="{{ route('pranota-cat.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pranota-cat.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Daftar Pranota Tagihan CAT
                    </a>
                @endif

                {{-- Tanda Terima --}}
                @if($user && ($user->can('tanda-terima-view') || $user->can('tanda-terima-update') || $user->can('tanda-terima-delete')))
                    <a href="{{ route('tanda-terima.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('tanda-terima.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Tanda Terima
                    </a>
                @endif

                {{-- Tanda Terima Tanpa Surat Jalan --}}
                @if($user && ($user->can('tanda-terima-tanpa-surat-jalan-view') || $user->can('tanda-terima-tanpa-surat-jalan-create') || $user->can('tanda-terima-tanpa-surat-jalan-update') || $user->can('tanda-terima-tanpa-surat-jalan-delete')))
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('tanda-terima-tanpa-surat-jalan.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Tanda Terima Tanpa Surat Jalan
                    </a>
                @endif

                {{-- Gate In --}}
                @if($user && ($user->can('gate-in-view') || $user->can('gate-in-create') || $user->can('gate-in-update') || $user->can('gate-in-delete')))
                    <a href="{{ route('gate-in.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('gate-in.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Gate In
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Aktivitas Kapal Sub-Dropdown --}}
        @php
            $isAktivitasKapalRoute = Request::routeIs('aktivitas-kapal.*') || Request::routeIs('pergerakan-kapal.*') || Request::routeIs('voyage.*') || Request::routeIs('jadwal-kapal.*') || Request::routeIs('status-kapal.*') || Request::routeIs('log-aktivitas-kapal.*') || Request::routeIs('monitoring-kapal.*') || Request::routeIs('naik-kapal.*') || Request::routeIs('bl.*');
            $hasAktivitasKapalPermissions = $user && ($user->can('aktivitas-kapal-view') || $user->can('pergerakan-kapal-view') || $user->can('voyage-view') || $user->can('jadwal-kapal-view') || $user->can('status-kapal-view') || $user->can('log-aktivitas-kapal-view') || $user->can('monitoring-kapal-view') || $user->can('prospek-edit') || $user->can('bl-view'));
        @endphp

        @if($hasAktivitasKapalPermissions)
        <div class="mt-2 mb-2">
            <button id="aktivitas-kapal-menu-toggle" class="w-full flex justify-between items-center py-1 px-3 rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isAktivitasKapalRoute ? 'bg-purple-50 text-purple-700 font-medium' : '' }}">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-4 h-4 rounded bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isAktivitasKapalRoute ? 'bg-purple-100' : '' }}">
                        <svg class="w-3 h-3 text-gray-600 group-hover:text-gray-700 {{ $isAktivitasKapalRoute ? 'text-purple-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium truncate w-full">Aktivitas Kapal</span>
                </div>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAktivitasKapalRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="aktivitas-kapal-menu-content" class="dropdown-content ml-6 space-y-2 mt-2" @if($isAktivitasKapalRoute) style="display: block;" @endif>
                {{-- Naik Kapal --}}
                @if($user && $user->can('prospek-edit'))
                    <a href="{{ route('naik-kapal.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('naik-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16v8a2 2 0 01-2 2H6a2 2 0 01-2-2V8zm0 0V6a2 2 0 012-2h12a2 2 0 012 2v2M8 12l2 2 4-4"/>
                        </svg>
                        Naik Kapal
                    </a>
                @endif

                {{-- BL (Bill of Lading) --}}
                @if($user && $user->can('bl-view'))
                    <a href="{{ route('bl.select') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('bl.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        BL (Bill of Lading)
                    </a>
                @endif

                {{-- Pergerakan Kapal --}}
                @if($user && $user->can('pergerakan-kapal-view'))
                    <a href="{{ route('pergerakan-kapal.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pergerakan-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Pergerakan Kapal
                    </a>
                @endif

                {{-- Daftar Voyage --}}
                @if($user && $user->can('voyage-view'))
                    <a href="{{ route('voyage.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('voyage.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Daftar Voyage
                    </a>
                @endif

                {{-- Jadwal Kapal --}}
                @if($user && $user->can('jadwal-kapal-view'))
                    <a href="{{ route('jadwal-kapal.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('jadwal-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Jadwal Kapal
                    </a>
                @endif

                {{-- Status Kapal --}}
                @if($user && $user->can('status-kapal-view'))
                    <a href="{{ route('status-kapal.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('status-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Status Kapal
                    </a>
                @endif

                {{-- Log Aktivitas --}}
                @if($user && $user->can('log-aktivitas-kapal-view'))
                    <a href="{{ route('log-aktivitas-kapal.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('log-aktivitas-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Log Aktivitas
                    </a>
                @endif

                {{-- Monitoring Kapal --}}
                @if($user && $user->can('monitoring-kapal-view'))
                    <a href="{{ route('monitoring-kapal.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('monitoring-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Monitoring Kapal
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Approval Tugas Sub-Dropdown --}}
        @php
            $isApprovalTugasRoute = Request::routeIs('approval.*') || Request::routeIs('approval-ii.*');
            $hasApprovalTugasPermissions = $user && ($user->can('approval-view') || $user->can('approval-approve') || $user->can('approval-print') || $user->can('approval-dashboard') || $user->can('approval') || $user->can('permohonan.approve'));
        @endphp

        @if($hasApprovalTugasPermissions)
        <div class="mt-2 mb-2">
            <button id="approval-tugas-menu-toggle" class="w-full flex justify-between items-center py-1 px-3 rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isApprovalTugasRoute ? 'bg-orange-50 text-orange-700 font-medium' : '' }}">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-4 h-4 rounded bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isApprovalTugasRoute ? 'bg-orange-100' : '' }}">
                        <svg class="w-3 h-3 text-gray-600 group-hover:text-gray-700 {{ $isApprovalTugasRoute ? 'text-orange-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium truncate w-full">Approval Tugas</span>
                </div>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isApprovalTugasRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="approval-tugas-menu-content" class="dropdown-content ml-6 space-y-2 mt-2" @if($isApprovalTugasRoute) style="display: block;" @endif>
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

                {{-- Approval Surat Jalan --}}
                @if($user && $user->can('approval-surat-jalan-view'))
                    <a href="{{ Route::has('approval.surat-jalan.index') ? route('approval.surat-jalan.index') : '#' }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('approval.surat-jalan.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Approval Surat Jalan
                        @if(!Route::has('approval.surat-jalan.index'))
                            <span class="ml-auto text-xs text-gray-400 italic">(in dev)</span>
                        @endif
                    </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endif



{{-- Separator --}}
<div class="my-6 mx-4 border-t border-gray-200"></div>

{{-- Pembayaran Dropdown --}}
@php
    $isPaymentRoute = Request::routeIs('pembayaran-pranota-kontainer.*') || Request::routeIs('pembayaran-pranota-perbaikan-kontainer.*') || Request::routeIs('pembayaran-pranota-cat.*') || Request::routeIs('pembayaran-pranota-supir.*') || Request::routeIs('pembayaran-pranota-surat-jalan.*') || Request::routeIs('pembayaran-pranota-uang-jalan.*') || Request::routeIs('pembayaran-aktivitas-lainnya.*') || Request::routeIs('pembayaran-uang-muka.*') || Request::routeIs('pembayaran-ob.*') || Request::routeIs('realisasi-uang-muka.*');
    $hasPaymentPermissions = $user && (
        $user->can('pembayaran-pranota-kontainer-view') ||
        $user->can('pembayaran-pranota-perbaikan-kontainer-view') ||
        $user->can('pembayaran-pranota-cat-view') ||
        $user->can('pembayaran-pranota-supir-view') ||
        $user->can('pembayaran-pranota-surat-jalan-view') ||
        $user->can('pembayaran-pranota-uang-jalan-view') ||
        $user->can('pembayaran-aktivitas-lainnya-view') ||
        $user->can('pembayaran-uang-muka-view') ||
        $user->can('pembayaran-ob-view') ||
        $user->can('realisasi-uang-muka-view')
    );
@endphp

@if($hasPaymentPermissions)
<div class="mt-4 mb-4">
    <button id="pembayaran-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg mt-4 mb-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isPaymentRoute ? 'bg-red-50 text-red-700 font-medium' : '' }}">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isPaymentRoute ? 'bg-red-100' : '' }}">
                <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isPaymentRoute ? 'text-red-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium truncate w-full">Pembayaran</span>
        </div>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isPaymentRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="pembayaran-menu-content" class="dropdown-content ml-8 space-y-4 mt-4 mb-4" @if($isPaymentRoute) style="display: block;" @endif>
        {{-- Aktivitas Supir Sub-dropdown --}}
        @php
            $isAktivitasSupirPaymentRoute = Request::routeIs('pembayaran-pranota-supir.*') || Request::routeIs('pembayaran-pranota-surat-jalan.*') || Request::routeIs('pembayaran-pranota-uang-jalan.*');
            $hasAktivitasSupirPaymentPermission = $user && ($user->can('pembayaran-pranota-supir-view') || $user->can('pembayaran-pranota-surat-jalan-view') || $user->can('pembayaran-pranota-uang-jalan-view'));
        @endphp
        @if($hasAktivitasSupirPaymentPermission)
        <div class="mt-2 mb-2">
            <button id="aktivitas-supir-payment-menu-toggle" class="w-full flex justify-between items-center py-1 px-3 rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isAktivitasSupirPaymentRoute ? 'bg-red-50 text-red-700 font-medium' : '' }}">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-4 h-4 rounded bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isAktivitasSupirPaymentRoute ? 'bg-red-100' : '' }}">
                        <svg class="w-3 h-3 text-gray-600 group-hover:text-gray-700 {{ $isAktivitasSupirPaymentRoute ? 'text-red-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium truncate w-full">Aktivitas Supir</span>
                </div>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAktivitasSupirPaymentRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="aktivitas-supir-payment-menu-content" class="dropdown-content ml-6 space-y-2 mt-2" @if($isAktivitasSupirPaymentRoute) style="display: block;" @endif>
                {{-- Pembayaran Pranota Supir --}}
                @if(Route::has('pembayaran-pranota-supir.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-supir-view')))
                    <a href="{{ route('pembayaran-pranota-supir.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-supir.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Bayar Pranota Supir
                    </a>
                @endif

                {{-- Pembayaran Pranota Surat Jalan --}}
                @if(Route::has('pembayaran-pranota-surat-jalan.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-surat-jalan-view')))
                    <a href="{{ route('pembayaran-pranota-surat-jalan.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-surat-jalan.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Bayar Pranota Surat Jalan
                    </a>
                @endif

                {{-- Pembayaran Pranota Uang Jalan --}}
                @if(Route::has('pembayaran-pranota-uang-jalan.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-uang-jalan-view')))
                    <a href="{{ route('pembayaran-pranota-uang-jalan.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-uang-jalan.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                        Bayar Pranota Uang Jalan
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Aktivitas Kontainer Sub-dropdown --}}
        @php
            $isAktivitasKontainerPaymentRoute = Request::routeIs('pembayaran-pranota-kontainer.*') || Request::routeIs('pembayaran-pranota-perbaikan-kontainer.*') || Request::routeIs('pembayaran-pranota-cat.*');
            $hasAktivitasKontainerPaymentPermission = $user && (
                $user->can('pembayaran-pranota-kontainer-view') ||
                $user->can('pembayaran-pranota-perbaikan-kontainer-view') ||
                $user->can('pembayaran-pranota-cat-view')
            );
        @endphp
        @if($hasAktivitasKontainerPaymentPermission)
        <div class="mt-2 mb-2">
            <button id="aktivitas-kontainer-payment-menu-toggle" class="w-full flex justify-between items-center py-1 px-3 rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isAktivitasKontainerPaymentRoute ? 'bg-red-50 text-red-700 font-medium' : '' }}">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-4 h-4 rounded bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isAktivitasKontainerPaymentRoute ? 'bg-red-100' : '' }}">
                        <svg class="w-3 h-3 text-gray-600 group-hover:text-gray-700 {{ $isAktivitasKontainerPaymentRoute ? 'text-red-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium truncate w-full">Aktivitas Kontainer</span>
                </div>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAktivitasKontainerPaymentRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="aktivitas-kontainer-payment-menu-content" class="dropdown-content ml-6 space-y-2 mt-2" @if($isAktivitasKontainerPaymentRoute) style="display: block;" @endif>
                {{-- Pembayaran Pranota Kontainer Sewa --}}
                @if(Route::has('pembayaran-pranota-kontainer.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-kontainer-view')))
                    <a href="{{ route('pembayaran-pranota-kontainer.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-kontainer.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Bayar Pranota Kontainer Sewa
                    </a>
                @endif

                {{-- Pembayaran Pranota Perbaikan Kontainer --}}
                @if(Route::has('pembayaran-pranota-perbaikan-kontainer.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-perbaikan-kontainer-view')))
                    <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-perbaikan-kontainer.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Bayar Pranota Perbaikan Kontainer
                    </a>
                @endif

                {{-- Pembayaran Pranota CAT Kontainer --}}
                @if(Route::has('pembayaran-pranota-cat.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-cat-view')))
                    <a href="{{ route('pembayaran-pranota-cat.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-cat.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Bayar Pranota CAT Kontainer
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Aktivitas Lain-lain Sub-dropdown --}}
        @php
            $isAktivitasLainnyaPaymentRoute = Request::routeIs('pembayaran-aktivitas-lainnya.*') || Request::routeIs('pembayaran-uang-muka.*') || Request::routeIs('pembayaran-ob.*') || Request::routeIs('realisasi-uang-muka.*');
            $hasAktivitasLainnyaPaymentPermission = $user && ($user->can('pembayaran-aktivitas-lainnya-view') || $user->can('pembayaran-uang-muka-view') || $user->can('pembayaran-ob-view') || $user->can('realisasi-uang-muka-view'));
        @endphp
        @if($hasAktivitasLainnyaPaymentPermission)
        <div class="mt-2 mb-2">
            <button id="aktivitas-lainnya-payment-menu-toggle" class="w-full flex justify-between items-center py-1 px-3 rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isAktivitasLainnyaPaymentRoute ? 'bg-red-50 text-red-700 font-medium' : '' }}">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-4 h-4 rounded bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isAktivitasLainnyaPaymentRoute ? 'bg-red-100' : '' }}">
                        <svg class="w-3 h-3 text-gray-600 group-hover:text-gray-700 {{ $isAktivitasLainnyaPaymentRoute ? 'text-red-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium truncate w-full">Aktivitas Lain-lain</span>
                </div>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAktivitasLainnyaPaymentRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="aktivitas-lainnya-payment-menu-content" class="dropdown-content ml-6 space-y-2 mt-2" @if($isAktivitasLainnyaPaymentRoute) style="display: block;" @endif>
                {{-- Pembayaran Aktivitas Lain-lain --}}
                @if(Route::has('pembayaran-aktivitas-lainnya.index') && ($isAdmin || auth()->user()->can('pembayaran-aktivitas-lainnya-view')))
                    <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pembayaran-aktivitas-lainnya.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Bayar Aktivitas Lain-lain
                    </a>
                @endif

                {{-- Pembayaran Uang Muka --}}
                @if(Route::has('pembayaran-uang-muka.index') && ($isAdmin || auth()->user()->can('pembayaran-uang-muka-view')))
                    <a href="{{ route('pembayaran-uang-muka.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pembayaran-uang-muka.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                        Pembayaran Uang Muka
                    </a>
                @endif

                {{-- Pembayaran OB --}}
                @if(Route::has('pembayaran-ob.index') && ($isAdmin || auth()->user()->can('pembayaran-ob-view')))
                    <a href="{{ route('pembayaran-ob.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pembayaran-ob.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Pembayaran OB
                    </a>
                @endif

                {{-- Realisasi Uang Muka --}}
                @if(Route::has('realisasi-uang-muka.index') && ($isAdmin || auth()->user()->can('realisasi-uang-muka-view')))
                    <a href="{{ route('realisasi-uang-muka.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('realisasi-uang-muka.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Realisasi Uang Muka
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
        setupDropdown('order-menu-toggle', 'order-menu-content');
        setupDropdown('input-menu-toggle', 'input-menu-content');
        setupDropdown('aktivitas-menu-toggle', 'aktivitas-menu-content');
        setupDropdown('aktivitas-supir-menu-toggle', 'aktivitas-supir-menu-content');
        setupDropdown('aktivitas-kontainer-menu-toggle', 'aktivitas-kontainer-menu-content');
        setupDropdown('aktivitas-aktivitas-kontainer-menu-toggle', 'aktivitas-aktivitas-kontainer-menu-content');
        setupDropdown('aktivitas-kapal-menu-toggle', 'aktivitas-kapal-menu-content');
        setupDropdown('approval-tugas-menu-toggle', 'approval-tugas-menu-content');
        setupDropdown('approval-input-menu-toggle', 'approval-input-menu-content');
        setupDropdown('uang-jalan-supir-menu-toggle', 'uang-jalan-supir-menu-content');
        setupDropdown('permohonan-memo-menu-toggle', 'permohonan-memo-menu-content');
        setupDropdown('pranota-supir-menu-toggle', 'pranota-supir-menu-content');
        setupDropdown('aktiva-menu-toggle', 'aktiva-menu-content');
        setupDropdown('kontainer-menu-toggle', 'kontainer-menu-content');
        setupDropdown('tagihan-kontainer-menu-toggle', 'tagihan-kontainer-menu-content');
        setupDropdown('pembayaran-menu-toggle', 'pembayaran-menu-content');
        setupDropdown('aktivitas-supir-payment-menu-toggle', 'aktivitas-supir-payment-menu-content');
        setupDropdown('aktivitas-kontainer-payment-menu-toggle', 'aktivitas-kontainer-payment-menu-content');
        setupDropdown('aktivitas-lainnya-payment-menu-toggle', 'aktivitas-lainnya-payment-menu-content');
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
