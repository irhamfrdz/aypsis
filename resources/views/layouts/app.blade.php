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
    <!-- Sidebar -->
    <div id="sidebar" class="hidden lg:flex lg:flex-col lg:w-64 bg-gradient-to-b from-blue-50 via-white to-gray-100 shadow-2xl border-r border-gray-300 fixed top-16 bottom-0 left-0 z-50 translate-x-0 transition-transform rounded-r-2xl">
            <!-- Mobile close button -->
            <div class="lg:hidden absolute top-4 right-4">
                <button id="close-sidebar" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Header -->
            <div class="p-6 border-b border-gray-300 flex-shrink-0 bg-white rounded-tr-2xl shadow-md">
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
            <nav class="flex-1 overflow-y-auto p-4 space-y-2 sidebar-scroll">
                @php
                    $user = Auth::user();
                    $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
                    $isDashboard = Request::routeIs('dashboard');

                    // Check if user has any master data permissions
                    $hasMasterPermissions = $user && (
                        $user->can('master-permission-view') ||
                        $user->can('master-cabang-view') ||
                        $user->can('master-coa-view') ||
                        $user->can('master-kode-nomor-view') ||
                        $user->can('master-tipe-akun-view')
                    );

                    // Show master section if user is admin OR has any master permissions
                    $showMasterSection = $isAdmin || $hasMasterPermissions;
                @endphp

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center py-2 px-5 rounded-xl mt-4 mb-4 transition-all duration-200 group shadow-sm text-xs {{ $isDashboard ? 'bg-blue-100 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-700' }}">
                    <div class="flex items-center justify-center w-8 h-8 rounded-xl mr-3 {{ $isDashboard ? 'bg-blue-200' : 'bg-blue-50 group-hover:bg-blue-200' }}">
                        <svg class="w-4 h-4 {{ $isDashboard ? 'text-blue-700' : 'text-blue-600 group-hover:text-blue-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium menu-text">Dashboard</span>
                </a>

                <!-- Master Data Section -->
                @php
                    $isMasterRoute = Request::routeIs('master.permission.*') || Request::routeIs('master-coa-*') || Request::routeIs('master.kode-nomor.*') || Request::routeIs('master.tipe-akun.*') || Request::routeIs('master.cabang.*');
                    $isPermohonanRoute = Request::routeIs('permohonan.*');
                    $isPenyelesaianRoute = Request::routeIs('approval.*');
                    $isPranotaRoute = Request::routeIs('pranota-supir.*') || Request::routeIs('pembayaran-pranota-supir.*');
                @endphp

                @if($showMasterSection)
                <div class="mt-4 mb-4">
                        <button id="master-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg mt-4 mb-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isMasterRoute ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isMasterRoute ? 'bg-blue-100' : '' }}">
                                    <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isMasterRoute ? 'text-blue-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-medium truncate w-full">Master Data</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isMasterRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                         <div id="master-menu-content" class="dropdown-content ml-8 space-y-4 mt-4 mb-4" @if($isMasterRoute) style="display: block;" @endif>
                            @if($user && $user->can('master-permission-view'))
                                <a href="{{ route('master.permission.index') }}" class="flex items-center py-1 px-4 rounded-md text-xs hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 {{ Request::routeIs('master.permission.*') ? 'bg-indigo-50 font-medium text-indigo-600' : 'text-gray-600' }}">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                     Izin (buat Programmer)
                                </a>
                            @endif
                            @if($user && $user->can('master-cabang-view'))
                                <a href="{{ route('master.cabang.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.cabang.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                     Cabang
                                </a>
                            @endif
                            @if($user && $user->can('master-coa-view'))
                                <a href="{{ route('master-coa-index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master-coa-*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    COA
                                </a>
                            @endif
                               @if($user && $user->can('master-kode-nomor-view'))
                                <a href="{{ route('master.kode-nomor.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.kode-nomor.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Kode Nomor
                                </a>
                            @endif
                            @if($user && $user->can('master-tipe-akun-view'))
                                <a href="{{ route('master.tipe-akun.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.tipe-akun.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    Tipe Akun
                                </a>
                            @endif
                        </div>
                    </div>
                    @else
                    @if($user && ($user->can('master-tujuan-view') || $user->can('master-kegiatan-view') || $user->can('master-permission-view') || $user->can('master-mobil-view') || $user->can('master-bank-view') || $user->can('master-coa-view') || $user->can('master-kode-nomor-view') || $user->can('master-tipe-akun-view')))
                    <div class="mt-4 mb-4">
                        <button id="master-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition-all duration-200 group {{ $isMasterRoute ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-3 {{ $isMasterRoute ? 'bg-blue-100' : '' }}">
                                        <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isMasterRoute ? 'text-blue-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Master Data</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isMasterRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="master-menu-content" class="dropdown-content ml-12 space-y-1 mt-2" @if($isMasterRoute) style="display: block;" @endif>
                            @if($user && $user->can('master-permission-view'))
                                <a href="{{ route('master.permission.index') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.permission.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Izin (Buat Programmer)
                                </a>
                            @endif
                            @if($user && $user->can('master-cabang-view'))
                                <a href="{{ route('master.cabang.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.cabang.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Cabang
                                </a>
                            @endif
                            @if($user && $user->can('master-coa-view'))
                                <a href="{{ route('master-coa-index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master-coa-*') ? 'bg-blue-50 font-medium text-blue-700' : 'text-gray-600' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    COA
                                </a>
                            @endif
                            @if($user && $user->can('master-kode-nomor-view'))
                                <a href="{{ route('master.kode-nomor.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.kode-nomor.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Kode Nomor
                                </a>
                            @endif
                            @if($user && $user->can('master-tipe-akun-view'))
                                <a href="{{ route('master.tipe-akun.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.tipe-akun.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    Tipe Akun
                                </a>
                            @endif
                            {{-- Hapus menu dari master data --}}
                        </div>
                    </div>
                    @endif
                    @endif

