@extends('layouts.app')

@section('title', 'Persetujuan Absensi Karyawan')
@section('page_title', 'Persetujuan Absensi Karyawan')

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
            Permintaan Persetujuan <span id="pending-count-badge" class="bg-amber-100 text-amber-800 text-xs px-2.5 py-0.5 rounded-full border border-amber-200 font-bold">0</span>
        </h1>
        <p class="text-xs text-gray-500 mt-1">Daftar karyawan yang melakukan absensi di luar radius kantor dan membutuhkan verifikasi.</p>
    </div>
    <div class="flex items-center gap-2 mt-3 sm:mt-0">
        <a href="{{ route('persetujuan-absensi.riwayat') }}" class="px-3.5 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold border border-blue-200 transition flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Izin
        </a>
        <button onclick="refreshData()" class="px-3.5 py-1.5 rounded-md bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold border border-gray-300 transition flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-arrows-rotate"></i> Segarkan Data
        </button>
    </div>
</div>

<!-- Tab Selector -->
<div class="flex border-b border-gray-200 bg-white rounded-lg p-1.5 mb-6 shadow-sm gap-2">
    <button id="tab-absen" onclick="switchTab('absen')" class="flex-1 py-2 px-4 rounded-md text-xs font-bold transition duration-200 text-white bg-blue-600 shadow-sm">
        📍 Absensi Diluar Area
    </button>
    <button id="tab-izin" onclick="switchTab('izin')" class="flex-1 py-2 px-4 rounded-md text-xs font-bold transition duration-200 text-gray-600 hover:text-gray-900 hover:bg-gray-100 border border-transparent">
        📝 Permohonan Izin / Cuti
    </button>
</div>

<!-- List Container -->
<div id="attendance-list" class="grid grid-cols-1 gap-6 max-w-2xl mx-auto">
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
<div id="empty-state" class="hidden text-center py-20 flex flex-col items-center justify-center bg-white rounded-lg border border-gray-200 shadow-sm">
    <div class="w-16 h-16 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-3xl mb-4 shadow-sm">
    </div>
    <h3 class="text-sm font-bold text-gray-900">Semua Bersih!</h3>
    <p class="text-xs text-gray-500 mt-1 max-w-xs">Tidak ada permintaan persetujuan absensi yang tertunda saat ini. Karyawan tertib melakukan absensi.</p>
</div>

