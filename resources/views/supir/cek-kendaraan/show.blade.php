@extends('layouts.supir')

@section('title', 'Detail Cek Kendaraan - AYPSIS')

@section('page_title', 'Detail Pengecekan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('supir.cek-kendaraan.index') }}" class="p-2 bg-white hover:bg-gray-100 rounded-full transition-all border shadow-sm">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Pengecekan</h1>
                <p class="text-gray-600 mt-1">{{ $cekKendaraan->mobil->nomor_polisi }} • {{ $cekKendaraan->tanggal->format('d M Y') }}</p>
            </div>
        </div>
        <div class="bg-indigo-600 text-white px-6 py-3 rounded-2xl shadow-lg shadow-indigo-100 text-center">
            <div class="text-[10px] uppercase font-bold tracking-widest opacity-80 mb-1">Odometer</div>
            <div class="text-xl font-black">{{ number_format($cekKendaraan->odometer, 0, ',', '.') }} <span class="text-sm font-medium">KM</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Panel: Summary & Info -->
        <div class="space-y-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-6 flex items-center">
                    <i class="fas fa-truck mr-2"></i> Informasi Unit
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">Merek / Tipe</span>
                        <span class="font-bold text-gray-800">{{ $cekKendaraan->mobil->merek }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">Pemeriksa</span>
                        <span class="font-bold text-gray-800">{{ $cekKendaraan->karyawan->nama_lengkap }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">Berlaku STNK</span>
                        <span class="font-bold {{ $cekKendaraan->masa_berlaku_stnk && $cekKendaraan->masa_berlaku_stnk->isPast() ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $cekKendaraan->masa_berlaku_stnk ? $cekKendaraan->masa_berlaku_stnk->format('d/m/Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-500 text-sm">Berlaku KIR</span>
                        <span class="font-bold {{ $cekKendaraan->masa_berlaku_kir && $cekKendaraan->masa_berlaku_kir->isPast() ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $cekKendaraan->masa_berlaku_kir ? $cekKendaraan->masa_berlaku_kir->format('d/m/Y') : '-' }}
                        </span>
                    </div>
                </div>
            </div>

            @if($cekKendaraan->foto_sebelum || $cekKendaraan->foto_sesudah)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-6 flex items-center">
                    <i class="fas fa-camera mr-2"></i> Dokumentasi
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    @if($cekKendaraan->foto_sebelum)
                    <div class="space-y-2">
                        <div class="aspect-square bg-gray-50 rounded-xl overflow-hidden border border-gray-100 shadow-inner group relative">
                            <img src="{{ asset('storage/' . $cekKendaraan->foto_sebelum) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-2">
                                <span class="text-[10px] text-white font-bold uppercase">Sebelum</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($cekKendaraan->foto_sesudah)
                    <div class="space-y-2">
                        <div class="aspect-square bg-gray-50 rounded-xl overflow-hidden border border-gray-100 shadow-inner group relative">
                            <img src="{{ asset('storage/' . $cekKendaraan->foto_sesudah) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-2">
                                <span class="text-[10px] text-white font-bold uppercase">Sesudah</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($cekKendaraan->catatan)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-4 flex items-center">
                    <i class="fas fa-comment-alt mr-2"></i> Catatan Driver
                </h3>
                <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100 italic text-gray-700 text-sm leading-relaxed">
                    "{{ $cekKendaraan->catatan }}"
                </div>
            </div>
            @endif
        </div>

        <!-- Right Panel: Checklist Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-8 py-6 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <span class="w-1.5 h-6 bg-indigo-600 rounded-full mr-3"></span>
                        Hasil Pemeriksaan Rutin
                    </h3>
                    <div class="flex items-center gap-4 text-xs font-bold text-gray-500">
                        <div class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-green-500 mr-1.5"></span> Baik</div>
                        <div class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-red-500 mr-1.5"></span> Cek/Rusak</div>
                    </div>
                </div>
                
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">
                        @php
                            $checklistItems = [
                                ['key' => 'kotak_p3k', 'label' => 'Kotak P3K', 'type' => 'status_kadaluarsa'],
                                ['key' => 'racun_api', 'label' => 'Racun Api / APAR', 'type' => 'status_ada'],
                                ['key' => 'plat_no_depan', 'label' => 'Plat No Depan', 'type' => 'status_ada'],
                                ['key' => 'plat_no_belakang', 'label' => 'Plat No Belakang', 'type' => 'status_ada'],
                                ['key' => 'lampu_jauh_kanan', 'label' => 'Lampu Besar Jauh Kanan', 'type' => 'status_fungsi'],
                                ['key' => 'lampu_jauh_kiri', 'label' => 'Lampu Besar Jauh Kiri', 'type' => 'status_fungsi'],
                                ['key' => 'lampu_dekat_kanan', 'label' => 'Lampu Besar Dekat Kanan', 'type' => 'status_fungsi'],
                                ['key' => 'lampu_dekat_kiri', 'label' => 'Lampu Besar Dekat Kiri', 'type' => 'status_fungsi'],
                                ['key' => 'lampu_sein_depan_kanan', 'label' => 'Lampu Belok Depan Kanan', 'type' => 'status_fungsi'],
                                ['key' => 'lampu_sein_depan_kiri', 'label' => 'Lampu Belok Depan Kiri', 'type' => 'status_fungsi'],
                                ['key' => 'lampu_sein_belakang_kanan', 'label' => 'Lampu Belok Belakang Kanan', 'type' => 'status_fungsi'],
                                ['key' => 'lampu_sein_belakang_kiri', 'label' => 'Lampu Belok Belakang Kiri', 'type' => 'status_fungsi'],
                                ['key' => 'lampu_rem_kanan', 'label' => 'Lampu Rem Kanan', 'type' => 'status_fungsi'],
                                ['key' => 'lampu_rem_kiri', 'label' => 'Lampu Rem Kiri', 'type' => 'status_fungsi'],
                                ['key' => 'lampu_mundur_kanan', 'label' => 'Lampu Alarm Mundur Kanan', 'type' => 'status_fungsi'],
                                ['key' => 'lampu_mundur_kiri', 'label' => 'Lampu Alarm Mundur Kiri', 'type' => 'status_fungsi'],
                                ['key' => 'sabuk_pengaman_kanan', 'label' => 'Sabuk Pengaman Kanan', 'type' => 'status_fungsi'],
                                ['key' => 'sabuk_pengaman_kiri', 'label' => 'Sabuk Pengaman Kiri', 'type' => 'status_fungsi'],
                                ['key' => 'kamvas_rem_depan_kanan', 'label' => 'Kamvas Rem Depan Kanan', 'type' => 'status_fungsi'],
                                ['key' => 'kamvas_rem_depan_kiri', 'label' => 'Kamvas Rem Depan Kiri', 'type' => 'status_fungsi'],
                                ['key' => 'kamvas_rem_belakang_kanan', 'label' => 'Kamvas Rem Belakang Kanan', 'type' => 'status_fungsi'],
                                ['key' => 'kamvas_rem_belakang_kiri', 'label' => 'Kamvas Rem Belakang Kiri', 'type' => 'status_fungsi'],
                                ['key' => 'spion_kanan', 'label' => 'Kaca Spion Luar Kanan', 'type' => 'status_fungsi'],
                                ['key' => 'spion_kiri', 'label' => 'Kaca Spion Luar Kiri', 'type' => 'status_fungsi'],
                                ['key' => 'tekanan_ban_depan_kanan', 'label' => 'Tekanan Ban Depan Kanan', 'type' => 'status_fungsi'],
                                ['key' => 'tekanan_ban_depan_kiri', 'label' => 'Tekanan Ban Depan Kiri', 'type' => 'status_fungsi'],
                                ['key' => 'tekanan_ban_belakang_kanan', 'label' => 'Tekanan Ban Belakang Kanan', 'type' => 'status_fungsi'],
                                ['key' => 'tekanan_ban_belakang_kiri', 'label' => 'Tekanan Ban Belakang Kiri', 'type' => 'status_fungsi'],
                                ['key' => 'ganjelan_ban', 'label' => 'Ganjelan Ban', 'type' => 'status_ada'],
                                ['key' => 'trakel_sabuk', 'label' => 'Trakel Sabuk', 'type' => 'status_ada'],
                                ['key' => 'twist_lock_kontainer', 'label' => 'Twist Lock Kontainer', 'type' => 'status_fungsi'],
                                ['key' => 'landing_buntut', 'label' => 'Landing Buntut', 'type' => 'status_fungsi'],
                                ['key' => 'patok_besi', 'label' => 'Patok Besi', 'type' => 'status_ada'],
                                ['key' => 'tutup_tangki', 'label' => 'Tutup Tangki', 'type' => 'status_ada'],
                                ['key' => 'lampu_no_plat', 'label' => 'Lampu No Plat', 'type' => 'status_fungsi'],
                                ['key' => 'lampu_bahaya', 'label' => 'Lampu Bahaya', 'type' => 'status_fungsi'],
                                ['key' => 'klakson', 'label' => 'Klakson', 'type' => 'status_fungsi'],
                                ['key' => 'radio', 'label' => 'Radio', 'type' => 'status_fungsi'],
                                ['key' => 'rem_tangan', 'label' => 'Rem Tangan', 'type' => 'status_fungsi'],
                                ['key' => 'pedal_gas', 'label' => 'Pedal Gas', 'type' => 'status_fungsi'],
                                ['key' => 'pedal_rem', 'label' => 'Pedal Rem', 'type' => 'status_fungsi'],
                                ['key' => 'porseneling', 'label' => 'Porseneling', 'type' => 'status_fungsi'],
                                ['key' => 'antena_radio', 'label' => 'Antena Radio', 'type' => 'status_ada'],
                                ['key' => 'speaker', 'label' => 'Speaker', 'type' => 'status_fungsi'],
                                ['key' => 'spion_dalam', 'label' => 'Kaca Spion Dalam', 'type' => 'status_fungsi'],
                                ['key' => 'dongkrak', 'label' => 'Dongkrak', 'type' => 'status_ada'],
                                ['key' => 'tangkai_dongkrak', 'label' => 'Tangkai Dongkrak', 'type' => 'status_ada'],
                                ['key' => 'kunci_roda', 'label' => 'Kunci Roda', 'type' => 'status_ada'],
                                ['key' => 'dop_roda', 'label' => 'Dop Roda', 'type' => 'status_ada'],
                                ['key' => 'wiper_depan', 'label' => 'Wiper Depan', 'type' => 'status_fungsi'],
                                ['key' => 'oli_mesin', 'label' => 'Oli Mesin', 'type' => 'status_baik'],
                                ['key' => 'air_radiator', 'label' => 'Air Radiator', 'type' => 'status_baik'],
                                ['key' => 'minyak_rem', 'label' => 'Minyak Rem', 'type' => 'status_baik'],
                                ['key' => 'air_wiper', 'label' => 'Air Wiper', 'type' => 'status_baik'],
                                ['key' => 'kondisi_aki', 'label' => 'Kondisi Aki', 'type' => 'status_baik'],
                                ['key' => 'pengukur_tekanan_ban', 'label' => 'Pengukur Tekanan Ban', 'type' => 'status_ada'],
                                ['key' => 'segitiga_pengaman', 'label' => 'Segitiga Pengaman', 'type' => 'status_ada'],
                                ['key' => 'jumlah_ban_serep', 'label' => 'Jumlah Ban Serep', 'type' => 'status_jumlah'],
                            ];
                        @endphp

                        @foreach($checklistItems as $item)
                            @php
                                $val = $cekKendaraan->{$item['key']};
                                $isGood = false;
                                
                                if (in_array($val, ['tidak_kadaluarsa', 'ada', 'berfungsi', 'baik'])) {
                                    $isGood = true;
                                } elseif (is_numeric($val) && (int)$val > 0) {
                                    $isGood = true;
                                }

                                // Formatting display name
                                $displayVal = str_replace('_', ' ', $val);
                            @endphp
                            <div class="flex items-center justify-between py-2 border-b border-gray-50 group hover:bg-gray-50/50 transition-all rounded-lg px-2 -mx-2">
                                <span class="text-sm font-semibold text-gray-700">{{ $item['label'] }}</span>
                                <div class="flex items-center">
                                    <span class="text-xs font-bold uppercase mr-3 {{ $isGood ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $displayVal }}
                                    </span>
                                    @if($isGood)
                                        <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]"></div>
                                    @else
                                        <div class="w-2 h-2 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,44,44,0.4)]"></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
