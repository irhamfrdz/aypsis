@extends('layouts.app')

@section('title', 'Detail Pranota Uang Kenek')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Pranota Uang Kenek</h1>
                <p class="text-gray-600 mt-1">{{ $pranotaUangKenek->no_pranota }}</p>
            </div>
            <div class="flex space-x-3">
                @can('pranota-uang-kenek-view')
                <button type="button" 
                        class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200" 
                        onclick="printRitasiKenek({{ $pranotaUangKenek->id }})">
                    <i class="fas fa-print mr-2"></i> Print Ritasi Kenek
                </button>
                @endcan
                <a href="{{ route('pranota-uang-kenek.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Status and Basic Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        @if($pranotaUangKenek->status === 'draft')
                            <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                        @elseif($pranotaUangKenek->status === 'submitted')
                            <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Submitted</span>
                        @elseif($pranotaUangKenek->status === 'approved')
                            <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                        @elseif($pranotaUangKenek->status === 'paid')
                            <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">Paid</span>
                        @elseif($pranotaUangKenek->status === 'cancelled')
                            <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal</label>
                        <div class="text-sm text-gray-900">{{ $pranotaUangKenek->tanggal ? \Carbon\Carbon::parse($pranotaUangKenek->tanggal)->format('d/m/Y') : '-' }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Jml SJ</label>
                        <div class="text-sm text-gray-900">{{ count($kenekDetails) }} surat jalan</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Total Uang Kenek</label>
                        <div class="text-lg font-bold text-green-600">Rp {{ number_format($pranotaUangKenek->total_uang, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Detail Kenek</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kenek</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Uang Rit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if(count($kenekDetails) > 0)
                                @foreach($kenekDetails as $index => $detail)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $detail['no_surat_jalan'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $detail['kenek_nama'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($detail['uang_rit'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">1</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pranotaUangKenek->no_surat_jalan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pranotaUangKenek->kenek_nama }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($pranotaUangKenek->uang_rit_kenek, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-sm font-bold text-gray-900">Total Keseluruhan</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">Rp {{ number_format($pranotaUangKenek->total_uang, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
                                @if($pranotaUangKenek->keterangan)
                                <tr>
                                    <td><strong>Keterangan</strong></td>
                                    <td>: {{ $pranotaUangKenek->keterangan }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        <!-- Informasi Surat Jalan -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Informasi Surat Jalan</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="140"><strong>No Surat Jalan</strong></td>
                                    <td>: {{ $pranotaUangKenek->no_surat_jalan }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Supir</strong></td>
                                    <td>: {{ $pranotaUangKenek->supir_nama ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kenek</strong></td>
                                    <td>: {{ $pranotaUangKenek->kenek_nama }}</td>
                                </tr>
                                <tr>
                                    <td><strong>No Plat</strong></td>
                                    <td>: {{ $pranotaUangKenek->no_plat }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Uang Rit Kenek</strong></td>
                                    <td>: Rp {{ number_format($pranotaUangKenek->uang_rit_kenek, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Informasi Audit -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="mb-3">Informasi Audit</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Dibuat Oleh</strong></td>
                                            <td>: {{ $pranotaUangKenek->createdBy ? $pranotaUangKenek->createdBy->name : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Dibuat</strong></td>
                                            <td>: {{ $pranotaUangKenek->created_at ? $pranotaUangKenek->created_at->format('d/m/Y H:i') : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                @if($pranotaUangKenek->updated_by)
                                <div class="col-md-4">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Diupdate Oleh</strong></td>
                                            <td>: {{ $pranotaUangKenek->updatedBy ? $pranotaUangKenek->updatedBy->name : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Update</strong></td>
                                            <td>: {{ $pranotaUangKenek->updated_at ? $pranotaUangKenek->updated_at->format('d/m/Y H:i') : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                @endif

                                @if($pranotaUangKenek->approved_by)
                                <div class="col-md-4">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Diapprove Oleh</strong></td>
                                            <td>: {{ $pranotaUangKenek->approvedBy ? $pranotaUangKenek->approvedBy->name : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Approve</strong></td>
                                            <td>: {{ $pranotaUangKenek->approved_at ? \Carbon\Carbon::parse($pranotaUangKenek->approved_at)->format('d/m/Y H:i') : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                @endif

                                @if($pranotaUangKenek->tanggal_bayar)
                                <div class="col-md-4">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Tanggal Bayar</strong></td>
                                            <td>: {{ \Carbon\Carbon::parse($pranotaUangKenek->tanggal_bayar)->format('d/m/Y') }}</td>
        </div>

        <!-- Information Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Audit Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Audit</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat Oleh</label>
                            <div class="text-sm text-gray-900">{{ $pranotaUangKenek->createdBy->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $pranotaUangKenek->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @if($pranotaUangKenek->updated_at && $pranotaUangKenek->updated_at != $pranotaUangKenek->created_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diupdate</label>
                            <div class="text-sm text-gray-900">{{ $pranotaUangKenek->updatedBy->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $pranotaUangKenek->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @endif
                        @if($pranotaUangKenek->approved_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Disetujui Oleh</label>
                            <div class="text-sm text-gray-900">{{ $pranotaUangKenek->approvedBy->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($pranotaUangKenek->approved_at)->format('d/m/Y H:i') }}</div>
                        </div>
                        @endif
                        @if($pranotaUangKenek->tanggal_bayar)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Bayar</label>
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($pranotaUangKenek->tanggal_bayar)->format('d/m/Y') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            @if($pranotaUangKenek->keterangan)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Keterangan</h3>
                </div>
                <div class="p-6">
                    <div class="text-sm text-gray-900">{{ $pranotaUangKenek->keterangan }}</div>
                </div>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        @if($pranotaUangKenek->status === 'draft')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4">
                <div class="flex flex-wrap gap-3">
                    @can('pranota-uang-kenek-edit')
                    <a href="{{ route('pranota-uang-kenek.edit', $pranotaUangKenek) }}" 
                       class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    @endcan

                    @can('pranota-uang-kenek-delete')
                    <button type="button" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200" 
                            onclick="deletePranota({{ $pranotaUangKenek->id }}, '{{ $pranotaUangKenek->no_pranota }}')">
                        <i class="fas fa-trash mr-2"></i> Hapus
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function printRitasiKenek(id) {
    const url = `/pranota-uang-kenek/${id}/print`;
    const printWindow = window.open(url, '_blank', 'width=800,height=600');
    
    printWindow.onload = function() {
        printWindow.print();
        printWindow.onafterprint = function() {
            printWindow.close();
        };
    };
}

function deletePranota(id, noPranota) {
    if (confirm(`Yakin ingin menghapus pranota ${noPranota}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/pranota-uang-kenek/${id}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                @endif

                                @if($pranotaUangKenek->isApproved())
                                    <button type="button" class="btn btn-success" 
                                            data-toggle="modal" 
                                            data-target="#markAsPaidModal">
                                        <i class="fas fa-money-bill"></i> Tandai Sebagai Dibayar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markAsPaidModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('pranota-uang-kenek.mark-as-paid', $pranotaUangKenek) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Tandai Sebagai Dibayar</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tanggal_bayar">Tanggal Bayar</label>
                        <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <p>Yakin tandai pranota <strong>{{ $pranotaUangKenek->no_pranota }}</strong> sebagai dibayar?</p>
                    <div class="alert alert-info">
                        <strong>Total yang akan dibayar:</strong> Rp {{ number_format($pranotaUangKenek->total_uang, 0, ',', '.') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Tandai Dibayar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection