@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    
    <!-- Page Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0 flex items-center gap-4">
            <a href="{{ route('pranota-uang-makan.index') }}" 
               class="flex items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-slate-300 transition-all text-slate-500 hover:text-slate-700 shadow-sm"
               title="Kembali ke Riwayat">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold tracking-tight">Edit Pranota Uang Makan</h1>
                <p class="text-sm text-slate-500 mt-1 flex items-center gap-2">
                    <span>Ubah data rincian dan kalkulasi uang makan</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                        <i class="fas fa-pencil-alt text-[10px] mr-1"></i> {{ $pranota->nomor_pranota }}
                    </span>
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('pranota-uang-makan.show', $pranota->id) }}" 
               class="btn bg-white border border-slate-200 hover:border-slate-300 text-slate-600 shadow-sm transition-all flex items-center px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fa-solid fa-eye mr-2 text-slate-400"></i>
                <span>Lihat Detail</span>
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-rose-500 mr-3 text-lg"></i>
                <p class="font-medium text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-rose-500 mr-3 text-lg mt-0.5"></i>
                <div>
                    <p class="font-bold text-sm mb-1">Terdapat kesalahan pengisian data:</p>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form id="edit-pranota-form" action="{{ route('pranota-uang-makan.update', $pranota->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Header Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Informasi Pranota</h2>
                    <p class="text-xs text-slate-400">Nomor dan tanggal penerbitan dokumen pranota uang makan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5" for="nomor_pranota">
                        Nomor Pranota <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nomor_pranota" name="nomor_pranota" 
                           class="w-full px-3 py-2 text-sm font-mono font-bold text-slate-800 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                           value="{{ old('nomor_pranota', $pranota->nomor_pranota) }}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5" for="tanggal_pranota">
                        Tanggal Pranota <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="tanggal_pranota" name="tanggal_pranota" 
                           class="w-full px-3 py-2 text-sm text-slate-800 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                           value="{{ old('tanggal_pranota', $pranota->tanggal_pranota ? $pranota->tanggal_pranota->format('Y-m-d') : date('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5" for="status">
                        Status Pranota
                    </label>
                    <select id="status" name="status" class="w-full px-3 py-2 text-sm text-slate-800 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="draft" {{ old('status', $pranota->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ old('status', $pranota->status) == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ old('status', $pranota->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Rincian Karyawan</h2>
                        <p class="text-xs text-slate-500">Edit nominal kehadiran, adjustment, atau hapus karyawan dari pranota ini</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" id="filter-search" 
                               class="pl-8 pr-3 py-1.5 text-xs border border-slate-300 rounded-lg shadow-2xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 w-48 sm:w-64 bg-white" 
                               placeholder="Cari nama atau NIK...">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <i class="fas fa-search text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Add Employee Button -->
                    <button type="button" onclick="openAddKaryawanModal()" 
                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors gap-1.5">
                        <i class="fas fa-user-plus text-xs"></i>
                        <span>Tambah Karyawan</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="details-table">
                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-white border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-center w-12">#</th>
                            <th class="px-4 py-3 min-w-[200px]">Karyawan</th>
                            <th class="px-4 py-3 text-center w-32">Kehadiran</th>
                            <th class="px-4 py-3 text-right min-w-[140px]">Nominal Awal (Rp)</th>
                            <th class="px-4 py-3 text-right min-w-[140px]">Adjustment (Rp)</th>
                            <th class="px-4 py-3 text-right min-w-[140px]">Total Akhir (Rp)</th>
                            <th class="px-4 py-3 min-w-[180px]">Catatan</th>
                            <th class="px-4 py-3 text-center w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100 bg-white" id="details-body">
                        @forelse($pranota->details as $index => $detail)
                            @php
                                $karyawanKey = class_basename($detail->tipe_karyawan) . '_' . $detail->karyawan_id;
                                $karyawanNama = $detail->karyawan->nama_lengkap ?? 'Karyawan Tidak Diketahui';
                                $karyawanNik = $detail->karyawan->nik ?? '-';
                                $karyawanPenempatan = $detail->karyawan->penempatan ?? ($detail->karyawan->cabang ?? '-');
                                $isTetap = ($detail->tipe_karyawan === 'App\\Models\\Karyawan');
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors item-row" data-key="{{ $karyawanKey }}" data-name="{{ strtolower($karyawanNama) }}" data-nik="{{ strtolower($karyawanNik) }}">
                                <input type="hidden" name="karyawans[{{ $karyawanKey }}][tipe_karyawan]" value="{{ $detail->tipe_karyawan }}">
                                <input type="hidden" name="karyawans[{{ $karyawanKey }}][karyawan_id]" value="{{ $detail->karyawan_id }}">

                                <!-- Row Number -->
                                <td class="px-4 py-3 text-center text-xs text-slate-400 font-mono row-number">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Employee Info -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center mr-2.5 text-xs font-bold border border-slate-200 shrink-0">
                                            {{ strtoupper(substr($karyawanNama, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-800 text-xs truncate" title="{{ $karyawanNama }}">
                                                {{ $karyawanNama }}
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-[10px] text-slate-400 font-mono">{{ $karyawanNik }}</span>
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-medium {{ $isTetap ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-700' }}">
                                                    {{ $isTetap ? 'Tetap' : 'Tidak Tetap' }}
                                                </span>
                                                @if($karyawanPenempatan && $karyawanPenempatan != '-')
                                                    <span class="text-[10px] text-slate-400">&bull; {{ $karyawanPenempatan }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kehadiran -->
                                <td class="px-4 py-3 text-center">
                                    <input type="text" name="karyawans[{{ $karyawanKey }}][kehadiran]" 
                                           value="{{ $detail->kehadiran }}" 
                                           class="w-full text-center px-2 py-1 text-xs border border-slate-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                                           placeholder="Contoh: 25 Hari">
                                </td>

                                <!-- Nominal Awal -->
                                <td class="px-4 py-3 text-right">
                                    <div class="relative">
                                        <input type="number" name="karyawans[{{ $karyawanKey }}][nominal_awal]" 
                                               value="{{ $detail->nominal_awal }}" 
                                               class="w-full text-right px-2 py-1 text-xs font-mono font-medium border border-slate-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 nominal-awal-input" 
                                               step="1000" min="0" required>
                                    </div>
                                </td>

                                <!-- Adjustment -->
                                <td class="px-4 py-3 text-right">
                                    <div class="relative">
                                        <input type="number" name="karyawans[{{ $karyawanKey }}][adjustment]" 
                                               value="{{ $detail->adjustment }}" 
                                               class="w-full text-right px-2 py-1 text-xs font-mono font-medium border border-slate-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 adjustment-input" 
                                               step="1000">
                                    </div>
                                </td>

                                <!-- Total Akhir Display -->
                                <td class="px-4 py-3 text-right">
                                    <span class="font-bold text-xs text-slate-800 font-mono total-akhir-display">
                                        Rp {{ number_format($detail->total_akhir, 0, ',', '.') }}
                                    </span>
                                </td>

                                <!-- Catatan -->
                                <td class="px-4 py-3">
                                    <input type="text" name="karyawans[{{ $karyawanKey }}][catatan]" 
                                           value="{{ $detail->catatan }}" 
                                           class="w-full px-2 py-1 text-xs border border-slate-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                                           placeholder="Keterangan opsional...">
                                </td>

                                <!-- Action (Delete) -->
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="removeRow(this)" 
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors" 
                                            title="Hapus Karyawan ini">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row">
                                <td colspan="8" class="px-6 py-10 text-center text-slate-400 text-xs">
                                    Belum ada karyawan di dalam pranota ini. Klik "Tambah Karyawan" untuk menambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t-2 border-slate-200 bg-slate-50/90">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-xs font-bold text-slate-600 uppercase tracking-wide">
                                Total (<span id="total-karyawan-display">{{ $pranota->details->count() }}</span> Karyawan)
                            </td>
                            <td class="px-4 py-4 text-right font-mono font-bold text-slate-700 text-xs" id="total-awal-display">
                                Rp {{ number_format($pranota->details->sum('nominal_awal'), 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-right font-mono font-bold text-xs" id="total-adjustment-display">
                                Rp {{ number_format($pranota->details->sum('adjustment'), 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-right font-mono font-black text-blue-600 text-sm" id="grand-total-display">
                                Rp {{ number_format($pranota->total_nominal, 0, ',', '.') }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <a href="{{ route('pranota-uang-makan.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                <i class="fas fa-times text-xs"></i>
                <span>Batal</span>
            </a>

            <div class="flex items-center gap-3">
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm hover:shadow transition-all">
                    <i class="fas fa-save text-xs"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal: Tambah Karyawan -->
<div id="add-karyawan-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-add-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true" onclick="closeAddKaryawanModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-flex flex-col align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200 max-h-[85vh]">
            <div class="bg-white px-5 py-4 border-b border-slate-100 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="bg-blue-50 p-2 rounded-lg text-blue-600">
                        <i class="fas fa-user-plus text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800" id="modal-add-title">Tambah Karyawan ke Pranota</h3>
                        <p class="text-xs text-slate-400">Pilih karyawan aktif untuk ditambahkan ke daftar pranota uang makan</p>
                    </div>
                </div>
                <button type="button" onclick="closeAddKaryawanModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                <div class="relative">
                    <input type="text" id="karyawan-modal-search" 
                           class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white" 
                           placeholder="Ketik nama karyawan atau NIK..." autocomplete="off">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400 text-xs"></i>
                    </div>
                </div>
            </div>

            <div class="p-2 overflow-y-auto flex-1 max-h-[50vh] divide-y divide-slate-100" id="karyawan-select-list">
                @foreach($allKaryawans as $k)
                    <div class="karyawan-select-item p-3 hover:bg-slate-50 rounded-lg cursor-pointer flex items-center justify-between transition-colors"
                         data-key="{{ $k->unique_id }}"
                         data-type="{{ $k->tipe_karyawan }}"
                         data-id="{{ $k->id }}"
                         data-name="{{ $k->nama_lengkap }}"
                         data-nik="{{ $k->nik ?? '-' }}"
                         data-label="{{ $k->tipe_label }}"
                         data-penempatan="{{ $k->penempatan ?? ($k->cabang ?? '-') }}"
                         data-nominal="{{ $k->nominal_uang_makan ?? 0 }}"
                         onclick="selectKaryawan(this)">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold shrink-0">
                                {{ strtoupper(substr($k->nama_lengkap, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-xs text-slate-800">{{ $k->nama_lengkap }}</div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    {{ $k->nik ?? '-' }} &bull; {{ $k->tipe_label }} &bull; {{ $k->penempatan ?? ($k->cabang ?? '-') }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-1 rounded bg-blue-50 text-blue-600 text-[11px] font-semibold hover:bg-blue-100 transition-colors">
                                <i class="fas fa-plus mr-1 text-[10px]"></i> Pilih
                            </span>
                        </div>
                    </div>
                @endforeach
                <div id="no-karyawan-results" class="hidden p-8 text-center text-slate-400 text-xs">
                    Tidak ditemukan karyawan yang cocok atau semua karyawan sudah ditambahkan.
                </div>
            </div>

            <div class="bg-slate-50 px-5 py-3 border-t border-slate-100 flex justify-end shrink-0">
                <button type="button" onclick="closeAddKaryawanModal()" 
                        class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function formatRupiah(num) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
    }

    function recalculateTotals() {
        const rows = document.querySelectorAll('#details-body tr.item-row');
        let totalCount = 0;
        let totalNominalAwal = 0;
        let totalAdjustment = 0;
        let grandTotal = 0;

        rows.forEach((row, index) => {
            totalCount++;
            const numberEl = row.querySelector('.row-number');
            if (numberEl) numberEl.innerText = totalCount;

            const awalInput = row.querySelector('.nominal-awal-input');
            const adjInput = row.querySelector('.adjustment-input');
            const totalDisplay = row.querySelector('.total-akhir-display');

            const nominalAwal = parseFloat(awalInput ? awalInput.value : 0) || 0;
            const adjustment = parseFloat(adjInput ? adjInput.value : 0) || 0;
            const totalAkhir = nominalAwal + adjustment;

            if (totalDisplay) {
                totalDisplay.innerText = formatRupiah(totalAkhir);
            }

            totalNominalAwal += nominalAwal;
            totalAdjustment += adjustment;
            grandTotal += totalAkhir;
        });

        // Update footer summary
        const countDisplay = document.getElementById('total-karyawan-display');
        const awalDisplay = document.getElementById('total-awal-display');
        const adjDisplay = document.getElementById('total-adjustment-display');
        const grandDisplay = document.getElementById('grand-total-display');

        if (countDisplay) countDisplay.innerText = totalCount;
        if (awalDisplay) awalDisplay.innerText = formatRupiah(totalNominalAwal);
        if (adjDisplay) {
            adjDisplay.innerText = formatRupiah(totalAdjustment);
            if (totalAdjustment > 0) {
                adjDisplay.className = 'px-4 py-4 text-right font-mono font-bold text-xs text-emerald-600';
            } else if (totalAdjustment < 0) {
                adjDisplay.className = 'px-4 py-4 text-right font-mono font-bold text-xs text-rose-600';
            } else {
                adjDisplay.className = 'px-4 py-4 text-right font-mono font-bold text-xs text-slate-700';
            }
        }
        if (grandDisplay) grandDisplay.innerText = formatRupiah(grandTotal);

        // Show/hide empty row
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) {
            if (totalCount === 0) {
                emptyRow.style.display = '';
            } else {
                emptyRow.style.display = 'none';
            }
        }
    }

    function removeRow(button) {
        const row = button.closest('tr');
        if (row) {
            row.remove();
            recalculateTotals();
        }
    }

    function openAddKaryawanModal() {
        const modal = document.getElementById('add-karyawan-modal');
        if (!modal) return;
        modal.classList.remove('hidden');

        // Check which keys are already in table to hide them from modal
        const existingKeys = Array.from(document.querySelectorAll('#details-body tr.item-row')).map(r => r.dataset.key);
        const modalItems = document.querySelectorAll('.karyawan-select-item');
        
        let visibleCount = 0;
        modalItems.forEach(item => {
            const key = item.dataset.key;
            if (existingKeys.includes(key)) {
                item.style.display = 'none';
            } else {
                item.style.display = 'flex';
                visibleCount++;
            }
        });

        const noResult = document.getElementById('no-karyawan-results');
        if (noResult) {
            noResult.style.display = (visibleCount === 0) ? 'block' : 'none';
        }

        const searchInput = document.getElementById('karyawan-modal-search');
        if (searchInput) {
            searchInput.value = '';
            setTimeout(() => searchInput.focus(), 100);
        }
    }

    function closeAddKaryawanModal() {
        const modal = document.getElementById('add-karyawan-modal');
        if (modal) modal.classList.add('hidden');
    }

    function selectKaryawan(el) {
        const key = el.dataset.key;
        const type = el.dataset.type;
        const id = el.dataset.id;
        const name = el.dataset.name;
        const nik = el.dataset.nik;
        const label = el.dataset.label;
        const penempatan = el.dataset.penempatan;
        const nominal = parseFloat(el.dataset.nominal) || 0;

        // Create new table row
        const tbody = document.getElementById('details-body');
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.style.display = 'none';

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/80 transition-colors item-row';
        tr.dataset.key = key;
        tr.dataset.name = (name || '').toLowerCase();
        tr.dataset.nik = (nik || '').toLowerCase();

        const badgeClass = (label === 'Tetap') ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-700';

        tr.innerHTML = `
            <input type="hidden" name="karyawans[${key}][tipe_karyawan]" value="${type}">
            <input type="hidden" name="karyawans[${key}][karyawan_id]" value="${id}">

            <td class="px-4 py-3 text-center text-xs text-slate-400 font-mono row-number"></td>

            <td class="px-4 py-3">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center mr-2.5 text-xs font-bold border border-slate-200 shrink-0">
                        ${name.charAt(0).toUpperCase()}
                    </div>
                    <div class="min-w-0">
                        <div class="font-semibold text-slate-800 text-xs truncate" title="${name}">
                            ${name}
                        </div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[10px] text-slate-400 font-mono">${nik}</span>
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-medium ${badgeClass}">
                                ${label}
                            </span>
                            ${penempatan && penempatan !== '-' ? `<span class="text-[10px] text-slate-400">&bull; ${penempatan}</span>` : ''}
                        </div>
                    </div>
                </div>
            </td>

            <td class="px-4 py-3 text-center">
                <input type="text" name="karyawans[${key}][kehadiran]" 
                       value="" 
                       class="w-full text-center px-2 py-1 text-xs border border-slate-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Kehadiran">
            </td>

            <td class="px-4 py-3 text-right">
                <div class="relative">
                    <input type="number" name="karyawans[${key}][nominal_awal]" 
                           value="${nominal}" 
                           class="w-full text-right px-2 py-1 text-xs font-mono font-medium border border-slate-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 nominal-awal-input" 
                           step="1000" min="0" required>
                </div>
            </td>

            <td class="px-4 py-3 text-right">
                <div class="relative">
                    <input type="number" name="karyawans[${key}][adjustment]" 
                           value="0" 
                           class="w-full text-right px-2 py-1 text-xs font-mono font-medium border border-slate-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 adjustment-input" 
                           step="1000">
                </div>
            </td>

            <td class="px-4 py-3 text-right">
                <span class="font-bold text-xs text-slate-800 font-mono total-akhir-display">
                    ${formatRupiah(nominal)}
                </span>
            </td>

            <td class="px-4 py-3">
                <input type="text" name="karyawans[${key}][catatan]" 
                       value="" 
                       class="w-full px-2 py-1 text-xs border border-slate-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Keterangan opsional...">
            </td>

            <td class="px-4 py-3 text-center">
                <button type="button" onclick="removeRow(this)" 
                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors" 
                        title="Hapus Karyawan ini">
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        // Bind input listeners
        tr.querySelector('.nominal-awal-input').addEventListener('input', recalculateTotals);
        tr.querySelector('.adjustment-input').addEventListener('input', recalculateTotals);

        recalculateTotals();
        closeAddKaryawanModal();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Bind calculation on input
        document.querySelectorAll('.nominal-awal-input, .adjustment-input').forEach(input => {
            input.addEventListener('input', recalculateTotals);
        });

        // Filter search logic on main table
        const filterInput = document.getElementById('filter-search');
        if (filterInput) {
            filterInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#details-body tr.item-row');

                rows.forEach(row => {
                    const name = row.dataset.name || '';
                    const nik = row.dataset.nik || '';
                    if (!query || name.includes(query) || nik.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Modal search logic
        const modalSearch = document.getElementById('karyawan-modal-search');
        if (modalSearch) {
            modalSearch.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const existingKeys = Array.from(document.querySelectorAll('#details-body tr.item-row')).map(r => r.dataset.key);
                const items = document.querySelectorAll('.karyawan-select-item');
                let count = 0;

                items.forEach(item => {
                    const key = item.dataset.key;
                    if (existingKeys.includes(key)) {
                        item.style.display = 'none';
                        return;
                    }

                    const name = (item.dataset.name || '').toLowerCase();
                    const nik = (item.dataset.nik || '').toLowerCase();
                    if (!query || name.includes(query) || nik.includes(query)) {
                        item.style.display = 'flex';
                        count++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                const noResult = document.getElementById('no-karyawan-results');
                if (noResult) {
                    noResult.style.display = (count === 0) ? 'block' : 'none';
                }
            });
        }

        // Initial recalculation
        recalculateTotals();
    });
</script>
@endpush
@endsection
