@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pilih Pranota dan Pembayaran DP</h6>
        </div>
        <div class="card-body">
            <form id="selectForm" action="{{ route('pembayaran-ob.create') }}" method="GET">
                
                <!-- Pilih Pranota -->
                <div class="form-group">
                    <label for="pranota_id">Pilih Pranota <span class="text-danger">*</span></label>
                    <select class="form-control" id="pranota_id" name="pranota_id" required>
                        <option value="">-- Pilih Pranota --</option>
                        @foreach($pranotas as $pranota)
                        <option value="{{ $pranota->id }}" 
                                data-kapal="{{ $pranota->nama_kapal ?? '-' }}"
                                data-voyage="{{ $pranota->voyage ?? '-' }}">
                            #{{ $pranota->id }} - {{ $pranota->nama_kapal ?? '-' }} / {{ $pranota->voyage ?? '-' }} 
                            ({{ \Carbon\Carbon::parse($pranota->created_at)->format('d/m/Y') }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Display Selected Kapal & Voyage -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Kapal</label>
                            <input type="text" class="form-control" id="display_kapal" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Voyage</label>
                            <input type="text" class="form-control" id="display_voyage" readonly>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Pilih Pembayaran DP (Multiple Select) -->
                <div class="form-group">
                    <label>Pilih Pembayaran DP (Opsional, bisa pilih lebih dari satu)</label>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="select_all_dp">
                                    </th>
                                    <th>Nomor Pembayaran</th>
                                    <th>Tanggal</th>
                                    <th>Supir</th>
                                    <th>Total Pembayaran</th>
                                    <th>Jumlah Per Supir</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pembayaranDps as $dp)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="dp_ids[]" value="{{ $dp->id }}" class="dp-checkbox">
                                    </td>
                                    <td>{{ $dp->nomor_pembayaran }}</td>
                                    <td>{{ \Carbon\Carbon::parse($dp->tanggal_pembayaran)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($dp->supir_ids)
                                            @php
                                                $supirIds = json_decode($dp->supir_ids, true);
                                                if ($supirIds) {
                                                    $supirs = \App\Models\MasterSupir::whereIn('id', $supirIds)->pluck('nama_lengkap')->toArray();
                                                    echo implode(', ', $supirs);
                                                } else {
                                                    echo '-';
                                                }
                                            @endphp
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-right">Rp {{ number_format((float)$dp->total_pembayaran, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format((float)$dp->jumlah_per_supir, 0, ',', '.') }}</td>
                                    <td>{{ $dp->keterangan ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada pembayaran DP</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Lanjutkan ke Form Create
                    </button>
                    <a href="{{ route('pembayaran-ob.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Update kapal and voyage display when pranota selected
    $('#pranota_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var kapal = selectedOption.data('kapal') || '-';
        var voyage = selectedOption.data('voyage') || '-';
        
        $('#display_kapal').val(kapal);
        $('#display_voyage').val(voyage);
    });

    // Select all DP checkboxes
    $('#select_all_dp').on('change', function() {
        $('.dp-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Uncheck "select all" if any checkbox is unchecked
    $('.dp-checkbox').on('change', function() {
        if (!$(this).prop('checked')) {
            $('#select_all_dp').prop('checked', false);
        } else {
            // Check if all checkboxes are checked
            var allChecked = $('.dp-checkbox:checked').length === $('.dp-checkbox').length;
            $('#select_all_dp').prop('checked', allChecked);
        }
    });
});
</script>
@endpush

@endsection
