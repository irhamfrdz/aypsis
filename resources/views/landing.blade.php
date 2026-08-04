@extends('layouts.public')

@section('content')
    <!-- Hero Section -->
    <section id="beranda" class="hero-bg min-h-screen flex items-center relative pt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-white space-y-6">
                    <div class="inline-block px-4 py-1.5 rounded-full bg-blue-600/30 border border-blue-400/30 backdrop-blur-sm text-sm font-semibold tracking-wide text-blue-200 uppercase mb-2">
                        Integrator Logistik Terpercaya
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-bold leading-tight">
                        Connecting <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Islands</span>,<br>
                        Delivering <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Trust</span>.
                    </h1>
                    <p class="text-lg lg:text-xl text-slate-300 max-w-lg font-light leading-relaxed">
                        PT Alexindo Yakinprima merupakan partner terbaik Anda untuk pengiriman peti kemas dan layanan logistik terpadu di seluruh Indonesia.
                    </p>
                    <div class="flex flex-wrap gap-4 pt-4">
                        <a href="#layanan" class="btn-primary px-8 py-3.5 rounded-full font-semibold text-white text-lg">
                            Eksplorasi Layanan
                        </a>
                        <a href="#rute" class="px-8 py-3.5 rounded-full font-semibold text-white border border-white/30 hover:bg-white/10 transition duration-300 text-lg backdrop-blur-sm">
                            Lihat Jadwal
                        </a>
                    </div>
                </div>
                
                <!-- Quick Tracking/Schedule Card -->
                <div class="glass-panel rounded-2xl p-8 lg:p-10 transform translate-y-8 lg:translate-y-0">
                    <h3 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                        <i class="fa-solid fa-magnifying-glass text-blue-600"></i> Lacak Pengiriman
                    </h3>
                    <form action="#" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Nomor Resi / Kontainer</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-box text-slate-400"></i>
                                </div>
                                <input type="text" class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 bg-slate-50 text-slate-900 transition-colors" placeholder="Masukkan nomor resi Anda...">
                            </div>
                        </div>
                        <button type="button" class="w-full btn-primary text-white font-bold py-3.5 px-4 rounded-xl flex justify-center items-center gap-2">
                            Lacak Sekarang <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </form>
                    
                    <div class="mt-8 pt-8 border-t border-slate-200">
                        <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Akses Cepat</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('public.pelabuhan') }}" class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-xl hover:bg-blue-50 transition-colors group">
                                <i class="fa-regular fa-calendar-days text-2xl text-blue-600 mb-2 group-hover:scale-110 transition-transform"></i>
                                <span class="text-sm font-medium text-slate-700">Pelabuhan Tujuan</span>
                            </a>
                            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-xl hover:bg-blue-50 transition-colors group">
                                <i class="fa-solid fa-calculator text-2xl text-blue-600 mb-2 group-hover:scale-110 transition-transform"></i>
                                <span class="text-sm font-medium text-slate-700">Cek Tarif</span>
                            </a>
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
                <h2 class="text-blue-600 font-bold tracking-wide uppercase text-sm mb-2">Layanan Utama</h2>
                <h3 class="text-3xl md:text-5xl font-bold text-slate-900 mb-6">Apa yang Dapat Kami Lakukan Untuk Anda?</h3>
                <p class="text-slate-600 text-lg">Solusi logistik end-to-end yang dirancang untuk mendukung efisiensi bisnis Anda di seluruh nusantara.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="service-card bg-white rounded-2xl p-8 border border-slate-100">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-ship text-3xl text-blue-600"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Shipping</h4>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Layanan pengiriman peti kemas via laut antar pulau dengan armada kapal yang handal dan jadwal keberangkatan yang tepat waktu.
                    </p>
                    <a href="#" class="text-blue-600 font-semibold flex items-center gap-2 hover:text-blue-800 transition-colors">
                        Pelajari Lebih Lanjut <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                
                <!-- Service 2 -->
                <div class="service-card bg-white rounded-2xl p-8 border border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110"></div>
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-truck-fast text-3xl text-blue-600"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Logistics & Trucking</h4>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Layanan pengiriman door-to-door yang didukung oleh armada truk terawat untuk memastikan kargo Anda aman sampai tujuan akhir.
                    </p>
                    <a href="#" class="text-blue-600 font-semibold flex items-center gap-2 hover:text-blue-800 transition-colors">
                        Pelajari Lebih Lanjut <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                
                <!-- Service 3 -->
                <div class="service-card bg-white rounded-2xl p-8 border border-slate-100">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fa-solid fa-warehouse text-3xl text-blue-600"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Depot & Storage</h4>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        Fasilitas penumpukan dan penyimpanan peti kemas yang aman, luas, dan dilengkapi dengan sistem manajemen yang terkomputerisasi.
                    </p>
                    <a href="#" class="text-blue-600 font-semibold flex items-center gap-2 hover:text-blue-800 transition-colors">
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
                    <h2 class="text-blue-600 font-bold tracking-wide uppercase text-sm mb-2">Jaringan Rute</h2>
                    <h3 class="text-3xl md:text-5xl font-bold text-slate-900 mb-6">Jelajahi Rute Pengiriman Kami</h3>
                    <p class="text-slate-600 text-lg mb-8 leading-relaxed">
                        Alexindo Yakinprima terus berinovasi untuk mendukung perekonomian nasional. Kami melayani berbagai rute strategis untuk memenuhi kebutuhan distribusi barang Anda ke seluruh pelosok Indonesia.
                    </p>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3 text-slate-700">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span class="font-medium">Rute reguler dan terjadwal</span>
                        </li>
                        <li class="flex items-center gap-3 text-slate-700">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span class="font-medium">Cakupan wilayah dari Barat hingga Timur Indonesia</span>
                        </li>
                        <li class="flex items-center gap-3 text-slate-700">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span class="font-medium">Layanan prioritas untuk komoditas tertentu</span>
                        </li>
                    </ul>
                    
                    <a href="{{ route('public.pelabuhan') }}" class="btn-primary inline-flex items-center gap-2 px-8 py-3.5 rounded-full font-semibold text-white">
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
                                <p class="text-slate-500 font-medium text-sm">Pelabuhan Tujuan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-20 bg-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="max-w-4xl mx-auto px-4 relative z-10 text-center">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Siap Untuk Memulai Pengiriman?</h2>
            <p class="text-blue-100 text-lg mb-10 max-w-2xl mx-auto">Masuk ke Portal Customer kami untuk melakukan booking, melacak pengiriman, dan mengelola tagihan dengan mudah.</p>
            <a href="{{ route('login') }}" class="inline-flex justify-center items-center gap-2 bg-white text-blue-900 px-10 py-4 rounded-full font-bold text-lg hover:bg-blue-50 hover:scale-105 transition-all shadow-[0_0_20px_rgba(255,255,255,0.3)]">
                Login ke Sistem <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </a>
        </div>
    </section>
@endsection
