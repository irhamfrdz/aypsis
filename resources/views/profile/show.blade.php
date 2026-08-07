@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Profil Saya</h2>
        <div class="flex items-center space-x-3">
            <button onclick="document.getElementById('editAccountModal').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                <i class="fas fa-edit mr-1"></i> Edit Akun
            </button>
            <a href="{{ route('dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-3 text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @php
        $formatDate = function($value, $format = 'd/M/Y') {
            if (empty($value)) return '-';
            // If it's a Carbon/DateTime instance, format directly
            if ($value instanceof \Illuminate\Support\Carbon || $value instanceof \DateTimeInterface) {
                try { return $value->format($format); } catch (\Throwable $e) { /* fallthrough */ }
            }
            // Try to parse string values
            try {
                $ts = strtotime((string)$value);
                if ($ts === false || $ts === -1) return '-';
                return date($format, $ts);
            } catch (\Throwable $e) {
                return '-';
            }
        };
    @endphp

    <details open class="mb-4 border rounded">
        <summary class="px-4 py-3 bg-gray-50 cursor-pointer font-semibold">Akun</summary>
        <div class="p-4 grid grid-cols-2 gap-6 text-sm">
            <div>
                <p class="font-semibold text-gray-600">Nama Lengkap</p>
                <p class="text-gray-800">{{ $user->name ?? '-' }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-600">Username</p>
                <p class="text-gray-800">{{ $user->username ?? '-' }}</p>
            </div>

            <div>
                <p class="font-semibold text-gray-600">Email</p>
                <p class="text-gray-800">{{ $user->email ?? '-' }}
                    @if(!empty($user->email))
                        <button onclick="navigator.clipboard.writeText('{{ $user->email }}')" class="ml-2 text-xs text-gray-500">Salin</button>
                    @endif
                </p>
            </div>
            <div>
                <p class="font-semibold text-gray-600">Status Akun</p>
                <p class="text-gray-800">
                    @if($user->status === 'approved')
                        Aktif
                    @elseif($user->status === 'pending')
                        Menunggu Persetujuan
                    @else
                        Tidak Aktif
                    @endif
                </p>
            </div>

            <div>
                <p class="font-semibold text-gray-600">Tanggal Bergabung</p>
                <p class="text-gray-800">{{ $formatDate($user->created_at, 'd/M/Y H:i') }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-600">Terakhir Update</p>
                <p class="text-gray-800">{{ $formatDate($user->updated_at, 'd/M/Y H:i') }}</p>
            </div>
        </div>
    </details>

    @if($user->karyawan)
        <details class="mb-4 border rounded">
            <summary class="px-4 py-3 bg-gray-50 cursor-pointer font-semibold">Pribadi</summary>
            <div class="p-4 grid grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="font-semibold text-gray-600">NIK</p>
                    <p class="text-gray-800">{{ $user->karyawan->nik ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Nama Lengkap</p>
                    <p class="text-gray-800">{{ $user->karyawan->nama_lengkap ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">Nama Panggilan</p>
                    <p class="text-gray-800">{{ $user->karyawan->nama_panggilan ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Email</p>
                    <p class="text-gray-800">{{ $user->karyawan->email ?? '-' }}
                        @if(!empty($user->karyawan->email))
                            <button onclick="navigator.clipboard.writeText('{{ $user->karyawan->email }}')" class="ml-2 text-xs text-gray-500">Salin</button>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">Tanggal Lahir</p>
                    <p class="text-gray-800">{{ $formatDate($user->karyawan->tanggal_lahir, 'd/M/Y') }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Tempat Lahir</p>
                    <p class="text-gray-800">{{ $user->karyawan->tempat_lahir ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">Jenis Kelamin</p>
                    <p class="text-gray-800">{{ $user->karyawan->jenis_kelamin_label ?? ($user->karyawan->jenis_kelamin ?? '-') }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Agama</p>
                    <p class="text-gray-800">{{ $user->karyawan->agama ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">Status Pernikahan</p>
                    <p class="text-gray-800">{{ $user->karyawan->status_perkawinan ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">No HP</p>
                    <p class="text-gray-800">{{ $user->karyawan->no_hp ?? '-' }}
                        @if(!empty($user->karyawan->no_hp))
                            <button onclick="navigator.clipboard.writeText('{{ $user->karyawan->no_hp }}')" class="ml-2 text-xs text-gray-500">Salin</button>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">Nomor KTP</p>
                    <p class="text-gray-800">{{ $user->karyawan->ktp ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Nomor KK</p>
                    <p class="text-gray-800">{{ $user->karyawan->kk ?? '-' }}</p>
                </div>
            </div>
        </details>

        <details class="mb-4 border rounded">
            <summary class="px-4 py-3 bg-gray-50 cursor-pointer font-semibold">Alamat</summary>
            <div class="p-4 grid grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="font-semibold text-gray-600">Alamat</p>
                    <p class="text-gray-800">{{ $user->karyawan->alamat ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">RT / RW</p>
                    <p class="text-gray-800">{{ $user->karyawan->rt_rw ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">Kelurahan</p>
                    <p class="text-gray-800">{{ $user->karyawan->kelurahan ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Kecamatan</p>
                    <p class="text-gray-800">{{ $user->karyawan->kecamatan ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">Kabupaten</p>
                    <p class="text-gray-800">{{ $user->karyawan->kabupaten ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Provinsi</p>
                    <p class="text-gray-800">{{ $user->karyawan->provinsi ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">Kode Pos</p>
                    <p class="text-gray-800">{{ $user->karyawan->kode_pos ?? '-' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="font-semibold text-gray-600">Alamat Lengkap</p>
                    <p class="text-gray-800">{{ $user->karyawan->alamat_lengkap ?? '-' }}</p>
                </div>
            </div>
        </details>

        <details class="mb-4 border rounded">
            <summary class="px-4 py-3 bg-gray-50 cursor-pointer font-semibold">Pekerjaan & Riwayat</summary>
            <div class="p-4 grid grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="font-semibold text-gray-600">Divisi</p>
                    <p class="text-gray-800">{{ $user->karyawan->divisi ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Pekerjaan</p>
                    <p class="text-gray-800">{{ $user->karyawan->pekerjaan ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">Tanggal Masuk</p>
                    <p class="text-gray-800">{{ $formatDate($user->karyawan->tanggal_masuk, 'd/M/Y') }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Tanggal Berhenti</p>
                    <p class="text-gray-800">{{ $formatDate($user->karyawan->tanggal_berhenti, 'd/M/Y') }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">Tanggal Masuk (Sebelumnya)</p>
                    <p class="text-gray-800">{{ $formatDate($user->karyawan->tanggal_masuk_sebelumnya, 'd/M/Y') }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Tanggal Berhenti (Sebelumnya)</p>
                    <p class="text-gray-800">{{ $formatDate($user->karyawan->tanggal_berhenti_sebelumnya, 'd/M/Y') }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">NIK Supervisor</p>
                    <p class="text-gray-800">{{ $user->karyawan->nik_supervisor ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Supervisor</p>
                    <p class="text-gray-800">{{ $user->karyawan->supervisor ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">Kantor Cabang AYP</p>
                    <p class="text-gray-800">{{ $user->karyawan->cabang ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Nomor Plat</p>
                    <p class="text-gray-800">{{ $user->karyawan->plat ?? '-' }}</p>
                </div>
            </div>
        </details>

        <details class="mb-4 border rounded">
            <summary class="px-4 py-3 bg-gray-50 cursor-pointer font-semibold">Bank</summary>
            <div class="p-4 grid grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="font-semibold text-gray-600">Nama Bank</p>
                    <p class="text-gray-800">{{ $user->karyawan->nama_bank ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Cabang Bank</p>
                    <p class="text-gray-800">{{ $user->karyawan->bank_cabang ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">Nomor Rekening</p>
                    <p class="text-gray-800">{{ $user->karyawan->akun_bank ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">Atas Nama</p>
                    <p class="text-gray-800">{{ $user->karyawan->atas_nama ?? '-' }}</p>
                </div>
            </div>
        </details>

        <details class="mb-4 border rounded">
            <summary class="px-4 py-3 bg-gray-50 cursor-pointer font-semibold">Pajak & JKN</summary>
            <div class="p-4 grid grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="font-semibold text-gray-600">Status Pajak</p>
                    <p class="text-gray-800">{{ $user->karyawan->status_pajak_label ?? ($user->karyawan->status_pajak ?? '-') }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-600">JKN</p>
                    <p class="text-gray-800">{{ $user->karyawan->jkn ?? '-' }}</p>
                </div>

                <div>
                    <p class="font-semibold text-gray-600">BP Jamsostek</p>
                    <p class="text-gray-800">{{ $user->karyawan->no_ketenagakerjaan ?? '-' }}</p>
                </div>
            </div>
        </details>

        @if($user->karyawan->catatan)
            <div class="mt-6">
                <p class="font-semibold text-gray-600">Catatan</p>
                <div class="mt-2 p-3 bg-gray-50 border rounded text-gray-800 min-h-[80px] whitespace-pre-wrap">
                    {{ $user->karyawan->catatan }}
                </div>
            </div>
        @endif
    @endif
</div>

<style>
    /* Prepare content for smooth height transitions */
    details > div {
        overflow: hidden;
        will-change: height;
        height: 0; /* JS will initialize correctly */
    }
</style>

<!-- Modal Edit Akun -->
<div id="editAccountModal" class="{{ $errors->has('username') || $errors->has('name') || $errors->has('current_password') || $errors->has('new_password') ? '' : 'hidden' }} fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('editAccountModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('profile.update.account') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Edit Akun</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Username</label>
                                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                    @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="border-t pt-4 mt-4">
                                    <h4 class="text-sm font-semibold text-gray-600 mb-2">Ganti Password (Opsional)</h4>
                                    <p class="text-xs text-gray-500 mb-3">Kosongkan jika tidak ingin mengganti password.</p>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                                            <input type="password" name="current_password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Password Baru</label>
                                            <input type="password" name="new_password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            @error('new_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                                            <input type="password" name="new_password_confirmation" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                    <button type="button" onclick="document.getElementById('editAccountModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* CSS transition setup */
div[style*="transition"] { overflow: hidden; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function initDetails(d) {
            var summary = d.querySelector('summary');
            var content = d.querySelector('div');
            if (!summary || !content) return;

            // Initialize height based on open state
            if (d.hasAttribute('open')) {
                content.style.height = content.scrollHeight + 'px';
            } else {
                content.style.height = '0px';
            }

            // Prevent native toggle; implement smooth animation
            summary.addEventListener('click', function(e) {
                e.preventDefault();
                if (d.hasAttribute('open')) {
                    // collapse
                    // set fixed height then transition to 0
                    content.style.height = content.scrollHeight + 'px';
                    // force reflow
                    void content.offsetHeight;
                    content.style.transition = 'height 240ms ease';
                    content.style.height = '0px';
                    d.removeAttribute('open');
                    content.addEventListener('transitionend', function cb() {
                        content.style.transition = '';
                        content.removeEventListener('transitionend', cb);
                    });
                } else {
                    // expand
                    // from 0 to scrollHeight, then set to auto
                    content.style.height = '0px';
                    d.setAttribute('open', '');
                    // force reflow
                    void content.offsetHeight;
                    var target = content.scrollHeight + 'px';
                    content.style.transition = 'height 240ms ease';
                    content.style.height = target;
                    content.addEventListener('transitionend', function cb() {
                        // allow natural height after animation
                        if (d.hasAttribute('open')) {
                            content.style.height = 'auto';
                        }
                        content.style.transition = '';
                        content.removeEventListener('transitionend', cb);
                    });
                }
            });
        }

        document.querySelectorAll('details').forEach(function(d) { initDetails(d); });
    });
</script>

@endsection
