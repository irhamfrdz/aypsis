@extends('layouts.supir')

@section('title', 'Riwayat Cek Kendaraan - AYPSIS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('supir.dashboard') }}" class="p-2 bg-white hover:bg-gray-100 rounded-full transition-all border shadow-sm">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Pengecekan</h1>
        </div>
        <a href="{{ route('supir.cek-kendaraan.create') }}" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 hover:shadow-xl transition-all flex items-center">
            <i class="fas fa-plus mr-2"></i> Cek Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Tanggal & Jam</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Kendaraan</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Odometer</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($history as $row)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">{{ $row->tanggal->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $row->jam }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-indigo-600">{{ $row->mobil->nomor_polisi }}</div>
                            <div class="text-xs text-gray-500">{{ $row->mobil->merek }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-600">
                            {{ number_format($row->odometer, 0, ',', '.') }} <span class="text-[10px] text-gray-400 font-medium tracking-tighter">KM</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-[10px] font-black rounded-lg bg-green-50 text-green-600 border border-green-100 uppercase tracking-tighter">
                                Tersimpan
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('supir.cek-kendaraan.show', $row->id) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-bold text-sm bg-indigo-50 px-3 py-1.5 rounded-lg transition-all transform group-hover:scale-105">
                                <i class="fas fa-eye mr-1.5"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="bg-gray-50 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-clipboard-list text-gray-300 text-2xl"></i>
                            </div>
                            <p class="text-gray-400 font-medium">Belum ada riwayat pengecekan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($history->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $history->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
