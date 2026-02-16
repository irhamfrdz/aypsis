<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Kendaraan - AYPSIS</title>
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
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('supir.dashboard') }}" class="p-2 bg-white/20 hover:bg-white/30 rounded-full transition-all backdrop-blur-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-2xl font-bold text-white">Pengecekan Kendaraan</h1>
            </div>
        </div>

        <form action="{{ route('supir.cek-kendaraan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Left Column: Basic Info -->
                <div class="space-y-6">
                    <div class="glass-card rounded-2xl p-6 shadow-xl">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Informasi Dasar
                        </h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kendaraan</label>
                                <select name="mobil_id" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all" required>
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam</label>
                                    <input type="time" name="jam" value="{{ date('H:i') }}" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Odometer (KM)</label>
                                <input type="number" name="odometer" placeholder="KM" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku STNK</label>
                                    <input type="date" name="masa_berlaku_stnk" id="masa_berlaku_stnk" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku KIR</label>
                                    <input type="date" name="masa_berlaku_kir" id="masa_berlaku_kir" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6 shadow-xl">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                            Lampiran Foto
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Sebelum</label>
                                <input type="file" name="foto_sebelum" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Sesudah</label>
                                <input type="file" name="foto_sesudah" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Middle & Right Columns: Checklist -->
                <div class="md:col-span-2 space-y-6">
                    <div class="glass-card rounded-2xl p-6 shadow-xl">
                        <h2 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Checklist Kendaraan
                        </h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
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
                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                                <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                <div class="flex space-x-2">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="{{ $key }}" value="baik" checked class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-200 transition-all">Baik</span>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="{{ $key }}" value="perlu_cek" class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-yellow-100 peer-checked:text-yellow-700 peer-checked:border-yellow-200 transition-all">Cek</span>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="{{ $key }}" value="buruk" class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-200 transition-all">Buruk</span>
                                    </label>
                                </div>
                            </div>
                            @endforeach

                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Kotak P3K</span>
                                <div class="flex space-x-2">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="kotak_p3k" value="tidak_kadaluarsa" checked class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-200 transition-all">Tidak Kadaluarsa</span>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="kotak_p3k" value="kadaluarsa" class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-200 transition-all">Kadaluarsa</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Plat No Belakang</span>
                                <div class="flex space-x-2">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="plat_no_belakang" value="ada" checked class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-200 transition-all">Ada</span>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="plat_no_belakang" value="tidak_ada" class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-200 transition-all">Tidak Ada</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Lampu Besar Dekat Kanan</span>
                                <div class="flex space-x-2">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="lampu_besar_dekat_kanan" value="berfungsi" checked class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-200 transition-all">Berfungsi</span>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="lampu_besar_dekat_kanan" value="tidak_berfungsi" class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-200 transition-all">Tidak Berfungsi</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Lampu Besar Dekat Kiri</span>
                                <div class="flex space-x-2">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="lampu_besar_dekat_kiri" value="berfungsi" checked class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-200 transition-all">Berfungsi</span>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="lampu_besar_dekat_kiri" value="tidak_berfungsi" class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-200 transition-all">Tidak Berfungsi</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Lampu Rem Kanan</span>
                                <div class="flex space-x-2">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="lampu_rem_kanan" value="berfungsi" checked class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-200 transition-all">Berfungsi</span>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="lampu_rem_kanan" value="tidak_berfungsi" class="hidden peer">
                                        <span class="px-3 py-1 text-xs rounded-full border border-gray-200 text-gray-400 peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-200 transition-all">Tidak Berfungsi</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                            <textarea name="catatan" rows="3" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tambahkan catatan jika ada kendala..."></textarea>
                        </div>
                        
                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all hover:-translate-y-1">
                                Simpan Pengecekan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
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
            if (select.value) {
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
</body>
</html>
