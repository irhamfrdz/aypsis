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

    <script>
        if (window.parent !== window && window.name === 'master-tarif-frame') {
            document.documentElement.classList.add('master-tarif-embedded');
        }
    </script>
    
    <style>
        html.master-tarif-embedded,
        html.master-tarif-embedded body { height: auto; overflow: auto; }
        html.master-tarif-embedded body { display: block; }
        html.master-tarif-embedded body > header,
        html.master-tarif-embedded #sidebar,
        html.master-tarif-embedded #sidebar-overlay { display: none; }
        html.master-tarif-embedded #page-content { margin: 0; overflow: visible; }
        html.master-tarif-embedded #page-content > div { padding: 0.75rem; }

        html, body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

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

        .clean-table th {
            padding: 12px 16px;
            border-bottom: 1px solid #dee2e6;
            background-color: #f8f9fa;
            text-align: left;
            font-weight: 600;
        }

        .clean-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f3f4;
            font-size: 0.875rem;
        }

        .clean-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Consistent navigation across all permission-based menu groups. */
        #sidebar {
            width: min(288px, 100vw);
            background: #fff;
            border-right: 1px solid #e2e8f0;
            border-radius: 0;
            box-shadow: 4px 0 24px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        #sidebar nav {
            min-height: 0;
            padding: 12px 10px 24px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
            overscroll-behavior: contain;
        }

        #sidebar nav > * { margin-top: 0; margin-bottom: 4px; }
        #sidebar nav > div { padding-top: 8px; }
        #sidebar nav > div + div { border-top: 1px solid #f1f5f9; }

        #sidebar nav a,
        #sidebar nav button {
            min-height: 36px;
            margin: 2px 0;
            padding: 8px 10px;
            border-radius: 7px;
            color: #475569;
            font-size: 13px;
            line-height: 1.4;
            text-align: left;
            box-shadow: none;
            transition: background-color 150ms, color 150ms;
        }

        #sidebar nav a:hover,
        #sidebar nav button:hover { background: #f1f5f9; color: #1e40af; }
        #sidebar nav a:focus-visible,
        #sidebar nav button:focus-visible { outline: 2px solid #2563eb; outline-offset: 2px; }
        #sidebar nav span { font-size: inherit; }
        #sidebar nav svg { flex-shrink: 0; }
        #sidebar .menu-text { white-space: normal; overflow: visible; }
        #sidebar nav .truncate { white-space: normal; overflow: visible; }
        #sidebar nav > div > button { font-weight: 600; color: #334155; }
        #sidebar nav button[aria-expanded="true"] { background: #f8fafc; color: #1d4ed8; }
        #sidebar nav .dropdown-content {
            margin: 4px 0 6px 12px;
            padding-left: 8px;
            border-left: 1px solid #e2e8f0;
        }
        #sidebar .dropdown-content > div { margin-left: 0; margin-right: 0; margin-bottom: 4px; }
        #sidebar nav [aria-current="page"],
        #sidebar nav .sidebar-active { background: #eff6ff; color: #1d4ed8; font-weight: 600; }
        #sidebar nav .sidebar-active::before { background: #2563eb; height: 18px; }
        #sidebar .sidebar-search-highlight { padding: 0; animation: none; }
        .sidebar-search-area { padding: 16px; border-bottom: 1px solid #e2e8f0; }
        .sidebar-brand { padding: 20px 16px 16px; }
        #page-content { min-width: 0; }

        @media (min-width: 1024px) {
            #sidebar { top: 0; position: relative; flex-shrink: 0; height: 100%; }
            .sidebar-search-area { padding-top: 0; }
            .page-content-with-sidebar { margin-left: 0; min-width: 0; }
        }

        @media (prefers-reduced-motion: reduce) {
            #sidebar, #sidebar * { transition: none !important; animation: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 flex flex-col h-screen">
    @php
        $user = Auth::user();
        $hasKaryawan = $user && $user->karyawan;
        $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
        $isSupirRoute = Request::routeIs('supir.*');
        $showSidebar = ($hasKaryawan || $isAdmin || $user) && !$isSupirRoute && !isset($hideSidebar);
    @endphp
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
        <div class="px-6 py-4 flex justify-between items-center">
            <div class="flex items-center">
                @if($showSidebar ?? true)
                <button type="button" id="mobile-menu-button" aria-label="Buka menu navigasi" aria-controls="sidebar" aria-expanded="false" class="lg:hidden mr-3 p-1 text-gray-600 hover:text-gray-900 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                @endif
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                <!-- Notification Bell -->
                <div class="relative">
                    <a href="{{ route('notifications.index') }}" target="_blank" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
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
                            <a href="{{ route('profile.show') }}" target="_blank" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
                @else
                <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-indigo-600 transition-colors">
                    Login
                </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="flex flex-1 overflow-hidden">
    @if($showSidebar)
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden transition-opacity duration-300"></div>
    
    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
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
                <button type="button" id="close-sidebar" aria-label="Tutup menu navigasi" class="text-white hover:bg-blue-700 p-2 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Header (Desktop Only) -->
            <div class="hidden lg:block sidebar-brand flex-shrink-0">
                <div class="flex items-center text-gray-800">
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

            </div>

            <!-- Search remains available while the menu scrolls, including on mobile. -->
            <div class="sidebar-search-area flex-shrink-0">
                <label for="sidebar-search" class="sr-only">Cari menu navigasi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           id="sidebar-search"
                           placeholder="Cari menu..."
                           class="sidebar-search-input w-full pl-10 pr-10 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 focus:bg-white transition-colors duration-200"
                           autocomplete="off">
                    <button type="button"
                            id="clear-search"
                            aria-label="Hapus pencarian menu"
                            disabled
                            class="sidebar-clear-btn absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 opacity-0 pointer-events-none transition-opacity duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav id="sidebar-navigation" aria-label="Navigasi utama" class="sidebar-scroll flex-1 overflow-y-auto">
                @php
                    $user = Auth::user();
                    $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
                    $isDashboard = Request::routeIs('dashboard');
                    $isAssetDashboard = Request::routeIs('dashboard.asset-insurance');

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
                        $user->can('master-pricelist-buruh-view') ||
                        $user->can('master-pricelist-biaya-dokumen-view') ||
                        $user->can('master-user-view') ||
                        $user->can('master-karyawan-view') ||
                        $user->can('master-jam-kerja-view') ||
                        $user->can('master-divisi-view') ||
                        $user->can('master-pekerjaan-view') ||
                        $user->can('master-pajak-view') ||
                        $user->can('master-penerima-bukti-potong-pph-view') ||
                        $user->can('master-bank-view') ||
                        $user->can('master-vendor-bengkel.view') ||
                        $user->can('master-vendor-asuransi-view') ||
                        $user->can('master-vendor-supir-view') ||
                        $user->can('vendor-kontainer-sewa-view') ||
                        $user->can('master-dokumen-kapal-alexindo-view') ||
                        $user->can('master-mobil-view') ||
                        $user->can('master-kapal.view') ||
                        $user->can('master-alat-berat-view') ||
                        $user->can('asset-view') ||
                        $user->can('master-asset-view') ||
                        $user->can('master-stock-kontainer-view') ||
                        $user->can('master-kontainer-view') ||
                        $user->can('master-chasis-batam-view') ||
                        $user->can('master-gudang-view') ||
                        $user->can('stock-ban-view') ||
                        $user->can('master-gudang-ban-view') ||
                        $user->can('master-gudang-amprahan-view') ||
                        $user->can('master-type-bon-amprahan-view') ||
                        $user->can('master-kartu-bensin-batam-view') ||

                        $user->can('master-pricelist-labuh-tambat-view') ||
                        $user->can('master-pricelist-freight-view') ||
                        $user->can('master-pricelist-pelindo-view') ||
                        $user->can('master-shipper-consignee-view') ||
                        $user->can('master-wa-templates-view') ||
                        $user->can('master-wa-broadcast-view') ||
                        $user->can('master-customer-buruh-view') ||
                        collect(config('master_tarif'))->contains(fn ($item) =>
                            $item['permission'] && ($user->can($item['permission']) || (!empty($item['admin']) && $isAdmin))
                        )
                    );


                    // Show master section if user is admin OR has any master permissions
                    $showMasterSection = $isAdmin || $hasMasterPermissions;
                @endphp

                <!-- Dashboard menus -->
                @php
                    $isDashboardSection = Request::routeIs(
                        'dashboard', 'dashboard.*', 'dashboard-dokumen-kapal-alexindo',
                        'hrd.dashboard*', 'gerak-voyage.dashboard',
                        'admin.cek-kendaraan.daily', 'admin.cek-kendaraan.weekly',
                        'stock-amprahan.dashboard-pemakaian', 'surat-jalan-bongkaran-batam.dashboard',
                        'dashboard-perjalanan-kendaraan.index'
                    );
                @endphp
                <div>
                    <button type="button" id="dashboard-menu-toggle" aria-controls="dashboard-menu-content" aria-expanded="{{ $isDashboardSection ? 'true' : 'false' }}" class="w-full flex justify-between items-center group {{ $isDashboardSection ? 'bg-blue-50 text-blue-700' : '' }}">
                        <span class="flex items-center">
                            <span class="font-semibold">Dashboard</span>
                        </span>
                        <svg class="w-4 h-4 dropdown-arrow transition-transform {{ $isDashboardSection ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="dashboard-menu-content" class="dropdown-content" @if($isDashboardSection) style="display: block;" @endif>
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" target="_blank" class="flex items-center py-2 px-5 rounded-xl mt-4 mb-4 transition-all duration-200 group shadow-sm text-xs {{ $isDashboard ? 'bg-blue-100 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-700' }}">
                            <span class="text-xs font-medium menu-text">Dashboard Utama</span>
                        </a>

                        <a href="{{ route('dashboard-perjalanan-kendaraan.index') }}" class="flex items-center py-2 px-5 rounded-xl mb-4 transition-all duration-200 group shadow-sm text-xs {{ Request::routeIs('dashboard-perjalanan-kendaraan.index') ? 'bg-blue-100 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-700' }}">
                            <span class="text-xs font-medium menu-text">Dashboard Perjalanan Kendaraan</span>
                        </a>

                        <!-- Dashboard Asuransi Asset -->
                        <a href="{{ route('dashboard.asset-insurance') }}" target="_blank" class="flex items-center py-2 px-5 rounded-xl mb-4 transition-all duration-200 group shadow-sm text-xs {{ $isAssetDashboard ? 'bg-yellow-100 text-yellow-700 font-bold' : 'text-gray-700 hover:bg-yellow-100 hover:text-yellow-700' }}">
                            <span class="text-xs font-medium menu-text">Dashboard Asuransi Asset</span>
                        </a>

                        <!-- Dashboard Dokumen Kapal Alexindo -->
                        @can('dashboard-dokumen-kapal-alexindo-view')
                        @php
                            $isDokumenKapalDashboard = Request::routeIs('dashboard-dokumen-kapal-alexindo');
                        @endphp
                        <a href="{{ route('dashboard-dokumen-kapal-alexindo') }}" target="_blank" class="flex items-center py-2 px-5 rounded-xl mb-4 transition-all duration-200 group shadow-sm text-xs {{ $isDokumenKapalDashboard ? 'bg-indigo-100 text-indigo-700 font-bold' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                            <span class="text-xs font-medium menu-text">Dashboard Dokumen Kapal</span>
                        </a>
                        @endcan

                        <!-- Dashboard HRD -->
                        @can('hrd-dashboard-view')
                        @php
                            $isHrdDashboard = Request::routeIs('hrd.dashboard*');
                        @endphp
                        <a href="{{ route('hrd.dashboard') }}" target="_blank" class="flex items-center py-2 px-5 rounded-xl mb-4 transition-all duration-200 group shadow-sm text-xs {{ $isHrdDashboard ? 'bg-indigo-100 text-indigo-700 font-bold' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                            <span class="text-xs font-medium menu-text">Dashboard HRD</span>
                        </a>
                        @endcan

                        <!-- Dashboard Gerak Voyage -->
                        @if($user && ($user->can('gerak-voyage-view') || $user->is_admin))
                        @php
                            $isGerakVoyageDashboard = Request::routeIs('gerak-voyage.dashboard');
                        @endphp
                        <a href="{{ route('gerak-voyage.dashboard') }}" target="_blank" class="flex items-center py-2 px-5 rounded-xl mb-4 transition-all duration-200 group shadow-sm text-xs {{ $isGerakVoyageDashboard ? 'bg-blue-100 text-blue-700 font-bold' : 'text-gray-700 hover:bg-blue-100 hover:text-blue-700' }}">
                            <span class="text-xs font-medium menu-text">Dashboard Gerak Voyage</span>
                        </a>
                        @endif

                        @if($user && $user->can('monitoring-cek-kendaraan-daily-view'))
                            <a href="{{ route('admin.cek-kendaraan.daily') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('admin.cek-kendaraan.daily') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Dashboard Cek Harian</span>
                            </a>
                        @endif

                        @if($user && $user->can('monitoring-cek-kendaraan-weekly-view'))
                            <a href="{{ route('admin.cek-kendaraan.weekly') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('admin.cek-kendaraan.weekly') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Dashboard Cek Mingguan</span>
                            </a>
                        @endif

                        @can('stock-amprahan-view')
                        <div class="mx-2 mb-3">
                            <a href="{{ route('stock-amprahan.dashboard-pemakaian') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 {{ Request::routeIs('stock-amprahan.dashboard-pemakaian') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">Dashboard Pemakaian Barang</a>
                        </div>
                        @endcan

                        {{-- Dashboard Surat Jalan Bongkaran Batam --}}
                        @if($user && ($user->can('surat-jalan-bongkaran-batam-view') || $user->can('surat-jalan-bongkaran-batam-create')))
                            <a href="{{ route('surat-jalan-bongkaran-batam.dashboard') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('surat-jalan-bongkaran-batam.dashboard') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Dashboard Bongkaran Batam</span>
                            </a>
                        @endif

                    </div>
                </div>
                <!-- Asuransi Tanda Terima -->
                @can('asuransi-tanda-terima-view')
                @php
                    $isAsuransiTandaTerima = Request::is('asuransi-tanda-terima*');
                @endphp
                <a href="{{ route('asuransi-tanda-terima.index') }}" target="_blank" class="flex items-center py-2 px-5 rounded-xl mb-4 transition-all duration-200 group shadow-sm text-xs {{ $isAsuransiTandaTerima ? 'bg-green-100 text-green-700 font-bold' : 'text-gray-700 hover:bg-green-100 hover:text-green-700' }}">
                    <span class="text-xs font-medium menu-text">Asuransi Tanda Terima</span>
                </a>
                @endcan

                @php
                    $showTrackingTransaksi = $user && (
                        $user->can('surat-jalan-view') ||
                        $user->can('surat-jalan-bongkaran-view') ||
                        $user->can('tanda-terima-view') ||
                        $user->can('tanda-terima-tanpa-surat-jalan-view')
                    );
                @endphp
                {{-- Monitoring Section --}}
                @php
                    $isLiveTracking = Request::routeIs('gps-tracking.*');
                    $isChatbox = Request::routeIs('chat.*');
                    $isMonitoringRoute = Request::routeIs('admin.cek-kendaraan.index') || Request::routeIs('tracking-transaksi.*') || $isLiveTracking || $isChatbox;
                    $showMonitoringSection = (bool) $user;
                @endphp

                @if($showMonitoringSection)
                <div class="mt-4 mb-6">
                    <button id="monitoring-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-sm font-medium {{ $isMonitoringRoute ? 'bg-green-50 text-green-700' : '' }}">
                        <span class="text-sm font-semibold">Monitoring</span>
                        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isMonitoringRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="monitoring-menu-content" class="dropdown-content ml-2 mt-3 space-y-2" @if($isMonitoringRoute) style="display: block;" @endif>
                        @if($showTrackingTransaksi)
                            <a href="{{ route('tracking-transaksi.index') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('tracking-transaksi.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium menu-text">Tracking Transaksi</span>
                            </a>
                        @endif
                        <a href="{{ route('gps-tracking.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ $isLiveTracking ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                            <span class="text-xs font-medium menu-text">Live Tracking (GPS)</span>
                        </a>

                <!-- Chatbox -->
                @can('chatbox-view')
                <a href="{{ route('chat.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ $isChatbox ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                    <span class="text-xs font-medium menu-text">Chatbox</span>
                </a>
                @endcan

                        @if($user && $user->can('monitoring-cek-kendaraan-view'))
                            <a href="{{ route('admin.cek-kendaraan.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('admin.cek-kendaraan.index') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Monitoring Cek Kendaraan</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Kelola Absensi Section --}}
                @php
                    $isAbsensiRoute = Request::routeIs('absensi.*') || Request::routeIs('master.lokasi-absensi.*') || Request::routeIs('master.persetujuan-absensi.*') || Request::routeIs('master.persetujuan-absensi-lupa.*') || Request::routeIs('master.persetujuan-absensi-lembur.*');
                    $hasAbsensiPermissions = $isAdmin || ($user && ($user->can('absensi-view') || $user->can('kelola-absensi-view') || $user->can('master-lokasi-absensi-view') || $user->can('approval-absensi-view') || $user->can('approval-absensi-lupa-view') || $user->can('approval-absensi-lembur-view')));
                @endphp

                @if($hasAbsensiPermissions)
                <div class="mt-4 mb-6">
                    <button id="absensi-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-sm font-medium {{ $isAbsensiRoute ? 'bg-blue-50 text-blue-700' : '' }}">
                        <span class="text-sm font-semibold">Kelola Absensi</span>
                        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isAbsensiRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="absensi-menu-content" class="dropdown-content ml-2 mt-3 space-y-2" @if($isAbsensiRoute) style="display: block;" @endif>
                        <a href="{{ Route::has('absensi.index') ? route('absensi.index') : '#' }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('absensi.index') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                            <span class="text-xs font-medium">Data Absensi</span>
                        </a>
                        @if($user && $user->can('approval-absensi-view'))
                            <a href="{{ route('master.persetujuan-absensi.index') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.persetujuan-absensi.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Persetujuan Absensi</span>
                            </a>
                        @endif
                        @if($user && $user->can('approval-absensi-lupa-view'))
                            <a href="{{ route('master.persetujuan-absensi-lupa.index') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.persetujuan-absensi-lupa.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Persetujuan Absensi Lupa</span>
                            </a>
                        @endif
                        @if($user && $user->can('approval-absensi-lembur-view'))
                            <a href="{{ route('master.persetujuan-absensi-lembur.index') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.persetujuan-absensi-lembur.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Persetujuan Absen Lembur</span>
                            </a>
                        @endif
                        @if($user && $user->can('master-lokasi-absensi-view'))
                            <a href="{{ route('master.lokasi-absensi.index') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.lokasi-absensi.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Kelola Lokasi (Maps)</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Berita & Pamflet --}}
                @php
                    $isBeritaRoute = Request::routeIs('berita.*');
                @endphp
                <a href="{{ route('berita.index') }}" class="flex items-center py-2 px-5 rounded-xl mb-4 transition-all duration-200 group shadow-sm text-xs {{ $isBeritaRoute ? 'bg-indigo-100 text-indigo-700 font-bold' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <span class="text-xs font-medium menu-text">Berita &amp; Pamflet (PWA)</span>
                </a>

                {{-- Data Karyawan (Cuti & Uang Makan) --}}
                @php
                    $isDataKaryawanRoute = Request::is('cuti*') || Request::routeIs('cuti.*') || Request::is('uang-makan*') || Request::routeIs('uang-makan.*') || Request::routeIs('uang-lembur.*');
                    $hasDataKaryawanPermissions = $isAdmin || ($user && ($user->can('data-cuti-view') || $user->can('data-uang-makan-view') || $user->can('payroll-uang-karyawan-view')));
                @endphp

                @if($hasDataKaryawanPermissions)
                <div class="mt-4 mb-6">
                    <button id="data-karyawan-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-sm font-medium {{ $isDataKaryawanRoute ? 'bg-blue-50 text-blue-700' : '' }}">
                        <span class="text-sm font-semibold">Uang Makan & Cuti</span>
                        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isDataKaryawanRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="data-karyawan-menu-content" class="dropdown-content ml-2 mt-3 space-y-2" @if($isDataKaryawanRoute) style="display: block;" @endif>
                        @if($user && $user->can('data-uang-makan-view'))
                            <a href="{{ route('uang-makan.index') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('uang-makan.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Data Uang Makan</span>
                            </a>
                        @endif

                        @if($user && ($user->can('payroll-uang-karyawan-view') || $isAdmin))
                            <a href="{{ route('uang-lembur.index') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('uang-lembur.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Data Uang Karyawan</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Payroll Section -->
                @php
                    $isPayrollRoute = Request::routeIs('payroll.*') || Request::routeIs('pranota-bpjs.*');
                    $showPayrollSection = $user && ($user->can('payroll-view') || $user->can('payroll-perhitungan-lembur-view') || $user->can('pranota-bpjs-view') || $isAdmin);
                @endphp

                @if($showPayrollSection)
                <div class="mt-4 mb-2">
                    <button id="payroll-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200 group text-sm font-medium {{ $isPayrollRoute ? 'bg-green-50 text-green-700 font-semibold shadow-sm' : '' }}">
                        <div class="flex items-center">
                            <span class="text-sm font-semibold">Payroll</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isPayrollRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="payroll-menu-content" class="dropdown-content ml-2 mt-3 space-y-2" @if($isPayrollRoute) style="display: block;" @endif>
                        @if($user->can('payroll-view') || $isAdmin)
                        <a href="{{ route('payroll.uang-makan') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('payroll.uang-makan') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                            <span class="text-xs font-medium">Uang Makan Mingguan</span>
                        </a>
                        @endif

                        @if($user->can('payroll-perhitungan-lembur-view') || $isAdmin)
                        <a href="{{ route('payroll.perhitungan-lembur') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('payroll.perhitungan-lembur') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                            <span class="text-xs font-medium">Perhitungan Lembur</span>
                        </a>
                        @endif

                        {{-- Pranota BPJS --}}
                        @if($user && ($user->can('pranota-bpjs-view') || $isAdmin))
                        <a href="{{ route('pranota-bpjs.index') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('pranota-bpjs.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                            <span class="text-xs font-medium">Pranota BPJS</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Master Data Section -->
                @if($showMasterSection)
                <div>
                    <a href="{{ route('master.data.index') }}" class="w-full flex items-center py-3 px-4 rounded-lg text-sm font-semibold {{ Request::routeIs('master.data.index') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}" @if(Request::routeIs('master.data.index')) aria-current="page" @endif>
                        Master Data
                    </a>
                </div>
                @endif
                <div>
                    <a href="{{ route('rekap.index') }}" class="w-full flex items-center py-3 px-4 rounded-lg text-sm font-semibold {{ Request::routeIs('rekap.index', 'pranota-puml.*', 'report.tanda-terima-jakarta.*', 'report.uang-jalan.*', 'report.pranota-uang-jalan.*', 'report.surat_jalan.*', 'report.pranota-ob.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}" @if(Request::routeIs('rekap.index')) aria-current="page" @endif>
                        Rekap dan Laporan
                    </a>
                </div>
{{-- User Dropdown has been completely moved to Master Data > Master Karyawan --}}

{{-- Aktiva Dropdown --}}
@php
    $isAktivaRoute = Request::routeIs('asset.*') || Request::routeIs('master.asset.*') || Request::routeIs('master.kontainer.*') || Request::routeIs('master.stock-kontainer.*') || Request::routeIs('history-kontainer.*') || Request::routeIs('perbaikan-kontainer.*') || Request::routeIs('master.mobil.*') || Request::routeIs('master-kapal.*') || Request::routeIs('master-jadwal-kapal-berlabuh.*') || Request::routeIs('master-gudang.*') || Request::routeIs('stock-ban.*') || Request::routeIs('master.merk-ban.*') || Request::routeIs('master.tipe-stock-ban.*') || Request::routeIs('master.gudang-amprahan.*') || Request::routeIs('master.type-bon-amprahan.*') || Request::routeIs('master.alat-berat.*') || Request::routeIs('master-dokumen-kapal-alexindo.*') || Request::routeIs('biaya-kapal.*') || Request::routeIs('pembayaran-biaya-kapal.*') || Request::routeIs('pembayaran-pranota-stock.*') || Request::routeIs('stock-amprahan.*') || Request::routeIs('pranota-stock.*') || Request::routeIs('tagihan-lain-lain-batam.*');
    $hasAktivaPermissions = $isAdmin || ($user && ($user->can('master-kontainer-view') || $user->can('master-stock-kontainer-view') || $user->can('perbaikan-kontainer-view') || $user->can('master-jadwal-kapal-berlabuh-view') || $user->can('master-gudang-view') || $user->can('stock-ban-view') || $user->can('master-merk-ban-view') || $user->can('master-nama-stock-ban-view') || $user->can('master-tipe-stock-ban-view') || $user->can('biaya-kapal-view') || $user->can('pembayaran-biaya-kapal-view') || $user->can('pembayaran-pranota-stock-view') || $user->can('pranota-stock-view') || $user->can('stock-amprahan-view') || $user->can('tagihan-service-kendaraan-batam-view') || $user->can('pranota-tagihan-lain-lain-batam-view')));
@endphp

@php
    $hasAktivaPermissions = $hasAktivaPermissions || ($user && $user->can('master-mobil-view'));
    $isAktivaRoute = $isAktivaRoute || Request::routeIs('master.armada.*') || Request::routeIs('dashboard-armada.*');
@endphp
@if($hasAktivaPermissions)
@php
    $isAktivaRoute = $isAktivaRoute || Request::routeIs('denah-gudang.*');
@endphp
<div class="mt-4 mb-6">
    <button id="aktiva-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-sm font-medium {{ $isAktivaRoute ? 'bg-blue-50 text-blue-700' : '' }}">
        <span class="text-sm font-semibold">Aktiva</span>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isAktivaRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="aktiva-menu-content" class="dropdown-content ml-2 mt-3 space-y-2" @if($isAktivaRoute) style="display: block;" @endif>
        @can('master-mobil-view')
            <a href="{{ route('dashboard-armada.index') }}" class="flex items-center py-2 px-3 mx-2 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('dashboard-armada.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">
                <span class="text-xs font-medium">Dashboard Armada</span>
            </a>
            <a href="{{ route('master.armada.index') }}" class="flex items-center py-2 px-3 mx-2 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.armada.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">
                <span class="text-xs font-medium">Master Armada</span>
            </a>
        @endcan
        {{-- Kontainer Sub-Dropdown --}}
        @php
            $isKontainerRoute = Request::routeIs('master.kontainer.*') || Request::routeIs('master.stock-kontainer.*') || Request::routeIs('master.kontainer.stock-pergudang') || Request::routeIs('history-kontainer.*') || Request::routeIs('gerak-kontainer.*') || Request::routeIs('pergerakan-kapal.*') || Request::routeIs('perbaikan-kontainer.*');
            $hasKontainerPermissions = $user && ($user->can('master-kontainer-view') || $user->can('master-stock-kontainer-view') || $user->can('perbaikan-kontainer-view'));
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
                @if($user && $user->can('master-stock-kontainer-view'))
                    <a href="{{ route('master.kontainer.stock-pergudang') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.kontainer.stock-pergudang') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Stock Kontainer per Gudang</span>
                    </a>
                @endif

                @if($user && $user->can('master-kontainer-view'))
                    <a href="{{ route('history-kontainer.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('history-kontainer.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">History Pergerakan</span>
                    </a>
                @endif

                @if($isAdmin || ($user && $user->can('gerak-kontainer-view')))
                    <a href="{{ route('gerak-kontainer.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('gerak-kontainer.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-bold text-blue-600 italic">Gerak Kontainer (Baru)</span>
                    </a>
                @endif

                @if($user && $user->can('pergerakan-kapal-view'))
                    <a href="{{ route('pergerakan-kapal.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('pergerakan-kapal.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pergerakan Kapal</span>
                    </a>
                @endif
                @if($user && $user->can('perbaikan-kontainer-view'))
                    <a href="{{ route('perbaikan-kontainer.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('perbaikan-kontainer.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 font-bold' }}">
                        <span class="text-xs">Perbaikan Kontainer</span>
                    </a>
                @endif
                @if($user && $user->can('pranota-perbaikan-kontainer-view'))
                    <a href="{{ route('pranota-perbaikan-kontainer.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('pranota-perbaikan-kontainer.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pranota Perbaikan Kontainer</span>
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Jadwal Kapal Berlabuh --}}
        @if($isAdmin || ($user && $user->can('master-jadwal-kapal-berlabuh-view')))
        <div class="mx-2 mb-3">
            <a href="{{ route('master-jadwal-kapal-berlabuh.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master-jadwal-kapal-berlabuh.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Jadwal Kapal Berlabuh</span>
            </a>
        </div>
        @endif

        {{-- Biaya Kapal --}}
        @if($user && $user->can('biaya-kapal-view'))
        <div class="mx-2 mb-3">
            <a href="{{ route('biaya-kapal.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('biaya-kapal.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Biaya Kapal</span>
            </a>
        </div>
        @endif

        {{-- Report Biaya Buruh --}}
        @if($user && $user->can('master-buruh-view'))
        <div class="mx-2 mb-3">
            <a href="{{ route('master.reports.buruh.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('master.reports.buruh.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-bold text-purple-600 italic">Report Biaya Buruh</span>
            </a>
        </div>
        @endif

        {{-- Bayar Biaya Kapal --}}
        @if($user && $user->can('pembayaran-biaya-kapal-view'))
        <div class="mx-2 mb-3">
            <a href="{{ route('pembayaran-biaya-kapal.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('pembayaran-biaya-kapal.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Bayar Biaya Kapal</span>
            </a>
        </div>
        @endif

        @if($user && $user->can('master-gudang-view'))
        <div class="mx-2 mb-3">
            <a href="{{ route('denah-gudang.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('denah-gudang.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Denah Kontainer Gudang</span>
            </a>
        </div>
        @endif

        {{-- Stock Ban --}}
        @if($user && $user->can('stock-ban-view'))
        <div class="mx-2 mb-3">
            <a href="{{ route('stock-ban.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('stock-ban.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Stock Ban</span>
            </a>
        </div>
        
        {{-- Invoice Kanisir Ban --}}
        <div class="mx-2 mb-3">
            <a href="{{ route('invoice-kanisir-ban.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('invoice-kanisir-ban.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Invoice Kanisir Ban</span>
            </a>
        </div>
        @endif

        {{-- Riwayat Pasang Ban --}}
        @if($isAdmin || ($user && $user->can('riwayat-pasang-ban-view')))
        <div class="mx-2 mb-3">
            <a href="{{ route('riwayat-pasang-ban.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('riwayat-pasang-ban.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Riwayat Pasang Ban</span>
            </a>
        </div>
        @endif
        
        {{-- Pembelian BBM Batam --}}
        @if($user && $user->can('pembelian-bbm-batam-view'))
        <div class="mx-2 mb-3">
            <a href="{{ route('pembelian-bbm-batam.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('pembelian-bbm-batam.*') ? 'bg-blue-100 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Pembelian BBM Batam</span>
            </a>
        </div>
        @endif

        {{-- Tagihan Lain Lain Batam --}}
        @if($user && ($user->can('tagihan-service-kendaraan-batam-view') || $isAdmin))
        <div class="mx-2 mb-3">
            <a href="{{ route('tagihan-lain-lain-batam.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('tagihan-lain-lain-batam.*') ? 'bg-blue-100 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Tagihan Lain Lain Batam</span>
            </a>
        </div>
        @endif

        {{-- Pranota Tagihan Lain Lain Batam --}}
        @if($user && ($user->can('pranota-tagihan-lain-lain-batam-view') || $isAdmin))
        <div class="mx-2 mb-3">
            <a href="{{ route('pranota-tagihan-lain-lain-batam.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('pranota-tagihan-lain-lain-batam.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Pranota Tagihan Batam</span>
            </a>
        </div>
        @endif

        {{-- Gaji Supir Batam --}}
        @if($user && ($user->can('gaji-supir-batam-view') || $isAdmin))
        <div class="mx-2 mb-3">
            <a href="{{ route('gaji-supir-batam.index') }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('gaji-supir-batam.*') ? 'bg-blue-100 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Gaji Supir Batam</span>
            </a>
        </div>
        @endif
        
        {{-- Stock Amprahan --}}
        @if($user && $user->can('stock-amprahan-view'))
        <div class="mx-2 mb-3">
            <a href="{{ Route::has('stock-amprahan.index') ? route('stock-amprahan.index') : '#' }}" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('stock-amprahan.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Stock Amprahan</span>
            </a>
        </div>
        @endif
        
        {{-- Pranota Stock Amprahan --}}
        @if($user && ($user->can('pranota-stock-view') || $isAdmin))
        <div class="mx-2 mb-3">
            <a href="{{ route('pranota-stock.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 {{ Request::routeIs('pranota-stock.*') ? 'bg-indigo-50 text-indigo-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Pranota Stock Amprahan</span>
            </a>
        </div>
        @endif

        {{-- Bayar Pranota Stock --}}
        @if(Route::has('pembayaran-pranota-stock.index') && $user && ($user->can('pembayaran-pranota-stock-view') || $isAdmin))
        <div class="mx-2 mb-3">
            <a href="{{ route('pembayaran-pranota-stock.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-stock.*') ? 'bg-indigo-50 text-indigo-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Bayar Pranota Stock</span>
            </a>
        </div>
        @endif

        {{-- Bayar Pranota Ongkos Truk --}}
        @if(Route::has('pembayaran-pranota-ongkos-truk.index') && $user && ($user->can('pembayaran-pranota-ongkos-truk-view') || $isAdmin))
        <div class="mx-2 mb-3">
            <a href="{{ route('pembayaran-pranota-ongkos-truk.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-ongkos-truk.*') ? 'bg-indigo-50 text-indigo-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Bayar Pranota Ongkos Truk</span>
            </a>
        </div>
        @endif

        {{-- Report Stock Akhir --}}
        @if($user && ($user->can('stock-ban-view') || $user->can('stock-amprahan-view')))
        <div class="mx-2 mb-3">
            <a href="{{ route('report.stock-akhir.index') }}" target="_blank" class="flex items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('report.stock-akhir.index') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                <span class="text-xs font-medium">Report Stock Akhir</span>
            </a>
        </div>
        @endif
    </div>
</div>
@endif

{{-- Aktivitas Dropdown --}}
@php
    $isAktivitasRoute = Request::routeIs('orders.*') || Request::routeIs('pranota-uang-jalan.*') || Request::routeIs('pranota-uang-jalan-bongkaran.*') || Request::routeIs('uang-jalan.*') || Request::routeIs('pembayaran-pranota-uang-jalan.*') || Request::routeIs('pranota-rit.*') || Request::routeIs('pranota-uang-rit.*') || Request::routeIs('pembayaran-pranota-rit.*') || Request::routeIs('pranota-uang-rit-kenek.*') || Request::routeIs('pembayaran-pranota-rit-kenek.*') || Request::routeIs('surat-jalan.*') || Request::routeIs('surat-jalan-bongkaran.*') || Request::routeIs('uang-jalan-bongkaran.*') || Request::routeIs('pranota-lembur.*') || Request::routeIs('aktivitas-kontainer.*') || Request::routeIs('daftar-tagihan-kontainer-sewa.*') || Request::routeIs('pranota-kontainer-sewa.*') || Request::routeIs('pranota.*') || Request::routeIs('tagihan-cat.*') || Request::routeIs('pranota-cat.*') || Request::routeIs('pembayaran-pranota-cat.*') || (Request::routeIs('ob.*') || Request::routeIs('pranota-ob-muat-temas.*')) || Request::routeIs('tagihan-ob.*') || Request::routeIs('pranota-ob.*') || Request::routeIs('pembayaran-ob.*') || Request::routeIs('pembayaran-pranota-ob.*') || Request::routeIs('pembayaran-pranota-ob-antar-gudang.*') || Request::routeIs('tanda-terima.*') || Request::routeIs('tanda-terima-batam.*') || Request::routeIs('tanda-terima-tanpa-surat-jalan.*') || Request::routeIs('approval-tanda-terima.*') || Request::routeIs('approval-tanda-terima-2.*') || Request::routeIs('gate-in.*') || Request::routeIs('aktivitas-kapal.*') || Request::routeIs('pergerakan-kapal.*') || Request::routeIs('voyage.*') || Request::routeIs('jadwal-kapal.*') || Request::routeIs('status-kapal.*') || Request::routeIs('log-aktivitas-kapal.*') || Request::routeIs('monitoring-kapal.*') || Request::routeIs('naik-kapal.*') || Request::routeIs('bl.*') || Request::routeIs('prospek.*') || Request::routeIs('pembayaran-aktivitas-lain.*') || Request::routeIs('invoice-aktivitas-lain.*') || Request::routeIs('pembayaran-uang-muka.*') || Request::routeIs('realisasi-uang-muka.*') || Request::routeIs('surat-jalan-bongkaran-batam.*') || Request::routeIs('penarikan-surat-jalan-batam.*');
    $hasAktivitasPermissions = $user && (
        $user->can('order-view') ||
        $user->can('order-create') ||
        $user->can('order-update') ||
        $user->can('order-delete') ||
        $user->can('order-batam-view') ||
        $user->can('order-batam-create') ||
        $user->can('order-batam-update') ||
        $user->can('order-batam-delete') ||
        $user->can('pranota-uang-jalan-view') ||
        $user->can('pranota-uang-jalan-batam-view') ||
        $user->can('uang-jalan-view') ||
        $user->can('uang-jalan-batam-view') ||
        $user->can('pembayaran-pranota-uang-jalan-view') ||
        $user->can('pembayaran-pranota-uang-jalan-batam-view') ||
        $user->can('pranota-rit-view') ||
        $user->can('pranota-uang-rit-view') ||
        $user->can('pembayaran-pranota-rit-view') ||
        $user->can('pranota-uang-rit-kenek-view') ||
        $user->can('pembayaran-pranota-rit-kenek-view') ||
        $user->can('surat-jalan-view') ||
        $user->can('surat-jalan-create') ||
        $user->can('surat-jalan-update') ||
        $user->can('surat-jalan-delete') ||
        $user->can('surat-jalan-batam-view') ||
        $user->can('surat-jalan-batam-create') ||
        $user->can('surat-jalan-batam-update') ||
        $user->can('surat-jalan-batam-delete') ||
        $user->can('surat-jalan-tarik-kosong-batam-view') ||
        $user->can('surat-jalan-tarik-kosong-batam-create') ||
        $user->can('surat-jalan-tarik-kosong-batam-update') ||
        $user->can('surat-jalan-tarik-kosong-batam-delete') ||
        $user->can('surat-jalan-bongkaran-view') ||
        $user->can('surat-jalan-bongkaran-create') ||
        $user->can('surat-jalan-bongkaran-update') ||
        $user->can('surat-jalan-bongkaran-delete') ||
        $user->can('surat-jalan-bongkaran-batam-view') ||
        $user->can('surat-jalan-bongkaran-batam-create') ||
        $user->can('surat-jalan-bongkaran-batam-update') ||
        $user->can('surat-jalan-bongkaran-batam-delete') ||
        $user->can('uang-jalan-bongkaran-view') ||
        $user->can('uang-jalan-bongkaran-create') ||
        $user->can('uang-jalan-bongkaran-update') ||
        $user->can('uang-jalan-bongkaran-delete') ||
        $user->can('pranota-lembur-view') ||
        $user->can('aktivitas-kontainer-view') ||
        $user->can('tagihan-kontainer-sewa-index') ||
        $user->can('pranota-kontainer-sewa-view') ||
        $user->can('pranota.view') ||
        $user->can('tagihan-cat-view') ||
        $user->can('pranota-cat-view') ||
        $user->can('pembayaran-pranota-cat-view') ||
        $user->can('ob-view') ||
        $user->can('tagihan-ob-view') ||
        $user->can('pranota-ob-view') ||
        $user->can('pembayaran-ob-view') ||
        $user->can('pembayaran-pranota-ob-view') ||
        $user->can('tanda-terima-view') ||
        $user->can('tanda-terima-batam-view') ||
        $user->can('tanda-terima-update') ||
        $user->can('tanda-terima-delete') ||
        $user->can('tanda-terima-tanpa-surat-jalan-view') ||
        $user->can('tanda-terima-tanpa-surat-jalan-create') ||
        $user->can('tanda-terima-tanpa-surat-jalan-update') ||
        $user->can('tanda-terima-tanpa-surat-jalan-delete') ||
        $user->can('approval-tanda-terima-view') ||
        $user->can('approval-tanda-terima-2-view') ||
        $user->can('gate-in-view') ||
        $user->can('gate-in-create') ||
        $user->can('gate-in-update') ||
        $user->can('gate-in-delete') ||
        $user->can('aktivitas-kapal-view') ||
        $user->can('pergerakan-kapal-view') ||
        $user->can('gerak-voyage-view') ||
        $user->can('voyage-view') ||
        $user->can('jadwal-kapal-view') ||
        $user->can('status-kapal-view') ||
        $user->can('log-aktivitas-kapal-view') ||
        $user->can('monitoring-kapal-view') ||
        $user->can('prospek-edit') ||
        $user->can('prospek-view') ||
        $user->can('bl-view') ||
        $user->can('pembayaran-aktivitas-lain-view') ||
        $user->can('pembayaran-aktivitas-lain-create') ||
        $user->can('pembayaran-aktivitas-lain-update') ||
        $user->can('pembayaran-aktivitas-lain-delete') ||
        $user->can('invoice-aktivitas-lain-view') ||
        $user->can('invoice-aktivitas-lain-create') ||
        $user->can('invoice-aktivitas-lain-update') ||
        $user->can('invoice-aktivitas-lain-delete') ||
        $user->can('pembayaran-uang-muka-view') ||
        $user->can('realisasi-uang-muka-delete')
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
            $isAktivitasSupirRoute = Request::routeIs('orders.*') || Request::routeIs('pranota-uang-jalan.*') || Request::routeIs('pranota-uang-jalan-bongkaran.*') || Request::routeIs('uang-jalan.*') || Request::routeIs('pembayaran-pranota-uang-jalan.*') || Request::routeIs('pranota-rit.*') || Request::routeIs('pranota-uang-rit.*') || Request::routeIs('pembayaran-pranota-rit.*') || Request::routeIs('pranota-uang-rit-kenek.*') || Request::routeIs('pembayaran-pranota-rit-kenek.*') || Request::routeIs('surat-jalan.*') || Request::routeIs('surat-jalan-bongkaran.*') || Request::routeIs('uang-jalan-bongkaran.*') || Request::routeIs('pranota-lembur.*') || Request::routeIs('tagihan-supir-vendor.*') || Request::routeIs('invoice-tagihan-vendor.*') || Request::routeIs('pranota-invoice-vendor-supir.*') || Request::routeIs('biaya-bensin.*') || Request::is('pranota-ongkos-truk*') || Request::routeIs('pranota-biaya-bensin.*');

            $hasAktivitasSupirPermissions = $user && (
                $user->can('order-view') || $user->can('order-create') || $user->can('order-update') || $user->can('order-delete') || 
                $user->can('pranota-uang-jalan-view') || $user->can('uang-jalan-view') || $user->can('pembayaran-pranota-uang-jalan-view') || 
                $user->can('pranota-rit-view') || $user->can('pranota-uang-rit-view') || $user->can('pembayaran-pranota-rit-view') || 
                $user->can('pranota-uang-rit-kenek-view') || $user->can('pembayaran-pranota-rit-kenek-view') ||
                $user->can('surat-jalan-view') || $user->can('surat-jalan-create') || $user->can('surat-jalan-update') || $user->can('surat-jalan-delete') || 
                $user->can('surat-jalan-bongkaran-view') || $user->can('surat-jalan-bongkaran-create') || $user->can('surat-jalan-bongkaran-update') || $user->can('surat-jalan-bongkaran-delete') || 
                $user->can('uang-jalan-bongkaran-view') || $user->can('uang-jalan-bongkaran-create') || $user->can('uang-jalan-bongkaran-update') || $user->can('uang-jalan-bongkaran-delete') || 
                $user->can('pranota-lembur-view') || $user->can('tagihan-supir-vendor-view') || $user->can('invoice-tagihan-vendor-view') || $user->can('pranota-invoice-vendor-supir-view') || 
                true
            );
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
                    $isMemoRoute = Request::is('pranota-ongkos-truk*');
                    $hasMemoPermissions = $user && Route::has('pranota-ongkos-truk.index');
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
                        {{-- Pranota Ongkos Truk --}}
                        <a href="{{ route('pranota-ongkos-truk.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::is('pranota-ongkos-truk*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                            <span class="text-xs">Pranota Ongkos Truk</span>
                        </a>
                    </div>
                </div>
                @endif

                {{-- Rit Sub-Dropdown --}}
                @php
                    $isRitRoute = Request::routeIs('pranota-rit.*') || 
                                 Request::routeIs('pranota-uang-rit.*') || 
                                 Request::routeIs('pembayaran-pranota-rit.*') || 
                                 Request::routeIs('pranota-uang-rit-kenek.*') || 
                                 Request::routeIs('pembayaran-pranota-rit-kenek.*') ||
                                 Request::routeIs('pranota-uang-rit-batam.*') ||
                                 Request::routeIs('saldo-utang-karyawan.*') ||
                                 Request::routeIs('permohonan-pinjaman-karyawan.*') ||
                                 Request::routeIs('saldo-utang-supir.*');
                    
                    $hasRitPermissions = $user && (
                        $user->can('pranota-rit-view') || 
                        $user->can('pranota-uang-rit-view') || 
                        $user->can('pembayaran-pranota-rit-view') || 
                        $user->can('pranota-uang-rit-kenek-view') || 
                        $user->can('pembayaran-pranota-rit-kenek-view') ||
                        $user->can('pranota-uang-rit-batam-view') ||
                        $user->can('saldo-utang-karyawan-view') ||
                        $user->can('permohonan-pinjaman-karyawan-view') ||
                        $isAdmin
                    );

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
                            <a href="{{ route('pranota-rit.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('pranota-rit.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pranota Rit</span>
                            </a>
                        @endif

                        @if($user && $user->can('pranota-uang-rit-view'))
                            <a href="{{ route('pranota-uang-rit.index', ['start_date' => \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'), 'end_date' => \Carbon\Carbon::now()->format('Y-m-d')]) }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('pranota-uang-rit.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pranota Uang Rit</span>
                            </a>
                        @endif

                        @if($user && ($user->can('saldo-utang-karyawan-view') || $isAdmin))
                            <a href="{{ route('saldo-utang-karyawan.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ (Request::routeIs('saldo-utang-karyawan.*') || Request::routeIs('saldo-utang-supir.*')) ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Saldo Utang Karyawan</span>
                            </a>
                        @endif

                        @if($user && ($user->can('permohonan-pinjaman-karyawan-view') || $isAdmin))
                            <a href="{{ route('permohonan-pinjaman-karyawan.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-amber-50 hover:text-amber-700 transition-all duration-200 {{ Request::routeIs('permohonan-pinjaman-karyawan.*') ? 'bg-amber-50 text-amber-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Permohonan Pinjaman Karyawan</span>
                            </a>
                        @endif

                        {{-- Pranota Uang Rit Batam --}}
                        @if($user && ($user->can('pranota-uang-rit-batam-view') || $isAdmin))
                            <a href="{{ route('pranota-uang-rit-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('pranota-uang-rit-batam.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pranota Uang Rit Batam</span>
                            </a>
                        @endif


                        {{-- Pembayaran Pranota Rit --}}
                        @if($user && $user->can('pembayaran-pranota-rit-view'))
                            <a href="{{ route('pembayaran-pranota-rit.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-rit.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <div class="w-1.5 h-1.5 rounded-full bg-purple-400 mr-2 flex-shrink-0"></div>
                                <span class="text-xs">Bayar Pranota Rit</span>
                            </a>
                        @endif

                        {{-- Pranota Uang Rit Kenek --}}
                        @if($user && $user->can('pranota-uang-rit-kenek-view'))
                            <a href="{{ route('pranota-uang-rit-kenek.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('pranota-uang-rit-kenek.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pranota Uang Rit Kenek</span>
                            </a>
                        @endif

                        {{-- Pembayaran Pranota Rit Kenek --}}
                        @if($user && $user->can('pembayaran-pranota-rit-kenek-view'))
                            <a href="{{ route('pembayaran-pranota-rit-kenek.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-rit-kenek.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-400 mr-2 flex-shrink-0"></div>
                                <span class="text-xs">Bayar Pranota Rit Kenek</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Pranota Uang Kenek --}}
                @if($user && $user->can('pranota-uang-kenek-view'))
                    <a href="{{ route('pranota-uang-kenek.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('pranota-uang-kenek.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pranota Uang Kenek</span>
                    </a>
                @endif

                {{-- Surat Jalan Sub-Dropdown --}}
                @php
                    $isSuratJalanRoute = Request::routeIs('orders.*') || Request::routeIs('orders-batam.*') || Request::routeIs('surat-jalan.*') || Request::routeIs('surat-jalan-batam.*') || Request::routeIs('surat-jalan-tarik-kosong-batam.*') || Request::routeIs('approval-order.*') || Request::routeIs('uang-jalan.*') || Request::routeIs('uang-jalan-batam.*') || Request::routeIs('pranota-uang-jalan.*') || Request::routeIs('pranota-uang-jalan-batam.*') || Request::routeIs('pembayaran-pranota-uang-jalan.*') || Request::routeIs('pembayaran-pranota-uang-jalan-batam.*') || Request::routeIs('permohonan-amprahan.*') || Request::routeIs('approval-permohonan-amprahan.*');
                    $hasSuratJalanPermissions = $user && (($user->can('order-view') || $user->can('order-create') || $user->can('order-update') || $user->can('order-delete')) || ($user->can('order-batam-view') || $user->can('order-batam-create') || $user->can('order-batam-update') || $user->can('order-batam-delete')) || ($user->can('surat-jalan-view') || $user->can('surat-jalan-create') || $user->can('surat-jalan-update') || $user->can('surat-jalan-delete')) || ($user->can('surat-jalan-batam-view') || $user->can('surat-jalan-batam-create') || $user->can('surat-jalan-batam-update') || $user->can('surat-jalan-batam-delete')) || ($user->can('surat-jalan-tarik-kosong-batam-view') || $user->can('surat-jalan-tarik-kosong-batam-create') || $user->can('surat-jalan-tarik-kosong-batam-update') || $user->can('surat-jalan-tarik-kosong-batam-delete')) || $user->can('approval-order-view') || $user->can('uang-jalan-view') || $user->can('uang-jalan-create') || $user->can('uang-jalan-update') || $user->can('uang-jalan-delete') || ($user->can('uang-jalan-batam-view') || $user->can('uang-jalan-batam-create') || $user->can('uang-jalan-batam-update') || $user->can('uang-jalan-batam-delete')) || $user->can('pranota-uang-jalan-view') || $user->can('pranota-uang-jalan-batam-view') || $user->can('pembayaran-pranota-uang-jalan-view') || $user->can('pembayaran-pranota-uang-jalan-batam-view') || $user->can('permohonan-amprahan-view') || $user->can('permohonan-amprahan-approve') || $isAdmin);
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
                        @if($user && ($user->can('order-view') || $user->can('order-create') || $user->can('order-update') || $user->can('order-delete')))
                            <a href="{{ route('orders.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('orders.index') || Request::routeIs('orders.show') || Request::routeIs('orders.edit') || Request::routeIs('orders.create') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                
                                <span class="text-xs">Pesanan Pengambilan Barang</span>
                            </a>
                        @endif
                            
                        @if($user && ($user->can('order-batam-view') || $user->can('order-batam-create') || $user->can('order-batam-update') || $user->can('order-batam-delete')))
                            <a href="{{ route('orders-batam.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('orders-batam.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                
                                <span class="text-xs">Orders Batam</span>
                            </a>
                        @endif

                        {{-- Approval Order --}}
                        @if($user && $user->can('approval-order-view'))
                            <a href="{{ route('approval-order.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('approval-order.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                
                                <span class="text-xs">Approval Order</span>
                            </a>
                        @endif

                        {{-- Surat Jalan --}}
                        @if($user && ($user->can('surat-jalan-view') || $user->can('surat-jalan-create') || $user->can('surat-jalan-update') || $user->can('surat-jalan-delete')))
                            <a href="{{ route('surat-jalan.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('surat-jalan.*') && !Request::routeIs('surat-jalan-batam.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                <span class="text-xs">Surat Jalan</span>
                            </a>
                        @endif

                        {{-- Surat Jalan Batam --}}
                        @if($user && ($user->can('surat-jalan-batam-view') || $user->can('surat-jalan-batam-create') || $user->can('surat-jalan-batam-update') || $user->can('surat-jalan-batam-delete')))
                            <a href="{{ route('surat-jalan-batam.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('surat-jalan-batam.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                <span class="text-xs">Surat Jalan Batam</span>
                            </a>
                        @endif

                        {{-- Surat Jalan Tarik Kosong Batam --}}
                        @if($user && ($user->can('surat-jalan-tarik-kosong-batam-view') || $isAdmin))
                            <a href="{{ route('surat-jalan-tarik-kosong-batam.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('surat-jalan-tarik-kosong-batam.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                <span class="text-xs font-bold text-blue-600">Surat Jalan Tarik Kosong Batam</span>
                            </a>
                        @endif

                        {{-- Uang Jalan Batam --}}
                        @if($user && ($user->can('uang-jalan-batam-view') || $user->can('uang-jalan-batam-create') || $user->can('uang-jalan-batam-update') || $user->can('uang-jalan-batam-delete')))
                            <a href="{{ route('uang-jalan-batam.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('uang-jalan-batam.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                <span class="text-xs">Uang Jalan Batam</span>
                            </a>
                        @endif

                        {{-- Pembatalan Surat Jalan --}}
                        @if($user && $user->can('surat-jalan-update'))
                            <a href="{{ route('pembatalan-surat-jalan.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('pembatalan-surat-jalan.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                <span class="text-xs">Pembatalan Surat Jalan</span>
                            </a>
                        @endif

                        {{-- Uang Jalan --}}
                        @if($user && ($user->can('uang-jalan-view') || $user->can('uang-jalan-create') || $user->can('uang-jalan-update') || $user->can('uang-jalan-delete')))
                            <a href="{{ route('uang-jalan.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('uang-jalan.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                
                                <span class="text-xs">Uang Jalan</span>
                            </a>
                        @endif

                        {{-- Pranota Uang Jalan --}}
                        @if($user && $user->can('pranota-uang-jalan-view'))
                            <a href="{{ route('pranota-uang-jalan.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('pranota-uang-jalan.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                
                                <span class="text-xs">Pranota Uang Jalan</span>
                            </a>
                        @endif

                        {{-- Pranota Uang Jalan Batam --}}
                        @if($user && $user->can('pranota-uang-jalan-batam-view'))
                            <a href="{{ route('pranota-uang-jalan-batam.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('pranota-uang-jalan-batam.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                
                                <span class="text-xs">Pranota Uang Jalan Batam</span>
                            </a>
                        @endif

                        {{-- Bayar Pranota Uang Jalan --}}
                        @if(Route::has('pembayaran-pranota-uang-jalan.index') && $user && $user->can('pembayaran-pranota-uang-jalan-view'))
                            <a href="{{ route('pembayaran-pranota-uang-jalan.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-uang-jalan.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                
                                <span class="text-xs">Bayar Pranota Uang Jalan</span>
                            </a>
                        @endif

                        {{-- Permintaan Amprahan --}}
                        @if(Route::has('permohonan-amprahan.index') && $user && ($user->can('permohonan-amprahan-view') || $user->is_admin))
                            <a href="{{ route('permohonan-amprahan.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('permohonan-amprahan.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600' }}">
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-400 mr-2 flex-shrink-0"></div>
                                <span>Permintaan Amprahan (PWA)</span>
                            </a>
                        @endif

                        {{-- Approval Permintaan Amprahan --}}
                        @if(Route::has('approval-permohonan-amprahan.index') && $user && ($user->can('permohonan-amprahan-approve') || $user->is_admin))
                            <a href="{{ route('approval-permohonan-amprahan.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('approval-permohonan-amprahan.*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600' }}">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-400 mr-2 flex-shrink-0"></div>
                                <span>Approval Permintaan Amprahan</span>
                            </a>
                        @endif

                        {{-- Bayar Pranota Uang Jalan Batam --}}
                        @if(Route::has('pembayaran-pranota-uang-jalan-batam.index') && $user && ($user->can('pembayaran-pranota-uang-jalan-batam-view') || $user->is_admin))
                            <a href="{{ route('pembayaran-pranota-uang-jalan-batam.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-uang-jalan-batam.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                <span class="text-xs">Bayar Pranota Batam</span>
                            </a>
                        @endif

                        @if(Route::has('pembayaran-pranota-uang-jalan-bongkaran.index') && $user && $user->can('pembayaran-pranota-uang-jalan-bongkaran-view'))
                            <a href="{{ route('pembayaran-pranota-uang-jalan-bongkaran.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-uang-jalan-bongkaran.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600' }}">
                                <div class="w-1.5 h-1.5 rounded-full bg-teal-400 mr-2 flex-shrink-0"></div>
                                <span>Pemb. Pranota UJ Bongkaran</span>
                            </a>
                        @endif

                        @if(Route::has('pembayaran-pranota-lembur.index') && $user && ($user->can('pembayaran-pranota-lembur-view') || $user->is_admin))
                            <a href="{{ route('pembayaran-pranota-lembur.index') }}" target="_blank" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-lembur.*') ? 'bg-orange-50 text-orange-700 font-medium' : 'text-gray-600' }}">
                                <div class="w-1.5 h-1.5 rounded-full bg-orange-400 mr-2 flex-shrink-0"></div>
                                <span>Pembayaran Pranota Lembur</span>
                            </a>
                        @endif
                    </div> 
                </div>
                @endif

                @if($user && ($user->can('gerak-voyage-view') || $user->is_admin))
                {{-- Tanggal Gerak Voyage --}}
                <div class="mx-1 mb-2">
                    <a href="{{ route('gerak-voyage.index') }}" class="flex items-center py-2 px-3 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('gerak-voyage.index', 'gerak-voyage.create') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-semibold">Tanggal Gerak Voyage</span>
                    </a>
                </div>
                @endif

                {{-- Surat Jalan Bongkaran Sub-Dropdown --}}
                @php
                    $isSuratJalanBongkaranRoute = Request::routeIs('surat-jalan-bongkaran.*') || Request::routeIs('uang-jalan-bongkaran.*') || Request::routeIs('pranota-uang-jalan-bongkaran.*') || Request::routeIs('tanda-terima-bongkaran.*') || Request::routeIs('surat-jalan-bongkaran-batam.*') || Request::routeIs('penarikan-surat-jalan-batam.*') || Request::routeIs('tagihan-lolo-batam.*') || Request::routeIs('pranota-lolo-batam.*');
                    $hasSuratJalanBongkaranPermissions = $user && ($user->can('surat-jalan-bongkaran-view') || $user->can('surat-jalan-bongkaran-outstanding') || $user->can('surat-jalan-bongkaran-create') || $user->can('surat-jalan-bongkaran-update') || $user->can('surat-jalan-bongkaran-delete') || $user->can('surat-jalan-bongkaran-batam-view') || $user->can('surat-jalan-bongkaran-batam-create') || $user->can('surat-jalan-bongkaran-batam-update') || $user->can('surat-jalan-bongkaran-batam-delete') || $user->can('tagihan-lolo-batam-view') || $user->can('tagihan-lolo-batam-create') || $user->can('pranota-lolo-batam-view') || $user->can('pranota-lolo-batam-create') || $user->can('uang-jalan-bongkaran-view') || $user->can('uang-jalan-bongkaran-create') || $user->can('uang-jalan-bongkaran-update') || $user->can('uang-jalan-bongkaran-delete') || $user->can('tanda-terima-bongkaran-view') || $user->can('tanda-terima-bongkaran-create') || $user->can('tanda-terima-bongkaran-update') || $user->can('tanda-terima-bongkaran-delete') || $user->can('tanda-terima-bongkaran-batam-view') || $user->can('tanda-terima-bongkaran-batam-create') || $user->can('tanda-terima-bongkaran-batam-update') || $user->can('tanda-terima-bongkaran-batam-delete'));
                @endphp

                @if($hasSuratJalanBongkaranPermissions)
                <div class="mx-1 mb-2">
                    <button id="surat-jalan-bongkaran-menu-toggle" class="w-full flex justify-between items-center py-1.5 px-2 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 group {{ $isSuratJalanBongkaranRoute ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-medium">Surat Jalan Bongkaran</span>
                        <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isSuratJalanBongkaranRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="surat-jalan-bongkaran-menu-content" class="dropdown-content ml-3 mt-1 space-y-1" @if($isSuratJalanBongkaranRoute) style="display: block;" @endif>
                        {{-- Surat Jalan Bongkaran --}}
                        @if($user && $user->can('surat-jalan-bongkaran-view'))
                            <a href="{{ route('surat-jalan-bongkaran.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ (Request::routeIs('surat-jalan-bongkaran.*') && !Request::routeIs('surat-jalan-bongkaran.outstanding')) ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                
                                <span class="text-xs">Surat Jalan Bongkaran</span>
                            </a>
                        @endif

                        {{-- Outstanding Surat Jalan Bongkaran --}}
                        @if($user && $user->can('surat-jalan-bongkaran-outstanding'))
                            <a href="{{ route('surat-jalan-bongkaran.outstanding') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('surat-jalan-bongkaran.outstanding') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs font-semibold text-amber-600">Outstanding Bongkaran</span>
                            </a>
                        @endif

                        {{-- Removed 'Tambah Surat Jalan Bongkaran' from the dropdown as requested --}}

                        @if($user && ($user->can('tanda-terima-bongkaran-view') || $user->can('tanda-terima-bongkaran-create') || $user->can('tanda-terima-bongkaran-update') || $user->can('tanda-terima-bongkaran-delete')))
                            <a href="{{ route('tanda-terima-bongkaran.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('tanda-terima-bongkaran.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Tanda Terima Surat Jalan Bongkaran</span>
                            </a>
                        @endif

                        {{-- Tanda Terima Surat Jalan Bongkaran Batam --}}
                        {{-- Kept commented out as it is unified under Tanda Terima Batam
                        @if($user && ($user->can('tanda-terima-bongkaran-batam-view') || $user->can('tanda-terima-bongkaran-batam-create')))
                            <a href="{{ route('tanda-terima-bongkaran-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('tanda-terima-bongkaran-batam.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Tanda Terima Surat Jalan Bongkaran Batam</span>
                            </a>
                        @endif
                        --}}


                        {{-- Surat Jalan Bongkaran Batam --}}
                        @if($user && ($user->can('surat-jalan-bongkaran-batam-view') || $user->can('surat-jalan-bongkaran-batam-create')))
                            <a href="{{ route('surat-jalan-bongkaran-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('surat-jalan-bongkaran-batam.index') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Surat Jalan Bongkaran Batam</span>
                            </a>
                        @endif

                        {{-- Penarikan Surat Jalan Batam --}}
                        @if($user && ($user->can('surat-jalan-bongkaran-batam-view') || $user->can('surat-jalan-bongkaran-batam-create')))
                            <a href="{{ route('penarikan-surat-jalan-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('penarikan-surat-jalan-batam.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Penarikan Surat Jalan Batam</span>
                            </a>
                        @endif

                        {{-- Tagihan LOLO Batam --}}
                        @if($user && ($user->can('tagihan-lolo-batam-view') || $user->can('tagihan-lolo-batam-create')))
                            <a href="{{ route('tagihan-lolo-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('tagihan-lolo-batam.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Tagihan LOLO Batam</span>
                            </a>
                        @endif

                        {{-- Pranota LOLO Batam --}}
                        @if($user && ($user->can('pranota-lolo-batam-view') || $user->can('tagihan-lolo-batam-view') || $isAdmin))
                            <a href="{{ route('pranota-lolo-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('pranota-lolo-batam.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs font-semibold text-teal-800">Pranota LOLO Batam</span>
                            </a>
                        @endif

                        {{-- Report Kerja Supir Batam --}}
                        @if($user && $user->can('report-kerja-supir-batam-view'))
                        <a href="{{ route('report-kerja-supir-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 {{ Request::routeIs('report-kerja-supir-batam.*') ? 'bg-indigo-50 text-indigo-700 font-medium shadow-sm' : 'text-gray-600' }}">
                            <span class="text-xs font-semibold">Report Kerja Supir Batam</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Pranota Lembur --}}
                @if($user && $user->can('pranota-lembur-view'))
                    <a href="{{ route('pranota-lembur.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('pranota-lembur.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pranota Lembur</span>
                    </a>
                @endif

                {{-- Tagihan Supir Vendor --}}
                @if($user && $user->can('tagihan-supir-vendor-view'))
                    <a href="{{ Route::has('tagihan-supir-vendor.index') ? route('tagihan-supir-vendor.index') : '#' }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 {{ Request::routeIs('tagihan-supir-vendor.*') ? 'bg-indigo-50 text-indigo-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Tagihan Supir Vendor</span>
                    </a>
                @endif

                {{-- Invoice Tagihan Vendor --}}
                @if($user && $user->can('invoice-tagihan-vendor-view'))
                    <a href="{{ Route::has('invoice-tagihan-vendor.index') ? route('invoice-tagihan-vendor.index') : '#' }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('invoice-tagihan-vendor.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Invoice Tagihan Vendor</span>
                    </a>
                @endif

                {{-- Pranota Invoice Vendor Supir --}}
                @if($user && $user->can('pranota-invoice-vendor-supir-view'))
                    <a href="{{ Route::has('pranota-invoice-vendor-supir.index') ? route('pranota-invoice-vendor-supir.index') : '#' }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-rose-50 hover:text-rose-700 transition-all duration-200 {{ Request::routeIs('pranota-invoice-vendor-supir.*') ? 'bg-rose-50 text-rose-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pranota Invoice Vendor Supir</span>
                    </a>
                @endif

                {{-- Pembayaran Pranota Invoice Vendor Supir --}}
                @if($user && $user->can('pembayaran-pranota-invoice-vendor-supir-view'))
                    <a href="{{ Route::has('pembayaran-pranota-invoice-vendor-supir.index') ? route('pembayaran-pranota-invoice-vendor-supir.index') : '#' }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-invoice-vendor-supir.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pembayaran Pranota Vendor Supir</span>
                    </a>
                @endif

                {{-- Biaya Bensin --}}
                @if($user && ($user->can('biaya-bensin-view') || $user->is_admin))
                    <a href="{{ route('biaya-bensin.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-amber-50 hover:text-amber-700 transition-all duration-200 {{ Request::routeIs('biaya-bensin.*') && !Request::routeIs('biaya-bensin.approval') ? 'bg-amber-50 text-amber-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Biaya Bensin</span>
                    </a>
                @endif

                {{-- Approval Isi Bensin --}}
                @if($user && ($user->can('biaya-bensin-update') || $user->is_admin))
                    <a href="{{ route('biaya-bensin.approval') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 {{ Request::routeIs('biaya-bensin.approval') ? 'bg-indigo-50 text-indigo-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Approval Isi Bensin</span>
                        @php
                            $pendingBensinCount = \App\Models\BiayaBensin::where('status', 'pending')->count();
                        @endphp
                        @if($pendingBensinCount > 0)
                            <span class="ml-auto bg-red-500 text-white rounded-full px-1.5 py-0.5 text-[10px] font-bold">{{ $pendingBensinCount }}</span>
                        @endif
                    </a>
                @endif

                {{-- Pranota Biaya Bensin --}}
                @if($user && ($user->can('pranota-biaya-bensin-view') || $user->is_admin))
                    <a href="{{ route('pranota-biaya-bensin.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('pranota-biaya-bensin.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pranota Biaya Bensin</span>
                    </a>
                @endif


            </div>
        </div>
        @endif

        {{-- Aktivitas Kontainer Sub-Dropdown --}}
        @php
            $isAktivitasKontainerRoute = Request::routeIs('daftar-tagihan-kontainer-sewa.*') || Request::routeIs('daftar-tagihan-kontainer-sewa-2.*') || Request::routeIs('pranota-kontainer-sewa.*') || Request::routeIs('pranota.*') || Request::routeIs('tagihan-cat.*') || Request::routeIs('pranota-cat.*') || Request::routeIs('pembayaran-pranota-cat.*');
            $hasAktivitasKontainerPermissions = $user && ($user->can('tagihan-kontainer-sewa-index') || $user->can('pranota-kontainer-sewa-view') || $user->can('pranota.view') || $user->can('tagihan-cat-view') || $user->can('pranota-cat-view') || $user->can('pembayaran-pranota-cat-view'));
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
                            <a href="{{ route('tagihan-cat.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-rose-50 hover:text-rose-700 transition-all duration-200 {{ Request::routeIs('tagihan-cat.*') ? 'bg-rose-50 text-rose-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Daftar Tagihan CAT</span>
                            </a>
                        @endif

                        {{-- Pranota Tagihan CAT --}}
                        @if($user && $user->can('pranota-cat-view'))
                            <a href="{{ route('pranota-cat.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-rose-50 hover:text-rose-700 transition-all duration-200 {{ Request::routeIs('pranota-cat.*') ? 'bg-rose-50 text-rose-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                <span class="text-xs">Pranota Tagihan CAT</span>
                            </a>
                        @endif

                        {{-- Pembayaran Pranota CAT Kontainer --}}
                        @if(Route::has('pembayaran-pranota-cat.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-cat-view')))
                            <a href="{{ route('pembayaran-pranota-cat.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-rose-50 hover:text-rose-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-cat.*') ? 'bg-rose-50 text-rose-700 font-medium shadow-sm' : 'text-gray-600' }}">
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
            $isAktivitasKapalRoute = Request::routeIs('aktivitas-kapal.*') || Request::routeIs('pergerakan-kapal.*') || Request::routeIs('voyage.*') || Request::routeIs('jadwal-kapal.*') || Request::routeIs('status-kapal.*') || Request::routeIs('log-aktivitas-kapal.*') || Request::routeIs('monitoring-kapal.*') || Request::routeIs('naik-kapal.*') || Request::routeIs('bl.*') || Request::routeIs('prospek.*') || Request::routeIs('tagihan-ob.*') || Request::routeIs('pranota-ob.*') || Request::routeIs('pembayaran-pranota-ob.*') || Request::routeIs('pembayaran-pranota-ob-antar-gudang.*') || Request::routeIs('pranota-ob-antar-gudang.*') || Request::routeIs('pembayaran-ob.*') || (Request::routeIs('ob.*') || Request::routeIs('pranota-ob-muat-temas.*')) || Request::routeIs('ob-antar-gudang.*');
            $hasAktivitasKapalPermissions = $user && ($user->can('aktivitas-kapal-view') || $user->can('pergerakan-kapal-view') || $user->can('voyage-view') || $user->can('jadwal-kapal-view') || $user->can('status-kapal-view') || $user->can('log-aktivitas-kapal-view') || $user->can('monitoring-kapal-view') || $user->can('prospek-edit') || $user->can('prospek-view') || $user->can('bl-view') || $user->can('ob-view') || $user->can('ob-antar-gudang-view') || $user->can('tagihan-ob-view') || $user->can('pranota-ob-view') || $user->can('pranota-ob-antar-gudang-view') || $user->can('pembayaran-pranota-ob-view') || $user->can('pembayaran-ob-view'));
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
                {{-- Gudang Kontainer --}}
                @if($user && $user->can('prospek-view'))
                    <a href="{{ route('prospek.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('prospek.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Gudang Kontainer</span>
                    </a>
                @endif

                {{-- Prospek --}}
                @if($user && $user->can('prospek-edit'))
                    <a href="{{ route('naik-kapal.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('naik-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Prospek</span>
                    </a>
                @endif

                {{-- Stowage Plan --}}
                @if($user && $user->can('stowage-plan-view'))
                    <a href="{{ route('stowage-plan.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('stowage-plan.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Stowage Plan</span>
                    </a>
                @endif

                {{-- Pembayaran DP OB --}}
                @if($user && $user->can('pembayaran-ob-view'))
                    <a href="{{ route('pembayaran-ob.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('pembayaran-ob.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pembayaran DP OB</span>
                    </a>
                @endif

                {{-- OB --}}
                @if($user && ($user->can('ob-view') || $user->can('tagihan-ob-view') || $user->can('pranota-ob-view') || $user->can('pembayaran-ob-view') || $user->can('pembayaran-pranota-ob-view')))
                    <a href="{{ route('ob.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ (Request::routeIs('ob.*') || Request::routeIs('pranota-ob-muat-temas.*')) && !Request::routeIs('tagihan-ob.*') && !Request::routeIs('pranota-ob.*') && !Request::routeIs('pembayaran-ob.*') && !Request::routeIs('pembayaran-pranota-ob.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">OB</span>
                    </a>
                @endif

                {{-- OB Antar Gudang --}}
                @if($user && ($user->can('ob-antar-gudang-view')))
                    <a href="{{ route('ob-antar-gudang.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('ob-antar-gudang.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">OB Antar Gudang</span>
                    </a>
                @endif

                {{-- Tagihan OB Antar Gudang --}}
                @if($user && ($user->can('tagihan-ob-antar-gudang-view') || $user->can('tagihan-ob-view')))
                    <a href="{{ route('tagihan-ob-antar-gudang.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-rose-50 hover:text-rose-700 transition-all duration-200 {{ Request::routeIs('tagihan-ob-antar-gudang.*') ? 'bg-rose-50 text-rose-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-medium text-blue-600">Tagihan OB Antar Gudang</span>
                    </a>
                @endif

                {{-- Pranota OB Antar Gudang --}}
                @if($user && ($user->can('pranota-ob-antar-gudang-view') || $user->can('pranota-ob-view')))
                    <a href="{{ route('pranota-ob-antar-gudang.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-rose-50 hover:text-rose-700 transition-all duration-200 {{ Request::routeIs('pranota-ob-antar-gudang.*') && !Request::routeIs('pembayaran-pranota-ob-antar-gudang.*') ? 'bg-rose-50 text-rose-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-medium text-teal-600">Pranota OB Antar Gudang</span>
                    </a>
                @endif

                {{-- Pembayaran Pranota OB Antar Gudang --}}
                @if($user && ($user->can('pranota-ob-antar-gudang-view') || $user->can('pranota-ob-view')))
                    <a href="{{ route('pembayaran-pranota-ob-antar-gudang.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-rose-50 hover:text-rose-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-ob-antar-gudang.*') ? 'bg-rose-50 text-rose-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-medium text-emerald-600">Pembayaran Pranota OB Antar Gudang</span>
                    </a>
                @endif

                {{-- Pranota OB --}}
                @if($user && $user->can('pranota-ob-view'))
                    <a href="{{ route('pranota-ob.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('pranota-ob.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pranota OB</span>
                    </a>
                @endif

                {{-- Pembayaran Pranota OB --}}
                @if(Route::has('pembayaran-pranota-ob.index') && ($isAdmin || auth()->user()->can('pembayaran-pranota-ob-view')))
                    <a href="{{ route('pembayaran-pranota-ob.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('pembayaran-pranota-ob.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pembayaran Pranota OB</span>
                    </a>
                @endif

                {{-- BL (Bill of Lading) --}}
                @if($user && $user->can('bl-view'))
                    <a href="{{ route('bl.select') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('bl.*') && !Request::routeIs('bl.rekap-bongkaran*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">BL (Bill of Lading)</span>
                    </a>
                @endif

                {{-- Pergerakan Kapal --}}
                @if($user && $user->can('pergerakan-kapal-view'))
                    <a href="{{ route('pergerakan-kapal.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('pergerakan-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pergerakan Kapal</span>
                    </a>
                @endif

                {{-- Daftar Voyage --}}
                @if($user && $user->can('voyage-view') && \Illuminate\Support\Facades\Route::has('voyage.index'))
                    <a href="{{ route('voyage.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('voyage.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Daftar Voyage</span>
                    </a>
                @endif

                {{-- Jadwal Kapal --}}
                @if($user && $user->can('jadwal-kapal-view') && \Illuminate\Support\Facades\Route::has('jadwal-kapal.index'))
                    <a href="{{ route('jadwal-kapal.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('jadwal-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Jadwal Kapal</span>
                    </a>
                @endif

                {{-- Status Kapal --}}
                @if($user && $user->can('status-kapal-view') && \Illuminate\Support\Facades\Route::has('status-kapal.index'))
                    <a href="{{ route('status-kapal.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('status-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Status Kapal</span>
                    </a>
                @endif

                {{-- Log Aktivitas --}}
                @if($user && $user->can('log-aktivitas-kapal-view') && \Illuminate\Support\Facades\Route::has('log-aktivitas-kapal.index'))
                    <a href="{{ route('log-aktivitas-kapal.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('log-aktivitas-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Log Aktivitas</span>
                    </a>
                @endif

                {{-- Monitoring Kapal --}}
                @if($user && $user->can('monitoring-kapal-view') && \Illuminate\Support\Facades\Route::has('monitoring-kapal.index'))
                    <a href="{{ route('monitoring-kapal.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('monitoring-kapal.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Monitoring Kapal</span>
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Aktivitas Pelabuhan Sub-Dropdown --}}
        @php
            $isAktivitasPelabuhanRoute = Request::routeIs('tanda-terima.*') || Request::routeIs('tanda-terima-batam.*') || Request::routeIs('tanda-terima-surat-jalan-tarik-kosong-batam.*') || Request::routeIs('tanda-terima-tanpa-surat-jalan.*') || Request::routeIs('tanda-terima-tanpa-surat-jalan-batam.*') || Request::routeIs('approval-tanda-terima.*') || Request::routeIs('approval-tanda-terima-2.*') || Request::routeIs('gate-in.*') || Request::routeIs('kontainer-perjalanan.*') || Request::routeIs('prospek-batam.*') || Request::routeIs('dokumen-tanda-terima.*') || Request::routeIs('tagihan-pelindo.*') || Request::routeIs('rincian-kontainer-pelindo.*') || Request::routeIs('tagihan-lolo-batam.*');
            $hasAktivitasPelabuhanPermissions = $user && (($user->can('tanda-terima-view') || $user->can('tanda-terima-batam-view') || $user->can('tanda-terima-surat-jalan-tarik-kosong-batam-view') || $user->can('tanda-terima-update') || $user->can('tanda-terima-delete') || $user->can('tanda-terima-tanpa-surat-jalan-view') || $user->can('tanda-terima-tanpa-surat-jalan-create') || $user->can('tanda-terima-tanpa-surat-jalan-update') || $user->can('tanda-terima-tanpa-surat-jalan-delete') || $user->can('tanda-terima-tanpa-surat-jalan-batam-view') || $user->can('tanda-terima-tanpa-surat-jalan-batam-create') || $user->can('tanda-terima-tanpa-surat-jalan-batam-update') || $user->can('tanda-terima-tanpa-surat-jalan-batam-delete') || $user->can('approval-tanda-terima-view') || $user->can('approval-tanda-terima-2-view') || $user->can('gate-in-view') || $user->can('gate-in-create') || $user->can('gate-in-update') || $user->can('gate-in-delete') || $user->can('checkpoint-kontainer-keluar-view') || $user->can('checkpoint-kontainer-keluar-create') || $user->can('checkpoint-kontainer-keluar-delete') || $user->can('tagihan-pelindo-view') || $user->can('tagihan-lolo-batam-view')) || $user->can('prospek-batam-view') || $user->can('prospek-batam-edit') || $user->can('dokumen-tanda-terima-view'));
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
                {{-- Tanda Terima --}}
                @if($user && ($user->can('tanda-terima-view') || $user->can('tanda-terima-update') || $user->can('tanda-terima-delete')))
                    <a href="{{ route('tanda-terima.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('tanda-terima.*') && !Request::routeIs('tanda-terima-batam.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Tanda Terima</span>
                    </a>
                @endif

                {{-- Tanda Terima Batam --}}
                @if($user && ($user->can('tanda-terima-batam-view') || $isAdmin))
                    <a href="{{ route('tanda-terima-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('tanda-terima-batam.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Tanda Terima Batam</span>
                    </a>
                @endif
                
                {{-- Tanda Terima Surat Jalan Tarik Kosong Batam --}}
                {{-- Kept commented out as it is unified under Tanda Terima Batam
                @if($user && ($user->can('tanda-terima-surat-jalan-tarik-kosong-batam-view') || $isAdmin))
                    <a href="{{ route('tanda-terima-surat-jalan-tarik-kosong-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('tanda-terima-surat-jalan-tarik-kosong-batam.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-bold text-blue-600">Tanda Terima SJ Tarik Kosong Batam</span>
                    </a>
                @endif
                --}}

                {{-- Langsir Batam --}}
                @can('langsir-batam-view')
                    <a href="{{ route('langsir-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('langsir-batam.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Langsir Kontainer Batam</span>
                    </a>
                @endcan

                {{-- Tagihan LOLO Batam --}}
                @if($user && ($user->can('tagihan-lolo-batam-view') || $isAdmin))
                    <a href="{{ route('tagihan-lolo-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('tagihan-lolo-batam.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-semibold text-emerald-700">Tagihan LOLO Batam</span>
                    </a>
                @endif


                {{-- Prospek Batam --}}
                @if($user && ($user->can('prospek-batam-view') || $user->can('prospek-batam-edit') || $isAdmin))
                    <a href="{{ route('prospek-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('prospek-batam.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Prospek Batam</span>
                    </a>
                @endif

                {{-- Tanda Terima Tanpa Surat Jalan --}}
                @if($user && ($user->can('tanda-terima-tanpa-surat-jalan-view') || $user->can('tanda-terima-tanpa-surat-jalan-create') || $user->can('tanda-terima-tanpa-surat-jalan-update') || $user->can('tanda-terima-tanpa-surat-jalan-delete')))
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('tanda-terima-tanpa-surat-jalan.*') && !Request::routeIs('tanda-terima-tanpa-surat-jalan-batam.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Tanda Terima Tanpa Surat Jalan</span>
                    </a>
                @endif

                {{-- Tanda Terima Tanpa Surat Jalan Batam --}}
                @if($user && ($user->can('tanda-terima-tanpa-surat-jalan-batam-view') || $user->can('tanda-terima-tanpa-surat-jalan-batam-create') || $user->can('tanda-terima-tanpa-surat-jalan-batam-update') || $user->can('tanda-terima-tanpa-surat-jalan-batam-delete')))
                    <a href="{{ route('tanda-terima-tanpa-surat-jalan-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('tanda-terima-tanpa-surat-jalan-batam.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Tanda Terima Tanpa Surat Jalan Batam</span>
                    </a>
                @endif

                {{-- Approval Tanda Terima --}}
                @if($user && $user->can('approval-tanda-terima-view'))
                    <a href="{{ route('approval-tanda-terima.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 {{ Request::routeIs('approval-tanda-terima.*') ? 'bg-indigo-50 text-indigo-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Approval Tanda Terima</span>
                    </a>
                @endif
                @if($user && $user->can('approval-tanda-terima-2-view'))
                    <a href="{{ route('approval-tanda-terima-2.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-indigo-50 hover:text-indigo-700 transition-all duration-200 {{ Request::routeIs('approval-tanda-terima-2.*') ? 'bg-indigo-50 text-indigo-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Approval Tanda Terima 2</span>
                    </a>
                @endif

                {{-- Dokumen Tanda Terima --}}
                @if($user && $user->can('dokumen-tanda-terima-view'))
                    <a href="{{ route('dokumen-tanda-terima.select') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('dokumen-tanda-terima.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Dokumen Tanda Terima</span>
                    </a>
                @endif

                {{-- Gate In --}}
                @if($user && ($user->can('gate-in-view') || $user->can('gate-in-create') || $user->can('gate-in-update') || $user->can('gate-in-delete')))
                    <a href="{{ route('gate-in.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('gate-in.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Gate In</span>
                    </a>
                @endif

                {{-- Tagihan Pelindo --}}
                @if($user && $user->can('tagihan-pelindo-view'))
                    <a href="{{ route('tagihan-pelindo.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('tagihan-pelindo.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-bold text-blue-600">Tagihan Pelindo</span>
                    </a>
                @endif

                {{-- Rincian Kontainer Pelindo --}}
                @if($user && $user->can('tagihan-pelindo-view'))
                    <a href="{{ route('rincian-kontainer-pelindo.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200 {{ Request::routeIs('rincian-kontainer-pelindo.*') ? 'bg-emerald-50 text-emerald-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs font-bold text-teal-600">Rincian Kontainer Pelindo</span>
                    </a>
                @endif


                {{-- Kontainer Dalam Perjalanan --}}
                @if($user && ($user->can('checkpoint-kontainer-keluar-view') || $user->can('checkpoint-kontainer-keluar-create') || $user->can('checkpoint-kontainer-keluar-delete')))
                    <a href="{{ route('kontainer-perjalanan.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('kontainer-perjalanan.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Kontainer Dalam Perjalanan</span>
                    </a>
                @endif


            </div>
        </div>
        @endif

        {{-- Aktivitas Lain-Lain Sub-Dropdown --}}
        @php
        $isAktivitasLainRoute = Request::routeIs('pembayaran-aktivitas-lain.*') || Request::routeIs('invoice-aktivitas-lain.*') || Request::routeIs('pembayaran-uang-muka.*') || Request::routeIs('realisasi-uang-muka.*');
        $hasAktivitasLainPermissions = $isAdmin || ($user && ($user->can('pembayaran-aktivitas-lain-view') || $user->can('pembayaran-aktivitas-lain-create') || $user->can('pembayaran-aktivitas-lain-update') || $user->can('pembayaran-aktivitas-lain-delete') || $user->can('invoice-aktivitas-lain-view') || $user->can('invoice-aktivitas-lain-create') || $user->can('invoice-aktivitas-lain-update') || $user->can('invoice-aktivitas-lain-delete') || $user->can('pembayaran-uang-muka-view') || $user->can('realisasi-uang-muka-view') || $user->can('realisasi-uang-muka-create') || $user->can('realisasi-uang-muka-update') || $user->can('realisasi-uang-muka-delete')));
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
                {{-- Invoice Aktivitas Lain --}}
                @if($isAdmin || ($user && $user->can('invoice-aktivitas-lain-view')))
                    <a href="{{ route('invoice-aktivitas-lain.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-pink-50 hover:text-pink-700 transition-all duration-200 {{ Request::routeIs('invoice-aktivitas-lain.*') ? 'bg-pink-50 text-pink-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Invoice Aktivitas Lain</span>
                    </a>
                @endif

                {{-- Pembayaran Aktivitas Lain --}}
                @if($isAdmin || ($user && $user->can('pembayaran-aktivitas-lain-view')))
                    <a href="{{ route('pembayaran-aktivitas-lain.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-pink-50 hover:text-pink-700 transition-all duration-200 {{ Request::routeIs('pembayaran-aktivitas-lain.*') ? 'bg-pink-50 text-pink-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pembayaran Aktivitas Lain</span>
                    </a>
                @endif
                {{-- Pembayaran Uang Muka --}}
                @if($isAdmin || ($user && $user->can('pembayaran-uang-muka-view')))
                    <a href="{{ route('pembayaran-uang-muka.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-pink-50 hover:text-pink-700 transition-all duration-200 {{ Request::routeIs('pembayaran-uang-muka.*') ? 'bg-pink-50 text-pink-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Pembayaran Uang Muka</span>
                    </a>
                @endif
                
                {{-- Realisasi Uang Muka --}}
                @if($isAdmin || ($user && $user->can('realisasi-uang-muka-view')))
                    <a href="{{ route('realisasi-uang-muka.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-pink-50 hover:text-pink-700 transition-all duration-200 {{ Request::routeIs('realisasi-uang-muka.*') ? 'bg-pink-50 text-pink-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Realisasi Uang Muka</span>
                    </a>
                @endif
            </div>
        </div>
        @endif 
    </div>
</div>
@endif

{{-- Kwitansi / Akuntansi Standalone Menu --}}
@if($user && $user->can('kwitansi-view'))
<div class="mt-4 mb-4">
    <a href="{{ route('kwitansi.select-ship') }}" target="_blank" class="w-full flex items-center py-2 px-5 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ Request::routeIs('kwitansi.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">
        <div class="flex items-center">
            <span class="text-xs font-medium truncate w-full">Kwitansi / Akuntansi</span>
        </div>
    </a>
</div>
@endif





{{-- Report Dropdown --}}
@php
    $isReportRoute = Request::routeIs('report.tagihan.*') || Request::routeIs('report.pembayaran.*') || Request::routeIs('report.rit.*') || Request::routeIs('report.lembur.*') || Request::routeIs('report.ongkos-truk.*') || Request::routeIs('report.manifests.*') || Request::routeIs('report.perincians.*') || Request::routeIs('report.kas-truck.*') || Request::routeIs('laporan-harian-kas-truck.*') || Request::routeIs('report-history-cuti.*');
    // Menu Report dapat diakses oleh semua user yang login
    $hasReportPermission = (bool) $user;
@endphp
@if($hasReportPermission)
<div class="mt-4 mb-4">
    <button id="report-menu-toggle" class="w-full flex justify-between items-center py-2 px-5 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ $isReportRoute ? 'bg-purple-50 text-purple-700 font-medium' : '' }}">
        <div class="flex items-center">
            <span class="text-xs font-medium truncate w-full">Report</span>
        </div>
        <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow {{ $isReportRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div id="report-menu-content" class="dropdown-content ml-8 space-y-2 mt-2" @if($isReportRoute) style="display: block;" @endif>
        {{-- Report History Cuti --}}
        <a href="{{ route('report-history-cuti.select-karyawan') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('report-history-cuti.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            Report History Cuti
        </a>

        {{-- Report Gaji Supir Batam --}}
        @if($user && ($isAdmin || $user->can('gaji-supir-batam-view')))
        <a href="{{ route('report-gaji-supir-batam.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('report-gaji-supir-batam.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            Report Gaji Supir Batam
        </a>
        @endif

        {{-- Report Pembayaran --}}
        <a href="{{ Route::has('report.pembayaran.index') ? route('report.pembayaran.index') : '#' }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('report.pembayaran.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            Report Pembayaran
            @if(!Route::has('report.pembayaran.index'))
                <span class="ml-auto text-xs text-gray-400 italic">(soon)</span>
            @endif
        </a>


        {{-- Report Rit --}}
        <a href="{{ route('report.rit.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('report.rit.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            Report Rit
        </a>

        {{-- Report Lembur/Nginap --}}
        <a href="{{ route('report.lembur.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('report.lembur.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            Report Lembur/Nginap
        </a>

        @if($user && ($isAdmin || $user->can('uang-jalan-view')))
        <a href="{{ route('laporan-harian-kas-truck.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('laporan-harian-kas-truck.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            Laporan Harian Kas Truck
        </a>
        @endif

        {{-- Report Kas Truck --}}
        <a href="{{ route('report.kas-truck.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('report.kas-truck.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            Report Kas Trucking
        </a>

        {{-- Report Ongkos Truk --}}
        <a href="{{ route('report.ongkos-truk.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('report.ongkos-truk.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            Report Ongkos Truk
        </a>

        {{-- Manifest --}}
        @if($user && ($isAdmin || $user->can('manifest-view')))
        <a href="{{ route('report.manifests.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('report.manifests.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            Manifest
        </a>
        @endif

        {{-- Perincian --}}
        @if($user && ($isAdmin || $user->can('perincian-view')))
        <a href="{{ route('report.perincians.index') }}" class="flex items-center py-1 px-3 rounded-md text-xs hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 {{ Request::routeIs('report.perincians.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600' }}">
            Perincian
        </a>
        @endif
    </div>
</div>
@endif

{{-- Purchase Order Menu --}}
@if($user && $user->can('purchase-order-view'))
<div class="mt-4">
    <a href="{{ route('purchase-orders.index') }}" class="w-full flex items-center gap-2 py-2 px-5 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ Request::routeIs('purchase-orders.*') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
        <i class="fas fa-file-invoice text-sm"></i>
        <span class="text-xs font-medium truncate w-full">Purchase Order</span>
    </a>
</div>
@endif

{{-- Jurnal OPT/OPP Menu - Standalone --}}
@if($user && $user->can('jurnal-opp-opt-view'))
<div class="mt-4">
    <a href="{{ route('jurnal-opp-opt.index') }}" target="_blank" class="w-full flex items-center py-2 px-5 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ Request::routeIs('jurnal-opp-opt.*') ? 'bg-emerald-50 text-emerald-700 font-medium' : '' }}">
        <span class="text-xs font-medium truncate w-full">Jurnal OPT/OPP</span>
    </a>
</div>
@endif

{{-- Audit Log Menu - Standalone --}}
@if($user && $user->can('audit-log-view'))
<div class="mt-4">
    <a href="{{ route('audit-logs.index') }}" target="_blank" class="w-full flex items-center py-2 px-5 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group text-xs {{ Request::routeIs('audit-logs.*') ? 'bg-orange-50 text-orange-700 font-medium' : '' }}">
        <span class="text-xs font-medium truncate w-full">Audit Log</span>
    </a>
</div>
@endif

                <p id="sidebar-empty-search" role="status" hidden class="px-3 py-6 text-sm text-gray-500 text-center">Menu tidak ditemukan. Coba kata kunci lain.</p>
            </nav>
        </div>
    @endif

    <!-- Page Content -->
    <div id="page-content" class="flex-1 overflow-auto {{ $showSidebar ? 'page-content-with-sidebar' : '' }}">
        <div class="p-6">
            @yield('content')
        </div>
    </div>

    {{-- jQuery CDN - Required for many scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    {{-- Select2 CSS & JS for searchable dropdowns --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- Stack skrip akan dirender di akhir file (line 2758) untuk memastikan DOM sudah siap --}}


    {{-- Skrip khusus untuk layout ini, seperti dropdown menu --}}
    <script>
        // Sidebar navigation keeps the existing routes and permission checks.
        const sidebar = document.getElementById('sidebar');
        const nav = document.getElementById('sidebar-navigation');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeSidebar = document.getElementById('close-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const mainContent = document.getElementById('page-content');
        const desktopSidebar = window.matchMedia('(min-width: 1024px)');
        let sidebarOpen = false;

        function setDropdown(toggle, content, expanded) {
            content.style.display = expanded ? 'block' : 'none';
            toggle.setAttribute('aria-expanded', String(expanded));
            toggle.querySelector('.dropdown-arrow')?.classList.toggle('rotate-180', expanded);
        }

        const dropdowns = nav ? [...nav.querySelectorAll('button[id$="-toggle"]')].map(toggle => {
            const content = document.getElementById(toggle.id.replace(/-toggle$/, '-content'));
            if (!content) return null;
            toggle.type = 'button';
            toggle.setAttribute('aria-controls', content.id);
            setDropdown(toggle, content, content.style.display === 'block');
            toggle.addEventListener('click', () => setDropdown(toggle, content, toggle.getAttribute('aria-expanded') !== 'true'));
            return { toggle, content };
        }).filter(Boolean) : [];

        nav?.querySelectorAll('a').forEach(link => {
            const active = [...link.classList].some(name => /^bg-(blue|green|purple|orange|indigo|pink|red|yellow|teal)-50$/.test(name))
                || new URL(link.href, location.href).pathname === location.pathname;
            if (active) {
                link.classList.add('sidebar-active');
                link.setAttribute('aria-current', 'page');
                dropdowns.forEach(({ toggle, content }) => {
                    if (content.contains(link)) setDropdown(toggle, content, true);
                });
            }
            link.title = link.textContent.trim();
            link.addEventListener('click', () => {
                if (!desktopSidebar.matches) setSidebarOpen(false);
            });
        });

        function setSidebarOpen(open, restoreFocus = true) {
            if (!sidebar) return;
            sidebarOpen = open && !desktopSidebar.matches;
            sidebar.classList.toggle('-translate-x-full', !sidebarOpen);
            sidebar.inert = !desktopSidebar.matches && !sidebarOpen;
            overlay?.classList.toggle('hidden', !sidebarOpen);
            mobileMenuButton?.setAttribute('aria-expanded', String(sidebarOpen));
            if (mainContent) mainContent.inert = sidebarOpen;
            if (sidebarOpen) closeSidebar?.focus();
            else if (restoreFocus && !desktopSidebar.matches) mobileMenuButton?.focus();
        }

        mobileMenuButton?.addEventListener('click', () => setSidebarOpen(true));
        closeSidebar?.addEventListener('click', () => setSidebarOpen(false));
        overlay?.addEventListener('click', () => setSidebarOpen(false));
        desktopSidebar.addEventListener('change', () => setSidebarOpen(false, false));
        setSidebarOpen(false, false);

        document.addEventListener('keydown', event => {
            if (!sidebarOpen) return;
            if (event.key === 'Escape') {
                event.preventDefault();
                setSidebarOpen(false);
            }
            if (event.key === 'Tab') {
                const focusable = [...sidebar.querySelectorAll('a, button, input')]
                    .filter(element => !element.disabled && element.getClientRects().length);
                const first = focusable[0];
                const last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last?.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first?.focus();
                }
            }
        });

        // Profile controls are absent on guest pages.
        const profileDropdownButton = document.getElementById('profileDropdownButton');
        const profileDropdown = document.getElementById('profileDropdown');
        profileDropdownButton?.addEventListener('click', event => {
            event.stopPropagation();
            profileDropdown?.classList.toggle('hidden');
        });
        document.addEventListener('click', event => {
            if (profileDropdownButton && profileDropdown
                && !profileDropdownButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.add('hidden');
            }
        });

        // Search literal text without replacing labels, SVGs, or dropdown arrows.
        const sidebarSearch = document.getElementById('sidebar-search');
        const clearSearch = document.getElementById('clear-search');
        const emptySearch = document.getElementById('sidebar-empty-search');
        if (sidebarSearch && nav) {
            const items = [...nav.querySelectorAll('a, button')];
            const groups = [...nav.querySelectorAll('div')].filter(group => group.querySelector('a, button'));
            let savedDisplay = null;
            let savedExpanded = null;

            function filterMenuItems() {
                const term = sidebarSearch.value.trim().toLocaleLowerCase();
                if (term && !savedDisplay) {
                    savedDisplay = new Map([...items, ...groups].map(element => [element, element.style.display]));
                    savedExpanded = new Map(dropdowns.map(({ toggle }) => [toggle, toggle.getAttribute('aria-expanded') === 'true']));
                }
                clearSearch?.classList.toggle('opacity-0', !term);
                clearSearch?.classList.toggle('pointer-events-none', !term);
                if (clearSearch) clearSearch.disabled = !term;
                if (!term) {
                    savedDisplay?.forEach((display, element) => { element.style.display = display; });
                    dropdowns.forEach(({ toggle, content }) => {
                        if (savedExpanded) setDropdown(toggle, content, savedExpanded.get(toggle));
                    });
                    savedDisplay = null;
                    savedExpanded = null;
                    if (emptySearch) emptySearch.hidden = true;
                    return;
                }

                const matches = new Set(items.filter(item => item.textContent.toLocaleLowerCase().includes(term)));
                const matchedGroups = dropdowns.filter(({ toggle }) => matches.has(toggle));
                items.forEach(item => {
                    const visible = matches.has(item) || matchedGroups.some(({ content }) => content.contains(item));
                    item.style.display = visible ? '' : 'none';
                });
                // Children are processed first so every matching menu has visible parents.
                [...dropdowns].reverse().forEach(({ toggle, content }) => {
                    const visible = [...content.querySelectorAll('a, button')].some(item => item.style.display !== 'none');
                    toggle.style.display = visible || matches.has(toggle) ? '' : 'none';
                    setDropdown(toggle, content, visible);
                });
                groups.forEach(group => {
                    const visible = [...group.querySelectorAll('a, button')].some(item => item.style.display !== 'none');
                    if (group.classList.contains('dropdown-content')) {
                        group.style.display = visible ? 'block' : 'none';
                    } else {
                        group.style.display = visible ? '' : 'none';
                    }
                });
                if (emptySearch) emptySearch.hidden = items.some(item => item.style.display !== 'none');
            }

            sidebarSearch.addEventListener('input', filterMenuItems);
            clearSearch?.addEventListener('click', () => {
                sidebarSearch.value = '';
                filterMenuItems();
                sidebarSearch.focus();
            });
            sidebarSearch.addEventListener('keydown', event => {
                if (event.key === 'Escape' && sidebarSearch.value) {
                    event.stopPropagation();
                    sidebarSearch.value = '';
                    filterMenuItems();
                }
            });
        }

        function deleteItem(event, form) {
            event.preventDefault();
            
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                return false;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalContent = submitBtn ? submitBtn.innerHTML : null;
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            }

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const row = form.closest('tr');
                    if (row) {
                        row.style.transition = 'all 0.3s ease-out';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => row.remove(), 300);
                    } else {
                        location.reload();
                    }
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalContent;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus data');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                }
            });

            return false;
        }
    </script>
    @stack('scripts')
</body>
</html>
