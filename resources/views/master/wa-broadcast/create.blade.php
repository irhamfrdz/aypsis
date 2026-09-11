@extends('layouts.app')

@section('title', 'Buat Broadcast WA')
@section('page_title', 'Buat Broadcast WhatsApp')

@section('content')
<div class="bg-white shadow rounded-lg p-6 font-sans max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Buat Broadcast Baru</h2>
        <a href="{{ route('master.wa-broadcast.index') }}" class="text-gray-600 hover:text-gray-900 font-medium flex items-center text-sm">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('master.wa-broadcast.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Pilihan Kapal -->
            <div>
                <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-2">Nama Kapal <span class="text-red-500">*</span></label>
                <select name="nama_kapal" id="nama_kapal" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    <option value="">-- Pilih Kapal --</option>
                    @foreach($kapals as $kapal)
                        <option value="{{ $kapal }}" {{ old('nama_kapal') == $kapal ? 'selected' : '' }}>{{ $kapal }}</option>
                    @endforeach
                </select>
                @error('nama_kapal') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Pilihan Voyage -->
            <div>
                <label for="no_voyage" class="block text-sm font-medium text-gray-700 mb-2">No Voyage <span class="text-red-500">*</span></label>
                <select name="no_voyage" id="no_voyage" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required {{ empty(old('nama_kapal')) ? 'disabled' : '' }}>
                    <option value="">{{ empty(old('nama_kapal')) ? '-- Pilih Kapal Terlebih Dahulu --' : '-- Pilih Voyage --' }}</option>
                    @foreach($voyages as $voyage)
                        <option value="{{ $voyage }}" {{ old('no_voyage') == $voyage ? 'selected' : '' }}>{{ $voyage }}</option>
                    @endforeach
                </select>
                @error('no_voyage') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div id="recipient-panel" class="hidden mb-6 border border-blue-200 bg-blue-50 p-4 rounded-lg">
            <div class="flex items-center justify-between gap-4 mb-3">
                <div>
                    <h3 class="text-sm font-bold text-blue-900">Penerima Broadcast</h3>
                    <p id="recipient-summary" class="text-xs text-blue-700 mt-1"></p>
                </div>
                <span id="recipient-loading" class="hidden text-xs text-blue-700">Memuat data...</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-blue-200 text-left text-xs text-blue-800">
                        <tr>
                            <th class="py-2 pr-4 font-semibold">SHIPPER</th>
                            <th class="py-2 pr-4 font-semibold">Contact Person / No. WhatsApp</th>
                            <th class="py-2 pr-4 font-semibold">Sumber</th>
                            <th class="py-2 font-semibold text-right">Kontainer</th>
                        </tr>
                    </thead>
                    <tbody id="recipient-list" class="divide-y divide-blue-100 text-gray-700"></tbody>
                </table>
            </div>
        </div>

        <div class="mb-6">
            <label for="deskripsi_masalah" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi / Keterangan Tambahan</label>
            <textarea name="deskripsi_masalah" id="deskripsi_masalah" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: Keterlambatan diperkirakan sekitar 2 hari...">{{ old('deskripsi_masalah') }}</textarea>
            @error('deskripsi_masalah') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <label for="template_id" class="block text-sm font-bold text-gray-800 mb-3">Pilih Template WA <span class="text-red-500">*</span></label>
            <select name="template_id" id="template_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                <option value="">-- Pilih Template Pesan --</option>
                @foreach($templates as $template)
                    <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>{{ $template->nama_template }}</option>
                @endforeach
            </select>
            @error('template_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            
            <div class="mt-3 text-xs text-gray-500 italic">
                * Pesan akan dibuat otomatis berdasarkan variabel sesuai dengan isi template yang dipilih. Data nomor shipper otomatis ditarik dari tabel manifest berdasarkan Kapal & Voyage yang dipilih di atas.
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('master.wa-broadcast.index') }}" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Preview & Kirim Pesan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<!-- Add Select2 if available in the project -->
<script>
    $(document).ready(function() {
        if($.fn.select2) {
            $('#nama_kapal, #no_voyage, #template_id').select2({
                width: '100%'
            });
        }

        const $namaKapal = $('#nama_kapal');
        const $noVoyage = $('#no_voyage');
        const $recipientPanel = $('#recipient-panel');
        const $recipientList = $('#recipient-list');
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
            const noVoyage = $noVoyage.val();

            if (!namaKapal || !noVoyage) {
                clearRecipients();
                return;
            }

            const requestId = ++recipientRequestId;
            $recipientPanel.removeClass('hidden');
            $recipientList.empty();
            $recipientSummary.text('Membaca data SHIPPER dan Contact Person dari manifest voyage terpilih.');
            $recipientLoading.removeClass('hidden');

            $.ajax({
                url: "{{ route('master.wa-broadcast.get-recipients') }}",
                type: 'GET',
                data: { nama_kapal: namaKapal, no_voyage: noVoyage },
                dataType: 'json',
                success: function(response) {
                    if (requestId !== recipientRequestId) {
                        return;
                    }

                    const recipients = response.success ? response.recipients : [];
                    $recipientList.empty();
                    $recipientSummary.text(recipients.length + ' SHIPPER akan menerima broadcast untuk voyage ini.');

                    $.each(recipients, function(index, recipient) {
                        const $row = $('<tr>');
                        $('<td>', { class: 'py-2.5 pr-4 font-medium text-gray-900', text: recipient.shipper_name }).appendTo($row);
                        $('<td>', { class: 'py-2.5 pr-4 text-gray-700', text: recipient.telepon || 'Belum ada Contact Person / no. WhatsApp' }).appendTo($row);
                        $('<td>', { class: 'py-2.5 pr-4 text-xs text-gray-500', text: recipient.sumber_tabel || '-' }).appendTo($row);
                        $('<td>', { class: 'py-2.5 text-right text-gray-700', text: recipient.jumlah_kontainer }).appendTo($row);
                        $row.appendTo($recipientList);
                    });

                    if (!recipients.length) {
                        $('<tr>').append($('<td>', {
                            colspan: 4,
                            class: 'py-3 text-center text-gray-500',
                            text: 'Tidak ada data SHIPPER pada manifest untuk voyage ini.'
                        })).appendTo($recipientList);
                    }
                },
                error: function(xhr, status, error) {
                    if (requestId !== recipientRequestId) {
                        return;
                    }

                    console.error('Error fetching broadcast recipients:', error);
                    $recipientList.empty();
                    $('<tr>').append($('<td>', {
                        colspan: 4,
                        class: 'py-3 text-center text-red-600',
                        text: 'Gagal memuat data penerima broadcast.'
                    })).appendTo($recipientList);
                    $recipientSummary.text('');
                },
                complete: function() {
                    if (requestId === recipientRequestId) {
                        $recipientLoading.addClass('hidden');
                    }
                }
            });
        }

        $namaKapal.on('change', function() {
            const namaKapal = $(this).val();

            if (!namaKapal) {
                clearRecipients();
                $noVoyage.empty().append('<option value="">-- Pilih Kapal Terlebih Dahulu --</option>');
                $noVoyage.prop('disabled', true);
                $noVoyage.trigger('change');
                return;
            }

            clearRecipients();
            $noVoyage.empty().append('<option value="">Memuat data voyage...</option>');
            $noVoyage.prop('disabled', true);
            $noVoyage.trigger('change');

            $.ajax({
                url: "{{ route('master.wa-broadcast.get-voyages') }}",
                type: 'GET',
                data: { nama_kapal: namaKapal },
                dataType: 'json',
                success: function(response) {
                    $noVoyage.empty();
                    if (response.success && response.voyages && response.voyages.length > 0) {
                        $noVoyage.append('<option value="">-- Pilih Voyage --</option>');
                        $.each(response.voyages, function(index, voyage) {
                            $noVoyage.append($('<option>', {
                                value: voyage,
                                text: voyage
                            }));
                        });
                        $noVoyage.prop('disabled', false);
                    } else {
                        $noVoyage.append('<option value="">-- Tidak ada voyage untuk kapal ini --</option>');
                        $noVoyage.prop('disabled', false);
                    }
                    $noVoyage.trigger('change');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching voyages:', error);
                    $noVoyage.empty().append('<option value="">-- Gagal memuat voyage --</option>');
                    $noVoyage.prop('disabled', false);
                    $noVoyage.trigger('change');
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
