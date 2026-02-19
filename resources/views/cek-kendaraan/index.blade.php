@extends('layouts.app')

@section('title', 'Monitoring Cek Kendaraan - AYPSIS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center border border-indigo-100 shadow-sm">
                <i class="fas fa-clipboard-check text-indigo-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Monitoring Cek Kendaraan</h1>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Daftar semua inspeksi unit armada</p>
            </div>
        </div>
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

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal & Jam</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Supir</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kendaraan</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Odometer</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pernyataan</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($cekKendaraans as $row)
                    <tr class="hover:bg-gray-50/50 transition-all group">
                        <td class="px-8 py-6">
                            <div class="text-sm font-black text-gray-900">{{ $row->tanggal->format('d/m/Y') }}</div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tight mt-0.5">{{ $row->jam }}</div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3 text-gray-500">
                                    <i class="fas fa-user text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $row->karyawan ? $row->karyawan->nama_lengkap : 'N/A' }}</div>
                                    <div class="text-[10px] text-gray-400 font-medium">{{ $row->karyawan ? $row->karyawan->nik : '-' }}</div>
                                </div>
                            </div>
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
                            @if($row->pernyataan == 'layak')
                                <span class="inline-flex items-center px-3 py-1 text-[9px] font-black rounded-full bg-green-50 text-green-600 border border-green-100 uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 shadow-[0_0_5px_rgba(34,197,94,0.5)]"></span>
                                    Layak
                                </span>
                            @elseif($row->pernyataan == 'tidak_layak')
                                <span class="inline-flex items-center px-3 py-1 text-[9px] font-black rounded-full bg-red-50 text-red-600 border border-red-100 uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5 shadow-[0_0_5px_rgba(239,68,68,0.5)]"></span>
                                    Tidak Layak
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 text-[9px] font-black rounded-full bg-gray-50 text-gray-400 border border-gray-100 uppercase tracking-widest">
                                    -
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            <a href="{{ route('admin.cek-kendaraan.show', $row->id) }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-indigo-600 hover:text-white hover:border-indigo-600 hover:shadow-lg hover:shadow-indigo-100 transition-all active:scale-95">
                                <i class="fas fa-eye mr-2"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-gray-100 border-dashed">
                                <i class="fas fa-clipboard-list text-gray-300 text-2xl"></i>
                            </div>
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Belum ada riwayat pengecekan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden divide-y divide-gray-50">
            @forelse($cekKendaraans as $row)
            <div class="p-6 space-y-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center border border-indigo-100">
                            <i class="fas fa-truck text-indigo-600"></i>
                        </div>
                        <div>
                            <div class="text-xs font-black text-gray-900 tracking-tight">{{ $row->mobil->nomor_polisi }}</div>
                            <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $row->mobil->merek }}</div>
                        </div>
                    </div>
                    @if($row->pernyataan == 'layak')
                        <span class="inline-flex items-center px-2 py-0.5 text-[8px] font-black rounded-full bg-green-50 text-green-600 border border-green-100 uppercase tracking-widest text-center truncate">
                            Layak
                        </span>
                    @elseif($row->pernyataan == 'tidak_layak')
                        <span class="inline-flex items-center px-2 py-0.5 text-[8px] font-black rounded-full bg-red-50 text-red-600 border border-red-100 uppercase tracking-widest text-center truncate">
                            Tidak Layak
                        </span>
                    @endif
                </div>
                
                <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center mr-3 text-gray-400 shadow-sm">
                        <i class="fas fa-user text-xs"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-900">{{ $row->karyawan ? $row->karyawan->nama_lengkap : 'N/A' }}</div>
                        <div class="text-[9px] text-gray-400 uppercase tracking-widest mt-0.5">Supir</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Odometer</div>
                        <div class="text-xs font-black text-gray-700">{{ number_format($row->odometer, 0, ',', '.') }} KM</div>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                        <div class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Waktu Cek</div>
                        <div class="text-xs font-black text-gray-700">{{ $row->tanggal->format('d/m/Y') }}</div>
                    </div>
                </div>

                <a href="{{ route('admin.cek-kendaraan.show', $row->id) }}" class="flex items-center justify-center w-full py-4 bg-gray-50 border border-gray-200 text-gray-700 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-indigo-600 hover:text-white transition-all active:scale-95 shadow-sm">
                    <i class="fas fa-eye mr-2"></i> Lihat Detail Lengkap
                </a>
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-gray-100 border-dashed">
                    <i class="fas fa-clipboard-list text-gray-300 text-xl"></i>
                </div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Belum ada riwayat</p>
            </div>
            @endforelse
        </div>

        @if($cekKendaraans->hasPages())
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                {{ $cekKendaraans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
