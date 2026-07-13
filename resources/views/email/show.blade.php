@extends('email.layout')

@section('email_content')
<div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <a href="{{ url()->previous() }}" class="text-gray-400 hover:text-gray-500">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-lg font-medium text-gray-900">{{ $email->getSubject() }}</h2>
    </div>
    <div class="flex items-center space-x-2">
        <form action="{{ route('email.moveToTrash', $email->getUid()) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</div>

<div class="p-6">
    @php
        $fromName = !empty($email->getFrom()[0]->personal) ? $email->getFrom()[0]->personal : ($email->getFrom()[0]->mail ?? 'Unknown');
        $fromEmail = $email->getFrom()[0]->mail ?? '';
        $toName = !empty($email->getTo()[0]->personal) ? $email->getTo()[0]->personal : ($email->getTo()[0]->mail ?? 'Unknown');
        $initial = strtoupper(substr($fromName, 0, 1));
    @endphp
    
    <div class="flex items-start justify-between mb-6">
        <div class="flex items-center">
            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-lg">
                {{ $initial }}
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-900">{{ $fromName }} &lt;{{ $fromEmail }}&gt;</p>
                <p class="text-xs text-gray-500">kepada {{ $toName }}</p>
            </div>
        </div>
        <div class="text-sm text-gray-500">
            @php $emailDate = \Carbon\Carbon::parse($email->getDate()->toString()); @endphp
            {{ $emailDate->format('d M Y H:i') }} ({{ $emailDate->diffForHumans() }})
        </div>
    </div>

    <div class="prose prose-sm max-w-none text-gray-800 bg-gray-50 rounded-lg p-6 border border-gray-100 min-h-[200px] overflow-x-auto">
        {!! $email->getHTMLBody() ?: nl2br(e($email->getTextBody())) !!}
    </div>

    @if($email->hasAttachments())
    <div class="mt-6">
        <h3 class="text-sm font-medium text-gray-900 mb-3"><i class="fas fa-paperclip mr-2"></i> Lampiran</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($email->getAttachments() as $attachment)
            <div class="flex items-center p-3 border border-gray-200 rounded-lg bg-white shadow-sm">
                <i class="fas fa-file text-gray-400 text-xl mr-3"></i>
                <div class="flex-1 min-w-0 mr-4">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->name }}</p>
                    <p class="text-xs text-gray-500">{{ round($attachment->size / 1024, 2) }} KB</p>
                </div>
                <a href="#" class="text-indigo-600 hover:text-indigo-900" title="Unduh (Belum diimplementasi)">
                    <i class="fas fa-download"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="mt-8 pt-6 border-t border-gray-200">
        <a href="{{ route('email.create', ['reply_to' => $fromEmail, 'subject' => 'Re: ' . $email->getSubject()]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-reply mr-2 text-gray-400"></i> Balas
        </a>
    </div>
</div>
@endsection
