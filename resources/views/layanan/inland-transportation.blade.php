@extends('layouts.public')
@section('title', 'Inland Transportation | ALEXINDO YAKINPRIMA')

@section('content')

<!-- Dynamic Hero Section -->
<div class="relative bg-slate-900 pt-32 pb-24 lg:pt-48 lg:pb-32 overflow-hidden">
    <!-- Dynamic angled background -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/inland-hero.jpg') }}" alt="Inland Transportation" class="w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/90 to-transparent"></div>
    </div>
    
    <!-- Emerald green accent polygon -->
    <div class="absolute top-0 right-0 w-1/2 h-full bg-emerald-600/10 transform skew-x-12 translate-x-32 hidden lg:block z-0 pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-slate-400 mb-8" aria-label="Breadcrumb" data-aos="fade-down">
            <ol class="inline-flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="hover:text-emerald-400 transition-colors">Beranda</a></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] text-slate-600"></i></li>
                <li><span class="text-slate-500 cursor-default">Layanan</span></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] text-slate-600"></i></li>
                <li class="text-white font-medium">Inland Transportation</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div data-aos="fade-right">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 font-bold text-sm mb-6 uppercase tracking-wider">
                    <i class="fa-solid fa-truck-fast"></i> Domestic Logistics
                </div>
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-white tracking-tight mb-6 leading-tight">
                    INLAND<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-emerald-600">TRANSPORTATION</span>
                </h1>
                <p class="text-xl text-slate-300 leading-relaxed mb-10 max-w-lg font-light">
                    Nadi utama distribusi rantai pasok Anda. Kami menghubungkan pelabuhan, pabrik, dan pusat distribusi di seluruh wilayah nusantara dengan presisi waktu tingkat tinggi.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#armada" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-4 px-8 rounded-xl shadow-[0_10px_20px_rgba(5,150,105,0.3)] transition-all flex items-center justify-center gap-2 transform hover:-translate-y-1">
                        LIHAT KAPASITAS ARMADA <i class="fa-solid fa-arrow-down"></i>
                    </a>
                </div>
            </div>
            
            <div class="hidden lg:block relative" data-aos="zoom-in" data-aos-delay="200">
                <!-- Data metrics card floating -->
                <div class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white/10 backdrop-blur-xl border border-white/20 p-8 rounded-3xl shadow-2xl w-80">
                    <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-4">
                        <span class="text-slate-300 font-medium">Fleet Tracker</span>
                        <span class="flex items-center gap-2 text-emerald-400 text-xs font-bold"><span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> LIVE</span>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">On-Time Performance</p>
                            <p class="text-3xl font-black text-white">99.4%</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">Total Coverage</p>
                            <p class="text-2xl font-bold text-white">Nasional (Domestik)</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">GPS Integration</p>
                            <p class="text-lg font-medium text-emerald-400"><i class="fa-solid fa-satellite-dish mr-1"></i> Active on all fleets</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section (Clean, High Contrast) -->
