@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Tambah Surat Jalan Batam</h1>
                <p class="text-xs text-gray-600 mt-1">Buat surat jalan baru untuk pengiriman Batam</p>
            </div>
            <a href="{{ route('surat-jalan-batam.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-150 flex items-center text-sm">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        @if($selectedOrder)
        <!-- Selected Order Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg mx-4 mt-4 p-4 text-sm">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="font-medium text-blue-800">Order Terpilih: {{ $selectedOrder->nomor_order }}</h4>
                    <p class="text-blue-700 text-xs mt-1">
                        Pengirim: {{ $selectedOrder->pengirim->nama_pengirim ?? '-' }} | 
                        Barang: {{ $selectedOrder->jenisBarang->nama_barang ?? '-' }}
                    </p>
                </div>
                <a href="{{ route('surat-jalan-batam.select-order') }}" class="text-blue-600 hover:underline">Ganti Order</a>
            </div>
        </div>
        @endif

        <!-- Form -->
        <form action="{{ route('surat-jalan-batam.store') }}" method="POST" class="p-4">
            @csrf

            @if($selectedOrder)
                <input type="hidden" name="order_batam_id" value="{{ $selectedOrder->id }}">
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Row 1 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Surat Jalan <span class="text-red-600">*</span></label>
                    <div class="flex">
                        <input type="text" name="no_surat_jalan" id="no_surat_jalan" value="{{ old('no_surat_jalan') }}" required
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="button" onclick="generateSjNumber()" class="bg-indigo-600 text-white px-3 py-2 rounded-r-lg text-xs">Auto</button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat Jalan <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal_surat_jalan" value="{{ old('tanggal_surat_jalan', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Section: Order Details -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Detail Pengiriman</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                    <input type="text" name="pengirim" value="{{ old('pengirim', $selectedOrder->pengirim->nama_pengirim ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                    <input type="text" name="jenis_barang" value="{{ old('jenis_barang', $selectedOrder->jenisBarang->nama_barang ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengambilan</label>
                    <input type="text" name="tujuan_pengambilan" value="{{ old('tujuan_pengambilan', $selectedOrder->tujuan_ambil ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman</label>
                    <input type="text" name="tujuan_pengiriman" value="{{ old('tujuan_pengiriman', $selectedOrder->tujuan_kirim ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50">
                </div>

                <!-- Section: Transport -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Informasi Transportasi</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Plat Kendaraan</label>
                    <input type="text" name="no_plat" value="{{ old('no_plat') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir</label>
                    <select name="supir" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Pilih Supir</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->nama_panggilan ?: $k->nama_lengkap }}">{{ $k->nama_panggilan ?: $k->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supir 2 (Opsional)</label>
                    <input type="text" name="supir2" value="{{ old('supir2') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kenek</label>
                    <input type="text" name="kenek" value="{{ old('kenek') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <!-- Section: Kontainer -->
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">Informasi Kontainer</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kontainer</label>
                    <input type="text" name="tipe_kontainer" value="{{ old('tipe_kontainer', $selectedOrder->tipe_kontainer ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Kontainer</label>
                    <input type="text" name="no_kontainer" value="{{ old('no_kontainer') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <a href="{{ route('surat-jalan-batam.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-600">Batal</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    async function generateSjNumber() {
        const dateInput = document.querySelector('input[name="tanggal_surat_jalan"]');
        const sjInput = document.getElementById('no_surat_jalan');
        
        try {
            const response = await fetch('{{ route("surat-jalan-batam.generate-number") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ date: dateInput.value })
            });
            const data = await response.json();
            if (data.number) {
                sjInput.value = data.number;
            }
        } catch (error) {
            console.error('Error generating SJ number:', error);
        }
    }
</script>
@endsection