<!-- Toast Notification -->
<div id="toast-notif" class="fixed bottom-5 right-5 z-[1000] transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
    <div id="toast-content" class="bg-white border px-4 py-3 rounded-lg shadow-xl flex items-center gap-3">
        <span id="toast-icon"></span>
        <span id="toast-text" class="text-xs font-semibold text-gray-800"></span>
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
        
        if (tabType === 'absen') {
            tabAbsen.className = "flex-1 py-2 px-4 rounded-md text-xs font-bold transition duration-200 text-white bg-blue-600 shadow-sm";
            tabIzin.className = "flex-1 py-2 px-4 rounded-md text-xs font-bold transition duration-200 text-gray-600 hover:text-gray-900 hover:bg-gray-100 border border-transparent";
            
            document.querySelector('h1.text-xl').childNodes[0].nodeValue = "🔔 Permintaan Persetujuan ";
            document.querySelector('p.text-xs').innerText = "Daftar karyawan yang melakukan absensi di luar radius kantor dan membutuhkan verifikasi.";
        } else {
            tabIzin.className = "flex-1 py-2 px-4 rounded-md text-xs font-bold transition duration-200 text-white bg-blue-600 shadow-sm";
            tabAbsen.className = "flex-1 py-2 px-4 rounded-md text-xs font-bold transition duration-200 text-gray-600 hover:text-gray-900 hover:bg-gray-100 border border-transparent";
            
            document.querySelector('h1.text-xl').childNodes[0].nodeValue = "🔔 Permintaan Persetujuan Izin ";
            document.querySelector('p.text-xs').innerText = "Daftar permohonan izin atau cuti karyawan yang diajukan dari aplikasi mobile.";
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
                    <div class="bg-white p-5 rounded-lg border border-gray-200 flex flex-col sm:flex-row gap-5 card-animate" id="card-${item.id}">
                        <!-- Selfie Preview -->
                        <div style="width: 112px; height: 144px;" class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0 flex items-center justify-center relative shadow-inner animate-fade-in">
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
                                    <span class="text-[10px] font-medium text-gray-400">${datetime}</span>
                                </div>
                                
                                <h3 class="text-sm font-bold text-gray-900 leading-snug">${item.nama_lengkap || 'Karyawan Tanpa Nama'}</h3>
                                <p class="text-[11px] text-gray-500 mt-0.5">NIK: ${item.nik} &bull; Divisi: ${item.divisi || 'Umum'}</p>
                                
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
                                <div class="mt-3 bg-amber-50/50 border-l-2 border-amber-500 p-2.5 rounded-r-md">
                                    <p class="text-[9px] text-amber-800 font-bold uppercase tracking-wider">Alasan Diluar Area:</p>
                                    <p class="text-xs text-amber-700 font-medium italic mt-0.5">"${item.keterangan || 'Tidak menuliskan alasan'}"</p>
                                </div>
                                
                                <!-- Admin Attachment -->
                                <div class="mt-3">
                                    <label for="admin_lampiran_absen_${item.id}" class="block text-[10px] font-medium text-gray-700 mb-1">Unggah Dokumen Admin (Opsional)</label>
                                    <input type="file" id="admin_lampiran_absen_${item.id}" class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-200 rounded-md bg-gray-50 cursor-pointer">
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3 mt-4 pt-3 border-t border-gray-100">
                                <button onclick="rejectAttendance(${item.id})" class="flex-1 py-1.5 text-xs font-semibold text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 hover:border-transparent rounded-md transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-xmark"></i> Tolak
                                </button>
                                <button onclick="approveAttendance(${item.id})" class="flex-1 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md transition shadow-sm flex items-center justify-center gap-1.5">
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
                    <div class="bg-white p-5 rounded-lg border border-gray-200 flex flex-col sm:flex-row gap-5 card-animate" id="card-${item.tabel_sumber}-${item.id}">
                        <!-- Attachment Photo Preview -->
                        ${item.lampiran 
                            ? `
                            <div style="width: 112px; height: 144px;" class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0 flex items-center justify-center relative shadow-inner animate-fade-in">
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
                                    <span class="text-[10px] font-medium text-gray-400">Diajukan: ${submitDate}</span>
                                </div>
                                
                                <h3 class="text-sm font-bold text-gray-900 leading-snug">${item.nama || 'Karyawan Tanpa Nama'}</h3>
                                <p class="text-[11px] text-gray-500 mt-0.5">NIK: ${item.nik} &bull; Divisi: ${item.divisi || 'Umum'}</p>
                                
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
                                <div class="mt-3 bg-slate-50 border-l-2 border-slate-400 p-2.5 rounded-r-md">
                                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider">${item.tabel_sumber === 'persetujuan_absensi_lupas' ? 'Alasan Lupa Absen:' : 'Alasan Izin:'}</p>
                                    <p class="text-xs text-slate-700 font-medium italic mt-0.5">"${item.alasan || 'Tidak menuliskan alasan'}"</p>
                                </div>
                                
                                <!-- Admin Attachment -->
                                <div class="mt-3">
                                    <label for="admin_lampiran_izin_${item.tabel_sumber}_${item.id}" class="block text-[10px] font-medium text-gray-700 mb-1">Unggah Dokumen Admin (Opsional)</label>
                                    <input type="file" id="admin_lampiran_izin_${item.tabel_sumber}_${item.id}" class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-200 rounded-md bg-gray-50 cursor-pointer">
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3 mt-4 pt-3 border-t border-gray-100">
                                <button onclick="rejectPermission(${item.id}, '${item.tabel_sumber}')" class="flex-1 py-1.5 text-xs font-semibold text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 hover:border-transparent rounded-md transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-xmark"></i> Tolak
                                </button>
                                <button onclick="approvePermission(${item.id}, '${item.tabel_sumber}')" class="flex-1 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md transition shadow-sm flex items-center justify-center gap-1.5">
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
