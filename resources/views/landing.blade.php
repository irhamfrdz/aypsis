@extends('layouts.public')

@section('additional_styles')
/* ─── Custom Variables ─── */
:root {
    --navy: #0a1628;
    --navy-mid: #0f2044;
    --blue-primary: #1a56db;
    --blue-light: #3b82f6;
    --blue-accent: #38bdf8;
}

/* ─── Hero ─── */
.hero-section {
    background: linear-gradient(135deg, rgba(10,22,40,0.96) 0%, rgba(15,32,68,0.92) 45%, rgba(10,22,40,0.97) 100%),
                url('https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    position: relative;
    overflow: hidden;
}
.hero-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 60% 40%, rgba(26,86,219,0.18) 0%, transparent 70%);
    pointer-events: none;
}
.hero-grid-overlay {
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(56,189,248,0.04) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(56,189,248,0.04) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
}

/* ─── Section Eyebrow ─── */
.section-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #1a56db;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border: 1px solid #bfdbfe;
    padding: 6px 14px;
    border-radius: 100px;
    margin-bottom: 16px;
}
.section-eyebrow::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #1a56db;
    flex-shrink: 0;
}

/* ─── Service Cards ─── */
.svc-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 32px 28px;
    transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
    position: relative;
    overflow: hidden;
}
.svc-card::before {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #1a56db, #38bdf8);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.4s ease;
}
.svc-card:hover {
    transform: translateY(-8px);
    border-color: #93c5fd;
    box-shadow: 0 24px 48px -12px rgba(26,86,219,0.15);
}
.svc-card:hover::before { transform: scaleX(1); }
.svc-icon {
    width: 56px; height: 56px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
    transition: all 0.35s ease;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    color: #1a56db;
    margin-bottom: 20px;
}
.svc-card:hover .svc-icon {
    background: linear-gradient(135deg, #1a56db, #3b82f6);
    color: #fff;
    box-shadow: 0 8px 24px rgba(26,86,219,0.35);
}

/* ─── Stat Cards ─── */
.stat-card {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 16px;
    padding: 28px 24px;
    text-align: center;
    transition: all 0.3s ease;
    backdrop-filter: blur(8px);
}
.stat-card:hover {
    background: rgba(255,255,255,0.1);
    border-color: rgba(56,189,248,0.4);
    transform: translateY(-4px);
}

/* ─── Pillar Cards ─── */
.pillar-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 28px 24px;
    transition: all 0.3s ease;
}
.pillar-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 12px 32px -8px rgba(26,86,219,0.12);
    transform: translateY(-3px);
}
.pillar-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    color: #1a56db;
    margin-bottom: 16px;
    transition: all 0.3s ease;
}
.pillar-card:hover .pillar-icon {
    background: linear-gradient(135deg, #1a56db, #3b82f6);
    color: #fff;
}

/* ─── Partner Logo ─── */
.partner-logo {
    filter: grayscale(100%) opacity(0.55);
    transition: all 0.3s ease;
}
.partner-logo:hover {
    filter: grayscale(0%) opacity(1);
    transform: scale(1.08);
}

/* ─── CTA Section ─── */
.cta-section {
    background: linear-gradient(135deg, #0a1628 0%, #0f2044 50%, #0a1628 100%);
    position: relative;
    overflow: hidden;
}
.cta-section::before {
    content: '';
    position: absolute;
    top: -50%; left: -50%;
    width: 200%; height: 200%;
    background: radial-gradient(ellipse 50% 50% at 50% 50%, rgba(26,86,219,0.2) 0%, transparent 70%);
    pointer-events: none;
}

/* ─── Dot Pattern ─── */
.dot-pattern {
    background-image: radial-gradient(circle, rgba(56,189,248,0.15) 1px, transparent 1px);
    background-size: 24px 24px;
}

/* ─── Testimonial Card ─── */
.testimonial-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 32px;
    transition: all 0.3s ease;
    position: relative;
}
.testimonial-card::before {
    content: '\201C';
    position: absolute;
    top: 16px; left: 24px;
    font-size: 72px;
    line-height: 1;
    color: #dbeafe;
    font-family: Georgia, serif;
}
.testimonial-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 16px 40px -8px rgba(26,86,219,0.12);
    transform: translateY(-4px);
}

/* ─── Glass Panel ─── */
.glass-panel {
    background: rgba(255,255,255,0.97);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.8);
    box-shadow: 0 32px 64px -20px rgba(10,22,40,0.25);
}

/* ─── About Image Frame ─── */
.about-img-frame {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
}
.about-img-frame::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(26,86,219,0.15), transparent 60%);
    pointer-events: none;
}
.about-badge {
    position: absolute;
    background: #fff;
    border-radius: 16px;
    padding: 14px 20px;
    box-shadow: 0 12px 32px rgba(0,0,0,0.12);
    border: 1px solid #e2e8f0;
    display: flex; align-items: center; gap: 12px;
}

/* ─── Badge Pulse ─── */
@keyframes badgePulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(26,86,219,0.4); }
    50% { box-shadow: 0 0 0 6px rgba(26,86,219,0); }
}
.badge-pulse { animation: badgePulse 2.5s ease infinite; }

/* ─── Map ─── */
#map-route { width: 100%; height: 100%; }

/* ─── Service card link full width ─── */
.svc-link-row { display: flex; align-items: center; }
@endsection

@section('content')

