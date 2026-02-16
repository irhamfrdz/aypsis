<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Cek Kendaraan - AYPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .gradient-bg { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); }
    </style>
</head>
<body class="min-h-screen">
    <div class="gradient-bg h-48 w-full absolute top-0 left-0 z-0"></div>
    
    <div class="relative z-10 container mx-auto px-4 pt-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('supir.dashboard') }}" class="p-2 bg-white/20 hover:bg-white/30 rounded-full transition-all backdrop-blur-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-2xl font-bold text-white">Riwayat Pengecekan</h1>
            </div>
            <a href="{{ route('supir.cek-kendaraan.create') }}" class="px-4 py-2 bg-white text-indigo-600 font-bold rounded-lg shadow-lg hover:shadow-xl transition-all">
                Cek Baru
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 glass-card bg-green-50/80 border-green-200 text-green-700 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">Tanggal & Jam</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">Kendaraan</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">Odometer</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($history as $row)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $row->tanggal->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $row->jam }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-indigo-600">{{ $row->mobil->nomor_polisi }}</div>
                                <div class="text-xs text-gray-500">{{ $row->mobil->merek }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ number_format($row->odometer, 0, ',', '.') }} KM
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-700 uppercase">
                                    Tersimpan
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('supir.cek-kendaraan.show', $row->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                Belum ada riwayat pengecekan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($history->hasPages())
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                    {{ $history->links() }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
