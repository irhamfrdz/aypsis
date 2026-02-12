@extends('layouts.app')

@section('title', 'Detail Pranota Lembur')
@section('page_title', 'Detail Pranota Lembur')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <i class="fas fa-bed mr-3 text-blue-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Detail Pranota Lembur/Nginap</h1>
                    <p class="text-gray-600">{{ $pranotaLembur->nomor_pranota }}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('pranota-lembur.list') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                @can('pranota-lembur-print')
                <a href="{{ route('pranota-lembur.print', $pranotaLembur->id) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200 inline-flex items-center">
                    <i class="fas fa-print mr-2"></i>
                    Cetak
                </a>
                @endcan
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Header Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Pranota</label>
                <p class="text-lg font-semibold text-gray-800">{{ $pranotaLembur->nomor_pranota }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Pranota</label>
                <p class="text-lg font-semibold text-gray-800">{{ $pranotaLembur->tanggal_pranota->format('d/m/Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $pranotaLembur->status_badge }}">
                    {{ $pranotaLembur->status_label }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Dibuat Oleh</label>
                <p class="text-gray-800">{{ $pranotaLembur->creator->name ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Dibuat</label>
                <p class="text-gray-800">{{ $pranotaLembur->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @if($pranotaLembur->approved_by)
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Disetujui Oleh</label>
                <p class="text-gray-800">{{ $pranotaLembur->approver->name ?? '-' }}<br>
                <small class="text-gray-600">{{ $pranotaLembur->approved_at ? $pranotaLembur->approved_at->format('d/m/Y H:i') : '' }}</small></p>
            </div>
            @endif
        </div>

        <!-- Table Surat Jalan -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Surat Jalan</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal TT</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No SJ</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supir</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Biaya Lembur</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Biaya Nginap</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @foreach($pranotaLembur->suratJalans as $sj)
                        <tr>
                            <td class="px-4 py-3">{{ $no++ }}</td>
                            <td class="px-4 py-3">{{ $sj->tandaTerima ? $sj->tandaTerima->tanggal->format('d/M/Y') : '-' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Muat</span></td>
                            <td class="px-4 py-3">{{ $sj->no_surat_jalan }}</td>
                            <td class="px-4 py-3">{{ $sj->pivot->supir }}</td>
                            <td class="px-4 py-3">{{ $sj->pivot->no_plat }}</td>
                            <td class="px-4 py-3">
                                @if($sj->pivot->is_lembur) <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold mr-1">Lembur</span> @endif
                                @if($sj->pivot->is_nginap) <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">Nginap</span> @endif
                            </td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($sj->pivot->biaya_lembur, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($sj->pivot->biaya_nginap, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($sj->pivot->total_biaya, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        @foreach($pranotaLembur->suratJalanBongkarans as $sj)
                        <tr>
                            <td class="px-4 py-3">{{ $no++ }}</td>
                            <td class="px-4 py-3">{{ $sj->tandaTerima ? $sj->tandaTerima->tanggal_tanda_terima->format('d/M/Y') : '-' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Bongkaran</span></td>
                            <td class="px-4 py-3">{{ $sj->nomor_surat_jalan }}</td>
                            <td class="px-4 py-3">{{ $sj->pivot->supir }}</td>
                            <td class="px-4 py-3">{{ $sj->pivot->no_plat }}</td>
                            <td class="px-4 py-3">
                                @if($sj->pivot->is_lembur) <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold mr-1">Lembur</span> @endif
                                @if($sj->pivot->is_nginap) <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">Nginap</span> @endif
                            </td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($sj->pivot->biaya_lembur, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($sj->pivot->biaya_nginap, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($sj->pivot->total_biaya, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="9" class="px-4 py-3 text-right font-semibold">Total Biaya:</td>
                            <td class="px-4 py-3 text-right font-bold text-lg">{{ $pranotaLembur->formatted_total_biaya }}</td>
                        </tr>
                        @if($pranotaLembur->adjustment != 0)
                        <tr>
                            <td colspan="9" class="px-4 py-3 text-right font-semibold">
                                Adjustment
                                @if($pranotaLembur->alasan_adjustment)
                                    <br><small class="text-gray-600 font-normal">({{ $pranotaLembur->alasan_adjustment }})</small>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-lg {{ $pranotaLembur->adjustment > 0 ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($pranotaLembur->adjustment, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="9" class="px-4 py-3 text-right font-semibold">Grand Total:</td>
                            <td class="px-4 py-3 text-right font-bold text-xl text-blue-600">{{ $pranotaLembur->formatted_total_setelah_adjustment }}</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Catatan -->
        @if($pranotaLembur->catatan)
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan:</label>
            <p class="text-gray-800">{{ $pranotaLembur->catatan }}</p>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
            @if($pranotaLembur->status === 'draft')
                @can('pranota-lembur-update')
                <form method="POST" action="{{ route('pranota-lembur.submit', $pranotaLembur->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200" onclick="return confirm('Yakin ingin submit pranota ini?')">
                        <i class="fas fa-paper-plane mr-2"></i> Submit
                    </button>
                </form>
                @endcan
            @endif

            @if($pranotaLembur->status === 'submitted')
                @can('pranota-lembur-approve')
                <form method="POST" action="{{ route('pranota-lembur.approve', $pranotaLembur->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition duration-200" onclick="return confirm('Yakin ingin approve pranota ini?')">
                        <i class="fas fa-check mr-2"></i> Approve
                    </button>
                </form>
                @endcan
            @endif

            @if(!in_array($pranotaLembur->status, ['paid']))
                @can('pranota-lembur-delete')
                <form method="POST" action="{{ route('pranota-lembur.cancel', $pranotaLembur->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition duration-200" onclick="return confirm('Yakin ingin membatalkan pranota ini?')">
                        <i class="fas fa-times mr-2"></i> Batalkan
                    </button>
                </form>
                @endcan
            @endif
        </div>
    </div>
</div>
@endsection
