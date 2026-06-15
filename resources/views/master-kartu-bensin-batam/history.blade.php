@extends('layouts.app')

@section('title', 'Riwayat Penggunaan Kartu Bensin Batam')
@section('page_title', 'Riwayat Kartu Bensin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Link -->
    <div class="mb-4">
        <a href="{{ route('master-kartu-bensin-batam.index') }}" class="text-sm text-blue-600 hover:text-blue-900 flex items-center gap-2 font-medium">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kartu
        </a>
    </div>

    <!-- Card Info Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-800 to-indigo-900 px-6 py-5 text-white">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-1 bg-blue-700/50 text-blue-100 rounded-md font-semibold text-xs border border-blue-600/50 uppercase tracking-wider">
                        Detail Kartu Bensin
                    </span>
                    <h3 class="text-2xl font-bold mt-2">{{ $card->nama_kartu }}</h3>
                    <p class="text-sm text-blue-200 mt-1 flex items-center gap-2">
                        <i class="fas fa-credit-card"></i> {{ $card->nomor_kartu }}
                        <span class="text-blue-400">|</span>
                        <i class="fas fa-building"></i> {{ $card->provider }}
                    </p>
                </div>
                <div class="text-right md:border-l md:border-white/10 md:pl-6">
                    <p class="text-xs text-blue-200 uppercase font-semibold tracking-wider">Saldo Saat Ini</p>
                    <div class="text-3xl font-extrabold mt-1 text-green-400">
                        Rp {{ number_format($card->saldo ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 flex items-center justify-end gap-2 text-xs">
                        @if($card->status == 'aktif')
                        <span class="px-2 py-0.5 bg-green-500/20 text-green-300 border border-green-500/30 rounded-full font-semibold">
                            Aktif
                        </span>
                        @else
                        <span class="px-2 py-0.5 bg-red-500/20 text-red-300 border border-red-500/30 rounded-full font-semibold">
                            Tidak Aktif
                        </span>
                        @endif

                        @if($card->mobil)
                        <span class="px-2 py-0.5 bg-white/10 text-white border border-white/20 rounded-md font-medium">
                            <i class="fas fa-truck mr-1 text-xs"></i> {{ $card->mobil->nomor_polisi }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-base font-bold text-gray-800">Log Riwayat Saldo</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar lengkap transaksi, penambahan, dan pengurangan saldo kartu.</p>
            </div>
            <button onclick="window.print()" class="px-3 py-1.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-sm transition">
                <i class="fas fa-print"></i> Cetak Riwayat
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Tanggal & Waktu</th>
                        <th class="px-6 py-4">Tipe Aksi</th>
                        <th class="px-6 py-4">Nominal</th>
                        <th class="px-6 py-4">Saldo Sebelum</th>
                        <th class="px-6 py-4">Saldo Sesudah</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4">Diinput Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($histories as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-600">
                            {{ $log->tanggal ? $log->tanggal->translatedFormat('d F Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->tipe == 'bertambah')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold border border-green-200">
                                <i class="fas fa-plus-circle text-[10px]"></i> Bertambah
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold border border-red-200">
                                <i class="fas fa-minus-circle text-[10px]"></i> Berkurang
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
                            @if($log->tipe == 'bertambah')
                            <span class="text-green-600">+ Rp {{ number_format($log->nominal, 0, ',', '.') }}</span>
                            @else
                            <span class="text-red-600">- Rp {{ number_format($log->nominal, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            Rp {{ number_format($log->saldo_sebelum, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-800">
                            Rp {{ number_format($log->saldo_sesudah, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 max-w-sm">
                            {{ $log->keterangan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-user-circle text-gray-400"></i>
                                <span>{{ $log->createdBy ? $log->createdBy->name : 'System' }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-history text-3xl mb-3 block text-gray-300"></i>
                            Belum ada riwayat perubahan saldo untuk kartu bensin ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($histories->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $histories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
