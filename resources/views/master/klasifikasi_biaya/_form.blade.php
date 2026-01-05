@csrf

<div class="space-y-4">
    <div>
        <label class="text-xs font-medium">Kode</label>
        <div class="relative">
            <input type="text" 
                   name="kode" 
                   id="kode_input"
                   value="{{ old('kode', $item->kode ?? '') }}" 
                   class="w-full mt-1 px-3 py-2 border rounded {{ isset($item) ? '' : 'bg-gray-50' }}"
                   {{ isset($item) ? '' : 'readonly' }}
                   placeholder="{{ isset($item) ? '' : 'Loading...' }}">
            @if(!isset($item))
            <div id="kode_loader" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            @endif
        </div>
        @error('kode')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="text-xs font-medium">Nama</label>
        <input type="text" name="nama" value="{{ old('nama', $item->nama ?? '') }}" class="w-full mt-1 px-3 py-2 border rounded">
        @error('nama')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="text-xs font-medium">Deskripsi</label>
        <textarea name="deskripsi" class="w-full mt-1 px-3 py-2 border rounded" rows="3">{{ old('deskripsi', $item->deskripsi ?? '') }}</textarea>
        @error('deskripsi')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="flex items-center">
        <input type="checkbox" name="is_active" id="is_active" class="mr-2" {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}>
        <label for="is_active" class="text-xs">Aktif</label>
    </div>

    <div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
        <a href="{{ route('klasifikasi-biaya.index') }}" class="ml-2 text-gray-600">Batal</a>
    </div>
</div>

@if(!isset($item))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kodeInput = document.getElementById('kode_input');
    const loader = document.getElementById('kode_loader');
    
    if (kodeInput && loader) {
        fetch('{{ route('klasifikasi-biaya.get-next-kode') }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.kode) {
                kodeInput.value = data.kode;
                loader.style.display = 'none';
            } else {
                throw new Error('Invalid response format');
            }
        })
        .catch(error => {
            console.error('Error fetching kode:', error);
            kodeInput.value = 'KB001';
            kodeInput.placeholder = 'Kode otomatis (offline mode)';
            loader.style.display = 'none';
        });
    }
});
</script>
@endif
