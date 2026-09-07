@extends('layouts.app')

@section('title', 'Dashboard Surat Jalan Bongkaran Batam')

@section('content')
<div class="flex-1 p-6 bg-gray-50/50 min-h-screen">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard Bongkaran Batam</h1>
            <nav class="flex text-sm text-gray-600 mt-2 font-medium">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-500">Bongkaran Batam</span>
            </nav>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('surat-jalan-bongkaran-batam.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-all duration-200">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                Lihat Data Tabel
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Cetak Report
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-8">
        <form method="GET" action="{{ route('surat-jalan-bongkaran-batam.dashboard') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Filter Kapal</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-ship text-gray-400"></i>
                    </div>
                    <input type="text" name="nama_kapal" value="{{ $selectedKapal }}" placeholder="Cari Nama Kapal..." class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-shadow">
                </div>
            </div>
            <div class="flex-1 w-full">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Filter Voyage</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-hashtag text-gray-400"></i>
                    </div>
                    <input type="text" name="no_voyage" value="{{ $selectedVoyage }}" placeholder="Cari No Voyage..." class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-shadow">
                </div>
            </div>
            <div class="w-full md:w-auto flex gap-2">
                <button type="submit" class="flex-1 md:flex-none inline-flex justify-center items-center px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg shadow-sm transition-all duration-200">
                    Terapkan Filter
                </button>
                @if($selectedKapal || $selectedVoyage)
                <a href="{{ route('surat-jalan-bongkaran-batam.dashboard') }}" class="inline-flex justify-center items-center px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg border border-red-100 transition-all duration-200" title="Reset Filter">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Dashboard Stats (Original 4 Cards, enhanced design) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Manifest -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition-shadow duration-300">
            <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="flex items-start">
                <div class="p-3.5 rounded-xl bg-blue-100 text-blue-600 z-10 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4 z-10">
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Manifest</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats->total_manifest, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between z-10 relative">
                <span class="text-xs text-gray-500 font-medium">Berdasarkan filter saat ini</span>
            </div>
        </div>

        <!-- Sudah Surat Jalan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition-shadow duration-300">
            <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="flex items-start">
                <div class="p-3.5 rounded-xl bg-emerald-100 text-emerald-600 z-10 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4 z-10">
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Sudah Surat Jalan</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats->sudah_sj, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50 z-10 relative">
                @php
                    $percentage = $stats->total_manifest > 0 ? round(($stats->sudah_sj / $stats->total_manifest) * 100, 1) : 0;
                @endphp
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-xs font-semibold text-gray-600">Progress</span>
                    <span class="text-xs font-bold text-emerald-600">{{ $percentage }}% Selesai</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- Belum Surat Jalan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition-shadow duration-300">
            <div class="absolute right-0 top-0 w-24 h-24 bg-orange-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="flex items-start">
                <div class="p-3.5 rounded-xl bg-orange-100 text-orange-600 z-10 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4 z-10">
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Belum Surat Jalan</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats->belum_sj, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between z-10 relative">
                <span class="text-xs text-orange-600 font-medium flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> Perlu diproses
                </span>
            </div>
        </div>

        <!-- Avg Leadtime -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition-shadow duration-300">
            <div class="absolute right-0 top-0 w-24 h-24 bg-purple-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="flex items-start">
                <div class="p-3.5 rounded-xl bg-purple-100 text-purple-600 z-10 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4 z-10">
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Avg Leadtime</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">
                        @if($stats->avg_leadtime_days > 0)
                            <span class="text-3xl">{{ $stats->avg_leadtime_days }}</span><span class="text-lg text-gray-500 mr-1">h</span>
                        @endif
                        <span class="text-3xl">{{ $stats->avg_leadtime_hours }}</span><span class="text-lg text-gray-500">j</span>
                    </h3>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between z-10 relative">
                <span class="text-xs text-gray-500 font-medium">Manifest ke Surat Jalan</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Tipe Kontainer Donut Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Proporsi Tipe Kontainer</h3>
            <div class="relative h-64 flex justify-center items-center">
                @if(count($chartTipeKontainer) > 0)
                    <canvas id="tipeKontainerChart"></canvas>
                @else
                    <div class="text-center text-gray-500">
                        <i class="fas fa-chart-pie text-4xl mb-3 text-gray-300"></i>
                        <p>Belum ada data untuk ditampilkan</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Status Penyelesaian SJ Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Status Penyelesaian Surat Jalan</h3>
            <div class="relative h-64 flex justify-center items-center">
                @if($stats->total_manifest > 0)
                    <canvas id="statusSJChart"></canvas>
                @else
                    <div class="text-center text-gray-500">
                        <i class="fas fa-chart-bar text-4xl mb-3 text-gray-300"></i>
                        <p>Belum ada data untuk ditampilkan</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tipe Kontainer Chart
        @if(count($chartTipeKontainer) > 0)
        const ctxTipe = document.getElementById('tipeKontainerChart').getContext('2d');
        const dataTipe = {
            labels: {!! json_encode($chartTipeKontainer->pluck('tipe_kontainer')->map(function($t) { return strtoupper($t ?: 'TIDAK DIKETAHUI'); })) !!},
            datasets: [{
                data: {!! json_encode($chartTipeKontainer->pluck('total')) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',  // Blue
                    'rgba(16, 185, 129, 0.8)',  // Emerald
                    'rgba(245, 158, 11, 0.8)',  // Amber
                    'rgba(139, 92, 246, 0.8)',  // Purple
                    'rgba(107, 114, 128, 0.8)'  // Gray
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        };
        new Chart(ctxTipe, {
            type: 'doughnut',
            data: dataTipe,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: "'Inter', sans-serif",
                                size: 12
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
        @endif

        // Status SJ Bar Chart
        @if($stats->total_manifest > 0)
        const ctxStatus = document.getElementById('statusSJChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'bar',
            data: {
                labels: ['Sudah SJ', 'Belum SJ'],
                datasets: [{
                    label: 'Jumlah Manifest',
                    data: [{{ $stats->sudah_sj }}, {{ $stats->belum_sj }}],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)', // Emerald
                        'rgba(249, 115, 22, 0.8)'  // Orange
                    ],
                    borderRadius: 6,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(243, 244, 246, 1)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: "'Inter', sans-serif" }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: "'Inter', sans-serif", weight: '600' }
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
@endpush
