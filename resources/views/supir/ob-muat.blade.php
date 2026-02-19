@extends('layouts.supir')

@section('title', 'OB Muat - Pilih Kapal & Voyage - AYPSIS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    <span class="font-bold">Terdapat kesalahan:</span>
                </div>
                <ul class="list-disc list-inside ml-6 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Header Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center border border-green-100">
                        <i class="fas fa-ship text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 tracking-tight">OB Muat (Step 1)</h2>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-0.5">Pilih kapal dan nomor voyage</p>
                    </div>
                </div>
                <a href="{{ route('supir.dashboard') }}" class="p-2 bg-gray-50 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <!-- Form Container -->
        <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-200 overflow-hidden">
            <div class="p-8">
                <form action="{{ url('supir/ob-muat/store') }}" method="POST" id="obMuatForm" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <!-- Dropdown Kapal -->
                        <div>
                            <label for="kapal" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                Kapal <span class="text-red-500 font-bold">*</span>
                            </label>
                            <div class="relative">
                                <select id="kapal" name="kapal" required 
                                        class="appearance-none w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 text-gray-900 font-bold text-sm transition-all"
                                        onchange="updateVoyageOptions(); updateKapalDetails();">
                                    <option value="">--Pilih Kapal--</option>
                                    @php $shipGroups = $naikKapals->groupBy('nama_kapal')->keys(); @endphp
                                    @foreach($shipGroups as $kapalName)
                                        <option value="{{ $kapalName }}" {{ $kapalName == ($selectedKapal ?? '') ? 'selected' : '' }}>{{ $kapalName }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown Voyage -->
                        <div>
                            <label for="voyage" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                No Voyage <span class="text-red-500 font-bold">*</span>
                            </label>
                            <div class="relative">
                                <select id="voyage" name="voyage" required 
                                        class="appearance-none w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 text-gray-900 font-bold text-sm transition-all disabled:opacity-50 disabled:bg-gray-100"
                                        disabled>
                                    <option value="">-PILIH KAPAL DAHULU-</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-5 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-4">
                        <button type="button" onclick="proceedToObMuat()" 
                                class="w-full py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:shadow-2xl hover:bg-indigo-700 transition-all duration-300 flex items-center justify-center disabled:opacity-50 disabled:shadow-none disabled:bg-gray-300 disabled:cursor-not-allowed uppercase tracking-widest text-xs"
                                id="proceedBtn" disabled>
                            <i class="fas fa-arrow-right mr-3"></i> Lanjutkan ke Index OB
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 p-6 border-t border-gray-100">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center border border-gray-200 mr-4 shrink-0">
                        <i class="fas fa-info-circle text-indigo-500 text-sm"></i>
                    </div>
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest leading-relaxed">
                        Pilih kapal dan nomor voyage yang sesuai dengan tugas pemuatan Anda. Data yang tampil berdasarkan jadwal keberangkatan kapal yang aktif di sistem.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Data voyage sudah diproses di controller supaya Blade tidak perlu mengeksekusi closures
    const voyageData = @json($voyageData ?? []);

    // Selected values passed from server (outside object literal)
    const initialSelectedKapal = @json($selectedKapal ?? '');
    const initialSelectedVoyage = @json($selectedVoyage ?? '');

    function updateVoyageOptions() {
        const kapalSelect = document.getElementById('kapal');
        const voyageSelect = document.getElementById('voyage');
        const proceedBtn = document.getElementById('proceedBtn');
        
        const selectedKapal = kapalSelect.value;
        
        // Reset voyage dropdown
        voyageSelect.innerHTML = '<option value="">-PILIH VOYAGE-</option>';
        voyageSelect.disabled = !selectedKapal;
        
        // Disable button
        proceedBtn.disabled = true;
        
        if (selectedKapal && voyageData && voyageData[selectedKapal]) {
            // Populate voyage options - hapus duplikat dan format dengan baik
            const uniqueVoyages = {};
            voyageData[selectedKapal].forEach(item => {
                if (!uniqueVoyages[item.voyage]) {
                    uniqueVoyages[item.voyage] = item;
                }
            });
            
            Object.values(uniqueVoyages).forEach(item => {
                const option = document.createElement('option');
                option.value = item.voyage;
                
                // Format text dengan info yang ada
                let displayText = item.voyage;
                const additionalInfo = [];
                
                if (item.tanggal_muat && item.tanggal_muat !== '-') {
                    additionalInfo.push(item.tanggal_muat);
                }
                if (item.pelabuhan_tujuan && item.pelabuhan_tujuan !== '-') {
                    additionalInfo.push(item.pelabuhan_tujuan);
                }
                
                if (additionalInfo.length > 0) {
                    displayText += ` (${additionalInfo.join(' - ')})`;
                }
                
                option.textContent = displayText;
                // mark selected if the server passed an initial selected voyage
                if (initialSelectedVoyage && item.voyage === initialSelectedVoyage) {
                    option.selected = true;
                }
                voyageSelect.appendChild(option);
            });
            
            // Enable voyage dropdown
            voyageSelect.disabled = false;
            // If a server-selected voyage exists, ensure the button state is updated
            if (initialSelectedVoyage) {
                updateKapalDetails();
            }
        } else if (selectedKapal) {
            // Add message for no voyages
            const option = document.createElement('option');
            option.value = '';
            option.textContent = '-TIDAK ADA VOYAGE-';
            option.disabled = true;
            voyageSelect.appendChild(option);
        }
    }

    function updateKapalDetails() {
        const kapalSelect = document.getElementById('kapal');
        const voyageSelect = document.getElementById('voyage');
        const proceedBtn = document.getElementById('proceedBtn');
        
        const selectedKapal = kapalSelect.value;
        const selectedVoyage = voyageSelect.value;
        
        if (selectedKapal && selectedVoyage && voyageData[selectedKapal]) {
            const voyageInfo = voyageData[selectedKapal].find(item => item.voyage === selectedVoyage);
            if (voyageInfo) {
                proceedBtn.disabled = false;
            } else {
                proceedBtn.disabled = true;
            }
        } else {
            proceedBtn.disabled = true;
        }
    }

    function proceedToObMuat() {
        const kapal = document.getElementById('kapal').value;
        const voyage = document.getElementById('voyage').value;
        
        if (kapal && voyage) {
            window.location.href = `/supir/ob-muat/index?kapal=${encodeURIComponent(kapal)}&voyage=${encodeURIComponent(voyage)}`;
        }
    }

    // Event listener untuk voyage selection
    const voyageSelectEl = document.getElementById('voyage');
    if (voyageSelectEl) {
        voyageSelectEl.addEventListener('change', updateKapalDetails);
    }

    // Preselect kapal and voyage if provided by the server
    document.addEventListener('DOMContentLoaded', function() {
        if (initialSelectedKapal) {
            const kapalSelect = document.getElementById('kapal');
            kapalSelect.value = initialSelectedKapal;
            updateVoyageOptions();
        }

        if (initialSelectedVoyage) {
            const voyageSelect = document.getElementById('voyage');
            if (voyageSelect.querySelector(`option[value="${initialSelectedVoyage}"]`)) {
                voyageSelect.value = initialSelectedVoyage;
                updateKapalDetails();
            }
        }
    });
</script>
@endpush
