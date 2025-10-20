@extends('layouts.app')

@section('title', 'Test Audit Log Modal')
@section('page_title', 'Test Audit Log Modal')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Test Audit Log Modal</h1>

            <p class="text-gray-600 mb-6">Klik tombol di bawah untuk menguji modal audit log:</p>

            <!-- Test Button 1 -->
            <div class="space-y-4">
                <button type="button"
                        class="audit-log-btn inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700"
                        data-model-type="App\Models\TujuanKirim"
                        data-model-id="1"
                        data-item-name="Test Tujuan Kirim">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Test Audit Log Modal (ID: 1)
                </button>

                <!-- Test dengan onclick langsung -->
                <button type="button"
                        onclick="showAuditLog('App\\Models\\TujuanKirim', '2', 'Test Direct Call')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Test Direct Function Call (ID: 2)
                </button>

                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h3 class="font-medium text-yellow-800 mb-2">Debug Info:</h3>
                    <p class="text-sm text-yellow-700">Buka console browser (F12) untuk melihat debug logs.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Audit Log Modal -->
@include('components.audit-log-modal')

@endsection

@push('scripts')
<script>
// Tambahan debugging
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Test page loaded');
    console.log('Available functions:');
    console.log('- showAuditLog:', typeof showAuditLog);
    console.log('- closeAuditLogModal:', typeof closeAuditLogModal);

    // Test CSRF token
    const token = document.querySelector('meta[name="csrf-token"]');
    console.log('CSRF Token available:', !!token);
    if (token) {
        console.log('CSRF Token value:', token.getAttribute('content'));
    }

    // Test buttons
    const buttons = document.querySelectorAll('.audit-log-btn');
    console.log('Audit buttons found:', buttons.length);
});
</script>
@endpush
