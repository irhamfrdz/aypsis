<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Pengirim</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 p-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Pengirim</h1>
                    <p class="mt-1 text-sm text-gray-600">Perbarui informasi pengirim</p>
                </div>
                <button type="button" onclick="window.close()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Tutup
                </button>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('tanda-terima.pengirim.update', $pengirim->id) }}" method="POST" class="space-y-6" id="pengirimForm">
                @csrf
                @method('PUT')
                
                <!-- Hidden field to mark as popup -->
                <input type="hidden" name="popup" value="1">

                <!-- Kode Field -->
                <div>
                    <label for="kode" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode" id="kode" value="{{ old('kode', $pengirim->kode) }}" required readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 @error('kode') border-red-500 @enderror">
                    @error('kode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Pengirim Field -->
                <div>
                    <label for="nama_pengirim" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Pengirim <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_pengirim" id="nama_pengirim" value="{{ old('nama_pengirim', $pengirim->nama_pengirim) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 @error('nama_pengirim') border-red-500 @enderror"
                           placeholder="Masukkan nama pengirim" autofocus>
                    @error('nama_pengirim')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Field -->
                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea name="catatan" id="catatan" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 @error('catatan') border-red-500 @enderror"
                              placeholder="Masukkan catatan (opsional)">{{ old('catatan', $pengirim->catatan) }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Harga Krani 20ft Field -->
                    <div>
                        <label for="harga_krani_20ft" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Krani 20ft
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                Rp
                            </span>
                            <input type="number" name="harga_krani_20ft" id="harga_krani_20ft" value="{{ old('harga_krani_20ft', $pengirim->harga_krani_20ft ?? 0) }}" min="0" step="1000"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 @error('harga_krani_20ft') border-red-500 @enderror"
                                   placeholder="0">
                        </div>
                        @error('harga_krani_20ft')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Krani 40ft Field -->
                    <div>
                        <label for="harga_krani_40ft" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Krani 40ft
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                Rp
                            </span>
                            <input type="number" name="harga_krani_40ft" id="harga_krani_40ft" value="{{ old('harga_krani_40ft', $pengirim->harga_krani_40ft ?? 0) }}" min="0" step="1000"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 @error('harga_krani_40ft') border-red-500 @enderror"
                                   placeholder="0">
                        </div>
                        @error('harga_krani_40ft')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status Field -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $pengirim->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $pengirim->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="window.close()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-md z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-md z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle form submission
            document.getElementById('pengirimForm').addEventListener('submit', function() {
                const submitButton = this.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            });
        });
    </script>
</body>
</html>
