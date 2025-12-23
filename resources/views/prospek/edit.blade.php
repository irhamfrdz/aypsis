@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-edit mr-3 text-yellow-600 text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit Prospek</h1>
                    <p class="text-gray-600">Ubah data prospek pengiriman kontainer</p>
                </div>
            </div>
            <a href="{{ route('prospek.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('prospek.update', $prospek->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Left Column - Main Information --}}
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Utama</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ old('tanggal', $prospek->tanggal?->format('Y-m-d')) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">- Pilih Status -</option>
                                    <option value="aktif" {{ old('status', $prospek->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="sudah_muat" {{ old('status', $prospek->status) == 'sudah_muat' ? 'selected' : '' }}>Sudah Muat</option>
                                    <option value="batal" {{ old('status', $prospek->status) == 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pengiriman</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Supir</label>
                                <input type="text" name="nama_supir" value="{{ old('nama_supir', $prospek->nama_supir) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Supir OB</label>
                                <input type="text" name="supir_ob" value="{{ old('supir_ob', $prospek->supir_ob) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PT/Pengirim</label>
                                <input type="text" name="pt_pengirim" value="{{ old('pt_pengirim', $prospek->pt_pengirim) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tujuan Pengiriman</label>
                                <input type="text" name="tujuan_pengiriman" value="{{ old('tujuan_pengiriman', $prospek->tujuan_pengiriman) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kapal</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kapal</label>
                                <select name="kapal_id" id="kapal_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">- Pilih Kapal -</option>
                                    @foreach($kapals as $kapal)
                                        <option value="{{ $kapal->id }}" {{ old('kapal_id', $prospek->kapal_id) == $kapal->id ? 'selected' : '' }}>
                                            {{ $kapal->nama_kapal }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Kapal (Manual)</label>
                                <input type="text" name="nama_kapal" id="nama_kapal" value="{{ old('nama_kapal', $prospek->nama_kapal) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Atau isi manual jika tidak ada di dropdown">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">No Voyage</label>
                                <input type="text" name="no_voyage" value="{{ old('no_voyage', $prospek->no_voyage) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pelabuhan Asal</label>
                                <input type="text" name="pelabuhan_asal" value="{{ old('pelabuhan_asal', $prospek->pelabuhan_asal) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Muat</label>
                                <input type="date" name="tanggal_muat" value="{{ old('tanggal_muat', $prospek->tanggal_muat?->format('Y-m-d')) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Container & Cargo Information --}}
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kontainer</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Kontainer</label>
                                <input type="text" name="nomor_kontainer" value="{{ old('nomor_kontainer', $prospek->nomor_kontainer) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">No Seal</label>
                                <input type="text" name="no_seal" value="{{ old('no_seal', $prospek->no_seal) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ukuran</label>
                                <input type="text" name="ukuran" value="{{ old('ukuran', $prospek->ukuran) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Contoh: 20, 40">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipe</label>
                                <input type="text" name="tipe" value="{{ old('tipe', $prospek->tipe) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Contoh: FCL, LCL">
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Barang</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jenis Barang</label>
                                <input type="text" name="barang" value="{{ old('barang', $prospek->barang) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Ton</label>
                                <input type="number" step="0.01" name="total_ton" value="{{ old('total_ton', $prospek->total_ton) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kuantitas</label>
                                <input type="number" name="kuantitas" value="{{ old('kuantitas', $prospek->kuantitas) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Volume (m³)</label>
                                <input type="number" step="0.01" name="total_volume" value="{{ old('total_volume', $prospek->total_volume) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Referensi</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">No Surat Jalan</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                                    {{ $prospek->no_surat_jalan ?? '-' }}
                                </p>
                            </div>
                            @if($prospek->suratJalan)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Link Surat Jalan</label>
                                <a href="{{ route('surat-jalan.show', $prospek->surat_jalan_id) }}" 
                                   class="mt-1 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Lihat Surat Jalan
                                </a>
                            </div>
                            @endif
                            @if($prospek->tandaTerima)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Link Tanda Terima</label>
                                <a href="{{ route('tanda-terima.show', $prospek->tanda_terima_id) }}" 
                                   class="mt-1 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Lihat Tanda Terima
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Keterangan</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea name="keterangan" rows="4" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('keterangan', $prospek->keterangan) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-8 flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                <a href="{{ route('prospek.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition duration-200">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200 flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Auto-fill nama_kapal when selecting from dropdown
document.getElementById('kapal_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (this.value) {
        document.getElementById('nama_kapal').value = selectedOption.text;
    }
});
</script>
@endpush
@endsection
