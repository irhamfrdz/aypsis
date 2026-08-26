@extends('layouts.public')
@section('title', 'Full Container Load (FCL) | ALEXINDO YAKINPRIMA')

@section('content')
<!-- Distinct Hero Section (Centered) -->
<div class="relative bg-slate-950 pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
    <div class="absolute inset-0">
        <!-- Blurred and darkened background image -->
        <img src="{{ asset('images/fcl-bg.png') }}" alt="FCL Ship" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[3px]"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10 text-center flex flex-col items-center">
        <!-- Breadcrumbs (Centered) -->
        <nav class="flex text-sm text-slate-300 mb-8 justify-center" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2 bg-slate-900/40 px-4 py-2 rounded-full backdrop-blur-sm border border-slate-700/50">
                <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                <li><span class="hover:text-white transition-colors cursor-default">Layanan</span></li>
                <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                <li class="text-white font-medium">FCL</li>
            </ol>
        </nav>

        <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-white tracking-tight mb-6 uppercase drop-shadow-lg">
            Full Container Load
        </h1>
        <p class="text-xl md:text-2xl text-slate-200 font-light mb-10 max-w-3xl drop-shadow-md">
            Solusi eksklusif pengiriman berskala besar. Kargo Anda memonopoli seluruh ruang kontainer untuk privasi, keamanan, dan kecepatan maksimal.
        </p>

        <a href="#hubungi" class="bg-blue-600 text-white hover:bg-blue-500 font-bold py-4 px-10 rounded shadow-[0_10px_20px_rgba(37,99,235,0.3)] transition-transform hover:-translate-y-1 flex items-center gap-2">
            REQUEST FCL QUOTE <i class="fa-solid fa-arrow-right ml-2"></i>
        </a>
    </div>

    <!-- Integrated Stats Strip at bottom of Hero -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-20 border-t border-slate-700/60 pt-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x-0 md:divide-x divide-slate-700/60">
            <div>
                <p class="text-3xl font-bold text-white mb-2 drop-shadow">100%</p>
                <p class="text-sm text-slate-300 uppercase tracking-widest font-medium">Ruang Eksklusif</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-white mb-2 drop-shadow">0</p>
                <p class="text-sm text-slate-300 uppercase tracking-widest font-medium">Konsolidasi (CFS)</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-white mb-2 drop-shadow">20/40<span class="text-lg">ft</span></p>
                <p class="text-sm text-slate-300 uppercase tracking-widest font-medium">Opsi Kontainer</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-white mb-2 drop-shadow">Max</p>
                <p class="text-sm text-slate-300 uppercase tracking-widest font-medium">Tingkat Keamanan</p>
            </div>
        </div>
    </div>
</div>

<!-- Bento Box Features (Very distinct from standard grids) -->
<div class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h2 class="text-3xl lg:text-5xl font-black text-slate-900 tracking-tight uppercase">Mengapa FCL?</h2>
            <div class="w-20 h-2 bg-blue-600 mt-4"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-[250px]">
            <!-- Bento Item 1 (Spans 2 columns) -->
            <div class="md:col-span-2 bg-blue-900 rounded-3xl p-8 md:p-10 text-white relative overflow-hidden group">
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <i class="fa-solid fa-lock text-4xl text-blue-400"></i>
                    <div>
                        <h3 class="text-2xl md:text-3xl font-bold mb-3">Keamanan Privasi Mutlak</h3>
                        <p class="text-blue-100 max-w-lg">Segel pintu kontainer dikunci dari titik pemuatan pabrik Anda dan tidak akan dibuka sampai tiba di tujuan akhir (consignee).</p>
                    </div>
                </div>
                <div class="absolute -right-10 -bottom-10 opacity-10 group-hover:scale-110 transition-transform duration-700">
                    <i class="fa-solid fa-shield-halved text-[200px]"></i>
                </div>
            </div>

            <!-- Bento Item 2 -->
            <div class="bg-white rounded-3xl p-8 md:p-10 border border-slate-200 flex flex-col h-full justify-between group hover:border-blue-500 transition-colors">
                <i class="fa-solid fa-stopwatch text-4xl text-slate-800 group-hover:text-blue-600 transition-colors"></i>
                <div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Transit Tercepat</h3>
                    <p class="text-slate-600 text-sm">Menghindari proses tunggu (konsolidasi/dekonsolidasi) di gudang pelabuhan.</p>
                </div>
            </div>

            <!-- Bento Item 3 -->
            <div class="bg-white rounded-3xl p-8 md:p-10 border border-slate-200 flex flex-col h-full justify-between group hover:border-blue-500 transition-colors">
                <i class="fa-solid fa-chart-pie text-4xl text-slate-800 group-hover:text-blue-600 transition-colors"></i>
                <div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Efisiensi Volume</h3>
                    <p class="text-slate-600 text-sm">Biaya satuan per satuan berat jauh lebih ekonomis untuk kargo di atas 15 CBM.</p>
                </div>
            </div>

            <!-- Bento Item 4 (Spans 2 columns) -->
            <div class="md:col-span-2 bg-slate-900 rounded-3xl p-8 md:p-10 text-white relative overflow-hidden group">
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <i class="fa-solid fa-file-contract text-4xl text-slate-400"></i>
                    <div>
                        <h3 class="text-2xl md:text-3xl font-bold mb-3">Administrasi Sederhana</h3>
                        <p class="text-slate-400 max-w-lg">Satu kontainer, satu pengirim, satu penerima. Memudahkan pelacakan dokumen (Bill of Lading) serta kelancaran inspeksi Bea Cukai.</p>
                    </div>
                </div>
                <div class="absolute -right-10 -bottom-10 opacity-10 group-hover:scale-110 transition-transform duration-700">
                    <i class="fa-solid fa-file-invoice text-[200px]"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comparison Section (20ft vs 40ft) -->
