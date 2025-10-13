@extends('layouts.app')

@section('title', 'Detail Data Transportasi')
@section('page_title', 'Detail Data Transportasi')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Detail Data Transportasi</h2>
            <a href="{{ route('master.tujuan-kegiatan-utama.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Kode -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Kode</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->kode ?: '-' }}</p>
            </div>

            <!-- Cabang -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Cabang</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->cabang ?: '-' }}</p>
            </div>

            <!-- Wilayah -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Wilayah</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->wilayah ?: '-' }}</p>
            </div>

            <!-- Dari -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Dari</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->dari ?: '-' }}</p>
            </div>

            <!-- Ke -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Ke</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->ke ?: '-' }}</p>
            </div>

            <!-- Uang Jalan 20ft -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Uang Jalan 20ft</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->uang_jalan_20ft ? 'Rp ' . number_format($tujuanKegiatanUtama->uang_jalan_20ft, 0, ',', '.') : '-' }}</p>
            </div>

            <!-- Uang Jalan 40ft -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Uang Jalan 40ft</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->uang_jalan_40ft ? 'Rp ' . number_format($tujuanKegiatanUtama->uang_jalan_40ft, 0, ',', '.') : '-' }}</p>
            </div>

            <!-- Liter -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Liter</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->liter ? number_format($tujuanKegiatanUtama->liter, 2, ',', '.') : '-' }}</p>
            </div>

            <!-- Jarak dari Penjaringan (km) -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Jarak dari Penjaringan (km)</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->jarak_dari_penjaringan_km ? number_format($tujuanKegiatanUtama->jarak_dari_penjaringan_km, 2, ',', '.') : '-' }}</p>
            </div>

            <!-- MEL 20ft -->
            <div>
                <label class="block text-sm font-medium text-gray-700">MEL 20ft</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->mel_20ft ? 'Rp ' . number_format($tujuanKegiatanUtama->mel_20ft, 0, ',', '.') : '-' }}</p>
            </div>

            <!-- MEL 40ft -->
            <div>
                <label class="block text-sm font-medium text-gray-700">MEL 40ft</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->mel_40ft ? 'Rp ' . number_format($tujuanKegiatanUtama->mel_40ft, 0, ',', '.') : '-' }}</p>
            </div>

            <!-- Ongkos Truk 20ft -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Ongkos Truk 20ft</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->ongkos_truk_20ft ? 'Rp ' . number_format($tujuanKegiatanUtama->ongkos_truk_20ft, 0, ',', '.') : '-' }}</p>
            </div>

            <!-- Ongkos Truk 40ft -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Ongkos Truk 40ft</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->ongkos_truk_40ft ? 'Rp ' . number_format($tujuanKegiatanUtama->ongkos_truk_40ft, 0, ',', '.') : '-' }}</p>
            </div>

            <!-- Antar Lokasi 20ft -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Antar Lokasi 20ft</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->antar_lokasi_20ft ? 'Rp ' . number_format($tujuanKegiatanUtama->antar_lokasi_20ft, 0, ',', '.') : '-' }}</p>
            </div>

            <!-- Antar Lokasi 40ft -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Antar Lokasi 40ft</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->antar_lokasi_40ft ? 'Rp ' . number_format($tujuanKegiatanUtama->antar_lokasi_40ft, 0, ',', '.') : '-' }}</p>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <p class="mt-1">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tujuanKegiatanUtama->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $tujuanKegiatanUtama->aktif ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Keterangan - Full Width -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700">Keterangan</label>
            <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->keterangan ?: '-' }}</p>
        </div>

        <!-- Timestamps -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Dibuat Pada</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->created_at->format('d M Y H:i') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Diupdate Pada</label>
                <p class="mt-1 text-sm text-gray-900">{{ $tujuanKegiatanUtama->updated_at->format('d M Y H:i') }}</p>
            </div>
        </div>

        <div class="mt-6 flex space-x-2">
            <a href="{{ route('master.tujuan-kegiatan-utama.edit', $tujuanKegiatanUtama) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                Edit
            </a>
            <form action="{{ route('master.tujuan-kegiatan-utama.destroy', $tujuanKegiatanUtama) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Hapus
                </button>
            </form>
        </div>
    </div>
@endsection
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Hapus
                </button>
            </form>
        </div>
    </div>
@endsection
