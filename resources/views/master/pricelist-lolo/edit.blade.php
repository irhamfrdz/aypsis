@extends('layouts.app')

@section('title', 'Edit Pricelist LOLO')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all duration-300">
            <div class="bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 p-10 text-white relative">
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight leading-none mb-3">EDIT PRICELIST LOLO</h2>
                        <p class="text-amber-100/80 font-medium text-lg">Perbarui data tarif Terminal Lift-On / Lift-Off</p>
                    </div>
                </div>
                <div class="absolute -right-8 -bottom-8 transform rotate-12 opacity-10">
                    <i class="fas fa-edit text-9xl"></i>
                </div>
            </div>

            <div class="p-12">
                <form action="{{ route('master.pricelist-lolo.update', $pricelistLolo->id) }}" method="POST" class="space-y-10">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="group relative">
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3 group-focus-within:text-amber-600 transition-colors">Terminal Petikemas <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <i class="fas fa-building absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-amber-500 transition-colors"></i>
                                <input type="text" name="terminal" class="w-full pl-12 pr-4 py-4 bg-gray-50 border-0 ring-1 ring-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all outline-none text-gray-900 font-bold" value="{{ old('terminal', $pricelistLolo->terminal) }}" placeholder="Contoh: Terminal Teluk Bayur" required>
                            </div>
                        </div>

                        <div class="group relative">
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3 group-focus-within:text-amber-600 transition-colors">Ukuran Kontainer <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <i class="fas fa-expand-arrows-alt absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-amber-500 transition-colors"></i>
                                <select name="size" class="w-full pl-12 pr-4 py-4 bg-gray-50 border-0 ring-1 ring-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all outline-none text-gray-900 font-bold cursor-pointer" required>
                                    <option value="" disabled>-- Pilih Ukuran --</option>
                                    <option value="20" {{ old('size', $pricelistLolo->size) == '20' ? 'selected' : '' }}>20'</option>
                                    <option value="40" {{ old('size', $pricelistLolo->size) == '40' ? 'selected' : '' }}>40'</option>
                                    <option value="45" {{ old('size', $pricelistLolo->size) == '45' ? 'selected' : '' }}>45'</option>
                                </select>
                            </div>
                        </div>

                        <div class="group relative">
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3 group-focus-within:text-amber-600 transition-colors">Kategori Muatan <span class="text-rose-500">*</span></label>
                            <div class="relative grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="kategori" value="Full" class="hidden peer" {{ old('kategori', $pricelistLolo->kategori) == 'Full' ? 'checked' : '' }} required>
                                    <div class="flex items-center justify-center p-4 rounded-xl border-2 border-gray-100 bg-gray-50 peer-checked:border-amber-600 peer-checked:bg-amber-50 transition-all font-black text-gray-500 peer-checked:text-amber-600">
                                        FULL
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="kategori" value="Empty" class="hidden peer" {{ old('kategori', $pricelistLolo->kategori) == 'Empty' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center p-4 rounded-xl border-2 border-gray-100 bg-gray-50 peer-checked:border-amber-600 peer-checked:bg-amber-50 transition-all font-black text-gray-500 peer-checked:text-amber-600">
                                        EMPTY
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="group relative">
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3 group-focus-within:text-amber-600 transition-colors">Jenis Aktivitas <span class="text-rose-500">*</span></label>
                            <div class="relative grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="tipe_aktivitas" value="Lift On" class="hidden peer" {{ old('tipe_aktivitas', $pricelistLolo->tipe_aktivitas) == 'Lift On' ? 'checked' : '' }} required>
                                    <div class="flex items-center justify-center p-4 rounded-xl border-2 border-gray-100 bg-gray-50 peer-checked:border-amber-600 peer-checked:bg-amber-50 transition-all font-black text-gray-500 peer-checked:text-amber-600">
                                        LIFT ON
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="tipe_aktivitas" value="Lift Off" class="hidden peer" {{ old('tipe_aktivitas', $pricelistLolo->tipe_aktivitas) == 'Lift Off' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center p-4 rounded-xl border-2 border-gray-100 bg-gray-50 peer-checked:border-amber-600 peer-checked:bg-amber-50 transition-all font-black text-gray-500 peer-checked:text-amber-600">
                                        LIFT OFF
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="group relative">
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3 group-focus-within:text-amber-600 transition-colors">Tarif (IDR) <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-gray-400 transition-colors group-focus-within:text-amber-500">Rp</span>
                                <input type="number" name="tarif" class="w-full pl-12 pr-4 py-4 bg-gray-50 border-0 ring-1 ring-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all outline-none text-gray-900 font-extrabold text-lg" value="{{ old('tarif', (int)$pricelistLolo->tarif) }}" placeholder="0" required>
                            </div>
                        </div>

                        <div class="group relative">
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3 group-focus-within:text-amber-600 transition-colors">Status Aktif <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <i class="fas fa-toggle-on absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-amber-500 transition-colors"></i>
                                <select name="status" class="w-full pl-12 pr-4 py-4 bg-gray-50 border-0 ring-1 ring-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all outline-none text-gray-900 font-bold cursor-pointer" required>
                                    <option value="aktif" {{ old('status', $pricelistLolo->status) == 'aktif' ? 'selected' : '' }}>AKTIF</option>
                                    <option value="non-aktif" {{ old('status', $pricelistLolo->status) == 'non-aktif' ? 'selected' : '' }}>TIDAK AKTIF</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pt-10 flex flex-col md:flex-row gap-4">
                        <div class="flex-grow">
                            <button type="submit" class="w-full py-5 bg-amber-600 hover:bg-amber-700 text-white font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-amber-100 transform transition-all active:scale-95 duration-200">
                                <i class="fas fa-sync-alt mr-2"></i> Perbarui Tarif
                            </button>
                        </div>
                        <div class="md:w-1/3">
                            <a href="{{ route('master.pricelist-lolo.index') }}" class="w-full inline-flex items-center justify-center py-5 bg-white border border-gray-200 text-gray-500 font-black uppercase tracking-widest rounded-2xl hover:bg-gray-50 transition-all">
                                <i class="fas fa-times-circle mr-2"></i> Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
