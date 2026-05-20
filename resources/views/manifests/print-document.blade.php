<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Photoscape - BL: {{ $manifest->nomor_bl }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        /* Screen view settings */
        body {
            background-color: #0f172a;
            color: #f8fafc;
        }

        /* Workbench canvas backdrop */
        .workbench {
            background-image: radial-gradient(rgba(51, 65, 85, 0.25) 1.5px, transparent 1.5px);
            background-size: 24px 24px;
        }

        /* Interactive sheet preview card */
        .sheet-preview {
            background-color: white;
            color: black;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: grid;
        }

        /* Print media styles */
        @media print {
            body, .workbench {
                background: white !important;
                color: black !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-area {
                display: block !important;
                width: 100% !important;
                height: auto !important;
            }
            .sheet-print {
                page-break-after: always;
                page-break-inside: avoid;
                box-sizing: border-box;
                display: grid !important;
                width: 100vw !important;
                height: 100vh !important;
                background-color: white !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }
            .print-cell {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                overflow: hidden !important;
                width: 100% !important;
                height: 100% !important;
                box-sizing: border-box !important;
            }
            .print-img {
                width: 100% !important;
                height: 100% !important;
                display: block !important;
            }
        }
    </style>
    <style id="dynamic-print-css"></style>
</head>
<body class="min-h-screen flex flex-col antialiased selection:bg-indigo-500 selection:text-white">

    @if(count($imageUrls) > 0)
    <!-- Header Panel (Screen Only) -->
    <header class="no-print bg-slate-950/90 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-50 px-6 py-4 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600/20 rounded-xl flex items-center justify-center border border-indigo-500/30 text-indigo-400">
                <i class="fa-solid fa-images text-lg"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                    Photoscape Print Studio
                    <span class="px-2 py-0.5 bg-indigo-500/20 text-indigo-400 text-xs font-semibold rounded-full border border-indigo-500/30">
                        {{ count($imageUrls) }} Lampiran
                    </span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    Kapal: <span class="text-slate-300 font-semibold">{{ $manifest->nama_kapal }}</span> | 
                    No. BL: <span class="text-slate-300 font-semibold">{{ $manifest->nomor_bl }}</span> | 
                    No. Tanda Terima: <span class="text-slate-300 font-semibold">{{ $manifest->nomor_tanda_terima ?: '-' }}</span>
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="window.print()" 
                    class="flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-emerald-950/50 hover:scale-[1.02] cursor-pointer">
                <i class="fa-solid fa-print"></i>
                Cetak Halaman
            </button>
            <button onclick="window.close()" 
                    class="flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold rounded-xl transition-all border border-slate-700 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
                Batal
            </button>
        </div>
    </header>

    <!-- Main Workspace (Screen Only) -->
    <div class="no-print flex-grow flex flex-col lg:flex-row h-[calc(100vh-73px)] overflow-hidden">
        
        <!-- Sidebar Controls Panel -->
        <aside class="w-full lg:w-80 bg-slate-900/90 border-b lg:border-b-0 lg:border-r border-slate-800/80 p-5 overflow-y-auto flex flex-col gap-6 backdrop-blur-sm shadow-2xl">
            
            <!-- Grid Layout Option -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                    <i class="fa-solid fa-table-cells mr-1.5 text-indigo-400"></i> Tata Letak Grid
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="setLayout(1)" id="btn-grid-1" class="layout-btn p-3 bg-slate-800/50 border border-slate-700/50 rounded-xl hover:bg-slate-800 hover:border-slate-600 text-slate-300 text-center transition-all cursor-pointer">
                        <div class="w-full h-8 border border-dashed border-slate-500 rounded bg-slate-950/40 mb-1 flex items-center justify-center text-xs">1</div>
                        <span class="text-xs font-medium">1 Per Halaman</span>
                    </button>
                    <button onclick="setLayout(2)" id="btn-grid-2" class="layout-btn p-3 bg-slate-800/50 border border-slate-700/50 rounded-xl hover:bg-slate-800 hover:border-slate-600 text-slate-300 text-center transition-all cursor-pointer">
                        <div class="w-full h-8 border border-dashed border-slate-500 rounded bg-slate-950/40 mb-1 grid grid-cols-1 grid-rows-2 gap-[2px] p-[2px]">
                            <div class="bg-slate-700/50 rounded-[1px]"></div>
                            <div class="bg-slate-700/50 rounded-[1px]"></div>
                        </div>
                        <span class="text-xs font-medium">2 Per Halaman</span>
                    </button>
                    <button onclick="setLayout(4)" id="btn-grid-4" class="layout-btn p-3 bg-slate-800/50 border border-slate-700/50 rounded-xl hover:bg-slate-800 hover:border-slate-600 text-slate-300 text-center transition-all cursor-pointer">
                        <div class="w-full h-8 border border-dashed border-slate-500 rounded bg-slate-950/40 mb-1 grid grid-cols-2 grid-rows-2 gap-[2px] p-[2px]">
                            <div class="bg-slate-700/50 rounded-[1px]"></div>
                            <div class="bg-slate-700/50 rounded-[1px]"></div>
                            <div class="bg-slate-700/50 rounded-[1px]"></div>
                            <div class="bg-slate-700/50 rounded-[1px]"></div>
                        </div>
                        <span class="text-xs font-medium">4 Per Halaman</span>
                    </button>
                    <button onclick="setLayout(6)" id="btn-grid-6" class="layout-btn p-3 bg-slate-800/50 border border-slate-700/50 rounded-xl hover:bg-slate-800 hover:border-slate-600 text-slate-300 text-center transition-all cursor-pointer">
                        <div class="w-full h-8 border border-dashed border-slate-500 rounded bg-slate-950/40 mb-1 grid grid-cols-2 grid-rows-3 gap-[2px] p-[2px]">
                            <div class="bg-slate-700/50 rounded-[1px]"></div>
                            <div class="bg-slate-700/50 rounded-[1px]"></div>
                            <div class="bg-slate-700/50 rounded-[1px]"></div>
                            <div class="bg-slate-700/50 rounded-[1px]"></div>
                            <div class="bg-slate-700/50 rounded-[1px]"></div>
                            <div class="bg-slate-700/50 rounded-[1px]"></div>
                        </div>
                        <span class="text-xs font-medium">6 Per Halaman</span>
                    </button>
                </div>
            </div>

            <hr class="border-slate-800">

            <!-- Paper Settings -->
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        <i class="fa-solid fa-file-invoice mr-1.5 text-indigo-400"></i> Ukuran Kertas
                    </label>
                    <select id="paper-size" onchange="updateSettings()" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2.5 text-slate-200 text-sm focus:outline-none focus:border-indigo-500 transition-colors cursor-pointer">
                        <option value="A4">A4 (210mm x 297mm)</option>
                        <option value="Letter">Letter (216mm x 279mm)</option>
                        <option value="F4">F4 / Folio (215mm x 330mm)</option>
                        <option value="A3">A3 (297mm x 420mm)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        <i class="fa-solid fa-arrows-spin mr-1.5 text-indigo-400"></i> Orientasi Kertas
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="setOrientation('portrait')" id="btn-orient-portrait" class="orient-btn py-2 px-3 bg-slate-800/50 border border-slate-700/50 rounded-xl hover:bg-slate-800 text-sm text-slate-300 font-medium transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-file mr-1"></i> Portrait
                        </button>
                        <button onclick="setOrientation('landscape')" id="btn-orient-landscape" class="orient-btn py-2 px-3 bg-slate-800/50 border border-slate-700/50 rounded-xl hover:bg-slate-800 text-sm text-slate-300 font-medium transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-file rotate-90 mr-1"></i> Landscape
                        </button>
                    </div>
                </div>
            </div>

            <hr class="border-slate-800">

            <!-- Fitting Modes & Margins -->
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        <i class="fa-solid fa-crop-simple mr-1.5 text-indigo-400"></i> Mode Gambar
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="setFitMode('contain')" id="btn-fit-contain" class="fit-btn py-2 px-3 bg-slate-800/50 border border-slate-700/50 rounded-xl hover:bg-slate-800 text-sm text-slate-300 font-medium transition-all cursor-pointer">
                            Fit (Utuh)
                        </button>
                        <button onclick="setFitMode('cover')" id="btn-fit-cover" class="fit-btn py-2 px-3 bg-slate-800/50 border border-slate-700/50 rounded-xl hover:bg-slate-800 text-sm text-slate-300 font-medium transition-all cursor-pointer">
                            Fill (Penuh)
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        <i class="fa-solid fa-arrows-left-right-to-line mr-1.5 text-indigo-400"></i> Jarak Jeda (Grid Gap)
                    </label>
                    <input type="range" id="grid-gap" min="0" max="30" value="10" oninput="updateSettings()" class="w-full h-1.5 bg-slate-800 rounded-lg appearance-none cursor-pointer accent-indigo-500">
                    <div class="flex justify-between text-[10px] text-slate-500 mt-1">
                        <span>0px</span>
                        <span id="gap-val">10px</span>
                        <span>30px</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        <i class="fa-solid fa-border-all mr-1.5 text-indigo-400"></i> Margin Kertas
                    </label>
                    <input type="range" id="page-margin" min="0" max="40" value="10" oninput="updateSettings()" class="w-full h-1.5 bg-slate-800 rounded-lg appearance-none cursor-pointer accent-indigo-500">
                    <div class="flex justify-between text-[10px] text-slate-500 mt-1">
                        <span>0px</span>
                        <span id="margin-val">10px</span>
                        <span>40px</span>
                    </div>
                </div>
            </div>

            <div class="mt-auto pt-4 border-t border-slate-800 text-[11px] text-slate-500 flex flex-col gap-1">
                <span class="font-semibold text-slate-400">TIPS PRINT:</span>
                <span>• Atur margin printer menjadi "None/Tanpa Margin"</span>
                <span>• Aktifkan opsi "Background Graphics" agar warna jarak tercetak dengan baik.</span>
            </div>
        </aside>

        <!-- Canvas Area -->
        <main class="flex-grow p-6 md:p-8 overflow-y-auto workbench flex flex-col items-center">
            
            <!-- Live Preview Pages Container -->
            <div id="preview-workspace" class="flex flex-col gap-8 w-full max-w-3xl items-center pb-12">
                <!-- Dynamically generated sheet previews go here -->
            </div>
            
        </main>
    </div>

    <!-- Hidden Print Layout Container -->
    <div id="print-workspace" class="hidden print:block print-area">
        <!-- Dynamically generated print pages go here -->
    </div>
    
    @else
    <!-- Empty State -->
    <main class="flex-grow max-w-7xl mx-auto w-full p-6 md:p-8 flex flex-col items-center justify-center">
        <div class="text-center py-16 px-6 max-w-md bg-slate-950/40 rounded-3xl border border-slate-800/80 shadow-2xl backdrop-blur-sm">
            <div class="w-16 h-16 bg-slate-800/80 rounded-2xl flex items-center justify-center mx-auto mb-5 text-slate-400 border border-slate-700/50 shadow-inner">
                <i class="fa-solid fa-image-slash text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Tidak Ada Gambar Dokumen</h3>
            <p class="text-sm text-slate-400 leading-relaxed mb-6">
                Tidak ditemukan lampiran gambar di menu Tanda Terima, Tanda Terima Tanpa Surat Jalan, atau Tanda Terima LCL untuk data manifest ini.
            </p>
            <button onclick="window.close()" 
                    class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold rounded-xl transition-all border border-slate-700 w-full cursor-pointer">
                Kembali ke Manifest
            </button>
        </div>
    </main>
    @endif

    <!-- Dynamic Layout Engine Script -->
    @if(count($imageUrls) > 0)
    <script>
        // Array of image URLs fetched from PHP
        const imageUrls = {!! json_encode($imageUrls) !!};
        
        // Default print preferences
        let currentLayout = 1;
        let currentOrientation = 'portrait';
        let currentFitMode = 'contain';
        
        // Paper definitions
        const paperRatio = {
            A4: 210 / 297,       // 0.707
            Letter: 216 / 279,   // 0.774
            F4: 215 / 330,       // 0.6515
            A3: 297 / 420,       // 0.707
        };

        const paperPrintSizes = {
            A4: { portrait: 'A4 portrait', landscape: 'A4 landscape' },
            Letter: { portrait: 'letter portrait', landscape: 'letter landscape' },
            F4: { portrait: '215mm 330mm portrait', landscape: '330mm 215mm landscape' },
            A3: { portrait: 'A3 portrait', landscape: 'A3 landscape' }
        };

        // Layout definitions based on grid count and orientation
        function getGridClasses(layout, orient) {
            switch(layout) {
                case 1:
                    return { cols: 1, rows: 1, css: 'grid-cols-1 grid-rows-1' };
                case 2:
                    return orient === 'portrait' 
                        ? { cols: 1, rows: 2, css: 'grid-cols-1 grid-rows-2' }
                        : { cols: 2, rows: 1, css: 'grid-cols-2 grid-rows-1' };
                case 4:
                    return { cols: 2, rows: 2, css: 'grid-cols-2 grid-rows-2' };
                case 6:
                    return orient === 'portrait'
                        ? { cols: 2, rows: 3, css: 'grid-cols-2 grid-rows-3' }
                        : { cols: 3, rows: 2, css: 'grid-cols-3 grid-rows-2' };
                case 8:
                    return orient === 'portrait'
                        ? { cols: 2, rows: 4, css: 'grid-cols-2 grid-rows-4' }
                        : { cols: 4, rows: 2, css: 'grid-cols-4 grid-rows-2' };
                default:
                    return { cols: 1, rows: 1, css: 'grid-cols-1 grid-rows-1' };
            }
        }

        // Initialize state
        function init() {
            setLayout(1);
            setOrientation('portrait');
            setFitMode('contain');
        }

        function setLayout(layoutVal) {
            currentLayout = layoutVal;
            
            // Toggle active styles on buttons
            document.querySelectorAll('.layout-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'border-indigo-500', 'text-white');
                btn.classList.add('bg-slate-800/50', 'border-slate-700/50', 'text-slate-300');
            });
            const activeBtn = document.getElementById(`btn-grid-${layoutVal}`);
            if (activeBtn) {
                activeBtn.classList.add('bg-indigo-600', 'border-indigo-500', 'text-white');
                activeBtn.classList.remove('bg-slate-800/50', 'border-slate-700/50', 'text-slate-300');
            }

            updateSettings();
        }

        function setOrientation(orient) {
            currentOrientation = orient;
            
            // Toggle active styles
            document.querySelectorAll('.orient-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'border-indigo-500', 'text-white');
                btn.classList.add('bg-slate-800/50', 'border-slate-700/50', 'text-slate-300');
            });
            const activeBtn = document.getElementById(`btn-orient-${orient}`);
            if (activeBtn) {
                activeBtn.classList.add('bg-indigo-600', 'border-indigo-500', 'text-white');
                activeBtn.classList.remove('bg-slate-800/50', 'border-slate-700/50', 'text-slate-300');
            }

            updateSettings();
        }

        function setFitMode(mode) {
            currentFitMode = mode;
            
            // Toggle active styles
            document.querySelectorAll('.fit-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'border-indigo-500', 'text-white');
                btn.classList.add('bg-slate-800/50', 'border-slate-700/50', 'text-slate-300');
            });
            const activeBtn = document.getElementById(`btn-fit-${mode}`);
            if (activeBtn) {
                activeBtn.classList.add('bg-indigo-600', 'border-indigo-500', 'text-white');
                activeBtn.classList.remove('bg-slate-800/50', 'border-slate-700/50', 'text-slate-300');
            }

            updateSettings();
        }

        function updateSettings() {
            const paperSelected = document.getElementById('paper-size').value;
            const gridGap = parseInt(document.getElementById('grid-gap').value);
            const pageMargin = parseInt(document.getElementById('page-margin').value);

            // Display numerical values
            document.getElementById('gap-val').innerText = `${gridGap}px`;
            document.getElementById('margin-val').innerText = `${pageMargin}px`;

            // Calculate ratios & layout dimensions
            const ratio = paperRatio[paperSelected];
            const activeRatio = currentOrientation === 'portrait' ? ratio : 1 / ratio;

            // Target base width for screen rendering
            const screenBaseWidth = 480; 
            const screenHeight = Math.round(screenBaseWidth / activeRatio);

            // Fetch grid details
            const grid = getGridClasses(currentLayout, currentOrientation);
            
            // Generate print media CSS overrides dynamically
            const sizeString = paperPrintSizes[paperSelected][currentOrientation];
            const dynamicCSS = document.getElementById('dynamic-print-css');
            dynamicCSS.innerHTML = `
                @media print {
                    @page {
                        size: ${sizeString};
                        margin: 0;
                    }
                    .sheet-print {
                        grid-template-columns: repeat(${grid.cols}, 1fr) !important;
                        grid-template-rows: repeat(${grid.rows}, 1fr) !important;
                        gap: ${gridGap}px !important;
                        padding: ${pageMargin}px !important;
                    }
                }
            `;

            // Compile page sheets array
            const totalPages = Math.ceil(imageUrls.length / currentLayout);
            let workspaceHtml = '';
            let printHtml = '';

            for (let pageIdx = 0; pageIdx < totalPages; pageIdx++) {
                const startIndex = pageIdx * currentLayout;
                const pageImages = imageUrls.slice(startIndex, startIndex + currentLayout);

                // Build screen view sheets
                let gridInnerHtml = '';
                // Build print sheets
                let printInnerHtml = '';

                for (let cellIdx = 0; cellIdx < currentLayout; cellIdx++) {
                    const imgUrl = pageImages[cellIdx];
                    if (imgUrl) {
                        gridInnerHtml += `
                            <div class="relative overflow-hidden flex items-center justify-center w-full h-full border border-slate-200/40 border-dashed" style="padding: 2px;">
                                <img src="${imgUrl}" class="w-full h-full transition-all duration-300" style="object-fit: ${currentFitMode};" />
                                <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded bg-slate-900/60 text-white font-medium text-[9px] no-print">Img ${startIndex + cellIdx + 1}</span>
                            </div>
                        `;
                        printInnerHtml += `
                            <div class="print-cell">
                                <img src="${imgUrl}" class="print-img" style="object-fit: ${currentFitMode};" />
                            </div>
                        `;
                    } else {
                        // Empty slot to maintain grid shape
                        gridInnerHtml += `
                            <div class="border border-dashed border-slate-300 flex items-center justify-center text-slate-300/40 text-xs font-semibold bg-slate-50/20">
                                Empty Cell
                            </div>
                        `;
                        printInnerHtml += `
                            <div class="print-cell bg-white"></div>
                        `;
                    }
                }

                // Render page container for screen preview
                workspaceHtml += `
                    <div class="flex flex-col items-center">
                        <div class="text-[10px] uppercase font-bold text-slate-400 mb-1.5 flex items-center gap-1.5">
                            <span>Halaman ${pageIdx + 1} dari ${totalPages}</span>
                            <span class="text-slate-600">•</span>
                            <span class="text-indigo-400">${pageImages.length} File Terpasang</span>
                        </div>
                        <div class="sheet-preview rounded-lg shadow-2xl relative" 
                             style="width: ${screenBaseWidth}px; height: ${screenHeight}px; padding: ${pageMargin * 0.8}px; gap: ${gridGap * 0.8}px; grid-template-columns: repeat(${grid.cols}, 1fr); grid-template-rows: repeat(${grid.rows}, 1fr);">
                            ${gridInnerHtml}
                        </div>
                    </div>
                `;

                // Render page container for print output
                printHtml += `
                    <div class="sheet-print">
                        ${printInnerHtml}
                    </div>
                `;
            }

            document.getElementById('preview-workspace').innerHTML = workspaceHtml;
            document.getElementById('print-workspace').innerHTML = printHtml;
        }

        // Start layout setup on page load
        window.addEventListener('DOMContentLoaded', init);
    </script>
    @endif
</body>
</html>
