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
                <p class="text-xs text-slate-400 mt-0.5">Kelola riwayat pengiriman pesan jadwal kapal, status pengiriman, dan kendala operasional ke shipper</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('master-jadwal-kapal-berlabuh.index') }}" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-xl transition-all shadow-sm">
                <i class="fas fa-calendar-alt mr-1.5 text-sky-500"></i>
                Master Jadwal Kapal
            </a>
            <a href="{{ route('master.wa-gateway.index') }}" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl transition-all shadow-sm">
                <i class="fas fa-wifi mr-1.5"></i>
                Koneksi WA
            </a>
            @can('master-wa-broadcast-create')
            <a href="{{ route('master.wa-broadcast.create', ['type' => 'jadwal']) }}" class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow transition-all">
                <i class="fas fa-ship mr-1.5"></i>
                Broadcast Jadwal
            </a>
            <a href="{{ route('master.wa-broadcast.create', ['type' => 'status_pengiriman']) }}" class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow transition-all">
                <i class="fas fa-shipping-fast mr-1.5"></i>
                Broadcast Status Pengiriman
            </a>
            <a href="{{ route('master.wa-broadcast.create', ['type' => 'kendala']) }}" class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow transition-all">
                <i class="fas fa-exclamation-triangle mr-1.5"></i>
                Broadcast Kendala
            </a>
            @endcan
        </div>
    </div>

    {{-- Tabs Switcher: Jadwal Kapal vs Status Pengiriman vs Kendala vs Semua --}}
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-2">
        <a href="{{ route('master.wa-broadcast.index', ['type' => 'all']) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ ($type ?? 'all') === 'all' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <i class="fas fa-list"></i>
            <span>Semua Riwayat</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ ($type ?? 'all') === 'all' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-700' }}">
                {{ $totalAll ?? $broadcasts->count() }}
            </span>
        </a>

        <a href="{{ route('master.wa-broadcast.index', ['type' => 'jadwal']) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ ($type ?? '') === 'jadwal' ? 'bg-sky-600 text-white shadow-sm' : 'bg-white text-sky-700 hover:bg-sky-50 border border-sky-200' }}">
            <i class="fas fa-ship"></i>
            <span>Riwayat Broadcast Jadwal Kapal</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ ($type ?? '') === 'jadwal' ? 'bg-sky-700 text-white' : 'bg-sky-100 text-sky-800' }}">
                {{ $totalJadwal ?? 0 }}
            </span>
        </a>

        <a href="{{ route('master.wa-broadcast.index', ['type' => 'status_pengiriman']) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ in_array($type ?? '', ['status_pengiriman', 'status']) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-indigo-700 hover:bg-indigo-50 border border-indigo-200' }}">
            <i class="fas fa-shipping-fast"></i>
            <span>Riwayat Broadcast Status Pengiriman</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ in_array($type ?? '', ['status_pengiriman', 'status']) ? 'bg-indigo-700 text-white' : 'bg-indigo-100 text-indigo-800' }}">
                {{ $totalStatusPengiriman ?? 0 }}
            </span>
        </a>

        <a href="{{ route('master.wa-broadcast.index', ['type' => 'kendala']) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ ($type ?? '') === 'kendala' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-amber-700 hover:bg-amber-50 border border-amber-200' }}">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Riwayat Broadcast Kendala / Delay</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ ($type ?? '') === 'kendala' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-800' }}">
                {{ $totalKendala ?? 0 }}
            </span>
        </a>
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
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-400 font-medium">Total Terfilter</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $broadcasts->count() }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-sky-600 font-medium">Broadcast Jadwal</p>
            <p class="text-2xl font-bold text-sky-700 mt-1">{{ $totalJadwal ?? 0 }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-indigo-600 font-medium">Status Pengiriman</p>
            <p class="text-2xl font-bold text-indigo-700 mt-1">{{ $totalStatusPengiriman ?? 0 }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-amber-600 font-medium">Broadcast Kendala</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">{{ $totalKendala ?? 0 }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm col-span-2 sm:col-span-1">
            <p class="text-xs text-emerald-600 font-medium">Total Shipper</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalShipper ?? $broadcasts->sum('total_shipper') }}</p>
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
                        <th class="px-5 py-3.5 text-left">Kategori / Keterangan</th>
                        <th class="px-5 py-3.5 text-left">Template WA</th>
                        <th class="px-5 py-3.5 text-center">Shipper</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($broadcasts as $index => $broadcast)
                    @php
                        $isStatus = ($broadcast->template && (stripos($broadcast->template->nama_template, 'status') !== false || stripos($broadcast->template->nama_template, 'pengiriman') !== false))
                            || stripos($broadcast->kategori_masalah, 'status') !== false
                            || stripos($broadcast->kategori_masalah, 'pengiriman') !== false;

                        $isJadwal = !$isStatus && (($broadcast->template && stripos($broadcast->template->nama_template, 'jadwal') !== false)
                            || stripos($broadcast->kategori_masalah, 'jadwal') !== false
                            || empty($broadcast->kategori_masalah));
                    @endphp
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
                                <div class="w-8 h-8 rounded-lg {{ $isStatus ? 'bg-indigo-50 border-indigo-100 text-indigo-600' : ($isJadwal ? 'bg-sky-50 border-sky-100 text-sky-600' : 'bg-amber-50 border-amber-100 text-amber-600') }} border flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ $isStatus ? 'fa-shipping-fast' : ($isJadwal ? 'fa-ship' : 'fa-exclamation-triangle') }} text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-800 leading-tight">{{ $broadcast->nama_kapal }}</div>
                                    <div class="text-[11px] text-slate-400">Voy. {{ $broadcast->no_voyage }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Kategori / Informasi --}}
                        <td class="px-5 py-3.5 max-w-xs">
                            @if($isStatus)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <i class="fas fa-shipping-fast mr-1 text-indigo-500 text-[10px]"></i>
                                    {{ $broadcast->kategori_masalah ?: 'Status Pengiriman' }}
                                </span>
                            @elseif($isJadwal)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                    <i class="fas fa-calendar-alt mr-1 text-sky-500 text-[10px]"></i>
                                    {{ $broadcast->kategori_masalah ?: 'Jadwal Kapal' }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i class="fas fa-exclamation-triangle mr-1 text-amber-500 text-[10px]"></i>
                                    {{ $broadcast->kategori_masalah }}
                                </span>
                            @endif

                            @if($broadcast->deskripsi_masalah)
                                <div class="text-[11px] text-slate-400 mt-1 line-clamp-1">{{ Str::limit($broadcast->deskripsi_masalah, 40) }}</div>
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
                                <p class="text-xs text-slate-300">Pilih jenis broadcast di bawah untuk memulai pengiriman</p>
                                @can('master-wa-broadcast-create')
                                <div class="flex items-center gap-2 mt-2">
                                    <a href="{{ route('master.wa-broadcast.create', ['type' => 'jadwal']) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow transition-all">
                                        <i class="fas fa-ship mr-1.5"></i> Broadcast Jadwal
                                    </a>
                                    <a href="{{ route('master.wa-broadcast.create', ['type' => 'status_pengiriman']) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow transition-all">
                                        <i class="fas fa-shipping-fast mr-1.5"></i> Broadcast Status
                                    </a>
                                    <a href="{{ route('master.wa-broadcast.create', ['type' => 'kendala']) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow transition-all">
                                        <i class="fas fa-exclamation-triangle mr-1.5"></i> Broadcast Kendala
                                    </a>
                                </div>
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
