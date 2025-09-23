@extends('layouts.app')

@section('title', 'Persetujuan Registrasi User')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Persetujuan Registrasi User</h1>
        <p class="text-gray-600">Kelola persetujuan akun user yang mendaftar di sistem</p>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Tabs --}}
    <div class="mb-6">
        <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
            <button onclick="showTab('pending')" id="tab-pending" class="flex-1 py-2 px-4 text-sm font-medium text-center rounded-md transition-colors duration-200">
                Menunggu Persetujuan
                @if($pendingUsers->count() > 0)
                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full ml-2">{{ $pendingUsers->count() }}</span>
                @endif
            </button>
            <button onclick="showTab('approved')" id="tab-approved" class="flex-1 py-2 px-4 text-sm font-medium text-center rounded-md transition-colors duration-200">
                Disetujui
            </button>
            <button onclick="showTab('rejected')" id="tab-rejected" class="flex-1 py-2 px-4 text-sm font-medium text-center rounded-md transition-colors duration-200">
                Ditolak
            </button>
        </div>
    </div>

    {{-- Pending Users Tab --}}
    <div id="pending-tab" class="tab-content">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Registrasi Menunggu Persetujuan ({{ $pendingUsers->count() }})</h3>
            </div>

            @if($pendingUsers->count() > 0)
                <div class="overflow-x-auto table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-[10px]">
                            @foreach($pendingUsers as $index => $user)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->username }}</div>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="text-sm text-gray-900">{{ $user->karyawan->nama_lengkap ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-3 text-[10px]">
                                            <form method="POST" action="{{ route('admin.user-approval.approve', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-800 hover:underline font-medium cursor-pointer border-none bg-transparent p-0" onclick="return confirm('Setujui registrasi user {{ $user->name }}?')">
                                                    Setujui
                                                </button>
                                            </form>
                                            <span class="text-gray-300">|</span>
                                            <button onclick="showRejectModal({{ $user->id }}, '{{ $user->name }}')" class="text-red-600 hover:text-red-800 hover:underline font-medium cursor-pointer border-none bg-transparent p-0">
                                                Tolak
                                            </button>
                                            <span class="text-gray-300">|</span>
                                            <a href="{{ route('admin.user-approval.show', $user) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
                    <p class="text-gray-500">Tidak ada registrasi user yang menunggu persetujuan</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Approved Users Tab --}}
    <div id="approved-tab" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">User yang Disetujui ({{ $approvedUsers->count() }})</h3>
            </div>

            @if($approvedUsers->count() > 0)
                <div class="overflow-x-auto table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disetujui</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disetujui Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-[10px]">
                            @foreach($approvedUsers as $index => $user)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->username }}</div>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="text-sm text-gray-900">{{ $user->karyawan->nama_lengkap ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->approved_at ? $user->approved_at->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->approvedBy->name ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Belum ada user yang disetujui</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Rejected Users Tab --}}
    <div id="rejected-tab" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">User yang Ditolak ({{ $rejectedUsers->count() }})</h3>
            </div>

            @if($rejectedUsers->count() > 0)
                <div class="overflow-x-auto table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="sticky-table-header bg-gray-50 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ditolak</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ditolak Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-[10px]">
                            @foreach($rejectedUsers as $index => $user)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->username }}</div>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="text-sm text-gray-900">{{ $user->karyawan->nama_lengkap ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->approved_at ? $user->approved_at->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->approvedBy->name ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-ban text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Belum ada user yang ditolak</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-times text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-5">Tolak Registrasi User</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Anda akan menolak registrasi user <span id="rejectUserName" class="font-semibold"></span>.
                    Berikan alasan penolakan (opsional):
                </p>
                <form id="rejectForm" method="POST" action="" class="mt-4">
                    @csrf
                    <textarea
                        name="rejection_reason"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                        rows="3"
                        placeholder="Alasan penolakan (opsional)..."
                    ></textarea>
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-200">
                            Tolak User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });

    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');

    // Update tab buttons
    document.querySelectorAll('[id^="tab-"]').forEach(btn => {
        btn.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
        btn.classList.add('text-gray-500', 'hover:text-gray-700');
    });

    document.getElementById('tab-' + tabName).classList.add('bg-white', 'text-blue-600', 'shadow-sm');
    document.getElementById('tab-' + tabName).classList.remove('text-gray-500', 'hover:text-gray-700');
}

function showRejectModal(userId, userName) {
    document.getElementById('rejectUserName').textContent = userName;
    document.getElementById('rejectForm').action = `/admin/user-approval/${userId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.querySelector('textarea[name="rejection_reason"]').value = '';
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    showTab('pending');
});
</script>
@endpush

@endsection

<style>
/* Sticky Table Header Styles */
.sticky-table-header {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: rgb(249 250 251); /* bg-gray-50 */
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
}

/* Enhanced table container for better scrolling */
.table-container {
    max-height: calc(100vh - 300px); /* Adjust based on your layout */
    overflow-y: auto;
    border: 1px solid rgb(229 231 235); /* border-gray-200 */
    border-radius: 0.5rem;
}

/* Smooth scrolling for better UX */
.table-container {
    scroll-behavior: smooth;
}

/* Table header cells need specific background to avoid transparency issues */
.sticky-table-header th {
    background-color: rgb(249 250 251) !important;
    border-bottom: 1px solid rgb(229 231 235);
}

/* Optional: Add a subtle border when scrolling */
.table-container.scrolled .sticky-table-header {
    border-bottom: 2px solid rgb(59 130 246); /* blue-500 */
}

/* Ensure dropdown menus appear above sticky header */
.relative.group .absolute {
    z-index: 20;
}
</style>
