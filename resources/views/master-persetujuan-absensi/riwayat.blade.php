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
    <div class="flex items-center gap-2">
        <a href="{{ route('master.persetujuan-absensi.index') }}" class="px-3.5 py-1.5 rounded-md bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold border border-gray-300 transition flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <button onclick="refreshData()" class="px-3.5 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold border border-blue-200 transition flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-arrows-rotate"></i> Segarkan Data
        </button>
    </div>
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
            const response = await fetch(`${API_BASE_URL}/master/api/admin/history-permissions`);
            if (!response.ok) throw new Error('Gagal mengambil data riwayat.');
            
            const data = await response.json();
            countBadge.innerText = data.length + (data.length === 200 ? '+' : '');

            if (data.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            
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
                    <div class="bg-white hover:bg-gray-50 p-4 sm:p-5 rounded-xl border border-gray-200 flex flex-col sm:flex-row gap-4 transition-all duration-200 shadow-sm card-animate" id="card-${item.tabel_sumber}-${item.id}">
                        <!-- Avatar / Icon -->
                        <div class="hidden sm:flex flex-col items-center justify-center w-12 h-12 rounded-full bg-slate-50 border border-slate-200 flex-shrink-0 shadow-sm mt-1">
                            <i class="fa-solid fa-clock-rotate-left text-slate-400 text-lg"></i>
                        </div>

                        <!-- Main Content -->
                        <div class="flex-1 w-full">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 mb-2">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                        ${typeBadge}
                                        ${statusBadge}
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2 mb-1">
                                        ${item.nama || 'Karyawan Tanpa Nama'}
                                    </h3>
                                    <p class="text-[11px] text-gray-500 flex flex-wrap items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded font-medium border border-gray-200">NIK: ${item.nik}</span>
                                        <span>&bull;</span>
                                        <span><i class="fa-solid fa-sitemap mr-1 opacity-70"></i> Divisi: ${item.divisi || 'Umum'}</span>
                                    </p>
                                </div>
                                <div class="text-[10px] font-medium text-gray-400 whitespace-nowrap text-left sm:text-right bg-slate-50 px-2 py-1 rounded border border-slate-100 mt-2 sm:mt-0">
                                    <i class="fa-solid fa-paper-plane mr-1"></i> Diajukan:<br/><span class="text-gray-500 font-semibold">${submitDate}</span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 bg-slate-50 p-3.5 rounded-lg border border-slate-100 mt-4">
                                <div class="md:col-span-5 flex flex-col gap-1.5 text-xs text-gray-700">
                                    <div class="flex items-start">
                                        <div class="w-6 text-center text-slate-400 mt-0.5 shrink-0"><i class="fa-solid fa-calendar-days"></i></div>
                                        <span><span class="text-slate-500">Tanggal:</span> <span class="font-semibold text-gray-800">${dateRange}</span></span>
                                    </div>
                                    ${item.waktu ? `
                                    <div class="flex items-start">
                                        <div class="w-6 text-center text-slate-400 mt-0.5 shrink-0"><i class="fa-solid fa-clock"></i></div>
                                        <span><span class="text-slate-500">Waktu:</span> <span class="font-semibold text-gray-800">${item.waktu}</span></span>
                                    </div>` : ''}
                                    ${leaveWarningHTML}
                                </div>
                                
                                <div class="md:col-span-7 flex flex-col gap-1 text-xs text-gray-700 border-t md:border-t-0 md:border-l border-slate-200 pt-3 md:pt-0 md:pl-4">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">${item.tabel_sumber === 'persetujuan_absensi_lupas' ? 'Alasan Lupa Absen:' : 'Alasan Izin:'}</span>
                                    <p class="font-medium italic text-slate-600 leading-relaxed">"${item.alasan || 'Tidak menuliskan alasan'}"</p>
                                    
                                    ${item.lampiran ? `
                                    <div class="mt-2.5">
                                        <a href="${item.lampiran.startsWith('http') || item.lampiran.startsWith('data:image') ? item.lampiran : API_BASE_URL + item.lampiran}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded transition-colors group">
                                            <i class="fa-solid fa-paperclip group-hover:scale-110 transition-transform"></i> Lihat Lampiran
                                        </a>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } catch (err) {
            console.error(err);
            container.innerHTML = `<div class="bg-rose-50 border border-rose-200 text-rose-600 p-4 rounded-lg text-sm font-semibold text-center w-full">Gagal memuat data riwayat. Silakan coba lagi.</div>`;
        }
    }
</script>
@endpush
