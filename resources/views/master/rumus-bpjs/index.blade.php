@extends('layouts.app')

@section('title', 'Master Rumus BPJS')
@section('page_title', 'Master Rumus BPJS')

@section('content')
<div class="space-y-6">

    {{-- ── Header ────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Master Rumus BPJS</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola rumus perhitungan iuran BPJS per group</p>
        </div>
        <button type="button" onclick="openModal()"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow-sm transition duration-200 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Rumus
        </button>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg" role="alert">
            <i class="fas fa-check-circle text-emerald-500"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── Tabel Group JKN ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-100 text-blue-700">
                <i class="fas fa-file-medical text-xs"></i>
            </span>
            <h3 class="text-base font-semibold text-gray-800">Group JKN</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="px-4 py-3 w-10 text-center font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Group Name</th>
                        <th class="px-4 py-3 font-semibold">Cabang BPJS</th>
                        <th class="px-4 py-3 font-semibold text-center">Tunjangan (%)</th>
                        <th class="px-4 py-3 font-semibold text-center">Hutang (%)</th>
                        <th class="px-4 py-3 font-semibold text-center">Biaya (%)</th>
                        <th class="px-4 py-3 font-semibold">Keterangan</th>
                        <th class="px-4 py-3 font-semibold text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rumusJkn as $index => $item)
                        <tr class="hover:bg-blue-50/30 transition duration-150">
                            <td class="px-4 py-3 text-center text-gray-400 text-xs">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800">{{ $item->group_name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $item->cabang_bpjs ?: '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($item->tunjangan_persen)
                                    <span class="inline-block bg-blue-50 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $item->tunjangan_persen }}%</span>
                                @else <span class="text-gray-300">—</span> @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($item->hutang_persen)
                                    <span class="inline-block bg-rose-50 text-rose-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $item->hutang_persen }}%</span>
                                @else <span class="text-gray-300">—</span> @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($item->biaya_persen)
                                    <span class="inline-block bg-amber-50 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $item->biaya_persen }}%</span>
                                @else <span class="text-gray-300">—</span> @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate">{{ $item->keterangan_custom ?: '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <button onclick="editModal({{ $item->toJson() }})"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <form action="{{ route('master-rumus-bpjs.destroy', $item->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus data ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-red-50 text-red-500 hover:bg-red-100 transition" title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 text-sm">
                                <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                Belum ada data Group JKN.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @php
        $rumusJamsostekBpu = $rumusJamsostek->filter(fn($item) => !str_contains(strtoupper($item->group_name), 'PPU'));
        $rumusJamsostekPpu = $rumusJamsostek->filter(fn($item) => str_contains(strtoupper($item->group_name), 'PPU'));
    @endphp

    {{-- ── Tabel Group BP Jamsostek BPU ─────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-sky-100 text-sky-700">
                <i class="fas fa-users text-xs"></i>
            </span>
            <h3 class="text-base font-semibold text-gray-800">Group BP Jamsostek <span class="text-sky-600">(BPU)</span></h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="px-4 py-3 w-10 text-center font-semibold">No</th>
                        <th class="px-4 py-3 font-semibold">Group Name</th>
                        <th class="px-4 py-3 font-semibold">Cabang BPJS</th>
                        <th class="px-4 py-3 font-semibold text-center">Tunjangan JKK 1%</th>
                        <th class="px-4 py-3 font-semibold text-center">Hutang (Rp) — Tabel DPP</th>
                        <th class="px-4 py-3 font-semibold text-center">Biaya (Rp)</th>
                        <th class="px-4 py-3 font-semibold text-center">Diskon</th>
                        <th class="px-4 py-3 font-semibold">Keterangan</th>
                        <th class="px-4 py-3 font-semibold text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rumusJamsostekBpu as $index => $item)
                        <tr class="hover:bg-sky-50/30 transition duration-150">
                            <td class="px-4 py-3 text-center text-gray-400 text-xs">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800">
                                {{ $item->group_name }}
                                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-sky-100 text-sky-700">BPU</span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $item->cabang_bpjs ?: '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($item->tunjangan_persen)
                                    <span class="inline-block bg-blue-50 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $item->tunjangan_persen }}%</span>
                                @else <span class="text-gray-300">—</span> @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($item->hutang_tiers && count($item->hutang_tiers) > 0)
                                    <div class="space-y-1">
                                        @foreach($item->hutang_tiers as $tier)
                                        <div class="flex items-center gap-1.5 text-xs">
                                            <span class="text-gray-400 font-mono">Rp {{ number_format((float)($tier['dpp'] ?? 0), 0, ',', '.') }}</span>
                                            <span class="text-gray-300">→</span>
                                            <span class="font-semibold text-gray-700 font-mono">Rp {{ number_format((float)($tier['potongan'] ?? 0), 0, ',', '.') }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                @elseif($item->hutang_persen)
                                    <span class="text-xs font-mono font-semibold text-gray-700">Rp {{ number_format((float)$item->hutang_persen, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-xs font-mono font-medium text-gray-700">
                                {{ $item->biaya_persen ? 'Rp ' . number_format((float)$item->biaya_persen, 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex flex-col items-center gap-1" id="diskon-cell-{{ $item->id }}">
                                    <select onchange="handleTableDiskonStatusChange(this, {{ $item->id }})"
                                            class="diskon-status-select text-xs border border-gray-300 rounded-lg shadow-sm py-1 px-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150 {{ ($item->diskon_status ?? 'tidak_ada') === 'ada' ? 'bg-amber-50 text-amber-900 border-amber-300 font-semibold' : 'bg-white text-gray-600' }}">
                                        <option value="tidak_ada" {{ ($item->diskon_status ?? 'tidak_ada') === 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                                        <option value="ada" {{ ($item->diskon_status ?? 'tidak_ada') === 'ada' ? 'selected' : '' }}>Ada Diskon</option>
                                    </select>
                                    <div class="diskon-inputs flex items-center gap-1 {{ ($item->diskon_status ?? 'tidak_ada') === 'ada' ? '' : 'hidden' }}">
                                        <input type="number" step="0.01" min="0"
                                               value="{{ $item->diskon_nilai ? (float)$item->diskon_nilai : '' }}"
                                               placeholder="Nilai"
                                               onchange="handleTableDiskonValueChange(this, {{ $item->id }})"
                                               class="diskon-nilai text-xs w-16 border border-gray-300 rounded-lg py-1 px-2 text-right font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                        <select onchange="handleTableDiskonTipeChange(this, {{ $item->id }})"
                                                class="diskon-tipe text-xs border border-gray-300 rounded-lg py-1 px-1 bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="persen" {{ ($item->diskon_tipe ?? 'persen') === 'persen' ? 'selected' : '' }}>%</option>
                                            <option value="nominal" {{ ($item->diskon_tipe ?? 'persen') === 'nominal' ? 'selected' : '' }}>Rp</option>
                                        </select>
                                    </div>
                                    <span class="save-status-msg text-[10px] text-emerald-600 hidden font-semibold">✓ Tersimpan</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate">{{ $item->keterangan_custom ?: '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <button onclick="editModal({{ $item->toJson() }})"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <form action="{{ route('master-rumus-bpjs.destroy', $item->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus data ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-red-50 text-red-500 hover:bg-red-100 transition" title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-400 text-sm">
                                <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                Belum ada data Group BP Jamsostek BPU.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Tabel Group BP Jamsostek PPU ─────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700">
                <i class="fas fa-briefcase text-xs"></i>
            </span>
            <h3 class="text-base font-semibold text-gray-800">Group BP Jamsostek <span class="text-emerald-600">(PPU)</span></h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="px-3 py-3 w-10 text-center font-semibold">No</th>
                        <th class="px-3 py-3 font-semibold">Group Name</th>
                        <th class="px-3 py-3 font-semibold">Cabang</th>
                        <th class="px-3 py-3 font-semibold text-center">JHT 3.7% Biaya</th>
                        <th class="px-3 py-3 font-semibold text-center">JHT 2% Hutang</th>
                        <th class="px-3 py-3 font-semibold text-center">JKK 0.24% Tunj.</th>
                        <th class="px-3 py-3 font-semibold text-center">JKM 0.3% Tunj.</th>
                        <th class="px-3 py-3 font-semibold text-center">JP 2% Biaya</th>
                        <th class="px-3 py-3 font-semibold text-center">JP 1% Hutang</th>
                        <th class="px-3 py-3 font-semibold text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rumusJamsostekPpu as $index => $item)
                        <tr class="hover:bg-emerald-50/30 transition duration-150">
                            <td class="px-3 py-3 text-center text-gray-400 text-xs">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3 font-semibold text-gray-800">
                                {{ $item->group_name }}
                                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-700">PPU</span>
                            </td>
                            <td class="px-3 py-3 text-gray-500">{{ $item->cabang_bpjs ?: '—' }}</td>
                            @foreach(['jht_biaya','jht_hutang','jkk_tunjangan','jkm_tunjangan','jp_biaya','jp_hutang'] as $field)
                            <td class="px-3 py-3 text-center">
                                @if($item->$field)
                                    <span class="inline-block bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $item->$field }}%</span>
                                @else <span class="text-gray-300">—</span> @endif
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <button onclick="editModal({{ $item->toJson() }})"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <form action="{{ route('master-rumus-bpjs.destroy', $item->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus data ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-red-50 text-red-500 hover:bg-red-100 transition" title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-400 text-sm">
                                <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                Belum ada data Group BP Jamsostek PPU.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════════
     Modal Form Tambah
     ════════════════════════════════════════════════════════════════ --}}
<div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl transform transition-all">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600">
                        <i class="fas fa-plus text-sm"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Tambah Rumus BPJS</h3>
                        <p class="text-xs text-gray-400">Isi data rumus perhitungan iuran</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="addRow()"
                            class="inline-flex items-center gap-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs transition">
                        <i class="fas fa-plus"></i> Tambah Baris
                    </button>
                    <button type="button" onclick="closeCreateModal()"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <form id="createForm" method="POST" action="{{ route('master-rumus-bpjs.store') }}">
                @csrf
                <div class="px-6 py-4 max-h-[70vh] overflow-y-auto space-y-4" id="dynamic-rows-container">

                    {{-- Baris pertama --}}
                    <div class="bpjs-row relative bg-gray-50 border border-gray-200 rounded-xl p-5">
                        <button type="button" class="absolute top-3 right-3 text-gray-300 hover:text-red-500 hidden remove-row-btn transition" onclick="removeRow(this)" title="Hapus Baris">
                            <i class="fas fa-times-circle"></i>
                        </button>

                        {{-- Row 1: Jenis, Nama, Cabang --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Jenis BPJS</label>
                                <select name="jenis[]" class="form-select w-full border-gray-300 rounded-lg shadow-sm text-sm" required
                                        onchange="toggleJenisFields(this)">
                                    <option value="jkn">Group JKN</option>
                                    <option value="jamsostek">Group BP Jamsostek</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Nama Group</label>
                                <input type="text" name="group_name[]" class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                       required placeholder="Cth: JKN-KIS-HARIAN" oninput="togglePpuFields(this)">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Cabang BPJS</label>
                                <input type="text" name="cabang_bpjs[]" class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                       placeholder="Cth: Batam / Jakarta">
                            </div>
                        </div>

                        {{-- JKN Fields (hanya muncul saat jenis = jkn) --}}
                        <div class="jkn-fields">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Tunjangan (%)</label>
                                    <div class="relative">
                                        <input type="number" name="tunjangan_persen[]"
                                               class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm pr-8"
                                               step="0.01" min="0" placeholder="Cth: 1">
                                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 text-sm">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Hutang (%)</label>
                                    <div class="relative">
                                        <input type="number" name="hutang_persen[]"
                                               class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm pr-8"
                                               step="0.01" min="0" placeholder="Cth: 1">
                                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 text-sm">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Biaya (%)</label>
                                    <div class="relative">
                                        <input type="number" name="biaya_persen[]"
                                               class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm pr-8"
                                               step="0.01" min="0" placeholder="Cth: 1">
                                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 text-sm">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BPU Fields (hanya muncul saat jenis = jamsostek) --}}
                        <div class="bpu-fields hidden">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">

                                {{-- Tunjangan --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Tunjangan JKK 1% (%)</label>
                                    <div class="relative">
                                        <input type="number" name="tunjangan_persen[]"
                                               class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm pr-8"
                                               step="0.01" min="0" placeholder="Cth: 1">
                                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 text-sm">%</span>
                                    </div>
                                </div>

                                {{-- Hutang — Tabel DPP --}}
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                        Hutang (Rp) — Tabel DPP
                                        <span class="ml-1 normal-case font-normal text-gray-400">(DPP → Potongan)</span>
                                    </label>
                                    <input type="hidden" name="hutang_tiers[]" class="hutang-tiers-json">
                                    <div class="border border-indigo-100 rounded-xl bg-indigo-50/40 p-3">
                                        <div class="hutang-tiers-container space-y-2 mb-2">
                                            <div class="hutang-tier-row grid grid-cols-[1fr_auto_1fr_auto] items-center gap-2">
                                                <div>
                                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1 uppercase tracking-wide">DPP (Rp)</label>
                                                    <input type="number" class="tier-dpp w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-right focus:ring-indigo-500 focus:border-indigo-500"
                                                           step="1" min="0" placeholder="0" oninput="syncTiersJson(this)">
                                                </div>
                                                <div class="flex flex-col items-center pt-5">
                                                    <i class="fas fa-arrow-right text-indigo-300 text-xs"></i>
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1 uppercase tracking-wide">Potongan (Rp)</label>
                                                    <input type="number" class="tier-potongan w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-right focus:ring-indigo-500 focus:border-indigo-500"
                                                           step="1" min="0" placeholder="0" oninput="syncTiersJson(this)">
                                                </div>
                                                <div class="flex flex-col items-center pt-5">
                                                    <button type="button" onclick="removeTierRow(this)"
                                                            class="remove-tier-btn hidden text-red-400 hover:text-red-600 transition w-6 h-6 flex items-center justify-center rounded-md hover:bg-red-50"
                                                            title="Hapus baris">
                                                        <i class="fas fa-times text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" onclick="addTierRow(this)"
                                                class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition">
                                            <i class="fas fa-plus"></i> Tambah Baris DPP
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Biaya + Diskon --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Biaya (% / Rp)</label>
                                    <input type="number" name="biaya_persen[]"
                                           class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                           step="0.01" min="0" placeholder="Cth: 5 atau 6800">
                                </div>
                                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3">
                                    <label class="block text-xs font-semibold text-amber-700 mb-2 uppercase tracking-wide">Variabel Diskon</label>
                                    <div class="flex flex-col gap-2">
                                        <select name="diskon_status[]" class="form-select w-full border-amber-200 rounded-lg shadow-sm text-sm bg-white" onchange="toggleDiskonInputs(this)">
                                            <option value="tidak_ada">Tidak Ada (0%)</option>
                                            <option value="ada">Ada Diskon</option>
                                        </select>
                                        <div class="diskon-nilai-wrapper hidden flex gap-2">
                                            <input type="number" name="diskon_nilai[]"
                                                   class="form-input flex-1 border-gray-300 rounded-lg shadow-sm text-sm"
                                                   step="0.01" min="0" placeholder="Nilai diskon">
                                            <select name="diskon_tipe[]" class="form-select border-gray-300 rounded-lg shadow-sm text-sm">
                                                <option value="persen">%</option>
                                                <option value="nominal">Rp</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PPU Fields --}}
                        <div class="ppu-fields hidden mb-4 bg-emerald-50 border border-emerald-100 rounded-xl p-4">
                            <p class="text-xs font-bold text-emerald-700 mb-3 uppercase tracking-wide">Variabel PPU Jamsostek</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach([
                                    ['jht_biaya[]','JHT Biaya (%)','3.7'],
                                    ['jht_hutang[]','JHT Hutang (%)','2'],
                                    ['jkk_tunjangan[]','JKK Tunjangan (%)','0.24'],
                                    ['jkm_tunjangan[]','JKM Tunjangan (%)','0.3'],
                                    ['jp_biaya[]','JP Biaya (%)','2'],
                                    ['jp_hutang[]','JP Hutang (%)','1'],
                                ] as [$fname, $flabel, $fph])
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $flabel }}</label>
                                    <input type="number" name="{{ $fname }}" class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                           step="0.01" min="0" placeholder="{{ $fph }}">
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Keterangan / Rumus Custom</label>
                            <textarea name="keterangan_custom[]" class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                      rows="2" placeholder="Cth: Maksimal (2 Orang Tua 3 Anak @ 35.000) / 2"></textarea>
                            <p class="text-xs text-gray-400 mt-1">Kosongkan kolom persentase jika menggunakan perhitungan custom.</p>
                        </div>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    <button type="button" onclick="closeCreateModal()"
                            class="px-5 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition">
                        <i class="fas fa-save mr-1.5"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════
     Modal Form Edit
     ════════════════════════════════════════════════════════════════ --}}
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-100 text-blue-600">
                        <i class="fas fa-edit text-sm"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Edit Rumus BPJS</h3>
                        <p class="text-xs text-gray-400">Ubah data rumus perhitungan iuran</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditModal()"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="px-6 py-4 max-h-[70vh] overflow-y-auto space-y-4">

                    {{-- Jenis --}}
                    <div>
                        <label for="edit_jenis" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Jenis BPJS</label>
                        <select id="edit_jenis" name="jenis" class="form-select w-full border-gray-300 rounded-lg shadow-sm text-sm" required
                                onchange="toggleEditJenisFields(this)">
                            <option value="jkn">Group JKN</option>
                            <option value="jamsostek">Group BP Jamsostek</option>
                        </select>
                    </div>

                    {{-- JKN Fields Edit (hanya muncul saat jenis = jkn) --}}
                    <div id="edit_jkn_fields">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="edit_tunjangan_persen_jkn" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Tunjangan (%)</label>
                                <div class="relative">
                                    <input type="number" id="edit_tunjangan_persen_jkn" name="tunjangan_persen"
                                           class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm pr-8"
                                           step="0.01" min="0" placeholder="Cth: 1">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 text-sm">%</span>
                                </div>
                            </div>
                            <div>
                                <label for="edit_hutang_persen_jkn" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Hutang (%)</label>
                                <div class="relative">
                                    <input type="number" id="edit_hutang_persen_jkn" name="hutang_persen"
                                           class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm pr-8"
                                           step="0.01" min="0" placeholder="Cth: 1">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 text-sm">%</span>
                                </div>
                            </div>
                            <div>
                                <label for="edit_biaya_persen_jkn" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Biaya (%)</label>
                                <div class="relative">
                                    <input type="number" id="edit_biaya_persen_jkn" name="biaya_persen"
                                           class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm pr-8"
                                           step="0.01" min="0" placeholder="Cth: 1">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 text-sm">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Nama & Cabang --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_group_name" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Nama Group</label>
                            <input type="text" id="edit_group_name" name="group_name"
                                   class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                   required placeholder="Cth: JKN-KIS-HARIAN" oninput="toggleEditPpuFields(this)">
                        </div>
                        <div>
                            <label for="edit_cabang_bpjs" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Cabang BPJS</label>
                            <input type="text" id="edit_cabang_bpjs" name="cabang_bpjs"
                                   class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                   placeholder="Cth: Batam / Jakarta">
                        </div>
                    </div>

                    {{-- BPU Fields (edit) --}}
                    <div id="edit_bpu_fields">

                        {{-- Tunjangan + DPP + Biaya --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="edit_tunjangan_persen" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Tunjangan JKK 1% (%)</label>
                                <div class="relative">
                                    <input type="number" id="edit_tunjangan_persen" name="tunjangan_persen"
                                           class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm pr-8"
                                           step="0.01" min="0" placeholder="Cth: 1">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 text-sm">%</span>
                                </div>
                            </div>
                            <div>
                                <label for="edit_biaya_persen" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Biaya (% / Rp)</label>
                                <input type="number" id="edit_biaya_persen" name="biaya_persen"
                                       class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                       step="0.01" min="0" placeholder="Cth: 5 atau 6800">
                            </div>
                        </div>

                        {{-- Hutang DPP Edit --}}
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                Hutang (Rp) — Tabel DPP
                                <span class="ml-1 normal-case font-normal text-gray-400">(DPP → Potongan)</span>
                            </label>
                            <input type="hidden" id="edit_hutang_tiers" name="hutang_tiers">
                            <div class="border border-indigo-100 rounded-xl bg-indigo-50/40 p-3">
                                <div id="edit-hutang-tiers-container" class="space-y-2 mb-2">
                                    {{-- filled by JS --}}
                                </div>
                                <button type="button" onclick="editAddTierRow()"
                                        class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition">
                                    <i class="fas fa-plus"></i> Tambah Baris DPP
                                </button>
                            </div>
                        </div>

                        {{-- Diskon (edit) --}}
                        <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 mb-4">
                            <label class="block text-xs font-bold text-amber-700 mb-2 uppercase tracking-wide">Variabel Diskon</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label for="edit_diskon_status" class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                                    <select id="edit_diskon_status" name="diskon_status"
                                            class="form-select w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                            onchange="toggleEditDiskonInputs(this)">
                                        <option value="tidak_ada">Tidak Ada (0%)</option>
                                        <option value="ada">Ada Diskon</option>
                                    </select>
                                </div>
                                <div id="edit_diskon_nilai_wrapper" class="hidden">
                                    <label for="edit_diskon_nilai" class="block text-xs font-semibold text-gray-600 mb-1">Nilai Diskon</label>
                                    <input type="number" id="edit_diskon_nilai" name="diskon_nilai"
                                           class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                           step="0.01" min="0" placeholder="Cth: 50">
                                </div>
                                <div id="edit_diskon_tipe_wrapper" class="hidden">
                                    <label for="edit_diskon_tipe" class="block text-xs font-semibold text-gray-600 mb-1">Tipe Diskon</label>
                                    <select id="edit_diskon_tipe" name="diskon_tipe"
                                            class="form-select w-full border-gray-300 rounded-lg shadow-sm text-sm">
                                        <option value="persen">Persentase (%)</option>
                                        <option value="nominal">Nominal (Rp)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PPU Fields (edit) --}}
                    <div id="edit_ppu_fields" class="hidden bg-emerald-50 border border-emerald-100 rounded-xl p-4 mb-4">
                        <p class="text-xs font-bold text-emerald-700 mb-3 uppercase tracking-wide">Variabel PPU Jamsostek</p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach([
                                ['edit_jht_biaya','jht_biaya','JHT Biaya (%)','3.7'],
                                ['edit_jht_hutang','jht_hutang','JHT Hutang (%)','2'],
                                ['edit_jkk_tunjangan','jkk_tunjangan','JKK Tunjangan (%)','0.24'],
                                ['edit_jkm_tunjangan','jkm_tunjangan','JKM Tunjangan (%)','0.3'],
                                ['edit_jp_biaya','jp_biaya','JP Biaya (%)','2'],
                                ['edit_jp_hutang','jp_hutang','JP Hutang (%)','1'],
                            ] as [$fid, $fname, $flabel, $fph])
                            <div>
                                <label for="{{ $fid }}" class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $flabel }}</label>
                                <input type="number" id="{{ $fid }}" name="{{ $fname }}"
                                       class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                       step="0.01" min="0" placeholder="{{ $fph }}">
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Keterangan (edit) --}}
                    <div>
                        <label for="edit_keterangan_custom" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Keterangan / Rumus Custom</label>
                        <textarea id="edit_keterangan_custom" name="keterangan_custom"
                                  class="form-input w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                  rows="2" placeholder="Cth: Maksimal (2 Orang Tua 3 Anak @ 35.000) / 2"></textarea>
                        <p class="text-xs text-gray-400 mt-1">Kosongkan kolom persentase jika group ini menggunakan perhitungan custom / manual.</p>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    <button type="button" onclick="closeEditModal()"
                            class="px-5 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition">
                        <i class="fas fa-save mr-1.5"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ── Modal Open/Close ──────────────────────────────────────────────────────

    function openModal() {
        const container = document.getElementById('dynamic-rows-container');
        const rows = container.getElementsByClassName('bpjs-row');
        while(rows.length > 1) rows[1].remove();

        const firstRow = rows[0];
        firstRow.querySelector('select[name="jenis[]"]').value = 'jkn';
        firstRow.querySelector('input[name="group_name[]"]').value = '';
        firstRow.querySelector('input[name="cabang_bpjs[]"]').value = '';
        firstRow.querySelector('input[name="tunjangan_persen[]"]').value = '';
        firstRow.querySelector('input[name="biaya_persen[]"]').value = '';
        firstRow.querySelector('textarea[name="keterangan_custom[]"]').value = '';
        firstRow.querySelector('input[name="jht_biaya[]"]').value = '';
        firstRow.querySelector('input[name="jht_hutang[]"]').value = '';
        firstRow.querySelector('input[name="jkk_tunjangan[]"]').value = '';
        firstRow.querySelector('input[name="jkm_tunjangan[]"]').value = '';
        firstRow.querySelector('input[name="jp_biaya[]"]').value = '';
        firstRow.querySelector('input[name="jp_hutang[]"]').value = '';
        firstRow.querySelector('select[name="diskon_status[]"]').value = 'tidak_ada';
        firstRow.querySelector('input[name="diskon_nilai[]"]').value = '';
        firstRow.querySelector('select[name="diskon_tipe[]"]').value = 'persen';
        toggleDiskonInputs(firstRow.querySelector('select[name="diskon_status[]"]'));

        const tiersContainer = firstRow.querySelector('.hutang-tiers-container');
        if (tiersContainer) {
            tiersContainer.innerHTML = '';
            tiersContainer.appendChild(buildTierRow('', ''));
            updateTierRemoveBtns(tiersContainer);
        }
        const tiersJson = firstRow.querySelector('.hutang-tiers-json');
        if (tiersJson) tiersJson.value = '';

        toggleJenisFields(firstRow.querySelector('select[name="jenis[]"]'));
        togglePpuFields(firstRow.querySelector('input[name="group_name[]"]'));
        updateRemoveButtons();
        document.getElementById('createModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function addRow() {
        const container = document.getElementById('dynamic-rows-container');
        const firstRow = container.querySelector('.bpjs-row');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelector('select[name="jenis[]"]').value = 'jkn';
        newRow.querySelector('input[name="group_name[]"]').value = '';
        newRow.querySelector('input[name="cabang_bpjs[]"]').value = '';
        newRow.querySelector('input[name="tunjangan_persen[]"]').value = '';
        newRow.querySelector('input[name="biaya_persen[]"]').value = '';
        newRow.querySelector('textarea[name="keterangan_custom[]"]').value = '';
        newRow.querySelector('input[name="jht_biaya[]"]').value = '';
        newRow.querySelector('input[name="jht_hutang[]"]').value = '';
        newRow.querySelector('input[name="jkk_tunjangan[]"]').value = '';
        newRow.querySelector('input[name="jkm_tunjangan[]"]').value = '';
        newRow.querySelector('input[name="jp_biaya[]"]').value = '';
        newRow.querySelector('input[name="jp_hutang[]"]').value = '';
        newRow.querySelector('select[name="diskon_status[]"]').value = 'tidak_ada';
        newRow.querySelector('input[name="diskon_nilai[]"]').value = '';
        newRow.querySelector('select[name="diskon_tipe[]"]').value = 'persen';
        toggleDiskonInputs(newRow.querySelector('select[name="diskon_status[]"]'));

        const tiersContainer = newRow.querySelector('.hutang-tiers-container');
        if (tiersContainer) {
            tiersContainer.innerHTML = '';
            tiersContainer.appendChild(buildTierRow('', ''));
            updateTierRemoveBtns(tiersContainer);
        }

        container.appendChild(newRow);
        toggleJenisFields(newRow.querySelector('select[name="jenis[]"]'));
        togglePpuFields(newRow.querySelector('input[name="group_name[]"]'));
        updateRemoveButtons();
    }

    function removeRow(btn) {
        const row = btn.closest('.bpjs-row');
        const container = document.getElementById('dynamic-rows-container');
        if (container.querySelectorAll('.bpjs-row').length > 1) {
            row.remove();
            updateRemoveButtons();
        }
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.bpjs-row');
        rows.forEach(row => {
            const btn = row.querySelector('.remove-row-btn');
            if (rows.length > 1) btn.classList.remove('hidden');
            else btn.classList.add('hidden');
        });
    }

    // ── Edit Modal ────────────────────────────────────────────────────────────

    function editModal(data) {
        document.getElementById('editForm').action = `/master-rumus-bpjs/${data.id}`;
        document.getElementById('edit_jenis').value = data.jenis;
        document.getElementById('edit_group_name').value = data.group_name;
        document.getElementById('edit_cabang_bpjs').value = data.cabang_bpjs || '';
        document.getElementById('edit_tunjangan_persen').value = data.tunjangan_persen || '';
        document.getElementById('edit_biaya_persen').value = data.biaya_persen || '';
        document.getElementById('edit_keterangan_custom').value = data.keterangan_custom || '';
        document.getElementById('edit_jht_biaya').value = data.jht_biaya || '';
        document.getElementById('edit_jht_hutang').value = data.jht_hutang || '';
        document.getElementById('edit_jkk_tunjangan').value = data.jkk_tunjangan || '';
        document.getElementById('edit_jkm_tunjangan').value = data.jkm_tunjangan || '';
        document.getElementById('edit_jp_biaya').value = data.jp_biaya || '';
        document.getElementById('edit_jp_hutang').value = data.jp_hutang || '';
        document.getElementById('edit_diskon_status').value = data.diskon_status || 'tidak_ada';
        document.getElementById('edit_diskon_nilai').value = data.diskon_nilai ? parseFloat(data.diskon_nilai) : '';
        document.getElementById('edit_diskon_tipe').value = data.diskon_tipe || 'persen';
        toggleEditDiskonInputs(document.getElementById('edit_diskon_status'));

        var container = document.getElementById('edit-hutang-tiers-container');
        container.innerHTML = '';
        var tiers = data.hutang_tiers;
        if (typeof tiers === 'string') { try { tiers = JSON.parse(tiers); } catch(e) { tiers = []; } }
        if (!tiers || !tiers.length) tiers = [{ dpp: '', potongan: '' }];
        tiers.forEach(function(t) { editAddTierRow(t.dpp, t.potongan); });
        syncEditTiersJson();

        // Populate JKN fields
        document.getElementById('edit_tunjangan_persen_jkn').value = data.tunjangan_persen || '';
        document.getElementById('edit_hutang_persen_jkn').value   = data.hutang_persen || '';
        document.getElementById('edit_biaya_persen_jkn').value    = data.biaya_persen || '';

        toggleEditJenisFields(document.getElementById('edit_jenis'));
        toggleEditPpuFields(document.getElementById('edit_group_name'));
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // ── Toggle PPU / Diskon ───────────────────────────────────────────────────

    function toggleDiskonInputs(select) {
        const bpuRow = select.closest('.bpu-fields');
        if (!bpuRow) return;
        const wrapper = bpuRow.querySelector('.diskon-nilai-wrapper');
        if (select.value === 'ada') wrapper?.classList.remove('hidden');
        else wrapper?.classList.add('hidden');
    }

    function toggleEditDiskonInputs(select) {
        const show = select.value === 'ada';
        document.getElementById('edit_diskon_nilai_wrapper')?.classList.toggle('hidden', !show);
        document.getElementById('edit_diskon_tipe_wrapper')?.classList.toggle('hidden', !show);
    }

    function togglePpuFields(input) {
        const row = input.closest('.bpjs-row');
        const jenis = row.querySelector('select[name="jenis[]"]')?.value;
        if (jenis !== 'jamsostek') return; // hanya berlaku saat jamsostek
        const ppu = row.querySelector('.ppu-fields');
        const bpu = row.querySelector('.bpu-fields');
        const isPpu = input.value.toUpperCase().includes('PPU');
        ppu.classList.toggle('hidden', !isPpu);
        bpu.classList.toggle('hidden', isPpu);
    }

    function toggleEditPpuFields(input) {
        const jenis = document.getElementById('edit_jenis')?.value;
        if (jenis !== 'jamsostek') return;
        const isPpu = input.value.toUpperCase().includes('PPU');
        document.getElementById('edit_ppu_fields')?.classList.toggle('hidden', !isPpu);
        document.getElementById('edit_bpu_fields')?.classList.toggle('hidden', isPpu);
    }

    /** Toggle antara JKN fields dan Jamsostek fields (Create modal) */
    function toggleJenisFields(select) {
        const row = select.closest('.bpjs-row');
        const jknFields = row.querySelector('.jkn-fields');
        const bpuFields = row.querySelector('.bpu-fields');
        const ppuFields = row.querySelector('.ppu-fields');
        const isJamsostek = select.value === 'jamsostek';
        jknFields?.classList.toggle('hidden',  isJamsostek);
        bpuFields?.classList.toggle('hidden', !isJamsostek);
        if (ppuFields) ppuFields.classList.add('hidden'); // reset PPU
        // Jika jamsostek, cek PPU dari nama group
        if (isJamsostek) {
            const groupInput = row.querySelector('input[name="group_name[]"]');
            if (groupInput) togglePpuFields(groupInput);
        }
    }

    /** Toggle antara JKN fields dan Jamsostek fields (Edit modal) */
    function toggleEditJenisFields(select) {
        const isJamsostek = select.value === 'jamsostek';
        document.getElementById('edit_jkn_fields')?.classList.toggle('hidden',  isJamsostek);
        document.getElementById('edit_bpu_fields')?.classList.toggle('hidden', !isJamsostek);
        if (!isJamsostek) {
            // Reset PPU fields saat JKN
            document.getElementById('edit_ppu_fields')?.classList.add('hidden');
        } else {
            // Cek PPU dari nama group
            toggleEditPpuFields(document.getElementById('edit_group_name'));
        }
    }

    // ── Table Diskon AJAX ─────────────────────────────────────────────────────

    function handleTableDiskonStatusChange(selectEl, id) {
        const cell = document.getElementById('diskon-cell-' + id);
        const inputsDiv = cell.querySelector('.diskon-inputs');
        if (selectEl.value === 'ada') {
            inputsDiv.classList.remove('hidden');
            selectEl.classList.add('bg-amber-50', 'text-amber-900', 'border-amber-300', 'font-semibold');
            selectEl.classList.remove('bg-white', 'text-gray-600');
        } else {
            inputsDiv.classList.add('hidden');
            selectEl.classList.remove('bg-amber-50', 'text-amber-900', 'border-amber-300', 'font-semibold');
            selectEl.classList.add('bg-white', 'text-gray-600');
        }
        saveDiskonAjax(id, cell);
    }

    function handleTableDiskonValueChange(inputEl, id) {
        saveDiskonAjax(id, document.getElementById('diskon-cell-' + id));
    }

    function handleTableDiskonTipeChange(selectEl, id) {
        saveDiskonAjax(id, document.getElementById('diskon-cell-' + id));
    }

    function saveDiskonAjax(id, cell) {
        const status = cell.querySelector('.diskon-status-select').value;
        const nilai  = cell.querySelector('.diskon-nilai').value;
        const tipe   = cell.querySelector('.diskon-tipe').value;
        const statusMsg = cell.querySelector('.save-status-msg');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch(`/master-rumus-bpjs/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ _method: 'PUT', diskon_status: status, diskon_nilai: status === 'ada' ? (nilai || 0) : 0, diskon_tipe: tipe })
        })
        .then(r => { if (!r.ok) throw new Error(); return r.json(); })
        .then(() => {
            if (statusMsg) {
                statusMsg.classList.remove('hidden');
                setTimeout(() => statusMsg.classList.add('hidden'), 2000);
            }
        })
        .catch(() => alert('Gagal menyimpan perubahan diskon.'));
    }

    // ── Hutang Tiers — Create Modal ───────────────────────────────────────────

    function buildTierRow(dpp, potongan) {
        var row = document.createElement('div');
        row.className = 'hutang-tier-row grid items-end gap-2';
        row.style.gridTemplateColumns = '1fr auto 1fr auto';
        row.innerHTML =
            '<div>' +
            '  <label class="block text-[10px] font-semibold text-gray-500 mb-1 uppercase tracking-wide">DPP (Rp)</label>' +
            '  <input type="number" class="tier-dpp w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-right focus:ring-indigo-500 focus:border-indigo-500"' +
            '         step="1" min="0" placeholder="0" value="' + (dpp || '') + '" oninput="syncTiersJson(this)">' +
            '</div>' +
            '<div class="flex items-center pb-1.5"><i class="fas fa-arrow-right text-indigo-300 text-xs"></i></div>' +
            '<div>' +
            '  <label class="block text-[10px] font-semibold text-gray-500 mb-1 uppercase tracking-wide">Potongan (Rp)</label>' +
            '  <input type="number" class="tier-potongan w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-right focus:ring-indigo-500 focus:border-indigo-500"' +
            '         step="1" min="0" placeholder="0" value="' + (potongan || '') + '" oninput="syncTiersJson(this)">' +
            '</div>' +
            '<div class="flex items-center pb-1.5">' +
            '  <button type="button" onclick="removeTierRow(this)"' +
            '          class="remove-tier-btn w-7 h-7 flex items-center justify-center rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition" title="Hapus baris">' +
            '    <i class="fas fa-times text-xs"></i>' +
            '  </button>' +
            '</div>';
        return row;
    }

    function syncTiersJson(el) {
        var bpjsRow = el.closest('.bpjs-row');
        if (!bpjsRow) return;
        var tiers = [];
        bpjsRow.querySelectorAll('.hutang-tier-row').forEach(function(row) {
            var dpp      = row.querySelector('.tier-dpp')?.value;
            var potongan = row.querySelector('.tier-potongan')?.value;
            tiers.push({ dpp: dpp ? parseFloat(dpp) : null, potongan: potongan ? parseFloat(potongan) : null });
        });
        var hiddenInput = bpjsRow.querySelector('.hutang-tiers-json');
        if (hiddenInput) hiddenInput.value = JSON.stringify(tiers);
    }

    function addTierRow(addBtn) {
        var container = addBtn.closest('.border').querySelector('.hutang-tiers-container');
        var newRow = buildTierRow('', '');
        container.appendChild(newRow);
        updateTierRemoveBtns(container);
    }

    function removeTierRow(btn) {
        var row = btn.closest('.hutang-tier-row');
        var container = row.closest('.hutang-tiers-container, #edit-hutang-tiers-container');
        row.remove();
        if (container) {
            updateTierRemoveBtns(container);
            var firstInput = container.querySelector('.tier-dpp');
            if (firstInput) {
                if (container.id === 'edit-hutang-tiers-container') syncEditTiersJson();
                else syncTiersJson(firstInput);
            }
        }
    }

    function updateTierRemoveBtns(container) {
        var rows = container.querySelectorAll('.hutang-tier-row');
        rows.forEach(function(row) {
            var btn = row.querySelector('.remove-tier-btn');
            if (btn) btn.classList.toggle('hidden', rows.length <= 1);
        });
    }

    // ── Hutang Tiers — Edit Modal ─────────────────────────────────────────────

    function editAddTierRow(dpp, potongan) {
        var container = document.getElementById('edit-hutang-tiers-container');
        var newRow = buildTierRow(dpp || '', potongan || '');
        newRow.querySelectorAll('.tier-dpp, .tier-potongan').forEach(function(inp) {
            inp.setAttribute('oninput', 'syncEditTiersJson()');
        });
        container.appendChild(newRow);
        updateTierRemoveBtns(container);
        syncEditTiersJson();
    }

    function syncEditTiersJson() {
        var container = document.getElementById('edit-hutang-tiers-container');
        var tiers = [];
        container.querySelectorAll('.hutang-tier-row').forEach(function(row) {
            var dpp      = row.querySelector('.tier-dpp')?.value;
            var potongan = row.querySelector('.tier-potongan')?.value;
            tiers.push({ dpp: dpp ? parseFloat(dpp) : null, potongan: potongan ? parseFloat(potongan) : null });
        });
        var hidden = document.getElementById('edit_hutang_tiers');
        if (hidden) hidden.value = JSON.stringify(tiers);
    }
</script>
@endsection
