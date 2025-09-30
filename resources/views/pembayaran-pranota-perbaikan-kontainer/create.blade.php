@extends('layouts.app')

@section('title', 'Form Pembayaran Pranota Perbaikan Kontainer')
@section('page_title', 'Form Pembayaran Pranota Perbaikan Kontainer')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-4 max-w-6xl mx-auto">
        @php
            // Definisikan kelas Tailwind untuk input yang lebih compact
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm p-2 transition-colors";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm p-2";
            $labelClasses = "block text-xs font-medium text-gray-700 mb-1";
        @endphp

        @if(session('success'))
            <div class="mb-3 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-3 p-4 rounded-lg bg-red-50 border-l-4 border-red-400 text-red-800 text-sm" id="error-alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Pembayaran Gagal!
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600" onclick="dismissErrorAlert()">
                                <span class="sr-only">Tutup</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- Only show validation errors if this is a POST request (form submission) --}}
        @if(request()->isMethod('post') && !empty($errors) && (is_object($errors) ? $errors->any() : (!empty($errors) && is_array($errors))))
            <div class="mb-3 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mt-1 list-disc list-inside">
                    @if(is_object($errors) && method_exists($errors, 'all'))
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @elseif(is_array($errors))
                        @foreach($errors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif

        <form id="pembayaranForm" action="{{ route('pembayaran-pranota-perbaikan-kontainer.store') }}" method="POST" class="space-y-3">
            @csrf

            <!-- Data Pembayaran & Bank -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Data Pembayaran -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Data Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="flex items-end gap-1">
                                <div class="flex-1">
                                    <label for="nomor_pembayaran" class="{{ $labelClasses }}">Nomor Pembayaran</label>
                                    <input type="text" name="nomor_pembayaran" id="nomor_pembayaran"
                                        value="{{ 'PPK-' . now()->format('y') . now()->format('m') . '-000001' }}"
                                        class="{{ $readonlyInputClasses }}" readonly>
                                </div>
                                <div class="w-16">
                                    <label for="nomor_cetakan" class="{{ $labelClasses }}">Cetak</label>
                                    <input type="number" name="nomor_cetakan" id="nomor_cetakan" min="1" max="9" value="1"
                                        class="{{ $inputClasses }}">
                                </div>
                            </div>
                            <div>
                                <label for="tanggal_kas" class="{{ $labelClasses }}">Tanggal Kas</label>
                                <input type="text" name="tanggal_kas" id="tanggal_kas"
                                    value="{{ now()->format('d/M/Y') }}"
                                    class="{{ $readonlyInputClasses }}" readonly required>
                                <input type="hidden" name="tanggal_kas" value="{{ now()->toDateString() }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank & Transaksi -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Bank & Transaksi</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <label for="bank" class="{{ $labelClasses }}">Pilih Bank</label>
                                <select name="bank" id="bank" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @foreach($akunCoa as $akun)
                                        <option value="{{ $akun->nama_akun }}" data-kode="{{ $akun->kode_nomor ?? '000' }}" {{ old('bank') == $akun->nama_akun ? 'selected' : '' }}>
                                            {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="jenis_transaksi" class="{{ $labelClasses }}">Jenis Transaksi</label>
                                <select name="jenis_transaksi" id="jenis_transaksi" class="{{ $inputClasses }}" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Debit">Debit</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pilih Pranota Perbaikan Kontainer --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800">Pilih Pranota Perbaikan Kontainer</h4>
                </div>
                <div class="overflow-x-auto max-h-60">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-20">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="h-3 w-3 text-indigo-600 border-gray-300 rounded">
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pranota</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontainer</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teknisi</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pranotaPerbaikanKontainers as $pranota)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <input type="checkbox" name="pranota_perbaikan_kontainer_ids[]" value="{{ $pranota->id }}" class="pranota-checkbox h-3 w-3 text-indigo-600 border-gray-300 rounded" checked>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">{{ $pranota->nomor_pranota ?? 'Belum ada' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        <strong>{{ $pranota->perbaikanKontainers->first()->nomor_kontainer ?? 'N/A' }}</strong><br>
                                        <small class="text-gray-500">{{ Str::limit($pranota->perbaikanKontainers->first()->deskripsi_kerusakan ?? '', 30) }}</small>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if ($pranota->tanggal_pranota)
                                            {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/M/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">{{ $pranota->nama_teknisi ?? '-' }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-semibold">Rp {{ number_format($pranota->total_biaya ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-xs">
                                        @if ($pranota->status == 'approved')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-green-100 text-green-800">Approved</span>
                                        @elseif ($pranota->status == 'belum_dibayar')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-blue-100 text-blue-800">Belum Dibayar</span>
                                        @elseif ($pranota->status == 'sudah_dibayar')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-emerald-100 text-emerald-800">Sudah Dibayar</span>
                                        @elseif ($pranota->status == 'pending')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-yellow-100 text-yellow-800">Pending</span>
                                        @else
                                            <span class="px-1.5 py-0.5 inline-flex text-xs font-medium rounded bg-gray-100 text-gray-800">{{ ucfirst($pranota->status ?? 'Unknown') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-2 py-4 text-center text-xs text-gray-500">
                                        Tidak ada pranota perbaikan kontainer yang tersedia untuk pembayaran
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Ringkasan Pembayaran --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <h4 class="text-sm font-semibold text-blue-800 mb-2">Ringkasan Pembayaran</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="text-center">
                        <div class="text-xs text-blue-600">Jumlah Pranota</div>
                        <div class="text-lg font-bold text-blue-800" id="jumlah-pranota">0</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-blue-600">Total Pembayaran</div>
                        <div class="text-lg font-bold text-blue-800" id="total-pembayaran">Rp 0</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-blue-600">Status</div>
                        <div class="text-sm font-semibold text-green-600">Siap Dibayar</div>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                <a href="{{ route('pembayaran-pranota-perbaikan-kontainer.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors text-sm">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors text-sm font-medium">
                    <i class="fas fa-save mr-1"></i> Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Function to update nomor pembayaran based on selected bank
    function updateNomorPembayaran() {
        var selectedOption = $('#bank option:selected');
        var kode = selectedOption.data('kode') || '000';
        var tahun = new Date().getFullYear().toString().slice(-2);
        var bulan = ('0' + (new Date().getMonth() + 1)).toString().slice(-2);
        var nomor = kode + tahun + bulan + '-000001';
        $('#nomor_pembayaran').val(nomor);
    }

    // Update nomor pembayaran when bank changes
    $('#bank').on('change', function() {
        updateNomorPembayaran();
    });

    // Initial update if bank is pre-selected
    if ($('#bank').val()) {
        updateNomorPembayaran();
    }

    // Select all checkbox functionality
    $('#select-all').on('change', function() {
        $('.pranota-checkbox').prop('checked', $(this).prop('checked'));
        updateSummary();
    });

    // Individual checkbox change
    $(document).on('change', '.pranota-checkbox', function() {
        var totalCheckboxes = $('.pranota-checkbox').length;
        var checkedCheckboxes = $('.pranota-checkbox:checked').length;

        $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
        updateSummary();
    });

    // Update summary function
    function updateSummary() {
        var selectedPranota = $('.pranota-checkbox:checked');
        var jumlahPranota = selectedPranota.length;
        var totalPembayaran = 0;

        selectedPranota.each(function() {
            var row = $(this).closest('tr');
            var biayaText = row.find('td:nth-child(6)').text().replace('Rp ', '').replace(/\./g, '').replace(',', '.');
            var biaya = parseFloat(biayaText) || 0;
            totalPembayaran += biaya;
        });

        $('#jumlah-pranota').text(jumlahPranota);
        $('#total-pembayaran').text('Rp ' + totalPembayaran.toLocaleString('id-ID'));
    }

    // Initial summary update
    updateSummary();

    // Show alert for session error
    @if(session('error'))
        // Auto-scroll to error alert
        setTimeout(function() {
            $('#error-alert')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 500);

        // Show browser alert for error
        setTimeout(function() {
            alert('Pembayaran Gagal!\n\n' + '{{ session("error") }}');
        }, 1000);
    @endif

    // Form validation
    $('#pembayaranForm').on('submit', function(e) {
        var selectedPranota = $('.pranota-checkbox:checked');
        if (selectedPranota.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu pranota untuk dibayar');
            return false;
        }

        var bank = $('#bank').val();
        if (!bank) {
            e.preventDefault();
            alert('Pilih bank terlebih dahulu');
            $('#bank').focus();
            return false;
        }

        var jenisTransaksi = $('#jenis_transaksi').val();
        if (!jenisTransaksi) {
            e.preventDefault();
            alert('Pilih jenis transaksi terlebih dahulu');
            $('#jenis_transaksi').focus();
            return false;
        }

        return confirm('Apakah Anda yakin ingin memproses pembayaran untuk ' + selectedPranota.length + ' pranota?');
    });
});

// Function to dismiss error alert
function dismissErrorAlert() {
    $('#error-alert').fadeOut(300, function() {
        $(this).remove();
    });
}
</script>
@endpush