{{-- ═══════════════════════════════════ HERO ═══════════════════════════════════ --}}
<section id="beranda" class="hero-section min-h-screen flex items-center relative pt-28 pb-20 lg:pt-36 lg:pb-24">
    <div class="hero-grid-overlay"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">

            {{-- LEFT --}}
            <div class="text-white space-y-7 lg:col-span-7" data-aos="fade-right" data-aos-duration="800">
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-white/10 border border-white/20 backdrop-blur-md text-xs font-semibold tracking-widest text-sky-300 uppercase badge-pulse"
                     data-lang-en="Trusted Logistics Integrator" data-lang-zh="值得信赖的物流集成商">
                    <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse flex-shrink-0"></span>
                    <span>Integrator Logistik Terpercaya — Sejak 2005</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.12] text-white">
                    Solusi Pengiriman<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-300 via-blue-200 to-teal-300">Kontainer &amp; Maritim</span><br>
                    <span class="text-white/90">Terpadu Nusantara</span>
                </h1>

                <p class="text-base sm:text-lg text-slate-300 max-w-xl leading-relaxed"
                   data-lang-en="PT Alexindo Yakinprima is your reliable partner for container shipping and integrated logistics services across the Indonesian archipelago."
                   data-lang-zh="PT Alexindo Yakinprima 是您在全印尼集装箱运输和综合物流服务的最佳合作伙伴。">
                    PT Alexindo Yakinprima adalah mitra terpercaya Anda untuk pengiriman peti kemas dan layanan logistik maritim terpadu di seluruh kepulauan Indonesia.
                </p>

                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="#layanan" class="btn-primary px-8 py-3.5 rounded-full font-semibold text-white text-base shadow-lg shadow-blue-900/40 inline-flex items-center gap-2"
                       data-lang-en='Explore Services <i class="fa-solid fa-arrow-right text-xs"></i>'
                       data-lang-zh='探索服务 <i class="fa-solid fa-arrow-right text-xs"></i>'>
                        Eksplorasi Layanan <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                    <a href="#tentang" class="px-8 py-3.5 rounded-full font-semibold text-white border border-white/25 hover:bg-white/10 transition-all duration-300 text-base backdrop-blur-sm inline-flex items-center gap-2">
                        Tentang Kami <i class="fa-solid fa-circle-info text-xs"></i>
                    </a>
                </div>

                <div class="pt-6 border-t border-white/10 grid grid-cols-3 gap-6 max-w-md">
                    <div>
                        <p class="text-3xl lg:text-4xl font-black text-white">20+</p>
                        <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Pelabuhan Aktif</p>
                    </div>
                    <div>
                        <p class="text-3xl lg:text-4xl font-black text-white">100%</p>
                        <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Rute Terjadwal</p>
                    </div>
                    <div>
                        <p class="text-3xl lg:text-4xl font-black text-white">24/7</p>
                        <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Support Operasi</p>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Tracking Card --}}
            <div class="lg:col-span-5" data-aos="fade-left" data-aos-duration="900" data-aos-delay="100">
                <div class="glass-panel rounded-3xl p-7 sm:p-8" id="tracking-card">
                    <div class="flex items-center justify-between pb-5 border-b border-slate-200 cursor-pointer group select-none mb-6" id="toggle-tracking-btn">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Layanan Mandiri</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2 mt-0.5">
                                <i class="fa-solid fa-satellite-dish text-blue-600"></i> Lacak Pengiriman
                            </h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live
                            </span>
                            <button type="button" class="w-8 h-8 rounded-full bg-slate-100 group-hover:bg-blue-50 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition-colors" aria-label="Toggle Card">
                                <i class="fa-solid fa-chevron-up text-xs transition-transform duration-300" id="tracking-chevron"></i>
                            </button>
                        </div>
                    </div>

                    <div id="tracking-content" class="transition-all duration-500 ease-in-out overflow-hidden opacity-100" style="max-height:1000px;">
                        <form action="{{ route('login') }}" method="GET" class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Nomor Resi / Kontainer / B/L</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa-solid fa-box-open"></i></div>
                                    <input type="text" id="quick-tracking-input"
                                           class="block w-full pl-11 pr-4 py-3.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50 text-slate-900 text-sm font-medium transition-all placeholder:text-slate-400"
                                           placeholder="Contoh: AYP-2026-00129"
                                           data-lang-en-placeholder="e.g. AYP-2026-00129"
                                           data-lang-zh-placeholder="例如: AYP-2026-00129">
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-info text-[10px]"></i> Masukkan nomor resi, segel, atau B/L untuk status terkini.
                                </p>
                            </div>
                            <button type="submit" class="w-full btn-primary text-white font-semibold py-3.5 px-5 rounded-xl flex justify-center items-center gap-2 text-sm shadow-md">
                                Lacak Sekarang <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>
                        </form>

                        <div class="mt-6 pt-5 border-t border-slate-200">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider" data-lang-en="Quick Access" data-lang-zh="快速访问">Akses Cepat</h4>
                                <span class="text-[11px] text-blue-600 font-medium">Navigasi Langsung</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ route('public.pelabuhan') }}" class="flex flex-col items-center justify-center p-3.5 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-xl transition-all duration-200 group text-center">
                                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform"><i class="fa-solid fa-anchor text-sm"></i></div>
                                    <span class="text-xs font-semibold text-slate-700 group-hover:text-blue-700" data-lang-en="Destination Ports" data-lang-zh="目的港">Pelabuhan Tujuan</span>
                                </a>
                                <a href="{{ route('login') }}" class="flex flex-col items-center justify-center p-3.5 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-xl transition-all duration-200 group text-center">
                                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform"><i class="fa-solid fa-calculator text-sm"></i></div>
                                    <span class="text-xs font-semibold text-slate-700 group-hover:text-blue-700" data-lang-en="Check Rates" data-lang-zh="查询资费">Cek Tarif</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-2 mt-5 text-white/40 text-xs">
                    <i class="fa-solid fa-chevron-down animate-bounce text-[10px]"></i>
                    <span>Scroll untuk menjelajahi lebih lanjut</span>
                    <i class="fa-solid fa-chevron-down animate-bounce text-[10px]"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="absolute bottom-0 inset-x-0 h-16 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
</section>


{{-- ═══════════════════════════════ ABOUT / COMPANY PROFILE ════════════════════ --}}
<section id="tentang" class="py-24 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            <div class="relative" data-aos="fade-right" data-aos-duration="800">
                <div class="about-img-frame shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1605745341112-85968b19335b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                         alt="PT Alexindo Yakinprima - Operasional Pelabuhan"
                         class="w-full h-[480px] object-cover rounded-3xl">
                </div>

                <div class="about-badge" style="bottom:24px;left:24px;">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-ship text-white text-base"></i>
                    </div>
                    <div>
                        <p class="text-xl font-black text-slate-900 leading-tight">6 Armada</p>
                        <p class="text-xs text-slate-500">Kapal Aktif Beroperasi</p>
                    </div>
                </div>

                <div class="about-badge" style="top:24px;right:24px;">
                    <div class="w-10 h-10 rounded-xl bg-amber-400 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-star text-white text-base"></i>
                    </div>
                    <div>
                        <p class="text-xl font-black text-slate-900 leading-tight">20+ Tahun</p>
                        <p class="text-xs text-slate-500">Pengalaman Industri</p>
                    </div>
                </div>

                <div class="absolute -z-10 -bottom-6 -right-6 w-72 h-72 rounded-full border-2 border-blue-100 opacity-60"></div>
                <div class="absolute -z-10 -bottom-10 -right-10 w-96 h-96 rounded-full border border-blue-50 opacity-40"></div>
            </div>

            <div class="space-y-6" data-aos="fade-left" data-aos-duration="800" data-aos-delay="100">
                <span class="section-eyebrow">Tentang Perusahaan</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Menjadi Tulang Punggung
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-sky-500"> Logistik Maritim</span> Indonesia
                </h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    <strong class="text-slate-800">PT Alexindo Yakinprima (AYPSIS)</strong> adalah perusahaan logistik maritim terkemuka yang berbasis di Jakarta. Kami mengkhususkan diri dalam layanan pengiriman peti kemas dan logistik terpadu untuk mendukung distribusi barang di seluruh kepulauan Indonesia.
                </p>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Dengan pengalaman lebih dari dua dekade, kami telah melayani ratusan mitra korporat terkemuka — dari industri FMCG, manufaktur, hingga konstruksi — dengan standar keamanan dan ketepatan waktu tertinggi.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 mt-0.5"><i class="fa-solid fa-shield-halved"></i></div>
                        <div><h4 class="font-bold text-slate-800 text-sm">Integritas &amp; Transparansi</h4><p class="text-xs text-slate-500 mt-0.5">Pelaporan real-time &amp; dokumen terverifikasi</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 mt-0.5"><i class="fa-solid fa-bolt"></i></div>
                        <div><h4 class="font-bold text-slate-800 text-sm">Kecepatan &amp; Efisiensi</h4><p class="text-xs text-slate-500 mt-0.5">Proses cepat dengan teknologi digital modern</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5"><i class="fa-solid fa-earth-asia"></i></div>
                        <div><h4 class="font-bold text-slate-800 text-sm">Jangkauan Nasional</h4><p class="text-xs text-slate-500 mt-0.5">Melayani seluruh wilayah kepulauan Indonesia</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 mt-0.5"><i class="fa-solid fa-headset"></i></div>
                        <div><h4 class="font-bold text-slate-800 text-sm">Dukungan 24/7</h4><p class="text-xs text-slate-500 mt-0.5">Tim operasional berpengalaman siap setiap saat</p></div>
                    </div>
                </div>

                <div class="pt-4">
                    <a href="#layanan" class="btn-primary inline-flex items-center gap-2 px-8 py-3.5 rounded-full font-semibold text-white text-sm">
                        Lihat Layanan Kami <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════ STATS / NUMBERS ════════════════════════════ --}}
