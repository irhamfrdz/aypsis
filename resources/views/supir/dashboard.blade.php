<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Supir - AYPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Tugas Saya</h1>
            <div class="flex items-center space-x-4">
                <span class="hidden sm:block text-gray-600">Halo, {{ Auth::user()->name }}!</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="container mx-auto mt-8 px-4 sm:px-6 flex-grow">
        <div class="space-y-8">

            {{-- Section Permohonan --}}
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Memo Permohonan</h2>
                <div class="space-y-6">
                    @forelse($permohonans as $permohonan)
                        @php
                            $sudahCheckpoint = $permohonan->kontainers->isNotEmpty();
                        @endphp
                        <a href="{{ route('supir.checkpoint.create', $permohonan) }}"
                           class="block shadow-md rounded-lg p-6 transition duration-300
                           {{ $sudahCheckpoint ? 'bg-green-50 border border-green-400 hover:bg-green-100' : 'bg-white hover:bg-gray-50' }}">
                            <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                                <h3 class="text-lg font-semibold {{ $sudahCheckpoint ? 'text-green-700' : 'text-indigo-600' }}">{{ $permohonan->nomor_memo }}</h3>
                                <span class="mt-2 sm:mt-0 px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $sudahCheckpoint ? 'bg-green-200 text-green-800' : 'bg-blue-100 text-blue-800' }} self-start">
                                    {{ $permohonan->status }}
                                </span>
                            </div>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm border-t pt-4">
                                <div>
                                    <p class="font-medium text-gray-500">Tujuan</p>
                                    <p class="text-gray-800 font-semibold">{{ $permohonan->tujuan }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Kegiatan</p>
                                    <p class="text-gray-800 font-semibold">{{ $kegiatanMap[$permohonan->kegiatan] ?? ucfirst($permohonan->kegiatan) }}</p>
                                </div>
                            </div>
                            @if($sudahCheckpoint)
                                <div class="mt-4 text-green-700 text-xs font-semibold flex items-center gap-1">
                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Sudah input nomor kontainer
                                </div>
                            @endif
                        </a>
                    @empty
                        <div class="bg-white shadow-md rounded-lg p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Tugas Aktif</h3>
                            <p class="mt-1 text-sm text-gray-500">Saat ini tidak ada memo permohonan yang ditugaskan kepada Anda.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Section Surat Jalan --}}
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Surat Jalan</h2>
                <div class="space-y-6">
                    @forelse($suratJalans as $suratJalan)
                        @php
                            $needsCheckpoint = $suratJalan->status === 'belum masuk checkpoint';
                            $checkpointCompleted = $suratJalan->status === 'checkpoint_completed';
                        @endphp
                        <a href="{{ route('supir.checkpoint.create-surat-jalan', $suratJalan->id) }}"
                           class="block shadow-md rounded-lg p-6 transition duration-300
                           {{ $checkpointCompleted ? 'bg-green-50 border border-green-400 hover:bg-green-100' :
                              ($needsCheckpoint ? 'bg-yellow-50 border border-yellow-400 hover:bg-yellow-100' : 'bg-white hover:bg-gray-50') }}">
                            <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                                <h3 class="text-lg font-semibold {{ $checkpointCompleted ? 'text-green-700' : ($needsCheckpoint ? 'text-yellow-700' : 'text-indigo-600') }}">
                                    {{ $suratJalan->no_surat_jalan }}
                                </h3>
                                <span class="mt-2 sm:mt-0 px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $checkpointCompleted ? 'bg-green-200 text-green-800' :
                                       ($needsCheckpoint ? 'bg-yellow-200 text-yellow-800' : 'bg-blue-100 text-blue-800') }} self-start">
                                    {{ $suratJalan->status }}
                                </span>
                            </div>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm border-t pt-4">
                                <div>
                                    <p class="font-medium text-gray-500">Tanggal</p>
                                    <p class="text-gray-800 font-semibold">{{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Kegiatan</p>
                                    <p class="text-gray-800 font-semibold">{{ $kegiatanMap[$suratJalan->kegiatan] ?? ucfirst($suratJalan->kegiatan) }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Tujuan Pengambilan</p>
                                    <p class="text-gray-800 font-semibold">{{ $suratJalan->tujuan_pengambilan ?? $suratJalan->order->tujuan_ambil ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Tujuan Pengiriman</p>
                                    <p class="text-gray-800 font-semibold">{{ $suratJalan->tujuan_pengiriman ?? $suratJalan->order->tujuan_kirim ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Jumlah Kontainer</p>
                                    <p class="text-gray-800 font-semibold">{{ $suratJalan->jumlah_kontainer ?? 0 }}</p>
                                </div>
                            </div>
                            @if($needsCheckpoint)
                                <div class="mt-4 text-yellow-700 text-xs font-semibold flex items-center gap-1">
                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                                    Perlu input nomor kontainer
                                </div>
                            @elseif($checkpointCompleted)
                                <div class="mt-4 text-green-700 text-xs font-semibold flex items-center gap-1">
                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Checkpoint selesai - {{ $suratJalan->no_kontainer ?? 'Kontainer terinput' }}
                                </div>
                            @endif
                        </a>
                    @empty
                        <div class="bg-white shadow-md rounded-lg p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Surat Jalan</h3>
                            <p class="mt-1 text-sm text-gray-500">Saat ini tidak ada surat jalan yang ditugaskan kepada Anda.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</body>
</html>
