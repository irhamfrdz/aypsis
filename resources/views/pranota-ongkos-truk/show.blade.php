@extends('layouts.app')

@section('title', 'Detail Pranota Ongkos Truk')
@section('page_title', 'Detail Pranota ' . $pranota->no_pranota)

@section('content')
<div class="p-6">
    <div class="max-w-5xl mx-auto">
        <!-- Action Buttons -->
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('pranota-ongkos-truk.index') }}" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors font-bold text-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
            <div class="flex gap-3">
                <button onclick="window.print()" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-bold text-sm flex items-center gap-2">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden shadow-blue-500/5">
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-10 text-white relative">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <span class="bg-white/20 backdrop-blur-md text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full mb-3 inline-block">
                            Pranota Ongkos Truk
                        </span>
                        <h2 class="text-4xl font-black">{{ $pranota->no_pranota }}</h2>
                        <div class="mt-2 flex items-center text-blue-100 font-medium whitespace-nowrap overflow-hidden">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            {{ $pranota->tanggal_pranota->format('l, d F Y') }}
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end">
                        <div class="bg-white/10 p-4 rounded-2xl backdrop-blur-sm border border-white/10">
                            <span class="text-blue-100 text-xs font-bold uppercase tracking-wider block mb-1">Total Dibayarkan</span>
                            <span class="text-3xl font-black">Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <!-- Abstract Decor -->
                <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                    <i class="fas fa-file-invoice text-9xl transform rotate-12"></i>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                    <div>
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <span class="w-8 h-px bg-gray-200 mr-3"></span> Informasi Umum
                        </h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                                <span class="text-sm text-gray-500 font-medium">Dibuat Oleh</span>
                                <span class="text-sm text-gray-900 font-bold">{{ $pranota->creator->username ?? 'System' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-50">
                                <span class="text-sm text-gray-500 font-medium">Status</span>
                                <span class="px-3 py-0.5 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase">{{ $pranota->status }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-500 font-medium">Tanggal Input</span>
                                <span class="text-sm text-gray-900 font-bold">{{ $pranota->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                            <span class="w-8 h-px bg-gray-200 mr-3"></span> Catatan & Adjustment
                        </h4>
                        <div class="space-y-4">
                             <div class="bg-gray-50 rounded-2xl p-4 min-h-[60px] border border-gray-100 italic text-sm text-gray-600">
                                {{ $pranota->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                            </div>
                            @if($pranota->adjustment != 0)
                            <div class="flex justify-between items-center py-2 px-4 bg-orange-50 border border-orange-100 rounded-xl">
                                <span class="text-xs text-orange-700 font-bold uppercase tracking-wide">Adjustment Nilai</span>
                                <span class="text-sm font-black {{ $pranota->adjustment < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div>
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                        <span class="w-8 h-px bg-gray-200 mr-3"></span> Rincian Item Surat Jalan
                    </h4>
                    <div class="overflow-hidden border border-gray-100 rounded-3xl">
                        <table class="w-full">
                            <thead class="bg-gray-50/80">
                                <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">
                                    <th class="px-6 py-4 text-left">No. Surat Jalan</th>
                                    <th class="px-6 py-4 text-left">Tanggal SJ</th>
                                    <th class="px-6 py-4 text-left">Tipe</th>
                                    <th class="px-6 py-4 text-right">Nominal (Net)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($pranota->items as $item)
                                    <tr class="hover:bg-gray-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-black text-gray-900">{{ $item->no_surat_jalan }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/M/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 {{ $item->type == 'SuratJalan' ? 'bg-blue-50 text-blue-600' : 'bg-teal-50 text-teal-600' }} rounded text-[9px] font-black uppercase">
                                                {{ $item->type == 'SuratJalan' ? 'Reguler' : 'Bongkaran' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-gray-900 text-sm">
                                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50/50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Subtotal Items
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-gray-900">
                                        Rp {{ number_format($pranota->items->sum('nominal'), 0, ',', '.') }}
                                    </td>
                                </tr>
                                @if($pranota->adjustment != 0)
                                <tr class="bg-white">
                                    <td colspan="3" class="px-6 py-2 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Adjustment Value
                                    </td>
                                    <td class="px-6 py-2 text-right text-sm font-black {{ $pranota->adjustment < 0 ? 'text-red-500' : 'text-green-500' }}">
                                        {{ $pranota->adjustment < 0 ? '-' : '+' }} Rp {{ number_format(abs($pranota->adjustment), 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                                <tr class="bg-blue-50/30">
                                    <td colspan="3" class="px-6 py-6 text-right text-sm font-black text-blue-600 uppercase tracking-widest">
                                        Grand Total
                                    </td>
                                    <td class="px-6 py-6 text-right text-2xl font-black text-blue-800">
                                        Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        header, #sidebar, .mb-6, .max-w-5xl {
            margin: 0 !important;
            box-shadow: none !important;
        }
        .max-w-5xl {
            max-width: 100% !important;
        }
        button, a { display: none !important; }
        .bg-gradient-to-r {
            background: #eee !important;
            color: black !important;
        }
        .text-white { color: black !important; }
        .bg-white\/20, .bg-white\/10 { border: 1px solid #ccc !important; }
    }
</style>
@endsection
