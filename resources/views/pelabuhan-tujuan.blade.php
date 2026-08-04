@extends('layouts.public')

@section('title', 'Informasi Pelabuhan Tujuan - ALEXINDO YAKINPRIMA')

{{-- Force the navbar to be in the "scrolled" (solid) state on this page since there is no hero image behind it --}}
@section('force_scrolled_nav', true)
@section('navbar_class', 'nav-scrolled')
@section('logo_class', 'text-slate-900')

@section('content')
<div class="pt-32 pb-24 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mb-6">Informasi Pelabuhan Tujuan</h1>
            <p class="text-slate-600 text-lg">
                PT Alexindo Yakinprima melayani pengiriman ke berbagai pelabuhan strategis di seluruh Indonesia. Berikut adalah daftar pelabuhan tujuan yang aktif kami layani.
            </p>
        </div>

        <!-- Search Box (UI Only) -->
        <div class="max-w-2xl mx-auto mb-12">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                </div>
                <input type="text" id="searchInput" class="block w-full pl-11 pr-4 py-4 border-0 rounded-2xl shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-blue-600 bg-white text-slate-900 transition-all text-lg" placeholder="Cari pelabuhan atau kota...">
            </div>
        </div>

        <!-- Grid of Ports -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="portGrid">
            @forelse($pelabuhans as $pelabuhan)
                <div class="port-card bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl font-bold">
                            {{ substr($pelabuhan->nama_pelabuhan, 0, 1) }}
                        </div>
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                            <i class="fa-solid fa-circle text-[8px]"></i> Aktif
                        </span>
                    </div>
                    
                    <h3 class="port-name text-xl font-bold text-slate-900 mb-1">{{ $pelabuhan->nama_pelabuhan }}</h3>
                    <div class="flex items-center gap-2 text-slate-500 mb-4 port-city">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>{{ $pelabuhan->kota ?? 'Indonesia' }}</span>
                    </div>
                    
                    @if($pelabuhan->keterangan)
                        <p class="text-slate-600 text-sm line-clamp-3">
                            {{ $pelabuhan->keterangan }}
                        </p>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-slate-200 border-dashed">
                    <i class="fa-solid fa-anchor-circle-exclamation text-4xl text-slate-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-slate-900">Belum ada data pelabuhan</h3>
                    <p class="text-slate-500 mt-1">Data pelabuhan tujuan belum tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
        
        <!-- Empty State for Search -->
        <div id="noResults" class="hidden text-center py-12 bg-white rounded-2xl border border-slate-200 border-dashed mt-6">
            <i class="fa-solid fa-magnifying-glass text-4xl text-slate-300 mb-4"></i>
            <h3 class="text-lg font-medium text-slate-900">Pelabuhan tidak ditemukan</h3>
            <p class="text-slate-500 mt-1">Coba gunakan kata kunci lain untuk mencari.</p>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const portCards = document.querySelectorAll('.port-card');
        const noResults = document.getElementById('noResults');
        const portGrid = document.getElementById('portGrid');
        
        if(searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase().trim();
                let hasResults = false;
                
                portCards.forEach(card => {
                    const name = card.querySelector('.port-name').textContent.toLowerCase();
                    const city = card.querySelector('.port-city').textContent.toLowerCase();
                    
                    if(name.includes(term) || city.includes(term)) {
                        card.style.display = 'block';
                        hasResults = true;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                if(!hasResults && term !== '') {
                    noResults.classList.remove('hidden');
                    if(portGrid) portGrid.classList.add('hidden');
                } else {
                    noResults.classList.add('hidden');
                    if(portGrid) portGrid.classList.remove('hidden');
                }
            });
        }
    });
</script>
@endsection
