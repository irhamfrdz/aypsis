@extends('layouts.app')

@section('title', 'Detail Permohonan Pinjaman - AYPSIS')

@section('content')
<div class="container mx-auto max-w-5xl px-4 py-6">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('permohonan-pinjaman-karyawan.index') }}" class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h1 class="text-2xl font-black text-gray-900">{{ $permohonan->nomor_permohonan }}</h1>
                <p class="text-sm text-gray-500">Diajukan {{ $permohonan->tanggal_permohonan->format('d F Y') }}</p>
            </div>
        </div>
        @php
            $badge = match($permohonan->status) {
                'approved' => 'bg-emerald-100 text-emerald-800',
                'rejected' => 'bg-red-100 text-red-800',
                'cancelled' => 'bg-gray-100 text-gray-700',
                default => 'bg-amber-100 text-amber-800',
            };
            $statusLabel = match($permohonan->status) {
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'cancelled' => 'Dibatalkan',
                default => 'Menunggu Persetujuan',
            };
        @endphp
        <span class="inline-flex self-start rounded-full px-4 py-2 text-sm font-bold {{ $badge }}">{{ $statusLabel }}</span>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-sm font-black uppercase tracking-wide text-gray-900">Data Karyawan</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><div class="text-xs text-gray-500">Nama</div><div class="font-bold text-gray-900">{{ $permohonan->karyawan->nama_lengkap ?? '-' }}</div></div>
                    <div><div class="text-xs text-gray-500">NIK</div><div class="font-semibold text-gray-900">{{ $permohonan->karyawan->nik ?? '-' }}</div></div>
                    <div><div class="text-xs text-gray-500">Divisi</div><div class="font-semibold text-gray-900">{{ $permohonan->karyawan->divisi ?? '-' }}</div></div>
                    <div><div class="text-xs text-gray-500">Pekerjaan</div><div class="font-semibold text-gray-900">{{ $permohonan->karyawan->pekerjaan ?? '-' }}</div></div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-sm font-black uppercase tracking-wide text-gray-900">Detail Pinjaman</h2>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div><div class="text-xs text-gray-500">Nominal Diajukan</div><div class="text-xl font-black text-gray-900">Rp {{ number_format((float) $permohonan->nominal, 0, ',', '.') }}</div></div>
                    <div><div class="text-xs text-gray-500">Tenor Diajukan</div><div class="text-xl font-black text-gray-900">{{ $permohonan->tenor_bulan }} bulan</div></div>
                    <div><div class="text-xs text-gray-500">Estimasi Cicilan</div><div class="font-bold text-indigo-700">Rp {{ number_format((float) $permohonan->rencana_cicilan, 0, ',', '.') }} / bulan</div></div>
                    <div><div class="text-xs text-gray-500">Tanggal Dibutuhkan</div><div class="font-semibold text-gray-900">{{ $permohonan->tanggal_dibutuhkan?->format('d/m/Y') ?? '-' }}</div></div>
                    <div class="sm:col-span-2"><div class="text-xs text-gray-500">Tujuan Pinjaman</div><div class="mt-1 whitespace-pre-line text-sm text-gray-900">{{ $permohonan->tujuan_pinjaman }}</div></div>
                    @if($permohonan->catatan_pemohon)
                        <div class="sm:col-span-2"><div class="text-xs text-gray-500">Catatan Pemohon</div><div class="mt-1 whitespace-pre-line text-sm text-gray-900">{{ $permohonan->catatan_pemohon }}</div></div>
                    @endif
                </div>
            </div>

            @if($permohonan->status !== 'pending')
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-black uppercase tracking-wide text-gray-900">Hasil Persetujuan</h2>
                    @if($permohonan->status === 'approved')
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><div class="text-xs text-gray-500">Nominal Disetujui</div><div class="text-xl font-black text-emerald-700">Rp {{ number_format((float) $permohonan->nominal_disetujui, 0, ',', '.') }}</div></div>
                            <div><div class="text-xs text-gray-500">Tenor Disetujui</div><div class="text-xl font-black text-gray-900">{{ $permohonan->tenor_disetujui }} bulan</div></div>
                            <div><div class="text-xs text-gray-500">Disetujui Oleh</div><div class="font-semibold text-gray-900">{{ $permohonan->approver->name ?? $permohonan->approver->username ?? '-' }}</div></div>
                            <div><div class="text-xs text-gray-500">Waktu Persetujuan</div><div class="font-semibold text-gray-900">{{ $permohonan->approved_at?->format('d/m/Y H:i') ?? '-' }}</div></div>
                        </div>
                    @elseif($permohonan->status === 'rejected')
                        <div class="text-sm text-gray-700">Ditolak oleh <strong>{{ $permohonan->rejector->name ?? $permohonan->rejector->username ?? '-' }}</strong> pada {{ $permohonan->rejected_at?->format('d/m/Y H:i') ?? '-' }}.</div>
                    @endif
                    @if($permohonan->catatan_approval)
                        <div class="mt-4 rounded-lg bg-gray-50 p-4"><div class="text-xs font-bold uppercase text-gray-500">Catatan</div><div class="mt-1 whitespace-pre-line text-sm text-gray-800">{{ $permohonan->catatan_approval }}</div></div>
                    @endif
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-5">
                <div class="text-xs font-bold uppercase text-indigo-700">Saldo Utang Saat Ini</div>
                <div class="mt-1 text-2xl font-black text-indigo-900">Rp {{ number_format((float) ($permohonan->karyawan->saldoUtang->saldo ?? 0), 0, ',', '.') }}</div>
                @if($permohonan->saldo_recorded_at)
                    <div class="mt-2 text-xs text-indigo-700">Pinjaman ini telah dicatat ke saldo pada {{ $permohonan->saldo_recorded_at->format('d/m/Y H:i') }}.</div>
                @endif
            </div>

            @if($permohonan->status === 'pending')
                @can('permohonan-pinjaman-karyawan-approve')
                    <form action="{{ route('permohonan-pinjaman-karyawan.approve', $permohonan) }}" method="POST" class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm" onsubmit="return confirm('Setujui permohonan dan tambahkan ke saldo utang karyawan?')">
                        @csrf
                        <h3 class="mb-4 font-black text-emerald-800">Setujui Permohonan</h3>
                        <label class="mb-1 block text-xs font-bold text-gray-600">Nominal Disetujui</label>
                        <input type="number" name="nominal_disetujui" value="{{ old('nominal_disetujui', $permohonan->nominal) }}" min="1" max="{{ $permohonan->nominal }}" step="0.01" required class="mb-3 w-full rounded-lg border-gray-300 text-sm">
                        <label class="mb-1 block text-xs font-bold text-gray-600">Tenor Disetujui</label>
                        <input type="number" name="tenor_disetujui" value="{{ old('tenor_disetujui', $permohonan->tenor_bulan) }}" min="1" max="120" required class="mb-3 w-full rounded-lg border-gray-300 text-sm">
                        <label class="mb-1 block text-xs font-bold text-gray-600">Catatan</label>
                        <textarea name="catatan_approval" rows="3" class="mb-3 w-full rounded-lg border-gray-300 text-sm">{{ old('catatan_approval') }}</textarea>
                        <button class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">Setujui & Catat Saldo</button>
                    </form>

                    <form action="{{ route('permohonan-pinjaman-karyawan.reject', $permohonan) }}" method="POST" class="rounded-xl border border-red-100 bg-white p-5 shadow-sm" onsubmit="return confirm('Tolak permohonan pinjaman ini?')">
                        @csrf
                        <h3 class="mb-4 font-black text-red-800">Tolak Permohonan</h3>
                        <label class="mb-1 block text-xs font-bold text-gray-600">Alasan Penolakan</label>
                        <textarea name="catatan_approval" rows="3" required class="mb-3 w-full rounded-lg border-gray-300 text-sm"></textarea>
                        <button class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Tolak Permohonan</button>
                    </form>
                @endcan

                @if((int) $permohonan->created_by === (int) auth()->id() || auth()->user()?->can('permohonan-pinjaman-karyawan-approve'))
                    <form action="{{ route('permohonan-pinjaman-karyawan.cancel', $permohonan) }}" method="POST" onsubmit="return confirm('Batalkan permohonan ini?')">
                        @csrf
                        <button class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">Batalkan Permohonan</button>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
