@extends('layouts.app')

@section('title', 'Laporan Pergerakan Kapal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Laporan Pergerakan Kapal</h3>
                    <div class="card-tools">
                        <button onclick="window.print()" class="btn btn-sm btn-primary">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kapal</th>
                                    <th>Kapten</th>
                                    <th>Voyage</th>
                                    <th>Pelabuhan Asal</th>
                                    <th>Pelabuhan Tujuan</th>
                                    <th>Tanggal Sandar</th>
                                    <th>Tanggal Labuh</th>
                                    <th>Tanggal Berangkat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pergerakanKapals as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->nama_kapal }}</td>
                                        <td>{{ $item->kapten }}</td>
                                        <td>{{ $item->voyage }}</td>
                                        <td>{{ $item->tujuan_asal }}</td>
                                        <td>{{ $item->tujuan_tujuan }}</td>
                                        <td>{{ $item->tanggal_sandar ? $item->tanggal_sandar->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $item->tanggal_labuh ? $item->tanggal_labuh->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $item->tanggal_berangkat ? $item->tanggal_berangkat->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            @switch($item->status)
                                                @case('scheduled')
                                                    <span class="badge bg-info">Terjadwal</span>
                                                    @break
                                                @case('sailing')
                                                    <span class="badge bg-warning">Berlayar</span>
                                                    @break
                                                @case('arrived')
                                                    <span class="badge bg-success">Tiba</span>
                                                    @break
                                                @case('departed')
                                                    <span class="badge bg-secondary">Berangkat</span>
                                                    @break
                                                @case('delayed')
                                                    <span class="badge bg-danger">Tertunda</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-dark">Dibatalkan</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge bg-primary">Disetujui</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-light text-dark">{{ $item->status }}</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data pergerakan kapal</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text-muted">
                        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh {{ Auth::user()->username }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .card-tools {
        display: none !important;
    }
    
    .btn {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    body {
        margin: 0;
        padding: 15px;
    }
    
    .container-fluid {
        margin: 0;
        padding: 0;
    }
}
</style>
@endsection