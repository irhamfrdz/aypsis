@extends('layouts.app')

@section('title', 'Kelola Jam Kerja')
@section('page_title', 'Kelola Jam Kerja')

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
<div class="bg-white rounded-lg border border-gray-200 p-6 mb-6 shadow-sm">
    <h1 class="text-xl font-bold text-gray-900 leading-tight">Kelola Jam Kerja</h1>
    <p class="text-xs text-gray-500 mt-1">Konfigurasi shift kerja karyawan beserta toleransi keterlambatan sistem absensi.</p>
</div>

<!-- Main Layout Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    
    <!-- Left Panel: Form Card (5 Cols) -->
    <div class="lg:col-span-5 flex flex-col gap-6">
        <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
            <h3 id="form-title" class="text-sm font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                <span class="text-blue-600"><i class="fa-solid fa-plus-minus"></i></span> Tambah Shift Kerja
            </h3>
            
            <form id="shift-form" class="space-y-4">
                <input type="hidden" id="shift-id" value="">
                
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nama Shift</label>
                    <input type="text" id="nama-shift" placeholder="Contoh: Shift Pagi, Shift Regular" required
                        class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Jam Masuk</label>
                        <input type="time" id="jam-masuk" required
                            class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Jam Keluar</label>
                        <input type="time" id="jam-keluar" required
                            class="w-full bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Toleransi Keterlambatan</label>
                    <div class="flex items-center gap-2">
                        <input type="number" id="toleransi" min="0" value="0" required
                            class="flex-1 bg-white border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        <span class="text-xs text-gray-500 font-bold bg-gray-50 border border-gray-300 px-3 py-2.5 rounded-md">MENIT</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 py-1">
                    <input type="checkbox" id="is-active" checked value="1"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                    <label for="is-active" class="text-xs font-semibold text-gray-700 select-none cursor-pointer">Status Aktif (Digunakan)</label>
                </div>

                <div class="flex gap-2 pt-1">
                    <button type="button" id="cancel-edit-btn" class="hidden flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 rounded-md text-sm transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md text-sm transition shadow-sm">
                        Simpan Shift
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Panel: List Card (7 Cols) -->
    <div class="lg:col-span-7 flex flex-col gap-4 bg-white rounded-lg border border-gray-200 p-5 shadow-sm h-fit min-h-[350px]">
        <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <span class="text-blue-600"><i class="fa-solid fa-list"></i></span> Daftar Shift Terdaftar
            </h3>
            <span id="shift-count-badge" class="bg-blue-50 text-blue-700 text-xs px-2.5 py-0.5 rounded-full border border-blue-200 font-semibold">0 Shift</span>
        </div>

        <!-- List Container -->
        <div id="shifts-list" class="space-y-3 flex-1 overflow-y-auto max-h-[480px] custom-scrollbar pr-1">
            <!-- Skeletons -->
            <div class="p-4 border border-gray-100 rounded-lg animate-pulse bg-gray-50 flex justify-between items-center">
                <div class="space-y-2 w-1/3">
                    <div class="h-4 bg-gray-200 rounded"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                </div>
                <div class="h-8 bg-gray-200 rounded w-16"></div>
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-12 flex flex-col items-center justify-center">
            <div class="w-12 h-12 rounded-full bg-slate-50 border border-gray-200 flex items-center justify-center text-xl mb-3">
                📭
            </div>
            <h4 class="text-xs font-bold text-gray-800">Tidak ada shift</h4>
            <p class="text-[11px] text-gray-500 mt-1">Silakan tambahkan konfigurasi jam kerja baru.</p>
        </div>
    </div>

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
    const API_BASE_URL = "{{ request()->getScheme() }}://{{ request()->getHost() }}:8084";
    let shiftsData = [];

    async function loadShifts() {
        const container = document.getElementById('shifts-list');
        const emptyState = document.getElementById('empty-state');
        const countBadge = document.getElementById('shift-count-badge');
        
        try {
            const response = await fetch(`${API_BASE_URL}/api/working-hours`);
            if (!response.ok) throw new Error('Gagal mengambil data.');
            
            shiftsData = await response.json();
            countBadge.innerText = `${shiftsData.length} Shift`;

            if (shiftsData.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            
            container.innerHTML = shiftsData.map(item => {
                const statusBadge = item.is_active === 1
                    ? `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">AKTIF</span>`
                    : `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-50 text-gray-600 border border-gray-200">NON-AKTIF</span>`;

                return `
                    <div class="p-4 border border-gray-200 rounded-lg flex items-center justify-between card-animate bg-white" id="shift-card-${item.id}">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <h4 class="text-sm font-bold text-gray-900 leading-snug">${item.nama_shift}</h4>
                                ${statusBadge}
                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-clock text-gray-400"></i> ${item.jam_masuk.substring(0, 5)} - ${item.jam_keluar.substring(0, 5)}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-hourglass-half text-gray-400"></i> Toleransi: ${item.toleransi_keterlambatan} menit
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button onclick="editShift(${item.id})" class="p-1.5 text-blue-600 hover:bg-blue-50 border border-transparent rounded-md transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button onclick="deleteShift(${item.id})" class="p-1.5 text-rose-600 hover:bg-rose-50 border border-transparent rounded-md transition" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        } catch (err) {
            showToast('❌', 'Gagal memuat daftar shift.', 'rose');
        }
    }

    document.getElementById('shift-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const id = document.getElementById('shift-id').value;
        const nama_shift = document.getElementById('nama-shift').value;
        const jam_masuk = document.getElementById('jam-masuk').value;
        const jam_keluar = document.getElementById('jam-keluar').value;
        const toleransi_keterlambatan = document.getElementById('toleransi').value;
        const is_active = document.getElementById('is-active').checked ? 1 : 0;

        const payload = { nama_shift, jam_masuk, jam_keluar, toleransi_keterlambatan, is_active };
        
        try {
            let response;
            if (id) {
                response = await fetch(`${API_BASE_URL}/api/working-hours/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
            } else {
                response = await fetch(`${API_BASE_URL}/api/working-hours`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
            }
            
            const resData = await response.json();
            if (!response.ok) throw new Error(resData.error || 'Gagal menyimpan shift.');

            showToast('✅', id ? 'Shift berhasil diperbarui.' : 'Shift berhasil ditambahkan.', 'emerald');
            resetForm();
            loadShifts();
        } catch (err) {
            showToast('❌', err.message, 'rose');
        }
    });

    function editShift(id) {
        const item = shiftsData.find(s => s.id === id);
        if (!item) return;

        document.getElementById('shift-id').value = item.id;
        document.getElementById('nama-shift').value = item.nama_shift;
        document.getElementById('jam-masuk').value = item.jam_masuk.substring(0, 5);
        document.getElementById('jam-keluar').value = item.jam_keluar.substring(0, 5);
        document.getElementById('toleransi').value = item.toleransi_keterlambatan;
        document.getElementById('is-active').checked = item.is_active === 1;

        // Update Form Title
        document.getElementById('form-title').innerHTML = `<span class="text-blue-600"><i class="fa-solid fa-pen-to-square"></i></span> Edit Shift Kerja`;
        document.getElementById('cancel-edit-btn').classList.remove('hidden');
    }

    async function deleteShift(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus shift kerja ini?')) return;
        
        try {
            const response = await fetch(`${API_BASE_URL}/api/working-hours/${id}`, {
                method: 'DELETE'
            });
            const resData = await response.json();
            
            if (!response.ok) throw new Error(resData.error || 'Gagal menghapus shift.');

            showToast('✖', 'Shift berhasil dihapus.', 'rose');
            loadShifts();
            resetForm();
        } catch (err) {
            showToast('❌', err.message, 'rose');
        }
    }

    document.getElementById('cancel-edit-btn').addEventListener('click', resetForm);

    function resetForm() {
        document.getElementById('shift-id').value = '';
        document.getElementById('nama-shift').value = '';
        document.getElementById('jam-masuk').value = '';
        document.getElementById('jam-keluar').value = '';
        document.getElementById('toleransi').value = 0;
        document.getElementById('is-active').checked = true;

        // Reset Form Title
        document.getElementById('form-title').innerHTML = `<span class="text-blue-600"><i class="fa-solid fa-plus-minus"></i></span> Tambah Shift Kerja`;
        document.getElementById('cancel-edit-btn').classList.add('hidden');
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

    window.onload = loadShifts;
</script>
@endpush
