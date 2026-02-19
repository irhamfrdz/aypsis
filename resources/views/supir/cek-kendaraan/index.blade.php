@extends('layouts.supir')

@section('title', 'Riwayat Cek Kendaraan - AYPSIS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center border border-indigo-100 shadow-sm">
                <i class="fas fa-history text-indigo-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Riwayat Pengecekan</h1>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Daftar inspeksi unit armada terakhir</p>
            </div>
        </div>
        <a href="{{ route('supir.cek-kendaraan.create') }}" class="w-full sm:w-auto px-6 py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:shadow-2xl hover:bg-indigo-700 transition-all duration-300 flex items-center justify-center uppercase tracking-widest text-xs">
            <i class="fas fa-plus mr-3"></i> Buat Cek Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl flex items-center animate-pulse-subtle">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-check-circle"></i>
            </div>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex items-center">
            <span class="w-1.5 h-6 bg-indigo-600 rounded-full mr-3"></span>
            <h2 class="text-lg font-black text-gray-900">Data Inspeksi</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal & Jam</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kendaraan</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Odometer</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($history as $row)
                    <tr class="hover:bg-gray-50/50 transition-all group">
                        <td class="px-8 py-6">
                            <div class="text-sm font-black text-gray-900">{{ $row->tanggal->format('d/m/Y') }}</div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tight mt-0.5">{{ $row->jam }}</div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-black border border-indigo-100 mb-1">
                                {{ $row->mobil->nomor_polisi }}
                            </div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">{{ $row->mobil->merek }}</div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-sm font-black text-gray-600">
                                {{ number_format($row->odometer, 0, ',', '.') }} <span class="text-[9px] text-gray-400 font-bold uppercase ml-0.5 tracking-tighter">KM</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center px-3 py-1 text-[9px] font-black rounded-full bg-green-50 text-green-600 border border-green-100 uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 shadow-[0_0_5px_rgba(34,197,94,0.5)]"></span>
                                Tersimpan
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <a href="{{ route('supir.cek-kendaraan.show', $row->id) }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-indigo-600 hover:text-white hover:border-indigo-600 hover:shadow-lg hover:shadow-indigo-100 transition-all active:scale-95">
                                <i class="fas fa-eye mr-2"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-gray-100 border-dashed">
                                <i class="fas fa-clipboard-list text-gray-300 text-3xl"></i>
                            </div>
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Belum ada riwayat pengecekan</p>
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