<div class="py-24 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl lg:text-5xl font-black text-slate-900 tracking-tight uppercase mb-4">Pilihan Spesifikasi</h2>
            <p class="text-lg text-slate-600">Sesuaikan ukuran kontainer dengan bobot dan dimensi kargo Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- 20ft -->
            <div class="border-2 border-slate-100 rounded-2xl p-8 hover:border-blue-600 transition-colors group cursor-pointer">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-10 bg-blue-600 rounded flex items-center justify-center text-white font-bold text-sm">20'</div>
                    <h3 class="text-2xl font-bold text-slate-900 group-hover:text-blue-600 transition-colors">20ft Standard</h3>
                </div>
                <div class="group [perspective:1000px] mb-6">
                    <div class="rounded-xl overflow-hidden bg-slate-50/50 p-4 border border-slate-100 flex justify-center transition-all duration-700 ease-out group-hover:[transform:rotateY(15deg)_rotateX(5deg)_scale(1.05)] shadow-sm group-hover:shadow-[20px_20px_40px_rgba(0,0,0,0.15)]">
                        <img src="{{ asset('images/kontainer-20ft.png') }}" alt="20ft Container" class="w-full h-auto object-contain max-h-56">
                    </div>
                </div>
                <p class="text-slate-600 mb-6">Ideal untuk muatan yang memiliki bobot/densitas tinggi namun tidak memakan banyak tempat (seperti mesin padat, keramik, baja).</p>
                <ul class="space-y-2 text-sm text-slate-700 font-medium">
                    <li class="flex justify-between border-b border-slate-100 pb-2"><span>Kapasitas Volume:</span> <span class="text-slate-900 font-bold">~33 CBM</span></li>
                    <li class="flex justify-between border-b border-slate-100 pb-2"><span>Berat Muatan Maks:</span> <span class="text-slate-900 font-bold">~28 Ton</span></li>
                    <li class="flex justify-between border-b border-slate-100 pb-2"><span>Dimensi Dalam (P x L x T):</span> <span class="text-slate-900 font-bold">5.9m x 2.3m x 2.3m</span></li>
                </ul>
            </div>

            <!-- 40ft -->
            <div class="border-2 border-slate-100 rounded-2xl p-8 hover:border-blue-600 transition-colors group cursor-pointer">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-24 h-10 bg-slate-800 rounded flex items-center justify-center text-white font-bold text-sm">40' / 40' HC</div>
                    <h3 class="text-2xl font-bold text-slate-900 group-hover:text-blue-600 transition-colors">40ft / High Cube</h3>
                </div>
                <div class="group/3d [perspective:1000px] mb-6">
                    <div class="rounded-xl overflow-hidden bg-slate-800 p-4 border border-slate-700 flex justify-center transition-all duration-700 ease-out group-hover/3d:[transform:rotateY(-15deg)_rotateX(5deg)_scale(1.05)] shadow-sm group-hover/3d:shadow-[20px_20px_40px_rgba(0,0,0,0.15)]">
                        <img src="{{ asset('images/kontainer-40ft.png') }}" alt="40ft Container" class="w-full h-auto object-contain max-h-56">
                    </div>
                </div>
                <p class="text-slate-600 mb-6">Cocok untuk kargo yang memakan banyak tempat atau volume besar namun bobotnya relatif ringan (seperti garmen, furnitur, elektronik).</p>
                <ul class="space-y-2 text-sm text-slate-700 font-medium">
                    <li class="flex justify-between border-b border-slate-100 pb-2"><span>Kapasitas Volume:</span> <span class="text-slate-900 font-bold">~67 CBM (HC: ~76 CBM)</span></li>
                    <li class="flex justify-between border-b border-slate-100 pb-2"><span>Berat Muatan Maks:</span> <span class="text-slate-900 font-bold">~26 - 28 Ton</span></li>
                    <li class="flex justify-between border-b border-slate-100 pb-2"><span>Dimensi Dalam (P x L x T):</span> <span class="text-slate-900 font-bold">12m x 2.3m x 2.3m (HC: 2.6m)</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Workflow Section (Zig-zag steps) -->
