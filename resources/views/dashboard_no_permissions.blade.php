@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Message for Users Without Permissions -->
    <div class="text-center py-12">
        <div class="max-w-2xl mx-auto">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-8 text-white">
                <div class="flex justify-center mb-6">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold mb-4">Selamat Datang di AYP SISTEM</h1>
                <p class="text-xl opacity-90 mb-6">Sistem Manajemen Terpadu</p>
                <div class="bg-white bg-opacity-20 rounded-lg p-6">
                    <p class="text-lg">
                        Anda telah berhasil login ke dalam sistem. Saat ini akun Anda sedang dalam proses setup permission akses.
                    </p>
                    <p class="text-sm mt-4 opacity-80">
                        Silakan hubungi administrator untuk mengatur permission akses Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Akun</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Nama Lengkap</label>
                    <p class="text-gray-900">{{ Auth::user()->karyawan->nama_lengkap ?? Auth::user()->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Username</label>
                    <p class="text-gray-900">{{ Auth::user()->username }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Email</label>
                    <p class="text-gray-900">{{ Auth::user()->karyawan->email ?? Auth::user()->email ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">NIK</label>
                    <p class="text-gray-900">{{ Auth::user()->karyawan->nik ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Divisi</label>
                    <p class="text-gray-900">{{ Auth::user()->karyawan->divisi ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Pekerjaan</label>
                    <p class="text-gray-900">{{ Auth::user()->karyawan->pekerjaan ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">No. HP</label>
                    <p class="text-gray-900">{{ Auth::user()->karyawan->no_hp ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Menunggu Setup Permission
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Card -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-800">Butuh Bantuan?</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        Jika Anda mengalami kesulitan atau memiliki pertanyaan tentang sistem ini,
                        silakan hubungi administrator atau tim IT untuk mendapatkan bantuan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $authUser = auth()->user();
    $karyawan = $authUser->karyawan;
    $isAuthorizedApprover = false;
    
    if ($karyawan) {
        $pekerjaan = strtoupper($karyawan->pekerjaan ?? '');
        if (in_array($pekerjaan, ['HRD', 'IT'])) {
            $isAuthorizedApprover = true;
        } else {
            // Check if they are a supervisor (have subordinates)
            $subordinatesCount = \App\Models\Karyawan::where('nik_supervisor', $karyawan->nik)->count();
            if ($subordinatesCount > 0) {
                $isAuthorizedApprover = true;
            }
        }
    }
    
    // Also allow super-admin or specific users
    if ($authUser->hasRole('super-admin') || $authUser->username === 'kiky') {
        $isAuthorizedApprover = true;
    }
@endphp

@if($isAuthorizedApprover)
    <!-- Modal Notifikasi Persetujuan Absensi -->
    <div id="approvalNotifModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="bg-blue-600 px-4 py-4 flex items-center justify-center relative">
                <div class="absolute -top-6 -right-6 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-2 shadow-inner">
                        <i class="fas fa-bell text-3xl text-blue-600 animate-pulse"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white text-center">Permintaan Persetujuan</h3>
                </div>
            </div>
            
            <div class="p-6 text-center">
                <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                    Terdapat <strong id="approvalNotifCount" class="text-blue-600 text-lg">0</strong> permohonan izin/absensi yang menunggu persetujuan Anda.
                </p>
                
                <div class="flex flex-col gap-2">
                    <a href="{{ route('master.persetujuan-absensi.index') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow transition-colors flex items-center justify-center">
                        <i class="fas fa-external-link-alt mr-2"></i> Tinjau Sekarang
                    </a>
                    <button onclick="closeApprovalNotifModal()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        Nanti Saja
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // Check session storage so we don't spam the user every time they go to dashboard
            if (sessionStorage.getItem('approval_notif_shown')) {
                return;
            }

            try {
                // Fetch attendance requests
                const resAtt = await fetch('{{ url("/master/api/admin/pending-attendance") }}');
                const dataAtt = await resAtt.json();
                
                // Fetch permission/leave requests
                const resPerm = await fetch('{{ url("/master/api/admin/pending-permissions") }}');
                const dataPerm = await resPerm.json();
                
                const totalPending = dataAtt.length + dataPerm.length;
                
                if (totalPending > 0) {
                    const modal = document.getElementById('approvalNotifModal');
                    document.getElementById('approvalNotifCount').innerText = totalPending;
                    
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    
                    // Trigger animation
                    setTimeout(() => {
                        modal.classList.remove('opacity-0');
                        modal.querySelector('.transform').classList.remove('scale-95');
                        modal.querySelector('.transform').classList.add('scale-100');
                    }, 50);
                    
                    // Mark as shown for this session
                    sessionStorage.setItem('approval_notif_shown', 'true');
                }
            } catch (err) {
                console.error('Error fetching pending approvals:', err);
            }
        });

        function closeApprovalNotifModal() {
            const modal = document.getElementById('approvalNotifModal');
            modal.classList.add('opacity-0');
            modal.querySelector('.transform').classList.remove('scale-100');
            modal.querySelector('.transform').classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
@endif

@endsection
