@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8 pb-4 border-b border-gray-100">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-extrabold tracking-tight">Gabungkan PUML</h1>
        <p class="text-sm text-gray-500 mt-1">Pilih data draf Uang Makan dan Lembur Karyawan untuk digabungkan menjadi satu dokumen PUML yang utuh.</p>
    </div>

    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 rounded-r-lg p-4 mb-6 shadow-sm flex items-start" role="alert">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-rose-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg p-4 mb-6 shadow-sm flex items-start" role="alert">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-emerald-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <form action="{{ route('pranota-puml.store') }}" method="POST">
        @csrf
        
        <!-- Input Group -->
        <div class="bg-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] rounded-xl border border-gray-100 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal PUML <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar text-gray-400"></i>
                        </div>
                        <input type="date" name="tanggal_pranota" required value="{{ date('Y-m-d') }}" class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200">
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Pilihan Uang Makan -->
            <div class="bg-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] rounded-xl border border-gray-100 overflow-hidden">
                <header class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-white border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center text-emerald-800 font-bold">
                        <div class="bg-emerald-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-utensils text-emerald-600"></i>
                        </div>
                        Draft Uang Makan
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        {{ $draftUangMakan->count() }} Tersedia
                    </span>
                </header>
                <div class="p-4 bg-gray-50/30">
                    @forelse($draftUangMakan as $um)
                        <label class="relative flex items-start p-4 cursor-pointer bg-white border border-gray-200 rounded-xl mb-3 hover:border-emerald-400 hover:shadow-md transition-all duration-200 group">
                            <div class="flex items-center h-5 mt-1">
                                <input type="checkbox" name="uang_makan_ids[]" value="{{ $um->id }}" class="form-checkbox text-emerald-500 border-gray-300 focus:ring-emerald-500 h-5 w-5 rounded transition-colors duration-200">
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-center mb-1">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-bold text-gray-900 group-hover:text-emerald-700 transition-colors">{{ $um->nomor_pranota }}</p>
                                        <a href="{{ route('pranota-uang-makan.show', $um->id) }}" target="_blank" onclick="event.stopPropagation()" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Lihat Isi Pranota">
                                            <i class="fas fa-external-link-alt text-[10px]"></i>
                                        </a>
                                    </div>
                                    <span class="text-xs font-medium text-gray-500">{{ $um->tanggal_pranota->format('d M Y') }}</span>
                                </div>
                                <p class="text-sm font-semibold text-emerald-600">Rp {{ number_format($um->total_nominal, 0, ',', '.') }}</p>
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-10 px-4 bg-white rounded-xl border border-dashed border-gray-300">
                            <div class="text-gray-300 mb-3"><i class="fas fa-inbox text-4xl"></i></div>
                            <p class="text-sm text-gray-500 font-medium mb-3">Tidak ada draf Uang Makan yang tersedia.</p>
                            <a href="{{ route('payroll.uang-makan') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors">
                                <i class="fas fa-calculator mr-2"></i> Kalkulasi Sekarang
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pilihan Lembur -->
            <div class="bg-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] rounded-xl border border-gray-100 overflow-hidden">
                <header class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center text-orange-800 font-bold">
                        <div class="bg-orange-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-clock text-orange-600"></i>
                        </div>
                        Draft Lembur
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        {{ $draftLembur->count() }} Tersedia
                    </span>
                </header>
                <div class="p-4 bg-gray-50/30">
                    @forelse($draftLembur as $lm)
                        <label class="relative flex items-start p-4 cursor-pointer bg-white border border-gray-200 rounded-xl mb-3 hover:border-orange-400 hover:shadow-md transition-all duration-200 group">
                            <div class="flex items-center h-5 mt-1">
                                <input type="checkbox" name="lembur_ids[]" value="{{ $lm->id }}" class="form-checkbox text-orange-500 border-gray-300 focus:ring-orange-500 h-5 w-5 rounded transition-colors duration-200">
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-center mb-1">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-bold text-gray-900 group-hover:text-orange-700 transition-colors">{{ $lm->nomor_pranota }}</p>
                                        <a href="{{ route('pranota-lembur-karyawan.show', $lm->id) }}" target="_blank" onclick="event.stopPropagation()" class="text-orange-500 hover:text-orange-700 p-1 rounded hover:bg-orange-50 transition-colors" title="Lihat Isi Pranota">
                                            <i class="fas fa-external-link-alt text-[10px]"></i>
                                        </a>
                                    </div>
                                    <span class="text-xs font-medium text-gray-500">{{ $lm->tanggal_pranota->format('d M Y') }}</span>
                                </div>
                                <p class="text-sm font-semibold text-orange-600">Rp {{ number_format($lm->total_setelah_adjustment, 0, ',', '.') }}</p>
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-10 px-4 bg-white rounded-xl border border-dashed border-gray-300">
                            <div class="text-gray-300 mb-3"><i class="fas fa-inbox text-4xl"></i></div>
                            <p class="text-sm text-gray-500 font-medium mb-3">Tidak ada draf Lembur yang tersedia.</p>
                            <a href="{{ route('payroll.perhitungan-lembur') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-orange-700 bg-orange-50 hover:bg-orange-100 transition-colors">
                                <i class="fas fa-calculator mr-2"></i> Kalkulasi Sekarang
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
            <a href="{{ route('pranota-puml.index') }}" class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                Batal
            </a>
            <button type="submit" class="inline-flex justify-center items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-check-circle mr-2"></i> Generate PUML
            </button>
        </div>
    </form>

    {{-- ================================================================= --}}
    {{-- RIWAYAT PRANOTA --}}
    {{-- ================================================================= --}}
    <div class="mt-10">
        <div class="mb-5 pb-3 border-b border-gray-100 flex items-center space-x-3">
            <div class="bg-indigo-100 p-2 rounded-lg">
                <i class="fas fa-history text-indigo-600"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-800">Riwayat Pranota</h2>
                <p class="text-xs text-gray-500">Pranota Uang Makan dan Lembur yang sudah diproses / digabungkan ke dalam PUML.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Riwayat Uang Makan --}}
            <div class="bg-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] rounded-xl border border-gray-100 overflow-hidden">
                <header class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-white border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center text-emerald-800 font-bold text-sm">
                        <div class="bg-emerald-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-utensils text-emerald-600 text-xs"></i>
                        </div>
                        Riwayat Uang Makan
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        {{ $riwayatUangMakan->count() }} Dokumen
                    </span>
                </header>
                <div class="overflow-x-auto">
                    @if($riwayatUangMakan->isEmpty())
                        <div class="text-center py-10 px-4">
                            <div class="text-gray-300 mb-2"><i class="fas fa-inbox text-4xl"></i></div>
                            <p class="text-sm text-gray-400">Belum ada riwayat uang makan.</p>
                        </div>
                    @else
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Pranota</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($riwayatUangMakan as $um)
                                    <tr class="hover:bg-emerald-50/40 transition-colors">
                                        <td class="px-4 py-3">
                                            <a href="{{ route('pranota-uang-makan.show', $um->id) }}" target="_blank"
                                               class="font-semibold text-emerald-700 hover:text-emerald-900 hover:underline flex items-center gap-1">
                                                {{ $um->nomor_pranota }}
                                                <i class="fas fa-external-link-alt text-[9px] opacity-60"></i>
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $um->tanggal_pranota->format('d M Y') }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-700">Rp {{ number_format($um->total_nominal, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($um->status === 'submitted')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                    <i class="fas fa-paper-plane mr-1 text-[9px]"></i> Submitted
                                                </span>
                                            @elseif($um->status === 'approved')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                    <i class="fas fa-check mr-1 text-[9px]"></i> Approved
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    {{ ucfirst($um->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if(!in_array($um->status, ['approved', 'paid']))
                                                <form method="POST" action="{{ route('pranota-puml.detach-uang-makan', $um->id) }}"
                                                      onsubmit="return confirm('Lepaskan pranota ini dari PUML? Pranota akan kembali ke status draft dan bisa dipilih untuk PUML baru.')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-lg border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100 hover:border-amber-400 transition-colors">
                                                        <i class="fas fa-unlink text-[9px]"></i> Lepaskan
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-300 italic">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Riwayat Lembur --}}
            <div class="bg-white shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] rounded-xl border border-gray-100 overflow-hidden">
                <header class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center text-orange-800 font-bold text-sm">
                        <div class="bg-orange-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-clock text-orange-600 text-xs"></i>
                        </div>
                        Riwayat Lembur
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        {{ $riwayatLembur->count() }} Dokumen
                    </span>
                </header>
                <div class="overflow-x-auto">
                    @if($riwayatLembur->isEmpty())
                        <div class="text-center py-10 px-4">
                            <div class="text-gray-300 mb-2"><i class="fas fa-inbox text-4xl"></i></div>
                            <p class="text-sm text-gray-400">Belum ada riwayat lembur.</p>
                        </div>
                    @else
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Pranota</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($riwayatLembur as $lm)
                                    <tr class="hover:bg-orange-50/40 transition-colors">
                                        <td class="px-4 py-3">
                                            <a href="{{ route('pranota-lembur-karyawan.show', $lm->id) }}" target="_blank"
                                               class="font-semibold text-orange-700 hover:text-orange-900 hover:underline flex items-center gap-1">
                                                {{ $lm->nomor_pranota }}
                                                <i class="fas fa-external-link-alt text-[9px] opacity-60"></i>
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $lm->tanggal_pranota->format('d M Y') }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-700">Rp {{ number_format($lm->total_setelah_adjustment, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($lm->status === 'submitted')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                    <i class="fas fa-paper-plane mr-1 text-[9px]"></i> Submitted
                                                </span>
                                            @elseif($lm->status === 'approved')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                    <i class="fas fa-check mr-1 text-[9px]"></i> Approved
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    {{ ucfirst($lm->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if(!in_array($lm->status, ['approved', 'paid']))
                                                <form method="POST" action="{{ route('pranota-puml.detach-lembur', $lm->id) }}"
                                                      onsubmit="return confirm('Lepaskan pranota ini dari PUML? Pranota akan kembali ke status draft dan bisa dipilih untuk PUML baru.')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-lg border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100 hover:border-amber-400 transition-colors">
                                                        <i class="fas fa-unlink text-[9px]"></i> Lepaskan
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-300 italic">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
