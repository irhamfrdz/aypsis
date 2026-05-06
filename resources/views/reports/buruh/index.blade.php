@extends('layouts.app')

@section('title', 'Laporan Biaya Buruh')
@section('page_title', 'Laporan Biaya Buruh')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Filter Section -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-8 transition-all duration-500 hover:shadow-2xl">
            <div class="p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight flex items-center">
                            <span class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-blue-200">
                                <i class="fas fa-file-invoice-dollar text-white"></i>
                            </span>
                            Laporan Biaya Buruh
                        </h1>
                        <p class="mt-2 text-gray-500 font-medium ml-16">Pantau distribusi pembayaran tenaga kerja per voyage</p>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button onclick="window.print()" class="inline-flex items-center px-5 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-2xl hover:bg-black transition-all duration-300 shadow-lg shadow-gray-200">
                            <i class="fas fa-print mr-2"></i> Cetak Laporan
                        </button>
                    </div>
                </div>

                <form action="{{ route('master.reports.buruh.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6 bg-gray-50 rounded-3xl border border-gray-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Dari Tanggal</label>
                        <div class="relative group">
                            <input type="date" name="start_date" value="{{ $startDate }}" 
                                   class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-300 font-bold text-gray-700 shadow-sm">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none opacity-0 group-focus-within:opacity-100 transition-opacity">
                                <i class="fas fa-calendar-alt text-blue-500"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Sampai Tanggal</label>
                        <div class="relative group">
                            <input type="date" name="end_date" value="{{ $endDate }}" 
                                   class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-300 font-bold text-gray-700 shadow-sm">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none opacity-0 group-focus-within:opacity-100 transition-opacity">
                                <i class="fas fa-calendar-alt text-blue-500"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama Buruh</label>
                        <select name="buruh_id" class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-300 font-bold text-gray-700 shadow-sm">
                            <option value="">Semua Buruh</option>
                            @foreach($allBuruhs as $b)
                                <option value="{{ $b->id }}" {{ request('buruh_id') == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full py-3.5 bg-blue-600 text-white text-sm font-black rounded-2xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 transition-all duration-300 shadow-xl shadow-blue-200 flex items-center justify-center group">
                            <i class="fas fa-filter mr-2 transform group-hover:scale-125 transition-transform"></i> Tampilkan Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mr-6">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Transaksi</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ number_format($reports->count()) }}</p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mr-6">
                    <i class="fas fa-money-bill-wave text-emerald-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Nominal</p>
                    <p class="text-3xl font-black text-emerald-600 mt-1">Rp {{ number_format($reports->sum('nominal'), 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100 flex items-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mr-6">
                    <i class="fas fa-ship text-amber-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Kapal Unik</p>
                    <p class="text-3xl font-black text-amber-600 mt-1">{{ number_format($reports->unique('kapal')->count()) }}</p>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-6 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                            <th class="px-8 py-6 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Kapal & Voyage</th>
                            <th class="px-8 py-6 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Nama Buruh</th>
                            <th class="px-8 py-6 text-right text-xs font-black text-gray-400 uppercase tracking-widest">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($reports as $report)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="text-sm font-bold text-gray-900">{{ $report->biayaKapal->tanggal->format('d M Y') }}</div>
                                <div class="text-[10px] font-black text-gray-400 uppercase mt-1 tracking-tighter">{{ $report->biayaKapal->tanggal->format('l') }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mr-3 group-hover:bg-amber-100 transition-colors">
                                        <i class="fas fa-ship text-amber-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $report->kapal }}</div>
                                        <div class="inline-block px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md text-[10px] font-black mt-1 uppercase tracking-widest border border-blue-100">
                                            {{ $report->voyage }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm font-bold text-gray-700 flex items-center">
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3 text-gray-400 text-xs">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    {{ $report->buruh->nama ?? 'N/A' }}
                                    @if($report->buruh && $report->buruh->nik)
                                        <span class="ml-2 text-[10px] font-medium text-gray-400">({{ $report->buruh->nik }})</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="text-base font-black text-gray-900 tracking-tight">Rp {{ number_format($report->nominal, 0, ',', '.') }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-32 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                        <i class="fas fa-folder-open text-gray-300 text-5xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">Data Tidak Ditemukan</h3>
                                    <p class="text-gray-500 font-medium max-w-sm">Coba sesuaikan range tanggal atau filter buruh untuk melihat hasil lainnya.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($reports->count() > 0)
                    <tfoot class="bg-gray-50/50">
                        <tr>
                            <td colspan="3" class="px-8 py-6 text-right text-sm font-black text-gray-500 uppercase tracking-widest">Total Keseluruhan</td>
                            <td class="px-8 py-6 text-right text-xl font-black text-blue-600 tracking-tighter">
                                Rp {{ number_format($reports->sum('nominal'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh {{ auth()->user()->name }}</p>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .max-w-7xl, .max-w-7xl * { visibility: visible; }
        .max-w-7xl { position: absolute; left: 0; top: 0; width: 100%; }
        button, form, .md\:grid-cols-3 { display: none !important; }
        .rounded-3xl, .shadow-xl { border-radius: 0; box-shadow: none; border: 1px solid #eee; }
        .bg-gray-50 { background: white !important; }
    }
</style>
@endsection
