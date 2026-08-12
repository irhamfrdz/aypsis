@extends('layouts.app')

@section('page_title', 'Notifikasi')
@section('title', 'Notifikasi')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Notifikasi</h1>
                    <p class="mt-2 text-sm text-gray-500">Pantau dan kelola seluruh aktivitas pembaruan Anda di sini.</p>
                </div>
                
                <div class="flex items-center gap-3">
                    @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-blue-600/10 text-blue-700 hover:bg-blue-600 hover:text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Tandai Semua Dibaca
                        </button>
                    </form>
                    @endif
                    
                    @if(auth()->user()->notifications->count() > 0)
                    <form action="{{ route('notifications.clear-all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus secara permanen semua riwayat notifikasi?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-red-50 text-red-600 hover:bg-red-500 hover:text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md focus:ring-2 focus:ring-red-500 focus:ring-offset-2 group">
                            <svg class="w-4 h-4 mr-2 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Bersihkan Riwayat
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex items-start shadow-sm" role="alert">
            <svg class="w-5 h-5 text-emerald-500 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm text-emerald-800 font-medium">
                {{ session('success') }}
            </div>
        </div>
        @endif

        <!-- Notifications List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($notifications->count() > 0)
                <div class="divide-y divide-gray-50">
                    @foreach($notifications as $notification)
                    @php 
                        $isUnread = is_null($notification->read_at);
                    @endphp
                    <div class="group p-5 hover:bg-gray-50/80 transition-all duration-200 {{ $isUnread ? 'bg-blue-50/30' : 'bg-white' }}">
                        <div class="flex items-start gap-4">
                            
                            <!-- Icon Indicator -->
                            <div class="shrink-0 mt-1">
                                @if(Str::contains($notification->type, 'Approved') || Str::contains($notification->type, 'Setuju'))
                                <div class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center ring-4 ring-emerald-50 group-hover:ring-white transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                @elseif(Str::contains($notification->type, 'Rejected') || Str::contains($notification->type, 'Tolak'))
                                <div class="h-10 w-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center ring-4 ring-rose-50 group-hover:ring-white transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                @else
                                <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center ring-4 ring-blue-50 group-hover:ring-white transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-4 mb-1">
                                    <p class="text-sm font-semibold text-gray-900 line-clamp-1">
                                        {{ $notification->data['title'] ?? 'Pemberitahuan Sistem' }}
                                    </p>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <time class="text-xs font-medium text-gray-400 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </time>
                                        @if($isUnread)
                                        <span class="relative flex h-2.5 w-2.5 ml-1">
                                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2 leading-relaxed">
                                    {{ $notification->data['message'] ?? 'Tidak ada rincian pesan.' }}
                                </p>
                                
                                @if(isset($notification->data['order_number']) || isset($notification->data['notes']))
                                <div class="flex flex-wrap gap-2 mb-2">
                                    @if(isset($notification->data['order_number']))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        <span class="text-gray-500 mr-1">Order:</span> {{ $notification->data['order_number'] }}
                                    </span>
                                    @endif
                                    @if(isset($notification->data['notes']) && $notification->data['notes'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        <span class="text-gray-500 mr-1">Catatan:</span> {{ Str::limit($notification->data['notes'], 40) }}
                                    </span>
                                    @endif
                                </div>
                                @endif
                                
                                @if(isset($notification->data['url']))
                                <div class="mt-3">
                                    <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                                            Lihat Selengkapnya
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                            
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="w-full sm:w-auto">
                        @include('components.rows-per-page')
                    </div>
                    <div class="w-full sm:w-auto">
                        @include('components.modern-pagination', ['paginator' => $notifications])
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-20 px-4">
                    <div class="h-24 w-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 ring-8 ring-gray-50/50">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Notifikasi</h3>
                    <p class="text-gray-500 text-center max-w-sm">Saat ini kotak masuk notifikasi Anda masih kosong. Pembaruan aktivitas akan muncul di sini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
