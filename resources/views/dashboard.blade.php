
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

    <!-- Rekap Supir Tanpa Tanda Terima -->
    @if($rekapSupirBelumTandaTerima->count() > 0)
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mt-8">
        <div class="bg-indigo-50 px-4 py-3 border-b border-indigo-200 flex justify-between items-center">
            <div>
                <h3 class="text-base font-semibold text-indigo-800 flex items-center">
                    <i class="fas fa-users mr-1 text-indigo-600"></i>
                    Rekap Supir Tanpa Tanda Terima
                </h3>
                <p class="text-xs text-indigo-600">Total {{ $rekapSupirBelumTandaTerima->sum('total') }} surat jalan belum ada tanda terima (Status Pembayaran: Dibayar)</p>
            </div>
            @if(request('supir'))
                <a href="{{ route('dashboard', request()->except(['supir', 'page'])) }}" class="text-xs bg-white text-indigo-600 px-2 py-1 rounded border border-indigo-200 hover:bg-indigo-50 transition-colors">
                    Reset Filter
                </a>
            @endif
        </div>
        
        <div class="p-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($rekapSupirBelumTandaTerima as $data)
                @php
                    $isActive = request('supir') == $data->supir;
                    $cardClass = $isActive 
                        ? 'bg-indigo-100 border-indigo-500 ring-1 ring-indigo-500' 
                        : 'bg-gray-50 border-gray-200 hover:border-indigo-300 hover:shadow-sm';
                @endphp
                <a href="{{ route('dashboard', array_merge(request()->except('page'), ['supir' => $isActive ? null : $data->supir])) }}" 
                   class="{{ $cardClass }} rounded-lg p-3 border flex flex-col items-center justify-center text-center transition-all duration-200 cursor-pointer group">
                    <span class="text-xs {{ $isActive ? 'text-indigo-700' : 'text-gray-500 group-hover:text-indigo-600' }} font-medium uppercase tracking-wider mb-1">
                        {{ $data->supir ?: 'Tanpa Nama' }}
                    </span>
                    <span class="text-2xl font-bold {{ $isActive ? 'text-indigo-900' : 'text-gray-800 group-hover:text-indigo-800' }}">
                        {{ $data->total }}
                    </span>
                    <span class="text-[10px] {{ $isActive ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}">Surat Jalan</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Data Surat Jalan Tanpa Tanda Terima -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mt-8">
        <div class="bg-red-50 px-4 py-3 border-b border-red-200 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-base font-semibold text-red-800 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1 text-red-600"></i>
                    Surat Jalan Tanpa Tanda Terima
                </h3>
                <p class="text-xs text-red-600">Daftar surat jalan yang belum dibuatkan tanda terimanya (Sudah Bayar Uang Jalan)</p>
            </div>
            
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <!-- Preserve existing query params except per_page -->
                @foreach(request()->except('per_page', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                
                <label for="per_page" class="text-xs text-red-700 font-medium whitespace-nowrap">Tampilkan:</label>
                <select name="per_page" id="per_page" onchange="this.form.submit()" class="text-xs border-red-300 focus:border-red-500 focus:ring-red-500 rounded shadow-sm bg-white text-gray-700 py-1 pl-2 pr-6">
                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : (!request('per_page') ? 'selected' : '') }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No Surat Jalan</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Uang Jalan</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pengirim</th>
                         <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tujuan</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supir</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suratJalanBelumTandaTerima as $sj)
                        <tr class="hover:bg-red-50 transition-colors duration-150">
                            <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-900">
                                {{ $sj->no_surat_jalan }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-500">
                                {{ optional($sj->uangJalan)->tanggal_uang_jalan ? $sj->uangJalan->tanggal_uang_jalan->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-500">
                                {{ $sj->pengirimRelation->nama ?? $sj->pengirim }}
                            </td>
                             <td class="px-3 py-2 whitespace-nowrap text-gray-500">
                                {{ $sj->tujuanPengirimanRelation->nama ?? $sj->order->tujuan_kirim ?? $sj->tujuan_pengiriman }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-500">
                                {{ $sj->supir }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <a href="{{ route('surat-jalan.show', $sj->id) }}" class="text-blue-600 hover:text-blue-900 text-xs font-medium">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-gray-500 italic">
                                Semua surat jalan sudah memiliki tanda terima.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($suratJalanBelumTandaTerima->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $suratJalanBelumTandaTerima->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
