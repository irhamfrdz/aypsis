@extends('layouts.app')

@section('title','Tambah Pengguna Baru')
@section('page_title', 'Tambah Pengguna')

@section('content')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <style>
        /* Small, neutral adjustments to keep layout readable */
        .permission-list { max-height: 420px; overflow: auto; }
        .permission-row { display:flex; gap:0.75rem; align-items:flex-start; padding:0.5rem 0; border-bottom:1px solid #f3f4f6 }
        .permission-meta { font-size:0.85rem; color:#6b7280 }
        .toolbar { display:flex; gap:0.5rem; flex-wrap:wrap }
        .toolbar .btn { padding:0.4rem 0.6rem; border-radius:6px; border:1px solid #e5e7eb; background:#fff }
    </style>
@endpush

<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Formulir User Baru</h2>

    @if (isset($errors) && is_object($errors) && method_exists($errors, 'any') && $errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6">
            <strong class="font-bold">Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('master.user.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input name="username" required value="{{ old('username') }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 p-2.5" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 p-2.5" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 p-2.5" />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Hubungkan dengan Karyawan (Opsional)</label>
                <select name="karyawan_id" id="karyawan_id">
                    <option value="">-- Tidak dihubungkan --</option>
                    @foreach ($karyawans as $karyawan)
                        <option value="{{ $karyawan->id }}" data-nama="{{ $karyawan->nama_lengkap }}">{{ $karyawan->nama_lengkap }} @if($karyawan->nik) ({{ $karyawan->nik }}) @endif</option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr class="my-6" />

        {{-- Permissions Simplified UI --}}
        <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">🎯 Izin Akses Sederhana</h3>
                <div class="text-sm text-gray-600">
                    Dipilih: <span id="permission_count" class="font-medium text-blue-600">0</span> / <span id="total_permissions">6</span>
                </div>
            </div>

            {{-- Copy Permission Feature --}}
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"/>
                                <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-indigo-800">📋 Copy Permission dari User Lain</h4>
                            <p class="text-sm text-indigo-700 mt-1">
                                Pilih user yang sudah ada untuk menyalin semua permission-nya ke user baru ini.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <select id="copy_user_select" class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih User --</option>
                            @foreach($users as $existingUser)
                                <option value="{{ $existingUser->id }}">
                                    {{ $existingUser->name }} ({{ $existingUser->username }})
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="copy_permissions_btn" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            📋 Copy Permission
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800">Sistem Permission Sederhana</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            Pilih izin akses sesuai dengan menu yang ingin diakses user. Nama permission sudah disesuaikan dengan nama menu untuk kemudahan.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="flex flex-wrap gap-2 mb-4">
                <button type="button" id="select_common" class="px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition-colors">
                    ✅ Pilih Umum
                </button>
                <button type="button" id="select_admin" class="px-3 py-2 bg-purple-600 text-white text-sm rounded-md hover:bg-purple-700 transition-colors">
                    👑 Pilih Admin
                </button>
                <button type="button" id="select_all" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors">
                    📋 Pilih Semua
                </button>
                <button type="button" id="deselect_all" class="px-3 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700 transition-colors">
                    ❌ Hapus Semua
                </button>
            </div>

            {{-- Permission Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="permission_cards">
                {{-- Menu Utama --}}
                <div class="permission-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <input id="perm-dashboard" name="simple_permissions[]" type="checkbox" value="dashboard" class="permission-checkbox mt-1">
                        <div class="ml-3 flex-1">
                            <label for="perm-dashboard" class="font-medium text-gray-900 cursor-pointer">Dashboard</label>
                            <p class="text-sm text-gray-600 mt-1">Akses halaman dashboard utama</p>
                            <span class="inline-block mt-2 px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Semua User</span>
                        </div>
                    </div>
                </div>

                <div class="permission-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <input id="perm-tagihan-kontainer" name="simple_permissions[]" type="checkbox" value="tagihan-kontainer" class="permission-checkbox mt-1">
                        <div class="ml-3 flex-1">
                            <label for="perm-tagihan-kontainer" class="font-medium text-gray-900 cursor-pointer">Tagihan Kontainer Sewa</label>
                            <p class="text-sm text-gray-600 mt-1">Menu tagihan kontainer sewa</p>
                            <span class="inline-block mt-2 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">Bisnis</span>
                        </div>
                    </div>
                </div>

                <div class="permission-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <input id="perm-pranota-supir" name="simple_permissions[]" type="checkbox" value="pranota-supir" class="permission-checkbox mt-1">
                        <div class="ml-3 flex-1">
                            <label for="perm-pranota-supir" class="font-medium text-gray-900 cursor-pointer">Pranota Supir</label>
                            <p class="text-sm text-gray-600 mt-1">Menu pranota supir</p>
                            <span class="inline-block mt-2 px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Operasional</span>
                        </div>
                    </div>
                </div>

                <div class="permission-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <input id="perm-pembayaran-pranota-supir" name="simple_permissions[]" type="checkbox" value="pembayaran-pranota-supir" class="permission-checkbox mt-1">
                        <div class="ml-3 flex-1">
                            <label for="perm-pembayaran-pranota-supir" class="font-medium text-gray-900 cursor-pointer">Pembayaran Pranota Supir</label>
                            <p class="text-sm text-gray-600 mt-1">Menu pembayaran pranota supir</p>
                            <span class="inline-block mt-2 px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">Keuangan</span>
                        </div>
                    </div>
                </div>

                <div class="permission-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <input id="perm-permohonan" name="simple_permissions[]" type="checkbox" value="permohonan" class="permission-checkbox mt-1">
                        <div class="ml-3 flex-1">
                            <label for="perm-permohonan" class="font-medium text-gray-900 cursor-pointer">Permohonan Memo</label>
                            <p class="text-sm text-gray-600 mt-1">Menu permohonan memo</p>
                            <span class="inline-block mt-2 px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded">Administrasi</span>
                        </div>
                    </div>
                </div>

                <div class="permission-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <input id="perm-user-approval" name="simple_permissions[]" type="checkbox" value="user-approval" class="permission-checkbox mt-1">
                        <div class="ml-3 flex-1">
                            <label for="perm-user-approval" class="font-medium text-gray-900 cursor-pointer">Persetujuan User</label>
                            <p class="text-sm text-gray-600 mt-1">Menyetujui user baru</p>
                            <span class="inline-block mt-2 px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Admin</span>
                        </div>
                    </div>
                </div>

                {{-- Master Data Section --}}
                <div class="md:col-span-2 lg:col-span-3">
                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Master Data (Pilih Semua untuk Admin)
                    </h4>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="permission-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow">
                            <div class="flex items-start">
                                <input id="perm-master-karyawan" name="simple_permissions[]" type="checkbox" value="master-karyawan" class="permission-checkbox mt-1">
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-karyawan" class="text-sm font-medium text-gray-900 cursor-pointer">Karyawan</label>
                                </div>
                            </div>
                        </div>

                        <div class="permission-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow">
                            <div class="flex items-start">
                                <input id="perm-master-user" name="simple_permissions[]" type="checkbox" value="master-user" class="permission-checkbox mt-1">
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-user" class="text-sm font-medium text-gray-900 cursor-pointer">User</label>
                                </div>
                            </div>
                        </div>

                        <div class="permission-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow">
                            <div class="flex items-start">
                                <input id="perm-master-kontainer" name="simple_permissions[]" type="checkbox" value="master-kontainer" class="permission-checkbox mt-1">
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-kontainer" class="text-sm font-medium text-gray-900 cursor-pointer">Kontainer</label>
                                </div>
                            </div>
                        </div>

                        <div class="permission-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow">
                            <div class="flex items-start">
                                <input id="perm-master-data" name="simple_permissions[]" type="checkbox" value="master-data" class="permission-checkbox mt-1">
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-data" class="text-sm font-medium text-gray-900 cursor-pointer">Semua Master</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-4 gap-3">
            <a href="{{ route('master.user.index') }}" class="inline-flex items-center px-4 py-2 border rounded">Batal</a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Choices for karyawan select
            const karyawanElement = document.getElementById('karyawan_id');
            if (karyawanElement) {
                new Choices(karyawanElement, {
                    searchEnabled: true,
                    shouldSort: false,
                    placeholder: true,
                    placeholderValue: 'Cari atau pilih karyawan...'
                });
            }

            // Simple Permission Management
            const checkboxes = () => Array.from(document.querySelectorAll('.permission-checkbox'));
            const countEl = document.getElementById('permission_count');
            const totalEl = document.getElementById('total_permissions');

            // Update permission count
            function updateCount() {
                const checked = checkboxes().filter(cb => cb.checked).length;
                const total = checkboxes().length;
                countEl.textContent = checked;
                totalEl.textContent = total;

                // Update visual feedback
                countEl.className = checked > 0 ? 'font-medium text-green-600' : 'font-medium text-gray-600';
            }

            // Quick Actions
            document.getElementById('select_common').addEventListener('click', function(){
                // Pilih permission umum (dashboard, tagihan-kontainer, pranota-supir)
                const commonPerms = ['dashboard', 'tagihan-kontainer', 'pranota-supir'];
                checkboxes().forEach(cb => {
                    cb.checked = commonPerms.includes(cb.value);
                });
                updateCount();
                showToast('✅ Permission umum dipilih', 'success');
            });

            document.getElementById('select_admin').addEventListener('click', function(){
                // Pilih semua permission untuk admin
                checkboxes().forEach(cb => cb.checked = true);
                updateCount();
                showToast('👑 Semua permission admin dipilih', 'success');
            });

            document.getElementById('select_all').addEventListener('click', function(){
                checkboxes().forEach(cb => cb.checked = true);
                updateCount();
                showToast('📋 Semua permission dipilih', 'info');
            });

            document.getElementById('deselect_all').addEventListener('click', function(){
                checkboxes().forEach(cb => cb.checked = false);
                updateCount();
                showToast('❌ Semua permission dihapus', 'warning');
            });

            // Master Data Logic - jika "Semua Master" dipilih, pilih semua master
            document.getElementById('perm-master-data').addEventListener('change', function(){
                const masterCheckboxes = ['perm-master-karyawan', 'perm-master-user', 'perm-master-kontainer'];
                masterCheckboxes.forEach(id => {
                    const checkbox = document.getElementById(id);
                    if (checkbox) checkbox.checked = this.checked;
                });
                updateCount();
            });

            // Update count on any change
            document.addEventListener('change', function(e){
                if (e.target && e.target.classList && e.target.classList.contains('permission-checkbox')) {
                    updateCount();
                }
            });

            // Toast notification function
            function showToast(message, type = 'info') {
                const colors = {
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                    warning: 'bg-yellow-500',
                    info: 'bg-blue-500'
                };

                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-all duration-300`;
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => document.body.removeChild(toast), 300);
                }, 3000);
            }

            // Permission card hover effects
            document.querySelectorAll('.permission-card').forEach(card => {
                card.addEventListener('mouseenter', function(){
                    this.classList.add('ring-2', 'ring-blue-300');
                });
                card.addEventListener('mouseleave', function(){
                    this.classList.remove('ring-2', 'ring-blue-300');
                });
            });

            // Copy Permission Feature
            document.getElementById('copy_permissions_btn').addEventListener('click', function(){
                const select = document.getElementById('copy_user_select');
                const userId = select.value;

                if (!userId) {
                    showToast('⚠️ Pilih user terlebih dahulu', 'warning');
                    return;
                }

                // Show loading state
                this.disabled = true;
                this.innerHTML = '⏳ Loading...';

                // Fetch user permissions via AJAX
                fetch(`/master/user/${userId}/permissions-for-copy`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const permissions = data.permissions;
                            const userName = data.user.name;

                            // Uncheck all permissions first
                            checkboxes().forEach(cb => cb.checked = false);

                            // Check permissions that match
                            let copiedCount = 0;
                            checkboxes().forEach(cb => {
                                if (permissions.includes(cb.value)) {
                                    cb.checked = true;
                                    copiedCount++;
                                }
                            });

                            updateCount();

                            if (copiedCount > 0) {
                                showToast(`✅ Berhasil menyalin ${copiedCount} permission dari ${userName}`, 'success');
                            } else {
                                showToast(`⚠️ User ${userName} tidak memiliki permission yang bisa disalin`, 'warning');
                            }
                        } else {
                            showToast('❌ Gagal mengambil data permission', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('❌ Terjadi kesalahan saat mengambil data', 'error');
                    })
                    .finally(() => {
                        // Reset button state
                        this.disabled = false;
                        this.innerHTML = '📋 Copy Permission';
                    });
            });

            // Enable/disable copy button based on selection
            document.getElementById('copy_user_select').addEventListener('change', function(){
                const btn = document.getElementById('copy_permissions_btn');
                btn.disabled = !this.value;
            });

            // Initialize copy button state
            document.getElementById('copy_permissions_btn').disabled = true;
        });
    </script>
@endpush