<section class="py-20 relative overflow-hidden" style="background: linear-gradient(135deg, #0a1628 0%, #0f2044 50%, #0a1628 100%);">
    <div class="dot-pattern absolute inset-0 opacity-30 pointer-events-none"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 rounded-full blur-3xl pointer-events-none" style="width:600px;height:300px;background:rgba(37,99,235,0.15);"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full border text-xs font-bold tracking-widest uppercase mb-4"
                  style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.15);color:#7dd3fc;">
                <i class="fa-solid fa-chart-line"></i> Angka Yang Bicara
            </span>
            <h2 class="text-3xl md:text-4xl font-extrabold" style="color:#ffffff;">Rekam Jejak Kami dalam Angka</h2>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="stat-card" data-aos="fade-up" data-aos-delay="0">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl"
                     style="background:rgba(14,165,233,0.2);color:#7dd3fc;"><i class="fa-solid fa-anchor"></i></div>
                <p class="text-3xl sm:text-4xl font-black mb-1" style="color:#ffffff;">20+</p>
                <p class="text-sm font-semibold" style="color:#e2e8f0;">Pelabuhan Tujuan</p>
                <p class="text-xs mt-0.5" style="color:#94a3b8;">Aktif beroperasi</p>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="80">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl"
                     style="background:rgba(59,130,246,0.2);color:#93c5fd;"><i class="fa-solid fa-box"></i></div>
                <p class="text-3xl sm:text-4xl font-black mb-1" style="color:#ffffff;">100+</p>
                <p class="text-sm font-semibold" style="color:#e2e8f0;">Kontainer Aktif</p>
                <p class="text-xs mt-0.5" style="color:#94a3b8;">Armada 20ft &amp; 40ft</p>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="160">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl"
                     style="background:rgba(99,102,241,0.2);color:#a5b4fc;"><i class="fa-solid fa-handshake"></i></div>
                <p class="text-3xl sm:text-4xl font-black mb-1" style="color:#ffffff;">500+</p>
                <p class="text-sm font-semibold" style="color:#e2e8f0;">Mitra Korporat</p>
                <p class="text-xs mt-0.5" style="color:#94a3b8;">Klien terpercaya</p>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="240">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl"
                     style="background:rgba(20,184,166,0.2);color:#5eead4;"><i class="fa-solid fa-ship"></i></div>
                <p class="text-3xl sm:text-4xl font-black mb-1" style="color:#ffffff;">6</p>
                <p class="text-sm font-semibold" style="color:#e2e8f0;">Unit Kapal</p>
                <p class="text-xs mt-0.5" style="color:#94a3b8;">Armada beroperasi</p>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════ SERVICES SECTION ═══════════════════════════ --}}
<section id="layanan" class="py-24 bg-slate-50 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="section-eyebrow">Layanan Utama</span>
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight mb-5">
                Solusi Logistik<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-sky-500">Maritim Menyeluruh</span>
            </h2>
            <p class="text-slate-600 text-base sm:text-lg leading-relaxed">End-to-end logistics solutions yang dirancang untuk mendukung efisiensi bisnis Anda di seluruh nusantara.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
            {{-- Sea Freight --}}
            <div class="svc-card" data-aos="fade-up" data-aos-delay="0">
                <div class="flex items-start justify-between mb-4">
                    <div class="svc-icon"><i class="fa-solid fa-ship"></i></div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">Maritim</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-3 leading-tight" data-lang-en="Sea Freight" data-lang-zh="海运">Sea Freight</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-5">Layanan pengiriman kargo via laut antar pulau dengan rute komprehensif untuk mendukung distribusi efisien di seluruh Nusantara.</p>
                <ul class="space-y-1.5 mb-6">
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> FCL &amp; LCL tersedia</li>
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Jadwal mingguan rutin</li>
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Tracking realtime</li>
                </ul>
                <a href="{{ route('public.layanan.sea-freight') }}" class="inline-flex items-center gap-2 text-blue-600 font-semibold text-sm hover:text-blue-800 transition-all group/link pt-4 border-t border-slate-100 w-full" data-lang-en='Learn More <i class="fa-solid fa-arrow-right"></i>' data-lang-zh='了解更多 <i class="fa-solid fa-arrow-right"></i>'>
                    <span>Pelajari Lebih Lanjut</span> <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform ml-auto"></i>
                </a>
            </div>

            {{-- FCL --}}
            <div class="svc-card" data-aos="fade-up" data-aos-delay="80">
                <div class="flex items-start justify-between mb-4">
                    <div class="svc-icon"><i class="fa-solid fa-box"></i></div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">Full Container</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-3 leading-tight" data-lang-en="FCL (Full Container)" data-lang-zh="整箱货 (FCL)">FCL (Full Container Load)</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-5">Solusi sewa kontainer penuh (20ft &amp; 40ft) untuk kargo bervolume besar dengan tingkat keamanan dan privasi yang maksimal.</p>
                <ul class="space-y-1.5 mb-6">
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> 20ft &amp; 40ft tersedia</li>
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Keamanan maksimal</li>
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Efisiensi biaya</li>
                </ul>
                <a href="{{ route('public.layanan.fcl') }}" class="inline-flex items-center gap-2 text-blue-600 font-semibold text-sm hover:text-blue-800 transition-all group/link pt-4 border-t border-slate-100 w-full" data-lang-en='Learn More <i class="fa-solid fa-arrow-right"></i>' data-lang-zh='了解更多 <i class="fa-solid fa-arrow-right"></i>'>
                    <span>Pelajari Lebih Lanjut</span> <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform ml-auto"></i>
                </a>
            </div>

            {{-- LCL --}}
            <div class="svc-card" data-aos="fade-up" data-aos-delay="160">
                <div class="flex items-start justify-between mb-4">
                    <div class="svc-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">Konsolidasi</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-3 leading-tight" data-lang-en="LCL (Less Container)" data-lang-zh="拼箱货 (LCL)">LCL (Less Container Load)</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-5">Opsi pengiriman ekonomis untuk kargo skala kecil (konsolidasi), bayar hanya sesuai ruang peti kemas yang digunakan.</p>
                <ul class="space-y-1.5 mb-6">
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Bayar sesuai volume</li>
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Konsolidasi terjadwal</li>
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Fleksibel &amp; ekonomis</li>
                </ul>
                <a href="{{ route('public.layanan.lcl') }}" class="inline-flex items-center gap-2 text-blue-600 font-semibold text-sm hover:text-blue-800 transition-all group/link pt-4 border-t border-slate-100 w-full" data-lang-en='Learn More <i class="fa-solid fa-arrow-right"></i>' data-lang-zh='了解更多 <i class="fa-solid fa-arrow-right"></i>'>
                    <span>Pelajari Lebih Lanjut</span> <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform ml-auto"></i>
                </a>
            </div>

            {{-- Door-to-Door --}}
            <div class="svc-card" data-aos="fade-up" data-aos-delay="0">
                <div class="flex items-start justify-between mb-4">
                    <div class="svc-icon"><i class="fa-solid fa-house-chimney"></i></div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">End-to-End</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-3 leading-tight" data-lang-en="Door-to-Door" data-lang-zh="门到门">Door-to-Door</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-5">Layanan terpadu dari titik penjemputan awal hingga lokasi tujuan akhir tanpa perlu repot mengurus administrasi antar moda.</p>
                <ul class="space-y-1.5 mb-6">
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Pick-up &amp; delivery</li>
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Tanpa multidokumen</li>
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> One-stop solution</li>
                </ul>
                <a href="{{ route('public.layanan.door-to-door') }}" class="inline-flex items-center gap-2 text-blue-600 font-semibold text-sm hover:text-blue-800 transition-all group/link pt-4 border-t border-slate-100 w-full" data-lang-en='Learn More <i class="fa-solid fa-arrow-right"></i>' data-lang-zh='了解更多 <i class="fa-solid fa-arrow-right"></i>'>
                    <span>Pelajari Lebih Lanjut</span> <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform ml-auto"></i>
                </a>
            </div>

            {{-- Project Cargo --}}
            <div class="svc-card" data-aos="fade-up" data-aos-delay="80">
                <div class="flex items-start justify-between mb-4">
                    <div class="svc-icon"><i class="fa-solid fa-truck-ramp-box"></i></div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-100">Heavy Cargo</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-3 leading-tight" data-lang-en="Project Cargo" data-lang-zh="项目货物">Project Cargo</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-5">Penanganan logistik khusus untuk alat berat, kargo <em>oversized</em>, dan material konstruksi dengan perencanaan rute presisi.</p>
                <ul class="space-y-1.5 mb-6">
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Alat berat &amp; oversized</li>
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Rute terencana presisi</li>
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Tim berpengalaman</li>
                </ul>
                <a href="{{ route('public.layanan.project-cargo') }}" class="inline-flex items-center gap-2 text-blue-600 font-semibold text-sm hover:text-blue-800 transition-all group/link pt-4 border-t border-slate-100 w-full" data-lang-en='Learn More <i class="fa-solid fa-arrow-right"></i>' data-lang-zh='了解更多 <i class="fa-solid fa-arrow-right"></i>'>
                    <span>Pelajari Lebih Lanjut</span> <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform ml-auto"></i>
                </a>
            </div>

            {{-- Inland Transport --}}
            <div class="svc-card" data-aos="fade-up" data-aos-delay="160">
                <div class="flex items-start justify-between mb-4">
                    <div class="svc-icon"><i class="fa-solid fa-truck-fast"></i></div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-orange-600 bg-orange-50 px-2.5 py-1 rounded-lg border border-orange-100">Darat</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-3 leading-tight" data-lang-en="Inland Transport" data-lang-zh="内陆运输">Inland Transportation</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-5">Armada truk angkutan darat modern (trailer &amp; box) yang siap mendistribusikan kargo Anda dengan cakupan wilayah operasional yang luas.</p>
                <ul class="space-y-1.5 mb-6">
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Trailer &amp; box truck</li>
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> Cakupan nasional</li>
                    <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-emerald-500 text-[10px] flex-shrink-0"></i> GPS tracking</li>
                </ul>
                <a href="{{ route('public.layanan.inland-transportation') }}" class="inline-flex items-center gap-2 text-blue-600 font-semibold text-sm hover:text-blue-800 transition-all group/link pt-4 border-t border-slate-100 w-full" data-lang-en='Learn More <i class="fa-solid fa-arrow-right"></i>' data-lang-zh='了解更多 <i class="fa-solid fa-arrow-right"></i>'>
                    <span>Pelajari Lebih Lanjut</span> <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform ml-auto"></i>
                </a>
            </div>
        </div>

        {{-- Featured: Customs & FTZ --}}
        <div class="mt-7 svc-card border-amber-200 hover:border-amber-400" data-aos="fade-up">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                <div class="lg:col-span-8">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-14 h-14 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid fa-stamp"></i></div>
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">Kepabeanan &amp; FTZ</span>
                            <h3 class="text-xl font-bold text-slate-900 mt-0.5" data-lang-en="Customs &amp; FTZ" data-lang-zh="海关与自由贸易区">Customs &amp; FTZ (Free Trade Zone)</h3>
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed max-w-2xl">Layanan PPJK resmi untuk kepengurusan dokumen pabean yang cepat dan terintegrasi sistem EDI, serta optimalisasi fasilitas Batam Free Trade Zone untuk efisiensi biaya impor-ekspor Anda.</p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <span class="text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-full">PPJK Resmi</span>
                        <span class="text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-full">Integrasi EDI</span>
                        <span class="text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-full">Batam FTZ</span>
                        <span class="text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-full">Import/Export</span>
                        <span class="text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-full">Customs Clearance</span>
                    </div>
                </div>
                <div class="lg:col-span-4 flex lg:justify-end">
                    <a href="{{ route('public.layanan.customs-ftz') }}" class="btn-primary inline-flex items-center gap-2 px-7 py-3.5 rounded-xl font-semibold text-white text-sm" data-lang-en='Learn More <i class="fa-solid fa-arrow-right"></i>' data-lang-zh='了解更多 <i class="fa-solid fa-arrow-right"></i>'>
                        Pelajari Layanan FTZ <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════ ROUTES & SCHEDULE ══════════════════════════ --}}
