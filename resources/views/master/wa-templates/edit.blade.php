@extends('layouts.app')

@section('title', 'Edit Template WA')
@section('page_title', 'Edit Template WhatsApp')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 font-sans max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-xl font-bold text-gray-800">Edit Template WA</h2>
        <a href="{{ route('master.wa-templates.index') }}" class="text-gray-600 hover:text-gray-900 font-medium flex items-center text-sm">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Ada {{ $errors->count() }} kesalahan:</h3>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('master.wa-templates.update', $wa_template) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-6">
            <div>
                <label for="nama_template" class="block text-sm font-semibold text-gray-700 mb-1">Nama Template</label>
                <input type="text" name="nama_template" id="nama_template" value="{{ old('nama_template', $wa_template->nama_template) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="isi_template" class="block text-sm font-semibold text-gray-700">Isi Template</label>
                    <span class="text-xs text-indigo-600 font-medium cursor-default">
                        <i class="fas fa-mouse-pointer mr-1"></i>Klik variabel di bawah untuk menyisipkan ke teks
                    </span>
                </div>

                {{-- Interactive Variable Palette --}}
                <div class="p-3.5 mb-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-2.5">
                    <div>
                        <div class="flex items-center gap-1.5 font-semibold text-slate-700 mb-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Status Pengiriman & Kontainer (Data Real-time dari Database):</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="insertVariable('{status}')"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-mono font-semibold text-xs border border-emerald-300 transition-all hover:scale-105"
                                title="Klik untuk sisipkan. Mengambil data Status OB kontainer: Sudah OB / Belum OB / Sebagian Sudah OB">
                                <i class="fas fa-bolt text-emerald-600 text-[10px]"></i>
                                {status}
                                <span class="text-[10px] font-sans font-normal text-emerald-700 ml-1">(Status OB)</span>
                            </button>
                            <button type="button" onclick="insertVariable('{status_ob}')"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-mono font-semibold text-xs border border-emerald-300 transition-all hover:scale-105"
                                title="Klik untuk sisipkan. Mengambil data Status OB muatan">
                                <i class="fas fa-tag text-emerald-600 text-[10px]"></i>
                                {status_ob}
                            </button>
                            <button type="button" onclick="insertVariable('{daftar_resi}')"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-800 font-mono font-semibold text-xs border border-indigo-300 transition-all hover:scale-105"
                                title="Klik untuk sisipkan. Menampilkan rincian nomor BL, nomor kontainer, serta status OB tiap kontainer">
                                <i class="fas fa-list-check text-indigo-600 text-[10px]"></i>
                                {daftar_resi}
                                <span class="text-[10px] font-sans font-normal text-indigo-700 ml-1">(BL + Ctr + Status OB)</span>
                            </button>
                            <button type="button" onclick="insertVariable('{kategori_masalah}')"
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-mono text-xs border border-slate-300 transition-all hover:scale-105"
                                title="Kategori masalah kapal atau status pengiriman">
                                {kategori_masalah}
                            </button>
                            <button type="button" onclick="insertVariable('{deskripsi_masalah}')"
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-mono text-xs border border-slate-300 transition-all hover:scale-105"
                                title="Deskripsi atau keterangan tambahan status muatan">
                                {deskripsi_masalah}
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 pt-2">
                        <div class="flex items-center gap-1.5 font-semibold text-slate-700 mb-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>Data Kapal, Shipper & Jadwal:</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="insertVariable('{shipper_name}')"
                                class="px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 font-mono text-xs border border-blue-200 transition-all hover:scale-105">
                                {shipper_name}
                            </button>
                            <button type="button" onclick="insertVariable('{nama_kapal}')"
                                class="px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 font-mono text-xs border border-blue-200 transition-all hover:scale-105">
                                {nama_kapal}
                            </button>
                            <button type="button" onclick="insertVariable('{no_voyage}')"
                                class="px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 font-mono text-xs border border-blue-200 transition-all hover:scale-105">
                                {no_voyage}
                            </button>
                            <button type="button" onclick="insertVariable('{pelabuhan}')"
                                class="px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 font-mono text-xs border border-blue-200 transition-all hover:scale-105">
                                {pelabuhan}
                            </button>
                            <button type="button" onclick="insertVariable('{close}')"
                                class="px-2 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-mono text-xs border border-amber-200 transition-all hover:scale-105">
                                {close}
                            </button>
                            <button type="button" onclick="insertVariable('{etd}')"
                                class="px-2 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-mono text-xs border border-amber-200 transition-all hover:scale-105">
                                {etd}
                            </button>
                            <button type="button" onclick="insertVariable('{eta}')"
                                class="px-2 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-mono text-xs border border-amber-200 transition-all hover:scale-105">
                                {eta}
                            </button>
                            <button type="button" onclick="insertVariable('{estimasi_keterlambatan}')"
                                class="px-2 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-mono text-xs border border-amber-200 transition-all hover:scale-105">
                                {estimasi_keterlambatan}
                            </button>
                        </div>
                    </div>

                    <div class="bg-emerald-50 rounded-lg p-2 border border-emerald-200 text-[11px] text-emerald-800 flex items-start gap-2">
                        <i class="fas fa-info-circle text-emerald-600 mt-0.5"></i>
                        <span>
                            <strong>Info Variabel Status:</strong> Variabel <code class="font-bold text-emerald-900 bg-emerald-200/70 px-1 rounded">{status}</code> atau <code class="font-bold text-emerald-900 bg-emerald-200/70 px-1 rounded">{status_ob}</code> otomatis mengambil status <strong>STATUS OB</strong> (Oper Bongkar) kontainer milik shipper dari database (misal: <em>Sudah OB (tanggal jam)</em>, <em>Belum OB</em>, atau <em>Sebagian Sudah OB</em>).
                        </span>
                    </div>
                </div>

                <textarea name="isi_template" id="isi_template" rows="10" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono leading-relaxed">{{ old('isi_template', $wa_template->isi_template) }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $wa_template->is_active) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300 shadow-sm text-sm">
                Perbarui
            </button>
        </div>
    </form>
</div>

<script>
function insertVariable(variable) {
    const textarea = document.getElementById('isi_template');
    if (!textarea) return;

    const startPos = textarea.selectionStart;
    const endPos = textarea.selectionEnd;
    const textBefore = textarea.value.substring(0, startPos);
    const textAfter = textarea.value.substring(endPos, textarea.value.length);

    textarea.value = textBefore + variable + textAfter;
    textarea.focus();
    const newPos = startPos + variable.length;
    textarea.setSelectionRange(newPos, newPos);
}
</script>
@endsection
