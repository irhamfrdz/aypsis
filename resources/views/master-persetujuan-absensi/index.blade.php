@extends('layouts.app')

@section('title', 'Persetujuan Absensi Karyawan')
@section('page_title', 'Persetujuan Absensi Karyawan')

@push('styles')
    <style>
        .approval-page {
            --approval-blue: #1d4ed8;
            --approval-ink: #0f172a;
        }
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
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, opacity 0.2s ease;
        }
        .card-animate:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px -12px rgba(15, 23, 42, 0.22);
            border-color: #bfdbfe;
        }
        @media (prefers-reduced-motion: reduce) {
            .card-animate { transition: none; }
        }
    </style>
@endpush

@section('content')
<!-- Page Header Card -->
<div class="approval-page mx-auto max-w-6xl">
<div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-7 mb-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5">
    <div class="min-w-0">
        <div class="flex items-center gap-3">
            <span class="h-11 w-11 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg"><i class="fa-solid fa-clipboard-check"></i></span>
            <div>
        <h1 id="approval-heading" class="text-xl sm:text-2xl font-bold text-slate-900 leading-tight flex items-center gap-2">
            Permintaan Persetujuan <span id="pending-count-badge" class="inline-flex min-w-7 justify-center bg-amber-50 text-amber-800 text-xs px-2.5 py-1 rounded-full border border-amber-200 font-bold">0</span>
        </h1>
        <p id="approval-description" class="text-sm text-slate-500 mt-1">Daftar karyawan yang melakukan absensi di luar radius kantor dan membutuhkan verifikasi.</p>
            </div>
        </div>
    </div>
    <div class="flex w-full sm:w-auto items-center gap-2">
        <a href="{{ route('master.persetujuan-absensi.riwayat') }}" class="flex-1 sm:flex-none justify-center px-4 py-2.5 rounded-lg bg-white hover:bg-blue-50 text-blue-700 text-sm font-semibold border border-slate-200 hover:border-blue-200 transition flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Izin
        </a>
        <button onclick="refreshData()" class="flex-1 sm:flex-none justify-center px-4 py-2.5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold border border-blue-700 transition flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-arrows-rotate"></i> Segarkan
        </button>
    </div>
</div>

<!-- Tab Selector -->
<div class="flex flex-col sm:flex-row bg-slate-100 rounded-xl p-1.5 mb-5 gap-1.5" role="tablist" aria-label="Jenis permintaan persetujuan">
    <button id="tab-absen" role="tab" aria-selected="true" onclick="switchTab('absen')" class="flex-1 py-3 px-4 rounded-lg text-sm font-semibold transition duration-200 text-white bg-blue-700 shadow-sm flex items-center justify-center gap-2">
        <i class="fa-solid fa-location-dot"></i> Absensi di Luar Area
    </button>
    <button id="tab-izin" role="tab" aria-selected="false" onclick="switchTab('izin')" class="flex-1 py-3 px-4 rounded-lg text-sm font-semibold transition duration-200 text-slate-600 hover:text-slate-900 hover:bg-white border border-transparent flex items-center justify-center gap-2">
        <i class="fa-solid fa-file-lines"></i> Izin dan Cuti
    </button>
</div>

<!-- List Container -->
<div id="attendance-list" class="grid grid-cols-1 gap-4">
    <!-- Loading Skeletons -->
    <div class="bg-white p-6 rounded-lg border border-gray-200 flex gap-4 animate-pulse">
        <div style="width: 112px; height: 144px;" class="bg-gray-200 rounded-lg flex-shrink-0"></div>
        <div class="flex-1 space-y-3">
            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
            <div class="h-6 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            <div class="h-8 bg-gray-200 rounded w-full mt-4"></div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-200 flex gap-4 animate-pulse">
        <div style="width: 112px; height: 144px;" class="bg-gray-200 rounded-lg flex-shrink-0"></div>
        <div class="flex-1 space-y-3">
            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
            <div class="h-6 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            <div class="h-8 bg-gray-200 rounded w-full mt-4"></div>
        </div>
    </div>
</div>

