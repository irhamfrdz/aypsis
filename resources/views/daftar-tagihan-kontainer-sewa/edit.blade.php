@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-4 lg:text-left">
            <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-1">
                Edit Tagihan Kontainer Sewa
            </h1>
            <p class="text-gray-600 text-sm">Perbarui data tagihan kontainer sewa</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 text-red-700 px-3 py-2 rounded-lg mb-4 shadow-sm">
                <div class="flex items-center mb-1">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium text-sm">Terdapat kesalahan dalam formulir:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <form action="{{ route('daftar-tagihan-kontainer-sewa.update', $item->id ?? 0) }}" method="POST" class="divide-y divide-gray-100">
                @csrf
                @method('PUT')

                @php
                    $inputClasses = "mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:ring-opacity-50 text-sm p-2.5 transition-all duration-200 min-h-[40px]";
                    $labelClasses = "block text-xs font-semibold text-gray-700 mb-1";
                    $currencyClasses = "mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:ring-opacity-50 text-sm p-2.5 transition-all duration-200 min-h-[40px] text-right font-mono";
                @endphp

                <!-- Informasi Dasar -->
                <div class="p-4 lg:p-5 space-y-4">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-base font-bold text-gray-800">Informasi Dasar</h3>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-4">
                        <div>
                            <label class="{{ $labelClasses }}">Vendor</label>
                            <input type="text" name="vendor" value="{{ old('vendor', $item->vendor ?? '') }}" class="{{ $inputClasses }}" placeholder="Nama vendor" />
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Nomor Kontainer</label>
                            <input type="text" name="nomor_kontainer" value="{{ old('nomor_kontainer', $item->nomor_kontainer ?? '') }}" class="{{ $inputClasses }}" placeholder="Nomor kontainer" />
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Size</label>
                            <input type="text" name="size" value="{{ old('size', $item->size ?? '') }}" class="{{ $inputClasses }}" placeholder="20ft, 40ft" />
                        </div>

                        <div>
                            <label class="{{ $labelClasses }}">Group</label>
                            <input type="text" name="group" value="{{ old('group', $item->group ?? '') }}" class="{{ $inputClasses }}" placeholder="Group kontainer" />
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Periode</label>
                            <input type="text" name="periode" value="{{ old('periode', $item->periode ?? '') }}" class="{{ $inputClasses }}" placeholder="Periode tagihan" />
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Masa</label>
                            <input type="text" name="masa" value="{{ old('masa', $item->masa ?? '') }}" class="{{ $inputClasses }}" placeholder="Masa sewa" />
                        </div>

                        <div>
                            <label class="{{ $labelClasses }}">Tanggal Mulai Sewa</label>
                            <input type="date" name="tanggal_awal" value="{{ old('tanggal_awal', isset($item->tanggal_awal) ? (is_string($item->tanggal_awal) ? $item->tanggal_awal : $item->tanggal_awal->format('Y-m-d')) : '') }}" class="{{ $inputClasses }}" />
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">Tanggal Selesai Sewa</label>
                            <input type="date" name="tanggal_akhir" value="{{ old('tanggal_akhir', isset($item->tanggal_akhir) ? (is_string($item->tanggal_akhir) ? $item->tanggal_akhir : $item->tanggal_akhir->format('Y-m-d')) : '') }}" class="{{ $inputClasses }}" />
                        </div>
                    </div>
                </div>

                <!-- Informasi Keuangan -->
                <div class="p-4 lg:p-5 space-y-4">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <h3 class="text-base font-bold text-gray-800">Informasi Keuangan</h3>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                        <div>
                            <label class="{{ $labelClasses }}">DPP</label>
                            <div class="relative">
                                <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium z-10 text-xs">Rp</span>
                                <input type="text"
                                       id="dpp_display"
                                       value="{{ old('dpp', number_format($item->dpp ?? 0, 2, ',', '.')) }}"
                                       class="{{ $currencyClasses }} pl-8 pr-12"
                                       placeholder="0,00"
                                       data-currency="dpp" />
                                <input type="hidden" name="dpp" id="dpp_hidden" value="{{ old('dpp', $item->dpp ?? 0) }}" />
                            </div>
                            <div class="mt-0.5 text-xs text-gray-400 text-right">
                                Original: {{ number_format($item->dpp ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">DPP Nilai Lain</label>
                            <div class="relative">
                                <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium z-10 text-xs">Rp</span>
                                <input type="text"
                                       id="dpp_nilai_lain_display"
                                       value="{{ old('dpp_nilai_lain', number_format($item->dpp_nilai_lain ?? 0, 2, ',', '.')) }}"
                                       class="{{ $currencyClasses }} pl-8 pr-12"
                                       placeholder="0,00"
                                       data-currency="dpp_nilai_lain" />
                                <input type="hidden" name="dpp_nilai_lain" id="dpp_nilai_lain_hidden" value="{{ old('dpp_nilai_lain', $item->dpp_nilai_lain ?? 0) }}" />
                            </div>
                            <div class="mt-0.5 text-xs text-gray-400 text-right">
                                Original: {{ number_format($item->dpp_nilai_lain ?? 0, 2, ',', '.') }}
                            </div>
                        </div>

                        <div>
                            <label class="{{ $labelClasses }}">PPN</label>
                            <div class="relative">
                                <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium z-10 text-xs">Rp</span>
                                <input type="text"
                                       id="ppn_display"
                                       value="{{ old('ppn', number_format($item->ppn ?? 0, 2, ',', '.')) }}"
                                       class="{{ $currencyClasses }} pl-8 pr-12"
                                       placeholder="0,00"
                                       data-currency="ppn" />
                                <input type="hidden" name="ppn" id="ppn_hidden" value="{{ old('ppn', $item->ppn ?? 0) }}" />
                            </div>
                            <div class="mt-0.5 text-xs text-gray-400 text-right">
                                Original: {{ number_format($item->ppn ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            <label class="{{ $labelClasses }}">PPH</label>
                            <div class="relative">
                                <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium z-10 text-xs">Rp</span>
                                <input type="text"
                                       id="pph_display"
                                       value="{{ old('pph', number_format($item->pph ?? 0, 2, ',', '.')) }}"
                                       class="{{ $currencyClasses }} pl-8 pr-12"
                                       placeholder="0,00"
                                       data-currency="pph" />
                                <input type="hidden" name="pph" id="pph_hidden" value="{{ old('pph', $item->pph ?? 0) }}" />
                            </div>
                            <div class="mt-0.5 text-xs text-gray-400 text-right">
                                Original: {{ number_format($item->pph ?? 0, 2, ',', '.') }}
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <label class="{{ $labelClasses }}">Grand Total</label>
                            <div class="relative">
                                <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold z-10 text-xs">Rp</span>
                                <input type="text"
                                       id="grand_total_display"
                                       value="{{ old('grand_total', number_format($item->grand_total ?? 0, 2, ',', '.')) }}"
                                       class="{{ $currencyClasses }} pl-8 pr-12 bg-yellow-50 border-yellow-200 font-bold"
                                       placeholder="0,00"
                                       data-currency="grand_total" />
                                <input type="hidden" name="grand_total" id="grand_total_hidden" value="{{ old('grand_total', $item->grand_total ?? 0) }}" />
                            </div>
                            <div class="mt-0.5 text-xs text-gray-400 text-right">
                                Original: {{ number_format($item->grand_total ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gray-50 px-4 py-4 lg:px-5 lg:py-4">
                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                        <a href="{{ route('daftar-tagihan-kontainer-sewa.index') }}"
                           class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border-2 border-gray-300 bg-white py-2 px-4 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 min-h-[40px]">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Batal
                        </a>

                        <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border-2 border-transparent bg-gradient-to-r from-green-600 to-emerald-600 py-2 px-4 text-sm font-semibold text-white shadow-lg hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 min-h-[40px]">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
console.log('=== SCRIPT LOADED ===');
console.log('Page:', 'Edit Tagihan Kontainer Sewa');

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM CONTENT LOADED ===');
    
    // Currency formatting function
    function formatCurrency(value) {
        // Remove non-numeric characters except decimal point
        let numericValue = value.toString().replace(/[^\d,.-]/g, '');

        // Convert comma to dot for calculation
        numericValue = numericValue.replace(',', '.');

        // Parse as float
        let number = parseFloat(numericValue) || 0;

        // Format with Indonesian locale (dot for thousands, comma for decimal)
        // Only show decimals if they exist and are not zero
        if (number % 1 === 0) {
            // Whole number - no decimals
            return number.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        } else {
            // Has decimals
            return number.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    // Function to get numeric value from formatted string
    function getNumericValue(formattedValue) {
        // Handle null, undefined, or empty values
        if (formattedValue === null || formattedValue === undefined || formattedValue === '') {
            return '0';
        }

        // Convert to string and trim whitespace
        let stringValue = formattedValue.toString().trim();

        // If empty after trim, return 0
        if (stringValue === '' || stringValue === '0' || stringValue === '0,00' || stringValue === '0.00') {
            return '0';
        }

        // Remove currency symbols and extra spaces
        stringValue = stringValue.replace(/Rp\s*/gi, '').trim();

        // Remove all non-numeric characters except dots, commas, and minus sign
        stringValue = stringValue.replace(/[^\d,.,-]/g, '');

        // Handle Indonesian format: dots are thousands separator, comma is decimal separator
        // Count dots and commas to determine format
        const dotCount = (stringValue.match(/\./g) || []).length;
        const commaCount = (stringValue.match(/,/g) || []).length;

        // Indonesian format: 1.234.567,89 (dots for thousands, comma for decimal)
        if (dotCount > 0 && commaCount <= 1) {
            // Remove dots (thousands separator)
            stringValue = stringValue.replace(/\./g, '');
            // Replace comma with dot (decimal separator)
            stringValue = stringValue.replace(',', '.');
        }
        // US format: 1,234,567.89 (commas for thousands, dot for decimal)
        else if (commaCount > 1 && dotCount <= 1) {
            // Remove commas (thousands separator)
            stringValue = stringValue.replace(/,/g, '');
            // Dot is already decimal separator
        }
        // Simple format with single comma (assume decimal)
        else if (commaCount === 1 && dotCount === 0) {
            stringValue = stringValue.replace(',', '.');
        }
        // Simple format with single dot (assume decimal)
        // No changes needed

        // Parse as float
        let number = parseFloat(stringValue);

        // If parsing failed, return 0
        if (isNaN(number) || !isFinite(number)) {
            console.warn('Failed to parse numeric value:', formattedValue, '-> returning 0');
            return '0';
        }

        // Return as string to avoid scientific notation
        return number.toString();
    }

    // Initialize currency formatting for all currency inputs
    const currencyInputs = document.querySelectorAll('[data-currency]');

    console.log('=== Initializing Currency Inputs ===');
    console.log('Found', currencyInputs.length, 'currency inputs');

    currencyInputs.forEach(input => {
        const fieldName = input.getAttribute('data-currency');
        const hiddenInput = document.getElementById(fieldName + '_hidden');
        
        console.log(`Currency field initialized: ${fieldName} = "${input.value}"`);
        
        if (!hiddenInput) {
            console.error(`Hidden input not found for ${fieldName}`);
            return;
        }

        let typingTimer;
        const doneTypingInterval = 1000; // 1 second

        // Update hidden input whenever display input changes
        function updateHiddenInput() {
            const displayValue = input.value.trim();
            
            if (displayValue === '' || displayValue === null || displayValue === undefined) {
                hiddenInput.value = '0';
                return;
            }
            
            // Convert formatted value to numeric
            let cleanValue = displayValue;
            cleanValue = cleanValue.replace(/Rp\s*/gi, '').trim();
            cleanValue = cleanValue.replace(/\./g, ''); // Remove thousands separator
            cleanValue = cleanValue.replace(/,/g, '.'); // Replace comma with dot
            
            const numericValue = parseFloat(cleanValue);
            hiddenInput.value = isNaN(numericValue) ? '0' : numericValue.toString();
            
            console.log(`Updated ${fieldName}: "${displayValue}" -> ${hiddenInput.value}`);
        }

        // Format on input - with delay to avoid interrupting typing
        input.addEventListener('input', function() {
            clearTimeout(typingTimer);

            typingTimer = setTimeout(() => {
                const cursorPosition = this.selectionStart;
                const oldValue = this.value;
                const newValue = formatCurrency(oldValue);

                if (newValue !== oldValue) {
                    this.value = newValue;
                    this.setSelectionRange(newValue.length, newValue.length);
                }
                
                updateHiddenInput();
            }, doneTypingInterval);
        });

        // Better focus behavior - double-click to select all
        input.addEventListener('focus', function() {
            // Don't auto-select on focus
        });

        // Double-click to select all for easy replacement
        input.addEventListener('dblclick', function() {
            this.select();
        });

        // Format on blur - only if value changed
        input.addEventListener('blur', function() {
            clearTimeout(typingTimer);

            const currentValue = this.value.trim();
            if (currentValue && currentValue !== '') {
                const formattedValue = formatCurrency(currentValue);
                this.value = formattedValue;
            }
            
            updateHiddenInput();
        });
        
        // Initialize hidden input value on page load
        updateHiddenInput();
    });

    // Form submission handler - just for logging and final check
    const form = document.querySelector('form');
    const submitButton = form.querySelector('button[type="submit"]');

    console.log('Form found:', form);
    console.log('Submit button found:', submitButton);

    if (!form) {
        console.error('CRITICAL: Form not found!');
        return;
    }

    form.addEventListener('submit', function(e) {
        console.log('\n\n=== FORM SUBMISSION STARTED ===');
        
        // Update all hidden inputs one last time before submit
        currencyInputs.forEach(input => {
            const fieldName = input.getAttribute('data-currency');
            const hiddenInput = document.getElementById(fieldName + '_hidden');
            
            if (!hiddenInput) return;
            
            const displayValue = input.value.trim();
            
            if (displayValue === '' || displayValue === null || displayValue === undefined) {
                hiddenInput.value = '0';
            } else {
                let cleanValue = displayValue;
                cleanValue = cleanValue.replace(/Rp\s*/gi, '').trim();
                cleanValue = cleanValue.replace(/\./g, '');
                cleanValue = cleanValue.replace(/,/g, '.');
                
                const numericValue = parseFloat(cleanValue);
                hiddenInput.value = isNaN(numericValue) ? '0' : numericValue.toString();
            }
            
            console.log(`Final ${fieldName}: display="${displayValue}" -> hidden="${hiddenInput.value}"`);
        });
        
        console.log('All hidden inputs updated. Form will submit normally.');
        
        // Show loading state
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            `;
        }
        
        // Let the form submit naturally
    });    // Auto-calculate Grand Total when other values change
    function calculateGrandTotal() {
        const dppHidden = document.getElementById('dpp_hidden');
        const dppNilaiLainHidden = document.getElementById('dpp_nilai_lain_hidden');
        const ppnHidden = document.getElementById('ppn_hidden');
        const pphHidden = document.getElementById('pph_hidden');
        const grandTotalDisplay = document.getElementById('grand_total_display');
        const grandTotalHidden = document.getElementById('grand_total_hidden');

        if (!dppHidden || !ppnHidden || !pphHidden || !grandTotalDisplay || !grandTotalHidden) return;

        const dpp = parseFloat(dppHidden.value) || 0;
        const ppn = parseFloat(ppnHidden.value) || 0;
        const pph = parseFloat(pphHidden.value) || 0;

        // Formula: DPP + PPN - PPH (tidak termasuk DPP Nilai Lain)
        const grandTotal = dpp + ppn - pph;

        grandTotalDisplay.value = formatCurrency(grandTotal);
        grandTotalHidden.value = grandTotal.toString();
        
        console.log(`Grand Total calculated: ${grandTotal}`);
    }

    // Add event listeners for auto-calculation on display inputs
    ['dpp', 'ppn', 'pph'].forEach(fieldName => {
        const displayInput = document.getElementById(fieldName + '_display');
        if (displayInput) {
            displayInput.addEventListener('input', calculateGrandTotal);
            displayInput.addEventListener('blur', calculateGrandTotal);
        }
    });

    // Add focus effects for better UX
    const allInputs = document.querySelectorAll('input, select, textarea');
    allInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('div')?.classList.add('ring-2', 'ring-blue-200');
        });

        input.addEventListener('blur', function() {
            this.closest('div')?.classList.remove('ring-2', 'ring-blue-200');
        });
    });
});
</script>

<style>
/* Mobile-friendly styles */
@media (max-width: 768px) {
    input, select, textarea {
        font-size: 14px !important;
        min-height: 40px !important;
    }

    .grid {
        gap: 0.75rem !important;
    }

    button, .btn {
        min-height: 40px !important;
        font-size: 14px !important;
    }
}

/* Enhanced focus states */
input:focus, select:focus, textarea:focus {
    transform: translateY(-0.5px);
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
}

/* Loading spinner animation */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Currency input styling */
[data-currency] {
    font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
    letter-spacing: 0.5px;
}

/* Better visual hierarchy */
.bg-gradient-to-br {
    background: linear-gradient(135deg, var(--tw-gradient-from), var(--tw-gradient-via), var(--tw-gradient-to));
}

.shadow-xl {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

button:hover, .btn:hover {
    transform: translateY(-2px);
}

/* Smooth transitions */
* {
    transition: all 0.2s ease;
}
</style>
@endpush
@endsection
