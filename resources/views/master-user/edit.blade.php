@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('page_title', 'Edit Pengguna')

@section('content')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <style>
        /* Permission Card Styles */
        .permission-card {
            transition: all 0.2s ease-in-out;
        }
        .permission-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .permission-checkbox {
            accent-color: #3b82f6;
        }
        .permission-checkbox:checked {
            background-color: #3b82f6;
        }
    </style>
@endpush

<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Pengguna: {{ $user->username }}</h2>

    @if (isset($errors) && is_object($errors) && method_exists($errors, 'any') && $errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6">
            <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
        </div>
    @endif

    <form action="{{ route('master.user.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input name="username" required value="{{ old('username', $user->username) }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 p-2.5" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password (kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 p-2.5" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 p-2.5" />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Hubungkan dengan Karyawan (Opsional)</label>
                <select name="karyawan_id" id="karyawan_id">
                    <option value="">-- Tidak dihubungkan --</option>
                    @foreach ($karyawans as $karyawan)
                        <option value="{{ $karyawan->id }}" data-nama="{{ $karyawan->nama_lengkap }}" @if(old('karyawan_id', $user->karyawan_id) == $karyawan->id) selected @endif>{{ $karyawan->nama_lengkap }} @if($karyawan->nik) ({{ $karyawan->nik }}) @endif</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">🎯 Izin Akses Sederhana</h3>
                <div class="text-sm text-gray-600">
                    Dipilih: <span id="permission_count" class="font-medium text-blue-600">0</span> / <span id="total_permissions">15</span>
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
                                Pilih user yang sudah ada untuk menyalin semua permission-nya ke user ini. Permission yang ada akan diganti.
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
                        <input id="perm-dashboard" name="simple_permissions[]" type="checkbox" value="dashboard" class="permission-checkbox mt-1" @if(in_array('dashboard', $userSimplePermissions ?? [])) checked @endif>
                        <div class="ml-3 flex-1">
                            <label for="perm-dashboard" class="font-medium text-gray-900 cursor-pointer">Dashboard</label>
                            <p class="text-sm text-gray-600 mt-1">Akses halaman dashboard utama</p>
                            <span class="inline-block mt-2 px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Semua User</span>
                        </div>
                    </div>
                </div>

                <div class="permission-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <input id="perm-tagihan-kontainer" name="simple_permissions[]" type="checkbox" value="tagihan-kontainer" class="permission-checkbox mt-1" @if(in_array('tagihan-kontainer', $userSimplePermissions ?? [])) checked @endif>
                        <div class="ml-3 flex-1">
                            <label for="perm-tagihan-kontainer" class="font-medium text-gray-900 cursor-pointer">Tagihan Kontainer Sewa</label>
                            <p class="text-sm text-gray-600 mt-1">Menu tagihan kontainer sewa</p>
                            <span class="inline-block mt-2 px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">Bisnis</span>
                        </div>
                    </div>
                </div>

                <div class="permission-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <input id="perm-pranota-supir" name="simple_permissions[]" type="checkbox" value="pranota-supir" class="permission-checkbox mt-1" @if(in_array('pranota-supir', $userSimplePermissions ?? [])) checked @endif>
                        <div class="ml-3 flex-1">
                            <label for="perm-pranota-supir" class="font-medium text-gray-900 cursor-pointer">Pranota Supir</label>
                            <p class="text-sm text-gray-600 mt-1">Menu pranota supir</p>
                            <span class="inline-block mt-2 px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Operasional</span>
                        </div>
                    </div>
                </div>

                <div class="permission-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <input id="perm-pembayaran-pranota-supir" name="simple_permissions[]" type="checkbox" value="pembayaran-pranota-supir" class="permission-checkbox mt-1" @if(in_array('pembayaran-pranota-supir', $userSimplePermissions ?? [])) checked @endif>
                        <div class="ml-3 flex-1">
                            <label for="perm-pembayaran-pranota-supir" class="font-medium text-gray-900 cursor-pointer">Pembayaran Pranota Supir</label>
                            <p class="text-sm text-gray-600 mt-1">Menu pembayaran pranota supir</p>
                            <span class="inline-block mt-2 px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">Keuangan</span>
                        </div>
                    </div>
                </div>

                <div class="permission-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <input id="perm-permohonan" name="simple_permissions[]" type="checkbox" value="permohonan" class="permission-checkbox mt-1" @if(in_array('permohonan', $userSimplePermissions ?? [])) checked @endif>
                        <div class="ml-3 flex-1">
                            <label for="perm-permohonan" class="font-medium text-gray-900 cursor-pointer">Permohonan Memo</label>
                            <p class="text-sm text-gray-600 mt-1">Menu permohonan memo</p>
                            <span class="inline-block mt-2 px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded">Administrasi</span>
                        </div>
                    </div>
                </div>

                <div class="permission-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <input id="perm-user-approval" name="simple_permissions[]" type="checkbox" value="user-approval" class="permission-checkbox mt-1" @if(in_array('user-approval', $userSimplePermissions ?? [])) checked @endif>
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
                                <input id="perm-master-karyawan" name="simple_permissions[]" type="checkbox" value="master-karyawan" class="permission-checkbox mt-1" @if(in_array('master-karyawan', $userSimplePermissions ?? [])) checked @endif>
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-karyawan" class="text-sm font-medium text-gray-900 cursor-pointer">Karyawan</label>
                                </div>
                            </div>
                        </div>

                        <div class="permission-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow">
                            <div class="flex items-start">
                                <input id="perm-master-user" name="simple_permissions[]" type="checkbox" value="master-user" class="permission-checkbox mt-1" @if(in_array('master-user', $userSimplePermissions ?? [])) checked @endif>
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-user" class="text-sm font-medium text-gray-900 cursor-pointer">User</label>
                                </div>
                            </div>
                        </div>

                        <div class="permission-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow">
                            <div class="flex items-start">
                                <input id="perm-master-kontainer" name="simple_permissions[]" type="checkbox" value="master-kontainer" class="permission-checkbox mt-1" @if(in_array('master-kontainer', $userSimplePermissions ?? [])) checked @endif>
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-kontainer" class="text-sm font-medium text-gray-900 cursor-pointer">Kontainer</label>
                                </div>
                            </div>
                        </div>

                        <div class="permission-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow">
                            <div class="flex items-start">
                                <input id="perm-master-pricelist-sewa-kontainer" name="simple_permissions[]" type="checkbox" value="master-pricelist-sewa-kontainer" class="permission-checkbox mt-1" @if(in_array('master-pricelist-sewa-kontainer', $userSimplePermissions ?? [])) checked @endif>
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-pricelist-sewa-kontainer" class="text-sm font-medium text-gray-900 cursor-pointer">Pricelist Sewa</label>
                                </div>
                            </div>
                        </div>

                        <div class="permission-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow">
                            <div class="flex items-start">
                                <input id="perm-master-tujuan" name="simple_permissions[]" type="checkbox" value="master-tujuan" class="permission-checkbox mt-1" @if(in_array('master-tujuan', $userSimplePermissions ?? [])) checked @endif>
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-tujuan" class="text-sm font-medium text-gray-900 cursor-pointer">Tujuan</label>
                                </div>
                            </div>
                        </div>

                        <div class="permission-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow">
                            <div class="flex items-start">
                                <input id="perm-master-kegiatan" name="simple_permissions[]" type="checkbox" value="master-kegiatan" class="permission-checkbox mt-1" @if(in_array('master-kegiatan', $userSimplePermissions ?? [])) checked @endif>
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-kegiatan" class="text-sm font-medium text-gray-900 cursor-pointer">Kegiatan</label>
                                </div>
                            </div>
                        </div>

                        <div class="permission-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow">
                            <div class="flex items-start">
                                <input id="perm-master-permission" name="simple_permissions[]" type="checkbox" value="master-permission" class="permission-checkbox mt-1" @if(in_array('master-permission', $userSimplePermissions ?? [])) checked @endif>
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-permission" class="text-sm font-medium text-gray-900 cursor-pointer">Permission</label>
                                </div>
                            </div>
                        </div>

                        <div class="permission-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow">
                            <div class="flex items-start">
                                <input id="perm-master-mobil" name="simple_permissions[]" type="checkbox" value="master-mobil" class="permission-checkbox mt-1" @if(in_array('master-mobil', $userSimplePermissions ?? [])) checked @endif>
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-mobil" class="text-sm font-medium text-gray-900 cursor-pointer">Mobil</label>
                                </div>
                            </div>
                        </div>

                        <div class="permission-card bg-white border border-gray-200 rounded-lg p-3 hover:shadow-sm transition-shadow">
                            <div class="flex items-start">
                                <input id="perm-master-data" name="simple_permissions[]" type="checkbox" value="master-data" class="permission-checkbox mt-1" @if(in_array('master-data', $userSimplePermissions ?? [])) checked @endif>
                                <div class="ml-2 flex-1">
                                    <label for="perm-master-data" class="text-sm font-medium text-gray-900 cursor-pointer">Master Data</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <a href="{{ route('master.user.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Perbarui
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Choices.js untuk Karyawan
            const karyawanElement = document.getElementById('karyawan_id');
            const karyawanChoices = new Choices(karyawanElement, {
                searchEnabled: true,
                itemSelectText: 'Tekan untuk memilih',
                shouldSort: false,
                placeholder: true,
                placeholderValue: 'Cari atau pilih karyawan...'
            });

            // Permission controls - Simplified version
            const checkboxes = () => Array.from(document.querySelectorAll('.permission-checkbox'));
            const countEl = document.getElementById('permission_count');

            function updateCount() {
                const checked = checkboxes().filter(cb => cb.checked).length;
                countEl.textContent = checked;
                return checked;
            }

            // Quick Actions
            document.getElementById('select_common').addEventListener('click', function(){
                const commonPerms = ['dashboard', 'tagihan-kontainer', 'pranota-supir'];
                checkboxes().forEach(cb => {
                    cb.checked = commonPerms.includes(cb.value);
                });
                updateCount();
                showToast('✅ Permission umum dipilih', 'success');
            });

            document.getElementById('select_admin').addEventListener('click', function(){
                checkboxes().forEach(cb => cb.checked = true);
                updateCount();
                showToast('👑 Semua permission admin dipilih', 'success');
            });

            document.getElementById('select_all').addEventListener('click', function(){
                checkboxes().forEach(cb => cb.checked = true);
                updateCount();
                showToast('📋 Semua permission dipilih', 'success');
            });

            document.getElementById('deselect_all').addEventListener('click', function(){
                checkboxes().forEach(cb => cb.checked = false);
                updateCount();
                showToast('❌ Semua permission dihapus', 'warning');
            });

            // Master Data auto-select
            document.getElementById('perm-master-data').addEventListener('change', function(){
                const masterCheckboxes = [
                    'perm-master-karyawan',
                    'perm-master-user',
                    'perm-master-kontainer',
                    'perm-master-pricelist-sewa-kontainer',
                    'perm-master-tujuan',
                    'perm-master-kegiatan',
                    'perm-master-permission',
                    'perm-master-mobil'
                ];
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

            // Initialize count
            updateCount();

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e){
                const checkedPermissions = checkboxes().filter(cb => cb.checked).length;
                if (checkedPermissions === 0) {
                    e.preventDefault();
                    showToast('⚠️ Pilih minimal 1 permission untuk user', 'warning');
                    return false;
                }
                showToast('🔄 Menyimpan perubahan user...', 'info');
            });
        });
    </script>
@endpush
