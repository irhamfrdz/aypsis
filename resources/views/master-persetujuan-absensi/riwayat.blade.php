@extends('layouts.app')

@section('title', 'Riwayat Persetujuan Absensi')
@section('page_title', 'Riwayat Persetujuan Absensi')

@push('styles')
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .card-animate {
            transition: all 0.2s ease-in-out;
        }
        .card-animate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }
    </style>
@endpush

@section('content')
<!-- Page Header Card -->
<div class="bg-white rounded-lg border border-gray-200 p-6 mb-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-xl font-bold text-gray-900 leading-tight flex items-center gap-2">
            Riwayat Persetujuan <span id="history-count-badge" class="bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded-full border border-blue-200 font-bold">0</span>
        </h1>
        <p class="text-xs text-gray-500 mt-1">Daftar riwayat permohonan izin/cuti karyawan yang telah diproses.</p>
    </div>
    <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
        <select id="filter-jenis" onchange="refreshData()" class="px-3.5 py-1.5 rounded-md bg-white border border-gray-300 text-gray-700 text-xs font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
            <option value="">Semua Jenis Izin</option>
            <option value="cuti">Cuti</option>
            <option value="Sakit">Izin: Sakit</option>
            <option value="Pulang Cepat">Izin: Pulang Cepat</option>
            <option value="Datang Terlambat">Izin: Datang Terlambat</option>
            <option value="Dinas Luar">Izin: Dinas Luar</option>
            <option value="lupa">Lupa Absen</option>
        </select>
        
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button onclick="printSelected()" class="flex-1 sm:flex-none justify-center px-3.5 py-1.5 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold border border-emerald-200 transition flex items-center gap-2 shadow-sm" id="btn-print-selected">
                <i class="fa-solid fa-print"></i> Cetak (<span id="print-count">0</span>)
            </button>
            <a href="{{ route('master.persetujuan-absensi.index') }}" class="flex-1 sm:flex-none justify-center px-3.5 py-1.5 rounded-md bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold border border-gray-300 transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <button onclick="refreshData()" class="flex-1 sm:flex-none justify-center px-3.5 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold border border-blue-200 transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrows-rotate"></i> Segarkan Data
            </button>
        </div>
    </div>
</div>

<!-- Select All Actions -->
<div class="max-w-4xl mx-auto mb-4 px-1 flex items-center justify-between" id="select-all-container" style="display: none;">
    <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-900">
        <input type="checkbox" id="check-all" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer" onclick="toggleCheckAll()">
        Pilih Semua untuk Dicetak
    </label>
</div>

<!-- List Container -->
<div id="attendance-list" class="grid grid-cols-1 gap-6 max-w-4xl mx-auto">
    <!-- Loading Skeletons -->
    <div class="bg-white p-5 rounded-xl border border-gray-200 flex flex-col sm:flex-row gap-4 animate-pulse">
        <div class="hidden sm:block w-12 h-12 bg-gray-200 rounded-full flex-shrink-0"></div>
        <div class="flex-1 space-y-3 w-full">
            <div class="flex justify-between items-center">
                <div class="h-5 bg-gray-200 rounded w-1/3"></div>
                <div class="h-4 bg-gray-200 rounded w-1/4"></div>
            </div>
            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
            <div class="h-16 bg-gray-200 rounded w-full mt-4"></div>
        </div>
    </div>
</div>

<!-- Empty State -->
<div id="empty-state" class="hidden flex flex-col items-center justify-center py-16 px-4 bg-white rounded-lg border border-gray-200 border-dashed mt-6">
    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
        <i class="fa-solid fa-inbox text-3xl text-gray-300"></i>
    </div>
    <h3 class="text-lg font-bold text-gray-900 mb-1">Tidak ada riwayat</h3>
    <p class="text-sm text-gray-500 text-center max-w-xs">Belum ada data permohonan izin atau cuti yang telah diproses.</p>
</div>
@endsection

