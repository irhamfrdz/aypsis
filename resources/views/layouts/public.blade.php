<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ALEXINDO YAKINPRIMA - Maritime & Logistics')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        
        .hero-bg {
            background-image: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.8)), url('https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .nav-scrolled {
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .nav-scrolled .nav-link {
            color: #1e293b;
        }
        
        .nav-scrolled .nav-logo {
            color: #0f172a;
        }

        /* Micro animations */
        .service-card {
            transition: all 0.3s ease;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        @yield('additional_styles')
    </style>
</head>
<body class="antialiased text-slate-800">

    <!-- Navigation -->
    <nav id="navbar" class="fixed w-full z-50 transition-all duration-300 py-4 @yield('navbar_class', 'text-white')">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="nav-logo text-2xl font-bold tracking-tighter flex items-center gap-2 transition-colors duration-300 @yield('logo_class', 'text-white')">
                        <img src="{{ asset('images/logo_transparent.png') }}?v={{ time() }}" alt="Logo AYP" class="h-14 w-auto">
                        <span>ALEXINDO<span class="text-blue-500 font-black">YAKINPRIMA</span></span>
                    </a>
                </div>
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="{{ route('home') }}#beranda" class="nav-link font-medium hover:text-blue-400 transition-colors" data-lang-en="Home" data-lang-zh="首页">Beranda</a>
                    <!-- Layanan Dropdown -->
                    <div class="relative group">
                        <a href="{{ route('home') }}#layanan" class="flex items-center gap-1 nav-link font-medium hover:text-blue-400 transition-colors focus:outline-none">
                            <span data-lang-en="Services" data-lang-zh="服务">Layanan</span> <i class="fa-solid fa-chevron-down text-[10px] ml-0.5 opacity-70"></i>
                        </a>
                        <div class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top scale-95 group-hover:scale-100 py-2 border border-slate-100 z-50 text-slate-800">
                            <a href="{{ route('public.layanan.sea-freight') }}" class="block px-4 py-2 text-sm hover:bg-blue-50 hover:text-blue-600 transition-colors">Sea Freight</a>
                            <a href="{{ route('public.layanan.fcl') }}" class="block px-4 py-2 text-sm hover:bg-blue-50 hover:text-blue-600 transition-colors">FCL</a>
                            <a href="{{ route('public.layanan.lcl') }}" class="block px-4 py-2 text-sm hover:bg-blue-50 hover:text-blue-600 transition-colors">LCL</a>
                            <a href="{{ route('public.layanan.door-to-door') }}" class="block px-4 py-2 text-sm hover:bg-blue-50 hover:text-blue-600 transition-colors">Door-to-Door</a>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-blue-50 hover:text-blue-600 transition-colors">Project Cargo</a>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-blue-50 hover:text-blue-600 transition-colors">Inland Transportation</a>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-blue-50 hover:text-blue-600 transition-colors">Customs & FTZ</a>
                        </div>
                    </div>
                    <a href="{{ route('home') }}#rute" class="nav-link font-medium hover:text-blue-400 transition-colors" data-lang-en="Routes" data-lang-zh="航线">Rute</a>
                    <a href="{{ route('public.pelabuhan') }}" class="nav-link font-medium hover:text-blue-400 transition-colors" data-lang-en="Ports" data-lang-zh="港口">Pelabuhan</a>
                    <a href="{{ route('home') }}#mitra" class="nav-link font-medium hover:text-blue-400 transition-colors" data-lang-en="Partners" data-lang-zh="合作伙伴">Mitra</a>
                    
                    <!-- Language Toggle -->
                    <div class="relative group">
                        <button class="flex items-center gap-1 nav-link font-medium hover:text-blue-400 transition-colors focus:outline-none" title="Ubah Bahasa / Change Language">
                            <i class="fa-solid fa-globe"></i> <span id="current-lang-label">ID</span> <i class="fa-solid fa-chevron-down text-[10px] ml-0.5 opacity-70"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top scale-95 group-hover:scale-100 py-2 border border-slate-100 z-50 text-slate-800">
                            <button onclick="setLang('id')" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm hover:bg-blue-50 hover:text-blue-600 transition-colors"><img src="https://flagcdn.com/w20/id.png" width="16" alt="ID" class="rounded-[2px]"> Indonesia (ID)</button>
                            <button onclick="setLang('en')" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm hover:bg-blue-50 hover:text-blue-600 transition-colors"><img src="https://flagcdn.com/w20/gb.png" width="16" alt="EN" class="rounded-[2px]"> English (EN)</button>
                            <button onclick="setLang('zh')" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm hover:bg-blue-50 hover:text-blue-600 transition-colors"><img src="https://flagcdn.com/w20/cn.png" width="16" alt="ZH" class="rounded-[2px]"> 中文 (ZH)</button>
                        </div>
                    </div>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary px-6 py-2.5 rounded-full font-semibold text-white shadow-lg" data-lang-en='Dashboard <i class="fa-solid fa-arrow-right ml-2"></i>' data-lang-zh='仪表盘 <i class="fa-solid fa-arrow-right ml-2"></i>'>
                            Dashboard <i class="fa-solid fa-arrow-right ml-2"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary px-6 py-2.5 rounded-full font-semibold text-white shadow-lg" data-lang-en='Login Portal <i class="fa-solid fa-user ml-2"></i>' data-lang-zh='登录门户 <i class="fa-solid fa-user ml-2"></i>'>
                            Login Portal <i class="fa-solid fa-user ml-2"></i>
                        </a>
                    @endauth
                </div>
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-2xl nav-link focus:outline-none">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu (Hidden by default) -->
        <div id="mobile-menu" class="hidden bg-white text-slate-800 absolute w-full shadow-xl mt-4 pb-4">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 flex flex-col">
                <a href="{{ route('home') }}#beranda" class="block px-3 py-2 text-base font-medium hover:text-blue-600 hover:bg-slate-50" data-lang-en="Home" data-lang-zh="首页">Beranda</a>
                <!-- Layanan Mobile Menu -->
                <div>
                    <div class="flex items-center justify-between w-full pr-4 hover:bg-slate-50">
                        <a href="{{ route('home') }}#layanan" class="block flex-grow px-3 py-2 text-base font-medium hover:text-blue-600" data-lang-en="Services" data-lang-zh="服务">Layanan</a>
                        <button onclick="document.getElementById('mobile-layanan-submenu').classList.toggle('hidden')" class="p-2 text-slate-500 hover:text-blue-600 focus:outline-none">
                            <i class="fa-solid fa-chevron-down text-sm"></i>
                        </button>
                    </div>
                    <div id="mobile-layanan-submenu" class="hidden pl-6 pr-3 py-2 space-y-1 bg-slate-50/50">
                        <a href="{{ route('public.layanan.sea-freight') }}" class="block px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded-md">Sea Freight</a>
                        <a href="{{ route('public.layanan.fcl') }}" class="block px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded-md">FCL</a>
                        <a href="{{ route('public.layanan.lcl') }}" class="block px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded-md">LCL</a>
                        <a href="{{ route('public.layanan.door-to-door') }}" class="block px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded-md">Door-to-Door</a>
                        <a href="#" class="block px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded-md">Project Cargo</a>
                        <a href="#" class="block px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded-md">Inland Transportation</a>
                        <a href="#" class="block px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded-md">Customs & FTZ</a>
                    </div>
                </div>
                <a href="{{ route('home') }}#rute" class="block px-3 py-2 text-base font-medium hover:text-blue-600 hover:bg-slate-50" data-lang-en="Routes" data-lang-zh="航线">Rute</a>
                <a href="{{ route('public.pelabuhan') }}" class="block px-3 py-2 text-base font-medium hover:text-blue-600 hover:bg-slate-50" data-lang-en="Ports" data-lang-zh="港口">Pelabuhan</a>
                <a href="{{ route('home') }}#mitra" class="block px-3 py-2 text-base font-medium hover:text-blue-600 hover:bg-slate-50" data-lang-en="Partners" data-lang-zh="合作伙伴">Mitra</a>
                
                <div class="px-3 py-2">
                    <div class="flex items-center gap-2 text-base font-medium mb-2 text-slate-800">
                        <i class="fa-solid fa-globe"></i> Language
                    </div>
                    <div class="flex gap-2 pl-6">
                        <button onclick="setLang('id')" id="lang-btn-id" class="flex items-center gap-1.5 px-3 py-1 text-sm rounded-lg border border-slate-200 transition-colors"><img src="https://flagcdn.com/w20/id.png" width="16" alt="ID" class="rounded-[2px]"> ID</button>
                        <button onclick="setLang('en')" id="lang-btn-en" class="flex items-center gap-1.5 px-3 py-1 text-sm rounded-lg border border-slate-200 transition-colors"><img src="https://flagcdn.com/w20/gb.png" width="16" alt="EN" class="rounded-[2px]"> EN</button>
                        <button onclick="setLang('zh')" id="lang-btn-zh" class="flex items-center gap-1.5 px-3 py-1 text-sm rounded-lg border border-slate-200 transition-colors"><img src="https://flagcdn.com/w20/cn.png" width="16" alt="ZH" class="rounded-[2px]"> ZH</button>
                    </div>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base font-medium text-blue-600" data-lang-en="Dashboard Portal" data-lang-zh="仪表盘门户">Dashboard Portal</a>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-blue-600" data-lang-en="Login Portal" data-lang-zh="登录门户">Login Portal</a>
                @endauth
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-300 py-16 border-t-4 border-blue-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <div class="lg:col-span-1">
                    <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tighter flex flex-col items-start gap-3 text-white mb-6">
                        <img src="{{ asset('images/logo_transparent.png') }}?v={{ time() }}" alt="Logo AYP" class="h-14 w-auto">
                        <span>ALEXINDO<span class="text-blue-500">YAKINPRIMA</span></span>
                    </a>
                    <p class="text-slate-400 mb-6 leading-relaxed">
                        Integrator maritim dan logistik terkemuka, menjadi partner terbaik dalam pengiriman peti kemas Anda.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-blue-600 transition-colors text-white"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-blue-600 transition-colors text-white"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-blue-600 transition-colors text-white"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-white font-bold text-lg mb-6">Tautan Cepat</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('home') }}#beranda" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Beranda</a></li>
                        <li><a href="{{ route('home') }}#layanan" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Layanan</a></li>
                        <li><a href="{{ route('home') }}#rute" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Jadwal & Rute</a></li>
                        <li><a href="{{ route('public.pelabuhan') }}" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Pelabuhan Tujuan</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs"></i> Login</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-bold text-lg mb-6">Layanan</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Sea Freight (FCL & LCL)</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Door-to-Door</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Project Cargo</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Inland Transportation</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Customs & FTZ</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-bold text-lg mb-6">Hubungi Kami</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot mt-1 text-blue-500"></i>
                            <span>Jl. Pluit raya No. 8 Blok B No. 12, RT.7/RW.7,<br>Penjaringan, Jakarta Utara, Jakarta 14440</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-phone text-blue-500"></i>
                            <span>+62 21 1234 5678</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-envelope text-blue-500"></i>
                            <span>info@alexindoyp.co.id</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p>&copy; {{ date('Y') }} PT Alexindo Yakinprima. Hak Cipta Dilindungi.</p>
                <div class="flex gap-6 text-sm">
                    <a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a>
                    <a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Script for Navbar Scroll Effect and Mobile Menu -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.getElementById('navbar');
            const navLinks = document.querySelectorAll('.nav-link');
            const navLogo = document.querySelector('.nav-logo');
            
            @if(View::hasSection('force_scrolled_nav'))
                // Force scrolled state (for non-hero pages)
                navbar.classList.add('nav-scrolled');
                navbar.classList.remove('text-white');
                if(navLogo) navLogo.classList.remove('text-white');
            @else
                // Scroll Effect for hero pages
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 50) {
                        navbar.classList.add('nav-scrolled');
                        navbar.classList.remove('text-white');
                        if(navLogo) navLogo.classList.remove('text-white');
                    } else {
                        navbar.classList.remove('nav-scrolled');
                        navbar.classList.add('text-white');
                        if(navLogo) navLogo.classList.add('text-white');
                    }
                });
            @endif
            
            // Mobile Menu Toggle
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if(mobileBtn && mobileMenu) {
                mobileBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                    
                    // Change icon
                    const icon = mobileBtn.querySelector('i');
                    if (mobileMenu.classList.contains('hidden')) {
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    } else {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-xmark');
                        
                        // Force solid background when menu is open
                        navbar.classList.add('nav-scrolled');
                        navbar.classList.remove('text-white');
                    }
                });
                
                // Close mobile menu when clicking a link
                const mobileLinks = mobileMenu.querySelectorAll('a');
                mobileLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        mobileMenu.classList.add('hidden');
                        const icon = mobileBtn.querySelector('i');
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    });
                });
            }
        });

        // Language toggle script
        let currentLang = localStorage.getItem('ayp_lang') || 'id';
        function updateLanguageUI() {
            const langLabel = document.getElementById('current-lang-label');
            if(langLabel) langLabel.textContent = currentLang.toUpperCase();

            // Update mobile buttons active state
            ['id', 'en', 'zh'].forEach(l => {
                const btn = document.getElementById('lang-btn-' + l);
                if (btn) {
                    if (l === currentLang) {
                        btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                        btn.classList.remove('text-slate-700', 'hover:bg-blue-50', 'hover:text-blue-600');
                    } else {
                        btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                        btn.classList.add('text-slate-700', 'hover:bg-blue-50', 'hover:text-blue-600');
                    }
                }
            });

            document.querySelectorAll('[data-lang-en]').forEach(el => {
                if(!el.hasAttribute('data-lang-id')) {
                    el.setAttribute('data-lang-id', el.innerHTML);
                }
                
                if (currentLang === 'en') {
                    el.innerHTML = el.getAttribute('data-lang-en');
                } else if (currentLang === 'zh') {
                    el.innerHTML = el.getAttribute('data-lang-zh') || el.getAttribute('data-lang-en');
                } else {
                    el.innerHTML = el.getAttribute('data-lang-id');
                }
            });

            document.querySelectorAll('[data-lang-en-placeholder]').forEach(el => {
                if(!el.hasAttribute('data-lang-id-placeholder')) {
                    el.setAttribute('data-lang-id-placeholder', el.getAttribute('placeholder') || '');
                }
                
                if (currentLang === 'en') {
                    el.setAttribute('placeholder', el.getAttribute('data-lang-en-placeholder'));
                } else if (currentLang === 'zh') {
                    el.setAttribute('placeholder', el.getAttribute('data-lang-zh-placeholder') || el.getAttribute('data-lang-en-placeholder'));
                } else {
                    el.setAttribute('placeholder', el.getAttribute('data-lang-id-placeholder'));
                }
            });
        }
        function setLang(lang) {
            currentLang = lang;
            localStorage.setItem('ayp_lang', currentLang);
            updateLanguageUI();
        }
        document.addEventListener('DOMContentLoaded', updateLanguageUI);
    </script>
    
    <!-- AOS Animation Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                once: true,
                offset: 50,
                easing: 'ease-out-cubic'
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>
