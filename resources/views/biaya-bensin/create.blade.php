@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f8fafc] py-8 px-4" style="background: radial-gradient(circle at top right, #fffdfa, #f8fafc);">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 animate-in fade-in slide-in-from-top-4 duration-700">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold uppercase tracking-wider mb-3 border border-amber-100">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    <span>Modul Operasional</span>
                </div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
                    Catat Biaya Bensin
                </h1>
                <p class="text-slate-500 mt-2 text-lg font-medium">Input data pengisian bahan bakar secara akurat</p>
            </div>
            <a href="{{ route('biaya-bensin.index') }}" 
               class="group inline-flex items-center px-5 py-2.5 bg-white text-slate-600 hover:text-slate-900 font-semibold rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Daftar
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white/80 backdrop-blur-xl rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-white/40 overflow-hidden animate-in zoom-in-95 duration-500">
            <div class="h-2 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600"></div>
            
            <form action="{{ route('biaya-bensin.store') }}" method="POST" class="p-8 md:p-12">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Left Column: Primary Info -->
                    <div class="space-y-8">
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-amber-500"></i> Informasi Utama
                            </h3>
                            
                            <div class="group">
                                <label for="tanggal" class="block text-sm font-bold text-slate-700 mb-2 transition-colors group-focus-within:text-amber-600">Tanggal Pengisian <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                        <i class="far fa-calendar-alt"></i>
                                    </div>
                                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                                           class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all duration-300 font-medium text-slate-900">
                                </div>
                                @error('tanggal') <p class="mt-2 text-sm text-red-500 font-medium animate-pulse">{{ $message }}</p> @enderror
                            </div>

                            <div class="group pt-2">
                                <label for="mobil_id" class="block text-sm font-bold text-slate-700 mb-2 transition-colors group-focus-within:text-amber-600">Kendaraan / Mobil <span class="text-red-500">*</span></label>
                                <div class="select2-wrapper">
                                    <select name="mobil_id" id="mobil_id" required class="select2 block w-full">
                                        <option value="">Cari Plat Nomor atau Tipe Mobil...</option>
                                        @foreach($mobils as $mobil)
                                            <option value="{{ $mobil->id }}" {{ old('mobil_id') == $mobil->id ? 'selected' : '' }}>
                                                {{ $mobil->nopol }} — {{ $mobil->merk }} {{ $mobil->tipe }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('mobil_id') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="group pt-2">
                                <label for="karyawan_id" class="block text-sm font-bold text-slate-700 mb-2 transition-colors group-focus-within:text-amber-600">Nama Supir <span class="text-red-500">*</span></label>
                                <div class="select2-wrapper">
                                    <select name="karyawan_id" id="karyawan_id" required class="select2 block w-full">
                                        <option value="">Pilih Supir...</option>
                                        @foreach($supirs as $supir)
                                            <option value="{{ $supir->id }}" {{ old('karyawan_id') == $supir->id ? 'selected' : '' }}>
                                                {{ $supir->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('karyawan_id') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Technical & Costs -->
                    <div class="space-y-8">
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                                <i class="fas fa-gas-pump mr-2 text-amber-500"></i> Detail Pengisian & Biaya
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-5">
                                <div class="group">
                                    <label for="km_awal" class="block text-sm font-bold text-slate-700 mb-2 transition-colors group-focus-within:text-amber-600">KM Awal</label>
                                    <input type="number" name="km_awal" id="km_awal" value="{{ old('km_awal') }}" placeholder="0"
                                           class="block w-full px-4 py-3.5 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all duration-300 font-medium text-slate-900">
                                </div>
                                <div class="group">
                                    <label for="km_akhir" class="block text-sm font-bold text-slate-700 mb-2 transition-colors group-focus-within:text-amber-600">KM Akhir</label>
                                    <input type="number" name="km_akhir" id="km_akhir" value="{{ old('km_akhir') }}" placeholder="0"
                                           class="block w-full px-4 py-3.5 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all duration-300 font-medium text-slate-900">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                                <div class="group">
                                    <label for="liter" class="block text-sm font-bold text-slate-700 mb-2 transition-colors group-focus-within:text-amber-600">Volume (Liter) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="liter" id="liter" value="{{ old('liter') }}" required placeholder="0.00"
                                               class="block w-full px-4 py-3.5 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all duration-300 font-bold text-slate-900 text-lg">
                                        <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none">
                                            <span class="text-slate-400 font-bold">L</span>
                                        </div>
                                    </div>
                                    @error('liter') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                                </div>
                                <div class="group">
                                    <label for="biaya" class="block text-sm font-bold text-slate-700 mb-2 transition-colors group-focus-within:text-amber-600">Total Biaya <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-amber-600 font-bold">Rp</span>
                                        </div>
                                        <input type="number" name="biaya" id="biaya" value="{{ old('biaya') }}" required placeholder="0"
                                               class="block w-full pl-12 pr-4 py-3.5 bg-amber-50/50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all duration-300 font-bold text-slate-900 text-lg">
                                    </div>
                                    @error('biaya') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="group pt-2">
                                <label for="keterangan" class="block text-sm font-bold text-slate-700 mb-2 transition-colors group-focus-within:text-amber-600">Keterangan Tambahan</label>
                                <textarea name="keterangan" id="keterangan" rows="4" placeholder="Contoh: Pengisian di SPBU 14.xxx, bensin penuh, dll..."
                                          class="block w-full px-4 py-3.5 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all duration-300 font-medium text-slate-900 resize-none">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center text-slate-400 text-sm italic">
                        <i class="fas fa-shield-alt mr-2"></i> Data akan diverifikasi oleh sistem audit
                    </div>
                    <button type="submit" 
                            class="w-full sm:w-auto group relative px-10 py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl shadow-xl shadow-slate-200 hover:shadow-slate-300 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 flex items-center justify-center overflow-hidden">
                        <span class="relative z-10 flex items-center">
                            <i class="fas fa-save mr-3 group-hover:scale-110 transition-transform"></i>
                            Simpan Data Pengisian
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-amber-400/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Footer Info -->
        <p class="text-center text-slate-400 text-sm mt-8">
            &copy; {{ date('Y') }} — Sistem Manajemen Armada & Logistik
        </p>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Scrollbar for better look */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Select2 Premium Styling */
    .select2-container--default .select2-selection--single {
        height: 54px !important;
        background-color: #f8fafc !important;
        border: 2px solid transparent !important;
        border-radius: 1rem !important;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        background-color: #fff !important;
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #0f172a !important;
        font-weight: 500;
        padding-left: 16px !important;
        font-size: 0.95rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 52px !important;
        right: 12px !important;
    }
    .select2-dropdown {
        border: none !important;
        border-radius: 1rem !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden;
        padding: 4px;
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px);
    }
    .select2-results__option {
        padding: 10px 16px !important;
        border-radius: 0.75rem !important;
        margin-bottom: 2px;
        transition: all 0.2s;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #f59e0b !important;
    }
    .select2-search--dropdown .select2-search__field {
        border-radius: 0.75rem !important;
        padding: 8px 12px !important;
        border: 1px solid #e2e8f0 !important;
    }

    /* Animations */
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('.container').parent() // Ensures dropdown respects container styling
        });

        // Add focus effect on Select2 wrapper
        $('.select2').on('select2:open', function() {
            $(this).next().addClass('is-focused');
        }).on('select2:close', function() {
            $(this).next().removeClass('is-focused');
        });
    });
</script>
@endpush
@endsection

