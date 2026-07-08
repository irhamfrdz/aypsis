@extends('layouts.app')

@section('title', 'Detail Pranota Perbaikan Kontainer')
@section('page_title', 'Detail Pranota Perbaikan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        {{-- Breadcrumb & Actions --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <nav class="flex text-sm text-gray-500 mb-1">
                    <a href="{{ route('pranota-perbaikan-kontainer.index') }}" class="hover:text-blue-600 transition-colors">Pranota Perbaikan</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-800 font-medium">Detail</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-800">Nomor: {{ $pranota->nomor_pranota }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pranota-perbaikan-kontainer.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                @can('pranota-perbaikan-kontainer-print')
                <a href="{{ route('pranota-perbaikan-kontainer.print', $pranota->id) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors shadow-sm">
                    <i class="fas fa-print mr-2"></i> Cetak Lengkap
                </a>
                <a href="{{ route('pranota-perbaikan-kontainer.print', [$pranota->id, 'type' => 'cat']) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 transition-colors shadow-sm">
                    <i class="fas fa-paint-roller mr-2"></i> Cetak Cat Saja
                </a>
                <a href="{{ route('pranota-perbaikan-kontainer.print', [$pranota->id, 'type' => 'perbaikan']) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-tools mr-2"></i> Cetak Perbaikan Saja
                </a>
                @endcan
            </div>
        </div>

        {{-- Details Card --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Informasi Pranota</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Pranota</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $pranota->tanggal_pranota ? $pranota->tanggal_pranota->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Vendor/Bengkel</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $pranota->vendor ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Bank / Rekening</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        @if($pranota->bank || $pranota->rekening)
                            {{ $pranota->bank }} - {{ $pranota->rekening }} 
                            @if($pranota->penerima)
                                (a.n. {{ $pranota->penerima }})
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</label>
                    <div class="mt-1">
                        @php
                            $badgeColor = match($pranota->status) {
                                'draft' => 'bg-gray-100 text-gray-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'paid' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                            $statusLabel = match($pranota->status) {
                                'draft' => 'Draft',
                                'approved' => 'Disetujui',
                                'paid' => 'Lunas',
                                'cancelled' => 'Batal',
                                default => ucfirst($pranota->status)
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Dibuat Oleh</label>
                    <p class="mt-1 text-sm font-medium text-gray-950">
                        {{ $pranota->creator->name ?? 'System' }}
                        <span class="text-xs text-gray-500 font-normal">({{ $pranota->created_at->format('d/m/Y H:i') }})</span>
                    </p>
                </div>
            </div>
            @if($pranota->keterangan)
                <div class="mt-6 border-t border-gray-100 pt-4">
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Keterangan</label>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-200/50 italic">{{ $pranota->keterangan }}</p>
                </div>
            @endif
        </div>

        {{-- Items Table Card --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Daftar Item Perbaikan Kontainer</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-500">
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-center">No</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">No. Perbaikan</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">No. Kontainer</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Ukuran & Tipe</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Bengkel</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Keterangan Kerusakan</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-right">Estimasi Biaya</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-right">Biaya Riil</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-right">Biaya Terpakai</th>
                            @can('pranota-perbaikan-kontainer-update')
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider text-center">Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150">
                        @php $subtotal = 0; @endphp
                        @forelse($pranota->items ?? [] as $index => $item)
                            @php
                                $biayaRiil = floatval($item['biaya_riil'] ?? 0);
                                $estimasi = floatval($item['estimasi_biaya'] ?? 0);
                                $biayaCat = floatval($item['biaya_cat'] ?? 0);
                                $biayaTerpakai = (($biayaRiil > 0) ? $biayaRiil : $estimasi) + $biayaCat;
                                $subtotal += $biayaTerpakai;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-center text-gray-500 font-medium">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $item['no_perbaikan'] ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $item['no_kontainer'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    @if(!empty($item['ukuran']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $item['ukuran'] }}FT
                                        </span>
                                    @endif
                                    @if(!empty($item['tipe']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-800 ml-1">
                                            {{ $item['tipe'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $item['bengkel'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 max-w-xs break-words">
                                    {{ $item['keterangan_kerusakan'] ?? (\App\Models\PerbaikanKontainer::find($item['id'] ?? null)->keterangan_kerusakan ?? '-') }}
                                    @if(!empty($item['is_cat']) && $biayaCat > 0)
                                        <div class="text-xs text-blue-600 font-semibold mt-1">
                                            <i class="fas fa-paint-roller mr-1"></i> Cat: {{ $item['jenis_cat'] === 'cat_full' ? 'Full' : 'Sebagian' }} ({{ $item['vendor_cat'] ?? '-' }}) - Rp {{ number_format($biayaCat, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($estimasi, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-900">
                                    @if($biayaRiil > 0)
                                        Rp {{ number_format($biayaRiil, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-indigo-600 font-semibold">Rp {{ number_format($biayaTerpakai, 0, ',', '.') }}</td>
                                @can('pranota-perbaikan-kontainer-update')
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <button type="button" 
                                            class="inline-flex items-center px-2.5 py-1.5 border border-indigo-200 text-xs font-semibold rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors btn-edit-biaya"
                                            data-id="{{ $item['id'] ?? '' }}"
                                            data-no-perbaikan="{{ $item['no_perbaikan'] ?? '-' }}"
                                            data-no-kontainer="{{ $item['no_kontainer'] ?? '-' }}"
                                            data-estimasi="{{ (int) $estimasi }}"
                                            data-riil="{{ (int) $biayaRiil }}"
                                            data-biaya-cat="{{ (int) $biayaCat }}">
                                        <i class="fas fa-edit mr-1"></i> Edit Biaya
                                    </button>
                                </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('pranota-perbaikan-kontainer-update') ? 10 : 9 }}" class="px-4 py-8 text-center text-gray-500 font-medium">Tidak ada item perbaikan kontainer terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Summary details --}}
            <div class="mt-6 border-t border-gray-150 pt-4 flex flex-col items-end gap-2 text-sm">
                <div class="flex justify-between w-64 text-gray-600">
                    <span>Subtotal Biaya:</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between w-64 text-gray-600">
                    <span>Adjustment:</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between w-64 text-base font-bold text-gray-900 border-t border-gray-100 pt-2">
                    <span>Total Keseluruhan:</span>
                    <span class="text-indigo-600">Rp {{ number_format($subtotal + $pranota->adjustment, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@can('pranota-perbaikan-kontainer-update')
<!-- Modal Edit Biaya -->
<div id="modalEditBiaya" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" id="modalBackdrop"></div>

        <!-- Trick to center the modal content -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="formEditBiaya" action="" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="item_id" id="modalItemId">
                
                <div class="bg-white px-6 pt-6 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200 mb-4">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Edit Biaya Item Perbaikan
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none close-modal">
                            <span class="sr-only">Close</span>
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <!-- Details -->
                    <div class="bg-gray-50 rounded-lg p-3 mb-4 text-sm text-gray-600 grid grid-cols-2 gap-2">
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase">No. Perbaikan</span>
                            <span id="labelNoPerbaikan" class="font-medium text-gray-900">-</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase">No. Kontainer</span>
                            <span id="labelNoKontainer" class="font-medium text-gray-900">-</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Estimasi Biaya -->
                        <div>
                            <label for="estimasi_biaya" class="block text-sm font-semibold text-gray-700 mb-1">Estimasi Biaya (Rp)</label>
                            <input type="number" name="estimasi_biaya" id="modalEstimasiBiaya" required min="0" step="any"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                        </div>

                        <!-- Biaya Riil -->
                        <div>
                            <label for="biaya_riil" class="block text-sm font-semibold text-gray-700 mb-1">Biaya Riil (Rp)</label>
                            <input type="number" name="biaya_riil" id="modalBiayaRiil" required min="0" step="any"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                            <p class="text-xs text-gray-500 mt-1">Jika Biaya Riil diisi > 0, maka Biaya Terpakai akan menggunakan Biaya Riil. Jika 0, maka menggunakan Estimasi Biaya.</p>
                        </div>

                        <!-- Biaya Cat -->
                        <div id="wrapperBiayaCat" class="hidden">
                            <label for="biaya_cat" class="block text-sm font-semibold text-gray-700 mb-1">Biaya Cat (Rp)</label>
                            <input type="number" name="biaya_cat" id="modalBiayaCat" required min="0" step="any"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                        </div>

                        <!-- Kalkulasi Biaya Terpakai (Dynamic) -->
                        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 text-indigo-900 flex justify-between items-center">
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider text-indigo-700">Kalkulasi Biaya Terpakai</span>
                                <span class="text-xs text-indigo-600">(Estimasi/Riil + Cat)</span>
                            </div>
                            <div class="text-right">
                                <span id="labelBiayaTerpakai" class="text-lg font-bold text-indigo-700">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="submit" id="btnSubmitBiaya"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
                            style="background-color: #4f46e5; color: #ffffff;">
                        Simpan Perubahan
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm close-modal transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalEditBiaya');
    const backdrop = document.getElementById('modalBackdrop');
    const closeBtns = document.querySelectorAll('.close-modal');
    const editBtns = document.querySelectorAll('.btn-edit-biaya');
    const form = document.getElementById('formEditBiaya');
    const estimasiInput = document.getElementById('modalEstimasiBiaya');
    const riilInput = document.getElementById('modalBiayaRiil');
    const biayaCatInput = document.getElementById('modalBiayaCat');
    const labelBiayaTerpakai = document.getElementById('labelBiayaTerpakai');
    const wrapperBiayaCat = document.getElementById('wrapperBiayaCat');
    const itemIdInput = document.getElementById('modalItemId');
    const labelNoPerbaikan = document.getElementById('labelNoPerbaikan');
    const labelNoKontainer = document.getElementById('labelNoKontainer');
    const submitBtn = document.getElementById('btnSubmitBiaya');

    function calculateTerpakai() {
        const estimasi = parseFloat(estimasiInput.value) || 0;
        const riil = parseFloat(riilInput.value) || 0;
        const biayaCat = parseFloat(biayaCatInput.value) || 0;
        
        const baseBiaya = riil > 0 ? riil : estimasi;
        const totalTerpakai = baseBiaya + biayaCat;

        labelBiayaTerpakai.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(totalTerpakai));
    }

    // Show modal on click
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const noPerbaikan = this.getAttribute('data-no-perbaikan');
            const noKontainer = this.getAttribute('data-no-kontainer');
            const estimasi = this.getAttribute('data-estimasi');
            const riil = this.getAttribute('data-riil');
            const biayaCat = this.getAttribute('data-biaya-cat') || 0;

            const currentBiayaCat = parseFloat(biayaCat);

            // Pre-fill fields
            itemIdInput.value = id;
            labelNoPerbaikan.textContent = noPerbaikan;
            labelNoKontainer.textContent = noKontainer;
            estimasiInput.value = estimasi;
            riilInput.value = riil;
            biayaCatInput.value = currentBiayaCat;

            if (currentBiayaCat > 0) {
                wrapperBiayaCat.classList.remove('hidden');
            } else {
                wrapperBiayaCat.classList.add('hidden');
            }

            // Set action url
            form.setAttribute('action', '{{ route("pranota-perbaikan-kontainer.update-item", $pranota->id) }}');

            calculateTerpakai();
            modal.classList.remove('hidden');
        });
    });

    // Close modal
    function closeModal() {
        modal.classList.add('hidden');
    }

    closeBtns.forEach(btn => btn.addEventListener('click', closeModal));
    if (backdrop) backdrop.addEventListener('click', closeModal);

    // Form inputs change triggers calculation
    [estimasiInput, riilInput, biayaCatInput].forEach(input => {
        input.addEventListener('input', calculateTerpakai);
    });

    // Submit form via AJAX (fetch)
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const actionUrl = form.getAttribute('action');

        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams(new FormData(form)).toString()
        })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                alert(response.message);
                window.location.reload();
            } else {
                alert(response.message || 'Gagal menyimpan perubahan.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan Perubahan';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan pada server.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan Perubahan';
        });
    });
});
</script>
@endpush
@endcan
@endsection
