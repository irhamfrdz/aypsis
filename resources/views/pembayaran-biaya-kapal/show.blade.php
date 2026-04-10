@extends('layouts.app')

@section('title', 'Detail Pembayaran Biaya Kapal')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pembayaran #{{ $pembayaran->nomor_pembayaran }}</h1>
            <p class="text-sm text-gray-500">Dibuat oleh {{ $pembayaran->creator->name ?? 'System' }} pada {{ $pembayaran->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('pembayaran-biaya-kapal.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Kembali
            </a>
            @can('pembayaran-biaya-kapal-delete')
            <form action="{{ route('pembayaran-biaya-kapal.destroy', $pembayaran->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus pembayaran ini? Status invoice akan kembali menjadi belum lunas.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Batalkan & Hapus
                </button>
            </form>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Informasi Transaksi</h2>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ strtoupper($pembayaran->status_pembayaran) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Tanggal Pembayaran</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-medium">
                                {{ $pembayaran->tanggal_pembayaran ? \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d F Y') : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Kas / Bank</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $pembayaran->bank }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Metode</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $pembayaran->jenis_transaksi }}</dd>
                        </div>
                        @if($pembayaran->nomor_accurate)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Nomor Accurate</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $pembayaran->nomor_accurate }}</dd>
                        </div>
                        @endif

                        @if($pembayaran->total_tagihan_penyesuaian != 0)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Penyesuaian (Adjustment)</dt>
                            <dd class="mt-1 text-sm text-amber-600 font-semibold italic">
                                Rp {{ number_format($pembayaran->total_tagihan_penyesuaian, 0, ',', '.') }}
                            </dd>
                            @if($pembayaran->alasan_penyesuaian)
                            <dd class="mt-1 text-xs text-gray-500">
                                <strong>Alasan:</strong> {{ $pembayaran->alasan_penyesuaian }}
                            </dd>
                            @endif
                        </div>
                        @endif
                        <div class="pt-4 border-t border-gray-100">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Total Akhir (Invoiced + Penyesuaian)</dt>
                            <dd class="mt-1 text-xl font-bold text-indigo-600">
                                Rp {{ number_format($pembayaran->total_pembayaran + ($pembayaran->total_tagihan_penyesuaian ?? 0), 0, ',', '.') }}
                            </dd>
                        </div>
                        @if($pembayaran->keterangan)
                        <div class="pt-4 border-t border-gray-100">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Keterangan</dt>
                            <dd class="mt-1 text-sm text-gray-600 italic">{{ $pembayaran->keterangan }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Invoices List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Invoice Terbayar</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapal / Klasifikasi</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pembayaran->biayaKapals as $biaya)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $biaya->nomor_invoice }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="font-medium text-gray-900">{{ $biaya->display_nama_kapal }}</div>
                                    <div class="text-xs">{{ $biaya->klasifikasiBiaya->nama ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900 font-mono">
                                    Rp {{ number_format($biaya->pivot->nominal ?? $biaya->total_biaya ?? $biaya->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('biaya-kapal.show', $biaya->id) }}" class="text-teal-600 hover:text-teal-900 bg-teal-50 px-2 py-1 rounded">Detail Invoice</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase">Subtotal</td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                                    Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
