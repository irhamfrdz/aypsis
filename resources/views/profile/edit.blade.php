@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-10 w-10 rounded-lg bg-blue-600 flex items-center justify-center">
                            <i class="fas fa-user-edit text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit Profil</h1>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm sm:text-base">Perbarui informasi profil dan pengaturan akun Anda</p>
                </div>
                <a href="{{ route('profile.show') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Profile Preview Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-8">
                    <div class="p-6 text-center bg-gradient-to-br from-blue-50 to-indigo-50">
                        {{-- Avatar --}}
                        <div class="mb-6">
                            <div class="w-24 h-24 rounded-full mx-auto flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg">
                                <i class="fas fa-user text-white text-3xl"></i>
                            </div>
                            <button class="mt-3 text-sm text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-camera mr-1"></i>Ubah Foto
                            </button>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $user->name }}</h3>
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

                    {{-- Navigation Menu --}}
                    <div class="p-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Pengaturan</h4>
                        <nav class="space-y-2">
                            <a href="#account" onclick="showEditTab('account')" id="nav-account" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 bg-blue-100 text-blue-700">
                                <i class="fas fa-user-cog mr-3"></i>Data Akun
                            </a>
                            @if($user->karyawan)
                                <a href="#personal" onclick="showEditTab('personal')" id="nav-personal" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-id-card mr-3"></i>Data Pribadi
                                </a>
                            @endif
                            <a href="#password" onclick="showEditTab('password')" id="nav-password" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-key mr-3"></i>Ubah Password
                            </a>
                            <a href="#danger" onclick="showEditTab('danger')" id="nav-danger" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-exclamation-triangle mr-3"></i>Zona Bahaya
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            {{-- Forms Section --}}
            <div class="lg:col-span-2">
                {{-- Account Info Form --}}
                <div id="account-form" class="edit-tab-content bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                        <h4 class="text-lg font-semibold text-blue-900 flex items-center">
                            <i class="fas fa-user-cog mr-2"></i>
                            Edit Data Akun
                        </h4>
                    </div>
                    
                    <form method="POST" action="{{ route('profile.update.account') }}" class="p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-semibold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-300 @enderror" 
                                    required>
                                @error('name')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="username" class="block text-sm font-semibold text-gray-700">Username <span class="text-red-500">*</span></label>
                                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('username') border-red-300 @enderror" 
                                    required>
                                @error('username')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-xs">Username hanya boleh mengandung huruf, angka, titik, dan underscore</p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 border-t border-gray-200 mt-6">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save mr-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Personal Info Form --}}
                @if($user->karyawan)
                    <div id="personal-form" class="edit-tab-content bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6 hidden">
                        <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                            <h4 class="text-lg font-semibold text-green-900 flex items-center">
                                <i class="fas fa-id-card mr-2"></i>
                                Edit Data Pribadi
                            </h4>
                        </div>
                        
                        <form method="POST" action="{{ route('profile.update.personal') }}" class="p-6">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div class="space-y-2">
                                    <label for="nik" class="block text-sm font-semibold text-gray-700">NIK</label>
                                    <input type="text" id="nik" name="nik" value="{{ old('nik', $user->karyawan->nik) }}" maxlength="20"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nik') border-red-300 @enderror">
                                    @error('nik')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-gray-500 text-xs">Nomor Induk Kependudukan (16 digit)</p>
                                </div>

                                <div class="space-y-2">
                                    <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $user->karyawan->nama_lengkap) }}" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama_lengkap') border-red-300 @enderror" 
                                        required>
                                    @error('nama_lengkap')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="nama_panggilan" class="block text-sm font-semibold text-gray-700">Nama Panggilan</label>
                                    <input type="text" id="nama_panggilan" name="nama_panggilan" value="{{ old('nama_panggilan', $user->karyawan->nama_panggilan) }}" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama_panggilan') border-red-300 @enderror">
                                    @error('nama_panggilan')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->karyawan->email) }}" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-300 @enderror">
                                    @error('email')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="no_hp" class="block text-sm font-semibold text-gray-700">No. HP</label>
                                    <input type="tel" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->karyawan->no_hp) }}" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('no_hp') border-red-300 @enderror">
                                    @error('no_hp')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-gray-500 text-xs">Format: 08xx-xxxx-xxxx atau +62xx-xxxx-xxxx</p>
                                </div>

                                <div class="space-y-2">
                                    <label for="tempat_lahir" class="block text-sm font-semibold text-gray-700">Tempat Lahir</label>
                                    <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $user->karyawan->tempat_lahir) }}" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tempat_lahir') border-red-300 @enderror">
                                    @error('tempat_lahir')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-700">Tanggal Lahir</label>
                                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->karyawan->tanggal_lahir ? \Carbon\Carbon::parse($user->karyawan->tanggal_lahir)->format('Y-m-d') : '') }}" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tanggal_lahir') border-red-300 @enderror">
                                    @error('tanggal_lahir')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="jenis_kelamin" class="block text-sm font-semibold text-gray-700">Jenis Kelamin</label>
                                    <select id="jenis_kelamin" name="jenis_kelamin" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jenis_kelamin') border-red-300 @enderror">
                                        <option value="">Pilih jenis kelamin...</option>
                                        <option value="L" {{ old('jenis_kelamin', $user->karyawan->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $user->karyawan->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="agama" class="block text-sm font-semibold text-gray-700">Agama</label>
                                    <select id="agama" name="agama" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('agama') border-red-300 @enderror">
                                        <option value="">Pilih agama...</option>
                                        <option value="Islam" {{ old('agama', $user->karyawan->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="Kristen" {{ old('agama', $user->karyawan->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                        <option value="Katolik" {{ old('agama', $user->karyawan->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                        <option value="Hindu" {{ old('agama', $user->karyawan->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                        <option value="Buddha" {{ old('agama', $user->karyawan->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                        <option value="Khonghucu" {{ old('agama', $user->karyawan->agama) == 'Khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                                    </select>
                                    @error('agama')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="status_perkawinan" class="block text-sm font-semibold text-gray-700">Status Perkawinan</label>
                                    <select id="status_perkawinan" name="status_perkawinan" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('status_perkawinan') border-red-300 @enderror">
                                        <option value="">Pilih status...</option>
                                        <option value="Belum Kawin" {{ old('status_perkawinan', $user->karyawan->status_perkawinan) == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                                        <option value="Kawin" {{ old('status_perkawinan', $user->karyawan->status_perkawinan) == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                                        <option value="Cerai Hidup" {{ old('status_perkawinan', $user->karyawan->status_perkawinan) == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                        <option value="Cerai Mati" {{ old('status_perkawinan', $user->karyawan->status_perkawinan) == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                                    </select>
                                    @error('status_perkawinan')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="divisi" class="block text-sm font-semibold text-gray-700">Divisi</label>
                                    <select id="divisi" name="divisi" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('divisi') border-red-300 @enderror">
                                        <option value="">Pilih divisi...</option>
                                        <option value="IT" {{ old('divisi', $user->karyawan->divisi) == 'IT' ? 'selected' : '' }}>IT</option>
                                        <option value="Finance" {{ old('divisi', $user->karyawan->divisi) == 'Finance' ? 'selected' : '' }}>Finance</option>
                                        <option value="Operations" {{ old('divisi', $user->karyawan->divisi) == 'Operations' ? 'selected' : '' }}>Operations</option>
                                        <option value="ABK" {{ old('divisi', $user->karyawan->divisi) == 'ABK' ? 'selected' : '' }}>ABK</option>
                                        <option value="Admin" {{ old('divisi', $user->karyawan->divisi) == 'Admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="HR" {{ old('divisi', $user->karyawan->divisi) == 'HR' ? 'selected' : '' }}>HR</option>
                                    </select>
                                    @error('divisi')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="pekerjaan" class="block text-sm font-semibold text-gray-700">Pekerjaan</label>
                                    <select id="pekerjaan" name="pekerjaan" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('pekerjaan') border-red-300 @enderror">
                                        <option value="">Pilih pekerjaan...</option>
                                        <option value="Administrator" {{ old('pekerjaan', $user->karyawan->pekerjaan) == 'Administrator' ? 'selected' : '' }}>Administrator</option>
                                        <option value="Supir Truck" {{ old('pekerjaan', $user->karyawan->pekerjaan) == 'Supir Truck' ? 'selected' : '' }}>Supir Truck</option>
                                        <option value="Supir Trailer" {{ old('pekerjaan', $user->karyawan->pekerjaan) == 'Supir Trailer' ? 'selected' : '' }}>Supir Trailer</option>
                                        <option value="Krani" {{ old('pekerjaan', $user->karyawan->pekerjaan) == 'Krani' ? 'selected' : '' }}>Krani</option>
                                        <option value="Staff Operasional" {{ old('pekerjaan', $user->karyawan->pekerjaan) == 'Staff Operasional' ? 'selected' : '' }}>Staff Operasional</option>
                                        <option value="Manager" {{ old('pekerjaan', $user->karyawan->pekerjaan) == 'Manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="Supervisor" {{ old('pekerjaan', $user->karyawan->pekerjaan) == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                                        <option value="Finance Staff" {{ old('pekerjaan', $user->karyawan->pekerjaan) == 'Finance Staff' ? 'selected' : '' }}>Finance Staff</option>
                                        <option value="IT Support" {{ old('pekerjaan', $user->karyawan->pekerjaan) == 'IT Support' ? 'selected' : '' }}>IT Support</option>
                                    </select>
                                    @error('pekerjaan')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="tanggal_masuk" class="block text-sm font-semibold text-gray-700">Tanggal Masuk</label>
                                    <input type="date" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', $user->karyawan->tanggal_masuk ? \Carbon\Carbon::parse($user->karyawan->tanggal_masuk)->format('Y-m-d') : '') }}" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tanggal_masuk') border-red-300 @enderror">
                                    @error('tanggal_masuk')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="no_ketenagakerjaan" class="block text-sm font-semibold text-gray-700">No. Ketenagakerjaan</label>
                                    <input type="text" id="no_ketenagakerjaan" name="no_ketenagakerjaan" value="{{ old('no_ketenagakerjaan', $user->karyawan->no_ketenagakerjaan) }}" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('no_ketenagakerjaan') border-red-300 @enderror">
                                    @error('no_ketenagakerjaan')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-gray-500 text-xs">Nomor kartu tenaga kerja (jika ada)</p>
                                </div>
                            </div>

                            <div class="space-y-2 mb-6">
                                <label for="alamat_lengkap" class="block text-sm font-semibold text-gray-700">Alamat Lengkap</label>
                                <textarea id="alamat_lengkap" name="alamat_lengkap" rows="4" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none @error('alamat_lengkap') border-red-300 @enderror" 
                                    placeholder="Masukkan alamat lengkap...">{{ old('alamat_lengkap', $user->karyawan->alamat_lengkap) }}</textarea>
                                @error('alamat_lengkap')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end pt-6 border-t border-gray-200">
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Password Form --}}
                <div id="password-form" class="edit-tab-content bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6 hidden">
                    <div class="px-6 py-4 bg-purple-50 border-b border-purple-200">
                        <h4 class="text-lg font-semibold text-purple-900 flex items-center">
                            <i class="fas fa-key mr-2"></i>
                            Ubah Password
                        </h4>
                    </div>
                    
                    <form method="POST" action="{{ route('profile.update.account') }}" class="p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label for="current_password" class="block text-sm font-semibold text-gray-700">Password Saat Ini <span class="text-red-500">*</span></label>
                                <input type="password" id="current_password" name="current_password" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('current_password') border-red-300 @enderror">
                                @error('current_password')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="new_password" class="block text-sm font-semibold text-gray-700">Password Baru <span class="text-red-500">*</span></label>
                                <input type="password" id="new_password" name="new_password" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('new_password') border-red-300 @enderror">
                                @error('new_password')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-xs">Password minimal 8 karakter</p>
                            </div>

                            <div class="space-y-2">
                                <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 border-t border-gray-200 mt-6">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-key mr-2"></i>Ubah Password
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Danger Zone --}}
                <div id="danger-form" class="edit-tab-content bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden mb-6 hidden">
                    <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                        <h4 class="text-lg font-semibold text-red-900 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Zona Bahaya
                        </h4>
                    </div>
                    
                    <div class="p-6">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                            <h5 class="text-lg font-semibold text-red-900 mb-2">Hapus Akun</h5>
                            <p class="text-red-800 text-sm mb-4">
                                Setelah akun Anda dihapus, semua data dan informasi akan dihapus secara permanen. 
                                Tindakan ini tidak dapat dibatalkan.
                            </p>
                            
                            <button onclick="showDeleteModal()" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all duration-200 text-sm">
                                <i class="fas fa-trash mr-2"></i>Hapus Akun
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Account Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-xl bg-white">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Akun</h3>
            <p class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menghapus akun? Semua data akan dihapus secara permanen.
            </p>
            
            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('DELETE')
                
                <div class="text-left">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password Anda</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                        placeholder="Masukkan password untuk konfirmasi">
                </div>
                
                <div class="text-left">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ketik "DELETE" untuk konfirmasi</label>
                    <input type="text" name="confirmation" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                        placeholder="DELETE">
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeDeleteModal()" class="px-6 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm">
                        <i class="fas fa-trash mr-2"></i>Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showEditTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.edit-tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-form').classList.remove('hidden');
    
    // Update navigation buttons
    document.querySelectorAll('[id^="nav-"]').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-700');
        btn.classList.add('text-gray-700', 'hover:bg-gray-100');
    });
    
    document.getElementById('nav-' + tabName).classList.add('bg-blue-100', 'text-blue-700');
    document.getElementById('nav-' + tabName).classList.remove('text-gray-700', 'hover:bg-gray-100');
}

function showDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.querySelector('input[name="password"]').value = '';
    document.querySelector('input[name="confirmation"]').value = '';
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a hash in URL to show specific tab
    const hash = window.location.hash.substring(1);
    if (hash && ['account', 'personal', 'password', 'danger'].includes(hash)) {
        showEditTab(hash);
    } else {
        showEditTab('account');
    }
});
</script>
@endpush

@endsection
