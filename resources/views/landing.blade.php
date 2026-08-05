@extends('layouts.public')

@section('content')
    <!-- Hero Section -->
    <section id="beranda" class="hero-bg min-h-screen flex items-center relative pt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-white space-y-6">
                    <div class="inline-block px-4 py-1.5 rounded-full bg-blue-600/30 border border-blue-400/30 backdrop-blur-sm text-sm font-semibold tracking-wide text-blue-200 uppercase mb-2" data-lang-en="Trusted Logistics Integrator" data-lang-zh="值得信赖的物流集成商">
                        Integrator Logistik Terpercaya
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-bold leading-tight">
                        ALEXINDO <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">YAKINPRIMA</span><br>
                    </h1>
                    <p class="text-lg lg:text-xl text-slate-300 max-w-lg font-light leading-relaxed" data-lang-en="PT Alexindo Yakinprima is your best partner for container shipping and integrated logistics services throughout Indonesia." data-lang-zh="PT Alexindo Yakinprima 是您在全印尼集装箱运输和综合物流服务的最佳合作伙伴。">
                        PT Alexindo Yakinprima merupakan partner terbaik Anda untuk pengiriman peti kemas dan layanan logistik terpadu di seluruh Indonesia.
                    </p>
                    <div class="flex flex-wrap gap-4 pt-4">
                        <a href="#layanan" class="btn-primary px-8 py-3.5 rounded-full font-semibold text-white text-lg" data-lang-en="Explore Services" data-lang-zh="探索服务">
                            Eksplorasi Layanan
                        </a>
                        <a href="#rute" class="px-8 py-3.5 rounded-full font-semibold text-white border border-white/30 hover:bg-white/10 transition duration-300 text-lg backdrop-blur-sm" data-lang-en="View Schedule" data-lang-zh="查看时间表">
                            Lihat Jadwal
                        </a>
                    </div>
                </div>
                
                <!-- Quick Tracking/Schedule Card -->
                <div class="glass-panel rounded-2xl p-8 lg:p-10 transform translate-y-8 lg:translate-y-0 transition-all duration-300" id="tracking-card">
                    <div class="flex justify-between items-center mb-6 cursor-pointer group transition-all duration-300" id="toggle-tracking-btn">
                        <h3 class="text-2xl font-bold text-slate-800 flex items-center gap-3" data-lang-en='<i class="fa-solid fa-magnifying-glass text-blue-600"></i> Track Shipment' data-lang-zh='<i class="fa-solid fa-magnifying-glass text-blue-600"></i> 追踪货运'>
                            <i class="fa-solid fa-magnifying-glass text-blue-600"></i> Lacak Pengiriman
                        </h3>
                        <button type="button" class="text-slate-400 group-hover:text-blue-600 transition-colors bg-slate-100 group-hover:bg-blue-100 w-8 h-8 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-chevron-up transition-transform duration-300" id="tracking-chevron"></i>
                        </button>
                    </div>
                    
                    <div id="tracking-content" class="transition-all duration-500 ease-in-out overflow-hidden opacity-100" style="max-height: 1000px;">
                    <form action="#" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1" data-lang-en="Receipt / Container Number" data-lang-zh="收据 / 集装箱号">Nomor Resi / Kontainer</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-box text-slate-400"></i>
                                </div>
                                <input type="text" class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 bg-slate-50 text-slate-900 transition-colors" placeholder="Masukkan nomor resi Anda..." data-lang-en-placeholder="Enter your receipt number..." data-lang-zh-placeholder="输入您的收据号...">
                            </div>
                        </div>
                        <button type="button" class="w-full btn-primary text-white font-bold py-3.5 px-4 rounded-xl flex justify-center items-center gap-2" data-lang-en='Track Now <i class="fa-solid fa-arrow-right"></i>' data-lang-zh='立即追踪 <i class="fa-solid fa-arrow-right"></i>'>
                            Lacak Sekarang <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </form>
                    
                    <div class="mt-8 pt-8 border-t border-slate-200">
                        <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4" data-lang-en="Quick Access" data-lang-zh="快速访问">Akses Cepat</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <a href="{{ route('public.pelabuhan') }}" class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-xl hover:bg-blue-50 transition-colors group">
                                <i class="fa-regular fa-calendar-days text-2xl text-blue-600 mb-2 group-hover:scale-110 transition-transform"></i>
                                <span class="text-sm font-medium text-slate-700 text-center" data-lang-en="Destination Ports" data-lang-zh="目的港">Pelabuhan Tujuan</span>
                            </a>
                            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-xl hover:bg-blue-50 transition-colors group">
                                <i class="fa-solid fa-calculator text-2xl text-blue-600 mb-2 group-hover:scale-110 transition-transform"></i>
                                <span class="text-sm font-medium text-slate-700 text-center" data-lang-en="Check Rates" data-lang-zh="查询资费">Cek Tarif</span>
                            </a>
                            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-xl hover:bg-blue-50 transition-colors group sm:col-span-1 col-span-2">
                                <i class="fa-solid fa-handshake text-2xl text-blue-600 mb-2 group-hover:scale-110 transition-transform"></i>
                                <span class="text-sm font-medium text-slate-700 text-center" data-lang-en="Partner Portal" data-lang-zh="合作伙伴门户">Portal Mitra</span>
                            </a>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Wave Divider -->
        <div class="absolute bottom-0 w-full overflow-hidden leading-none z-0">
            <svg class="relative block w-full h-12 md:h-24" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118,130.98,130.12,201.2,125.79,243.68,123.16,283.42,109.97,321.39,56.44Z" fill="#f8fafc"></path>
            </svg>
        </div>
    </section>

    <!-- Services Section -->
    <section id="layanan" class="py-24 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-blue-600 font-bold tracking-wide uppercase text-sm mb-2" data-lang-en="Main Services" data-lang-zh="主要服务">Layanan Utama</h2>
                <h3 class="text-3xl md:text-5xl font-bold text-slate-900 mb-6" data-lang-en="What Can We Do For You?" data-lang-zh="我们能为您做什么？">Apa yang Dapat Kami Lakukan Untuk Anda?</h3>
                <p class="text-slate-600 text-lg" data-lang-en="End-to-end logistics solutions designed to support your business efficiency across the archipelago." data-lang-zh="旨在支持您在整个群岛的业务效率的端到端物流解决方案。">Solusi logistik end-to-end yang dirancang untuk mendukung efisiensi bisnis Anda di seluruh nusantara.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="service-card bg-white rounded-2xl p-8 border border-slate-100">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-ship text-3xl text-blue-600"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3" data-lang-en="Shipping" data-lang-zh="海运">Shipping</h4>
                    <p class="text-slate-600 leading-relaxed mb-6" data-lang-en="Inter-island sea container shipping services with a reliable fleet and timely departure schedules." data-lang-zh="拥有可靠船队和准时出发时间表的岛际海运集装箱服务。">
                        Layanan pengiriman peti kemas via laut antar pulau dengan armada kapal yang handal dan jadwal keberangkatan yang tepat waktu.
                    </p>
                    <a href="#" class="text-blue-600 font-semibold flex items-center gap-2 hover:text-blue-800 transition-colors" data-lang-en='Learn More <i class="fa-solid fa-arrow-right"></i>' data-lang-zh='了解更多 <i class="fa-solid fa-arrow-right"></i>'>
                        Pelajari Lebih Lanjut <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                
                <!-- Service 2 -->
                <div class="service-card bg-white rounded-2xl p-8 border border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110"></div>
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-truck-fast text-3xl text-blue-600"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3" data-lang-en="Logistics & Trucking" data-lang-zh="物流与卡车运输">Logistics & Trucking</h4>
                    <p class="text-slate-600 leading-relaxed mb-6" data-lang-en="Door-to-door delivery services supported by a well-maintained truck fleet to ensure your cargo arrives safely at its final destination." data-lang-zh="由维护良好的卡车车队支持的门到门送货服务，确保您的货物安全抵达最终目的地。">
                        Layanan pengiriman door-to-door yang didukung oleh armada truk terawat untuk memastikan kargo Anda aman sampai tujuan akhir.
                    </p>
                    <a href="#" class="text-blue-600 font-semibold flex items-center gap-2 hover:text-blue-800 transition-colors" data-lang-en='Learn More <i class="fa-solid fa-arrow-right"></i>' data-lang-zh='了解更多 <i class="fa-solid fa-arrow-right"></i>'>
                        Pelajari Lebih Lanjut <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                
                <!-- Service 3 -->
                <div class="service-card bg-white rounded-2xl p-8 border border-slate-100">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-warehouse text-3xl text-blue-600"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3" data-lang-en="Depot & Storage" data-lang-zh="堆场与仓储">Depot & Storage</h4>
                    <p class="text-slate-600 leading-relaxed mb-6" data-lang-en="Safe, spacious container stacking and storage facilities equipped with a computerized management system." data-lang-zh="配备计算机化管理系统的安全、宽敞的集装箱堆放和存储设施。">
                        Fasilitas penumpukan dan penyimpanan peti kemas yang aman, luas, dan dilengkapi dengan sistem manajemen yang terkomputerisasi.
                    </p>
                    <a href="#" class="text-blue-600 font-semibold flex items-center gap-2 hover:text-blue-800 transition-colors" data-lang-en='Learn More <i class="fa-solid fa-arrow-right"></i>' data-lang-zh='了解更多 <i class="fa-solid fa-arrow-right"></i>'>
                        Pelajari Lebih Lanjut <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Routes & Schedule Section -->
    <section id="rute" class="py-24 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-blue-600 font-bold tracking-wide uppercase text-sm mb-2" data-lang-en="Route Network" data-lang-zh="航线网络">Jaringan Rute</h2>
                    <h3 class="text-3xl md:text-5xl font-bold text-slate-900 mb-6" data-lang-en="Explore Our Shipping Routes" data-lang-zh="探索我们的运输航线">Jelajahi Rute Pengiriman Kami</h3>
                    <p class="text-slate-600 text-lg mb-8 leading-relaxed" data-lang-en="Alexindo Yakinprima continues to innovate to support the national economy. We serve various strategic routes to meet your goods distribution needs to all corners of Indonesia." data-lang-zh="Alexindo Yakinprima 不断创新以支持国民经济。我们提供各种战略航线，以满足您在印尼各个角落的货物配送需求。">
                        Alexindo Yakinprima terus berinovasi untuk mendukung perekonomian nasional. Kami melayani berbagai rute strategis untuk memenuhi kebutuhan distribusi barang Anda ke seluruh pelosok Indonesia.
                    </p>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3 text-slate-700">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span class="font-medium" data-lang-en="Regular and scheduled routes" data-lang-zh="定期和计划航线">Rute reguler dan terjadwal</span>
                        </li>
                        <li class="flex items-center gap-3 text-slate-700">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span class="font-medium" data-lang-en="Coverage from Western to Eastern Indonesia" data-lang-zh="覆盖印尼西部到东部">Cakupan wilayah dari Barat hingga Timur Indonesia</span>
                        </li>
                        <li class="flex items-center gap-3 text-slate-700">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span class="font-medium" data-lang-en="Priority services for specific commodities" data-lang-zh="特定商品的优先服务">Layanan prioritas untuk komoditas tertentu</span>
                        </li>
                    </ul>
                    
                    <a href="{{ route('public.pelabuhan') }}" class="btn-primary inline-flex items-center gap-2 px-8 py-3.5 rounded-full font-semibold text-white" data-lang-en='View All Routes <i class="fa-solid fa-map-location-dot"></i>' data-lang-zh='查看所有航线 <i class="fa-solid fa-map-location-dot"></i>'>
                        Lihat Semua Rute <i class="fa-solid fa-map-location-dot"></i>
                    </a>
                </div>
                
                <div class="relative">
                    <div class="absolute inset-0 bg-blue-600 rounded-3xl transform rotate-3 scale-105 opacity-10"></div>
                    <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Shipping Container Port" class="rounded-3xl shadow-2xl relative z-10 w-full object-cover h-[500px]">
                    
                    <!-- Floating Stats -->
                    <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-2xl shadow-xl z-20 border border-slate-100 animate-bounce" style="animation-duration: 3s;">
                        <div class="flex items-center gap-4">
                            <div class="bg-blue-100 text-blue-600 p-3 rounded-xl">
                                <i class="fa-solid fa-anchor text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-slate-900">20+</p>
                                <p class="text-slate-500 font-medium text-sm" data-lang-en="Destination Ports" data-lang-zh="目的港">Pelabuhan Tujuan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mitra Section -->
    <section id="mitra" class="py-24 bg-slate-50 relative border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-blue-600 font-bold tracking-wide uppercase text-sm mb-2" data-lang-en="Our Partners" data-lang-zh="我们的合作伙伴">Mitra Kami</h2>
                <h3 class="text-3xl md:text-5xl font-bold text-slate-900 mb-6" data-lang-en="Join and Grow Together" data-lang-zh="加入并共同成长">Bergabung dan Berkembang Bersama</h3>
                <p class="text-slate-600 text-lg" data-lang-en="Enjoy convenience, transparency, and full support by joining as an official partner of PT Alexindo Yakinprima." data-lang-zh="作为 PT Alexindo Yakinprima 的官方合作伙伴，享受便利、透明和全面支持。">Nikmati kemudahan, transparansi, dan dukungan penuh dengan bergabung sebagai mitra resmi PT Alexindo Yakinprima.</p>
            </div>
            
            <!-- Partner Logos Marquee -->
            <div class="overflow-hidden w-full relative py-4" id="logo-marquee-wrapper" style="-webkit-mask-image: linear-gradient(to right, transparent, black 20%, black 80%, transparent); mask-image: linear-gradient(to right, transparent, black 20%, black 80%, transparent);">
                <div class="flex flex-nowrap items-center w-max" id="logo-marquee-track">
                    <!-- Base Set of Logos -->
                    <div class="relative flex items-center justify-center shrink-0 px-4 mr-16 lg:mr-24">
                        <img src="{{ asset('images/mayora_logo.png') }}" alt="Mayora" class="h-32 md:h-48 w-auto max-w-none object-contain transition-transform duration-300 hover:scale-110">
                    </div>
                    <div class="relative flex items-center justify-center shrink-0 px-4 mr-16 lg:mr-24">
                        <img src="{{ asset('images/aqua_logo.png') }}" alt="Aqua" class="h-24 md:h-36 w-auto max-w-none object-contain transition-transform duration-300 hover:scale-110">
                    </div>
                    <div class="relative flex items-center justify-center shrink-0 px-4 mr-16 lg:mr-24">
                        <img src="{{ asset('images/indofood_logo.png') }}" alt="Indofood" class="h-32 md:h-48 w-auto max-w-none object-contain transition-transform duration-300 hover:scale-110">
                    </div>
                    <div class="relative flex items-center justify-center shrink-0 px-4 mr-16 lg:mr-24">
                        <img src="{{ asset('images/garudafood_logo.png') }}" alt="Garudafood" class="h-32 md:h-48 w-auto max-w-none object-contain transition-transform duration-300 hover:scale-110">
                    </div>
                    <div class="relative flex items-center justify-center shrink-0 px-4 mr-16 lg:mr-24">
                        <img src="{{ asset('images/mulia_logo.png') }}" alt="Mulia Ceramics" class="h-24 md:h-36 w-auto max-w-none object-contain transition-transform duration-300 hover:scale-110">
                    </div>
                    <div class="relative flex items-center justify-center shrink-0 px-4 mr-16 lg:mr-24">
                        <img src="{{ asset('images/ot_logo.png') }}" alt="OT Group" class="h-24 md:h-36 w-auto max-w-none object-contain transition-transform duration-300 hover:scale-110">
                    </div>
                    <div class="relative flex items-center justify-center shrink-0 px-4 mr-16 lg:mr-24">
                        <img src="{{ asset('images/wavin_logo.png') }}" alt="Wavin" class="h-32 md:h-48 w-auto max-w-none object-contain transition-transform duration-300 hover:scale-110">
                    </div>
                    <div class="relative flex items-center justify-center shrink-0 px-4 mr-16 lg:mr-24">
                        <img src="{{ asset('images/indah_kiat_logo.png') }}" alt="Indah Kiat" class="h-24 md:h-36 w-auto max-w-none object-contain transition-transform duration-300 hover:scale-110">
                    </div>
                    <div class="relative flex items-center justify-center shrink-0 px-4 mr-16 lg:mr-24">
                        <img src="{{ asset('images/teh_pucuk_logo.png') }}" alt="Teh Pucuk" class="h-24 md:h-36 w-auto max-w-none object-contain transition-transform duration-300 hover:scale-110">
                    </div>
                </div>
            </div>

            <script>
                // Use window load instead of DOMContentLoaded so images are fully loaded before calculating width
                window.addEventListener("load", function() {
                    const track = document.getElementById('logo-marquee-track');
                    
                    // Duplicate the content multiple times to ensure it covers wide screens
                    const originalHTML = track.innerHTML;
                    // We need enough duplicates to fill the screen and allow seamless scrolling.
                    track.innerHTML = originalHTML + originalHTML + originalHTML + originalHTML + originalHTML; 
                    
                    // Calculate exact pixel width of ONE set. 
                    // We duplicated it so there are 5 identical sets total.
                    let setWidth = track.scrollWidth / 5;
                    
                    // Start position: shifted left by 2 sets so we have plenty of content on both sides
                    let pos = -(setWidth * 2); 
                    const speed = 1.0; // Pixel step per frame (adjust for speed)
                    
                    function animateMarquee() {
                        pos += speed; // Move right
                        
                        // If we have moved right by exactly one full set width
                        if (pos >= -setWidth) {
                            pos -= setWidth; // Seamlessly jump back exactly one set width to the left
                        }
                        
                        track.style.transform = `translateX(${pos}px)`;
                        requestAnimationFrame(animateMarquee);
                    }
                    
                    // Recalculate width on window resize to ensure responsiveness
                    window.addEventListener('resize', function() {
                        setWidth = track.scrollWidth / 5;
                        // Don't reset position completely to avoid jumping during resize, just ensure it's valid
                        if (pos > -setWidth) pos -= setWidth;
                        if (pos < -(setWidth * 3)) pos += setWidth;
                    });
                    
                    // Start animation
                    animateMarquee();
                });
            </script>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-20 bg-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="max-w-4xl mx-auto px-4 relative z-10 text-center">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6" data-lang-en="Ready to Start Shipping?" data-lang-zh="准备好开始运输了吗？">Siap Untuk Memulai Pengiriman?</h2>
            <p class="text-blue-100 text-lg mb-10 max-w-2xl mx-auto" data-lang-en="Log in to our Customer Portal to easily make bookings, track shipments, and manage invoices." data-lang-zh="登录我们的客户门户，轻松进行预订、追踪货运和管理发票。">Masuk ke Portal Customer kami untuk melakukan booking, melacak pengiriman, dan mengelola tagihan dengan mudah.</p>
            <a href="{{ route('login') }}" class="inline-flex justify-center items-center gap-2 bg-white text-blue-900 px-10 py-4 rounded-full font-bold text-lg hover:bg-blue-50 hover:scale-105 transition-all shadow-[0_0_20px_rgba(255,255,255,0.3)]" data-lang-en='Login to System <i class="fa-solid fa-arrow-right-to-bracket"></i>' data-lang-zh='登录系统 <i class="fa-solid fa-arrow-right-to-bracket"></i>'>
                Login ke Sistem <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </a>
        </div>
    </section>

    <!-- Chatbox Feature -->
    <div id="chatbox-container" class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
        <!-- Chat Window (Hidden by default) -->
        <div id="chatbox-window" class="bg-white w-80 sm:w-96 rounded-2xl shadow-2xl border border-slate-100 overflow-hidden mb-4 transition-all duration-300 transform origin-bottom-right scale-0 opacity-0 pointer-events-none">
            <!-- Header -->
            <div class="bg-blue-600 text-white p-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm" data-lang-en="Customer Support" data-lang-zh="客户支持">Customer Support</h4>
                        <p class="text-xs text-blue-100" data-lang-en="Online" data-lang-zh="在线">Online</p>
                    </div>
                </div>
                <button id="close-chat" class="text-blue-100 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <!-- Chat Area -->
            <div class="h-64 p-4 overflow-y-auto bg-slate-50 flex flex-col gap-3">
                <!-- Welcome Message -->
                <div class="flex items-start gap-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-robot text-blue-600 text-xs"></i>
                    </div>
                    <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 text-sm text-slate-700">
                        <p data-lang-en="Hello! I'm your virtual assistant. How can I help you today?" data-lang-zh="你好！我是虚拟助手。今天有什么我可以帮您的吗？">Halo! Saya asisten virtual Anda. Ada yang bisa saya bantu hari ini?</p>
                    </div>
                </div>
            </div>
            
            <!-- Input Area -->
            <div id="faq-chips-container" class="px-3 pt-2 pb-1 bg-white flex overflow-x-auto gap-2 hidden border-t border-slate-100 no-scrollbar whitespace-nowrap"></div>
            <div class="p-3 bg-white">
                <form class="flex items-center gap-2" onsubmit="event.preventDefault();">
                    <input type="text" class="w-full px-4 py-2 border border-slate-200 rounded-full text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-slate-50" placeholder="Ketik pesan Anda..." data-lang-en-placeholder="Type your message..." data-lang-zh-placeholder="输入您的信息...">
                    <button type="submit" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center flex-shrink-0 hover:bg-blue-700 transition-colors shadow-sm">
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Toggle Button -->
        <button id="toggle-chat" class="w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 hover:scale-110 transition-all duration-300 flex items-center justify-center relative group">
            <span class="absolute top-0 right-0 w-3.5 h-3.5 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
            <i class="fa-regular fa-comment-dots text-2xl group-hover:scale-110 transition-transform"></i>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatWindow = document.getElementById('chatbox-window');
            const toggleBtn = document.getElementById('toggle-chat');
            const closeBtn = document.getElementById('close-chat');
            const chatForm = chatWindow.querySelector('form');
            const chatInput = chatForm.querySelector('input');
            const chatArea = chatWindow.querySelector('.overflow-y-auto');
            
            let isOpen = false;
            let sessionId = localStorage.getItem('chat_session_id');
            if (!sessionId) {
                sessionId = 'session_' + Math.random().toString(36).substr(2, 9);
                localStorage.setItem('chat_session_id', sessionId);
            }
            let pollInterval = null;
            let faqsLoaded = false;
            
            function loadFaqs() {
                if(faqsLoaded) return;
                fetch('/api/chat/faqs')
                    .then(faqRes => faqRes.json())
                    .then(faqData => {
                        if (faqData.faqs && faqData.faqs.length > 0) {
                            const faqContainer = document.getElementById('faq-chips-container');
                            let faqHtml = '';
                            faqData.faqs.forEach(faq => {
                                faqHtml += `<button onclick="sendFaq(${faq.id}, '${faq.question.replace(/'/g, "\\'")}')" class="bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 text-xs px-3 py-1.5 rounded-full transition-colors flex-shrink-0">${faq.question}</button>`;
                            });
                            faqContainer.innerHTML = faqHtml;
                            faqContainer.classList.remove('hidden');
                        }
                        faqsLoaded = true;
                    })
                    .catch(err => console.error(err));
            }

            function toggleChat() {
                isOpen = !isOpen;
                if (isOpen) {
                    chatWindow.classList.remove('scale-0', 'opacity-0', 'pointer-events-none');
                    chatWindow.classList.add('scale-100', 'opacity-100');
                    toggleBtn.innerHTML = '<i class="fa-solid fa-xmark text-2xl group-hover:scale-110 transition-transform"></i>';
                    
                    // Fetch FAQs
                    loadFaqs();
                    
                    // Fetch existing messages
                    fetchMessages();
                    
                    // Start polling
                    if(pollInterval) clearInterval(pollInterval);
                    pollInterval = setInterval(fetchMessages, 3000);
                } else {
                    chatWindow.classList.remove('scale-100', 'opacity-100');
                    chatWindow.classList.add('scale-0', 'opacity-0', 'pointer-events-none');
                    toggleBtn.innerHTML = '<span class="absolute top-0 right-0 w-3.5 h-3.5 bg-red-500 rounded-full border-2 border-white animate-pulse"></span><i class="fa-regular fa-comment-dots text-2xl group-hover:scale-110 transition-transform"></i>';
                    
                    if(pollInterval) clearInterval(pollInterval);
                }
            }

            toggleBtn.addEventListener('click', toggleChat);
            closeBtn.addEventListener('click', toggleChat);
            
            function renderMessage(msg) {
                const isSelf = msg.is_admin == 0 || msg.is_admin === false; // On landing page, self is NOT admin
                const time = new Date(msg.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                
                if (isSelf) {
                    return `
                        <div class="flex items-end justify-end gap-2">
                            <div class="bg-blue-600 text-white p-3 rounded-2xl rounded-br-none shadow-sm text-sm">
                                <p>${msg.message}</p>
                                <div class="text-[10px] text-blue-200 mt-1 text-right">${time}</div>
                            </div>
                        </div>
                    `;
                } else {
                    return `
                        <div class="flex items-start gap-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-headset text-blue-600 text-xs"></i>
                            </div>
                            <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 text-sm text-slate-700">
                                <p>${msg.message}</p>
                                <div class="text-[10px] text-gray-400 mt-1">${time}</div>
                            </div>
                        </div>
                    `;
                }
            }
            
            function fetchMessages() {
                fetch(`/api/chat/messages?session_id=${sessionId}`)
                    .then(res => res.json())
                    .then(data => {
                        const welcomeHtml = `
                            <div class="flex items-start gap-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-robot text-blue-600 text-xs"></i>
                                </div>
                                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 text-sm text-slate-700">
                                    <p data-lang-en="Hello! I'm your virtual assistant. How can I help you today?" data-lang-zh="你好！我是虚拟助手。今天有什么我可以帮您的吗？">Halo! Saya asisten virtual Anda. Ada yang bisa saya bantu hari ini?</p>
                                </div>
                            </div>
                        `;
                        
                        let msgsHtml = welcomeHtml;
                        
                        if (data.messages && data.messages.length > 0) {
                            data.messages.forEach(msg => {
                                msgsHtml += renderMessage(msg);
                            });
                        }
                        
                        chatArea.innerHTML = msgsHtml;
                        chatArea.scrollTop = chatArea.scrollHeight;
                    })
                    .catch(err => console.error(err));
            }
            
            window.sendFaq = function(faqId, question) {
                // Immediately show optimistic UI
                const tempHtml = renderMessage({
                    is_admin: 0,
                    message: question,
                    created_at: new Date().toISOString()
                });
                chatArea.innerHTML += tempHtml;
                chatArea.scrollTop = chatArea.scrollHeight;
                
                // Send to server
                fetch('/api/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        message: question,
                        name: 'Visitor',
                        is_faq: true,
                        faq_id: faqId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) fetchMessages();
                })
                .catch(err => console.error(err));
            };
            
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const message = chatInput.value.trim();
                if (!message) return;
                
                chatInput.value = '';
                
                // Immediately show optimistic UI
                const tempHtml = renderMessage({
                    is_admin: 0,
                    message: message,
                    created_at: new Date().toISOString()
                });
                chatArea.innerHTML += tempHtml;
                chatArea.scrollTop = chatArea.scrollHeight;
                
                // Send to server
                fetch('/api/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        message: message,
                        name: 'Visitor'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) fetchMessages();
                })
                .catch(err => {
                    console.error(err);
                });
            });
            // Tracking minimize logic
            const toggleTrackingBtn = document.getElementById('toggle-tracking-btn');
            const trackingContent = document.getElementById('tracking-content');
            const trackingChevron = document.getElementById('tracking-chevron');
            const trackingCard = document.getElementById('tracking-card');
            
            if (toggleTrackingBtn && trackingContent) {
                toggleTrackingBtn.addEventListener('click', function() {
                    if (trackingContent.style.maxHeight === '0px') {
                        // Expand
                        trackingContent.style.maxHeight = '1000px';
                        trackingContent.classList.remove('opacity-0');
                        trackingContent.classList.add('opacity-100');
                        trackingChevron.classList.remove('rotate-180');
                        
                        // Restore padding and margin
                        toggleTrackingBtn.classList.remove('mb-0');
                        toggleTrackingBtn.classList.add('mb-6');
                        if (trackingCard) {
                            trackingCard.classList.remove('p-5', 'lg:p-6');
                            trackingCard.classList.add('p-8', 'lg:p-10');
                        }
                    } else {
                        // Collapse
                        trackingContent.style.maxHeight = '0px';
                        trackingContent.classList.remove('opacity-100');
                        trackingContent.classList.add('opacity-0');
                        trackingChevron.classList.add('rotate-180');
                        
                        // Reduce padding and margin
                        toggleTrackingBtn.classList.remove('mb-6');
                        toggleTrackingBtn.classList.add('mb-0');
                        if (trackingCard) {
                            trackingCard.classList.remove('p-8', 'lg:p-10');
                            trackingCard.classList.add('p-5', 'lg:p-6');
                        }
                    }
                });
            }
        });
    </script>
@endsection
