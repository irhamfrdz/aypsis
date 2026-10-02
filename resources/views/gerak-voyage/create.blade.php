@extends('layouts.app')

@section('title', 'Input Tanggal dan Jam - Gerak Voyage')
@section('page_title', 'Input Tanggal dan Jam - Gerak Voyage')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Card -->
        <div class="rounded-2xl shadow-xl overflow-hidden mb-8" style="background: linear-gradient(to right, #2563eb, #4f46e5);">
            <div class="px-8 py-6">
                <div class="flex items-center justify-between gap-6">
                    <div class="flex items-center flex-1 min-w-0">
                        <div style="background: rgba(255,255,255,0.2);" class="rounded-full p-3 mr-4 flex-shrink-0">
                            <svg class="w-8 h-8" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-2xl font-bold" style="color: white;">Input Tanggal dan Jam Gerak Voyage</h1>
                            <p style="color: #c7d2fe;" class="text-sm mt-1">Kapal: <strong>{{ $namaKapal }}</strong> | Voyage: <strong>{{ $noVoyage }}</strong></p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('gerak-voyage.index') }}" 
                           class="bg-white text-blue-600 hover:bg-gray-50 px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-lg hover:shadow-xl text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-bold mb-1">Ada kesalahan input:</p>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <form action="{{ route('gerak-voyage.store') }}" method="POST">
                @csrf
                <input type="hidden" name="nama_kapal" value="{{ $namaKapal }}">
                <input type="hidden" name="no_voyage" value="{{ $noVoyage }}">
                
                @php
                    $jadwalFields = [
                        'tanggal_muat' => 'Muat',
                        'tanggal_mulai_berlayar' => 'Mulai Berlayar',
                        'tanggal_berlabuh' => 'Berlabuh',
                        'tanggal_sandar' => 'Sandar',
                        'tanggal_mulai_bongkar' => 'Mulai Bongkar',
                        'tanggal_selesai_bongkar' => 'Selesai Bongkar',
                    ];
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    @foreach($jadwalFields as $tanggalField => $label)
                        @php
                            $jamField = str_replace('tanggal_', 'jam_', $tanggalField);
                            $tanggalValue = $tanggalField === 'tanggal_muat' ? $tanggalMuatDefault : $manifest?->{$tanggalField};
                            $jamValue = $manifest?->{$jamField};
                        @endphp
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <h2 class="mb-3 text-sm font-bold text-gray-800">{{ $label }}</h2>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label for="{{ $tanggalField }}" class="mb-1 block text-xs font-semibold text-gray-600">Tanggal</label>
                                    <input type="date" id="{{ $tanggalField }}" name="{{ $tanggalField }}"
                                        value="{{ old($tanggalField, $tanggalValue ? \Carbon\Carbon::parse($tanggalValue)->format('Y-m-d') : '') }}"
                                        class="w-full rounded-lg border-gray-300 bg-white text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="{{ $jamField }}" class="mb-1 block text-xs font-semibold text-gray-600">Jam</label>
                                    <input type="text" id="{{ $jamField }}" name="{{ $jamField }}"
                                        value="{{ old($jamField, $jamValue ? substr($jamValue, 0, 5) : '') }}"
                                        class="js-24-hour-time w-full rounded-lg border-gray-300 bg-white text-sm focus:border-blue-500 focus:ring-blue-500"
                                        inputmode="numeric" maxlength="5" placeholder="HH:mm"
                                        pattern="(?:[01][0-9]|2[0-3]):[0-5][0-9]"
                                        title="Masukkan jam dalam format 24 jam, misalnya 14:30"
                                        autocomplete="off"
                                    >
                                    <p class="mt-1 text-[10px] text-gray-500">Format 24 jam (00:00–23:59)</p>
                                </div>
                            </div>
                            @if($tanggalField === 'tanggal_muat')
                                <p class="mt-2 text-xs {{ $tanggalMuatOb ? 'text-blue-600' : 'text-gray-500' }}">
                                    {{ $tanggalMuatOb ? 'Tanggal OB Muat tersedia sebagai acuan; jam diisi manual.' : 'Tanggal OB Muat belum tersedia; isi tanggal dan jam secara manual.' }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-100">
                    <button type="submit" 
                            class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Tanggal dan Jam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-24-hour-time').forEach(function (input) {
            input.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9:]/g, '').slice(0, 5);
                this.setCustomValidity('');
            });

            input.addEventListener('blur', function () {
                const rawValue = this.value.trim();
                if (!rawValue) {
                    this.setCustomValidity('');
                    return;
                }

                let normalizedValue = rawValue;
                const digitsOnly = rawValue.replace(/\D/g, '');

                if (/^\d{3,4}$/.test(digitsOnly)) {
                    const paddedValue = digitsOnly.padStart(4, '0');
                    normalizedValue = paddedValue.slice(0, 2) + ':' + paddedValue.slice(2);
                } else {
                    const parts = rawValue.split(':');
                    if (parts.length === 2 && /^\d{1,2}$/.test(parts[0]) && /^\d{1,2}$/.test(parts[1])) {
                        normalizedValue = parts[0].padStart(2, '0') + ':' + parts[1].padStart(2, '0');
                    }
                }

                this.value = normalizedValue;
                const isValid = /^(?:[01]\d|2[0-3]):[0-5]\d$/.test(normalizedValue);
                this.setCustomValidity(isValid ? '' : 'Masukkan jam dalam format 24 jam antara 00:00 sampai 23:59.');
            });
        });
    });
</script>
@endpush
@endsection
