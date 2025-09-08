@extends('layouts.app')

@section('title','Tambah Pengguna Baru')
@section('page_title', 'Tambah Pengguna')

@section('content')

@push('styles')
    {{-- Tambahkan CSS untuk Choices.js --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <style>
        .choices__inner {
            background-color: #f3f4f6; /* bg-gray-100 */
            border-radius: 0.375rem; /* rounded-md */
            border: 1px solid #d1d5db; /* border-gray-300 */
            font-size: 1rem; /* text-base */
            padding: 0.5rem 0.75rem; /* p-2.5 equivalent */
            min-height: 46px;
        }
        .is-focused .choices__inner, .is-open .choices__inner {
            border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5);
        }
    </style>
@endpush

<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Formulir User Baru</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6">
            <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('master.user.store') }}" method="POST" class="space-y-8">
        @csrf
        
        {{-- Hidden input untuk menyimpan role yang dipilih --}}
        <input type="hidden" name="selected_role" id="selected_role" value="staff">
        
        @php
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
        @endphp

        {{-- Informasi Pengguna --}}
        <fieldset class="border p-4 rounded-md">
            <legend class="text-lg font-semibold text-gray-800 px-2">Informasi Pengguna</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                <div class="md:col-span-2">
                    <label for="karyawan_id" class="block text-sm font-medium text-gray-700 mb-1">Hubungkan dengan Karyawan (Opsional)</label>
                    <select name="karyawan_id" id="karyawan_id">
                        <option value="">-- Tidak dihubungkan --</option>
                        {{-- Pastikan $karyawans berisi semua karyawan dari controller --}}
                        @foreach ($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}" data-nama="{{ $karyawan->nama_lengkap }}">
                                {{ $karyawan->nama_lengkap }}
                                @if($karyawan->nik)
                                    ({{ $karyawan->nik }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" class="{{ $inputClasses }}" required value="{{ old('name') }}">
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" id="username" class="{{ $inputClasses }}" required value="{{ old('username') }}">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" class="{{ $inputClasses }}" required>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="{{ $inputClasses }}" required>
                </div>
            </div>
        </fieldset>

        {{-- Izin Akses --}}
        <fieldset class="border p-4 rounded-md">
            <legend class="text-lg font-semibold text-gray-800 px-2">Tingkat Akses</legend>
            <div class="space-y-4 pt-4">
                
                {{-- Role/Level Akses Utama --}}
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h4 class="font-bold text-md text-blue-800 mb-3">Pilih Level Akses:</h4>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input id="role_admin" name="user_role" type="radio" value="admin" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <label for="role_admin" class="ml-3">
                                <span class="font-medium text-gray-900">👑 Super Admin</span>
                                <p class="text-sm text-gray-600">Akses penuh ke semua fitur sistem</p>
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="role_manager" name="user_role" type="radio" value="manager" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <label for="role_manager" class="ml-3">
                                <span class="font-medium text-gray-900">👨‍💼 Manager</span>
                                <p class="text-sm text-gray-600">Dapat kelola data master, pranota, dan laporan</p>
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="role_staff" name="user_role" type="radio" value="staff" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" checked>
                            <label for="role_staff" class="ml-3">
                                <span class="font-medium text-gray-900">👨‍💻 Staff</span>
                                <p class="text-sm text-gray-600">Akses dasar untuk input data dan melihat laporan</p>
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="role_supir" name="user_role" type="radio" value="supir" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <label for="role_supir" class="ml-3">
                                <span class="font-medium text-gray-900">🚛 Supir</span>
                                <p class="text-sm text-gray-600">Hanya bisa melihat jadwal dan pranota sendiri</p>
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="role_custom" name="user_role" type="radio" value="custom" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <label for="role_custom" class="ml-3">
                                <span class="font-medium text-gray-900">⚙️ Custom</span>
                                <p class="text-sm text-gray-600">Pilih izin secara manual</p>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Permission Details (Hidden by default) --}}
                <div id="custom_permissions" class="bg-gray-50 p-4 rounded-lg border border-gray-200" style="display: none;">
                    <h4 class="font-bold text-md text-gray-700 mb-3">Pilih Izin Khusus:</h4>
                    @php
                        $simplePermissions = [
                            'Master Data' => ['master-user', 'master-kontainer', 'master-kegiatan', 'master-tujuan'],
                            'Pranota' => ['pranota-create', 'pranota-edit', 'pranota-view', 'pranota-delete'],
                            'Pembayaran' => ['pembayaran-view', 'pembayaran-create', 'pembayaran-edit'],
                            'Laporan' => ['laporan-view', 'laporan-export'],
                            'Approval' => ['approval-pranota', 'approval-pembayaran']
                        ];
                    @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($simplePermissions as $groupName => $permissionNames)
                            <div class="space-y-2">
                                <h5 class="font-semibold text-gray-700 border-b pb-1">{{ $groupName }}</h5>
                                @foreach ($permissionNames as $permName)
                                    @php
                                        $permission = $permissions->firstWhere('name', $permName);
                                    @endphp
                                    @if ($permission)
                                        <div class="flex items-center">
                                            <input id="perm-{{ $permission->id }}" name="permissions[]" type="checkbox" value="{{ $permission->id }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                            <label for="perm-{{ $permission->id }}" class="ml-2 text-sm text-gray-700">
                                                {{ $permission->description ?? str_replace('-', ' ', ucwords($permission->name, '-')) }}
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                
                {{-- Quick Permission Summary --}}
                <div id="permission_summary" class="bg-green-50 p-3 rounded-lg border border-green-200">
                    <h5 class="font-medium text-green-800 mb-2">✅ Izin yang akan diberikan:</h5>
                    <p id="summary_text" class="text-sm text-green-700">Staff - Akses dasar untuk input data dan melihat laporan</p>
                </div>
            </div>
        </fieldset>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('master.user.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Simpan
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

            const nameInput = document.getElementById('name');
            const usernameInput = document.getElementById('username');

            // Event listener untuk mengisi nama dan username otomatis
            karyawanElement.addEventListener('choice', function(event) {
                const selectedOption = event.detail.choice;
                if (selectedOption && selectedOption.value) {
                    const namaLengkap = selectedOption.dataset.nama;
                    nameInput.value = namaLengkap;

                    // Buat username default dari nama lengkap (lowercase, tanpa spasi)
                    usernameInput.value = namaLengkap.toLowerCase().replace(/\s+/g, '.');
                }
            });

            // Kosongkan input jika pilihan karyawan dihapus
            karyawanElement.addEventListener('removeItem', function(event) {
                nameInput.value = '';
                usernameInput.value = '';
            });

            // === PERMISSION SYSTEM ===
            const roleRadios = document.querySelectorAll('input[name="user_role"]');
            const customPermissions = document.getElementById('custom_permissions');
            const summaryText = document.getElementById('summary_text');
            const allPermissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

            // Role descriptions dan permissions
            const roleDefinitions = {
                'admin': {
                    description: 'Super Admin - Akses penuh ke semua fitur sistem',
                    permissions: 'all' // Semua permission
                },
                'manager': {
                    description: 'Manager - Dapat kelola data master, pranota, dan laporan',
                    permissions: ['master-user', 'master-kontainer', 'master-kegiatan', 'master-tujuan', 'pranota-create', 'pranota-edit', 'pranota-view', 'pranota-delete', 'pembayaran-view', 'pembayaran-create', 'pembayaran-edit', 'laporan-view', 'laporan-export', 'approval-pranota', 'approval-pembayaran']
                },
                'staff': {
                    description: 'Staff - Akses dasar untuk input data dan melihat laporan',
                    permissions: ['pranota-create', 'pranota-view', 'pembayaran-view', 'laporan-view']
                },
                'supir': {
                    description: 'Supir - Hanya bisa melihat jadwal dan pranota sendiri',
                    permissions: ['pranota-view']
                },
                'custom': {
                    description: 'Custom - Izin dipilih secara manual',
                    permissions: []
                }
            };

            // Handle role change
            roleRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const selectedRole = this.value;
                    const roleData = roleDefinitions[selectedRole];
                    
                    // Update hidden input
                    document.getElementById('selected_role').value = selectedRole;
                    
                    // Update summary
                    summaryText.textContent = roleData.description;
                    
                    // Show/hide custom permissions
                    if (selectedRole === 'custom') {
                        customPermissions.style.display = 'block';
                    } else {
                        customPermissions.style.display = 'none';
                        
                        // Auto-select permissions based on role
                        allPermissionCheckboxes.forEach(checkbox => {
                            if (roleData.permissions === 'all') {
                                checkbox.checked = true;
                            } else if (Array.isArray(roleData.permissions)) {
                                // Get permission name from the permission ID
                                const permissionId = checkbox.value;
                                const permissionLabel = checkbox.nextElementSibling?.textContent || '';
                                
                                // Check if this permission should be selected for this role
                                checkbox.checked = roleData.permissions.some(permName => 
                                    permissionLabel.toLowerCase().includes(permName.replace('-', ' '))
                                );
                            } else {
                                checkbox.checked = false;
                            }
                        });
                    }
                });
            });

            // Update summary when custom permissions change
            customPermissions.addEventListener('change', function() {
                if (document.querySelector('input[name="user_role"]:checked')?.value === 'custom') {
                    const checkedCount = customPermissions.querySelectorAll('input[type="checkbox"]:checked').length;
                    summaryText.textContent = `Custom - ${checkedCount} izin khusus dipilih`;
                }
            });

            // Initialize with default role (staff)
            document.getElementById('role_staff').dispatchEvent(new Event('change'));
        });
    </script>
@endpush
