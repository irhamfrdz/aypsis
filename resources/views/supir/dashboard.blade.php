<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Supir - AYPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Supir</h1>
                <div class="flex items-center space-x-4">
                    <span class="hidden sm:block text-gray-600">Halo, {{ Auth::user()->name }}!</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-red-500 hover:text-red-700">Logout</button>
                    </form>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="mt-4 border-t pt-4">
                <div class="flex flex-wrap gap-2 sm:gap-4">
                    <a href="{{ route('supir.dashboard') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V9"></path>
                        </svg>
                        Tugas Saya
                    </a>
                    
                    <a href="#" onclick="showRiwayatTugas()" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Riwayat Tugas
                    </a>
                    
                    <a href="#" onclick="showProfilSupir()" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profil Supir
                    </a>
                    
                    <a href="{{ route('supir.ob-muat') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414A1 1 0 0016 10v6a1 1 0 01-1 1z"></path>
                        </svg>
                        OB Muat
                    </a>
                    
                    <a href="{{ route('supir.ob-bongkar') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                        OB Bongkar
                    </a>

                    <a href="{{ route('supir.cek-kendaraan.index') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Cek Kendaraan
                    </a>
                    
                    <a href="#" onclick="showBantuanSupir()" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Bantuan
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <main class="container mx-auto mt-8 px-4 sm:px-6 flex-grow">
        <div class="space-y-8">

            {{-- Section Permohonan --}}
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Memo Permohonan</h2>
                <div class="space-y-6">
                    @forelse($permohonans as $permohonan)
                        @php
                            $sudahCheckpoint = $permohonan->kontainers->isNotEmpty();
                        @endphp
                        <a href="{{ route('supir.checkpoint.create', $permohonan) }}"
                           class="block shadow-md rounded-lg p-6 transition duration-300
                           {{ $sudahCheckpoint ? 'bg-green-50 border border-green-400 hover:bg-green-100' : 'bg-white hover:bg-gray-50' }}">
                            <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                                <h3 class="text-lg font-semibold {{ $sudahCheckpoint ? 'text-green-700' : 'text-indigo-600' }}">{{ $permohonan->nomor_memo }}</h3>
                                <span class="mt-2 sm:mt-0 px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $sudahCheckpoint ? 'bg-green-200 text-green-800' : 'bg-blue-100 text-blue-800' }} self-start">
                                    {{ $permohonan->status }}
                                </span>
                            </div>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm border-t pt-4">
                                <div>
                                    <p class="font-medium text-gray-500">Tujuan</p>
                                    <p class="text-gray-800 font-semibold">{{ $permohonan->tujuan }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Kegiatan</p>
                                    <p class="text-gray-800 font-semibold">{{ $kegiatanMap[$permohonan->kegiatan] ?? ucfirst($permohonan->kegiatan) }}</p>
                                </div>
                            </div>
                            @if($sudahCheckpoint)
                                <div class="mt-4 text-green-700 text-xs font-semibold flex items-center gap-1">
                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Sudah input nomor kontainer
                                </div>
                            @endif
                        </a>
                    @empty
                        <div class="bg-white shadow-md rounded-lg p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Tugas Aktif</h3>
                            <p class="mt-1 text-sm text-gray-500">Saat ini tidak ada memo permohonan yang ditugaskan kepada Anda.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Section Surat Jalan --}}
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Surat Jalan</h2>
                <div class="space-y-6">
                    @forelse($suratJalans as $suratJalan)
                        @php
                            $needsCheckpoint = $suratJalan->status === 'belum masuk checkpoint';
                            $checkpointCompleted = $suratJalan->status === 'checkpoint_completed';
                            $isBongkaran = isset($suratJalan->is_bongkaran) && $suratJalan->is_bongkaran;
                            $checkpointRoute = $isBongkaran 
                                ? route('supir.checkpoint.create-surat-jalan-bongkaran', $suratJalan->id)
                                : route('supir.checkpoint.create-surat-jalan', $suratJalan->id);
                        @endphp
                        <a href="{{ $checkpointRoute }}"
                           class="block shadow-md rounded-lg p-6 transition duration-300
                           {{ $checkpointCompleted ? 'bg-green-50 border border-green-400 hover:bg-green-100' :
                              ($needsCheckpoint ? 'bg-yellow-50 border border-yellow-400 hover:bg-yellow-100' : 'bg-white hover:bg-gray-50') }}">
                            <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                                <h3 class="text-lg font-semibold {{ $checkpointCompleted ? 'text-green-700' : ($needsCheckpoint ? 'text-yellow-700' : 'text-indigo-600') }}">
                                    {{ $suratJalan->no_surat_jalan ?? $suratJalan->nomor_surat_jalan }}
                                    @if($isBongkaran)
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Bongkaran</span>
                                    @endif
                                </h3>
                                <span class="mt-2 sm:mt-0 px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $checkpointCompleted ? 'bg-green-200 text-green-800' :
                                       ($needsCheckpoint ? 'bg-yellow-200 text-yellow-800' : 'bg-blue-100 text-blue-800') }} self-start">
                                    {{ $suratJalan->status }}
                                </span>
                            </div>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm border-t pt-4">
                                <div>
                                    <p class="font-medium text-gray-500">Tanggal</p>
                                    <p class="text-gray-800 font-semibold">{{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Kegiatan</p>
                                    <p class="text-gray-800 font-semibold">{{ $kegiatanMap[$suratJalan->kegiatan] ?? ucfirst($suratJalan->kegiatan) }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Tujuan Pengambilan</p>
                                    <p class="text-gray-800 font-semibold">{{ $suratJalan->tujuan_pengambilan ?? $suratJalan->order->tujuan_ambil ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Tujuan Pengiriman</p>
                                    <p class="text-gray-800 font-semibold">{{ $suratJalan->tujuan_pengiriman ?? $suratJalan->order->tujuan_kirim ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Jumlah Kontainer</p>
                                    <p class="text-gray-800 font-semibold">{{ $suratJalan->jumlah_kontainer ?? 0 }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">No. Kontainer</p>
                                    <p class="text-gray-800 font-semibold">{{ $suratJalan->no_kontainer ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">No. Seal</p>
                                    <p class="text-gray-800 font-semibold">{{ $suratJalan->no_seal ?? '-' }}</p>
                                </div>
                            </div>
                            @if($needsCheckpoint)
                                <div class="mt-4 text-yellow-700 text-xs font-semibold flex items-center gap-1">
                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                                    Perlu input nomor kontainer
                                </div>
                            @elseif($checkpointCompleted)
                                <div class="mt-4 text-green-700 text-xs font-semibold flex items-center gap-1">
                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Checkpoint selesai - {{ $suratJalan->no_kontainer ?? 'Kontainer terinput' }}
                                </div>
                            @endif
                        </a>
                    @empty
                        <div class="bg-white shadow-md rounded-lg p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Surat Jalan</h3>
                            <p class="mt-1 text-sm text-gray-500">Saat ini tidak ada surat jalan yang ditugaskan kepada Anda.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <!-- Modal untuk Riwayat Tugas -->
    <div id="riwayatModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Tugas</h3>
                <button onclick="closeModal('riwayatModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900">Surat Jalan Selesai</h4>
                        <p class="text-sm text-gray-600 mt-1">Menampilkan tugas yang telah diselesaikan dalam 30 hari terakhir</p>
                        <div class="mt-3 text-center text-gray-500">
                            <p class="text-sm">Fitur riwayat tugas akan segera tersedia</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Profil Supir -->
    <div id="profilModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Profil Supir</h3>
                <button onclick="closeModal('profilModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900">{{ Auth::user()->name }}</h4>
                    <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                    <div class="mt-4 space-y-2 text-left">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Role:</span>
                            <span class="text-sm font-medium">Supir</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Status:</span>
                            <span class="text-sm font-medium text-green-600">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Bantuan -->
    <div id="bantuanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Bantuan & Panduan</h3>
                <button onclick="closeModal('bantuanModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <div class="space-y-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">📋 Cara Menggunakan Dashboard</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p>• <strong>Memo Permohonan:</strong> Klik untuk input nomor kontainer yang akan diambil</p>
                            <p>• <strong>Surat Jalan:</strong> Klik untuk melakukan checkpoint dan input detail kontainer</p>
                            <p>• <strong>Status Hijau:</strong> Tugas sudah selesai dikerjakan</p>
                            <p>• <strong>Status Kuning:</strong> Tugas perlu dikerjakan segera</p>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">🚛 Proses Checkpoint</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p>• Pastikan nomor kontainer sesuai dengan yang tertera di lokasi</p>
                            <p>• Input nomor seal jika tersedia</p>
                            <p>• Periksa kondisi kontainer sebelum melakukan checkpoint</p>
                            <p>• Hubungi supervisor jika ada masalah atau kerusakan</p>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">📞 Kontak Bantuan</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p>• <strong>IT Support:</strong> ext. 101</p>
                            <p>• <strong>Supervisor:</strong> ext. 102</p>
                            <p>• <strong>Admin:</strong> ext. 103</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showRiwayatTugas() {
            const modal = document.getElementById('riwayatModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        function showProfilSupir() {
            const modal = document.getElementById('profilModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        function showBantuanSupir() {
            const modal = document.getElementById('bantuanModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('bg-opacity-50')) {
                closeModal(e.target.id);
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modals = ['riwayatModal', 'profilModal', 'bantuanModal'];
                modals.forEach(modalId => {
                    closeModal(modalId);
                });
            }
        });
        
        // Auto refresh dashboard every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
