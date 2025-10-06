@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Profil Saya</h2>
        <div class="flex items-center space-x-3">
            <a href="{{ route('dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
                Kembali
            </a>
        </div>
    </div>

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
