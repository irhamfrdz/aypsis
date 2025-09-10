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
                        <span>{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                        <div class="py-2">
                            <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-3"></i>Profil Saya
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-edit mr-3"></i>Edit Profil
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
        <!-- Sidebar -->
        <div id="sidebar" class="hidden lg:flex lg:flex-col lg:w-64 bg-white shadow-lg border-r border-gray-200 fixed lg:static inset-y-0 left-0 z-40 transform -translate-x-full lg:translate-x-0 transition-transform">
            <!-- Mobile close button -->
            <div class="lg:hidden absolute top-4 right-4">
                <button id="close-sidebar" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Header -->
            <div class="p-6 border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center text-gray-800">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-lg text-gray-900">AYPSIS</h2>
                        <p class="text-xs text-gray-500">Management System</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-2 sidebar-scroll">
                @php
                    $user = Auth::user();
                    $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
                @endphp

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 rounded-lg mb-1 transition-all duration-200 group @if(Request::routeIs('dashboard')) bg-blue-50 text-blue-700 font-medium @endif">
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-3 @if(Request::routeIs('dashboard')) bg-blue-100 @endif">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-700 @if(Request::routeIs('dashboard')) text-blue-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        </svg>
                    </div>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Master Data Section -->
                @php
                    $isMasterRoute = Request::routeIs('master.karyawan.*') || Request::routeIs('master.user.*') || Request::routeIs('master.kontainer.*') || Request::routeIs('master.tujuan.*') || Request::routeIs('master.kegiatan.*') || Request::routeIs('master.permission.*') || Request::routeIs('master.mobil.*');
                    $isPermohonanRoute = Request::routeIs('permohonan.*');
                    $isPenyelesaianRoute = Request::routeIs('penyelesaian.*');
                    $isPranotaRoute = Request::routeIs('pranota-supir.*');
                @endphp

                @if($isAdmin)
                <div class="mb-1">
                    <button id="master-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition-all duration-200 group {{ $isMasterRoute ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-3 {{ $isMasterRoute ? 'bg-blue-100' : '' }}">
                                <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-700 {{ $isMasterRoute ? 'text-blue-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <span class="font-medium">Master Data</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200 {{ $isMasterRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="master-menu-content" class="dropdown-content ml-12 space-y-1 mt-2" @if($isMasterRoute) style="display: block;" @endif>
                        @can('master-karyawan')
                            <a href="{{ route('master.karyawan.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 @if(Request::routeIs('master.karyawan.*')) bg-blue-50 text-blue-700 font-medium @endif">
                                <div class="w-2 h-2 rounded-full bg-gray-400 mr-3 @if(Request::routeIs('master.karyawan.*')) bg-blue-500 @endif"></div>
                                Karyawan
                            </a>
                        @endcan

                                @can('master-user')
                                    <a href="{{ route('master.user.index') }}" class="flex items-center py-2 px-3 rounded-md text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 @if(Request::routeIs('master.user.*')) bg-indigo-50 text-indigo-600 font-medium @endif">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                        </svg>
                                        User
                                    </a>
                                @endcan

                                @can('master-kontainer')
                                    <a href="{{ route('master.kontainer.index') }}" class="flex items-center py-2 px-3 rounded-md text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 @if(Request::routeIs('master.kontainer.*')) bg-indigo-50 text-indigo-600 font-medium @endif">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        Kontainer
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
                    <div class="mb-1">
                        <button id="master-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition-all duration-200 group {{ $isMasterRoute ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-3 {{ $isMasterRoute ? 'bg-blue-100' : '' }}">
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-700 {{ $isMasterRoute ? 'text-blue-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <span class="font-medium">Master Data</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 {{ $isMasterRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="master-menu-content" class="dropdown-content ml-12 space-y-1 mt-2" @if($isMasterRoute) style="display: block;" @endif>
                            @can('master-karyawan')
                                <a href="{{ route('master.karyawan.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 @if(Request::routeIs('master.karyawan.*')) bg-blue-50 text-blue-700 font-medium @endif">
                                    <div class="w-2 h-2 rounded-full bg-gray-400 mr-3 @if(Request::routeIs('master.karyawan.*')) bg-blue-500 @endif"></div>
                                    Karyawan
                                </a>
                            @endcan
                            @can('master-user')
                                <a href="{{ route('master.user.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 @if(Request::routeIs('master.user.*')) bg-blue-50 text-blue-700 font-medium @endif">
                                    <div class="w-2 h-2 rounded-full bg-gray-400 mr-3 @if(Request::routeIs('master.user.*')) bg-blue-500 @endif"></div>
                                    User
                                </a>
                            @endcan
                            @can('master-kontainer')
                                <a href="{{ route('master.kontainer.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 @if(Request::routeIs('master.kontainer.*')) bg-blue-50 text-blue-700 font-medium @endif">
                                    <div class="w-2 h-2 rounded-full bg-gray-400 mr-3 @if(Request::routeIs('master.kontainer.*')) bg-blue-500 @endif"></div>
                                    Kontainer
                                </a>
                            @endcan
                            @can('master-pricelist-sewa-kontainer')
                                <a href="{{ route('master.pricelist-sewa-kontainer.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 @if(Request::routeIs('master.pricelist-sewa-kontainer.*')) bg-blue-50 text-blue-700 font-medium @endif">
                                    <div class="w-2 h-2 rounded-full bg-gray-400 mr-3 @if(Request::routeIs('master.pricelist-sewa-kontainer.*')) bg-blue-500 @endif"></div>
                                    Pricelist Sewa Kontainer
                                </a>
                            @endcan
                            @can('master-tujuan')
                                <a href="{{ route('master.tujuan.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 @if(Request::routeIs('master.tujuan.*')) bg-blue-50 text-blue-700 font-medium @endif">
                                    <div class="w-2 h-2 rounded-full bg-gray-400 mr-3 @if(Request::routeIs('master.tujuan.*')) bg-blue-500 @endif"></div>
                                    Tujuan
                                </a>
                            @endcan
                            @can('master-kegiatan')
                                <a href="{{ route('master.kegiatan.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 @if(Request::routeIs('master.kegiatan.*')) bg-blue-50 text-blue-700 font-medium @endif">
                                    <div class="w-2 h-2 rounded-full bg-gray-400 mr-3 @if(Request::routeIs('master.kegiatan.*')) bg-blue-500 @endif"></div>
                                    Kegiatan
                                </a>
                            @endcan
                            @can('master-permission')
                                <a href="{{ route('master.permission.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 @if(Request::routeIs('master.permission.*')) bg-blue-50 text-blue-700 font-medium @endif">
                                    <div class="w-2 h-2 rounded-full bg-gray-400 mr-3 @if(Request::routeIs('master.permission.*')) bg-blue-500 @endif"></div>
                                    Izin
                                </a>
                            @endcan
                            @can('master-mobil')
                                <a href="{{ route('master.mobil.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 @if(Request::routeIs('master.mobil.*')) bg-blue-50 text-blue-700 font-medium @endif">
                                    <div class="w-2 h-2 rounded-full bg-gray-400 mr-3 @if(Request::routeIs('master.mobil.*')) bg-blue-500 @endif"></div>
                                    Mobil
                                </a>
                            @endcan
                            {{-- Hapus menu dari master data --}}
                        </div>
                    </div>
                    @endcanany
                    @endif

                    {{-- User Approval Management (only for admins or users with master-user permission) --}}
                    @if($isAdmin || auth()->user()->can('master-user'))
                    @php
                        $isUserApprovalRoute = Request::routeIs('admin.user-approval.*');
                        $pendingUsersCount = \App\Models\User::where('status', 'pending')->count();
                    @endphp
                    <div class="mb-1">
                        <a href="{{ route('admin.user-approval.index') }}" class="w-full flex items-center py-3 px-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition-all duration-200 group {{ $isUserApprovalRoute ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-3 {{ $isUserApprovalRoute ? 'bg-blue-100' : '' }}">
                                <i class="fas fa-user-check text-gray-600 group-hover:text-gray-700 {{ $isUserApprovalRoute ? 'text-blue-600' : '' }}"></i>
                            </div>
                            <span class="font-medium">Persetujuan User</span>
                            @if($pendingUsersCount > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingUsersCount }}</span>
                            @endif
                        </a>
                    </div>
                    @endif

                    {{-- Dropdown untuk Tagihan Kontainer Sewa (di luar master data) --}}
                    {{-- Tagihan Kontainer Sewa menu removed (refactored) --}}

                    {{-- Dropdown untuk Tagihan Kontainer Sewa (baru) --}}
                    @if($isAdmin || auth()->user()->can('master-pranota-tagihan-kontainer'))
                    @php
                        $isPranotaTagihanRoute = Request::routeIs('pembayaran-pranota-tagihan-kontainer.*') || Request::routeIs('pranota-tagihan-kontainer.*') || Request::routeIs('pembayaran-pranota-kontainer.*') || Request::routeIs('pranota.*') || Request::routeIs('daftar-tagihan-kontainer-sewa.*');
                    @endphp
                    <div class="mb-1">
                        <button id="pranota-tagihan-kontainer-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition-all duration-200 group {{ $isPranotaTagihanRoute ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-3 {{ $isPranotaTagihanRoute ? 'bg-blue-100' : '' }}">
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-700 {{ $isPranotaTagihanRoute ? 'text-blue-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <span class="font-medium">Tagihan Kontainer Sewa</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 {{ $isPranotaTagihanRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="pranota-tagihan-kontainer-menu-content" class="dropdown-content ml-12 space-y-1 mt-2" @if($isPranotaTagihanRoute) style="display: block;" @endif>
                            @if (Route::has('pembayaran-pranota-tagihan-kontainer.index'))
                                <a href="{{ route('pembayaran-pranota-tagihan-kontainer.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm {{ Request::routeIs('pembayaran-pranota-tagihan-kontainer.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <div class="w-2 h-2 rounded-full {{ Request::routeIs('pembayaran-pranota-tagihan-kontainer.*') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                    Pembayaran Tagihan Kontainer Sewa
                                </a>
                            @endif

                            @if (Route::has('daftar-tagihan-kontainer-sewa.index'))
                                <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm {{ Request::routeIs('daftar-tagihan-kontainer-sewa.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <div class="w-2 h-2 rounded-full {{ Request::routeIs('daftar-tagihan-kontainer-sewa.*') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                    Daftar Tagihan Kontainer
                                </a>
                            @endif

                            @if (Route::has('pranota.index'))
                                <a href="{{ route('pranota.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm {{ Request::routeIs('pranota.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <div class="w-2 h-2 rounded-full {{ Request::routeIs('pranota.*') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                    Daftar Pranota
                                </a>
                            @endif

                            @if (Route::has('pembayaran-pranota-kontainer.index'))
                                <a href="{{ route('pembayaran-pranota-kontainer.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm {{ Request::routeIs('pembayaran-pranota-kontainer.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <div class="w-2 h-2 rounded-full {{ Request::routeIs('pembayaran-pranota-kontainer.*') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                    Pembayaran Pranota Kontainer
                                </a>
                            @endif

                            @if (Route::has('pembayaran-pranota-tagihan-kontainer.create'))
                                <a href="{{ route('pembayaran-pranota-tagihan-kontainer.create') }}" class="flex items-center py-2 px-3 rounded-lg text-sm {{ Request::routeIs('pembayaran-pranota-tagihan-kontainer.create') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                    <div class="w-2 h-2 rounded-full {{ Request::routeIs('pembayaran-pranota-tagihan-kontainer.create') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                    Buat Pembayaran
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Dropdown untuk Permohonan Memo --}}
                    @if($isAdmin || auth()->user()->can('master-permohonan'))
                    <div class="mb-1">
                        <button id="permohonan-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition-all duration-200 group {{ $isPermohonanRoute ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-3 {{ $isPermohonanRoute ? 'bg-blue-100' : '' }}">
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-700 {{ $isPermohonanRoute ? 'text-blue-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <span class="font-medium">Permohonan Memo</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 {{ $isPermohonanRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="permohonan-menu-content" class="dropdown-content ml-12 space-y-1 mt-2" @if($isPermohonanRoute) style="display: block;" @endif>
                            <a href="{{ route('permohonan.create') }}" class="flex items-center py-2 px-3 rounded-lg text-sm {{ Request::routeIs('permohonan.create') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                <div class="w-2 h-2 rounded-full {{ Request::routeIs('permohonan.create') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                Buat Permohonan
                            </a>
                            <a href="{{ route('permohonan.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm {{ Request::routeIs('permohonan.index') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                <div class="w-2 h-2 rounded-full {{ Request::routeIs('permohonan.index') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                Daftar Permohonan
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Dropdown untuk Pranota --}}
                    @if($isAdmin || auth()->user()->can('master-pranota-supir'))
                    <div class="mb-1">
                        <button id="pranota-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition-all duration-200 group {{ $isPranotaRoute ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-3 {{ $isPranotaRoute ? 'bg-blue-100' : '' }}">
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-700 {{ $isPranotaRoute ? 'text-blue-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <span class="font-medium">Pranota Supir</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 {{ $isPranotaRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="pranota-menu-content" class="dropdown-content ml-12 space-y-1 mt-2" @if($isPranotaRoute) style="display: block;" @endif>
                            <a href="{{ route('pranota-supir.create') }}" class="flex items-center py-2 px-3 rounded-lg text-sm {{ Request::routeIs('pranota-supir.create') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                <div class="w-2 h-2 rounded-full {{ Request::routeIs('pranota-supir.create') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                Buat Pranota Supir
                            </a>
                            <a href="{{ route('pranota-supir.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm {{ Request::routeIs('pranota-supir.index') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                <div class="w-2 h-2 rounded-full {{ Request::routeIs('pranota-supir.index') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                Daftar Pranota Supir
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Dropdown untuk Pembayaran Pranota Supir --}}
                    @if($isAdmin || auth()->user()->can('master-pembayaran-pranota-supir'))
                    @php
                        $isPembayaranPranotaRoute = Request::routeIs('pembayaran-pranota-supir.*');
                    @endphp
                    <div class="mb-1">
                        <button id="pembayaran-pranota-menu-toggle" class="w-full flex justify-between items-center py-3 px-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition-all duration-200 group {{ $isPembayaranPranotaRoute ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-3 {{ $isPembayaranPranotaRoute ? 'bg-blue-100' : '' }}">
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-700 {{ $isPembayaranPranotaRoute ? 'text-blue-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <span class="font-medium">Pembayaran Pranota Supir</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 {{ $isPembayaranPranotaRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="pembayaran-pranota-menu-content" class="dropdown-content ml-12 space-y-1 mt-2" @if($isPembayaranPranotaRoute) style="display: block;" @endif>
                            <a href="{{ route('pembayaran-pranota-supir.create') }}" class="flex items-center py-2 px-3 rounded-lg text-sm {{ Request::routeIs('pembayaran-pranota-supir.create') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                <div class="w-2 h-2 rounded-full {{ Request::routeIs('pembayaran-pranota-supir.create') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                Buat Pembayaran
                            </a>
                            <a href="{{ route('pembayaran-pranota-supir.index') }}" class="flex items-center py-2 px-3 rounded-lg text-sm {{ Request::routeIs('pembayaran-pranota-supir.index') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-all duration-200">
                                <div class="w-2 h-2 rounded-full {{ Request::routeIs('pembayaran-pranota-supir.index') ? 'bg-blue-500' : 'bg-gray-400' }} mr-3"></div>
                                Daftar Pembayaran
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Link untuk Penyelesaian Tugas --}}
                    @if($isAdmin || auth()->user()->can('master-permohonan'))
                    <a href="{{ route('approval.dashboard') }}" class="flex items-center py-3 px-4 text-gray-700 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition-all duration-200 group mb-1 {{ $isPenyelesaianRoute ? 'bg-orange-50 text-orange-700 font-medium' : '' }}">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-gray-200 mr-3 {{ $isPenyelesaianRoute ? 'bg-orange-100' : '' }}">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-700 {{ $isPenyelesaianRoute ? 'text-orange-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="font-medium">Approval Tugas</span>
                    </a>
                @endif
            </nav>
        </div>

        <!-- Page Content -->
        <div class="flex-1 overflow-auto">
            <div class="p-6">
                @yield('content')
            </div>
        </div>

        <!-- Mobile overlay -->
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
                const icon = toggleButton.querySelector('svg');
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
        setupDropdown('permohonan-menu-toggle', 'permohonan-menu-content');
        setupDropdown('pranota-menu-toggle', 'pranota-menu-content');
        setupDropdown('pembayaran-pranota-menu-toggle', 'pembayaran-pranota-menu-content');
        setupDropdown('pranota-tagihan-kontainer-menu-toggle', 'pranota-tagihan-kontainer-menu-content');
    </script>
</body>
</html>
