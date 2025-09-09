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
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-200">
                <h2 class="text-lg font-semibold text-yellow-800">
                    <i class="fas fa-clock text-yellow-600 mr-2"></i>
                    Registrasi Menunggu Persetujuan ({{ $pendingUsers->count() }})
                </h2>
            </div>

            @if($pendingUsers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pendingUsers as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-yellow-500 flex items-center justify-center">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->karyawan->nama_lengkap ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->karyawan->divisi ?? 'N/A' }} - {{ $user->karyawan->pekerjaan ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('d/m/Y H:i') }}
                                        <div class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $user->registration_reason }}">
                                            {{ Str::limit($user->registration_reason, 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <form method="POST" action="{{ route('admin.user-approval.approve', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs transition-colors duration-200" onclick="return confirm('Setujui registrasi user {{ $user->name }}?')">
                                                <i class="fas fa-check mr-1"></i>Setujui
                                            </button>
                                        </form>
                                        <button onclick="showRejectModal({{ $user->id }}, '{{ $user->name }}')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs transition-colors duration-200">
                                            <i class="fas fa-times mr-1"></i>Tolak
                                        </button>
                                        <a href="{{ route('admin.user-approval.show', $user) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition-colors duration-200">
                                            <i class="fas fa-eye mr-1"></i>Detail
                                        </a>
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
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                <h2 class="text-lg font-semibold text-green-800">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    User yang Disetujui ({{ $approvedUsers->count() }})
                </h2>
            </div>

            @if($approvedUsers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disetujui</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disetujui Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($approvedUsers as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->karyawan->nama_lengkap ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->karyawan->divisi ?? 'N/A' }} - {{ $user->karyawan->pekerjaan ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->approved_at ? $user->approved_at->format('d/m/Y H:i') : 'N/A' }}
                                        @if($user->approved_at)
                                            <div class="text-xs text-gray-400">{{ $user->approved_at->diffForHumans() }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                <h2 class="text-lg font-semibold text-red-800">
                    <i class="fas fa-times-circle text-red-600 mr-2"></i>
                    User yang Ditolak ({{ $rejectedUsers->count() }})
                </h2>
            </div>

            @if($rejectedUsers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ditolak</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ditolak Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rejectedUsers as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-red-500 flex items-center justify-center">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->karyawan->nama_lengkap ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->karyawan->divisi ?? 'N/A' }} - {{ $user->karyawan->pekerjaan ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->approved_at ? $user->approved_at->format('d/m/Y H:i') : 'N/A' }}
                                        @if($user->approved_at)
                                            <div class="text-xs text-gray-400">{{ $user->approved_at->diffForHumans() }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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
