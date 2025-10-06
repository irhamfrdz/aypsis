@extends('layouts.app')

@section('title', 'Print Pranota Supir - ' . $startDate . ' sampai ' . $endDate)

@section('content')
<style>
@media print {
    body { margin: 0; }
    .no-print { display: none; }
    .page-break { page-break-before: always; }
    .print-header { position: fixed; top: 0; left: 0; right: 0; background: white; padding: 10px; border-bottom: 1px solid #ccc; }
    .print-content { margin-top: 60px; }
}

.print-header {
    background: #f8f9fa;
    padding: 15px;
    border-bottom: 2px solid #007bff;
    margin-bottom: 20px;
}

.pranota-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 20px;
    padding: 15px;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pranota-header {
    background: #e9ecef;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    border-left: 4px solid #007bff;
}

.permohonan-item {
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
    background: #f8f9fa;
}

.kontainer-list {
    margin-top: 8px;
    padding-left: 15px;
}

.kontainer-item {
    font-size: 0.9em;
    color: #495057;
    margin-bottom: 2px;
}
</style>

<div class="container-fluid">
    <div class="print-header">
        <div class="row">
            <div class="col-md-8">
                <h4 class="mb-1">Laporan Pranota Supir</h4>
                <p class="mb-0 text-muted">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
                <p class="mb-0 text-muted">Total Pranota: {{ $pranotas->count() }}</p>
            </div>
            <div class="col-md-4 text-right">
                <p class="mb-0 text-muted">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
                <button class="btn btn-primary btn-sm no-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>

    <div class="print-content">
        @foreach($pranotas as $pranota)
        <div class="pranota-card">
            <div class="pranota-header">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Pranota #{{ $pranota->nomor_pranota }}</strong><br>
                        <small class="text-muted">Tanggal: {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/m/Y') }}</small>
                    </div>
                    <div class="col-md-6 text-right">
                        <small class="text-muted">
                            Dibuat: {{ \Carbon\Carbon::parse($pranota->created_at)->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h6>Detail Permohonan:</h6>
                    @if($pranota->permohonans->count() > 0)
                        @foreach($pranota->permohonans as $permohonan)
                        <div class="permohonan-item">
                            <div class="row">
                                <div class="col-md-8">
                                    <strong>{{ $permohonan->nomor_memo }}</strong> -
                                    {{ $kegiatanMap[$permohonan->kode_kegiatan] ?? $permohonan->kode_kegiatan }}<br>
                                    <small class="text-muted">
                                        Supir: {{ $permohonan->supir->nama ?? 'N/A' }} |
                                        Krani: {{ $permohonan->krani->nama ?? 'N/A' }}
                                    </small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <small class="text-muted">
                                        Tanggal: {{ \Carbon\Carbon::parse($permohonan->tanggal_memo)->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>

                            @if($permohonan->kontainers->count() > 0)
                            <div class="kontainer-list">
                                <strong>Kontainer:</strong>
                                @foreach($permohonan->kontainers as $kontainer)
                                <div class="kontainer-item">
                                    {{ $kontainer->nomor_kontainer }} ({{ $kontainer->tipe_kontainer }})
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">Tidak ada permohonan terkait.</p>
                    @endif
                </div>
            </div>
        </div>

        @if(!$loop->last)
        <div class="page-break"></div>
        @endif
        @endforeach
    </div>
</div>

<script>
window.onload = function() {
    // Auto-print setelah halaman dimuat (opsional, bisa dinonaktifkan)
    // setTimeout(function() { window.print(); }, 1000);
};
</script>
@endsection
