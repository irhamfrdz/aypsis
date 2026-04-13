@extends('layouts.app')

@section('title', 'Edit Batch Asuransi')
@section('page_title', 'Edit Batch Asuransi')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('asuransi-tanda-terima-multi.update', $batch->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100 transition-all duration-300">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-8 border-b pb-4">Metadata Batch Asuransi</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nomor Polis</label>
                    <input type="text" name="nomor_polis" value="{{ old('nomor_polis', $batch->nomor_polis) }}" 
                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-3 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Polis <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_polis" value="{{ old('tanggal_polis', $batch->tanggal_polis->format('Y-m-d')) }}" required
                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-3 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Vendor Asuransi <span class="text-red-500">*</span></label>
                    <select name="vendor_asuransi_id" required
                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-3 transition-all">
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_asuransi_id', $batch->vendor_asuransi_id) == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->nama_asuransi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Rate (%)</label>
                        <input type="number" step="0.00001" name="asuransi_rate" value="{{ old('asuransi_rate', $batch->asuransi_rate) }}"
                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-3 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Biaya Admin</label>
                        <input type="number" name="biaya_admin" value="{{ old('biaya_admin', $batch->biaya_admin) }}"
                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-3 transition-all">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="4" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-3 transition-all">{{ old('keterangan', $batch->keterangan) }}</textarea>
                </div>
            </div>

            <div class="mt-10 pt-8 border-t flex items-center justify-between">
                <a href="{{ route('asuransi-tanda-terima-multi.show', $batch->id) }}" class="text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest flex items-center">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center px-8 py-3.5 bg-gray-900 hover:bg-black text-white rounded-xl font-bold uppercase tracking-widest shadow-xl transition-all transform hover:-translate-y-0.5">
                    Perbarui Data Batch
                </button>
            </div>
        </div>
    </form>

    <!-- Display-only section for items -->
    <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 mt-8">
        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Daftar Item dalam Batch ini (Read-Only)</h4>
        <div class="space-y-3">
            @foreach($batch->items as $item)
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center group transition-all hover:border-blue-200">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-gray-900">{{ $item->receipt_number }}</span>
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">{{ $item->receipt_type }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-indigo-700">Rp {{ number_format($item->nilai_pertanggungan, 0, ',', '.') }}</span>
                        <div class="text-[10px] text-gray-400 uppercase font-medium">Nilai Barang</div>
                    </div>
                </div>
            @endforeach
        </div>
        <p class="mt-6 text-[10px] text-gray-400 italic text-center uppercase tracking-tighter">Untuk mengubah daftar item, silakan buat batch baru atau hubungi administrator.</p>
    </div>
</div>
@endsection
