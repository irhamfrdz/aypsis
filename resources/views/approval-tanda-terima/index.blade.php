@extends('layouts.app')

@section('title', 'Upload Dokumen Tanda Terima')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <i class="fas fa-file-upload text-indigo-600"></i>
                    </div>
                    Upload Dokumen Tanda Terima
                </h1>
                <p class="text-gray-600 mt-1">Upload dokumen untuk Tanda Terima FCL, LCL, dan Tanpa Surat Jalan.</p>
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
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Dokumen</th>
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
                                                @php
                                                    $filePath = is_array($path) ? ($path['path'] ?? '') : $path;
                                                    $fileType = is_array($path) && isset($path['type']) ? $path['type'] : 'Lainnya';
                                                    $column = is_array($path) && isset($path['column']) ? $path['column'] : 'asuransi_path';
                                                    $originalIndex = is_array($path) && isset($path['original_index']) ? $path['original_index'] : $index;
                                                @endphp
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ asset('storage/' . $filePath) }}" target="_blank" 
                                                       class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">
                                                        Dokumen {{ $fileType }}
                                                    </a>
                                                    @can('approval-tanda-terima-upload')
                                                    <form action="{{ route('approval-tanda-terima.delete-document', ['sourceType' => $item['source_type'], 'id' => $item['id'], 'column' => $column, 'index' => $originalIndex]) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-rose-500 hover:text-rose-700 p-0.5 rounded-md hover:bg-rose-50 transition-colors" title="Hapus Dokumen">
                                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    @can('approval-tanda-terima-upload')
                                    <button type="button" 
                                            onclick="openUploadModal('{{ $item['source_type'] }}', '{{ $item['id'] }}', '{{ $item['number'] }}')"
                                            class="p-2 bg-white border border-gray-200 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-all"
                                            title="Upload Dokumen">
                                        <i class="fas fa-upload"></i>
                                    </button>
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
                        Upload Dokumen
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
                    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                        <!-- PPBJ -->
                        <div class="p-4 bg-gray-50/50 border border-gray-200 rounded-xl hover:border-indigo-200 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Dokumen PPBJ</label>
                                    <span class="text-[10px] text-gray-400">PDF, JPG, PNG (Max 10MB)</span>
                                </div>
                                <button type="button" onclick="addFileInput('ppbj-container', 'file_ppbj[]')" 
                                        class="text-[10px] font-bold uppercase bg-indigo-100 text-indigo-700 px-2.5 py-1.5 rounded-lg hover:bg-indigo-200 transition-all flex items-center gap-1">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                            <div id="ppbj-container" class="space-y-2">
                                <div class="relative flex items-center gap-2 bg-white p-2 rounded-lg border border-gray-100 shadow-sm group">
                                    <input type="file" name="file_ppbj[]" accept=".pdf,.jpg,.jpeg,.png"
                                           class="flex-1 text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Packing List -->
                        <div class="p-4 bg-gray-50/50 border border-gray-200 rounded-xl hover:border-indigo-200 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Dokumen Packing List</label>
                                    <span class="text-[10px] text-gray-400">PDF, JPG, PNG (Max 10MB)</span>
                                </div>
                                <button type="button" onclick="addFileInput('packing-list-container', 'file_packing_list[]')" 
                                        class="text-[10px] font-bold uppercase bg-indigo-100 text-indigo-700 px-2.5 py-1.5 rounded-lg hover:bg-indigo-200 transition-all flex items-center gap-1">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                            <div id="packing-list-container" class="space-y-2">
                                <div class="relative flex items-center gap-2 bg-white p-2 rounded-lg border border-gray-100 shadow-sm group">
                                    <input type="file" name="file_packing_list[]" accept=".pdf,.jpg,.jpeg,.png"
                                           class="flex-1 text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Invoice -->
                        <div class="p-4 bg-gray-50/50 border border-gray-200 rounded-xl hover:border-indigo-200 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Dokumen Invoice</label>
                                    <span class="text-[10px] text-gray-400">PDF, JPG, PNG (Max 10MB)</span>
                                </div>
                                <button type="button" onclick="addFileInput('invoice-container', 'file_invoice[]')" 
                                        class="text-[10px] font-bold uppercase bg-indigo-100 text-indigo-700 px-2.5 py-1.5 rounded-lg hover:bg-indigo-200 transition-all flex items-center gap-1">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                            <div id="invoice-container" class="space-y-2">
                                <div class="relative flex items-center gap-2 bg-white p-2 rounded-lg border border-gray-100 shadow-sm group">
                                    <input type="file" name="file_invoice[]" accept=".pdf,.jpg,.jpeg,.png"
                                           class="flex-1 text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Faktur Pajak -->
                        <div class="p-4 bg-gray-50/50 border border-gray-200 rounded-xl hover:border-indigo-200 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Dokumen Faktur Pajak</label>
                                    <span class="text-[10px] text-gray-400">PDF, JPG, PNG (Max 10MB)</span>
                                </div>
                                <button type="button" onclick="addFileInput('faktur-pajak-container', 'file_faktur_pajak[]')" 
                                        class="text-[10px] font-bold uppercase bg-indigo-100 text-indigo-700 px-2.5 py-1.5 rounded-lg hover:bg-indigo-200 transition-all flex items-center gap-1">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                            <div id="faktur-pajak-container" class="space-y-2">
                                <div class="relative flex items-center gap-2 bg-white p-2 rounded-lg border border-gray-100 shadow-sm group">
                                    <input type="file" name="file_faktur_pajak[]" accept=".pdf,.jpg,.jpeg,.png"
                                           class="flex-1 text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                </div>
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

    function openUploadModal(sourceType, id, identity) {
        const form = document.getElementById('uploadForm');
        form.action = `/approval-tanda-terima/${sourceType}/${id}/upload`;
        
        // Reset inputs
        form.reset();
        document.getElementById('upload_identity').value = identity;

        // Clear additional rows and keep only one for each
        ['ppbj-container', 'packing-list-container', 'invoice-container', 'faktur-pajak-container'].forEach(containerId => {
            const container = document.getElementById(containerId);
            const inputName = containerId === 'ppbj-container' ? 'file_ppbj[]' : 
                             containerId === 'packing-list-container' ? 'file_packing_list[]' :
                             containerId === 'invoice-container' ? 'file_invoice[]' : 'file_faktur_pajak[]';
            
            container.innerHTML = `
                <div class="relative flex items-center gap-2 bg-white p-2 rounded-lg border border-gray-100 shadow-sm group">
                    <input type="file" name="${inputName}" accept=".pdf,.jpg,.jpeg,.png"
                           class="flex-1 text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                </div>
            `;
        });

        openModal('uploadModal');
    }

    function addFileInput(containerId, inputName) {
        const container = document.getElementById(containerId);
        const newRow = document.createElement('div');
        newRow.className = 'relative flex items-center gap-2 bg-white p-2 rounded-lg border border-gray-100 shadow-sm group animate-fade-in';
        newRow.innerHTML = `
            <input type="file" name="${inputName}" accept=".pdf,.jpg,.jpeg,.png"
                   class="flex-1 text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
            <button type="button" onclick="this.parentElement.remove()" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-md transition-colors">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(newRow);
    }
</script>
@endsection
