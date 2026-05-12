@extends('layouts.app')

@section('content')
<style>
    .report-card {
        animation: fadeInScale 0.5s ease-out forwards;
    }

    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.98) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .form-input-focus:focus {
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
        border-color: #7c3aed !important;
    }

    .btn-hover-effect {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-hover-effect:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }

    .btn-hover-effect:active {
        transform: translateY(0);
    }

    /* Custom date input styling */
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: invert(0.4);
        transition: all 0.2s;
    }

    input[type="date"]::-webkit-calendar-picker-indicator:hover {
        filter: invert(0.2) sepia(1) saturate(5) hue-rotate(240deg);
    }

    .bg-gradient-purple {
        background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%) !important;
    }

    .text-label {
        color: #374151 !important; /* text-gray-700 fallback */
    }

    .form-container {
        background: #ffffff;
        border-radius: 0 0 1rem 1rem;
    }
</style>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden report-card">
                <!-- Header -->
                <div class="bg-gradient-purple px-8 py-10 text-white relative">
                    <div class="relative z-10">
                        <h2 class="text-3xl font-bold mb-2">Report Tanda Terima Jakarta</h2>
                        <p class="text-purple-100 opacity-90">Aggregated report for all Tanda Terima types in Jakarta region.</p>
                    </div>
                    <div class="absolute right-0 top-0 mt-8 mr-8 opacity-10">
                        <svg class="w-32 h-32 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="p-8">
                    <form action="{{ route('report.tanda-terima-jakarta.view') }}" method="GET" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Start Date -->
                            <div class="space-y-2">
                                <label for="start_date" class="block text-sm font-semibold text-label">Start Date</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <input type="date" name="start_date" id="start_date" 
                                        value="{{ $startDate }}" 
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 text-sm transition-all duration-200 form-input-focus">
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="space-y-2">
                                <label for="end_date" class="block text-sm font-semibold text-label">End Date</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <input type="date" name="end_date" id="end_date" 
                                        value="{{ $endDate }}" 
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 text-sm transition-all duration-200 form-input-focus">
                                </div>
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        This report will include data from Standard Tanda Terima, Tanpa SJ, and LCL modules.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <button type="submit" class="flex-1 flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 shadow-md btn-hover-effect">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Report
                            </button>
                            
                            <button type="submit" name="export" value="true" formaction="{{ route('report.tanda-terima-jakarta.export') }}" class="flex-1 flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 shadow-sm btn-hover-effect">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export to Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