<section id="rute" class="py-24 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-14 items-center">

            <div class="lg:col-span-5 space-y-6" data-aos="fade-right" data-aos-duration="800">
                <span class="section-eyebrow" data-lang-en="Route Network" data-lang-zh="航线网络">Jaringan Rute</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight" data-lang-en="Explore Our Shipping Routes" data-lang-zh="探索我们的运输航线">
                    Jelajahi Rute<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-sky-500">Pengiriman Kami</span>
                </h2>
                <p class="text-slate-500 text-base leading-relaxed">Alexindo Yakinprima terus berinovasi untuk mendukung perekonomian nasional. Kami melayani berbagai rute strategis untuk memenuhi kebutuhan distribusi barang Anda ke seluruh pelosok Indonesia.</p>

                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0"><i class="fa-solid fa-check text-xs"></i></div>
                        <span class="font-medium text-sm text-slate-700" data-lang-en="Regular and scheduled routes" data-lang-zh="定期和计划航线">Rute reguler dan terjadwal secara mingguan</span>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0"><i class="fa-solid fa-check text-xs"></i></div>
                        <span class="font-medium text-sm text-slate-700" data-lang-en="Coverage from Western to Eastern Indonesia" data-lang-zh="覆盖印尼西部到东部">Cakupan strategis wilayah Barat hingga Timur Indonesia</span>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0"><i class="fa-solid fa-check text-xs"></i></div>
                        <span class="font-medium text-sm text-slate-700" data-lang-en="Priority services for specific commodities" data-lang-zh="特定商品的优先服务">Penanganan prioritas untuk muatan industri &amp; komoditas</span>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0"><i class="fa-solid fa-check text-xs"></i></div>
                        <span class="font-medium text-sm text-slate-700">Integrasi dengan moda transportasi darat</span>
                    </div>
                </div>

                <div class="pt-4">
                    <a href="{{ route('public.pelabuhan') }}" class="btn-primary inline-flex items-center gap-2 px-8 py-3.5 rounded-full font-semibold text-white text-sm" data-lang-en='View All Routes <i class="fa-solid fa-map-location-dot"></i>' data-lang-zh='查看所有航线 <i class="fa-solid fa-map-location-dot"></i>'>
                        <span>Lihat Semua Pelabuhan &amp; Rute</span>
                        <i class="fa-solid fa-map-location-dot text-sm"></i>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-7 flex flex-col gap-5" data-aos="fade-left" data-aos-duration="900">
                <div class="bg-slate-900 rounded-3xl p-3 sm:p-4 shadow-2xl border border-slate-800">
                    <div class="flex items-center justify-between px-3 py-2 text-slate-400 text-xs border-b border-slate-800 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                            <span class="ml-2 font-mono text-[11px] text-slate-300">Live Route Simulation</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-[11px] text-emerald-400 font-medium">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>Aktif: Sunda Kelapa ⇄ Batam</span>
                        </div>
                    </div>
                    <div class="rounded-2xl overflow-hidden relative h-[380px] sm:h-[420px] bg-slate-100">
                        <div id="map-route" class="w-full h-full"></div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center sm:items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-anchor text-lg"></i></div>
                        <div class="text-center sm:text-left"><p class="text-xl sm:text-2xl font-black text-slate-900">20+</p><p class="text-xs text-slate-500 font-medium mt-0.5" data-lang-en="Destination Ports" data-lang-zh="目的港">Pelabuhan</p></div>
                    </div>
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center sm:items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-ship text-lg"></i></div>
                        <div class="text-center sm:text-left"><p class="text-xl sm:text-2xl font-black text-slate-900">6</p><p class="text-xs text-slate-500 font-medium mt-0.5" data-lang-en="Ships Fleet" data-lang-zh="船队">Kapal Armada</p></div>
                    </div>
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center sm:items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-box text-lg"></i></div>
                        <div class="text-center sm:text-left"><p class="text-xl sm:text-2xl font-black text-slate-900">100+</p><p class="text-xs text-slate-500 font-medium mt-0.5" data-lang-en="Containers" data-lang-zh="集装箱">Kontainer Aktif</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════ PROCESS / HOW WE WORK ══════════════════════ --}}
