@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-800">Detail Pembayaran Aktivitas Lain</h1>
            <div class="flex gap-2">
                @can('pembayaran-aktivitas-lain-update')
                    @if($pembayaranAktivitasLain->status == 'pending')
                        <a href="{{ route('pembayaran-aktivitas-lain.edit', $pembayaranAktivitasLain) }}" class="inline-flex items-center px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                    @endif
                @endcan
                <a href="{{ route('pembayaran-aktivitas-lain.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Status Badge -->
            <div class="mb-6">
                @if($pembayaranAktivitasLain->status == 'pending')
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Pending
                    </span>
                @elseif($pembayaranAktivitasLain->status == 'approved')
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Approved
                    </span>
                @else
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Paid
                    </span>
                @endif
            </div>

            <!-- Detail Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nomor</label>
                        <p class="mt-1 text-base font-semibold text-gray-900">{{ $pembayaranAktivitasLain->nomor }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tanggal</label>
                        <p class="mt-1 text-base text-gray-900">{{ $pembayaranAktivitasLain->tanggal->format('d F Y') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Jenis Aktivitas</label>
                        <p class="mt-1 text-base text-gray-900">{{ $pembayaranAktivitasLain->jenis_aktivitas }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Jumlah</label>
                        <p class="mt-1 text-lg font-bold text-blue-600">Rp {{ number_format($pembayaranAktivitasLain->jumlah, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Metode Pembayaran</label>
                        <p class="mt-1 text-base text-gray-900">{{ ucfirst($pembayaranAktivitasLain->metode_pembayaran) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Debit/Kredit</label>
                        <p class="mt-1 text-base text-gray-900">{{ ucfirst($pembayaranAktivitasLain->debit_kredit ?? '-') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Akun Biaya</label>
                        @php
                            $akunCoa = $pembayaranAktivitasLain->akun_coa_id ? DB::table('akun_coa')->find($pembayaranAktivitasLain->akun_coa_id) : null;
                        @endphp
                        <p class="mt-1 text-base text-gray-900">
                            @if($akunCoa)
                                {{ $akunCoa->kode_nomor }} - {{ $akunCoa->nama_akun }}
                            @else
                                -
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Dibuat Oleh</label>
                        <p class="mt-1 text-base text-gray-900">{{ $pembayaranAktivitasLain->creator->name ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $pembayaranAktivitasLain->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($pembayaranAktivitasLain->approved_by)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Disetujui Oleh</label>
                        <p class="mt-1 text-base text-gray-900">{{ $pembayaranAktivitasLain->approver->name ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $pembayaranAktivitasLain->approved_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>

                @if($pembayaranAktivitasLain->keterangan)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Keterangan</label>
                    <p class="mt-1 text-base text-gray-900 whitespace-pre-line">{{ $pembayaranAktivitasLain->keterangan }}</p>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-200 flex gap-3">
                @can('pembayaran-aktivitas-lain-approve')
                    @if($pembayaranAktivitasLain->status == 'pending')
                        <form action="{{ route('pembayaran-aktivitas-lain.approve', $pembayaranAktivitasLain) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini?')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium text-sm rounded-md transition">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Approve
                            </button>
                        </form>
                    @elseif($pembayaranAktivitasLain->status == 'approved')
                        <form action="{{ route('pembayaran-aktivitas-lain.mark-as-paid', $pembayaranAktivitasLain) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" onclick="return confirm('Tandai sebagai Paid?')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-md transition">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Tandai Paid
                            </button>
                        </form>
                    @endif
                @endcan

                @can('pembayaran-aktivitas-lain-delete')
                    @if($pembayaranAktivitasLain->status != 'paid')
                        <form action="{{ route('pembayaran-aktivitas-lain.destroy', $pembayaranAktivitasLain) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium text-sm rounded-md transition">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
