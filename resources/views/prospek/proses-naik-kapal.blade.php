@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-clipboard-check mr-3 text-green-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Proses Prospek</h1>
                    <p class="text-gray-600">Tujuan: <span class="font-semibold text-green-600">{{ $tujuan->nama }}</span></p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('prospek.pilih-tujuan') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Pilih Tujuan Lain
                </a>
                <a href="{{ route('prospek.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Prospek
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    <div id="alert-container">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                    </div>
                    <div>
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle mr-2 mt-1"></i>
                    </div>
                    <div>
                        <p class="font-bold">Terjadi kesalahan:</p>
                        <ul class="mt-1 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($prospeksAktif->count() > 0)
        {{-- Form Naik Kapal --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 bg-blue-500 text-white rounded-t-lg">
                <h3 class="text-lg font-semibold">Tambah Data</h3>
            </div>
            
            <div class="p-6">
                <form action="{{ route('prospek.execute-naik-kapal') }}" method="POST" id="naikKapalForm">
                    @csrf
                    <input type="hidden" name="tujuan_id" value="{{ $tujuanId }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Tanggal --}}
                        <div>
                            <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="tanggal" 
                                   name="tanggal"
                                   value="{{ old('tanggal', date('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tanggal') border-red-500 @enderror"
                                   required>
                            @error('tanggal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kapal --}}
                        <div>
                            <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Kapal <span class="text-red-500">*</span>
                            </label>
                            <select id="kapal_id" 
                                    name="kapal_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kapal_id') border-red-500 @enderror"
                                    required>
                                <option value="">--Pilih Kapal--</option>
                                @foreach($masterKapals as $kapal)
                                    <option value="{{ $kapal->id }}" {{ old('kapal_id') == $kapal->id ? 'selected' : '' }}>
                                        {{ $kapal->nama_kapal }} ({{ $kapal->nickname ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('kapal_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- No Voyage --}}
                        <div>
                            <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">
                                No Voyage <span class="text-red-500">*</span>
                            </label>
                            <select id="no_voyage" 
                                    name="no_voyage"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('no_voyage') border-red-500 @enderror"
                                    required>
                                <option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>
                            </select>
                            @error('no_voyage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- No Kontainer dan Seal (Modal Select) --}}
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                No Kontainer dan Seal <span class="text-red-500">*</span>
                            </label>
                            
                            {{-- Hidden inputs for selected values --}}
                            <div id="hidden_inputs"></div>
                            
                            {{-- Modal Trigger Button --}}
                            <div class="flex gap-2">
                                <button type="button" 
                                        onclick="openProspekModal()" 
                                        id="prospek_trigger_btn"
                                        class="flex-1 flex items-center justify-between px-4 py-2.5 border border-blue-300 rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100 transition duration-200 group focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <div class="flex items-center overflow-hidden">
                                        <i class="fas fa-search mr-3 text-blue-500 group-hover:scale-110 transition-transform duration-200"></i>
                                        <span id="pilihStatusText" class="truncate font-medium text-sm">-- Klik untuk Pilih Kontainer - Seal --</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-xs opacity-50"></i>
                                </button>
                                <button type="button" 
                                        id="clearAllBtn"
                                        title="Hapus semua pilihan"
                                        class="bg-white hover:bg-red-50 text-gray-400 hover:text-red-600 px-3 py-2 rounded-md border border-gray-300 hover:border-red-300 transition duration-200">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            
                            <div class="mt-2 flex justify-between items-center px-1">
                                <span id="selectedCount" class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                    Terpilih: 0 prospek
                                </span>
                                <span class="text-[10px] text-gray-500 italic">Total tersedia: {{ $prospeksAktif->where('tipe', '!=', 'CARGO')->count() }}</span>
                            </div>

                            {{-- Modal Pilih Kontainer --}}
                            <div id="prospekModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                    <!-- Background overlay -->
                                    <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeProspekModal()"></div>
                                    
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    
                                    <!-- Modal panel -->
                                    <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full border-t-8 border-blue-600">
                                        <!-- Modal Header -->
                                        <div class="bg-blue-600 px-6 py-4 flex justify-between items-center sticky top-0 z-10">
                                            <div class="flex items-center">
                                                <i class="fas fa-shipping-fast text-white mr-3 text-xl"></i>
                                                <div>
                                                    <h3 class="text-lg font-bold text-white" id="modal-title">Pilih Kontainer & Seal</h3>
                                                    <p class="text-blue-100 text-xs">Pilih satu atau beberapa kontainer untuk diproses</p>
                                                </div>
                                            </div>
                                            <button type="button" class="text-white hover:text-blue-200 transition-colors p-2" onclick="closeProspekModal()">
                                                <i class="fas fa-times text-xl"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Modal Content -->
                                        <div class="bg-white p-6">
                                            <!-- Bulk Input Textarea -->
                                            <div class="mb-6 bg-blue-50 p-4 rounded-xl border border-blue-100 shadow-sm">
                                                <div class="flex justify-between items-center mb-2">
                                                    <label class="text-[10px] uppercase font-bold text-blue-600 flex items-center">
                                                        <i class="fas fa-paste mr-2"></i>Input Massal (Salin/Tempel Daftar Kontainer)
                                                    </label>
                                                    <span class="text-[9px] text-gray-500 italic">Pisahkan dengan titik koma (;) atau baris baru</span>
                                                </div>
                                                <div class="flex flex-col sm:flex-row gap-3">
                                                    <textarea id="bulkInputText" 
                                                              rows="2" 
                                                              placeholder="Contoh: AYPU1234567; AYPU7654321; ..." 
                                                              class="flex-1 px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm placeholder:text-blue-200"></textarea>
                                                    <button type="button" 
                                                            onclick="processBulkInput()" 
                                                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold transition-all flex items-center justify-center min-h-[50px]">
                                                        <i class="fas fa-check-circle mr-2"></i>Proses & Pilih
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="relative flex items-center py-2 mb-4">
                                                <div class="flex-grow border-t border-gray-100"></div>
                                                <span class="flex-shrink mx-4 text-[10px] text-gray-300 font-bold uppercase tracking-widest">Atau Pilih Manual</span>
                                                <div class="flex-grow border-t border-gray-100"></div>
                                            </div>

                                            <!-- Filter and Search -->
                                            <div class="flex flex-col sm:flex-row gap-3 mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                                                <div class="flex-1 relative">
                                                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Cari Prospek</label>
                                                    <input type="text" 
                                                           id="modalSearch" 
                                                           placeholder="Cari no kontainer, seal, supir, pengirim, atau barang..." 
                                                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                                    <i class="fas fa-search absolute left-3 top-[34px] text-gray-400"></i>
                                                </div>
                                                <div class="flex items-end gap-2">
                                                    <button type="button" 
                                                            onclick="toggleSelectAllInModal()" 
                                                            id="modalSelectAllBtn"
                                                            class="bg-blue-100 text-blue-700 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200 whitespace-nowrap h-[42px] border border-blue-200">
                                                        <i class="fas fa-check-double mr-2"></i>Select All Visible
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Table List -->
                                            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                                                <div class="overflow-y-auto max-h-[400px]">
                                                    <table class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
                                                            <tr>
                                                                <th class="px-4 py-3 text-left">
                                                                    <div class="flex items-center">
                                                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pilih</span>
                                                                    </div>
                                                                </th>
                                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No Kontainer - Seal</th>
                                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe</th>
                                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Supir</th>
                                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="modalTableBody" class="bg-white divide-y divide-gray-100">
                                                            @foreach($prospeksAktif as $prospek)
                                                                @php
                                                                    $suratJalanInfo = $prospek->no_surat_jalan ?? '';
                                                                    if (strtoupper($prospek->tipe ?? '') === 'CARGO') {
                                                                        $displayText = $suratJalanInfo ?: 'CARGO #' . $prospek->id;
                                                                    } elseif ($prospek->nomor_kontainer) {
                                                                        $displayText = $prospek->no_seal 
                                                                            ? $prospek->nomor_kontainer . ' - ' . $prospek->no_seal 
                                                                            : $prospek->nomor_kontainer;
                                                                    } else {
                                                                        $displayText = 'ID #' . $prospek->id . ' - ' . strtoupper($prospek->tipe ?? 'N/A');
                                                                    }
                                                                @endphp
                                                                <tr class="modal-prospek-row hover:bg-blue-50 cursor-pointer transition-colors"
                                                                    data-id="{{ $prospek->id }}"
                                                                    data-text="{{ $displayText }}"
                                                                    data-tipe="{{ $prospek->tipe }}"
                                                                    data-supir="{{ $prospek->nama_supir }}"
                                                                    data-tanggal="{{ $prospek->created_at ? $prospek->created_at->format('d/m/Y') : '-' }}"
                                                                    data-pengirim="{{ $prospek->pt_pengirim ?? '-' }}"
                                                                    data-barang="{{ $prospek->barang ?? '-' }}"
                                                                    onclick="toggleProspekSelection('{{ $prospek->id }}')">
                                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                                        <div class="flex items-center">
                                                                            <input type="checkbox" 
                                                                                   id="checkbox_{{ $prospek->id }}" 
                                                                                   class="prospek-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                                                                                   onclick="event.stopPropagation()">
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                                        <div class="text-sm font-bold text-gray-900">{{ $displayText }}</div>
                                                                        @if($suratJalanInfo)
                                                                            <div class="text-[10px] text-gray-500 uppercase">SJ: {{ $suratJalanInfo }}</div>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $prospek->tipe === 'CARGO' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
                                                                            {{ $prospek->tipe }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $prospek->nama_supir }}</td>
                                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 font-medium">{{ $prospek->created_at ? $prospek->created_at->format('d/m/Y') : '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Modal Footer -->
                                        <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-between items-center border-t gap-4">
                                            <div class="flex items-center">
                                                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                                    <i class="fas fa-info-circle text-blue-600"></i>
                                                </div>
                                                <span id="modalSelectedSummary" class="text-sm font-bold text-gray-700">0 kontainer terpilih</span>
                                            </div>
                                            <button type="button" 
                                                    onclick="closeProspekModal()" 
                                                    class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-lg font-bold shadow-lg shadow-blue-200 transform hover:scale-105 transition-all">
                                                Simpan & Selesai
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @error('prospek_ids')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tujuan Kirim Asal --}}
                        <div>
                            <label for="pelabuhan_asal" class="block text-sm font-medium text-gray-700 mb-2">
                                Asal <span class="text-red-500">*</span>
                            </label>
                            <select id="pelabuhan_asal" 
                                    name="pelabuhan_asal"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('pelabuhan_asal') border-red-500 @enderror"
                                    required>
                                <option value="">--Pilih ASAL--</option>
                                @foreach($masterTujuanKirims as $tujuanKirim)
                                    <option value="{{ $tujuanKirim->nama_tujuan }}" {{ old('pelabuhan_asal') == $tujuanKirim->nama_tujuan ? 'selected' : '' }}>
                                        {{ $tujuanKirim->nama_tujuan }} - {{ $tujuanKirim->kota }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pelabuhan_asal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tujuan (Read-only) --}}
                        <div>
                            <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-2">
                                Tujuan
                            </label>
                            <input type="text" 
                                   id="tujuan" 
                                   name="tujuan"
                                   value="{{ $tujuan->nama }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed"
                                   readonly>
                        </div>
                    </div>

                    {{-- Selected Prospeks Table --}}
                    <div id="selectedProspeksTable" class="mt-6 hidden">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-list mr-2"></i>
                                Kontainer Terpilih
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer - Seal</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PT. Pengirim</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedProspeksTableBody" class="divide-y divide-gray-200">
                                        <!-- Dynamic content will be inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="mt-6 flex gap-3">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                            Submit
                        </button>
                        <button type="button" 
                                id="exportExcelBtn"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <i class="fas fa-file-excel mr-2"></i>
                            Export Excel
                        </button>
                        <a href="{{ route('prospek.pilih-tujuan') }}" 
                           class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @else
        {{-- Tidak Ada Prospek --}}
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="flex flex-col items-center justify-center">
                <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Tidak Ada Prospek untuk {{ $tujuan->nama }}</h3>
                <p class="text-gray-600 mb-6">Belum ada prospek aktif yang memiliki tujuan pengiriman ke {{ $tujuan->nama }}</p>
                <div class="flex gap-3">
                    <a href="{{ route('prospek.pilih-tujuan') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Pilih Tujuan Lain
                    </a>
                    <a href="{{ route('prospek.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Prospek
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endpush

<style>
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translate3d(0, -10px, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
    
    .animate-fade-in-down {
        animation-name: fadeInDown;
        animation-duration: 0.3s;
        animation-fill-mode: both;
    }

    /* Table Styling */
    .table-row-prospek {
        transition: background-color 0.15s ease;
    }
    
    .table-row-prospek:hover {
        background-color: #f9fafb;
    }
    
    .table-row-prospek input[type="text"],
    .table-row-prospek select {
        min-width: 150px;
    }
    
    .table-row-prospek input[type="text"]:focus,
    .table-row-prospek select:focus {
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
</style>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Choices JS initialization
        const kapalEl = document.getElementById('kapal_id');
        if (kapalEl && typeof Choices !== 'undefined') {
            const choices = new Choices(kapalEl, {
                searchEnabled: true,
                shouldSort: false,
                searchPlaceholderValue: 'Cari nama kapal...'
            });
            kapalEl.choicesInstance = choices;
        }

        // Element references
        const kapalSelect = document.getElementById('kapal_id');
        const voyageSelect = document.getElementById('no_voyage');
        const prospekModal = document.getElementById('prospekModal');
        const modalSearch = document.getElementById('modalSearch');
        const modalTableBody = document.getElementById('modalTableBody');
        const modalRows = document.querySelectorAll('.modal-prospek-row');
        const selectedCountEl = document.getElementById('selectedCount');
        const modalSelectedSummary = document.getElementById('modalSelectedSummary');
        const pilihStatusText = document.getElementById('pilihStatusText');
        const hiddenInputs = document.getElementById('hidden_inputs');
        const selectedProspeksTable = document.getElementById('selectedProspeksTable');
        const selectedProspeksTableBody = document.getElementById('selectedProspeksTableBody');
        const exportExcelBtn = document.getElementById('exportExcelBtn');
        
        let selectedProspeks = [];

        // Kapal change listener
        if (kapalSelect) {
            kapalSelect.addEventListener('change', function() {
                const kapalId = this.value;
                voyageSelect.innerHTML = '<option value="">Loading...</option>';
                voyageSelect.disabled = true;
                
                if (!kapalId) {
                    voyageSelect.innerHTML = '<option value="">-PILIH KAPAL TERLEBIH DAHULU-</option>';
                    return;
                }
                
                fetch(`{{ route('prospek.get-voyage-by-kapal') }}?kapal_id=${kapalId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    voyageSelect.innerHTML = '';
                    if (data.success && data.voyages && data.voyages.length > 0) {
                        voyageSelect.innerHTML += '<option value="">-PILIH VOYAGE-</option>';
                        data.voyages.forEach(voyage => {
                            voyageSelect.innerHTML += `<option value="${voyage}">${voyage}</option>`;
                        });
                    } else {
                        voyageSelect.innerHTML = '<option value="">Belum ada voyage untuk kapal ini</option>';
                    }
                    voyageSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    voyageSelect.innerHTML = '<option value="">Error loading</option>';
                    voyageSelect.disabled = false;
                });
            });
        }

        // Modal functions
        window.openProspekModal = function() {
            prospekModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            if(modalSearch) modalSearch.focus();
            updateModalSummary();
        };

        window.closeProspekModal = function() {
            prospekModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            syncToMainForm();
        };

        window.processBulkInput = function() {
            const input = document.getElementById('bulkInputText').value;
            if (!input) return;

            const identifiers = input.split(/[;,\n\s]+/).map(s => s.trim().toLowerCase()).filter(s => s.length > 0);
            let matchedCount = 0;
            let notFound = [];

            identifiers.forEach(idnt => {
                let foundMatch = false;
                modalRows.forEach(row => {
                    const containerText = row.getAttribute('data-text').toLowerCase();
                    const id = row.getAttribute('data-id');
                    const checkbox = document.getElementById(`checkbox_${id}`);

                    if (containerText.includes(idnt)) {
                        if (!checkbox.checked) {
                            checkbox.checked = true;
                            row.classList.toggle('bg-blue-100', true);
                            matchedCount++;
                        }
                        foundMatch = true;
                    }
                });
                if (!foundMatch) notFound.push(idnt);
            });

            updateModalSummary();
            if (notFound.length > 0) {
                alert(`Berhasil memilih ${matchedCount} kontainer.\n\nTidak ditemukan: ${notFound.slice(0, 10).join(', ')}${notFound.length > 10 ? '...' : ''}`);
            } else {
                alert(`Berhasil memilih ${matchedCount} kontainer.`);
            }
        };

        window.toggleProspekSelection = function(id) {
            const checkbox = document.getElementById(`checkbox_${id}`);
            const row = document.querySelector(`.modal-prospek-row[data-id="${id}"]`);
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                row.classList.toggle('bg-blue-100', checkbox.checked);
                updateModalSummary();
            }
        };

        function updateModalSummary() {
            const count = Array.from(document.querySelectorAll('.prospek-checkbox')).filter(cb => cb.checked).length;
            if(modalSelectedSummary) modalSelectedSummary.textContent = `${count} kontainer terpilih`;
        }

        function syncToMainForm() {
            selectedProspeks = [];
            const checkboxes = document.querySelectorAll('.prospek-checkbox:checked');
            checkboxes.forEach(cb => {
                const id = cb.id.replace('checkbox_', '');
                const row = document.querySelector(`.modal-prospek-row[data-id="${id}"]`);
                selectedProspeks.push({
                    id: id, text: row.dataset.text, tipe: row.dataset.tipe,
                    supir: row.dataset.supir, tanggal: row.dataset.tanggal,
                    pengirim: row.dataset.pengirim, barang: row.dataset.barang
                });
            });
            renderMainFormUI();
        }

        function renderMainFormUI() {
            if(selectedCountEl) selectedCountEl.textContent = `Terpilih: ${selectedProspeks.length} prospek`;
            if(modalSelectedSummary) modalSelectedSummary.textContent = `${selectedProspeks.length} kontainer terpilih`;
            
            if(pilihStatusText) {
                if(selectedProspeks.length === 0) {
                    pilihStatusText.textContent = '-- Klik untuk Pilih Kontainer - Seal --';
                    pilihStatusText.className = 'truncate font-medium text-sm text-blue-500';
                } else {
                    pilihStatusText.textContent = selectedProspeks.length === 1 ? selectedProspeks[0].text : `${selectedProspeks.length} kontainer terpilih`;
                    pilihStatusText.className = 'truncate font-bold text-sm text-blue-700';
                }
            }

            hiddenInputs.innerHTML = '';
            selectedProspeks.forEach(p => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'prospek_ids[]';
                input.value = p.id;
                hiddenInputs.appendChild(input);
            });

            if(selectedProspeks.length > 0) {
                selectedProspeksTable.classList.remove('hidden');
                exportExcelBtn.disabled = false;
            } else {
                selectedProspeksTable.classList.add('hidden');
                exportExcelBtn.disabled = true;
            }

            selectedProspeksTableBody.innerHTML = '';
            selectedProspeks.forEach((p, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 font-medium';
                tr.innerHTML = `
                    <td class="px-4 py-2 text-xs font-medium text-gray-500">${index + 1}</td>
                    <td class="px-4 py-2 text-xs font-bold text-gray-900">${p.text}</td>
                    <td class="px-4 py-2 text-xs text-center"><span class="px-2 py-0.5 rounded-full ${p.tipe === 'CARGO' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'} text-[10px] font-bold">${p.tipe}</span></td>
                    <td class="px-4 py-2 text-xs text-gray-600">${p.pengirim}</td>
                    <td class="px-4 py-2 text-xs text-gray-600">${p.barang}</td>
                    <td class="px-4 py-2 text-xs text-gray-600">${p.supir}</td>
                    <td class="px-4 py-2 text-xs text-gray-500">${p.tanggal}</td>
                    <td class="px-4 py-2 text-xs text-right"><button type="button" onclick="removeFromSelection('${p.id}')" class="text-red-500 hover:text-red-700 transition-colors"><i class="fas fa-trash-alt"></i></button></td>
                `;
                selectedProspeksTableBody.appendChild(tr);
            });
        }

        window.removeFromSelection = function(id) {
            const cb = document.getElementById(`checkbox_${id}`);
            if(cb) cb.checked = false;
            const row = document.querySelector(`.modal-prospek-row[data-id="${id}"]`);
            if(row) row.classList.remove('bg-blue-100');
            syncToMainForm();
        };

        if (modalSearch) {
            modalSearch.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                modalRows.forEach(row => {
                    const text = row.getAttribute('data-text').toLowerCase();
                    const supir = row.getAttribute('data-supir').toLowerCase();
                    const pengirim = row.getAttribute('data-pengirim').toLowerCase();
                    const barang = row.getAttribute('data-barang').toLowerCase();
                    row.style.display = (text.includes(term) || supir.includes(term) || pengirim.includes(term) || barang.includes(term)) ? '' : 'none';
                });
            });
        }

        window.toggleSelectAllInModal = function() {
            const visibleRows = Array.from(modalRows).filter(row => row.style.display !== 'none');
            const allVisibleChecked = visibleRows.every(row => document.getElementById(`checkbox_${row.dataset.id}`).checked);
            visibleRows.forEach(row => {
                const id = row.dataset.id;
                const checkbox = document.getElementById(`checkbox_${id}`);
                checkbox.checked = !allVisibleChecked;
                row.classList.toggle('bg-blue-100', checkbox.checked);
            });
            updateModalSummary();
        };

        const clearAllBtn = document.getElementById('clearAllBtn');
        if(clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                if(!confirm('Hapus semua pilihan?')) return;
                document.querySelectorAll('.prospek-checkbox').forEach(cb => cb.checked = false);
                modalRows.forEach(row => row.classList.remove('bg-blue-100'));
                syncToMainForm();
            });
        }

        const form = document.getElementById('naikKapalForm');
        if(form) {
            form.addEventListener('submit', function(e) {
                if (selectedProspeks.length === 0) { e.preventDefault(); alert('Pilih kontainer!'); return; }
                if (!kapalSelect.value || !voyageSelect.value) { e.preventDefault(); alert('Kapal/Voyage belum diisi!'); return; }
                if (!confirm(`Proses muat ${selectedProspeks.length} kontainer ke kapal ini?`)) e.preventDefault();
            });
        }

        if(exportExcelBtn) {
            exportExcelBtn.addEventListener('click', function() {
                if (selectedProspeks.length === 0) return;
                const exportForm = document.createElement('form');
                exportForm.method = 'GET';
                exportForm.action = '{{ route("prospek.export-excel") }}';
                exportForm.target = '_blank';
                selectedProspeks.forEach(p => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'prospek_ids[]';
                    input.value = p.id;
                    exportForm.appendChild(input);
                });
                const tInput = document.createElement('input');
                tInput.type = 'hidden'; tInput.name = 'tujuan_id'; tInput.value = '{{ $tujuanId }}';
                exportForm.appendChild(tInput);
                document.body.appendChild(exportForm);
                exportForm.submit();
                document.body.removeChild(exportForm);
            });
        }
    });
    </script>
@endpush
@endsection