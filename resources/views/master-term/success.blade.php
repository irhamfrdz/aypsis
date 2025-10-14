@extends('layouts.app')

@section('title', 'Term Berhasil Ditambahkan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 flex items-center justify-center">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-xl font-semibold text-gray-900 mb-2">Term Berhasil Ditambahkan!</h1>
            <p class="text-gray-600 mb-2">Data term "{{ $term->nama_status }}" telah berhasil disimpan.</p>
            <p class="text-sm text-gray-500 mb-6">Window akan menutup otomatis dalam 2 detik...</p>

            <div class="space-y-3">
                <button onclick="closeAndRefresh()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Selesai
                </button>

                <a href="{{ route('term.create') }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Lagi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function closeAndRefresh() {
    console.log('Attempting to close and refresh...'); // Debug log

    // Notify parent window that new term was added
    if (window.opener && !window.opener.closed) {
        console.log('Sending message to parent window...'); // Debug log

        try {
            window.opener.postMessage({
                type: 'term-added',
                data: {
                    id: {{ $term->id }},
                    nama_status: "{{ addslashes($term->nama_status) }}",
                    kode: "{{ addslashes($term->kode) }}"
                }
            }, '*');

            console.log('Message sent successfully'); // Debug log
        } catch (error) {
            console.error('Error sending message:', error);
        }

        // Wait a bit to ensure message is received
        setTimeout(function() {
            window.close();
        }, 500);
    } else {
        console.log('No valid opener window found'); // Debug log
        // If no opener, just close the window
        window.close();
    }
}

// Auto-close after 2 seconds if opened in popup
document.addEventListener('DOMContentLoaded', function() {
    console.log('Success page loaded'); // Debug log
    console.log('Window opener:', window.opener); // Debug log

    // Always try to close after 2 seconds, regardless of opener
    setTimeout(function() {
        closeAndRefresh();
    }, 2000);

    // Also add manual close option
    const closeButton = document.querySelector('button[onclick="closeAndRefresh()"]');
    if (closeButton) {
        closeButton.onclick = function() {
            closeAndRefresh();
        };
    }
});
</script>
@endpush
