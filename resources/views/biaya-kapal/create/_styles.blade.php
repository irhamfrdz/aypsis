{{-- Styles CSS --}}
@push('styles')
<style>
    /* Select2 Styling */
    .select2-container {
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        height: 42px !important;
        padding: 6px 12px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        padding-left: 0 !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
    }
    .select2-container--open .select2-selection--single {
        border-color: #3b82f6 !important;
    }
    .select2-results__option--highlighted {
        background-color: #3b82f6 !important;
    }

    /* Searchable Multi-Select Styling */
    #kapal_container, #voyage_container_input {
        transition: all 0.15s ease;
    }
    
    #kapal_container:focus-within, #voyage_container_input:focus-within {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .selected-chip {
        display: inline-flex;
        align-items: center;
        background-color: #3b82f6;
        color: white;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 4px;
        margin: 1px;
        gap: 6px;
    }
    
    .selected-chip .remove-chip {
        margin-left: 4px;
        cursor: pointer;
        font-weight: bold;
        font-size: 0.875rem;
        opacity: 0.8;
    }
    
    .selected-chip .remove-chip:hover {
        opacity: 1;
    }
    
    .kapal-option, .voyage-option, .bl-option {
        transition: background-color 0.15s ease;
        position: relative;
    }
    
    .kapal-option:hover, .voyage-option:hover, .bl-option:hover {
        background-color: #eff6ff !important;
    }
    
    .kapal-option.selected, .voyage-option.selected, .bl-option.selected {
        background-color: #dbeafe !important;
        border-left: 3px solid #3b82f6;
        padding-left: 9px;
    }
    
    .kapal-option.selected::after, .voyage-option.selected::after, .bl-option.selected::after, .jenis-biaya-option.selected::after {
        content: '\u2713';
        position: absolute;
        right: 12px;
        color: #3b82f6;
        font-weight: bold;
        font-size: 1rem;
    }
    
    .jenis-biaya-option {
        transition: background-color 0.15s ease;
        position: relative;
    }
    
    .jenis-biaya-option:hover {
        background-color: #eff6ff !important;
    }
    
    .jenis-biaya-option.selected {
        background-color: #dbeafe !important;
        border-left: 3px solid #3b82f6;
        padding-left: 9px;
    }
    
    #jenis_biaya_container {
        transition: all 0.15s ease;
    }
    
    #jenis_biaya_container:focus-within {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    #kapal_search::placeholder, #voyage_search::placeholder {
        color: #9ca3af;
    }
    
    #kapal_dropdown, #voyage_dropdown, .trucking-bl-dropdown {
        border-top: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .trucking-chip {
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .trucking-chip:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    
    .trucking-bl-option.selected {
        background-color: #dbeafe !important;
        border-left: 3px solid #3b82f6;
    }

    /* Stuffing Section Styling */
    .stuffing-section .bg-rose-500 {
        background-color: #f43f5e !important;
        border: 2px solid #e11d48 !important;
        box-shadow: 0 4px 6px -1px rgba(244, 63, 94, 0.3) !important;
        position: relative !important;
        z-index: 10 !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .stuffing-section .bg-rose-500:hover {
        background-color: #e11d48 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 8px -1px rgba(244, 63, 94, 0.4) !important;
    }

    .stuffing-section button[onclick*="addTandaTerimaToSection"] {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 160px !important;
        margin-left: auto !important;
        font-weight: 600 !important;
        animation: pulse 2s infinite !important;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
</style>

@endpush