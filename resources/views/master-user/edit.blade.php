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

        .permission-checkbox:indeterminate {
            accent-color: #f59e0b; /* Orange color for indeterminate state */
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
                        <option value="{{ $karyawan->id }}" data-nama="{{ $karyawan->nama_panggilan ?: $karyawan->nama_lengkap }}" @if(old('karyawan_id', $user->karyawan_id) == $karyawan->id) selected @endif>{{ $karyawan->nama_panggilan ?: $karyawan->nama_lengkap }} @if($karyawan->nik) ({{ $karyawan->nik }}) @endif</option>
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
                                        <span class="expand-icon text-lg mr-2" style="display: none;"></span>
                                        <div>
                                            <div class="font-semibold">Dashboard</div>
                                            <div class="text-xs text-gray-500">Akses halaman dashboard sistem</div>
                                        </div>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[dashboard][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['dashboard']['view']) && $userMatrixPermissions['dashboard']['view']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
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
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="master-header-checkbox permission-checkbox" data-permission="view">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="master-header-checkbox permission-checkbox" data-permission="create">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="master-header-checkbox permission-checkbox" data-permission="update">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="master-header-checkbox permission-checkbox" data-permission="delete">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="master-header-checkbox permission-checkbox" data-permission="approve">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="master-header-checkbox permission-checkbox" data-permission="print">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="master-header-checkbox permission-checkbox" data-permission="export">
                                </td>
                            </tr>

                            {{-- Master Data Sub-modules --}}

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

                            {{-- Data COA --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data COA</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-coa][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-coa']['view']) && $userMatrixPermissions['master-coa']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-coa][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-coa']['create']) && $userMatrixPermissions['master-coa']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-coa][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-coa']['update']) && $userMatrixPermissions['master-coa']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-coa][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-coa']['delete']) && $userMatrixPermissions['master-coa']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Tipe Akun --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Tipe Akun</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-tipe-akun][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-tipe-akun']['view']) && $userMatrixPermissions['master-tipe-akun']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-tipe-akun][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-tipe-akun']['create']) && $userMatrixPermissions['master-tipe-akun']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-tipe-akun][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-tipe-akun']['update']) && $userMatrixPermissions['master-tipe-akun']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-tipe-akun][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-tipe-akun']['delete']) && $userMatrixPermissions['master-tipe-akun']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Cabang --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Cabang</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-cabang][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-cabang']['view']) && $userMatrixPermissions['master-cabang']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-cabang][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-cabang']['create']) && $userMatrixPermissions['master-cabang']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-cabang][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-cabang']['update']) && $userMatrixPermissions['master-cabang']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-cabang][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-cabang']['delete']) && $userMatrixPermissions['master-cabang']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Kode Nomor --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Kode Nomor</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-kode-nomor][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kode-nomor']['view']) && $userMatrixPermissions['master-kode-nomor']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kode-nomor][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kode-nomor']['create']) && $userMatrixPermissions['master-kode-nomor']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kode-nomor][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kode-nomor']['update']) && $userMatrixPermissions['master-kode-nomor']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kode-nomor][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kode-nomor']['delete']) && $userMatrixPermissions['master-kode-nomor']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Nomor Terakhir --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Nomor Terakhir</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-nomor-terakhir][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-nomor-terakhir']['view']) && $userMatrixPermissions['master-nomor-terakhir']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-nomor-terakhir][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-nomor-terakhir']['create']) && $userMatrixPermissions['master-nomor-terakhir']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-nomor-terakhir][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-nomor-terakhir']['update']) && $userMatrixPermissions['master-nomor-terakhir']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-nomor-terakhir][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-nomor-terakhir']['delete']) && $userMatrixPermissions['master-nomor-terakhir']['delete']) checked @endif></td>
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

                            {{-- Data Pengirim --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Pengirim</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-pengirim][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pengirim']['view']) && $userMatrixPermissions['master-pengirim']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pengirim][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pengirim']['create']) && $userMatrixPermissions['master-pengirim']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pengirim][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pengirim']['update']) && $userMatrixPermissions['master-pengirim']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pengirim][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pengirim']['delete']) && $userMatrixPermissions['master-pengirim']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Jenis Barang --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Jenis Barang</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-jenis-barang][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-jenis-barang']['view']) && $userMatrixPermissions['master-jenis-barang']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-jenis-barang][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-jenis-barang']['create']) && $userMatrixPermissions['master-jenis-barang']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-jenis-barang][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-jenis-barang']['update']) && $userMatrixPermissions['master-jenis-barang']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-jenis-barang][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-jenis-barang']['delete']) && $userMatrixPermissions['master-jenis-barang']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Master Klasifikasi Biaya --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Master Klasifikasi Biaya</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-klasifikasi-biaya][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-klasifikasi-biaya']['view']) && $userMatrixPermissions['master-klasifikasi-biaya']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-klasifikasi-biaya][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-klasifikasi-biaya']['create']) && $userMatrixPermissions['master-klasifikasi-biaya']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-klasifikasi-biaya][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-klasifikasi-biaya']['update']) && $userMatrixPermissions['master-klasifikasi-biaya']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-klasifikasi-biaya][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-klasifikasi-biaya']['delete']) && $userMatrixPermissions['master-klasifikasi-biaya']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Kelola BBM --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="inline-block w-3 h-3 bg-indigo-100 rounded-full mr-3"></span>
                                        <div class="text-sm font-medium">Kelola BBM</div>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-kelola-bbm][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kelola-bbm']['view']) && $userMatrixPermissions['master-kelola-bbm']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kelola-bbm][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kelola-bbm']['create']) && $userMatrixPermissions['master-kelola-bbm']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kelola-bbm][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kelola-bbm']['update']) && $userMatrixPermissions['master-kelola-bbm']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kelola-bbm][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kelola-bbm']['delete']) && $userMatrixPermissions['master-kelola-bbm']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Term --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Term</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-term][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-term']['view']) && $userMatrixPermissions['master-term']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-term][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-term']['create']) && $userMatrixPermissions['master-term']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-term][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-term']['update']) && $userMatrixPermissions['master-term']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-term][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-term']['delete']) && $userMatrixPermissions['master-term']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Tujuan Kirim --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Tujuan Kirim</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-tujuan-kirim][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-tujuan-kirim']['view']) && $userMatrixPermissions['master-tujuan-kirim']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-tujuan-kirim][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-tujuan-kirim']['create']) && $userMatrixPermissions['master-tujuan-kirim']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-tujuan-kirim][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-tujuan-kirim']['update']) && $userMatrixPermissions['master-tujuan-kirim']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-tujuan-kirim][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-tujuan-kirim']['delete']) && $userMatrixPermissions['master-tujuan-kirim']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Master Pengirim/Penerima --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Master Pengirim/Penerima</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-pengirim-penerima][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pengirim-penerima']['view']) && $userMatrixPermissions['master-pengirim-penerima']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pengirim-penerima][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pengirim-penerima']['create']) && $userMatrixPermissions['master-pengirim-penerima']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pengirim-penerima][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pengirim-penerima']['update']) && $userMatrixPermissions['master-pengirim-penerima']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pengirim-penerima][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pengirim-penerima']['delete']) && $userMatrixPermissions['master-pengirim-penerima']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Jenis Layanan Pelabuhan --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Jenis Layanan Pelabuhan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-jenis-layanan-pelabuhan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-jenis-layanan-pelabuhan']['view']) && $userMatrixPermissions['master-jenis-layanan-pelabuhan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-jenis-layanan-pelabuhan][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-jenis-layanan-pelabuhan']['create']) && $userMatrixPermissions['master-jenis-layanan-pelabuhan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-jenis-layanan-pelabuhan][edit]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-jenis-layanan-pelabuhan']['edit']) && $userMatrixPermissions['master-jenis-layanan-pelabuhan']['edit']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-jenis-layanan-pelabuhan][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-jenis-layanan-pelabuhan']['delete']) && $userMatrixPermissions['master-jenis-layanan-pelabuhan']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Master Pelayanan Pelabuhan --}}
                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Master Pelayanan Pelabuhan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-pelayanan-pelabuhan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pelayanan-pelabuhan']['view']) && $userMatrixPermissions['master-pelayanan-pelabuhan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pelayanan-pelabuhan][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pelayanan-pelabuhan']['create']) && $userMatrixPermissions['master-pelayanan-pelabuhan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pelayanan-pelabuhan][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pelayanan-pelabuhan']['update']) && $userMatrixPermissions['master-pelayanan-pelabuhan']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pelayanan-pelabuhan][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pelayanan-pelabuhan']['delete']) && $userMatrixPermissions['master-pelayanan-pelabuhan']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Operational Management --}}
                            <tr class="module-row" data-module="operational">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">Operational Management</div>
                                            <div class="text-xs text-gray-500">Modul pengelolaan operasional harian</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="operational-header-checkbox" data-permission="view">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="operational-header-checkbox" data-permission="create">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="operational-header-checkbox" data-permission="update">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="operational-header-checkbox" data-permission="delete">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="operational-header-checkbox" data-permission="approve">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="operational-header-checkbox" data-permission="print">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="operational-header-checkbox" data-permission="export">
                                </td>
                            </tr>

                            {{-- Order Management --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Order Management</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[order-management][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['order-management']['view']) && $userMatrixPermissions['order-management']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[order-management][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['order-management']['create']) && $userMatrixPermissions['order-management']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[order-management][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['order-management']['update']) && $userMatrixPermissions['order-management']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[order-management][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['order-management']['delete']) && $userMatrixPermissions['order-management']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[order-management][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['order-management']['approve']) && $userMatrixPermissions['order-management']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[order-management][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['order-management']['print']) && $userMatrixPermissions['order-management']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[order-management][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['order-management']['export']) && $userMatrixPermissions['order-management']['export']) checked @endif></td>
                            </tr>

                            {{-- Surat Jalan --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Surat Jalan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[surat-jalan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['surat-jalan']['view']) && $userMatrixPermissions['surat-jalan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[surat-jalan][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['surat-jalan']['create']) && $userMatrixPermissions['surat-jalan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[surat-jalan][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['surat-jalan']['update']) && $userMatrixPermissions['surat-jalan']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[surat-jalan][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['surat-jalan']['delete']) && $userMatrixPermissions['surat-jalan']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[surat-jalan][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['surat-jalan']['approve']) && $userMatrixPermissions['surat-jalan']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[surat-jalan][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['surat-jalan']['print']) && $userMatrixPermissions['surat-jalan']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[surat-jalan][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['surat-jalan']['export']) && $userMatrixPermissions['surat-jalan']['export']) checked @endif></td>
                            </tr>

                            {{-- Surat Jalan Bongkaran --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Surat Jalan Bongkaran</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[surat-jalan-bongkaran][view]" value="1" class="permission-checkbox" @if(old('permissions.surat-jalan-bongkaran.view') || (isset($userMatrixPermissions['surat-jalan-bongkaran']['view']) && $userMatrixPermissions['surat-jalan-bongkaran']['view']) || ($user && $user->can('surat-jalan-bongkaran-view'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[surat-jalan-bongkaran][create]" value="1" class="permission-checkbox" @if(old('permissions.surat-jalan-bongkaran.create') || (isset($userMatrixPermissions['surat-jalan-bongkaran']['create']) && $userMatrixPermissions['surat-jalan-bongkaran']['create']) || ($user && $user->can('surat-jalan-bongkaran-create'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[surat-jalan-bongkaran][update]" value="1" class="permission-checkbox" @if(old('permissions.surat-jalan-bongkaran.update') || (isset($userMatrixPermissions['surat-jalan-bongkaran']['update']) && $userMatrixPermissions['surat-jalan-bongkaran']['update']) || ($user && $user->can('surat-jalan-bongkaran-update'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[surat-jalan-bongkaran][delete]" value="1" class="permission-checkbox" @if(old('permissions.surat-jalan-bongkaran.delete') || (isset($userMatrixPermissions['surat-jalan-bongkaran']['delete']) && $userMatrixPermissions['surat-jalan-bongkaran']['delete']) || ($user && $user->can('surat-jalan-bongkaran-delete'))) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[surat-jalan-bongkaran][print]" value="1" class="permission-checkbox" @if(old('permissions.surat-jalan-bongkaran.print') || (isset($userMatrixPermissions['surat-jalan-bongkaran']['print']) && $userMatrixPermissions['surat-jalan-bongkaran']['print']) || ($user && $user->can('surat-jalan-bongkaran-print'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[surat-jalan-bongkaran][export]" value="1" class="permission-checkbox" @if(old('permissions.surat-jalan-bongkaran.export') || (isset($userMatrixPermissions['surat-jalan-bongkaran']['export']) && $userMatrixPermissions['surat-jalan-bongkaran']['export']) || ($user && $user->can('surat-jalan-bongkaran-export'))) checked @endif></td>
                            </tr>
                            {{-- Uang Jalan Bongkaran --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Uang Jalan Bongkaran</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[uang-jalan-bongkaran][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['uang-jalan-bongkaran']['view']) && $userMatrixPermissions['uang-jalan-bongkaran']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[uang-jalan-bongkaran][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['uang-jalan-bongkaran']['create']) && $userMatrixPermissions['uang-jalan-bongkaran']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[uang-jalan-bongkaran][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['uang-jalan-bongkaran']['update']) && $userMatrixPermissions['uang-jalan-bongkaran']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[uang-jalan-bongkaran][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['uang-jalan-bongkaran']['delete']) && $userMatrixPermissions['uang-jalan-bongkaran']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Tanda Terima --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Tanda Terima</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[tanda-terima][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tanda-terima']['view']) && $userMatrixPermissions['tanda-terima']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tanda-terima][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tanda-terima']['create']) && $userMatrixPermissions['tanda-terima']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tanda-terima][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tanda-terima']['update']) && $userMatrixPermissions['tanda-terima']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tanda-terima][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tanda-terima']['delete']) && $userMatrixPermissions['tanda-terima']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[tanda-terima][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tanda-terima']['print']) && $userMatrixPermissions['tanda-terima']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tanda-terima][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tanda-terima']['export']) && $userMatrixPermissions['tanda-terima']['export']) checked @endif></td>
                            </tr>

                            {{-- Tanda Terima Tanpa Surat Jalan --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Tanda Terima (Tanpa Surat Jalan)</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[tanda-terima-tanpa-surat-jalan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tanda-terima-tanpa-surat-jalan']['view']) && $userMatrixPermissions['tanda-terima-tanpa-surat-jalan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tanda-terima-tanpa-surat-jalan][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tanda-terima-tanpa-surat-jalan']['create']) && $userMatrixPermissions['tanda-terima-tanpa-surat-jalan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tanda-terima-tanpa-surat-jalan][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tanda-terima-tanpa-surat-jalan']['update']) && $userMatrixPermissions['tanda-terima-tanpa-surat-jalan']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tanda-terima-tanpa-surat-jalan][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tanda-terima-tanpa-surat-jalan']['delete']) && $userMatrixPermissions['tanda-terima-tanpa-surat-jalan']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Gate In --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Gate In</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[gate-in][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['gate-in']['view']) && $userMatrixPermissions['gate-in']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[gate-in][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['gate-in']['create']) && $userMatrixPermissions['gate-in']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[gate-in][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['gate-in']['update']) && $userMatrixPermissions['gate-in']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[gate-in][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['gate-in']['delete']) && $userMatrixPermissions['gate-in']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[gate-in][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['gate-in']['print']) && $userMatrixPermissions['gate-in']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[gate-in][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['gate-in']['export']) && $userMatrixPermissions['gate-in']['export']) checked @endif></td>
                            </tr>

                            {{-- Pranota Surat Jalan --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pranota Surat Jalan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-surat-jalan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-surat-jalan']['view']) && $userMatrixPermissions['pranota-surat-jalan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-surat-jalan][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-surat-jalan']['create']) && $userMatrixPermissions['pranota-surat-jalan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-surat-jalan][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-surat-jalan']['update']) && $userMatrixPermissions['pranota-surat-jalan']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-surat-jalan][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-surat-jalan']['delete']) && $userMatrixPermissions['pranota-surat-jalan']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-surat-jalan][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-surat-jalan']['approve']) && $userMatrixPermissions['pranota-surat-jalan']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-surat-jalan][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-surat-jalan']['print']) && $userMatrixPermissions['pranota-surat-jalan']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-surat-jalan][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-surat-jalan']['export']) && $userMatrixPermissions['pranota-surat-jalan']['export']) checked @endif></td>
                            </tr>

                            {{-- Uang Jalan --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Uang Jalan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[uang-jalan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['uang-jalan']['view']) && $userMatrixPermissions['uang-jalan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[uang-jalan][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['uang-jalan']['create']) && $userMatrixPermissions['uang-jalan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[uang-jalan][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['uang-jalan']['update']) && $userMatrixPermissions['uang-jalan']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[uang-jalan][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['uang-jalan']['delete']) && $userMatrixPermissions['uang-jalan']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[uang-jalan][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['uang-jalan']['approve']) && $userMatrixPermissions['uang-jalan']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[uang-jalan][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['uang-jalan']['print']) && $userMatrixPermissions['uang-jalan']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[uang-jalan][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['uang-jalan']['export']) && $userMatrixPermissions['uang-jalan']['export']) checked @endif></td>
                            </tr>

                            {{-- Pranota Uang Jalan --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pranota Uang Jalan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-uang-jalan']['view']) && $userMatrixPermissions['pranota-uang-jalan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-uang-jalan']['create']) && $userMatrixPermissions['pranota-uang-jalan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-uang-jalan']['update']) && $userMatrixPermissions['pranota-uang-jalan']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-uang-jalan']['delete']) && $userMatrixPermissions['pranota-uang-jalan']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-uang-jalan']['approve']) && $userMatrixPermissions['pranota-uang-jalan']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-uang-jalan']['print']) && $userMatrixPermissions['pranota-uang-jalan']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-uang-jalan']['export']) && $userMatrixPermissions['pranota-uang-jalan']['export']) checked @endif></td>
                            </tr>

                            {{-- Pranota Uang Jalan Bongkaran --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pranota Uang Jalan Bongkaran</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan-bongkaran][view]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-jalan-bongkaran.view') || (isset($userMatrixPermissions['pranota-uang-jalan-bongkaran']['view']) && $userMatrixPermissions['pranota-uang-jalan-bongkaran']['view']) || ($user && $user->can('pranota-uang-jalan-bongkaran-view'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan-bongkaran][create]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-jalan-bongkaran.create') || (isset($userMatrixPermissions['pranota-uang-jalan-bongkaran']['create']) && $userMatrixPermissions['pranota-uang-jalan-bongkaran']['create']) || ($user && $user->can('pranota-uang-jalan-bongkaran-create'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan-bongkaran][update]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-jalan-bongkaran.update') || (isset($userMatrixPermissions['pranota-uang-jalan-bongkaran']['update']) && $userMatrixPermissions['pranota-uang-jalan-bongkaran']['update']) || ($user && $user->can('pranota-uang-jalan-bongkaran-update'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan-bongkaran][delete]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-jalan-bongkaran.delete') || (isset($userMatrixPermissions['pranota-uang-jalan-bongkaran']['delete']) && $userMatrixPermissions['pranota-uang-jalan-bongkaran']['delete']) || ($user && $user->can('pranota-uang-jalan-bongkaran-delete'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan-bongkaran][approve]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-jalan-bongkaran.approve') || (isset($userMatrixPermissions['pranota-uang-jalan-bongkaran']['approve']) && $userMatrixPermissions['pranota-uang-jalan-bongkaran']['approve']) || ($user && $user->can('pranota-uang-jalan-bongkaran-approve'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan-bongkaran][print]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-jalan-bongkaran.print') || (isset($userMatrixPermissions['pranota-uang-jalan-bongkaran']['print']) && $userMatrixPermissions['pranota-uang-jalan-bongkaran']['print']) || ($user && $user->can('pranota-uang-jalan-bongkaran-print'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-jalan-bongkaran][export]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-jalan-bongkaran.export') || (isset($userMatrixPermissions['pranota-uang-jalan-bongkaran']['export']) && $userMatrixPermissions['pranota-uang-jalan-bongkaran']['export']) || ($user && $user->can('pranota-uang-jalan-bongkaran-export'))) checked @endif></td>
                            </tr>

                            {{-- Tanda Terima Bongkaran --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Tanda Terima Bongkaran</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[tanda-terima-bongkaran][view]" value="1" class="permission-checkbox" @if(old('permissions.tanda-terima-bongkaran.view') || (isset($userMatrixPermissions['tanda-terima-bongkaran']['view']) && $userMatrixPermissions['tanda-terima-bongkaran']['view']) || ($user && $user->can('tanda-terima-bongkaran-view'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tanda-terima-bongkaran][create]" value="1" class="permission-checkbox" @if(old('permissions.tanda-terima-bongkaran.create') || (isset($userMatrixPermissions['tanda-terima-bongkaran']['create']) && $userMatrixPermissions['tanda-terima-bongkaran']['create']) || ($user && $user->can('tanda-terima-bongkaran-create'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tanda-terima-bongkaran][update]" value="1" class="permission-checkbox" @if(old('permissions.tanda-terima-bongkaran.update') || (isset($userMatrixPermissions['tanda-terima-bongkaran']['update']) && $userMatrixPermissions['tanda-terima-bongkaran']['update']) || ($user && $user->can('tanda-terima-bongkaran-update'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tanda-terima-bongkaran][delete]" value="1" class="permission-checkbox" @if(old('permissions.tanda-terima-bongkaran.delete') || (isset($userMatrixPermissions['tanda-terima-bongkaran']['delete']) && $userMatrixPermissions['tanda-terima-bongkaran']['delete']) || ($user && $user->can('tanda-terima-bongkaran-delete'))) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[tanda-terima-bongkaran][print]" value="1" class="permission-checkbox" @if(old('permissions.tanda-terima-bongkaran.print') || (isset($userMatrixPermissions['tanda-terima-bongkaran']['print']) && $userMatrixPermissions['tanda-terima-bongkaran']['print']) || ($user && $user->can('tanda-terima-bongkaran-print'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tanda-terima-bongkaran][export]" value="1" class="permission-checkbox" @if(old('permissions.tanda-terima-bongkaran.export') || (isset($userMatrixPermissions['tanda-terima-bongkaran']['export']) && $userMatrixPermissions['tanda-terima-bongkaran']['export']) || ($user && $user->can('tanda-terima-bongkaran-export'))) checked @endif></td>
                            </tr>

                            {{-- Checkpoint Kontainer Keluar --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Checkpoint Kontainer Keluar</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[checkpoint-kontainer-keluar][view]" value="1" class="permission-checkbox" @if(old('permissions.checkpoint-kontainer-keluar.view') || (isset($userMatrixPermissions['checkpoint-kontainer-keluar']['view']) && $userMatrixPermissions['checkpoint-kontainer-keluar']['view']) || ($user && $user->can('checkpoint-kontainer-keluar-view'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[checkpoint-kontainer-keluar][create]" value="1" class="permission-checkbox" @if(old('permissions.checkpoint-kontainer-keluar.create') || (isset($userMatrixPermissions['checkpoint-kontainer-keluar']['create']) && $userMatrixPermissions['checkpoint-kontainer-keluar']['create']) || ($user && $user->can('checkpoint-kontainer-keluar-create'))) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[checkpoint-kontainer-keluar][delete]" value="1" class="permission-checkbox" @if(old('permissions.checkpoint-kontainer-keluar.delete') || (isset($userMatrixPermissions['checkpoint-kontainer-keluar']['delete']) && $userMatrixPermissions['checkpoint-kontainer-keluar']['delete']) || ($user && $user->can('checkpoint-kontainer-keluar-delete'))) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Checkpoint Kontainer Masuk --}}
                            <tr class="submodule-row" data-parent="operational">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Checkpoint Kontainer Masuk</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[checkpoint-kontainer-masuk][view]" value="1" class="permission-checkbox" @if(old('permissions.checkpoint-kontainer-masuk.view') || (isset($userMatrixPermissions['checkpoint-kontainer-masuk']['view']) && $userMatrixPermissions['checkpoint-kontainer-masuk']['view']) || ($user && $user->can('checkpoint-kontainer-masuk-view'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[checkpoint-kontainer-masuk][create]" value="1" class="permission-checkbox" @if(old('permissions.checkpoint-kontainer-masuk.create') || (isset($userMatrixPermissions['checkpoint-kontainer-masuk']['create']) && $userMatrixPermissions['checkpoint-kontainer-masuk']['create']) || ($user && $user->can('checkpoint-kontainer-masuk-create'))) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[checkpoint-kontainer-masuk][delete]" value="1" class="permission-checkbox" @if(old('permissions.checkpoint-kontainer-masuk.delete') || (isset($userMatrixPermissions['checkpoint-kontainer-masuk']['delete']) && $userMatrixPermissions['checkpoint-kontainer-masuk']['delete']) || ($user && $user->can('checkpoint-kontainer-masuk-delete'))) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>


                            {{-- User --}}
                            <tr class="module-row" data-module="user">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">User</div>
                                            <div class="text-xs text-gray-500">Modul pengelolaan user dan karyawan</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="user-header-checkbox permission-checkbox" data-permission="view">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="user-header-checkbox permission-checkbox" data-permission="create">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="user-header-checkbox permission-checkbox" data-permission="update">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="user-header-checkbox permission-checkbox" data-permission="delete">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="user-header-checkbox permission-checkbox" data-permission="approve">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="user-header-checkbox permission-checkbox" data-permission="print">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="user-header-checkbox permission-checkbox" data-permission="export">
                                </td>
                            </tr>

                            {{-- User Sub-modules --}}
                            {{-- Data User --}}
                            <tr class="submodule-row" data-parent="user">
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

                            {{-- Data Karyawan --}}
                            <tr class="submodule-row" data-parent="user">
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

                            {{-- Data Divisi --}}
                            <tr class="submodule-row" data-parent="user">
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
                            <tr class="submodule-row" data-parent="user">
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

                            {{-- Data Pajak --}}
                            <tr class="submodule-row" data-parent="user">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Pajak</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-pajak][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pajak']['view']) && $userMatrixPermissions['master-pajak']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pajak][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pajak']['create']) && $userMatrixPermissions['master-pajak']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pajak][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pajak']['update']) && $userMatrixPermissions['master-pajak']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pajak][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pajak']['delete']) && $userMatrixPermissions['master-pajak']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Bank --}}
                            <tr class="submodule-row" data-parent="user">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Bank</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-bank][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-bank']['view']) && $userMatrixPermissions['master-bank']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-bank][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-bank']['create']) && $userMatrixPermissions['master-bank']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-bank][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-bank']['update']) && $userMatrixPermissions['master-bank']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-bank][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-bank']['delete']) && $userMatrixPermissions['master-bank']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Persetujuan User --}}
                            <tr class="submodule-row" data-parent="user">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Persetujuan User</span>
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

                            {{-- Aktiva --}}
                            <tr class="module-row" data-module="aktiva">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">Aktiva</div>
                                            <div class="text-xs text-gray-500">Modul pengelolaan aktiva perusahaan</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktiva-header-checkbox permission-checkbox" data-permission="view">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktiva-header-checkbox permission-checkbox" data-permission="create">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktiva-header-checkbox permission-checkbox" data-permission="update">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktiva-header-checkbox permission-checkbox" data-permission="delete">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktiva-header-checkbox permission-checkbox" data-permission="approve">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktiva-header-checkbox permission-checkbox" data-permission="print">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktiva-header-checkbox permission-checkbox" data-permission="export">
                                </td>
                            </tr>

                            {{-- Aktiva Sub-modules --}}
                            {{-- Data Kontainer --}}
                            <tr class="submodule-row" data-parent="aktiva">
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

                            {{-- Data Kapal --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Kapal</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-kapal][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kapal']['view']) && $userMatrixPermissions['master-kapal']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kapal][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kapal']['create']) && $userMatrixPermissions['master-kapal']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kapal][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kapal']['update']) && $userMatrixPermissions['master-kapal']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kapal][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kapal']['delete']) && $userMatrixPermissions['master-kapal']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[master-kapal][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kapal']['print']) && $userMatrixPermissions['master-kapal']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-kapal][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-kapal']['export']) && $userMatrixPermissions['master-kapal']['export']) checked @endif></td>
                            </tr>

                            {{-- Biaya Kapal --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Biaya Kapal</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[biaya-kapal][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['biaya-kapal']['view']) && $userMatrixPermissions['biaya-kapal']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[biaya-kapal][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['biaya-kapal']['create']) && $userMatrixPermissions['biaya-kapal']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[biaya-kapal][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['biaya-kapal']['update']) && $userMatrixPermissions['biaya-kapal']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[biaya-kapal][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['biaya-kapal']['delete']) && $userMatrixPermissions['biaya-kapal']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[biaya-kapal][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['biaya-kapal']['print']) && $userMatrixPermissions['biaya-kapal']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[biaya-kapal][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['biaya-kapal']['export']) && $userMatrixPermissions['biaya-kapal']['export']) checked @endif></td>
                            </tr>

                            {{-- Pricelist Sewa Kontainer --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pricelist Sewa Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-pricelist-sewa-kontainer][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-sewa-kontainer']['view']) && $userMatrixPermissions['master-pricelist-sewa-kontainer']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-sewa-kontainer][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-sewa-kontainer']['create']) && $userMatrixPermissions['master-pricelist-sewa-kontainer']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-sewa-kontainer][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-sewa-kontainer']['update']) && $userMatrixPermissions['master-pricelist-sewa-kontainer']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-sewa-kontainer][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-sewa-kontainer']['delete']) && $userMatrixPermissions['master-pricelist-sewa-kontainer']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Pricelist CAT --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pricelist CAT</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-pricelist-cat][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-cat']['view']) && $userMatrixPermissions['master-pricelist-cat']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-cat][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-cat']['create']) && $userMatrixPermissions['master-pricelist-cat']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-cat][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-cat']['update']) && $userMatrixPermissions['master-pricelist-cat']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-cat][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-cat']['delete']) && $userMatrixPermissions['master-pricelist-cat']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Pricelist Uang Jalan Batam --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pricelist Uang Jalan Batam</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-pricelist-uang-jalan-batam][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-uang-jalan-batam']['view']) && $userMatrixPermissions['master-pricelist-uang-jalan-batam']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-uang-jalan-batam][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-uang-jalan-batam']['create']) && $userMatrixPermissions['master-pricelist-uang-jalan-batam']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-uang-jalan-batam][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-uang-jalan-batam']['update']) && $userMatrixPermissions['master-pricelist-uang-jalan-batam']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-uang-jalan-batam][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-uang-jalan-batam']['delete']) && $userMatrixPermissions['master-pricelist-uang-jalan-batam']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Pricelist OB --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pricelist OB</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-pricelist-ob][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-ob']['view']) && $userMatrixPermissions['master-pricelist-ob']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-ob][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-ob']['create']) && $userMatrixPermissions['master-pricelist-ob']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-ob][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-ob']['update']) && $userMatrixPermissions['master-pricelist-ob']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-ob][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-ob']['delete']) && $userMatrixPermissions['master-pricelist-ob']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Pricelist Rit --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pricelist Rit</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-pricelist-rit][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-rit']['view']) && $userMatrixPermissions['master-pricelist-rit']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-rit][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-rit']['create']) && $userMatrixPermissions['master-pricelist-rit']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-rit][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-rit']['update']) && $userMatrixPermissions['master-pricelist-rit']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-rit][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-pricelist-rit']['delete']) && $userMatrixPermissions['master-pricelist-rit']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Vendor/Bengkel --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Data Vendor/Bengkel</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-vendor-bengkel][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-vendor-bengkel']['view']) && $userMatrixPermissions['master-vendor-bengkel']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-vendor-bengkel][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-vendor-bengkel']['create']) && $userMatrixPermissions['master-vendor-bengkel']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-vendor-bengkel][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-vendor-bengkel']['update']) && $userMatrixPermissions['master-vendor-bengkel']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-vendor-bengkel][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-vendor-bengkel']['delete']) && $userMatrixPermissions['master-vendor-bengkel']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Vendor Kontainer Sewa --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Vendor Kontainer Sewa</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[vendor-kontainer-sewa][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['vendor-kontainer-sewa']['view']) && $userMatrixPermissions['vendor-kontainer-sewa']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[vendor-kontainer-sewa][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['vendor-kontainer-sewa']['create']) && $userMatrixPermissions['vendor-kontainer-sewa']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[vendor-kontainer-sewa][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['vendor-kontainer-sewa']['update']) && $userMatrixPermissions['vendor-kontainer-sewa']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[vendor-kontainer-sewa][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['vendor-kontainer-sewa']['delete']) && $userMatrixPermissions['vendor-kontainer-sewa']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[vendor-kontainer-sewa][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['vendor-kontainer-sewa']['print']) && $userMatrixPermissions['vendor-kontainer-sewa']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[vendor-kontainer-sewa][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['vendor-kontainer-sewa']['export']) && $userMatrixPermissions['vendor-kontainer-sewa']['export']) checked @endif></td>
                            </tr>

                            {{-- Data Stock Kontainer --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Stock Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-stock-kontainer][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-stock-kontainer']['view']) && $userMatrixPermissions['master-stock-kontainer']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-stock-kontainer][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-stock-kontainer']['create']) && $userMatrixPermissions['master-stock-kontainer']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-stock-kontainer][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-stock-kontainer']['update']) && $userMatrixPermissions['master-stock-kontainer']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-stock-kontainer][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-stock-kontainer']['delete']) && $userMatrixPermissions['master-stock-kontainer']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Data Mobil --}}
                            <tr class="submodule-row" data-parent="aktiva">
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

                            {{-- Pergerakan Kapal --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pergerakan Kapal</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pergerakan-kapal][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kapal']['view']) && $userMatrixPermissions['pergerakan-kapal']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pergerakan-kapal][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kapal']['create']) && $userMatrixPermissions['pergerakan-kapal']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pergerakan-kapal][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kapal']['update']) && $userMatrixPermissions['pergerakan-kapal']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pergerakan-kapal][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kapal']['delete']) && $userMatrixPermissions['pergerakan-kapal']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pergerakan-kapal][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kapal']['approve']) && $userMatrixPermissions['pergerakan-kapal']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pergerakan-kapal][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kapal']['print']) && $userMatrixPermissions['pergerakan-kapal']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pergerakan-kapal][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kapal']['export']) && $userMatrixPermissions['pergerakan-kapal']['export']) checked @endif></td>
                            </tr>

                            {{-- Pergerakan Kontainer --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pergerakan Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pergerakan-kontainer][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kontainer']['view']) && $userMatrixPermissions['pergerakan-kontainer']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pergerakan-kontainer][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kontainer']['create']) && $userMatrixPermissions['pergerakan-kontainer']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pergerakan-kontainer][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kontainer']['update']) && $userMatrixPermissions['pergerakan-kontainer']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pergerakan-kontainer][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kontainer']['delete']) && $userMatrixPermissions['pergerakan-kontainer']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pergerakan-kontainer][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kontainer']['approve']) && $userMatrixPermissions['pergerakan-kontainer']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pergerakan-kontainer][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kontainer']['print']) && $userMatrixPermissions['pergerakan-kontainer']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pergerakan-kontainer][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pergerakan-kontainer']['export']) && $userMatrixPermissions['pergerakan-kontainer']['export']) checked @endif></td>
                            </tr>

                            {{-- Master Gudang --}}
                            <tr class="submodule-row" data-parent="aktiva">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Master Gudang</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-gudang][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-gudang']['view']) && $userMatrixPermissions['master-gudang']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-gudang][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-gudang']['create']) && $userMatrixPermissions['master-gudang']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-gudang][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-gudang']['update']) && $userMatrixPermissions['master-gudang']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-gudang][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-gudang']['delete']) && $userMatrixPermissions['master-gudang']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[master-gudang][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-gudang']['print']) && $userMatrixPermissions['master-gudang']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[master-gudang][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['master-gudang']['export']) && $userMatrixPermissions['master-gudang']['export']) checked @endif></td>
                            </tr>

                            {{-- Aktivitas --}}
                            <tr class="module-row" data-module="aktivitas">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">Aktivitas</div>
                                            <div class="text-xs text-gray-500">Modul pengelolaan aktivitas operasional</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-header-checkbox permission-checkbox" data-permission="view">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-header-checkbox permission-checkbox" data-permission="create">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-header-checkbox permission-checkbox" data-permission="update">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-header-checkbox permission-checkbox" data-permission="delete">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-header-checkbox permission-checkbox" data-permission="approve">
                                </td>
                                
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-header-checkbox permission-checkbox" data-permission="print">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-header-checkbox permission-checkbox" data-permission="export">
                                </td>
                            </tr>

                            {{-- Aktivitas Sub-modules --}}
                            {{-- Permohonan Memo --}}
                            <tr class="submodule-row" data-parent="aktivitas">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Permohonan Memo</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['view']) && $userMatrixPermissions['permohonan-memo']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['create']) && $userMatrixPermissions['permohonan-memo']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['update']) && $userMatrixPermissions['permohonan-memo']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['delete']) && $userMatrixPermissions['permohonan-memo']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['approve']) && $userMatrixPermissions['permohonan-memo']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['print']) && $userMatrixPermissions['permohonan-memo']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[permohonan-memo][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['permohonan-memo']['export']) && $userMatrixPermissions['permohonan-memo']['export']) checked @endif></td>
                            </tr>

                            {{-- Pranota Uang Rit --}}
                            <tr class="submodule-row" data-parent="aktivitas">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <div>
                                            <span>Pranota Uang Rit</span>
                                            <div class="text-xs text-gray-500 mt-1">Hak khusus: 
                                                <label class="inline-flex items-center ml-2">
                                                    <input type="checkbox" name="permissions[pranota-uang-rit][mark-paid]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-uang-rit']['mark-paid']) && $userMatrixPermissions['pranota-uang-rit']['mark-paid']) checked @endif>
                                                    <span class="ml-2 text-xs text-gray-600">Mark Paid</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-uang-rit][view]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-rit.view') || (isset($userMatrixPermissions['pranota-uang-rit']['view']) && $userMatrixPermissions['pranota-uang-rit']['view']) || ($user && $user->can('pranota-uang-rit-view'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-rit][create]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-rit.create') || (isset($userMatrixPermissions['pranota-uang-rit']['create']) && $userMatrixPermissions['pranota-uang-rit']['create']) || ($user && $user->can('pranota-uang-rit-create'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-rit][update]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-rit.update') || (isset($userMatrixPermissions['pranota-uang-rit']['update']) && $userMatrixPermissions['pranota-uang-rit']['update']) || ($user && $user->can('pranota-uang-rit-update'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-rit][delete]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-rit.delete') || (isset($userMatrixPermissions['pranota-uang-rit']['delete']) && $userMatrixPermissions['pranota-uang-rit']['delete']) || ($user && $user->can('pranota-uang-rit-delete'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-rit][approve]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-rit.approve') || (isset($userMatrixPermissions['pranota-uang-rit']['approve']) && $userMatrixPermissions['pranota-uang-rit']['approve']) || ($user && $user->can('pranota-uang-rit-approve'))) checked @endif></td>
                                
                                <td><input type="checkbox" name="permissions[pranota-uang-rit][print]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-rit.print') || (isset($userMatrixPermissions['pranota-uang-rit']['print']) && $userMatrixPermissions['pranota-uang-rit']['print']) || ($user && $user->can('pranota-uang-rit-print'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-uang-rit][export]" value="1" class="permission-checkbox" @if(old('permissions.pranota-uang-rit.export') || (isset($userMatrixPermissions['pranota-uang-rit']['export']) && $userMatrixPermissions['pranota-uang-rit']['export']) || ($user && $user->can('pranota-uang-rit-export'))) checked @endif></td>
                            </tr>

                            {{-- Pranota Supir --}}
                            <tr class="submodule-row" data-parent="aktivitas">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pranota Supir</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-supir][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-supir']['view']) && $userMatrixPermissions['pranota-supir']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-supir][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-supir']['create']) && $userMatrixPermissions['pranota-supir']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-supir][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-supir']['update']) && $userMatrixPermissions['pranota-supir']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-supir][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-supir']['delete']) && $userMatrixPermissions['pranota-supir']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-supir][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-supir']['approve']) && $userMatrixPermissions['pranota-supir']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-supir][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-supir']['print']) && $userMatrixPermissions['pranota-supir']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-supir][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-supir']['export']) && $userMatrixPermissions['pranota-supir']['export']) checked @endif></td>
                            </tr>

                            {{-- Prospek --}}
                            <tr class="submodule-row" data-parent="aktivitas">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Prospek</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[prospek][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['prospek']['view']) && $userMatrixPermissions['prospek']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[prospek][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['prospek']['create']) && $userMatrixPermissions['prospek']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[prospek][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['prospek']['update']) && $userMatrixPermissions['prospek']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[prospek][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['prospek']['delete']) && $userMatrixPermissions['prospek']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Daftar Tagihan Kontainer Sewa --}}
                            <tr class="submodule-row" data-parent="aktivitas">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Daftar Tagihan Kontainer Sewa</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer-sewa][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer-sewa']['view']) && $userMatrixPermissions['tagihan-kontainer-sewa']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer-sewa][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer-sewa']['create']) && $userMatrixPermissions['tagihan-kontainer-sewa']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer-sewa][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer-sewa']['update']) && $userMatrixPermissions['tagihan-kontainer-sewa']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer-sewa][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer-sewa']['delete']) && $userMatrixPermissions['tagihan-kontainer-sewa']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer-sewa][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer-sewa']['approve']) && $userMatrixPermissions['tagihan-kontainer-sewa']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer-sewa][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer-sewa']['print']) && $userMatrixPermissions['tagihan-kontainer-sewa']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer-sewa][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-kontainer-sewa']['export']) && $userMatrixPermissions['tagihan-kontainer-sewa']['export']) checked @endif></td>
                            </tr>

                            {{-- Daftar Pranota Kontainer Sewa --}}
                            <tr class="submodule-row" data-parent="aktivitas">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Daftar Pranota Kontainer Sewa</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-kontainer-sewa][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-kontainer-sewa']['view']) && $userMatrixPermissions['pranota-kontainer-sewa']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-kontainer-sewa][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-kontainer-sewa']['create']) && $userMatrixPermissions['pranota-kontainer-sewa']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-kontainer-sewa][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-kontainer-sewa']['update']) && $userMatrixPermissions['pranota-kontainer-sewa']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-kontainer-sewa][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-kontainer-sewa']['delete']) && $userMatrixPermissions['pranota-kontainer-sewa']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-kontainer-sewa][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-kontainer-sewa']['approve']) && $userMatrixPermissions['pranota-kontainer-sewa']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-kontainer-sewa][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-kontainer-sewa']['print']) && $userMatrixPermissions['pranota-kontainer-sewa']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-kontainer-sewa][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-kontainer-sewa']['export']) && $userMatrixPermissions['pranota-kontainer-sewa']['export']) checked @endif></td>
                            </tr>

                            {{-- Daftar Tagihan Perbaikan Kontainer --}}
                            <tr class="submodule-row" data-parent="aktivitas">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Daftar Tagihan Perbaikan Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[tagihan-perbaikan-kontainer][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-perbaikan-kontainer']['view']) && $userMatrixPermissions['tagihan-perbaikan-kontainer']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-perbaikan-kontainer][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-perbaikan-kontainer']['create']) && $userMatrixPermissions['tagihan-perbaikan-kontainer']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-perbaikan-kontainer][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-perbaikan-kontainer']['update']) && $userMatrixPermissions['tagihan-perbaikan-kontainer']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-perbaikan-kontainer][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-perbaikan-kontainer']['delete']) && $userMatrixPermissions['tagihan-perbaikan-kontainer']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-perbaikan-kontainer][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-perbaikan-kontainer']['approve']) && $userMatrixPermissions['tagihan-perbaikan-kontainer']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-perbaikan-kontainer][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-perbaikan-kontainer']['print']) && $userMatrixPermissions['tagihan-perbaikan-kontainer']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-perbaikan-kontainer][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-perbaikan-kontainer']['export']) && $userMatrixPermissions['tagihan-perbaikan-kontainer']['export']) checked @endif></td>
                            </tr>

                            {{-- Daftar Pranota Perbaikan Kontainer --}}
                            <tr class="submodule-row" data-parent="aktivitas">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Daftar Pranota Perbaikan Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['view']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['create']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['update']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['delete']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['approve']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['print']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-perbaikan-kontainer][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-perbaikan-kontainer']['export']) && $userMatrixPermissions['pranota-perbaikan-kontainer']['export']) checked @endif></td>
                            </tr>

                            {{-- Daftar Tagihan CAT --}}
                            <tr class="submodule-row" data-parent="aktivitas">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Daftar Tagihan CAT</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[tagihan-cat][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-cat']['view']) && $userMatrixPermissions['tagihan-cat']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-cat][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-cat']['create']) && $userMatrixPermissions['tagihan-cat']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-cat][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-cat']['update']) && $userMatrixPermissions['tagihan-cat']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-cat][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-cat']['delete']) && $userMatrixPermissions['tagihan-cat']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-cat][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-cat']['approve']) && $userMatrixPermissions['tagihan-cat']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-cat][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-cat']['print']) && $userMatrixPermissions['tagihan-cat']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[tagihan-cat][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['tagihan-cat']['export']) && $userMatrixPermissions['tagihan-cat']['export']) checked @endif></td>
                            </tr>

                            {{-- Daftar Pranota CAT --}}
                            <tr class="submodule-row" data-parent="aktivitas">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Daftar Pranota CAT</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-cat][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-cat']['view']) && $userMatrixPermissions['pranota-cat']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-cat][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-cat']['create']) && $userMatrixPermissions['pranota-cat']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-cat][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-cat']['update']) && $userMatrixPermissions['pranota-cat']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-cat][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-cat']['delete']) && $userMatrixPermissions['pranota-cat']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-cat][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-cat']['approve']) && $userMatrixPermissions['pranota-cat']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-cat][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-cat']['print']) && $userMatrixPermissions['pranota-cat']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-cat][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-cat']['export']) && $userMatrixPermissions['pranota-cat']['export']) checked @endif></td>
                            </tr>

                            {{-- Pranota Rit --}}
                            <tr class="submodule-row" data-parent="aktivitas">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pranota Rit</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-rit][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit']['view']) && $userMatrixPermissions['pranota-rit']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-rit][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit']['create']) && $userMatrixPermissions['pranota-rit']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-rit][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit']['update']) && $userMatrixPermissions['pranota-rit']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-rit][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit']['delete']) && $userMatrixPermissions['pranota-rit']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-rit][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit']['approve']) && $userMatrixPermissions['pranota-rit']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-rit][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit']['print']) && $userMatrixPermissions['pranota-rit']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-rit][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit']['export']) && $userMatrixPermissions['pranota-rit']['export']) checked @endif></td>
                            </tr>

                            {{-- Pranota Rit Kenek --}}
                                                        {{-- Pranota OB --}}
                                                        <tr class="submodule-row" data-parent="aktivitas">
                                                            <td class="submodule">
                                                                <div class="flex items-center justify-between">
                                                                    <div>
                                                                        <span class="module-icon">📄</span>
                                                                        <span class="font-medium">Pranota OB</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><input type="checkbox" name="permissions[pranota-ob][view]" value="1" class="permission-checkbox" @if(old('permissions.pranota-ob.view') || (isset($userMatrixPermissions['pranota-ob']['view']) && $userMatrixPermissions['pranota-ob']['view']) || ($user && $user->can('pranota-ob-view'))) checked @endif></td>
                                                            <td><input type="checkbox" name="permissions[pranota-ob][create]" value="1" class="permission-checkbox" @if(old('permissions.pranota-ob.create') || (isset($userMatrixPermissions['pranota-ob']['create']) && $userMatrixPermissions['pranota-ob']['create']) || ($user && $user->can('pranota-ob-create'))) checked @endif></td>
                                                            <td><input type="checkbox" name="permissions[pranota-ob][update]" value="1" class="permission-checkbox" @if(old('permissions.pranota-ob.update') || (isset($userMatrixPermissions['pranota-ob']['update']) && $userMatrixPermissions['pranota-ob']['update']) || ($user && $user->can('pranota-ob-update'))) checked @endif></td>
                                                            <td><input type="checkbox" name="permissions[pranota-ob][delete]" value="1" class="permission-checkbox" @if(old('permissions.pranota-ob.delete') || (isset($userMatrixPermissions['pranota-ob']['delete']) && $userMatrixPermissions['pranota-ob']['delete']) || ($user && $user->can('pranota-ob-delete'))) checked @endif></td>
                                                            <td class="empty-cell"></td>
                                                            <td><input type="checkbox" name="permissions[pranota-ob][print]" value="1" class="permission-checkbox" @if(old('permissions.pranota-ob.print') || (isset($userMatrixPermissions['pranota-ob']['print']) && $userMatrixPermissions['pranota-ob']['print']) || ($user && $user->can('pranota-ob-print'))) checked @endif></td>
                                                            <td><input type="checkbox" name="permissions[pranota-ob][export]" value="1" class="permission-checkbox" @if(old('permissions.pranota-ob.export') || (isset($userMatrixPermissions['pranota-ob']['export']) && $userMatrixPermissions['pranota-ob']['export']) || ($user && $user->can('pranota-ob-export'))) checked @endif></td>
                                                        </tr>

                            <tr class="submodule-row" data-parent="aktivitas">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pranota Rit Kenek</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-rit-kenek][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit-kenek']['view']) && $userMatrixPermissions['pranota-rit-kenek']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-rit-kenek][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit-kenek']['create']) && $userMatrixPermissions['pranota-rit-kenek']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-rit-kenek][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit-kenek']['update']) && $userMatrixPermissions['pranota-rit-kenek']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-rit-kenek][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit-kenek']['delete']) && $userMatrixPermissions['pranota-rit-kenek']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-rit-kenek][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit-kenek']['approve']) && $userMatrixPermissions['pranota-rit-kenek']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-rit-kenek][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit-kenek']['print']) && $userMatrixPermissions['pranota-rit-kenek']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pranota-rit-kenek][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pranota-rit-kenek']['export']) && $userMatrixPermissions['pranota-rit-kenek']['export']) checked @endif></td>
                            </tr>

                            {{-- Pembayaran --}}
                            <tr class="module-row" data-module="pembayaran">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">Pembayaran</div>
                                            <div class="text-xs text-gray-500">Modul pengelolaan pembayaran pranota dan tagihan</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="view">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="create">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="update">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="delete">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="approve">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="print">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="export">
                                </td>
                            </tr>

                            {{-- Pembayaran Sub-modules --}}
                            {{-- Pembayaran Pranota Supir --}}
                            <tr class="submodule-row" data-parent="pembayaran">
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

                            {{-- Pembayaran Pranota Kontainer --}}
                            <tr class="submodule-row" data-parent="pembayaran">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pembayaran Pranota Kontainer Sewa</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['view']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['create']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['update']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['delete']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['approve']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['print']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-kontainer']['export']) && $userMatrixPermissions['pembayaran-pranota-kontainer']['export']) checked @endif></td>
                            </tr>

                            {{-- Pembayaran Pranota Perbaikan Kontainer --}}
                            <tr class="submodule-row" data-parent="pembayaran">
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
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-perbaikan-kontainer][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['approve']) && $userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-perbaikan-kontainer][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['print']) && $userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-perbaikan-kontainer][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['export']) && $userMatrixPermissions['pembayaran-pranota-perbaikan-kontainer']['export']) checked @endif></td>
                            </tr>

                            {{-- Pembayaran Pranota CAT --}}
                            <tr class="submodule-row" data-parent="pembayaran">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pembayaran Pranota CAT</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-cat][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-cat']['view']) && $userMatrixPermissions['pembayaran-pranota-cat']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-cat][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-cat']['create']) && $userMatrixPermissions['pembayaran-pranota-cat']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-cat][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-cat']['update']) && $userMatrixPermissions['pembayaran-pranota-cat']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-cat][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-cat']['delete']) && $userMatrixPermissions['pembayaran-pranota-cat']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-cat][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-cat']['approve']) && $userMatrixPermissions['pembayaran-pranota-cat']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-cat][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-cat']['print']) && $userMatrixPermissions['pembayaran-pranota-cat']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-cat][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-cat']['export']) && $userMatrixPermissions['pembayaran-pranota-cat']['export']) checked @endif></td>
                            </tr>

                            {{-- Pembayaran Pranota OB --}}
                            <tr class="submodule-row" data-parent="pembayaran">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pembayaran Pranota OB</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-ob][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-ob']['view']) && $userMatrixPermissions['pembayaran-pranota-ob']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-ob][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-ob']['create']) && $userMatrixPermissions['pembayaran-pranota-ob']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-ob][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-ob']['update']) && $userMatrixPermissions['pembayaran-pranota-ob']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-ob][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-ob']['delete']) && $userMatrixPermissions['pembayaran-pranota-ob']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-ob][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-ob']['approve']) && $userMatrixPermissions['pembayaran-pranota-ob']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-ob][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-ob']['print']) && $userMatrixPermissions['pembayaran-pranota-ob']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-ob][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-ob']['export']) && $userMatrixPermissions['pembayaran-pranota-ob']['export']) checked @endif></td>
                            </tr>

                            {{-- Pembayaran Pranota Surat Jalan --}}
                            <tr class="submodule-row" data-parent="pembayaran">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pembayaran Pranota Surat Jalan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-surat-jalan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-surat-jalan']['view']) && $userMatrixPermissions['pembayaran-pranota-surat-jalan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-surat-jalan][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-surat-jalan']['create']) && $userMatrixPermissions['pembayaran-pranota-surat-jalan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-surat-jalan][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-surat-jalan']['update']) && $userMatrixPermissions['pembayaran-pranota-surat-jalan']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-surat-jalan][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-surat-jalan']['delete']) && $userMatrixPermissions['pembayaran-pranota-surat-jalan']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-surat-jalan][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-surat-jalan']['approve']) && $userMatrixPermissions['pembayaran-pranota-surat-jalan']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-surat-jalan][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-surat-jalan']['print']) && $userMatrixPermissions['pembayaran-pranota-surat-jalan']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-surat-jalan][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-surat-jalan']['export']) && $userMatrixPermissions['pembayaran-pranota-surat-jalan']['export']) checked @endif></td>
                            </tr>

                            {{-- Pembayaran Pranota Uang Jalan --}}
                            <tr class="submodule-row" data-parent="pembayaran">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Pembayaran Pranota Uang Jalan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-uang-jalan']['view']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-uang-jalan']['create']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-uang-jalan']['update']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-uang-jalan']['delete']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-uang-jalan']['approve']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-uang-jalan']['print']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-pranota-uang-jalan']['export']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan']['export']) checked @endif></td>
                            </tr>

                            {{-- Pembayaran Pranota Uang Jalan Bongkaran --}}
                            <tr class="submodule-row" data-parent="pembayaran">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Pembayaran Pranota Uang Jalan Bongkaran</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan-bongkaran][view]" value="1" class="permission-checkbox" @if(old('permissions.pembayaran-pranota-uang-jalan-bongkaran.view') || (isset($userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['view']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['view']) || ($user && $user->can('pembayaran-pranota-uang-jalan-bongkaran-view'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan-bongkaran][create]" value="1" class="permission-checkbox" @if(old('permissions.pembayaran-pranota-uang-jalan-bongkaran.create') || (isset($userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['create']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['create']) || ($user && $user->can('pembayaran-pranota-uang-jalan-bongkaran-create'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan-bongkaran][update]" value="1" class="permission-checkbox" @if(old('permissions.pembayaran-pranota-uang-jalan-bongkaran.update') || (isset($userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['update']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['update']) || ($user && $user->can('pembayaran-pranota-uang-jalan-bongkaran-edit'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan-bongkaran][delete]" value="1" class="permission-checkbox" @if(old('permissions.pembayaran-pranota-uang-jalan-bongkaran.delete') || (isset($userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['delete']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['delete']) || ($user && $user->can('pembayaran-pranota-uang-jalan-bongkaran-delete'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan-bongkaran][approve]" value="1" class="permission-checkbox" @if(old('permissions.pembayaran-pranota-uang-jalan-bongkaran.approve') || (isset($userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['approve']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['approve']) || ($user && $user->can('pembayaran-pranota-uang-jalan-bongkaran-approve'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan-bongkaran][print]" value="1" class="permission-checkbox" @if(old('permissions.pembayaran-pranota-uang-jalan-bongkaran.print') || (isset($userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['print']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['print']) || ($user && $user->can('pembayaran-pranota-uang-jalan-bongkaran-print'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-uang-jalan-bongkaran][export]" value="1" class="permission-checkbox" @if(old('permissions.pembayaran-pranota-uang-jalan-bongkaran.export') || (isset($userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['export']) && $userMatrixPermissions['pembayaran-pranota-uang-jalan-bongkaran']['export']) || ($user && $user->can('pembayaran-pranota-uang-jalan-bongkaran-export'))) checked @endif></td>
                            </tr>

                            {{-- Pembayaran Uang Muka --}}
                            <tr class="submodule-row" data-parent="pembayaran">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span>Pembayaran Uang Muka</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-uang-muka][view]" value="1" class="permission-checkbox" @if(old('permissions.pembayaran-uang-muka.view') || (isset($userMatrixPermissions['pembayaran-uang-muka']['view']) && $userMatrixPermissions['pembayaran-uang-muka']['view']) || ($user && $user->can('pembayaran-uang-muka-view'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-uang-muka][create]" value="1" class="permission-checkbox" @if(old('permissions.pembayaran-uang-muka.create') || (isset($userMatrixPermissions['pembayaran-uang-muka']['create']) && $userMatrixPermissions['pembayaran-uang-muka']['create']) || ($user && $user->can('pembayaran-uang-muka-create'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-uang-muka][update]" value="1" class="permission-checkbox" @if(old('permissions.pembayaran-uang-muka.update') || (isset($userMatrixPermissions['pembayaran-uang-muka']['update']) && $userMatrixPermissions['pembayaran-uang-muka']['update']) || ($user && $user->can('pembayaran-uang-muka-edit'))) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-uang-muka][delete]" value="1" class="permission-checkbox" @if(old('permissions.pembayaran-uang-muka.delete') || (isset($userMatrixPermissions['pembayaran-uang-muka']['delete']) && $userMatrixPermissions['pembayaran-uang-muka']['delete']) || ($user && $user->can('pembayaran-uang-muka-delete'))) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Approval System --}}
                            <tr class="module-row" data-module="approval">
                                <td class="module-header">
                                    <span class="expand-icon">▶</span>
                                    <span class="module-icon">✅</span>
                                    Sistem Persetujuan
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="approval-header-checkbox" data-permission="view">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="approval-header-checkbox" data-permission="create">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="approval-header-checkbox" data-permission="update">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="approval-header-checkbox" data-permission="delete">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="approval-header-checkbox" data-permission="approve">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="approval-header-checkbox" data-permission="print">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="approval-header-checkbox" data-permission="export">
                                </td>
                            </tr>

                            {{-- Approval Sub-modules --}}
                            {{-- Approval Tugas 1 --}}
                            <tr class="submodule-row" data-parent="approval">
                                <td class="submodule">
                                    <span class="module-icon">🔐</span>
                                    Approval Tugas 1 (Supervisor/Manager)
                                </td>
                                <td><input type="checkbox" name="permissions[approval-tugas-1][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-tugas-1']['view']) && $userMatrixPermissions['approval-tugas-1']['view']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[approval-tugas-1][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-tugas-1']['approve']) && $userMatrixPermissions['approval-tugas-1']['approve']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Approval Tugas 2 --}}
                            <tr class="submodule-row" data-parent="approval">
                                <td class="submodule">
                                    <span class="module-icon">🔒</span>
                                    Approval Tugas 2 (General Manager)
                                </td>
                                <td><input type="checkbox" name="permissions[approval-tugas-2][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-tugas-2']['view']) && $userMatrixPermissions['approval-tugas-2']['view']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[approval-tugas-2][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-tugas-2']['approve']) && $userMatrixPermissions['approval-tugas-2']['approve']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Approval Surat Jalan --}}
                            <tr class="submodule-row" data-parent="approval">
                                <td class="submodule">
                                    <span class="module-icon">📋</span>
                                    Approval Surat Jalan
                                </td>
                                <td><input type="checkbox" name="permissions[approval-surat-jalan][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-surat-jalan']['view']) && $userMatrixPermissions['approval-surat-jalan']['view']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td><input type="checkbox" name="permissions[approval-surat-jalan][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-surat-jalan']['approve']) && $userMatrixPermissions['approval-surat-jalan']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[approval-surat-jalan][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-surat-jalan']['print']) && $userMatrixPermissions['approval-surat-jalan']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[approval-surat-jalan][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-surat-jalan']['export']) && $userMatrixPermissions['approval-surat-jalan']['export']) checked @endif></td>
                            </tr>

                            {{-- Approval Order --}}
                            <tr class="submodule-row" data-parent="approval">
                                <td class="submodule">
                                    <span class="module-icon">🎯</span>
                                    Approval Order
                                </td>
                                <td><input type="checkbox" name="permissions[approval-order][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-order']['view']) && $userMatrixPermissions['approval-order']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[approval-order][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-order']['create']) && $userMatrixPermissions['approval-order']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[approval-order][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-order']['update']) && $userMatrixPermissions['approval-order']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[approval-order][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-order']['delete']) && $userMatrixPermissions['approval-order']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[approval-order][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-order']['approve']) && $userMatrixPermissions['approval-order']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[approval-order][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-order']['print']) && $userMatrixPermissions['approval-order']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[approval-order][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['approval-order']['export']) && $userMatrixPermissions['approval-order']['export']) checked @endif></td>
                            </tr>

                            {{-- Aktivitas Lain-lain --}}
                            <tr class="module-row" data-module="aktivitas-lainnya">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <span class="module-icon">📋</span>
                                        <div>
                                            <div class="font-semibold">Aktivitas Lain-lain</div>
                                            <div class="text-xs text-gray-500">Pengelolaan aktivitas dan pembayaran lain-lain</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-lainnya-header-checkbox permission-checkbox" data-permission="view">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-lainnya-header-checkbox permission-checkbox" data-permission="create">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-lainnya-header-checkbox permission-checkbox" data-permission="update">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-lainnya-header-checkbox permission-checkbox" data-permission="delete">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-lainnya-header-checkbox permission-checkbox" data-permission="approve">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-lainnya-header-checkbox permission-checkbox" data-permission="print">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="aktivitas-lainnya-header-checkbox permission-checkbox" data-permission="export">
                                </td>
                            </tr>

                            <tr class="submodule-row" data-parent="aktivitas-lainnya">
                                <td class="submodule">
                                    <span class="module-icon">📝</span>
                                    Aktivitas Lain-lain
                                </td>
                                <td><input type="checkbox" name="permissions[aktivitas-lainnya][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['aktivitas-lainnya']['view']) && $userMatrixPermissions['aktivitas-lainnya']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[aktivitas-lainnya][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['aktivitas-lainnya']['create']) && $userMatrixPermissions['aktivitas-lainnya']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[aktivitas-lainnya][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['aktivitas-lainnya']['update']) && $userMatrixPermissions['aktivitas-lainnya']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[aktivitas-lainnya][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['aktivitas-lainnya']['delete']) && $userMatrixPermissions['aktivitas-lainnya']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[aktivitas-lainnya][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['aktivitas-lainnya']['approve']) && $userMatrixPermissions['aktivitas-lainnya']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[aktivitas-lainnya][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['aktivitas-lainnya']['print']) && $userMatrixPermissions['aktivitas-lainnya']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[aktivitas-lainnya][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['aktivitas-lainnya']['export']) && $userMatrixPermissions['aktivitas-lainnya']['export']) checked @endif></td>
                            </tr>

                            {{-- Invoice Aktivitas Lain --}}
                            <tr class="submodule-row" data-parent="aktivitas-lainnya">
                                <td class="submodule">
                                    <span class="module-icon">🧾</span>
                                    Invoice Aktivitas Lain
                                </td>
                                <td><input type="checkbox" name="permissions[invoice-aktivitas-lain][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['invoice-aktivitas-lain']['view']) && $userMatrixPermissions['invoice-aktivitas-lain']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[invoice-aktivitas-lain][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['invoice-aktivitas-lain']['create']) && $userMatrixPermissions['invoice-aktivitas-lain']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[invoice-aktivitas-lain][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['invoice-aktivitas-lain']['update']) && $userMatrixPermissions['invoice-aktivitas-lain']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[invoice-aktivitas-lain][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['invoice-aktivitas-lain']['delete']) && $userMatrixPermissions['invoice-aktivitas-lain']['delete']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            <tr class="submodule-row" data-parent="aktivitas-lainnya">
                                <td class="submodule">
                                    <span class="module-icon">💰</span>
                                    Pembayaran Aktivitas Lain
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-aktivitas-lain][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-aktivitas-lain']['view']) && $userMatrixPermissions['pembayaran-aktivitas-lain']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-aktivitas-lain][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-aktivitas-lain']['create']) && $userMatrixPermissions['pembayaran-aktivitas-lain']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-aktivitas-lain][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-aktivitas-lain']['update']) && $userMatrixPermissions['pembayaran-aktivitas-lain']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-aktivitas-lain][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-aktivitas-lain']['delete']) && $userMatrixPermissions['pembayaran-aktivitas-lain']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-aktivitas-lain][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-aktivitas-lain']['approve']) && $userMatrixPermissions['pembayaran-aktivitas-lain']['approve']) checked @endif></td>
                                <td class="empty-cell"></td>
                                <td class="empty-cell"></td>
                            </tr>

                            {{-- Pembayaran --}}
                            <tr class="module-row" data-module="pembayaran">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold">Pembayaran</div>
                                            <div class="text-xs text-gray-500">Modul pengelolaan pembayaran uang muka dan OB</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="view">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="create">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="update">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="delete">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="approve">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="print">
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="pembayaran-header-checkbox permission-checkbox" data-permission="export">
                                </td>
                            </tr>

                            {{-- Pembayaran Sub-modules --}}
                            <tr class="submodule-row" data-parent="pembayaran">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pembayaran Uang Muka</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-uang-muka][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-uang-muka']['view']) && $userMatrixPermissions['pembayaran-uang-muka']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-uang-muka][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-uang-muka']['create']) && $userMatrixPermissions['pembayaran-uang-muka']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-uang-muka][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-uang-muka']['update']) && $userMatrixPermissions['pembayaran-uang-muka']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-uang-muka][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-uang-muka']['delete']) && $userMatrixPermissions['pembayaran-uang-muka']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-uang-muka][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-uang-muka']['approve']) && $userMatrixPermissions['pembayaran-uang-muka']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-uang-muka][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-uang-muka']['print']) && $userMatrixPermissions['pembayaran-uang-muka']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-uang-muka][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-uang-muka']['export']) && $userMatrixPermissions['pembayaran-uang-muka']['export']) checked @endif></td>
                            </tr>

                            <tr class="submodule-row" data-parent="pembayaran">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Realisasi Uang Muka</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[realisasi-uang-muka][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['realisasi-uang-muka']['view']) && $userMatrixPermissions['realisasi-uang-muka']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[realisasi-uang-muka][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['realisasi-uang-muka']['create']) && $userMatrixPermissions['realisasi-uang-muka']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[realisasi-uang-muka][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['realisasi-uang-muka']['update']) && $userMatrixPermissions['realisasi-uang-muka']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[realisasi-uang-muka][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['realisasi-uang-muka']['delete']) && $userMatrixPermissions['realisasi-uang-muka']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[realisasi-uang-muka][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['realisasi-uang-muka']['approve']) && $userMatrixPermissions['realisasi-uang-muka']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[realisasi-uang-muka][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['realisasi-uang-muka']['print']) && $userMatrixPermissions['realisasi-uang-muka']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[realisasi-uang-muka][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['realisasi-uang-muka']['export']) && $userMatrixPermissions['realisasi-uang-muka']['export']) checked @endif></td>
                            </tr>

                            <tr class="submodule-row" data-parent="pembayaran">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─</span>
                                        <span>Pembayaran OB</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-ob][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-ob']['view']) && $userMatrixPermissions['pembayaran-ob']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-ob][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-ob']['create']) && $userMatrixPermissions['pembayaran-ob']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-ob][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-ob']['update']) && $userMatrixPermissions['pembayaran-ob']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-ob][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-ob']['delete']) && $userMatrixPermissions['pembayaran-ob']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-ob][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-ob']['approve']) && $userMatrixPermissions['pembayaran-ob']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-ob][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-ob']['print']) && $userMatrixPermissions['pembayaran-ob']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[pembayaran-ob][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['pembayaran-ob']['export']) && $userMatrixPermissions['pembayaran-ob']['export']) checked @endif></td>
                            </tr>

                            {{-- Audit Log --}}
                            <tr class="module-row" data-module="audit-log">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <div>
                                            <div class="font-semibold flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Audit Log
                                            </div>
                                            <div class="text-xs text-gray-500">Pengelolaan log aktivitas sistem</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="audit-log-header-checkbox permission-checkbox" data-permission="view">
                                </td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="text-center text-gray-500 text-sm py-3">
                                    <input type="checkbox" class="audit-log-header-checkbox permission-checkbox" data-permission="export">
                                </td>
                            </tr>

                            <tr class="submodule-row" data-parent="audit-log">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <svg class="w-3 h-3 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span>Lihat Log Aktivitas</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[audit-log][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['audit-log']['view']) && $userMatrixPermissions['audit-log']['view']) checked @endif></td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                            </tr>

                            <tr class="submodule-row" data-parent="audit-log">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <svg class="w-3 h-3 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>Export Log ke CSV</span>
                                    </div>
                                </td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td><input type="checkbox" name="permissions[audit-log][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['audit-log']['export']) && $userMatrixPermissions['audit-log']['export']) checked @endif></td>
                            </tr>

                            {{-- BL (Bill of Lading) - Single Row --}}
                            <tr>
                                <td class="text-left font-medium">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>BL (Bill of Lading)</span>
                                    </div>
                                    <div class="text-xs text-gray-500 ml-6">Pengelolaan dokumen Bill of Lading</div>
                                </td>
                                <td><input type="checkbox" name="permissions[bl][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['bl']['view']) && $userMatrixPermissions['bl']['view']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[bl][create]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['bl']['create']) && $userMatrixPermissions['bl']['create']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[bl][update]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['bl']['update']) && $userMatrixPermissions['bl']['update']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[bl][delete]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['bl']['delete']) && $userMatrixPermissions['bl']['delete']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[bl][approve]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['bl']['approve']) && $userMatrixPermissions['bl']['approve']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[bl][print]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['bl']['print']) && $userMatrixPermissions['bl']['print']) checked @endif></td>
                                <td><input type="checkbox" name="permissions[bl][export]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['bl']['export']) && $userMatrixPermissions['bl']['export']) checked @endif></td>
                            </tr>

                            {{-- OB (Ocean Bunker) - Single Row --}}
                            <tr>
                                <td class="text-left font-medium">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                        </svg>
                                        <span>OB (Ocean Bunker)</span>
                                    </div>
                                    <div class="text-xs text-gray-500 ml-6">Pengelolaan Ocean Bunker - pilih kapal dan voyage</div>
                                </td>
                                <td><input type="checkbox" name="permissions[ob][view]" value="1" class="permission-checkbox" @if(isset($userMatrixPermissions['ob']['view']) && $userMatrixPermissions['ob']['view']) checked @endif></td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
                                <td class="empty-cell text-center text-gray-400">-</td>
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

                // Initialize check all aktiva permissions
                initializeCheckAllAktiva();

                // Initialize check all master permissions
                initializeCheckAllMaster();

                // Initialize check all user permissions
                initializeCheckAllUser();
                initializeCheckAllAktivitasLainnya();

                // Initialize check all pembayaran permissions
                initializeCheckAllPembayaran();

                // Initialize check all aktivitas permissions
                initializeCheckAllAktivitas();

                // Initialize check all approval permissions
                initializeCheckAllApproval();

                // Initialize check all operational permissions
                initializeCheckAllOperational();

                // Initialize check all audit log permissions
                initializeCheckAllAuditLog();
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

            function initializeCheckAllAktiva() {
                // Handle header checkbox changes
                document.querySelectorAll('.aktiva-header-checkbox').forEach(function(headerCheckbox) {
                    headerCheckbox.addEventListener('change', function() {
                        const permission = this.dataset.permission;
                        const isChecked = this.checked;

                        // Update all checkboxes for this permission in aktiva sub-modules
                        const aktivaCheckboxes = document.querySelectorAll(`[data-parent="aktiva"] input[name*="[${permission}]"]`);
                        aktivaCheckboxes.forEach(function(checkbox) {
                            checkbox.checked = isChecked;
                        });

                        // Show toast notification
                        if (isChecked) {
                            showToast(`Semua izin ${permission} Aktiva telah dicentang`, 'success');
                        } else {
                            showToast(`Semua izin ${permission} Aktiva telah dihapus`, 'warning');
                        }
                    });
                });

                // Handle sub-module checkbox changes to update header checkboxes
                document.querySelectorAll('[data-parent="aktiva"] .permission-checkbox').forEach(function(subCheckbox) {
                    subCheckbox.addEventListener('change', function() {
                        updateAktivaHeaderCheckboxes();
                    });
                });

                // Initialize header checkboxes state
                updateAktivaHeaderCheckboxes();
            }

            function updateAktivaHeaderCheckboxes() {
                const permissions = ['view', 'create', 'update', 'delete', 'approve', 'mark-paid', 'print', 'export'];

                permissions.forEach(function(permission) {
                    const headerCheckbox = document.querySelector(`.aktiva-header-checkbox[data-permission="${permission}"]`);
                    const aktivaCheckboxes = document.querySelectorAll(`[data-parent="aktiva"] input[name*="[${permission}]"]`);

                    if (headerCheckbox && aktivaCheckboxes.length > 0) {
                        const allChecked = Array.from(aktivaCheckboxes).every(cb => cb.checked);
                        const someChecked = Array.from(aktivaCheckboxes).some(cb => cb.checked);

                        headerCheckbox.checked = allChecked;
                        headerCheckbox.indeterminate = someChecked && !allChecked;
                    }
                });
            }

            function initializeCheckAllMaster() {
                // Handle header checkbox changes
                document.querySelectorAll('.master-header-checkbox').forEach(function(headerCheckbox) {
                    headerCheckbox.addEventListener('change', function() {
                        const permission = this.dataset.permission;
                        const isChecked = this.checked;

                        // Update all checkboxes for this permission in master sub-modules
                        const masterCheckboxes = document.querySelectorAll(`[data-parent="master"] input[name*="[${permission}]"]`);
                        masterCheckboxes.forEach(function(checkbox) {
                            checkbox.checked = isChecked;
                        });

                        // Show toast notification
                        if (isChecked) {
                            showToast(`Semua izin ${permission} Master Data telah dicentang`, 'success');
                        } else {
                            showToast(`Semua izin ${permission} Master Data telah dihapus`, 'warning');
                        }
                    });
                });

                // Handle sub-module checkbox changes to update header checkboxes
                document.querySelectorAll('[data-parent="master"] .permission-checkbox').forEach(function(subCheckbox) {
                    subCheckbox.addEventListener('change', function() {
                        updateMasterHeaderCheckboxes();
                    });
                });

                // Initialize header checkboxes state
                updateMasterHeaderCheckboxes();
            }

            function updateMasterHeaderCheckboxes() {
                const permissions = ['view', 'create', 'update', 'delete', 'approve', 'mark-paid', 'print', 'export'];

                permissions.forEach(function(permission) {
                    const headerCheckbox = document.querySelector(`.master-header-checkbox[data-permission="${permission}"]`);
                    const masterCheckboxes = document.querySelectorAll(`[data-parent="master"] input[name*="[${permission}]"]`);

                    if (headerCheckbox && masterCheckboxes.length > 0) {
                        const allChecked = Array.from(masterCheckboxes).every(cb => cb.checked);
                        const someChecked = Array.from(masterCheckboxes).some(cb => cb.checked);

                        headerCheckbox.checked = allChecked;
                        headerCheckbox.indeterminate = someChecked && !allChecked;
                    }
                });
            }

            function initializeCheckAllUser() {
                // Handle header checkbox changes
                document.querySelectorAll('.user-header-checkbox').forEach(function(headerCheckbox) {
                    headerCheckbox.addEventListener('change', function() {
                        const permission = this.dataset.permission;
                        const isChecked = this.checked;

                        // Update all checkboxes for this permission in user sub-modules
                        const userCheckboxes = document.querySelectorAll(`[data-parent="user"] input[name*="[${permission}]"]`);
                        userCheckboxes.forEach(function(checkbox) {
                            checkbox.checked = isChecked;
                        });

                        // Show toast notification
                        if (isChecked) {
                            showToast(`Semua izin ${permission} User telah dicentang`, 'success');
                        } else {
                            showToast(`Semua izin ${permission} User telah dihapus`, 'warning');
                        }
                    });
                });

                // Handle sub-module checkbox changes to update header checkboxes
                document.querySelectorAll('[data-parent="user"] .permission-checkbox').forEach(function(subCheckbox) {
                    subCheckbox.addEventListener('change', function() {
                        updateUserHeaderCheckboxes();
                    });
                });

                // Initialize header checkboxes state
                updateUserHeaderCheckboxes();
            }

            function updateUserHeaderCheckboxes() {
                const permissions = ['view', 'create', 'update', 'delete', 'approve', 'mark-paid', 'print', 'export'];

                permissions.forEach(function(permission) {
                    const headerCheckbox = document.querySelector(`.user-header-checkbox[data-permission="${permission}"]`);
                    const userCheckboxes = document.querySelectorAll(`[data-parent="user"] input[name*="[${permission}]"]`);

                    if (headerCheckbox && userCheckboxes.length > 0) {
                        const allChecked = Array.from(userCheckboxes).every(cb => cb.checked);
                        const someChecked = Array.from(userCheckboxes).some(cb => cb.checked);

                        headerCheckbox.checked = allChecked;
                        headerCheckbox.indeterminate = someChecked && !allChecked;
                    }
                });
            }

            // Initialize checkbox handling for Aktivitas Lain-lain
            function initializeCheckAllAktivitasLainnya() {
                // Handle header checkbox changes
                document.querySelectorAll('.aktivitas-lainnya-header-checkbox').forEach(function(headerCheckbox) {
                    headerCheckbox.addEventListener('change', function() {
                        const permission = this.dataset.permission;
                        const isChecked = this.checked;

                        // Update all checkboxes for this permission in aktivitas-lainnya sub-modules
                        const aktivitasCheckboxes = document.querySelectorAll(`[data-parent="aktivitas-lainnya"] input[name*="[${permission}]"]`);
                        aktivitasCheckboxes.forEach(function(checkbox) {
                            checkbox.checked = isChecked;
                        });

                        // Show toast notification
                        if (isChecked) {
                            showToast(`✅ Semua izin ${permission} Aktivitas Lain-lain telah dicentang`, 'success');
                        } else {
                            showToast(`❌ Semua izin ${permission} Aktivitas Lain-lain telah dihapus`, 'warning');
                        }
                    });
                });

                // Handle sub-module checkbox changes to update header checkboxes
                document.querySelectorAll('[data-parent="aktivitas-lainnya"] .permission-checkbox').forEach(function(subCheckbox) {
                    subCheckbox.addEventListener('change', function() {
                        updateAktivitasLainnyaHeaderCheckboxes();
                    });
                });

                // Initialize header checkboxes state
                updateAktivitasLainnyaHeaderCheckboxes();
            }

            function updateAktivitasLainnyaHeaderCheckboxes() {
                const permissions = ['view', 'create', 'update', 'delete', 'approve', 'mark-paid', 'print', 'export'];

                permissions.forEach(function(permission) {
                    const headerCheckbox = document.querySelector(`.aktivitas-lainnya-header-checkbox[data-permission="${permission}"]`);
                    const aktivitasCheckboxes = document.querySelectorAll(`[data-parent="aktivitas-lainnya"] input[name*="[${permission}]"]`);

                    if (headerCheckbox && aktivitasCheckboxes.length > 0) {
                        const allChecked = Array.from(aktivitasCheckboxes).every(cb => cb.checked);
                        const someChecked = Array.from(aktivitasCheckboxes).some(cb => cb.checked);

                        headerCheckbox.checked = allChecked;
                        headerCheckbox.indeterminate = someChecked && !allChecked;
                    }
                });
            }

            function initializeCheckAllPembayaran() {
                // Handle header checkbox changes
                document.querySelectorAll('.pembayaran-header-checkbox').forEach(function(headerCheckbox) {
                    headerCheckbox.addEventListener('change', function() {
                        const permission = this.dataset.permission;
                        const isChecked = this.checked;

                        // Update all checkboxes for this permission in pembayaran sub-modules
                        const pembayaranCheckboxes = document.querySelectorAll(`[data-parent="pembayaran"] input[name*="[${permission}]"]`);
                        pembayaranCheckboxes.forEach(function(checkbox) {
                            checkbox.checked = isChecked;
                        });

                        // Show toast notification
                        if (isChecked) {
                            showToast(`Semua izin ${permission} Pembayaran telah dicentang`, 'success');
                        } else {
                            showToast(`Semua izin ${permission} Pembayaran telah dihapus`, 'warning');
                        }
                    });
                });

                // Handle sub-module checkbox changes to update header checkboxes
                document.querySelectorAll('[data-parent="pembayaran"] .permission-checkbox').forEach(function(subCheckbox) {
                    subCheckbox.addEventListener('change', function() {
                        updatePembayaranHeaderCheckboxes();
                    });
                });

                // Initialize header checkboxes state
                updatePembayaranHeaderCheckboxes();
            }

            function updatePembayaranHeaderCheckboxes() {
                const permissions = ['view', 'create', 'update', 'delete', 'approve', 'mark-paid', 'print', 'export'];

                permissions.forEach(function(permission) {
                    const headerCheckbox = document.querySelector(`.pembayaran-header-checkbox[data-permission="${permission}"]`);
                    const pembayaranCheckboxes = document.querySelectorAll(`[data-parent="pembayaran"] input[name*="[${permission}]"]`);

                    if (headerCheckbox && pembayaranCheckboxes.length > 0) {
                        const allChecked = Array.from(pembayaranCheckboxes).every(cb => cb.checked);
                        const someChecked = Array.from(pembayaranCheckboxes).some(cb => cb.checked);

                        headerCheckbox.checked = allChecked;
                        headerCheckbox.indeterminate = someChecked && !allChecked;
                    }
                });
            }

            function initializeCheckAllAktivitas() {
                // Handle header checkbox changes
                document.querySelectorAll('.aktivitas-header-checkbox').forEach(function(headerCheckbox) {
                    headerCheckbox.addEventListener('change', function() {
                        const permission = this.dataset.permission;
                        const isChecked = this.checked;

                        // Update all checkboxes for this permission in aktivitas sub-modules
                        const aktivitasCheckboxes = document.querySelectorAll(`[data-parent="aktivitas"] input[name*="[${permission}]"]`);
                        aktivitasCheckboxes.forEach(function(checkbox) {
                            checkbox.checked = isChecked;
                        });

                        // Show toast notification
                        if (isChecked) {
                            showToast(`Semua izin ${permission} Aktivitas telah dicentang`, 'success');
                        } else {
                            showToast(`Semua izin ${permission} Aktivitas telah dihapus`, 'warning');
                        }
                    });
                });

                // Handle sub-module checkbox changes to update header checkboxes
                document.querySelectorAll('[data-parent="aktivitas"] .permission-checkbox').forEach(function(subCheckbox) {
                    subCheckbox.addEventListener('change', function() {
                        updateAktivitasHeaderCheckboxes();
                    });
                });

                // Initialize header checkboxes state
                updateAktivitasHeaderCheckboxes();
            }

            function updateAktivitasHeaderCheckboxes() {
                const permissions = ['view', 'create', 'update', 'delete', 'approve', 'mark-paid', 'print', 'export'];

                permissions.forEach(function(permission) {
                    const headerCheckbox = document.querySelector(`.aktivitas-header-checkbox[data-permission="${permission}"]`);
                    const aktivitasCheckboxes = document.querySelectorAll(`[data-parent="aktivitas"] input[name*="[${permission}]"]`);

                    if (headerCheckbox && aktivitasCheckboxes.length > 0) {
                        const allChecked = Array.from(aktivitasCheckboxes).every(cb => cb.checked);
                        const someChecked = Array.from(aktivitasCheckboxes).some(cb => cb.checked);

                        headerCheckbox.checked = allChecked;
                        headerCheckbox.indeterminate = someChecked && !allChecked;
                    }
                });
            }

            function initializeCheckAllApproval() {
                // Handle header checkbox changes
                document.querySelectorAll('.approval-header-checkbox').forEach(function(headerCheckbox) {
                    headerCheckbox.addEventListener('change', function() {
                        const permission = this.dataset.permission;
                        const isChecked = this.checked;

                        // Update all checkboxes for this permission in approval sub-modules
                        const approvalCheckboxes = document.querySelectorAll(`[data-parent="approval"] input[name*="[${permission}]"]`);
                        approvalCheckboxes.forEach(function(checkbox) {
                            checkbox.checked = isChecked;
                        });

                        // Show toast notification
                        if (isChecked) {
                            showToast(`Semua izin ${permission} Approval telah dicentang`, 'success');
                        } else {
                            showToast(`Semua izin ${permission} Approval telah dihapus`, 'warning');
                        }
                    });
                });

                // Handle sub-module checkbox changes to update header checkboxes
                document.querySelectorAll('[data-parent="approval"] .permission-checkbox').forEach(function(subCheckbox) {
                    subCheckbox.addEventListener('change', function() {
                        updateApprovalHeaderCheckboxes();
                    });
                });

                // Initialize header checkboxes state
                updateApprovalHeaderCheckboxes();
            }

            function updateApprovalHeaderCheckboxes() {
                const permissions = ['view', 'create', 'update', 'delete', 'approve', 'mark-paid', 'print', 'export'];

                permissions.forEach(function(permission) {
                    const headerCheckbox = document.querySelector(`.approval-header-checkbox[data-permission="${permission}"]`);
                    const approvalCheckboxes = document.querySelectorAll(`[data-parent="approval"] input[name*="[${permission}]"]`);

                    if (headerCheckbox && approvalCheckboxes.length > 0) {
                        const allChecked = Array.from(approvalCheckboxes).every(cb => cb.checked);
                        const someChecked = Array.from(approvalCheckboxes).some(cb => cb.checked);

                        headerCheckbox.checked = allChecked;
                        headerCheckbox.indeterminate = someChecked && !allChecked;
                    }
                });
            }

            function initializeCheckAllOperational() {
                // Handle header checkbox changes
                document.querySelectorAll('.operational-header-checkbox').forEach(function(headerCheckbox) {
                    headerCheckbox.addEventListener('change', function() {
                        const permission = this.dataset.permission;
                        const isChecked = this.checked;

                        // Update all checkboxes for this permission in operational sub-modules
                        const operationalCheckboxes = document.querySelectorAll(`[data-parent="operational"] input[name*="[${permission}]"]`);
                        operationalCheckboxes.forEach(function(checkbox) {
                            checkbox.checked = isChecked;
                        });

                        // Show toast notification
                        if (isChecked) {
                            showToast(`Semua izin ${permission} Operational telah dicentang`, 'success');
                        } else {
                            showToast(`Semua izin ${permission} Operational telah dihapus`, 'warning');
                        }
                    });
                });

                // Handle sub-module checkbox changes to update header checkboxes
                document.querySelectorAll('[data-parent="operational"] .permission-checkbox').forEach(function(subCheckbox) {
                    subCheckbox.addEventListener('change', function() {
                        updateOperationalHeaderCheckboxes();
                    });
                });

                // Initialize header checkboxes state
                updateOperationalHeaderCheckboxes();
            }

            function updateOperationalHeaderCheckboxes() {
                const permissions = ['view', 'create', 'update', 'delete', 'approve', 'mark-paid', 'print', 'export'];

                permissions.forEach(function(permission) {
                    const headerCheckbox = document.querySelector(`.operational-header-checkbox[data-permission="${permission}"]`);
                    const operationalCheckboxes = document.querySelectorAll(`[data-parent="operational"] input[name*="[${permission}]"]`);

                    if (headerCheckbox && operationalCheckboxes.length > 0) {
                        const allChecked = Array.from(operationalCheckboxes).every(cb => cb.checked);
                        const someChecked = Array.from(operationalCheckboxes).some(cb => cb.checked);

                        headerCheckbox.checked = allChecked;
                        headerCheckbox.indeterminate = someChecked && !allChecked;
                    }
                });
            }

            function initializeCheckAllAuditLog() {
                // Handle header checkbox changes
                document.querySelectorAll('.audit-log-header-checkbox').forEach(function(headerCheckbox) {
                    headerCheckbox.addEventListener('change', function() {
                        const permission = this.dataset.permission;
                        const isChecked = this.checked;

                        // Update all checkboxes for this permission in audit log sub-modules
                        const auditLogCheckboxes = document.querySelectorAll(`[data-parent="audit-log"] input[name*="[${permission}]"]`);
                        auditLogCheckboxes.forEach(function(checkbox) {
                            checkbox.checked = isChecked;
                        });

                        // Show toast notification
                        if (isChecked) {
                            showToast(`✅ Semua izin ${permission} Audit Log telah dicentang`, 'success');
                        } else {
                            showToast(`❌ Semua izin ${permission} Audit Log telah dihapus`, 'warning');
                        }
                    });
                });

                // Handle sub-module checkbox changes to update header checkboxes
                document.querySelectorAll('[data-parent="audit-log"] .permission-checkbox').forEach(function(subCheckbox) {
                    subCheckbox.addEventListener('change', function() {
                        updateAuditLogHeaderCheckboxes();
                    });
                });

                // Initialize header checkboxes state
                updateAuditLogHeaderCheckboxes();
            }

            function updateAuditLogHeaderCheckboxes() {
                const permissions = ['view', 'export'];

                permissions.forEach(function(permission) {
                    const headerCheckbox = document.querySelector(`.audit-log-header-checkbox[data-permission="${permission}"]`);
                    const auditLogCheckboxes = document.querySelectorAll(`[data-parent="audit-log"] input[name*="[${permission}]"]`);

                    if (headerCheckbox && auditLogCheckboxes.length > 0) {
                        const allChecked = Array.from(auditLogCheckboxes).every(cb => cb.checked);
                        const someChecked = Array.from(auditLogCheckboxes).some(cb => cb.checked);

                        headerCheckbox.checked = allChecked;
                        headerCheckbox.indeterminate = someChecked && !allChecked;
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
