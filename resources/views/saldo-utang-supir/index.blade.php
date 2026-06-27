@extends('layouts.app')

@section('title', 'Saldo Utang Supir - AYPSIS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center border border-indigo-100 shadow-sm">
                <i class="fas fa-hand-holding-usd text-indigo-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Saldo Utang Supir</h1>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Kelola dan pantau saldo pinjaman & kasbon armada supir</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('saldo-utang-supir.import') }}" class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl transition-all duration-200 text-xs flex items-center gap-2 uppercase tracking-wider">
                <i class="fas fa-file-import"></i> Import Saldo Awal
            </a>
            <a href="{{ route('saldo-utang-supir.create') }}" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 transition-all duration-200 text-xs flex items-center gap-2 uppercase tracking-wider">
                <i class="fas fa-plus-circle"></i> Tambah Transaksi
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center">
            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-check-circle"></i>
            </div>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Alert Warning for Unmatched Rows -->
    @if(session('warning_data'))
        <div class="mb-6 p-6 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl shadow-sm">
            <div class="flex items-center mb-3">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center mr-3 text-amber-700">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <span class="font-black text-sm">Ada data supir yang tidak cocok (tidak ditemukan di database):</span>
            </div>
            <div class="overflow-x-auto max-h-48 overflow-y-auto mt-2 rounded-lg border border-amber-100">
                <table class="w-full text-left text-xs text-amber-800">
                    <thead class="bg-amber-100/50">
                        <tr>
                            <th class="px-4 py-2 font-bold">NIK</th>
                            <th class="px-4 py-2 font-bold">Nama CSV</th>
                            <th class="px-4 py-2 font-bold">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-100/50">
                        @foreach(session('warning_data') as $row)
                            <tr>
                                <td class="px-4 py-1.5 font-mono">{{ $row['nik'] ?: '-' }}</td>
                                <td class="px-4 py-1.5 font-semibold">{{ $row['nama'] }}</td>
                                <td class="px-4 py-1.5 font-mono">Rp {{ $row['saldo'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl flex items-center">
            <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Search Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <form action="{{ route('saldo-utang-supir.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-xs"></i>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari supir berdasarkan nama atau NIK..." 
                       class="block w-full pl-9 pr-4 py-2.5 text-xs text-gray-900 placeholder-gray-400 bg-gray-50 border border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition-colors duration-200">
                    Filter
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('saldo-utang-supir.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs uppercase tracking-wider transition-colors duration-200">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Grid -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/75 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">NIK</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Supir</th>
                        <th class="px-6 py-4 class text-[10px] font-black text-gray-400 uppercase tracking-widest">Jabatan / Pekerjaan</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Saldo Utang Aktif</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($supirs as $supir)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-xs font-semibold text-gray-700">
                                {{ $supir->nik ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                        {{ strtoupper(substr($supir->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-black text-gray-900">{{ strtoupper($supir->nama_lengkap) }}</div>
                                        <div class="text-[9px] text-gray-400">Panggilan: {{ $supir->nama_panggilan ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                <span class="px-2 py-1 rounded bg-slate-100 text-slate-700 text-[10px] font-bold uppercase tracking-wider">
                                    {{ $supir->pekerjaan ?? 'Supir' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-xs font-bold">
                                @php
                                    $saldo = $supir->saldoUtang ? $supir->saldoUtang->saldo : 0;
                                @endphp
                                @if($saldo > 0)
                                    <span class="text-rose-600 font-black">Rp {{ number_format($saldo, 0, ',', '.') }}</span>
                                @elseif($saldo < 0)
                                    <span class="text-emerald-600 font-black">Rp {{ number_format(abs($saldo), 0, ',', '.') }} (Lebih Bayar)</span>
                                @else
                                    <span class="text-gray-400 font-medium">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('saldo-utang-supir.show', $supir->id) }}" 
                                   class="inline-flex items-center justify-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-black rounded-lg text-[10px] uppercase tracking-wider transition-colors duration-200 gap-1.5">
                                    <i class="fas fa-history text-[9px]"></i> Riwayat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center border border-gray-100 mb-3">
                                        <i class="fas fa-user-slash text-gray-400"></i>
                                    </div>
                                    <span class="text-xs text-gray-400 font-semibold">Tidak ada data supir ditemukan</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($supirs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $supirs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