<section class="py-24 bg-slate-50 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="section-eyebrow">Cara Kerja</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4">
                Proses Pengiriman yang <span class="text-blue-600">Mudah &amp; Transparan</span>
            </h2>
            <p class="text-slate-500 leading-relaxed">Empat langkah sederhana dari pemesanan hingga barang tiba di tujuan.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 relative">
            <div class="text-center" data-aos="fade-up" data-aos-delay="0">
                <div class="relative inline-flex items-center justify-center mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-200"><i class="fa-solid fa-file-contract text-white text-xl"></i></div>
                    <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-white border-2 border-blue-200 text-blue-600 text-xs font-black flex items-center justify-center shadow-sm">01</span>
                </div>
                <h3 class="font-bold text-slate-900 text-base mb-2">Pengajuan &amp; Booking</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Hubungi tim kami atau login portal untuk melakukan booking pengiriman dan konsultasi tarif.</p>
            </div>

            <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="relative inline-flex items-center justify-center mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200"><i class="fa-solid fa-box-open text-white text-xl"></i></div>
                    <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-white border-2 border-indigo-200 text-indigo-600 text-xs font-black flex items-center justify-center shadow-sm">02</span>
                </div>
                <h3 class="font-bold text-slate-900 text-base mb-2">Penyiapan Kargo</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Kargo disiapkan, dikemas, dan diinspeksi sesuai standar keamanan maritim internasional.</p>
            </div>

            <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="relative inline-flex items-center justify-center mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-sky-600 flex items-center justify-center shadow-lg shadow-sky-200"><i class="fa-solid fa-ship text-white text-xl"></i></div>
                    <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-white border-2 border-sky-200 text-sky-600 text-xs font-black flex items-center justify-center shadow-sm">03</span>
                </div>
                <h3 class="font-bold text-slate-900 text-base mb-2">Pengiriman &amp; Transit</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Kapal beroperasi sesuai jadwal terjadwal dengan sistem tracking realtime 24/7.</p>
            </div>

            <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="relative inline-flex items-center justify-center mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-200"><i class="fa-solid fa-circle-check text-white text-xl"></i></div>
                    <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-white border-2 border-emerald-200 text-emerald-600 text-xs font-black flex items-center justify-center shadow-sm">04</span>
                </div>
                <h3 class="font-bold text-slate-900 text-base mb-2">Tiba &amp; Selesai</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Kargo tiba di pelabuhan tujuan, siap untuk pengambilan atau distribusi inland.</p>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════ WHY CHOOSE US ══════════════════════════════ --}}
<section class="py-24 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            <div data-aos="fade-right">
                <span class="section-eyebrow">Keunggulan Kami</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                    Mengapa Memilih<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-sky-500">Alexindo Yakinprima?</span>
                </h2>
                <p class="text-slate-500 leading-relaxed mb-8">Kami bukan sekadar perusahaan pengiriman. Kami adalah mitra strategis yang berkomitmen penuh terhadap keberhasilan rantai distribusi bisnis Anda.</p>

                <div class="space-y-5">
                    <div>
                        <div class="flex justify-between text-sm font-medium text-slate-700 mb-1.5">
                            <span>On-time Delivery Rate</span><span class="text-blue-600 font-bold">96%</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-blue-400" style="width:96%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm font-medium text-slate-700 mb-1.5">
                            <span>Kepuasan Pelanggan</span><span class="text-emerald-600 font-bold">98%</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400" style="width:98%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm font-medium text-slate-700 mb-1.5">
                            <span>Keamanan Kargo (Zero Loss)</span><span class="text-indigo-600 font-bold">99%</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-indigo-400" style="width:99%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5" data-aos="fade-left" data-aos-delay="100">
                <div class="pillar-card" data-aos="fade-up" data-aos-delay="0">
                    <div class="pillar-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <h3 class="font-bold text-slate-900 text-sm mb-1.5">Keamanan Terjamin</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Pemeriksaan kontainer &amp; penanganan kargo sesuai standar operasional keselamatan tinggi.</p>
                </div>
                <div class="pillar-card" data-aos="fade-up" data-aos-delay="60">
                    <div class="pillar-icon"><i class="fa-solid fa-clock"></i></div>
                    <h3 class="font-bold text-slate-900 text-sm mb-1.5">Jadwal Tepat Waktu</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Rute reguler mingguan terencana memastikan kelancaran rantai distribusi bisnis Anda.</p>
                </div>
                <div class="pillar-card" data-aos="fade-up" data-aos-delay="120">
                    <div class="pillar-icon"><i class="fa-solid fa-satellite-dish"></i></div>
                    <h3 class="font-bold text-slate-900 text-sm mb-1.5">Portal Digital Modern</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Sistem pelacakan manifes &amp; integrasi dokumen pengiriman secara transparan dan akurat.</p>
                </div>
                <div class="pillar-card" data-aos="fade-up" data-aos-delay="180">
                    <div class="pillar-icon"><i class="fa-solid fa-headset"></i></div>
                    <h3 class="font-bold text-slate-900 text-sm mb-1.5">Dukungan Responsif</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Tim operasional berpengalaman siap memberikan solusi kargo terbaik setiap saat.</p>
                </div>
                <div class="pillar-card" data-aos="fade-up" data-aos-delay="240">
                    <div class="pillar-icon"><i class="fa-solid fa-scale-balanced"></i></div>
                    <h3 class="font-bold text-slate-900 text-sm mb-1.5">Harga Kompetitif</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Tarif transparan dan kompetitif tanpa biaya tersembunyi untuk semua layanan.</p>
                </div>
                <div class="pillar-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="pillar-icon"><i class="fa-solid fa-leaf"></i></div>
                    <h3 class="font-bold text-slate-900 text-sm mb-1.5">Operasi Bertanggung Jawab</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Berkomitmen pada praktik bisnis yang bertanggung jawab dan ramah lingkungan.</p>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════ PARTNERS (Mitra) ═══════════════════════════ --}}
