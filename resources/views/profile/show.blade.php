@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-10 w-10 rounded-lg bg-blue-600 flex items-center justify-center">
                            <i class="fas fa-user-circle text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Profil Saya</h1>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm sm:text-base">Kelola informasi profil dan pengaturan akun Anda</p>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-600"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Profile Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 text-center bg-gradient-to-br from-blue-50 to-indigo-50">
                        {{-- Avatar --}}
                        <div class="mb-6">
                            <div class="w-24 h-24 rounded-full mx-auto flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg">
                                <i class="fas fa-user text-white text-3xl"></i>
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $user->karyawan->nama_lengkap ?? $user->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $user->username }}</p>

                        {{-- Status Badge --}}
                        @if($user->status === 'approved')
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                <i class="fas fa-check-circle mr-2"></i>Akun Aktif
                            </div>
                        @elseif($user->status === 'pending')
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                <i class="fas fa-clock mr-2"></i>Menunggu Persetujuan
                            </div>
                        @else
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                                <i class="fas fa-times-circle mr-2"></i>Tidak Aktif
                            </div>
                        @endif
                    </div>

                    {{-- Quick Info --}}
                    <div class="p-6 border-t border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                            Informasi Akun
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Bergabung</span>
                                <span class="font-medium text-sm">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Terakhir Update</span>
                                <span class="font-medium text-sm">{{ $user->updated_at ? $user->updated_at->format('d/m/Y') : '-' }}</span>
                            </div>
                            @if($user->karyawan)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm">NIK</span>
                                    <span class="font-medium text-sm">{{ $user->karyawan->nik ?: '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm">No. HP</span>
                                    <span class="font-medium text-sm">{{ $user->karyawan->no_hp ?: '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm">Divisi</span>
                                    <span class="font-medium text-sm">{{ $user->karyawan->divisi ?: '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm">Pekerjaan</span>
                                    <span class="font-medium text-sm">{{ $user->karyawan->pekerjaan ?: '-' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Information --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Tab Navigation --}}
                    <div class="border-b border-gray-200 bg-gray-50">
                        <nav class="flex space-x-8 px-6" aria-label="Tabs">
                            <button onclick="showProfileTab('account')" id="tab-account" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center">
                                <i class="fas fa-user-cog mr-2"></i>Data Akun
                            </button>
                            @if($user->karyawan)
                                <button onclick="showProfileTab('personal')" id="tab-personal" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center">
                                    <i class="fas fa-id-card mr-2"></i>Data Pribadi
                                </button>
                            @endif
                            <button onclick="showProfileTab('security')" id="tab-security" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center">
                                <i class="fas fa-shield-alt mr-2"></i>Keamanan
                            </button>
                        </nav>
                    </div>

                    {{-- Account Info Tab Content --}}
                    <div id="account-tab" class="profile-tab-content p-8">
                        <div class="mb-6">
                            <h4 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                                <i class="fas fa-user-circle mr-3 text-blue-600"></i>
                                Informasi Akun
                            </h4>
                            <p class="text-gray-600">Informasi dasar akun pengguna sistem</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->name }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Username</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->username }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Status Akun</label>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    @if($user->status === 'approved')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                            <i class="fas fa-check mr-2"></i>Disetujui
                                        </span>
                                    @elseif($user->status === 'pending')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <i class="fas fa-clock mr-2"></i>Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                                            <i class="fas fa-times mr-2"></i>Ditolak
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Tanggal Bergabung</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <div class="font-medium">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : '-' }}</div>
                                    <div class="text-sm text-gray-500 mt-1">{{ $user->created_at ? $user->created_at->diffForHumans() : '-' }}</div>
                                </div>
                            </div>
                        </div>

                        @if($user->registration_reason)
                            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                                <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-comment-alt mr-2 text-blue-600"></i>
                                    Alasan Registrasi
                                </label>
                                <div class="text-base text-gray-900 bg-white p-4 rounded-lg border border-blue-200 leading-relaxed">
                                    {{ $user->registration_reason }}
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Personal Info Tab Content --}}
                    @if($user->karyawan)
                        <div id="personal-tab" class="profile-tab-content p-8 hidden">
                            <div class="mb-6">
                                <h4 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                                    <i class="fas fa-id-badge mr-3 text-green-600"></i>
                                    Data Pribadi
                                </h4>
                                <p class="text-gray-600">Informasi pribadi karyawan terkait</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">NIK</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->nik ?: '-' }}</div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->nama_lengkap ?: '-' }}</div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Nama Panggilan</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->nama_panggilan ?: '-' }}</div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Email</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->email ?: '-' }}</div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">No. HP</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->no_hp ?: '-' }}</div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Tempat Lahir</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->tempat_lahir ?: '-' }}</div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Tanggal Lahir</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        {{ $user->karyawan->tanggal_lahir ? \Carbon\Carbon::parse($user->karyawan->tanggal_lahir)->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Jenis Kelamin</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        {{ $user->karyawan->jenis_kelamin ? ($user->karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') : '-' }}
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Agama</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->agama ?: '-' }}</div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Status Perkawinan</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->status_perkawinan ?: '-' }}</div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Divisi</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->divisi ?: '-' }}</div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Pekerjaan</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->pekerjaan ?: '-' }}</div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Tanggal Masuk</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        {{ $user->karyawan->tanggal_masuk ? \Carbon\Carbon::parse($user->karyawan->tanggal_masuk)->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">No. Ketenagakerjaan</label>
                                    <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->no_ketenagakerjaan ?: '-' }}</div>
                                </div>
                            </div>

                            @if($user->karyawan->alamat_lengkap)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                    <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-map-marker-alt mr-2 text-green-600"></i>
                                        Alamat Lengkap
                                    </label>
                                    <div class="text-base text-gray-900 bg-white p-4 rounded-lg border border-green-200 leading-relaxed">
                                        {{ $user->karyawan->alamat_lengkap }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Security Tab Content --}}
                    <div id="security-tab" class="profile-tab-content p-8 hidden">
                        <div class="mb-6">
                            <h4 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                                <i class="fas fa-shield-alt mr-3 text-purple-600"></i>
                                Keamanan Akun
                            </h4>
                            <p class="text-gray-600">Pengaturan keamanan dan privasi akun</p>
                        </div>

                        <div class="space-y-6">
                            {{-- Password Section --}}
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h5 class="text-lg font-semibold text-gray-900 flex items-center">
                                            <i class="fas fa-key mr-2 text-purple-600"></i>
                                            Kata Sandi
                                        </h5>
                                        <p class="text-gray-600 text-sm">Terakhir diubah: {{ $user->updated_at ? $user->updated_at->format('d/m/Y') : '-' }}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-purple-800">
                                    Pastikan kata sandi Anda kuat dan unik untuk menjaga keamanan akun.
                                </div>
                            </div>

                            {{-- Login History Placeholder --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                                <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-history mr-2 text-gray-600"></i>
                                    Aktivitas Login
                                </h5>
                                <div class="text-center py-8">
                                    <i class="fas fa-clock text-gray-400 text-3xl mb-4"></i>
                                    <p class="text-gray-500">Riwayat login akan ditampilkan di sini</p>
                                    <p class="text-gray-400 text-sm">Fitur ini akan segera tersedia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showProfileTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.profile-tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });

    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');

    // Update tab buttons
    document.querySelectorAll('[id^="tab-"]').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });

    document.getElementById('tab-' + tabName).classList.add('border-blue-500', 'text-blue-600');
    document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    showProfileTab('account');
});
</script>
@endpush

@endsection
