    // ============= PENERIMA SELECT2 INITIALIZATION =============
    // Load Select2 if jQuery is available
    if (typeof jQuery !== 'undefined') {
        // Check if Select2 is loaded
        function initPenerimaSelect2() {
            if (typeof jQuery.fn.select2 !== 'undefined') {
                jQuery('#penerima').select2({
                    placeholder: '-- Pilih atau ketik nama penerima --',
                    allowClear: true,
                    tags: true,
                    width: '100%'
                });
            } else {
                // Load Select2 CSS
                if (!document.querySelector('link[href*="select2"]')) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css';
                    document.head.appendChild(link);
                }
                
                // Load Select2 JS
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
                script.onload = function() {
                    jQuery('#penerima').select2({
                        placeholder: '-- Pilih atau ketik nama penerima --',
                        allowClear: true,
                        tags: true,
                        width: '100%'
                    });
                };
                document.head.appendChild(script);
            }
        }
        
        // Initialize on DOM ready
        jQuery(document).ready(function() {
            initPenerimaSelect2();
        });
    }
