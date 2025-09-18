@extends('layouts.app')

@section('title', 'Checklist Kelengkapan Crew - ' . ($karyawan->nama_lengkap ?? ''))
@section('page_title', 'Checklist Kelengkapan Crew')

@section('content')

<div class="container mx-auto px-2 py-6">
    <!-- Modern Header Card -->
    <section class="bg-gradient-to-r from-blue-700 to-blue-900 text-white rounded-2xl shadow-lg p-6 mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0">
                <div class="w-16 h-16 rounded-full bg-blue-200 flex items-center justify-center text-blue-800 text-2xl font-bold shadow-inner">
                    <span aria-label="Avatar">{{ mb_substr($karyawan->nama_lengkap ?? '?',0,1) }}</span>
                </div>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Checklist Kelengkapan Crew</h1>
                <div class="mt-1 text-blue-100 text-base font-medium flex flex-wrap gap-2">
                    <span>Karyawan: <strong>{{ $karyawan->nama_lengkap ?? '-' }}</strong></span>
                    <span>NIK: <strong>{{ $karyawan->nik ?? '-' }}</strong></span>
                    <span>Divisi: <strong>{{ $karyawan->divisi ?? '-' }}</strong></span>
                    <span>Jabatan: <strong>{{ $karyawan->pekerjaan ?? '-' }}</strong></span>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-2 items-end">
            @if(request()->routeIs('karyawan.onboarding-crew-checklist'))
            <a href="{{ route('karyawan.onboarding-edit', $karyawan->id) }}" class="inline-flex items-center bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>
                Kembali ke Onboarding
            </a>
            @else
            <a href="{{ route('master.karyawan.index') }}" class="inline-flex items-center bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>
                Kembali
            </a>
            @endif
            <a href="{{ route('master.karyawan.crew-checklist.print', $karyawan->id) }}" target="_blank" class="inline-flex items-center bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"></path></svg>
                Cetak
            </a>
        </div>
    </section>

    <div class="bg-white shadow rounded p-6">
    <form id="crew-checklist-new-form" action="{{ route('master.karyawan.crew-checklist.update', $karyawan->id) }}" method="POST">
            @csrf

            <!-- Legend -->
            <div class="mb-6 flex flex-wrap items-center gap-4 text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
                <span class="font-semibold text-gray-800">Legenda Status:</span>
                <div class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span>Valid / Ada</span>
                </div>
                <div class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span>Akan Expired</span>
                </div>
                <div class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span>Expired</span>
                </div>
                <div class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                    <span>Tidak Tersedia</span>
                </div>
            </div>

            <!-- Summary + Tips -->
            <div class="mb-4 p-4 bg-blue-50 rounded-md flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                <div class="text-sm text-blue-700">Ringkasan: <span id="summary-ada" class="font-semibold">0</span> Ada • <span id="summary-soon" class="font-semibold">0</span> Akan Expired • <span id="summary-expired" class="font-semibold">0</span> Expired • <span id="summary-tidak" class="font-semibold">0</span> Tidak</div>
                <div class="text-sm text-gray-600">Format tanggal: <code class="bg-white px-1 py-0.5 rounded">10/Sep/2025</code> atau <code class="bg-white px-1 py-0.5 rounded">10/09/2025</code></div>
            </div>

            <!-- Advanced Search Bar -->
            <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-1">
                        <input type="text" id="item-search" placeholder="Cari item checklist..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm" autocomplete="off">
                        <div class="mt-2 text-xs text-gray-500 flex items-center gap-4">
                            <span>Tip: Gunakan kata kunci seperti "BST", "ijazah", "rekening"</span>
                            <span class="text-blue-600 font-medium">• Enter untuk fokus pertama</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="expand-all-btn" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition text-sm font-medium">
                            Expand All
                        </button>
                        <button type="button" id="collapse-all-btn" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition text-sm font-medium">
                            Collapse All
                        </button>
                    </div>
                </div>
                <div id="search-results" class="mt-3 hidden">
                    <div class="text-sm text-gray-600">Ditemukan <span id="search-count">0</span> item</div>
                </div>
            </div>

            @php
                // Grouping keywords (lowercase) — we match by contains to handle variations in stored names
                $groupDataPribadiKeywords = ['formulir data karyawan','cv','e-ktp','kartu keluarga','bpjs','npwp','photo','pas photo','rek','rekening','buku pelaut'];
                $groupIjasahKeywords = ['ijazah','endor','endors'];
                $groupSertifikatKeywords = ['bst','scrb','aff','mfa','sat','sdsd','erm','brm','mc'];

                // Deduplicate by normalized item_name (case-insensitive, whitespace-normalized)
                $uniqueMap = [];
                foreach ($checklistItems as $it) {
                    $norm = mb_strtolower(trim(preg_replace('/\s+/', ' ', $it->item_name)));
                    if (!isset($uniqueMap[$norm])) {
                        $uniqueMap[$norm] = $it;
                    }
                }
                $items = collect(array_values($uniqueMap));
            @endphp

            <div class="space-y-8">
                {{-- Data Pribadi --}}
                <section aria-labelledby="section-data-pribadi" class="rounded-2xl shadow-lg border border-blue-100 bg-blue-50/60">
                <details class="group" open>
                    <summary class="cursor-pointer mb-3 list-none px-6 pt-6">
                        <div class="flex items-center justify-between">
                            <h4 id="section-data-pribadi" class="text-lg font-bold text-blue-900 tracking-wide flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z"/></svg>
                                1. Data Pribadi
                            </h4>
                            <div class="text-sm text-blue-700 font-semibold">Ada: <span class="section-count" data-section="data-pribadi">0</span></div>
                        </div>
                    </summary>
                    <div class="grid gap-4 px-6 pb-6">
                        @php $seenCanonical = []; @endphp
                        @foreach($items->filter(function($it) use ($groupDataPribadiKeywords){
                            $name = mb_strtolower($it->item_name);
                            foreach ($groupDataPribadiKeywords as $kw) { if (mb_stripos($name, $kw) !== false) return true; }
                            return false;
                        }) as $item)
                            @php
                                // canonical grouping to avoid duplicates (e.g., 'REKENING BCA' vs 'Rek BCA')
                                $norm = mb_strtolower(trim(preg_replace('/\s+/', ' ', $item->item_name)));
                                $canon = $norm;
                                if (preg_match('/\b(rek|rekening|bca)\b/', $norm)) { $canon = 'rekening bca'; }
                                if (!empty($seenCanonical[$canon])) { @endphp @continue @php }
                                $seenCanonical[$canon] = true;

                                $status = ($item->status ?? 'tidak');
                                $badgeColor = $status === 'ada' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700';
                                $displayLabel = $canon === 'rekening bca' ? 'Rekening BCA' : $item->item_name;
                            @endphp

                            <div class="border border-blue-100 rounded-xl p-4 shadow bg-white/90 hover:shadow-md transition-shadow duration-200 checklist-item" data-section="data-pribadi">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-base font-semibold text-gray-800">{{ $displayLabel }}</h3>
                                            <div class="inline-flex items-center px-2 py-1 rounded-full status-badge text-xs font-semibold"
                                                data-item-id="{{ $item->id }}"
                                                :class="{
                                                    'bg-green-100 text-green-800': '{{ $status }}' === 'ada',
                                                    'bg-yellow-100 text-yellow-800': '{{ $status }}' === 'akan expired',
                                                    'bg-red-100 text-red-800': '{{ $status }}' === 'expired',
                                                    'bg-gray-100 text-gray-700': '{{ $status }}' === 'tidak'
                                                }">
                                                {{ ucfirst($status) }}
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor / Info</label>
                                                <input type="text" name="checklist[{{ $item->id }}][nomor_sertifikat]" value="{{ $item->nomor_sertifikat ?? '' }}" placeholder="Contoh: ABCD1234" class="nomor-field block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm" data-item-id="{{ $item->id }}" data-item-name="{{ $item->item_name }}">
                                                <p class="mt-1 text-xs text-gray-500">Min 4 karakter alfanumerik.</p>
                                                <p class="mt-1 text-xs text-red-600 hidden nomor-error" data-item-id="{{ $item->id }}">Nomor tidak valid.</p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Terbit</label>
                                                <input type="date" name="checklist[{{ $item->id }}][issued_date]" value="{{ $item->issued_date ? \Carbon\Carbon::parse($item->issued_date)->format('Y-m-d') : '' }}" class="date-picker block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm" data-item-id="{{ $item->id }}" data-field="issued">
                                                <p class="mt-1 text-xs text-gray-500">Pilih tanggal terbit.</p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Expired</label>
                                                <input type="date" name="checklist[{{ $item->id }}][expired_date]" value="{{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('Y-m-d') : '' }}" class="date-picker block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm" data-item-id="{{ $item->id }}" data-field="expired">
                                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada.</p>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Catatan</label>
                                            <textarea name="checklist[{{ $item->id }}][catatan]" rows="2" class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm resize-none" placeholder="Tambahkan catatan">{{ $item->catatan ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="checklist[{{ $item->id }}][status]" value="{{ $status }}" class="status-input" data-item-id="{{ $item->id }}">
                                <input type="hidden" name="checklist[{{ $item->id }}][item_name]" value="{{ $item->item_name }}">
                            </div>
                        @endforeach
                    </div>
                </details>
                </section>

                {{-- Ijasah --}}
                <section aria-labelledby="section-ijasah" class="rounded-2xl shadow-lg border border-indigo-100 bg-indigo-50/60">
                <details class="group" open>
                    <summary class="cursor-pointer mb-3 list-none px-6 pt-6">
                        <div class="flex items-center justify-between">
                            <h4 id="section-ijasah" class="text-lg font-bold text-indigo-900 tracking-wide flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z"/></svg>
                                2. Ijasah
                            </h4>
                            <div class="flex items-center gap-3">
                                <div class="text-sm text-indigo-700 font-semibold">Ada: <span class="section-count" data-section="ijasah">0</span></div>
                                <div class="w-24 bg-indigo-200 rounded-full h-2">
                                    <div class="section-progress bg-indigo-600 h-2 rounded-full" data-section="ijasah" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </summary>
                    <div class="grid gap-4 px-6 pb-6">
                        @foreach($items->filter(function($it) use ($groupIjasahKeywords){
                            $name = mb_strtolower($it->item_name);
                            foreach ($groupIjasahKeywords as $kw) { if (mb_stripos($name, $kw) !== false) return true; }
                            return false;
                        }) as $item)
                            @php
                                $status = ($item->status ?? 'tidak');
                                $badgeColor = $status === 'ada' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700';
                            @endphp

                            <div class="border border-indigo-100 rounded-xl p-4 shadow bg-white/90 hover:shadow-md transition-shadow duration-200 checklist-item" data-section="ijasah">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-base font-semibold text-gray-800">{{ $item->item_name }}</h3>
                                            <div class="inline-flex items-center px-2 py-1 rounded-full status-badge text-xs font-semibold"
                                                data-item-id="{{ $item->id }}"
                                                :class="{
                                                    'bg-green-100 text-green-800': '{{ $status }}' === 'ada',
                                                    'bg-yellow-100 text-yellow-800': '{{ $status }}' === 'akan expired',
                                                    'bg-red-100 text-red-800': '{{ $status }}' === 'expired',
                                                    'bg-gray-100 text-gray-700': '{{ $status }}' === 'tidak'
                                                }">
                                                {{ ucfirst($status) }}
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor / Info</label>
                                                <input type="text" name="checklist[{{ $item->id }}][nomor_sertifikat]" value="{{ $item->nomor_sertifikat ?? '' }}" placeholder="Contoh: ABCD1234" class="nomor-field block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm" data-item-id="{{ $item->id }}" data-item-name="{{ $item->item_name }}">
                                                <p class="mt-1 text-xs text-gray-500">Min 4 karakter alfanumerik.</p>
                                                <p class="mt-1 text-xs text-red-600 hidden nomor-error" data-item-id="{{ $item->id }}">Nomor tidak valid.</p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Terbit</label>
                                                <input type="hidden" name="checklist[{{ $item->id }}][issued_date]" value="{{ $item->issued_date ? \Carbon\Carbon::parse($item->issued_date)->format('Y-m-d') : '' }}" class="iso-date-input" data-item-id="{{ $item->id }}" data-field="issued">
                                                <input type="text" placeholder="dd/Mon/YYYY" value="{{ $item->issued_date ? \Carbon\Carbon::parse($item->issued_date)->format('d/M/Y') : '' }}" class="display-date-input block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm" data-item-id="{{ $item->id }}" data-field="issued">
                                                <p class="mt-1 text-xs text-gray-500">Contoh: 10/Sep/2025</p>
                                                <p class="mt-1 text-xs text-red-600 hidden date-error" data-item-id="{{ $item->id }}" data-field="issued">Format tidak valid.</p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Expired</label>
                                                <input type="hidden" name="checklist[{{ $item->id }}][expired_date]" value="{{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('Y-m-d') : '' }}" class="iso-date-input" data-item-id="{{ $item->id }}" data-field="expired">
                                                <input type="text" placeholder="dd/Mon/YYYY" value="{{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('d/M/Y') : '' }}" class="display-date-input block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm" data-item-id="{{ $item->id }}" data-field="expired">
                                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada.</p>
                                                <p class="mt-1 text-xs text-red-600 hidden date-error" data-item-id="{{ $item->id }}" data-field="expired">Format tidak valid.</p>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Catatan</label>
                                            <textarea name="checklist[{{ $item->id }}][catatan]" rows="2" class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm resize-none" placeholder="Tambahkan catatan">{{ $item->catatan ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="checklist[{{ $item->id }}][status]" value="{{ $status }}" class="status-input" data-item-id="{{ $item->id }}">
                                <input type="hidden" name="checklist[{{ $item->id }}][item_name]" value="{{ $item->item_name }}">
                            </div>
                        @endforeach
                    </div>
                </details>
                </section>

                {{-- Sertifikat --}}
                <section aria-labelledby="section-sertifikat" class="rounded-2xl shadow-lg border border-purple-100 bg-purple-50/60">
                <details class="group" open>
                    <summary class="cursor-pointer mb-3 list-none px-6 pt-6">
                        <div class="flex items-center justify-between">
                            <h4 id="section-sertifikat" class="text-lg font-bold text-purple-900 tracking-wide flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z"/></svg>
                                3. Sertifikat
                            </h4>
                            <div class="flex items-center gap-3">
                                <div class="text-sm text-purple-700 font-semibold">Ada: <span class="section-count" data-section="sertifikat">0</span></div>
                                <div class="w-24 bg-purple-200 rounded-full h-2">
                                    <div class="section-progress bg-purple-600 h-2 rounded-full" data-section="sertifikat" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </summary>
                    <div class="grid gap-4 px-6 pb-6">
                        @foreach($items->filter(function($it) use ($groupSertifikatKeywords){
                            $name = mb_strtolower($it->item_name);
                            foreach ($groupSertifikatKeywords as $kw) { if (mb_stripos($name, $kw) !== false) return true; }
                            return false;
                        }) as $item)
                            @php
                                $status = ($item->status ?? 'tidak');
                                $badgeColor = $status === 'ada' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700';
                            @endphp

                            <div class="border border-purple-100 rounded-xl p-4 shadow bg-white/90 hover:shadow-md transition-shadow duration-200 checklist-item" data-section="sertifikat">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-base font-semibold text-gray-800">{{ $item->item_name }}</h3>
                                            <div class="inline-flex items-center px-2 py-1 rounded-full status-badge text-xs font-semibold"
                                                data-item-id="{{ $item->id }}"
                                                :class="{
                                                    'bg-green-100 text-green-800': '{{ $status }}' === 'ada',
                                                    'bg-yellow-100 text-yellow-800': '{{ $status }}' === 'akan expired',
                                                    'bg-red-100 text-red-800': '{{ $status }}' === 'expired',
                                                    'bg-gray-100 text-gray-700': '{{ $status }}' === 'tidak'
                                                }">
                                                {{ ucfirst($status) }}
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor / Info</label>
                                                <input type="text" name="checklist[{{ $item->id }}][nomor_sertifikat]" value="{{ $item->nomor_sertifikat ?? '' }}" placeholder="Contoh: ABCD1234" class="nomor-field block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm" data-item-id="{{ $item->id }}" data-item-name="{{ $item->item_name }}">
                                                <p class="mt-1 text-xs text-gray-500">Min 4 karakter alfanumerik.</p>
                                                <p class="mt-1 text-xs text-red-600 hidden nomor-error" data-item-id="{{ $item->id }}">Nomor tidak valid.</p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Terbit</label>
                                                <input type="hidden" name="checklist[{{ $item->id }}][issued_date]" value="{{ $item->issued_date ? \Carbon\Carbon::parse($item->issued_date)->format('Y-m-d') : '' }}" class="iso-date-input" data-item-id="{{ $item->id }}" data-field="issued">
                                                <input type="text" placeholder="dd/Mon/YYYY" value="{{ $item->issued_date ? \Carbon\Carbon::parse($item->issued_date)->format('d/M/Y') : '' }}" class="display-date-input block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm" data-item-id="{{ $item->id }}" data-field="issued">
                                                <p class="mt-1 text-xs text-gray-500">Contoh: 10/Sep/2025</p>
                                                <p class="mt-1 text-xs text-red-600 hidden date-error" data-item-id="{{ $item->id }}" data-field="issued">Format tidak valid.</p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Expired</label>
                                                <input type="hidden" name="checklist[{{ $item->id }}][expired_date]" value="{{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('Y-m-d') : '' }}" class="iso-date-input" data-item-id="{{ $item->id }}" data-field="expired">
                                                <input type="text" placeholder="dd/Mon/YYYY" value="{{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('d/M/Y') : '' }}" class="display-date-input block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm" data-item-id="{{ $item->id }}" data-field="expired">
                                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada.</p>
                                                <p class="mt-1 text-xs text-red-600 hidden date-error" data-item-id="{{ $item->id }}" data-field="expired">Format tidak valid.</p>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Catatan</label>
                                            <textarea name="checklist[{{ $item->id }}][catatan]" rows="2" class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm resize-none" placeholder="Tambahkan catatan">{{ $item->catatan ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="checklist[{{ $item->id }}][status]" value="{{ $status }}" class="status-input" data-item-id="{{ $item->id }}">
                                <input type="hidden" name="checklist[{{ $item->id }}][item_name]" value="{{ $item->item_name }}">
                            </div>
                        @endforeach
                    </div>
                </details>
                </section>

                {{-- Others (not in groups) --}}
                @php
                    $others = $items->filter(function($it) use ($groupDataPribadiKeywords, $groupIjasahKeywords, $groupSertifikatKeywords) {
                        $name = mb_strtolower($it->item_name);
                        foreach ($groupDataPribadiKeywords as $kw) { if (mb_stripos($name, $kw) !== false) return false; }
                        foreach ($groupIjasahKeywords as $kw) { if (mb_stripos($name, $kw) !== false) return false; }
                        foreach ($groupSertifikatKeywords as $kw) { if (mb_stripos($name, $kw) !== false) return false; }
                        return true;
                    });
                @endphp
                @if($others->count())
                <section aria-labelledby="section-others" class="rounded-2xl shadow-lg border border-gray-200 bg-gray-50/60">
                <details class="group" open>
                    <summary class="cursor-pointer mb-3 list-none px-6 pt-6">
                        <div class="flex items-center justify-between">
                            <h4 id="section-others" class="text-lg font-bold text-gray-800 tracking-wide flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z"/></svg>
                                Lainnya
                            </h4>
                            <div class="text-sm text-gray-700 font-semibold">Ada: <span class="section-count" data-section="others">0</span></div>
                        </div>
                    </summary>
                    <div class="grid gap-4 px-6 pb-6">
                        @foreach($others as $item)
                            @php
                                $status = ($item->status ?? 'tidak');
                                $badgeColor = $status === 'ada' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700';
                            @endphp

                            <div class="border border-gray-200 rounded-xl p-4 shadow bg-white/90 hover:shadow-md transition-shadow duration-200 checklist-item" data-section="others">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-base font-semibold text-gray-800">{{ $item->item_name }}</h3>
                                            <div class="inline-flex items-center px-2 py-1 rounded-full status-badge text-xs font-semibold"
                                                data-item-id="{{ $item->id }}"
                                                :class="{
                                                    'bg-green-100 text-green-800': '{{ $status }}' === 'ada',
                                                    'bg-yellow-100 text-yellow-800': '{{ $status }}' === 'akan expired',
                                                    'bg-red-100 text-red-800': '{{ $status }}' === 'expired',
                                                    'bg-gray-100 text-gray-700': '{{ $status }}' === 'tidak'
                                                }">
                                                {{ ucfirst($status) }}
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor / Info</label>
                                                <input type="text" name="checklist[{{ $item->id }}][nomor_sertifikat]" value="{{ $item->nomor_sertifikat ?? '' }}" placeholder="Contoh: ABCD1234" class="nomor-field block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm" data-item-id="{{ $item->id }}" data-item-name="{{ $item->item_name }}">
                                                <p class="mt-1 text-xs text-gray-500">Min 4 karakter alfanumerik.</p>
                                                <p class="mt-1 text-xs text-red-600 hidden nomor-error" data-item-id="{{ $item->id }}">Nomor tidak valid.</p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Terbit</label>
                                                <input type="hidden" name="checklist[{{ $item->id }}][issued_date]" value="{{ $item->issued_date ? \Carbon\Carbon::parse($item->issued_date)->format('Y-m-d') : '' }}" class="iso-date-input" data-item-id="{{ $item->id }}" data-field="issued">
                                                <input type="text" placeholder="dd/Mon/YYYY" value="{{ $item->issued_date ? \Carbon\Carbon::parse($item->issued_date)->format('d/M/Y') : '' }}" class="display-date-input block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm" data-item-id="{{ $item->id }}" data-field="issued">
                                                <p class="mt-1 text-xs text-gray-500">Contoh: 10/Sep/2025</p>
                                                <p class="mt-1 text-xs text-red-600 hidden date-error" data-item-id="{{ $item->id }}" data-field="issued">Format tidak valid.</p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Expired</label>
                                                <input type="hidden" name="checklist[{{ $item->id }}][expired_date]" value="{{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('Y-m-d') : '' }}" class="iso-date-input" data-item-id="{{ $item->id }}" data-field="expired">
                                                <input type="text" placeholder="dd/Mon/YYYY" value="{{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('d/M/Y') : '' }}" class="display-date-input block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm" data-item-id="{{ $item->id }}" data-field="expired">
                                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada.</p>
                                                <p class="mt-1 text-xs text-red-600 hidden date-error" data-item-id="{{ $item->id }}" data-field="expired">Format tidak valid.</p>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Catatan</label>
                                            <textarea name="checklist[{{ $item->id }}][catatan]" rows="2" class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200 shadow-sm resize-none" placeholder="Tambahkan catatan">{{ $item->catatan ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="checklist[{{ $item->id }}][status]" value="{{ $status }}" class="status-input" data-item-id="{{ $item->id }}">
                                <input type="hidden" name="checklist[{{ $item->id }}][item_name]" value="{{ $item->item_name }}">
                            </div>
                        @endforeach
                    </div>
                </details>
                </section>
                @endif
            </div>

            <!-- Tombol submit tersembunyi untuk trigger JS -->
            <button type="submit" id="submit-btn" style="display:none"></button>
            <!-- Floating sticky save bar -->
            <div id="sticky-bar" class="fixed bottom-4 left-4 right-4 md:right-auto md:bottom-8 md:left-auto md:pr-8 flex items-center justify-end gap-4 pointer-events-auto z-50">
                <div class="hidden md:inline-flex items-center bg-white/90 text-gray-700 px-4 py-2 rounded-lg shadow">
                    <div class="text-sm">Ringkasan: <span id="sticky-ada" class="font-semibold">0</span> • <span id="sticky-soon" class="font-semibold">0</span> • <span id="sticky-expired" class="font-semibold">0</span> • <span id="sticky-tidak" class="font-semibold">0</span></div>
                </div>
                <button type="button" id="sticky-save-btn" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-5 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    Simpan Checklist
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const alnumRegex = /^[A-Za-z0-9]{4,}$/;
    const nomorFields = document.querySelectorAll('.nomor-field');

    function updateItemStatus(itemId, nomorValue) {
        const statusInput = document.querySelector(`input.status-input[data-item-id="${itemId}"]`);
        if (!statusInput) return;
        const newStatus = nomorValue && alnumRegex.test(nomorValue.trim()) ? 'ada' : 'tidak';
        statusInput.value = newStatus;
    }

    nomorFields.forEach(function(field) {
        const itemId = field.getAttribute('data-item-id');
        ['input','change','blur'].forEach(function(evt) { field.addEventListener(evt, function(){ updateItemStatus(itemId, this.value); validateNomorField(field); computeExpiryBadge(); updateSummary(); }); });
        updateItemStatus(itemId, field.value);
        validateNomorField(field);
    });

    const form = document.getElementById('crew-checklist-new-form');
    const submitBtn = document.getElementById('submit-btn');
    if (form) {
        form.addEventListener('submit', function(){
            nomorFields.forEach(function(field){ updateItemStatus(field.getAttribute('data-item-id'), field.value); });
            // Before submit, sync display date inputs into hidden ISO inputs and validate
            let hasErrors = false;
            document.querySelectorAll('.display-date-input').forEach(function(display){
                const id = display.getAttribute('data-item-id');
                const field = display.getAttribute('data-field');
                const isoInput = document.querySelector(`.iso-date-input[data-item-id="${id}"][data-field="${field}"]`);
                if (!isoInput) return;
                const iso = parseDisplayDateToIso(display.value);
                isoInput.value = iso || '';
                // show/hide date error
                const err = document.querySelector(`.date-error[data-item-id="${id}"][data-field="${field}"]`);
                if (display.value && !iso) { if (err) err.classList.remove('hidden'); hasErrors = true; } else { if (err) err.classList.add('hidden'); }
            });
            // validate nomor fields once more before submit
            nomorFields.forEach(function(field){ if (!validateNomorField(field)) hasErrors = true; });
            if (hasErrors) { if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Simpan Checklist'; } alert('Periksa kembali input yang berwarna merah.'); return false; }
            if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Menyimpan...'; }
        });
    }


    // Date picker: update badge and summary on change
    document.querySelectorAll('.date-picker').forEach(function(picker){
        picker.addEventListener('change', function(){
            computeExpiryBadge();
            updateSummary();
        });
    });

    // Validation helpers
    function validateNomorField(field) {
        const v = field.value && field.value.trim();
        const ok = v && alnumRegex.test(v.trim());
        const err = document.querySelector(`.nomor-error[data-item-id="${field.getAttribute('data-item-id')}"]`);
        if (!ok) { if (err) err.classList.remove('hidden'); field.classList.add('border-red-400'); } else { if (err) err.classList.add('hidden'); field.classList.remove('border-red-400'); }
        return ok;
    }

    // Summary updater
    function updateSummary() {
        let cntAda = 0, cntSoon = 0, cntExp = 0, cntTidak = 0;
        document.querySelectorAll('.status-badge').forEach(function(badge){
            const txt = badge.textContent.trim().toLowerCase();
            if (txt === 'ada') cntAda++; else if (txt === 'akan expired' || txt === 'akan kadaluarsa' || txt === 'akan expired') cntSoon++; else if (txt === 'expired') cntExp++; else if (txt === 'tidak') cntTidak++;
        });
        document.getElementById('summary-ada').textContent = cntAda;
        document.getElementById('summary-soon').textContent = cntSoon;
        document.getElementById('summary-expired').textContent = cntExp;
        document.getElementById('summary-tidak').textContent = cntTidak;

        // update sticky bar copies
        const stickyAda = document.getElementById('sticky-ada'); if (stickyAda) stickyAda.textContent = cntAda;
        const stickySoon = document.getElementById('sticky-soon'); if (stickySoon) stickySoon.textContent = cntSoon;
        const stickyExp = document.getElementById('sticky-expired'); if (stickyExp) stickyExp.textContent = cntExp;
        const stickyTidak = document.getElementById('sticky-tidak'); if (stickyTidak) stickyTidak.textContent = cntTidak;
        // per-section counts (counts items with status 'ada' inside each data-section)
        ['data-pribadi','ijasah','sertifikat','others'].forEach(function(section){
            const el = document.querySelector(`.section-count[data-section="${section}"]`);
            if (!el) return;
            const cnt = document.querySelectorAll(`[data-section="${section}"] .status-badge`).length ? Array.from(document.querySelectorAll(`[data-section="${section}"] .status-badge`)).filter(b=>b.textContent.trim().toLowerCase() === 'ada').length : 0;
            el.textContent = cnt;
        });
    }

    // Determine expiry state for badges
    function computeExpiryBadge() {
        const today = new Date();
        document.querySelectorAll('.status-badge').forEach(function(badge){
            const id = badge.getAttribute('data-item-id');
            const isoExp = document.querySelector(`.iso-date-input[data-item-id="${id}"][data-field="expired"]`);
            const isoIssued = document.querySelector(`.iso-date-input[data-item-id="${id}"][data-field="issued"]`);
            const statusInput = document.querySelector(`input.status-input[data-item-id="${id}"]`);

            let badgeText = badge.textContent.trim();
            let cls = 'bg-gray-100 text-gray-700';

            // If no nomor/status marked 'ada', show 'Tidak'
            if (statusInput && statusInput.value !== 'ada') {
                badgeText = 'Tidak';
                cls = 'bg-gray-100 text-gray-700';
            } else if (isoExp && isoExp.value) {
                const expDate = new Date(isoExp.value + 'T00:00:00');
                const diffDays = Math.ceil((expDate - today) / (1000*60*60*24));
                if (diffDays < 0) {
                    badgeText = 'Expired';
                    cls = 'bg-red-100 text-red-800';
                } else if (diffDays <= 30) {
                    badgeText = 'Akan Expired';
                    cls = 'bg-yellow-100 text-yellow-800';
                } else {
                    badgeText = 'Ada';
                    cls = 'bg-green-100 text-green-800';
                }
            } else if (statusInput && statusInput.value === 'ada') {
                // Has nomor but no expiry date
                badgeText = 'Ada';
                cls = 'bg-green-100 text-green-800';
            }

            badge.textContent = badgeText;
            badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold status-badge ' + cls;
        });
    }

    // Recompute badges on init and when display dates change
    computeExpiryBadge();
    document.querySelectorAll('.display-date-input').forEach(function(d){ d.addEventListener('blur', function(){ computeExpiryBadge(); updateSummary(); }); });
    document.querySelectorAll('.nomor-field').forEach(function(n){ n.addEventListener('blur', function(){ computeExpiryBadge(); updateSummary(); }); });
    // initial summary
    updateSummary();

    // Wire sticky save button to submit main form
    const stickyBtn = document.getElementById('sticky-save-btn');
    if (stickyBtn) {
        stickyBtn.addEventListener('click', function(){
            // trigger validation and submit via original submit button
            if (submitBtn) submitBtn.click();
        });
    }
});
</script>
@endpush

@endsection

