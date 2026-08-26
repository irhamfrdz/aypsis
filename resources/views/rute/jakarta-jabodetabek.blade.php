@extends('layouts.public')
@section('title', 'Rute Jakarta & Jabodetabek | ALEXINDO YAKINPRIMA')

@section('content')

<!-- Dynamic Hero Section -->
<div class="relative bg-slate-950 pt-32 pb-24 lg:pt-48 lg:pb-32 overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/rute-jakarta-hero.jpg') }}" alt="Jakarta Jabodetabek Route" class="w-full h-full object-cover opacity-40 mix-blend-luminosity">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/90 to-transparent"></div>
    </div>
    
    <!-- Accent element -->
    <div class="absolute top-0 right-0 w-1/3 h-full bg-blue-500/5 transform skew-x-12 translate-x-32 hidden lg:block z-0 pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-slate-400 mb-8" aria-label="Breadcrumb" data-aos="fade-down">
            <ol class="inline-flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="hover:text-blue-400 transition-colors">Beranda</a></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i></li>
                <li><span class="cursor-default">Rute</span></li>
                <li><i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i></li>
                <li class="text-white font-medium">Jakarta & Jabodetabek</li>
            </ol>
        </nav>

        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 text-sm font-semibold mb-6" data-aos="fade-right" data-aos-delay="100">
                <i class="fa-solid fa-location-dot"></i> Hub Distribusi Utama
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-6 leading-tight tracking-tight" data-aos="fade-up" data-aos-delay="200">
                Rute Logistik <br>
                <span class="text-blue-500">Jakarta & Jabodetabek</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-300 mb-8 leading-relaxed max-w-2xl" data-aos="fade-up" data-aos-delay="300">
                Konektivitas tanpa batas menuju pusat perekonomian terbesar Indonesia. Kami memfasilitasi kelancaran arus barang dari dan menuju area Jakarta, Bogor, Depok, Tangerang, dan Bekasi dengan jadwal pengiriman yang presisi.
            </p>
            
            <div class="flex flex-wrap gap-4" data-aos="fade-up" data-aos-delay="400">
                <a href="#konsultasi" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-full transition-all shadow-lg hover:shadow-blue-500/30">
                    Cek Jadwal Kapal <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Tujuan & Pengertian -->
<div class="py-20 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right">
                <h2 class="text-3xl font-bold text-slate-900 mb-6 tracking-tight">Mengenal Rute Jakarta - Jabodetabek</h2>
                <div class="w-20 h-1.5 bg-blue-600 mb-8"></div>
                
                <h4 class="text-xl font-bold text-slate-800 mb-3">Definisi Jalur Strategis</h4>
                <p class="text-slate-600 text-lg mb-6 leading-relaxed">
                    <strong>Rute Jakarta & Jabodetabek</strong> adalah jalur distribusi utama yang menghubungkan pusat industri dan perdagangan di Ibukota beserta kota-kota penyangganya (Bogor, Depok, Tangerang, Bekasi) dengan berbagai rute antar pulau di Indonesia melalui Pelabuhan Tanjung Priok.
                </p>

                <h4 class="text-xl font-bold text-slate-800 mb-3 mt-8">Tujuan Ekspedisi & Forwarding</h4>
                <p class="text-slate-600 text-lg mb-8 leading-relaxed">
                    Tujuan utama rute ini adalah untuk <strong>memastikan <i>supply chain</i> (rantai pasok) nasional berjalan optimal</strong>. Jabodetabek bertindak sebagai sentra konsumsi maupun produksi. Pengiriman melalui rute ini dirancang untuk mendistribusikan barang jadi, material mentah, dan FMCG (<i>Fast-Moving Consumer Goods</i>) secara efisien menuju gudang distributor atau <i>end-user</i>.
                </p>
            </div>
            
            <div class="relative" data-aos="fade-left">
                <div class="absolute -inset-4 bg-slate-100 transform -rotate-2 rounded-2xl z-0"></div>
                <img src="{{ asset('images/peta-rute-jakarta.jpg') }}" alt="Peta Rute Distribusi Jakarta" class="relative z-10 w-full h-auto rounded-xl shadow-2xl border border-white/50">
                
                <!-- Floating badge -->
                <div class="absolute -bottom-6 -right-6 bg-white p-5 rounded-2xl shadow-xl z-20 border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500 font-medium">Jaringan Rute</div>
                        <div class="text-lg font-bold text-slate-900">100% Terkoneksi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Workflow / Alur Pengiriman -->
