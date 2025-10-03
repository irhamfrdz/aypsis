<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Update Checkpoint - AYPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Update Checkpoint</h1>
            <div class="flex items-center space-x-4">
                <span class="hidden sm:block text-gray-600">Halo, {{ Auth::user()->name }}!</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="container mx-auto mt-8 px-4 sm:px-6 flex-grow">
        <div class="space-y-6">
            {{-- Notifikasi Sukses --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            {{-- Notifikasi Error --}}
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            {{-- Notifikasi Validasi --}}
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Detail Permohonan --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">Memo: {{ $permohonan->nomor_memo }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="font-medium text-gray-500">Tujuan</p>
                        <p class="text-gray-800">{{ $permohonan->tujuan }}</p>
                    </div>
                    <div>
                        @php
                            $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $permohonan->kegiatan)
                                            ->value('nama_kegiatan') ?? ucfirst($permohonan->kegiatan);
                        @endphp
                        <p class="font-medium text-gray-500">Kegiatan</p>
                        <p class="text-gray-800">{{ $kegiatanName }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-500">Ukuran</p>
                        <p class="text-gray-800">{{ $permohonan->ukuran }} ft</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-500">Jumlah Kontainer</p>
                        <p class="text-gray-800">{{ $permohonan->jumlah_kontainer }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-500">Vendor</p>
                        <p class="text-gray-800">{{ $permohonan->vendor_perusahaan }}</p>
                    </div>
                </div>
            </div>

            {{-- Form Input Checkpoint --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Checkpoint Supir</h3>
                <form action="{{ route('supir.checkpoint.store', $permohonan) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        {{-- Logika untuk menampilkan input atau daftar kontainer --}}
                        @if($permohonan->kontainers->isNotEmpty())
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Kontainer (Ditugaskan)</label>
                                <div class="mt-1 space-y-2">
                                    @foreach($permohonan->kontainers as $kontainer)
                                        <div class="block w-full rounded-md border-gray-300 bg-gray-200 shadow-sm p-2.5">
                                            {{ $kontainer->nomor_seri_gabungan }}
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Kontainer sudah diinput.</p>
                            </div>
                        @else
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Pilih Nomor Kontainer</label>
                                <div>
                                    <label for="tanggal_checkpoint" class="block text-sm font-medium text-gray-700">Tanggal Checkpoint</label>
                                    <input type="date" id="tanggal_checkpoint" name="tanggal_checkpoint" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" value="{{ date('Y-m-d') }}" required>
                                </div>
                                @push('styles')
                                    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                                @endpush

                                @push('scripts')
                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            // Inisialisasi Select2 pada dropdown kontainer biasa
                                            $('select.select-kontainer').each(function() {
                                                $(this).select2({
                                                    placeholder: 'Cari nomor kontainer',
                                                    width: '100%'
                                                });
                                            });

                                            // Inisialisasi Select2 dengan tags pada dropdown antar kontainer perbaikan
                                            $('select.select-kontainer-perbaikan').each(function() {
                                                $(this).select2({
                                                    placeholder: 'Pilih atau ketik nomor kontainer',
                                                    width: '100%',
                                                    tags: true,
                                                    tokenSeparators: [',', ' '],
                                                    createTag: function (params) {
                                                        var term = $.trim(params.term);
                                                        if (term === '') {
                                                            return null;
                                                        }
                                                        return {
                                                            id: term,
                                                            text: term,
                                                            newTag: true
                                                        }
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                @endpush

                                @php
                                    // Use the resolved kegiatan name (if available) so checks work even when
                                    // $permohonan->kegiatan holds a kode_kegiatan instead of the display name.
                                    $kegiatanLower = strtolower($kegiatanName ?? ($permohonan->kegiatan ?? ''));
                                    $isTarikSewa = (stripos($kegiatanLower, 'tarik') !== false && stripos($kegiatanLower, 'sewa') !== false)
                                        || (stripos($kegiatanLower, 'pengambilan') !== false)
                                        || ($kegiatanLower === 'pengambilan');
                                    $isPerbaikanKontainer = (stripos($kegiatanLower, 'perbaikan') !== false && stripos($kegiatanLower, 'kontainer') !== false)
                                        || (stripos($kegiatanLower, 'repair') !== false && stripos($kegiatanLower, 'container') !== false);
                                    $isAntarSewa = stripos($kegiatanLower, 'antar') !== false && stripos($kegiatanLower, 'sewa') !== false;
                                    $isAntarKontainerPerbaikan = (stripos($kegiatanLower, 'antar') !== false && stripos($kegiatanLower, 'kontainer') !== false && stripos($kegiatanLower, 'perbaikan') !== false);
                                @endphp

                                @for ($i = 0; $i < $permohonan->jumlah_kontainer; $i++)
                                    <div class="relative mt-1">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Kontainer #{{ $i + 1 }}</label>
                                        @if($isAntarKontainerPerbaikan)
                                            {{-- For antar kontainer perbaikan, show dropdown from master stock kontainer but allow free text --}}
                                            <select name="nomor_kontainer[]" class="select-kontainer-perbaikan block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5 pr-10" required>
                                                <option value="">-- Pilih atau Ketik Nomor Kontainer #{{ $i + 1 }} --</option>
                                                @if(isset($stockKontainers) && $stockKontainers->isNotEmpty())
                                                    @foreach($stockKontainers as $stock)
                                                        <option value="{{ $stock->nomor_kontainer }}">{{ $stock->nomor_kontainer }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif($isPerbaikanKontainer)
                                            {{-- For perbaikan kontainer, allow free text input regardless of vendor --}}
                                            <input type="text" name="nomor_kontainer[]" class="block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5" placeholder="Masukkan nomor kontainer #{{ $i + 1 }}" required>
                                        @elseif($isAntarSewa)
                                            {{-- For antar kontainer sewa, allow free text input regardless of vendor --}}
                                            <input type="text" name="nomor_kontainer[]" class="block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5" placeholder="Masukkan nomor kontainer #{{ $i + 1 }}" required>
                                        @elseif(in_array($permohonan->vendor_perusahaan, ['ZONA','DPE','SOC']) && $isTarikSewa)
                                            {{-- For sewa pickup (tarik kontainer sewa), require selecting from approved/tagihan group kontainers --}}
                                            @if(isset($kontainerList) && $kontainerList->isNotEmpty())
                                                <select name="nomor_kontainer[]" class="select-kontainer block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5 pr-10" required>
                                                    <option value="">-- Pilih Kontainer #{{ $i + 1 }} --</option>
                                                    @foreach($kontainerList as $kontainer)
                                                        {{-- Send the displayed serial as the option value so the controller
                                                             receives exactly what the driver sees/chooses. This avoids the
                                                             controller creating records with numeric ids as serials. --}}
                                                        <option value="{{ $kontainer->nomor_seri_gabungan }}">{{ $kontainer->nomor_seri_gabungan }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" name="nomor_kontainer[]" class="block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5" placeholder="Tidak ada kontainer approved di grup tagihan" required>
                                            @endif
                                        @elseif(in_array($permohonan->vendor_perusahaan, ['ZONA','DPE','SOC']))
                                            {{-- Allow free-text for vendors that supply container numbers (legacy behavior) when not a sewa pickup --}}
                                            <input type="text" name="nomor_kontainer[]" class="block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5" placeholder="Masukkan nomor kontainer #{{ $i + 1 }}" required>
                                        @else
                                            <select name="nomor_kontainer[]" class="select-kontainer block w-full rounded-lg border border-indigo-300 bg-white shadow focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition p-2.5 pr-10" required>
                                                <option value="">-- Pilih Kontainer #{{ $i + 1 }} --</option>
                                                    @if(isset($kontainerList))
                                                    @foreach($kontainerList as $kontainer)
                                                        {{-- Keep select values as the serial so what supir selects is what gets submitted. --}}
                                                        <option value="{{ $kontainer->nomor_seri_gabungan }}">{{ $kontainer->nomor_seri_gabungan }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l4-4-4-4m8 8V8" /></svg>
                                            </div>
                                        @endif
                                    </div>
                                @endfor
                                <p class="text-xs text-gray-500 mt-1">
                                    @if($isAntarKontainerPerbaikan)
                                        Pilih dari master stock kontainer atau ketik nomor kontainer yang akan diantar untuk perbaikan.
                                    @elseif($isPerbaikanKontainer)
                                        Masukkan nomor kontainer yang akan diperbaiki.
                                    @elseif($isAntarSewa)
                                        Masukkan nomor kontainer yang akan diantar.
                                    @else
                                        Pilih nomor kontainer sesuai jumlah di memo.
                                    @endif
                                </p>
                            </div>
                        @endif
                        <div>
                            <label for="surat_jalan_vendor" class="block text-sm font-medium text-gray-700">Surat Jalan Vendor</label>
                            <input type="text" id="surat_jalan_vendor" name="surat_jalan_vendor" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5" placeholder="Masukkan nomor surat jalan vendor">
                        </div>
                        <div>
                            <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan / Keterangan</label>
                            <textarea id="catatan" name="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2.5"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Simpan Checkpoint
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Riwayat Checkpoint --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Checkpoint</h3>
                <div class="space-y-4">
                    @forelse($permohonan->checkpoints as $checkpoint)
                        <div class="border-l-4 pl-4 {{ $loop->first ? 'border-indigo-500' : 'border-gray-300' }}">
                            <p class="font-semibold text-gray-800">{{ $checkpoint->catatan }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $checkpoint->created_at->format('d M Y, H:i') }} - {{ $checkpoint->lokasi ?? 'Lokasi tidak diketahui' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada riwayat checkpoint.</p>
                    @endforelse
                </div>
            </div>

            {{-- Tombol Kembali --}}
            <div class="pt-4">
                <a href="{{ route('supir.dashboard') }}" class="w-full sm:w-auto inline-flex justify-center py-2 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Kembali ke Daftar Tugas
                </a>
            </div>
        </div>
    </main>
</body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Select2 pada dropdown kontainer biasa
            $('select.select-kontainer').each(function() {
                $(this).select2({
                    placeholder: 'Cari nomor kontainer',
                    width: '100%'
                });
            });

            // Inisialisasi Select2 dengan tags pada dropdown antar kontainer perbaikan
            $('select.select-kontainer-perbaikan').each(function() {
                $(this).select2({
                    placeholder: 'Pilih atau ketik nomor kontainer',
                    width: '100%',
                    tags: true,
                    tokenSeparators: [',', ' '],
                    createTag: function (params) {
                        var term = $.trim(params.term);
                        if (term === '') {
                            return null;
                        }
                        return {
                            id: term,
                            text: term,
                            newTag: true
                        }
                    }
                });
            });
        });
    </script>
</html>
