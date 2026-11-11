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

    @if(session('success'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
        {{ session('success') }}
    </div>
    @endif

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
                    @can('master-kartu-bensin-batam-edit')
                    <button type="button" id="open-topup-modal" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2 text-sm font-semibold text-white hover:bg-green-600">
                        <i class="fas fa-plus-circle"></i> Top Up Saldo
                    </button>
                    @endcan
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

    @can('master-kartu-bensin-batam-edit')
    <div id="topup-modal" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="topup-modal-title">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between">
                <h2 id="topup-modal-title" class="text-lg font-bold text-gray-800">Top Up Saldo</h2>
                <button type="button" data-close-topup class="text-gray-500 hover:text-gray-800" aria-label="Tutup formulir top up"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('master-kartu-bensin-batam.topup', $card->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="topup-tanggal" class="mb-1 block text-sm font-medium text-gray-700">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" id="topup-tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                    @error('tanggal')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="topup-nominal" class="mb-1 block text-sm font-medium text-gray-700">Nominal (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="topup-nominal" name="nominal" value="{{ old('nominal') }}" min="1" step="1" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" placeholder="Masukkan nominal top up">
                    @error('nominal')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="topup-keterangan" class="mb-1 block text-sm font-medium text-gray-700">Keterangan <span class="text-red-500">*</span></label>
                    <textarea id="topup-keterangan" name="keterangan" rows="3" maxlength="1000" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" placeholder="Masukkan keterangan top up">{{ old('keterangan') }}</textarea>
                    @error('keterangan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close-topup class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Simpan Top Up</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

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
                        <th class="px-6 py-4">No. Transaksi</th>
                        <th class="px-6 py-4">Tipe Aksi</th>
                        <th class="px-6 py-4">Nominal</th>
                        <th class="px-6 py-4">Saldo Sebelum</th>
                        <th class="px-6 py-4">Saldo Sesudah</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4">Diinput Oleh</th>
                        @can('master-kartu-bensin-batam-edit')
                        <th class="px-6 py-4">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($histories as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-600">
                            {{ $log->tanggal ? $log->tanggal->translatedFormat('d F Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-blue-700">
                            {{ $log->nomor_transaksi ?? $log->legacy_nomor_transaksi ?? '-' }}
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
                        @can('master-kartu-bensin-batam-edit')
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('master-kartu-bensin-batam.history.edit', [$card->id, $log->id]) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800" aria-label="Edit riwayat {{ $log->nomor_transaksi ?? $log->id }}">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                        @endcan
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->can('master-kartu-bensin-batam-edit') ? 9 : 8 }}" class="px-6 py-12 text-center text-gray-500">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('topup-modal');
        const openButton = document.getElementById('open-topup-modal');
        if (!modal || !openButton) return;

        function closeModal() { modal.classList.add('hidden'); }
        openButton.addEventListener('click', function () {
            modal.classList.remove('hidden');
            document.getElementById('topup-tanggal').focus();
        });
        modal.querySelectorAll('[data-close-topup]').forEach(button => button.addEventListener('click', closeModal));
        modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
        document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });
    });
</script>
@endpush