<div class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            
            <div class="lg:col-span-5" data-aos="fade-right">
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-6 tracking-tight">Menjembatani<br>Setiap Titik Distribusi.</h2>
                <div class="w-16 h-1.5 bg-emerald-500 mb-8 rounded-full"></div>
                <p class="text-slate-600 leading-relaxed mb-6">
                    <strong>Inland Transportation</strong> (Transportasi Darat) adalah tulang punggung dari seluruh proses logistik. Baik itu mengangkut peti kemas ekspor dari pabrik ke pelabuhan, maupun mendistribusikan barang jadi (*Fast Moving Consumer Goods*) ke gudang-gudang regional.
                </p>
                <p class="text-slate-600 leading-relaxed mb-8">
                    Kami memahami bahwa keterlambatan di jalur darat akan berdampak efek domino pada jadwal kapal atau ketersediaan stok ritel Anda. Oleh karena itu, kami menerapkan sistem manajemen armada berbasis data untuk efisiensi rute.
                </p>
                
                <ul class="space-y-4">
                    <li class="flex items-center gap-3 text-slate-700 font-medium">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i> Manajemen Rute Cerdas
                    </li>
                    <li class="flex items-center gap-3 text-slate-700 font-medium">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i> Visibilitas Pelacakan Real-Time
                    </li>
                    <li class="flex items-center gap-3 text-slate-700 font-medium">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i> Pengemudi Tersertifikasi & Berpengalaman
                    </li>
                </ul>
            </div>

            <div class="lg:col-span-7" data-aos="fade-left">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="relative h-64 rounded-2xl overflow-hidden shadow-lg group">
                        <img src="{{ asset('images/truck-container.jpg') }}" alt="Container Trucking" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <h4 class="text-white font-bold text-lg mb-1">Container Haulage</h4>
                            <p class="text-emerald-300 text-xs font-semibold uppercase">Port-to-Door / Door-to-Port</p>
                        </div>
                    </div>
                    <div class="relative h-64 rounded-2xl overflow-hidden shadow-lg group mt-0 sm:mt-12">
                        <img src="{{ asset('images/truck-box.jpg') }}" alt="Box Truck Distribution" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <h4 class="text-white font-bold text-lg mb-1">Distribution Logistics</h4>
                            <p class="text-emerald-300 text-xs font-semibold uppercase">Warehouse / Hub-and-Spoke</p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Workflow / Operational Model (Cards) -->
