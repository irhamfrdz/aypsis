@extends('layouts.app')

@section('title', 'Detail Permohonan')
@section('page_title', 'Detail Memo: ' . $permohonan->nomor_memo)

@section('content')
<div class="space-y-8">
    {{-- Detail Permohonan --}}
    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Permohonan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div>
                <p class="font-medium text-gray-500">Nomor Memo</p>
                <p>{{ $permohonan->nomor_memo }}</p>
            </div>
            <div>
                <p class="font-medium text-gray-500">Tanggal Memo</p>
                <p>{{ \Carbon\Carbon::parse($permohonan->tanggal_memo)->format('d F Y') }}</p>
            </div>
            <div>
                <p class="font-medium text-gray-500">Kegiatan</p>
                <p>{{ ucfirst($permohonan->kegiatan) }}</p>
            </div>
            <div>
                <p class="font-medium text-gray-500">Supir</p>
                <p>{{ $permohonan->supir->nama_panggilan ?? '-' }} ({{ $permohonan->plat_nomor }})</p>
            </div>
            <div>
                <p class="font-medium text-gray-500">Dari - Ke</p>
                <p>{{ $permohonan->dari ?? '-' }} - {{ $permohonan->ke ?? '-' }}</p>
            </div>
            <div>
                <p class="font-medium text-gray-500">Uang Jalan</p>
                <p>Rp. {{ number_format($permohonan->jumlah_uang_jalan, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="font-medium text-gray-500">Adjustment</p>
                <p>{{ $permohonan->adjustment ? 'Rp. ' . number_format($permohonan->adjustment, 0, ',', '.') : '-' }}</p>
            </div>
            <div>
                <p class="font-medium text-gray-500">Alasan Adjustment</p>
                <p>{{ $permohonan->alasan_adjustment ?? '-' }}</p>
            </div>
            <div>
                <p class="font-medium text-gray-500">Total Biaya</p>
                <p>Rp. {{ number_format($permohonan->total_harga_setelah_adj, 0, ',', '.') }}</p>
            </div>
            {{-- REVISI: Tampilkan daftar nomor kontainer --}}
            <div class="md:col-span-3">
                <p class="font-medium text-gray-500">Nomor Kontainer</p>
                <div class="flex flex-wrap gap-2 mt-1">
                    @forelse($permohonan->kontainers as $kontainer)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800">
                            {{ $kontainer->nomor_seri_gabungan }}
                        </span>
                    @empty
                        <span class="text-gray-500">- Belum ada nomor kontainer yang diinput -</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Checkpoint --}}
    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Checkpoint</h3>
        <div class="space-y-6">
            @forelse($permohonan->checkpoints as $checkpoint)
                <div class="relative flex items-start">
                    <div class="absolute left-0 top-0 h-full w-px bg-gray-300 ml-3"></div>
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $loop->first ? 'bg-indigo-500' : 'bg-gray-300' }}">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-800">{{ $checkpoint->catatan }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $checkpoint->created_at->format('d M Y, H:i') }} - {{ $checkpoint->lokasi ?? 'Lokasi tidak diketahui' }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada riwayat checkpoint untuk permohonan ini.</p>
            @endforelse
        </div>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('permohonan.index') }}" class="inline-flex justify-center py-2 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Kembali
        </a>
    </div>
</div>
@endsection
