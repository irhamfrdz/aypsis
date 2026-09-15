@extends('layouts.app')

@section('title', 'Broadcast WhatsApp')
@section('page_title', 'Broadcast WhatsApp')

@section('content')
<div class="space-y-5 font-sans max-w-full">

    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-sm flex-shrink-0">
                <i class="fab fa-whatsapp text-2xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-800 tracking-tight">Riwayat Broadcast WhatsApp</h1>
                <p class="text-xs text-slate-400 mt-0.5">Kelola dan kirim ulang broadcast pesan ke shipper</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('master.wa-gateway.index') }}" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl transition-all shadow-sm">
                <i class="fas fa-wifi mr-1.5"></i>
                Status Koneksi WA
            </a>
            @can('master-wa-broadcast-create')
            <a href="{{ route('master.wa-broadcast.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow transition-all">
                <i class="fas fa-plus mr-1.5"></i>
                Buat Broadcast
            </a>
            @endcan
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-xl shadow-sm text-sm">
            <i class="fas fa-check-circle text-emerald-500 text-base flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3.5 rounded-xl shadow-sm text-sm">
            <i class="fas fa-times-circle text-rose-500 text-base flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Stats Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-400 font-medium">Total Broadcast</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $broadcasts->count() }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-400 font-medium">Total Shipper Dikirim</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $broadcasts->sum('total_shipper') }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm col-span-2 sm:col-span-1">
            <p class="text-xs text-slate-400 font-medium">Broadcast Terbaru</p>
            <p class="text-sm font-bold text-slate-800 mt-1 truncate">
                {{ $broadcasts->first() ? \Carbon\Carbon::parse($broadcasts->first()->created_at)->diffForHumans() : '-' }}
            </p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table id="dataTable" class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        <th class="px-5 py-3.5 text-left w-10">#</th>
                        <th class="px-5 py-3.5 text-left">Tanggal</th>
                        <th class="px-5 py-3.5 text-left">Kapal & Voyage</th>
                        <th class="px-5 py-3.5 text-left">Kendala</th>
                        <th class="px-5 py-3.5 text-left">Template WA</th>
                        <th class="px-5 py-3.5 text-center">Shipper</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($broadcasts as $index => $broadcast)
                    <tr class="hover:bg-slate-50/70 transition-colors group">

                        {{-- No --}}
                        <td class="px-5 py-3.5 text-slate-400 text-xs font-medium">{{ $index + 1 }}</td>

                        {{-- Tanggal --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="text-xs font-semibold text-slate-700">{{ \Carbon\Carbon::parse($broadcast->created_at)->format('d M Y') }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($broadcast->created_at)->format('H:i') }} WIB</div>
                        </td>

                        {{-- Kapal & Voyage --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-500 flex-shrink-0">
                                    <i class="fas fa-ship text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-800 leading-tight">{{ $broadcast->nama_kapal }}</div>
                                    <div class="text-[11px] text-slate-400">Voy. {{ $broadcast->no_voyage }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Kendala --}}
                        <td class="px-5 py-3.5 max-w-xs">
                            @if($broadcast->kategori_masalah)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    {{ $broadcast->kategori_masalah }}
                                </span>
                            @endif
                            @if($broadcast->deskripsi_masalah)
                                <div class="text-[11px] text-slate-400 mt-1 line-clamp-1">{{ Str::limit($broadcast->deskripsi_masalah, 40) }}</div>
                            @endif
                            @if(!$broadcast->kategori_masalah && !$broadcast->deskripsi_masalah)
                                <span class="text-slate-300 italic text-xs">—</span>
                            @endif
                        </td>

                        {{-- Template WA --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($broadcast->template)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-violet-50 text-violet-700 border border-violet-200">
                                    <i class="fas fa-file-alt mr-1 text-violet-400"></i>
                                    {{ $broadcast->template->nama_template }}
                                </span>
                            @else
                                <span class="text-slate-300 italic text-xs">Terhapus</span>
                            @endif
                        </td>

                        {{-- Total Shipper --}}
                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fas fa-users mr-1 text-emerald-400 text-[10px]"></i>
                                {{ $broadcast->total_shipper }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-5 py-3.5 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('report.manifests.broadcast-preview') }}" method="POST" target="_blank" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="nama_kapal" value="{{ $broadcast->nama_kapal }}">
                                    <input type="hidden" name="no_voyage" value="{{ $broadcast->no_voyage }}">
                                    <input type="hidden" name="kategori_masalah" value="{{ $broadcast->kategori_masalah }}">
                                    <input type="hidden" name="deskripsi_masalah" value="{{ $broadcast->deskripsi_masalah }}">
                                    <input type="hidden" name="template_id" value="{{ $broadcast->wa_template_id }}">
                                    <button type="submit" title="Kirim Ulang" class="inline-flex items-center px-3 py-1.5 text-[11px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-all shadow-2xs">
                                        <i class="fas fa-paper-plane mr-1.5 text-blue-500"></i>
                                        Kirim Ulang
                                    </button>
                                </form>
                                <form action="{{ route('master.wa-broadcast.destroy', $broadcast->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus riwayat broadcast ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus" class="inline-flex items-center px-3 py-1.5 text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition-all shadow-2xs">
                                        <i class="fas fa-trash-alt mr-1.5 text-rose-400"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center space-y-3 text-slate-300">
                                <i class="fab fa-whatsapp text-5xl"></i>
                                <p class="text-sm font-semibold text-slate-400">Belum ada riwayat broadcast</p>
                                <p class="text-xs text-slate-300">Klik "Buat Broadcast" untuk memulai pengiriman</p>
                                @can('master-wa-broadcast-create')
                                <a href="{{ route('master.wa-broadcast.create') }}" class="mt-2 inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow transition-all">
                                    <i class="fas fa-plus mr-1.5"></i> Buat Broadcast
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            responsive: true,
            order: [[0, 'desc']],
            pageLength: 25,
            language: {
                search: "",
                searchPlaceholder: "Cari broadcast...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(dari _MAX_ total)",
                emptyTable: "Belum ada riwayat broadcast",
                paginate: {
                    first: "«",
                    last: "»",
                    next: "›",
                    previous: "‹"
                }
            },
            columnDefs: [
                { orderable: false, targets: [6] }
            ]
        });
    });
</script>
@endpush
@endsection