{{-- User Dropdown --}}
@php
    $isUserRoute = Request::routeIs('master.user.*') || Request::routeIs('master.karyawan.*') || Request::routeIs('master.divisi.*') || Request::routeIs('master.pekerjaan.*') || Request::routeIs('master.pajak.*') || Request::routeIs('admin.user-approval.*') || Request::routeIs('master.mobil.*') || Request::routeIs('master-bank-*');
    $hasUserPermissions = $user && ($user->can('master-user-view') || $user->can('master-karyawan-view') || $user->can('master-divisi-view') || $user->can('master-pekerjaan-view') || $user->can('master-pajak-view') || $user->can('master-mobil-view') || $user->can('master-bank-view'));
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
<div class="mt-4 mb-4">
    <button id="user-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg mt-4 mb-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isUserRoute ? 'bg-purple-50 text-purple-700 font-medium' : '' }}">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isUserRoute ? 'bg-purple-100' : '' }}">
                <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isUserRoute ? 'text-purple-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="text-xs font-medium truncate w-full">User</span>
        </div>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isUserRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="user-menu-content" class="dropdown-content ml-8 space-y-4 mt-4 mb-4" @if($isUserRoute) style="display: block;" @endif>
        @if($user && $user->can('master-user-view'))
            <a href="{{ route('master.user.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.user.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Master User
            </a>
        @endif
        @if($user && $user->can('master-karyawan-view'))
            <a href="{{ route('master.karyawan.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.karyawan.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                Master Karyawan
            </a>
        @endif
        @if($user && $user->can('master-divisi-view'))
            <a href="{{ route('master.divisi.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.divisi.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Master Divisi
            </a>
        @endif
        @if($user && $user->can('master-pekerjaan-view'))
            <a href="{{ route('master.pekerjaan.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.pekerjaan.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0V8a2 2 0 01-2 2H8a2 2 0 01-2-2V6m8 0H8m0 0V4"/>
                </svg>
                Master Pekerjaan
            </a>
        @endif
        @if($user && $user->can('master-pajak-view'))
            <a href="{{ route('master.pajak.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.pajak.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Master Pajak
            </a>
        @endif
        @if($user && $user->can('master-mobil-view'))
            <a href="{{ route('master.mobil.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.mobil.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12a2 2 0 100-4 2 2 0 000 4zm0 0v10m0-10a2 2 0 002-2V2"/>
                </svg>
                Master Mobil
            </a>
        @endif
        @if($user && $user->can('master-bank-view'))
            <a href="{{ route('master-bank-index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master-bank-*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Master Bank
            </a>
        @endif
        @if($hasUserApprovalAccess)
            @php
                $pendingUsersCount = \App\Models\User::where('status', 'pending')->count();
            @endphp
            <a href="{{ route('admin.user-approval.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('admin.user-approval.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <i class="fas fa-user-check w-3 h-3 mr-2"></i>
                Persetujuan User
                @if($pendingUsersCount > 0)
                    <span class="ml-auto bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full">{{ $pendingUsersCount }}</span>
                @endif
            </a>
        @endif
    </div>
</div>
@endif

