@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('page_title', 'Edit Pengguna')

@section('content')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <style>
        /* Permission Matrix Styles */
        .permission-matrix {
            border-collapse: collapse;
            width: 100%;
        }

        .permission-matrix th,
        .permission-matrix td {
            padding: 8px 12px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .permission-matrix th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .module-header {
            text-align: left !important;
            font-weight: 600;
            background-color: #f3f4f6;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .module-header:hover {
            background-color: #e5e7eb;
        }

        .submodule {
            text-align: left !important;
            padding-left: 40px !important;
            background-color: #fafafa;
        }

        .empty-cell {
            background-color: #f9fafb;
            cursor: not-allowed;
        }

        .permission-checkbox {
            accent-color: #3b82f6;
            transform: scale(1.1);
            cursor: pointer;
        }

        .expand-icon {
            display: inline-block;
            width: 20px;
            text-align: center;
            transition: transform 0.2s;
            cursor: pointer;
        }

        .module-row.expanded .module-header {
            background-color: #dbeafe;
        }

        .submodule-row {
            transition: all 0.2s ease;
        }

        .submodule-row.visible {
            background-color: #fefefe;
        }

        /* Copy Permission Feature Styles */
        .copy-permission-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .permission-matrix {
                font-size: 12px;
            }

            .permission-matrix th,
            .permission-matrix td {
                padding: 6px 8px;
            }

            .submodule {
                padding-left: 30px !important;
            }
        }

        /* Toast Styles */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 12px 16px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        /* Module icons */
        .module-icon {
            margin-right: 8px;
            color: #6b7280;
        }

        /* Form styling improvements */
        .form-input {
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Button improvements */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* Permission section styling */
        .permission-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .permission-section-header {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }

        .permission-section-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            font-size: 20px;
            color: white;
        }

        .permission-section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }

        /* Table improvements */
        .permission-matrix {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .permission-matrix tbody tr:hover {
            background-color: #f8fafc;
        }

        .permission-matrix .module-row:hover {
            background-color: #e2e8f0;
        }

        /* Responsive improvements */
        @media (max-width: 1024px) {
            .permission-matrix {
                font-size: 13px;
            }

            .permission-matrix th,
            .permission-matrix td {
                padding: 10px 8px;
            }
        }

        @media (max-width: 768px) {
            .permission-matrix {
                font-size: 12px;
            }

            .permission-matrix th,
            .permission-matrix td {
                padding: 8px 6px;
            }

            .submodule {
                padding-left: 20px !important;
            }

            .permission-section {
                padding: 16px;
            }
        }

        /* Loading states */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Animation for expand/collapse */
        .submodule-row {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Sistem Izin Akses (Accurate Style)</h3>
            </div>

            {{-- Permission Matrix --}}
            <div class="permission-section">
                <div class="permission-section-header">
                    <div class="permission-section-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;"></div>
                    <div>
                        <h4 class="permission-section-title">Matriks Izin Akses Detail</h4>
                        <p class="text-sm text-gray-600 mt-1">Atur izin akses untuk setiap modul dan sub-modul</p>
                    </div>
                </div>

                <div class="overflow-x-auto bg-white rounded-lg border border-gray-200">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            <strong>Tip:</strong> Gunakan tombol "Centang Semua" untuk memberikan semua izin akses, atau centang secara manual untuk kontrol lebih detail.
                        </div>
                        <button type="button" id="check_all_permissions" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors">
                            Centang Semua
                        </button>
                    </div>
                    <table class="permission-matrix">
                        <thead>
                            <tr>
                                <th class="module-header" style="min-width: 200px;">Modul / Sub-Modul</th>
                                <th style="min-width: 80px;">Lihat<br><small class="text-xs text-gray-500">(View)</small></th>
                                <th style="min-width: 80px;">Input<br><small class="text-xs text-gray-500">(Create)</small></th>
                                <th style="min-width: 80px;">Edit<br><small class="text-xs text-gray-500">(Update)</small></th>
                                <th style="min-width: 80px;">Hapus<br><small class="text-xs text-gray-500">(Delete)</small></th>
                                <th style="min-width: 80px;">Setuju<br><small class="text-xs text-gray-500">(Approve)</small></th>
                                <th style="min-width: 80px;">Cetak<br><small class="text-xs text-gray-500">(Print)</small></th>
                                <th style="min-width: 80px;">Export<br><small class="text-xs text-gray-500">(Export)</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Dashboard --}}
                            <tr class="module-row" data-module="dashboard">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">Dashboard</div>
                                            <div class="text-xs text-gray-500">Halaman utama sistem</div>
                                        </div>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[dashboard][view]" value="1" class="permission-checkbox" checked></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- User Approval --}}
                            <tr class="module-row" data-module="user-approval">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">Persetujuan User</div>
                                            <div class="text-xs text-gray-500">Modul approval user baru</div>
                                        </div>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[user-approval][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['user-approval']['view']) && $userMatrixPermissions['user-approval']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[user-approval][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['user-approval']['create']) && $userMatrixPermissions['user-approval']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[user-approval][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['user-approval']['update']) && $userMatrixPermissions['user-approval']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[user-approval][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['user-approval']['delete']) && $userMatrixPermissions['user-approval']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[user-approval][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['user-approval']['print']) && $userMatrixPermissions['user-approval']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[user-approval][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['user-approval']['export']) && $userMatrixPermissions['user-approval']['export']) checked @endif></td>
                            </tr>

                            {{-- Master Data --}}
                            <tr class="module-row" data-module="master">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">Master Data</div>
                                            <div class="text-xs text-gray-500">Data master sistem</div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="7" class="text-center text-gray-500 text-sm py-3">
                                    Header menu - izin ditentukan oleh sub-menu
                                </td>
                            </tr>

                            {{-- Master Data Sub-modules --}}
                            {{-- Hidden checkbox for master-karyawan main permission --}}
                            <input type="hidden" name="permissions[master-karyawan][main]" value="0" id="master-karyawan-main">
                            <input type="checkbox" name="permissions[master-karyawan][main]" value="1" id="master-karyawan-main-checkbox" class="hidden" @if(isset($userMatrixPermissions['master-karyawan']['main']) && $userMatrixPermissions['master-karyawan']['main']) checked @endif>

                            {{-- Data Karyawan --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Karyawan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-karyawan][view]" value="1" class="permission-checkbox karyawan-permission" @if(isset($userMatrixPermissions['master-karyawan']['view']) && $userMatrixPermissions['master-karyawan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-karyawan][create]" value="1" class="permission-checkbox karyawan-permission" @if(isset($userMatrixPermissions['master-karyawan']['create']) && $userMatrixPermissions['master-karyawan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-karyawan][update]" value="1" class="permission-checkbox karyawan-permission" @if(isset($userMatrixPermissions['master-karyawan']['update']) && $userMatrixPermissions['master-karyawan']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-karyawan][delete]" value="1" class="permission-checkbox karyawan-permission" @if(isset($userMatrixPermissions['master-karyawan']['delete']) && $userMatrixPermissions['master-karyawan']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[master-karyawan][print]" value="1" class="permission-checkbox karyawan-permission" @if(isset($userMatrixPermissions['master-karyawan']['print']) && $userMatrixPermissions['master-karyawan']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-karyawan][export]" value="1" class="permission-checkbox karyawan-permission" @if(isset($userMatrixPermissions['master-karyawan']['export']) && $userMatrixPermissions['master-karyawan']['export']) checked @endif></td>
                            </tr>

                            {{-- Data User --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data User</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-user][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-user']['view']) && $userMatrixPermissions['master-user']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-user][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-user']['create']) && $userMatrixPermissions['master-user']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-user][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-user']['update']) && $userMatrixPermissions['master-user']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-user][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-user']['delete']) && $userMatrixPermissions['master-user']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Kontainer --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-kontainer][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kontainer']['view']) && $userMatrixPermissions['master-kontainer']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kontainer][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kontainer']['create']) && $userMatrixPermissions['master-kontainer']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kontainer][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kontainer']['update']) && $userMatrixPermissions['master-kontainer']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kontainer][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kontainer']['delete']) && $userMatrixPermissions['master-kontainer']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Tujuan --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Tujuan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-tujuan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-tujuan']['view']) && $userMatrixPermissions['master-tujuan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-tujuan][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-tujuan']['create']) && $userMatrixPermissions['master-tujuan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-tujuan][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-tujuan']['update']) && $userMatrixPermissions['master-tujuan']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-tujuan][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-tujuan']['delete']) && $userMatrixPermissions['master-tujuan']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Kegiatan --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Kegiatan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-kegiatan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kegiatan']['view']) && $userMatrixPermissions['master-kegiatan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kegiatan][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kegiatan']['create']) && $userMatrixPermissions['master-kegiatan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kegiatan][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kegiatan']['update']) && $userMatrixPermissions['master-kegiatan']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kegiatan][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kegiatan']['delete']) && $userMatrixPermissions['master-kegiatan']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Permission --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Permission</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-permission][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-permission']['view']) && $userMatrixPermissions['master-permission']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-permission][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-permission']['create']) && $userMatrixPermissions['master-permission']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-permission][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-permission']['update']) && $userMatrixPermissions['master-permission']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-permission][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-permission']['delete']) && $userMatrixPermissions['master-permission']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Mobil --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Mobil</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-mobil][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-mobil']['view']) && $userMatrixPermissions['master-mobil']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-mobil][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-mobil']['create']) && $userMatrixPermissions['master-mobil']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-mobil][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-mobil']['update']) && $userMatrixPermissions['master-mobil']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-mobil][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-mobil']['delete']) && $userMatrixPermissions['master-mobil']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Divisi --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Divisi</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-divisi][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-divisi']['view']) && $userMatrixPermissions['master-divisi']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-divisi][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-divisi']['create']) && $userMatrixPermissions['master-divisi']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-divisi][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-divisi']['update']) && $userMatrixPermissions['master-divisi']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-divisi][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-divisi']['delete']) && $userMatrixPermissions['master-divisi']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Pekerjaan --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Pekerjaan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-pekerjaan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pekerjaan']['view']) && $userMatrixPermissions['master-pekerjaan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pekerjaan][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pekerjaan']['create']) && $userMatrixPermissions['master-pekerjaan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pekerjaan][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pekerjaan']['update']) && $userMatrixPermissions['master-pekerjaan']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pekerjaan][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pekerjaan']['delete']) && $userMatrixPermissions['master-pekerjaan']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[master-pekerjaan][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pekerjaan']['print']) && $userMatrixPermissions['master-pekerjaan']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pekerjaan][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pekerjaan']['export']) && $userMatrixPermissions['master-pekerjaan']['export']) checked @endif></td>
                            </tr>




                            {{-- Tagihan Kontainer Sewa --}}
                            <tr class="module-row" data-module="tagihan-kontainer">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">Tagihan Kontainer Sewa & Pranota</div>
                                            <div class="text-xs text-gray-500">Modul tagihan, pembayaran kontainer, pranota dan permohonan</div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="7" class="text-center text-gray-500 text-sm py-3">
                                    Header menu - izin ditentukan oleh sub-menu
                                </td>
                            </tr>

                            {{-- Tagihan Kontainer Sewa Sub-modules --}}
                            {{-- Daftar Tagihan Kontainer --}}
                            <tr class="submodule-row" data-parent="tagihan-kontainer">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Daftar Tagihan Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer']['view']) && $userMatrixPermissions['tagihan-kontainer']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer']['create']) && $userMatrixPermissions['tagihan-kontainer']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer']['update']) && $userMatrixPermissions['tagihan-kontainer']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer']['delete']) && $userMatrixPermissions['tagihan-kontainer']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer']['print']) && $userMatrixPermissions['tagihan-kontainer']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer']['export']) && $userMatrixPermissions['tagihan-kontainer']['export']) checked @endif></td>
                            </tr>

                            {{-- Daftar Pranota Kontainer --}}
                            <tr class="submodule-row" data-parent="tagihan-kontainer">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Daftar Pranota Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota']['view']) && $userMatrixPermissions['pranota']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota']['create']) && $userMatrixPermissions['pranota']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota']['update']) && $userMatrixPermissions['pranota']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota']['delete']) && $userMatrixPermissions['pranota']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[pranota][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota']['print']) && $userMatrixPermissions['pranota']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota']['export']) && $userMatrixPermissions['pranota']['export']) checked @endif></td>
                            </tr>

                            {{-- Pembayaran Pranota Kontainer --}}
                            <tr class="submodule-row" data-parent="tagihan-kontainer">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pembayaran Pranota Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['view']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['create']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['update']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['delete']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['print']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['export']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['export']) checked @endif></td>
                            </tr>



                            {{-- Perbaikan Kontainer --}}
                            <tr class="module-row" data-module="perbaikan-kontainer">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">Perbaikan Kontainer</div>
                                            <div class="text-xs text-gray-500">Modul perbaikan, pranota dan pembayaran perbaikan kontainer</div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="7" class="text-center text-gray-500 text-sm py-3">
                                    Header menu - izin ditentukan oleh sub-menu
                                </td>
                            </tr>

                            {{-- Perbaikan Kontainer Sub-modules --}}
                            {{-- Daftar Perbaikan Kontainer --}}
                            <tr class="submodule-row" data-parent="perbaikan-kontainer">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Daftar Perbaikan Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[perbaikan-kontainer][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['perbaikan-kontainer']['view']) && $userMatrixPermissions['perbaikan-kontainer']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[perbaikan-kontainer][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['perbaikan-kontainer']['create']) && $userMatrixPermissions['perbaikan-kontainer']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[perbaikan-kontainer][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['perbaikan-kontainer']['update']) && $userMatrixPermissions['perbaikan-kontainer']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[perbaikan-kontainer][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['perbaikan-kontainer']['delete']) && $userMatrixPermissions['perbaikan-kontainer']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[perbaikan-kontainer][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['perbaikan-kontainer']['print']) && $userMatrixPermissions['perbaikan-kontainer']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[perbaikan-kontainer][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['perbaikan-kontainer']['export']) && $userMatrixPermissions['perbaikan-kontainer']['export']) checked @endif></td>
                            </tr>

                            {{-- Pranota Perbaikan Kontainer --}}
                            <tr class="submodule-row" data-parent="perbaikan-kontainer">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pranota Perbaikan Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['view']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['create']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['update']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['delete']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['print']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['export']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['export']) checked @endif></td>
                            </tr>

                            {{-- Pembayaran Pranota Perbaikan Kontainer --}}
                            <tr class="submodule-row" data-parent="perbaikan-kontainer">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pembayaran Pranota Perbaikan Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-perbaikan-kontainer][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['view']) && $userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-perbaikan-kontainer][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['create']) && $userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-perbaikan-kontainer][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['update']) && $userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-perbaikan-kontainer][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['delete']) && $userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-perbaikan-kontainer][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['print']) && $userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-perbaikan-kontainer][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['export']) && $userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['export']) checked @endif></td>
                            </tr>

                            {{-- Menu Supir --}}
                            <tr class="module-row" data-module="permohonan-memo">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">Menu Supir</div>
                                            <div class="text-xs text-gray-500">Modul menu supir dan approval</div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="7" class="text-center text-gray-500 text-sm py-3">
                                    Header menu - izin ditentukan oleh sub-menu
                                </td>
                            </tr>

                            {{-- Menu Supir Sub-modules --}}
                            {{-- Memo Permohonan --}}
                            <tr class="submodule-row" data-parent="permohonan-memo">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Memo Permohonan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['view']) && $userMatrixPermissions['permohonan-memo']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['create']) && $userMatrixPermissions['permohonan-memo']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['update']) && $userMatrixPermissions['permohonan-memo']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['delete']) && $userMatrixPermissions['permohonan-memo']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['print']) && $userMatrixPermissions['permohonan-memo']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['export']) && $userMatrixPermissions['permohonan-memo']['export']) checked @endif></td>
                            </tr>

                            {{-- Pranota Supir --}}
                            <tr class="submodule-row" data-parent="permohonan-memo">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pranota Supir</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-supir][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-supir']['view']) && $userMatrixPermissions['pranota-supir']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-supir][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-supir']['create']) && $userMatrixPermissions['pranota-supir']['create']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[pranota-supir][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-supir']['print']) && $userMatrixPermissions['pranota-supir']['print']) checked @endif></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Pembayaran Pranota Supir --}}
                            <tr class="submodule-row" data-parent="permohonan-memo">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pembayaran Pranota Supir</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-supir']['view']) && $userMatrixPermissions['pembayaran-pranota-supir']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-supir']['create']) && $userMatrixPermissions['pembayaran-pranota-supir']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-supir']['update']) && $userMatrixPermissions['pembayaran-pranota-supir']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-supir']['delete']) && $userMatrixPermissions['pembayaran-pranota-supir']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-supir']['approve']) && $userMatrixPermissions['pembayaran-pranota-supir']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-supir']['print']) && $userMatrixPermissions['pembayaran-pranota-supir']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-supir']['export']) && $userMatrixPermissions['pembayaran-pranota-supir']['export']) checked @endif></td>
                            </tr>

                            {{-- Approval Tugas --}}
                            <tr class="module-row" data-module="approval-tugas">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">Approval Tugas</div>
                                            <div class="text-xs text-gray-500">Modul approval dan penyelesaian tugas supir</div>
                                        </div>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[approval-permohonan-memo][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-permohonan-memo']['view']) && $userMatrixPermissions['approval-permohonan-memo']['view']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[approval-permohonan-memo][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-permohonan-memo']['approve']) && $userMatrixPermissions['approval-permohonan-memo']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[approval-permohonan-memo][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-permohonan-memo']['print']) && $userMatrixPermissions['approval-permohonan-memo']['print']) checked @endif></td>
                                <td class="empty-cell"></td>
                            </tr>

                        </tbody>
                    </table>
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
                            <h4 class="text-sm font-medium text-indigo-800">Copy Permission dari User Lain</h4>
                            <p class="text-sm text-indigo-700 mt-1">
                                Pilih user yang sudah ada untuk menyalin semua izin aksesnya ke user ini.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <select id="copy_user_select" class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih User --</option>
                            @foreach($users as $existingUser)
                                <option value="{{ $existingUser->id }}">
                                    {{ $existingUser->username }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="copy_permissions_btn" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            Copy Permission
                        </button>
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
            // ==========================================
            // INITIALIZATION
            // ==========================================

            // Initialize Choices.js for karyawan select
            initializeKaryawanSelect();

            // Initialize module expand/collapse functionality
            initializeModuleExpansion();

            // Initialize permission controls
            initializePermissionControls();

            // ==========================================
            // KARYAWAN SELECT INITIALIZATION
            // ==========================================

            function initializeKaryawanSelect() {
                const karyawanElement = document.getElementById('karyawan_id');
                if (karyawanElement) {
                    new Choices(karyawanElement, {
                        searchEnabled: true,
                        shouldSort: false,
                        placeholder: true,
                        placeholderValue: 'Cari atau pilih karyawan...'
                    });
                }
            }

            // ==========================================
            // MODULE EXPANSION FUNCTIONALITY
            // ==========================================

            function initializeModuleExpansion() {
                // Initially hide all sub-modules
                document.querySelectorAll('.submodule-row').forEach(function(row) {
                    row.style.display = 'none';
                });

                // Hide expand icons for modules that don't have sub-modules
                document.querySelectorAll('.module-row').forEach(function(row) {
                    const moduleName = row.dataset.module;
                    const submodules = document.querySelectorAll(`[data-parent="${moduleName}"]`);
                    const expandIcon = row.querySelector('.expand-icon');

                    if (submodules.length === 0 && expandIcon) {
                        // Hide the expand icon if no sub-modules exist
                        expandIcon.style.display = 'none';
                    } else if (expandIcon) {
                        // Add click listener only if there are sub-modules
                        row.addEventListener('click', function(e) {
                            // Don't trigger if clicking on a checkbox
                            if (e.target.type === 'checkbox') {
                                return;
                            }

                            const module = this.dataset.module;
                            const icon = this.querySelector('.expand-icon');
                            const isExpanded = icon.classList.contains('expanded');

                            if (isExpanded) {
                                collapseModule(module);
                            } else {
                                expandModule(module);
                            }
                        });
                    }
                });
            }

            function expandModule(moduleName) {
                const moduleRow = document.querySelector(`[data-module="${moduleName}"]`);
                const icon = moduleRow.querySelector('.expand-icon');
                const submodules = document.querySelectorAll(`[data-parent="${moduleName}"]`);

                // Update icon only if it exists
                if (icon) {
                    icon.classList.add('expanded');
                    icon.textContent = '▼';
                }

                // Show sub-modules
                submodules.forEach(function(submodule) {
                    submodule.style.display = 'table-row';
                    submodule.classList.add('visible');
                });

                // Update row styling
                moduleRow.classList.add('expanded');
            }

            function collapseModule(moduleName) {
                const moduleRow = document.querySelector(`[data-module="${moduleName}"]`);
                const icon = moduleRow.querySelector('.expand-icon');
                const submodules = document.querySelectorAll(`[data-parent="${moduleName}"]`);

                // Update icon only if it exists
                if (icon) {
                    icon.classList.remove('expanded');
                    icon.textContent = '▶';
                }

                // Hide sub-modules
                submodules.forEach(function(submodule) {
                    submodule.style.display = 'none';
                    submodule.classList.remove('visible');
                });

                // Update row styling
                moduleRow.classList.remove('expanded');
            }

            // ==========================================
            // PERMISSION CONTROLS
            // ==========================================

            function initializePermissionControls() {
                // Initialize karyawan permission logic
                initializeKaryawanPermissions();

                // Initialize copy permission feature
                initializeCopyPermissions();

                // Initialize check all permissions
                initializeCheckAllPermissions();
            }

            function initializeKaryawanPermissions() {
                // Add event listeners to karyawan permission checkboxes
                document.querySelectorAll('.karyawan-permission').forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        updateKaryawanMainPermission();

                        const karyawanCheckboxes = document.querySelectorAll('.karyawan-permission');
                        const anyChecked = Array.from(karyawanCheckboxes).some(cb => cb.checked);

                        if (anyChecked) {
                            showToast('Permission menu Master Karyawan telah diaktifkan', 'success');
                        } else {
                            showToast('Permission menu Master Karyawan telah dinonaktifkan', 'warning');
                        }
                    });
                });

                // Initialize main permission on page load
                updateKaryawanMainPermission();
            }

            function updateKaryawanMainPermission() {
                const mainCheckbox = document.getElementById('master-karyawan-main-checkbox');
                const karyawanCheckboxes = document.querySelectorAll('.karyawan-permission');

                if (!mainCheckbox) {
                    console.warn('Master karyawan main checkbox not found');
                    return;
                }

                // Check if any karyawan permission is checked
                const anyChecked = Array.from(karyawanCheckboxes).some(cb => cb.checked);

                // Set main permission accordingly
                mainCheckbox.checked = anyChecked;

                // Update hidden input value
                const hiddenInput = document.getElementById('master-karyawan-main');
                if (hiddenInput) {
                    hiddenInput.value = anyChecked ? '1' : '0';
                }

                console.log('Karyawan main permission updated:', anyChecked);
            }

            function initializeCopyPermissions() {
                const copyBtn = document.getElementById('copy_permissions_btn');
                const select = document.getElementById('copy_user_select');

                // Enable/disable copy button based on selection
                select.addEventListener('change', function(){
                    copyBtn.disabled = !this.value;
                });

                // Initialize copy button state
                copyBtn.disabled = true;

                // Copy permission functionality
                copyBtn.addEventListener('click', function(){
                    const userId = select.value;

                    if (!userId) {
                        showToast('Pilih user terlebih dahulu', 'warning');
                        return;
                    }

                    // Show loading state
                    this.disabled = true;
                    this.innerHTML = 'Loading...';

                    // Fetch user permissions via AJAX
                    fetch(`/master/user/${userId}/permissions-for-copy`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const permissions = data.permissions;
                                const userName = data.user.username;

                                // Uncheck all permissions first
                                document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);

                                // Apply permissions from the copied user
                                Object.keys(permissions).forEach(module => {
                                    if (permissions[module] && typeof permissions[module] === 'object') {
                                        Object.keys(permissions[module]).forEach(action => {
                                            const checkbox = document.querySelector(`input[name="permissions[${module}][${action}]"]`);
                                            if (checkbox) {
                                                checkbox.checked = true;
                                            }
                                        });
                                    }
                                });

                                showToast(`Berhasil menyalin permission dari ${userName}`, 'success');
                            } else {
                                showToast('Gagal mengambil data permission', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Terjadi kesalahan saat mengambil data', 'error');
                        })
                        .finally(() => {
                            // Reset button state
                            this.disabled = false;
                            this.innerHTML = '📋 Copy Permission';
                        });
                });
            }

            function initializeCheckAllPermissions() {
                document.getElementById('check_all_permissions').addEventListener('click', function() {
                    const checkboxes = document.querySelectorAll('.permission-checkbox');
                    const isAllChecked = Array.from(checkboxes).every(cb => cb.checked);

                    if (isAllChecked) {
                        // If all are checked, uncheck them
                        checkboxes.forEach(cb => cb.checked = false);
                        this.innerHTML = 'Centang Semua';
                        this.classList.remove('bg-red-600', 'hover:bg-red-700');
                        this.classList.add('bg-blue-600', 'hover:bg-blue-700');
                        showToast('Semua izin telah dihapus', 'warning');
                    } else {
                        // If not all checked, check them all
                        checkboxes.forEach(cb => cb.checked = true);
                        this.innerHTML = 'Hapus Semua';
                        this.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                        this.classList.add('bg-red-600', 'hover:bg-red-700');
                        showToast('Semua izin telah dicentang', 'success');
                    }
                });
            }

            // ==========================================
            // UTILITY FUNCTIONS
            // ==========================================

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

                // Auto remove after 3 seconds
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        });
    </script>
@endpush
