@extends('email.layout')

@section('email_content')
<div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <h2 class="text-lg font-medium text-gray-900">Kotak Masuk (IMAP)</h2>
</div>

<div class="divide-y divide-gray-200">
    @forelse($emails as $email)
    @php
        $isRead = $email->hasFlag('Seen');
        $fromName = !empty($email->getFrom()[0]->personal) ? $email->getFrom()[0]->personal : ($email->getFrom()[0]->mail ?? 'Unknown');
        $initial = strtoupper(substr($fromName, 0, 1));
    @endphp
    <div class="hover:bg-gray-50 flex items-center px-6 py-4 {{ !$isRead ? 'bg-indigo-50/50' : '' }}">
        <div class="min-w-0 flex-1 flex items-center">
            <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                    {{ $initial }}
                </div>
            </div>
            <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-900 truncate {{ !$isRead ? 'font-bold' : '' }}">{{ $fromName }}</p>
                    <a href="{{ route('email.show', $email->getUid()) }}" class="block mt-1">
                        <p class="text-sm text-gray-600 truncate {{ !$isRead ? 'font-semibold text-gray-900' : '' }}">{{ $email->getSubject() }}</p>
                    </a>
                </div>
                <div class="hidden md:block">
                    <div>
                        <p class="text-sm text-gray-500 truncate">
                            {{ Str::limit($email->getTextBody(), 50) }}
                        </p>
                        <p class="mt-1 flex items-center text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($email->getDate()->toString())->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="ml-5 flex-shrink-0 flex items-center space-x-2">
            <form action="{{ route('email.markAsSpam', $email->getUid()) }}" method="POST" class="inline-block">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-gray-400 hover:text-yellow-600 transition-colors" title="Tandai Spam">
                    <i class="fas fa-exclamation-circle"></i>
                </button>
            </form>
            <form action="{{ route('email.moveToTrash', $email->getUid()) }}" method="POST" class="inline-block">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="px-6 py-10 text-center text-gray-500">
        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
        <p>Kotak masuk Anda kosong atau tidak terhubung.</p>
    </div>
    @endforelse
</div>

@if(isset($emails) && $emails->hasPages())
<div class="px-6 py-4 border-t border-gray-200">
    {{ $emails->links() }}
</div>
@endif
@endsection