{{-- Aktiva Dropdown --}}
@php
    $isAktivaRoute = Request::routeIs('master.kontainer.*') || Request::routeIs('master.master.pricelist-sewa-kontainer.*') || Request::routeIs('master.stock-kontainer.*') || Request::routeIs('master.pricelist-cat.*');
    $hasAktivaPermissions = $user && ($user->can('master-kontainer-view') || $user->can('master-pricelist-sewa-kontainer-view') || $user->can('master-stock-kontainer-view') || $user->can('master-pricelist-cat-view'));
@endphp

@if($hasAktivaPermissions)
<div class="mt-4 mb-4">
    <button id="aktiva-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg mt-4 mb-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isAktivaRoute ? 'bg-green-50 text-green-700 font-medium' : '' }}">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isAktivaRoute ? 'bg-green-100' : '' }}">
                <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isAktivaRoute ? 'text-green-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <span class="text-xs font-medium truncate w-full">Aktiva</span>
        </div>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isAktivaRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="aktiva-menu-content" class="dropdown-content ml-8 space-y-4 mt-4 mb-4" @if($isAktivaRoute) style="display: block;" @endif>
        {{-- Kontainer Sub-Dropdown --}}
        @php
            $isKontainerRoute = Request::routeIs('master.kontainer.*') || Request::routeIs('master.master.pricelist-sewa-kontainer.*') || Request::routeIs('master.stock-kontainer.*') || Request::routeIs('master.pricelist-cat.*') || Request::routeIs('master.vendor-bengkel.*');
            $hasKontainerPermissions = $user && ($user->can('master-kontainer-view') || $user->can('master-pricelist-sewa-kontainer-view') || $user->can('master-stock-kontainer-view') || $user->can('master-pricelist-cat-view') || $user->can('master-vendor-bengkel-view'));
        @endphp

        @if($hasKontainerPermissions)
        <div class="mt-2 mb-2">
            <button id="kontainer-menu-toggle" class="w-full flex justify-between items-center py-1 px-3 rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isKontainerRoute ? 'bg-cyan-50 text-cyan-700 font-medium' : '' }}">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-4 h-4 rounded bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isKontainerRoute ? 'bg-cyan-100' : '' }}">
                        <svg class="w-3 h-3 text-gray-600 group-hover:text-gray-700 {{ $isKontainerRoute ? 'text-cyan-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium truncate w-full">Kontainer</span>
                </div>
                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isKontainerRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="kontainer-menu-content" class="dropdown-content ml-6 space-y-2 mt-2" @if($isKontainerRoute) style="display: block;" @endif>
                @if($user && $user->can('master-kontainer-view'))
                    <a href="{{ route('master.kontainer.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('kontainer.*') ? 'bg-cyan-50 text-cyan-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Master Kontainer
                    </a>
                @endif
                @if($user && $user->can('master-pricelist-sewa-kontainer-view'))
                    <a href="{{ route('master.master.pricelist-sewa-kontainer.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.master.pricelist-sewa-kontainer.*') ? 'bg-cyan-50 text-cyan-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Master Pricelist Kontainer Sewa
                    </a>
                @endif
                @if($user && $user->can('master-stock-kontainer-view'))
                    <a href="{{ route('master.stock-kontainer.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.stock-kontainer.*') ? 'bg-cyan-50 text-cyan-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Stock Kontainer
                    </a>
                @endif
                @if($user && $user->can('master-pricelist-cat-view'))
                    <a href="{{ route('master.pricelist-cat.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.pricelist-cat.*') ? 'bg-cyan-50 text-cyan-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Pricelist CAT
                    </a>
                @endif
                @if($user && $user->can('master-vendor-bengkel-view'))
                    <a href="{{ route('master.vendor-bengkel.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.vendor-bengkel.*') ? 'bg-cyan-50 text-cyan-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-2.5 h-2.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Vendor/Bengkel
                    </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endif

{{-- Aktivitas Supir Dropdown --}}
@php
    $isAktivitasSupirRoute = Request::routeIs('master.tujuan.*') || Request::routeIs('master.kegiatan.*');
    $hasAktivitasSupirPermissions = $user && ($user->can('master-tujuan-view') || $user->can('master-kegiatan-view'));
@endphp

@if($hasAktivitasSupirPermissions)
<div class="mt-4 mb-4">
    <button id="aktivitas-supir-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg mt-4 mb-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isAktivitasSupirRoute ? 'bg-orange-50 text-orange-700 font-medium' : '' }}">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isAktivitasSupirRoute ? 'bg-orange-100' : '' }}">
                <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isAktivitasSupirRoute ? 'text-orange-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="text-xs font-medium truncate w-full">Aktivitas Supir</span>
        </div>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isAktivitasSupirRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="aktivitas-supir-menu-content" class="dropdown-content ml-8 space-y-4 mt-4 mb-4" @if($isAktivitasSupirRoute) style="display: block;" @endif>
        @if($user && $user->can('master-tujuan-view'))
            <a href="{{ route('master.tujuan.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.tujuan.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Master Tujuan
            </a>
        @endif
        @if($user && $user->can('master-kegiatan-view'))
            <a href="{{ route('master.kegiatan.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('master.kegiatan.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Master Kegiatan
            </a>
        @endif
    </div>
</div>
@endif

{{-- Separator --}}
<div class="my-6 mx-4 border-t border-gray-200"></div>

{{-- Uang Jalan Supir Dropdown --}}
@php
    $isUangJalanSupirRoute = $isPermohonanRoute || $isPranotaRoute || $isPenyelesaianRoute;
    $hasUangJalanSupirPermission = ($isAdmin || auth()->user()->can('permohonan-memo-view')) ||
        ($isAdmin ||
            auth()->user()->can('pranota-supir') ||
            auth()->user()->can('pranota-supir-view') ||
            auth()->user()->can('pranota-supir-create') ||
            auth()->user()->can('pranota-supir-update') ||
            auth()->user()->can('pranota-supir-delete') ||
            auth()->user()->can('pranota-supir-approve') ||
            auth()->user()->can('pranota-supir-print') ||
            auth()->user()->can('pranota-supir-export') ||
            auth()->user()->can('pembayaran-pranota-supir') ||
            auth()->user()->can('pembayaran-pranota-supir-view') ||
            auth()->user()->can('pembayaran-pranota-supir-create') ||
            auth()->user()->can('pembayaran-pranota-supir-update') ||
            auth()->user()->can('pembayaran-pranota-supir-delete') ||
            auth()->user()->can('pembayaran-pranota-supir-approve') ||
            auth()->user()->can('pembayaran-pranota-supir-print') ||
            auth()->user()->can('pembayaran-pranota-supir-export')) ||
        ($isAdmin ||
            auth()->user()->can('approval-view') ||
            auth()->user()->can('approval-approve') ||
            auth()->user()->can('approval-print') ||
            auth()->user()->can('approval-dashboard') ||
            auth()->user()->can('approval') ||
            auth()->user()->can('permohonan.approve'));
@endphp

@if($hasUangJalanSupirPermission)
<div class="mt-4 mb-4">
    <button id="uang-jalan-supir-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg mt-4 mb-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isUangJalanSupirRoute ? 'bg-emerald-50 text-emerald-700 font-medium' : '' }}">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isUangJalanSupirRoute ? 'bg-emerald-100' : '' }}">
                <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isUangJalanSupirRoute ? 'text-emerald-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <span class="text-xs font-medium truncate w-full">Uang Jalan Supir</span>
        </div>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isUangJalanSupirRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="uang-jalan-supir-menu-content" class="dropdown-content ml-8 space-y-4 mt-4 mb-4" @if($isUangJalanSupirRoute) style="display: block;" @endif>
        {{-- Permohonan Memo Sub-section --}}
        @if($isAdmin || auth()->user()->can('permohonan-memo-view'))
            <div class="space-y-2">
                <div class="flex items-center py-1 px-3 text-xs font-medium text-gray-700 border-b border-gray-200 pb-1">
                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Permohonan Memo
                </div>
                <a href="{{ route('permohonan.create') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 ml-4 {{ Request::routeIs('permohonan.create') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Permohonan
                </a>
                <a href="{{ route('permohonan.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 ml-4 {{ Request::routeIs('permohonan.index') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Daftar Permohonan
                </a>
            </div>
        @endif

        {{-- Pranota Supir Sub-section --}}
        @php
            $hasPranotaPermission = $isAdmin ||
                auth()->user()->can('pranota-supir') ||
                auth()->user()->can('pranota-supir-view') ||
                auth()->user()->can('pranota-supir-create') ||
                auth()->user()->can('pranota-supir-update') ||
                auth()->user()->can('pranota-supir-delete') ||
                auth()->user()->can('pranota-supir-approve') ||
                auth()->user()->can('pranota-supir-print') ||
                auth()->user()->can('pranota-supir-export') ||
                auth()->user()->can('pembayaran-pranota-supir') ||
                auth()->user()->can('pembayaran-pranota-supir-view') ||
                auth()->user()->can('pembayaran-pranota-supir-create') ||
                auth()->user()->can('pembayaran-pranota-supir-update') ||
                auth()->user()->can('pembayaran-pranota-supir-delete') ||
                auth()->user()->can('pembayaran-pranota-supir-approve') ||
                auth()->user()->can('pembayaran-pranota-supir-print') ||
                auth()->user()->can('pembayaran-pranota-supir-export');
        @endphp
        @if($hasPranotaPermission)
            <div class="space-y-2">
                <div class="flex items-center py-1 px-3 text-xs font-medium text-gray-700 border-b border-gray-200 pb-1">
                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Pranota Supir
                </div>
                <a href="{{ route('pranota-supir.create') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 ml-4 {{ Request::routeIs('pranota-supir.create') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Pranota Supir
                </a>
                <a href="{{ route('pranota-supir.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 ml-4 {{ Request::routeIs('pranota-supir.index') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Daftar Pranota Supir
                </a>
                @if($user && ($user->can('pembayaran-pranota-supir-create') || $user->can('pembayaran-pranota-supir-view')))
                    <a href="{{ route('pembayaran-pranota-supir.create') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 ml-4 {{ Request::routeIs('pembayaran-pranota-supir.create') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Pembayaran Pranota Supir
                    </a>
                @endif
                @if($user && $user->can('pembayaran-pranota-supir-view'))
                    <a href="{{ route('pembayaran-pranota-supir.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 ml-4 {{ Request::routeIs('pembayaran-pranota-supir.index') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Daftar Pembayaran Pranota Supir
                    </a>
                @endif
            </div>
        @endif

        {{-- Approval Tugas --}}
        @php
            $hasApprovalPermission = $isAdmin ||
                auth()->user()->can('approval-view') ||
                auth()->user()->can('approval-approve') ||
                auth()->user()->can('approval-print') ||
                auth()->user()->can('approval-dashboard') ||
                auth()->user()->can('approval') ||
                auth()->user()->can('permohonan.approve');
        @endphp
        @if($hasApprovalPermission)
            <a href="{{ route('approval.dashboard') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('approval.dashboard') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Approval Tugas
            </a>
        @endif
    </div>
