
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Message -->
    <div>
        <h2 class="text-2xl font-semibold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h2>
        <p class="text-gray-500">Berikut adalah ringkasan aktivitas sistem Anda.</p>
    </div>

    <!-- Data Prospek Berdasarkan Tujuan dan Ukuran Kontainer -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 flex items-center">
                <i class="fas fa-chart-bar mr-1 text-blue-600"></i>
                Data Prospek Berdasarkan Tujuan dan Ukuran Kontainer
            </h3>
            <p class="text-xs text-gray-600">Data prospek yang belum dimuat ke kapal</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            Tujuan
                        </th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-cube mr-1"></i>
                            20ft
                        </th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-cubes mr-1"></i>
                            40ft
                        </th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-truck mr-1"></i>
                            Cargo
                        </th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-calculator mr-1"></i>
                            Total
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $tujuanColors = [
                            'Jakarta' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-800', 'badge' => 'bg-blue-100'],
                            'Batam' => ['bg' => 'bg-green-50', 'text' => 'text-green-800', 'badge' => 'bg-green-100'],
                            'Pinang' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-800', 'badge' => 'bg-orange-100']
                        ];
                    @endphp
                    
                    @foreach($prospekData as $tujuan => $data)
                        @php
                            $colors = $tujuanColors[$tujuan] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-800', 'badge' => 'bg-gray-100'];
                            $total = $data['20ft'] + $data['40ft'] + $data['Cargo'];
                        @endphp
                        <tr class="hover:{{ $colors['bg'] }} transition-colors duration-150">
                            <td class="px-3 py-2 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $colors['badge'] }} {{ $colors['text'] }}">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $tujuan }}
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="text-lg font-bold text-gray-900">{{ $data['20ft'] }}</span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="text-lg font-bold text-gray-900">{{ $data['40ft'] }}</span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="text-lg font-bold text-gray-900">{{ $data['Cargo'] }}</span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="text-lg font-bold {{ $colors['text'] }}">{{ $total }}</span>
                            </td>
                        </tr>
                    @endforeach
                    
                    <!-- Total Row -->
                    <tr class="bg-gray-100 border-t-2 border-gray-300">
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-800 text-white">
                                <i class="fas fa-calculator mr-1"></i>
                                TOTAL
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <span class="text-lg font-bold text-purple-600">
                                {{ array_sum(array_column($prospekData, '20ft')) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <span class="text-lg font-bold text-indigo-600">
                                {{ array_sum(array_column($prospekData, '40ft')) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <span class="text-lg font-bold text-yellow-600">
                                {{ array_sum(array_column($prospekData, 'Cargo')) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <span class="text-xl font-bold text-gray-800">
                                {{ array_sum(array_column($prospekData, '20ft')) + array_sum(array_column($prospekData, '40ft')) + array_sum(array_column($prospekData, 'Cargo')) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dashboard Asset Asuransi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Asset Asuransi yang Sudah Lewat -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                <h3 class="text-base font-semibold text-red-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Asset Asuransi Lewat Jatuh Tempo
                </h3>
                <p class="text-xs text-red-600 mt-1">Total: {{ $assetsExpired->count() }} asset</p>
            </div>
            
            <div class="overflow-x-auto" style="max-height: 400px;">
                @if($assetsExpired->isEmpty())
                    <div class="p-6 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-medium">Tidak ada asset dengan asuransi lewat jatuh tempo</p>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode No</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Merek/Jenis</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($assetsExpired as $asset)
                                @php
                                    $tanggal = \Carbon\Carbon::parse($asset->tanggal_jatuh_tempo_asuransi);
                                    $today = \Carbon\Carbon::today();
                                    $diffDays = abs($today->diffInDays($tanggal, false));
                                    
                                    $bulanIndonesia = [
                                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                                        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                                        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                                    ];
                                    $formattedDate = $tanggal->format('d') . ' ' . $bulanIndonesia[$tanggal->month] . ' ' . $tanggal->format('Y');
                                @endphp
                                <tr class="hover:bg-red-50">
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="font-mono text-xs font-medium">{{ $asset->kode_no }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-xs">{{ $asset->merek ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $asset->jenis ?? '-' }}</div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="text-xs">{{ $formattedDate }}</div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            Lewat {{ $diffDays }} hari
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <!-- Asset Asuransi yang Akan Jatuh Tempo (1 Bulan) -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-yellow-50 px-4 py-3 border-b border-yellow-200">
                <h3 class="text-base font-semibold text-yellow-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Asset Asuransi Jatuh Tempo (1 Bulan)
                </h3>
                <p class="text-xs text-yellow-600 mt-1">Total: {{ $assetsExpiringSoon->count() }} asset</p>
            </div>
            
            <div class="overflow-x-auto" style="max-height: 400px;">
                @if($assetsExpiringSoon->isEmpty())
                    <div class="p-6 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-medium">Tidak ada asset yang akan jatuh tempo dalam 1 bulan</p>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode No</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Merek/Jenis</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($assetsExpiringSoon as $asset)
                                @php
                                    $tanggal = \Carbon\Carbon::parse($asset->tanggal_jatuh_tempo_asuransi);
                                    $today = \Carbon\Carbon::today();
                                    $diffDays = $today->diffInDays($tanggal, false);
                                    
                                    $bulanIndonesia = [
                                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                                        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                                        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                                    ];
                                    $formattedDate = $tanggal->format('d') . ' ' . $bulanIndonesia[$tanggal->month] . ' ' . $tanggal->format('Y');
                                @endphp
                                <tr class="hover:bg-yellow-50">
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="font-mono text-xs font-medium">{{ $asset->kode_no }}</span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-xs">{{ $asset->merek ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $asset->jenis ?? '-' }}</div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="text-xs">{{ $formattedDate }}</div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $diffDays }} hari lagi
                                        </span>
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
@endsection
