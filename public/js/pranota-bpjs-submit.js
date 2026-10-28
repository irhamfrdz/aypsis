(function (root) {
    root.prepareBpjsSubmission = function (form, parseAmount) {
        const fields = Array.from(form.querySelectorAll('[name^="details["]')).filter(field => !field.disabled);
        const details = {};
        fields.forEach(field => {
            const match = field.name.match(/^details\[([^\]]+)\]\[([^\]]+)\]$/);
            if (!match) return;
            const [, index, key] = match;
            details[index] ??= {};
            details[index][key] = field.matches('input[type="text"]') ? parseAmount(field.value) : field.value;
        });
        let payload = form.querySelector('[name="details_json"]');
        if (!payload) {
            payload = document.createElement('input');
            payload.type = 'hidden';
            payload.name = 'details_json';
            form.appendChild(payload);
        }
        payload.value = JSON.stringify(Object.values(details));
        // Submit one field instead of thousands of PHP input variables.
        fields.forEach(field => field.removeAttribute('name'));
    };
})(globalThis);
