@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50/50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold uppercase tracking-wider mb-2.5 border border-amber-200/50">
                    <i class="fas fa-edit text-[10px]"></i> Operasional Kendaraan
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Edit Catatan Biaya Bensin</h1>
                <p class="text-slate-500 mt-1 text-sm">Perbarui informasi pengisian bahan bakar kendaraan di bawah ini.</p>
            </div>
            <a href="{{ route('biaya-bensin.index') }}" 
               class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-700 font-semibold hover:bg-slate-50 hover:text-slate-900 active:bg-slate-100 transition-all shadow-sm gap-2 text-sm">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        <!-- Main Form -->
        <form action="{{ route('biaya-bensin.update', $item->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left Column: Driver & Kendaraan Details -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                                <i class="fas fa-user-astronaut"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Driver & Kendaraan</h3>
                                <p class="text-xs text-slate-400">Ubah supir, unit kendaraan, atau kartu bensin</p>
                            </div>
                        </div>

                        <!-- Tanggal -->
                        <div>
                            <label for="tanggal" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Tanggal Pengisian <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $item->tanggal->format('Y-m-d')) }}" required
                                   class="block w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-slate-900 font-semibold text-sm">
                            @error('tanggal') <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Supir -->
                        <div>
                            <label for="karyawan_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Nama Supir <span class="text-red-500">*</span>
                            </label>
                            <select name="karyawan_id" id="karyawan_id" required class="select2 block w-full">
                                <option value="">Pilih Supir...</option>
                                @foreach($supirs as $supir)
                                    <option value="{{ $supir->id }}" data-mobil-id="{{ $mobils->where('karyawan_id', $supir->id)->first()?->id ?? '' }}" {{ old('karyawan_id', $item->karyawan_id) == $supir->id ? 'selected' : '' }}>
                                        {{ $supir->nama_panggilan ?: $supir->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            @error('karyawan_id') <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Mobil -->
                        <div>
                            <label for="mobil_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Kendaraan / Mobil <span class="text-red-500">*</span>
                            </label>
                            <select name="mobil_id" id="mobil_id" required class="select2 block w-full">
                                <option value="">Pilih Mobil...</option>
                                @foreach($mobils as $mobil)
                                    <option value="{{ $mobil->id }}" {{ old('mobil_id', $item->mobil_id) == $mobil->id ? 'selected' : '' }}>
                                        {{ $mobil->nomor_polisi ?: '-' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mobil_id') <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Kartu Bensin -->
                        <div>
                            <label for="nomor_kartu" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Nomor Kartu Bensin
                            </label>
                            <select name="nomor_kartu" id="nomor_kartu" class="select2 block w-full">
                                <option value="">Pilih Nomor Kartu...</option>
                                @foreach($kartus as $kartu)
                                    <option value="{{ $kartu->nomor_kartu }}" {{ old('nomor_kartu', $item->nomor_kartu) == $kartu->nomor_kartu ? 'selected' : '' }}>
                                        {{ $kartu->nomor_kartu }} - {{ $kartu->nama_kartu }} ({{ $kartu->provider }})
                                    </option>
                                @endforeach
                            </select>
                            @error('nomor_kartu') <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Column: Fill up details & calculations -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Detail Pengisian & Jarak</h3>
                                <p class="text-xs text-slate-400">Perbarui detail volume bensin, harga, dan KM kendaraan</p>
                            </div>
                        </div>

                        <!-- KM Group -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="km_awal" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    KM Awal
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs font-semibold">KM</span>
                                    <input type="number" name="km_awal" id="km_awal" value="{{ old('km_awal', $item->km_awal) }}" placeholder="0"
                                           class="block w-full pl-10 pr-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-slate-900 font-semibold text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="km_akhir" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    KM Akhir
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs font-semibold">KM</span>
                                    <input type="number" name="km_akhir" id="km_akhir" value="{{ old('km_akhir', $item->km_akhir) }}" placeholder="0"
                                           class="block w-full pl-10 pr-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-slate-900 font-semibold text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Calculations Widget -->
                        <div class="bg-amber-50/40 rounded-2xl border border-amber-100/60 p-4 sm:p-5 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Volume (Liter) -->
                                <div>
                                    <label for="liter" class="block text-xs font-bold text-amber-900 uppercase tracking-wider mb-2">
                                        Volume Liter <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="liter" id="liter" value="{{ old('liter', $item->liter) }}" required placeholder="0.00"
                                               class="block w-full pl-4 pr-10 py-3 bg-white border border-amber-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-bold text-slate-900 text-sm">
                                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 font-bold text-xs">Liters</span>
                                    </div>
                                </div>

                                <!-- Harga per Liter -->
                                <div>
                                    <label for="harga_per_liter" class="block text-xs font-bold text-amber-900 uppercase tracking-wider mb-2">
                                        Harga per Liter
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">Rp</span>
                                        <input type="number" id="harga_per_liter" value="{{ old('harga_per_liter', $item->harga_per_liter) }}" placeholder="0"
                                               class="block w-full pl-9 pr-4 py-3 bg-white border border-amber-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-bold text-slate-900 text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Total Biaya -->
                            <div class="pt-2">
                                <label for="biaya" class="block text-xs font-bold text-amber-900 uppercase tracking-wider mb-2">
                                    Total Biaya Bensin <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-amber-700 font-extrabold text-base">Rp</span>
                                    <input type="number" name="biaya" id="biaya" value="{{ old('biaya', $item->biaya) }}" required placeholder="0"
                                           class="block w-full pl-11 pr-4 py-3.5 bg-amber-500/10 border-2 border-amber-500/30 rounded-xl focus:bg-white focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-black text-slate-900 text-lg">
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label for="keterangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Keterangan
                            </label>
                            <textarea name="keterangan" id="keterangan" rows="3" placeholder="Tulis catatan tambahan di sini jika ada..."
                                      class="block w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-slate-900 text-sm resize-none">{{ old('keterangan', $item->keterangan) }}</textarea>
                        </div>

                        <!-- Bukti Beli -->
                        <div>
                            <label for="bukti_beli" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Lampirkan Bukti Pembelian <span class="text-slate-400 font-normal normal-case">(Foto / PDF)</span>
                            </label>
                            
                            @if($item->bukti_beli)
                                <div class="mb-3 flex items-center gap-2">
                                    <a href="{{ asset('storage/' . $item->bukti_beli) }}" target="_blank" class="inline-flex items-center text-xs font-semibold text-amber-600 hover:text-amber-700 gap-1.5 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200/50">
                                        <i class="fas fa-file-invoice"></i> Lihat Bukti Saat Ini
                                    </a>
                                </div>
                            @endif

                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-xl hover:border-amber-500/50 transition-all bg-slate-50/30">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-cloud-upload-alt text-slate-400 text-3xl mb-3"></i>
                                    <div class="flex text-sm text-slate-600 justify-center">
                                        <label for="bukti_beli" class="relative cursor-pointer bg-white rounded-md font-semibold text-amber-600 hover:text-amber-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-amber-500">
                                            <span>Pilih File</span>
                                            <input id="bukti_beli" name="bukti_beli" type="file" accept="image/*,application/pdf" class="sr-only">
                                        </label>
                                    </div>
                                    <p class="text-xs text-slate-500">PNG, JPG, PDF hingga 10MB</p>
                                    <p id="file-chosen" class="text-xs text-amber-600 font-semibold mt-2 hidden"></p>
                                </div>
                            </div>
                            @error('bukti_beli') <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Footer -->
            <div class="mt-8 bg-white border border-slate-100 rounded-2xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                <div class="text-xs text-slate-400 flex items-center gap-2">
                    <i class="fas fa-info-circle text-amber-500 text-sm"></i>
                    <span>Harap periksa kembali detail pengisian bensin sebelum memperbarui data.</span>
                </div>
                <button type="submit" 
                        class="w-full sm:w-auto px-8 py-3.5 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-850 active:bg-slate-950 transition-all shadow-md hover:shadow-lg transform active:scale-[0.98] flex items-center justify-center gap-2.5 text-sm">
                    <i class="fas fa-save"></i> Perbarui Catatan
                </button>
            </div>
        </form>
        
        <div class="text-center mt-8 text-slate-400 text-xs font-semibold">
            &copy; {{ date('Y') }} — Fleet Management System
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Premium Select2 Styling Overrides */
    .select2-container--default .select2-selection--single {
        height: 46px !important;
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }
    .select2-container--default .select2-selection--single:focus-within,
    .select2-container--default.select2-container--focus .select2-selection--single {
        background-color: #ffffff !important;
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 1rem !important;
        color: #0f172a !important;
        font-weight: 600;
        font-size: 0.875rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
        right: 8px !important;
    }
    .select2-dropdown {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
        overflow: hidden;
        z-index: 9999;
    }
    .select2-results__option {
        padding: 8px 12px !important;
        font-size: 0.875rem !important;
        font-weight: 500;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #f59e0b !important;
        color: white !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            width: '100%'
        });

        // File upload helper text trigger
        $('#bukti_beli').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if(fileName) {
                $('#file-chosen').text('File terpilih: ' + fileName).removeClass('hidden');
            } else {
                $('#file-chosen').addClass('hidden');
            }
        });

        // Autofill vehicle based on selected driver
        $('#karyawan_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const mobilId = selectedOption.data('mobil-id');
            if (mobilId) {
                $('#mobil_id').val(mobilId).trigger('change');
            }
        });

        // Dynamic Calculations
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
