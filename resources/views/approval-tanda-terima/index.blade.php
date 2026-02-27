@extends('layouts.app')

@section('title', 'Approval Tanda Terima (Asuransi)')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <i class="fas fa-shield-alt text-indigo-600"></i>
                    </div>
                    Approval Tanda Terima (Asuransi)
                </h1>
                <p class="text-gray-600 mt-1">Verifikasi dan setujui dokumen asuransi untuk Tanda Terima FCL, LCL, dan Tanpa Surat Jalan.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    Data Terintegrasi
                </span>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 animate-fade-in">
        <div class="flex items-center">
            <div class="p-2 bg-emerald-100 rounded-lg shrink-0">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('info'))
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 animate-fade-in">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-lg"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-semibold text-blue-800">{{ session('info') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Filters -->
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <form method="GET" action="{{ route('approval-tanda-terima.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pencarian</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="No. SJ / TT, Penerima..." 
                               class="w-full pl-10 pr-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tipe Data</label>
                    <select name="type" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="all" {{ $type == 'all' ? 'selected' : '' }}>Semua Tipe</option>
                        <option value="fcl" {{ $type == 'fcl' ? 'selected' : '' }}>FCL (Tanda Terima)</option>
                        <option value="lcl" {{ $type == 'lcl' ? 'selected' : '' }}>LCL</option>
                        <option value="ttsj" {{ $type == 'ttsj' ? 'selected' : '' }}>Tanpa Surat Jalan (TTSJ)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status Approval</label>
                    <select name="status" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Menunggu Approval</option>
                        <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Sudah Disetujui</option>
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg transition-colors shadow-sm shadow-indigo-200">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                    <a href="{{ route('approval-tanda-terima.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Sumber</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Identitas</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Penerima / Pengirim</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Asuransi</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data as $item)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-tight
                                    {{ $item['source_type'] == 'fcl' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $item['source_type'] == 'lcl' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $item['source_type'] == 'ttsj' ? 'bg-orange-100 text-orange-700' : '' }}">
                                    {{ $item['type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $item['number'] }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ $item['date'] ? \Carbon\Carbon::parse($item['date'])->format('d M Y') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="text-sm text-gray-900 font-medium truncate max-w-[200px]" title="Penerima: {{ $item['penerima'] }}">
                                        <i class="fas fa-arrow-right text-emerald-500 mr-2 text-[10px]"></i>{{ $item['penerima'] ?: '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate max-w-[200px]" title="Pengirim: {{ $item['pengirim'] }}">
                                        <i class="fas fa-arrow-left text-blue-400 mr-2 text-[10px]"></i>{{ $item['pengirim'] ?: '-' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if(!empty($item['asuransi_paths']))
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-emerald-50 rounded-lg border border-emerald-100">
                                            <i class="fas fa-file-pdf text-emerald-600 text-lg"></i>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <span class="text-[10px] font-bold text-emerald-700 uppercase">{{ count($item['asuransi_paths']) }} Dokumen Tersedia</span>
                                            <div class="flex flex-col gap-0.5">
                                                @foreach($item['asuransi_paths'] as $index => $path)
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank" 
                                                   class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">
                                                    Dokumen {{ $index + 1 }}
                                                </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-3 text-gray-400">
                                        <div class="p-2 bg-gray-50 rounded-lg border border-gray-100">
                                            <i class="fas fa-file-excel text-gray-400 text-lg"></i>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase italic">Belum Ada</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($item['is_approved'])
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 w-fit">
                                            <i class="fas fa-check-circle mr-1"></i> APPROVED
                                        </span>
                                        <span class="text-[9px] text-gray-400 font-medium">
                                            {{ \Carbon\Carbon::parse($item['approved_at'])->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 w-fit">
                                        <i class="fas fa-clock mr-1"></i> PENDING
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    @can('approval-tanda-terima-upload')
                                    <button type="button" 
                                            onclick="openUploadModal('{{ $item['source_type'] }}', '{{ $item['id'] }}', '{{ $item['number'] }}')"
                                            class="p-2 bg-white border border-gray-200 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-all"
                                            title="Upload Asuransi">
                                        <i class="fas fa-upload"></i>
                                    </button>
                                    @endcan

                                    @can('approval-tanda-terima-approve')
                                    @if(!$item['is_approved'] && !empty($item['asuransi_paths']))
                                        <button type="button"
                                                onclick="openApproveModal('{{ $item['source_type'] }}', '{{ $item['id'] }}', '{{ $item['number'] }}')"
                                                class="p-2 bg-emerald-600 rounded-lg text-white hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-200"
                                                title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif

                                    @if($item['is_approved'])
                                        <button type="button"
                                                onclick="openRejectModal('{{ $item['source_type'] }}', '{{ $item['id'] }}', '{{ $item['number'] }}')"
                                                class="p-2 bg-rose-50 border border-rose-200 rounded-lg text-rose-600 hover:bg-rose-100 transition-all"
                                                title="Batalkan Approval">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-folder-open text-gray-300 text-3xl"></i>
                                    </div>
                                    <h3 class="text-gray-900 font-bold">Tidak Ada Data</h3>
                                    <p class="text-gray-500 text-sm mt-1 max-w-xs mx-auto">Tidak ditemukan data tanda terima yang sesuai dengan filter pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('uploadModal')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all animate-modal-enter">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-upload text-indigo-600"></i>
                        Upload Dokumen Asuransi
                    </h3>
                    <button onclick="closeModal('uploadModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <form id="uploadForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Identitas Tanda Terima</label>
                        <input type="text" id="upload_identity" readonly class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm text-gray-600 font-mono">
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih File Dokumen</label>
                        <div class="relative group">
                            <input type="file" name="asuransi_file[]" multiple required accept=".pdf,.jpg,.jpeg,.png"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                   onchange="updateFileName(this)">
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 flex flex-col items-center justify-center group-hover:border-indigo-400 transition-colors">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3 group-hover:text-indigo-500 transition-colors"></i>
                                <span class="text-sm font-medium text-gray-600" id="file_name_display">Klik atau seret file ke sini</span>
                                <span class="text-[10px] text-gray-400 mt-2 uppercase tracking-widest font-bold">PDF, JPG, PNG (Max 10MB)</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeModal('uploadModal')" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-all">
                        Unggah Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('approveModal')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all animate-modal-enter">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600"></i>
                        Approve Asuransi
                    </h3>
                    <button onclick="closeModal('approveModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Identitas</label>
                        <input type="text" id="approve_identity" readonly class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm text-gray-600 font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan (Opsional)</label>
                        <textarea name="keterangan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-sm"></textarea>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700">Tutup</button>
                    <button type="submit" class="px-6 py-2 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 shadow-sm shadow-emerald-200 transition-all">
                        Approve Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject (Cancel Approval) Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('rejectModal')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all animate-modal-enter">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-rose-600"></i>
                        Batalkan Approval
                    </h3>
                    <button onclick="closeModal('rejectModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="p-6">
                    <div class="p-4 bg-rose-50 border border-rose-100 rounded-xl mb-4">
                        <p class="text-xs font-semibold text-rose-800 leading-relaxed">
                            <i class="fas fa-info-circle mr-1"></i>
                            Anda akan membatalkan status approval untuk data ini. Dokumen asuransi yang sudah diunggah tidak akan dihapus.
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Identitas</label>
                        <input type="text" id="reject_identity" readonly class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-sm text-gray-600 font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Pembatalan</label>
                        <textarea name="keterangan" rows="3" required placeholder="Tuliskan alasan pembatalan status approval..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all text-sm"></textarea>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-rose-600 text-white text-sm font-bold rounded-lg hover:bg-rose-700 shadow-sm shadow-rose-200 transition-all">
                        Ya, Batalkan Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes modal-enter {
        from { opacity: 0; transform: scale(0.95) translateY(-20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .animate-modal-enter {
        animation: modal-enter 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.4s ease-out;
    }
</style>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function updateFileName(input) {
        const display = document.getElementById('file_name_display');
        
        // Reset old content
        display.innerHTML = '';
        display.classList.remove('text-indigo-600', 'font-bold');

        if (input.files && input.files.length > 0) {
            display.classList.add('flex', 'flex-col', 'gap-1', 'items-center', 'mt-2', 'w-full');
            
            for (let i = 0; i < input.files.length; i++) {
                const fileItem = document.createElement('div');
                fileItem.className = 'text-xs text-indigo-700 bg-indigo-50 px-3 py-1.5 rounded-md border border-indigo-100 flex items-center justify-between w-full max-w-xs truncate';
                
                // Add icon and text
                fileItem.innerHTML = `
                    <div class="flex items-center gap-2 truncate">
                        <i class="fas fa-file text-indigo-400"></i>
                        <span class="truncate font-semibold" title="${input.files[i].name}">${input.files[i].name}</span>
                    </div>
                `;
                display.appendChild(fileItem);
            }
        } else {
            // Revert back to default layout
            display.classList.remove('flex', 'flex-col', 'gap-1', 'items-center', 'mt-2', 'w-full');
            display.textContent = 'Klik atau seret file ke sini';
        }
    }

    function openUploadModal(sourceType, id, identity) {
        const form = document.getElementById('uploadForm');
        form.action = `/approval-tanda-terima/${sourceType}/${id}/upload`;
        document.getElementById('upload_identity').value = identity;
        openModal('uploadModal');
    }

    function openApproveModal(sourceType, id, identity) {
        const form = document.getElementById('approveForm');
        form.action = `/approval-tanda-terima/${sourceType}/${id}/approve`;
        document.getElementById('approve_identity').value = identity;
        openModal('approveModal');
    }

    function openRejectModal(sourceType, id, identity) {
        const form = document.getElementById('rejectForm');
        form.action = `/approval-tanda-terima/${sourceType}/${id}/reject`;
        document.getElementById('reject_identity').value = identity;
        openModal('rejectModal');
    }
</script>
@endsection
