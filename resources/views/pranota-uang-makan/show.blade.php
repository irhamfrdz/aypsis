@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    
    <!-- Page Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <!-- Left: Title & Back Button -->
        <div class="mb-4 sm:mb-0 flex items-center gap-4">
            <a href="{{ route('pranota-uang-makan.index') }}" 
               class="flex items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-slate-300 transition-all text-slate-500 hover:text-slate-700 shadow-sm"
               title="Kembali ke Daftar">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold tracking-tight">Detail Pranota Uang Makan</h1>
                <p class="text-sm text-slate-500 mt-1">Rincian uang makan karyawan berdasarkan kehadiran</p>
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center space-x-3">
            <!-- Print Button -->
            <button onclick="window.print()" 
                    class="btn bg-white border-slate-200 hover:border-slate-300 text-slate-600 shadow-sm transition-all flex items-center px-4 py-2 rounded-lg">
                <i class="fa-solid fa-print text-slate-400 mr-2"></i>
                <span class="font-medium">Cetak Pranota</span>
            </button>
            
            <!-- Delete Button -->
            <form action="{{ route('pranota-uang-makan.destroy', $pranota->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pranota ini? Tindakan ini tidak dapat dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="btn bg-rose-500 hover:bg-rose-600 text-white shadow-sm transition-all flex items-center px-4 py-2 rounded-lg">
                    <i class="fa-solid fa-trash-can mr-2 text-white/70"></i>
                    <span class="font-medium">Hapus</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Card: Nomor Pranota -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center transition-all hover:shadow-md">
            <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mr-5 shrink-0">
                <i class="fa-solid fa-file-invoice text-2xl"></i>
            </div>
            <div>
                <div class="text-sm font-semibold text-slate-500 mb-1 uppercase tracking-wider">Nomor Pranota</div>
                <div class="text-2xl font-bold text-slate-800">{{ $pranota->nomor_pranota }}</div>
            </div>
        </div>
        
        <!-- Card: Tanggal Pranota -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center transition-all hover:shadow-md">
            <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mr-5 shrink-0">
                <i class="fa-solid fa-calendar-day text-2xl"></i>
            </div>
            <div>
                <div class="text-sm font-semibold text-slate-500 mb-1 uppercase tracking-wider">Tanggal Pranota</div>
                <div class="text-2xl font-bold text-slate-800">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Details Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/80 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800 flex items-center">
                <i class="fa-solid fa-users text-slate-400 mr-2"></i>
                Rincian Karyawan
            </h2>
            <span class="bg-slate-200 text-slate-600 text-xs font-bold px-3 py-1 rounded-full">
                {{ $pranota->details->count() }} Data
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="text-xs font-semibold uppercase text-slate-500 bg-white border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">Karyawan</th>
                        <th class="px-6 py-4 whitespace-nowrap text-center">Kehadiran</th>
                        <th class="px-6 py-4 whitespace-nowrap text-right">Nominal Awal</th>
                        <th class="px-6 py-4 whitespace-nowrap text-right">Adjustment</th>
                        <th class="px-6 py-4 whitespace-nowrap">Catatan</th>
                        <th class="px-6 py-4 whitespace-nowrap text-right font-bold text-slate-700">Total Akhir</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($pranota->details as $detail)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center mr-3 text-sm font-bold border border-slate-200 group-hover:bg-blue-50 group-hover:text-blue-600 group-hover:border-blue-100 transition-colors">
                                        {{ substr($detail->karyawan->nama_lengkap ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $detail->karyawan->nama_lengkap ?? '-' }}</div>
                                        <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $detail->karyawan->nik ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $detail->kehadiran }} Hari
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-slate-600 font-medium">
                                Rp {{ number_format($detail->nominal_awal, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($detail->adjustment > 0)
                                    <span class="inline-flex items-center text-emerald-600 font-semibold bg-emerald-50 px-2 py-1 rounded text-xs border border-emerald-100">
                                        <i class="fa-solid fa-plus text-[10px] mr-1"></i> Rp {{ number_format($detail->adjustment, 0, ',', '.') }}
                                    </span>
                                @elseif($detail->adjustment < 0)
                                    <span class="inline-flex items-center text-rose-600 font-semibold bg-rose-50 px-2 py-1 rounded text-xs border border-rose-100">
                                        <i class="fa-solid fa-minus text-[10px] mr-1"></i> Rp {{ number_format(abs($detail->adjustment), 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-medium">Rp 0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm max-w-xs truncate" title="{{ $detail->catatan }}">
                                {{ $detail->catatan ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="font-bold text-slate-800 text-[15px]">
                                    Rp {{ number_format($detail->total_akhir, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-inbox text-2xl text-slate-400"></i>
                                    </div>
                                    <p class="font-medium text-slate-600">Tidak ada rincian data karyawan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-200 bg-slate-50/80">
                        <td colspan="5" class="px-6 py-5 text-right text-sm font-extrabold text-slate-700 tracking-wide uppercase">
                            Total Keseluruhan
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="text-xl font-black text-blue-600">
                                Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        body {
            background-color: white !important;
        }
        body * {
            visibility: hidden;
        }
        .max-w-9xl, .max-w-9xl * {
            visibility: visible;
        }
        .max-w-9xl {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0 !important;
        }
        .btn, form, a, .fa-arrow-left {
            display: none !important;
        }
        .shadow-sm, .shadow-md {
            box-shadow: none !important;
        }
        .border, .border-slate-200, .border-slate-100 {
            border-color: #e5e7eb !important;
        }
        .bg-white, .bg-slate-50, .bg-slate-50\/80, .bg-slate-100 {
            background-color: transparent !important;
        }
        /* Make sure table lines show up in print */
        table {
            border: 1px solid #e5e7eb !important;
        }
        th, td {
            border-bottom: 1px solid #e5e7eb !important;
        }
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('print') && urlParams.get('print') === 'true') {
            // Beri sedikit delay agar halaman render sepenuhnya sebelum print dialog muncul
            setTimeout(() => {
                window.print();
            }, 500);
        }
    });
</script>
@endpush
@endsection