</div>
@endif

{{-- Tagihan Kontainer Dropdown --}}
@php
    $isTagihanKontainerRoute = Request::routeIs('daftar-tagihan-kontainer-sewa.*') ||
        Request::routeIs('pranota.*') ||
        Request::routeIs('perbaikan-kontainer.*') ||
        Request::routeIs('pranota-perbaikan-kontainer.*') ||
        Request::routeIs('tagihan-cat.*') ||
        Request::routeIs('pranota-cat.*');
    $hasTagihanKontainerPermission = ($isAdmin || auth()->user()->can('tagihan-kontainer-view')) ||
        ($isAdmin || auth()->user()->can('pranota.view')) ||
        ($isAdmin || auth()->user()->can('perbaikan-kontainer-view')) ||
        ($isAdmin || auth()->user()->can('pranota-perbaikan-kontainer-view')) ||
        ($isAdmin || auth()->user()->can('tagihan-cat-view')) ||
        ($isAdmin || auth()->user()->can('pranota-cat-view'));
@endphp

@if($hasTagihanKontainerPermission)
<div class="mt-4 mb-4">
    <button id="tagihan-kontainer-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg mt-4 mb-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isTagihanKontainerRoute ? 'bg-purple-50 text-purple-700 font-medium' : '' }}">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isTagihanKontainerRoute ? 'bg-purple-100' : '' }}">
                <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isTagihanKontainerRoute ? 'text-purple-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium truncate w-full">Tagihan Kontainer</span>
        </div>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isTagihanKontainerRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="tagihan-kontainer-menu-content" class="dropdown-content ml-8 space-y-4 mt-4 mb-4" @if($isTagihanKontainerRoute) style="display: block;" @endif>
        {{-- Daftar Tagihan Kontainer Sewa --}}
        @if(Route::has('daftar-tagihan-kontainer-sewa.index') && ($isAdmin || auth()->user()->can('tagihan-kontainer-view')))
            <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('daftar-tagihan-kontainer-sewa.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Daftar Tagihan Kontainer Sewa
            </a>
        @endif

        {{-- Daftar Pranota Kontainer Sewa --}}
        @if(Route::has('pranota.index') && ($isAdmin || auth()->user()->can('pranota.view')))
            <a href="{{ route('pranota.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pranota.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Daftar Pranota Kontainer Sewa
            </a>
        @endif

        {{-- Daftar Tagihan Perbaikan Kontainer --}}
        @if(Route::has('perbaikan-kontainer.index') && ($isAdmin || auth()->user()->can('perbaikan-kontainer-view')))
            <a href="{{ route('perbaikan-kontainer.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('perbaikan-kontainer.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Daftar Tagihan Perbaikan Kontainer
            </a>
        @endif

        {{-- Pranota Perbaikan Kontainer --}}
        @if(Route::has('pranota-perbaikan-kontainer.index') && ($isAdmin || auth()->user()->can('pranota-perbaikan-kontainer-view')))
            <a href="{{ route('pranota-perbaikan-kontainer.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pranota-perbaikan-kontainer.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Pranota Perbaikan Kontainer
            </a>
        @endif

        {{-- Daftar Tagihan CAT --}}
        @if(Route::has('tagihan-cat.index') && ($isAdmin || auth()->user()->can('tagihan-cat-view')))
            <a href="{{ route('tagihan-cat.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('tagihan-cat.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Daftar Tagihan CAT
            </a>
        @endif

        {{-- Daftar Pranota Tagihan CAT --}}
        @if(Route::has('pranota-cat.index') && ($isAdmin || auth()->user()->can('pranota-cat-view')))
            <a href="{{ route('pranota-cat.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pranota-cat.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Daftar Pranota Tagihan CAT
            </a>
        @endif
    </div>
</div>
@endif

{{-- Pembayaran Tagihan Kontainer Dropdown --}}
@php
    $isPembayaranTagihanKontainerRoute = Request::routeIs('pembayaran-pranota-kontainer.*') ||
        Request::routeIs('pembayaran-pranota-perbaikan-kontainer.*');
    $hasPembayaranTagihanKontainerPermission = ($isAdmin || auth()->user()->can('pembayaran-pranota-kontainer-view')) ||
        ($isAdmin || auth()->user()->can('pembayaran-pranota-perbaikan-kontainer-view'));
@endphp

@if($hasPembayaranTagihanKontainerPermission)
<div class="mt-4 mb-4">
    <button id="pembayaran-tagihan-kontainer-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg mt-4 mb-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isPembayaranTagihanKontainerRoute ? 'bg-red-50 text-red-700 font-medium' : '' }}">
        <div class="flex items-center">
            <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isPembayaranTagihanKontainerRoute ? 'bg-red-100' : '' }}">
                <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isPembayaranTagihanKontainerRoute ? 'text-red-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium truncate w-full">Pembayaran Tagihan Kontainer</span>
        </div>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isPembayaranTagihanKontainerRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="pembayaran-tagihan-kontainer-menu-content" class="dropdown-content ml-8 space-y-4 mt-4 mb-4" @if($isPembayaranTagihanKontainerRoute) style="display: block;" @endif>
        {{-- Pembayaran Pranota Tagihan Kontainer Sewa --}}
        @if(Route::has('pembayaran-pranota-kontainer.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-kontainer-view')))
            <a href="{{ route('pembayaran-pranota-kontainer.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-kontainer.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Pembayaran Pranota Tagihan Kontainer Sewa
            </a>
        @endif

        {{-- Pembayaran Pranota Perbaikan Kontainer --}}
        @if(Route::has('pembayaran-pranota-perbaikan-kontainer.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-perbaikan-kontainer-view')))
            <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-perbaikan-kontainer.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pembayaran Pranota Perbaikan Kontainer
            </a>
        @endif
    </div>
