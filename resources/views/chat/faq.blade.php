@extends('layouts.app')
@section('title', 'Chatbox FAQ Management - AYPSIS')
@section('page_title', 'Chatbox Auto Reply (FAQ)')

@section('content')
<div class="p-6">
    <!-- Navigation Tabs -->
    <div class="flex space-x-4 mb-4 border-b border-gray-200">
        <a href="{{ route('chat.index') }}" class="py-2 px-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium">
            <i class="fa-solid fa-comments mr-2"></i> Live Chat
        </a>
        <a href="{{ route('chat.faq.index') }}" class="py-2 px-4 border-b-2 border-blue-600 font-semibold text-blue-600">
            <i class="fa-solid fa-robot mr-2"></i> Auto Reply (FAQ)
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Daftar Pertanyaan Otomatis (FAQ)</h2>
                <p class="text-sm text-gray-500">Pertanyaan ini akan muncul sebagai tombol pilihan bagi pengunjung di Landing Page.</p>
            </div>
            <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fa-solid fa-plus mr-1"></i> Tambah FAQ
            </button>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-t border-gray-200 text-gray-600 text-sm">
                        <th class="p-4 font-semibold">Pertanyaan (Judul Tombol)</th>
                        <th class="p-4 font-semibold">Jawaban Otomatis</th>
                        <th class="p-4 font-semibold text-center">Status Aktif</th>
                        <th class="p-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-sm font-medium text-gray-800 align-top max-w-xs">{{ $faq->question }}</td>
                        <td class="p-4 text-sm text-gray-600 align-top max-w-md whitespace-pre-wrap">{{ $faq->answer }}</td>
                        <td class="p-4 text-center align-top">
                            @if($faq->is_active)
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-green-300">Aktif</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-gray-300">Nonaktif</span>
                            @endif
                        </td>
                        <td class="p-4 text-right align-top">
                            <div class="flex justify-end gap-2">
                                <button onclick="openEditModal({{ $faq->id }}, '{{ addslashes($faq->question) }}', '{{ addslashes($faq->answer) }}', {{ $faq->is_active ? 'true' : 'false' }})" class="text-blue-500 hover:text-blue-700 transition-colors bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('chat.faq.destroy', $faq->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus FAQ ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-sm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500">
                            <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada daftar FAQ. Tambahkan pertanyaan pertama Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="faqModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 transform transition-all">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Tambah FAQ</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="faqForm" action="{{ route('chat.faq.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pertanyaan (Teks Tombol) *</label>
                    <input type="text" name="question" id="inputQuestion" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Contoh: Bagaimana cara order?">
                    <p class="text-xs text-gray-500 mt-1">Gunakan pertanyaan yang singkat dan jelas.</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jawaban Otomatis *</label>
                    <textarea name="answer" id="inputAnswer" required rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Tuliskan jawaban lengkap di sini..."></textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="inputActive" value="1" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                    <label for="inputActive" class="ml-2 text-sm font-medium text-gray-700">Aktifkan (Tampilkan di halaman)</label>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3 bg-gray-50 rounded-b-xl">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-medium text-sm transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition-colors shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openAddModal() {
        document.getElementById('modalTitle').innerText = 'Tambah FAQ Baru';
        document.getElementById('faqForm').action = "{{ route('chat.faq.store') }}";
        document.getElementById('formMethod').value = 'POST';
        
        document.getElementById('inputQuestion').value = '';
        document.getElementById('inputAnswer').value = '';
        document.getElementById('inputActive').checked = true;
        
        document.getElementById('faqModal').classList.remove('hidden');
    }
    
    function openEditModal(id, question, answer, isActive) {
        document.getElementById('modalTitle').innerText = 'Edit FAQ';
        document.getElementById('faqForm').action = "/chat-faqs/" + id;
        document.getElementById('formMethod').value = 'PUT';
        
        document.getElementById('inputQuestion').value = question;
        document.getElementById('inputAnswer').value = answer;
        document.getElementById('inputActive').checked = isActive;
        
        document.getElementById('faqModal').classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('faqModal').classList.add('hidden');
    }
</script>
@endpush
