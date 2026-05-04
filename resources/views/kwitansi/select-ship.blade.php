@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">
    <!-- Main Card Container -->
    <div class="w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl shadow-indigo-100/50 overflow-hidden border border-slate-100 transform transition-all duration-500 hover:shadow-indigo-200/60">
        
        <!-- Premium Header Section -->
        <div class="relative px-8 py-10 sm:px-12 sm:py-12 bg-indigo-600 overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-32 h-32 bg-indigo-400/20 rounded-full blur-2xl"></div>
            
            <div class="relative z-10 flex flex-col items-center text-center">
                <div class="inline-flex items-center justify-center p-4 bg-white/15 backdrop-blur-md rounded-2xl mb-6 shadow-xl border border-white/20">
                    <i class="fas fa-ship text-4xl text-white"></i>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight mb-2 uppercase">
                    Pilih Kapal & Voyage
                </h1>
                <div class="h-1 w-20 bg-indigo-300/50 rounded-full mb-4"></div>
                <p class="text-indigo-100 text-sm sm:text-base font-medium max-w-md">
                    Silahkan tentukan pelayaran untuk menyaring data manifest yang akan ditagih.
                </p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="px-8 py-10 sm:px-12 sm:py-12 bg-white">
            <form method="GET" action="{{ route('kwitansi.index') }}#manifest" id="selectShipForm" class="space-y-8">
                
                <!-- Ship Selection Group -->
                <div class="space-y-3">
                    <label for="nama_kapal" class="flex items-center text-xs font-black text-slate-400 uppercase tracking-widest ml-1">
                        <span class="w-8 h-px bg-slate-200 mr-2"></span>
                        Nama Kapal
                        <span class="text-indigo-500 ml-1 font-bold">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-600 text-slate-400">
                            <i class="fas fa-anchor text-lg"></i>
                        </div>
                        <select name="nama_kapal" id="nama_kapal" required 
                                class="w-full pl-12 pr-12 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-0 focus:border-indigo-500 focus:bg-white transition-all duration-300 appearance-none text-slate-700 font-bold text-lg hover:border-slate-200 cursor-pointer">
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($ships as $ship)
                                <option value="{{ $ship->nama_kapal }}">
                                    {{ $ship->nama_kapal }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-5 pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fas fa-chevron-down text-sm"></i>
                        </div>
                    </div>
                </div>

                <!-- Voyage Selection Group -->
                <div class="space-y-3">
                    <label for="no_voyage" class="flex items-center text-xs font-black text-slate-400 uppercase tracking-widest ml-1">
                        <span class="w-8 h-px bg-slate-200 mr-2"></span>
                        Nomor Voyage
                        <span class="text-indigo-500 ml-1 font-bold">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-600 text-slate-400">
                            <i class="fas fa-route text-lg"></i>
                        </div>
                        <select name="no_voyage" id="no_voyage" required disabled
                                class="w-full pl-12 pr-12 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-0 focus:border-indigo-500 focus:bg-white transition-all duration-300 appearance-none text-slate-700 font-bold text-lg hover:border-slate-200 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                            <option value="">-- Pilih Voyage --</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-5 pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fas fa-chevron-down text-sm"></i>
                        </div>
                    </div>
                    <div id="voyage-help-text" class="flex items-center ml-2 text-xs font-medium text-slate-400 animate-pulse">
                        <i class="fas fa-info-circle mr-2 text-indigo-400"></i>
                        <span>Pilih kapal untuk memuat data voyage</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit" 
                            class="flex-[2] group relative bg-indigo-600 text-white font-black px-8 py-5 rounded-2xl hover:bg-indigo-700 focus:outline-none transition-all duration-300 shadow-xl shadow-indigo-200 hover:shadow-indigo-300 flex items-center justify-center overflow-hidden">
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-white/0 via-white/10 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        <span class="relative flex items-center tracking-wider uppercase">
                            Lihat Manifest
                            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </button>
                    <a href="{{ url('/') }}" 
                       class="flex-1 bg-slate-100 text-slate-500 font-bold px-8 py-5 rounded-2xl hover:bg-slate-200 transition-all duration-300 text-center flex items-center justify-center uppercase tracking-wider text-sm">
                        <i class="fas fa-times mr-2 text-slate-400"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tips Section -->
    <div class="w-full max-w-2xl mt-8">
        <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-6 flex items-center backdrop-blur-sm shadow-sm transition-all hover:shadow-md">
            <div class="flex-shrink-0 w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm border border-indigo-50 mr-5">
                <i class="fas fa-lightbulb text-indigo-500 text-xl"></i>
            </div>
            <div>
                <h4 class="text-indigo-900 font-black text-xs uppercase tracking-widest mb-1">💡 Tips Penagihan</h4>
                <p class="text-indigo-600/80 text-sm leading-relaxed font-medium">
                    Filter ini membantu Anda memproses tagihan secara berkelompok berdasarkan pelayaran kapal, meminimalisir kesalahan data penagihan.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar for the page */
    ::-webkit-scrollbar {
        width: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    ::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }

    /* Animation for the card */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .w-full.max-w-2xl {
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shipSelect = document.getElementById('nama_kapal');
        const voyageSelect = document.getElementById('no_voyage');
        const helpText = document.getElementById('voyage-help-text');

        shipSelect.addEventListener('change', function() {
            const shipName = this.value;
            
            if (!shipName) {
                voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option>';
                voyageSelect.disabled = true;
                helpText.classList.remove('animate-pulse');
                helpText.innerHTML = '<i class="fas fa-info-circle mr-2 text-indigo-400"></i><span>Pilih kapal untuk memuat data voyage</span>';
                return;
            }

            // Loading state
            voyageSelect.innerHTML = '<option value="">Memuat data...</option>';
            voyageSelect.disabled = true;
            helpText.classList.add('animate-pulse');
            helpText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-indigo-500"></i><span>Sedang mengambil data voyage...</span>';

            // Fetch voyages via API
            fetch(`/api/kwitansi/voyages/${encodeURIComponent(shipName)}`)
                .then(response => response.json())
                .then(data => {
                    voyageSelect.innerHTML = '<option value="">-- Pilih Voyage --</option>';
                    
                    if (data.voyages && data.voyages.length > 0) {
                        data.voyages.forEach(v => {
                            const opt = document.createElement('option');
                            opt.value = v;
                            opt.textContent = v;
                            voyageSelect.appendChild(opt);
                        });
                        voyageSelect.disabled = false;
                        helpText.classList.remove('animate-pulse');
                        helpText.innerHTML = `<i class="fas fa-check-circle text-emerald-500 mr-2"></i><span class="text-emerald-600">${data.voyages.length} voyage ditemukan untuk kapal ini</span>`;
                    } else {
                        voyageSelect.innerHTML = '<option value="">Tidak ada voyage</option>';
                        helpText.classList.remove('animate-pulse');
                        helpText.innerHTML = '<i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i><span class="text-amber-600">Tidak ada data voyage untuk kapal ini</span>';
                    }
                })
                .catch(err => {
                    console.error('Error fetching voyages:', err);
                    voyageSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                    helpText.classList.remove('animate-pulse');
                    helpText.innerHTML = '<i class="fas fa-times-circle text-rose-500 mr-2"></i><span class="text-rose-600">Terjadi kesalahan saat memuat data</span>';
                });
        });
    });
</script>
@endpush
@endsection