</div>
@endif

{{-- User Approval Management (only for admins or users with master-user or user-approval permission) --}}
{{-- Sudah dipindahkan ke dropdown User --}}



                    {{-- Dropdown untuk Tagihan Kontainer Sewa (di luar master data) --}}
                    {{-- Tagihan Kontainer Sewa menu removed (refactored) --}}

                    {{-- Dropdown untuk Tagihan Kontainer Sewa (baru) --}}
                    @php
                        // Check if user has access to any submenu in this section
                        $hasTagihanAccess = $isAdmin || auth()->user()->can('tagihan-kontainer-view');
                        $hasPranotaAccess = $isAdmin || auth()->user()->can('pranota.view');
                        $hasPembayaranPranotaAccess = $isAdmin || auth()->user()->can('pembayaran-pranota-kontainer.view') || auth()->user()->can('pembayaran-pranota-tagihan-kontainer.view');
                        $hasAnyAccess = $hasTagihanAccess || $hasPranotaAccess || $hasPembayaranPranotaAccess;
                    @endphp

                    @if($hasAnyAccess)
                    @php
                        $isPranotaTagihanRoute = Request::routeIs('pembayaran-pranota-tagihan-kontainer.*') || Request::routeIs('pranota-tagihan-kontainer.*') || Request::routeIs('pembayaran-pranota-kontainer.*') || Request::routeIs('pranota.*') || Request::routeIs('daftar-tagihan-kontainer-sewa.*');
                    @endphp
                    <div class="mt-4 mb-4">
                        <button id="pranota-tagihan-kontainer-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg mt-4 mb-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isPranotaTagihanRoute ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isPranotaTagihanRoute ? 'bg-blue-100' : '' }}">
                                    <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isPranotaTagihanRoute ? 'text-blue-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-medium menu-text">Tagihan Kontainer Sewa</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isPranotaTagihanRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="pranota-tagihan-kontainer-menu-content" class="dropdown-content ml-8 space-y-4 mt-4 mb-4" @if($isPranotaTagihanRoute) style="display: block;" @endif>
                            @if (Route::has('pembayaran-pranota-tagihan-kontainer.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-tagihan-kontainer.view')))
                                <a href="{{ route('pembayaran-pranota-tagihan-kontainer.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs {{ Request::routeIs('pembayaran-pranota-tagihan-kontainer.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <div class="w-2 h-2 rounded-full {{ Request::routeIs('pembayaran-pranota-tagihan-kontainer.*') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                    Pembayaran Tagihan Kontainer Sewa
                                </a>
                            @endif

                            @if (Route::has('daftar-tagihan-kontainer-sewa.index') && ($isAdmin || auth()->user()->can('tagihan-kontainer-view')))
                                <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs {{ Request::routeIs('daftar-tagihan-kontainer-sewa.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <div class="w-2 h-2 rounded-full {{ Request::routeIs('daftar-tagihan-kontainer-sewa.*') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                    Daftar Tagihan Kontainer
                                </a>
                            @endif

                            @if (Route::has('pranota.index') && ($isAdmin || auth()->user()->can('pranota.view')))
                                <a href="{{ route('pranota.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs {{ Request::routeIs('pranota.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <div class="w-2 h-2 rounded-full {{ Request::routeIs('pranota.*') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                    Daftar Pranota Kontainer
                                </a>
                            @endif

                            @if (Route::has('pembayaran-pranota-kontainer.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-kontainer.view')))
                                <a href="{{ route('pembayaran-pranota-kontainer.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs {{ Request::routeIs('pembayaran-pranota-kontainer.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <div class="w-2 h-2 rounded-full {{ Request::routeIs('pembayaran-pranota-kontainer.*') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                    Pembayaran Pranota Kontainer
                                </a>
                            @endif

                            @if (Route::has('pembayaran-pranota-tagihan-kontainer.create') && ($isAdmin || auth()->user()->can('pembayaran-pranota-tagihan-kontainer.create')))
                                <a href="{{ route('pembayaran-pranota-tagihan-kontainer.create') }}" class="flex items-center py-1 px-4 rounded-lg text-xs {{ Request::routeIs('pembayaran-pranota-tagihan-kontainer.create') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <div class="w-2 h-2 rounded-full {{ Request::routeIs('pembayaran-pranota-tagihan-kontainer.create') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                    Buat Pembayaran
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Menu Perbaikan Kontainer --}}
                    @php
                        $hasPerbaikanKontainerPermission = $isAdmin ||
                            auth()->user()->can('perbaikan-kontainer-view') ||
                            auth()->user()->can('perbaikan-kontainer-create') ||
                            auth()->user()->can('perbaikan-kontainer-update') ||
                            auth()->user()->can('perbaikan-kontainer-delete') ||
                            auth()->user()->can('tagihan-cat-view') ||
                            auth()->user()->can('tagihan-cat-create') ||
                            auth()->user()->can('tagihan-cat-update') ||
                            auth()->user()->can('tagihan-cat-delete');
                        $isPerbaikanKontainerRoute = Request::routeIs('perbaikan-kontainer.*') ||
                            Request::routeIs('pranota-perbaikan-kontainer.*') ||
                            Request::routeIs('pembayaran-pranota-perbaikan-kontainer.*') ||
                            Request::routeIs('tagihan-cat.*') ||
                            Request::routeIs('pranota-cat.*');
                    @endphp
                    @if($hasPerbaikanKontainerPermission)
                    <div class="mb-1">
                        <button id="perbaikan-kontainer-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg mt-4 mb-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isPerbaikanKontainerRoute ? 'bg-green-50 text-green-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-2 {{ $isPerbaikanKontainerRoute ? 'bg-green-100' : '' }}">
                                    <svg class="w-4 h-4 text-gray-600 group-hover:text-gray-700 {{ $isPerbaikanKontainerRoute ? 'text-green-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-medium menu-text">Perbaikan Kontainer</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isPerbaikanKontainerRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="perbaikan-kontainer-menu-content" class="dropdown-content ml-8 space-y-4 mt-4 mb-4" @if($isPerbaikanKontainerRoute) style="display: block;" @endif>
                            @if($user && $user->can('perbaikan-kontainer-view'))
                                <a href="{{ route('perbaikan-kontainer.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs {{ Request::routeIs('perbaikan-kontainer.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Daftar Tagihan Perbaikan Kontainer
                                </a>
                            @endif
                            @if($user && $user->can('pranota-perbaikan-kontainer-view'))
                                <a href="{{ route('pranota-perbaikan-kontainer.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs {{ Request::routeIs('pranota-perbaikan-kontainer.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Daftar Pranota Perbaikan Kontainer
                                </a>
                            @endif
                            @if($user && $user->can('pembayaran-pranota-perbaikan-kontainer-view'))
                                <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs {{ Request::routeIs('pembayaran-pranota-perbaikan-kontainer.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Pembayaran Pranota Perbaikan Kontainer
                                </a>
                            @endif
                            @if($user && $user->can('tagihan-cat-view'))
                                <a href="{{ route('tagihan-cat.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs {{ Request::routeIs('tagihan-cat.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Daftar Tagihan CAT
                                </a>
                            @endif
                            @if($user && $user->can('pranota-cat-view'))
                                <a href="{{ route('pranota-cat.index') }}" class="flex items-center py-1 px-4 rounded-lg text-xs {{ Request::routeIs('pranota-cat.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Daftar Pranota Tagihan CAT
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- User Approval Management (only for admins or users with master-user or user-approval permission) --}}
                    {{-- Sudah dipindahkan ke bawah Master Data --}}


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

        function openSidebar() {
            sidebar.classList.remove('hidden');
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }

        function closeSidebarMenu() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            setTimeout(() => {
                sidebar.classList.add('hidden');
            }, 300);
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

        setupDropdown('master-menu-toggle', 'master-menu-content');
        setupDropdown('user-menu-toggle', 'user-menu-content');
        setupDropdown('uang-jalan-supir-menu-toggle', 'uang-jalan-supir-menu-content');
        setupDropdown('aktivitas-supir-menu-toggle', 'aktivitas-supir-menu-content');
        setupDropdown('aktiva-menu-toggle', 'aktiva-menu-content');
        setupDropdown('kontainer-menu-toggle', 'kontainer-menu-content');
        setupDropdown('pranota-tagihan-kontainer-menu-toggle', 'pranota-tagihan-kontainer-menu-content');
        setupDropdown('perbaikan-kontainer-menu-toggle', 'perbaikan-kontainer-menu-content');
        setupDropdown('tagihan-kontainer-menu-toggle', 'tagihan-kontainer-menu-content');
        setupDropdown('pembayaran-tagihan-kontainer-menu-toggle', 'pembayaran-tagihan-kontainer-menu-content');

        // Sidebar search functionality
        const sidebarSearch = document.getElementById('sidebar-search');
        const clearSearch = document.getElementById('clear-search');
        const nav = document.querySelector('nav.sidebar-scroll');

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

                menuItems.forEach(item => {
                    const text = item.textContent.toLowerCase().trim();
                    const isVisible = text.includes(searchTerm.toLowerCase()) || searchTerm === '';

                    if (item.tagName === 'A' || item.tagName === 'BUTTON') {
                        // Main menu items (Dashboard, Master Data, etc.)
                        if (isVisible) {
                            item.style.display = '';
                            if (searchTerm !== '') {
                                highlightText(item, searchTerm);
                            }
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
                                        if (searchTerm !== '') {
                                            const subText = subItem.textContent.toLowerCase().trim();
                                            if (subText.includes(searchTerm.toLowerCase())) {
                                                highlightText(subItem, searchTerm);
                                            }
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
                        const isVisible = text.includes(searchTerm.toLowerCase()) || searchTerm === '';

                        if (isVisible) {
                            subItem.style.display = '';
                            if (searchTerm !== '') {
                                highlightText(subItem, searchTerm);
                            }
                            hasVisibleSubItems = true;
                        } else {
                            subItem.style.display = 'none';
                        }
                    });

                    // Show/hide dropdown content based on visible subitems
                    if (hasVisibleSubItems || searchTerm === '') {
                        content.style.display = 'block';
                        // Also show the parent toggle
                        const toggleId = content.id?.replace('-content', '-toggle');
                        if (toggleId) {
                            const toggle = document.getElementById(toggleId);
                            if (toggle) {
                                toggle.style.display = '';
                                if (searchTerm !== '' && toggle.textContent.toLowerCase().trim().includes(searchTerm.toLowerCase())) {
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
