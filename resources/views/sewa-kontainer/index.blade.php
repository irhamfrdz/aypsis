@extends('layouts.app')

@section('title', 'Sewa Kontainer')
@section('page_title', 'Portal Sewa Kontainer')

@push('styles')
<style>
    /* Premium visual effects */
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .theme-transition {
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    /* Tab active styles */
    .tab-active {
        border-bottom-width: 2px;
    }
</style>
@endpush

@section('content')
<div class="flex-1 overflow-y-auto p-6 theme-transition" id="sewa-viewport">
    <!-- Top Bar with Mode selector & Info -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-ship text-emerald-600" id="header-icon"></i>
                <span>Portal Sewa Kontainer</span>
                <span class="text-xs px-2.5 py-1 rounded-full font-bold uppercase border theme-transition" id="badge-mode">
                    Sewa Out (Lessor)
                </span>
            </h2>
            <p class="text-sm text-gray-500 mt-1" id="mode-desc">
                Sistem kalkulasi proris maret ke januari (30 hari) & tahun kabisat februari (28/29 hari)
            </p>
        </div>

        <!-- Mode Toggle Switch -->
        <div class="bg-gray-200 p-1 rounded-xl flex gap-1 border border-gray-300">
            <button onclick="setAppMode('sewa_out')" id="btn-sewa-out" class="px-4 py-2 rounded-lg text-xs font-bold transition-all duration-300 flex items-center gap-2 cursor-pointer bg-white text-emerald-700 shadow-sm">
                <i class="fas fa-sign-out-alt"></i> Sewa Out (Lessor)
            </button>
            <button onclick="setAppMode('sewa_in')" id="btn-sewa-in" class="px-4 py-2 rounded-lg text-xs font-bold transition-all duration-300 flex items-center gap-2 cursor-pointer text-gray-600 hover:bg-gray-100">
                <i class="fas fa-sign-in-alt"></i> Sewa In (Lessee)
            </button>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <i class="fas fa-box text-2xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Kontainer</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ count($kontainers) }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <i class="fas fa-file-contract text-2xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Rental Aktif</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ count($sewas->where('status_sewa', 'Aktif')) }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-red-50 text-red-600 rounded-xl">
                <i class="fas fa-clock text-2xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Belum Lunas</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1 text-red-600">
                    Rp {{ number_format($tagihans->whereIn('status_bayar', ['Belum Bayar', 'Belum Ditagih'])->sum('jumlah_tagihan'), 0, ',', '.') }}
                </h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl" id="kpi-lunas-icon">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Realisasi</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1 text-emerald-600" id="kpi-lunas-val">
                    Rp {{ number_format($tagihans->where('status_bayar', 'Lunas')->sum('jumlah_tagihan'), 0, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 mb-6 flex overflow-x-auto gap-4">
        <button onclick="switchTab('billing')" id="tab-billing" class="tab-btn py-3 px-4 text-sm font-semibold border-b-2 border-emerald-600 text-emerald-600">
            <i class="fas fa-file-invoice-dollar mr-2"></i>Billing & Pembayaran
        </button>
        <button onclick="switchTab('contracts')" id="tab-contracts" class="tab-btn py-3 px-4 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700">
            <i class="fas fa-handshake mr-2"></i>Transaksi Kontrak
        </button>
        <button onclick="switchTab('master')" id="tab-master" class="tab-btn py-3 px-4 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700">
            <i class="fas fa-database mr-2"></i>Master Database
        </button>
        <button onclick="switchTab('import')" id="tab-import" class="tab-btn py-3 px-4 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700">
            <i class="fas fa-file-excel mr-2"></i>Bulk Import/Backup
        </button>
    </div>

    <!-- Tab 1: Billing & Tagihan -->
    <div id="content-billing" class="tab-content">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-200 flex flex-wrap justify-between items-center gap-4">
                <h4 class="font-bold text-gray-800 text-lg">Daftar Tagihan Periodik</h4>
                <div class="flex gap-2">
                    <button onclick="openInvoiceModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                        <i class="fas fa-file-invoice mr-1"></i> Buat Invoice Grup
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider text-[10px] font-bold">
                            <th class="p-4">Tagihan ID</th>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Kontainer</th>
                            <th class="p-4">Bulan Ke</th>
                            <th class="p-4">Rentang Tanggal</th>
                            <th class="p-4">Tipe Tarif</th>
                            <th class="p-4">Estimasi Tagihan</th>
                            <th class="p-4">Status Bayar</th>
                            <th class="p-4">Invoice No</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse($tagihans as $tagihan)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="p-4 font-mono text-xs">{{ $tagihan->id_tagihan }}</td>
                            <td class="p-4 font-semibold">{{ $tagihan->transaksi->customer->nama_customer ?? '-' }}</td>
                            <td class="p-4">{{ $tagihan->transaksi->kontainer->no_kontainer ?? '-' }}</td>
                            <td class="p-4 text-center">{{ $tagihan->bulan_ke }}</td>
                            <td class="p-4 text-xs">
                                {{ date('d/m/Y', strtotime($tagihan->tanggal_awal)) }} - {{ date('d/m/Y', strtotime($tagihan->tanggal_akhir)) }}
                                <br><span class="text-[10px] text-gray-400">({{ $tagihan->jumlah_hari }} hari)</span>
                            </td>
                            <td class="p-4 text-xs font-semibold">{{ $tagihan->tipe_tarif }}</td>
                            <td class="p-4 font-bold text-gray-800">
                                Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-full text-xs font-bold uppercase
                                    @if($tagihan->status_bayar === 'Lunas') bg-emerald-100 text-emerald-800
                                    @elseif($tagihan->status_bayar === 'Belum Bayar') bg-red-100 text-red-800
                                    @elseif($tagihan->status_bayar === 'Pranota') bg-indigo-100 text-indigo-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $tagihan->status_bayar }}
                                </span>
                            </td>
                            <td class="p-4 text-xs font-mono text-blue-600 font-semibold">{{ $tagihan->nomor_invoice_grup ?: '-' }}</td>
                            <td class="p-4 text-center">
                                <button onclick="editPaymentOverride('{{ json_encode($tagihan) }}')" class="p-1 px-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs font-bold transition-all">
                                    <i class="fas fa-edit mr-1"></i> Override
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-gray-400">Belum ada tagihan periodik yang tergenerate.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 2: Transaksi Kontrak -->
    <div id="content-contracts" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                <h4 class="font-bold text-gray-800 text-lg">Daftar Kontrak Sewa Kontainer</h4>
                <button onclick="openSewaModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all">
                    <i class="fas fa-plus mr-1"></i> Tambah Kontrak Baru
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider text-[10px] font-bold">
                            <th class="p-4">Sewa ID</th>
                            <th class="p-4">No Kontainer</th>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Tanggal Sewa</th>
                            <th class="p-4">Tanggal Kembali</th>
                            <th class="p-4">Tarif Bulanan</th>
                            <th class="p-4">Tarif Harian</th>
                            <th class="p-4">Jenis Tarif</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse($sewas as $sewa)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="p-4 font-mono text-xs">{{ $sewa->id_sewa }}</td>
                            <td class="p-4 font-bold">{{ $sewa->no_kontainer }}</td>
                            <td class="p-4 font-semibold">{{ $sewa->customer->nama_customer ?? '-' }}</td>
                            <td class="p-4">{{ date('d/m/Y', strtotime($sewa->tanggal_sewa)) }}</td>
                            <td class="p-4">{{ $sewa->tanggal_kembali ? date('d/m/Y', strtotime($sewa->tanggal_kembali)) : '-' }}</td>
                            <td class="p-4">Rp {{ number_format($sewa->tarif_bulanan, 0, ',', '.') }}</td>
                            <td class="p-4">Rp {{ number_format($sewa->tarif_harian, 0, ',', '.') }}</td>
                            <td class="p-4">{{ $sewa->jenis_tarif }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold uppercase {{ $sewa->status_sewa === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $sewa->status_sewa }}
                                </span>
                            </td>
                            <td class="p-4 text-center flex justify-center gap-2">
                                @if($sewa->status_sewa === 'Aktif')
                                <button onclick="terminateSewa('{{ $sewa->id_sewa }}')" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-bold transition-all">
                                    <i class="fas fa-calendar-times"></i> Kembalikan
                                </button>
                                @endif
                                <button onclick="editSewa('{{ json_encode($sewa) }}')" class="px-2.5 py-1 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-lg text-xs font-bold transition-all">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-gray-400">Belum ada transaksi sewa kontainer.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 3: Master Database -->
    <div id="content-master" class="tab-content hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Customers -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h5 class="font-bold text-gray-800">Master Customer</h5>
                    <button onclick="openCustomerModal()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
                <div class="p-4 max-h-96 overflow-y-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs text-gray-500 font-bold border-b border-gray-100 pb-2">
                                <th class="pb-2">ID Customer</th>
                                <th class="pb-2">Nama Customer</th>
                                <th class="pb-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($customers as $c)
                            <tr>
                                <td class="py-2.5 font-mono text-xs">{{ $c->id_customer }}</td>
                                <td class="py-2.5 font-semibold">{{ $c->nama_customer }}</td>
                                <td class="py-2.5 text-right">
                                    <button onclick="deleteMaster('customer', '{{ $c->id_customer }}')" class="text-red-500 hover:text-red-700 transition-colors">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tipe Kontainer -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h5 class="font-bold text-gray-800">Tipe Kontainer</h5>
                    <button onclick="openTipeModal()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
                <div class="p-4 max-h-96 overflow-y-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs text-gray-500 font-bold border-b border-gray-100 pb-2">
                                <th class="pb-2">ID Tipe</th>
                                <th class="pb-2">Nama Tipe</th>
                                <th class="pb-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($tipes as $t)
                            <tr>
                                <td class="py-2.5 font-mono text-xs">{{ $t->id_tipe }}</td>
                                <td class="py-2.5 font-semibold">{{ $t->nama_tipe }}</td>
                                <td class="py-2.5 text-right">
                                    <button onclick="deleteMaster('tipe', '{{ $t->id_tipe }}')" class="text-red-500 hover:text-red-700 transition-colors">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ukuran Kontainer -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h5 class="font-bold text-gray-800">Ukuran Kontainer</h5>
                    <button onclick="openUkuranModal()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
                <div class="p-4 max-h-96 overflow-y-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs text-gray-500 font-bold border-b border-gray-100 pb-2">
                                <th class="pb-2">ID Ukuran</th>
                                <th class="pb-2">Deskripsi Ukuran</th>
                                <th class="pb-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($ukurans as $u)
                            <tr>
                                <td class="py-2.5 font-mono text-xs">{{ $u->id_ukuran }}</td>
                                <td class="py-2.5 font-semibold">{{ $u->deskripsi_ukuran }}</td>
                                <td class="py-2.5 text-right">
                                    <button onclick="deleteMaster('ukuran', '{{ $u->id_ukuran }}')" class="text-red-500 hover:text-red-700 transition-colors">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Master Kontainer -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h5 class="font-bold text-gray-800">Master Unit Kontainer</h5>
                    <button onclick="openKontainerModal()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
                <div class="p-4 max-h-96 overflow-y-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs text-gray-500 font-bold border-b border-gray-100 pb-2">
                                <th class="pb-2">No Kontainer</th>
                                <th class="pb-2">Customer</th>
                                <th class="pb-2">Tipe / Ukuran</th>
                                <th class="pb-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($kontainers as $k)
                            <tr>
                                <td class="py-2.5 font-bold">{{ $k->no_kontainer }}</td>
                                <td class="py-2.5">{{ $k->customer->nama_customer ?? '-' }}</td>
                                <td class="py-2.5 text-xs">{{ $k->tipe->nama_tipe ?? '-' }} / {{ $k->ukuran->deskripsi_ukuran ?? '-' }}</td>
                                <td class="py-2.5 text-right">
                                    <button onclick="deleteMaster('kontainer', '{{ $k->no_kontainer }}')" class="text-red-500 hover:text-red-700 transition-colors">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 4: Excel Bulk Import / Backup -->
    <div id="content-import" class="tab-content hidden">
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm p-6 max-w-2xl mx-auto">
            <h4 class="text-lg font-bold text-gray-800 mb-2">Restorasi Data JSON / Bulk Import</h4>
            <p class="text-sm text-gray-500 mb-6">
                Unggah file JSON backup dari sistem portable portal penyewaan kontainer offline untuk merekonsiliasi seluruh data ke database lokal.
            </p>

            <form action="{{ route('sewa-kontainer.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-emerald-500 transition-all cursor-pointer">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                    <p class="text-xs text-gray-500">Pilih berkas format cadangan `.json` (.json backup)</p>
                    <input type="file" name="backup_file" required class="mt-4 mx-auto block text-xs">
                </div>

                <div class="flex justify-end gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                        <i class="fas fa-sync-alt mr-1"></i> Pulihkan & Sinkronisasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 1: Payment Override Form -->
<div id="paymentOverrideModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200">
        <h4 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
            <i class="fas fa-edit text-emerald-600"></i> Override Tagihan Periodik
        </h4>
        <form id="overrideForm" onsubmit="submitOverride(event)">
            @csrf
            <input type="hidden" name="id_tagihan" id="ov-id-tagihan">
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Status Bayar</label>
                    <select name="status_bayar" id="ov-status-bayar" class="w-full border border-gray-300 p-2 rounded-lg text-sm bg-white">
                        <option value="Belum Ditagih">Belum Ditagih</option>
                        <option value="Pranota">Pranota</option>
                        <option value="Belum Bayar">Belum Bayar</option>
                        <option value="Lunas">Lunas</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Nomor Invoice Grup</label>
                    <input type="text" name="nomor_invoice_grup" id="ov-nomor-invoice" class="w-full border border-gray-300 p-2 rounded-lg text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Tagihan</label>
                    <input type="date" name="tanggal_tagihan" id="ov-tgl-tagihan" class="w-full border border-gray-300 p-2 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" id="ov-tgl-bayar" class="w-full border border-gray-300 p-2 rounded-lg text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Jumlah Tagihan Aktual (Override)</label>
                    <input type="number" name="jumlah_tagihan_override" id="ov-tagihan-override" class="w-full border border-gray-300 p-2 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Jumlah Bayar</label>
                    <input type="number" name="jumlah_bayar" id="ov-jumlah-bayar" class="w-full border border-gray-300 p-2 rounded-lg text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">PPN (Masukan/Keluaran)</label>
                    <input type="number" name="ppn" id="ov-ppn" class="w-full border border-gray-300 p-2 rounded-lg text-sm" placeholder="11%">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">PPh 23</label>
                    <input type="number" name="pph" id="ov-pph" class="w-full border border-gray-300 p-2 rounded-lg text-sm" placeholder="2%">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Nomor Bukti Bayar / EBK</label>
                <input type="text" name="nomor_bayar" id="ov-nomor-bayar" class="w-full border border-gray-300 p-2 rounded-lg text-sm">
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeOverrideModal()" class="px-4 py-2 border border-gray-200 text-gray-500 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Create Contract (Sewa) -->
<div id="sewaModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200">
        <h4 class="font-bold text-gray-800 text-lg mb-4">Tambah Kontrak Rental Kontainer</h4>
        <form id="sewaForm" onsubmit="submitSewa(event)">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Unit Kontainer</label>
                <select name="no_kontainer" required class="w-full border border-gray-300 p-2 rounded-lg text-sm bg-white">
                    @foreach($kontainers as $k)
                    <option value="{{ $k->no_kontainer }}">{{ $k->no_kontainer }} ({{ $k->tipe->nama_tipe ?? '' }} {{ $k->ukuran->deskripsi_ukuran ?? '' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Customer / Penyewa</label>
                <select name="id_customer" required class="w-full border border-gray-300 p-2 rounded-lg text-sm bg-white">
                    @foreach($customers as $c)
                    <option value="{{ $c->id_customer }}">{{ $c->nama_customer }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Mulai Sewa</label>
                    <input type="date" name="tanggal_sewa" required class="w-full border border-gray-300 p-2 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Kembali (Optional)</label>
                    <input type="date" name="tanggal_kembali" class="w-full border border-gray-300 p-2 rounded-lg text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Tarif Bulanan</label>
                    <input type="number" name="tarif_bulanan" required class="w-full border border-gray-300 p-2 rounded-lg text-sm" placeholder="Contoh: 3000000">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Tarif Harian</label>
                    <input type="number" name="tarif_harian" required class="w-full border border-gray-300 p-2 rounded-lg text-sm" placeholder="Contoh: 150000">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Jenis Tarif Utama</label>
                    <select name="jenis_tarif" class="w-full border border-gray-300 p-2 rounded-lg text-sm bg-white">
                        <option value="Bulanan">Bulanan</option>
                        <option value="Harian">Harian</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Status Sewa</label>
                    <select name="status_sewa" class="w-full border border-gray-300 p-2 rounded-lg text-sm bg-white">
                        <option value="Aktif">Aktif</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Catatan</label>
                <textarea name="catatan" class="w-full border border-gray-300 p-2 rounded-lg text-sm" rows="3"></textarea>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeSewaModal()" class="px-4 py-2 border border-gray-200 text-gray-500 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                    Simpan Kontrak
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Master Customer -->
<div id="customerModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-gray-200">
        <h4 class="font-bold text-gray-800 text-lg mb-4">Tambah Master Customer</h4>
        <form id="customerForm" onsubmit="submitCustomer(event)">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Nama Customer</label>
                <input type="text" name="nama_customer" required class="w-full border border-gray-300 p-2 rounded-lg text-sm">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('customerModal')" class="px-4 py-2 border border-gray-200 text-gray-500 rounded-xl text-xs font-bold hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 4: Master Tipe -->
<div id="tipeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-gray-200">
        <h4 class="font-bold text-gray-800 text-lg mb-4">Tambah Master Tipe</h4>
        <form id="tipeForm" onsubmit="submitTipe(event)">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Nama Tipe (e.g. Dry, Reefer)</label>
                <input type="text" name="nama_tipe" required class="w-full border border-gray-300 p-2 rounded-lg text-sm">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('tipeModal')" class="px-4 py-2 border border-gray-200 text-gray-500 rounded-xl text-xs font-bold hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 5: Master Ukuran -->
<div id="ukuranModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-gray-200">
        <h4 class="font-bold text-gray-800 text-lg mb-4">Tambah Master Ukuran</h4>
        <form id="ukuranForm" onsubmit="submitUkuran(event)">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Deskripsi Ukuran (e.g. 20', 40')</label>
                <input type="text" name="deskripsi_ukuran" required class="w-full border border-gray-300 p-2 rounded-lg text-sm">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('ukuranModal')" class="px-4 py-2 border border-gray-200 text-gray-500 rounded-xl text-xs font-bold hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 6: Master Unit Kontainer -->
<div id="kontainerModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-gray-200">
        <h4 class="font-bold text-gray-800 text-lg mb-4">Tambah Unit Kontainer</h4>
        <form id="kontainerForm" onsubmit="submitKontainer(event)">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Nomor Kontainer</label>
                <input type="text" name="no_kontainer" required class="w-full border border-gray-300 p-2 rounded-lg text-sm" placeholder="Contoh: AMFU3153692">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Owner / Customer</label>
                <select name="id_customer" required class="w-full border border-gray-300 p-2 rounded-lg text-sm bg-white">
                    @foreach($customers as $c)
                    <option value="{{ $c->id_customer }}">{{ $c->nama_customer }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Tipe</label>
                    <select name="id_tipe" required class="w-full border border-gray-300 p-2 rounded-lg text-sm bg-white">
                        @foreach($tipes as $t)
                        <option value="{{ $t->id_tipe }}">{{ $t->nama_tipe }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Ukuran</label>
                    <select name="id_ukuran" required class="w-full border border-gray-300 p-2 rounded-lg text-sm bg-white">
                        @foreach($ukurans as $u)
                        <option value="{{ $u->id_ukuran }}">{{ $u->deskripsi_ukuran }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('kontainerModal')" class="px-4 py-2 border border-gray-200 text-gray-500 rounded-xl text-xs font-bold hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 7: Create Invoice Group -->
<div id="invoiceModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-xl shadow-2xl border border-gray-200">
        <h4 class="font-bold text-gray-800 text-lg mb-4">Buat Invoice Group Baru</h4>
        <form id="invoiceForm" onsubmit="submitInvoice(event)">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Nomor Invoice</label>
                    <input type="text" name="nomor_invoice" required class="w-full border border-gray-300 p-2 rounded-lg text-sm" placeholder="INV/2026/0618/001">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Customer / Vendor</label>
                    <select name="id_customer" required class="w-full border border-gray-300 p-2 rounded-lg text-sm bg-white">
                        @foreach($customers as $c)
                        <option value="{{ $c->id_customer }}">{{ $c->nama_customer }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Invoice</label>
                    <input type="date" name="tanggal_invoice" required class="w-full border border-gray-300 p-2 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Status Pembayaran</label>
                    <select name="status_pembayaran" class="w-full border border-gray-300 p-2 rounded-lg text-sm bg-white">
                        <option value="Belum Bayar">Belum Bayar</option>
                        <option value="Lunas">Lunas</option>
                    </select>
                </div>
            </div>

            <!-- List of eligible Tagihans for this Invoice -->
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-2">Pilih Tagihan Periodik untuk digabung:</label>
                <div class="border border-gray-200 rounded-lg p-3 max-h-40 overflow-y-auto space-y-2">
                    @foreach($tagihans->whereNull('nomor_invoice_grup') as $tg)
                    <label class="flex items-center gap-2 text-xs">
                        <input type="checkbox" name="list_id_tagihan[]" value="{{ $tg->id_tagihan }}" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-mono">{{ $tg->id_tagihan }}</span> - 
                        <span>{{ $tg->transaksi->kontainer->no_kontainer ?? '' }} Bulan ke-{{ $tg->bulan_ke }} (Rp {{ number_format($tg->jumlah_tagihan, 0, ',', '.') }})</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Adjustment Biaya (Optional)</label>
                    <input type="number" name="adjustment_biaya" class="w-full border border-gray-300 p-2 rounded-lg text-sm" placeholder="Contoh: -150000">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Keterangan Adjustment</label>
                    <input type="text" name="adjustment_keterangan" class="w-full border border-gray-300 p-2 rounded-lg text-sm" placeholder="Potongan diskon / klaim">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Deskripsi Invoice</label>
                <textarea name="deskripsi" class="w-full border border-gray-300 p-2 rounded-lg text-sm" rows="2"></textarea>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('invoiceModal')" class="px-4 py-2 border border-gray-200 text-gray-500 rounded-xl text-xs font-bold hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">Buat Invoice</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let appMode = localStorage.getItem('sewa_kontainer_app_mode') || 'sewa_out';

    $(document).ready(function() {
        applyThemeMode(appMode);
    });

    function setAppMode(mode) {
        appMode = mode;
        localStorage.setItem('sewa_kontainer_app_mode', mode);
        applyThemeMode(mode);
    }

    function applyThemeMode(mode) {
        const viewport = $('#sewa-viewport');
        const badge = $('#badge-mode');
        const desc = $('#mode-desc');
        const icon = $('#header-icon');
        const btnSewaOut = $('#btn-sewa-out');
        const btnSewaIn = $('#btn-sewa-in');
        
        // Tab buttons color transitions
        const tabBtns = $('.tab-btn');

        if (mode === 'sewa_in') {
            badge.text('Pihak Penyewa (Sewa In)');
            badge.addClass('bg-indigo-100 text-indigo-800 border-indigo-200').removeClass('bg-emerald-100 text-emerald-800 border-emerald-200');
            desc.text('Sistem kontrol biaya pengeluaran, PPN Masukan, PPh 23, serta rekonsiliasi tagihan dari Vendor');
            icon.addClass('text-indigo-600').removeClass('text-emerald-600');
            
            btnSewaIn.addClass('bg-white text-indigo-700 shadow-sm').removeClass('text-gray-600 hover:bg-gray-100');
            btnSewaOut.addClass('text-gray-600 hover:bg-gray-100').removeClass('bg-white text-emerald-700 shadow-sm');
            
            // Adjust theme for tab borders & active states
            $('.tab-btn').each(function() {
                if ($(this).hasClass('text-emerald-600')) {
                    $(this).addClass('text-indigo-600 border-indigo-600').removeClass('text-emerald-600 border-emerald-600');
                }
            });
            $('#kpi-lunas-icon').addClass('bg-indigo-50 text-indigo-600').removeClass('bg-emerald-50 text-emerald-600');
            $('#kpi-lunas-val').addClass('text-indigo-600').removeClass('text-emerald-600');
        } else {
            badge.text('Pihak Pemilik (Sewa Out)');
            badge.addClass('bg-emerald-100 text-emerald-800 border-emerald-200').removeClass('bg-indigo-100 text-indigo-800 border-indigo-200');
            desc.text('Sistem kalkulasi proris maret ke januari (30 hari) & tahun kabisat februari (28/29 hari)');
            icon.addClass('text-emerald-600').removeClass('text-indigo-600');
            
            btnSewaOut.addClass('bg-white text-emerald-700 shadow-sm').removeClass('text-gray-600 hover:bg-gray-100');
            btnSewaIn.addClass('text-gray-600 hover:bg-gray-100').removeClass('bg-white text-indigo-700 shadow-sm');
            
            $('.tab-btn').each(function() {
                if ($(this).hasClass('text-indigo-600')) {
                    $(this).addClass('text-emerald-600 border-emerald-600').removeClass('text-indigo-600 border-indigo-600');
                }
            });
            $('#kpi-lunas-icon').addClass('bg-emerald-50 text-emerald-600').removeClass('bg-indigo-50 text-indigo-600');
            $('#kpi-lunas-val').addClass('text-emerald-600').removeClass('text-indigo-600');
        }
    }

    function switchTab(tab) {
        $('.tab-content').addClass('hidden');
        $(`#content-${tab}`).removeClass('hidden');

        $('.tab-btn').removeClass('border-emerald-600 text-emerald-600 border-indigo-600 text-indigo-600').addClass('border-transparent text-gray-500 hover:text-gray-700');
        
        const activeColorClass = appMode === 'sewa_in' ? 'border-indigo-600 text-indigo-600' : 'border-emerald-600 text-emerald-600';
        $(`#tab-${tab}`).addClass(activeColorClass).removeClass('border-transparent text-gray-500');
    }

    // Modal Helpers
    function openModal(id) {
        $(`#${id}`).removeClass('hidden');
    }
    function closeModal(id) {
        $(`#${id}`).addClass('hidden');
    }

    // Customer
    function openCustomerModal() { openModal('customerModal'); }
    function submitCustomer(e) {
        e.preventDefault();
        $.post('{{ route("sewa-kontainer.customer.store") }}', $('#customerForm').serialize(), function() {
            location.reload();
        });
    }

    // Tipe
    function openTipeModal() { openModal('tipeModal'); }
    function submitTipe(e) {
        e.preventDefault();
        $.post('{{ route("sewa-kontainer.tipe.store") }}', $('#tipeForm').serialize(), function() {
            location.reload();
        });
    }

    // Ukuran
    function openUkuranModal() { openModal('ukuranModal'); }
    function submitUkuran(e) {
        e.preventDefault();
        $.post('{{ route("sewa-kontainer.ukuran.store") }}', $('#ukuranForm').serialize(), function() {
            location.reload();
        });
    }

    // Kontainer
    function openKontainerModal() { openModal('kontainerModal'); }
    function submitKontainer(e) {
        e.preventDefault();
        $.post('{{ route("sewa-kontainer.kontainer.store") }}', $('#kontainerForm').serialize(), function() {
            location.reload();
        });
    }

    // Master Delete
    function deleteMaster(type, id) {
        if (confirm(`Apakah Anda yakin ingin menghapus data master ${type} ini?`)) {
            $.ajax({
                url: `/sewa-kontainer/master/${type}/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    location.reload();
                }
            });
        }
    }

    // Sewa Contract Modal
    function openSewaModal() { openModal('sewaModal'); }
    function closeSewaModal() { closeModal('sewaModal'); }
    function submitSewa(e) {
        e.preventDefault();
        $.post('{{ route("sewa-kontainer.sewa.store") }}', $('#sewaForm').serialize(), function() {
            location.reload();
        });
    }

    // Terminate Contract
    function terminateSewa(id) {
        const tgl = prompt('Masukkan tanggal pengembalian kontainer (YYYY-MM-DD):', '{{ date("Y-m-d") }}');
        if (tgl) {
            $.post(`/sewa-kontainer/sewa/${id}/terminate`, {
                _token: '{{ csrf_token() }}',
                tanggal_kembali: tgl
            }, function() {
                location.reload();
            });
        }
    }

    // Payment Override Modal
    function editPaymentOverride(tagihanJson) {
        const tagihan = JSON.parse(tagihanJson);
        $('#ov-id-tagihan').val(tagihan.id_tagihan);
        $('#ov-status-bayar').val(tagihan.status_bayar);
        $('#ov-nomor-invoice').val(tagihan.nomor_invoice_grup);
        $('#ov-tgl-tagihan').val(tagihan.tanggal_tagihan);
        $('#ov-tgl-bayar').val(tagihan.tanggal_bayar);
        $('#ov-tagihan-override').val(tagihan.jumlah_tagihan_override);
        $('#ov-jumlah-bayar').val(tagihan.jumlah_bayar);
        $('#ov-ppn').val(tagihan.ppn);
        $('#ov-pph').val(tagihan.pph);
        $('#ov-nomor-bayar').val(tagihan.nomor_bayar);
        openModal('paymentOverrideModal');
    }
    function closeOverrideModal() { closeModal('paymentOverrideModal'); }
    
    function submitOverride(e) {
        e.preventDefault();
        const id = $('#ov-id-tagihan').val();
        $.post(`/sewa-kontainer/tagihan/${id}/override`, $('#overrideForm').serialize(), function() {
            location.reload();
        });
    }

    // Invoice
    function openInvoiceModal() { openModal('invoiceModal'); }
    function submitInvoice(e) {
        e.preventDefault();
        $.post('{{ route("sewa-kontainer.invoice.store") }}', $('#invoiceForm').serialize(), function() {
            location.reload();
        });
    }
</script>
@endpush