@push('scripts')
<script>
    const API_BASE_URL = '{{ url('/') }}';

    document.addEventListener('DOMContentLoaded', () => {
        loadHistoryPermissions();
    });

    async function refreshData() {
        const btn = document.querySelector('button[onclick="refreshData()"]');
        const icon = btn.querySelector('i');
        icon.classList.add('fa-spin');
        btn.disabled = true;
        
        await loadHistoryPermissions();
        
        setTimeout(() => {
            icon.classList.remove('fa-spin');
            btn.disabled = false;
        }, 500);
    }

    async function loadHistoryPermissions() {
        const container = document.getElementById('attendance-list');
        const emptyState = document.getElementById('empty-state');
        const countBadge = document.getElementById('history-count-badge');
        
        container.innerHTML = `
            <div class="bg-white p-5 rounded-xl border border-gray-200 flex flex-col sm:flex-row gap-4 animate-pulse">
                <div class="hidden sm:block w-12 h-12 bg-gray-200 rounded-full flex-shrink-0"></div>
                <div class="flex-1 space-y-3 w-full">
                    <div class="flex justify-between items-center">
                        <div class="h-5 bg-gray-200 rounded w-1/3"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                    </div>
                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    <div class="h-16 bg-gray-200 rounded w-full mt-4"></div>
                </div>
            </div>
        `;

        try {
            const filterValue = document.getElementById('filter-jenis')?.value || '';
            const queryParams = filterValue ? `?filter_jenis=${encodeURIComponent(filterValue)}` : '';
            const response = await fetch(`${API_BASE_URL}/master/api/admin/history-permissions${queryParams}`);
            if (!response.ok) throw new Error('Gagal mengambil data riwayat.');
            
            const data = await response.json();
            countBadge.innerText = data.length + (data.length === 200 ? '+' : '');

            if (data.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                document.getElementById('select-all-container').style.display = 'none';
                return;
            }

            emptyState.classList.add('hidden');
            document.getElementById('select-all-container').style.display = 'flex';
            document.getElementById('check-all').checked = false;
            updatePrintCount();
            
            container.innerHTML = data.map(item => {
                const startObj = new Date(item.tanggal_mulai);
                const endObj = new Date(item.tanggal_selesai);
                
                const d1 = new Date(startObj.getFullYear(), startObj.getMonth(), startObj.getDate());
                const d2 = new Date(endObj.getFullYear(), endObj.getMonth(), endObj.getDate());
                const diffTime = Math.abs(d2 - d1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                const startDate = startObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                const endDate = endObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                const dateRange = startDate === endDate 
                    ? `${startDate} <span class="text-blue-600 font-bold ml-1">(1 Hari)</span>` 
                    : `${startDate} s/d ${endDate} <span class="text-blue-600 font-bold ml-1">(${diffDays} Hari)</span>`;
                const submitDate = new Date(item.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });

                let typeLabel = '';
                let badgeColor = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                let badgePrefix = 'IZIN:';
                
                if (item.tabel_sumber === 'persetujuan_absensi_lupas') {
                    badgePrefix = 'LUPA ABSEN:';
                    badgeColor = 'bg-amber-50 text-amber-700 border-amber-200';
                    typeLabel = item.jenis_izin || 'Lupa Absen';
                } else {
                    if (item.jenis_izin === 'tidak_masuk') typeLabel = 'Tidak Masuk';
                    else if (item.jenis_izin === 'datang_terlambat') typeLabel = 'Datang Terlambat';
                    else if (item.jenis_izin === 'pulang_cepat') typeLabel = 'Pulang Cepat';
                    else if (item.jenis_izin === 'dinas_luar') typeLabel = 'Dinas Luar';
                    else typeLabel = item.jenis_izin;
                }

                const typeBadge = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${badgeColor}">${badgePrefix} ${typeLabel ? typeLabel.toUpperCase() : ''}</span>`;

                let leaveWarningHTML = '';
                if (item.jenis_izin && item.jenis_izin.toLowerCase() === 'tahunan' && item.sisa_cuti !== undefined) {
                    if (diffDays > item.sisa_cuti) {
                        const minus = item.sisa_cuti - diffDays;
                        leaveWarningHTML = `
                            <div class="mt-1.5 flex items-center text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2 py-1 rounded w-fit">
                                <i class="fa-solid fa-circle-exclamation mr-1.5"></i> Peringatan: Melebihi sisa cuti (akan menjadi ${minus} hari)
                            </div>
                        `;
                    } else {
                        leaveWarningHTML = `
                            <div class="mt-1.5 flex items-center text-[10px] font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded w-fit">
                                <i class="fa-solid fa-circle-check mr-1.5"></i> Sisa cuti: ${item.sisa_cuti} hari
                            </div>
                        `;
                    }
                }

                let statusBadge = '';
                const itemStatus = (item.status || '').toUpperCase();
                if (itemStatus === 'APPROVED' || itemStatus === 'DISETUJUI') {
                    statusBadge = `<span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-check mr-1"></i> DISETUJUI</span>`;
                } else if (itemStatus === 'REJECTED' || itemStatus === 'DITOLAK') {
                    statusBadge = `<span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200"><i class="fa-solid fa-xmark mr-1"></i> DITOLAK</span>`;
                } else {
                    statusBadge = `<span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">${item.status}</span>`;
                }

                return `
                    <div class="bg-white hover:bg-gray-50/80 p-5 sm:p-6 rounded-2xl border border-gray-200 transition-all duration-200 shadow-sm hover:shadow-md card-animate group" id="card-${item.tabel_sumber}-${item.id}">
                        <!-- Header Bar -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 border-b border-gray-100 pb-4">
                            <div class="flex flex-wrap items-center gap-2">
                                ${item.tabel_sumber !== 'persetujuan_absensi_lupas' ? `
                                <input type="checkbox" class="print-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer mr-1" data-type="${item.tabel_sumber}" data-id="${item.id}" onclick="updatePrintCount()">
                                ` : ''}
                                ${typeBadge}
                                ${statusBadge}
                                <span class="hidden sm:inline-block text-gray-300 mx-1">|</span>
                                <span class="text-xs font-medium text-gray-500"><i class="fa-solid fa-paper-plane mr-1.5 opacity-70"></i> Diajukan: <span class="text-gray-700">${submitDate}</span></span>
                            </div>
                            
                            <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                                ${item.lampiran ? `
                                <a href="${item.lampiran.startsWith('http') || item.lampiran.startsWith('data:image') ? item.lampiran : API_BASE_URL + item.lampiran}" target="_blank" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors" title="Lihat Lampiran">
                                    <i class="fa-solid fa-paperclip"></i> Lampiran
                                </a>
                                ` : ''}
                                
                                ${item.tabel_sumber !== 'persetujuan_absensi_lupas' ? `
                                <a href="${API_BASE_URL}/master/persetujuan-absensi/print/${item.tabel_sumber}/${item.id}" target="_blank" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors" title="Cetak Form">
                                    <i class="fa-solid fa-print"></i> Cetak Form
                                </a>
                                ` : ''}
                            </div>
                        </div>

                        <!-- Main Content Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                            
                            <!-- Kolom 1: Karyawan Info -->
                            <div class="md:col-span-4 flex items-start gap-4">
                                <div class="hidden sm:flex items-center justify-center w-12 h-12 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex-shrink-0">
                                    <i class="fa-solid fa-user-tag text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 mb-1.5 leading-tight">
                                        ${item.nama || 'Karyawan Tanpa Nama'}
                                    </h3>
                                    <div class="text-xs text-gray-500 space-y-1.5">
                                        <div class="flex items-center gap-2"><i class="fa-solid fa-id-badge w-3.5 text-center text-gray-400"></i> NIK: <span class="font-semibold text-gray-700">${item.nik}</span></div>
                                        <div class="flex items-center gap-2"><i class="fa-solid fa-sitemap w-3.5 text-center text-gray-400"></i> ${item.divisi || 'Umum'}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Kolom 2: Jadwal Waktu -->
                            <div class="md:col-span-4 border-t md:border-t-0 border-l-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2.5">Detail Waktu</span>
                                <div class="space-y-2.5 text-sm text-gray-700">
                                    <div class="flex items-start gap-2.5 bg-slate-50/50 p-2 rounded-lg border border-slate-100">
                                        <i class="fa-solid fa-calendar-day text-blue-500 mt-0.5 shrink-0"></i>
                                        <span class="font-semibold text-gray-800 text-xs">${dateRange}</span>
                                    </div>
                                    ${item.waktu ? `
                                    <div class="flex items-start gap-2.5">
                                        <i class="fa-solid fa-clock text-blue-400 mt-0.5 shrink-0"></i>
                                        <span class="font-medium text-gray-700 text-xs">${item.waktu}</span>
                                    </div>` : ''}
                                    ${leaveWarningHTML ? `<div class="mt-3">${leaveWarningHTML}</div>` : ''}
                                </div>
                            </div>

                            <!-- Kolom 3: Alasan -->
                            <div class="md:col-span-4 border-t md:border-t-0 border-l-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2.5">${item.tabel_sumber === 'persetujuan_absensi_lupas' ? 'Alasan Lupa Absen' : 'Alasan Izin'}</span>
                                <div class="text-[13px] font-medium italic text-slate-600 bg-slate-50/80 p-3.5 rounded-xl border border-slate-100 leading-relaxed min-h-[60px]">
                                    "${item.alasan || 'Tidak menuliskan alasan'}"
                                </div>
                            </div>
                            
                        </div>
                    </div>
                `;
            }).join('');
        } catch (err) {
            console.error(err);
            container.innerHTML = `<div class="bg-rose-50 border border-rose-200 text-rose-600 p-4 rounded-lg text-sm font-semibold text-center w-full">Gagal memuat data riwayat. Silakan coba lagi.</div>`;
            document.getElementById('select-all-container').style.display = 'none';
        }
    }

    function toggleCheckAll() {
        const isChecked = document.getElementById('check-all').checked;
        const checkboxes = document.querySelectorAll('.print-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = isChecked;
        });
        updatePrintCount();
    }

    function updatePrintCount() {
        const checkboxes = document.querySelectorAll('.print-checkbox:checked');
        document.getElementById('print-count').innerText = checkboxes.length;
        
        const checkAll = document.getElementById('check-all');
        const allCheckboxes = document.querySelectorAll('.print-checkbox');
        if (allCheckboxes.length > 0) {
            checkAll.checked = checkboxes.length === allCheckboxes.length;
        } else {
            checkAll.checked = false;
        }
    }

    function printSelected() {
        const checkboxes = document.querySelectorAll('.print-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Pilih minimal satu data untuk dicetak.');
            return;
        }
        
        let items = [];
        checkboxes.forEach(cb => {
            items.push({
                type: cb.dataset.type,
                id: cb.dataset.id
            });
        });
        
        const params = new URLSearchParams();
        items.forEach((item, i) => {
            params.append(`items[${i}][type]`, item.type);
            params.append(`items[${i}][id]`, item.id);
        });
        
        window.open(`${API_BASE_URL}/master/persetujuan-absensi/print-multiple?${params.toString()}`, '_blank');
    }
</script>
@endpush
