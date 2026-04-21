@extends('layouts.app')

@section('title', 'Daftar Pembatalan Surat Jalan')
@section('page_title', 'Pembatalan Surat Jalan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Daftar Surat Jalan Dibukukan Batal</h1>
                <p class="mt-1 text-sm text-gray-600">Daftar riwayat transaksi pembatalan surat jalan</p>
            </div>
            <a href="{{ route('pembatalan-surat-jalan.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Pembatalan
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. SJ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl. Kas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Akhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan Batal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Batal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pembatalans as $pembatalan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $pembatalan->no_surat_jalan }}
                            <div class="mt-1">
                                @if(($pembatalan->tipe_sj ?? 'reguler') === 'reguler')
                                    <span class="px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-semibold uppercase border border-blue-100">Reguler</span>
                                @else
                                    <span class="px-1.5 py-0.5 rounded-full bg-purple-50 text-purple-600 text-[10px] font-semibold uppercase border border-purple-100">Bongkaran</span>
                                @endif
                                @if($pembatalan->nomor_accurate)
                                    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-semibold uppercase border border-amber-100">Synced</span>
                                @endif
                                @if($pembatalan->is_synced)
                                    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold uppercase border border-blue-200">
                                        Jurnal COA
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                            {{ $pembatalan->nomor_pembayaran ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                            {{ $pembatalan->bank ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                            {{ $pembatalan->tanggal_kas ? $pembatalan->tanggal_kas->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                            {{ $pembatalan->jenis_transaksi ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900 whitespace-nowrap">
                            Rp {{ number_format((float) ($pembatalan->total_tagihan_setelah_penyesuaian ?? 0), 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 max-w-xs truncate">
                            {{ $pembatalan->alasan_batal }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ ucfirst($pembatalan->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $pembatalan->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('pembatalan-surat-jalan.show', $pembatalan->id) }}" class="text-blue-600 hover:text-blue-900" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                
                                @if(!$pembatalan->is_synced)
                                <form action="{{ route('pembatalan-surat-jalan.sync-to-coa', $pembatalan->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900" title="Sinkronkan ke COA">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('pembatalan-surat-jalan.edit', $pembatalan->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada riwayat pembatalan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        @if($pembatalans->hasPages())
        <div class="mt-4">
            {{ $pembatalans->links() }}
        </div>
        @endif

    </div>
</div>
@endsection
