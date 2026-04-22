@extends('layouts.app')

@section('title', 'Detail Pelamar Karyawan')
@section('page_title', 'Detail Pelamar')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Detail Pelamar Karyawan</h2>
        <div class="flex items-center space-x-3">
            <a href="{{ route('master.pelamar-karyawan.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Personal Information --}}
    <div class="mb-8 overflow-hidden border rounded-xl shadow-sm">
        <div class="px-5 py-3 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-700 uppercase tracking-wider text-xs">Data Pribadi</h3>
            @if($pelamar->cv_path)
                <a href="{{ asset('storage/' . $pelamar->cv_path) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase bg-indigo-50 px-3 py-1 rounded-full transition-all">
                    <i class="fas fa-file-pdf mr-1"></i> Buka CV / Resume
                </a>
            @endif
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Nama Lengkap</label>
                <p class="text-sm font-semibold text-gray-900">{{ strtoupper($pelamar->nama_lengkap) }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Email</label>
                <p class="text-sm text-gray-900 font-medium">{{ $pelamar->email ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Jenis Kelamin</label>
                <p class="text-sm text-gray-900 font-medium">{{ $pelamar->jenis_kelamin }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">No. Handphone</label>
                <p class="text-sm text-gray-900 font-medium">{{ $pelamar->no_handphone }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Agama</label>
                <p class="text-sm text-gray-900 font-medium">{{ $pelamar->agama ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Tempat, Tanggal Lahir</label>
                <p class="text-sm text-gray-900 font-medium">{{ strtoupper($pelamar->tempat_lahir) }}, {{ \Carbon\Carbon::parse($pelamar->tanggal_lahir)->format('d F Y') }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Kontak Darurat</label>
                <p class="text-sm text-gray-900 font-medium">{{ $pelamar->kontak_darurat ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Identification --}}
    <div class="mb-8 overflow-hidden border rounded-xl shadow-sm">
        <div class="px-5 py-3 bg-gray-50 border-b">
            <h3 class="font-bold text-gray-700 uppercase tracking-wider text-xs">Identitas & Legalitas</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">No. NIK (KTP)</label>
                <p class="text-sm font-semibold text-gray-900">{{ $pelamar->no_nik }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">No. Kartu Keluarga</label>
                <p class="text-sm font-semibold text-gray-900">{{ $pelamar->no_kartu_keluarga }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">NPWP</label>
                <p class="text-sm font-semibold text-gray-900">{{ $pelamar->npwp ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">No. BPJS Kesehatan</label>
                <p class="text-sm font-semibold text-gray-900">{{ $pelamar->no_bpjs_kesehatan ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">No. BPJS Ketenagakerjaan</label>
                <p class="text-sm font-semibold text-gray-900">{{ $pelamar->no_ketenagakerjaan ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Nomor Rekening</label>
                <p class="text-sm font-semibold text-gray-900">{{ $pelamar->nomor_rekening ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="mb-8 overflow-hidden border rounded-xl shadow-sm">
        <div class="px-5 py-3 bg-gray-50 border-b">
            <h3 class="font-bold text-gray-700 uppercase tracking-wider text-xs">Detail Tambahan</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Ukuran Wearpack</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                    {{ $pelamar->wearpack_size }}
                </span>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">No. Sepatu Safety</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                    {{ $pelamar->no_safety_shoes }}
                </span>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Jumlah Tanggungan (Anak)</label>
                <p class="text-sm font-semibold text-gray-900">{{ $pelamar->tanggungan_anak }} Orang</p>
            </div>
        </div>
    </div>

    {{-- Address --}}
    <div class="mb-8 overflow-hidden border rounded-xl shadow-sm">
        <div class="px-5 py-3 bg-gray-50 border-b">
            <h3 class="font-bold text-gray-700 uppercase tracking-wider text-xs">Alamat Tinggal</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Alamat Lengkap</label>
                <p class="text-sm text-gray-900 font-medium">{{ strtoupper($pelamar->alamat_lengkap) }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Kelurahan / Kecamatan</label>
                <p class="text-sm text-gray-900 font-medium">{{ strtoupper($pelamar->kelurahan) }} / {{ strtoupper($pelamar->kecamatan) }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Kota / Provinsi</label>
                <p class="text-sm text-gray-900 font-medium">{{ strtoupper($pelamar->kota_kabupaten) }} / {{ strtoupper($pelamar->provinsi) }}</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Kode Pos</label>
                <p class="text-sm text-gray-900 font-medium">{{ $pelamar->kode_pos }}</p>
            </div>
        </div>
    </div>

    <div class="mt-8 pt-6 border-t flex justify-between items-center text-[10px] text-gray-400 font-medium uppercase tracking-widest">
        <span>ID Pelamar: #{{ $pelamar->id }}</span>
        <span>Terdaftar pada: {{ $pelamar->created_at->format('d F Y H:i') }}</span>
    </div>
</div>
@endsection
