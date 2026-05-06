@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
                    <h5 class="m-0 font-weight-bold text-primary">📦 Master Item Kwitansi</h5>
                    <button class="btn btn-primary btn-sm rounded-pill px-3" data-toggle="modal" data-target="#modalTambahItem">
                        <i class="fas fa-plus mr-1"></i> Tambah Item
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                            <thead class="bg-light text-secondary text-uppercase small font-weight-bold">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Item</th>
                                    <th>Satuan</th>
                                    <th class="text-right">Harga Satuan</th>
                                    <th>Keterangan</th>
                                    <th width="150" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="font-weight-bold text-dark">{{ $item->nama_item }}</td>
                                    <td><span class="badge badge-light border text-secondary px-2">{{ $item->satuan ?: '-' }}</span></td>
                                    <td class="text-right font-weight-bold text-primary">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="small text-muted">{{ Str::limit($item->keterangan, 50) ?: '-' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm rounded-circle shadow-sm mr-1" 
                                                onclick="editItem({{ $item->id }}, '{{ $item->nama_item }}', '{{ $item->satuan }}', {{ $item->harga_satuan }}, '{{ $item->keterangan }}')"
                                                data-toggle="modal" data-target="#modalEditItem">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('master.item-kwitansi.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-circle shadow-sm" onclick="return confirm('Hapus item ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahItem" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold">Tambah Item Kwitansi Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('master.item-kwitansi.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-4">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-secondary">NAMA ITEM <span class="text-danger">*</span></label>
                        <input type="text" name="nama_item" class="form-control rounded-lg bg-light border-0 py-4" required placeholder="Contoh: Biaya Sewa Forklift">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-secondary">SATUAN</label>
                                <input type="text" name="satuan" class="form-control rounded-lg bg-light border-0 py-4" placeholder="Rit / Jam / Hari">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-secondary">HARGA SATUAN <span class="text-danger">*</span></label>
                                <input type="number" name="harga_satuan" class="form-control rounded-lg bg-light border-0 py-4" required placeholder="0">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-secondary">KETERANGAN</label>
                        <textarea name="keterangan" class="form-control rounded-lg bg-light border-0" rows="3" placeholder="Opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEditItem" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold">Edit Item Kwitansi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditItem" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body pt-4">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-secondary">NAMA ITEM <span class="text-danger">*</span></label>
                        <input type="text" name="nama_item" id="edit_nama" class="form-control rounded-lg bg-light border-0 py-4" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-secondary">SATUAN</label>
                                <input type="text" name="satuan" id="edit_satuan" class="form-control rounded-lg bg-light border-0 py-4">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-secondary">HARGA SATUAN <span class="text-danger">*</span></label>
                                <input type="number" name="harga_satuan" id="edit_harga" class="form-control rounded-lg bg-light border-0 py-4" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-secondary">KETERANGAN</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control rounded-lg bg-light border-0" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info rounded-pill px-4 shadow text-white">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editItem(id, nama, satuan, harga, keterangan) {
    document.getElementById('formEditItem').action = '{{ url("master/item-kwitansi") }}/' + id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_satuan').value = satuan === 'null' ? '' : satuan;
    document.getElementById('edit_harga').value = harga;
    document.getElementById('edit_keterangan').value = keterangan === 'null' ? '' : keterangan;
}
</script>

<style>
    .form-control:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1) !important;
        border: 1px solid #4e73df !important;
    }
    .table thead th {
        border-top: none;
        border-bottom: 2px solid #eef2f7;
    }
    .table td {
        vertical-align: middle;
        padding: 1.2rem 0.75rem;
    }
    .badge-light {
        background-color: #f8f9fc;
    }
</style>
@endsection
