@extends('layouts.app')

@section('title', 'Detail User - ' . $user->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-10 w-10 rounded-lg bg-blue-600 flex items-center justify-center">
                            <i class="fas fa-user-check text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Detail Registrasi User</h1>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm sm:text-base">Informasi lengkap tentang registrasi user {{ $user->name }}</p>
                </div>
                <a href="{{ route('admin.user-approval.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-600"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle mr-3 text-red-600"></i>
                    <span class="font-medium">Terjadi kesalahan:</span>
                </div>
                <ul class="list-disc list-inside ml-6 space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            {{-- Status & Action Card --}}
            <div class="xl:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <div class="text-center">
                            {{-- Status Badge --}}
                            <div class="mb-6">
                                @if($user->status === 'pending')
                                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <i class="fas fa-clock mr-2"></i>Menunggu Persetujuan
                                    </div>
                                @elseif($user->status === 'approved')
                                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check-circle mr-2"></i>Disetujui
                                    </div>
                                @elseif($user->status === 'rejected')
                                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-times-circle mr-2"></i>Ditolak
                                    </div>
                                @endif
                            </div>

                            {{-- User Avatar --}}
                            <div class="mb-6">
                                <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center shadow-lg
                                    @if($user->status === 'pending') bg-gradient-to-br from-yellow-400 to-yellow-600
                                    @elseif($user->status === 'approved') bg-gradient-to-br from-green-400 to-green-600
                                    @elseif($user->status === 'rejected') bg-gradient-to-br from-red-400 to-red-600
                                    @endif">
                                    <i class="fas fa-user text-white text-2xl"></i>
                                </div>
                            </div>

                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $user->name }}</h3>
                            <p class="text-gray-600 text-sm mb-6">{{ $user->username }}</p>

                            {{-- Action Buttons --}}
                            @if($user->status === 'pending')
                                <div class="space-y-3">
                                    <form method="POST" action="{{ route('admin.user-approval.approve', $user) }}" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md" onclick="return confirm('Setujui registrasi user {{ $user->name }}?')">
                                            <i class="fas fa-check mr-2"></i>Setujui User
                                        </button>
                                    </form>
                                    <button onclick="showRejectModal({{ $user->id }}, '{{ $user->name }}')" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fas fa-times mr-2"></i>Tolak User
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Quick Info Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-6">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                            Informasi Cepat
                        </h4>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600 font-medium">Status</span>
                                <span class="font-semibold capitalize">{{ ucfirst($user->status) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600 font-medium">Tanggal Daftar</span>
                                <span class="font-semibold">{{ $user->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if($user->approved_at)
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-gray-600 font-medium">{{ $user->status === 'approved' ? 'Disetujui' : 'Ditolak' }}</span>
                                    <span class="font-semibold">{{ $user->approved_at->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if($user->approvedBy)
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-gray-600 font-medium">{{ $user->status === 'approved' ? 'Disetujui' : 'Ditolak' }} Oleh</span>
                                    <span class="font-semibold">{{ $user->approvedBy->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Information --}}
            <div class="xl:col-span-3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Tab Navigation --}}
                    <div class="border-b border-gray-200 bg-gray-50">
                        <nav class="flex space-x-6 px-6 overflow-x-auto scrollbar-hide" aria-label="Tabs" style="scrollbar-width: none; -ms-overflow-style: none;">
                            <style>
                                .scrollbar-hide::-webkit-scrollbar {
                                    display: none;
                                }
                            </style>
                            <button onclick="showDetailTab('akun')" id="tab-akun" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center min-w-max">
                                <i class="fas fa-user-shield mr-2"></i>Informasi Akun
                            </button>
                            <button onclick="showDetailTab('pribadi')" id="tab-pribadi" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center min-w-max">
                                <i class="fas fa-user mr-2"></i>Data Pribadi
                            </button>
                            <button onclick="showDetailTab('alamat')" id="tab-alamat" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center min-w-max">
                                <i class="fas fa-map-marker-alt mr-2"></i>Data Alamat
                            </button>
                            <button onclick="showDetailTab('pekerjaan')" id="tab-pekerjaan" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center min-w-max">
                                <i class="fas fa-briefcase mr-2"></i>Data Pekerjaan & Riwayat
                            </button>
                            <button onclick="showDetailTab('bank')" id="tab-bank" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center min-w-max">
                                <i class="fas fa-university mr-2"></i>Data Bank
                            </button>
                            <button onclick="showDetailTab('pajak')" id="tab-pajak" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center min-w-max">
                                <i class="fas fa-file-invoice-dollar mr-2"></i>Data Pajak & JKN
                            </button>
                            <button onclick="showDetailTab('audit')" id="tab-audit" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center min-w-max">
                                <i class="fas fa-history mr-2"></i>Audit Trail
                            </button>
                        </nav>
                    </div>

                {{-- Informasi Akun Tab Content --}}
                <div id="akun-tab" class="detail-tab-content p-8">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-user-shield mr-3 text-blue-600"></i>
                            Informasi Akun User
                        </h4>
                        <p class="text-gray-600">Detail informasi akun pengguna yang mendaftar</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                            <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->name }}</div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Username</label>
                            <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->username }}</div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Status Account</label>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                @if($user->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <i class="fas fa-clock mr-2"></i>Pending
                                    </span>
                                @elseif($user->status === 'approved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check mr-2"></i>Approved
                                    </span>
                                @elseif($user->status === 'rejected')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-times mr-2"></i>Rejected
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Tanggal Registrasi</label>
                            <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="font-medium">{{ $user->created_at->format('d/m/Y H:i:s') }}</div>
                                <div class="text-sm text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-comment-alt mr-2 text-blue-600"></i>
                            Alasan Registrasi
                        </label>
                        <div class="text-base text-gray-900 bg-white p-4 rounded-lg border border-blue-200 leading-relaxed">
                            {{ $user->registration_reason ?: 'Tidak ada alasan yang diberikan' }}
                        </div>
                    </div>
                </div>

                {{-- Data Pribadi Tab Content --}}
                <div id="pribadi-tab" class="detail-tab-content p-8">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-user-circle mr-3 text-blue-600"></i>
                            Data Pribadi
                        </h4>
                        <p class="text-gray-600">Informasi pribadi karyawan</p>
                    </div>

                    @if($user->karyawan)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
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
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->jenis_kelamin ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Status Perkawinan</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->status_perkawinan ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Agama</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->agama ?: '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-24 h-24 mx-auto mb-6 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl"></i>
                            </div>
                            <h5 class="text-xl font-semibold text-gray-900 mb-2">Data Karyawan Tidak Ditemukan</h5>
                            <p class="text-gray-600">Data karyawan tidak ditemukan atau tidak terhubung dengan akun ini.</p>
                        </div>
                    @endif
                </div>

                {{-- Data Alamat Tab Content --}}
                <div id="alamat-tab" class="detail-tab-content p-8 hidden">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-map-marker-alt mr-3 text-green-600"></i>
                            Data Alamat
                        </h4>
                        <p class="text-gray-600">Informasi alamat lengkap karyawan</p>
                    </div>

                    @if($user->karyawan && ($user->karyawan->alamat_lengkap || $user->karyawan->ktp || $user->karyawan->kk))
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">No. KTP</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->ktp ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">No. KK</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->kk ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Alamat</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->alamat ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">RT/RW</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->rt_rw ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Kelurahan</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->kelurahan ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Kecamatan</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->kecamatan ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Kabupaten</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->kabupaten ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Provinsi</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->provinsi ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Kode Pos</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->kode_pos ?: '-' }}</div>
                            </div>
                        </div>

                        @if($user->karyawan->alamat_lengkap)
                            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-home mr-2 text-green-600"></i>
                                    Alamat Lengkap
                                </label>
                                <div class="text-base text-gray-900 bg-white p-4 rounded-lg border border-green-200 leading-relaxed">
                                    {{ $user->karyawan->alamat_lengkap }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-16">
                            <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-gray-400 text-3xl"></i>
                            </div>
                            <h5 class="text-xl font-semibold text-gray-900 mb-2">Data Alamat Tidak Tersedia</h5>
                            <p class="text-gray-600">Informasi alamat belum diisi atau tidak tersedia.</p>
                        </div>
                    @endif
                </div>

                {{-- Data Pekerjaan & Riwayat Tab Content --}}
                <div id="pekerjaan-tab" class="detail-tab-content p-8 hidden">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-briefcase mr-3 text-purple-600"></i>
                            Data Pekerjaan & Riwayat
                        </h4>
                        <p class="text-gray-600">Informasi pekerjaan dan riwayat kerja karyawan</p>
                    </div>

                    @if($user->karyawan)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
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
                                <label class="block text-sm font-semibold text-gray-700">Tanggal Berhenti</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    {{ $user->karyawan->tanggal_berhenti ? \Carbon\Carbon::parse($user->karyawan->tanggal_berhenti)->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Tanggal Masuk Sebelumnya</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    {{ $user->karyawan->tanggal_masuk_sebelumnya ? \Carbon\Carbon::parse($user->karyawan->tanggal_masuk_sebelumnya)->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Tanggal Berhenti Sebelumnya</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    {{ $user->karyawan->tanggal_berhenti_sebelumnya ? \Carbon\Carbon::parse($user->karyawan->tanggal_berhenti_sebelumnya)->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">NIK Supervisor</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->nik_supervisor ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Supervisor</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->supervisor ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Cabang</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->cabang ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Plat Nomor</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->plat ?: '-' }}</div>
                            </div>
                        </div>

                        @if($user->karyawan->catatan)
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                                <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-sticky-note mr-2 text-purple-600"></i>
                                    Catatan
                                </label>
                                <div class="text-base text-gray-900 bg-white p-4 rounded-lg border border-purple-200 leading-relaxed">
                                    {{ $user->karyawan->catatan }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-16">
                            <div class="w-24 h-24 mx-auto mb-6 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl"></i>
                            </div>
                            <h5 class="text-xl font-semibold text-gray-900 mb-2">Data Pekerjaan Tidak Ditemukan</h5>
                            <p class="text-gray-600">Data pekerjaan tidak ditemukan atau tidak terhubung dengan akun ini.</p>
                        </div>
                    @endif
                </div>

                {{-- Data Bank Tab Content --}}
                <div id="bank-tab" class="detail-tab-content p-8 hidden">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-university mr-3 text-indigo-600"></i>
                            Data Bank
                        </h4>
                        <p class="text-gray-600">Informasi rekening bank karyawan</p>
                    </div>

                    @if($user->karyawan)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Nama Bank</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->nama_bank ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Cabang Bank</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->bank_cabang ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Nomor Rekening</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->akun_bank ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Atas Nama</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->atas_nama ?: '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-24 h-24 mx-auto mb-6 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-university text-blue-600 text-3xl"></i>
                            </div>
                            <h5 class="text-xl font-semibold text-gray-900 mb-2">Data Bank Belum Tersedia</h5>
                            <p class="text-gray-600">Informasi rekening bank akan ditampilkan di sini ketika tersedia.</p>
                        </div>
                    @endif
                </div>

                {{-- Data Pajak & JKN Tab Content --}}
                <div id="pajak-tab" class="detail-tab-content p-8 hidden">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-file-invoice-dollar mr-3 text-orange-600"></i>
                            Data Pajak & JKN
                        </h4>
                        <p class="text-gray-600">Informasi pajak dan Jaminan Kesehatan Nasional karyawan</p>
                    </div>

                    @if($user->karyawan)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Status Pajak</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->status_pajak ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Nomor JKN/BPJS Kesehatan</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->jkn ?: '-' }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">No. Ketenagakerjaan</label>
                                <div class="text-base text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ $user->karyawan->no_ketenagakerjaan ?: '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-24 h-24 mx-auto mb-6 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-invoice-dollar text-orange-600 text-3xl"></i>
                            </div>
                            <h5 class="text-xl font-semibold text-gray-900 mb-2">Data Pajak & JKN Belum Tersedia</h5>
                            <p class="text-gray-600">Informasi pajak dan JKN akan ditampilkan di sini ketika tersedia.</p>
                        </div>
                    @endif
                </div>

                {{-- Audit Trail Tab Content --}}
                <div id="audit-tab" class="detail-tab-content p-8 hidden">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-timeline mr-3 text-purple-600"></i>
                            Riwayat Perubahan Status
                        </h4>
                        <p class="text-gray-600">Timeline lengkap perubahan status akun user</p>
                    </div>

                    <div class="flow-root">
                        <ul role="list" class="space-y-6">
                            {{-- Registration Event --}}
                            <li class="relative">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center shadow-lg">
                                            <i class="fas fa-user-plus text-white text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                                            <div class="flex items-center justify-between mb-3">
                                                <h5 class="text-lg font-semibold text-gray-900">Registrasi User</h5>
                                                <time class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full" datetime="{{ $user->created_at->toISOString() }}">
                                                    {{ $user->created_at->format('d/m/Y H:i') }}
                                                </time>
                                            </div>
                                            <p class="text-gray-700 mb-3">
                                                User mendaftar dengan username <span class="font-semibold text-blue-600">{{ $user->username }}</span>
                                            </p>
                                            @if($user->registration_reason)
                                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                                    <p class="text-sm font-medium text-blue-900 mb-2">Alasan Registrasi:</p>
                                                    <p class="text-sm text-blue-800">{{ $user->registration_reason }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($user->approved_at && $user->approvedBy)
                                    <div class="absolute left-6 top-16 bottom-0 w-0.5 bg-gray-200"></div>
                                @endif
                            </li>

                            {{-- Approval/Rejection Event --}}
                            @if($user->approved_at && $user->approvedBy)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                @if($user->status === 'approved')
                                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                        <i class="fas fa-check text-white text-sm"></i>
                                                    </span>
                                                @else
                                                    <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                        <i class="fas fa-times text-white text-sm"></i>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">
                                                        User {{ $user->status === 'approved' ? 'disetujui' : 'ditolak' }} oleh
                                                        <span class="font-medium">{{ $user->approvedBy->name }}</span>
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time datetime="{{ $user->approved_at->toISOString() }}">
                                                        {{ $user->approved_at->format('d/m/Y H:i') }}
                                                    </time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            {{-- Current Status --}}
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            @if($user->status === 'pending')
                                                <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-clock text-white text-sm"></i>
                                                </span>
                                            @elseif($user->status === 'approved')
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-check-circle text-white text-sm"></i>
                                                </span>
                                            @else
                                                <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-times-circle text-white text-sm"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm text-gray-900">
                                                Status saat ini:
                                                <span class="font-medium">
                                                    @if($user->status === 'pending')
                                                        Menunggu persetujuan
                                                    @elseif($user->status === 'approved')
                                                        User dapat menggunakan sistem
                                                    @else
                                                        Akses ditolak
                                                    @endif
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-xl bg-white">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <i class="fas fa-times text-red-600 text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Tolak Registrasi User</h3>
            <p class="text-gray-600 mb-6">
                Anda akan menolak registrasi user <span id="rejectUserName" class="font-semibold text-red-600"></span>.
                Berikan alasan penolakan (opsional):
            </p>

            <form id="rejectForm" method="POST" action="" class="space-y-4">
                @csrf
                <div class="text-left">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Penolakan</label>
                    <textarea
                        name="rejection_reason"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                        rows="4"
                        placeholder="Tuliskan alasan penolakan (opsional)..."
                    ></textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeRejectModal()" class="px-6 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm">
                        <i class="fas fa-times mr-2"></i>Tolak User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showDetailTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.detail-tab-content').forEach(tab => {
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
    showDetailTab('akun');
});
</script>
@endpush

@endsection
