@extends('layouts.app')

@section('title', 'Edit Pranota Uang Kenek')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Pranota Uang Kenek</h3>
                    <div class="card-tools">
                        <a href="{{ route('pranota-uang-kenek.show', $pranotaUangKenek) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('pranota-uang-kenek.update', $pranotaUangKenek) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <!-- Informasi Pranota -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Informasi Pranota</h5>
                                
                                <div class="form-group">
                                    <label for="no_pranota">No Pranota</label>
                                    <input type="text" class="form-control" id="no_pranota" 
                                           value="{{ $pranotaUangKenek->no_pranota }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal">Tanggal Pranota <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                           id="tanggal" name="tanggal" 
                                           value="{{ old('tanggal', $pranotaUangKenek->tanggal) }}" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                              id="keterangan" name="keterangan" rows="3" 
                                              placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $pranotaUangKenek->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Informasi Surat Jalan -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Informasi Surat Jalan</h5>
                                
                                <div class="form-group">
                                    <label for="no_surat_jalan">No Surat Jalan</label>
                                    <input type="text" class="form-control" id="no_surat_jalan" 
                                           value="{{ $pranotaUangKenek->no_surat_jalan }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="supir_nama">Supir</label>
                                    <input type="text" class="form-control" id="supir_nama" 
                                           value="{{ $pranotaUangKenek->supir_nama ?: '-' }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="kenek_nama">Kenek <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kenek_nama') is-invalid @enderror" 
                                           id="kenek_nama" name="kenek_nama" 
                                           value="{{ old('kenek_nama', $pranotaUangKenek->kenek_nama) }}" required>
                                    @error('kenek_nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="no_plat">No Plat <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('no_plat') is-invalid @enderror" 
                                           id="no_plat" name="no_plat" 
                                           value="{{ old('no_plat', $pranotaUangKenek->no_plat) }}" required>
                                    @error('no_plat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="uang_rit_kenek">Uang Rit Kenek <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" class="form-control @error('uang_rit_kenek') is-invalid @enderror" 
                                               id="uang_rit_kenek" name="uang_rit_kenek" 
                                               value="{{ old('uang_rit_kenek', $pranotaUangKenek->uang_rit_kenek) }}" 
                                               min="0" step="1000" required>
                                        @error('uang_rit_kenek')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Summary -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3">Ringkasan</h5>
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Status:</strong> 
                                            <span class="badge badge-secondary ml-2">{{ ucfirst($pranotaUangKenek->status) }}</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Total Uang:</strong> 
                                            <span class="text-success ml-2" id="totalUangDisplay">
                                                Rp {{ number_format($pranotaUangKenek->total_uang, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('pranota-uang-kenek.show', $pranotaUangKenek) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Update total when uang rit kenek changes
    $('#uang_rit_kenek').on('input', function() {
        var uangRitKenek = parseFloat($(this).val()) || 0;
        $('#totalUangDisplay').text('Rp ' + uangRitKenek.toLocaleString('id-ID'));
    });
});
</script>
@endsection