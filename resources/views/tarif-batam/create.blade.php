@extends('layouts.main')

@section('title', 'Tambah Tarif Batam')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">💰 Tambah Tarif Batam</h1>
                    <p class="text-muted">Menambahkan tarif pengiriman baru untuk wilayah Batam</p>
                </div>
                <a href="{{ route('tarif-batam.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Tarif Batam</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('tarif-batam.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    {{-- Tarif Chasis AYP --}}
                    <div class="col-md-6 mb-3">
                        <label for="chasis_ayp" class="form-label">Tarif Chasis AYP</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control @error('chasis_ayp') is-invalid @enderror" 
                                   id="chasis_ayp" 
                                   name="chasis_ayp" 
                                   value="{{ old('chasis_ayp') }}"
                                   placeholder="0"
                                   min="0"
                                   step="0.01">
                        </div>
                        @error('chasis_ayp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tarif 20ft Full --}}
                    <div class="col-md-6 mb-3">
                        <label for="20ft_full" class="form-label">Tarif 20ft Full</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control @error('20ft_full') is-invalid @enderror" 
                                   id="20ft_full" 
                                   name="20ft_full" 
                                   value="{{ old('20ft_full') }}"
                                   placeholder="0"
                                   min="0"
                                   step="0.01">
                        </div>
                        @error('20ft_full')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tarif 20ft Empty --}}
                    <div class="col-md-6 mb-3">
                        <label for="20ft_empty" class="form-label">Tarif 20ft Empty</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control @error('20ft_empty') is-invalid @enderror" 
                                   id="20ft_empty" 
                                   name="20ft_empty" 
                                   value="{{ old('20ft_empty') }}"
                                   placeholder="0"
                                   min="0"
                                   step="0.01">
                        </div>
                        @error('20ft_empty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tarif Antar Lokasi --}}
                    <div class="col-md-6 mb-3">
                        <label for="antar_lokasi" class="form-label">Tarif Antar Lokasi</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control @error('antar_lokasi') is-invalid @enderror" 
                                   id="antar_lokasi" 
                                   name="antar_lokasi" 
                                   value="{{ old('antar_lokasi') }}"
                                   placeholder="0"
                                   min="0"
                                   step="0.01">
                        </div>
                        @error('antar_lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tarif 40ft Full --}}
                    <div class="col-md-6 mb-3">
                        <label for="40ft_full" class="form-label">Tarif 40ft Full</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control @error('40ft_full') is-invalid @enderror" 
                                   id="40ft_full" 
                                   name="40ft_full" 
                                   value="{{ old('40ft_full') }}"
                                   placeholder="0"
                                   min="0"
                                   step="0.01">
                        </div>
                        @error('40ft_full')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tarif 40ft Empty --}}
                    <div class="col-md-6 mb-3">
                        <label for="40ft_empty" class="form-label">Tarif 40ft Empty</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control @error('40ft_empty') is-invalid @enderror" 
                                   id="40ft_empty" 
                                   name="40ft_empty" 
                                   value="{{ old('40ft_empty') }}"
                                   placeholder="0"
                                   min="0"
                                   step="0.01">
                        </div>
                        @error('40ft_empty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tarif 40ft Antar Lokasi --}}
                    <div class="col-md-6 mb-3">
                        <label for="40ft_antar_lokasi" class="form-label">Tarif 40ft Antar Lokasi</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control @error('40ft_antar_lokasi') is-invalid @enderror" 
                                   id="40ft_antar_lokasi" 
                                   name="40ft_antar_lokasi" 
                                   value="{{ old('40ft_antar_lokasi') }}"
                                   placeholder="0"
                                   min="0"
                                   step="0.01">
                        </div>
                        @error('40ft_antar_lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Masa Berlaku --}}
                    <div class="col-md-6 mb-3">
                        <label for="masa_berlaku" class="form-label">Masa Berlaku <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('masa_berlaku') is-invalid @enderror" 
                               id="masa_berlaku" 
                               name="masa_berlaku" 
                               value="{{ old('masa_berlaku') }}"
                               required>
                        @error('masa_berlaku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" 
                                name="status" 
                                required>
                            <option value="">Pilih Status</option>
                            <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="col-12 mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                  id="keterangan" 
                                  name="keterangan" 
                                  rows="3"
                                  placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('tarif-batam.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default masa berlaku to today
    const masaBerlakuInput = document.getElementById('masa_berlaku');
    if (!masaBerlakuInput.value) {
        const today = new Date().toISOString().split('T')[0];
        masaBerlakuInput.value = today;
    }
});
</script>
@endpush