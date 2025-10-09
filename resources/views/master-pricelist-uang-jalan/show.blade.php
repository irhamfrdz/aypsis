@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Master Pricelist Uang Jalan</h1>
                    <p class="text-gray-600">Detail pricelist untuk rute {{ $pricelist->dari }} - {{ $pricelist->ke }}</p>
                </div>
                <div>
                    <a href="{{ route('master-pricelist-uang-jalan.edit', $pricelist) }}" 
                       class="btn btn-warning mr-2">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <a href="{{ route('master-pricelist-uang-jalan.index') }}" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Main Info Card -->
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle mr-2"></i>Informasi Pricelist
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="font-weight-bold text-primary border-bottom pb-2">Informasi Dasar</h6>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="font-weight-bold text-muted">Kode:</label>
                                    <p class="mb-0">{{ $pricelist->kode }}</p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="font-weight-bold text-muted">Cabang:</label>
                                    <p class="mb-0">{{ $pricelist->cabang }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold text-muted">Wilayah:</label>
                                    <p class="mb-0">{{ $pricelist->wilayah }}</p>
                                </div>
                            </div>

                            <!-- Route Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="font-weight-bold text-primary border-bottom pb-2">Informasi Rute</h6>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold text-muted">Dari:</label>
                                    <p class="mb-0">{{ $pricelist->dari }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold text-muted">Ke:</label>
                                    <p class="mb-0">{{ $pricelist->ke }}</p>
                                </div>
                            </div>

                            <!-- Pricing Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="font-weight-bold text-primary border-bottom pb-2">Informasi Tarif</h6>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold text-muted">Uang Jalan 20ft:</label>
                                    <p class="mb-0 text-success font-weight-bold">
                                        Rp {{ number_format($pricelist->uang_jalan_20ft, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold text-muted">Uang Jalan 40ft:</label>
                                    <p class="mb-0 text-success font-weight-bold">
                                        Rp {{ number_format($pricelist->uang_jalan_40ft, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold text-muted">Mel 20 Feet:</label>
                                    <p class="mb-0">
                                        {{ $pricelist->mel_20_feet ? 'Rp ' . number_format($pricelist->mel_20_feet, 0, ',', '.') : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold text-muted">Mel 40 Feet:</label>
                                    <p class="mb-0">
                                        {{ $pricelist->mel_40_feet ? 'Rp ' . number_format($pricelist->mel_40_feet, 0, ',', '.') : '-' }}
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold text-muted">Ongkos Truk 20ft:</label>
                                    <p class="mb-0">
                                        {{ $pricelist->ongkos_truk_20ft ? 'Rp ' . number_format($pricelist->ongkos_truk_20ft, 0, ',', '.') : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold text-muted">Antar Lokasi 20ft:</label>
                                    <p class="mb-0">
                                        {{ $pricelist->antar_lokasi_20ft ? 'Rp ' . number_format($pricelist->antar_lokasi_20ft, 0, ',', '.') : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold text-muted">Antar Lokasi 40ft:</label>
                                    <p class="mb-0">
                                        {{ $pricelist->antar_lokasi_40ft ? 'Rp ' . number_format($pricelist->antar_lokasi_40ft, 0, ',', '.') : '-' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="font-weight-bold text-primary border-bottom pb-2">Informasi Tambahan</h6>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold text-muted">Liter:</label>
                                    <p class="mb-0">{{ $pricelist->liter ?? '-' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold text-muted">Jarak (km):</label>
                                    <p class="mb-0">{{ $pricelist->jarak_dari_penjaringan_km ?? '-' }} km</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold text-muted">Status:</label>
                                    <span class="badge {{ $pricelist->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $pricelist->status === 'active' ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold text-muted">Berlaku Dari:</label>
                                    <p class="mb-0">
                                        {{ $pricelist->valid_from ? $pricelist->valid_from->format('d/m/Y') : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold text-muted">Berlaku Sampai:</label>
                                    <p class="mb-0">
                                        {{ $pricelist->valid_to ? $pricelist->valid_to->format('d/m/Y') : '-' }}
                                    </p>
                                </div>
                            </div>

                            @if($pricelist->keterangan)
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="font-weight-bold text-muted">Keterangan:</label>
                                    <p class="mb-0">{{ $pricelist->keterangan }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="col-md-4">
                    <!-- Total Biaya Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calculator mr-2"></i>Total Biaya
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="font-weight-bold text-muted">Total Biaya 20ft:</label>
                                <h4 class="text-success font-weight-bold">
                                    Rp {{ number_format($pricelist->getTotalBiaya('20ft'), 0, ',', '.') }}
                                </h4>
                                <small class="text-muted">
                                    Uang Jalan + Mel + Ongkos Truk + Antar Lokasi
                                </small>
                            </div>
                            <div class="mb-0">
                                <label class="font-weight-bold text-muted">Total Biaya 40ft:</label>
                                <h4 class="text-success font-weight-bold">
                                    Rp {{ number_format($pricelist->getTotalBiaya('40ft'), 0, ',', '.') }}
                                </h4>
                                <small class="text-muted">
                                    Uang Jalan + Mel + Antar Lokasi
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Validity Status Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-check mr-2"></i>Status Validitas
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $now = now();
                                $isValid = true;
                                
                                if ($pricelist->valid_from && $now < $pricelist->valid_from) {
                                    $isValid = false;
                                    $validityText = 'Belum Berlaku';
                                    $validityClass = 'warning';
                                } elseif ($pricelist->valid_to && $now > $pricelist->valid_to) {
                                    $isValid = false;
                                    $validityText = 'Sudah Expired';
                                    $validityClass = 'danger';
                                } else {
                                    $validityText = 'Berlaku';
                                    $validityClass = 'success';
                                }
                                
                                if ($pricelist->status !== 'active') {
                                    $isValid = false;
                                    $validityText = 'Non-Aktif';
                                    $validityClass = 'secondary';
                                }
                            @endphp
                            
                            <div class="text-center">
                                <div class="alert alert-{{ $validityClass }} mb-3">
                                    <i class="fas fa-{{ $isValid ? 'check-circle' : 'times-circle' }} mr-2"></i>
                                    <strong>{{ $validityText }}</strong>
                                </div>
                                
                                @if($pricelist->valid_from)
                                <div class="mb-2">
                                    <small class="text-muted">Mulai Berlaku:</small><br>
                                    <strong>{{ $pricelist->valid_from->format('d F Y') }}</strong>
                                </div>
                                @endif
                                
                                @if($pricelist->valid_to)
                                <div class="mb-0">
                                    <small class="text-muted">Berakhir:</small><br>
                                    <strong>{{ $pricelist->valid_to->format('d F Y') }}</strong>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Audit Trail Card -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-history mr-2"></i>Audit Trail
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Dibuat Oleh:</small><br>
                                <strong>{{ $pricelist->creator ? $pricelist->creator->name : 'System' }}</strong><br>
                                <small class="text-muted">{{ $pricelist->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            
                            @if($pricelist->updated_at != $pricelist->created_at)
                            <div class="mb-0">
                                <small class="text-muted">Diubah Oleh:</small><br>
                                <strong>{{ $pricelist->updater ? $pricelist->updater->name : 'System' }}</strong><br>
                                <small class="text-muted">{{ $pricelist->updated_at->format('d/m/Y H:i') }}</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-cogs mr-2"></i>Aksi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('master-pricelist-uang-jalan.edit', $pricelist) }}" 
                                       class="btn btn-warning mr-2">
                                        <i class="fas fa-edit mr-2"></i>Edit Data
                                    </a>
                                    
                                    @if($pricelist->status === 'active')
                                        <button class="btn btn-secondary mr-2" 
                                                onclick="toggleStatus('{{ $pricelist->id }}', 'inactive')">
                                            <i class="fas fa-pause mr-2"></i>Nonaktifkan
                                        </button>
                                    @else
                                        <button class="btn btn-success mr-2" 
                                                onclick="toggleStatus('{{ $pricelist->id }}', 'active')">
                                            <i class="fas fa-play mr-2"></i>Aktifkan
                                        </button>
                                    @endif
                                    
                                    <button class="btn btn-danger" 
                                            onclick="deleteData('{{ $pricelist->id }}')">
                                        <i class="fas fa-trash mr-2"></i>Hapus
                                    </button>
                                </div>
                                
                                <div>
                                    <a href="{{ route('master-pricelist-uang-jalan.index') }}" 
                                       class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pricelist ini?</p>
                <p><strong>{{ $pricelist->kode }} - {{ $pricelist->dari }} ke {{ $pricelist->ke }}</strong></p>
                <p class="text-danger"><small>Data yang dihapus tidak dapat dikembalikan!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStatus(id, status) {
    if (confirm('Apakah Anda yakin ingin mengubah status pricelist ini?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/master-pricelist-uang-jalan/${id}`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);
        
        // Copy all current data (hidden inputs for all fields)
        const currentData = {!! json_encode($pricelist->toArray()) !!};
        Object.keys(currentData).forEach(key => {
            if (!['created_at', 'updated_at', 'creator', 'updater'].includes(key)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = currentData[key] || '';
                form.appendChild(input);
            }
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteData(id) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/master-pricelist-uang-jalan/${id}`;
    $('#deleteModal').modal('show');
}
</script>
@endsection