<div class="py-24 bg-slate-50 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-blue-600 font-semibold tracking-wider uppercase text-sm mb-2 block">Standar Operasional (SOP)</span>
            <h2 class="text-3xl font-bold text-slate-900 mb-4 tracking-tight">Alur Distribusi Rute Jabodetabek</h2>
            <p class="text-slate-500 max-w-2xl mx-auto">Kami mengadopsi sistem manajemen logistik kelas dunia untuk memastikan kargo Anda ditangani secara sistematis sejak penjemputan hingga tiba di titik akhir.</p>
        </div>

        <div class="relative">
            <!-- Connecting Line -->
            <div class="hidden lg:block absolute top-1/2 left-0 w-full h-0.5 bg-slate-200 -translate-y-1/2 z-0"></div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative z-10">
                <!-- Step 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 relative group hover:shadow-lg transition-all" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 bg-slate-900 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6 mx-auto relative shadow-lg group-hover:scale-110 transition-transform">
                        1
                        <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-3 text-center">Konsolidasi / Pick-up</h3>
                    <p class="text-sm text-slate-500 text-center">
                        Pengambilan kargo dari pabrik/gudang pengirim. Untuk layanan LCL, kargo akan dikumpulkan di fasilitas depo kami sebelum dimuat.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 relative group hover:shadow-lg transition-all" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 bg-slate-900 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6 mx-auto relative shadow-lg group-hover:scale-110 transition-transform">
                        2
                        <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs">
                            <i class="fa-solid fa-ship"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-3 text-center">Port Handling (Tj. Priok)</h3>
                    <p class="text-sm text-slate-500 text-center">
                        Proses <i>loading</i> (muat) peti kemas ke atas kapal laut di Pelabuhan Tanjung Priok dengan pengawasan ketat.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 relative group hover:shadow-lg transition-all" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-14 h-14 bg-slate-900 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6 mx-auto relative shadow-lg group-hover:scale-110 transition-transform">
                        3
                        <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-3 text-center">Linehaul / Stripping</h3>
                    <p class="text-sm text-slate-500 text-center">
                        Setelah kapal tiba di pelabuhan tujuan (atau sebaliknya saat tiba di Priok), peti kemas dibongkar dan dipersiapkan untuk transportasi darat.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border-t-4 border-t-blue-500 relative group hover:shadow-xl transition-all" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-14 h-14 bg-blue-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6 mx-auto relative shadow-lg group-hover:scale-110 transition-transform">
                        4
                        <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-emerald-500 text-white rounded-full flex items-center justify-center text-xs border border-white">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-blue-700 mb-3 text-center">Last-Mile Delivery</h3>
                    <p class="text-sm text-slate-600 text-center font-medium">
                        Pengiriman tahap akhir menuju alamat tujuan di seluruh area Jabodetabek. Bukti pengiriman (POD) langsung dilaporkan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Keunggulan Section -->
<div class="py-20 bg-slate-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 z-0 pointer-events-none opacity-20" style="background-image: url('{{ asset('images/background-tekstur05.png') }}'); background-size: cover; background-position: center;"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right">
                <img src="{{ asset('images/rute-jakarta-depot.jpg') }}" alt="Armada Truk Jabodetabek" class="w-full h-auto rounded-2xl shadow-2xl border border-slate-700">
            </div>
            
            <div data-aos="fade-left">
                <h2 class="text-3xl font-bold mb-6 tracking-tight">Kenapa Memilih Layanan Kami untuk Jabodetabek?</h2>
                <p class="text-slate-400 text-lg mb-8 leading-relaxed">
                    Sebagai episentrum niaga nasional, lalu lintas logistik di Jabodetabek sangat padat. Kami memiliki armada khusus dan infrastruktur depo yang mampu menjamin keandalan pengiriman Anda di area ini.
                </p>
                
                <ul class="space-y-6">
                    <li class="flex items-start gap-4 bg-slate-800/50 p-4 rounded-xl border border-white/5">
                        <i class="fa-solid fa-truck-ramp-box text-blue-400 mt-1 text-2xl"></i>
                        <div>
                            <h4 class="font-bold text-white text-lg">Infrastruktur Depo Terpusat</h4>
                            <p class="text-sm text-slate-400 mt-1">Kami mengelola lahan penumpukan mandiri yang dekat dengan kawasan industri dan Pelabuhan Tanjung Priok untuk bongkar muat cepat.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-4 bg-slate-800/50 p-4 rounded-xl border border-white/5">
                        <i class="fa-solid fa-route text-blue-400 mt-1 text-2xl"></i>
                        <div>
                            <h4 class="font-bold text-white text-lg">Peta Rute Anti-Macet</h4>
                            <p class="text-sm text-slate-400 mt-1">Tim lapangan kami sangat memahami topografi wilayah Jabodetabek, memastikan truk bermanuver menghindari kemacetan ibukota.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="py-24 relative overflow-hidden" id="konsultasi">
    <div class="absolute inset-0 z-0 bg-blue-600"></div>
    <div class="absolute inset-0 z-0 pointer-events-none opacity-30 mix-blend-overlay" style="background-image: url('{{ asset('images/background-tekstur06.png') }}'); background-size: cover; background-position: center;"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10" data-aos="zoom-in-up">
        <h2 class="text-3xl md:text-5xl font-black text-white mb-6">Siap Mengirim Kargo ke Jakarta / Jabodetabek?</h2>
        <p class="text-blue-100 text-lg md:text-xl mb-10 max-w-2xl mx-auto leading-relaxed">
            Dapatkan tarif komersial terbaik untuk distribusi skala besar maupun kecil Anda. Hubungi tim marketing kami untuk reservasi ruang muat.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-5">
            <a href="#" class="w-full sm:w-auto px-8 py-4 bg-white text-blue-700 font-bold rounded-full hover:bg-blue-50 transition-all shadow-xl hover:-translate-y-1 flex items-center justify-center gap-2">
                <i class="fa-brands fa-whatsapp text-xl text-emerald-500"></i> Hubungi Tim Sales
            </a>
            <a href="#" class="w-full sm:w-auto px-8 py-4 bg-transparent border-2 border-white/30 text-white font-bold rounded-full hover:bg-white/10 hover:border-white transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-file-invoice"></i> Minta Penawaran Harga
            </a>
        </div>
    </div>
</div>

@endsection
