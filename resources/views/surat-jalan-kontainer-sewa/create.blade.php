@extends('layouts.app')

@section('title', 'Buat Surat Jalan ' . ($tipe === 'pengambilan' ? 'Pengambilan' : 'Pengembalian') . ' Kontainer Sewa')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('surat-jalan-kontainer-sewa.index') }}" class="hover:text-cyan-600 transition">SJ Kontainer Sewa</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 font-medium">Buat {{ $tipe === 'pengambilan' ? 'Pengambilan' : 'Pengembalian' }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-3">
            @if($tipe === 'pengambilan')
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-truck-loading text-emerald-600"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Buat Surat Jalan Pengambilan Kontainer Sewa</h1>
                    <p class="text-sm text-gray-500">Catat pengambilan kontainer dari vendor</p>
                </div>
            @else
                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-undo-alt text-orange-600"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Buat Surat Jalan Pengembalian Kontainer Sewa</h1>
                    <p class="text-sm text-gray-500">Catat pengembalian kontainer ke vendor</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4 text-sm">
            <ul class="list-disc ml-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('surat-jalan-kontainer-sewa.store') }}" id="form-create">
        @csrf
        <input type="hidden" name="tipe" value="{{ $tipe }}">

        {{-- Info Utama --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b"><i class="fas fa-info-circle text-cyan-600 mr-1"></i> Informasi Utama</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nomor Surat Jalan</label>
                    <input type="text" value="{{ $nomorSuratJalan }}" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 bg-gray-50" readonly>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Vendor</label>
                    <select name="vendor" id="vendor-select" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="">-- Pilih Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor }}" {{ old('vendor') == $vendor ? 'selected' : '' }}>{{ $vendor }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Supir</label>
                    <input type="text" name="supir" value="{{ old('supir') }}" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Nama supir">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">No. Plat</label>
                    <input type="text" name="no_plat" value="{{ old('no_plat') }}" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="B 1234 XYZ">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi {{ $tipe === 'pengambilan' ? 'Pengambilan' : 'Pengembalian' }}</label>
                    @if($tipe === 'pengambilan')
                        <input type="text" name="lokasi_pengambilan" value="{{ old('lokasi_pengambilan') }}" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Lokasi pengambilan">
                    @else
                        <input type="text" name="lokasi_pengembalian" value="{{ old('lokasi_pengembalian') }}" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Lokasi pengembalian">
                    @endif
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
            </div>
        </div>

        {{-- Pilih Kontainer --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b">
                <h2 class="text-sm font-semibold text-gray-700"><i class="fas fa-boxes text-cyan-600 mr-1"></i> Pilih Kontainer</h2>
                <button type="button" id="btn-add-row" class="px-3 py-1.5 bg-cyan-600 text-white text-xs rounded-md hover:bg-cyan-700 transition">
                    <i class="fas fa-plus mr-1"></i> Tambah Baris
                </button>
            </div>

            <div id="kontainer-rows" class="space-y-3">
                {{-- Template row will be cloned here via JS --}}
                <div class="kontainer-row flex flex-wrap items-start gap-3 p-3 bg-gray-50 rounded-md border border-gray-200">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nomor Kontainer <span class="text-red-500">*</span></label>
                        <select name="kontainer_ids[]" class="kontainer-select w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" required>
                            <option value="">-- Pilih --</option>
                            @foreach($kontainers as $k)
                                <option value="{{ $k->nomor_seri_gabungan }}" data-ukuran="{{ $k->ukuran }}" data-tipe="{{ $k->tipe_kontainer }}" data-vendor="{{ $k->vendor }}">
                                    {{ $k->nomor_seri_gabungan }} ({{ $k->vendor }} - {{ $k->ukuran }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-[120px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kondisi</label>
                        <select name="kondisi[]" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500">
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Catatan Kondisi</label>
                        <input type="text" name="catatan_kondisi[]" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Opsional...">
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="btn-remove-row px-2 py-2 text-red-500 hover:bg-red-50 rounded transition mt-5" title="Hapus baris" style="display:none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('surat-jalan-kontainer-sewa.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-cyan-600 text-white text-sm rounded-md hover:bg-cyan-700 transition">
                <i class="fas fa-save mr-1"></i> Simpan Surat Jalan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowsContainer = document.getElementById('kontainer-rows');
    const btnAdd = document.getElementById('btn-add-row');

    function updateRemoveButtons() {
        const rows = rowsContainer.querySelectorAll('.kontainer-row');
        rows.forEach((row, i) => {
            const btn = row.querySelector('.btn-remove-row');
            if (btn) btn.style.display = rows.length > 1 ? '' : 'none';
        });
    }

    btnAdd.addEventListener('click', function() {
        const firstRow = rowsContainer.querySelector('.kontainer-row');
        const newRow = firstRow.cloneNode(true);
        
        // Reset values
        newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        newRow.querySelectorAll('input').forEach(i => i.value = '');
        
        rowsContainer.appendChild(newRow);
        
        // Attach remove handler
        newRow.querySelector('.btn-remove-row').addEventListener('click', function() {
            newRow.remove();
            updateRemoveButtons();
        });

        updateRemoveButtons();
    });

    // Attach remove handlers to existing rows
    rowsContainer.querySelectorAll('.btn-remove-row').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.kontainer-row').remove();
            updateRemoveButtons();
        });
    });

    updateRemoveButtons();
});
</script>
@endpush
@endsection
