@extends('layouts.app')

@section('title', 'Buat Broadcast WA')
@section('page_title', 'Buat Broadcast WhatsApp')

@section('content')
<div class="space-y-5 font-sans max-w-4xl mx-auto pb-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-sm flex-shrink-0">
                <i class="fab fa-whatsapp text-2xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-800 tracking-tight">Buat Broadcast Baru</h1>
                <p class="text-xs text-slate-400 mt-0.5">Kirim pesan otomatis ke seluruh shipper berdasarkan Kapal & Voyage</p>
            </div>
        </div>
        <a href="{{ route('master.wa-broadcast.index') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all shadow-sm">
            <i class="fas fa-arrow-left mr-2 text-slate-400 text-xs"></i>
            Kembali
        </a>
    </div>

    <form action="{{ route('master.wa-broadcast.store') }}" method="POST">
        @csrf

        {{-- Step 1: Pilih Kapal & Voyage --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">1</span>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Pilih Kapal & Voyage</h2>
                    <p class="text-[11px] text-slate-400">Data penerima akan dimuat otomatis dari manifest</p>
                </div>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Nama Kapal --}}
                <div>
                    <label for="nama_kapal" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Kapal <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                            <i class="fas fa-ship text-xs"></i>
                        </span>
                        <select name="nama_kapal" id="nama_kapal"
                            class="w-full pl-8 pr-3 py-2.5 text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 bg-white transition-all" required>
                            <option value="">-- Pilih Kapal --</option>
                            @foreach($kapals as $kapal)
                                <option value="{{ $kapal }}" {{ old('nama_kapal') == $kapal ? 'selected' : '' }}>{{ $kapal }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('nama_kapal') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- No Voyage --}}
                <div>
                    <label for="no_voyage" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        No. Voyage <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                            <i class="fas fa-route text-xs"></i>
                        </span>
                        <select name="no_voyage" id="no_voyage"
                            class="w-full pl-8 pr-3 py-2.5 text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 bg-white transition-all disabled:bg-slate-50 disabled:text-slate-400"
                            required {{ empty(old('nama_kapal')) ? 'disabled' : '' }}>
                            <option value="">{{ empty(old('nama_kapal')) ? '-- Pilih Kapal Terlebih Dahulu --' : '-- Pilih Voyage --' }}</option>
                            @foreach($voyages as $voyage)
                                <option value="{{ $voyage }}" {{ old('no_voyage') == $voyage ? 'selected' : '' }}>{{ $voyage }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('no_voyage') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Recipient Preview Panel --}}
        <div id="recipient-panel" class="hidden bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-100 bg-slate-50/60">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-users text-emerald-500"></i>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Daftar Penerima Broadcast</h3>
                        <p id="recipient-summary" class="text-[11px] text-slate-400"></p>
                    </div>
                </div>
                <span id="recipient-loading" class="hidden text-[11px] text-slate-500">
                    <i class="fas fa-spinner fa-spin mr-1 text-emerald-500"></i> Memuat data...
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                            <th class="px-5 py-3 text-left">Shipper</th>
                            <th class="px-5 py-3 text-left">No. WhatsApp</th>
                            <th class="px-5 py-3 text-left">Sumber</th>
                            <th class="px-5 py-3 text-right">Kontainer</th>
                        </tr>
                    </thead>
                    <tbody id="recipient-list" class="divide-y divide-slate-100 text-slate-700"></tbody>
                </table>
            </div>
        </div>

        {{-- Step 2: Keterangan Kendala --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">2</span>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Keterangan Kendala <span class="text-slate-400 font-normal">(Opsional)</span></h2>
                    <p class="text-[11px] text-slate-400">Isi jika ada informasi tambahan yang perlu disampaikan</p>
                </div>
            </div>
            <div class="p-5 space-y-4">
                {{-- Kategori Masalah --}}
                <div>
                    <label for="kategori_masalah" class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Kendala</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                            <i class="fas fa-tag text-xs"></i>
                        </span>
                        <select name="kategori_masalah" id="kategori_masalah"
                            class="w-full pl-8 pr-3 py-2.5 text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 bg-white transition-all">
                            <option value="">-- Pilih Kategori (jika ada) --</option>
                            <option value="Keterlambatan Sandar" {{ old('kategori_masalah') == 'Keterlambatan Sandar' ? 'selected' : '' }}>Keterlambatan Sandar</option>
                            <option value="Keterlambatan Bongkar" {{ old('kategori_masalah') == 'Keterlambatan Bongkar' ? 'selected' : '' }}>Keterlambatan Bongkar</option>
                            <option value="Cuaca Buruk" {{ old('kategori_masalah') == 'Cuaca Buruk' ? 'selected' : '' }}>Cuaca Buruk</option>
                            <option value="Kerusakan Kapal" {{ old('kategori_masalah') == 'Kerusakan Kapal' ? 'selected' : '' }}>Kerusakan Kapal</option>
                            <option value="Antrean Pelabuhan" {{ old('kategori_masalah') == 'Antrean Pelabuhan' ? 'selected' : '' }}>Antrean Pelabuhan</option>
                            <option value="Informasi Umum" {{ old('kategori_masalah') == 'Informasi Umum' ? 'selected' : '' }}>Informasi Umum</option>
                        </select>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="deskripsi_masalah" class="block text-xs font-semibold text-slate-700 mb-1.5">Deskripsi / Keterangan Tambahan</label>
                    <textarea name="deskripsi_masalah" id="deskripsi_masalah" rows="3"
                        class="w-full px-3.5 py-2.5 text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-all placeholder:text-slate-300 resize-none"
                        placeholder="Contoh: Keterlambatan diperkirakan sekitar 2 hari karena cuaca buruk di jalur pelayaran...">{{ old('deskripsi_masalah') }}</textarea>
                    @error('deskripsi_masalah') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Step 3: Pilih Template WA --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">3</span>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Pilih Template Pesan WA <span class="text-rose-500">*</span></h2>
                    <p class="text-[11px] text-slate-400">Template menentukan format pesan yang akan dikirim ke semua shipper</p>
                </div>
            </div>
            <div class="p-5">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                        <i class="fas fa-file-alt text-xs"></i>
                    </span>
                    <select name="template_id" id="template_id"
                        class="w-full pl-8 pr-3 py-2.5 text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 bg-white transition-all" required>
                        <option value="">-- Pilih Template Pesan --</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>{{ $template->nama_template }}</option>
                        @endforeach
                    </select>
                </div>
                @error('template_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                <div class="mt-3 flex items-start gap-2 text-[11px] text-slate-400 bg-slate-50 border border-slate-100 rounded-xl p-3">
                    <i class="fas fa-info-circle text-slate-300 mt-0.5 flex-shrink-0"></i>
                    <span>Pesan dibuat otomatis berdasarkan variabel pada template yang dipilih. Data nomor WhatsApp shipper ditarik langsung dari manifest berdasarkan Kapal & Voyage di atas.</span>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('master.wa-broadcast.index') }}"
                class="inline-flex items-center px-5 py-2.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-all shadow-sm">
                <i class="fas fa-times mr-1.5 text-slate-400"></i>
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center px-6 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow transition-all">
                <i class="fab fa-whatsapp mr-2"></i>
                Preview & Kirim Pesan
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        if($.fn.select2) {
            $('#nama_kapal, #no_voyage, #template_id').select2({ width: '100%' });
        }

        const $namaKapal     = $('#nama_kapal');
        const $noVoyage      = $('#no_voyage');
        const $recipientPanel = $('#recipient-panel');
        const $recipientList  = $('#recipient-list');
        const $recipientSummary = $('#recipient-summary');
        const $recipientLoading = $('#recipient-loading');
        let recipientRequestId = 0;

        function clearRecipients() {
            recipientRequestId++;
            $recipientList.empty();
            $recipientSummary.empty();
            $recipientPanel.addClass('hidden');
            $recipientLoading.addClass('hidden');
        }

        function loadRecipients() {
            const namaKapal = $namaKapal.val();
            const noVoyage  = $noVoyage.val();

            if (!namaKapal || !noVoyage) { clearRecipients(); return; }

            const requestId = ++recipientRequestId;
            $recipientPanel.removeClass('hidden');
            $recipientList.empty();
            $recipientSummary.text('Membaca data shipper dari manifest...');
            $recipientLoading.removeClass('hidden');

            $.ajax({
                url: "{{ route('master.wa-broadcast.get-recipients') }}",
                type: 'GET',
                data: { nama_kapal: namaKapal, no_voyage: noVoyage },
                dataType: 'json',
                success: function(response) {
                    if (requestId !== recipientRequestId) return;

                    const recipients = response.success ? response.recipients : [];
                    $recipientList.empty();
                    $recipientSummary.text(recipients.length + ' shipper akan menerima broadcast pada voyage ini.');

                    $.each(recipients, function(i, r) {
                        const hasPhone = r.telepon && r.telepon !== '-';
                        const $row = $('<tr>').addClass('hover:bg-slate-50/70 transition-colors');

                        $('<td>', { class: 'px-5 py-3 font-semibold text-slate-800' }).text(r.shipper_name).appendTo($row);
                        $('<td>', { class: 'px-5 py-3' }).html(
                            hasPhone
                                ? '<span class="inline-flex items-center gap-1 text-emerald-700 font-medium"><i class="fas fa-check-circle text-emerald-400 text-[10px]"></i>' + r.telepon + '</span>'
                                : '<span class="text-rose-400 italic text-[11px]">Belum ada no. WA</span>'
                        ).appendTo($row);
                        $('<td>', { class: 'px-5 py-3 text-slate-400 text-[11px]' }).text(r.sumber_tabel || '-').appendTo($row);
                        $('<td>', { class: 'px-5 py-3 text-right font-semibold text-slate-700' }).html(
                            '<span class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-[11px] font-bold">' + r.jumlah_kontainer + '</span>'
                        ).appendTo($row);

                        $row.appendTo($recipientList);
                    });

                    if (!recipients.length) {
                        $('<tr>').append($('<td>', {
                            colspan: 4,
                            class: 'px-5 py-8 text-center text-slate-400 text-xs',
                            html: '<i class="fas fa-inbox text-2xl mb-2 block text-slate-300"></i>Tidak ada data shipper pada manifest untuk voyage ini.'
                        })).appendTo($recipientList);
                    }
                },
                error: function() {
                    if (requestId !== recipientRequestId) return;
                    $recipientList.empty();
                    $('<tr>').append($('<td>', {
                        colspan: 4,
                        class: 'px-5 py-6 text-center text-rose-500 text-xs',
                        html: '<i class="fas fa-exclamation-triangle mr-1"></i> Gagal memuat data penerima broadcast.'
                    })).appendTo($recipientList);
                    $recipientSummary.text('');
                },
                complete: function() {
                    if (requestId === recipientRequestId) $recipientLoading.addClass('hidden');
                }
            });
        }

        $namaKapal.on('change', function() {
            const namaKapal = $(this).val();
            if (!namaKapal) {
                clearRecipients();
                $noVoyage.empty().append('<option value="">-- Pilih Kapal Terlebih Dahulu --</option>');
                $noVoyage.prop('disabled', true).trigger('change');
                return;
            }

            clearRecipients();
            $noVoyage.empty().append('<option value="">Memuat voyage...</option>').prop('disabled', true).trigger('change');

            $.ajax({
                url: "{{ route('master.wa-broadcast.get-voyages') }}",
                type: 'GET',
                data: { nama_kapal: namaKapal },
                dataType: 'json',
                success: function(response) {
                    $noVoyage.empty();
                    if (response.success && response.voyages && response.voyages.length > 0) {
                        $noVoyage.append('<option value="">-- Pilih Voyage --</option>');
                        $.each(response.voyages, function(i, voyage) {
                            $noVoyage.append($('<option>', { value: voyage, text: voyage }));
                        });
                        $noVoyage.prop('disabled', false);
                    } else {
                        $noVoyage.append('<option value="">-- Tidak ada voyage tersedia --</option>').prop('disabled', false);
                    }
                    $noVoyage.trigger('change');
                },
                error: function() {
                    $noVoyage.empty().append('<option value="">-- Gagal memuat voyage --</option>').prop('disabled', false).trigger('change');
                }
            });
        });

        $noVoyage.on('change', loadRecipients);

        if ($namaKapal.val() && $noVoyage.val()) {
            loadRecipients();
        }
    });
</script>
@endpush
@endsection
