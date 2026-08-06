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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col min-h-[700px]">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <h3 class="font-bold text-gray-700"><i class="fas fa-ship text-purple-500 mr-2"></i> Peta Kapal</h3>
                        <!-- Toggles removed -->
                    </div>
                    <div class="flex gap-2">
                        <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded font-medium">Terisi: {{ $plans->count() }}</span>
                        <span class="text-xs px-2 py-1 bg-orange-100 text-orange-700 rounded font-medium">Kosong: {{ $manifestsWithoutPlan->count() }}</span>
                    </div>
                </div>
                
                <!-- DECK PLAN VIEW -->
                <div id="view-deckplan" class="flex-1 bg-gray-50 flex flex-col">
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
                    <div id="deck-plan-grid" class="overflow-auto p-6" style="background-image: radial-gradient(#00000011 1px, transparent 1px); background-size: 20px 20px;">
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
    const disabledSlots = @json($disabledSlots);

    document.addEventListener('DOMContentLoaded', function() {
        renderDeckPlan();
    });

    function renderDeckPlan() {
        const tier = document.getElementById('deck-plan-tier').value;
        const grid = document.getElementById('deck-plan-grid');
        
        if(availableBays.length === 0 || availableRows.length === 0) {
            grid.innerHTML = '<div class="text-gray-400 text-center w-full mt-20 p-6 bg-white rounded-lg shadow-sm max-w-sm"><i class="fas fa-exclamation-triangle text-3xl mb-3 text-yellow-400"></i><p>Konfigurasi Bay dan Row di Master Kapal belum diset.</p></div>';
            return;
        }

        // Urutkan Row: Even descending, Odd ascending
        const sortedRows = [...availableRows].sort((a, b) => {
            let numA = parseInt(a);
            let numB = parseInt(b);
            let isAEven = numA % 2 === 0;
            let isBEven = numB % 2 === 0;

            if (isAEven && !isBEven) return -1;
            if (!isAEven && isBEven) return 1;
            
            if (isAEven) return numB - numA;
            else return numA - numB;
        });

        // Urutkan bay dari depan ke belakang (kecil ke besar)
        const sortedBays = [...availableBays].sort((a,b) => parseInt(a) - parseInt(b));

        let html = `
            <div class="w-full flex justify-center min-h-max pb-10">
                <div class="relative bg-slate-100 border-4 border-slate-400 shadow-xl flex-shrink-0" 
                     style="border-radius: 50% 50% 5% 5% / 150px 150px 10px 10px; padding: 120px 30px 50px 30px; min-width: 320px;">
                    
                    <!-- Bow (Depan) Indicator -->
                    <div class="absolute top-6 left-0 right-0 text-center text-slate-500 font-bold text-xs tracking-widest uppercase">
                        <i class="fas fa-caret-up block mb-1 text-lg text-slate-400"></i>
                        Bow (Depan)
                    </div>

                    <!-- Port / Starboard Indicators -->
                    <div class="absolute left-[-20px] top-1/2 transform -translate-y-1/2 -rotate-90 text-slate-400 font-bold text-[10px] tracking-widest whitespace-nowrap">
                        <span class="text-red-400 font-black">&larr;</span> PORT SIDE (KIRI)
                    </div>
                    <div class="absolute right-[-20px] top-1/2 transform translate-y-1/2 rotate-90 text-slate-400 font-bold text-[10px] tracking-widest whitespace-nowrap">
                        STARBOARD (KANAN) <span class="text-green-500 font-black">&rarr;</span>
                    </div>

                    <!-- Grid Table -->
                    <div class="bg-white rounded p-3 shadow-inner relative z-10">
                        <table class="w-full table-auto border-collapse mx-auto">
                            <thead>
                                <tr>
                                    <th class="p-2 border-b-2 border-r-2 border-gray-200 bg-gray-50 text-[10px] font-bold text-gray-500 uppercase tracking-wider w-12 shadow-sm">Bay \\ Row</th>
                                    ${sortedRows.map(row => `<th class="p-2 border-b-2 border-gray-200 bg-blue-50 text-xs font-bold text-blue-700 text-center w-14 shadow-sm">${row}</th>`).join('')}
                                </tr>
                            </thead>
                            <tbody>
                                ${sortedBays.map(bay => `
                                    <tr>
                                        <th class="p-2 border-r-2 border-b border-gray-200 bg-gray-50 text-xs font-bold text-gray-600 shadow-sm text-center">
                                            ${bay}
                                        </th>
                                        ${sortedRows.map(row => {
                                            const slotId = `${bay}${row}${tier}`;
                                            const isDisabled = disabledSlots.includes(slotId);
                                            const container = plansData.find(p => p.bay === bay && p.row === row && p.tier === tier);
                                            
                                            if (isDisabled) {
                                                return `
                                                <td class="p-1.5 border border-gray-100 text-center relative group transition-colors">
                                                    <div class="w-10 h-14 mx-auto border-2 border-dashed border-gray-300 rounded-sm bg-gray-50 flex flex-col items-center justify-center opacity-50 shadow-sm gap-1">
                                                        <span class="text-[8px] font-mono font-bold text-gray-400 line-through">${slotId}</span>
                                                    </div>
                                                </td>
                                                `;
                                            } else if (container) {
                                                return `
                                                <td class="p-1.5 border border-gray-100 text-center relative group">
                                                    <div class="w-10 h-14 mx-auto border-2 border-orange-500 rounded-sm bg-orange-500 flex flex-col items-center justify-center shadow-md relative overflow-hidden" title="No: ${container.container} | Tipe: ${container.type}">
                                                        <i class="fas fa-box text-white/30 text-xl absolute"></i>
                                                        <span class="text-[8px] font-mono font-bold text-white relative z-10 leading-tight">${container.container.substring(0,4)}<br/>${container.container.substring(4)}</span>
                                                    </div>
                                                </td>
                                                `;
                                            } else {
                                                return `
                                                <td class="p-1.5 border border-gray-100 text-center relative group transition-colors hover:bg-slate-50">
                                                    <div class="w-10 h-14 mx-auto border-2 border-slate-300 rounded-sm bg-white flex flex-col items-center justify-center group-hover:border-slate-500 group-hover:bg-slate-100 transition-all shadow-sm gap-1">
                                                        <div class="w-6 h-1.5 bg-slate-200 rounded-sm"></div>
                                                        <span class="text-[8px] font-mono font-bold text-slate-500 group-hover:text-slate-700">${slotId}</span>
                                                    </div>
                                                </td>
                                                `;
                                            }
                                        }).join('')}
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Stern (Belakang) Indicator -->
                    <div class="absolute bottom-6 left-0 right-0 text-center text-slate-500 font-bold text-xs tracking-widest uppercase">
                        Stern (Belakang)
                        <i class="fas fa-caret-down block mt-1 text-lg text-slate-400"></i>
                    </div>
                </div>
            </div>
        `;
        
        grid.innerHTML = html;
    }
</script>
@endpush
@endsection
