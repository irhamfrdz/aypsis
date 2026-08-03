@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="flex items-center space-x-3 text-sm text-gray-500 mb-2">
                <a href="{{ route('stowage-plan.index') }}" class="hover:text-purple-600 transition-colors">Stowage Plan</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="font-medium text-gray-700">{{ $shipName }} (Voyage: {{ $voyage ?? '-' }})</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Kapal: {{ $shipName }}</h1>
            <p class="text-gray-500 mt-1">Voyage: <span class="font-semibold text-purple-700">{{ $voyage ?? '-' }}</span></p>
        </div>
        <div>
            <a href="{{ route('stowage-plan.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-gray-600 hover:bg-gray-50 transition-colors font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar: Unassigned Containers -->
        <div class="w-full lg:w-1/3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[700px]">
                <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700"><i class="fas fa-box text-orange-500 mr-2"></i> Belum Dialokasikan</h3>
                    <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-1 rounded-full">{{ $manifestsWithoutPlan->count() }}</span>
                </div>
                <div class="p-4 overflow-y-auto flex-1 space-y-3 bg-gray-50/50">
                    @forelse($manifestsWithoutPlan as $manifest)
                        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm hover:border-purple-300 hover:shadow transition-all cursor-pointer group">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-bold text-sm text-gray-800">{{ $manifest->nomor_kontainer ?? 'N/A' }}</span>
                                <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $manifest->tipe_kontainer ?? '-' }} / {{ $manifest->size_kontainer ?? '-' }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mb-1"><i class="fas fa-file-invoice text-gray-400 mr-1"></i> {{ $manifest->nomor_manifest ?? $manifest->nomor_bl }}</div>
                            <div class="text-xs text-gray-500 truncate"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> {{ $manifest->pelabuhan_asal }} <i class="fas fa-arrow-right mx-1 text-gray-300"></i> {{ $manifest->pelabuhan_tujuan }}</div>
                            
                            <div class="mt-3 pt-3 border-t border-gray-100 flex gap-2">
                                @if(count($stowageBays) > 0)
                                    <select class="w-full text-xs border-gray-200 rounded p-1 bay-input">
                                        <option value="">Bay</option>
                                        @foreach($stowageBays as $b)
                                            <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" placeholder="Bay" class="w-full text-xs border-gray-200 rounded p-1 bay-input" />
                                @endif

                                @if(count($stowageRows) > 0)
                                    <select class="w-full text-xs border-gray-200 rounded p-1 row-input">
                                        <option value="">Row</option>
                                        @foreach($stowageRows as $r)
                                            <option value="{{ str_pad($r, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($r, 2, '0', STR_PAD_LEFT) }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" placeholder="Row" class="w-full text-xs border-gray-200 rounded p-1 row-input" />
                                @endif

                                @if(count($stowageTiers) > 0)
                                    <select class="w-full text-xs border-gray-200 rounded p-1 tier-input">
                                        <option value="">Tier</option>
                                        @foreach($stowageTiers as $t)
                                            <option value="{{ str_pad($t, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($t, 2, '0', STR_PAD_LEFT) }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" placeholder="Tier" class="w-full text-xs border-gray-200 rounded p-1 tier-input" />
                                @endif

                                <button onclick="saveStowagePlan(this, '{{ $manifest->id }}')" class="bg-purple-100 text-purple-700 hover:bg-purple-600 hover:text-white rounded px-2 transition-colors">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-check-circle text-3xl mb-2 text-green-400"></i>
                            <p class="text-sm">Semua kontainer sudah dialokasikan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main Area: Stowage Grid Visual -->
        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[700px]">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <h3 class="font-bold text-gray-700"><i class="fas fa-ship text-purple-500 mr-2"></i> Peta Kapal</h3>
                        <div class="bg-gray-100 p-1 rounded-lg flex text-xs">
                            <button id="btn-topdown" onclick="toggleView('topdown')" class="px-3 py-1 bg-white shadow-sm rounded-md font-medium text-purple-700 transition-colors">Top Down</button>
                            <button id="btn-deckplan" onclick="toggleView('deckplan')" class="px-3 py-1 text-gray-500 hover:text-gray-700 font-medium transition-colors">Deck Plan</button>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded font-medium">Terisi: {{ $plans->count() }}</span>
                        <span class="text-xs px-2 py-1 bg-orange-100 text-orange-700 rounded font-medium">Kosong: {{ $manifestsWithoutPlan->count() }}</span>
                    </div>
                </div>
                
                <!-- TOP DOWN VIEW -->
                <div id="view-topdown" class="flex-1 bg-[#87CEEB] p-6 overflow-y-auto relative flex justify-center items-center" style="background-image: radial-gradient(#ffffff33 1px, transparent 1px); background-size: 20px 20px;">
                    
                    <!-- Vessel Shape -->
                    <div class="relative w-[340px] min-h-[550px] bg-[#2C3E50] border-4 border-[#1A252F] rounded-t-[170px] rounded-b-3xl shadow-2xl flex flex-col py-12 px-6">
                        
                        <!-- Bow (Front) Indicator -->
                        <div class="absolute top-4 left-1/2 transform -translate-x-1/2 text-white/50 text-xs font-bold tracking-widest">BOW</div>
                        
                        <!-- Grid of Bays (Generic layout) -->
                        <div class="flex-1 w-full flex flex-col gap-2 mt-8 z-10 relative">
                            @php
                                // Group allocated plans by bay
                                $bayPlans = $plans->groupBy('bay');
                            @endphp

                            @foreach($stowageBays as $index => $bayNum)
                                @php
                                    $hasContainers = isset($bayPlans[$bayNum]);
                                    $count = $hasContainers ? $bayPlans[$bayNum]->count() : 0;
                                    
                                    // Make front and back bays narrower to fit ship curve
                                    $widthClass = 'w-full';
                                    if($index === 0 || $index === count($stowageBays) - 1) $widthClass = 'w-3/5 mx-auto';
                                    elseif($index === 1 || $index === count($stowageBays) - 2) $widthClass = 'w-4/5 mx-auto';
                                @endphp
                                <div class="{{ $widthClass }} h-8 border border-white/20 rounded flex items-center justify-center relative group transition-all {{ $hasContainers ? 'bg-orange-500 border-orange-400 shadow-[0_0_10px_rgba(249,115,22,0.5)]' : 'bg-[#34495E] hover:bg-[#3D566E]' }}">
                                    <span class="text-[10px] font-bold {{ $hasContainers ? 'text-white' : 'text-white/40 group-hover:text-white/80' }}">BAY {{ str_pad($bayNum, 2, '0', STR_PAD_LEFT) }}</span>
                                    
                                    @if($hasContainers)
                                        <div class="absolute -right-8 bg-white text-orange-600 text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-md">
                                            {{ $count }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Bridge / Accommodation Structure (Rear) -->
                        <div class="w-[110%] -ml-[5%] h-24 bg-white mt-6 rounded-lg shadow-lg border-2 border-gray-300 flex flex-col justify-center items-center relative z-20">
                            <div class="w-4/5 h-8 bg-blue-100 border border-blue-200 rounded flex gap-1 px-2 items-center justify-center mb-2">
                                <div class="w-1/4 h-4 bg-blue-300 rounded-sm"></div>
                                <div class="w-2/4 h-4 bg-blue-300 rounded-sm"></div>
                                <div class="w-1/4 h-4 bg-blue-300 rounded-sm"></div>
                            </div>
                            <span class="text-[10px] font-bold text-gray-500 tracking-wider">BRIDGE</span>
                        </div>

                        <!-- Stern Indicator -->
                        <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 text-white/50 text-[10px] font-bold tracking-widest">STERN</div>
                    </div>
                </div>

                <!-- DECK PLAN VIEW -->
                <div id="view-deckplan" class="hidden flex-1 bg-gray-50 flex flex-col">
                    <div class="p-4 border-b border-gray-200 bg-white flex justify-between items-center">
                        <h4 class="font-bold text-gray-600 text-sm">Denah Deck (Bays x Rows)</h4>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 font-semibold">Pilih Tier:</span>
                            <select id="deck-plan-tier" onchange="renderDeckPlan()" class="border-gray-200 rounded p-1 text-sm bg-gray-50 font-bold text-purple-700 focus:ring-purple-500">
                                @foreach($stowageTiers as $t)
                                    <option value="{{ str_pad($t, 2, '0', STR_PAD_LEFT) }}">TIER {{ str_pad($t, 2, '0', STR_PAD_LEFT) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="deck-plan-grid" class="flex-1 overflow-auto p-6 flex flex-col items-center justify-start" style="background-image: radial-gradient(#00000011 1px, transparent 1px); background-size: 20px 20px;">
                        <!-- Rendered via JS -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function saveStowagePlan(button, manifestId) {
        const container = button.closest('.group');
        const bay = container.querySelector('.bay-input').value;
        const row = container.querySelector('.row-input').value;
        const tier = container.querySelector('.tier-input').value;

        if(!bay || !row || !tier) {
            alert('Mohon isi Bay, Row, dan Tier dengan lengkap!');
            return;
        }

        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        fetch('/api/stowage-plans', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                manifest_id: manifestId,
                bay: bay,
                row: row,
                tier: tier
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Flash success & reload to update map
                window.location.reload();
            } else {
                alert('Gagal menyimpan stowage plan');
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan sistem');
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.disabled = false;
        });
    }

    @php
        $mappedPlans = $plans->map(function($p) { 
            return [
                'bay' => str_pad($p->bay, 2, '0', STR_PAD_LEFT),
                'row' => str_pad($p->row, 2, '0', STR_PAD_LEFT),
                'tier' => str_pad($p->tier, 2, '0', STR_PAD_LEFT),
                'container' => $p->manifest->nomor_kontainer ?? 'UNKNOWN',
                'type' => $p->manifest->tipe_kontainer ?? '-'
            ];
        })->values();
    @endphp
    const plansData = @json($mappedPlans);
    const availableBays = Object.values(@json($stowageBays));
    const availableRows = Object.values(@json($stowageRows));
    const availableTiers = Object.values(@json($stowageTiers));

    function toggleView(view) {
        if(view === 'topdown') {
            document.getElementById('view-topdown').classList.remove('hidden');
            document.getElementById('view-deckplan').classList.add('hidden');
            
            document.getElementById('btn-topdown').className = 'px-3 py-1 bg-white shadow-sm rounded-md font-medium text-purple-700 transition-colors';
            document.getElementById('btn-deckplan').className = 'px-3 py-1 text-gray-500 hover:text-gray-700 font-medium transition-colors';
        } else {
            document.getElementById('view-topdown').classList.add('hidden');
            document.getElementById('view-deckplan').classList.remove('hidden');
            
            document.getElementById('btn-deckplan').className = 'px-3 py-1 bg-white shadow-sm rounded-md font-medium text-purple-700 transition-colors';
            document.getElementById('btn-topdown').className = 'px-3 py-1 text-gray-500 hover:text-gray-700 font-medium transition-colors';
            
            renderDeckPlan();
        }
    }

    function renderDeckPlan() {
        const tier = document.getElementById('deck-plan-tier').value;
        const grid = document.getElementById('deck-plan-grid');
        
        if(availableBays.length === 0 || availableRows.length === 0) {
            grid.innerHTML = '<div class="text-gray-400 text-center w-full mt-20 p-6 bg-white rounded-lg shadow-sm max-w-sm"><i class="fas fa-exclamation-triangle text-3xl mb-3 text-yellow-400"></i><p>Konfigurasi Bay dan Row di Master Kapal belum diset.</p></div>';
            return;
        }

        let html = '<div class="inline-flex flex-col gap-1 pb-10">';
        
        availableRows.forEach(row => {
            html += '<div class="flex gap-1 items-center">';
            // Y-axis label
            html += `<div class="text-[10px] text-gray-500 font-bold w-6 text-right pr-1">${row}</div>`;
            
            availableBays.forEach(bay => {
                const container = plansData.find(p => p.bay === bay && p.row === row && p.tier === tier);
                
                if(container) {
                    html += `
                        <div class="w-14 h-14 bg-orange-500 border border-orange-600 rounded flex flex-col items-center justify-center shadow-inner relative group cursor-pointer hover:bg-orange-400 transition-colors" title="No: ${container.container} | Tipe: ${container.type}">
                            <i class="fas fa-box text-white/30 text-xl absolute"></i>
                            <span class="text-[8px] text-white font-bold truncate w-12 text-center relative z-10 leading-tight">${container.container.substring(0,4)}<br/>${container.container.substring(4)}</span>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="w-14 h-14 bg-white/50 border border-gray-300 border-dashed rounded flex flex-col items-center justify-center hover:bg-white transition-colors">
                            <span class="text-[9px] text-gray-300 opacity-0 group-hover:opacity-100">+</span>
                        </div>
                    `;
                }
            });
            html += '</div>';
        });
        
        // X-axis labels
        html += '<div class="flex gap-1 items-center mt-1 border-t-2 border-gray-400 pt-1">';
        html += `<div class="w-6"></div>`; // empty space for Y-axis column
        availableBays.forEach(bay => {
            html += `<div class="w-14 text-center text-[10px] text-gray-600 font-bold">BAY ${bay}</div>`;
        });
        html += '</div>'; // close x-axis
        
        html += '</div>'; // close main flex
        grid.innerHTML = html;
    }
</script>
@endpush
@endsection
