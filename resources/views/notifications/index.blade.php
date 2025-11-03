@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Notifikasi</h1>
            <p class="text-gray-600 mt-1">Daftar semua notifikasi Anda</p>
        </div>
        
        @if(auth()->user()->unreadNotifications->count() > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                <i class="fas fa-check-double mr-2"></i>
                Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($notifications->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($notifications as $notification)
                <div class="p-4 hover:bg-gray-50 transition duration-150 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <!-- Notification Icon -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    @if(Str::contains($notification->type, 'Approved'))
                                    <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                    @elseif(Str::contains($notification->type, 'Rejected'))
                                    <div class="h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-times text-red-600"></i>
                                    </div>
                                    @else
                                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-bell text-blue-600"></i>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="ml-3 flex-1">
                                    <!-- Message -->
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $notification->data['message'] ?? 'Notifikasi' }}
                                    </p>
                                    
                                    <!-- Details -->
                                    @if(isset($notification->data['order_number']))
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-semibold">Nomor Order:</span> {{ $notification->data['order_number'] }}
                                    </p>
                                    @endif
                                    
                                    @if(isset($notification->data['notes']) && $notification->data['notes'])
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-semibold">Catatan:</span> {{ $notification->data['notes'] }}
                                    </p>
                                    @endif
                                    
                                    <!-- Time -->
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="ml-4 flex flex-col space-y-2">
                            <!-- View Button -->
                            @if(isset($notification->data['url']))
                            <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200">
                                    <i class="fas fa-eye mr-1"></i>
                                    Lihat
                                </button>
                            </form>
                            @endif
                            
                            <!-- Unread Badge -->
                            @if(!$notification->read_at)
                            <span class="px-2 py-1 text-xs bg-blue-600 text-white rounded-full text-center">
                                Baru
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                {{ $notifications->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="inline-block h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-bell-slash text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Notifikasi</h3>
                <p class="text-gray-600">Anda tidak memiliki notifikasi saat ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection
