@extends('layouts.app')

@section('title','Tambah Pengguna Baru')
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

        .permission-checkbox {
            accent-color: #3b82f6;
            transform: scale(1.1);
        }

        .expand-icon {
            display: inline-block;
            width: 20px;
            text-align: center;
            transition: transform 0.2s;
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

        {{-- Permissions System - Accurate Style --}}
        <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">🔐 Sistem Izin Akses (Accurate Style)</h3>
            </div>

            {{-- Permission Matrix --}}
            <div class="permission-section">
                <div class="permission-section-header">
                    <div class="permission-section-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">📊</div>
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
                            ✅ Centang Semua
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
                                        <span class="text-lg mr-2">📊</span>
                                        <div>
                                            <div class="font-semibold">Dashboard</div>
                                            <div class="text-xs text-gray-500">Halaman utama sistem</div>
                                        </div>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[dashboard][view]" value="1" class="permission-checkbox" checked></td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>

                            {{-- Master Data --}}
                            <tr class="module-row" data-module="master">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <span class="text-lg mr-2">📁</span>
                                        <div>
                                            <div class="font-semibold">Master Data</div>
                                            <div class="text-xs text-gray-500">Data master sistem</div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="7" class="text-center text-gray-500 text-sm py-3">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Header menu - izin ditentukan oleh sub-menu
                                </td>
                            </tr>

                            {{-- Master Data Sub-modules --}}
                            {{-- Hidden checkbox for master-karyawan main permission --}}
                            <input type="hidden" name="permissions[master-karyawan][main]" value="0" id="master-karyawan-main">
                            <input type="checkbox" name="permissions[master-karyawan][main]" value="1" id="master-karyawan-main-checkbox" class="hidden">

                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 👥</span>
                                        <span>Data Karyawan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-karyawan][view]" value="1" class="permission-checkbox karyawan-permission"></td>
                                <td><input type="checkbox" name="permissions[master-karyawan][create]" value="1" class="permission-checkbox karyawan-permission"></td>
                                <td><input type="checkbox" name="permissions[master-karyawan][update]" value="1" class="permission-checkbox karyawan-permission"></td>
                                <td><input type="checkbox" name="permissions[master-karyawan][delete]" value="1" class="permission-checkbox karyawan-permission"></td>
                                <td class="text-center text-gray-400">-</td>
                                <td><input type="checkbox" name="permissions[master-karyawan][print]" value="1" class="permission-checkbox karyawan-permission"></td>
                                <td><input type="checkbox" name="permissions[master-karyawan][export]" value="1" class="permission-checkbox karyawan-permission"></td>
                            </tr>

                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 👤</span>
                                        <span>Data User</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-user][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-user][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-user][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-user][delete]" value="1" class="permission-checkbox"></td>
                                <td class="text-center text-gray-400">-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>

                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 📦</span>
                                        <span>Data Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-kontainer][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-kontainer][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-kontainer][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-kontainer][delete]" value="1" class="permission-checkbox"></td>
                                <td class="text-center text-gray-400">-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>

                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 🎯</span>
                                        <span>Data Tujuan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-tujuan][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-tujuan][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-tujuan][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-tujuan][delete]" value="1" class="permission-checkbox"></td>
                                <td class="text-center text-gray-400">-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>

                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 📋</span>
                                        <span>Data Kegiatan</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-kegiatan][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-kegiatan][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-kegiatan][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-kegiatan][delete]" value="1" class="permission-checkbox"></td>
                                <td class="text-center text-gray-400">-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>

                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 🔐</span>
                                        <span>Data Permission</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-permission][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-permission][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-permission][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-permission][delete]" value="1" class="permission-checkbox"></td>
                                <td class="text-center text-gray-400">-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>

                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 🚗</span>
                                        <span>Data Mobil</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-mobil][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-mobil][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-mobil][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-mobil][delete]" value="1" class="permission-checkbox"></td>
                                <td class="text-center text-gray-400">-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>

                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 💰</span>
                                        <span>Data Pricelist Sewa Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-pricelist-sewa-kontainer][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-sewa-kontainer][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-sewa-kontainer][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-pricelist-sewa-kontainer][delete]" value="1" class="permission-checkbox"></td>
                                <td class="text-center text-gray-400">-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>

                            <tr class="submodule-row" data-parent="master">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 📊</span>
                                        <span>Data Tipe Akun</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[master-tipe-akun][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-tipe-akun][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-tipe-akun][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[master-tipe-akun][delete]" value="1" class="permission-checkbox"></td>
                                <td class="text-center text-gray-400">-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>

                            {{-- Tagihan Kontainer Sewa --}}
                            <tr class="module-row" data-module="tagihan-kontainer">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <span class="text-lg mr-2">💰</span>
                                        <div>
                                            <div class="font-semibold">Tagihan Kontainer Sewa & Pranota</div>
                                            <div class="text-xs text-gray-500">Modul tagihan, pembayaran kontainer, pranota dan permohonan</div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="7" class="text-center text-gray-500 text-sm py-3">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Header menu - izin ditentukan oleh sub-menu
                                </td>
                            </tr>

                            {{-- Tagihan Kontainer Sewa Sub-modules --}}
                            <tr class="submodule-row" data-parent="tagihan-kontainer">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 📋</span>
                                        <span>Daftar Tagihan Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer][delete]" value="1" class="permission-checkbox"></td>
                                <td class="text-center text-gray-400">-</td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer][print]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[tagihan-kontainer][export]" value="1" class="permission-checkbox"></td>
                            </tr>

                            <tr class="submodule-row" data-parent="tagihan-kontainer">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 📄</span>
                                        <span>Daftar Pranota Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pranota][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pranota][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pranota][delete]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pranota][approve]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pranota][print]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pranota][export]" value="1" class="permission-checkbox"></td>
                            </tr>

                            <tr class="submodule-row" data-parent="tagihan-kontainer">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 💳</span>
                                        <span>Pembayaran Pranota Kontainer</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][delete]" value="1" class="permission-checkbox"></td>
                                <td class="text-center text-gray-400">-</td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][print]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-kontainer][export]" value="1" class="permission-checkbox"></td>
                            </tr>

                            <tr class="submodule-row" data-parent="tagihan-kontainer">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 🚛</span>
                                        <span>Pranota Supir</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pranota-supir][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pranota-supir][create]" value="1" class="permission-checkbox"></td>
                                <td class="text-center text-gray-400">-</td>
                                <td class="text-center text-gray-400">-</td>
                                <td class="text-center text-gray-400">-</td>
                                <td><input type="checkbox" name="permissions[pranota-supir][print]" value="1" class="permission-checkbox"></td>
                                <td class="text-center text-gray-400">-</td>
                            </tr>

                            <tr class="submodule-row" data-parent="tagihan-kontainer">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 💰</span>
                                        <span>Pembayaran Pranota Supir</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][delete]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][approve]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][print]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[pembayaran-pranota-supir][export]" value="1" class="permission-checkbox"></td>
                            </tr>

                            {{-- Permohonan Memo Sub-modules (moved to tagihan-kontainer) --}}
                            <tr class="submodule-row" data-parent="tagihan-kontainer">
                                <td class="submodule">
                                    <div class="flex items-center">
                                        <span class="text-sm mr-2">└─ 📝</span>
                                        <span>Permohonan Memo</span>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[permohonan][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[permohonan][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[permohonan][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[permohonan][delete]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[permohonan][approve]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[permohonan][print]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[permohonan][export]" value="1" class="permission-checkbox"></td>
                            </tr>

                            {{-- User Approval --}}
                            <tr class="module-row" data-module="user-approval">
                                <td class="module-header">
                                    <div class="flex items-center">
                                        <span class="expand-icon text-lg mr-2">▶</span>
                                        <span class="text-lg mr-2">✅</span>
                                        <div>
                                            <div class="font-semibold">Persetujuan User</div>
                                            <div class="text-xs text-gray-500">Modul approval user baru</div>
                                        </div>
                                    </div>
                                </td>
                                <td><input type="checkbox" name="permissions[user-approval][view]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[user-approval][create]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[user-approval][update]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[user-approval][delete]" value="1" class="permission-checkbox"></td>
                                <td>-</td>
                                <td><input type="checkbox" name="permissions[user-approval][print]" value="1" class="permission-checkbox"></td>
                                <td><input type="checkbox" name="permissions[user-approval][export]" value="1" class="permission-checkbox"></td>
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
                            <h4 class="text-sm font-medium text-indigo-800">📋 Copy Permission dari User Lain</h4>
                            <p class="text-sm text-indigo-700 mt-1">
                                Pilih user yang sudah ada untuk menyalin semua izin aksesnya ke user baru ini.
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
                            📋 Copy Permission
                        </button>
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

            // Copy Permission Feature

            // Module Expand/Collapse Functionality
            // Add click event listeners to module rows
            document.querySelectorAll('.module-row').forEach(function(row) {
                row.addEventListener('click', function(e) {
                    // Don't trigger if clicking on a checkbox
                    if (e.target.type === 'checkbox') {
                        return;
                    }

                    const module = this.dataset.module;
                    const icon = this.querySelector('.expand-icon');
                    const isExpanded = icon.classList.contains('expanded');

                    if (isExpanded) {
                        // Collapse
                        collapseModule(module);
                    } else {
                        // Expand
                        expandModule(module);
                    }
                });
            });

            // Initially hide all sub-modules
            document.querySelectorAll('.submodule-row').forEach(function(row) {
                row.style.display = 'none';
            });

            // Expand Module Function
            function expandModule(moduleName) {
                const moduleRow = document.querySelector(`[data-module="${moduleName}"]`);
                const icon = moduleRow.querySelector('.expand-icon');
                const submodules = document.querySelectorAll(`[data-parent="${moduleName}"]`);

                // Update icon
                icon.classList.add('expanded');
                icon.textContent = '▼';

                // Show sub-modules
                submodules.forEach(function(submodule) {
                    submodule.style.display = 'table-row';
                    submodule.classList.add('visible');
                });

                // Update row styling
                moduleRow.classList.add('expanded');
            }

            // Collapse Module Function
            function collapseModule(moduleName) {
                const moduleRow = document.querySelector(`[data-module="${moduleName}"]`);
                const icon = moduleRow.querySelector('.expand-icon');
                const submodules = document.querySelectorAll(`[data-parent="${moduleName}"]`);

                // Update icon
                icon.classList.remove('expanded');
                icon.textContent = '▶';

                // Hide sub-modules
                submodules.forEach(function(submodule) {
                    submodule.style.display = 'none';
                    submodule.classList.remove('visible');
                });

                // Update row styling
                moduleRow.classList.remove('expanded');
            }

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

                            showToast(`✅ Berhasil menyalin permission dari ${userName}`, 'success');
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

                // Auto remove after 3 seconds
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }

            // Initialize with dashboard view permission checked by default
            document.querySelector('input[name="permissions[dashboard][view]"]').checked = true;

            // Master Karyawan Permission Logic
            // When any karyawan permission is checked, also check the main permission
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
                    hiddenInput.value = anyChecked ? '0' : '0'; // Hidden input is always 0, checkbox handles the value
                }

                console.log('Karyawan main permission updated:', anyChecked);
            }

            // Add event listeners to karyawan permission checkboxes
            document.querySelectorAll('.karyawan-permission').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    updateKaryawanMainPermission();

                    const karyawanCheckboxes = document.querySelectorAll('.karyawan-permission');
                    const anyChecked = Array.from(karyawanCheckboxes).some(cb => cb.checked);

                    if (anyChecked) {
                        showToast('✅ Permission menu Master Karyawan telah diaktifkan', 'success');
                    } else {
                        showToast('⚠️ Permission menu Master Karyawan telah dinonaktifkan', 'warning');
                    }
                });
            });

            // Initialize main permission on page load
            updateKaryawanMainPermission();

            // Check All Permissions Button
            document.getElementById('check_all_permissions').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.permission-checkbox');
                const isAllChecked = Array.from(checkboxes).every(cb => cb.checked);

                if (isAllChecked) {
                    // If all are checked, uncheck them
                    checkboxes.forEach(cb => cb.checked = false);
                    this.innerHTML = '✅ Centang Semua';
                    this.classList.remove('bg-red-600', 'hover:bg-red-700');
                    this.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    showToast('❌ Semua izin telah dihapus', 'warning');
                } else {
                    // If not all checked, check them all
                    checkboxes.forEach(cb => cb.checked = true);
                    this.innerHTML = '❌ Hapus Semua';
                    this.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    this.classList.add('bg-red-600', 'hover:bg-red-700');
                    showToast('✅ Semua izin telah dicentang', 'success');
                }
            });
        });
    </script>
@endpush