<section id="mitra" class="py-24 bg-slate-50 border-t border-slate-100 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14" data-aos="fade-up">
            <span class="section-eyebrow" data-lang-en="Our Partners" data-lang-zh="我们的合作伙伴">Mitra &amp; Rekanan</span>
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight mb-5" data-lang-en="Join and Grow Together" data-lang-zh="加入并共同成长">
                Dipercaya Oleh<br><span class="text-blue-600">Perusahaan Terkemuka</span>
            </h2>
            <p class="text-slate-500 text-base sm:text-lg leading-relaxed" data-lang-en="Enjoy convenience, transparency, and full support by joining as an official partner of PT Alexindo Yakinprima." data-lang-zh="作为 PT Alexindo Yakinprima 的官方合作伙伴，享受便利、透明和全面支持。">
                Nikmati kemudahan, transparansi, dan dukungan penuh dengan bergabung sebagai mitra resmi PT Alexindo Yakinprima.
            </p>
        </div>

        {{-- Logo Marquee --}}
        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm mb-16" data-aos="fade-up">
            <div class="overflow-hidden w-full relative py-2" id="logo-marquee-wrapper"
                 style="-webkit-mask-image:linear-gradient(to right,transparent,black 12%,black 88%,transparent);mask-image:linear-gradient(to right,transparent,black 12%,black 88%,transparent);">
                <div class="flex flex-nowrap items-center w-max" id="logo-marquee-track">
                    <div class="flex items-center justify-center shrink-0 px-10"><img src="{{ asset('images/mayora_logo.png') }}" alt="Mayora" class="partner-logo h-10 sm:h-12 w-auto max-w-[130px] object-contain"></div>
                    <div class="flex items-center justify-center shrink-0 px-10"><img src="{{ asset('images/aqua_logo.png') }}" alt="Aqua" class="partner-logo h-10 sm:h-12 w-auto max-w-[130px] object-contain"></div>
                    <div class="flex items-center justify-center shrink-0 px-10"><img src="{{ asset('images/indofood_logo.png') }}" alt="Indofood" class="partner-logo h-10 sm:h-12 w-auto max-w-[130px] object-contain"></div>
                    <div class="flex items-center justify-center shrink-0 px-10"><img src="{{ asset('images/garudafood_logo.png') }}" alt="Garudafood" class="partner-logo h-10 sm:h-12 w-auto max-w-[130px] object-contain"></div>
                    <div class="flex items-center justify-center shrink-0 px-10"><img src="{{ asset('images/mulia_logo.png') }}" alt="Mulia Ceramics" class="partner-logo h-9 sm:h-11 w-auto max-w-[130px] object-contain"></div>
                    <div class="flex items-center justify-center shrink-0 px-10"><img src="{{ asset('images/ot_logo.png') }}" alt="OT Group" class="partner-logo h-10 sm:h-12 w-auto max-w-[130px] object-contain"></div>
                    <div class="flex items-center justify-center shrink-0 px-10"><img src="{{ asset('images/wavin_logo.png') }}" alt="Wavin" class="partner-logo h-10 sm:h-12 w-auto max-w-[130px] object-contain"></div>
                    <div class="flex items-center justify-center shrink-0 px-10"><img src="{{ asset('images/indah_kiat_logo.png') }}" alt="Indah Kiat" class="partner-logo h-10 sm:h-12 w-auto max-w-[130px] object-contain"></div>
                    <div class="flex items-center justify-center shrink-0 px-10"><img src="{{ asset('images/teh_pucuk_logo.png') }}" alt="Teh Pucuk" class="partner-logo h-10 sm:h-12 w-auto max-w-[130px] object-contain"></div>
                </div>
            </div>
        </div>

        {{-- Testimonials --}}
        <div class="text-center mb-10" data-aos="fade-up">
            <span class="section-eyebrow">Testimoni</span>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-900">Apa Kata Klien Kami</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="testimonial-card" data-aos="fade-up" data-aos-delay="0">
                <div class="flex gap-0.5 mb-4 mt-6">
                    <i class="fa-solid fa-star text-amber-400 text-sm"></i><i class="fa-solid fa-star text-amber-400 text-sm"></i><i class="fa-solid fa-star text-amber-400 text-sm"></i><i class="fa-solid fa-star text-amber-400 text-sm"></i><i class="fa-solid fa-star text-amber-400 text-sm"></i>
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-6 italic">"Alexindo Yakinprima adalah mitra logistik yang sangat andal. Jadwal pengiriman selalu tepat waktu dan tim support sangat responsif 24 jam."</p>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-sky-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">B</div>
                    <div><p class="font-bold text-slate-900 text-sm">Budi Santoso</p><p class="text-xs text-slate-400">Logistics Manager — PT Indofood</p></div>
                </div>
            </div>

            <div class="testimonial-card" data-aos="fade-up" data-aos-delay="80">
                <div class="flex gap-0.5 mb-4 mt-6">
                    <i class="fa-solid fa-star text-amber-400 text-sm"></i><i class="fa-solid fa-star text-amber-400 text-sm"></i><i class="fa-solid fa-star text-amber-400 text-sm"></i><i class="fa-solid fa-star text-amber-400 text-sm"></i><i class="fa-solid fa-star text-amber-400 text-sm"></i>
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-6 italic">"Sistem tracking digital mereka memudahkan kami memantau status kargo secara real-time. Sangat membantu operasional distribusi kami."</p>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">D</div>
                    <div><p class="font-bold text-slate-900 text-sm">Dewi Rahayu</p><p class="text-xs text-slate-400">Supply Chain Director — Mayora Group</p></div>
                </div>
            </div>

            <div class="testimonial-card" data-aos="fade-up" data-aos-delay="160">
                <div class="flex gap-0.5 mb-4 mt-6">
                    <i class="fa-solid fa-star text-amber-400 text-sm"></i><i class="fa-solid fa-star text-amber-400 text-sm"></i><i class="fa-solid fa-star text-amber-400 text-sm"></i><i class="fa-solid fa-star text-amber-400 text-sm"></i><i class="fa-solid fa-star text-amber-400 text-sm"></i>
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-6 italic">"Tarif kompetitif dengan kualitas layanan premium. Door-to-door service mereka sangat menghemat waktu dan biaya operasional kami."</p>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">H</div>
                    <div><p class="font-bold text-slate-900 text-sm">Hendra Wijaya</p><p class="text-xs text-slate-400">Operations Head — OT Group</p></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener("load", function () {
            const track = document.getElementById('logo-marquee-track');
            if (!track) return;
            const originalHTML = track.innerHTML;
            track.innerHTML = originalHTML + originalHTML + originalHTML + originalHTML;
            let setWidth = track.scrollWidth / 4;
            let pos = -(setWidth * 2);
            const speed = 0.7;
            function animateMarquee() {
                pos += speed;
                if (pos >= -setWidth) pos -= setWidth;
                track.style.transform = `translateX(${pos}px)`;
                requestAnimationFrame(animateMarquee);
            }
            window.addEventListener('resize', function () { setWidth = track.scrollWidth / 4; });
            animateMarquee();
        });
    </script>
</section>


