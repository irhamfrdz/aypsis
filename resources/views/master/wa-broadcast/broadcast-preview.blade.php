@extends('layouts.app')

@section('title', 'Preview Broadcast WA')
@section('page_title', 'Preview Broadcast WhatsApp')

@section('content')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <style>
        /* Custom DataTables Styling */
        div.dataTables_wrapper div.dataTables_filter input {
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            padding: 0.45rem 0.85rem;
            font-size: 0.875rem;
            background-color: #ffffff;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        div.dataTables_wrapper div.dataTables_filter input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        div.dataTables_wrapper div.dataTables_length select {
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            padding: 0.45rem 2rem 0.45rem 0.85rem;
            font-size: 0.875rem;
            background-color: #ffffff;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        div.dataTables_wrapper div.dataTables_length select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.35em 0.75em;
            border-radius: 0.375rem;
            font-weight: 500;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #2563eb !important;
            color: #ffffff !important;
            border: 1px solid #2563eb !important;
        }
        .dataTables_wrapper .grid {
            margin-bottom: 1rem;
            align-items: center;
        }
        /* Custom scrollbar for container list */
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
@endpush

<div class="space-y-6 font-sans max-w-7xl mx-auto pb-12">
    
    <!-- Top Bar Navigation & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-sm">
                <i class="fab fa-whatsapp text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 tracking-tight">Preview Broadcast WhatsApp</h1>
                <p class="text-xs text-slate-500 mt-0.5">Periksa dan sesuaikan nomor kontak shipper sebelum mengirim notifikasi</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <!-- Gateway Status Pill -->
            <div id="topGatewayStatusBadge" class="hidden sm:inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-slate-400 mr-2" id="topGatewayDot"></span>
                <span id="topGatewayText">Memeriksa Gateway...</span>
            </div>

            <a href="{{ route('master.wa-broadcast.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-600 bg-slate-50 hover:bg-slate-100 hover:text-slate-900 border border-slate-200 rounded-xl transition-all shadow-sm">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Kapal & Voyage -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-blue-300 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i class="fas fa-ship text-base"></i>
                </div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-100">
                    Voyage {{ $noVoyage }}
                </span>
            </div>
            <div class="mt-3">
                <p class="text-xs font-medium text-slate-400">Kapal & Voyage</p>
                <h4 class="text-base font-bold text-slate-800 truncate mt-0.5" title="{{ $namaKapal }}">{{ $namaKapal }}</h4>
            </div>
        </div>

        <!-- Kendala / Masalah -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-amber-300 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <i class="fas fa-exclamation-triangle text-base"></i>
                </div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-100">
                    Kategori
                </span>
            </div>
            <div class="mt-3">
                <p class="text-xs font-medium text-slate-400">Kendala Operasional</p>
                <h4 class="text-base font-bold text-slate-800 truncate mt-0.5" title="{{ $kategoriMasalah }}">{{ $kategoriMasalah }}</h4>
            </div>
        </div>

        <!-- Total Shipper Terdampak -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-emerald-300 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i class="fas fa-building text-base"></i>
                </div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-100">
                    Shipper
                </span>
            </div>
            <div class="mt-3">
                <p class="text-xs font-medium text-slate-400">Total Shipper</p>
                <div class="flex items-baseline gap-2 mt-0.5">
                    <h4 class="text-xl font-bold text-slate-800">{{ count($broadcastData) }}</h4>
                    <span class="text-xs text-slate-500 font-medium">perusahaan</span>
                </div>
            </div>
        </div>

        <!-- Total Kontainer Terdampak -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-indigo-300 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <i class="fas fa-boxes-stacked text-base"></i>
                </div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-100">
                    Kontainer
                </span>
            </div>
            <div class="mt-3">
                <p class="text-xs font-medium text-slate-400">Kontainer Terdampak</p>
                <div class="flex items-baseline gap-2 mt-0.5">
                    <h4 class="text-xl font-bold text-slate-800">{{ collect($broadcastData)->sum('jumlah_kontainer') }}</h4>
                    <span class="text-xs text-slate-500 font-medium">unit kontainer</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card & Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Table Control & Toolbar Header -->
        <div class="p-5 border-b border-slate-100 bg-slate-50/70">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <span>Daftar Kontak & Pesan Shipper</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Centang shipper yang ingin dikirimkan pesan, lalu klik <strong>Kirim Massal</strong> atau kirim satu per satu.
                    </p>
                </div>
                
                <!-- Summary Tag & Bulk Send Button -->
                <div class="flex items-center flex-wrap gap-3">
                    <div class="inline-flex items-center px-3.5 py-2 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold shadow-sm">
                        <i class="fas fa-check-circle mr-1.5 text-blue-500"></i>
                        <span id="selected-recipient-summary">0 dari 0 Shipper Dipilih</span>
                    </div>

                    <!-- Tombol Kirim Massal -->
                    <button type="button" 
                            id="btn-open-bulk-modal" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500"
                            disabled
                            title="Pilih minimal 1 shipper yang memiliki nomor kontak">
                        <i class="fab fa-whatsapp mr-2 text-sm"></i>
                        <span>Kirim Massal (<span id="bulk-selected-count">0</span>)</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <div class="p-5 overflow-x-auto">
            <table id="contactTable" class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3.5 px-4 text-center w-12 rounded-l-xl">
                            <input type="checkbox" id="select-all-recipients" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer" title="Pilih semua shipper">
                        </th>
                        <th class="py-3.5 px-3 text-center w-12">No</th>
                        <th class="py-3.5 px-4 min-w-[200px]">Shipper & Sumber</th>
                        <th class="py-3.5 px-4 min-w-[240px]">No. WhatsApp / Kontak</th>
                        <th class="py-3.5 px-4 min-w-[180px]">Kontainer Terdampak</th>
                        <th class="py-3.5 px-3 text-center min-w-[120px]">Status</th>
                        <th class="py-3.5 px-4 text-center min-w-[160px] rounded-r-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($broadcastData as $index => $data)
                        <tr class="hover:bg-slate-50/70 transition-colors group" id="table-row-{{ $index }}">
                            <!-- Checkbox -->
                            <td class="recipient-select-cell py-3.5 px-4 text-center">
                                <input type="checkbox"
                                       class="recipient-checkbox h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer"
                                       data-recipient-index="{{ $index }}"
                                       title="Pilih {{ $data['shipper_name'] }}">
                            </td>

                            <!-- Nomor Baris -->
                            <td class="py-3.5 px-3 text-center text-xs font-medium text-slate-400">
                                {{ $index + 1 }}
                            </td>

                            <!-- Nama Shipper & Sumber -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 text-sm leading-snug group-hover:text-blue-600 transition-colors shipper-name-text">
                                    {{ $data['shipper_name'] }}
                                </div>
                                <div class="mt-1 flex items-center gap-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        <i class="fas fa-database text-[9px] mr-1 text-slate-400"></i>
                                        {{ $data['sumber_tabel'] }}
                                    </span>
                                </div>
                            </td>

                            <!-- Input No WhatsApp -->
                            <td class="py-3.5 px-4">
                                <div class="relative rounded-xl shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                        <i class="fab fa-whatsapp text-sm text-emerald-600"></i>
                                    </div>
                                    <input type="text"
                                           class="contact-person-input block w-full rounded-xl border border-slate-200 pl-9 pr-3 py-1.5 text-xs font-medium text-slate-800 placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all bg-white"
                                           data-recipient-index="{{ $index }}"
                                           value="{{ $data['telepon'] }}"
                                           placeholder="Contoh: 08123456789"
                                           autocomplete="off">
                                </div>
                                <p class="mt-1 text-[10px] text-slate-400">Dapat diedit langsung sebelum kirim</p>
                            </td>

                            <!-- Kontainer Terdampak -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $data['jumlah_kontainer'] }} Kontainer
                                    </span>
                                </div>
                                @if(!empty($data['daftar_kontainer']))
                                    <div class="flex max-w-xs flex-wrap gap-1 custom-scrollbar max-h-16 overflow-y-auto">
                                        @foreach($data['daftar_kontainer'] as $kontainer)
                                            <span class="rounded bg-slate-50 border border-slate-200 px-1.5 py-0.5 text-[10px] font-mono text-slate-700 font-medium">{{ $kontainer }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">-</span>
                                @endif
                            </td>

                            <!-- Status Kontak -->
                            <td class="contact-status py-3.5 px-3 text-center" data-recipient-index="{{ $index }}">
                                <!-- Dynamic status pill rendered by JS -->
                            </td>

                            <!-- Tombol Aksi -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Preview Button -->
                                    <button type="button"
                                            class="preview-msg-btn inline-flex items-center justify-center px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-medium transition-colors border border-slate-200 shadow-sm"
                                            data-shipper="{{ $data['shipper_name'] }}"
                                            data-recipient-index="{{ $index }}"
                                            data-message="{{ $data['pesan'] }}"
                                            title="Lihat isi pesan WA untuk shipper ini">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>

                                    <!-- Send WA Action Container -->
                                    <div class="wa-action inline-block" data-recipient-index="{{ $index }}" data-message="{{ $data['pesan'] }}">
                                        @if($data['wa_url'])
                                            <a href="{{ $data['wa_url'] }}" target="_blank" class="btn-single-send inline-flex items-center justify-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all" data-recipient-index="{{ $index }}">
                                                <i class="fab fa-whatsapp mr-1.5 text-sm"></i>
                                                Kirim WA
                                            </a>
                                        @else
                                            <span class="inline-flex items-center justify-center px-3 py-1.5 bg-slate-100 text-slate-400 text-xs font-medium rounded-xl border border-slate-200 cursor-not-allowed">
                                                <i class="fas fa-phone-slash mr-1.5 text-xs text-slate-400"></i>
                                                No. Kosong
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                                        <i class="fas fa-inbox text-xl"></i>
                                    </div>
                                    <p class="font-medium text-slate-600">Tidak ada data shipper untuk kapal dan voyage ini.</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Silakan periksa manifest kapal atau pilih voyage lainnya.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Asisten Pengiriman Massal (Broadcast Dispatcher) -->
<div id="bulkBroadcastModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900 bg-opacity-60 flex items-center justify-center p-4 transition-all">
    <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <!-- Header Modal -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shadow-sm">
                    <i class="fab fa-whatsapp text-xl"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Asisten Pengiriman Broadcast Massal</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih mode pengiriman otomatis 1-klik (Gateway) atau buka WhatsApp Web</p>
                </div>
            </div>
            <button type="button" id="closeBulkModalBtn" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Body Modal -->
        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
            
            <!-- Gateway Status Alert Box -->
            <div id="modalGatewayAlert" class="p-4 rounded-2xl border flex items-center justify-between text-xs transition-all bg-slate-50 border-slate-200">
                <div class="flex items-center space-x-3">
                    <span class="w-3 h-3 rounded-full bg-slate-400" id="modalGatewayDot"></span>
                    <div>
                        <p class="font-bold text-slate-800" id="modalGatewayTitle">Memeriksa Status WA Gateway...</p>
                        <p class="text-[11px] text-slate-500" id="modalGatewayDesc">Mendeteksi koneksi microservice Baileys di port 3000</p>
                    </div>
                </div>
                <div id="modalGatewayAction">
                    <!-- Dynamic button for QR Code if needed -->
                </div>
            </div>

            <!-- Progress Bar Card -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-2">
                    <span id="bulkProgressLabel">Proses: 0 dari 0 Shipper</span>
                    <span id="bulkProgressPercent" class="text-emerald-600 font-bold">0%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                    <div id="bulkProgressBar" class="bg-emerald-500 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>

            <!-- 1-Click Gateway Automatic Broadcast Card -->
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-400 rounded-2xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-1.5 text-emerald-800 font-bold text-sm">
                            <i class="fas fa-bolt text-amber-500"></i>
                            <span>Pengiriman Otomatis 1-Klik (Background)</span>
                        </div>
                        <p class="text-xs text-emerald-700 mt-1">
                            Kirim ke seluruh shipper terpilih di background tanpa membuka tab WhatsApp satu per satu.
                        </p>
                    </div>
                    <button type="button" 
                            id="btnStartGatewayBulkSend" 
                            class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow transition-all cursor-pointer whitespace-nowrap">
                        <i class="fas fa-paper-plane mr-2"></i>
                        <span>Kirim Semua Sekarang (1 Klik)</span>
                    </button>
                </div>
            </div>

            <!-- Current Shipper Card (Sedang Diproses) -->
            <div id="bulkCurrentCard" class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2.5 mb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Detail Penerima Terpilih</span>
                    </div>
                    <span id="bulkCurrentIndexBadge" class="text-xs font-semibold px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">
                        Penerima ke-1
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
                    <div>
                        <p class="text-[11px] font-medium text-slate-400">Shipper</p>
                        <h4 id="bulkCurrentShipper" class="text-sm font-bold text-slate-800 truncate">-</h4>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-slate-400">No. WhatsApp</p>
                        <h4 id="bulkCurrentPhone" class="text-sm font-bold text-emerald-600 font-mono">-</h4>
                    </div>
                </div>

                <!-- Message Preview Box -->
                <div>
                    <p class="text-[11px] font-medium text-slate-400 mb-1">Isi Pesan WhatsApp:</p>
                    <div id="bulkCurrentMessage" class="bg-stone-100 p-3 rounded-xl border border-slate-200 text-xs text-slate-800 leading-relaxed font-sans max-h-28 overflow-y-auto whitespace-pre-wrap select-all">
                        -
                    </div>
                </div>
            </div>

            <!-- Completion Banner (Hidden by default) -->
            <div id="bulkCompletedBanner" class="hidden bg-emerald-50 border border-emerald-200 rounded-2xl p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-check text-2xl"></i>
                </div>
                <h4 class="text-base font-bold text-emerald-800">Semua Broadcast Berhasil Diproses!</h4>
                <p class="text-xs text-emerald-600 mt-1">Seluruh pesan WhatsApp shipper terpilih telah diproses.</p>
            </div>

            <!-- Option Multi-Tab Shortcut (WhatsApp Web) -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-600">
                <div class="flex items-center gap-2">
                    <i class="fab fa-chrome text-slate-400 text-sm"></i>
                    <span>Alternatif: Buka semua tab WhatsApp Web di browser</span>
                </div>
                <button type="button" id="btnOpenAllTabs" class="inline-flex items-center justify-center px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl font-bold shadow-sm transition-all whitespace-nowrap">
                    <i class="fas fa-external-link-alt mr-1.5 text-[11px]"></i>
                    Buka Semua Tab Web
                </button>
            </div>

            <!-- Queue Mini List -->
            <div>
                <h4 class="text-xs font-bold text-slate-700 mb-2">Daftar Antrean Penerima:</h4>
                <div id="bulkQueueList" class="divide-y divide-slate-100 border border-slate-200 rounded-2xl max-h-40 overflow-y-auto bg-white custom-scrollbar">
                    <!-- Queue items dynamically injected -->
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <button type="button" id="closeBulkModalFooterBtn" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-200 rounded-xl transition-colors">
                Tutup Asisten
            </button>
            <div class="flex items-center gap-2">
                <button type="button" id="btnSkipCurrent" class="px-3.5 py-2 text-xs font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition-colors shadow-sm">
                    <i class="fas fa-forward mr-1 text-slate-400"></i>
                    Lewati Shipper
                </button>
                <button type="button" id="btnSendAndNext" class="inline-flex items-center px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow transition-all">
                    <i class="fab fa-whatsapp mr-1.5 text-sm"></i>
                    <span>Buka Tab WA & Lanjut</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Pesan WhatsApp Modern (Individual) -->
<div id="messagePreviewModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900 bg-opacity-60 flex items-center justify-center p-4 transition-all">
    <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <!-- Header Modal -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                    <i class="fab fa-whatsapp text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800" id="modalShipperName">Nama Shipper</h3>
                    <p class="text-xs text-slate-500" id="modalRecipientPhone">Nomor WhatsApp</p>
                </div>
            </div>
            <button type="button" id="closeModalBtn" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Body Modal (WA Chat Bubble Style) -->
        <div class="p-6 bg-stone-100 relative min-h-[220px] max-h-[60vh] overflow-y-auto custom-scrollbar">
            <div class="bg-white rounded-2xl rounded-tl-xs p-4 shadow-sm border border-slate-200 text-xs text-slate-800 font-sans leading-relaxed whitespace-pre-wrap select-all relative" id="modalMessageContent">
                <!-- Message content injected by JS -->
            </div>
        </div>

        <!-- Footer Modal -->
        <div class="px-6 py-4 bg-white border-t border-slate-100 flex items-center justify-between gap-3">
            <button type="button" id="copyMessageBtn" class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                <i class="fas fa-copy mr-1.5 text-slate-500"></i>
                <span id="copyBtnText">Salin Teks</span>
            </button>
            <div class="flex items-center gap-2">
                <button type="button" id="closeModalFooterBtn" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Tutup
                </button>
                <a href="#" id="modalDirectWaLink" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow transition-all">
                    <i class="fab fa-whatsapp mr-1.5 text-sm"></i>
                    Buka di WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>
<script>
    $(document).ready(function() {
        let contactTable;
        const selectedRecipients = {};
        const contactValues = {};
        const sentRecipients = {};
        let isGatewayOnline = false;

        // Inisialisasi checkbox & nomor awal
        $('.recipient-checkbox').each(function() {
            const index = $(this).attr('data-recipient-index');
            selectedRecipients[index] = false;
        });

        $('.contact-person-input').each(function() {
            contactValues[$(this).attr('data-recipient-index')] = $(this).val() || '';
        });

        function normalizeWaPhone(value) {
            let phone = (value || '').replace(/[^0-9]/g, '');
            if (!phone) {
                return '';
            }

            if (phone.startsWith('0')) {
                return '62' + phone.substring(1);
            }

            return phone.startsWith('62') ? phone : '62' + phone;
        }

        // Cek Status Gateway Baileys
        function checkGateway() {
            $.get("{{ route('master.wa-broadcast.gateway-status') }}")
                .done(function(res) {
                    if (res.isReady) {
                        isGatewayOnline = true;
                        $('#topGatewayStatusBadge').removeClass('bg-slate-100 bg-amber-50 text-slate-600 text-amber-700 border-slate-200 border-amber-200').addClass('bg-emerald-50 text-emerald-700 border-emerald-200');
                        $('#topGatewayDot').removeClass('bg-slate-400 bg-amber-500').addClass('bg-emerald-500');
                        $('#topGatewayText').text('WA Gateway Online (' + (res.user ? res.user.split('@')[0].split(':')[0] : 'Siap') + ')');

                        $('#modalGatewayAlert').removeClass('bg-slate-50 bg-amber-50 border-slate-200 border-amber-200 text-slate-800 text-amber-800').addClass('bg-emerald-50 border-emerald-200 text-emerald-800');
                        $('#modalGatewayDot').removeClass('bg-slate-400 bg-amber-500').addClass('bg-emerald-500 animate-pulse');
                        $('#modalGatewayTitle').text('WA Gateway Terkoneksi & Siap Kirim Otomatis');
                        $('#modalGatewayDesc').text('Nomor Aktif: ' + (res.user || '-'));
                        $('#modalGatewayAction').empty();
                    } else {
                        isGatewayOnline = false;
                        $('#topGatewayStatusBadge').removeClass('bg-emerald-50 text-emerald-700 border-emerald-200').addClass('bg-amber-50 text-amber-700 border-amber-200');
                        $('#topGatewayDot').removeClass('bg-emerald-500').addClass('bg-amber-500');
                        $('#topGatewayText').text('WA Gateway Belum Scan QR');

                        $('#modalGatewayAlert').removeClass('bg-emerald-50 border-emerald-200 text-emerald-800').addClass('bg-amber-50 border-amber-200 text-amber-800');
                        $('#modalGatewayDot').removeClass('bg-emerald-500 animate-pulse').addClass('bg-amber-500');
                        $('#modalGatewayTitle').text('WA Gateway Belum Terkoneksi / Belum Scan QR');
                        $('#modalGatewayDesc').text('Jalankan service di folder wa-gateway dan scan QR code.');
                        $('#modalGatewayAction').html(`
                            <a href="http://localhost:3000/qr" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-xs shadow-sm transition-all">
                                <i class="fas fa-qrcode mr-1.5"></i> Buka Scan QR
                            </a>
                        `);
                    }
                })
                .fail(function() {
                    isGatewayOnline = false;
                    $('#topGatewayStatusBadge').removeClass('bg-emerald-50 text-emerald-700 border-emerald-200 bg-amber-50 text-amber-700 border-amber-200').addClass('bg-slate-100 text-slate-600 border-slate-200');
                    $('#topGatewayDot').removeClass('bg-emerald-500 bg-amber-500').addClass('bg-slate-400');
                    $('#topGatewayText').text('WA Gateway Offline');

                    $('#modalGatewayAlert').removeClass('bg-emerald-50 border-emerald-200 text-emerald-800 bg-amber-50 border-amber-200 text-amber-800').addClass('bg-slate-50 border-slate-200 text-slate-700');
                    $('#modalGatewayDot').removeClass('bg-emerald-500 animate-pulse bg-amber-500').addClass('bg-slate-400');
                    $('#modalGatewayTitle').text('Microservice WA Gateway Belum Dijalankan');
                    $('#modalGatewayDesc').text('Jalankan "node server.js" di terminal folder wa-gateway untuk mengaktifkan kirim 1-klik.');
                    $('#modalGatewayAction').empty();
                });
        }

        checkGateway();

        function updateSelectedSummary() {
            const total = Object.keys(selectedRecipients).length;
            const selected = Object.values(selectedRecipients).filter(Boolean).length;
            
            // Hitung yang terpilih dan nomornya valid
            let validSelectedCount = 0;
            Object.keys(selectedRecipients).forEach(idx => {
                if (selectedRecipients[idx] && normalizeWaPhone(contactValues[idx])) {
                    validSelectedCount++;
                }
            });

            $('#selected-recipient-summary').text(selected + ' dari ' + total + ' Shipper Dipilih');
            $('#bulk-selected-count').text(validSelectedCount);
            
            if (validSelectedCount > 0) {
                $('#btn-open-bulk-modal')
                    .prop('disabled', false)
                    .removeClass('opacity-50 cursor-not-allowed bg-slate-300 text-slate-500')
                    .addClass('bg-emerald-600 hover:bg-emerald-700 text-white shadow');
            } else {
                $('#btn-open-bulk-modal')
                    .prop('disabled', true)
                    .addClass('opacity-50 cursor-not-allowed bg-slate-300 text-slate-500')
                    .removeClass('bg-emerald-600 hover:bg-emerald-700 text-white shadow');
            }

            $('#select-all-recipients').prop('checked', total > 0 && selected === total);
            $('#select-all-recipients').prop('indeterminate', selected > 0 && selected < total);
        }

        function refreshRecipientContact(index, value) {
            const phone = normalizeWaPhone(value);
            const $status = $('.contact-status[data-recipient-index="' + index + '"]');
            const $action = $('.wa-action[data-recipient-index="' + index + '"]');
            const message = $action.attr('data-message') || '';

            // Jika sudah dikirim
            if (sentRecipients[index]) {
                $status.html(`
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        <i class="fas fa-check-double text-[10px] text-blue-500"></i>
                        Terkirim
                    </span>
                `);
                const url = 'https://web.whatsapp.com/send?phone=' + phone + '&text=' + encodeURIComponent(message);
                $action.html(`
                    <a href="${url}" target="_blank" class="btn-single-send inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all" data-recipient-index="${index}">
                        <i class="fas fa-redo-alt mr-1 text-[10px]"></i> Kirim Ulang
                    </a>
                `);
                return;
            }

            if (!selectedRecipients[index]) {
                $status.html(`
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Tidak Dipilih
                    </span>
                `);
                $action.html(`
                    <span class="inline-flex items-center justify-center px-3 py-1.5 bg-slate-100 text-slate-400 text-xs font-medium rounded-xl border border-slate-200 cursor-not-allowed">
                        <i class="fas fa-minus mr-1.5 text-[10px]"></i>
                        Dilewati
                    </span>
                `);
                return;
            }

            if (phone) {
                $status.html(`
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Siap Kirim
                    </span>
                `);

                const url = 'https://web.whatsapp.com/send?phone=' + phone + '&text=' + encodeURIComponent(message);
                const $link = $action.find('a');
                if ($link.length) {
                    $link.attr('href', url);
                } else {
                    $('<a>', {
                        href: url,
                        target: '_blank',
                        class: 'btn-single-send inline-flex items-center justify-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all',
                        html: '<i class="fab fa-whatsapp mr-1.5 text-sm"></i> Kirim WA',
                        'data-recipient-index': index
                    }).appendTo($action.empty());
                }
            } else {
                $status.html(`
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Nomor Kosong
                    </span>
                `);
                $action.html(`
                    <span class="inline-flex items-center justify-center px-3 py-1.5 bg-slate-100 text-slate-400 text-xs font-medium rounded-xl border border-slate-200 cursor-not-allowed" title="Nomor WhatsApp belum diisi">
                        <i class="fas fa-phone-slash mr-1.5 text-xs text-slate-400"></i>
                        No. Kosong
                    </span>
                `);
            }
        }

        $('.contact-person-input').each(function() {
            refreshRecipientContact($(this).attr('data-recipient-index'), contactValues[$(this).attr('data-recipient-index')] || '');
        });

        $(document).on('input', '.contact-person-input', function() {
            const index = $(this).attr('data-recipient-index');
            contactValues[index] = $(this).val() || '';
            refreshRecipientContact(index, contactValues[index]);
            updateSelectedSummary();
        });

        contactTable = $('#contactTable').DataTable({
            responsive: true,
            pageLength: 10,
            dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4"lf>rt<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 text-xs text-slate-500"ip>',
            language: {
                search: "",
                searchPlaceholder: "Cari shipper / kontak...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari total _TOTAL_ shipper",
                infoEmpty: "Tidak ada data shipper",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>',
                    next: '<i class="fas fa-chevron-right text-xs"></i>',
                    previous: '<i class="fas fa-chevron-left text-xs"></i>'
                }
            }
        });

        $('#contactTable').on('draw.dt', function() {
            $('.recipient-checkbox').each(function() {
                const index = $(this).attr('data-recipient-index');
                $(this).prop('checked', Boolean(selectedRecipients[index]));
                refreshRecipientContact(index, contactValues[index] || '');
            });
            updateSelectedSummary();
        });

        $(document).on('change', '.recipient-checkbox', function() {
            const index = $(this).attr('data-recipient-index');
            selectedRecipients[index] = $(this).prop('checked');
            refreshRecipientContact(index, contactValues[index] || '');
            updateSelectedSummary();
        });

        $('#select-all-recipients').on('change', function() {
            const isSelected = $(this).prop('checked');
            
            // Ubah di seluruh baris data table
            Object.keys(selectedRecipients).forEach(idx => {
                selectedRecipients[idx] = isSelected;
            });

            contactTable.rows().nodes().to$().find('.recipient-checkbox').each(function() {
                const index = $(this).attr('data-recipient-index');
                $(this).prop('checked', isSelected);
                refreshRecipientContact(index, contactValues[index] || '');
            });
            
            updateSelectedSummary();
        });

        // Single Send click tracking
        $(document).on('click', '.btn-single-send', function() {
            const index = $(this).attr('data-recipient-index');
            if (index !== undefined) {
                sentRecipients[index] = true;
                setTimeout(() => {
                    refreshRecipientContact(index, contactValues[index] || '');
                }, 500);
            }
        });

        // ── Preview Modal Handler ──
        $(document).on('click', '.preview-msg-btn', function() {
            const index = $(this).attr('data-recipient-index');
            const shipper = $(this).attr('data-shipper');
            const message = $(this).attr('data-message');
            const phone = contactValues[index] || '';
            const normalizedPhone = normalizeWaPhone(phone);

            $('#modalShipperName').text(shipper);
            $('#modalRecipientPhone').text(phone ? 'No: ' + phone : 'Nomor WhatsApp belum diisi');
            $('#modalMessageContent').text(message);

            if (normalizedPhone) {
                $('#modalDirectWaLink').attr('href', 'https://web.whatsapp.com/send?phone=' + normalizedPhone + '&text=' + encodeURIComponent(message)).removeClass('opacity-50 pointer-events-none');
            } else {
                $('#modalDirectWaLink').attr('href', '#').addClass('opacity-50 pointer-events-none');
            }

            $('#copyBtnText').text('Salin Teks');
            $('#messagePreviewModal').removeClass('hidden');
        });

        function closeModal() {
            $('#messagePreviewModal').addClass('hidden');
        }

        $('#closeModalBtn, #closeModalFooterBtn').on('click', closeModal);

        $('#messagePreviewModal').on('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Copy message to clipboard
        $('#copyMessageBtn').on('click', function() {
            const textToCopy = $('#modalMessageContent').text();
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(textToCopy).then(function() {
                    $('#copyBtnText').text('Tersalin!');
                    setTimeout(() => { $('#copyBtnText').text('Salin Teks'); }, 2000);
                });
            } else {
                const tempTextArea = document.createElement('textarea');
                tempTextArea.value = textToCopy;
                document.body.appendChild(tempTextArea);
                tempTextArea.select();
                document.execCommand('copy');
                document.body.removeChild(tempTextArea);
                $('#copyBtnText').text('Tersalin!');
                setTimeout(() => { $('#copyBtnText').text('Salin Teks'); }, 2000);
            }
        });


        // ═══════════════════════════════════════════════════════════
        // ── FITUR PENGIRIMAN BROADCAST MASSAL (BULK DISPATCHER) ───
        // ═══════════════════════════════════════════════════════════
        let bulkQueue = [];
        let currentQueueIndex = 0;
        let isGatewaySending = false;

        function buildBulkQueue() {
            bulkQueue = [];
            Object.keys(selectedRecipients).forEach(idx => {
                if (selectedRecipients[idx]) {
                    const phone = normalizeWaPhone(contactValues[idx]);
                    if (phone) {
                        const $row = $('#table-row-' + idx);
                        const shipperName = $row.find('.shipper-name-text').text().trim() || ('Shipper #' + (parseInt(idx) + 1));
                        const message = $('.wa-action[data-recipient-index="' + idx + '"]').attr('data-message') || '';
                        bulkQueue.push({
                            index: idx,
                            shipper: shipperName,
                            phone: phone,
                            rawPhone: contactValues[idx],
                            message: message,
                            status: sentRecipients[idx] ? 'sent' : 'pending'
                        });
                    }
                }
            });
        }

        function renderBulkQueueUI() {
            const total = bulkQueue.length;
            if (total === 0) {
                return;
            }

            const processed = bulkQueue.filter(q => q.status === 'sent' || q.status === 'skipped').length;
            const percent = Math.round((processed / total) * 100);

            $('#bulkProgressLabel').text(`Proses: ${processed} dari ${total} Shipper`);
            $('#bulkProgressPercent').text(`${percent}%`);
            $('#bulkProgressBar').css('width', `${percent}%`);

            // Check if done
            if (currentQueueIndex >= total) {
                $('#bulkCurrentCard').addClass('hidden');
                $('#bulkCompletedBanner').removeClass('hidden');
                $('#btnSendAndNext').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
                $('#btnSkipCurrent').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
                $('#btnStartGatewayBulkSend').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
            } else {
                $('#bulkCurrentCard').removeClass('hidden');
                $('#bulkCompletedBanner').addClass('hidden');
                $('#btnSendAndNext').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
                $('#btnSkipCurrent').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
                $('#btnStartGatewayBulkSend').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');

                const currentItem = bulkQueue[currentQueueIndex];
                $('#bulkCurrentIndexBadge').text(`Penerima ke-${currentQueueIndex + 1} dari ${total}`);
                $('#bulkCurrentShipper').text(currentItem.shipper);
                $('#bulkCurrentPhone').text(currentItem.rawPhone || currentItem.phone);
                $('#bulkCurrentMessage').text(currentItem.message);
            }

            // Render mini queue list
            let listHtml = '';
            bulkQueue.forEach((item, i) => {
                let badge = '';
                let activeBg = (i === currentQueueIndex && currentQueueIndex < total) ? 'bg-emerald-50 font-semibold' : '';

                if (item.status === 'sent') {
                    badge = '<span class="text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200"><i class="fas fa-check-double mr-1 text-[9px]"></i> Terkirim</span>';
                } else if (item.status === 'sending') {
                    badge = '<span class="text-[10px] text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full font-bold animate-pulse"><i class="fas fa-spinner fa-spin mr-1 text-[9px]"></i> Mengirim...</span>';
                } else if (item.status === 'failed') {
                    badge = '<span class="text-[10px] text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200"><i class="fas fa-times mr-1 text-[9px]"></i> Gagal</span>';
                } else if (item.status === 'skipped') {
                    badge = '<span class="text-[10px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200"><i class="fas fa-forward mr-1 text-[9px]"></i> Dilewati</span>';
                } else if (i === currentQueueIndex) {
                    badge = '<span class="text-[10px] text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full font-bold animate-pulse"><i class="fas fa-paper-plane mr-1 text-[9px]"></i> Fokus</span>';
                } else {
                    badge = '<span class="text-[10px] text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full">Menunggu</span>';
                }

                listHtml += `
                    <div class="px-4 py-2 flex items-center justify-between text-xs ${activeBg}">
                        <div class="flex items-center space-x-2 truncate pr-2">
                            <span class="text-slate-400 font-mono w-5">${i + 1}.</span>
                            <span class="text-slate-800 font-medium truncate">${item.shipper}</span>
                            <span class="text-slate-400 text-[11px] hidden sm:inline">(${item.phone})</span>
                        </div>
                        <div class="flex-shrink-0">
                            ${badge}
                        </div>
                    </div>
                `;
            });
            $('#bulkQueueList').html(listHtml);
        }

        // Buka modal Bulk Dispatcher
        $('#btn-open-bulk-modal').on('click', function() {
            checkGateway();
            buildBulkQueue();
            if (bulkQueue.length === 0) {
                alert('Silakan pilih minimal 1 shipper yang memiliki nomor WhatsApp valid terlebih dahulu.');
                return;
            }
            // Set index ke yang belum terkirim pertama
            currentQueueIndex = bulkQueue.findIndex(q => q.status === 'pending');
            if (currentQueueIndex === -1) {
                currentQueueIndex = 0;
            }
            renderBulkQueueUI();
            $('#bulkBroadcastModal').removeClass('hidden');
        });

        function closeBulkModal() {
            if (isGatewaySending) {
                if (!confirm('Proses pengiriman otomatis sedang berjalan. Apakah Anda yakin ingin menutup modal?')) {
                    return;
                }
            }
            $('#bulkBroadcastModal').addClass('hidden');
        }

        $('#closeBulkModalBtn, #closeBulkModalFooterBtn').on('click', closeBulkModal);

        $('#bulkBroadcastModal').on('click', function(e) {
            if (e.target === this) {
                closeBulkModal();
            }
        });

        // ── PENGIRIMAN 1-KLIK OTOMATIS VIA GATEWAY ──────────────────
        $('#btnStartGatewayBulkSend').on('click', async function() {
            if (isGatewaySending) return;

            if (!isGatewayOnline) {
                alert('Microservice WhatsApp Gateway belum terkoneksi. Silakan jalankan "node server.js" di folder wa-gateway dan scan QR code terlebih dahulu.');
                return;
            }

            const pendingItems = bulkQueue.filter(q => q.status === 'pending' || q.status === 'failed');
            if (pendingItems.length === 0) {
                alert('Semua pesan dalam antrean sudah selesai dikirim.');
                return;
            }

            if (!confirm(`Mulai pengiriman otomatis ke ${pendingItems.length} shipper di background?`)) {
                return;
            }

            isGatewaySending = true;
            $('#btnStartGatewayBulkSend').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim Otomatis...');

            for (let i = 0; i < bulkQueue.length; i++) {
                const item = bulkQueue[i];
                if (item.status === 'sent') continue;

                currentQueueIndex = i;
                item.status = 'sending';
                renderBulkQueueUI();

                try {
                    const response = await $.ajax({
                        url: "{{ route('master.wa-broadcast.gateway-send-single') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            phone: item.phone,
                            message: item.message
                        }
                    });

                    if (response.status) {
                        item.status = 'sent';
                        sentRecipients[item.index] = true;
                    } else {
                        item.status = 'failed';
                    }
                } catch (err) {
                    console.error('Send error:', err);
                    item.status = 'failed';
                }

                refreshRecipientContact(item.index, contactValues[item.index] || '');
                renderBulkQueueUI();

                // Delay aman 1 detik antar nomor agar tidak dianggap spam oleh WhatsApp
                await new Promise(r => setTimeout(r, 1000));
            }

            currentQueueIndex = bulkQueue.length;
            isGatewaySending = false;
            $('#btnStartGatewayBulkSend').prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i> Kirim Semua Sekarang (1 Klik)');
            renderBulkQueueUI();
            alert('Proses pengiriman broadcast otomatis selesai!');
        });

        // Kirim & Lanjut (Buka tab WA saat ini, lalu geser ke index berikutnya)
        $('#btnSendAndNext').on('click', function() {
            if (currentQueueIndex >= bulkQueue.length) return;

            const item = bulkQueue[currentQueueIndex];
            const url = 'https://web.whatsapp.com/send?phone=' + item.phone + '&text=' + encodeURIComponent(item.message);
            
            // Buka tab WA
            window.open(url, '_blank');

            // Tandai sudah terkirim
            item.status = 'sent';
            sentRecipients[item.index] = true;
            refreshRecipientContact(item.index, contactValues[item.index] || '');

            // Majukan antrean
            currentQueueIndex++;
            renderBulkQueueUI();
        });

        // Lewati shipper saat ini
        $('#btnSkipCurrent').on('click', function() {
            if (currentQueueIndex >= bulkQueue.length) return;

            const item = bulkQueue[currentQueueIndex];
            item.status = 'skipped';

            currentQueueIndex++;
            renderBulkQueueUI();
        });

        // Buka Semua Tab Sekaligus (Multi-tab with delay)
        $('#btnOpenAllTabs').on('click', function() {
            if (bulkQueue.length === 0) return;

            if (!confirm(`Apakah Anda yakin ingin membuka ${bulkQueue.length} tab WhatsApp sekaligus di browser Anda? Pastikan pop-up browser tidak diblokir.`)) {
                return;
            }

            bulkQueue.forEach((item, i) => {
                setTimeout(() => {
                    const url = 'https://web.whatsapp.com/send?phone=' + item.phone + '&text=' + encodeURIComponent(item.message);
                    window.open(url, '_blank');
                    item.status = 'sent';
                    sentRecipients[item.index] = true;
                    refreshRecipientContact(item.index, contactValues[item.index] || '');
                    
                    if (i === bulkQueue.length - 1) {
                        currentQueueIndex = bulkQueue.length;
                        renderBulkQueueUI();
                    }
                }, i * 350); // delay 350ms per tab agar browser tidak hang
            });
        });

        updateSelectedSummary();
    });
</script>
@endpush
@endsection
