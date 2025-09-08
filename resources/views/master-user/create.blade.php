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
            <legend class="text-lg font-semibold text-gray-800 px-2">Izin Akses Per User</legend>
            <div class="space-y-4 pt-4">
                
                {{-- Quick Actions --}}
                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200 mb-4">
                    <div class="flex flex-wrap gap-2">
                        <button type="button" id="select_all" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            ✅ Pilih Semua
                        </button>
                        <button type="button" id="deselect_all" class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                            ❌ Hapus Semua
                        </button>
                        <button type="button" id="select_basic" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                            📝 Izin Dasar
                        </button>
                        <button type="button" id="select_admin" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                            👑 Izin Admin
                        </button>
                    </div>
                    <p class="text-xs text-blue-700 mt-2">Gunakan tombol di atas untuk memilih grup izin dengan cepat, lalu sesuaikan secara manual</p>
                </div>

                {{-- Permission Counter --}}
                <div class="bg-green-50 p-3 rounded-lg border border-green-200 mb-4">
                    <p class="text-green-800">
                        <span class="font-medium">Izin dipilih:</span> 
                        <span id="permission_count" class="font-bold">0</span> dari {{ $permissions->count() }} izin tersedia
                    </p>
                </div>

                {{-- Individual Permissions --}}
                @php
                    $groupedPermissions = $permissions->groupBy(function($item) {
                        // Kelompokkan berdasarkan kata kunci yang lebih spesifik
                        $name = $item->name;
                        
                        if (str_contains($name, 'master-pricelist') || str_contains($name, 'pricelist')) {
                            return 'master';
                        } else if (str_contains($name, 'master-')) {
                            return 'master';
                        } else if (str_contains($name, 'pranota')) {
                            return 'pranota';
                        } else if (str_contains($name, 'pembayaran')) {
                            return 'pembayaran';
                        } else if (str_contains($name, 'approval')) {
                            return 'approval';
                        } else if (str_contains($name, 'laporan')) {
                            return 'laporan';
                        } else {
                            // Fallback ke grouping lama
                            return explode('-', $name)[0];
                        }
                    });
                @endphp
                
                <div class="space-y-4">
                    @foreach ($groupedPermissions as $groupName => $groupPermissions)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="flex items-center justify-between mb-3 border-b pb-2">
                                <h4 class="font-bold text-md text-gray-700">
                                    @if($groupName == 'master')
                                        🔧 Master Data
                                    @elseif($groupName == 'pranota')
                                        📋 Pranota
                                    @elseif($groupName == 'pembayaran')
                                        💰 Pembayaran
                                    @elseif($groupName == 'laporan')
                                        📊 Laporan
                                    @elseif($groupName == 'approval')
                                        ✅ Approval
                                    @else
                                        {{ ucfirst($groupName) }}
                                    @endif
                                </h4>
                                <div class="flex gap-2">
                                    <button type="button" class="group-select-all text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200" data-group="{{ $groupName }}">
                                        Pilih Semua
                                    </button>
                                    <button type="button" class="group-deselect-all text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200" data-group="{{ $groupName }}">
                                        Hapus Semua
                                    </button>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-3">
                                @foreach ($groupPermissions as $permission)
                                    <div class="relative flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="permission-{{ $permission->id }}" 
                                                   name="permissions[]" 
                                                   type="checkbox" 
                                                   value="{{ $permission->id }}" 
                                                   data-group="{{ $groupName }}"
                                                   class="permission-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="permission-{{ $permission->id }}" class="font-medium text-gray-700 cursor-pointer hover:text-indigo-600">
                                                @php
                                                    // Buat deskripsi yang lebih mudah dipahami
                                                    $readableName = $permission->description;
                                                    
                                                    if (!$readableName) {
                                                        $name = $permission->name;
                                                        
                                                        // Mapping nama permission ke deskripsi yang mudah dipahami
                                                        $permissionMap = [
                                                            'master-user' => 'Kelola Data Pengguna',
                                                            'master-kontainer' => 'Kelola Data Kontainer',
                                                            'master-kegiatan' => 'Kelola Data Kegiatan',
                                                            'master-tujuan' => 'Kelola Data Tujuan',
                                                            'master-permission' => 'Kelola Izin Akses',
                                                            'master-mobil' => 'Kelola Data Mobil',
                                                            'master-pricelist-sewa-kontainer' => 'Kelola Harga Sewa Kontainer',
                                                            'pranota-create' => 'Buat Pranota Baru',
                                                            'pranota-edit' => 'Edit Pranota',
                                                            'pranota-view' => 'Lihat Pranota',
                                                            'pranota-delete' => 'Hapus Pranota',
                                                            'pembayaran-view' => 'Lihat Pembayaran',
                                                            'pembayaran-create' => 'Buat Pembayaran',
                                                            'pembayaran-edit' => 'Edit Pembayaran',
                                                            'laporan-view' => 'Lihat Laporan',
                                                            'laporan-export' => 'Export Laporan',
                                                            'approval-pranota' => 'Setujui Pranota',
                                                            'approval-pembayaran' => 'Setujui Pembayaran'
                                                        ];
                                                        
                                                        // Cari exact match dulu
                                                        if (isset($permissionMap[$name])) {
                                                            $readableName = $permissionMap[$name];
                                                        } 
                                                        // Jika tidak ada exact match, coba deteksi pattern
                                                        else if (str_contains($name, 'master-pricelist')) {
                                                            $readableName = 'Kelola Harga Sewa Kontainer';
                                                        }
                                                        else if (str_contains($name, 'master-')) {
                                                            $part = str_replace('master-', '', $name);
                                                            $readableName = 'Kelola Data ' . ucwords(str_replace('-', ' ', $part));
                                                        }
                                                        else if (str_contains($name, 'pranota')) {
                                                            if (str_contains($name, 'create')) $readableName = 'Buat Pranota';
                                                            else if (str_contains($name, 'edit')) $readableName = 'Edit Pranota';
                                                            else if (str_contains($name, 'view')) $readableName = 'Lihat Pranota';
                                                            else if (str_contains($name, 'delete')) $readableName = 'Hapus Pranota';
                                                            else $readableName = 'Kelola Pranota';
                                                        }
                                                        else if (str_contains($name, 'pembayaran')) {
                                                            if (str_contains($name, 'create')) $readableName = 'Buat Pembayaran';
                                                            else if (str_contains($name, 'edit')) $readableName = 'Edit Pembayaran';
                                                            else if (str_contains($name, 'view')) $readableName = 'Lihat Pembayaran';
                                                            else $readableName = 'Kelola Pembayaran';
                                                        }
                                                        else if (str_contains($name, 'approval')) {
                                                            $readableName = 'Persetujuan ' . ucwords(str_replace(['approval-', '-'], ['', ' '], $name));
                                                        }
                                                        else {
                                                            // Fallback: convert ke title case
                                                            $readableName = ucwords(str_replace('-', ' ', $name));
                                                        }
                                                    }
                                                @endphp
                                                {{ $readableName }}
                                            </label>
                                            @if (!$permission->description)
                                                <p class="text-xs text-gray-500">{{ $permission->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
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
            const allCheckboxes = document.querySelectorAll('.permission-checkbox');
            const permissionCount = document.getElementById('permission_count');

            // Update counter
            function updatePermissionCount() {
                const checkedCount = document.querySelectorAll('.permission-checkbox:checked').length;
                permissionCount.textContent = checkedCount;
            }

            // Tombol Select All
            document.getElementById('select_all').addEventListener('click', function() {
                allCheckboxes.forEach(cb => cb.checked = true);
                updatePermissionCount();
            });

            // Tombol Deselect All
            document.getElementById('deselect_all').addEventListener('click', function() {
                allCheckboxes.forEach(cb => cb.checked = false);
                updatePermissionCount();
            });

            // Tombol Select Basic (izin dasar untuk staff)
            document.getElementById('select_basic').addEventListener('click', function() {
                allCheckboxes.forEach(cb => cb.checked = false);
                // Select basic permissions
                const basicPerms = ['view', 'create', 'edit']; // kata kunci untuk izin dasar
                allCheckboxes.forEach(cb => {
                    const label = cb.nextElementSibling.textContent.toLowerCase();
                    if (basicPerms.some(perm => label.includes(perm))) {
                        cb.checked = true;
                    }
                });
                updatePermissionCount();
            });

            // Tombol Select Admin (semua izin)
            document.getElementById('select_admin').addEventListener('click', function() {
                allCheckboxes.forEach(cb => cb.checked = true);
                updatePermissionCount();
            });

            // Group select/deselect buttons
            document.querySelectorAll('.group-select-all').forEach(btn => {
                btn.addEventListener('click', function() {
                    const group = this.dataset.group;
                    document.querySelectorAll(`[data-group="${group}"]`).forEach(cb => cb.checked = true);
                    updatePermissionCount();
                });
            });

            document.querySelectorAll('.group-deselect-all').forEach(btn => {
                btn.addEventListener('click', function() {
                    const group = this.dataset.group;
                    document.querySelectorAll(`[data-group="${group}"]`).forEach(cb => cb.checked = false);
                    updatePermissionCount();
                });
            });

            // Update counter when individual checkboxes change
            allCheckboxes.forEach(cb => {
                cb.addEventListener('change', updatePermissionCount);
            });

            // Initialize counter
            updatePermissionCount();
        });
    </script>
@endpush