{{-- ═══════════════════════════════ CTA SECTION ════════════════════════════════ --}}
<section class="cta-section py-24">
    <div class="dot-pattern absolute inset-0 opacity-20 pointer-events-none"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 relative z-10 text-center" data-aos="fade-up">
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sky-300 text-xs font-bold tracking-widest uppercase mb-6">
            <i class="fa-solid fa-rocket"></i> Portal Pelanggan &amp; Rekanan
        </span>
        <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-5 leading-tight" data-lang-en="Ready to Start Shipping?" data-lang-zh="准备好开始运输了吗？">
            Siap Untuk Memulai<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-300 to-blue-300">Pengiriman Bersama Kami?</span>
        </h2>
        <p class="text-slate-300 text-base sm:text-lg mb-10 max-w-2xl mx-auto leading-relaxed" data-lang-en="Log in to our Customer Portal to easily make bookings, track shipments, and manage invoices." data-lang-zh="登录我们的客户门户，轻松进行预订、追踪货运和管理发票。">
            Masuk ke Portal Customer kami untuk melakukan booking, melacak pengiriman, dan mengelola tagihan dengan mudah dan transparan.
        </p>

        <div class="flex flex-wrap justify-center items-center gap-4">
            <a href="{{ route('login') }}" class="inline-flex justify-center items-center gap-2.5 bg-white text-slate-900 hover:bg-slate-50 px-9 py-4 rounded-full font-bold text-base transition-all shadow-2xl hover:-translate-y-1" data-lang-en='Login to System <i class="fa-solid fa-arrow-right-to-bracket"></i>' data-lang-zh='登录系统 <i class="fa-solid fa-arrow-right-to-bracket"></i>'>
                <i class="fa-solid fa-arrow-right-to-bracket text-blue-600"></i>
                <span>Login ke Sistem</span>
            </a>
            <a href="https://wa.me/628111234567" target="_blank" rel="noopener"
               class="inline-flex justify-center items-center gap-2.5 bg-white/10 hover:bg-white/15 border border-white/25 text-white px-9 py-4 rounded-full font-semibold text-base transition-all backdrop-blur-sm hover:-translate-y-1">
                <i class="fa-brands fa-whatsapp text-emerald-400 text-xl"></i>
                <span>Hubungi Sales / CS</span>
            </a>
        </div>

        <div class="mt-12 flex flex-wrap justify-center gap-6 text-slate-400 text-xs">
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-lock text-sky-400"></i> Koneksi Aman SSL</span>
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-shield-halved text-sky-400"></i> Data Terlindungi</span>
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-clock text-sky-400"></i> Dukungan 24/7</span>
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-certificate text-sky-400"></i> 20+ Tahun Pengalaman</span>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════ FLOATING CHAT WIDGET ═══════════════════════ --}}
<div id="chatbox-container" class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
    <div id="chatbox-window"
         class="bg-white w-80 sm:w-96 rounded-2xl shadow-2xl border border-slate-200 overflow-hidden mb-4 transition-all duration-300 transform origin-bottom-right scale-0 opacity-0 pointer-events-none">
        <div class="bg-gradient-to-r from-blue-700 to-blue-600 text-white p-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center"><i class="fa-solid fa-headset text-base"></i></div>
                <div>
                    <h4 class="font-bold text-sm" data-lang-en="Customer Support" data-lang-zh="客户支持">Customer Support</h4>
                    <div class="flex items-center gap-1.5 mt-0.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span><p class="text-xs text-blue-100" data-lang-en="Online" data-lang-zh="在线">Online</p></div>
                </div>
            </div>
            <button id="close-chat" class="text-blue-100 hover:text-white transition-colors w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white/10"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <div class="h-64 p-4 overflow-y-auto bg-slate-50 flex flex-col gap-3">
            <div class="flex items-start gap-2">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-robot text-blue-600 text-xs"></i></div>
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-200/60 text-sm text-slate-700">
                    <p data-lang-en="Hello! I'm your virtual assistant. How can I help you today?" data-lang-zh="你好！我是虚拟助手。今天有什么我可以帮您的吗？">Halo! Saya asisten virtual Alexindo. Ada yang bisa kami bantu hari ini?</p>
                </div>
            </div>
        </div>

        <div id="faq-chips-container" class="px-3 pt-2 pb-1 bg-white flex overflow-x-auto gap-2 hidden border-t border-slate-100 no-scrollbar whitespace-nowrap"></div>
        <div class="p-3 bg-white border-t border-slate-100">
            <form class="flex items-center gap-2" onsubmit="event.preventDefault();">
                <input type="text" class="w-full px-4 py-2.5 border border-slate-200 rounded-full text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-slate-50"
                       placeholder="Ketik pesan Anda..." data-lang-en-placeholder="Type your message..." data-lang-zh-placeholder="输入您的信息...">
                <button type="submit" class="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center flex-shrink-0 hover:bg-blue-700 transition-colors shadow-sm"><i class="fa-solid fa-paper-plane text-xs"></i></button>
            </form>
        </div>
    </div>

    <button id="toggle-chat" class="w-14 h-14 bg-blue-600 text-white rounded-full shadow-xl hover:bg-blue-700 hover:scale-105 transition-all duration-300 flex items-center justify-center relative group">
        <span class="absolute top-0 right-0 w-3.5 h-3.5 bg-emerald-400 rounded-full border-2 border-white animate-pulse"></span>
        <i class="fa-regular fa-comment-dots text-2xl group-hover:scale-110 transition-transform"></i>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatWindow = document.getElementById('chatbox-window');
        const toggleBtn  = document.getElementById('toggle-chat');
        const closeBtn   = document.getElementById('close-chat');
        const chatForm   = chatWindow?.querySelector('form');
        const chatInput  = chatForm?.querySelector('input');
        const chatArea   = chatWindow?.querySelector('.overflow-y-auto');
        if (!chatWindow || !toggleBtn || !closeBtn) return;

        let isOpen = false;
        let sessionId = localStorage.getItem('chat_session_id');
        if (!sessionId) { sessionId = 'session_' + Math.random().toString(36).substr(2, 9); localStorage.setItem('chat_session_id', sessionId); }
        let pollInterval = null;
        let faqsLoaded = false;

        function loadFaqs() {
            if (faqsLoaded) return;
            fetch('/api/chat/faqs').then(r => r.json()).then(data => {
                if (data.faqs && data.faqs.length > 0) {
                    const c = document.getElementById('faq-chips-container');
                    if (!c) return;
                    c.innerHTML = data.faqs.map(faq => `<button type="button" onclick="sendFaq(${faq.id},'${faq.question.replace(/'/g,"\\'")}');" class="bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 text-xs px-3 py-1.5 rounded-full transition-colors flex-shrink-0">${faq.question}</button>`).join('');
                    c.classList.remove('hidden');
                }
                faqsLoaded = true;
            }).catch(() => {});
        }

        function toggleChat() {
            isOpen = !isOpen;
            if (isOpen) {
                chatWindow.classList.remove('scale-0','opacity-0','pointer-events-none');
                chatWindow.classList.add('scale-100','opacity-100');
                toggleBtn.innerHTML = '<i class="fa-solid fa-xmark text-2xl group-hover:scale-110 transition-transform"></i>';
                loadFaqs(); fetchMessages();
                if (pollInterval) clearInterval(pollInterval);
                pollInterval = setInterval(fetchMessages, 3000);
            } else {
                chatWindow.classList.remove('scale-100','opacity-100');
                chatWindow.classList.add('scale-0','opacity-0','pointer-events-none');
                toggleBtn.innerHTML = '<span class="absolute top-0 right-0 w-3.5 h-3.5 bg-emerald-400 rounded-full border-2 border-white animate-pulse"></span><i class="fa-regular fa-comment-dots text-2xl group-hover:scale-110 transition-transform"></i>';
                if (pollInterval) clearInterval(pollInterval);
            }
        }
        toggleBtn.addEventListener('click', toggleChat);
        closeBtn.addEventListener('click', toggleChat);

        function renderMessage(msg) {
            const isSelf = msg.is_admin == 0 || msg.is_admin === false;
            const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
            return isSelf
                ? `<div class="flex items-end justify-end gap-2"><div class="bg-blue-600 text-white p-3 rounded-2xl rounded-br-none shadow-sm text-sm"><p>${msg.message}</p><div class="text-[10px] text-blue-200 mt-1 text-right">${time}</div></div></div>`
                : `<div class="flex items-start gap-2"><div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-headset text-blue-600 text-xs"></i></div><div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 text-sm text-slate-700"><p>${msg.message}</p><div class="text-[10px] text-gray-400 mt-1">${time}</div></div></div>`;
        }

        function fetchMessages() {
            if (!chatArea) return;
            fetch(`/api/chat/messages?session_id=${sessionId}`).then(r => r.json()).then(data => {
                const welcome = `<div class="flex items-start gap-2"><div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-robot text-blue-600 text-xs"></i></div><div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 text-sm text-slate-700"><p>Halo! Saya asisten virtual Alexindo. Ada yang bisa kami bantu hari ini?</p></div></div>`;
                let html = welcome;
                if (data.messages && data.messages.length > 0) data.messages.forEach(m => { html += renderMessage(m); });
                chatArea.innerHTML = html;
                chatArea.scrollTop = chatArea.scrollHeight;
            }).catch(() => {});
        }

        window.sendFaq = function (faqId, question) {
            if (!chatArea) return;
            chatArea.innerHTML += renderMessage({ is_admin: 0, message: question, created_at: new Date().toISOString() });
            chatArea.scrollTop = chatArea.scrollHeight;
            fetch('/api/chat/send', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''}, body:JSON.stringify({session_id:sessionId,message:question,name:'Visitor',is_faq:true,faq_id:faqId}) })
                .then(r => r.json()).then(d => { if (d.success) fetchMessages(); }).catch(() => {});
        };

        if (chatForm && chatInput) {
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const msg = chatInput.value.trim();
                if (!msg || !chatArea) return;
                chatInput.value = '';
                chatArea.innerHTML += renderMessage({ is_admin: 0, message: msg, created_at: new Date().toISOString() });
                chatArea.scrollTop = chatArea.scrollHeight;
                fetch('/api/chat/send', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''}, body:JSON.stringify({session_id:sessionId,message:msg,name:'Visitor'}) })
                    .then(r => r.json()).then(d => { if (d.success) fetchMessages(); }).catch(() => {});
            });
        }

        // ─── Tracking Card Toggle ───
        const toggleTrackingBtn = document.getElementById('toggle-tracking-btn');
        const trackingContent   = document.getElementById('tracking-content');
        const trackingChevron   = document.getElementById('tracking-chevron');
        const trackingCard      = document.getElementById('tracking-card');

        if (toggleTrackingBtn && trackingContent && trackingChevron) {
            toggleTrackingBtn.addEventListener('click', function () {
                const isCollapsed = trackingContent.style.maxHeight === '0px';
                if (isCollapsed) {
                    trackingContent.style.maxHeight = '1000px';
                    trackingContent.classList.remove('opacity-0'); trackingContent.classList.add('opacity-100');
                    trackingChevron.classList.remove('rotate-180');
                    if(trackingCard){ trackingCard.classList.remove('p-5','sm:p-6'); trackingCard.classList.add('p-7','sm:p-8'); }
                } else {
                    trackingContent.style.maxHeight = '0px';
                    trackingContent.classList.remove('opacity-100'); trackingContent.classList.add('opacity-0');
                    trackingChevron.classList.add('rotate-180');
                    if(trackingCard){ trackingCard.classList.remove('p-7','sm:p-8'); trackingCard.classList.add('p-5','sm:p-6'); }
                }
            });
        }
    });
