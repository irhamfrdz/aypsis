@extends('layouts.app')

@section('title', 'Permohonan Pinjaman Karyawan - AYPSIS')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Permohonan Pinjaman Karyawan</h1>
            <p class="mt-1 text-sm text-gray-500">Pengajuan, persetujuan, dan pencatatan saldo utang karyawan.</p>
        </div>
        @can('permohonan-pinjaman-karyawan-create')
            <a href="{{ route('permohonan-pinjaman-karyawan.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> Buat Permohonan
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-2 gap-3 md:grid-cols-4 mb-6">
        @php
            $cards = [
                'pending' => ['Menunggu', 'border-amber-100 bg-amber-50 text-amber-700', 'text-amber-900'],
                'approved' => ['Disetujui', 'border-emerald-100 bg-emerald-50 text-emerald-700', 'text-emerald-900'],
                'rejected' => ['Ditolak', 'border-red-100 bg-red-50 text-red-700', 'text-red-900'],
                'cancelled' => ['Dibatalkan', 'border-gray-200 bg-gray-50 text-gray-600', 'text-gray-900'],
            ];
        @endphp
        @foreach($cards as $status => [$label, $cardClasses, $numberClass])
            <a href="{{ route('permohonan-pinjaman-karyawan.index', ['status' => $status]) }}" class="rounded-xl border p-4 hover:shadow-sm {{ $cardClasses }}">
                <div class="text-xs font-bold uppercase tracking-wide">{{ $label }}</div>
                <div class="mt-1 text-2xl font-black {{ $numberClass }}">{{ $summary->get($status)?->jumlah ?? 0 }}</div>
                <div class="mt-1 text-xs">Rp {{ number_format((float) ($summary->get($status)?->total ?? 0), 0, ',', '.') }}</div>
            </a>
        @endforeach
    </div>

    <div class="mb-5 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('permohonan-pinjaman-karyawan.index') }}" class="grid gap-3 md:grid-cols-[1fr_220px_auto]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor, nama, atau NIK karyawan..." class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <select name="status" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="all">Semua status</option>
                @foreach($cards as $status => [$label, $cardClasses, $numberClass])
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Filter</button>
                <a href="{{ route('permohonan-pinjaman-karyawan.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase text-gray-500">Nomor / Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase text-gray-500">Karyawan</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase text-gray-500">Nominal</th>
                        <th class="px-5 py-3 text-center text-xs font-bold uppercase text-gray-500">Tenor</th>
                        <th class="px-5 py-3 text-center text-xs font-bold uppercase text-gray-500">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-bold uppercase text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($permohonans as $item)
                        @php
                            $badge = match($item->status) {
                                'approved' => 'bg-emerald-100 text-emerald-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-gray-100 text-gray-700',
                                default => 'bg-amber-100 text-amber-800',
                            };
                            $statusLabel = match($item->status) {
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                'cancelled' => 'Dibatalkan',
                                default => 'Menunggu',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $item->nomor_permohonan }}</div>
                                <div class="text-xs text-gray-500">{{ $item->tanggal_permohonan->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $item->karyawan->nama_lengkap ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->karyawan->nik ?? 'Tanpa NIK' }} &middot; {{ $item->karyawan->divisi ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="text-sm font-bold text-gray-900">Rp {{ number_format((float) $item->nominal, 0, ',', '.') }}</div>
                                @if($item->status === 'approved' && (float) $item->nominal_disetujui !== (float) $item->nominal)
                                    <div class="text-xs text-emerald-700">Disetujui Rp {{ number_format((float) $item->nominal_disetujui, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center text-sm text-gray-700">{{ $item->tenor_disetujui ?? $item->tenor_bulan }} bulan</td>
                            <td class="px-5 py-4 text-center"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $badge }}">{{ $statusLabel }}</span></td>
                            <td class="px-5 py-4 text-center">
                                <a href="{{ route('permohonan-pinjaman-karyawan.show', $item) }}" class="inline-flex rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-500">Belum ada permohonan pinjaman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($permohonans->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">{{ $permohonans->links() }}</div>
        @endif
    </div>
</div>
@endsection
