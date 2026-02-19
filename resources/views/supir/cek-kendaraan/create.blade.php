@extends('layouts.supir')

@section('title', 'Cek Kendaraan - AYPSIS')

@section('page_title', 'Pengecekan Kendaraan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center space-x-3 sm:space-x-4">
            <a href="{{ route('supir.dashboard') }}" class="p-2 sm:p-2.5 bg-white hover:bg-gray-100 rounded-xl transition-all border border-gray-200 shadow-sm text-gray-400 hover:text-gray-600">
                <i class="fas fa-chevron-left sm:text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Cek Kendaraan</h1>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Formulir pemeriksaan rutin unit</p>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-400 text-xl mr-3"></i>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-8">
        <div class="flex">
            <i class="fas fa-exclamation-circle text-red-400 text-xl mr-3"></i>
            <div>
                <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('supir.cek-kendaraan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Basic Info -->
            <div class="space-y-8">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-gray-100 border border-gray-100">
                    <h2 class="text-lg font-black text-gray-900 mb-6 flex items-center">
                        <span class="w-1.5 h-6 bg-indigo-600 rounded-full mr-3"></span>
                        Informasi Dasar
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kendaraan <span class="text-red-500">*</span></label>
                            <select name="mobil_id" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 transition-all text-sm px-4 py-3 bg-gray-50" required>
                                <option value="">-- Pilih Kendaraan --</option>
                                @foreach($mobils as $mobil)
                                    <option value="{{ $mobil->id }}" 
                                            {{ $defaultMobilId == $mobil->id ? 'selected' : '' }}
                                            data-stnk="{{ $mobil->pajak_stnk ? $mobil->pajak_stnk->format('Y-m-d') : '' }}"
                                            data-kir="{{ $mobil->pajak_kir ? $mobil->pajak_kir->format('Y-m-d') : '' }}">
                                        {{ $mobil->nomor_polisi }} ({{ $mobil->merek }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-gray-50" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jam</label>
                                <input type="time" name="jam" value="{{ date('H:i') }}" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-gray-50" required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Odometer (KM)</label>
                            <div class="relative">
                                <input type="number" name="odometer" placeholder="KM" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-gray-50">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-semibold">KM</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Berlaku STNK</label>
                                <input type="date" name="masa_berlaku_stnk" id="masa_berlaku_stnk" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Berlaku KIR</label>
                                <input type="date" name="masa_berlaku_kir" id="masa_berlaku_kir" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 bg-gray-50">
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Middle & Right Columns: Checklist -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-gray-100 border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <span class="w-1.5 h-6 bg-indigo-600 rounded-full mr-3"></span>
                            Checklist Pemeriksaan
                        </h2>
                        <span class="text-xs font-semibold bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-full border border-indigo-100 uppercase tracking-wider">
                            Total 57 Item
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @php
                            $items = [
                                ['name' => 'kotak_p3k', 'label' => 'Kotak P3K', 'type' => 'status_kadaluarsa'],
                                ['name' => 'racun_api', 'label' => 'Racun Api / APAR', 'type' => 'status_kadaluarsa'],
                                ['name' => 'plat_no_depan', 'label' => 'Plat No Depan', 'type' => 'status_ada'],
                                ['name' => 'plat_no_belakang', 'label' => 'Plat No Belakang', 'type' => 'status_ada'],
                                ['name' => 'lampu_jauh_kanan', 'label' => 'Lampu Besar Jauh Kanan', 'type' => 'status_fungsi'],
                                ['name' => 'lampu_jauh_kiri', 'label' => 'Lampu Besar Jauh Kiri', 'type' => 'status_fungsi'],
                                ['name' => 'lampu_dekat_kanan', 'label' => 'Lampu Besar Dekat Kanan', 'type' => 'status_fungsi'],
                                ['name' => 'lampu_dekat_kiri', 'label' => 'Lampu Besar Dekat Kiri', 'type' => 'status_fungsi'],
                                ['name' => 'lampu_sein_depan_kanan', 'label' => 'Lampu Belok Depan Kanan', 'type' => 'status_fungsi'],
                                ['name' => 'lampu_sein_depan_kiri', 'label' => 'Lampu Belok Depan Kiri', 'type' => 'status_fungsi'],
                                ['name' => 'lampu_sein_belakang_kanan', 'label' => 'Lampu Belok Belakang Kanan', 'type' => 'status_fungsi'],
                                ['name' => 'lampu_sein_belakang_kiri', 'label' => 'Lampu Belok Belakang Kiri', 'type' => 'status_fungsi'],
                                ['name' => 'lampu_rem_kanan', 'label' => 'Lampu Rem Kanan', 'type' => 'status_fungsi'],
                                ['name' => 'lampu_rem_kiri', 'label' => 'Lampu Rem Kiri', 'type' => 'status_fungsi'],
                                ['name' => 'lampu_mundur_kanan', 'label' => 'Lampu Alarm Mundur Kanan', 'type' => 'status_fungsi'],
                                ['name' => 'lampu_mundur_kiri', 'label' => 'Lampu Alarm Mundur Kiri', 'type' => 'status_fungsi'],
                                ['name' => 'sabuk_pengaman_kanan', 'label' => 'Sabuk Pengaman Kanan', 'type' => 'status_fungsi'],
                                ['name' => 'sabuk_pengaman_kiri', 'label' => 'Sabuk Pengaman Kiri', 'type' => 'status_fungsi'],
                                ['name' => 'kamvas_rem_depan_kanan', 'label' => 'Kamvas Rem Depan Kanan', 'type' => 'status_fungsi'],
                                ['name' => 'kamvas_rem_depan_kiri', 'label' => 'Kamvas Rem Depan Kiri', 'type' => 'status_fungsi'],
                                ['name' => 'kamvas_rem_belakang_kanan', 'label' => 'Kamvas Rem Belakang Kanan', 'type' => 'status_fungsi'],
                                ['name' => 'kamvas_rem_belakang_kiri', 'label' => 'Kamvas Rem Belakang Kiri', 'type' => 'status_fungsi'],
                                ['name' => 'spion_kanan', 'label' => 'Kaca Spion Luar Kanan', 'type' => 'status_fungsi'],
                                ['name' => 'spion_kiri', 'label' => 'Kaca Spion Luar Kiri', 'type' => 'status_fungsi'],
                                ['name' => 'tekanan_ban_depan_kanan', 'label' => 'Tekanan Ban Depan Kanan', 'type' => 'status_fungsi'],
                                ['name' => 'tekanan_ban_depan_kiri', 'label' => 'Tekanan Ban Depan Kiri', 'type' => 'status_fungsi'],
                                ['name' => 'tekanan_ban_belakang_kanan', 'label' => 'Tekanan Ban Belakang Kanan', 'type' => 'status_fungsi'],
                                ['name' => 'tekanan_ban_belakang_kiri', 'label' => 'Tekanan Ban Belakang Kiri', 'type' => 'status_fungsi'],
                                ['name' => 'ganjelan_ban', 'label' => 'Ganjelan Ban', 'type' => 'status_ada'],
                                ['name' => 'trakel_sabuk', 'label' => 'Trakel Sabuk', 'type' => 'status_ada'],
                                ['name' => 'twist_lock_kontainer', 'label' => 'Twist Lock Kontainer', 'type' => 'status_fungsi'],
                                ['name' => 'landing_buntut', 'label' => 'Landing Buntut', 'type' => 'status_fungsi'],
                                ['name' => 'patok_besi', 'label' => 'Patok Besi', 'type' => 'status_ada'],
                                ['name' => 'tutup_tangki', 'label' => 'Tutup Tangki', 'type' => 'status_ada'],
                                ['name' => 'lampu_no_plat', 'label' => 'Lampu No Plat', 'type' => 'status_fungsi'],
                                ['name' => 'lampu_bahaya', 'label' => 'Lampu Bahaya', 'type' => 'status_fungsi'],
                                ['name' => 'klakson', 'label' => 'Klakson', 'type' => 'status_fungsi'],
                                ['name' => 'radio', 'label' => 'Radio', 'type' => 'status_fungsi'],
                                ['name' => 'rem_tangan', 'label' => 'Rem Tangan', 'type' => 'status_fungsi'],
                                ['name' => 'pedal_gas', 'label' => 'Pedal Gas', 'type' => 'status_fungsi'],
                                ['name' => 'pedal_rem', 'label' => 'Pedal Rem', 'type' => 'status_fungsi'],
                                ['name' => 'porseneling', 'label' => 'Porseneling', 'type' => 'status_fungsi'],
                                ['name' => 'antena_radio', 'label' => 'Antena Radio', 'type' => 'status_ada'],
                                ['name' => 'speaker', 'label' => 'Speaker', 'type' => 'status_fungsi'],
                                ['name' => 'spion_dalam', 'label' => 'Kaca Spion Dalam', 'type' => 'status_fungsi'],
                                ['name' => 'dongkrak', 'label' => 'Dongkrak', 'type' => 'status_ada'],
                                ['name' => 'tangkai_dongkrak', 'label' => 'Tangkai Dongkrak', 'type' => 'status_ada'],
                                ['name' => 'kunci_roda', 'label' => 'Kunci Roda', 'type' => 'status_ada'],
                                ['name' => 'dop_roda', 'label' => 'Dop Roda', 'type' => 'status_ada'],
                                ['name' => 'wiper_depan', 'label' => 'Wiper Depan', 'type' => 'status_fungsi'],
                                ['name' => 'oli_mesin', 'label' => 'Oli Mesin', 'type' => 'status_baik'],
                                ['name' => 'air_radiator', 'label' => 'Air Radiator', 'type' => 'status_baik'],
                                ['name' => 'minyak_rem', 'label' => 'Minyak Rem', 'type' => 'status_baik'],
                                ['name' => 'air_wiper', 'label' => 'Air Wiper', 'type' => 'status_baik'],
                                ['name' => 'kondisi_aki', 'label' => 'Kondisi Aki', 'type' => 'status_baik'],
                                ['name' => 'pengukur_tekanan_ban', 'label' => 'Pengukur Tekanan Ban', 'type' => 'status_ada'],
                                ['name' => 'segitiga_pengaman', 'label' => 'Segitiga Pengaman', 'type' => 'status_ada'],
                                ['name' => 'jumlah_ban_serep', 'label' => 'Jumlah Ban Serep', 'type' => 'status_jumlah'],
                            ];
                        @endphp

                        @foreach($items as $item)
                        <div class="p-4 sm:p-5 rounded-2xl bg-gray-50 border border-gray-100 hover:border-indigo-200 hover:bg-white transition-all duration-200 group">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-1 group-hover:text-indigo-600 transition-colors">{{ $item['label'] }}</label>
                            <div class="flex flex-row gap-2 sm:gap-3">
                                @if($item['type'] == 'status_kadaluarsa')
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="{{ $item['name'] }}" value="tidak_kadaluarsa" class="hidden peer" checked>
                                        <div class="px-2 py-2 text-[11px] font-bold text-center rounded-lg border bg-white text-gray-400 peer-checked:bg-green-600 peer-checked:border-green-600 peer-checked:text-white transition-all">
                                            TIDAK KADALUARSA
                                        </div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="{{ $item['name'] }}" value="kadaluarsa" class="hidden peer">
                                        <div class="px-2 py-2 text-[11px] font-bold text-center rounded-lg border bg-white text-gray-400 peer-checked:bg-red-600 peer-checked:border-red-600 peer-checked:text-white transition-all">
                                            KADALUARSA
                                        </div>
                                    </label>
                                @elseif($item['type'] == 'status_ada')
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="{{ $item['name'] }}" value="ada" class="hidden peer" checked>
                                        <div class="px-2 py-2 text-[11px] font-bold text-center rounded-lg border bg-white text-gray-400 peer-checked:bg-green-600 peer-checked:border-green-600 peer-checked:text-white transition-all uppercase">
                                            Ada
                                        </div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="{{ $item['name'] }}" value="tidak_ada" class="hidden peer">
                                        <div class="px-2 py-2 text-[11px] font-bold text-center rounded-lg border bg-white text-gray-400 peer-checked:bg-red-600 peer-checked:border-red-600 peer-checked:text-white transition-all uppercase">
                                            Tidak Ada
                                        </div>
                                    </label>
                                @elseif($item['type'] == 'status_fungsi')
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="{{ $item['name'] }}" value="berfungsi" class="hidden peer" checked>
                                        <div class="px-2 py-2 text-[11px] font-bold text-center rounded-lg border bg-white text-gray-400 peer-checked:bg-green-600 peer-checked:border-green-600 peer-checked:text-white transition-all uppercase">
                                            Berfungsi
                                        </div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="{{ $item['name'] }}" value="tidak_berfungsi" class="hidden peer">
                                        <div class="px-2 py-2 text-[11px] font-bold text-center rounded-lg border bg-white text-gray-400 peer-checked:bg-red-600 peer-checked:border-red-600 peer-checked:text-white transition-all uppercase">
                                            Rusak
                                        </div>
                                    </label>
                                @elseif($item['type'] == 'status_baik')
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="{{ $item['name'] }}" value="baik" class="hidden peer" checked>
                                        <div class="px-2 py-2 text-[11px] font-bold text-center rounded-lg border bg-white text-gray-400 peer-checked:bg-green-600 peer-checked:border-green-600 peer-checked:text-white transition-all uppercase">
                                            Baik
                                        </div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="{{ $item['name'] }}" value="tidak_baik" class="hidden peer">
                                        <div class="px-2 py-2 text-[11px] font-bold text-center rounded-lg border bg-white text-gray-400 peer-checked:bg-red-600 peer-checked:border-red-600 peer-checked:text-white transition-all uppercase">
                                            Buruk
                                        </div>
                                    </label>
                                @elseif($item['type'] == 'status_jumlah')
                                    <div class="w-full">
                                        <input type="number" name="{{ $item['name'] }}" value="0" min="0" 
                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm font-medium bg-white"
                                            placeholder="Masukan jumlah pcs...">
                                    </div>
                                @else
                                    <div class="w-full text-center text-red-400 py-2 border-2 border-dashed border-red-50 rounded-xl text-xs">
                                        Error: Tipe input tidak valid
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        <label class="block text-sm font-semibold text-gray-800 mb-3">Catatan Tambahan</label>
                        <textarea name="catatan" rows="4" class="w-full rounded-2xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 p-4 text-sm" placeholder="Contoh: Lampu depan kiri agak redup, butuh ganti bohlam..."></textarea>
                    </div>
                    
                    <div class="mt-10 flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-gray-50 pt-8">
                        <a href="{{ route('supir.dashboard') }}" class="w-full sm:w-auto px-8 py-4 bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 font-black rounded-2xl transition-all text-center uppercase tracking-widest text-[10px]">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 transition-all active:scale-95 uppercase tracking-widest text-xs">
                            Simpan Data Pengecekan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.querySelector('select[name="mobil_id"]').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        const stnkDate = selectedOption.getAttribute('data-stnk');
        if (stnkDate) {
            document.getElementById('masa_berlaku_stnk').value = stnkDate;
        }

        const kirDate = selectedOption.getAttribute('data-kir');
        if (kirDate) {
            document.getElementById('masa_berlaku_kir').value = kirDate;
        }
    });

    // Trigger on load for default selection
    window.addEventListener('DOMContentLoaded', () => {
        const select = document.querySelector('select[name="mobil_id"]');
        if (select && select.value) {
            const selectedOption = select.options[select.selectedIndex];
            
            const stnkDate = selectedOption.getAttribute('data-stnk');
            if (stnkDate) {
                document.getElementById('masa_berlaku_stnk').value = stnkDate;
            }

            const kirDate = selectedOption.getAttribute('data-kir');
            if (kirDate) {
                document.getElementById('masa_berlaku_kir').value = kirDate;
            }
        }
    });
</script>
@endpush
