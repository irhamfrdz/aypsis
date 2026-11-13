
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@push('styles')
<style>
    .dashboard-page {
        --dashboard-navy: #142D52;
        --dashboard-line: #e2e7ed;
        color: #25364b;
        font: 12px/1.5 Arial, sans-serif;
        display: flex;
        flex-direction: column;
        gap: 24px;
        min-width: 0;
    }
    .dashboard-page input, .dashboard-page select, .dashboard-page button {
        font-family: Arial, sans-serif;
    }
    .dashboard-page [class*="text-"] { font-size: 12px; }
    .dashboard-page h2 { color: var(--dashboard-navy); font-size: 20px; line-height: 1.4; margin-bottom: 4px; }
    .dashboard-page h3 { color: var(--dashboard-navy); font-size: 14px; line-height: 1.5; }
    .dashboard-page h3 > i { color: var(--dashboard-navy); margin-right: 8px; }
    .dashboard-page p { color: #6b7788; margin-top: 3px; }
    .dashboard-welcome { padding: 2px 0 0; }
    .dashboard-actions { flex-wrap: wrap; justify-content: flex-end; }
    .dashboard-date-form { flex-wrap: wrap; }
    .dashboard-page input:not([type="hidden"]), .dashboard-page select {
        border: 1px solid #d5dce5; border-radius: 6px; box-shadow: none; font-size: 12px; min-height: 34px;
    }
    .dashboard-page :is(a, button, input, select):focus-visible {
        outline: 2px solid #587ba8; outline-offset: 3px;
    }
    .dashboard-date-control { position: relative; width: 150px; }
    .dashboard-date-control input { width: 100%; background: #fff; color: transparent; }
    .dashboard-date-control input::-webkit-datetime-edit { opacity: 0; }
    .dashboard-date-label { position: absolute; z-index: 1; pointer-events: none; top: 50%; left: 11px; transform: translateY(-50%); color: #25364b; }
    .dashboard-date-form button[type="submit"] {
        background: var(--dashboard-navy); border: 1px solid var(--dashboard-navy); border-radius: 6px; min-height: 34px; padding: 6px 14px;
    }
    .dashboard-date-form button[type="submit"]:hover { background: #203f69; }
    .dashboard-panel {
        background: #fff; border: 1px solid var(--dashboard-line); border-radius: 10px;
        box-shadow: 0 2px 8px rgb(20 45 82 / 4%); overflow: hidden;
    }
    .dashboard-panel > :first-child:not(.dashboard-driver-summary),
    .dashboard-driver-summary > :first-child,
    .dashboard-subsection > :first-child {
        background: #fff; border-color: var(--dashboard-line); padding: 16px 18px;
    }
    .dashboard-page table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .dashboard-page th { background: #f6f8fa; color: #677487; font-size: 12px; font-weight: 600; padding: 11px 18px; letter-spacing: .02em; }
    .dashboard-page td { padding: 12px 18px; border-color: var(--dashboard-line); }
    .dashboard-prospek table { min-width: 520px; }
    .dashboard-prospek th:first-child { width: 30%; }
    .dashboard-prospek td span { background: transparent; color: var(--dashboard-navy); }
    .dashboard-prospek td:not(:first-child) span { font-size: 16px; font-weight: 600; }
    .dashboard-prospek td:first-child span { padding: 0; }
    .dashboard-prospek td:first-child i { color: #8391a3; margin-right: 8px; }
    .dashboard-prospek tbody tr:hover { background: #f8fafc; }
    .dashboard-prospek .dashboard-total,
    .dashboard-prospek .dashboard-total:hover { background: var(--dashboard-navy); border-color: var(--dashboard-navy); }
    .dashboard-prospek .dashboard-total :is(td, span, i) { color: #fff; border-color: var(--dashboard-navy); }
    .dashboard-driver-summary > .p-4, .dashboard-subsection > .p-4 { padding: 18px; }
    .dashboard-driver-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; }
    .dashboard-status-legend { display: flex; flex-wrap: wrap; gap: 6px 16px; padding: 12px 18px 0; color: #6b7788; }
    .dashboard-status-legend span { display: inline-flex; align-items: center; gap: 6px; }
    .dashboard-status-legend span::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: #8a9aaf; }
    .dashboard-status-legend span:nth-child(2)::before { background: #c18a32; }
    .dashboard-status-legend span:nth-child(3)::before { background: #c15a5a; }
    .dashboard-driver-card {
        background: #fff; border: 1px solid var(--dashboard-line); border-top: 3px solid #8a9aaf;
        border-radius: 10px; padding: 16px 12px; min-width: 0; justify-content: flex-start;
        transition: none; box-shadow: 0 1px 3px rgb(20 45 82 / 3%); text-decoration: none;
    }
    .dashboard-driver-card[data-work-status="idle"] { border-top-color: #c18a32; }
    .dashboard-driver-card[data-work-status="very-idle"] { border-top-color: #c15a5a; }
    .dashboard-driver-card[aria-current="true"] { background: #f3f6fb; outline: 2px solid var(--dashboard-navy); outline-offset: -1px; box-shadow: none; }
    a.dashboard-driver-card:hover { border-color: #7187a4; background: #f8fafc; box-shadow: none; }
    .dashboard-driver-card > .text-2xl { font-size: 24px; color: var(--dashboard-navy); line-height: 1.4; }
    .dashboard-driver-card > .text-xl { font-size: 20px; }
    .dashboard-driver-card [title] { max-width: 100%; }
    .dashboard-driver-card span.truncate { white-space: normal; overflow-wrap: anywhere; }
    .dashboard-driver-card > .border-t { border-color: var(--dashboard-line); padding-top: 10px; margin-top: 10px; }
    .dashboard-driver-card > .grid { margin-top: auto; padding-top: 10px; gap: 8px; }
    .dashboard-driver-card > .grid > div { background: #f6f8fa; padding: 7px; border-radius: 5px; min-width: 0; }
    .dashboard-driver-card > .grid > div > :first-child { color: #6b7788; font-size: 12px; letter-spacing: 0; }
    .dashboard-driver-card > .grid .truncate { white-space: normal; overflow-wrap: anywhere; }
    .dashboard-driver-card > .grid > div:last-child { background: #edf2f8; }
    .dashboard-subsection { border-top: 1px solid var(--dashboard-line); }
    .dashboard-subsection > :first-child { background: #f8fafc; }
    .dashboard-mutasi .grid > div { background: #f8fafc; border-color: var(--dashboard-line); border-radius: 8px; }
    .dashboard-mutasi .text-lg { font-size: 16px; }
    .dashboard-approval { margin-top: 0; }
    .dashboard-approval table { min-width: 760px; }
    .dashboard-approval .dashboard-detail {
        display: inline-flex; align-items: center; justify-content: center; min-height: 30px;
        border: 1px solid #d7e0eb; border-radius: 5px; color: var(--dashboard-navy); background: #f3f6fa; padding: 4px 12px;
    }
    .dashboard-approval .dashboard-detail:hover { background: #e6edf6; border-color: #9bacc1; }
    .dashboard-approval label { color: #677487; }
    .dashboard-approval nav [aria-current="page"] > span { background: #edf2f8; color: var(--dashboard-navy); border-color: #b6c5d8; }
    .dashboard-approval nav svg { width: 16px; height: 16px; }
    .dashboard-page #resetPermissionModal .text-white { color: #fff; }
    @media (max-width: 1100px) {
        .dashboard-welcome { align-items: flex-start; flex-direction: column; }
        .dashboard-actions { justify-content: flex-start; }
    }
    @media (max-width: 640px) {
        .dashboard-page { gap: 18px; }
        .dashboard-page h2 { font-size: 18px; }
        .dashboard-driver-grid { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; }
        .dashboard-date-form label { width: 100%; }
        .dashboard-approval > :first-child { align-items: flex-start; gap: 12px; }
        .dashboard-page th, .dashboard-page td { padding: 11px 14px; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-page">
    <!-- Welcome Message & Actions -->
    <div class="dashboard-welcome flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h2>
            <p class="text-gray-500">Berikut adalah ringkasan aktivitas sistem Anda.</p>
        </div>

        <div class="dashboard-actions flex items-center gap-3">
            <form action="{{ route('dashboard') }}" method="GET" class="dashboard-date-form flex items-center gap-2">
                @foreach(request()->except(['tanggal_dashboard', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label for="tanggal_dashboard" class="text-sm text-gray-600 font-medium whitespace-nowrap">Lihat per Tanggal:</label>
                <div class="dashboard-date-control">
                <span class="dashboard-date-label" aria-hidden="true">{{ \Carbon\Carbon::parse($filterDate ?? date('Y-m-d'))->format('d/M/Y') }}</span>
                <input type="date" id="tanggal_dashboard" name="tanggal_dashboard"
                       value="{{ $filterDate ?? date('Y-m-d') }}"
                       class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-1.5">
                </div>
                <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @if(request('tanggal_dashboard'))
                    <a href="{{ route('dashboard', request()->except(['tanggal_dashboard', 'page'])) }}" class="px-3 py-1.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors" title="Reset Tanggal">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>

        @if(auth()->check() && strtolower(auth()->user()->username) === 'kiky')
        <div class="mt-4 sm:mt-0 flex space-x-2">
            <!-- Form is hidden, but will be submitted by JS -->
            <form id="reset-permissions-form" action="{{ route('master.user.reset-all-permissions') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="password" id="reset-password-input">
            </form>

            <button type="button" onclick="openResetModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200">
                <i class="fas fa-user-shield mr-2"></i>
                Reset All Permissions
            </button>

            <a href="{{ route('backup.database') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                <i class="fas fa-database mr-2"></i>
                Backup Database
            </a>
        </div>
        @endif
        </div> <!-- End of flex gap-3 wrapper -->
    </div> <!-- End of header flex wrapper -->

        @if(auth()->check() && strtolower(auth()->user()->username) === 'kiky')
        <!-- Modal Reset Permission -->
        <div id="resetPermissionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeResetModal()"></div>

                <!-- This element is to trick the browser into centering the modal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-orange-200">
                    <div class="bg-orange-600 px-4 py-3 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Konfirmasi Keamanan
                        </h3>
                        <button type="button" class="text-white hover:text-orange-200 transition-colors focus:outline-none" onclick="closeResetModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-shield-alt text-orange-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        Anda akan menghapus semua permission user (kecuali Kiky). Tindakan ini <span class="font-bold text-red-600 underline decoration-red-300 decoration-2">tidak dapat dibatalkan</span>.
                                    </p>
                                    <p class="mt-2 text-xs text-gray-500 italic">
                                        Masukkan password login Anda untuk mengonfirmasi tindakan ini.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="modal-password" class="block text-sm font-bold text-gray-700 mb-2">Password Anda</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-key text-gray-400 group-focus-within:text-orange-500 transition-colors"></i>
                                </div>
                                <input type="password" id="modal-password"
                                       class="block w-full pl-11 pr-12 py-3.5 border-2 border-gray-100 rounded-2xl focus:ring-0 focus:border-orange-500 text-sm transition-all bg-gray-50 focus:bg-white placeholder-gray-300"
                                       placeholder="Masukkan password konfirmasi..."
                                       onkeyup="if(event.key === 'Enter') submitResetForm()">
                                <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-orange-600 transition-colors focus:outline-none">
                                    <i id="password-toggle-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
                        <button type="button" onclick="submitResetForm()"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-md px-6 py-2.5 bg-orange-600 text-base font-semibold text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:w-auto sm:text-sm transition-all hover:-translate-y-0.5">
                            Reset Sekarang
                        </button>
                        <button type="button" onclick="closeResetModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-2.5 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:w-auto sm:text-sm transition-all">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openResetModal() {
                document.getElementById('resetPermissionModal').classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('modal-password').focus();
                }, 100);
            }

            function closeResetModal() {
                document.getElementById('resetPermissionModal').classList.add('hidden');
                document.getElementById('modal-password').value = '';
                // Reset input type and icon
                const input = document.getElementById('modal-password');
                const icon = document.getElementById('password-toggle-icon');
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }

            function togglePasswordVisibility() {
                const input = document.getElementById('modal-password');
                const icon = document.getElementById('password-toggle-icon');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            function submitResetForm() {
                const password = document.getElementById('modal-password').value;
                if (!password) {
                    alert('Silakan masukkan password Anda.');
                    document.getElementById('modal-password').focus();
                    return;
                }

                document.getElementById('reset-password-input').value = password;
                document.getElementById('reset-permissions-form').submit();
            }
        </script>
        @endif

    <!-- Data Prospek Berdasarkan Tujuan dan Ukuran Kontainer -->
    <section class="dashboard-panel dashboard-prospek" aria-labelledby="prospek-heading">
        <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
            <h3 id="prospek-heading" class="text-base font-semibold text-gray-800 flex items-center">
                <i class="fas fa-chart-bar mr-1 text-blue-600"></i>
                Data Prospek Kontainer
            </h3>
            <p class="text-xs text-gray-600">Data prospek yang belum dimuat ke kapal</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            Tujuan
                        </th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-cube mr-1"></i>
                            20ft
                        </th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-cubes mr-1"></i>
                            40ft
                        </th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-truck mr-1"></i>
                            Cargo
                        </th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-calculator mr-1"></i>
                            Total
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $tujuanColors = [
                            'Jakarta' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-800', 'badge' => 'bg-blue-100'],
                            'Batam' => ['bg' => 'bg-green-50', 'text' => 'text-green-800', 'badge' => 'bg-green-100'],
                            'Pinang' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-800', 'badge' => 'bg-orange-100']
                        ];
                    @endphp

                    @foreach($prospekData as $tujuan => $data)
                        @php
                            $colors = $tujuanColors[$tujuan] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-800', 'badge' => 'bg-gray-100'];
                            $total = $data['20ft'] + $data['40ft'] + $data['Cargo'];
                        @endphp
                        <tr class="hover:{{ $colors['bg'] }} transition-colors duration-150">
                            <td class="px-3 py-2 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $colors['badge'] }} {{ $colors['text'] }}">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $tujuan }}
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="text-lg font-bold text-gray-900">{{ $data['20ft'] }}</span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="text-lg font-bold text-gray-900">{{ $data['40ft'] }}</span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="text-lg font-bold text-gray-900">{{ $data['Cargo'] }}</span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="text-lg font-bold {{ $colors['text'] }}">{{ $total }}</span>
                            </td>
                        </tr>
                    @endforeach

                    <!-- Total Row -->
                    <tr class="dashboard-total bg-gray-100 border-t-2 border-gray-300">
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-800 text-white">
                                <i class="fas fa-calculator mr-1"></i>
                                TOTAL
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <span class="text-lg font-bold text-purple-600">
                                {{ array_sum(array_column($prospekData, '20ft')) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <span class="text-lg font-bold text-indigo-600">
                                {{ array_sum(array_column($prospekData, '40ft')) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <span class="text-lg font-bold text-yellow-600">
                                {{ array_sum(array_column($prospekData, 'Cargo')) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <span class="text-xl font-bold text-gray-800">
                                {{ array_sum(array_column($prospekData, '20ft')) + array_sum(array_column($prospekData, '40ft')) + array_sum(array_column($prospekData, 'Cargo')) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="dashboard-panel dashboard-drivers" aria-labelledby="drivers-heading">
    <!-- Rekap Supir Menunggu Approval Tanda Terima & Rekap Kerja -->
    @if($rekapSupirBelumTandaTerima->count() > 0)
    <div class="dashboard-driver-summary">
        <div class="bg-indigo-50 px-4 py-3 border-b border-indigo-200 flex justify-between items-center">
            <div>
                <h3 id="drivers-heading" class="text-base font-semibold text-indigo-800 flex items-center">
                    <i class="fas fa-users mr-2 text-indigo-600"></i>
                    Rekap Supir Menunggu Approval Tanda Terima & Status Kerja
                </h3>
                <p class="text-xs text-indigo-600">Total {{ $rekapSupirBelumTandaTerima->sum('total') }} surat jalan belum disetujui di pelabuhan. Status warna juga menandakan berapa lama supir tidak mendapat Surat Jalan.</p>
            </div>
            @if(request('supir'))
                <a href="{{ route('dashboard', request()->except(['supir', 'page'])) }}" class="text-xs bg-white text-indigo-600 px-2 py-1 rounded border border-indigo-200 hover:bg-indigo-50 transition-colors">
                    Reset Filter
                </a>
            @endif
        </div>

        <div class="dashboard-status-legend" aria-label="Keterangan status kerja supir">
            <span>Kurang dari 3 hari tanpa SJ</span>
            <span>3–6 hari tanpa SJ</span>
            <span>7 hari atau lebih / belum pernah SJ</span>
        </div>
        <div class="p-4">
            <div class="dashboard-driver-grid">
                @foreach($rekapSupirBelumTandaTerima as $data)
                @php
                    $isActive = request('supir') == $data->supir;
                    $hasOverdue = false;
                    if ($data->oldest_uang_jalan) {
                        $tanggalUj = \Carbon\Carbon::parse($data->oldest_uang_jalan)->startOfDay();
                        $hariIni = \Carbon\Carbon::parse($filterDate ?? now())->startOfDay();
                        $hasOverdue = $tanggalUj->diffInDays($hariIni) > 3 && $tanggalUj->isBefore($hariIni);
                    }

                    // Logic for work status
                    $hariIni = \Carbon\Carbon::parse($filterDate ?? now())->startOfDay();
                    $lamaTidakKerja = 0;
                    $isIdle = false;
                    $isVeryIdle = false;

                    if ($data->terakhir_surat_jalan) {
                        $tanggalTerakhir = \Carbon\Carbon::parse($data->terakhir_surat_jalan)->startOfDay();
                        $lamaTidakKerja = $tanggalTerakhir->diffInDays($hariIni);
                        $isIdle = $lamaTidakKerja >= 3 && $lamaTidakKerja < 7;
                        $isVeryIdle = $lamaTidakKerja >= 7;
                    } else {
                        $isVeryIdle = true; // Belum pernah dapat SJ
                    }

                    if ($isActive) {
                        $cardClass = $hasOverdue ? 'bg-red-100 border-red-500 ring-1 ring-red-500' : 'bg-indigo-100 border-indigo-500 ring-1 ring-indigo-500';
                    } else {
                        if ($isVeryIdle) {
                            $cardClass = 'bg-red-50 border-red-200 hover:border-red-400 hover:shadow-sm';
                        } elseif ($isIdle) {
                            $cardClass = 'bg-orange-50 border-orange-200 hover:border-orange-400 hover:shadow-sm';
                        } else {
                            $cardClass = 'bg-gray-50 border-gray-200 hover:border-indigo-300 hover:shadow-sm';
                        }
                    }

                    $supirClass = $isActive ? ($hasOverdue ? 'text-red-700' : 'text-indigo-700') : ($isVeryIdle ? 'text-red-600' : ($isIdle ? 'text-orange-600' : 'text-gray-600'));
                    $totalClass = $isActive ? ($hasOverdue ? 'text-red-900' : 'text-indigo-900') : ($isVeryIdle ? 'text-red-800' : ($isIdle ? 'text-orange-800' : 'text-gray-800'));
                    $labelClass = $isActive ? ($hasOverdue ? 'text-red-600' : 'text-indigo-600') : ($isVeryIdle ? 'text-red-500' : ($isIdle ? 'text-orange-500' : 'text-gray-500'));
                @endphp
                <a href="{{ route('dashboard', array_merge(request()->except('page'), ['supir' => $isActive ? null : $data->supir])) }}"
                   aria-current="{{ $isActive ? 'true' : 'false' }}" data-work-status="{{ $isVeryIdle ? 'very-idle' : ($isIdle ? 'idle' : 'active') }}" class="dashboard-driver-card {{ $cardClass }} rounded-lg p-3 border flex flex-col items-center justify-center text-center transition-all duration-200 cursor-pointer group relative">
                    <div class="flex flex-col items-center justify-center gap-1 mb-1">
                        <div class="flex items-center justify-center gap-1">
                            <span class="text-xs {{ $supirClass }} font-semibold uppercase tracking-wider truncate max-w-[100px]" title="{{ $data->nama_lengkap ?? $data->supir }}">
                                {{ $data->supir ?: 'Tanpa Nama' }}
                            </span>
                            @if($hasOverdue)
                                <i class="fas fa-exclamation-triangle text-red-500 text-[10px]" title="Ada SJ tertunda > 3 hari"></i>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center justify-center gap-1">
                            @if(isset($data->is_customer) && $data->is_customer)
                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 font-medium">NON-AYP</span>
                            @endif
                            @if(isset($data->is_vendor) && $data->is_vendor)
                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-teal-100 text-teal-700 font-medium">VENDOR</span>
                            @endif
                        </div>
                    </div>

                    @if($data->total > 0)
                        <span class="text-2xl font-bold {{ $totalClass }}">
                            {{ $data->total }}
                        </span>
                        <span class="text-[10px] {{ $labelClass }}">Surat Jalan Tertunda</span>
                    @else
                        <span class="text-2xl font-bold text-green-600">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <span class="text-[10px] text-green-600">Semua SJ Selesai</span>
                    @endif

                    <div class="dashboard-driver-status mt-2 pt-2 border-t {{ $isVeryIdle ? 'border-red-200' : ($isIdle ? 'border-orange-200' : 'border-gray-200') }} w-full">
                        <span class="text-[10px] font-medium {{ $isVeryIdle ? 'text-red-600' : ($isIdle ? 'text-orange-600' : 'text-gray-500') }} block leading-tight">
                            @if($data->terakhir_surat_jalan)
                                {{ $lamaTidakKerja }} Hari Tidak SJ<br>
                                <span class="text-[9px] opacity-75">Trx: {{ \Carbon\Carbon::parse($data->terakhir_surat_jalan)->format('d/M/Y') }}</span>
                            @else
                                Belum Pernah SJ
                            @endif
                        </span>
                    </div>

                    @if(isset($data->mutasi))
                    <div class="mt-2 pt-2 border-t {{ $isVeryIdle ? 'border-red-200' : ($isIdle ? 'border-orange-200' : 'border-gray-200') }} w-full grid grid-cols-2 gap-x-2 gap-y-2 text-left">
                        <div class="bg-white/50 rounded px-1.5 py-1">
                            <div class="text-[9px] text-gray-500 font-medium uppercase tracking-wider mb-0.5">Awal</div>
                            <div class="font-bold text-gray-800 text-xs truncate" title="Rp {{ number_format($data->mutasi->saldo_akhir <= 0 ? 0 : $data->mutasi->saldo_awal, 0, ',', '.') }}">Rp {{ number_format($data->mutasi->saldo_akhir <= 0 ? 0 : $data->mutasi->saldo_awal, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-red-50/50 rounded px-1.5 py-1">
                            <div class="text-[9px] text-red-500 font-medium uppercase tracking-wider mb-0.5">Debit</div>
                            <div class="font-bold text-red-700 text-xs truncate" title="Rp {{ number_format($data->mutasi->saldo_akhir <= 0 ? 0 : $data->mutasi->debit, 0, ',', '.') }}">Rp {{ number_format($data->mutasi->saldo_akhir <= 0 ? 0 : $data->mutasi->debit, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-green-50/50 rounded px-1.5 py-1">
                            <div class="text-[9px] text-green-500 font-medium uppercase tracking-wider mb-0.5">Kredit</div>
                            <div class="font-bold text-green-700 text-xs truncate" title="Rp {{ number_format($data->mutasi->saldo_akhir <= 0 ? 0 : $data->mutasi->kredit, 0, ',', '.') }}">Rp {{ number_format($data->mutasi->saldo_akhir <= 0 ? 0 : $data->mutasi->kredit, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-indigo-50/50 rounded px-1.5 py-1">
                            <div class="text-[9px] text-indigo-500 font-medium uppercase tracking-wider mb-0.5">Akhir</div>
                            <div class="font-bold {{ $data->mutasi->saldo_akhir > 0 ? 'text-red-700' : 'text-green-700' }} text-xs truncate" title="Rp {{ number_format($data->mutasi->saldo_akhir, 0, ',', '.') }}">Rp {{ number_format($data->mutasi->saldo_akhir, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Rekap Kerja Supir (Jakarta) -->
    @if(isset($rekapKerjaSupirJakarta) && $rekapKerjaSupirJakarta->count() > 0)
    <div class="dashboard-subsection dashboard-jakarta">
        <div class="bg-blue-50 px-4 py-3 border-b border-blue-200 flex justify-between items-center">
            <div>
                <h3 class="text-base font-semibold text-blue-800 flex items-center">
                    <i class="fas fa-truck mr-2 text-blue-600"></i>
                    Rekap Kerja Supir (Cabang Jakarta)
                </h3>
                <p class="text-xs text-blue-600">Memonitor supir yang bekerja berdasarkan waktu terakhir mendapatkan Surat Jalan</p>
            </div>
        </div>

        <div class="p-4">
            <div class="dashboard-driver-grid">
                @foreach($rekapKerjaSupirJakarta as $data)
                @php
                    $hariIni = now()->startOfDay();
                    $lamaTidakKerja = 0;
                    $isIdle = false;
                    $isVeryIdle = false;

                    if ($data->terakhir_surat_jalan) {
                        $tanggalTerakhir = \Carbon\Carbon::parse($data->terakhir_surat_jalan)->startOfDay();
                        $lamaTidakKerja = $tanggalTerakhir->diffInDays($hariIni);
                        $isIdle = $lamaTidakKerja >= 3 && $lamaTidakKerja < 7;
                        $isVeryIdle = $lamaTidakKerja >= 7;
                    } else {
                        $isVeryIdle = true; // Belum pernah dapat SJ
                    }

                    if ($isVeryIdle) {
                        $cardClass = 'bg-red-50 border-red-200 hover:border-red-400 hover:shadow-sm';
                        $supirClass = 'text-red-700';
                        $statusClass = 'text-red-900';
                        $labelClass = 'text-red-600';
                    } elseif ($isIdle) {
                        $cardClass = 'bg-orange-50 border-orange-200 hover:border-orange-400 hover:shadow-sm';
                        $supirClass = 'text-orange-700';
                        $statusClass = 'text-orange-900';
                        $labelClass = 'text-orange-600';
                    } else {
                        $cardClass = 'bg-gray-50 border-gray-200 hover:border-blue-300 hover:shadow-sm';
                        $supirClass = 'text-gray-600 group-hover:text-blue-700';
                        $statusClass = 'text-gray-800 group-hover:text-blue-900';
                        $labelClass = 'text-gray-500 group-hover:text-blue-600';
                    }
                @endphp
                <div data-work-status="{{ $isVeryIdle ? 'very-idle' : ($isIdle ? 'idle' : 'active') }}" class="dashboard-driver-card {{ $cardClass }} rounded-lg p-3 border flex flex-col items-center justify-center text-center transition-all duration-200 group relative">
                    <div class="flex items-center justify-center gap-1 mb-1">
                        <span class="text-xs {{ $supirClass }} font-semibold uppercase tracking-wider truncate w-full" title="{{ $data->nama_lengkap }}">
                            {{ $data->nama_panggilan ?: 'Tanpa Nama' }}
                        </span>
                        @if($isVeryIdle)
                            <i class="fas fa-exclamation-triangle text-red-500 text-[10px]" title="Lebih dari 7 hari atau belum pernah"></i>
                        @endif
                    </div>
                    <span class="text-xl font-bold {{ $statusClass }}">
                        @if($data->terakhir_surat_jalan)
                            {{ $lamaTidakKerja }} <span class="text-[11px] font-normal">Hari</span>
                        @else
                            <span class="text-sm">Belum Ada</span>
                        @endif
                    </span>
                    <span class="text-[10px] {{ $labelClass }} mt-1 block">
                        @if($data->terakhir_surat_jalan)
                            Terakhir SJ: {{ \Carbon\Carbon::parse($data->terakhir_surat_jalan)->format('d/M/Y') }}
                        @else
                            Tidak Ada SJ
                        @endif
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Mutasi Uang Jalan per Supir --}}
    @if(isset($mutasiUangJalan) && $mutasiUangJalan)
    <div class="dashboard-subsection dashboard-mutasi">
        <div class="bg-blue-50 px-4 py-3 border-b border-blue-200">
            <h3 class="text-base font-semibold text-blue-800 flex items-center">
                <i class="fas fa-wallet mr-2 text-blue-600"></i>
                Rekap Uang Jalan — {{ $mutasiUangJalan->supir === 'NON_AYP' ? 'Supir Non-AYP' : $mutasiUangJalan->supir }}
            </h3>
            <p class="text-xs text-blue-600">Ringkasan mutasi uang jalan sampai dengan {{ \Carbon\Carbon::parse($filterDate ?? now())->format('d/M/Y') }}</p>
        </div>

        <div class="p-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- Saldo Awal --}}
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 text-center">
                    <span class="text-xs text-gray-500 font-medium uppercase tracking-wider block mb-1">Saldo Awal</span>
                    <span class="text-lg font-bold text-gray-800">Rp {{ number_format($mutasiUangJalan->saldo_akhir <= 0 ? 0 : $mutasiUangJalan->saldo_awal, 0, ',', '.') }}</span>
                </div>

                {{-- Total Debit (Uang Jalan Keluar) --}}
                <div class="bg-red-50 rounded-lg p-4 border border-red-200 text-center">
                    <span class="text-xs text-red-500 font-medium uppercase tracking-wider block mb-1">
                        <i class="fas fa-arrow-up mr-1"></i> Debit (UJ Keluar)
                    </span>
                    <span class="text-lg font-bold text-red-700">Rp {{ number_format($mutasiUangJalan->saldo_akhir <= 0 ? 0 : $mutasiUangJalan->total_debit, 0, ',', '.') }}</span>
                </div>

                {{-- Total Kredit (Tanda Terima Disetujui Pelabuhan) --}}
                <div class="bg-green-50 rounded-lg p-4 border border-green-200 text-center">
                    <span class="text-xs text-green-500 font-medium uppercase tracking-wider block mb-1">
                        <i class="fas fa-arrow-down mr-1"></i> Kredit (TT Disetujui)
                    </span>
                    <span class="text-lg font-bold text-green-700">Rp {{ number_format($mutasiUangJalan->saldo_akhir <= 0 ? 0 : $mutasiUangJalan->total_kredit, 0, ',', '.') }}</span>
                </div>

                {{-- Saldo Akhir --}}
                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200 text-center">
                    <span class="text-xs text-indigo-500 font-medium uppercase tracking-wider block mb-1">Saldo Akhir (Outstanding)</span>
                    <span class="text-lg font-bold {{ $mutasiUangJalan->saldo_akhir > 0 ? 'text-red-700' : 'text-green-700' }}">
                        Rp {{ number_format($mutasiUangJalan->saldo_akhir, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif
    </section>

    <!-- Data Surat Jalan Menunggu Approval Tanda Terima -->
    <div class="dashboard-panel dashboard-approval">
        <div class="bg-red-50 px-4 py-3 border-b border-red-200 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-base font-semibold text-red-800 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1 text-red-600"></i>
                    Approval Tanda Terima
                </h3>
                <p class="text-xs text-red-600">Daftar surat jalan yang tanda terimanya belum disetujui di pelabuhan (Sudah Bayar Uang Jalan)</p>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <!-- Preserve existing query params except per_page -->
                @foreach(request()->except('per_page', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                <label for="per_page" class="text-xs text-red-700 font-medium whitespace-nowrap">Tampilkan:</label>
                <select name="per_page" id="per_page" onchange="this.form.submit()" class="text-xs border-red-300 focus:border-red-500 focus:ring-red-500 rounded shadow-sm bg-white text-gray-700 py-1 pl-2 pr-6">
                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : (!request('per_page') ? 'selected' : '') }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No Surat Jalan</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Uang Jalan</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pengirim</th>
                         <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tujuan</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supir</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suratJalanBelumTandaTerima as $sj)
                        @php
                            $isLebihDari3Hari = false;
                            if (optional($sj->uangJalan)->tanggal_uang_jalan) {
                                $tanggalUj = $sj->uangJalan->tanggal_uang_jalan->copy()->startOfDay();
                                $hariIni = now()->startOfDay();
                                $isLebihDari3Hari = $tanggalUj->diffInDays($hariIni) > 3 && $tanggalUj->isBefore($hariIni);
                            }
                            $rowClass = $isLebihDari3Hari ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-red-50';
                            $textClassPrimary = $isLebihDari3Hari ? 'text-red-700 font-bold' : 'text-gray-900 font-medium';
                            $textClassSecondary = $isLebihDari3Hari ? 'text-red-600 font-medium' : 'text-gray-500';
                        @endphp
                        <tr class="{{ $rowClass }} transition-colors duration-150">
                            <td class="px-3 py-2 whitespace-nowrap {{ $textClassPrimary }}">
                                {{ $sj->no_surat_jalan }}
                                @if($isLebihDari3Hari)
                                    <i class="fas fa-exclamation-triangle ml-1 text-red-500" title="Lebih dari 3 hari"></i>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap {{ $textClassSecondary }}">
                                {{ optional($sj->uangJalan)->tanggal_uang_jalan ? $sj->uangJalan->tanggal_uang_jalan->format('d/M/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap {{ $textClassSecondary }}">
                                {{ $sj->pengirimRelation->nama ?? $sj->pengirim }}
                            </td>
                             <td class="px-3 py-2 whitespace-nowrap {{ $textClassSecondary }}">
                                {{ $sj->tujuanPengirimanRelation->nama ?? $sj->order->tujuan_kirim ?? $sj->tujuan_pengiriman }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap {{ $textClassPrimary }}">
                                {{ $sj->supir }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <a href="{{ route('surat-jalan.show', $sj->id) }}" class="dashboard-detail text-blue-600 hover:text-blue-900 text-xs font-medium">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-gray-500 italic">
                                Semua surat jalan sudah disetujui di pelabuhan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($suratJalanBelumTandaTerima->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $suratJalanBelumTandaTerima->links() }}
        </div>
        @endif
    </div>


</div>

@php
    $authUser = auth()->user();
    $karyawan = $authUser->karyawan;
    $isAuthorizedApprover = false;

    if ($karyawan) {
        $pekerjaan = strtoupper($karyawan->pekerjaan ?? '');
        if (in_array($pekerjaan, ['HRD', 'IT'])) {
            $isAuthorizedApprover = true;
        } else {
            // Check if they are a supervisor (have subordinates)
            $subordinatesCount = \App\Models\Karyawan::where('nik_supervisor', $karyawan->nik)->count();
            if ($subordinatesCount > 0) {
                $isAuthorizedApprover = true;
            }
        }
    }

    // Also allow super-admin or specific users
    if ($authUser->hasRole('super-admin') || $authUser->username === 'kiky') {
        $isAuthorizedApprover = true;
    }
@endphp

@if($isAuthorizedApprover)
    <!-- Modal Notifikasi Persetujuan Absensi -->
    <div id="approvalNotifModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="bg-blue-600 px-4 py-4 flex items-center justify-center relative">
                <div class="absolute -top-6 -right-6 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>

                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-2 shadow-inner">
                        <i class="fas fa-bell text-3xl text-blue-600 animate-pulse"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white text-center">Permintaan Persetujuan</h3>
                </div>
            </div>

            <div class="p-6 text-center">
                <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                    Terdapat <strong id="approvalNotifCount" class="text-blue-600 text-lg">0</strong> permohonan izin/absensi yang menunggu persetujuan Anda.
                </p>

                <div class="flex flex-col gap-2">
                    <a href="{{ route('master.persetujuan-absensi.index') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow transition-colors flex items-center justify-center">
                        <i class="fas fa-external-link-alt mr-2"></i> Tinjau Sekarang
                    </a>
                    <button onclick="closeApprovalNotifModal()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        Nanti Saja
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // Check session storage so we don't spam the user every time they go to dashboard
            if (sessionStorage.getItem('approval_notif_shown')) {
                return;
            }

            try {
                // Fetch attendance requests
                const resAtt = await fetch('{{ url("/master/api/admin/pending-attendance") }}');
                const dataAtt = await resAtt.json();

                // Fetch permission/leave requests
                const resPerm = await fetch('{{ url("/master/api/admin/pending-permissions") }}');
                const dataPerm = await resPerm.json();

                const totalPending = dataAtt.length + dataPerm.length;

                if (totalPending > 0) {
                    const modal = document.getElementById('approvalNotifModal');
                    document.getElementById('approvalNotifCount').innerText = totalPending;

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');

                    // Trigger animation
                    setTimeout(() => {
                        modal.classList.remove('opacity-0');
                        modal.querySelector('.transform').classList.remove('scale-95');
                        modal.querySelector('.transform').classList.add('scale-100');
                    }, 50);

                    // Mark as shown for this session
                    sessionStorage.setItem('approval_notif_shown', 'true');
                }
            } catch (err) {
                console.error('Error fetching pending approvals:', err);
            }
        });

        function closeApprovalNotifModal() {
            const modal = document.getElementById('approvalNotifModal');
            modal.classList.add('opacity-0');
            modal.querySelector('.transform').classList.remove('scale-100');
            modal.querySelector('.transform').classList.add('scale-95');

            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
@endif

@endsection


@push('scripts')
<script>
    document.getElementById('tanggal_dashboard').addEventListener('change', function () {
        const parts = this.value.split('-').map(Number);
        const label = this.parentElement.querySelector('.dashboard-date-label');
        label.textContent = this.value ? new Intl.DateTimeFormat('en-GB', {
            day: '2-digit', month: 'short', year: 'numeric'
        }).format(new Date(parts[0], parts[1] - 1, parts[2])).replace(/ /g, '/').replace('Sept', 'Sep') : 'dd/mmm/yyyy';
    });
</script>
@endpush
