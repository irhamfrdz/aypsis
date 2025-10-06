@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-5xl mx-auto p-4 bg-white shadow-md rounded-lg">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Profil Saya</h2>
        <div class="flex items-center space-x-2">
            <a href="{{ route('profile.edit') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-1.5 px-3 rounded text-sm">
                Edit
            </a>
            <a href="{{ route('dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-1.5 px-3 rounded text-sm">
                Kembali
            </a>
        </div>
    </div>

    @php
        $formatDate = function($value, $format = 'd/M/Y') {
            if (empty($value)) return '-';
            if ($value instanceof \Illuminate\Support\Carbon || $value instanceof \DateTimeInterface) {
                try { return $value->format($format); } catch (\Throwable $e) { return '-'; }
            }
            try {
                $ts = strtotime((string)$value);
                return ($ts === false || $ts === -1) ? '-' : date($format, $ts);
            } catch (\Throwable $e) { return '-'; }
        };
    @endphp

    <details open class="mb-3 border rounded">
        <summary class="px-3 py-2 bg-gray-50 cursor-pointer font-medium text-sm">Akun</summary>
        <div class="p-3 grid grid-cols-3 gap-4 text-xs">
            <div><p class="font-medium text-gray-600">Nama Lengkap</p><p class="text-gray-800">{{ $user->name ?? '-' }}</p></div>
            <div><p class="font-medium text-gray-600">Username</p><p class="text-gray-800">{{ $user->username ?? '-' }}</p></div>
            <div><p class="font-medium text-gray-600">Email</p><p class="text-gray-800">{{ $user->email ?? '-' }} @if(!empty($user->email))<button onclick="navigator.clipboard.writeText('{{ $user->email }}')" class="ml-1 text-xs text-gray-500">📋</button>@endif</p></div>
            <div><p class="font-medium text-gray-600">Status</p><p class="text-gray-800">@if($user->status === 'approved')Aktif@elseif($user->status === 'pending')Pending@elseTidak Aktif@endif</p></div>
            <div><p class="font-medium text-gray-600">Bergabung</p><p class="text-gray-800">{{ $formatDate($user->created_at, 'd/M/Y') }}</p></div>
            <div><p class="font-medium text-gray-600">Update</p><p class="text-gray-800">{{ $formatDate($user->updated_at, 'd/M/Y') }}</p></div>
        </div>
    </details>

    @if($user->registration_reason)
        <details class="mb-3 border rounded">
            <summary class="px-3 py-2 bg-gray-50 cursor-pointer font-medium text-sm">Alasan Registrasi</summary>
            <div class="p-3 text-xs"><p class="text-gray-800 whitespace-pre-wrap">{{ $user->registration_reason }}</p></div>
        </details>
    @endif

    @if($user->karyawan)
        <details class="mb-3 border rounded">
            <summary class="px-3 py-2 bg-gray-50 cursor-pointer font-medium text-sm">Pribadi</summary>
            <div class="p-3 grid grid-cols-3 gap-4 text-xs">
                <div><p class="font-medium text-gray-600">NIK</p><p class="text-gray-800">{{ $user->karyawan->nik ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Nama Lengkap</p><p class="text-gray-800">{{ $user->karyawan->nama_lengkap ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Nama Panggilan</p><p class="text-gray-800">{{ $user->karyawan->nama_panggilan ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Email</p><p class="text-gray-800">{{ $user->karyawan->email ?? '-' }} @if(!empty($user->karyawan->email))<button onclick="navigator.clipboard.writeText('{{ $user->karyawan->email }}')" class="ml-1 text-xs text-gray-500">📋</button>@endif</p></div>
                <div><p class="font-medium text-gray-600">Tgl Lahir</p><p class="text-gray-800">{{ $formatDate($user->karyawan->tanggal_lahir, 'd/M/Y') }}</p></div>
                <div><p class="font-medium text-gray-600">Tmpt Lahir</p><p class="text-gray-800">{{ $user->karyawan->tempat_lahir ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Jenis Kelamin</p><p class="text-gray-800">{{ $user->karyawan->jenis_kelamin_label ?? ($user->karyawan->jenis_kelamin ?? '-') }}</p></div>
                <div><p class="font-medium text-gray-600">Agama</p><p class="text-gray-800">{{ $user->karyawan->agama ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Status Nikah</p><p class="text-gray-800">{{ $user->karyawan->status_perkawinan ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">No HP</p><p class="text-gray-800">{{ $user->karyawan->no_hp ?? '-' }} @if(!empty($user->karyawan->no_hp))<button onclick="navigator.clipboard.writeText('{{ $user->karyawan->no_hp }}')" class="ml-1 text-xs text-gray-500">📋</button>@endif</p></div>
                <div><p class="font-medium text-gray-600">KTP</p><p class="text-gray-800">{{ $user->karyawan->ktp ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">KK</p><p class="text-gray-800">{{ $user->karyawan->kk ?? '-' }}</p></div>
            </div>
        </details>

        <details class="mb-3 border rounded">
            <summary class="px-3 py-2 bg-gray-50 cursor-pointer font-medium text-sm">Alamat</summary>
            <div class="p-3 grid grid-cols-3 gap-4 text-xs">
                <div><p class="font-medium text-gray-600">Alamat</p><p class="text-gray-800">{{ $user->karyawan->alamat ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">RT/RW</p><p class="text-gray-800">{{ $user->karyawan->rt_rw ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Kelurahan</p><p class="text-gray-800">{{ $user->karyawan->kelurahan ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Kecamatan</p><p class="text-gray-800">{{ $user->karyawan->kecamatan ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Kabupaten</p><p class="text-gray-800">{{ $user->karyawan->kabupaten ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Provinsi</p><p class="text-gray-800">{{ $user->karyawan->provinsi ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Kode Pos</p><p class="text-gray-800">{{ $user->karyawan->kode_pos ?? '-' }}</p></div>
                <div class="col-span-2"><p class="font-medium text-gray-600">Alamat Lengkap</p><p class="text-gray-800">{{ $user->karyawan->alamat_lengkap ?? '-' }}</p></div>
            </div>
        </details>

        <details class="mb-3 border rounded">
            <summary class="px-3 py-2 bg-gray-50 cursor-pointer font-medium text-sm">Pekerjaan</summary>
            <div class="p-3 grid grid-cols-3 gap-4 text-xs">
                <div><p class="font-medium text-gray-600">Divisi</p><p class="text-gray-800">{{ $user->karyawan->divisi ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Pekerjaan</p><p class="text-gray-800">{{ $user->karyawan->pekerjaan ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Tgl Masuk</p><p class="text-gray-800">{{ $formatDate($user->karyawan->tanggal_masuk, 'd/M/Y') }}</p></div>
                <div><p class="font-medium text-gray-600">Tgl Berhenti</p><p class="text-gray-800">{{ $formatDate($user->karyawan->tanggal_berhenti, 'd/M/Y') }}</p></div>
                <div><p class="font-medium text-gray-600">NIK Supervisor</p><p class="text-gray-800">{{ $user->karyawan->nik_supervisor ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Supervisor</p><p class="text-gray-800">{{ $user->karyawan->supervisor ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Cabang</p><p class="text-gray-800">{{ $user->karyawan->cabang ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Plat</p><p class="text-gray-800">{{ $user->karyawan->plat ?? '-' }}</p></div>
            </div>
        </details>

        <details class="mb-3 border rounded">
            <summary class="px-3 py-2 bg-gray-50 cursor-pointer font-medium text-sm">Bank</summary>
            <div class="p-3 grid grid-cols-2 gap-4 text-xs">
                <div><p class="font-medium text-gray-600">Nama Bank</p><p class="text-gray-800">{{ $user->karyawan->nama_bank ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Cabang</p><p class="text-gray-800">{{ $user->karyawan->bank_cabang ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">No Rekening</p><p class="text-gray-800">{{ $user->karyawan->akun_bank ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">Atas Nama</p><p class="text-gray-800">{{ $user->karyawan->atas_nama ?? '-' }}</p></div>
            </div>
        </details>

        <details class="mb-3 border rounded">
            <summary class="px-3 py-2 bg-gray-50 cursor-pointer font-medium text-sm">Pajak & JKN</summary>
            <div class="p-3 grid grid-cols-3 gap-4 text-xs">
                <div><p class="font-medium text-gray-600">Status Pajak</p><p class="text-gray-800">{{ $user->karyawan->status_pajak_label ?? ($user->karyawan->status_pajak ?? '-') }}</p></div>
                <div><p class="font-medium text-gray-600">JKN</p><p class="text-gray-800">{{ $user->karyawan->jkn ?? '-' }}</p></div>
                <div><p class="font-medium text-gray-600">BP Jamsostek</p><p class="text-gray-800">{{ $user->karyawan->no_ketenagakerjaan ?? '-' }}</p></div>
            </div>
        </details>

        @if($user->karyawan->catatan)
            <div class="mt-4">
                <p class="font-medium text-gray-600 text-sm">Catatan</p>
                <div class="mt-1 p-2 bg-gray-50 border rounded text-gray-800 text-xs whitespace-pre-wrap">{{ $user->karyawan->catatan }}</div>
            </div>
        @endif
    @endif
</div>

<style>
    details > div { overflow: hidden; will-change: height; height: 0; }
    summary { list-style: none; }
    summary::-webkit-details-marker { display: none; }
    summary::marker { display: none; }
    summary::after { content: ' ▶'; float: right; transform: rotate(0deg); transition: transform 0.2s; }
    details[open] summary::after { transform: rotate(90deg); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function initDetails(d) {
            var summary = d.querySelector('summary');
            var content = d.querySelector('div');
            if (!summary || !content) return;

            if (d.hasAttribute('open')) {
                content.style.height = content.scrollHeight + 'px';
            } else {
                content.style.height = '0px';
            }

            summary.addEventListener('click', function(e) {
                e.preventDefault();
                if (d.hasAttribute('open')) {
                    content.style.height = content.scrollHeight + 'px';
                    void content.offsetHeight;
                    content.style.transition = 'height 0.2s ease';
                    content.style.height = '0px';
                    d.removeAttribute('open');
                    content.addEventListener('transitionend', function cb() {
                        content.style.transition = '';
                        content.removeEventListener('transitionend', cb);
                    });
                } else {
                    content.style.height = '0px';
                    d.setAttribute('open', '');
                    void content.offsetHeight;
                    var target = content.scrollHeight + 'px';
                    content.style.transition = 'height 0.2s ease';
                    content.style.height = target;
                    content.addEventListener('transitionend', function cb() {
                        if (d.hasAttribute('open')) content.style.height = 'auto';
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