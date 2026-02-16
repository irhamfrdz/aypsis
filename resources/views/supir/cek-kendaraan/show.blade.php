<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Cek Kendaraan - AYPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .gradient-bg { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); }
    </style>
</head>
<body class="min-h-screen pb-12">
    <div class="gradient-bg h-48 w-full absolute top-0 left-0 z-0"></div>
    
    <div class="relative z-10 container mx-auto px-4 pt-8">
        <div class="flex items-center justify-between mb-8 text-white">
            <div class="flex items-center space-x-4">
                <a href="{{ route('supir.cek-kendaraan.index') }}" class="p-2 bg-white/20 hover:bg-white/30 rounded-full transition-all backdrop-blur-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold">Detail Pengecekan</h1>
                    <p class="text-white/80 transition-all">{{ $cekKendaraan->mobil->nomor_polisi }} • {{ $cekKendaraan->tanggal->format('d M Y') }}</p>
                </div>
            </div>
            <div class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl border border-white/30 text-center">
                <div class="text-xs uppercase tracking-widest opacity-70">Odometer</div>
                <div class="text-xl font-bold">{{ number_format($cekKendaraan->odometer, 0, ',', '.') }} KM</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Left Panel: Summary & Info -->
            <div class="space-y-6">
                <div class="glass-card rounded-2xl p-6 shadow-xl">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Informasi Unit</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-500">Merek</span>
                            <span class="font-semibold">{{ $cekKendaraan->mobil->merek }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-500">Berlaku STNK</span>
                            <span class="font-semibold {{ $cekKendaraan->masa_berlaku_stnk && $cekKendaraan->masa_berlaku_stnk->isPast() ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $cekKendaraan->masa_berlaku_stnk ? $cekKendaraan->masa_berlaku_stnk->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-500">Berlaku KIR</span>
                            <span class="font-semibold {{ $cekKendaraan->masa_berlaku_kir && $cekKendaraan->masa_berlaku_kir->isPast() ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $cekKendaraan->masa_berlaku_kir ? $cekKendaraan->masa_berlaku_kir->format('d/m/Y') : '-' }}
                            </span>
                        </div>

                        <div class="py-2">
                            <span class="text-gray-500 block mb-1">Pemeriksa</span>
                            <span class="font-semibold block">{{ $cekKendaraan->karyawan->nama_lengkap }}</span>
                        </div>
                    </div>
                </div>

                @if($cekKendaraan->foto_sebelum || $cekKendaraan->foto_sesudah)
                <div class="glass-card rounded-2xl p-6 shadow-xl">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Dokumentasi</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @if($cekKendaraan->foto_sebelum)
                        <div class="space-y-1">
                            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                <img src="{{ asset('storage/' . $cekKendaraan->foto_sebelum) }}" class="w-full h-full object-cover">
                            </div>
                            <span class="text-[10px] text-gray-500 uppercase text-center block">Sebelum</span>
                        </div>
                        @endif
                        @if($cekKendaraan->foto_sesudah)
                        <div class="space-y-1">
                            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                <img src="{{ asset('storage/' . $cekKendaraan->foto_sesudah) }}" class="w-full h-full object-cover">
                            </div>
                            <span class="text-[10px] text-gray-500 uppercase text-center block border-indigo-400">Sesudah</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Panel: Checklist Details -->
            <div class="md:col-span-2">
                <div class="glass-card rounded-2xl p-6 shadow-xl">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Hasil Checklist</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @php
                            $items = [
                                'oli_mesin' => 'Oli Mesin',
                                'air_radiator' => 'Air Radiator',
                                'minyak_rem' => 'Minyak Rem',
                                'air_wiper' => 'Air Wiper',
                                'lampu_depan' => 'Lampu Depan',
                                'lampu_belakang' => 'Lampu Belakang',
                                'lampu_sein' => 'Lampu Sein (L/R)',
                                'lampu_rem' => 'Lampu Rem',
                                'kondisi_ban' => 'Kondisi Fisik Ban',
                                'tekanan_ban' => 'Tekanan Ban',
                                'aki' => 'Kondisi Aki',
                                'fungsi_rem' => 'Fungsi Rem',
                                'fungsi_kopling' => 'Fungsi Kopling',
                                'kebersihan_interior' => 'Kebersihan Dalam',
                                'kebersihan_eksterior' => 'Kebersihan Luar',
                            ];
                        @endphp

                        @foreach($items as $key => $label)
                        <div class="flex items-center justify-between p-4 rounded-xl border {{ $cekKendaraan->$key === 'baik' ? 'bg-green-50 border-green-100' : ($cekKendaraan->$key === 'perlu_cek' ? 'bg-yellow-50 border-yellow-100' : 'bg-red-50 border-red-100') }}">
                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            <div class="flex items-center">
                                @if($cekKendaraan->$key === 'baik')
                                    <span class="text-xs font-bold text-green-700 uppercase">BAIK</span>
                                    <svg class="w-5 h-5 ml-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                @elseif($cekKendaraan->$key === 'perlu_cek')
                                    <span class="text-xs font-bold text-yellow-700 uppercase">PERLU CEK</span>
                                    <svg class="w-5 h-5 ml-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                @else
                                    <span class="text-xs font-bold text-red-700 uppercase">BURUK</span>
                                    <svg class="w-5 h-5 ml-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                @endif
                            </div>
                        </div>
                        @endforeach

                        <div class="flex items-center justify-between p-4 rounded-xl border {{ $cekKendaraan->kotak_p3k === 'tidak_kadaluarsa' ? 'bg-green-50 border-green-100' : 'bg-red-50 border-red-100' }}">
                            <span class="text-sm font-medium text-gray-700">Kotak P3K</span>
                            <div class="flex items-center">
                                @if($cekKendaraan->kotak_p3k === 'tidak_kadaluarsa')
                                    <span class="text-xs font-bold text-green-700 uppercase">TIDAK KADALUARSA</span>
                                    <svg class="w-5 h-5 ml-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                @else
                                    <span class="text-xs font-bold text-red-700 uppercase">KADALUARSA</span>
                                    <svg class="w-5 h-5 ml-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-xl border {{ $cekKendaraan->plat_no_belakang === 'ada' ? 'bg-green-50 border-green-100' : 'bg-red-50 border-red-100' }}">
                            <span class="text-sm font-medium text-gray-700">Plat No Belakang</span>
                            <div class="flex items-center">
                                @if($cekKendaraan->plat_no_belakang === 'ada')
                                    <span class="text-xs font-bold text-green-700 uppercase">ADA</span>
                                    <svg class="w-5 h-5 ml-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                @else
                                    <span class="text-xs font-bold text-red-700 uppercase">TIDAK ADA</span>
                                    <svg class="w-5 h-5 ml-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-xl border {{ $cekKendaraan->lampu_besar_dekat_kanan === 'berfungsi' ? 'bg-green-50 border-green-100' : 'bg-red-50 border-red-100' }}">
                            <span class="text-sm font-medium text-gray-700">Lampu Besar Dekat Kanan</span>
                            <div class="flex items-center">
                                @if($cekKendaraan->lampu_besar_dekat_kanan === 'berfungsi')
                                    <span class="text-xs font-bold text-green-700 uppercase">BERFUNGSI</span>
                                    <svg class="w-5 h-5 ml-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                @else
                                    <span class="text-xs font-bold text-red-700 uppercase">TIDAK BERFUNGSI</span>
                                    <svg class="w-5 h-5 ml-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-xl border {{ $cekKendaraan->lampu_besar_dekat_kiri === 'berfungsi' ? 'bg-green-50 border-green-100' : 'bg-red-50 border-red-100' }}">
                            <span class="text-sm font-medium text-gray-700">Lampu Besar Dekat Kiri</span>
                            <div class="flex items-center">
                                @if($cekKendaraan->lampu_besar_dekat_kiri === 'berfungsi')
                                    <span class="text-xs font-bold text-green-700 uppercase">BERFUNGSI</span>
                                    <svg class="w-5 h-5 ml-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                @else
                                    <span class="text-xs font-bold text-red-700 uppercase">TIDAK BERFUNGSI</span>
                                    <svg class="w-5 h-5 ml-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-xl border {{ $cekKendaraan->lampu_rem_kanan === 'berfungsi' ? 'bg-green-50 border-green-100' : 'bg-red-50 border-red-100' }}">
                            <span class="text-sm font-medium text-gray-700">Lampu Rem Kanan</span>
                            <div class="flex items-center">
                                @if($cekKendaraan->lampu_rem_kanan === 'berfungsi')
                                    <span class="text-xs font-bold text-green-700 uppercase">BERFUNGSI</span>
                                    <svg class="w-5 h-5 ml-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                @else
                                    <span class="text-xs font-bold text-red-700 uppercase">TIDAK BERFUNGSI</span>
                                    <svg class="w-5 h-5 ml-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($cekKendaraan->catatan)
                    <div class="mt-8 p-6 rounded-2xl bg-gray-50 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Catatan Driver</h4>
                        <p class="text-gray-700 text-sm leading-relaxed">{{ $cekKendaraan->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
