@extends('layouts.app')

@section('title', 'Detail Stock Ban - ' . ($stockBan->nomor_seri ?? 'N/A'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Breadcrumb -->
    <nav class="flex mb-5" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('stock-ban.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">
                    <i class="fas fa-layer-group mr-2"></i> Stock Ban
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-300 text-xs mx-2"></i>
                    <span class="text-sm font-semibold text-indigo-600">Detail</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-2xl">
        <!-- Premium Header Section -->
        <div class="relative bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 px-6 py-10 sm:px-10 overflow-hidden">
            <!-- Abstract background shapes -->
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-48 h-48 bg-indigo-400 opacity-20 rounded-full blur-2xl"></div>

            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 shadow-inner">
                        <i class="fas fa-circle-notch text-white text-3xl sm:text-4xl animate-pulse"></i>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white border border-white/30 mb-2 uppercase tracking-widest">
                            Ban ID #{{ $stockBan->id }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                            {{ $stockBan->nomor_seri ?? 'N/A' }}
                        </h1>
                        <p class="text-indigo-100 text-sm font-medium flex items-center gap-2 mt-1">
                            <i class="fas fa-tag opacity-70"></i> {{ $stockBan->namaStockBan->nama ?? 'Stock Ban Luar' }}
                        </p>
                    </div>
                </div>
                
                <div class="flex flex-col items-start md:items-end gap-2">
                    <span class="px-4 py-1.5 rounded-xl text-xs font-black uppercase tracking-tighter shadow-lg
                        {{ $stockBan->status == 'Stok' ? 'bg-emerald-500 text-white' : 
                           ($stockBan->status == 'Terpakai' ? 'bg-purple-500 text-white' : 
                           ($stockBan->status == 'Sedang Dimasak' ? 'bg-orange-500 text-white' : 'bg-gray-500 text-white')) }}">
                        <i class="fas {{ $stockBan->status == 'Stok' ? 'fa-check-circle' : 'fa-info-circle' }} mr-1.5"></i>
                        {{ $stockBan->status }}
                    </span>
                    <div class="text-white/80 text-[10px] font-bold uppercase tracking-widest">Kondisi: {{ ucfirst($stockBan->kondisi) }}</div>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <!-- Left Column: Specs & Condition -->
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-8 h-[2px] bg-indigo-500"></span> Spesifikasi & Kondisi
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <!-- Merk -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 transition-hover hover:bg-white hover:shadow-md group">
                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-indigo-500 shadow-sm group-hover:bg-indigo-500 group-hover:text-white transition-all">
                                <i class="fas fa-copyright text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Merek</p>
                                <p class="text-sm font-black text-gray-800">{{ $stockBan->merk ?? ($stockBan->merkBan->nama ?? '-') }}</p>
                            </div>
                        </div>

                        <!-- Ukuran -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 transition-hover hover:bg-white hover:shadow-md group">
                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-blue-500 shadow-sm group-hover:bg-blue-500 group-hover:text-white transition-all">
                                <i class="fas fa-expand-arrows-alt text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Ukuran</p>
                                <p class="text-sm font-black text-gray-800">{{ $stockBan->ukuran ?? '-' }}</p>
                            </div>
                        </div>

                        <!-- Kondisi -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 transition-hover hover:bg-white hover:shadow-md group">
                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-100 flex items-center justify-center shadow-sm transition-all
                                {{ $stockBan->kondisi == 'asli' ? 'text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white' : 'text-amber-500 group-hover:bg-amber-500 group-hover:text-white' }}">
                                <i class="fas fa-shield-alt text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kondisi</p>
                                <p class="text-sm font-black text-gray-800">{{ ucfirst($stockBan->kondisi) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Location & Value -->
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-8 h-[2px] bg-indigo-500"></span> Lokasi & Nilai
                    </h3>

                    <div class="grid grid-cols-1 gap-4">
                        <!-- Lokasi -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 transition-hover hover:bg-white hover:shadow-md group">
                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-red-500 shadow-sm group-hover:bg-red-500 group-hover:text-white transition-all">
                                <i class="fas fa-map-marker-alt text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Lokasi / Posisi</p>
                                <p class="text-sm font-black text-gray-800">{{ $stockBan->lokasi ?? '-' }}</p>
                                @if($stockBan->mobil)
                                    <p class="text-[10px] font-bold text-blue-600 mt-0.5"><i class="fas fa-truck mr-1"></i> Unit: {{ $stockBan->mobil->nomor_polisi }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Harga -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 transition-hover hover:bg-white hover:shadow-md group">
                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-green-600 shadow-sm group-hover:bg-green-600 group-hover:text-white transition-all">
                                <i class="fas fa-money-bill-wave text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Harga Beli</p>
                                <p class="text-sm font-black text-gray-800">Rp {{ number_format($stockBan->harga_beli, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <!-- Supplier -->
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 transition-hover hover:bg-white hover:shadow-md group">
                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-purple-500 shadow-sm group-hover:bg-purple-500 group-hover:text-white transition-all">
                                <i class="fas fa-store text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tempat Beli / Supplier</p>
                                <p class="text-sm font-black text-gray-800">{{ $stockBan->tempat_beli ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time & History Cards -->
            <div class="bg-indigo-50/50 rounded-3xl p-6 sm:p-8 mb-10 border border-indigo-100">
                <h3 class="text-xs font-black text-indigo-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                    <i class="fas fa-history text-indigo-500"></i> Riwayat & Timeline
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-indigo-50">
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Tgl Masuk</p>
                        <div class="flex items-center">
                            <input type="date" 
                                value="{{ $stockBan->tanggal_masuk ? $stockBan->tanggal_masuk->format('Y-m-d') : '' }}" 
                                class="bg-transparent border-none p-0 text-sm font-black text-gray-800 focus:ring-0 date-history-editor w-full"
                                data-table="stock_bans"
                                data-id="{{ $stockBan->id }}"
                                data-field="tanggal_masuk">
                        </div>
                    </div>

                    @if($stockBan->tanggal_keluar)
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-indigo-50">
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Tgl Pasang</p>
                        <div class="flex items-center">
                            <input type="date" 
                                value="{{ $stockBan->tanggal_keluar ? $stockBan->tanggal_keluar->format('Y-m-d') : '' }}" 
                                class="bg-transparent border-none p-0 text-sm font-black text-gray-800 focus:ring-0 date-history-editor w-full"
                                data-table="stock_bans"
                                data-id="{{ $stockBan->id }}"
                                data-field="tanggal_keluar">
                        </div>
                    </div>
                    @endif

                    @if($stockBan->tanggal_digunakan)
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-indigo-50">
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Tgl Digunakan</p>
                        <div class="flex items-center">
                            <input type="date" 
                                value="{{ $stockBan->tanggal_digunakan ? $stockBan->tanggal_digunakan->format('Y-m-d') : '' }}" 
                                class="bg-transparent border-none p-0 text-sm font-black text-gray-800 focus:ring-0 date-history-editor w-full"
                                data-table="stock_bans"
                                data-id="{{ $stockBan->id }}"
                                data-field="tanggal_digunakan">
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Shipping Info (Conditional) -->
                @if($stockBan->status == 'Dikirim Ke Batam' || $stockBan->kapal)
                <div class="mt-6 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
                    <i class="fas fa-ship absolute right-[-10px] bottom-[-10px] text-7xl opacity-20 transform -rotate-12"></i>
                    <div class="relative">
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-80 mb-2">Informasi Pengiriman</p>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-vessel-tight"></i>
                                </div>
                                <span class="font-bold">{{ $stockBan->kapal->nama_kapal ?? 'Tanpa Nama Kapal' }}</span>
                            </div>
                            @if($stockBan->tanggal_kirim)
                            <div class="text-xs opacity-90"><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($stockBan->tanggal_kirim)->format('d M Y') }}</div>
                            @endif
                            @if($stockBan->penerima)
                            <div class="text-xs opacity-90"><i class="fas fa-user-check mr-1"></i> {{ $stockBan->penerima->nama_lengkap }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Notes & Additional Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                @if($stockBan->keterangan)
                <div class="md:col-span-2">
                    <div class="bg-amber-50 border border-amber-100 rounded-3xl p-6 h-full">
                        <h3 class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-sticky-note"></i> Catatan Khusus
                        </h3>
                        <p class="text-sm text-amber-900 leading-relaxed italic whitespace-pre-wrap">"{{ $stockBan->keterangan }}"</p>
                    </div>
                </div>
                @endif

                <div class="{{ $stockBan->keterangan ? 'md:col-span-1' : 'md:col-span-3' }}">
                    <div class="bg-gray-50 border border-gray-100 rounded-3xl p-6">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fas fa-fingerprint text-gray-500"></i> Audit Traill
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 mt-1.5 rounded-full bg-indigo-400"></div>
                                <div class="text-xs">
                                    <p class="text-gray-400 font-bold uppercase tracking-tighter">Dibuat</p>
                                    <p class="text-gray-700 font-black">{{ $stockBan->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="text-indigo-500 font-bold">{{ $stockBan->createdBy->name ?? 'System' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 mt-1.5 rounded-full bg-emerald-400"></div>
                                <div class="text-xs">
                                    <p class="text-gray-400 font-bold uppercase tracking-tighter">Update Terakhir</p>
                                    <p class="text-gray-700 font-black">{{ $stockBan->updated_at->format('d/m/Y H:i') }}</p>
                                    <p class="text-emerald-500 font-bold">{{ $stockBan->updatedBy->name ?? 'System' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Footer Actions -->
            <div class="flex flex-col sm:flex-row items-center gap-4 pt-8 border-t border-gray-100">
                <a href="{{ route('stock-ban.index') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-gray-200 text-sm font-black rounded-2xl text-gray-600 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95">
                    <i class="fas fa-arrow-left mr-2"></i> KEMBALI
                </a>

                <div class="flex w-full sm:w-auto gap-3 flex-1 sm:justify-end">
                    @can('stock-ban-update')
                        <a href="{{ route('stock-ban.edit', $stockBan->id) }}" 
                           class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-black rounded-2xl shadow-lg shadow-amber-200 text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 transition-all active:scale-95">
                            <i class="fas fa-edit mr-2"></i> EDIT
                        </a>
                    @endcan

                    @can('stock-ban-delete')
                        <form action="{{ route('stock-ban.destroy', $stockBan->id) }}" method="POST" class="flex-1 sm:flex-none" onsubmit="return confirm('Apakah Anda yakin ingin menghapus stock ban ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-black rounded-2xl shadow-lg shadow-red-200 text-white bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 transition-all active:scale-95">
                                <i class="fas fa-trash-alt mr-2"></i> HAPUS
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Success -->
@if(session('success'))
<div id="success-alert" class="fixed bottom-10 right-10 left-10 sm:left-auto z-50 transform transition-all duration-500 translate-y-20 opacity-0">
    <div class="bg-emerald-500 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4">
        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
            <i class="fas fa-check"></i>
        </div>
        <div>
            <p class="font-black text-sm uppercase tracking-widest">Berhasil!</p>
            <p class="text-xs opacity-90">{{ session('success') }}</p>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white/50 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show alert with animation
    const alert = document.getElementById('success-alert');
    if (alert) {
        setTimeout(() => {
            alert.classList.remove('translate-y-20', 'opacity-0');
        }, 100);
        setTimeout(() => {
            alert.classList.add('translate-y-20', 'opacity-0');
        }, 5000);
    }

    $('.date-history-editor').on('change', function() {
        const input = $(this);
        const sourceTable = input.data('table');
        const originalId = input.data('id');
        const field = input.data('field');
        const newDate = input.val();

        if (!newDate) return;

        if (!confirm('Apakah Anda yakin ingin memperbarui tanggal?')) {
            window.location.reload();
            return;
        }

        input.addClass('opacity-50 pointer-events-none');

        $.ajax({
            url: "{{ route('stock-ban.update-history-date') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                source_table: sourceTable,
                original_id: originalId,
                field: field,
                new_date: newDate
            },
            success: function(response) {
                if (response.success) {
                    // Show success notification instead of alert
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-20 right-5 z-[9999] bg-indigo-600 text-white px-6 py-3 rounded-xl shadow-2xl animate-bounce';
                    toast.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Tanggal diperbarui!';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                    input.removeClass('opacity-50 pointer-events-none');
                } else {
                    alert(response.message || 'Terjadi kesalahan saat memperbarui tanggal');
                    window.location.reload();
                }
            },
            error: function(xhr) {
                alert('Gagal menghubungi server.');
                window.location.reload();
            }
        });
    });
});
</script>
@endpush
