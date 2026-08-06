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
                    @forelse($manifestsWithoutPlan as $containerNo => $manifestGroup)
                        @php
                            $firstManifest = $manifestGroup->first();
                            $manifestIds = $manifestGroup->pluck('id')->join(',');
                            $isLcl = $manifestGroup->count() > 1;
                            $displayNo = str_starts_with($containerNo, 'UNASSIGNED_') ? 'N/A' : $containerNo;
                        @endphp
                        <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm hover:border-purple-300 hover:shadow transition-all cursor-move group" draggable="true" ondragstart="dragStowage(event, '{{ $manifestIds }}', '{{ $firstManifest->size_kontainer ?? '' }}')" ondragend="dragStowageEnd(event)">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-bold text-sm text-gray-800">{{ $displayNo }}</span>
                                <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $firstManifest->tipe_kontainer ?? '-' }} / {{ $firstManifest->size_kontainer ?? '-' }}</span>
                            </div>
                            @if($isLcl)
                                <div class="text-xs text-purple-600 font-semibold mb-1"><i class="fas fa-layer-group mr-1"></i> LCL ({{ $manifestGroup->count() }} Manifest)</div>
                            @else
                                <div class="text-xs text-gray-500 mb-1"><i class="fas fa-file-invoice text-gray-400 mr-1"></i> {{ $firstManifest->nomor_manifest ?? $firstManifest->nomor_bl }}</div>
                            @endif
                            <div class="text-xs text-gray-500 truncate"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> {{ $firstManifest->pelabuhan_asal }} <i class="fas fa-arrow-right mx-1 text-gray-300"></i> {{ $firstManifest->pelabuhan_tujuan }}</div>
                            
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

                                <button onclick="saveStowagePlan(this, '{{ $manifestIds }}', '{{ $firstManifest->size_kontainer ?? '' }}')" class="bg-purple-100 text-purple-700 hover:bg-purple-600 hover:text-white rounded px-2 transition-colors">
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
                        <div class="bg-gray-100 p-1 rounded-lg flex text-xs">
                            <button id="btn-deckplan" onclick="toggleView('deckplan')" class="px-3 py-1 bg-white shadow-sm rounded-md font-medium text-purple-700 transition-colors">Deck Plan</button>
                            <button id="btn-3d" onclick="toggleView('3d')" class="px-3 py-1 text-gray-500 hover:text-gray-700 font-medium transition-colors">3D View (Beta)</button>
                        </div>
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

                <!-- 3D VIEW -->
                <div id="view-3d" class="hidden w-full bg-slate-900 rounded-b-xl overflow-hidden relative shadow-inner" style="height: 700px;">
                    <div class="absolute top-4 left-4 z-10 bg-black/50 text-white px-3 py-1.5 rounded text-xs font-medium backdrop-blur-sm border border-white/10 pointer-events-none">
                        <i class="fas fa-mouse mr-2"></i> Drag untuk rotasi &bull; Scroll untuk zoom
                    </div>
                    <div id="threejs-container" class="w-full h-full cursor-move"></div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<script>
    function saveStowagePlan(button, manifestId, size) {
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
                manifest_ids: manifestId.toString().split(','),
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
                'type' => $p->manifest->tipe_kontainer ?? '-',
                'size' => $p->manifest->size_kontainer ?? '-'
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

    function toggleView(view) {
        if(view === 'deckplan') {
            document.getElementById('view-deckplan').classList.remove('hidden');
            document.getElementById('view-3d').classList.add('hidden');
            
            document.getElementById('btn-deckplan').className = 'px-3 py-1 bg-white shadow-sm rounded-md font-medium text-purple-700 transition-colors';
            document.getElementById('btn-3d').className = 'px-3 py-1 text-gray-500 hover:text-gray-700 font-medium transition-colors';
        } else {
            document.getElementById('view-deckplan').classList.add('hidden');
            document.getElementById('view-3d').classList.remove('hidden');
            
            document.getElementById('btn-3d').className = 'px-3 py-1 bg-white shadow-sm rounded-md font-medium text-purple-700 transition-colors';
            document.getElementById('btn-deckplan').className = 'px-3 py-1 text-gray-500 hover:text-gray-700 font-medium transition-colors';
            
            setTimeout(init3DPreview, 50);
        }
    }

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
                                            const exactContainer = plansData.find(p => p.bay === bay && p.row === row && p.tier === tier);
                                            
                                            // Check if this slot is the NEXT bay after a 40ft container
                                            const currentBayIdx = sortedBays.indexOf(bay);
                                            let blockedBy40ft = null;
                                            if (currentBayIdx > 0) {
                                                const prevBayInOrder = sortedBays[currentBayIdx - 1];
                                                blockedBy40ft = plansData.find(p => p.bay === prevBayInOrder && p.row === row && p.tier === tier && p.size && p.size.toString().includes('40'));
                                            }
                                            
                                            // Check if this exact container is 40ft (to render the top half)
                                            const is40ft = exactContainer && exactContainer.size && exactContainer.size.toString().includes('40');
                                            
                                            if (isDisabled) {
                                                return `
                                                <td class="p-1.5 border border-gray-100 text-center relative group transition-colors">
                                                    <div class="w-10 h-14 mx-auto border-2 border-dashed border-gray-300 rounded-sm bg-gray-50 flex flex-col items-center justify-center opacity-50 shadow-sm gap-1">
                                                        <span class="text-[8px] font-mono font-bold text-gray-400 line-through">${slotId}</span>
                                                    </div>
                                                </td>
                                                `;
                                            } else if (blockedBy40ft && !exactContainer) {
                                                // This slot is the bottom half of a 40ft from the previous bay
                                                return `
                                                <td class="p-0 border-x border-b border-gray-100 text-center relative group">
                                                    <div onclick="cancelStowage('${blockedBy40ft.bay}', '${row}', '${tier}')" class="w-10 mx-auto border-x-2 border-b-2 border-t-0 border-blue-500 rounded-b-sm bg-blue-500 hover:bg-red-500 hover:border-red-600 flex flex-col items-center justify-center shadow-md relative overflow-hidden cursor-pointer transition-colors" style="margin-top: -8px; height: calc(3.5rem + 8px);" title="40ft | Blokir oleh Bay ${blockedBy40ft.bay} | No: ${blockedBy40ft.container}">
                                                        <span class="text-[8px] font-mono font-bold text-white relative z-10 leading-tight group-hover:hidden text-center opacity-70">40ft</span>
                                                        <i class="fas fa-times text-white/50 text-2xl absolute hidden group-hover:block z-20"></i>
                                                    </div>
                                                </td>
                                                `;
                                            } else if (is40ft) {
                                                // This is the TOP half of a 40ft container - extends downward
                                                return `
                                                <td class="p-0 border-x border-t border-gray-100 text-center relative group">
                                                    <div onclick="cancelStowage('${bay}', '${row}', '${tier}')" class="w-10 mx-auto border-x-2 border-t-2 border-b-0 border-blue-500 rounded-t-sm bg-blue-500 hover:bg-red-500 hover:border-red-600 flex flex-col items-center justify-center shadow-md relative overflow-hidden cursor-pointer transition-colors z-10" style="margin-bottom: -8px; height: calc(3.5rem + 8px);" title="40ft | No: ${exactContainer.container} | Size: ${exactContainer.size}">
                                                        <i class="fas fa-box text-white/30 text-xl absolute group-hover:hidden"></i>
                                                        <i class="fas fa-times text-white/50 text-3xl absolute hidden group-hover:block z-20"></i>
                                                        <span class="text-[8px] font-mono font-bold text-white relative z-10 leading-tight group-hover:hidden text-center">${exactContainer.container.substring(0,4)}<br/>${exactContainer.container.substring(4)}</span>
                                                    </div>
                                                </td>
                                                `;
                                            } else if (exactContainer) {
                                                // Regular 20ft container
                                                return `
                                                <td class="p-1.5 border border-gray-100 text-center relative group">
                                                    <div onclick="cancelStowage('${bay}', '${row}', '${tier}')" class="w-10 h-14 mx-auto border-2 border-orange-500 rounded-sm bg-orange-500 hover:bg-red-500 hover:border-red-600 flex flex-col items-center justify-center shadow-md relative overflow-hidden cursor-pointer transition-colors" title="Klik untuk membatalkan | No: ${exactContainer.container} | Size: ${exactContainer.size}">
                                                        <i class="fas fa-box text-white/30 text-xl absolute group-hover:hidden"></i>
                                                        <i class="fas fa-times text-white/50 text-3xl absolute hidden group-hover:block"></i>
                                                        <span class="text-[8px] font-mono font-bold text-white relative z-10 leading-tight group-hover:hidden">${exactContainer.container.substring(0,4)}<br/>${exactContainer.container.substring(4)}</span>
                                                    </div>
                                                </td>
                                                `;
                                            } else {
                                                return `
                                                <td class="p-1.5 border border-gray-100 text-center relative group transition-colors hover:bg-slate-50"
                                                    ondragover="allowStowageDrop(event)"
                                                    ondragenter="allowStowageDrop(event)"
                                                    ondragleave="dragStowageLeave(event)"
                                                    ondrop="dropStowage(event, '${bay}', '${row}', '${tier}')">
                                                    <div class="w-10 h-14 mx-auto border-2 border-slate-300 rounded-sm bg-white flex flex-col items-center justify-center group-hover:border-slate-500 group-hover:bg-slate-100 transition-all shadow-sm gap-1">
                                                        <div class="w-6 h-1.5 bg-slate-200 rounded-sm pointer-events-none"></div>
                                                        <span class="text-[8px] font-mono font-bold text-slate-500 group-hover:text-slate-700 pointer-events-none">${slotId}</span>
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

    let threeRenderer;
    function init3DPreview() {
        if (typeof THREE === 'undefined') {
            document.getElementById('threejs-container').innerHTML = '<div class="flex items-center justify-center h-full text-white text-sm">Gagal memuat engine 3D (Pastikan koneksi internet aktif untuk CDN).</div>';
            return;
        }

        const container = document.getElementById('threejs-container');
        if (!container) return;
        
        if (threeRenderer) {
            container.innerHTML = '';
            threeRenderer.dispose();
        }

        const width = container.clientWidth;
        const height = container.clientHeight;

        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0x0f172a);

        const camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
        
        threeRenderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
        threeRenderer.setSize(width, height);
        threeRenderer.setPixelRatio(window.devicePixelRatio);
        container.appendChild(threeRenderer.domElement);

        const controls = new THREE.OrbitControls(camera, threeRenderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.autoRotate = true;
        controls.autoRotateSpeed = 1.0;

        const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
        scene.add(ambientLight);
        const dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
        dirLight.position.set(20, 40, 20);
        scene.add(dirLight);
        const dirLight2 = new THREE.DirectionalLight(0xffffff, 0.4);
        dirLight2.position.set(-20, -40, -20);
        scene.add(dirLight2);

        const shipGroup = new THREE.Group();
        scene.add(shipGroup);

        const bays = [...availableBays].sort((a,b) => parseInt(a) - parseInt(b));
        const tiers = [...availableTiers].sort((a,b) => parseInt(a) - parseInt(b));
        const rows = [...availableRows].sort((a, b) => {
            let numA = parseInt(a); let numB = parseInt(b);
            let isAEven = numA % 2 === 0; let isBEven = numB % 2 === 0;
            if (isAEven && !isBEven) return -1;
            if (!isAEven && isBEven) return 1;
            if (isAEven) return numB - numA; else return numA - numB;
        });

        const boxGeo = new THREE.BoxGeometry(0.9, 1.2, 2.5);
        const filledMat = new THREE.MeshPhongMaterial({ color: 0xf97316, shininess: 30 }); // orange-500
        const disabledMat = new THREE.MeshBasicMaterial({ color: 0xef4444, wireframe: true, transparent: true, opacity: 0.15 });
        const emptyMat = new THREE.MeshBasicMaterial({ color: 0x64748b, wireframe: true, transparent: true, opacity: 0.1 });
        const edgeMat = new THREE.LineBasicMaterial({ color: 0xc2410c, transparent: true, opacity: 0.5 }); // darker orange

        bays.forEach((bay, bIdx) => {
            rows.forEach((row, rIdx) => {
                tiers.forEach((tier, tIdx) => {
                    const slotId = `${bay}${row}${tier}`;
                    const isDisabled = disabledSlots.includes(slotId);
                    const containerData = plansData.find(p => p.bay === bay && p.row === row && p.tier === tier);
                    
                    // Check if previous bay in order has a 40ft that blocks this slot
                    let blockedBy40ft = false;
                    if (bIdx > 0) {
                        const prevBay = bays[bIdx - 1];
                        blockedBy40ft = plansData.find(p => p.bay === prevBay && p.row === row && p.tier === tier && p.size && p.size.toString().includes('40'));
                    }
                    const is40ft = containerData && containerData.size && containerData.size.toString().includes('40');
                    
                    let mesh;
                    if (isDisabled) {
                        mesh = new THREE.Mesh(boxGeo, disabledMat);
                    } else if (is40ft) {
                        // Render 40ft container spanning 2 bays
                        const box40Geo = new THREE.BoxGeometry(0.9, 1.2, 5.3);
                        const filled40Mat = new THREE.MeshPhongMaterial({ color: 0x3b82f6, shininess: 30 });
                        mesh = new THREE.Mesh(box40Geo, filled40Mat);
                        const edges = new THREE.EdgesGeometry(box40Geo);
                        const line = new THREE.LineSegments(edges, new THREE.LineBasicMaterial({ color: 0x1d4ed8, transparent: true, opacity: 0.5 }));
                        mesh.add(line);
                        
                        mesh.position.x = (rIdx - (rows.length - 1) / 2) * 1.1;
                        mesh.position.y = (tIdx) * 1.4;
                        mesh.position.z = (bIdx - (bays.length - 1) / 2) * 2.8 + 1.4;
                        
                        shipGroup.add(mesh);
                        return;
                    } else if (blockedBy40ft) {
                        return; // already rendered by previous bay's 40ft
                    } else if (containerData) {
                        mesh = new THREE.Mesh(boxGeo, filledMat);
                        const edges = new THREE.EdgesGeometry(boxGeo);
                        const line = new THREE.LineSegments(edges, edgeMat);
                        mesh.add(line);
                    } else {
                        mesh = new THREE.Mesh(boxGeo, emptyMat);
                    }
                    
                    if (mesh) {
                        mesh.position.x = (rIdx - (rows.length - 1) / 2) * 1.1;
                        mesh.position.y = (tIdx) * 1.4;
                        mesh.position.z = (bIdx - (bays.length - 1) / 2) * 2.8;
                        shipGroup.add(mesh);
                    }
                });
            });
        });

        if (bays.length > 0 && rows.length > 0) {
            const rowsCount = Math.max(rows.length, 1);
            const baysCount = Math.max(bays.length, 1);
            
            const w = (rowsCount * 1.1 + 1) / 2;
            const cl = (baysCount * 2.8) / 2;
            const bowL = 5;
            const sternL = 3;

            const boatShape = new THREE.Shape();
            boatShape.moveTo(-w, cl + sternL);
            boatShape.lineTo(w, cl + sternL);
            boatShape.lineTo(w, -cl + 1);
            boatShape.quadraticCurveTo(w, -cl - bowL/2, 0, -cl - bowL);
            boatShape.quadraticCurveTo(-w, -cl - bowL/2, -w, -cl + 1);
            boatShape.lineTo(-w, cl + sternL);

            const extrudeSettings = {
                depth: 3,
                bevelEnabled: true,
                bevelSegments: 2,
                steps: 1,
                bevelSize: 0.3,
                bevelThickness: 0.3
            };

            const hullGeo = new THREE.ExtrudeGeometry(boatShape, extrudeSettings);
            hullGeo.rotateX(Math.PI / 2); 
            const hullMat = new THREE.MeshPhongMaterial({ color: 0x991b1b }); // red-800
            const hull = new THREE.Mesh(hullGeo, hullMat);
            hull.position.y = -0.5;
            shipGroup.add(hull);

            const deckGeo = new THREE.ShapeGeometry(boatShape);
            deckGeo.rotateX(-Math.PI / 2);
            const deckMat = new THREE.MeshPhongMaterial({ color: 0x475569 }); // slate-600
            const deck = new THREE.Mesh(deckGeo, deckMat);
            deck.position.y = -0.48;
            shipGroup.add(deck);

            const bridgeW = w * 1.8;
            const bridgeL = 2.5;
            const bridgeH = 4;
            const bridgeGeo = new THREE.BoxGeometry(bridgeW, bridgeH, bridgeL);
            const bridgeMat = new THREE.MeshPhongMaterial({ color: 0xf1f5f9 }); // slate-100
            const bridge = new THREE.Mesh(bridgeGeo, bridgeMat);
            bridge.position.set(0, bridgeH/2 - 0.5, cl + sternL/2);
            shipGroup.add(bridge);
            
            const windowGeo = new THREE.BoxGeometry(bridgeW * 0.8, 1, bridgeL + 0.1);
            const windowMat = new THREE.MeshPhongMaterial({ color: 0x0f172a }); // dark glass
            const bridgeWindows = new THREE.Mesh(windowGeo, windowMat);
            bridgeWindows.position.set(0, bridgeH/2, cl + sternL/2);
            shipGroup.add(bridgeWindows);
            
            const funnelGeo = new THREE.CylinderGeometry(0.6, 0.8, 2.5, 16);
            const funnelMat = new THREE.MeshPhongMaterial({ color: 0xeab308 }); // yellow-500
            const funnel = new THREE.Mesh(funnelGeo, funnelMat);
            funnel.position.set(0, bridgeH, cl + sternL - 1);
            shipGroup.add(funnel);
            
            const funnelTopGeo = new THREE.CylinderGeometry(0.6, 0.6, 0.5, 16);
            const funnelTopMat = new THREE.MeshPhongMaterial({ color: 0x1e293b }); // slate-800
            const funnelTop = new THREE.Mesh(funnelTopGeo, funnelTopMat);
            funnelTop.position.set(0, bridgeH + 1.5, cl + sternL - 1);
            shipGroup.add(funnelTop);
        }

        const box = new THREE.Box3().setFromObject(shipGroup);
        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3());
        
        shipGroup.position.x = -center.x;
        shipGroup.position.y = -center.y;
        shipGroup.position.z = -center.z;

        const maxDim = Math.max(size.x, size.y, size.z) || 10;
        camera.position.set(maxDim * 1.2, maxDim * 1, maxDim * 1.2);
        camera.lookAt(0, 0, 0);

        const animate = function () {
            if (!document.getElementById('threejs-container')) return;
            window.current3DAnimationId = requestAnimationFrame(animate);
            controls.update();
            threeRenderer.render(scene, camera);
        };
        
        if (window.current3DAnimationId) cancelAnimationFrame(window.current3DAnimationId);
        animate();
        
        window.addEventListener('resize', () => {
            if (!document.getElementById('threejs-container')) return;
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            threeRenderer.setSize(container.clientWidth, container.clientHeight);
        }, false);
    }

    // --- Drag and Drop Handlers ---
    function dragStowage(ev, manifestId, size) {
        ev.dataTransfer.setData("manifestId", manifestId);
        ev.dataTransfer.setData("size", size || '');
        ev.currentTarget.classList.add('opacity-50');
    }

    function dragStowageEnd(ev) {
        ev.currentTarget.classList.remove('opacity-50');
    }

    function allowStowageDrop(ev) {
        ev.preventDefault();
        const cell = ev.currentTarget.querySelector('div');
        if (cell) {
            cell.classList.add('bg-purple-100', 'border-purple-400');
            cell.classList.remove('bg-white', 'border-slate-300');
        }
    }

    function dragStowageLeave(ev) {
        const cell = ev.currentTarget.querySelector('div');
        if (cell) {
            cell.classList.remove('bg-purple-100', 'border-purple-400');
            cell.classList.add('bg-white', 'border-slate-300');
        }
    }

    function dropStowage(ev, bay, row, tier) {
        ev.preventDefault();
        const cell = ev.currentTarget.querySelector('div');
        if (cell) {
            cell.classList.remove('bg-purple-100', 'border-purple-400');
            cell.classList.add('bg-white', 'border-slate-300');
        }
        
        const manifestId = ev.dataTransfer.getData("manifestId");
        const size = ev.dataTransfer.getData("size");

        if (manifestId) {
            ev.currentTarget.innerHTML = `<div class="w-10 h-14 mx-auto border-2 border-purple-500 rounded-sm bg-purple-50 flex flex-col items-center justify-center shadow-md relative overflow-hidden"><i class="fas fa-spinner fa-spin text-purple-500 text-xl"></i></div>`;

            
            fetch('/api/stowage-plans', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    manifest_ids: manifestId.toString().split(','),
                    bay: bay,
                    row: row,
                    tier: tier
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert('Gagal menyimpan stowage plan: ' + (data.message || 'Error'));
                    renderDeckPlan();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan sistem');
                renderDeckPlan();
            });
        }
    }

    function cancelStowage(bay, row, tier) {
        if (!confirm(`Batalkan alokasi kontainer di Bay ${bay}, Row ${row}, Tier ${tier}?`)) return;

        fetch('/api/stowage-plans/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                bay: bay,
                row: row,
                tier: tier,
                nama_kapal: '{{ $shipName }}',
                no_voyage: '{{ $voyage ?? '' }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                window.location.reload();
            } else {
                alert('Gagal membatalkan stowage plan: ' + (data.message || 'Error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan sistem');
        });
    }
</script>
@endpush
@endsection