<!-- Empty State -->
<div id="empty-state" class="hidden text-center px-6 py-16 flex flex-col items-center justify-center bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-2xl text-emerald-600 mb-4">
        <i class="fa-solid fa-circle-check"></i>
    </div>
    <h3 class="text-base font-bold text-slate-900">Tidak ada permintaan tertunda</h3>
    <p class="text-sm text-slate-500 mt-1 max-w-md">Semua permintaan pada kategori ini sudah ditangani. Permintaan baru akan muncul di halaman ini.</p>
</div>

<!-- Toast Notification -->
<div id="toast-notif" class="fixed bottom-5 right-5 z-[1000] transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
    <div id="toast-content" class="bg-white border px-4 py-3 rounded-lg shadow-xl flex items-center gap-3">
        <span id="toast-icon"></span>
        <span id="toast-text" class="text-xs font-semibold text-gray-800"></span>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    const API_BASE_URL = "{{ url('') }}";
    let activeTab = 'absen';

    function switchTab(tabType) {
        activeTab = tabType;
        const tabAbsen = document.getElementById('tab-absen');
        const tabIzin = document.getElementById('tab-izin');
        const heading = document.getElementById('approval-heading');
        const description = document.getElementById('approval-description');
        
        if (tabType === 'absen') {
            tabAbsen.className = "flex-1 py-3 px-4 rounded-lg text-sm font-semibold transition duration-200 text-white bg-blue-700 shadow-sm flex items-center justify-center gap-2";
            tabIzin.className = "flex-1 py-3 px-4 rounded-lg text-sm font-semibold transition duration-200 text-slate-600 hover:text-slate-900 hover:bg-white border border-transparent flex items-center justify-center gap-2";
            tabAbsen.setAttribute('aria-selected', 'true');
            tabIzin.setAttribute('aria-selected', 'false');
            
            heading.childNodes[0].nodeValue = "Permintaan Persetujuan ";
            description.innerText = "Daftar karyawan yang melakukan absensi di luar radius kantor dan membutuhkan verifikasi.";
        } else {
            tabIzin.className = "flex-1 py-3 px-4 rounded-lg text-sm font-semibold transition duration-200 text-white bg-blue-700 shadow-sm flex items-center justify-center gap-2";
            tabAbsen.className = "flex-1 py-3 px-4 rounded-lg text-sm font-semibold transition duration-200 text-slate-600 hover:text-slate-900 hover:bg-white border border-transparent flex items-center justify-center gap-2";
            tabAbsen.setAttribute('aria-selected', 'false');
            tabIzin.setAttribute('aria-selected', 'true');
            
            heading.childNodes[0].nodeValue = "Permintaan Persetujuan Izin ";
            description.innerText = "Daftar permohonan izin atau cuti karyawan yang diajukan melalui aplikasi mobile.";
        }
        
        refreshData();
    }

    function refreshData() {
        if (activeTab === 'absen') {
            loadPendingAttendances();
        } else {
            loadPendingPermissions();
        }
    }

    async function loadPendingAttendances() {
        const container = document.getElementById('attendance-list');
        const emptyState = document.getElementById('empty-state');
        const countBadge = document.getElementById('pending-count-badge');
        emptyState.classList.add('hidden');
        
        container.innerHTML = `
            <div class="bg-white p-6 rounded-lg border border-gray-200 flex gap-4 animate-pulse">
                <div style="width: 112px; height: 144px;" class="bg-gray-200 rounded-lg flex-shrink-0"></div>
                <div class="flex-1 space-y-3">
                    <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                    <div class="h-6 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    <div class="h-8 bg-gray-200 rounded w-full mt-4"></div>
                </div>
            </div>
        `;

        try {
            const response = await fetch(`${API_BASE_URL}/master/api/admin/pending-attendance`);
            if (!response.ok) throw new Error('Gagal mengambil data.');
            
            const data = await response.json();
            countBadge.innerText = data.length;

            if (data.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            
            container.innerHTML = data.map(item => {
                const datetime = new Date(item.waktu).toLocaleString('id-ID', {
                    weekday: 'long', day: 'numeric', month: 'short', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });

                const typeBadge = item.tipe === 'masuk'
                    ? `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">ABSEN MASUK</span>`
                    : `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">ABSEN PULANG</span>`;

                const hasCoords = item.latitude !== null && item.latitude !== undefined && item.longitude !== null && item.longitude !== undefined;
                const mapsUrl = hasCoords ? `https://www.google.com/maps/search/?api=1&query=${item.latitude},${item.longitude}` : '#';

                return `
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-5 card-animate" id="card-${item.id}">
                        <!-- Selfie Preview -->
                        <div style="width: 112px; height: 144px;" class="bg-slate-50 rounded-xl overflow-hidden border border-slate-200 flex-shrink-0 flex items-center justify-center relative animate-fade-in">
                            ${item.foto 
                                ? `<img src="${item.foto.startsWith('http') || item.foto.startsWith('data:image') ? item.foto : API_BASE_URL + item.foto}" style="width: 100%; height: 100%; object-fit: cover;" class="animate-fade-in" alt="Selfie Karyawan" />`
                                : `<span class="text-4xl text-gray-300">👤</span>`
                            }
                        </div>

                        <!-- Details -->
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start gap-2 mb-2">
                                    ${typeBadge}
                                    <span class="text-xs font-medium text-slate-500">${datetime}</span>
                                </div>
                                
                                <h3 class="text-base font-bold text-slate-900 leading-snug">${item.nama_lengkap || 'Karyawan Tanpa Nama'}</h3>
                                <p class="text-xs text-slate-500 mt-1">NIK: ${item.nik} <span class="mx-1 text-slate-300">•</span> Divisi: ${item.divisi || 'Umum'}</p>
                                
                                <!-- Location -->
                                <div class="mt-2.5 flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-700">
                                        <span class="text-gray-400"><i class="fa-solid fa-location-dot"></i></span>
                                        ${hasCoords 
                                            ? `<a href="${mapsUrl}" target="_blank" class="hover:underline font-medium">
                                                    Buka di Google Maps (${parseFloat(item.latitude).toFixed(5)}, ${parseFloat(item.longitude).toFixed(5)})
                                               </a>`
                                            : `<span class="text-gray-400 italic">Koordinat tidak terdeteksi</span>`
                                        }
                                    </div>
                                    ${item.detail_lokasi ? `<p class="text-[10.5px] text-gray-500 ml-4 leading-snug"><i class="fa-solid fa-map-pin mr-1 text-gray-400"></i> ${item.detail_lokasi}</p>` : ''}
                                </div>

                                <!-- Reason -->
                                <div class="mt-4 bg-amber-50 border border-amber-100 p-3 rounded-xl">
                                    <p class="text-[10px] text-amber-800 font-bold uppercase tracking-wider">Alasan di luar area</p>
                                    <p class="text-sm text-amber-900 mt-1">${item.keterangan || 'Tidak menuliskan alasan'}</p>
                                </div>
                                
                                <!-- Admin Attachment -->
                                <div class="mt-3">
                                    <label for="admin_lampiran_absen_${item.id}" class="block text-xs font-semibold text-slate-700 mb-1.5">Dokumen pendukung <span class="font-normal text-slate-400">(opsional)</span></label>
                                    <input type="file" id="admin_lampiran_absen_${item.id}" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-200 rounded-lg bg-white cursor-pointer">
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3 mt-5 pt-4 border-t border-slate-100">
                                <button onclick="rejectAttendance(${item.id})" class="flex-1 py-2.5 text-sm font-semibold text-rose-700 hover:text-white bg-white hover:bg-rose-600 border border-rose-200 hover:border-rose-600 rounded-lg transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-xmark"></i> Tolak
                                </button>
                                <button onclick="approveAttendance(${item.id})" class="flex-1 py-2.5 text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 border border-transparent rounded-lg transition shadow-sm flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-check"></i> Setujui
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } catch (err) {
            showToast('❌', 'Gagal memuat permintaan persetujuan.', 'rose');
        }
    }

    async function loadPendingPermissions() {
        const container = document.getElementById('attendance-list');
        const emptyState = document.getElementById('empty-state');
        const countBadge = document.getElementById('pending-count-badge');
        emptyState.classList.add('hidden');
        
        container.innerHTML = `
            <div class="bg-white p-6 rounded-lg border border-gray-200 flex gap-4 animate-pulse">
                <div style="width: 112px; height: 144px;" class="bg-gray-200 rounded-lg flex-shrink-0"></div>
                <div class="flex-1 space-y-3">
                    <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                    <div class="h-6 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    <div class="h-8 bg-gray-200 rounded w-full mt-4"></div>
                </div>
            </div>
        `;

        try {
            const response = await fetch(`${API_BASE_URL}/master/api/admin/pending-permissions`);
            if (!response.ok) throw new Error('Gagal mengambil data.');
            
            const data = await response.json();
            countBadge.innerText = data.length;

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

                return `
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-5 card-animate" id="card-${item.tabel_sumber}-${item.id}">
                        <!-- Attachment Photo Preview -->
                        ${item.lampiran 
                            ? `
                            <div style="width: 112px; height: 144px;" class="bg-slate-50 rounded-xl overflow-hidden border border-slate-200 flex-shrink-0 flex items-center justify-center relative animate-fade-in">
                                <img src="${item.lampiran.startsWith('http') || item.lampiran.startsWith('data:image') ? item.lampiran : API_BASE_URL + item.lampiran}" style="width: 100%; height: 100%; object-fit: cover;" class="animate-fade-in" alt="Lampiran Izin Karyawan" onclick="window.open(this.src, '_blank')" style="cursor: zoom-in;" />
                            </div>
                            `
                            : ''
                        }

                        <!-- Details -->
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start gap-2 mb-2">
                                    ${typeBadge}
                                    <span class="text-xs font-medium text-slate-500">Diajukan: ${submitDate}</span>
                                </div>
                                
                                <h3 class="text-base font-bold text-slate-900 leading-snug">${item.nama || 'Karyawan Tanpa Nama'}</h3>
                                <p class="text-xs text-slate-500 mt-1">NIK: ${item.nik} <span class="mx-1 text-slate-300">•</span> Divisi: ${item.divisi || 'Umum'}</p>
                                
                                <div class="mt-2.5 flex flex-col gap-1 text-xs text-gray-700 font-medium">
                                    <div>
                                        <span class="text-gray-400 font-medium"><i class="fa-solid fa-calendar-days mr-1.5"></i></span>
                                        Tanggal: <span class="font-semibold text-gray-800">${dateRange}</span>
                                        ${leaveWarningHTML}
                                    </div>
                                    ${item.waktu ? `<div><span class="text-gray-400 font-medium"><i class="fa-solid fa-clock mr-1.5"></i></span>Waktu: <span class="font-semibold text-gray-800">${item.waktu}</span></div>` : ''}
                                    <div>
                                        <span class="text-gray-400 font-medium"><i class="fa-solid fa-paperclip mr-1.5"></i></span>
                                        Lampiran: <span class="font-semibold ${item.lampiran ? 'text-emerald-600' : 'text-gray-500'}">${item.lampiran ? 'Ada (Klik foto)' : 'Tidak ada'}</span>
                                    </div>
                                </div>

                                <!-- Reason -->
                                <div class="mt-4 bg-slate-50 border border-slate-200 p-3 rounded-xl">
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">${item.tabel_sumber === 'persetujuan_absensi_lupas' ? 'Alasan lupa absen' : 'Alasan izin'}</p>
                                    <p class="text-sm text-slate-800 mt-1">${item.alasan || 'Tidak menuliskan alasan'}</p>
                                </div>
                                
                                <!-- Admin Attachment -->
                                <div class="mt-3">
                                    <label for="admin_lampiran_izin_${item.tabel_sumber}_${item.id}" class="block text-xs font-semibold text-slate-700 mb-1.5">Dokumen pendukung <span class="font-normal text-slate-400">(opsional)</span></label>
                                    <input type="file" id="admin_lampiran_izin_${item.tabel_sumber}_${item.id}" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-200 rounded-lg bg-white cursor-pointer">
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3 mt-5 pt-4 border-t border-slate-100">
                                <button onclick="rejectPermission(${item.id}, '${item.tabel_sumber}')" class="flex-1 py-2.5 text-sm font-semibold text-rose-700 hover:text-white bg-white hover:bg-rose-600 border border-rose-200 hover:border-rose-600 rounded-lg transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-xmark"></i> Tolak
                                </button>
                                <button onclick="approvePermission(${item.id}, '${item.tabel_sumber}')" class="flex-1 py-2.5 text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 border border-transparent rounded-lg transition shadow-sm flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-check"></i> Setujui
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } catch (err) {
            showToast('❌', 'Gagal memuat permintaan izin.', 'rose');
        }
    }

    async function approveAttendance(id) {
        try {
            const formData = new FormData();
            formData.append('attendance_id', id);
            
            const fileInput = document.getElementById(`admin_lampiran_absen_${id}`);
            if (fileInput && fileInput.files[0]) {
                formData.append('admin_lampiran', fileInput.files[0]);
            }

            const response = await fetch(`${API_BASE_URL}/master/api/attendance/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            const resData = await response.json();
            
            if (!response.ok) throw new Error(resData.error || 'Gagal menyetujui.');

            showToast('✅', 'Absensi berhasil disetujui.', 'emerald');
            removeCardFromUI(`card-${id}`);
        } catch (err) {
            showToast('❌', err.message, 'rose');
        }
    }

    async function rejectAttendance(id) {
        if (!confirm('Apakah Anda yakin ingin menolak absensi ini?')) return;
        try {
            const formData = new FormData();
            formData.append('attendance_id', id);
            
            const fileInput = document.getElementById(`admin_lampiran_absen_${id}`);
            if (fileInput && fileInput.files[0]) {
                formData.append('admin_lampiran', fileInput.files[0]);
            }

            const response = await fetch(`${API_BASE_URL}/master/api/attendance/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            const resData = await response.json();
            
            if (!response.ok) throw new Error(resData.error || 'Gagal menolak.');

            showToast('✖', 'Absensi berhasil ditolak.', 'rose');
            removeCardFromUI(`card-${id}`);
        } catch (err) {
            showToast('❌', err.message, 'rose');
        }
    }

    async function approvePermission(id, tabel_sumber) {
        try {
            const formData = new FormData();
            formData.append('permission_id', id);
            formData.append('tabel_sumber', tabel_sumber);
            
            const fileInput = document.getElementById(`admin_lampiran_izin_${tabel_sumber}_${id}`);
            if (fileInput && fileInput.files[0]) {
                formData.append('admin_lampiran', fileInput.files[0]);
            }

            const response = await fetch(`${API_BASE_URL}/master/api/admin/permissions/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            const resData = await response.json();
            
            if (!response.ok) throw new Error(resData.error || 'Gagal menyetujui.');

            showToast('✅', 'Permohonan izin berhasil disetujui.', 'emerald');
            removeCardFromUI(`card-${tabel_sumber}-${id}`);
        } catch (err) {
            showToast('❌', err.message, 'rose');
        }
    }

    async function rejectPermission(id, tabel_sumber) {
        if (!confirm('Apakah Anda yakin ingin menolak permohonan izin ini?')) return;
        try {
            const formData = new FormData();
            formData.append('permission_id', id);
            formData.append('tabel_sumber', tabel_sumber);
            
            const fileInput = document.getElementById(`admin_lampiran_izin_${tabel_sumber}_${id}`);
            if (fileInput && fileInput.files[0]) {
                formData.append('admin_lampiran', fileInput.files[0]);
            }

            const response = await fetch(`${API_BASE_URL}/master/api/admin/permissions/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            const resData = await response.json();
            
            if (!response.ok) throw new Error(resData.error || 'Gagal menolak.');

            showToast('✖', 'Permohonan izin ditolak.', 'rose');
            removeCardFromUI(`card-${tabel_sumber}-${id}`);
        } catch (err) {
            showToast('❌', err.message, 'rose');
        }
    }

    function removeCardFromUI(cardId) {
        const card = document.getElementById(cardId);
        if (card) {
            card.style.transform = 'scale(0.9) translateY(20px)';
            card.style.opacity = '0';
            setTimeout(() => {
                card.remove();
                refreshData();
            }, 300);
        }
    }

    function showToast(icon, message, color) {
        const toast = document.getElementById('toast-notif');
        const toastIcon = document.getElementById('toast-icon');
        const toastText = document.getElementById('toast-text');
        const toastContent = document.getElementById('toast-content');

        toastIcon.innerText = icon;
        toastText.innerText = message;

        toastContent.className = `bg-white text-gray-800 border border-${color}-300 px-4 py-3 rounded-lg shadow-xl flex items-center gap-3`;
        toast.className = "fixed bottom-5 right-5 z-[1000] transform translate-y-0 opacity-100 transition-all duration-300";
        
        setTimeout(() => {
            toast.className = "fixed bottom-5 right-5 z-[1000] transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none";
        }, 3000);
    }

    window.onload = refreshData;
</script>
@endpush
