@extends('layouts.app')

@section('title', 'Edit Pranota Uang Rit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">💰 Edit Pranota Uang Rit</h1>
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-1 text-sm text-gray-500">
                        <li><a href="{{ route('pranota-uang-rit.index') }}" class="hover:text-gray-700">Pranota Uang Rit</a></li>
                        <li class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <a href="{{ route('pranota-uang-rit.show', $pranotaUangRit) }}" class="ml-1 hover:text-gray-700">{{ $pranotaUangRit->no_pranota }}</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-gray-900 font-medium">Edit</span>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('pranota-uang-rit.show', $pranotaUangRit) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-eye mr-2"></i> Lihat Detail
                </a>
                <a href="{{ route('pranota-uang-rit.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <!-- Form -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Edit Pranota Uang Rit</h3>
                            <span class="px-3 py-1 text-sm rounded-full {{ $pranotaUangRit->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($pranotaUangRit->status) }}
                            </span>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('pranota-uang-rit.update', $pranotaUangRit) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="no_pranota" class="block text-sm font-medium text-gray-700 mb-2">No. Pranota</label>
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" value="{{ $pranotaUangRit->no_pranota }}" readonly>
                                        <p class="mt-1 text-sm text-gray-500">Nomor otomatis, tidak dapat diubah</p>
                                    </div>
                                    <div>
                                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                                            Tanggal <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal') border-red-500 @enderror" 
                                               id="tanggal" name="tanggal" value="{{ old('tanggal', $pranotaUangRit->tanggal->format('Y-m-d')) }}" required>
                                        @error('tanggal')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-6 mt-6">
                                    <div>
                                        <label for="surat_jalan_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Surat Jalan (Opsional)</label>
                                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_jalan_id') border-red-500 @enderror" 
                                                id="surat_jalan_id" name="surat_jalan_id">
                                            <option value="">- Pilih Surat Jalan atau Input Manual -</option>
                                            @foreach($suratJalans as $sj)
                                            <option value="{{ $sj->id }}" 
                                                    data-no-surat-jalan="{{ $sj->no_surat_jalan }}"
                                                    data-supir-nama="{{ $sj->supir_nama }}"
                                                    data-no-plat="{{ $sj->no_plat }}"
                                                    data-uang-jalan="{{ $sj->uang_jalan }}"
                                                    {{ old('surat_jalan_id', $pranotaUangRit->surat_jalan_id) == $sj->id ? 'selected' : '' }}>
                                                {{ $sj->no_surat_jalan }} - {{ $sj->supir_nama }} ({{ $sj->no_plat }})
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('surat_jalan_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <hr class="my-6 border-gray-200">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="no_surat_jalan" class="block text-sm font-medium text-gray-700 mb-2">
                                            No. Surat Jalan <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('no_surat_jalan') border-red-500 @enderror" 
                                               id="no_surat_jalan" name="no_surat_jalan" value="{{ old('no_surat_jalan', $pranotaUangRit->no_surat_jalan) }}" required>
                                        @error('no_surat_jalan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="supir_nama" class="block text-sm font-medium text-gray-700 mb-2">
                                            Nama Supir <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('supir_nama') border-red-500 @enderror" 
                                               id="supir_nama" name="supir_nama" value="{{ old('supir_nama', $pranotaUangRit->supir_nama) }}" required>
                                        @error('supir_nama')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-6 mt-6">
                                    <div>
                                        <label for="no_plat" class="block text-sm font-medium text-gray-700 mb-2">
                                            No. Plat <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('no_plat') border-red-500 @enderror" 
                                               id="no_plat" name="no_plat" value="{{ old('no_plat', $pranotaUangRit->no_plat) }}" required>
                                        @error('no_plat')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <hr class="my-6 border-gray-200">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="uang_jalan" class="block text-sm font-medium text-gray-700 mb-2">
                                            Uang Jalan <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                                            <input type="number" 
                                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('uang_jalan') border-red-500 @enderror" 
                                                   id="uang_jalan" name="uang_jalan" value="{{ old('uang_jalan', $pranotaUangRit->uang_jalan) }}" 
                                                   min="0" step="1000" required>
                                        </div>
                                        @error('uang_jalan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="uang_rit" class="block text-sm font-medium text-gray-700 mb-2">
                                            Uang Rit <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                                            <input type="number" 
                                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('uang_rit') border-red-500 @enderror" 
                                                   id="uang_rit" name="uang_rit" value="{{ old('uang_rit', $pranotaUangRit->uang_rit) }}" 
                                                   min="0" step="1000" required>
                                        </div>
                                        @error('uang_rit')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-500 @enderror" 
                                              id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $pranotaUangRit->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mt-8">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <i class="fas fa-save mr-2"></i> Update Pranota Uang Rit
                                    </button>
                                    <a href="{{ route('pranota-uang-rit.show', $pranotaUangRit) }}" class="btn btn-info ml-2">
                                        <i class="fas fa-eye"></i> Lihat Detail
                                    </a>
                                    <a href="{{ route('pranota-uang-rit.index') }}" class="ml-3 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <i class="fas fa-times mr-2"></i> Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <!-- Summary Card -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Ringkasan</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center mb-6">
                                <div class="flex-1">
                                    <div class="text-xs font-semibold text-blue-600 uppercase mb-1">
                                        Uang Jalan
                                    </div>
                                    <div class="text-xl font-bold text-gray-800" id="summary-uang-jalan">
                                        Rp {{ number_format($pranotaUangRit->uang_jalan, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-road text-2xl text-gray-300"></i>
                                </div>
                            </div>

                            <div class="flex items-center mb-6">
                                <div class="flex-1">
                                    <div class="text-xs font-semibold text-green-600 uppercase mb-1">
                                        Uang Rit
                                    </div>
                                    <div class="text-xl font-bold text-gray-800" id="summary-uang-rit">
                                        Rp {{ number_format($pranotaUangRit->uang_rit, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-truck text-2xl text-gray-300"></i>
                                </div>
                            </div>

                            <hr class="border-gray-200 mb-6">

                            <div class="flex items-center">
                                <div class="flex-1">
                                    <div class="text-xs font-semibold text-blue-600 uppercase mb-1">
                                        Total Uang
                                    </div>
                                    <div class="text-2xl font-bold text-blue-600" id="summary-total">
                                        Rp {{ number_format($pranotaUangRit->total_uang, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-money-bill-wave text-2xl text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Card -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 mt-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-blue-600">ℹ️ Status Informasi</h3>
                        </div>
                        <div class="p-6">
                            <div class="mb-4">
                                <strong class="text-gray-700">Status Saat Ini:</strong><br>
                                <span class="inline-flex px-3 py-1 text-sm rounded-full {{ $pranotaUangRit->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($pranotaUangRit->status) }}
                                </span>
                            </div>
                            <div class="mb-4">
                                <strong class="text-gray-700">Dibuat:</strong><br>
                                <span class="text-gray-600">
                                    {{ $pranotaUangRit->created_at->format('d/m/Y H:i') }}
                                    @if($pranotaUangRit->creator)
                                        oleh {{ $pranotaUangRit->creator->name }}
                                    @endif
                                </span>
                            </div>
                            @if($pranotaUangRit->updated_at != $pranotaUangRit->created_at)
                            <div class="mb-0">
                                <strong class="text-gray-700">Terakhir Update:</strong><br>
                                <span class="text-gray-600">{{ $pranotaUangRit->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 mt-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-yellow-600">⚠️ Perhatian</h3>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-500 mr-2 mt-1 flex-shrink-0"></i>
                                    <span class="text-gray-600">Hanya pranota dengan status Draft atau Submitted yang dapat diedit</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-500 mr-2 mt-1 flex-shrink-0"></i>
                                    <span class="text-gray-600">Perubahan akan mempertahankan status saat ini</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-500 mr-2 mt-1 flex-shrink-0"></i>
                                    <span class="text-gray-600">Pastikan data sudah benar sebelum menyimpan</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const suratJalanSelect = document.getElementById('surat_jalan_id');
    const noSuratJalanInput = document.getElementById('no_surat_jalan');
    const supirNamaInput = document.getElementById('supir_nama');
    const noPlatInput = document.getElementById('no_plat');
    const uangJalanInput = document.getElementById('uang_jalan');
    const uangRitInput = document.getElementById('uang_rit');

    // Auto-fill when surat jalan is selected
    suratJalanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            noSuratJalanInput.value = selectedOption.dataset.noSuratJalan || '';
            supirNamaInput.value = selectedOption.dataset.supirNama || '';
            noPlatInput.value = selectedOption.dataset.noPlat || '';
            uangJalanInput.value = selectedOption.dataset.uangJalan || 0;
            
            // Update summary
            updateSummary();
        }
    });

    // Update summary when amounts change
    uangJalanInput.addEventListener('input', updateSummary);
    uangRitInput.addEventListener('input', updateSummary);

    function updateSummary() {
        const uangJalan = parseInt(uangJalanInput.value) || 0;
        const uangRit = parseInt(uangRitInput.value) || 0;
        const total = uangJalan + uangRit;

        document.getElementById('summary-uang-jalan').textContent = 'Rp ' + numberFormat(uangJalan);
        document.getElementById('summary-uang-rit').textContent = 'Rp ' + numberFormat(uangRit);
        document.getElementById('summary-total').textContent = 'Rp ' + numberFormat(total);
    }

    function numberFormat(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Initial summary update
    updateSummary();
});
</script>
@endpush