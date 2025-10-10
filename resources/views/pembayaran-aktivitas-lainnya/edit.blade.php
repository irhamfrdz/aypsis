@extends('layouts.app')

@section('title', 'Edit Pembayaran Aktivitas Lain-lain')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Pembayaran Aktivitas Lain-lain
                        @if($pembayaran->nomor_pembayaran)
                            <span class="badge badge-info ml-2">{{ $pembayaran->nomor_pembayaran }}</span>
                        @endif
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('pembayaran-aktivitas-lainnya.show', $pembayaran->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                @if($pembayaran->status !== 'draft')
                    <div class="alert alert-warning mx-3 mt-3">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Pembayaran dengan status {{ ucfirst($pembayaran->status) }} memiliki batasan dalam pengeditan.
                    </div>
                @endif

                <form action="{{ route('pembayaran-aktivitas-lainnya.update', $pembayaran->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_pembayaran">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                    <input type="date"
                                           class="form-control"
                                           id="tanggal_pembayaran"
                                           name="tanggal_pembayaran"
                                           value="{{ old('tanggal_pembayaran', $pembayaran->tanggal_pembayaran->format('Y-m-d')) }}"
                                           {{ $pembayaran->status === 'paid' ? 'readonly' : '' }}
                                           required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="metode_pembayaran">Metode Pembayaran <span class="text-danger">*</span></label>
                                    <select class="form-control"
                                            id="metode_pembayaran"
                                            name="metode_pembayaran"
                                            {{ $pembayaran->status === 'paid' ? 'disabled' : '' }}
                                            required>
                                        <option value="">Pilih Metode Pembayaran</option>
                                        <option value="cash" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="transfer" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                        <option value="check" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) == 'check' ? 'selected' : '' }}>Check</option>
                                        <option value="credit_card" {{ old('metode_pembayaran', $pembayaran->metode_pembayaran) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    </select>
                                    @if($pembayaran->status === 'paid')
                                        <input type="hidden" name="metode_pembayaran" value="{{ $pembayaran->metode_pembayaran }}">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="referensi_pembayaran">Referensi Pembayaran</label>
                            <input type="text"
                                   class="form-control"
                                   id="referensi_pembayaran"
                                   name="referensi_pembayaran"
                                   value="{{ old('referensi_pembayaran', $pembayaran->referensi_pembayaran) }}"
                                   {{ $pembayaran->status === 'paid' ? 'readonly' : '' }}
                                   placeholder="Nomor referensi, nomor cek, atau keterangan lainnya">
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control"
                                      id="keterangan"
                                      name="keterangan"
                                      rows="3"
                                      {{ $pembayaran->status === 'paid' ? 'readonly' : '' }}
                                      placeholder="Keterangan pembayaran (opsional)">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kegiatan">Kegiatan</label>
                                    <select class="form-control"
                                            id="kegiatan"
                                            name="kegiatan"
                                            {{ $pembayaranAktivitasLainnya->status === 'paid' ? 'disabled' : '' }}>
                                        <option value="">Pilih Kegiatan</option>
                                        @if(isset($masterKegiatan) && $masterKegiatan->count() > 0)
                                            @foreach($masterKegiatan as $kegiatan)
                                                <option value="{{ $kegiatan->nama_kegiatan }}" 
                                                    {{ old('kegiatan', $pembayaranAktivitasLainnya->kegiatan) == $kegiatan->nama_kegiatan ? 'selected' : '' }}>
                                                    {{ $kegiatan->nama_kegiatan }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>Tidak ada kegiatan uang muka tersedia</option>
                                        @endif
                                    </select>
                                    <small class="form-text text-muted">Data dari master kegiatan bertipe "uang muka"</small>
                                </div>
                            </div>
                            <div class="col-md-6" id="plat_nomor_container">
                                <div class="form-group">
                                    <label for="plat_nomor">Plat Nomor <span class="text-danger d-none">*</span></label>
                                    <select class="form-control"
                                            id="plat_nomor"
                                            name="plat_nomor"
                                            {{ $pembayaranAktivitasLainnya->status === 'paid' ? 'disabled' : '' }}>
                                        <option value="">Pilih Plat Nomor</option>
                                        @if(isset($masterMobil) && $masterMobil->count() > 0)
                                            @foreach($masterMobil as $mobil)
                                                <option value="{{ $mobil->plat_nomor }}" 
                                                    {{ old('plat_nomor', $pembayaranAktivitasLainnya->plat_nomor) == $mobil->plat_nomor ? 'selected' : '' }}>
                                                    {{ $mobil->plat_nomor }}
                                                    @if($mobil->merk && $mobil->tipe)
                                                        - {{ $mobil->merk }} {{ $mobil->tipe }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>Tidak ada mobil tersedia</option>
                                        @endif
                                    </select>
                                    <small class="form-text text-muted">Wajib dipilih untuk kegiatan KIR & STNK</small>
                                </div>
                            </div>
                        </div>

                        <!-- Checkbox Bayar DP -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_dp"
                                       name="is_dp"
                                       value="1"
                                       {{ old('is_dp', $pembayaran->is_dp) ? 'checked' : '' }}
                                       {{ $pembayaran->status === 'paid' ? 'disabled' : '' }}>
                                <label class="custom-control-label" for="is_dp">
                                    <strong>Bayar DP (Down Payment)</strong>
                                </label>
                            </div>
                            <small class="text-muted">Centang jika ini adalah pembayaran uang muka/DP</small>

                            <!-- Info panel untuk DP -->
                            <div id="dp-info" class="mt-2" style="display: none;">
                                <div class="alert alert-info alert-sm">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Pembayaran DP:</strong> Pembayaran ini akan ditandai sebagai uang muka yang perlu dilunasi kemudian.
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">
                            <i class="fas fa-list mr-2"></i>
                            Aktivitas yang Dibayar
                        </h5>

                        @if($pembayaran->status === 'draft')
                            <!-- Search aktivitas (only for draft) -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" id="search_aktivitas" class="form-control"
                                               placeholder="Cari aktivitas berdasarkan nomor atau deskripsi...">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="btn_search">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-info" id="btn_show_all">
                                        <i class="fas fa-eye"></i> Tampilkan Semua
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Current selected aktivitas -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="aktivitas_table">
                                <thead>
                                    <tr>
                                        @if($pembayaran->status === 'draft')
                                            <th width="50">
                                                <input type="checkbox" id="select_all">
                                            </th>
                                        @endif
                                        <th>Nomor Aktivitas</th>
                                        <th>Tanggal</th>
                                        <th>Deskripsi</th>
                                        <th>Vendor</th>
                                        <th>Nominal Asli</th>
                                        <th>Nominal Dibayar</th>
                                        @if($pembayaran->status !== 'draft')
                                            <th>Status</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pembayaran->detailPembayaran as $detail)
                                        <tr data-id="{{ $detail->aktivitas_lain_id }}">
                                            @if($pembayaran->status === 'draft')
                                                <td>
                                                    <input type="checkbox"
                                                           name="aktivitas_ids[]"
                                                           value="{{ $detail->aktivitas_lain_id }}"
                                                           class="aktivitas-checkbox"
                                                           checked>
                                                </td>
                                            @endif
                                            <td>{{ $detail->aktivitasLain->nomor_aktivitas }}</td>
                                            <td>{{ $detail->aktivitasLain->tanggal_aktivitas->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;" title="{{ $detail->aktivitasLain->deskripsi_aktivitas }}">
                                                    {{ $detail->aktivitasLain->deskripsi_aktivitas }}
                                                </div>
                                            </td>
                                            <td>{{ $detail->aktivitasLain->vendor->nama ?? '-' }}</td>
                                            <td>Rp {{ number_format($detail->aktivitasLain->nominal, 0, ',', '.') }}</td>
                                            <td>
                                                @if($pembayaran->status === 'draft')
                                                    <input type="text"
                                                           name="nominal_custom[{{ $detail->aktivitas_lain_id }}]"
                                                           class="form-control nominal-custom"
                                                           value="{{ number_format($detail->nominal_dibayar, 0, ',', '.') }}"
                                                           data-original="{{ $detail->aktivitasLain->nominal }}">
                                                @else
                                                    <span class="text-success font-weight-bold">
                                                        Rp {{ number_format($detail->nominal_dibayar, 0, ',', '.') }}
                                                    </span>
                                                @endif
                                            </td>
                                            @if($pembayaran->status !== 'draft')
                                                <td>
                                                    <span class="badge badge-success">Dibayar</span>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $pembayaran->status === 'draft' ? '7' : '6' }}" class="text-center py-4">
                                                <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                                <p class="text-muted">Belum ada aktivitas yang dipilih untuk pembayaran ini.</p>
                                            </td>
                                        </tr>
                                    @endforelse

                                    @if($pembayaran->status === 'draft')
                                        <!-- Available aktivitas for draft status -->
                                        @foreach($availableAktivitas as $item)
                                            <tr data-id="{{ $item->id }}" style="display: none;" class="available-aktivitas">
                                                <td>
                                                    <input type="checkbox"
                                                           name="aktivitas_ids[]"
                                                           value="{{ $item->id }}"
                                                           class="aktivitas-checkbox">
                                                </td>
                                                <td>{{ $item->nomor_aktivitas }}</td>
                                                <td>{{ $item->tanggal_aktivitas->format('d/m/Y') }}</td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $item->deskripsi_aktivitas }}">
                                                        {{ $item->deskripsi_aktivitas }}
                                                    </div>
                                                </td>
                                                <td>{{ $item->vendor->nama ?? '-' }}</td>
                                                <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                                <td>
                                                    <input type="text"
                                                           name="nominal_custom[{{ $item->id }}]"
                                                           class="form-control nominal-custom"
                                                           value="{{ number_format($item->nominal, 0, ',', '.') }}"
                                                           data-original="{{ $item->nominal }}"
                                                           disabled>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Total -->
                        <div class="row mt-3">
                            <div class="col-md-6 offset-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Total Nominal:</strong>
                                            </div>
                                            <div class="col-6 text-right">
                                                <strong id="total_nominal">Rp {{ number_format($pembayaran->total_nominal, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status info -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-left-{{ $pembayaran->status === 'paid' ? 'success' : ($pembayaran->status === 'approved' ? 'info' : 'warning') }}">
                                    <div class="card-body py-2">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <strong>Status Saat Ini:</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="badge badge-{{ $pembayaran->status === 'paid' ? 'success' : ($pembayaran->status === 'approved' ? 'info' : 'warning') }} px-3 py-2">
                                                    {{ ucfirst($pembayaran->status) }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <small class="text-muted">
                                                    Diupdate: {{ $pembayaran->updated_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($pembayaran->status !== 'paid')
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    @if($pembayaran->status === 'draft')
                                        <button type="submit" name="action" value="save" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan sebagai Draft
                                        </button>
                                        <button type="submit" name="action" value="submit" class="btn btn-success">
                                            <i class="fas fa-paper-plane"></i> Simpan & Submit untuk Approval
                                        </button>
                                    @elseif($pembayaran->status === 'approved' && auth()->user()->hasPermission('pembayaran-aktivitas-lainnya-approve'))
                                        <button type="submit" name="action" value="pay" class="btn btn-success">
                                            <i class="fas fa-credit-card"></i> Tandai sebagai Dibayar
                                        </button>
                                    @endif
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('pembayaran-aktivitas-lainnya.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table th {
        background-color: #f8f9fa;
    }

    #aktivitas_table tbody tr.selected {
        background-color: #e3f2fd;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #dee2e6;
    }

    .border-left-success {
        border-left: 4px solid #28a745;
    }

    .border-left-info {
        border-left: 4px solid #17a2b8;
    }

    .border-left-warning {
        border-left: 4px solid #ffc107;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let allAktivitas = [];

    // Handle DP checkbox
    $('#is_dp').on('change', function() {
        if ($(this).is(':checked')) {
            $('#dp-info').slideDown();
        } else {
            $('#dp-info').slideUp();
        }
    });

    // Show DP info if already checked on page load
    if ($('#is_dp').is(':checked')) {
        $('#dp-info').show();
    }

    @if($pembayaran->status === 'draft')
    // Store all aktivitas data for search (only for draft)
    $('#aktivitas_table tbody tr[data-id]').each(function() {
        let row = $(this);
        allAktivitas.push({
            id: row.data('id'),
            element: row,
            searchText: row.find('td').text().toLowerCase()
        });
    });

    // Select all checkbox
    $('#select_all').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('.aktivitas-checkbox:visible').prop('checked', isChecked).trigger('change');
    });

    // Individual checkbox change
    $('.aktivitas-checkbox').on('change', function() {
        let row = $(this).closest('tr');
        let nominalInput = row.find('.nominal-custom');

        if ($(this).is(':checked')) {
            row.addClass('selected').show();
            nominalInput.prop('disabled', false);
        } else {
            row.removeClass('selected');
            if (row.hasClass('available-aktivitas')) {
                row.hide();
            }
            nominalInput.prop('disabled', true);
        }

        updateTotal();
        updateSelectAllCheckbox();
    });

    // Search aktivitas
    $('#search_aktivitas').on('input', function() {
        performSearch();
    });

    $('#btn_search').on('click', function() {
        performSearch();
    });

    function performSearch() {
        let searchTerm = $('#search_aktivitas').val().toLowerCase();

        if (searchTerm === '') {
            hideUnselectedAvailableAktivitas();
            return;
        }

        $('.available-aktivitas').each(function() {
            let row = $(this);
            let searchText = row.find('td').text().toLowerCase();

            if (searchText.includes(searchTerm)) {
                row.show();
            } else {
                row.hide();
            }
        });

        updateSelectAllCheckbox();
    }

    // Show all aktivitas
    $('#btn_show_all').on('click', function() {
        showAllAvailableAktivitas();
    });

    function showAllAvailableAktivitas() {
        $('#search_aktivitas').val('');
        $('.available-aktivitas').show();
        updateSelectAllCheckbox();
    }

    function hideUnselectedAvailableAktivitas() {
        $('.available-aktivitas').each(function() {
            let checkbox = $(this).find('.aktivitas-checkbox');
            if (!checkbox.is(':checked')) {
                $(this).hide();
            }
        });
        updateSelectAllCheckbox();
    }

    // Update select all checkbox state
    function updateSelectAllCheckbox() {
        let totalVisible = $('.aktivitas-checkbox:visible').length;
        let totalChecked = $('.aktivitas-checkbox:visible:checked').length;

        $('#select_all').prop('indeterminate', totalChecked > 0 && totalChecked < totalVisible);
        $('#select_all').prop('checked', totalChecked > 0 && totalChecked === totalVisible);
    }
    @endif

    // Format nominal input
    $('.nominal-custom').on('input', function() {
        let value = $(this).val().replace(/[^\d]/g, '');
        let formattedValue = new Intl.NumberFormat('id-ID').format(value);
        $(this).val(formattedValue);
        updateTotal();
    });

    // Calculate total
    function updateTotal() {
        let total = 0;
        $('.aktivitas-checkbox:checked').each(function() {
            let row = $(this).closest('tr');
            let nominalInput = row.find('.nominal-custom');
            let nominal = nominalInput.val().replace(/[^\d]/g, '') || 0;
            total += parseInt(nominal);
        });

        $('#total_nominal').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
    }

    // Form validation
    $('form').on('submit', function(e) {
        @if($pembayaran->status === 'draft')
        let checkedCount = $('.aktivitas-checkbox:checked').length;

        if (checkedCount === 0) {
            e.preventDefault();
            alert('Harap pilih minimal satu aktivitas untuk dibayar.');
            return false;
        }
        @endif

        // Remove number formatting before submission
        $('.nominal-custom').each(function() {
            let value = $(this).val().replace(/[^\d]/g, '');
            $(this).val(value);
        });
    });

    // Initialize
    @if($pembayaran->status === 'draft')
    hideUnselectedAvailableAktivitas();
    updateSelectAllCheckbox();
    @endif
    updateTotal();

    // Auto-resize textarea
    $('textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Handler untuk dropdown kegiatan - tampilkan plat nomor jika kegiatan mengandung "kir" atau "stnk"
    $('#kegiatan').on('change', function() {
        let selectedKegiatan = $(this).val().toLowerCase();
        
        // Cek apakah kegiatan mengandung kata "kir" atau "stnk"
        if (selectedKegiatan.includes('kir') || selectedKegiatan.includes('stnk')) {
            $('#plat_nomor_container').show();
            $('#plat_nomor').attr('required', true);
            
            // Tambahkan visual indicator bahwa field ini wajib
            $('#plat_nomor_container label .text-danger').removeClass('d-none');
        } else {
            $('#plat_nomor_container').show(); // Tetap tampilkan tapi tidak wajib
            $('#plat_nomor').removeAttr('required');
            
            // Sembunyikan visual indicator required
            $('#plat_nomor_container label .text-danger').addClass('d-none');
        }
    });

    // Cek kegiatan pada saat load (untuk old input)
    let initialKegiatan = $('#kegiatan').val();
    if (initialKegiatan) {
        $('#kegiatan').trigger('change');
    }
});
</script>
@endpush