<div class="py-24 bg-slate-50" id="alur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight mb-4">Model Layanan Operasional</h2>
            <p class="text-slate-600 text-lg max-w-2xl mx-auto">Kami menyesuaikan alur distribusi dengan model bisnis perusahaan Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-3xl mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-ship"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Pre/On-Carriage</h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-4">
                    Layanan penarikan peti kemas ekspor/impor. Menghubungkan pelabuhan laut utama dengan pabrik atau gudang Anda menggunakan armada *Tractor Head* & *Chassis*.
                </p>
                <div class="bg-slate-50 p-3 rounded-lg text-xs text-slate-500 font-medium border border-slate-100">
                    <span class="text-emerald-600"><i class="fa-solid fa-check"></i></span> FCL (20ft / 40ft / 45ft)
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-3xl mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Hub & Spoke Distribution</h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-4">
                    Konsolidasi barang di gudang pusat (Hub) untuk kemudian didistribusikan ke gudang-gudang regional atau cabang ritel (Spoke) menggunakan berbagai ukuran truk.
                </p>
                <div class="bg-slate-50 p-3 rounded-lg text-xs text-slate-500 font-medium border border-slate-100">
                    <span class="text-blue-600"><i class="fa-solid fa-check"></i></span> LTL & FTL Distribution
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-3xl mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-route"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Dedicated Transport</h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-4">
                    Penyediaan armada khusus yang sepenuhnya didedikasikan (dikontrak) untuk melayani jalur distribusi eksklusif perusahaan Anda dalam jangka waktu tertentu.
                </p>
                <div class="bg-slate-50 p-3 rounded-lg text-xs text-slate-500 font-medium border border-slate-100">
                    <span class="text-emerald-600"><i class="fa-solid fa-check"></i></span> Contract Logistics
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fleet Types (Visual List) -->
<div class="py-24 bg-slate-900 relative overflow-hidden" id="armada">
    <!-- Local Texture Pattern Overlay -->
    <div class="absolute inset-0 z-0 pointer-events-none" style="background-image: url('{{ asset('images/background-tekstur.png') }}'); background-size: cover; background-position: center;"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-white mb-4 tracking-tight">Ketersediaan Armada Darat</h2>
            <p class="text-slate-400 text-lg">Dari paket kargo ringan hingga peti kemas masif, armada kami siap beroperasi.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
            <!-- Fleet 1 -->
            <div class="bg-slate-800 rounded-2xl p-6 text-center border-b-4 border-emerald-500 hover:bg-slate-700 transition-colors" data-aos="zoom-in" data-aos-delay="100">
                <div class="h-20 flex items-center justify-center text-5xl text-slate-300 mb-4">
                    <i class="fa-solid fa-truck-front"></i>
                </div>
                <h4 class="text-lg font-bold text-white mb-1">CDD & CDE</h4>
                <p class="text-xs text-slate-400">(Colt Diesel Engkel / Double)</p>
                <p class="text-xs text-emerald-400 mt-2 font-medium">Kapasitas: 2 - 4 Ton</p>
            </div>
            
            <!-- Fleet 2 -->
            <div class="bg-slate-800 rounded-2xl p-6 text-center border-b-4 border-emerald-500 hover:bg-slate-700 transition-colors" data-aos="zoom-in" data-aos-delay="200">
                <div class="h-20 flex items-center justify-center text-5xl text-slate-300 mb-4">
                    <i class="fa-solid fa-truck"></i>
                </div>
                <h4 class="text-lg font-bold text-white mb-1">Fuso & Tronton</h4>
                <p class="text-xs text-slate-400">(Bak Terbuka / Box tertutup)</p>
                <p class="text-xs text-emerald-400 mt-2 font-medium">Kapasitas: 8 - 15 Ton</p>
            </div>

            <!-- Fleet 3 -->
            <div class="bg-slate-800 rounded-2xl p-6 text-center border-b-4 border-emerald-500 hover:bg-slate-700 transition-colors" data-aos="zoom-in" data-aos-delay="300">
                <div class="h-20 flex items-center justify-center text-5xl text-slate-300 mb-4">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <h4 class="text-lg font-bold text-white mb-1">Wingbox</h4>
                <p class="text-xs text-slate-400">(Bongkar muat tiga sisi)</p>
                <p class="text-xs text-emerald-400 mt-2 font-medium">Kapasitas: s/d 20 Ton</p>
            </div>

            <!-- Fleet 4 -->
            <div class="bg-slate-800 rounded-2xl p-6 text-center border-b-4 border-emerald-500 hover:bg-slate-700 transition-colors" data-aos="zoom-in" data-aos-delay="400">
                <div class="h-20 flex items-center justify-center text-5xl text-slate-300 mb-4">
                    <i class="fa-solid fa-truck-moving"></i>
                </div>
                <h4 class="text-lg font-bold text-white mb-1">Tractor Head</h4>
                <p class="text-xs text-slate-400">(Chassis 20ft / 40ft / 45ft)</p>
                <p class="text-xs text-emerald-400 mt-2 font-medium">Kapasitas: s/d 30 Ton</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-emerald-600 py-20 relative overflow-hidden">
    <!-- Local Texture Pattern Overlay -->
    <div class="absolute inset-0 z-0 pointer-events-none" style="background-image: url('{{ asset('images/background-tekstur02.png') }}'); background-size: cover; background-position: center;"></div>
    
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10" data-aos="zoom-in-up">
        <h2 class="text-3xl md:text-5xl font-black text-white mb-6">Optimalkan Rute Darat Anda</h2>
        <p class="text-emerald-100 text-lg mb-10 max-w-2xl mx-auto">
            Kurangi biaya distribusi dan tingkatkan kecepatan pengiriman dengan manajemen armada transportasi dari AYP Logistics.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="mailto:sales@alexindoyp.co.id" class="bg-slate-900 text-white font-bold py-4 px-8 rounded-xl hover:bg-slate-800 transition-colors shadow-lg flex items-center justify-center gap-2">
                <i class="fa-solid fa-envelope"></i> MINTA PENAWARAN TARIF
            </a>
            <a href="https://wa.me/622112345678" target="_blank" class="bg-transparent border-2 border-white text-white font-bold py-4 px-8 rounded-xl hover:bg-white hover:text-emerald-600 transition-colors flex items-center justify-center gap-2">
                <i class="fa-brands fa-whatsapp text-xl"></i> HUBUNGI DISPATCHER
            </a>
        </div>
    </div>
</div>

@endsection
