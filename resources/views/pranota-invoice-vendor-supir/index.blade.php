@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Pranota Invoice Vendor Supir</h2>
            <p class="text-sm text-gray-500">Gabungkan berbagai invoice tagihan supir vendor menjadi satu pranota</p>
        </div>
        @if(auth()->user()->can('pranota-invoice-vendor-supir-create'))
        <a href="{{ route('pranota-invoice-vendor-supir.create') }}" class="bg-rose-600 hover:bg-rose-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors flex items-center shadow-sm" style="background-color: #e11d48; box-shadow: 0 4px 6px -1px rgba(225, 29, 72, 0.3);">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Buat Pranota Baru
        </a>
        @endif
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('pranota-invoice-vendor-supir.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div class="col-span-1 md:col-span-3 relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-sm" 
                    placeholder="Cari No Pranota atau Nama Vendor...">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="col-span-1 flex space-x-2">
                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors flex justify-center items-center">
                    Cari
                </button>
                <a href="{{ route('pranota-invoice-vendor-supir.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg text-sm transition-colors text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">No Pranota</th>
                        <th class="px-6 py-4">Vendor</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Total Nominal</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pranotas as $pranota)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-rose-600">
                            {{ $pranota->no_pranota }}
                        </td>
                        <td class="px-6 py-4 font-medium">{{ $pranota->vendor->nama_vendor ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $pranota->tanggal_pranota->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">Rp {{ number_format($pranota->grand_total > 0 ? $pranota->grand_total : $pranota->total_nominal, 0, ',', '.') }}</div>
                            @php
                                $originalSubtotal = $pranota->total_nominal;
                                if ($pranota->pph > 0) {
                                    $originalSubtotal += $pranota->pph;
                                }
                            @endphp
                            <div class="text-[10px] text-gray-500 mt-0.5">Subtotal: Rp {{ number_format($originalSubtotal, 0, ',', '.') }}</div>
                            @if($pranota->pph > 0)
                                <div class="text-[10px] text-red-500 mt-0.5 italic">- PPH 2%: Rp {{ number_format($pranota->pph, 0, ',', '.') }}</div>
                            @endif
                            @if($pranota->total_uang_muat > 0)
                                <div class="text-[10px] text-indigo-600 mt-0.5 italic">+ Uang Muat: Rp {{ number_format($pranota->total_uang_muat, 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($pranota->status_pembayaran == 'lunas')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Lunas
                                </span>
                            @elseif($pranota->status_pembayaran == 'sebagian')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Sebagian
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Belum Dibayar
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-3">
                                <a href="{{ route('pranota-invoice-vendor-supir.show', $pranota->id) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>

                                <a href="{{ route('pranota-invoice-vendor-supir.print', $pranota->id) }}" target="_blank" class="text-gray-600 hover:text-gray-800 transition-colors" title="Cetak Pranota">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                
                                <a href="{{ route('pranota-invoice-vendor-supir.edit', $pranota->id) }}" class="text-amber-500 hover:text-amber-700 transition-colors" title="Edit Pranota">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>

                                <button type="button" onclick="confirmDelete('{{ $pranota->id }}')" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Pranota">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                
                                @if(auth()->user()->can('pranota-invoice-vendor-supir-update') && $pranota->pph <= 0)
                                <button type="button" onclick="confirmAddPph('{{ $pranota->id }}')" class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Tambahkan PPH 2%">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                                </button>
                                <form id="add-pph-form-{{ $pranota->id }}" action="{{ route('pranota-invoice-vendor-supir.add-pph', $pranota->id) }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                                @endif

                                @if(auth()->user()->can('pranota-invoice-vendor-supir-update'))
                                <button type="button" onclick="openUangMuatModal('{{ $pranota->id }}')" class="text-indigo-500 hover:text-indigo-700 transition-colors" title="Kelola Uang Muat">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                @endif

                                <form id="delete-form-{{ $pranota->id }}" action="{{ route('pranota-invoice-vendor-supir.destroy', $pranota->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center">
                            <div class="flex flex-col flex-auto items-center justify-center p-4">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-sm text-gray-500 font-medium">Belum ada data Pranota Invoice Vendor Supir!</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pranotas->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $pranotas->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    function confirmDelete(id) {
        if(confirm('Apakah Anda yakin ingin menghapus pranota ini? Data invoice di dalamnya tidak akan terhapus, hanya dilepaskan kembali.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    function confirmAddPph(id) {
        if(confirm('Apakah Anda yakin ingin menambahkan PPH 2% pada pranota ini? Total nominal akan dikurangi 2% dan disimpan sebagai PPH.')) {
            document.getElementById('add-pph-form-' + id).submit();
        }
    }

    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    function openUangMuatModal(id) {
        const container = document.getElementById('sj-list-container');
        container.innerHTML = '<tr><td colspan="2" class="px-4 py-8 text-center text-gray-500">Memuat data Surat Jalan...</td></tr>';
        
        const form = document.getElementById('form-uang-muat');
        form.action = `/pranota-invoice-vendor-supir/${id}/add-uang-muat`;
        
        toggleModal('modal-uang-muat');
        
        fetch(`/pranota-invoice-vendor-supir/${id}/get-surat-jalans`)
            .then(response => response.json())
            .then(data => {
                container.innerHTML = '';
                if (data.error) throw new Error(data.error);
                if (data.length === 0) {
                    container.innerHTML = '<tr><td colspan="2" class="px-4 py-8 text-center text-gray-500">Tidak ada Surat Jalan dalam Pranota ini.</td></tr>';
                    return;
                }
                
                data.forEach(sj => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50 transition-colors';
                    row.innerHTML = `
                        <td class="px-4 py-3 font-medium text-gray-700">${sj.no_surat_jalan}</td>
                        <td class="px-4 py-3">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">Rp</span>
                                <input type="text" name="uang_muat[${sj.id}]" value="${formatNumber(sj.uang_muat)}" 
                                    class="w-full pl-10 pr-4 py-2 text-sm border-gray-300 rounded-lg focus:ring-rose-500 focus:border-rose-500 transition-all"
                                    onkeyup="autoFormatNumber(this)">
                            </div>
                        </td>
                    `;
                    container.appendChild(row);
                });
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = `<tr><td colspan="2" class="px-4 py-8 text-center text-red-500 font-medium italic">Gagal memuat data: ${err.message}</td></tr>`;
            });
    }

    function formatNumber(n) {
        return n.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function autoFormatNumber(input) {
        let selectionStart = input.selectionStart;
        let originalLength = input.value.length;
        
        let value = input.value.replace(/[^0-9]/g, '');
        if (value === "") {
            input.value = "";
        } else {
            input.value = formatNumber(value);
        }
        
        // Adjust cursor position
        let newLength = input.value.length;
        selectionStart = selectionStart + (newLength - originalLength);
        input.setSelectionRange(selectionStart, selectionStart);
    }
</script>

<!-- Modal Uang Muat -->
<div id="modal-uang-muat" class="fixed inset-0 z-[9999] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm shadow-2xl" onclick="toggleModal('modal-uang-muat')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button type="button" onclick="toggleModal('modal-uang-muat')" class="text-gray-400 hover:text-gray-500 focus:outline-none transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form id="form-uang-muat" method="POST">
                @csrf
                <div class="bg-white px-6 pt-6 pb-6 w-full">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 bg-indigo-100 p-3 rounded-xl mr-4">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Kelola Uang Muat</h3>
                            <p class="text-sm text-gray-500">Input nominal uang muat per surat jalan dalam pranota ini.</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                        <div class="max-h-[50vh] overflow-y-auto custom-scrollbar">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 sticky top-0 z-10">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold text-gray-700 border-b">No Surat Jalan</th>
                                        <th class="px-4 py-3 font-semibold text-gray-700 border-b">Uang Muat</th>
                                    </tr>
                                </thead>
                                <tbody id="sj-list-container" class="divide-y divide-gray-50 bg-white">
                                    <!-- Dynamic rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-amber-50 border border-amber-100 rounded-xl">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs text-amber-800 leading-relaxed font-medium">
                                    Nominal uang muat yang ditambahkan tidak akan dikenakan pemotongan PPH 2% pada total tagihan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3">
                    <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg shadow-indigo-200 transition-all duration-200 transform hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-100 focus:outline-none">
                        Simpan Uang Muat
                        <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    <button type="button" onclick="toggleModal('modal-uang-muat')" class="inline-flex justify-center items-center px-6 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl shadow-sm transition-all duration-200 focus:outline-none">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f9fafb; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
@endsection
