@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-edit mr-2 text-amber-600"></i>
                Edit Catatan Biaya Bensin
            </h1>
            <p class="text-gray-600 mt-1">Perbarui informasi pengisian bensin kendaraan</p>
        </div>
        <a href="{{ route('biaya-bensin.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <form action="{{ route('biaya-bensin.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Pengisian <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $item->tanggal->format('Y-m-d')) }}" required
                               class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                        @error('tanggal') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="mobil_id" class="block text-sm font-semibold text-gray-700 mb-2">Kendaraan/Mobil <span class="text-red-500">*</span></label>
                        <select name="mobil_id" id="mobil_id" required class="select2 block w-full">
                            <option value="">Pilih Mobil</option>
                            @foreach($mobils as $mobil)
                                <option value="{{ $mobil->id }}" {{ old('mobil_id', $item->mobil_id) == $mobil->id ? 'selected' : '' }}>
                                    {{ $mobil->nomor_polisi ?: '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('mobil_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="karyawan_id" class="block text-sm font-semibold text-gray-700 mb-2">Supir <span class="text-red-500">*</span></label>
                        <select name="karyawan_id" id="karyawan_id" required class="select2 block w-full">
                            <option value="">Pilih Supir</option>
                            @foreach($supirs as $supir)
                                <option value="{{ $supir->id }}" data-mobil-id="{{ $mobils->where('karyawan_id', $supir->id)->first()?->id ?? '' }}" {{ old('karyawan_id', $item->karyawan_id) == $supir->id ? 'selected' : '' }}>
                                    {{ $supir->nama_panggilan ?: $supir->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="km_awal" class="block text-sm font-semibold text-gray-700 mb-2">KM Awal</label>
                            <input type="number" name="km_awal" id="km_awal" value="{{ old('km_awal', $item->km_awal) }}" placeholder="0"
                                   class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            @error('km_awal') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="km_akhir" class="block text-sm font-semibold text-gray-700 mb-2">KM Akhir</label>
                            <input type="number" name="km_akhir" id="km_akhir" value="{{ old('km_akhir', $item->km_akhir) }}" placeholder="0"
                                   class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            @error('km_akhir') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="liter" class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Liter <span class="text-red-500">*</span></label>
                            <div class="relative mt-1">
                                <input type="number" step="0.01" name="liter" id="liter" value="{{ old('liter', $item->liter) }}" required placeholder="0.00"
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm pr-12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">L</span>
                                </div>
                            </div>
                            @error('liter') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="biaya" class="block text-sm font-semibold text-gray-700 mb-2">Total Biaya (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="biaya" id="biaya" value="{{ old('biaya', $item->biaya) }}" required placeholder="0"
                                       class="block w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            </div>
                            @error('biaya') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."
                                  class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">{{ old('keterangan', $item->keterangan) }}</textarea>
                        @error('keterangan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="bukti_beli" class="block text-sm font-semibold text-gray-700 mb-2">Bukti Beli (Photo/PDF)</label>
                        @if($item->bukti_beli)
                            <div class="mb-3 flex items-center gap-2">
                                <a href="{{ asset('storage/' . $item->bukti_beli) }}" target="_blank" class="inline-flex items-center text-sm font-semibold text-amber-600 hover:text-amber-700">
                                    <i class="fas fa-file-alt mr-1.5"></i> Lihat Bukti Saat Ini
                                </a>
                            </div>
                        @endif
                        <input type="file" name="bukti_beli" id="bukti_beli" accept="image/*,application/pdf"
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none sm:text-sm">
                        @error('bukti_beli') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-10 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i> Perbarui Catatan
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border-color: #D1D5DB;
        border-radius: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });

        $('#karyawan_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const mobilId = selectedOption.data('mobil-id');
            if (mobilId) {
                $('#mobil_id').val(mobilId).trigger('change');
            }
        });
    });
</script>
@endpush
@endsection
