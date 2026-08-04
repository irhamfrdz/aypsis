@extends('layouts.app')

@section('title', 'Perhitungan Lembur Karyawan')
@section('page_title', 'Perhitungan Lembur Karyawan')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Perhitungan Lembur Karyawan</h1>
                    <p class="mt-1 text-sm text-gray-600">Perhitungan akumulasi jam dan uang lembur per karyawan</p>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form action="{{ route('payroll.perhitungan-lembur') }}" method="GET" class="space-y-4" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search Karyawan -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold text-gray-700 mb-1">Cari Karyawan / NIK</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Nama atau NIK..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                    </div>

                    <!-- Group -->
                    <div class="md:col-span-1">
                        <label for="grup" class="block text-xs font-semibold text-gray-700 mb-1">Group</label>
                        <select name="grup" id="grup"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs"
                                onchange="handleGrupFilterChange()">
                            <option value="">Semua Group</option>
                            @foreach($grupsList as $g)
                                <option value="{{ $g }}" {{ request('grup') == $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sub Group -->
                    <div class="md:col-span-1">
                        <label for="sub_grup" class="block text-xs font-semibold text-gray-700 mb-1">Sub Group</label>
                        <select name="sub_grup" id="sub_grup"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            <option value="">Semua Sub Group</option>
                        </select>
                    </div>

                    <!-- Group BPJS -->
                    <div class="md:col-span-1">
                        <label for="grup_bpjs" class="block text-xs font-semibold text-gray-700 mb-1">Group BPJS</label>
                        <select name="grup_bpjs" id="grup_bpjs"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs"
                                onchange="handleGrupBpjsFilterChange()">
                            <option value="">Semua Group BPJS</option>
                            @foreach($grupsBpjsList as $g)
                                <option value="{{ $g }}" {{ request('grup_bpjs') == $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sub Group BPJS -->
                    <div class="md:col-span-1">
                        <label for="sub_grup_bpjs" class="block text-xs font-semibold text-gray-700 mb-1">Sub Group BPJS</label>
                        <select name="sub_grup_bpjs" id="sub_grup_bpjs"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            <option value="">Semua Sub Group BPJS</option>
                        </select>
                    </div>

                    <!-- Tingkat Kehadiran -->
                    <div class="md:col-span-1">
                        <label for="kehadiran" class="block text-xs font-semibold text-gray-700 mb-1">Tingkat Kehadiran</label>
                        <select name="kehadiran" id="kehadiran"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                            <option value="">Semua Tingkat</option>
                            <option value="0_hari" {{ request('kehadiran') == '0_hari' ? 'selected' : '' }}>0 Hari (Tidak Absen)</option>
                            <option value="ada_absen" {{ request('kehadiran') == 'ada_absen' ? 'selected' : '' }}>Ada Absen (> 0 Hari)</option>
                            <option value="tidak_lengkap" {{ request('kehadiran') == 'tidak_lengkap' ? 'selected' : '' }}>Hanya Masuk/Pulang Saja</option>
                        </select>
                    </div>

                    <!-- Dari Tanggal -->
                    <div class="md:col-span-1">
                        <label for="start_date" class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" required
                               value="{{ $startDateStr }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                    </div>

                    <!-- Sampai Tanggal -->
                    <div class="md:col-span-1">
                        <label for="end_date" class="block text-xs font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" required
                               value="{{ $endDateStr }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 text-xs">
                    </div>

                    <!-- Action Buttons -->
                    <div class="md:col-span-5 flex items-end gap-2 justify-end mt-2">
                        @if(request()->anyFilled(['search', 'penempatan', 'grup', 'sub_grup', 'grup_bpjs', 'sub_grup_bpjs', 'kehadiran']))
                            <a href="{{ route('payroll.perhitungan-lembur') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-lg focus:outline-none transition-colors duration-200 h-[38px] shadow-sm">
                                Reset
                            </a>
                        @endif
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg focus:outline-none transition-colors duration-200 h-[38px] shadow-sm">
                            Filter Rekap
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-16">
                                No
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                NIK
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Nama Karyawan
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Lembur (H. Biasa)
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Lembur (H. Libur)
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Total Bayar
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rekapData as $id => $data)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-indigo-600 text-sm font-semibold">
                                    {{ $data['karyawan']->nik }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $data['karyawan']->nama_lengkap }}
                                    <div class="text-xs text-gray-500 mt-1">{{ $data['karyawan']->penempatan ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    @if($data['total_jam_biasa'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $data['total_jam_biasa'] }} Jam
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    @if($data['total_jam_libur'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $data['total_jam_libur'] }} Jam
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-emerald-600">
                                    Rp {{ number_format($data['total_nominal'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <button type="button" data-nama="{{ $data['karyawan']->nama_lengkap }}" data-detail="{{ json_encode($data['detail']) }}" class="btn-detail text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors font-medium">
                                        Lihat Rincian
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-file-invoice-dollar text-2xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900">Tidak ada data lembur</h3>
                                        <p class="mt-1 text-sm text-gray-500">Silakan sesuaikan filter tanggal atau karyawan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-list text-indigo-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="detailTitle">
                            Rincian Lembur
                        </h3>
                        <div class="mt-4 max-h-[60vh] overflow-y-auto pr-2" id="detailContent">
                            <!-- Content will be injected here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeDetailModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.btn-detail').forEach(btn => {
        btn.addEventListener('click', function() {
            const nama = this.getAttribute('data-nama');
            let details = [];
            try {
                details = JSON.parse(this.getAttribute('data-detail'));
            } catch (e) {
                console.error("Gagal parse details", e);
            }
            
            let html = '<table class="min-w-full divide-y divide-gray-200 mt-2"><thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th><th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Tipe Hari</th><th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Jam Pulang</th><th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Durasi</th><th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Tarif/Rule</th><th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Nominal</th></tr></thead><tbody class="divide-y divide-gray-200">';
            
            let total = 0;
            if (details.length > 0) {
                details.forEach(row => {
                    html += `<tr>
                        <td class="px-4 py-2 text-sm text-gray-900">${row.tanggal}</td>
                        <td class="px-4 py-2 text-sm text-gray-500">${row.tipe_hari}</td>
                        <td class="px-4 py-2 text-sm text-center text-gray-900 font-mono">${row.jam_pulang}</td>
                        <td class="px-4 py-2 text-sm text-center font-bold text-indigo-600">${row.durasi_jam} Jam</td>
                        <td class="px-4 py-2 text-sm text-gray-500">${row.rule}</td>
                        <td class="px-4 py-2 text-sm text-right font-bold text-emerald-600">Rp ${Number(row.nominal).toLocaleString('id-ID')}</td>
                    </tr>`;
                    total += Number(row.nominal);
                });
                
                html += `<tr class="bg-gray-50">
                    <td colspan="5" class="px-4 py-3 text-right text-sm font-bold text-gray-900">TOTAL</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-emerald-700">Rp ${total.toLocaleString('id-ID')}</td>
                </tr>`;
            } else {
                html += '<tr><td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada rincian</td></tr>';
            }
            html += '</tbody></table>';
            
            document.getElementById('detailTitle').innerText = 'Rincian Lembur: ' + nama;
            document.getElementById('detailContent').innerHTML = html;
            document.getElementById('detailModal').classList.remove('hidden');
        });
    });

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    const grupMap = @json($grupMap ?? []);
    const oldSubGrup = '{{ request('sub_grup') }}';
    const oldGrup = '{{ request('grup') }}';

    const grupBpjsMap = @json($grupBpjsMap ?? []);
    const oldSubGrupBpjs = '{{ request('sub_grup_bpjs') }}';
    const oldGrupBpjs = '{{ request('grup_bpjs') }}';

    function handleGrupFilterChange() {
        const grupSelect = document.getElementById('grup');
        const subGrupSelect = document.getElementById('sub_grup');
        const selectedGrup = grupSelect.value;
        
        // Reset Sub Group options
        subGrupSelect.innerHTML = '<option value="">Semua Sub Group</option>';
        
        if (selectedGrup && grupMap[selectedGrup]) {
            const subs = grupMap[selectedGrup];
            subs.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub;
                option.text = sub;
                subGrupSelect.appendChild(option);
            });
        }
    }

    function handleGrupBpjsFilterChange() {
        const grupBpjsSelect = document.getElementById('grup_bpjs');
        const subGrupBpjsSelect = document.getElementById('sub_grup_bpjs');
        const selectedGrupBpjs = grupBpjsSelect.value;
        
        // Reset Sub Group options
        subGrupBpjsSelect.innerHTML = '<option value="">Semua Sub Group BPJS</option>';
        
        if (selectedGrupBpjs && grupBpjsMap[selectedGrupBpjs]) {
            const subs = grupBpjsMap[selectedGrupBpjs];
            subs.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub;
                option.text = sub;
                subGrupBpjsSelect.appendChild(option);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        handleGrupFilterChange();
        if (oldSubGrup) {
            document.getElementById('sub_grup').value = oldSubGrup;
        }

        handleGrupBpjsFilterChange();
        if (oldSubGrupBpjs) {
            document.getElementById('sub_grup_bpjs').value = oldSubGrupBpjs;
        }
    });
</script>
@endpush
@endsection
