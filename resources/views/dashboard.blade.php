
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
</div>
@endsection
