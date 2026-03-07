// ============= PENERIMA SELECT2 INITIALIZATION =============
jQuery(document).ready(function() {
    if (typeof jQuery.fn.select2 !== 'undefined') {
        jQuery('#penerima').select2({
            placeholder: '-- Pilih atau ketik nama penerima --',
            allowClear: true,
            tags: true,
            width: '100%'
        });
    }
});
