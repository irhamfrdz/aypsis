@extends('layouts.app')

@section('title', 'Riwayat Utang ' . strtoupper($supir->nama_lengkap) . ' - AYPSIS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button & Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('saldo-utang-supir.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-900 shadow-sm transition-all duration-200">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight">Detail Riwayat Utang</h1>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ strtoupper($supir->nama_lengkap) }} ({{ $supir->nik ?? 'TANPA NIK' }})</p>
            </div>
        </div>
        
        <!-- Big Balance Display -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4 flex items-center space-x-4">
            <div class="w-10 h-10 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600">
                <i class="fas fa-wallet text-sm"></i>
            </div>
            <div>
                <div class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Saldo Utang Saat Ini</div>
                <div class="text-lg font-black text-rose-600 font-mono">
                    @php
                        $saldo = $supir->saldoUtang ? $supir->saldoUtang->saldo : 0;
                    @endphp
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xs font-black text-gray-900 uppercase tracking-wider">Mutasi Rekening Utang</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/75 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Referensi</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Jenis Transaksi</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Nominal</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayat as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-xs text-gray-600">
                                {{ $item->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-950">
                                {{ $item->referensi }}
                            </td>
                            <td class="px-6 py-4">
                                @if($item->tipe === 'penambahan')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100 uppercase tracking-wider">
                                        Pinjaman / Kasbon
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wider">
                                        Pembayaran / Potongan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-xs font-bold">
                                @if($item->tipe === 'penambahan')
                                    <span class="text-rose-600">+Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-emerald-600">-Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-400">
                                {{ $item->keterangan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center border border-gray-100 mb-3">
                                        <i class="fas fa-file-invoice text-gray-400"></i>
                                    </div>
                                    <span class="text-xs text-gray-400 font-semibold">Belum ada riwayat transaksi utang</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
