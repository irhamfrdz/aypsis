@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="fas fa-gas-pump mr-2"></i> Operasional Kendaraan
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Catat Biaya Bensin</h1>
                <p class="text-gray-500 mt-1">Lengkapi formulir di bawah untuk mencatat pengisian bahan bakar</p>
            </div>
            <a href="{{ route('biaya-bensin.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-all shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="h-1.5 bg-amber-500"></div>
            
            <form action="{{ route('biaya-bensin.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-10">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div class="pb-2 border-b border-gray-100 mb-2">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-id-card mr-3 text-amber-500"></i> Informasi Utama
                            </h3>
                        </div>

                        <div>
                            <label for="tanggal" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Pengisian <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                                   class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-gray-900 font-medium">
                            @error('tanggal') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="mobil_id" class="block text-sm font-bold text-gray-700 mb-2">Kendaraan / Mobil <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="mobil_id" id="mobil_id" required class="select2 block w-full">
                                    <option value="">Pilih Mobil...</option>
                                    @foreach($mobils as $mobil)
                                        <option value="{{ $mobil->id }}" {{ old('mobil_id') == $mobil->id ? 'selected' : '' }}>
                                            {{ $mobil->nomor_polisi ?: '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('mobil_id') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="karyawan_id" class="block text-sm font-bold text-gray-700 mb-2">Nama Supir <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="karyawan_id" id="karyawan_id" required class="select2 block w-full">
                                    <option value="">Pilih Supir...</option>
                                    @foreach($supirs as $supir)
                                        <option value="{{ $supir->id }}" data-mobil-id="{{ $mobils->where('karyawan_id', $supir->id)->first()?->id ?? '' }}" {{ old('karyawan_id') == $supir->id ? 'selected' : '' }}>
                                            {{ $supir->nama_panggilan ?: $supir->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('karyawan_id') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="nomor_kartu" class="block text-sm font-bold text-gray-700 mb-2">Nomor Kartu</label>
                            <div class="relative">
                                <select name="nomor_kartu" id="nomor_kartu" class="select2 block w-full">
                                    <option value="">Pilih Nomor Kartu...</option>
                                    @foreach($kartus as $kartu)
                                        <option value="{{ $kartu->nomor_kartu }}" {{ old('nomor_kartu', $lastNomorKartu) == $kartu->nomor_kartu ? 'selected' : '' }}>
                                            {{ $kartu->nomor_kartu }} - {{ $kartu->nama_kartu }} ({{ $kartu->provider }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('nomor_kartu') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div class="pb-2 border-b border-gray-100 mb-2">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-tachometer-alt mr-3 text-amber-500"></i> Detail Operasional
                            </h3>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="km_awal" class="block text-sm font-bold text-gray-700 mb-2">KM Awal</label>
                                <input type="number" name="km_awal" id="km_awal" value="{{ old('km_awal') }}" placeholder="0"
                                       class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-gray-900">
                            </div>
                            <div>
                                <label for="km_akhir" class="block text-sm font-bold text-gray-700 mb-2">KM Akhir</label>
                                <input type="number" name="km_akhir" id="km_akhir" value="{{ old('km_akhir') }}" placeholder="0"
                                       class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-gray-900">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="liter" class="block text-sm font-bold text-gray-700 mb-2">Volume <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="liter" id="liter" value="{{ old('liter') }}" required placeholder="0.00"
                                           class="block w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-bold text-gray-900">
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 font-bold">L</div>
                                </div>
                            </div>
                            <div>
                                <label for="harga_per_liter" class="block text-sm font-bold text-gray-700 mb-2">Harga per Liter</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 font-bold">Rp</div>
                                    <input type="number" id="harga_per_liter" value="{{ $lastHargaPerLiter }}" placeholder="0"
                                           class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-bold text-gray-900">
                                </div>
                            </div>
                            <div>
                                <label for="biaya" class="block text-sm font-bold text-gray-700 mb-2">Total Biaya <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-amber-600 font-bold">Rp</div>
                                    <input type="number" name="biaya" id="biaya" value="{{ old('biaya') }}" required placeholder="0"
                                           class="block w-full pl-12 pr-4 py-3 bg-amber-50 border border-amber-100 rounded-xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-bold text-gray-900">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-2">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3" placeholder="Opsional..."
                                      class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-gray-900 resize-none">{{ old('keterangan') }}</textarea>
                        </div>

                        <div>
                            <label for="bukti_beli" class="block text-sm font-bold text-gray-700 mb-2">Lampirkan Bukti Beli (Photo/PDF)</label>
                            <input type="file" name="bukti_beli" id="bukti_beli" accept="image/*,application/pdf"
                                   class="block w-full text-sm text-gray-900 border border-gray-200 rounded-xl cursor-pointer bg-gray-50 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
                            @error('bukti_beli') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="text-sm text-gray-400 italic">
                        <i class="fas fa-info-circle mr-1"></i> Data yang disimpan tidak dapat diubah tanpa persetujuan admin.
                    </div>
                    <button type="submit" 
                            class="w-full md:w-auto px-10 py-4 bg-gray-900 text-white font-bold rounded-2xl hover:bg-black transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center">
                        <i class="fas fa-save mr-3"></i> Simpan Catatan Bensin
                    </button>
                </div>
            </form>
        </div>
        
        <div class="text-center mt-8 text-gray-400 text-xs">
            &copy; {{ date('Y') }} — Fleet Management System
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 52px !important;
        background-color: #f9fafb !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.75rem !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 1rem !important;
        color: #111827 !important;
        font-weight: 500;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 50px !important;
    }
    .select2-dropdown {
        border: 1px solid #e5e7eb !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #f59e0b !important;
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

        $('#liter').on('input', function() {
            const liter = parseFloat($(this).val()) || 0;
            const harga = parseFloat($('#harga_per_liter').val()) || 0;
            if (liter > 0 && harga > 0) {
                $('#biaya').val(Math.round(liter * harga));
            }
        });

        $('#harga_per_liter').on('input', function() {
            const harga = parseFloat($(this).val()) || 0;
            const liter = parseFloat($('#liter').val()) || 0;
            const biaya = parseFloat($('#biaya').val()) || 0;
            if (harga > 0) {
                if (document.activeElement.id === 'harga_per_liter') {
                    if (liter > 0) {
                        $('#biaya').val(Math.round(liter * harga));
                    } else if (biaya > 0) {
                        $('#liter').val(Math.round((biaya / harga) * 100) / 100);
                    }
                }
            }
        });

        $('#biaya').on('input', function() {
            const biaya = parseFloat($(this).val()) || 0;
            const harga = parseFloat($('#harga_per_liter').val()) || 0;
            const liter = parseFloat($('#liter').val()) || 0;
            if (biaya > 0) {
                if (harga > 0) {
                    $('#liter').val(Math.round((biaya / harga) * 100) / 100);
                } else if (liter > 0) {
                    $('#harga_per_liter').val(Math.round((biaya / liter) * 100) / 100);
                }
            }
        });
    });
</script>
@endpush
@endsection

