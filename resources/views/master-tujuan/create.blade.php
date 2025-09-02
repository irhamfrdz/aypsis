@extends('layouts.app')

@section('title','Tambah Tujuan')
@section('page_title', 'Tambah Tujuan')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Form Tambah Tujuan</h2>

    <form action="{{route('master.tujuan.store')}}" method="POST">
        @csrf

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="cabang" class="block text-sm font-medium text-gray-700">Cabang</label>
                <select name="cabang" id="cabang" class="mt-1 block w-full bg-white rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Cabang --</option>
                    <option value="JKT" {{ old('cabang') == 'JKT' ? 'selected' : '' }}>JKT</option>
                    <option value="BTM" {{ old('cabang') == 'BTM' ? 'selected' : '' }}>BTM</option>
                    <option value="PNG" {{ old('cabang') == 'PNG' ? 'selected' : '' }}>PNG</option>
                </select>
            </div>

            <div>
                <label for="wilayah" class="block text-sm font-medium text-gray-700">Wilayah</label>
                <input type="text" name="wilayah" id="wilayah" value="{{ old('wilayah') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="mb-4">
            <label for="rute" class="block text-sm font-medium text-gray-700">Rute</label>
            <input type="text" name="rute" id="rute" value="{{ old('rute') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

    <!-- Uang Jalan (general) removed per request -->

        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="uang_jalan_20_formatted" class="block text-sm font-medium text-gray-700">Uang Jalan 20ft</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-200 bg-gray-50 text-gray-700">Rp</span>
                    <input type="text" id="uang_jalan_20_formatted" value="{{ old('uang_jalan_20') !== null ? number_format(old('uang_jalan_20'), 0, ',', '.') : '' }}" class="flex-1 block w-full bg-gray-100 rounded-r-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                </div>
                <!-- Hidden numeric input submitted -->
                <input type="hidden" name="uang_jalan_20" id="uang_jalan_20" value="{{ old('uang_jalan_20') ?? 0 }}">
            </div>

            <div class="mb-4">
                <label for="ongkos_truk_20_formatted" class="block text-sm font-medium text-gray-700">Ongkos Truk 20ft</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-200 bg-gray-50 text-gray-700">Rp</span>
                    <input type="text" id="ongkos_truk_20_formatted" value="{{ old('ongkos_truk_20') !== null ? number_format(old('ongkos_truk_20'), 0, ',', '.') : '' }}" class="flex-1 block w-full bg-gray-100 rounded-r-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                </div>
                <input type="hidden" name="ongkos_truk_20" id="ongkos_truk_20" value="{{ old('ongkos_truk_20') ?? 0 }}">
            </div>

            <div class="mb-4">
                <label for="uang_jalan_40_formatted" class="block text-sm font-medium text-gray-700">Uang Jalan 40ft</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-200 bg-gray-50 text-gray-700">Rp</span>
                    <input type="text" id="uang_jalan_40_formatted" value="{{ old('uang_jalan_40') !== null ? number_format(old('uang_jalan_40'), 0, ',', '.') : '' }}" class="flex-1 block w-full bg-gray-100 rounded-r-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                </div>
                <input type="hidden" name="uang_jalan_40" id="uang_jalan_40" value="{{ old('uang_jalan_40') ?? 0 }}">
            </div>

            <div class="mb-4">
                <label for="ongkos_truk_40_formatted" class="block text-sm font-medium text-gray-700">Ongkos Truk 40ft</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-200 bg-gray-50 text-gray-700">Rp</span>
                    <input type="text" id="ongkos_truk_40_formatted" value="{{ old('ongkos_truk_40') !== null ? number_format(old('ongkos_truk_40'), 0, ',', '.') : '' }}" class="flex-1 block w-full bg-gray-100 rounded-r-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                </div>
                <input type="hidden" name="ongkos_truk_40" id="ongkos_truk_40" value="{{ old('ongkos_truk_40') ?? 0 }}">
            </div>

            <div class="mb-4">
                <label for="antar_20_formatted" class="block text-sm font-medium text-gray-700">Antar Lokasi 20ft</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-200 bg-gray-50 text-gray-700">Rp</span>
                    <input type="text" id="antar_20_formatted" value="{{ old('antar_20') !== null ? number_format(old('antar_20'), 0, ',', '.') : '' }}" class="flex-1 block w-full bg-gray-100 rounded-r-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                </div>
                <input type="hidden" name="antar_20" id="antar_20" value="{{ old('antar_20') ?? 0 }}">
            </div>

            <div class="mb-4">
                <label for="antar_40_formatted" class="block text-sm font-medium text-gray-700">Antar Lokasi 40ft</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-200 bg-gray-50 text-gray-700">Rp</span>
                    <input type="text" id="antar_40_formatted" value="{{ old('antar_40') !== null ? number_format(old('antar_40'), 0, ',', '.') : '' }}" class="flex-1 block w-full bg-gray-100 rounded-r-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                </div>
                <input type="hidden" name="antar_40" id="antar_40" value="{{ old('antar_40') ?? 0 }}">
            </div>
        </div>

        <div class="mb-4">
            <label for="rit_sewa_kontainer" class="block text-sm font-medium text-gray-700">Rit Sewa Kontainer</label>
            <input type="number" step="1" name="rit_sewa_kontainer" id="rit_sewa_kontainer" value="{{ old('rit_sewa_kontainer') }}" class="mt-1 block w-full bg-gray-100 rounded-md border border-gray-200 p-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="flex space-x-2">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Simpan
            </button>

            <a href="{{route('master.tujuan.index')}}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Format input as thousands (dot) and keep a hidden numeric value for submission
(function(){
    function formatThousands(value){
        const digits = String(value).replace(/\D/g,'');
        if (!digits) return '';
        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function wire(formattedId, hiddenId){
        const fmt = document.getElementById(formattedId);
        const hid = document.getElementById(hiddenId);
        if (!fmt || !hid) return;

        function updateHidden(){
            const raw = fmt.value.replace(/\D/g, '');
            hid.value = raw ? parseInt(raw, 10) : 0;
        }

        fmt.addEventListener('input', function(){
            this.value = formatThousands(this.value);
            this.selectionStart = this.selectionEnd = this.value.length;
            updateHidden();
        });

        updateHidden();
        const form = fmt.closest('form');
        if (form) form.addEventListener('submit', updateHidden);
    }

    // general uang_jalan removed
    wire('uang_jalan_20_formatted','uang_jalan_20');
    wire('uang_jalan_40_formatted','uang_jalan_40');
    wire('ongkos_truk_20_formatted','ongkos_truk_20');
    wire('ongkos_truk_40_formatted','ongkos_truk_40');
    wire('antar_20_formatted','antar_20');
    wire('antar_40_formatted','antar_40');
})();
</script>
@endpush
