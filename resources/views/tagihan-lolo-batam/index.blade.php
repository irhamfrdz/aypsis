@extends('layouts.app')

@section('title', 'Tagihan LOLO Batam')
@section('page_title', 'Tagihan LOLO Batam')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tagihan LOLO Batam</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola dan pantau seluruh kontainer berstatus LOLO serta penerbitan faktur tagihan di Batam.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('master.pricelist-lolo-batam.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-150">
                <i class="fas fa-tags mr-2"></i>
                Master Tarif LOLO Batam
            </a>
            @can('tagihan-lolo-batam-export')
            <a href="{{ route('tagihan-lolo-batam.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all duration-150">
                <i class="fas fa-file-excel mr-2"></i>
                Export CSV
            </a>
            @endcan
        </div>
    </div>

    {{-- Alert Section --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm flex items-center text-emerald-800 text-sm">
        <i class="fas fa-check-circle text-emerald-500 text-lg mr-3"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm flex items-center text-red-800 text-sm">
        <i class="fas fa-exclamation-circle text-red-500 text-lg mr-3"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Navigation Tabs --}}
    <div class="flex border-b border-gray-200 mb-6 space-x-2">
        <a href="{{ route('tagihan-lolo-batam.index', array_merge(request()->except(['tab', 'kontainer_page', 'faktur_page']), ['tab' => 'kontainer'])) }}" 
           class="py-3 px-5 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'kontainer' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50 rounded-t-lg' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <i class="fas fa-boxes"></i>
            <span>Kontainer LOLO (Bongkaran & Langsir)</span>
            @if($countBelumDitagihKontainer > 0)
                <span class="ml-1.5 px-2 py-0.5 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-300">
                    {{ $countBelumDitagihKontainer }} Belum Masuk Pranota
                </span>
            @endif
        </a>

        <a href="{{ route('tagihan-lolo-batam.index', array_merge(request()->except(['tab', 'kontainer_page', 'faktur_page']), ['tab' => 'faktur'])) }}" 
           class="py-3 px-5 text-sm font-bold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'faktur' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50 rounded-t-lg' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <i class="fas fa-file-invoice"></i>
            <span>Faktur Tagihan LOLO Batam</span>
            <span class="ml-1.5 px-2 py-0.5 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                {{ $totalTagihanCount }}
            </span>
        </a>
    </div>

    @if($activeTab === 'kontainer')
        {{-- ================= TAB 1: KONTAINER LOLO (BONGKARAN & LANGSIR) ================= --}}
        
        {{-- Statistics Cards for Containers --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Kontainer LOLO</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalKontainerLoloCount, 0, ',', '.') }}</h3>
                    <p class="text-xs text-gray-400 mt-1">Bongkaran & Langsir Batam</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Belum Masuk Pranota</p>
                    <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($countBelumDitagihKontainer, 0, ',', '.') }}</h3>
                    <p class="text-xs text-amber-600 font-semibold mt-1">Siap dibuatkan pranota</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sudah Masuk Pranota</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($countSudahDitagihKontainer, 0, ',', '.') }}</h3>
                    <p class="text-xs text-emerald-600 font-medium mt-1">Telah dibuatkan pranota</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Faktur Diterbitkan</p>
                    <h3 class="text-2xl font-bold text-indigo-700 mt-1">{{ number_format($totalTagihanCount, 0, ',', '.') }}</h3>
                    <p class="text-xs text-gray-400 mt-1">Invoice Tagihan LOLO</p>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>

        {{-- Search & Filter Section for Containers --}}
        <div class="mb-6 bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <form method="GET" action="{{ route('tagihan-lolo-batam.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3">
                <input type="hidden" name="tab" value="kontainer">

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Kontainer / No. SJ / Kapal</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nomor kontainer, surat jalan, kapal, supir..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sumber Dokumen</label>
                    <select name="sumber" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white">
                        <option value="all" {{ ($sumber ?? '') == 'all' ? 'selected' : '' }}>Semua Sumber</option>
                        <option value="bongkaran" {{ ($sumber ?? '') == 'bongkaran' ? 'selected' : '' }}>Surat Jalan Bongkaran</option>
                        <option value="langsir" {{ ($sumber ?? '') == 'langsir' ? 'selected' : '' }}>Langsir Batam</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status Pranota</label>
                    <select name="status_tagihan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white">
                        <option value="" {{ empty($statusTagihan) ? 'selected' : '' }}>Semua Status</option>
                        <option value="belum" {{ ($statusTagihan ?? '') == 'belum' ? 'selected' : '' }}>Belum Masuk Pranota</option>
                        <option value="sudah" {{ ($statusTagihan ?? '') == 'sudah' ? 'selected' : '' }}>Sudah Masuk Pranota</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition-colors flex items-center justify-center shadow-sm">
                        <i class="fas fa-search mr-1.5"></i> Filter
                    </button>
                    <a href="{{ route('tagihan-lolo-batam.index', ['tab' => 'kontainer']) }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm transition-colors" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- Batch Selection / Container Table Section --}}
        <div>
            {{-- Floating / Top Multi-Select Action Banner --}}
            <div id="selectionBanner" class="hidden mb-4 p-4 bg-indigo-50 border border-indigo-200 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0" id="selectedCountBadge">0</span>
                    <div>
                        <p class="text-sm font-bold text-indigo-900"><span id="selectedCountText">0</span> Kontainer LOLO dipilih</p>
                        <p class="text-xs text-indigo-700">Buat satu pranota tagihan LOLO gabungan dari kontainer yang dipilih.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="clearAllSelections()" class="px-3 py-1.5 text-xs text-gray-600 hover:text-gray-800 bg-white border border-gray-300 rounded-lg">
                        Batal Pilihan
                    </button>
                    <button type="button" onclick="openCreatePranotaModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition-all flex items-center">
                        <i class="fas fa-file-invoice mr-1.5"></i> Buat Pranota dari Kontainer Terpilih
                    </button>
                </div>
            </div>

            {{-- Pending Containers Table Card --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/75">
                            <tr>
                                <th class="px-4 py-3.5 text-center w-10">
                                    <input type="checkbox" id="selectAllContainers" onchange="toggleSelectAllContainers(this)" class="rounded text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No. Surat Jalan / Transaksi</th>
                                <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Sumber</th>
                                <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nomor Kontainer</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Size</th>
                                <th class="px-4 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Total Tarif</th>
                                <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider min-w-[230px]">Operator</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status Pranota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($pendingContainers as $c)
                            <tr class="hover:bg-gray-50/75 transition-colors {{ $c->is_billed ? 'bg-gray-50/30' : '' }}">
                                <td class="px-4 py-4 text-center">
                                    @if(!$c->is_billed)
                                        <input type="checkbox" 
                                               name="selected_containers[]" 
                                               value="{{ $c->sumber }}_{{ $c->id }}" 
                                               data-sumber="{{ $c->sumber }}"
                                               data-id="{{ $c->id }}"
                                               data-sj="{{ $c->nomor_surat_jalan ?: '-' }}"
                                               data-no-kontainer="{{ $c->no_kontainer ?: '-' }}"
                                                data-size="{{ preg_replace('/[^0-9]/', '', (string)$c->size) ?: ($c->size ?: '20') }}"
                                                data-tipe="{{ $c->tipe_kontainer ?: 'FULL' }}"
                                                data-kapal="{{ $c->kapal !== '-' ? $c->kapal : '' }}"
                                                data-voyage="{{ $c->voyage !== '-' ? $c->voyage : '' }}"
                                                data-lokasi="{{ $c->lokasi ?: '' }}"
                                                data-tarif="{{ $c->total_tarif ?? 0 }}"
                                                onchange="updateContainerSelection()" 
                                                class="container-checkbox rounded text-indigo-600 focus:ring-indigo-500">
                                    @else
                                        <span class="text-gray-300"><i class="fas fa-lock text-xs" title="Sudah masuk pranota"></i></span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    {{ $c->nomor_surat_jalan ?: '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $c->tanggal ? date('d/m/Y', strtotime($c->tanggal)) : '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    @if($c->sumber === 'bongkaran')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Bongkaran
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            Langsir
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    {{ $c->no_kontainer ?: '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-800">
                                    {{ $c->display_size ?? ($c->size ? $c->size.'\'' : '20\'') }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right font-semibold text-emerald-700 text-sm">
                                    {{ $c->formatted_total_tarif }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    @if($c->is_billed)
                                        @if($c->tipe_operator === 'AYP')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                <i class="fas fa-user-tie mr-1 text-blue-500"></i> AYP: {{ $c->operator ?: 'Operator AYP' }}
                                            </span>
                                        @elseif($c->tipe_operator === 'VENDOR')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                                <i class="fas fa-building mr-1 text-purple-500"></i> Vendor: {{ $c->operator ?: 'Vendor' }}
                                            </span>
                                        @elseif($c->operator)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                                {{ $c->operator }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    @else
                                        <div class="flex flex-col gap-1.5 min-w-[210px]">
                                            <div class="flex items-center gap-3">
                                                <label class="inline-flex items-center text-xs font-semibold text-gray-700 cursor-pointer">
                                                    <input type="radio" 
                                                           name="operators[{{ $c->sumber }}][{{ $c->id }}][tipe]" 
                                                           value="AYP" 
                                                           checked 
                                                           onchange="toggleRowOperator('{{ $c->sumber }}', {{ $c->id }}, this.value)" 
                                                           class="text-indigo-600 focus:ring-indigo-500 text-xs">
                                                    <span class="ml-1 text-xs">Operator AYP</span>
                                                </label>
                                                <label class="inline-flex items-center text-xs font-semibold text-gray-700 cursor-pointer">
                                                    <input type="radio" 
                                                           name="operators[{{ $c->sumber }}][{{ $c->id }}][tipe]" 
                                                           value="VENDOR" 
                                                           onchange="toggleRowOperator('{{ $c->sumber }}', {{ $c->id }}, this.value)" 
                                                           class="text-indigo-600 focus:ring-indigo-500 text-xs">
                                                    <span class="ml-1 text-xs">Vendor</span>
                                                </label>
                                            </div>

                                            <div id="row_operator_ayp_{{ $c->sumber }}_{{ $c->id }}">
                                                <select name="operators[{{ $c->sumber }}][{{ $c->id }}][karyawan_id]" 
                                                        class="w-full text-xs py-1 px-2.5 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                                    <option value="">-- Pilih Operator AYP --</option>
                                                    @foreach($karyawanOperators as $ko)
                                                        <option value="{{ $ko->id }}">{{ $ko->nama_lengkap }}{{ $ko->pekerjaan ? ' ('.$ko->pekerjaan.')' : ($ko->divisi ? ' ('.$ko->divisi.')' : '') }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div id="row_operator_vendor_{{ $c->sumber }}_{{ $c->id }}" class="hidden">
                                                <input type="text" 
                                                       name="operators[{{ $c->sumber }}][{{ $c->id }}][vendor_nama]" 
                                                       placeholder="Ketik nama vendor..." 
                                                       class="w-full text-xs py-1 px-2.5 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    @if($c->is_billed)
                                        <a href="{{ route('tagihan-lolo-batam.show', $c->tagihan_id) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 border border-emerald-300 transition-colors" title="Klik untuk lihat pranota/faktur">
                                            <i class="fas fa-check-circle mr-1 text-emerald-600"></i>
                                            Sudah Masuk Pranota ({{ $c->nomor_tagihan }})
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                                            <i class="fas fa-clock mr-1 text-amber-600"></i> Belum Masuk Pranota
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-boxes text-4xl text-gray-300 mb-3 block"></i>
                                    <p class="text-base font-semibold">Tidak ditemukan data kontainer LOLO Batam.</p>
                                    <p class="text-xs text-gray-400 mt-1">Data kontainer dari Surat Jalan Bongkaran Batam atau Langsir Batam yang menggunakan LOLO akan otomatis muncul di sini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pendingContainers->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $pendingContainers->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- ================= MODAL POPUP BUAT PRANOTA LOLO BATAM ================= --}}
        <div id="createPranotaModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeCreatePranotaModal()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal Dialog Card --}}
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100">
                    <form action="{{ route('tagihan-lolo-batam.store') }}" method="POST" id="modalPranotaForm">
                        @csrf
                        {{-- Hidden Form Fields --}}
                        <input type="hidden" name="vendor" id="modal_vendor" value="">
                        <input type="hidden" name="status_pembayaran" id="modal_status_pembayaran" value="Belum Lunas">
                        <input type="hidden" name="tanggal_bayar" id="modal_tanggal_bayar" value="">
                        <input type="hidden" name="kapal" id="modal_kapal" value="">
                        <input type="hidden" name="voyage" id="modal_voyage" value="">
                        <input type="hidden" name="keterangan" id="modal_keterangan" value="">
                        <input type="hidden" name="tipe_operator" id="modal_tipe_operator" value="AYP">
                        <input type="hidden" name="operator_karyawan_id" id="modal_operator_karyawan_id" value="">
                        <input type="hidden" name="operator" id="modal_operator" value="">

                        {{-- Modal Header --}}
                        <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between text-white shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-lg border border-white/20">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-white leading-tight">Buat Pranota Tagihan LOLO Batam</h3>
                                    <p class="text-xs text-indigo-100 mt-0.5">Penerbitan pranota tagihan dari kontainer yang dipilih</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeCreatePranotaModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors" title="Tutup Modal">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-6 max-h-[75vh] overflow-y-auto space-y-4">
                            {{-- Pranota Metadata Section (Nomor & Tanggal Pranota) --}}
                            <div class="bg-gray-50 border border-gray-200/80 rounded-xl p-4 grid grid-cols-1 sm:grid-cols-2 gap-4 shadow-sm">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1 flex items-center">
                                        <i class="fas fa-barcode text-indigo-600 mr-1.5"></i> Nomor Pranota <span class="text-red-500 ml-0.5">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nomor_tagihan" 
                                           id="modal_nomor_tagihan" 
                                           value="{{ $nomorTagihan ?? '' }}" 
                                           required 
                                           class="w-full px-3.5 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-indigo-900 tracking-wide focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm uppercase">
                                    <p class="text-[11px] text-gray-400 mt-1">Default: 2 digit kode (LB) - 2 digit bulan - 2 digit tahun - 6 digit no urut</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1 flex items-center">
                                        <i class="fas fa-calendar-alt text-indigo-600 mr-1.5"></i> Tanggal Pranota <span class="text-red-500 ml-0.5">*</span>
                                    </label>
                                    <input type="date" 
                                           name="tanggal_tagihan" 
                                           id="modal_tanggal_tagihan" 
                                           value="{{ date('Y-m-d') }}" 
                                           required 
                                           class="w-full px-3.5 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                    <p class="text-[11px] text-gray-400 mt-1">Tanggal penerbitan pranota</p>
                                </div>
                            </div>

                            {{-- Selected Containers Detail Section --}}
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center">
                                        <i class="fas fa-cubes text-indigo-600 mr-2"></i> Rincian Kontainer Terpilih (<span id="modalSelectedCount">0 Kontainer</span>)
                                    </h4>
                                </div>

                                <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50 text-xs font-bold text-gray-600 uppercase border-b border-gray-200">
                                            <tr>
                                                <th class="px-3 py-3 text-center w-10">#</th>
                                                <th class="px-4 py-3 text-left">No. Kontainer</th>
                                                <th class="px-3 py-3 text-center">Size</th>
                                                <th class="px-4 py-3 text-left">Sumber / No. SJ</th>
                                                <th class="px-4 py-3 text-left min-w-[180px]">Operator</th>
                                                <th class="px-4 py-3 text-right w-44">Total Tarif (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="modalItemsTableBody" class="divide-y divide-gray-200 bg-white text-xs">
                                            {{-- Dynamically populated via JS --}}
                                        </tbody>
                                        <tfoot class="bg-gray-50/80 font-bold text-gray-800 border-t border-gray-200">
                                            <tr>
                                                <td colspan="5" class="px-4 py-3.5 text-right text-xs uppercase tracking-wider text-gray-600">Total Tagihan:</td>
                                                <td class="px-4 py-3.5 text-right text-base text-indigo-700 font-extrabold" id="modalGrandTotal">Rp 0</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="text-xs text-gray-500">
                                Pastikan seluruh data kontainer dan operator telah sesuai sebelum menerbitkan pranota.
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="closeCreatePranotaModal()" class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 rounded-xl text-xs font-bold transition-all">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center">
                                    <i class="fas fa-check-circle mr-1.5"></i> Simpan & Terbitkan Pranota
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @else
        {{-- ================= TAB 2: FAKTUR TAGIHAN LOLO BATAM ================= --}}

        {{-- Statistics Cards for Invoices --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Tagihan</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalTagihanCount, 0, ',', '.') }}</h3>
                    <p class="text-xs text-gray-400 mt-1">Semua invoice LOLO</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Belum Lunas</p>
                    <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($countBelumLunas, 0, ',', '.') }}</h3>
                    <p class="text-xs text-amber-600 font-semibold mt-1">Rp {{ number_format($totalBelumLunas, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-clock"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lunas</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">Rp {{ number_format($totalLunas, 0, ',', '.') }}</h3>
                    <p class="text-xs text-emerald-600 font-medium mt-1">Telah diselesaikan</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Nominal</p>
                    <h3 class="text-2xl font-bold text-indigo-700 mt-1">Rp {{ number_format($totalNominal, 0, ',', '.') }}</h3>
                    <p class="text-xs text-gray-400 mt-1">Keseluruhan tagihan</p>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        {{-- Search & Filter Section for Invoices --}}
        <div class="mb-6 bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <form method="GET" action="{{ route('tagihan-lolo-batam.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3">
                <input type="hidden" name="tab" value="faktur">

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Keyword</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="No tagihan, kontainer, kapal, voyage..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status Pembayaran</label>
                    <select name="status_pembayaran" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white">
                        <option value="">Semua Status</option>
                        <option value="Belum Lunas" {{ ($statusPembayaran ?? '') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="Lunas" {{ ($statusPembayaran ?? '') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Vendor</label>
                    <input type="text" name="vendor" value="{{ $vendor ?? '' }}" placeholder="Filter vendor..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition-colors flex items-center justify-center shadow-sm">
                        <i class="fas fa-search mr-1.5"></i> Filter
                    </button>
                    <a href="{{ route('tagihan-lolo-batam.index', ['tab' => 'faktur']) }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm transition-colors" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- Invoices Table Card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/75">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No. Tagihan</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Vendor / Depo</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Operator</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kapal / Voyage</th>
                            <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Item Kontainer</th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Total Tagihan</th>
                            <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($tagihans as $tagihan)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <a href="{{ route('tagihan-lolo-batam.show', $tagihan->id) }}" class="font-bold text-indigo-600 hover:text-indigo-800 text-sm flex items-center">
                                    <i class="fas fa-file-invoice text-gray-400 mr-2"></i>
                                    {{ $tagihan->nomor_tagihan }}
                                </a>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $tagihan->tanggal_tagihan ? $tagihan->tanggal_tagihan->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                {{ $tagihan->vendor ?: '-' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm">
                                @if($tagihan->tipe_operator === 'AYP')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fas fa-user-tie mr-1 text-blue-500"></i> AYP: {{ $tagihan->operator ?: ($tagihan->operatorKaryawan->nama_lengkap ?? '-') }}
                                    </span>
                                @elseif($tagihan->tipe_operator === 'VENDOR')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i class="fas fa-building mr-1 text-purple-500"></i> Vendor: {{ $tagihan->operator ?: ($tagihan->vendor ?: '-') }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($tagihan->kapal)
                                    <div class="font-semibold text-gray-800">{{ $tagihan->kapal }}</div>
                                    <div class="text-xs text-gray-400">Voy: {{ $tagihan->voyage ?: '-' }}</div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                    {{ $tagihan->items->count() }} Kontainer
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right font-bold text-emerald-700 text-sm">
                                {{ $tagihan->formatted_total_tagihan }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $tagihan->status_color }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $tagihan->status_pembayaran === 'Lunas' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    {{ $tagihan->status_pembayaran }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('tagihan-lolo-batam.show', $tagihan->id) }}" class="p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('tagihan-lolo-batam-print')
                                    <a href="{{ route('tagihan-lolo-batam.print', $tagihan->id) }}" target="_blank" class="p-1.5 text-purple-600 hover:text-purple-900 hover:bg-purple-50 rounded-lg transition-colors" title="Cetak Invoice">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @endcan
                                    @can('tagihan-lolo-batam-update')
                                    <a href="{{ route('tagihan-lolo-batam.edit', $tagihan->id) }}" class="p-1.5 text-amber-600 hover:text-amber-900 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('tagihan-lolo-batam-delete')
                                    <form action="{{ route('tagihan-lolo-batam.destroy', $tagihan->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan LOLO ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-file-invoice text-4xl text-gray-300 mb-3 block"></i>
                                <p class="text-base font-semibold">Belum ada data Faktur Tagihan LOLO Batam.</p>
                                <p class="text-xs text-gray-400 mt-1">Klik tombol Buat Tagihan Baru atau pilih dari tab Kontainer LOLO.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tagihans->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $tagihans->links() }}
            </div>
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script>
    function toggleRowOperator(sumber, id, tipe) {
        const aypBox = document.getElementById(`row_operator_ayp_${sumber}_${id}`);
        const vendorBox = document.getElementById(`row_operator_vendor_${sumber}_${id}`);
        if (tipe === 'AYP') {
            if (aypBox) aypBox.classList.remove('hidden');
            if (vendorBox) vendorBox.classList.add('hidden');
        } else {
            if (aypBox) aypBox.classList.add('hidden');
            if (vendorBox) vendorBox.classList.remove('hidden');
        }
    }

    function toggleSelectAllContainers(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.container-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateContainerSelection();
    }

    function updateContainerSelection() {
        const selected = document.querySelectorAll('.container-checkbox:checked');
        const count = selected.length;
        const banner = document.getElementById('selectionBanner');
        const badge = document.getElementById('selectedCountBadge');
        const text = document.getElementById('selectedCountText');

        if (count > 0) {
            banner.classList.remove('hidden');
            badge.innerText = count;
            text.innerText = count;
        } else {
            banner.classList.add('hidden');
            const master = document.getElementById('selectAllContainers');
            if (master) master.checked = false;
        }
    }

    function clearAllSelections() {
        const checkboxes = document.querySelectorAll('.container-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        const master = document.getElementById('selectAllContainers');
        if (master) master.checked = false;
        updateContainerSelection();
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(Math.round(num));
    }

    function toggleModalTanggalBayar() {
        const status = document.getElementById('modal_status_pembayaran').value;
        const box = document.getElementById('modal_tanggal_bayar_box');
        const inp = document.getElementById('modal_tanggal_bayar');
        if (status === 'Lunas') {
            box.classList.remove('hidden');
            inp.required = true;
            if (!inp.value) {
                inp.value = new Date().toISOString().split('T')[0];
            }
        } else {
            box.classList.add('hidden');
            inp.required = false;
        }
    }

    function toggleModalOperatorTipe(tipe) {
        const aypBox = document.getElementById('modal_operator_ayp_box');
        const vendorBox = document.getElementById('modal_operator_vendor_box');
        if (tipe === 'AYP') {
            aypBox.classList.remove('hidden');
            vendorBox.classList.add('hidden');
        } else {
            aypBox.classList.add('hidden');
            vendorBox.classList.remove('hidden');
        }
    }

    function recalculateModalItem(idx) {
        // Calculate Grand Total
        let total = 0;
        const allTarif = document.querySelectorAll('[id^="modal_item_tarif_"]');
        allTarif.forEach((el) => {
            const itemTarif = parseFloat(el.value) || 0;
            total += itemTarif;
        });

        document.getElementById('modalGrandTotal').innerText = 'Rp ' + formatNumber(total);
    }

    function openCreatePranotaModal() {
        const selectedCheckboxes = document.querySelectorAll('.container-checkbox:checked');
        if (selectedCheckboxes.length === 0) {
            alert('Silakan pilih minimal satu kontainer terlebih dahulu.');
            return;
        }

        const tableBody = document.getElementById('modalItemsTableBody');
        tableBody.innerHTML = '';
        let grandTotal = 0;
        let detectedKapal = '';
        let detectedVoyage = '';
        let firstOperatorTipe = 'AYP';
        let firstOperatorKaryawanId = '';
        let firstOperatorVendor = '';

        selectedCheckboxes.forEach((cb, idx) => {
            const sumber = cb.getAttribute('data-sumber');
            const id = cb.getAttribute('data-id');
            const sj = cb.getAttribute('data-sj');
            const noKontainer = cb.getAttribute('data-no-kontainer');
            const rawSize = cb.getAttribute('data-size') || '20';
            const size = rawSize.replace(/[^0-9]/g, '') || rawSize;
            const tipe = cb.getAttribute('data-tipe');
            const kapal = cb.getAttribute('data-kapal');
            const voyage = cb.getAttribute('data-voyage');
            const tarif = parseFloat(cb.getAttribute('data-tarif')) || 0;
            grandTotal += tarif;

            if (!detectedKapal && kapal && kapal !== '-') detectedKapal = kapal;
            if (!detectedVoyage && voyage && voyage !== '-') detectedVoyage = voyage;

            // Get row operator info
            const rowOpTipeRadio = document.querySelector(`input[name="operators[${sumber}][${id}][tipe]"]:checked`);
            const opTipe = rowOpTipeRadio ? rowOpTipeRadio.value : 'AYP';
            
            let opKaryawanId = '';
            let opKaryawanName = '';
            let opVendorName = '';
            let opDisplayText = '';

            if (opTipe === 'AYP') {
                const sel = document.querySelector(`select[name="operators[${sumber}][${id}][karyawan_id]"]`);
                if (sel && sel.value) {
                    opKaryawanId = sel.value;
                    opKaryawanName = sel.options[sel.selectedIndex].text;
                    opDisplayText = `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200"><i class="fas fa-user-tie mr-1 text-blue-500"></i> ${opKaryawanName}</span>`;
                    if (!firstOperatorKaryawanId) {
                        firstOperatorTipe = 'AYP';
                        firstOperatorKaryawanId = opKaryawanId;
                        firstOperatorVendor = '';
                    }
                } else {
                    opDisplayText = `<span class="text-xs text-gray-500 font-medium">Operator AYP</span>`;
                }
            } else {
                const inp = document.querySelector(`input[name="operators[${sumber}][${id}][vendor_nama]"]`);
                opVendorName = inp ? inp.value.trim() : '';
                opDisplayText = `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200"><i class="fas fa-building mr-1 text-purple-500"></i> ${opVendorName || 'Vendor'}</span>`;
                if (!firstOperatorVendor && opVendorName) {
                    firstOperatorTipe = 'VENDOR';
                    firstOperatorVendor = opVendorName;
                    firstOperatorKaryawanId = '';
                }
            }

            const rowHtml = `
                <tr class="hover:bg-gray-50/75 transition-colors">
                    <td class="px-3 py-3 text-center text-gray-500 font-semibold">${idx + 1}</td>
                    <td class="px-4 py-3 font-bold text-gray-900">
                        ${noKontainer}
                        <input type="hidden" name="items[${idx}][nomor_kontainer]" value="${noKontainer}">
                        <input type="hidden" name="items[${idx}][size]" value="${size}">
                        <input type="hidden" name="items[${idx}][tipe_kontainer]" value="${tipe}">
                        <input type="hidden" name="items[${idx}][sumber_data]" value="${sumber}">
                        <input type="hidden" name="items[${idx}][${sumber === 'bongkaran' ? 'surat_jalan_bongkaran_id' : 'langsir_batam_id'}]" value="${id}">
                        <input type="hidden" name="items[${idx}][nomor_surat_jalan]" value="${sj !== '-' ? sj : ''}">
                        <input type="hidden" name="items[${idx}][kegiatan]" value="LOLO ${sumber === 'bongkaran' ? 'Bongkaran' : 'Langsir'} Batam (${sj})">
                        <input type="hidden" name="items[${idx}][keterangan]" value="${kapal ? 'Kapal: ' + kapal : ''} ${voyage ? 'Voy: ' + voyage : ''}">
                        <input type="hidden" name="items[${idx}][jumlah]" value="1">
                    </td>
                    <td class="px-3 py-3 text-center font-semibold text-gray-700">${size}'</td>
                    <td class="px-4 py-3 text-gray-600 text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold ${sumber === 'bongkaran' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'} mr-1.5 uppercase">${sumber}</span>
                        <span class="font-medium">${sj}</span>
                    </td>
                    <td class="px-4 py-3">
                        ${opDisplayText}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <span class="text-xs text-gray-500 font-semibold">Rp</span>
                            <input type="number" 
                                   name="items[${idx}][tarif]" 
                                   value="${tarif}" 
                                   min="0" 
                                   step="any"
                                   oninput="recalculateModalItem(${idx})" 
                                   id="modal_item_tarif_${idx}" 
                                   class="w-32 px-2.5 py-1.5 text-right text-xs font-bold text-emerald-700 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 bg-emerald-50/20">
                        </div>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', rowHtml);
        });

        document.getElementById('modalSelectedCount').innerText = selectedCheckboxes.length + ' Kontainer';
        document.getElementById('modalGrandTotal').innerText = 'Rp ' + formatNumber(grandTotal);

        if (detectedKapal) document.getElementById('modal_kapal').value = detectedKapal;
        if (detectedVoyage) document.getElementById('modal_voyage').value = detectedVoyage;

        document.getElementById('modal_tipe_operator').value = firstOperatorTipe;
        document.getElementById('modal_operator_karyawan_id').value = firstOperatorKaryawanId;
        document.getElementById('modal_operator').value = firstOperatorVendor;

        // Show modal
        document.getElementById('createPranotaModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeCreatePranotaModal() {
        document.getElementById('createPranotaModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
</script>
@endpush
@endsection