<div class="py-24 bg-slate-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-5xl font-black text-center tracking-tight uppercase mb-20">Alur Pengiriman FCL</h2>
        
        <div class="space-y-16">
            <!-- Step 1 -->
            <div class="flex flex-col md:flex-row items-center gap-8 md:gap-16">
                <div class="w-full md:w-1/2 flex justify-center md:justify-end relative">
                    <div class="text-[120px] font-black text-slate-800 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 -z-10 select-none">01</div>
                    <img src="https://images.unsplash.com/photo-1580674684081-77699ca9d1d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Empty Container" class="rounded-xl shadow-2xl border border-slate-700 max-w-xs relative z-10 hover:scale-105 transition-transform duration-500">
                </div>
                <div class="w-full md:w-1/2">
                    <h3 class="text-2xl font-bold mb-4 text-blue-400">Positioning (Penarikan Empty)</h3>
                    <p class="text-slate-400 text-lg leading-relaxed">Kami menarik kontainer kosong dari depo menuju pabrik atau gudang muat Anda. Proses <em>stuffing</em> (pemuatan) sepenuhnya dilakukan di lokasi Anda dengan pengawasan penuh dari pihak Anda.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex flex-col md:flex-row-reverse items-center gap-8 md:gap-16">
                <div class="w-full md:w-1/2 flex justify-center md:justify-start relative">
                    <div class="text-[120px] font-black text-slate-800 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 -z-10 select-none">02</div>
                    <img src="https://images.unsplash.com/photo-1541427468627-a89a96e5ca1d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Port Yard" class="rounded-xl shadow-2xl border border-slate-700 max-w-xs relative z-10 hover:scale-105 transition-transform duration-500">
                </div>
                <div class="w-full md:w-1/2 text-left md:text-right">
                    <h3 class="text-2xl font-bold mb-4 text-blue-400">Trucking ke Pelabuhan & Segel</h3>
                    <p class="text-slate-400 text-lg leading-relaxed">Setelah selesai dimuat dan disegel resmi (sealed), armada truk kami membawa kontainer langsung ke pelabuhan muat tanpa harus melewati proses konsolidasi tambahan di gudang logistik.</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="flex flex-col md:flex-row items-center gap-8 md:gap-16">
                <div class="w-full md:w-1/2 flex justify-center md:justify-end relative">
                    <div class="text-[120px] font-black text-slate-800 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 -z-10 select-none">03</div>
                    <img src="https://images.unsplash.com/photo-1586528116311-ad8ed7e66a5a?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Shipping Vessel" class="rounded-xl shadow-2xl border border-slate-700 max-w-xs relative z-10 hover:scale-105 transition-transform duration-500">
                </div>
                <div class="w-full md:w-1/2">
                    <h3 class="text-2xl font-bold mb-4 text-blue-400">Voyage & Unloading</h3>
                    <p class="text-slate-400 text-lg leading-relaxed">Kontainer berlayar di atas kapal induk. Setibanya di pelabuhan tujuan, kontainer diturunkan secara utuh dan diserahkan kepada pihak penerima tanpa dibuka sedikitpun.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Minimalist -->
<div class="py-24 bg-blue-600 text-center" id="hubungi">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-black text-white uppercase tracking-tight mb-6">Booking Kontainer Anda Sekarang</h2>
        <p class="text-blue-100 text-xl mb-10 max-w-2xl mx-auto">Tingkatkan efisiensi logistik massal Anda. Konsultasikan jadwal keberangkatan, harga, dan ketersediaan ruang (space) untuk kargo FCL Anda.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="mailto:info@alexindoyp.co.id" class="inline-block bg-slate-900 text-white font-bold py-4 px-10 hover:bg-slate-800 transition-colors">
                HUBUNGI SALES (EMAIL)
            </a>
            <a href="https://wa.me/622112345678" target="_blank" class="inline-block bg-white text-blue-900 font-bold py-4 px-10 hover:bg-slate-100 transition-colors">
                <i class="fa-brands fa-whatsapp"></i> CHAT WHATSAPP
            </a>
        </div>
    </div>
</div>
@endsection
