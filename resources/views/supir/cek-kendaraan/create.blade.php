@extends('layouts.supir')

@section('title', 'Cek Kendaraan - AYPSIS')

@section('page_title', 'Pengecekan Kendaraan')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-lg">
    <!-- Header Simple -->
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('supir.dashboard') }}" class="p-2 -ml-2 text-gray-600 hover:text-gray-900">
            <i class="fas fa-chevron-left text-lg"></i>
        </a>
        <h1 class="text-lg font-bold text-gray-900">Cek Kendaraan</h1>
        <div class="w-8"></div> <!-- Spacer for center alignment -->
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 text-sm flex items-center shadow-sm">
        <i class="fas fa-check-circle mr-3 text-lg"></i>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 text-sm shadow-sm">
        <p class="font-bold mb-2 flex items-center"><i class="fas fa-exclamation-circle mr-2"></i> Error:</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('supir.cek-kendaraan.store') }}" method="POST" enctype="multipart/form-data" id="cekForm">
        @csrf
        
        <!-- Step 1: Info Dasar (Card Style) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
            <h2 class="text-sm font-bold text-gray-900 mb-4 flex items-center uppercase tracking-wider">
                <i class="fas fa-info-circle text-indigo-500 mr-2"></i> Info Unit
            </h2>
            
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Unit Kendaraan</label>
                    <div class="relative">
                        <select name="mobil_id" class="w-full appearance-none rounded-xl border-gray-200 bg-gray-50 text-sm font-bold text-gray-800 py-3 pl-4 pr-10 focus:border-indigo-500 focus:ring-0 transition-all" required>
                            <option value="">-- Pilih Unit --</option>
                            @foreach($mobils as $mobil)
                                <option value="{{ $mobil->id }}" 
                                        {{ $defaultMobilId == $mobil->id ? 'selected' : '' }}
                                        data-stnk="{{ $mobil->pajak_stnk ? $mobil->pajak_stnk->format('Y-m-d') : '' }}"
                                        data-kir="{{ $mobil->pajak_kir ? $mobil->pajak_kir->format('Y-m-d') : '' }}">
                                    {{ $mobil->nomor_polisi }} ({{ $mobil->merek }})
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Tgl & Jam</label>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-200">
                            <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                            <input type="hidden" name="jam" value="{{ date('H:i') }}">
                            <div class="text-sm font-bold text-gray-800">{{ date('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ date('H:i') }} WIB</div>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Odometer</label>
                         <div class="relative">
                            <input type="number" name="odometer" placeholder="0" class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm font-bold text-gray-800 py-3 pl-3 pr-8 focus:border-indigo-500 focus:ring-0 text-right">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">KM</span>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs for compatibility but keep value if needed -->
                <input type="hidden" name="masa_berlaku_stnk" id="masa_berlaku_stnk">
                <input type="hidden" name="masa_berlaku_kir" id="masa_berlaku_kir">
            </div>
        </div>

        <!-- Step 2: Checklist with Tabs -->
        @php
            $items = [
                ['name' => 'kotak_p3k', 'label' => 'Kotak P3K', 'type' => 'status_kadaluarsa'],
                ['name' => 'racun_api', 'label' => 'APAR', 'type' => 'status_kadaluarsa'],
                ['name' => 'plat_no_depan', 'label' => 'Plat Depan', 'type' => 'status_ada'],
                ['name' => 'plat_no_belakang', 'label' => 'Plat Belakang', 'type' => 'status_ada'],
                ['name' => 'lampu_jauh_kanan', 'label' => 'L. Jauh Kanan', 'type' => 'status_fungsi'],
                ['name' => 'lampu_jauh_kiri', 'label' => 'L. Jauh Kiri', 'type' => 'status_fungsi'],
                ['name' => 'lampu_dekat_kanan', 'label' => 'L. Dekat Kanan', 'type' => 'status_fungsi'],
                ['name' => 'lampu_dekat_kiri', 'label' => 'L. Dekat Kiri', 'type' => 'status_fungsi'],
                ['name' => 'lampu_sein_depan_kanan', 'label' => 'Sein Dpn Kanan', 'type' => 'status_fungsi'],
                ['name' => 'lampu_sein_depan_kiri', 'label' => 'Sein Dpn Kiri', 'type' => 'status_fungsi'],
                ['name' => 'lampu_sein_belakang_kanan', 'label' => 'Sein Blk Kanan', 'type' => 'status_fungsi'],
                ['name' => 'lampu_sein_belakang_kiri', 'label' => 'Sein Blk Kiri', 'type' => 'status_fungsi'],
                ['name' => 'lampu_rem_kanan', 'label' => 'Rem Kanan', 'type' => 'status_fungsi'],
                ['name' => 'lampu_rem_kiri', 'label' => 'Rem Kiri', 'type' => 'status_fungsi'],
                ['name' => 'lampu_mundur_kanan', 'label' => 'Mundur Kanan', 'type' => 'status_fungsi'],
                ['name' => 'lampu_mundur_kiri', 'label' => 'Mundur Kiri', 'type' => 'status_fungsi'],
                ['name' => 'sabuk_pengaman_kanan', 'label' => 'Sabuk Kanan', 'type' => 'status_fungsi'],
                ['name' => 'sabuk_pengaman_kiri', 'label' => 'Sabuk Kiri', 'type' => 'status_fungsi'],
                ['name' => 'kamvas_rem_depan_kanan', 'label' => 'Kanvas Dpn Kanan', 'type' => 'status_fungsi'],
                ['name' => 'kamvas_rem_depan_kiri', 'label' => 'Kanvas Dpn Kiri', 'type' => 'status_fungsi'],
                ['name' => 'kamvas_rem_belakang_kanan', 'label' => 'Kanvas Blk Kanan', 'type' => 'status_fungsi'],
                ['name' => 'kamvas_rem_belakang_kiri', 'label' => 'Kanvas Blk Kiri', 'type' => 'status_fungsi'],
                ['name' => 'spion_kanan', 'label' => 'Spion Kanan', 'type' => 'status_fungsi'],
                ['name' => 'spion_kiri', 'label' => 'Spion Kiri', 'type' => 'status_fungsi'],
                ['name' => 'tekanan_ban_depan_kanan', 'label' => 'Ban Dpn Kanan', 'type' => 'status_fungsi'],
                ['name' => 'tekanan_ban_depan_kiri', 'label' => 'Ban Dpn Kiri', 'type' => 'status_fungsi'],
                ['name' => 'tekanan_ban_belakang_kanan', 'label' => 'Ban Blk Kanan', 'type' => 'status_fungsi'],
                ['name' => 'tekanan_ban_belakang_kiri', 'label' => 'Ban Blk Kiri', 'type' => 'status_fungsi'],
                ['name' => 'ganjelan_ban', 'label' => 'Ganjelan Ban', 'type' => 'status_ada'],
                ['name' => 'trakel_sabuk', 'label' => 'Trakel Sabuk', 'type' => 'status_ada'],
                ['name' => 'twist_lock_kontainer', 'label' => 'Twist Lock', 'type' => 'status_fungsi'],
                ['name' => 'landing_buntut', 'label' => 'Landing Buntut', 'type' => 'status_fungsi'],
                ['name' => 'patok_besi', 'label' => 'Patok Besi', 'type' => 'status_ada'],
                ['name' => 'tutup_tangki', 'label' => 'Tutup Tangki', 'type' => 'status_ada'],
                ['name' => 'lampu_no_plat', 'label' => 'Lampu Plat', 'type' => 'status_fungsi'],
                ['name' => 'lampu_bahaya', 'label' => 'Lampu Hazard', 'type' => 'status_fungsi'],
                ['name' => 'klakson', 'label' => 'Klakson', 'type' => 'status_fungsi'],
                ['name' => 'radio', 'label' => 'Radio', 'type' => 'status_fungsi'],
                ['name' => 'rem_tangan', 'label' => 'Rem Tangan', 'type' => 'status_fungsi'],
                ['name' => 'pedal_gas', 'label' => 'Pedal Gas', 'type' => 'status_fungsi'],
                ['name' => 'pedal_rem', 'label' => 'Pedal Rem', 'type' => 'status_fungsi'],
                ['name' => 'porseneling', 'label' => 'Porseneling', 'type' => 'status_fungsi'],
                ['name' => 'antena_radio', 'label' => 'Antena', 'type' => 'status_ada'],
                ['name' => 'speaker', 'label' => 'Speaker', 'type' => 'status_fungsi'],
                ['name' => 'spion_dalam', 'label' => 'Spion Dalam', 'type' => 'status_fungsi'],
                ['name' => 'dongkrak', 'label' => 'Dongkrak', 'type' => 'status_ada'],
                ['name' => 'tangkai_dongkrak', 'label' => 'Tangkai Dongkrak', 'type' => 'status_ada'],
                ['name' => 'kunci_roda', 'label' => 'Kunci Roda', 'type' => 'status_ada'],
                ['name' => 'dop_roda', 'label' => 'Dop Roda', 'type' => 'status_ada'],
                ['name' => 'wiper_depan', 'label' => 'Wiper Depan', 'type' => 'status_fungsi'],
                ['name' => 'oli_mesin', 'label' => 'Oli Mesin', 'type' => 'status_baik'],
                ['name' => 'air_radiator', 'label' => 'Air Radiator', 'type' => 'status_baik'],
                ['name' => 'minyak_rem', 'label' => 'Minyak Rem', 'type' => 'status_baik'],
                ['name' => 'air_wiper', 'label' => 'Air Wiper', 'type' => 'status_baik'],
                ['name' => 'kondisi_aki', 'label' => 'Kondisi Aki', 'type' => 'status_baik'],
                ['name' => 'pengukur_tekanan_ban', 'label' => 'Alat.Ukur Angin', 'type' => 'status_ada'],
                ['name' => 'segitiga_pengaman', 'label' => 'Segitiga Pengaman', 'type' => 'status_ada'],
                ['name' => 'jumlah_ban_serep', 'label' => 'Jml Ban Serep', 'type' => 'status_jumlah'],
            ];

            // Define Categories
            $categories = [
                'penting' => [
                    'label' => 'Penting',
                    'icon' => 'fas fa-exclamation-triangle',
                    'items' => ['oli_mesin', 'air_radiator', 'minyak_rem', 'kondisi_aki', 'tekanan_ban_depan_kanan', 'tekanan_ban_depan_kiri', 'tekanan_ban_belakang_kanan', 'tekanan_ban_belakang_kiri', 'rem_tangan', 'pedal_rem', 'klakson', 'wiper_depan', 'ganjelan_ban']
                ],
                'lampu' => [
                    'label' => 'Lampu',
                    'icon' => 'fas fa-lightbulb',
                    'items' => ['lampu_jauh_kanan', 'lampu_jauh_kiri', 'lampu_dekat_kanan', 'lampu_dekat_kiri', 'lampu_sein_depan_kanan', 'lampu_sein_depan_kiri', 'lampu_sein_belakang_kanan', 'lampu_sein_belakang_kiri', 'lampu_rem_kanan', 'lampu_rem_kiri', 'lampu_mundur_kanan', 'lampu_mundur_kiri', 'lampu_no_plat', 'lampu_bahaya']
                ],
                'kelengkapan' => [
                    'label' => 'Alat',
                    'icon' => 'fas fa-tools',
                    'items' => ['kotak_p3k', 'racun_api', 'segitiga_pengaman', 'dongkrak', 'tangkai_dongkrak', 'kunci_roda', 'pengukur_tekanan_ban', 'jumlah_ban_serep', 'patok_besi', 'trakel_sabuk']
                ],
                'body' => [
                    'label' => 'Body',
                    'icon' => 'fas fa-car',
                    'items' => ['plat_no_depan', 'plat_no_belakang', 'spion_kanan', 'spion_kiri', 'spion_dalam', 'tutup_tangki', 'dop_roda', 'landing_buntut', 'twist_lock_kontainer']
                ],
                'lainnya' => [
                    'label' => 'Lainnya',
                    'icon' => 'fas fa-list',
                    'items' => ['air_wiper', 'sabuk_pengaman_kanan', 'sabuk_pengaman_kiri', 'kamvas_rem_depan_kanan', 'kamvas_rem_depan_kiri', 'kamvas_rem_belakang_kanan', 'kamvas_rem_belakang_kiri', 'radio', 'speaker', 'antena_radio', 'porseneling', 'pedal_gas']
                ]
            ];

            // Map flat items to key for easy lookup
            $itemMap = [];
            foreach($items as $item) {
                $itemMap[$item['name']] = $item;
            }
        @endphp

        <!-- Tab Navigation (Scrollable) -->
        <div class="flex overflow-x-auto gap-2 pb-4 pt-1 mb-2 scrollbar-hide snap-x" id="categoryTabs">
            @php $i = 0; @endphp
            @foreach($categories as $key => $cat)
                <button type="button" 
                    onclick="showTab('{{ $key }}')"
                    class="tab-btn snap-start shrink-0 px-4 py-2.5 rounded-xl border flex items-center transition-all shadow-sm {{ $i === 0 ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}"
                    data-target="{{ $key }}">
                    <i class="{{ $cat['icon'] }} mr-2 text-xs"></i>
                    <span class="text-sm font-bold">{{ $cat['label'] }}</span>
                </button>
                @php $i++; @endphp
            @endforeach
        </div>

        <!-- Content Sections -->
        @php $i = 0; @endphp
        @foreach($categories as $key => $cat)
            <div id="{{ $key }}-section" class="tab-content {{ $i === 0 ? '' : 'hidden' }}">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-700">{{ $cat['label'] }}</h3>
                        <button type="button" onclick="checkAllGood('{{ $key }}-section')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100">
                            <i class="fas fa-check-double mr-1"></i> Semua OK
                        </button>
                    </div>

                    <div class="divide-y divide-gray-50">
                        @foreach($cat['items'] as $itemName)
                             @php 
                                $item = $itemMap[$itemName] ?? null; 
                                if(!$item) continue;
                             @endphp
                             
                             <div class="p-3 sm:p-4 flex items-center justify-between gap-3 hover:bg-gray-50 transition-colors">
                                <label class="text-xs sm:text-sm font-medium text-gray-700 flex-1">{{ $item['label'] }}</label>
                                
                                <div class="flex items-center">
                                    @if($item['type'] == 'status_jumlah')
                                        <div class="flex items-center border border-gray-200 rounded-lg bg-gray-50 overflow-hidden w-24">
                                            <input type="number" name="{{ $item['name'] }}" value="1" min="0" class="w-full text-center bg-transparent border-0 p-1 text-sm focus:ring-0 font-bold">
                                        </div>
                                    @else
                                        <!-- Compact Toggle Switches -->
                                        <div class="bg-gray-100 p-1 rounded-lg flex items-center">
                                            @php
                                                $valOk = 'ada'; $valBad = 'tidak_ada'; $labelOk = 'Ada'; $labelBad = 'Tdk';
                                                
                                                if($item['type'] == 'status_fungsi') { $valOk = 'berfungsi'; $valBad = 'tidak_berfungsi'; $labelOk = 'OK'; $labelBad = 'Rusak'; }
                                                if($item['type'] == 'status_baik') { $valOk = 'baik'; $valBad = 'tidak_baik'; $labelOk = 'Baik'; $labelBad = 'Buruk'; }
                                                if($item['type'] == 'status_kadaluarsa') { $valOk = 'tidak_kadaluarsa'; $valBad = 'kadaluarsa'; $labelOk = 'OK'; $labelBad = 'Exp'; } // Reversed logic for safety
                                            @endphp

                                            <label class="cursor-pointer">
                                                <input type="radio" name="{{ $item['name'] }}" value="{{ $valOk }}" class="hidden peer status-ok" checked>
                                                <div class="px-3 py-1.5 rounded-md text-[10px] font-bold text-gray-500 peer-checked:bg-white peer-checked:text-green-600 peer-checked:shadow-sm transition-all whitespace-nowrap">
                                                    {{ $labelOk }}
                                                </div>
                                            </label>
                                            <label class="cursor-pointer">
                                                <input type="radio" name="{{ $item['name'] }}" value="{{ $valBad }}" class="hidden peer">
                                                <div class="px-3 py-1.5 rounded-md text-[10px] font-bold text-gray-500 peer-checked:bg-white peer-checked:text-red-500 peer-checked:shadow-sm transition-all whitespace-nowrap">
                                                    {{ $labelBad }}
                                                </div>
                                            </label>
                                        </div>
                                    @endif
                                </div>
                             </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @php $i++; @endphp
        @endforeach

        <!-- Declaration & Notes -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mt-6">
            <h3 class="font-bold text-gray-900 mb-4 uppercase tracking-wider text-xs">Kesimpulan</h3>
            
            <div class="space-y-3 mb-6">
                <label class="block p-3 border border-indigo-100 bg-indigo-50/50 rounded-xl cursor-pointer has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-300 transition-all">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="pernyataan" value="layak" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" checked>
                        <div>
                            <span class="block text-sm font-bold text-indigo-900">Layak Jalan</span>
                        </div>
                    </div>
                </label>
                
                <label class="block p-3 border border-gray-200 rounded-xl cursor-pointer has-[:checked]:bg-red-50 has-[:checked]:border-red-300 transition-all">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="pernyataan" value="tidak_layak" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300">
                        <div>
                            <span class="block text-sm font-bold text-gray-900">Tidak Layak Jalan</span>
                        </div>
                    </div>
                </label>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase mb-2 block">Catatan Tambahan</label>
                <textarea name="catatan" rows="3" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-0 text-sm p-3 transition-all" placeholder="Ada kerusakan lain? Catat disini..."></textarea>
            </div>
        </div>

        <!-- Sticky Bottom Action -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 p-4 pb-6 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-20">
            <div class="container mx-auto max-w-lg flex gap-3">
                <a href="{{ route('supir.dashboard') }}" class="flex-1 py-3.5 bg-gray-100 text-gray-500 font-bold rounded-xl text-center text-sm">
                    Batal
                </a>
                <button type="submit" class="flex-[2] py-3.5 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 active:scale-95 transition-all text-sm">
                    Kirim Laporan
                </button>
            </div>
        </div>
        <!-- Spacer for sticky bottom -->
        <div class="h-24"></div> 
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Tab Switching Logic
    function showTab(targetId) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
        });
        
        // Show target content
        document.getElementById(targetId + '-section').classList.remove('hidden');
        
        // Update button states
        document.querySelectorAll('.tab-btn').forEach(btn => {
            if(btn.dataset.target === targetId) {
                btn.className = 'tab-btn snap-start shrink-0 px-4 py-2.5 rounded-xl border flex items-center transition-all shadow-sm bg-indigo-600 text-white border-indigo-600';
            } else {
                btn.className = 'tab-btn snap-start shrink-0 px-4 py-2.5 rounded-xl border flex items-center transition-all shadow-sm bg-white text-gray-500 border-gray-200 hover:bg-gray-50';
            }
        });

        // Scroll tab into view if needed
        const activeTab = document.querySelector(`[data-target="${targetId}"]`);
        if(activeTab) {
            activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    // "Check All Good" Feature
    function checkAllGood(sectionId) {
        const section = document.getElementById(sectionId);
        const radioGroups = section.querySelectorAll('input[type="radio"].status-ok');
        
        radioGroups.forEach(radio => {
            radio.checked = true;
        });

        // Optional: specific fields like quantities default to 1 or logic
        const numberInputs = section.querySelectorAll('input[type="number"]');
        numberInputs.forEach(input => {
            if(input.value == 0 || input.value == '') input.value = 1; 
        });
    }

    // Existing STNK/KIR Logic
    document.querySelector('select[name="mobil_id"]').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        const stnkDate = selectedOption.getAttribute('data-stnk');
        if (stnkDate) document.getElementById('masa_berlaku_stnk').value = stnkDate;

        const kirDate = selectedOption.getAttribute('data-kir');
        if (kirDate) document.getElementById('masa_berlaku_kir').value = kirDate;
    });

    // Initial Trigger
    window.addEventListener('DOMContentLoaded', () => {
        const select = document.querySelector('select[name="mobil_id"]');
        if (select && select.value) {
            select.dispatchEvent(new Event('change'));
        }
    });
</script>
<style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>
@endpush
