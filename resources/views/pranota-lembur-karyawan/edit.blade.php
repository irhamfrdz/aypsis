@extends('layouts.app')

@section('title', 'Edit Pranota Lembur Karyawan - ' . $pranota->nomor_pranota)
@section('page_title', 'Edit Pranota Lembur Karyawan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('pranota-lembur-karyawan.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center gap-2">
                                <span>Edit Pranota Lembur</span>
                                <span class="text-lg sm:text-xl font-mono text-blue-600 font-semibold">{{ $pranota->nomor_pranota }}</span>
                            </h1>
                            <p class="mt-1 text-sm text-gray-600">Perbarui tanggal pranota, nilai adjustment, catatan, atau karyawan dalam pranota ini.</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('pranota-lembur-karyawan.show', $pranota->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors">
                        <i class="fas fa-eye mr-2 text-gray-500"></i>
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <form action="{{ route('pranota-lembur-karyawan.update', $pranota->id) }}" method="POST" id="form-edit-page">
            @csrf
            @method('PUT')
            <input type="hidden" name="_redirect_to" value="show">
            <input type="hidden" name="periode_mulai" value="{{ $pranota->periode_mulai ? $pranota->periode_mulai->format('Y-m-d') : '' }}">
            <input type="hidden" name="periode_selesai" value="{{ $pranota->periode_selesai ? $pranota->periode_selesai->format('Y-m-d') : '' }}">

            <!-- Info Cards & Primary Inputs -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Nomor Pranota</label>
                    <div class="text-lg font-bold text-blue-600 font-mono">{{ $pranota->nomor_pranota }}</div>
                    <div class="text-xs text-gray-400 mt-1">Dibuat {{ $pranota->created_at->format('d/m/Y H:i') }}</div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <label for="tanggal_pranota" class="block text-xs font-semibold text-gray-700 mb-1">
                        Tanggal Pranota <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_pranota" id="tanggal_pranota" required
                           value="{{ old('tanggal_pranota', $pranota->tanggal_pranota ? $pranota->tanggal_pranota->format('Y-m-d') : date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Total Karyawan</label>
                    <div class="text-xl font-bold text-gray-900" id="card-total-karyawan">{{ $pranota->karyawans->count() }} Orang</div>
                    <div class="text-xs text-gray-400 mt-1">Dalam pranota ini</div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Total Nominal Akhir</label>
                    <div class="text-xl font-bold text-emerald-600" id="card-total-akhir">Rp {{ number_format($pranota->total_setelah_adjustment, 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-400 mt-1" id="card-total-biaya-info">Nominal Awal: Rp {{ number_format($pranota->total_biaya, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Rincian Karyawan</h3>
                        <p class="text-xs text-gray-500">Edit adjustment (+ / -) dan catatan untuk tiap karyawan</p>
                    </div>
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="filter-edit-karyawan" placeholder="Cari nama atau NIK..." class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="table-edit-karyawans">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Karyawan</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Hari & Tanggal</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Jam Lembur</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Nominal Awal</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider w-36">Adjustment (+/-)</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Akhir</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Catatan</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-16">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-xs" id="tbody-edit-karyawans">
                            @foreach($pranota->karyawans as $index => $detail)
                            @php
                                $tglLembur = $detail->tanggal_lembur;
                                if (is_string($tglLembur)) {
                                    $tglLembur = json_decode($tglLembur, true);
                                }
                                $datesList = is_array($tglLembur) ? array_filter($tglLembur) : [];
                                $totalHari = count($datesList);
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors row-karyawan-edit" data-nama="{{ strtolower($detail->karyawan->nama_lengkap ?? '') }}" data-nik="{{ strtolower($detail->karyawan->nik ?? '') }}">
                                <td class="px-4 py-3 whitespace-nowrap text-gray-500 row-number">
                                    {{ $index + 1 }}
                                    <input type="hidden" name="karyawans[{{ $detail->id }}][detail_id]" value="{{ $detail->id }}">
                                    <input type="hidden" name="karyawans[{{ $detail->id }}][karyawan_id]" value="{{ $detail->karyawan_id }}">
                                    <input type="hidden" name="karyawans[{{ $detail->id }}][jam_lembur]" value="{{ $detail->jam_lembur }}">
                                    <input type="hidden" name="karyawans[{{ $detail->id }}][nominal_awal]" class="row-nominal-awal-hidden" value="{{ (float) $detail->nominal_awal }}">
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-bold text-gray-900">{{ $detail->karyawan->nama_lengkap ?? 'Karyawan' }}</div>
                                    <div class="text-[10px] text-gray-500 font-mono">{{ $detail->karyawan->nik ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($totalHari > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200" title="{{ implode(', ', $datesList) }}">
                                            <i class="far fa-calendar-alt mr-1 text-[10px]"></i> {{ $totalHari }} Hari
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-[11px]">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-100 text-blue-800">
                                        {{ $detail->jam_lembur }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-gray-700 row-nominal-awal" data-val="{{ (float) $detail->nominal_awal }}">
                                    Rp {{ number_format($detail->nominal_awal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <input type="number" 
                                           name="karyawans[{{ $detail->id }}][adjustment]" 
                                           class="input-row-adjustment w-28 px-2 py-1 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-right font-semibold {{ $detail->adjustment > 0 ? 'text-blue-600' : ($detail->adjustment < 0 ? 'text-rose-600' : 'text-gray-700') }}" 
                                           value="{{ (int) $detail->adjustment }}" 
                                           step="1000">
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-emerald-600 row-total-akhir" data-val="{{ (float) $detail->total_akhir }}">
                                    Rp {{ number_format($detail->total_akhir, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <input type="text" 
                                           name="karyawans[{{ $detail->id }}][catatan]" 
                                           class="w-full min-w-[140px] px-2 py-1 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                                           placeholder="Catatan..." 
                                           value="{{ $detail->catatan }}">
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <button type="button" 
                                            onclick="removeRowKaryawan(this)" 
                                            class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-800 transition-colors" 
                                            title="Hapus Karyawan ini dari Pranota">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200 text-xs font-bold text-gray-900">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right">TOTAL</td>
                                <td class="px-4 py-3 text-right text-gray-900" id="tfoot-total-biaya">
                                    Rp {{ number_format($pranota->total_biaya, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-orange-600" id="tfoot-total-adj">
                                    Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-emerald-700" id="tfoot-total-akhir">
                                    Rp {{ number_format($pranota->total_setelah_adjustment, 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Sticky Bottom Action Bar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-gray-500 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span>Karyawan yang dihapus dari pranota ini akan kembali berstatus "Belum Masuk Pranota" di kalkulasi lembur.</span>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('pranota-lembur-karyawan.show', $pranota->id) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" id="btn-submit-edit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors cursor-pointer">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    function formatRupiah(num) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(num));
    }

    function recalculateEditTable() {
        const rows = document.querySelectorAll('#tbody-edit-karyawans tr.row-karyawan-edit');
        let totalBiaya = 0;
        let totalAdj = 0;
        let totalAkhir = 0;
        let count = 0;

        rows.forEach((row, idx) => {
            count++;
            const numCell = row.querySelector('.row-number');
            if (numCell) {
                // keep the hidden inputs inside
                const hiddenInputs = numCell.querySelectorAll('input');
                numCell.childNodes[0].nodeValue = (count) + ' ';
            }

            const awalTd = row.querySelector('.row-nominal-awal');
            const adjInput = row.querySelector('.input-row-adjustment');
            const akhirTd = row.querySelector('.row-total-akhir');

            const awal = parseFloat(awalTd?.getAttribute('data-val') || 0);
            const adj = parseFloat(adjInput?.value || 0);
            const akhir = awal + adj;

            totalBiaya += awal;
            totalAdj += adj;
            totalAkhir += akhir;

            if (akhirTd) {
                akhirTd.setAttribute('data-val', akhir);
                akhirTd.innerText = formatRupiah(akhir);
            }

            if (adjInput) {
                adjInput.classList.remove('text-blue-600', 'text-rose-600', 'text-gray-700');
                if (adj > 0) adjInput.classList.add('text-blue-600');
                else if (adj < 0) adjInput.classList.add('text-rose-600');
                else adjInput.classList.add('text-gray-700');
            }
        });

        // Update cards
        const cardKaryawan = document.getElementById('card-total-karyawan');
        if (cardKaryawan) cardKaryawan.innerText = count + ' Orang';

        const cardAkhir = document.getElementById('card-total-akhir');
        if (cardAkhir) cardAkhir.innerText = formatRupiah(totalAkhir);

        const cardBiayaInfo = document.getElementById('card-total-biaya-info');
        if (cardBiayaInfo) cardBiayaInfo.innerText = 'Nominal Awal: ' + formatRupiah(totalBiaya);

        // Update tfoot
        const tfootBiaya = document.getElementById('tfoot-total-biaya');
        if (tfootBiaya) tfootBiaya.innerText = formatRupiah(totalBiaya);

        const tfootAdj = document.getElementById('tfoot-total-adj');
        if (tfootAdj) tfootAdj.innerText = (totalAdj > 0 ? '+' : '') + formatRupiah(totalAdj);

        const tfootAkhir = document.getElementById('tfoot-total-akhir');
        if (tfootAkhir) tfootAkhir.innerText = formatRupiah(totalAkhir);

        const btnSubmit = document.getElementById('btn-submit-edit');
        if (btnSubmit) {
            btnSubmit.disabled = count === 0;
            if (count === 0) {
                btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    function removeRowKaryawan(btn) {
        const rows = document.querySelectorAll('#tbody-edit-karyawans tr.row-karyawan-edit');
        if (rows.length <= 1) {
            alert('Pranota minimal harus memiliki 1 karyawan. Jika ingin menghapus seluruh pranota, gunakan tombol Hapus Pranota di halaman riwayat.');
            return;
        }

        const tr = btn.closest('tr');
        const nama = tr.querySelector('td:nth-child(2) .font-bold')?.innerText || 'karyawan ini';
        if (confirm(`Apakah Anda yakin ingin mengeluarkan ${nama} dari pranota ini?`)) {
            tr.remove();
            recalculateEditTable();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Event listeners on adjustment inputs
        document.querySelectorAll('.input-row-adjustment').forEach(input => {
            input.addEventListener('input', recalculateEditTable);
        });

        // Search filter
        const searchInput = document.getElementById('filter-edit-karyawan');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#tbody-edit-karyawans tr.row-karyawan-edit');
                rows.forEach(row => {
                    const nama = row.getAttribute('data-nama') || '';
                    const nik = row.getAttribute('data-nik') || '';
                    row.style.display = (!q || nama.includes(q) || nik.includes(q)) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush
@endsection
