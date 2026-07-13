@extends('layouts.app')

@section('title', 'Kelola Email')
@section('page_title', 'Kelola Email')

@section('content')
<div class="p-6 h-full overflow-y-auto">
    <div class="flex flex-col md:flex-row gap-6 h-full">
        <!-- Email Sidebar -->
        <div class="w-full md:w-64 flex-shrink-0">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <a href="{{ route('email.create') }}" class="w-full flex items-center justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i> Tulis Pesan
                    </a>
                </div>
                <nav class="p-2 space-y-1">
                    <a href="{{ route('email.inbox') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ Request::routeIs('email.inbox') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-inbox w-5 mr-2 {{ Request::routeIs('email.inbox') ? 'text-indigo-700' : 'text-gray-400' }}"></i>
                        Kotak Masuk
                    </a>
                    <a href="{{ route('email.sent') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ Request::routeIs('email.sent') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-paper-plane w-5 mr-2 {{ Request::routeIs('email.sent') ? 'text-indigo-700' : 'text-gray-400' }}"></i>
                        Terkirim
                    </a>
                    <a href="{{ route('email.spam') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ Request::routeIs('email.spam') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-exclamation-triangle w-5 mr-2 {{ Request::routeIs('email.spam') ? 'text-indigo-700' : 'text-gray-400' }}"></i>
                        Spam
                    </a>
                    <a href="{{ route('email.trash') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ Request::routeIs('email.trash') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-trash-alt w-5 mr-2 {{ Request::routeIs('email.trash') ? 'text-indigo-700' : 'text-gray-400' }}"></i>
                        Terhapus
                    </a>
                </nav>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <nav class="p-2 space-y-1">
                        <a href="{{ route('email.settings') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ Request::routeIs('email.settings') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fas fa-cog w-5 mr-2 {{ Request::routeIs('email.settings') ? 'text-indigo-700' : 'text-gray-400' }}"></i>
                            Pengaturan Akun
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Email Content -->
        <div class="flex-1 min-w-0 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
            @if(session('success'))
            <div class="p-4 bg-green-50 border-b border-green-200 text-green-700 text-sm">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="p-4 bg-red-50 border-b border-red-200 text-red-700 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
            @endif
            
            <div class="flex-1 overflow-y-auto">
                @yield('email_content')
            </div>
        </div>
    </div>
</div>
@endsection
