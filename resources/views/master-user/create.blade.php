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
            <legend class="text-lg font-semibold text-gray-800 px-2">Izin Akses</legend>
            <div class="space-y-6 pt-4">
                @php
                    $groupedPermissions = $permissions->groupBy(function($item) {
                        // Mengambil bagian pertama dari nama izin, cth: 'master-user' -> 'master'
                        return explode('-', $item->name)[0];
                    });
                @endphp
                @foreach ($groupedPermissions as $groupName => $groupPermissions)
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-bold text-md text-gray-700 mb-3 border-b pb-2">{{ ucfirst($groupName) }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                            @foreach ($groupPermissions as $permission)
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="permission-{{ $permission->id }}" name="permissions[]" type="checkbox" value="{{ $permission->id }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="permission-{{ $permission->id }}" class="font-medium text-gray-700">{{ $permission->description ?? $permission->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
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
        });
    </script>
@endpush
