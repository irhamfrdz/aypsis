{{-- Paper Size Selector Component --}}
{{-- Usage: @include('components.paper-selector', ['selectedSize' => $paperSize ?? 'Half-A4']) --}}

<div class="form-group">
    <label for="paper_size" class="form-label font-medium text-gray-700">Ukuran Kertas:</label>
    <select name="paper_size" id="paper_size" class="form-control mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none" style="border-color: #ccc !important;">
        <option value="A4" {{ ($selectedSize ?? 'Half-A4') === 'A4' ? 'selected' : '' }}>A4 (210 x 297 mm)</option>
        <option value="Custom-215" {{ ($selectedSize ?? 'Half-A4') === 'Custom-215' ? 'selected' : '' }}>Custom 21.5cm (215 x 297 mm)</option>
        <option value="Folio" {{ ($selectedSize ?? 'Half-A4') === 'Folio' ? 'selected' : '' }}>Folio (8.5 x 13 inch)</option>
        <option value="Half-A4" {{ ($selectedSize ?? 'Half-A4') === 'Half-A4' ? 'selected' : '' }}>1/2 A4 (210 x 148.5 mm)</option>
        <option value="Half-Custom-215" {{ ($selectedSize ?? 'Half-A4') === 'Half-Custom-215' ? 'selected' : '' }}>1/2 Custom 21.5cm (215 x 148.5 mm)</option>
        <option value="Half-Folio" {{ ($selectedSize ?? 'Half-A4') === 'Half-Folio' ? 'selected' : '' }}>1/2 Folio (8.5 x 6.5 inch)</option>
    </select>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paperSelect = document.getElementById('paper_size');

    if (paperSelect) {
        paperSelect.addEventListener('change', function() {
            const selectedSize = this.value;

            // Update form or URL parameter
            const form = this.closest('form');
            if (form) {
                // If in a form, submit or update hidden field
                let hiddenInput = form.querySelector('input[name="paper_size"]');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'paper_size';
                    form.appendChild(hiddenInput);
                }
                hiddenInput.value = selectedSize;
            }

            // Store in localStorage for persistence
            localStorage.setItem('preferred_paper_size', selectedSize);

            // Optional: Auto-refresh preview if on print page
            if (window.location.href.includes('/print')) {
                const url = new URL(window.location);
                url.searchParams.set('paper_size', selectedSize);
                window.location.href = url.toString();
            }
        });

        // Load saved preference
        const savedSize = localStorage.getItem('preferred_paper_size');
        if (savedSize && !paperSelect.value) {
            paperSelect.value = savedSize;
        }
    }
});
</script>
