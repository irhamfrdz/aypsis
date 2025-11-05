@extends('layouts.app')

@section('title', 'Tambah Surat Jalan Bongkaran')

@section('content')
<div class="flex-1 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Surat Jalan Bongkaran</h1>
            <nav class="flex text-sm text-gray-600 mt-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="mx-2">/</span>
                <a href="{{ route('surat-jalan-bongkaran.index') }}" class="hover:text-blue-600">Surat Jalan Bongkaran</a>
                <span class="mx-2">/</span>
                <span class="text-gray-500">Tambah</span>
            </nav>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex">
                <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h4 class="font-medium">Terdapat kesalahan pada form:</h4>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Form -->
    <form action="{{ route('surat-jalan-bongkaran.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Basic Information Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Dasar</h2>
            </div>
            <div class="p-6">
                <!-- Selected Kapal & Voyage Info (Read-only) -->
                @if(isset($selectedKapal) && isset($noVoyage))
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-blue-700 mb-1">Kapal Terpilih</label>
                                <p class="text-sm font-semibold text-blue-900">{{ $selectedKapal->nama_kapal }}</p>
                                <input type="hidden" name="kapal_id" value="{{ $selectedKapal->id }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-blue-700 mb-1">No Voyage</label>
                                <p class="text-sm font-semibold text-blue-900">{{ $noVoyage }}</p>
                                <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-blue-700 mb-1">No BL</label>
                                <p class="text-sm font-semibold text-blue-900">{{ request('no_bl', '-') }}</p>
                                @if(request('no_bl'))
                                    <input type="hidden" name="no_bl" value="{{ request('no_bl') }}">
                                @endif
                            </div>
                        </div>
                        <div class="mt-3 text-right">
                            <a href="{{ route('surat-jalan-bongkaran.select-kapal') }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Ubah Pilihan
                            </a>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Order -->
                    <div>
                        <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Order <span class="text-red-500">*</span>
                        </label>
                        <select name="order_id" id="order_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('order_id') border-red-300 @enderror">
                            <option value="">Pilih Order</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                    {{ $order->nomor_order }}
                                </option>
                            @endforeach
                        </select>
                        @error('order_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kapal (if not pre-selected) -->
                    @if(!isset($selectedKapal))
                    <div>
                        <label for="kapal_id" class="block text-sm font-medium text-gray-700 mb-1">Kapal</label>
                        <select name="kapal_id" id="kapal_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kapal_id') border-red-300 @enderror">
                            <option value="">Pilih Kapal</option>
                            @foreach($kapals as $kapal)
                                <option value="{{ $kapal->id }}" {{ old('kapal_id') == $kapal->id ? 'selected' : '' }}>
                                    {{ $kapal->nama_kapal }}
                                </option>
                            @endforeach
                        </select>
                        @error('kapal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    <!-- Nomor Surat Jalan -->
                    <div>
                        <label for="nomor_surat_jalan" class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Surat Jalan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomor_surat_jalan" id="nomor_surat_jalan" required
                               value="{{ old('nomor_surat_jalan') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nomor_surat_jalan') border-red-300 @enderror"
                               placeholder="Masukkan nomor surat jalan">
                        @error('nomor_surat_jalan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Bongkar -->
                    <div>
                        <label for="tanggal_bongkar" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Bongkar <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_bongkar" id="tanggal_bongkar" required
                               value="{{ old('tanggal_bongkar') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_bongkar') border-red-300 @enderror">
                        @error('tanggal_bongkar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jam Mulai Bongkar -->
                    <div>
                        <label for="jam_mulai_bongkar" class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai Bongkar</label>
                        <input type="time" name="jam_mulai_bongkar" id="jam_mulai_bongkar"
                               value="{{ old('jam_mulai_bongkar') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jam_mulai_bongkar') border-red-300 @enderror">
                        @error('jam_mulai_bongkar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jam Selesai Bongkar -->
                    <div>
                        <label for="jam_selesai_bongkar" class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai Bongkar</label>
                        <input type="time" name="jam_selesai_bongkar" id="jam_selesai_bongkar"
                               value="{{ old('jam_selesai_bongkar') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jam_selesai_bongkar') border-red-300 @enderror">
                        @error('jam_selesai_bongkar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Pengirim & Penerima Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Pengirim & Penerima</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Pengirim -->
                    <div class="space-y-4">
                        <h3 class="text-md font-medium text-gray-700 border-b pb-2">Pengirim</h3>
                        
                        <div>
                            <label for="nama_pengirim" class="block text-sm font-medium text-gray-700 mb-1">Nama Pengirim</label>
                            <input type="text" name="nama_pengirim" id="nama_pengirim"
                                   value="{{ old('nama_pengirim') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_pengirim') border-red-300 @enderror"
                                   placeholder="Masukkan nama pengirim">
                            @error('nama_pengirim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alamat_pengirim" class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengirim</label>
                            <textarea name="alamat_pengirim" id="alamat_pengirim" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('alamat_pengirim') border-red-300 @enderror"
                                      placeholder="Masukkan alamat pengirim">{{ old('alamat_pengirim') }}</textarea>
                            @error('alamat_pengirim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="telepon_pengirim" class="block text-sm font-medium text-gray-700 mb-1">Telepon Pengirim</label>
                            <input type="text" name="telepon_pengirim" id="telepon_pengirim"
                                   value="{{ old('telepon_pengirim') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('telepon_pengirim') border-red-300 @enderror"
                                   placeholder="Masukkan nomor telepon pengirim">
                            @error('telepon_pengirim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Penerima -->
                    <div class="space-y-4">
                        <h3 class="text-md font-medium text-gray-700 border-b pb-2">Penerima</h3>
                        
                        <div>
                            <label for="nama_penerima" class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima</label>
                            <input type="text" name="nama_penerima" id="nama_penerima"
                                   value="{{ old('nama_penerima') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_penerima') border-red-300 @enderror"
                                   placeholder="Masukkan nama penerima">
                            @error('nama_penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alamat_penerima" class="block text-sm font-medium text-gray-700 mb-1">Alamat Penerima</label>
                            <textarea name="alamat_penerima" id="alamat_penerima" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('alamat_penerima') border-red-300 @enderror"
                                      placeholder="Masukkan alamat penerima">{{ old('alamat_penerima') }}</textarea>
                            @error('alamat_penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="telepon_penerima" class="block text-sm font-medium text-gray-700 mb-1">Telepon Penerima</label>
                            <input type="text" name="telepon_penerima" id="telepon_penerima"
                                   value="{{ old('telepon_penerima') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('telepon_penerima') border-red-300 @enderror"
                                   placeholder="Masukkan nomor telepon penerima">
                            @error('telepon_penerima')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Barang Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informasi Barang</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Jenis Barang -->
                    <div>
                        <label for="jenis_barang" class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                        <input type="text" name="jenis_barang" id="jenis_barang"
                               value="{{ old('jenis_barang') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_barang') border-red-300 @enderror"
                               placeholder="Masukkan jenis barang">
                        @error('jenis_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Barang -->
                    <div>
                        <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" id="nama_barang"
                               value="{{ old('nama_barang') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_barang') border-red-300 @enderror"
                               placeholder="Masukkan nama barang">
                        @error('nama_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Barang -->
                    <div>
                        <label for="jumlah_barang" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Barang</label>
                        <input type="number" name="jumlah_barang" id="jumlah_barang" step="0.01"
                               value="{{ old('jumlah_barang') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jumlah_barang') border-red-300 @enderror"
                               placeholder="Masukkan jumlah barang">
                        @error('jumlah_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Satuan Barang -->
                    <div>
                        <label for="satuan_barang" class="block text-sm font-medium text-gray-700 mb-1">Satuan Barang</label>
                        <input type="text" name="satuan_barang" id="satuan_barang"
                               value="{{ old('satuan_barang') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('satuan_barang') border-red-300 @enderror"
                               placeholder="Masukkan satuan barang (kg, ton, pcs, dll)">
                        @error('satuan_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Berat Barang -->
                    <div>
                        <label for="berat_barang" class="block text-sm font-medium text-gray-700 mb-1">Berat Barang (kg)</label>
                        <input type="number" name="berat_barang" id="berat_barang" step="0.01"
                               value="{{ old('berat_barang') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('berat_barang') border-red-300 @enderror"
                               placeholder="Masukkan berat barang">
                        @error('berat_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Volume Barang -->
                    <div>
                        <label for="volume_barang" class="block text-sm font-medium text-gray-700 mb-1">Volume Barang (m³)</label>
                        <input type="number" name="volume_barang" id="volume_barang" step="0.01"
                               value="{{ old('volume_barang') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('volume_barang') border-red-300 @enderror"
                               placeholder="Masukkan volume barang">
                        @error('volume_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nilai Barang -->
                    <div>
                        <label for="nilai_barang" class="block text-sm font-medium text-gray-700 mb-1">Nilai Barang (Rp)</label>
                        <input type="number" name="nilai_barang" id="nilai_barang" step="0.01"
                               value="{{ old('nilai_barang') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nilai_barang') border-red-300 @enderror"
                               placeholder="Masukkan nilai barang">
                        @error('nilai_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kondisi Barang -->
                    <div>
                        <label for="kondisi_barang" class="block text-sm font-medium text-gray-700 mb-1">Kondisi Barang</label>
                        <select name="kondisi_barang" id="kondisi_barang"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kondisi_barang') border-red-300 @enderror">
                            <option value="">Pilih kondisi barang</option>
                            <option value="baik" {{ old('kondisi_barang') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak" {{ old('kondisi_barang') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="cacat" {{ old('kondisi_barang') == 'cacat' ? 'selected' : '' }}>Cacat</option>
                        </select>
                        @error('kondisi_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keterangan Barang (full width) -->
                    <div class="md:col-span-2 lg:col-span-3">
                        <label for="keterangan_barang" class="block text-sm font-medium text-gray-700 mb-1">Keterangan Barang</label>
                        <textarea name="keterangan_barang" id="keterangan_barang" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('keterangan_barang') border-red-300 @enderror"
                                  placeholder="Masukkan keterangan tambahan tentang barang">{{ old('keterangan_barang') }}</textarea>
                        @error('keterangan_barang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 pt-6">
            <a href="{{ route('surat-jalan-bongkaran.index') }}" 
               class="inline-flex items-center px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Batal
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto generate nomor surat jalan if needed
    const generateNomor = () => {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const date = String(today.getDate()).padStart(2, '0');
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        
        return `SJB/${year}${month}${date}/${random}`;
    };
    
    // Set default nomor if empty
    const nomorInput = document.getElementById('nomor_surat_jalan');
    if (!nomorInput.value) {
        nomorInput.value = generateNomor();
    }
    
    // Set default tanggal to today
    const tanggalInput = document.getElementById('tanggal_bongkar');
    if (!tanggalInput.value) {
        const today = new Date().toISOString().split('T')[0];
        tanggalInput.value = today;
    }
});
</script>
@endpush