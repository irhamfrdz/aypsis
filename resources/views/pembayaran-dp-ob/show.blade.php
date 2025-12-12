@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-medium text-gray-900">
                        {{ $title }}
                    </h1>
                    <p class="mt-2 text-gray-600">Detail informasi pembayaran Down Payment (DP) Out Bound (OB)</p>
                </div>

                <div class="flex space-x-3">
                    @can('pembayaran-ob-print')
                    <a href="{{ route('pembayaran-ob.print', $pembayaran->id) }}"
                       target="_blank"
                       class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-print mr-1"></i> Print
                    </a>
                    @endcan

                    @can('pembayaran-ob-edit')
                    <a href="{{ route('pembayaran-ob.edit', $pembayaran->id) }}"
                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    @endcan

                    <a href="{{ route('pembayaran-ob.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Detail Information -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informasi Pembayaran -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Informasi Pembayaran</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Nomor Pembayaran</label>
                                <p class="mt-1 text-base font-semibold text-blue-600">{{ $pembayaran->nomor_pembayaran }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Tanggal Pembayaran</label>
                                <p class="mt-1 text-base text-gray-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Kegiatan</label>
                                <p class="mt-1 text-base text-gray-900">{{ $pembayaran->kegiatan ?? '-' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Nomor Voyage</label>
                                <p class="mt-1 text-base text-gray-900">{{ $pembayaran->nomor_voyage ?? '-' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Akun Kas/Bank</label>
                                <p class="mt-1 text-base text-gray-900">
                                    @if($pembayaran->kasBankAkun)
                                        {{ $pembayaran->kasBankAkun->nomor_akun }} - {{ $pembayaran->kasBankAkun->nama_akun }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Jenis Transaksi</label>
                                <p class="mt-1">
                                    @if($pembayaran->jenis_transaksi == 'debit')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-plus-circle mr-1"></i> Debit
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-minus-circle mr-1"></i> Kredit
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Informasi Keuangan -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Informasi Keuangan</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Total DP OB</label>
                                <p class="mt-1 text-2xl font-bold text-green-600">Rp {{ number_format($pembayaran->dp_amount ?? $pembayaran->total_pembayaran, 0, ',', '.') }}</p>
                            </div>

                            @php
                                $jumlahPerSupirArray = is_array($pembayaran->jumlah_per_supir) ? $pembayaran->jumlah_per_supir : [];
                                $totalRealisasi = array_sum($jumlahPerSupirArray);
                                $dpAmount = $pembayaran->dp_amount ?? $pembayaran->total_pembayaran;
                                $sisaDP = $dpAmount - $totalRealisasi;
                            @endphp

                            @if($totalRealisasi > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Total Realisasi</label>
                                <p class="mt-1 text-xl font-semibold text-blue-600">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Sisa DP</label>
                                <p class="mt-1 text-xl font-semibold {{ $sisaDP > 0 ? 'text-orange-600' : 'text-gray-400' }}">
                                    Rp {{ number_format($sisaDP, 0, ',', '.') }}
                                </p>
                            </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <p class="mt-1">
                                    @php
                                        if ($totalRealisasi >= $dpAmount) {
                                            $status = 'selesai';
                                            $badgeColor = 'bg-green-100 text-green-800';
                                            $statusText = 'Selesai';
                                        } elseif ($totalRealisasi > 0) {
                                            $status = 'sebagian';
                                            $badgeColor = 'bg-yellow-100 text-yellow-800';
                                            $statusText = 'Sebagian Direalisasi';
                                        } else {
                                            $status = 'belum';
                                            $badgeColor = 'bg-red-100 text-red-800';
                                            $statusText = 'Belum Direalisasi';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $badgeColor }}">
                                        {{ $statusText }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Supir -->
                    @if(!empty($pembayaran->supir_ids))
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Supir & Jumlah DP</h3>
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Supir</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah DP</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Realisasi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $supirListData = \App\Models\Karyawan::whereIn('id', $pembayaran->supir_ids)->get();
                                    @endphp
                                    @foreach($supirListData as $index => $supir)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supir->nik }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $supir->nama_lengkap }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                                Rp {{ number_format($jumlahPerSupirArray[$supir->id] ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @php
                                                    $realisasi = $jumlahPerSupirArray[$supir->id] ?? 0;
                                                @endphp
                                                @if($realisasi > 0)
                                                    <span class="text-blue-600">Rp {{ number_format($realisasi, 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total:</td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-green-700">
                                            Rp {{ number_format(array_sum($jumlahPerSupirArray), 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-blue-700">
                                            Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Keterangan -->
                    @if($pembayaran->keterangan)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Keterangan</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-base text-gray-900 whitespace-pre-line">{{ $pembayaran->keterangan }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Informasi Audit -->
                    <div class="mt-6 pt-6 border-t border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Dibuat Oleh</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $pembayaran->creator->name ?? '-' }}
                                <span class="text-gray-500">{{ $pembayaran->created_at ? '(' . $pembayaran->created_at->format('d/m/Y H:i') . ')' : '' }}</span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Terakhir Diupdate</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $pembayaran->updater->name ?? '-' }}
                                <span class="text-gray-500">{{ $pembayaran->updated_at ? '(' . $pembayaran->updated_at->format('d/m/Y H:i') . ')' : '' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
