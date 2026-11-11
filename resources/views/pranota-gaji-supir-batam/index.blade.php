@extends('layouts.app')

@section('title', 'Pranota Gaji Supir Batam')
@section('page_title', 'Pranota Gaji Supir Batam')

@include('pranota-gaji-supir-batam.partials.styles')

@section('content')
<div class="pg-shell">
    <div class="pg-header">
        <div>
            <p class="pg-eyebrow">Payroll / Supir Batam</p>
            <h1>Pranota Gaji Supir Batam</h1>
            <p class="pg-subtitle">Kelola pranota dan rincian gaji untuk pembayaran.</p>
        </div>
        @can('pranota-gaji-supir-batam-create')
            <a href="{{ route('pranota-gaji-supir-batam.create') }}" class="pg-btn pg-btn-primary"><i class="fas fa-plus" aria-hidden="true"></i>Buat Pranota</a>
        @endcan
    </div>
    @if(session('success'))<div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    <div class="pg-card">
        <div class="pg-card-heading"><h2>Daftar Pranota</h2><p>{{ number_format($pranotas->total(), 0, ',', '.') }} pranota</p></div>
        <div class="pg-table-scroll" tabindex="0" role="region" aria-label="Daftar pranota gaji supir Batam">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-600"><tr>
                <th class="px-4 py-3">Nomor Pranota</th><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Periode Gaji</th><th class="px-4 py-3 text-center">Jumlah Gaji</th><th class="px-4 py-3 text-right">Total Bersih</th><th class="px-4 py-3">Keterangan</th><th class="px-4 py-3 text-right">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pranotas as $pranota)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3"><a href="{{ route('pranota-gaji-supir-batam.show', $pranota) }}" class="pg-link">{{ $pranota->nomor_pranota }}</a></td>
                        <td class="whitespace-nowrap px-4 py-3">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</td>
                        @php
                            $tanggalMulai = $pranota->items->pluck('gaji.tanggal_mulai')->filter()->sort()->first();
                            $tanggalSelesai = $pranota->items->pluck('gaji.tanggal_selesai')->filter()->sort()->last();
                        @endphp
                        <td class="whitespace-nowrap px-4 py-3">{{ $tanggalMulai && $tanggalSelesai ? $tanggalMulai->format('d/m/Y').' - '.$tanggalSelesai->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3 text-center">{{ $pranota->items_count }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format((float) $pranota->total_gaji, 0, ',', '.') }}</td>
                        <td class="max-w-xs px-4 py-3">{{ $pranota->keterangan ?: '-' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <div class="pg-actions">
                            <a href="{{ route('pranota-gaji-supir-batam.show', $pranota) }}" class="pg-link">Rincian</a>
                            <a href="{{ route('pranota-gaji-supir-batam.print', $pranota) }}" target="_blank" rel="noopener" class="text-gray-600 hover:underline">Cetak</a>
                            @can('pranota-gaji-supir-batam-delete')
                                <form action="{{ route('pranota-gaji-supir-batam.destroy', $pranota) }}" method="POST" onsubmit="return confirm('Hapus pranota ini? Gaji yang belum dibayar akan tersedia kembali.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="pg-danger hover:underline">Hapus</button>
                                </form>
                            @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="pg-empty">Belum ada pranota gaji supir Batam.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
    <div class="pg-pagination">{{ $pranotas->links() }}</div>
</div>
@endsection
