@extends('layouts.app')

@section('title', 'Form Pranota Supir')
@section('page_title', 'Form Pranota Supir')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        @php
            // Definisikan kelas Tailwind untuk input yang lebih besar dan jelas
            $inputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-base p-2.5";
            $readonlyInputClasses = "mt-1 block w-full rounded-md border-gray-300 bg-gray-200 shadow-sm text-base p-2.5";
        @endphp

        <!-- Form untuk filter tanggal (dipindahkan ke luar form utama) -->
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter Memo Berdasarkan Tanggal</h3>
        <form action="{{ route('pranota-supir.create') }}" method="GET" class="mb-8 p-4 border rounded-lg bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="{{ $inputClasses }}" value="{{ $start_date ?? '' }}">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="end_date" class="{{ $inputClasses }}" value="{{ $end_date ?? '' }}">
                </div>
                <div class="flex space-x-2 pt-4 md:pt-0">
                    <button type="submit" class="inline-flex justify-center py-2.5 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full">
                        Cari
                    </button>
                    <a href="{{ route('pranota-supir.create') }}" class="inline-flex justify-center py-2.5 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <form id="pranotaForm" action="{{ route('pranota-supir.store') }}" method="POST">
            @csrf

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Pranota</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Nomor Pranota (Otomatis) + Nomor Cetakan -->
                <div class="flex items-end gap-4">
                    <div class="w-full">
                        <label for="nomor_pranota_display" class="block text-sm font-medium text-gray-700">Nomor Pranota (Otomatis)</label>
                        <input type="text" id="nomor_pranota_display" class="{{ $readonlyInputClasses }}" value="{{ $nomor_pranota_display }}" readonly>
                    </div>
                    <div>
                        <label for="nomor_cetakan" class="block text-sm font-medium text-gray-700">Nomor Cetakan</label>
                        <div class="flex gap-2">
                            <input type="number" min="1" id="nomor_cetakan" name="nomor_cetakan" value="{{ $nomor_cetakan ?? 1 }}" class="w-16 {{ $inputClasses }}">
                        </div>
                    </div>
                </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ...existing code...

            // Nomor Cetakan & Nomor Pranota Otomatis
            const nomorCetakanInput = document.getElementById('nomor_cetakan');
            const nomorPranotaDisplay = document.getElementById('nomor_pranota_display');
            nomorCetakanInput.addEventListener('input', updateNomorPranota);

            function updateNomorPranota() {
                // Ambil format dari value awal
                let format = nomorPranotaDisplay.value;
                // Ganti nomor cetakan di format
                let parts = format.split('-');
                if(parts.length === 5) {
                    parts[1] = nomorCetakanInput.value;
                    nomorPranotaDisplay.value = parts.join('-');
                }
            }
        });
    </script>

                <!-- Tanggal Pranota (Otomatis) -->
                <div>
                    <label for="tanggal_pranota" class="block text-sm font-medium text-gray-700">Tanggal Pranota</label>
                    <input type="date" name="tanggal_pranota" id="tanggal_pranota" class="{{ $readonlyInputClasses }}" value="{{ now()->toDateString() }}" readonly>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pilih Memo Permohonan</h3>
            <div class="mb-6">
                <div class="overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Memo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supir</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Plat</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya Memo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($permohonans as $permohonan)
                                @if($permohonan->pranotas->isEmpty())
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="permohonan_ids[]" value="{{ $permohonan->id }}" data-biaya="{{ $permohonan->total_harga_setelah_adj }}" class="permohonan-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $permohonan->nomor_memo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $kegiatanMap[$permohonan->kegiatan] ?? ucfirst($permohonan->kegiatan) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $permohonan->supir->nama_lengkap ?? $permohonan->supir->nama_panggilan ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $permohonan->plat_nomor ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">Rp. {{ number_format($permohonan->total_harga_setelah_adj, 2, ',', '.') }}</td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada permohonan yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-sm text-gray-500">
                    * Anda dapat memilih satu atau lebih memo permohonan untuk dimasukkan ke dalam pranota ini
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Total Biaya Pranota</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Total Biaya dari Memo -->
                <div>
                    <label for="total_biaya_memo_display" class="block text-sm font-medium text-gray-700">Total Biaya Memo (Otomatis)</label>
                    <input type="text" id="total_biaya_memo_display" class="{{ $readonlyInputClasses }}" readonly>
                </div>

                <!-- Adjustment -->
                <div>
                    <label for="adjustment" class="block text-sm font-medium text-gray-700">Adjustment</label>
                    <input type="number" name="adjustment" id="adjustment" class="{{ $inputClasses }}" value="0" step="0.01">
                </div>

                <!-- Alasan Adjustment -->
                <div class="md:col-span-2">
                    <label for="alasan_adjustment" class="block text-sm font-medium text-gray-700">Alasan Adjustment</label>
                    <textarea name="alasan_adjustment" id="alasan_adjustment" rows="2" class="{{ $inputClasses }}"></textarea>
                </div>

                <!-- Total Biaya Setelah Adjustment -->
                <div class="md:col-span-2">
                    <label for="total_biaya_pranota_display" class="block text-sm font-medium text-gray-700">Total Biaya Pranota (Otomatis)</label>
                    <input type="text" id="total_biaya_pranota_display" class="{{ $readonlyInputClasses }}" readonly>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Tambahan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="3" class="{{ $inputClasses }}"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Buat Pranota
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('select-all');
            const permohonanCheckboxes = document.querySelectorAll('.permohonan-checkbox');
            const totalBiayaMemoDisplay = document.getElementById('total_biaya_memo_display');
            const adjustmentInput = document.getElementById('adjustment');
            const totalBiayaPranotaDisplay = document.getElementById('total_biaya_pranota_display');

            function updateTotalBiayaMemo() {
                let total = 0;
                permohonanCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        total += parseFloat(checkbox.dataset.biaya) || 0;
                    }
                });
                totalBiayaMemoDisplay.value = 'Rp. ' + total.toLocaleString('id-ID');
                return total;
            }

            function updateTotalBiayaPranota() {
                const totalMemo = updateTotalBiayaMemo();
                const adjustment = parseFloat(adjustmentInput.value) || 0;
                const total = totalMemo + adjustment;
                totalBiayaPranotaDisplay.value = 'Rp. ' + total.toLocaleString('id-ID');
            }

            selectAllCheckbox.addEventListener('change', function () {
                permohonanCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateTotalBiayaPranota();
            });

            permohonanCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateTotalBiayaPranota);
            });

            adjustmentInput.addEventListener('input', updateTotalBiayaPranota);

            // Tambahkan validasi untuk memastikan minimal satu memo dipilih
            const pranotaForm = document.getElementById('pranotaForm');
            pranotaForm.addEventListener('submit', function(e) {
                const checkedCheckboxes = document.querySelectorAll('.permohonan-checkbox:checked');
                if (checkedCheckboxes.length === 0) {
                    e.preventDefault();
                    alert('Silakan pilih minimal satu memo permohonan.');
                    return false;
                }
            });

            updateTotalBiayaPranota();
        });
    </script>
@endsection
