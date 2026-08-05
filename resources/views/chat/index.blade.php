@extends('layouts.app')
@section('title', 'Chatbox Management - AYPSIS')
@section('page_title', 'Chatbox Management')

@push('styles')
<style>
    .chat-container {
        height: calc(100vh - 180px);
    }
    .messages-container {
        height: calc(100% - 140px);
        overflow-y: auto;
    }
    .session-item.active {
        background-color: #ebf5ff;
        border-left: 4px solid #3b82f6;
    }
</style>
@endpush

@section('content')
<div class="p-6">
    <!-- Navigation Tabs -->
    <div class="flex space-x-4 mb-4 border-b border-gray-200">
        <a href="{{ route('chat.index') }}" class="py-2 px-4 border-b-2 border-blue-600 font-semibold text-blue-600">
            <i class="fa-solid fa-comments mr-2"></i> Live Chat
        </a>
        <a href="{{ route('chat.faq.index') }}" class="py-2 px-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium">
            <i class="fa-solid fa-robot mr-2"></i> Auto Reply (FAQ)
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex chat-container">
        <!-- Sessions List -->
        <div class="w-1/3 border-r border-gray-200 flex flex-col bg-gray-50">
            <div class="p-4 border-b border-gray-200 bg-white">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <i class="fa-solid fa-comments text-blue-600"></i> Aktif Sesi
                </h3>
            </div>
            <div class="flex-1 overflow-y-auto">
                @forelse($chats as $session)
                <button class="session-item w-full text-left p-4 border-b border-gray-100 hover:bg-gray-100 transition-colors relative" data-session-id="{{ $session->session_id }}" onclick="loadMessages('{{ $session->session_id }}', this)">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-semibold text-sm text-gray-800">{{ $session->name }}</span>
                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($session->last_activity)->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-gray-600 truncate">{{ $session->last_message }}</p>
                    @if($session->unread_count > 0)
                    <span class="absolute top-4 right-4 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $session->unread_count }}</span>
                    @endif
                </button>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fa-regular fa-comment-dots text-4xl mb-3 text-gray-300"></i>
                    <p class="text-sm">Belum ada obrolan masuk.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Chat Area -->
        <div class="w-2/3 flex flex-col bg-white">
            <div id="chat-header" class="p-4 border-b border-gray-200 flex items-center justify-between bg-white hidden">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <h4 id="active-session-name" class="font-bold text-gray-800 text-sm">Session Name</h4>
                        <p id="active-session-id" class="text-xs text-gray-500">ID: ...</p>
                    </div>
                </div>
                <button onclick="clearChat()" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-sm transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i> <span class="font-semibold">Clear Chat</span>
                </button>
            </div>

            <div id="empty-state" class="flex-1 flex flex-col items-center justify-center text-gray-400">
                <i class="fa-regular fa-comments text-6xl mb-4 text-gray-200"></i>
                <p>Pilih sesi obrolan untuk mulai membalas.</p>
            </div>

            <div id="messages-area" class="messages-container p-4 bg-slate-50 hidden flex-col gap-4">
                <!-- Messages will be loaded here -->
            </div>

            <div id="reply-area" class="p-4 bg-white border-t border-gray-200 hidden">
                <form id="reply-form" class="flex gap-2">
                    @csrf
                    <input type="hidden" id="reply-session-id" name="session_id">
                    <input type="text" id="reply-message" name="message" class="flex-1 border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 focus:bg-white transition-colors" placeholder="Ketik balasan Anda di sini..." required autocomplete="off">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl text-sm font-semibold transition-colors flex items-center gap-2 shadow-sm">
                        Kirim <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeSession = null;
    let pollInterval = null;

    function loadMessages(sessionId, element) {
        // Update UI
        document.querySelectorAll('.session-item').forEach(el => el.classList.remove('active'));
        if (element) element.classList.add('active');

        document.getElementById('empty-state').classList.add('hidden');
        document.getElementById('chat-header').classList.remove('hidden');
        document.getElementById('chat-header').classList.add('flex');
        document.getElementById('messages-area').classList.remove('hidden');
        document.getElementById('messages-area').classList.add('flex');
        document.getElementById('reply-area').classList.remove('hidden');

        // Set session data
        activeSession = sessionId;
        document.getElementById('active-session-id').textContent = 'ID: ' + sessionId;
        if(element) {
            document.getElementById('active-session-name').textContent = element.querySelector('span.font-semibold').textContent;
            // Hide badge
            const badge = element.querySelector('.bg-red-500');
            if(badge) badge.style.display = 'none';
        }
        document.getElementById('reply-session-id').value = sessionId;

        fetchMessages(sessionId);
        
        // Start polling
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => {
            if (activeSession === sessionId) fetchMessages(sessionId, false);
        }, 3000);
    }

    function fetchMessages(sessionId, scrollToBottom = true) {
        fetch(`/chat/${sessionId}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('messages-area');
                container.innerHTML = '';
                
                data.messages.forEach(msg => {
                    const isSelf = msg.is_admin == 1 || msg.is_admin === true;
                    const date = new Date(msg.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                    
                    const html = `
                        <div class="flex flex-col max-w-[70%] ${isSelf ? 'self-end items-end' : 'self-start items-start'}">
                            <div class="px-4 py-2 rounded-2xl text-sm shadow-sm ${isSelf ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-100 rounded-bl-none'}">
                                ${msg.message}
                            </div>
                            <span class="text-[10px] text-gray-400 mt-1">${msg.name} • ${date}</span>
                        </div>
                    `;
                    container.innerHTML += html;
                });

                if(scrollToBottom) {
                    container.scrollTop = container.scrollHeight;
                }
            })
            .catch(err => console.error(err));
    }

    function clearChat() {
        if(!activeSession) return;
        if(!confirm('Yakin ingin menghapus seluruh riwayat obrolan pada sesi ini? Tindakan ini tidak dapat dibatalkan.')) return;

        fetch(`/chat/${activeSession}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Hide chat area
                document.getElementById('empty-state').classList.remove('hidden');
                document.getElementById('chat-header').classList.add('hidden');
                document.getElementById('chat-header').classList.remove('flex');
                document.getElementById('messages-area').classList.add('hidden');
                document.getElementById('messages-area').classList.remove('flex');
                document.getElementById('reply-area').classList.add('hidden');
                
                // Remove from sidebar
                const sessionEl = document.querySelector(`.session-item[data-session-id="${activeSession}"]`);
                if(sessionEl) sessionEl.remove();
                
                activeSession = null;
            }
        })
        .catch(err => console.error(err));
    }

    document.getElementById('reply-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const msgInput = document.getElementById('reply-message');
        const message = msgInput.value;
        const sessionId = document.getElementById('reply-session-id').value;

        if(!message.trim()) return;

        fetch('{{ route("chat.reply") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                session_id: sessionId,
                message: message
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                msgInput.value = '';
                fetchMessages(sessionId, true);
            }
        });
    });
</script>
@endpush
