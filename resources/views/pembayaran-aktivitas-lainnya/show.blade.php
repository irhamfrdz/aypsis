@extends('layouts.app')

@section('title', 'Detail Pembayaran Aktivitas Lainnya')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-lg rounded-lg">
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-lg">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-eye mr-3 text-blue-600"></i>
                Detail Pembayaran Aktivitas Lainnya
            </h3>
            <div class="flex space-x-2">
                @can('pembayaran-aktivitas-lainnya-update')
                    @if($pembayaranAktivitasLainnya->status === 'draft')
                        <a href="{{ route('pembayaran-aktivitas-lainnya.edit', $pembayaranAktivitasLainnya) }}" class="inline-flex items-center px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a>
                    @endif
                @endcan
                <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Status Badge -->
            <div class="mb-6">
                @php
                    $statusColors = [
                        'draft' => 'gray',
                        'pending' => 'yellow',
                        'approved' => 'blue',
                        'rejected' => 'red',
                        'paid' => 'green'
                    ];
                    $color = $statusColors[$pembayaranAktivitasLainnya->status] ?? 'gray';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                    <i class="fas fa-circle mr-2"></i>
                    {{ ucfirst($pembayaranAktivitasLainnya->status) }}
                </span>
            </div>

            <!-- Informasi Utama -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembayaran</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nomor Pembayaran</dt>
                            <dd class="text-sm text-gray-900 font-mono">{{ $pembayaranAktivitasLainnya->nomor_pembayaran }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nomor Accurate</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaranAktivitasLainnya->nomor_accurate ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Pembayaran</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaranAktivitasLainnya->tanggal_pembayaran->format('d F Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Pembayaran</dt>
                            <dd class="text-lg font-bold text-green-600">Rp {{ number_format($pembayaranAktivitasLainnya->total_pembayaran, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jenis Transaksi</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaranAktivitasLainnya->jenis_transaksi_label }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Akun</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Bank/Kas</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaranAktivitasLainnya->akunBank->nama_akun ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Akun Biaya</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaranAktivitasLainnya->akunBiaya->nama_akun ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nomor Voyage</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaranAktivitasLainnya->nomor_voyage ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Kapal</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaranAktivitasLainnya->nama_kapal ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Plat Nomor</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaranAktivitasLainnya->plat_nomor ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Aktivitas Pembayaran -->
            <div class="mb-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Aktivitas Pembayaran</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-900 whitespace-pre-line">{{ $pembayaranAktivitasLainnya->aktivitas_pembayaran }}</p>
                </div>
            </div>

            <!-- Informasi Audit -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Dibuat Oleh</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaranAktivitasLainnya->creator->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                            <dd class="text-sm text-gray-900">{{ $pembayaranAktivitasLainnya->created_at->format('d F Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                @if($pembayaranAktivitasLainnya->approved_by)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Disetujui Oleh</h4>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama</dt>
                                <dd class="text-sm text-gray-900">{{ $pembayaranAktivitasLainnya->approver->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                                <dd class="text-sm text-gray-900">{{ $pembayaranAktivitasLainnya->approved_at ? $pembayaranAktivitasLainnya->approved_at->format('d F Y H:i') : '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            @can('pembayaran-aktivitas-lainnya-approve')
                <div class="mt-8 flex justify-end space-x-4">
                    @if($pembayaranAktivitasLainnya->status === 'pending')
                        <form action="{{ route('pembayaran-aktivitas-lainnya.approve', $pembayaranAktivitasLainnya) }}" method="POST" class="inline">
                            @csrf
                            @method('POST')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out" onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini?')">
                                <i class="fas fa-check mr-2"></i> Setujui Pembayaran
                            </button>
                        </form>
                    @elseif($pembayaranAktivitasLainnya->status === 'approved')
                        <form action="{{ route('pembayaran-aktivitas-lainnya.mark-as-paid', $pembayaranAktivitasLainnya) }}" method="POST" class="inline">
                            @csrf
                            @method('POST')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium text-sm rounded-md transition duration-150 ease-in-out" onclick="return confirm('Apakah Anda yakin ingin menandai pembayaran ini sebagai sudah dibayar?')">
                                <i class="fas fa-money-bill-wave mr-2"></i> Tandai Sudah Bayar
                            </button>
                        </form>
                    @endif
                </div>
            @endcan
        </div>
    </div>
</div>
@endsection