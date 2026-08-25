@extends('layouts.public')
@section('title', 'Sea Freight Services | ALEXINDO YAKINPRIMA')

@section('content')
<!-- Hero Section -->
<div class="relative bg-slate-900 pt-32 pb-24 lg:pt-48 lg:pb-32 overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('images/sea-freight-bg.png') }}" alt="Sea Freight Vessel" class="w-full h-full object-cover opacity-80">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/60 to-transparent"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        <div class="max-w-3xl">
            <!-- Breadcrumbs -->
            <nav class="flex text-sm text-slate-400 mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
                            <span class="hover:text-white transition-colors cursor-default">Layanan</span>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
                            <span class="text-blue-400 font-medium">Sea Freight</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <span class="inline-block py-1.5 px-4 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 font-semibold tracking-wider uppercase text-xs mb-6 backdrop-blur-sm" data-lang-en="Premium Ocean Freight" data-lang-zh="优质海运">Layanan Pelayaran Premium</span>
            
            <h1 class="text-4xl md:text-5xl lg:text-7xl font-bold text-white tracking-tight mb-6 leading-tight">
                Global <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Sea Freight</span> Solutions
            </h1>
            
            <p class="mt-4 text-xl text-slate-300 max-w-2xl leading-relaxed font-light mb-10" data-lang-en="Connecting your business to the world through reliable, efficient, and cost-effective maritime logistics." data-lang-zh="通过可靠、高效且具有成本效益的海上物流，将您的业务连接到世界。">
                Menghubungkan bisnis Anda secara global melalui layanan logistik maritim yang aman, andal, dan hemat biaya dengan visibilitas end-to-end.
            </p>

            <div class="flex flex-wrap gap-4">
                <a href="#hubungi" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3.5 px-8 rounded-full transition-all duration-300 shadow-lg shadow-blue-600/30 flex items-center gap-2 group">
                    Konsultasi Pengiriman <i class="fa-solid fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="{{ route('public.pelabuhan') }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 font-semibold py-3.5 px-8 rounded-full transition-all duration-300 backdrop-blur-md flex items-center gap-2">
                    <i class="fa-solid fa-anchor"></i> Cek Rute Pelabuhan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Key Stats (Floating Overlap) -->
<div class="relative -mt-12 z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
    <div class="bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.1)] p-8 lg:p-10 border border-slate-100 flex flex-col md:flex-row justify-between gap-8 divide-y md:divide-y-0 md:divide-x divide-slate-100">
        <div class="flex-1 text-center md:text-left md:pl-4">
            <p class="text-4xl font-black text-slate-900 mb-1">99<span class="text-blue-600">%</span></p>
            <p class="text-sm text-slate-500 font-medium uppercase tracking-wide">On-Time Delivery</p>
        </div>
        <div class="flex-1 text-center md:text-left pt-6 md:pt-0 md:pl-12">
            <p class="text-4xl font-black text-slate-900 mb-1">50<span class="text-blue-600">+</span></p>
            <p class="text-sm text-slate-500 font-medium uppercase tracking-wide">Pelabuhan Tujuan</p>
        </div>
        <div class="flex-1 text-center md:text-left pt-6 md:pt-0 md:pl-12">
            <p class="text-4xl font-black text-slate-900 mb-1">24<span class="text-blue-600">/7</span></p>
            <p class="text-sm text-slate-500 font-medium uppercase tracking-wide">Customer Support</p>
        </div>
        <div class="flex-1 text-center md:text-left pt-6 md:pt-0 md:pl-12">
            <p class="text-4xl font-black text-slate-900 mb-1">10<span class="text-blue-600">K+</span></p>
            <p class="text-sm text-slate-500 font-medium uppercase tracking-wide">TEUs Per Tahun</p>
        </div>
    </div>
</div>

<!-- Intro / Overview Section -->
<div class="py-16 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            <div class="relative order-2 lg:order-1">
                <!-- Abstract decorative element -->
                <div class="absolute -top-10 -left-10 w-72 h-72 bg-blue-50 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
                <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-cyan-50 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
                
                <div class="relative rounded-[2rem] overflow-hidden shadow-2xl border border-slate-100 group">
                    <img src="https://images.unsplash.com/photo-1578575437130-527eed3abbec?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Port Logistics" class="w-full h-[550px] object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
                    <div class="absolute bottom-8 left-8 right-8">
                        <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-white/20 transform transition-transform duration-500 group-hover:-translate-y-2">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <i class="fa-solid fa-satellite-dish"></i>
                                </div>
                                <h4 class="font-bold text-slate-900 text-lg">Visibilitas Maksimal</h4>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">Pantau pergerakan kargo secara real-time dari pelabuhan asal hingga tujuan akhir, memberikan Anda kontrol penuh atas rantai pasok.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="order-1 lg:order-2">
                <span class="text-blue-600 font-bold tracking-wider uppercase text-sm mb-3 block flex items-center gap-2">
                    <span class="w-8 h-px bg-blue-600"></span> Tentang Layanan
                </span>
                <h2 class="text-3xl lg:text-5xl font-bold text-slate-900 mb-6 leading-[1.15]">Mendorong Pertumbuhan Perdagangan Global</h2>
                <div class="prose prose-lg text-slate-600">
                    <p class="mb-5 leading-relaxed text-lg">
                        Pengiriman jalur laut (<strong>Sea Freight</strong>) tetap menjadi pilar utama perdagangan internasional. Dengan kapasitas volume masif dan efisiensi biaya yang tidak tertandingi, layanan ini sangat ideal untuk pergerakan logistik modern antar pulau maupun negara.
                    </p>
                    <p class="mb-8 leading-relaxed">
                        Sebagai perusahaan integrator maritim terkemuka, <strong>PT Alexindo Yakinprima</strong> merancang solusi <em>end-to-end</em> untuk menyederhanakan kompleksitas pengiriman peti kemas. Kami menavigasi setiap detail regulasi dan rute agar kargo Anda tiba dengan aman.
                    </p>
                </div>
                
                <div class="space-y-4 mb-8">
                    <div class="flex items-start gap-4">
                        <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center flex-shrink-0 mt-1 shadow-md shadow-blue-500/20">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Jadwal Keberangkatan Presisi</h4>
                            <p class="text-slate-600 text-sm mt-1">Konsistensi jadwal untuk perencanaan produksi yang lebih baik.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center flex-shrink-0 mt-1 shadow-md shadow-blue-500/20">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Jaminan Ruang (Space Guarantee)</h4>
                            <p class="text-slate-600 text-sm mt-1">Prioritas ketersediaan space pada puncak musim pengiriman (peak season).</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center flex-shrink-0 mt-1 shadow-md shadow-blue-500/20">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Optimasi Rute & Biaya</h4>
                            <p class="text-slate-600 text-sm mt-1">Saran profesional untuk mengkombinasikan efisiensi biaya dan kecepatan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FCL & LCL Detailed Section -->
<div class="py-24 bg-slate-50 relative">
    <!-- Top separator -->
    <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-blue-600 font-bold tracking-wider uppercase text-sm mb-3 block">Opsi Peti Kemas</span>
            <h2 class="text-3xl lg:text-5xl font-bold text-slate-900 mb-6">Solusi Kapasitas Spesifik</h2>
            <p class="text-lg text-slate-600 leading-relaxed">Setiap bisnis memiliki skala berbeda. Kami memberikan fleksibilitas untuk memilih layanan yang secara presisi menyesuaikan volume kargo Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10">
            <!-- FCL Card -->
            <div class="bg-white rounded-[2rem] p-10 lg:p-12 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_20px_50px_rgb(0,0,0,0.1)] transition-all duration-500 relative overflow-hidden group border border-slate-100">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-50 rounded-full blur-3xl transition-transform duration-700 group-hover:scale-150"></div>
                
                <div class="relative z-10 flex flex-col h-full">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center text-3xl mb-8 shadow-xl shadow-blue-600/30 transform group-hover:-translate-y-2 transition-transform duration-500">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-slate-900 mb-4">Full Container Load (FCL)</h3>
                    <p class="text-slate-600 leading-relaxed mb-8 flex-grow">
                        Layanan penyewaan satu peti kemas secara eksklusif. Barang Anda tidak digabungkan dengan milik perusahaan lain. Solusi mutlak untuk pengiriman massal dengan keamanan maksimal dan waktu transit tercepat.
                    </p>
                    
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <ul class="space-y-4">
                            <li class="flex items-center gap-3 text-slate-700 font-medium">
                                <i class="fa-solid fa-lock text-blue-500 w-5"></i> Privasi dan keamanan tertinggi.
                            </li>
                            <li class="flex items-center gap-3 text-slate-700 font-medium">
                                <i class="fa-solid fa-bolt text-blue-500 w-5"></i> Tanpa proses konsolidasi ulang di gudang (CFS).
                            </li>
                            <li class="flex items-center gap-3 text-slate-700 font-medium">
                                <i class="fa-solid fa-ruler-combined text-blue-500 w-5"></i> Tersedia ukuran 20ft, 40ft (Dry/HC/Reefer).
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- LCL Card -->
            <div class="bg-white rounded-[2rem] p-10 lg:p-12 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_20px_50px_rgb(0,0,0,0.1)] transition-all duration-500 relative overflow-hidden group border border-slate-100">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-cyan-50 rounded-full blur-3xl transition-transform duration-700 group-hover:scale-150"></div>
                
                <div class="relative z-10 flex flex-col h-full">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-cyan-500 to-cyan-700 text-white flex items-center justify-center text-3xl mb-8 shadow-xl shadow-cyan-600/30 transform group-hover:-translate-y-2 transition-transform duration-500">
                        <i class="fa-solid fa-cubes"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-slate-900 mb-4">Less than Container Load (LCL)</h3>
                    <p class="text-slate-600 leading-relaxed mb-8 flex-grow">
                        Solusi <em>space-sharing</em> dimana Anda hanya membayar ruang yang digunakan. Kargo Anda dikonsolidasikan secara aman bersama pengiriman lain. Menghilangkan kewajiban untuk menunggu stok penuh.
                    </p>
                    
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <ul class="space-y-4">
                            <li class="flex items-center gap-3 text-slate-700 font-medium">
                                <i class="fa-solid fa-percent text-cyan-500 w-5"></i> Hemat biaya drastis untuk volume kecil (parsial).
                            </li>
                            <li class="flex items-center gap-3 text-slate-700 font-medium">
                                <i class="fa-solid fa-money-bill-transfer text-cyan-500 w-5"></i> Mempercepat arus kas (cashflow) barang UMKM.
                            </li>
                            <li class="flex items-center gap-3 text-slate-700 font-medium">
                                <i class="fa-solid fa-warehouse text-cyan-500 w-5"></i> Manajemen konsolidasi profesional di gudang.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Why Choose Us Grid -->
<div class="py-24 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end gap-8 mb-16">
            <div class="max-w-2xl">
                <span class="text-blue-600 font-bold tracking-wider uppercase text-sm mb-3 block">Keunggulan Inti</span>
                <h2 class="text-3xl lg:text-5xl font-bold text-slate-900">Infrastruktur Kuat.<br>Layanan Andal.</h2>
            </div>
            <p class="text-lg text-slate-600 max-w-md pb-2">
                Kemitraan strategis dan integrasi teknologi modern memastikan nilai tambah bagi logistik bisnis Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Item 1 -->
            <div class="p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl hover:border-transparent transition-all duration-300 group">
                <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-blue-600 text-2xl mb-6 group-hover:bg-blue-600 group-hover:border-blue-600 group-hover:text-white group-hover:-translate-y-1 transition-all duration-300">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Jaringan Kemitraan Luas</h4>
                <p class="text-slate-600 leading-relaxed">Kolaborasi langsung dengan operator pelayaran tier-1 nasional, menjamin alokasi space secara konsisten di musim tersibuk.</p>
            </div>
            
            <!-- Item 2 -->
            <div class="p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl hover:border-transparent transition-all duration-300 group">
                <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-blue-600 text-2xl mb-6 group-hover:bg-blue-600 group-hover:border-blue-600 group-hover:text-white group-hover:-translate-y-1 transition-all duration-300">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Manajemen Risiko</h4>
                <p class="text-slate-600 leading-relaxed">Ahli logistik kami merancang perlindungan preventif serta menyediakan akses asuransi laut untuk menjamin keamanan aset finansial kargo.</p>
            </div>
            
            <!-- Item 3 -->
            <div class="p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl hover:border-transparent transition-all duration-300 group">
                <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-blue-600 text-2xl mb-6 group-hover:bg-blue-600 group-hover:border-blue-600 group-hover:text-white group-hover:-translate-y-1 transition-all duration-300">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Integrasi Door-to-Door</h4>
                <p class="text-slate-600 leading-relaxed">Sinkronisasi armada truk darat dengan kedatangan kapal untuk menciptakan distribusi multimoda (door-to-door) tanpa jeda.</p>
            </div>

            <!-- Item 4 -->
            <div class="p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl hover:border-transparent transition-all duration-300 group">
                <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-blue-600 text-2xl mb-6 group-hover:bg-blue-600 group-hover:border-blue-600 group-hover:text-white group-hover:-translate-y-1 transition-all duration-300">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Kelancaran Administrasi</h4>
                <p class="text-slate-600 leading-relaxed">Penanganan penerbitan Bill of Lading, kelengkapan manifes pelabuhan, dan proses bea cukai dengan akurasi 100%.</p>
            </div>

            <!-- Item 5 -->
            <div class="p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl hover:border-transparent transition-all duration-300 group">
                <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-blue-600 text-2xl mb-6 group-hover:bg-blue-600 group-hover:border-blue-600 group-hover:text-white group-hover:-translate-y-1 transition-all duration-300">
                    <i class="fa-solid fa-leaf"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Ekosistem Keberlanjutan</h4>
                <p class="text-slate-600 leading-relaxed">Mendukung praktek 'Green Logistics' dengan efisiensi muatan yang menghasilkan footprint CO2 lebih rendah per ton pengiriman.</p>
            </div>
            
            <!-- Item 6 -->
            <div class="p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl hover:border-transparent transition-all duration-300 group">
                <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-blue-600 text-2xl mb-6 group-hover:bg-blue-600 group-hover:border-blue-600 group-hover:text-white group-hover:-translate-y-1 transition-all duration-300">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-3">Customer Success Center</h4>
                <p class="text-slate-600 leading-relaxed">Tim Account Management berdedikasi yang menangani kendala teknis dan menyediakan update posisi muatan secara responsif.</p>
            </div>
        </div>
    </div>
</div>

<!-- Premium CTA Section -->
<div class="py-24 bg-white relative" id="hubungi">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="bg-gradient-to-br from-blue-900 via-slate-900 to-blue-950 rounded-[3rem] p-10 lg:p-20 overflow-hidden shadow-2xl relative">
            <!-- Background Elements -->
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-500 rounded-full blur-[100px] opacity-20 transform translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-cyan-500 rounded-full blur-[100px] opacity-20 transform -translate-x-1/2 translate-y-1/2"></div>
            
            <div class="relative z-10 text-center max-w-3xl mx-auto">
                <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-blue-300 font-semibold tracking-wider uppercase text-sm mb-6 backdrop-blur-sm">Tingkatkan Bisnis Anda</span>
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">Siap Mendorong Rantai Pasok Anda Lebih Jauh?</h2>
                <p class="text-blue-100 text-lg mb-10 leading-relaxed max-w-2xl mx-auto font-light">
                    Diskusikan spesifikasi kargo dan tujuan logistik Anda bersama konsultan kami. Kami merumuskan strategi pelayaran paling efisien untuk margin bisnis yang lebih baik.
                </p>
                <div class="flex flex-col sm:flex-row gap-5 justify-center">
                    <a href="mailto:info@alexindoyp.co.id" class="inline-flex items-center justify-center bg-white text-blue-900 font-bold py-4 px-10 rounded-full hover:bg-slate-50 transition-all duration-300 shadow-[0_0_40px_rgba(255,255,255,0.2)] hover:shadow-[0_0_60px_rgba(255,255,255,0.3)] hover:-translate-y-1">
                        <i class="fa-solid fa-envelope mr-3 text-blue-600"></i> Minta Quotation
                    </a>
                    <a href="https://wa.me/622112345678" target="_blank" class="inline-flex items-center justify-center bg-transparent border-2 border-white/30 text-white font-bold py-4 px-10 rounded-full hover:bg-white/10 hover:border-white transition-all duration-300">
                        <i class="fa-brands fa-whatsapp mr-3 text-green-400 text-xl"></i> Konsultasi WhatsApp
                    </a>
                </div>
                
                <div class="mt-12 pt-12 border-t border-white/10 flex flex-wrap justify-center gap-8 md:gap-16 text-slate-300 text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-check-circle text-blue-400"></i> Respon dalam 24 Jam
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-check-circle text-blue-400"></i> Harga Kompetitif
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-check-circle text-blue-400"></i> Analisis Rute Gratis
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