</script>
@endsection

@section('scripts')
{{-- Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    window.mapInitialized = false;
    window.shipAnimationId = null;
    window.routeMapInstance = null;

    document.addEventListener('DOMContentLoaded', function() { initRouteMap(); });

    function initRouteMap() {
        if (window.mapInitialized) return;
        if (!document.getElementById('map-route')) return;
        window.mapInitialized = true;

        window.routeMapInstance = L.map('map-route', {
            zoomControl: false, dragging: false, scrollWheelZoom: false, doubleClickZoom: false
        }).setView([-2.5, 105.4], 6);

        const map = window.routeMapInstance;
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO', subdomains: 'abcd', maxZoom: 20
        }).addTo(map);

        const sundaKelapa  = [-6.1219, 106.8080];
        const srimasBatam  = [1.1301, 104.0531];
        const controlPoint = [-2.5, 107.5];

        const routeCoords = [];
        for (let i = 0; i <= 60; i++) {
            const t = i / 60, u = 1 - t;
            routeCoords.push([
                u*u*sundaKelapa[0] + 2*u*t*controlPoint[0] + t*t*srimasBatam[0],
                u*u*sundaKelapa[1] + 2*u*t*controlPoint[1] + t*t*srimasBatam[1]
            ]);
        }

        L.polyline(routeCoords, { color:'#94a3b8', weight:2, opacity:0.5, dashArray:'4,8', lineCap:'round' }).addTo(map);
        const activeLine = L.polyline([], { color:'#ef4444', weight:4, opacity:1, lineCap:'round' }).addTo(map);

        L.circleMarker(sundaKelapa, { radius:6, fillColor:'#ffffff', color:'#dc2626', weight:3, fillOpacity:1 }).addTo(map)
            .bindTooltip('<b>Sunda Kelapa</b><br>Jakarta', { permanent:true, direction:'right', className:'text-xs font-bold border-none shadow-sm' });
        L.circleMarker(srimasBatam, { radius:6, fillColor:'#ffffff', color:'#dc2626', weight:3, fillOpacity:1 }).addTo(map)
            .bindTooltip('<b>Srimas</b><br>Batam', { permanent:true, direction:'left', className:'text-xs font-bold border-none shadow-sm' });

        const shipSvg = `<svg width="30" height="75" viewBox="0 0 24 60" xmlns="http://www.w3.org/2000/svg" style="filter:drop-shadow(0px 8px 12px rgba(0,0,0,0.4));"><path d="M 12,2 C 18,5 22,12 22,20 L 22,50 C 22,58 18,60 12,60 C 6,60 2,58 2,50 L 2,20 C 2,12 6,5 12,2 Z" fill="#cbd5e1" stroke="#64748b" stroke-width="1"/><path d="M 12,4 C 17,7 20,13 20,20 L 20,49 C 20,55 17,57 12,57 C 7,57 4,55 4,49 L 4,20 C 4,13 7,7 12,4 Z" fill="#f1f5f9"/><g stroke="#0f172a" stroke-width="0.5"><rect x="6" y="14" width="5" height="10" fill="#ef4444"/><rect x="13" y="14" width="5" height="10" fill="#3b82f6"/><rect x="6" y="25" width="5" height="10" fill="#eab308"/><rect x="13" y="25" width="5" height="10" fill="#22c55e"/><rect x="6" y="36" width="5" height="10" fill="#f97316"/><rect x="13" y="36" width="5" height="10" fill="#8b5cf6"/></g><rect x="4" y="48" width="16" height="7" fill="#ffffff" stroke="#94a3b8" stroke-width="1" rx="1"/><rect x="6" y="49" width="12" height="2" fill="#0284c7"/><circle cx="12" cy="56" r="1.5" fill="#ef4444"/></svg>`;

        const shipIcon = L.divIcon({
            html: `<div id="animated-ship" style="transition:transform 0.1s linear;transform-origin:center center;">${shipSvg}</div>`,
            className: '', iconSize: [30,75], iconAnchor: [15,37.5]
        });
        const shipMarker = L.marker(sundaKelapa, { icon: shipIcon, zIndexOffset: 1000 }).addTo(map);

        function getBearing(start, end) {
            const lat1 = start[0]*Math.PI/180, lat2 = end[0]*Math.PI/180;
            const dLng = (end[1]-start[1])*Math.PI/180;
            const y = Math.sin(dLng)*Math.cos(lat2);
            const x = Math.cos(lat1)*Math.sin(lat2)-Math.sin(lat1)*Math.cos(lat2)*Math.cos(dLng);
            return (Math.atan2(y,x)*180/Math.PI+360)%360;
        }
        function getPointOnLine(p1, p2, t) { return [p1[0]+(p2[0]-p1[0])*t, p1[1]+(p2[1]-p1[1])*t]; }

        let progress = 0, direction = 1;
        const totalPoints = routeCoords.length;

        function animateShip() {
            progress += 0.002 * direction;
            if (progress >= 1) { progress = 1; direction = -1; activeLine.setLatLngs([]); }
            else if (progress <= 0) { progress = 0; direction = 1; activeLine.setLatLngs([]); }

            const sp = progress*(totalPoints-1), si = Math.floor(sp), t = sp - si;
            if (si < totalPoints-1) {
                const pos = getPointOnLine(routeCoords[si], routeCoords[si+1], t);
                shipMarker.setLatLng(pos);
                if (direction === 1) { const tr = routeCoords.slice(0,si+1); tr.push(pos); activeLine.setLatLngs(tr); }
                else { const tr = routeCoords.slice(si+1); tr.unshift(pos); activeLine.setLatLngs(tr); }
                const bearing = getBearing(routeCoords[si], routeCoords[si+1]);
                const rotation = direction===1 ? bearing : (bearing+180)%360;
                const shipEl = document.getElementById('animated-ship');
                if (shipEl) shipEl.style.transform = `rotate(${rotation}deg)`;
            }
            requestAnimationFrame(animateShip);
        }
        animateShip();
    }
</script>
@endsection
