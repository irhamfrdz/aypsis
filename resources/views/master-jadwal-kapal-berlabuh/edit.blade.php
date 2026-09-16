@extends('layouts.app')

@section('title', 'Edit Jadwal Kapal Berlabuh')
@section('page_title', 'Edit Jadwal Kapal Berlabuh')

@section('content')
<div class="container mx-auto px-4 py-4 max-w-4xl">
    <!-- Breadcrumb / Back button -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('master-jadwal-kapal-berlabuh.index') }}" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Edit Jadwal Kapal Berlabuh</h1>
                <p class="text-xs sm:text-sm text-gray-500">Perbarui jadwal kapal {{ $jadwal->nama_kapal }} ({{ $jadwal->pelabuhan }})</p>
            </div>
        </div>
    </div>

    <!-- Error Validation Alert -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat beberapa kesalahan pengisian formulir:</h3>
                    <ul class="mt-2 list-disc list-inside text-xs text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('master-jadwal-kapal-berlabuh.update', $jadwal->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Pelabuhan & Kapal -->
            <div class="border-b border-gray-100 pb-6 mb-6">
                <h2 class="text-sm font-bold uppercase tracking-wider text-sky-800 mb-4 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-sky-500 rounded-full"></span>
                    Informasi Kapal & Pelabuhan
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Pelabuhan / Rute -->
                    <div>
                        <label for="pelabuhan" class="block text-xs font-bold text-gray-700 mb-1">
                            Pelabuhan / Rute Tujuan <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <select id="master_pelabuhan_id" name="master_pelabuhan_id"
                                    class="w-full text-xs border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    onchange="updatePelabuhanInput(this)">
                                <option value="">-- Pilih dari Master Pelabuhan atau ketik manual --</option>
                                @foreach($masterPelabuhans as $p)
                                    <option value="{{ $p->id }}" data-nama="{{ $p->nama_pelabuhan }}" {{ old('master_pelabuhan_id', $jadwal->master_pelabuhan_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_pelabuhan }} ({{ $p->kota }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" id="pelabuhan" name="pelabuhan" value="{{ old('pelabuhan', $jadwal->pelabuhan) }}"
                                   placeholder="Contoh: TANJUNG PINANG"
                                   required
                                   class="w-full text-xs font-semibold uppercase border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Nama Kapal -->
                    <div>
                        <label for="nama_kapal" class="block text-xs font-bold text-gray-700 mb-1">
                            Nama Kapal <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <select id="master_kapal_id" name="master_kapal_id"
                                    class="w-full text-xs border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    onchange="updateKapalInput(this)">
                                <option value="">-- Pilih dari Master Kapal atau ketik manual --</option>
                                @foreach($masterKapals as $k)
                                    <option value="{{ $k->id }}" data-nama="{{ $k->nama_kapal }}" {{ old('master_kapal_id', $jadwal->master_kapal_id) == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kapal }} ({{ $k->pelayaran ?? 'Alexindo' }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" id="nama_kapal" name="nama_kapal" value="{{ old('nama_kapal', $jadwal->nama_kapal) }}"
                                   placeholder="Contoh: KM SEKAR PERMATA"
                                   required
                                   class="w-full text-xs font-bold uppercase border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Voyage -->
                    <div>
                        <label for="no_voyage" class="block text-xs font-semibold text-gray-700 mb-1">
                            Nomor Voyage (Opsional)
                        </label>
                        <input type="text" id="no_voyage" name="no_voyage" value="{{ old('no_voyage', $jadwal->no_voyage) }}"
                               placeholder="Contoh: 01/IX/2026"
                               class="w-full text-xs border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-xs font-bold text-gray-700 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full text-xs border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="aktif" {{ old('status', $jadwal->status) == 'aktif' ? 'selected' : '' }}>Aktif / Berjalan</option>
                            <option value="selesai" {{ old('status', $jadwal->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="batal" {{ old('status', $jadwal->status) == 'batal' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Jadwal Tanggal (Close, ETD, ETA) -->
            <div class="border-b border-gray-100 pb-6 mb-6">
                <h2 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-4 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span>
                    Jadwal Waktu (Closing, ETD & ETA)
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <!-- Tanggal Closing -->
                    <div class="bg-sky-50/60 p-4 rounded-xl border border-sky-100">
                        <label for="tanggal_closing" class="block text-xs font-bold text-sky-900 mb-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Tanggal Close
                        </label>
                        <input type="date" id="tanggal_closing" name="tanggal_closing"
                               value="{{ old('tanggal_closing', $jadwal->tanggal_closing ? \Carbon\Carbon::parse($jadwal->tanggal_closing)->format('Y-m-d') : '') }}"
                               class="w-full text-xs font-medium border border-sky-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <p class="text-[11px] text-sky-700 mt-1">Batas akhir penerimaan muatan</p>
                    </div>

                    <!-- Tanggal ETD -->
                    <div class="bg-amber-50 p-4 rounded-xl border border-amber-200">
                        <label for="tanggal_etd" class="block text-xs font-bold text-amber-950 mb-1 flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 bg-amber-400 text-amber-950 text-[10px] font-extrabold rounded">ETD</span>
                            Tanggal Keberangkatan
                        </label>
                        <input type="date" id="tanggal_etd" name="tanggal_etd"
                               value="{{ old('tanggal_etd', $jadwal->tanggal_etd ? \Carbon\Carbon::parse($jadwal->tanggal_etd)->format('Y-m-d') : '') }}"
                               class="w-full text-xs font-bold text-amber-950 border border-amber-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <p class="text-[11px] text-amber-800 mt-1">Estimated Time of Departure</p>
                    </div>

                    <!-- Tanggal ETA -->
                    <div class="bg-rose-50 p-4 rounded-xl border border-rose-200">
                        <label for="tanggal_eta" class="block text-xs font-bold text-rose-950 mb-1 flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 bg-rose-600 text-white text-[10px] font-extrabold rounded">ETA</span>
                            Tanggal Tiba / Berlabuh
                        </label>
                        <input type="date" id="tanggal_eta" name="tanggal_eta"
                               value="{{ old('tanggal_eta', $jadwal->tanggal_eta ? \Carbon\Carbon::parse($jadwal->tanggal_eta)->format('Y-m-d') : '') }}"
                               class="w-full text-xs font-bold text-rose-950 border border-rose-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                        <p class="text-[11px] text-rose-800 mt-1">Estimated Time of Arrival / Labuh</p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Keterangan -->
            <div class="mb-6">
                <label for="keterangan" class="block text-xs font-semibold text-gray-700 mb-1">
                    Keterangan / Catatan Tambahan (Opsional)
                </label>
                <textarea id="keterangan" name="keterangan" rows="3" placeholder="Tambahkan catatan khusus terkait jadwal ini..."
                          class="w-full text-xs border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('keterangan', $jadwal->keterangan) }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('master-jadwal-kapal-berlabuh.index') }}"
                   class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updatePelabuhanInput(select) {
    var selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.dataset.nama) {
        document.getElementById('pelabuhan').value = selectedOption.dataset.nama;
    }
}

function updateKapalInput(select) {
    var selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.dataset.nama) {
        document.getElementById('nama_kapal').value = selectedOption.dataset.nama;
    }
}
</script>
@endsection
