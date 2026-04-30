@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Detail Pranota Uang Rit</h1>
            <p class="text-sm text-gray-500 font-medium">{{ $pranota->nomor_pranota }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pranota-uang-rit-batam.index') }}" 
               class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all flex items-center shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            @if($pranota->status_pembayaran != 'paid')
            <form action="{{ route('pranota-uang-rit-batam.destroy', $pranota) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pranota ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-sm font-bold hover:bg-red-100 transition-all flex items-center border border-red-100 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">Daftar Surat Jalan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">No. Surat Jalan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tujuan Ambil</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Uang Rit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pranota->suratJalanBatams as $sj)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-indigo-600">{{ $sj->no_surat_jalan }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $sj->tanggal_surat_jalan->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $sj->tujuan_pengambilan }}</td>
                                    <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">
                                        Rp {{ number_format($sj->pivot->uang_rit, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-sm font-bold text-gray-800 text-right">Subtotal</td>
                                <td class="px-6 py-4 text-sm font-black text-gray-900 text-right">
                                    Rp {{ number_format($pranota->total_rit, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            @if($pranota->catatan)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-2">Catatan</h3>
                <p class="text-gray-600 text-sm leading-relaxed">{{ $pranota->catatan }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Ringkasan Pembayaran</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500 font-medium">Supir</span>
                        <span class="text-sm font-bold text-gray-900">{{ $pranota->supir_nama }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500 font-medium">Tanggal</span>
                        <span class="text-sm font-bold text-gray-900">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500 font-medium">Status</span>
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $pranota->status_pembayaran == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $pranota->status_pembayaran }}
                        </span>
                    </div>
                    
                    <div class="pt-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Total Uang Rit</span>
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($pranota->total_rit, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Penyesuaian</span>
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($pranota->penyesuaian, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-100 mt-4">
                            <span class="text-lg font-black text-gray-900">Total Akhir</span>
                            <span class="text-xl font-black text-indigo-600">Rp {{ number_format($pranota->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 rounded-3xl shadow-xl p-6 text-white overflow-hidden relative">
                <div class="relative z-10">
                    <h3 class="font-bold text-gray-400 mb-1 text-xs uppercase tracking-widest">Metadata</h3>
                    <div class="space-y-2 mt-3">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-gray-500">Dibuat Oleh</span>
                            <span class="text-sm font-medium">{{ $pranota->creator->name ?? 'System' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] text-gray-500">Waktu Buat</span>
                            <span class="text-sm font-medium">{{ $pranota->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
                <!-- Abstract decoration -->
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-600 rounded-full blur-3xl opacity-20"></div>
            </div>
        </div>
    </div>
</div>
@endsection
