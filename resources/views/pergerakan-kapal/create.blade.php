@extends('layouts.app')

@section('title', 'Tambah Pergerakan Kapal')
@section('page_title', 'Tambah Pergerakan Kapal')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Tambah Pergerakan Kapal</h1>
                <p class="text-xs text-gray-600 mt-1">Isi form di bawah untuk menambah data pergerakan kapal</p>
            </div>
            <div>
                <a href="{{ route('pergerakan-kapal.index') }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-4">
            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <div class="font-medium text-sm mb-2">Terdapat kesalahan pada input:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pergerakan-kapal.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Informasi Kapal -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kapal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label for="nama_kapal" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Kapal <span class="text-red-500">*</span>
                            </label>
                            <select name="nama_kapal" id="nama_kapal" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nama_kapal') border-red-500 @enderror">
                                <option value="">-- Pilih Nama Kapal --</option>
                                @foreach($masterKapals as $kapal)
                                    <option value="{{ $kapal->nama_kapal }}" {{ old('nama_kapal') == $kapal->nama_kapal ? 'selected' : '' }}>
                                        {{ $kapal->nama_kapal }}
                                    </option>
                                @endforeach
                            </select>
                            @error('nama_kapal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kapten" class="block text-sm font-medium text-gray-700 mb-1">
                                Kapten
                            </label>
                            <select name="kapten" id="kapten"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('kapten') border-red-500 @enderror">
                                <option value="">-- Pilih Kapten --</option>
                                @foreach($nahkodas as $nahkoda)
                                    <option value="{{ $nahkoda->nama_lengkap }}" {{ old('kapten') == $nahkoda->nama_lengkap ? 'selected' : '' }}>
                                        {{ $nahkoda->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kapten')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="voyage" class="block text-sm font-medium text-gray-700 mb-1">
                                Voyage
                            </label>
                            <div class="flex gap-2">
                                <input type="text" name="voyage" id="voyage" value="{{ old('voyage') }}"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('voyage') border-red-500 @enderror"
                                       placeholder="Masukkan nomor voyage atau klik Generate">
                                <button type="button" onclick="generateVoyage()"
                                        class="px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Generate
                                </button>
                            </div>
                            @error('voyage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Rute -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Rute</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="tujuan_asal" class="block text-sm font-medium text-gray-700 mb-1">
                                Tujuan Asal <span class="text-red-500">*</span>
                            </label>
                            <select name="tujuan_asal" id="tujuan_asal" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_asal') border-red-500 @enderror">
                                <option value="">-- Pilih Tujuan Asal --</option>
                                @foreach($tujuanKirims as $tujuan)
                                    <option value="{{ $tujuan->nama_tujuan }}" {{ old('tujuan_asal') == $tujuan->nama_tujuan ? 'selected' : '' }}>
                                        {{ $tujuan->nama_tujuan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tujuan_asal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tujuan_tujuan" class="block text-sm font-medium text-gray-700 mb-1">
                                Tujuan Pengiriman <span class="text-red-500">*</span>
                            </label>
                            <select name="tujuan_tujuan" id="tujuan_tujuan" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_tujuan') border-red-500 @enderror">
                                <option value="">-- Pilih Tujuan Pengiriman --</option>
                                @foreach($tujuanKirims as $tujuan)
                                    <option value="{{ $tujuan->nama_tujuan }}" {{ old('tujuan_tujuan') == $tujuan->nama_tujuan ? 'selected' : '' }}>
                                        {{ $tujuan->nama_tujuan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tujuan_tujuan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Transit -->
                    <div class="flex items-center mb-4">
                        <input type="checkbox" name="transit" id="transit" value="1" {{ old('transit') ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                               onchange="toggleTransitFields()">
                        <label for="transit" class="ml-2 block text-sm text-gray-900">
                            Ada Transit
                        </label>
                    </div>

                    <!-- Transit Fields -->
                    <div id="transit-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: {{ old('transit') ? 'grid' : 'none' }}">
                        <div>
                            <label for="tujuan_transit" class="block text-sm font-medium text-gray-700 mb-1">
                                Tujuan Transit
                            </label>
                            <select name="tujuan_transit" id="tujuan_transit"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tujuan_transit') border-red-500 @enderror">
                                <option value="">-- Pilih Tujuan Transit --</option>
                                @foreach($tujuanKirims as $tujuan)
                                    <option value="{{ $tujuan->nama_tujuan }}" {{ old('tujuan_transit') == $tujuan->nama_tujuan ? 'selected' : '' }}>
                                        {{ $tujuan->nama_tujuan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tujuan_transit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="voyage_transit" class="block text-sm font-medium text-gray-700 mb-1">
                                Voyage Transit
                            </label>
                            <input type="text" name="voyage_transit" id="voyage_transit" value="{{ old('voyage_transit') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('voyage_transit') border-red-500 @enderror"
                                   placeholder="Nomor voyage transit">
                            @error('voyage_transit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Jadwal -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Jadwal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="tanggal_sandar" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Sandar
                            </label>
                            <input type="datetime-local" name="tanggal_sandar" id="tanggal_sandar" value="{{ old('tanggal_sandar') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_sandar') border-red-500 @enderror">
                            @error('tanggal_sandar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tanggal_labuh" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Labuh
                            </label>
                            <input type="datetime-local" name="tanggal_labuh" id="tanggal_labuh" value="{{ old('tanggal_labuh') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_labuh') border-red-500 @enderror">
                            @error('tanggal_labuh')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tanggal_berangkat" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Berangkat
                            </label>
                            <input type="datetime-local" name="tanggal_berangkat" id="tanggal_berangkat" value="{{ old('tanggal_berangkat') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tanggal_berangkat') border-red-500 @enderror">
                            @error('tanggal_berangkat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status dan Keterangan -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status dan Keterangan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                                <option value="">-- Pilih Status --</option>
                                <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                                <option value="sailing" {{ old('status') == 'sailing' ? 'selected' : '' }}>Berlayar</option>
                                <option value="arrived" {{ old('status') == 'arrived' ? 'selected' : '' }}>Tiba</option>
                                <option value="departed" {{ old('status') == 'departed' ? 'selected' : '' }}>Berangkat</option>
                                <option value="delayed" {{ old('status') == 'delayed' ? 'selected' : '' }}>Tertunda</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                            Keterangan
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('pergerakan-kapal.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Pergerakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endpush

@push('scripts')
<script>
    function toggleTransitFields() {
        const transitCheckbox = document.getElementById('transit');
        const transitFields = document.getElementById('transit-fields');

        if (transitCheckbox.checked) {
            transitFields.style.display = 'grid';
        } else {
            transitFields.style.display = 'none';
            // Clear values when hiding
            document.getElementById('tujuan_transit').value = '';
            document.getElementById('voyage_transit').value = '';
        }
    }

    function generateVoyage() {
        const namaKapal = document.getElementById('nama_kapal').value;
        const tujuanAsal = document.getElementById('tujuan_asal').value;
        const tujuanTujuan = document.getElementById('tujuan_tujuan').value;

        if (!namaKapal || !tujuanAsal || !tujuanTujuan) {
            alert('Silakan pilih Nama Kapal, Tujuan Asal, dan Tujuan Pengiriman terlebih dahulu');
            return;
        }

        // Show loading state
        const voyageInput = document.getElementById('voyage');
        const generateBtn = document.querySelector('button[onclick="generateVoyage()"]');

        voyageInput.value = 'Generating...';
        generateBtn.disabled = true;
        generateBtn.textContent = 'Loading...';

        // Call API to generate voyage number
        const params = new URLSearchParams({
            nama_kapal: namaKapal,
            tujuan_asal: tujuanAsal,
            tujuan_tujuan: tujuanTujuan
        });

        fetch(`{{ route('api.pergerakan-kapal.generate-voyage') }}?${params}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                if (!response.ok) {
                    if (response.status === 302) {
                        throw new Error('Authentication required. Please login first.');
                    }
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                return response.json();
            })
            .then(data => {
                console.log('API Response:', data);

                if (data.voyage_number) {
                    voyageInput.value = data.voyage_number;
                } else if (data.error) {
                    alert('Error: ' + data.error);
                    voyageInput.value = '';
                } else {
                    alert('Unexpected response format');
                    voyageInput.value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat generate nomor voyage: ' + error.message);
                voyageInput.value = '';
            })
            .finally(() => {
                // Reset button state
                generateBtn.disabled = false;
                generateBtn.textContent = 'Generate';
            });
    }

    // Auto generate when kapal or tujuan changed
    document.addEventListener('DOMContentLoaded', function() {
        const fields = ['nama_kapal', 'tujuan_asal', 'tujuan_tujuan'];
        fields.forEach(field => {
            document.getElementById(field).addEventListener('change', function() {
                // Auto generate if all required fields are filled
                const namaKapal = document.getElementById('nama_kapal').value;
                const tujuanAsal = document.getElementById('tujuan_asal').value;
                const tujuanTujuan = document.getElementById('tujuan_tujuan').value;

                if (namaKapal && tujuanAsal && tujuanTujuan) {
                    generateVoyage();
                }
            });
        });
    });
</script>
    {{-- Choices.js for searchable Nama Kapal --}}
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const namaKapalEl = document.getElementById('nama_kapal');
            if (namaKapalEl && typeof Choices !== 'undefined') {
                const namaKapalChoices = new Choices(namaKapalEl, {
                    searchEnabled: true,
                    shouldSort: false,
                    searchPlaceholderValue: 'Cari nama kapal...'
                });
                namaKapalEl.choicesInstance = namaKapalChoices;
            }
        });
    </script>
@endpush
