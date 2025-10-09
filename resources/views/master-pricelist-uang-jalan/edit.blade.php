@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Master Pricelist Uang Jalan</h1>
                    <p class="text-gray-600">Ubah data pricelist uang jalan untuk rute {{ $pricelist->dari }} - {{ $pricelist->ke }}</p>
                </div>
                <a href="{{ route('master-pricelist-uang-jalan.index') }}" 
                   class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>

            <!-- Form Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('master-pricelist-uang-jalan.update', $pricelist) }}" id="editForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="font-weight-bold text-primary border-bottom pb-2">Informasi Dasar</h5>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Kode -->
                            <div class="col-md-3 mb-3">
                                <label for="kode" class="form-label font-weight-bold">Kode <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('kode') is-invalid @enderror" 
                                       id="kode" 
                                       name="kode" 
                                       value="{{ old('kode', $pricelist->kode) }}"
                                       readonly>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Cabang -->
                            <div class="col-md-3 mb-3">
                                <label for="cabang" class="form-label font-weight-bold">Cabang <span class="text-danger">*</span></label>
                                <select class="form-control @error('cabang') is-invalid @enderror" 
                                        id="cabang" 
                                        name="cabang" 
                                        required>
                                    <option value="">Pilih Cabang</option>
                                    @foreach(['JKT' => 'Jakarta', 'SBY' => 'Surabaya', 'SRG' => 'Semarang', 'BDG' => 'Bandung', 'PLB' => 'Palembang'] as $code => $name)
                                        <option value="{{ $code }}" {{ old('cabang', $pricelist->cabang) == $code ? 'selected' : '' }}>
                                            {{ $code }} - {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cabang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Wilayah -->
                            <div class="col-md-6 mb-3">
                                <label for="wilayah" class="form-label font-weight-bold">Wilayah <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('wilayah') is-invalid @enderror" 
                                       id="wilayah" 
                                       name="wilayah" 
                                       value="{{ old('wilayah', $pricelist->wilayah) }}"
                                       placeholder="Contoh: JAKARTA UTARA"
                                       required>
                                @error('wilayah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Route Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="font-weight-bold text-primary border-bottom pb-2">Informasi Rute</h5>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Dari -->
                            <div class="col-md-6 mb-3">
                                <label for="dari" class="form-label font-weight-bold">Dari <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('dari') is-invalid @enderror" 
                                       id="dari" 
                                       name="dari" 
                                       value="{{ old('dari', $pricelist->dari) }}"
                                       placeholder="Lokasi asal"
                                       required>
                                @error('dari')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Ke -->
                            <div class="col-md-6 mb-3">
                                <label for="ke" class="form-label font-weight-bold">Ke <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('ke') is-invalid @enderror" 
                                       id="ke" 
                                       name="ke" 
                                       value="{{ old('ke', $pricelist->ke) }}"
                                       placeholder="Lokasi tujuan"
                                       required>
                                @error('ke')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Pricing Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="font-weight-bold text-primary border-bottom pb-2">Informasi Tarif</h5>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Uang Jalan 20ft -->
                            <div class="col-md-6 mb-3">
                                <label for="uang_jalan_20ft" class="form-label font-weight-bold">Uang Jalan 20ft <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" 
                                           class="form-control currency-input @error('uang_jalan_20ft') is-invalid @enderror" 
                                           id="uang_jalan_20ft" 
                                           name="uang_jalan_20ft" 
                                           value="{{ old('uang_jalan_20ft', number_format($pricelist->uang_jalan_20ft, 0, ',', '.')) }}"
                                           placeholder="0"
                                           required>
                                </div>
                                @error('uang_jalan_20ft')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Uang Jalan 40ft -->
                            <div class="col-md-6 mb-3">
                                <label for="uang_jalan_40ft" class="form-label font-weight-bold">Uang Jalan 40ft <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" 
                                           class="form-control currency-input @error('uang_jalan_40ft') is-invalid @enderror" 
                                           id="uang_jalan_40ft" 
                                           name="uang_jalan_40ft" 
                                           value="{{ old('uang_jalan_40ft', number_format($pricelist->uang_jalan_40ft, 0, ',', '.')) }}"
                                           placeholder="0"
                                           required>
                                </div>
                                @error('uang_jalan_40ft')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Mel 20ft -->
                            <div class="col-md-6 mb-3">
                                <label for="mel_20_feet" class="form-label font-weight-bold">Mel 20 Feet</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" 
                                           class="form-control currency-input @error('mel_20_feet') is-invalid @enderror" 
                                           id="mel_20_feet" 
                                           name="mel_20_feet" 
                                           value="{{ old('mel_20_feet', number_format($pricelist->mel_20_feet ?? 0, 0, ',', '.')) }}"
                                           placeholder="0">
                                </div>
                                @error('mel_20_feet')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mel 40ft -->
                            <div class="col-md-6 mb-3">
                                <label for="mel_40_feet" class="form-label font-weight-bold">Mel 40 Feet</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" 
                                           class="form-control currency-input @error('mel_40_feet') is-invalid @enderror" 
                                           id="mel_40_feet" 
                                           name="mel_40_feet" 
                                           value="{{ old('mel_40_feet', number_format($pricelist->mel_40_feet ?? 0, 0, ',', '.')) }}"
                                           placeholder="0">
                                </div>
                                @error('mel_40_feet')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Ongkos Truk 20ft -->
                            <div class="col-md-4 mb-3">
                                <label for="ongkos_truk_20ft" class="form-label font-weight-bold">Ongkos Truk 20ft</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" 
                                           class="form-control currency-input @error('ongkos_truk_20ft') is-invalid @enderror" 
                                           id="ongkos_truk_20ft" 
                                           name="ongkos_truk_20ft" 
                                           value="{{ old('ongkos_truk_20ft', number_format($pricelist->ongkos_truk_20ft ?? 0, 0, ',', '.')) }}"
                                           placeholder="0">
                                </div>
                                @error('ongkos_truk_20ft')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Antar Lokasi 20ft -->
                            <div class="col-md-4 mb-3">
                                <label for="antar_lokasi_20ft" class="form-label font-weight-bold">Antar Lokasi 20ft</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" 
                                           class="form-control currency-input @error('antar_lokasi_20ft') is-invalid @enderror" 
                                           id="antar_lokasi_20ft" 
                                           name="antar_lokasi_20ft" 
                                           value="{{ old('antar_lokasi_20ft', number_format($pricelist->antar_lokasi_20ft ?? 0, 0, ',', '.')) }}"
                                           placeholder="0">
                                </div>
                                @error('antar_lokasi_20ft')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Antar Lokasi 40ft -->
                            <div class="col-md-4 mb-3">
                                <label for="antar_lokasi_40ft" class="form-label font-weight-bold">Antar Lokasi 40ft</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" 
                                           class="form-control currency-input @error('antar_lokasi_40ft') is-invalid @enderror" 
                                           id="antar_lokasi_40ft" 
                                           name="antar_lokasi_40ft" 
                                           value="{{ old('antar_lokasi_40ft', number_format($pricelist->antar_lokasi_40ft ?? 0, 0, ',', '.')) }}"
                                           placeholder="0">
                                </div>
                                @error('antar_lokasi_40ft')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="font-weight-bold text-primary border-bottom pb-2">Informasi Tambahan</h5>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Liter -->
                            <div class="col-md-3 mb-3">
                                <label for="liter" class="form-label font-weight-bold">Liter</label>
                                <input type="number" 
                                       class="form-control @error('liter') is-invalid @enderror" 
                                       id="liter" 
                                       name="liter" 
                                       value="{{ old('liter', $pricelist->liter) }}"
                                       placeholder="0"
                                       min="0">
                                @error('liter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Jarak -->
                            <div class="col-md-3 mb-3">
                                <label for="jarak_dari_penjaringan_km" class="form-label font-weight-bold">Jarak (km)</label>
                                <input type="number" 
                                       class="form-control @error('jarak_dari_penjaringan_km') is-invalid @enderror" 
                                       id="jarak_dari_penjaringan_km" 
                                       name="jarak_dari_penjaringan_km" 
                                       value="{{ old('jarak_dari_penjaringan_km', $pricelist->jarak_dari_penjaringan_km) }}"
                                       placeholder="0"
                                       min="0"
                                       step="0.1">
                                @error('jarak_dari_penjaringan_km')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Valid From -->
                            <div class="col-md-3 mb-3">
                                <label for="valid_from" class="form-label font-weight-bold">Berlaku Dari</label>
                                <input type="date" 
                                       class="form-control @error('valid_from') is-invalid @enderror" 
                                       id="valid_from" 
                                       name="valid_from" 
                                       value="{{ old('valid_from', $pricelist->valid_from ? $pricelist->valid_from->format('Y-m-d') : '') }}">
                                @error('valid_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Valid To -->
                            <div class="col-md-3 mb-3">
                                <label for="valid_to" class="form-label font-weight-bold">Berlaku Sampai</label>
                                <input type="date" 
                                       class="form-control @error('valid_to') is-invalid @enderror" 
                                       id="valid_to" 
                                       name="valid_to" 
                                       value="{{ old('valid_to', $pricelist->valid_to ? $pricelist->valid_to->format('Y-m-d') : '') }}">
                                @error('valid_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Keterangan -->
                            <div class="col-md-8 mb-3">
                                <label for="keterangan" class="form-label font-weight-bold">Keterangan</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                          id="keterangan" 
                                          name="keterangan" 
                                          rows="3"
                                          placeholder="Keterangan tambahan...">{{ old('keterangan', $pricelist->keterangan) }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label font-weight-bold">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status">
                                    <option value="active" {{ old('status', $pricelist->status) == 'active' ? 'selected' : '' }}>
                                        Aktif
                                    </option>
                                    <option value="inactive" {{ old('status', $pricelist->status) == 'inactive' ? 'selected' : '' }}>
                                        Non-Aktif
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Auto-calculation Display -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-success">Total Biaya (Auto-calculated)</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Total Biaya 20ft:</strong> 
                                                <span id="total_20ft" class="text-success font-weight-bold">Rp 0</span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Total Biaya 40ft:</strong> 
                                                <span id="total_40ft" class="text-success font-weight-bold">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('master-pricelist-uang-jalan.index') }}" 
                                       class="btn btn-secondary mr-2">
                                        <i class="fas fa-times mr-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save mr-2"></i>Update Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Currency formatting
    const currencyInputs = document.querySelectorAll('.currency-input');
    
    currencyInputs.forEach(input => {
        // Format on load
        formatCurrency(input);
        
        input.addEventListener('input', function() {
            formatCurrency(this);
            calculateTotals();
        });
        
        input.addEventListener('blur', function() {
            formatCurrency(this);
            calculateTotals();
        });
    });
    
    function formatCurrency(input) {
        let value = input.value.replace(/[^\d]/g, '');
        if (value) {
            let formatted = parseInt(value).toLocaleString('id-ID');
            input.value = formatted;
        }
    }
    
    function parseCurrency(value) {
        return parseInt(value.replace(/[^\d]/g, '')) || 0;
    }
    
    function calculateTotals() {
        // Get values
        const uangJalan20ft = parseCurrency(document.getElementById('uang_jalan_20ft').value);
        const uangJalan40ft = parseCurrency(document.getElementById('uang_jalan_40ft').value);
        const mel20ft = parseCurrency(document.getElementById('mel_20_feet').value);
        const mel40ft = parseCurrency(document.getElementById('mel_40_feet').value);
        const ongkosTruk20ft = parseCurrency(document.getElementById('ongkos_truk_20ft').value);
        const antarLokasi20ft = parseCurrency(document.getElementById('antar_lokasi_20ft').value);
        const antarLokasi40ft = parseCurrency(document.getElementById('antar_lokasi_40ft').value);
        
        // Calculate totals
        const total20ft = uangJalan20ft + mel20ft + ongkosTruk20ft + antarLokasi20ft;
        const total40ft = uangJalan40ft + mel40ft + antarLokasi40ft;
        
        // Display totals
        document.getElementById('total_20ft').textContent = 'Rp ' + total20ft.toLocaleString('id-ID');
        document.getElementById('total_40ft').textContent = 'Rp ' + total40ft.toLocaleString('id-ID');
    }
    
    // Form submission - convert formatted numbers back to integers
    document.getElementById('editForm').addEventListener('submit', function(e) {
        currencyInputs.forEach(input => {
            let value = input.value.replace(/[^\d]/g, '');
            input.value = value;
        });
    });
    
    // Initial calculation
    calculateTotals();
});
</script>
@endsection